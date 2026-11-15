<?php
if (!defined('ABSPATH')) exit;

/* Store details, tax wording, order steps and messages. Admin only. */

/* India keeps GST wording; every other country gets VAT / generic wording. */
function bdesk_region() {
    $base = function_exists('wc_get_base_location') ? wc_get_base_location() : array();
    return (isset($base['country']) && $base['country'] === 'IN') ? 'IN' : 'INTL';
}

function bdesk_defaults() {
    $in = bdesk_region() === 'IN';
    return array(
        'store_name' => get_bloginfo('name'),
        'address' => '',
        'state' => '',
        'gstin' => '',
        'tax_id_label' => $in ? 'GSTIN' : 'TRN',
        'tax_label' => $in ? 'GST' : 'VAT',
        'phone' => '',
        'email' => get_option('admin_email'),
        'logo' => '',
        'accent' => '#8c1d3a',
        'prefix' => 'INV-',
        'next_number' => 1,
        'hsn' => $in ? '6204' : '',
        'tax_inclusive' => 1,
        'detail_attrs' => $in ? 'fabric, stitching, pieces' : '',
        'invoice_note' => 'Thank you for shopping with us.',
        'pack_checklist' => $in
            ? 'Before sealing: check each piece for loose threads and stains, include the dupatta and trouser, fold embroidery inwards with tissue.'
            : 'Before sealing: check every item against this slip, add all accessories, and pad the box well.',
        'slip_note' => $in ? 'Dry clean embroidered pieces. Exchanges within 7 days of delivery with tags attached.' : '',
        'product_list' => 1,
        'wa_enabled' => 0,
        'wa_token' => '',
        'wa_phone_id' => '',
        'wa_app_secret' => '',
        'wa_verify_token' => '',
        'wa_api_version' => 'v23.0',
        'wa_auto_confirm' => 1,
        'wa_ar_countries' => 'SA,KW,QA,BH,OM',
        'wa_tpl_confirm' => 'bdesk_order_confirm',
        'wa_tpl_confirm_ar' => 'bdesk_order_confirm_ar',
        'wa_tpl_shipped' => 'bdesk_order_shipped',
        'wa_tpl_shipped_ar' => 'bdesk_order_shipped_ar',
        'wa_tpl_delivered' => 'bdesk_order_delivered',
        'wa_tpl_delivered_ar' => 'bdesk_order_delivered_ar',
        'confirm_orders' => 1,
        'couriers' => $in ? 'Delhivery, Blue Dart, DTDC, India Post, Own delivery' : 'Aramex, DHL, FedEx, Own delivery',
        'email_tracking' => 1,
        'wa_confirm' => 'Hello {name}, this is {store}. We received your order #{order} for {items}. Total: {total} ({payment}). Please reply YES to confirm and we will ship it right away.',
        'wa_shipped' => 'Hello {name}, your {store} order #{order} is on the way with {courier}. Tracking number: {tracking}. Total: {total} ({payment}).',
    );
}

function bdesk_settings() {
    return wp_parse_args((array) get_option('bdesk_settings', array()), bdesk_defaults());
}

function bdesk_accent() {
    $s = bdesk_settings();
    return sanitize_hex_color($s['accent']) ?: '#8c1d3a';
}

