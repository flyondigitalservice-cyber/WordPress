<?php
/**
 * CSV export of invoices (for accounting / spreadsheets).
 *
 * @package Invoice_Packing_Slip_Maker
 */

defined( 'ABSPATH' ) || exit;

/**
 * Export.
 */
class IPSM_Export {

	/**
	 * Stream a CSV with one row per invoice.
	 *
	 * @param WC_Order[] $orders Orders.
	 */
	public static function send_csv( array $orders ) {
		IPSM_Renderer::clean_buffers();
		nocache_headers();
		header( 'Content-Type: text/csv; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename="invoices-' . gmdate( 'Y-m-d-His' ) . '.csv"' );

		$out = fopen( 'php://output', 'w' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fopen
		fwrite( $out, "\xEF\xBB\xBF" ); // phpcs:ignore -- UTF-8 BOM so Excel detects the encoding.

		fputcsv( $out, self::headers() );
		foreach ( $orders as $order ) {
			fputcsv( $out, array_map( array( __CLASS__, 'safe' ), self::row( new IPSM_Document( $order, IPSM_Document::TYPE_INVOICE ) ) ) );
		}
		fclose( $out ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose
	}

	/**
	 * Column headers.
	 *
	 * @return string[]
	 */
	public static function headers() {
		return apply_filters(
			'ipsm_csv_headers',
			array(
				__( 'Invoice number', 'invoice-packing-slip-maker' ),
				__( 'Invoice date', 'invoice-packing-slip-maker' ),
				__( 'Order number', 'invoice-packing-slip-maker' ),
				__( 'Order date', 'invoice-packing-slip-maker' ),
				__( 'Status', 'invoice-packing-slip-maker' ),
				__( 'Customer', 'invoice-packing-slip-maker' ),
				__( 'Email', 'invoice-packing-slip-maker' ),
				__( 'Phone', 'invoice-packing-slip-maker' ),
				__( 'Billing address', 'invoice-packing-slip-maker' ),
				__( 'Shipping address', 'invoice-packing-slip-maker' ),
				__( 'Payment method', 'invoice-packing-slip-maker' ),
				__( 'Items', 'invoice-packing-slip-maker' ),
				__( 'Currency', 'invoice-packing-slip-maker' ),
				__( 'Subtotal', 'invoice-packing-slip-maker' ),
				__( 'Discount', 'invoice-packing-slip-maker' ),
				__( 'Shipping', 'invoice-packing-slip-maker' ),
				__( 'Fees', 'invoice-packing-slip-maker' ),
				__( 'Tax', 'invoice-packing-slip-maker' ),
				__( 'Refunded', 'invoice-packing-slip-maker' ),
				__( 'Total', 'invoice-packing-slip-maker' ),
			)
		);
	}

	/**
	 * One row.
	 *
	 * @param IPSM_Document $doc Invoice document.
	 * @return array
	 */
	public static function row( IPSM_Document $doc ) {
		$order = $doc->order;
		$items = array();
		foreach ( $doc->items() as $item ) {
			$items[] = $item['name'] . ( $item['sku'] ? ' [' . $item['sku'] . ']' : '' ) . ' x ' . wc_stock_amount( $item['qty'] );
		}
		$fees = 0;
		foreach ( $order->get_fees() as $fee ) {
			$fees += (float) $fee->get_total();
		}
		$dp = wc_get_price_decimals();

		return apply_filters(
			'ipsm_csv_row',
			array(
				$doc->invoice_number(),
				(int) $order->get_meta( IPSM_Document::META_DATE ) ? wp_date( 'Y-m-d', (int) $order->get_meta( IPSM_Document::META_DATE ) ) : '',
				$order->get_order_number(),
				$order->get_date_created() ? $order->get_date_created()->date_i18n( 'Y-m-d H:i' ) : '',
				wc_get_order_status_name( $order->get_status() ),
				trim( $order->get_formatted_billing_full_name() ),
				$order->get_billing_email(),
				$order->get_billing_phone(),
				implode( ', ', $doc->billing_lines() ),
				implode( ', ', $doc->has_shipping_address() ? $doc->shipping_lines() : array() ),
				$order->get_payment_method_title(),
				implode( '; ', $items ),
				$order->get_currency(),
				wc_format_decimal( $order->get_subtotal(), $dp ),
				wc_format_decimal( $order->get_total_discount(), $dp ),
				wc_format_decimal( $order->get_shipping_total(), $dp ),
				wc_format_decimal( $fees, $dp ),
				wc_format_decimal( $order->get_total_tax(), $dp ),
				wc_format_decimal( $order->get_total_refunded(), $dp ),
				wc_format_decimal( $order->get_total(), $dp ),
			),
			$doc
		);
	}

	/**
	 * Neutralise spreadsheet formula injection.
	 *
	 * @param mixed $value Cell.
	 * @return string
	 */
	public static function safe( $value ) {
		$value = (string) $value;
		// Numbers and phone numbers ("+91 98200 33344", "-12.50") cannot carry a formula payload.
		$harmless = (bool) preg_match( '/^[+-]?[\d\s().,\/-]+$/', $value );
		if ( '' !== $value && ! $harmless && in_array( $value[0], array( '=', '+', '-', '@', "\t", "\r" ), true ) ) {
			$value = "'" . $value;
		}
		return $value;
	}
}
