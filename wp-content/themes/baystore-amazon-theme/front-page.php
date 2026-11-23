<?php
/**
 * Baystore — Homepage (blue & yellow marketplace layout)
 *
 * @package Baystore_Amazon
 */

get_header();

$shop_url    = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );
$coupon_code = get_theme_mod( 'baystore_coupon_code', 'BAY10' );
$free_thresh = (int) get_theme_mod( 'baystore_free_shipping_threshold', 499 );
$depts       = baystore_get_departments();
$all         = baystore_products( '', 100 );

$disc = function ( $p ) {
	$reg  = (float) $p->get_regular_price();
	$sale = (float) $p->get_price();
	return $reg > $sale && $reg > 0 ? ( $reg - $sale ) / $reg : 0;
};
$deals = $all;
usort( $deals, function ( $a, $b ) use ( $disc ) {
	return $disc( $b ) <=> $disc( $a );
} );

$by_cat = array();
foreach ( $depts as $d ) {
	$by_cat[ $d->slug ] = baystore_products( $d->slug, 30 );
}
$img = function ( $p ) {
	return get_the_post_thumbnail_url( $p->get_id(), 'woocommerce_thumbnail' ) ?: get_post_meta( $p->get_id(), '_bs_ext_image', true );
};
$max_off = function ( $items ) use ( $disc ) {
	$m = 0;
	foreach ( $items as $p ) {
		$m = max( $m, $disc( $p ) );
	}
	return (int) round( $m * 100 );
};

$slides = array(
	array( 'slug' => 'home-kitchen', 'eyebrow' => 'Home & Kitchen Fest', 'title' => 'Smart kitchen & storage essentials', 'tone' => 'blue' ),
	array( 'slug' => 'aureveda-superfoods', 'eyebrow' => 'Healthy Living Days', 'title' => 'Superfoods, seeds & herbal bath bars', 'tone' => 'yellow' ),
	array( 'slug' => 'electronics-gadgets', 'eyebrow' => 'Gadget Bonanza', 'title' => 'Projectors, speakers & smart lights', 'tone' => 'navy' ),
);
?>

