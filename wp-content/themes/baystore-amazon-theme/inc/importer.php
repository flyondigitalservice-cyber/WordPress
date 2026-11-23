<?php
/**
 * Baystore Amazon Lean — 1-Click WooCommerce Catalog & Coupon Importer
 *
 * @package Baystore_Amazon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'admin_menu', function () {
	add_theme_page(
		__( 'Baystore Catalog Importer', 'baystore-amazon' ),
		__( 'Baystore Importer', 'baystore-amazon' ),
		'manage_options',
		'baystore-importer',
		'baystore_render_importer_page'
	);
} );

function baystore_render_importer_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$message = '';
	if ( isset( $_POST['baystore_import_submit'] ) && check_admin_referer( 'baystore_import_action', 'baystore_import_nonce' ) ) {
		if ( ! class_exists( 'WooCommerce' ) ) {
			$message = '<div class="notice notice-error"><p>Please install and activate <strong>WooCommerce</strong> first before importing products.</p></div>';
		} else {
			$limit   = isset( $_POST['baystore_import_limit'] ) ? absint( $_POST['baystore_import_limit'] ) : 30;
			$result  = baystore_run_catalog_import( $limit );
			$message = '<div class="notice notice-success"><p>' . esc_html( $result ) . '</p></div>';
		}
	}

	?>
	<div class="wrap">
		<h1>Baystore.in — 1-Click Catalog &amp; Lean Coupon Importer</h1>
		<?php echo $message; ?>
		<div style="background:#fff;border:1px solid #ccd0d4;padding:20px;max-width:720px;margin-top:16px;border-radius:6px;">
			<p>This tool imports real <strong>Baystore.in</strong> departments, products (with MRP, sale price, SKU, descriptions, and external/sideloaded images), and creates the <strong>BAY10</strong> (10% Off) WooCommerce coupon.</p>
			<form method="post">
				<?php wp_nonce_field( 'baystore_import_action', 'baystore_import_nonce' ); ?>
				<p>
					<label for="baystore_import_limit"><strong>How many primary products to import?</strong></label><br>
					<select name="baystore_import_limit" id="baystore_import_limit" style="margin-top:6px;">
						<option value="20">20 Featured Products (Fastest — ~15s)</option>
						<option value="45" selected>45 Curated Products (~30s)</option>
						<option value="90">All 90 Baystore Products</option>
					</select>
				</p>
				<p>
					<button type="submit" name="baystore_import_submit" class="button button-primary button-hero">
						Import Baystore Catalog &amp; Create BAY10 Coupon
					</button>
				</p>
			</form>
		</div>
	</div>
	<?php
}

function baystore_run_catalog_import( $limit = 45 ) {
	$seed_path = get_template_directory() . '/inc/catalog-seed.json';
	if ( ! file_exists( $seed_path ) ) {
		return 'Seed file not found.';
	}

	$items = json_decode( file_get_contents( $seed_path ), true );
	if ( ! is_array( $items ) ) {
		return 'Invalid catalog seed JSON.';
	}

	// Ensure BAY10 10% coupon exists.
	if ( ! wc_get_coupon_id_by_code( 'BAY10' ) ) {
		$coupon = new WC_Coupon();
		$coupon->set_code( 'BAY10' );
		$coupon->set_discount_type( 'percent' );
		$coupon->set_amount( 10 );
		$coupon->set_individual_use( false );
		$coupon->set_description( 'Baystore Lean Conversion 10% Instant Discount' );
		$coupon->save();
	}

	$created = 0;
	$skipped = 0;
	$count   = 0;

	// Prioritize primary SKUs first.
	usort( $items, function ( $a, $b ) {
		return ( ! empty( $b['isPrimary'] ) ? 1 : 0 ) - ( ! empty( $a['isPrimary'] ) ? 1 : 0 );
	} );

	if ( ! function_exists( 'media_sideload_image' ) ) {
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';
	}

	foreach ( $items as $item ) {
		if ( $count >= $limit ) {
			break;
		}
		$count++;

		$sku = 'BS-' . substr( (string) $item['id'], -6 );
		if ( wc_get_product_id_by_sku( $sku ) ) {
			$skipped++;
			continue;
		}

		// Ensure category exists.
		$dept_name = ! empty( $item['department'] ) ? $item['department'] : 'Home & Kitchen';
		$term      = term_exists( $dept_name, 'product_cat' );
		if ( ! $term ) {
			$term = wp_insert_term( $dept_name, 'product_cat' );
		}
		$cat_ids = ( ! is_wp_error( $term ) && isset( $term['term_id'] ) ) ? array( (int) $term['term_id'] ) : array();

		$product = new WC_Product_Simple();
		$product->set_name( sanitize_text_field( $item['shortTitle'] ) );
		$product->set_slug( sanitize_title( $item['handle'] ) );
		$product->set_status( 'publish' );
		$product->set_catalog_visibility( 'visible' );
		$product->set_sku( $sku );
		$product->set_regular_price( (string) $item['mrp'] );
		$product->set_sale_price( (string) $item['price'] );
		$product->set_price( (string) $item['price'] );
		$product->set_manage_stock( true );
		$product->set_stock_quantity( isset( $item['stockLeft'] ) ? (int) $item['stockLeft'] : 25 );
		$product->set_stock_status( 'instock' );
		$product->set_category_ids( $cat_ids );

		$bullets_html = '';
		if ( ! empty( $item['bullets'] ) && is_array( $item['bullets'] ) ) {
			$bullets_html .= '<ul class="bs-pdp-bullets">';
			foreach ( $item['bullets'] as $b ) {
				$bullets_html .= '<li>' . esc_html( $b ) . '</li>';
			}
			$bullets_html .= '</ul>';
		}
		$product->set_short_description( $bullets_html ?: wp_kses_post( $item['description'] ) );
		$product->set_description( wp_kses_post( $item['description'] ) );

		$product_id = $product->save();

		if ( $product_id ) {
			update_post_meta( $product_id, '_bs_rating', (string) $item['rating'] );
			update_post_meta( $product_id, '_bs_reviews_count', (string) $item['reviewsCount'] );
			update_post_meta( $product_id, '_bs_bought_month', (string) $item['boughtLastMonth'] );
			if ( ! empty( $item['badge'] ) ) {
				update_post_meta( $product_id, '_bs_badge', sanitize_text_field( $item['badge'] ) );
			}
			if ( ! empty( $item['images'][0] ) ) {
				update_post_meta( $product_id, '_bs_ext_image', esc_url_raw( $item['images'][0] ) );
				$att_id = media_sideload_image( $item['images'][0], $product_id, $item['shortTitle'], 'id' );
				if ( ! is_wp_error( $att_id ) ) {
					set_post_thumbnail( $product_id, $att_id );
				}
			}
			$created++;
		}
	}

	return sprintf( 'Import complete: %d products created, %d skipped (already existed). BAY10 coupon ready.', $created, $skipped );
}
