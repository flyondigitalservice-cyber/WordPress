<?php
/**
 * First-run setup: collections, starter catalogue, pages, and the
 * Google Drive → Media Library importer.
 *
 * @package HadhramiFashion
 */

defined( 'ABSPATH' ) || exit;

/**
 * Pages the theme relies on: template => array( title, slug ).
 * Cart, checkout and My Account are WooCommerce pages; /rider/ and /track/<code>/
 * come from the Hadhrami Delivery plugin.
 *
 * @return array
 */
function hf_required_pages() {
	return array(
		'page-templates/cloth.php'    => array( __( 'Cloth Library', 'hadhramifashion' ), 'cloth-library' ),
		'page-templates/heritage.php' => array( __( 'Our Heritage', 'hadhramifashion' ), 'heritage' ),
		'page-templates/express.php'  => array( __( 'Two-Hour Delivery', 'hadhramifashion' ), 'delivery' ),
	);
}

/**
 * Create anything missing. Safe to run repeatedly.
 */
function hf_seed() {
	if ( class_exists( 'WooCommerce' ) ) {
		hf_seed_store();
	}

	foreach ( hf_required_pages() as $template => $page ) {
		$found = get_posts(
			array(
				'post_type'      => 'page',
				'post_status'    => 'any',
				'meta_key'       => '_wp_page_template',
				'meta_value'     => $template,
				'posts_per_page' => 1,
				'fields'         => 'ids',
			)
		);
		if ( $found ) {
			continue;
		}
		$page_id = wp_insert_post(
			array(
				'post_type'   => 'page',
				'post_status' => 'publish',
				'post_title'  => $page[0],
				'post_name'   => $page[1],
			)
		);
		if ( $page_id && ! is_wp_error( $page_id ) ) {
			update_post_meta( $page_id, '_wp_page_template', $template );
		}
	}

	// Pretty permalinks: /shop/, /product/..., and the plugin's /rider/ and /track/ links.
	if ( ! get_option( 'permalink_structure' ) ) {
		update_option( 'permalink_structure', '/%postname%/' );
	}
	flush_rewrite_rules();
}

/**
 * WooCommerce: categories, occasion tags, classic cart/checkout pages and the
 * starter catalogue (only when the store has no products yet).
 */
