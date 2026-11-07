<?php
/**
 * Customizer: every business setting is editable from Appearance → Customize → Tasdir Settings.
 *
 * @package Tasdir
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register Customizer panels, sections and controls.
 *
 * @param WP_Customize_Manager $wp_customize Manager.
 */
function tasdir_customize_register( $wp_customize ) {
	$d = tasdir_defaults();

	$wp_customize->add_panel(
		'tasdir',
		array(
			'title'    => __( 'Tasdir Settings', 'tasdir' ),
			'priority' => 20,
		)
	);

	$sections = array(
		'tasdir_contact'  => __( 'WhatsApp & Contact', 'tasdir' ),
		'tasdir_hero'     => __( 'Home Hero & Sections', 'tasdir' ),
		'tasdir_tracking' => __( 'Analytics & Pixels', 'tasdir' ),
		'tasdir_seo'      => __( 'SEO', 'tasdir' ),
	);
	foreach ( $sections as $id => $title ) {
		$wp_customize->add_section( $id, array( 'title' => $title, 'panel' => 'tasdir' ) );
	}

	$add = static function ( $key, $section, $label, $type = 'text', $description = '', $sanitize = 'sanitize_text_field' ) use ( $wp_customize, $d ) {
		$wp_customize->add_setting(
			$key,
			array(
				'default'           => isset( $d[ $key ] ) ? $d[ $key ] : '',
				'sanitize_callback' => $sanitize,
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			$key,
			array(
				'label'       => $label,
				'description' => $description,
				'section'     => $section,
				'type'        => $type,
			)
		);
	};

	$checkbox = static function ( $v ) {
		return $v ? 1 : 0;
	};

	// WhatsApp & contact.
	$add( 'whatsapp_number', 'tasdir_contact', __( 'WhatsApp number', 'tasdir' ), 'text', __( 'International format with country code, e.g. +91 98765 43210. All WhatsApp buttons and the enquiry form use this.', 'tasdir' ) );
	$add( 'whatsapp_message', 'tasdir_contact', __( 'Default WhatsApp message', 'tasdir' ), 'textarea', '', 'sanitize_textarea_field' );
	$add( 'lead_email', 'tasdir_contact', __( 'Send enquiry alerts to (email)', 'tasdir' ), 'email', __( 'Leave blank to use the site admin email.', 'tasdir' ), 'sanitize_email' );
	$add( 'company_name', 'tasdir_contact', __( 'Legal company name', 'tasdir' ) );
	$add( 'phone', 'tasdir_contact', __( 'Phone', 'tasdir' ) );
	$add( 'email', 'tasdir_contact', __( 'Public email', 'tasdir' ), 'email', '', 'sanitize_email' );
	$add( 'address', 'tasdir_contact', __( 'Address', 'tasdir' ), 'textarea', '', 'sanitize_textarea_field' );
	$add( 'hours', 'tasdir_contact', __( 'Business hours', 'tasdir' ) );
	$add( 'facebook_url', 'tasdir_contact', __( 'Facebook URL', 'tasdir' ), 'url', '', 'esc_url_raw' );
	$add( 'instagram_url', 'tasdir_contact', __( 'Instagram URL', 'tasdir' ), 'url', '', 'esc_url_raw' );
	$add( 'linkedin_url', 'tasdir_contact', __( 'LinkedIn URL', 'tasdir' ), 'url', '', 'esc_url_raw' );
	$add( 'youtube_url', 'tasdir_contact', __( 'YouTube URL', 'tasdir' ), 'url', '', 'esc_url_raw' );
	$add( 'show_wa_float', 'tasdir_contact', __( 'Show floating WhatsApp button', 'tasdir' ), 'checkbox', '', $checkbox );
	$add( 'show_mobile_bar', 'tasdir_contact', __( 'Show sticky mobile action bar', 'tasdir' ), 'checkbox', '', $checkbox );
	$add( 'show_topbar', 'tasdir_contact', __( 'Show top contact bar', 'tasdir' ), 'checkbox', '', $checkbox );

	// Hero & sections.
	$add( 'hero_eyebrow', 'tasdir_hero', __( 'Hero small heading', 'tasdir' ) );
	$add( 'hero_title', 'tasdir_hero', __( 'Hero title', 'tasdir' ), 'textarea', __( 'Wrap words in <em></em> to highlight them in gold.', 'tasdir' ), 'tasdir_sanitize_title' );
	$add( 'hero_text', 'tasdir_hero', __( 'Hero text', 'tasdir' ), 'textarea', '', 'sanitize_textarea_field' );
	$wp_customize->add_setting( 'hero_image', array( 'default' => 0, 'sanitize_callback' => 'absint' ) );
	$wp_customize->add_control(
		new WP_Customize_Media_Control(
			$wp_customize,
			'hero_image',
			array(
				'label'     => __( 'Hero background image', 'tasdir' ),
				'section'   => 'tasdir_hero',
				'mime_type' => 'image',
			)
		)
	);
	$add( 'markets', 'tasdir_hero', __( 'Export markets (one per line)', 'tasdir' ), 'textarea', '', 'sanitize_textarea_field' );
	$add( 'footer_text', 'tasdir_hero', __( 'Footer about text', 'tasdir' ), 'textarea', '', 'sanitize_textarea_field' );

	// Tracking.
	$add( 'ga4_id', 'tasdir_tracking', __( 'Google Analytics 4 Measurement ID', 'tasdir' ), 'text', __( 'Format: G-XXXXXXXXXX', 'tasdir' ), 'tasdir_sanitize_id' );
	$add( 'gtm_id', 'tasdir_tracking', __( 'Google Tag Manager ID (optional)', 'tasdir' ), 'text', __( 'Format: GTM-XXXXXXX. If you use GTM to load GA4, leave the GA4 field blank to avoid double counting.', 'tasdir' ), 'tasdir_sanitize_id' );
	$add( 'fb_pixel_id', 'tasdir_tracking', __( 'Meta (Facebook) Pixel ID', 'tasdir' ), 'text', __( 'Numbers only. Fires PageView, ViewContent on products, Contact on WhatsApp clicks and Lead on enquiry submit.', 'tasdir' ), 'tasdir_sanitize_id' );
	$add( 'google_verify', 'tasdir_tracking', __( 'Google Search Console verification code', 'tasdir' ), 'text', __( 'Only the content="…" value.', 'tasdir' ) );
	$add( 'bing_verify', 'tasdir_tracking', __( 'Bing Webmaster verification code', 'tasdir' ) );
	$add( 'fb_domain_verify', 'tasdir_tracking', __( 'Facebook domain verification code', 'tasdir' ) );
	$add( 'track_admins', 'tasdir_tracking', __( 'Also track logged-in administrators', 'tasdir' ), 'checkbox', '', $checkbox );
	$add( 'head_code', 'tasdir_tracking', __( 'Extra <head> code (advanced)', 'tasdir' ), 'textarea', __( 'Other verification tags or scripts. Only users who can edit theme options can change this.', 'tasdir' ), 'tasdir_sanitize_head_code' );

	// SEO.
	$add( 'seo_home_title', 'tasdir_seo', __( 'Home page SEO title', 'tasdir' ) );
	$add( 'seo_home_desc', 'tasdir_seo', __( 'Home page meta description', 'tasdir' ), 'textarea', __( 'About 150–160 characters.', 'tasdir' ), 'sanitize_textarea_field' );
	$wp_customize->add_setting( 'seo_og_image', array( 'default' => 0, 'sanitize_callback' => 'absint' ) );
	$wp_customize->add_control(
		new WP_Customize_Media_Control(
			$wp_customize,
			'seo_og_image',
			array(
				'label'       => __( 'Default social share image', 'tasdir' ),
				'description' => __( '1200×630 recommended. Used when a page has no featured image.', 'tasdir' ),
				'section'     => 'tasdir_seo',
				'mime_type'   => 'image',
			)
		)
	);
}
add_action( 'customize_register', 'tasdir_customize_register' );

/**
 * Sanitize the hero title (allow em/strong/br).
 *
 * @param string $v Value.
 * @return string
 */
function tasdir_sanitize_title( $v ) {
	return wp_kses( $v, tasdir_kses_title() );
}

/**
 * Sanitize tracking IDs (letters, digits, dash).
 *
 * @param string $v Value.
 * @return string
 */
function tasdir_sanitize_id( $v ) {
	return strtoupper( preg_replace( '/[^A-Za-z0-9\-]/', '', (string) $v ) );
}

/**
 * Raw head code is only stored for users allowed to post unfiltered HTML.
 *
 * @param string $v Value.
 * @return string
 */
function tasdir_sanitize_head_code( $v ) {
	return current_user_can( 'unfiltered_html' ) ? (string) $v : wp_kses_post( $v );
}

/**
 * Admin notice until the WhatsApp number is configured.
 */
function tasdir_setup_notice() {
	if ( tasdir_wa_number() || ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}
	$url = admin_url( 'customize.php?autofocus[section]=tasdir_contact' );
	printf(
		'<div class="notice notice-warning"><p><strong>%1$s</strong> %2$s <a href="%3$s">%4$s</a></p></div>',
		esc_html__( 'Tasdir theme:', 'tasdir' ),
		esc_html__( 'add your WhatsApp number so enquiries reach your phone.', 'tasdir' ),
		esc_url( $url ),
		esc_html__( 'Open settings →', 'tasdir' )
	);
}
add_action( 'admin_notices', 'tasdir_setup_notice' );
