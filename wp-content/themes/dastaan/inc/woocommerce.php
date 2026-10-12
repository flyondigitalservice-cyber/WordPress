<?php
/**
 * WooCommerce integration: loop, single product, mini-cart, AJAX add-to-bag, Buy Now.
 *
 * @package Dastaan
 */

defined( 'ABSPATH' ) || exit;

/* -------------------------------------------------------------------------
 * Global clean-up: theme templates render these themselves.
 * ---------------------------------------------------------------------- */
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );
remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_output_all_notices', 10 );
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );
remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10 );
remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10 );
remove_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10 );
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5 );
remove_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10 );
remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );

remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50 );
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );

/* -------------------------------------------------------------------------
 * Catalogue settings
 * ---------------------------------------------------------------------- */
add_filter(
	'loop_shop_columns',
	function () {
		return (int) dastaan_option( 'shop_columns' );
	}
);
add_filter(
	'loop_shop_per_page',
	function () {
		return (int) dastaan_option( 'shop_per_page' );
	},
	20
);
add_filter(
	'woocommerce_output_related_products_args',
	function ( $args ) {
		$args['posts_per_page'] = (int) dastaan_option( 'product_related_count' );
		$args['columns']        = 4;
		return $args;
	}
);
add_filter( 'woocommerce_product_thumbnails_columns', fn() => 5 );
add_filter( 'woocommerce_gallery_thumbnail_size', fn() => array( 160, 213 ) );
add_filter( 'woocommerce_gallery_image_size', fn() => 'woocommerce_single' );
add_filter( 'woocommerce_breadcrumb_defaults', fn( $d ) => array_merge( $d, array( 'delimiter' => '<span class="sep">/</span>' ) ) );

/* -------------------------------------------------------------------------
 * Badges
 * ---------------------------------------------------------------------- */

/**
 * Product badges markup.
 *
 * @param WC_Product $product Product.
 * @return string
 */
function dastaan_product_badges( $product ) {
	$badges = array();
	if ( ! $product->is_in_stock() ) {
		$badges[] = '<span class="badge badge--soldout">' . esc_html__( 'Sold out', 'dastaan' ) . '</span>';
	} elseif ( $product->is_on_sale() && dastaan_option( 'shop_sale_badge' ) ) {
		$regular = (float) $product->get_regular_price();
		$sale    = (float) $product->get_sale_price();
		if ( $product->is_type( 'variable' ) ) {
			$regular = (float) $product->get_variation_regular_price( 'max' );
			$sale    = (float) $product->get_variation_sale_price( 'min' );
		}
		$pct = ( $regular > 0 && $sale > 0 ) ? round( ( ( $regular - $sale ) / $regular ) * 100 ) : 0;
		$badges[] = '<span class="badge badge--sale">' . ( $pct ? esc_html( sprintf( /* translators: %d: percentage */ __( '%d%% off', 'dastaan' ), $pct ) ) : esc_html__( 'Sale', 'dastaan' ) ) . '</span>';
	}
	$days = (int) dastaan_option( 'shop_new_badge_days' );
	if ( $days > 0 && $product->get_date_created() && ( time() - $product->get_date_created()->getTimestamp() ) < $days * DAY_IN_SECONDS ) {
		$badges[] = '<span class="badge badge--new">' . esc_html__( 'New', 'dastaan' ) . '</span>';
	}
	if ( $product->managing_stock() && $product->is_in_stock() && (int) $product->get_stock_quantity() === 1 ) {
		$badges[] = '<span class="badge badge--limited">' . esc_html__( 'One of one', 'dastaan' ) . '</span>';
	}
	$badges = apply_filters( 'dastaan_product_badges', $badges, $product );

	$badges = apply_filters( 'dastaan_badges', $badges, $product );
	return $badges ? '<div class="product-badges">' . implode( '', $badges ) . '</div>' : '';
}

/* -------------------------------------------------------------------------
 * Wishlist (plugin agnostic)
 * ---------------------------------------------------------------------- */

/**
 * Wishlist button for a product — uses YITH / TI / WPC shortcodes when present.
 *
 * @param int $product_id Product ID.
 */
