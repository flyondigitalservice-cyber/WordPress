<?php
/**
 * Pages (all 45 site pages use this template). Content is edited in the block editor.
 *
 * @package DGF
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	if ( '1' !== get_post_meta( get_the_ID(), '_dgf_hide_hero', true ) ) {
		get_template_part( 'template-parts/hero' );
	}
	?>
	<article id="post-<?php the_ID(); ?>" <?php post_class( 'dgf-page' ); ?>>
		<div class="dgf-content">
			<?php the_content(); ?>
		</div>
	</article>
	<?php
endwhile;

get_footer();
