<?php
/**
 * [lw_whatsapp_form] — contact form that opens WhatsApp with the message prefilled.
 *
 * @package luxurywatchs
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render the WhatsApp contact form.
 *
 * @return string
 */
function lw_whatsapp_form_shortcode() {
	ob_start();
	?>
	<form class="lw-waform" action="https://wa.me/<?php echo esc_attr( lw_whatsapp_number() ); ?>" method="get" target="_blank" data-waform>
		<div class="lw-waform__row">
			<label for="lw-wa-name"><?php esc_html_e( 'Your name', 'luxurywatchs' ); ?></label>
			<input id="lw-wa-name" type="text" data-field="Name" required autocomplete="name">
		</div>
		<div class="lw-waform__row">
			<label for="lw-wa-city"><?php esc_html_e( 'City / PIN code', 'luxurywatchs' ); ?></label>
			<input id="lw-wa-city" type="text" data-field="City" autocomplete="postal-code">
		</div>
		<div class="lw-waform__row">
			<label for="lw-wa-topic"><?php esc_html_e( 'I want to', 'luxurywatchs' ); ?></label>
			<select id="lw-wa-topic" data-field="Topic">
				<option><?php esc_html_e( 'Buy a watch', 'luxurywatchs' ); ?></option>
				<option><?php esc_html_e( 'Know the price of a watch', 'luxurywatchs' ); ?></option>
				<option><?php esc_html_e( 'See a live video', 'luxurywatchs' ); ?></option>
				<option><?php esc_html_e( 'Track my order', 'luxurywatchs' ); ?></option>
				<option><?php esc_html_e( 'Warranty / service', 'luxurywatchs' ); ?></option>
				<option><?php esc_html_e( 'Something else', 'luxurywatchs' ); ?></option>
			</select>
		</div>
		<div class="lw-waform__row">
			<label for="lw-wa-msg"><?php esc_html_e( 'Message', 'luxurywatchs' ); ?></label>
			<textarea id="lw-wa-msg" rows="4" data-field="Message" placeholder="<?php esc_attr_e( 'Product code (e.g. LW-0927-03), style or budget…', 'luxurywatchs' ); ?>"></textarea>
		</div>
		<input type="hidden" name="text" value="<?php esc_attr_e( 'Hi LuxuryWatchs! I have a question.', 'luxurywatchs' ); ?>">
		<button class="lw-btn lw-btn--wa lw-btn--block" type="submit"><?php echo lw_whatsapp_icon(); // phpcs:ignore ?><?php esc_html_e( 'Send on WhatsApp', 'luxurywatchs' ); ?></button>
		<p class="lw-waform__note"><?php esc_html_e( 'Opens WhatsApp with your message ready to send. We reply Mon–Sat, 10am–8pm IST.', 'luxurywatchs' ); ?></p>
	</form>
	<?php
	return ob_get_clean();
}
add_shortcode( 'lw_whatsapp_form', 'lw_whatsapp_form_shortcode' );
