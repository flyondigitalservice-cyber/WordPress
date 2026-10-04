<?php
/**
 * Reusable template helpers.
 *
 * @package FlavorKit
 */

defined( 'ABSPATH' ) || exit;

/**
 * Turn *word* into a highlighted span and escape everything else.
 *
 * @param string $text Raw text from the Customizer.
 * @return string Safe HTML.
 */
function flavorkit_highlight( $text ) {
	$text = esc_html( $text );
	return preg_replace( '/\*(.+?)\*/', '<span class="fk-hl">$1</span>', $text );
}

/**
 * Parse "a | b | c" lines from a textarea setting into arrays.
 *
 * @param string $raw       Raw textarea value.
 * @param int    $min_parts Minimum number of parts (padded with '').
 * @return array<int, string[]>
 */
function flavorkit_lines( $raw, $min_parts = 2 ) {
	$out = array();
	foreach ( preg_split( '/\r\n|\r|\n/', (string) $raw ) as $line ) {
		$line = trim( $line );
		if ( '' === $line ) {
			continue;
		}
		$parts = array_map( 'trim', explode( '|', $line ) );
		$out[] = array_pad( $parts, $min_parts, '' );
	}
	return $out;
}

/**
 * Split a single-line "a | b | c" value.
 *
 * @param string $raw Raw value.
 * @return string[]
 */
function flavorkit_pipe_list( $raw ) {
	return array_values( array_filter( array_map( 'trim', explode( '|', (string) $raw ) ), 'strlen' ) );
}

/**
 * Shop URL with graceful fallback when WooCommerce is inactive.
 *
 * @return string
 */
function flavorkit_shop_url() {
	if ( function_exists( 'wc_get_page_permalink' ) ) {
		return wc_get_page_permalink( 'shop' );
	}
	return home_url( '/' );
}

/**
 * Resolve a Customizer link, empty means shop.
 *
 * @param string $url URL or anchor.
 * @return string
 */
function flavorkit_link( $url ) {
	$url = trim( (string) $url );
	if ( '' === $url ) {
		return flavorkit_shop_url();
	}
	if ( 0 === strpos( $url, '#' ) ) {
		return is_front_page() ? $url : home_url( '/' . $url );
	}
	return $url;
}

/**
 * Inline SVG icon set (stroke icons, 24×24).
 *
 * @param string $name  Icon name.
 * @param int    $size  Pixel size.
 * @return string SVG markup.
 */
