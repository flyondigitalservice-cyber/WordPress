<?php
/**
 * Template Name: Heritage
 *
 * @package HadhramiFashion
 */

get_header();
$hf_founder = hf_opt( 'founder' );
?>
<header class="page-hero page-hero--image">
	<img src="<?php echo esc_url( hf_brand_image( 'legacy', 2200 ) ); ?>" alt="" referrerpolicy="no-referrer">
	<div class="wrap">
		<span class="eyebrow"><?php esc_html_e( 'Our Heritage', 'hadhramifashion' ); ?></span>
		<h1><?php esc_html_e( 'A thread that was never cut.', 'hadhramifashion' ); ?></h1>
		<p class="lede">
			<?php
			printf(
				/* translators: %s: patron */
				esc_html__( 'The story of a Hadhrami family of tailors, told today by %s.', 'hadhramifashion' ),
				esc_html( $hf_founder )
			);
			?>
		</p>
	</div>
</header>

<section class="section">
	<div class="wrap split">
		<div class="split__media reveal">
			<img src="<?php echo esc_url( hf_brand_image( 'atelier', 1400 ) ); ?>" alt="" loading="lazy" referrerpolicy="no-referrer">
		</div>
		<div class="reveal">
			<span class="eyebrow"><?php esc_html_e( 'Where we come from', 'hadhramifashion' ); ?></span>
			<h2><?php esc_html_e( 'Merchants of the monsoon.', 'hadhramifashion' ); ?></h2>
			<p class="lede"><?php esc_html_e( 'For centuries the people of Hadhramaut followed the trade winds — to the Swahili coast, to the Malay world, to the Deccan. They carried scholarship, spices and skill, and they settled into cities that still remember them.', 'hadhramifashion' ); ?></p>
			<p><?php esc_html_e( 'Our family carried a pair of shears. What began as a small counter where neighbours brought cloth for Eid became the house that dressed their sons for their weddings, and then their grandsons for their first interviews.', 'hadhramifashion' ); ?></p>
			<blockquote class="patron">
				<?php echo esc_html( hf_copy( 'legacy_quote' ) ); ?>
				<cite><?php echo esc_html( $hf_founder ); ?></cite>
			</blockquote>
		</div>
	</div>
</section>

<section class="section section--ivory">
	<div class="wrap">
		<div class="section-head reveal">
			<div>
				<span class="eyebrow"><?php esc_html_e( 'Three things we never changed', 'hadhramifashion' ); ?></span>
				<h2><?php esc_html_e( 'The house rules.', 'hadhramifashion' ); ?></h2>
			</div>
		</div>
		<div class="eras">
			<div class="era reveal">
				<small><?php esc_html_e( 'I', 'hadhramifashion' ); ?></small>
				<h3><?php esc_html_e( 'Your pattern is yours', 'hadhramifashion' ); ?></h3>
				<p><?php esc_html_e( 'We draft a paper pattern for every client and keep it. The second suit is faster than the first; the tenth fits better than the second.', 'hadhramifashion' ); ?></p>
			</div>
			<div class="era reveal">
				<small><?php esc_html_e( 'II', 'hadhramifashion' ); ?></small>
				<h3><?php esc_html_e( 'Speed is a form of respect', 'hadhramifashion' ); ?></h3>
				<p><?php esc_html_e( 'A wedding moves, a flight is early, a shirt is stained an hour before the meeting. We work in shifts so your day does not wait on our queue.', 'hadhramifashion' ); ?></p>
			</div>
			<div class="era reveal">
				<small><?php esc_html_e( 'III', 'hadhramifashion' ); ?></small>
				<h3><?php esc_html_e( 'Nothing leaves unpressed', 'hadhramifashion' ); ?></h3>
				<p><?php esc_html_e( 'Every seam pressed open, every button sewn with a shank, every piece checked by the hand that cut it.', 'hadhramifashion' ); ?></p>
			</div>
		</div>
	</div>
</section>

<section class="section cta-band">
	<div class="wrap reveal">
		<span class="eyebrow eyebrow--plain"><?php esc_html_e( 'Visit the atelier', 'hadhramifashion' ); ?></span>
		<h2><?php echo esc_html( hf_delivery_opt( 'store_address' ) ); ?></h2>
		<p class="lede"><?php esc_html_e( 'Tea is always on. Bring the jacket you love most — we will tell you why it fits.', 'hadhramifashion' ); ?></p>
		<div class="cta-band__actions">
			<a class="btn btn--light" target="_blank" rel="noopener" href="<?php echo esc_url( 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode( hf_delivery_opt( 'store_lat' ) . ',' . hf_delivery_opt( 'store_lng' ) ) ); ?>"><?php esc_html_e( 'Get directions', 'hadhramifashion' ); ?></a>
		</div>
	</div>
</section>
<?php
get_footer();
