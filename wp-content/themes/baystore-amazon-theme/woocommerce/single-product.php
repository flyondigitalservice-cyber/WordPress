<?php
/**
 * Baystore — Product Detail Page
 *
 * @package Baystore_Amazon
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	global $product;

	$pid         = $product->get_id();
	$price       = (float) $product->get_price();
	$mrp         = (float) ( $product->get_regular_price() ?: $price * 1.35 );
	$discount    = $mrp > $price ? round( ( $mrp - $price ) * 100 / $mrp ) : 0;
	$rating      = get_post_meta( $pid, '_bs_rating', true ) ?: '4.6';
	$rev_count   = get_post_meta( $pid, '_bs_reviews_count', true ) ?: '428';
	$coupon_code = get_theme_mod( 'baystore_coupon_code', 'BAY10' );
	$free_thresh = (int) get_theme_mod( 'baystore_free_shipping_threshold', 499 );
	$buy_now_url = add_query_arg( 'baystore_buy_now', $pid, home_url( '/' ) );
	$terms       = get_the_terms( $pid, 'product_cat' );
	$cat         = ( $terms && ! is_wp_error( $terms ) ) ? $terms[0] : null;

	$gallery = array();
	if ( has_post_thumbnail() ) {
		$gallery[] = get_the_post_thumbnail_url( $pid, 'large' );
	}
	foreach ( $product->get_gallery_image_ids() as $gid ) {
		$gallery[] = wp_get_attachment_image_url( $gid, 'large' );
	}
	if ( ! $gallery && get_post_meta( $pid, '_bs_ext_image', true ) ) {
		$gallery[] = get_post_meta( $pid, '_bs_ext_image', true );
	}
	?>

	<div class="bs-crumbs"><div class="bs-wrap"><?php woocommerce_breadcrumb(); ?></div></div>

	<div class="bs-wrap bs-pdp">
		<div class="bs-pdp-gallery">
			<div class="bs-pdp-main">
				<?php if ( $gallery ) : ?>
					<img id="bs-pdp-main-img" src="<?php echo esc_url( $gallery[0] ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" />
				<?php endif; ?>
				<?php if ( $discount >= 5 ) : ?>
					<span class="bs-pcard-tag">-<?php echo (int) $discount; ?>%</span>
				<?php endif; ?>
			</div>
			<?php if ( count( $gallery ) > 1 ) : ?>
				<div class="bs-pdp-thumbs">
					<?php foreach ( $gallery as $g ) : ?>
						<button type="button" onclick="document.getElementById('bs-pdp-main-img').src='<?php echo esc_url( $g ); ?>'"><img src="<?php echo esc_url( $g ); ?>" alt="" /></button>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>

		<div class="bs-pdp-info">
			<?php if ( $cat ) : ?>
				<a class="bs-eyebrow" href="<?php echo esc_url( get_term_link( $cat ) ); ?>"><?php echo esc_html( html_entity_decode( $cat->name ) ); ?></a>
			<?php endif; ?>
			<h1 class="bs-pdp-title"><?php the_title(); ?></h1>
			<div class="bs-pcard-rating"><span>★ <?php echo esc_html( $rating ); ?></span> · <?php echo esc_html( $rev_count ); ?> reviews</div>

			<div class="bs-pdp-price">
				<span class="bs-price-now"><?php echo esc_html( baystore_format_inr( $price ) ); ?></span>
				<?php if ( $mrp > $price ) : ?>
					<span class="bs-price-mrp">MRP <?php echo esc_html( baystore_format_inr( $mrp ) ); ?></span>
					<span class="bs-save-pill">Save <?php echo esc_html( baystore_format_inr( $mrp - $price ) ); ?></span>
				<?php endif; ?>
			</div>
			<div class="bs-muted bs-small">Inclusive of all taxes · <?php echo $product->is_in_stock() ? 'In stock' : 'Check availability'; ?></div>

			<div class="bs-pdp-actions">
				<button type="button" class="bs-btn bs-btn-primary" data-bs-add-product="<?php echo (int) $pid; ?>" data-bs-qty="1">Add to cart</button>
				<a href="<?php echo esc_url( $buy_now_url ); ?>" class="bs-btn bs-btn-accent">Buy now</a>
			</div>

			<button type="button" class="bs-pdp-coupon" data-bs-apply-coupon="<?php echo esc_attr( $coupon_code ); ?>">
				<span>Extra 10% off with code <strong><?php echo esc_html( $coupon_code ); ?></strong></span>
				<span class="bs-coupon-apply">Apply</span>
			</button>

			<div class="bs-pdp-delivery" data-bs-open-pincode>
				<span>Delivery by <strong data-bs-eta-text>Tomorrow</strong> · <span data-bs-loc-text>Delivering to Mumbai 400001</span></span>
				<span class="bs-coupon-apply">Change</span>
			</div>

			<div class="bs-tiles bs-tiles-sm">
				<div class="bs-tile bs-tone-0"><strong>Free delivery</strong><span>above ₹<?php echo (int) $free_thresh; ?></span></div>
				<div class="bs-tile bs-tone-1"><strong>Cash on delivery</strong><span>cash or UPI</span></div>
				<div class="bs-tile bs-tone-2"><strong>7-day replacement</strong><span>doorstep pickup</span></div>
			</div>

			<div class="bs-pdp-desc">
				<h2>About this product</h2>
				<?php echo wp_kses_post( $product->get_short_description() ?: $product->get_description() ); ?>
			</div>
		</div>
	</div>

	<?php
	$related = $cat ? baystore_products( $cat->slug, 5, array( 'exclude' => array( $pid ) ) ) : array();
	if ( $related ) :
		?>
		<section class="bs-wrap bs-section">
			<div class="bs-section-head">
				<h2 class="bs-title">You may also like</h2>
				<span class="bs-rule"></span>
			</div>
			<div class="bs-pgrid">
				<?php
				foreach ( array_slice( $related, 0, 4 ) as $rp ) {
					get_template_part( 'template-parts/product-card', null, array( 'wc_product' => $rp ) );
				}
				?>
			</div>
		</section>
	<?php endif; ?>

	<div class="bs-wrap bs-pdp-reviews">
		<?php
		if ( comments_open() || get_comments_number() ) {
			comments_template();
		}
		?>
	</div>
	<?php
endwhile;

get_footer();
