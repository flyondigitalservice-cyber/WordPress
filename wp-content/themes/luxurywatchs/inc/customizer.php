<?php
/**
 * Customizer options: WhatsApp, announcement, hero slides.
 *
 * @package luxurywatchs
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register customizer settings.
 *
 * @param WP_Customize_Manager $wp_customize Manager.
 */
function lw_customize_register( $wp_customize ) {
	$wp_customize->add_section(
		'lw_store',
		array(
			'title'    => __( 'LuxuryWatchs Store', 'luxurywatchs' ),
			'priority' => 30,
		)
	);

	$text_settings = array(
		'lw_whatsapp'     => array( __( 'WhatsApp number (with country code, digits only)', 'luxurywatchs' ), '917887722192' ),
		'lw_phone'        => array( __( 'Display phone', 'luxurywatchs' ), '+91 78877 22192' ),
		'lw_email'        => array( __( 'Support email', 'luxurywatchs' ), 'sales@luxurywatchs.co.in' ),
		'lw_announcement' => array( __( 'Announcement bar text', 'luxurywatchs' ), 'Free Shipping Across India  •  Cash on Delivery Available  •  7-Day Easy Returns' ),
		'lw_offer_code'   => array( __( 'Offer coupon code', 'luxurywatchs' ), 'LUXE10' ),
		'lw_instagram'    => array( __( 'Instagram URL', 'luxurywatchs' ), 'https://instagram.com/' ),
		'lw_youtube'      => array( __( 'YouTube URL', 'luxurywatchs' ), 'https://youtube.com/' ),
		'lw_facebook'     => array( __( 'Facebook URL', 'luxurywatchs' ), 'https://facebook.com/' ),
	);

	foreach ( $text_settings as $id => $data ) {
		$wp_customize->add_setting(
			$id,
			array(
				'default'           => $data[1],
				'sanitize_callback' => 'sanitize_text_field',
			)
		);
		$wp_customize->add_control(
			$id,
			array(
				'label'   => $data[0],
				'section' => 'lw_store',
				'type'    => 'text',
			)
		);
	}

	$wp_customize->add_section(
		'lw_hero',
		array(
			'title'    => __( 'Homepage Hero Slides', 'luxurywatchs' ),
			'priority' => 31,
		)
	);

	for ( $i = 1; $i <= 3; $i++ ) {
		$wp_customize->add_setting( "lw_hero_img_$i", array( 'sanitize_callback' => 'esc_url_raw' ) );
		$wp_customize->add_control(
			new WP_Customize_Image_Control(
				$wp_customize,
				"lw_hero_img_$i",
				array(
					/* translators: %d slide number */
					'label'   => sprintf( __( 'Slide %d image', 'luxurywatchs' ), $i ),
					'section' => 'lw_hero',
				)
			)
		);
	}
}
add_action( 'customize_register', 'lw_customize_register' );
