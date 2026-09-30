<?php
/**
 * 404 page.
 *
 * @package Zobo
 */

get_header();
?>

<section class="not-found">
	<div class="container">
		<h1 class="page-title">404</h1>
		<p><?php esc_html_e( 'This page never made it past the idea stage. Let’s get you back on track.', 'zobo-d2c' ); ?></p>
		<a class="btn btn-primary btn-lg" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Back to home', 'zobo-d2c' ); ?></a>
	</div>
</section>

<?php
get_footer();
