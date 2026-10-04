<?php
/**
 * Theme setup: supports, menus, image sizes, widget areas.
 *
 * @package FlavorKit
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register theme features.
 */
function flavorkit_setup() {
	load_theme_textdomain( 'flavorkit', FLAVORKIT_DIR . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'editor-styles' );
	add_theme_support( 'customize-selective-refresh-widgets' );
	add_theme_support(
		'html5',
		array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' )
	);
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 80,
			'width'       => 240,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);

	// WooCommerce.
	add_theme_support(
		'woocommerce',
		array(
			'thumbnail_image_width' => 600,
			'single_image_width'    => 900,
			'product_grid'          => array(
				'default_rows'    => 3,
				'min_rows'        => 1,
				'default_columns' => 4,
				'min_columns'     => 2,
				'max_columns'     => 5,
			),
		)
	);
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );

	add_editor_style( 'assets/css/editor.css' );

	add_image_size( 'flavorkit-card', 640, 640, true );
	add_image_size( 'flavorkit-wide', 1400, 900, true );

	register_nav_menus(
		array(
			'primary' => __( 'Primary menu', 'flavorkit' ),
			'footer'  => __( 'Footer — Quick links', 'flavorkit' ),
			'help'    => __( 'Footer — Help', 'flavorkit' ),
		)
	);
}
add_action( 'after_setup_theme', 'flavorkit_setup' );

/**
 * Content width for embeds.
 */
function flavorkit_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'flavorkit_content_width', 860 );
}
add_action( 'after_setup_theme', 'flavorkit_content_width', 0 );

/**
 * Widget areas.
 */
function flavorkit_widgets_init() {
	register_sidebar(
		array(
			'name'          => __( 'Blog sidebar', 'flavorkit' ),
			'id'            => 'sidebar-1',
			'description'   => __( 'Shown on blog posts and archives.', 'flavorkit' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
	register_sidebar(
		array(
			'name'          => __( 'Shop filters', 'flavorkit' ),
			'id'            => 'shop-filters',
			'description'   => __( 'Optional filters shown above the product grid in the shop.', 'flavorkit' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
}
add_action( 'widgets_init', 'flavorkit_widgets_init' );

/**
 * Body classes.
 *
 * @param string[] $classes Classes.
 * @return string[]
 */
function flavorkit_body_classes( $classes ) {
	$classes[] = 'fk-buttons-' . sanitize_html_class( flavorkit_mod( 'button_style' ) );
	if ( is_front_page() ) {
		$classes[] = 'fk-home';
	}
	return $classes;
}
add_filter( 'body_class', 'flavorkit_body_classes' );

/**
 * Fallback for the primary menu when no menu is assigned.
 */
function flavorkit_menu_fallback() {
	$items = array(
		home_url( '/' ) => __( 'Home', 'flavorkit' ),
	);
	if ( function_exists( 'wc_get_page_permalink' ) ) {
		$items[ wc_get_page_permalink( 'shop' ) ] = __( 'Shop all', 'flavorkit' );
	}
	$items[ home_url( '/#story' ) ]   = __( 'Our story', 'flavorkit' );
	$items[ home_url( '/#reviews' ) ] = __( 'Reviews', 'flavorkit' );
	$items[ home_url( '/#faq' ) ]     = __( 'FAQ', 'flavorkit' );

	echo '<ul class="fk-menu">';
	foreach ( $items as $url => $label ) {
		printf( '<li class="menu-item"><a href="%s">%s</a></li>', esc_url( $url ), esc_html( $label ) );
	}
	echo '</ul>';
}
