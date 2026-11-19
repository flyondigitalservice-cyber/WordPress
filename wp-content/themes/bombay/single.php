<?php
/**
 * Single posts. Elementor/bm- posts render their own layout; plain posts get a simple article layout.
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
		$cats    = get_the_category();
		$eyebrow = 'Blog' . ( $cats ? ' &middot; ' . esc_html( $cats[0]->name ) : '' ) . ' &middot; ' . esc_html( get_the_date() );
		?>
		<main class="bm-main bm-page">
			<?php get_template_part( 'template-parts/hero', null, array( 'eyebrow' => $eyebrow, 'title' => get_the_title() ) ); ?>
			<section class="bm-sec">
				<article class="bm-wrap bm-prose">
					<?php
					if ( has_post_thumbnail() ) {
						the_post_thumbnail( 'large', array( 'class' => 'bm-prose-img' ) );
					}
					the_content();
					?>
				</article>
			</section>
		</main>
		<?php
	endif;
endwhile;

get_footer();
