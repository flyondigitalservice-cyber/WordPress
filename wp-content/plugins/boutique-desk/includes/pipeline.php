<?php
if (!defined('ABSPATH')) exit;

/*
 * Order steps. An order that WooCommerce calls "Processing" moves through
 * Confirm -> Pack -> Hand to courier -> In transit, and only becomes "Completed"
 * when it is delivered. The step lives in order meta, so WooCommerce statuses,
 * emails and other plugins keep working as before.
 */

function bdesk_stages() {
    return array(
        'new' => __('To confirm', 'boutique-desk'),
        'confirmed' => __('To pack', 'boutique-desk'),
        'packed' => __('Hand to courier', 'boutique-desk'),
        'shipped' => __('In transit', 'boutique-desk'),
        'returned' => __('Returned', 'boutique-desk'),
    );
}

function bdesk_open_statuses() {
    return array('processing', 'on-hold');
}

function bdesk_first_stage() {
    $s = bdesk_settings();
    return $s['confirm_orders'] ? 'new' : 'confirmed';
}

/* The step an open order is at; empty for orders that are finished or not yet paid for. */
function bdesk_stage($order) {
    if ($order->has_status('completed')) return 'delivered';
    if (!$order->has_status(bdesk_open_statuses())) return '';
    $stage = $order->get_meta('_bdesk_stage');
    if (!isset(bdesk_stages()[$stage])) return bdesk_first_stage();
    if ($stage === 'new' && bdesk_first_stage() !== 'new') return 'confirmed';
    return $stage;
}

/* Cash still has to be collected: a cash on delivery order that has not been delivered yet. */
function bdesk_is_cod($order) {
    $cod = $order->get_payment_method() === 'cod' && !$order->has_status(array('completed', 'refunded'));
    return (bool) apply_filters('bdesk_is_cod', $cod, $order);
}

/* All open orders, oldest first, grouped by step. */
function bdesk_pipeline() {
    $groups = array_fill_keys(array_keys(bdesk_stages()), array());
    $orders = wc_get_orders(array('limit' => 400, 'type' => 'shop_order', 'status' => bdesk_open_statuses(), 'orderby' => 'date', 'order' => 'ASC'));
    foreach ($orders as $o) {
        $stage = bdesk_stage($o);
        if (isset($groups[$stage])) $groups[$stage][] = $o;
    }
    return $groups;
}

/* Digits only, with the country code, as wa.me wants it. */
function bdesk_phone_digits($order) {
    $raw = trim((string) ($order->get_billing_phone() ?: $order->get_shipping_phone()));
    $digits = preg_replace('/\D+/', '', $raw);
    if ($digits === '') return '';
    if (strpos($raw, '+') === 0) return $digits;
    if (strpos($digits, '00') === 0) return substr($digits, 2);
    $country = $order->get_billing_country() ?: $order->get_shipping_country();
    $code = $country ? WC()->countries->get_country_calling_code($country) : '';
    if (is_array($code)) $code = reset($code);
    $code = preg_replace('/\D+/', '', (string) $code);
    if ($code === '') return $digits;
    if (strpos($digits, $code) === 0 && strlen($digits) >= strlen($code) + 8) return $digits;
    return $code . ltrim($digits, '0');
}

/* "AED 999.00": the currency code reads the same in English and Arabic messages, unlike a symbol. */
function bdesk_plain_money($amount, $order) {
    return $order->get_currency() . ' ' . number_format((float) $amount, wc_get_price_decimals(), '.', ',');
}

function bdesk_message($template, $order) {
    $items = array();
    foreach ($order->get_items() as $it) $items[] = ($it->get_quantity() > 1 ? $it->get_quantity() . ' x ' : '') . bdesk_short_name($it->get_name());
    $map = array(
        '{name}' => $order->get_billing_first_name() ?: $order->get_formatted_billing_full_name(),
        '{order}' => $order->get_order_number(),
        '{items}' => implode(', ', $items),
        '{total}' => bdesk_plain_money($order->get_total(), $order),
        '{payment}' => bdesk_is_cod($order) ? __('cash on delivery', 'boutique-desk') : __('paid', 'boutique-desk'),
        '{store}' => bdesk_settings()['store_name'],
        '{courier}' => $order->get_meta('_bdesk_courier') ?: __('our courier', 'boutique-desk'),
        '{tracking}' => $order->get_meta('_bdesk_tracking') ?: '-',
    );
    return strtr($template, $map);
}

function bdesk_wa_url($order, $which) {
    $phone = bdesk_phone_digits($order);
    if ($phone === '') return '';
    $s = bdesk_settings();
    return 'https://wa.me/' . $phone . '?text=' . rawurlencode(bdesk_message($which === 'shipped' ? $s['wa_shipped'] : $s['wa_confirm'], $order));
}

