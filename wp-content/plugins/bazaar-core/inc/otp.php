<?php
/**
 * Passwordless email OTP login/sign-up for customers.
 *
 * Opt in with add_theme_support( 'bazaar-otp' ). Place the form with [bzr_otp_login].
 *
 * Delivery is pluggable so SMS/WhatsApp providers can be added later:
 *   add_filter( 'bzr_otp_deliver', function ( $handled, $email, $code ) { ...send...; return true; }, 10, 3 );
 *
 * Security notes:
 *  - Codes are 6 digits from random_int(), stored only as an HMAC, expire in 10 minutes.
 *  - 5 wrong guesses invalidate the code. Sends are rate-limited per email and per IP
 *    (proxy/CDN aware, see bzr_client_ip()); limits are filterable via `bzr_rate_limits`.
 *  - New accounts are only created when WooCommerce allows registration on My Account
 *    (WooCommerce > Settings > Accounts & Privacy), or via the `bzr_otp_allow_signup` filter.
 *  - Responses never reveal whether an account exists.
 *  - Only customer/subscriber accounts can sign in this way. Admins, editors and shop
 *    managers must use their password.
 *
 * @package BazaarCore
 */

defined( 'ABSPATH' ) || exit;

const BZR_OTP_TTL          = 600; // Seconds a code stays valid.
const BZR_OTP_MAX_ATTEMPTS = 5;

/**
 * Whether the feature is on.
 *
 * @return bool
 */
function bzr_otp_enabled() {
	return bzr_feature( 'otp' );
}

/**
 * May the OTP form create new customer accounts?
 *
 * Follows WooCommerce's "Allow customers to create an account on the My account page" setting.
 *
 * @return bool
 */
function bzr_otp_signup_allowed() {
	$allowed = function_exists( 'wc_create_new_customer' ) && 'yes' === get_option( 'woocommerce_enable_myaccount_registration', 'no' );
	return (bool) apply_filters( 'bzr_otp_allow_signup', $allowed );
}

/**
 * HMAC of a code, bound to the email so codes are not interchangeable.
 *
 * @param string $email Email.
 * @param string $code  Code.
 * @return string
 */
function bzr_otp_hash( $email, $code ) {
	return hash_hmac( 'sha256', $email . '|' . $code, wp_salt( 'auth' ) );
}

/**
 * Deliver a code. Default: email.
 *
 * @param string $email Recipient.
 * @param string $code  The code.
 * @return bool
 */
function bzr_otp_deliver( $email, $code ) {
	$handled = apply_filters( 'bzr_otp_deliver', null, $email, $code );
	if ( null !== $handled ) {
		return (bool) $handled;
	}

	$site    = wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES );
	$subject = sprintf(
		/* translators: 1: one-time code, 2: site name */
		__( '%1$s is your %2$s login code', 'bazaar-core' ),
		$code,
		$site
	);
	$message = sprintf(
		/* translators: 1: one-time code, 2: minutes */
		__( "Your one-time login code is: %1\$s\n\nIt expires in %2\$d minutes. If you did not request it, you can ignore this email.", 'bazaar-core' ),
		$code,
		(int) ( BZR_OTP_TTL / 60 )
	);

	return wp_mail( $email, $subject, $message );
}

/**
 * REST: request a code.
 *
 * @param WP_REST_Request $request Request.
 * @return WP_REST_Response|WP_Error
 */
