<?php
/**
 * Plugin Name:       Hadhrami Delivery
 * Description:       Own-rider delivery for Hadhrami Fashion on WooCommerce: 2-hour shirts and 24-hour tailored suits, auto-assignment to riders, rider mobile app with Google Maps navigation, live customer tracking, delivery OTP, COD cash collection and WhatsApp updates. Forked from One Love Delivery 1.2.
 * Version:           1.3.0
 * Requires at least: 6.2
 * Requires PHP:      7.4
 * Author:            Hadhrami Fashion
 * Text Domain:       hadhrami-delivery
 * WC requires at least: 7.0
 *
 * @package HadhramiDelivery
 */

defined( 'ABSPATH' ) || exit;

define( 'HFD_DELIVERY_VERSION', '1.3.0' );
define( 'HFD_DELIVERY_FILE', __FILE__ );
define( 'HFD_DELIVERY_DIR', plugin_dir_path( __FILE__ ) );
define( 'HFD_DELIVERY_URL', plugin_dir_url( __FILE__ ) );

require_once HFD_DELIVERY_DIR . 'includes/class-hfd-install.php';
require_once HFD_DELIVERY_DIR . 'includes/class-hfd-settings.php';
require_once HFD_DELIVERY_DIR . 'includes/class-hfd-geo.php';
require_once HFD_DELIVERY_DIR . 'includes/class-hfd-deliveries.php';
require_once HFD_DELIVERY_DIR . 'includes/class-hfd-whatsapp.php';
require_once HFD_DELIVERY_DIR . 'includes/class-hfd-rest.php';
require_once HFD_DELIVERY_DIR . 'includes/class-hfd-frontend.php';
require_once HFD_DELIVERY_DIR . 'includes/class-hfd-checkout.php';
require_once HFD_DELIVERY_DIR . 'includes/class-hfd-fees.php';
require_once HFD_DELIVERY_DIR . 'includes/class-hfd-promise.php';
require_once HFD_DELIVERY_DIR . 'includes/class-hfd-reports.php';
require_once HFD_DELIVERY_DIR . 'includes/class-hfd-admin.php';
require_once HFD_DELIVERY_DIR . 'includes/class-hfd-hq.php';

register_activation_hook( __FILE__, array( 'HFD_Install', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'HFD_Install', 'deactivate' ) );

// Declare compatibility with WooCommerce High-Performance Order Storage.
add_action( 'before_woocommerce_init', function () {
	if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
	}
} );

add_action( 'plugins_loaded', function () {
	HFD_Install::maybe_upgrade();

	if ( ! class_exists( 'WooCommerce' ) ) {
		add_action( 'admin_notices', function () {
			echo '<div class="notice notice-error"><p><strong>Hadhrami Delivery</strong> needs WooCommerce to be installed and active.</p></div>';
		} );
		return;
	}

	HFD_Deliveries::init();
	HFD_WhatsApp::init();
	HFD_REST::init();
	HFD_Frontend::init();
	HFD_Checkout::init();
	HFD_Fees::init();
	HFD_Promise::init();
	HFD_HQ::init();
	if ( is_admin() ) {
		HFD_Admin::init();
		HFD_Reports::init();
	}
} );
