<?php
/**
 * Boutique: switch on Bazaar Core features and register starter pages.
 *
 * @package Bazaar
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'after_setup_theme',
	static function () {
		foreach ( array( 'swatches', 'size-guide', 'whatsapp', 'pdp-extras', 'sticky-atc', 'otp', 'contact-form', 'filters', 'reviews' ) as $feature ) {
			add_theme_support( 'bazaar-' . $feature );
		}
	}
);

/**
 * Fashion trust badges.
 */
add_filter(
	'bzr_trust_badges',
	static function () {
		return array(
			array( 'return', __( '7-day size exchange', 'bazaar-boutique' ) ),
			array( 'truck', __( 'Cash on delivery', 'bazaar-boutique' ) ),
			array( 'shield', __( 'Secure checkout', 'bazaar-boutique' ) ),
			array( 'chat', __( 'Styling help on WhatsApp', 'bazaar-boutique' ) ),
		);
	}
);

/**
 * Default size guide: body measurements plus an abaya length chart.
 * Store owners can replace it entirely in Appearance > Store Options.
 */
add_filter(
	'bzr_default_size_guide',
	static function ( $html ) {
		$rows = array(
			array( '52', '5\'0" - 5\'2"' ),
			array( '54', '5\'2" - 5\'4"' ),
			array( '56', '5\'4" - 5\'6"' ),
			array( '58', '5\'6" - 5\'8"' ),
			array( '60', '5\'8" - 5\'10"' ),
		);

		$abaya  = '<h3>' . esc_html__( 'Abaya length', 'bazaar-boutique' ) . '</h3>';
		$abaya .= '<table class="bzr-size-table"><thead><tr><th scope="col">' . esc_html__( 'Length (in)', 'bazaar-boutique' ) . '</th><th scope="col">' . esc_html__( 'Your height', 'bazaar-boutique' ) . '</th></tr></thead><tbody>';
		foreach ( $rows as $row ) {
			$abaya .= '<tr><th scope="row">' . esc_html( $row[0] ) . '</th><td>' . esc_html( $row[1] ) . '</td></tr>';
		}
		$abaya .= '</tbody></table>';

		return $html . $abaya;
	}
);

/**
 * Pages created on activation (existing slugs are left alone).
 */
add_filter(
	'bzr_starter_pages',
	static function () {
		return array(
			'about-us'             => array( 'title' => __( 'About us', 'bazaar-boutique' ), 'pattern' => 'bazaar-boutique/page-about' ),
			'contact-us'           => array( 'title' => __( 'Contact us', 'bazaar-boutique' ), 'pattern' => 'bazaar-boutique/page-contact' ),
			'shipping-policy'      => array( 'title' => __( 'Shipping policy', 'bazaar-boutique' ), 'pattern' => 'bazaar-boutique/page-shipping' ),
			'delivery-information' => array( 'title' => __( 'Delivery information', 'bazaar-boutique' ), 'pattern' => 'bazaar-boutique/page-delivery' ),
			'return-refund-policy' => array( 'title' => __( 'Exchange & return policy', 'bazaar-boutique' ), 'pattern' => 'bazaar-boutique/page-returns' ),
			'payment-methods'      => array( 'title' => __( 'Payment methods', 'bazaar-boutique' ), 'pattern' => 'bazaar-boutique/page-payment' ),
			'terms-conditions'     => array( 'title' => __( 'Terms & conditions', 'bazaar-boutique' ), 'pattern' => 'bazaar-boutique/page-terms' ),
			'login'                => array( 'title' => __( 'Sign in', 'bazaar-boutique' ), 'pattern' => 'bazaar-boutique/page-login' ),
		);
	}
);
