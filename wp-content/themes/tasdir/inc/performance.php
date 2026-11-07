<?php
/**
 * Speed: strip WordPress front-end bloat the theme does not need.
 *
 * @package Tasdir
 */

defined( 'ABSPATH' ) || exit;

/**
 * Remove emoji, oEmbed discovery, RSD, shortlink and generator tags.
 */
function tasdir_cleanup_head() {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
	remove_action( 'wp_head', 'wp_generator' );
	remove_action( 'wp_head', 'rsd_link' );
	remove_action( 'wp_head', 'wlwmanifest_link' );
	remove_action( 'wp_head', 'wp_shortlink_wp_head' );
	remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
	remove_action( 'wp_head', 'wp_oembed_add_host_js' );
	add_filter( 'emoji_svg_url', '__return_false' );
}
add_action( 'init', 'tasdir_cleanup_head' );

/**
 * Drop block CSS on templates that never render block content (home & product archive).
 */
function tasdir_trim_styles() {
	if ( is_front_page() || is_post_type_archive( 'tasdir_product' ) ) {
		wp_dequeue_style( 'wp-block-library' );
		wp_dequeue_style( 'wp-block-library-theme' );
		wp_dequeue_style( 'global-styles' );
		wp_dequeue_style( 'classic-theme-styles' );
	}
	wp_dequeue_script( 'wp-embed' );
}
add_action( 'wp_enqueue_scripts', 'tasdir_trim_styles', 100 );

/**
 * Only load block styles for blocks actually used on the page.
 */
add_filter( 'should_load_separate_core_block_assets', '__return_true' );

/**
 * Mark the first content image as high priority (better LCP on posts/pages).
 */
add_filter(
	'wp_min_priority_img_pixels',
	static function () {
		return 30000;
	}
);
