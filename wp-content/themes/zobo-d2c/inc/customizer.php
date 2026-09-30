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
		'hero_eyebrow'  => __( 'Idea → Brand → Shelf → Sales', 'zobo-d2c' ),
		'hero_title'    => __( 'You bring the idea. We build the brand that sells it.', 'zobo-d2c' ),
		'hero_text'     => __( 'Zobo is the one launch partner for D2C founders. Logo, trademark, licences, manufacturing, marketplaces, website, performance ads, influencers and TVCs, run by one team on one timeline.', 'zobo-d2c' ),
		'email'         => 'hello@zobo.co.in',
		'phone'         => '+91 87676 97389',
		'whatsapp'      => '918767697389',
		'address'       => __( 'Andheri East, Mumbai, Maharashtra 400069, India', 'zobo-d2c' ),
		'hours'         => __( 'Mon – Sat · 10:00 – 19:00 IST', 'zobo-d2c' ),
		'behance'       => 'https://www.behance.net/iamboos',
		'instagram'     => '',
		'linkedin'      => '',
		'youtube'       => '',
		'calendar_url'  => '',
		'footer_blurb'  => __( 'Zobo takes D2C founders from idea to shelf: brand, legal, manufacturing, testing, marketplaces and growth, under one roof.', 'zobo-d2c' ),
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
		'zobo-d2c',
		array(
			'title'    => __( 'Zobo Settings', 'zobo-d2c' ),
			'priority' => 30,
		)
	);

	$sections = array(
		'zobo_hero'    => array(
			'title'  => __( 'Hero', 'zobo-d2c' ),
			'fields' => array(
				'hero_eyebrow' => array( __( 'Eyebrow line', 'zobo-d2c' ), 'text' ),
				'hero_title'   => array( __( 'Headline', 'zobo-d2c' ), 'text' ),
				'hero_text'    => array( __( 'Intro text', 'zobo-d2c' ), 'textarea' ),
			),
		),
		'zobo_contact' => array(
			'title'  => __( 'Contact', 'zobo-d2c' ),
			'fields' => array(
				'email'        => array( __( 'Email (also receives form enquiries)', 'zobo-d2c' ), 'email' ),
				'phone'        => array( __( 'Phone', 'zobo-d2c' ), 'text' ),
				'whatsapp'     => array( __( 'WhatsApp number with country code, e.g. 919876543210', 'zobo-d2c' ), 'text' ),
				'address'      => array( __( 'Address / city', 'zobo-d2c' ), 'textarea' ),
				'hours'        => array( __( 'Working hours', 'zobo-d2c' ), 'text' ),
				'calendar_url' => array( __( 'Booking link (Calendly, Google Calendar, etc.)', 'zobo-d2c' ), 'url' ),
			),
		),
		'zobo_social'  => array(
			'title'  => __( 'Social links', 'zobo-d2c' ),
			'fields' => array(
				'behance'   => array( __( 'Behance', 'zobo-d2c' ), 'url' ),
				'instagram' => array( __( 'Instagram', 'zobo-d2c' ), 'url' ),
				'linkedin'  => array( __( 'LinkedIn', 'zobo-d2c' ), 'url' ),
				'youtube'   => array( __( 'YouTube', 'zobo-d2c' ), 'url' ),
			),
		),
		'zobo_footer'  => array(
			'title'  => __( 'Footer', 'zobo-d2c' ),
			'fields' => array(
				'footer_blurb' => array( __( 'Footer blurb', 'zobo-d2c' ), 'textarea' ),
			),
		),
	);

	$defaults = zobo_defaults();

	foreach ( $sections as $section_id => $section ) {
		$wp_customize->add_section(
			$section_id,
			array(
				'title' => $section['title'],
				'panel' => 'zobo-d2c',
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
