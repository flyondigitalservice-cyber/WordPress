<?php
/**
 * Plugin Name:       Zobo Currency Switcher for WooCommerce
 * Plugin URI:        https://zobodigital.com/
 * Description:       Automatic geolocation currency switching for WooCommerce with a header / menu / footer / floating currency dropdown, live exchange rates, per-currency rounding, shipping & coupon conversion and payment-gateway rules.
 * Version:           1.1.0
 * Author:            Zobo Digital
 * Author URI:        https://zobodigital.com/
 * Text Domain:       zobo-currency-switcher
 * Domain Path:       /languages
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Requires Plugins:  woocommerce
 * WC requires at least: 7.0
 * WC tested up to:   10.9
 * License:           Proprietary
 * License URI:       https://zobodigital.com/
 *
 * Copyright (c) Zobo Digital. All rights reserved.
 * Licensed for use only on the domains Zobo Digital has authorised. Copying, redistributing
 * or using this plugin on any other site, or removing the licence check, is not permitted.
 */

defined( 'ABSPATH' ) || exit;

define( 'ZCS_VERSION', '1.1.0' );
define( 'ZCS_FILE', __FILE__ );
define( 'ZCS_PATH', plugin_dir_path( __FILE__ ) );
define( 'ZCS_URL', plugin_dir_url( __FILE__ ) );

require_once ZCS_PATH . 'includes/class-zcs-license.php';
require_once ZCS_PATH . 'includes/class-zcs-currencies.php';
require_once ZCS_PATH . 'includes/class-zcs-settings.php';
require_once ZCS_PATH . 'includes/class-zcs-rates.php';
require_once ZCS_PATH . 'includes/class-zcs-manager.php';
require_once ZCS_PATH . 'includes/class-zcs-pricing.php';
require_once ZCS_PATH . 'includes/class-zcs-frontend.php';
require_once ZCS_PATH . 'includes/class-zcs-admin.php';

// Declare HPOS + Cart/Checkout Blocks compatibility.
add_action(
	'before_woocommerce_init',
	function () {
		if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', ZCS_FILE, true );
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'cart_checkout_blocks', ZCS_FILE, true );
		}
	}
);

register_activation_hook( __FILE__, 'zcs_activate' );
register_deactivation_hook( __FILE__, array( 'ZCS_Rates', 'deactivate' ) );

/**
 * Activation: refuse on unlicensed domains.
 */
function zcs_activate() {
	if ( ! ZCS_License::is_licensed() ) {
		deactivate_plugins( plugin_basename( ZCS_FILE ) );
		wp_die( esc_html( ZCS_License::message() ), esc_html__( 'Plugin not licensed', 'zobo-currency-switcher' ), array( 'back_link' => true ) );
	}
	ZCS_Rates::activate();
}

add_action( 'plugins_loaded', 'zcs_boot', 20 );

/**
 * Boot the plugin once WooCommerce is available.
 */
function zcs_boot() {
	load_plugin_textdomain( 'zobo-currency-switcher', false, dirname( plugin_basename( ZCS_FILE ) ) . '/languages' );

	if ( ! ZCS_License::is_licensed() ) {
		add_action( 'admin_notices', array( 'ZCS_License', 'notice' ) );
		return;
	}

	if ( ! class_exists( 'WooCommerce' ) ) {
		add_action(
			'admin_notices',
			function () {
				echo '<div class="notice notice-error"><p>' . esc_html__( 'Zobo Currency Switcher requires WooCommerce to be installed and active.', 'zobo-currency-switcher' ) . '</p></div>';
			}
		);
		return;
	}

	require_once ZCS_PATH . 'includes/class-zcs-widget.php';

	ZCS_Rates::init();
	ZCS_Manager::instance()->init();
	( new ZCS_Pricing( ZCS_Manager::instance() ) )->init();
	( new ZCS_Frontend( ZCS_Manager::instance() ) )->init();

	if ( is_admin() ) {
		( new ZCS_Admin() )->init();
	}
}

/**
 * Public helper: the currency prices are currently shown in.
 *
 * @return string
 */
function zcs_get_current_currency() {
	return class_exists( 'ZCS_Manager' ) && ZCS_License::is_licensed() ? ZCS_Manager::instance()->get_current() : get_option( 'woocommerce_currency' );
}

/**
 * Public helper: convert an amount from the store base currency into the current (or given) currency.
 *
 * @param float|string $amount Amount in base currency.
 * @param string|null  $to     Target currency code. Defaults to current currency.
 * @return float
 */
function zcs_convert( $amount, $to = null ) {
	if ( ! ZCS_License::is_licensed() ) {
		return (float) $amount;
	}
	$to   = $to ? strtoupper( $to ) : zcs_get_current_currency();
	$rate = ZCS_Rates::get_rate( $to );
	return $rate > 0 ? (float) $amount * $rate : (float) $amount;
}
