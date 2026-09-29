<?php
/**
 * Lightweight on-page SEO: an editable "SEO" box on every page/post, meta +
 * Open Graph tags, and JSON-LD (LocalBusiness with parent organisation,
 * Service, BreadcrumbList, FAQPage).
 *
 * If Yoast / Rank Math / AIOSEO / SEOPress / TSF is active, the title, meta and
 * OG output steps aside so there are no duplicates; business schema stays.
 *
 * @package DGF
 */

defined( 'ABSPATH' ) || exit;

const DGF_SEO_FIELDS = array( '_dgf_seo_title', '_dgf_seo_desc', '_dgf_seo_keyword', '_dgf_seo_noindex' );

add_action( 'init', 'dgf_register_seo_meta' );
/**
 * Register meta so it is also editable through the REST API / block editor.
 */
function dgf_register_seo_meta() {
	foreach ( array( 'page', 'post' ) as $type ) {
		foreach ( DGF_SEO_FIELDS as $key ) {
			register_post_meta(
				$type,
				$key,
				array(
					'type'          => 'string',
					'single'        => true,
					'show_in_rest'  => true,
					'auth_callback' => static function () {
						return current_user_can( 'edit_posts' );
					},
				)
			);
		}
	}
}

add_action( 'add_meta_boxes', 'dgf_seo_meta_box' );
/**
 * Add the SEO box.
 */
function dgf_seo_meta_box() {
	foreach ( array( 'page', 'post' ) as $type ) {
		add_meta_box( 'dgf_seo', __( 'SEO (Dubai Gym Flooring)', 'dgf' ), 'dgf_seo_meta_box_html', $type, 'normal', 'high' );
	}
}

/**
 * SEO box markup.
 *
 * @param WP_Post $post Post.
 */
function dgf_seo_meta_box_html( $post ) {
	wp_nonce_field( 'dgf_seo_save', 'dgf_seo_nonce' );
	$title   = get_post_meta( $post->ID, '_dgf_seo_title', true );
	$desc    = get_post_meta( $post->ID, '_dgf_seo_desc', true );
	$kw      = get_post_meta( $post->ID, '_dgf_seo_keyword', true );
	$noindex = get_post_meta( $post->ID, '_dgf_seo_noindex', true );
	if ( dgf_seo_plugin_active() ) {
		echo '<p><em>' . esc_html__( 'An SEO plugin is active, so it controls titles and meta descriptions. These fields are kept as a backup.', 'dgf' ) . '</em></p>';
	}
	?>
	<p>
		<label for="dgf_seo_title"><strong><?php esc_html_e( 'SEO title', 'dgf' ); ?></strong> <span class="description">(<?php esc_html_e( 'aim for 50–60 characters', 'dgf' ); ?>)</span></label>
		<input type="text" class="widefat" id="dgf_seo_title" name="dgf_seo_title" value="<?php echo esc_attr( $title ); ?>" maxlength="90">
	</p>
	<p>
		<label for="dgf_seo_desc"><strong><?php esc_html_e( 'Meta description', 'dgf' ); ?></strong> <span class="description">(<?php esc_html_e( 'aim for 140–160 characters', 'dgf' ); ?>)</span></label>
		<textarea class="widefat" id="dgf_seo_desc" name="dgf_seo_desc" rows="3" maxlength="320"><?php echo esc_textarea( $desc ); ?></textarea>
	</p>
	<p>
		<label for="dgf_seo_keyword"><strong><?php esc_html_e( 'Focus keyword', 'dgf' ); ?></strong></label>
		<input type="text" class="widefat" id="dgf_seo_keyword" name="dgf_seo_keyword" value="<?php echo esc_attr( $kw ); ?>">
	</p>
	<p>
		<label><input type="checkbox" name="dgf_seo_noindex" value="1" <?php checked( '1', $noindex ); ?>> <?php esc_html_e( 'Hide this page from search engines (noindex)', 'dgf' ); ?></label>
	</p>
	<?php
}

