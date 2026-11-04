<?php
/**
 * Raikwar Seal functions.
 */

define( 'RAIKWAR_SEAL_VER', '1.0.0' );
/* Compatibility: Blocksy Companion expects Blocksy helpers to exist. */
if ( ! function_exists( 'blocksy_get_wp_parent_theme' ) ) {
	function blocksy_get_wp_parent_theme() {
		$t = wp_get_theme();
		return $t->parent() ? $t->parent() : $t;
	}
}


function raikwar_seal_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'custom-logo', array( 'height' => 96, 'width' => 96, 'flex-width' => true, 'flex-height' => true ) );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
	register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'raikwar-seal' ),
		'footer'  => __( 'Footer Menu', 'raikwar-seal' ),
	) );
}
add_action( 'after_setup_theme', 'raikwar_seal_setup' );

/* ---------------------------------------------------------------------------
   Assets
   --------------------------------------------------------------------------- */
function raikwar_seal_scripts() {
	wp_enqueue_style( 'raikwar-seal-fonts', 'https://fonts.googleapis.com/css2?family=Gloock&family=IBM+Plex+Mono:wght@400;500;600&family=Instrument+Sans:wght@400;500;600;700&display=swap', array(), null );

	$deps = array( 'raikwar-seal-fonts' );
	$dist     = get_stylesheet_directory() . '/dist/styles.css';
	$is_draft = get_option( 'wpvibe_draft_theme' ) === get_stylesheet();
	if ( ! $is_draft && file_exists( $dist ) ) {
		wp_enqueue_style( 'raikwar-seal-compiled', get_theme_file_uri( 'dist/styles.css' ), array(), filemtime( $dist ) );
		$deps[] = 'raikwar-seal-compiled';
	}

	$site_css = get_stylesheet_directory() . '/assets/css/site.css';
	wp_enqueue_style( 'raikwar-seal-site', get_theme_file_uri( 'assets/css/site.css' ), $deps, file_exists( $site_css ) ? filemtime( $site_css ) : RAIKWAR_SEAL_VER );

	$site_js = get_stylesheet_directory() . '/assets/js/site.js';
	wp_enqueue_script( 'raikwar-seal-site', get_theme_file_uri( 'assets/js/site.js' ), array(), file_exists( $site_js ) ? filemtime( $site_js ) : RAIKWAR_SEAL_VER, array( 'strategy' => 'defer', 'in_footer' => true ) );

	// Block styles only where blocks are used.
	if ( is_front_page() ) {
		wp_dequeue_style( 'wp-block-library' );
		wp_dequeue_style( 'global-styles' );
		wp_dequeue_style( 'classic-theme-styles' );
	}
}
add_action( 'wp_enqueue_scripts', 'raikwar_seal_scripts', 20 );

function raikwar_seal_resource_hints( $urls, $relation ) {
	if ( 'preconnect' === $relation ) {
		$urls[] = 'https://fonts.googleapis.com';
		$urls[] = array( 'href' => 'https://fonts.gstatic.com', 'crossorigin' );
	}
	return $urls;
}
add_filter( 'wp_resource_hints', 'raikwar_seal_resource_hints', 10, 2 );

/* Head cleanup (speed + less noise) */
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'wp_head', 'wp_generator' );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wp_shortlink_wp_head' );
add_filter( 'xmlrpc_enabled', '__return_false' );

/* ---------------------------------------------------------------------------
   Business details — editable in Appearance → Customize → Business details
   --------------------------------------------------------------------------- */
function raikwar_info_defaults() {
	return array(
		'brand'     => 'Raikwar DSC',
		'legal'     => "Raikwar's Liaisoning & Business Registration Consultant",
		'phone1'    => '9594976480',
		'phone2'    => '8898284384',
		'whatsapp'  => '919594976480',
		'email'     => 'raikwarconsulting@gmail.com',
		'email2'    => 'raikwarsdsc@gmail.com',
		'address'   => 'Near ICICI ATM, Borivali East, Mumbai, Maharashtra',
		'address2'  => 'J/31, Flat No. 701, Global City, Virar West, Maharashtra',
		'hours'     => 'Mon–Sat · 9:00 AM – 7:00 PM',
		'years'     => '5+',
		'map_url'   => 'https://www.google.com/maps/search/?api=1&query=Raikwar+DSC+Borivali+East+Mumbai',
	);
}

