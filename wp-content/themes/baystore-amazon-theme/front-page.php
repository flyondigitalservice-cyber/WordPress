<?php
/**
 * Baystore — Homepage Template
 *
 * @package Baystore_Amazon
 */

get_header();

$shop_url    = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );
$coupon_code = get_theme_mod( 'baystore_coupon_code', 'BAY10' );
$free_thresh = (int) get_theme_mod( 'baystore_free_shipping_threshold', 499 );
$hero_title  = get_theme_mod( 'baystore_hero_headline', 'Everyday essentials, thoughtfully picked.' );
$hero_sub    = get_theme_mod( 'baystore_hero_sub', 'Kitchen helpers, natural superfoods, self-care and small joys for the home — fairly priced and delivered across India.' );
$depts       = baystore_get_departments();
$all         = baystore_products( '', 60 );
$use_wc      = ! empty( $all );

$cat_blurbs = array(
	'home-kitchen'           => 'Boards, organisers, storage',
	'aureveda-superfoods'    => 'Seeds, grains, bath bars',
	'personal-care-wellness' => 'Herbal care & grooming',
	'electronics-gadgets'    => 'Projectors, audio, lights',
	'festive-handmade-decor' => 'Artisan pieces & gifting',
);

// Hero mosaic: one image from up to four departments.
$mosaic = array();
foreach ( $depts as $d ) {
	$img = baystore_cat_image( $d );
	if ( $img ) {
		$mosaic[] = array( 'img' => $img, 'term' => $d );
	}
	if ( count( $mosaic ) >= 4 ) {
		break;
	}
}
?>

