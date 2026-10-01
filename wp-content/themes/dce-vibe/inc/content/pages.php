<?php
/**
 * Page definitions and content assembly for the whole site.
 *
 * @package dce-vibe
 */

defined( 'ABSPATH' ) || exit;

/* ------------------------------------------------------------------------
 * URL helpers.
 * --------------------------------------------------------------------- */

function dce_path( $slug ) {
	static $map = null;
	if ( null === $map ) {
		$map = array();
		foreach ( dce_products() as $s => $p ) {
			$map[ $s ] = '/' . $p['parent'] . '/' . $s . '/';
		}
		foreach ( dce_areas() as $s => $a ) {
			$map[ $s ] = '/areas-we-serve/' . $s . '/';
		}
	}
	if ( 'home' === $slug ) {
		return home_url( '/' );
	}
	return home_url( isset( $map[ $slug ] ) ? $map[ $slug ] : '/' . $slug . '/' );
}

function dce_product_card( $slug ) {
	$p = dce_products()[ $slug ];
	return array(
		'title' => $p['name'],
		'text'  => $p['card'],
		'url'   => dce_path( $slug ),
		'img'   => dce_img( $p['img'], 1 ),
	);
}

function dce_area_title( $slug ) {
	$a = dce_areas()[ $slug ];
	return isset( $a['title'] ) ? $a['title'] : 'Curtains &amp; Blinds in ' . $a['name'];
}

function dce_area_links( $region = null ) {
	$links = array();
	foreach ( dce_areas() as $slug => $a ) {
		if ( $region && $a['region'] !== $region ) {
			continue;
		}
		$links[] = array( $a['name'], dce_path( $slug ) );
	}
	return $links;
}

function dce_shared_faqs() {
	return array(
		array( 'Is the home visit and measurement free?', 'Yes. We visit, measure your windows and bring fabric and blind samples at no cost. You receive a clear quotation afterwards, with no obligation.' ),
		array( 'Which areas do you cover?', 'All of Dubai and the wider UAE, including Abu Dhabi, Sharjah, Ajman, Umm Al Quwain, Ras Al Khaimah, Fujairah and Al Ain.' ),
	);
}

function dce_chips() {
	return array( 'Free home visit', 'Made to measure', 'Installed by our team' );
}

/* ------------------------------------------------------------------------
 * Product pages.
 * --------------------------------------------------------------------- */

function dce_build_product( $slug ) {
	$p    = dce_products()[ $slug ];
	$imgs = dce_imgs( $p['img'] );
	$hero = $imgs ? $imgs[0] : null;
	$name = $p['name'];
	$lc   = strtolower( wp_strip_all_tags( html_entity_decode( $name ) ) );

	$out  = dce_sec_hero(
		array(
			'kicker' => $p['kicker'],
			'title'  => $p['h1'],
			'lead'   => $p['lead'],
			'img'    => $hero,
			'chips'  => dce_chips(),
		)
	);
	$out .= dce_sec_trust();
	$out .= dce_sec_split(
		array(
			'kicker'  => 'About ' . $name,
			'title'   => $p['what_title'],
			'paras'   => $p['paras'],
			'checks'  => $p['checks'],
			'img'     => count( $imgs ) > 1 ? $imgs[1] : $hero,
			'buttons' => array(
				array( 'Browse fabric catalogues', home_url( '/catalogue/' ), 'is-style-outline' ),
				array( 'Ask on WhatsApp', '#whatsapp', 'dce-wa' ),
			),
		)
	);
	if ( count( $imgs ) >= 3 ) {
		$out .= dce_sec_gallery( 'Gallery', 'Our <em>' . $lc . '</em> work', 'A selection of ' . $lc . ' we have made and installed. Every piece is measured for its window.', array_slice( $imgs, 0, 6 ), 'dce-sec' );
	}
	$out .= dce_sec_tiles( 'Options', $p['opt_title'], 'Everything is made to measure, so you choose exactly what suits your room — we bring samples to your home.', $p['tiles'] );
	$out .= dce_sec_steps();
	$out .= dce_sec_faq( array_merge( $p['faqs'], array_slice( dce_shared_faqs(), 0, 1 ) ), $name . ' <em>FAQ</em>' );

	$cards = array();
	foreach ( $p['related'] as $r ) {
		$cards[] = dce_product_card( $r );
	}
	$out .= dce_sec_cards( 'You may also like', 'Related <em>styles</em>', '', $cards, 'dce-sec dce-sec-alt' );
	$out .= dce_sec_links( 'Areas we serve', $name . ' across <em>Dubai &amp; the UAE</em>', 'Free home visits and installation in every emirate. Choose your area for local details.', dce_area_links(), 'dce-sec' );
	$out .= dce_sec_quote( wp_strip_all_tags( html_entity_decode( $name ) ) );
	return $out;
}

/* ------------------------------------------------------------------------
 * Area pages.
 * --------------------------------------------------------------------- */

