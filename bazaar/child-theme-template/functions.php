<?php
/**
 * Client child theme. The parent (Bazaar theme) and the Bazaar Core plugin do the heavy lifting.
 *
 * Common customisations:
 * - Brand colours/fonts: theme.json in this folder (only list what differs from the parent).
 * - Override a template or part: copy it from the parent into templates/ or parts/ here.
 * - Override a pattern: copy the parent pattern file into patterns/ here (same slug).
 * - Change trust badges, starter pages or size guide: use the bzr_* filters below.
 *
 * @package ClientChild
 */

defined( 'ABSPATH' ) || exit;

/**
 * Load the child stylesheet after the parent styles.
 */
add_action(
	'wp_enqueue_scripts',
	static function () {
		wp_enqueue_style( 'client-child', get_stylesheet_uri(), array( 'bzr-main' ), wp_get_theme()->get( 'Version' ) );
	},
	20
);

/*
Examples (uncomment to use):

add_filter( 'bzr_trust_badges', function () {
	return array(
		array( 'truck', 'Free delivery in Mumbai' ),
		array( 'return', '10-day returns' ),
		array( 'shield', 'Secure checkout' ),
		array( 'chat', 'WhatsApp support' ),
	);
} );

// Allow more login-code emails per visitor IP (e.g. a busy office network).
add_filter( 'bzr_rate_limits', function ( $limits ) {
	$limits['otp_ip'] = array( 30, 15 * MINUTE_IN_SECONDS );
	return $limits;
} );
*/
