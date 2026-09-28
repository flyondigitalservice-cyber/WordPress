<?php
/**
 * Turns the page data into standard Gutenberg block markup (core blocks only),
 * so every heading, paragraph, list, button and FAQ is editable in the block
 * editor without any page-builder plugin.
 *
 * Special link: any button or link whose URL is "#whatsapp" is turned into a
 * live WhatsApp chat link (with the page name prefilled) when the page renders,
 * so changing the number in the Customizer updates every button at once.
 *
 * @package DGF
 */

defined( 'ABSPATH' ) || exit;

/* -------------------------------------------------------------------------
 * Block helpers — output matches core save() markup so blocks validate.
 * ----------------------------------------------------------------------- */

/**
 * JSON attributes for a block comment.
 *
 * @param array $attrs Attributes.
 * @return string
 */
function dgf_b_attrs( $attrs ) {
	return $attrs ? ' ' . wp_json_encode( $attrs, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) : '';
}

/**
 * Inline text: escaped, but allowing simple <a>, <strong>, <em>.
 *
 * @param string $text Text.
 * @return string
 */
function dgf_b_text( $text ) {
	return wp_kses(
		$text,
		array(
			'a'      => array( 'href' => array() ),
			'strong' => array(),
			'em'     => array(),
		)
	);
}

/**
 * Paragraph block.
 *
 * @param string $text  Text.
 * @param string $class Extra class.
 * @return string
 */
function dgf_b_p( $text, $class = '' ) {
	$attrs = $class ? array( 'className' => $class ) : array();
	$open  = $class ? '<p class="' . esc_attr( $class ) . '">' : '<p>';
	return "<!-- wp:paragraph" . dgf_b_attrs( $attrs ) . " -->\n" . $open . dgf_b_text( $text ) . "</p>\n<!-- /wp:paragraph -->\n\n";
}

/**
 * Heading block.
 *
 * @param string $text  Text.
 * @param int    $level Level.
 * @param string $class Extra class.
 * @return string
 */
function dgf_b_h( $text, $level = 2, $class = '' ) {
	$attrs = array();
	if ( 2 !== $level ) {
		$attrs['level'] = $level;
	}
	if ( $class ) {
		$attrs['className'] = $class;
	}
	$classes = trim( 'wp-block-heading ' . $class );
	return "<!-- wp:heading" . dgf_b_attrs( $attrs ) . " -->\n<h{$level} class=\"" . esc_attr( $classes ) . '">' . dgf_b_text( $text ) . "</h{$level}>\n<!-- /wp:heading -->\n\n";
}

/**
 * List block.
 *
 * @param string[] $items Items.
 * @param string   $class Extra class.
 * @return string
 */
function dgf_b_list( $items, $class = '' ) {
	$attrs = $class ? array( 'className' => $class ) : array();
	$html  = "<!-- wp:list" . dgf_b_attrs( $attrs ) . " -->\n<ul class=\"" . esc_attr( trim( 'wp-block-list ' . $class ) ) . '">';
	foreach ( $items as $item ) {
		$html .= "<!-- wp:list-item -->\n<li>" . dgf_b_text( $item ) . "</li>\n<!-- /wp:list-item -->";
	}
	return $html . "</ul>\n<!-- /wp:list -->\n\n";
}

/**
 * Group block.
 *
 * @param string $inner Inner blocks.
 * @param string $class Class.
 * @return string
 */
function dgf_b_group( $inner, $class ) {
	return "<!-- wp:group" . dgf_b_attrs( array( 'className' => $class ) ) . " -->\n<div class=\"wp-block-group " . esc_attr( $class ) . "\">" . $inner . "</div>\n<!-- /wp:group -->\n\n";
}

/**
 * Columns block.
 *
 * @param string[] $columns Inner markup per column.
 * @param string   $class   Class.
 * @return string
 */
function dgf_b_columns( $columns, $class = '' ) {
	$attrs = $class ? array( 'className' => $class ) : array();
	$html  = "<!-- wp:columns" . dgf_b_attrs( $attrs ) . " -->\n<div class=\"" . esc_attr( trim( 'wp-block-columns ' . $class ) ) . '">';
	foreach ( $columns as $column ) {
		$html .= "<!-- wp:column -->\n<div class=\"wp-block-column\">" . $column . "</div>\n<!-- /wp:column -->";
	}
	return $html . "</div>\n<!-- /wp:columns -->\n\n";
}

/**
 * Buttons block.
 *
 * @param array $buttons List of [label, url, style ('fill'|'outline'), new_tab].
 * @return string
 */
function dgf_b_buttons( $buttons ) {
	$html = "<!-- wp:buttons -->\n<div class=\"wp-block-buttons\">";
	foreach ( $buttons as $button ) {
		$label   = $button[0];
		$url     = $button[1];
		$style   = isset( $button[2] ) ? $button[2] : 'fill';
		$new_tab = ! empty( $button[3] );
		$attrs   = array();
		if ( 'outline' === $style ) {
			$attrs['className'] = 'is-style-outline';
		}
		if ( $new_tab ) {
			$attrs['linkTarget'] = '_blank';
			$attrs['rel']        = 'noreferrer noopener';
		}
		$wrap   = 'outline' === $style ? 'wp-block-button is-style-outline' : 'wp-block-button';
		$target = $new_tab ? ' target="_blank" rel="noreferrer noopener"' : '';
		$html  .= "<!-- wp:button" . dgf_b_attrs( $attrs ) . " -->\n<div class=\"{$wrap}\"><a class=\"wp-block-button__link wp-element-button\" href=\"" . esc_url( $url ) . "\"{$target}>" . esc_html( $label ) . "</a></div>\n<!-- /wp:button -->";
	}
	return $html . "</div>\n<!-- /wp:buttons -->\n\n";
}

