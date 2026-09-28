<?php
/**
 * Dubai Gym Flooring theme bootstrap.
 *
 * Every module lives in /inc so each concern (setup, customizer, SEO, leads,
 * catalogues, installer) can be read and edited on its own.
 *
 * @package DGF
 */

defined( 'ABSPATH' ) || exit;

define( 'DGF_VERSION', '1.0.0' );
define( 'DGF_DIR', get_template_directory() );
define( 'DGF_URI', get_template_directory_uri() );

require_once DGF_DIR . '/inc/helpers.php';
require_once DGF_DIR . '/inc/setup.php';
require_once DGF_DIR . '/inc/customizer.php';
require_once DGF_DIR . '/inc/seo.php';
require_once DGF_DIR . '/inc/leads.php';
require_once DGF_DIR . '/inc/shortcodes.php';
require_once DGF_DIR . '/inc/pages-data.php';
require_once DGF_DIR . '/inc/content-builder.php';
require_once DGF_DIR . '/inc/installer.php';
require_once DGF_DIR . '/inc/image-import.php';

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	require_once DGF_DIR . '/inc/cli.php';
}
