<?php
/**
 * Thin HTTP client for the YouPI PG merchant REST API.
 *
 * Endpoints used:
 *   POST /api/v1/payments      — create a payment session, returns checkoutUrl.
 *   GET  /api/v1/payments/{id} — read payment status (merchant view with X-Merchant-Key).
 *
 * @package YouPI_Luxury_Gateway
 */

defined( 'ABSPATH' ) || exit;

/**
 * YouPI PG API client.
 */
class Youpi_Luxury_API_Client {

	/**
	 * Base URL of the YouPI payment API, without trailing slash.
	 *
	 * @var string
	 */
	private $api_url;

	/**
	 * Merchant id (X-Merchant-Id).
	 *
	 * @var string
	 */
	private $merchant_id;

	/**
	 * Merchant secret API key (X-Merchant-Key).
	 *
	 * @var string
	 */
	private $api_key;

	/**
	 * Constructor.
	 *
	 * @param string $api_url     Payment API base URL, e.g. https://pay.example.com.
	 * @param string $merchant_id Merchant id.
	 * @param string $api_key     Merchant secret API key.
	 */
	public function __construct( $api_url, $merchant_id, $api_key ) {
		$this->api_url     = untrailingslashit( trim( (string) $api_url ) );
		$this->merchant_id = trim( (string) $merchant_id );
		$this->api_key     = trim( (string) $api_key );
	}

	/**
	 * Whether the client has the minimum configuration to talk to the gateway.
	 *
	 * @return bool
	 */
	public function is_configured() {
		return '' !== $this->api_url && '' !== $this->api_key;
	}

	/**
	 * Create a payment session.
	 *
	 * @param array  $payload         Request body (orderId, amount, currency, returnUrl, customer, metadata).
	 * @param string $idempotency_key Idempotency key.
	 * @return array|WP_Error Decoded response with an extra `_http_status` key, or WP_Error.
	 */
	public function create_payment( array $payload, $idempotency_key = '' ) {
		$headers = array();
		if ( '' !== $idempotency_key ) {
			$headers['Idempotency-Key'] = $idempotency_key;
		}
		return $this->request( 'POST', '/api/v1/payments', $payload, $headers );
	}

	/**
	 * Read a payment session.
	 *
	 * @param string $payment_id Gateway payment id.
	 * @return array|WP_Error
	 */
	public function get_payment( $payment_id ) {
		return $this->request( 'GET', '/api/v1/payments/' . rawurlencode( $payment_id ) );
	}

	/**
	 * Perform an HTTP request against the gateway.
	 *
	 * @param string     $method  HTTP method.
	 * @param string     $path    Path beginning with a slash.
	 * @param array|null $body    JSON body.
	 * @param array      $headers Extra headers.
	 * @return array|WP_Error
	 */
	private function request( $method, $path, $body = null, array $headers = array() ) {
		if ( ! $this->is_configured() ) {
			return new WP_Error( 'youpi_not_configured', __( 'The YouPI gateway is not configured.', 'youpi-luxury-gateway' ) );
		}

		$headers = array_merge(
			array(
				'Accept'         => 'application/json',
				'X-Merchant-Key' => $this->api_key,
			),
			$headers
		);
		if ( '' !== $this->merchant_id ) {
			$headers['X-Merchant-Id'] = $this->merchant_id;
		}

		$args = array(
			'method'      => $method,
			'timeout'     => 20,
			'redirection' => 0,
			'headers'     => $headers,
		);
		if ( null !== $body ) {
			$args['headers']['Content-Type'] = 'application/json';
			$args['body']                    = wp_json_encode( $body );
		}

		$response = wp_remote_request( $this->api_url . $path, $args );
		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$status  = (int) wp_remote_retrieve_response_code( $response );
		$decoded = json_decode( wp_remote_retrieve_body( $response ), true );
		if ( ! is_array( $decoded ) ) {
			$decoded = array();
		}
		$decoded['_http_status'] = $status;

		if ( $status < 200 || $status >= 300 ) {
			$message = isset( $decoded['message'] ) ? $decoded['message'] : ( isset( $decoded['error'] ) ? $decoded['error'] : 'HTTP ' . $status );
			return new WP_Error(
				isset( $decoded['code'] ) ? (string) $decoded['code'] : 'youpi_http_' . $status,
				(string) $message,
				$decoded
			);
		}

		return $decoded;
	}
}
