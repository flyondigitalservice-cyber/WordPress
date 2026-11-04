</main>

<footer class="site-footer">
	<div class="wrap">
		<div class="footer-grid">
			<div class="footer-brand">
				<?php
				$footer_logo = (int) get_theme_mod( 'raikwar_footer_logo' );
				if ( ! $footer_logo && get_theme_mod( 'custom_logo' ) ) {
					$footer_logo = (int) attachment_url_to_postid( wp_get_upload_dir()['baseurl'] . '/2026/02/raikwar-logo-white.png' );
				}
				if ( $footer_logo ) {
					echo wp_get_attachment_image( $footer_logo, 'full', false, array( 'class' => 'footer-logo', 'alt' => raikwar_info( 'brand' ) . ' logo', 'loading' => 'lazy' ) );
				} else {
					echo '<span class="brand__name">' . esc_html( raikwar_info( 'brand' ) ) . '</span>';
				}
				?>
				<p><?php echo esc_html( raikwar_info( 'legal' ) ); ?>. Digital Signatures, licenses and government registrations, handled end-to-end for businesses across Mumbai and India.</p>
				<a class="btn btn--primary" href="<?php echo esc_url( raikwar_wa() ); ?>" target="_blank" rel="noopener"><?php echo raikwar_icon( 'wa' ); ?>Chat on WhatsApp</a>
			</div>

			<div>
				<h4>Quick links</h4>
				<?php
				if ( has_nav_menu( 'footer' ) ) {
					wp_nav_menu( array( 'theme_location' => 'footer', 'container' => false, 'depth' => 1 ) );
				} else {
					raikwar_fallback_menu();
				}
				?>
			</div>

			<div>
				<h4>Popular services</h4>
				<ul>
					<?php foreach ( array_slice( raikwar_services(), 0, 7 ) as $s ) : ?>
						<li><a href="<?php echo esc_url( $s[4] ? $s[4] : home_url( '/#services' ) ); ?>"><?php echo esc_html( $s[0] ); ?></a></li>
					<?php endforeach; ?>
				</ul>
			</div>

			<div>
				<h4>Contact</h4>
				<ul>
					<li><a href="<?php echo esc_attr( raikwar_tel( raikwar_info( 'phone1' ) ) ); ?>"><?php echo esc_html( raikwar_phone_label( raikwar_info( 'phone1' ) ) ); ?></a></li>
					<?php if ( raikwar_info( 'phone2' ) ) : ?>
						<li><a href="<?php echo esc_attr( raikwar_tel( raikwar_info( 'phone2' ) ) ); ?>"><?php echo esc_html( raikwar_phone_label( raikwar_info( 'phone2' ) ) ); ?></a></li>
					<?php endif; ?>
					<li><a href="mailto:<?php echo esc_attr( antispambot( raikwar_info( 'email' ) ) ); ?>"><?php echo esc_html( antispambot( raikwar_info( 'email' ) ) ); ?></a></li>
					<?php if ( raikwar_info( 'email2' ) ) : ?>
						<li><a href="mailto:<?php echo esc_attr( antispambot( raikwar_info( 'email2' ) ) ); ?>"><?php echo esc_html( antispambot( raikwar_info( 'email2' ) ) ); ?></a></li>
					<?php endif; ?>
					<li><?php echo esc_html( raikwar_info( 'address' ) ); ?></li>
					<?php if ( raikwar_info( 'address2' ) ) : ?>
						<li><?php echo esc_html( raikwar_info( 'address2' ) ); ?></li>
					<?php endif; ?>
					<li><?php echo esc_html( raikwar_info( 'hours' ) ); ?></li>
				</ul>
			</div>
		</div>
	</div>
	<div class="wrap">
		<div class="footer-bottom">
			<span>&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php echo esc_html( raikwar_info( 'legal' ) ); ?>. All rights reserved.</span>
			<span>Website by <a href="https://bombaymarketing.in" target="_blank" rel="noopener">Bombay Marketing</a></span>
		</div>
	</div>
</footer>

<div class="mbar">
	<a class="mbar__call" href="<?php echo esc_attr( raikwar_tel( raikwar_info( 'phone1' ) ) ); ?>"><?php echo raikwar_icon( 'phone' ); ?>Call now</a>
	<a class="mbar__wa" href="<?php echo esc_url( raikwar_wa() ); ?>" target="_blank" rel="noopener"><?php echo raikwar_icon( 'wa' ); ?>WhatsApp</a>
</div>

<?php wp_footer(); ?>
</body>
</html>
