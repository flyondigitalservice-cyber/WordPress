<?php
/**
 * HTML rendering (for printing) and the document endpoint that serves HTML,
 * PDF downloads and CSV exports.
 *
 * Endpoint: admin-ajax.php?action=ipsm_document&type=invoice&order_ids=1,2&output=html|pdf
 *
 * @package Invoice_Packing_Slip_Maker
 */

defined( 'ABSPATH' ) || exit;

/**
 * Renderer + endpoint.
 */
class IPSM_Renderer {

	const ACTION = 'ipsm_document';
	const NONCE  = 'ipsm_document';

	/**
	 * Hooks.
	 */
	public static function init() {
		add_action( 'wp_ajax_' . self::ACTION, array( __CLASS__, 'handle' ) );
		add_action( 'wp_ajax_nopriv_' . self::ACTION, array( __CLASS__, 'handle' ) );
	}

	/**
	 * Build a document URL.
	 *
	 * @param int|int[] $order_ids Order ID(s).
	 * @param string    $type      Document type.
	 * @param string    $output    html|pdf.
	 * @param array     $extra     Extra query args.
	 * @return string
	 */
	public static function url( $order_ids, $type = IPSM_Document::TYPE_INVOICE, $output = 'html', $extra = array() ) {
		$args = array_merge(
			array(
				'action'    => self::ACTION,
				'type'      => $type,
				'order_ids' => implode( ',', array_map( 'absint', (array) $order_ids ) ),
				'output'    => $output,
				'_wpnonce'  => wp_create_nonce( self::NONCE ),
			),
			$extra
		);
		return add_query_arg( $args, admin_url( 'admin-ajax.php' ) );
	}

	/**
	 * Whether the current user may manage documents for any order.
	 *
	 * @return bool
	 */
	public static function is_shop_manager() {
		return current_user_can( 'edit_shop_orders' ) || current_user_can( 'manage_woocommerce' );
	}

	/**
	 * Whether the current visitor may download the invoice of an order as a customer.
	 *
	 * @param WC_Order $order Order.
	 * @param string   $key   Order key supplied by a guest (optional).
	 * @return bool
	 */
	public static function customer_can_view_invoice( WC_Order $order, $key = '' ) {
		if ( ! IPSM_Settings::enabled( 'customer_download' ) ) {
			return false;
		}
		$owns = ( get_current_user_id() && (int) $order->get_customer_id() === get_current_user_id() )
			|| ( $key && hash_equals( $order->get_order_key(), $key ) );
		if ( ! $owns ) {
			return false;
		}
		// Customers only get an invoice once one exists or the order has been paid.
		$allowed = $order->get_meta( IPSM_Document::META_NUMBER ) || $order->is_paid();
		return (bool) apply_filters( 'ipsm_customer_can_view_invoice', $allowed, $order );
	}

