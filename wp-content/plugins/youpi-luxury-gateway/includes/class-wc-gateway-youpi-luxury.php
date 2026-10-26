<?php
/**
 * WooCommerce payment gateway backed by a self-hosted YouPI PG instance,
 * tuned for high-value luxury watch orders.
 *
 * @package YouPI_Luxury_Gateway
 */

defined( 'ABSPATH' ) || exit;

/**
 * YouPI Luxury gateway.
 */
class WC_Gateway_YouPI_Luxury extends WC_Payment_Gateway {

	const META_PAYMENT_ID     = '_youpi_payment_id';
	const META_ORDER_REF      = '_youpi_order_ref';
	const META_REF_VERSION    = '_youpi_ref_version';
	const META_STATUS         = '_youpi_status';
	const META_UTR            = '_youpi_utr';
	const META_AWAITING_RECON = '_youpi_awaiting_reconciliation';
	const META_DELIVERY_IDS   = '_youpi_delivery_ids';

	/** Statuses that mean money was observed arriving. */
	const PAID_STATUSES = array( 'SUCCESS', 'OBSERVED_SUCCESS', 'RECONCILED' );

	/** Terminal statuses that mean no money arrived for that attempt. */
	const UNPAID_STATUSES = array( 'FAILED', 'EXPIRED', 'CANCELLED' );

	/**
	 * Logger context.
	 *
	 * @var array
	 */
	private static $log_context = array( 'source' => 'youpi-luxury-gateway' );

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id                 = YOUPI_LUXURY_GATEWAY_ID;
		$this->icon               = '';
		$this->has_fields         = false;
		$this->method_title       = __( 'YouPI UPI — Luxury', 'youpi-luxury-gateway' );
		$this->method_description = __( 'Accept direct-to-bank UPI payments through your self-hosted YouPI PG gateway, with reconciliation holds for high-value timepieces and a concierge-style checkout.', 'youpi-luxury-gateway' );
		$this->supports           = array( 'products', 'refunds' );

		$this->init_form_fields();
		$this->init_settings();

		$this->title       = $this->get_option( 'title' );
		$this->description = $this->get_option( 'description' );
		$this->enabled     = $this->get_option( 'enabled' );

		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
		add_action( 'woocommerce_thankyou_' . $this->id, array( $this, 'thankyou_page' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'woocommerce_email_before_order_table', array( $this, 'email_instructions' ), 10, 3 );
	}

