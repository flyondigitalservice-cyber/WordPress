<?php
/**
 * Dubai Curtain Experts — theme functions.
 *
 * A fully editable WordPress theme: every public string, image and colour is
 * controlled from Appearance → Customize, with content managed through
 * Services / Projects / Testimonials / FAQ post types.
 *
 * @package Dubai_Curtain_Experts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'DCE_VERSION', '1.1.0' );

require_once get_template_directory() . '/inc/defaults.php';
require_once get_template_directory() . '/inc/post-types.php';
require_once get_template_directory() . '/inc/customizer.php';
require_once get_template_directory() . '/inc/site.php';
require_once get_template_directory() . '/inc/installer.php';

/**
 * Theme setup.
 */
function dce_setup() {
	load_theme_textdomain( 'dubai-curtain-experts', get_template_directory() . '/languages' );

	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support(
		'html5',
		array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' )
	);
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 120,
			'width'       => 360,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'editor-styles' );
	add_editor_style( array( 'assets/css/editor.css', 'assets/css/pages.css' ) );

	// Image sizes used across the front page.
	add_image_size( 'dce-card', 900, 675, true );
	add_image_size( 'dce-hero', 1600, 1100, true );

	register_nav_menus(
		array(
			'primary' => __( 'Primary Menu', 'dubai-curtain-experts' ),
			'footer'  => __( 'Footer Menu', 'dubai-curtain-experts' ),
		)
	);

	$GLOBALS['content_width'] = 1400;
}
add_action( 'after_setup_theme', 'dce_setup' );

/**
 * Register widget areas: four footer columns + blog sidebar.
 */
function dce_widgets_init() {
	$footer = array(
		1 => __( 'Footer Column 1 — Brand', 'dubai-curtain-experts' ),
		2 => __( 'Footer Column 2 — Links', 'dubai-curtain-experts' ),
		3 => __( 'Footer Column 3 — Links', 'dubai-curtain-experts' ),
		4 => __( 'Footer Column 4 — Contact', 'dubai-curtain-experts' ),
	);
	foreach ( $footer as $n => $name ) {
		register_sidebar(
			array(
				'name'          => $name,
				'id'            => 'footer-' . $n,
				'description'   => __( 'Widgets render inside the dark footer grid.', 'dubai-curtain-experts' ),
				'before_widget' => '<div id="%1$s" class="dce-widget %2$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<h3 class="dce-widget-title">',
				'after_title'   => '</h3>',
			)
		);
	}
	register_sidebar(
		array(
			'name'          => __( 'Blog Sidebar', 'dubai-curtain-experts' ),
			'id'            => 'sidebar-1',
			'description'   => __( 'Shown beside posts and archives when populated.', 'dubai-curtain-experts' ),
			'before_widget' => '<section id="%1$s" class="dce-widget dce-sidebar-widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h3 class="dce-widget-title">',
			'after_title'   => '</h3>',
		)
	);
}
add_action( 'widgets_init', 'dce_widgets_init' );

/**
 * Front-end assets.
 */
function dce_assets() {
	// Fonts: Fraunces (display) + Manrope (body).
	wp_enqueue_style(
		'dce-fonts',
		'https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300..700;1,9..144,300..700&family=Manrope:wght@300..800&display=swap',
		array(),
		null
	);

	wp_enqueue_style( 'dce-base', get_stylesheet_uri(), array(), DCE_VERSION );
	wp_enqueue_style(
		'dce-theme',
		get_template_directory_uri() . '/assets/css/theme.css',
		array( 'dce-base' ),
		filemtime( get_template_directory() . '/assets/css/theme.css' )
	);
	wp_enqueue_style(
		'dce-pages',
		get_template_directory_uri() . '/assets/css/pages.css',
		array( 'dce-theme' ),
		filemtime( get_template_directory() . '/assets/css/pages.css' )
	);

	wp_enqueue_script(
		'dce-theme',
		get_template_directory_uri() . '/assets/js/theme.js',
		array(),
		filemtime( get_template_directory() . '/assets/js/theme.js' ),
		array(
			'strategy'  => 'defer',
			'in_footer' => true,
		)
	);

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'dce_assets' );