function dce_build_area( $slug ) {
	$a    = dce_areas()[ $slug ];
	$img  = dce_img( $a['img'][0], $a['img'][1] );
	$img2 = dce_img( 'curtain-project-dubai', ( crc32( $slug ) % 8 ) + 1 );
	$name = $a['name'];

	$out  = dce_sec_hero(
		array(
			'kicker' => 'Areas we serve · ' . ( 'dubai' === $a['region'] ? 'Dubai' : 'UAE' ),
			'title'  => str_replace( 'in ' . $name, 'in <em>' . $name . '</em>', dce_area_title( $slug ) ),
			'lead'   => $a['lead'],
			'img'    => $img,
			'chips'  => array( 'Free home visit in ' . $name, 'Samples brought to you', 'Installed by our team' ),
		)
	);
	$out .= dce_sec_trust();
	$out .= dce_sec_split(
		array(
			'kicker'  => 'Local service',
			'title'   => 'Made to measure for <em>' . $name . '</em>',
			'paras'   => $a['paras'],
			'checks'  => array(
				'Free measurement visit at your home or office in ' . $name,
				'Fabric and blind samples to compare in your own light',
				'Clear, itemised quotation before anything is made',
				'Professional installation of tracks, rods, blinds and motors',
			),
			'img'     => $img2 ? $img2 : $img,
			'reverse' => true,
		)
	);
	$out .= dce_b_section( dce_sec_head( 'Communities', 'Where we work in <em>' . $name . '</em>', 'Including, but not limited to:' ) . dce_b_wide( dce_b_list( $a['places'], 'dce-chips' ) ), 'dce-sec dce-sec-alt' );

	$cards = array();
	foreach ( $a['popular'] as $s ) {
		$cards[] = dce_product_card( $s );
	}
	$out .= dce_sec_cards( 'Popular here', 'Favourite choices in <em>' . $name . '</em>', 'The curtains and blinds our clients in ' . $name . ' choose most often.', $cards, 'dce-sec' );
	$out .= dce_sec_steps();
	$out .= dce_sec_faq( array_merge( $a['faqs'], array_slice( dce_shared_faqs(), 0, 1 ) ), 'Curtains &amp; blinds in ' . $name . ' — <em>FAQ</em>' );

	$links = array();
	foreach ( $a['nearby'] as $n ) {
		$links[] = array( dce_areas()[ $n ]['name'], dce_path( $n ) );
	}
	$links[] = array( 'All areas we serve', home_url( '/areas-we-serve/' ) );
	$out    .= dce_sec_links( 'Nearby', 'We also serve <em>nearby areas</em>', '', $links, 'dce-sec dce-sec-alt' );
	$out    .= dce_sec_quote( '', $a['lead_area'], 'Book a free visit in <em>' . $name . '</em>' );
	return $out;
}

/* ------------------------------------------------------------------------
 * Hub and company pages.
 * --------------------------------------------------------------------- */

