<?php
/**
 * Blog index, archives and search results.
 *
 * @package dce-vibe
 */

get_header();
?>
<main id="primary" class="dce-main">
	<?php dce_breadcrumbs(); ?>
	<div class="dce-container">
		<header class="dce-page-head">
			<?php if ( is_home() && get_option( 'page_for_posts' ) ) : ?>
				<p class="dce-kicker"><?php esc_html_e( 'Guides & ideas', 'dce-vibe' ); ?></p>
				<h1><?php echo esc_html( get_the_title( get_option( 'page_for_posts' ) ) ); ?></h1>
			<?php elseif ( is_search() ) : ?>
				<h1><?php printf( esc_html__( 'Search: %s', 'dce-vibe' ), esc_html( get_search_query() ) ); ?></h1>
			<?php elseif ( is_archive() ) : ?>
				<h1><?php echo wp_kses_post( get_the_archive_title() ); ?></h1>
			<?php else : ?>
				<h1><?php esc_html_e( 'Latest articles', 'dce-vibe' ); ?></h1>
			<?php endif; ?>
		</header>
		<?php if ( have_posts() ) : ?>
			<div class="dce-posts">
				<?php
				while ( have_posts() ) :
					the_post();
					get_template_part( 'template-parts/card' );
				endwhile;
				?>
			</div>
			<div class="dce-pagination"><?php the_posts_pagination(); ?></div>
		<?php else : ?>
			<p><?php esc_html_e( 'Nothing found yet.', 'dce-vibe' ); ?></p>
			<?php get_search_form(); ?>
		<?php endif; ?>
	</div>
</main>
<?php
get_footer();