function bdesk_couriers() {
    $s = bdesk_settings();
    return array_values(array_filter(array_map('trim', explode(',', $s['couriers']))));
}

function bdesk_set_stage($order, $stage, $note) {
    $order->update_meta_data('_bdesk_stage', $stage);
    $order->update_meta_data('_bdesk_' . $stage . '_at', time());
    $order->save();
    $user = wp_get_current_user();
    $order->add_order_note($note . ($user && $user->ID ? ' (' . $user->display_name . ')' : ''));
}

/* One step forward, back, or out. Returns a short message for the desk, or WP_Error. */
function bdesk_do_step($order, $do, $data = array()) {
    $stage = bdesk_stage($order);
    $wrong = new WP_Error('bdesk_step', __('That order has already moved on. The desk has been refreshed.', 'boutique-desk'));
    switch ($do) {
        case 'confirm':
            if ($stage !== 'new') return $wrong;
            bdesk_set_stage($order, 'confirmed', __('Store Desk: customer confirmed the order.', 'boutique-desk'));
            return __('Order confirmed. It is now waiting to be packed.', 'boutique-desk');
        case 'no_answer':
            if ($stage !== 'new') return $wrong;
            $tries = (int) $order->get_meta('_bdesk_attempts') + 1;
            $order->update_meta_data('_bdesk_attempts', $tries);
            $order->update_meta_data('_bdesk_attempt_at', time());
            $order->save();
            /* translators: %d: number of contact attempts */
            $order->add_order_note(sprintf(__('Store Desk: customer did not answer (attempt %d).', 'boutique-desk'), $tries));
            return __('Noted. The order stays in To confirm.', 'boutique-desk');
        case 'cancel':
            if (!in_array($stage, array('new', 'confirmed'), true)) return $wrong;
            $order->update_status('cancelled', __('Store Desk: cancelled before packing.', 'boutique-desk'), true);
            return __('Order cancelled and stock returned.', 'boutique-desk');
        case 'pack':
            if ($stage !== 'confirmed') return $wrong;
            if (isset($data['serials'])) $order->update_meta_data('_bdesk_serials', sanitize_text_field($data['serials']));
            bdesk_set_stage($order, 'packed', __('Store Desk: order packed.', 'boutique-desk'));
            return __('Order packed. Hand it to the courier next.', 'boutique-desk');
        case 'ship':
            if ($stage !== 'packed') return $wrong;
            $courier = isset($data['courier']) ? sanitize_text_field($data['courier']) : '';
            $tracking = isset($data['tracking']) ? sanitize_text_field($data['tracking']) : '';
            $order->update_meta_data('_bdesk_courier', $courier);
            $order->update_meta_data('_bdesk_tracking', $tracking);
            $line = trim($courier . ($tracking !== '' ? ' ' . $tracking : ''));
            /* translators: %s: courier and tracking number */
            bdesk_set_stage($order, 'shipped', $line !== '' ? sprintf(__('Store Desk: shipped with %s.', 'boutique-desk'), $line) : __('Store Desk: shipped.', 'boutique-desk'));
            $s = bdesk_settings();
            if ($s['email_tracking'] && $line !== '') {
                /* translators: 1: courier, 2: tracking number */
                $msg = $tracking !== '' ? sprintf(__('Your order has shipped with %1$s. Tracking number: %2$s.', 'boutique-desk'), $courier ?: __('our courier', 'boutique-desk'), $tracking)
                    : sprintf(__('Your order has shipped with %s.', 'boutique-desk'), $courier);
                $order->add_order_note($msg, 1);
            }
            do_action('bdesk_order_shipped', $order);
            return __('Order shipped. It is now in transit.', 'boutique-desk');
        case 'deliver':
            if ($stage !== 'shipped') return $wrong;
            $cod = bdesk_is_cod($order);
            $order->update_meta_data('_bdesk_stage', 'delivered');
            $order->update_meta_data('_bdesk_delivered_at', time());
            $order->save();
            $order->update_status('completed', $cod ? __('Store Desk: delivered and cash collected.', 'boutique-desk') : __('Store Desk: delivered.', 'boutique-desk'), true);
            do_action('bdesk_order_delivered', $order);
            return __('Order delivered and completed.', 'boutique-desk');
        case 'return':
            if ($stage !== 'shipped') return $wrong;
            if (bdesk_is_cod($order)) {
                $order->update_meta_data('_bdesk_stage', 'returned');
                $order->save();
                $order->update_status('cancelled', __('Store Desk: parcel came back undelivered.', 'boutique-desk'), true);
                return __('Marked as returned. The order is cancelled and stock returned.', 'boutique-desk');
            }
            bdesk_set_stage($order, 'returned', __('Store Desk: parcel came back undelivered. This order was paid, so it needs a refund or a re-send.', 'boutique-desk'));
            return __('Marked as returned. This order was paid: refund it or send it again.', 'boutique-desk');
        case 'back':
            $prev = array('confirmed' => 'new', 'packed' => 'confirmed', 'shipped' => 'packed', 'returned' => 'shipped');
            if (!isset($prev[$stage]) || ($prev[$stage] === 'new' && bdesk_first_stage() !== 'new')) return $wrong;
            /* translators: %s: step name */
            bdesk_set_stage($order, $prev[$stage], sprintf(__('Store Desk: moved back to "%s".', 'boutique-desk'), bdesk_stages()[$prev[$stage]]));
            return __('Order moved back one step.', 'boutique-desk');
    }
    return new WP_Error('bdesk_step', __('Unknown action.', 'boutique-desk'));
}

