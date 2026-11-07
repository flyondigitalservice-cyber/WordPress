<?php
/**
 * Reusable WhatsApp CTA band.
 *
 * @package Tasdir
 */

defined( 'ABSPATH' ) || exit;
?>
<section class="section" style="padding-top:0">
	<div class="wrap">
		<div class="cta-band">
			<h2><?php esc_html_e( 'Need a quotation?', 'tasdir' ); ?></h2>
			<p><?php esc_html_e( 'Message our export desk for today’s prices, packing options and shipment dates.', 'tasdir' ); ?></p>
			<div class="btns">
				<a class="btn btn--wa" href="<?php echo esc_url( tasdir_wa_url() ); ?>" target="_blank" rel="noopener"><?php tasdir_the_icon( 'whatsapp' ); ?><?php esc_html_e( 'Chat on WhatsApp', 'tasdir' ); ?></a>
				<a class="btn btn--ghost" href="<?php echo esc_url( home_url( '/#enquiry' ) ); ?>"><?php esc_html_e( 'Send enquiry form', 'tasdir' ); ?></a>
			</div>
		</div>
	</div>
</section>
