<?php
/**
 * Template helpers and homepage data.
 *
 * @package luxurywatchs
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WhatsApp number, digits only.
 *
 * @return string
 */
function lw_whatsapp_number() {
	return preg_replace( '/\D/', '', get_theme_mod( 'lw_whatsapp', '919999999999' ) );
}

/**
 * WhatsApp click-to-chat URL.
 *
 * @param string $message Prefilled message.
 * @return string
 */
function lw_whatsapp_url( $message = '' ) {
	if ( '' === $message ) {
		$message = __( 'Hi! I want to know more about your watches.', 'luxurywatchs' );
	}
	return 'https://wa.me/' . lw_whatsapp_number() . '?text=' . rawurlencode( $message );
}

/**
 * Shop page URL with WooCommerce fallback.
 *
 * @return string
 */
function lw_shop_url() {
	return function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );
}

/**
 * URL for a product category slug, falling back to the shop page.
 *
 * @param string $slug Category slug.
 * @return string
 */
function lw_cat_url( $slug ) {
	if ( taxonomy_exists( 'product_cat' ) ) {
		$term = get_term_by( 'slug', $slug, 'product_cat' );
		if ( $term && ! is_wp_error( $term ) ) {
			return get_term_link( $term );
		}
	}
	return add_query_arg( 'product_cat', $slug, lw_shop_url() );
}

/**
 * Thumbnail URL of a product category, or empty string.
 *
 * @param string $slug Category slug.
 * @return string
 */
function lw_cat_image( $slug ) {
	if ( ! taxonomy_exists( 'product_cat' ) ) {
		return '';
	}
	$term = get_term_by( 'slug', $slug, 'product_cat' );
	if ( ! $term || is_wp_error( $term ) ) {
		return '';
	}
	$thumb_id = get_term_meta( $term->term_id, 'thumbnail_id', true );
	return $thumb_id ? (string) wp_get_attachment_image_url( $thumb_id, 'lw-card' ) : '';
}

/**
 * Style categories shown on the homepage.
 *
 * @return array
 */
function lw_style_categories() {
	return array(
		array( 'slug' => 'chronograph', 'name' => __( 'Chronograph', 'luxurywatchs' ), 'tag' => __( 'Racing-bred precision', 'luxurywatchs' ) ),
		array( 'slug' => 'diver', 'name' => __( 'Diver', 'luxurywatchs' ), 'tag' => __( 'Built for the deep', 'luxurywatchs' ) ),
		array( 'slug' => 'gmt-time-zone', 'name' => __( 'GMT / Time Zone', 'luxurywatchs' ), 'tag' => __( 'Two zones, one wrist', 'luxurywatchs' ) ),
		array( 'slug' => 'classic', 'name' => __( 'Classic & Dress', 'luxurywatchs' ), 'tag' => __( 'Timeless elegance', 'luxurywatchs' ) ),
		array( 'slug' => 'digital', 'name' => __( 'Digital', 'luxurywatchs' ), 'tag' => __( 'Modern & smart', 'luxurywatchs' ) ),
		array( 'slug' => 'skeleton', 'name' => __( 'Skeleton', 'luxurywatchs' ), 'tag' => __( 'See the heartbeat', 'luxurywatchs' ) ),
		array( 'slug' => 'women', 'name' => __( 'For Her', 'luxurywatchs' ), 'tag' => __( 'Graceful statements', 'luxurywatchs' ) ),
		array( 'slug' => 'couple', 'name' => __( 'Couple Sets', 'luxurywatchs' ), 'tag' => __( 'Gift together', 'luxurywatchs' ) ),
	);
}

/**
 * In-house signature collections (own brand names only).
 *
 * @return array
 */
