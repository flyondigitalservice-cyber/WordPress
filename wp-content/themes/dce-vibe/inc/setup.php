<?php
/**
 * Theme supports, menus, assets.
 *
 * @package dce-vibe
 */

defined( 'ABSPATH' ) || exit;

add_action( 'after_setup_theme', 'dce_setup' );
function dce_setup() {
	load_theme_textdomain( 'dce-vibe', DCE_DIR . '/languages' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'editor-styles' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' ) );
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 120,
			'width'       => 360,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);
	add_post_type_support( 'page', 'excerpt' );

	register_nav_menus(
		array(
			'primary'          => __( 'Header — main menu', 'dce-vibe' ),
			'footer_curtains'  => __( 'Footer — Curtains column', 'dce-vibe' ),
			'footer_blinds'    => __( 'Footer — Blinds column', 'dce-vibe' ),
			'footer_company'   => __( 'Footer — Company column', 'dce-vibe' ),
			'footer_legal'     => __( 'Footer — bottom links', 'dce-vibe' ),
		)
	);

	add_image_size( 'dce-card', 720, 540, true );

	add_editor_style( array( dce_fonts_url(), 'assets/css/blocks.css' ) );
}

function dce_fonts_url() {
	return 'https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;0,600;0,700;1,500&family=Manrope:wght@400;500;600;700;800&display=swap';
}

add_action( 'wp_enqueue_scripts', 'dce_assets' );
function dce_assets() {
	wp_enqueue_style( 'dce-fonts', dce_fonts_url(), array(), null );
	wp_enqueue_style( 'dce-style', get_stylesheet_uri(), array(), DCE_VERSION );
	wp_enqueue_style( 'dce-blocks', DCE_URI . '/assets/css/blocks.css', array( 'dce-style' ), DCE_VERSION );
	wp_enqueue_script( 'dce-site', DCE_URI . '/assets/js/site.js', array(), DCE_VERSION, array( 'strategy' => 'defer', 'in_footer' => true ) );
	wp_localize_script(
		'dce-site',
		'DCE',
		array(
			'ajax'   => admin_url( 'admin-ajax.php' ),
			'nonce'  => wp_create_nonce( 'dce_lead' ),
			'wa'     => dce_wa_number(),
			'brand'  => dce_opt( 'brand' ),
			'i18n'   => array(
				'required' => __( 'Please add your name and phone number.', 'dce-vibe' ),
				'opening'  => __( 'Opening WhatsApp… your request has been saved.', 'dce-vibe' ),
			),
		)
	);
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}

add_filter( 'wp_resource_hints', 'dce_resource_hints', 10, 2 );
function dce_resource_hints( $urls, $relation ) {
	if ( 'preconnect' === $relation ) {
		$urls[] = array( 'href' => 'https://fonts.gstatic.com', 'crossorigin' );
		$urls[] = 'https://fonts.googleapis.com';
	}
	return $urls;
}

add_action( 'widgets_init', 'dce_widgets' );
function dce_widgets() {
	register_sidebar(
		array(
			'name'          => __( 'Footer — extra (below brand column)', 'dce-vibe' ),
			'id'            => 'footer-extra',
			'description'   => __( 'Optional widgets shown under the footer brand text.', 'dce-vibe' ),
			'before_widget' => '<div id="%1$s" class="dce-widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h3>',
			'after_title'   => '</h3>',
		)
	);
}

/** Primary menu fallback before menus are created: list top-level pages. */
function dce_menu_fallback() {
	echo '<ul class="dce-nav-list">';
	wp_list_pages(
		array(
			'title_li' => '',
			'depth'    => 2,
		)
	);
	echo '</ul>';
}

/** Body classes. */
add_filter( 'body_class', 'dce_body_class' );
function dce_body_class( $classes ) {
	$classes[] = 'dce';
	if ( is_singular() && has_blocks() ) {
		$classes[] = 'dce-has-blocks';
	}
	return $classes;
}

/** Shorter excerpts. */
add_filter( 'excerpt_length', fn() => 26 );
add_filter( 'excerpt_more', fn() => '…' );

/** On first activation, keep the footer free of WordPress default widgets. */
add_action( 'after_switch_theme', 'dce_after_switch' );
function dce_after_switch() {
	if ( get_option( 'dce_widgets_cleaned' ) ) {
		return;
	}
	$sidebars = wp_get_sidebars_widgets();
	if ( ! empty( $sidebars['footer-extra'] ) ) {
		$sidebars['wp_inactive_widgets'] = array_merge( isset( $sidebars['wp_inactive_widgets'] ) ? $sidebars['wp_inactive_widgets'] : array(), $sidebars['footer-extra'] );
		$sidebars['footer-extra']        = array();
		wp_set_sidebars_widgets( $sidebars );
	}
	update_option( 'dce_widgets_cleaned', 1 );
}
