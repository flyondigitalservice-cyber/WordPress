<?php
/**
 * Plugin Name:       Invoice & Packing Slip Maker for WooCommerce
 * Description:       Create invoices and packing slips for WooCommerce orders. Email invoices to customers as PDF attachments, print them, export them as PDF or CSV, and print packing slips and shipping labels to stick on parcels.
 * Version:           1.2.0
 * Requires at least: 6.2
 * Requires PHP:      7.4
 * Requires Plugins:  woocommerce
 * WC requires at least: 7.6
 * WC tested up to:   9.4
 * Author:            Flyon Digital Service
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       invoice-packing-slip-maker
 *
 * @package Invoice_Packing_Slip_Maker
 */

defined( 'ABSPATH' ) || exit;

define( 'IPSM_VERSION', '1.2.0' );
define( 'IPSM_FILE', __FILE__ );
define( 'IPSM_DIR', plugin_dir_path( __FILE__ ) );
define( 'IPSM_URL', plugin_dir_url( __FILE__ ) );

/**
 * Declare compatibility with WooCommerce High-Performance Order Storage.
 */
add_action(
	'before_woocommerce_init',
	static function () {
		if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', IPSM_FILE, true );
		}
	}
);

/**
 * Boot the plugin once WooCommerce is loaded.
 */
function ipsm_init() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		add_action(
			'admin_notices',
			static function () {
				echo '<div class="notice notice-error"><p>' . esc_html__( 'Invoice & Packing Slip Maker requires WooCommerce to be installed and active.', 'invoice-packing-slip-maker' ) . '</p></div>';
			}
		);
		return;
	}

	require_once IPSM_DIR . 'includes/class-ipsm-settings.php';
	require_once IPSM_DIR . 'includes/class-ipsm-document.php';
	require_once IPSM_DIR . 'includes/class-ipsm-pdf-writer.php';
	require_once IPSM_DIR . 'includes/class-ipsm-barcode.php';
	require_once IPSM_DIR . 'includes/class-ipsm-pdf.php';
	require_once IPSM_DIR . 'includes/class-ipsm-renderer.php';
	require_once IPSM_DIR . 'includes/class-ipsm-export.php';
	require_once IPSM_DIR . 'includes/class-ipsm-mailer.php';
	require_once IPSM_DIR . 'includes/class-ipsm-admin.php';
	require_once IPSM_DIR . 'includes/class-ipsm-my-account.php';

	IPSM_Settings::init();
	IPSM_Renderer::init();
	IPSM_Mailer::init();
	IPSM_Admin::init();
	IPSM_My_Account::init();
}
add_action( 'plugins_loaded', 'ipsm_init', 20 );

/**
 * Settings link on the Plugins screen.
 *
 * @param string[] $links Existing links.
 * @return string[]
 */
function ipsm_action_links( $links ) {
	array_unshift(
		$links,
		'<a href="' . esc_url( admin_url( 'admin.php?page=ipsm-settings' ) ) . '">' . esc_html__( 'Settings', 'invoice-packing-slip-maker' ) . '</a>'
	);
	return $links;
}
add_filter( 'plugin_action_links_' . plugin_basename( IPSM_FILE ), 'ipsm_action_links' );
