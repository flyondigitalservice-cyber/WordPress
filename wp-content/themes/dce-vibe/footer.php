<?php
/**
 * Footer. Columns are menus (Appearance → Menus); texts and contacts are in the Customizer.
 *
 * @package dce-vibe
 */

defined( 'ABSPATH' ) || exit;

$dce_footer_menus = array(
	'footer_curtains' => __( 'Curtains', 'dce-vibe' ),
	'footer_blinds'   => __( 'Blinds', 'dce-vibe' ),
	'footer_company'  => __( 'Company', 'dce-vibe' ),
);
$dce_locations    = get_nav_menu_locations();
?>

<?php if ( dce_opt( 'parent_url' ) && dce_opt( 'parent_text' ) ) : ?>
<aside class="dce-parent-strip" aria-label="<?php esc_attr_e( 'Parent company', 'dce-vibe' ); ?>">
	<div class="dce-container dce-parent-strip-inner">
		<p><?php echo esc_html( dce_opt( 'parent_text' ) ); ?></p>
		<a class="dce-btn dce-btn-ghost dce-btn-sm" href="<?php echo esc_url( dce_opt( 'parent_url' ) ); ?>" target="_blank" rel="noopener"><?php printf( esc_html__( 'Visit %s', 'dce-vibe' ), esc_html( dce_opt( 'parent_name' ) ) ); ?> <?php echo dce_icon( 'arrow', 14 ); // phpcs:ignore ?></a>
	</div>
</aside>
<?php endif; ?>

<footer class="dce-footer" id="colophon">
	<div class="dce-container dce-footer-grid">
		<div class="dce-footer-col dce-footer-brandcol">
			<a class="dce-brand dce-brand-light" href="<?php echo esc_url( home_url( '/' ) ); ?>">
				<span class="dce-brand-logo"><?php echo dce_logo_html( true ); // phpcs:ignore ?></span>
				<span class="dce-brand-text"><strong><?php echo esc_html( dce_opt( 'brand' ) ); ?></strong><?php if ( dce_opt( 'brand_sub' ) ) : ?><em><?php echo esc_html( dce_opt( 'brand_sub' ) ); ?></em><?php endif; ?></span>
			</a>
			<p class="dce-footer-about"><?php echo esc_html( dce_opt( 'footer_about' ) ); ?></p>
			<p class="dce-footer-hours"><?php echo dce_icon( 'clock', 14 ); // phpcs:ignore ?> <?php echo esc_html( dce_opt( 'hours' ) ); ?></p>
			<?php $dce_social = dce_social_links(); if ( $dce_social ) : ?>
			<div class="dce-footer-social">
				<?php foreach ( $dce_social as $net => $url ) : ?>
					<a href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener" aria-label="<?php echo esc_attr( ucfirst( $net ) ); ?>"><?php echo dce_icon( $net, 16 ); // phpcs:ignore ?></a>
				<?php endforeach; ?>
			</div>
			<?php endif; ?>
			<?php if ( is_active_sidebar( 'footer-extra' ) ) { dynamic_sidebar( 'footer-extra' ); } ?>
		</div>

		<?php foreach ( $dce_footer_menus as $dce_loc => $dce_label ) : ?>
		<div class="dce-footer-col">
			<?php
			$dce_title = $dce_label;
			if ( ! empty( $dce_locations[ $dce_loc ] ) ) {
				$dce_menu = wp_get_nav_menu_object( $dce_locations[ $dce_loc ] );
				if ( $dce_menu ) {
					$dce_title = $dce_menu->name;
				}
			}
			?>
			<h3><?php echo esc_html( $dce_title ); ?></h3>
			<?php
			wp_nav_menu(
				array(
					'theme_location' => $dce_loc,
					'container'      => false,
					'depth'          => 1,
					'fallback_cb'    => '__return_empty_string',
				)
			);
			?>
		</div>
		<?php endforeach; ?>

		<div class="dce-footer-col">
			<h3><?php esc_html_e( 'Contact', 'dce-vibe' ); ?></h3>
			<ul class="dce-footer-contact">
				<li><?php echo dce_icon( 'pin', 15 ); // phpcs:ignore ?><a href="<?php echo esc_url( dce_opt( 'map_url' ) ); ?>" target="_blank" rel="noopener"><?php echo esc_html( dce_opt( 'address' ) ); ?></a></li>
				<li><?php echo dce_icon( 'phone', 15 ); // phpcs:ignore ?><a href="<?php echo esc_url( dce_tel_url() ); ?>"><?php echo esc_html( dce_opt( 'phone' ) ); ?></a></li>
				<li><?php echo dce_icon( 'whatsapp', 15 ); // phpcs:ignore ?><a href="<?php echo esc_url( dce_wa_url( dce_page_wa_message() ) ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'WhatsApp us', 'dce-vibe' ); ?></a></li>
				<li><?php echo dce_icon( 'mail', 15 ); // phpcs:ignore ?><a href="mailto:<?php echo esc_attr( antispambot( dce_opt( 'email' ) ) ); ?>"><?php echo esc_html( antispambot( dce_opt( 'email' ) ) ); ?></a></li>
			</ul>
			<a class="dce-btn dce-btn-wa dce-btn-sm dce-footer-cta" href="<?php echo esc_url( dce_wa_url( dce_page_wa_message() ) ); ?>" target="_blank" rel="noopener"><?php echo dce_icon( 'whatsapp', 16 ); // phpcs:ignore ?> <?php esc_html_e( 'Book a free home visit', 'dce-vibe' ); ?></a>
		</div>
	</div>

	<div class="dce-container dce-footer-bottom">
		<span><?php echo esc_html( dce_opt( 'footer_copy' ) ); ?></span>
		<?php
		wp_nav_menu(
			array(
				'theme_location'  => 'footer_legal',
				'container'       => 'nav',
				'container_aria_label' => __( 'Legal', 'dce-vibe' ),
				'depth'           => 1,
				'fallback_cb'     => '__return_empty_string',
			)
		);
		?>
		<?php if ( dce_opt( 'parent_url' ) ) : ?>
		<span><?php esc_html_e( 'Part of', 'dce-vibe' ); ?> <a href="<?php echo esc_url( dce_opt( 'parent_url' ) ); ?>" target="_blank" rel="noopener"><?php echo esc_html( dce_opt( 'parent_name' ) ); ?></a></span>
		<?php endif; ?>
	</div>