function hf_seed_store() {
	$cats = array();
	foreach ( array( 'suits' => __( 'Suits', 'hadhramifashion' ), 'shirts' => __( 'Shirts', 'hadhramifashion' ) ) as $slug => $name ) {
		$term = term_exists( $slug, 'product_cat' );
		if ( ! $term ) {
			$term = wp_insert_term( $name, 'product_cat', array( 'slug' => $slug ) );
		}
		$cats[ $slug ] = is_array( $term ) ? (int) $term['term_id'] : 0;
	}
	$tags = array();
	$occasions = array(
		'business' => __( 'Business', 'hadhramifashion' ),
		'summer'   => __( 'Summer Weight', 'hadhramifashion' ),
		'occasion' => __( 'Evening & Occasion', 'hadhramifashion' ),
		'wedding'  => __( 'Wedding', 'hadhramifashion' ),
	);
	foreach ( $occasions as $slug => $name ) {
		$term = term_exists( $slug, 'product_tag' );
		if ( ! $term ) {
			$term = wp_insert_term( $name, 'product_tag', array( 'slug' => $slug ) );
		}
		$tags[ $slug ] = is_array( $term ) ? (int) $term['term_id'] : 0;
	}

	// The delivery plugin hooks the classic checkout: use the shortcodes, not blocks.
	foreach ( array( 'cart' => '[woocommerce_cart]', 'checkout' => '[woocommerce_checkout]' ) as $page => $shortcode ) {
		$page_id = wc_get_page_id( $page );
		if ( $page_id > 0 && get_post( $page_id ) ) {
			if ( false === strpos( get_post_field( 'post_content', $page_id ), $shortcode ) ) {
				wp_update_post( array( 'ID' => $page_id, 'post_content' => $shortcode ) );
			}
		} else {
			$page_id = wp_insert_post(
				array(
					'post_type'    => 'page',
					'post_status'  => 'publish',
					'post_title'   => 'cart' === $page ? __( 'Your Bag', 'hadhramifashion' ) : __( 'Checkout', 'hadhramifashion' ),
					'post_content' => $shortcode,
				)
			);
			update_option( 'woocommerce_' . $page . '_page_id', $page_id );
		}
	}

	$existing = get_posts( array( 'post_type' => 'product', 'post_status' => 'any', 'posts_per_page' => 1, 'fields' => 'ids' ) );
	if ( $existing ) {
		return;
	}

	// Fresh store: rupees, Hyderabad, pay on delivery.
	update_option( 'woocommerce_currency', 'INR' );
	update_option( 'woocommerce_price_num_decimals', '0' );
	if ( ! get_option( 'woocommerce_default_country' ) || 0 === strpos( (string) get_option( 'woocommerce_default_country' ), 'US' ) ) {
		update_option( 'woocommerce_default_country', 'IN:TS' );
	}
	$cod            = (array) get_option( 'woocommerce_cod_settings', array() );
	$cod['enabled'] = 'yes';
	$cod['title']   = isset( $cod['title'] ) && $cod['title'] ? $cod['title'] : __( 'Pay on delivery or at your fitting', 'hadhramifashion' );
	update_option( 'woocommerce_cod_settings', $cod );

	$data  = hf_catalog_data();
	$order = 0;
	foreach ( $data['suits'] as $suit ) {
		list( $name, $subtitle, $price, $image, $swatch, $fabric, $occasion ) = $suit;
		hf_create_product(
			array(
				'name'        => $name,
				'price'       => $price,
				'order'       => $order++,
				'cat'         => $cats['suits'],
				'tag'         => isset( $tags[ $occasion ] ) ? $tags[ $occasion ] : 0,
				'short'       => sprintf(
					/* translators: %s: cloth */
					__( 'Cut from %s and finished by hand in our atelier. Made to your measure and delivered within 24 hours.', 'hadhramifashion' ),
					strtolower( $fabric )
				),
				'description' => hf_suit_story( $fabric ),
				'meta'        => array(
					'_hf_subtitle'     => $subtitle,
					'_hf_fabric'       => $fabric,
					'_hf_drive_image'  => $image,
					'_hf_drive_swatch' => $swatch,
					'_hfd_express'     => 'no',
				),
			)
		);
	}
	foreach ( $data['shirts'] as $shirt ) {
		list( $name, $subtitle, $price, $image, $colour, $express ) = $shirt;
		hf_create_product(
			array(
				'name'        => $name,
				'price'       => $price,
				'order'       => $order++,
				'cat'         => $cats['shirts'],
				'tag'         => 'Evening' === $colour ? $tags['occasion'] : $tags['business'],
				'short'       => $express
					? __( 'Kept pressed and ready in every collar size — at your door within two hours, or cut to your measure in twenty-four.', 'hadhramifashion' )
					: __( 'Cut to your measure from two-ply cotton and delivered within twenty-four hours.', 'hadhramifashion' ),
				'description' => hf_shirt_story(),
				'meta'        => array(
					'_hf_subtitle'    => $subtitle,
					'_hf_colour'      => $colour,
					'_hf_fabric'      => __( 'Two-ply cotton', 'hadhramifashion' ),
					'_hf_drive_image' => $image,
					'_hfd_express'    => $express ? 'yes' : 'no',
				),
			)
		);
	}
}

/**
 * Create one simple WooCommerce product.
 *
 * @param array $args name, price, order, cat, tag, short, description, meta.
 * @return int Product ID.
 */
