<?php
/**
 * Template Name: Full-width Blocks
 * Template Post Type: page
 *
 * Edge-to-edge template for pages built in the block editor. The page's own
 * blocks provide the headline, so no automatic title is printed; breadcrumbs
 * are shown on every page except the homepage.
 *
 * @package Dubai_Curtain_Experts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main id="primary" class="dce-main dce-blocks-main">
	<?php
	while ( have_posts() ) :
		the_post();
		if ( ! is_front_page() ) :
			?>
			<div class="dce-container dce-crumbs-wrap"><?php dce_breadcrumbs(); ?></div>
		<?php endif; ?>
		<article id="post-<?php the_ID(); ?>" <?php post_class( 'dce-blocks' ); ?>>
			<?php the_content(); ?>
		</article>
		<?php
	endwhile;
	?>
</main>

<?php
get_footer();