function dce_build_hub( $type ) {
	$is_c  = 'curtains' === $type;
	$cards = array();
	foreach ( dce_products() as $s => $p ) {
		if ( $p['parent'] === $type ) {
			$cards[] = dce_product_card( $s );
		}
	}
	$out  = dce_sec_hero(
		array(
			'kicker' => 'Made to measure · Dubai &amp; UAE',
			'title'  => $is_c ? 'Curtains in Dubai, <em>made to measure</em>' : 'Blinds in Dubai, <em>made to measure</em>',
			'lead'   => $is_c
				? 'Wave, pinch pleat, eyelet, American style, blackout, sheer and motorized curtains — stitched for your exact windows and installed by our own team across the UAE.'
				: 'Roller, sunscreen, zebra, Roman, vertical, wooden, aluminium, bamboo and printed blinds — made to fit your windows precisely and installed across Dubai and the UAE.',
			'img'    => $is_c ? dce_img( 'american-style-curtains', 2 ) : dce_img( 'roman-blinds', 1 ),
			'chips'  => dce_chips(),
		)
	);
	$out .= dce_sec_trust();
	$out .= dce_sec_cards( $is_c ? 'Curtain styles' : 'Blind types', $is_c ? 'Choose your <em>curtain style</em>' : 'Choose your <em>blind type</em>', $is_c ? 'Twelve curtain styles, hundreds of fabrics. Tap a style to see options, photos and FAQs.' : 'Ten blind systems for every room, budget and light level. Tap a type to learn more.', $cards );
	$out .= dce_sec_split(
		array(
			'kicker'  => 'Why made to measure',
			'title'   => $is_c ? 'Curtains that fit, <em>hang and last</em>' : 'Blinds that fit <em>perfectly</em>',
			'paras'   => $is_c
				? array( 'Ready-made curtains rarely suit UAE windows: they are too short, too narrow or leave light gaps. We measure each window and make every curtain to the exact width, drop and fullness it needs.', 'Choose from our fabric catalogues — sheers, linens, velvets, jacquards and blackout — and we handle tracks, rods, linings and installation.' )
				: array( 'A blind only works if it fits: correct width for the recess, the right drop and the right control side. We measure every window and make each blind to size.', 'We help you pick the right system for each room — darkness for bedrooms, glare control for living rooms and offices, moisture resistance for kitchens and bathrooms.' ),
			'checks'  => array( 'Free home visit and measurement', 'Samples brought to your home', 'Clear quotation before production', 'Installed by our own team' ),
			'img'     => $is_c ? dce_img( 'curtain-project-dubai', 2 ) : dce_img( 'wooden-blinds', 2 ),
			'buttons' => array( array( 'Browse fabric catalogues', home_url( '/catalogue/' ), 'is-style-outline' ) ),
		)
	);
	$out .= dce_sec_steps();
	$out .= dce_sec_faq(
		array_merge(
			$is_c
				? array(
					array( 'Which curtain style is best for floor-to-ceiling windows?', 'Wave curtains on a ceiling track are the most popular for large glass. Pinch pleats suit more formal rooms.' ),
					array( 'Can you make curtains with blackout lining?', 'Yes. Almost any fabric can be lined with blackout for bedrooms and media rooms.' ),
					array( 'Do you supply and install tracks and rods?', 'Yes — tracks, rods, brackets, motors and installation are included in our quotation.' ),
				)
				: array(
					array( 'Which blind is best for bedrooms?', 'Blackout roller blinds give the darkest room. Roman blinds with blackout lining add softness.' ),
					array( 'Which blind is best for living rooms?', 'Zebra blinds and sunscreen roller blinds let you control light while keeping the view.' ),
					array( 'Can blinds be motorized?', 'Yes. Roller, zebra and Roman blinds can be motorized with remote or app control.' ),
				),
			dce_shared_faqs()
		),
		$is_c ? 'Curtain <em>FAQ</em>' : 'Blinds <em>FAQ</em>'
	);
	$out .= dce_sec_cta( 'Not sure which <em>' . ( $is_c ? 'curtain' : 'blind' ) . '</em> to choose?', 'Send us a photo of your window on WhatsApp — we will suggest the best options and book a free home visit with samples.' );
	$out .= dce_sec_quote( '', '', 'Get a free <em>quote</em>' );
	return $out;
}

function dce_build_home() {
	$curtains = array();
	$blinds   = array();
	foreach ( array( 'wave-curtains', 'pinch-pleat-curtains', 'blackout-curtains', 'sheer-curtains', 'motorized-curtains', 'american-style-curtains' ) as $s ) {
		$curtains[] = dce_product_card( $s );
	}
	foreach ( array( 'blackout-roller-blinds', 'zebra-blinds', 'roman-blinds', 'wooden-blinds', 'sunscreen-roller-blinds', 'vertical-blinds' ) as $s ) {
		$blinds[] = dce_product_card( $s );
	}
	$gallery = array_merge( array_slice( dce_imgs( 'curtain-project-dubai' ), 0, 4 ), array( dce_img( 'motorized-curtains', 1 ), dce_img( 'sheer-curtains', 2 ), dce_img( 'blackout-curtains', 2 ), dce_img( 'motorized-curtains', 2 ) ) );
	$gallery = array_values( array_filter( $gallery ) );

	$out  = dce_sec_hero(
		array(
			'kicker' => 'Dubai Curtain Experts · Part of Casa Vera Home',
			'title'  => 'Curtains &amp; blinds, <em>made to measure</em> in Dubai',
			'lead'   => 'From wave curtains for sea-view apartments to blackout blinds for restful bedrooms — we measure, make and install beautiful window dressings across Dubai and the UAE.',
			'img'    => dce_img( 'wave-curtains', 2 ),
			'chips'  => array( 'Free home visit &amp; measurement', 'Hundreds of fabrics', 'Professional installation' ),
		)
	);
	$out .= dce_sec_trust();
	$out .= dce_sec_cards( 'Curtains', 'Curtains for <em>every room</em>', 'Twelve curtain styles made to the exact size of your windows.', $curtains, 'dce-sec', dce_b_buttons( array( array( 'View all curtain styles', home_url( '/curtains/' ), 'is-style-outline' ) ), 'dce-center' ) );
	$out .= dce_sec_cards( 'Blinds', 'Blinds that <em>fit perfectly</em>', 'From blackout rollers to natural bamboo — ten blind systems for homes and offices.', $blinds, 'dce-sec dce-sec-alt', dce_b_buttons( array( array( 'View all blind types', home_url( '/blinds/' ), 'is-style-outline' ) ), 'dce-center' ) );
	$out .= dce_sec_split(
		array(
			'kicker'  => 'Why clients choose us',
			'title'   => 'One studio, from <em>measure to install</em>',
			'paras'   => array(
				'We are the curtains and blinds studio of <a href="#parent">Casa Vera Home</a>. Our team visits your home, measures every window, helps you choose fabrics and systems, and installs everything — so you deal with one team from start to finish.',
				'Visit our showroom at Empire Plaza, Naif Road, Deira, or let us bring samples to you anywhere in the UAE.',
			),
			'checks'  => array( 'Free home visit with samples', 'Made-to-measure curtains and blinds', 'Tracks, rods and motors supplied and installed', 'Homes, offices, clinics and hospitality', 'Clear quotations with no surprises' ),
			'img'     => dce_img( 'curtain-project-dubai', 1 ),
			'buttons' => array( array( 'About us', home_url( '/about-us/' ), 'is-style-outline' ) ),
		)
	);
	$out .= dce_sec_gallery( 'Recent work', 'Projects across <em>Dubai</em>', 'Real homes, real windows. See more on our projects page.', array_slice( $gallery, 0, 8 ), 'dce-sec' );
	$out .= dce_sec_steps();
	$out .= dce_sec_cta( 'Browse our <em>fabric catalogues</em>', 'Explore our curtain fabric collections online — then we bring the real samples to your home.', array( array( 'View catalogues', home_url( '/catalogue/' ), '' ), array( 'WhatsApp us', '#whatsapp', 'dce-wa' ) ) );
	$out .= dce_sec_links( 'Areas we serve', 'Across Dubai <em>&amp; the UAE</em>', 'Free home visits and installation in every emirate.', dce_area_links(), 'dce-sec dce-sec-alt' );
	$out .= dce_sec_parent();
	$out .= dce_sec_faq(
		array_merge(
			array(
				array( 'How much do curtains and blinds cost in Dubai?', 'It depends on the size of your windows, the fabric or blind system and options like linings or motors. After the free home visit you receive an itemised quotation.' ),
				array( 'How long does it take?', 'Timelines depend on the fabric and quantity. We confirm the schedule in your quotation before production starts.' ),
				array( 'Do you supply offices and commercial projects?', 'Yes — offices, clinics, hospitals, restaurants, shops and hotels, including logo-printed sunscreen blinds and hospital curtain tracks.' ),
				array( 'Can I visit your showroom?', 'Yes. Empire Plaza Shopping Center, Shop 49, Naif Road, Deira, Dubai — Monday to Saturday, 8:00 AM to 5:30 PM.' ),
			),
			dce_shared_faqs()
		)
	);
	$out .= dce_sec_quote();
	return $out;
}

