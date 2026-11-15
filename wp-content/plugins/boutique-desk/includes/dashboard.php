<?php
if (!defined('ABSPATH')) exit;

/* Store Desk: what needs doing right now, step by step, plus today's numbers. */
add_action('admin_menu', function () {
    $label = __('Store Desk', 'boutique-desk');
    $waiting = function_exists('wc_orders_count') ? (int) wc_orders_count('processing') : 0;
    $bubble = $waiting ? ' <span class="awaiting-mod">' . number_format_i18n($waiting) . '</span>' : '';
    add_menu_page($label, $label . $bubble, 'edit_shop_orders', 'bdesk', 'bdesk_dashboard_page', 'dashicons-store', 2);
});

function bdesk_paid_statuses() {
    return array('processing', 'completed', 'on-hold');
}

/* Orders created in [from, to), as objects. */
function bdesk_orders_between($from, $to, $statuses = null) {
    return wc_get_orders(array(
        'limit' => -1,
        'type' => 'shop_order',
        'status' => $statuses ?: bdesk_paid_statuses(),
        'date_created' => $from . '...' . ($to - 1),
    ));
}

function bdesk_sum($orders) {
    $total = 0;
    foreach ($orders as $o) $total += (float) $o->get_total() - (float) $o->get_total_refunded();
    return $total;
}

function bdesk_doc_url($ids, $type) {
    return wp_nonce_url(admin_url('admin-post.php?action=bdesk_doc&type=' . $type . '&ids=' . implode(',', array_map('absint', (array) $ids))), 'bdesk_doc');
}

function bdesk_tab_url($tab) {
    return admin_url('admin.php?page=bdesk&tab=' . $tab);
}

/* Open orders from the same phone number or email: usually one customer ordering twice by mistake. */
function bdesk_duplicates($orders) {
    $seen = array();
    foreach ($orders as $o) {
        $keys = array_filter(array(substr(preg_replace('/\D+/', '', (string) $o->get_billing_phone()), -9), strtolower((string) $o->get_billing_email())));
        foreach ($keys as $k) $seen[$k][$o->get_id()] = $o->get_order_number();
    }
    $dupes = array();
    foreach ($seen as $group) {
        if (count($group) < 2) continue;
        foreach ($group as $id => $number) {
            foreach ($group as $other_id => $other_number) {
                if ($other_id !== $id) $dupes[$id][$other_id] = $other_number;
            }
        }
    }
    return $dupes;
}

/* How long an order may sit at a step before the desk marks it late (hours). */
function bdesk_late_after($stage) {
    $hours = array('new' => 24, 'confirmed' => 24, 'packed' => 24, 'shipped' => 168, 'returned' => 48);
    return (int) apply_filters('bdesk_late_after', isset($hours[$stage]) ? $hours[$stage] : 48, $stage);
}

function bdesk_stage_since($order, $stage) {
    $at = (int) $order->get_meta('_bdesk_' . $stage . '_at');
    return $at ?: ($order->get_date_created() ? $order->get_date_created()->getTimestamp() : time());
}

function bdesk_step_button($do, $label, $class = '', $confirm = '') {
    return '<button type="submit" name="do" value="' . esc_attr($do) . '" class="button button-small ' . esc_attr($class) . '"' . ($confirm ? ' onclick="return confirm(\'' . esc_js($confirm) . '\')"' : '') . '>' . esc_html($label) . '</button>';
}

