<?php
/**
 * Front page: section stack controlled by the Customizer.
 *
 * @package Dastaan
 */

defined( 'ABSPATH' ) || exit;

get_header();

$dastaan_sections = apply_filters(
	'dastaan_home_sections',
	array( 'circles', 'hero', 'usp', 'products-featured', 'banners', 'products-bestseller', 'crafts', 'reels', 'testimonials', 'bts', 'instagram', 'studio', 'page-content', 'seo-content' )
);

do_action( 'dastaan_home_sections_before' );

foreach ( $dastaan_sections as $dastaan_section ) {
	switch ( $dastaan_section ) {
		case 'products-featured':
			get_template_part( 'template-parts/home/products', null, array( 'prefix' => 'featured' ) );
			break;
		case 'products-bestseller':
			get_template_part( 'template-parts/home/products', null, array( 'prefix' => 'bestseller' ) );
			break;
		case 'page-content':
			if ( 'page' === get_option( 'show_on_front' ) && have_posts() ) {
				while ( have_posts() ) {
					the_post();
					$dastaan_content = trim( get_the_content() );
					if ( '' !== $dastaan_content ) {
						echo '<section class="section home-page-content"><div class="container entry-content">';
						the_content();
						echo '</div></section>';
					}
				}
				rewind_posts();
			}
			break;
		default:
			get_template_part( 'template-parts/home/' . $dastaan_section );
	}
}

do_action( 'dastaan_home_sections_after' );

get_footer();
