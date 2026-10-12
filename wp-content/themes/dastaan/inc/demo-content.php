<?php
/**
 * One-click demo products (Dastaan → Demo Products, or `wp dastaan demo import`).
 *
 * Creates craft categories, demo products with galleries, attributes, sale prices, reviews and
 * product videos, using only images/video bundled with the theme. Everything created is tracked
 * in the `dastaan_demo_content` option so "Remove demo products" deletes exactly that and nothing else.
 *
 * @package Dastaan
 */

defined( 'ABSPATH' ) || exit;

const DASTAAN_DEMO_OPTION = 'dastaan_demo_content';

/**
 * Demo catalogue definition.
 *
 * Image keys refer to files in assets/images (cat: categories/, hero: hero/).
 *
 * @return array
 */
function dastaan_demo_catalogue() {
	$set = __( 'Kurta fabric 2.5 m, bottom fabric 2.5 m, dupatta 2.25 m', 'dastaan' );

	return array(
		'categories' => array(
			'chikankari'    => __( 'Chikankari', 'dastaan' ),
			'mul-chanderi'  => __( 'Mul Chanderi', 'dastaan' ),
			'pure-organza'  => __( 'Pure Organza', 'dastaan' ),
			'zardozi'       => __( 'Zardozi', 'dastaan' ),
			'gotta-patti'   => __( 'Gotta Patti', 'dastaan' ),
			'mukaish-pitta' => __( 'Mukaish Pitta', 'dastaan' ),
			'festive-edit'  => __( 'Festive Edit', 'dastaan' ),
		),
		'products'   => array(
			array(
				'name'     => 'Noor — Ivory Mul Chanderi Chikankari Set',
				'cats'     => array( 'chikankari', 'mul-chanderi' ),
				'price'    => 12500,
				'image'    => 'cat:chikankari',
				'gallery'  => array( 'hero:hero-chikankari', 'cat:mul-chanderi', 'cat:mukaish-pitta' ),
				'video'    => true,
				'fabric'   => 'Mul Chanderi',
				'craft'    => 'Lucknowi Chikankari, Pearl Cut-dana',
				'colour'   => 'Ivory',
				'sales'    => 46,
				'featured' => true,
				'short'    => 'An heirloom ivory set in feather-light mul chanderi, hand-embroidered with Lucknowi shadow work and finished with pearl cut-dana.',
			),
			array(
				'name'     => 'Gulbahar — Onion Pink Pure Organza Set',
				'cats'     => array( 'pure-organza', 'chikankari' ),
				'price'    => 16800,
				'sale'     => 14280,
				'image'    => 'cat:pure-organza',
				'gallery'  => array( 'hero:hero-pastel-organza', 'cat:chikankari' ),
				'video'    => true,
				'fabric'   => 'Pure Organza Silk',
				'craft'    => 'Chikankari, Sequin Highlights',
				'colour'   => 'Onion Pink',
				'sales'    => 38,
				'featured' => true,
				'short'    => 'Sheer onion-pink organza with dense chikankari borders that catch the light — a soft statement for day weddings.',
			),
			array(
				'name'     => 'Shahi Mehfil — Maroon Velvet Zardozi Set',
				'cats'     => array( 'zardozi', 'festive-edit' ),
				'price'    => 24500,
				'image'    => 'cat:zardozi',
				'gallery'  => array( 'hero:hero-zardozi', 'cat:gotta-patti' ),
				'fabric'   => 'Silk Velvet',
				'craft'    => 'Zardozi, Dabka, Nakshi',
				'colour'   => 'Maroon',
				'stock'    => 1,
				'sales'    => 29,
				'featured' => true,
				'short'    => 'Rich maroon velvet worked in antique-gold zardozi florals — made for sangeet nights and trousseau trunks.',
			),
			array(
				'name'     => 'Rani Bagh — Magenta Gotta Patti Organza Set',
				'cats'     => array( 'gotta-patti', 'festive-edit' ),
				'price'    => 13900,
				'sale'     => 11815,
				'image'    => 'cat:gotta-patti',
				'gallery'  => array( 'cat:zardozi', 'cat:festive-edit' ),
				'video'    => true,
				'fabric'   => 'Organza',
				'craft'    => 'Gotta Patti, Zari Paisley',
				'colour'   => 'Magenta',
				'sales'    => 33,
				'featured' => true,
				'short'    => 'Jewel-toned magenta organza with hand-applied gotta patti paisleys from Jaipur karigars.',
			),
			array(
				'name'     => 'Chandni — White Georgette Mukaish Pitta Set',
				'cats'     => array( 'mukaish-pitta', 'chikankari' ),
				'price'    => 9800,
				'image'    => 'cat:mukaish-pitta',
				'gallery'  => array( 'cat:chikankari', 'hero:hero-chikankari' ),
				'fabric'   => 'Viscose Georgette',
				'craft'    => 'Mukaish, Pitta Work, Chikankari',
				'colour'   => 'White',
				'sales'    => 52,
				'featured' => true,
				'short'    => 'Moonlit white georgette scattered with hand-twisted mukaish stars and pitta-work florals.',
			),
			array(
				'name'     => 'Aasmani — Powder Blue Mul Chanderi Set',
				'cats'     => array( 'mul-chanderi', 'chikankari' ),
				'price'    => 11200,
				'image'    => 'cat:mul-chanderi',
				'gallery'  => array( 'hero:hero-pastel-organza', 'cat:pure-organza' ),
				'fabric'   => 'Mul Chanderi',
				'craft'    => 'Chikankari, Shadow Work',
				'colour'   => 'Powder Blue',
				'stock'    => 2,
				'sales'    => 41,
				'short'    => 'A breezy powder-blue mul chanderi set with all-over shadow-work buti — summer elegance, made by hand.',
			),
			array(
				'name'     => 'Utsav — Emerald Silk Festive Set',
				'cats'     => array( 'festive-edit', 'zardozi' ),
				'price'    => 18900,
				'sale'     => 16065,
				'image'    => 'cat:festive-edit',
				'gallery'  => array( 'cat:zardozi', 'hero:hero-zardozi' ),
				'video'    => true,
				'fabric'   => 'Chanderi Silk with Organza Dupatta',
				'craft'    => 'Zari Border, Sequin Butis',
				'colour'   => 'Emerald Green',
				'sales'    => 24,
				'short'    => 'Emerald silk paired with a sheer organza dupatta edged in hand-laid zari — festive, regal, unmistakably handmade.',
			),
			array(
				'name'     => 'Rang-e-Pastel — Lavender Organza Set',
				'cats'     => array( 'pure-organza' ),
				'price'    => 14500,
				'image'    => 'hero:hero-pastel-organza',
				'gallery'  => array( 'cat:pure-organza', 'cat:mul-chanderi' ),
				'fabric'   => 'Pure Organza Silk',
				'craft'    => 'Gota Lace, Sequin Scatter',
				'colour'   => 'Lavender',
				'sales'    => 19,
				'short'    => 'Lavender organza edged in delicate gota lace with a scatter of hand-stitched sequins.',
			),
			array(
				'name'     => 'Mehtaab — Ivory Chikankari Kurta Fabric',
				'cats'     => array( 'chikankari' ),
				'price'    => 6500,
				'image'    => 'hero:hero-chikankari',
				'gallery'  => array( 'cat:chikankari', 'cat:mukaish-pitta' ),
				'fabric'   => 'Cotton Mul',
				'craft'    => 'Lucknowi Chikankari',
				'colour'   => 'Ivory',
				'sales'    => 61,
				'set'      => __( 'Kurta fabric 2.75 m', 'dastaan' ),
				'short'    => 'An everyday heirloom: soft cotton mul with a hand-embroidered chikankari yoke and sleeve buti.',
			),
			array(
				'name'     => 'Begum — Wine Zardozi Dupatta',
				'cats'     => array( 'zardozi', 'festive-edit' ),
				'price'    => 8900,
				'image'    => 'hero:hero-zardozi',
				'gallery'  => array( 'cat:zardozi', 'cat:gotta-patti' ),
				'fabric'   => 'Silk Organza',
				'craft'    => 'Zardozi, Dabka',
				'colour'   => 'Wine',
				'stock'    => 1,
				'sales'    => 14,
				'set'      => __( 'Dupatta 2.5 m', 'dastaan' ),
				'short'    => 'A statement wine dupatta with zardozi corners to lift any plain kurta into occasion wear.',
			),
		),
		'variable'   => array(
			'name'    => 'Bahaar — Pastel Organza Chikankari Set',
			'cats'    => array( 'pure-organza', 'chikankari' ),
			'image'   => 'hero:hero-pastel-organza',
			'gallery' => array( 'cat:pure-organza', 'cat:mul-chanderi', 'cat:chikankari' ),
			'colours' => array(
				'Blush Pink'  => array( 15500, 'cat:pure-organza' ),
				'Powder Blue' => array( 15500, 'cat:mul-chanderi' ),
				'Ivory'       => array( 16200, 'cat:chikankari' ),
			),
			'short'   => 'Our bestselling organza chikankari set, offered in three pastels — pick the one that tells your story.',
		),
		'set_default' => $set,
		'reviews'     => array(
			array( 'Ananya Iyer', 5, 'The embroidery is even finer in person. My tailor said the fabric quality is exceptional.' ),
			array( 'Priya Malhotra', 5, 'Packed beautifully and arrived in four days. The colour is exactly as shown.' ),
			array( 'Sneha Kulkarni', 4, 'Gorgeous work. The dupatta is lighter than I expected but drapes wonderfully.' ),
			array( 'Fatima Shaikh', 5, 'Wore it to my cousin\'s mehendi and got so many compliments. Worth every rupee.' ),
		),
	);
}

