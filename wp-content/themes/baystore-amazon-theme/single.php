<?php
/**
 * Baystore Amazon Lean — Single Post Template
 *
 * @package Baystore_Amazon
 */

get_header();
?>
<main class="bs-container" style="padding:24px 16px;">
	<article style="background:#fff;border-radius:6px;padding:28px;border:1px solid #e3e6e6;max-width:900px;margin:0 auto;">
		<?php
		while ( have_posts() ) :
			the_post();
			?>
			<h1 style="margin:0 0 12px;font-size:28px;font-weight:800;"><?php the_title(); ?></h1>
			<div style="font-size:12px;color:#565959;margin-bottom:20px;"><?php echo esc_html( get_the_date() ); ?></div>
			<div><?php the_content(); ?></div>
		<?php endwhile; ?>
	</article>
</main>
<?php
get_footer();
