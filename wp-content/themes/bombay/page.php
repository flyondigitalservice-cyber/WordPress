<?php
/**
 * Pages. Elementor and bm- pages render their own layout; plain pages get a hero and readable text column.
 *
 * @package Bombay
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	if ( bombay_has_own_layout() ) :
		?>
		<main class="bm-main"><?php the_content(); ?></main>
		<?php
	else :
		?>
		<main class="bm-main bm-page">
			<?php get_template_part( 'template-parts/hero', null, array( 'title' => get_the_title() ) ); ?>
			<section class="bm-sec">
				<div class="bm-wrap bm-prose"><?php the_content(); ?></div>
			</section>
		</main>
		<?php
	endif;
endwhile;

get_footer();