function dce_build_services() {
	$out  = dce_sec_hero(
		array(
			'kicker' => 'Our services',
			'title'  => 'Everything for your windows, <em>under one roof</em>',
			'lead'   => 'Measurement, design advice, made-to-measure production, hardware, motorization and installation — for homes, offices and commercial projects across the UAE.',
			'img'    => dce_img( 'motorized-curtains', 2 ),
			'chips'  => dce_chips(),
		)
	);
	$out .= dce_sec_trust();
	$out .= dce_sec_tiles(
		'What we do',
		'Our <em>services</em>',
		'One team handles your project from the first message to the final fitting.',
		array(
			array( 'Free home visit &amp; measuring', 'We measure every window precisely and check walls, ceilings and recesses for fixing.' ),
			array( 'Fabric &amp; design advice', 'Samples in your own light, plus advice on fullness, linings, colours and layering.' ),
			array( 'Made-to-measure curtains', 'Wave, pinch pleat, eyelet, American style, Roman and more — stitched to size.' ),
			array( 'Made-to-measure blinds', 'Roller, sunscreen, zebra, Roman, vertical, wooden, aluminium, bamboo and printed.' ),
			array( 'Tracks, rods &amp; hardware', 'Ceiling and wall tracks, decorative rods, brackets and tiebacks supplied and fitted.' ),
			array( 'Motorization', 'Wired and wire-free motors with remote, app and smart-home control.' ),
			array( 'Commercial projects', 'Offices, clinics, hospitals, schools, retail and hospitality.' ),
			array( 'Branded &amp; printed blinds', 'Logo sunscreen blinds and custom printed blinds for businesses and homes.' ),
		),
		'dce-sec'
	);
	$out .= dce_sec_split(
		array(
			'kicker'  => 'The home visit',
			'title'   => 'What happens at your <em>free home visit</em>',
			'paras'   => array( 'Our consultant arrives with fabric books and blind samples, measures each window and looks at how light enters the room at different times of day.', 'We talk through how you use each room — sleep, work, entertaining — and recommend the right combination of curtains and blinds. You receive an itemised quotation afterwards.' ),
			'checks'  => array( 'Precise measurement of every window', 'Samples to compare in your room', 'Advice on tracks, rods and motors', 'Itemised quotation with no obligation' ),
			'img'     => dce_img( 'curtain-project-dubai', 3 ),
			'reverse' => true,
			'class'   => 'dce-sec-alt',
		)
	);
	$out .= dce_sec_steps();
	$cards = array();
	foreach ( array( 'hospital-curtains', 'logo-sunscreen-blinds', 'vertical-blinds', 'sunscreen-roller-blinds' ) as $s ) {
		$cards[] = dce_product_card( $s );
	}
	$out .= dce_sec_cards( 'For businesses', 'Commercial <em>solutions</em>', 'Durable systems for offices, clinics, retail and hospitality across the UAE.', $cards, 'dce-sec' );
	$out .= dce_sec_faq( array_merge( array( array( 'Do you only install products you supply?', 'We focus on curtains, blinds, tracks and motors that we supply, so we can stand behind the full result.' ) ), dce_shared_faqs() ) );
	$out .= dce_sec_quote();
	return $out;
}

