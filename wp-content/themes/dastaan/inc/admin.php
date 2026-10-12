<?php
/**
 * Admin: welcome page, setup checklist, notices.
 *
 * @package Dastaan
 */

defined( 'ABSPATH' ) || exit;

/**
 * Menu.
 */
function dastaan_admin_menu() {
	add_menu_page(
		__( 'Dastaan Theme', 'dastaan' ),
		__( 'Dastaan', 'dastaan' ),
		'edit_theme_options',
		'dastaan-welcome',
		'dastaan_welcome_page',
		'dashicons-art',
		59
	);
	add_submenu_page( 'dastaan-welcome', __( 'Setup & Help', 'dastaan' ), __( 'Setup & Help', 'dastaan' ), 'edit_theme_options', 'dastaan-welcome', 'dastaan_welcome_page' );
}
add_action( 'admin_menu', 'dastaan_admin_menu' );

/**
 * Setup checklist state.
 *
 * @return array
 */
function dastaan_setup_checks() {
	$checks = array();
	$checks[] = array( class_exists( 'WooCommerce' ), __( 'WooCommerce is active', 'dastaan' ), admin_url( 'plugin-install.php?s=woocommerce&tab=search&type=term' ) );
	$checks[] = array( (bool) get_theme_mod( 'custom_logo' ), __( 'Logo uploaded (Customizer → Site Identity)', 'dastaan' ), admin_url( 'customize.php?autofocus[section]=title_tagline' ) );
	$checks[] = array( has_nav_menu( 'primary' ), __( 'Primary menu assigned', 'dastaan' ), admin_url( 'nav-menus.php' ) );
	$checks[] = array( has_nav_menu( 'footer_help' ), __( 'Footer menus assigned', 'dastaan' ), admin_url( 'nav-menus.php' ) );
	$checks[] = array( 'page' === get_option( 'show_on_front' ), __( 'Static front page set (Settings → Reading). The theme home layout is used automatically.', 'dastaan' ), admin_url( 'options-reading.php' ) );
	$checks[] = array( (bool) get_option( 'permalink_structure' ), __( 'Pretty permalinks enabled (needed for /llms.txt)', 'dastaan' ), admin_url( 'options-permalink.php' ) );
	$checks[] = array( (bool) dastaan_option( 'fb_pixel_id' ), __( 'Meta Pixel ID added', 'dastaan' ), admin_url( 'customize.php?autofocus[section]=dastaan_tracking' ) );
	$checks[] = array( (bool) dastaan_option( 'clarity_id' ), __( 'Microsoft Clarity ID added', 'dastaan' ), admin_url( 'customize.php?autofocus[section]=dastaan_tracking' ) );
	if ( class_exists( 'WooCommerce' ) ) {
		$checks[] = array( dastaan_is_classic_checkout(), __( 'Checkout page uses the Dastaan premium checkout ([woocommerce_checkout] shortcode)', 'dastaan' ), admin_url( 'post.php?post=' . wc_get_page_id( 'checkout' ) . '&action=edit' ) );
		$checks[] = array( 'yes' === get_option( 'woocommerce_feature_product_gallery_videos_enabled' ), __( 'WooCommerce "Product gallery videos" feature enabled (WooCommerce → Settings → Advanced → Features) — optional, the theme video field works without it', 'dastaan' ), admin_url( 'admin.php?page=wc-settings&tab=advanced&section=features' ) );
		$checks[] = array( 'INR' === get_woocommerce_currency(), __( 'Currency set to INR', 'dastaan' ), admin_url( 'admin.php?page=wc-settings' ) );
	}

	return $checks;
}

/**
 * Is the checkout page shortcode-based?
 *
 * @return bool
 */
function dastaan_is_classic_checkout() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return false;
	}
	$page = get_post( wc_get_page_id( 'checkout' ) );

	return $page && has_shortcode( $page->post_content, 'woocommerce_checkout' );
}

/**
 * Welcome page.
 */
