<?php
/**
 * WooCommerce integration.
 *
 * @package FlavorKit
 */

defined( 'ABSPATH' ) || exit;

/*
 * Layout wrappers: use the theme container, drop the default sidebar.
 */
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );

add_action(
	'woocommerce_before_main_content',
	function () {
		echo '<main id="primary" class="fk-main fk-shop"><div class="fk-container">';
	},
	10
);
add_action(
	'woocommerce_after_main_content',
	function () {
		echo '</div></main>';
	},
	10
);

/**
 * Breadcrumb markup.
 *
 * @return array
 */
add_filter(
	'woocommerce_breadcrumb_defaults',
	function ( $args ) {
		$args['delimiter']   = '<span class="fk-crumb-sep" aria-hidden="true">/</span>';
		$args['wrap_before'] = '<nav class="woocommerce-breadcrumb fk-breadcrumb" aria-label="' . esc_attr__( 'Breadcrumb', 'flavorkit' ) . '">';
		return $args;
	}
);

// Products per row / page.
add_filter( 'loop_shop_columns', fn() => 4 );
add_filter( 'loop_shop_per_page', fn() => 12 );
add_filter(
	'woocommerce_output_related_products_args',
	function ( $args ) {
		$args['posts_per_page'] = 4;
		$args['columns']        = 4;
		return $args;
	}
);
add_filter( 'woocommerce_upsell_display_args', fn( $args ) => array_merge( $args, array( 'columns' => 4 ) ) );
add_filter( 'woocommerce_cross_sells_columns', fn() => 4 );

/**
 * Show the Shop filters widget area above the grid when it has widgets.
 */
add_action(
	'woocommerce_before_shop_loop',
	function () {
		if ( is_active_sidebar( 'shop-filters' ) ) {
			echo '<div class="fk-shop-filters">';
			dynamic_sidebar( 'shop-filters' );
			echo '</div>';
		}
	},
	5
);

/**
 * Header cart count markup (also used as an AJAX fragment).
 *
 * @return string
 */
function flavorkit_cart_count_html() {
	$count = WC()->cart ? WC()->cart->get_cart_contents_count() : 0;
	return '<span class="fk-cart-count' . ( $count ? '' : ' is-empty' ) . '">' . esc_html( $count ) . '</span>';
}

add_filter(
	'woocommerce_add_to_cart_fragments',
	function ( $fragments ) {
		$fragments['span.fk-cart-count'] = flavorkit_cart_count_html();
		return $fragments;
	}
);

/**
 * Free-shipping progress bar inside the cart drawer.
 * Uses the minimum amount of the first enabled free_shipping method.
 */
function flavorkit_free_shipping_threshold() {
	$cached = get_transient( 'flavorkit_free_ship_min' );
	if ( false !== $cached ) {
		return (float) $cached;
	}
	$min = 0;
	if ( class_exists( 'WC_Shipping_Zones' ) ) {
		foreach ( WC_Shipping_Zones::get_zones() as $zone ) {
			foreach ( $zone['shipping_methods'] as $method ) {
				if ( 'free_shipping' === $method->id && 'yes' === $method->enabled && ! empty( $method->min_amount ) ) {
					$min = (float) $method->min_amount;
					break 2;
				}
			}
		}
	}
	set_transient( 'flavorkit_free_ship_min', $min, HOUR_IN_SECONDS );
	return $min;
}

/**
 * Print the free-shipping meter.
 */
function flavorkit_free_shipping_meter() {
	$min = flavorkit_free_shipping_threshold();
	if ( ! $min || ! WC()->cart ) {
		return;
	}
	$total   = (float) WC()->cart->get_displayed_subtotal();
	$percent = min( 100, ( $total / $min ) * 100 );
	echo '<div class="fk-ship-meter">';
	if ( $total >= $min ) {
		echo '<p>' . esc_html__( 'Yay! You\'ve unlocked FREE shipping.', 'flavorkit' ) . '</p>';
	} else {
		/* translators: %s amount left */
		echo '<p>' . wp_kses_post( sprintf( __( 'You\'re %s away from FREE shipping', 'flavorkit' ), wc_price( $min - $total ) ) ) . '</p>';
	}
	echo '<div class="fk-ship-bar"><span style="width:' . esc_attr( round( $percent ) ) . '%"></span></div></div>';
}
add_action( 'woocommerce_before_mini_cart', 'flavorkit_free_shipping_meter' );

/**
 * Trust badges under the add-to-cart button on single products.
 */
add_action(
	'woocommerce_single_product_summary',
	function () {
		$items = array_slice( flavorkit_lines( flavorkit_mod( 'usp_items' ), 3 ), 0, 4 );
		if ( ! $items ) {
			return;
		}
		echo '<ul class="fk-product-trust">';
		foreach ( $items as $item ) {
			echo '<li>' . flavorkit_icon( $item[2] ? $item[2] : 'check', 20 ) . '<span>' . esc_html( $item[0] ) . '</span></li>'; // phpcs:ignore WordPress.Security.EscapeOutput
		}
		echo '</ul>';
	},
	35
);

/**
 * Sale badge text: show % off.
 */
