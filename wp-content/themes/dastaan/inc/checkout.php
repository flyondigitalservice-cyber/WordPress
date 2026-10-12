<?php
/**
 * Checkout: India-first fields, trust elements, coupon placement, thank-you extras.
 *
 * @package Dastaan
 */

defined( 'ABSPATH' ) || exit;

/**
 * Field tuning: phone before email, no company, friendly placeholders, India defaults.
 *
 * @param array $fields Fields.
 * @return array
 */
function dastaan_checkout_fields( $fields ) {
	$b = &$fields['billing'];

	unset( $b['billing_company'] );

	$order = array(
		'billing_first_name' => 10,
		'billing_last_name'  => 20,
		'billing_phone'      => 30,
		'billing_email'      => 40,
		'billing_country'    => 50,
		'billing_address_1'  => 60,
		'billing_address_2'  => 70,
		'billing_city'       => 80,
		'billing_state'      => 90,
		'billing_postcode'   => 100,
	);
	foreach ( $order as $key => $priority ) {
		if ( isset( $b[ $key ] ) ) {
			$b[ $key ]['priority'] = $priority;
		}
	}

	$placeholders = array(
		'billing_first_name' => __( 'First name', 'dastaan' ),
		'billing_last_name'  => __( 'Last name', 'dastaan' ),
		'billing_phone'      => __( '10-digit mobile (for delivery updates on WhatsApp)', 'dastaan' ),
		'billing_email'      => __( 'Email for order confirmation', 'dastaan' ),
		'billing_address_1'  => __( 'Flat / house no., building, street', 'dastaan' ),
		'billing_address_2'  => __( 'Landmark, area (optional)', 'dastaan' ),
		'billing_city'       => __( 'City', 'dastaan' ),
		'billing_postcode'   => __( 'PIN code', 'dastaan' ),
	);
	foreach ( $placeholders as $key => $ph ) {
		if ( isset( $b[ $key ] ) ) {
			$b[ $key ]['placeholder'] = $ph;
		}
	}
	if ( isset( $b['billing_phone'] ) ) {
		$b['billing_phone']['type']  = 'tel';
		$b['billing_phone']['class'] = array( 'form-row-first' );
		$b['billing_phone']['custom_attributes'] = array( 'inputmode' => 'tel', 'autocomplete' => 'tel' );
	}
	if ( isset( $b['billing_email'] ) ) {
		$b['billing_email']['class'] = array( 'form-row-last' );
	}
	if ( isset( $b['billing_city'] ) ) {
		$b['billing_city']['class'] = array( 'form-row-first' );
	}
	if ( isset( $b['billing_state'] ) ) {
		$b['billing_state']['class'] = array( 'form-row-last' );
	}
	if ( isset( $b['billing_postcode'] ) ) {
		$b['billing_postcode']['class'] = array( 'form-row-wide' );
	}

	if ( isset( $fields['shipping']['shipping_company'] ) ) {
		unset( $fields['shipping']['shipping_company'] );
	}
	if ( isset( $fields['order']['order_comments'] ) ) {
		$fields['order']['order_comments']['label']       = __( 'Delivery instructions or gift note', 'dastaan' );
		$fields['order']['order_comments']['placeholder'] = __( 'e.g. Call before delivery, gift wrap with a note for Maa…', 'dastaan' );
	}

	return $fields;
}
add_filter( 'woocommerce_checkout_fields', 'dastaan_checkout_fields', 20 );

/**
 * WooCommerce's address-i18n script re-applies locale priorities on the client, which pushes the
 * phone field to the bottom again. Set phone priority (and required) at the locale level so the
 * PHP order and the JS order agree. Phone is required: it powers WhatsApp delivery updates.
 *
 * @param array $fields Default address fields / locale defaults.
 * @return array
 */
function dastaan_phone_locale( $fields ) {
	if ( isset( $fields['phone'] ) ) {
		$fields['phone']['priority'] = 30;
		$fields['phone']['class']    = array( 'form-row-first' );
		if ( 'hidden' !== get_option( 'woocommerce_checkout_phone_field', 'required' ) ) {
			$fields['phone']['required'] = (bool) apply_filters( 'dastaan_phone_required', true );
		}
	}
	return $fields;
}
add_filter( 'woocommerce_default_address_fields', 'dastaan_phone_locale', 20 );
add_filter( 'woocommerce_get_country_locale_default', 'dastaan_phone_locale', 20 );
add_filter( 'woocommerce_get_country_locale_base', 'dastaan_phone_locale', 20 );

/**
 * Country-specific locales: keep phone near the top for every country.
 *
 * @param array $locale Locale array.
 * @return array
 */
function dastaan_phone_locale_countries( $locale ) {
	foreach ( $locale as $cc => $fields ) {
		if ( isset( $fields['phone'] ) && is_array( $fields['phone'] ) ) {
			$locale[ $cc ]['phone']['priority'] = 30;
		}
	}
	return $locale;
}
add_filter( 'woocommerce_get_country_locale', 'dastaan_phone_locale_countries', 20 );

/**
 * Validate Indian mobile numbers gently (10 digits, optional +91).
 *
 * @param array    $data   Data.
 * @param WP_Error $errors Errors.
 */
