<?php
/**
 * Fallback template for posts, archives and search.
 *
 * @package luxurywatchs
 */

get_header();
?>
<div class="lw-container lw-page">
	<header class="lw-page__head">
		<h1 class="lw-h2">
			<?php
			if ( is_home() ) {
				esc_html_e( 'Journal', 'luxurywatchs' );
			} elseif ( is_search() ) {
				/* translators: %s search query */
				printf( esc_html__( 'Results for “%s”', 'luxurywatchs' ), esc_html( get_search_query() ) );
			} else {
				the_archive_title();
			}
			?>
		</h1>
	</header>

	<?php if ( have_posts() ) : ?>
		<div class="lw-posts">
			<?php
			while ( have_posts() ) :
				the_post();
				?>
				<article <?php post_class( 'lw-post-card' ); ?>>
					<?php if ( has_post_thumbnail() ) : ?>
						<a href="<?php the_permalink(); ?>" class="lw-post-card__media"><?php the_post_thumbnail( 'lw-card' ); ?></a>
					<?php endif; ?>
					<h2 class="lw-post-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
					<div class="lw-post-card__excerpt"><?php the_excerpt(); ?></div>
				</article>
			<?php endwhile; ?>
		</div>
		<div class="lw-pagination"><?php the_posts_pagination(); ?></div>
	<?php else : ?>
		<p><?php esc_html_e( 'Nothing found. Try another search or browse the shop.', 'luxurywatchs' ); ?></p>
		<a class="lw-btn lw-btn--gold" href="<?php echo esc_url( lw_shop_url() ); ?>"><?php esc_html_e( 'Shop Watches', 'luxurywatchs' ); ?></a>
	<?php endif; ?>
</div>
<?php
get_footer();
