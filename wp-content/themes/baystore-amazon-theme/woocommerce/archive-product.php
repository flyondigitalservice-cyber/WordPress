<?php
/**
 * Baystore — Shop / Category Archive (filters left, results right)
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
$orderby  = isset( $_GET['orderby'] ) ? sanitize_key( wp_unslash( $_GET['orderby'] ) ) : 'menu_order'; // phpcs:ignore
$sorts    = array(
	'menu_order' => 'Relevance',
	'popularity' => 'Popularity',
	'price'      => 'Price — Low to High',
	'price-desc' => 'Price — High to Low',
	'date'       => 'Newest First',
);
?>

<div class="bs-wrap bs-shop">
	<aside class="bs-card bs-filters">
		<h3 class="bs-filters-title">Filters</h3>

		<div class="bs-filter">
			<h4>Categories</h4>
			<a href="<?php echo esc_url( $shop_url ); ?>" class="<?php echo $current ? '' : 'is-active'; ?>">All products</a>
			<?php foreach ( baystore_get_departments() as $c ) : ?>
				<a href="<?php echo esc_url( get_term_link( $c ) ); ?>" class="<?php echo ( $current && $current->term_id === $c->term_id ) ? 'is-active' : ''; ?>"><?php echo esc_html( html_entity_decode( $c->name ) ); ?> <em>(<?php echo (int) $c->count; ?>)</em></a>
			<?php endforeach; ?>
		</div>

		<div class="bs-filter">
			<h4>Price</h4>
			<?php foreach ( $prices as $label => $range ) : ?>
				<?php $active = isset( $_GET['min_price'] ) && (int) $_GET['min_price'] === $range[0] && isset( $_GET['max_price'] ) && (int) $_GET['max_price'] === $range[1]; // phpcs:ignore ?>
				<a class="bs-check <?php echo $active ? 'is-active' : ''; ?>" href="<?php echo esc_url( $active ? $base_url : add_query_arg( array( 'min_price' => $range[0], 'max_price' => $range[1] ), $base_url ) ); ?>"><?php echo esc_html( $label ); ?></a>
			<?php endforeach; ?>
		</div>

		<div class="bs-filter">
			<h4>Offers</h4>
			<p>✓ Free delivery above ₹499</p>
			<p>✓ Cash on Delivery</p>
			<p>✓ Extra 10% off: BAY10</p>
		</div>
	</aside>

	<section class="bs-card bs-results">
		<?php woocommerce_breadcrumb(); ?>
		<div class="bs-results-head">
			<h1><?php woocommerce_page_title(); ?> <small><?php woocommerce_result_count(); ?></small></h1>
		</div>
		<div class="bs-sortbar">
			<span>Sort by</span>
			<?php foreach ( $sorts as $key => $label ) : ?>
				<a href="<?php echo esc_url( add_query_arg( 'orderby', $key ) ); ?>" class="<?php echo $orderby === $key ? 'is-active' : ''; ?>"><?php echo esc_html( $label ); ?></a>
			<?php endforeach; ?>
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
	</section>
</div>

<?php
get_footer();