/**
 * Details (FAQ) block.
 *
 * @param string $question Question.
 * @param string $answer   Answer.
 * @return string
 */
function dgf_b_details( $question, $answer ) {
	return "<!-- wp:details -->\n<details class=\"wp-block-details\"><summary>" . esc_html( $question ) . '</summary>' . trim( dgf_b_p( $answer ) ) . "</details>\n<!-- /wp:details -->\n\n";
}

/**
 * Shortcode block.
 *
 * @param string $shortcode Shortcode.
 * @return string
 */
function dgf_b_sc( $shortcode ) {
	return "<!-- wp:shortcode -->\n" . $shortcode . "\n<!-- /wp:shortcode -->\n\n";
}

/**
 * Section heading pair (eyebrow + h2).
 *
 * @param string $eyebrow Eyebrow.
 * @param string $title   Title.
 * @return string
 */
function dgf_b_heading_pair( $eyebrow, $title ) {
	return dgf_b_p( $eyebrow, 'dgf-eyebrow' ) . dgf_b_h( $title );
}

/**
 * Escape a value for use inside a shortcode attribute.
 *
 * @param string $value Value.
 * @return string
 */
function dgf_sc_attr( $value ) {
	return str_replace( array( '"', '[', ']' ), array( '&quot;', '(', ')' ), $value );
}

/* -------------------------------------------------------------------------
 * Page builders.
 * ----------------------------------------------------------------------- */

/**
 * Build block content for one page.
 *
 * @param string $slug Slug.
 * @param array  $page Page data.
 * @return string
 */
function dgf_build_content( $slug, $page ) {
	switch ( $page['type'] ) {
		case 'home':
			return dgf_build_home();
		case 'product':
			return dgf_build_product( $page );
		case 'service':
			return dgf_build_service( $page );
		case 'location':
			return dgf_build_location( $page );
		case 'hub':
			return dgf_build_hub( $slug, $page );
		case 'about':
			return dgf_build_about();
		case 'catalogues':
			return dgf_build_catalogues();
		case 'projects':
			return dgf_build_projects();
		case 'faqs':
			return dgf_build_faqs();
		case 'contact':
			return dgf_build_contact();
		case 'quote':
			return dgf_build_quote();
		case 'legal':
			return dgf_build_legal( $page['legal'] );
	}
	return '';
}

/**
 * Intro section with the quote form alongside.
 *
 * @param string   $eyebrow    Eyebrow.
 * @param string   $title      Heading.
 * @param string[] $paragraphs Paragraphs.
 * @param string   $form_title Form title.
 * @return string
 */
function dgf_section_intro( $eyebrow, $title, $paragraphs, $form_title ) {
	$left = dgf_b_heading_pair( $eyebrow, $title );
	foreach ( $paragraphs as $paragraph ) {
		$left .= dgf_b_p( $paragraph );
	}
	$left .= dgf_b_buttons(
		array(
			array( __( 'Get a quote on WhatsApp', 'dgf' ), '#whatsapp' ),
			array( __( 'View catalogues', 'dgf' ), dgf_page_url( 'catalogues' ), 'outline' ),
		)
	);
	$right = dgf_b_sc( '[dgf_quote_form title="' . dgf_sc_attr( $form_title ) . '"]' );
	return dgf_b_group( dgf_b_columns( array( $left, $right ), 'dgf-intro__cols' ), 'dgf-section dgf-intro' );
}

/**
 * Four feature cards.
 *
 * @param string $eyebrow Eyebrow.
 * @param string $title   Title.
 * @param array  $items   [heading, text] pairs.
 * @param string $class   Extra class.
 * @return string
 */
function dgf_section_features( $eyebrow, $title, $items, $class = '' ) {
	$cols = array();
	foreach ( $items as $i => $item ) {
		$cols[] = dgf_b_p( sprintf( '%02d', $i + 1 ), 'dgf-num' ) . dgf_b_h( $item[0], 3 ) . dgf_b_p( $item[1] );
	}
	return dgf_b_group( dgf_b_heading_pair( $eyebrow, $title ) . dgf_b_columns( $cols, 'dgf-features__grid' ), trim( 'dgf-section dgf-features ' . $class ) );
}

/**
 * FAQ section.
 *
 * @param string $title Title.
 * @param array  $faqs  [q, a] pairs.
 * @return string
 */
function dgf_section_faqs( $title, $faqs ) {
	$inner = dgf_b_heading_pair( __( 'FAQs', 'dgf' ), $title );
	foreach ( $faqs as $faq ) {
		$inner .= dgf_b_details( $faq[0], $faq[1] );
	}
	return dgf_b_group( $inner, 'dgf-section dgf-faq' );
}

/**
 * Four standard process steps.
 *
 * @return array
 */
