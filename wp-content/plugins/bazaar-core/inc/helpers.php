<?php
/**
 * Shared helpers: client IP detection (proxy/CDN aware) and rate limiting.
 *
 * @package BazaarCore
 */

defined( 'ABSPATH' ) || exit;

/**
 * Whether the active theme opted in to a Bazaar feature, e.g. bzr_feature( 'otp' ).
 *
 * @param string $name Feature name without the "bazaar-" prefix.
 * @return bool
 */
function bzr_feature( $name ) {
	return current_theme_supports( 'bazaar-' . $name );
}

/**
 * Proxy headers the site owner can choose from in Store Options.
 *
 * @return array<string,array{0:string,1:string}> key => [ $_SERVER key, label ].
 */
function bzr_proxy_headers() {
	return array(
		''     => array( '', __( 'None - visitors connect directly (default)', 'bazaar-core' ) ),
		'cf'   => array( 'HTTP_CF_CONNECTING_IP', __( 'Cloudflare (CF-Connecting-IP)', 'bazaar-core' ) ),
		'xff'  => array( 'HTTP_X_FORWARDED_FOR', __( 'Load balancer / reverse proxy (X-Forwarded-For)', 'bazaar-core' ) ),
		'xri'  => array( 'HTTP_X_REAL_IP', __( 'Nginx proxy (X-Real-IP)', 'bazaar-core' ) ),
		'tci'  => array( 'HTTP_TRUE_CLIENT_IP', __( 'Akamai / Cloudflare Enterprise (True-Client-IP)', 'bazaar-core' ) ),
	);
}

/**
 * Does an IP fall inside a CIDR range (IPv4 or IPv6)? A bare IP is treated as /32 or /128.
 *
 * @param string $ip   IP address.
 * @param string $cidr Range such as 173.245.48.0/20 or 2400:cb00::/32.
 * @return bool
 */
function bzr_ip_in_range( $ip, $cidr ) {
	$parts  = explode( '/', trim( $cidr ), 2 );
	$subnet = @inet_pton( $parts[0] ); // phpcs:ignore WordPress.PHP.NoSilencedErrors -- invalid input simply does not match.
	$addr   = @inet_pton( $ip ); // phpcs:ignore WordPress.PHP.NoSilencedErrors
	if ( false === $subnet || false === $addr || strlen( $subnet ) !== strlen( $addr ) ) {
		return false;
	}
	$max  = strlen( $addr ) * 8;
	$bits = isset( $parts[1] ) && '' !== $parts[1] ? (int) $parts[1] : $max;
	if ( $bits < 0 || $bits > $max ) {
		return false;
	}
	$bytes = intdiv( $bits, 8 );
	if ( substr( $addr, 0, $bytes ) !== substr( $subnet, 0, $bytes ) ) {
		return false;
	}
	$rem = $bits % 8;
	if ( 0 === $rem ) {
		return true;
	}
	$mask = ( 0xff << ( 8 - $rem ) ) & 0xff;
	return ( ord( $addr[ $bytes ] ) & $mask ) === ( ord( $subnet[ $bytes ] ) & $mask );
}

/**
 * The visitor's IP, used for rate limiting.
 *
 * By default only REMOTE_ADDR is trusted, because forwarded headers can be forged. Sites behind
 * Cloudflare, a load balancer or a reverse proxy must pick the header in Appearance > Store Options,
 * otherwise every visitor appears to share the proxy's IP and the limits trip for everyone at once.
 * When "Trusted proxy IPs" is filled in, the header is only honoured for requests from those IPs.
 *
 * @return string
 */
function bzr_client_ip() {
	static $ip = null;
	if ( null !== $ip ) {
		return $ip;
	}

	$remote = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
	$ip     = filter_var( $remote, FILTER_VALIDATE_IP ) ? $remote : 'unknown';

	$headers = bzr_proxy_headers();
	$choice  = (string) bzr_option( 'proxy_header', '' );
	$server  = isset( $headers[ $choice ] ) ? $headers[ $choice ][0] : '';

	if ( '' !== $server && ! empty( $_SERVER[ $server ] ) && bzr_proxy_is_trusted( $remote ) ) {
		$raw = sanitize_text_field( wp_unslash( $_SERVER[ $server ] ) );
		// X-Forwarded-For is "client, proxy1, proxy2": the right-most entry was added by our own proxy.
		$list      = array_map( 'trim', explode( ',', $raw ) );
		$candidate = 'xff' === $choice ? end( $list ) : $list[0];
		if ( filter_var( $candidate, FILTER_VALIDATE_IP ) ) {
			$ip = $candidate;
		}
	}

	/**
	 * Filter the client IP used for rate limiting.
	 *
	 * @param string $ip     Detected IP.
	 * @param string $remote REMOTE_ADDR.
	 */
	$ip = (string) apply_filters( 'bzr_client_ip', $ip, $remote );
	return $ip;
}

