<?php
/**
 * Review order: items with thumbnails, coupon toggle, totals.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package Dastaan
 * @version 11.2.0
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="woocommerce-checkout-review-order-table checkout-review">
<ul class="checkout-items">
	<?php
	do_action( 'woocommerce_review_order_before_cart_contents' );

	foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
		$_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
		$visible  = apply_filters( 'woocommerce_checkout_cart_item_visible', true, $cart_item, $cart_item_key );

		if ( $_product instanceof WC_Product && $_product->exists() && $cart_item['quantity'] > 0 && $visible ) {
			// WC_Cart::get_item_product_name() only exists in recent WooCommerce releases; fall back to the product name.
			$cart_item_name = method_exists( WC()->cart, 'get_item_product_name' ) ? WC()->cart->get_item_product_name( $cart_item, $_product ) : $_product->get_name();
			$product_name   = apply_filters( 'woocommerce_cart_item_name', $cart_item_name, $cart_item, $cart_item_key );
			?>
			<li class="checkout-item <?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">
				<div class="checkout-item__thumb">
					<?php echo apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image( 'thumbnail' ), $cart_item, $cart_item_key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<span class="checkout-item__qty"><?php echo (int) $cart_item['quantity']; ?></span>
				</div>
				<div class="checkout-item__info product-name">
					<span class="checkout-item__name"><?php echo wp_kses_post( $product_name ); ?></span>
					<?php echo apply_filters( 'woocommerce_checkout_cart_item_quantity', '<span class="product-quantity screen-reader-text">' . sprintf( '&times;&nbsp;%s', $cart_item['quantity'] ) . '</span>', $cart_item, $cart_item_key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<?php echo wc_get_formatted_cart_item_data( $cart_item, false, $cart_item_name ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>
				<div class="checkout-item__total product-total">
					<?php echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>
			</li>
			<?php
		}
	}

	do_action( 'woocommerce_review_order_after_cart_contents' );
	?>
</ul>

<?php if ( wc_coupons_enabled() ) : ?>
	<div class="checkout-coupon">
		<button type="button" class="checkout-coupon__toggle js-coupon-toggle" aria-expanded="false" aria-controls="checkout-coupon-fields"><?php dastaan_the_icon( 'gift', array( 'size' => 16 ) ); ?><span><?php esc_html_e( 'Have a gift or coupon code?', 'dastaan' ); ?></span></button>
		<div id="checkout-coupon-fields" class="checkout-coupon__fields" hidden>
			<label for="d_coupon_code" class="screen-reader-text"><?php esc_html_e( 'Coupon code', 'dastaan' ); ?></label>
			<input type="text" id="d_coupon_code" class="input-text" placeholder="<?php esc_attr_e( 'Enter code', 'dastaan' ); ?>" autocomplete="off">
			<button type="button" class="btn btn--outline btn--small js-apply-coupon" data-nonce="<?php echo esc_attr( wp_create_nonce( 'apply-coupon' ) ); ?>"><?php esc_html_e( 'Apply', 'dastaan' ); ?></button>
		</div>
	</div>
<?php endif; ?>

<table class="shop_table checkout-totals">
	<tbody>
		<tr class="cart-subtotal">
			<th><?php esc_html_e( 'Subtotal', 'woocommerce' ); ?></th>
			<td><?php wc_cart_totals_subtotal_html(); ?></td>
		</tr>

		<?php foreach ( WC()->cart->get_coupons() as $code => $coupon ) : ?>
			<tr class="cart-discount coupon-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
				<th><?php wc_cart_totals_coupon_label( $coupon ); ?></th>
				<td><?php wc_cart_totals_coupon_html( $coupon ); ?></td>
			</tr>
		<?php endforeach; ?>

		<?php if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) : ?>
			<?php do_action( 'woocommerce_review_order_before_shipping' ); ?>
			<?php wc_cart_totals_shipping_html(); ?>
			<?php do_action( 'woocommerce_review_order_after_shipping' ); ?>
		<?php endif; ?>

		<?php foreach ( WC()->cart->get_fees() as $fee ) : ?>
			<tr class="fee">
				<th><?php echo esc_html( $fee->name ); ?></th>
				<td><?php wc_cart_totals_fee_html( $fee ); ?></td>
			</tr>
		<?php endforeach; ?>

		<?php if ( wc_tax_enabled() && ! WC()->cart->display_prices_including_tax() ) : ?>
			<?php if ( 'itemized' === get_option( 'woocommerce_tax_total_display' ) ) : ?>
				<?php foreach ( WC()->cart->get_tax_totals() as $code => $tax ) : // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited ?>
					<tr class="tax-rate tax-rate-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
						<th><?php echo esc_html( $tax->label ); ?></th>
						<td><?php echo wp_kses_post( $tax->formatted_amount ); ?></td>
					</tr>
				<?php endforeach; ?>
			<?php else : ?>
				<tr class="tax-total">
					<th><?php echo esc_html( WC()->countries->tax_or_vat() ); ?></th>
					<td><?php wc_cart_totals_taxes_total_html(); ?></td>
				</tr>
			<?php endif; ?>
		<?php endif; ?>

		<?php do_action( 'woocommerce_review_order_before_order_total' ); ?>
		<tr class="order-total">
			<th><?php esc_html_e( 'Total', 'woocommerce' ); ?></th>
			<td><?php wc_cart_totals_order_total_html(); ?></td>
		</tr>
		<?php do_action( 'woocommerce_review_order_after_order_total' ); ?>
	</tbody>
</table>
</div>