function dastaan_validate_phone( $data, $errors ) {
	if ( empty( $data['billing_phone'] ) ) {
		return;
	}
	if ( ! apply_filters( 'dastaan_validate_indian_phone', true ) ) {
		return;
	}
	$digits = preg_replace( '/\D+/', '', $data['billing_phone'] );
	if ( 'IN' === ( $data['billing_country'] ?? '' ) && ! preg_match( '/^(91)?[6-9]\d{9}$/', $digits ) ) {
		$errors->add( 'billing_phone', __( 'Please enter a valid 10-digit Indian mobile number.', 'dastaan' ) );
	}
}
add_action( 'woocommerce_after_checkout_validation', 'dastaan_validate_phone', 10, 2 );

/**
 * Move the coupon form into the order summary (sidebar) as a toggle.
 */
remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10 );

/**
 * Trust strip under the Place Order button.
 */
function dastaan_checkout_trust() {
	?>
	<ul class="checkout-trust">
		<li><?php dastaan_the_icon( 'lock', array( 'size' => 16 ) ); ?><?php esc_html_e( '256-bit SSL encrypted checkout', 'dastaan' ); ?></li>
		<li><?php dastaan_the_icon( 'upi', array( 'size' => 16 ) ); ?><?php esc_html_e( 'UPI, cards, net banking & EMI', 'dastaan' ); ?></li>
		<li><?php dastaan_the_icon( 'exchange', array( 'size' => 16 ) ); ?><?php esc_html_e( '48-hour exchange after delivery', 'dastaan' ); ?></li>
	</ul>
	<?php
	$wa = dastaan_whatsapp_url( __( 'Hi Dastaan, I need help with my checkout.', 'dastaan' ) );
	if ( $wa ) {
		echo '<p class="checkout-help">' . esc_html__( 'Stuck? ', 'dastaan' ) . '<a href="' . esc_url( $wa ) . '" target="_blank" rel="noopener">' . esc_html__( 'Chat with us on WhatsApp', 'dastaan' ) . '</a></p>';
	}
}
add_action( 'woocommerce_review_order_after_submit', 'dastaan_checkout_trust' );

/**
 * Privacy text: shorter, friendlier.
 */
add_filter(
	'woocommerce_get_privacy_policy_text',
	function ( $text, $type ) {
		if ( 'checkout' === $type ) {
			return sprintf(
				/* translators: %s: privacy policy link */
				__( 'Your details are used only to process this order and send delivery updates. See our %s.', 'dastaan' ),
				'[privacy_policy]'
			);
		}
		return $text;
	},
	10,
	2
);

/**
 * Login prompt copy.
 */
add_filter( 'woocommerce_checkout_login_message', fn() => __( 'Returning customer?', 'dastaan' ) );

/**
 * Thank-you page: WhatsApp support + "what happens next".
 *
 * @param int $order_id Order ID.
 */
function dastaan_thankyou_extras( $order_id ) {
	$order = wc_get_order( $order_id );
	if ( ! $order ) {
		return;
	}
	$wa = dastaan_whatsapp_url( sprintf( /* translators: %s: order number */ __( 'Hi Dastaan, this is regarding my order #%s', 'dastaan' ), $order->get_order_number() ) );
	?>
	<section class="thankyou-next">
		<h2><?php esc_html_e( 'What happens next', 'dastaan' ); ?></h2>
		<ol class="thankyou-steps">
			<li><strong><?php esc_html_e( 'Quality check', 'dastaan' ); ?></strong><span><?php esc_html_e( 'Your piece is inspected under daylight, measured and folded in muslin.', 'dastaan' ); ?></span></li>
			<li><strong><?php esc_html_e( 'Dispatch in 2–4 days', 'dastaan' ); ?></strong><span><?php esc_html_e( 'Insured, tracked shipping. Tracking details arrive by email and WhatsApp.', 'dastaan' ); ?></span></li>
			<li><strong><?php esc_html_e( 'Unbox & stitch', 'dastaan' ); ?></strong><span><?php esc_html_e( 'Need a stitching guide or tailor recommendation? Just message us.', 'dastaan' ); ?></span></li>
		</ol>
		<?php if ( $wa ) : ?>
			<a class="btn btn--whatsapp" href="<?php echo esc_url( $wa ); ?>" target="_blank" rel="noopener"><?php dastaan_the_icon( 'whatsapp', array( 'size' => 18 ) ); ?><span><?php esc_html_e( 'Message us about this order', 'dastaan' ); ?></span></a>
		<?php endif; ?>
	</section>
	<?php
}
add_action( 'woocommerce_thankyou', 'dastaan_thankyou_extras', 5 );

/**
 * Cart page: show "secure checkout" assurance below proceed button.
 */
add_action(
	'woocommerce_proceed_to_checkout',
	function () {
		echo '<p class="cart-secure">' . dastaan_icon( 'lock', array( 'size' => 14 ) ) . esc_html__( 'Secure checkout · UPI · Cards · EMI', 'dastaan' ) . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	},
	30
);

/**
 * Checkout: remove the "ship to a different address" open-by-default behaviour (cleaner on mobile).
 */
add_filter( 'woocommerce_ship_to_different_address_checked', '__return_false' );
