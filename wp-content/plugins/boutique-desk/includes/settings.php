<?php
if (!defined('ABSPATH')) exit;

/* Invoice details: store identity, GST and numbering. Admin only. */
function bdesk_defaults() {
    return array(
        'store_name' => get_bloginfo('name'),
        'address' => '',
        'state' => '',
        'gstin' => '',
        'phone' => '',
        'email' => get_option('admin_email'),
        'logo' => '',
        'prefix' => 'INV-',
        'next_number' => 1,
        'hsn' => '6204',
        'tax_inclusive' => 1,
        'invoice_note' => 'Thank you for shopping with us.',
        'slip_note' => 'Dry clean embroidered pieces. Exchanges within 7 days of delivery with tags attached.',
    );
}

function bdesk_settings() {
    return wp_parse_args((array) get_option('bdesk_settings', array()), bdesk_defaults());
}

function bdesk_sanitize_settings($in) {
    $d = bdesk_defaults();
    $out = array();
    foreach ($d as $k => $v) {
        $raw = isset($in[$k]) ? $in[$k] : '';
        if ($k === 'address' || $k === 'invoice_note' || $k === 'slip_note') $out[$k] = sanitize_textarea_field($raw);
        elseif ($k === 'email') $out[$k] = sanitize_email($raw);
        elseif ($k === 'logo') $out[$k] = esc_url_raw($raw);
        elseif ($k === 'next_number') $out[$k] = max(1, absint($raw));
        elseif ($k === 'tax_inclusive') $out[$k] = empty($raw) ? 0 : 1;
        else $out[$k] = sanitize_text_field($raw);
    }
    $out['gstin'] = strtoupper($out['gstin']);
    return $out;
}

add_action('admin_init', function () {
    register_setting('bdesk_settings', 'bdesk_settings', array('sanitize_callback' => 'bdesk_sanitize_settings'));
});

add_action('admin_menu', function () {
    add_submenu_page('woocommerce', __('Invoices & slips', 'boutique-desk'), __('Invoices & slips', 'boutique-desk'), 'manage_woocommerce', 'bdesk-settings', 'bdesk_settings_page');
}, 60);

function bdesk_settings_page() {
    $s = bdesk_settings();
    $fields = array(
        'store_name' => array('Store name', 'text'),
        'address' => array('Store address (printed on invoices and as return address on slips)', 'textarea'),
        'state' => array('Store state (for GST place of supply)', 'text'),
        'gstin' => array('GSTIN', 'text'),
        'phone' => array('Phone', 'text'),
        'email' => array('Email', 'email'),
        'logo' => array('Logo URL (optional)', 'url'),
        'prefix' => array('Invoice number prefix', 'text'),
        'next_number' => array('Next invoice number', 'number'),
        'hsn' => array('Default HSN code for clothing (can be changed per product)', 'text'),
        'tax_inclusive' => array('Prices include GST', 'checkbox'),
        'invoice_note' => array('Note at the bottom of invoices', 'textarea'),
        'slip_note' => array('Care / returns note on packing slips', 'textarea'),
    );
    echo '<div class="wrap bdesk-wrap"><h1>Invoices &amp; packing slips</h1><form method="post" action="options.php">';
    settings_fields('bdesk_settings');
    echo '<table class="form-table" role="presentation">';
    foreach ($fields as $k => $f) {
        $name = 'bdesk_settings[' . $k . ']';
        echo '<tr><th scope="row"><label for="bdesk-' . esc_attr($k) . '">' . esc_html($f[0]) . '</label></th><td>';
        if ($f[1] === 'textarea') echo '<textarea class="large-text" rows="3" id="bdesk-' . esc_attr($k) . '" name="' . esc_attr($name) . '">' . esc_textarea($s[$k]) . '</textarea>';
        elseif ($f[1] === 'checkbox') echo '<input type="checkbox" id="bdesk-' . esc_attr($k) . '" name="' . esc_attr($name) . '" value="1" ' . checked($s[$k], 1, false) . '>';
        else echo '<input class="regular-text" type="' . esc_attr($f[1]) . '" id="bdesk-' . esc_attr($k) . '" name="' . esc_attr($name) . '" value="' . esc_attr($s[$k]) . '">';
        echo '</td></tr>';
    }
    echo '</table>';
    submit_button();
    echo '</form><p>Give someone limited access: Users &rarr; Add New, role <strong>Store Editor</strong>. They can add and edit products, process orders and print documents, but cannot delete anything or change settings.</p></div>';
}

/* Per-product HSN code, in the product's General tab. */
add_action('woocommerce_product_options_general_product_data', function () {
    woocommerce_wp_text_input(array('id' => '_bdesk_hsn', 'label' => __('HSN code', 'boutique-desk'), 'desc_tip' => true, 'description' => __('Printed on GST invoices. Leave blank to use the default from Invoices & slips.', 'boutique-desk')));
});
add_action('woocommerce_admin_process_product_object', function ($product) {
    if (isset($_POST['_bdesk_hsn'])) $product->update_meta_data('_bdesk_hsn', sanitize_text_field(wp_unslash($_POST['_bdesk_hsn'])));
});