<main class="bs-home">
	<div class="bs-wrap">

		<!-- Category strip -->
		<?php if ( $depts ) : ?>
		<section class="bs-card bs-catstrip">
			<a href="<?php echo esc_url( add_query_arg( 'orderby', 'popularity', $shop_url ) ); ?>" class="bs-catstrip-item">
				<span class="bs-catstrip-img bs-catstrip-deal">%</span>
				<span>Top Offers</span>
			</a>
			<?php foreach ( $depts as $d ) : ?>
				<a href="<?php echo esc_url( get_term_link( $d ) ); ?>" class="bs-catstrip-item">
					<span class="bs-catstrip-img"><img src="<?php echo esc_url( baystore_cat_image( $d ) ); ?>" alt="" /></span>
					<span><?php echo esc_html( baystore_short_cat_name( $d->name ) ); ?></span>
				</a>
			<?php endforeach; ?>
			<a href="<?php echo esc_url( add_query_arg( 'orderby', 'date', $shop_url ) ); ?>" class="bs-catstrip-item">
				<span class="bs-catstrip-img bs-catstrip-new">NEW</span>
				<span>New Arrivals</span>
			</a>
		</section>
		<?php endif; ?>

		<!-- Hero carousel -->
		<section class="bs-hero-carousel">
			<?php
			$first = true;
			foreach ( $slides as $s ) :
				$term  = get_term_by( 'slug', $s['slug'], 'product_cat' );
				$items = isset( $by_cat[ $s['slug'] ] ) ? array_slice( $by_cat[ $s['slug'] ], 0, 3 ) : array();
				if ( ! $term ) {
					continue;
				}
				?>
				<div class="bs-hero-slide bs-hero-<?php echo esc_attr( $s['tone'] ); ?> <?php echo $first ? 'is-active' : ''; ?>">
					<div class="bs-hero-copy">
						<span class="bs-hero-eyebrow"><?php echo esc_html( $s['eyebrow'] ); ?></span>
						<h2 class="bs-hero-title"><?php echo esc_html( $s['title'] ); ?></h2>
						<p class="bs-hero-off">Up to <b><?php echo (int) $max_off( $by_cat[ $s['slug'] ] ); ?>% off</b> + extra 10% with <?php echo esc_html( $coupon_code ); ?></p>
						<a href="<?php echo esc_url( get_term_link( $term ) ); ?>" class="bs-btn bs-btn-yellow">Shop now</a>
					</div>
					<div class="bs-hero-products">
						<?php foreach ( $items as $p ) : ?>
							<a href="<?php echo esc_url( get_permalink( $p->get_id() ) ); ?>" class="bs-hero-prod">
								<img src="<?php echo esc_url( $img( $p ) ); ?>" alt="" />
								<span>From <?php echo esc_html( baystore_format_inr( $p->get_price() ) ); ?></span>
							</a>
						<?php endforeach; ?>
					</div>
				</div>
				<?php
				$first = false;
			endforeach;
			?>
			<button type="button" id="bs-hero-prev" class="bs-hero-arrow bs-hero-prev" aria-label="Previous slide">‹</button>
			<button type="button" id="bs-hero-next" class="bs-hero-arrow bs-hero-next" aria-label="Next slide">›</button>
		</section>

		<!-- Quad cards -->
		<section class="bs-quad-row">
			<?php foreach ( array_slice( $depts, 0, 3 ) as $d ) : ?>
				<div class="bs-card bs-quad">
					<h3><?php echo esc_html( html_entity_decode( $d->name ) ); ?> | Up to <?php echo (int) $max_off( $by_cat[ $d->slug ] ); ?>% off</h3>
					<div class="bs-quad-grid">
						<?php
						$seen = array();
						$pick = array();
						foreach ( $by_cat[ $d->slug ] as $cand ) {
							$key = strtolower( wp_trim_words( $cand->get_name(), 3, '' ) );
							if ( isset( $seen[ $key ] ) ) {
								continue;
							}
							$seen[ $key ] = true;
							$pick[]       = $cand;
						}
						$pick = array_slice( array_merge( $pick, $by_cat[ $d->slug ] ), 0, 4 );
						?>
						<?php foreach ( array_unique( $pick, SORT_REGULAR ) as $p ) : ?>
							<a href="<?php echo esc_url( get_permalink( $p->get_id() ) ); ?>">
								<span class="bs-quad-img"><img src="<?php echo esc_url( $img( $p ) ); ?>" alt="" loading="lazy" /></span>
								<span class="bs-quad-name"><?php echo esc_html( wp_trim_words( $p->get_name(), 4, '…' ) ); ?></span>
							</a>
						<?php endforeach; ?>
					</div>
					<a href="<?php echo esc_url( get_term_link( $d ) ); ?>" class="bs-link">See all offers</a>
				</div>
			<?php endforeach; ?>
			<div class="bs-card bs-quad bs-quad-promo">
				<span class="bs-promo-tag">Limited period</span>
				<h3>Extra 10% off on your order</h3>
				<p>Use code <b><?php echo esc_html( $coupon_code ); ?></b>. Works with Cash on Delivery.</p>
				<button type="button" class="bs-coupon-box" data-bs-apply-coupon="<?php echo esc_attr( $coupon_code ); ?>">
					<span><?php echo esc_html( $coupon_code ); ?></span><em>Tap to apply</em>
				</button>
				<ul class="bs-promo-list">
					<li>✓ Free delivery above ₹<?php echo (int) $free_thresh; ?></li>
					<li>✓ Cash on Delivery available</li>
					<li>✓ 7-day easy replacement</li>
				</ul>
			</div>
		</section>

		<!-- Deals of the day -->
		<?php if ( $deals ) : ?>
		<section class="bs-card bs-row">
			<div class="bs-row-head">
				<div class="bs-row-title">
					<h2>Bay Deals of the Day</h2>
					<span class="bs-timer">⏱ <span id="bs-countdown-timer">Ends in 04:18:42</span></span>
				</div>
				<a href="<?php echo esc_url( add_query_arg( 'orderby', 'popularity', $shop_url ) ); ?>" class="bs-btn bs-btn-blue bs-btn-sm">View all</a>
			</div>
			<div class="bs-hscroll">
				<?php
				foreach ( array_slice( $deals, 0, 12 ) as $p ) {
					get_template_part( 'template-parts/product-card', null, array( 'wc_product' => $p ) );
				}
				?>
			</div>
		</section>
		<?php endif; ?>

		<!-- Offer banner -->
		<section class="bs-offer-banner">
			<div>
				<strong>Pay on Delivery</strong> across India &nbsp;·&nbsp; <strong>Free delivery</strong> on orders above ₹<?php echo (int) $free_thresh; ?> &nbsp;·&nbsp; Extra <strong>10% off</strong> with <?php echo esc_html( $coupon_code ); ?>
			</div>
			<a href="<?php echo esc_url( $shop_url ); ?>" class="bs-btn bs-btn-blue bs-btn-sm">Shop now</a>
		</section>

		<!-- Category rows -->
		<?php foreach ( $depts as $i => $d ) : ?>
			<?php
			if ( empty( $by_cat[ $d->slug ] ) ) {
				continue;
			}
			?>
			<section class="bs-card bs-row">
				<div class="bs-row-head">
					<div class="bs-row-title">
						<h2>Top picks in <?php echo esc_html( html_entity_decode( $d->name ) ); ?></h2>
						<span class="bs-row-sub"><?php echo (int) $d->count; ?> products · up to <?php echo (int) $max_off( $by_cat[ $d->slug ] ); ?>% off</span>
					</div>
					<a href="<?php echo esc_url( get_term_link( $d ) ); ?>" class="bs-btn bs-btn-blue bs-btn-sm">View all</a>
				</div>
				<div class="bs-hscroll">
					<?php
					foreach ( $by_cat[ $d->slug ] as $p ) {
						get_template_part( 'template-parts/product-card', null, array( 'wc_product' => $p ) );
					}
					?>
				</div>
			</section>
		<?php endforeach; ?>

		<!-- Trust strip -->
		<section class="bs-card bs-trust">
			<div><b>🚚 Free delivery</b><span>On orders above ₹<?php echo (int) $free_thresh; ?></span></div>
			<div><b>💵 Cash on Delivery</b><span>Pay by cash or UPI at your door</span></div>
			<div><b>🔄 7-day replacement</b><span>Easy doorstep pickup</span></div>
			<div><b>🛡️ Genuine products</b><span>Checked before dispatch</span></div>
		</section>
	</div>
</main>

<?php
get_footer();
