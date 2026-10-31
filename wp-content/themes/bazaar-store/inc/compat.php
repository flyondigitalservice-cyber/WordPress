<?php
/**
 * Graceful fallback when the Bazaar Core plugin is not active.
 *
 * The theme still renders: patterns that call bzr_option()/bzr_whatsapp_url() get safe
 * defaults, and feature shortcodes print nothing instead of raw [bzr_...] text.
 * Administrators see a notice asking them to activate the plugin.
 *
 * @package Bazaar
 */

defined( 'ABSPATH' ) || exit;

// Plugins load before themes, so if the plugin is active these already exist.
if ( defined( 'BAZAAR_CORE_VERSION' ) ) {
	return;
}

if ( ! function_exists( 'bzr_option' ) ) {
	/**
	 * Fallback: no stored options without the plugin.
	 *
	 * @param string $key     Option key.
	 * @param mixed  $default Fallback.
	 * @return mixed
	 */
	function bzr_option( $key, $default = '' ) { // phpcs:ignore Universal.NamingConventions.NoReservedKeywordParameterNames
		unset( $key );
		return $default;
	}
}

if ( ! function_exists( 'bzr_whatsapp_url' ) ) {
	/**
	 * Fallback: WhatsApp links are hidden without the plugin.
	 *
	 * @return string
	 */
	function bzr_whatsapp_url() {
		return '';
	}
}

add_action(
	'init',
	static function () {
		foreach ( array( 'bzr_otp_login', 'bzr_contact_form', 'bzr_filters', 'bzr_compare', 'bzr_reviews' ) as $tag ) {
			if ( ! shortcode_exists( $tag ) ) {
				add_shortcode( $tag, '__return_empty_string' );
			}
		}
	},
	20
);

// Also expand them inside patterns used by templates (see bzr_render_shortcode_block() in the plugin).
add_filter(
	'render_block_core/shortcode',
	static function ( $html ) {
		return false !== strpos( $html, '[bzr_' ) ? do_shortcode( $html ) : $html;
	}
);

add_action(
	'admin_notices',
	static function () {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}
		$installed = file_exists( WP_PLUGIN_DIR . '/bazaar-core/bazaar-core.php' );
		$url       = $installed
			? wp_nonce_url( admin_url( 'plugins.php?action=activate&plugin=bazaar-core%2Fbazaar-core.php' ), 'activate-plugin_bazaar-core/bazaar-core.php' )
			: admin_url( 'plugin-install.php?tab=upload' );
		printf(
			'<div class="notice notice-warning"><p><strong>%1$s</strong> %2$s <a href="%3$s">%4$s</a></p></div>',
			esc_html( wp_get_theme( get_template() )->get( 'Name' ) ),
			esc_html__( 'needs the Bazaar Core plugin for login, contact form, product features and Store Options.', 'bazaar-store' ),
			esc_url( $url ),
			$installed ? esc_html__( 'Activate Bazaar Core', 'bazaar-store' ) : esc_html__( 'Upload Bazaar Core', 'bazaar-store' )
		);
	}
);
