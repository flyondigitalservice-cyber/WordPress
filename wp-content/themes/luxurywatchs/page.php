<?php
/**
 * Page template (also used by cart, checkout and account pages).
 *
 * @package luxurywatchs
 */

get_header();
?>
<div class="lw-container lw-page">
	<?php
	while ( have_posts() ) :
		the_post();
		?>
		<article <?php post_class(); ?>>
			<header class="lw-page__head"><h1 class="lw-h2"><?php the_title(); ?></h1></header>
			<div class="lw-content"><?php the_content(); ?></div>
		</article>
	<?php endwhile; ?>
</div>
<?php
get_footer();
