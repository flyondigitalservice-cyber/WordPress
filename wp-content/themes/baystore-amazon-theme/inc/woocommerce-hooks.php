<?php
/**
 * Baystore Amazon Lean — WooCommerce AJAX, Buy Now & CRO Hooks
 *
 * @package Baystore_Amazon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Helper: Build clean cart JSON payload for Slide-Over Cart Drawer.
 */
function baystore_build_cart_payload() {
	if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
		return array(
			'count'    => 0,
			'subtotal' => 0,
			'total'    => 0,
			'items'    => array(),
		);
	}

	WC()->cart->calculate_totals();
	$items = array();
	foreach ( WC()->cart->get_cart() as $key => $cart_item ) {
		$prod = $cart_item['data'];
		if ( ! $prod ) {
			continue;
		}
		$img_id  = $prod->get_image_id();
		$img_url = $img_id ? wp_get_attachment_image_url( $img_id, 'woocommerce_thumbnail' ) : get_post_meta( $prod->get_id(), '_bs_ext_image', true );
		if ( ! $img_url ) {
			$img_url = wc_placeholder_img_src();
		}
		$items[] = array(
			'key'   => $key,
			'id'    => $prod->get_id(),
			'name'  => $prod->get_name(),
			'price' => (float) $prod->get_price(),
			'qty'   => (int) $cart_item['quantity'],
			'image' => esc_url_raw( $img_url ),
		);
	}

	return array(
		'count'    => (int) WC()->cart->get_cart_contents_count(),
		'subtotal' => (float) WC()->cart->get_subtotal(),
		'total'    => (float) WC()->cart->get_total( 'edit' ),
		'items'    => $items,
	);
}

/**
 * AJAX: Get Cart
 */
add_action( 'wp_ajax_baystore_get_cart', 'baystore_ajax_get_cart' );
add_action( 'wp_ajax_nopriv_baystore_get_cart', 'baystore_ajax_get_cart' );
function baystore_ajax_get_cart() {
	check_ajax_referer( 'baystore_lean_nonce', 'nonce' );
	wp_send_json_success( baystore_build_cart_payload() );
}

/**
 * AJAX: Add to Cart
 */
add_action( 'wp_ajax_baystore_add_to_cart', 'baystore_ajax_add_to_cart' );
add_action( 'wp_ajax_nopriv_baystore_add_to_cart', 'baystore_ajax_add_to_cart' );
function baystore_ajax_add_to_cart() {
	check_ajax_referer( 'baystore_lean_nonce', 'nonce' );
	if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
		wp_send_json_error( array( 'message' => 'WooCommerce not active' ) );
	}
	$product_id = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;
	$quantity   = isset( $_POST['quantity'] ) ? max( 1, absint( $_POST['quantity'] ) ) : 1;
	if ( ! $product_id ) {
		wp_send_json_error( array( 'message' => 'Invalid product' ) );
	}
	WC()->cart->add_to_cart( $product_id, $quantity );
	wp_send_json_success( baystore_build_cart_payload() );
}

/**
 * AJAX: Update Cart Qty
 */
add_action( 'wp_ajax_baystore_update_cart_qty', 'baystore_ajax_update_cart_qty' );
add_action( 'wp_ajax_nopriv_baystore_update_cart_qty', 'baystore_ajax_update_cart_qty' );
function baystore_ajax_update_cart_qty() {
	check_ajax_referer( 'baystore_lean_nonce', 'nonce' );
	if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
		wp_send_json_error();
	}
	$key = isset( $_POST['cart_key'] ) ? sanitize_text_field( wp_unslash( $_POST['cart_key'] ) ) : '';
	$qty = isset( $_POST['quantity'] ) ? (int) $_POST['quantity'] : 0;
	if ( $key ) {
		if ( $qty <= 0 ) {
			WC()->cart->remove_cart_item( $key );
		} else {
			WC()->cart->set_quantity( $key, $qty, true );
		}
	}
	wp_send_json_success( baystore_build_cart_payload() );
}

/**
 * AJAX: Apply Coupon (e.g. BAY10)
 */
