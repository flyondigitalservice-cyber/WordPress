<?php
/**
 * Single blog post.
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
		?>
		<article id="post-<?php the_ID(); ?>" <?php post_class( 'dce-entry' ); ?>>
			<header class="dce-container dce-page-head" style="max-width:860px">
				<p class="dce-kicker"><?php echo esc_html( get_the_date() ); ?></p>
				<h1><?php the_title(); ?></h1>
			</header>
			<?php if ( has_post_thumbnail() ) : ?>
				<div class="dce-container" style="max-width:1040px"><?php the_post_thumbnail( 'large', array( 'style' => 'width:100%;border-radius:24px;aspect-ratio:16/9;object-fit:cover' ) ); ?></div>
			<?php endif; ?>
			<div class="entry-content is-layout-constrained has-global-padding" style="padding-top:2rem"><?php the_content(); ?></div>
			<div class="dce-container" style="max-width:860px;padding-bottom:3rem" id="quote">
				<?php echo do_shortcode( '[dce_lead_form title="' . esc_attr__( 'Need help choosing?', 'dce-vibe' ) . '"]' ); // phpcs:ignore ?>
			</div>
		</article>
	<?php endwhile; ?>
</main>
<?php
get_footer();