/**
 * Preconnects for Google Fonts.
 *
 * @param array  $urls          URLs.
 * @param string $relation_type Relation.
 * @return array
 */
function dce_resource_hints( $urls, $relation_type ) {
	if ( 'preconnect' === $relation_type ) {
		$urls[] = array(
			'href' => 'https://fonts.gstatic.com',
			'crossorigin',
		);
		$urls[] = 'https://fonts.googleapis.com';
	}
	return $urls;
}
add_filter( 'wp_resource_hints', 'dce_resource_hints', 10, 2 );

/**
 * Output brand colour CSS variables from the Customizer.
 */
function dce_colour_vars() {
	$ink   = get_theme_mod( 'color_ink', '#211d16' );
	$paper = get_theme_mod( 'color_paper', '#f6f1e7' );
	$brass = get_theme_mod( 'color_brass', '#a8803f' );
	$night = get_theme_mod( 'color_night', '#171310' );
	printf(
		'<style id="dce-colours">:root{--dce-ink:%1$s;--dce-paper:%2$s;--dce-brass:%3$s;--dce-night:%4$s;}</style>' . "\n",
		esc_attr( $ink ),
		esc_attr( $paper ),
		esc_attr( $brass ),
		esc_attr( $night )
	);
}
add_action( 'wp_head', 'dce_colour_vars', 20 );

/**
 * Inline SVG icon set (stroke style, Lucide-like).
 *
 * @param string $name Icon key.
 * @param array  $args Optional. size, class.
 * @return string
 */
