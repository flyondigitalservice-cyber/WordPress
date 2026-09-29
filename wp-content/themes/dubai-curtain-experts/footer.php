<?php
/**
 * Footer: widget columns, brand block, credit bar and floating actions.
 *
 * Columns are edited in Appearance → Widgets (Footer Column 1–4). Empty
 * columns fall back to the Footer Menu and the Customizer contact details.
 *
 * @package Dubai_Curtain_Experts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

	<footer class="dce-footer" id="colophon">
		<div class="dce-container dce-footer-grid">

			<div class="dce-footer-col dce-footer-brandcol">
				<a class="dce-brand dce-brand-light" href="<?php echo esc_url( home_url( '/' ) ); ?>">
					<?php if ( has_custom_logo() ) : ?>
						<span class="dce-brand-logo"><?php echo wp_get_attachment_image( get_theme_mod( 'custom_logo' ), 'full', false, array( 'alt' => dce_mod( 'brand_name' ) ) ); ?></span>
					<?php else : ?>
						<span class="dce-brand-mark dce-brand-mark-light"><span><?php echo esc_html( mb_substr( dce_mod( 'brand_name' ), 0, 1 ) ); ?></span></span>
					<?php endif; ?>
					<span class="dce-brand-text">
						<strong><?php echo esc_html( dce_mod( 'brand_name' ) ); ?></strong>
						<em><?php echo esc_html( dce_mod( 'sub_brand' ) ); ?></em>
					</span>
				</a>
				<?php if ( is_active_sidebar( 'footer-1' ) ) : ?>
					<?php dynamic_sidebar( 'footer-1' ); ?>
				<?php else : ?>
					<p class="dce-footer-blurb">
						<?php esc_html_e( 'Made-to-measure curtains and blinds for homes and businesses across Dubai and the UAE.', 'dubai-curtain-experts' ); ?>
					</p>
				<?php endif; ?>
			</div>

			<div class="dce-footer-col">
				<?php if ( is_active_sidebar( 'footer-2' ) ) : ?>
					<?php dynamic_sidebar( 'footer-2' ); ?>
				<?php else : ?>
					<h3 class="dce-widget-title"><?php esc_html_e( 'Explore', 'dubai-curtain-experts' ); ?></h3>
					<?php
					if ( has_nav_menu( 'footer' ) ) {
						wp_nav_menu(
							array(
								'theme_location' => 'footer',
								'container'      => false,
								'menu_class'     => 'dce-foot-list',
								'depth'          => 1,
							)
						);
					} else {
						dce_fallback_menu();
					}
					?>
				<?php endif; ?>
			</div>

			<div class="dce-footer-col">
				<?php if ( is_active_sidebar( 'footer-3' ) ) : ?>
					<?php dynamic_sidebar( 'footer-3' ); ?>
				<?php endif; ?>
			</div>

			<div class="dce-footer-col">
				<?php if ( is_active_sidebar( 'footer-4' ) ) : ?>
					<?php dynamic_sidebar( 'footer-4' ); ?>
				<?php else : ?>
					<h3 class="dce-widget-title"><?php esc_html_e( 'Showroom', 'dubai-curtain-experts' ); ?></h3>
					<ul class="dce-foot-contact">
						<li><?php echo dce_icon( 'pin', array( 'size' => 15 ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><a href="<?php echo esc_url( dce_mod( 'map_url' ) ); ?>" target="_blank" rel="noopener"><?php echo esc_html( dce_mod( 'address' ) ); ?></a></span></li>
						<li><?php echo dce_icon( 'clock', array( 'size' => 15 ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><?php echo esc_html( dce_mod( 'hours_weekday' ) ); ?><br><?php echo esc_html( dce_mod( 'hours_sunday' ) ); ?></span></li>
						<li><?php echo dce_icon( 'phone', array( 'size' => 15 ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><a href="tel:<?php echo esc_attr( dce_tel() ); ?>"><?php echo esc_html( dce_mod( 'phone' ) ); ?></a></li>
						<li><?php echo dce_icon( 'whatsapp', array( 'size' => 15 ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><a href="<?php echo esc_url( dce_wa_url( dce_wa_context_text() ) ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'WhatsApp us', 'dubai-curtain-experts' ); ?></a></li>
						<li><?php echo dce_icon( 'mail', array( 'size' => 15 ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><a href="mailto:<?php echo esc_attr( dce_mod( 'email' ) ); ?>"><?php echo esc_html( dce_mod( 'email' ) ); ?></a></li>
					</ul>
				<?php endif; ?>
			</div>
		</div>

		<div class="dce-container dce-footer-bottom">
			<span class="dce-footer-credit"><?php echo esc_html( dce_mod( 'footer_text' ) ); ?></span>
			<span class="dce-footer-made">
				<?php esc_html_e( 'Part of', 'dubai-curtain-experts' ); ?>
				<a href="<?php echo esc_url( dce_parent_url() ); ?>" target="_blank" rel="noopener">Casa Vera Home</a>
			</span>
		</div>
	</footer>
</div><!-- #page -->

<!-- ============================================================ floats -->
<div class="dce-floats">
	<a class="dce-float dce-float-call" href="tel:<?php echo esc_attr( dce_tel() ); ?>" aria-label="<?php esc_attr_e( 'Call us', 'dubai-curtain-experts' ); ?>">
		<?php echo dce_icon( 'phone', array( 'size' => 20 ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</a>
	<a class="dce-float dce-float-wa" href="<?php echo esc_url( dce_wa_url( dce_wa_context_text() ) ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e( 'Chat on WhatsApp', 'dubai-curtain-experts' ); ?>">
		<?php echo dce_icon( 'whatsapp', array( 'size' => 24 ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</a>
</div>

<?php wp_footer(); ?>
</body>
</html>
