<?php
/**
 * One-time SEO page builder: turns the compact data in seo-pages-data.json
 * into block content (same layout as the theme's own pages).
 */

function cfmx_p( $t, $c = null ) {
	if ( $c ) {
		return "<!-- wp:paragraph {\"className\":\"$c\"} -->\n<p class=\"$c\">$t</p>\n<!-- /wp:paragraph -->\n\n";
	}
	return "<!-- wp:paragraph -->\n<p>$t</p>\n<!-- /wp:paragraph -->\n\n";
}
function cfmx_h( $t, $l = 2 ) {
	if ( 2 === $l ) {
		return "<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">$t</h2>\n<!-- /wp:heading -->\n\n";
	}
	return "<!-- wp:heading {\"level\":$l} -->\n<h$l class=\"wp-block-heading\">$t</h$l>\n<!-- /wp:heading -->\n\n";
}
function cfmx_ul( $items, $c = 'cfm-checks' ) {
	$lis = '';
	foreach ( $items as $i ) {
		$lis .= "<!-- wp:list-item -->\n<li>$i</li>\n<!-- /wp:list-item -->";
	}
	return "<!-- wp:list {\"className\":\"$c\"} -->\n<ul class=\"wp-block-list $c\">$lis</ul>\n<!-- /wp:list -->\n\n";
}
function cfmx_sc( $code ) {
	return "<!-- wp:shortcode -->\n$code\n<!-- /wp:shortcode -->\n\n";
}
function cfmx_group( $c, $inner ) {
	return "<!-- wp:group {\"className\":\"$c\"} -->\n<div class=\"wp-block-group $c\">$inner</div>\n<!-- /wp:group -->\n\n";
}
function cfmx_cols( $columns, $c = null ) {
	$inner = '';
	foreach ( $columns as $col ) {
		$inner .= "<!-- wp:column -->\n<div class=\"wp-block-column\">$col</div>\n<!-- /wp:column -->";
	}
	if ( $c ) {
		return "<!-- wp:columns {\"className\":\"$c\"} -->\n<div class=\"wp-block-columns $c\">$inner</div>\n<!-- /wp:columns -->\n\n";
	}
	return "<!-- wp:columns -->\n<div class=\"wp-block-columns\">$inner</div>\n<!-- /wp:columns -->\n\n";
}
function cfmx_buttons( $label = 'View catalogues', $href = CFMX_SITE . '/catalogues/' ) {
	return "<!-- wp:buttons -->\n<div class=\"wp-block-buttons\"><!-- wp:button -->\n<div class=\"wp-block-button\">"
		. "<a class=\"wp-block-button__link wp-element-button\" href=\"#whatsapp\">Get a quote on WhatsApp</a></div>\n"
		. "<!-- /wp:button --><!-- wp:button {\"className\":\"is-style-outline\"} -->\n<div class=\"wp-block-button is-style-outline\">"
		. "<a class=\"wp-block-button__link wp-element-button\" href=\"$href\">$label</a></div>\n"
		. "<!-- /wp:button --></div>\n<!-- /wp:buttons -->\n\n";
}
function cfmx_outline_button( $label, $href ) {
	return "<!-- wp:buttons -->\n<div class=\"wp-block-buttons\"><!-- wp:button {\"className\":\"is-style-outline\"} -->\n"
		. "<div class=\"wp-block-button is-style-outline\"><a class=\"wp-block-button__link wp-element-button\" href=\"$href\">$label</a></div>\n"
		. "<!-- /wp:button --></div>\n<!-- /wp:buttons -->\n\n";
}
function cfmx_intro( $eyebrow, $heading, $paras, $form, $second = null ) {
	$left = cfmx_p( $eyebrow, 'cfm-eyebrow' ) . cfmx_h( $heading );
	foreach ( $paras as $x ) {
		$left .= cfmx_p( $x );
	}
	$left  .= $second ? cfmx_buttons( $second[0], $second[1] ) : cfmx_buttons();
	$right  = cfmx_sc( "[cfm_quote_form title=\"$form\"]" );
	return cfmx_group( 'cfm-section cfm-intro', cfmx_cols( array( $left, $right ), 'cfm-intro__cols' ) );
}
function cfmx_split( $e1, $h1, $l1, $e2, $h2, $l2 ) {
	$a = cfmx_p( $e1, 'cfm-eyebrow' ) . cfmx_h( $h1 ) . cfmx_ul( $l1 );
	$b = cfmx_p( $e2, 'cfm-eyebrow' ) . cfmx_h( $h2 ) . cfmx_ul( $l2 );
	return cfmx_group( 'cfm-section cfm-split', cfmx_cols( array( $a, $b ) ) );
}
function cfmx_features( $eyebrow, $heading, $items, $extra = '' ) {
	$out = cfmx_p( $eyebrow, 'cfm-eyebrow' ) . cfmx_h( $heading );
	for ( $s = 0; $s < count( $items ); $s += 4 ) {
		$cols = array();
		foreach ( array_slice( $items, $s, 4 ) as $k => $it ) {
			$cols[] = cfmx_p( sprintf( '%02d', $s + $k + 1 ), 'cfm-num' ) . cfmx_h( $it[0], 3 ) . cfmx_p( $it[1] );
		}
		$out .= cfmx_cols( $cols, 'cfm-features__grid' );
	}
	return cfmx_group( trim( 'cfm-section cfm-features ' . $extra ), $out );
}
function cfmx_prose( $eyebrow, $heading, $paras, $bullets = null, $c = 'cfm-section cfm-local' ) {
	$inner = cfmx_p( $eyebrow, 'cfm-eyebrow' ) . cfmx_h( $heading );
	foreach ( $paras as $x ) {
		$inner .= cfmx_p( $x );
	}
	if ( $bullets ) {
		$inner .= cfmx_ul( $bullets );
	}
	return cfmx_group( $c, $inner );
}
function cfmx_table( $head, $rows ) {
	$th = '';
	foreach ( $head as $c ) {
		$th .= "<th>$c</th>";
	}
	$trs = '';
	foreach ( $rows as $r ) {
		$trs .= '<tr>';
		foreach ( $r as $c ) {
			$trs .= "<td>$c</td>";
		}
		$trs .= '</tr>';
	}
	return "<!-- wp:table {\"hasFixedLayout\":false,\"className\":\"cfm-table\"} -->\n"
		. "<figure class=\"wp-block-table cfm-table\"><table><thead><tr>$th</tr></thead><tbody>$trs</tbody></table></figure>\n"
		. "<!-- /wp:table -->\n\n";
}
function cfmx_faq( $heading, $qa ) {
	$inner = cfmx_p( 'FAQs', 'cfm-eyebrow' ) . cfmx_h( $heading );
	foreach ( $qa as $x ) {
		$inner .= "<!-- wp:details -->\n<details class=\"wp-block-details\"><summary>{$x[0]}</summary>"
			. "<!-- wp:paragraph -->\n<p>{$x[1]}</p>\n<!-- /wp:paragraph --></details>\n<!-- /wp:details -->\n\n";
	}
	return cfmx_group( 'cfm-section cfm-faq', $inner );
}
function cfmx_children( $eyebrow, $heading, $parent, $limit = null, $after = '' ) {
	$attrs = "parent=\"$parent\"";
	if ( $limit ) {
		$attrs .= " limit=\"$limit\"";
	}
	return cfmx_group( 'cfm-section cfm-products', cfmx_p( $eyebrow, 'cfm-eyebrow' ) . cfmx_h( $heading ) . cfmx_sc( "[cfm_children $attrs]" ) . $after );
}
function cfmx_pills( $heading = 'Other areas we serve', $parent = 'areas-we-serve', $eyebrow = 'Nearby' ) {
	return cfmx_group( 'cfm-section cfm-areas', cfmx_p( $eyebrow, 'cfm-eyebrow' ) . cfmx_h( $heading ) . cfmx_sc( "[cfm_children parent=\"$parent\" style=\"pills\"]" ) );
}
function cfmx_links( $eyebrow, $heading, $links ) {
	$items = array();
	foreach ( $links as $l ) {
		$items[] = '<a href="' . CFMX_SITE . $l[1] . "\">{$l[0]}</a>";
	}
	return cfmx_group( 'cfm-section cfm-related', cfmx_p( $eyebrow, 'cfm-eyebrow' ) . cfmx_h( $heading ) . cfmx_ul( $items ) );
}
function cfmx_cta( $t ) {
	return cfmx_sc( "[cfm_cta title=\"$t\"]" );
}