/**
 * Stored record of created demo content.
 *
 * @return array
 */
function dastaan_demo_record() {
	$r = get_option( DASTAAN_DEMO_OPTION, array() );

	return wp_parse_args( is_array( $r ) ? $r : array(), array( 'products' => array(), 'attachments' => array(), 'terms' => array() ) );
}

/**
 * Import a bundled theme file into the media library (once).
 *
 * @param string $rel     Path relative to the theme's assets/ folder.
 * @param array  $record  Record (by reference) to track created attachments.
 * @return int Attachment ID or 0.
 */
function dastaan_demo_attachment( $rel, &$record ) {
	static $cache = array();
	if ( isset( $cache[ $rel ] ) ) {
		return $cache[ $rel ];
	}
	$existing = get_posts(
		array(
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'meta_key'       => '_dastaan_demo_src', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'meta_value'     => $rel, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
		)
	);
	if ( $existing ) {
		$cache[ $rel ] = (int) $existing[0];
		return $cache[ $rel ];
	}
	$source = DASTAAN_DIR . 'assets/' . $rel;
	if ( ! file_exists( $source ) ) {
		return 0;
	}
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';

	$tmp = wp_tempnam( basename( $source ) );
	if ( ! $tmp || ! copy( $source, $tmp ) ) {
		return 0;
	}
	$id = media_handle_sideload( array( 'name' => 'dastaan-demo-' . basename( $source ), 'tmp_name' => $tmp ), 0 );
	if ( is_wp_error( $id ) ) {
		wp_delete_file( $tmp );
		return 0;
	}
	$label = ucwords( str_replace( array( '-', '.jpg', '.mp4' ), array( ' ', '', '' ), basename( $source ) ) );
	update_post_meta( $id, '_dastaan_demo_src', $rel );
	update_post_meta( $id, '_wp_attachment_image_alt', sprintf( /* translators: %s: craft name */ __( '%s — hand embroidery close-up', 'dastaan' ), $label ) );
	$record['attachments'][] = (int) $id;
	$cache[ $rel ]           = (int) $id;

	return (int) $id;
}

