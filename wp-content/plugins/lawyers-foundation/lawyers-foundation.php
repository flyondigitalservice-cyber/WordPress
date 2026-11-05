<?php
/**
 * Plugin Name:       Lawyers Foundation Membership
 * Description:       Membership platform for a lawyers' foundation: lawyer member applications with admin approval, a member directory, a member dashboard, foundation events held two or three times a year with RSVPs, initiatives, and pro bono service-hour logging.
 * Version:           1.0.0
 * Requires at least: 6.2
 * Requires PHP:      7.4
 * Author:            Flyon Digital Service
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       lawyers-foundation
 *
 * @package Lawyers_Foundation
 */

defined( 'ABSPATH' ) || exit;

define( 'LF_VERSION', '1.0.0' );
define( 'LF_FILE', __FILE__ );
define( 'LF_DIR', plugin_dir_path( __FILE__ ) );
define( 'LF_URL', plugin_dir_url( __FILE__ ) );

require_once LF_DIR . 'includes/helpers.php';
require_once LF_DIR . 'includes/post-types.php';
require_once LF_DIR . 'includes/settings.php';
require_once LF_DIR . 'includes/members.php';
require_once LF_DIR . 'includes/events.php';
require_once LF_DIR . 'includes/service.php';
require_once LF_DIR . 'includes/install.php';

register_activation_hook( __FILE__, 'lf_activate' );
register_deactivation_hook( __FILE__, 'lf_deactivate' );

/**
 * Front-end stylesheet.
 */
function lf_enqueue_assets() {
	wp_enqueue_style( 'lawyers-foundation', LF_URL . 'assets/lf.css', array(), LF_VERSION );
}
add_action( 'wp_enqueue_scripts', 'lf_enqueue_assets' );

/**
 * Keep pages with personalised forms (nonces, member data) out of page caches such as LiteSpeed.
 */
function lf_nocache_member_pages() {
	$post = get_queried_object();
	$hit  = is_singular( 'lf_event' );
	if ( ! $hit && $post instanceof WP_Post ) {
		foreach ( array( 'lf_register', 'lf_dashboard', 'lf_directory' ) as $tag ) {
			if ( has_shortcode( $post->post_content, $tag ) ) {
				$hit = true;
				break;
			}
		}
	}
	if ( $hit ) {
		if ( ! defined( 'DONOTCACHEPAGE' ) ) {
			define( 'DONOTCACHEPAGE', true );
		}
		do_action( 'litespeed_control_set_nocache', 'lawyers-foundation member page' ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
		nocache_headers();
	}
}
add_action( 'template_redirect', 'lf_nocache_member_pages', 1 );

/**
 * Practice-area checkbox styles on the admin user screens.
 *
 * @param string $hook Admin page hook.
 */
function lf_admin_assets( $hook ) {
	if ( in_array( $hook, array( 'profile.php', 'user-edit.php' ), true ) ) {
		wp_add_inline_style( 'common', '.lf-checks{display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:4px 16px}' );
	}
}
add_action( 'admin_enqueue_scripts', 'lf_admin_assets' );
