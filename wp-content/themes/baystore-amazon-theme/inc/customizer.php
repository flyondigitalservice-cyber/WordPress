<?php
/**
 * Baystore Amazon Lean — WordPress Customizer Controls
 *
 * @package Baystore_Amazon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'customize_register', function ( $wp_customize ) {
	$wp_customize->add_section( 'baystore_cro_section', array(
		'title'    => __( 'Baystore Lean Conversion Settings', 'baystore-amazon' ),
		'priority' => 30,
	) );

	$wp_customize->add_setting( 'baystore_free_shipping_threshold', array(
		'default'           => 499,
		'sanitize_callback' => 'absint',
	) );
	$wp_customize->add_control( 'baystore_free_shipping_threshold', array(
		'label'   => __( 'Free Delivery Threshold (INR)', 'baystore-amazon' ),
		'section' => 'baystore_cro_section',
		'type'    => 'number',
	) );

	$wp_customize->add_setting( 'baystore_coupon_code', array(
		'default'           => 'BAY10',
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'baystore_coupon_code', array(
		'label'   => __( 'Active Header/Drawer Coupon Code', 'baystore-amazon' ),
		'section' => 'baystore_cro_section',
		'type'    => 'text',
	) );

	$wp_customize->add_setting( 'baystore_hero_headline', array(
		'default'           => 'Smart Kitchen & Storage Essentials',
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'baystore_hero_headline', array(
		'label'   => __( 'Hero Slide 1 Headline', 'baystore-amazon' ),
		'section' => 'baystore_cro_section',
		'type'    => 'text',
	) );

	$wp_customize->add_setting( 'baystore_hero_sub', array(
		'default'           => 'Stainless Steel Chopping Boards, Fridge Organisers & 200W Coffee Grinders — Up to 50% Off + Free Delivery ≥ ₹499',
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'baystore_hero_sub', array(
		'label'   => __( 'Hero Slide 1 Subtitle', 'baystore-amazon' ),
		'section' => 'baystore_cro_section',
		'type'    => 'textarea',
	) );
} );
