<?php
/**
 * Fallback template: blog index, archives and search results as a card grid.
 *
 * @package Bombay
 */

defined( 'ABSPATH' ) || exit;

get_header();

if ( is_search() ) {
	$hero = array(
		'eyebrow' => 'Search',
		/* translators: %s: search query. */
		'title'   => sprintf( 'Results for &ldquo;%s&rdquo;', esc_html( get_search_query() ) ),
	);
} elseif ( is_archive() ) {
	$hero = array(
		'eyebrow' => 'Blog',
		'title'   => wp_strip_all_tags( get_the_archive_title() ),
		'lead'    => wp_strip_all_tags( get_the_archive_description() ),
	);
} else {
	$hero = array(
		'eyebrow' => 'Blog',
		'title'   => 'Latest articles',
	);
}
?>
<main class="bm-main bm-page">
	<?php get_template_part( 'template-parts/hero', null, $hero ); ?>
	<section class="bm-sec">
		<div class="bm-wrap">
			<?php if ( have_posts() ) : ?>
				<div class="bm-cards">
					<?php
					while ( have_posts() ) :
						the_post();
						?>
						<article class="bm-card">
							<p class="bm-card-meta"><?php echo esc_html( 'page' === get_post_type() ? 'Page' : get_the_date() ); ?></p>
							<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
							<p><?php echo esc_html( wp_trim_words( wp_strip_all_tags( get_the_excerpt() ), 26 ) ); ?></p>
							<a class="bm-card-link" href="<?php the_permalink(); ?>">Read more &rarr;</a>
						</article>
					<?php endwhile; ?>
				</div>
				<div class="bm-pager">
					<?php
					the_posts_pagination(
						array(
							'mid_size'  => 1,
							'prev_text' => '&larr; Newer',
							'next_text' => 'Older &rarr;',
						)
					);
					?>
				</div>
			<?php else : ?>
				<div class="bm-empty">
					<h2>Nothing found</h2>
					<p>Try another search, or browse our <a href="<?php echo esc_url( home_url( '/blog/' ) ); ?>">blog</a> and <a href="<?php echo esc_url( home_url( '/guides/' ) ); ?>">guides</a>.</p>
					<?php get_search_form(); ?>
				</div>
			<?php endif; ?>
		</div>
	</section>
</main>
<?php
get_footer();
