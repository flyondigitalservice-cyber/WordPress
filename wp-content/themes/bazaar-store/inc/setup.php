<?php
/**
 * Theme setup: supports, patterns, translations.
 *
 * @package Bazaar
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register theme supports.
 */
function bzr_theme_setup() {
	// Translations: wp-content/languages/themes/bazaar-store-{locale}.mo or this theme's /languages folder.
	load_theme_textdomain( 'bazaar-store', BZR_THEME_DIR . '/languages' );

	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'editor-styles' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'html5', array( 'comment-list', 'comment-form', 'search-form', 'gallery', 'caption', 'style', 'script' ) );

	// WooCommerce.
	add_theme_support( 'woocommerce' );
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );

	// Match the front end inside the block editor.
	add_editor_style( 'assets/css/main.css' );
	if ( file_exists( BZR_THEME_DIR . '/assets/css/theme.css' ) ) {
		add_editor_style( 'assets/css/theme.css' );
	}

	// Image sizes tuned for product cards and the single-product gallery.
	add_image_size( 'bzr-card', 600, 800, true );
}
add_action( 'after_setup_theme', 'bzr_theme_setup' );

/**
 * Register pattern categories so patterns group nicely in the inserter.
 */
function bzr_theme_pattern_categories() {
	register_block_pattern_category(
		'bazaar-store',
		array( 'label' => __( 'Store sections', 'bazaar-store' ) )
	);
	register_block_pattern_category(
		'bazaar-pages',
		array( 'label' => __( 'Store pages', 'bazaar-store' ) )
	);
}
add_action( 'init', 'bzr_theme_pattern_categories' );