function dce_build_catalogue() {
	$c   = dce_catalogues();
	$out = dce_sec_hero(
		array(
			'kicker'  => 'Fabric catalogues',
			'title'   => 'Browse our <em>fabric catalogues</em>',
			'lead'    => 'Explore our curtain fabric collections online. Tap any catalogue to open the PDF, note the names you like and send them to us on WhatsApp — we bring the real samples to your home.',
			'img'     => dce_img( 'printed-blinds', 4 ),
			'chips'   => array( 'Open PDFs online', 'Real samples at your home visit', 'Hundreds of fabrics' ),
			'buttons' => array(
				array( 'Ask about a fabric on WhatsApp', '#whatsapp', 'dce-wa' ),
				array( 'Open all catalogues', dce_drive_folder( $c['all_folder'] ), 'is-style-outline', true ),
			),
		)
	);
	$out .= dce_sec_trust();

	$inner = '';
	foreach ( $c['curtain'] as $cat ) {
		$inner .= dce_b_group(
			dce_b_h( $cat[0], 3 ) . dce_b_p( $cat[1] . ' · PDF ' . $cat[3] ) . dce_b_buttons( array( array( 'View catalogue', dce_drive_file( $cat[2] ), '', true ) ) ),
			array( 'class' => 'dce-cat-card' )
		);
	}
	$out .= dce_b_section( dce_sec_head( 'Curtain fabrics', 'Curtain fabric <em>catalogues</em>', 'Large PDF files open in a new tab — allow a few seconds on mobile data.' ) . dce_b_wide( $inner, 'dce-catalogue' ), 'dce-sec' );

	$inner = '';
	foreach ( $c['folders'] as $f ) {
		$inner .= dce_b_group(
			dce_b_h( $f[0], 3 ) . dce_b_p( $f[1] ) . dce_b_buttons( array( array( 'Open collection', dce_drive_folder( $f[2] ), '', true ) ) ),
			array( 'class' => 'dce-cat-card dce-cat-folder' )
		);
	}
	foreach ( $c['brochures'] as $b ) {
		$inner .= dce_b_group(
			dce_b_h( $b[0], 3 ) . dce_b_p( $b[1] . ' · PDF ' . $b[3] ) . dce_b_buttons( array( array( 'View brochure', dce_drive_file( $b[2] ), '', true ), array( 'Learn more', dce_path( $b[4] ), 'is-style-outline' ) ) ),
			array( 'class' => 'dce-cat-card' )
		);
	}
	$out .= dce_b_section( dce_sec_head( 'More collections', 'Collections <em>&amp; brochures</em>', 'Outdoor fabrics, extra collections, custom printing and curtain motors.' ) . dce_b_wide( $inner, 'dce-catalogue' ), 'dce-sec dce-sec-alt' );
	$out .= dce_sec_video( 'Custom printing', 'Custom printed <em>fabric</em> in action', 'See how photos and artwork are printed onto fabric for curtains and blinds.', 'https://drive.google.com/file/d/' . $c['videos']['printed'] . '/preview' );
	$out .= dce_sec_cta( 'Prefer to feel the <em>fabric</em>?', 'Book a free home visit and we will bring the sample books to you, or visit our showroom on Naif Road, Deira.' );
	$out .= dce_sec_quote( '', '', 'Request <em>samples</em> at home' );
	return $out;
}

function dce_build_projects() {
	$c    = dce_catalogues();
	$imgs = array_merge( dce_imgs( 'curtain-project-dubai' ), dce_imgs( 'motorized-curtains' ), dce_imgs( 'sheer-curtains' ), dce_imgs( 'blackout-curtains' ) );
	$out  = dce_sec_hero(
		array(
			'kicker' => 'Our projects',
			'title'  => 'Recent work across <em>Dubai &amp; the UAE</em>',
			'lead'   => 'Villas, apartments, majlis and offices — a look at curtains and blinds we have measured, made and installed.',
			'img'    => dce_img( 'curtain-project-dubai', 1 ),
			'chips'  => dce_chips(),
		)
	);
	$out .= dce_sec_trust();
	$out .= dce_sec_gallery( 'Gallery', 'Installed by <em>our team</em>', 'Sheers, blackout layers, wave curtains and motorized tracks in real Dubai homes.', $imgs, 'dce-sec' );
	$out .= dce_sec_video( 'Video', 'A finished <em>installation</em>', 'A short walk-through of a completed curtain project.', 'https://drive.google.com/file/d/' . $c['videos']['projects'] . '/preview', 'dce-sec dce-sec-alt' );
	$cards = array();
	foreach ( array( 'wave-curtains', 'american-style-curtains', 'roman-curtains', 'wooden-blinds', 'roman-blinds', 'logo-sunscreen-blinds' ) as $s ) {
		$cards[] = dce_product_card( $s );
	}
	$out .= dce_sec_cards( 'By product', 'Explore projects <em>by style</em>', '', $cards, 'dce-sec' );
	$out .= dce_sec_quote( '', '', 'Start <em>your project</em>' );
	return $out;
}

