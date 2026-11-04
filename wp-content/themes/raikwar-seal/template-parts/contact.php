<?php
/**
 * Contact block: details + form (Contact Form 7 / WPForms if present).
 */
$form = '';
if ( shortcode_exists( 'contact-form-7' ) ) {
	$cf = get_posts( array( 'post_type' => 'wpcf7_contact_form', 'numberposts' => 1, 'post_status' => 'publish' ) );
	if ( $cf ) {
		$form = do_shortcode( '[contact-form-7 id="' . (int) $cf[0]->ID . '"]' );
	}
}
if ( ! $form && shortcode_exists( 'wpforms' ) ) {
	$wf = get_posts( array( 'post_type' => 'wpforms', 'numberposts' => 1, 'post_status' => 'publish' ) );
	if ( $wf ) {
		$form = do_shortcode( '[wpforms id="' . (int) $wf[0]->ID . '"]' );
	}
}
?>
<section class="section" id="contact">
	<div class="wrap contact-grid">
		<div>
			<div class="label">Contact</div>
			<h2 style="font-size:clamp(34px,4.6vw,58px)">Talk to a real person today.</h2>
			<p style="color:var(--ink-2);font-size:18px;max-width:480px;margin-bottom:36px">Call, WhatsApp or drop a message. We reply quickly during working hours.</p>
			<ul class="contact-list">
				<li><span class="ic"><?php echo raikwar_icon( 'phone' ); ?></span><div><small>Phone</small>
					<a href="<?php echo esc_attr( raikwar_tel( raikwar_info( 'phone1' ) ) ); ?>"><?php echo esc_html( raikwar_phone_label( raikwar_info( 'phone1' ) ) ); ?></a>
					<?php if ( raikwar_info( 'phone2' ) ) : ?><br><a href="<?php echo esc_attr( raikwar_tel( raikwar_info( 'phone2' ) ) ); ?>"><?php echo esc_html( raikwar_phone_label( raikwar_info( 'phone2' ) ) ); ?></a><?php endif; ?>
				</div></li>
				<li><span class="ic"><?php echo raikwar_icon( 'mail' ); ?></span><div><small>Email</small><a href="mailto:<?php echo esc_attr( antispambot( raikwar_info( 'email' ) ) ); ?>"><?php echo esc_html( antispambot( raikwar_info( 'email' ) ) ); ?></a>
					<?php if ( raikwar_info( 'email2' ) ) : ?><br><a href="mailto:<?php echo esc_attr( antispambot( raikwar_info( 'email2' ) ) ); ?>"><?php echo esc_html( antispambot( raikwar_info( 'email2' ) ) ); ?></a><?php endif; ?>
				</div></li>
				<li><span class="ic"><?php echo raikwar_icon( 'pin' ); ?></span><div><small>Offices</small><a href="<?php echo esc_url( raikwar_info( 'map_url' ) ); ?>" target="_blank" rel="noopener"><?php echo esc_html( raikwar_info( 'address' ) ); ?></a>
					<?php if ( raikwar_info( 'address2' ) ) : ?><br><span style="font-weight:600;font-size:18px"><?php echo esc_html( raikwar_info( 'address2' ) ); ?></span><?php endif; ?>
				</div></li>
				<li><span class="ic"><?php echo raikwar_icon( 'clock' ); ?></span><div><small>Hours</small><span style="font-weight:600;font-size:18px"><?php echo esc_html( raikwar_info( 'hours' ) ); ?></span></div></li>
			</ul>
		</div>
		<div class="contact-card">
			<?php if ( $form ) : ?>
				<h3 style="font-size:30px">Send an enquiry</h3>
				<?php echo $form; // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<?php else : ?>
				<h3 style="font-size:30px">Quickest way: WhatsApp</h3>
				<p style="color:var(--ink-2)">Send us the service you need and a photo of your PAN card. We’ll reply with the document checklist and fee.</p>
				<a class="btn btn--wa" href="<?php echo esc_url( raikwar_wa() ); ?>" target="_blank" rel="noopener"><?php echo raikwar_icon( 'wa' ); ?>Start WhatsApp chat</a>
			<?php endif; ?>
		</div>
	</div>
</section>
