<?php
/**
 * Products: custom post type, collections and product meta.
 *
 * @package HadhramiFashion
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'init',
	function () {
		register_post_type(
			'hf_product',
			array(
				'labels'       => array(
					'name'          => __( 'Products', 'hadhramifashion' ),
					'singular_name' => __( 'Product', 'hadhramifashion' ),
					'add_new_item'  => __( 'Add product', 'hadhramifashion' ),
					'edit_item'     => __( 'Edit product', 'hadhramifashion' ),
					'all_items'     => __( 'All Products', 'hadhramifashion' ),
				),
				'public'       => true,
				'has_archive'  => 'collection',
				'rewrite'      => array( 'slug' => 'product' ),
				'menu_icon'    => 'dashicons-businessman',
				'menu_position' => 4,
				'supports'     => array( 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes' ),
				'show_in_rest' => true,
			)
		);

		register_taxonomy(
			'hf_collection',
			'hf_product',
			array(
				'labels'            => array(
					'name'          => __( 'Collections', 'hadhramifashion' ),
					'singular_name' => __( 'Collection', 'hadhramifashion' ),
				),
				'hierarchical'      => true,
				'public'            => true,
				'rewrite'           => array( 'slug' => 'shop' ),
				'show_admin_column' => true,
				'show_in_rest'      => true,
			)
		);

		register_taxonomy(
			'hf_occasion',
			'hf_product',
			array(
				'labels'            => array(
					'name'          => __( 'Occasions', 'hadhramifashion' ),
					'singular_name' => __( 'Occasion', 'hadhramifashion' ),
				),
				'hierarchical'      => true,
				'public'            => true,
				'rewrite'           => array( 'slug' => 'occasion' ),
				'show_admin_column' => true,
				'show_in_rest'      => true,
			)
		);
	}
);

/**
 * Product meta fields: key => array( label, type ).
 *
 * @return array
 */
function hf_product_fields() {
	return array(
		'_hf_price'        => array( __( 'Price', 'hadhramifashion' ), 'number' ),
		'_hf_subtitle'     => array( __( 'Subtitle (e.g. “Two-piece suit · Wool flannel”)', 'hadhramifashion' ), 'text' ),
		'_hf_fabric'       => array( __( 'Cloth', 'hadhramifashion' ), 'text' ),
		'_hf_colour'       => array( __( 'Colour family', 'hadhramifashion' ), 'text' ),
		'_hf_drive_image'  => array( __( 'Google Drive image ID (used when no featured image is set)', 'hadhramifashion' ), 'text' ),
		'_hf_drive_swatch' => array( __( 'Google Drive cloth swatch ID', 'hadhramifashion' ), 'text' ),
		'_hf_express'      => array( __( 'In stock for 2-hour express delivery', 'hadhramifashion' ), 'checkbox' ),
	);
}

add_action(
	'add_meta_boxes',
	function () {
		add_meta_box( 'hf_product_details', __( 'Tailoring details', 'hadhramifashion' ), 'hf_render_product_box', 'hf_product', 'normal', 'high' );
	}
);

/**
 * Product meta box.
 *
 * @param WP_Post $post Post.
 */
function hf_render_product_box( $post ) {
	wp_nonce_field( 'hf_product_save', 'hf_product_nonce' );
	echo '<table class="form-table" role="presentation">';
	foreach ( hf_product_fields() as $key => $field ) {
		$value = get_post_meta( $post->ID, $key, true );
		echo '<tr><th><label for="' . esc_attr( $key ) . '">' . esc_html( $field[0] ) . '</label></th><td>';
		if ( 'checkbox' === $field[1] ) {
			printf( '<input type="checkbox" id="%1$s" name="%1$s" value="1" %2$s>', esc_attr( $key ), checked( $value, '1', false ) );
		} else {
			printf(
				'<input type="%3$s" step="any" class="regular-text" id="%1$s" name="%1$s" value="%2$s">',
				esc_attr( $key ),
				esc_attr( $value ),
				esc_attr( $field[1] )
			);
		}
		echo '</td></tr>';
	}
	echo '</table>';
}

