<?php
/**
 * Single post.
 *
 * @package FlavorKit
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<main id="primary" class="fk-main">
		<article id="post-<?php the_ID(); ?>" <?php post_class( 'fk-single' ); ?>>
			<header class="fk-page-header">
				<div class="fk-container fk-container-narrow">
					<?php the_category( ' ' ); ?>
					<h1 class="fk-page-title"><?php the_title(); ?></h1>
					<?php flavorkit_posted_on(); ?>
				</div>
				<?php flavorkit_wave( 'bottom', 'var(--fk-cream)' ); ?>
			</header>

			<div class="fk-section">
				<div class="fk-container fk-container-narrow">
					<?php if ( has_post_thumbnail() ) : ?>
						<figure class="fk-single-thumb"><?php the_post_thumbnail( 'flavorkit-wide' ); ?></figure>
					<?php endif; ?>
					<div class="fk-entry">
						<?php the_content(); ?>
						<?php wp_link_pages(); ?>
					</div>
					<?php the_tags( '<p class="fk-tags">', ' ', '</p>' ); ?>
					<?php
					the_post_navigation(
						array(
							'prev_text' => '<span>' . esc_html__( 'Previous', 'flavorkit' ) . '</span>%title',
							'next_text' => '<span>' . esc_html__( 'Next', 'flavorkit' ) . '</span>%title',
						)
					);
					if ( comments_open() || get_comments_number() ) {
						comments_template();
					}
					?>
				</div>
			</div>
		</article>
	</main>
	<?php
endwhile;

get_footer();
