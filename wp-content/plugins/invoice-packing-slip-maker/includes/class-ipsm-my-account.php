<?php
/**
 * Customer-facing invoice download links (My Account → Orders, order view, thank-you page).
 *
 * @package Invoice_Packing_Slip_Maker
 */

defined( 'ABSPATH' ) || exit;

/**
 * My Account.
 */
class IPSM_My_Account {

	/**
	 * Hooks.
	 */
	public static function init() {
		add_filter( 'woocommerce_my_account_my_orders_actions', array( __CLASS__, 'orders_list_action' ), 10, 2 );
		add_action( 'woocommerce_order_details_after_order_table', array( __CLASS__, 'order_details_button' ) );
	}

	/**
	 * Customer-safe URL for the invoice PDF.
	 *
	 * @param WC_Order $order Order.
	 * @return string
	 */
	public static function invoice_url( WC_Order $order ) {
		$extra = get_current_user_id() ? array() : array( 'key' => $order->get_order_key() );
		return IPSM_Renderer::url( $order->get_id(), IPSM_Document::TYPE_INVOICE, 'pdf', $extra );
	}

	/**
	 * "Invoice" button in My Account → Orders.
	 *
	 * @param array    $actions Actions.
	 * @param WC_Order $order   Order.
	 * @return array
	 */
	public static function orders_list_action( $actions, $order ) {
		if ( IPSM_Renderer::customer_can_view_invoice( $order ) ) {
			$actions['ipsm-invoice'] = array(
				'url'  => self::invoice_url( $order ),
				'name' => __( 'Invoice', 'invoice-packing-slip-maker' ),
			);
		}
		return $actions;
	}

	/**
	 * Download button below the order details table.
	 *
	 * @param WC_Order $order Order.
	 */
	public static function order_details_button( $order ) {
		if ( ! $order instanceof WC_Order ) {
			return;
		}
		if ( ! IPSM_Renderer::customer_can_view_invoice( $order, get_current_user_id() ? '' : $order->get_order_key() ) ) {
			return;
		}
		printf(
			'<p class="ipsm-download-invoice"><a class="button" href="%s">%s</a></p>',
			esc_url( self::invoice_url( $order ) ),
			esc_html__( 'Download invoice (PDF)', 'invoice-packing-slip-maker' )
		);
	}
}
