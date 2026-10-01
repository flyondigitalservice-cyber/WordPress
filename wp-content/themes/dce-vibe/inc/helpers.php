<?php
/**
 * Helper functions: business details, WhatsApp/phone links, icons.
 *
 * @package dce-vibe
 */

defined( 'ABSPATH' ) || exit;

/**
 * Default business details. Every value can be changed in Appearance → Customize → DCE Business Details.
 */
function dce_defaults() {
	return array(
		'brand'           => 'Dubai Curtain Experts',
		'brand_sub'       => 'Part of Casa Vera Home',
		'legal_name'      => 'Mukhtar Curtain LLC',
		'phone'           => '+971 50 859 9803',
		'whatsapp'        => '971508599803',
		'wa_message'      => 'Hello Dubai Curtain Experts, I would like to book a free home visit and measurement.',
		'email'           => 'info@dubaicurtainexperts.ae',
		'lead_email'      => 'info@dubaicurtainexperts.ae',
		'address'         => 'Empire Plaza Shopping Center, Shop 49, Naif Road, Deira, Dubai, UAE',
		'map_url'         => 'https://maps.google.com/?q=Empire+Plaza+Naif+Road+Deira+Dubai',
		'hours'           => 'Monday – Saturday, 8:00 AM – 5:30 PM (Sunday closed)',
		'parent_name'     => 'Casa Vera Home',
		'parent_url'      => 'https://casaverahome.ae/',
		'parent_text'     => 'Dubai Curtain Experts is the curtains and blinds studio of Casa Vera Home. For furniture, décor and complete interiors, visit our parent company.',
		'logo_with_text'  => 1,
		'topbar_show'     => 1,
		'topbar_text'     => 'Free home visit & measurement across Dubai and the UAE',
		'header_cta_text' => 'Book a free home visit',
		'header_cta_url'  => '#quote',
		'footer_about'    => 'Made-to-measure curtains and blinds for homes, offices and hospitality projects across Dubai and the UAE. Measured, stitched and installed by our own team.',
		'footer_copy'     => '© {year} Dubai Curtain Experts — Casa Vera Home (Mukhtar Curtain LLC). All rights reserved.',
		'float_wa'        => 1,
		'mobile_bar'      => 1,
		'instagram'       => '',
		'facebook'        => '',
		'tiktok'          => '',
		'youtube'         => '',
		'linkedin'        => '',
		'geo_lat'         => '25.2711',
		'geo_lng'         => '55.3075',
		'price_range'     => 'AED',
	);
}

/**
 * Read a business option (Customizer theme mod) with its default.
 *
 * @param string $key Option key without prefix.
 * @return string
 */
function dce_opt( $key ) {
	$defaults = dce_defaults();
	$default  = isset( $defaults[ $key ] ) ? $defaults[ $key ] : '';
	$value    = get_theme_mod( 'dce_' . $key, $default );
	if ( is_string( $value ) ) {
		$value = str_replace( '{year}', gmdate( 'Y' ), $value );
	}
	return $value;
}

/** WhatsApp number as digits only. */
function dce_wa_number() {
	return preg_replace( '/\D+/', '', (string) dce_opt( 'whatsapp' ) );
}

/**
 * Build a wa.me link.
 *
 * @param string $message Optional prefilled message.
 * @return string
 */
function dce_wa_url( $message = '' ) {
	if ( '' === $message ) {
		$message = dce_opt( 'wa_message' );
	}
	return 'https://wa.me/' . dce_wa_number() . '?text=' . rawurlencode( $message );
}

/** Message prefilled for the current page, so every WhatsApp lead says where it came from. */
function dce_page_wa_message( $post = null ) {
	$post = get_post( $post );
	if ( ! $post || is_front_page() ) {
		return dce_opt( 'wa_message' );
	}
	$title = wp_strip_all_tags( get_the_title( $post ) );
	/* translators: 1: brand, 2: page title, 3: url */
	return sprintf( 'Hello %1$s, I am interested in %2$s. Please share details and book a free home visit. (%3$s)', dce_opt( 'brand' ), $title, get_permalink( $post ) );
}

/** tel: link. */
function dce_tel_url() {
	return 'tel:+' . preg_replace( '/\D+/', '', (string) dce_opt( 'phone' ) );
}

