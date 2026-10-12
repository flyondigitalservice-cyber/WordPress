<?php
/**
 * Single product content.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package Dastaan
 * @version 3.6.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

do_action( 'woocommerce_before_single_product' );

if ( post_password_required() ) {
	echo get_the_password_form(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	return;
}
?>
<div id="product-<?php the_ID(); ?>" <?php wc_product_class( 'product-layout', $product ); ?>>
	<div class="product-layout__gallery">
		<?php do_action( 'woocommerce_before_single_product_summary' ); ?>
	</div>
	<div class="product-layout__summary">
		<div class="summary entry-summary">
			<?php do_action( 'woocommerce_single_product_summary' ); ?>
		</div>
	</div>
	<div class="product-layout__after">
		<?php do_action( 'woocommerce_after_single_product_summary' ); ?>
	</div>
</div>

<?php if ( dastaan_option( 'product_sticky_atc' ) && $product->is_purchasable() && $product->is_in_stock() ) : ?>
	<div class="sticky-atc" aria-hidden="true">
		<div class="sticky-atc__inner">
			<?php echo $product->get_image( 'thumbnail', array( 'class' => 'sticky-atc__img' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<div class="sticky-atc__info">
				<span class="sticky-atc__name"><?php echo esc_html( $product->get_name() ); ?></span>
				<span class="sticky-atc__price"><?php echo $product->get_price_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
			</div>
			<button type="button" class="btn btn--primary sticky-atc__btn js-sticky-atc"><?php echo $product->is_type( 'simple' ) ? esc_html__( 'Add to bag', 'dastaan' ) : esc_html__( 'Choose options', 'dastaan' ); ?></button>
		</div>
	</div>
<?php endif; ?>

<?php do_action( 'woocommerce_after_single_product' ); ?>
