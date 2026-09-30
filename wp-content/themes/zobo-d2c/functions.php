<?php
/**
 * Zobo D2C theme functions.
 *
 * @package Zobo
 */

if ( ! defined( 'ZOBOD2C_VERSION' ) ) {
	define( 'ZOBOD2C_VERSION', '1.0.0' );
}

require get_template_directory() . '/inc/customizer.php';
require get_template_directory() . '/inc/content.php';
require get_template_directory() . '/inc/contact-form.php';

/**
 * Theme setup.
 */
function zobod2c_setup() {
	load_theme_textdomain( 'zobo-d2c', get_template_directory() . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 80,
			'width'       => 240,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);

	register_nav_menus(
		array(
			'primary' => __( 'Primary menu', 'zobo-d2c' ),
			'footer'  => __( 'Footer menu', 'zobo-d2c' ),
		)
	);

	add_image_size( 'zobo-work', 900, 700, true );
}
add_action( 'after_setup_theme', 'zobod2c_setup' );

/**
 * Case study post type, so real projects can be added from the dashboard.
 */
function zobod2c_register_work() {
	register_post_type(
		'zobo_work',
		array(
			'labels'       => array(
				'name'          => __( 'Case Studies', 'zobo-d2c' ),
				'singular_name' => __( 'Case Study', 'zobo-d2c' ),
				'add_new_item'  => __( 'Add New Case Study', 'zobo-d2c' ),
				'edit_item'     => __( 'Edit Case Study', 'zobo-d2c' ),
			),
			'public'       => true,
			'has_archive'  => true,
			'rewrite'      => array( 'slug' => 'case-studies' ),
			'menu_icon'    => 'dashicons-portfolio',
			'show_in_rest' => true,
			'supports'     => array( 'title', 'editor', 'excerpt', 'thumbnail' ),
		)
	);
}
add_action( 'init', 'zobod2c_register_work' );

/**
 * Styles and scripts.
 */
