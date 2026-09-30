<?php
/**
 * Single posts, pages and case studies.
 *
 * @package Zobo
 */

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<header class="page-hero">
			<div class="container">
				<?php if ( 'zobo_work' === get_post_type() ) : ?>
					<p class="kicker"><?php esc_html_e( 'Case study', 'zobo-d2c' ); ?></p>
				<?php elseif ( 'post' === get_post_type() ) : ?>
					<p class="kicker"><?php esc_html_e( 'Journal', 'zobo-d2c' ); ?></p>
				<?php endif; ?>
				<h1 class="page-title"><?php the_title(); ?></h1>
				<?php if ( 'post' === get_post_type() ) : ?>
					<p class="page-meta"><?php echo esc_html( get_the_date() ); ?> · <?php the_author(); ?></p>
				<?php endif; ?>
			</div>
		</header>

		<div class="container">
			<?php if ( has_post_thumbnail() && ! is_page() ) : ?>
				<div class="entry-thumb"><?php the_post_thumbnail( 'full' ); ?></div>
			<?php endif; ?>

			<div class="entry-content">
				<?php
				the_content();
				wp_link_pages();
				?>
			</div>

			<?php if ( comments_open() || get_comments_number() ) : ?>
				<?php comments_template(); ?>
			<?php endif; ?>
		</div>
	</article>
	<?php
endwhile;

get_footer();