function dastaan_wishlist_button( $product_id ) {
	$html = '';
	if ( shortcode_exists( 'ti_wishlists_addtowishlist' ) ) {
		$html = do_shortcode( '[ti_wishlists_addtowishlist product_id="' . absint( $product_id ) . '"]' );
	} elseif ( shortcode_exists( 'yith_wcwl_add_to_wishlist' ) ) {
		$html = do_shortcode( '[yith_wcwl_add_to_wishlist product_id="' . absint( $product_id ) . '"]' );
	} elseif ( shortcode_exists( 'woosw' ) ) {
		$html = do_shortcode( '[woosw id="' . absint( $product_id ) . '"]' );
	}
	$html = apply_filters( 'dastaan_wishlist_button_html', $html, $product_id );
	if ( $html ) {
		echo '<div class="wishlist-btn-wrap">' . $html . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- plugin output.
	}
}

/* -------------------------------------------------------------------------
 * Single product hooks
 * ---------------------------------------------------------------------- */

/**
 * Badges + category above title.
 */
function dastaan_single_pre_title() {
	global $product;
	$cats = wc_get_product_terms( $product->get_id(), 'product_cat', array( 'orderby' => 'parent', 'order' => 'DESC' ) );
	echo '<div class="summary-top">';
	if ( $cats ) {
		echo '<a class="summary-cat" href="' . esc_url( get_term_link( $cats[0] ) ) . '">' . esc_html( $cats[0]->name ) . '</a>';
	}
	echo dastaan_product_badges( $product ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo '</div>';
}
add_action( 'woocommerce_single_product_summary', 'dastaan_single_pre_title', 4 );

/**
 * Spec list from visible attributes, shown under the short description.
 */
function dastaan_single_specs() {
	global $product;
	$attrs = $product->get_attributes();
	$rows  = array();
	foreach ( $attrs as $attr ) {
		if ( ! $attr instanceof WC_Product_Attribute || ! $attr->get_visible() || $attr->get_variation() ) {
			continue;
		}
		$values = $attr->is_taxonomy() ? wp_list_pluck( $attr->get_terms() ? $attr->get_terms() : array(), 'name' ) : $attr->get_options();
		if ( empty( $values ) ) {
			continue;
		}
		$rows[] = array( wc_attribute_label( $attr->get_name(), $product ), implode( ', ', array_map( 'wp_strip_all_tags', $values ) ) );
	}
	if ( ! $rows ) {
		return;
	}
	echo '<dl class="product-specs">';
	foreach ( array_slice( $rows, 0, 6 ) as $r ) {
		echo '<div class="product-specs__row"><dt>' . esc_html( $r[0] ) . '</dt><dd>' . esc_html( $r[1] ) . '</dd></div>';
	}
	echo '</dl>';
}
add_action( 'woocommerce_single_product_summary', 'dastaan_single_specs', 25 );

/**
 * Buy now + WhatsApp + wishlist after the add-to-cart form.
 */
function dastaan_single_after_atc() {
	global $product;
	echo '<div class="summary-actions">';
	if ( dastaan_option( 'product_show_whatsapp' ) && dastaan_whatsapp_url() ) {
		$msg = sprintf( /* translators: 1: product name, 2: url */ __( 'Hi Dastaan, I have a question about "%1$s" — %2$s', 'dastaan' ), $product->get_name(), get_permalink( $product->get_id() ) );
		echo '<a class="btn btn--whatsapp btn--block" href="' . esc_url( dastaan_whatsapp_url( $msg ) ) . '" target="_blank" rel="noopener">' . dastaan_icon( 'whatsapp', array( 'size' => 18 ) ) . '<span>' . esc_html( dastaan_option( 'product_whatsapp_text' ) ) . '</span></a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
	dastaan_wishlist_button( $product->get_id() );
	echo '</div>';

	// Delivery + trust.
	$delivery = dastaan_option( 'product_delivery_text' );
	echo '<div class="summary-trust">';
	if ( $delivery ) {
		echo '<p class="delivery-line">' . dastaan_icon( 'truck', array( 'size' => 18 ) ) . '<span>' . esc_html( $delivery ) . '</span></p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
	echo '<ul class="trust-list">';
	foreach ( array( 'product_trust_1' => 'hand', 'product_trust_2' => 'lock', 'product_trust_3' => 'exchange' ) as $key => $icon ) {
		$t = dastaan_option( $key );
		if ( $t ) {
			echo '<li>' . dastaan_icon( $icon, array( 'size' => 16 ) ) . '<span>' . esc_html( $t ) . '</span></li>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}
	echo '</ul></div>';

	// Size guide + share.
	echo '<div class="summary-utils">';
	$guide = (int) dastaan_option( 'product_size_guide_page' );
	if ( $guide ) {
		echo '<a class="util-link" href="' . esc_url( get_permalink( $guide ) ) . '">' . dastaan_icon( 'ruler', array( 'size' => 16 ) ) . esc_html__( 'Measurement & stitching guide', 'dastaan' ) . '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
	echo '<button type="button" class="util-link js-share" data-title="' . esc_attr( $product->get_name() ) . '" data-url="' . esc_url( get_permalink( $product->get_id() ) ) . '">' . dastaan_icon( 'share', array( 'size' => 16 ) ) . esc_html__( 'Share', 'dastaan' ) . '</button>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo '</div>';

	if ( is_active_sidebar( 'product-after' ) ) {
		echo '<div class="summary-widgets">';
		dynamic_sidebar( 'product-after' );
		echo '</div>';
	}
}
add_action( 'woocommerce_single_product_summary', 'dastaan_single_after_atc', 35 );

/**
 * Buy now button inside the add-to-cart form.
 */
function dastaan_buy_now_button() {
	global $product;
	if ( ! dastaan_option( 'product_show_buynow' ) || ! $product->is_purchasable() || ! $product->is_in_stock() ) {
		return;
	}
	echo '<button type="submit" name="dastaan_buy_now" value="1" class="btn btn--outline btn--buynow">' . esc_html__( 'Buy now', 'dastaan' ) . '</button>';
}
add_action( 'woocommerce_after_add_to_cart_button', 'dastaan_buy_now_button' );

/**
 * Buy now → skip the cart.
 *
 * @param string $url URL.
 * @return string
 */
function dastaan_buy_now_redirect( $url ) {
	if ( isset( $_REQUEST['dastaan_buy_now'] ) && '1' === $_REQUEST['dastaan_buy_now'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- WC add-to-cart is nonce-less by design; flag only changes the redirect.
		return wc_get_checkout_url();
	}

	return $url;
}
add_filter( 'woocommerce_add_to_cart_redirect', 'dastaan_buy_now_redirect' );

/**
 * Meta (SKU / tags) at the bottom of the summary, compact.
 */
function dastaan_single_meta() {
	global $product;
	$sku = $product->get_sku();
	echo '<div class="summary-meta">';
	if ( $sku ) {
		echo '<span class="meta-item"><span class="meta-label">' . esc_html__( 'Design code', 'dastaan' ) . '</span> ' . esc_html( $sku ) . '</span>';
	}
	echo wc_get_product_tag_list( $product->get_id(), ', ', '<span class="meta-item"><span class="meta-label">' . esc_html__( 'Crafts', 'dastaan' ) . '</span> ', '</span>' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo '</div>';
}
add_action( 'woocommerce_single_product_summary', 'dastaan_single_meta', 60 );

/**
 * Accordion (replaces tabs) + related products after the summary.
 */
function dastaan_single_after_summary() {
	get_template_part( 'template-parts/product/accordion' );
}
add_action( 'woocommerce_after_single_product_summary', 'dastaan_single_after_summary', 10 );

function dastaan_single_related() {
	echo '<div class="related-wrap">';
	woocommerce_upsell_display();
	woocommerce_output_related_products();
	echo '</div>';
}
add_action( 'woocommerce_after_single_product_summary', 'dastaan_single_related', 20 );

/**
 * Related products heading copy.
 */
add_filter( 'woocommerce_product_related_products_heading', fn() => __( 'You may also love', 'dastaan' ) );
add_filter( 'woocommerce_product_upsells_products_heading', fn() => __( 'Complete the look', 'dastaan' ) );

/**
 * Stock copy for one-of-one pieces.
 *
 * @param string     $html    HTML.
 * @param WC_Product $product Product.
 * @return string
 */
function dastaan_stock_html( $html, $product ) {
	if ( $product->managing_stock() && $product->is_in_stock() ) {
		$qty = (int) $product->get_stock_quantity();
		if ( 1 === $qty ) {
			return '<p class="stock in-stock stock--limited">' . dastaan_icon( 'sparkle', array( 'size' => 14 ) ) . esc_html__( 'Only 1 piece — handcrafted, one of one', 'dastaan' ) . '</p>';
		}
		if ( $qty <= 3 ) {
			return '<p class="stock in-stock stock--limited">' . esc_html( sprintf( /* translators: %d: quantity */ _n( 'Only %d piece left', 'Only %d pieces left', $qty, 'dastaan' ), $qty ) ) . '</p>';
		}
	}

	return $html;
}
add_filter( 'woocommerce_get_stock_html', 'dastaan_stock_html', 10, 2 );

/* -------------------------------------------------------------------------
 * Mini cart drawer + fragments
 * ---------------------------------------------------------------------- */

/**
 * Cart count fragment.
 *
 * @param array $fragments Fragments.
 * @return array
 */
function dastaan_cart_fragments( $fragments ) {
	$count = WC()->cart ? WC()->cart->get_cart_contents_count() : 0;
	$fragments['.header-cart-count'] = '<span class="header-cart-count' . ( $count ? ' has-items' : '' ) . '">' . esc_html( $count ) . '</span>';
	ob_start();
	woocommerce_mini_cart();
	$fragments['div.mini-cart-drawer__content'] = '<div class="mini-cart-drawer__content">' . ob_get_clean() . '</div>';

	return $fragments;
}
add_filter( 'woocommerce_add_to_cart_fragments', 'dastaan_cart_fragments' );

/**
 * Free shipping progress inside the mini cart.
 */
function dastaan_mini_cart_progress() {
	if ( ! WC()->cart || WC()->cart->is_empty() ) {
		return;
	}
	$threshold = (float) apply_filters( 'dastaan_free_shipping_threshold', 0 );
	if ( $threshold <= 0 ) {
		return;
	}
	$subtotal = (float) WC()->cart->get_subtotal();
	$pct      = min( 100, round( ( $subtotal / $threshold ) * 100 ) );
	echo '<div class="mini-cart-shipping"><p>';
	if ( $subtotal >= $threshold ) {
		esc_html_e( 'You have unlocked free shipping.', 'dastaan' );
	} else {
		echo wp_kses_post( sprintf( /* translators: %s: amount */ __( 'Add %s more for free shipping', 'dastaan' ), wc_price( $threshold - $subtotal ) ) );
	}
	echo '</p><span class="bar"><span style="width:' . esc_attr( $pct ) . '%"></span></span></div>';
}
add_action( 'woocommerce_before_mini_cart', 'dastaan_mini_cart_progress' );

/**
 * Enable AJAX add to cart on archives even if the option is off in WC settings.
 */
add_filter( 'option_woocommerce_enable_ajax_add_to_cart', fn() => 'yes' );

/**
 * Single product AJAX add-to-cart endpoint (keeps the user on the page and opens the drawer).
 */
function dastaan_ajax_add_to_cart() {
	dastaan_verify_front_nonce();

	$product_id   = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;
	$variation_id = isset( $_POST['variation_id'] ) ? absint( $_POST['variation_id'] ) : 0;
	$quantity     = isset( $_POST['quantity'] ) ? wc_stock_amount( wp_unslash( $_POST['quantity'] ) ) : 1; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	$variation    = array();
	foreach ( $_POST as $key => $value ) {
		if ( 0 === strpos( $key, 'attribute_' ) ) {
			$variation[ sanitize_title( wp_unslash( $key ) ) ] = sanitize_text_field( wp_unslash( $value ) );
		}
	}
	if ( ! $product_id ) {
		wp_send_json_error( array( 'message' => __( 'Invalid product.', 'dastaan' ) ), 400 );
	}
	$passed = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity, $variation_id, $variation );
	if ( ! $passed ) {
		$notices = wc_get_notices( 'error' );
		wc_clear_notices();
		wp_send_json_error( array( 'message' => $notices ? wp_strip_all_tags( $notices[0]['notice'] ) : __( 'Could not add to bag.', 'dastaan' ) ), 400 );
	}
	$key = WC()->cart->add_to_cart( $product_id, $quantity, $variation_id, $variation );
	if ( ! $key ) {
		$notices = wc_get_notices( 'error' );
		wc_clear_notices();
		wp_send_json_error( array( 'message' => $notices ? wp_strip_all_tags( $notices[0]['notice'] ) : __( 'Could not add to bag.', 'dastaan' ) ), 400 );
	}
	wc_clear_notices();
	do_action( 'woocommerce_ajax_added_to_cart', $product_id );

	$product = wc_get_product( $variation_id ? $variation_id : $product_id );
	wp_send_json_success(
		array(
			'fragments' => apply_filters( 'woocommerce_add_to_cart_fragments', array() ),
			'cart_hash' => WC()->cart->get_cart_hash(),
			'item'      => $product ? array(
				'name'  => $product->get_name(),
				'price' => wc_get_price_to_display( $product ),
				'sku'   => $product->get_sku() ? $product->get_sku() : $product->get_id(),
				'qty'   => $quantity,
			) : null,
		)
	);
}
add_action( 'wp_ajax_dastaan_add_to_cart', 'dastaan_ajax_add_to_cart' );
add_action( 'wp_ajax_nopriv_dastaan_add_to_cart', 'dastaan_ajax_add_to_cart' );

/* -------------------------------------------------------------------------
 * Misc copy & behaviour
 * ---------------------------------------------------------------------- */
add_filter( 'woocommerce_product_single_add_to_cart_text', fn() => __( 'Add to bag', 'dastaan' ) );
add_filter(
	'woocommerce_product_add_to_cart_text',
	function ( $text, $product ) {
		if ( $product->is_type( 'simple' ) && $product->is_purchasable() && $product->is_in_stock() ) {
			return __( 'Add to bag', 'dastaan' );
		}
		if ( $product->is_type( 'variable' ) ) {
			return __( 'Choose options', 'dastaan' );
		}
		return $text;
	},
	10,
	2
);
add_filter( 'woocommerce_order_button_text', fn() => __( 'Place order securely', 'dastaan' ) );
add_filter( 'woocommerce_sale_flash', '__return_empty_string' );
add_filter( 'woocommerce_show_page_title', '__return_false' );
add_filter( 'woocommerce_cart_item_thumbnail', fn( $img ) => $img ); // Hook for plugins.

/**
 * Empty cart copy.
 */
add_action(
	'woocommerce_cart_is_empty',
	function () {
		echo '<div class="empty-state">' . dastaan_icon( 'cart-empty', array( 'size' => 48 ) ) . '<h2>' . esc_html__( 'Your bag is empty', 'dastaan' ) . '</h2><p>' . esc_html__( 'Discover handcrafted unstitched sets, each one a story.', 'dastaan' ) . '</p><a class="btn btn--primary" href="' . esc_url( wc_get_page_permalink( 'shop' ) ) . '">' . esc_html__( 'Start exploring', 'dastaan' ) . '</a></div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	},
	5
);
remove_action( 'woocommerce_cart_is_empty', 'wc_empty_cart_message', 10 );

/**
 * Shop category banner description position: we render it in archive template, so stop WC from printing it twice.
 */
remove_action( 'woocommerce_archive_description', 'woocommerce_taxonomy_archive_description', 10 );
remove_action( 'woocommerce_archive_description', 'woocommerce_product_archive_description', 10 );

/**
 * Search form: products first.
 *
 * @param WP_Query $q Query.
 */
function dastaan_search_products( $q ) {
	if ( ! is_admin() && $q->is_main_query() && $q->is_search() && dastaan_has_wc() && empty( $q->get( 'post_type' ) ) ) {
		$q->set( 'post_type', array( 'product', 'post', 'page' ) );
	}
}
add_action( 'pre_get_posts', 'dastaan_search_products' );

/**
 * Cart: move cross-sells below the two-column (items | totals) layout.
 */
function dastaan_move_cross_sells() {
	remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cross_sell_display' );
	add_action( 'woocommerce_after_cart', 'woocommerce_cross_sell_display', 5 );
}
add_action( 'init', 'dastaan_move_cross_sells' );