/**
 * Resolve an image key like "cat:zardozi" to an attachment ID.
 *
 * @param string $key    Key.
 * @param array  $record Record.
 * @return int
 */
function dastaan_demo_image( $key, &$record ) {
	list( $type, $file ) = explode( ':', $key );
	$folder              = 'hero' === $type ? 'images/hero/' : 'images/categories/';

	return dastaan_demo_attachment( $folder . $file . '.jpg', $record );
}

/**
 * Text attributes for a demo product.
 *
 * @param array  $p   Product definition.
 * @param string $set Default set contents.
 * @return WC_Product_Attribute[]
 */
function dastaan_demo_attributes( $p, $set ) {
	$rows  = array(
		__( 'Fabric', 'dastaan' )       => $p['fabric'],
		__( 'Craft', 'dastaan' )        => $p['craft'],
		__( 'Colour', 'dastaan' )       => $p['colour'],
		__( 'Set contents', 'dastaan' ) => isset( $p['set'] ) ? $p['set'] : $set,
	);
	$attrs = array();
	$pos   = 0;
	foreach ( $rows as $label => $value ) {
		$a = new WC_Product_Attribute();
		$a->set_name( $label );
		$a->set_options( array( $value ) );
		$a->set_visible( true );
		$a->set_position( $pos++ );
		$attrs[] = $a;
	}

	return $attrs;
}

