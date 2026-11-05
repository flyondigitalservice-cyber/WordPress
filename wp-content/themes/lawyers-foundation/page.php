<?php
/**
 * Pages and single posts / events / initiatives.
 *
 * @package Lawyers_Foundation_Theme
 */

get_header();
while ( have_posts() ) :
	the_post();
	?>
	<header class="page-header">
		<div class="wrap">
			<?php if ( 'lf_event' === get_post_type() ) : ?>
				<p class="eyebrow"><?php esc_html_e( 'Foundation event', 'lawyers-foundation-theme' ); ?></p>
			<?php elseif ( 'lf_initiative' === get_post_type() ) : ?>
				<p class="eyebrow"><?php esc_html_e( 'Initiative', 'lawyers-foundation-theme' ); ?></p>
			<?php elseif ( 'post' === get_post_type() ) : ?>
				<p class="eyebrow"><?php echo esc_html( get_the_date() ); ?></p>
			<?php endif; ?>
			<h1><?php the_title(); ?></h1>
		</div>
	</header>
	<div class="content-area">
		<div class="wrap">
			<article <?php post_class( 'entry-content' ); ?>>
				<?php
				if ( has_post_thumbnail() && 'page' !== get_post_type() ) {
					the_post_thumbnail( 'large' );
				}
				the_content();
				wp_link_pages();
				?>
			</article>
			<?php
			if ( 'post' === get_post_type() && ( comments_open() || get_comments_number() ) ) {
				comments_template();
			}
			?>
		</div>
	</div>
	<?php
endwhile;
get_footer();
