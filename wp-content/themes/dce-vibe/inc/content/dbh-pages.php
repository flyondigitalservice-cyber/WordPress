<?php
/**
 * Dubai Blinds Hub — home, category hubs, company pages, page list and menus.
 *
 * @package dce-vibe
 */

defined( 'ABSPATH' ) || exit;

function dbh_services_in( $cat ) {
	return array_keys( array_filter( dbh_services(), fn( $s ) => $s['cat'] === $cat ) );
}

function dbh_cards( $slugs ) {
	return array_map( 'dce_product_card', $slugs );
}

/** Hub copy per category. */
function dbh_hub_copy() {
	return array(
		'blinds'     => array( 'Blinds in Dubai, <em>made to measure</em>', 'Roller, blackout, venetian, vertical, Roman, motorized and office blinds — measured, made and installed across Dubai and the UAE.', array( 'roman-blinds', 1 ), 'Blinds Dubai | Roller, Blackout, Venetian & Motorized Blinds', 'Made-to-measure blinds in Dubai: roller, blackout, venetian, vertical, Roman, motorized and office blinds. Free site visit and installation across the UAE.' ),
		'curtains'   => array( 'Curtains in Dubai, <em>tailored to your windows</em>', 'Blackout, sheer, motorized and office curtains, villa installations, rods and tracks, plus alterations — all by one team.', array( 'wave-curtains', 2 ), 'Curtains Dubai | Blackout, Sheer & Motorized Curtains', 'Custom curtains in Dubai: blackout, sheer, motorized and office curtains, villa curtain installation, rods & tracks and curtain alterations. Free site visit.' ),
		'flooring'   => array( 'Flooring in Dubai, <em>supplied &amp; installed</em>', 'Wooden, vinyl, laminate, gym, epoxy, sports and rubber flooring for homes, offices, gyms and commercial spaces.', null, 'Flooring Dubai | Wood, Vinyl, Laminate, Gym & Epoxy Floors', 'Flooring supply and installation in Dubai: wooden, vinyl, laminate, gym, epoxy, sports and rubber flooring for homes and businesses. Free site visit.' ),
		'upholstery' => array( 'Upholstery in Dubai, <em>like new again</em>', 'Sofa, chair, restaurant seating, car seat and outdoor furniture upholstery, plus custom cushions — in fabric or leather.', null, 'Upholstery Dubai | Sofa, Chair, Restaurant & Outdoor Upholstery', 'Upholstery services in Dubai: sofa, chair, restaurant seating, car seat and outdoor furniture upholstery and custom cushions. Fabric or leather. Free quote.' ),
		'wallpaper'  => array( 'Wallpaper in Dubai, <em>beautifully installed</em>', '3D, textured, kids’ room and office wallpaper, custom murals, removal and commercial installation.', null, 'Wallpaper Dubai | 3D, Textured, Murals & Commercial Wallpaper', 'Wallpaper supply and installation in Dubai: 3D, textured, kids room and office wallpaper, custom murals, removal and commercial projects. Free site visit.' ),
		'carpets'    => array( 'Carpets in Dubai, <em>fitted to perfection</em>', 'Wall to wall carpet, carpet tiles, exhibition and office carpet, custom runners and carpet cleaning.', null, 'Carpets Dubai | Wall to Wall, Carpet Tiles & Office Carpet', 'Carpet supply and installation in Dubai: wall to wall carpet, carpet tiles, exhibition and office carpet, custom runners and carpet cleaning. Free site visit.' ),
	);
}

function dbh_build_hub( $cat ) {
	$c     = dbh_hub_copy()[ $cat ];
	$label = dbh_categories()[ $cat ][0];
	$out   = dce_sec_hero(
		array(
			'kicker' => $label . ' · Dubai &amp; UAE',
			'title'  => $c[0],
			'lead'   => $c[1],
			'img'    => $c[2] ? dce_img( $c[2][0], $c[2][1] ) : null,
			'chips'  => array( 'Free site visit', 'Supplied &amp; installed', 'Dubai &amp; all UAE' ),
		)
	);
	$out  .= dce_sec_trust();
	$out  .= dce_sec_cards( $label, 'Our ' . strtolower( $label ) . ' <em>services</em>', 'Choose a service to see options, FAQs and book a free site visit.', dbh_cards( dbh_services_in( $cat ) ) );
	$out  .= dce_sec_steps();
	$out  .= dce_sec_faq(
		array(
			array( 'Is the site visit free?', 'Yes. We visit, measure and bring samples at no cost, then send an itemised quotation.' ),
			array( 'Do you work outside Dubai?', 'Yes — Abu Dhabi, Sharjah, Ajman, Umm Al Quwain, Ras Al Khaimah, Fujairah and Al Ain.' ),
			array( 'Do you handle commercial projects?', 'Yes, for offices, hotels, restaurants, clinics, schools and retail.' ),
		),
		$label . ' <em>FAQ</em>'
	);
	$out  .= dce_sec_cta( 'Send us a photo, <em>get a quote</em>', 'Share photos of your space on WhatsApp and we will suggest the best options and book a free site visit.' );
	$out  .= dce_sec_quote( '', '', 'Get a free <em>quote</em>' );
	return $out;
}

