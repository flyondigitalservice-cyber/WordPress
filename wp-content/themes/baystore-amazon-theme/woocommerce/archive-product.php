<?php
/**
 * Baystore — Shop / Category Archive
 *
 * @package Baystore_Amazon
 */

defined( 'ABSPATH' ) || exit;

get_header();

$shop_url = wc_get_page_permalink( 'shop' );
$current  = is_product_category() ? get_queried_object() : null;
$base_url = $current ? get_term_link( $current ) : $shop_url;
$prices   = array(
	'Under ₹250'    => array( 0, 250 ),
	'₹250 – ₹500'   => array( 250, 500 ),
	'₹500 – ₹1,000' => array( 500, 1000 ),
	'Over ₹1,000'   => array( 1000, 99999 ),
);
?>

<section class="bs-wrap bs-archive-head">
	<h1 class="bs-title-xl"><?php woocommerce_page_title(); ?></h1>
	<span class="bs-rule"></span>
	<?php if ( $current && $current->description ) : ?>
		<p><?php echo esc_html( $current->description ); ?></p>
	<?php endif; ?>

	<div class="bs-tabs">
		<a href="<?php echo esc_url( $shop_url ); ?>" class="bs-tab <?php echo $current ? '' : 'is-active'; ?>">All</a>
		<?php foreach ( baystore_get_departments() as $c ) : ?>
			<a href="<?php echo esc_url( get_term_link( $c ) ); ?>" class="bs-tab <?php echo ( $current && $current->term_id === $c->term_id ) ? 'is-active' : ''; ?>"><?php echo esc_html( baystore_short_cat_name( $c->name ) ); ?></a>
		<?php endforeach; ?>
	</div>
</section>

<div class="bs-wrap">
	<div class="bs-toolbar">
		<div class="bs-toolbar-left">
			<?php woocommerce_result_count(); ?>
			<div class="bs-price-chips">
				<?php foreach ( $prices as $label => $range ) : ?>
					<?php $active = isset( $_GET['min_price'] ) && (int) $_GET['min_price'] === $range[0] && isset( $_GET['max_price'] ) && (int) $_GET['max_price'] === $range[1]; // phpcs:ignore ?>
					<a class="bs-chip <?php echo $active ? 'is-active' : ''; ?>" href="<?php echo esc_url( $active ? $base_url : add_query_arg( array( 'min_price' => $range[0], 'max_price' => $range[1] ), $base_url ) ); ?>"><?php echo esc_html( $label ); ?></a>
				<?php endforeach; ?>
			</div>
		</div>
		<?php woocommerce_catalog_ordering(); ?>
	</div>

	<?php if ( woocommerce_product_loop() ) : ?>
		<div class="bs-pgrid">
			<?php
			while ( have_posts() ) {
				the_post();
				global $product;
				get_template_part( 'template-parts/product-card', null, array( 'wc_product' => $product ) );
			}
			?>
		</div>
		<div class="bs-pagination"><?php woocommerce_pagination(); ?></div>
	<?php else : ?>
		<?php do_action( 'woocommerce_no_products_found' ); ?>
	<?php endif; ?>
</div>

<?php
get_footer();
