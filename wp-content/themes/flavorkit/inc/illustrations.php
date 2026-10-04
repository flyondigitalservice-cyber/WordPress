<?php
/**
 * Brand-coloured SVG packaging illustrations + demo catalogue.
 *
 * These render whenever a client has not uploaded real photography yet or
 * WooCommerce has no products, so the theme always looks complete. They are
 * drawn with CSS variables, so they automatically follow the active preset.
 *
 * @package FlavorKit
 */

defined( 'ABSPATH' ) || exit;

/**
 * Colour pairs (pack body, band) built from brand variables.
 *
 * @return array<int, string[]>
 */
function flavorkit_pack_palettes() {
	return array(
		array( 'var(--fk-primary)', 'var(--fk-secondary)' ),
		array( 'var(--fk-accent)', 'var(--fk-secondary)' ),
		array( 'var(--fk-secondary)', 'var(--fk-primary)' ),
		array( 'var(--fk-dark)', 'var(--fk-primary)' ),
		array( 'var(--fk-primary)', 'var(--fk-accent)' ),
		array( 'var(--fk-accent)', 'var(--fk-primary)' ),
	);
}

/**
 * Render a packaging illustration.
 *
 * @param string $shape   can|pouch|jar|bottle|box.
 * @param int    $palette Palette index.
 * @param string $label   Big label word.
 * @param string $sub     Small label line.
 * @return string SVG markup.
 */
function flavorkit_pack_svg( $shape = 'can', $palette = 0, $label = '', $sub = '' ) {
	$palettes = flavorkit_pack_palettes();
	$pair     = $palettes[ absint( $palette ) % count( $palettes ) ];
	$c1       = $pair[0];
	$c2       = $pair[1];
	$label    = esc_html( $label ? $label : get_bloginfo( 'name' ) );
	$sub      = esc_html( $sub );
	$font     = 'font-family:var(--fk-font-heading);font-weight:800;fill:var(--fk-dark)';
	$sub_font = 'font-family:var(--fk-font-body);font-weight:700;fill:var(--fk-dark);opacity:.7;letter-spacing:.08em';
	$size     = mb_strlen( wp_strip_all_tags( $label ) ) > 7 ? 14 : 17;
	$shadow   = '<ellipse cx="100" cy="250" rx="62" ry="8" style="fill:#000;opacity:.12"/>';

	switch ( $shape ) {
		case 'pouch':
			$body = '<path d="M44 34h112l8 200a12 12 0 0 1-12 12H48a12 12 0 0 1-12-12z" style="fill:' . $c1 . '"/>'
				. '<path d="M37 196c40 16 86 16 126 0l1 38a12 12 0 0 1-12 12H48a12 12 0 0 1-12-12z" style="fill:' . $c2 . '"/>'
				. '<rect x="44" y="22" width="112" height="20" rx="4" style="fill:' . $c2 . '"/>'
				. '<path d="M50 30h100" style="stroke:#fff;stroke-width:2;stroke-dasharray:4 4;opacity:.6"/>'
				. '<rect x="54" y="52" width="10" height="130" rx="5" style="fill:#fff;opacity:.25"/>'
				. '<ellipse cx="100" cy="122" rx="44" ry="38" style="fill:#fff"/>';
			$ty   = 122;
			break;

		case 'jar':
			$body = '<rect x="40" y="52" width="120" height="194" rx="34" style="fill:' . $c1 . '"/>'
				. '<rect x="54" y="20" width="92" height="38" rx="10" style="fill:' . $c2 . '"/>'
				. '<path d="M62 30v18M76 30v18M90 30v18M104 30v18M118 30v18M132 30v18" style="stroke:#000;stroke-width:2;opacity:.12"/>'
				. '<rect x="40" y="104" width="120" height="86" style="fill:#fff"/>'
				. '<rect x="52" y="64" width="10" height="34" rx="5" style="fill:#fff;opacity:.3"/>';
			$ty   = 147;
			break;

		case 'bottle':
			$body = '<path d="M84 26h32v28c0 10 30 18 30 48v128a16 16 0 0 1-16 16H70a16 16 0 0 1-16-16V102c0-30 30-38 30-48z" style="fill:' . $c1 . '"/>'
				. '<rect x="80" y="10" width="40" height="22" rx="6" style="fill:' . $c2 . '"/>'
				. '<rect x="54" y="122" width="92" height="76" style="fill:#fff"/>'
				. '<rect x="54" y="198" width="92" height="12" style="fill:' . $c2 . '"/>'
				. '<rect x="64" y="96" width="9" height="130" rx="4.5" style="fill:#fff;opacity:.28"/>';
			$ty   = 160;
			break;

		case 'box':
			$body = '<path d="M50 52 72 26h88l-22 26z" style="fill:' . $c2 . '"/>'
				. '<path d="M138 52 160 26v194l-22 26z" style="fill:' . $c2 . '"/>'
				. '<path d="M138 52 160 26v194l-22 26z" style="fill:#000;opacity:.18"/>'
				. '<rect x="40" y="52" width="98" height="194" rx="4" style="fill:' . $c1 . '"/>'
				. '<path d="M40 200c30-14 68-14 98 0v42a4 4 0 0 1-4 4H44a4 4 0 0 1-4-4z" style="fill:' . $c2 . '"/>'
				. '<circle cx="89" cy="128" r="40" style="fill:#fff"/>';
			return '<svg class="fk-pack fk-pack-box" viewBox="0 0 200 260" role="img" aria-label="' . $label . '">' . $shadow . $body
				. '<text x="89" y="128" text-anchor="middle" style="' . $font . ';font-size:' . ( $size - 2 ) . 'px">' . $label . '</text>'
				. ( $sub ? '<text x="89" y="146" text-anchor="middle" style="' . $sub_font . ';font-size:8px">' . $sub . '</text>' : '' )
				. '</svg>';

		case 'can':
		default:
			$body = '<rect x="48" y="26" width="104" height="220" rx="20" style="fill:' . $c1 . '"/>'
				. '<path d="M48 164c26-22 78-22 104 0v62a20 20 0 0 1-20 20H68a20 20 0 0 1-20-20z" style="fill:' . $c2 . '"/>'
				. '<rect x="56" y="16" width="88" height="18" rx="9" style="fill:#e6e1ea"/>'
				. '<rect x="60" y="48" width="10" height="176" rx="5" style="fill:#fff;opacity:.28"/>'
				. '<circle cx="100" cy="110" r="40" style="fill:#fff"/>';
			$ty   = 110;
			break;
	}

	return '<svg class="fk-pack fk-pack-' . esc_attr( $shape ) . '" viewBox="0 0 200 260" role="img" aria-label="' . $label . '">' . $shadow . $body
		. '<text x="100" y="' . ( $ty + ( $sub ? 0 : 6 ) ) . '" text-anchor="middle" style="' . $font . ';font-size:' . $size . 'px">' . $label . '</text>'
		. ( $sub ? '<text x="100" y="' . ( $ty + 18 ) . '" text-anchor="middle" style="' . $sub_font . ';font-size:9px">' . $sub . '</text>' : '' )
		. '</svg>';
}

