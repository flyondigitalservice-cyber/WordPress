<?php
/**
 * WooCommerce enhancements. Features are opt-in per theme with add_theme_support():
 *   bazaar-swatches    Button/colour swatches for variation attributes.
 *   bazaar-size-guide  Size guide pop-up on products with a "size" attribute.
 *   bazaar-whatsapp    Floating WhatsApp button + "Order on WhatsApp" on products.
 *   bazaar-pdp-extras  Savings/stock line, delivery estimate, trust badges.
 *   bazaar-sticky-atc  Sticky add-to-cart bar on small screens.
 *   bazaar-countdown   "Deal ends in" timer for scheduled sales.
 *   bazaar-emi         "Pay in N instalments" line (only when configured).
 *
 * @package BazaarCore
 */

defined( 'ABSPATH' ) || exit;

/* -------------------------------------------------------------------------
 * Swatches
 * ---------------------------------------------------------------------- */

/**
 * Slug => hex map for colour swatches. Extend via the `bzr_swatch_colors` filter.
 *
 * @return array<string,string>
 */
function bzr_swatch_colors() {
	return apply_filters(
		'bzr_swatch_colors',
		array(
			'black'    => '#111111',
			'white'    => '#ffffff',
			'ivory'    => '#fffff0',
			'cream'    => '#f5ecd7',
			'beige'    => '#d9c7a7',
			'brown'    => '#6b4423',
			'grey'     => '#8a8a8a',
			'gray'     => '#8a8a8a',
			'red'      => '#c0262d',
			'maroon'   => '#6d1a2b',
			'burgundy' => '#6d1a2b',
			'pink'     => '#e8a0b4',
			'peach'    => '#f4b69a',
			'orange'   => '#e8832a',
			'mustard'  => '#d4a017',
			'yellow'   => '#f2c94c',
			'green'    => '#2f7d4f',
			'olive'    => '#6b6b2a',
			'teal'     => '#1e7f7f',
			'blue'     => '#2457a7',
			'navy'     => '#14213d',
			'purple'   => '#6a3d8f',
			'lavender' => '#b9a7d9',
			'gold'     => '#b08d57',
			'silver'   => '#c0c0c0',
		)
	);
}

/**
 * Replace variation <select> with accessible swatch buttons. The original
 * select stays in the DOM (visually hidden) so WooCommerce's own variation
 * logic keeps working untouched.
 *
 * @param string $html Select markup.
 * @param array  $args Dropdown args.
 * @return string
 */
function bzr_swatches_html( $html, $args ) {
	if ( ! bzr_feature( 'swatches' ) ) {
		return $html;
	}

	$options   = isset( $args['options'] ) ? (array) $args['options'] : array();
	$product   = isset( $args['product'] ) ? $args['product'] : null;
	$attribute = isset( $args['attribute'] ) ? $args['attribute'] : '';

	if ( empty( $options ) || ! $product || '' === $attribute ) {
		return $html;
	}

	$selected = isset( $args['selected'] ) ? $args['selected'] : '';
	$names    = array();

	if ( taxonomy_exists( $attribute ) ) {
		$terms = wc_get_product_terms( $product->get_id(), $attribute, array( 'fields' => 'all' ) );
		foreach ( $terms as $term ) {
			$names[ $term->slug ] = $term->name;
		}
	}

	$is_color = (bool) preg_match( '/colou?r/i', $attribute );
	$colors   = $is_color ? bzr_swatch_colors() : array();
	$buttons  = '';

	foreach ( $options as $option ) {
		$label      = isset( $names[ $option ] ) ? $names[ $option ] : $option;
		$label      = apply_filters( 'woocommerce_variation_option_name', $label, null, $attribute, $product );
		$is_current = (string) $selected === (string) $option;
		$hex        = $is_color ? ( isset( $colors[ sanitize_title( $option ) ] ) ? $colors[ sanitize_title( $option ) ] : '' ) : '';

		$buttons .= sprintf(
			'<button type="button" role="radio" class="bzr-swatch%1$s" data-value="%2$s" aria-checked="%3$s" aria-label="%4$s"%5$s%6$s>%7$s</button>',
			$hex ? ' bzr-swatch--color' : '',
			esc_attr( $option ),
			$is_current ? 'true' : 'false',
			esc_attr( $label ),
			$hex ? ' style="--bzr-swatch:' . esc_attr( $hex ) . '"' : '',
			$hex ? ' title="' . esc_attr( $label ) . '"' : '',
			$hex ? '<span class="screen-reader-text">' . esc_html( $label ) . '</span>' : esc_html( $label )
		);
	}

	return '<span class="bzr-swatches__select">' . $html . '</span>' .
		'<div class="bzr-swatches" role="radiogroup" aria-label="' . esc_attr( wc_attribute_label( $attribute ) ) . '">' . $buttons . '</div>';
}
add_filter( 'woocommerce_dropdown_variation_attribute_options_html', 'bzr_swatches_html', 20, 2 );