/**
 * Long description shared by the demo products.
 *
 * @param array $p Product definition.
 * @return string
 */
function dastaan_demo_description( $p ) {
	return '<p>' . esc_html( $p['short'] ) . '</p>'
		. '<p>' . esc_html__( 'Every Dastaan piece is sourced directly from the artisan families who embroider it. Depending on the density of work, a single set takes between three and six weeks to complete by hand — small irregularities are the signature of the needle, not a flaw.', 'dastaan' ) . '</p>'
		. '<h3>' . esc_html__( 'Styling notes', 'dastaan' ) . '</h3>'
		. '<ul><li>' . esc_html__( 'Stitch as a straight kurta, anarkali or A-line with palazzos.', 'dastaan' ) . '</li>'
		. '<li>' . esc_html__( 'Our stylists share a free stitching guide on WhatsApp after purchase.', 'dastaan' ) . '</li>'
		. '<li>' . esc_html__( 'Pairs beautifully with pearl or polki jewellery.', 'dastaan' ) . '</li></ul>';
}

/**
 * Add approved reviews to a product.
 *
 * @param int   $product_id Product.
 * @param array $reviews    Reviews pool.
 * @param int   $count      How many.
 * @param int   $offset     Rotation offset.
 */
function dastaan_demo_reviews( $product_id, $reviews, $count, $offset ) {
	for ( $i = 0; $i < $count; $i++ ) {
		$r   = $reviews[ ( $i + $offset ) % count( $reviews ) ];
		$cid = wp_insert_comment(
			array(
				'comment_post_ID'      => $product_id,
				'comment_author'       => $r[0],
				'comment_author_email' => sanitize_title( $r[0] ) . '@example.com',
				'comment_content'      => $r[2],
				'comment_type'         => 'review',
				'comment_approved'     => 1,
				'comment_date'         => gmdate( 'Y-m-d H:i:s', time() - ( $i + 1 ) * 5 * DAY_IN_SECONDS ),
			)
		);
		if ( $cid ) {
			update_comment_meta( $cid, 'rating', $r[1] );
			update_comment_meta( $cid, 'verified', 1 );
		}
	}
	if ( class_exists( 'WC_Comments' ) ) {
		WC_Comments::clear_transients( $product_id );
	}
}

/**
 * Run the import.
 *
 * @return array|WP_Error Summary counts.
 */