function raikwar_info( $key ) {
	$d = raikwar_info_defaults();
	$v = get_theme_mod( 'raikwar_' . $key, '' );
	return '' !== trim( (string) $v ) ? $v : ( isset( $d[ $key ] ) ? $d[ $key ] : '' );
}

function raikwar_tel( $n ) {
	$digits = preg_replace( '/\D+/', '', $n );
	if ( strlen( $digits ) === 10 ) {
		$digits = '91' . $digits;
	}
	return 'tel:+' . $digits;
}

function raikwar_phone_label( $n ) {
	$d = preg_replace( '/\D+/', '', $n );
	return strlen( $d ) === 10 ? '+91 ' . substr( $d, 0, 5 ) . ' ' . substr( $d, 5 ) : $n;
}

function raikwar_wa( $msg = '' ) {
	$msg = $msg ? $msg : 'Hello Raikwar DSC, I need help with a registration.';
	return 'https://wa.me/' . preg_replace( '/\D+/', '', raikwar_info( 'whatsapp' ) ) . '?text=' . rawurlencode( $msg );
}

function raikwar_seal_customize( $wp_customize ) {
	$wp_customize->add_section( 'raikwar_business', array(
		'title'    => __( 'Business details', 'raikwar-seal' ),
		'priority' => 25,
	) );
	$labels = array(
		'brand'    => 'Short brand name',
		'legal'    => 'Full business name',
		'phone1'   => 'Primary phone',
		'phone2'   => 'Second phone',
		'whatsapp' => 'WhatsApp number (with country code, e.g. 919594976480)',
		'email'    => 'Email',
		'email2'   => 'Second email',
		'address'  => 'Office address',
		'address2' => 'Second office address',
		'hours'    => 'Opening hours',
		'years'    => 'Years of experience (e.g. 5+)',
		'map_url'  => 'Google Maps link',
	);
	$defaults = raikwar_info_defaults();
	foreach ( $labels as $key => $label ) {
		$wp_customize->add_setting( 'raikwar_' . $key, array( 'default' => '', 'sanitize_callback' => 'sanitize_text_field' ) );
		$wp_customize->add_control( 'raikwar_' . $key, array(
			'label'       => $label,
			'section'     => 'raikwar_business',
			'type'        => 'text',
			'input_attrs' => array( 'placeholder' => $defaults[ $key ] ),
		) );
	}
}
add_action( 'customize_register', 'raikwar_seal_customize' );

/* ---------------------------------------------------------------------------
   Services register (used on the front page + footer)
   cat: dsc | biz | export | food | ip
   --------------------------------------------------------------------------- */