	/**
	 * Settings fields.
	 */
	public function init_form_fields() {
		$webhook_url = rest_url( Youpi_Luxury_Webhook::ROUTE_NAMESPACE . Youpi_Luxury_Webhook::ROUTE );

		$this->form_fields = array(
			'enabled'               => array(
				'title'   => __( 'Enable/Disable', 'youpi-luxury-gateway' ),
				'type'    => 'checkbox',
				'label'   => __( 'Enable YouPI UPI payments', 'youpi-luxury-gateway' ),
				'default' => 'no',
			),
			'title'                 => array(
				'title'       => __( 'Title', 'youpi-luxury-gateway' ),
				'type'        => 'text',
				'description' => __( 'Payment method name shown at checkout.', 'youpi-luxury-gateway' ),
				'default'     => __( 'Private UPI Transfer', 'youpi-luxury-gateway' ),
				'desc_tip'    => true,
			),
			'description'           => array(
				'title'       => __( 'Description', 'youpi-luxury-gateway' ),
				'type'        => 'textarea',
				'description' => __( 'Shown beneath the payment method at checkout.', 'youpi-luxury-gateway' ),
				'default'     => __( 'Settle your timepiece directly from your bank with any UPI app — Google Pay, PhonePe, Paytm or CRED. Funds go straight to our boutique account; no card surcharges.', 'youpi-luxury-gateway' ),
			),
			'connection_title'      => array(
				'title'       => __( 'Gateway connection', 'youpi-luxury-gateway' ),
				'type'        => 'title',
				'description' => sprintf(
					/* translators: %s: webhook URL */
					__( 'In the YouPI Merchant Admin panel, set the merchant <strong>Webhook URL</strong> to: %s', 'youpi-luxury-gateway' ),
					'<code>' . esc_html( $webhook_url ) . '</code>'
				),
			),
			'api_url'               => array(
				'title'       => __( 'Payment API URL', 'youpi-luxury-gateway' ),
				'type'        => 'url',
				'description' => __( 'Base URL of your YouPI payment API, e.g. https://pay.yourboutique.com. Must use HTTPS in production.', 'youpi-luxury-gateway' ),
				'default'     => '',
				'placeholder' => 'https://pay.yourboutique.com',
			),
			'merchant_id'           => array(
				'title'       => __( 'Merchant ID', 'youpi-luxury-gateway' ),
				'type'        => 'text',
				'description' => __( 'Merchant UUID or slug from the YouPI Merchant Admin panel.', 'youpi-luxury-gateway' ),
				'default'     => '',
			),
			'api_key'               => array(
				'title'       => __( 'Merchant API key', 'youpi-luxury-gateway' ),
				'type'        => 'password',
				'description' => __( 'Secret merchant key (mk_live_…). Sent server-to-server only, never to the browser.', 'youpi-luxury-gateway' ),
				'default'     => '',
			),
			'webhook_secret'        => array(
				'title'       => __( 'Webhook secret', 'youpi-luxury-gateway' ),
				'type'        => 'password',
				'description' => __( 'Used to verify the HMAC-SHA256 signature on gateway webhooks.', 'youpi-luxury-gateway' ),
				'default'     => '',
			),
			'order_prefix'          => array(
				'title'       => __( 'Order reference prefix', 'youpi-luxury-gateway' ),
				'type'        => 'text',
				'description' => __( 'Prepended to the WooCommerce order number when creating gateway sessions. Use a unique prefix per store if several stores share one gateway.', 'youpi-luxury-gateway' ),
				'default'     => 'LUX-',
			),
			'luxury_title'          => array(
				'title'       => __( 'High-value order controls', 'youpi-luxury-gateway' ),
				'type'        => 'title',
				'description' => __( 'Bank alerts can be delayed, duplicated or spoofed. For high-value timepieces, hold orders until the credit is confirmed against your bank statement.', 'youpi-luxury-gateway' ),
			),
			'max_amount'            => array(
				'title'             => __( 'Maximum UPI order value (₹)', 'youpi-luxury-gateway' ),
				'type'              => 'number',
				'description'       => __( 'UPI is hidden at checkout above this value (most banks cap UPI at ₹1,00,000 per transaction). Leave 0 for no limit.', 'youpi-luxury-gateway' ),
				'default'           => '100000',
				'custom_attributes' => array(
					'min'  => '0',
					'step' => '1',
				),
			),
			'review_threshold'      => array(
				'title'             => __( 'Reconciliation hold threshold (₹)', 'youpi-luxury-gateway' ),
				'type'              => 'number',
				'description'       => __( 'Orders at or above this value are placed On hold after payment is observed, until the gateway reports it RECONCILED or you confirm the credit manually. Set 0 to hold every order.', 'youpi-luxury-gateway' ),
				'default'           => '25000',
				'custom_attributes' => array(
					'min'  => '0',
					'step' => '1',
				),
			),
			'require_phone'         => array(
				'title'   => __( 'Require mobile number', 'youpi-luxury-gateway' ),
				'type'    => 'checkbox',
				'label'   => __( 'Require a valid Indian mobile number (used for the gateway\'s WhatsApp verification)', 'youpi-luxury-gateway' ),
				'default' => 'yes',
			),
			'discreet_billing'      => array(
				'title'   => __( 'Discreet billing', 'youpi-luxury-gateway' ),
				'type'    => 'checkbox',
				'label'   => __( 'Do not send product names or brands to the payment gateway (only order number and piece count)', 'youpi-luxury-gateway' ),
				'default' => 'yes',
			),
			'concierge_title'       => array(
				'title' => __( 'Client concierge', 'youpi-luxury-gateway' ),
				'type'  => 'title',
			),
			'concierge_phone'       => array(
				'title'       => __( 'Concierge phone / WhatsApp', 'youpi-luxury-gateway' ),
				'type'        => 'text',
				'description' => __( 'Shown to clients at checkout and on the order confirmation page.', 'youpi-luxury-gateway' ),
				'default'     => '',
			),
			'concierge_email'       => array(
				'title'   => __( 'Concierge email', 'youpi-luxury-gateway' ),
				'type'    => 'email',
				'default' => '',
			),
			'debug'                 => array(
				'title'       => __( 'Debug log', 'youpi-luxury-gateway' ),
				'type'        => 'checkbox',
				'label'       => __( 'Log gateway requests and webhooks (WooCommerce → Status → Logs)', 'youpi-luxury-gateway' ),
				'default'     => 'no',
			),
		);
	}

