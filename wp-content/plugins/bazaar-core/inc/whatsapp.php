<?php
/**
 * WhatsApp ordering/chat (opt in: bazaar-whatsapp). Works with or without WooCommerce.
 *
 * @package BazaarCore
 */

defined( 'ABSPATH' ) || exit;

/* -------------------------------------------------------------------------
 * WhatsApp
 * ---------------------------------------------------------------------- */

/**
 * Build a wa.me link, or '' when no number is configured.
 *
 * @param string      $extra Text appended to the message (e.g. product name + link).
 * @param string|null $base  Opening message. Defaults to the "WhatsApp message" option (an order prefix).
 * @return string
 */
function bzr_whatsapp_url( $extra = '', $base = null ) {
	$number = bzr_option( 'whatsapp_number' );
	if ( ! $number ) {
		return '';
	}

	if ( null === $base ) {
		$base = bzr_option( 'whatsapp_message', __( 'Hi! I would like to order:', 'bazaar-core' ) );
	}
	$text = trim( $base . ' ' . $extra );

	return add_query_arg( 'text', rawurlencode( $text ), 'https://wa.me/' . $number );
}

/**
 * "Order on WhatsApp" link on single product pages.
 */
function bzr_product_whatsapp_button() {
	if ( ! bzr_feature( 'whatsapp' ) ) {
		return;
	}

	global $product;
	if ( ! $product ) {
		return;
	}

	$url = bzr_whatsapp_url( $product->get_name() . ' - ' . get_permalink( $product->get_id() ) );
	if ( ! $url ) {
		return;
	}

	printf(
		'<a class="bzr-wa-order" href="%1$s" target="_blank" rel="noopener noreferrer">%2$s</a>',
		esc_url( $url ),
		esc_html__( 'Order on WhatsApp', 'bazaar-core' )
	);
}
add_action( 'woocommerce_after_add_to_cart_form', 'bzr_product_whatsapp_button' );

/**
 * Floating WhatsApp chat button, sitewide.
 */
function bzr_floating_whatsapp() {
	if ( ! bzr_feature( 'whatsapp' ) || is_admin() ) {
		return;
	}

	$url = bzr_whatsapp_url( '', __( 'Hi! I have a question.', 'bazaar-core' ) );
	if ( ! $url ) {
		return;
	}

	printf(
		'<a class="bzr-wa-float" href="%1$s" target="_blank" rel="noopener noreferrer" aria-label="%2$s"><svg viewBox="0 0 32 32" width="28" height="28" aria-hidden="true" focusable="false"><path fill="currentColor" d="M16 3a13 13 0 0 0-11.1 19.7L3 29l6.5-1.7A13 13 0 1 0 16 3zm0 2.4a10.6 10.6 0 1 1-5.4 19.7l-.4-.2-3.8 1 1-3.7-.3-.4A10.6 10.6 0 0 1 16 5.4zm-4 5.3c-.3 0-.7.1-1 .5s-1.2 1.2-1.2 2.9 1.2 3.3 1.4 3.5 2.4 3.8 5.9 5.2c2.9 1.1 3.5.9 4.1.8s2-.8 2.3-1.6.3-1.5.2-1.6-.3-.2-.7-.4-2-1-2.3-1.1-.5-.2-.8.2-.9 1.1-1.1 1.3-.4.3-.7.1-1.4-.5-2.7-1.7a10 10 0 0 1-1.9-2.3c-.2-.4 0-.6.2-.8l.5-.6c.2-.2.2-.4.4-.6s.1-.5 0-.6-.7-1.8-1-2.4-.5-.5-.8-.5z"/></svg></a>',
		esc_url( $url ),
		esc_attr__( 'Chat on WhatsApp', 'bazaar-core' )
	);
}
add_action( 'wp_footer', 'bzr_floating_whatsapp' );
