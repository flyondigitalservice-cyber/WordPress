<?php
/**
 * Baystore Amazon Lean — Page Template (handles Cart, Checkout, My Account, Info Pages)
 *
 * @package Baystore_Amazon
 */

get_header();
?>
<main class="bs-container" style="padding:24px 16px;">
	<div style="background:#fff;border-radius:6px;padding:24px;border:1px solid #e3e6e6;">
		<?php
		while ( have_posts() ) :
			the_post();
			?>
			<h1 style="margin:0 0 16px;font-size:24px;font-weight:800;"><?php the_title(); ?></h1>
			<div><?php the_content(); ?></div>
		<?php endwhile; ?>
	</div>
</main>
<?php
get_footer();
