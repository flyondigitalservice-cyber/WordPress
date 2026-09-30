<?php
/**
 * Site footer.
 *
 * @package Zobo
 */

$zobo_socials = array(
	'behance'   => 'Behance',
	'instagram' => 'Instagram',
	'linkedin'  => 'LinkedIn',
	'youtube'   => 'YouTube',
);
$zobo_wa      = zobod2c_whatsapp_url();
?>
</main>

<footer class="site-footer">
	<div class="container">
		<div class="footer-big" aria-hidden="true"><?php echo esc_html( zobod2c_brand_name() ); ?><span class="wordmark-dot">.</span></div>

		<div class="footer-grid">
			<div class="footer-about">
				<p><?php echo esc_html( zobod2c_opt( 'footer_blurb' ) ); ?></p>
			</div>

			<div>
				<h2 class="footer-title"><?php esc_html_e( 'Talk to us', 'zobo-d2c' ); ?></h2>
				<ul class="footer-list">
					<?php if ( zobod2c_opt( 'email' ) ) : ?>
						<li><a href="mailto:<?php echo esc_attr( antispambot( zobod2c_opt( 'email' ) ) ); ?>"><?php echo esc_html( antispambot( zobod2c_opt( 'email' ) ) ); ?></a></li>
					<?php endif; ?>
					<?php if ( zobod2c_opt( 'phone' ) ) : ?>
						<li><a href="tel:<?php echo esc_attr( preg_replace( '/[^\d+]/', '', zobod2c_opt( 'phone' ) ) ); ?>"><?php echo esc_html( zobod2c_opt( 'phone' ) ); ?></a></li>
					<?php endif; ?>
					<?php if ( $zobo_wa ) : ?>
						<li><a href="<?php echo esc_url( $zobo_wa ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'WhatsApp', 'zobo-d2c' ); ?></a></li>
					<?php endif; ?>
					<?php if ( zobod2c_opt( 'address' ) ) : ?>
						<li><?php echo nl2br( esc_html( zobod2c_opt( 'address' ) ) ); ?></li>
					<?php endif; ?>
					<?php if ( zobod2c_opt( 'hours' ) ) : ?>
						<li><?php echo esc_html( zobod2c_opt( 'hours' ) ); ?></li>
					<?php endif; ?>
				</ul>
			</div>

			<div>
				<h2 class="footer-title"><?php esc_html_e( 'Follow', 'zobo-d2c' ); ?></h2>
				<ul class="footer-list">
					<?php foreach ( $zobo_socials as $zobo_key => $zobo_label ) : ?>
						<?php if ( zobod2c_opt( $zobo_key ) ) : ?>
							<li><a href="<?php echo esc_url( zobod2c_opt( $zobo_key ) ); ?>" target="_blank" rel="noopener"><?php echo esc_html( $zobo_label ); ?></a></li>
						<?php endif; ?>
					<?php endforeach; ?>
				</ul>
			</div>

			<?php if ( has_nav_menu( 'footer' ) ) : ?>
				<div>
					<h2 class="footer-title"><?php esc_html_e( 'Company', 'zobo-d2c' ); ?></h2>
					<?php
					wp_nav_menu(
						array(
							'theme_location' => 'footer',
							'container'      => false,
							'menu_class'     => 'footer-list',
							'depth'          => 1,
						)
					);
					?>
				</div>
			<?php endif; ?>
		</div>

		<div class="footer-bottom">
			<span>&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php echo esc_html( ucfirst( zobod2c_brand_name() ) ); ?>. <?php esc_html_e( 'All rights reserved.', 'zobo-d2c' ); ?></span>
			<span><?php esc_html_e( 'Made in India, for founders everywhere.', 'zobo-d2c' ); ?></span>
		</div>
	</div>
</footer>

<?php if ( $zobo_wa ) : ?>
	<a class="wa-float" href="<?php echo esc_url( $zobo_wa ); ?>" target="_blank" rel="noopener" aria-label="<?php esc_attr_e( 'Chat on WhatsApp', 'zobo-d2c' ); ?>">
		<?php echo zobod2c_icon( 'whatsapp' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
	</a>
<?php endif; ?>

<?php wp_footer(); ?>
</body>
</html>
