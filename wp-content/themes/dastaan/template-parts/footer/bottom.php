<?php
/**
 * Footer bottom bar.
 *
 * @package Dastaan
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="footer-bottom">
	<div class="container footer-bottom__inner">
		<div class="footer-bottom__left">
			<p class="copyright"><?php echo esc_html( dastaan_placeholders( dastaan_option( 'footer_copyright' ) ) ); ?></p>
			<?php if ( dastaan_option( 'footer_legal_text' ) ) : ?><p class="legal-text"><?php echo esc_html( dastaan_option( 'footer_legal_text' ) ); ?></p><?php endif; ?>
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'legal',
					'container'      => false,
					'menu_class'     => 'legal-menu',
					'depth'          => 1,
					'fallback_cb'    => function () {
						$items = array();
						$pp    = get_option( 'wp_page_for_privacy_policy' );
						if ( $pp ) {
							$items[] = '<li><a href="' . esc_url( get_permalink( $pp ) ) . '">' . esc_html( get_the_title( $pp ) ) . '</a></li>';
						}
						if ( dastaan_has_wc() ) {
							$terms = wc_get_page_id( 'terms' );
							if ( $terms > 0 ) {
								$items[] = '<li><a href="' . esc_url( get_permalink( $terms ) ) . '">' . esc_html( get_the_title( $terms ) ) . '</a></li>';
							}
							$refund = wc_get_page_id( 'refund_returns' );
							if ( $refund > 0 ) {
								$items[] = '<li><a href="' . esc_url( get_permalink( $refund ) ) . '">' . esc_html( get_the_title( $refund ) ) . '</a></li>';
							}
						}
						if ( $items ) {
							echo '<ul class="legal-menu">' . implode( '', $items ) . '</ul>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						}
					},
				)
			);
			?>
		</div>
		<div class="footer-bottom__right">
			<?php if ( dastaan_option( 'footer_payment_icons' ) ) { dastaan_payment_icons(); } ?>
			<p class="made-in"><?php dastaan_the_icon( 'needle', array( 'size' => 14 ) ); ?><?php esc_html_e( 'Handcrafted in India · Studio in Seawoods, Navi Mumbai', 'dastaan' ); ?></p>
		</div>
	</div>
</div>