function bdesk_sanitize_settings($in) {
    $d = bdesk_defaults();
    $out = array();
    $long = array('address', 'invoice_note', 'slip_note', 'pack_checklist', 'wa_confirm', 'wa_shipped');
    $flags = array('tax_inclusive', 'confirm_orders', 'email_tracking', 'product_list', 'wa_enabled', 'wa_auto_confirm');
    $old = (array) get_option('bdesk_settings', array());
    foreach ($d as $k => $v) {
        $raw = isset($in[$k]) ? $in[$k] : '';
        if (in_array($k, array('wa_token', 'wa_app_secret'), true)) {
            // Secrets are never shown again; leaving the box empty keeps the saved value.
            $out[$k] = trim((string) $raw) === '' ? (isset($old[$k]) ? $old[$k] : '') : sanitize_text_field(trim($raw));
            continue;
        }
        if ($k === 'wa_phone_id') { $out[$k] = preg_replace('/\D+/', '', (string) $raw); continue; }
        if ($k === 'wa_verify_token') { $out[$k] = isset($old[$k]) ? $old[$k] : ''; continue; }
        if ($k === 'wa_api_version') { $out[$k] = preg_match('/^v\d+\.\d+$/', trim((string) $raw)) ? trim($raw) : $v; continue; }
        if ($k === 'wa_ar_countries') { $out[$k] = preg_replace('/[^A-Z,]/', '', strtoupper((string) $raw)); continue; }
        if (strpos($k, 'wa_tpl_') === 0) { $out[$k] = preg_replace('/[^a-z0-9_]/', '', strtolower((string) $raw)); continue; }
        if (in_array($k, $long, true)) $out[$k] = sanitize_textarea_field($raw);
        elseif ($k === 'email') $out[$k] = sanitize_email($raw);
        elseif ($k === 'logo') $out[$k] = esc_url_raw($raw);
        elseif ($k === 'accent') $out[$k] = sanitize_hex_color($raw) ?: $v;
        elseif ($k === 'next_number') $out[$k] = max(1, absint($raw));
        elseif (in_array($k, $flags, true)) $out[$k] = empty($raw) ? 0 : 1;
        else $out[$k] = sanitize_text_field($raw);
    }
    $out['gstin'] = strtoupper($out['gstin']);
    return $out;
}

add_action('admin_init', function () {
    register_setting('bdesk_settings', 'bdesk_settings', array('sanitize_callback' => 'bdesk_sanitize_settings'));
});

add_action('admin_menu', function () {
    add_submenu_page('woocommerce', __('Store Desk settings', 'boutique-desk'), __('Store Desk settings', 'boutique-desk'), 'manage_woocommerce', 'bdesk-settings', 'bdesk_settings_page');
}, 60);

