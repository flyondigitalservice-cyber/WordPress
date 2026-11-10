<?php
/**
 * Builder / developer brand pages.
 *
 *   /builder/lodha/   One page per builder: logo, about, facts and ALL of its projects.
 *   /builders/        Directory of every builder that has a live project.
 *
 * HOW A PROPERTY IS LINKED TO A BUILDER
 *   The theme's "Developer / Builder" field on the property (Promotion & New
 *   Project box) is the single source of truth. When a property is saved, the
 *   matching Builder is attached automatically. A builder page is created the
 *   first time a PUBLISHED property names it.
 *
 * EDIT A BUILDER PAGE
 *   Properties > Builders: logo, tagline, about text, website, year, head office,
 *   and "Featured" (shown first on the homepage strip and the directory).
 *
 * @package PropzedPublisher
 */

defined( 'ABSPATH' ) || exit;

const PZP_BUILDER_TAX = 'property_builder';

/**
 * Register the Builder taxonomy on properties.
 */
function pzp_register_builder_taxonomy() {
	register_taxonomy(
		PZP_BUILDER_TAX,
		'property',
		array(
			'labels'            => array(
				'name'          => __( 'Builders', 'propzed-publisher' ),
				'singular_name' => __( 'Builder', 'propzed-publisher' ),
				'menu_name'     => __( 'Builders', 'propzed-publisher' ),
				'search_items'  => __( 'Search Builders', 'propzed-publisher' ),
				'all_items'     => __( 'All Builders', 'propzed-publisher' ),
				'edit_item'     => __( 'Edit Builder', 'propzed-publisher' ),
				'add_new_item'  => __( 'Add New Builder', 'propzed-publisher' ),
				'not_found'     => __( 'No builders found', 'propzed-publisher' ),
			),
			'public'            => true,
			'hierarchical'      => false,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'show_tagcloud'     => false,
			'meta_box_cb'       => false, // Linked through the "Developer / Builder" field instead.
			'rewrite'           => array(
				'slug'       => 'builder',
				'with_front' => false,
			),
		)
	);
}
add_action( 'init', 'pzp_register_builder_taxonomy', 11 );

/* -------------------------------------------------------------------------
 * Linking properties to builders
 * ---------------------------------------------------------------------- */

/**
 * Find a builder by name (case-insensitive) or slug.
 *
 * @param string $name Builder name as typed on the property.
 * @return WP_Term|null
 */
function pzp_find_builder( $name ) {
	$name = trim( (string) $name );
	if ( '' === $name ) {
		return null;
	}
	$terms = get_terms(
		array(
			'taxonomy'   => PZP_BUILDER_TAX,
			'name'       => $name,
			'hide_empty' => false,
			'number'     => 1,
		)
	);
	if ( $terms && ! is_wp_error( $terms ) ) {
		return $terms[0];
	}
	$term = get_term_by( 'slug', sanitize_title( $name ), PZP_BUILDER_TAX );
	return $term ? $term : null;
}

/**
 * Attach a property to the builder named in its "Developer / Builder" field.
 *
 * @param int $post_id Property ID.
 */
function pzp_sync_builder( $post_id ) {
	if ( 'property' !== get_post_type( $post_id ) ) {
		return;
	}
	$name = trim( (string) get_post_meta( $post_id, '_pz_developer', true ) );
	$term = pzp_find_builder( $name );

	// Builder pages are only created by live listings, never by unreviewed submissions.
	if ( ! $term && '' !== $name && 'publish' === get_post_status( $post_id ) ) {
		$created = wp_insert_term( $name, PZP_BUILDER_TAX );
		if ( ! is_wp_error( $created ) ) {
			$term = get_term( $created['term_id'], PZP_BUILDER_TAX );
		}
	}

	wp_set_object_terms( $post_id, $term ? array( (int) $term->term_id ) : array(), PZP_BUILDER_TAX );
	pzp_flush_builder_cache();
}

add_action(
	'save_post_property',
	static function ( $post_id ) {
		if ( wp_is_post_revision( $post_id ) || ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) ) {
			return;
		}
		pzp_sync_builder( $post_id );
	},
	20 // After the theme has saved the property fields (priority 10).
);

/**
 * Link every existing property once, right after the plugin is first enabled.
 * Can be run again from Properties > Publisher Settings.
 *
 * @return int Number of properties checked.
 */
