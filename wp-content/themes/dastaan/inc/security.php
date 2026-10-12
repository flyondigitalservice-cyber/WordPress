<?php
/**
 * Security hardening (theme-level, non-invasive, filterable).
 *
 * @package Dastaan
 */

defined( 'ABSPATH' ) || exit;

/* Remove fingerprinting + unused discovery endpoints. */
remove_action( 'wp_head', 'wp_generator' );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wp_shortlink_wp_head' );
remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head' );
add_filter( 'the_generator', '__return_empty_string' );

/**
 * Strip version query strings from theme/core assets (keeps our own cache-busting via DASTAAN_VERSION).
 *
 * @param string $src Source.
 * @return string
 */
function dastaan_strip_core_version( $src ) {
	if ( $src && strpos( $src, 'ver=' . get_bloginfo( 'version' ) ) !== false ) {
		$src = remove_query_arg( 'ver', $src );
	}

	return $src;
}
add_filter( 'style_loader_src', 'dastaan_strip_core_version', 999 );
add_filter( 'script_loader_src', 'dastaan_strip_core_version', 999 );

/* XML-RPC (optional). Deferred to init so option defaults (translatable) are not read too early. */
add_action(
	'init',
	function () {
		if ( ! dastaan_option( 'xmlrpc_disable' ) ) {
			return;
		}
		add_filter( 'xmlrpc_enabled', '__return_false' );
		add_filter( 'xmlrpc_methods', '__return_empty_array' );
		add_filter(
			'wp_headers',
			function ( $headers ) {
				unset( $headers['X-Pingback'] );
				return $headers;
			}
		);
		add_filter( 'pings_open', '__return_false', 20 );
	},
	1
);

/**
 * Generic login error (no username/password hints).
 */
add_filter(
	'login_errors',
	function () {
		return __( 'The details you entered are not correct.', 'dastaan' );
	}
);

/**
 * Block author enumeration (?author=1 → /author/admin/).
 */
function dastaan_block_author_enum() {
	if ( is_admin() ) {
		return;
	}
	if ( isset( $_GET['author'] ) && ! is_user_logged_in() ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		wp_safe_redirect( home_url( '/' ), 301 );
		exit;
	}
}
add_action( 'init', 'dastaan_block_author_enum' );

/**
 * Hide the REST users endpoint from anonymous visitors.
 *
 * @param array $endpoints Endpoints.
 * @return array
 */
function dastaan_rest_users( $endpoints ) {
	if ( ! is_user_logged_in() ) {
		unset( $endpoints['/wp/v2/users'], $endpoints['/wp/v2/users/(?P<id>[\d]+)'] );
	}

	return $endpoints;
}
add_filter( 'rest_endpoints', 'dastaan_rest_users' );

/**
 * Security headers (only when not already set by the server).
 */
function dastaan_security_headers() {
	if ( headers_sent() || is_admin() ) {
		return;
	}
	$headers = apply_filters(
		'dastaan_security_headers',
		array(
			'X-Content-Type-Options' => 'nosniff',
			'X-Frame-Options'        => 'SAMEORIGIN',
			'Referrer-Policy'        => 'strict-origin-when-cross-origin',
			'Permissions-Policy'     => 'camera=(), microphone=(), geolocation=(), payment=(self)',
			'X-XSS-Protection'       => '0',
		)
	);
	$existing = array_map( 'strtolower', array_keys( dastaan_headers_list() ) );
	foreach ( $headers as $k => $v ) {
		if ( ! in_array( strtolower( $k ), $existing, true ) ) {
			header( $k . ': ' . $v );
		}
	}
}
add_action( 'send_headers', 'dastaan_security_headers' );

/**
 * headers_list() as assoc array.
 *
 * @return array
 */
function dastaan_headers_list() {
	$out = array();
	foreach ( headers_list() as $h ) {
		$parts = explode( ':', $h, 2 );
		if ( 2 === count( $parts ) ) {
			$out[ trim( $parts[0] ) ] = trim( $parts[1] );
		}
	}

	return $out;
}

/**
 * Disallow risky upload types (SVG can carry scripts) unless an SVG sanitiser plugin is active.
 *
 * @param array $mimes Mimes.
 * @return array
 */
function dastaan_upload_mimes( $mimes ) {
	if ( ! class_exists( 'Safe_Svg' ) && ! defined( 'SVG_SUPPORT_VERSION' ) ) {
		unset( $mimes['svg'], $mimes['svgz'] );
	}
	unset( $mimes['exe'], $mimes['php'], $mimes['phtml'], $mimes['js'] );
	// Make sure modern video / image types are allowed for product media.
	$mimes['webp'] = 'image/webp';
	$mimes['mp4']  = 'video/mp4';
	$mimes['webm'] = 'video/webm';
	$mimes['mov']  = 'video/quicktime';

	return $mimes;
}
add_filter( 'upload_mimes', 'dastaan_upload_mimes' );

/**
 * Disable comments on media attachments (common spam vector).
 */
add_filter(
	'comments_open',
	function ( $open, $post_id ) {
		$post = get_post( $post_id );
		return ( $post && 'attachment' === $post->post_type ) ? false : $open;
	},
	10,
	2
);

/**
 * Strip HTML from comment author display names & disallow links in comment text for guests.
 */
add_filter( 'pre_comment_content', 'wp_kses_data' );

/**
 * Remove WooCommerce generator tag.
 */
add_action(
	'wp_loaded',
	function () {
		if ( dastaan_has_wc() ) {
			remove_action( 'wp_head', array( WC(), 'generator' ) );
			remove_action( 'get_the_generator_html', 'wc_generator_tag', 10 );
			remove_action( 'get_the_generator_xhtml', 'wc_generator_tag', 10 );
		}
	}
);

/**
 * Nonce-protected AJAX helper used by theme front-end actions.
 *
 * @return void
 */
function dastaan_verify_front_nonce() {
	if ( ! check_ajax_referer( 'dastaan_front', 'nonce', false ) ) {
		wp_send_json_error( array( 'message' => __( 'Session expired, please reload.', 'dastaan' ) ), 403 );
	}
}