function dastaan_demo_import() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return new WP_Error( 'dastaan_no_wc', __( 'Please install and activate WooCommerce first.', 'dastaan' ) );
	}
	$record = dastaan_demo_record();
	if ( $record['products'] ) {
		return new WP_Error( 'dastaan_demo_exists', __( 'Demo products are already imported. Remove them first to import again.', 'dastaan' ) );
	}
	if ( function_exists( 'set_time_limit' ) ) {
		set_time_limit( 300 ); // phpcs:ignore Squiz.PHP.DiscouragedFunctions.Discouraged -- image sub-sizes take a while on shared hosting.
	}

	$data = dastaan_demo_catalogue();

	// Categories (reuse existing ones with the same slug).
	$cat_ids = array();
	$order   = 0;
	foreach ( $data['categories'] as $slug => $name ) {
		$term = get_term_by( 'slug', $slug, 'product_cat' );
		if ( ! $term ) {
			$res = wp_insert_term( $name, 'product_cat', array( 'slug' => $slug ) );
			if ( is_wp_error( $res ) ) {
				continue;
			}
			$term_id             = (int) $res['term_id'];
			$record['terms'][]   = $term_id;
			update_term_meta( $term_id, 'order', $order );
			$thumb = dastaan_demo_image( 'cat:' . $slug, $record );
			if ( $thumb ) {
				update_term_meta( $term_id, 'thumbnail_id', $thumb );
			}
		} else {
			$term_id = (int) $term->term_id;
		}
		$cat_ids[ $slug ] = $term_id;
		++$order;
	}

	$video_id  = dastaan_demo_attachment( 'video/dastaan-craft-closeup.mp4', $record );
	$poster_id = dastaan_demo_attachment( 'video/dastaan-craft-closeup-poster.jpg', $record );

	// Simple products. Older items first so "New in" shows the first ones on top.
	$i = 0;
	foreach ( array_reverse( $data['products'] ) as $p ) {
		$product = new WC_Product_Simple();
		$product->set_name( $p['name'] );
		$product->set_status( 'publish' );
		$product->set_catalog_visibility( 'visible' );
		$product->set_regular_price( (string) $p['price'] );
		if ( ! empty( $p['sale'] ) ) {
			$product->set_sale_price( (string) $p['sale'] );
		}
		$product->set_short_description( $p['short'] );
		$product->set_description( dastaan_demo_description( $p ) );
		$product->set_sku( 'DST-' . ( 1101 + $i ) );
		$product->set_category_ids( array_values( array_intersect_key( $cat_ids, array_flip( $p['cats'] ) ) ) );
		$product->set_featured( ! empty( $p['featured'] ) );
		$product->set_image_id( dastaan_demo_image( $p['image'], $record ) );
		$product->set_gallery_image_ids( array_filter( array_map( fn( $k ) => dastaan_demo_image( $k, $record ), $p['gallery'] ) ) );
		$product->set_attributes( dastaan_demo_attributes( $p, $data['set_default'] ) );
		$product->set_weight( '0.6' );
		if ( ! empty( $p['stock'] ) ) {
			$product->set_manage_stock( true );
			$product->set_stock_quantity( (int) $p['stock'] );
		}
		$product->set_reviews_allowed( true );
		// Stagger dates (oldest first) so "New in" ordering and "New" badges look natural.
		$product->set_date_created( time() - ( count( $data['products'] ) - $i ) * 5 * DAY_IN_SECONDS );
		$product_id = $product->save();
		if ( ! $product_id ) {
			continue;
		}
		update_post_meta( $product_id, 'total_sales', (int) $p['sales'] );
		update_post_meta( $product_id, '_dastaan_demo', 1 );
		if ( ! empty( $p['video'] ) && $video_id ) {
			update_post_meta( $product_id, '_dastaan_product_video', $video_id );
			if ( $poster_id ) {
				update_post_meta( $product_id, '_dastaan_product_video_poster', $poster_id );
			}
		}
		dastaan_demo_reviews( $product_id, $data['reviews'], 1 + ( $i % 3 ), $i );
		$record['products'][] = (int) $product_id;
		++$i;
	}

	// Variable product (colour swatches as a dropdown).
	$v      = $data['variable'];
	$parent = new WC_Product_Variable();
	$parent->set_name( $v['name'] );
	$parent->set_status( 'publish' );
	$parent->set_short_description( $v['short'] );
	$parent->set_description( dastaan_demo_description( array( 'short' => $v['short'] ) ) );
	$parent->set_sku( 'DST-1201' );
	$parent->set_category_ids( array_values( array_intersect_key( $cat_ids, array_flip( $v['cats'] ) ) ) );
	$parent->set_featured( true );
	$parent->set_image_id( dastaan_demo_image( $v['image'], $record ) );
	$parent->set_gallery_image_ids( array_filter( array_map( fn( $k ) => dastaan_demo_image( $k, $record ), $v['gallery'] ) ) );
	$colour = new WC_Product_Attribute();
	$colour->set_name( __( 'Colour', 'dastaan' ) );
	$colour->set_options( array_keys( $v['colours'] ) );
	$colour->set_visible( true );
	$colour->set_variation( true );
	$fabric = new WC_Product_Attribute();
	$fabric->set_name( __( 'Fabric', 'dastaan' ) );
	$fabric->set_options( array( 'Pure Organza Silk' ) );
	$fabric->set_visible( true );
	$fabric->set_position( 1 );
	$parent->set_attributes( array( $colour, $fabric ) );
	$parent_id = $parent->save();
	if ( $parent_id ) {
		$k = 0;
		foreach ( $v['colours'] as $name => $conf ) {
			$variation = new WC_Product_Variation();
			$variation->set_parent_id( $parent_id );
			$variation->set_attributes( array( sanitize_title( __( 'Colour', 'dastaan' ) ) => $name ) );
			$variation->set_regular_price( (string) $conf[0] );
			$variation->set_image_id( dastaan_demo_image( $conf[1], $record ) );
			$variation->set_sku( 'DST-1201-' . ( ++$k ) );
			$variation->save();
		}
		WC_Product_Variable::sync( $parent_id );
		update_post_meta( $parent_id, 'total_sales', 70 );
		update_post_meta( $parent_id, '_dastaan_demo', 1 );
		if ( $video_id ) {
			update_post_meta( $parent_id, '_dastaan_product_video', $video_id );
			if ( $poster_id ) {
				update_post_meta( $parent_id, '_dastaan_product_video_poster', $poster_id );
			}
		}
		dastaan_demo_reviews( $parent_id, $data['reviews'], 4, 0 );
		$record['products'][] = (int) $parent_id;
	}

	update_option( DASTAAN_DEMO_OPTION, $record, false );
	if ( function_exists( 'wc_delete_product_transients' ) ) {
		wc_delete_product_transients();
	}

	return array(
		'products'    => count( $record['products'] ),
		'attachments' => count( $record['attachments'] ),
		'terms'       => count( $record['terms'] ),
	);
}

