<?php
/**
 * Activation: new listing statuses, residential property types and the
 * weekend-home / budget-housing locations the guides target.
 * Safe to run again; existing terms are never changed.
 *
 * @package PropzedProjects
 */

defined( 'ABSPATH' ) || exit;

/**
 * Terms this plugin adds.
 *
 * @return array
 */
function pzs_default_terms() {
	return array(
		'property_status'   => array(
			'sold'       => array( 'Sold', '', '' ),
			'leased-out' => array( 'Leased Out', '', '' ),
		),
		'property_type'     => array(
			'villa'               => array( 'Villas & Bungalows', '', '' ),
			'residential-project' => array( 'Residential Projects', '', '' ),
			'apartment'           => array( 'Flats & Apartments', '', '' ),
			'villa-plot'          => array( 'Villa Plots (NA)', '', '' ),
		),
		// slug => [ name, parent slug, description ].
		'property_location' => array(
			'karjat'      => array( 'Karjat', 'raigad', 'Weekend villas, farmhouses and NA plots at the foot of Matheran, on the Central Railway line.' ),
			'khopoli'     => array( 'Khopoli', 'raigad', 'Mumbai–Pune Expressway town with villa plots, second homes and industrial land.' ),
			'neral'       => array( 'Neral', 'raigad', 'Budget flats and plots on the Karjat line, gateway to Matheran.' ),
			'pen'         => array( 'Pen', 'raigad', 'Plots and second homes on the Mumbai–Goa highway towards Alibaug.' ),
			'lonavala'    => array( 'Lonavala & Khandala', 'pune', 'Hill-station villas and holiday homes on the Mumbai–Pune Expressway.' ),
			'dronagiri'   => array( 'Dronagiri', 'navi-mumbai', 'CIDCO node near JNPA and the Atal Setu landing.' ),
			'kamothe'     => array( 'Kamothe', 'navi-mumbai', 'Mid-budget flats between Kharghar and Panvel.' ),
			'badlapur'    => array( 'Badlapur & Ambernath', 'mmr', 'Budget flats on the Central Railway line beyond Kalyan.' ),
			'mira-road'   => array( 'Mira Road & Bhayandar', 'mmr', 'Budget flats on the Western Railway line north of Dahisar.' ),
			'kandivali'   => array( 'Kandivali & Borivali', 'mumbai', '' ),
			'malad'       => array( 'Malad', 'mumbai', '' ),
			'chembur'     => array( 'Chembur', 'mumbai', '' ),
			'ghatkopar'   => array( 'Ghatkopar', 'mumbai', '' ),
		),
	);
}

/**
 * Create the terms. Needs the theme's taxonomies, so it runs on init.
 */
function pzs_install_terms() {
	foreach ( pzs_default_terms() as $taxonomy => $terms ) {
		if ( ! taxonomy_exists( $taxonomy ) ) {
			continue;
		}
		foreach ( $terms as $slug => $t ) {
			if ( get_term_by( 'slug', $slug, $taxonomy ) ) {
				continue;
			}
			$parent = 0;
			if ( $t[1] ) {
				$p      = get_term_by( 'slug', $t[1], $taxonomy );
				$parent = $p ? (int) $p->term_id : 0;
			}
			wp_insert_term(
				$t[0],
				$taxonomy,
				array(
					'slug'        => $slug,
					'parent'      => $parent,
					'description' => $t[2],
				)
			);
		}
	}
	update_option( 'pzs_terms_version', PZS_VERSION );
}

/**
 * Activation hook: terms are added on the next init (taxonomies exist then).
 */
function pzs_activate() {
	delete_option( 'pzs_terms_version' );
	update_option( 'pzs_flush_rewrites', 1 );
	wp_schedule_single_event( time() + 30, 'pzs_regenerate_llms' );
}

/**
 * Deactivation: stop scheduled jobs. Data stays in the database.
 */
function pzs_deactivate() {
	wp_clear_scheduled_hook( 'pzs_regenerate_llms' );
	flush_rewrite_rules( false );
}

add_action(
	'init',
	static function () {
		if ( get_option( 'pzs_terms_version' ) !== PZS_VERSION ) {
			pzs_install_terms();
		}
		if ( get_option( 'pzs_flush_rewrites' ) ) {
			delete_option( 'pzs_flush_rewrites' );
			add_action( 'wp_loaded', static function () {
				flush_rewrite_rules( false );
			} );
		}
	},
	30
);
