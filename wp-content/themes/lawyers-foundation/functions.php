<?php
/**
 * Lawyers Foundation theme.
 *
 * @package Lawyers_Foundation_Theme
 */

defined( 'ABSPATH' ) || exit;

/**
 * Theme supports and menus.
 */
function lft_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'custom-logo', array( 'height' => 96, 'width' => 96, 'flex-width' => true, 'flex-height' => true ) );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'wp-block-styles' );
	register_nav_menus(
		array(
			'primary' => __( 'Primary menu', 'lawyers-foundation-theme' ),
			'footer'  => __( 'Footer menu', 'lawyers-foundation-theme' ),
		)
	);
}
add_action( 'after_setup_theme', 'lft_setup' );

/**
 * Styles and the small mobile-menu script.
 */
function lft_assets() {
	wp_enqueue_style( 'lawyers-foundation-theme', get_stylesheet_uri(), array(), wp_get_theme()->get( 'Version' ) );
	wp_enqueue_script( 'lawyers-foundation-theme', get_template_directory_uri() . '/assets/nav.js', array(), wp_get_theme()->get( 'Version' ), array( 'in_footer' => true, 'strategy' => 'defer' ) );
}
add_action( 'wp_enqueue_scripts', 'lft_assets' );

/**
 * Whether the membership plugin is active.
 *
 * @return bool
 */
function lft_has_plugin() {
	return function_exists( 'lf_page_url' );
}

/**
 * Foundation name (plugin setting, else the site title).
 *
 * @return string
 */
function lft_name() {
	return lft_has_plugin() ? lf_org_name() : get_bloginfo( 'name' );
}

/**
 * URL of a plugin page, or a fallback.
 *
 * @param string $key Page key.
 * @return string
 */
function lft_url( $key ) {
	return lft_has_plugin() ? lf_page_url( $key ) : home_url( '/' );
}

/**
 * Fallback primary menu built from the plugin's pages.
 */
function lft_fallback_menu() {
	$items = array( home_url( '/' ) => __( 'Home', 'lawyers-foundation-theme' ) );
	if ( lft_has_plugin() ) {
		$items[ lft_url( 'work' ) ]      = __( 'Our Work', 'lawyers-foundation-theme' );
		$items[ lft_url( 'events' ) ]    = __( 'Events', 'lawyers-foundation-theme' );
		$items[ lft_url( 'directory' ) ] = __( 'Members', 'lawyers-foundation-theme' );
	}
	echo '<ul>';
	foreach ( $items as $url => $label ) {
		echo '<li><a href="' . esc_url( $url ) . '">' . esc_html( $label ) . '</a></li>';
	}
	echo '</ul>';
}

/**
 * Customizer: home page hero.
 *
 * @param WP_Customize_Manager $wp_customize Customizer.
 */
function lft_customize( $wp_customize ) {
	$wp_customize->add_section( 'lft_home', array( 'title' => __( 'Home page', 'lawyers-foundation-theme' ), 'priority' => 30 ) );
	$fields = array(
		'lft_hero_eyebrow' => array( __( 'Hero eyebrow', 'lawyers-foundation-theme' ), 'text' ),
		'lft_hero_title'   => array( __( 'Hero heading', 'lawyers-foundation-theme' ), 'text' ),
		'lft_hero_text'    => array( __( 'Hero text', 'lawyers-foundation-theme' ), 'textarea' ),
	);
	foreach ( $fields as $id => $field ) {
		$wp_customize->add_setting( $id, array( 'default' => lft_default( $id ), 'sanitize_callback' => 'textarea' === $field[1] ? 'sanitize_textarea_field' : 'sanitize_text_field' ) );
		$wp_customize->add_control( $id, array( 'label' => $field[0], 'section' => 'lft_home', 'type' => $field[1] ) );
	}
	$wp_customize->add_setting( 'lft_hero_image', array( 'sanitize_callback' => 'esc_url_raw' ) );
	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'lft_hero_image', array( 'label' => __( 'Hero background image', 'lawyers-foundation-theme' ), 'section' => 'lft_home' ) ) );
}
add_action( 'customize_register', 'lft_customize' );

/**
 * Default copy.
 *
 * @param string $id Setting ID.
 * @return string
 */
function lft_default( $id ) {
	$defaults = array(
		'lft_hero_eyebrow' => __( 'A foundation of lawyers, for justice', 'lawyers-foundation-theme' ),
		'lft_hero_title'   => __( 'Lawyers giving their skills to the people who need them most', 'lawyers-foundation-theme' ),
		'lft_hero_text'    => __( 'Our members are advocates and counsel who volunteer through the foundation: running free legal-aid clinics, defending the unrepresented and teaching communities their rights.', 'lawyers-foundation-theme' ),
	);
	return isset( $defaults[ $id ] ) ? $defaults[ $id ] : '';
}

/**
 * Theme mod with default.
 *
 * @param string $id Setting ID.
 * @return string
 */
function lft_mod( $id ) {
	return (string) get_theme_mod( $id, lft_default( $id ) );
}

/**
 * Scales-of-justice mark used when no custom logo is set.
 *
 * @return string
 */
function lft_mark() {
	return '<svg class="brand-mark" viewBox="0 0 40 40" aria-hidden="true" fill="none" stroke="#b08d3c" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="20" cy="20" r="19" stroke-opacity=".35"/><path d="M20 9v22M13 31h14M11 14h18M20 12v2"/><path d="M11 14l-4 8a4 4 0 0 0 8 0zM29 14l-4 8a4 4 0 0 0 8 0z"/></svg>';
}
