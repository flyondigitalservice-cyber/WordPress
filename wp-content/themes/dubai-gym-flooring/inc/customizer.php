<?php
/**
 * Customizer: every header, footer, contact and brand detail is editable here
 * (Appearance → Customize → "Dubai Gym Flooring").
 *
 * @package DGF
 */

defined( 'ABSPATH' ) || exit;

add_action( 'customize_register', 'dgf_customize_register' );
/**
 * Register panels, sections and controls.
 *
 * @param WP_Customize_Manager $wp_customize Manager.
 */
function dgf_customize_register( $wp_customize ) {
	$wp_customize->add_panel(
		'dgf_panel',
		array(
			'title'    => __( 'Dubai Gym Flooring', 'dgf' ),
			'priority' => 20,
		)
	);

	$sections = array(
		'dgf_contact' => array(
			'title'  => __( 'Contact & WhatsApp', 'dgf' ),
			'fields' => array(
				'whatsapp_number'  => array( __( 'WhatsApp number (international, e.g. 9715XXXXXXXX)', 'dgf' ), 'text', __( 'Every quote/contact button opens a WhatsApp chat to this number.', 'dgf' ) ),
				'whatsapp_message' => array( __( 'Default WhatsApp message', 'dgf' ), 'textarea', __( 'Product and area pages prefill their own page name automatically.', 'dgf' ) ),
				'phone_display'    => array( __( 'Phone (as displayed)', 'dgf' ), 'text', '' ),
				'phone_link'       => array( __( 'Phone (for tel: link, e.g. +9714XXXXXXX)', 'dgf' ), 'text', '' ),
				'email'            => array( __( 'Email', 'dgf' ), 'email', '' ),
				'address'          => array( __( 'Address', 'dgf' ), 'textarea', '' ),
				'hours'            => array( __( 'Opening hours', 'dgf' ), 'text', '' ),
				'map_embed_url'    => array( __( 'Google Maps embed URL (optional)', 'dgf' ), 'url', __( 'Google Maps → Share → Embed a map → copy only the src="…" URL.', 'dgf' ) ),
			),
		),
		'dgf_header'  => array(
			'title'  => __( 'Header', 'dgf' ),
			'fields' => array(
				'show_topbar'      => array( __( 'Show top bar', 'dgf' ), 'checkbox', '' ),
				'topbar_text'      => array( __( 'Top bar text', 'dgf' ), 'text', '' ),
				'header_cta_label' => array( __( 'Header button label', 'dgf' ), 'text', '' ),
			),
		),
		'dgf_brand'   => array(
			'title'  => __( 'Brand & Mother Company', 'dgf' ),
			'fields' => array(
				'brand_name'    => array( __( 'Brand name', 'dgf' ), 'text', __( 'Upload the logo under Site Identity.', 'dgf' ) ),
				'brand_tagline' => array( __( 'Brand tagline', 'dgf' ), 'text', '' ),
				'parent_name'   => array( __( 'Mother company name', 'dgf' ), 'text', '' ),
				'parent_url'    => array( __( 'Mother company website', 'dgf' ), 'url', '' ),
				'parent_blurb'  => array( __( 'Mother company line (footer & About page)', 'dgf' ), 'textarea', '' ),
			),
		),
		'dgf_footer'  => array(
			'title'  => __( 'Footer & Social', 'dgf' ),
			'fields' => array(
				'footer_about'     => array( __( 'Footer about text', 'dgf' ), 'textarea', '' ),
				'footer_copyright' => array( __( 'Copyright line ({year} = current year)', 'dgf' ), 'text', '' ),
				'social_instagram' => array( __( 'Instagram URL', 'dgf' ), 'url', '' ),
				'social_facebook'  => array( __( 'Facebook URL', 'dgf' ), 'url', '' ),
				'social_linkedin'  => array( __( 'LinkedIn URL', 'dgf' ), 'url', '' ),
				'social_youtube'   => array( __( 'YouTube URL', 'dgf' ), 'url', '' ),
				'social_tiktok'    => array( __( 'TikTok URL', 'dgf' ), 'url', '' ),
			),
		),
		'dgf_stats'   => array(
			'title'  => __( 'Home page highlights', 'dgf' ),
			'fields' => array(
				'stat1_value' => array( __( 'Highlight 1 value', 'dgf' ), 'text', '' ),
				'stat1_label' => array( __( 'Highlight 1 label', 'dgf' ), 'text', '' ),
				'stat2_value' => array( __( 'Highlight 2 value', 'dgf' ), 'text', '' ),
				'stat2_label' => array( __( 'Highlight 2 label', 'dgf' ), 'text', '' ),
				'stat3_value' => array( __( 'Highlight 3 value', 'dgf' ), 'text', '' ),
				'stat3_label' => array( __( 'Highlight 3 label', 'dgf' ), 'text', '' ),
				'stat4_value' => array( __( 'Highlight 4 value', 'dgf' ), 'text', '' ),
				'stat4_label' => array( __( 'Highlight 4 label', 'dgf' ), 'text', '' ),
			),
		),
		'dgf_leads'   => array(
			'title'  => __( 'Leads', 'dgf' ),
			'fields' => array(
				'lead_email' => array( __( 'Send a copy of every lead to (email)', 'dgf' ), 'email', __( 'Leave empty to use the site admin email. All leads are also stored under wp-admin → Leads.', 'dgf' ) ),
			),
		),
	);

	$priority = 10;
	foreach ( $sections as $section_id => $section ) {
		$wp_customize->add_section(
			$section_id,
			array(
				'title'    => $section['title'],
				'panel'    => 'dgf_panel',
				'priority' => $priority,
			)
		);
		$priority += 10;

		foreach ( $section['fields'] as $key => $field ) {
			list( $label, $type, $description ) = $field;
			$setting_id = 'dgf_' . $key;
			$sanitize   = 'sanitize_text_field';
			if ( 'textarea' === $type ) {
				$sanitize = 'sanitize_textarea_field';
			} elseif ( 'url' === $type ) {
				$sanitize = 'esc_url_raw';
			} elseif ( 'email' === $type ) {
				$sanitize = 'sanitize_email';
			} elseif ( 'checkbox' === $type ) {
				$sanitize = 'dgf_sanitize_checkbox';
			}

			$wp_customize->add_setting(
				$setting_id,
				array(
					'default'           => dgf_defaults()[ $key ],
					'sanitize_callback' => $sanitize,
					'transport'         => 'refresh',
				)
			);
			$wp_customize->add_control(
				$setting_id,
				array(
					'label'       => $label,
					'description' => $description,
					'section'     => $section_id,
					'type'        => $type,
				)
			);
		}
	}

	// Live-edit pencils on the front end inside the Customizer preview.
	if ( isset( $wp_customize->selective_refresh ) ) {
		$wp_customize->selective_refresh->add_partial( 'dgf_topbar_text', array( 'selector' => '.dgf-topbar__text' ) );
		$wp_customize->selective_refresh->add_partial( 'dgf_header_cta_label', array( 'selector' => '.dgf-header__cta' ) );
		$wp_customize->selective_refresh->add_partial( 'dgf_footer_about', array( 'selector' => '.dgf-footer__about' ) );
		$wp_customize->selective_refresh->add_partial( 'dgf_parent_blurb', array( 'selector' => '.dgf-footer__parent' ) );
		$wp_customize->selective_refresh->add_partial( 'dgf_footer_copyright', array( 'selector' => '.dgf-footer__copy' ) );
		$wp_customize->selective_refresh->add_partial( 'dgf_whatsapp_number', array( 'selector' => '.dgf-footer__contact' ) );
	}
}

/**
 * Checkbox sanitizer.
 *
 * @param mixed $value Value.
 * @return string
 */
function dgf_sanitize_checkbox( $value ) {
	return ( ! empty( $value ) && 'false' !== $value ) ? '1' : '';
}
