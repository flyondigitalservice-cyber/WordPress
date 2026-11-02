<?php
/**
 * Keeps LiteSpeed from serving cached HTML to agents that ask for Markdown.
 *
 * LiteSpeed answers from its page cache before PHP runs, so the plugin cannot
 * fix this at runtime. It puts a small rule block at the top of .htaccess and
 * takes it out again on deactivation.
 *
 * @package Bombay_Agent_Ready
 */

defined( 'ABSPATH' ) || exit;

class BMAR_Htaccess {

	const BEGIN   = '# BEGIN Bombay Agent Ready';
	const END     = '# END Bombay Agent Ready';
	const VERSION = '1';
	const OPTION  = 'bmar_htaccess_version';

	/**
	 * Register hooks.
	 *
	 * @param string $plugin_file Main plugin file.
	 */
	public static function init( $plugin_file ) {
		register_activation_hook( $plugin_file, array( __CLASS__, 'install' ) );
		register_deactivation_hook( $plugin_file, array( __CLASS__, 'uninstall' ) );

		// Also covers updates uploaded over an already-active copy.
		add_action( 'init', array( __CLASS__, 'maybe_install' ), 1 );
	}

	/**
	 * Install once per rule version.
	 */
	public static function maybe_install() {
		if ( self::VERSION !== get_option( self::OPTION ) ) {
			self::install();
		}
	}

	/**
	 * Rule block.
	 *
	 * @return string
	 */
	private static function block() {
		return self::BEGIN . "\n"
			. "# Agents asking for Markdown must bypass the LiteSpeed page cache.\n"
			. "<IfModule LiteSpeed>\n"
			. "RewriteEngine On\n"
			. "RewriteCond %{HTTP_ACCEPT} text/(x-)?markdown [NC,OR]\n"
			. "RewriteCond %{QUERY_STRING} (^|&)format=(md|markdown)(&|$) [NC]\n"
			. "RewriteRule .* - [E=Cache-Control:no-cache]\n"
			. "</IfModule>\n"
			. self::END . "\n";
	}

	/**
	 * Path to the site's .htaccess.
	 *
	 * @return string
	 */
	private static function path() {
		if ( ! function_exists( 'get_home_path' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}
		return get_home_path() . '.htaccess';
	}

	/**
	 * Remove the block from .htaccess contents.
	 *
	 * @param string $contents Contents.
	 * @return string
	 */
	private static function strip( $contents ) {
		$pattern = '/' . preg_quote( self::BEGIN, '/' ) . '.*?' . preg_quote( self::END, '/' ) . '\R?/s';
		return preg_replace( $pattern, '', $contents );
	}

	/**
	 * Add (or refresh) the block at the top of .htaccess.
	 */
	public static function install() {
		$file = self::path();
		if ( ! file_exists( $file ) || ! is_writable( $file ) ) {
			update_option( self::OPTION, 'unwritable', false );
			return;
		}

		$contents = (string) file_get_contents( $file ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		$updated  = self::block() . "\n" . ltrim( self::strip( $contents ) );

		if ( $updated !== $contents ) {
			file_put_contents( $file, $updated, LOCK_EX ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
		}

		update_option( self::OPTION, self::VERSION, false );
	}

	/**
	 * Remove the block.
	 */
	public static function uninstall() {
		delete_option( self::OPTION );

		$file = self::path();
		if ( ! file_exists( $file ) || ! is_writable( $file ) ) {
			return;
		}

		$contents = (string) file_get_contents( $file ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		$updated  = self::strip( $contents );
		if ( $updated !== $contents ) {
			file_put_contents( $file, ltrim( $updated ), LOCK_EX ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
		}
	}
}
