<?php
/**
 * Site footer, bag drawer and floating contact.
 *
 * @package HadhramiFashion
 */

$whatsapp = preg_replace( '/\D/', '', (string) hf_opt( 'whatsapp' ) );
$suits    = get_term_link( 'suits', 'product_cat' );
$shirts   = get_term_link( 'shirts', 'product_cat' );
?>
</main>

<footer class="site-footer">
	<div class="wrap">
		<div class="footer-grid">
			<div class="footer-brand">
				<span class="brand__name">Hadhrami</span>
				<span class="brand__sub"><?php esc_html_e( 'Fashion · Tailors', 'hadhramifashion' ); ?></span>
				<p>
					<?php
					printf(
						/* translators: %s: patron name */
						esc_html__( 'A family house of tailoring under %s. Shirts and suits cut with patience, delivered with urgency.', 'hadhramifashion' ),
						esc_html( hf_opt( 'founder' ) )
					);
					?>
				</p>
			</div>
			<div>
				<h4><?php esc_html_e( 'The House', 'hadhramifashion' ); ?></h4>
				<ul>
					<?php if ( ! is_wp_error( $suits ) ) : ?>
						<li><a href="<?php echo esc_url( $suits ); ?>"><?php esc_html_e( 'Suits', 'hadhramifashion' ); ?></a></li>
					<?php endif; ?>
					<?php if ( ! is_wp_error( $shirts ) ) : ?>
						<li><a href="<?php echo esc_url( $shirts ); ?>"><?php esc_html_e( 'Shirts', 'hadhramifashion' ); ?></a></li>
					<?php endif; ?>
					<li><a href="<?php echo esc_url( hf_page_url( 'page-templates/cloth.php' ) ); ?>"><?php esc_html_e( 'Cloth Library', 'hadhramifashion' ); ?></a></li>
					<li><a href="<?php echo esc_url( hf_page_url( 'page-templates/heritage.php' ) ); ?>"><?php esc_html_e( 'Our Heritage', 'hadhramifashion' ); ?></a></li>
				</ul>
			</div>
			<div>
				<h4><?php esc_html_e( 'Delivery', 'hadhramifashion' ); ?></h4>
				<ul>
					<li><a href="<?php echo esc_url( hf_page_url( 'page-templates/express.php' ) ); ?>"><?php esc_html_e( '2-hour shirts', 'hadhramifashion' ); ?></a></li>
					<li><a href="<?php echo esc_url( hf_page_url( 'page-templates/express.php' ) ); ?>"><?php esc_html_e( '24-hour tailoring', 'hadhramifashion' ); ?></a></li>
					<li><a href="<?php echo esc_url( hf_track_url() ); ?>"><?php esc_html_e( 'Track an order', 'hadhramifashion' ); ?></a></li>
				</ul>
			</div>
			<div>
				<h4><?php esc_html_e( 'The Atelier', 'hadhramifashion' ); ?></h4>
				<ul>
					<li><?php echo esc_html( hf_delivery_opt( 'store_address' ) ); ?></li>
					<li><a href="tel:<?php echo esc_attr( preg_replace( '/[^\d+]/', '', hf_opt( 'phone' ) ) ); ?>"><?php echo esc_html( hf_opt( 'phone' ) ); ?></a></li>
					<li><a href="mailto:<?php echo esc_attr( hf_opt( 'email' ) ); ?>"><?php echo esc_html( hf_opt( 'email' ) ); ?></a></li>
					<li>
						<?php
						printf(
							/* translators: 1: opening hour, 2: closing hour */
							esc_html__( 'Daily %1$s:00 – %2$s:00', 'hadhramifashion' ),
							esc_html( hf_delivery_opt( 'open_hour' ) ),
							esc_html( hf_opt( 'close_hour' ) )
						);
						?>
					</li>
				</ul>
			</div>
		</div>
		<div class="footer-base">
			<span>© <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php esc_html_e( 'Hadhrami Fashion. A family business.', 'hadhramifashion' ); ?></span>
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'footer',
					'container'      => false,
					'fallback_cb'    => false,
					'depth'          => 1,
					'menu_class'     => 'hf-menu',
				)
			);
			?>
		</div>
	</div>
</footer>

<div class="scrim" data-scrim></div>

<aside class="drawer" id="bag-drawer" aria-label="<?php esc_attr_e( 'Your bag', 'hadhramifashion' ); ?>" aria-hidden="true">
	<div class="drawer__head">
		<h3><?php esc_html_e( 'Your Bag', 'hadhramifashion' ); ?></h3>
		<button type="button" data-bag-close aria-label="<?php esc_attr_e( 'Close bag', 'hadhramifashion' ); ?>"><?php echo hf_icon( 'close' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
	</div>
	<?php hf_mini_bag(); ?>
</aside>

<?php if ( $whatsapp ) : ?>
	<a class="wa-float" href="<?php echo esc_url( 'https://wa.me/' . $whatsapp ); ?>" target="_blank" rel="noopener">
		<?php echo hf_icon( 'phone' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		<?php esc_html_e( 'Speak to a tailor', 'hadhramifashion' ); ?>
	</a>
<?php endif; ?>

<div class="toast" data-toast role="status" aria-live="polite"></div>

<?php wp_footer(); ?>
</body>
</html>
