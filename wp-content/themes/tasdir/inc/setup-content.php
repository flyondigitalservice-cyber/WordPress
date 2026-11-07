<?php
/**
 * One-time starter content on theme activation: products, key pages, menu and pretty permalinks.
 * Never overwrites anything that already exists.
 *
 * @package Tasdir
 */

defined( 'ABSPATH' ) || exit;

/**
 * Create starter content.
 */
function tasdir_starter_content() {
	if ( get_option( 'tasdir_starter_done' ) ) {
		flush_rewrite_rules();
		return;
	}

	// Pretty permalinks are required for clean, SEO-friendly URLs.
	if ( ! get_option( 'permalink_structure' ) ) {
		global $wp_rewrite;
		$wp_rewrite->set_permalink_structure( '/%postname%/' );
	}

	tasdir_register_products();

	$products = array(
		'banana' => array(
			'title'   => __( 'Fresh Cavendish Banana', 'tasdir' ),
			'order'   => 1,
			'excerpt' => __( 'Export-grade green Cavendish bananas from Indian farms — uniform fingers, hand graded and packed in ventilated 13 kg Tasdir cartons.', 'tasdir' ),
			'content' => "<!-- wp:paragraph -->\n<p>" . __( 'Tasdir sources Cavendish (G9) bananas directly from partner farms in India. Hands are de-handed, washed, treated, graded by finger length and packed at the right maturity so they ripen evenly at destination.', 'tasdir' ) . "</p>\n<!-- /wp:paragraph -->\n\n<!-- wp:heading -->\n<h2>" . __( 'Quality & packing', 'tasdir' ) . "</h2>\n<!-- /wp:heading -->\n\n<!-- wp:list -->\n<ul><li>" . __( 'Green, mature, unripe fruit for sea freight', 'tasdir' ) . '</li><li>' . __( 'Crown-cut clusters, latex cleaned', 'tasdir' ) . '</li><li>' . __( 'Polythene liner + ventilated corrugated carton', 'tasdir' ) . '</li><li>' . __( 'Shipped in temperature-controlled reefer containers (13–14 °C)', 'tasdir' ) . "</li></ul>\n<!-- /wp:list -->",
			'specs'   => array(
				'varieties'  => 'Cavendish (G9)',
				'origin'     => 'India',
				'packaging'  => '13 kg net ventilated carton',
				'moq'        => '1 × 40 ft reefer',
				'season'     => 'Year round',
				'shelf_life' => '25–30 days at 13–14 °C',
				'grade'      => 'Export Grade A',
			),
		),
		'grapes' => array(
			'title'   => __( 'Fresh Table Grapes', 'tasdir' ),
			'order'   => 2,
			'excerpt' => __( 'Crisp, sweet black, green and red seedless table grapes from India, pre-cooled and packed in branded export cartons.', 'tasdir' ),
			'content' => "<!-- wp:paragraph -->\n<p>" . __( 'Our table grapes come from vineyards in India’s main grape belt. Bunches are harvested at the right sugar level, trimmed, pre-cooled and packed with SO₂ pads for long-distance sea freight.', 'tasdir' ) . "</p>\n<!-- /wp:paragraph -->\n\n<!-- wp:heading -->\n<h2>" . __( 'Varieties', 'tasdir' ) . "</h2>\n<!-- /wp:heading -->\n\n<!-- wp:list -->\n<ul><li>" . __( 'Black seedless (Sharad, Krishna, Jumbo)', 'tasdir' ) . '</li><li>' . __( 'Green seedless (Thompson, Sonaka)', 'tasdir' ) . '</li><li>' . __( 'Red globe / Flame seedless', 'tasdir' ) . "</li></ul>\n<!-- /wp:list -->",
			'specs'   => array(
				'varieties'  => 'Black, Green & Red seedless',
				'origin'     => 'India',
				'packaging'  => '4.5 kg / 5 kg / 8 kg cartons',
				'moq'        => '1 × 40 ft reefer',
				'season'     => 'January – May',
				'shelf_life' => '45–60 days in cold chain',
				'grade'      => 'Export Grade A',
			),
		),
	);

	$has_products = get_posts(
		array(
			'post_type'      => 'tasdir_product',
			'post_status'    => 'any',
			'posts_per_page' => 1,
			'fields'         => 'ids',
		)
	);
	if ( ! $has_products ) {
		foreach ( $products as $slug => $p ) {
			$id = wp_insert_post(
				array(
					'post_type'    => 'tasdir_product',
					'post_status'  => 'publish',
					'post_name'    => $slug,
					'post_title'   => $p['title'],
					'post_excerpt' => $p['excerpt'],
					'post_content' => $p['content'],
					'menu_order'   => $p['order'],
				)
			);
			if ( $id && ! is_wp_error( $id ) ) {
				foreach ( $p['specs'] as $k => $v ) {
					update_post_meta( $id, '_tasdir_' . $k, $v );
				}
			}
		}
	}

	$pages = array(
		'about-us'       => array(
			__( 'About Us', 'tasdir' ),
			"<!-- wp:paragraph -->\n<p>" . __( 'Tasdir Import Export Private Limited is an Indian exporter of premium fresh fruit. We work directly with growers to source, grade, pack and ship bananas and table grapes to importers, wholesalers and retail chains abroad.', 'tasdir' ) . "</p>\n<!-- /wp:paragraph -->\n\n<!-- wp:heading -->\n<h2>" . __( 'Our promise', 'tasdir' ) . "</h2>\n<!-- /wp:heading -->\n\n<!-- wp:list -->\n<ul><li>" . __( 'Farm-direct sourcing and full traceability', 'tasdir' ) . '</li><li>' . __( 'Hand grading to export standards', 'tasdir' ) . '</li><li>' . __( 'Branded, ventilated export cartons', 'tasdir' ) . '</li><li>' . __( 'Cold-chain logistics and complete documentation', 'tasdir' ) . '</li><li>' . __( 'Fast, transparent communication on WhatsApp', 'tasdir' ) . "</li></ul>\n<!-- /wp:list -->",
		),
		'export-process' => array(
			__( 'Export Process', 'tasdir' ),
			"<!-- wp:paragraph -->\n<p>" . __( 'From enquiry to delivery, here is how a Tasdir shipment works.', 'tasdir' ) . "</p>\n<!-- /wp:paragraph -->\n\n<!-- wp:list {\"ordered\":true} -->\n<ol><li><strong>" . __( 'Enquiry & quotation', 'tasdir' ) . '</strong> — ' . __( 'share product, volume and destination port; we send a quotation.', 'tasdir' ) . '</li><li><strong>' . __( 'Proforma & payment terms', 'tasdir' ) . '</strong> — ' . __( 'Incoterms, packing and shipment date confirmed in writing.', 'tasdir' ) . '</li><li><strong>' . __( 'Harvest, grading & packing', 'tasdir' ) . '</strong> — ' . __( 'fruit is harvested to order, graded and packed in Tasdir cartons.', 'tasdir' ) . '</li><li><strong>' . __( 'Pre-cooling & reefer loading', 'tasdir' ) . '</strong> — ' . __( 'cold chain maintained from pack-house to port.', 'tasdir' ) . '</li><li><strong>' . __( 'Documents & shipment tracking', 'tasdir' ) . '</strong> — ' . __( 'invoice, packing list, phytosanitary certificate, certificate of origin and B/L shared promptly.', 'tasdir' ) . "</li></ol>\n<!-- /wp:list -->",
		),
		'contact'        => array( __( 'Contact', 'tasdir' ), '' ),
		'privacy-policy-tasdir' => array(
			__( 'Privacy Policy', 'tasdir' ),
			"<!-- wp:paragraph -->\n<p>" . __( 'When you send an enquiry we store the details you provide (name, phone, email, company, country and order details) to reply to you and prepare quotations. We do not sell your data. This site uses Google Analytics and the Meta Pixel to measure visits and advertising performance. Contact us to have your data removed.', 'tasdir' ) . "</p>\n<!-- /wp:paragraph -->",
		),
	);

	foreach ( $pages as $slug => $page ) {
		if ( get_page_by_path( $slug ) ) {
			continue;
		}
		$args = array(
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_name'    => $slug,
			'post_title'   => $page[0],
			'post_content' => $page[1],
		);
		$id   = wp_insert_post( $args );
		if ( 'contact' === $slug && $id && ! is_wp_error( $id ) ) {
			update_post_meta( $id, '_wp_page_template', 'templates/contact.php' );
		}
		if ( 'privacy-policy-tasdir' === $slug && $id && ! is_wp_error( $id ) && ! get_option( 'wp_page_for_privacy_policy' ) ) {
			update_option( 'wp_page_for_privacy_policy', $id );
		}
	}

	if ( 'Just another WordPress site' === get_option( 'blogdescription' ) || '' === get_option( 'blogdescription' ) ) {
		update_option( 'blogdescription', __( 'Fresh fruit exporter from India', 'tasdir' ) );
	}

	update_option( 'tasdir_starter_done', 1 );
	flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'tasdir_starter_content' );
