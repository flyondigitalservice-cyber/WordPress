<?php
/**
 * Baystore Amazon Lean — Theme Functions & Setup
 *
 * @package Baystore_Amazon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'BAYSTORE_THEME_VERSION', '1.3.1' );

add_action( 'after_setup_theme', function () {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'custom-logo' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption' ) );

	// WooCommerce Amazon-grade support.
	add_theme_support( 'woocommerce' );
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );

	register_nav_menus( array(
		'primary' => __( 'Primary Department Nav', 'baystore-amazon' ),
		'footer'  => __( 'Footer Quick Links', 'baystore-amazon' ),
	) );
} );

add_action( 'wp_enqueue_scripts', function () {
	wp_enqueue_style(
		'baystore-inter-font',
		'https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap',
		array(),
		null
	);
	wp_enqueue_style(
		'baystore-style',
		get_stylesheet_uri(),
		array(),
		BAYSTORE_THEME_VERSION
	);

	wp_enqueue_style(
		'baystore-store',
		get_template_directory_uri() . '/assets/css/baystore-store.css',
		array( 'baystore-style' ),
		BAYSTORE_THEME_VERSION
	);
	wp_enqueue_script(
		'baystore-store-js',
		get_template_directory_uri() . '/assets/js/baystore-store.js',
		array(),
		BAYSTORE_THEME_VERSION,
		true
	);

	wp_enqueue_script(
		'baystore-lean-js',
		get_template_directory_uri() . '/assets/js/baystore-lean.js',
		array(),
		BAYSTORE_THEME_VERSION,
		true
	);

	wp_localize_script( 'baystore-lean-js', 'baystoreLeanConfig', array(
		'ajaxUrl'       => admin_url( 'admin-ajax.php' ),
		'nonce'         => wp_create_nonce( 'baystore_lean_nonce' ),
		'freeThreshold' => (int) get_theme_mod( 'baystore_free_shipping_threshold', 499 ),
		'currency'      => function_exists( 'get_woocommerce_currency_symbol' ) ? get_woocommerce_currency_symbol() : '₹',
		'checkoutUrl'   => function_exists( 'wc_get_checkout_url' ) ? wc_get_checkout_url() : home_url( '/checkout/' ),
	) );
} );

/**
 * Helper: Load bundled Baystore seed catalog (used as fallback preview if WooCommerce has 0 products yet).
 */
function baystore_get_seed_catalog() {
	static $cache = null;
	if ( null !== $cache ) {
		return $cache;
	}
	$path = get_template_directory() . '/inc/catalog-seed.json';
	if ( ! file_exists( $path ) ) {
		return array();
	}
	$data  = json_decode( file_get_contents( $path ), true );
	$cache = is_array( $data ) ? $data : array();
	return $cache;
}

/**
 * Helper: Format INR neatly.
 */
function baystore_format_inr( $amount ) {
	return '₹' . number_format( (float) $amount, 0, '.', ',' );
}

/**
 * Helper: Product departments (WooCommerce categories, minus Uncategorized), largest first.
 */
function baystore_get_departments( $limit = 8 ) {
	if ( ! taxonomy_exists( 'product_cat' ) ) {
		return array();
	}
	$terms = get_terms( array(
		'taxonomy'   => 'product_cat',
		'hide_empty' => true,
		'number'     => $limit,
		'orderby'    => 'count',
		'order'      => 'DESC',
		'exclude'    => array( (int) get_option( 'default_product_cat' ) ),
	) );
	return is_wp_error( $terms ) ? array() : $terms;
}

/**
 * Helper: Short nav label for a department ("Home & Kitchen" stays, "Auréveda & Superfoods" -> "Superfoods").
 */
function baystore_short_cat_name( $name ) {
	$name = html_entity_decode( $name );
	foreach ( baystore_aisles() as $a ) {
		if ( $a['cat'] === $name ) {
			return $a['name'];
		}
	}
	return $name;
}

/**
 * Helper: How Baystore talks about each department ("aisle").
 * Keyed by product_cat slug.
 */
function baystore_aisles() {
	return array(
		'home-kitchen'           => array(
			'cat'  => 'Home & Kitchen',
			'name' => 'Kitchen & Home',
			'quad' => 'Fix the little things',
			'line' => 'Bottles that don’t leak, a grinder for your masala, a trap for monsoon mosquitoes — small fixes, fairly priced.',
		),
		'aureveda-superfoods'    => array(
			'cat'  => 'Auréveda & Superfoods',
			'name' => 'Pantry & Bath',
			'quad' => 'Start your mornings right',
			'line' => 'Seeds for your breakfast bowl and herbal bars for your bath. Simple, everyday, fairly priced.',
		),
		'personal-care-wellness' => array(
			'cat'  => 'Personal Care & Wellness',
			'name' => 'Self-care',
			'quad' => 'Look after yourself',
			'line' => 'Massage oils, facial kits and herbal remedies — the kind of things your family already keeps at home.',
		),
		'electronics-gadgets'    => array(
			'cat'  => 'Electronics & Gadgets',
			'name' => 'Room Glow-ups',
			'quad' => 'Give your room a mood',
			'line' => 'Galaxy projectors, LED strips and speakers that give any room a mood — for movie nights, parties or just winding down.',
		),
		'festive-handmade-decor' => array(
			'cat'  => 'Festive & Handmade Decor',
			'name' => 'Festive & Gifting',
			'quad' => 'Ready for the next festival',
			'line' => 'Handcrafted wall art, torans and lights for festivals, housewarmings and the gift you forgot to buy.',
		),
	);
}

function baystore_aisle( $slug ) {
	$all = baystore_aisles();
	return isset( $all[ $slug ] ) ? $all[ $slug ] : null;
}

/**
 * Helper: Image for a department — its thumbnail if set, else the first product image in it.
 */
function baystore_cat_image( $term ) {
	$thumb_id = (int) get_term_meta( $term->term_id, 'thumbnail_id', true );
	if ( $thumb_id ) {
		return wp_get_attachment_image_url( $thumb_id, 'woocommerce_thumbnail' );
	}
	if ( ! function_exists( 'wc_get_products' ) ) {
		return '';
	}
	foreach ( wc_get_products( array( 'status' => 'publish', 'limit' => 6, 'category' => array( $term->slug ) ) ) as $p ) {
		$url = get_the_post_thumbnail_url( $p->get_id(), 'woocommerce_thumbnail' ) ?: get_post_meta( $p->get_id(), '_bs_ext_image', true );
		if ( $url ) {
			return $url;
		}
	}
	return '';
}

/**
 * Helper: Fetch published products, optionally by category slug.
 */
function baystore_products( $cat = '', $limit = 8, $args = array() ) {
	if ( ! function_exists( 'wc_get_products' ) ) {
		return array();
	}
	$q = array_merge( array( 'status' => 'publish', 'limit' => $limit ), $args );
	if ( $cat ) {
		$q['category'] = array( $cat );
	}
	return wc_get_products( $q );
}

require_once get_template_directory() . '/inc/customizer.php';
require_once get_template_directory() . '/inc/woocommerce-hooks.php';
require_once get_template_directory() . '/inc/importer.php';