add_action( 'wp_ajax_baystore_apply_coupon', 'baystore_ajax_apply_coupon' );
add_action( 'wp_ajax_nopriv_baystore_apply_coupon', 'baystore_ajax_apply_coupon' );
function baystore_ajax_apply_coupon() {
	check_ajax_referer( 'baystore_lean_nonce', 'nonce' );
	if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
		wp_send_json_error( array( 'message' => 'WooCommerce not active' ) );
	}
	$code = isset( $_POST['coupon'] ) ? sanitize_text_field( wp_unslash( $_POST['coupon'] ) ) : 'BAY10';
	if ( ! WC()->cart->has_discount( $code ) ) {
		$applied = WC()->cart->apply_coupon( $code );
		if ( ! $applied ) {
			wp_send_json_error( array( 'message' => 'Coupon saved — apply at checkout: ' . $code ) );
		}
	}
	wp_send_json_success( baystore_build_cart_payload() );
}

/**
 * AJAX: Department-Scoped Live Search Autocomplete
 */
add_action( 'wp_ajax_baystore_live_search', 'baystore_ajax_live_search' );
add_action( 'wp_ajax_nopriv_baystore_live_search', 'baystore_ajax_live_search' );
function baystore_ajax_live_search() {
	check_ajax_referer( 'baystore_lean_nonce', 'nonce' );
	$query = isset( $_POST['query'] ) ? sanitize_text_field( wp_unslash( $_POST['query'] ) ) : '';
	$dept  = isset( $_POST['dept'] ) ? sanitize_text_field( wp_unslash( $_POST['dept'] ) ) : '';

	if ( ! class_exists( 'WooCommerce' ) || strlen( $query ) < 2 ) {
		wp_send_json_success( array() );
	}

	$args = array(
		'post_type'      => 'product',
		'post_status'    => 'publish',
		's'              => $query,
		'posts_per_page' => 6,
	);
	if ( $dept && 'all' !== strtolower( $dept ) ) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'product_cat',
				'field'    => 'slug',
				'terms'    => $dept,
			),
		);
	}

	$q       = new WP_Query( $args );
	$results = array();
	while ( $q->have_posts() ) {
		$q->the_post();
		$product = wc_get_product( get_the_ID() );
		if ( ! $product ) {
			continue;
		}
		$img = get_the_post_thumbnail_url( get_the_ID(), 'woocommerce_thumbnail' );
		if ( ! $img ) {
			$img = get_post_meta( get_the_ID(), '_bs_ext_image', true ) ?: wc_placeholder_img_src();
		}
		$terms = wp_get_post_terms( get_the_ID(), 'product_cat', array( 'fields' => 'names' ) );
		$results[] = array(
			'id'       => get_the_ID(),
			'title'    => get_the_title(),
			'url'      => get_permalink(),
			'price'    => (float) $product->get_price(),
			'image'    => esc_url_raw( $img ),
			'category' => ! empty( $terms ) ? $terms[0] : 'Baystore',
		);
	}
	wp_reset_postdata();

	wp_send_json_success( $results );
}

/**
 * Lean 1-Click "Buy Now" Redirect (?baystore_buy_now=PRODUCT_ID)
 */
add_action( 'template_redirect', function () {
	if ( isset( $_GET['baystore_buy_now'] ) && function_exists( 'WC' ) && WC()->cart ) {
		$pid = absint( $_GET['baystore_buy_now'] );
		if ( $pid > 0 ) {
			WC()->cart->empty_cart();
			WC()->cart->add_to_cart( $pid, 1 );
			wp_safe_redirect( wc_get_checkout_url() );
			exit;
		}
	}
} );

/**
 * Lean Checkout Field Optimization: Remove low-impact friction fields (Company Name).
 */
add_filter( 'woocommerce_checkout_fields', function ( $fields ) {
	if ( isset( $fields['billing']['billing_company'] ) ) {
		unset( $fields['billing']['billing_company'] );
	}
	if ( isset( $fields['shipping']['shipping_company'] ) ) {
		unset( $fields['shipping']['shipping_company'] );
	}
	return $fields;
} );
