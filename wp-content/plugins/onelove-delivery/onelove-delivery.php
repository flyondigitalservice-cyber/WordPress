<?php
/**
 * Plugin Name:       One Love Delivery
 * Description:       Last-mile delivery for WooCommerce: auto-assigns orders to your own riders, rider mobile app with Google Maps navigation, live customer tracking, COD cash collection and WhatsApp updates.
 * Version:           1.1.0
 * Requires at least: 6.2
 * Requires PHP:      7.4
 * Author:            One Love Energy
 * Text Domain:       onelove-delivery
 * WC requires at least: 7.0
 *
 * @package OneLoveDelivery
 */

defined( 'ABSPATH' ) || exit;

define( 'OLE_DELIVERY_VERSION', '1.1.0' );
define( 'OLE_DELIVERY_FILE', __FILE__ );
define( 'OLE_DELIVERY_DIR', plugin_dir_path( __FILE__ ) );
define( 'OLE_DELIVERY_URL', plugin_dir_url( __FILE__ ) );

require_once OLE_DELIVERY_DIR . 'includes/class-ole-install.php';
require_once OLE_DELIVERY_DIR . 'includes/class-ole-settings.php';
require_once OLE_DELIVERY_DIR . 'includes/class-ole-geo.php';
require_once OLE_DELIVERY_DIR . 'includes/class-ole-deliveries.php';
require_once OLE_DELIVERY_DIR . 'includes/class-ole-whatsapp.php';
require_once OLE_DELIVERY_DIR . 'includes/class-ole-rest.php';
require_once OLE_DELIVERY_DIR . 'includes/class-ole-frontend.php';
require_once OLE_DELIVERY_DIR . 'includes/class-ole-checkout.php';
require_once OLE_DELIVERY_DIR . 'includes/class-ole-fees.php';
require_once OLE_DELIVERY_DIR . 'includes/class-ole-reports.php';
require_once OLE_DELIVERY_DIR . 'includes/class-ole-admin.php';

register_activation_hook( __FILE__, array( 'OLE_Install', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'OLE_Install', 'deactivate' ) );

// Declare compatibility with WooCommerce High-Performance Order Storage.
add_action( 'before_woocommerce_init', function () {
	if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
	}
} );

add_action( 'plugins_loaded', function () {
	OLE_Install::maybe_upgrade();

	if ( ! class_exists( 'WooCommerce' ) ) {
		add_action( 'admin_notices', function () {
			echo '<div class="notice notice-error"><p><strong>One Love Delivery</strong> needs WooCommerce to be installed and active.</p></div>';
		} );
		return;
	}

	OLE_Deliveries::init();
	OLE_WhatsApp::init();
	OLE_REST::init();
	OLE_Frontend::init();
	OLE_Checkout::init();
	OLE_Fees::init();
	if ( is_admin() ) {
		OLE_Admin::init();
		OLE_Reports::init();
	}
} );
