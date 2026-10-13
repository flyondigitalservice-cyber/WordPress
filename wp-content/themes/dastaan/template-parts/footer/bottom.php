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
						// Published legal pages only: the settings first, then common slugs.
						$items = array();
						$pages = array(
							array( (int) get_option( 'wp_page_for_privacy_policy' ), array( 'privacy-policy', 'privacy-policy-2', 'privacy' ) ),
							array( dastaan_has_wc() ? wc_get_page_id( 'terms' ) : 0, array( 'terms-conditions', 'terms-and-conditions', 'terms' ) ),
							array( dastaan_has_wc() ? wc_get_page_id( 'refund_returns' ) : 0, array( 'refund_returns', 'returns-exchanges', 'refund-policy' ) ),
						);
						foreach ( $pages as $candidate ) {
							$page = ( $candidate[0] > 0 && 'publish' === get_post_status( $candidate[0] ) ) ? get_post( $candidate[0] ) : dastaan_find_page( $candidate[1] );
							if ( $page ) {
								$items[ $page->ID ] = '<li><a href="' . esc_url( get_permalink( $page ) ) . '">' . esc_html( get_the_title( $page ) ) . '</a></li>';
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