function pzp_sync_all_builders() {
	$ids = get_posts(
		array(
			'post_type'   => 'property',
			'post_status' => array( 'publish', 'pending', 'draft' ),
			'numberposts' => 2000,
			'fields'      => 'ids',
			'meta_query'  => array( // phpcs:ignore WordPress.DB.SlowDBQuery
				array(
					'key'     => '_pz_developer',
					'value'   => '',
					'compare' => '!=',
				),
			),
		)
	);
	foreach ( $ids as $id ) {
		pzp_sync_builder( $id );
	}
	update_option( 'pzp_builders_synced', PZP_VERSION );
	pzp_purge_url( pzp_builders_url() );
	return count( $ids );
}

add_action(
	'admin_init',
	static function () {
		if ( ! get_option( 'pzp_builders_synced' ) && current_user_can( 'manage_options' ) && taxonomy_exists( PZP_BUILDER_TAX ) ) {
			pzp_sync_all_builders();
		}
	}
);

/* -------------------------------------------------------------------------
 * Builder data
 * ---------------------------------------------------------------------- */

/**
 * Drop cached builder facts (project counts, cities, types).
 */
function pzp_flush_builder_cache() {
	update_option( 'pzp_cache_salt', time(), false );
}
add_action( 'edited_' . PZP_BUILDER_TAX, 'pzp_flush_builder_cache' );
add_action( 'deleted_post', 'pzp_flush_builder_cache' );
add_action( 'transition_post_status', 'pzp_flush_builder_cache' );

/**
 * Everything a template needs to show one builder.
 *
 * @param WP_Term $term Builder term.
 * @return array
 */
function pzp_builder_data( $term ) {
	$key    = 'pzp_b_' . $term->term_id . '_' . (int) get_option( 'pzp_cache_salt', 0 );
	$cached = get_transient( $key );
	if ( is_array( $cached ) ) {
		return $cached;
	}

	$ids = get_posts(
		array(
			'post_type'     => 'property',
			'post_status'   => 'publish',
			'numberposts'   => 300,
			'fields'        => 'ids',
			'no_found_rows' => true,
			'tax_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery
				array(
					'taxonomy' => PZP_BUILDER_TAX,
					'terms'    => $term->term_id,
				),
			),
		)
	);

	$cities = array();
	$types  = array();
	if ( $ids ) {
		$locations = wp_get_object_terms( $ids, 'property_location' );
		foreach ( is_wp_error( $locations ) ? array() : $locations as $location ) {
			// Region (depth 0) > City (depth 1) > Locality (depth 2): keep the city.
			$ancestors = get_ancestors( $location->term_id, 'property_location', 'taxonomy' );
			$depth     = count( $ancestors );
			if ( 1 === $depth ) {
				$cities[ $location->term_id ] = html_entity_decode( $location->name, ENT_QUOTES );
			} elseif ( $depth >= 2 ) {
				$city = get_term( $ancestors[ $depth - 2 ], 'property_location' );
				if ( $city && ! is_wp_error( $city ) ) {
					$cities[ $city->term_id ] = html_entity_decode( $city->name, ENT_QUOTES );
				}
			}
		}
		$type_terms = wp_get_object_terms( $ids, 'property_type' );
		foreach ( is_wp_error( $type_terms ) ? array() : $type_terms as $type ) {
			$types[ $type->term_id ] = html_entity_decode( $type->name, ENT_QUOTES );
		}
	}

	$logo_id = (int) get_term_meta( $term->term_id, 'pzp_logo', true );
	$link    = get_term_link( $term );

	$data = array(
		'id'          => (int) $term->term_id,
		'slug'        => $term->slug,
		'name'        => html_entity_decode( $term->name, ENT_QUOTES ),
		'link'        => is_wp_error( $link ) ? '' : $link,
		'count'       => count( $ids ),
		'cities'      => array_values( $cities ),
		'types'       => array_values( $types ),
		'logo'        => $logo_id ? (string) wp_get_attachment_image_url( $logo_id, 'medium' ) : '',
		'tagline'     => (string) get_term_meta( $term->term_id, 'pzp_tagline', true ),
		'website'     => (string) get_term_meta( $term->term_id, 'pzp_website', true ),
		'established' => (string) get_term_meta( $term->term_id, 'pzp_established', true ),
		'hq'          => (string) get_term_meta( $term->term_id, 'pzp_hq', true ),
		'featured'    => (bool) get_term_meta( $term->term_id, 'pzp_featured', true ),
		'description' => (string) $term->description,
	);

	set_transient( $key, $data, 12 * HOUR_IN_SECONDS );
	return $data;
}

