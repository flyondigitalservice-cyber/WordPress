<?php
/**
 * Plugin Name:       Propzed Projects, Sales & Assistant
 * Description:       Add-on for the Propzed theme: mark listings Sold / Leased Out (front end and wp-admin), villas and residential projects with an interactive floor plan and live unit availability, guide/blog SEO (Article, FAQ, breadcrumbs), a property assistant chat widget and an AI-ready llms.txt. No theme files are changed.
 * Version:           1.0.0
 * Requires at least: 6.2
 * Requires PHP:      7.4
 * Author:            Flyon Digital Service
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       propzed-projects
 *
 * @package PropzedProjects
 */

defined( 'ABSPATH' ) || exit;

define( 'PZS_VERSION', '1.0.0' );
define( 'PZS_FILE', __FILE__ );
define( 'PZS_DIR', plugin_dir_path( __FILE__ ) );
define( 'PZS_URL', plugin_dir_url( __FILE__ ) );

require_once PZS_DIR . 'includes/setup.php';

register_activation_hook( __FILE__, 'pzs_activate' );
register_deactivation_hook( __FILE__, 'pzs_deactivate' );

/**
 * Load the modules once the theme is known. Everything depends on the Propzed
 * theme helpers (propzed_meta(), the "property" post type...), so with any
 * other theme the plugin only shows an admin notice.
 */
function pzs_boot() {
	if ( ! function_exists( 'propzed_meta' ) ) {
		add_action(
			'admin_notices',
			static function () {
				echo '<div class="notice notice-warning"><p>' . esc_html__( 'Propzed Projects, Sales & Assistant needs the Propzed theme. It is inactive until that theme is active.', 'propzed-projects' ) . '</p></div>';
			}
		);
		return;
	}

	foreach ( array( 'settings', 'sold', 'residential', 'inventory', 'seo', 'llms', 'assistant' ) as $module ) {
		require_once PZS_DIR . 'includes/' . $module . '.php';
	}
}
add_action( 'after_setup_theme', 'pzs_boot', 20 );
