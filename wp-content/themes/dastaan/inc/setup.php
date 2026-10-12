<?php
/**
 * Theme setup: supports, menus, image sizes, sidebars.
 *
 * @package Dastaan
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register theme supports.
 */
function dastaan_setup() {
	load_theme_textdomain( 'dastaan', DASTAAN_DIR . 'languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'editor-styles' );
	add_editor_style( 'assets/css/editor.css' );
	add_theme_support( 'customize-selective-refresh-widgets' );
	add_theme_support(
		'html5',
		array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' )
	);
	add_theme_support(
		'custom-logo',
		array(
			'height'               => 80,
			'width'                => 320,
			'flex-height'          => true,
			'flex-width'           => true,
			'unlink-homepage-logo' => true,
		)
	);

	register_nav_menus(
		array(
			'primary'     => __( 'Primary Menu (header)', 'dastaan' ),
			'mobile'      => __( 'Mobile Menu (falls back to Primary)', 'dastaan' ),
			'footer_shop' => __( 'Footer — Shop column', 'dastaan' ),
			'footer_help' => __( 'Footer — Help column', 'dastaan' ),
			'footer_studio' => __( 'Footer — Studio column', 'dastaan' ),
			'legal'       => __( 'Footer — Legal links', 'dastaan' ),
		)
	);

	// Image sizes tuned for a 3:4 fashion catalogue.
	add_image_size( 'dastaan-card', 600, 800, true );      // Product cards (3:4).
	add_image_size( 'dastaan-card-2x', 900, 1200, true );  // Retina cards.
	add_image_size( 'dastaan-circle', 300, 300, true );    // Category circles.
	add_image_size( 'dastaan-tile', 800, 1000, true );     // Banners / craft tiles (4:5).
	add_image_size( 'dastaan-hero', 1920, 1000, true );    // Hero slides.
	add_image_size( 'dastaan-hero-mobile', 900, 1200, true );
	add_image_size( 'dastaan-square', 700, 700, true );    // Instagram grid.

	// WooCommerce.
	add_theme_support(
		'woocommerce',
		array(
			'thumbnail_image_width' => 600,
			'single_image_width'    => 1000,
			'product_grid'          => array(
				'default_rows'    => 3,
				'min_rows'        => 1,
				'default_columns' => 4,
				'min_columns'     => 2,
				'max_columns'     => 5,
			),
		)
	);
	// The theme ships its own gallery (zoom, lightbox, video). WC's flexslider/photoswipe are intentionally not enabled.

	$GLOBALS['content_width'] = (int) dastaan_option( 'container_width' );
}
add_action( 'after_setup_theme', 'dastaan_setup' );

/**
 * Widget areas.
 */
function dastaan_widgets_init() {
	$areas = array(
		'shop-sidebar'  => array( __( 'Shop Filters (off-canvas)', 'dastaan' ), __( 'Filters shown in the shop drawer. Add WooCommerce filter widgets here.', 'dastaan' ) ),
		'footer-extra'  => array( __( 'Footer — Extra column', 'dastaan' ), __( 'Optional 5th footer column (e.g. a map or badges).', 'dastaan' ) ),
		'product-after' => array( __( 'Product page — after summary', 'dastaan' ), __( 'Shown under the add-to-cart area on product pages.', 'dastaan' ) ),
	);
	foreach ( $areas as $id => $labels ) {
		register_sidebar(
			array(
				'name'          => $labels[0],
				'id'            => $id,
				'description'   => $labels[1],
				'before_widget' => '<section id="%1$s" class="widget %2$s">',
				'after_widget'  => '</section>',
				'before_title'  => '<h3 class="widget-title">',
				'after_title'   => '</h3>',
			)
		);
	}
}
add_action( 'widgets_init', 'dastaan_widgets_init' );

/**
 * Body classes for layout hooks.
 *
 * @param array $classes Classes.
 * @return array
 */
function dastaan_body_classes( $classes ) {
	$classes[] = 'dastaan';
	if ( dastaan_option( 'header_sticky' ) ) {
		$classes[] = 'has-sticky-header';
	}
	if ( dastaan_has_wc() && ( is_checkout() && ! is_order_received_page() ) ) {
		$classes[] = 'is-checkout-focus';
	}
	if ( is_front_page() ) {
		$classes[] = 'is-home';
	}

	return $classes;
}
add_filter( 'body_class', 'dastaan_body_classes' );

/**
 * Pingback header for singular posts.
 */
function dastaan_pingback_header() {
	if ( is_singular() && pings_open() ) {
		printf( '<link rel="pingback" href="%s">' . "\n", esc_url( get_bloginfo( 'pingback_url' ) ) );
	}
}
add_action( 'wp_head', 'dastaan_pingback_header' );

/**
 * Register block patterns category.
 */
function dastaan_block_pattern_category() {
	if ( function_exists( 'register_block_pattern_category' ) ) {
		register_block_pattern_category( 'dastaan', array( 'label' => __( 'Dastaan', 'dastaan' ) ) );
	}
}
add_action( 'init', 'dastaan_block_pattern_category' );

/**
 * Excerpt length / more.
 */
add_filter(
	'excerpt_length',
	function () {
		return 24;
	},
	999
);
add_filter(
	'excerpt_more',
	function () {
		return '&hellip;';
	}
);