function dce_build_about() {
	$out  = dce_sec_hero(
		array(
			'kicker' => 'About us',
			'title'  => 'The curtain studio of <em>Casa Vera Home</em>',
			'lead'   => 'Dubai Curtain Experts makes and installs made-to-measure curtains and blinds for homes and businesses across Dubai and the UAE.',
			'img'    => dce_img( 'wave-curtains', 1 ),
			'chips'  => dce_chips(),
		)
	);
	$out .= dce_sec_trust();
	$out .= dce_sec_split(
		array(
			'kicker' => 'Who we are',
			'title'  => 'Focused on <em>the window</em>',
			'paras'  => array(
				'We are part of <a href="#parent">Casa Vera Home</a>, operated by Mukhtar Curtain LLC. Our showroom is at Empire Plaza Shopping Center, Shop 49, Naif Road, Deira, Dubai.',
				'Our work starts with a visit to your address: we measure each window, look at the light and bring fabric and blind samples so you can choose in your own room.',
				'Once you confirm, your curtains and blinds are made to measure and installed by our team — tracks, rods, motors and all.',
			),
			'checks' => array(
				'Curtains: wave, pinch pleat, eyelet, American style, Roman, blackout, sheer, motorized, beaded, cinema, kids and hospital',
				'Blinds: roller, sunscreen, zebra, Roman, vertical, wooden, aluminium, bamboo, printed and logo blinds',
				'Homes, offices, shops, clinics and hospitality projects',
			),
			'img'    => dce_img( 'curtain-project-dubai', 4 ),
		)
	);
	$out .= dce_sec_steps();
	$out .= dce_sec_parent();
	$out .= dce_b_section(
		dce_b_columns(
			array(
				dce_b_p( 'Visit us', 'dce-kicker' ) . dce_b_h( 'Showroom &amp; <em>hours</em>' ) . dce_b_p( 'See fabrics and blinds in person at our Deira showroom, or book a free home visit and we will bring samples to you.' ) . dce_b_buttons( array( array( 'Get directions', '#map', '' ), array( 'WhatsApp us', '#whatsapp', 'dce-wa' ) ) ),
				dce_b_list(
					array(
						'<strong>Address</strong> <a href="#map">[dce_info key="address"]</a>',
						'<strong>Hours</strong> [dce_info key="hours"]',
						'<strong>Phone</strong> <a href="#call">[dce_info key="phone"]</a>',
						'<strong>Email</strong> <a href="#email">[dce_info key="email"]</a>',
					),
					'dce-contact-list'
				),
			)
		),
		'dce-sec dce-sec-alt'
	);
	$out .= dce_sec_quote();
	return $out;
}

function dce_build_contact() {
	$map  = '<div class="dce-video" style="aspect-ratio:21/9"><iframe src="https://www.google.com/maps?q=Empire+Plaza+Shopping+Center+Naif+Road+Deira+Dubai&amp;output=embed" title="Dubai Curtain Experts showroom map" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe></div>';
	$out  = dce_sec_hero(
		array(
			'kicker'  => 'Contact us',
			'title'   => 'Let’s dress <em>your windows</em>',
			'lead'    => 'Message us on WhatsApp with a few photos of your windows, call us, or fill in the form below. We reply within working hours and book your free home visit.',
			'img'     => dce_img( 'eyelet-curtains', 1 ),
			'chips'   => array( 'Free home visit', 'Reply within working hours', 'Dubai &amp; all UAE' ),
			'buttons' => array(
				array( 'WhatsApp us now', '#whatsapp', 'dce-wa' ),
				array( 'Call ' . esc_html( dce_opt( 'phone' ) ), '#call', 'is-style-outline' ),
			),
		)
	);
	$out .= dce_sec_trust();
	$out .= dce_sec_quote( '', '', 'Send your <em>request</em>' );
	$out .= dce_b_section( dce_sec_head( 'Showroom', 'Find <em>our showroom</em>', 'Empire Plaza Shopping Center, Shop 49, Naif Road, Deira, Dubai — Monday to Saturday, 8:00 AM to 5:30 PM.' ) . dce_b_group( dce_b_html( $map ), array( 'align' => 'wide' ) ) . dce_b_buttons( array( array( 'Open in Google Maps', '#map', '' ) ), 'dce-center' ), 'dce-sec' );
	$out .= dce_sec_links( 'Areas we serve', 'Free visits <em>across the UAE</em>', '', dce_area_links(), 'dce-sec dce-sec-alt' );
	$out .= dce_sec_parent();
	return $out;
}

