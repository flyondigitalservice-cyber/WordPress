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
$zobo_wa      = zobo_whatsapp_url();
?>
</main>

<footer class="site-footer">
	<div class="container">
		<div class="footer-big" aria-hidden="true"><?php bloginfo( 'name' ); ?><span class="wordmark-dot">.</span></div>

		<div class="footer-grid">
			<div class="footer-about">
				<p><?php echo esc_html( zobo_opt( 'footer_blurb' ) ); ?></p>
			</div>

			<div>
				<h2 class="footer-title"><?php esc_html_e( 'Talk to us', 'zobo' ); ?></h2>
				<ul class="footer-list">
					<?php if ( zobo_opt( 'email' ) ) : ?>
						<li><a href="mailto:<?php echo esc_attr( antispambot( zobo_opt( 'email' ) ) ); ?>"><?php echo esc_html( antispambot( zobo_opt( 'email' ) ) ); ?></a></li>
					<?php endif; ?>
					<?php if ( zobo_opt( 'phone' ) ) : ?>
						<li><a href="tel:<?php echo esc_attr( preg_replace( '/[^\d+]/', '', zobo_opt( 'phone' ) ) ); ?>"><?php echo esc_html( zobo_opt( 'phone' ) ); ?></a></li>
					<?php endif; ?>
					<?php if ( $zobo_wa ) : ?>
						<li><a href="<?php echo esc_url( $zobo_wa ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'WhatsApp', 'zobo' ); ?></a></li>
					<?php endif; ?>
					<?php if ( zobo_opt( 'address' ) ) : ?>
						<li><?php echo nl2br( esc_html( zobo_opt( 'address' ) ) ); ?></li>
					<?php endif; ?>
				</ul>
			</div>

			<div>
				<h2 class="footer-title"><?php esc_html_e( 'Follow', 'zobo' ); ?></h2>
				<ul class="footer-list">
					<?php foreach ( $zobo_socials as $zobo_key => $zobo_label ) : ?>
						<?php if ( zobo_opt( $zobo_key ) ) : ?>
							<li><a href="<?php echo esc_url( zobo_opt( $zobo_key ) ); ?>" target="_blank" rel="noopener"><?php echo esc_html( $zobo_label ); ?></a></li>
						<?php endif; ?>
					<?php endforeach; ?>
				</ul>
			</div>

			<?php if ( has_nav_menu( 'footer' ) ) : ?>
				<div>
					<h2 class="footer-title"><?php esc_html_e( 'Company', 'zobo' ); ?></h2>
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
			<span>&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>. <?php esc_html_e( 'All rights reserved.', 'zobo' ); ?></span>
			<span><?php esc_html_e( 'Made in India, for founders everywhere.', 'zobo' ); ?></span>
		</div>
	</div>
</footer>

<?php if ( $zobo_wa ) : ?>
	<a class="wa-float" href="<?php echo esc_url( $zobo_wa ); ?>" target="_blank" rel="noopener" aria-label="<?php esc_attr_e( 'Chat on WhatsApp', 'zobo' ); ?>">
		<?php echo zobo_icon( 'whatsapp' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
	</a>
<?php endif; ?>

<?php wp_footer(); ?>
</body>
</html>
