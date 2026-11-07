<?php
/**
 * Small helpers used across templates.
 *
 * @package Tasdir
 */

defined( 'ABSPATH' ) || exit;

/**
 * Default values for every Customizer option.
 *
 * @return array
 */
function tasdir_defaults() {
	return array(
		// Company / contact.
		'company_name'     => 'Tasdir Import Export Private Limited',
		'whatsapp_number'  => '',
		'whatsapp_message' => 'Hello Tasdir, I would like a quotation for fresh fruit export.',
		'phone'            => '',
		'email'            => '',
		'address'          => 'India',
		'hours'            => 'Mon – Sat, 9:00 AM – 7:00 PM IST',
		'facebook_url'     => '',
		'instagram_url'    => '',
		'linkedin_url'     => '',
		'youtube_url'      => '',
		'lead_email'       => '',
		// Hero.
		'hero_eyebrow'     => 'Fresh fruit exporters from India',
		'hero_title'       => 'Farm-fresh Indian fruit, <em>packed for the world.</em>',
		'hero_text'        => 'Premium bananas and table grapes, sourced direct from Indian farms, graded by hand and shipped in export-grade cartons with full documentation.',
		'hero_image'       => 0,
		// Sections.
		'markets'          => "UAE\nSaudi Arabia\nQatar\nOman\nKuwait\nBahrain\nEurope\nSoutheast Asia",
		'show_wa_float'    => 1,
		'show_mobile_bar'  => 1,
		'show_topbar'      => 1,
		'footer_text'      => 'Tasdir Import Export Private Limited exports premium fresh fruit from India — farm sourced, hand graded and packed in branded export cartons.',
		// Tracking.
		'ga4_id'           => '',
		'gtm_id'           => '',
		'fb_pixel_id'      => '',
		'google_verify'    => '',
		'fb_domain_verify' => '',
		'bing_verify'      => '',
		'track_admins'     => 0,
		'head_code'        => '',
		// SEO.
		'seo_home_title'   => 'Tasdir Import Export | Fresh Banana & Grapes Exporter from India',
		'seo_home_desc'    => 'Tasdir Import Export Pvt. Ltd. — exporter of premium fresh bananas and table grapes from India. Farm-direct sourcing, export-grade packing, worldwide shipping. Get a quote on WhatsApp.',
		'seo_og_image'     => 0,
	);
}

/**
 * Get a theme option with its default.
 *
 * @param string $key Option key.
 * @return mixed
 */
function tasdir_opt( $key ) {
	$defaults = tasdir_defaults();
	return get_theme_mod( $key, isset( $defaults[ $key ] ) ? $defaults[ $key ] : '' );
}

/**
 * WhatsApp number as digits only (international format, no +).
 *
 * @return string
 */
function tasdir_wa_number() {
	return preg_replace( '/\D+/', '', (string) tasdir_opt( 'whatsapp_number' ) );
}

/**
 * Build a wa.me link. Falls back to the on-page enquiry form if no number is configured.
 *
 * @param string $message Pre-filled message.
 * @return string
 */
function tasdir_wa_url( $message = '' ) {
	$number = tasdir_wa_number();
	if ( ! $number ) {
		return home_url( '/#enquiry' );
	}
	if ( '' === $message ) {
		$message = (string) tasdir_opt( 'whatsapp_message' );
	}
	return 'https://wa.me/' . $number . ( $message ? '?text=' . rawurlencode( $message ) : '' );
}

/**
 * Theme image URL by slug and width.
 *
 * @param string $slug  Image slug in assets/img.
 * @param int    $width 768 or 1376.
 * @return string
 */
function tasdir_asset_img( $slug, $width = 1376 ) {
	return TASDIR_URI . '/assets/img/' . $slug . '-' . (int) $width . '.webp';
}

/**
 * Responsive <img> for a bundled theme image.
 *
 * @param string $slug  Image slug.
 * @param string $alt   Alt text.
 * @param string $sizes Sizes attribute.
 * @param array  $attrs Extra attributes.
 * @return string
 */
function tasdir_theme_img( $slug, $alt, $sizes = '100vw', $attrs = array() ) {
	$attrs = wp_parse_args(
		$attrs,
		array(
			'loading'  => 'lazy',
			'decoding' => 'async',
			'width'    => 1376,
			'height'   => 768,
		)
	);
	$html  = sprintf(
		'<img src="%1$s" srcset="%2$s 768w, %1$s 1376w" sizes="%3$s" alt="%4$s"',
		esc_url( tasdir_asset_img( $slug, 1376 ) ),
		esc_url( tasdir_asset_img( $slug, 768 ) ),
		esc_attr( $sizes ),
		esc_attr( $alt )
	);
	foreach ( $attrs as $k => $v ) {
		$html .= ' ' . esc_attr( $k ) . '="' . esc_attr( $v ) . '"';
	}
	return $html . '>';
}