function dastaan_welcome_page() {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}
	if ( isset( $_POST['dastaan_convert_checkout'] ) && check_admin_referer( 'dastaan_convert_checkout' ) && class_exists( 'WooCommerce' ) ) {
		$cart_id = wc_get_page_id( 'cart' );
		$co_id   = wc_get_page_id( 'checkout' );
		if ( $cart_id > 0 ) {
			wp_update_post( array( 'ID' => $cart_id, 'post_content' => '<!-- wp:shortcode -->[woocommerce_cart]<!-- /wp:shortcode -->' ) );
		}
		if ( $co_id > 0 ) {
			wp_update_post( array( 'ID' => $co_id, 'post_content' => '<!-- wp:shortcode -->[woocommerce_checkout]<!-- /wp:shortcode -->' ) );
		}
		echo '<div class="notice notice-success"><p>' . esc_html__( 'Cart and Checkout pages now use the Dastaan premium templates.', 'dastaan' ) . '</p></div>';
	}
	$checks = dastaan_setup_checks();
	?>
	<div class="wrap dastaan-welcome">
		<h1 style="font-weight:600"><?php esc_html_e( 'Dastaan by Artisans — Theme Setup', 'dastaan' ); ?></h1>
		<p style="max-width:720px;font-size:14px"><?php esc_html_e( 'Everything on the storefront is editable from the Customizer. Use this checklist once, then manage daily content from Products, Menus and the Customizer.', 'dastaan' ); ?></p>

		<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(320px,1fr));gap:20px;margin-top:20px;max-width:1200px">
			<div class="card" style="max-width:none">
				<h2 class="title"><?php esc_html_e( 'Setup checklist', 'dastaan' ); ?></h2>
				<ul style="margin:0">
					<?php foreach ( $checks as $c ) : ?>
						<li style="display:flex;gap:10px;align-items:flex-start;padding:7px 0;border-bottom:1px solid #f0f0f1">
							<span class="dashicons <?php echo $c[0] ? 'dashicons-yes-alt' : 'dashicons-warning'; ?>" style="color:<?php echo $c[0] ? '#2e6b4f' : '#b8933e'; ?>"></span>
							<span style="flex:1"><?php echo esc_html( $c[1] ); ?></span>
							<?php if ( ! $c[0] ) : ?><a class="button button-small" href="<?php echo esc_url( $c[2] ); ?>"><?php esc_html_e( 'Fix', 'dastaan' ); ?></a><?php endif; ?>
						</li>
					<?php endforeach; ?>
				</ul>
				<?php if ( class_exists( 'WooCommerce' ) && ! dastaan_is_classic_checkout() ) : ?>
					<form method="post" style="margin-top:14px">
						<?php wp_nonce_field( 'dastaan_convert_checkout' ); ?>
						<button class="button button-primary" name="dastaan_convert_checkout" value="1"><?php esc_html_e( 'Switch Cart & Checkout to Dastaan premium templates', 'dastaan' ); ?></button>
						<p class="description"><?php esc_html_e( 'Replaces the block-based cart/checkout with the shortcode versions the theme styles (two-column checkout, sticky summary, trust badges, WhatsApp help).', 'dastaan' ); ?></p>
					</form>
				<?php endif; ?>
			</div>

			<div class="card" style="max-width:none">
				<h2 class="title"><?php esc_html_e( 'Quick links', 'dastaan' ); ?></h2>
				<p>
					<a class="button button-primary" href="<?php echo esc_url( admin_url( 'customize.php?autofocus[panel]=dastaan_panel' ) ); ?>"><?php esc_html_e( 'Open Theme Options', 'dastaan' ); ?></a>
					<a class="button" href="<?php echo esc_url( admin_url( 'nav-menus.php' ) ); ?>"><?php esc_html_e( 'Menus', 'dastaan' ); ?></a>
					<a class="button" href="<?php echo esc_url( admin_url( 'edit.php?post_type=dastaan_subscriber' ) ); ?>"><?php esc_html_e( 'Subscribers', 'dastaan' ); ?></a>
					<?php if ( class_exists( 'WooCommerce' ) ) : ?>
						<a class="button" href="<?php echo esc_url( admin_url( 'edit-tags.php?taxonomy=product_cat&post_type=product' ) ); ?>"><?php esc_html_e( 'Category images', 'dastaan' ); ?></a>
					<?php endif; ?>
				</p>
				<h3><?php esc_html_e( 'Home page sections (in order)', 'dastaan' ); ?></h3>
				<ol style="margin-left:18px">
					<li><?php esc_html_e( 'Announcement bar → Header (logo, search, account, wishlist, bag)', 'dastaan' ); ?></li>
					<li><?php esc_html_e( 'Category circles (from product categories — add thumbnails under Products → Categories)', 'dastaan' ); ?></li>
					<li><?php esc_html_e( 'Hero slider (up to 4 slides)', 'dastaan' ); ?></li>
					<li><?php esc_html_e( 'USP bar → New in → Offer banners → Bestsellers', 'dastaan' ); ?></li>
					<li><?php esc_html_e( 'Craft showcase → Reels → Testimonials → Behind the scenes', 'dastaan' ); ?></li>
					<li><?php esc_html_e( 'Instagram → Studio strip → SEO text + FAQ → Newsletter → Footer', 'dastaan' ); ?></li>
				</ol>
				<h3><?php esc_html_e( 'Product videos', 'dastaan' ); ?></h3>
				<p><?php esc_html_e( 'Edit any product → "Product Video" box in the sidebar → upload an MP4 or paste a YouTube/Vimeo/Instagram link and optionally a poster image. It appears inside the gallery with a play button, in the lightbox and as VideoObject schema for Google.', 'dastaan' ); ?></p>
				<h3><?php esc_html_e( 'Shortcodes', 'dastaan' ); ?></h3>
				<code>[dastaan_reels] [dastaan_instagram] [dastaan_usp] [dastaan_banners] [dastaan_testimonials] [dastaan_faq] [dastaan_products source="bestselling" count="8"] [dastaan_whatsapp text="Chat with us"]</code>
			</div>
		</div>
	</div>
	<?php
}