	/**
	 * Warn about insecure or incomplete configuration on the settings screen.
	 */
	public function admin_options() {
		$api_url = (string) $this->get_option( 'api_url' );
		if ( '' !== $api_url && 0 !== strpos( $api_url, 'https://' ) ) {
			echo '<div class="notice notice-warning inline"><p>' . esc_html__( 'The Payment API URL is not HTTPS. Use HTTPS for any real-money transactions.', 'youpi-luxury-gateway' ) . '</p></div>';
		}
		if ( 'yes' === $this->enabled && '' === (string) $this->get_option( 'webhook_secret' ) ) {
			echo '<div class="notice notice-warning inline"><p>' . esc_html__( 'No webhook secret is set — orders will only update when the client returns to your site or during the 10-minute reconciliation sweep.', 'youpi-luxury-gateway' ) . '</p></div>';
		}
		parent::admin_options();
	}

	/**
	 * Build an API client from the current settings.
	 *
	 * @return Youpi_Luxury_API_Client
	 */
	public function get_client() {
		return new Youpi_Luxury_API_Client(
			$this->get_option( 'api_url' ),
			$this->get_option( 'merchant_id' ),
			$this->get_option( 'api_key' )
		);
	}

	/**
	 * Only offer UPI for INR orders inside the configured value ceiling.
	 *
	 * @return bool
	 */
	public function is_available() {
		if ( ! parent::is_available() ) {
			return false;
		}
		if ( 'INR' !== get_woocommerce_currency() ) {
			return false;
		}
		if ( ! $this->get_client()->is_configured() ) {
			return false;
		}

		$max = (float) $this->get_option( 'max_amount', 0 );
		if ( $max > 0 ) {
			$total = $this->get_order_total();
			if ( $total > $max ) {
				return false;
			}
		}
		return true;
	}

	/**
	 * Checkout description with concierge contact appended.
	 *
	 * @return string
	 */
	public function get_description() {
		$description = parent::get_description();
		$concierge   = $this->get_concierge_line();
		if ( $concierge ) {
			$description .= "\n\n" . $concierge;
		}
		return apply_filters( 'woocommerce_gateway_description', $description, $this->id );
	}

	/**
	 * Concierge contact sentence, or empty string.
	 *
	 * @return string
	 */
	public function get_concierge_line() {
		$parts = array_filter(
			array(
				trim( (string) $this->get_option( 'concierge_phone' ) ),
				trim( (string) $this->get_option( 'concierge_email' ) ),
			)
		);
		if ( ! $parts ) {
			return '';
		}
		/* translators: %s: concierge phone and/or email */
		return sprintf( __( 'Questions about your timepiece or payment? Our concierge is at your service: %s', 'youpi-luxury-gateway' ), implode( ' · ', $parts ) );
	}

	/**
	 * Normalise an Indian mobile number to 10 digits, or return '' if invalid.
	 *
	 * @param string $phone Raw phone.
	 * @return string
	 */
	public static function normalise_indian_mobile( $phone ) {
		$digits = preg_replace( '/\D+/', '', (string) $phone );
		if ( 12 === strlen( $digits ) && 0 === strpos( $digits, '91' ) ) {
			$digits = substr( $digits, 2 );
		} elseif ( 11 === strlen( $digits ) && 0 === strpos( $digits, '0' ) ) {
			$digits = substr( $digits, 1 );
		}
		return preg_match( '/^[6-9]\d{9}$/', $digits ) ? $digits : '';
	}

	/**
	 * Gateway reference for an order. A new version is minted only when the order
	 * total changed after an earlier session was opened (gateway returns 409).
	 *
	 * @param WC_Order $order Order.
	 * @return string
	 */
	private function get_order_ref( WC_Order $order ) {
		$version = (int) $order->get_meta( self::META_REF_VERSION );
		$ref     = $this->get_option( 'order_prefix', 'LUX-' ) . $order->get_order_number();
		return $version > 1 ? $ref . '-V' . $version : $ref;
	}

