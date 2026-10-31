<?php
/**
 * Store: switch on Bazaar Core features and register starter pages.
 *
 * @package Bazaar
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'after_setup_theme',
	static function () {
		add_theme_support( 'bazaar-whatsapp' );
		add_theme_support( 'bazaar-pdp-extras' );
		add_theme_support( 'bazaar-sticky-atc' );
		add_theme_support( 'bazaar-otp' );
		add_theme_support( 'bazaar-contact-form' );
		add_theme_support( 'bazaar-reviews' );
	}
);

/**
 * Pages created on activation (existing slugs are left alone).
 */
add_filter(
	'bzr_starter_pages',
	static function () {
		return array(
			'about-us'             => array( 'title' => __( 'About us', 'bazaar-store' ), 'pattern' => 'bazaar-store/page-about' ),
			'contact-us'           => array( 'title' => __( 'Contact us', 'bazaar-store' ), 'pattern' => 'bazaar-store/page-contact' ),
			'shipping-policy'      => array( 'title' => __( 'Shipping policy', 'bazaar-store' ), 'pattern' => 'bazaar-store/page-shipping' ),
			'delivery-information' => array( 'title' => __( 'Delivery information', 'bazaar-store' ), 'pattern' => 'bazaar-store/page-delivery' ),
			'return-refund-policy' => array( 'title' => __( 'Return & refund policy', 'bazaar-store' ), 'pattern' => 'bazaar-store/page-returns' ),
			'payment-methods'      => array( 'title' => __( 'Payment methods', 'bazaar-store' ), 'pattern' => 'bazaar-store/page-payment' ),
			'terms-conditions'     => array( 'title' => __( 'Terms & conditions', 'bazaar-store' ), 'pattern' => 'bazaar-store/page-terms' ),
			'login'                => array( 'title' => __( 'Sign in', 'bazaar-store' ), 'pattern' => 'bazaar-store/page-login' ),
		);
	}
);
