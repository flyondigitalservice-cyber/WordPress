<?php
/**
 * Refund tracking for YouPI UPI orders.
 *
 * YouPI PG settles payments directly into the merchant's bank account and has no
 * refund API, so the money must be returned by the merchant's own bank (UPI or
 * IMPS/NEFT to the payer identified by the original UTR). This class records each
 * WooCommerce refund as a pending payout and lets staff enter the bank reference
 * once it has been sent, which notifies the client.
 *
 * @package YouPI_Luxury_Gateway
 */

defined( 'ABSPATH' ) || exit;

/**
 * Refund records and the order-screen meta box.
 */
class Youpi_Luxury_Refunds {

	const META_REFUNDS = '_youpi_refunds';
	const NONCE_ACTION = 'youpi_luxury_refund_payout';
	const NONCE_FIELD  = 'youpi_luxury_refund_nonce';

	/**
	 * Hook the admin meta box and its save handler.
	 */
	public static function init() {
		add_action( 'add_meta_boxes', array( __CLASS__, 'add_meta_box' ), 30 );
		add_action( 'woocommerce_process_shop_order_meta', array( __CLASS__, 'save_payouts' ), 50 );
	}

	/**
	 * Validate a refund request and record it as a pending payout.
	 *
	 * Called from the gateway's process_refund(), after WooCommerce has created the
	 * refund object. Returning a WP_Error makes WooCommerce delete that refund.
	 *
	 * @param WC_Order $order  Order.
	 * @param float    $amount Amount to refund.
	 * @param string   $reason Reason entered by staff.
	 * @return true|WP_Error
	 */
	public static function record_refund( WC_Order $order, $amount, $reason ) {
		if ( null === $amount || (float) $amount <= 0 ) {
			return new WP_Error( 'youpi_refund_amount', __( 'Enter a refund amount greater than zero.', 'youpi-luxury-gateway' ) );
		}

		$utr = (string) $order->get_meta( WC_Gateway_YouPI_Luxury::META_UTR );
		if ( '' === $utr ) {
			$utr = (string) $order->get_transaction_id();
		}
		if ( '' === $utr ) {
			return new WP_Error( 'youpi_refund_unpaid', __( 'No confirmed UPI payment (UTR) is recorded on this order, so there is nothing to refund through UPI.', 'youpi-luxury-gateway' ) );
		}

		// WooCommerce has already saved the refund; the newest one is ours.
		$refunds   = $order->get_refunds();
		$refund_id = $refunds ? (int) $refunds[0]->get_id() : 0;
		$user      = wp_get_current_user();

		$records   = self::get_records( $order );
		$records[] = array(
			'key'          => wp_generate_uuid4(),
			'refund_id'    => $refund_id,
			'amount'       => wc_format_decimal( $amount, 2 ),
			'reason'       => sanitize_text_field( (string) $reason ),
			'original_utr' => $utr,
			'status'       => 'pending',
			'created_at'   => time(),
			'created_by'   => $user ? $user->display_name : '',
			'payout_ref'   => '',
			'sent_at'      => 0,
			'sent_by'      => '',
		);
		$order->update_meta_data( self::META_REFUNDS, $records );
		$order->add_order_note(
			sprintf(
				/* translators: 1: amount, 2: original UTR */
				__( 'UPI refund of %1$s recorded — payout pending. Send it from the boutique bank account to the payer of UTR %2$s, then enter the payout reference in the "UPI refund payouts" box.', 'youpi-luxury-gateway' ),
				wc_price( $amount, array( 'currency' => $order->get_currency() ) ),
				$utr
			)
		);
		$order->save();

		return true;
	}

	/**
	 * Refund records stored on an order.
	 *
	 * @param WC_Order $order Order.
	 * @return array[]
	 */
	public static function get_records( WC_Order $order ) {
		$records = $order->get_meta( self::META_REFUNDS );
		return is_array( $records ) ? array_values( $records ) : array();
	}

	/**
	 * Records whose WooCommerce refund still exists (a refund deleted in the
	 * order screen no longer needs a payout).
	 *
	 * @param WC_Order $order Order.
	 * @return array[]
	 */
	public static function get_live_records( WC_Order $order ) {
		$refund_ids = array_map(
			static function ( $refund ) {
				return (int) $refund->get_id();
			},
			$order->get_refunds()
		);
		return array_values(
			array_filter(
				self::get_records( $order ),
				static function ( $record ) use ( $refund_ids ) {
					return 'sent' === $record['status'] || empty( $record['refund_id'] ) || in_array( (int) $record['refund_id'], $refund_ids, true );
				}
			)
		);
	}

	/**
	 * Register the meta box on YouPI orders (HPOS and legacy order screens).
	 *
	 * @param string $screen_id Current screen.
	 */
	public static function add_meta_box( $screen_id ) {
		$order_screen = function_exists( 'wc_get_page_screen_id' ) ? wc_get_page_screen_id( 'shop-order' ) : 'shop_order';
		if ( $screen_id !== $order_screen ) {
			return;
		}
		add_meta_box(
			'youpi-luxury-refunds',
			__( 'UPI refund payouts', 'youpi-luxury-gateway' ),
			array( __CLASS__, 'render_meta_box' ),
			$order_screen,
			'side',
			'default'
		);
	}

