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
 *
 * @return array
 */
function hf_required_pages() {
	return array(
		'page-templates/cloth.php'    => array( __( 'Cloth Library', 'hadhramifashion' ), 'cloth-library' ),
		'page-templates/heritage.php' => array( __( 'Our Heritage', 'hadhramifashion' ), 'heritage' ),
		'page-templates/express.php'  => array( __( 'Two-Hour Delivery', 'hadhramifashion' ), 'delivery' ),
		'page-templates/checkout.php' => array( __( 'Your Bag', 'hadhramifashion' ), 'bag' ),
		'page-templates/track.php'    => array( __( 'Track Your Order', 'hadhramifashion' ), 'track' ),
		'page-templates/rider.php'    => array( __( 'Rider', 'hadhramifashion' ), 'rider' ),
	);
}

/**
 * Create anything missing. Safe to run repeatedly.
 */
function hf_seed() {
	// Collections.
	$collections = array(
		'suits'  => __( 'Suits', 'hadhramifashion' ),
		'shirts' => __( 'Shirts', 'hadhramifashion' ),
	);
	foreach ( $collections as $slug => $name ) {
		if ( ! term_exists( $slug, 'hf_collection' ) ) {
			wp_insert_term( $name, 'hf_collection', array( 'slug' => $slug ) );
		}
	}
	$occasions = array(
		'business' => __( 'Business', 'hadhramifashion' ),
		'summer'   => __( 'Summer Weight', 'hadhramifashion' ),
		'occasion' => __( 'Evening & Occasion', 'hadhramifashion' ),
		'wedding'  => __( 'Wedding', 'hadhramifashion' ),
	);
	foreach ( $occasions as $slug => $name ) {
		if ( ! term_exists( $slug, 'hf_occasion' ) ) {
			wp_insert_term( $name, 'hf_occasion', array( 'slug' => $slug ) );
		}
	}

	// Products — only on a fresh install.
	$existing = get_posts( array( 'post_type' => 'hf_product', 'post_status' => 'any', 'posts_per_page' => 1, 'fields' => 'ids' ) );
	if ( ! $existing ) {
		$data  = hf_catalog_data();
		$order = 0;
		foreach ( $data['suits'] as $suit ) {
			list( $name, $subtitle, $price, $image, $swatch, $fabric, $occasion ) = $suit;
			$id = wp_insert_post(
				array(
					'post_type'    => 'hf_product',
					'post_status'  => 'publish',
					'post_title'   => $name,
					'menu_order'   => $order++,
					'post_excerpt' => sprintf(
						/* translators: %s: cloth */
						__( 'Cut from %s and finished by hand in our atelier. Made to your measure and delivered within 24 hours.', 'hadhramifashion' ),
						strtolower( $fabric )
					),
					'post_content' => hf_suit_story( $fabric ),
				)
			);
			if ( ! $id || is_wp_error( $id ) ) {
				continue;
			}
			wp_set_object_terms( $id, 'suits', 'hf_collection' );
			wp_set_object_terms( $id, $occasion, 'hf_occasion' );
			update_post_meta( $id, '_hf_price', $price );
			update_post_meta( $id, '_hf_subtitle', $subtitle );
			update_post_meta( $id, '_hf_fabric', $fabric );
			update_post_meta( $id, '_hf_drive_image', $image );
			update_post_meta( $id, '_hf_drive_swatch', $swatch );
			update_post_meta( $id, '_hf_express', '' );
		}

		foreach ( $data['shirts'] as $shirt ) {
			list( $name, $subtitle, $price, $image, $colour, $express ) = $shirt;
			$id = wp_insert_post(
				array(
					'post_type'    => 'hf_product',
					'post_status'  => 'publish',
					'post_title'   => $name,
					'menu_order'   => $order++,
					'post_excerpt' => $express
						? __( 'Kept pressed and ready in every collar and sleeve size — at your door within two hours, or cut to your measure in twenty-four.', 'hadhramifashion' )
						: __( 'Cut to your measure from two-ply cotton and delivered within twenty-four hours.', 'hadhramifashion' ),
					'post_content' => hf_shirt_story(),
				)
			);
			if ( ! $id || is_wp_error( $id ) ) {
				continue;
			}
			wp_set_object_terms( $id, 'shirts', 'hf_collection' );
			wp_set_object_terms( $id, 'Evening' === $colour ? 'occasion' : 'business', 'hf_occasion' );
			update_post_meta( $id, '_hf_price', $price );
			update_post_meta( $id, '_hf_subtitle', $subtitle );
			update_post_meta( $id, '_hf_colour', $colour );
			update_post_meta( $id, '_hf_fabric', __( 'Two-ply cotton', 'hadhramifashion' ) );
			update_post_meta( $id, '_hf_drive_image', $image );
			update_post_meta( $id, '_hf_express', $express ? '1' : '' );
		}
	}

	// Pages.
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

	// Pretty permalinks make the collection URLs readable.
	if ( ! get_option( 'permalink_structure' ) ) {
		update_option( 'permalink_structure', '/%postname%/' );
	}
	flush_rewrite_rules();
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
		add_submenu_page( 'hf-settings', __( 'Setup & Images', 'hadhramifashion' ), __( 'Setup & Images', 'hadhramifashion' ), 'manage_options', 'hf-setup', 'hf_render_setup_page' );
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
	$products = get_posts( array( 'post_type' => 'hf_product', 'post_status' => 'any', 'posts_per_page' => -1, 'fields' => 'ids' ) );
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
		<p><?php esc_html_e( 'Recreates any missing collections and pages (Bag, Track, Rider, Heritage…). Products are only created on an empty catalogue.', 'hadhramifashion' ); ?></p>
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
		wp_safe_redirect( admin_url( 'admin.php?page=hf-setup' ) );
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
		wp_safe_redirect( admin_url( 'admin.php?page=hf-setup' . ( $remaining ? '&hf_running=1' : '' ) ) );
		exit;
	}
);
