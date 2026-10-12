<?php
/**
 * Product card (shop loop, carousels, related).
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package Dastaan
 * @version 9.4.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( ! is_a( $product, WC_Product::class ) || ! $product->is_visible() ) {
	return;
}

$dastaan_link      = apply_filters( 'woocommerce_loop_product_link', get_the_permalink(), $product );
$dastaan_gallery   = $product->get_gallery_image_ids();
$dastaan_hover_id  = ( dastaan_option( 'shop_hover_image' ) && ! empty( $dastaan_gallery ) ) ? (int) $dastaan_gallery[0] : 0;
$dastaan_has_video = function_exists( 'dastaan_product_has_video' ) && dastaan_product_has_video( $product );
$dastaan_quick     = dastaan_option( 'shop_quick_add' ) && $product->is_purchasable() && $product->is_in_stock();
$dastaan_cats      = wc_get_product_terms( $product->get_id(), 'product_cat', array( 'orderby' => 'parent', 'order' => 'DESC' ) );
?>
<li <?php wc_product_class( 'product-card', $product ); ?>>
	<div class="product-card__media">
		<a href="<?php echo esc_url( $dastaan_link ); ?>" class="product-card__link" aria-label="<?php echo esc_attr( $product->get_name() ); ?>">
			<?php
			echo $product->get_image( 'dastaan-card', array( 'class' => 'product-card__img product-card__img--main', 'loading' => 'lazy', 'sizes' => '(min-width: 1024px) 25vw, 50vw' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			if ( $dastaan_hover_id ) {
				echo wp_get_attachment_image( $dastaan_hover_id, 'dastaan-card', false, array( 'class' => 'product-card__img product-card__img--hover', 'loading' => 'lazy', 'aria-hidden' => 'true' ) );
			}
			?>
		</a>
		<?php echo dastaan_product_badges( $product ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<?php if ( $dastaan_has_video ) : ?>
			<span class="product-card__video" title="<?php esc_attr_e( 'Video available', 'dastaan' ); ?>"><?php dastaan_the_icon( 'play', array( 'size' => 12 ) ); ?></span>
		<?php endif; ?>
		<div class="product-card__actions">
			<?php dastaan_wishlist_button( $product->get_id() ); ?>
		</div>
		<?php if ( $dastaan_quick ) : ?>
			<div class="product-card__quick">
				<?php
				if ( $product->is_type( 'simple' ) ) {
					woocommerce_template_loop_add_to_cart( array( 'class' => 'btn btn--light btn--block add_to_cart_button ajax_add_to_cart' ) );
				} else {
					echo '<a class="btn btn--light btn--block" href="' . esc_url( $dastaan_link ) . '">' . esc_html__( 'Choose options', 'dastaan' ) . '</a>';
				}
				?>
			</div>
		<?php endif; ?>
	</div>
	<div class="product-card__body">
		<?php if ( $dastaan_cats ) : ?>
			<p class="product-card__cat"><?php echo esc_html( $dastaan_cats[0]->name ); ?></p>
		<?php endif; ?>
		<h2 class="product-card__title woocommerce-loop-product__title"><a href="<?php echo esc_url( $dastaan_link ); ?>"><?php echo esc_html( $product->get_name() ); ?></a></h2>
		<div class="product-card__price"><?php echo $product->get_price_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
		<?php
		if ( wc_review_ratings_enabled() && $product->get_rating_count() ) {
			echo '<div class="product-card__rating">';
			dastaan_stars( $product->get_average_rating(), 12 );
			echo '<span class="rating-count">(' . esc_html( $product->get_rating_count() ) . ')</span></div>';
		}
		?>
	</div>
</li>