function dce_icon( $name, $args = array() ) {
	$size  = isset( $args['size'] ) ? (int) $args['size'] : 18;
	$class = isset( $args['class'] ) ? $args['class'] : '';

	$paths = array(
		'phone'    => '<path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.91.34 1.85.57 2.81.7A2 2 0 0 1 22 16.92z"/>',
		'mail'     => '<rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>',
		'pin'      => '<path d="M20 10c0 4.99-5.54 10.19-7.4 11.79a1 1 0 0 1-1.2 0C9.54 20.19 4 14.99 4 10a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/>',
		'clock'    => '<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>',
		'arrow-up-right' => '<path d="M7 7h10v10"/><path d="M7 17 17 7"/>',
		'arrow-left' => '<path d="m12 19-7-7 7-7"/><path d="M19 12H5"/>',
		'arrow-right' => '<path d="M5 12h14"/><path d="m12 5 7 7-7 7"/>',
		'check'    => '<path d="M20 6 9 17l-5-5"/>',
		'plus'     => '<path d="M5 12h14"/><path d="M12 5v14"/>',
		'menu'     => '<line x1="4" x2="20" y1="6" y2="6"/><line x1="4" x2="20" y1="12" y2="12"/><line x1="4" x2="20" y1="18" y2="18"/>',
		'close'    => '<path d="M18 6 6 18"/><path d="m6 6 12 12"/>',
		'chevron-down' => '<path d="m6 9 6 6 6-6"/>',
		'chevron-left' => '<path d="m15 18-6-6 6-6"/>',
		'chevron-right' => '<path d="m9 18 6-6-6-6"/>',
		'quote'    => '<path d="M3 21c3 0 7-1 7-8V5c0-1.25-.76-2-2-2H4c-1.25 0-2 .75-2 1.97V11c0 1.25.75 2 2 2 1 0 1 0 1 1v1c0 1-1 2-2 2s-1 .01-1 1.03V20c0 1 0 1 1 1z"/><path d="M15 21c3 0 7-1 7-8V5c0-1.25-.75-2-2-2h-4c-1.25 0-2 .75-2 1.97V11c0 1.25.75 2 2 2h.75c0 2.25.25 4-2.75 4v3c0 1 0 1 1 1z"/>',
		'ruler'    => '<path d="M21.3 15.3a2.4 2.4 0 0 1 0 3.4l-2.6 2.6a2.4 2.4 0 0 1-3.4 0L2.7 8.7a2.4 2.4 0 0 1 0-3.4l2.6-2.6a2.4 2.4 0 0 1 3.4 0Z"/><path d="m14.5 12.5 2-2"/><path d="m11.5 9.5 2-2"/><path d="m8.5 6.5 2-2"/><path d="m17.5 15.5 2-2"/>',
		'scissors' => '<circle cx="6" cy="6" r="3"/><path d="M8.12 8.12 12 12"/><path d="M20 4 8.12 15.88"/><circle cx="6" cy="18" r="3"/><path d="M14.8 14.8 20 20"/>',
		'truck'    => '<path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"/><path d="M15 18h-5"/><path d="M20 10h1.38a1 1 0 0 1 .85.47l1.07 1.61a2 2 0 0 1 .31 1.06V17a1 1 0 0 1-1 1h-1"/><path d="M20 18h-5"/><circle cx="7.5" cy="18.5" r="2.5"/><circle cx="17.5" cy="18.5" r="2.5"/>',
		'shield'   => '<path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/><path d="m9 12 2 2 4-4"/>',
		'whatsapp' => '<path fill="currentColor" stroke="none" d="M12 2a10 10 0 0 0-8.6 15.1L2 22l5-1.3A10 10 0 1 0 12 2Zm5.3 14.1c-.2.6-1.3 1.2-1.8 1.2-.5.1-1 .2-3.3-.7-2.8-1.1-4.6-4-4.7-4.2-.1-.2-1.1-1.5-1.1-2.9s.7-2 1-2.3c.3-.3.6-.3.8-.3h.6c.2 0 .4 0 .6.5l.9 2.1c.1.2.1.4 0 .5l-.3.5-.4.5c-.1.1-.3.3-.1.6.2.3.8 1.3 1.7 2.1 1.2 1 2.1 1.4 2.4 1.5.3.1.5.1.6-.1l.9-1c.2-.3.4-.2.6-.1l2 1c.3.1.5.2.5.3.1.2.1.7-.1 1.4Z"/>',
		'sparkle'  => '<path d="M9.94 15.5a2 2 0 0 0-1.44-1.44L3 12.5l5.5-1.56A2 2 0 0 0 9.94 9.5L11.5 4l1.56 5.5a2 2 0 0 0 1.44 1.44L20 12.5l-5.5 1.56a2 2 0 0 0-1.44 1.44L11.5 21Z"/>',
	);

	if ( ! isset( $paths[ $name ] ) ) {
		return '';
	}

	return sprintf(
		'<svg xmlns="http://www.w3.org/2000/svg" width="%1$d" height="%1$d" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="dce-icon %2$s" aria-hidden="true" focusable="false">%3$s</svg>',
		$size,
		esc_attr( $class ),
		$paths[ $name ]
	);
}

/**
 * Fallback menu (used before a menu is assigned).
 */
function dce_fallback_menu() {
	echo '<ul class="dce-nav-list">';
	wp_list_pages(
		array(
			'title_li' => '',
			'depth'    => 1,
		)
	);
	echo '</ul>';
}

/**
 * Lead form handler (no plugin dependency). Targets admin-post.php?action=dce_quote.
 *
 * 1. Stores the lead (Dashboard → Leads).
 * 2. Emails it to the Customizer email (plus the site admin email).
 * 3. Sends the visitor to WhatsApp with the same details prefilled.
 */
