<?php
/**
 * Fallback template (blog index, archives).
 *
 * @package DGF
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<section class="dgf-hero dgf-hero--slim">
	<div class="dgf-hero__shade" aria-hidden="true"></div>
	<div class="dgf-wrap dgf-hero__inner">
		<p class="dgf-eyebrow dgf-hero__eyebrow"><?php echo esc_html( dgf_opt( 'brand_name' ) ); ?></p>
		<h1 class="dgf-hero__title">
			<?php
			if ( is_search() ) {
				/* translators: %s: search query */
				printf( esc_html__( 'Search: %s', 'dgf' ), esc_html( get_search_query() ) );
			} elseif ( is_archive() ) {
				echo esc_html( wp_strip_all_tags( get_the_archive_title() ) );
			} else {
				esc_html_e( 'News & guides', 'dgf' );
			}
			?>
		</h1>
	</div>
</section>
<div class="dgf-content">
	<div class="dgf-section">
		<?php if ( have_posts() ) : ?>
			<div class="dgf-posts">
				<?php
				while ( have_posts() ) :
					the_post();
					?>
					<article <?php post_class( 'dgf-post-card' ); ?>>
						<?php if ( has_post_thumbnail() ) : ?>
							<a href="<?php the_permalink(); ?>" class="dgf-post-card__media"><?php the_post_thumbnail( 'dgf-card', array( 'loading' => 'lazy' ) ); ?></a>
						<?php endif; ?>
						<h2 class="dgf-post-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
						<p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 24, '…' ) ); ?></p>
					</article>
				<?php endwhile; ?>
			</div>
			<?php the_posts_pagination(); ?>
		<?php else : ?>
			<p><?php esc_html_e( 'Nothing found. Try a search or message us on WhatsApp.', 'dgf' ); ?></p>
			<?php get_search_form(); ?>
		<?php endif; ?>
	</div>
	<?php echo do_shortcode( '[dgf_cta]' ); ?>
</div>
<?php
get_footer();
