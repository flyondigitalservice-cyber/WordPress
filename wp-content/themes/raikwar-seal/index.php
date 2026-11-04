<?php
/**
 * Blog index / fallback.
 */
get_header();
$title = is_home() && get_option( 'page_for_posts' ) ? get_the_title( get_option( 'page_for_posts' ) ) : __( 'Updates', 'raikwar-seal' );
?>
<header class="page-hero grain">
	<div class="wrap">
		<div class="crumbs"><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a> / <?php echo esc_html( $title ); ?></div>
		<h1><?php echo esc_html( $title ); ?></h1>
		<p>Guides and news on DSC, GST, licences and government registrations.</p>
	</div>
</header>
<div class="content-area"><div class="wrap"><?php get_template_part( 'template-parts/loop' ); ?></div></div>
<?php
get_template_part( 'template-parts/cta' );
get_footer();
