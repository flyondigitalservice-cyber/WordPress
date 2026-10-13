<?php
/**
 * Off-canvas mobile menu.
 *
 * @package Dastaan
 */

defined( 'ABSPATH' ) || exit;
$dastaan_location = has_nav_menu( 'mobile' ) ? 'mobile' : 'primary';
?>
<div id="mobile-menu" class="drawer drawer--left" aria-hidden="true" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Menu', 'dastaan' ); ?>">
	<div class="drawer__panel">
		<div class="drawer__head">
			<div class="site-branding site-branding--drawer"><?php get_template_part( 'template-parts/header/logo' ); ?></div>
			<button class="icon-btn js-close-drawer" type="button" aria-label="<?php esc_attr_e( 'Close menu', 'dastaan' ); ?>"><?php dastaan_the_icon( 'close', array( 'size' => 24 ) ); ?></button>
		</div>
		<nav class="mobile-nav" aria-label="<?php esc_attr_e( 'Mobile', 'dastaan' ); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => $dastaan_location,
					'container'      => false,
					'menu_class'     => 'menu',
					'depth'          => 3,
					'walker'         => new Dastaan_Walker_Nav(),
					'fallback_cb'    => 'dastaan_menu_fallback',
				)
			);
			?>
		</nav>
		<div class="drawer__foot">
			<?php if ( dastaan_has_wc() ) : ?>
				<a class="drawer__link" href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>"><?php dastaan_the_icon( 'user', array( 'size' => 18 ) ); ?><?php esc_html_e( 'My account', 'dastaan' ); ?></a>
				<?php if ( dastaan_wishlist_url() ) : ?>
					<a class="drawer__link" href="<?php echo dastaan_wishlist_url(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>"><?php dastaan_the_icon( 'heart', array( 'size' => 18 ) ); ?><?php esc_html_e( 'Wishlist', 'dastaan' ); ?></a>
				<?php endif; ?>
			<?php endif; ?>
			<?php if ( dastaan_whatsapp_url() ) : ?>
				<a class="drawer__link" href="<?php echo esc_url( dastaan_whatsapp_url() ); ?>" target="_blank" rel="noopener"><?php dastaan_the_icon( 'whatsapp', array( 'size' => 18 ) ); ?><?php esc_html_e( 'Chat on WhatsApp', 'dastaan' ); ?></a>
			<?php endif; ?>
			<?php dastaan_social_links( 'social-links--drawer' ); ?>
		</div>
	</div>
	<button class="drawer__backdrop js-close-drawer" type="button" tabindex="-1" aria-label="<?php esc_attr_e( 'Close', 'dastaan' ); ?>"></button>
</div>