add_action( 'save_post', 'dgf_seo_save', 10, 2 );
/**
 * Save the SEO box.
 *
 * @param int     $post_id Post ID.
 * @param WP_Post $post    Post.
 */
function dgf_seo_save( $post_id, $post ) {
	if ( ! isset( $_POST['dgf_seo_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['dgf_seo_nonce'] ), 'dgf_seo_save' ) ) {
		return;
	}
	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! current_user_can( 'edit_post', $post_id ) || wp_is_post_revision( $post ) ) {
		return;
	}
	$map = array(
		'dgf_seo_title'   => '_dgf_seo_title',
		'dgf_seo_desc'    => '_dgf_seo_desc',
		'dgf_seo_keyword' => '_dgf_seo_keyword',
	);
	foreach ( $map as $field => $key ) {
		$value = isset( $_POST[ $field ] ) ? sanitize_textarea_field( wp_unslash( $_POST[ $field ] ) ) : '';
		update_post_meta( $post_id, $key, $value );
	}
	update_post_meta( $post_id, '_dgf_seo_noindex', empty( $_POST['dgf_seo_noindex'] ) ? '' : '1' );
}

/**
 * SEO description for the current request.
 *
 * @return string
 */
function dgf_meta_description() {
	if ( is_singular() ) {
		$id   = get_queried_object_id();
		$desc = get_post_meta( $id, '_dgf_seo_desc', true );
		if ( ! $desc ) {
			$desc = has_excerpt( $id ) ? get_the_excerpt( $id ) : wp_trim_words( wp_strip_all_tags( strip_shortcodes( get_post_field( 'post_content', $id ) ) ), 28, '…' );
		}
		return trim( wp_strip_all_tags( $desc ) );
	}
	return dgf_opt( 'brand_tagline' );
}

add_filter( 'pre_get_document_title', 'dgf_document_title', 20 );
/**
 * Use the custom SEO title when set.
 *
 * @param string $title Title.
 * @return string
 */
function dgf_document_title( $title ) {
	if ( dgf_seo_plugin_active() || ! is_singular() ) {
		return $title;
	}
	$custom = get_post_meta( get_queried_object_id(), '_dgf_seo_title', true );
	return $custom ? wp_strip_all_tags( $custom ) : $title;
}

add_filter( 'wp_robots', 'dgf_robots' );
/**
 * Respect the per-page noindex switch.
 *
 * @param array $robots Directives.
 * @return array
 */
function dgf_robots( $robots ) {
	if ( ! dgf_seo_plugin_active() && is_singular() && '1' === get_post_meta( get_queried_object_id(), '_dgf_seo_noindex', true ) ) {
		$robots['noindex']  = true;
		$robots['nofollow'] = false;
		$robots['follow']   = true;
	} else {
		$robots['max-image-preview'] = 'large';
	}
	return $robots;
}

add_filter( 'wp_sitemaps_posts_query_args', 'dgf_sitemap_exclude_noindex', 10, 2 );
/**
 * Keep noindex pages out of wp-sitemap.xml.
 *
 * @param array  $args      Query args.
 * @param string $post_type Post type.
 * @return array
 */
function dgf_sitemap_exclude_noindex( $args, $post_type ) {
	if ( in_array( $post_type, array( 'page', 'post' ), true ) ) {
		$args['meta_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
			'relation' => 'OR',
			array( 'key' => '_dgf_seo_noindex', 'compare' => 'NOT EXISTS' ),
			array( 'key' => '_dgf_seo_noindex', 'value' => '1', 'compare' => '!=' ),
		);
	}
	return $args;
}

add_action( 'wp_head', 'dgf_head_meta', 2 );
/**
 * Meta description, Open Graph and Twitter tags.
 */
function dgf_head_meta() {
	if ( dgf_seo_plugin_active() ) {
		return;
	}
	$desc  = dgf_meta_description();
	$title = wp_get_document_title();
	$url   = is_singular() ? get_permalink() : home_url( add_query_arg( array() ) );
	$image = '';
	if ( is_singular() && has_post_thumbnail() ) {
		$image = get_the_post_thumbnail_url( null, 'dgf-hero' );
	} elseif ( has_custom_logo() ) {
		$image = wp_get_attachment_image_url( (int) get_theme_mod( 'custom_logo' ), 'full' );
	}

	echo "\n<!-- Dubai Gym Flooring SEO -->\n";
	if ( $desc ) {
		printf( '<meta name="description" content="%s">' . "\n", esc_attr( $desc ) );
	}
	printf( '<meta property="og:locale" content="%s">' . "\n", esc_attr( str_replace( '-', '_', get_bloginfo( 'language' ) ) ) );
	printf( '<meta property="og:type" content="%s">' . "\n", is_front_page() ? 'website' : 'article' );
	printf( '<meta property="og:title" content="%s">' . "\n", esc_attr( $title ) );
	if ( $desc ) {
		printf( '<meta property="og:description" content="%s">' . "\n", esc_attr( $desc ) );
	}
	printf( '<meta property="og:url" content="%s">' . "\n", esc_url( $url ) );
	printf( '<meta property="og:site_name" content="%s">' . "\n", esc_attr( dgf_opt( 'brand_name' ) ) );
	if ( $image ) {
		printf( '<meta property="og:image" content="%s">' . "\n", esc_url( $image ) );
	}
	printf( '<meta name="twitter:card" content="%s">' . "\n", $image ? 'summary_large_image' : 'summary' );
	printf( '<meta name="geo.region" content="AE-DU">' . "\n" );
	printf( '<meta name="geo.placename" content="Dubai">' . "\n" );
}

add_action( 'wp_head', 'dgf_schema', 30 );
/**
 * JSON-LD structured data.
 */
function dgf_schema() {
	$home   = home_url( '/' );
	$org_id = $home . '#business';
	$areas  = array( 'Dubai', 'Abu Dhabi', 'Sharjah', 'Ajman', 'Ras Al Khaimah', 'Fujairah', 'Umm Al Quwain', 'Al Ain' );

	$business = array(
		'@type'              => 'HomeAndConstructionBusiness',
		'@id'                => $org_id,
		'name'               => dgf_opt( 'brand_name' ),
		'description'        => dgf_opt( 'footer_about' ),
		'url'                => $home,
		'areaServed'         => array_map(
			static function ( $a ) {
				return array( '@type' => 'City', 'name' => $a );
			},
			$areas
		),
		'address'            => array(
			'@type'           => 'PostalAddress',
			'streetAddress'   => dgf_opt( 'address' ),
			'addressLocality' => 'Dubai',
			'addressCountry'  => 'AE',
		),
		'parentOrganization' => array(
			'@type' => 'Organization',
			'name'  => dgf_opt( 'parent_name' ),
			'url'   => dgf_opt( 'parent_url' ),
		),
	);
	if ( has_custom_logo() ) {
		$business['logo']  = wp_get_attachment_image_url( (int) get_theme_mod( 'custom_logo' ), 'full' );
		$business['image'] = $business['logo'];
	} elseif ( has_site_icon() ) {
		$business['logo'] = get_site_icon_url( 512 );
	}
	if ( dgf_opt( 'phone_link' ) ) {
		$business['telephone'] = dgf_opt( 'phone_link' );
	} elseif ( dgf_wa_number() ) {
		$business['telephone'] = '+' . dgf_wa_number();
	}
	if ( dgf_opt( 'email' ) ) {
		$business['email'] = dgf_opt( 'email' );
	}
	$same_as = array_values( array_filter( array( dgf_opt( 'social_instagram' ), dgf_opt( 'social_facebook' ), dgf_opt( 'social_linkedin' ), dgf_opt( 'social_youtube' ), dgf_opt( 'social_tiktok' ) ) ) );
	if ( $same_as ) {
		$business['sameAs'] = $same_as;
	}

	$graph = array( $business );

	if ( is_singular( 'page' ) ) {
		$id    = get_queried_object_id();
		$url   = get_permalink( $id );
		$type  = get_post_meta( $id, '_dgf_type', true );
		$title = wp_strip_all_tags( get_the_title( $id ) );

		$graph[] = array(
			'@type'       => 'WebPage',
			'@id'         => $url . '#webpage',
			'url'         => $url,
			'name'        => $title,
			'description' => dgf_meta_description(),
			'isPartOf'    => array( '@id' => $home . '#website' ),
			'about'       => array( '@id' => $org_id ),
		);

		if ( in_array( $type, array( 'product', 'service', 'location', 'interior' ), true ) ) {
			$service = array(
				'@type'       => 'Service',
				'name'        => $title,
				'serviceType' => 'Gym flooring supply and installation',
				'provider'    => array( '@id' => $org_id ),
				'url'         => $url,
				'description' => dgf_meta_description(),
			);
			$place = get_post_meta( $id, '_dgf_place', true );
			$service['areaServed'] = $place ? array( '@type' => 'Place', 'name' => $place ) : array( '@type' => 'Country', 'name' => 'United Arab Emirates' );
			$graph[] = $service;
		}

		// Breadcrumbs.
		$crumbs    = array();
		$ancestors = array_reverse( get_post_ancestors( $id ) );
		$crumbs[]  = array( 'name' => __( 'Home', 'dgf' ), 'item' => $home );
		foreach ( $ancestors as $ancestor ) {
			$crumbs[] = array( 'name' => wp_strip_all_tags( get_the_title( $ancestor ) ), 'item' => get_permalink( $ancestor ) );
		}
		if ( ! is_front_page() ) {
			$crumbs[] = array( 'name' => $title, 'item' => $url );
		}
		$list = array();
		foreach ( $crumbs as $i => $crumb ) {
			$list[] = array( '@type' => 'ListItem', 'position' => $i + 1, 'name' => $crumb['name'], 'item' => $crumb['item'] );
		}
		$graph[] = array( '@type' => 'BreadcrumbList', 'itemListElement' => $list );

		// FAQ schema from core/details blocks in the page content.
		$faqs = dgf_extract_faqs( get_post_field( 'post_content', $id ) );
		if ( $faqs ) {
			$graph[] = array(
				'@type'      => 'FAQPage',
				'mainEntity' => array_map(
					static function ( $faq ) {
						return array(
							'@type'          => 'Question',
							'name'           => $faq['q'],
							'acceptedAnswer' => array( '@type' => 'Answer', 'text' => $faq['a'] ),
						);
					},
					$faqs
				),
			);
		}
	}

	$graph[] = array(
		'@type'     => 'WebSite',
		'@id'       => $home . '#website',
		'url'       => $home,
		'name'      => dgf_opt( 'brand_name' ),
		'publisher' => array( '@id' => $org_id ),
	);

	$data = array(
		'@context' => 'https://schema.org',
		'@graph'   => $graph,
	);
	echo '<script type="application/ld+json">' . wp_json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . "</script>\n";
}

/**
 * Pull question/answer pairs out of core/details blocks.
 *
 * @param string $content Post content.
 * @return array<int,array{q:string,a:string}>
 */
function dgf_extract_faqs( $content ) {
	$faqs = array();
	if ( ! has_blocks( $content ) ) {
		return $faqs;
	}
	$walk = static function ( $blocks ) use ( &$walk, &$faqs ) {
		foreach ( $blocks as $block ) {
			if ( 'core/details' === $block['blockName'] ) {
				if ( preg_match( '#<summary>(.*?)</summary>#s', $block['innerHTML'], $m ) ) {
					$answer = '';
					foreach ( $block['innerBlocks'] as $inner ) {
						$answer .= ' ' . render_block( $inner );
					}
					$q = trim( wp_strip_all_tags( $m[1] ) );
					$a = trim( preg_replace( '/\s+/', ' ', wp_strip_all_tags( $answer ) ) );
					if ( $q && $a ) {
						$faqs[] = array( 'q' => $q, 'a' => $a );
					}
				}
			}
			if ( ! empty( $block['innerBlocks'] ) ) {
				$walk( $block['innerBlocks'] );
			}
		}
	};
	$walk( parse_blocks( $content ) );
	return $faqs;
}