add_action('admin_post_bdesk_step', function () {
    $id = isset($_POST['id']) ? absint($_POST['id']) : 0;
    check_admin_referer('bdesk_step_' . $id);
    if (!current_user_can('edit_shop_orders')) wp_die(esc_html__('You do not have permission to do this.', 'boutique-desk'));
    $do = isset($_POST['do']) ? sanitize_key($_POST['do']) : '';
    $tab = isset($_POST['tab']) ? sanitize_key($_POST['tab']) : '';
    $order = wc_get_order($id);
    $result = ($order && $order->get_type() === 'shop_order') ? bdesk_do_step($order, $do, wp_unslash($_POST)) : new WP_Error('bdesk_step', __('Order not found.', 'boutique-desk'));
    set_transient('bdesk_notice_' . get_current_user_id(), array(is_wp_error($result) ? 'error' : 'success', is_wp_error($result) ? $result->get_error_message() : '#' . $order->get_order_number() . ': ' . $result), 60);
    wp_safe_redirect(admin_url('admin.php?page=bdesk' . ($tab ? '&tab=' . $tab : '')));
    exit;
});

/* ---------- The step, courier and tracking elsewhere in the store ---------- */

function bdesk_stage_label($order) {
    $stage = bdesk_stage($order);
    if ($stage === 'delivered') return __('Delivered', 'boutique-desk');
    $stages = bdesk_stages();
    return isset($stages[$stage]) ? $stages[$stage] : '';
}

/* A "Step" column in the WooCommerce orders list (both order storage modes). */
foreach (array('manage_edit-shop_order_columns', 'woocommerce_shop_order_list_table_columns') as $f_hook) {
    add_filter($f_hook, function ($cols) {
        $out = array();
        foreach ($cols as $k => $v) {
            $out[$k] = $v;
            if ($k === 'order_status') $out['bdesk_stage'] = __('Step', 'boutique-desk');
        }
        return $out;
    }, 20);
}
function bdesk_stage_column($order) {
    $label = bdesk_stage_label($order);
    if ($label === '') return;
    echo '<span class="bdesk-tag">' . esc_html($label) . '</span>';
    $tracking = $order->get_meta('_bdesk_tracking');
    if ($tracking) echo '<br><small>' . esc_html(trim($order->get_meta('_bdesk_courier') . ' ' . $tracking)) . '</small>';
}
add_action('woocommerce_shop_order_list_table_custom_column', function ($col, $order) {
    if ($col === 'bdesk_stage' && $order instanceof WC_Order) bdesk_stage_column($order);
}, 10, 2);
add_action('manage_shop_order_posts_custom_column', function ($col, $post_id) {
    if ($col !== 'bdesk_stage') return;
    $order = wc_get_order($post_id);
    if ($order) bdesk_stage_column($order);
}, 10, 2);

/* Customers see the courier and tracking number on their order page and in order emails. */
function bdesk_tracking_lines($order) {
    $courier = $order->get_meta('_bdesk_courier');
    $tracking = $order->get_meta('_bdesk_tracking');
    if (!$courier && !$tracking) return array();
    $lines = array();
    if ($courier) $lines[__('Courier', 'boutique-desk')] = $courier;
    if ($tracking) $lines[__('Tracking number', 'boutique-desk')] = $tracking;
    return $lines;
}
add_action('woocommerce_order_details_after_order_table', function ($order) {
    $lines = bdesk_tracking_lines($order);
    if (!$lines) return;
    echo '<section class="bdesk-tracking"><h2>' . esc_html__('Delivery', 'boutique-desk') . '</h2><p>';
    foreach ($lines as $k => $v) echo esc_html($k) . ': <strong>' . esc_html($v) . '</strong><br>';
    echo '</p></section>';
});
add_action('woocommerce_email_order_meta', function ($order, $sent_to_admin, $plain_text) {
    if (!$order instanceof WC_Order) return;
    $lines = bdesk_tracking_lines($order);
    if (!$lines) return;
    foreach ($lines as $k => $v) echo $plain_text ? esc_html($k . ': ' . $v) . "\n" : '<p style="margin:0 0 6px">' . esc_html($k) . ': <strong>' . esc_html($v) . '</strong></p>';
}, 10, 3);