function zobod2c_assets() {
	wp_enqueue_style(
		'zobo-fonts',
		'https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,400;12..96,600;12..96,800&family=Inter:wght@400;500;600&display=swap',
		array(),
		null
	);
	wp_enqueue_style( 'zobo-main', get_template_directory_uri() . '/assets/css/main.css', array( 'zobo-fonts' ), ZOBOD2C_VERSION );
	wp_enqueue_script( 'zobo-main', get_template_directory_uri() . '/assets/js/main.js', array(), ZOBOD2C_VERSION, true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'zobod2c_assets' );

/**
 * Preconnect to Google Fonts.
 *
 * @param array  $urls          URLs to print for resource hints.
 * @param string $relation_type The relation type the URLs are printed for.
 * @return array
 */
function zobod2c_resource_hints( $urls, $relation_type ) {
	if ( 'preconnect' === $relation_type ) {
		$urls[] = 'https://fonts.googleapis.com';
		$urls[] = array(
			'href' => 'https://fonts.gstatic.com',
			'crossorigin',
		);
	}
	return $urls;
}
add_filter( 'wp_resource_hints', 'zobod2c_resource_hints', 10, 2 );

/**
 * Fallback primary menu pointing at front-page sections.
 */
function zobod2c_fallback_menu() {
	$items = array(
		'#journey'  => __( 'Journey', 'zobo-d2c' ),
		'#services' => __( 'Services', 'zobo-d2c' ),
		'#industries' => __( 'Industries', 'zobo-d2c' ),
		'#plans'    => __( 'Plans', 'zobo-d2c' ),
		'#work'     => __( 'Work', 'zobo-d2c' ),
		'#faq'      => __( 'FAQ', 'zobo-d2c' ),
	);
	$base = is_front_page() ? '' : home_url( '/' );
	echo '<ul class="menu">';
	foreach ( $items as $anchor => $label ) {
		printf( '<li><a href="%s">%s</a></li>', esc_url( $base . $anchor ), esc_html( $label ) );
	}
	echo '</ul>';
}

/**
 * Site name for the wordmark, without a trailing domain suffix
 * (a site titled "zobo.co.in" shows as "zobo").
 *
 * @return string
 */
function zobod2c_brand_name() {
	$name = get_bloginfo( 'name' );
	$bare = preg_replace( '/\.(co\.in|in|com|co)$/i', '', $name );
	return '' !== $bare ? $bare : $name;
}

/**
 * WhatsApp click-to-chat URL built from the Customizer number.
 *
 * @param string $message Prefilled message.
 * @return string
 */
function zobod2c_whatsapp_url( $message = '' ) {
	$number = preg_replace( '/\D+/', '', zobod2c_opt( 'whatsapp' ) );
	if ( '' === $number ) {
		return '';
	}
	if ( '' === $message ) {
		$message = __( 'Hi Zobo, I have a brand idea and want to launch it.', 'zobo-d2c' );
	}
	return 'https://wa.me/' . $number . '?text=' . rawurlencode( $message );
}

/**
 * Inline SVG icons.
 *
 * @param string $name Icon name.
 * @return string
 */
function zobod2c_icon( $name ) {
	$icons = array(
		'arrow'     => '<path d="M5 12h14M13 6l6 6-6 6"/>',
		'whatsapp'  => '<path d="M3 21l1.7-5A8.5 8.5 0 1 1 8 19.4L3 21z"/><path d="M9 9.5c0 3 2.5 5.5 5.5 5.5l1.2-1.3-1.9-1-1 .8a4 4 0 0 1-2.3-2.3l.8-1-1-1.9L9 9.5z"/>',
		'bulb'      => '<path d="M9 18h6M10 21h4M12 3a6 6 0 0 0-3.5 10.9V16h7v-2.1A6 6 0 0 0 12 3z"/>',
		'pen'       => '<path d="M12 19l7-7 3 3-7 7-3-3z"/><path d="M18 13l-1.5-7.5L2 2l3.5 14.5L13 18l5-5z"/><circle cx="11" cy="11" r="2"/>',
		'shield'    => '<path d="M12 3l8 3v6c0 5-3.5 8-8 9-4.5-1-8-4-8-9V6l8-3z"/><path d="M9 12l2 2 4-4"/>',
		'doc'       => '<path d="M14 3H6v18h12V7l-4-4z"/><path d="M14 3v4h4M9 13h6M9 17h6"/>',
		'factory'   => '<path d="M3 21V10l6 4V10l6 4V6h6v15H3z"/><path d="M7 17h2M12 17h2M17 17h2"/>',
		'handshake' => '<path d="M11 17l2 2a1.4 1.4 0 0 0 2-2"/><path d="M14 14l2.5 2.5a1.4 1.4 0 0 0 2-2l-3.8-3.9a3 3 0 0 0-4.2 0l-.9.9a1.4 1.4 0 0 1-2-2l2.8-2.8a5 5 0 0 1 5.6-1l.7.3H21v8"/><path d="M3 5h3l7 7M3 13l5 5"/>',
		'cart'      => '<circle cx="9" cy="20" r="1.5"/><circle cx="18" cy="20" r="1.5"/><path d="M2 3h3l2.7 12.4a2 2 0 0 0 2 1.6h8.5a2 2 0 0 0 2-1.5L22 8H6"/>',
		'monitor'   => '<rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/>',
		'chart'     => '<path d="M3 3v18h18"/><path d="M7 15l4-4 3 3 6-7"/>',
		'star'      => '<path d="M12 2l3 6.5 7 .8-5.2 4.8 1.4 7L12 17.6 5.8 21l1.4-7L2 9.3l7-.8L12 2z"/>',
		'tv'        => '<rect x="2" y="7" width="20" height="14" rx="2"/><path d="M17 2l-5 5-5-5"/>',
		'flask'     => '<path d="M9 3h6M10 3v6L4.5 18.5A1.7 1.7 0 0 0 6 21h12a1.7 1.7 0 0 0 1.5-2.5L14 9V3"/><path d="M7 15h10"/>',
		'clock'     => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
		'plus'      => '<path d="M12 5v14M5 12h14"/>',
		'mail'      => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 6 9-6"/>',
		'phone'     => '<path d="M5 4h4l2 5-2.5 1.5a11 11 0 0 0 5 5L15 13l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 6a2 2 0 0 1 2-2z"/>',
		'pin'       => '<path d="M12 21s-7-6.2-7-12a7 7 0 0 1 14 0c0 5.8-7 12-7 12z"/><circle cx="12" cy="9" r="2.5"/>',
	);
	if ( ! isset( $icons[ $name ] ) ) {
		return '';
	}
	return '<svg class="icon" viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $icons[ $name ] . '</svg>';
}

/**
 * Flag JS support before paint so scroll reveals don't flash.
 */
function zobod2c_js_class() {
	echo "<script>document.documentElement.classList.add('js');</script>\n";
}
add_action( 'wp_head', 'zobod2c_js_class', 0 );

/**
 * Send visitors from the previous theme's template-only pages, which have no
 * content of their own, to the matching front-page section. A page that gets
 * real content in the editor is shown normally.
 */
function zobod2c_legacy_page_redirect() {
	if ( ! is_page() ) {
		return;
	}
	$map = array(
		'services'   => '#services',
		'industries' => '#industries',
		'process'    => '#journey',
		'pricing'    => '#plans',
		'work'       => '#work',
		'contact'    => '#contact',
	);
	$page = get_queried_object();
	if ( ! $page instanceof WP_Post || ! isset( $map[ $page->post_name ] ) || '' !== trim( $page->post_content ) ) {
		return;
	}
	wp_safe_redirect( home_url( '/' ) . $map[ $page->post_name ], 302 );
	exit;
}
add_action( 'template_redirect', 'zobod2c_legacy_page_redirect' );
