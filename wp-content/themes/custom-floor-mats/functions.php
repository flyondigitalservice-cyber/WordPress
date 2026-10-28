<?php
/**
 * Custom Floor Mats theme bootstrap.
 *
 * Every module lives in /inc so each concern (setup, customizer, SEO, leads,
 * catalogues, installer) can be read and edited on its own.
 *
 * @package CFM
 */

defined( 'ABSPATH' ) || exit;

define( 'CFM_VERSION', '1.0.0' );
define( 'CFM_DIR', get_template_directory() );
define( 'CFM_URI', get_template_directory_uri() );

require_once CFM_DIR . '/inc/helpers.php';
require_once CFM_DIR . '/inc/setup.php';
require_once CFM_DIR . '/inc/customizer.php';
require_once CFM_DIR . '/inc/seo.php';
require_once CFM_DIR . '/inc/leads.php';
require_once CFM_DIR . '/inc/shortcodes.php';
require_once CFM_DIR . '/inc/interiors-data.php';
require_once CFM_DIR . '/inc/pages-data.php';
require_once CFM_DIR . '/inc/content-builder.php';
require_once CFM_DIR . '/inc/installer.php';
require_once CFM_DIR . '/inc/image-import.php';
require_once CFM_DIR . '/inc/drive-import.php';

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	require_once CFM_DIR . '/inc/cli.php';
}
