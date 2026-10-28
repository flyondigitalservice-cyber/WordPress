<?php
/**
 * Site footer. Menus: Appearance → Menus (Footer: Products / Areas / Company / Legal).
 * Texts, socials and contact details: Customize → Custom Floor Mats.
 *
 * @package CFM
 */

defined( 'ABSPATH' ) || exit;

$cfm_socials = array(
	'instagram' => cfm_opt( 'social_instagram' ),
	'facebook'  => cfm_opt( 'social_facebook' ),
	'linkedin'  => cfm_opt( 'social_linkedin' ),
	'youtube'   => cfm_opt( 'social_youtube' ),
	'tiktok'    => cfm_opt( 'social_tiktok' ),
);
$cfm_menus   = array(
	'footer_products' => __( 'Products', 'cfm' ),
	'footer_areas'    => __( 'Areas we serve', 'cfm' ),
	'footer_company'  => __( 'Company', 'cfm' ),
);
?>
</main>

<footer class="cfm-footer">
	<div class="cfm-wrap cfm-footer__grid">
		<div class="cfm-footer__brand">
			<a class="cfm-logo cfm-logo--footer" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php echo esc_attr( cfm_opt( 'brand_name' ) ); ?>"><?php echo cfm_brand_mark( 'light' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
			<p class="cfm-footer__about"><?php echo esc_html( cfm_opt( 'footer_about' ) ); ?></p>
			<?php if ( cfm_opt( 'parent_url' ) ) : ?>
				<p class="cfm-footer__parent">
					<?php echo esc_html( cfm_opt( 'parent_blurb' ) ); ?>
					<a href="<?php echo esc_url( cfm_opt( 'parent_url' ) ); ?>" target="_blank" rel="noopener"><?php echo esc_html( wp_parse_url( cfm_opt( 'parent_url' ), PHP_URL_HOST ) ); ?> <?php echo cfm_icon( 'external', 12 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
				</p>
			<?php endif; ?>
			<?php if ( array_filter( $cfm_socials ) ) : ?>
				<ul class="cfm-social">
					<?php foreach ( array_filter( $cfm_socials ) as $cfm_network => $cfm_url ) : ?>
						<li><a href="<?php echo esc_url( $cfm_url ); ?>" target="_blank" rel="noopener" aria-label="<?php echo esc_attr( ucfirst( $cfm_network ) ); ?>"><?php echo cfm_icon( $cfm_network, 18 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>

		<?php foreach ( $cfm_menus as $cfm_location => $cfm_label ) : ?>
			<?php if ( has_nav_menu( $cfm_location ) ) : ?>
				<nav class="cfm-footer__col" aria-label="<?php echo esc_attr( $cfm_label ); ?>">
					<h2 class="cfm-footer__title"><?php echo esc_html( wp_get_nav_menu_name( $cfm_location ) && 0 !== strpos( wp_get_nav_menu_name( $cfm_location ), 'Footer' ) ? wp_get_nav_menu_name( $cfm_location ) : $cfm_label ); ?></h2>
					<?php
					wp_nav_menu(
						array(
							'theme_location' => $cfm_location,
							'container'      => false,
							'menu_class'     => 'cfm-footer__menu',
							'depth'          => 1,
						)
					);
					?>
				</nav>
			<?php endif; ?>
		<?php endforeach; ?>

		<div class="cfm-footer__col cfm-footer__contact">
			<h2 class="cfm-footer__title"><?php esc_html_e( 'Get in touch', 'cfm' ); ?></h2>
			<?php echo cfm_sc_contact_info(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<a class="cfm-btn cfm-btn--wa cfm-btn--sm" href="<?php echo esc_url( cfm_wa_url( cfm_wa_message_for() ) ); ?>" target="_blank" rel="noopener" data-cfm-wa><?php echo cfm_icon( 'whatsapp', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><?php esc_html_e( 'WhatsApp us', 'cfm' ); ?></span></a>
		</div>
	</div>

	<div class="cfm-footer__bar">
		<div class="cfm-wrap cfm-footer__bar-inner">
			<p class="cfm-footer__copy"><?php echo esc_html( str_replace( '{year}', wp_date( 'Y' ), cfm_opt( 'footer_copyright' ) ) ); ?></p>
			<?php
			if ( has_nav_menu( 'legal' ) ) {
				wp_nav_menu(
					array(
						'theme_location' => 'legal',
						'container'      => false,
						'menu_class'     => 'cfm-footer__legal',
						'depth'          => 1,
					)
				);
			}
			?>
		</div>
	</div>
</footer>

<a class="cfm-float-wa" href="<?php echo esc_url( cfm_wa_url( cfm_wa_message_for() ) ); ?>" target="_blank" rel="noopener" aria-label="<?php esc_attr_e( 'Chat on WhatsApp', 'cfm' ); ?>" data-cfm-wa>
	<?php echo cfm_icon( 'whatsapp', 30 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	<span class="cfm-float-wa__label"><?php esc_html_e( 'Quote on WhatsApp', 'cfm' ); ?></span>
</a>

<nav class="cfm-mobilebar" aria-label="<?php esc_attr_e( 'Quick contact', 'cfm' ); ?>">
	<?php if ( cfm_opt( 'phone_link' ) ) : ?>
		<a href="<?php echo esc_url( 'tel:' . preg_replace( '/[^\d+]/', '', cfm_opt( 'phone_link' ) ) ); ?>"><?php echo cfm_icon( 'phone', 20 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><?php esc_html_e( 'Call', 'cfm' ); ?></span></a>
	<?php else : ?>
		<a href="<?php echo esc_url( cfm_page_url( 'catalogues' ) ); ?>"><?php echo cfm_icon( 'file', 20 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><?php esc_html_e( 'Catalogues', 'cfm' ); ?></span></a>
	<?php endif; ?>
	<a class="is-wa" href="<?php echo esc_url( cfm_wa_url( cfm_wa_message_for() ) ); ?>" target="_blank" rel="noopener" data-cfm-wa><?php echo cfm_icon( 'whatsapp', 20 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><?php esc_html_e( 'WhatsApp', 'cfm' ); ?></span></a>
	<a href="<?php echo esc_url( cfm_page_url( 'get-a-free-quote' ) ); ?>"><?php echo cfm_icon( 'ruler', 20 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><?php esc_html_e( 'Free quote', 'cfm' ); ?></span></a>
</nav>

<div class="cfm-lightbox" hidden data-cfm-lightbox-root>
	<button class="cfm-lightbox__close" type="button" aria-label="<?php esc_attr_e( 'Close', 'cfm' ); ?>"><?php echo cfm_icon( 'close', 28 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button>
	<img alt="">
</div>

<div class="cfm-pdfviewer" hidden data-cfm-pdf-root role="dialog" aria-modal="true" aria-labelledby="cfm-pdf-title">
	<div class="cfm-pdfviewer__bar">
		<strong id="cfm-pdf-title" class="cfm-pdfviewer__title"></strong>
		<a class="cfm-btn cfm-btn--ghost cfm-btn--sm" href="#" target="_blank" rel="noopener" data-cfm-pdf-open><?php echo cfm_icon( 'external', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><?php esc_html_e( 'New tab', 'cfm' ); ?></span></a>
		<a class="cfm-btn cfm-btn--ghost cfm-btn--sm" href="#" download data-cfm-pdf-download><?php echo cfm_icon( 'download', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><?php esc_html_e( 'Download', 'cfm' ); ?></span></a>
		<a class="cfm-btn cfm-btn--wa cfm-btn--sm" href="#" target="_blank" rel="noopener" data-cfm-pdf-wa><?php echo cfm_icon( 'whatsapp', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><?php esc_html_e( 'Ask price', 'cfm' ); ?></span></a>
		<button class="cfm-pdfviewer__close" type="button" aria-label="<?php esc_attr_e( 'Close', 'cfm' ); ?>"><?php echo cfm_icon( 'close', 24 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button>
	</div>
	<iframe class="cfm-pdfviewer__frame" title="<?php esc_attr_e( 'Catalogue', 'cfm' ); ?>" src="about:blank"></iframe>
</div>

<?php wp_footer(); ?>
</body>
</html>
