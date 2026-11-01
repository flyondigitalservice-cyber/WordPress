<?php
/**
 * Invoice email (HTML).
 *
 * Override by copying to yourtheme/woocommerce/emails/ipsm-customer-invoice.php.
 * Subject, heading and extra text are editable in WooCommerce → Settings → Emails → Invoice (PDF).
 *
 * @package Invoice_Packing_Slip_Maker
 *
 * @var WC_Order $order
 * @var string   $email_heading
 * @var string   $additional_content
 * @var bool     $sent_to_admin
 * @var bool     $plain_text
 * @var WC_Email $email
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<p>
	<?php
	if ( $order->get_billing_first_name() ) {
		/* translators: %s: customer first name */
		printf( esc_html__( 'Hi %s,', 'invoice-packing-slip-maker' ), esc_html( $order->get_billing_first_name() ) );
	} else {
		esc_html_e( 'Hi there,', 'invoice-packing-slip-maker' );
	}
	?>
</p>
<p>
	<?php
	/* translators: %s: order number */
	printf( esc_html__( 'Please find attached the invoice for your order #%s. A summary of the order is below.', 'invoice-packing-slip-maker' ), esc_html( $order->get_order_number() ) );
	?>
</p>

<?php
do_action( 'woocommerce_email_order_details', $order, $sent_to_admin, $plain_text, $email );
do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text, $email );
do_action( 'woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text, $email );

if ( $additional_content ) {
	echo wp_kses_post( wpautop( wptexturize( $additional_content ) ) );
}

do_action( 'woocommerce_email_footer', $email );
