<?php
/**
 * Shared helpers: options with defaults, WhatsApp links, icons, page lookup.
 *
 * @package CFM
 */

defined( 'ABSPATH' ) || exit;

/**
 * Default values for every Customizer setting. Anything here can be changed in
 * Appearance → Customize without touching code.
 *
 * @return array<string,string>
 */
function cfm_defaults() {
	return array(
		// Contact & WhatsApp.
		'whatsapp_number'  => '971508599803',
		'whatsapp_message' => 'Hello Custom Floor Mats, I would like a quote for a custom mat.',
		'phone_display'    => '+971 50 859 9803',
		'phone_link'       => '+971508599803',
		'email'            => 'info@customfloormats.ae',
		'address'          => 'Dubai, United Arab Emirates',
		'hours'            => 'Mon – Sat: 9:00 AM – 7:00 PM',
		'map_embed_url'    => '',
		// Header.
		'topbar_text'      => 'Free logo mockup · Made to any size · Delivery across all 7 emirates',
		'header_cta_label' => 'Get a Quote on WhatsApp',
		'show_topbar'      => '1',
		// Brand / mother company.
		'brand_name'       => 'Custom Floor Mats',
		'brand_tagline'    => 'Custom logo, entrance & safety mats across the UAE',
		'parent_name'      => 'Casa Vera Home',
		'parent_url'       => 'https://casaverahome.ae/',
		'parent_blurb'     => 'Custom Floor Mats is the matting division of Casa Vera Home — flooring, interiors and home solutions across the UAE.',
		// Footer.
		'footer_about'     => 'Custom logo mats, entrance systems, anti-fatigue, rubber, wet-area and car mats — designed, made to size and delivered for offices, hotels, restaurants, villas and gyms across Dubai and the UAE.',
		'footer_copyright' => '© {year} Custom Floor Mats. All rights reserved.',
		'social_instagram' => '',
		'social_facebook'  => '',
		'social_linkedin'  => '',
		'social_youtube'   => '',
		'social_tiktok'    => '',
		// Trust stats on the home page (edit to your real numbers).
		'stat1_value'      => '7',
		'stat1_label'      => 'Emirates served',
		'stat2_value'      => '12',
		'stat2_label'      => 'Mat types, made to size',
		'stat3_value'      => 'Free',
		'stat3_label'      => 'Logo mockup',
		'stat4_value'      => '1:1',
		'stat4_label'      => 'WhatsApp support',
		// Leads.
		'lead_email'       => 'casaverahome07@gmail.com',
	);
}

/**
 * Read a theme option with its default.
 *
 * @param string $key Setting key without prefix.
 * @return string
 */
function cfm_opt( $key ) {
	$defaults = cfm_defaults();
	$default  = isset( $defaults[ $key ] ) ? $defaults[ $key ] : '';
	$value    = get_theme_mod( 'cfm_' . $key, $default );
	return is_string( $value ) ? $value : (string) $value;
}

/**
 * WhatsApp number in international format, digits only (e.g. 9715XXXXXXXX).
 *
 * @return string
 */
function cfm_wa_number() {
	$number = preg_replace( '/\D+/', '', cfm_opt( 'whatsapp_number' ) );
	if ( 0 === strpos( $number, '00' ) ) {
		$number = substr( $number, 2 );
	}
	// Local UAE mobile written as 05XXXXXXXX → 9715XXXXXXXX.
	if ( 10 === strlen( $number ) && 0 === strpos( $number, '05' ) ) {
		$number = '971' . substr( $number, 1 );
	}
	return $number;
}

/**
 * Build a wa.me click-to-chat URL with a prefilled message.
 *
 * Falls back to the Contact page when no number is configured so that no
 * button ever points to a dead link.
 *
 * @param string $message Prefilled text.
 * @return string
 */
function cfm_wa_url( $message = '' ) {
	$number = cfm_wa_number();
	if ( '' === $number ) {
		return cfm_page_url( 'contact' ) . '#quote';
	}
	if ( '' === $message ) {
		$message = cfm_opt( 'whatsapp_message' );
	}
	return 'https://wa.me/' . $number . '?text=' . rawurlencode( $message );
}

/**
 * Prefilled WhatsApp message for the current page.
 *
 * @param int|null $post_id Page ID, current page when null.
 * @return string
 */
function cfm_wa_message_for( $post_id = null ) {
	$post_id = $post_id ? $post_id : get_queried_object_id();
	if ( ! $post_id || (int) get_option( 'page_on_front' ) === (int) $post_id ) {
		return cfm_opt( 'whatsapp_message' );
	}
	$title = wp_strip_all_tags( get_the_title( $post_id ) );
	return sprintf(
		/* translators: 1: page title, 2: page URL */
		__( 'Hello %1$s, I am interested in %2$s. Please share price and availability. (%3$s)', 'cfm' ),
		cfm_opt( 'brand_name' ),
		$title,
		get_permalink( $post_id )
	);
}

