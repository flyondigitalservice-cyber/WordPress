<?php
/**
 * Single post.
 *
 * @package luxurywatchs
 */

get_header();
?>
<div class="lw-container lw-page lw-page--narrow">
	<?php
	while ( have_posts() ) :
		the_post();
		?>
		<article <?php post_class(); ?>>
			<header class="lw-page__head">
				<p class="lw-eyebrow"><?php echo esc_html( get_the_date() ); ?></p>
				<h1 class="lw-h2"><?php the_title(); ?></h1>
			</header>
			<?php if ( has_post_thumbnail() ) : ?>
				<div class="lw-single__media"><?php the_post_thumbnail( 'large' ); ?></div>
			<?php endif; ?>
			<div class="lw-content"><?php the_content(); ?></div>
		</article>
		<?php
		if ( comments_open() || get_comments_number() ) {
			comments_template();
		}
	endwhile;
	?>
</div>
<?php
get_footer();
