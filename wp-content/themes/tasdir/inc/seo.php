<?php
/**
 * Lightweight SEO: titles, meta descriptions, robots, Open Graph, Twitter cards and JSON-LD schema.
 * Automatically switches itself off when Yoast, Rank Math, SEOPress, AIOSEO or The SEO Framework is active.
 *
 * @package Tasdir
 */

defined( 'ABSPATH' ) || exit;

/**
 * Is a dedicated SEO plugin active?
 *
 * @return bool
 */
function tasdir_seo_plugin_active() {
	return defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || defined( 'SEOPRESS_VERSION' ) || defined( 'AIOSEO_VERSION' ) || defined( 'THE_SEO_FRAMEWORK_VERSION' );
}

/* ---------- Per-page SEO fields ---------- */

/**
 * Post types with the SEO box.
 *
 * @return string[]
 */
function tasdir_seo_post_types() {
	return array( 'post', 'page', 'tasdir_product' );
}

/**
 * Register SEO meta (also editable via REST / block editor).
 */
function tasdir_register_seo_meta() {
	foreach ( tasdir_seo_post_types() as $pt ) {
		foreach ( array( '_tasdir_seo_title', '_tasdir_seo_desc' ) as $key ) {
			register_post_meta(
				$pt,
				$key,
				array(
					'type'              => 'string',
					'single'            => true,
					'show_in_rest'      => true,
					'sanitize_callback' => 'sanitize_text_field',
					'auth_callback'     => static function () {
						return current_user_can( 'edit_posts' );
					},
				)
			);
		}
		register_post_meta(
			$pt,
			'_tasdir_noindex',
			array(
				'type'          => 'boolean',
				'single'        => true,
				'show_in_rest'  => true,
				'auth_callback' => static function () {
					return current_user_can( 'edit_posts' );
				},
			)
		);
	}
}
add_action( 'init', 'tasdir_register_seo_meta' );

/**
 * SEO meta box.
 */
function tasdir_seo_metabox() {
	if ( tasdir_seo_plugin_active() ) {
		return;
	}
	add_meta_box( 'tasdir_seo', __( 'SEO (search & social)', 'tasdir' ), 'tasdir_seo_metabox_html', tasdir_seo_post_types(), 'normal', 'low' );
}
add_action( 'add_meta_boxes', 'tasdir_seo_metabox' );

/**
 * Render SEO box.
 *
 * @param WP_Post $post Post.
 */
function tasdir_seo_metabox_html( $post ) {
	wp_nonce_field( 'tasdir_seo', 'tasdir_seo_nonce' );
	$title = get_post_meta( $post->ID, '_tasdir_seo_title', true );
	$desc  = get_post_meta( $post->ID, '_tasdir_seo_desc', true );
	$noidx = get_post_meta( $post->ID, '_tasdir_noindex', true );
	?>
	<p><label for="tasdir_seo_title"><strong><?php esc_html_e( 'SEO title', 'tasdir' ); ?></strong> <span class="description"><?php esc_html_e( '(~60 characters, leave blank for the post title)', 'tasdir' ); ?></span></label>
	<input type="text" class="widefat" id="tasdir_seo_title" name="tasdir_seo_title" value="<?php echo esc_attr( $title ); ?>" maxlength="90"></p>
	<p><label for="tasdir_seo_desc"><strong><?php esc_html_e( 'Meta description', 'tasdir' ); ?></strong> <span class="description"><?php esc_html_e( '(~155 characters, leave blank to use the excerpt)', 'tasdir' ); ?></span></label>
	<textarea class="widefat" id="tasdir_seo_desc" name="tasdir_seo_desc" rows="3" maxlength="320"><?php echo esc_textarea( $desc ); ?></textarea></p>
	<p><label><input type="checkbox" name="tasdir_noindex" value="1" <?php checked( $noidx ); ?>> <?php esc_html_e( 'Hide this page from search engines (noindex)', 'tasdir' ); ?></label></p>
	<?php
}

/**
 * Save SEO box.
 *
 * @param int $post_id Post ID.
 */
