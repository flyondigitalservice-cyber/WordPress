<?php
/**
 * Search results.
 *
 * @package Dastaan
 */

defined( 'ABSPATH' ) || exit;
get_header();
?>
<div class="container page-wrap">
	<?php dastaan_breadcrumbs(); ?>
	<header class="page-header">
		<p class="eyebrow"><?php esc_html_e( 'Search results', 'dastaan' ); ?></p>
		<h1 class="page-title"><?php echo esc_html( get_search_query() ); ?></h1>
		<?php get_search_form(); ?>
	</header>
	<?php if ( have_posts() ) : ?>
		<?php
		$dastaan_products = array();
		$dastaan_others   = array();
		while ( have_posts() ) {
			the_post();
			if ( 'product' === get_post_type() ) {
				$dastaan_products[] = get_the_ID();
			} else {
				$dastaan_others[] = get_the_ID();
			}
		}
		rewind_posts();
		?>
		<?php if ( $dastaan_products && dastaan_has_wc() ) : ?>
			<h2 class="section-title section-title--sm"><?php esc_html_e( 'Pieces', 'dastaan' ); ?></h2>
			<ul class="products columns-4">
				<?php
				foreach ( $dastaan_products as $dastaan_pid ) {
					$post = get_post( $dastaan_pid ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
					setup_postdata( $post );
					wc_get_template_part( 'content', 'product' );
				}
				wp_reset_postdata();
				?>
			</ul>
		<?php endif; ?>
		<?php if ( $dastaan_others ) : ?>
			<h2 class="section-title section-title--sm"><?php esc_html_e( 'Pages & journal', 'dastaan' ); ?></h2>
			<div class="post-grid">
				<?php
				foreach ( $dastaan_others as $dastaan_pid ) {
					$post = get_post( $dastaan_pid ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
					setup_postdata( $post );
					get_template_part( 'template-parts/content/content', 'search' );
				}
				wp_reset_postdata();
				?>
			</div>
		<?php endif; ?>
		<?php the_posts_pagination( array( 'mid_size' => 1 ) ); ?>
	<?php else : ?>
		<?php get_template_part( 'template-parts/content/content', 'none' ); ?>
	<?php endif; ?>
</div>
<?php
get_footer();