/**
 * Build all page payloads from the data array.
 *
 * @param array $d Decoded seo-pages-data.json.
 * @return array
 */
function cfmx_build_pages( $d ) {
	$pages = array();
	$add   = function ( $key, $slug, $title, $content, $excerpt, $st, $sd, $kw, $type, $parent, $img = null, $place = '', $emirate = '', $order = 0 ) use ( &$pages ) {
		$pages[] = array(
			'key' => $key, 'slug' => $slug, 'title' => $title, 'content' => $content, 'excerpt' => $excerpt, 'parent' => $parent, 'img' => $img, 'order' => $order,
			'meta' => array( '_cfm_type' => $type, '_cfm_place' => $place, '_cfm_emirate' => $emirate, '_cfm_seo_title' => $st, '_cfm_seo_desc' => $sd, '_cfm_seo_keyword' => $kw ),
		);
	};
	$hub_slug = 'interiors-uae';

	// 1. Dubai areas.
	foreach ( $d['DUBAI_AREAS'] as $i => $a ) {
		$pl = $a['place'];
		$c  = cfmx_intro( 'Floor mats · Dubai', "Custom floor mats for $pl", $a['intro'], "Free quote in $pl" );
		$c .= cfmx_split( 'Coverage', "Where we work in $pl", $a['coverage'], 'Projects', "Typical projects in $pl", $a['projects'] );
		$c .= cfmx_children( 'Products', "Mats we supply in $pl", 'floor-mats', 6, cfmx_outline_button( 'See all 12 mat types', CFMX_SITE . '/floor-mats/' ) );
		$c .= cfmx_prose( 'Local know-how', "Planning your project in $pl", $a['local'], array( 'Free logo mockup and samples (photos and sizes on WhatsApp)', 'Mats made to your exact size, shape and colours', "Delivery and installation planned around your building's access rules", 'Formal quotes and tax invoices for businesses' ) );
		$c .= cfmx_sc( '[cfm_gallery source="all" limit="8" title="Recent mats"]' );
		$faqs   = $a['faqs'];
		$faqs[] = array( "How much do custom floor mats cost in $pl?", "Price depends on mat type, size, construction and any logo or installation. Send your measurements, logo and a photo of the space on WhatsApp and we will reply with options and a clear quote for $pl." );
		$c .= cfmx_faq( "Floor mats in $pl — FAQs", $faqs );
		$c .= cfmx_pills();
		$c .= cfmx_cta( "Custom mats in $pl — get your quote" );
		$add( $a['slug'], $a['slug'], $a['title'], $c, $a['excerpt'], $a['seo_title'], $a['seo_desc'], $a['kw'], 'location', 'areas-we-serve', null, $pl, 'Dubai', 20 + $i );
	}

	// 2. Interiors hub + emirate pages.
	$S   = $d['SERVICES'];
	$hub = cfmx_intro( 'Casa Vera Home · UAE', 'Curtains, blinds and carpets in every emirate', array(
		'Our Casa Vera Home interiors team measures, makes and installs curtains, blinds and carpets far beyond Dubai — in Abu Dhabi, Al Ain, Sharjah, Ajman, Ras Al Khaimah, Fujairah and Umm Al Quwain.',
		'Choose your emirate below to see the areas we cover, the products we install there and local tips for your home or business.',
	), 'Free measuring visit', array( 'All interiors', CFMX_SITE . '/interiors/' ) );
	foreach ( $d['EMIRATES'] as $e ) {
		$links = array();
		foreach ( array( 'curtains', 'blinds', 'carpets' ) as $s ) {
			$links[] = array( "{$S[$s]['label']} in {$e['name']}", "/$hub_slug/$s-{$e['key']}/" );
		}
		$hub .= cfmx_links( $e['name'], "Curtains, blinds & carpets in {$e['name']}", $links );
	}
	$hub .= cfmx_features( 'Why Casa Vera Home', 'One team for every window and floor', array(
		array( 'Free measuring', 'We visit, measure every window and floor and bring samples.' ),
		array( 'Made to measure', 'Curtains stitched and blinds made to your exact sizes.' ),
		array( 'Installed properly', 'Tracks, motors, underlay and finishing done by our own fitters.' ),
		array( 'All seven emirates', 'Planned visits across the UAE with confirmed dates.' ),
	) );
	$hub .= cfmx_faq( 'Interiors across the UAE — FAQs', array(
		array( 'Do you really install outside Dubai?', 'Yes — we schedule measuring and installation trips to every emirate and confirm the dates in your quote.' ),
		array( 'Is there a charge for measuring outside Dubai?', 'Tell us your location on WhatsApp; for most projects the measuring visit is free and we confirm before we travel.' ),
		array( 'Can you do curtains, blinds and carpets in one visit?', 'Yes — one team can measure all three, so your home is finished with matching colours and one installation schedule.' ),
	) );
	$hub .= cfmx_cta( 'Curtains, blinds or carpets anywhere in the UAE' );
	$add( $hub_slug, $hub_slug, 'Curtains, Blinds & Carpets Across the UAE', $hub,
		'Made-to-measure curtains, blinds and carpets measured and installed in Abu Dhabi, Al Ain, Sharjah, Ajman, Ras Al Khaimah, Fujairah and Umm Al Quwain.',
		'Curtains, Blinds & Carpets UAE | Abu Dhabi, Sharjah & More',
		'Curtains, blinds and carpets installed across the UAE by Casa Vera Home: Abu Dhabi, Al Ain, Sharjah, Ajman, Ras Al Khaimah, Fujairah and Umm Al Quwain.',
		'curtains blinds carpets uae', 'hub', 0, 318, '', '', 50 );

	$order = 0;
	foreach ( $d['EMIRATES'] as $e ) {
		foreach ( array( 'curtains', 'blinds', 'carpets' ) as $sk ) {
			$s     = $S[ $sk ];
			$n     = $e['name'];
			$fmt   = function ( $t ) use ( $n ) {
				return str_replace( '{name}', $n, $t );
			};
			$lab   = $s['label'];
			$low   = strtolower( $lab );
			$title = "$lab in $n";
			$c  = cfmx_intro( "$lab · $n", "$lab in $n, measured and installed", array( $s['lead'], "In $n we work in {$e['homes']}, as well as {$e['biz']}. {$s['offer']}" ), "Free $low quote in $n", array( "All $low", CFMX_SITE . "/{$s['hub']}/" ) );
			$c .= cfmx_features( 'What we install', "Popular $low in $n", $s['products'] );
			$c .= cfmx_split( 'Coverage', "Areas we cover in $n", $e['areas'], 'Options', 'Fabrics, finishes & extras', $s['options'] );
			$c .= cfmx_prose( 'Local know-how', "Planning $low in $n", array( $e['note'], $e['logistics'] ), array( 'Free measuring visit with samples', 'Written quote per room or area', 'Installation by our own team', 'After-sales support and alterations' ) );
			$c .= cfmx_links( 'Explore', "$lab services", $s['links'] );
			$qa = array();
			foreach ( $s['faqs'] as $f ) {
				$qa[] = array( $fmt( $f[0] ), $fmt( $f[1] ) );
			}
			$c .= cfmx_faq( "$title — FAQs", $qa );
			$c .= cfmx_pills( 'Curtains, blinds & carpets in other emirates', $hub_slug, 'Across the UAE' );
			$c .= cfmx_sc( '[cfm_parent_company]' );
			$c .= cfmx_cta( "$title — book a free measuring visit" );
			$add( "$sk-{$e['key']}", "$sk-{$e['key']}", $title, $c, $fmt( $s['excerpt'] ), $fmt( $s['seo_title'] ), $fmt( $s['seo_desc'] ), $fmt( $s['kw'] ), 'interior', $hub_slug, $s['img'], $n, $e['emirate'], $order );
			$order++;
		}
	}

	// 3. Events & exhibitions.
	$H  = $d['EVENTS_HUB'];
	$c  = cfmx_intro( 'Events & Exhibitions', 'Flooring that is ready when the doors open', array(
		'Exhibitions, launches, weddings and gala dinners all run to fixed dates. We supply and install exhibition carpet, stand flooring, red carpets, branded mats and floor graphics — on time and in your colours.',
		'We work with organisers, stand builders, planners and venues across Dubai World Trade Centre, Expo City Dubai, ADNEC, hotel ballrooms and outdoor venues.',
	), 'Event flooring quote', array( 'Exhibition carpet', CFMX_SITE . '/carpets/exhibition-carpet/' ) );
	$c .= cfmx_children( 'What we supply', 'Event & exhibition flooring', 'events-exhibitions' );
	$c .= cfmx_features( 'Why us', 'Built around your event schedule', array(
		array( 'Deadline-driven', 'Installation inside build-up windows, removal at breakdown.' ),
		array( 'Brand colours', 'Carpets, mats and graphics matched to your theme.' ),
		array( 'One supplier', 'Carpet, stand floors, branded mats and graphics together.' ),
		array( 'UAE-wide', 'Dubai, Abu Dhabi and venues across the emirates.' ),
	) );
	$c .= cfmx_split( 'Venues', 'Where we install', array( 'Dubai World Trade Centre', 'Expo City Dubai', 'ADNEC, Abu Dhabi', 'Hotel ballrooms and conference centres', 'Outdoor venues, tents and beaches', 'Malls and retail activations' ),
		'Events', 'Events we supply', array( 'Trade shows and exhibitions', 'Product launches and openings', 'Conferences and summits', 'Weddings and gala dinners', 'Sports and community events', 'Brand activations and pop-ups' ) );
	$c .= cfmx_faq( 'Event & exhibition flooring — FAQs', array(
		array( 'How quickly can you supply event carpet?', 'Stocked colours can often be arranged at short notice. Send your event date, venue and size on WhatsApp and we will confirm.' ),
		array( 'Do you remove flooring after the event?', 'Yes — removal at breakdown can be included in the quote.' ),
		array( 'Can you match our brand colours?', 'Yes — we offer a wide colour range and will suggest the closest match, with printed graphics for exact logos.' ),
	) );
	$c .= cfmx_links( 'Guides', 'Planning an event?', array(
		array( 'Best event & exhibition flooring in Dubai', '/buying-guides/best-event-exhibition-flooring-dubai/' ),
		array( 'How to choose an exhibition flooring contractor', '/buying-guides/exhibition-flooring-contractor-dubai/' ),
	) );
	$c .= cfmx_cta( 'Event or exhibition coming up? Get your flooring quote' );
	$add( $H['slug'], $H['slug'], $H['title'], $c, $H['excerpt'], $H['seo_title'], $H['seo_desc'], $H['kw'], 'hub', 0, $H['img'], '', '', 45 );

	foreach ( $d['EVENTS'] as $i => $ev ) {
		$lt = strtolower( $ev['title'] );
		$c  = cfmx_intro( 'Events & Exhibitions', "{$ev['title']} in Dubai & the UAE", $ev['intro'], "Get a price for $lt", array( 'All event flooring', CFMX_SITE . '/events-exhibitions/' ) );
		$c .= cfmx_features( 'Benefits', "Why choose our $lt", $ev['features'] );
		$c .= cfmx_split( 'Options', 'Options & finishes', $ev['options'], 'Where it’s used', 'Perfect for', $ev['uses'] );
		$c .= cfmx_features( 'How it works', 'From brief to event day', array(
			array( 'Send your brief', 'Event date, venue, size and your logo or colours on WhatsApp.' ),
			array( 'Mockup & quote', 'We send options, a mockup where relevant and a written quote.' ),
			array( 'Production', 'Made or prepared to your exact sizes and colours.' ),
			array( 'Install & remove', 'Laid before doors open and removed after if required.' ),
		), 'cfm-steps' );
		$c .= cfmx_faq( "{$ev['title']} — common questions", $ev['faqs'] );
		$c .= cfmx_pills( 'More event & exhibition flooring', 'events-exhibitions', 'Keep exploring' );
		$c .= cfmx_sc( '[cfm_parent_company]' );
		$c .= cfmx_cta( "Get a quote for $lt" );
		$add( $ev['slug'], $ev['slug'], $ev['title'], $c, $ev['excerpt'], $ev['seo_title'], $ev['seo_desc'], $ev['kw'], 'interior', 'events-exhibitions', $ev['img'], '', '', $i );
	}

	// 4. Buying guides.
	$G  = $d['GUIDES_HUB'];
	$c  = cfmx_intro( 'Buying guides', 'Choose the right mat, curtain, blind or carpet', array(
		'Not sure what to buy? These guides compare the top choices for UAE homes, offices, hotels and events — what each option is best for and what to watch out for.',
		'We also explain how to choose a reliable supplier or installer, with the questions to ask before you pay.',
	), 'Ask us for advice' );
	$c .= cfmx_children( 'Top choices', 'Guides & comparisons', 'buying-guides' );
	$c .= cfmx_cta( 'Still deciding? Ask us on WhatsApp' );
	$add( $G['slug'], $G['slug'], $G['title'], $c, $G['excerpt'], $G['seo_title'], $G['seo_desc'], $G['kw'], 'hub', 0, $G['img'], '', '', 60 );

	foreach ( $d['TOP_GUIDES'] as $i => $g ) {
		$c     = cfmx_intro( 'Buying guide · 2026', 'The short answer', array( $g['lead'], 'Every pick below links to the full product page with sizes, options and photos.' ), 'Get advice & a quote' );
		$picks = cfmx_p( 'Top choices', 'cfm-eyebrow' ) . cfmx_h( 'Our top choices at a glance' );
		foreach ( $g['picks'] as $k => $pk ) {
			$picks .= cfmx_h( ( $k + 1 ) . ". {$pk[0]}", 3 ) . cfmx_p( "<strong>Best for:</strong> {$pk[1]}. {$pk[2]} <a href=\"" . CFMX_SITE . "{$pk[3]}\">See " . strtolower( $pk[0] ) . ' →</a>' );
		}
		$c .= cfmx_group( 'cfm-section cfm-local', $picks );
		$c .= cfmx_group( 'cfm-section cfm-local', cfmx_p( 'Quick comparison', 'cfm-eyebrow' ) . cfmx_h( 'Which one should you choose?' ) . cfmx_table( $g['table'][0], $g['table'][1] ) );
		$c .= cfmx_prose( 'Buying tips', 'How to choose well', array( 'A few simple rules make a big difference to how long your purchase lasts and how good it looks.' ), $g['tips'] );
		$c .= cfmx_faq( explode( ':', $g['title'] )[0] . ' — FAQs', $g['faqs'] );
		$c .= cfmx_pills( 'More buying guides', 'buying-guides', 'Keep reading' );
		$c .= cfmx_cta( 'Want a recommendation for your space?' );
		$add( $g['slug'], $g['slug'], $g['title'], $c, $g['excerpt'], $g['seo_title'], $g['seo_desc'], $g['kw'], 'guide', 'buying-guides', $g['img'], '', '', $i );
	}

	foreach ( $d['PROVIDER_GUIDES'] as $i => $g ) {
		$c  = cfmx_intro( 'Supplier guide', 'Choosing with confidence', array( $g['lead'], 'Use this checklist to compare suppliers — and see how we measure up.' ), 'Compare our quote' );
		$c .= cfmx_split( 'What the best offer', 'What top providers offer', $g['offers'], 'Before you order', 'Questions to ask', $g['questions'] );
		$c .= cfmx_split( 'Watch out', 'Red flags to avoid', $g['red_flags'], 'Our promise', 'Why clients choose us', $g['why'] );
		$c .= cfmx_faq( explode( ':', $g['title'] )[0] . ' — FAQs', $g['faqs'] );
		$c .= cfmx_pills( 'More buying guides', 'buying-guides', 'Keep reading' );
		$c .= cfmx_sc( '[cfm_parent_company]' );
		$c .= cfmx_cta( 'Get a quote to compare' );
		$add( $g['slug'], $g['slug'], $g['title'], $c, $g['excerpt'], $g['seo_title'], $g['seo_desc'], $g['kw'], 'guide', 'buying-guides', $g['img'], '', '', 10 + $i );
	}
	return $pages;
}
