<?php
/**
 * Customizer: every business, header and footer detail is editable here.
 *
 * @package dce-vibe
 */

defined( 'ABSPATH' ) || exit;

add_action( 'customize_register', 'dce_customize_register' );
function dce_customize_register( $wp_customize ) {
	$wp_customize->add_panel(
		'dce_panel',
		array(
			'title'       => __( 'DCE Business, Header & Footer', 'dce-vibe' ),
			'description' => __( 'Contact details here are used everywhere: header, footer, WhatsApp buttons, lead forms and SEO schema.', 'dce-vibe' ),
			'priority'    => 20,
		)
	);

	$sections = array(
		'dce_business' => array(
			'title'  => __( 'Business details', 'dce-vibe' ),
			'fields' => array(
				'brand'       => array( __( 'Brand name', 'dce-vibe' ), 'text' ),
				'brand_sub'   => array( __( 'Brand subtitle (under logo)', 'dce-vibe' ), 'text' ),
				'legal_name'  => array( __( 'Legal company name', 'dce-vibe' ), 'text' ),
				'phone'       => array( __( 'Phone (display)', 'dce-vibe' ), 'text' ),
				'email'       => array( __( 'Email', 'dce-vibe' ), 'email' ),
				'address'     => array( __( 'Showroom address', 'dce-vibe' ), 'textarea' ),
				'map_url'     => array( __( 'Google Maps link', 'dce-vibe' ), 'url' ),
				'hours'       => array( __( 'Opening hours', 'dce-vibe' ), 'text' ),
				'geo_lat'     => array( __( 'Latitude (SEO schema)', 'dce-vibe' ), 'text' ),
				'geo_lng'     => array( __( 'Longitude (SEO schema)', 'dce-vibe' ), 'text' ),
				'price_range' => array( __( 'Price range (SEO schema)', 'dce-vibe' ), 'text' ),
			),
		),
		'dce_whatsapp' => array(
			'title'  => __( 'WhatsApp & leads', 'dce-vibe' ),
			'fields' => array(
				'whatsapp'   => array( __( 'WhatsApp number (international, digits only, e.g. 971508599803)', 'dce-vibe' ), 'text' ),
				'wa_message' => array( __( 'Default WhatsApp message', 'dce-vibe' ), 'textarea' ),
				'lead_email' => array( __( 'Send a copy of every lead to this email', 'dce-vibe' ), 'email' ),
				'float_wa'   => array( __( 'Show floating WhatsApp button', 'dce-vibe' ), 'checkbox' ),
				'mobile_bar' => array( __( 'Show mobile Call / WhatsApp / Quote bar', 'dce-vibe' ), 'checkbox' ),
			),
		),
		'dce_header'   => array(
			'title'  => __( 'Header', 'dce-vibe' ),
			'fields' => array(
				'logo_with_text'  => array( __( 'Show brand name next to the logo', 'dce-vibe' ), 'checkbox' ),
				'topbar_show'     => array( __( 'Show top announcement bar', 'dce-vibe' ), 'checkbox' ),
				'topbar_text'     => array( __( 'Top bar text', 'dce-vibe' ), 'text' ),
				'header_cta_text' => array( __( 'Header button text', 'dce-vibe' ), 'text' ),
				'header_cta_url'  => array( __( 'Header button link (#quote opens the quote form, #whatsapp opens WhatsApp)', 'dce-vibe' ), 'text' ),
			),
		),
		'dce_parent'   => array(
			'title'  => __( 'Parent company (Casa Vera Home)', 'dce-vibe' ),
			'fields' => array(
				'parent_name' => array( __( 'Parent company name', 'dce-vibe' ), 'text' ),
				'parent_url'  => array( __( 'Parent company website', 'dce-vibe' ), 'url' ),
				'parent_text' => array( __( 'Parent company strip text (above footer)', 'dce-vibe' ), 'textarea' ),
			),
		),
		'dce_footer'   => array(
			'title'  => __( 'Footer', 'dce-vibe' ),
			'fields' => array(
				'footer_about' => array( __( 'Footer about text', 'dce-vibe' ), 'textarea' ),
				'footer_copy'  => array( __( 'Copyright line ({year} = current year)', 'dce-vibe' ), 'text' ),
			),
		),
		'dce_social'   => array(
			'title'  => __( 'Social profiles', 'dce-vibe' ),
			'fields' => array(
				'instagram' => array( 'Instagram URL', 'url' ),
				'facebook'  => array( 'Facebook URL', 'url' ),
				'tiktok'    => array( 'TikTok URL', 'url' ),
				'youtube'   => array( 'YouTube URL', 'url' ),
				'linkedin'  => array( 'LinkedIn URL', 'url' ),
			),
		),
	);

	$defaults = dce_defaults();
	foreach ( $sections as $section_id => $section ) {
		$wp_customize->add_section(
			$section_id,
			array(
				'title' => $section['title'],
				'panel' => 'dce_panel',
			)
		);
		foreach ( $section['fields'] as $key => $field ) {
			list( $label, $type ) = $field;
			$sanitize = 'sanitize_text_field';
			if ( 'url' === $type ) {
				$sanitize = 'esc_url_raw';
			} elseif ( 'email' === $type ) {
				$sanitize = 'sanitize_email';
			} elseif ( 'textarea' === $type ) {
				$sanitize = 'sanitize_textarea_field';
			} elseif ( 'checkbox' === $type ) {
				$sanitize = 'dce_sanitize_checkbox';
			}
			$wp_customize->add_setting(
				'dce_' . $key,
				array(
					'default'           => isset( $defaults[ $key ] ) ? $defaults[ $key ] : '',
					'sanitize_callback' => $sanitize,
					'transport'         => 'refresh',
				)
			);
			$wp_customize->add_control(
				'dce_' . $key,
				array(
					'label'   => $label,
					'section' => $section_id,
					'type'    => $type,
				)
			);
		}
	}
}

function dce_sanitize_checkbox( $value ) {
	return $value ? 1 : 0;
}
