<?php
/**
 * WP-CLI commands.
 *
 *   wp dgf install                 Create missing pages, menus, front page.
 *   wp dgf rebuild <slug>          Regenerate one page from starter content.
 *   wp dgf import-images [--dir=]  Import images/PDFs and attach them to pages.
 *   wp dgf whatsapp <number>       Set the WhatsApp number.
 *   wp dgf list                    List all pages with their URL and image status.
 *
 * @package DGF
 */

defined( 'ABSPATH' ) || exit;

WP_CLI::add_command(
	'dgf install',
	static function () {
		$r = dgf_install();
		WP_CLI::success( sprintf( '%d pages created, %d already existed.', $r['created'], $r['existing'] ) );
	}
);

WP_CLI::add_command(
	'dgf rebuild',
	static function ( $args ) {
		dgf_install_pages( array( sanitize_title( $args[0] ) ) );
		WP_CLI::success( 'Rebuilt ' . $args[0] );
	}
);

WP_CLI::add_command(
	'dgf import-images',
	static function ( $args, $assoc ) {
		$r = dgf_import_images( isset( $assoc['dir'] ) ? $assoc['dir'] : '' );
		foreach ( $r['notes'] as $note ) {
			WP_CLI::log( $note );
		}
		WP_CLI::success( sprintf( '%d imported, %d featured images set, %d skipped.', $r['imported'], $r['featured'], $r['skipped'] ) );
	}
);

WP_CLI::add_command(
	'dgf whatsapp',
	static function ( $args ) {
		set_theme_mod( 'dgf_whatsapp_number', sanitize_text_field( $args[0] ) );
		WP_CLI::success( 'WhatsApp number set to ' . dgf_wa_number() );
	}
);

WP_CLI::add_command(
	'dgf list',
	static function () {
		$rows = array();
		foreach ( dgf_pages_data() as $slug => $page ) {
			$post   = dgf_get_page( $slug );
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