/* One order row, with the buttons that fit its step. */
function bdesk_order_row($o, $stage, $dupes) {
    $cod = bdesk_is_cod($o);
    $phone = $o->get_billing_phone() ?: $o->get_shipping_phone();
    $since = bdesk_stage_since($o, $stage);
    $late = (time() - $since) > bdesk_late_after($stage) * HOUR_IN_SECONDS;
    $place = array_filter(array(trim((string) ($o->get_shipping_city() ?: $o->get_billing_city())), WC()->countries->countries[$o->get_shipping_country() ?: $o->get_billing_country()] ?? ''));
    $tries = (int) $o->get_meta('_bdesk_attempts');
    $wa = bdesk_wa_url($o, $stage === 'shipped' ? 'shipped' : 'confirm');
    $serials = (string) $o->get_meta('_bdesk_serials');
    ?>
    <tr class="<?php echo $late ? 'is-late' : ''; ?>">
        <td data-label="Order"><a href="<?php echo esc_url($o->get_edit_order_url()); ?>"><strong>#<?php echo esc_html($o->get_order_number()); ?></strong></a>
            <br><small class="bdesk-age" title="<?php echo esc_attr(wc_format_datetime($o->get_date_created(), 'j M Y, g:i a')); ?>"><?php echo esc_html(human_time_diff($since)); ?><?php echo $late ? ' · late' : ''; ?></small>
            <?php if ($o->has_status('on-hold')): ?><br><span class="bdesk-tag">On hold</span><?php endif; ?>
        </td>
        <td data-label="Customer"><?php echo esc_html($o->get_formatted_billing_full_name()); ?><br><small><?php echo esc_html(implode(', ', $place)); ?></small>
            <?php if ($phone): ?><br><span class="bdesk-contact"><a href="tel:<?php echo esc_attr(preg_replace('/[^\d+]/', '', $phone)); ?>"><?php echo esc_html($phone); ?></a><?php if ($wa): ?> <a class="bdesk-wa" target="_blank" rel="noopener" href="<?php echo esc_url($wa); ?>">WhatsApp</a><?php endif; ?></span><?php endif; ?>
        </td>
        <td data-label="Items" class="bdesk-items">
            <?php bdesk_wa_chips($o); ?>
            <?php foreach ($o->get_items() as $it): ?><div><?php echo esc_html($it->get_quantity()); ?> × <?php echo esc_html($it->get_name()); ?></div><?php endforeach; ?>
            <?php if ($o->get_customer_note()): ?><div class="bdesk-flag">Note: <?php echo esc_html($o->get_customer_note()); ?></div><?php endif; ?>
            <?php if (isset($dupes[$o->get_id()])): ?><div class="bdesk-flag is-warn">Same customer also ordered #<?php echo esc_html(implode(', #', $dupes[$o->get_id()])); ?>. Check before shipping both.</div><?php endif; ?>
            <?php if ($tries && $stage === 'new'): ?><div class="bdesk-flag is-warn"><?php echo esc_html(sprintf(_n('%d attempt, no answer', '%d attempts, no answer', $tries, 'boutique-desk'), $tries)); ?></div><?php endif; ?>
            <?php if ($serials && $stage !== 'confirmed'): ?><div class="bdesk-flag">Serial: <?php echo esc_html($serials); ?></div><?php endif; ?>
            <?php if ($stage === 'shipped' || $stage === 'returned'): $ship = trim($o->get_meta('_bdesk_courier') . ' ' . $o->get_meta('_bdesk_tracking')); if ($ship): ?><div class="bdesk-flag"><?php echo esc_html($ship); ?></div><?php endif; endif; ?>
        </td>
        <td data-label="Total"><?php echo wp_kses_post(bdesk_money($o->get_total(), $o)); ?><br><?php echo $cod ? '<span class="bdesk-tag is-cod">COD</span>' : '<span class="bdesk-tag is-paid">Paid</span>'; ?></td>
        <td data-label="Next step" class="bdesk-actions">
            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <input type="hidden" name="action" value="bdesk_step"><input type="hidden" name="id" value="<?php echo esc_attr($o->get_id()); ?>"><input type="hidden" name="tab" value="<?php echo esc_attr($stage); ?>">
                <?php wp_nonce_field('bdesk_step_' . $o->get_id(), '_wpnonce', false); ?>
                <?php if ($stage === 'new'): ?>
                    <?php echo bdesk_step_button('confirm', 'Confirmed', 'button-primary'); ?>
                    <?php echo bdesk_step_button('no_answer', 'No answer'); ?>
                    <?php echo bdesk_step_button('cancel', 'Cancel order', 'bdesk-danger', 'Cancel this order? The customer will not be charged and stock is returned.'); ?>
                <?php elseif ($stage === 'confirmed'): ?>
                    <input type="text" name="serials" value="<?php echo esc_attr($serials); ?>" placeholder="Serial no. (optional)" aria-label="Serial number">
                    <?php echo bdesk_step_button('pack', 'Packed', 'button-primary'); ?>
                    <a class="button button-small" target="_blank" href="<?php echo esc_url(bdesk_doc_url($o->get_id(), 'slip')); ?>">Slip</a>
                    <a class="button button-small" target="_blank" href="<?php echo esc_url(bdesk_doc_url($o->get_id(), 'invoice')); ?>">Invoice</a>
                <?php elseif ($stage === 'packed'): ?>
                    <select name="courier" aria-label="Courier"><?php foreach (bdesk_couriers() as $c): ?><option><?php echo esc_html($c); ?></option><?php endforeach; ?></select>
                    <input type="text" name="tracking" placeholder="Tracking no." aria-label="Tracking number">
                    <?php echo bdesk_step_button('ship', 'Shipped', 'button-primary'); ?>
                    <a class="button button-small" target="_blank" href="<?php echo esc_url(bdesk_doc_url($o->get_id(), 'slip')); ?>">Slip</a>
                <?php elseif ($stage === 'shipped'): ?>
                    <?php echo bdesk_step_button('deliver', $cod ? 'Delivered, cash received' : 'Delivered', 'button-primary'); ?>
                    <?php echo bdesk_step_button('return', 'Came back', 'bdesk-danger', $cod ? 'Mark this parcel as returned? The order will be cancelled and stock returned.' : 'Mark this parcel as returned?'); ?>
                <?php elseif ($stage === 'returned'): ?>
                    <a class="button button-small button-primary" href="<?php echo esc_url($o->get_edit_order_url()); ?>">Open order to refund</a>
                <?php endif; ?>
                <?php if ($stage !== bdesk_first_stage()): ?><button type="submit" name="do" value="back" class="button-link bdesk-back">Undo</button><?php endif; ?>
            </form>
        </td>
    </tr>
    <?php
}

