<?php
/**
 * Theme bootstrap. Keep this file thin: it only loads modules.
 *
 * Store features (OTP login, contact form, swatches, compare, filters, WhatsApp, updates...)
 * live in the Bazaar Core plugin, shared by all Bazaar themes. This theme only switches the
 * features on (inc/theme-features.php) and provides design: theme.json, templates, patterns.
 *
 * @package Bazaar
 */

defined( 'ABSPATH' ) || exit;

define( 'BZR_THEME_DIR', get_template_directory() );
define( 'BZR_THEME_URI', get_template_directory_uri() );

require_once BZR_THEME_DIR . '/inc/setup.php';
require_once BZR_THEME_DIR . '/inc/enqueue.php';
require_once BZR_THEME_DIR . '/inc/theme-features.php';
require_once BZR_THEME_DIR . '/inc/compat.php';
