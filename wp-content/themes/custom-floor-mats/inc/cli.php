<?php
/**
 * WP-CLI commands.
 *
 *   wp cfm install                 Create missing pages, menus, front page.
 *   wp cfm rebuild <slug>          Regenerate one page from starter content.
 *   wp cfm import-images [--dir=]  Import images/PDFs and attach them to pages.
 *   wp cfm drive-import            Import the Casa Vera Google Drive images and catalogues.
 *   wp cfm whatsapp <number>       Set the WhatsApp number.
 *   wp cfm list                    List all pages with their URL and image status.
 *
 * @package CFM
 */

defined( 'ABSPATH' ) || exit;

WP_CLI::add_command(
	'cfm install',
	static function () {
		$r = cfm_install();
		WP_CLI::success( sprintf( '%d pages created, %d already existed.', $r['created'], $r['existing'] ) );
	}
);

WP_CLI::add_command(
	'cfm rebuild',
	static function ( $args ) {
		cfm_install_pages( array( sanitize_title( $args[0] ) ) );
		WP_CLI::success( 'Rebuilt ' . $args[0] );
	}
);

WP_CLI::add_command(
	'cfm import-images',
	static function ( $args, $assoc ) {
		$r = cfm_import_images( isset( $assoc['dir'] ) ? $assoc['dir'] : '' );
		foreach ( $r['notes'] as $note ) {
			WP_CLI::log( $note );
		}
		WP_CLI::success( sprintf( '%d imported, %d featured images set, %d skipped.', $r['imported'], $r['featured'], $r['skipped'] ) );
	}
);

WP_CLI::add_command(
	'cfm drive-import',
	static function () {
		$r = cfm_drive_import();
		foreach ( $r['notes'] as $note ) {
			WP_CLI::log( $note );
		}
		WP_CLI::success( sprintf( '%d imported, %d featured images set, %d failed.', $r['imported'], $r['featured'], $r['failed'] ) );
	}
);

WP_CLI::add_command(
	'cfm whatsapp',
	static function ( $args ) {
		set_theme_mod( 'cfm_whatsapp_number', sanitize_text_field( $args[0] ) );
		WP_CLI::success( 'WhatsApp number set to ' . cfm_wa_number() );
	}
);

WP_CLI::add_command(
	'cfm list',
	static function () {
		$rows = array();
		foreach ( cfm_pages_data() as $slug => $page ) {
			$post   = cfm_get_page( $slug );
			$rows[] = array(
				'slug'  => $slug,
				'type'  => $page['type'],
				'url'   => $post ? get_permalink( $post ) : '(missing)',
				'image' => $post && has_post_thumbnail( $post ) ? 'yes' : 'no',
			);
		}
		WP_CLI\Utils\format_items( 'table', $rows, array( 'slug', 'type', 'url', 'image' ) );
	}
);
