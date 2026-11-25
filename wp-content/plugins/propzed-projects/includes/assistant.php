<?php
/**
 * PROPERTY ASSISTANT (chat widget)
 *
 * A guided chat that qualifies the visitor (buy / lease / invest, type, location,
 * budget), shows live matching listings from the theme's API, answers common
 * questions (stamp duty, RERA, loans, site visits) with links to the guides, and
 * sends the lead to Enquiries through the theme's POST /propzed/v1/enquiry
 * (same inbox, email alert and rate limit as the website forms).
 *
 * Runs fully in the browser: no AI subscription, no API key, no visitor data sent
 * anywhere except this website.
 *
 * @package PropzedProjects
 */

defined( 'ABSPATH' ) || exit;

/**
 * Data for the widget.
 *
 * @return array
 */
function pzs_assistant_data() {
	$residential = array( 'villa', 'residential-project', 'apartment', 'villa-plot' );
	$types       = array();
	foreach ( get_terms( array( 'taxonomy' => 'property_type', 'hide_empty' => false ) ) as $t ) {
		$types[] = array(
			'slug'  => $t->slug,
			'name'  => html_entity_decode( $t->name, ENT_QUOTES, 'UTF-8' ),
			'count' => (int) $t->count,
			'home'  => in_array( $t->slug, $residential, true ),
		);
	}

	$locations = array();
	$terms     = get_terms(
		array(
			'taxonomy'   => 'property_location',
			'hide_empty' => false,
		)
	);
	foreach ( is_wp_error( $terms ) ? array() : $terms as $t ) {
		if ( ! $t->parent ) {
			continue;
		}
		$depth = count( get_ancestors( $t->term_id, 'property_location', 'taxonomy' ) );
		if ( 1 === $depth ) {
			$locations[] = array(
				'slug' => $t->slug,
				'name' => html_entity_decode( $t->name, ENT_QUOTES, 'UTF-8' ),
			);
		}
	}

	$guides = array();
	$cat    = get_category_by_slug( pzs_setting( 'guides_category' ) );
	foreach ( get_posts( array_filter( array( 'numberposts' => 8, 'cat' => $cat ? $cat->term_id : 0 ) ) ) as $p ) {
		if ( 'hello-world' === $p->post_name ) {
			continue;
		}
		$guides[] = array(
			'title' => html_entity_decode( get_the_title( $p ), ENT_QUOTES, 'UTF-8' ),
			'url'   => get_permalink( $p ),
			'slug'  => $p->post_name,
		);
	}

	$context = null;
	if ( is_singular( 'property' ) ) {
		$id      = get_queried_object_id();
		$context = array(
			'id'        => $id,
			'title'     => html_entity_decode( get_the_title( $id ), ENT_QUOTES, 'UTF-8' ),
			'price'     => propzed_price( $id ),
			'sold'      => (bool) pzs_sale_state( $id ),
			'inventory' => (bool) get_post_meta( $id, '_pzs_inventory', true ),
		);
	}

	return array(
		'name'      => pzs_setting( 'assistant_name' ),
		'hello'     => pzs_setting( 'assistant_hello' ),
		'api'       => rest_url( 'propzed/v1/' ),
		'phone'     => propzed_option( 'phone' ),
		'tel'       => propzed_phone_link(),
		'whatsapp'  => preg_replace( '/\D/', '', (string) propzed_option( 'whatsapp' ) ),
		'listing'   => propzed_listing_url(),
		'sell'      => propzed_template_url( 'templates/template-sell.php' ),
		'privacy'   => get_privacy_policy_url(),
		'types'     => $types,
		'locations' => $locations,
		'budgets'   => array(
			'home'       => array(
				'0-5000000'         => 'Under ₹ 50 L',
				'5000000-10000000'  => '₹ 50 L – 1 Cr',
				'10000000-20000000' => '₹ 1 – 2 Cr',
				'20000000-50000000' => '₹ 2 – 5 Cr',
				'50000000-'         => 'Above ₹ 5 Cr',
			),
			'commercial' => propzed_budget_options(),
		),
		'guides'    => $guides,
		'context'   => $context,
	);
}

add_action(
	'wp_enqueue_scripts',
	static function () {
		if ( ! pzs_setting( 'assistant' ) || is_admin() ) {
			return;
		}
		wp_enqueue_script( 'pzs-assistant', PZS_URL . 'assets/js/pzs-assistant.js', array(), PZS_VERSION, true );
		wp_add_inline_script( 'pzs-assistant', 'window.pzsChat = ' . wp_json_encode( pzs_assistant_data(), JSON_HEX_TAG | JSON_UNESCAPED_UNICODE ) . ';', 'before' );
	},
	30
);
