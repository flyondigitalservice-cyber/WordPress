<?php
/**
 * Single post.
 *
 * @package HadhramiFashion
 */

get_header();
while ( have_posts() ) :
	the_post();
	?>
	<header class="page-hero">
		<div class="wrap"><h1><?php the_title(); ?></h1></div>
	</header>
	<div class="wrap section--tight">
		<div class="entry-content"><?php the_content(); ?></div>
	</div>
	<?php
endwhile;
get_footer();
