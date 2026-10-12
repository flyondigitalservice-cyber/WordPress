<?php
/**
 * Hadhrami Fashion — theme bootstrap.
 *
 * @package HadhramiFashion
 */

defined( 'ABSPATH' ) || exit;

define( 'HF_VERSION', '1.0.0' );
define( 'HF_DIR', get_template_directory() );
define( 'HF_URI', get_template_directory_uri() );

require HF_DIR . '/inc/helpers.php';
require HF_DIR . '/inc/settings.php';
require HF_DIR . '/inc/setup.php';
require HF_DIR . '/inc/catalog.php';
require HF_DIR . '/inc/delivery.php';
require HF_DIR . '/inc/seed.php';
