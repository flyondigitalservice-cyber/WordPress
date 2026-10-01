<?php
/**
 * Fabric catalogues and brochures. The PDFs are large (3–17 MB), so they stay on
 * Google Drive and open from clickable buttons instead of being stored in the theme.
 * Make sure each Drive file/folder is shared as "Anyone with the link can view".
 *
 * @package dce-vibe
 */

defined( 'ABSPATH' ) || exit;

function dce_drive_file( $id ) {
	return 'https://drive.google.com/file/d/' . $id . '/view';
}

function dce_drive_folder( $id ) {
	return 'https://drive.google.com/drive/folders/' . $id;
}

function dce_catalogues() {
	return array(
		'curtain' => array(
			array( 'Stellar', 'Curtain fabric collection · CV 916', '1JcLP-vCrRfCtqZ6isRSN7dyRy7bhSwWU', '16.6 MB' ),
			array( 'Matrix', 'Curtain fabric e-catalogue', '1RQpeXrK8OE1Upvsz1S4pXKDY0WaJ7EV9', '13 MB' ),
			array( 'Gems', 'Curtain fabric collection · 917', '1Os81n2JDRQHDgu5eUsyXDjBF5_716Gld', '10.3 MB' ),
			array( 'Linen Life', 'Linen-look fabrics · 414', '1ro1hERYaN42n1HWBkROP1gral6c3Xee4', '10.5 MB' ),
			array( 'Splendid', 'Curtain fabric collection · 249', '1a05-I59wuo3GyneWir-0WB4jqmJ_W_9r', '5.9 MB' ),
			array( 'Artisan VIII', 'Curtain fabric collection', '1MWYWQRf7GfMxe8MmXwxxgM2RAe19mJS_', '7.1 MB' ),
			array( 'Moods — Nottingham', 'Curtain fabric e-catalogue', '1PGkZxWaUBFzVnh-1BWrLgTEW3lZFqB8v', '4.2 MB' ),
			array( 'Awesome I', 'Curtain fabric collection', '1YfRE4azycybu1TRTwYAv0OGeKIp38UVC', '4.3 MB' ),
			array( 'Awesome III', 'Curtain fabric collection', '1ZF3VULU5dZUpU5_F92VlhWgI08w7uGad', '3.7 MB' ),
			array( 'Awesome IV', 'Curtain fabric collection', '1dMMh7mY4qRf9dFBnLmjVXLGZStijkZdW', '4.4 MB' ),
			array( 'Awesome V', 'Curtain fabric collection', '1EvB-E9WyKaMnRRJZ01VrUa17taoCZKEu', '5.2 MB' ),
			array( 'Awesome VI', 'Curtain fabric collection', '10eklmrh1s9XuogVUzC9j2NUXVTeMzpkP', '3.3 MB' ),
			array( 'Awesome VII', 'Curtain fabric collection', '1X-TCnoD0YeTzhHOa97-Nmxbe-jFe18Xj', '5 MB' ),
			array( 'Awesome VIII', 'Curtain fabric collection', '1sRnJ7qaWGGb6Uvd6reIUKmwnMWuM5iO_', '4.5 MB' ),
		),
		'folders' => array(
			array( 'U Collection', 'Fabric collection images', '1N-h-ySQNt204vD3uRV_8edeGENbs4i0m' ),
			array( 'R Collection', 'Fabric collection images', '15gF6TfWVuZeNGjTdSqv78nVT2O212yix' ),
			array( 'D3 Catalogue', 'Fabric catalogue set', '1H98OUCv_GeevKOdJF9ECsRnvxzZKFOiM' ),
			array( 'Sunbrella Outdoor Fabrics', 'Outdoor-rated fabrics for terraces and majlis', '1yfdt7LGaQrwVBtEYlQFeTbF2h2iI_E7Y' ),
		),
		'brochures' => array(
			array( 'Custom Print on Fabric', 'Print photos and artwork on curtains and blinds', '195b9Z4Cw-2oL3_8PdogrP3ZOM4KnR7-E', '1.5 MB', 'printed-blinds' ),
			array( 'Wire-Free Curtain Motor', 'Blindtex medium wire-free motor brochure', '1g6uymZHU8CkLJ059npVs-87AVbmef4VI', '1.3 MB', 'motorized-curtains' ),
		),
		'all_folder' => '1XLNOFa1i8SoZggdzri9_YqX3P1WIONSK',
		'videos'     => array(
			'printed'  => '1USS1rMfctfaeDKIx3GxxP79znyh7JQT1',
			'projects' => '1fy97cch9K4WiVXaW9dtEQquXD6I5yaHD',
		),
	);
}
