<?php
/**
 * Theme supports, assets and Customizer.
 *
 * @package HadhramiFashion
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'after_setup_theme',
	function () {
		load_theme_textdomain( 'hadhramifashion', HF_DIR . '/languages' );
		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script' ) );
		add_theme_support( 'custom-logo', array( 'height' => 80, 'width' => 260, 'flex-width' => true, 'flex-height' => true ) );
		add_theme_support( 'responsive-embeds' );
		add_image_size( 'hf-portrait', 900, 1200, true );

		register_nav_menus(
			array(
				'primary' => __( 'Primary', 'hadhramifashion' ),
				'footer'  => __( 'Footer', 'hadhramifashion' ),
			)
		);
	}
);

add_action(
	'wp_enqueue_scripts',
	function () {
		wp_enqueue_style(
			'hf-fonts',
			'https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;1,400;1,500&family=Jost:wght@300;400;500&display=swap',
			array(),
			null
		);
		wp_enqueue_style( 'hf-style', get_stylesheet_uri(), array( 'hf-fonts' ), HF_VERSION );

		wp_enqueue_script( 'hf-main', HF_URI . '/assets/js/main.js', array(), HF_VERSION, true );
		wp_localize_script( 'hf-main', 'HF', hf_front_config() );

		if ( hf_page_needs_maps() ) {
			wp_enqueue_script( 'hf-maps', HF_URI . '/assets/js/maps.js', array( 'hf-main' ), HF_VERSION, true );
		}
	}
);

/**
 * Whether the current view shows a Google Map.
 *
 * @return bool
 */
function hf_page_needs_maps() {
	if ( is_front_page() ) {
		return true;
	}
	if ( is_page_template( array( 'page-templates/checkout.php', 'page-templates/track.php', 'page-templates/rider.php', 'page-templates/express.php' ) ) ) {
		return true;
	}
	return false;
}

/**
 * Configuration passed to the front-end scripts.
 *
 * @return array
 */
function hf_front_config() {
	return array(
		'rest'        => esc_url_raw( rest_url( 'hf/v1/' ) ),
		'mapsKey'     => hf_opt( 'maps_api_key' ),
		'currency'    => hf_opt( 'currency_symbol' ),
		'store'       => array(
			'name'    => hf_opt( 'store_name' ),
			'address' => hf_opt( 'store_address' ),
			'lat'     => (float) hf_opt( 'store_lat' ),
			'lng'     => (float) hf_opt( 'store_lng' ),
		),
		'expressKm'   => (float) hf_opt( 'express_radius_km' ),
		'tailoredKm'  => (float) hf_opt( 'tailored_radius_km' ),
		'checkoutUrl' => hf_page_url( 'page-templates/checkout.php' ),
		'trackUrl'    => hf_page_url( 'page-templates/track.php' ),
		'shopUrl'     => get_post_type_archive_link( 'hf_product' ),
		'i18n'        => array(
			'added'      => __( 'Added to your bag', 'hadhramifashion' ),
			'empty'      => __( 'Your bag is empty.', 'hadhramifashion' ),
			'outOfRange' => __( 'This address is outside our delivery area — we will call you to arrange a fitting.', 'hadhramifashion' ),
		),
	);
}

/*
 * Customizer — every brand image can be replaced by an uploaded one.
 */
