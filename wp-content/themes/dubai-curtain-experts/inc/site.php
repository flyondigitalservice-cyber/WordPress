<?php
/**
 * Site layer: WhatsApp lead routing, SEO meta + schema, breadcrumbs,
 * lead inbox and the Drive media importer.
 *
 * Everything here reads the same Customizer values as the rest of the theme
 * (phone, whatsapp, email, address, hours), so contact details are edited in
 * one place: Appearance → Customize → Dubai Curtain Experts.
 *
 * @package Dubai_Curtain_Experts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* ------------------------------------------------------------------ helpers */

/**
 * Parent company URL (Casa Vera Home).
 *
 * @return string
 */
function dce_parent_url() {
	return 'https://casaverahome.ae/';
}

/**
 * WhatsApp number in wa.me format (digits only).
 *
 * @return string
 */
function dce_wa_number() {
	return preg_replace( '/\D+/', '', (string) dce_mod( 'whatsapp' ) );
}

/**
 * Build a wa.me link with a prefilled message.
 *
 * @param string $text Message.
 * @return string
 */
function dce_wa_url( $text = '' ) {
	$url = 'https://wa.me/' . dce_wa_number();
	if ( '' !== $text ) {
		$url .= '?text=' . rawurlencode( $text );
	}
	return $url;
}

/**
 * Default WhatsApp message for the current view, naming the page so every
 * lead arrives with its source.
 *
 * @return string
 */
function dce_wa_context_text() {
	$brand = dce_mod( 'brand_name' );
	if ( is_front_page() || ! is_singular() ) {
		/* translators: %s: brand name */
		return sprintf( __( 'Hello %s, I would like to book a free home visit and measurement.', 'dubai-curtain-experts' ), $brand );
	}
	/* translators: 1: brand name, 2: page title */
	return sprintf( __( 'Hello %1$s, I am interested in %2$s. Please arrange a free home visit.', 'dubai-curtain-experts' ), $brand, wp_strip_all_tags( get_the_title() ) );
}

/**
 * Tel: href value.
 *
 * @return string
 */
function dce_tel() {
	return preg_replace( '/[^\d+]/', '', (string) dce_mod( 'phone' ) );
}

/* --------------------------------------------------------------- shortcodes */

/**
 * [dce_whatsapp label="Chat on WhatsApp" text="Custom message"]
 *
 * @param array $atts Attributes.
 * @return string
 */
function dce_sc_whatsapp( $atts ) {
	$atts = shortcode_atts(
		array(
			'label' => __( 'Chat on WhatsApp', 'dubai-curtain-experts' ),
			'text'  => '',
		),
		$atts,
		'dce_whatsapp'
	);
	$text = '' !== $atts['text'] ? $atts['text'] : dce_wa_context_text();
	return sprintf(
		'<a class="dce-btn dce-btn-wa" href="%1$s" target="_blank" rel="noopener noreferrer">%2$s<span>%3$s</span></a>',
		esc_url( dce_wa_url( $text ) ),
		dce_icon( 'whatsapp', array( 'size' => 18 ) ),
		esc_html( $atts['label'] )
	);
}
add_shortcode( 'dce_whatsapp', 'dce_sc_whatsapp' );

/**
 * Lead form markup. Submissions are emailed to the Customizer email,
 * stored in Leads, then the visitor is handed to WhatsApp with the same
 * details prefilled — so no lead depends on a single channel.
 *
 * @param array $atts Attributes (service = preselected interest).
 * @return string
 */