/* -------------------------------------------------------------------------
 * Size guide
 * ---------------------------------------------------------------------- */

/**
 * Default size chart, used when no custom HTML is saved in Store Options.
 * A generic unisex body-measurement chart; themes can replace it with the
 * `bzr_default_size_guide` filter.
 *
 * @return string
 */
function bzr_default_size_guide() {
	$rows = array(
		array( 'XS', '32-34', '26-28', '34-36' ),
		array( 'S', '34-36', '28-30', '36-38' ),
		array( 'M', '38-40', '30-32', '38-40' ),
		array( 'L', '40-42', '32-34', '40-42' ),
		array( 'XL', '42-44', '34-36', '42-44' ),
		array( 'XXL', '44-46', '36-38', '44-46' ),
	);

	$html  = '<table class="bzr-size-table"><caption>' . esc_html__( 'Body measurements in inches. Between two sizes? Choose the larger one.', 'bazaar-core' ) . '</caption><thead><tr>';
	$html .= '<th scope="col">' . esc_html__( 'Size', 'bazaar-core' ) . '</th>';
	$html .= '<th scope="col">' . esc_html__( 'Chest / Bust', 'bazaar-core' ) . '</th>';
	$html .= '<th scope="col">' . esc_html__( 'Waist', 'bazaar-core' ) . '</th>';
	$html .= '<th scope="col">' . esc_html__( 'Hip', 'bazaar-core' ) . '</th>';
	$html .= '</tr></thead><tbody>';

	foreach ( $rows as $row ) {
		$html .= '<tr><th scope="row">' . esc_html( $row[0] ) . '</th>';
		for ( $i = 1; $i < 4; $i++ ) {
			$html .= '<td>' . esc_html( $row[ $i ] ) . '</td>';
		}
		$html .= '</tr>';
	}
	$html .= '</tbody></table>';

	/**
	 * Filter the default size guide HTML (shown when Store Options has none).
	 *
	 * @param string $html Size guide markup.
	 */
	return (string) apply_filters( 'bzr_default_size_guide', $html );
}

/**
 * Print the size guide trigger + dialog above the variation form.
 */
function bzr_size_guide() {
	if ( ! bzr_feature( 'size-guide' ) ) {
		return;
	}

	global $product;
	if ( ! $product || ! $product->is_type( 'variable' ) ) {
		return;
	}

	$has_size = false;
	foreach ( array_keys( $product->get_variation_attributes() ) as $name ) {
		if ( preg_match( '/size/i', $name ) ) {
			$has_size = true;
			break;
		}
	}
	if ( ! $has_size ) {
		return;
	}

	$content = bzr_option( 'size_guide_html' );
	$content = wp_kses_post( $content ? $content : bzr_default_size_guide() );
	?>
	<button type="button" class="bzr-size-guide__open" data-bzr-dialog="bzr-size-guide">
		<?php esc_html_e( 'Size guide', 'bazaar-core' ); ?>
	</button>
	<dialog id="bzr-size-guide" class="bzr-dialog" aria-labelledby="bzr-size-guide-title">
		<div class="bzr-dialog__inner">
			<h2 id="bzr-size-guide-title"><?php esc_html_e( 'Size guide', 'bazaar-core' ); ?></h2>
			<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput -- sanitized with wp_kses_post / built from escaped parts. ?>
			<button type="button" class="bzr-dialog__close" data-bzr-dialog-close>
				<?php esc_html_e( 'Close', 'bazaar-core' ); ?>
			</button>
		</div>
	</dialog>
	<?php
}
add_action( 'woocommerce_before_variations_form', 'bzr_size_guide' );

