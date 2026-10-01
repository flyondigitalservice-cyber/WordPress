<?php
/**
 * Page template. Pages are built from blocks — edit them in the block editor.
 *
 * @package dce-vibe
 */

get_header();
?>
<main id="primary" class="dce-main">
	<?php
	while ( have_posts() ) :
		the_post();
		dce_breadcrumbs();
		$dce_has_h1 = false !== strpos( get_the_content(), '<h1' );
		?>
		<article id="post-<?php the_ID(); ?>" <?php post_class( 'dce-entry' ); ?>>
			<?php if ( ! $dce_has_h1 ) : ?>
				<header class="dce-container dce-page-head"><h1><?php the_title(); ?></h1></header>
				<div class="dce-container"><?php the_content(); ?></div>
			<?php else : ?>
				<div class="entry-content is-layout-constrained has-global-padding"><?php the_content(); ?></div>
			<?php endif; ?>
		</article>
	<?php endwhile; ?>
</main>
<?php
get_footer();
