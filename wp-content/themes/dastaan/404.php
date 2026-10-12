<?php
/**
 * 404.
 *
 * @package Dastaan
 */

defined( 'ABSPATH' ) || exit;
get_header();
?>
<div class="container page-wrap page-wrap--narrow error-404">
	<p class="eyebrow"><?php esc_html_e( 'Error 404', 'dastaan' ); ?></p>
	<h1 class="page-title"><?php esc_html_e( 'This thread leads nowhere', 'dastaan' ); ?></h1>
	<p class="section-text"><?php esc_html_e( 'The page you are looking for has moved or never existed. Try a search, or explore the collection.', 'dastaan' ); ?></p>
	<?php get_search_form(); ?>
	<p class="error-404__actions">
		<a class="btn btn--primary" href="<?php echo esc_url( dastaan_has_wc() ? wc_get_page_permalink( 'shop' ) : home_url( '/' ) ); ?>"><?php esc_html_e( 'Explore the collection', 'dastaan' ); ?></a>
		<a class="btn btn--text" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Back home', 'dastaan' ); ?></a>
	</p>
</div>
<?php
if ( dastaan_has_wc() ) {
	get_template_part( 'template-parts/home/products', null, array( 'force' => true, 'source' => 'recent', 'count' => 8, 'title' => __( 'New in the studio', 'dastaan' ), 'eyebrow' => __( 'Meanwhile', 'dastaan' ) ) );
}
get_footer();
