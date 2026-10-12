<?php
/**
 * Not found.
 *
 * @package HadhramiFashion
 */

get_header();
?>
<section class="section center">
	<div class="wrap wrap--narrow">
		<span class="eyebrow eyebrow--plain"><?php esc_html_e( 'Measure twice, cut once', 'hadhramifashion' ); ?></span>
		<h1><?php esc_html_e( 'This page has gone to the cutting room.', 'hadhramifashion' ); ?></h1>
		<p class="lede"><?php esc_html_e( 'The page you were looking for is not here — but your next suit might be.', 'hadhramifashion' ); ?></p>
		<p><a class="btn" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Return to the house', 'hadhramifashion' ); ?></a></p>
	</div>
</section>
<?php
get_footer();
