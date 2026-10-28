<?php
/**
 * Fallback template (blog index, archives).
 *
 * @package CFM
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<section class="cfm-hero cfm-hero--slim">
	<div class="cfm-hero__shade" aria-hidden="true"></div>
	<div class="cfm-wrap cfm-hero__inner">
		<p class="cfm-eyebrow cfm-hero__eyebrow"><?php echo esc_html( cfm_opt( 'brand_name' ) ); ?></p>
		<h1 class="cfm-hero__title">
			<?php
			if ( is_search() ) {
				/* translators: %s: search query */
				printf( esc_html__( 'Search: %s', 'cfm' ), esc_html( get_search_query() ) );
			} elseif ( is_archive() ) {
				echo esc_html( wp_strip_all_tags( get_the_archive_title() ) );
			} else {
				esc_html_e( 'News & guides', 'cfm' );
			}
			?>
		</h1>
	</div>
</section>
<div class="cfm-content">
	<div class="cfm-section">
		<?php if ( have_posts() ) : ?>
			<div class="cfm-posts">
				<?php
				while ( have_posts() ) :
					the_post();
					?>
					<article <?php post_class( 'cfm-post-card' ); ?>>
						<?php if ( has_post_thumbnail() ) : ?>
							<a href="<?php the_permalink(); ?>" class="cfm-post-card__media"><?php the_post_thumbnail( 'cfm-card', array( 'loading' => 'lazy' ) ); ?></a>
						<?php endif; ?>
						<h2 class="cfm-post-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
						<p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 24, '…' ) ); ?></p>
					</article>
				<?php endwhile; ?>
			</div>
			<?php the_posts_pagination(); ?>
		<?php else : ?>
			<p><?php esc_html_e( 'Nothing found. Try a search or message us on WhatsApp.', 'cfm' ); ?></p>
			<?php get_search_form(); ?>
		<?php endif; ?>
	</div>
	<?php echo do_shortcode( '[cfm_cta]' ); ?>
</div>
<?php
get_footer();
