<?php
/**
 * Contact form: [bzr_contact_form]. Posts to admin-post.php, emails the store owner.
 * Opt in with add_theme_support( 'bazaar-contact-form' ).
 *
 * Protections: nonce, honeypot, minimum-fill-time check, per-IP rate limit,
 * sanitized input, and no user-controlled values in mail headers except a validated Reply-To.
 *
 * @package BazaarCore
 */

defined( 'ABSPATH' ) || exit;

/**
 * Render the form.
 *
 * @return string
 */
function bzr_contact_form_shortcode() {
	if ( ! bzr_feature( 'contact-form' ) ) {
		return '';
	}

	// Result flag from the redirect. Only known values are honoured.
	$result = isset( $_GET['bzr_contact'] ) ? sanitize_key( wp_unslash( $_GET['bzr_contact'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
	$notice = '';
	if ( 'sent' === $result ) {
		$notice = '<p class="bzr-form__notice is-success" role="status">' . esc_html__( 'Thank you! Your message has been sent. We will reply soon.', 'bazaar-core' ) . '</p>';
	} elseif ( 'busy' === $result ) {
		$notice = '<p class="bzr-form__notice is-error" role="alert">' . esc_html__( 'Too many messages were sent from your connection. Please wait a few minutes and try again.', 'bazaar-core' ) . '</p>';
	} elseif ( 'error' === $result ) {
		$notice = '<p class="bzr-form__notice is-error" role="alert">' . esc_html__( 'Sorry, we could not send your message. Please check the fields and try again.', 'bazaar-core' ) . '</p>';
	}

	ob_start();
	echo $notice; // phpcs:ignore WordPress.Security.EscapeOutput -- built from escaped strings above.
	?>
	<form class="bzr-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
		<input type="hidden" name="action" value="bzr_contact" />
		<input type="hidden" name="bzr_ts" value="<?php echo esc_attr( (string) time() ); ?>" />
		<?php wp_nonce_field( 'bzr_contact', 'bzr_nonce' ); ?>
		<p>
			<label for="bzr-c-name"><?php esc_html_e( 'Your name', 'bazaar-core' ); ?></label>
			<input type="text" id="bzr-c-name" name="name" required maxlength="100" autocomplete="name" />
		</p>
		<p>
			<label for="bzr-c-email"><?php esc_html_e( 'Email', 'bazaar-core' ); ?></label>
			<input type="email" id="bzr-c-email" name="email" required maxlength="100" autocomplete="email" />
		</p>
		<p>
			<label for="bzr-c-order"><?php esc_html_e( 'Order number (optional)', 'bazaar-core' ); ?></label>
			<input type="text" id="bzr-c-order" name="order" maxlength="40" />
		</p>
		<p>
			<label for="bzr-c-message"><?php esc_html_e( 'How can we help?', 'bazaar-core' ); ?></label>
			<textarea id="bzr-c-message" name="message" rows="6" required maxlength="3000"></textarea>
		</p>
		<div class="bzr-otp__hp" aria-hidden="true"><label>Website<input type="text" name="website" tabindex="-1" autocomplete="off" /></label></div>
		<p><button type="submit" class="wp-element-button"><?php esc_html_e( 'Send message', 'bazaar-core' ); ?></button></p>
	</form>
	<?php
	return (string) ob_get_clean();
}
add_shortcode( 'bzr_contact_form', 'bzr_contact_form_shortcode' );

/**
 * Redirect back to the form with a result flag.
 *
 * @param string $flag sent|error|busy.
 */
function bzr_contact_redirect( $flag ) {
	$back = wp_get_referer();
	$back = $back ? $back : home_url( '/' );
	wp_safe_redirect( add_query_arg( 'bzr_contact', $flag, remove_query_arg( 'bzr_contact', $back ) ) );
	exit;
}

/**
 * Handle submission.
 */
function bzr_contact_handle() {
	if ( ! bzr_feature( 'contact-form' ) ) {
		bzr_contact_redirect( 'error' );
	}

	$nonce = isset( $_POST['bzr_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['bzr_nonce'] ) ) : '';
	if ( ! wp_verify_nonce( $nonce, 'bzr_contact' ) ) {
		bzr_contact_redirect( 'error' );
	}

	// Honeypot filled, or submitted faster than a human could (< 3s): pretend success.
	$ts = isset( $_POST['bzr_ts'] ) ? absint( $_POST['bzr_ts'] ) : 0;
	if ( ! empty( $_POST['website'] ) || ( time() - $ts ) < 3 ) {
		bzr_contact_redirect( 'sent' );
	}

	list( $max, $window ) = bzr_limit( 'contact_ip' );
	if ( ! bzr_rate_limit( 'contact-ip:' . bzr_client_ip(), $max, $window ) ) {
		bzr_contact_redirect( 'busy' );
	}

	$name    = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
	$email   = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$order   = isset( $_POST['order'] ) ? sanitize_text_field( wp_unslash( $_POST['order'] ) ) : '';
	$message = isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '';

	if ( '' === $name || ! is_email( $email ) || '' === $message ) {
		bzr_contact_redirect( 'error' );
	}

	$to      = is_email( bzr_option( 'store_email' ) ) ? bzr_option( 'store_email' ) : get_option( 'admin_email' );
	$subject = sprintf(
		/* translators: 1: site name, 2: sender name */
		__( '[%1$s] New message from %2$s', 'bazaar-core' ),
		wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ),
		// Strip control characters so the name cannot break the Subject header.
		preg_replace( '/[\r\n\t]+/', ' ', $name )
	);
	$body    = sprintf(
		"%s\n%s\n%s\n\n%s",
		sprintf( /* translators: %s: name */ __( 'Name: %s', 'bazaar-core' ), $name ),
		sprintf( /* translators: %s: email */ __( 'Email: %s', 'bazaar-core' ), $email ),
		$order ? sprintf( /* translators: %s: order number */ __( 'Order: %s', 'bazaar-core' ), $order ) : '',
		$message
	);

	$sent = wp_mail( $to, $subject, $body, array( 'Reply-To: ' . $email ) );

	bzr_contact_redirect( $sent ? 'sent' : 'error' );
}
add_action( 'admin_post_nopriv_bzr_contact', 'bzr_contact_handle' );
add_action( 'admin_post_bzr_contact', 'bzr_contact_handle' );