function raikwar_services() {
	$dsc = home_url( '/digital-signature-certificate-dsc-in-mumbai-class-3-dsc-provider/' );
	$gst = home_url( '/gst-registration-in-mumbai/' );
	return array(
		array( 'Class-3 DSC', 'DSC', 'dsc', 'Same-day Class 3 digital signature for MCA, GST, ITR, EPFO and more.', $dsc ),
		array( 'DSC for Tenders', 'DSC-T', 'dsc', 'Signing + encryption DSC for e-tendering and GeM bids.', $dsc ),
		array( 'GST Registration', 'GSTIN', 'biz', 'GSTIN for traders, service providers and e-commerce sellers.', $gst ),
		array( 'MSME / Udyam', 'UDYAM', 'biz', 'Udyam certificate to unlock MSME benefits and schemes.', '' ),
		array( 'Shop & Establishment', 'GUMASTA', 'biz', 'Gumasta / Shop Act registration for shops and offices.', '' ),
		array( 'Trade License', 'TL', 'biz', 'Legal approval to run your business premises.', '' ),
		array( 'BMC Licenses', 'BMC', 'biz', 'Municipal approvals and licenses in Mumbai.', '' ),
		array( 'Startup India DPIIT', 'DPIIT', 'biz', 'Startup recognition for tax and funding benefits.', '' ),
		array( 'ISO Certification', 'ISO', 'biz', 'ISO 9001 and other standards to build credibility.', '' ),
		array( 'GeM Registration', 'GeM', 'biz', 'Sell to government buyers on the GeM portal.', '' ),
		array( 'ISIN NSDL / CDSL', 'ISIN', 'biz', 'ISIN allotment for shares of private companies.', '' ),
		array( 'Franking / e-SBTR', 'STAMP', 'biz', 'Stamp duty franking and e-SBTR for documents.', '' ),
		array( 'Notary Services', 'NOTARY', 'biz', 'Affidavits, agreements and document attestation.', '' ),
		array( 'Import Export Code', 'IEC', 'export', 'IEC from DGFT to start importing or exporting.', '' ),
		array( 'AD Code Registration', 'AD', 'export', 'AD Code with ICEGATE for export clearances.', '' ),
		array( 'APEDA Registration', 'APEDA', 'export', 'RCMC for exporters of agri & processed food.', '' ),
		array( 'Spice Board RCMC', 'SPICE', 'export', 'Registration for spice exporters.', '' ),
		array( 'FSSAI License', 'FSSAI', 'food', 'Basic, State and Central food licenses.', '' ),
		array( 'FDA Registration', 'FDA', 'food', 'For food, cosmetic and healthcare products.', '' ),
		array( 'Drug License', 'DL', 'food', 'Retail and wholesale licenses for pharmacies.', '' ),
		array( 'NABL Testing', 'NABL', 'food', 'Water and food testing from accredited labs.', '' ),
		array( 'BIS Certification', 'BIS', 'food', 'Mandatory product certification by BIS.', '' ),
		array( 'Trademark Registration', 'TM', 'ip', 'Search, filing and objection replies for your brand.', '' ),
		array( 'Copyright Registration', '©', 'ip', 'Protect software, art, music and written work.', '' ),
		array( 'PPL Music License', 'PPL', 'ip', 'Legal licence to play music at your venue.', '' ),
	);
}

function raikwar_faqs() {
	return array(
		array( 'How fast can I get a Class 3 Digital Signature?', 'In most cases the same day. Once your documents and video verification are complete, the DSC is usually issued within a few hours. The final timing depends on verification by the Certifying Authority.' ),
		array( 'Do I need to visit your office?', 'No. Almost every service is handled 100% online: you share documents on WhatsApp or email, we prepare and file the application, and you complete OTP or video verification from your phone. You can also visit our office if you prefer.' ),
		array( 'What documents are usually needed?', 'For most registrations: PAN, Aadhaar, a passport-size photo, business address proof and bank details. We send you an exact checklist for your service before starting, so there are no surprises.' ),
		array( 'Which DSC do I need for e-tendering?', 'Government e-tenders usually need a Class 3 DSC with both signing and encryption. Tell us which portal you are bidding on and we will issue the right certificate.' ),
		array( 'Can you renew my expired DSC?', 'Yes. A DSC is valid for 1 to 3 years. We issue a fresh certificate on a new or existing USB token, usually the same day.' ),
		array( 'Do I need GST registration?', 'GST is mandatory once your turnover crosses the threshold for your state and type of business, or if you sell inter-state or through e-commerce platforms. Share your business details and we will confirm whether you need it.' ),
		array( 'Are your fees fixed?', 'Yes. You get a clear quote with government fees and our professional fee shown separately before any work starts. No hidden charges.' ),
		array( 'Do you work with businesses outside Mumbai?', 'Yes. We serve clients across India. DSC, GST, IEC, Udyam, Trademark, FSSAI and most other registrations can be completed remotely.' ),
	);
}

/* ---------------------------------------------------------------------------
   SEO basics (skipped automatically if an SEO plugin is installed)
   --------------------------------------------------------------------------- */
function raikwar_has_seo_plugin() {
	return defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || defined( 'AIOSEO_VERSION' ) || class_exists( 'SEOPress\Core\Kernel' ) || defined( 'THE_SEO_FRAMEWORK_VERSION' );
}