function dgf_default_steps() {
	return array(
		array( __( 'Message us on WhatsApp', 'dgf' ), __( 'Share your location, room size, photos and how you train.', 'dgf' ) ),
		array( __( 'Survey & samples', 'dgf' ), __( 'We visit, measure, check the subfloor and show real samples.', 'dgf' ) ),
		array( __( 'Clear quote', 'dgf' ), __( 'A written quote with the recommended system for each zone.', 'dgf' ) ),
		array( __( 'Install & handover', 'dgf' ), __( 'Our crew installs, finishes the edges and leaves the site clean.', 'dgf' ) ),
	);
}

/**
 * Product page.
 *
 * @param array $p Data.
 * @return string
 */
function dgf_build_product( $p ) {
	$title = $p['title'];
	$html  = dgf_section_intro( __( 'Overview', 'dgf' ), sprintf( '%s in Dubai &amp; the UAE', $title ), $p['intro'], sprintf( 'Get a price for %s', $title ) );
	$html .= dgf_section_features( __( 'Benefits', 'dgf' ), sprintf( 'Why choose %s', strtolower( $title ) ), $p['benefits'] );

	$specs = dgf_b_heading_pair( __( 'Specifications', 'dgf' ), __( 'Options &amp; specifications', 'dgf' ) ) . dgf_b_list( $p['specs'], 'dgf-checks' );
	$uses  = dgf_b_heading_pair( __( 'Applications', 'dgf' ), __( 'Where it’s used', 'dgf' ) ) . dgf_b_list( $p['uses'], 'dgf-checks' );
	$html .= dgf_b_group( dgf_b_columns( array( $specs, $uses ) ), 'dgf-section dgf-split' );

	$html .= dgf_b_group(
		dgf_b_sc( '[dgf_catalogues scope="page" title="' . dgf_sc_attr( $title . ' — downloads' ) . '"]' ) .
		dgf_b_sc( '[dgf_gallery title="' . dgf_sc_attr( $title . ' — project photos' ) . '"]' ),
		'dgf-section dgf-media'
	);

	$html .= dgf_b_group(
		dgf_b_heading_pair( __( 'Service area', 'dgf' ), sprintf( '%s across Dubai and every emirate', $title ) ) .
		dgf_b_p( sprintf( 'We supply and install %s throughout Dubai and across Abu Dhabi, Sharjah, Ajman, Ras Al Khaimah, Fujairah, Umm Al Quwain and Al Ain. Choose your area for local details:', strtolower( $title ) ) ) .
		dgf_b_sc( '[dgf_children parent="areas-we-serve" style="pills"]' ),
		'dgf-section dgf-areas'
	);

	$html .= dgf_section_faqs( sprintf( '%s — common questions', $title ), $p['faqs'] );
	$html .= dgf_b_group( dgf_b_heading_pair( __( 'Keep exploring', 'dgf' ), __( 'Related flooring systems', 'dgf' ) ) . dgf_b_sc( '[dgf_related limit="3"]' ), 'dgf-section dgf-related' );
	$html .= dgf_b_sc( '[dgf_cta title="' . dgf_sc_attr( sprintf( 'Get a quote for %s', strtolower( $title ) ) ) . '"]' );
	return $html;
}

/**
 * Service page.
 *
 * @param array $p Data.
 * @return string
 */
function dgf_build_service( $p ) {
	$title = $p['title'];
	$html  = dgf_section_intro( __( 'Service', 'dgf' ), sprintf( '%s across Dubai &amp; the UAE', $title ), $p['intro'], sprintf( 'Book: %s', $title ) );
	$html .= dgf_section_features( __( 'How it works', 'dgf' ), __( 'Simple, step by step', 'dgf' ), $p['steps'], 'dgf-steps' );
	$html .= dgf_b_group(
		dgf_b_heading_pair( __( 'Products', 'dgf' ), __( 'Flooring systems we supply and install', 'dgf' ) ) .
		dgf_b_sc( '[dgf_children parent="gym-flooring-products" limit="6"]' ) .
		dgf_b_buttons( array( array( __( 'See all products', 'dgf' ), dgf_page_url( 'gym-flooring-products' ), 'outline' ) ) ),
		'dgf-section dgf-products'
	);
	$html .= dgf_b_sc( '[dgf_gallery title="' . dgf_sc_attr( $title . ' — recent work' ) . '"]' );
	$html .= dgf_section_faqs( sprintf( '%s — common questions', $title ), $p['faqs'] );
	$html .= dgf_b_group( dgf_b_heading_pair( __( 'More services', 'dgf' ), __( 'Other ways we can help', 'dgf' ) ) . dgf_b_sc( '[dgf_related limit="2"]' ), 'dgf-section dgf-related' );
	$html .= dgf_b_sc( '[dgf_cta]' );
	return $html;
}

/**
 * Location page.
 *
 * @param array $p Data.
 * @return string
 */
