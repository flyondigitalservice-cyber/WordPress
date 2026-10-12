<?php
/**
 * Shop / category archive.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package Dastaan
 * @version 8.6.0
 */

defined( 'ABSPATH' ) || exit;

get_header( 'shop' );

$dastaan_term        = is_product_taxonomy() ? get_queried_object() : null;
$dastaan_banner      = $dastaan_term && 'product_cat' === $dastaan_term->taxonomy ? dastaan_category_image( $dastaan_term, 'dastaan-hero' ) : '';
$dastaan_description = '';
if ( $dastaan_term ) {
	$dastaan_description = term_description();
} elseif ( is_shop() ) {
	$dastaan_shop_page   = get_post( wc_get_page_id( 'shop' ) );
	$dastaan_description = $dastaan_shop_page ? apply_filters( 'the_content', $dastaan_shop_page->post_content ) : '';
}
$dastaan_show_filters = dastaan_option( 'shop_show_sidebar' ) && is_active_sidebar( 'shop-sidebar' );
$dastaan_subcats      = $dastaan_term ? get_terms( array( 'taxonomy' => 'product_cat', 'parent' => $dastaan_term->term_id, 'hide_empty' => true ) ) : array();
?>
<div class="shop-page">
	<header class="shop-hero<?php echo $dastaan_banner ? ' shop-hero--image' : ''; ?>">
		<?php if ( $dastaan_banner ) : ?>
			<div class="shop-hero__media"><img src="<?php echo esc_url( $dastaan_banner ); ?>" alt="" loading="eager" fetchpriority="high" decoding="async"></div>
		<?php endif; ?>
		<div class="container shop-hero__content">
			<?php dastaan_breadcrumbs(); ?>
			<h1 class="shop-hero__title"><?php woocommerce_page_title(); ?></h1>
			<?php if ( $dastaan_description && $dastaan_term ) : ?>
				<div class="shop-hero__text"><?php echo wp_kses_post( wp_trim_words( wp_strip_all_tags( $dastaan_description ), 40 ) ); ?></div>
			<?php endif; ?>
			<?php if ( $dastaan_subcats && ! is_wp_error( $dastaan_subcats ) ) : ?>
				<ul class="shop-hero__chips">
					<?php foreach ( $dastaan_subcats as $dastaan_sc ) : ?>
						<li><a href="<?php echo esc_url( get_term_link( $dastaan_sc ) ); ?>"><?php echo esc_html( $dastaan_sc->name ); ?></a></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>
	</header>

	<div class="container shop-body">
		<?php woocommerce_output_all_notices(); ?>
		<?php do_action( 'woocommerce_before_main_content' ); ?>

		<?php if ( woocommerce_product_loop() ) : ?>
			<div class="shop-toolbar">
				<div class="shop-toolbar__left">
					<?php if ( $dastaan_show_filters ) : ?>
						<button class="btn btn--ghost btn--small js-open-filters" type="button" aria-controls="shop-filters" aria-expanded="false"><?php dastaan_the_icon( 'filter', array( 'size' => 16 ) ); ?><span><?php esc_html_e( 'Filters', 'dastaan' ); ?></span></button>
					<?php endif; ?>
					<?php woocommerce_result_count(); ?>
				</div>
				<div class="shop-toolbar__right">
					<?php woocommerce_catalog_ordering(); ?>
					<div class="grid-switch" role="group" aria-label="<?php esc_attr_e( 'Grid density', 'dastaan' ); ?>">
						<button type="button" class="grid-switch__btn is-active" data-cols="auto" aria-label="<?php esc_attr_e( 'Comfortable grid', 'dastaan' ); ?>"><?php dastaan_the_icon( 'grid', array( 'size' => 16 ) ); ?></button>
						<button type="button" class="grid-switch__btn" data-cols="dense" aria-label="<?php esc_attr_e( 'Dense grid', 'dastaan' ); ?>"><?php dastaan_the_icon( 'menu', array( 'size' => 16 ) ); ?></button>
					</div>
				</div>
			</div>

			<?php do_action( 'woocommerce_before_shop_loop' ); ?>

			<?php woocommerce_product_loop_start(); ?>
			<?php
			if ( wc_get_loop_prop( 'total' ) ) {
				while ( have_posts() ) {
					the_post();
					do_action( 'woocommerce_shop_loop' );
					wc_get_template_part( 'content', 'product' );
				}
			}
			?>
			<?php woocommerce_product_loop_end(); ?>

			<?php do_action( 'woocommerce_after_shop_loop' ); ?>
		<?php else : ?>
			<?php do_action( 'woocommerce_no_products_found' ); ?>
		<?php endif; ?>

		<?php do_action( 'woocommerce_after_main_content' ); ?>

		<?php if ( $dastaan_description && ( is_shop() || is_paged() === false ) ) : ?>
			<div class="shop-seo entry-content"><?php echo wp_kses_post( $dastaan_description ); ?></div>
		<?php endif; ?>
	</div>

	<?php if ( $dastaan_show_filters ) : ?>
		<div id="shop-filters" class="drawer drawer--left filters-drawer" aria-hidden="true" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Filters', 'dastaan' ); ?>">
			<div class="drawer__panel">
				<div class="drawer__head">
					<h2 class="drawer__title"><?php esc_html_e( 'Filter & refine', 'dastaan' ); ?></h2>
					<button class="icon-btn js-close-drawer" type="button" aria-label="<?php esc_attr_e( 'Close filters', 'dastaan' ); ?>"><?php dastaan_the_icon( 'close', array( 'size' => 24 ) ); ?></button>
				</div>
				<div class="drawer__body filters-body"><?php dynamic_sidebar( 'shop-sidebar' ); ?></div>
			</div>
			<button class="drawer__backdrop js-close-drawer" type="button" tabindex="-1" aria-label="<?php esc_attr_e( 'Close', 'dastaan' ); ?>"></button>
		</div>
	<?php endif; ?>
</div>
<?php
get_footer( 'shop' );
