<?php
/**
 * Smart links and shortcodes used inside editable page content.
 *
 * In any button, link or menu item you can use these addresses:
 *   #whatsapp → opens WhatsApp with a message naming the current page
 *   #call     → phone call
 *   #email    → email
 *   #map      → showroom on Google Maps
 *   #quote    → the quote form on this page (or the contact page)
 *   #parent   → Casa Vera Home website
 *
 * Shortcodes: [dce_info key="phone|email|address|hours|brand|parent_name|whatsapp"], [dce_year]
 *
 * @package dce-vibe
 */

defined( 'ABSPATH' ) || exit;

/**
 * Map of smart hash links to real URLs.
 *
 * @param bool $has_quote_form Whether the current content contains a quote form.
 * @return array
 */
function dce_smart_links( $has_quote_form = true ) {
	return array(
		'#whatsapp' => dce_wa_url( dce_page_wa_message() ),
		'#call'     => dce_tel_url(),
		'#email'    => 'mailto:' . antispambot( dce_opt( 'email' ) ),
		'#map'      => dce_opt( 'map_url' ),
		'#quote'    => $has_quote_form ? '#quote' : dce_quote_url(),
		'#parent'   => dce_opt( 'parent_url' ),
	);
}

/**
 * Replace smart links in HTML.
 *
 * @param string $html HTML.
 * @return string
 */
function dce_apply_smart_links( $html, $has_quote_form = null ) {
	if ( false === strpos( $html, 'href="#' ) ) {
		return $html;
	}
	if ( null === $has_quote_form ) {
		$has_quote_form = false !== strpos( $html, 'id="quote"' );
	}
	foreach ( dce_smart_links( $has_quote_form ) as $hash => $url ) {
		$attrs = '';
		if ( in_array( $hash, array( '#whatsapp', '#map', '#parent' ), true ) ) {
			$attrs = ' target="_blank" rel="noopener"';
		}
		$html = preg_replace_callback(
			'/<a([^>]*?)href="' . preg_quote( $hash, '/' ) . '"([^>]*)>/i',
			function ( $m ) use ( $url, $attrs ) {
				$rest = $m[1] . $m[2];
				if ( $attrs && false !== stripos( $rest, 'target=' ) ) {
					$attrs = '';
				}
				return '<a' . $m[1] . 'href="' . esc_url( $url ) . '"' . $m[2] . $attrs . '>';
			},
			$html
		);
	}
	return $html;
}

add_filter( 'the_content', 'dce_content_smart_links', 50 );
function dce_content_smart_links( $content ) {
	return dce_apply_smart_links( $content );
}

add_filter( 'widget_block_content', 'dce_apply_smart_links', 50 );

/** Smart links in menus too (e.g. a "WhatsApp" menu item with URL #whatsapp). */
add_filter( 'nav_menu_link_attributes', 'dce_menu_smart_links', 10, 1 );
function dce_menu_smart_links( $atts ) {
	if ( ! empty( $atts['href'] ) && '#' === $atts['href'][0] ) {
		$map = dce_smart_links( false );
		if ( isset( $map[ $atts['href'] ] ) ) {
			if ( in_array( $atts['href'], array( '#whatsapp', '#map', '#parent' ), true ) ) {
				$atts['target'] = '_blank';
				$atts['rel']    = 'noopener';
			}
			$atts['href'] = $map[ $atts['href'] ];
		}
	}
	return $atts;
}

add_shortcode( 'dce_info', 'dce_info_shortcode' );
function dce_info_shortcode( $atts ) {
	$atts = shortcode_atts( array( 'key' => 'phone' ), $atts, 'dce_info' );
	$key  = sanitize_key( $atts['key'] );
	$val  = dce_opt( $key );
	if ( 'email' === $key ) {
		return esc_html( antispambot( $val ) );
	}
	return esc_html( $val );
}

add_shortcode( 'dce_year', fn() => gmdate( 'Y' ) );

/** Resolve a header CTA url that may be a smart link. */
function dce_resolve_url( $url ) {
	if ( $url && '#' === $url[0] ) {
		$map = dce_smart_links( false );
		if ( isset( $map[ $url ] ) ) {
			return $map[ $url ];
		}
	}
	return $url;
}
