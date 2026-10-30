<?php
/**
 * Plugin Name: Boutique Desk
 * Description: Category Sellers with their own limited dashboard, a limited "Store Editor" role with its own sales and orders dashboard, plus GST-ready invoices and packing slips made for clothing stores (size, colour, fabric, HSN, COD to collect).
 * Version: 1.1.0
 * Author: Ferasha
 * Requires Plugins: woocommerce
 * Requires at least: 6.2
 * Requires PHP: 7.4
 * WC requires at least: 7.0
 * Text Domain: boutique-desk
 */

if (!defined('ABSPATH')) exit;

define('BDESK_VERSION', '1.1.0');
define('BDESK_FILE', __FILE__);
define('BDESK_DIR', plugin_dir_path(__FILE__));
define('BDESK_URL', plugin_dir_url(__FILE__));

require_once BDESK_DIR . 'includes/role.php';
require_once BDESK_DIR . 'includes/settings.php';
require_once BDESK_DIR . 'includes/dashboard.php';
require_once BDESK_DIR . 'includes/documents.php';
require_once BDESK_DIR . 'includes/sellers.php';

register_activation_hook(__FILE__, 'bdesk_install_role');
register_deactivation_hook(__FILE__, 'bdesk_remove_role');

/* Works with WooCommerce's order tables (HPOS) as well as the older posts storage. */
add_action('before_woocommerce_init', function () {
    if (class_exists('\Automattic\WooCommerce\Utilities\FeaturesUtil')) {
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('custom_order_tables', BDESK_FILE, true);
    }
});

add_action('admin_enqueue_scripts', function () {
    wp_enqueue_style('bdesk-admin', BDESK_URL . 'assets/admin.css', array(), BDESK_VERSION);
});

/* The screen id of the order editor, for both storage modes. */
function bdesk_order_screens() {
    $screens = array('shop_order');
    if (function_exists('wc_get_page_screen_id')) $screens[] = wc_get_page_screen_id('shop-order');
    return array_unique($screens);
}

function bdesk_money($amount, $order = null) {
    return wc_price($amount, array('currency' => $order ? $order->get_currency() : get_woocommerce_currency()));
}