function lw_collections() {
	return array(
		array( 'slug' => 'abyss-diver', 'name' => 'Abyss', 'line' => __( 'Ceramic-bezel divers', 'luxurywatchs' ), 'tone' => 'ocean' ),
		array( 'slug' => 'grand-prix', 'name' => 'Grand Prix', 'line' => __( 'Tachymeter chronographs', 'luxurywatchs' ), 'tone' => 'carbon' ),
		array( 'slug' => 'voyager-gmt', 'name' => 'Voyager', 'line' => __( 'Dual-tone GMT travellers', 'luxurywatchs' ), 'tone' => 'pepsi' ),
		array( 'slug' => 'heritage', 'name' => 'Heritage', 'line' => __( 'Slim dress classics', 'luxurywatchs' ), 'tone' => 'ivory' ),
		array( 'slug' => 'aurum', 'name' => 'Aurum', 'line' => __( 'Two-tone & gold finish', 'luxurywatchs' ), 'tone' => 'gold' ),
		array( 'slug' => 'atelier-skeleton', 'name' => 'Atelier', 'line' => __( 'Open-heart automatics', 'luxurywatchs' ), 'tone' => 'steel' ),
		array( 'slug' => 'nautica', 'name' => 'Nautica', 'line' => __( 'Integrated-bracelet sports', 'luxurywatchs' ), 'tone' => 'navy' ),
		array( 'slug' => 'pulse-digital', 'name' => 'Pulse', 'line' => __( 'Digital & ana-digi', 'luxurywatchs' ), 'tone' => 'carbon' ),
	);
}

/**
 * Price bands in INR.
 *
 * @return array
 */
function lw_price_bands() {
	return array(
		array( 'label' => __( 'Under', 'luxurywatchs' ), 'amount' => '4,999', 'min' => 0, 'max' => 4999 ),
		array( 'label' => __( 'Under', 'luxurywatchs' ), 'amount' => '9,999', 'min' => 0, 'max' => 9999 ),
		array( 'label' => __( 'Under', 'luxurywatchs' ), 'amount' => '19,999', 'min' => 0, 'max' => 19999 ),
		array( 'label' => __( 'Premium', 'luxurywatchs' ), 'amount' => '20,000+', 'min' => 20000, 'max' => 0 ),
	);
}

/**
 * Shop URL filtered by price.
 *
 * @param int $min Min price.
 * @param int $max Max price, 0 for none.
 * @return string
 */
function lw_price_url( $min, $max ) {
	$args = array( 'min_price' => $min );
	if ( $max ) {
		$args['max_price'] = $max;
	}
	return add_query_arg( $args, lw_shop_url() );
}

/**
 * Inline SVG watch illustration used when no image is uploaded.
 *
 * @param string $tone Colour tone key.
 * @return string
 */
function lw_watch_svg( $tone = 'gold' ) {
	$tones = array(
		'gold'   => array( '#1a1712', '#c9a45c', '#e8cf95' ),
		'ocean'  => array( '#06222f', '#2c7a8c', '#9fd3df' ),
		'carbon' => array( '#111', '#555', '#d9d9d9' ),
		'pepsi'  => array( '#101624', '#b3243a', '#2647a8' ),
		'ivory'  => array( '#f3ede1', '#8a6d3b', '#3a2f22' ),
		'steel'  => array( '#1b1d21', '#8b939c', '#c9a45c' ),
		'navy'   => array( '#0d1a33', '#2e4a7d', '#cfd8e6' ),
	);
	$c = isset( $tones[ $tone ] ) ? $tones[ $tone ] : $tones['gold'];
	return sprintf(
		'<svg class="lw-watch-svg" viewBox="0 0 200 260" aria-hidden="true" focusable="false"><rect x="70" y="0" width="60" height="70" rx="8" fill="%2$s" opacity=".55"/><rect x="70" y="190" width="60" height="70" rx="8" fill="%2$s" opacity=".55"/><circle cx="100" cy="130" r="74" fill="%2$s"/><circle cx="100" cy="130" r="64" fill="%1$s"/><circle cx="100" cy="130" r="58" fill="none" stroke="%3$s" stroke-width="1" stroke-dasharray="1 8.1"/><rect x="176" y="122" width="10" height="16" rx="3" fill="%2$s"/><line x1="100" y1="130" x2="100" y2="88" stroke="%3$s" stroke-width="4" stroke-linecap="round"/><line x1="100" y1="130" x2="132" y2="146" stroke="%3$s" stroke-width="3" stroke-linecap="round"/><circle cx="100" cy="130" r="4" fill="%3$s"/></svg>',
		esc_attr( $c[0] ),
		esc_attr( $c[1] ),
		esc_attr( $c[2] )
	);
}

/**
 * Small inline icon set.
 *
 * @param string $name Icon key.
 * @return string
 */