function raikwar_meta_description() {
	if ( is_front_page() ) {
		return 'Raikwar DSC, Mumbai: same-day Class 3 Digital Signature (DSC), GST, FSSAI, Trademark, IEC, ISO, Shop Act (Gumasta), MSME and 25+ government registrations. Call ' . raikwar_phone_label( raikwar_info( 'phone1' ) ) . '.';
	}
	if ( is_singular() ) {
		$post = get_queried_object();
		$text = has_excerpt( $post ) ? get_the_excerpt( $post ) : wp_strip_all_tags( strip_shortcodes( $post->post_content ) );
		$text = trim( preg_replace( '/\s+/', ' ', $text ) );
		if ( $text ) {
			return wp_html_excerpt( $text, 155, '…' );
		}
	}
	if ( is_category() || is_tag() ) {
		$d = wp_strip_all_tags( term_description() );
		if ( $d ) {
			return wp_html_excerpt( $d, 155, '…' );
		}
	}
	return 'DSC, GST, FSSAI, Trademark, IEC and business registration consultant in Mumbai – Raikwar DSC.';
}

function raikwar_seal_seo_head() {
	if ( raikwar_has_seo_plugin() ) {
		return;
	}
	$desc  = raikwar_meta_description();
	$url   = is_singular() ? get_permalink() : ( is_front_page() ? home_url( '/' ) : '' );
	$title = wp_get_document_title();
	echo '<meta name="description" content="' . esc_attr( $desc ) . "\">\n";
	if ( $url ) {
		echo '<link rel="canonical" href="' . esc_url( $url ) . "\">\n";
	}
	echo '<meta property="og:type" content="' . ( is_single() ? 'article' : 'website' ) . "\">\n";
	echo '<meta property="og:title" content="' . esc_attr( $title ) . "\">\n";
	echo '<meta property="og:description" content="' . esc_attr( $desc ) . "\">\n";
	echo '<meta property="og:site_name" content="' . esc_attr( raikwar_info( 'brand' ) ) . "\">\n";
	echo '<meta property="og:locale" content="en_IN">' . "\n";
	if ( $url ) {
		echo '<meta property="og:url" content="' . esc_url( $url ) . "\">\n";
	}
	if ( is_singular() && has_post_thumbnail() ) {
		echo '<meta property="og:image" content="' . esc_url( get_the_post_thumbnail_url( null, 'large' ) ) . "\">\n";
	}
	echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
	echo '<meta name="theme-color" content="#0e1a3a">' . "\n";

	if ( is_front_page() ) {
		$schema = array(
			'@context'     => 'https://schema.org',
			'@type'        => 'ProfessionalService',
			'name'         => raikwar_info( 'legal' ),
			'alternateName'=> raikwar_info( 'brand' ),
			'url'          => home_url( '/' ),
			'telephone'    => '+91' . substr( preg_replace( '/\D+/', '', raikwar_info( 'phone1' ) ), -10 ),
			'email'        => raikwar_info( 'email' ),
			'address'      => array(
				'@type'           => 'PostalAddress',
				'streetAddress'   => raikwar_info( 'address' ),
				'addressLocality' => 'Mumbai',
				'addressRegion'   => 'Maharashtra',
				'addressCountry'  => 'IN',
			),
			'areaServed'   => array( 'Mumbai', 'Thane', 'Navi Mumbai', 'India' ),
			'openingHours' => 'Mo-Sa 09:00-19:00',
			'priceRange'   => '₹₹',
		);
		$logo_id = get_theme_mod( 'custom_logo' );
		if ( $logo_id ) {
			$schema['logo'] = wp_get_attachment_image_url( $logo_id, 'full' );
			$schema['image'] = $schema['logo'];
		}
		echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . "</script>\n";
	}
}
add_action( 'wp_head', 'raikwar_seal_seo_head', 2 );

/* Short, keyword-led title on the home page */
function raikwar_seal_title_parts( $parts ) {
	if ( is_front_page() ) {
		$parts['title']   = 'Digital Signature (DSC) & Business Registration Consultant in Mumbai';
		$parts['tagline'] = raikwar_info( 'brand' );
		unset( $parts['site'] );
	} elseif ( isset( $parts['site'] ) ) {
		$parts['site'] = raikwar_info( 'brand' );
	}
	return $parts;
}
add_filter( 'document_title_parts', 'raikwar_seal_title_parts' );
add_filter( 'document_title_separator', function () { return '|'; } );

/* Add explicit dimensions + async decoding to content images missing them */
add_filter( 'wp_img_tag_add_decoding_attr', '__return_true' );

