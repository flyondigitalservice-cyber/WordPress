<?php
/**
 * Header.
 *
 * @package Dastaan
 */

defined( 'ABSPATH' ) || exit;

$dastaan_checkout_focus = dastaan_has_wc() && is_checkout() && ! is_order_received_page();
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
	<meta name="theme-color" content="<?php echo esc_attr( dastaan_option( 'color_bg' ) ); ?>">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link screen-reader-text" href="#main"><?php esc_html_e( 'Skip to content', 'dastaan' ); ?></a>
<div id="page" class="site">

<?php if ( $dastaan_checkout_focus ) : ?>
	<header class="checkout-header">
		<div class="container checkout-header__inner">
			<a class="checkout-header__back" href="<?php echo esc_url( wc_get_cart_url() ); ?>"><?php dastaan_the_icon( 'chevron-left', array( 'size' => 18 ) ); ?><span><?php esc_html_e( 'Back to bag', 'dastaan' ); ?></span></a>
			<div class="site-branding site-branding--center"><?php get_template_part( 'template-parts/header/logo' ); ?></div>
			<span class="checkout-header__secure"><?php dastaan_the_icon( 'lock', array( 'size' => 16 ) ); ?><span><?php esc_html_e( 'Secure checkout', 'dastaan' ); ?></span></span>
		</div>
	</header>
<?php else : ?>
	<?php do_action( 'dastaan_before_header' ); ?>
	<?php get_template_part( 'template-parts/header/announcement' ); ?>

	<header id="masthead" class="site-header" data-sticky="<?php echo dastaan_option( 'header_sticky' ) ? '1' : '0'; ?>">
		<div class="container header-main">
			<button class="icon-btn hamburger" type="button" aria-controls="mobile-menu" aria-expanded="false" aria-label="<?php esc_attr_e( 'Open menu', 'dastaan' ); ?>">
				<?php dastaan_the_icon( 'menu', array( 'size' => 24 ) ); ?>
			</button>

			<div class="site-branding"><?php get_template_part( 'template-parts/header/logo' ); ?></div>

			<nav class="primary-nav" aria-label="<?php esc_attr_e( 'Primary', 'dastaan' ); ?>">
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'primary',
						'container'      => false,
						'menu_class'     => 'menu',
						'depth'          => 3,
						'walker'         => new Dastaan_Walker_Nav(),
						'fallback_cb'    => 'dastaan_menu_fallback',
					)
				);
				?>
			</nav>

			<div class="header-actions">
				<?php if ( dastaan_option( 'header_promo_text' ) && dastaan_whatsapp_url() ) : ?>
					<a class="header-promo" href="<?php echo esc_url( dastaan_whatsapp_url() ); ?>" target="_blank" rel="noopener"><?php dastaan_the_icon( 'whatsapp', array( 'size' => 16 ) ); ?><span><?php echo esc_html( dastaan_option( 'header_promo_text' ) ); ?></span></a>
				<?php endif; ?>
				<?php if ( dastaan_option( 'header_show_search' ) ) : ?>
					<button class="icon-btn js-open-search" type="button" aria-label="<?php esc_attr_e( 'Search', 'dastaan' ); ?>" aria-controls="search-overlay" aria-expanded="false"><?php dastaan_the_icon( 'search', array( 'size' => 22 ) ); ?></button>
				<?php endif; ?>
				<?php if ( dastaan_has_wc() ) : ?>
					<a class="icon-btn hide-mobile" href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" aria-label="<?php esc_attr_e( 'My account', 'dastaan' ); ?>"><?php dastaan_the_icon( 'user', array( 'size' => 22 ) ); ?></a>
					<?php if ( dastaan_option( 'header_show_wishlist' ) && dastaan_option( 'wishlist_url' ) ) : ?>
						<a class="icon-btn hide-mobile" href="<?php echo dastaan_url( dastaan_option( 'wishlist_url' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>" aria-label="<?php esc_attr_e( 'Wishlist', 'dastaan' ); ?>"><?php dastaan_the_icon( 'heart', array( 'size' => 22 ) ); ?></a>
					<?php endif; ?>
					<button class="icon-btn cart-btn js-open-cart" type="button" aria-label="<?php esc_attr_e( 'Open bag', 'dastaan' ); ?>" aria-controls="mini-cart-drawer" aria-expanded="false">
						<?php dastaan_the_icon( 'bag', array( 'size' => 22 ) ); ?>
						<?php $dastaan_count = WC()->cart ? WC()->cart->get_cart_contents_count() : 0; ?>
						<span class="header-cart-count<?php echo $dastaan_count ? ' has-items' : ''; ?>"><?php echo esc_html( $dastaan_count ); ?></span>
					</button>
				<?php endif; ?>
			</div>
		</div>
	</header>
	<?php do_action( 'dastaan_after_header' ); ?>

	<?php
	get_template_part( 'template-parts/header/mobile-menu' );
	get_template_part( 'template-parts/header/search-overlay' );
	if ( dastaan_has_wc() ) {
		get_template_part( 'template-parts/header/mini-cart' );
	}
	?>
<?php endif; ?>

<main id="main" class="site-main">
