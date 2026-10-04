<?php
/**
 * Styles, scripts, fonts and the dynamic colour system.
 *
 * @package FlavorKit
 */

defined( 'ABSPATH' ) || exit;

/**
 * Asset version based on file modification time.
 *
 * @param string $relative Path relative to theme root.
 * @return string
 */
function flavorkit_asset_version( $relative ) {
	$file = FLAVORKIT_DIR . '/' . $relative;
	return file_exists( $file ) ? (string) filemtime( $file ) : FLAVORKIT_VERSION;
}

/**
 * Google Fonts URL for the chosen heading/body fonts.
 *
 * @return string
 */
function flavorkit_fonts_url() {
	$fonts    = flavorkit_fonts();
	$families = array_unique( array( flavorkit_mod( 'font_heading' ), flavorkit_mod( 'font_body' ) ) );
	$query    = array();
	foreach ( $families as $family ) {
		if ( isset( $fonts[ $family ] ) ) {
			$query[] = 'family=' . str_replace( ' ', '+', $family ) . ':wght@' . $fonts[ $family ];
		}
	}
	if ( ! $query ) {
		return '';
	}
	return 'https://fonts.googleapis.com/css2?' . implode( '&', $query ) . '&display=swap';
}

/**
 * CSS custom properties generated from the Customizer.
 *
 * @return string
 */
function flavorkit_dynamic_css() {
	$c       = flavorkit_colors();
	$heading = flavorkit_mod( 'font_heading' );
	$body    = flavorkit_mod( 'font_body' );
	$radius  = absint( flavorkit_mod( 'radius' ) );

	$vars = array(
		'--fk-primary'      => $c['primary'],
		'--fk-secondary'    => $c['secondary'],
		'--fk-accent'       => $c['accent'],
		'--fk-dark'         => $c['dark'],
		'--fk-cream'        => $c['cream'],
		'--fk-on-primary'   => flavorkit_contrast( $c['primary'], $c['dark'] ),
		'--fk-on-secondary' => flavorkit_contrast( $c['secondary'], $c['dark'] ),
		'--fk-on-accent'    => flavorkit_contrast( $c['accent'], $c['dark'] ),
		'--fk-font-heading' => '"' . $heading . '", system-ui, sans-serif',
		'--fk-font-body'    => '"' . $body . '", system-ui, sans-serif',
		'--fk-radius'       => $radius . 'px',
	);

	$css = ':root{';
	foreach ( $vars as $name => $value ) {
		$css .= $name . ':' . wp_strip_all_tags( $value ) . ';';
	}
	return $css . '}';
}

/**
 * Front-end assets.
 */
function flavorkit_enqueue_assets() {
	$fonts = flavorkit_fonts_url();
	if ( $fonts ) {
		wp_enqueue_style( 'flavorkit-fonts', $fonts, array(), null ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
	}

	wp_enqueue_style( 'flavorkit-main', FLAVORKIT_URI . '/assets/css/main.css', array(), flavorkit_asset_version( 'assets/css/main.css' ) );
	wp_add_inline_style( 'flavorkit-main', flavorkit_dynamic_css() );

	if ( class_exists( 'WooCommerce' ) ) {
		wp_enqueue_style( 'flavorkit-woocommerce', FLAVORKIT_URI . '/assets/css/woocommerce.css', array( 'flavorkit-main' ), flavorkit_asset_version( 'assets/css/woocommerce.css' ) );
		// Keep the header cart count & drawer in sync after AJAX add-to-cart.
		wp_enqueue_script( 'wc-cart-fragments' );
	}

	wp_enqueue_script(
		'flavorkit-main',
		FLAVORKIT_URI . '/assets/js/main.js',
		array(),
		flavorkit_asset_version( 'assets/js/main.js' ),
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		)
	);

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'flavorkit_enqueue_assets' );

/**
 * Preconnect to Google Fonts.
 *
 * @param array  $urls          URLs.
 * @param string $relation_type Relation.
 * @return array
 */
function flavorkit_resource_hints( $urls, $relation_type ) {
	if ( 'preconnect' === $relation_type && wp_style_is( 'flavorkit-fonts', 'queue' ) ) {
		$urls[] = array( 'href' => 'https://fonts.gstatic.com', 'crossorigin' );
	}
	return $urls;
}
add_filter( 'wp_resource_hints', 'flavorkit_resource_hints', 10, 2 );

/**
 * Block editor: same fonts and colours as the front end.
 */
function flavorkit_editor_assets() {
	$fonts = flavorkit_fonts_url();
	if ( $fonts ) {
		wp_enqueue_style( 'flavorkit-editor-fonts', $fonts, array(), null ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
	}
	wp_register_style( 'flavorkit-editor-vars', false, array(), FLAVORKIT_VERSION );
	wp_enqueue_style( 'flavorkit-editor-vars' );
	wp_add_inline_style( 'flavorkit-editor-vars', flavorkit_dynamic_css() );
}
add_action( 'enqueue_block_editor_assets', 'flavorkit_editor_assets' );

/**
 * Flag JS support early so scroll-reveal never hides content without JS.
 */
function flavorkit_js_flag() {
	echo "<script>document.documentElement.classList.add('fk-js');</script>\n";
}
add_action( 'wp_head', 'flavorkit_js_flag', 1 );
