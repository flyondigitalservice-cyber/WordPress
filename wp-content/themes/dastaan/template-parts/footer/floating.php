<?php
/**
 * Floating elements: WhatsApp, back to top, cookie consent, toast.
 *
 * @package Dastaan
 */

defined( 'ABSPATH' ) || exit;
$dastaan_wa = dastaan_whatsapp_url();
?>
<?php if ( dastaan_option( 'show_whatsapp_float' ) && $dastaan_wa && ! ( dastaan_has_wc() && is_checkout() ) ) : ?>
	<aside class="floating-help" aria-label="<?php esc_attr_e( 'Quick help', 'dastaan' ); ?>"><a class="wa-float" href="<?php echo esc_url( $dastaan_wa ); ?>" target="_blank" rel="noopener" aria-label="<?php esc_attr_e( 'Chat on WhatsApp', 'dastaan' ); ?>"><?php dastaan_the_icon( 'whatsapp', array( 'size' => 26 ) ); ?><span class="wa-float__label"><?php esc_html_e( 'Need help?', 'dastaan' ); ?></span></a></aside>
<?php endif; ?>
<button class="back-to-top" type="button" aria-label="<?php esc_attr_e( 'Back to top', 'dastaan' ); ?>"><?php dastaan_the_icon( 'arrow-up', array( 'size' => 18 ) ); ?></button>
<div class="toast" role="status" aria-live="polite" hidden></div>
<?php if ( dastaan_option( 'consent_enable' ) && ( dastaan_option( 'fb_pixel_id' ) || dastaan_option( 'ga4_id' ) || dastaan_option( 'clarity_id' ) ) ) : ?>
	<div class="consent" role="dialog" aria-label="<?php esc_attr_e( 'Cookie consent', 'dastaan' ); ?>" hidden>
		<p><?php echo esc_html( dastaan_option( 'consent_text' ) ); ?>
			<?php $dastaan_pp = get_option( 'wp_page_for_privacy_policy' ); ?>
			<?php if ( $dastaan_pp ) : ?><a href="<?php echo esc_url( get_permalink( $dastaan_pp ) ); ?>"><?php esc_html_e( 'Learn more', 'dastaan' ); ?></a><?php endif; ?>
		</p>
		<div class="consent__actions">
			<button type="button" class="btn btn--small btn--ghost js-consent" data-consent="denied"><?php echo esc_html( dastaan_option( 'consent_decline' ) ); ?></button>
			<button type="button" class="btn btn--small btn--primary js-consent" data-consent="granted"><?php echo esc_html( dastaan_option( 'consent_accept' ) ); ?></button>
		</div>
	</div>
<?php endif; ?>
