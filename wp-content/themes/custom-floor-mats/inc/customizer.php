<?php
/**
 * Customizer: every header, footer, contact and brand detail is editable here
 * (Appearance → Customize → "Custom Floor Mats").
 *
 * @package CFM
 */

defined( 'ABSPATH' ) || exit;

add_action( 'customize_register', 'cfm_customize_register' );
/**
 * Register panels, sections and controls.
 *
 * @param WP_Customize_Manager $wp_customize Manager.
 */
function cfm_customize_register( $wp_customize ) {
	$wp_customize->add_panel(
		'cfm_panel',
		array(
			'title'    => __( 'Custom Floor Mats', 'cfm' ),
			'priority' => 20,
		)
	);

	$sections = array(
		'cfm_contact' => array(
			'title'  => __( 'Contact & WhatsApp', 'cfm' ),
			'fields' => array(
				'whatsapp_number'  => array( __( 'WhatsApp number (international, e.g. 9715XXXXXXXX)', 'cfm' ), 'text', __( 'Every quote/contact button opens a WhatsApp chat to this number.', 'cfm' ) ),
				'whatsapp_message' => array( __( 'Default WhatsApp message', 'cfm' ), 'textarea', __( 'Product and area pages prefill their own page name automatically.', 'cfm' ) ),
				'phone_display'    => array( __( 'Phone (as displayed)', 'cfm' ), 'text', '' ),
				'phone_link'       => array( __( 'Phone (for tel: link, e.g. +9714XXXXXXX)', 'cfm' ), 'text', '' ),
				'email'            => array( __( 'Email', 'cfm' ), 'email', '' ),
				'address'          => array( __( 'Address', 'cfm' ), 'textarea', '' ),
				'hours'            => array( __( 'Opening hours', 'cfm' ), 'text', '' ),
				'map_embed_url'    => array( __( 'Google Maps embed URL (optional)', 'cfm' ), 'url', __( 'Google Maps → Share → Embed a map → copy only the src="…" URL.', 'cfm' ) ),
			),
		),
		'cfm_header'  => array(
			'title'  => __( 'Header', 'cfm' ),
			'fields' => array(
				'show_topbar'      => array( __( 'Show top bar', 'cfm' ), 'checkbox', '' ),
				'topbar_text'      => array( __( 'Top bar text', 'cfm' ), 'text', '' ),
				'header_cta_label' => array( __( 'Header button label', 'cfm' ), 'text', '' ),
			),
		),
		'cfm_brand'   => array(
			'title'  => __( 'Brand & Mother Company', 'cfm' ),
			'fields' => array(
				'brand_name'    => array( __( 'Brand name', 'cfm' ), 'text', __( 'Upload the logo under Site Identity.', 'cfm' ) ),
				'brand_tagline' => array( __( 'Brand tagline', 'cfm' ), 'text', '' ),
				'parent_name'   => array( __( 'Mother company name', 'cfm' ), 'text', '' ),
				'parent_url'    => array( __( 'Mother company website', 'cfm' ), 'url', '' ),
				'parent_blurb'  => array( __( 'Mother company line (footer & About page)', 'cfm' ), 'textarea', '' ),
			),
		),
		'cfm_footer'  => array(
			'title'  => __( 'Footer & Social', 'cfm' ),
			'fields' => array(
				'footer_about'     => array( __( 'Footer about text', 'cfm' ), 'textarea', '' ),
				'footer_copyright' => array( __( 'Copyright line ({year} = current year)', 'cfm' ), 'text', '' ),
				'social_instagram' => array( __( 'Instagram URL', 'cfm' ), 'url', '' ),
				'social_facebook'  => array( __( 'Facebook URL', 'cfm' ), 'url', '' ),
				'social_linkedin'  => array( __( 'LinkedIn URL', 'cfm' ), 'url', '' ),
				'social_youtube'   => array( __( 'YouTube URL', 'cfm' ), 'url', '' ),
				'social_tiktok'    => array( __( 'TikTok URL', 'cfm' ), 'url', '' ),
			),
		),
		'cfm_stats'   => array(
			'title'  => __( 'Home page highlights', 'cfm' ),
			'fields' => array(
				'stat1_value' => array( __( 'Highlight 1 value', 'cfm' ), 'text', '' ),
				'stat1_label' => array( __( 'Highlight 1 label', 'cfm' ), 'text', '' ),
				'stat2_value' => array( __( 'Highlight 2 value', 'cfm' ), 'text', '' ),
				'stat2_label' => array( __( 'Highlight 2 label', 'cfm' ), 'text', '' ),
				'stat3_value' => array( __( 'Highlight 3 value', 'cfm' ), 'text', '' ),
				'stat3_label' => array( __( 'Highlight 3 label', 'cfm' ), 'text', '' ),
				'stat4_value' => array( __( 'Highlight 4 value', 'cfm' ), 'text', '' ),
				'stat4_label' => array( __( 'Highlight 4 label', 'cfm' ), 'text', '' ),
			),
		),
		'cfm_leads'   => array(
			'title'  => __( 'Leads', 'cfm' ),
			'fields' => array(
				'lead_email' => array( __( 'Send a copy of every lead to (email)', 'cfm' ), 'email', __( 'Leave empty to use the site admin email. All leads are also stored under wp-admin → Leads.', 'cfm' ) ),
			),
		),
	);

	$priority = 10;
	foreach ( $sections as $section_id => $section ) {
		$wp_customize->add_section(
			$section_id,
			array(
				'title'    => $section['title'],
				'panel'    => 'cfm_panel',
				'priority' => $priority,
			)
		);
		$priority += 10;

		foreach ( $section['fields'] as $key => $field ) {
			list( $label, $type, $description ) = $field;
			$setting_id = 'cfm_' . $key;
			$sanitize   = 'sanitize_text_field';
			if ( 'textarea' === $type ) {
				$sanitize = 'sanitize_textarea_field';
			} elseif ( 'url' === $type ) {
				$sanitize = 'esc_url_raw';
			} elseif ( 'email' === $type ) {
				$sanitize = 'sanitize_email';
			} elseif ( 'checkbox' === $type ) {
				$sanitize = 'cfm_sanitize_checkbox';
			}

			$wp_customize->add_setting(
				$setting_id,
				array(
					'default'           => cfm_defaults()[ $key ],
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
		$wp_customize->selective_refresh->add_partial( 'cfm_topbar_text', array( 'selector' => '.cfm-topbar__text' ) );
		$wp_customize->selective_refresh->add_partial( 'cfm_header_cta_label', array( 'selector' => '.cfm-header__cta' ) );
		$wp_customize->selective_refresh->add_partial( 'cfm_footer_about', array( 'selector' => '.cfm-footer__about' ) );
		$wp_customize->selective_refresh->add_partial( 'cfm_parent_blurb', array( 'selector' => '.cfm-footer__parent' ) );
		$wp_customize->selective_refresh->add_partial( 'cfm_footer_copyright', array( 'selector' => '.cfm-footer__copy' ) );
		$wp_customize->selective_refresh->add_partial( 'cfm_whatsapp_number', array( 'selector' => '.cfm-footer__contact' ) );
	}
}

/**
 * Checkbox sanitizer.
 *
 * @param mixed $value Value.
 * @return string
 */
function cfm_sanitize_checkbox( $value ) {
	return ( ! empty( $value ) && 'false' !== $value ) ? '1' : '';
}