function dce_sc_lead_form( $atts = array() ) {
	$atts   = shortcode_atts( array( 'service' => '' ), $atts, 'dce_lead_form' );
	$status = isset( $_GET['quote'] ) ? sanitize_key( wp_unslash( $_GET['quote'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$source = is_singular() ? wp_strip_all_tags( get_the_title() ) : '';

	ob_start();
	?>
	<form class="dce-form dce-lead-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
		<input type="hidden" name="action" value="dce_quote">
		<input type="hidden" name="dce_source" value="<?php echo esc_attr( $source ); ?>">
		<input type="hidden" name="dce_source_url" value="<?php echo esc_url( is_singular() ? get_permalink() : home_url( '/' ) ); ?>">
		<?php wp_nonce_field( 'dce_quote', 'dce_quote_nonce' ); ?>
		<input class="dce-honeypot" type="text" name="dce_company" tabindex="-1" autocomplete="off" aria-hidden="true">
		<?php if ( 'error' === $status ) : ?>
			<p class="dce-form-error"><?php esc_html_e( 'Please add your name and phone number — or tap the WhatsApp button instead.', 'dubai-curtain-experts' ); ?></p>
		<?php elseif ( 'success' === $status ) : ?>
			<p class="dce-form-ok"><?php esc_html_e( 'Thank you — your request has been received. We will contact you shortly.', 'dubai-curtain-experts' ); ?></p>
		<?php endif; ?>
		<div class="dce-form-grid">
			<input class="dce-field" type="text" name="dce_name" required placeholder="<?php esc_attr_e( 'Full name *', 'dubai-curtain-experts' ); ?>" autocomplete="name">
			<input class="dce-field" type="tel" name="dce_phone" required placeholder="<?php esc_attr_e( 'Phone / WhatsApp *', 'dubai-curtain-experts' ); ?>" autocomplete="tel">
			<input class="dce-field" type="email" name="dce_email" placeholder="<?php esc_attr_e( 'Email', 'dubai-curtain-experts' ); ?>" autocomplete="email">
			<input class="dce-field" type="text" name="dce_area" placeholder="<?php esc_attr_e( 'Area / community (e.g. Dubai Marina)', 'dubai-curtain-experts' ); ?>">
			<input class="dce-field dce-field-wide" type="text" name="dce_service" value="<?php echo esc_attr( $atts['service'] ); ?>" placeholder="<?php esc_attr_e( 'What do you need? (e.g. wave curtains, zebra blinds)', 'dubai-curtain-experts' ); ?>">
			<textarea class="dce-field dce-field-wide" name="dce_message" rows="4" placeholder="<?php esc_attr_e( 'Rooms, number of windows, approximate sizes…', 'dubai-curtain-experts' ); ?>"></textarea>
		</div>
		<button class="dce-btn dce-btn-wa dce-btn-block" type="submit">
			<?php echo dce_icon( 'whatsapp', array( 'size' => 18 ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<span><?php esc_html_e( 'Send request via WhatsApp', 'dubai-curtain-experts' ); ?></span>
		</button>
		<p class="dce-form-note">
			<?php
			printf(
				/* translators: 1: email, 2: phone */
				esc_html__( 'Your request is also emailed to %1$s. Prefer to call? %2$s', 'dubai-curtain-experts' ),
				esc_html( dce_mod( 'email' ) ),
				'<a href="tel:' . esc_attr( dce_tel() ) . '">' . esc_html( dce_mod( 'phone' ) ) . '</a>' // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			);
			?>
		</p>
	</form>
	<?php
	return ob_get_clean();
}
add_shortcode( 'dce_lead_form', 'dce_sc_lead_form' );

/* -------------------------------------------------------------- lead inbox */

/**
 * Admin screen listing stored leads (latest first).
 */
function dce_leads_menu() {
	add_menu_page(
		__( 'Leads', 'dubai-curtain-experts' ),
		__( 'Leads', 'dubai-curtain-experts' ),
		'edit_pages',
		'dce-leads',
		'dce_leads_screen',
		'dashicons-whatsapp',
		3
	);
}
add_action( 'admin_menu', 'dce_leads_menu' );

/**
 * Render the leads table.
 */
function dce_leads_screen() {
	$leads = array_reverse( (array) get_option( 'dce_leads', array() ) );
	echo '<div class="wrap"><h1>' . esc_html__( 'Website leads', 'dubai-curtain-experts' ) . '</h1>';
	echo '<p>' . esc_html__( 'Every form submission is stored here, emailed to the address set in the Customizer and handed to WhatsApp.', 'dubai-curtain-experts' ) . '</p>';
	echo '<table class="widefat striped"><thead><tr>';
	foreach ( array( 'Date', 'Name', 'Phone', 'Email', 'Area', 'Interest', 'Message', 'Page' ) as $h ) {
		echo '<th>' . esc_html( $h ) . '</th>';
	}
	echo '</tr></thead><tbody>';
	if ( ! $leads ) {
		echo '<tr><td colspan="8">' . esc_html__( 'No leads yet.', 'dubai-curtain-experts' ) . '</td></tr>';
	}
	foreach ( $leads as $l ) {
		$phone = isset( $l['phone'] ) ? $l['phone'] : '';
		echo '<tr>';
		echo '<td>' . esc_html( isset( $l['time'] ) ? $l['time'] : '' ) . '</td>';
		echo '<td>' . esc_html( isset( $l['name'] ) ? $l['name'] : '' ) . '</td>';
		echo '<td><a href="https://wa.me/' . esc_attr( preg_replace( '/\D+/', '', $phone ) ) . '" target="_blank" rel="noopener">' . esc_html( $phone ) . '</a></td>';
		echo '<td>' . esc_html( isset( $l['email'] ) ? $l['email'] : '' ) . '</td>';
		echo '<td>' . esc_html( isset( $l['area'] ) ? $l['area'] : '' ) . '</td>';
		echo '<td>' . esc_html( isset( $l['service'] ) ? $l['service'] : '' ) . '</td>';
		echo '<td>' . esc_html( isset( $l['message'] ) ? $l['message'] : '' ) . '</td>';
		echo '<td>' . esc_html( isset( $l['source'] ) ? $l['source'] : '' ) . '</td>';
		echo '</tr>';
	}
	echo '</tbody></table></div>';
}

/* --------------------------------------------------------------------- SEO */

/**
 * True when a dedicated SEO plugin is active (then we stay out of the way).
 *
 * @return bool
 */
function dce_seo_plugin_active() {
	return defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || defined( 'AIOSEO_VERSION' ) || defined( 'SEOPRESS_VERSION' );
}

/**
 * Pages get an Excerpt box — its text is the page's meta description.
 */
function dce_page_excerpts() {
	add_post_type_support( 'page', 'excerpt' );
}
add_action( 'init', 'dce_page_excerpts' );

/**
 * Meta description for the current request.
 *
 * @return string
 */
function dce_meta_description() {
	$desc = '';
	if ( is_singular() ) {
		$post = get_queried_object();
		if ( $post && has_excerpt( $post ) ) {
			$desc = get_the_excerpt( $post );
		} elseif ( $post ) {
			$desc = wp_trim_words( wp_strip_all_tags( strip_shortcodes( $post->post_content ) ), 28, '…' );
		}
	}
	if ( '' === trim( $desc ) ) {
		$desc = get_bloginfo( 'description' );
	}
	return trim( preg_replace( '/\s+/', ' ', wp_strip_all_tags( $desc ) ) );
}

/**
 * Description, Open Graph and Twitter tags.
 */
function dce_seo_head() {
	if ( dce_seo_plugin_active() ) {
		return;
	}
	$desc  = dce_meta_description();
	$title = wp_get_document_title();
	$url   = is_singular() ? get_permalink() : home_url( add_query_arg( array() ) );
	$image = '';
	if ( is_singular() && has_post_thumbnail() ) {
		$image = get_the_post_thumbnail_url( null, 'large' );
	}
	if ( ! $image && has_custom_logo() ) {
		$image = wp_get_attachment_image_url( get_theme_mod( 'custom_logo' ), 'full' );
	}

	if ( $desc ) {
		printf( '<meta name="description" content="%s">' . "\n", esc_attr( $desc ) );
	}
	printf( '<meta property="og:type" content="%s">' . "\n", is_front_page() ? 'website' : 'article' );
	printf( '<meta property="og:site_name" content="%s">' . "\n", esc_attr( dce_mod( 'brand_name' ) ) );
	printf( '<meta property="og:title" content="%s">' . "\n", esc_attr( $title ) );
	if ( $desc ) {
		printf( '<meta property="og:description" content="%s">' . "\n", esc_attr( $desc ) );
	}
	printf( '<meta property="og:url" content="%s">' . "\n", esc_url( $url ) );
	printf( '<meta property="og:locale" content="%s">' . "\n", esc_attr( get_locale() ) );
	if ( $image ) {
		printf( '<meta property="og:image" content="%s">' . "\n", esc_url( $image ) );
	}
	printf( '<meta name="twitter:card" content="%s">' . "\n", $image ? 'summary_large_image' : 'summary' );
	echo '<meta name="geo.region" content="AE-DU">' . "\n";
	echo '<meta name="geo.placename" content="Dubai">' . "\n";
}
add_action( 'wp_head', 'dce_seo_head', 2 );

/**
 * Structured data: LocalBusiness everywhere, BreadcrumbList on inner pages,
 * FAQPage when the page contains Details (FAQ) blocks.
 */
function dce_schema() {
	if ( dce_seo_plugin_active() ) {
		return;
	}
	$graph = array();

	$business = array(
		'@type'              => 'HomeAndConstructionBusiness',
		'@id'                => home_url( '/#business' ),
		'name'               => dce_mod( 'brand_name' ),
		'url'                => home_url( '/' ),
		'telephone'          => dce_mod( 'phone' ),
		'email'              => dce_mod( 'email' ),
		'priceRange'         => 'AED',
		'address'            => array(
			'@type'           => 'PostalAddress',
			'streetAddress'   => 'Empire Plaza Shopping Center, Shop 49, Naif Road',
			'addressLocality' => 'Deira, Dubai',
			'addressRegion'   => 'Dubai',
			'addressCountry'  => 'AE',
		),
		'openingHoursSpecification' => array(
			array(
				'@type'     => 'OpeningHoursSpecification',
				'dayOfWeek' => array( 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday' ),
				'opens'     => '08:00',
				'closes'    => '17:30',
			),
		),
		'areaServed'         => array( 'Dubai', 'Abu Dhabi', 'Sharjah', 'Ajman', 'Ras Al Khaimah', 'Al Ain', 'United Arab Emirates' ),
		'parentOrganization' => array(
			'@type' => 'Organization',
			'name'  => 'Casa Vera Home (Mukhtar Curtain LLC)',
			'url'   => dce_parent_url(),
		),
		'sameAs'             => array( dce_parent_url() ),
	);
	if ( has_custom_logo() ) {
		$business['logo'] = wp_get_attachment_image_url( get_theme_mod( 'custom_logo' ), 'full' );
	}
	$graph[] = $business;

	if ( is_singular() && ! is_front_page() ) {
		$items = array();
		$pos   = 1;
		foreach ( dce_breadcrumb_trail() as $crumb ) {
			$items[] = array(
				'@type'    => 'ListItem',
				'position' => $pos++,
				'name'     => $crumb['title'],
				'item'     => $crumb['url'],
			);
		}
		$graph[] = array(
			'@type'           => 'BreadcrumbList',
			'itemListElement' => $items,
		);
	}

	if ( is_singular( 'post' ) ) {
		$post    = get_queried_object();
		$article = array(
			'@type'            => 'BlogPosting',
			'headline'         => wp_strip_all_tags( get_the_title( $post ) ),
			'description'      => dce_meta_description(),
			'datePublished'    => get_the_date( 'c', $post ),
			'dateModified'     => get_the_modified_date( 'c', $post ),
			'mainEntityOfPage' => get_permalink( $post ),
			'author'           => array( '@id' => home_url( '/#business' ) ),
			'publisher'        => array( '@id' => home_url( '/#business' ) ),
		);
		if ( has_post_thumbnail( $post ) ) {
			$article['image'] = get_the_post_thumbnail_url( $post, 'large' );
		}
		$graph[] = $article;
	}

	if ( is_singular() ) {
		$post = get_queried_object();
		if ( $post && has_block( 'details', $post ) ) {
			$faqs = array();
			foreach ( parse_blocks( $post->post_content ) as $block ) {
				dce_collect_faqs( $block, $faqs );
			}
			if ( $faqs ) {
				$graph[] = array(
					'@type'      => 'FAQPage',
					'mainEntity' => $faqs,
				);
			}
		}
	}

	printf(
		'<script type="application/ld+json">%s</script>' . "\n",
		wp_json_encode(
			array(
				'@context' => 'https://schema.org',
				'@graph'   => $graph,
			),
			JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
		)
	);
}
add_action( 'wp_head', 'dce_schema', 30 );

/**
 * Walk blocks and collect Details blocks as FAQ entries.
 *
 * @param array $block Parsed block.
 * @param array $faqs  Collected entries (by reference).
 */
function dce_collect_faqs( $block, &$faqs ) {
	if ( 'core/details' === $block['blockName'] ) {
		$html = $block['innerHTML'];
		if ( preg_match( '#<summary>(.*?)</summary>#s', $html, $m ) ) {
			$answer = '';
			foreach ( $block['innerBlocks'] as $inner ) {
				$answer .= ' ' . wp_strip_all_tags( render_block( $inner ) );
			}
			$faqs[] = array(
				'@type'          => 'Question',
				'name'           => wp_strip_all_tags( $m[1] ),
				'acceptedAnswer' => array(
					'@type' => 'Answer',
					'text'  => trim( preg_replace( '/\s+/', ' ', $answer ) ),
				),
			);
		}
	}
	if ( ! empty( $block['innerBlocks'] ) ) {
		foreach ( $block['innerBlocks'] as $inner ) {
			dce_collect_faqs( $inner, $faqs );
		}
	}
}

/* ------------------------------------------------------------- breadcrumbs */

/**
 * Breadcrumb trail for the current page (Home → ancestors → page).
 *
 * @return array<int,array{title:string,url:string}>
 */
function dce_breadcrumb_trail() {
	$trail = array(
		array(
			'title' => __( 'Home', 'dubai-curtain-experts' ),
			'url'   => home_url( '/' ),
		),
	);
	if ( is_singular() ) {
		$post = get_queried_object();
		if ( 'post' === $post->post_type && get_option( 'page_for_posts' ) ) {
			$trail[] = array(
				'title' => wp_strip_all_tags( get_the_title( (int) get_option( 'page_for_posts' ) ) ),
				'url'   => get_permalink( (int) get_option( 'page_for_posts' ) ),
			);
		}
		foreach ( array_reverse( get_post_ancestors( $post ) ) as $ancestor ) {
			$trail[] = array(
				'title' => wp_strip_all_tags( get_the_title( $ancestor ) ),
				'url'   => get_permalink( $ancestor ),
			);
		}
		$trail[] = array(
			'title' => wp_strip_all_tags( get_the_title( $post ) ),
			'url'   => get_permalink( $post ),
		);
	}
	return $trail;
}

/**
 * Print breadcrumbs.
 */
function dce_breadcrumbs() {
	$trail = dce_breadcrumb_trail();
	$last  = count( $trail ) - 1;
	echo '<nav class="dce-crumbs" aria-label="' . esc_attr__( 'Breadcrumb', 'dubai-curtain-experts' ) . '"><ol>';
	foreach ( $trail as $i => $crumb ) {
		if ( $i === $last ) {
			echo '<li aria-current="page">' . esc_html( $crumb['title'] ) . '</li>';
		} else {
			echo '<li><a href="' . esc_url( $crumb['url'] ) . '">' . esc_html( $crumb['title'] ) . '</a></li>';
		}
	}
	echo '</ol></nav>';
}

/* ------------------------------------------------------------ Drive import */

/**
 * REST route used once to copy the owner's Google Drive photos into the
 * Media Library (admins only). Re-running is safe: files already imported
 * (matched by Drive ID) are returned, not downloaded again.
 */
function dce_register_import_route() {
	register_rest_route(
		'dce/v1',
		'/import',
		array(
			'methods'             => 'POST',
			'permission_callback' => function () {
				return current_user_can( 'upload_files' ) && current_user_can( 'manage_options' );
			},
			'callback'            => 'dce_rest_import',
		)
	);
}
add_action( 'rest_api_init', 'dce_register_import_route' );

/**
 * Import callback.
 *
 * @param WP_REST_Request $request Request with items[{drive_id,name,alt}].
 * @return array
 */
function dce_rest_import( $request ) {
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';

	$items = (array) $request->get_param( 'items' );
	$out   = array();

	foreach ( $items as $item ) {
		$drive = isset( $item['drive_id'] ) ? preg_replace( '/[^A-Za-z0-9_-]/', '', $item['drive_id'] ) : '';
		$name  = isset( $item['name'] ) ? sanitize_file_name( $item['name'] ) : '';
		$alt   = isset( $item['alt'] ) ? sanitize_text_field( $item['alt'] ) : '';
		if ( '' === $drive || '' === $name ) {
			continue;
		}

		$existing = get_posts(
			array(
				'post_type'      => 'attachment',
				'post_status'    => 'inherit',
				'meta_key'       => '_dce_drive_id', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				'meta_value'     => $drive, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
				'fields'         => 'ids',
				'posts_per_page' => 1,
			)
		);

		if ( $existing ) {
			$id = (int) $existing[0];
		} else {
			$tmp = download_url( 'https://drive.google.com/uc?export=download&id=' . $drive, 60 );
			if ( is_wp_error( $tmp ) ) {
				$out[ $drive ] = array( 'error' => $tmp->get_error_message() );
				continue;
			}
			$id = media_handle_sideload(
				array(
					'name'     => $name,
					'tmp_name' => $tmp,
				),
				0,
				$alt
			);
			if ( is_wp_error( $id ) ) {
				wp_delete_file( $tmp );
				$out[ $drive ] = array( 'error' => $id->get_error_message() );
				continue;
			}
			update_post_meta( $id, '_dce_drive_id', $drive );
			update_post_meta( $id, '_wp_attachment_image_alt', $alt );
		}

		$meta          = wp_get_attachment_metadata( $id );
		$large         = wp_get_attachment_image_src( $id, 'large' );
		$out[ $drive ] = array(
			'id'    => $id,
			'url'   => $large ? $large[0] : wp_get_attachment_url( $id ),
			'w'     => isset( $meta['width'] ) ? (int) $meta['width'] : 0,
			'h'     => isset( $meta['height'] ) ? (int) $meta['height'] : 0,
		);
	}

	return $out;
}