function dbh_build_home() {
	$out  = dce_sec_hero(
		array(
			'kicker' => 'Dubai Blinds Hub · Part of Casa Vera Home',
			'title'  => 'Blinds, curtains <em>&amp; interiors</em> in Dubai',
			'lead'   => 'One team for your windows, floors, walls and furniture — blinds, curtains, flooring, upholstery, wallpaper and carpets, measured and installed across the UAE.',
			'img'    => dce_img( 'zebra-blinds', 1 ),
			'chips'  => array( 'Free site visit &amp; measurement', 'Six services, one team', 'Professional installation' ),
		)
	);
	$out .= dce_sec_trust();

	$cats = array();
	foreach ( dbh_categories() as $key => $c ) {
		$copy   = dbh_hub_copy()[ $key ];
		$first  = dbh_services_in( $key );
		$img    = $copy[2] ? dce_img( $copy[2][0], $copy[2][1] ) : null;
		if ( ! $img && $first ) {
			$imgs = dce_product_images( $first[0] );
			$img  = $imgs ? $imgs[0] : null;
		}
		$cats[] = array(
			'title' => $c[0],
			'text'  => $copy[1],
			'url'   => home_url( '/' . $c[1] . '/' ),
			'img'   => $img,
			'more'  => 'View ' . strtolower( $c[0] ),
		);
	}
	$out .= dce_sec_cards( 'What we do', 'Six services, <em>one trusted team</em>', 'Everything for the inside of your home or business, measured and installed by our own team.', $cats, 'dce-sec' );
	$out .= dce_sec_cards( 'Blinds', 'Popular <em>blinds</em>', 'Made to measure for every window and budget.', dbh_cards( array( 'roller-blinds', 'blackout-blinds', 'venetian-blinds', 'motorized-blinds' ) ), 'dce-sec dce-sec-alt', dce_b_buttons( array( array( 'All blinds', home_url( '/blinds-dubai/' ), 'is-style-outline' ) ), 'dce-center' ) );
	$out .= dce_sec_cards( 'Curtains', 'Popular <em>curtains</em>', 'Tailored curtains, tracks and motors.', dbh_cards( array( 'blackout-curtains', 'sheer-curtains', 'motorized-curtains', 'villa-curtain-installation' ) ), 'dce-sec', dce_b_buttons( array( array( 'All curtains', home_url( '/curtains/' ), 'is-style-outline' ) ), 'dce-center' ) );
	$out .= dce_sec_split(
		array(
			'kicker'  => 'Why Dubai Blinds Hub',
			'title'   => 'One call for your <em>whole interior</em>',
			'paras'   => array(
				'Renovating or moving in? Instead of coordinating five different suppliers, one team measures your windows, floors, walls and furniture, and installs everything in a planned schedule.',
				'We are part of <a href="#parent">Casa Vera Home</a>, with a showroom at Empire Plaza Shopping Center, Shop 49, Naif Road, Deira, Dubai.',
			),
			'checks'  => array( 'Free site visit with samples', 'Blinds, curtains, flooring, upholstery, wallpaper and carpets', 'Homes, offices, hotels and restaurants', 'Clear, itemised quotations', 'Our own installation team' ),
			'img'     => dce_img( 'curtain-project-dubai', 2 ),
			'buttons' => array( array( 'About us', home_url( '/about-us/' ), 'is-style-outline' ) ),
		)
	);
	$out .= dce_sec_steps();
	$out .= dce_sec_cta( 'Browse our <em>fabric catalogues</em>', 'Curtain, upholstery and outdoor fabrics online — then we bring real samples to your site visit.', array( array( 'View catalogues', home_url( '/catalogue/' ), '' ), array( 'WhatsApp us', '#whatsapp', 'dce-wa' ) ) );
	$out .= dce_sec_parent();
	$out .= dce_sec_faq(
		array(
			array( 'Which services do you offer?', 'Blinds, curtains, flooring, upholstery, wallpaper and carpets — supply and installation for homes and businesses.' ),
			array( 'Is the site visit free?', 'Yes. We measure, bring samples and send an itemised quotation at no cost.' ),
			array( 'Do you cover the whole UAE?', 'Yes — Dubai, Abu Dhabi, Sharjah, Ajman, Umm Al Quwain, Ras Al Khaimah, Fujairah and Al Ain.' ),
			array( 'Where is your showroom?', 'Empire Plaza Shopping Center, Shop 49, Naif Road, Deira, Dubai.' ),
		)
	);
	$out .= dce_sec_quote();
	return $out;
}