function flavorkit_icon( $name, $size = 24 ) {
	$paths = array(
		'cart'      => '<path d="M6 7h12l-1 13H7L6 7Z"/><path d="M9 7a3 3 0 0 1 6 0"/>',
		'search'    => '<circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/>',
		'user'      => '<circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/>',
		'menu'      => '<path d="M4 7h16M4 12h16M4 17h16"/>',
		'close'     => '<path d="M6 6l12 12M18 6 6 18"/>',
		'arrow'     => '<path d="M5 12h14M13 6l6 6-6 6"/>',
		'arrow-left' => '<path d="M19 12H5M11 6l-6 6 6 6"/>',
		'plus'      => '<path d="M12 5v14M5 12h14"/>',
		'minus'     => '<path d="M5 12h14"/>',
		'star'      => '<path d="m12 3 2.7 5.6 6.1.9-4.4 4.3 1 6.1L12 17l-5.4 2.9 1-6.1L3.2 9.5l6.1-.9L12 3Z" fill="currentColor"/>',
		'leaf'      => '<path d="M5 19c0-8 6-14 15-14 0 9-6 15-14 15"/><path d="M5 19 13 11"/>',
		'heart'     => '<path d="M12 20s-7-4.4-9-9a4.8 4.8 0 0 1 9-3 4.8 4.8 0 0 1 9 3c-2 4.6-9 9-9 9Z"/>',
		'truck'     => '<path d="M3 6h11v10H3zM14 10h4l3 3v3h-7"/><circle cx="7" cy="18" r="2"/><circle cx="17" cy="18" r="2"/>',
		'shield'    => '<path d="M12 3 4 6v6c0 5 3.5 8 8 9 4.5-1 8-4 8-9V6l-8-3Z"/><path d="m9 12 2 2 4-4"/>',
		'sparkle'   => '<path d="M12 3v4M12 17v4M3 12h4M17 12h4M6 6l2.5 2.5M15.5 15.5 18 18M6 18l2.5-2.5M15.5 8.5 18 6"/>',
		'flame'     => '<path d="M12 21c4 0 7-2.7 7-6.5 0-3.5-2.5-5.5-4-8.5-1 2-2 3-3.5 3.5C12 7 11 5 9 3c0 4-4 6.5-4 11.5C5 18.3 8 21 12 21Z"/>',
		'drop'      => '<path d="M12 3s7 7.5 7 12a7 7 0 0 1-14 0c0-4.5 7-12 7-12Z"/>',
		'gift'      => '<path d="M4 10h16v10H4zM3 7h18v3H3zM12 7v13"/><path d="M12 7c-2-3-6-3-6 0M12 7c2-3 6-3 6 0"/>',
		'check'     => '<path d="m5 12 5 5 9-10"/>',
		'instagram' => '<rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor"/>',
		'facebook'  => '<path d="M14 8h3V4h-3a4 4 0 0 0-4 4v3H7v4h3v6h4v-6h3l1-4h-4V8Z"/>',
		'youtube'   => '<rect x="2" y="5" width="20" height="14" rx="4"/><path d="m10 9 5 3-5 3V9Z" fill="currentColor"/>',
		'x'         => '<path d="M4 4l16 16M20 4 4 20"/>',
		'linkedin'  => '<rect x="3" y="3" width="18" height="18" rx="3"/><path d="M8 10v7M8 7v.01M12 17v-4a2 2 0 0 1 4 0v4M12 10v7"/>',
		'whatsapp'  => '<path d="M4 20l1.3-4A8 8 0 1 1 8 18.7L4 20Z"/><path d="M9 9c0 3 3 6 6 6l1-1.5-2-1-1 .8c-1-.4-2-1.4-2.4-2.4l.8-1-1-2L9 9Z" fill="currentColor"/>',
	);

	if ( ! isset( $paths[ $name ] ) ) {
		return '';
	}

	return sprintf(
		'<svg class="fk-icon fk-icon-%1$s" width="%2$d" height="%2$d" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">%3$s</svg>',
		esc_attr( $name ),
		absint( $size ),
		$paths[ $name ]
	);
}

/**
 * Star rating markup.
 *
 * @param float $rating Rating out of 5.
 * @return string
 */
function flavorkit_stars( $rating ) {
	$rating = max( 0, min( 5, (float) $rating ) );
	$html   = '<span class="fk-stars" role="img" aria-label="' . esc_attr( sprintf( /* translators: %s rating */ __( 'Rated %s out of 5', 'flavorkit' ), $rating ) ) . '">';
	for ( $i = 1; $i <= 5; $i++ ) {
		$html .= '<span class="fk-star' . ( $i <= round( $rating ) ? ' is-on' : '' ) . '">' . flavorkit_icon( 'star', 16 ) . '</span>';
	}
	return $html . '</span>';
}

/**
 * Section heading block (eyebrow + title).
 *
 * @param string $eyebrow Small label.
 * @param string $title   Title (supports *highlight*).
 * @param string $text    Optional intro.
 * @param string $align   center|left.
 */
function flavorkit_section_heading( $eyebrow, $title, $text = '', $align = 'center' ) {
	if ( ! $eyebrow && ! $title ) {
		return;
	}
	echo '<header class="fk-section-head fk-align-' . esc_attr( $align ) . ' fk-reveal">';
	if ( $eyebrow ) {
		echo '<p class="fk-eyebrow">' . esc_html( $eyebrow ) . '</p>';
	}
	if ( $title ) {
		echo '<h2 class="fk-section-title">' . flavorkit_highlight( $title ) . '</h2>'; // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in helper.
	}
	if ( $text ) {
		echo '<p class="fk-section-text">' . esc_html( $text ) . '</p>';
	}
	echo '</header>';
}