add_action(
	'customize_register',
	function ( $wp_customize ) {
		$wp_customize->add_section(
			'hf_imagery',
			array(
				'title'       => __( 'Hadhrami Imagery', 'hadhramifashion' ),
				'description' => __( 'Leave empty to use the images from the brand Google Drive folder.', 'hadhramifashion' ),
				'priority'    => 30,
			)
		);

		$slots = array(
			'hero'           => __( 'Home hero', 'hadhramifashion' ),
			'legacy'         => __( 'Legacy / heritage story', 'hadhramifashion' ),
			'atelier'        => __( 'Atelier & craft', 'hadhramifashion' ),
			'tile_suits'     => __( 'Collection tile — Suits', 'hadhramifashion' ),
			'tile_shirts'    => __( 'Collection tile — Shirts', 'hadhramifashion' ),
			'tile_cloth'     => __( 'Collection tile — Cloth', 'hadhramifashion' ),
			'tile_wedding'   => __( 'Occasion — Wedding', 'hadhramifashion' ),
			'tile_occasion'  => __( 'Occasion — Evening', 'hadhramifashion' ),
			'tile_essential' => __( 'Occasion — Everyday', 'hadhramifashion' ),
		);

		foreach ( $slots as $slot => $label ) {
			$wp_customize->add_setting( 'hf_img_' . $slot, array( 'sanitize_callback' => 'esc_url_raw' ) );
			$wp_customize->add_control(
				new WP_Customize_Image_Control(
					$wp_customize,
					'hf_img_' . $slot,
					array(
						'label'   => $label,
						'section' => 'hf_imagery',
					)
				)
			);
		}

		$wp_customize->add_section( 'hf_copy', array( 'title' => __( 'Hadhrami Home Copy', 'hadhramifashion' ), 'priority' => 31 ) );
		$copy = array(
			'hero_title'   => array( __( 'Hero headline', 'hadhramifashion' ), __( 'Cloth, cut and conscience — handed down.', 'hadhramifashion' ) ),
			'hero_text'    => array( __( 'Hero text', 'hadhramifashion' ), __( 'The House of Saquib Hadhrami has dressed fathers, sons and grooms for generations. Today, a white shirt reaches your door in two hours, and a suit cut to your measure in twenty-four.', 'hadhramifashion' ) ),
			'legacy_quote' => array( __( 'Patron quote', 'hadhramifashion' ), __( 'A suit should be ready when life is. My grandfather kept his shears sharp for weddings that could not wait — we simply kept the promise.', 'hadhramifashion' ) ),
		);
		foreach ( $copy as $key => $c ) {
			$wp_customize->add_setting( 'hf_' . $key, array( 'default' => $c[1], 'sanitize_callback' => 'sanitize_textarea_field' ) );
			$wp_customize->add_control( 'hf_' . $key, array( 'label' => $c[0], 'section' => 'hf_copy', 'type' => 'textarea' ) );
		}
	}
);

/**
 * Get a Customizer copy string with its default.
 *
 * @param string $key Key without the `hf_` prefix.
 * @return string
 */
function hf_copy( $key ) {
	$defaults = array(
		'hero_title'   => __( 'Cloth, cut and conscience — handed down.', 'hadhramifashion' ),
		'hero_text'    => __( 'The House of Saquib Hadhrami has dressed fathers, sons and grooms for generations. Today, a white shirt reaches your door in two hours, and a suit cut to your measure in twenty-four.', 'hadhramifashion' ),
		'legacy_quote' => __( 'A suit should be ready when life is. My grandfather kept his shears sharp for weddings that could not wait — we simply kept the promise.', 'hadhramifashion' ),
	);
	return get_theme_mod( 'hf_' . $key, isset( $defaults[ $key ] ) ? $defaults[ $key ] : '' );
}

/**
 * Fallback primary navigation when no menu is assigned.
 */
function hf_fallback_menu() {
	$items = array(
		array( __( 'Suits', 'hadhramifashion' ), get_term_link( 'suits', 'hf_collection' ) ),
		array( __( 'Shirts', 'hadhramifashion' ), get_term_link( 'shirts', 'hf_collection' ) ),
		array( __( 'Cloth Library', 'hadhramifashion' ), hf_page_url( 'page-templates/cloth.php' ) ),
		array( __( 'Heritage', 'hadhramifashion' ), hf_page_url( 'page-templates/heritage.php' ) ),
		array( __( '2-Hour Delivery', 'hadhramifashion' ), hf_page_url( 'page-templates/express.php' ) ),
	);
	echo '<ul class="hf-menu">';
	foreach ( $items as $item ) {
		if ( is_wp_error( $item[1] ) ) {
			continue;
		}
		printf( '<li><a href="%s">%s</a></li>', esc_url( $item[1] ), esc_html( $item[0] ) );
	}
	echo '</ul>';
}
