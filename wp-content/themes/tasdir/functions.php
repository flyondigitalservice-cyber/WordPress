<?php
/**
 * Tasdir Export theme bootstrap.
 *
 * @package Tasdir
 */

defined( 'ABSPATH' ) || exit;

define( 'TASDIR_VERSION', '1.0.0' );
define( 'TASDIR_DIR', get_template_directory() );
define( 'TASDIR_URI', get_template_directory_uri() );

require TASDIR_DIR . '/inc/helpers.php';
require TASDIR_DIR . '/inc/icons.php';
require TASDIR_DIR . '/inc/customizer.php';
require TASDIR_DIR . '/inc/products.php';
require TASDIR_DIR . '/inc/leads.php';
require TASDIR_DIR . '/inc/tracking.php';
require TASDIR_DIR . '/inc/seo.php';
require TASDIR_DIR . '/inc/performance.php';
require TASDIR_DIR . '/inc/setup-content.php';

/**
 * Theme supports & menus.
 */
function tasdir_setup() {
	load_theme_textdomain( 'tasdir', TASDIR_DIR . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'editor-styles' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' ) );
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 104,
			'width'       => 320,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);

	add_image_size( 'tasdir-card', 768, 480, true );
	add_image_size( 'tasdir-wide', 1376, 768, true );

	register_nav_menus(
		array(
			'primary' => __( 'Primary menu', 'tasdir' ),
			'footer'  => __( 'Footer menu', 'tasdir' ),
		)
	);
}
add_action( 'after_setup_theme', 'tasdir_setup' );

/**
 * Front-end assets: one CSS file, one deferred JS file. No jQuery.
 */
function tasdir_assets() {
	$css = TASDIR_DIR . '/assets/css/main.css';
	$js  = TASDIR_DIR . '/assets/js/main.js';

	wp_enqueue_style( 'tasdir', TASDIR_URI . '/assets/css/main.css', array(), TASDIR_VERSION . '.' . filemtime( $css ) );

	wp_enqueue_script(
		'tasdir',
		TASDIR_URI . '/assets/js/main.js',
		array(),
		TASDIR_VERSION . '.' . filemtime( $js ),
		array(
			'strategy'  => 'defer',
			'in_footer' => true,
		)
	);

	wp_localize_script(
		'tasdir',
		'TASDIR',
		array(
			'ajax'     => admin_url( 'admin-ajax.php' ),
			'waNumber' => tasdir_wa_number(),
			'currency' => 'USD',
			'i18n'     => array(
				'sending' => __( 'Opening WhatsApp…', 'tasdir' ),
				'ok'      => __( 'Thank you! Your enquiry is saved. We are opening WhatsApp so you can send it to our export desk.', 'tasdir' ),
				'okNoWa'  => __( 'Thank you! Your enquiry has been received. Our export team will contact you shortly.', 'tasdir' ),
				'err'     => __( 'Sorry, something went wrong. Please try again or message us on WhatsApp directly.', 'tasdir' ),
				'invalid' => __( 'Please fill in your name and a valid phone/WhatsApp number.', 'tasdir' ),
			),
		)
	);

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'tasdir_assets' );

/**
 * Preload the LCP hero image on the front page.
 */
function tasdir_preload_hero() {
	if ( ! is_front_page() || (int) tasdir_opt( 'hero_image' ) ) {
		return; // A custom hero image is printed with fetchpriority="high" instead.
	}
	printf(
		'<link rel="preload" as="image" href="%1$s" imagesrcset="%2$s" imagesizes="100vw" fetchpriority="high">' . "\n",
		esc_url( tasdir_hero_image_url( 1376 ) ),
		esc_attr( tasdir_hero_image_url( 768 ) . ' 768w, ' . tasdir_hero_image_url( 1376 ) . ' 1376w' )
	);
}
add_action( 'wp_head', 'tasdir_preload_hero', 2 );

/**
 * Widgets (footer column).
 */
function tasdir_widgets() {
	register_sidebar(
		array(
			'name'          => __( 'Footer extra', 'tasdir' ),
			'id'            => 'footer-extra',
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h2>',
			'after_title'   => '</h2>',
		)
	);
}
add_action( 'widgets_init', 'tasdir_widgets' );

/**
 * Excerpt length for cards.
 */
add_filter(
	'excerpt_length',
	static function () {
		return 24;
	}
);
