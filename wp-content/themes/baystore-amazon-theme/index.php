<?php
/**
 * Baystore Amazon Lean — Main Index Fallback
 *
 * @package Baystore_Amazon
 */

get_header();
?>
<main class="bs-container" style="padding:24px 16px;">
	<div style="background:#fff;border-radius:6px;padding:24px;border:1px solid #e3e6e6;">
		<?php if ( have_posts() ) : ?>
			<?php while ( have_posts() ) : the_post(); ?>
				<article style="margin-bottom:24px;padding-bottom:20px;border-bottom:1px solid #eee;">
					<h2 style="margin:0 0 8px;"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
					<div><?php the_excerpt(); ?></div>
				</article>
			<?php endwhile; ?>
			<?php the_posts_pagination(); ?>
		<?php else : ?>
			<p>No content found.</p>
		<?php endif; ?>
	</div>
</main>
<?php
get_footer();