function bdesk_settings_page() {
    $s = bdesk_settings();
    $groups = array(
        'Store details' => array(
            'store_name' => array('Store name', 'text'),
            'address' => array('Store address (printed on invoices and as return address on slips)', 'textarea'),
            'phone' => array('Phone', 'text'),
            'email' => array('Email', 'email'),
            'logo' => array('Logo URL (optional)', 'url'),
            'accent' => array('Accent colour (hex, for the desk and printed documents)', 'text'),
        ),
        'Tax and invoices' => array(
            'tax_label' => array('Tax name (GST, VAT...)', 'text'),
            'tax_id_label' => array('Tax number label (GSTIN, TRN, VAT no...)', 'text'),
            'gstin' => array('Tax registration number', 'text'),
            'state' => array('Store state (India only: GST place of supply)', 'text'),
            'tax_inclusive' => array('Prices include tax', 'checkbox'),
            'hsn' => array('Default HSN / tariff code (leave empty to hide the column; can be set per product)', 'text'),
            'prefix' => array('Invoice number prefix', 'text'),
            'next_number' => array('Next invoice number', 'number'),
            'detail_attrs' => array('Product attributes to print on documents (comma separated, e.g. fabric, colour, ram)', 'text'),
            'invoice_note' => array('Note at the bottom of invoices', 'textarea'),
        ),
        'Packing slips' => array(
            'pack_checklist' => array('Packing checklist line', 'textarea'),
            'slip_note' => array('Care / returns note on packing slips', 'textarea'),
        ),
        'Product list' => array(
            'product_list' => array('Product list upgrades: specs, deal %, sold out button, needs-attention check, bulk actions', 'checkbox'),
        ),
        'WhatsApp (automatic messages)' => array(
            'wa_enabled' => array('Send WhatsApp messages automatically', 'checkbox'),
            'wa_token' => array('Access token (permanent token from a Meta System User)', 'password'),
            'wa_phone_id' => array('Phone number ID (WhatsApp Manager &rarr; API setup)', 'text'),
            'wa_app_secret' => array('App secret (Meta app &rarr; Settings &rarr; Basic; verifies customer replies)', 'password'),
            'wa_auto_confirm' => array('Confirm the order automatically when the customer replies YES', 'checkbox'),
            'wa_ar_countries' => array('Send Arabic templates to customers in these countries (codes, comma separated)', 'text'),
            'wa_tpl_confirm' => array('Template name: order received (English)', 'text'),
            'wa_tpl_confirm_ar' => array('Template name: order received (Arabic, optional)', 'text'),
            'wa_tpl_shipped' => array('Template name: order shipped (English)', 'text'),
            'wa_tpl_shipped_ar' => array('Template name: order shipped (Arabic, optional)', 'text'),
            'wa_tpl_delivered' => array('Template name: order delivered (English)', 'text'),
            'wa_tpl_delivered_ar' => array('Template name: order delivered (Arabic, optional)', 'text'),
            'wa_api_version' => array('Meta API version', 'text'),
        ),
        'Order steps' => array(
            'confirm_orders' => array('Confirm each order with the customer (call or WhatsApp) before packing', 'checkbox'),
            'couriers' => array('Couriers (comma separated)', 'text'),
            'email_tracking' => array('Email the customer the courier and tracking number when an order ships', 'checkbox'),
            'wa_confirm' => array('WhatsApp message: confirm order', 'textarea'),
            'wa_shipped' => array('WhatsApp message: order shipped', 'textarea'),
        ),
    );
    echo '<div class="wrap bdesk-wrap"><h1>Store Desk settings</h1><form method="post" action="options.php">';
    settings_fields('bdesk_settings');
    foreach ($groups as $title => $fields) {
        echo '<h2 class="title">' . esc_html($title) . '</h2><table class="form-table" role="presentation">';
        foreach ($fields as $k => $f) {
            $name = 'bdesk_settings[' . $k . ']';
            echo '<tr><th scope="row"><label for="bdesk-' . esc_attr($k) . '">' . wp_kses_post($f[0]) . '</label></th><td>';
            if ($f[1] === 'textarea') echo '<textarea class="large-text" rows="3" id="bdesk-' . esc_attr($k) . '" name="' . esc_attr($name) . '">' . esc_textarea($s[$k]) . '</textarea>';
            elseif ($f[1] === 'password') echo '<input class="regular-text" type="password" autocomplete="new-password" id="bdesk-' . esc_attr($k) . '" name="' . esc_attr($name) . '" value="" placeholder="' . esc_attr($s[$k] !== '' ? '•••••••• saved (leave empty to keep)' : '') . '">';
            elseif ($f[1] === 'checkbox') echo '<input type="checkbox" id="bdesk-' . esc_attr($k) . '" name="' . esc_attr($name) . '" value="1" ' . checked($s[$k], 1, false) . '>';
            else echo '<input class="regular-text" type="' . esc_attr($f[1]) . '" id="bdesk-' . esc_attr($k) . '" name="' . esc_attr($name) . '" value="' . esc_attr($s[$k]) . '">';
            echo '</td></tr>';
        }
        echo '</table>';
    }
    echo '<p class="description">WhatsApp messages can use {name}, {order}, {items}, {total}, {payment}, {store}, {courier} and {tracking}.</p>';
    submit_button();
    echo '</form>';
    if (function_exists('bdesk_wa_settings_extra')) bdesk_wa_settings_extra();
    echo '<p>Give someone limited access: Users &rarr; Add New, role <strong>Store Editor</strong>. They can add and edit products, process orders and print documents, but cannot delete anything or change settings.</p></div>';
}

/* Per-product HSN / tariff code, shown only to stores that use one. */
function bdesk_uses_codes() {
    $s = bdesk_settings();
    return bdesk_region() === 'IN' || $s['hsn'] !== '';
}
add_action('woocommerce_product_options_general_product_data', function () {
    if (!bdesk_uses_codes()) return;
    woocommerce_wp_text_input(array('id' => '_bdesk_hsn', 'label' => __('HSN code', 'boutique-desk'), 'desc_tip' => true, 'description' => __('Printed on invoices. Leave blank to use the default from Store Desk settings.', 'boutique-desk')));
});
add_action('woocommerce_admin_process_product_object', function ($product) {
    if (isset($_POST['_bdesk_hsn'])) $product->update_meta_data('_bdesk_hsn', sanitize_text_field(wp_unslash($_POST['_bdesk_hsn'])));
});
