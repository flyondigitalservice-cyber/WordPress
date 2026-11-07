<?php
/**
 * "Products" post type with export specifications.
 *
 * @package Tasdir
 */

defined( 'ABSPATH' ) || exit;

/**
 * Spec fields shown on product cards, product pages and in schema.
 *
 * @return array key => label
 */
function tasdir_product_fields() {
	return array(
		'varieties'  => __( 'Varieties', 'tasdir' ),
		'origin'     => __( 'Origin', 'tasdir' ),
		'packaging'  => __( 'Packaging', 'tasdir' ),
		'moq'        => __( 'Min. order', 'tasdir' ),
		'season'     => __( 'Availability', 'tasdir' ),
		'shelf_life' => __( 'Shelf life', 'tasdir' ),
		'grade'      => __( 'Grade', 'tasdir' ),
	);
}

/**
 * Register the product post type.
 */
function tasdir_register_products() {
	register_post_type(
		'tasdir_product',
		array(
			'labels'       => array(
				'name'          => __( 'Products', 'tasdir' ),
				'singular_name' => __( 'Product', 'tasdir' ),
				'add_new_item'  => __( 'Add new product', 'tasdir' ),
				'edit_item'     => __( 'Edit product', 'tasdir' ),
				'all_items'     => __( 'All products', 'tasdir' ),
				'menu_name'     => __( 'Export Products', 'tasdir' ),
			),
			'public'       => true,
			'has_archive'  => 'products',
			'rewrite'      => array( 'slug' => 'products', 'with_front' => false ),
			'menu_icon'    => 'dashicons-carrot',
			'menu_position' => 5,
			'show_in_rest' => true,
			'supports'     => array( 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes', 'revisions' ),
		)
	);

	foreach ( array_keys( tasdir_product_fields() ) as $key ) {
		register_post_meta(
			'tasdir_product',
			'_tasdir_' . $key,
			array(
				'type'              => 'string',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => 'sanitize_text_field',
				'auth_callback'     => static function () {
					return current_user_can( 'edit_posts' );
				},
			)
		);
	}
}
add_action( 'init', 'tasdir_register_products' );

/**
 * Product spec meta box.
 */
function tasdir_product_metabox() {
	add_meta_box( 'tasdir_specs', __( 'Export specifications', 'tasdir' ), 'tasdir_product_metabox_html', 'tasdir_product', 'normal', 'high' );
}
add_action( 'add_meta_boxes', 'tasdir_product_metabox' );

/**
 * Render the specs meta box.
 *
 * @param WP_Post $post Post.
 */
function tasdir_product_metabox_html( $post ) {
	wp_nonce_field( 'tasdir_specs', 'tasdir_specs_nonce' );
	echo '<table class="form-table" role="presentation"><tbody>';
	foreach ( tasdir_product_fields() as $key => $label ) {
		printf(
			'<tr><th scope="row"><label for="tasdir_%1$s">%2$s</label></th><td><input type="text" class="large-text" id="tasdir_%1$s" name="tasdir_specs[%1$s]" value="%3$s"></td></tr>',
			esc_attr( $key ),
			esc_html( $label ),
			esc_attr( get_post_meta( $post->ID, '_tasdir_' . $key, true ) )
		);
	}
	echo '</tbody></table>';
	echo '<p class="description">' . esc_html__( 'Tip: set a Featured image for the product photo. The excerpt is used on product cards and as the meta description.', 'tasdir' ) . '</p>';
}

/**
 * Save specs.
 *
 * @param int $post_id Post ID.
 */
function tasdir_save_product( $post_id ) {
	if ( ! isset( $_POST['tasdir_specs_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['tasdir_specs_nonce'] ) ), 'tasdir_specs' ) ) {
		return;
	}
	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	$specs = isset( $_POST['tasdir_specs'] ) ? (array) wp_unslash( $_POST['tasdir_specs'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	foreach ( array_keys( tasdir_product_fields() ) as $key ) {
		$val = isset( $specs[ $key ] ) ? sanitize_text_field( $specs[ $key ] ) : '';
		if ( '' === $val ) {
			delete_post_meta( $post_id, '_tasdir_' . $key );
		} else {
			update_post_meta( $post_id, '_tasdir_' . $key, $val );
		}
	}
}
add_action( 'save_post_tasdir_product', 'tasdir_save_product' );

/**
 * Specs of a product as label => value (non-empty only).
 *
 * @param int $post_id Post ID.
 * @return array
 */
function tasdir_product_specs( $post_id ) {
	$out = array();
	foreach ( tasdir_product_fields() as $key => $label ) {
		$v = get_post_meta( $post_id, '_tasdir_' . $key, true );
		if ( '' !== $v ) {
			$out[ $label ] = $v;
		}
	}
	return $out;
}

/**
 * Product image: featured image, else a bundled photo matched by slug, else the banana box.
 *
 * @param int    $post_id Post ID.
 * @param string $sizes   Sizes attribute.
 * @param bool   $eager   Load eagerly (above the fold).
 * @return string
 */
function tasdir_product_image( $post_id, $sizes = '(min-width: 980px) 560px, 100vw', $eager = false ) {
	$title = get_the_title( $post_id );
	if ( has_post_thumbnail( $post_id ) ) {
		return get_the_post_thumbnail(
			$post_id,
			'tasdir-wide',
			array(
				'sizes'   => $sizes,
				'loading' => $eager ? 'eager' : 'lazy',
				'alt'     => $title,
			)
		);
	}
	$slug = get_post_field( 'post_name', $post_id );
	$map  = array(
		'grape'  => 'grapes-box',
		'banana' => 'banana-box',
	);
	$img  = 'banana-box';
	foreach ( $map as $needle => $file ) {
		if ( false !== stripos( $slug . ' ' . $title, $needle ) ) {
			$img = $file;
			break;
		}
	}
	/* translators: %s: product name. */
	return tasdir_theme_img( $img, sprintf( __( '%s export carton by Tasdir Import Export', 'tasdir' ), $title ), $sizes, array( 'loading' => $eager ? 'eager' : 'lazy' ) );
}

/**
 * Pre-filled WhatsApp message for a product.
 *
 * @param string $name Product name.
 * @return string
 */
function tasdir_product_wa_message( $name ) {
	/* translators: %s: product name. */
	return sprintf( __( 'Hello Tasdir, I am interested in importing %s. Please share price, packing and shipment details.', 'tasdir' ), $name );
}

/**
 * Show products in menu order on the archive.
 *
 * @param WP_Query $q Query.
 */
function tasdir_product_archive_order( $q ) {
	if ( ! is_admin() && $q->is_main_query() && $q->is_post_type_archive( 'tasdir_product' ) ) {
		$q->set( 'orderby', array( 'menu_order' => 'ASC', 'date' => 'DESC' ) );
		$q->set( 'posts_per_page', 24 );
	}
}
add_action( 'pre_get_posts', 'tasdir_product_archive_order' );
