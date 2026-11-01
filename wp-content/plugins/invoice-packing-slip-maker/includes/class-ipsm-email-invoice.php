<?php
/**
 * "Invoice (PDF)" customer email. Appears under WooCommerce → Settings → Emails,
 * where the subject, heading, extra content and format can be edited.
 *
 * @package Invoice_Packing_Slip_Maker
 */

defined( 'ABSPATH' ) || exit;

/**
 * Invoice email.
 */
class IPSM_Email_Invoice extends WC_Email {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id             = 'ipsm_customer_invoice';
		$this->customer_email = true;
		$this->title          = __( 'Invoice (PDF)', 'invoice-packing-slip-maker' );
		$this->description    = __( 'Sends the customer their invoice as a PDF attachment. Sent from the order screen, bulk actions, or automatically on a chosen order status.', 'invoice-packing-slip-maker' );
		$this->template_html  = 'emails/ipsm-customer-invoice.php';
		$this->template_plain = 'emails/plain/ipsm-customer-invoice.php';
		$this->template_base  = IPSM_DIR . 'templates/';
		$this->placeholders   = array(
			'{order_date}'     => '',
			'{order_number}'   => '',
			'{invoice_number}' => '',
		);

		parent::__construct();
	}

	/**
	 * Default subject.
	 *
	 * @return string
	 */
	public function get_default_subject() {
		return __( 'Invoice {invoice_number} for your order #{order_number}', 'invoice-packing-slip-maker' );
	}

	/**
	 * Default heading.
	 *
	 * @return string
	 */
	public function get_default_heading() {
		return __( 'Your invoice', 'invoice-packing-slip-maker' );
	}

	/**
	 * Default additional content.
	 *
	 * @return string
	 */
	public function get_default_additional_content() {
		return __( 'Thank you for shopping with us.', 'invoice-packing-slip-maker' );
	}

	/**
	 * Send the email.
	 *
	 * @param int           $order_id Order ID.
	 * @param WC_Order|bool $order    Order.
	 * @return bool
	 */
	public function trigger( $order_id, $order = false ) {
		$this->setup_locale();

		if ( $order_id && ! is_a( $order, 'WC_Order' ) ) {
			$order = wc_get_order( $order_id );
		}

		$sent = false;
		if ( is_a( $order, 'WC_Order' ) ) {
			$doc                                    = new IPSM_Document( $order, IPSM_Document::TYPE_INVOICE );
			$this->object                           = $order;
			$this->recipient                        = $order->get_billing_email();
			$this->placeholders['{order_date}']     = wc_format_datetime( $order->get_date_created() );
			$this->placeholders['{order_number}']   = $order->get_order_number();
			$this->placeholders['{invoice_number}'] = $doc->invoice_number();

			if ( $this->is_enabled() && $this->get_recipient() ) {
				$sent = $this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
			}
		}

		$this->restore_locale();
		return (bool) $sent;
	}

	/**
	 * HTML content.
	 *
	 * @return string
	 */
	public function get_content_html() {
		return wc_get_template_html(
			$this->template_html,
			array(
				'order'              => $this->object,
				'email_heading'      => $this->get_heading(),
				'additional_content' => $this->get_additional_content(),
				'sent_to_admin'      => false,
				'plain_text'         => false,
				'email'              => $this,
			),
			'',
			$this->template_base
		);
	}

	/**
	 * Plain content.
	 *
	 * @return string
	 */
	public function get_content_plain() {
		return wc_get_template_html(
			$this->template_plain,
			array(
				'order'              => $this->object,
				'email_heading'      => $this->get_heading(),
				'additional_content' => $this->get_additional_content(),
				'sent_to_admin'      => false,
				'plain_text'         => true,
				'email'              => $this,
			),
			'',
			$this->template_base
		);
	}
}