function lw_icon( $name ) {
	$icons = array(
		'search'   => '<path d="M11 19a8 8 0 1 1 0-16 8 8 0 0 1 0 16Zm10 2-4.35-4.35"/>',
		'user'     => '<path d="M20 21a8 8 0 0 0-16 0M12 13a5 5 0 1 0 0-10 5 5 0 0 0 0 10Z"/>',
		'bag'      => '<path d="M6 7h12l1 14H5L6 7Zm3 0a3 3 0 0 1 6 0"/>',
		'heart'    => '<path d="M12 21s-8-5.5-8-11a4.5 4.5 0 0 1 8-2.8A4.5 4.5 0 0 1 20 10c0 5.5-8 11-8 11Z"/>',
		'menu'     => '<path d="M3 6h18M3 12h18M3 18h18"/>',
		'close'    => '<path d="M6 6l12 12M18 6 6 18"/>',
		'home'     => '<path d="M3 11 12 4l9 7v9h-6v-6H9v6H3v-9Z"/>',
		'grid'     => '<path d="M4 4h7v7H4zM13 4h7v7h-7zM4 13h7v7H4zM13 13h7v7h-7z"/>',
		'truck'    => '<path d="M3 6h11v10H3zM14 9h4l3 3v4h-7M7.5 19a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3Zm10 0a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3Z"/>',
		'cash'     => '<path d="M3 7h18v10H3zM12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"/>',
		'shield'   => '<path d="M12 3 4 6v6c0 5 3.5 8 8 9 4.5-1 8-4 8-9V6l-8-3Zm-3 9 2 2 4-4"/>',
		'return'   => '<path d="M4 9h11a5 5 0 0 1 0 10H8M4 9l4-4M4 9l4 4"/>',
		'gift'     => '<path d="M4 11h16v10H4zM3 7h18v4H3zM12 7v14M12 7S10 3 7.5 4 9 7 12 7Zm0 0s2-4 4.5-3S15 7 12 7Z"/>',
		'star'     => '<path d="m12 3 2.7 5.6 6.1.9-4.4 4.3 1 6.1L12 17l-5.4 2.9 1-6.1-4.4-4.3 6.1-.9L12 3Z"/>',
		'arrow'    => '<path d="M5 12h14M13 6l6 6-6 6"/>',
		'chevron-l' => '<path d="m15 6-6 6 6 6"/>',
		'chevron-r' => '<path d="m9 6 6 6-6 6"/>',
		'gear'     => '<path d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Zm7.4-3a7.4 7.4 0 0 0-.1-1.2l2-1.6-2-3.4-2.4 1a7.4 7.4 0 0 0-2-1.2L14.5 3h-5l-.4 2.6a7.4 7.4 0 0 0-2 1.2l-2.4-1-2 3.4 2 1.6a7.4 7.4 0 0 0 0 2.4l-2 1.6 2 3.4 2.4-1a7.4 7.4 0 0 0 2 1.2l.4 2.6h5l.4-2.6a7.4 7.4 0 0 0 2-1.2l2.4 1 2-3.4-2-1.6c.1-.4.1-.8.1-1.2Z"/>',
		'box'      => '<path d="M3 7.5 12 3l9 4.5v9L12 21l-9-4.5v-9Zm0 0L12 12m0 0 9-4.5M12 12v9"/>',
		'headset'  => '<path d="M4 14v-2a8 8 0 0 1 16 0v2M4 14h3v5H5a1 1 0 0 1-1-1v-4Zm16 0h-3v5h2a1 1 0 0 0 1-1v-4Z"/>',
	);
	if ( ! isset( $icons[ $name ] ) ) {
		return '';
	}
	return '<svg class="lw-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">' . $icons[ $name ] . '</svg>';
}

/**
 * WhatsApp glyph.
 *
 * @return string
 */
function lw_whatsapp_icon() {
	return '<svg class="lw-icon" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false"><path d="M12 2a10 10 0 0 0-8.6 15.1L2 22l5-1.3A10 10 0 1 0 12 2Zm0 18.2a8.2 8.2 0 0 1-4.2-1.1l-.3-.2-3 .8.8-2.9-.2-.3A8.2 8.2 0 1 1 12 20.2Zm4.5-6.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 0 1-3.3-2.9c-.2-.4.2-.4.7-1.3.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5a1 1 0 0 0-.7.3 3 3 0 0 0-.9 2.2 5.1 5.1 0 0 0 1.1 2.7 11.7 11.7 0 0 0 4.5 4c1.7.7 2.3.8 3.2.6.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.2-1.2-.1-.1-.3-.2-.5-.3Z"/></svg>';
}

