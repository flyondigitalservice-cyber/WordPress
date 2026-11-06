<?php
/**
 * One-time, batched import of Drive portfolio photos (draft-theme only).
 * Each request with ?cfmx_photos=1 imports the next batch; progress lives in
 * the cfm_photos_2026_11 option, so re-running never duplicates a photo.
 *
 * @package CFM
 */

defined( 'ABSPATH' ) || exit;

add_action( 'wp_loaded', 'cfmx_run_photos', 99 );
/**
 * Import the next batch.
 */
function cfmx_run_photos() {
	if ( ! isset( $_GET['cfmx_photos'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		return;
	}
	$opt   = 'cfm_photos_2026_11';
	$state = get_option( $opt );
	$state = is_array( $state ) ? $state : array( 'done' => array(), 'errors' => array() );
	if ( get_transient( 'cfm_photos_lock' ) ) {
		return;
	}
	set_transient( 'cfm_photos_lock', 1, 120 );

	$items = get_transient( 'cfm_photos_spec' );
	if ( ! is_array( $items ) ) {
		$url  = 'https://raw.githubusercontent.com/flyondigitalservice-cyber/wordpress/9964c3d4c9b71664b7b2bdd583717f938f73a8a1/seo-content/2026-11/drive-photos.json';
		$resp = wp_remote_get( $url, array( 'timeout' => 30 ) );
		$body = is_wp_error( $resp ) ? '' : wp_remote_retrieve_body( $resp );
		if ( '1ac60b906b516fcc828ef249d7acce0e561abc023dae9f5326dad0e591daaaab' !== hash( 'sha256', $body ) ) {
			$state['errors'][] = 'spec download failed or checksum mismatch';
			update_option( $opt, $state, false );
			delete_transient( 'cfm_photos_lock' );
			return;
		}
		$items = json_decode( $body, true );
		set_transient( 'cfm_photos_spec', $items, HOUR_IN_SECONDS );
	}

	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';

	$batch = 0;
	foreach ( $items as $it ) {
		$key = $it['page'] . ':' . $it['code'];
		if ( isset( $state['done'][ $key ] ) ) {
			continue;
		}
		if ( $batch >= 6 ) {
			break;
		}
		$batch++;
		$tmp = download_url( 'https://drive.google.com/uc?export=download&id=' . rawurlencode( $it['drive'] ), 60 );
		if ( is_wp_error( $tmp ) ) {
			$state['errors'][]     = $key . ': ' . $tmp->get_error_message();
			$state['done'][ $key ] = 0;
			continue;
		}
		$type = wp_get_image_mime( $tmp );
		if ( ! in_array( $type, array( 'image/jpeg', 'image/png', 'image/webp' ), true ) ) {
			wp_delete_file( $tmp );
			$state['errors'][]     = $key . ': not an image (' . $type . ')';
			$state['done'][ $key ] = 0;
			continue;
		}
		$id = media_handle_sideload( array( 'name' => $it['filename'], 'tmp_name' => $tmp ), (int) $it['page'], $it['title'] );
		if ( is_wp_error( $id ) ) {
			wp_delete_file( $tmp );
			$state['errors'][]     = $key . ': ' . $id->get_error_message();
			$state['done'][ $key ] = 0;
			continue;
		}
		update_post_meta( $id, '_wp_attachment_image_alt', $it['alt'] );
		if ( ! empty( $it['featured'] ) ) {
			set_post_thumbnail( (int) $it['page'], $id );
		}
		$state['done'][ $key ] = $id;
	}
	$state['remaining'] = count( $items ) - count( $state['done'] );
	update_option( $opt, $state, false );
	delete_transient( 'cfm_photos_lock' );
}
