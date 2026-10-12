<?php
/**
 * Archive template.
 *
 * @package Dastaan
 */

defined( 'ABSPATH' ) || exit;
get_header();
?>
<div class="container page-wrap">
	<?php dastaan_breadcrumbs(); ?>
	<header class="page-header">
		<?php if ( is_home() && ! is_front_page() ) : ?>
			<h1 class="page-title"><?php single_post_title(); ?></h1>
		<?php elseif ( is_archive() ) : ?>
			<h1 class="page-title"><?php echo wp_kses_post( get_the_archive_title() ); ?></h1>
			<?php the_archive_description( '<div class="archive-description">', '</div>' ); ?>
		<?php else : ?>
			<h1 class="page-title"><?php esc_html_e( 'Journal', 'dastaan' ); ?></h1>
		<?php endif; ?>
	</header>
	<?php if ( have_posts() ) : ?>
		<div class="post-grid">
			<?php
			while ( have_posts() ) {
				the_post();
				get_template_part( 'template-parts/content/content', get_post_type() );
			}
			?>
		</div>
		<?php
		the_posts_pagination(
			array(
				'mid_size'  => 1,
				'prev_text' => dastaan_icon( 'chevron-left', array( 'size' => 16 ) ) . '<span class="screen-reader-text">' . esc_html__( 'Previous', 'dastaan' ) . '</span>',
				'next_text' => '<span class="screen-reader-text">' . esc_html__( 'Next', 'dastaan' ) . '</span>' . dastaan_icon( 'chevron-right', array( 'size' => 16 ) ),
			)
		);
		?>
	<?php else : ?>
		<?php get_template_part( 'template-parts/content/content', 'none' ); ?>
	<?php endif; ?>
</div>
<?php
get_footer();
