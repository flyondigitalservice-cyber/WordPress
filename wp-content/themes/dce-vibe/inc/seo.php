<?php
/**
 * Lightweight SEO: editable meta title/description per page, Open Graph, and JSON-LD schema
 * (LocalBusiness with parent organisation, Service, FAQPage, BreadcrumbList).
 * Automatically steps aside when Yoast, Rank Math, AIOSEO or SEOPress is active.
 *
 * @package dce-vibe
 */

defined( 'ABSPATH' ) || exit;

function dce_seo_plugin_active() {
	return defined( 'WPSEO_VERSION' ) || class_exists( 'RankMath' ) || defined( 'AIOSEO_VERSION' ) || defined( 'SEOPRESS_VERSION' ) || defined( 'THE_SEO_FRAMEWORK_VERSION' ) || defined( 'SURERANK_VERSION' ) || defined( 'SURERANK_FILE' );
}

/* ---------- Meta box ---------- */
add_action( 'init', 'dce_register_seo_meta' );
function dce_register_seo_meta() {
	foreach ( array( 'page', 'post' ) as $type ) {
		foreach ( array( '_dce_seo_title', '_dce_seo_desc', '_dce_service_name' ) as $key ) {
			register_post_meta(
				$type,
				$key,
				array(
					'type'              => 'string',
					'single'            => true,
					'show_in_rest'      => true,
					'sanitize_callback' => 'sanitize_text_field',
					'auth_callback'     => fn() => current_user_can( 'edit_posts' ),
				)
			);
		}
		register_post_meta(
			$type,
			'_dce_noindex',
			array(
				'type'          => 'boolean',
				'single'        => true,
				'show_in_rest'  => true,
				'auth_callback' => fn() => current_user_can( 'edit_posts' ),
			)
		);
	}
}

add_action( 'add_meta_boxes', 'dce_seo_metabox' );
function dce_seo_metabox() {
	if ( dce_seo_plugin_active() ) {
		return;
	}
	foreach ( array( 'page', 'post' ) as $type ) {
		add_meta_box( 'dce_seo', __( 'SEO — search result title & description', 'dce-vibe' ), 'dce_seo_metabox_html', $type, 'normal', 'high' );
	}
}

function dce_seo_metabox_html( $post ) {
	wp_nonce_field( 'dce_seo_save', 'dce_seo_nonce' );
	$title = get_post_meta( $post->ID, '_dce_seo_title', true );
	$desc  = get_post_meta( $post->ID, '_dce_seo_desc', true );
	$svc   = get_post_meta( $post->ID, '_dce_service_name', true );
	$noidx = get_post_meta( $post->ID, '_dce_noindex', true );
	?>
	<p><label for="dce_seo_title"><strong><?php esc_html_e( 'Meta title', 'dce-vibe' ); ?></strong> <span class="description">(<?php esc_html_e( 'ideal 50–60 characters; leave empty to use the page title', 'dce-vibe' ); ?>)</span></label><br>
	<input type="text" class="widefat" id="dce_seo_title" name="dce_seo_title" value="<?php echo esc_attr( $title ); ?>" maxlength="90"></p>
	<p><label for="dce_seo_desc"><strong><?php esc_html_e( 'Meta description', 'dce-vibe' ); ?></strong> <span class="description">(<?php esc_html_e( 'ideal 140–160 characters', 'dce-vibe' ); ?>)</span></label><br>
	<textarea class="widefat" id="dce_seo_desc" name="dce_seo_desc" rows="3" maxlength="200"><?php echo esc_textarea( $desc ); ?></textarea></p>
	<p><label for="dce_service_name"><strong><?php esc_html_e( 'Service name for schema', 'dce-vibe' ); ?></strong> <span class="description">(<?php esc_html_e( 'e.g. "Wave curtains" — adds Service structured data', 'dce-vibe' ); ?>)</span></label><br>
	<input type="text" class="widefat" id="dce_service_name" name="dce_service_name" value="<?php echo esc_attr( $svc ); ?>"></p>
	<p><label><input type="checkbox" name="dce_noindex" value="1" <?php checked( $noidx ); ?>> <?php esc_html_e( 'Hide this page from search engines (noindex)', 'dce-vibe' ); ?></label></p>
	<?php
}

