<?php
/**
 * Google Drive importer.
 *
 * The Casa Vera Home Drive folders are shared "anyone with the link", so the
 * site downloads each file straight from Drive, adds it to the Media Library,
 * attaches it to its page and (for the first photo of a page) sets the
 * featured/hero image. PDFs become catalogue cards with the in-page viewer.
 *
 * Run it from Appearance → CFM Setup, with `wp cfm drive-import`, or as an
 * administrator via POST /wp-json/cfm/v1/drive-import. Files already imported
 * are skipped, so it is safe to run again after adding entries below.
 *
 * @package CFM
 */

defined( 'ABSPATH' ) || exit;

/**
 * Drive files to import: file ID, page slug, optional title, featured flag.
 *
 * @return array<int,array{id:string,slug:string,title?:string,featured?:bool,excerpt?:string}>
 */
function cfm_drive_manifest() {
	return array(
		// Catalogues & technical data sheets (PDF).
		array( 'id' => '1W438S0qUCw-Ozt5ikLXZItq851hStTKv', 'slug' => 'rubber-gym-mats', 'title' => 'Rubber & EPDM Gym Mats — Catalogue', 'excerpt' => 'Rubber and EPDM gym mats and tiles: thicknesses, colours and applications.' ),
		array( 'id' => '1lWNzFkI1Mv7CdsDEAbbisiDaJv_u9TX3', 'slug' => 'rubber-gym-mats', 'title' => 'TerraFit Extreme — Technical Data Sheet', 'excerpt' => 'Heavy-duty rubber gym tiles for free-weight and drop zones.' ),
		array( 'id' => '1ZHPp56n1IyHmkrtZ0tjTAuv5SABaMx6w', 'slug' => 'rubber-gym-mats', 'title' => 'TerraFit Active Rolls — Technical Data Sheet', 'excerpt' => 'Rubber flooring rolls for gyms, studios and large training areas.' ),
		array( 'id' => '12iT6imeJ1wwCxpTrU6u3ZPDB6HpwbI-t', 'slug' => 'kids-play-mats', 'title' => 'TerraPlay Square — Technical Data Sheet', 'excerpt' => 'Square rubber safety tiles for play areas and playgrounds.' ),

		// Rubber gym mats.
		array( 'id' => '15BjzYq7Zj9XzJ0JQ3efZGjfv7v70s_qf', 'slug' => 'rubber-gym-mats', 'title' => 'Rubber gym mat tiles', 'featured' => true ),
		array( 'id' => '1_U73Q95zFBSzHwIdEQCv1JVj2anUbCgk', 'slug' => 'rubber-gym-mats', 'title' => 'EPDM rubber gym mats' ),
		array( 'id' => '1E1TpjV3DY4rb-emV0X-IkWMJ49I5ieiq', 'slug' => 'rubber-gym-mats', 'title' => 'EPDM colour-fleck gym mats' ),
		array( 'id' => '1yOTr_A1eMhXiDLZ1W2-JVKuWp14mxKSn', 'slug' => 'rubber-gym-mats', 'title' => 'EPDM rubber mats, 15–50 mm' ),
		array( 'id' => '1P2ecP_E1qBtM8vSQCtXnk7ycAEyX5rrR', 'slug' => 'rubber-gym-mats', 'title' => 'EPDM gym mat installation' ),
		array( 'id' => '1kBmkY7oSGwPNHyI3FLRt38e4ZMGOufXH', 'slug' => 'rubber-gym-mats', 'title' => 'Vinyl and rubber gym mats' ),
		array( 'id' => '19QgURUjIipT7eawHVRUIdXMx3KpEZTbe', 'slug' => 'rubber-gym-mats', 'title' => 'TerraFit Extreme rubber tile' ),
		array( 'id' => '1NOd5q9Q40y0VCAXzYczcTXWDsYiKFU-5', 'slug' => 'rubber-gym-mats', 'title' => 'TerraFit Square rubber tile' ),
		array( 'id' => '1kjTDx7ouBJl5bcu9SAbzz85Gao94xy-r', 'slug' => 'rubber-gym-mats', 'title' => 'TerraFit Active rubber rolls' ),
		array( 'id' => '1O-EiGRUY4r2UhEWogojSpa22AxIOaB7x', 'slug' => 'rubber-gym-mats', 'title' => 'Rubber roll gym mats' ),
		array( 'id' => '1DLn08OCt6g72rrviiWd2Mf23MGRLba5J', 'slug' => 'rubber-gym-mats', 'title' => 'Rubber roll gym mats installed' ),
		array( 'id' => '1oWth5q92itdcgA7Y9wYe6ByqjrGXntgC', 'slug' => 'rubber-gym-mats', 'title' => 'Rubber mats' ),

		// Custom / logo mats.
		array( 'id' => '1I4orsrCvu0ZTOU7WKUYIgYPI6qU7Gjh_', 'slug' => 'custom-logo-mats', 'title' => 'Customised mat', 'featured' => true ),

		// Kids play mats.
		array( 'id' => '1114NR1IT9b3W5C-zyrUBTVQHvslVc3oX', 'slug' => 'kids-play-mats', 'title' => 'Kids play area matting', 'featured' => true ),
		array( 'id' => '1phPRT5HMbTgUkk7NI84UpqJJIQp9XT8l', 'slug' => 'kids-play-mats', 'title' => 'Colourful EPDM play surface' ),

		// Project photos.
		array( 'id' => '1e7RC9z2pQOgU8gLszCSXQ8_LtPzUfZFV', 'slug' => 'projects', 'title' => 'Mat project' ),
		array( 'id' => '1bbzwAzII5BQY702xN8CKghSDdjxiEsIV', 'slug' => 'projects', 'title' => 'Mat project' ),
		array( 'id' => '1b2e_qENdXI-R7cagx12R117vi2veBjs_', 'slug' => 'projects', 'title' => 'Mat project' ),
		array( 'id' => '1o8G9og6lDgXU6UH8cleiU8HAhbxVxs02', 'slug' => 'projects', 'title' => 'Mat project' ),
		array( 'id' => '18ivfR9bIw_UaNSvLtCbdDljLrGEszmWM', 'slug' => 'projects', 'title' => 'Mat project' ),
		array( 'id' => '1jQVZ7ip4-gTH6dgCpiagmCrE0tvMQORl', 'slug' => 'projects', 'title' => 'Mat project' ),
		array( 'id' => '1JzxAM3SivT-aTHelqX88B9X125MY-EGY', 'slug' => 'projects', 'title' => 'Mat project' ),
		array( 'id' => '1XYCgf21EOe4SHTdJyI5jCk06kJT1yVmk', 'slug' => 'projects', 'title' => 'Mat project' ),
		array( 'id' => '1brsl82jxdawrgYnh16qObVKWuR5mH7Zl', 'slug' => 'projects', 'title' => 'Mat project' ),
		array( 'id' => '1oWh5ilNDckO0iXQIEjjlnkAJZyoxHfc8', 'slug' => 'projects', 'title' => 'Mat project' ),
		array( 'id' => '1xG5x4ulZopag8Vqh9WI72-XncoEjrEri', 'slug' => 'projects', 'title' => 'Mat project' ),
		array( 'id' => '19ydUSZsB9r7yth3qK1CvgJS0Y4W0US-S', 'slug' => 'projects', 'title' => 'Mat project' ),
		array( 'id' => '1_Z4Jfm6pyMQc0LXXJ7bzWyABuZvNnAuQ', 'slug' => 'projects', 'title' => 'Mat project' ),
		array( 'id' => '1z_v9Ne6ctrQl6guiOhTr_3LwhgK7SB7z', 'slug' => 'projects', 'title' => 'Poured rubber project' ),
		array( 'id' => '1uVeDulnbzXAeW6pX5bvTYLE4T2BRnYzZ', 'slug' => 'projects', 'title' => 'Poured rubber project' ),
		array( 'id' => '1WvjHAYC41qW8_KZam2jd7tswZmyOcXp6', 'slug' => 'projects', 'title' => 'Poured rubber project' ),
		array( 'id' => '1q2jm_dfbrbJKNwRtpWzVt8AC7Qz34ALU', 'slug' => 'projects', 'title' => 'Poured rubber project' ),
		array( 'id' => '1UUh--a5LsRFz_saXO5nPOax2tNqsgVrU', 'slug' => 'projects', 'title' => 'Poured rubber project' ),
		array( 'id' => '1eg_c42-WHzoG1ZzemdKds2vyGtO5cqQw', 'slug' => 'projects', 'title' => 'Poured rubber project' ),
		array( 'id' => '1ozpXs9d9V9Hjc0FhlTSn2vALjESPo-PD', 'slug' => 'projects', 'title' => 'Poured rubber project' ),
	);
}

