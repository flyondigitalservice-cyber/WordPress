<?php
/**
 * Product card in loops.
 *
 * Overrides woocommerce/templates/content-product.php while keeping every
 * standard hook so extensions (wishlists, badges, quick view) keep working.
 *
 * @package FlavorKit
 * @version 9.4.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( ! is_a( $product, WC_Product::class ) || ! $product->is_visible() ) {
	return;
}

$fk_gallery = $product->get_gallery_image_ids();
$fk_hover   = $fk_gallery ? wp_get_attachment_image( $fk_gallery[0], 'woocommerce_thumbnail', false, array( 'class' => 'fk-card-hover-img', 'loading' => 'lazy', 'alt' => '' ) ) : '';
$fk_tone    = absint( $product->get_id() ) % 4;
?>
<li <?php wc_product_class( 'fk-card', $product ); ?>>
	<?php do_action( 'woocommerce_before_shop_loop_item' ); // Opens the product link. ?>
	<span class="fk-card-media fk-tone-<?php echo esc_attr( $fk_tone ); ?><?php echo $fk_hover ? ' has-hover' : ''; ?>">
		<?php
		woocommerce_show_product_loop_sale_flash();
		if ( $product->get_image_id() ) {
			woocommerce_template_loop_product_thumbnail();
		} else {
			$fk_shapes = array( 'can', 'pouch', 'jar', 'bottle', 'box' );
			$fk_words  = explode( ' ', strtoupper( $product->get_name() ) );
			echo flavorkit_pack_svg( $fk_shapes[ $product->get_id() % 5 ], $product->get_id(), $fk_words[0], isset( $fk_words[1] ) ? implode( ' ', array_slice( $fk_words, 1, 2 ) ) : '' ); // phpcs:ignore WordPress.Security.EscapeOutput
		}
		echo $fk_hover; // phpcs:ignore WordPress.Security.EscapeOutput
		?>
	</span>
	<?php
	// Leave sale flash & thumbnail to our markup above.
	remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10 );
	remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10 );
	do_action( 'woocommerce_before_shop_loop_item_title' );
	add_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10 );
	add_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10 );
	?>
	<span class="fk-card-body">
		<?php if ( wc_review_ratings_enabled() && $product->get_review_count() ) : ?>
			<span class="fk-card-rating"><?php echo flavorkit_stars( $product->get_average_rating() ); // phpcs:ignore WordPress.Security.EscapeOutput ?> <span>(<?php echo esc_html( $product->get_review_count() ); ?>)</span></span>
		<?php endif; ?>
		<?php
		do_action( 'woocommerce_shop_loop_item_title' );

		// Rating is printed above; keep price and any extension output.
		remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
		do_action( 'woocommerce_after_shop_loop_item_title' );
		add_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
		?>
	</span>
	<?php do_action( 'woocommerce_after_shop_loop_item' ); // Closes the link + add-to-cart button. ?>
</li>