function dce_build_areas_hub() {
	$dubai = array();
	$uae   = array();
	foreach ( dce_areas() as $slug => $a ) {
		$card = array(
			'title' => $a['name'],
			'text'  => wp_trim_words( $a['lead'], 18, '…' ),
			'url'   => dce_path( $slug ),
			'img'   => dce_img( $a['img'][0], $a['img'][1] ),
			'more'  => 'Local details',
		);
		if ( 'dubai' === $a['region'] ) {
			$dubai[] = $card;
		} else {
			$uae[] = $card;
		}
	}
	$out  = dce_sec_hero(
		array(
			'kicker' => 'Areas we serve',
			'title'  => 'Curtains &amp; blinds across <em>Dubai and the UAE</em>',
			'lead'   => 'Free home visits, measurement and installation in every Dubai community and all seven emirates — from Dubai Marina to Al Ain and Fujairah.',
			'img'    => dce_img( 'motorized-curtains', 1 ),
			'chips'  => dce_chips(),
		)
	);
	$out .= dce_sec_trust();
	$out .= dce_sec_cards( 'Dubai', 'Dubai <em>communities</em>', 'From waterfront towers to family villa communities.', $dubai, 'dce-sec' );
	$out .= dce_sec_cards( 'UAE', 'Across the <em>Emirates</em>', 'Abu Dhabi, Sharjah, Ajman, Umm Al Quwain, Ras Al Khaimah, Fujairah and Al Ain.', $uae, 'dce-sec dce-sec-alt' );
	$out .= dce_sec_steps();
	$out .= dce_sec_faq( dce_shared_faqs() );
	$out .= dce_sec_quote();
	return $out;
}

function dce_build_blog() {
	$out  = dce_b_section( dce_sec_head( 'Guides &amp; ideas', 'Curtain &amp; blind <em>guides</em>', 'Practical advice on choosing, measuring and caring for curtains and blinds in UAE homes.' ) . dce_b_latest_posts( 12 ), 'dce-sec' );
	$out .= dce_sec_quote();
	return $out;
}

function dce_build_privacy() {
	$inner = dce_b_h( 'Privacy policy', 1 )
		. dce_b_p( 'This policy explains how Dubai Curtain Experts (Casa Vera Home, Mukhtar Curtain LLC) handles information you share through this website.', 'dce-lead' )
		. dce_b_h( 'What we collect' )
		. dce_b_p( 'When you use the request form we collect the details you enter: name, phone number, area, the product you are interested in and your message. When you contact us on WhatsApp, phone or email, we receive the information you choose to send.' )
		. dce_b_h( 'How we use it' )
		. dce_b_p( 'We use your details only to respond to your enquiry, arrange home visits, prepare quotations and deliver your order. Form submissions are stored securely on this website and emailed to our team so that no request is missed. We do not sell your information.' )
		. dce_b_h( 'WhatsApp' )
		. dce_b_p( 'Our form and buttons open WhatsApp, a service operated by WhatsApp LLC / Meta. Messages you send there are also subject to WhatsApp’s own privacy policy.' )
		. dce_b_h( 'Cookies and embedded content' )
		. dce_b_p( 'This site may load fonts, maps, videos and documents from third parties such as Google. These services may collect data according to their own policies.' )
		. dce_b_h( 'Contact' )
		. dce_b_p( 'For questions or to ask us to delete your information, email <a href="#email">[dce_info key="email"]</a> or call <a href="#call">[dce_info key="phone"]</a>.' );
	return dce_b_section( $inner, 'dce-sec dce-prose' );
}

/* ------------------------------------------------------------------------
 * Page list. Order matters: parents first.
 * --------------------------------------------------------------------- */