	/**
	 * Meta box contents.
	 *
	 * @param WP_Post|WC_Order $post_or_order Legacy post or HPOS order.
	 */
	public static function render_meta_box( $post_or_order ) {
		$order = $post_or_order instanceof WC_Order ? $post_or_order : wc_get_order( $post_or_order->ID );
		if ( ! $order || YOUPI_LUXURY_GATEWAY_ID !== $order->get_payment_method() ) {
			echo '<p>' . esc_html__( 'This order was not paid with YouPI UPI.', 'youpi-luxury-gateway' ) . '</p>';
			return;
		}

		$records = self::get_live_records( $order );
		if ( ! $records ) {
			echo '<p>' . esc_html__( 'No UPI refunds yet. Use the Refund button below the order items; each refund will appear here until its bank payout is recorded.', 'youpi-luxury-gateway' ) . '</p>';
			return;
		}

		wp_nonce_field( self::NONCE_ACTION, self::NONCE_FIELD );
		echo '<ul class="youpi-lux-refunds">';
		foreach ( $records as $record ) {
			$amount = wc_price( $record['amount'], array( 'currency' => $order->get_currency() ) );
			echo '<li style="border-bottom:1px solid #eee;padding:8px 0;">';
			echo '<strong>' . wp_kses_post( $amount ) . '</strong> — ';
			if ( 'sent' === $record['status'] ) {
				echo '<span style="color:#2e7d32;">' . esc_html__( 'Sent', 'youpi-luxury-gateway' ) . '</span><br />';
				/* translators: 1: payout reference, 2: date, 3: staff name */
				echo '<small>' . esc_html( sprintf( __( 'Ref %1$s · %2$s · %3$s', 'youpi-luxury-gateway' ), $record['payout_ref'], wp_date( get_option( 'date_format' ), (int) $record['sent_at'] ), $record['sent_by'] ) ) . '</small>';
			} else {
				echo '<span style="color:#b26a00;">' . esc_html__( 'Payout pending', 'youpi-luxury-gateway' ) . '</span><br />';
				/* translators: %s: original UTR */
				echo '<small>' . esc_html( sprintf( __( 'Pay back the sender of UTR %s', 'youpi-luxury-gateway' ), $record['original_utr'] ) ) . '</small>';
				if ( '' !== $record['reason'] ) {
					echo '<br /><small>' . esc_html( $record['reason'] ) . '</small>';
				}
				$field = 'youpi_refund_payout[' . $record['key'] . ']';
				echo '<p><label for="' . esc_attr( 'youpi-payout-' . $record['key'] ) . '">' . esc_html__( 'Bank payout reference (UTR)', 'youpi-luxury-gateway' ) . '</label>';
				echo '<input type="text" class="widefat" id="' . esc_attr( 'youpi-payout-' . $record['key'] ) . '" name="' . esc_attr( $field ) . '" value="" autocomplete="off" /></p>';
			}
			echo '</li>';
		}
		echo '</ul>';
		echo '<p class="description">' . esc_html__( 'Enter the reference once the money has left your bank, then click Update. The client is emailed the reference.', 'youpi-luxury-gateway' ) . '</p>';
	}

	/**
	 * Save payout references entered in the meta box.
	 *
	 * @param int $order_id Order id.
	 */
	public static function save_payouts( $order_id ) {
		if ( empty( $_POST[ self::NONCE_FIELD ] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST[ self::NONCE_FIELD ] ) ), self::NONCE_ACTION ) ) {
			return;
		}
		if ( empty( $_POST['youpi_refund_payout'] ) || ! is_array( $_POST['youpi_refund_payout'] ) || ! current_user_can( 'edit_shop_orders' ) ) {
			return;
		}

		$order = wc_get_order( $order_id );
		if ( ! $order ) {
			return;
		}

		$submitted = array_map( 'sanitize_text_field', wp_unslash( $_POST['youpi_refund_payout'] ) );
		$records   = self::get_records( $order );
		$user      = wp_get_current_user();
		$changed   = false;

		foreach ( $records as $i => $record ) {
			$ref = isset( $submitted[ $record['key'] ] ) ? trim( $submitted[ $record['key'] ] ) : '';
			if ( '' === $ref || 'pending' !== $record['status'] ) {
				continue;
			}
			$records[ $i ]['status']     = 'sent';
			$records[ $i ]['payout_ref'] = $ref;
			$records[ $i ]['sent_at']    = time();
			$records[ $i ]['sent_by']    = $user ? $user->display_name : '';
			$changed                     = true;

			$order->add_order_note(
				sprintf(
					/* translators: 1: amount, 2: bank reference */
					__( 'Your refund of %1$s has been sent to your bank account. Bank reference: %2$s. Depending on your bank it may take up to 3 working days to appear.', 'youpi-luxury-gateway' ),
					wc_price( $record['amount'], array( 'currency' => $order->get_currency() ) ),
					$ref
				),
				1,
				true
			);
		}

		if ( $changed ) {
			$order->update_meta_data( self::META_REFUNDS, $records );
			$order->save();
		}
	}
}
