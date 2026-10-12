<?php
/**
 * SEO: meta tags, Open Graph, Twitter cards, robots, JSON-LD.
 *
 * Plays nicely with Yoast / Rank Math / AIOSEO / SEOPress: when one of those is active,
 * the theme only emits schema those plugins do not (LocalBusiness/Store, FAQ, Video, Product extras).
 *
 * @package Dastaan
 */

defined( 'ABSPATH' ) || exit;

/**
 * Description for the current view.
 *
 * @return string
 */
function dastaan_meta_description() {
	$desc = '';
	if ( is_front_page() ) {
		$desc = get_bloginfo( 'description' );
		if ( ! $desc ) {
			$desc = dastaan_option( 'footer_about' );
		}
	} elseif ( is_singular() ) {
		$post = get_queried_object();
		if ( $post ) {
			if ( dastaan_has_wc() && is_product() ) {
				$product = wc_get_product( $post->ID );
				$desc    = $product ? $product->get_short_description() : '';
			}
			if ( ! $desc && has_excerpt( $post ) ) {
				$desc = get_the_excerpt( $post );
			}
			if ( ! $desc ) {
				$desc = $post->post_content;
			}
		}
	} elseif ( is_tax() || is_category() || is_tag() ) {
		$desc = term_description();
	} elseif ( dastaan_has_wc() && is_shop() ) {
		$desc = get_post_field( 'post_excerpt', wc_get_page_id( 'shop' ) );
		if ( ! $desc ) {
			$desc = sprintf( /* translators: %s: site name */ __( 'Shop premium handcrafted unstitched dress material for women at %s — pure organza, mul chanderi, chikankari, zardozi and gota patti sets, ₹5,000 to ₹25,000.', 'dastaan' ), get_bloginfo( 'name' ) );
		}
	}
	$desc = wp_strip_all_tags( strip_shortcodes( (string) $desc ) );
	$desc = preg_replace( '/\s+/', ' ', $desc );
	$desc = trim( $desc );
	if ( mb_strlen( $desc ) > 158 ) {
		$desc = rtrim( mb_substr( $desc, 0, 155 ) ) . '…';
	}

	return apply_filters( 'dastaan_meta_description', $desc );
}

/**
 * Social image for current view.
 *
 * @return string
 */
function dastaan_social_image() {
	if ( is_singular() && has_post_thumbnail() ) {
		$src = wp_get_attachment_image_url( get_post_thumbnail_id(), 'large' );
		if ( $src ) {
			return $src;
		}
	}
	if ( ( is_tax( 'product_cat' ) ) ) {
		$term = get_queried_object();
		if ( $term ) {
			return dastaan_category_image( $term, 'large' );
		}
	}
	$hero = dastaan_option( 'hero_1_image' );

	return $hero ? $hero : '';
}

/**
 * Head output.
 */
