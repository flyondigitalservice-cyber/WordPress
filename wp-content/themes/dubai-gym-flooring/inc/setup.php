<?php
/**
 * Theme supports, menus, assets and admin notices.
 *
 * @package DGF
 */

defined( 'ABSPATH' ) || exit;

add_action( 'after_setup_theme', 'dgf_setup' );
/**
 * Register theme features.
 */
function dgf_setup() {
	load_theme_textdomain( 'dgf', DGF_DIR . '/languages' );

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
			'height'      => 96,
			'width'       => 440,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);
	add_theme_support(
		'editor-color-palette',
		array(
			array( 'name' => __( 'Graphite', 'dgf' ), 'slug' => 'graphite', 'color' => '#111311' ),
			array( 'name' => __( 'Volt', 'dgf' ), 'slug' => 'volt', 'color' => '#C8F031' ),
			array( 'name' => __( 'Chalk', 'dgf' ), 'slug' => 'chalk', 'color' => '#F4F2EC' ),
			array( 'name' => __( 'Steel', 'dgf' ), 'slug' => 'steel', 'color' => '#6B6F68' ),
			array( 'name' => __( 'White', 'dgf' ), 'slug' => 'white', 'color' => '#FFFFFF' ),
		)
	);
	add_editor_style( array( dgf_fonts_url(), 'assets/css/editor.css' ) );

	add_image_size( 'dgf-card', 720, 540, true );
	add_image_size( 'dgf-hero', 1920, 1080, true );

	register_nav_menus(
		array(
			'primary'         => __( 'Primary (header)', 'dgf' ),
			'footer_products' => __( 'Footer: Products', 'dgf' ),
			'footer_areas'    => __( 'Footer: Areas', 'dgf' ),
			'footer_company'  => __( 'Footer: Company', 'dgf' ),
			'legal'           => __( 'Footer: Legal (bottom bar)', 'dgf' ),
		)
	);

	// Excerpt = hero sub-heading on pages; edit it in the page's Excerpt panel.
	add_post_type_support( 'page', 'excerpt' );
}

/**
 * Google Fonts stylesheet URL.
 *
 * @return string
 */
function dgf_fonts_url() {
	return 'https://fonts.googleapis.com/css2?family=Big+Shoulders+Display:wght@600;700;800;900&family=Instrument+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400&display=swap';
}

add_action( 'wp_enqueue_scripts', 'dgf_assets' );
/**
 * Front-end assets.
 */
function dgf_assets() {
	$css_ver = DGF_VERSION . '.' . filemtime( DGF_DIR . '/assets/css/main.css' );
	$js_ver  = DGF_VERSION . '.' . filemtime( DGF_DIR . '/assets/js/main.js' );

	wp_enqueue_style( 'dgf-fonts', dgf_fonts_url(), array(), null ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
	wp_enqueue_style( 'dgf-main', DGF_URI . '/assets/css/main.css', array(), $css_ver );
	wp_enqueue_script( 'dgf-main', DGF_URI . '/assets/js/main.js', array(), $js_ver, array( 'strategy' => 'defer', 'in_footer' => true ) );
	wp_localize_script(
		'dgf-main',
		'DGF',
		array(
			'leadEndpoint' => esc_url_raw( rest_url( 'dgf/v1/lead' ) ),
			'waNumber'     => dgf_wa_number(),
			'brand'        => dgf_opt( 'brand_name' ),
			'i18n'         => array(
				'sending' => __( 'Opening WhatsApp…', 'dgf' ),
				'error'   => __( 'Please fill in your name and phone number.', 'dgf' ),
				'saved'   => __( 'Thank you! Your enquiry was received — we will contact you shortly.', 'dgf' ),
			),
		)
	);

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}

add_filter( 'wp_resource_hints', 'dgf_resource_hints', 10, 2 );
/**
 * Preconnect to Google Fonts.
 *
 * @param array  $urls          URLs.
 * @param string $relation_type Relation.
 * @return array
 */
function dgf_resource_hints( $urls, $relation_type ) {
	if ( 'preconnect' === $relation_type ) {
		$urls[] = array( 'href' => 'https://fonts.gstatic.com', 'crossorigin' );
		$urls[] = 'https://fonts.googleapis.com';
	}
	return $urls;
}

add_filter( 'body_class', 'dgf_body_class' );
/**
 * Page-type body classes.
 *
 * @param array $classes Classes.
 * @return array
 */
function dgf_body_class( $classes ) {
	if ( is_page() ) {
		$type = get_post_meta( get_queried_object_id(), '_dgf_type', true );
		if ( $type ) {
			$classes[] = 'dgf-type-' . sanitize_html_class( $type );
		}
	}
	return $classes;
}

add_filter( 'excerpt_more', static function () {
	return '…';
} );

// Remove the emoji detection script (saves a request, no visual change).
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );

add_action( 'admin_notices', 'dgf_admin_notices' );
/**
 * Remind admins to set the WhatsApp number — without it lead buttons fall back to the Contact page.
 */
function dgf_admin_notices() {
	if ( ! current_user_can( 'edit_theme_options' ) || '' !== dgf_wa_number() ) {
		return;
	}
	$url = admin_url( 'customize.php?autofocus[section]=dgf_contact' );
	printf(
		'<div class="notice notice-warning"><p><strong>%1$s</strong> %2$s <a href="%3$s">%4$s</a></p></div>',
		esc_html__( 'Dubai Gym Flooring:', 'dgf' ),
		esc_html__( 'Add your WhatsApp number so every quote button opens a WhatsApp chat. Until then, buttons point to the Contact page (leads are still saved under Leads).', 'dgf' ),
		esc_url( $url ),
		esc_html__( 'Set WhatsApp number →', 'dgf' )
	);
}