	/**
	 * Create a gateway session and redirect the client to the hosted UPI checkout.
	 *
	 * @param int $order_id Order id.
	 * @return array
	 */
	public function process_payment( $order_id ) {
		$order = wc_get_order( $order_id );
		if ( ! $order ) {
			return $this->fail( __( 'Order not found.', 'youpi-luxury-gateway' ) );
		}

		if ( 'INR' !== $order->get_currency() ) {
			return $this->fail( __( 'UPI payments are available for orders in Indian Rupees only.', 'youpi-luxury-gateway' ) );
		}

		$max = (float) $this->get_option( 'max_amount', 0 );
		if ( $max > 0 && (float) $order->get_total() > $max ) {
			return $this->fail( __( 'This order exceeds the UPI transaction limit. Please contact our concierge to arrange a bank transfer.', 'youpi-luxury-gateway' ) );
		}

		$phone = self::normalise_indian_mobile( $order->get_billing_phone() );
		if ( 'yes' === $this->get_option( 'require_phone' ) && '' === $phone ) {
			return $this->fail( __( 'Please enter a valid 10-digit Indian mobile number in your billing details to pay by UPI.', 'youpi-luxury-gateway' ) );
		}

		$result = $this->create_session( $order, $phone );

		// The order total was edited after an earlier session: mint a fresh reference once.
		if ( is_wp_error( $result ) && 'ORDER_AMOUNT_MISMATCH' === $result->get_error_code() ) {
			$order->update_meta_data( self::META_REF_VERSION, max( 2, (int) $order->get_meta( self::META_REF_VERSION ) + 1 ) );
			$order->save();
			$result = $this->create_session( $order, $phone );
		}

		if ( is_wp_error( $result ) ) {
			self::log( 'Create payment failed for order ' . $order->get_id() . ': ' . $result->get_error_code() . ' ' . $result->get_error_message(), 'error' );
			$order->add_order_note(
				/* translators: %s: error message */
				sprintf( __( 'YouPI session could not be created: %s', 'youpi-luxury-gateway' ), $result->get_error_message() )
			);
			return $this->fail( $this->client_error_message( $result ) );
		}

		if ( empty( $result['checkoutUrl'] ) || empty( $result['paymentId'] ) ) {
			self::log( 'Create payment returned no checkoutUrl for order ' . $order->get_id(), 'error' );
			return $this->fail( __( 'The payment service returned an unexpected response. Please try again.', 'youpi-luxury-gateway' ) );
		}

		$order->update_meta_data( self::META_PAYMENT_ID, sanitize_text_field( $result['paymentId'] ) );
		$order->update_meta_data( self::META_ORDER_REF, sanitize_text_field( $result['orderId'] ) );
		$order->update_meta_data( self::META_STATUS, sanitize_text_field( isset( $result['status'] ) ? $result['status'] : 'INITIATED' ) );
		$order->save();

		// An earlier attempt for this order may already be paid.
		if ( isset( $result['status'] ) && in_array( $result['status'], array_merge( self::PAID_STATUSES, array( 'MANUAL_REVIEW' ) ), true ) ) {
			self::apply_gateway_status( $order, $result, 'create' );
			return array(
				'result'   => 'success',
				'redirect' => $this->get_return_url( $order ),
			);
		}

		$order->add_order_note(
			/* translators: 1: gateway payment id, 2: gateway order reference */
			sprintf( __( 'Client redirected to YouPI UPI checkout (payment %1$s, reference %2$s).', 'youpi-luxury-gateway' ), $result['paymentId'], $result['orderId'] )
		);

		if ( function_exists( 'WC' ) && WC()->cart ) {
			WC()->cart->empty_cart();
		}

		return array(
			'result'   => 'success',
			'redirect' => esc_url_raw( $result['checkoutUrl'] ),
		);
	}