function dastaan_seo_head() {
	// Verification tags never conflict, emit regardless.
	$gv = dastaan_option( 'google_verify' );
	if ( $gv ) {
		printf( '<meta name="google-site-verification" content="%s">' . "\n", esc_attr( $gv ) );
	}
	$fv = dastaan_option( 'fb_domain_verify' );
	if ( $fv ) {
		printf( '<meta name="facebook-domain-verification" content="%s">' . "\n", esc_attr( $fv ) );
	}

	if ( dastaan_has_seo_plugin() ) {
		return;
	}

	$desc  = dastaan_meta_description();
	$title = wp_get_document_title();
	$url   = '';
	if ( is_singular() ) {
		$url = get_permalink();
	} elseif ( is_front_page() ) {
		$url = home_url( '/' );
	} elseif ( is_tax() || is_category() || is_tag() ) {
		$url = get_term_link( get_queried_object() );
		$url = is_wp_error( $url ) ? '' : $url;
	} elseif ( dastaan_has_wc() && is_shop() ) {
		$url = wc_get_page_permalink( 'shop' );
	}
	$image = dastaan_social_image();
	$type  = ( dastaan_has_wc() && is_product() ) ? 'product' : ( is_singular( 'post' ) ? 'article' : 'website' );

	if ( $desc ) {
		printf( '<meta name="description" content="%s">' . "\n", esc_attr( $desc ) );
	}
	if ( $url && ! is_paged() ) {
		printf( '<link rel="canonical" href="%s">' . "\n", esc_url( $url ) );
	}
	printf( '<meta property="og:site_name" content="%s">' . "\n", esc_attr( get_bloginfo( 'name' ) ) );
	printf( '<meta property="og:type" content="%s">' . "\n", esc_attr( $type ) );
	printf( '<meta property="og:title" content="%s">' . "\n", esc_attr( $title ) );
	if ( $desc ) {
		printf( '<meta property="og:description" content="%s">' . "\n", esc_attr( $desc ) );
	}
	if ( $url ) {
		printf( '<meta property="og:url" content="%s">' . "\n", esc_url( $url ) );
	}
	if ( $image ) {
		printf( '<meta property="og:image" content="%s">' . "\n", esc_url( $image ) );
	}
	printf( '<meta property="og:locale" content="%s">' . "\n", esc_attr( get_locale() ) );
	echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
	printf( '<meta name="twitter:title" content="%s">' . "\n", esc_attr( $title ) );
	if ( $desc ) {
		printf( '<meta name="twitter:description" content="%s">' . "\n", esc_attr( $desc ) );
	}
	if ( $image ) {
		printf( '<meta name="twitter:image" content="%s">' . "\n", esc_url( $image ) );
	}

	if ( dastaan_has_wc() && is_product() ) {
		$product = wc_get_product( get_the_ID() );
		if ( $product ) {
			printf( '<meta property="product:price:amount" content="%s">' . "\n", esc_attr( wc_get_price_to_display( $product ) ) );
			printf( '<meta property="product:price:currency" content="%s">' . "\n", esc_attr( get_woocommerce_currency() ) );
			printf( '<meta property="product:availability" content="%s">' . "\n", $product->is_in_stock() ? 'in stock' : 'out of stock' );
		}
	}
}
add_action( 'wp_head', 'dastaan_seo_head', 1 );

/**
 * Robots: keep transactional / thin pages out of the index.
 *
 * @param array $robots Robots.
 * @return array
 */
function dastaan_robots( $robots ) {
	$noindex = is_search() || is_404();
	if ( dastaan_has_wc() && ( is_cart() || is_checkout() || is_account_page() ) ) {
		$noindex = true;
	}
	$wishlist = dastaan_option( 'wishlist_url' );
	if ( $wishlist && is_page() && untrailingslashit( wp_parse_url( home_url( $wishlist ), PHP_URL_PATH ) ) === untrailingslashit( wp_parse_url( get_permalink(), PHP_URL_PATH ) ) ) {
		$noindex = true;
	}
	if ( $noindex ) {
		$robots['noindex'] = true;
		$robots['follow']  = true;
		unset( $robots['max-image-preview'] );
	} else {
		$robots['max-image-preview'] = 'large';
		$robots['max-snippet']       = -1;
		$robots['max-video-preview'] = -1;
	}

	return $robots;
}
add_filter( 'wp_robots', 'dastaan_robots' );

/**
 * Organization / Store node reused in several graphs.
 *
 * @return array
 */
function dastaan_schema_org_node() {
	$logo_id = get_theme_mod( 'custom_logo' );
	$logo    = $logo_id ? wp_get_attachment_image_url( $logo_id, 'full' ) : '';
	$same_as = array_filter(
		array(
			dastaan_option( 'social_instagram' ),
			dastaan_option( 'social_facebook' ),
			dastaan_option( 'social_youtube' ),
			dastaan_option( 'social_pinterest' ),
		)
	);
	$node = array(
		'@type'          => array( 'Organization', 'Store', 'ClothingStore' ),
		'@id'            => home_url( '/#organization' ),
		'name'           => get_bloginfo( 'name' ),
		'url'            => home_url( '/' ),
		'description'    => wp_strip_all_tags( dastaan_option( 'footer_about' ) ),
		'telephone'      => dastaan_option( 'phone_number' ),
		'email'          => dastaan_option( 'contact_email' ),
		'priceRange'     => '₹5,000 – ₹25,000',
		'currenciesAccepted' => 'INR',
		'paymentAccepted'=> 'UPI, Credit Card, Debit Card, Net Banking, EMI',
		'address'        => array(
			'@type'           => 'PostalAddress',
			'streetAddress'   => 'Seawoods',
			'addressLocality' => 'Navi Mumbai',
			'addressRegion'   => 'Maharashtra',
			'postalCode'      => '400706',
			'addressCountry'  => 'IN',
		),
		'areaServed'     => 'IN',
		'knowsAbout'     => array( 'Chikankari', 'Zardozi', 'Dabka', 'Gota Patti', 'Mukaish', 'Pitta work', 'Jaali work', 'Pure organza silk', 'Mul chanderi', 'Unstitched dress material' ),
	);
	if ( $logo ) {
		$node['logo']  = array( '@type' => 'ImageObject', 'url' => $logo );
		$node['image'] = $logo;
	}
	if ( $same_as ) {
		$node['sameAs'] = array_values( $same_as );
	}
	$hours = dastaan_option( 'studio_hours' );
	if ( $hours ) {
		$node['openingHours'] = $hours;
	}

	return $node;
}

