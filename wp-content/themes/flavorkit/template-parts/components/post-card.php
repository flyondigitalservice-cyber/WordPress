<?php
/**
 * Blog post card.
 *
 * @package FlavorKit
 */

defined( 'ABSPATH' ) || exit;
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'fk-post-card fk-reveal' ); ?>>
	<a class="fk-post-media fk-tone-<?php echo esc_attr( get_the_ID() % 4 ); ?>" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
		<?php
		if ( has_post_thumbnail() ) {
			the_post_thumbnail( 'flavorkit-card' );
		} else {
			echo flavorkit_pack_svg( 'jar', get_the_ID(), strtoupper( wp_trim_words( get_the_title(), 1, '' ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput
		}
		?>
	</a>
	<div class="fk-post-body">
		<?php flavorkit_posted_on(); ?>
		<h2 class="fk-post-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
		<p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 22 ) ); ?></p>
		<a class="fk-link" href="<?php the_permalink(); ?>"><?php esc_html_e( 'Read more', 'flavorkit' ); ?> <?php echo flavorkit_icon( 'arrow', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span class="screen-reader-text"> <?php the_title(); ?></span></a>
	</div>
</article>