/**
 * Demo catalogue shown until WooCommerce has real products.
 *
 * @return array<int, array<string, mixed>>
 */
function flavorkit_demo_products() {
	return array(
		array( 'name' => 'Mango Chilli Fizz', 'price' => 60, 'regular' => 75, 'rating' => 4.9, 'reviews' => 1240, 'shape' => 'can', 'label' => 'MANGO', 'sub' => 'CHILLI FIZZ', 'palette' => 0, 'badge' => 'Bestseller' ),
		array( 'name' => 'Peri Peri Makhana', 'price' => 149, 'regular' => 0, 'rating' => 4.8, 'reviews' => 860, 'shape' => 'pouch', 'label' => 'PERI PERI', 'sub' => 'MAKHANA', 'palette' => 1, 'badge' => 'New' ),
		array( 'name' => 'Classic Garam Masala', 'price' => 199, 'regular' => 249, 'rating' => 4.9, 'reviews' => 2110, 'shape' => 'jar', 'label' => 'MASALA', 'sub' => 'CLASSIC', 'palette' => 2, 'badge' => '-20%' ),
		array( 'name' => 'Berry Kombucha', 'price' => 120, 'regular' => 0, 'rating' => 4.7, 'reviews' => 540, 'shape' => 'bottle', 'label' => 'BERRY', 'sub' => 'KOMBUCHA', 'palette' => 4, 'badge' => '' ),
		array( 'name' => 'Choco Protein Granola', 'price' => 349, 'regular' => 399, 'rating' => 4.8, 'reviews' => 980, 'shape' => 'box', 'label' => 'CHOCO', 'sub' => 'GRANOLA', 'palette' => 3, 'badge' => 'High protein' ),
		array( 'name' => 'Cheesy Pasta Cup', 'price' => 99, 'regular' => 0, 'rating' => 4.6, 'reviews' => 410, 'shape' => 'jar', 'label' => 'CHEESY', 'sub' => 'PASTA CUP', 'palette' => 5, 'badge' => '' ),
		array( 'name' => 'Lemon Ginger Soda', 'price' => 60, 'regular' => 0, 'rating' => 4.8, 'reviews' => 760, 'shape' => 'can', 'label' => 'LEMON', 'sub' => 'GINGER SODA', 'palette' => 2, 'badge' => 'Gut friendly' ),
		array( 'name' => 'Party Snack Combo', 'price' => 499, 'regular' => 649, 'rating' => 4.9, 'reviews' => 320, 'shape' => 'pouch', 'label' => 'COMBO', 'sub' => 'PACK OF 5', 'palette' => 3, 'badge' => 'Save 23%' ),
	);
}

/**
 * Format a demo price with the store currency where possible.
 *
 * @param float $amount Amount.
 * @return string Safe HTML.
 */
function flavorkit_demo_price( $amount ) {
	if ( function_exists( 'wc_price' ) ) {
		return wc_price( $amount );
	}
	return esc_html( '₹' . number_format_i18n( $amount ) );
}
