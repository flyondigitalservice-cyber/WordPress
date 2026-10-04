<?php
/**
 * Site footer.
 *
 * @package FlavorKit
 */

defined( 'ABSPATH' ) || exit;

$fk_copyright = flavorkit_mod( 'footer_copyright' );
if ( ! $fk_copyright ) {
	/* translators: 1: year, 2: site name */
	$fk_copyright = sprintf( __( '© %1$s %2$s. All rights reserved.', 'flavorkit' ), gmdate( 'Y' ), get_bloginfo( 'name' ) );
}
$fk_whatsapp = flavorkit_whatsapp_url();
?>

<footer class="fk-footer">
	<?php flavorkit_wave( 'top', 'var(--fk-dark)' ); ?>
	<div class="fk-container">
		<div class="fk-footer-grid">
			<div class="fk-footer-brand">
				<a class="fk-footer-wordmark" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a>
				<p><?php echo esc_html( flavorkit_mod( 'footer_about' ) ); ?></p>
				<?php flavorkit_social_links(); ?>
			</div>

			<div class="fk-footer-col">
				<h2 class="fk-footer-title"><?php esc_html_e( 'Shop', 'flavorkit' ); ?></h2>
				<?php
				if ( has_nav_menu( 'footer' ) ) {
					wp_nav_menu(
						array(
							'theme_location' => 'footer',
							'container'      => false,
							'depth'          => 1,
						)
					);
				} elseif ( function_exists( 'wc_get_page_permalink' ) ) {
					echo '<ul>';
					$fk_cats = get_terms(
						array(
							'taxonomy'   => 'product_cat',
							'hide_empty' => true,
							'number'     => 5,
							'exclude'    => array( (int) get_option( 'default_product_cat' ) ),
						)
					);
					if ( ! is_wp_error( $fk_cats ) ) {
						foreach ( $fk_cats as $fk_cat ) {
							printf( '<li><a href="%s">%s</a></li>', esc_url( get_term_link( $fk_cat ) ), esc_html( $fk_cat->name ) );
						}
					}
					printf( '<li><a href="%s">%s</a></li>', esc_url( wc_get_page_permalink( 'shop' ) ), esc_html__( 'Shop all', 'flavorkit' ) );
					echo '</ul>';
				}
				?>
			</div>

			<div class="fk-footer-col">
				<h2 class="fk-footer-title"><?php esc_html_e( 'Help', 'flavorkit' ); ?></h2>
				<?php
				if ( has_nav_menu( 'help' ) ) {
					wp_nav_menu(
						array(
							'theme_location' => 'help',
							'container'      => false,
							'depth'          => 1,
						)
					);
				} else {
					echo '<ul>';
					if ( function_exists( 'wc_get_page_permalink' ) ) {
						printf( '<li><a href="%s">%s</a></li>', esc_url( wc_get_page_permalink( 'myaccount' ) ), esc_html__( 'My account', 'flavorkit' ) );
						printf( '<li><a href="%s">%s</a></li>', esc_url( wc_get_cart_url() ), esc_html__( 'Cart', 'flavorkit' ) );
					}
					printf( '<li><a href="%s">%s</a></li>', esc_url( home_url( '/#faq' ) ), esc_html__( 'FAQ', 'flavorkit' ) );
					$fk_privacy = get_privacy_policy_url();
					if ( $fk_privacy ) {
						printf( '<li><a href="%s">%s</a></li>', esc_url( $fk_privacy ), esc_html__( 'Privacy policy', 'flavorkit' ) );
					}
					echo '</ul>';
				}
				?>
			</div>

			<div class="fk-footer-col fk-footer-news">
				<h2 class="fk-footer-title"><?php esc_html_e( 'Stay in the loop', 'flavorkit' ); ?></h2>
				<p><?php esc_html_e( 'Offers, new drops and recipes. No spam, ever.', 'flavorkit' ); ?></p>
				<?php get_template_part( 'template-parts/components/newsletter-form', null, array( 'compact' => true ) ); ?>
			</div>
		</div>

		<div class="fk-footer-bottom">
			<p><?php echo esc_html( $fk_copyright ); ?></p>
			<p class="fk-pay" aria-label="<?php esc_attr_e( 'Payment methods', 'flavorkit' ); ?>">
				<span>UPI</span><span>VISA</span><span>Mastercard</span><span>RuPay</span><span>COD</span>
			</p>
		</div>
	</div>
	<p class="fk-footer-giant" aria-hidden="true"><?php bloginfo( 'name' ); ?></p>
</footer>

<?php if ( class_exists( 'WooCommerce' ) ) : ?>
	<div id="fk-cart-drawer" class="fk-drawer fk-drawer-right" aria-hidden="true" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Your cart', 'flavorkit' ); ?>">
		<div class="fk-drawer-head">
			<span class="fk-drawer-title"><?php esc_html_e( 'Your cart', 'flavorkit' ); ?></span>
			<button class="fk-icon-btn fk-drawer-close" type="button" aria-label="<?php esc_attr_e( 'Close cart', 'flavorkit' ); ?>"><?php echo flavorkit_icon( 'close' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
		</div>
		<div class="widget_shopping_cart_content"><?php woocommerce_mini_cart(); ?></div>
	</div>
<?php endif; ?>

<div class="fk-overlay" hidden></div>

<?php if ( $fk_whatsapp ) : ?>
	<a class="fk-whatsapp" href="<?php echo esc_url( $fk_whatsapp ); ?>" target="_blank" rel="noopener" aria-label="<?php esc_attr_e( 'Chat with us on WhatsApp', 'flavorkit' ); ?>">
		<?php echo flavorkit_icon( 'whatsapp', 28 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
	</a>
<?php endif; ?>

<?php wp_footer(); ?>
</body>
</html>