add_filter(
	'woocommerce_sale_flash',
	function ( $html, $post, $product ) {
		if ( $product && $product->is_type( 'simple' ) && $product->get_regular_price() > 0 && $product->get_sale_price() !== '' ) {
			$pct = round( ( 1 - ( (float) $product->get_sale_price() / (float) $product->get_regular_price() ) ) * 100 );
			if ( $pct > 0 ) {
				return '<span class="onsale">-' . esc_html( $pct ) . '%</span>';
			}
		}
		return $html;
	},
	10,
	3
);

/**
 * Query products for the homepage grid.
 *
 * @param string $source Source key.
 * @param int    $limit  Count.
 * @return WC_Product[]
 */
function flavorkit_get_home_products( $source, $limit ) {
	$args = array(
		'status'     => 'publish',
		'limit'      => $limit,
		'visibility' => 'catalog',
	);

	switch ( $source ) {
		case 'featured':
			$args['featured'] = true;
			break;
		case 'on_sale':
			$args['include'] = wc_get_product_ids_on_sale() ? wc_get_product_ids_on_sale() : array( 0 );
			break;
		case 'top_rated':
			$args['orderby']  = 'meta_value_num';
			$args['meta_key'] = '_wc_average_rating'; // phpcs:ignore WordPress.DB.SlowDBQuery
			$args['order']    = 'DESC';
			break;
		case 'recent':
			$args['orderby'] = 'date';
			$args['order']   = 'DESC';
			break;
		case 'best_selling':
		default:
			$args['orderby']  = 'meta_value_num';
			$args['meta_key'] = 'total_sales'; // phpcs:ignore WordPress.DB.SlowDBQuery
			$args['order']    = 'DESC';
			break;
	}

	$products = wc_get_products( $args );

	// Featured/sale may legitimately be empty: fall back to newest.
	if ( ! $products && 'recent' !== $source ) {
		$products = wc_get_products(
			array(
				'status'     => 'publish',
				'limit'      => $limit,
				'visibility' => 'catalog',
			)
		);
	}
	return $products;
}

/**
 * Clear the free-shipping cache when shipping settings change.
 */
add_action( 'woocommerce_update_options_shipping', fn() => delete_transient( 'flavorkit_free_ship_min' ) );
add_action( 'woocommerce_shipping_zone_method_status_toggled', fn() => delete_transient( 'flavorkit_free_ship_min' ) );

/**
 * Brand-coloured pack illustration for a product without a photo.
 *
 * The shape follows the product category (Beverages → can, Spices → jar …)
 * so imported demo products look right before real photography exists.
 *
 * @param WC_Product $product Product.
 * @return string SVG markup.
 */
function flavorkit_product_pack( $product ) {
	$id    = $product->get_parent_id() ? $product->get_parent_id() : $product->get_id();
	$name  = $product->get_parent_id() ? get_the_title( $id ) : $product->get_name();
	$shape = '';
	$map   = array(
		'kombucha|juice|bottle|sauce' => 'bottle',
		'beverage|drink|soda|juice'   => 'can',
		'breakfast|cereal|granola|muesli|box|gift' => 'box',
		'spice|masala|pickle|jar'     => 'jar',
		'snack|chip|makhana|combo'    => 'pouch',
	);
	$haystack = strtolower( $name . ' ' . implode( ' ', wp_get_post_terms( $id, 'product_cat', array( 'fields' => 'slugs' ) ) ) );
	foreach ( $map as $pattern => $candidate ) {
		if ( preg_match( '/' . $pattern . '/', $haystack ) ) {
			$shape = $candidate;
			break;
		}
	}
	if ( ! $shape ) {
		$shapes = array( 'can', 'pouch', 'jar', 'bottle', 'box' );
		$shape  = $shapes[ $id % 5 ];
	}
	$words = preg_split( '/\s+/', strtoupper( wp_strip_all_tags( $name ) ) );
	return flavorkit_pack_svg( $shape, $id, $words[0], implode( ' ', array_slice( $words, 1, 2 ) ) );
}

/**
 * Single product: replace the grey placeholder with the pack illustration.
 */
add_filter(
	'woocommerce_single_product_image_thumbnail_html',
	function ( $html, $attachment_id ) {
		global $product;
		if ( ! $attachment_id && $product instanceof WC_Product ) {
			return '<div class="woocommerce-product-gallery__image--placeholder fk-single-pack fk-tone-' . esc_attr( $product->get_id() % 4 ) . '">' . flavorkit_product_pack( $product ) . '</div>';
		}
		return $html;
	},
	10,
	2
);

/**
 * Cart & mini-cart thumbnails for products without a photo.
 */
add_filter(
	'woocommerce_cart_item_thumbnail',
	function ( $html, $cart_item ) {
		$product = isset( $cart_item['data'] ) ? $cart_item['data'] : null;
		if ( $product instanceof WC_Product && ! $product->get_image_id() && ! ( $product->get_parent_id() && get_post_thumbnail_id( $product->get_parent_id() ) ) ) {
			return '<span class="fk-cart-pack fk-tone-' . esc_attr( $product->get_id() % 4 ) . '">' . flavorkit_product_pack( $product ) . '</span>';
		}
		return $html;
	},
	10,
	2
);