add_action(
	'save_post_hf_product',
	function ( $post_id ) {
		if ( ! isset( $_POST['hf_product_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['hf_product_nonce'] ), 'hf_product_save' ) ) {
			return;
		}
		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		foreach ( hf_product_fields() as $key => $field ) {
			if ( 'checkbox' === $field[1] ) {
				update_post_meta( $post_id, $key, isset( $_POST[ $key ] ) ? '1' : '' );
				continue;
			}
			$value = isset( $_POST[ $key ] ) ? sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) : '';
			update_post_meta( $post_id, $key, 'number' === $field[1] ? (string) floatval( $value ) : $value );
		}
	}
);

/**
 * Collection slug of a product: `suits` or `shirts`.
 *
 * @param int $post_id Product ID.
 * @return string
 */
function hf_product_kind( $post_id ) {
	$terms = get_the_terms( $post_id, 'hf_collection' );
	if ( $terms && ! is_wp_error( $terms ) ) {
		foreach ( $terms as $term ) {
			if ( in_array( $term->slug, array( 'suits', 'shirts' ), true ) ) {
				return $term->slug;
			}
		}
	}
	return 'shirts';
}

/**
 * Print a product's main image.
 *
 * @param int    $post_id Product ID.
 * @param int    $width   Requested width.
 * @param string $class   CSS class.
 * @param bool   $lazy    Lazy load.
 */
function hf_product_image( $post_id, $width = 900, $class = '', $lazy = true ) {
	if ( has_post_thumbnail( $post_id ) ) {
		echo get_the_post_thumbnail(
			$post_id,
			'hf-portrait',
			array(
				'class'   => $class,
				'loading' => $lazy ? 'lazy' : 'eager',
			)
		);
		return;
	}
	$drive = get_post_meta( $post_id, '_hf_drive_image', true );
	if ( $drive ) {
		hf_drive_img( $drive, get_the_title( $post_id ), $width, $class, $lazy );
	}
}

/**
 * Data the front end needs to put a product in the bag.
 *
 * @param int $post_id Product ID.
 * @return array
 */
function hf_product_payload( $post_id ) {
	$drive = get_post_meta( $post_id, '_hf_drive_image', true );
	$thumb = has_post_thumbnail( $post_id ) ? get_the_post_thumbnail_url( $post_id, 'medium' ) : hf_drive_url( $drive, 300 );
	return array(
		'id'      => $post_id,
		'title'   => get_the_title( $post_id ),
		'price'   => (float) get_post_meta( $post_id, '_hf_price', true ),
		'kind'    => hf_product_kind( $post_id ),
		'express' => '1' === get_post_meta( $post_id, '_hf_express', true ),
		'image'   => $thumb,
		'url'     => get_permalink( $post_id ),
	);
}

/**
 * Collection archives: show more per page, ordered as curated.
 */
add_action(
	'pre_get_posts',
	function ( $query ) {
		if ( is_admin() || ! $query->is_main_query() ) {
			return;
		}
		if ( $query->is_post_type_archive( 'hf_product' ) || $query->is_tax( array( 'hf_collection', 'hf_occasion' ) ) ) {
			$query->set( 'posts_per_page', 24 );
			$query->set( 'orderby', array( 'menu_order' => 'ASC', 'date' => 'DESC' ) );

			$meta   = array();
			$colour = isset( $_GET['colour'] ) ? sanitize_text_field( wp_unslash( $_GET['colour'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
			if ( $colour ) {
				$meta[] = array( 'key' => '_hf_colour', 'value' => $colour );
			}
			if ( isset( $_GET['express'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
				$meta[] = array( 'key' => '_hf_express', 'value' => '1' );
			}
			if ( $meta ) {
				$query->set( 'meta_query', $meta );
			}
		}
	}
);
