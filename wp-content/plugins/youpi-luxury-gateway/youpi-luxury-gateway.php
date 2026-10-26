<?php
/**
 * Plugin Name:       YouPI Luxury Payment Gateway
 * Plugin URI:        https://github.com/youpi-pg/YouPI-PG
 * Description:       WooCommerce UPI payment gateway for luxury watch boutiques, powered by a self-hosted YouPI PG instance. Includes high-value reconciliation holds, discreet billing and concierge support.
 * Version:           1.1.0
 * Requires at least: 6.2
 * Requires PHP:      7.4
 * Requires Plugins:  woocommerce
 * WC requires at least: 7.6
 * WC tested up to:   9.4
 * Author:            Flyon Digital Service
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       youpi-luxury-gateway
 *
 * @package YouPI_Luxury_Gateway
 */

defined( 'ABSPATH' ) || exit;

define( 'YOUPI_LUXURY_VERSION', '1.1.0' );
define( 'YOUPI_LUXURY_FILE', __FILE__ );
define( 'YOUPI_LUXURY_DIR', plugin_dir_path( __FILE__ ) );
define( 'YOUPI_LUXURY_URL', plugin_dir_url( __FILE__ ) );
define( 'YOUPI_LUXURY_GATEWAY_ID', 'youpi_luxury' );
define( 'YOUPI_LUXURY_CRON_HOOK', 'youpi_luxury_reconcile_pending' );

/**
 * Declare compatibility with WooCommerce High-Performance Order Storage and the block checkout.
 */
add_action(
	'before_woocommerce_init',
	static function () {
		if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', YOUPI_LUXURY_FILE, true );
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'cart_checkout_blocks', YOUPI_LUXURY_FILE, true );
		}
	}
);

/**
 * Boot the gateway once WooCommerce is available.
 */
function youpi_luxury_init() {
	if ( ! class_exists( 'WC_Payment_Gateway' ) ) {
		add_action( 'admin_notices', 'youpi_luxury_missing_wc_notice' );
		return;
	}

	require_once YOUPI_LUXURY_DIR . 'includes/class-youpi-luxury-api-client.php';
	require_once YOUPI_LUXURY_DIR . 'includes/class-wc-gateway-youpi-luxury.php';
	require_once YOUPI_LUXURY_DIR . 'includes/class-youpi-luxury-webhook.php';
	require_once YOUPI_LUXURY_DIR . 'includes/class-youpi-luxury-refunds.php';

	add_filter(
		'woocommerce_payment_gateways',
		static function ( $gateways ) {
			$gateways[] = 'WC_Gateway_YouPI_Luxury';
			return $gateways;
		}
	);

	add_filter( 'plugin_action_links_' . plugin_basename( YOUPI_LUXURY_FILE ), 'youpi_luxury_action_links' );

	Youpi_Luxury_Webhook::init();
	Youpi_Luxury_Refunds::init();

	add_action( YOUPI_LUXURY_CRON_HOOK, array( 'WC_Gateway_YouPI_Luxury', 'reconcile_pending_orders' ) );
}
add_action( 'plugins_loaded', 'youpi_luxury_init', 11 );

/**
 * Register the gateway with the WooCommerce block checkout.
 */
add_action(
	'woocommerce_blocks_loaded',
	static function () {
		if ( ! class_exists( \Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType::class ) ) {
			return;
		}
		require_once YOUPI_LUXURY_DIR . 'includes/class-youpi-luxury-blocks.php';
		add_action(
			'woocommerce_blocks_payment_method_type_registration',
			static function ( \Automattic\WooCommerce\Blocks\Payments\PaymentMethodRegistry $registry ) {
				$registry->register( new Youpi_Luxury_Blocks() );
			}
		);
	}
);

/**
 * Admin notice shown when WooCommerce is not active.
 */
function youpi_luxury_missing_wc_notice() {
	echo '<div class="notice notice-error"><p>' . esc_html__( 'YouPI Luxury Payment Gateway requires WooCommerce to be installed and active.', 'youpi-luxury-gateway' ) . '</p></div>';
}

/**
 * Add a "Settings" link on the Plugins screen.
 *
 * @param array $links Existing links.
 * @return array
 */
function youpi_luxury_action_links( $links ) {
	$url = admin_url( 'admin.php?page=wc-settings&tab=checkout&section=' . YOUPI_LUXURY_GATEWAY_ID );
	array_unshift( $links, '<a href="' . esc_url( $url ) . '">' . esc_html__( 'Settings', 'youpi-luxury-gateway' ) . '</a>' );
	return $links;
}

/**
 * Ten-minute schedule for the pending-order reconciliation sweep.
 */
add_filter(
	'cron_schedules',
	static function ( $schedules ) {
		$schedules['youpi_luxury_ten_minutes'] = array(
			'interval' => 10 * MINUTE_IN_SECONDS,
			'display'  => __( 'Every 10 minutes (YouPI Luxury)', 'youpi-luxury-gateway' ),
		);
		return $schedules;
	}
);

register_activation_hook(
	__FILE__,
	static function () {
		if ( ! wp_next_scheduled( YOUPI_LUXURY_CRON_HOOK ) ) {
			wp_schedule_event( time() + MINUTE_IN_SECONDS, 'youpi_luxury_ten_minutes', YOUPI_LUXURY_CRON_HOOK );
		}
	}
);

register_deactivation_hook(
	__FILE__,
	static function () {
		wp_clear_scheduled_hook( YOUPI_LUXURY_CRON_HOOK );
	}
);
