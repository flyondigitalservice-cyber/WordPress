<?php
/**
 * Footer.
 *
 * @package Lawyers_Foundation_Theme
 */

?>
</main>
<footer class="site-footer">
	<div class="wrap">
		<div class="footer-grid">
			<div>
				<h3><?php echo esc_html( lft_name() ); ?></h3>
				<p><?php echo esc_html( get_bloginfo( 'description' ) ? get_bloginfo( 'description' ) : __( 'A community of lawyers working for access to justice.', 'lawyers-foundation-theme' ) ); ?></p>
			</div>
			<div>
				<h3><?php esc_html_e( 'Foundation', 'lawyers-foundation-theme' ); ?></h3>
				<?php
				if ( has_nav_menu( 'footer' ) ) {
					wp_nav_menu( array( 'theme_location' => 'footer', 'container' => false, 'depth' => 1 ) );
				} else {
					lft_fallback_menu();
				}
				?>
			</div>
			<?php if ( lft_has_plugin() ) : ?>
			<div>
				<h3><?php esc_html_e( 'Members', 'lawyers-foundation-theme' ); ?></h3>
				<ul>
					<li><a href="<?php echo esc_url( lft_url( 'join' ) ); ?>"><?php esc_html_e( 'Become a member', 'lawyers-foundation-theme' ); ?></a></li>
					<li><a href="<?php echo esc_url( lft_url( 'dashboard' ) ); ?>"><?php esc_html_e( 'Member dashboard', 'lawyers-foundation-theme' ); ?></a></li>
					<li><a href="<?php echo esc_url( lft_url( 'directory' ) ); ?>"><?php esc_html_e( 'Member directory', 'lawyers-foundation-theme' ); ?></a></li>
				</ul>
			</div>
			<?php endif; ?>
		</div>
		<div class="footer-bottom">&copy; <?php echo esc_html( wp_date( 'Y' ) . ' ' . lft_name() ); ?></div>
	</div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