function dgf_build_location( $p ) {
	$place = $p['place'];
	$short = preg_replace( '/\s*\(.*\)$/', '', $place );
	if ( preg_match( '/\((.*)\)/', $place, $m ) ) {
		$short = $m[1];
	}
	$html = dgf_section_intro( sprintf( 'Gym flooring · %s', $p['emirate'] ), sprintf( 'Gym flooring for %s', $place ), $p['intro'], sprintf( 'Free quote in %s', $short ) );

	$left  = dgf_b_heading_pair( __( 'Coverage', 'dgf' ), sprintf( 'Where we work in %s', $short ) ) . dgf_b_list( $p['communities'], 'dgf-checks dgf-checks--pins' );
	$right = dgf_b_heading_pair( __( 'Projects', 'dgf' ), sprintf( 'Typical projects in %s', $short ) ) . dgf_b_list( $p['projects'], 'dgf-checks' );
	$html .= dgf_b_group( dgf_b_columns( array( $left, $right ) ), 'dgf-section dgf-split' );

	$html .= dgf_b_group(
		dgf_b_heading_pair( __( 'Products', 'dgf' ), sprintf( 'Flooring systems we install in %s', $short ) ) .
		dgf_b_sc( '[dgf_children parent="gym-flooring-products" limit="6"]' ) .
		dgf_b_buttons( array( array( __( 'See all 15 flooring systems', 'dgf' ), dgf_page_url( 'gym-flooring-products' ), 'outline' ) ) ),
		'dgf-section dgf-products'
	);

	$html .= dgf_b_group(
		dgf_b_heading_pair( __( 'Local know-how', 'dgf' ), sprintf( 'Planning your project in %s', $short ) ) .
		dgf_b_p( $p['note'] ) .
		dgf_b_list(
			array(
				__( 'Free survey and samples (photos on WhatsApp for remote sites)', 'dgf' ),
				__( 'Documents for building management or community approval', 'dgf' ),
				__( 'Delivery and installation planned around your access rules', 'dgf' ),
				__( 'Clean handover with finished edges and thresholds', 'dgf' ),
			),
			'dgf-checks'
		),
		'dgf-section dgf-local'
	);

	$faqs   = $p['faqs'];
	$faqs[] = array(
		sprintf( 'How much does gym flooring cost in %s?', $short ),
		sprintf( 'The price depends on the system, thickness, area size and any subfloor preparation. Send your measurements, a photo of the space and how you train on WhatsApp, and we will reply with options and a clear quote for %s.', $short ),
	);
	$html .= dgf_section_faqs( sprintf( 'Gym flooring in %s — FAQs', $short ), $faqs );
	$html .= dgf_b_group( dgf_b_heading_pair( __( 'Nearby', 'dgf' ), __( 'Other areas we serve', 'dgf' ) ) . dgf_b_sc( '[dgf_children parent="areas-we-serve" style="pills"]' ), 'dgf-section dgf-areas' );
	$html .= dgf_b_sc( '[dgf_cta title="' . dgf_sc_attr( sprintf( 'Gym flooring in %s — get your quote', $short ) ) . '"]' );
	return $html;
}

/**
 * Hub pages (Products, Services, Areas).
 *
 * @param string $slug Slug.
 * @param array  $p    Data.
 * @return string
 */
function dgf_build_hub( $slug, $p ) {
	$html = '';
	if ( 'products' === $p['hub_of'] ) {
		$html .= dgf_b_group(
			dgf_b_heading_pair( __( 'The range', 'dgf' ), __( 'Choose the right floor for every zone', 'dgf' ) ) .
			dgf_b_p( 'Most gyms combine two or three systems: thick rubber or platforms where weights are dropped, rolls or tiles for general training, turf for sleds and vinyl for studios. Explore each system below, or send us your floor plan on WhatsApp and we will recommend the mix.' ) .
			dgf_b_sc( '[dgf_children parent="' . $slug . '"]' ),
			'dgf-section dgf-products'
		);
		$html .= dgf_b_group(
			dgf_b_heading_pair( __( 'Quick guide', 'dgf' ), __( 'Which thickness do I need?', 'dgf' ) ) .
			dgf_b_list(
				array(
					'<strong>Cardio, stretching, bodyweight:</strong> 6–15 mm rubber, PVC or sports vinyl',
					'<strong>Dumbbells &amp; general strength:</strong> 15–30 mm rubber tiles',
					'<strong>Heavy barbells &amp; Olympic lifting:</strong> 30–50 mm tiles or a deadlift platform',
					'<strong>Sleds, prowlers &amp; agility:</strong> dense gym turf',
					'<strong>Upper floors &amp; apartments:</strong> add an acoustic underlay',
				),
				'dgf-checks'
			) .
			dgf_b_buttons( array( array( __( 'Ask which floor suits me', 'dgf' ), '#whatsapp' ), array( __( 'Download catalogues', 'dgf' ), dgf_page_url( 'catalogues' ), 'outline' ) ) ),
			'dgf-section dgf-guide'
		);
	} elseif ( 'services' === $p['hub_of'] ) {
		$html .= dgf_b_group(
			dgf_b_heading_pair( __( 'End to end', 'dgf' ), __( 'From first message to finished floor', 'dgf' ) ) .
			dgf_b_p( 'We handle the whole job — advice, samples, measuring, supply, subfloor preparation, installation and aftercare — so you deal with one team and one quote.' ) .
			dgf_b_sc( '[dgf_children parent="' . $slug . '"]' ),
			'dgf-section dgf-products'
		);
		$html .= dgf_section_features( __( 'How it works', 'dgf' ), __( 'Four simple steps', 'dgf' ), dgf_default_steps(), 'dgf-steps' );
	} else {
		$html .= dgf_b_group(
			dgf_b_heading_pair( __( 'Dubai', 'dgf' ), __( 'Gym flooring across Dubai', 'dgf' ) ) .
			dgf_b_p( 'From Marina and Downtown towers to Palm villas and Al Quoz warehouses — local pages with the communities we cover and the projects we typically deliver.' ) .
			dgf_b_sc( '[dgf_children parent="' . $slug . '" emirate="dubai"]' ),
			'dgf-section dgf-products'
		);
		$html .= dgf_b_group(
			dgf_b_heading_pair( __( 'UAE', 'dgf' ), __( 'Gym flooring in every emirate', 'dgf' ) ) .
			dgf_b_p( 'We supply and install across the UAE. For projects outside Dubai we usually start with photos and measurements on WhatsApp, then plan a combined survey and installation visit.' ) .
			dgf_b_sc( '[dgf_children parent="' . $slug . '" emirate="other"]' ),
			'dgf-section dgf-products'
		);
	}
	$html .= dgf_b_sc( '[dgf_cta]' );
	return $html;
}