/**
 * Menu fallback used before menus are assigned: top-level pages from the installer map.
 */
function dgf_menu_fallback() {
	$items = array(
		'gym-flooring-products' => __( 'Products', 'dgf' ),
		'services'              => __( 'Services', 'dgf' ),
		'areas-we-serve'        => __( 'Areas', 'dgf' ),
		'catalogues'            => __( 'Catalogues', 'dgf' ),
		'projects'              => __( 'Projects', 'dgf' ),
		'about'                 => __( 'About', 'dgf' ),
		'contact'               => __( 'Contact', 'dgf' ),
	);
	echo '<ul class="dgf-menu">';
	foreach ( $items as $slug => $label ) {
		printf( '<li class="menu-item"><a href="%s">%s</a></li>', esc_url( dgf_page_url( $slug ) ), esc_html( $label ) );
	}
	echo '</ul>';
}

add_action( 'init', 'dgf_register_hero_meta' );
/**
 * Hero fields (editable in the "Page hero" box and via REST).
 */
function dgf_register_hero_meta() {
	foreach ( array( '_dgf_hero_title', '_dgf_hide_hero', '_dgf_type', '_dgf_place', '_dgf_emirate' ) as $key ) {
		register_post_meta(
			'page',
			$key,
			array(
				'type'          => 'string',
				'single'        => true,
				'show_in_rest'  => true,
				'auth_callback' => static function () {
					return current_user_can( 'edit_pages' );
				},
			)
		);
	}
}

add_action( 'add_meta_boxes_page', 'dgf_hero_meta_box' );
/**
 * "Page hero" box.
 */
function dgf_hero_meta_box() {
	add_meta_box( 'dgf_hero', __( 'Page hero (top banner)', 'dgf' ), 'dgf_hero_meta_box_html', 'page', 'side', 'high' );
}

/**
 * Hero box markup.
 *
 * @param WP_Post $post Page.
 */
function dgf_hero_meta_box_html( $post ) {
	wp_nonce_field( 'dgf_hero_save', 'dgf_hero_nonce' );
	?>
	<p><label for="dgf_hero_title"><strong><?php esc_html_e( 'Hero heading (H1)', 'dgf' ); ?></strong></label>
	<textarea id="dgf_hero_title" name="dgf_hero_title" class="widefat" rows="2" placeholder="<?php echo esc_attr( $post->post_title ); ?>"><?php echo esc_textarea( get_post_meta( $post->ID, '_dgf_hero_title', true ) ); ?></textarea></p>
	<p class="description"><?php esc_html_e( 'Empty = page title. Sub-heading = the page Excerpt. Background = the Featured image.', 'dgf' ); ?></p>
	<p><label><input type="checkbox" name="dgf_hide_hero" value="1" <?php checked( '1', get_post_meta( $post->ID, '_dgf_hide_hero', true ) ); ?>> <?php esc_html_e( 'Hide the hero on this page', 'dgf' ); ?></label></p>
	<?php
}

add_action( 'save_post_page', 'dgf_hero_save', 10, 2 );
/**
 * Save hero box.
 *
 * @param int     $post_id Page ID.
 * @param WP_Post $post    Page.
 */
function dgf_hero_save( $post_id, $post ) {
	if ( ! isset( $_POST['dgf_hero_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['dgf_hero_nonce'] ), 'dgf_hero_save' ) ) {
		return;
	}
	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! current_user_can( 'edit_post', $post_id ) || wp_is_post_revision( $post ) ) {
		return;
	}
	update_post_meta( $post_id, '_dgf_hero_title', isset( $_POST['dgf_hero_title'] ) ? sanitize_textarea_field( wp_unslash( $_POST['dgf_hero_title'] ) ) : '' );
	update_post_meta( $post_id, '_dgf_hide_hero', empty( $_POST['dgf_hide_hero'] ) ? '' : '1' );
}

add_action( 'wp_head', 'dgf_favicon_fallback', 5 );
/**
 * Brand favicon until a Site Icon is uploaded (Customize → Site Identity).
 */
function dgf_favicon_fallback() {
	if ( ! has_site_icon() ) {
		$svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48"><rect x="1" y="1" width="46" height="46" rx="10" fill="#c8f031"/><path d="M11 13h12a11 11 0 0 1 0 22H11z" fill="none" stroke="#111311" stroke-width="4.5" stroke-linejoin="round"/><path d="M29 24h8M33 18v12" stroke="#111311" stroke-width="4" stroke-linecap="round"/></svg>';
		printf( '<link rel="icon" href="%s" type="image/svg+xml">' . "\n", esc_attr( 'data:image/svg+xml,' . rawurlencode( $svg ) ) );
	}
}

add_filter( 'post_thumbnail_id', 'dgf_inherit_parent_thumbnail', 10, 2 );
/**
 * Front end: a page without its own featured image borrows its parent page's
 * image (e.g. a curtain page uses the Curtains category photo). Setting a
 * featured image on the page itself always wins.
 *
 * @param int|false        $thumbnail_id Thumbnail ID.
 * @param int|WP_Post|null $post         Post.
 * @return int|false
 */
function dgf_inherit_parent_thumbnail( $thumbnail_id, $post ) {
	if ( $thumbnail_id || is_admin() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
		return $thumbnail_id;
	}
	$post = get_post( $post );
	if ( $post && 'page' === $post->post_type && $post->post_parent ) {
		$parent_thumb = (int) get_post_meta( $post->post_parent, '_thumbnail_id', true );
		if ( $parent_thumb ) {
			return $parent_thumb;
		}
	}
	return $thumbnail_id;
}