/**
 * Is REMOTE_ADDR an allowed proxy? With no list configured, any proxy is accepted.
 *
 * @param string $remote REMOTE_ADDR.
 * @return bool
 */
function bzr_proxy_is_trusted( $remote ) {
	$list = preg_split( '/[\s,]+/', (string) bzr_option( 'trusted_proxies', '' ), -1, PREG_SPLIT_NO_EMPTY );
	if ( ! $list ) {
		return true;
	}
	foreach ( $list as $range ) {
		if ( bzr_ip_in_range( $remote, $range ) ) {
			return true;
		}
	}
	return false;
}

/**
 * Count a hit against a rate-limit bucket. Returns false when the bucket is full.
 *
 * @param string $bucket Bucket key (e.g. "otp-ip:1.2.3.4").
 * @param int    $max    Hits allowed per window.
 * @param int    $window Window length in seconds.
 * @return bool
 */
function bzr_rate_limit( $bucket, $max, $window ) {
	$key   = 'bzr_rl_' . md5( $bucket );
	$state = get_transient( $key );
	$now   = time();

	if ( ! is_array( $state ) || $state['reset'] <= $now ) {
		$state = array(
			'count' => 0,
			'reset' => $now + $window,
		);
	}
	if ( $state['count'] >= $max ) {
		return false;
	}
	++$state['count'];
	// Keep the original window end, so repeated hits do not push the reset further out.
	set_transient( $key, $state, max( 1, $state['reset'] - $now ) );
	return true;
}

/**
 * Rate limits, filterable per site: [ max, window seconds ].
 *
 * @param string $name otp_email|otp_ip|contact_ip.
 * @return int[]
 */
function bzr_limit( $name ) {
	$limits = apply_filters(
		'bzr_rate_limits',
		array(
			'otp_email'  => array( 3, 15 * MINUTE_IN_SECONDS ),
			'otp_ip'     => array( 10, 15 * MINUTE_IN_SECONDS ),
			'contact_ip' => array( 5, 10 * MINUTE_IN_SECONDS ),
		)
	);
	return isset( $limits[ $name ] ) ? array_map( 'absint', $limits[ $name ] ) : array( 5, 600 );
}

/**
 * Is a known SMTP/email-delivery plugin active? Used to warn when OTP emails may hit spam.
 *
 * @return bool
 */
function bzr_has_smtp_plugin() {
	$known = defined( 'WPMS_PLUGIN_VER' )        // WP Mail SMTP.
		|| defined( 'FLUENTMAIL' )                // FluentSMTP.
		|| class_exists( 'PostmanOptions' )       // Post SMTP.
		|| defined( 'EASY_WP_SMTP_PLUGIN_VERSION' )
		|| defined( 'SENDGRID_PLUGIN_VERSION' )
		|| class_exists( 'Mailgun' )
		|| defined( 'BREVO_PLUGIN_VERSION' )
		|| has_action( 'phpmailer_init' );
	return (bool) apply_filters( 'bzr_has_smtp', $known );
}

/**
 * Expand [bzr_*] shortcodes inside Shortcode blocks at render time.
 *
 * Block templates run do_shortcode() on the template source before patterns referenced by
 * <!-- wp:pattern --> are expanded, so a shortcode inside a pattern used by a template
 * (e.g. the reviews section on the front page) would otherwise print as raw text.
 *
 * @param string $html Rendered Shortcode block.
 * @return string
 */
function bzr_render_shortcode_block( $html ) {
	return false !== strpos( $html, '[bzr_' ) ? do_shortcode( $html ) : $html;
}
add_filter( 'render_block_core/shortcode', 'bzr_render_shortcode_block' );
