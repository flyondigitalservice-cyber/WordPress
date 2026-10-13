<?php
/**
 * Dastaan by Artisans — theme bootstrap.
 *
 * Every file in /inc is self-contained and guarded with ABSPATH checks.
 * Nothing here runs before WordPress is fully loaded.
 *
 * @package Dastaan
 */

defined( 'ABSPATH' ) || exit;

define( 'DASTAAN_VERSION', '1.3.0' );
define( 'DASTAAN_DIR', trailingslashit( get_template_directory() ) );
define( 'DASTAAN_URI', trailingslashit( get_template_directory_uri() ) );

$dastaan_includes = array(
	'inc/helpers.php',          // Option access, icons, sanitizers, small template tags.
	'inc/setup.php',            // Theme supports, menus, image sizes, widgets.
	'inc/enqueue.php',          // Styles, scripts, fonts, inline CSS variables.
	'inc/customizer.php',       // Every editable option (panels, sections, controls).
	'inc/walker-nav.php',       // Mega-menu aware nav walker.
	'inc/template-tags.php',    // Larger output helpers: breadcrumbs, badges, social, etc.
	'inc/blocks.php',           // Shortcodes + block patterns (Instagram, Reels, USP, Banners).
	'inc/newsletter.php',       // Native newsletter capture (no plugin needed).
	'inc/seo.php',              // Meta tags, Open Graph, JSON-LD schema.
	'inc/ai-optimization.php',  // llms.txt, robots directives for AI crawlers.
	'inc/tracking.php',         // Meta Pixel, GA4, Microsoft Clarity + consent gating.
	'inc/security.php',         // Hardening headers and WordPress clean-ups.
	'inc/performance.php',      // Asset trimming, resource hints, lazy-loading.
	'inc/admin.php',            // Admin notices, welcome page, product meta boxes.
	'inc/demo-content.php',     // One-click demo products (Dastaan → Demo Products / wp dastaan demo).
);

foreach ( $dastaan_includes as $dastaan_file ) {
	$dastaan_path = DASTAAN_DIR . $dastaan_file;
	if ( file_exists( $dastaan_path ) ) {
		require_once $dastaan_path;
	}
}
unset( $dastaan_includes, $dastaan_file, $dastaan_path );

if ( class_exists( 'WooCommerce' ) ) {
	require_once DASTAAN_DIR . 'inc/woocommerce.php';
	require_once DASTAAN_DIR . 'inc/product-video.php';
	require_once DASTAAN_DIR . 'inc/checkout.php';
}
