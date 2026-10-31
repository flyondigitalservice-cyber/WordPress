<?php
/**
 * Electronics: switch on Bazaar Core features and register starter pages.
 *
 * @package Bazaar
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'after_setup_theme',
	static function () {
		foreach ( array( 'whatsapp', 'pdp-extras', 'sticky-atc', 'otp', 'contact-form', 'filters', 'compare', 'countdown', 'emi', 'warranty', 'reviews' ) as $feature ) {
			add_theme_support( 'bazaar-' . $feature );
		}
	}
);

/**
 * Electronics trust badges. Warranty and invoice wording come from Store Options.
 */
add_filter(
	'bzr_trust_badges',
	static function () {
		return array(
			array( 'shield', bzr_option( 'warranty_text', __( 'Brand warranty', 'bazaar-electronics' ) ) ),
			array( 'return', __( 'Replacement for defects', 'bazaar-electronics' ) ),
			array(
				'truck',
				/* translators: %s: invoice label from Store Options, e.g. "GST invoice" */
				sprintf( __( 'Genuine, %s', 'bazaar-electronics' ), bzr_option( 'invoice_label', __( 'GST invoice', 'bazaar-electronics' ) ) ),
			),
			array( 'chat', __( 'Expert support', 'bazaar-electronics' ) ),
		);
	}
);

/**
 * Pages created on activation (existing slugs are left alone).
 */
add_filter(
	'bzr_starter_pages',
	static function () {
		return array(
			'about-us'             => array( 'title' => __( 'About us', 'bazaar-electronics' ), 'pattern' => 'bazaar-electronics/page-about' ),
			'contact-us'           => array( 'title' => __( 'Contact us', 'bazaar-electronics' ), 'pattern' => 'bazaar-electronics/page-contact' ),
			'warranty-service'     => array( 'title' => __( 'Warranty & service', 'bazaar-electronics' ), 'pattern' => 'bazaar-electronics/page-warranty' ),
			'compare'              => array( 'title' => __( 'Compare products', 'bazaar-electronics' ), 'pattern' => 'bazaar-electronics/page-compare' ),
			'shipping-policy'      => array( 'title' => __( 'Shipping policy', 'bazaar-electronics' ), 'pattern' => 'bazaar-electronics/page-shipping' ),
			'delivery-information' => array( 'title' => __( 'Delivery information', 'bazaar-electronics' ), 'pattern' => 'bazaar-electronics/page-delivery' ),
			'return-refund-policy' => array( 'title' => __( 'Return & replacement policy', 'bazaar-electronics' ), 'pattern' => 'bazaar-electronics/page-returns' ),
			'payment-methods'      => array( 'title' => __( 'Payment methods', 'bazaar-electronics' ), 'pattern' => 'bazaar-electronics/page-payment' ),
			'terms-conditions'     => array( 'title' => __( 'Terms & conditions', 'bazaar-electronics' ), 'pattern' => 'bazaar-electronics/page-terms' ),
			'login'                => array( 'title' => __( 'Sign in', 'bazaar-electronics' ), 'pattern' => 'bazaar-electronics/page-login' ),
		);
	}
);
