<?php
/**
 * LuxuryWatchs Signature theme functions.
 *
 * @package luxurywatchs
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'LW_VERSION', '1.0.1' );
define( 'LW_DIR', get_template_directory() );
define( 'LW_URI', get_template_directory_uri() );

require LW_DIR . '/inc/customizer.php';
require LW_DIR . '/inc/helpers.php';
require LW_DIR . '/inc/woocommerce.php';
require LW_DIR . '/inc/contact-form.php';

/**
 * Theme setup.
 */
function lw_setup() {
	load_theme_textdomain( 'luxurywatchs', LW_DIR . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 80,
			'width'       => 260,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);

	add_theme_support( 'woocommerce' );
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );

	add_image_size( 'lw-card', 600, 600, true );
	add_image_size( 'lw-hero', 1920, 1000, true );

	register_nav_menus(
		array(
			'primary' => __( 'Primary Menu', 'luxurywatchs' ),
			'footer'  => __( 'Footer Menu', 'luxurywatchs' ),
		)
	);
}
add_action( 'after_setup_theme', 'lw_setup' );

/**
 * Enqueue assets.
 */
function lw_assets() {
	wp_enqueue_style(
		'lw-fonts',
		'https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=Inter:wght@400;500;600;700&display=swap',
		array(),
		null
	);
	wp_enqueue_style( 'lw-main', LW_URI . '/assets/css/main.css', array(), LW_VERSION );
	wp_enqueue_script( 'lw-main', LW_URI . '/assets/js/main.js', array(), LW_VERSION, true );
}
add_action( 'wp_enqueue_scripts', 'lw_assets' );

/**
 * Preconnect to Google Fonts.
 *
 * @param array  $urls          URLs.
 * @param string $relation_type Relation type.
 * @return array
 */
function lw_resource_hints( $urls, $relation_type ) {
	if ( 'preconnect' === $relation_type ) {
		$urls[] = array(
			'href' => 'https://fonts.gstatic.com',
			'crossorigin',
		);
	}
	return $urls;
}
add_filter( 'wp_resource_hints', 'lw_resource_hints', 10, 2 );

/**
 * Widgets.
 */
function lw_widgets_init() {
	register_sidebar(
		array(
			'name'          => __( 'Shop Sidebar', 'luxurywatchs' ),
			'id'            => 'shop-sidebar',
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		)
	);
}
add_action( 'widgets_init', 'lw_widgets_init' );

/**
 * Organization + WebSite schema for SEO.
 */
function lw_schema() {
	if ( ! is_front_page() ) {
		return;
	}
	$schema = array(
		'@context' => 'https://schema.org',
		'@graph'   => array(
			array(
				'@type' => 'Store',
				'name'  => get_bloginfo( 'name' ),
				'url'   => home_url( '/' ),
				'telephone'       => '+' . lw_whatsapp_number(),
				'currenciesAccepted' => 'INR',
				'paymentAccepted' => 'Cash on Delivery, UPI, Credit Card, Debit Card, Net Banking',
				'areaServed'      => 'IN',
			),
			array(
				'@type'           => 'WebSite',
				'url'             => home_url( '/' ),
				'name'            => get_bloginfo( 'name' ),
				'potentialAction' => array(
					'@type'       => 'SearchAction',
					'target'      => home_url( '/?s={search_term_string}&post_type=product' ),
					'query-input' => 'required name=search_term_string',
				),
			),
		),
	);
	echo '<script type="application/ld+json">' . wp_json_encode( $schema ) . '</script>' . "\n";
}
add_action( 'wp_head', 'lw_schema' );
