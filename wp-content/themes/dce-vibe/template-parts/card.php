<?php
/**
 * Post card.
 *
 * @package dce-vibe
 */
?>
<article <?php post_class( 'dce-post-card' ); ?>>
	<?php if ( has_post_thumbnail() ) : ?>
		<a href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true"><?php the_post_thumbnail( 'dce-card' ); ?></a>
	<?php endif; ?>
	<div class="dce-post-card-body">
		<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
		<p><?php echo esc_html( get_the_excerpt() ); ?></p>
		<a href="<?php the_permalink(); ?>"><?php esc_html_e( 'Read the guide →', 'dce-vibe' ); ?></a>
	</div>
</article>
