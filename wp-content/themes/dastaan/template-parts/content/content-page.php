<?php
/**
 * Post card.
 *
 * @package Dastaan
 */

defined( 'ABSPATH' ) || exit;
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'post-card' ); ?>>
	<?php if ( has_post_thumbnail() ) : ?>
		<a class="post-card__media" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true"><?php the_post_thumbnail( 'dastaan-tile' ); ?></a>
	<?php endif; ?>
	<div class="post-card__body">
		<?php
		$dastaan_cats = get_the_category();
		if ( $dastaan_cats ) {
			echo '<p class="eyebrow">' . esc_html( $dastaan_cats[0]->name ) . '</p>';
		}
		?>
		<h2 class="post-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
		<p class="post-card__excerpt"><?php echo esc_html( get_the_excerpt() ); ?></p>
		<p class="post-card__meta"><?php dastaan_posted_on(); ?></p>
	</div>
</article>