function tasdir_seo_save( $post_id ) {
	if ( ! isset( $_POST['tasdir_seo_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['tasdir_seo_nonce'] ) ), 'tasdir_seo' ) ) {
		return;
	}
	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	update_post_meta( $post_id, '_tasdir_seo_title', isset( $_POST['tasdir_seo_title'] ) ? sanitize_text_field( wp_unslash( $_POST['tasdir_seo_title'] ) ) : '' );
	update_post_meta( $post_id, '_tasdir_seo_desc', isset( $_POST['tasdir_seo_desc'] ) ? sanitize_textarea_field( wp_unslash( $_POST['tasdir_seo_desc'] ) ) : '' );
	update_post_meta( $post_id, '_tasdir_noindex', ! empty( $_POST['tasdir_noindex'] ) );
}
add_action( 'save_post', 'tasdir_seo_save' );

/* ---------- Front-end output ---------- */

/**
 * Document title override.
 *
 * @param string $title Title (empty = default).
 * @return string
 */
function tasdir_pre_title( $title ) {
	if ( tasdir_seo_plugin_active() ) {
		return $title;
	}
	if ( is_front_page() ) {
		$t = tasdir_opt( 'seo_home_title' );
		return $t ? $t : $title;
	}
	if ( is_singular() ) {
		$t = get_post_meta( get_queried_object_id(), '_tasdir_seo_title', true );
		return $t ? $t : $title;
	}
	if ( is_post_type_archive( 'tasdir_product' ) ) {
		/* translators: %s: site name. */
		return sprintf( __( 'Fresh Fruit Export Products from India | %s', 'tasdir' ), get_bloginfo( 'name' ) );
	}
	return $title;
}
add_filter( 'pre_get_document_title', 'tasdir_pre_title' );

/**
 * Meta description for the current view.
 *
 * @return string
 */
function tasdir_meta_description() {
	$desc = '';
	if ( is_front_page() ) {
		$desc = tasdir_opt( 'seo_home_desc' );
	} elseif ( is_singular() ) {
		$id   = get_queried_object_id();
		$desc = get_post_meta( $id, '_tasdir_seo_desc', true );
		if ( ! $desc ) {
			$post = get_post( $id );
			$desc = has_excerpt( $id ) ? $post->post_excerpt : wp_trim_words( strip_shortcodes( $post->post_content ), 30, '…' );
		}
	} elseif ( is_post_type_archive( 'tasdir_product' ) ) {
		$desc = __( 'Fresh bananas, table grapes and more — export-grade fruit from India by Tasdir Import Export. Farm-direct sourcing, branded cartons and worldwide shipping.', 'tasdir' );
	} elseif ( is_category() || is_tag() || is_tax() ) {
		$desc = term_description();
	}
	if ( ! $desc ) {
		$desc = get_bloginfo( 'description' );
	}
	$desc = trim( preg_replace( '/\s+/', ' ', wp_strip_all_tags( (string) $desc ) ) );
	return mb_strlen( $desc ) > 160 ? rtrim( mb_substr( $desc, 0, 157 ) ) . '…' : $desc;
}

/**
 * Share image URL for the current view.
 *
 * @return string
 */
function tasdir_share_image() {
	if ( is_singular() && has_post_thumbnail( get_queried_object_id() ) ) {
		return (string) get_the_post_thumbnail_url( get_queried_object_id(), 'tasdir-wide' );
	}
	$id = (int) tasdir_opt( 'seo_og_image' );
	if ( $id && wp_get_attachment_image_url( $id, 'full' ) ) {
		return (string) wp_get_attachment_image_url( $id, 'full' );
	}
	if ( is_singular( 'tasdir_product' ) && false !== stripos( get_the_title(), 'grape' ) ) {
		return tasdir_asset_img( 'grapes-box', 1376 );
	}
	return tasdir_asset_img( 'banana-box', 1376 );
}

/**
 * Canonical URL for non-singular views (core handles singular).
 *
 * @return string
 */
function tasdir_current_url() {
	if ( is_singular() ) {
		return (string) wp_get_canonical_url();
	}
	if ( is_front_page() ) {
		return home_url( '/' );
	}
	if ( is_post_type_archive() ) {
		return (string) get_post_type_archive_link( get_query_var( 'post_type' ) );
	}
	if ( is_category() || is_tag() || is_tax() ) {
		$link = get_term_link( get_queried_object() );
		return is_wp_error( $link ) ? '' : $link;
	}
	return '';
}

/**
 * Output meta tags.
 */
function tasdir_seo_head() {
	if ( tasdir_seo_plugin_active() ) {
		return;
	}
	$desc  = tasdir_meta_description();
	$url   = tasdir_current_url();
	$title = wp_get_document_title();
	$img   = tasdir_share_image();

	if ( $desc ) {
		printf( '<meta name="description" content="%s">' . "\n", esc_attr( $desc ) );
	}
	if ( $url && ! is_singular() && ! is_paged() ) {
		printf( '<link rel="canonical" href="%s">' . "\n", esc_url( $url ) );
	}
	echo '<meta property="og:locale" content="' . esc_attr( get_locale() ) . '">' . "\n";
	echo '<meta property="og:site_name" content="' . esc_attr( get_bloginfo( 'name' ) ) . '">' . "\n";
	echo '<meta property="og:type" content="' . ( is_singular( 'post' ) ? 'article' : 'website' ) . '">' . "\n";
	echo '<meta property="og:title" content="' . esc_attr( $title ) . '">' . "\n";
	if ( $desc ) {
		echo '<meta property="og:description" content="' . esc_attr( $desc ) . '">' . "\n";
	}
	if ( $url ) {
		echo '<meta property="og:url" content="' . esc_url( $url ) . '">' . "\n";
	}
	if ( $img ) {
		echo '<meta property="og:image" content="' . esc_url( $img ) . '">' . "\n";
		echo '<meta name="twitter:image" content="' . esc_url( $img ) . '">' . "\n";
	}
	echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
	echo '<meta name="twitter:title" content="' . esc_attr( $title ) . '">' . "\n";
	if ( $desc ) {
		echo '<meta name="twitter:description" content="' . esc_attr( $desc ) . '">' . "\n";
	}

	tasdir_schema();
}
add_action( 'wp_head', 'tasdir_seo_head', 4 );

/**
 * Robots: noindex search, 404, attachment pages and pages flagged in the SEO box.
 *
 * @param array $robots Robots directives.
 * @return array
 */
function tasdir_robots( $robots ) {
	if ( tasdir_seo_plugin_active() ) {
		return $robots;
	}
	if ( is_search() || is_404() || is_attachment() || ( is_singular() && get_post_meta( get_queried_object_id(), '_tasdir_noindex', true ) ) ) {
		$robots['noindex'] = true;
		$robots['follow']  = true;
	} else {
		$robots['max-image-preview'] = 'large';
	}
	return $robots;
}
add_filter( 'wp_robots', 'tasdir_robots' );

/**
 * Exclude noindexed posts from the core XML sitemap (/wp-sitemap.xml).
 *
 * @param array  $args      Query args.
 * @param string $post_type Post type.
 * @return array
 */
function tasdir_sitemap_exclude( $args, $post_type ) {
	if ( in_array( $post_type, tasdir_seo_post_types(), true ) ) {
		$args['meta_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
			'relation' => 'OR',
			array(
				'key'     => '_tasdir_noindex',
				'compare' => 'NOT EXISTS',
			),
			array(
				'key'   => '_tasdir_noindex',
				'value' => '',
			),
		);
	}
	return $args;
}
add_filter( 'wp_sitemaps_posts_query_args', 'tasdir_sitemap_exclude', 10, 2 );

/**
 * Remove users from the public sitemap (no author archives needed).
 *
 * @param WP_Sitemaps_Provider $provider Provider.
 * @param string               $name     Name.
 * @return WP_Sitemaps_Provider|false
 */
add_filter(
	'wp_sitemaps_add_provider',
	static function ( $provider, $name ) {
		return 'users' === $name ? false : $provider;
	},
	10,
	2
);

/**
 * FAQ content shared by the front page and the FAQPage schema.
 *
 * @return array question => answer
 */
function tasdir_faqs() {
	return apply_filters(
		'tasdir_faqs',
		array(
			__( 'Which fruits do you export?', 'tasdir' )                    => __( 'We currently export fresh Cavendish bananas and table grapes (black, green and red varieties) from India. Contact us for other seasonal fruit and vegetables.', 'tasdir' ),
			__( 'What packaging do you offer?', 'tasdir' )                   => __( 'Fruit is packed in Tasdir-branded, ventilated corrugated export cartons. Banana cartons are typically 13 kg net; grape cartons are packed to buyer specification. Private-label packing is available on request.', 'tasdir' ),
			__( 'What is the minimum order quantity?', 'tasdir' )            => __( 'Our standard minimum order is one reefer container. Share your destination port and required volume on WhatsApp and we will confirm availability and pricing.', 'tasdir' ),
			__( 'Which shipping terms do you work on?', 'tasdir' )           => __( 'We quote FOB and CIF/CFR to your destination port. Payment terms and Incoterms are confirmed in the proforma invoice.', 'tasdir' ),
			__( 'Do you provide export documentation?', 'tasdir' )           => __( 'Yes. Each shipment is supported by the commercial invoice, packing list, certificate of origin, phytosanitary certificate and bill of lading, plus any additional documents your country requires.', 'tasdir' ),
			__( 'How do I get a quotation?', 'tasdir' )                      => __( 'Fill in the enquiry form or tap the WhatsApp button. Tell us the product, quantity and destination and our export desk will reply with a quotation.', 'tasdir' ),
		)
	);
}

/**
 * JSON-LD schema graph.
 */
function tasdir_schema() {
	$home   = home_url( '/' );
	$org_id = $home . '#organization';
	$same   = array_values( array_filter( array( tasdir_opt( 'facebook_url' ), tasdir_opt( 'instagram_url' ), tasdir_opt( 'linkedin_url' ), tasdir_opt( 'youtube_url' ) ) ) );

	$org = array(
		'@type'       => 'Organization',
		'@id'         => $org_id,
		'name'        => tasdir_opt( 'company_name' ),
		'url'         => $home,
		'description' => tasdir_opt( 'seo_home_desc' ),
	);
	$logo_id = get_theme_mod( 'custom_logo' );
	if ( $logo_id && wp_get_attachment_image_url( $logo_id, 'full' ) ) {
		$org['logo'] = wp_get_attachment_image_url( $logo_id, 'full' );
	}
	if ( $same ) {
		$org['sameAs'] = $same;
	}
	$phone = tasdir_opt( 'phone' ) ? tasdir_opt( 'phone' ) : ( tasdir_wa_number() ? '+' . tasdir_wa_number() : '' );
	if ( $phone || tasdir_opt( 'email' ) ) {
		$org['contactPoint'] = array_filter(
			array(
				'@type'             => 'ContactPoint',
				'contactType'       => 'sales',
				'telephone'         => $phone,
				'email'             => tasdir_opt( 'email' ),
				'availableLanguage' => array( 'English', 'Hindi' ),
			)
		);
	}
	if ( tasdir_opt( 'address' ) ) {
		$org['address'] = array(
			'@type'          => 'PostalAddress',
			'streetAddress'  => tasdir_opt( 'address' ),
			'addressCountry' => 'IN',
		);
	}

	$graph = array(
		$org,
		array(
			'@type'     => 'WebSite',
			'@id'       => $home . '#website',
			'url'       => $home,
			'name'      => get_bloginfo( 'name' ),
			'publisher' => array( '@id' => $org_id ),
		),
	);

	if ( is_front_page() ) {
		$faq = array();
		foreach ( tasdir_faqs() as $q => $a ) {
			$faq[] = array(
				'@type'          => 'Question',
				'name'           => $q,
				'acceptedAnswer' => array(
					'@type' => 'Answer',
					'text'  => $a,
				),
			);
		}
		$graph[] = array(
			'@type'      => 'FAQPage',
			'mainEntity' => $faq,
		);
	}

	if ( is_singular( 'tasdir_product' ) ) {
		$id      = get_queried_object_id();
		$product = array(
			'@type'       => 'Product',
			'name'        => get_the_title( $id ),
			'description' => tasdir_meta_description(),
			'image'       => tasdir_share_image(),
			'url'         => get_permalink( $id ),
			'brand'       => array(
				'@type' => 'Brand',
				'name'  => 'Tasdir',
			),
			'manufacturer' => array( '@id' => $org_id ),
			'countryOfOrigin' => 'IN',
		);
		$props = array();
		foreach ( tasdir_product_specs( $id ) as $label => $value ) {
			$props[] = array(
				'@type' => 'PropertyValue',
				'name'  => $label,
				'value' => $value,
			);
		}
		if ( $props ) {
			$product['additionalProperty'] = $props;
		}
		$graph[] = $product;
	}

	if ( ! is_front_page() ) {
		$list = array();
		foreach ( tasdir_breadcrumb_items() as $i => $item ) {
			$list[] = array_filter(
				array(
					'@type'    => 'ListItem',
					'position' => $i + 1,
					'name'     => $item[0],
					'item'     => $item[1],
				)
			);
		}
		$graph[] = array(
			'@type'           => 'BreadcrumbList',
			'itemListElement' => $list,
		);
	}

	echo '<script type="application/ld+json">' . wp_json_encode(
		array(
			'@context' => 'https://schema.org',
			'@graph'   => $graph,
		),
		JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG
	) . "</script>\n";
}