	/**
	 * Call the gateway to create (or resume) a session for the order.
	 *
	 * @param WC_Order $order Order.
	 * @param string   $phone Normalised mobile.
	 * @return array|WP_Error
	 */
	private function create_session( WC_Order $order, $phone ) {
		$ref = $this->get_order_ref( $order );

		$customer = array(
			'fullName' => trim( $order->get_billing_first_name() . ' ' . $order->get_billing_last_name() ),
		);
		if ( '' !== $phone ) {
			$customer['phoneNumber'] = $phone;
		}
		if ( $order->get_billing_email() ) {
			$customer['email'] = $order->get_billing_email();
		}

		$metadata = array(
			'wc_order_id'     => (string) $order->get_id(),
			'wc_order_number' => (string) $order->get_order_number(),
			'store'           => wp_parse_url( home_url(), PHP_URL_HOST ),
			'piece_count'     => (int) $order->get_item_count(),
		);
		if ( 'yes' !== $this->get_option( 'discreet_billing' ) ) {
			$metadata['items'] = array_values(
				array_map(
					static function ( $item ) {
						return $item->get_name() . ' × ' . $item->get_quantity();
					},
					$order->get_items()
				)
			);
		}

		$payload = array(
			'orderId'   => $ref,
			'amount'    => wc_format_decimal( $order->get_total(), 2 ),
			'currency'  => 'INR',
			'returnUrl' => $this->get_return_url( $order ),
			'customer'  => $customer,
			'metadata'  => $metadata,
		);

		self::log( 'Creating payment for order ' . $order->get_id() . ' as ' . $ref );

		return $this->get_client()->create_payment( $payload, $ref . '-' . self::to_paise( $order->get_total() ) );
	}

	/**
	 * Client-facing message for an API error.
	 *
	 * @param WP_Error $error Error.
	 * @return string
	 */
	private function client_error_message( WP_Error $error ) {
		$concierge = $this->get_concierge_line();
		switch ( $error->get_error_code() ) {
			case 'OBSERVERS_UNHEALTHY':
				$msg = __( 'UPI payments are briefly unavailable while we verify our bank connection. Please try again in a minute.', 'youpi-luxury-gateway' );
				break;
			case 'DEVICE_SESSION_MISMATCH':
				$msg = __( 'A payment for this order is already open on another device. Please complete it there, or wait five minutes and try again.', 'youpi-luxury-gateway' );
				break;
			default:
				$data = $error->get_error_data();
				if ( is_array( $data ) && ! empty( $data['isBanned'] ) ) {
					$msg = __( 'We are unable to accept a UPI payment for this order. Please contact our concierge.', 'youpi-luxury-gateway' );
				} else {
					$msg = __( 'We could not start your UPI payment. Please try again.', 'youpi-luxury-gateway' );
				}
		}
		return $concierge ? $msg . ' ' . $concierge : $msg;
	}

	/**
	 * Add a checkout error and return a failure result.
	 *
	 * @param string $message Message.
	 * @return array
	 */
	private function fail( $message ) {
		wc_add_notice( $message, 'error' );
		return array( 'result' => 'failure' );
	}

	/**
	 * Convert a decimal rupee amount to integer paise without float drift.
	 *
	 * @param string|float $amount Amount.
	 * @return int
	 */
	public static function to_paise( $amount ) {
		return (int) round( (float) wc_format_decimal( $amount, 2 ) * 100 );
	}

