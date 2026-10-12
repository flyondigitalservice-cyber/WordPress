<?php
/**
 * Footer.
 *
 * @package Dastaan
 */

defined( 'ABSPATH' ) || exit;

$dastaan_checkout_focus = dastaan_has_wc() && is_checkout() && ! is_order_received_page();
?>
</main><!-- #main -->

<?php if ( $dastaan_checkout_focus ) : ?>
	<footer class="checkout-footer">
		<div class="container checkout-footer__inner">
			<?php if ( dastaan_option( 'footer_payment_icons' ) ) { dastaan_payment_icons(); } ?>
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'legal',
					'container'      => false,
					'menu_class'     => 'legal-menu',
					'depth'          => 1,
					'fallback_cb'    => false,
				)
			);
			?>
			<p class="copyright"><?php echo esc_html( dastaan_placeholders( dastaan_option( 'footer_copyright' ) ) ); ?></p>
		</div>
	</footer>
<?php else : ?>
	<?php do_action( 'dastaan_footer_before' ); ?>
	<?php get_template_part( 'template-parts/footer/newsletter' ); ?>
	<footer id="colophon" class="site-footer">
		<?php get_template_part( 'template-parts/footer/columns' ); ?>
		<?php get_template_part( 'template-parts/footer/bottom' ); ?>
	</footer>
	<?php get_template_part( 'template-parts/footer/mobile-nav' ); ?>
<?php endif; ?>

<?php get_template_part( 'template-parts/footer/floating' ); ?>

</div><!-- #page -->
<?php wp_footer(); ?>
</body>
</html>