/**
 * Hero image URL (Customizer image or bundled plantation photo).
 *
 * @param int $width Width hint.
 * @return string
 */
function tasdir_hero_image_url( $width = 1376 ) {
	$id = (int) tasdir_opt( 'hero_image' );
	if ( $id ) {
		$url = wp_get_attachment_image_url( $id, $width <= 768 ? 'tasdir-card' : 'tasdir-wide' );
		if ( $url ) {
			return $url;
		}
	}
	return tasdir_asset_img( 'banana-plantation', $width <= 768 ? 768 : 1376 );
}

/**
 * Markets list from the Customizer textarea.
 *
 * @return string[]
 */
function tasdir_markets() {
	return array_filter( array_map( 'trim', explode( "\n", (string) tasdir_opt( 'markets' ) ) ) );
}

/**
 * Site logo (custom logo or text wordmark).
 *
 * @param bool $link Wrap in home link.
 */
function tasdir_logo( $link = true ) {
	if ( has_custom_logo() && $link ) {
		the_custom_logo();
		return;
	}
	$mark = '<span class="brand-mark"><span class="brand-mark__name">T<span>A</span>SDIR</span><span class="brand-mark__sub">Import Export Pvt. Ltd.</span></span>';
	if ( $link ) {
		printf( '<a class="brand" href="%1$s" rel="home" aria-label="%2$s">%3$s</a>', esc_url( home_url( '/' ) ), esc_attr( get_bloginfo( 'name' ) ), $mark ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	} else {
		echo $mark; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}

/**
 * Fallback primary menu when none is assigned.
 */
function tasdir_menu_fallback() {
	$items = array(
		home_url( '/' )                             => __( 'Home', 'tasdir' ),
		get_post_type_archive_link( 'tasdir_product' ) => __( 'Products', 'tasdir' ),
	);
	foreach ( array( 'about-us' => __( 'About Us', 'tasdir' ), 'export-process' => __( 'Export Process', 'tasdir' ), 'contact' => __( 'Contact', 'tasdir' ) ) as $slug => $label ) {
		$page = get_page_by_path( $slug );
		$items[ $page ? get_permalink( $page ) : home_url( '/#enquiry' ) ] = $label;
	}
	echo '<ul class="menu">';
	foreach ( $items as $url => $label ) {
		printf( '<li><a href="%s">%s</a></li>', esc_url( $url ), esc_html( $label ) );
	}
	echo '</ul>';
}

/**
 * Allowed HTML for the hero title (just emphasis).
 *
 * @return array
 */
function tasdir_kses_title() {
	return array(
		'em'     => array(),
		'strong' => array(),
		'br'     => array(),
	);
}

/**
 * Simple breadcrumbs (also mirrored in JSON-LD by seo.php).
 *
 * @return array List of [label, url].
 */
function tasdir_breadcrumb_items() {
	$items = array( array( __( 'Home', 'tasdir' ), home_url( '/' ) ) );
	if ( is_singular( 'tasdir_product' ) ) {
		$items[] = array( __( 'Products', 'tasdir' ), get_post_type_archive_link( 'tasdir_product' ) );
		$items[] = array( get_the_title(), get_permalink() );
	} elseif ( is_post_type_archive( 'tasdir_product' ) ) {
		$items[] = array( __( 'Products', 'tasdir' ), get_post_type_archive_link( 'tasdir_product' ) );
	} elseif ( is_singular() ) {
		$items[] = array( get_the_title(), get_permalink() );
	} elseif ( is_archive() ) {
		$items[] = array( wp_strip_all_tags( get_the_archive_title() ), '' );
	} elseif ( is_search() ) {
		$items[] = array( __( 'Search', 'tasdir' ), '' );
	} elseif ( is_home() && get_option( 'page_for_posts' ) ) {
		$items[] = array( get_the_title( get_option( 'page_for_posts' ) ), get_permalink( get_option( 'page_for_posts' ) ) );
	}
	return $items;
}

/**
 * Render breadcrumbs.
 */
function tasdir_breadcrumbs() {
	$items = tasdir_breadcrumb_items();
	$last  = count( $items ) - 1;
	echo '<nav class="breadcrumbs" aria-label="' . esc_attr__( 'Breadcrumb', 'tasdir' ) . '">';
	foreach ( $items as $i => $item ) {
		if ( $i === $last || ! $item[1] ) {
			echo '<span aria-current="page">' . esc_html( $item[0] ) . '</span>';
		} else {
			echo '<a href="' . esc_url( $item[1] ) . '">' . esc_html( $item[0] ) . '</a> / ';
		}
	}
	echo '</nav>';
}
