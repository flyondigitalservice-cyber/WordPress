<?php
/**
 * Product carousel. Args: prefix (featured|bestseller) or explicit source/title/count.
 *
 * @package Dastaan
 */

defined( 'ABSPATH' ) || exit;

if ( ! dastaan_has_wc() ) {
	return;
}
$dastaan_prefix = isset( $args['prefix'] ) ? $args['prefix'] : '';
$dastaan_force  = ! empty( $args['force'] );
if ( $dastaan_prefix ) {
	if ( ! $dastaan_force && ! dastaan_option( $dastaan_prefix . '_enable' ) ) {
		return;
	}
	$dastaan_source   = dastaan_option( $dastaan_prefix . '_source' );
	$dastaan_category = dastaan_option( $dastaan_prefix . '_category' );
	$dastaan_count    = (int) dastaan_option( $dastaan_prefix . '_count' );
	$dastaan_title    = dastaan_option( $dastaan_prefix . '_title' );
	$dastaan_eyebrow  = dastaan_option( $dastaan_prefix . '_eyebrow' );
	$dastaan_link     = 'category' === $dastaan_source && $dastaan_category ? get_term_link( $dastaan_category, 'product_cat' ) : wc_get_page_permalink( 'shop' );
	$dastaan_link     = is_wp_error( $dastaan_link ) ? wc_get_page_permalink( 'shop' ) : $dastaan_link;
	$dastaan_link_txt = dastaan_option( 'featured_link_text' );
} else {
	$dastaan_source   = isset( $args['source'] ) ? $args['source'] : 'recent';
	$dastaan_category = isset( $args['category'] ) ? $args['category'] : '';
	$dastaan_count    = isset( $args['count'] ) ? (int) $args['count'] : 8;
	$dastaan_title    = isset( $args['title'] ) ? $args['title'] : '';
	$dastaan_eyebrow  = isset( $args['eyebrow'] ) ? $args['eyebrow'] : '';
	$dastaan_link     = isset( $args['link'] ) ? $args['link'] : '';
	$dastaan_link_txt = isset( $args['link_text'] ) ? $args['link_text'] : '';
}
$dastaan_q = dastaan_products_query( $dastaan_source, $dastaan_count, $dastaan_category );
if ( ! $dastaan_q || ! $dastaan_q->have_posts() ) {
	return;
}
?>
<section class="section product-carousel">
	<div class="container">
		<?php dastaan_section_heading( array( 'eyebrow' => $dastaan_eyebrow, 'title' => $dastaan_title, 'link' => $dastaan_link, 'link_text' => $dastaan_link_txt, 'align' => 'split' ) ); ?>
		<div class="carousel" data-carousel>
			<button class="carousel__arrow carousel__arrow--prev" type="button" aria-label="<?php esc_attr_e( 'Scroll left', 'dastaan' ); ?>"><?php dastaan_the_icon( 'chevron-left', array( 'size' => 20 ) ); ?></button>
			<ul class="products carousel__track columns-4">
				<?php
				while ( $dastaan_q->have_posts() ) {
					$dastaan_q->the_post();
					wc_get_template_part( 'content', 'product' );
				}
				wp_reset_postdata();
				?>
			</ul>
			<button class="carousel__arrow carousel__arrow--next" type="button" aria-label="<?php esc_attr_e( 'Scroll right', 'dastaan' ); ?>"><?php dastaan_the_icon( 'chevron-right', array( 'size' => 20 ) ); ?></button>
		</div>
	</div>
</section>