add_action( 'save_post', 'dce_seo_save', 10, 2 );
function dce_seo_save( $post_id, $post ) {
	if ( ! isset( $_POST['dce_seo_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['dce_seo_nonce'] ) ), 'dce_seo_save' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	update_post_meta( $post_id, '_dce_seo_title', sanitize_text_field( wp_unslash( $_POST['dce_seo_title'] ?? '' ) ) );
	update_post_meta( $post_id, '_dce_seo_desc', sanitize_textarea_field( wp_unslash( $_POST['dce_seo_desc'] ?? '' ) ) );
	update_post_meta( $post_id, '_dce_service_name', sanitize_text_field( wp_unslash( $_POST['dce_service_name'] ?? '' ) ) );
	update_post_meta( $post_id, '_dce_noindex', ! empty( $_POST['dce_noindex'] ) );
}

/* ---------- Output ---------- */
function dce_seo_description() {
	if ( is_singular() ) {
		$id   = get_queried_object_id();
		$desc = get_post_meta( $id, '_dce_seo_desc', true );
		if ( ! $desc ) {
			$post = get_post( $id );
			$desc = has_excerpt( $id ) ? get_the_excerpt( $id ) : wp_trim_words( wp_strip_all_tags( strip_shortcodes( excerpt_remove_blocks( $post->post_content ) ) ), 28, '…' );
		}
		return trim( wp_strip_all_tags( $desc ) );
	}
	if ( is_front_page() || is_home() ) {
		return get_bloginfo( 'description' );
	}
	if ( is_category() || is_tag() ) {
		return wp_strip_all_tags( term_description() );
	}
	return '';
}

add_filter( 'pre_get_document_title', 'dce_seo_title_filter', 20 );
function dce_seo_title_filter( $title ) {
	if ( dce_seo_plugin_active() || ! is_singular() ) {
		return $title;
	}
	$custom = get_post_meta( get_queried_object_id(), '_dce_seo_title', true );
	return $custom ? $custom : $title;
}

add_filter( 'document_title_separator', fn() => '|' );

add_filter( 'wp_robots', 'dce_seo_robots' );
function dce_seo_robots( $robots ) {
	if ( ! dce_seo_plugin_active() && is_singular() && get_post_meta( get_queried_object_id(), '_dce_noindex', true ) ) {
		$robots['noindex']  = true;
		$robots['nofollow'] = false;
	} elseif ( ! dce_seo_plugin_active() ) {
		$robots['max-image-preview'] = 'large';
	}
	return $robots;
}

add_action( 'wp_head', 'dce_seo_head', 2 );
function dce_seo_head() {
	if ( dce_seo_plugin_active() ) {
		return;
	}
	$desc  = dce_seo_description();
	$title = wp_get_document_title();
	$url   = is_singular() ? get_permalink() : ( is_front_page() ? home_url( '/' ) : '' );
	$image = '';
	if ( is_singular() && has_post_thumbnail() ) {
		$image = get_the_post_thumbnail_url( null, 'large' );
	}
	if ( $desc ) {
		echo '<meta name="description" content="' . esc_attr( wp_html_excerpt( $desc, 170, '…' ) ) . '">' . "\n";
	}
	echo '<meta property="og:locale" content="en_AE">' . "\n";
	echo '<meta property="og:site_name" content="' . esc_attr( dce_opt( 'brand' ) ) . '">' . "\n";
	echo '<meta property="og:type" content="' . ( is_singular( 'post' ) ? 'article' : 'website' ) . '">' . "\n";
	echo '<meta property="og:title" content="' . esc_attr( $title ) . '">' . "\n";
	if ( $desc ) {
		echo '<meta property="og:description" content="' . esc_attr( wp_html_excerpt( $desc, 200, '…' ) ) . '">' . "\n";
	}
	if ( $url ) {
		echo '<meta property="og:url" content="' . esc_url( $url ) . '">' . "\n";
	}
	if ( $image ) {
		echo '<meta property="og:image" content="' . esc_url( $image ) . '">' . "\n";
	}
	echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
	echo '<meta name="geo.region" content="AE-DU">' . "\n";
	echo '<meta name="geo.placename" content="Dubai">' . "\n";
}

/**
 * Extract FAQ question/answers from core/details blocks inside a .dce-faq group.
 *
 * @param string $content Post content.
 * @return array
 */
function dce_extract_faqs( $content ) {
	$faqs = array();
	if ( ! preg_match_all( '#<details[^>]*class="[^"]*wp-block-details[^"]*"[^>]*>\s*<summary>(.*?)</summary>(.*?)</details>#s', $content, $m, PREG_SET_ORDER ) ) {
		return $faqs;
	}
	foreach ( $m as $row ) {
		$q = trim( wp_strip_all_tags( $row[1] ) );
		$a = trim( preg_replace( '/\s+/', ' ', wp_strip_all_tags( preg_replace( '/<!--.*?-->/s', '', $row[2] ) ) ) );
		if ( $q && $a ) {
			$faqs[] = array( $q, $a );
		}
	}
	return $faqs;
}

add_action( 'wp_head', 'dce_schema', 30 );
function dce_schema() {
	if ( dce_seo_plugin_active() ) {
		return;
	}
	$home  = home_url( '/' );
	$biz   = $home . '#business';
	$logo  = has_custom_logo() ? wp_get_attachment_image_url( get_theme_mod( 'custom_logo' ), 'full' ) : '';
	$same  = array_values( dce_social_links() );
	$areas = array( 'Dubai', 'Abu Dhabi', 'Sharjah', 'Ajman', 'Ras Al Khaimah', 'Al Ain', 'Fujairah', 'Umm Al Quwain' );

	$graph   = array();
	$graph[] = array_filter(
		array(
			'@type'              => array( 'HomeAndConstructionBusiness', 'LocalBusiness' ),
			'@id'                => $biz,
			'name'               => dce_opt( 'brand' ),
			'legalName'          => dce_opt( 'legal_name' ),
			'url'                => $home,
			'logo'               => $logo,
			'image'              => $logo,
			'telephone'          => '+' . preg_replace( '/\D+/', '', dce_opt( 'phone' ) ),
			'email'              => dce_opt( 'email' ),
			'priceRange'         => dce_opt( 'price_range' ),
			'address'            => array(
				'@type'           => 'PostalAddress',
				'streetAddress'   => dce_opt( 'address' ),
				'addressLocality' => 'Dubai',
				'addressRegion'   => 'Dubai',
				'addressCountry'  => 'AE',
			),
			'geo'                => array(
				'@type'     => 'GeoCoordinates',
				'latitude'  => dce_opt( 'geo_lat' ),
				'longitude' => dce_opt( 'geo_lng' ),
			),
			'openingHoursSpecification' => array(
				array(
					'@type'     => 'OpeningHoursSpecification',
					'dayOfWeek' => array( 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday' ),
					'opens'     => '08:00',
					'closes'    => '17:30',
				),
			),
			'areaServed'         => array_map( fn( $a ) => array( '@type' => 'City', 'name' => $a ), $areas ),
			'parentOrganization' => array(
				'@type' => 'Organization',
				'name'  => dce_opt( 'parent_name' ),
				'url'   => dce_opt( 'parent_url' ),
			),
			'sameAs'             => $same ? $same : null,
		)
	);
	$graph[] = array(
		'@type'     => 'WebSite',
		'@id'       => $home . '#website',
		'url'       => $home,
		'name'      => dce_opt( 'brand' ),
		'publisher' => array( '@id' => $biz ),
	);

	if ( is_singular() ) {
		$post = get_queried_object();
		$url  = get_permalink( $post );

		// Breadcrumbs.
		$crumbs = array( array( 'Home', $home ) );
		foreach ( array_reverse( get_post_ancestors( $post ) ) as $anc ) {
			$crumbs[] = array( get_the_title( $anc ), get_permalink( $anc ) );
		}
		if ( ! is_front_page() ) {
			$crumbs[] = array( get_the_title( $post ), $url );
		}
		$list = array();
		foreach ( $crumbs as $i => $c ) {
			$list[] = array(
				'@type'    => 'ListItem',
				'position' => $i + 1,
				'name'     => wp_strip_all_tags( $c[0] ),
				'item'     => $c[1],
			);
		}
		$graph[] = array(
			'@type'           => 'BreadcrumbList',
			'@id'             => $url . '#breadcrumb',
			'itemListElement' => $list,
		);

		$service = get_post_meta( $post->ID, '_dce_service_name', true );
		if ( $service ) {
			$graph[] = array_filter(
				array(
					'@type'       => 'Service',
					'@id'         => $url . '#service',
					'name'        => $service,
					'serviceType' => $service,
					'description' => dce_seo_description(),
					'provider'    => array( '@id' => $biz ),
					'areaServed'  => array_map( fn( $a ) => array( '@type' => 'City', 'name' => $a ), $areas ),
					'url'         => $url,
					'image'       => has_post_thumbnail( $post ) ? get_the_post_thumbnail_url( $post, 'large' ) : null,
				)
			);
		}

		$faqs = dce_extract_faqs( $post->post_content );
		if ( $faqs ) {
			$graph[] = array(
				'@type'      => 'FAQPage',
				'@id'        => $url . '#faq',
				'mainEntity' => array_map(
					fn( $f ) => array(
						'@type'          => 'Question',
						'name'           => $f[0],
						'acceptedAnswer' => array(
							'@type' => 'Answer',
							'text'  => $f[1],
						),
					),
					$faqs
				),
			);
		}
	}

	echo '<script type="application/ld+json">' . wp_json_encode(
		array(
			'@context' => 'https://schema.org',
			'@graph'   => $graph,
		),
		JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
	) . '</script>' . "\n";
}