/* -------------------------------------------------------------------------
 * Product page extras (opt in: bazaar-pdp-extras, bazaar-sticky-atc)
 * ---------------------------------------------------------------------- */

/**
 * Small inline SVG icon set (24px, stroke).
 *
 * @param string $name truck|return|shield|chat.
 * @return string
 */
function bzr_icon( $name ) {
	$paths = array(
		'truck'  => '<rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/>',
		'return' => '<polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"/>',
		'shield' => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>',
		'chat'   => '<path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/>',
	);
	if ( ! isset( $paths[ $name ] ) ) {
		return '';
	}
	return '<svg class="bzr-icon" viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">' . $paths[ $name ] . '</svg>';
}

/**
 * Savings + stock urgency line above the add-to-cart form.
 */
function bzr_pdp_status_line() {
	if ( ! bzr_feature( 'pdp-extras' ) ) {
		return;
	}
	global $product;
	if ( ! $product ) {
		return;
	}

	$lines = array();

	if ( $product->is_on_sale() && $product->is_type( 'simple' ) ) {
		$regular = (float) $product->get_regular_price();
		$sale    = (float) $product->get_sale_price();
		if ( $regular > 0 && $sale > 0 && $sale < $regular ) {
			$lines[] = sprintf(
				/* translators: %s: percentage */
				esc_html__( 'You save %s%%', 'bazaar-core' ),
				esc_html( number_format_i18n( round( ( $regular - $sale ) / $regular * 100 ) ) )
			);
		}
	}

	if ( $product->managing_stock() && $product->is_in_stock() ) {
		$qty = (int) $product->get_stock_quantity();
		if ( $qty > 0 && $qty <= 5 ) {
			$lines[] = sprintf(
				/* translators: %d: units left */
				esc_html( _n( 'Only %d left in stock', 'Only %d left in stock', $qty, 'bazaar-core' ) ),
				$qty
			);
		}
	}

	if ( $lines ) {
		echo '<p class="bzr-status">' . implode( ' &middot; ', $lines ) . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput -- each part escaped above.
	}
}
add_action( 'woocommerce_before_add_to_cart_form', 'bzr_pdp_status_line' );

/**
 * Estimated delivery window, from Store Options.
 */
function bzr_pdp_delivery_estimate() {
	if ( ! bzr_feature( 'pdp-extras' ) ) {
		return;
	}
	$min = (int) bzr_option( 'delivery_min', 0 );
	$max = (int) bzr_option( 'delivery_max', 0 );
	if ( $min < 1 || $max < $min ) {
		return;
	}

	$now   = current_datetime();
	$from  = wp_date( 'D, j M', $now->modify( "+{$min} days" )->getTimestamp() );
	$until = wp_date( 'D, j M', $now->modify( "+{$max} days" )->getTimestamp() );

	printf(
		'<p class="bzr-delivery">%1$s %2$s <strong>%3$s &ndash; %4$s</strong></p>',
		bzr_icon( 'truck' ), // phpcs:ignore WordPress.Security.EscapeOutput -- static SVG.
		esc_html__( 'Estimated delivery', 'bazaar-core' ),
		esc_html( $from ),
		esc_html( $until )
	);
}
add_action( 'woocommerce_after_add_to_cart_form', 'bzr_pdp_delivery_estimate', 5 );

/**
 * Trust badges under the add-to-cart button.
 */
