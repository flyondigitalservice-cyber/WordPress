<?php
/**
 * FlavorKit functions and definitions.
 *
 * @package FlavorKit
 */

defined( 'ABSPATH' ) || exit;

define( 'FLAVORKIT_VERSION', '1.0.0' );
define( 'FLAVORKIT_DIR', get_template_directory() );
define( 'FLAVORKIT_URI', get_template_directory_uri() );

require_once FLAVORKIT_DIR . '/inc/defaults.php';
require_once FLAVORKIT_DIR . '/inc/setup.php';
require_once FLAVORKIT_DIR . '/inc/template-tags.php';
require_once FLAVORKIT_DIR . '/inc/illustrations.php';
require_once FLAVORKIT_DIR . '/inc/customizer.php';
require_once FLAVORKIT_DIR . '/inc/enqueue.php';
require_once FLAVORKIT_DIR . '/inc/newsletter.php';

if ( class_exists( 'WooCommerce' ) ) {
	require_once FLAVORKIT_DIR . '/inc/woocommerce.php';
}
