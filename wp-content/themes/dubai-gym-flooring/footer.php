<?php
/**
 * Site footer. Menus: Appearance → Menus (Footer: Products / Areas / Company / Legal).
 * Texts, socials and contact details: Customize → Dubai Gym Flooring.
 *
 * @package DGF
 */

defined( 'ABSPATH' ) || exit;

$dgf_socials = array(
	'instagram' => dgf_opt( 'social_instagram' ),
	'facebook'  => dgf_opt( 'social_facebook' ),
	'linkedin'  => dgf_opt( 'social_linkedin' ),
	'youtube'   => dgf_opt( 'social_youtube' ),
	'tiktok'    => dgf_opt( 'social_tiktok' ),
);
$dgf_menus   = array(
	'footer_products' => __( 'Products', 'dgf' ),
	'footer_areas'    => __( 'Areas we serve', 'dgf' ),
	'footer_company'  => __( 'Company', 'dgf' ),
);
?>
</main>

<footer class="dgf-footer">
	<div class="dgf-wrap dgf-footer__grid">
		<div class="dgf-footer__brand">
			<a class="dgf-logo dgf-logo--footer" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php echo esc_attr( dgf_opt( 'brand_name' ) ); ?>"><?php echo dgf_brand_mark( 'light' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
			<p class="dgf-footer__about"><?php echo esc_html( dgf_opt( 'footer_about' ) ); ?></p>
			<?php if ( dgf_opt( 'parent_url' ) ) : ?>
				<p class="dgf-footer__parent">
					<?php echo esc_html( dgf_opt( 'parent_blurb' ) ); ?>
					<a href="<?php echo esc_url( dgf_opt( 'parent_url' ) ); ?>" target="_blank" rel="noopener"><?php echo esc_html( wp_parse_url( dgf_opt( 'parent_url' ), PHP_URL_HOST ) ); ?> <?php echo dgf_icon( 'external', 12 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
				</p>
			<?php endif; ?>
			<?php if ( array_filter( $dgf_socials ) ) : ?>
				<ul class="dgf-social">
					<?php foreach ( array_filter( $dgf_socials ) as $dgf_network => $dgf_url ) : ?>
						<li><a href="<?php echo esc_url( $dgf_url ); ?>" target="_blank" rel="noopener" aria-label="<?php echo esc_attr( ucfirst( $dgf_network ) ); ?>"><?php echo dgf_icon( $dgf_network, 18 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>

		<?php foreach ( $dgf_menus as $dgf_location => $dgf_label ) : ?>
			<?php if ( has_nav_menu( $dgf_location ) ) : ?>
				<nav class="dgf-footer__col" aria-label="<?php echo esc_attr( $dgf_label ); ?>">
					<h2 class="dgf-footer__title"><?php echo esc_html( wp_get_nav_menu_name( $dgf_location ) && 0 !== strpos( wp_get_nav_menu_name( $dgf_location ), 'Footer' ) ? wp_get_nav_menu_name( $dgf_location ) : $dgf_label ); ?></h2>
					<?php
					wp_nav_menu(
						array(
							'theme_location' => $dgf_location,
							'container'      => false,
							'menu_class'     => 'dgf-footer__menu',
							'depth'          => 1,
						)
					);
					?>
				</nav>
			<?php endif; ?>
		<?php endforeach; ?>

		<div class="dgf-footer__col dgf-footer__contact">
			<h2 class="dgf-footer__title"><?php esc_html_e( 'Get in touch', 'dgf' ); ?></h2>
			<?php echo dgf_sc_contact_info(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<a class="dgf-btn dgf-btn--wa dgf-btn--sm" href="<?php echo esc_url( dgf_wa_url( dgf_wa_message_for() ) ); ?>" target="_blank" rel="noopener" data-dgf-wa><?php echo dgf_icon( 'whatsapp', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><?php esc_html_e( 'WhatsApp us', 'dgf' ); ?></span></a>
		</div>
	</div>

	<div class="dgf-footer__bar">
		<div class="dgf-wrap dgf-footer__bar-inner">
			<p class="dgf-footer__copy"><?php echo esc_html( str_replace( '{year}', wp_date( 'Y' ), dgf_opt( 'footer_copyright' ) ) ); ?></p>
			<?php
			if ( has_nav_menu( 'legal' ) ) {
				wp_nav_menu(
					array(
						'theme_location' => 'legal',
						'container'      => false,
						'menu_class'     => 'dgf-footer__legal',
						'depth'          => 1,
					)
				);
			}
			?>
		</div>
	</div>
</footer>

<a class="dgf-float-wa" href="<?php echo esc_url( dgf_wa_url( dgf_wa_message_for() ) ); ?>" target="_blank" rel="noopener" aria-label="<?php esc_attr_e( 'Chat on WhatsApp', 'dgf' ); ?>" data-dgf-wa>
	<?php echo dgf_icon( 'whatsapp', 30 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	<span class="dgf-float-wa__label"><?php esc_html_e( 'Quote on WhatsApp', 'dgf' ); ?></span>
</a>

<nav class="dgf-mobilebar" aria-label="<?php esc_attr_e( 'Quick contact', 'dgf' ); ?>">
	<?php if ( dgf_opt( 'phone_link' ) ) : ?>
		<a href="<?php echo esc_url( 'tel:' . preg_replace( '/[^\d+]/', '', dgf_opt( 'phone_link' ) ) ); ?>"><?php echo dgf_icon( 'phone', 20 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><?php esc_html_e( 'Call', 'dgf' ); ?></span></a>
	<?php else : ?>
		<a href="<?php echo esc_url( dgf_page_url( 'catalogues' ) ); ?>"><?php echo dgf_icon( 'file', 20 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><?php esc_html_e( 'Catalogues', 'dgf' ); ?></span></a>
	<?php endif; ?>
	<a class="is-wa" href="<?php echo esc_url( dgf_wa_url( dgf_wa_message_for() ) ); ?>" target="_blank" rel="noopener" data-dgf-wa><?php echo dgf_icon( 'whatsapp', 20 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><?php esc_html_e( 'WhatsApp', 'dgf' ); ?></span></a>
	<a href="<?php echo esc_url( dgf_page_url( 'get-a-free-quote' ) ); ?>"><?php echo dgf_icon( 'ruler', 20 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><?php esc_html_e( 'Free quote', 'dgf' ); ?></span></a>
</nav>

<div class="dgf-lightbox" hidden data-dgf-lightbox-root>
	<button class="dgf-lightbox__close" type="button" aria-label="<?php esc_attr_e( 'Close', 'dgf' ); ?>"><?php echo dgf_icon( 'close', 28 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button>
	<img alt="">
</div>

<?php wp_footer(); ?>
</body>
</html>