/** URL used by "Book a free visit" buttons: the quote form on the page, or the contact page. */
function dce_quote_url() {
	$contact = get_page_by_path( 'contact-us' );
	return $contact ? get_permalink( $contact ) . '#quote' : home_url( '/#quote' );
}

/** Products and areas offered in the lead form. */
function dce_lead_services() {
	return apply_filters(
		'dce_lead_services',
		array(
			'Wave curtains', 'Pinch pleat curtains', 'Eyelet curtains', 'American style curtains', 'Roman curtains',
			'Blackout curtains', 'Sheer curtains', 'Motorized curtains', 'Beaded curtains', 'Cinema curtains',
			'Kids curtains', 'Hospital curtains', 'Blackout roller blinds', 'Sunscreen roller blinds', 'Zebra blinds',
			'Roman blinds', 'Vertical blinds', 'Wooden blinds', 'Aluminium venetian blinds', 'Bamboo blinds',
			'Printed blinds', 'Logo sunscreen blinds', 'Curtains & blinds for a whole home', 'Office / commercial project',
		)
	);
}

function dce_lead_areas() {
	return apply_filters(
		'dce_lead_areas',
		array(
			'Dubai Marina / JBR', 'Downtown Dubai / Business Bay', 'Palm Jumeirah', 'Jumeirah / Umm Suqeim',
			'Dubai Hills / Arabian Ranches', 'JVC / JLT / Dubai South', 'Deira / Bur Dubai', 'Mirdif / Festival City',
			'Al Barsha / Al Sufouh', 'Dubai Silicon Oasis', 'DAMAC Hills / Motor City', 'Emirates Hills / The Springs',
			'Other Dubai area', 'Abu Dhabi', 'Sharjah', 'Ajman', 'Ras Al Khaimah', 'Al Ain', 'Fujairah', 'Umm Al Quwain',
		)
	);
}

/**
 * Inline SVG icons.
 *
 * @param string $name Icon name.
 * @param int    $size Pixel size.
 * @return string
 */
function dce_icon( $name, $size = 18 ) {
	$paths = array(
		'whatsapp'  => '<path fill="currentColor" stroke="none" d="M12 2a10 10 0 0 0-8.6 15.1L2 22l5-1.3A10 10 0 1 0 12 2Zm5.3 14.1c-.2.6-1.3 1.2-1.8 1.2-.5.1-1 .2-3.3-.7-2.8-1.1-4.6-4-4.7-4.2-.1-.2-1.1-1.5-1.1-2.9s.7-2 1-2.3c.3-.3.6-.3.8-.3h.6c.2 0 .4 0 .6.5l.9 2.1c.1.2.1.4 0 .5l-.3.5-.4.5c-.1.1-.3.3-.1.6.2.3.8 1.3 1.7 2.1 1.2 1 2.1 1.4 2.4 1.5.3.1.5.1.6-.1l.9-1c.2-.3.4-.2.6-.1l2 1c.3.1.5.2.5.3.1.2.1.7-.1 1.4Z"/>',
		'phone'     => '<path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1.9.4 1.8.7 2.7a2 2 0 0 1-.5 2.1L8 9.8a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.8.6 2.7.7a2 2 0 0 1 1.7 2Z"/>',
		'mail'      => '<rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-10 6L2 7"/>',
		'pin'       => '<path d="M20 10c0 6-8 12-8 12S4 16 4 10a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/>',
		'clock'     => '<circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>',
		'arrow'     => '<path d="M7 17 17 7"/><path d="M7 7h10v10"/>',
		'menu'      => '<path d="M4 6h16M4 12h16M4 18h16"/>',
		'close'     => '<path d="M18 6 6 18M6 6l12 12"/>',
		'calendar'  => '<rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/>',
		'instagram' => '<rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r=".5" fill="currentColor"/>',
		'facebook'  => '<path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/>',
		'tiktok'    => '<path d="M16 3a5 5 0 0 0 5 5v3a8 8 0 0 1-5-1.7V16a6 6 0 1 1-6-6v3a3 3 0 1 0 3 3V3z"/>',
		'youtube'   => '<path d="M22.5 6.4a2.8 2.8 0 0 0-2-2C18.8 4 12 4 12 4s-6.8 0-8.5.4a2.8 2.8 0 0 0-2 2A29 29 0 0 0 1 12a29 29 0 0 0 .5 5.6 2.8 2.8 0 0 0 2 2C5.2 20 12 20 12 20s6.8 0 8.5-.4a2.8 2.8 0 0 0 2-2A29 29 0 0 0 23 12a29 29 0 0 0-.5-5.6Z"/><path d="m10 15 5-3-5-3z"/>',
		'linkedin'  => '<path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-4 0v7h-4v-7a6 6 0 0 1 6-6zM2 9h4v12H2z"/><circle cx="4" cy="4" r="2"/>',
	);
	if ( ! isset( $paths[ $name ] ) ) {
		return '';
	}
	return sprintf(
		'<svg xmlns="http://www.w3.org/2000/svg" width="%1$d" height="%1$d" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="dce-icon dce-icon-%2$s" aria-hidden="true" focusable="false">%3$s</svg>',
		(int) $size,
		esc_attr( $name ),
		$paths[ $name ]
	);
}