/**
 * Home page (hero is rendered by the template from the page title/excerpt/featured image).
 *
 * @return string
 */
function dgf_build_home() {
	$html  = dgf_b_sc( '[dgf_stats]' );
	$left  = dgf_b_heading_pair( __( 'Why us', 'dgf' ), __( 'One specialist team for every gym floor', 'dgf' ) );
	$left .= dgf_b_p( 'We only do gym and sports flooring — so we know which floor belongs under a squat rack, a treadmill line, a sled lane or a Pilates reformer. We visit, measure, bring samples and give you a clear recommendation for each zone.' );
	$left .= dgf_b_p( 'Then our own crew installs it, with clean cuts, finished edges and the paperwork your building management asks for.' );
	$left .= dgf_b_buttons( array( array( __( 'Get a quote on WhatsApp', 'dgf' ), '#whatsapp' ), array( __( 'About us', 'dgf' ), dgf_page_url( 'about' ), 'outline' ) ) );
	$right = dgf_b_list(
		array(
			'Free site survey and samples',
			'Rubber tiles, rolls, EPDM, turf, vinyl, PVC &amp; platforms',
			'Acoustic systems for towers, hotels and offices',
			'Home, commercial, CrossFit, hotel and school projects',
			'Installation across Dubai and all seven emirates',
			'Every enquiry answered on WhatsApp',
		),
		'dgf-checks dgf-checks--lg'
	);
	$html .= dgf_b_group( dgf_b_columns( array( $left, $right ) ), 'dgf-section dgf-split dgf-why' );

	$html .= dgf_b_group(
		dgf_b_heading_pair( __( 'Products', 'dgf' ), __( 'Flooring systems for every zone', 'dgf' ) ) .
		dgf_b_sc( '[dgf_children parent="gym-flooring-products" limit="8"]' ) .
		dgf_b_buttons( array( array( __( 'View all products', 'dgf' ), dgf_page_url( 'gym-flooring-products' ), 'outline' ) ) ),
		'dgf-section dgf-products'
	);

	$html .= dgf_section_features( __( 'How it works', 'dgf' ), __( 'From WhatsApp to finished floor', 'dgf' ), dgf_default_steps(), 'dgf-steps' );

	$sectors = array(
		array( 'Home gyms', sprintf( 'Villa, apartment and garage gyms that protect your floors and keep noise down. <a href="%s">Home gym flooring →</a>', esc_url( dgf_page_url( 'home-gym-flooring' ) ) ) ),
		array( 'Commercial gyms', sprintf( 'Zoned, branded floors for clubs, studios and corporate gyms. <a href="%s">Commercial flooring →</a>', esc_url( dgf_page_url( 'commercial-gym-flooring' ) ) ) ),
		array( 'CrossFit &amp; functional', sprintf( 'Drop-proof lifting zones and sled turf for boxes. <a href="%s">CrossFit flooring →</a>', esc_url( dgf_page_url( 'crossfit-flooring' ) ) ) ),
		array( 'Towers &amp; hotels', sprintf( 'Acoustic systems that keep neighbours and guests happy. <a href="%s">Acoustic flooring →</a>', esc_url( dgf_page_url( 'acoustic-gym-flooring' ) ) ) ),
	);
	$html .= dgf_section_features( __( 'Who we work with', 'dgf' ), __( 'Built around how you train', 'dgf' ), $sectors, 'dgf-sectors' );

	$html .= dgf_b_group(
		dgf_b_heading_pair( __( 'Areas we serve', 'dgf' ), __( 'Across Dubai and all seven emirates', 'dgf' ) ) .
		dgf_b_sc( '[dgf_children parent="areas-we-serve" style="pills"]' ),
		'dgf-section dgf-areas'
	);

	$html .= dgf_b_group(
		dgf_b_columns(
			array(
				dgf_b_heading_pair( __( 'Catalogues', 'dgf' ), __( 'Download the full range as PDF', 'dgf' ) ) . dgf_b_p( 'Specifications, colours and thicknesses for every system — or ask on WhatsApp and we will send the catalogue that fits your project.' ),
				dgf_b_buttons( array( array( __( 'Open catalogues', 'dgf' ), dgf_page_url( 'catalogues' ) ), array( __( 'Request on WhatsApp', 'dgf' ), '#whatsapp', 'outline' ) ) ),
			),
			'dgf-banner__cols'
		),
		'dgf-section dgf-banner'
	);

	$html .= dgf_b_sc( '[dgf_gallery source="all" limit="8" title="Recent installations"]' );

	$html .= dgf_section_faqs(
		__( 'Gym flooring questions, answered', 'dgf' ),
		array(
			array( 'What is the best gym flooring for Dubai homes and gyms?', 'For most gyms, rubber is the best all-rounder: 15–20 mm tiles for general training and 30 mm+ or a platform where heavy weights are dropped. Add turf for sleds, vinyl for studios and an acoustic layer on upper floors.' ),
			array( 'Do you install outside Dubai?', 'Yes. We supply and install across Abu Dhabi, Sharjah, Ajman, Ras Al Khaimah, Fujairah, Umm Al Quwain and Al Ain.' ),
			array( 'How do I get a price?', 'Send your room size, a few photos and how you train on WhatsApp. We reply with the recommended system and a clear quote, and can visit with samples.' ),
			array( 'Can I see samples before I order?', 'Yes. We bring samples to your site during the free survey, or arrange samples for you to review.' ),
		)
	);
	$html .= dgf_b_sc( '[dgf_parent_company]' );
	$html .= dgf_b_sc( '[dgf_cta]' );
	return $html;
}

