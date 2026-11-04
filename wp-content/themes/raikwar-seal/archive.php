<?php
/**
 * Archives.
 */
get_header();
?>
<header class="page-hero grain">
	<div class="wrap">
		<div class="crumbs"><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a> / Archive</div>
		<h1><?php echo esc_html( wp_strip_all_tags( get_the_archive_title() ) ); ?></h1>
		<?php the_archive_description( '<p>', '</p>' ); ?>
	</div>
</header>
<div class="content-area"><div class="wrap"><?php get_template_part( 'template-parts/loop' ); ?></div></div>
<?php
get_template_part( 'template-parts/cta' );
get_footer();
