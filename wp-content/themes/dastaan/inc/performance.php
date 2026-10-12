<?php
/**
 * Performance: trim what we don't use, hint what we do.
 *
 * @package Dastaan
 */

defined( 'ABSPATH' ) || exit;

/* Emoji scripts/styles. */
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
remove_action( 'admin_print_styles', 'print_emoji_styles' );
remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
add_filter( 'emoji_svg_url', '__return_false' );

/* oEmbed discovery of *this* site by others is fine; drop the heavy embed script + REST links. */
remove_action( 'wp_head', 'wp_oembed_add_host_js' );
remove_action( 'wp_head', 'rest_output_link_wp_head' );

/**
 * Front-end dequeues.
 */
function dastaan_dequeue() {
	if ( is_admin() ) {
		return;
	}
	// Dashicons only for logged-in users (admin bar).
	if ( ! is_user_logged_in() ) {
		wp_dequeue_style( 'dashicons' );
	}
	// Classic theme styles & global block styles we override anyway.
	wp_dequeue_style( 'classic-theme-styles' );

	// jQuery Migrate (toggle).
	if ( dastaan_option( 'remove_jquery_migrate' ) ) {
		global $wp_scripts;
		if ( isset( $wp_scripts->registered['jquery'] ) ) {
			$wp_scripts->registered['jquery']->deps = array_diff( $wp_scripts->registered['jquery']->deps, array( 'jquery-migrate' ) );
		}
	}

	// WooCommerce assets only where they are needed.
	if ( dastaan_has_wc() && ! is_woocommerce() && ! is_cart() && ! is_checkout() && ! is_account_page() && ! is_front_page() ) {
		wp_dequeue_style( 'wc-blocks-style' );
		wp_dequeue_script( 'wc-cart-fragments' ); // Mini-cart still works via our own refresh on WC pages.
	}
	// WC's own gallery scripts are replaced by the theme gallery.
	if ( dastaan_has_wc() && is_product() ) {
		wp_dequeue_script( 'flexslider' );
		wp_dequeue_script( 'zoom' );
		wp_dequeue_script( 'photoswipe' );
		wp_dequeue_script( 'photoswipe-ui-default' );
		wp_dequeue_style( 'photoswipe' );
		wp_dequeue_style( 'photoswipe-default-skin' );
	}
}
add_action( 'wp_enqueue_scripts', 'dastaan_dequeue', 100 );

/**
 * Defer non-critical third-party scripts (never WooCommerce core/jQuery to keep dependency order).
 *
 * @param string $tag    Tag.
 * @param string $handle Handle.
 * @return string
 */
function dastaan_defer_scripts( $tag, $handle ) {
	$defer = apply_filters( 'dastaan_defer_handles', array( 'comment-reply', 'wp-embed' ) );
	if ( in_array( $handle, $defer, true ) && false === strpos( $tag, ' defer' ) ) {
		$tag = str_replace( ' src=', ' defer src=', $tag );
	}

	return $tag;
}
add_filter( 'script_loader_tag', 'dastaan_defer_scripts', 10, 2 );

/**
 * Heartbeat: slower on the front end.
 *
 * @param array $settings Settings.
 * @return array
 */
add_filter(
	'heartbeat_settings',
	function ( $settings ) {
		$settings['interval'] = 60;
		return $settings;
	}
);

/**
 * Lazy-load + async decoding for content images; mark the first product image on single pages as high priority.
 *
 * @param array $attr Attributes.
 * @return array
 */
function dastaan_image_attrs( $attr ) {
	if ( ! isset( $attr['decoding'] ) ) {
		$attr['decoding'] = 'async';
	}

	return $attr;
}
add_filter( 'wp_get_attachment_image_attributes', 'dastaan_image_attrs', 20 );

/**
 * Limit revisions to keep the DB lean (only if not configured).
 */
add_filter(
	'wp_revisions_to_keep',
	function ( $num ) {
		return defined( 'WP_POST_REVISIONS' ) ? $num : 10;
	}
);

/**
 * Preload fonts CSS as early as possible (already handled by preconnect) and add dns-prefetch for WhatsApp.
 */
add_action(
	'wp_head',
	function () {
		echo '<link rel="dns-prefetch" href="//wa.me">' . "\n";
	},
	2
);

/**
 * Serve WebP/AVIF uploads when the server can generate them (WP 6.1+ handles this automatically via image_editor_output_format).
 *
 * @param array $formats Formats.
 * @return array
 */
add_filter(
	'image_editor_output_format',
	function ( $formats ) {
		$formats['image/jpeg'] = 'image/webp';
		return $formats;
	}
);

/**
 * Big image threshold: products are uploaded at phone resolution; cap at 2000px.
 */
add_filter(
	'big_image_size_threshold',
	function () {
		return 2000;
	}
);

/**
 * WooCommerce ships three stylesheets. The theme fully styles layout, so drop the float-based
 * layout/smallscreen sheets and keep only the general one (forms, notices, fallbacks).
 *
 * @param array $styles Styles.
 * @return array
 */
function dastaan_wc_styles( $styles ) {
	if ( apply_filters( 'dastaan_keep_wc_styles', false ) ) {
		unset( $styles['woocommerce-layout'], $styles['woocommerce-smallscreen'] );
		return $styles;
	}
	return array(); // The theme's woocommerce.css styles every WooCommerce screen.
}
add_filter( 'woocommerce_enqueue_styles', 'dastaan_wc_styles' );