function bzr_pdp_trust_badges() {
	if ( ! bzr_feature( 'pdp-extras' ) ) {
		return;
	}

	$items = apply_filters(
		'bzr_trust_badges',
		array(
			array( 'shield', __( 'Secure checkout', 'bazaar-core' ) ),
			array( 'return', __( 'Easy returns', 'bazaar-core' ) ),
			array( 'truck', __( 'Fast delivery', 'bazaar-core' ) ),
			array( 'chat', __( 'Friendly support', 'bazaar-core' ) ),
		)
	);

	echo '<ul class="bzr-trust">';
	foreach ( $items as $item ) {
		echo '<li>' . bzr_icon( $item[0] ) . '<span>' . esc_html( $item[1] ) . '</span></li>'; // phpcs:ignore WordPress.Security.EscapeOutput -- static SVG + escaped label.
	}
	echo '</ul>';
}
add_action( 'woocommerce_after_add_to_cart_form', 'bzr_pdp_trust_badges', 8 );

/**
 * Sticky add-to-cart bar for small screens. The button scrolls to the real form.
 */
function bzr_sticky_atc() {
	if ( ! bzr_feature( 'sticky-atc' ) || ! is_product() ) {
		return;
	}
	global $product;
	if ( ! $product ) {
		return;
	}
	?>
	<div class="bzr-sticky-atc" data-bzr-sticky-atc hidden>
		<div class="bzr-sticky-atc__info">
			<span class="bzr-sticky-atc__title"><?php echo esc_html( $product->get_name() ); ?></span>
			<span class="bzr-sticky-atc__price"><?php echo wp_kses_post( $product->get_price_html() ); ?></span>
		</div>
		<button type="button" class="wp-element-button" data-bzr-sticky-target>
			<?php echo $product->is_type( 'simple' ) ? esc_html__( 'Add to cart', 'bazaar-core' ) : esc_html__( 'Choose options', 'bazaar-core' ); ?>
		</button>
	</div>
	<?php
}
add_action( 'wp_footer', 'bzr_sticky_atc' );

/* -------------------------------------------------------------------------
 * Deal countdown + instalment line (opt in: bazaar-countdown, bazaar-emi)
 * ---------------------------------------------------------------------- */

/**
 * "Deal ends in" timer for simple products with a scheduled sale end (within 14 days).
 */
function bzr_pdp_countdown() {
	if ( ! bzr_feature( 'countdown' ) ) {
		return;
	}
	global $product;
	if ( ! $product || ! $product->is_on_sale() || ! $product->get_date_on_sale_to() ) {
		return;
	}

	$end = $product->get_date_on_sale_to()->getTimestamp();
	$now = time();
	if ( $end <= $now || ( $end - $now ) > 14 * DAY_IN_SECONDS ) {
		return;
	}

	printf(
		'<p class="bzr-countdown" role="timer" data-bzr-countdown="%1$d"><span>%2$s</span> <strong data-bzr-countdown-text>--</strong></p>',
		(int) $end,
		esc_html__( 'Deal ends in', 'bazaar-core' )
	);
}
add_action( 'woocommerce_before_add_to_cart_form', 'bzr_pdp_countdown', 5 );

/**
 * "Pay in N instalments of X". Shown only when configured, so no false claims by default.
 */
function bzr_pdp_emi() {
	if ( ! bzr_feature( 'emi' ) ) {
		return;
	}
	global $product;
	$months = (int) bzr_option( 'emi_months', 0 );
	if ( ! $product || $months < 2 ) {
		return;
	}
	$price = (float) $product->get_price();
	if ( $price <= 0 || $price < (float) bzr_option( 'emi_min_price', 0 ) ) {
		return;
	}

	printf(
		'<p class="bzr-emi">%s</p>',
		wp_kses_post(
			sprintf(
				/* translators: 1: number of instalments, 2: formatted amount */
				__( 'Pay in %1$d instalments of %2$s', 'bazaar-core' ),
				$months,
				wc_price( $price / $months )
			)
		)
	);
}
add_action( 'woocommerce_before_add_to_cart_form', 'bzr_pdp_emi', 12 );
