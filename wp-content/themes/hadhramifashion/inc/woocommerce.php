<?php
/**
 * WooCommerce integration: product details, made-to-measure cart data, the bag drawer,
 * Drive-hosted product images, and collection filters.
 *
 * Delivery (2-hour / 24-hour promises, riders, tracking) is handled by the
 * Hadhrami Delivery plugin.
 *
 * @package HadhramiFashion
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'after_setup_theme',
	function () {
		add_theme_support(
			'woocommerce',
			array(
				'thumbnail_image_width' => 600,
				'single_image_width'    => 1000,
			)
		);
	}
);

// The theme provides its own layout around WooCommerce screens.
add_filter( 'woocommerce_enqueue_styles', function ( $styles ) {
	unset( $styles['woocommerce-layout'], $styles['woocommerce-smallscreen'] );
	return $styles;
} );
add_filter( 'loop_shop_per_page', function () {
	return 24;
} );

/**
 * Product kind from its category: `suits` or `shirts`.
 *
 * @param int $product_id Product ID.
 * @return string
 */
function hf_product_kind( $product_id ) {
	return has_term( 'suits', 'product_cat', $product_id ) ? 'suits' : 'shirts';
}

/**
 * Ready-to-wear 2-hour stock (flag owned by the Hadhrami Delivery plugin).
 *
 * @param int $product_id Product ID.
 * @return bool
 */
function hf_is_express( $product_id ) {
	return 'yes' === get_post_meta( $product_id, '_hfd_express', true );
}

/**
 * Occasion tags used to group suits (Business, Summer Weight, Evening & Occasion, Wedding).
 *
 * @return WP_Term[]
 */
function hf_occasions() {
	$terms = get_terms(
		array(
			'taxonomy'   => 'product_tag',
			'slug'       => array( 'business', 'summer', 'occasion', 'wedding' ),
			'hide_empty' => true,
			'orderby'    => 'slug__in',
		)
	);
	return is_wp_error( $terms ) ? array() : $terms;
}

/* --------------------------------------------------------------------------
 * Product fields (Product data → General).
 * ----------------------------------------------------------------------- */

/**
 * Tailoring fields: meta key => label.
 *
 * @return array
 */
function hf_product_fields() {
	return array(
		'_hf_subtitle'     => __( 'Subtitle (e.g. “Two-piece suit · Wool flannel”)', 'hadhramifashion' ),
		'_hf_fabric'       => __( 'Cloth', 'hadhramifashion' ),
		'_hf_colour'       => __( 'Colour family (for shirt filters)', 'hadhramifashion' ),
		'_hf_drive_image'  => __( 'Google Drive image ID (used until an image is set)', 'hadhramifashion' ),
		'_hf_drive_swatch' => __( 'Google Drive cloth swatch ID', 'hadhramifashion' ),
	);
}

add_action(
	'woocommerce_product_options_general_product_data',
	function () {
		echo '<div class="options_group">';
		foreach ( hf_product_fields() as $key => $label ) {
			woocommerce_wp_text_input(
				array(
					'id'    => $key,
					'label' => $label,
				)
			);
		}
		echo '</div>';
	}
);