/**
 * Import pending Drive files.
 *
 * @param int $limit Max files this run (0 = all). Keeps each request short on small hosts.
 * @return array{imported:int,featured:int,failed:int,remaining:int,notes:string[]}
 */
function cfm_drive_import( $limit = 0 ) {
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';
	if ( function_exists( 'set_time_limit' ) ) {
		@set_time_limit( 300 ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
	}

	$done   = (array) get_option( 'cfm_drive_imported', array() );
	$result = array( 'imported' => 0, 'featured' => 0, 'failed' => 0, 'remaining' => 0, 'notes' => array() );
	$count  = 0;

	foreach ( cfm_drive_manifest() as $file ) {
		if ( isset( $done[ $file['id'] ] ) && get_post( (int) $done[ $file['id'] ] ) ) {
			continue;
		}
		if ( $limit && $count >= $limit ) {
			++$result['remaining'];
			continue;
		}
		++$count;
		$page = cfm_get_page( $file['slug'] );
		if ( ! $page ) {
			$result['notes'][] = sprintf( 'No page "%s" — run the page installer first.', $file['slug'] );
			++$result['failed'];
			continue;
		}

		$tmp = download_url( 'https://drive.google.com/uc?export=download&id=' . rawurlencode( $file['id'] ), 120 );
		if ( is_wp_error( $tmp ) ) {
			$result['notes'][] = sprintf( '%s: %s', $file['id'], $tmp->get_error_message() );
			++$result['failed'];
			continue;
		}
		$ext = cfm_drive_sniff_ext( $tmp );
		if ( '' === $ext ) {
			wp_delete_file( $tmp );
			$result['notes'][] = sprintf( '%s: not an image or PDF (is the file still shared?).', $file['id'] );
			++$result['failed'];
			continue;
		}
		$title = isset( $file['title'] ) ? $file['title'] : $page->post_title;
		$id    = media_handle_sideload(
			array(
				'name'     => sanitize_file_name( $file['slug'] . '-' . sanitize_title( $title ) . '.' . $ext ),
				'tmp_name' => $tmp,
			),
			$page->ID,
			$title,
			array( 'post_excerpt' => isset( $file['excerpt'] ) ? $file['excerpt'] : '' )
		);
		if ( is_wp_error( $id ) ) {
			wp_delete_file( $tmp );
			$result['notes'][] = sprintf( '%s: %s', $file['id'], $id->get_error_message() );
			++$result['failed'];
			continue;
		}
		$done[ $file['id'] ] = $id;
		update_option( 'cfm_drive_imported', $done, false );
		++$result['imported'];

		if ( 'pdf' !== $ext ) {
			update_post_meta( $id, '_wp_attachment_image_alt', sprintf( '%s — %s', $title, cfm_opt( 'brand_name' ) ) );
			if ( ! empty( $file['featured'] ) && ! get_post_meta( $page->ID, '_thumbnail_id', true ) ) {
				set_post_thumbnail( $page->ID, $id );
				++$result['featured'];
			}
		}
	}
	return $result;
}

/**
 * File type from its first bytes (Drive download names carry no extension).
 *
 * @param string $path Temp file.
 * @return string jpg|png|webp|gif|pdf or ''.
 */
function cfm_drive_sniff_ext( $path ) {
	$head = (string) file_get_contents( $path, false, null, 0, 16 ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
	if ( 0 === strpos( $head, '%PDF' ) ) {
		return 'pdf';
	}
	if ( 0 === strpos( $head, "\xFF\xD8\xFF" ) ) {
		return 'jpg';
	}
	if ( 0 === strpos( $head, "\x89PNG" ) ) {
		return 'png';
	}
	if ( 0 === strpos( $head, 'GIF8' ) ) {
		return 'gif';
	}
	if ( 0 === strpos( $head, 'RIFF' ) && 'WEBP' === substr( $head, 8, 4 ) ) {
		return 'webp';
	}
	return '';
}

add_action( 'rest_api_init', 'cfm_drive_rest' );
/**
 * Administrator-only REST routes: run the installer and the Drive import.
 */
function cfm_drive_rest() {
	$admin = static function () {
		return current_user_can( 'manage_options' );
	};
	register_rest_route(
		'cfm/v1',
		'/drive-import',
		array(
			'methods'             => 'POST',
			'permission_callback' => $admin,
			'args'                => array( 'limit' => array( 'default' => 8, 'sanitize_callback' => 'absint' ) ),
			'callback'            => static function ( WP_REST_Request $request ) {
				return rest_ensure_response( cfm_drive_import( (int) $request['limit'] ) );
			},
		)
	);
	register_rest_route(
		'cfm/v1',
		'/install',
		array(
			'methods'             => 'POST',
			'permission_callback' => $admin,
			'callback'            => static function () {
				return rest_ensure_response( cfm_install() );
			},
		)
	);
}
