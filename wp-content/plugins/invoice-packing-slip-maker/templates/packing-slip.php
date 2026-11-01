<?php
/**
 * Packing slip body.
 *
 * Override by copying to yourtheme/invoice-packing-slip-maker/packing-slip.php.
 *
 * @package Invoice_Packing_Slip_Maker
 *
 * @var IPSM_Document $doc
 * @var array         $shop
 * @var WC_Order      $order
 * @var array         $texts
 */

defined( 'ABSPATH' ) || exit;

$ipsm_prices = IPSM_Settings::enabled( 'slip_show_prices' );
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
			echo $shop['phone'] ? esc_html( $shop['phone'] ) : '';
			?>
		</span>
	</div>
	<div>
		<table class="ipsm-meta">
			<tr><th><?php esc_html_e( 'Order number:', 'invoice-packing-slip-maker' ); ?></th><td><?php echo esc_html( $order->get_order_number() ); ?></td></tr>
			<tr><th><?php esc_html_e( 'Order date:', 'invoice-packing-slip-maker' ); ?></th><td><?php echo esc_html( $doc->order_date() ); ?></td></tr>
			<?php if ( $order->get_shipping_method() ) : ?>
				<tr><th><?php esc_html_e( 'Shipping method:', 'invoice-packing-slip-maker' ); ?></th><td><?php echo esc_html( $order->get_shipping_method() ); ?></td></tr>
			<?php endif; ?>
		</table>
	</div>
</div>

<div class="ipsm-cols">
	<div class="ipsm-ship-big">
		<h3 class="ipsm-h"><?php esc_html_e( 'Ship to', 'invoice-packing-slip-maker' ); ?></h3>
		<?php
		foreach ( $doc->shipping_lines() as $i => $line ) {
			echo 0 === $i ? '<strong>' . esc_html( $line ) . '</strong><br />' : esc_html( $line ) . '<br />';
		}
		echo $doc->shipping_phone() ? esc_html( $doc->shipping_phone() ) : '';
		?>
	</div>
	<div>
		<h3 class="ipsm-h"><?php esc_html_e( 'Bill to', 'invoice-packing-slip-maker' ); ?></h3>
		<?php
		foreach ( $doc->billing_lines() as $line ) {
			echo esc_html( $line ) . '<br />';
		}
		?>
	</div>
</div>

<table class="ipsm-items">
	<thead>
		<tr>
			<th style="width:28px"></th>
			<th><?php esc_html_e( 'Product', 'invoice-packing-slip-maker' ); ?></th>
			<th class="num"><?php esc_html_e( 'Qty', 'invoice-packing-slip-maker' ); ?></th>
			<?php if ( $ipsm_prices ) : ?>
				<th class="num"><?php esc_html_e( 'Price', 'invoice-packing-slip-maker' ); ?></th>
				<th class="num"><?php esc_html_e( 'Total', 'invoice-packing-slip-maker' ); ?></th>
			<?php else : ?>
				<th class="num"><?php esc_html_e( 'Weight', 'invoice-packing-slip-maker' ); ?></th>
			<?php endif; ?>
		</tr>
	</thead>
	<tbody>
		<?php foreach ( $doc->items() as $item ) : ?>
			<tr>
				<td><span class="ipsm-check"></span></td>
				<td>
					<div class="ipsm-item-name"><?php echo esc_html( $item['name'] ); ?></div>
					<?php if ( $item['sku'] && IPSM_Settings::enabled( 'show_sku' ) ) : ?>
						<div class="ipsm-item-meta"><?php /* translators: %s: SKU */ echo esc_html( sprintf( __( 'SKU: %s', 'invoice-packing-slip-maker' ), $item['sku'] ) ); ?></div>
					<?php endif; ?>
					<?php foreach ( $item['meta'] as $meta_line ) : ?>
						<div class="ipsm-item-meta"><?php echo esc_html( $meta_line ); ?></div>
					<?php endforeach; ?>
				</td>
				<td class="num ipsm-qty-big"><?php echo esc_html( wc_stock_amount( $item['qty'] ) ); ?></td>
				<?php if ( $ipsm_prices ) : ?>
					<td class="num"><?php echo wp_kses_post( $doc->price( $item['unit_price'] ) ); ?></td>
					<td class="num"><?php echo wp_kses_post( $doc->price( $item['total'] ) ); ?></td>
				<?php else : ?>
					<td class="num"><?php echo $item['weight'] ? esc_html( wp_strip_all_tags( wc_format_weight( $item['weight'] ) ) ) : '—'; ?></td>
				<?php endif; ?>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>

<table class="ipsm-totals">
	<tr><th><?php esc_html_e( 'Total items', 'invoice-packing-slip-maker' ); ?></th><td><?php echo esc_html( wc_stock_amount( $doc->total_qty() ) ); ?></td></tr>
	<?php if ( $doc->total_weight() ) : ?>
		<tr><th><?php esc_html_e( 'Total weight', 'invoice-packing-slip-maker' ); ?></th><td><?php echo esc_html( wp_strip_all_tags( $doc->total_weight() ) ); ?></td></tr>
	<?php endif; ?>
</table>

<?php if ( $order->get_customer_note() ) : ?>
	<div class="ipsm-notes">
		<h3 class="ipsm-h"><?php esc_html_e( 'Customer note', 'invoice-packing-slip-maker' ); ?></h3>
		<p><?php echo esc_html( $order->get_customer_note() ); ?></p>
	</div>
<?php endif; ?>

<?php if ( $texts['footer'] ) : ?>
	<div class="ipsm-footer"><?php echo esc_html( $texts['footer'] ); ?></div>
<?php endif; ?>
