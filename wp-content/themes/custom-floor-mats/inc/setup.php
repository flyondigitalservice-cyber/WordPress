<?php
/**
 * Theme supports, menus, assets and admin notices.
 *
 * @package CFM
 */

defined( 'ABSPATH' ) || exit;

add_action( 'after_setup_theme', 'cfm_setup' );
/**
 * Register theme features.
 */
function cfm_setup() {
	load_theme_textdomain( 'cfm', CFM_DIR . '/languages' );

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
			array( 'name' => __( 'Espresso', 'cfm' ), 'slug' => 'espresso', 'color' => '#17130E' ),
			array( 'name' => __( 'Saffron', 'cfm' ), 'slug' => 'saffron', 'color' => '#F2A93B' ),
			array( 'name' => __( 'Jute', 'cfm' ), 'slug' => 'jute', 'color' => '#F2EBDF' ),
			array( 'name' => __( 'Clay', 'cfm' ), 'slug' => 'clay', 'color' => '#C4643C' ),
			array( 'name' => __( 'White', 'cfm' ), 'slug' => 'white', 'color' => '#FFFFFF' ),
		)
	);
	add_editor_style( array( cfm_fonts_url(), 'assets/css/editor.css' ) );

	add_image_size( 'cfm-card', 720, 540, true );
	add_image_size( 'cfm-hero', 1920, 1080, true );

	register_nav_menus(
		array(
			'primary'         => __( 'Primary (header)', 'cfm' ),
			'footer_products' => __( 'Footer: Products', 'cfm' ),
			'footer_areas'    => __( 'Footer: Areas', 'cfm' ),
			'footer_company'  => __( 'Footer: Company', 'cfm' ),
			'legal'           => __( 'Footer: Legal (bottom bar)', 'cfm' ),
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
function cfm_fonts_url() {
	return 'https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400..700;1,9..144,400..600&family=Instrument+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400&display=swap';
}

add_action( 'wp_enqueue_scripts', 'cfm_assets' );
/**
 * Front-end assets.
 */
function cfm_assets() {
	$css_ver = CFM_VERSION . '.' . filemtime( CFM_DIR . '/assets/css/main.css' );
	$js_ver  = CFM_VERSION . '.' . filemtime( CFM_DIR . '/assets/js/main.js' );

	wp_enqueue_style( 'cfm-fonts', cfm_fonts_url(), array(), null ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
	wp_enqueue_style( 'cfm-main', CFM_URI . '/assets/css/main.css', array(), $css_ver );
	wp_enqueue_script( 'cfm-main', CFM_URI . '/assets/js/main.js', array(), $js_ver, array( 'strategy' => 'defer', 'in_footer' => true ) );
	wp_localize_script(
		'cfm-main',
		'CFM',
		array(
			'leadEndpoint' => esc_url_raw( rest_url( 'cfm/v1/lead' ) ),
			'waNumber'     => cfm_wa_number(),
			'brand'        => cfm_opt( 'brand_name' ),
			'i18n'         => array(
				'sending' => __( 'Opening WhatsApp…', 'cfm' ),
				'error'   => __( 'Please fill in your name and phone number.', 'cfm' ),
				'saved'   => __( 'Thank you! Your enquiry was received — we will contact you shortly.', 'cfm' ),
			),
		)
	);

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}

add_filter( 'wp_resource_hints', 'cfm_resource_hints', 10, 2 );
/**
 * Preconnect to Google Fonts.
 *
 * @param array  $urls          URLs.
 * @param string $relation_type Relation.
 * @return array
 */
function cfm_resource_hints( $urls, $relation_type ) {
	if ( 'preconnect' === $relation_type ) {
		$urls[] = array( 'href' => 'https://fonts.gstatic.com', 'crossorigin' );
		$urls[] = 'https://fonts.googleapis.com';
	}
	return $urls;
}

add_filter( 'body_class', 'cfm_body_class' );
/**
 * Page-type body classes.
 *
 * @param array $classes Classes.
 * @return array
 */
function cfm_body_class( $classes ) {
	if ( is_page() ) {
		$type = get_post_meta( get_queried_object_id(), '_cfm_type', true );
		if ( $type ) {
			$classes[] = 'cfm-type-' . sanitize_html_class( $type );
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

add_action( 'admin_notices', 'cfm_admin_notices' );
/**
 * Remind admins to set the WhatsApp number — without it lead buttons fall back to the Contact page.
 */
function cfm_admin_notices() {
	if ( ! current_user_can( 'edit_theme_options' ) || '' !== cfm_wa_number() ) {
		return;
	}
	$url = admin_url( 'customize.php?autofocus[section]=cfm_contact' );
	printf(
		'<div class="notice notice-warning"><p><strong>%1$s</strong> %2$s <a href="%3$s">%4$s</a></p></div>',
		esc_html__( 'Custom Floor Mats:', 'cfm' ),
		esc_html__( 'Add your WhatsApp number so every quote button opens a WhatsApp chat. Until then, buttons point to the Contact page (leads are still saved under Leads).', 'cfm' ),
		esc_url( $url ),
		esc_html__( 'Set WhatsApp number →', 'cfm' )
	);
}

/**
 * Menu fallback used before menus are assigned: top-level pages from the installer map.
 */
function cfm_menu_fallback() {
	$items = array(
		'floor-mats'            => __( 'Mats', 'cfm' ),
		'services'              => __( 'Services', 'cfm' ),
		'areas-we-serve'        => __( 'Areas', 'cfm' ),
		'catalogues'            => __( 'Catalogues', 'cfm' ),
		'projects'              => __( 'Projects', 'cfm' ),
		'about'                 => __( 'About', 'cfm' ),
		'contact'               => __( 'Contact', 'cfm' ),
	);
	echo '<ul class="cfm-menu">';
	foreach ( $items as $slug => $label ) {
		printf( '<li class="menu-item"><a href="%s">%s</a></li>', esc_url( cfm_page_url( $slug ) ), esc_html( $label ) );
	}
	echo '</ul>';
}

add_action( 'init', 'cfm_register_hero_meta' );
/**
 * Hero fields (editable in the "Page hero" box and via REST).
 */
function cfm_register_hero_meta() {
	foreach ( array( '_cfm_hero_title', '_cfm_hide_hero', '_cfm_type', '_cfm_place', '_cfm_emirate' ) as $key ) {
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

add_action( 'add_meta_boxes_page', 'cfm_hero_meta_box' );
/**
 * "Page hero" box.
 */
function cfm_hero_meta_box() {
	add_meta_box( 'cfm_hero', __( 'Page hero (top banner)', 'cfm' ), 'cfm_hero_meta_box_html', 'page', 'side', 'high' );
}

/**
 * Hero box markup.
 *
 * @param WP_Post $post Page.
 */
function cfm_hero_meta_box_html( $post ) {
	wp_nonce_field( 'cfm_hero_save', 'cfm_hero_nonce' );
	?>
	<p><label for="cfm_hero_title"><strong><?php esc_html_e( 'Hero heading (H1)', 'cfm' ); ?></strong></label>
	<textarea id="cfm_hero_title" name="cfm_hero_title" class="widefat" rows="2" placeholder="<?php echo esc_attr( $post->post_title ); ?>"><?php echo esc_textarea( get_post_meta( $post->ID, '_cfm_hero_title', true ) ); ?></textarea></p>
	<p class="description"><?php esc_html_e( 'Empty = page title. Sub-heading = the page Excerpt. Background = the Featured image.', 'cfm' ); ?></p>
	<p><label><input type="checkbox" name="cfm_hide_hero" value="1" <?php checked( '1', get_post_meta( $post->ID, '_cfm_hide_hero', true ) ); ?>> <?php esc_html_e( 'Hide the hero on this page', 'cfm' ); ?></label></p>
	<?php
}

add_action( 'save_post_page', 'cfm_hero_save', 10, 2 );
/**
 * Save hero box.
 *
 * @param int     $post_id Page ID.
 * @param WP_Post $post    Page.
 */
function cfm_hero_save( $post_id, $post ) {
	if ( ! isset( $_POST['cfm_hero_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['cfm_hero_nonce'] ), 'cfm_hero_save' ) ) {
		return;
	}
	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! current_user_can( 'edit_post', $post_id ) || wp_is_post_revision( $post ) ) {
		return;
	}
	update_post_meta( $post_id, '_cfm_hero_title', isset( $_POST['cfm_hero_title'] ) ? sanitize_textarea_field( wp_unslash( $_POST['cfm_hero_title'] ) ) : '' );
	update_post_meta( $post_id, '_cfm_hide_hero', empty( $_POST['cfm_hide_hero'] ) ? '' : '1' );
}

add_action( 'wp_head', 'cfm_favicon_fallback', 5 );
/**
 * Brand favicon until a Site Icon is uploaded (Customize → Site Identity).
 */
function cfm_favicon_fallback() {
	if ( ! has_site_icon() ) {
		$svg = (string) file_get_contents( CFM_DIR . '/assets/img/favicon.svg' );
		printf( '<link rel="icon" href="%s" type="image/svg+xml">' . "\n", esc_attr( 'data:image/svg+xml,' . rawurlencode( $svg ) ) );
	}
}

add_filter( 'post_thumbnail_id', 'cfm_inherit_parent_thumbnail', 10, 2 );
/**
 * Front end: a page without its own featured image borrows its parent page's
 * image (e.g. a curtain page uses the Curtains category photo). Setting a
 * featured image on the page itself always wins.
 *
 * @param int|false        $thumbnail_id Thumbnail ID.
 * @param int|WP_Post|null $post         Post.
 * @return int|false
 */
function cfm_inherit_parent_thumbnail( $thumbnail_id, $post ) {
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
