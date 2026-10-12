<?php
/**
 * Single product wrapper.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package Dastaan
 * @version 1.6.4
 */

defined( 'ABSPATH' ) || exit;

get_header( 'shop' );
?>
<div class="product-page">
	<div class="container">
		<?php dastaan_breadcrumbs(); ?>
		<?php woocommerce_output_all_notices(); ?>
		<?php do_action( 'woocommerce_before_main_content' ); ?>
		<?php
		while ( have_posts() ) :
			the_post();
			wc_get_template_part( 'content', 'single-product' );
		endwhile;
		?>
		<?php do_action( 'woocommerce_after_main_content' ); ?>
	</div>
</div>
<?php
get_footer( 'shop' );