/**
 * Notices.
 */
function dastaan_admin_notices() {
	if ( ! current_user_can( 'activate_plugins' ) ) {
		return;
	}
	$screen = get_current_screen();
	if ( ! class_exists( 'WooCommerce' ) ) {
		printf(
			'<div class="notice notice-warning"><p><strong>%s</strong> %s <a href="%s">%s</a></p></div>',
			esc_html__( 'Dastaan theme:', 'dastaan' ),
			esc_html__( 'WooCommerce is required for the shop, product pages and checkout.', 'dastaan' ),
			esc_url( admin_url( 'plugin-install.php?s=woocommerce&tab=search&type=term' ) ),
			esc_html__( 'Install WooCommerce', 'dastaan' )
		);
	} elseif ( $screen && 'toplevel_page_dastaan-welcome' !== $screen->id && ! dastaan_is_classic_checkout() && ! get_user_meta( get_current_user_id(), 'dastaan_dismiss_checkout_notice', true ) ) {
		printf(
			'<div class="notice notice-info is-dismissible"><p><strong>%s</strong> %s <a href="%s">%s</a></p></div>',
			esc_html__( 'Dastaan theme:', 'dastaan' ),
			esc_html__( 'Your checkout is still the default block layout. Switch to the premium Dastaan checkout in one click.', 'dastaan' ),
			esc_url( admin_url( 'admin.php?page=dastaan-welcome' ) ),
			esc_html__( 'Open setup', 'dastaan' )
		);
	}
}
add_action( 'admin_notices', 'dastaan_admin_notices' );

/**
 * Admin bar shortcut.
 *
 * @param WP_Admin_Bar $bar Bar.
 */
function dastaan_admin_bar( $bar ) {
	if ( ! current_user_can( 'edit_theme_options' ) || is_admin() ) {
		return;
	}
	$bar->add_node(
		array(
			'id'    => 'dastaan-options',
			'title' => __( 'Edit storefront', 'dastaan' ),
			'href'  => admin_url( 'customize.php?autofocus[panel]=dastaan_panel&url=' . rawurlencode( home_url( add_query_arg( array() ) ) ) ),
		)
	);
}
add_action( 'admin_bar_menu', 'dastaan_admin_bar', 90 );

/**
 * Declare WooCommerce feature compatibility (HPOS, cart/checkout blocks awareness).
 */
add_action(
	'before_woocommerce_init',
	function () {
		if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', DASTAAN_DIR . 'functions.php', true );
		}
	}
);