/**
 * Wavy section divider.
 *
 * @param string $position top|bottom.
 * @param string $color    CSS colour value for the wave.
 */
function flavorkit_wave( $position = 'bottom', $color = 'var(--fk-cream)' ) {
	printf(
		'<div class="fk-wave fk-wave-%1$s" aria-hidden="true"><svg viewBox="0 0 1440 80" preserveAspectRatio="none"><path style="fill:%2$s" d="M0,40 C120,80 240,0 360,40 C480,80 600,0 720,40 C840,80 960,0 1080,40 C1200,80 1320,0 1440,40 L1440,80 L0,80 Z"/></svg></div>',
		esc_attr( $position ),
		esc_attr( $color )
	);
}

/**
 * Social links list.
 */
function flavorkit_social_links() {
	$networks = array( 'instagram', 'facebook', 'youtube', 'x', 'linkedin' );
	$html     = '';
	foreach ( $networks as $network ) {
		$url = flavorkit_mod( 'social_' . $network );
		if ( $url ) {
			$html .= sprintf(
				'<li><a href="%1$s" target="_blank" rel="noopener" aria-label="%2$s">%3$s</a></li>',
				esc_url( $url ),
				esc_attr( ucfirst( $network ) ),
				flavorkit_icon( $network, 20 )
			);
		}
	}
	if ( $html ) {
		echo '<ul class="fk-social">' . $html . '</ul>'; // phpcs:ignore WordPress.Security.EscapeOutput
	}
}

/**
 * WhatsApp chat URL.
 *
 * @return string
 */
function flavorkit_whatsapp_url() {
	$number = preg_replace( '/\D+/', '', (string) flavorkit_mod( 'whatsapp_number' ) );
	if ( ! $number ) {
		return '';
	}
	return 'https://wa.me/' . $number . '?text=' . rawurlencode( flavorkit_mod( 'whatsapp_message' ) );
}

/**
 * Pick black or white text for a background colour.
 *
 * @param string $hex Hex colour.
 * @param string $dark Colour to use on light backgrounds.
 * @return string
 */
function flavorkit_contrast( $hex, $dark = '#111111' ) {
	$hex = ltrim( (string) $hex, '#' );
	if ( 3 === strlen( $hex ) ) {
		$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
	}
	if ( 6 !== strlen( $hex ) ) {
		return '#ffffff';
	}
	$r = hexdec( substr( $hex, 0, 2 ) );
	$g = hexdec( substr( $hex, 2, 2 ) );
	$b = hexdec( substr( $hex, 4, 2 ) );
	// Perceived luminance (YIQ).
	$yiq = ( ( $r * 299 ) + ( $g * 587 ) + ( $b * 114 ) ) / 1000;
	return $yiq >= 150 ? $dark : '#ffffff';
}

/**
 * Image from a Customizer media setting (attachment ID or URL).
 *
 * @param mixed  $value Attachment ID or URL.
 * @param string $size  Image size.
 * @param string $class Class.
 * @param string $alt   Alt text fallback.
 * @return string
 */
function flavorkit_image( $value, $size = 'large', $class = '', $alt = '' ) {
	if ( ! $value ) {
		return '';
	}
	if ( is_numeric( $value ) ) {
		return wp_get_attachment_image( (int) $value, $size, false, array( 'class' => $class, 'loading' => 'lazy' ) );
	}
	return sprintf( '<img src="%1$s" class="%2$s" alt="%3$s" loading="lazy" />', esc_url( $value ), esc_attr( $class ), esc_attr( $alt ) );
}

/**
 * Post meta line.
 */
function flavorkit_posted_on() {
	printf(
		'<p class="fk-meta"><time datetime="%1$s">%2$s</time> · %3$s</p>',
		esc_attr( get_the_date( DATE_W3C ) ),
		esc_html( get_the_date() ),
		esc_html( get_the_author() )
	);
}