	/**
	 * Request handler.
	 */
	public static function handle() {
		// phpcs:disable WordPress.Security.NonceVerification.Recommended -- checked below.
		$type   = isset( $_GET['type'] ) ? sanitize_key( wp_unslash( $_GET['type'] ) ) : IPSM_Document::TYPE_INVOICE;
		$output = isset( $_GET['output'] ) ? sanitize_key( wp_unslash( $_GET['output'] ) ) : 'html';
		$ids    = isset( $_GET['order_ids'] ) ? array_filter( array_map( 'absint', explode( ',', sanitize_text_field( wp_unslash( $_GET['order_ids'] ) ) ) ) ) : array();
		$key    = isset( $_GET['key'] ) ? sanitize_text_field( wp_unslash( $_GET['key'] ) ) : '';
		$nonce  = isset( $_GET['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ) : '';
		$embed  = ! empty( $_GET['embed'] );
		// phpcs:enable

		if ( ! in_array( $type, IPSM_Document::types(), true ) || ! in_array( $output, array( 'html', 'pdf', 'csv' ), true ) || ! $ids ) {
			wp_die( esc_html__( 'Invalid document request.', 'invoice-packing-slip-maker' ), '', array( 'response' => 400 ) );
		}

		$valid_nonce = (bool) wp_verify_nonce( $nonce, self::NONCE );
		$orders      = array();

		foreach ( array_slice( $ids, 0, 500 ) as $id ) {
			$order = wc_get_order( $id );
			if ( ! $order instanceof WC_Order || $order instanceof WC_Order_Refund ) {
				continue;
			}
			if ( self::is_shop_manager() && $valid_nonce ) {
				$orders[] = $order;
				continue;
			}
			// Customers: one invoice at a time, either logged in with a nonce or with the order key.
			$customer_ok = 1 === count( $ids )
				&& IPSM_Document::TYPE_INVOICE === $type
				&& 'csv' !== $output
				&& ( $valid_nonce || $key )
				&& self::customer_can_view_invoice( $order, $key );
			if ( $customer_ok ) {
				$orders[] = $order;
			}
		}

		if ( ! $orders ) {
			wp_die( esc_html__( 'You are not allowed to view this document.', 'invoice-packing-slip-maker' ), '', array( 'response' => 403 ) );
		}

		if ( 'csv' === $output ) {
			IPSM_Export::send_csv( $orders );
			exit;
		}

		// Preview (settings page) never assigns invoice numbers. Shop managers only.
		$preview = ! empty( $_GET['preview'] ) && self::is_shop_manager() && $valid_nonce; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		try {
			$docs = array();
			foreach ( $orders as $order ) {
				$docs[] = new IPSM_Document( $order, $type, $preview );
			}

			if ( 'pdf' === $output ) {
				$inline = ! empty( $_GET['inline'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				self::send_pdf( $docs, $inline );
				exit;
			}

			$html = self::html( $docs, ! $embed );
		} catch ( \Throwable $e ) {
			IPSM_Document::log( 'Document generation failed: ' . $e->getMessage() );
			wp_die( esc_html__( 'Sorry, this document could not be generated. Details were written to WooCommerce → Status → Logs.', 'invoice-packing-slip-maker' ), '', array( 'response' => 500 ) );
		}

		self::clean_buffers();
		nocache_headers();
		header( 'Content-Type: text/html; charset=utf-8' );
		echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- templates escape their own output.
		exit;
	}

	/**
	 * Stream a PDF to the browser.
	 *
	 * @param IPSM_Document[] $docs   Documents.
	 * @param bool            $inline Show in browser instead of download.
	 */
	public static function send_pdf( array $docs, $inline = false ) {
		$pdf      = IPSM_PDF::generate( $docs );
		$filename = 1 === count( $docs )
			? $docs[0]->filename()
			: sanitize_file_name( $docs[0]->type . 's-' . gmdate( 'Y-m-d-His' ) ) . '.pdf';

		self::clean_buffers();
		nocache_headers();
		header( 'Content-Type: application/pdf' );
		header( 'Content-Disposition: ' . ( $inline ? 'inline' : 'attachment' ) . '; filename="' . $filename . '"' );
		header( 'Content-Length: ' . strlen( $pdf ) );
		echo $pdf; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- binary PDF.
	}

	/**
	 * Discard anything other plugins or the theme have already printed, so
	 * downloads are never corrupted by stray whitespace or notices.
	 */
	public static function clean_buffers() {
		while ( ob_get_level() > 0 ) {
			ob_end_clean();
		}
	}

	/**
	 * Locate a template, allowing themes to override it in /yourtheme/invoice-packing-slip-maker/.
	 *
	 * @param string $name Template file name.
	 * @return string
	 */
	public static function locate( $name ) {
		$theme = locate_template( array( 'invoice-packing-slip-maker/' . $name ) );
		$path  = $theme ? $theme : IPSM_DIR . 'templates/' . $name;
		return apply_filters( 'ipsm_template_path', $path, $name );
	}

	/**
	 * Full HTML page for one or more documents.
	 *
	 * @param IPSM_Document[] $docs    Documents.
	 * @param bool            $toolbar Show the Print / Download toolbar.
	 * @return string
	 */
	public static function html( array $docs, $toolbar = true ) {
		$docs = array_values( $docs );
		$type = $docs ? $docs[0]->type : IPSM_Document::TYPE_INVOICE;

		$pdf_url = '';
		if ( $toolbar && $docs ) {
			$ids   = array_map(
				static function ( $d ) {
					return $d->order->get_id();
				},
				$docs
			);
			$extra = array();
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			if ( ! empty( $_GET['key'] ) ) {
				$extra['key'] = sanitize_text_field( wp_unslash( $_GET['key'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			}
			$pdf_url = self::url( $ids, $type, 'pdf', $extra );
		}

		ob_start();
		include self::locate( 'document.php' );
		return ob_get_clean();
	}

	/**
	 * Render the body of a single document.
	 *
	 * @param IPSM_Document $doc Document.
	 */
	public static function render_body( IPSM_Document $doc ) {
		$shop  = $doc->shop();
		$order = $doc->order;
		$texts = $doc->texts();
		include self::locate( $doc->type . '.php' );
	}
}