/** The brand logo: Customizer logo if set, otherwise the bundled SVG mark. */
function dce_logo_html( $light = false ) {
	if ( has_custom_logo() ) {
		$logo_id = get_theme_mod( 'custom_logo' );
		return wp_get_attachment_image( $logo_id, 'full', false, array( 'alt' => esc_attr( dce_opt( 'brand' ) ), 'loading' => $light ? 'lazy' : 'eager' ) );
	}
	return dce_logo_svg( $light );
}

function dce_logo_svg( $light = false ) {
	$c = $light ? '#d4b88a' : '#8c6d3f';
	$d = $light ? '#ffffff' : '#1d1b18';
	return '<svg viewBox="0 0 64 64" width="46" height="46" role="img" aria-label="' . esc_attr( dce_opt( 'brand' ) ) . '" xmlns="http://www.w3.org/2000/svg"><rect x="6" y="8" width="52" height="3" rx="1.5" fill="' . $d . '"/><path d="M10 12c0 16 2 30 6 44h8c-3-12-4-28-4-44z" fill="' . $c . '"/><path d="M54 12c0 16-2 30-6 44h-8c3-12 4-28 4-44z" fill="' . $c . '"/><path d="M24 12c0 14 1 30 3 44h10c2-14 3-30 3-44z" fill="' . $d . '" opacity=".12"/><circle cx="32" cy="34" r="2.2" fill="' . $c . '"/></svg>';
}

/** Social links that have a URL set. */
function dce_social_links() {
	$out = array();
	foreach ( array( 'instagram', 'facebook', 'tiktok', 'youtube', 'linkedin' ) as $net ) {
		$url = dce_opt( $net );
		if ( $url ) {
			$out[ $net ] = $url;
		}
	}
	return $out;
}

/** Breadcrumb trail for pages/posts. */
function dce_breadcrumbs() {
	if ( is_front_page() ) {
		return;
	}
	$items = array( array( home_url( '/' ), __( 'Home', 'dce-vibe' ) ) );
	if ( is_singular( 'page' ) ) {
		foreach ( array_reverse( get_post_ancestors( get_the_ID() ) ) as $anc ) {
			$items[] = array( get_permalink( $anc ), get_the_title( $anc ) );
		}
		$items[] = array( '', get_the_title() );
	} elseif ( is_singular( 'post' ) ) {
		$blog = (int) get_option( 'page_for_posts' );
		if ( $blog ) {
			$items[] = array( get_permalink( $blog ), get_the_title( $blog ) );
		}
		$items[] = array( '', get_the_title() );
	} elseif ( is_home() ) {
		$items[] = array( '', single_post_title( '', false ) ? single_post_title( '', false ) : __( 'Blog', 'dce-vibe' ) );
	} elseif ( is_archive() ) {
		$items[] = array( '', wp_strip_all_tags( get_the_archive_title() ) );
	} elseif ( is_search() ) {
		$items[] = array( '', __( 'Search', 'dce-vibe' ) );
	} else {
		return;
	}
	echo '<div class="dce-container dce-crumbs-wrap"><nav class="dce-crumbs" aria-label="' . esc_attr__( 'Breadcrumb', 'dce-vibe' ) . '"><ol>';
	foreach ( $items as $item ) {
		if ( $item[0] ) {
			echo '<li><a href="' . esc_url( $item[0] ) . '">' . esc_html( wp_strip_all_tags( $item[1] ) ) . '</a></li>';
		} else {
			echo '<li aria-current="page">' . esc_html( wp_strip_all_tags( $item[1] ) ) . '</li>';
		}
	}
	echo '</ol></nav></div>';
}