/**
 * Render product loop, or fallback demo cards when WooCommerce/products are missing.
 *
 * @param array $args { type: best|new|sale, limit:int }.
 */
function lw_product_grid( $args = array() ) {
	$args = wp_parse_args( $args, array( 'type' => 'best', 'limit' => 8 ) );

	if ( class_exists( 'WooCommerce' ) ) {
		$atts = array(
			'limit'   => (int) $args['limit'],
			'columns' => 4,
		);
		if ( 'best' === $args['type'] ) {
			$atts['best_selling'] = 'true';
		} elseif ( 'sale' === $args['type'] ) {
			$atts['on_sale'] = 'true';
		} else {
			$atts['orderby'] = 'date';
			$atts['order']   = 'DESC';
		}
		$parts = array();
		foreach ( $atts as $k => $v ) {
			$parts[] = $k . '="' . esc_attr( $v ) . '"';
		}
		$html = do_shortcode( '[products ' . implode( ' ', $parts ) . ']' );
		if ( false !== strpos( $html, 'class="product' ) || false !== strpos( $html, 'type-product' ) ) {
			echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- WooCommerce output.
			return;
		}
	}

	$demo = array(
		array( 'Abyss 41 Ceramic Diver', 'ocean', 8999, 12999, 4.9, 'Bestseller' ),
		array( 'Grand Prix Chrono Panda', 'carbon', 11499, 15999, 4.8, 'New' ),
		array( 'Voyager GMT Bi-Colour', 'pepsi', 12999, 17999, 4.9, 'Hot' ),
		array( 'Heritage Slim Ivory', 'ivory', 6499, 8999, 4.7, '' ),
		array( 'Aurum Two-Tone Date', 'gold', 13999, 19999, 5.0, 'Limited' ),
		array( 'Atelier Open Heart', 'steel', 9999, 13999, 4.8, '' ),
		array( 'Nautica Blue Integrated', 'navy', 14999, 21999, 4.9, 'Trending' ),
		array( 'Pulse Ana-Digi Carbon', 'carbon', 3999, 5999, 4.6, '' ),
	);
	if ( 'new' === $args['type'] ) {
		$demo = array_reverse( $demo );
	}
	$demo = array_slice( $demo, 0, (int) $args['limit'] );

	echo '<ul class="lw-demo-grid">';
	foreach ( $demo as $d ) {
		$off = round( ( 1 - $d[2] / $d[3] ) * 100 );
		echo '<li class="lw-pcard">';
		echo '<a class="lw-pcard__media" href="' . esc_url( lw_shop_url() ) . '">';
		if ( $d[5] ) {
			echo '<span class="lw-badge">' . esc_html( $d[5] ) . '</span>';
		}
		echo '<span class="lw-off">-' . esc_html( $off ) . '%</span>';
		echo lw_watch_svg( $d[1] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '</a><div class="lw-pcard__body">';
		echo '<div class="lw-rating">' . lw_icon( 'star' ) . ' ' . esc_html( number_format( $d[4], 1 ) ) . '</div>';
		echo '<h3 class="lw-pcard__title"><a href="' . esc_url( lw_shop_url() ) . '">' . esc_html( $d[0] ) . '</a></h3>';
		echo '<div class="lw-price"><ins>₹' . esc_html( number_format_i18n( $d[2] ) ) . '</ins> <del>₹' . esc_html( number_format_i18n( $d[3] ) ) . '</del></div>';
		echo '<div class="lw-pcard__meta">' . esc_html__( 'COD available • Free shipping', 'luxurywatchs' ) . '</div>';
		echo '<a class="lw-btn lw-btn--sm lw-btn--ghost" href="' . esc_url( lw_whatsapp_url( sprintf( 'Hi! I want to order: %s', $d[0] ) ) ) . '" target="_blank" rel="noopener">' . lw_whatsapp_icon() . esc_html__( 'Order on WhatsApp', 'luxurywatchs' ) . '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '</div></li>';
	}
	echo '</ul>';
}
