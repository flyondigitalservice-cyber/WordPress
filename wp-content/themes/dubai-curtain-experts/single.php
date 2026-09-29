<?php
/**
 * Single post template — blog guides.
 *
 * @package Dubai_Curtain_Experts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main id="primary" class="dce-main dce-page-main dce-post-main">
	<div class="dce-container dce-narrow">
		<?php dce_breadcrumbs(); ?>
		<?php
		while ( have_posts() ) :
			the_post();
			?>
			<article id="post-<?php the_ID(); ?>" <?php post_class( 'dce-single' ); ?>>
				<header class="dce-page-head">
					<p class="dce-post-meta">
						<?php echo esc_html( get_the_date() ); ?>
						<?php
						$cats = get_the_category();
						if ( $cats ) {
							echo ' · <a href="' . esc_url( get_category_link( $cats[0] ) ) . '">' . esc_html( $cats[0]->name ) . '</a>';
						}
						?>
					</p>
					<h1 class="dce-h1"><?php the_title(); ?></h1>
				</header>
				<?php if ( has_post_thumbnail() ) : ?>
					<div class="dce-imgframe dce-single-frame">
						<?php the_post_thumbnail( 'dce-hero', array( 'fetchpriority' => 'high' ) ); ?>
					</div>
				<?php endif; ?>
				<div class="dce-entry-content">
					<?php the_content(); ?>
				</div>
				<footer class="dce-single-foot">
					<?php the_tags( '<div class="dce-tags">', ' ', '</div>' ); ?>
					<nav class="dce-post-nav" aria-label="<?php esc_attr_e( 'Post navigation', 'dubai-curtain-experts' ); ?>">
						<?php previous_post_link( '<span class="dce-post-nav-prev">%link</span>', '&larr; %title' ); ?>
						<?php next_post_link( '<span class="dce-post-nav-next">%link</span>', '%title &rarr;' ); ?>
					</nav>
				</footer>
			</article>
			<?php
		endwhile;
		?>
	</div>
</main>

<?php
get_footer();
