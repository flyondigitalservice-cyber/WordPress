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

/**
 * Dubai Blinds Hub: wallpaper, flooring and carpet catalogue collections (Google Drive folders of PDFs).
 * Each entry: [name, description, Drive folder ID].
 */
function dbh_catalogue_sections() {
	return array(
		array(
			'Wallpaper',
			'Wallpaper <em>catalogues</em>',
			'Textured, natural, kids and mural wallpaper collections. Open a collection to browse the PDF pattern books.',
			array(
				array( 'Korean wall coverings', 'Artis, Living, Motive, Natural, Decent, Metropolis, Beyond', '1TEWSNIpPAZY2zBNjsdUFBSwvyyRxxocB' ),
				array( 'Special wall coverings', 'Acoustic, Belgian, Beton, Grasscloth and designer papers', '1n4yFIfCdOJwPFDTezL23CXOooZ5OjX9D' ),
				array( 'Sisal &amp; jute wallpaper', 'Natural-fibre wallcoverings', '13HGzOf9igy7QwE3WAwtBqY8A9Zi7ytE1' ),
				array( 'Custom wallpaper', 'Horizons, Serenade and more', '10fQZA5x5HsX7Bax9FrYN3kYw-vPr-Hlq' ),
				array( 'Kids wallpaper', 'DreamWorld and Tiny pattern books', '1bfa0kYz1FClTO2-lizY1OQ0rzg59gowd' ),
				array( 'Wall murals', 'Chinoiserie and Daisy Bennett mural collections', '105EY-RWe68ReYSKi4uyz4pCbJ2FcDy8e' ),
			),
		),
		array(
			'Flooring',
			'Flooring <em>catalogues</em>',
			'Laminate, SPC, LVT, vinyl and gym flooring ranges.',
			array(
				array( 'Laminate flooring', 'AC2, Dynamic, Exquisit, Glamour and Robusto ranges', '1tnHCuNQ3swkj0l7ErazMwfIjM5woW-yz' ),
				array( 'SPC flooring', 'Plank, herringbone and concrete-look SPC', '1_5UltCRPGsfNgC_LDPmXWh_hWGxBHZlz' ),
				array( 'LVT flooring', 'Luxury vinyl tile collections', '10QIvF-uLVqOzXcSSnOGV67EqrVHois6t' ),
				array( 'Vinyl flooring', 'Ruby Acoustic, Ruby Compact and more', '1AQz8EdkP_tpqpjpya_KDAw4PVOlyBP1M' ),
				array( 'Hospital vinyl', 'Hygienic vinyl for clinics and hospitals', '15sNhXVXtUL9keQzAXOMPhk-jXMK7ExXr' ),
				array( 'Gym flooring', 'Rubber, EPDM and vinyl gym floors', '1Zm4axcc7TXNL9RP_RihYTu-DMz9pGQqm' ),
			),
		),
		array(
			'Carpets',
			'Carpet <em>catalogues</em>',
			'Wall to wall carpet, carpet tiles and mosque carpet collections.',
			array(
				array( 'Wall to wall carpets', 'Antibes, Atticus, Berlin, Bulgari, Cadiz, Coco and more', '1Ycw_jPbWfIaKMldhfTakB5N-7nZBL5hD' ),
				array( 'Carpet tiles', 'Commercial carpet tile ranges', '1YNI81J4er5Odvn_zx1toVXqCwD1jjVLM' ),
				array( 'Mosque carpets', 'Prayer hall and mosque carpets', '18MEmJjzR9zs2rnK91bTaKxZdr1BObty2' ),
			),
		),
	);
}
