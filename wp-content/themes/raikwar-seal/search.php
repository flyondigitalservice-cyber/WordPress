<?php
/**
 * Search results.
 */
get_header();
?>
<header class="page-hero grain">
	<div class="wrap">
		<div class="crumbs"><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a> / Search</div>
		<h1>Results for “<?php echo esc_html( get_search_query() ); ?>”</h1>
		<div style="max-width:520px;margin-top:20px"><?php get_search_form(); ?></div>
	</div>
</header>
<div class="content-area"><div class="wrap"><?php get_template_part( 'template-parts/loop' ); ?></div></div>
<?php get_footer(); ?>
