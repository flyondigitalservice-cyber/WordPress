<?php
/**
 * Baystore Amazon Lean — Reusable Product Card Partial
 * Supports both WooCommerce WC_Product ($args['wc_product']) and seed items ($args['seed_item']).
 *
 * @package Baystore_Amazon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$wc_product = isset( $args['wc_product'] ) ? $args['wc_product'] : null;
$seed_item  = isset( $args['seed_item'] ) ? $args['seed_item'] : null;

if ( $wc_product && is_a( $wc_product, 'WC_Product' ) ) {
	$pid        = $wc_product->get_id();
	$title      = $wc_product->get_name();
	$permalink  = get_permalink( $pid );
	$price      = (float) $wc_product->get_price();
	$mrp        = (float) ( $wc_product->get_regular_price() ?: $price * 1.35 );
	$discount   = $mrp > $price ? round( ( $mrp - $price ) * 100 / $mrp ) : 0;
	$img_url    = get_the_post_thumbnail_url( $pid, 'woocommerce_thumbnail' ) ?: get_post_meta( $pid, '_bs_ext_image', true );
	$rating     = get_post_meta( $pid, '_bs_rating', true ) ?: '4.5';
	$rev_count  = get_post_meta( $pid, '_bs_reviews_count', true ) ?: '320';
	$badge      = get_post_meta( $pid, '_bs_badge', true ) ?: ( $discount >= 35 ? 'Limited Time Deal' : 'Best Seller' );
	$buy_url    = add_query_arg( 'baystore_buy_now', $pid, home_url( '/' ) );
	$is_wc      = true;
} elseif ( is_array( $seed_item ) ) {
	$pid       = (int) $seed_item['id'];
	$title     = $seed_item['shortTitle'];
	$permalink = ! empty( $seed_item['sourceUrl'] ) ? $seed_item['sourceUrl'] : '#';
	$price     = (float) $seed_item['price'];
	$mrp       = (float) $seed_item['mrp'];
	$discount  = (int) $seed_item['discountPct'];
	$img_url   = ! empty( $seed_item['images'][0] ) ? $seed_item['images'][0] : '';
	$rating    = $seed_item['rating'];
	$rev_count = $seed_item['reviewsCount'];
	$badge     = ! empty( $seed_item['badge'] ) ? $seed_item['badge'] : 'Best Seller';
	$buy_url   = $permalink;
	$is_wc     = false;
} else {
	return;
}

$is_new  = $is_wc && ( time() - get_post_time( 'U', true, $pid ) ) < 30 * DAY_IN_SECONDS;
$tag     = $discount >= 20 ? '-' . (int) $discount . '%' : ( 'Best Seller' === $badge ? 'Best seller' : '' );
$cat_lbl = '';
if ( $is_wc ) {
	$terms = get_the_terms( $pid, 'product_cat' );
	if ( $terms && ! is_wp_error( $terms ) ) {
		$cat_lbl = baystore_short_cat_name( $terms[0]->name );
	}
}
?>

<article class="bs-pcard">
	<a href="<?php echo esc_url( $permalink ); ?>" class="bs-pcard-img">
		<?php if ( $img_url ) : ?>
			<img src="<?php echo esc_url( $img_url ); ?>" alt="<?php echo esc_attr( $title ); ?>" loading="lazy" />
		<?php endif; ?>
		<?php if ( $tag ) : ?>
			<span class="bs-pcard-tag"><?php echo esc_html( $tag ); ?></span>
		<?php endif; ?>
	</a>

	<div class="bs-pcard-body">
		<?php if ( $cat_lbl ) : ?>
			<div class="bs-pcard-cat"><?php echo esc_html( $cat_lbl ); ?></div>
		<?php endif; ?>
		<a href="<?php echo esc_url( $permalink ); ?>" class="bs-pcard-title"><?php echo esc_html( $title ); ?></a>
		<div class="bs-pcard-rating"><span>★ <?php echo esc_html( $rating ); ?></span> · <?php echo esc_html( $rev_count ); ?> reviews</div>
		<div class="bs-pcard-price">
			<span class="bs-price-now"><?php echo esc_html( baystore_format_inr( $price ) ); ?></span>
			<?php if ( $mrp > $price ) : ?>
				<span class="bs-price-mrp"><?php echo esc_html( baystore_format_inr( $mrp ) ); ?></span>
			<?php endif; ?>
		</div>
	</div>

	<div class="bs-pcard-actions">
		<?php if ( $is_wc ) : ?>
			<button type="button" class="bs-btn bs-btn-primary bs-btn-block" data-bs-add-product="<?php echo (int) $pid; ?>" data-bs-qty="1">Add to cart</button>
		<?php else : ?>
			<a href="<?php echo esc_url( $permalink ); ?>" class="bs-btn bs-btn-primary bs-btn-block">View product</a>
		<?php endif; ?>
	</div>
</article>
