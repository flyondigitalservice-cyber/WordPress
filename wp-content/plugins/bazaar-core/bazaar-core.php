<?php
/**
 * Plugin Name:       Bazaar Core
 * Description:       Shared store features for the Bazaar themes (Store, Electronics, Boutique): passwordless OTP login, contact form, product compare, spec filters, variation swatches, size guide, WhatsApp ordering, product-page extras, real customer reviews, starter pages and theme/plugin updates. Features switch on only when the active theme asks for them.
 * Version:           1.0.0
 * Requires at least: 6.5
 * Tested up to:      7.1
 * Requires PHP:      8.0
 * Requires Plugins:  woocommerce
 * Author:            Flyon Digital Service
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       bazaar-core
 * Domain Path:       /languages
 * Update URI:        bazaar
 *
 * @package BazaarCore
 */

defined( 'ABSPATH' ) || exit;

define( 'BAZAAR_CORE_VERSION', '1.0.0' );
define( 'BAZAAR_CORE_FILE', __FILE__ );
define( 'BAZAAR_CORE_DIR', __DIR__ );
define( 'BAZAAR_CORE_URI', plugins_url( '', __FILE__ ) );

require_once BAZAAR_CORE_DIR . '/inc/helpers.php';
require_once BAZAAR_CORE_DIR . '/inc/options.php';
require_once BAZAAR_CORE_DIR . '/inc/assets.php';
require_once BAZAAR_CORE_DIR . '/inc/otp.php';
require_once BAZAAR_CORE_DIR . '/inc/contact-form.php';
require_once BAZAAR_CORE_DIR . '/inc/whatsapp.php';
require_once BAZAAR_CORE_DIR . '/inc/starter-pages.php';
require_once BAZAAR_CORE_DIR . '/inc/updater.php';

/**
 * Translations live in /languages (bazaar-core-{locale}.mo) or wp-content/languages/plugins.
 */
function bzr_core_load_textdomain() {
	load_plugin_textdomain( 'bazaar-core', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
add_action( 'init', 'bzr_core_load_textdomain', 0 );

/**
 * WooCommerce-dependent modules load once all plugins are in, so plugin order does not matter.
 */
function bzr_core_load_woocommerce_modules() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		// Store shortcodes print nothing (instead of raw [bzr_...] text) until WooCommerce is active.
		foreach ( array( 'bzr_filters', 'bzr_compare', 'bzr_reviews' ) as $tag ) {
			add_shortcode( $tag, '__return_empty_string' );
		}
		return;
	}
	require_once BAZAAR_CORE_DIR . '/inc/woocommerce.php';
	require_once BAZAAR_CORE_DIR . '/inc/filters.php';
	require_once BAZAAR_CORE_DIR . '/inc/compare.php';
	require_once BAZAAR_CORE_DIR . '/inc/reviews.php';
}
add_action( 'plugins_loaded', 'bzr_core_load_woocommerce_modules' );

register_activation_hook( __FILE__, 'bzr_core_activate' );

/**
 * On activation, create the active theme's starter pages (if it declares any).
 * The theme's functions.php is already loaded at this point, so its filters are in place.
 */
function bzr_core_activate() {
	// Defer to the next admin load: patterns are registered on init, which has already run.
	update_option( 'bzr_create_starter_pages', 1, false );
}