function hf_create_product( $args ) {
	$product = new WC_Product_Simple();
	$product->set_name( $args['name'] );
	$product->set_status( 'publish' );
	$product->set_regular_price( (string) $args['price'] );
	$product->set_menu_order( $args['order'] );
	$product->set_short_description( $args['short'] );
	$product->set_description( $args['description'] );
	$product->set_category_ids( array_filter( array( $args['cat'] ) ) );
	$product->set_tag_ids( array_filter( array( $args['tag'] ) ) );
	$product->set_sold_individually( false );
	foreach ( $args['meta'] as $key => $value ) {
		$product->update_meta_data( $key, $value );
	}
	return $product->save();
}

/**
 * Story copy for a suit.
 *
 * @param string $fabric Cloth.
 * @return string
 */
function hf_suit_story( $fabric ) {
	return '<p>' . sprintf(
		/* translators: %s: cloth */
		esc_html__( 'Every Hadhrami suit begins at the cutting table, not on a production line. Your measurements are drafted into a paper pattern that is yours alone, the %s is laid and chalked by hand, and a master tailor shapes the canvas so the jacket follows you rather than the other way round.', 'hadhramifashion' ),
		esc_html( strtolower( $fabric ) )
	) . '</p><p>' . esc_html__( 'Half-canvassed chest · Hand-set collar · Functional cuff buttons · Bemberg lining · Generous inlays so it can be altered for years.', 'hadhramifashion' ) . '</p>';
}

/**
 * Story copy for a shirt.
 *
 * @return string
 */
function hf_shirt_story() {
	return '<p>' . esc_html__( 'Two-ply cotton, single-needle side seams, a collar that holds its line without a stiff fused feel, and mother-of-pearl effect buttons sewn with a shank so they sit flat under a tie.', 'hadhramifashion' ) . '</p>';
}

add_action( 'after_switch_theme', 'hf_seed' );

/*
 * Setup screen: re-seed and import Drive images into the Media Library.
 */
add_action(
	'admin_menu',
	function () {
		add_theme_page( __( 'Hadhrami Setup', 'hadhramifashion' ), __( 'Hadhrami Setup', 'hadhramifashion' ), 'manage_options', 'hf-setup', 'hf_render_setup_page' );
	},
	20
);

/**
 * Every Drive file the theme references, keyed by file ID → product ID (0 for brand images).
 *
 * @return array
 */
function hf_drive_manifest() {
	$manifest = array();
	$data     = hf_catalog_data();
	foreach ( $data['brand'] as $file_id ) {
		$manifest[ $file_id ] = 0;
	}
	$products = get_posts( array( 'post_type' => 'product', 'post_status' => 'any', 'posts_per_page' => -1, 'fields' => 'ids' ) );
	foreach ( $products as $product_id ) {
		foreach ( array( '_hf_drive_image', '_hf_drive_swatch' ) as $key ) {
			$file_id = get_post_meta( $product_id, $key, true );
			if ( $file_id ) {
				$manifest[ $file_id ] = '_hf_drive_image' === $key ? $product_id : 0;
			}
		}
	}
	return $manifest;
}

/**
 * Setup screen.
 */
