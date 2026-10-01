<?php
/**
 * DCE Vibe theme bootstrap.
 *
 * @package dce-vibe
 */

defined( 'ABSPATH' ) || exit;

define( 'DCE_VERSION', '2.0.0' );
define( 'DCE_DIR', get_template_directory() );
define( 'DCE_URI', get_template_directory_uri() );

require DCE_DIR . '/inc/helpers.php';
require DCE_DIR . '/inc/setup.php';
require DCE_DIR . '/inc/customizer.php';
require DCE_DIR . '/inc/content-filters.php';
require DCE_DIR . '/inc/leads.php';
require DCE_DIR . '/inc/seo.php';
require DCE_DIR . '/inc/builder.php';
require DCE_DIR . '/inc/patterns.php';

// Page content and the importer are only needed in the admin and WP-CLI.
if ( is_admin() || ( defined( 'WP_CLI' ) && WP_CLI ) || defined( 'DCE_LOAD_IMPORTER' ) ) {
	require DCE_DIR . '/inc/content/catalogues.php';
	require DCE_DIR . '/inc/content/products.php';
	require DCE_DIR . '/inc/content/areas.php';
	require DCE_DIR . '/inc/content/pages.php';
	require DCE_DIR . '/inc/importer.php';
}