	/**
	 * Apply a gateway status (from a webhook, a status lookup or a create call) to an order.
	 *
	 * @param WC_Order $order  Order.
	 * @param array    $data   Gateway payload: event|status, paymentId, amountPaise, currency, utr, verificationTag.
	 * @param string   $source Where the status came from: webhook, return, cron, create.
	 * @return string  What happened: paid, held, failed, ignored.
	 */
	public static function apply_gateway_status( WC_Order $order, array $data, $source ) {
		$status = strtoupper( (string) ( isset( $data['event'] ) ? $data['event'] : ( isset( $data['status'] ) ? $data['status'] : '' ) ) );
		if ( isset( $data['internalStatus'] ) && 'RECONCILED' === $data['internalStatus'] ) {
			$status = 'RECONCILED';
		}

		$payment_id = isset( $data['paymentId'] ) ? sanitize_text_field( $data['paymentId'] ) : '';
		$utr        = isset( $data['utr'] ) ? sanitize_text_field( $data['utr'] ) : '';
		$tag        = isset( $data['verificationTag'] ) ? sanitize_text_field( $data['verificationTag'] ) : '';
		$gateway    = self::instance();

		self::log( sprintf( 'Order %d: %s status %s (payment %s)', $order->get_id(), $source, $status, $payment_id ) );

		if ( $order->is_paid() || $order->has_status( 'refunded' ) ) {
			return 'ignored';
		}

		// Only the payment attempt currently attached to the order may fail it; a late
		// failure from an abandoned earlier attempt must not override a fresh one.
		if ( in_array( $status, self::UNPAID_STATUSES, true ) ) {
			if ( $payment_id !== $order->get_meta( self::META_PAYMENT_ID ) || ! $order->has_status( array( 'pending', 'failed' ) ) ) {
				return 'ignored';
			}
			$order->update_meta_data( self::META_STATUS, $status );
			if ( ! $order->has_status( 'failed' ) ) {
				$order->update_status(
					'failed',
					/* translators: %s: gateway status */
					sprintf( __( 'YouPI payment %s — no funds received. The client can retry from their account.', 'youpi-luxury-gateway' ), strtolower( $status ) )
				);
			} else {
				$order->save();
			}
			return 'failed';
		}

		if ( 'MANUAL_REVIEW' === $status ) {
			if ( $order->has_status( 'on-hold' ) && 'MANUAL_REVIEW' === $order->get_meta( self::META_STATUS ) ) {
				return 'ignored';
			}
			$order->update_meta_data( self::META_STATUS, $status );
			$order->update_meta_data( self::META_AWAITING_RECON, 'yes' );
			$order->update_status( 'on-hold', __( 'YouPI flagged this payment for manual review. Confirm the credit in your bank statement before releasing the timepiece.', 'youpi-luxury-gateway' ) );
			return 'held';
		}

		if ( ! in_array( $status, self::PAID_STATUSES, true ) ) {
			return 'ignored';
		}

		// Never trust status alone: the observed amount must match the order to the paisa.
		$expected_paise = self::to_paise( $order->get_total() );
		$received_paise = isset( $data['amountPaise'] ) ? (int) $data['amountPaise'] : -1;
		$currency       = isset( $data['currency'] ) ? strtoupper( (string) $data['currency'] ) : 'INR';
		if ( $received_paise !== $expected_paise || 'INR' !== $currency ) {
			if ( 'AMOUNT_MISMATCH' !== $order->get_meta( self::META_STATUS ) ) {
				$order->update_meta_data( self::META_STATUS, 'AMOUNT_MISMATCH' );
				$order->update_status(
					'on-hold',
					sprintf(
						/* translators: 1: received amount, 2: expected amount, 3: UTR */
						__( 'YouPI reported a payment of ₹%1$s but the order total is ₹%2$s (UTR %3$s). Do not dispatch — reconcile manually.', 'youpi-luxury-gateway' ),
						number_format( max( 0, $received_paise ) / 100, 2 ),
						number_format( $expected_paise / 100, 2 ),
						$utr ? $utr : '—'
					)
				);
			}
			return 'held';
		}

		if ( $utr ) {
			$order->update_meta_data( self::META_UTR, $utr );
		}
		if ( $payment_id ) {
			$order->update_meta_data( self::META_PAYMENT_ID, $payment_id );
		}

		$threshold = $gateway ? (float) $gateway->get_option( 'review_threshold', 0 ) : 0;
		$must_hold = 'RECONCILED' !== $status && (float) $order->get_total() >= $threshold;

		if ( $must_hold ) {
			if ( 'yes' === $order->get_meta( self::META_AWAITING_RECON ) && $order->has_status( 'on-hold' ) ) {
				return 'held';
			}
			$order->update_meta_data( self::META_STATUS, $status );
			$order->update_meta_data( self::META_AWAITING_RECON, 'yes' );
			$order->set_transaction_id( $utr );
			$order->update_status(
				'on-hold',
				sprintf(
					/* translators: 1: UTR, 2: verification tag */
					__( 'UPI payment observed (UTR %1$s, %2$s). High-value order held: confirm the credit in your bank statement, then set the order to Processing. It will be released automatically if the gateway reports it reconciled.', 'youpi-luxury-gateway' ),
					$utr ? $utr : '—',
					$tag ? $tag : $status
				)
			);
			return 'held';
		}

		$order->update_meta_data( self::META_STATUS, $status );
		$order->delete_meta_data( self::META_AWAITING_RECON );
		$order->add_order_note(
			sprintf(
				/* translators: 1: gateway status, 2: UTR, 3: verification tag */
				__( 'YouPI payment %1$s. UTR: %2$s. Verification: %3$s.', 'youpi-luxury-gateway' ),
				$status,
				$utr ? $utr : '—',
				$tag ? $tag : '—'
			)
		);
		$order->payment_complete( $utr );
		return 'paid';
	}

