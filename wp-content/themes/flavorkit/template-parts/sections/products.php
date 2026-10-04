<?php
/**
 * Bestseller product grid (real WooCommerce products, or demo cards).
 *
 * @package FlavorKit
 */

defined( 'ABSPATH' ) || exit;

$fk_count    = max( 1, min( 24, absint( flavorkit_mod( 'products_count' ) ) ) );
$fk_products = function_exists( 'flavorkit_get_home_products' ) ? flavorkit_get_home_products( flavorkit_mod( 'products_source' ), $fk_count ) : array();
?>
<section class="fk-section fk-products" id="shop">
	<div class="fk-container">
		<?php flavorkit_section_heading( flavorkit_mod( 'products_eyebrow' ), flavorkit_mod( 'products_title' ) ); ?>

		<?php if ( $fk_products ) : ?>
			<div class="woocommerce">
				<ul class="products columns-4">
					<?php
					foreach ( $fk_products as $fk_product ) {
						$GLOBALS['post'] = get_post( $fk_product->get_id() ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride
						setup_postdata( $GLOBALS['post'] );
						wc_get_template_part( 'content', 'product' );
					}
					wp_reset_postdata();
					?>
				</ul>
			</div>
		<?php else : ?>
			<ul class="products fk-demo-products columns-4">
				<?php
				foreach ( array_slice( flavorkit_demo_products(), 0, $fk_count ) as $fk_i => $fk_demo ) {
					get_template_part( 'template-parts/components/demo-card', null, array( 'product' => $fk_demo, 'index' => $fk_i ) );
				}
				?>
			</ul>
		<?php endif; ?>

		<?php if ( flavorkit_mod( 'products_btn_text' ) ) : ?>
			<p class="fk-center fk-reveal"><a class="fk-btn fk-btn-primary fk-btn-lg" href="<?php echo esc_url( flavorkit_shop_url() ); ?>"><?php echo esc_html( flavorkit_mod( 'products_btn_text' ) ); ?> <?php echo flavorkit_icon( 'arrow', 20 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a></p>
		<?php endif; ?>
	</div>
</section>
