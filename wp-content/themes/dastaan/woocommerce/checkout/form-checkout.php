<?php
/**
 * Checkout form: 2-column layout, step indicators, sticky order summary.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package Dastaan
 * @version 9.4.0
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_checkout_form', $checkout );

if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
	echo esc_html( apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'woocommerce' ) ) );
	return;
}
?>
<form name="checkout" method="post" class="checkout woocommerce-checkout d-checkout" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data" aria-label="<?php echo esc_attr__( 'Checkout', 'woocommerce' ); ?>">
	<div class="d-checkout__main">
		<ol class="checkout-steps" aria-label="<?php esc_attr_e( 'Checkout progress', 'dastaan' ); ?>">
			<li class="is-done"><span>1</span><?php esc_html_e( 'Bag', 'dastaan' ); ?></li>
			<li class="is-active"><span>2</span><?php esc_html_e( 'Details', 'dastaan' ); ?></li>
			<li><span>3</span><?php esc_html_e( 'Payment', 'dastaan' ); ?></li>
		</ol>

		<?php if ( $checkout->get_checkout_fields() ) : ?>
			<?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>
			<div class="col2-set d-checkout__details" id="customer_details">
				<div class="col-1">
					<?php do_action( 'woocommerce_checkout_billing' ); ?>
				</div>
				<div class="col-2">
					<?php do_action( 'woocommerce_checkout_shipping' ); ?>
				</div>
			</div>
			<?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>
		<?php endif; ?>

		<?php do_action( 'woocommerce_checkout_before_order_review_heading' ); ?>
		<h3 id="order_review_heading" class="d-checkout__summary-heading"><?php esc_html_e( 'Your order', 'woocommerce' ); ?></h3>
		<?php do_action( 'woocommerce_checkout_before_order_review' ); ?>
	</div>

	<aside class="d-checkout__aside">
		<div class="d-checkout__summary">
			<button type="button" class="d-checkout__summary-toggle js-summary-toggle" aria-expanded="false" aria-controls="order_review">
				<span><?php esc_html_e( 'Order summary', 'dastaan' ); ?></span>
				<strong class="d-checkout__summary-total"><?php wc_cart_totals_order_total_html(); ?></strong>
				<?php dastaan_the_icon( 'chevron-right', array( 'size' => 16, 'class' => 'd-checkout__summary-caret' ) ); ?>
			</button>
			<div id="order_review" class="woocommerce-checkout-review-order">
				<?php do_action( 'woocommerce_checkout_order_review' ); ?>
			</div>
		</div>
		<?php do_action( 'woocommerce_checkout_after_order_review' ); ?>
	</aside>
</form>
<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>
