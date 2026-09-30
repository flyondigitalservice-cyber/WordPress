<?php
/**
 * Blog, archive and search listing.
 *
 * @package Zobo
 */

get_header();
?>

<section class="page-hero">
	<div class="container">
		<p class="kicker">
			<?php
			if ( is_search() ) {
				esc_html_e( 'Search', 'zobo-d2c' );
			} elseif ( is_post_type_archive( 'zobo_work' ) ) {
				esc_html_e( 'Work', 'zobo-d2c' );
			} else {
				esc_html_e( 'Journal', 'zobo-d2c' );
			}
			?>
		</p>
		<h1 class="page-title">
			<?php
			if ( is_search() ) {
				/* translators: %s: search query */
				printf( esc_html__( 'Results for “%s”', 'zobo-d2c' ), esc_html( get_search_query() ) );
			} elseif ( is_archive() ) {
				echo esc_html( wp_strip_all_tags( get_the_archive_title() ) );
			} elseif ( is_home() && get_option( 'page_for_posts' ) ) {
				echo esc_html( get_the_title( (int) get_option( 'page_for_posts' ) ) );
			} else {
				esc_html_e( 'Notes for founders', 'zobo-d2c' );
			}
			?>
		</h1>
	</div>
</section>

<div class="container">
	<?php if ( have_posts() ) : ?>
		<div class="post-grid">
			<?php
			while ( have_posts() ) :
				the_post();
				?>
				<a <?php post_class( 'post-card' ); ?> href="<?php the_permalink(); ?>">
					<?php if ( has_post_thumbnail() ) : ?>
						<div class="work-media"><?php the_post_thumbnail( 'zobo-work', array( 'loading' => 'lazy' ) ); ?></div>
					<?php endif; ?>
					<?php if ( 'post' === get_post_type() ) : ?>
						<p class="page-meta"><?php echo esc_html( get_the_date() ); ?></p>
					<?php endif; ?>
					<h2><?php the_title(); ?></h2>
					<p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 24 ) ); ?></p>
				</a>
			<?php endwhile; ?>
		</div>
		<div class="pagination"><?php the_posts_pagination( array( 'mid_size' => 1 ) ); ?></div>
	<?php else : ?>
		<div class="not-found">
			<p><?php esc_html_e( 'Nothing here yet. Try a search, or head back home.', 'zobo-d2c' ); ?></p>
			<?php get_search_form(); ?>
		</div>
	<?php endif; ?>
</div>

<?php
get_footer();
