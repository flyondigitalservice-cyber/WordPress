<?php
/**
 * Bombay Marketing theme setup.
 *
 * @package Bombay
 */

defined( 'ABSPATH' ) || exit;

define( 'BOMBAY_THEME_VERSION', '1.0.0' );

if ( ! isset( $content_width ) ) {
	$content_width = 1170;
}

/**
 * Theme supports and menus.
 */
function bombay_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' ) );
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 232,
			'width'       => 1260,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);
	register_nav_menus( array( 'primary' => 'Main menu' ) );
}
add_action( 'after_setup_theme', 'bombay_setup' );

/**
 * On activation, reuse the existing "Main Menu" for the primary location if nothing is assigned.
 */
function bombay_assign_menu() {
	$locations = get_theme_mod( 'nav_menu_locations', array() );
	if ( empty( $locations['primary'] ) ) {
		$menu = wp_get_nav_menu_object( 'main-menu' );
		if ( $menu ) {
			$locations['primary'] = (int) $menu->term_id;
			set_theme_mod( 'nav_menu_locations', $locations );
		}
	}
}
add_action( 'after_switch_theme', 'bombay_assign_menu' );

/**
 * Styles and scripts.
 */
function bombay_assets() {
	$uri = get_template_directory_uri();
	$dir = get_template_directory();

	wp_enqueue_style(
		'bombay-fonts',
		'https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,600;12..96,800&family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,700&family=Space+Grotesk:wght@400;500;600;700&display=swap',
		array(),
		null
	);
	wp_enqueue_style( 'bombay-base', $uri . '/assets/css/base.css', array( 'bombay-fonts' ), (string) filemtime( $dir . '/assets/css/base.css' ) );
	wp_enqueue_style( 'bombay-site', $uri . '/assets/css/site.css', array( 'bombay-base' ), (string) filemtime( $dir . '/assets/css/site.css' ) );
	wp_enqueue_script(
		'bombay-site',
		$uri . '/assets/js/site.js',
		array(),
		(string) filemtime( $dir . '/assets/js/site.js' ),
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		)
	);
}
add_action( 'wp_enqueue_scripts', 'bombay_assets' );

/**
 * Preconnect to Google Fonts.
 *
 * @param array  $urls          URLs.
 * @param string $relation_type Relation.
 * @return array
 */
function bombay_resource_hints( $urls, $relation_type ) {
	if ( 'preconnect' === $relation_type ) {
		$urls[] = 'https://fonts.googleapis.com';
		$urls[] = array(
			'href' => 'https://fonts.gstatic.com',
			'crossorigin',
		);
	}
	return $urls;
}
add_filter( 'wp_resource_hints', 'bombay_resource_hints', 10, 2 );

/**
 * Logo markup: the Customizer logo if set, otherwise the uploaded white logo.
 */
function bombay_logo() {
	$id  = (int) get_theme_mod( 'custom_logo' );
	$src = $id ? wp_get_attachment_image_url( $id, 'full' ) : '';
	if ( ! $src ) {
		$src = content_url( '/uploads/2026/11/Bombay-Marketing-Logo-White.png' );
	}
	printf(
		'<a class="bm-brand" href="%1$s" rel="home"><img src="%2$s" alt="%3$s" width="1260" height="232" fetchpriority="high"></a>',
		esc_url( home_url( '/' ) ),
		esc_url( $src ),
		esc_attr( get_bloginfo( 'name' ) )
	);
}

/**
 * Print the main menu.
 *
 * @param string $class Menu UL class.
 * @param string $id    Menu UL id.
 */
function bombay_menu( $class, $id ) {
	$args = array(
		'container'   => false,
		'menu_class'  => $class,
		'menu_id'     => $id,
		'depth'       => 2,
		'fallback_cb' => false,
	);
	if ( has_nav_menu( 'primary' ) ) {
		$args['theme_location'] = 'primary';
	} else {
		$args['menu'] = 'main-menu';
	}
	wp_nav_menu( $args );
}

/**
 * Whether the current singular content brings its own Bombay hero (Elementor or raw bm- markup).
 *
 * @return bool
 */
function bombay_has_own_layout() {
	$id = get_queried_object_id();
	if ( ! $id ) {
		return false;
	}
	if ( 'builder' === get_post_meta( $id, '_elementor_edit_mode', true ) ) {
		return true;
	}
	return false !== strpos( (string) get_post_field( 'post_content', $id ), 'bm-hero' );
}

/**
 * Shorter excerpts for archive cards.
 */
add_filter(
	'excerpt_length',
	function () {
		return 26;
	}
);
add_filter(
	'excerpt_more',
	function () {
		return '&hellip;';
	}
);

/**
 * Body class for the theme shell.
 *
 * @param array $classes Classes.
 * @return array
 */
function bombay_body_class( $classes ) {
	$classes[] = 'bm-theme';
	return $classes;
}
add_filter( 'body_class', 'bombay_body_class' );

/**
 * Leftover assets from Element Pack / UiCore. Nothing on the site uses them; drop them so the
 * theme renders the same whether or not those plugins are still active.
 */
function bombay_drop_legacy_assets() {
	foreach ( array( 'bdt-uikit', 'ep-helper', 'base-desktop', 'base-mobile', 'elementor-post-99', 'uicore_global', 'uicore-animate' ) as $handle ) {
		wp_dequeue_style( $handle );
	}
	foreach ( array( 'bdt-uikit', 'element-pack-helper', 'uicore_global', 'uicore-animate' ) as $handle ) {
		wp_dequeue_script( $handle );
	}
}
add_action( 'wp_enqueue_scripts', 'bombay_drop_legacy_assets', 999 );
add_action( 'wp_print_styles', 'bombay_drop_legacy_assets', 1 );
add_action( 'wp_print_footer_scripts', 'bombay_drop_legacy_assets', 1 );