<main class="bs-home">

	<!-- Category bubbles -->
	<?php if ( $depts ) : ?>
	<section class="bs-bubbles">
		<a href="<?php echo esc_url( $shop_url ); ?>" class="bs-bubble">
			<span class="bs-bubble-ring"><span class="bs-bubble-img bs-bubble-new">New</span></span>
			<span class="bs-bubble-label">New in</span>
		</a>
		<?php foreach ( $depts as $d ) : ?>
			<a href="<?php echo esc_url( get_term_link( $d ) ); ?>" class="bs-bubble">
				<span class="bs-bubble-ring"><span class="bs-bubble-img"><img src="<?php echo esc_url( baystore_cat_image( $d ) ); ?>" alt="" loading="lazy" /></span></span>
				<span class="bs-bubble-label"><?php echo esc_html( baystore_short_cat_name( $d->name ) ); ?></span>
			</a>
		<?php endforeach; ?>
	</section>
	<?php endif; ?>

	<!-- Hero -->
	<section class="bs-hero">
		<div class="bs-hero-copy">
			<div class="bs-eyebrow">Baystore · Shipped across India</div>
			<h1 class="bs-hero-h1"><?php echo esc_html( $hero_title ); ?></h1>
			<p class="bs-hero-lead"><?php echo esc_html( $hero_sub ); ?></p>
			<div class="bs-hero-cta">
				<a href="<?php echo esc_url( $shop_url ); ?>" class="bs-btn bs-btn-primary">Shop all</a>
				<a href="<?php echo esc_url( add_query_arg( 'orderby', 'popularity', $shop_url ) ); ?>" class="bs-btn bs-btn-ghost">Best sellers</a>
			</div>
			<ul class="bs-hero-points">
				<li>Free delivery above ₹<?php echo (int) $free_thresh; ?></li>
				<li>Cash on delivery &amp; UPI</li>
				<li>7-day easy replacement</li>
			</ul>
		</div>
		<div class="bs-hero-mosaic">
			<?php foreach ( $mosaic as $i => $m ) : ?>
				<a href="<?php echo esc_url( get_term_link( $m['term'] ) ); ?>" class="bs-mosaic-tile bs-tone-<?php echo (int) $i; ?>">
					<img src="<?php echo esc_url( $m['img'] ); ?>" alt="" />
					<span><?php echo esc_html( baystore_short_cat_name( $m['term']->name ) ); ?> →</span>
				</a>
			<?php endforeach; ?>
		</div>
	</section>

	<!-- Promise tiles -->
	<section class="bs-wrap">
		<div class="bs-tiles">
			<div class="bs-tile bs-tone-0"><strong>Free delivery</strong><span>on orders above ₹<?php echo (int) $free_thresh; ?></span></div>
			<div class="bs-tile bs-tone-1"><strong>Cash on delivery</strong><span>pay by cash or UPI at your door</span></div>
			<div class="bs-tile bs-tone-2"><strong>7-day replacement</strong><span>easy doorstep pickup</span></div>
			<button type="button" class="bs-tile bs-tone-3" data-bs-apply-coupon="<?php echo esc_attr( $coupon_code ); ?>"><strong>Extra 10% off</strong><span>tap to apply code <?php echo esc_html( $coupon_code ); ?></span></button>
		</div>
	</section>

	<!-- Intro -->
	<section class="bs-wrap bs-intro">
		<h2 class="bs-title-xl">Useful things for every home</h2>
		<span class="bs-rule"></span>
		<p>From stainless steel chopping boards and fridge organisers to raw chia seeds, herbal bath bars and galaxy projectors — a small, honest catalogue of things people actually use, priced in rupees and delivered to your door.</p>
	</section>

	<!-- Shop by category -->
	<?php if ( $depts ) : ?>
	<section class="bs-wrap bs-section">
		<div class="bs-section-head">
			<h2 class="bs-title">Shop by category</h2>
			<span class="bs-rule"></span>
			<p>Five aisles, one easy checkout.</p>
		</div>
		<div class="bs-cat-grid">
			<?php foreach ( $depts as $d ) : ?>
				<a href="<?php echo esc_url( get_term_link( $d ) ); ?>" class="bs-cat-card">
					<span class="bs-cat-img"><img src="<?php echo esc_url( baystore_cat_image( $d ) ); ?>" alt="" loading="lazy" /></span>
					<strong><?php echo esc_html( baystore_short_cat_name( $d->name ) ); ?></strong>
					<em><?php echo esc_html( isset( $cat_blurbs[ $d->slug ] ) ? $cat_blurbs[ $d->slug ] : $d->count . ' products' ); ?></em>
				</a>
			<?php endforeach; ?>
		</div>
	</section>
	<?php endif; ?>

	<!-- The Edit (tabbed) -->
	<?php if ( $use_wc ) : ?>
	<section class="bs-wrap bs-section">
		<div class="bs-section-head">
			<h2 class="bs-title">The Edit</h2>
			<span class="bs-rule"></span>
			<p>Customer favourites, by aisle.</p>
		</div>
		<div class="bs-tabs" role="tablist">
			<button type="button" class="bs-tab is-active" data-bs-tab="all">Popular</button>
			<?php foreach ( $depts as $d ) : ?>
				<button type="button" class="bs-tab" data-bs-tab="<?php echo esc_attr( $d->slug ); ?>"><?php echo esc_html( baystore_short_cat_name( $d->name ) ); ?></button>
			<?php endforeach; ?>
		</div>

		<div class="bs-tab-panel is-active" data-bs-panel="all">
			<div class="bs-pgrid">
				<?php
				foreach ( baystore_products( '', 8, array( 'orderby' => 'rand' ) ) as $prod ) {
					get_template_part( 'template-parts/product-card', null, array( 'wc_product' => $prod ) );
				}
				?>
			</div>
		</div>
		<?php foreach ( $depts as $d ) : ?>
			<div class="bs-tab-panel" data-bs-panel="<?php echo esc_attr( $d->slug ); ?>">
				<div class="bs-pgrid">
					<?php
					foreach ( baystore_products( $d->slug, 8 ) as $prod ) {
						get_template_part( 'template-parts/product-card', null, array( 'wc_product' => $prod ) );
					}
					?>
				</div>
			</div>
		<?php endforeach; ?>
		<div class="bs-center"><a href="<?php echo esc_url( $shop_url ); ?>" class="bs-btn bs-btn-ghost">View all products</a></div>
	</section>
	<?php endif; ?>

	<!-- Offer band -->
	<section class="bs-wrap">
		<div class="bs-offer-band">
			<div>
				<div class="bs-eyebrow" style="color:#FCE3D8;">This week</div>
				<h2>Take an extra 10% off your first order</h2>
				<p>Use code <strong><?php echo esc_html( $coupon_code ); ?></strong> at checkout. Works with cash on delivery.</p>
			</div>
			<button type="button" class="bs-btn bs-btn-light" data-bs-apply-coupon="<?php echo esc_attr( $coupon_code ); ?>">Apply <?php echo esc_html( $coupon_code ); ?></button>
		</div>
	</section>

	<!-- Department shelves -->
	<?php
	if ( $use_wc ) :
		foreach ( array_slice( $depts, 0, 3 ) as $d ) :
			$items = baystore_products( $d->slug, 4, array( 'orderby' => 'date', 'order' => 'DESC' ) );
			if ( ! $items ) {
				continue;
			}
			?>
			<section class="bs-wrap bs-section">
				<div class="bs-shelf-row">
					<div>
						<h2 class="bs-title-left"><?php echo esc_html( html_entity_decode( $d->name ) ); ?></h2>
						<p class="bs-muted"><?php echo esc_html( isset( $cat_blurbs[ $d->slug ] ) ? $cat_blurbs[ $d->slug ] : '' ); ?></p>
					</div>
					<a href="<?php echo esc_url( get_term_link( $d ) ); ?>" class="bs-link-arrow">Shop <?php echo esc_html( baystore_short_cat_name( $d->name ) ); ?> →</a>
				</div>
				<div class="bs-pgrid">
					<?php
					foreach ( $items as $prod ) {
						get_template_part( 'template-parts/product-card', null, array( 'wc_product' => $prod ) );
					}
					?>
				</div>
			</section>
			<?php
		endforeach;
	endif;
	?>

	<!-- Why Baystore -->
	<section class="bs-wrap bs-section">
		<div class="bs-section-head">
			<h2 class="bs-title">Why shop with us</h2>
			<span class="bs-rule"></span>
		</div>
		<div class="bs-tiles bs-tiles-why">
			<div class="bs-tile bs-tone-1"><strong>Hand-picked range</strong><span>Every product checked before it is listed</span></div>
			<div class="bs-tile bs-tone-2"><strong>Priced in rupees</strong><span>Clear MRP and real discounts</span></div>
			<div class="bs-tile bs-tone-0"><strong>Pan-India shipping</strong><span>Metro cities to small towns</span></div>
			<div class="bs-tile bs-tone-3"><strong>Real people to help</strong><span>Ask us before you buy</span></div>
		</div>
	</section>
</main>

<?php
get_footer();
