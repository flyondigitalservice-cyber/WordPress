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
 * @package CFM
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
function cfm_b_attrs( $attrs ) {
	return $attrs ? ' ' . wp_json_encode( $attrs, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) : '';
}

/**
 * Inline text: escaped, but allowing simple <a>, <strong>, <em>.
 *
 * @param string $text Text.
 * @return string
 */
function cfm_b_text( $text ) {
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
function cfm_b_p( $text, $class = '' ) {
	$attrs = $class ? array( 'className' => $class ) : array();
	$open  = $class ? '<p class="' . esc_attr( $class ) . '">' : '<p>';
	return "<!-- wp:paragraph" . cfm_b_attrs( $attrs ) . " -->\n" . $open . cfm_b_text( $text ) . "</p>\n<!-- /wp:paragraph -->\n\n";
}

/**
 * Heading block.
 *
 * @param string $text  Text.
 * @param int    $level Level.
 * @param string $class Extra class.
 * @return string
 */
function cfm_b_h( $text, $level = 2, $class = '' ) {
	$attrs = array();
	if ( 2 !== $level ) {
		$attrs['level'] = $level;
	}
	if ( $class ) {
		$attrs['className'] = $class;
	}
	$classes = trim( 'wp-block-heading ' . $class );
	return "<!-- wp:heading" . cfm_b_attrs( $attrs ) . " -->\n<h{$level} class=\"" . esc_attr( $classes ) . '">' . cfm_b_text( $text ) . "</h{$level}>\n<!-- /wp:heading -->\n\n";
}

/**
 * List block.
 *
 * @param string[] $items Items.
 * @param string   $class Extra class.
 * @return string
 */
function cfm_b_list( $items, $class = '' ) {
	$attrs = $class ? array( 'className' => $class ) : array();
	$html  = "<!-- wp:list" . cfm_b_attrs( $attrs ) . " -->\n<ul class=\"" . esc_attr( trim( 'wp-block-list ' . $class ) ) . '">';
	foreach ( $items as $item ) {
		$html .= "<!-- wp:list-item -->\n<li>" . cfm_b_text( $item ) . "</li>\n<!-- /wp:list-item -->";
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
function cfm_b_group( $inner, $class ) {
	return "<!-- wp:group" . cfm_b_attrs( array( 'className' => $class ) ) . " -->\n<div class=\"wp-block-group " . esc_attr( $class ) . "\">" . $inner . "</div>\n<!-- /wp:group -->\n\n";
}

/**
 * Columns block.
 *
 * @param string[] $columns Inner markup per column.
 * @param string   $class   Class.
 * @return string
 */
function cfm_b_columns( $columns, $class = '' ) {
	$attrs = $class ? array( 'className' => $class ) : array();
	$html  = "<!-- wp:columns" . cfm_b_attrs( $attrs ) . " -->\n<div class=\"" . esc_attr( trim( 'wp-block-columns ' . $class ) ) . '">';
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
function cfm_b_buttons( $buttons ) {
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
		$html  .= "<!-- wp:button" . cfm_b_attrs( $attrs ) . " -->\n<div class=\"{$wrap}\"><a class=\"wp-block-button__link wp-element-button\" href=\"" . esc_url( $url ) . "\"{$target}>" . esc_html( $label ) . "</a></div>\n<!-- /wp:button -->";
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
function cfm_b_details( $question, $answer ) {
	return "<!-- wp:details -->\n<details class=\"wp-block-details\"><summary>" . esc_html( $question ) . '</summary>' . trim( cfm_b_p( $answer ) ) . "</details>\n<!-- /wp:details -->\n\n";
}

/**
 * Shortcode block.
 *
 * @param string $shortcode Shortcode.
 * @return string
 */
function cfm_b_sc( $shortcode ) {
	return "<!-- wp:shortcode -->\n" . $shortcode . "\n<!-- /wp:shortcode -->\n\n";
}

/**
 * Section heading pair (eyebrow + h2).
 *
 * @param string $eyebrow Eyebrow.
 * @param string $title   Title.
 * @return string
 */
function cfm_b_heading_pair( $eyebrow, $title ) {
	return cfm_b_p( $eyebrow, 'cfm-eyebrow' ) . cfm_b_h( $title );
}

/**
 * Escape a value for use inside a shortcode attribute.
 *
 * @param string $value Value.
 * @return string
 */
function cfm_sc_attr( $value ) {
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
function cfm_build_content( $slug, $page ) {
	switch ( $page['type'] ) {
		case 'home':
			return cfm_build_home();
		case 'product':
			return cfm_build_product( $page );
		case 'interior':
			return cfm_build_interior( $page );
		case 'service':
			return cfm_build_service( $page );
		case 'location':
			return cfm_build_location( $page );
		case 'hub':
			return cfm_build_hub( $slug, $page );
		case 'about':
			return cfm_build_about();
		case 'catalogues':
			return cfm_build_catalogues();
		case 'projects':
			return cfm_build_projects();
		case 'faqs':
			return cfm_build_faqs();
		case 'contact':
			return cfm_build_contact();
		case 'quote':
			return cfm_build_quote();
		case 'legal':
			return cfm_build_legal( $page['legal'] );
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
function cfm_section_intro( $eyebrow, $title, $paragraphs, $form_title ) {
	$left = cfm_b_heading_pair( $eyebrow, $title );
	foreach ( $paragraphs as $paragraph ) {
		$left .= cfm_b_p( $paragraph );
	}
	$left .= cfm_b_buttons(
		array(
			array( __( 'Get a quote on WhatsApp', 'cfm' ), '#whatsapp' ),
			array( __( 'View catalogues', 'cfm' ), cfm_page_url( 'catalogues' ), 'outline' ),
		)
	);
	$right = cfm_b_sc( '[cfm_quote_form title="' . cfm_sc_attr( $form_title ) . '"]' );
	return cfm_b_group( cfm_b_columns( array( $left, $right ), 'cfm-intro__cols' ), 'cfm-section cfm-intro' );
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
function cfm_section_features( $eyebrow, $title, $items, $class = '' ) {
	$cols = array();
	foreach ( $items as $i => $item ) {
		$cols[] = cfm_b_p( sprintf( '%02d', $i + 1 ), 'cfm-num' ) . cfm_b_h( $item[0], 3 ) . cfm_b_p( $item[1] );
	}
	return cfm_b_group( cfm_b_heading_pair( $eyebrow, $title ) . cfm_b_columns( $cols, 'cfm-features__grid' ), trim( 'cfm-section cfm-features ' . $class ) );
}

/**
 * FAQ section.
 *
 * @param string $title Title.
 * @param array  $faqs  [q, a] pairs.
 * @return string
 */
function cfm_section_faqs( $title, $faqs ) {
	$inner = cfm_b_heading_pair( __( 'FAQs', 'cfm' ), $title );
	foreach ( $faqs as $faq ) {
		$inner .= cfm_b_details( $faq[0], $faq[1] );
	}
	return cfm_b_group( $inner, 'cfm-section cfm-faq' );
}

/**
 * Four standard process steps.
 *
 * @return array
 */
function cfm_default_steps() {
	return array(
		array( __( 'Message us on WhatsApp', 'cfm' ), __( 'Share your logo, the size you need and a photo of the space.', 'cfm' ) ),
		array( __( 'Free mockup & samples', 'cfm' ), __( 'We send a digital mockup and options — and samples if you want to feel them.', 'cfm' ) ),
		array( __( 'Clear quote', 'cfm' ), __( 'A written quote with the right mat for every entrance or area.', 'cfm' ) ),
		array( __( 'Made, delivered & fitted', 'cfm' ), __( 'Your mats are made to size, delivered and installed where needed.', 'cfm' ) ),
	);
}

/**
 * Product page.
 *
 * @param array $p Data.
 * @return string
 */
function cfm_build_product( $p ) {
	$title = $p['title'];
	$html  = cfm_section_intro( __( 'Overview', 'cfm' ), sprintf( '%s in Dubai &amp; the UAE', $title ), $p['intro'], sprintf( 'Get a price for %s', $title ) );
	$html .= cfm_section_features( __( 'Benefits', 'cfm' ), sprintf( 'Why choose %s', strtolower( $title ) ), $p['benefits'] );

	$specs = cfm_b_heading_pair( __( 'Specifications', 'cfm' ), __( 'Options &amp; specifications', 'cfm' ) ) . cfm_b_list( $p['specs'], 'cfm-checks' );
	$uses  = cfm_b_heading_pair( __( 'Applications', 'cfm' ), __( 'Where it’s used', 'cfm' ) ) . cfm_b_list( $p['uses'], 'cfm-checks' );
	$html .= cfm_b_group( cfm_b_columns( array( $specs, $uses ) ), 'cfm-section cfm-split' );

	$html .= cfm_b_group(
		cfm_b_sc( '[cfm_catalogues scope="page" title="' . cfm_sc_attr( $title . ' — downloads' ) . '"]' ) .
		cfm_b_sc( '[cfm_gallery title="' . cfm_sc_attr( $title . ' — project photos' ) . '"]' ),
		'cfm-section cfm-media'
	);

	$html .= cfm_b_group(
		cfm_b_heading_pair( __( 'Service area', 'cfm' ), sprintf( '%s across Dubai and every emirate', $title ) ) .
		cfm_b_p( sprintf( 'We supply and deliver %s throughout Dubai and across Abu Dhabi, Sharjah, Ajman, Ras Al Khaimah, Fujairah, Umm Al Quwain and Al Ain. Choose your area for local details:', strtolower( $title ) ) ) .
		cfm_b_sc( '[cfm_children parent="areas-we-serve" style="pills"]' ),
		'cfm-section cfm-areas'
	);

	$html .= cfm_section_faqs( sprintf( '%s — common questions', $title ), $p['faqs'] );
	$html .= cfm_b_group( cfm_b_heading_pair( __( 'Keep exploring', 'cfm' ), __( 'Related mats', 'cfm' ) ) . cfm_b_sc( '[cfm_related limit="3"]' ), 'cfm-section cfm-related' );
	$html .= cfm_b_sc( '[cfm_cta title="' . cfm_sc_attr( sprintf( 'Get a quote for %s', strtolower( $title ) ) ) . '"]' );
	return $html;
}

/**
 * Interior service page (curtains, blinds, carpets, wallpaper, upholstery, flooring).
 *
 * @param array $p Data.
 * @return string
 */
function cfm_build_interior( $p ) {
	$title = $p['title'];
	$html  = cfm_section_intro( sprintf( '%s · %s', $p['category'], cfm_opt( 'parent_name' ) ), sprintf( '%s in Dubai &amp; the UAE', $title ), $p['intro'], sprintf( 'Get a price for %s', $title ) );
	$html .= cfm_section_features( __( 'Benefits', 'cfm' ), sprintf( 'Why choose %s', strtolower( $title ) ), $p['benefits'] );

	$specs = cfm_b_heading_pair( __( 'Options', 'cfm' ), __( 'Options &amp; finishes', 'cfm' ) ) . cfm_b_list( $p['specs'], 'cfm-checks' );
	$uses  = cfm_b_heading_pair( __( 'Where it’s used', 'cfm' ), __( 'Perfect for', 'cfm' ) ) . cfm_b_list( $p['uses'], 'cfm-checks' );
	$html .= cfm_b_group( cfm_b_columns( array( $specs, $uses ) ), 'cfm-section cfm-split' );

	$html .= cfm_b_group(
		cfm_b_sc( '[cfm_catalogues scope="page" title="' . cfm_sc_attr( $title . ' — downloads' ) . '"]' ) .
		cfm_b_sc( '[cfm_gallery title="' . cfm_sc_attr( $title . ' — project photos' ) . '"]' ),
		'cfm-section cfm-media'
	);
	$html .= cfm_section_features( __( 'How it works', 'cfm' ), __( 'Measured, made and installed', 'cfm' ), cfm_default_steps(), 'cfm-steps' );
	$html .= cfm_b_group(
		cfm_b_heading_pair( __( 'Service area', 'cfm' ), __( 'Across Dubai and every emirate', 'cfm' ) ) .
		cfm_b_p( sprintf( 'We measure and install %s throughout Dubai and across Abu Dhabi, Sharjah, Ajman, Ras Al Khaimah, Fujairah, Umm Al Quwain and Al Ain. Send your location on WhatsApp to book a visit.', strtolower( $title ) ) ),
		'cfm-section cfm-areas'
	);
	$html .= cfm_section_faqs( sprintf( '%s — common questions', $title ), $p['faqs'] );
	$html .= cfm_b_group( cfm_b_heading_pair( __( 'Keep exploring', 'cfm' ), sprintf( 'More %s services', strtolower( $p['category'] ) ) ) . cfm_b_sc( '[cfm_related limit="3"]' ), 'cfm-section cfm-related' );
	$html .= cfm_b_sc( '[cfm_parent_company]' );
	$html .= cfm_b_sc( '[cfm_cta title="' . cfm_sc_attr( sprintf( 'Get a quote for %s', strtolower( $title ) ) ) . '"]' );
	return $html;
}

/**
 * Service page.
 *
 * @param array $p Data.
 * @return string
 */
function cfm_build_service( $p ) {
	$title = $p['title'];
	$html  = cfm_section_intro( __( 'Service', 'cfm' ), sprintf( '%s across Dubai &amp; the UAE', $title ), $p['intro'], sprintf( 'Book: %s', $title ) );
	$html .= cfm_section_features( __( 'How it works', 'cfm' ), __( 'Simple, step by step', 'cfm' ), $p['steps'], 'cfm-steps' );
	$html .= cfm_b_group(
		cfm_b_heading_pair( __( 'Products', 'cfm' ), __( 'Mats we design and make', 'cfm' ) ) .
		cfm_b_sc( '[cfm_children parent="floor-mats" limit="6"]' ) .
		cfm_b_buttons( array( array( __( 'See all mats', 'cfm' ), cfm_page_url( 'floor-mats' ), 'outline' ) ) ),
		'cfm-section cfm-products'
	);
	$html .= cfm_b_sc( '[cfm_gallery title="' . cfm_sc_attr( $title . ' — recent work' ) . '"]' );
	$html .= cfm_section_faqs( sprintf( '%s — common questions', $title ), $p['faqs'] );
	$html .= cfm_b_group( cfm_b_heading_pair( __( 'More services', 'cfm' ), __( 'Other ways we can help', 'cfm' ) ) . cfm_b_sc( '[cfm_related limit="2"]' ), 'cfm-section cfm-related' );
	$html .= cfm_b_sc( '[cfm_cta]' );
	return $html;
}

/**
 * Location page.
 *
 * @param array $p Data.
 * @return string
 */
function cfm_build_location( $p ) {
	$place = $p['place'];
	$short = preg_replace( '/\s*\(.*\)$/', '', $place );
	if ( preg_match( '/\((.*)\)/', $place, $m ) ) {
		$short = $m[1];
	}
	$html = cfm_section_intro( sprintf( 'Floor mats · %s', $p['emirate'] ), sprintf( 'Custom floor mats for %s', $place ), $p['intro'], sprintf( 'Free quote in %s', $short ) );

	$left  = cfm_b_heading_pair( __( 'Coverage', 'cfm' ), sprintf( 'Where we work in %s', $short ) ) . cfm_b_list( $p['communities'], 'cfm-checks cfm-checks--pins' );
	$right = cfm_b_heading_pair( __( 'Projects', 'cfm' ), sprintf( 'Typical projects in %s', $short ) ) . cfm_b_list( $p['projects'], 'cfm-checks' );
	$html .= cfm_b_group( cfm_b_columns( array( $left, $right ) ), 'cfm-section cfm-split' );

	$html .= cfm_b_group(
		cfm_b_heading_pair( __( 'Products', 'cfm' ), sprintf( 'Mats we supply in %s', $short ) ) .
		cfm_b_sc( '[cfm_children parent="floor-mats" limit="6"]' ) .
		cfm_b_buttons( array( array( __( 'See all 12 mat types', 'cfm' ), cfm_page_url( 'floor-mats' ), 'outline' ) ) ),
		'cfm-section cfm-products'
	);

	$html .= cfm_b_group(
		cfm_b_heading_pair( __( 'Local know-how', 'cfm' ), sprintf( 'Planning your project in %s', $short ) ) .
		cfm_b_p( $p['note'] ) .
		cfm_b_list(
			array(
				__( 'Free logo mockup and samples (photos and sizes on WhatsApp)', 'cfm' ),
				__( 'Documents for building management or community approval', 'cfm' ),
				__( 'Delivery and installation planned around your access rules', 'cfm' ),
				__( 'Mats made to your exact size, shape and colours', 'cfm' ),
			),
			'cfm-checks'
		),
		'cfm-section cfm-local'
	);

	$html .= cfm_b_sc( '[cfm_gallery source="all" limit="8" title="Recent mats"]' );

	$faqs   = $p['faqs'];
	$faqs[] = array(
		sprintf( 'How much do custom floor mats cost in %s?', $short ),
		sprintf( 'The price depends on the mat type, size, construction and any logo or installation. Send your measurements, logo and a photo of the space on WhatsApp, and we will reply with options and a clear quote for %s.', $short ),
	);
	$html .= cfm_section_faqs( sprintf( 'Floor mats in %s — FAQs', $short ), $faqs );
	$html .= cfm_b_group( cfm_b_heading_pair( __( 'Nearby', 'cfm' ), __( 'Other areas we serve', 'cfm' ) ) . cfm_b_sc( '[cfm_children parent="areas-we-serve" style="pills"]' ), 'cfm-section cfm-areas' );
	$html .= cfm_b_sc( '[cfm_cta title="' . cfm_sc_attr( sprintf( 'Custom mats in %s — get your quote', $short ) ) . '"]' );
	return $html;
}

/**
 * Hub pages (Products, Services, Areas).
 *
 * @param string $slug Slug.
 * @param array  $p    Data.
 * @return string
 */
function cfm_build_hub( $slug, $p ) {
	$html = '';
	if ( 'products' === $p['hub_of'] ) {
		$html .= cfm_b_group(
			cfm_b_heading_pair( __( 'The range', 'cfm' ), __( 'The right mat for every door, floor and job', 'cfm' ) ) .
			cfm_b_p( 'Most buildings need more than one kind of mat: a scraper outside, an absorbent logo mat inside, anti-fatigue mats where staff stand and anti-slip matting in wet areas. Explore each type below, or send us photos on WhatsApp and we will recommend the mix.' ) .
			cfm_b_sc( '[cfm_children parent="' . $slug . '"]' ),
			'cfm-section cfm-products'
		);
		$html .= cfm_b_group(
			cfm_b_heading_pair( __( 'Quick guide', 'cfm' ), __( 'Which mat do I need?', 'cfm' ) ) .
			cfm_b_list(
				array(
					'<strong>Branding at the door:</strong> printed or inlaid custom logo mats',
					'<strong>Outside the entrance:</strong> rubber, ribbed or coil scraper mats',
					'<strong>Inside lobbies &amp; vestibules:</strong> absorbent entrance mats or a recessed system',
					'<strong>Kitchens, counters &amp; workshops:</strong> anti-fatigue mats',
					'<strong>Pools, showers &amp; changing rooms:</strong> anti-slip wet-area matting',
					'<strong>Gyms, playrooms &amp; cars:</strong> rubber gym, kids play and custom-fit car mats',
				),
				'cfm-checks'
			) .
			cfm_b_buttons( array( array( __( 'Ask which mat suits me', 'cfm' ), '#whatsapp' ), array( __( 'Download catalogues', 'cfm' ), cfm_page_url( 'catalogues' ), 'outline' ) ) ),
			'cfm-section cfm-guide'
		);
	} elseif ( 'interiors' === $p['hub_of'] ) {
		$html .= cfm_b_group(
			cfm_b_heading_pair( cfm_opt( 'parent_name' ), __( 'Interiors, flooring &amp; upholstery', 'cfm' ) ) .
			cfm_b_p( 'Alongside our custom floor mats, the Casa Vera Home team measures, makes and installs curtains, blinds, carpets, wallpaper, upholstery and flooring for homes, offices and hotels across the UAE.' ) .
			cfm_b_sc( '[cfm_children slugs="' . implode( ',', cfm_interior_category_slugs() ) . '"]' ),
			'cfm-section cfm-products'
		);
		$html .= cfm_section_features( __( 'How it works', 'cfm' ), __( 'Measured, made and installed', 'cfm' ), cfm_default_steps(), 'cfm-steps' );
		$html .= cfm_b_sc( '[cfm_parent_company]' );
	} elseif ( 'interior-category' === $p['hub_of'] ) {
		$html .= cfm_b_group(
			cfm_b_heading_pair( cfm_opt( 'parent_name' ), sprintf( '%s in Dubai &amp; the UAE', $p['title'] ) ) .
			cfm_b_p( $p['intro'] ) .
			cfm_b_sc( '[cfm_children parent="' . $slug . '"]' ),
			'cfm-section cfm-products'
		);
		$html .= cfm_section_features( __( 'How it works', 'cfm' ), __( 'Measured, made and installed', 'cfm' ), cfm_default_steps(), 'cfm-steps' );
		$html .= cfm_b_group(
			cfm_b_heading_pair( __( 'More services', 'cfm' ), __( 'Explore other Casa Vera Home services', 'cfm' ) ) .
			cfm_b_sc( '[cfm_children slugs="' . implode( ',', array_diff( cfm_interior_category_slugs(), array( $slug ) ) ) . '"]' ),
			'cfm-section cfm-related'
		);
		$html .= cfm_b_sc( '[cfm_parent_company]' );
	} elseif ( 'services' === $p['hub_of'] ) {
		$html .= cfm_b_group(
			cfm_b_heading_pair( __( 'End to end', 'cfm' ), __( 'From first message to finished mat', 'cfm' ) ) .
			cfm_b_p( 'We handle the whole job — advice, logo design, samples, measuring, production, delivery and installation — so you deal with one team and one quote.' ) .
			cfm_b_sc( '[cfm_children parent="' . $slug . '"]' ),
			'cfm-section cfm-products'
		);
		$html .= cfm_section_features( __( 'How it works', 'cfm' ), __( 'Four simple steps', 'cfm' ), cfm_default_steps(), 'cfm-steps' );
	} else {
		$html .= cfm_b_group(
			cfm_b_heading_pair( __( 'Dubai', 'cfm' ), __( 'Custom floor mats across Dubai', 'cfm' ) ) .
			cfm_b_p( 'From Marina and Downtown towers to Palm villas and Al Quoz warehouses — local pages with the communities we cover and the mats we typically supply.' ) .
			cfm_b_sc( '[cfm_children parent="' . $slug . '" emirate="dubai"]' ),
			'cfm-section cfm-products'
		);
		$html .= cfm_b_group(
			cfm_b_heading_pair( __( 'UAE', 'cfm' ), __( 'Floor mats in every emirate', 'cfm' ) ) .
			cfm_b_p( 'We deliver across the UAE. For orders outside Dubai we usually start with photos and measurements on WhatsApp, then plan delivery or a combined measuring and installation visit.' ) .
			cfm_b_sc( '[cfm_children parent="' . $slug . '" emirate="other"]' ),
			'cfm-section cfm-products'
		);
	}
	$html .= cfm_b_sc( '[cfm_cta]' );
	return $html;
}

/**
 * Home page (hero is rendered by the template from the page title/excerpt/featured image).
 *
 * @return string
 */
function cfm_build_home() {
	$html  = cfm_b_sc( '[cfm_stats]' );
	$left  = cfm_b_heading_pair( __( 'Why us', 'cfm' ), __( 'One specialist team for every mat you need', 'cfm' ) );
	$left .= cfm_b_p( 'We focus on mats — so we know which one belongs at a hotel entrance, a mall door, a restaurant kitchen, a lift, a pool deck or a family car. Send your logo and size and we reply with a free mockup and a clear recommendation.' );
	$left .= cfm_b_p( 'Every mat is made to your exact size and colours, delivered across the UAE and — for recessed and wall-to-wall systems — installed by our own fitters.' );
	$left .= cfm_b_buttons( array( array( __( 'Get a quote on WhatsApp', 'cfm' ), '#whatsapp' ), array( __( 'About us', 'cfm' ), cfm_page_url( 'about' ), 'outline' ) ) );
	$right = cfm_b_list(
		array(
			'Free digital logo mockup before you order',
			'Logo, entrance, scraper, coir &amp; recessed matting',
			'Anti-fatigue, anti-slip, rubber gym &amp; kids mats',
			'Custom-fit car mats and hotel day mats',
			'Made to any size — delivered across all seven emirates',
			'Every enquiry answered on WhatsApp',
		),
		'cfm-checks cfm-checks--lg'
	);
	$html .= cfm_b_group( cfm_b_columns( array( $left, $right ) ), 'cfm-section cfm-split cfm-why' );

	$html .= cfm_b_group(
		cfm_b_heading_pair( __( 'Products', 'cfm' ), __( 'Mats made for the way you use them', 'cfm' ) ) .
		cfm_b_sc( '[cfm_children parent="floor-mats" limit="12"]' ) .
		cfm_b_buttons( array( array( __( 'View all mats', 'cfm' ), cfm_page_url( 'floor-mats' ), 'outline' ) ) ),
		'cfm-section cfm-products'
	);

	$html .= cfm_b_group(
		cfm_b_heading_pair( cfm_opt( 'parent_name' ) . ' services', __( 'Interiors, flooring &amp; upholstery', 'cfm' ) ) .
		cfm_b_p( 'Our parent company also measures, makes and installs curtains, blinds, carpets, wallpaper, upholstery and flooring for homes, offices and hotels.' ) .
		cfm_b_sc( '[cfm_children slugs="' . implode( ',', cfm_interior_category_slugs() ) . '"]' ) .
		cfm_b_buttons( array( array( __( 'All interior services', 'cfm' ), cfm_page_url( 'interiors' ), 'outline' ) ) ),
		'cfm-section cfm-interiors'
	);

	$html .= cfm_section_features( __( 'How it works', 'cfm' ), __( 'From your logo to your doorstep', 'cfm' ), cfm_default_steps(), 'cfm-steps' );

	$sectors = array(
		array( 'Offices &amp; retail', sprintf( 'Branded entrances that greet every client and customer. <a href="%s">Custom logo mats →</a>', esc_url( cfm_page_url( 'custom-logo-mats' ) ) ) ),
		array( 'Hotels &amp; towers', sprintf( 'Lobby systems and fresh day mats in every lift. <a href="%s">Lift &amp; day mats →</a>', esc_url( cfm_page_url( 'lift-day-mats' ) ) ) ),
		array( 'Restaurants &amp; industry', sprintf( 'Comfort and grip for long shifts on hard floors. <a href="%s">Anti-fatigue mats →</a>', esc_url( cfm_page_url( 'anti-fatigue-mats' ) ) ) ),
		array( 'Villas &amp; cars', sprintf( 'Personalised doormats and custom-fit car mats. <a href="%s">Car floor mats →</a>', esc_url( cfm_page_url( 'car-floor-mats' ) ) ) ),
	);
	$html .= cfm_section_features( __( 'Who we work with', 'cfm' ), __( 'Mats for every kind of space', 'cfm' ), $sectors, 'cfm-sectors' );

	$html .= cfm_b_group(
		cfm_b_heading_pair( __( 'Areas we serve', 'cfm' ), __( 'Across Dubai and all seven emirates', 'cfm' ) ) .
		cfm_b_sc( '[cfm_children parent="areas-we-serve" style="pills"]' ),
		'cfm-section cfm-areas'
	);

	$html .= cfm_b_group(
		cfm_b_columns(
			array(
				cfm_b_heading_pair( __( 'Catalogues', 'cfm' ), __( 'Download the full range as PDF', 'cfm' ) ) . cfm_b_p( 'Colours, constructions and sizes for every mat — view them right here in your browser, download them, or ask on WhatsApp and we will send the one that fits your project.' ),
				cfm_b_buttons( array( array( __( 'Open catalogues', 'cfm' ), cfm_page_url( 'catalogues' ) ), array( __( 'Request on WhatsApp', 'cfm' ), '#whatsapp', 'outline' ) ) ),
			),
			'cfm-banner__cols'
		),
		'cfm-section cfm-banner'
	);

	$html .= cfm_b_sc( '[cfm_gallery source="all" limit="8" title="Recent mats"]' );

	$html .= cfm_section_faqs(
		__( 'Floor mat questions, answered', 'cfm' ),
		array(
			array( 'Can you put my logo on a floor mat?', 'Yes. Send your logo on WhatsApp with the size you need and we reply with a free digital mockup. Logo mats are printed or inlaid in your colours and made to any size or shape.' ),
			array( 'Do you deliver outside Dubai?', 'Yes. We deliver and install across Abu Dhabi, Sharjah, Ajman, Ras Al Khaimah, Fujairah, Umm Al Quwain and Al Ain.' ),
			array( 'How do I get a price?', 'Send the size, a photo of the space and your logo (if any) on WhatsApp. We reply with the recommended mat, a mockup and a clear quote.' ),
			array( 'How long does a custom mat take?', 'Most custom logo mats are ready about 7–14 working days after you approve the mockup. Stock mats can often be delivered within days.' ),
		)
	);
	$html .= cfm_b_sc( '[cfm_parent_company]' );
	$html .= cfm_b_sc( '[cfm_cta]' );
	return $html;
}

/**
 * About page.
 *
 * @return string
 */
function cfm_build_about() {
	$html  = cfm_b_group(
		cfm_b_columns(
			array(
				cfm_b_heading_pair( __( 'Who we are', 'cfm' ), __( 'Floor mat specialists, backed by Casa Vera Home', 'cfm' ) ) .
				cfm_b_p( 'Custom Floor Mats focuses on one thing: the mats that protect floors, keep people safe and carry your brand. We help businesses, hotels, property managers, restaurants and homeowners choose, design and install the right mat for every entrance and workspace.' ) .
				cfm_b_p( sprintf( 'We are part of <a href="%s">Casa Vera Home</a>, a UAE flooring, interiors and home-solutions company. That gives our clients the backing of an established business, with a specialist team dedicated to matting.', esc_url( cfm_opt( 'parent_url' ) ) ) ) .
				cfm_b_buttons( array( array( __( 'Talk to us on WhatsApp', 'cfm' ), '#whatsapp' ), array( __( 'Visit Casa Vera Home', 'cfm' ), cfm_opt( 'parent_url' ), 'outline', true ) ) ),
				cfm_b_list(
					array(
						'Specialists in custom and commercial matting',
						'Free digital mockup for every logo mat',
						'Samples before you decide',
						'Made to any size, shape and colour',
						'Coverage across all seven emirates',
					),
					'cfm-checks cfm-checks--lg'
				),
			)
		),
		'cfm-section cfm-split'
	);
	$html .= cfm_section_features(
		__( 'What we value', 'cfm' ),
		__( 'How we work', 'cfm' ),
		array(
			array( 'Right mat, right place', 'We match each mat to its job, so your budget goes where it works hardest.' ),
			array( 'Straight answers', 'Clear quotes and honest advice — including when a cheaper option will do.' ),
			array( 'Detail matters', 'Sharp logos, exact sizes and clean edges — every time.' ),
			array( 'Fast communication', 'Every enquiry handled on WhatsApp, so you are never waiting on email.' ),
		)
	);
	$html .= cfm_section_features( __( 'Our process', 'cfm' ), __( 'Four simple steps', 'cfm' ), cfm_default_steps(), 'cfm-steps' );
	$html .= cfm_b_sc( '[cfm_gallery source="all" limit="8" title="Recent mats"]' );
	$html .= cfm_b_sc( '[cfm_parent_company]' );
	$html .= cfm_b_sc( '[cfm_cta]' );
	return $html;
}

/**
 * Catalogues page.
 *
 * @return string
 */
function cfm_build_catalogues() {
	$html  = cfm_b_group(
		cfm_b_heading_pair( __( 'Downloads', 'cfm' ), __( 'Floor mat catalogues (PDF)', 'cfm' ) ) .
		cfm_b_p( 'Tap <strong>View PDF</strong> to flip through a catalogue right here on the page, <strong>Download</strong> to save it, or <strong>Ask price</strong> to get a quote for anything you see on WhatsApp.' ) .
		cfm_b_sc( '[cfm_catalogues]' ),
		'cfm-section cfm-catalogue-section'
	);
	$html .= cfm_b_group(
		cfm_b_heading_pair( __( 'Can’t find it?', 'cfm' ), __( 'Need a specific data sheet?', 'cfm' ) ) .
		cfm_b_p( 'Consultants, fit-out contractors and procurement teams: we can send technical data sheets, colour cards and samples for approvals. Tell us the product and project on WhatsApp.' ) .
		cfm_b_buttons( array( array( __( 'Request a data sheet', 'cfm' ), '#whatsapp' ) ) ),
		'cfm-section cfm-local'
	);
	$html .= cfm_b_sc( '[cfm_cta]' );
	return $html;
}

/**
 * Projects page.
 *
 * @return string
 */
function cfm_build_projects() {
	$html  = cfm_b_group(
		cfm_b_heading_pair( __( 'Our work', 'cfm' ), __( 'Recent mats we have made', 'cfm' ) ) .
		cfm_b_p( 'A selection of mats we have designed, made and installed. Tap any photo to enlarge it — and message us on WhatsApp if you would like something similar.' ) .
		cfm_b_sc( '[cfm_gallery source="all" limit="60"]' ),
		'cfm-section cfm-projects'
	);
	$html .= cfm_section_features(
		__( 'Sectors', 'cfm' ),
		__( 'Projects we deliver', 'cfm' ),
		array(
			array( 'Corporate &amp; retail', 'Logo mats for offices, showrooms, banks and stores.' ),
			array( 'Hospitality', 'Hotel entrances, lift day mats and restaurant kitchens.' ),
			array( 'Property &amp; facilities', 'Tower lobbies, recessed systems and car-park doors.' ),
			array( 'Homes &amp; cars', 'Personalised doormats, play mats and car mats.' ),
		)
	);
	$html .= cfm_b_sc( '[cfm_cta title="Want a mat like these?"]' );
	return $html;
}

/**
 * FAQs page.
 *
 * @return string
 */
function cfm_build_faqs() {
	$html  = cfm_section_faqs(
		__( 'Choosing your mat', 'cfm' ),
		array(
			array( 'Printed or inlaid logo mat — which is better?', 'Printed mats reproduce fine detail, gradients and photos. Inlaid mats are cut from solid coloured pile and suit bold, simple logos that must last for many years. We advise after seeing your artwork.' ),
			array( 'What size should my entrance mat be?', 'Visitors need four to six steps on matting to remove most dirt. For a home door a standard mat is fine; busy commercial entrances benefit from 3–6 metres split between outdoor scraper and indoor wiper zones.' ),
			array( 'Which mats can go outdoors?', 'Rubber scraper, PVC coil and rubber-moulded logo mats are made for full outdoor use. Nylon logo mats and coir are best at covered entrances.' ),
			array( 'What file do you need for my logo?', 'A vector file (AI, EPS, PDF or SVG) is ideal. A clear PNG or JPG is fine to start — our designers will advise.' ),
		)
	);
	$html .= cfm_section_faqs(
		__( 'Production, care &amp; installation', 'cfm' ),
		array(
			array( 'How long does a custom mat take?', 'Most logo mats are ready about 7–14 working days after mockup approval. Recessed systems and large orders are scheduled to your programme.' ),
			array( 'Will the mat slide on marble or tiles?', 'No. Our mats use high-grip rubber backings with bevelled edges designed for smooth stone and tile floors.' ),
			array( 'How do I clean a logo mat?', 'Vacuum regularly and spot-clean spills. Nylon logo mats can be washed or pressure-washed periodically; we send care instructions with every order.' ),
			array( 'Do you install recessed matting?', 'Yes. We measure existing matwells or work with your fit-out team, then make and install the system to size.' ),
		)
	);
	$html .= cfm_section_faqs(
		__( 'Prices, delivery &amp; areas', 'cfm' ),
		array(
			array( 'How much does a custom floor mat cost?', 'It depends on the type, size, construction and logo. Send your size, photo and artwork on WhatsApp for a clear quote.' ),
			array( 'Which areas do you cover?', 'All of Dubai plus Abu Dhabi, Sharjah, Ajman, Ras Al Khaimah, Fujairah, Umm Al Quwain and Al Ain.' ),
			array( 'Is the logo mockup free?', 'Yes. Digital mockups are free with no obligation to order.' ),
			array( 'Do you supply single mats or only bulk?', 'Both — from one personalised doormat to hundreds of branded mats for a hotel group or retail chain.' ),
		)
	);
	$html .= cfm_b_sc( '[cfm_cta title="Still have a question?" text="Ask us on WhatsApp — a real person from our matting team will answer."]' );
	return $html;
}

/**
 * Contact page.
 *
 * @return string
 */
function cfm_build_contact() {
	$left  = cfm_b_heading_pair( __( 'Talk to us', 'cfm' ), __( 'WhatsApp is the fastest way to reach us', 'cfm' ) );
	$left .= cfm_b_p( 'Send your logo, the size you need and a photo of the space. We reply with a free mockup, options and a clear quote — and can book a site visit for larger entrances.' );
	$left .= cfm_b_sc( '[cfm_contact_info]' );
	$left .= cfm_b_buttons( array( array( __( 'Chat on WhatsApp', 'cfm' ), '#whatsapp' ) ) );
	$right = cfm_b_sc( '[cfm_quote_form title="Send an enquiry"]' );
	$html  = cfm_b_group( cfm_b_columns( array( $left, $right ), 'cfm-intro__cols' ), 'cfm-section cfm-intro' );
	$html .= cfm_b_sc( '[cfm_map]' );
	$html .= cfm_b_sc( '[cfm_parent_company]' );
	return $html;
}

/**
 * Quote page.
 *
 * @return string
 */
function cfm_build_quote() {
	$left  = cfm_b_sc( '[cfm_quote_form title="Your project"]' );
	$right = cfm_b_heading_pair( __( 'What happens next', 'cfm' ), __( 'A quote without the wait', 'cfm' ) );
	$right .= cfm_b_list(
		array(
			'Your details open in WhatsApp — just press send',
			'Our team replies with questions or options',
			'We send a free logo mockup and samples if needed',
			'You receive a clear, itemised quote',
		),
		'cfm-checks cfm-checks--lg'
	);
	$right .= cfm_b_p( 'Every enquiry is also saved on our system, so nothing is missed even if WhatsApp does not open on your device.', 'cfm-small' );
	$html   = cfm_b_group( cfm_b_columns( array( $left, $right ), 'cfm-intro__cols' ), 'cfm-section cfm-intro' );
	$html  .= cfm_b_group( cfm_b_heading_pair( __( 'Browse first?', 'cfm' ), __( 'Explore our mats', 'cfm' ) ) . cfm_b_sc( '[cfm_children parent="floor-mats" limit="4"]' ), 'cfm-section cfm-products' );
	return $html;
}

/**
 * Legal pages (starter text — review with your legal adviser).
 *
 * @param string $which privacy|terms.
 * @return string
 */
function cfm_build_legal( $which ) {
	$brand = cfm_opt( 'brand_name' );
	$html  = cfm_b_p( sprintf( '<em>Last updated: %s. This is starter text — please review it with your legal adviser and adjust it to your business.</em>', wp_date( 'j F Y' ) ), 'cfm-small' );
	if ( 'privacy' === $which ) {
		$sections = array(
			'Who we are'                  => sprintf( '%s is the custom matting division of Casa Vera Home, operating in the United Arab Emirates. This policy explains how we handle personal information shared through this website and WhatsApp.', $brand ),
			'What we collect'             => 'When you send an enquiry we collect the details you provide — such as your name, phone/WhatsApp number, email, location, the product you are interested in, your area size and your message — together with the page you sent it from.',
			'How we use it'               => 'We use your details only to reply to your enquiry, prepare quotes and mockups, arrange deliveries and installations, and provide after-sales support. We do not sell your personal information.',
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
			'Samples and colours'         => 'Samples, mockups and on-screen images are for guidance. Small variations in colour and texture between screen, sample and production batches can occur. Custom-made mats are produced to the approved mockup and cannot be returned unless faulty.',
			'Site readiness'              => 'The client is responsible for providing clear access, suitable working hours and any building approvals required. Matwells and floors must be clean, dry and sound unless preparation is included in the quote.',
			'Delivery and installation'   => 'Delivery and installation dates are agreed in advance and may change due to stock, access or site conditions. We will keep you informed on WhatsApp.',
			'Warranty and care'           => 'Product warranties follow the manufacturer’s terms. Damage caused by misuse, incorrect cleaning products or conditions outside normal use is not covered.',
			'Liability'                   => 'Our liability is limited to the value of the goods and services supplied, to the extent permitted by UAE law.',
			'Governing law'               => 'These terms are governed by the laws of the United Arab Emirates and the Emirate of Dubai.',
		);
	}
	foreach ( $sections as $heading => $text ) {
		$html .= cfm_b_h( $heading ) . cfm_b_p( $text );
	}
	return cfm_b_group( $html, 'cfm-section cfm-legal' );
}

add_filter( 'the_content', 'cfm_replace_whatsapp_links', 20 );
/**
 * Swap "#whatsapp" links for a live WhatsApp chat URL.
 *
 * @param string $content Content.
 * @return string
 */
function cfm_replace_whatsapp_links( $content ) {
	if ( false === strpos( $content, '#whatsapp' ) ) {
		return $content;
	}
	$url = esc_url( cfm_wa_url( cfm_wa_message_for() ) );
	// Buttons that already set a target keep it; others get target/rel added.
	$content = preg_replace( '/href="#whatsapp"(?![^>]*\btarget=)/', 'href="' . $url . '" target="_blank" rel="noopener" data-cfm-wa', $content );
	return str_replace( 'href="#whatsapp"', 'href="' . $url . '" data-cfm-wa', $content );
}

add_filter( 'nav_menu_link_attributes', 'cfm_menu_whatsapp_link' );
/**
 * Menu items with the URL "#whatsapp" open WhatsApp too.
 *
 * @param array $atts Link attributes.
 * @return array
 */
function cfm_menu_whatsapp_link( $atts ) {
	if ( isset( $atts['href'] ) && '#whatsapp' === $atts['href'] ) {
		$atts['href']   = cfm_wa_url( cfm_wa_message_for() );
		$atts['target'] = '_blank';
		$atts['rel']    = 'noopener';
	}
	return $atts;
}
