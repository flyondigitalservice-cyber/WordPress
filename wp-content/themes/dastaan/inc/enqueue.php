<?php
/**
 * Assets: styles, scripts, fonts, CSS variables.
 *
 * @package Dastaan
 */

defined( 'ABSPATH' ) || exit;

/**
 * Google Fonts URL (opt-in, Customizer toggle). Returns '' when disabled.
 *
 * @return string
 */
function dastaan_fonts_url() {
	if ( ! dastaan_option( 'google_fonts' ) ) {
		return '';
	}
	$families = array(
		'Cormorant+Garamond:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500',
		'Jost:wght@300;400;500;600',
	);

	return esc_url_raw( 'https://fonts.googleapis.com/css2?family=' . implode( '&family=', $families ) . '&display=swap' );
}

/**
 * Front-end assets.
 */
function dastaan_enqueue_assets() {
	$ver = DASTAAN_VERSION;

	$fonts = dastaan_fonts_url();
	if ( $fonts ) {
		wp_enqueue_style( 'dastaan-fonts', $fonts, array(), null ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
	}

	wp_enqueue_style( 'dastaan-main', DASTAAN_URI . 'assets/css/main.css', array(), $ver );
	wp_add_inline_style( 'dastaan-main', dastaan_css_variables() );

	if ( dastaan_has_wc() ) {
		wp_enqueue_style( 'dastaan-woocommerce', DASTAAN_URI . 'assets/css/woocommerce.css', array( 'dastaan-main' ), $ver );
	}

	wp_enqueue_script( 'dastaan-main', DASTAAN_URI . 'assets/js/main.js', array(), $ver, true );
	wp_script_add_data( 'dastaan-main', 'strategy', 'defer' );

	wp_localize_script(
		'dastaan-main',
		'dastaanData',
		array(
			'ajaxUrl'     => admin_url( 'admin-ajax.php' ),
			'nonce'       => wp_create_nonce( 'dastaan_front' ),
			'homeUrl'     => home_url( '/' ),
			'cartUrl'     => dastaan_has_wc() ? wc_get_cart_url() : '',
			'checkoutUrl' => dastaan_has_wc() ? wc_get_checkout_url() : '',
			'heroAutoplay'=> (int) dastaan_option( 'hero_autoplay' ),
			'heroInterval'=> max( 3, (int) dastaan_option( 'hero_interval' ) ) * 1000,
			'consent'     => (int) dastaan_option( 'consent_enable' ),
			'currency'    => dastaan_has_wc() ? get_woocommerce_currency() : 'INR',
			'i18n'        => array(
				'addedToCart' => __( 'Added to your bag', 'dastaan' ),
				'viewBag'     => __( 'View bag', 'dastaan' ),
				'close'       => __( 'Close', 'dastaan' ),
				'days'        => __( 'days', 'dastaan' ),
				'hrs'         => __( 'hrs', 'dastaan' ),
				'min'         => __( 'min', 'dastaan' ),
				'sec'         => __( 'sec', 'dastaan' ),
				'copied'      => __( 'Link copied', 'dastaan' ),
				'codeCopied'  => __( 'Code copied: %s', 'dastaan' ),
			),
		)
	);

	if ( dastaan_has_wc() && is_product() ) {
		wp_enqueue_script( 'dastaan-product', DASTAAN_URI . 'assets/js/product.js', array( 'dastaan-main' ), $ver, true );
		wp_script_add_data( 'dastaan-product', 'strategy', 'defer' );
	}
	if ( dastaan_has_wc() && ( is_checkout() || is_cart() ) ) {
		wp_enqueue_script( 'dastaan-checkout', DASTAAN_URI . 'assets/js/checkout.js', array( 'dastaan-main', 'jquery' ), $ver, true );
		wp_script_add_data( 'dastaan-checkout', 'strategy', 'defer' );
	}

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'dastaan_enqueue_assets' );

/**
 * Build the CSS custom properties from Customizer colours/typography.
 *
 * @return string
 */
function dastaan_css_variables() {
	$primary = dastaan_option( 'color_primary' );
	$accent  = dastaan_option( 'color_accent' );
	$bg      = dastaan_option( 'color_bg' );
	$text    = dastaan_option( 'color_text' );
	$width   = max( 1100, min( 1800, (int) dastaan_option( 'container_width' ) ) );
	$radius  = max( 0, min( 40, (int) dastaan_option( 'button_radius' ) ) );

	$primary = sanitize_hex_color( $primary ) ? $primary : '#6E1E2B';
	$accent  = sanitize_hex_color( $accent ) ? $accent : '#B8933E';
	$bg      = sanitize_hex_color( $bg ) ? $bg : '#FAF7F2';
	$text    = sanitize_hex_color( $text ) ? $text : '#1F1A17';

	$css = ':root{'
		. '--d-primary:' . $primary . ';'
		. '--d-primary-dark:' . dastaan_shade( $primary, -18 ) . ';'
		. '--d-primary-soft:' . dastaan_hex_to_rgba( $primary, 0.08 ) . ';'
		. '--d-accent:' . $accent . ';'
		. '--d-accent-light:' . dastaan_shade( $accent, 28 ) . ';'
		. '--d-accent-text:' . dastaan_shade( $accent, -30 ) . ';'
		. '--d-accent-soft:' . dastaan_hex_to_rgba( $accent, 0.14 ) . ';'
		. '--d-bg:' . $bg . ';'
		. '--d-bg-2:' . dastaan_shade( $bg, -4 ) . ';'
		. '--d-ink:' . $text . ';'
		. '--d-container:' . $width . 'px;'
		. '--d-radius:' . $radius . 'px;'
		. '}';

	if ( ! dastaan_option( 'google_fonts' ) ) {
		$css .= ':root{--d-font-display:Georgia,"Times New Roman",serif;--d-font-body:system-ui,-apple-system,"Segoe UI",Roboto,sans-serif;}';
	}

	return $css;
}

/**
 * Lighten/darken a hex colour.
 *
 * @param string $hex     Hex.
 * @param int    $percent -100..100.
 * @return string
 */
function dastaan_shade( $hex, $percent ) {
	$hex = ltrim( $hex, '#' );
	if ( 3 === strlen( $hex ) ) {
		$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
	}
	$out = '#';
	for ( $i = 0; $i < 3; $i++ ) {
		$c = hexdec( substr( $hex, $i * 2, 2 ) );
		$c = $percent > 0 ? $c + ( 255 - $c ) * $percent / 100 : $c + $c * $percent / 100;
		$out .= str_pad( dechex( (int) round( max( 0, min( 255, $c ) ) ) ), 2, '0', STR_PAD_LEFT );
	}

	return $out;
}

/**
 * Hex to rgba().
 *
 * @param string $hex   Hex.
 * @param float  $alpha Alpha.
 * @return string
 */
function dastaan_hex_to_rgba( $hex, $alpha ) {
	$hex = ltrim( $hex, '#' );
	if ( 3 === strlen( $hex ) ) {
		$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
	}
	$r = hexdec( substr( $hex, 0, 2 ) );
	$g = hexdec( substr( $hex, 2, 2 ) );
	$b = hexdec( substr( $hex, 4, 2 ) );

	return sprintf( 'rgba(%d,%d,%d,%s)', $r, $g, $b, (float) $alpha );
}

/**
 * Preconnect / preload hints.
 *
 * @param array  $urls          URLs.
 * @param string $relation_type Relation.
 * @return array
 */
function dastaan_resource_hints( $urls, $relation_type ) {
	if ( 'preconnect' === $relation_type && dastaan_fonts_url() ) {
		$urls[] = array( 'href' => 'https://fonts.googleapis.com', 'crossorigin' );
		$urls[] = array( 'href' => 'https://fonts.gstatic.com', 'crossorigin' );
	}

	return $urls;
}
add_filter( 'wp_resource_hints', 'dastaan_resource_hints', 10, 2 );

/**
 * Preload the LCP hero image on the front page.
 */
function dastaan_preload_hero() {
	if ( ! is_front_page() ) {
		return;
	}
	$img = dastaan_option( 'hero_1_image' );
	if ( $img ) {
		printf( '<link rel="preload" as="image" href="%s" fetchpriority="high">' . "\n", esc_url( $img ) );
	}
}
add_action( 'wp_head', 'dastaan_preload_hero', 2 );

/**
 * Editor assets (block editor fonts for consistent typography).
 */
function dastaan_editor_assets() {
	$fonts = dastaan_fonts_url();
	if ( $fonts ) {
		wp_enqueue_style( 'dastaan-fonts-editor', $fonts, array(), null ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
	}
	wp_add_inline_style( 'wp-edit-blocks', dastaan_css_variables() );
}
add_action( 'enqueue_block_editor_assets', 'dastaan_editor_assets' );
