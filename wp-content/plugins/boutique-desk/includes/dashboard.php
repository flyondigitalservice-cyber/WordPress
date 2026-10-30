<?php
if (!defined('ABSPATH')) exit;

/* Store Desk: today's numbers, what needs packing, and quick actions. */
add_action('admin_menu', function () {
    add_menu_page(__('Store Desk', 'boutique-desk'), __('Store Desk', 'boutique-desk'), 'edit_shop_orders', 'bdesk', 'bdesk_dashboard_page', 'dashicons-store', 2);
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

function bdesk_status_count($status) {
    return count(wc_get_orders(array('limit' => -1, 'type' => 'shop_order', 'status' => $status, 'return' => 'ids')));
}

function bdesk_doc_url($ids, $type) {
    return wp_nonce_url(admin_url('admin-post.php?action=bdesk_doc&type=' . $type . '&ids=' . implode(',', array_map('absint', (array) $ids))), 'bdesk_doc');
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

    $to_pack = wc_get_orders(array('limit' => 20, 'type' => 'shop_order', 'status' => array('processing', 'on-hold'), 'orderby' => 'date', 'order' => 'ASC'));
    $low = wc_get_products(array('limit' => 8, 'status' => 'publish', 'stock_status' => array('outofstock')));

    $orders_url = function_exists('wc_get_container') && class_exists('\Automattic\WooCommerce\Utilities\OrderUtil') && \Automattic\WooCommerce\Utilities\OrderUtil::custom_orders_table_usage_is_enabled()
        ? admin_url('admin.php?page=wc-orders') : admin_url('edit.php?post_type=shop_order');
    $change = $prev_sales > 0 ? round((($month_sales - $prev_sales) / $prev_sales) * 100) : null;
    $user = wp_get_current_user();
    ?>
    <div class="wrap bdesk-wrap">
        <div class="bdesk-head">
            <div><h1>Store Desk</h1><p>Hello <?php echo esc_html($user->display_name); ?>, here is the store on <?php echo esc_html(wp_date('l, j F')); ?>.</p></div>
            <div class="bdesk-head-actions">
                <?php if (current_user_can('publish_products')): ?><a class="button button-primary" href="<?php echo esc_url(admin_url('post-new.php?post_type=product')); ?>">Add product</a><?php endif; ?>
                <a class="button" href="<?php echo esc_url($orders_url); ?>">All orders</a>
            </div>
        </div>
        <?php if (isset($_GET['bdesk_done'])): ?><div class="notice notice-success is-dismissible"><p>Order marked as completed. The customer has been emailed.</p></div><?php endif; ?>

        <div class="bdesk-cards">
            <div class="bdesk-card"><span>Sales today</span><strong><?php echo wp_kses_post(bdesk_money(bdesk_sum($today_orders))); ?></strong><small><?php echo esc_html(count($today_orders)); ?> orders</small></div>
            <div class="bdesk-card"><span>Sales this month</span><strong><?php echo wp_kses_post(bdesk_money($month_sales)); ?></strong><small><?php echo $change === null ? esc_html(count($month_orders) . ' orders') : esc_html(($change >= 0 ? '+' : '') . $change . '% vs last month'); ?></small></div>
            <div class="bdesk-card is-accent"><span>To pack &amp; ship</span><strong><?php echo esc_html(bdesk_status_count('processing')); ?></strong><small>Processing orders</small></div>
            <div class="bdesk-card"><span>Waiting</span><strong><?php echo esc_html(bdesk_status_count('on-hold') + bdesk_status_count('pending')); ?></strong><small>On hold or awaiting payment</small></div>
            <div class="bdesk-card"><span>Average order</span><strong><?php echo wp_kses_post(bdesk_money(count($month_orders) ? $month_sales / count($month_orders) : 0)); ?></strong><small>This month</small></div>
        </div>

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

        <section class="bdesk-panel">
            <div class="bdesk-panel-head"><h2>Orders to pack</h2>
                <?php if ($to_pack): $ids = array_map(function ($o) { return $o->get_id(); }, $to_pack); ?>
                <div><a class="button" target="_blank" href="<?php echo esc_url(bdesk_doc_url($ids, 'slip')); ?>">Print all packing slips</a> <a class="button" target="_blank" href="<?php echo esc_url(bdesk_doc_url($ids, 'invoice')); ?>">Print all invoices</a></div>
                <?php endif; ?>
            </div>
            <?php if ($to_pack): ?>
            <table class="widefat striped bdesk-table">
                <thead><tr><th>Order</th><th>Date</th><th>Customer</th><th>Items</th><th>Total</th><th>Payment</th><th>Status</th><th></th></tr></thead>
                <tbody>
                <?php foreach ($to_pack as $o):
                    $items = array();
                    foreach ($o->get_items() as $it) $items[] = $it->get_quantity() . ' × ' . $it->get_name();
                    $cod = $o->get_payment_method() === 'cod'; ?>
                    <tr>
                        <td><a href="<?php echo esc_url($o->get_edit_order_url()); ?>"><strong>#<?php echo esc_html($o->get_order_number()); ?></strong></a></td>
                        <td><?php echo esc_html(wc_format_datetime($o->get_date_created(), 'j M, g:i a')); ?></td>
                        <td><?php echo esc_html($o->get_formatted_billing_full_name()); ?><br><small><?php echo esc_html(trim($o->get_shipping_city() ?: $o->get_billing_city()) . ', ' . ($o->get_shipping_postcode() ?: $o->get_billing_postcode())); ?></small></td>
                        <td class="bdesk-items"><?php echo esc_html(implode(', ', $items)); ?></td>
                        <td><?php echo wp_kses_post(bdesk_money($o->get_total(), $o)); ?></td>
                        <td><?php echo $cod ? '<span class="bdesk-tag is-cod">COD</span>' : '<span class="bdesk-tag is-paid">Prepaid</span>'; ?></td>
                        <td><?php echo esc_html(wc_get_order_status_name($o->get_status())); ?></td>
                        <td class="bdesk-actions">
                            <a class="button button-small" target="_blank" href="<?php echo esc_url(bdesk_doc_url($o->get_id(), 'slip')); ?>">Slip</a>
                            <a class="button button-small" target="_blank" href="<?php echo esc_url(bdesk_doc_url($o->get_id(), 'invoice')); ?>">Invoice</a>
                            <?php if ($o->has_status('processing')): ?><a class="button button-small button-primary" href="<?php echo esc_url(wp_nonce_url(admin_url('admin-post.php?action=bdesk_complete&id=' . $o->get_id()), 'bdesk_complete_' . $o->get_id())); ?>">Mark shipped</a><?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?><p class="bdesk-empty">Nothing waiting. Every order is packed.</p><?php endif; ?>
        </section>
    </div>
    <?php
}

/* Mark an order completed from the desk. */
add_action('admin_post_bdesk_complete', function () {
    $id = isset($_GET['id']) ? absint($_GET['id']) : 0;
    check_admin_referer('bdesk_complete_' . $id);
    if (!current_user_can('edit_shop_orders')) wp_die(esc_html__('You do not have permission to do this.', 'boutique-desk'));
    $order = wc_get_order($id);
    if ($order) $order->update_status('completed', __('Marked as shipped from Store Desk.', 'boutique-desk'), true);
    wp_safe_redirect(admin_url('admin.php?page=bdesk&bdesk_done=1'));
    exit;
});
