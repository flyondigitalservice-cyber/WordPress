<?php
/**
 * Product comparison without a plugin. Opt in with add_theme_support( 'bazaar-compare' ).
 *
 * The selection (max 4 product IDs) lives in the visitor's localStorage. The [bzr_compare]
 * page fetches product data from WooCommerce's public Store API and renders a spec table.
 * Product cards get a "Compare" checkbox injected by assets/js/compare.js.
 *
 * @package BazaarCore
 */

defined( 'ABSPATH' ) || exit;

/**
 * URL of the compare page (the page with slug "compare").
 *
 * @return string
 */
function bzr_compare_page_url() {
	$page = get_page_by_path( 'compare', OBJECT, 'page' );
	return $page ? get_permalink( $page ) : home_url( '/compare/' );
}

/**
 * Enqueue the compare script sitewide (small, deferred) when the feature is on.
 */
function bzr_compare_assets() {
	if ( ! bzr_feature( 'compare' ) ) {
		return;
	}
	wp_enqueue_script( 'bzr-compare', BAZAAR_CORE_URI . '/assets/js/compare.js', array(), bzr_asset_version( 'assets/js/compare.js' ), array( 'in_footer' => true, 'strategy' => 'defer' ) );

	wp_add_inline_script(
		'bzr-compare',
		'window.bzrCompare=' . wp_json_encode(
			array(
				'api'     => esc_url_raw( rest_url( 'wc/store/v1/products' ) ),
				'page'    => esc_url_raw( bzr_compare_page_url() ),
				'max'     => 4,
				'i18n'    => array(
					'compare'  => __( 'Compare', 'bazaar-core' ),
					'selected' => __( 'selected', 'bazaar-core' ),
					'clear'    => __( 'Clear', 'bazaar-core' ),
					'full'     => __( 'You can compare up to 4 products.', 'bazaar-core' ),
					'empty'    => __( 'Pick two or more products with the Compare checkbox to see them side by side.', 'bazaar-core' ),
					'browse'   => __( 'Browse products', 'bazaar-core' ),
					'shop'     => esc_url_raw( function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' ) ),
					'remove'   => __( 'Remove', 'bazaar-core' ),
					'view'     => __( 'View product', 'bazaar-core' ),
					'price'    => __( 'Price', 'bazaar-core' ),
					'rating'   => __( 'Rating', 'bazaar-core' ),
					'stock'    => __( 'Availability', 'bazaar-core' ),
					'inStock'  => __( 'In stock', 'bazaar-core' ),
					'outStock' => __( 'Out of stock', 'bazaar-core' ),
					'error'    => __( 'Could not load products. Please try again.', 'bazaar-core' ),
				),
			)
		),
		'before'
	);
}
add_action( 'wp_enqueue_scripts', 'bzr_compare_assets' );

/**
 * [bzr_compare] page container (filled by JS).
 *
 * @return string
 */
function bzr_compare_shortcode() {
	if ( ! bzr_feature( 'compare' ) ) {
		return '';
	}
	return '<div class="bzr-compare" data-bzr-compare-page aria-live="polite"><noscript>' . esc_html__( 'Please enable JavaScript to use product comparison.', 'bazaar-core' ) . '</noscript></div>';
}
add_shortcode( 'bzr_compare', 'bzr_compare_shortcode' );

/**
 * Compare button on the single-product page.
 */
function bzr_compare_button() {
	if ( ! bzr_feature( 'compare' ) ) {
		return;
	}
	global $product;
	if ( ! $product ) {
		return;
	}
	printf(
		'<button type="button" class="bzr-compare-btn" data-bzr-compare-id="%1$d" aria-pressed="false">%2$s</button>',
		(int) $product->get_id(),
		esc_html__( 'Add to compare', 'bazaar-core' )
	);
}
add_action( 'woocommerce_after_add_to_cart_form', 'bzr_compare_button', 12 );

/**
 * Sticky tray listing how many products are selected.
 */
function bzr_compare_tray() {
	if ( ! bzr_feature( 'compare' ) || is_admin() ) {
		return;
	}
	?>
	<div class="bzr-tray" data-bzr-tray hidden>
		<span data-bzr-tray-count></span>
		<a class="wp-element-button" data-bzr-tray-link href="<?php echo esc_url( bzr_compare_page_url() ); ?>"><?php esc_html_e( 'Compare', 'bazaar-core' ); ?></a>
		<button type="button" class="bzr-tray__clear" data-bzr-tray-clear><?php esc_html_e( 'Clear', 'bazaar-core' ); ?></button>
	</div>
	<?php
}
add_action( 'wp_footer', 'bzr_compare_tray' );