function bzr_otp_rest_request( WP_REST_Request $request ) {
	$generic = new WP_REST_Response( array( 'message' => __( 'If the address is valid, a code is on its way.', 'bazaar-core' ) ), 200 );

	// Honeypot: bots fill hidden fields.
	if ( '' !== (string) $request->get_param( 'website' ) ) {
		return $generic;
	}

	$email = sanitize_email( (string) $request->get_param( 'email' ) );
	if ( ! is_email( $email ) ) {
		return new WP_Error( 'bzr_otp_email', __( 'Please enter a valid email address.', 'bazaar-core' ), array( 'status' => 400 ) );
	}
	$email = strtolower( $email );

	list( $ip_max, $ip_window ) = bzr_limit( 'otp_ip' );
	list( $em_max, $em_window ) = bzr_limit( 'otp_email' );
	if ( ! bzr_rate_limit( 'otp-ip:' . bzr_client_ip(), $ip_max, $ip_window ) || ! bzr_rate_limit( 'otp-em:' . $email, $em_max, $em_window ) ) {
		return new WP_Error( 'bzr_otp_rate', __( 'Too many requests. Please wait a few minutes and try again.', 'bazaar-core' ), array( 'status' => 429 ) );
	}

	$user = get_user_by( 'email', $email );
	if ( $user && ! bzr_otp_user_allowed( $user ) ) {
		// Do not reveal this; behave as if sent.
		return $generic;
	}
	if ( ! $user && ! bzr_otp_signup_allowed() ) {
		// Sign-ups are closed: send nothing, but do not reveal that the account does not exist.
		return $generic;
	}

	$code = str_pad( (string) random_int( 0, 999999 ), 6, '0', STR_PAD_LEFT );
	set_transient(
		'bzr_otp_' . md5( $email ),
		array(
			'hash'     => bzr_otp_hash( $email, $code ),
			'attempts' => 0,
		),
		BZR_OTP_TTL
	);

	bzr_otp_deliver( $email, $code );

	return $generic;
}

/**
 * Only low-privilege accounts may use OTP sign-in.
 *
 * @param WP_User $user User.
 * @return bool
 */
function bzr_otp_user_allowed( $user ) {
	$roles = (array) $user->roles;
	return ! empty( $roles ) && ! array_diff( $roles, array( 'customer', 'subscriber' ) );
}

/**
 * REST: verify a code and sign in (creating the customer when needed).
 *
 * @param WP_REST_Request $request Request.
 * @return WP_REST_Response|WP_Error
 */
function bzr_otp_rest_verify( WP_REST_Request $request ) {
	$email = strtolower( sanitize_email( (string) $request->get_param( 'email' ) ) );
	$code  = preg_replace( '/\D+/', '', (string) $request->get_param( 'code' ) );
	$fail  = new WP_Error( 'bzr_otp_invalid', __( 'That code is incorrect or has expired.', 'bazaar-core' ), array( 'status' => 400 ) );

	if ( ! is_email( $email ) || 6 !== strlen( $code ) ) {
		return $fail;
	}

	$key    = 'bzr_otp_' . md5( $email );
	$stored = get_transient( $key );
	if ( ! is_array( $stored ) ) {
		return $fail;
	}

	if ( $stored['attempts'] >= BZR_OTP_MAX_ATTEMPTS ) {
		delete_transient( $key );
		return $fail;
	}

	if ( ! hash_equals( $stored['hash'], bzr_otp_hash( $email, $code ) ) ) {
		++$stored['attempts'];
		set_transient( $key, $stored, BZR_OTP_TTL );
		return $fail;
	}

	delete_transient( $key ); // Single use.

	$user = get_user_by( 'email', $email );
	if ( ! $user ) {
		if ( ! bzr_otp_signup_allowed() ) {
			return $fail;
		}
		$user_id = wc_create_new_customer( $email, '', wp_generate_password( 24 ) );
		if ( is_wp_error( $user_id ) ) {
			return $fail;
		}
		$user = get_user_by( 'id', $user_id );
	}

	if ( ! $user || ! bzr_otp_user_allowed( $user ) ) {
		return $fail;
	}

	wp_set_current_user( $user->ID );
	wp_set_auth_cookie( $user->ID, true );
	do_action( 'wp_login', $user->user_login, $user );

	$redirect = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : home_url( '/' );

	return new WP_REST_Response( array( 'redirect' => esc_url_raw( $redirect ) ), 200 );
}

/**
 * Register REST routes. Public by design: the endpoints authenticate via the emailed code.
 */
function bzr_otp_register_routes() {
	if ( ! bzr_otp_enabled() ) {
		return;
	}

	register_rest_route(
		'bazaar/v1',
		'/otp/request',
		array(
			'methods'             => 'POST',
			'callback'            => 'bzr_otp_rest_request',
			'permission_callback' => '__return_true',
		)
	);
	register_rest_route(
		'bazaar/v1',
		'/otp/verify',
		array(
			'methods'             => 'POST',
			'callback'            => 'bzr_otp_rest_verify',
			'permission_callback' => '__return_true',
		)
	);
}
add_action( 'rest_api_init', 'bzr_otp_register_routes' );

