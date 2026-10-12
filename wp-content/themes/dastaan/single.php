<?php
/**
 * Single post.
 *
 * @package Dastaan
 */

defined( 'ABSPATH' ) || exit;
get_header();

while ( have_posts() ) :
	the_post();
	?>
	<article id="post-<?php the_ID(); ?>" <?php post_class( 'single-article' ); ?>>
		<div class="container page-wrap page-wrap--narrow">
			<?php dastaan_breadcrumbs(); ?>
			<header class="entry-header">
				<?php
				$dastaan_cats = get_the_category();
				if ( $dastaan_cats ) {
					echo '<p class="eyebrow">' . esc_html( $dastaan_cats[0]->name ) . '</p>';
				}
				?>
				<h1 class="entry-title"><?php the_title(); ?></h1>
				<p class="entry-meta"><?php dastaan_posted_on(); ?></p>
			</header>
			<?php if ( has_post_thumbnail() ) : ?>
				<figure class="entry-thumbnail"><?php the_post_thumbnail( 'large' ); ?></figure>
			<?php endif; ?>
			<div class="entry-content">
				<?php
				the_content();
				wp_link_pages( array( 'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'dastaan' ), 'after' => '</div>' ) );
				?>
			</div>
			<footer class="entry-footer">
				<?php the_tags( '<p class="tag-list">', ' ', '</p>' ); ?>
				<nav class="post-nav" aria-label="<?php esc_attr_e( 'Post navigation', 'dastaan' ); ?>">
					<?php previous_post_link( '<span class="post-nav__prev">%link</span>', '← %title' ); ?>
					<?php next_post_link( '<span class="post-nav__next">%link</span>', '%title →' ); ?>
				</nav>
			</footer>
			<?php
			if ( comments_open() || get_comments_number() ) {
				comments_template();
			}
			?>
		</div>
	</article>
	<?php
endwhile;

get_footer();
