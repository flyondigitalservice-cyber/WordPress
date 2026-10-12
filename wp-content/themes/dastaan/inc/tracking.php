<?php
/**
 * Tracking: Meta Pixel, GA4, Microsoft Clarity — consent-aware, e-commerce events included.
 *
 * Events:
 *  - Meta:  PageView, ViewContent, ViewCategory, Search, AddToCart, InitiateCheckout, Purchase
 *  - GA4:   page_view, view_item, view_item_list, search, add_to_cart, begin_checkout, purchase
 *
 * @package Dastaan
 */

defined( 'ABSPATH' ) || exit;

/**
 * Any tracker configured?
 *
 * @return bool
 */
function dastaan_tracking_enabled() {
	if ( is_customize_preview() || is_admin() ) {
		return false;
	}
	if ( current_user_can( 'manage_options' ) && apply_filters( 'dastaan_track_admins', false ) === false ) {
		return false; // Never pollute your own data.
	}

	return (bool) ( dastaan_option( 'fb_pixel_id' ) || dastaan_option( 'ga4_id' ) || dastaan_option( 'clarity_id' ) );
}

/**
 * Product → tracking item.
 *
 * @param WC_Product $product  Product.
 * @param int        $qty      Qty.
 * @return array
 */
function dastaan_tracking_item( $product, $qty = 1 ) {
	$cats = wc_get_product_terms( $product->get_id(), 'product_cat', array( 'fields' => 'names' ) );

	return array(
		'id'       => (string) ( $product->get_sku() ? $product->get_sku() : $product->get_id() ),
		'name'     => $product->get_name(),
		'price'    => (float) wc_get_price_to_display( $product ),
		'quantity' => (int) $qty,
		'category' => $cats ? implode( '/', $cats ) : '',
	);
}

/**
 * Server-side event payloads for the current page.
 *
 * @return array
 */
function dastaan_tracking_events() {
	$events = array();
	if ( ! dastaan_has_wc() ) {
		if ( is_search() ) {
			$events['search'] = array( 'query' => get_search_query() );
		}
		return $events;
	}

	if ( is_product() ) {
		$product = wc_get_product( get_the_ID() );
		if ( $product ) {
			$events['viewContent'] = array( 'items' => array( dastaan_tracking_item( $product ) ), 'value' => (float) wc_get_price_to_display( $product ) );
		}
	} elseif ( is_product_category() ) {
		$term = get_queried_object();
		$events['viewCategory'] = array( 'name' => $term ? $term->name : '' );
	} elseif ( is_search() ) {
		$events['search'] = array( 'query' => get_search_query() );
	} elseif ( is_checkout() && ! is_order_received_page() && WC()->cart ) {
		$items = array();
		foreach ( WC()->cart->get_cart() as $ci ) {
			if ( ! empty( $ci['data'] ) ) {
				$items[] = dastaan_tracking_item( $ci['data'], $ci['quantity'] );
			}
		}
		$events['initiateCheckout'] = array( 'items' => $items, 'value' => (float) WC()->cart->get_total( 'edit' ), 'num_items' => (int) WC()->cart->get_cart_contents_count() );
	} elseif ( is_order_received_page() ) {
		$order_id = absint( get_query_var( 'order-received' ) );
		$order    = $order_id ? wc_get_order( $order_id ) : false;
		$key      = isset( $_GET['key'] ) ? wc_clean( wp_unslash( $_GET['key'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( $order && hash_equals( $order->get_order_key(), $key ) && ! $order->get_meta( '_dastaan_tracked' ) ) {
			$items = array();
			foreach ( $order->get_items() as $item ) {
				$p = $item->get_product();
				if ( $p ) {
					$items[] = dastaan_tracking_item( $p, $item->get_quantity() );
				}
			}
			$events['purchase'] = array(
				'transaction_id' => (string) $order->get_order_number(),
				'value'          => (float) $order->get_total(),
				'shipping'       => (float) $order->get_shipping_total(),
				'tax'            => (float) $order->get_total_tax(),
				'coupon'         => implode( ',', $order->get_coupon_codes() ),
				'items'          => $items,
			);
			$order->update_meta_data( '_dastaan_tracked', time() );
			$order->save();
		}
	}

	return $events;
}

/**
 * Enqueue tracking script with config.
 */
function dastaan_tracking_enqueue() {
	if ( ! dastaan_tracking_enabled() ) {
		return;
	}
	wp_enqueue_script( 'dastaan-tracking', DASTAAN_URI . 'assets/js/tracking.js', array(), DASTAAN_VERSION, true );
	wp_script_add_data( 'dastaan-tracking', 'strategy', 'defer' );
	wp_localize_script(
		'dastaan-tracking',
		'dastaanTracking',
		array(
			'pixel'    => dastaan_pixel_id(),
			'ga4'      => dastaan_option( 'ga4_id' ),
			'clarity'  => dastaan_option( 'clarity_id' ),
			'consent'  => (int) dastaan_option( 'consent_enable' ),
			'currency' => dastaan_has_wc() ? get_woocommerce_currency() : 'INR',
			'events'   => dastaan_tracking_events(),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'dastaan_tracking_enqueue', 20 );

/**
 * Meta Pixel ID — empty when the official Facebook for WooCommerce plugin already injects a pixel.
 *
 * @return string
 */
function dastaan_pixel_id() {
	if ( class_exists( 'WC_Facebookcommerce' ) && apply_filters( 'dastaan_defer_pixel_to_plugin', true ) ) {
		return '';
	}
	return (string) dastaan_option( 'fb_pixel_id' );
}

/**
 * Noscript pixel fallback (only when consent is not required).
 */
function dastaan_pixel_noscript() {
	if ( ! dastaan_tracking_enabled() || dastaan_option( 'consent_enable' ) ) {
		return;
	}
	$id = dastaan_pixel_id();
	if ( $id ) {
		printf( '<noscript><img height="1" width="1" style="display:none" alt="" src="https://www.facebook.com/tr?id=%s&ev=PageView&noscript=1"/></noscript>' . "\n", esc_attr( $id ) );
	}
}
add_action( 'wp_body_open', 'dastaan_pixel_noscript' );

/**
 * Expose product data on add-to-cart buttons for AJAX events.
 *
 * @param array      $args    Args.
 * @param WC_Product $product Product.
 * @return array
 */
function dastaan_atc_button_data( $args, $product ) {
	$args['attributes']['data-track-name']  = $product->get_name();
	$args['attributes']['data-track-price'] = wc_get_price_to_display( $product );
	$args['attributes']['data-track-sku']   = $product->get_sku() ? $product->get_sku() : $product->get_id();

	return $args;
}
add_filter( 'woocommerce_loop_add_to_cart_args', 'dastaan_atc_button_data', 10, 2 );