	/**
	 * Refunds are possible only once a UPI payment (UTR) is recorded on the order.
	 *
	 * @param WC_Order $order Order.
	 * @return bool
	 */
	public function can_refund_order( $order ) {
		return $order instanceof WC_Order
			&& ( '' !== (string) $order->get_meta( self::META_UTR ) || '' !== (string) $order->get_transaction_id() );
	}

	/**
	 * Record a refund. YouPI has no refund API, so the payout is sent from the
	 * boutique's bank and tracked by Youpi_Luxury_Refunds until staff record it.
	 *
	 * @param int        $order_id Order id.
	 * @param float|null $amount   Refund amount.
	 * @param string     $reason   Refund reason.
	 * @return true|WP_Error
	 */
	public function process_refund( $order_id, $amount = null, $reason = '' ) {
		$order = wc_get_order( $order_id );
		if ( ! $order ) {
			return new WP_Error( 'youpi_refund_order', __( 'Order not found.', 'youpi-luxury-gateway' ) );
		}
		$result = Youpi_Luxury_Refunds::record_refund( $order, $amount, $reason );
		if ( true === $result ) {
			self::log( sprintf( 'Order %d: refund of %s recorded, payout pending.', $order->get_id(), $amount ) );
		}
		return $result;
	}

	/**
	 * Fetch the latest gateway status for an order and apply it.
	 *
	 * @param WC_Order $order  Order.
	 * @param string   $source Source label.
	 * @return string|null Outcome, or null when no lookup happened.
	 */
	public static function sync_order( WC_Order $order, $source ) {
		$gateway    = self::instance();
		$payment_id = (string) $order->get_meta( self::META_PAYMENT_ID );
		if ( ! $gateway || '' === $payment_id ) {
			return null;
		}

		$data = $gateway->get_client()->get_payment( $payment_id );
		if ( is_wp_error( $data ) ) {
			self::log( 'Status lookup failed for order ' . $order->get_id() . ': ' . $data->get_error_message(), 'warning' );
			return null;
		}
		if ( isset( $data['orderId'] ) && $data['orderId'] !== $order->get_meta( self::META_ORDER_REF ) ) {
			self::log( 'Status lookup for order ' . $order->get_id() . ' returned a different order reference; ignoring.', 'error' );
			return null;
		}
		return self::apply_gateway_status( $order, $data, $source );
	}

	/**
	 * Cron: reconcile recent pending / held orders in case a webhook was missed.
	 */
	public static function reconcile_pending_orders() {
		$orders = wc_get_orders(
			array(
				'payment_method' => YOUPI_LUXURY_GATEWAY_ID,
				'status'         => array( 'pending', 'on-hold' ),
				'date_created'   => '>' . ( time() - 3 * DAY_IN_SECONDS ),
				'limit'          => 25,
				'orderby'        => 'date',
				'order'          => 'DESC',
			)
		);
		foreach ( $orders as $order ) {
			if ( $order->has_status( 'on-hold' ) && 'yes' !== $order->get_meta( self::META_AWAITING_RECON ) ) {
				continue;
			}
			self::sync_order( $order, 'cron' );
		}
	}