/**
 * Builders with at least one live project: featured first, then by project count.
 *
 * @param int $limit Maximum number (0 = all).
 * @return array[] List of pzp_builder_data() arrays.
 */
function pzp_get_builders( $limit = 0 ) {
	$terms = get_terms(
		array(
			'taxonomy'   => PZP_BUILDER_TAX,
			'hide_empty' => true,
			'number'     => 300,
		)
	);
	if ( ! $terms || is_wp_error( $terms ) ) {
		return array();
	}
	$builders = array_filter(
		array_map( 'pzp_builder_data', $terms ),
		static function ( $builder ) {
			return $builder['count'] > 0;
		}
	);
	usort(
		$builders,
		static function ( $a, $b ) {
			if ( $a['featured'] !== $b['featured'] ) {
				return $a['featured'] ? -1 : 1;
			}
			if ( $a['count'] !== $b['count'] ) {
				return $b['count'] - $a['count'];
			}
			return strcasecmp( $a['name'], $b['name'] );
		}
	);
	return $limit ? array_slice( $builders, 0, $limit ) : $builders;
}

/**
 * One-line summary, e.g. "4 projects · Navi Mumbai, Thane".
 *
 * @param array $builder Builder data.
 * @return string
 */
function pzp_builder_summary( $builder ) {
	/* translators: %d: number of projects */
	$parts = array( sprintf( _n( '%d project', '%d projects', $builder['count'], 'propzed-publisher' ), $builder['count'] ) );
	if ( $builder['cities'] ) {
		$parts[] = implode( ', ', array_slice( $builder['cities'], 0, 3 ) );
	}
	return implode( ' · ', $parts );
}

/**
 * Logo image, or a tile with the builder's initials when no logo is set.
 *
 * @param array  $builder Builder data.
 * @param string $class   Extra CSS class.
 * @return string HTML.
 */
function pzp_builder_logo( $builder, $class = '' ) {
	if ( $builder['logo'] ) {
		return sprintf(
			'<span class="pzp-logo %1$s"><img src="%2$s" alt="" loading="lazy" width="96" height="96"></span>',
			esc_attr( $class ),
			esc_url( $builder['logo'] )
		);
	}
	return sprintf( '<span class="pzp-logo pzp-logo--mono %1$s" aria-hidden="true">%2$s</span>', esc_attr( $class ), esc_html( pzp_initials( $builder['name'] ) ) );
}

/**
 * Card linking to a builder page (directory, homepage strip).
 *
 * @param array $builder Builder data.
 * @return string HTML.
 */
function pzp_builder_card( $builder ) {
	return sprintf(
		'<a class="pzp-bcard" href="%1$s" data-pzp-name="%5$s">%2$s<span class="pzp-bcard__text"><strong>%3$s</strong><small>%4$s</small></span>%6$s</a>',
		esc_url( $builder['link'] ),
		pzp_builder_logo( $builder ), // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in pzp_builder_logo().
		esc_html( $builder['name'] ),
		esc_html( pzp_builder_summary( $builder ) ),
		esc_attr( strtolower( $builder['name'] ) ),
		propzed_icon( 'arrow' ) // phpcs:ignore WordPress.Security.EscapeOutput -- static SVG.
	);
}

/* -------------------------------------------------------------------------
 * Builder page query, SEO and structured data
 * ---------------------------------------------------------------------- */

/**
 * Builder page: 12 projects per page, optional ?pzp_status=for-sale filter.
 *
 * @param WP_Query $query Query.
 */
function pzp_builder_query( $query ) {
	if ( is_admin() || ! $query->is_main_query() || ! $query->is_tax( PZP_BUILDER_TAX ) ) {
		return;
	}
	$query->set( 'post_type', 'property' );
	$query->set( 'posts_per_page', 12 );

	$status = pzp_builder_status_filter();
	if ( $status ) {
		$tax_query   = (array) $query->get( 'tax_query' );
		$tax_query[] = array(
			'taxonomy' => 'property_status',
			'field'    => 'slug',
			'terms'    => $status,
		);
		$query->set( 'tax_query', $tax_query );
	}
}
add_action( 'pre_get_posts', 'pzp_builder_query' );

