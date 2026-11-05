<?php
/**
 * Fallback template: news, archives and search.
 *
 * @package Lawyers_Foundation_Theme
 */

get_header();
?>
<header class="page-header">
	<div class="wrap">
		<h1>
			<?php
			if ( is_home() ) {
				esc_html_e( 'News', 'lawyers-foundation-theme' );
			} elseif ( is_search() ) {
				/* translators: %s: search query */
				printf( esc_html__( 'Search results for “%s”', 'lawyers-foundation-theme' ), esc_html( get_search_query() ) );
			} elseif ( is_404() ) {
				esc_html_e( 'Page not found', 'lawyers-foundation-theme' );
			} else {
				echo esc_html( wp_strip_all_tags( get_the_archive_title() ) );
			}
			?>
		</h1>
	</div>
</header>
<div class="content-area">
	<div class="wrap post-list">
		<?php if ( have_posts() && ! is_404() ) : ?>
			<?php
			while ( have_posts() ) :
				the_post();
				?>
				<article <?php post_class(); ?>>
					<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
					<?php if ( 'lf_event' === get_post_type() && function_exists( 'lf_format_date' ) ) : ?>
						<p class="meta"><?php echo esc_html( lf_format_date( get_post_meta( get_the_ID(), '_lf_start', true ) ) ); ?></p>
					<?php elseif ( 'post' === get_post_type() ) : ?>
						<p class="meta"><?php echo esc_html( get_the_date() ); ?></p>
					<?php endif; ?>
					<?php the_excerpt(); ?>
				</article>
			<?php endwhile; ?>
			<div class="pagination"><?php echo wp_kses_post( paginate_links() ); ?></div>
		<?php else : ?>
			<p><?php esc_html_e( 'Nothing found here.', 'lawyers-foundation-theme' ); ?> <a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Return home', 'lawyers-foundation-theme' ); ?></a></p>
		<?php endif; ?>
	</div>
</div>
<?php
get_footer();
