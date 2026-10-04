<?php
/**
 * Demo product card shown before real products exist.
 *
 * @package FlavorKit
 *
 * @var array $args { product: array, index: int }
 */

defined( 'ABSPATH' ) || exit;

$fk_p     = $args['product'];
$fk_index = isset( $args['index'] ) ? (int) $args['index'] : 0;
$fk_url   = flavorkit_shop_url();
?>
<li class="product fk-card fk-reveal" style="--fk-delay:<?php echo esc_attr( ( $fk_index % 4 ) * 80 ); ?>ms">
	<a class="fk-card-media fk-tone-<?php echo esc_attr( $fk_index % 4 ); ?>" href="<?php echo esc_url( $fk_url ); ?>" tabindex="-1" aria-hidden="true">
		<?php if ( $fk_p['badge'] ) : ?>
			<span class="fk-badge"><?php echo esc_html( $fk_p['badge'] ); ?></span>
		<?php endif; ?>
		<?php echo flavorkit_pack_svg( $fk_p['shape'], $fk_p['palette'], $fk_p['label'], $fk_p['sub'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
	</a>
	<div class="fk-card-body">
		<p class="fk-card-rating"><?php echo flavorkit_stars( $fk_p['rating'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?> <span>(<?php echo esc_html( number_format_i18n( $fk_p['reviews'] ) ); ?>)</span></p>
		<h3 class="woocommerce-loop-product__title"><a href="<?php echo esc_url( $fk_url ); ?>"><?php echo esc_html( $fk_p['name'] ); ?></a></h3>
		<span class="price">
			<?php if ( $fk_p['regular'] ) : ?>
				<del><?php echo flavorkit_demo_price( $fk_p['regular'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></del>
			<?php endif; ?>
			<ins><?php echo flavorkit_demo_price( $fk_p['price'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></ins>
		</span>
		<a class="button fk-btn fk-btn-primary fk-btn-block" href="<?php echo esc_url( $fk_url ); ?>"><?php esc_html_e( 'Add to cart', 'flavorkit' ); ?></a>
	</div>
</li>
