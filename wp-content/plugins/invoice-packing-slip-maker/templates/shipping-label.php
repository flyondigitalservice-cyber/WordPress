<?php
/**
 * Shipping label body — print it on a label sheet / thermal printer and stick it on the parcel.
 *
 * Override by copying to yourtheme/invoice-packing-slip-maker/shipping-label.php.
 *
 * @package Invoice_Packing_Slip_Maker
 *
 * @var IPSM_Document $doc
 * @var array         $shop
 * @var WC_Order      $order
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="ipsm-label">
	<section class="from">
		<div class="lbl"><?php esc_html_e( 'From', 'invoice-packing-slip-maker' ); ?></div>
		<strong><?php echo esc_html( $shop['name'] ); ?></strong><br />
		<?php
		foreach ( $shop['address'] as $line ) {
			echo esc_html( $line ) . '<br />';
		}
		echo $shop['phone'] ? esc_html( $shop['phone'] ) : '';
		?>
	</section>
	<section class="to">
		<div class="lbl"><?php esc_html_e( 'Ship to', 'invoice-packing-slip-maker' ); ?></div>
		<?php
		foreach ( $doc->shipping_lines() as $i => $line ) {
			echo 0 === $i ? '<div class="name">' . esc_html( $line ) . '</div>' : esc_html( $line ) . '<br />';
		}
		?>
		<?php if ( $doc->shipping_phone() ) : ?>
			<div class="phone"><?php /* translators: %s: phone */ echo esc_html( sprintf( __( 'Phone: %s', 'invoice-packing-slip-maker' ), $doc->shipping_phone() ) ); ?></div>
		<?php endif; ?>
	</section>
	<?php $ipsm_code = IPSM_Barcode::clean( apply_filters( 'ipsm_barcode_value', $order->get_order_number(), $doc ) ); ?>
	<?php if ( '' !== $ipsm_code ) : ?>
		<section class="barcode">
			<?php echo IPSM_Barcode::svg( $ipsm_code, 56 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- generated SVG, only numbers and an escaped label. ?>
			<div class="code"><?php echo esc_html( $ipsm_code ); ?></div>
		</section>
	<?php endif; ?>
	<section class="bottom">
		<div>
			<div class="lbl"><?php esc_html_e( 'Order', 'invoice-packing-slip-maker' ); ?></div>
			<div class="order-no">#<?php echo esc_html( $order->get_order_number() ); ?></div>
		</div>
		<div class="info">
			<?php echo $order->get_shipping_method() ? esc_html( $order->get_shipping_method() ) . '<br />' : ''; ?>
			<?php /* translators: %s: item count */ echo esc_html( sprintf( __( 'Items: %s', 'invoice-packing-slip-maker' ), wc_stock_amount( $doc->total_qty() ) ) ); ?><br />
			<?php if ( $doc->total_weight() ) : ?>
				<?php /* translators: %s: weight */ echo esc_html( sprintf( __( 'Weight: %s', 'invoice-packing-slip-maker' ), wp_strip_all_tags( $doc->total_weight() ) ) ); ?><br />
			<?php endif; ?>
			<?php echo esc_html( $doc->order_date() ); ?>
		</div>
	</section>
</div>