/**
 * Listing status chosen on a builder page ('' = all).
 *
 * @return string
 */
function pzp_builder_status_filter() {
	if ( empty( $_GET['pzp_status'] ) || ! is_string( $_GET['pzp_status'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		return '';
	}
	$slug = sanitize_title( wp_unslash( $_GET['pzp_status'] ) ); // phpcs:ignore WordPress.Security.NonceVerification
	return term_exists( $slug, 'property_status' ) ? $slug : '';
}

/**
 * Is one of this plugin's public SEO pages being shown?
 *
 * @return bool
 */
function pzp_is_builder_page() {
	return is_tax( PZP_BUILDER_TAX ) || 'builders' === pzp_current_view();
}

/**
 * Should this plugin print SEO tags? (No, when Yoast / Rank Math etc. is active.)
 *
 * @return bool
 */
function pzp_owns_seo() {
	return ! ( function_exists( 'propzed_seo_plugin_active' ) && propzed_seo_plugin_active() );
}

/**
 * Browser / Google titles.
 */
add_filter(
	'document_title_parts',
	static function ( $parts ) {
		if ( 'publisher' === pzp_current_view() ) {
			$parts['title'] = __( 'Publisher Account: List & Manage Your Properties', 'propzed-publisher' );
			unset( $parts['tagline'] );
		} elseif ( ! pzp_owns_seo() ) {
			return $parts;
		} elseif ( 'builders' === pzp_current_view() ) {
			$parts['title'] = __( 'Top Commercial Builders & Developers in Mumbai, Navi Mumbai & India', 'propzed-publisher' );
			unset( $parts['tagline'] );
		} elseif ( is_tax( PZP_BUILDER_TAX ) ) {
			$builder = pzp_builder_data( get_queried_object() );
			/* translators: 1: builder name 2: number of projects */
			$parts['title'] = sprintf( _n( '%1$s: %2$d Commercial Project, Price & Details', '%1$s: %2$d Commercial Projects, Prices & Details', max( 1, $builder['count'] ), 'propzed-publisher' ), $builder['name'], $builder['count'] );
		}
		return $parts;
	},
	20
);

/**
 * Meta description of a builder page / the directory.
 *
 * @return string
 */
function pzp_seo_description() {
	if ( is_tax( PZP_BUILDER_TAX ) ) {
		$builder = pzp_builder_data( get_queried_object() );
		$text    = $builder['description'] ? $builder['description'] : $builder['tagline'];
		if ( ! $text ) {
			$text = sprintf(
				/* translators: 1: builder name 2: summary such as "4 projects · Navi Mumbai" 3: phone */
				__( 'All commercial projects by %1$s on Propzed (%2$s): prices, sizes, approvals and connectivity. Call %3$s for a site visit.', 'propzed-publisher' ),
				$builder['name'],
				pzp_builder_summary( $builder ),
				propzed_option( 'phone' )
			);
		}
	} else {
		$text = __( 'Browse commercial projects by builder: offices, showrooms, malls, warehouses and plots from leading developers across Mumbai, Navi Mumbai, Thane and India.', 'propzed-publisher' );
	}
	return wp_html_excerpt( trim( preg_replace( '/\s+/', ' ', wp_strip_all_tags( $text ) ) ), 160, '…' );
}

/**
 * Replace the theme's generic head tags with builder-specific ones.
 */
add_action(
	'wp',
	static function () {
		if ( 'publisher' === pzp_current_view() ) {
			remove_action( 'wp_head', 'propzed_seo_head', 2 );
			return;
		}
		if ( pzp_is_builder_page() && pzp_owns_seo() ) {
			remove_action( 'wp_head', 'propzed_seo_head', 2 );
			add_action( 'wp_head', 'pzp_seo_head', 2 );
		}
	}
);

/**
 * Print description, canonical and Open Graph tags.
 */
function pzp_seo_head() {
	$is_builder = is_tax( PZP_BUILDER_TAX );
	$builder    = $is_builder ? pzp_builder_data( get_queried_object() ) : null;
	$canonical  = $is_builder ? $builder['link'] : pzp_builders_url();
	$paged      = (int) get_query_var( 'paged' );
	if ( $is_builder && $paged > 1 && get_option( 'permalink_structure' ) ) {
		$canonical = trailingslashit( $canonical ) . user_trailingslashit( 'page/' . $paged );
	}
	$desc  = pzp_seo_description();
	$image = ( $builder && $builder['logo'] ) ? $builder['logo'] : ( function_exists( 'propzed_seo_image' ) ? propzed_seo_image() : '' );

	printf( '<meta name="description" content="%s">' . "\n", esc_attr( $desc ) );
	printf( '<link rel="canonical" href="%s">' . "\n", esc_url( $canonical ) );

	$og = array(
		'og:site_name'   => get_bloginfo( 'name' ),
		'og:type'        => 'website',
		'og:title'       => wp_get_document_title(),
		'og:description' => $desc,
		'og:url'         => $canonical,
		'og:image'       => $image,
	);
	foreach ( $og as $property => $content ) {
		if ( $content ) {
			printf( '<meta property="%1$s" content="%2$s">' . "\n", esc_attr( $property ), esc_attr( $content ) );
		}
	}
	echo '<meta name="twitter:card" content="summary_large_image">' . "\n";

	// Structured data: the builder as an Organization with its listing page.
	if ( $builder ) {
		$org = array_filter(
			array(
				'@type'        => 'Organization',
				'name'         => $builder['name'],
				'url'          => $builder['website'] ? $builder['website'] : $builder['link'],
				'logo'         => $builder['logo'],
				'description'  => $builder['description'] ? wp_strip_all_tags( $builder['description'] ) : $builder['tagline'],
				'foundingDate' => $builder['established'],
			)
		);
		$schema = array(
			'@context' => 'https://schema.org',
			'@type'    => 'CollectionPage',
			'name'     => wp_get_document_title(),
			'url'      => $canonical,
			'about'    => $org,
		);
		echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput
	}
}

/**
 * The publisher area and empty / filtered builder pages stay out of Google.
 */
add_filter(
	'wp_robots',
	static function ( $robots ) {
		global $wp_query;
		$noindex = 'publisher' === pzp_current_view();
		if ( is_tax( PZP_BUILDER_TAX ) && ( pzp_builder_status_filter() || ! $wp_query->post_count ) ) {
			$noindex = true;
		}
		if ( $noindex ) {
			$robots['noindex'] = true;
			$robots['follow']  = true;
			unset( $robots['max-image-preview'] );
		}
		return $robots;
	},
	20
);

/* -------------------------------------------------------------------------
 * Front-end blocks on existing theme pages
 * ---------------------------------------------------------------------- */

/**
 * Single property: "About the developer" card under the overview, linking to
 * every other project by the same builder.
 *
 * @param string $content Post content.
 * @return string
 */
function pzp_property_builder_block( $content ) {
	if ( ! is_singular( 'property' ) || ! in_the_loop() || ! is_main_query() ) {
		return $content;
	}
	$terms = get_the_terms( get_the_ID(), PZP_BUILDER_TAX );
	if ( ! $terms || is_wp_error( $terms ) ) {
		return $content;
	}
	$builder = pzp_builder_data( $terms[0] );
	if ( ! $builder['link'] ) {
		return $content;
	}

	$others = max( 0, $builder['count'] - 1 );
	$label  = $others
		/* translators: 1: number of other projects 2: builder name */
		? sprintf( _n( 'View %1$d more project by %2$s', 'View %1$d more projects by %2$s', $others, 'propzed-publisher' ), $others, $builder['name'] )
		/* translators: %s: builder name */
		: sprintf( __( 'About %s', 'propzed-publisher' ), $builder['name'] );

	$block  = '<aside class="pzp-devcard" aria-label="' . esc_attr__( 'Developer', 'propzed-publisher' ) . '">';
	$block .= pzp_builder_logo( $builder );
	$block .= '<div class="pzp-devcard__text"><span class="pzp-devcard__eyebrow">' . esc_html__( 'Developer', 'propzed-publisher' ) . '</span>';
	$block .= '<strong>' . esc_html( $builder['name'] ) . '</strong>';
	if ( $builder['tagline'] ) {
		$block .= '<p>' . esc_html( $builder['tagline'] ) . '</p>';
	}
	$block .= '<a class="pzp-devcard__link" href="' . esc_url( $builder['link'] ) . '">' . esc_html( $label ) . ' ' . propzed_icon( 'arrow' ) . '</a>';
	$block .= '</div></aside>';

	return $content . $block;
}
add_filter( 'the_content', 'pzp_property_builder_block', 20 );

/**
 * Homepage: "Top Builders" strip, printed just before the theme's "markets" section.
 */
function pzp_home_builders_strip() {
	static $done = false;
	if ( $done || ! is_front_page() || ! pzp_setting( 'home_builders' ) ) {
		return;
	}
	$done     = true;
	$builders = pzp_get_builders( 8 );
	if ( count( $builders ) < 3 ) {
		return;
	}
	?>
	<section class="pz-section pz-section--alt pzp-home-builders" aria-labelledby="pzp-home-builders-title">
		<div class="pz-container">
			<div class="pz-section__head">
				<div>
					<span class="pz-eyebrow"><?php esc_html_e( 'Browse by developer', 'propzed-publisher' ); ?></span>
					<h2 id="pzp-home-builders-title"><?php esc_html_e( 'Top Builders & Developers', 'propzed-publisher' ); ?></h2>
					<p><?php esc_html_e( 'See every commercial project from a builder on one page.', 'propzed-publisher' ); ?></p>
				</div>
				<a class="pz-link-arrow" href="<?php echo esc_url( pzp_builders_url() ); ?>"><?php esc_html_e( 'All builders', 'propzed-publisher' ); ?> <?php echo propzed_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
			</div>
			<div class="pzp-bgrid">
				<?php
				foreach ( $builders as $builder ) {
					echo pzp_builder_card( $builder ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in pzp_builder_card().
				}
				?>
			</div>
		</div>
	</section>
	<?php
}
add_action( 'get_template_part_template-parts/home/markets', 'pzp_home_builders_strip' );

/* -------------------------------------------------------------------------
 * Admin: extra fields on Properties > Builders
 * ---------------------------------------------------------------------- */

/**
 * Builder fields: meta key => [ label, input type, help ].
 *
 * @return array
 */
function pzp_builder_fields() {
	return array(
		'pzp_tagline'     => array( __( 'Tagline', 'propzed-publisher' ), 'text', __( 'One short line shown under the builder name.', 'propzed-publisher' ) ),
		'pzp_website'     => array( __( 'Website', 'propzed-publisher' ), 'url', '' ),
		'pzp_established' => array( __( 'Established (year)', 'propzed-publisher' ), 'number', '' ),
		'pzp_hq'          => array( __( 'Head office (city)', 'propzed-publisher' ), 'text', '' ),
	);
}

/**
 * Render the builder fields on the add / edit screens.
 *
 * @param WP_Term|string $term Term on the edit screen, taxonomy name on the add screen.
 */
function pzp_builder_form_fields( $term ) {
	$editing = $term instanceof WP_Term;
	$term_id = $editing ? $term->term_id : 0;
	$logo_id = $term_id ? (int) get_term_meta( $term_id, 'pzp_logo', true ) : 0;
	$open    = $editing ? '<tr class="form-field"><th scope="row">%s</th><td>' : '<div class="form-field">%s';
	$close   = $editing ? '</td></tr>' : '</div>';

	wp_nonce_field( 'pzp_builder_fields', 'pzp_builder_nonce' );

	printf( $open, '<label for="pzp_logo">' . esc_html__( 'Logo', 'propzed-publisher' ) . '</label>' ); // phpcs:ignore WordPress.Security.EscapeOutput
	echo '<input type="hidden" id="pzp_logo" name="pzp_logo" value="' . esc_attr( $logo_id ) . '">';
	echo '<span id="pzp-logo-preview" style="display:block;margin-bottom:8px">' . ( $logo_id ? wp_get_attachment_image( $logo_id, 'thumbnail' ) : '' ) . '</span>';
	echo '<button type="button" class="button" id="pzp-logo-pick">' . esc_html__( 'Choose logo', 'propzed-publisher' ) . '</button> ';
	echo '<button type="button" class="button-link" id="pzp-logo-clear">' . esc_html__( 'Remove', 'propzed-publisher' ) . '</button>';
	echo '<p class="description">' . esc_html__( 'Square image, at least 300 × 300 px. Without a logo the initials are shown.', 'propzed-publisher' ) . '</p>';
	echo $close; // phpcs:ignore WordPress.Security.EscapeOutput

	foreach ( pzp_builder_fields() as $key => $field ) {
		$value = $term_id ? get_term_meta( $term_id, $key, true ) : '';
		printf( $open, '<label for="' . esc_attr( $key ) . '">' . esc_html( $field[0] ) . '</label>' ); // phpcs:ignore WordPress.Security.EscapeOutput
		printf( '<input type="%1$s" id="%2$s" name="%2$s" value="%3$s">', esc_attr( $field[1] ), esc_attr( $key ), esc_attr( $value ) );
		if ( $field[2] ) {
			echo '<p class="description">' . esc_html( $field[2] ) . '</p>';
		}
		echo $close; // phpcs:ignore WordPress.Security.EscapeOutput
	}

	printf( $open, esc_html__( 'Featured', 'propzed-publisher' ) ); // phpcs:ignore WordPress.Security.EscapeOutput
	printf(
		'<label><input type="checkbox" name="pzp_featured" value="1" %1$s> %2$s</label>',
		checked( $term_id ? get_term_meta( $term_id, 'pzp_featured', true ) : '', '1', false ),
		esc_html__( 'Show first on the homepage strip and the builders directory', 'propzed-publisher' )
	);
	echo $close; // phpcs:ignore WordPress.Security.EscapeOutput
}
add_action( PZP_BUILDER_TAX . '_add_form_fields', 'pzp_builder_form_fields' );
add_action( PZP_BUILDER_TAX . '_edit_form_fields', 'pzp_builder_form_fields' );

/**
 * Save the builder fields.
 *
 * @param int $term_id Term ID.
 */
function pzp_save_builder_fields( $term_id ) {
	if ( ! isset( $_POST['pzp_builder_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['pzp_builder_nonce'] ), 'pzp_builder_fields' ) || ! current_user_can( 'manage_categories' ) ) {
		return;
	}
	update_term_meta( $term_id, 'pzp_logo', isset( $_POST['pzp_logo'] ) ? absint( $_POST['pzp_logo'] ) : 0 );
	update_term_meta( $term_id, 'pzp_featured', empty( $_POST['pzp_featured'] ) ? '' : '1' );

	foreach ( pzp_builder_fields() as $key => $field ) {
		$raw = isset( $_POST[ $key ] ) ? wp_unslash( $_POST[ $key ] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput -- sanitised below.
		if ( 'url' === $field[1] ) {
			$value = esc_url_raw( $raw );
		} elseif ( 'number' === $field[1] ) {
			$value = absint( $raw ) ? (string) absint( $raw ) : '';
		} else {
			$value = sanitize_text_field( $raw );
		}
		update_term_meta( $term_id, $key, $value );
	}
	pzp_flush_builder_cache();
}
add_action( 'created_' . PZP_BUILDER_TAX, 'pzp_save_builder_fields' );
add_action( 'edited_' . PZP_BUILDER_TAX, 'pzp_save_builder_fields' );

/**
 * Media picker for the logo field (Builders screens only).
 */
add_action(
	'admin_enqueue_scripts',
	static function () {
		$screen = get_current_screen();
		if ( ! $screen || PZP_BUILDER_TAX !== $screen->taxonomy ) {
			return;
		}
		wp_enqueue_media();
		wp_add_inline_script(
			'media-editor',
			"jQuery(function($){var frame;$('#pzp-logo-pick').on('click',function(e){e.preventDefault();if(!frame){frame=wp.media({title:'" . esc_js( __( 'Builder logo', 'propzed-publisher' ) ) . "',library:{type:'image'},multiple:false});frame.on('select',function(){var a=frame.state().get('selection').first().toJSON();$('#pzp_logo').val(a.id);var u=(a.sizes&&a.sizes.thumbnail)?a.sizes.thumbnail.url:a.url;$('#pzp-logo-preview').html($('<img>',{src:u,alt:'',width:96,height:96}));});}frame.open();});$('#pzp-logo-clear').on('click',function(e){e.preventDefault();$('#pzp_logo').val('');$('#pzp-logo-preview').empty();});});"
		);
	}
);

/**
 * Builders list: tell the admin how builders are linked.
 */
add_action(
	PZP_BUILDER_TAX . '_pre_add_form',
	static function () {
		echo '<div class="notice notice-info inline"><p>';
		echo esc_html__( 'A builder page lists every published property whose "Developer / Builder" field matches the builder name. To attach a property, type the same name in that field on the property.', 'propzed-publisher' );
		echo ' <a href="' . esc_url( pzp_builders_url() ) . '" target="_blank" rel="noopener">' . esc_html__( 'View the builders directory', 'propzed-publisher' ) . '</a>';
		echo '</p></div>';
	}
);
