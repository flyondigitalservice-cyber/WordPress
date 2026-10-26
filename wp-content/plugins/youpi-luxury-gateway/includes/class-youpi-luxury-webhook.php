<?php
/**
 * Receives and verifies YouPI PG webhooks.
 *
 * Contract (see YouPI PG README, "Authoritative Server-to-Server Webhook Verification"):
 *   X-Gateway-Signature:    sha256=HMAC_SHA256(secret, rawBody)
 *   X-Gateway-Signature-V2: sha256=HMAC_SHA256(secret, "<timestamp_ms>.<rawBody>")
 *   X-Gateway-Timestamp:    ms epoch of the first signing (reused on retries)
 *   X-Gateway-Delivery-Id:  stable across retries; used to deduplicate
 *
 * @package YouPI_Luxury_Gateway
 */

defined( 'ABSPATH' ) || exit;

/**
 * Webhook endpoint.
 */
class Youpi_Luxury_Webhook {

	const ROUTE_NAMESPACE = 'youpi-luxury/v1';
	const ROUTE           = '/webhook';

	/** Retries reuse the original timestamp for up to an hour, so allow two. */
	const TOLERANCE_MS = 7200000;

	/** How many processed delivery ids to remember per order. */
	const MAX_DELIVERY_IDS = 20;

	/**
	 * Hook the REST route.
	 */
	public static function init() {
		add_action( 'rest_api_init', array( __CLASS__, 'register_route' ) );
	}

	/**
	 * Register POST /wp-json/youpi-luxury/v1/webhook.
	 */
	public static function register_route() {
		register_rest_route(
			self::ROUTE_NAMESPACE,
			self::ROUTE,
			array(
				'methods'             => 'POST',
				'callback'            => array( __CLASS__, 'handle' ),
				// Authenticated by the HMAC signature inside handle().
				'permission_callback' => '__return_true',
			)
		);
	}

	/**
	 * Verify the signature over the raw body.
	 *
	 * @param string $raw_body  Raw request body.
	 * @param string $secret    Webhook secret.
	 * @param string $sig_v1    X-Gateway-Signature.
	 * @param string $sig_v2    X-Gateway-Signature-V2.
	 * @param string $timestamp X-Gateway-Timestamp.
	 * @return bool
	 */
	public static function verify_signature( $raw_body, $secret, $sig_v1, $sig_v2, $timestamp ) {
		if ( '' === $secret ) {
			return false;
		}
		if ( '' !== $sig_v2 && '' !== $timestamp ) {
			$expected = 'sha256=' . hash_hmac( 'sha256', $timestamp . '.' . $raw_body, $secret );
			if ( hash_equals( $expected, $sig_v2 ) ) {
				return true;
			}
		}
		if ( '' !== $sig_v1 ) {
			$expected = 'sha256=' . hash_hmac( 'sha256', $raw_body, $secret );
			if ( hash_equals( $expected, $sig_v1 ) ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Handle a webhook delivery.
	 *
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response
	 */
	public static function handle( WP_REST_Request $request ) {
		$gateway = WC_Gateway_YouPI_Luxury::instance();
		if ( ! $gateway ) {
			return new WP_REST_Response( array( 'error' => 'gateway_unavailable' ), 503 );
		}

		$raw_body  = (string) $request->get_body();
		$sig_v1    = trim( (string) $request->get_header( 'x_gateway_signature' ) );
		$sig_v2    = trim( (string) $request->get_header( 'x_gateway_signature_v2' ) );
		$timestamp = trim( (string) $request->get_header( 'x_gateway_timestamp' ) );
		$secret    = (string) $gateway->get_option( 'webhook_secret' );

		if ( ! self::verify_signature( $raw_body, $secret, $sig_v1, $sig_v2, $timestamp ) ) {
			WC_Gateway_YouPI_Luxury::log( 'Rejected webhook with invalid signature.', 'error' );
			return new WP_REST_Response( array( 'error' => 'invalid_signature' ), 401 );
		}

		$payload = json_decode( $raw_body, true );
		if ( ! is_array( $payload ) || empty( $payload['orderId'] ) || empty( $payload['event'] ) ) {
			return new WP_REST_Response( array( 'error' => 'invalid_payload' ), 400 );
		}

		// Freshness: the signed body timestamp must be recent and match the header.
		$now_ms  = (int) round( microtime( true ) * 1000 );
		$body_ts = isset( $payload['timestamp'] ) ? (int) $payload['timestamp'] : 0;
		if ( $body_ts <= 0 || abs( $now_ms - $body_ts ) > self::TOLERANCE_MS ) {
			WC_Gateway_YouPI_Luxury::log( 'Rejected stale webhook for ' . $payload['orderId'], 'error' );
			return new WP_REST_Response( array( 'error' => 'stale_timestamp' ), 401 );
		}
		if ( '' !== $timestamp && (string) $body_ts !== $timestamp ) {
			return new WP_REST_Response( array( 'error' => 'timestamp_mismatch' ), 401 );
		}

		$header_delivery = trim( (string) $request->get_header( 'x_gateway_delivery_id' ) );
		$delivery_id     = isset( $payload['deliveryId'] ) ? (string) $payload['deliveryId'] : $header_delivery;
		if ( '' !== $header_delivery && $header_delivery !== $delivery_id ) {
			return new WP_REST_Response( array( 'error' => 'delivery_id_mismatch' ), 401 );
		}

		$order = self::find_order( $payload );
		if ( ! $order ) {
			WC_Gateway_YouPI_Luxury::log( 'Webhook for unknown order reference ' . $payload['orderId'], 'warning' );
			// Acknowledge so the gateway stops retrying a delivery we will never match.
			return new WP_REST_Response( array( 'received' => true, 'matched' => false ), 202 );
		}

		$seen = $order->get_meta( WC_Gateway_YouPI_Luxury::META_DELIVERY_IDS );
		$seen = is_array( $seen ) ? $seen : array();
		if ( '' !== $delivery_id && in_array( $delivery_id, $seen, true ) ) {
			return new WP_REST_Response( array( 'received' => true, 'duplicate' => true ), 200 );
		}

		$outcome = WC_Gateway_YouPI_Luxury::apply_gateway_status( $order, $payload, 'webhook' );

		if ( '' !== $delivery_id ) {
			$order = wc_get_order( $order->get_id() );
			$seen[] = $delivery_id;
			$order->update_meta_data( WC_Gateway_YouPI_Luxury::META_DELIVERY_IDS, array_slice( $seen, -self::MAX_DELIVERY_IDS ) );
			$order->save();
		}

		return new WP_REST_Response( array( 'received' => true, 'outcome' => $outcome ), 200 );
	}

	/**
	 * Resolve the WooCommerce order a webhook refers to, requiring the gateway
	 * reference stored on the order to match the signed orderId.
	 *
	 * @param array $payload Webhook payload.
	 * @return WC_Order|null
	 */
	private static function find_order( array $payload ) {
		$ref = (string) $payload['orderId'];

		if ( ! empty( $payload['metadata']['wc_order_id'] ) ) {
			$order = wc_get_order( absint( $payload['metadata']['wc_order_id'] ) );
			if ( $order && $ref === $order->get_meta( WC_Gateway_YouPI_Luxury::META_ORDER_REF ) ) {
				return $order;
			}
		}

		$orders = wc_get_orders(
			array(
				'limit'      => 1,
				'meta_key'   => WC_Gateway_YouPI_Luxury::META_ORDER_REF, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				'meta_value' => $ref, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
			)
		);
		return $orders ? $orders[0] : null;
	}
}