/**
 * Permalink of a page by path (e.g. "floor-mats/custom-logo-mats")
 * or by slug alone.
 *
 * @param string $path Page path or slug.
 * @return string
 */
function cfm_page_url( $path ) {
	$page = cfm_get_page( $path );
	return $page ? get_permalink( $page ) : home_url( '/' . trim( $path, '/' ) . '/' );
}

/**
 * Find a page by path, falling back to slug.
 *
 * @param string $path Page path or slug.
 * @return WP_Post|null
 */
function cfm_get_page( $path ) {
	// Pages created or adopted by the installer are tracked by ID (an adopted page may keep its own slug).
	$ids = get_option( 'cfm_page_ids', array() );
	$key = basename( trim( $path, '/' ) );
	if ( is_array( $ids ) && isset( $ids[ $key ] ) ) {
		$tracked = get_post( (int) $ids[ $key ] );
		if ( $tracked && 'page' === $tracked->post_type && 'trash' !== $tracked->post_status ) {
			return $tracked;
		}
	}
	$page = get_page_by_path( $path );
	if ( $page ) {
		return $page;
	}
	$found = get_posts(
		array(
			'post_type'      => 'page',
			'name'           => sanitize_title( basename( $path ) ),
			'posts_per_page' => 1,
			'post_status'    => array( 'publish', 'draft', 'private' ),
		)
	);
	return $found ? $found[0] : null;
}

/**
 * Inline SVG icon set (stroke icons, 24px grid).
 *
 * @param string $name Icon name.
 * @param int    $size Pixel size.
 * @return string
 */
function cfm_icon( $name, $size = 20 ) {
	$paths = array(
		'whatsapp' => '<path fill="currentColor" stroke="none" d="M19.05 4.91A9.82 9.82 0 0 0 12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38a9.9 9.9 0 0 0 4.74 1.21h.01c5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.91-7.01Zm-7.01 15.24h-.01a8.2 8.2 0 0 1-4.19-1.15l-.3-.18-3.12.82.83-3.04-.2-.31a8.2 8.2 0 0 1-1.26-4.38c0-4.54 3.7-8.23 8.25-8.23 2.2 0 4.27.86 5.83 2.42a8.18 8.18 0 0 1 2.41 5.83c0 4.54-3.7 8.22-8.24 8.22Zm4.52-6.16c-.25-.12-1.47-.72-1.69-.81-.23-.08-.39-.12-.56.13-.16.25-.64.81-.79.97-.14.17-.29.19-.54.06-.25-.12-1.05-.39-1.99-1.23-.74-.66-1.23-1.47-1.38-1.72-.14-.25-.02-.38.11-.51.11-.11.25-.29.37-.43.13-.15.17-.25.25-.42.08-.17.04-.31-.02-.43-.06-.13-.56-1.34-.76-1.84-.2-.48-.41-.42-.56-.43h-.48c-.17 0-.43.06-.66.31-.22.25-.86.85-.86 2.07 0 1.22.89 2.4 1.01 2.56.12.17 1.75 2.67 4.23 3.74.59.26 1.05.41 1.41.52.59.19 1.13.16 1.56.1.48-.07 1.47-.6 1.67-1.18.21-.58.21-1.07.14-1.18-.06-.1-.22-.16-.47-.29Z"/>',
		'phone'    => '<path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1.9.4 1.8.7 2.7a2 2 0 0 1-.5 2.1L8 9.8a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.8.6 2.7.7a2 2 0 0 1 1.7 2Z"/>',
		'mail'     => '<rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-10 6L2 7"/>',
		'pin'      => '<path d="M20 10c0 6-8 12-8 12S4 16 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="3"/>',
		'clock'    => '<circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>',
		'arrow'    => '<path d="M5 12h14M13 5l7 7-7 7"/>',
		'check'    => '<path d="M20 6 9 17l-5-5"/>',
		'file'     => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z"/><path d="M14 2v6h6M9 15h6M9 11h2"/>',
		'download' => '<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3"/>',
		'menu'     => '<path d="M3 6h18M3 12h18M3 18h18"/>',
		'close'    => '<path d="M18 6 6 18M6 6l12 12"/>',
		'chevron'  => '<path d="m6 9 6 6 6-6"/>',
		'shield'   => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"/><path d="m9 12 2 2 4-4"/>',
		'layers'   => '<path d="m12 2 10 5-10 5L2 7Z"/><path d="m2 17 10 5 10-5M2 12l10 5 10-5"/>',
		'ruler'    => '<path d="M21.3 15.3 8.7 2.7a1 1 0 0 0-1.4 0L2.7 7.3a1 1 0 0 0 0 1.4l12.6 12.6a1 1 0 0 0 1.4 0l4.6-4.6a1 1 0 0 0 0-1.4Z"/><path d="m7.5 10.5 2-2M10.5 13.5l2-2M13.5 16.5l2-2"/>',
		'truck'    => '<path d="M10 17h4V5H2v12h3M14 8h4l4 4v5h-3"/><circle cx="7.5" cy="17.5" r="2.5"/><circle cx="17.5" cy="17.5" r="2.5"/>',
		'volume'   => '<path d="M11 5 6 9H2v6h4l5 4V5Z"/><path d="M22 9l-6 6M16 9l6 6"/>',
		'bolt'     => '<path d="M13 2 3 14h9l-1 8 10-12h-9l1-8Z"/>',
		'home'     => '<path d="m3 10 9-7 9 7v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2Z"/><path d="M9 22V12h6v10"/>',
		'external' => '<path d="M15 3h6v6M10 14 21 3M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>',
		'instagram' => '<rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="4"/><path d="M17.5 6.5h.01"/>',
		'facebook' => '<path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3Z"/>',
		'linkedin' => '<path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-4 0v7h-4v-7a6 6 0 0 1 6-6ZM2 9h4v12H2Z"/><circle cx="4" cy="4" r="2"/>',
		'youtube'  => '<path d="M22.5 6.4a2.8 2.8 0 0 0-2-2C18.8 4 12 4 12 4s-6.8 0-8.5.4a2.8 2.8 0 0 0-2 2A29 29 0 0 0 1 12a29 29 0 0 0 .5 5.6 2.8 2.8 0 0 0 2 2c1.7.4 8.5.4 8.5.4s6.8 0 8.5-.4a2.8 2.8 0 0 0 2-2A29 29 0 0 0 23 12a29 29 0 0 0-.5-5.6Z"/><path d="m9.8 15.5 5.7-3.5-5.7-3.5Z"/>',
		'tiktok'   => '<path d="M9 12a4 4 0 1 0 4 4V2a5 5 0 0 0 5 5"/>',
	);
	if ( ! isset( $paths[ $name ] ) ) {
		return '';
	}
	return sprintf(
		'<svg class="cfm-icon cfm-icon--%1$s" width="%2$d" height="%2$d" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">%3$s</svg>',
		esc_attr( $name ),
		(int) $size,
		$paths[ $name ]
	);
}

