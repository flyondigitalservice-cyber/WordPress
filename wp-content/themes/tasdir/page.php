<?php
/**
 * Page template. Every page ends with a quote call-to-action.
 *
 * @package Tasdir
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	get_template_part( 'template-parts/page-hero', null, array( 'title' => get_the_title() ) );
	?>
	<section class="section">
		<div class="wrap">
			<article <?php post_class( 'content' ); ?>>
				<?php
				if ( has_post_thumbnail() ) {
					the_post_thumbnail( 'tasdir-wide', array( 'loading' => 'eager', 'sizes' => '(min-width: 820px) 780px, 100vw' ) );
				}
				the_content();
				wp_link_pages();
				?>
			</article>
		</div>
	</section>
	<?php
	get_template_part( 'template-parts/cta-band' );
endwhile;

get_footer();
