<?php
/**
 * WooCommerce integration tweaks.
 *
 * @package luxurywatchs
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WooCommerce' ) ) {
	return;
}

// Use our own wrappers.
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );

add_filter( 'loop_shop_columns', static function () {
	return 4;
} );

add_filter( 'loop_shop_per_page', static function () {
	return 24;
} );

add_filter( 'woocommerce_output_related_products_args', static function ( $args ) {
	$args['posts_per_page'] = 4;
	$args['columns']        = 4;
	return $args;
} );

/**
 * Show percentage discount on sale badge.
 */
add_filter(
	'woocommerce_sale_flash',
	static function ( $html, $post, $product ) {
		if ( $product && $product->is_type( 'simple' ) && $product->get_regular_price() && $product->get_sale_price() ) {
			$pct = round( ( 1 - (float) $product->get_sale_price() / (float) $product->get_regular_price() ) * 100 );
			return '<span class="onsale">-' . esc_html( $pct ) . '%</span>';
		}
		return $html;
	},
	10,
	3
);

/**
 * Trust strip + WhatsApp order button on the single product page.
 */
add_action(
	'woocommerce_single_product_summary',
	static function () {
		global $product;
		$msg = sprintf( 'Hi! I want to order: %s (%s)', $product->get_name(), get_permalink( $product->get_id() ) );
		echo '<a class="lw-btn lw-btn--wa lw-btn--block" href="' . esc_url( lw_whatsapp_url( $msg ) ) . '" target="_blank" rel="noopener">' . lw_whatsapp_icon() . esc_html__( 'Order instantly on WhatsApp', 'luxurywatchs' ) . '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '<ul class="lw-pdp-trust">';
		echo '<li>' . lw_icon( 'cash' ) . esc_html__( 'Cash on Delivery', 'luxurywatchs' ) . '</li>'; // phpcs:ignore
		echo '<li>' . lw_icon( 'truck' ) . esc_html__( 'Free shipping, 3–6 days', 'luxurywatchs' ) . '</li>'; // phpcs:ignore
		echo '<li>' . lw_icon( 'headset' ) . esc_html__( 'Live video before dispatch', 'luxurywatchs' ) . '</li>'; // phpcs:ignore
		echo '<li>' . lw_icon( 'shield' ) . esc_html__( '6-month warranty on selected models', 'luxurywatchs' ) . '</li>'; // phpcs:ignore
		echo '</ul>';
	},
	35
);

/**
 * Sticky mobile add-to-cart bar on product pages.
 */
add_action(
	'wp_footer',
	static function () {
		if ( ! is_product() ) {
			return;
		}
		$product = wc_get_product( get_queried_object_id() );
		if ( ! $product || ! $product->is_purchasable() ) {
			return;
		}
		echo '<div class="lw-sticky-atc" data-sticky-atc>';
		echo '<div class="lw-sticky-atc__info"><strong>' . esc_html( $product->get_name() ) . '</strong><span>' . wp_kses_post( $product->get_price_html() ) . '</span></div>';
		if ( $product->is_type( 'simple' ) ) {
			echo '<a class="lw-btn lw-btn--gold" href="' . esc_url( $product->add_to_cart_url() ) . '">' . esc_html__( 'Add to Cart', 'luxurywatchs' ) . '</a>';
		} else {
			echo '<a class="lw-btn lw-btn--gold" href="#product-' . esc_attr( $product->get_id() ) . '">' . esc_html__( 'Choose Options', 'luxurywatchs' ) . '</a>';
		}
		echo '</div>';
	}
);

/**
 * Keep the header cart count fresh via AJAX fragments.
 */
add_filter(
	'woocommerce_add_to_cart_fragments',
	static function ( $fragments ) {
		$fragments['span.lw-cart-count'] = '<span class="lw-cart-count">' . esc_html( WC()->cart->get_cart_contents_count() ) . '</span>';
		return $fragments;
	}
);

/**
 * Products without a price show "Price on request" with a WhatsApp link.
 */
add_filter(
	'woocommerce_empty_price_html',
	static function ( $html, $product ) {
		$msg = sprintf( 'Hi! Please share the price of %s (%s)', $product->get_name(), get_permalink( $product->get_id() ) );
		return '<a class="lw-por" href="' . esc_url( lw_whatsapp_url( $msg ) ) . '" target="_blank" rel="noopener">' . esc_html__( 'Price on request', 'luxurywatchs' ) . '</a>';
	},
	10,
	2
);