	/**
	 * Order-received page: refresh status and show a concierge panel.
	 *
	 * @param int $order_id Order id.
	 */
	public function thankyou_page( $order_id ) {
		$order = wc_get_order( $order_id );
		if ( ! $order ) {
			return;
		}

		if ( $order->has_status( 'pending' ) ) {
			self::sync_order( $order, 'return' );
			$order = wc_get_order( $order_id );
		}

		$utr = (string) $order->get_meta( self::META_UTR );

		echo '<section class="youpi-lux-panel">';
		echo '<h2 class="youpi-lux-panel__title">' . esc_html__( 'Payment', 'youpi-luxury-gateway' ) . '</h2>';

		if ( $order->is_paid() ) {
			echo '<p>' . esc_html__( 'Your UPI payment has been received. Your timepiece is now being prepared for fully insured delivery.', 'youpi-luxury-gateway' ) . '</p>';
		} elseif ( $order->has_status( 'on-hold' ) ) {
			echo '<p>' . esc_html__( 'Thank you — we have received notice of your UPI payment. As with every high-value piece, our team is confirming the credit with our bank before dispatch. You will receive an email the moment it is confirmed.', 'youpi-luxury-gateway' ) . '</p>';
		} elseif ( $order->has_status( 'failed' ) ) {
			echo '<p>' . esc_html__( 'Your UPI payment was not completed and no funds were taken. You may try again below.', 'youpi-luxury-gateway' ) . '</p>';
			echo '<p><a class="button youpi-lux-button" href="' . esc_url( $order->get_checkout_payment_url() ) . '">' . esc_html__( 'Retry payment', 'youpi-luxury-gateway' ) . '</a></p>';
		} else {
			echo '<p class="youpi-lux-pending" data-youpi-pending="1">' . esc_html__( 'We are verifying your UPI payment with our bank. This page will refresh automatically.', 'youpi-luxury-gateway' ) . '</p>';
			// Re-check a few times while verification completes.
			echo "<script>(function(){var k='youpi_lux_refresh',n=12;try{n=parseInt(sessionStorage.getItem(k)||'0',10);sessionStorage.setItem(k,n+1);}catch(e){}if(n<12){setTimeout(function(){location.reload();},10000);}})();</script>";
		}

		if ( $utr ) {
			/* translators: %s: UPI transaction reference */
			echo '<p class="youpi-lux-utr">' . esc_html( sprintf( __( 'UPI reference (UTR): %s', 'youpi-luxury-gateway' ), $utr ) ) . '</p>';
		}

		$concierge = $this->get_concierge_line();
		if ( $concierge ) {
			echo '<p class="youpi-lux-concierge">' . esc_html( $concierge ) . '</p>';
		}
		echo '</section>';
	}

	/**
	 * Add payment context to customer emails.
	 *
	 * @param WC_Order $order         Order.
	 * @param bool     $sent_to_admin Whether the email is for the admin.
	 * @param bool     $plain_text    Plain-text email.
	 */
	public function email_instructions( $order, $sent_to_admin, $plain_text = false ) {
		if ( $sent_to_admin || ! $order instanceof WC_Order || $this->id !== $order->get_payment_method() ) {
			return;
		}
		if ( $order->has_status( 'on-hold' ) && 'yes' === $order->get_meta( self::META_AWAITING_RECON ) ) {
			$text = __( 'We have received notice of your UPI payment and are confirming the credit with our bank before your timepiece is dispatched.', 'youpi-luxury-gateway' );
			echo $plain_text ? esc_html( $text ) . "\n\n" : '<p>' . esc_html( $text ) . '</p>';
		}
	}

	/**
	 * Front-end styles on checkout and order pages.
	 */
	public function enqueue_styles() {
		if ( 'yes' !== $this->enabled ) {
			return;
		}
		if ( is_checkout() || is_wc_endpoint_url( 'order-received' ) || is_account_page() ) {
			wp_enqueue_style( 'youpi-luxury', YOUPI_LUXURY_URL . 'assets/css/youpi-luxury.css', array(), YOUPI_LUXURY_VERSION );
		}
	}

	/**
	 * The registered gateway instance.
	 *
	 * @return WC_Gateway_YouPI_Luxury|null
	 */
	public static function instance() {
		if ( ! function_exists( 'WC' ) || ! WC()->payment_gateways() ) {
			return null;
		}
		$gateways = WC()->payment_gateways()->payment_gateways();
		return isset( $gateways[ YOUPI_LUXURY_GATEWAY_ID ] ) ? $gateways[ YOUPI_LUXURY_GATEWAY_ID ] : null;
	}

	/**
	 * Log a message when debug logging is on (errors are always logged).
	 *
	 * @param string $message Message.
	 * @param string $level   Log level.
	 */
	public static function log( $message, $level = 'info' ) {
		$gateway = self::instance();
		if ( 'error' !== $level && ( ! $gateway || 'yes' !== $gateway->get_option( 'debug' ) ) ) {
			return;
		}
		wc_get_logger()->log( $level, $message, self::$log_context );
	}
}
