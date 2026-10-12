<?php
/**
 * Fallback template.
 *
 * @package HadhramiFashion
 */

get_header();
?>
<header class="page-hero">
	<div class="wrap">
		<h1><?php echo is_home() ? esc_html__( 'Journal', 'hadhramifashion' ) : wp_kses_post( get_the_archive_title() ); ?></h1>
	</div>
</header>
<div class="wrap wrap--narrow section--tight">
	<?php if ( have_posts() ) : ?>
		<?php
		while ( have_posts() ) :
			the_post();
			?>
			<article <?php post_class( 'reveal' ); ?> style="margin-bottom:3rem">
				<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
				<div class="entry-content"><?php the_excerpt(); ?></div>
			</article>
		<?php endwhile; ?>
		<div class="pagination"><?php echo paginate_links(); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
	<?php else : ?>
		<p class="lede"><?php esc_html_e( 'Nothing to show yet.', 'hadhramifashion' ); ?></p>
	<?php endif; ?>
</div>
<?php
get_footer();