/**
 * Remove everything the importer created.
 *
 * @return array Summary counts.
 */
function dastaan_demo_remove() {
	$record  = dastaan_demo_record();
	$removed = array( 'products' => 0, 'attachments' => 0, 'terms' => 0 );
	foreach ( $record['products'] as $id ) {
		$product = function_exists( 'wc_get_product' ) ? wc_get_product( $id ) : null;
		if ( $product ) {
			foreach ( $product->get_children() as $child ) {
				wp_delete_post( $child, true );
			}
			$product->delete( true );
			++$removed['products'];
		}
	}
	foreach ( $record['attachments'] as $id ) {
		if ( wp_delete_attachment( $id, true ) ) {
			++$removed['attachments'];
		}
	}
	foreach ( $record['terms'] as $id ) {
		$term = get_term( $id, 'product_cat' );
		if ( $term && ! is_wp_error( $term ) && 0 === (int) $term->count && ! is_wp_error( wp_delete_term( $id, 'product_cat' ) ) ) {
			++$removed['terms'];
		}
	}
	delete_option( DASTAAN_DEMO_OPTION );
	if ( function_exists( 'wc_delete_product_transients' ) ) {
		wc_delete_product_transients();
	}

	return $removed;
}

/**
 * Admin page.
 */
function dastaan_demo_admin_menu() {
	add_submenu_page( 'dastaan-welcome', __( 'Demo Products', 'dastaan' ), __( 'Demo Products', 'dastaan' ), 'manage_options', 'dastaan-demo', 'dastaan_demo_page' );
}
add_action( 'admin_menu', 'dastaan_demo_admin_menu', 20 );

/**
 * Demo page markup + actions.
 */