function bdesk_dashboard_page() {
    if (!current_user_can('edit_shop_orders')) wp_die(esc_html__('You do not have access to this page.', 'boutique-desk'));
    $tz = wp_timezone();
    $today = (new DateTimeImmutable('today', $tz))->getTimestamp();
    $tomorrow = $today + DAY_IN_SECONDS;
    $month = (new DateTimeImmutable('first day of this month midnight', $tz))->getTimestamp();
    $last_month = (new DateTimeImmutable('first day of last month midnight', $tz))->getTimestamp();

    $today_orders = bdesk_orders_between($today, $tomorrow);
    $month_orders = bdesk_orders_between($month, $tomorrow);
    $prev_orders = bdesk_orders_between($last_month, $month);
    $month_sales = bdesk_sum($month_orders);
    $prev_sales = bdesk_sum($prev_orders);

    // Open orders by step, and the cash that is still out.
    $pipeline = bdesk_pipeline();
    $stages = bdesk_stages();
    if (bdesk_first_stage() !== 'new') unset($stages['new']);
    if (!$pipeline['returned']) unset($stages['returned']);
    $all_open = array();
    foreach ($pipeline as $list) $all_open = array_merge($all_open, $list);
    $dupes = bdesk_duplicates($all_open);
    $cod_open = 0; $cod_transit = 0; $late_count = 0;
    foreach ($pipeline as $stage => $list) {
        foreach ($list as $o) {
            if ((time() - bdesk_stage_since($o, $stage)) > bdesk_late_after($stage) * HOUR_IN_SECONDS) $late_count++;
            if (!bdesk_is_cod($o)) continue;
            $cod_open += (float) $o->get_total();
            if ($stage === 'shipped') $cod_transit += (float) $o->get_total();
        }
    }
    $tab = isset($_GET['tab']) ? sanitize_key($_GET['tab']) : '';
    if (!isset($stages[$tab]) && $tab !== 'done') {
        $tab = key($stages);
        foreach ($stages as $k => $label) { if ($pipeline[$k]) { $tab = $k; break; } }
    }

    // Last 14 days, one bar per day.
    $days = array();
    $range = bdesk_orders_between($today - 13 * DAY_IN_SECONDS, $tomorrow);
    for ($i = 13; $i >= 0; $i--) $days[wp_date('Y-m-d', $today - $i * DAY_IN_SECONDS)] = 0;
    foreach ($range as $o) {
        $key = wp_date('Y-m-d', $o->get_date_created()->getTimestamp());
        if (isset($days[$key])) $days[$key] += (float) $o->get_total() - (float) $o->get_total_refunded();
    }
    $peak = max(1, max($days));

    // Best sellers this month, by quantity.
    $best = array();
    foreach ($month_orders as $o) {
        foreach ($o->get_items() as $item) {
            $pid = $item->get_product_id();
            if (!isset($best[$pid])) $best[$pid] = array('name' => $item->get_name(), 'qty' => 0, 'total' => 0);
            $best[$pid]['qty'] += $item->get_quantity();
            $best[$pid]['total'] += (float) $item->get_total();
        }
    }
    uasort($best, function ($a, $b) { return $b['qty'] <=> $a['qty']; });
    $best = array_slice($best, 0, 5, true);
    $low = wc_get_products(array('limit' => 8, 'status' => 'publish', 'stock_status' => array('outofstock')));

    $orders_url = class_exists('\Automattic\WooCommerce\Utilities\OrderUtil') && \Automattic\WooCommerce\Utilities\OrderUtil::custom_orders_table_usage_is_enabled()
        ? admin_url('admin.php?page=wc-orders') : admin_url('edit.php?post_type=shop_order');
    $change = $prev_sales > 0 ? round((($month_sales - $prev_sales) / $prev_sales) * 100) : null;
    $user = wp_get_current_user();
    $notice = get_transient('bdesk_notice_' . $user->ID);
    if ($notice) delete_transient('bdesk_notice_' . $user->ID);
    $hints = array(
        'new' => 'Call or WhatsApp each customer to confirm the order and address before anything is packed.',
        'confirmed' => 'Print the slip, pack the order, note the serial number, then press Packed.',
        'packed' => 'Choose the courier, type the tracking number, then press Shipped. The customer is told automatically.',
        'shipped' => 'When the courier confirms delivery' . ($cod_transit ? ' and pays the cash' : '') . ', press Delivered. If the parcel comes back, press Came back.',
        'returned' => 'These parcels came back but were already paid for. Refund the customer or send the order again.',
    );
    ?>
    <div class="wrap bdesk-wrap" style="--bdesk-accent:<?php echo esc_attr(bdesk_accent()); ?>">
        <div class="bdesk-head">
            <div><h1>Store Desk</h1><p>Hello <?php echo esc_html($user->display_name); ?>, here is the store on <?php echo esc_html(wp_date('l, j F')); ?>.</p></div>
            <div class="bdesk-head-actions">
                <?php if (current_user_can('publish_products')): ?><a class="button button-primary" href="<?php echo esc_url(admin_url('post-new.php?post_type=product')); ?>">Add product</a><?php endif; ?>
                <a class="button" href="<?php echo esc_url($orders_url); ?>">All orders</a>
            </div>
        </div>
        <?php $wa_err = get_option('bdesk_wa_last_error'); if ($wa_err && (time() - (int) $wa_err['time']) < 2 * DAY_IN_SECONDS): ?>
            <div class="notice notice-error"><p><strong>WhatsApp problem:</strong> <?php echo esc_html($wa_err['text']); ?><?php echo in_array((int) $wa_err['code'], array(190, 102), true) ? ' The access token has expired or is wrong. Update it in WooCommerce &rarr; Store Desk settings.' : ''; ?> Customers were not messaged; call them instead.</p></div>
        <?php endif; ?>
        <?php if ($notice): ?><div class="notice notice-<?php echo esc_attr($notice[0]); ?> is-dismissible"><p><?php echo esc_html($notice[1]); ?></p></div><?php endif; ?>

        <div class="bdesk-cards">
            <div class="bdesk-card is-accent"><span>Open orders</span><strong><?php echo esc_html(count($all_open)); ?></strong><small><?php echo $late_count ? esc_html(sprintf(_n('%d is late', '%d are late', $late_count, 'boutique-desk'), $late_count)) : 'All on time'; ?></small></div>
            <div class="bdesk-card"><span>Cash to collect</span><strong><?php echo wp_kses_post(bdesk_money($cod_open)); ?></strong><small><?php echo wp_kses_post(bdesk_money($cod_transit)); ?> with couriers</small></div>
            <div class="bdesk-card"><span>Sales today</span><strong><?php echo wp_kses_post(bdesk_money(bdesk_sum($today_orders))); ?></strong><small><?php echo esc_html(sprintf(_n('%d order', '%d orders', count($today_orders), 'boutique-desk'), count($today_orders))); ?></small></div>
            <div class="bdesk-card"><span>Sales this month</span><strong><?php echo wp_kses_post(bdesk_money($month_sales)); ?></strong><small><?php echo $change === null ? esc_html(sprintf(_n('%d order', '%d orders', count($month_orders), 'boutique-desk'), count($month_orders))) : esc_html(($change >= 0 ? '+' : '') . $change . '% vs last month'); ?></small></div>
            <div class="bdesk-card"><span>Average order</span><strong><?php echo wp_kses_post(bdesk_money(count($month_orders) ? $month_sales / count($month_orders) : 0)); ?></strong><small>This month</small></div>
        </div>

        <section class="bdesk-panel bdesk-pipeline">
            <nav class="bdesk-steps" aria-label="Order steps">
                <?php $n = 0; foreach ($stages as $k => $label): $n++; ?>
                    <a class="bdesk-step<?php echo $tab === $k ? ' is-current' : ''; ?>" href="<?php echo esc_url(bdesk_tab_url($k)); ?>"<?php echo $tab === $k ? ' aria-current="page"' : ''; ?>><em><?php echo esc_html($n); ?></em><span><?php echo esc_html($label); ?></span><b><?php echo esc_html(count($pipeline[$k])); ?></b></a>
                <?php endforeach; ?>
                <a class="bdesk-step is-done<?php echo $tab === 'done' ? ' is-current' : ''; ?>" href="<?php echo esc_url(bdesk_tab_url('done')); ?>"><span>Delivered</span></a>
            </nav>

            <?php if ($tab === 'done'):
                $done = wc_get_orders(array('limit' => 25, 'type' => 'shop_order', 'status' => 'completed', 'orderby' => 'date', 'order' => 'DESC')); ?>
                <?php if ($done): ?>
                <table class="widefat striped bdesk-table">
                    <thead><tr><th>Order</th><th>Customer</th><th>Items</th><th>Total</th><th>Shipping</th><th></th></tr></thead>
                    <tbody><?php foreach ($done as $o): ?>
                        <tr>
                            <td data-label="Order"><a href="<?php echo esc_url($o->get_edit_order_url()); ?>"><strong>#<?php echo esc_html($o->get_order_number()); ?></strong></a><br><small><?php echo esc_html(wc_format_datetime($o->get_date_completed() ?: $o->get_date_created(), 'j M Y')); ?></small></td>
                            <td data-label="Customer"><?php echo esc_html($o->get_formatted_billing_full_name()); ?><br><small><?php echo esc_html($o->get_shipping_city() ?: $o->get_billing_city()); ?></small></td>
                            <td data-label="Items" class="bdesk-items"><?php foreach ($o->get_items() as $it): ?><div><?php echo esc_html($it->get_quantity()); ?> × <?php echo esc_html($it->get_name()); ?></div><?php endforeach; ?></td>
                            <td data-label="Total"><?php echo wp_kses_post(bdesk_money($o->get_total() - $o->get_total_refunded(), $o)); ?></td>
                            <td data-label="Shipping"><?php echo esc_html(trim($o->get_meta('_bdesk_courier') . ' ' . $o->get_meta('_bdesk_tracking')) ?: '—'); ?></td>
                            <td class="bdesk-actions"><a class="button button-small" target="_blank" href="<?php echo esc_url(bdesk_doc_url($o->get_id(), 'invoice')); ?>">Invoice</a></td>
                        </tr>
                    <?php endforeach; ?></tbody>
                </table>
                <?php else: ?><p class="bdesk-empty">No delivered orders yet.</p><?php endif; ?>
            <?php else: $list = $pipeline[$tab]; ?>
                <div class="bdesk-panel-head"><p class="bdesk-hint"><?php echo esc_html($hints[$tab]); ?></p>
                    <?php if ($list && in_array($tab, array('confirmed', 'packed'), true)): $ids = array_map(function ($o) { return $o->get_id(); }, $list); ?>
                    <div><a class="button" target="_blank" href="<?php echo esc_url(bdesk_doc_url($ids, 'slip')); ?>">Print all packing slips</a> <a class="button" target="_blank" href="<?php echo esc_url(bdesk_doc_url($ids, 'invoice')); ?>">Print all invoices</a></div>
                    <?php endif; ?>
                </div>
                <?php if ($list): ?>
                <table class="widefat striped bdesk-table">
                    <thead><tr><th>Order</th><th>Customer</th><th>Items</th><th>Total</th><th>Next step</th></tr></thead>
                    <tbody><?php foreach ($list as $o) bdesk_order_row($o, $tab, $dupes); ?></tbody>
                </table>
                <?php else: ?><p class="bdesk-empty">Nothing waiting at this step.</p><?php endif; ?>
            <?php endif; ?>
        </section>

        <div class="bdesk-grid">
            <section class="bdesk-panel bdesk-chart-panel">
                <h2>Last 14 days</h2>
                <div class="bdesk-chart" role="img" aria-label="Daily sales for the last 14 days">
                    <?php foreach ($days as $d => $v): ?>
                        <div class="bdesk-bar" title="<?php echo esc_attr(wp_date('j M', strtotime($d)) . ': ' . html_entity_decode(wp_strip_all_tags(bdesk_money($v)))); ?>"><i style="height:<?php echo esc_attr(max(2, round($v / $peak * 100))); ?>%"></i><em><?php echo esc_html(wp_date('j', strtotime($d))); ?></em></div>
                    <?php endforeach; ?>
                </div>
            </section>
            <section class="bdesk-panel">
                <h2>Best sellers this month</h2>
                <?php if ($best): ?><ol class="bdesk-list"><?php foreach ($best as $pid => $b): ?><li><a href="<?php echo esc_url(get_edit_post_link($pid)); ?>"><?php echo esc_html($b['name']); ?></a><span><?php echo esc_html($b['qty']); ?> sold</span></li><?php endforeach; ?></ol>
                <?php else: ?><p class="bdesk-empty">No sales yet this month.</p><?php endif; ?>
                <?php if ($low): ?><h2 class="bdesk-sub">Sold out</h2><ul class="bdesk-list"><?php foreach ($low as $p): ?><li><a href="<?php echo esc_url(get_edit_post_link($p->get_id())); ?>"><?php echo esc_html($p->get_name()); ?></a><span class="bdesk-tag">Restock</span></li><?php endforeach; ?></ul><?php endif; ?>
            </section>
        </div>
    </div>
    <?php
}

/* Small WhatsApp status chips on an order row. */
function bdesk_wa_chips($o) {
    $reply = $o->get_meta('_bdesk_wa_reply');
    if ($reply) {
        $cls = $reply === 'yes' ? 'is-ok' : 'is-warn';
        echo '<div><span class="bdesk-chip ' . esc_attr($cls) . '">' . esc_html(sprintf(__('Customer replied: %s', 'boutique-desk'), $o->get_meta('_bdesk_wa_reply_text'))) . '</span></div>';
    }
    foreach (bdesk_wa_events() as $ev) {
        $st = $o->get_meta('_bdesk_wa_' . $ev);
        if (!$st) continue;
        if ($st === 'failed') echo '<div><span class="bdesk-chip is-warn">' . esc_html(sprintf(__('WhatsApp %s failed: call', 'boutique-desk'), $ev)) . '</span></div>';
        elseif ($ev === 'confirm' || $ev === 'shipped') echo '<div><span class="bdesk-chip">' . esc_html(sprintf(__('WhatsApp %1$s: %2$s', 'boutique-desk'), $ev, $st === 'queued' ? 'sending' : $st)) . '</span></div>';
    }
}