add_action(
	'woocommerce_admin_process_product_object',
	function ( $product ) {
		foreach ( array_keys( hf_product_fields() ) as $key ) {
			if ( isset( $_POST[ $key ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification -- WooCommerce verifies the product nonce.
				$product->update_meta_data( $key, sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) ); // phpcs:ignore WordPress.Security.NonceVerification
			}
		}
	}
);

/* --------------------------------------------------------------------------
 * Images: fall back to the Google Drive original until imported.
 * ----------------------------------------------------------------------- */

/**
 * Print a product's main image.
 *
 * @param int    $product_id Product ID.
 * @param int    $width      Requested width.
 * @param string $class      CSS class.
 * @param bool   $lazy       Lazy load.
 */
function hf_product_image( $product_id, $width = 900, $class = '', $lazy = true ) {
	if ( has_post_thumbnail( $product_id ) ) {
		echo get_the_post_thumbnail(
			$product_id,
			$width > 700 ? 'woocommerce_single' : 'woocommerce_thumbnail',
			array(
				'class'   => $class,
				'loading' => $lazy ? 'lazy' : 'eager',
			)
		);
		return;
	}
	$drive = get_post_meta( $product_id, '_hf_drive_image', true );
	if ( $drive ) {
		hf_drive_img( $drive, get_the_title( $product_id ), $width, $class, $lazy );
	}
}

add_filter(
	'woocommerce_product_get_image',
	function ( $image, $product ) {
		if ( $product->get_image_id() ) {
			return $image;
		}
		$id    = $product->get_parent_id() ? $product->get_parent_id() : $product->get_id();
		$drive = get_post_meta( $id, '_hf_drive_image', true );
		if ( ! $drive ) {
			return $image;
		}
		ob_start();
		hf_drive_img( $drive, $product->get_name(), 300 );
		return ob_get_clean();
	},
	10,
	2
);

/* --------------------------------------------------------------------------
 * Made-to-measure cart data.
 * ----------------------------------------------------------------------- */

/**
 * Measurement fields per kind: key => label.
 *
 * @param string $kind `suits` or `shirts`.
 * @return array
 */
function hf_measure_fields( $kind ) {
	if ( 'suits' === $kind ) {
		return array(
			'chest'    => __( 'Chest', 'hadhramifashion' ),
			'waist'    => __( 'Waist', 'hadhramifashion' ),
			'seat'     => __( 'Seat', 'hadhramifashion' ),
			'shoulder' => __( 'Shoulder', 'hadhramifashion' ),
			'sleeve'   => __( 'Sleeve', 'hadhramifashion' ),
			'jacket'   => __( 'Jacket length', 'hadhramifashion' ),
			'inseam'   => __( 'Trouser inseam', 'hadhramifashion' ),
			'height'   => __( 'Height', 'hadhramifashion' ),
			'weight'   => __( 'Weight (kg)', 'hadhramifashion' ),
		);
	}
	return array(
		'neck'     => __( 'Neck', 'hadhramifashion' ),
		'chest'    => __( 'Chest', 'hadhramifashion' ),
		'waist'    => __( 'Waist', 'hadhramifashion' ),
		'shoulder' => __( 'Shoulder', 'hadhramifashion' ),
		'sleeve'   => __( 'Sleeve', 'hadhramifashion' ),
		'length'   => __( 'Shirt length', 'hadhramifashion' ),
	);
}

add_filter(
	'woocommerce_add_cart_item_data',
	function ( $data, $product_id ) {
		// phpcs:disable WordPress.Security.NonceVerification -- WooCommerce add-to-cart request.
		$kind = hf_product_kind( $product_id );
		$fit  = ( 'suits' === $kind || ( isset( $_POST['hf_fit'] ) && 'measure' === $_POST['hf_fit'] ) ) ? 'measure' : 'ready';

		$data['hf_fit'] = $fit;
		if ( 'measure' === $fit ) {
			$data['hfd_made_to_measure'] = true; // Read by Hadhrami Delivery: no 2-hour promise.
			$measures = array();
			foreach ( array_keys( hf_measure_fields( $kind ) ) as $key ) {
				$value = isset( $_POST[ 'm_' . $key ] ) ? sanitize_text_field( wp_unslash( $_POST[ 'm_' . $key ] ) ) : '';
				if ( '' !== $value ) {
					$measures[ $key ] = mb_substr( $value, 0, 20 );
				}
			}
			$data['hf_measure'] = $measures;
		} else {
			$data['hf_size'] = isset( $_POST['hf_size'] ) ? sanitize_text_field( wp_unslash( $_POST['hf_size'] ) ) : '';
			$data['hf_cut']  = isset( $_POST['hf_cut'] ) ? sanitize_text_field( wp_unslash( $_POST['hf_cut'] ) ) : '';
		}
		// phpcs:enable
		return $data;
	},
	10,
	2
);

/**
 * Human-readable lines for a cart item's tailoring choices.
 *
 * @param array  $item Cart item (or equivalent array).
 * @param string $kind Product kind.
 * @return array label => value
 */
function hf_item_details( $item, $kind ) {
	$out = array();
	if ( empty( $item['hf_fit'] ) ) {
		return $out;
	}
	if ( 'measure' === $item['hf_fit'] ) {
		$out[ __( 'Fit', 'hadhramifashion' ) ] = __( 'Made to measure', 'hadhramifashion' );
		if ( ! empty( $item['hf_measure'] ) ) {
			$labels = hf_measure_fields( $kind );
			$parts  = array();
			foreach ( $item['hf_measure'] as $key => $value ) {
				$parts[] = ( isset( $labels[ $key ] ) ? $labels[ $key ] : $key ) . ' ' . $value;
			}
			$out[ __( 'Measurements', 'hadhramifashion' ) ] = implode( ' · ', $parts );
		} else {
			$out[ __( 'Measurements', 'hadhramifashion' ) ] = __( 'Tailor to visit and measure', 'hadhramifashion' );
		}
	} else {
		$out[ __( 'Fit', 'hadhramifashion' ) ] = __( 'Ready to wear', 'hadhramifashion' );
		if ( ! empty( $item['hf_size'] ) ) {
			$out[ __( 'Collar', 'hadhramifashion' ) ] = $item['hf_size'] . '″';
		}
		if ( ! empty( $item['hf_cut'] ) ) {
			$out[ __( 'Cut', 'hadhramifashion' ) ] = $item['hf_cut'];
		}
	}
	return $out;
}

add_filter(
	'woocommerce_get_item_data',
	function ( $data, $item ) {
		foreach ( hf_item_details( $item, hf_product_kind( $item['product_id'] ) ) as $key => $value ) {
			$data[] = array(
				'key'   => $key,
				'value' => $value,
			);
		}
		return $data;
	},
	10,
	2
);

add_action(
	'woocommerce_checkout_create_order_line_item',
	function ( $line, $cart_item_key, $values ) {
		foreach ( hf_item_details( $values, hf_product_kind( $values['product_id'] ) ) as $key => $value ) {
			$line->add_meta_data( $key, $value );
		}
	},
	10,
	3
);

/* --------------------------------------------------------------------------
 * Bag drawer (mini cart) kept in sync with WooCommerce fragments.
 * ----------------------------------------------------------------------- */

/**
 * Bag drawer contents.
 */
function hf_mini_bag() {
	echo '<div class="hf-mini-bag">';
	$cart = function_exists( 'WC' ) && WC()->cart ? WC()->cart : null;
	if ( ! $cart || $cart->is_empty() ) {
		echo '<div class="drawer__body"><p class="empty-bag">' . esc_html__( 'Your bag is empty.', 'hadhramifashion' ) . '</p></div>';
		echo '</div>';
		return;
	}
	echo '<div class="drawer__body">';
	foreach ( $cart->get_cart() as $key => $item ) {
		$product = $item['data'];
		if ( ! $product ) {
			continue;
		}
		$meta = array();
		foreach ( hf_item_details( $item, hf_product_kind( $item['product_id'] ) ) as $value ) {
			$meta[] = $value;
		}
		printf(
			'<div class="line">%1$s<div><a class="line__title" href="%2$s">%3$s</a><div class="line__meta">%4$s</div><div class="line__meta">%5$s × %6$s</div><button type="button" class="line__remove" data-remove-key="%7$s">%8$s</button></div><div class="line__price">%9$s</div></div>',
			$product->get_image( 'woocommerce_gallery_thumbnail' ), // phpcs:ignore WordPress.Security.EscapeOutput
			esc_url( $product->get_permalink( $item ) ),
			esc_html( $product->get_name() ),
			esc_html( implode( ' · ', $meta ) ),
			esc_html( $item['quantity'] ),
			wp_kses_post( wc_price( $product->get_price() ) ),
			esc_attr( $key ),
			esc_html__( 'Remove', 'hadhramifashion' ),
			wp_kses_post( $cart->get_product_subtotal( $product, $item['quantity'] ) )
		);
	}
	echo '</div>';
	printf(
		'<div class="drawer__foot"><div class="row"><span>%1$s</span><strong>%2$s</strong></div><p class="muted" style="font-size:.82rem">%3$s</p><a class="btn btn--block" href="%4$s">%5$s %6$s</a><a class="link-arrow" style="margin-top:1rem" href="%7$s">%8$s</a></div>',
		esc_html__( 'Subtotal', 'hadhramifashion' ),
		wp_kses_post( $cart->get_cart_subtotal() ),
		esc_html__( 'Choose 2-hour or 24-hour delivery at checkout.', 'hadhramifashion' ),
		esc_url( wc_get_checkout_url() ),
		esc_html__( 'Checkout', 'hadhramifashion' ),
		hf_icon( 'arrow' ), // phpcs:ignore WordPress.Security.EscapeOutput
		esc_url( wc_get_cart_url() ),
		esc_html__( 'View bag', 'hadhramifashion' )
	);
	echo '</div>';
}

/**
 * Bag count badge.
 */
function hf_bag_count() {
	$count = function_exists( 'WC' ) && WC()->cart ? WC()->cart->get_cart_contents_count() : 0;
	printf( '<span class="bag-count" data-count="%1$d">%1$d</span>', (int) $count );
}

add_filter(
	'woocommerce_add_to_cart_fragments',
	function ( $fragments ) {
		ob_start();
		hf_mini_bag();
		$fragments['div.hf-mini-bag'] = ob_get_clean();
		ob_start();
		hf_bag_count();
		$fragments['span.bag-count'] = ob_get_clean();
		return $fragments;
	}
);

/* --------------------------------------------------------------------------
 * Collection filters: ?colour=White and ?express=1 on shop/category pages.
 * ----------------------------------------------------------------------- */

add_action(
	'woocommerce_product_query',
	function ( $query ) {
		$meta   = $query->get( 'meta_query' );
		$meta   = is_array( $meta ) ? $meta : array();
		$colour = isset( $_GET['colour'] ) ? sanitize_text_field( wp_unslash( $_GET['colour'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
		if ( $colour ) {
			$meta[] = array(
				'key'   => '_hf_colour',
				'value' => $colour,
			);
		}
		if ( isset( $_GET['express'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			$meta[] = array(
				'key'   => '_hfd_express',
				'value' => 'yes',
			);
		}
		$query->set( 'meta_query', $meta );
	}
);

/* --------------------------------------------------------------------------
 * Misc WooCommerce screens.
 * ----------------------------------------------------------------------- */

// Wrap WooCommerce pages (cart, checkout, account) in the theme layout.
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );

// "Track" in the header goes to the customer's orders (each has a live-tracking link).
function hf_track_url() {
	return function_exists( 'wc_get_account_endpoint_url' ) ? wc_get_account_endpoint_url( 'orders' ) : home_url( '/' );
}
