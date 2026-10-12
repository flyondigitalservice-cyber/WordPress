<?php
/**
 * Product details accordion (replaces WooCommerce tabs).
 *
 * @package Dastaan
 */

defined( 'ABSPATH' ) || exit;

global $product;

$dastaan_description = apply_filters( 'the_content', $product->get_description() );
$dastaan_care        = dastaan_option( 'product_care_text' );
$dastaan_shipping    = dastaan_option( 'product_shipping_text' );
$dastaan_attrs       = $product->has_attributes() || ( $product->get_weight() || $product->has_dimensions() );

$dastaan_items = array();
if ( trim( wp_strip_all_tags( $dastaan_description ) ) ) {
	$dastaan_items['description'] = array( __( 'The story & details', 'dastaan' ), $dastaan_description );
}
if ( $dastaan_attrs ) {
	ob_start();
	wc_display_product_attributes( $product );
	$dastaan_items['attributes'] = array( __( 'Set contents & measurements', 'dastaan' ), ob_get_clean() );
}
if ( $dastaan_care ) {
	$dastaan_items['care'] = array( __( 'Fabric & care', 'dastaan' ), wpautop( esc_html( $dastaan_care ) ) );
}
if ( $dastaan_shipping ) {
	$dastaan_items['shipping'] = array( __( 'Shipping & exchange', 'dastaan' ), wpautop( esc_html( $dastaan_shipping ) ) );
}
if ( comments_open() ) {
	ob_start();
	comments_template();
	$dastaan_reviews = ob_get_clean();
	/* translators: %d: review count */
	$dastaan_items['reviews'] = array( sprintf( __( 'Reviews (%d)', 'dastaan' ), $product->get_review_count() ), $dastaan_reviews );
}
$dastaan_items = apply_filters( 'dastaan_product_accordion', $dastaan_items, $product );
if ( ! $dastaan_items ) {
	return;
}
?>
<section class="product-accordion" aria-label="<?php esc_attr_e( 'Product information', 'dastaan' ); ?>">
	<div class="accordion">
		<?php $dastaan_first = true; ?>
		<?php foreach ( $dastaan_items as $dastaan_key => $dastaan_item ) : ?>
			<details class="accordion__item" id="tab-<?php echo esc_attr( $dastaan_key ); ?>" <?php echo $dastaan_first ? 'open' : ''; ?>>
				<summary class="accordion__summary"><h2 class="accordion__title"><?php echo esc_html( $dastaan_item[0] ); ?></h2><?php dastaan_the_icon( 'plus', array( 'size' => 18, 'class' => 'accordion__icon' ) ); ?></summary>
				<div class="accordion__body entry-content"><?php echo $dastaan_item[1]; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- content filtered/escaped above. ?></div>
			</details>
			<?php $dastaan_first = false; ?>
		<?php endforeach; ?>
	</div>
</section>