/**
 * JSON-LD graph.
 */
function dastaan_schema_output() {
	$graph    = array();
	$has_plugin = dastaan_has_seo_plugin();

	if ( is_front_page() ) {
		$graph[] = dastaan_schema_org_node();
		if ( ! $has_plugin ) {
			$graph[] = array(
				'@type'           => 'WebSite',
				'@id'             => home_url( '/#website' ),
				'url'             => home_url( '/' ),
				'name'            => get_bloginfo( 'name' ),
				'publisher'       => array( '@id' => home_url( '/#organization' ) ),
				'inLanguage'      => get_locale(),
				'potentialAction' => array(
					'@type'       => 'SearchAction',
					'target'      => array( '@type' => 'EntryPoint', 'urlTemplate' => home_url( '/?s={search_term_string}' . ( dastaan_has_wc() ? '&post_type=product' : '' ) ) ),
					'query-input' => 'required name=search_term_string',
				),
			);
		}
		// FAQ.
		$faq = array();
		for ( $i = 1; $i <= 4; $i++ ) {
			$q = dastaan_option( "faq_{$i}_q" );
			$a = dastaan_option( "faq_{$i}_a" );
			if ( $q && $a ) {
				$faq[] = array(
					'@type'          => 'Question',
					'name'           => wp_strip_all_tags( $q ),
					'acceptedAnswer' => array( '@type' => 'Answer', 'text' => wp_strip_all_tags( $a ) ),
				);
			}
		}
		if ( $faq && dastaan_option( 'seo_enable' ) ) {
			$graph[] = array( '@type' => 'FAQPage', '@id' => home_url( '/#faq' ), 'mainEntity' => $faq );
		}
	}

	// Breadcrumbs (plugins handle their own).
	if ( ! $has_plugin && ! is_front_page() ) {
		$items = dastaan_get_breadcrumb_items();
		if ( count( $items ) > 1 ) {
			$list = array();
			foreach ( $items as $i => $it ) {
				$entry = array( '@type' => 'ListItem', 'position' => $i + 1, 'name' => $it['name'] );
				if ( $it['url'] ) {
					$entry['item'] = $it['url'];
				}
				$list[] = $entry;
			}
			$graph[] = array( '@type' => 'BreadcrumbList', 'itemListElement' => $list );
		}
	}

	// Product video.
	if ( dastaan_has_wc() && is_product() && function_exists( 'dastaan_get_product_videos' ) ) {
		$product = wc_get_product( get_the_ID() );
		if ( $product ) {
			$videos = dastaan_get_product_videos( $product );
			foreach ( $videos as $v ) {
				if ( empty( $v['src'] ) ) {
					continue;
				}
				$node = array(
					'@type'        => 'VideoObject',
					'name'         => $product->get_name(),
					'description'  => wp_strip_all_tags( $product->get_short_description() ? $product->get_short_description() : $product->get_name() ),
					'thumbnailUrl' => $v['poster'] ? $v['poster'] : wp_get_attachment_image_url( $product->get_image_id(), 'large' ),
					'uploadDate'   => get_the_date( DATE_W3C, $product->get_id() ),
					'contentUrl'   => $v['src'],
				);
				if ( ! empty( $v['embed'] ) ) {
					$node['embedUrl'] = $v['embed'];
				}
				$graph[] = $node;
			}
		}
	}

	// Collection page for categories.
	if ( ! $has_plugin && dastaan_has_wc() && ( is_product_category() || is_shop() ) ) {
		$graph[] = array(
			'@type'       => 'CollectionPage',
			'name'        => wp_strip_all_tags( woocommerce_page_title( false ) ),
			'url'         => is_shop() ? wc_get_page_permalink( 'shop' ) : get_term_link( get_queried_object() ),
			'description' => dastaan_meta_description(),
			'isPartOf'    => array( '@id' => home_url( '/#website' ) ),
		);
	}

	// Article.
	if ( ! $has_plugin && is_singular( 'post' ) ) {
		$graph[] = array(
			'@type'         => 'Article',
			'headline'      => get_the_title(),
			'datePublished' => get_the_date( DATE_W3C ),
			'dateModified'  => get_the_modified_date( DATE_W3C ),
			'author'        => array( '@type' => 'Person', 'name' => get_the_author() ),
			'publisher'     => array( '@id' => home_url( '/#organization' ) ),
			'image'         => dastaan_social_image(),
			'mainEntityOfPage' => get_permalink(),
		);
	}

	$graph = apply_filters( 'dastaan_schema_graph', $graph );
	if ( empty( $graph ) ) {
		return;
	}
	echo '<script type="application/ld+json">' . wp_json_encode( array( '@context' => 'https://schema.org', '@graph' => $graph ), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
}
add_action( 'wp_head', 'dastaan_schema_output', 90 );

/**
 * Enrich WooCommerce's own Product schema with brand, material and craft attributes.
 *
 * @param array      $markup  Markup.
 * @param WC_Product $product Product.
 * @return array
 */
function dastaan_product_schema( $markup, $product ) {
	$markup['brand'] = array( '@type' => 'Brand', 'name' => get_bloginfo( 'name' ) );
	$markup['audience'] = array( '@type' => 'PeopleAudience', 'suggestedGender' => 'female' );
	$markup['category'] = 'Apparel & Accessories > Clothing > Traditional & Ceremonial Clothing';

	$attrs = $product->get_attributes();
	$props = array();
	foreach ( $attrs as $attr ) {
		if ( ! $attr instanceof WC_Product_Attribute || ! $attr->get_visible() ) {
			continue;
		}
		$name   = wc_attribute_label( $attr->get_name(), $product );
		$values = $attr->is_taxonomy() ? wp_list_pluck( $attr->get_terms() ? $attr->get_terms() : array(), 'name' ) : $attr->get_options();
		if ( empty( $values ) ) {
			continue;
		}
		$value = implode( ', ', array_map( 'wp_strip_all_tags', $values ) );
		if ( preg_match( '/fabric|material/i', $name ) ) {
			$markup['material'] = $value;
		} elseif ( preg_match( '/colou?r/i', $name ) ) {
			$markup['color'] = $value;
		}
		$props[] = array( '@type' => 'PropertyValue', 'name' => $name, 'value' => $value );
	}
	if ( $props ) {
		$markup['additionalProperty'] = $props;
	}
	if ( empty( $markup['image'] ) && $product->get_image_id() ) {
		$markup['image'] = wp_get_attachment_image_url( $product->get_image_id(), 'large' );
	}
	if ( isset( $markup['offers'] ) && is_array( $markup['offers'] ) ) {
		foreach ( $markup['offers'] as &$offer ) {
			$offer['itemCondition'] = 'https://schema.org/NewCondition';
			$offer['seller']        = array( '@id' => home_url( '/#organization' ) );
			$offer['shippingDetails'] = array(
				'@type'               => 'OfferShippingDetails',
				'shippingRate'        => array( '@type' => 'MonetaryAmount', 'value' => 0, 'currency' => 'INR' ),
				'shippingDestination' => array( '@type' => 'DefinedRegion', 'addressCountry' => 'IN' ),
			);
		}
		unset( $offer );
	}

	return apply_filters( 'dastaan_schema_product', $markup, $product );
}
add_filter( 'woocommerce_structured_data_product', 'dastaan_product_schema', 10, 2 );

/**
 * Document title separator.
 */
add_filter(
	'document_title_separator',
	function () {
		return '—';
	}
);

/**
 * Alt text fallback: never ship an image without alt.
 *
 * @param array   $attr       Attributes.
 * @param WP_Post $attachment Attachment.
 * @return array
 */
function dastaan_img_alt_fallback( $attr, $attachment ) {
	if ( empty( $attr['alt'] ) && $attachment instanceof WP_Post ) {
		$parent = $attachment->post_parent ? get_post( $attachment->post_parent ) : null;
		$attr['alt'] = $parent ? $parent->post_title : $attachment->post_title;
	}

	return $attr;
}
add_filter( 'wp_get_attachment_image_attributes', 'dastaan_img_alt_fallback', 10, 2 );
