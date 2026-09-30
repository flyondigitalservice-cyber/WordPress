<?php
/**
 * Customizer settings: contact details, social links and hero copy.
 *
 * @package Zobo
 */

/**
 * Default values for every Zobo setting.
 *
 * @return array
 */
function zobo_defaults() {
	return array(
		'hero_eyebrow'  => __( 'Idea → Brand → Shelf → Sales', 'zobo' ),
		'hero_title'    => __( 'You bring the idea. We build the brand that sells it.', 'zobo' ),
		'hero_text'     => __( 'Zobo is the one launch partner for D2C founders. Logo, trademark, licences, manufacturing, marketplaces, website, performance ads, influencers and TVCs, run by one team on one timeline.', 'zobo' ),
		'email'         => 'hello@zobo.co.in',
		'phone'         => '',
		'whatsapp'      => '',
		'address'       => __( 'India', 'zobo' ),
		'behance'       => 'https://www.behance.net/iamboos',
		'instagram'     => '',
		'linkedin'      => '',
		'youtube'       => '',
		'calendar_url'  => '',
		'footer_blurb'  => __( 'The launch partner for founders who want to build something that matters.', 'zobo' ),
	);
}

/**
 * Read a Zobo setting.
 *
 * @param string $key Setting key.
 * @return string
 */
function zobo_opt( $key ) {
	$defaults = zobo_defaults();
	$default  = isset( $defaults[ $key ] ) ? $defaults[ $key ] : '';
	return (string) get_theme_mod( 'zobo_' . $key, $default );
}

/**
 * Register Customizer controls.
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager.
 */
function zobo_customize_register( $wp_customize ) {
	$wp_customize->add_panel(
		'zobo',
		array(
			'title'    => __( 'Zobo Settings', 'zobo' ),
			'priority' => 30,
		)
	);

	$sections = array(
		'zobo_hero'    => array(
			'title'  => __( 'Hero', 'zobo' ),
			'fields' => array(
				'hero_eyebrow' => array( __( 'Eyebrow line', 'zobo' ), 'text' ),
				'hero_title'   => array( __( 'Headline', 'zobo' ), 'text' ),
				'hero_text'    => array( __( 'Intro text', 'zobo' ), 'textarea' ),
			),
		),
		'zobo_contact' => array(
			'title'  => __( 'Contact', 'zobo' ),
			'fields' => array(
				'email'        => array( __( 'Email (also receives form enquiries)', 'zobo' ), 'email' ),
				'phone'        => array( __( 'Phone', 'zobo' ), 'text' ),
				'whatsapp'     => array( __( 'WhatsApp number with country code, e.g. 919876543210', 'zobo' ), 'text' ),
				'address'      => array( __( 'Address / city', 'zobo' ), 'textarea' ),
				'calendar_url' => array( __( 'Booking link (Calendly, Google Calendar, etc.)', 'zobo' ), 'url' ),
			),
		),
		'zobo_social'  => array(
			'title'  => __( 'Social links', 'zobo' ),
			'fields' => array(
				'behance'   => array( __( 'Behance', 'zobo' ), 'url' ),
				'instagram' => array( __( 'Instagram', 'zobo' ), 'url' ),
				'linkedin'  => array( __( 'LinkedIn', 'zobo' ), 'url' ),
				'youtube'   => array( __( 'YouTube', 'zobo' ), 'url' ),
			),
		),
		'zobo_footer'  => array(
			'title'  => __( 'Footer', 'zobo' ),
			'fields' => array(
				'footer_blurb' => array( __( 'Footer blurb', 'zobo' ), 'textarea' ),
			),
		),
	);

	$defaults = zobo_defaults();

	foreach ( $sections as $section_id => $section ) {
		$wp_customize->add_section(
			$section_id,
			array(
				'title' => $section['title'],
				'panel' => 'zobo',
			)
		);

		foreach ( $section['fields'] as $key => $field ) {
			list( $label, $type ) = $field;

			if ( 'url' === $type ) {
				$sanitize = 'esc_url_raw';
			} elseif ( 'email' === $type ) {
				$sanitize = 'sanitize_email';
			} elseif ( 'textarea' === $type ) {
				$sanitize = 'sanitize_textarea_field';
			} else {
				$sanitize = 'sanitize_text_field';
			}

			$wp_customize->add_setting(
				'zobo_' . $key,
				array(
					'default'           => $defaults[ $key ],
					'sanitize_callback' => $sanitize,
				)
			);
			$wp_customize->add_control(
				'zobo_' . $key,
				array(
					'label'   => $label,
					'section' => $section_id,
					'type'    => $type,
				)
			);
		}
	}
}
add_action( 'customize_register', 'zobo_customize_register' );