/**
 * About page.
 *
 * @return string
 */
function dgf_build_about() {
	$html  = dgf_b_group(
		dgf_b_columns(
			array(
				dgf_b_heading_pair( __( 'Who we are', 'dgf' ), __( 'Gym flooring specialists, backed by Casa Vera Home', 'dgf' ) ) .
				dgf_b_p( 'Dubai Gym Flooring focuses on one thing: floors for training. We help homeowners, gym operators, hotels, schools and fit-out contractors choose, supply and install the right system for every zone of a gym.' ) .
				dgf_b_p( sprintf( 'We are part of <a href="%s">Casa Vera Home</a>, a UAE flooring, interiors and home-solutions company. That gives our clients the backing of an established business, with a specialist team dedicated to fitness floors.', esc_url( dgf_opt( 'parent_url' ) ) ) ) .
				dgf_b_buttons( array( array( __( 'Talk to us on WhatsApp', 'dgf' ), '#whatsapp' ), array( __( 'Visit Casa Vera Home', 'dgf' ), dgf_opt( 'parent_url' ), 'outline', true ) ) ),
				dgf_b_list(
					array(
						'Specialists in gym and sports flooring',
						'Advice based on how you actually train',
						'Samples on site before you decide',
						'Our own installation crews',
						'Coverage across all seven emirates',
					),
					'dgf-checks dgf-checks--lg'
				),
			)
		),
		'dgf-section dgf-split'
	);
	$html .= dgf_section_features(
		__( 'What we value', 'dgf' ),
		__( 'How we work', 'dgf' ),
		array(
			array( 'Right floor, right zone', 'We specify by zone so your budget goes where it protects the most.' ),
			array( 'Straight answers', 'Clear quotes and honest advice — including when a cheaper option will do.' ),
			array( 'Respect for buildings', 'We work to building rules and leave sites clean.' ),
			array( 'Fast communication', 'Every enquiry handled on WhatsApp, so you are never waiting on email.' ),
		)
	);
	$html .= dgf_section_features( __( 'Our process', 'dgf' ), __( 'Four simple steps', 'dgf' ), dgf_default_steps(), 'dgf-steps' );
	$html .= dgf_b_sc( '[dgf_parent_company]' );
	$html .= dgf_b_sc( '[dgf_cta]' );
	return $html;
}

/**
 * Catalogues page.
 *
 * @return string
 */
function dgf_build_catalogues() {
	$html  = dgf_b_group(
		dgf_b_heading_pair( __( 'Downloads', 'dgf' ), __( 'Gym flooring catalogues (PDF)', 'dgf' ) ) .
		dgf_b_p( 'Tap <strong>View PDF</strong> to open a catalogue in your browser, <strong>Download</strong> to save it, or <strong>Ask price</strong> to get a quote for anything you see on WhatsApp.' ) .
		dgf_b_sc( '[dgf_catalogues]' ),
		'dgf-section dgf-catalogue-section'
	);
	$html .= dgf_b_group(
		dgf_b_heading_pair( __( 'Can’t find it?', 'dgf' ), __( 'Need a specific data sheet?', 'dgf' ) ) .
		dgf_b_p( 'Consultants and fit-out contractors: we can send technical data sheets and samples for approvals. Tell us the product and project on WhatsApp.' ) .
		dgf_b_buttons( array( array( __( 'Request a data sheet', 'dgf' ), '#whatsapp' ) ) ),
		'dgf-section dgf-local'
	);
	$html .= dgf_b_sc( '[dgf_cta]' );
	return $html;
}

/**
 * Projects page.
 *
 * @return string
 */