function dastaan_demo_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$notice = '';
	$type   = 'success';
	if ( isset( $_POST['dastaan_demo_action'] ) && check_admin_referer( 'dastaan_demo' ) ) {
		$action = sanitize_key( wp_unslash( $_POST['dastaan_demo_action'] ) );
		if ( 'import' === $action ) {
			$res = dastaan_demo_import();
			if ( is_wp_error( $res ) ) {
				$notice = $res->get_error_message();
				$type   = 'error';
			} else {
				/* translators: 1: products 2: images 3: categories */
				$notice = sprintf( __( 'Imported %1$d demo products, %2$d media files and %3$d categories.', 'dastaan' ), $res['products'], $res['attachments'], $res['terms'] );
			}
		} elseif ( 'remove' === $action ) {
			$res = dastaan_demo_remove();
			/* translators: 1: products 2: images 3: categories */
			$notice = sprintf( __( 'Removed %1$d demo products, %2$d media files and %3$d empty categories.', 'dastaan' ), $res['products'], $res['attachments'], $res['terms'] );
		}
	}
	$record = dastaan_demo_record();
	$has    = ! empty( $record['products'] );
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Demo Products', 'dastaan' ); ?></h1>
		<?php if ( $notice ) : ?>
			<div class="notice notice-<?php echo esc_attr( $type ); ?>"><p><?php echo esc_html( $notice ); ?></p></div>
		<?php endif; ?>
		<div class="card" style="max-width:720px">
			<p><?php esc_html_e( 'Fill the shop with 11 styled demo pieces so you can preview every part of the theme: craft categories with images, product galleries with a product video, sale prices, one-of-one stock badges, a colour-variable set, reviews and bestsellers.', 'dastaan' ); ?></p>
			<p><?php esc_html_e( 'Only images and the video bundled with the theme are used. Your existing products, pages and settings are never changed, and "Remove demo products" deletes only what was imported here.', 'dastaan' ); ?></p>
			<?php if ( ! class_exists( 'WooCommerce' ) ) : ?>
				<p><strong><?php esc_html_e( 'Activate WooCommerce to import demo products.', 'dastaan' ); ?></strong></p>
			<?php elseif ( $has ) : ?>
				<p><span class="dashicons dashicons-yes-alt" style="color:#2e6b4f"></span> <?php echo esc_html( sprintf( /* translators: %d: count */ _n( '%d demo product is installed.', '%d demo products are installed.', count( $record['products'] ), 'dastaan' ), count( $record['products'] ) ) ); ?>
					<a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" target="_blank"><?php esc_html_e( 'View shop', 'dastaan' ); ?></a></p>
				<form method="post" onsubmit="return confirm('<?php echo esc_js( __( 'Delete all demo products and their images?', 'dastaan' ) ); ?>');">
					<?php wp_nonce_field( 'dastaan_demo' ); ?>
					<button class="button" name="dastaan_demo_action" value="remove"><?php esc_html_e( 'Remove demo products', 'dastaan' ); ?></button>
				</form>
			<?php else : ?>
				<form method="post">
					<?php wp_nonce_field( 'dastaan_demo' ); ?>
					<button class="button button-primary button-hero" name="dastaan_demo_action" value="import"><?php esc_html_e( 'Import demo products', 'dastaan' ); ?></button>
				</form>
			<?php endif; ?>
		</div>
	</div>
	<?php
}

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	/**
	 * `wp dastaan demo import|remove`
	 *
	 * @param array $args Positional args.
	 */
	WP_CLI::add_command(
		'dastaan demo',
		function ( $args ) {
			$cmd = isset( $args[0] ) ? $args[0] : '';
			if ( 'import' === $cmd ) {
				$res = dastaan_demo_import();
				if ( is_wp_error( $res ) ) {
					WP_CLI::error( $res->get_error_message() );
				}
				WP_CLI::success( sprintf( 'Imported %d products, %d media files, %d categories.', $res['products'], $res['attachments'], $res['terms'] ) );
			} elseif ( 'remove' === $cmd ) {
				$res = dastaan_demo_remove();
				WP_CLI::success( sprintf( 'Removed %d products, %d media files, %d categories.', $res['products'], $res['attachments'], $res['terms'] ) );
			} else {
				WP_CLI::error( 'Usage: wp dastaan demo <import|remove>' );
			}
		}
	);
}
