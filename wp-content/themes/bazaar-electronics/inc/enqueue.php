<?php
/**
 * Theme styles. Component styles and scripts come from the Bazaar Core plugin.
 *
 * @package Bazaar
 */

defined( 'ABSPATH' ) || exit;

/**
 * Enqueue theme styles, versioned by theme version + file mtime for cache busting.
 */
function bzr_theme_enqueue_assets() {
	$version = wp_get_theme( get_template() )->get( 'Version' );

	$css = BZR_THEME_DIR . '/assets/css/main.css';
	wp_enqueue_style( 'bzr-main', BZR_THEME_URI . '/assets/css/main.css', array(), $version . '.' . filemtime( $css ) );

	$theme_css = BZR_THEME_DIR . '/assets/css/theme.css';
	if ( file_exists( $theme_css ) ) {
		wp_enqueue_style( 'bzr-theme', BZR_THEME_URI . '/assets/css/theme.css', array( 'bzr-main' ), $version . '.' . filemtime( $theme_css ) );
	}
}
add_action( 'wp_enqueue_scripts', 'bzr_theme_enqueue_assets' );
