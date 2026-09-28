<?php
/**
 * Single blog post.
 *
 * @package DGF
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<section class="dgf-hero dgf-hero--slim<?php echo has_post_thumbnail() ? ' has-image' : ''; ?>">
		<?php if ( has_post_thumbnail() ) : ?>
			<?php the_post_thumbnail( 'dgf-hero', array( 'class' => 'dgf-hero__img', 'loading' => 'eager', 'fetchpriority' => 'high' ) ); ?>
		<?php endif; ?>
		<div class="dgf-hero__shade" aria-hidden="true"></div>
		<div class="dgf-wrap dgf-hero__inner">
			<p class="dgf-eyebrow dgf-hero__eyebrow"><?php echo esc_html( get_the_date() ); ?></p>
			<h1 class="dgf-hero__title"><?php the_title(); ?></h1>
		</div>
	</section>
	<article id="post-<?php the_ID(); ?>" <?php post_class( 'dgf-page' ); ?>>
		<div class="dgf-content dgf-prose">
			<?php the_content(); ?>
		</div>
		<?php echo do_shortcode( '[dgf_cta]' ); ?>
	</article>
	<?php
endwhile;

get_footer();
