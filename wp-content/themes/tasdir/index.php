<?php
/**
 * Blog / archive / search fallback.
 *
 * @package Tasdir
 */

defined( 'ABSPATH' ) || exit;

get_header();

if ( is_search() ) {
	/* translators: %s: search query. */
	$tasdir_title = sprintf( __( 'Search results for “%s”', 'tasdir' ), get_search_query() );
} elseif ( is_archive() ) {
	$tasdir_title = wp_strip_all_tags( get_the_archive_title() );
} elseif ( is_home() && get_option( 'page_for_posts' ) ) {
	$tasdir_title = get_the_title( get_option( 'page_for_posts' ) );
} else {
	$tasdir_title = __( 'News & insights', 'tasdir' );
}

get_template_part( 'template-parts/page-hero', null, array( 'title' => $tasdir_title ) );
?>
<section class="section">
	<div class="wrap">
		<?php if ( have_posts() ) : ?>
			<div class="post-list">
				<?php
				while ( have_posts() ) :
					the_post();
					?>
					<article <?php post_class( 'card' ); ?>>
						<?php if ( has_post_thumbnail() ) : ?>
							<a class="card__media" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true"><?php the_post_thumbnail( 'tasdir-card', array( 'sizes' => '(min-width: 980px) 370px, (min-width: 640px) 50vw, 100vw' ) ); ?></a>
						<?php endif; ?>
						<div class="card__body">
							<div class="entry-meta"><?php echo esc_html( get_the_date() ); ?></div>
							<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
							<p><?php echo esc_html( get_the_excerpt() ); ?></p>
						</div>
					</article>
				<?php endwhile; ?>
			</div>
			<div class="pagination"><?php echo paginate_links(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
		<?php else : ?>
			<div class="content">
				<p><?php esc_html_e( 'Nothing found. Try a different search or contact our export desk.', 'tasdir' ); ?></p>
				<?php get_search_form(); ?>
			</div>
		<?php endif; ?>
	</div>
</section>
<?php
get_footer();
