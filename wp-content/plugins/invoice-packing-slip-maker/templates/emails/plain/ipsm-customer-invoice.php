<?php
/**
 * Invoice email (plain text).
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

echo "=================================================\n";
echo esc_html( wp_strip_all_tags( $email_heading ) ) . "\n";
echo "=================================================\n\n";

echo $order->get_billing_first_name()
	/* translators: %s: customer first name */
	? sprintf( esc_html__( 'Hi %s,', 'invoice-packing-slip-maker' ), esc_html( $order->get_billing_first_name() ) ) . "\n\n"
	: esc_html__( 'Hi there,', 'invoice-packing-slip-maker' ) . "\n\n";
/* translators: %s: order number */
echo sprintf( esc_html__( 'Please find attached the invoice for your order #%s. A summary of the order is below.', 'invoice-packing-slip-maker' ), esc_html( $order->get_order_number() ) ) . "\n\n";

do_action( 'woocommerce_email_order_details', $order, $sent_to_admin, $plain_text, $email );

echo "\n----------------------------------------\n\n";

do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text, $email );
do_action( 'woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text, $email );

if ( $additional_content ) {
	echo esc_html( wp_strip_all_tags( wptexturize( $additional_content ) ) );
	echo "\n\n----------------------------------------\n\n";
}

echo wp_kses_post( apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) ) );
