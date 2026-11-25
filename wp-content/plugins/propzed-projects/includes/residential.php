<?php
/**
 * Villas and residential projects.
 *
 * The theme builds its meta boxes, saving and the "Specifications" table from
 * propzed_property_fields(). This adds a "Residential / Villa details" group
 * through the theme's own filter, so the fields appear in wp-admin and on the
 * property page with no template change.
 *
 * Property types added on activation: Villas & Bungalows, Residential Projects,
 * Flats & Apartments, Villa Plots (NA). SEO landing pages such as
 * /villa-in-karjat/ and /residential-project-for-sale-in-ulwe/ work automatically.
 *
 * @package PropzedProjects
 */

defined( 'ABSPATH' ) || exit;

add_filter(
	'propzed_property_fields',
	static function ( $fields ) {
		$residential = array(
			'title'         => __( 'Residential / Villa details', 'propzed-projects' ),
			'show_in_specs' => true,
			'fields'        => array(
				'configurations' => array( 'label' => __( 'Configurations', 'propzed-projects' ), 'type' => 'text', 'placeholder' => '1, 2 & 3 BHK  /  4 BHK villas' ),
				'carpet_range'   => array( 'label' => __( 'Carpet area range', 'propzed-projects' ), 'type' => 'text', 'placeholder' => '410 – 980 sq.ft' ),
				'plot_size'      => array( 'label' => __( 'Plot size (villas)', 'propzed-projects' ), 'type' => 'text', 'placeholder' => '5,000 sq.ft' ),
				'bedrooms'       => array( 'label' => __( 'Bedrooms', 'propzed-projects' ), 'type' => 'text', 'placeholder' => '4' ),
				'bathrooms'      => array( 'label' => __( 'Bathrooms', 'propzed-projects' ), 'type' => 'text' ),
				'towers'         => array( 'label' => __( 'Towers / phases', 'propzed-projects' ), 'type' => 'text', 'placeholder' => '3 towers, G + 22' ),
				'total_units'    => array( 'label' => __( 'Total units', 'propzed-projects' ), 'type' => 'text' ),
				'project_acres'  => array( 'label' => __( 'Project land', 'propzed-projects' ), 'type' => 'text', 'placeholder' => '6 acres' ),
				'rera_no'        => array( 'label' => __( 'MahaRERA number', 'propzed-projects' ), 'type' => 'text', 'placeholder' => 'P5200000XXXX' ),
				'furnishing'     => array(
					'label'   => __( 'Furnishing', 'propzed-projects' ),
					'type'    => 'select',
					'options' => array(
						''       => '—',
						'bare'   => __( 'Bare shell / unfurnished', 'propzed-projects' ),
						'semi'   => __( 'Semi-furnished', 'propzed-projects' ),
						'fully'  => __( 'Fully furnished', 'propzed-projects' ),
					),
				),
				'pool'           => array( 'label' => __( 'Private pool', 'propzed-projects' ), 'type' => 'text', 'placeholder' => 'Yes, 12 × 5 m' ),
				'amenities'      => array( 'label' => __( 'Amenities', 'propzed-projects' ), 'type' => 'text', 'wide' => true, 'placeholder' => 'Clubhouse, pool, gym, kids play area, EV charging' ),
			),
		);

		// Insert after "Specifications".
		$out = array();
		foreach ( $fields as $key => $group ) {
			$out[ $key ] = $group;
			if ( 'specs' === $key ) {
				$out['residential'] = $residential;
			}
		}
		if ( ! isset( $out['residential'] ) ) {
			$out['residential'] = $residential;
		}

		// More useful zoning / status options for homes.
		if ( isset( $out['specs']['fields']['zoning']['options'] ) ) {
			$out['specs']['fields']['zoning']['options']['residential'] = __( 'Residential', 'propzed-projects' );
		}
		if ( isset( $out['specs']['fields']['possession']['options'] ) ) {
			$out['specs']['fields']['possession']['options']['new-launch'] = __( 'New launch / pre-launch', 'propzed-projects' );
		}
		return $out;
	}
);

/**
 * Short labels for SEO landing page headings, e.g. "Villas for Sale in Karjat".
 */
add_filter(
	'propzed_seo_type_labels',
	static function ( $labels ) {
		return array_merge(
			$labels,
			array(
				'villa'               => __( 'Villas', 'propzed-projects' ),
				'residential-project' => __( 'Residential Projects', 'propzed-projects' ),
				'apartment'           => __( 'Flats', 'propzed-projects' ),
				'villa-plot'          => __( 'Villa Plots', 'propzed-projects' ),
			)
		);
	}
);

/**
 * Is this a residential listing (villa, flat, project)?
 *
 * @param int|null $post_id Post ID.
 * @return bool
 */
function pzs_is_residential( $post_id = null ) {
	$slugs = wp_get_object_terms( $post_id ? $post_id : get_the_ID(), 'property_type', array( 'fields' => 'slugs' ) );
	return ! is_wp_error( $slugs ) && (bool) array_intersect( $slugs, array( 'villa', 'residential-project', 'apartment', 'villa-plot' ) );
}

/**
 * Residential pages: the sidebar form asks for floor plans too
 * (theme string swapped via gettext, theme text domain only).
 */
add_filter(
	'gettext_propzed',
	static function ( $translation, $text ) {
		if ( 'Get Price & Site Visit' === $text && is_singular( 'property' ) && pzs_is_residential( get_queried_object_id() ) ) {
			return __( 'Get Price, Floor Plans & Site Visit', 'propzed-projects' );
		}
		return $translation;
	},
	10,
	2
);