function dbh_build_blinds_guide() {
	$out  = dce_sec_hero(
		array(
			'kicker' => 'Blinds guide · Dubai',
			'title'  => 'Choosing blinds in Dubai: <em>types, uses &amp; tips</em>',
			'lead'   => 'Not sure which blind is right for each room? Here is a practical guide to the main blind types we install in UAE homes and offices — and where each one works best.',
			'img'    => dce_img( 'wooden-blinds', 1 ),
			'chips'  => array( 'Free site visit', 'Samples brought to you', 'Installed by our team' ),
		)
	);
	$out .= dce_sec_trust();
	$out .= dce_sec_tiles(
		'Room by room',
		'Which blind <em>for which room?</em>',
		'A quick starting point — we confirm the best option on site.',
		array(
			array( 'Bedrooms', 'Blackout roller or blackout Roman blinds for deep sleep; add sheer curtains for softness.' ),
			array( 'Living rooms', 'Sunscreen rollers or venetian blinds to cut glare while keeping the view.' ),
			array( 'Kitchens &amp; bathrooms', 'Aluminium venetians or PVC vertical blinds that resist moisture.' ),
			array( 'Sliding doors', 'Vertical blinds that stack neatly to the side.' ),
			array( 'Offices', 'Sunscreen and vertical blinds for screen glare; blackout for meeting rooms.' ),
			array( 'High windows', 'Motorized blinds controlled by remote or app.' ),
		),
		'dce-sec'
	);
	$out .= dce_sec_cards( 'Blind types', 'Explore <em>our blinds</em>', '', dbh_cards( dbh_services_in( 'blinds' ) ), 'dce-sec dce-sec-alt' );
	$out .= dce_sec_split(
		array(
			'kicker'  => 'Good to know',
			'title'   => 'Measuring and fitting <em>matter most</em>',
			'paras'   => array(
				'Inside-recess blinds look built-in but need enough depth; outside-recess blinds overlap the frame and block more light. The control side, stack height and fixing surface all affect the result.',
				'That is why we measure every window ourselves and confirm details before anything is made.',
			),
			'checks'  => array( 'Recess depth and fixing surface checked', 'Control side and stack planned', 'Child-safe controls recommended', 'Motor options explained' ),
			'img'     => dce_img( 'aluminium-venetian-blinds', 1 ),
			'reverse' => true,
		)
	);
	$out .= dce_sec_faq(
		array(
			array( 'What are the most popular blinds in Dubai?', 'Roller blinds (blackout and sunscreen), venetian blinds and motorized blinds are the most requested.' ),
			array( 'Are blinds better than curtains?', 'Blinds are slimmer and practical; curtains add softness. Many rooms use both.' ),
			array( 'Can I see samples before ordering?', 'Yes, we bring samples to your free site visit.' ),
		),
		'Blinds <em>FAQ</em>'
	);
	$out .= dce_sec_quote( '', '', 'Book a free <em>blinds consultation</em>' );
	return $out;
}

