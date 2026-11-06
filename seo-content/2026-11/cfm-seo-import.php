<?php
/**
 * One-time importer for the November 2026 SEO pages. Runs once (guarded by an
 * option), creates missing pages only, never overwrites existing ones.
 *
 * @package CFM
 */

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'CFMX_SITE' ) ) {
	define( 'CFMX_SITE', untrailingslashit( home_url() ) );
}
require_once __DIR__ . '/cfm-seo-builder.php';

add_action( 'wp_loaded', 'cfmx_run_seo_import', 99 );
/**
 * Create the pages.
 */
function cfmx_run_seo_import() {
	$flag = 'cfm_seo_import_2026_11';
	if ( get_option( $flag ) || ! isset( $_GET['cfmx_import'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		return;
	}
	update_option( $flag, 'running', false );

	$data = json_decode( (string) file_get_contents( __DIR__ . '/seo-pages-data.json' ), true );
	if ( ! is_array( $data ) ) {
		update_option( $flag, 'error: bad data', false );
		return;
	}
	kses_remove_filters();
	$author = get_users( array( 'role' => 'administrator', 'number' => 1, 'fields' => 'ID' ) );
	$author = $author ? (int) $author[0] : 1;

	$ids = array();
	$log = array( 'created' => array(), 'skipped' => array(), 'errors' => array() );
	foreach ( cfmx_build_pages( $data ) as $pg ) {
		$parent = 0;
		if ( $pg['parent'] ) {
			if ( isset( $ids[ $pg['parent'] ] ) ) {
				$parent = $ids[ $pg['parent'] ];
			} else {
				$pp     = get_page_by_path( $pg['parent'] );
				$parent = $pp ? $pp->ID : 0;
			}
			if ( ! $parent ) {
				$log['errors'][] = 'no parent for ' . $pg['slug'];
				continue;
			}
		}
		$path     = ( $parent ? get_page_uri( $parent ) . '/' : '' ) . $pg['slug'];
		$existing = get_page_by_path( $path );
		if ( $existing ) {
			$ids[ $pg['key'] ]  = $existing->ID;
			$log['skipped'][]   = $path;
			continue;
		}
		$id = wp_insert_post(
			wp_slash(
				array(
					'post_type'    => 'page',
					'post_status'  => 'publish',
					'post_title'   => $pg['title'],
					'post_name'    => $pg['slug'],
					'post_content' => $pg['content'],
					'post_excerpt' => $pg['excerpt'],
					'post_parent'  => $parent,
					'menu_order'   => (int) $pg['order'],
					'post_author'  => $author,
				)
			),
			true
		);
		if ( is_wp_error( $id ) ) {
			$log['errors'][] = $pg['slug'] . ': ' . $id->get_error_message();
			continue;
		}
		foreach ( $pg['meta'] as $k => $v ) {
			update_post_meta( $id, $k, $v );
		}
		if ( ! empty( $pg['img'] ) && wp_attachment_is_image( (int) $pg['img'] ) ) {
			set_post_thumbnail( $id, (int) $pg['img'] );
		}
		$ids[ $pg['key'] ] = $id;
		$log['created'][]  = $id . ' ' . $path;
	}
	update_option( $flag, wp_json_encode( $log ), false );
}