/**
 * True when a dedicated SEO plugin is active; our own meta output then steps aside.
 *
 * @return bool
 */
function cfm_seo_plugin_active() {
	return defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || defined( 'AIOSEO_VERSION' ) || defined( 'SEOPRESS_VERSION' ) || defined( 'THE_SEO_FRAMEWORK_VERSION' );
}

/**
 * Brand mark: uploaded custom logo, else the bundled SVG wordmark.
 *
 * @param string $variant "light" for dark backgrounds, "dark" for light ones.
 * @return string
 */
function cfm_brand_mark( $variant = 'light' ) {
	if ( has_custom_logo() ) {
		$logo_id = (int) get_theme_mod( 'custom_logo' );
		return wp_get_attachment_image(
			$logo_id,
			'full',
			false,
			array(
				'class'    => 'cfm-logo__img',
				'alt'      => cfm_opt( 'brand_name' ),
				'loading'  => 'eager',
				'decoding' => 'async',
			)
		);
	}
	return cfm_logo_svg( 'dark' === $variant ? '#17130e' : '#ffffff' );
}

/**
 * Default wordmark as inline SVG (inherits the page's web fonts). Replace it by
 * uploading your logo in Customize → Site Identity.
 *
 * @param string $text_color Wordmark colour.
 * @return string
 */
function cfm_logo_svg( $text_color = '#ffffff' ) {
	$name = cfm_opt( 'brand_name' );
	return sprintf(
		'<svg class="cfm-logo__svg" width="248" height="48" viewBox="0 0 248 48" role="img" aria-label="%1$s" xmlns="http://www.w3.org/2000/svg">' .
		'<rect x="1" y="1" width="46" height="46" rx="11" fill="#f2a93b"/>' .
		'<rect x="12" y="12" width="24" height="24" rx="3" fill="none" stroke="#17130e" stroke-width="3.5"/>' .
		'<rect x="17.5" y="17.5" width="13" height="13" rx="1.5" fill="#17130e"/>' .
		'<path d="M16 12V7M21 12V7M27 12V7M32 12V7M16 36v5M21 36v5M27 36v5M32 36v5" stroke="#17130e" stroke-width="2.4" stroke-linecap="round"/>' .
		'<text x="58" y="25" fill="%2$s" font-family="Fraunces, Georgia, serif" font-weight="600" font-size="21" letter-spacing="-.3">%3$s</text>' .
		'<text x="59" y="41" fill="#f2a93b" font-family="Instrument Sans, Segoe UI, sans-serif" font-weight="600" font-size="9" letter-spacing="2.6">%4$s</text>' .
		'</svg>',
		esc_attr( $name ),
		esc_attr( $text_color ),
		esc_html( $name ),
		esc_html( strtoupper( sprintf( 'by %s', cfm_opt( 'parent_name' ) ) ) )
	);
}