function dce_handle_quote() {
	$back = wp_get_referer() ? wp_get_referer() : home_url( '/' );
	$back = remove_query_arg( 'quote', $back );

	if ( ! isset( $_POST['dce_quote_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['dce_quote_nonce'] ) ), 'dce_quote' ) ) {
		wp_safe_redirect( add_query_arg( 'quote', 'error', $back ) . '#contact' );
		exit;
	}

	// Honeypot — silently bin spam.
	if ( ! empty( $_POST['dce_company'] ) ) {
		wp_safe_redirect( add_query_arg( 'quote', 'success', $back ) . '#contact' );
		exit;
	}

	$field = function ( $key ) {
		return isset( $_POST[ $key ] ) ? sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing -- verified above.
	};

	$name    = $field( 'dce_name' );
	$phone   = $field( 'dce_phone' );
	$email   = isset( $_POST['dce_email'] ) ? sanitize_email( wp_unslash( $_POST['dce_email'] ) ) : '';
	$service = $field( 'dce_service' );
	$area    = $field( 'dce_area' );
	$source  = $field( 'dce_source' );
	$src_url = isset( $_POST['dce_source_url'] ) ? esc_url_raw( wp_unslash( $_POST['dce_source_url'] ) ) : '';
	$message = isset( $_POST['dce_message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['dce_message'] ) ) : '';

	if ( '' === $name || '' === $phone ) {
		wp_safe_redirect( add_query_arg( 'quote', 'error', $back ) . '#contact' );
		exit;
	}

	$leads   = (array) get_option( 'dce_leads', array() );
	$leads[] = array(
		'time'    => current_time( 'mysql' ),
		'name'    => $name,
		'phone'   => $phone,
		'email'   => $email,
		'service' => $service,
		'area'    => $area,
		'message' => $message,
		'source'  => $source,
	);
	update_option( 'dce_leads', array_slice( $leads, -500 ), false );

	$subject = sprintf(
		/* translators: 1: sender name, 2: page */
		__( 'New website lead: %1$s (%2$s)', 'dubai-curtain-experts' ),
		$name,
		$source ? $source : __( 'website', 'dubai-curtain-experts' )
	);
	$body    = "Name: {$name}\nPhone: {$phone}\nEmail: {$email}\nInterest: {$service}\nArea: {$area}\nPage: {$source} {$src_url}\n\nMessage:\n{$message}\n";
	$to      = array_unique( array_filter( array( dce_mod( 'email' ), get_option( 'admin_email' ) ) ) );
	$headers = $email ? array( 'Reply-To: ' . $name . ' <' . $email . '>' ) : array();
	wp_mail( $to, $subject, $body, $headers );

	$wa_text = sprintf(
		"Hello %s, I'd like a free home visit.\nName: %s\nPhone: %s\nArea: %s\nInterest: %s\nDetails: %s\n(Sent from: %s)",
		dce_mod( 'brand_name' ),
		$name,
		$phone,
		$area,
		$service,
		$message,
		$source ? $source : home_url( '/' )
	);

	// External redirect to WhatsApp (wa.me is not a local host, so use wp_redirect).
	wp_redirect( dce_wa_url( $wa_text ) ); // phpcs:ignore WordPress.Security.SafeRedirect.wp_redirect_wp_redirect
	exit;
}
add_action( 'admin_post_dce_quote', 'dce_handle_quote' );
add_action( 'admin_post_nopriv_dce_quote', 'dce_handle_quote' );

/**
 * Suggest a static front page when the front page is not configured.
 */
function dce_admin_notice_frontpage() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	if ( 'page' === get_option( 'show_on_front' ) ) {
		return;
	}
	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	if ( $screen && in_array( $screen->id, array( 'dashboard', 'themes' ), true ) ) {
		echo '<div class="notice notice-info is-dismissible"><p>';
		echo wp_kses_post(
			sprintf(
				/* translators: %s: link to Reading settings */
				__( 'Dubai Curtain Experts: create a page named “Home”, then set it under %s so the editable front page renders.', 'dubai-curtain-experts' ),
				'<a href="' . esc_url( admin_url( 'options-reading.php' ) ) . '">' . esc_html__( 'Settings → Reading → A static page', 'dubai-curtain-experts' ) . '</a>'
			)
		);
		echo '</p></div>';
	}
}
add_action( 'admin_notices', 'dce_admin_notice_frontpage' );