/* ---------------------------------------------------------------------------
   Inline SVG icons
   --------------------------------------------------------------------------- */
function raikwar_icon( $name ) {
	$p = array(
		'phone'  => '<path d="M5 4h4l2 5-2.5 1.5a11 11 0 0 0 5 5L15 13l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 6a2 2 0 0 1 2-2"/>',
		'mail'   => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/>',
		'pin'    => '<path d="M12 21s-7-6.2-7-11.5A7 7 0 0 1 19 9.5C19 14.8 12 21 12 21z"/><circle cx="12" cy="9.5" r="2.5"/>',
		'clock'  => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
		'arrow'  => '<path d="M5 12h14M13 6l6 6-6 6"/>',
		'check'  => '<path d="m5 12.5 4.5 4.5L19 7.5"/>',
		'menu'   => '<path d="M4 7h16M4 12h16M4 17h16"/>',
		'close'  => '<path d="M6 6l12 12M18 6 6 18"/>',
		'bolt'   => '<path d="M13 3 5 14h6l-1 7 8-11h-6l1-7z"/>',
		'shield' => '<path d="M12 3 4 6v6c0 4.5 3.4 8.3 8 9 4.6-.7 8-4.5 8-9V6l-8-3z"/><path d="m8.5 12 2.5 2.5 4.5-5"/>',
		'rupee'  => '<path d="M7 5h10M7 9h10M7 5c6 0 6 8 0 8l7 6"/>',
		'user'   => '<circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/>',
		'doc'    => '<path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8l-5-5z"/><path d="M14 3v5h5M9 13h6M9 17h6"/>',
		'globe'  => '<circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3a14 14 0 0 1 0 18M12 3a14 14 0 0 0 0 18"/>',
		'wa'     => '<path d="M4 20l1.3-4A8 8 0 1 1 8 18.7L4 20z"/><path d="M9 9.5c.3 2.2 2.3 4.2 4.5 4.5l1-1.2 1.8.8c-.2 1.2-1.2 1.9-2.3 1.9A6 6 0 0 1 8 9.5c0-1.1.7-2.1 1.9-2.3l.8 1.8L9 9.5z"/>',
	);
	if ( ! isset( $p[ $name ] ) ) {
		return '';
	}
	return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">' . $p[ $name ] . '</svg>';
}

/* Fallback menu if none assigned */
function raikwar_fallback_menu() {
	$items = array(
		home_url( '/' )          => 'Home',
		home_url( '/services/' ) => 'Services',
		home_url( '/about/' )    => 'About',
		home_url( '/news/' )     => 'Updates',
		home_url( '/contact/' )  => 'Contact',
	);
	echo '<ul>';
	foreach ( $items as $url => $label ) {
		echo '<li><a href="' . esc_url( $url ) . '">' . esc_html( $label ) . '</a></li>';
	}
	echo '</ul>';
}

/* Excerpt */
add_filter( 'excerpt_length', function () { return 22; } );
add_filter( 'excerpt_more', function () { return '…'; } );

/* ---------------------------------------------------------------------------
   Gutenberg palette from theme.css tokens
   --------------------------------------------------------------------------- */
function raikwar_seal_editor_tokens() {
	$css_path = get_stylesheet_directory() . '/theme.css';
	if ( ! file_exists( $css_path ) ) {
		return;
	}
	$css = file_get_contents( $css_path );
	if ( false === $css || ! preg_match( '/@theme\s*\{([\s\S]+?)\}/', $css, $m ) ) {
		return;
	}
	$palette = array();
	if ( preg_match_all( '/--color-([a-z0-9_-]+?)\s*:\s*([^;]+);/i', $m[1], $matches, PREG_SET_ORDER ) ) {
		foreach ( $matches as $match ) {
			$palette[] = array(
				'slug'  => $match[1],
				'name'  => ucwords( str_replace( '-', ' ', $match[1] ) ),
				'color' => trim( $match[2] ),
			);
		}
	}
	if ( $palette ) {
		add_theme_support( 'editor-color-palette', $palette );
	}
	add_editor_style( 'editor.css' );
}
add_action( 'after_setup_theme', 'raikwar_seal_editor_tokens', 20 );
