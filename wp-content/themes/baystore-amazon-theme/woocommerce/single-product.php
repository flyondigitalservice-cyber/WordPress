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
	$bought      = get_post_meta( $pid, '_bs_bought_month', true );
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
	$coupon_price = round( $price * 0.9 );
	?>

	<div class="bs-wrap">
		<div class="bs-crumbs"><?php woocommerce_breadcrumb(); ?></div>

		<div class="bs-card bs-pdp">
			<div class="bs-pdp-left">
				<div class="bs-pdp-gallery">
					<?php if ( count( $gallery ) > 1 ) : ?>
						<div class="bs-pdp-thumbs">
							<?php foreach ( $gallery as $i => $g ) : ?>
								<button type="button" class="<?php echo 0 === $i ? 'is-active' : ''; ?>" onclick="document.getElementById('bs-pdp-main-img').src='<?php echo esc_url( $g ); ?>';this.parentNode.querySelectorAll('button').forEach(function(b){b.classList.remove('is-active')});this.classList.add('is-active');"><img src="<?php echo esc_url( $g ); ?>" alt="" /></button>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
					<div class="bs-pdp-main">
						<?php if ( $gallery ) : ?>
							<img id="bs-pdp-main-img" src="<?php echo esc_url( $gallery[0] ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" />
						<?php endif; ?>
					</div>
				</div>
				<div class="bs-pdp-actions">
					<button type="button" class="bs-btn bs-btn-yellow bs-btn-lg" data-bs-add-product="<?php echo (int) $pid; ?>" data-bs-qty="1">Add to cart</button>
					<a href="<?php echo esc_url( $buy_now_url ); ?>" class="bs-btn bs-btn-blue bs-btn-lg">Buy now</a>
				</div>
			</div>

			<div class="bs-pdp-info">
				<?php if ( $cat ) : ?>
					<a class="bs-pdp-cat" href="<?php echo esc_url( get_term_link( $cat ) ); ?>">From our <?php echo esc_html( baystore_short_cat_name( $cat->name ) ); ?> aisle →</a>
				<?php endif; ?>
				<h1 class="bs-pdp-title"><?php the_title(); ?></h1>
				<div class="bs-pcard-rating bs-pdp-rating">
					<span class="bs-stars" style="--r:<?php echo esc_attr( (float) $rating ); ?>"></span>
					<span class="bs-rating-num"><?php echo esc_html( $rating ); ?></span>
					<span class="bs-rating-count"><?php echo esc_html( number_format_i18n( (int) $rev_count ) ); ?> ratings &amp; reviews</span>
					<?php if ( $bought ) : ?>
						<span class="bs-bought"><?php echo esc_html( $bought ); ?> bought in past month</span>
					<?php endif; ?>
				</div>

				<?php if ( $discount > 0 ) : ?>
					<div class="bs-special"><span>Bay price</span> Lower than MRP</div>
				<?php endif; ?>
				<div class="bs-pdp-price">
					<span class="bs-price-now"><?php echo esc_html( baystore_format_inr( $price ) ); ?></span>
					<?php if ( $mrp > $price ) : ?>
						<span class="bs-price-mrp"><?php echo esc_html( baystore_format_inr( $mrp ) ); ?></span>
						<span class="bs-off-chip"><?php echo (int) $discount; ?>% OFF</span>
					<?php endif; ?>
				</div>
				<?php if ( $mrp > $price ) : ?>
					<div class="bs-pdp-save">You save <?php echo esc_html( baystore_format_inr( $mrp - $price ) ); ?></div>
				<?php endif; ?>
				<div class="bs-pdp-tax">Inclusive of all taxes</div>

				<div class="bs-offers">
					<h3>Available offers</h3>
					<ul>
						<li><span class="bs-offer-ico">🏷️</span><span><b>Coupon:</b> Extra 10% off with <b><?php echo esc_html( $coupon_code ); ?></b> — pay just <?php echo esc_html( baystore_format_inr( $coupon_price ) ); ?>. <button type="button" class="bs-link-btn" data-bs-apply-coupon="<?php echo esc_attr( $coupon_code ); ?>">Apply</button></span></li>
						<li><span class="bs-offer-ico">🚚</span><span><b>Free delivery</b> on orders above ₹<?php echo (int) $free_thresh; ?> (₹49 below that)</span></li>
						<li><span class="bs-offer-ico">💵</span><span><b>Cash on Delivery</b> — pay by cash or UPI when it arrives</span></li>
					</ul>
				</div>

				<div class="bs-pdp-meta">
					<div class="bs-meta-label">Delivery</div>
					<div>
						<button type="button" class="bs-pin-box" data-bs-open-pincode>
							<span>📍 <span data-bs-loc-text>Mumbai 400001</span></span>
							<b>Change</b>
						</button>
						<div class="bs-eta">Delivery by <b data-bs-eta-text>Tomorrow</b> · <?php echo $price >= $free_thresh ? '<span class="bs-green">Free</span>' : '₹49'; ?></div>
					</div>

					<div class="bs-meta-label">Services</div>
					<ul class="bs-services">
						<li>🔄 7 days replacement</li>
						<li>💵 Cash on Delivery available</li>
						<li>🤝 Questions? Ask us before you order</li>
					</ul>

					<div class="bs-meta-label">Seller</div>
					<div><b class="bs-blue">Baystore Retail</b> <span class="bs-rating-num">★ 4.6</span></div>
				</div>

				<div class="bs-pdp-desc">
					<h2>About this product</h2>
					<?php echo wp_kses_post( $product->get_short_description() ?: $product->get_description() ); ?>
				</div>
			</div>
		</div>

		<?php
		$related = $cat ? baystore_products( $cat->slug, 13, array( 'exclude' => array( $pid ) ) ) : array();
		if ( $related ) :
			?>
			<section class="bs-card bs-row">
				<div class="bs-row-head">
					<div class="bs-row-title"><h2>More from this aisle</h2></div>
					<a href="<?php echo esc_url( get_term_link( $cat ) ); ?>" class="bs-btn bs-btn-blue bs-btn-sm">View all</a>
				</div>
				<div class="bs-hscroll">
					<?php
					foreach ( array_slice( $related, 0, 12 ) as $rp ) {
						get_template_part( 'template-parts/product-card', null, array( 'wc_product' => $rp ) );
					}
					?>
				</div>
			</section>
		<?php endif; ?>

		<?php if ( comments_open() || get_comments_number() ) : ?>
			<div class="bs-card bs-pdp-reviews"><?php comments_template(); ?></div>
		<?php endif; ?>
	</div>
	<?php
endwhile;

get_footer();