function dgf_build_projects() {
	$html  = dgf_b_group(
		dgf_b_heading_pair( __( 'Our work', 'dgf' ), __( 'Recent gym flooring installations', 'dgf' ) ) .
		dgf_b_p( 'A selection of floors we have supplied and installed. Tap any photo to enlarge it — and message us on WhatsApp if you would like something similar.' ) .
		dgf_b_sc( '[dgf_gallery source="all" limit="60"]' ),
		'dgf-section dgf-projects'
	);
	$html .= dgf_section_features(
		__( 'Sectors', 'dgf' ),
		__( 'Projects we deliver', 'dgf' ),
		array(
			array( 'Home gyms', 'Villas, apartments, garages and rooftops.' ),
			array( 'Commercial gyms', 'Clubs, boutique studios and corporate gyms.' ),
			array( 'CrossFit boxes', 'Lifting zones, WOD floors and sled lanes.' ),
			array( 'Hotels &amp; towers', 'Acoustic systems above guest rooms and residents.' ),
		)
	);
	$html .= dgf_b_sc( '[dgf_cta title="Want a floor like these?"]' );
	return $html;
}

/**
 * FAQs page.
 *
 * @return string
 */
function dgf_build_faqs() {
	$html  = dgf_section_faqs(
		__( 'Choosing your floor', 'dgf' ),
		array(
			array( 'What thickness of gym flooring do I need?', 'Cardio and bodyweight areas: 6–15 mm. Dumbbells and general strength: 15–30 mm. Heavy barbells and Olympic lifting: 30–50 mm or a deadlift platform. On upper floors, add an acoustic underlay.' ),
			array( 'Rubber tiles or rubber rolls — which is better?', 'Tiles are easier to install, lift and replace, and come thicker for lifting zones. Rolls cover large areas quickly with fewer seams. Many gyms use both.' ),
			array( 'What is EPDM?', 'EPDM is a synthetic rubber that holds colour and resists UV. It is used for colour flecks in gym tiles and for outdoor and playground surfaces.' ),
			array( 'Do I need a deadlift platform?', 'If you deadlift or drop heavy barbells, a platform gives the best protection and noise reduction for that spot.' ),
		)
	);
	$html .= dgf_section_faqs(
		__( 'Installation &amp; care', 'dgf' ),
		array(
			array( 'How long does installation take?', 'Most home gyms are installed within a day once materials are on site. Commercial projects are planned to your programme.' ),
			array( 'Can flooring go over my existing tiles or marble?', 'Usually yes. Rubber and PVC tiles can be laid over existing hard floors after we check the level and condition.' ),
			array( 'How do I clean a rubber gym floor?', 'Sweep or vacuum, then damp-mop with a pH-neutral cleaner. Avoid solvents, oils and bleach.' ),
			array( 'Will the floor reduce noise for my neighbours?', 'Rubber reduces impact noise significantly; acoustic underlays and platforms reduce it further. We recommend a build-up based on your building.' ),
		)
	);
	$html .= dgf_section_faqs(
		__( 'Prices, delivery &amp; areas', 'dgf' ),
		array(
			array( 'How much does gym flooring cost?', 'It depends on the system, thickness, area and preparation. Send your size and photos on WhatsApp for a clear quote.' ),
			array( 'Which areas do you cover?', 'All of Dubai plus Abu Dhabi, Sharjah, Ajman, Ras Al Khaimah, Fujairah, Umm Al Quwain and Al Ain.' ),
			array( 'Is the site survey free?', 'Yes, the survey and samples are free for gym flooring projects in our service areas.' ),
			array( 'Can I buy flooring without installation?', 'Yes. We can supply materials only, and advise on laying interlocking tiles yourself.' ),
		)
	);
	$html .= dgf_b_sc( '[dgf_cta title="Still have a question?" text="Ask us on WhatsApp — a real person from our flooring team will answer."]' );
	return $html;
}

/**
 * Contact page.
 *
 * @return string
 */
function dgf_build_contact() {
	$left  = dgf_b_heading_pair( __( 'Talk to us', 'dgf' ), __( 'WhatsApp is the fastest way to reach us', 'dgf' ) );
	$left .= dgf_b_p( 'Send your location, room size and a couple of photos. We reply with options, samples and a clear quote — and can book a free site survey.' );
	$left .= dgf_b_sc( '[dgf_contact_info]' );
	$left .= dgf_b_buttons( array( array( __( 'Chat on WhatsApp', 'dgf' ), '#whatsapp' ) ) );
	$right = dgf_b_sc( '[dgf_quote_form title="Send an enquiry"]' );
	$html  = dgf_b_group( dgf_b_columns( array( $left, $right ), 'dgf-intro__cols' ), 'dgf-section dgf-intro' );
	$html .= dgf_b_sc( '[dgf_map]' );
	$html .= dgf_b_sc( '[dgf_parent_company]' );
	return $html;
}

/**
 * Quote page.
 *
 * @return string
 */
