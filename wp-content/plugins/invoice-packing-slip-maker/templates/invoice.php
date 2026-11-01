<?php
/**
 * Invoice body.
 *
 * Override by copying to yourtheme/invoice-packing-slip-maker/invoice.php.
 *
 * @package Invoice_Packing_Slip_Maker
 *
 * @var IPSM_Document $doc
 * @var array         $shop
 * @var WC_Order      $order
 * @var array         $texts
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="ipsm-header">
	<div class="ipsm-logo">
		<?php if ( $shop['logo_url'] ) : ?>
			<img src="<?php echo esc_url( $shop['logo_url'] ); ?>" alt="<?php echo esc_attr( $shop['name'] ); ?>" />
		<?php else : ?>
			<div class="ipsm-shopname"><?php echo esc_html( $shop['name'] ); ?></div>
		<?php endif; ?>
	</div>
	<div class="ipsm-title ipsm-accent"><?php echo esc_html( $doc->title() ); ?></div>
</div>

<div class="ipsm-cols">
	<div>
		<?php if ( $shop['logo_url'] ) : ?>
			<strong><?php echo esc_html( $shop['name'] ); ?></strong><br />
		<?php endif; ?>
		<span class="ipsm-muted">
			<?php
			foreach ( $shop['address'] as $line ) {
				echo esc_html( $line ) . '<br />';
			}
			echo $shop['phone'] ? esc_html( $shop['phone'] ) . '<br />' : '';
			echo $shop['email'] ? esc_html( $shop['email'] ) . '<br />' : '';
			/* translators: %s: tax number */
			echo $shop['tax_id'] ? esc_html( sprintf( __( 'Tax ID: %s', 'invoice-packing-slip-maker' ), $shop['tax_id'] ) ) : '';
			?>
		</span>
	</div>
	<div>
		<table class="ipsm-meta">
			<tr><th><?php esc_html_e( 'Invoice number:', 'invoice-packing-slip-maker' ); ?></th><td><?php echo esc_html( $doc->invoice_number() ); ?></td></tr>
			<tr><th><?php esc_html_e( 'Invoice date:', 'invoice-packing-slip-maker' ); ?></th><td><?php echo esc_html( $doc->invoice_date() ); ?></td></tr>
			<tr><th><?php esc_html_e( 'Order number:', 'invoice-packing-slip-maker' ); ?></th><td><?php echo esc_html( $order->get_order_number() ); ?></td></tr>
			<tr><th><?php esc_html_e( 'Order date:', 'invoice-packing-slip-maker' ); ?></th><td><?php echo esc_html( $doc->order_date() ); ?></td></tr>
			<?php if ( $order->get_payment_method_title() ) : ?>
				<tr><th><?php esc_html_e( 'Payment method:', 'invoice-packing-slip-maker' ); ?></th><td><?php echo esc_html( $order->get_payment_method_title() ); ?></td></tr>
			<?php endif; ?>
		</table>
	</div>
</div>

<div class="ipsm-cols">
	<div>
		<h3 class="ipsm-h"><?php esc_html_e( 'Bill to', 'invoice-packing-slip-maker' ); ?></h3>
		<?php
		foreach ( $doc->billing_lines() as $line ) {
			echo esc_html( $line ) . '<br />';
		}
		if ( IPSM_Settings::enabled( 'show_email_phone' ) ) {
			echo $order->get_billing_email() ? esc_html( $order->get_billing_email() ) . '<br />' : '';
			echo $order->get_billing_phone() ? esc_html( $order->get_billing_phone() ) : '';
		}
		?>
	</div>
	<?php if ( IPSM_Settings::enabled( 'show_shipping_addr' ) && $doc->has_shipping_address() ) : ?>
		<div>
			<h3 class="ipsm-h"><?php esc_html_e( 'Ship to', 'invoice-packing-slip-maker' ); ?></h3>
			<?php
			foreach ( $doc->shipping_lines() as $line ) {
				echo esc_html( $line ) . '<br />';
			}
			?>
		</div>
	<?php endif; ?>
</div>

<?php do_action( 'ipsm_before_items', $doc ); ?>

<table class="ipsm-items">
	<thead>
		<tr>
			<th><?php esc_html_e( 'Product', 'invoice-packing-slip-maker' ); ?></th>
			<th class="num"><?php esc_html_e( 'Qty', 'invoice-packing-slip-maker' ); ?></th>
			<th class="num"><?php esc_html_e( 'Price', 'invoice-packing-slip-maker' ); ?></th>
			<th class="num"><?php esc_html_e( 'Total', 'invoice-packing-slip-maker' ); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ( $doc->items() as $item ) : ?>
			<tr>
				<td>
					<div class="ipsm-item-name"><?php echo esc_html( $item['name'] ); ?></div>
					<?php if ( $item['sku'] && IPSM_Settings::enabled( 'show_sku' ) ) : ?>
						<div class="ipsm-item-meta"><?php /* translators: %s: SKU */ echo esc_html( sprintf( __( 'SKU: %s', 'invoice-packing-slip-maker' ), $item['sku'] ) ); ?></div>
					<?php endif; ?>
					<?php foreach ( $item['meta'] as $meta_line ) : ?>
						<div class="ipsm-item-meta"><?php echo esc_html( $meta_line ); ?></div>
					<?php endforeach; ?>
				</td>
				<td class="num"><?php echo esc_html( wc_stock_amount( $item['qty'] ) ); ?></td>
				<td class="num"><?php echo wp_kses_post( $doc->price( $item['unit_price'] ) ); ?></td>
				<td class="num"><?php echo wp_kses_post( $doc->price( $item['total'] ) ); ?></td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>

<?php
$ipsm_totals = $doc->totals();
$ipsm_last   = array_key_last( $ipsm_totals );
?>
<table class="ipsm-totals">
	<?php foreach ( $ipsm_totals as $ipsm_key => $row ) : ?>
		<tr class="<?php echo $ipsm_key === $ipsm_last ? 'ipsm-grand' : ''; ?>">
			<th><?php echo esc_html( $row['label'] ); ?></th>
			<td><?php echo wp_kses_post( $row['value'] ); ?></td>
		</tr>
	<?php endforeach; ?>
</table>

<?php do_action( 'ipsm_after_totals', $doc ); ?>

<?php if ( $order->get_customer_note() || $texts['notes'] ) : ?>
	<div class="ipsm-notes">
		<?php if ( $order->get_customer_note() ) : ?>
			<h3 class="ipsm-h"><?php esc_html_e( 'Customer note', 'invoice-packing-slip-maker' ); ?></h3>
			<p><?php echo esc_html( $order->get_customer_note() ); ?></p>
		<?php endif; ?>
		<?php if ( $texts['notes'] ) : ?>
			<h3 class="ipsm-h"><?php esc_html_e( 'Notes', 'invoice-packing-slip-maker' ); ?></h3>
			<p><?php echo esc_html( $texts['notes'] ); ?></p>
		<?php endif; ?>
	</div>
<?php endif; ?>

<?php if ( $texts['footer'] ) : ?>
	<div class="ipsm-footer"><?php echo esc_html( $texts['footer'] ); ?></div>
<?php endif; ?>
