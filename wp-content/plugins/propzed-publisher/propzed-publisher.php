<?php
/**
 * Plugin Name:       Propzed Publisher & Builders
 * Description:       Add-on for the Propzed theme: builder / developer brand pages that list every project, publisher accounts that can only submit properties for admin approval, app API endpoints, and a mobile, accessibility and conversion layer.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Propzed
 * License:           GPL-2.0-or-later
 * Text Domain:       propzed-publisher
 *
 * HOW IT IS BUILT
 *   includes/routing.php          /builders/ and /publisher/ URLs, settings, shared helpers
 *   includes/builders.php         "Builder" taxonomy, brand pages, homepage strip, SEO
 *   includes/publisher.php        Publisher role, sign-up, login, submit / edit / withdraw
 *   includes/publisher-admin.php  Admin review box, emails, wp-admin lock-out for publishers
 *   includes/rest.php             /wp-json/propzed/v1/builders and /publisher/* for the app
 *   includes/enhance.php          Front-end CSS/JS layer, menu links, settings screen
 *
 * SAFETY: every module is loaded only when the Propzed theme (and the theme
 * functions this plugin calls) is present. With any other theme the plugin
 * stays dormant and shows an admin notice, so it can never cause a fatal error.
 *
 * @package PropzedPublisher
 */

defined( 'ABSPATH' ) || exit;

define( 'PZP_VERSION', '1.0.0' );
define( 'PZP_DIR', plugin_dir_path( __FILE__ ) );
define( 'PZP_URL', plugin_dir_url( __FILE__ ) );

/**
 * Theme functions this plugin relies on. If one is missing, nothing is loaded.
 *
 * @return string[] Missing function names (empty = ready).
 */
function pzp_missing_theme_functions() {
	$required = array(
		'propzed_meta',
		'propzed_icon',
		'propzed_price',
		'propzed_area',
		'propzed_location_label',
		'propzed_option',
		'propzed_phone_link',
		'propzed_whatsapp_link',
		'propzed_term_select',
		'propzed_field_options',
		'propzed_all_fields',
		'propzed_sanitize_field',
		'propzed_valid_phone',
		'propzed_notify_email',
		'propzed_listing_url',
		'propzed_template_url',
		'propzed_gallery_ids',
	);
	return array_values( array_filter( $required, static function ( $fn ) {
		return ! function_exists( $fn );
	} ) );
}

/**
 * Load the modules once the theme's functions.php has run.
 */
function pzp_boot() {
	if ( pzp_missing_theme_functions() ) {
		add_action( 'admin_notices', 'pzp_dormant_notice' );
		return;
	}

	foreach ( array( 'routing', 'builders', 'publisher', 'publisher-admin', 'rest', 'enhance' ) as $module ) {
		require_once PZP_DIR . 'includes/' . $module . '.php';
	}

	/**
	 * Fires when Propzed Publisher & Builders is fully loaded.
	 */
	do_action( 'pzp_loaded' );
}
add_action( 'after_setup_theme', 'pzp_boot', 20 );

/**
 * Notice shown when the Propzed theme is not active.
 */
function pzp_dormant_notice() {
	if ( ! current_user_can( 'activate_plugins' ) ) {
		return;
	}
	echo '<div class="notice notice-warning"><p>';
	echo esc_html__( 'Propzed Publisher & Builders is inactive because the Propzed theme (version 1.1 or newer) is not the active theme. Nothing on the site has been changed.', 'propzed-publisher' );
	echo '</p></div>';
}

/**
 * Activation / deactivation: only mark the permalinks for a refresh.
 * (The refresh itself runs on the next page load, see includes/routing.php.)
 */
register_activation_hook(
	__FILE__,
	static function () {
		delete_option( 'pzp_rewrite_version' );
	}
);
register_deactivation_hook(
	__FILE__,
	static function () {
		delete_option( 'pzp_rewrite_version' );
		// Rebuilt on the next page load, by then without this plugin's URLs.
		delete_option( 'rewrite_rules' );
	}
);