function dbh_build_about() {
	$out  = dce_sec_hero(
		array(
			'kicker' => 'About us',
			'title'  => 'Dubai Blinds Hub, <em>part of Casa Vera Home</em>',
			'lead'   => 'We supply and install blinds, curtains, flooring, upholstery, wallpaper and carpets for homes and businesses across Dubai and the UAE.',
			'img'    => dce_img( 'curtain-project-dubai', 4 ),
			'chips'  => array( 'Free site visit', 'One team, six services', 'Dubai &amp; all UAE' ),
		)
	);
	$out .= dce_sec_trust();
	$out .= dce_sec_split(
		array(
			'kicker' => 'Who we are',
			'title'  => 'Interiors, <em>handled end to end</em>',
			'paras'  => array(
				'Dubai Blinds Hub is part of <a href="#parent">Casa Vera Home</a>, operated by Mukhtar Curtain LLC. Our showroom is at Empire Plaza Shopping Center, Shop 49, Naif Road, Deira, Dubai.',
				'We start with a free site visit: we measure, listen to how you use each space and bring samples. You receive a clear quotation, and our own team installs everything.',
			),
			'checks' => array(
				'Blinds: roller, blackout, venetian, vertical, Roman, motorized and office',
				'Curtains: blackout, sheer, motorized, office, villa installation, rods &amp; tracks, alterations',
				'Flooring, upholstery, wallpaper and carpets',
				'Homes, offices, hotels, restaurants, schools and clinics',
			),
			'img'    => dce_img( 'sheer-curtains', 2 ),
		)
	);
	$out .= dce_sec_steps();
	$out .= dce_sec_parent();
	$out .= dce_sec_quote();
	return $out;
}

function dbh_build_contact() {
	$map  = '<div class="dce-video" style="aspect-ratio:21/9"><iframe src="https://www.google.com/maps?q=Empire+Plaza+Shopping+Center+Naif+Road+Deira+Dubai&amp;output=embed" title="Showroom map" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe></div>';
	$out  = dce_sec_hero(
		array(
			'kicker'  => 'Contact us',
			'title'   => 'Let’s plan <em>your project</em>',
			'lead'    => 'Send photos of your windows, floors or furniture on WhatsApp, call us, or use the form below. We reply within working hours and book your free site visit.',
			'img'     => dce_img( 'eyelet-curtains', 1 ),
			'chips'   => array( 'Free site visit', 'Reply within working hours', 'Dubai &amp; all UAE' ),
			'buttons' => array(
				array( 'WhatsApp us now', '#whatsapp', 'dce-wa' ),
				array( 'Call ' . esc_html( dce_opt( 'phone' ) ), '#call', 'is-style-outline' ),
			),
		)
	);
	$out .= dce_sec_trust();
	$out .= dce_sec_quote( '', '', 'Send your <em>request</em>' );
	$out .= dce_b_section( dce_sec_head( 'Showroom', 'Find <em>our showroom</em>', 'Empire Plaza Shopping Center, Shop 49, Naif Road, Deira, Dubai.' ) . dce_b_group( dce_b_html( $map ), array( 'align' => 'wide' ) ) . dce_b_buttons( array( array( 'Open in Google Maps', '#map', '' ) ), 'dce-center' ), 'dce-sec' );
	$out .= dce_sec_parent();
	return $out;
}