</footer>

<?php if ( dce_opt( 'float_wa' ) ) : ?>
<a class="dce-float-wa" href="<?php echo esc_url( dce_wa_url( dce_page_wa_message() ) ); ?>" target="_blank" rel="noopener" aria-label="<?php esc_attr_e( 'Chat with us on WhatsApp', 'dce-vibe' ); ?>"><?php echo dce_icon( 'whatsapp', 30 ); // phpcs:ignore ?></a>
<?php endif; ?>

<?php if ( dce_opt( 'mobile_bar' ) ) : ?>
<nav class="dce-mobile-bar" aria-label="<?php esc_attr_e( 'Quick contact', 'dce-vibe' ); ?>">
	<a href="<?php echo esc_url( dce_tel_url() ); ?>"><?php echo dce_icon( 'phone', 18 ); // phpcs:ignore ?><?php esc_html_e( 'Call', 'dce-vibe' ); ?></a>
	<a class="is-wa" href="<?php echo esc_url( dce_wa_url( dce_page_wa_message() ) ); ?>" target="_blank" rel="noopener"><?php echo dce_icon( 'whatsapp', 18 ); // phpcs:ignore ?><?php esc_html_e( 'WhatsApp', 'dce-vibe' ); ?></a>
	<a href="<?php echo esc_url( ( is_singular() && false !== strpos( (string) get_post_field( 'post_content', get_queried_object_id() ), 'dce_lead_form' ) ) ? '#quote' : dce_quote_url() ); ?>"><?php echo dce_icon( 'calendar', 18 ); // phpcs:ignore ?><?php esc_html_e( 'Free visit', 'dce-vibe' ); ?></a>
</nav>
<?php endif; ?>

<?php wp_footer(); ?>
</body>
</html>