function dce_page_defs() {
	$defs = array(
		'home'           => array(
			'title' => 'Curtains &amp; Blinds in Dubai',
			'build' => 'dce_build_home',
			'thumb' => array( 'wave-curtains', 2 ),
			'seo'   => array( 'Curtains & Blinds Dubai | Made to Measure | Dubai Curtain Experts', 'Made-to-measure curtains and blinds in Dubai & the UAE: wave, blackout, sheer, motorized curtains and roller, zebra, Roman & wooden blinds. Free home visit.' ),
		),
		'curtains'       => array(
			'title' => 'Curtains in Dubai',
			'build' => fn() => dce_build_hub( 'curtains' ),
			'thumb' => array( 'american-style-curtains', 2 ),
			'seo'   => array( 'Curtains Dubai | Made-to-Measure Curtains & Installation', 'Made-to-measure curtains in Dubai: wave, pinch pleat, eyelet, American style, Roman, blackout, sheer and motorized curtains. Free home visit & measurement.' ),
			'svc'   => 'Made-to-measure curtains',
		),
		'blinds'         => array(
			'title' => 'Blinds in Dubai',
			'build' => fn() => dce_build_hub( 'blinds' ),
			'thumb' => array( 'roman-blinds', 1 ),
			'seo'   => array( 'Blinds Dubai | Roller, Zebra, Roman & Wooden Blinds Made to Measure', 'Made-to-measure blinds in Dubai: blackout & sunscreen rollers, zebra, Roman, vertical, wooden, aluminium, bamboo and printed blinds. Free home visit.' ),
			'svc'   => 'Made-to-measure blinds',
		),
	);
	foreach ( dce_products() as $slug => $p ) {
		$defs[ $slug ] = array(
			'title'  => wp_strip_all_tags( html_entity_decode( $p['name'] ) ) . ' in Dubai',
			'parent' => $p['parent'],
			'build'  => fn() => dce_build_product( $slug ),
			'thumb'  => array( $p['img'], 1 ),
			'seo'    => array( $p['seo_title'], $p['seo_desc'] ),
			'svc'    => wp_strip_all_tags( html_entity_decode( $p['name'] ) ),
		);
	}
	$defs['services']       = array(
		'title' => 'Our Services',
		'build' => 'dce_build_services',
		'thumb' => array( 'motorized-curtains', 2 ),
		'seo'   => array( 'Curtain & Blind Services Dubai | Measure, Make, Install, Motorize', 'Free home visit, made-to-measure curtains and blinds, tracks and rods, motorization and commercial projects across Dubai and the UAE.' ),
	);
	$defs['catalogue']      = array(
		'title' => 'Fabric Catalogues',
		'build' => 'dce_build_catalogue',
		'thumb' => array( 'printed-blinds', 4 ),
		'seo'   => array( 'Curtain Fabric Catalogues | Dubai Curtain Experts', 'Browse our curtain fabric catalogues online: Stellar, Matrix, Gems, Linen Life, Splendid, Awesome and more. Open the PDFs and book a free sample visit.' ),
	);
	$defs['projects']       = array(
		'title' => 'Our Projects',
		'build' => 'dce_build_projects',
		'thumb' => array( 'curtain-project-dubai', 1 ),
		'seo'   => array( 'Curtain & Blind Projects in Dubai | Our Recent Work', 'See curtains and blinds we have measured, made and installed in Dubai villas, apartments and offices: sheers, blackout, wave and motorized curtains.' ),
	);
	$defs['about-us']       = array(
		'title' => 'About Us',
		'build' => 'dce_build_about',
		'thumb' => array( 'wave-curtains', 1 ),
		'seo'   => array( 'About Dubai Curtain Experts | Part of Casa Vera Home', 'Dubai Curtain Experts is the curtains and blinds studio of Casa Vera Home (Mukhtar Curtain LLC), with a showroom on Naif Road, Deira, Dubai.' ),
	);
	$defs['contact-us']     = array(
		'title' => 'Contact Us',
		'build' => 'dce_build_contact',
		'thumb' => array( 'eyelet-curtains', 1 ),
		'seo'   => array( 'Contact Dubai Curtain Experts | WhatsApp +971 50 859 9803', 'Book a free home visit for curtains and blinds in Dubai and the UAE. WhatsApp or call +971 50 859 9803, or visit our showroom at Empire Plaza, Deira.' ),
	);
	$defs['areas-we-serve'] = array(
		'title' => 'Areas We Serve',
		'build' => 'dce_build_areas_hub',
		'thumb' => array( 'motorized-curtains', 1 ),
		'seo'   => array( 'Curtains & Blinds Across Dubai & the UAE | Areas We Serve', 'Free home visits for curtains and blinds in Dubai Marina, Downtown, Palm Jumeirah, JVC, Abu Dhabi, Sharjah, Ajman, RAK, Fujairah, Al Ain and more.' ),
	);
	foreach ( dce_areas() as $slug => $a ) {
		$defs[ $slug ] = array(
			'title'  => wp_strip_all_tags( html_entity_decode( dce_area_title( $slug ) ) ),
			'parent' => 'areas-we-serve',
			'build'  => fn() => dce_build_area( $slug ),
			'thumb'  => $a['img'],
			'seo'    => array( $a['seo_title'], $a['seo_desc'] ),
			'svc'    => 'Curtains and blinds in ' . wp_strip_all_tags( html_entity_decode( $a['name'] ) ),
		);
	}
	$defs['blog']           = array(
		'title' => 'Curtain &amp; Blind Guides',
		'build' => 'dce_build_blog',
		'thumb' => array( 'wave-curtains', 4 ),
		'seo'   => array( 'Curtain & Blind Guides for UAE Homes | Blog', 'Guides on choosing, measuring and caring for curtains and blinds in Dubai: blackout, sheer, wave vs pinch pleat, motorized curtains and more.' ),
	);
	$defs['privacy-policy'] = array(
		'title' => 'Privacy Policy',
		'build' => 'dce_build_privacy',
		'seo'   => array( 'Privacy Policy | Dubai Curtain Experts', 'How Dubai Curtain Experts handles the information you share through our website, forms and WhatsApp.' ),
	);
	return $defs;
}