/**
 * [bzr_otp_login] shortcode.
 *
 * @return string
 */
function bzr_otp_shortcode() {
	if ( ! bzr_otp_enabled() ) {
		return '';
	}

	if ( is_user_logged_in() ) {
		$url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : home_url( '/' );
		return '<p>' . sprintf(
			/* translators: %s: account link */
			esc_html__( 'You are signed in. %s', 'bazaar-core' ),
			'<a href="' . esc_url( $url ) . '">' . esc_html__( 'Go to your account', 'bazaar-core' ) . '</a>'
		) . '</p>';
	}

	wp_enqueue_script( 'bzr-otp', BAZAAR_CORE_URI . '/assets/js/otp.js', array(), bzr_asset_version( 'assets/js/otp.js' ), array( 'in_footer' => true, 'strategy' => 'defer' ) );
	wp_add_inline_script(
		'bzr-otp',
		'window.bzrOtp=' . wp_json_encode(
			array(
				'request' => esc_url_raw( rest_url( 'bazaar/v1/otp/request' ) ),
				'verify'  => esc_url_raw( rest_url( 'bazaar/v1/otp/verify' ) ),
				'i18n'    => array(
					'sending'  => __( 'Sending...', 'bazaar-core' ),
					'verifying' => __( 'Verifying...', 'bazaar-core' ),
					'error'    => __( 'Something went wrong. Please try again.', 'bazaar-core' ),
					'sent'     => __( 'We emailed you a 6-digit code.', 'bazaar-core' ),
				),
			)
		),
		'before'
	);

	ob_start();
	?>
	<div class="bzr-otp" data-bzr-otp>
		<form class="bzr-otp__step" data-step="email" novalidate>
			<?php if ( bzr_otp_signup_allowed() ) : ?>
				<h2 class="bzr-otp__title"><?php esc_html_e( 'Sign in or create an account', 'bazaar-core' ); ?></h2>
				<p><?php esc_html_e( 'Enter your email and we will send you a one-time code. No password needed.', 'bazaar-core' ); ?></p>
			<?php else : ?>
				<h2 class="bzr-otp__title"><?php esc_html_e( 'Sign in with a one-time code', 'bazaar-core' ); ?></h2>
				<p><?php esc_html_e( 'Enter the email of your account and we will send you a one-time code. No password needed.', 'bazaar-core' ); ?></p>
			<?php endif; ?>
			<label for="bzr-otp-email"><?php esc_html_e( 'Email address', 'bazaar-core' ); ?></label>
			<input type="email" id="bzr-otp-email" name="email" autocomplete="email" inputmode="email" required />
			<div class="bzr-otp__hp" aria-hidden="true"><label>Website<input type="text" name="website" tabindex="-1" autocomplete="off" /></label></div>
			<button type="submit" class="wp-element-button"><?php esc_html_e( 'Send code', 'bazaar-core' ); ?></button>
		</form>
		<form class="bzr-otp__step" data-step="code" hidden novalidate>
			<h2 class="bzr-otp__title"><?php esc_html_e( 'Enter your code', 'bazaar-core' ); ?></h2>
			<p data-bzr-otp-sent></p>
			<label for="bzr-otp-code"><?php esc_html_e( '6-digit code', 'bazaar-core' ); ?></label>
			<input type="text" id="bzr-otp-code" name="code" inputmode="numeric" autocomplete="one-time-code" pattern="[0-9]{6}" maxlength="6" required />
			<button type="submit" class="wp-element-button"><?php esc_html_e( 'Verify and sign in', 'bazaar-core' ); ?></button>
			<button type="button" class="bzr-otp__back" data-bzr-otp-back><?php esc_html_e( 'Use a different email', 'bazaar-core' ); ?></button>
		</form>
		<p class="bzr-otp__status" role="status" aria-live="polite" data-bzr-otp-status></p>
	</div>
	<?php
	return (string) ob_get_clean();
}
add_shortcode( 'bzr_otp_login', 'bzr_otp_shortcode' );
