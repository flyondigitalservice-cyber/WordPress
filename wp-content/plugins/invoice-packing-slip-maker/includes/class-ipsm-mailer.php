<?php
/**
 * Email integration: registers the Invoice email, attaches invoice PDFs to
 * WooCommerce emails and sends invoices automatically / on demand.
 *
 * @package Invoice_Packing_Slip_Maker
 */

defined( 'ABSPATH' ) || exit;

/**
 * Mailer.
 */
class IPSM_Mailer {

	/**
	 * Hooks.
	 */
	public static function init() {
		add_filter( 'woocommerce_email_classes', array( __CLASS__, 'register_email' ) );
		add_filter( 'woocommerce_email_attachments', array( __CLASS__, 'attach_pdf' ), 10, 4 );
		add_action( 'woocommerce_order_status_changed', array( __CLASS__, 'maybe_auto_send' ), 20, 4 );
		add_action( 'ipsm_cleanup_temp', array( __CLASS__, 'cleanup_temp' ) );
	}

	/**
	 * Register the email class with WooCommerce.
	 *
	 * @param WC_Email[] $emails Emails.
	 * @return WC_Email[]
	 */
	public static function register_email( $emails ) {
		require_once IPSM_DIR . 'includes/class-ipsm-email-invoice.php';
		$emails['IPSM_Email_Invoice'] = new IPSM_Email_Invoice();
		return $emails;
	}

	/**
	 * Attach the invoice PDF to the selected WooCommerce emails.
	 *
	 * @param array    $attachments Attachments.
	 * @param string   $email_id    Email ID.
	 * @param mixed    $object      Email object (order).
	 * @param WC_Email $email       Email (WC 5.6+).
	 * @return array
	 */
	public static function attach_pdf( $attachments, $email_id, $object, $email = null ) {
		if ( ! $object instanceof WC_Order || $object instanceof WC_Order_Refund ) {
			return $attachments;
		}

		$wanted = 'ipsm_customer_invoice' === $email_id || in_array( $email_id, (array) IPSM_Settings::get( 'attach_to' ), true );
		if ( ! apply_filters( 'ipsm_attach_invoice', $wanted, $email_id, $object ) ) {
			return $attachments;
		}

		try {
			$file = self::write_temp_pdf( new IPSM_Document( $object, IPSM_Document::TYPE_INVOICE ) );
			if ( $file ) {
				$attachments[] = $file;
			}
		} catch ( \Throwable $e ) {
			// The WooCommerce email must still go out, just without the PDF.
			IPSM_Document::log( sprintf( 'Could not attach invoice to email "%s" for order %d: %s', $email_id, $object->get_id(), $e->getMessage() ) );
		}
		return $attachments;
	}

	/**
	 * Write the PDF into a private temporary folder so wp_mail() can attach it.
	 *
	 * @param IPSM_Document $doc Document.
	 * @return string|false Path.
	 */
	public static function write_temp_pdf( IPSM_Document $doc ) {
		$base = self::temp_dir();
		if ( ! $base ) {
			return false;
		}
		$dir = trailingslashit( $base ) . wp_generate_password( 16, false );
		if ( ! wp_mkdir_p( $dir ) ) {
			return false;
		}
		$path = trailingslashit( $dir ) . $doc->filename();
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
		if ( false === file_put_contents( $path, IPSM_PDF::generate( array( $doc ) ) ) ) {
			return false;
		}

		if ( ! wp_next_scheduled( 'ipsm_cleanup_temp' ) ) {
			wp_schedule_single_event( time() + HOUR_IN_SECONDS, 'ipsm_cleanup_temp' );
		}
		return $path;
	}

	/**
	 * Protected temp folder inside uploads.
	 *
	 * @return string|false
	 */
	private static function temp_dir() {
		$uploads = wp_upload_dir( null, false );
		if ( ! empty( $uploads['error'] ) ) {
			return false;
		}
		$dir = trailingslashit( $uploads['basedir'] ) . 'ipsm-temp';
		if ( ! is_dir( $dir ) ) {
			wp_mkdir_p( $dir );
			// phpcs:disable WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
			file_put_contents( $dir . '/.htaccess', "Require all denied\nDeny from all\n" );
			file_put_contents( $dir . '/index.php', "<?php\n// Silence is golden.\n" );
			// phpcs:enable
		}
		return $dir;
	}

	/**
	 * Delete temp PDFs older than one hour.
	 */
	public static function cleanup_temp() {
		$dir = self::temp_dir();
		if ( ! $dir ) {
			return;
		}
		$subs = glob( $dir . '/*', GLOB_ONLYDIR );
		foreach ( $subs ? $subs : array() as $sub ) {
			$mtime = @filemtime( $sub ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
			if ( $mtime && $mtime > time() - HOUR_IN_SECONDS ) {
				continue;
			}
			$files = glob( $sub . '/*' );
			foreach ( $files ? $files : array() as $file ) {
				wp_delete_file( $file );
			}
			@rmdir( $sub ); // phpcs:ignore
		}
		if ( glob( $dir . '/*', GLOB_ONLYDIR ) && ! wp_next_scheduled( 'ipsm_cleanup_temp' ) ) {
			wp_schedule_single_event( time() + HOUR_IN_SECONDS, 'ipsm_cleanup_temp' );
		}
	}

	/**
	 * Email the invoice to the customer.
	 *
	 * @param WC_Order $order Order.
	 * @return bool
	 */
	public static function send_invoice( WC_Order $order ) {
		$emails = WC()->mailer()->get_emails();
		if ( empty( $emails['IPSM_Email_Invoice'] ) || ! $order->get_billing_email() ) {
			return false;
		}
		try {
			$sent = $emails['IPSM_Email_Invoice']->trigger( $order->get_id(), $order );
		} catch ( \Throwable $e ) {
			IPSM_Document::log( sprintf( 'Invoice email failed for order %d: %s', $order->get_id(), $e->getMessage() ) );
			$sent = false;
		}

		$order->update_meta_data( IPSM_Document::META_SENT, time() );
		$order->save();
		$order->add_order_note(
			$sent
				/* translators: %s: email address */
				? sprintf( __( 'Invoice emailed to %s.', 'invoice-packing-slip-maker' ), $order->get_billing_email() )
				: __( 'Invoice email could not be sent (check that the "Invoice (PDF)" email is enabled in WooCommerce → Settings → Emails).', 'invoice-packing-slip-maker' )
		);
		return $sent;
	}

	/**
	 * Auto-send once when the order reaches the configured status.
	 *
	 * @param int      $order_id Order ID.
	 * @param string   $from     Old status.
	 * @param string   $to       New status.
	 * @param WC_Order $order    Order.
	 */
	public static function maybe_auto_send( $order_id, $from, $to, $order = null ) {
		$status = (string) IPSM_Settings::get( 'auto_send_status' );
		if ( ! $status || 'wc-' . $to !== $status ) {
			return;
		}
		try {
			$order = $order instanceof WC_Order ? $order : wc_get_order( $order_id );
			if ( ! $order || $order->get_meta( IPSM_Document::META_SENT ) ) {
				return;
			}
			self::send_invoice( $order );
		} catch ( \Throwable $e ) {
			// Never interrupt checkout or an order update because of the invoice.
			IPSM_Document::log( sprintf( 'Automatic invoice email failed for order %d: %s', $order_id, $e->getMessage() ) );
		}
	}
}