function dgf_build_quote() {
	$left  = dgf_b_sc( '[dgf_quote_form title="Your project"]' );
	$right = dgf_b_heading_pair( __( 'What happens next', 'dgf' ), __( 'A quote without the wait', 'dgf' ) );
	$right .= dgf_b_list(
		array(
			'Your details open in WhatsApp — just press send',
			'Our team replies with questions or options',
			'We book a free survey with samples if needed',
			'You receive a clear, itemised quote',
		),
		'dgf-checks dgf-checks--lg'
	);
	$right .= dgf_b_p( 'Every enquiry is also saved on our system, so nothing is missed even if WhatsApp does not open on your device.', 'dgf-small' );
	$html   = dgf_b_group( dgf_b_columns( array( $left, $right ), 'dgf-intro__cols' ), 'dgf-section dgf-intro' );
	$html  .= dgf_b_group( dgf_b_heading_pair( __( 'Browse first?', 'dgf' ), __( 'Explore our flooring systems', 'dgf' ) ) . dgf_b_sc( '[dgf_children parent="gym-flooring-products" limit="4"]' ), 'dgf-section dgf-products' );
	return $html;
}

/**
 * Legal pages (starter text — review with your legal adviser).
 *
 * @param string $which privacy|terms.
 * @return string
 */
function dgf_build_legal( $which ) {
	$brand = 'Dubai Gym Flooring';
	$html  = dgf_b_p( sprintf( '<em>Last updated: %s. This is starter text — please review it with your legal adviser and adjust it to your business.</em>', wp_date( 'j F Y' ) ), 'dgf-small' );
	if ( 'privacy' === $which ) {
		$sections = array(
			'Who we are'                  => sprintf( '%s is the fitness flooring division of Casa Vera Home, operating in the United Arab Emirates. This policy explains how we handle personal information shared through this website and WhatsApp.', $brand ),
			'What we collect'             => 'When you send an enquiry we collect the details you provide — such as your name, phone/WhatsApp number, email, location, the product you are interested in, your area size and your message — together with the page you sent it from.',
			'How we use it'               => 'We use your details only to reply to your enquiry, prepare quotes, arrange surveys, deliveries and installations, and provide after-sales support. We do not sell your personal information.',
			'WhatsApp'                    => 'Our enquiry forms open WhatsApp with your details prefilled. Messages sent on WhatsApp are also subject to WhatsApp’s own privacy policy.',
			'Storage and retention'       => 'Enquiries are stored securely in our website system and business WhatsApp account and are kept only as long as needed for the purposes above or as required by law.',
			'Cookies'                     => 'This website uses essential cookies needed to function. If analytics or advertising tools are added, this section should be updated to describe them.',
			'Your rights'                 => 'You can ask us to access, correct or delete your personal information at any time by contacting us on WhatsApp or email.',
			'Contact'                     => 'Questions about this policy? Contact us through the Contact page.',
		);
	} else {
		$sections = array(
			'Quotes'                      => 'Quotes are based on the information and measurements available at the time and are valid for the period stated on the quote. Final quantities may be confirmed after a site survey.',
			'Orders and payment'          => 'Orders are confirmed once the quote is accepted and any agreed deposit is received. Payment terms are stated on each quote or invoice.',
			'Samples and colours'         => 'Samples and on-screen images are for guidance. Natural variation in colour and texture between batches can occur.',
			'Site readiness'              => 'The client is responsible for providing clear access, suitable working hours and any building approvals required. Subfloors must be clean, dry and sound unless preparation is included in the quote.',
			'Delivery and installation'   => 'Delivery and installation dates are agreed in advance and may change due to stock, access or site conditions. We will keep you informed on WhatsApp.',
			'Warranty and care'           => 'Product warranties follow the manufacturer’s terms. Damage caused by misuse, incorrect cleaning products or conditions outside normal use is not covered.',
			'Liability'                   => 'Our liability is limited to the value of the goods and services supplied, to the extent permitted by UAE law.',
			'Governing law'               => 'These terms are governed by the laws of the United Arab Emirates and the Emirate of Dubai.',
		);
	}
	foreach ( $sections as $heading => $text ) {
		$html .= dgf_b_h( $heading ) . dgf_b_p( $text );
	}
	return dgf_b_group( $html, 'dgf-section dgf-legal' );
}

add_filter( 'the_content', 'dgf_replace_whatsapp_links', 20 );
/**
 * Swap "#whatsapp" links for a live WhatsApp chat URL.
 *
 * @param string $content Content.
 * @return string
 */
function dgf_replace_whatsapp_links( $content ) {
	if ( false === strpos( $content, '#whatsapp' ) ) {
		return $content;
	}
	$url = esc_url( dgf_wa_url( dgf_wa_message_for() ) );
	// Buttons that already set a target keep it; others get target/rel added.
	$content = preg_replace( '/href="#whatsapp"(?![^>]*\btarget=)/', 'href="' . $url . '" target="_blank" rel="noopener" data-dgf-wa', $content );
	return str_replace( 'href="#whatsapp"', 'href="' . $url . '" data-dgf-wa', $content );
}

add_filter( 'nav_menu_link_attributes', 'dgf_menu_whatsapp_link' );
/**
 * Menu items with the URL "#whatsapp" open WhatsApp too.
 *
 * @param array $atts Link attributes.
 * @return array
 */
function dgf_menu_whatsapp_link( $atts ) {
	if ( isset( $atts['href'] ) && '#whatsapp' === $atts['href'] ) {
		$atts['href']   = dgf_wa_url( dgf_wa_message_for() );
		$atts['target'] = '_blank';
		$atts['rel']    = 'noopener';
	}
	return $atts;
}
