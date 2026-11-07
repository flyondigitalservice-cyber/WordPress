<?php
/**
 * Enquiry form → saves lead → opens WhatsApp with the details pre-filled.
 *
 * @package Tasdir
 */

defined( 'ABSPATH' ) || exit;

$tasdir_selected = isset( $args['product'] ) ? (string) $args['product'] : '';
$tasdir_names    = get_posts(
	array(
		'post_type'      => 'tasdir_product',
		'posts_per_page' => 30,
		'orderby'        => 'menu_order',
		'order'          => 'ASC',
		'fields'         => 'ids',
		'no_found_rows'  => true,
	)
);
$tasdir_sent     = isset( $_GET['enquiry'] ) && 'sent' === $_GET['enquiry']; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
?>
<div class="form-card">
	<h3><?php esc_html_e( 'Request a quotation', 'tasdir' ); ?></h3>
	<p><?php esc_html_e( 'Reply within business hours. Your details open straight in WhatsApp — no waiting for email.', 'tasdir' ); ?></p>

	<form class="lead-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" novalidate>
		<input type="hidden" name="action" value="tasdir_lead">
		<input type="hidden" name="page" value="">
		<input type="hidden" name="source" value="">
		<input type="hidden" name="tasdir_t" value="">
		<div class="hp-field" aria-hidden="true">
			<label><?php esc_html_e( 'Website', 'tasdir' ); ?> <input type="text" name="website" tabindex="-1" autocomplete="off"></label>
		</div>

		<div class="form-grid form-grid--2">
			<div class="field">
				<label for="lf-name"><?php esc_html_e( 'Full name', 'tasdir' ); ?> <span class="req">*</span></label>
				<input id="lf-name" name="name" type="text" autocomplete="name" required>
			</div>
			<div class="field">
				<label for="lf-phone"><?php esc_html_e( 'WhatsApp number', 'tasdir' ); ?> <span class="req">*</span></label>
				<input id="lf-phone" name="phone" type="tel" inputmode="tel" autocomplete="tel" placeholder="+971 50 123 4567" required>
			</div>
			<div class="field">
				<label for="lf-company"><?php esc_html_e( 'Company', 'tasdir' ); ?></label>
				<input id="lf-company" name="company" type="text" autocomplete="organization">
			</div>
			<div class="field">
				<label for="lf-country"><?php esc_html_e( 'Destination country', 'tasdir' ); ?></label>
				<input id="lf-country" name="country" type="text" autocomplete="country-name">
			</div>
			<div class="field">
				<label for="lf-product"><?php esc_html_e( 'Product', 'tasdir' ); ?></label>
				<select id="lf-product" name="product">
					<?php foreach ( $tasdir_names as $tasdir_pid ) : ?>
						<?php $tasdir_t = get_the_title( $tasdir_pid ); ?>
						<option value="<?php echo esc_attr( $tasdir_t ); ?>" <?php selected( $tasdir_selected, $tasdir_t ); ?>><?php echo esc_html( $tasdir_t ); ?></option>
					<?php endforeach; ?>
					<option value="<?php esc_attr_e( 'Multiple / other', 'tasdir' ); ?>"><?php esc_html_e( 'Multiple / other', 'tasdir' ); ?></option>
				</select>
			</div>
			<div class="field">
				<label for="lf-qty"><?php esc_html_e( 'Quantity', 'tasdir' ); ?></label>
				<input id="lf-qty" name="quantity" type="text" placeholder="<?php esc_attr_e( 'e.g. 2 × 40ft reefer / month', 'tasdir' ); ?>">
			</div>
			<div class="field field--full">
				<label for="lf-email"><?php esc_html_e( 'Email', 'tasdir' ); ?></label>
				<input id="lf-email" name="email" type="email" autocomplete="email">
			</div>
			<div class="field field--full">
				<label for="lf-msg"><?php esc_html_e( 'Requirements', 'tasdir' ); ?></label>
				<textarea id="lf-msg" name="message" rows="3" placeholder="<?php esc_attr_e( 'Destination port, packing, Incoterms (FOB/CIF), target price…', 'tasdir' ); ?>"></textarea>
			</div>
		</div>

		<button type="submit" class="btn btn--wa btn--block" style="margin-top:18px"><?php tasdir_the_icon( 'whatsapp' ); ?><span><?php esc_html_e( 'Send enquiry on WhatsApp', 'tasdir' ); ?></span></button>
		<p class="form-status<?php echo $tasdir_sent ? ' is-ok' : ''; ?>" role="status" aria-live="polite"><?php echo $tasdir_sent ? esc_html__( 'Thank you! Your enquiry has been received. Our export team will contact you shortly.', 'tasdir' ) : ''; ?></p>
		<p class="form-note"><?php esc_html_e( 'We use your details only to reply to this enquiry.', 'tasdir' ); ?></p>
	</form>
</div>