function dbh_page_defs() {
	$defs = array(
		'home'         => array(
			'title' => 'Home',
			'build' => 'dbh_build_home',
			'thumb' => array( 'zebra-blinds', 1 ),
			'seo'   => array( 'Dubai Blinds Hub | Blinds, Curtains, Flooring & Upholstery Dubai', 'Blinds, curtains, flooring, upholstery, wallpaper and carpets in Dubai & the UAE — measured, supplied and installed by one team. Free site visit.' ),
		),
		'about-us'     => array(
			'title' => 'About Us',
			'build' => 'dbh_build_about',
			'thumb' => array( 'curtain-project-dubai', 4 ),
			'seo'   => array( 'About Dubai Blinds Hub | Part of Casa Vera Home', 'Dubai Blinds Hub is part of Casa Vera Home (Mukhtar Curtain LLC), supplying and installing blinds, curtains, flooring, upholstery, wallpaper and carpets in the UAE.' ),
		),
	);
	foreach ( dbh_categories() as $cat => $c ) {
		$copy           = dbh_hub_copy()[ $cat ];
		$defs[ $c[1] ]  = array(
			'title' => 'blinds-dubai' === $c[1] ? 'Blinds Dubai' : $c[0] . ' in Dubai',
			'build' => fn() => dbh_build_hub( $cat ),
			'thumb' => $copy[2],
			'seo'   => array( $copy[3], $copy[4] ),
			'svc'   => $c[0],
		);
	}
	$defs['blinds-in-dubai'] = array(
		'title' => 'Blinds In Dubai',
		'build' => 'dbh_build_blinds_guide',
		'thumb' => array( 'wooden-blinds', 1 ),
		'seo'   => array( 'Blinds in Dubai: Types, Uses & How to Choose | Dubai Blinds Hub', 'A practical guide to choosing blinds in Dubai — roller, blackout, venetian, vertical, Roman and motorized blinds room by room. Free site visit.' ),
	);
	foreach ( dbh_services() as $slug => $s ) {
		$defs[ $slug ] = array(
			'title' => html_entity_decode( $s['name'], ENT_QUOTES, 'UTF-8' ),
			'build' => fn() => dce_build_product( $slug ),
			'thumb' => 'p:' . $slug,
			'seo'   => array( $s['seo_title'], $s['seo_desc'] ),
			'svc'   => html_entity_decode( $s['name'], ENT_QUOTES, 'UTF-8' ),
		);
	}
	$defs['catalogue']      = array(
		'title' => 'Fabric Catalogues',
		'build' => 'dce_build_catalogue',
		'thumb' => array( 'printed-blinds', 4 ),
		'seo'   => array( 'Curtain & Upholstery Fabric Catalogues | Dubai Blinds Hub', 'Browse curtain, upholstery and outdoor fabric catalogues online, then book a free site visit to see real samples.' ),
	);
	$defs['contact-us']     = array(
		'title' => 'Contact Us',
		'build' => 'dbh_build_contact',
		'thumb' => array( 'eyelet-curtains', 1 ),
		'seo'   => array( 'Contact Dubai Blinds Hub | WhatsApp +971 50 859 9803', 'Book a free site visit for blinds, curtains, flooring, upholstery, wallpaper or carpets. WhatsApp or call +971 50 859 9803. Showroom on Naif Road, Deira.' ),
	);
	$defs['privacy-policy'] = array(
		'title' => 'Privacy Policy',
		'build' => 'dce_build_privacy',
		'seo'   => array( 'Privacy Policy | Dubai Blinds Hub', 'How Dubai Blinds Hub handles the information you share through our website, forms and WhatsApp.' ),
	);
	return $defs;
}

function dbh_build_menus( $ids ) {
	$parent = dce_opt( 'parent_url' );
	$main   = array();
	foreach ( dbh_categories() as $cat => $c ) {
		$children = array();
		foreach ( dbh_services_in( $cat ) as $slug ) {
			$children[] = array( html_entity_decode( dbh_services()[ $slug ]['name'], ENT_QUOTES, 'UTF-8' ), $slug );
		}
		if ( 'blinds' === $cat ) {
			$children[] = array( 'Blinds guide', 'blinds-in-dubai' );
		}
		$main[] = array( $c[0], $c[1], $children );
	}
	$main[] = array( 'Catalogues', 'catalogue' );
	$main[] = array( 'About', 'about-us', array( array( 'About us', 'about-us' ), array( 'Casa Vera Home', $parent ) ) );
	$main[] = array( 'Contact', 'contact-us' );
	dce_make_menu( 'Main Menu', 'primary', $main, $ids );

	$col = function ( $cats ) {
		$items = array();
		foreach ( $cats as $cat ) {
			$items[] = array( dbh_categories()[ $cat ][0], dbh_categories()[ $cat ][1] );
		}
		return $items;
	};
	dce_make_menu( 'Window treatments', 'footer_curtains', array_merge( $col( array( 'blinds', 'curtains' ) ), array( array( 'Roller blinds', 'roller-blinds' ), array( 'Blackout blinds', 'blackout-blinds' ), array( 'Motorized curtains', 'motorized-curtains' ), array( 'Villa curtains', 'villa-curtain-installation' ) ) ), $ids );
	dce_make_menu( 'Floors, walls & furniture', 'footer_blinds', $col( array( 'flooring', 'upholstery', 'wallpaper', 'carpets' ) ), $ids );
	dce_make_menu(
		'Company',
		'footer_company',
		array(
			array( 'About us', 'about-us' ),
			array( 'Fabric catalogues', 'catalogue' ),
			array( 'Blinds guide', 'blinds-in-dubai' ),
			array( 'Contact', 'contact-us' ),
			array( 'Casa Vera Home', $parent ),
		),
		$ids
	);
	dce_make_menu( 'Footer links', 'footer_legal', array( array( 'Privacy policy', 'privacy-policy' ), array( 'Contact', 'contact-us' ) ), $ids );
}
