<?php
/**
 * Template Name: Delivery Promise
 *
 * @package HadhramiFashion
 */

get_header();
?>
<header class="page-hero">
	<div class="wrap">
		<span class="eyebrow"><?php esc_html_e( 'The Hadhrami Hours', 'hadhramifashion' ); ?></span>
		<h1><?php esc_html_e( 'Two hours for a shirt. Twenty-four for a suit.', 'hadhramifashion' ); ?></h1>
		<p class="lede"><?php esc_html_e( 'Not a warehouse, not a courier network — our own riders leaving our own atelier, carrying clothes that were pressed minutes before.', 'hadhramifashion' ); ?></p>
	</div>
</header>

<section class="section section--tight">
	<div class="wrap">
		<?php get_template_part( 'template-parts/zone-checker' ); ?>
	</div>
</section>

<section class="section section--ivory">
	<div class="wrap">
		<div class="eras">
			<div class="era">
				<small><?php esc_html_e( 'Within 2 hours', 'hadhramifashion' ); ?></small>
				<h3><?php esc_html_e( 'Ready-to-wear shirts', 'hadhramifashion' ); ?></h3>
				<p>
					<?php
					printf(
						/* translators: 1: radius, 2: cutoff hour */
						esc_html__( 'Whites, blues and evening shirts in every collar size, to any address within %1$s km. Order by %2$s:00 for same-day delivery; later orders arrive first thing next morning.', 'hadhramifashion' ),
						esc_html( hf_opt( 'express_radius_km' ) ),
						esc_html( hf_opt( 'express_cutoff_hour' ) )
					);
					?>
				</p>
			</div>
			<div class="era">
				<small><?php esc_html_e( 'Within 24 hours', 'hadhramifashion' ); ?></small>
				<h3><?php esc_html_e( 'Made-to-measure suits & shirts', 'hadhramifashion' ); ?></h3>
				<p>
					<?php
					printf(
						/* translators: %s: radius */
						esc_html__( 'Send your measurements or let our tailor visit you. Your pattern is drafted, cut and finished overnight, and delivered within %s km.', 'hadhramifashion' ),
						esc_html( hf_opt( 'tailored_radius_km' ) )
					);
					?>
				</p>
			</div>
			<div class="era">
				<small><?php esc_html_e( 'Live on the map', 'hadhramifashion' ); ?></small>
				<h3><?php esc_html_e( 'Follow your rider', 'hadhramifashion' ); ?></h3>
				<p><?php esc_html_e( 'From the moment your piece leaves the atelier you can watch it move across the city, and call your rider directly.', 'hadhramifashion' ); ?></p>
			</div>
		</div>
	</div>
</section>
<?php
get_footer();
