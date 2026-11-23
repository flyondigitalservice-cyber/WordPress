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
	array( 'slug' => 'home-kitchen', 'eyebrow' => 'For your kitchen', 'title' => 'The little kitchen fixes you keep meaning to buy', 'tone' => 'blue' ),
	array( 'slug' => 'aureveda-superfoods', 'eyebrow' => 'For your pantry & bath', 'title' => 'Seeds for breakfast, herbal bars for bath time', 'tone' => 'yellow' ),
	array( 'slug' => 'electronics-gadgets', 'eyebrow' => 'For your room', 'title' => 'Turn a plain room into the one everyone hangs out in', 'tone' => 'navy' ),
);
$min_price = function ( $items ) {
	$m = null;
	foreach ( $items as $p ) {
		$v = (float) $p->get_price();
		$m = null === $m ? $v : min( $m, $v );
	}
	return (int) $m;
};
?>

<main class="bs-home">
	<div class="bs-wrap">

		<!-- Category strip -->
		<?php if ( $depts ) : ?>
		<section class="bs-card bs-catstrip">
			<a href="<?php echo esc_url( add_query_arg( 'orderby', 'popularity', $shop_url ) ); ?>" class="bs-catstrip-item">
				<span class="bs-catstrip-img bs-catstrip-deal">%</span>
				<span>Price drops</span>
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
						<p class="bs-hero-line"><?php echo esc_html( baystore_aisle( $s['slug'] )['line'] ); ?></p>
						<p class="bs-hero-off">From <b><?php echo esc_html( baystore_format_inr( $min_price( $by_cat[ $s['slug'] ] ) ) ); ?></b> · pay when it arrives</p>
						<a href="<?php echo esc_url( get_term_link( $term ) ); ?>" class="bs-btn bs-btn-yellow">Take a look</a>
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

		<!-- Hello / brand voice -->
		<section class="bs-card bs-hello">
			<div class="bs-hello-copy">
				<span class="bs-hello-wave" aria-hidden="true">👋</span>
				<h2>Hi, we’re Baystore.</h2>
				<p>We’re a small online shop for the everyday things Indian homes actually use. Not everything under the sun — just useful finds for five corners of your home: the kitchen, the pantry, your self-care shelf, your room, and the festive box in the loft.</p>
				<p>Everything is priced in rupees with the MRP shown right next to it, you can pay when it reaches your door, and if something isn’t right we’ll replace it within 7 days.</p>
			</div>
			<ul class="bs-hello-points">
				<li><b>Pay when it arrives</b><span>Cash or UPI at your door, anywhere in India.</span></li>
				<li><b>Honest prices</b><span>Our price and the MRP side by side. No surprises at checkout.</span></li>
				<li><b>Ask us first</b><span>Not sure it’s right for you? Message us before you order.</span></li>
			</ul>
		</section>

		<!-- Quad cards -->
		<section class="bs-quad-row">
			<?php foreach ( array_slice( $depts, 0, 3 ) as $d ) : ?>
				<div class="bs-card bs-quad">
					<?php $aisle = baystore_aisle( $d->slug ); ?>
					<h3><?php echo esc_html( $aisle ? $aisle['quad'] : html_entity_decode( $d->name ) ); ?></h3>
					<p class="bs-quad-sub"><?php echo esc_html( baystore_short_cat_name( $d->name ) ); ?> · from <?php echo esc_html( baystore_format_inr( $min_price( $by_cat[ $d->slug ] ) ) ); ?></p>
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
					<a href="<?php echo esc_url( get_term_link( $d ) ); ?>" class="bs-link">Browse the aisle →</a>
				</div>
			<?php endforeach; ?>
			<div class="bs-card bs-quad bs-quad-promo">
				<span class="bs-promo-tag">A small welcome</span>
				<h3>Take an extra 10% off</h3>
				<p>Use code <b><?php echo esc_html( $coupon_code ); ?></b> at checkout. Yes, it works with Cash on Delivery too.</p>
				<button type="button" class="bs-coupon-box" data-bs-apply-coupon="<?php echo esc_attr( $coupon_code ); ?>">
					<span><?php echo esc_html( $coupon_code ); ?></span><em>Tap to apply</em>
				</button>
				<ul class="bs-promo-list">
					<li>✓ Free delivery above ₹<?php echo (int) $free_thresh; ?></li>
					<li>✓ Pay cash or UPI when it arrives</li>
					<li>✓ 7-day easy replacement</li>
				</ul>
			</div>
		</section>

		<!-- Deals of the day -->
		<?php if ( $deals ) : ?>
		<section class="bs-card bs-row">
			<div class="bs-row-head">
				<div class="bs-row-title">
					<h2>Biggest price drops</h2>
					<span class="bs-row-sub">Sorted by how much you save against MRP.</span>
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
				<strong>Pay when it arrives.</strong> Free delivery from ₹<?php echo (int) $free_thresh; ?> (₹49 below that). New here? Take 10% off with <strong><?php echo esc_html( $coupon_code ); ?></strong>.
			</div>
			<a href="<?php echo esc_url( $shop_url ); ?>" class="bs-btn bs-btn-blue bs-btn-sm">Start shopping</a>
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
						<?php $aisle = baystore_aisle( $d->slug ); ?>
						<h2><?php echo esc_html( baystore_short_cat_name( $d->name ) ); ?></h2>
						<span class="bs-row-sub"><?php echo esc_html( $aisle ? $aisle['line'] : '' ); ?></span>
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
			<div><b>🚚 Free delivery from ₹<?php echo (int) $free_thresh; ?></b><span>Just ₹49 on smaller orders</span></div>
			<div><b>💵 Pay when it arrives</b><span>Cash or UPI at your door</span></div>
			<div><b>🔄 Not right? We’ll replace it</b><span>Within 7 days, picked up from your door</span></div>
			<div><b>🤝 Ask before you buy</b><span>A real person will get back to you</span></div>
		</section>
	</div>
</main>

<?php
get_footer();
