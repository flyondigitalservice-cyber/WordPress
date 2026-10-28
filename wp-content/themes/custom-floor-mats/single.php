<?php
/**
 * Single blog post.
 *
 * @package CFM
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<section class="cfm-hero cfm-hero--slim<?php echo has_post_thumbnail() ? ' has-image' : ''; ?>">
		<?php if ( has_post_thumbnail() ) : ?>
			<?php the_post_thumbnail( 'cfm-hero', array( 'class' => 'cfm-hero__img', 'loading' => 'eager', 'fetchpriority' => 'high' ) ); ?>
		<?php endif; ?>
		<div class="cfm-hero__shade" aria-hidden="true"></div>
		<div class="cfm-wrap cfm-hero__inner">
			<p class="cfm-eyebrow cfm-hero__eyebrow"><?php echo esc_html( get_the_date() ); ?></p>
			<h1 class="cfm-hero__title"><?php the_title(); ?></h1>
		</div>
	</section>
	<article id="post-<?php the_ID(); ?>" <?php post_class( 'cfm-page' ); ?>>
		<div class="cfm-content cfm-prose">
			<?php the_content(); ?>
		</div>
		<?php echo do_shortcode( '[cfm_cta]' ); ?>
	</article>
	<?php
endwhile;

get_footer();