function hf_render_setup_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$manifest = hf_drive_manifest();
	$imported = get_option( 'hf_drive_imports', array() );
	$pending  = array_diff_key( $manifest, $imported );
	$running  = isset( $_GET['hf_running'] ) && $pending; // phpcs:ignore WordPress.Security.NonceVerification
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Hadhrami Fashion — Setup & Images', 'hadhramifashion' ); ?></h1>
		<?php if ( ! class_exists( 'WooCommerce' ) || ! class_exists( 'HFD_Settings' ) ) : ?>
			<div class="notice notice-warning inline"><p><?php esc_html_e( 'Install and activate WooCommerce and the Hadhrami Delivery plugin, then press “Run setup” below to create the catalogue.', 'hadhramifashion' ); ?></p></div>
		<?php endif; ?>

		<h2><?php esc_html_e( 'Product imagery', 'hadhramifashion' ); ?></h2>
		<p>
			<?php
			printf(
				/* translators: 1: imported, 2: total */
				esc_html__( '%1$d of %2$d images from the brand Google Drive folder are in your Media Library. Until imported, images load directly from Google Drive.', 'hadhramifashion' ),
				count( $manifest ) - count( $pending ),
				count( $manifest )
			);
			?>
		</p>
		<?php if ( $pending ) : ?>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" id="hf-import-form">
				<?php wp_nonce_field( 'hf_import' ); ?>
				<input type="hidden" name="action" value="hf_import_images">
				<?php submit_button( $running ? __( 'Importing… keep this tab open', 'hadhramifashion' ) : __( 'Import images into Media Library', 'hadhramifashion' ), 'primary', 'submit', false, $running ? array( 'disabled' => 'disabled' ) : array() ); ?>
			</form>
			<?php if ( $running ) : ?>
				<script>setTimeout(function(){var f=document.getElementById('hf-import-form');f.querySelector('[type=submit]').disabled=false;f.submit();},400);</script>
			<?php endif; ?>
		<?php else : ?>
			<p><strong><?php esc_html_e( 'All images are imported.', 'hadhramifashion' ); ?></strong></p>
		<?php endif; ?>

		<h2><?php esc_html_e( 'Catalogue & pages', 'hadhramifashion' ); ?></h2>
		<p><?php esc_html_e( 'Recreates missing categories and pages (Heritage, Cloth Library, Delivery) and makes sure the cart and checkout use the classic WooCommerce pages the delivery plugin needs. Products are only created when the store is empty.', 'hadhramifashion' ); ?></p>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<?php wp_nonce_field( 'hf_seed' ); ?>
			<input type="hidden" name="action" value="hf_seed">
			<?php submit_button( __( 'Run setup', 'hadhramifashion' ), 'secondary', 'submit', false ); ?>
		</form>
	</div>
	<?php
}

add_action(
	'admin_post_hf_seed',
	function () {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die();
		}
		check_admin_referer( 'hf_seed' );
		hf_seed();
		// Forget failed image imports so they can be retried.
		update_option( 'hf_drive_imports', array_filter( (array) get_option( 'hf_drive_imports', array() ) ), false );
		wp_safe_redirect( admin_url( 'themes.php?page=hf-setup' ) );
		exit;
	}
);

add_action(
	'admin_post_hf_import_images',
	function () {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die();
		}
		check_admin_referer( 'hf_import' );

		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';

		$manifest = hf_drive_manifest();
		$imported = get_option( 'hf_drive_imports', array() );
		$pending  = array_slice( array_diff_key( $manifest, $imported ), 0, 6, true );

		foreach ( $pending as $file_id => $product_id ) {
			$tmp = download_url( 'https://drive.google.com/uc?export=download&id=' . rawurlencode( $file_id ), 60 );
			if ( is_wp_error( $tmp ) ) {
				$imported[ $file_id ] = 0; // Skip on failure so the batch can finish; re-run setup later.
				continue;
			}
			$file = array(
				'name'     => 'hadhrami-' . sanitize_file_name( $file_id ) . '.jpg',
				'tmp_name' => $tmp,
			);
			$attachment = media_handle_sideload( $file, $product_id ? $product_id : 0 );
			if ( is_wp_error( $attachment ) ) {
				wp_delete_file( $tmp );
				$imported[ $file_id ] = 0;
				continue;
			}
			$imported[ $file_id ] = $attachment;
			if ( $product_id && ! has_post_thumbnail( $product_id ) ) {
				set_post_thumbnail( $product_id, $attachment );
				update_post_meta( $attachment, '_wp_attachment_image_alt', get_the_title( $product_id ) );
			}
		}

		// Failed files are stored as 0: they keep loading from Drive and are not retried in this run.
		update_option( 'hf_drive_imports', $imported, false );

		$remaining = array_diff_key( $manifest, $imported );
		wp_safe_redirect( admin_url( 'themes.php?page=hf-setup' . ( $remaining ? '&hf_running=1' : '' ) ) );
		exit;
	}
);
