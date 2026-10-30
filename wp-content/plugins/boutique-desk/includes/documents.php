<?php
if (!defined('ABSPATH')) exit;

/*
 * Printable GST invoices and packing slips. Each opens as a clean A4 page and the
 * browser's print dialog saves it as PDF, so no PDF library is needed.
 */

/* Sequential invoice number, assigned the first time an invoice is printed. */
function bdesk_invoice_number($order) {
    $no = $order->get_meta('_bdesk_invoice_no');
    if ($no) return $no;
    $s = bdesk_settings();
    $n = (int) $s['next_number'];
    $s['next_number'] = $n + 1;
    update_option('bdesk_settings', $s);
    $no = $s['prefix'] . str_pad((string) $n, 4, '0', STR_PAD_LEFT);
    $order->update_meta_data('_bdesk_invoice_no', $no);
    $order->update_meta_data('_bdesk_invoice_date', time());
    $order->save();
    return $no;
}

/* 12345.5 -> "Twelve Thousand Three Hundred Forty Five Rupees and Fifty Paise Only" (Indian grouping). */
function bdesk_words($amount) {
    $ones = array('', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine', 'Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen');
    $tens = array('', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety');
    $two = function ($n) use ($ones, $tens) { return $n < 20 ? $ones[$n] : trim($tens[intdiv($n, 10)] . ' ' . $ones[$n % 10]); };
    $three = function ($n) use ($two, $ones) { return trim(($n >= 100 ? $ones[intdiv($n, 100)] . ' Hundred ' : '') . $two($n % 100)); };
    $rupees = (int) floor($amount);
    $paise = (int) round(($amount - $rupees) * 100);
    if ($paise === 100) { $rupees++; $paise = 0; }
    $parts = array();
    foreach (array(10000000 => 'Crore', 100000 => 'Lakh', 1000 => 'Thousand') as $unit => $label) {
        if ($rupees >= $unit) { $parts[] = ($unit === 10000000 ? bdesk_words_int(intdiv($rupees, $unit), $three) : $two(intdiv($rupees, $unit))) . ' ' . $label; $rupees %= $unit; }
    }
    if ($rupees) $parts[] = $three($rupees);
    $text = $parts ? implode(' ', $parts) . ' Rupees' : 'Zero Rupees';
    if ($paise) $text .= ' and ' . $two($paise) . ' Paise';
    return $text . ' Only';
}
function bdesk_words_int($n, $three) {
    return $n < 1000 ? $three($n) : $three(intdiv($n, 1000)) . ' Thousand ' . $three($n % 1000);
}

/* Size, colour and other choices on a line item, plus fabric from the product. */
function bdesk_item_details($item, $product) {
    $bits = array();
    foreach ($item->get_formatted_meta_data('') as $meta) {
        $bits[] = wp_strip_all_tags($meta->display_key) . ': ' . wp_strip_all_tags($meta->display_value);
    }
    if ($product) {
        foreach (array('fabric', 'pa_fabric', 'stitching', 'pa_stitching', 'pieces', 'pa_pieces') as $att) {
            $v = $product->get_attribute($att);
            if ($v) $bits[] = wc_attribute_label($att, $product) . ': ' . $v;
        }
    }
    return array_values(array_unique($bits));
}

function bdesk_item_hsn($product) {
    $s = bdesk_settings();
    if (!$product) return $s['hsn'];
    $hsn = $product->get_meta('_bdesk_hsn');
    if (!$hsn && $product->get_parent_id()) { $parent = wc_get_product($product->get_parent_id()); if ($parent) $hsn = $parent->get_meta('_bdesk_hsn'); }
    return $hsn ?: $s['hsn'];
}

function bdesk_can_view($order) {
    if (current_user_can('edit_shop_orders')) return true;
    return is_user_logged_in() && (int) $order->get_customer_id() === get_current_user_id() && $order->has_status(array('processing', 'completed', 'on-hold'));
}

add_action('admin_post_bdesk_doc', 'bdesk_render_request');
function bdesk_render_request() {
    check_admin_referer('bdesk_doc');
    $type = (isset($_GET['type']) && $_GET['type'] === 'slip') ? 'slip' : 'invoice';
    $ids = isset($_GET['ids']) ? array_slice(array_filter(array_map('absint', explode(',', sanitize_text_field(wp_unslash($_GET['ids']))))), 0, 100) : array();
    $orders = array();
    foreach ($ids as $id) { $o = wc_get_order($id); if ($o && bdesk_can_view($o)) $orders[] = $o; }
    if (!$orders) wp_die(esc_html__('No orders to print, or you do not have access to them.', 'boutique-desk'));
    bdesk_render_documents($orders, $type);
    exit;
}

function bdesk_address($order, $which) {
    $addr = $which === 'shipping' && $order->has_shipping_address() ? $order->get_formatted_shipping_address() : $order->get_formatted_billing_address();
    return wp_kses($addr, array('br' => array()));
}

function bdesk_render_documents($orders, $type) {
    $s = bdesk_settings();
    $title = $type === 'slip' ? 'Packing slip' : 'Tax invoice';
    nocache_headers();
    ?><!doctype html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?php echo esc_html($title . (count($orders) === 1 ? ' #' . $orders[0]->get_order_number() : 's')); ?></title>
<style>
@page{size:A4;margin:12mm}
*{box-sizing:border-box}
body{margin:0;background:#eeeae4;font:13px/1.5 -apple-system,'Segoe UI',Roboto,Arial,sans-serif;color:#1d1a17}
.bar{position:sticky;top:0;display:flex;gap:10px;justify-content:center;padding:12px;background:#1d1a17}
.bar button{font:600 14px sans-serif;padding:10px 22px;border:0;background:#8c1d3a;color:#fff;cursor:pointer;border-radius:3px}
.bar button.alt{background:#fff;color:#1d1a17}
.doc{width:210mm;max-width:100%;min-height:270mm;margin:18px auto;padding:14mm;background:#fff;box-shadow:0 2px 14px rgba(0,0,0,.08)}
.top{display:flex;justify-content:space-between;gap:24px;border-bottom:2px solid #1d1a17;padding-bottom:14px}
.brand img{max-height:56px;max-width:200px;display:block;margin-bottom:6px}
.brand strong{font:600 22px Georgia,serif;letter-spacing:.06em;display:block}
.brand p,.meta p{margin:2px 0;color:#5a524b;font-size:12px}
.meta{text-align:right}
.meta h1{margin:0 0 6px;font:600 20px Georgia,serif;text-transform:uppercase;letter-spacing:.08em}
.meta b{color:#1d1a17}
.parties{display:grid;grid-template-columns:1fr 1fr;gap:24px;margin:18px 0}
.parties h3{margin:0 0 4px;font-size:11px;text-transform:uppercase;letter-spacing:.1em;color:#8a8076}
.parties p{margin:0}
table{width:100%;border-collapse:collapse}
th{background:#f5f1ec;font-size:11px;text-transform:uppercase;letter-spacing:.06em;text-align:left;padding:8px;border-bottom:1px solid #d9d1c7}
td{padding:9px 8px;border-bottom:1px solid #ece5dc;vertical-align:top}
.num{text-align:right;white-space:nowrap}
.item-name{font-weight:600}
.item-meta{color:#6b625a;font-size:12px}
.thumb{width:54px;height:66px;object-fit:cover;background:#f5f1ec;display:block}
.totals{margin-left:auto;width:320px;margin-top:10px}
.totals td{border:0;padding:5px 8px}
.totals tr.grand td{border-top:2px solid #1d1a17;font-weight:700;font-size:15px;padding-top:9px}
.words{margin:12px 0;padding:10px 12px;background:#f5f1ec;font-size:12px}
.cod{margin:16px 0;padding:14px 16px;border:2px dashed #8c1d3a;font-size:16px;font-weight:700;color:#8c1d3a;text-align:center}
.paid{margin:16px 0;padding:10px 16px;background:#eef6ef;color:#1e5b2c;font-weight:600;text-align:center}
.check{width:22px;height:22px;border:1.5px solid #1d1a17;display:inline-block}
.note{margin-top:22px;padding-top:12px;border-top:1px solid #ece5dc;color:#5a524b;font-size:12px;white-space:pre-line}
.sign{margin-top:36px;text-align:right;font-size:12px}
.ship-to{border:2px solid #1d1a17;padding:14px 16px;font-size:15px;line-height:1.55}
.ship-to h3{color:#1d1a17}
@media print{body{background:#fff}.bar{display:none}.doc{margin:0;box-shadow:none;padding:0;width:auto;min-height:0;page-break-after:always}.doc:last-child{page-break-after:auto}}
</style></head><body>
<div class="bar"><button onclick="window.print()">Print / Save as PDF</button><button class="alt" onclick="window.close()">Close</button></div>
<?php foreach ($orders as $order) { $type === 'slip' ? bdesk_slip($order, $s) : bdesk_invoice($order, $s); } ?>
<script>window.addEventListener('load',function(){setTimeout(function(){window.print();},350);});</script>
</body></html><?php
}

function bdesk_doc_header($order, $s, $heading, $lines) {
    ?>
    <div class="top">
        <div class="brand">
            <?php if ($s['logo']): ?><img src="<?php echo esc_url($s['logo']); ?>" alt=""><?php endif; ?>
            <strong><?php echo esc_html($s['store_name']); ?></strong>
            <?php if ($s['address']): ?><p><?php echo nl2br(esc_html($s['address'])); ?></p><?php endif; ?>
            <p><?php echo esc_html(implode(' · ', array_filter(array($s['phone'], $s['email'])))); ?></p>
            <?php if ($s['gstin']): ?><p><b>GSTIN:</b> <?php echo esc_html($s['gstin']); ?></p><?php endif; ?>
        </div>
        <div class="meta"><h1><?php echo esc_html($heading); ?></h1><?php foreach ($lines as $k => $v): ?><p><?php echo esc_html($k); ?>: <b><?php echo esc_html($v); ?></b></p><?php endforeach; ?></div>
    </div>
    <?php
}

function bdesk_invoice($order, $s) {
    $no = bdesk_invoice_number($order);
    $date = $order->get_meta('_bdesk_invoice_date') ?: time();
    $state_code = $order->get_shipping_state() ?: $order->get_billing_state();
    $country = $order->get_shipping_country() ?: $order->get_billing_country();
    $states = WC()->countries->get_states($country);
    $place = $states && isset($states[$state_code]) ? $states[$state_code] : $state_code;
    echo '<div class="doc">';
    bdesk_doc_header($order, $s, 'Tax invoice', array(
        'Invoice no.' => $no,
        'Invoice date' => wp_date('j M Y', (int) $date),
        'Order no.' => '#' . $order->get_order_number(),
        'Order date' => wc_format_datetime($order->get_date_created(), 'j M Y'),
        'Payment' => $order->get_payment_method_title() ?: '—',
    ));
    ?>
    <div class="parties">
        <div><h3>Billed to</h3><p><?php echo bdesk_address($order, 'billing'); ?></p><p><?php echo esc_html($order->get_billing_phone()); ?></p><p><?php echo esc_html($order->get_billing_email()); ?></p></div>
        <div><h3>Shipped to</h3><p><?php echo bdesk_address($order, 'shipping'); ?></p><?php if ($place): ?><p><b>Place of supply:</b> <?php echo esc_html($place); ?></p><?php endif; ?></div>
    </div>
    <table>
        <thead><tr><th>#</th><th>Item</th><th>HSN</th><th class="num">Qty</th><th class="num">Rate</th><th class="num">Amount</th></tr></thead>
        <tbody>
        <?php $i = 0; foreach ($order->get_items() as $item): $i++; $product = $item->get_product(); $qty = max(1, $item->get_quantity()); ?>
            <tr>
                <td><?php echo esc_html($i); ?></td>
                <td><div class="item-name"><?php echo esc_html($item->get_name()); ?></div>
                    <div class="item-meta"><?php echo esc_html(implode(' · ', array_filter(array_merge($product && $product->get_sku() ? array('SKU: ' . $product->get_sku()) : array(), bdesk_item_details($item, $product))))); ?></div></td>
                <td><?php echo esc_html(bdesk_item_hsn($product)); ?></td>
                <td class="num"><?php echo esc_html($qty); ?></td>
                <td class="num"><?php echo wp_kses_post(bdesk_money($item->get_subtotal() / $qty, $order)); ?></td>
                <td class="num"><?php echo wp_kses_post(bdesk_money($item->get_subtotal(), $order)); ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <table class="totals">
        <tr><td>Subtotal</td><td class="num"><?php echo wp_kses_post(bdesk_money($order->get_subtotal(), $order)); ?></td></tr>
        <?php if ((float) $order->get_discount_total() > 0): ?><tr><td>Discount</td><td class="num">−<?php echo wp_kses_post(bdesk_money($order->get_discount_total(), $order)); ?></td></tr><?php endif; ?>
        <tr><td>Shipping</td><td class="num"><?php echo (float) $order->get_shipping_total() > 0 ? wp_kses_post(bdesk_money($order->get_shipping_total(), $order)) : 'Free'; ?></td></tr>
        <?php foreach ($order->get_fees() as $fee): ?><tr><td><?php echo esc_html($fee->get_name()); ?></td><td class="num"><?php echo wp_kses_post(bdesk_money($fee->get_total(), $order)); ?></td></tr><?php endforeach; ?>
        <?php foreach ($order->get_tax_totals() as $tax): ?><tr><td><?php echo esc_html($tax->label); ?></td><td class="num"><?php echo wp_kses_post($tax->formatted_amount); ?></td></tr><?php endforeach; ?>
        <?php if ((float) $order->get_total_refunded() > 0): ?><tr><td>Refunded</td><td class="num">−<?php echo wp_kses_post(bdesk_money($order->get_total_refunded(), $order)); ?></td></tr><?php endif; ?>
        <tr class="grand"><td>Total</td><td class="num"><?php echo wp_kses_post(bdesk_money($order->get_total() - $order->get_total_refunded(), $order)); ?></td></tr>
    </table>
    <div class="words"><b>Amount in words:</b> <?php echo esc_html(bdesk_words($order->get_total() - $order->get_total_refunded())); ?><?php if (!$order->get_tax_totals() && $s['tax_inclusive']): ?><br>Prices are inclusive of GST.<?php endif; ?></div>
    <?php if ($s['invoice_note']): ?><div class="note"><?php echo esc_html($s['invoice_note']); ?></div><?php endif; ?>
    <div class="sign">For <?php echo esc_html($s['store_name']); ?><br><br>Authorised signatory<br><small>This is a computer-generated invoice.</small></div>
    </div>
    <?php
}

function bdesk_slip($order, $s) {
    $cod = $order->get_payment_method() === 'cod' && !$order->is_paid();
    $count = 0;
    foreach ($order->get_items() as $it) $count += $it->get_quantity();
    echo '<div class="doc">';
    bdesk_doc_header($order, $s, 'Packing slip', array(
        'Order no.' => '#' . $order->get_order_number(),
        'Order date' => wc_format_datetime($order->get_date_created(), 'j M Y'),
        'Pieces' => (string) $count,
        'Shipping' => $order->get_shipping_method() ?: '—',
    ));
    ?>
    <div class="parties">
        <div class="ship-to"><h3>Ship to</h3><p><?php echo bdesk_address($order, 'shipping'); ?></p><p><b><?php echo esc_html($order->get_shipping_phone() ?: $order->get_billing_phone()); ?></b></p></div>
        <div><h3>Return address</h3><p><?php echo nl2br(esc_html($s['store_name'] . "\n" . $s['address'])); ?></p><?php if ($order->get_customer_note()): ?><h3 style="margin-top:12px">Customer note</h3><p><?php echo esc_html($order->get_customer_note()); ?></p><?php endif; ?></div>
    </div>
    <?php if ($cod): ?><div class="cod">Cash on delivery: collect <?php echo esc_html(html_entity_decode(wp_strip_all_tags(bdesk_money($order->get_total(), $order)))); ?></div>
    <?php else: ?><div class="paid">Prepaid order: nothing to collect</div><?php endif; ?>
    <table>
        <thead><tr><th></th><th>Item</th><th>Size / details</th><th class="num">Qty</th><th>Packed</th></tr></thead>
        <tbody>
        <?php foreach ($order->get_items() as $item): $product = $item->get_product(); $img = $product ? wp_get_attachment_image_url($product->get_image_id() ?: ($product->get_parent_id() ? get_post_thumbnail_id($product->get_parent_id()) : 0), 'thumbnail') : ''; ?>
            <tr>
                <td><?php if ($img): ?><img class="thumb" src="<?php echo esc_url($img); ?>" alt=""><?php endif; ?></td>
                <td><div class="item-name"><?php echo esc_html($item->get_name()); ?></div><div class="item-meta"><?php echo esc_html($product && $product->get_sku() ? 'SKU: ' . $product->get_sku() : ''); ?></div></td>
                <td class="item-meta"><?php echo esc_html(implode(' · ', bdesk_item_details($item, $product)) ?: '—'); ?></td>
                <td class="num"><b><?php echo esc_html($item->get_quantity()); ?></b></td>
                <td><span class="check"></span></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <p class="item-meta" style="margin-top:14px">Before sealing: check each piece for loose threads and stains, include the dupatta and trouser, fold embroidery inwards with tissue.</p>
    <?php if ($s['slip_note']): ?><div class="note"><?php echo esc_html($s['slip_note']); ?></div><?php endif; ?>
    </div>
    <?php
}

/* Buttons on the order screen. */
add_action('add_meta_boxes', function () {
    foreach (bdesk_order_screens() as $screen) {
        add_meta_box('bdesk-docs', __('Invoice & packing slip', 'boutique-desk'), 'bdesk_order_box', $screen, 'side', 'high');
    }
});
function bdesk_order_box($post_or_order) {
    $order = $post_or_order instanceof WC_Order ? $post_or_order : wc_get_order($post_or_order->ID);
    if (!$order) return;
    $no = $order->get_meta('_bdesk_invoice_no');
    echo '<p><a class="button" target="_blank" href="' . esc_url(bdesk_doc_url($order->get_id(), 'invoice')) . '">Print invoice</a> <a class="button" target="_blank" href="' . esc_url(bdesk_doc_url($order->get_id(), 'slip')) . '">Packing slip</a></p>';
    if ($no) echo '<p class="description">Invoice ' . esc_html($no) . '</p>';
}

/* Bulk print from the orders list (both order storage modes). */
foreach (array('bulk_actions-edit-shop_order', 'bulk_actions-woocommerce_page_wc-orders') as $f_hook) {
    add_filter($f_hook, function ($actions) {
        $actions['bdesk_invoice'] = __('Print invoices', 'boutique-desk');
        $actions['bdesk_slip'] = __('Print packing slips', 'boutique-desk');
        return $actions;
    });
}
foreach (array('handle_bulk_actions-edit-shop_order', 'handle_bulk_actions-woocommerce_page_wc-orders') as $f_hook) {
    add_filter($f_hook, function ($redirect, $action, $ids) {
        if (!in_array($action, array('bdesk_invoice', 'bdesk_slip'), true)) return $redirect;
        return bdesk_doc_url($ids, $action === 'bdesk_slip' ? 'slip' : 'invoice');
    }, 10, 3);
}

/* Customers can download their own invoice from My account. */
add_filter('woocommerce_my_account_my_orders_actions', function ($actions, $order) {
    if ($order->has_status(array('processing', 'completed'))) {
        $actions['bdesk_invoice'] = array('url' => bdesk_doc_url($order->get_id(), 'invoice'), 'name' => __('Invoice', 'boutique-desk'));
    }
    return $actions;
}, 10, 2);
