<?php
/**
 * Front-end assets for the shared features. Theme styling stays in the theme.
 *
 * @package BazaarCore
 */

defined( 'ABSPATH' ) || exit;

/**
 * Cache-busting version for a plugin file: plugin version + file mtime.
 *
 * @param string $relative Path relative to the plugin folder.
 * @return string
 */
function bzr_asset_version( $relative ) {
	$file = BAZAAR_CORE_DIR . '/' . ltrim( $relative, '/' );
	return BAZAAR_CORE_VERSION . ( file_exists( $file ) ? '.' . filemtime( $file ) : '' );
}

/**
 * Is any Bazaar feature switched on by the active theme?
 *
 * @return bool
 */
function bzr_any_feature() {
	foreach ( array( 'swatches', 'size-guide', 'whatsapp', 'pdp-extras', 'sticky-atc', 'otp', 'contact-form', 'filters', 'compare', 'countdown', 'emi', 'reviews' ) as $feature ) {
		if ( bzr_feature( $feature ) ) {
			return true;
		}
	}
	return false;
}

/**
 * Enqueue the component stylesheet and behaviour script.
 */
function bzr_core_enqueue_assets() {
	if ( ! bzr_any_feature() ) {
		return;
	}

	wp_enqueue_style( 'bzr-core', BAZAAR_CORE_URI . '/assets/css/bazaar-core.css', array(), bzr_asset_version( 'assets/css/bazaar-core.css' ) );

	wp_enqueue_script(
		'bzr-core',
		BAZAAR_CORE_URI . '/assets/js/core.js',
		array(),
		bzr_asset_version( 'assets/js/core.js' ),
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		)
	);
}
// Priority 5 so the theme stylesheet (default priority 10) loads after and can override.
add_action( 'wp_enqueue_scripts', 'bzr_core_enqueue_assets', 5 );
