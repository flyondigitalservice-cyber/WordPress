<?php
/**
 * Dubai Blinds Hub — service pages (blinds, curtains, flooring, upholstery, wallpaper, carpets).
 * URLs match the existing site (all top-level, e.g. /roller-blinds/).
 *
 * Image sources: 'imgs' = bundled theme photos [group, n]; 'media' = file-name prefixes of photos
 * already in this site's Media Library. Pages without a matching photo show a text-only hero
 * until you add one in the editor.
 *
 * @package dce-vibe
 */

defined( 'ABSPATH' ) || exit;

/** Category labels and hub slugs. */
function dbh_categories() {
	return array(
		'blinds'     => array( 'Blinds', 'blinds-dubai' ),
		'curtains'   => array( 'Curtains', 'curtains' ),
		'flooring'   => array( 'Flooring', 'flooring' ),
		'upholstery' => array( 'Upholstery', 'upholstery' ),
		'wallpaper'  => array( 'Wallpaper', 'wallpaper' ),
		'carpets'    => array( 'Carpets', 'carpets' ),
	);
}

/**
 * Compact service definition.
 *
 * @param string $cat  Category key.
 * @param string $name Display name.
 * @param array  $d    Content.
 */
function dbh_s( $cat, $name, $d ) {
	$label = dbh_categories()[ $cat ][0];
	return array_merge(
		array(
			'name'       => $name,
			'cat'        => $cat,
			'parent'     => '',
			'img'        => '',
			'imgs'       => array(),
			'media'      => array(),
			'kicker'     => $label . ' · Dubai &amp; UAE',
			'opt_title'  => 'Options &amp; <em>finishes</em>',
			'catalogue'  => in_array( $cat, array( 'curtains', 'upholstery' ), true ),
			'related'    => array(),
		),
		$d
	);
}

function dbh_services() {
	return array(

		/* ------------------------------------------------ BLINDS ------------------------------------------------ */

		'roller-blinds' => dbh_s( 'blinds', 'Roller Blinds', array(
			'imgs'       => array( array( 'printed-blinds', 2 ), array( 'logo-sunscreen-blinds', 2 ), array( 'printed-blinds', 3 ) ),
			'media'      => array( 'printed-roller-blinds', 'Coolaroo' ),
			'seo_title'  => 'Roller Blinds Dubai | Blackout, Sunscreen & Printed Rollers',
			'seo_desc'   => 'Roller blinds in Dubai made to measure: blackout, sunscreen, dim-out and printed fabrics, chain or motorized. Free site visit and installation across the UAE.',
			'card'       => 'Slim, simple and made to measure — blackout, sunscreen or printed.',
			'h1'         => '<em>Roller</em> blinds in Dubai',
			'lead'       => 'The most versatile blind there is: a single fabric panel that rolls neatly away. Choose blackout for bedrooms, sunscreen for living rooms and offices, or print your own design.',
			'what_title' => 'Why roller blinds <em>work everywhere</em>',
			'paras'      => array(
				'A roller blind is a straight fabric panel on a tube. It takes very little space, fits inside or outside the window recess and suits every style of room, from apartments in JLT to villas in Arabian Ranches.',
				'The fabric decides the job: blackout for sleep, sunscreen mesh for glare control with a view, dim-out for a softer balance, or printed fabric for kids’ rooms, cafés and shopfronts.',
			),
			'checks'     => array( 'Blackout, dim-out, sunscreen and printed fabrics', 'Chain, spring or motorized control', 'Optional cassette to hide the roll', 'Double brackets for day & night layers', 'Made to the millimetre for each window' ),
			'tiles'      => array(
				array( 'Blackout rollers', 'Complete darkness for bedrooms and media rooms.' ),
				array( 'Sunscreen rollers', 'Cut glare and heat while keeping your view.' ),
				array( 'Printed rollers', 'Photos, patterns or logos printed on the fabric.' ),
				array( 'Motorized rollers', 'Remote, app and smart-home control.' ),
			),
			'faqs'       => array(
				array( 'Which roller blind fabric should I choose?', 'Blackout for bedrooms, sunscreen for living rooms and offices with a view, dim-out where you want softer light. We bring samples to compare on site.' ),
				array( 'Can roller blinds be fitted inside the window recess?', 'Yes, if the recess is deep enough. Otherwise we fit them on the wall or ceiling above the window.' ),
				array( 'Can I combine a sunscreen and a blackout roller?', 'Yes — a double bracket holds both, so you get daytime glare control and night-time darkness.' ),
			),
			'related'    => array( 'blackout-blinds', 'motorized-blinds', 'office-blinds', 'venetian-blinds' ),
		) ),

		'blackout-blinds' => dbh_s( 'blinds', 'Blackout Blinds', array(
			'imgs'       => array( array( 'roman-blinds', 3 ) ),
			'media'      => array( 'totalshade', 'blackout' ),
			'seo_title'  => 'Blackout Blinds Dubai | Room-Darkening Blinds for Bedrooms',
			'seo_desc'   => 'Blackout blinds in Dubai for bedrooms, nurseries and media rooms: roller, Roman and duo blackout blinds, side channels and motors. Free site visit.',
			'card'       => 'Full room-darkening for better sleep, naps and screen rooms.',
			'h1'         => '<em>Blackout</em> blinds in Dubai',
			'lead'       => 'Sleep past sunrise and keep afternoon heat out. We measure and fit blackout blinds so the edges are tight and the room goes properly dark.',
			'what_title' => 'Darkness that <em>actually works</em>',
			'paras'      => array(
				'A blackout fabric stops light passing through, but the fitting decides how dark a room gets. We choose between recess and face fitting, add cassettes and, where needed, side channels to close the gaps.',
				'Blackout blinds also reduce solar heat on west-facing glass, which helps bedrooms and home offices stay cool in the UAE summer.',
			),
			'checks'     => array( 'Roller, Roman and duo blackout options', 'Cassettes and side channels for tighter edges', 'Thermal backing to reduce heat', 'Child-safe cordless controls', 'Pairs with sheer curtains' ),
			'tiles'      => array(
				array( 'Blackout roller', 'Clean and minimal; the most popular choice.' ),
				array( 'Blackout Roman', 'Softer fabric folds with a blackout lining.' ),
				array( 'Duo blackout', 'Day & night bands with a blackout layer.' ),
				array( 'Side channels', 'Close light gaps at the window edges.' ),
			),
			'faqs'       => array(
				array( 'Will a blackout blind make my room completely dark?', 'The fabric blocks light; small gaps at the edges are reduced with face fitting, cassettes and side channels. We advise the best option for each window.' ),
				array( 'Are blackout blinds good for nurseries?', 'Yes, and we recommend cordless or tensioned controls for child safety.' ),
				array( 'Do blackout blinds reduce heat?', 'Yes, especially fabrics with a thermal or reflective backing.' ),
			),
			'related'    => array( 'roller-blinds', 'blackout-curtains', 'motorized-blinds', 'roman-blinds' ),
		) ),

		'venetian-blinds' => dbh_s( 'blinds', 'Venetian Blinds', array(
			'imgs'       => array( array( 'wooden-blinds', 2 ), array( 'aluminium-venetian-blinds', 1 ), array( 'wooden-blinds', 1 ), array( 'aluminium-venetian-blinds', 2 ) ),
			'media'      => array( 'Wooden-Blinds' ),
			'seo_title'  => 'Venetian Blinds Dubai | Wooden & Aluminium Slat Blinds',
			'seo_desc'   => 'Venetian blinds in Dubai: 50mm and 25mm wooden, faux-wood and aluminium slats with precise tilt control. Made to measure and installed. Free site visit.',
			'card'       => 'Tilting wooden or aluminium slats for precise light control.',
			'h1'         => '<em>Venetian</em> blinds in Dubai',
			'lead'       => 'Horizontal slats that tilt to angle the sun and lift to open the view. Warm timber for living spaces, tough aluminium for kitchens, bathrooms and offices.',
			'what_title' => 'Slats that give you <em>total control</em>',
			'paras'      => array(
				'Venetian blinds let you direct daylight exactly where you want it — tilt up for privacy with light, tilt down to block glare, or raise the whole blind for a clear window.',
				'Choose 50mm wooden slats for a warm, architectural look, faux-wood for humid rooms, or slim 25mm aluminium for a crisp finish that is easy to wipe clean.',
			),
			'checks'     => array( 'Wood, faux-wood and aluminium slats', '50mm and 25mm slat sizes', 'Cords or decorative ladder tapes', 'Moisture-resistant options', 'Made to measure for every window' ),
			'tiles'      => array(
				array( 'Wooden 50mm', 'Natural timber for living rooms and bedrooms.' ),
				array( 'Faux wood', 'Wood look with moisture resistance.' ),
				array( 'Aluminium 25mm', 'Slim, durable and easy to clean.' ),
				array( 'Ladder tapes', 'Cloth tapes for a tailored finish.' ),
			),
			'faqs'       => array(
				array( 'Wood or aluminium venetians?', 'Wood brings warmth to living spaces; aluminium suits kitchens, bathrooms and offices where moisture and cleaning matter.' ),
				array( 'Are venetian blinds easy to clean?', 'Yes — dust the slats regularly; aluminium can be wiped with a damp cloth.' ),
				array( 'Do you fit venetian blinds in offices?', 'Yes, aluminium venetians are popular for offices and clinics.' ),
			),
			'related'    => array( 'roller-blinds', 'vertical-blinds', 'office-blinds', 'roman-blinds' ),
		) ),

		'vertical-blinds' => dbh_s( 'blinds', 'Vertical Blinds', array(
			'imgs'       => array( array( 'vertical-blinds', 1 ) ),
			'media'      => array( 'Vertical-Blinds' ),
			'seo_title'  => 'Vertical Blinds Dubai | Sliding Door & Office Vertical Blinds',
			'seo_desc'   => 'Vertical blinds in Dubai for sliding doors, wide windows and offices. Fabric, blackout and PVC louvres, made to measure. Free site visit and installation.',
			'card'       => 'Rotating louvres for sliding doors, wide glass and offices.',
			'h1'         => '<em>Vertical</em> blinds in Dubai',
			'lead'       => 'Practical, durable and ideal for wide openings. Vertical louvres rotate to control light and slide aside so you can walk straight through to the balcony or garden.',
			'what_title' => 'Made for <em>wide openings</em>',
			'paras'      => array(
				'Vertical blinds hang as individual louvres from a head rail. Turn them to direct light, or draw them fully to one side — perfect for patio and balcony sliding doors.',
				'They are hard-wearing and simple to maintain, which is why offices, schools and clinics across Dubai choose them for large window runs.',
			),
			'checks'     => array( 'Fabric, blackout and PVC louvres', 'Stack left, right or centre split', 'Individual louvres can be replaced', 'Great for sliding doors and offices', 'Manual or motorized control' ),
			'tiles'      => array(
				array( 'Fabric louvres', 'Soft, light-filtering finish for homes.' ),
				array( 'Blackout louvres', 'For meeting rooms and bedrooms.' ),
				array( 'PVC louvres', 'Wipe-clean for kitchens and clinics.' ),
				array( 'Split stacking', 'Open from the centre on wide doors.' ),
			),
			'faqs'       => array(
				array( 'Are vertical blinds good for sliding doors?', 'Yes — they stack neatly to one side and the louvres rotate for privacy.' ),
				array( 'Can one damaged louvre be replaced?', 'Usually yes, without replacing the whole blind.' ),
				array( 'Do vertical blinds come in blackout?', 'Yes, blackout louvres are available.' ),
			),
			'related'    => array( 'office-blinds', 'venetian-blinds', 'roller-blinds', 'office-curtains' ),
		) ),

		'roman-blinds' => dbh_s( 'blinds', 'Roman Blinds', array(
			'imgs'       => array( array( 'roman-blinds', 1 ), array( 'roman-blinds', 2 ), array( 'roman-blinds', 4 ), array( 'roman-blinds', 5 ) ),
			'catalogue'  => true,
			'seo_title'  => 'Roman Blinds Dubai | Fabric Roman Blinds, Lined or Blackout',
			'seo_desc'   => 'Fabric Roman blinds in Dubai: linen, sheer, woven and printed fabrics, lined or blackout, chain or motorized. Made to measure with free site visit.',
			'card'       => 'Soft fabric folds — the warmth of a curtain in a neat blind.',
			'h1'         => '<em>Roman</em> blinds in Dubai',
			'lead'       => 'Fabric that lifts into soft, even folds. Roman blinds add texture and warmth to bedrooms, kitchens, studies and bay windows without the bulk of full curtains.',
			'what_title' => 'Softness with a <em>tidy footprint</em>',
			'paras'      => array(
				'Each Roman blind is sewn from your chosen fabric with rods at the back, so it folds neatly when raised and lies smooth when lowered — showing off the full texture or pattern.',
				'Add a blackout or thermal lining for bedrooms, or keep it unlined in linen or sheer for gently filtered light in living areas.',
			),
			'checks'     => array( 'Linen, sheer, woven and printed fabrics', 'Standard, thermal or blackout linings', 'Flat or soft cascading folds', 'Chain, cordless or motorized', 'Layer under side curtains' ),
			'tiles'      => array(
				array( 'Linen &amp; sheer', 'Light, airy and relaxed.' ),
				array( 'Woven textures', 'Natural character for warm interiors.' ),
				array( 'Prints', 'Florals, geometrics and statement fabrics.' ),
				array( 'Blackout lined', 'Darkness with softness for bedrooms.' ),
			),
			'faqs'       => array(
				array( 'Can Roman blinds be blackout?', 'Yes, with a blackout lining sewn into the blind.' ),
				array( 'Are Roman blinds good for kitchens?', 'Yes, using easy-care fabrics away from direct heat.' ),
				array( 'Can I choose any fabric?', 'Most curtain fabrics work. Browse our fabric catalogues and we bring samples to your visit.' ),
			),
			'related'    => array( 'roller-blinds', 'blackout-blinds', 'sheer-curtains', 'venetian-blinds' ),
		) ),

		'motorized-blinds' => dbh_s( 'blinds', 'Motorized Blinds', array(
			'imgs'       => array( array( 'zebra-blinds', 1 ) ),
			'media'      => array( 'duplex-blinds', 'Zebra-Blinds' ),
			'seo_title'  => 'Motorized Blinds Dubai | Electric & Smart Home Blinds',
			'seo_desc'   => 'Motorized blinds in Dubai: roller, zebra and Roman blinds with remote, app, voice and smart-home control. Wired or rechargeable motors. Free site visit.',
			'card'       => 'Remote, app and voice control for every blind.',
			'h1'         => '<em>Motorized</em> blinds in Dubai',
			'lead'       => 'Raise and lower every blind in the house from your phone, a remote or a schedule. Ideal for high windows, wide glass and smart homes.',
			'what_title' => 'Smart shading, <em>zero effort</em>',
			'paras'      => array(
				'A quiet motor inside the blind tube replaces the chain. Group blinds by room, set them to close at sunset or open with your alarm, and reach windows that are hard to get to.',
				'We offer wired motors for new fit-outs and rechargeable battery motors for existing homes, compatible with popular apps and smart-home platforms.',
			),
			'checks'     => array( 'Roller, zebra and Roman blinds', 'Remote, wall switch, app and voice', 'Wired or rechargeable motors', 'Schedules and scenes', 'No cords — safer for children' ),
			'tiles'      => array(
				array( 'Rechargeable', 'No wiring needed — great for existing homes.' ),
				array( 'Wired', 'Best planned during fit-out or renovation.' ),
				array( 'Smart home', 'Works with popular apps and voice assistants.' ),
				array( 'Groups &amp; scenes', 'Control a whole room or floor at once.' ),
			),
			'faqs'       => array(
				array( 'Do motorized blinds need wiring?', 'Not necessarily — rechargeable motors run for months between charges.' ),
				array( 'Can I add a motor to my existing blinds?', 'Sometimes; it depends on the blind system. We check during the visit.' ),
				array( 'Are motorized blinds child safe?', 'Yes — there are no hanging cords or chains.' ),
			),
			'related'    => array( 'motorized-curtains', 'roller-blinds', 'blackout-blinds', 'office-blinds' ),
		) ),

		'office-blinds' => dbh_s( 'blinds', 'Office Blinds', array(
			'imgs'       => array( array( 'aluminium-venetian-blinds', 4 ), array( 'logo-sunscreen-blinds', 3 ), array( 'vertical-blinds', 1 ) ),
			'seo_title'  => 'Office Blinds Dubai | Glare Control for Workplaces',
			'seo_desc'   => 'Office blinds in Dubai: sunscreen rollers, vertical, venetian and branded blinds that cut screen glare and heat. Fitted around working hours. Free site survey.',
			'card'       => 'Glare-free workspaces with sunscreen, vertical and branded blinds.',
			'h1'         => '<em>Office</em> blinds in Dubai',
			'lead'       => 'Comfortable, glare-free workspaces. We supply and install blinds for offices, meeting rooms, receptions and co-working spaces across Dubai and the UAE.',
			'what_title' => 'Better light for <em>better work</em>',
			'paras'      => array(
				'Screens and strong sun do not mix. Sunscreen roller blinds reduce glare and heat while keeping natural light and the view; blackout blinds make meeting rooms ready for presentations.',
				'We survey the floor, schedule installation outside working hours where needed, and can print your logo on reception and shopfront blinds.',
			),
			'checks'     => array( 'Sunscreen, blackout, vertical and venetian', 'Logo-printed blinds for receptions', 'Motorized control for large facades', 'Installation outside working hours', 'Multi-floor and multi-site projects' ),
			'tiles'      => array(
				array( 'Open-plan areas', 'Sunscreen rollers to cut glare on screens.' ),
				array( 'Meeting rooms', 'Blackout blinds for presentations.' ),
				array( 'Receptions', 'Branded blinds that reinforce your identity.' ),
				array( 'Facades', 'Motorized groups for large glazing.' ),
			),
			'faqs'       => array(
				array( 'Can you install outside office hours?', 'Yes, we can schedule installation in the evening or on quieter days.' ),
				array( 'Which blind is best for screen glare?', 'Sunscreen roller blinds with low openness reduce glare while keeping light.' ),
				array( 'Do you print logos on blinds?', 'Yes — share your logo file and we prepare a proof.' ),
			),
			'related'    => array( 'vertical-blinds', 'roller-blinds', 'office-curtains', 'office-carpet-installation' ),
		) ),

		/* ------------------------------------------------ CURTAINS ------------------------------------------------ */

		'blackout-curtains' => dbh_s( 'curtains', 'Blackout Curtains', array(
			'imgs'       => array( array( 'blackout-curtains', 1 ), array( 'blackout-curtains', 2 ), array( 'blackout-curtains', 3 ), array( 'blackout-curtains', 4 ) ),
			'seo_title'  => 'Blackout Curtains Dubai | Custom Room-Darkening Curtains',
			'seo_desc'   => 'Custom blackout curtains in Dubai for bedrooms, nurseries and hotel rooms. Wave or pleated, lined or full blackout fabric, ceiling tracks. Free site visit.',
			'card'       => 'Tailored blackout curtains for deep sleep and cooler rooms.',
			'h1'         => '<em>Blackout</em> curtains in Dubai',
			'lead'       => 'Block out the early sun, street lights and afternoon heat. Our blackout curtains are tailored to each window and hung so light does not sneak around the edges.',
			'what_title' => 'Rest well, <em>stay cool</em>',
			'paras'      => array(
				'We use fabrics that are blackout on their own or add a blackout lining to almost any decorative fabric, so you are free to choose the look you want.',
				'Hanging the curtain from a ceiling track and letting it overlap the window on both sides is what makes the room truly dark — we plan this during the site visit.',
			),
			'checks'     => array( 'Full blackout fabrics or blackout linings', 'Ceiling tracks to stop light at the top', 'Generous side overlaps', 'Wave, pleated or eyelet headings', 'Popular for hotels and serviced apartments' ),
			'tiles'      => array(
				array( 'Bedrooms', 'Deeper sleep and later mornings.' ),
				array( 'Nurseries', 'Day-time naps without the glare.' ),
				array( 'Hotels', 'Durable blackout for guest rooms.' ),
				array( 'Home cinemas', 'Darkness for a better picture.' ),
			),
			'faqs'       => array(
				array( 'Can my favourite fabric be made blackout?', 'Usually yes — we add a blackout lining behind it.' ),
				array( 'Do blackout curtains reduce AC costs?', 'They reduce solar heat through the glass, which helps rooms stay cooler.' ),
				array( 'Curtains or blackout blinds?', 'Curtains add softness and insulation; blinds are slimmer. Many bedrooms use both.' ),
			),
			'related'    => array( 'sheer-curtains', 'blackout-blinds', 'motorized-curtains', 'curtain-rods-tracks' ),
		) ),

		'sheer-curtains' => dbh_s( 'curtains', 'Sheer Curtains', array(
			'imgs'       => array( array( 'sheer-curtains', 1 ), array( 'sheer-curtains', 2 ), array( 'sheer-curtains', 3 ), array( 'sheer-curtains', 4 ), array( 'sheer-curtains', 5 ) ),
			'seo_title'  => 'Sheer Curtains Dubai | Voile & Linen Sheers Made to Measure',
			'seo_desc'   => 'Sheer curtains in Dubai: voile, linen-look and chiffon sheers that soften sunlight and add daytime privacy. Wave or pleated, made to measure. Free site visit.',
			'card'       => 'Soft voile and linen sheers that tame the sun and keep the view.',
			'h1'         => '<em>Sheer</em> curtains in Dubai',
			'lead'       => 'Light, airy fabrics that turn harsh sunlight into a soft glow — giving daytime privacy without blocking your view of the city, sea or garden.',
			'what_title' => 'The everyday layer <em>for every window</em>',
			'paras'      => array(
				'Sheers are the curtains you live with all day. They diffuse glare, protect floors and furniture from fading and make large glass feel calm and finished.',
				'Pair a sheer with a blackout curtain on a double track, or with a blackout roller blind, for complete day-and-night control.',
			),
			'checks'     => array( 'Voile, linen-look and chiffon sheers', 'Wave or pleated headings', 'Weighted hems for a clean fall', 'Double tracks with blackout layers', 'Motor-ready tracks available' ),
			'tiles'      => array(
				array( 'Voile', 'Smooth, fine and very light.' ),
				array( 'Linen-look', 'Natural texture with more privacy.' ),
				array( 'Chiffon', 'Fluid drape with a soft sheen.' ),
				array( 'Patterned', 'Stripes, embroidery and burn-out designs.' ),
			),
			'faqs'       => array(
				array( 'Do sheers give privacy at night?', 'Not on their own — add a lined or blackout layer for evenings.' ),
				array( 'Which sheer handles strong sun best?', 'Denser linen-look sheers filter the most glare.' ),
				array( 'Can sheers be motorized?', 'Yes, on a motorized track — often paired with a blackout layer.' ),
			),
			'related'    => array( 'blackout-curtains', 'motorized-curtains', 'villa-curtain-installation', 'curtain-rods-tracks' ),
		) ),

		'motorized-curtains' => dbh_s( 'curtains', 'Motorized Curtains', array(
			'imgs'       => array( array( 'motorized-curtains', 1 ), array( 'motorized-curtains', 2 ), array( 'motorized-curtains', 3 ), array( 'motorized-curtains', 4 ) ),
			'seo_title'  => 'Motorized Curtains Dubai | Electric Curtain Tracks & Motors',
			'seo_desc'   => 'Motorized curtains in Dubai: electric tracks with remote, app and voice control for sheers and blackouts. Wired or wire-free motors. Free site visit.',
			'card'       => 'Electric curtain tracks with remote, app and voice control.',
			'h1'         => '<em>Motorized</em> curtains in Dubai',
			'lead'       => 'Open the sheers with your morning alarm and close the blackouts at sunset — automatically. Electric curtain tracks make large windows effortless.',
			'what_title' => 'Curtains that <em>move for you</em>',
			'paras'      => array(
				'A motor at the end of the track draws the curtain smoothly and quietly. Control sheer and blackout layers separately, group whole rooms, or link everything to your smart-home system.',
				'Motorized tracks are ideal for double-height villa windows, wide balcony glass and hotel suites — and they protect fabrics from being pulled by hand.',
			),
			'checks'     => array( 'Remote, switch, app and voice control', 'Independent sheer and blackout layers', 'Wired or wire-free motors', 'Schedules and smart scenes', 'Quiet tracks for bedrooms' ),
			'tiles'      => array(
				array( 'Villas', 'Tall windows and long runs made easy.' ),
				array( 'Apartments', 'Wire-free motors with no rewiring.' ),
				array( 'Hotels', 'Bedside control for guests.' ),
				array( 'Offices', 'Boardrooms and reception areas.' ),
			),
			'faqs'       => array(
				array( 'Do I need a power socket near the window?', 'Wired motors do; wire-free (battery) motors do not.' ),
				array( 'Can I open motorized curtains by hand?', 'Most systems allow gentle manual use or have an override.' ),
				array( 'Which smart homes are supported?', 'Many motors work with popular apps and assistants — we confirm on site.' ),
			),
			'related'    => array( 'motorized-blinds', 'sheer-curtains', 'blackout-curtains', 'villa-curtain-installation' ),
		) ),

		'office-curtains' => dbh_s( 'curtains', 'Office Curtains', array(
			'imgs'       => array( array( 'blackout-curtains', 1 ), array( 'sheer-curtains', 5 ), array( 'motorized-curtains', 3 ) ),
			'seo_title'  => 'Office Curtains Dubai | Boardroom & Reception Curtains',
			'seo_desc'   => 'Office curtains in Dubai for boardrooms, receptions and executive offices: blackout, sheer and acoustic-friendly fabrics, motorized tracks. Free site survey.',
			'card'       => 'Boardroom, reception and executive office curtains.',
			'h1'         => '<em>Office</em> curtains in Dubai',
			'lead'       => 'Professional curtains for boardrooms, receptions and executive offices — blackout for presentations, sheers for daylight and a polished first impression.',
			'what_title' => 'A calmer, more <em>polished workplace</em>',
			'paras'      => array(
				'Curtains soften hard office interiors, reduce glare on screens and help absorb sound in meeting rooms. Heavier fabrics make video calls and presentations more comfortable.',
				'We work with fit-out contractors and facility managers, and install outside office hours when needed.',
			),
			'checks'     => array( 'Blackout for boardrooms and training rooms', 'Sheers for open-plan daylight', 'Heavier fabrics to soften acoustics', 'Motorized tracks for wide glazing', 'Evening and weekend installation' ),
			'tiles'      => array(
				array( 'Boardrooms', 'Blackout and motorized control.' ),
				array( 'Receptions', 'Elegant sheers and layered drapes.' ),
				array( 'Executive offices', 'Tailored curtains with blinds behind.' ),
				array( 'Clinics', 'Easy-care fabrics and privacy.' ),
			),
			'faqs'       => array(
				array( 'Do you work with fit-out contractors?', 'Yes, we coordinate with contractors and facility managers.' ),
				array( 'Can curtains help with echo?', 'Heavier fabrics help absorb sound and soften meeting rooms.' ),
				array( 'Do you handle multiple floors?', 'Yes, we survey and schedule larger projects in phases.' ),
			),
			'related'    => array( 'office-blinds', 'motorized-curtains', 'office-carpet-installation', 'office-wallpaper' ),
		) ),

		'villa-curtain-installation' => dbh_s( 'curtains', 'Villa Curtain Installation', array(
			'imgs'       => array( array( 'american-style-curtains', 1 ), array( 'curtain-project-dubai', 1 ), array( 'american-style-curtains', 2 ), array( 'curtain-project-dubai', 2 ), array( 'curtain-project-dubai', 5 ) ),
			'seo_title'  => 'Villa Curtain Installation Dubai | Complete Villa Curtains',
			'seo_desc'   => 'Complete villa curtain installation in Dubai and the UAE: every room measured, tailored curtains and blinds, tracks and motors fitted in one project. Free visit.',
			'card'       => 'Every room of your villa — measured, made and fitted in one project.',
			'h1'         => '<em>Villa curtain</em> installation in Dubai',
			'lead'       => 'From majlis and double-height living rooms to bedrooms and maids’ rooms — we measure every window in your villa and deliver a complete, coordinated installation.',
			'what_title' => 'One team, <em>every window</em>',
			'paras'      => array(
				'Villas often have dozens of windows in different sizes and heights. We measure them all in one visit, help you choose a consistent palette and give a single itemised quotation.',
				'Our team installs tracks, rods, motors, curtains and blinds room by room, and leaves the villa clean and ready to live in.',
			),
			'checks'     => array( 'Whole-villa measurement in one visit', 'Majlis, living, bedrooms and service rooms', 'Double-height and motorized windows', 'Coordinated fabrics room by room', 'Scheduled installation with one team' ),
			'tiles'      => array(
				array( 'New handovers', 'Complete fit-out for empty villas.' ),
				array( 'Renovations', 'Replace tired curtains room by room.' ),
				array( 'Majlis', 'Formal drapes, sheers and valances.' ),
				array( 'Motorization', 'Tall windows on electric tracks.' ),
			),
			'faqs'       => array(
				array( 'How long does a full villa take?', 'It depends on the number of windows and fabrics; we give a clear schedule in the quotation.' ),
				array( 'Can you remove old curtains and tracks?', 'Yes, removal can be included in the job.' ),
				array( 'Do you work in Arabian Ranches, Dubai Hills and the Palm?', 'Yes — and in villa communities across all emirates.' ),
			),
			'related'    => array( 'motorized-curtains', 'blackout-curtains', 'curtain-rods-tracks', 'sofa-upholstery' ),
		) ),

		'curtain-rods-tracks' => dbh_s( 'curtains', 'Curtain Rods &amp; Tracks', array(
			'imgs'       => array( array( 'pinch-pleat-curtains', 5 ), array( 'wave-curtains', 3 ), array( 'eyelet-curtains', 5 ) ),
			'seo_title'  => 'Curtain Rods & Tracks Dubai | Supply & Installation',
			'seo_desc'   => 'Curtain rods and tracks in Dubai: ceiling and wall tracks, wave tracks, decorative rods, bay tracks and motorized systems — supplied and installed. Free site visit.',
			'card'       => 'Ceiling tracks, wave tracks, decorative rods and motors — fitted.',
			'h1'         => 'Curtain <em>rods &amp; tracks</em> in Dubai',
			'lead'       => 'The right hardware makes curtains glide, hang straight and last. We supply and install ceiling tracks, wave tracks, bay tracks, decorative rods and motorized systems.',
			'what_title' => 'Hardware that <em>holds it all together</em>',
			'paras'      => array(
				'Tracks give a clean, modern line and are essential for wave curtains and motorization. Decorative rods with finials make the hardware part of the design.',
				'We check ceilings, gypsum, concrete and recesses on site and use the right fixings so tracks stay straight even with heavy blackout curtains.',
			),
			'checks'     => array( 'Ceiling, wall and recessed tracks', 'Wave and ripple-fold tracks', 'Bay window and curved tracks', 'Decorative metal and wood rods', 'Motorized track systems' ),
			'tiles'      => array(
				array( 'Ceiling tracks', 'Clean lines and better blackout.' ),
				array( 'Wave tracks', 'Even S-fold curtains.' ),
				array( 'Decorative rods', 'Finials in black, brass, chrome or wood.' ),
				array( 'Repairs &amp; replacement', 'Swap broken or sagging tracks.' ),
			),
			'faqs'       => array(
				array( 'Can you replace a sagging curtain track?', 'Yes — we remove the old track and fit a new one with suitable fixings.' ),
				array( 'Rod or track?', 'Tracks are best for wave curtains, motors and clean ceilings; rods suit eyelet and decorative styles.' ),
				array( 'Can you fix tracks into gypsum ceilings?', 'Yes, using the right fixings or a timber batten where needed.' ),
			),
			'related'    => array( 'motorized-curtains', 'curtain-alteration-stitching', 'villa-curtain-installation', 'sheer-curtains' ),
		) ),

		'curtain-alteration-stitching' => dbh_s( 'curtains', 'Curtain Alteration &amp; Stitching', array(
			'imgs'       => array( array( 'pinch-pleat-curtains', 5 ), array( 'printed-blinds', 4 ) ),
			'seo_title'  => 'Curtain Alteration & Stitching Dubai | Resize, Reline, Restyle',
			'seo_desc'   => 'Curtain alteration and stitching in Dubai: shorten, lengthen, widen, reline with blackout or change the heading of your existing curtains. Pickup and refit.',
			'card'       => 'Shorten, reline or restyle the curtains you already have.',
			'h1'         => 'Curtain <em>alteration &amp; stitching</em> in Dubai',
			'lead'       => 'Moved home or changed your windows? We alter your existing curtains — shorten, lengthen, widen, add blackout lining or change the heading — and refit them.',
			'what_title' => 'Give your curtains <em>a second life</em>',
			'paras'      => array(
				'Good fabric deserves to be reused. Our stitching team can resize curtains for new windows, add linings for privacy and blackout, or convert eyelet curtains into pinch pleat or wave headings.',
				'We can collect the curtains, alter them in our workshop and come back to rehang them on new or existing tracks.',
			),
			'checks'     => array( 'Shorten, lengthen or widen', 'Add blackout or thermal lining', 'Change headings (eyelet, pleat, wave)', 'Repair hems, seams and hooks', 'Collection and rehanging' ),
			'tiles'      => array(
				array( 'Resizing', 'Fit curtains to new windows.' ),
				array( 'Relining', 'Add blackout, thermal or standard linings.' ),
				array( 'New headings', 'Modernise to wave or pleats.' ),
				array( 'Repairs', 'Fix hems, seams and torn hooks.' ),
			),
			'faqs'       => array(
				array( 'Can you add blackout lining to my curtains?', 'Yes, in most cases we can sew a blackout lining behind your existing fabric.' ),
				array( 'Do you collect the curtains?', 'Yes, we can collect, alter and rehang them.' ),
				array( 'Can eyelet curtains become wave curtains?', 'Often yes — we check the fabric and width first.' ),
			),
			'related'    => array( 'curtain-rods-tracks', 'blackout-curtains', 'sheer-curtains', 'cushion-pillow-upholstery' ),
		) ),

		/* ------------------------------------------------ FLOORING ------------------------------------------------ */

		'wooden-flooring' => dbh_s( 'flooring', 'Wooden Flooring', array(
			'media'      => array( 'wooden-floor', 'wood-floor', 'parquet' ),
			'seo_title'  => 'Wooden Flooring Dubai | Engineered & Solid Wood Floors',
			'seo_desc'   => 'Wooden flooring in Dubai: engineered and solid wood, herringbone and chevron patterns, supplied and installed with skirting. Free site visit and quotation.',
			'card'       => 'Engineered and solid wood floors, plank or herringbone.',
			'h1'         => '<em>Wooden</em> flooring in Dubai',
			'lead'       => 'The warmth and character of real timber underfoot. We supply and install engineered and solid wood floors in planks, herringbone and chevron for homes and offices.',
			'what_title' => 'Natural warmth, <em>built to last</em>',
			'paras'      => array(
				'Engineered wood has a real timber top layer over a stable core, which makes it well suited to air-conditioned UAE interiors. Solid wood can be sanded and refinished over many years.',
				'We check the subfloor, recommend the right underlay and installation method, and finish with matching skirting and thresholds.',
			),
			'checks'     => array( 'Engineered and solid wood', 'Plank, herringbone and chevron', 'Oiled, lacquered and brushed finishes', 'Underlay and skirting included', 'Subfloor check before installation' ),
			'tiles'      => array(
				array( 'Engineered oak', 'Stable and popular for apartments and villas.' ),
				array( 'Herringbone', 'Classic pattern for living rooms.' ),
				array( 'Wide planks', 'A calm, contemporary look.' ),
				array( 'Finishing', 'Skirting, thresholds and stair nosing.' ),
			),
			'faqs'       => array(
				array( 'Is wooden flooring suitable for Dubai?', 'Yes — engineered wood is stable in air-conditioned interiors and is the most popular choice.' ),
				array( 'Can wood flooring go over tiles?', 'Often yes, after checking the levels and condition of the existing floor.' ),
				array( 'How do I care for a wood floor?', 'Sweep regularly, use a slightly damp mop and felt pads under furniture.' ),
			),
			'related'    => array( 'laminate-flooring', 'vinyl-flooring', 'wall-to-wall-carpet', 'custom-carpet-runners' ),
		) ),

		'vinyl-flooring' => dbh_s( 'flooring', 'Vinyl Flooring', array(
			'media'      => array( 'vinyl', 'spc' ),
			'seo_title'  => 'Vinyl Flooring Dubai | SPC & LVT Waterproof Floors',
			'seo_desc'   => 'Vinyl flooring in Dubai: waterproof SPC and LVT planks and tiles in wood and stone looks for homes, offices and shops. Supply and installation. Free site visit.',
			'card'       => 'Waterproof SPC and LVT in realistic wood and stone looks.',
			'h1'         => '<em>Vinyl</em> flooring in Dubai',
			'lead'       => 'Waterproof, quiet and tough. Modern SPC and LVT vinyl floors look like wood or stone and suit kitchens, rentals, offices and busy family homes.',
			'what_title' => 'Practical floors that <em>look natural</em>',
			'paras'      => array(
				'Vinyl planks and tiles are water resistant, warm underfoot and easy to clean, which makes them ideal for kitchens, kids’ rooms, rental units and retail spaces.',
				'Click-fit SPC can often be installed over existing tiles, keeping renovation fast and clean.',
			),
			'checks'     => array( 'SPC and LVT options', 'Wood and stone designs', 'Water resistant and easy to clean', 'Often installs over existing tiles', 'Suitable for homes and commercial spaces' ),
			'tiles'      => array(
				array( 'SPC click', 'Rigid core, fast installation.' ),
				array( 'LVT glue-down', 'Durable for commercial traffic.' ),
				array( 'Wood looks', 'Oak, walnut and greige tones.' ),
				array( 'Stone looks', 'Marble and concrete effects.' ),
			),
			'faqs'       => array(
				array( 'Is vinyl flooring waterproof?', 'SPC and LVT are highly water resistant; seams and edges are finished to protect the subfloor.' ),
				array( 'Can vinyl go over existing tiles?', 'Often yes, if the tiles are level and sound.' ),
				array( 'Is vinyl good for rentals?', 'Yes — it is durable, affordable and quick to install.' ),
			),
			'related'    => array( 'laminate-flooring', 'wooden-flooring', 'carpet-tiles', 'epoxy-flooring' ),
		) ),

		'laminate-flooring' => dbh_s( 'flooring', 'Laminate Flooring', array(
			'media'      => array( 'laminate' ),
			'seo_title'  => 'Laminate Flooring Dubai | Affordable Wood-Look Floors',
			'seo_desc'   => 'Laminate flooring in Dubai: affordable, scratch-resistant wood-look floors in many shades. Supplied and installed with underlay and skirting. Free site visit.',
			'card'       => 'Affordable, scratch-resistant wood-look flooring.',
			'h1'         => '<em>Laminate</em> flooring in Dubai',
			'lead'       => 'The look of wood at a friendly price. Laminate is scratch resistant, quick to install and available in many colours and textures.',
			'what_title' => 'Smart value, <em>great looks</em>',
			'paras'      => array(
				'Laminate uses a printed wood design under a hard-wearing surface layer. It resists scratches from daily life and is a popular upgrade for bedrooms, living rooms and offices.',
				'We install it with a suitable underlay for comfort and sound, and finish edges with matching skirting.',
			),
			'checks'     => array( 'Many wood shades and textures', 'Scratch-resistant surface', 'Quiet underlay included', 'Skirting and thresholds', 'Fast installation' ),
			'tiles'      => array(
				array( 'Bedrooms', 'Warm and quiet with good underlay.' ),
				array( 'Living rooms', 'Wide planks for a spacious feel.' ),
				array( 'Offices', 'Durable and easy to maintain.' ),
				array( 'Rentals', 'Great value refresh between tenants.' ),
			),
			'faqs'       => array(
				array( 'Laminate or vinyl?', 'Laminate is great value for dry rooms; vinyl is better where water is a concern.' ),
				array( 'Is laminate noisy?', 'A good underlay keeps it quiet and comfortable.' ),
				array( 'How long does installation take?', 'An average apartment room can often be done in a day.' ),
			),
			'related'    => array( 'vinyl-flooring', 'wooden-flooring', 'wall-to-wall-carpet', 'textured-wallpaper' ),
		) ),

		'gym-flooring' => dbh_s( 'flooring', 'Gym Flooring', array(
			'media'      => array( 'gym' ),
			'seo_title'  => 'Gym Flooring Dubai | Rubber Tiles & Home Gym Floors',
			'seo_desc'   => 'Gym flooring in Dubai for home gyms, fitness studios and hotels: impact-absorbing rubber tiles, rolls and turf. Supply and installation. Free site visit.',
			'card'       => 'Impact-absorbing rubber tiles and rolls for home and commercial gyms.',
			'h1'         => '<em>Gym</em> flooring in Dubai',
			'lead'       => 'Protect your floor, your equipment and your joints. We install rubber gym tiles, rolls and turf for home gyms, studios, hotels and residential towers.',
			'what_title' => 'Built for <em>heavy training</em>',
			'paras'      => array(
				'Gym flooring absorbs impact from dropped weights, reduces noise to neighbours and gives a stable, non-slip surface for training.',
				'We recommend thickness by activity — thinner for cardio, thicker for free-weight zones — and can combine rubber with sled-track turf.',
			),
			'checks'     => array( 'Rubber tiles and rolls', 'Thickness matched to training', 'Noise and vibration reduction', 'Sled-track turf zones', 'Home and commercial gyms' ),
			'tiles'      => array(
				array( 'Home gyms', 'Protect villa and apartment floors.' ),
				array( 'Free-weight zones', 'Thicker tiles for drops.' ),
				array( 'Studios', 'Functional training layouts.' ),
				array( 'Turf tracks', 'Sled and agility lanes.' ),
			),
			'faqs'       => array(
				array( 'What thickness do I need?', 'Cardio areas need less; free-weight zones need thicker tiles. We advise based on your equipment.' ),
				array( 'Can gym flooring go in an apartment?', 'Yes — rubber flooring also helps reduce noise to neighbours.' ),
				array( 'How do I clean rubber flooring?', 'Vacuum and mop with a mild, rubber-safe cleaner.' ),
			),
			'related'    => array( 'rubber-flooring', 'sports-flooring', 'epoxy-flooring', 'vinyl-flooring' ),
		) ),

		'epoxy-flooring' => dbh_s( 'flooring', 'Epoxy Flooring', array(
			'media'      => array( 'epoxy' ),
			'seo_title'  => 'Epoxy Flooring Dubai | Garage, Warehouse & Showroom Floors',
			'seo_desc'   => 'Epoxy flooring in Dubai for garages, warehouses, showrooms and kitchens: seamless, chemical-resistant coatings in solid, flake and metallic finishes. Free survey.',
			'card'       => 'Seamless, tough coatings for garages, warehouses and showrooms.',
			'h1'         => '<em>Epoxy</em> flooring in Dubai',
			'lead'       => 'A seamless, hard-wearing floor that resists stains, chemicals and heavy traffic. Ideal for villa garages, warehouses, workshops, showrooms and commercial kitchens.',
			'what_title' => 'Seamless strength, <em>easy cleaning</em>',
			'paras'      => array(
				'Epoxy is applied as a liquid coating over prepared concrete and cures into a tough, seamless surface with no joints to trap dirt.',
				'Surface preparation is the key to a lasting result — we assess the concrete, repair cracks and prepare the surface before coating.',
			),
			'checks'     => array( 'Solid, flake and metallic finishes', 'Chemical and stain resistant', 'Seamless and easy to clean', 'Anti-slip options', 'Concrete preparation and repair' ),
			'tiles'      => array(
				array( 'Garages', 'Clean, bright villa garages.' ),
				array( 'Warehouses', 'Durable for forklifts and traffic.' ),
				array( 'Showrooms', 'Glossy, metallic statement floors.' ),
				array( 'Kitchens', 'Hygienic, seamless surfaces.' ),
			),
			'faqs'       => array(
				array( 'How long does epoxy take to cure?', 'It depends on the system and conditions; we give a clear timeline before starting.' ),
				array( 'Is epoxy slippery?', 'Anti-slip additives can be added for wet or sloped areas.' ),
				array( 'Can epoxy go over tiles?', 'Epoxy works best on prepared concrete; we assess other surfaces on site.' ),
			),
			'related'    => array( 'gym-flooring', 'sports-flooring', 'rubber-flooring', 'vinyl-flooring' ),
		) ),

		'sports-flooring' => dbh_s( 'flooring', 'Sports Flooring', array(
			'media'      => array( 'sports' ),
			'seo_title'  => 'Sports Flooring Dubai | Courts, Schools & Multi-Sport Floors',
			'seo_desc'   => 'Sports flooring in Dubai for schools, clubs and residential courts: PVC sports floors, modular tiles and rubber surfaces with line marking. Free site survey.',
			'card'       => 'Indoor and outdoor sports surfaces with line marking.',
			'h1'         => '<em>Sports</em> flooring in Dubai',
			'lead'       => 'Safe, high-performance surfaces for schools, sports clubs, academies and residential courts — indoor and outdoor, with professional line marking.',
			'what_title' => 'Performance and <em>player safety</em>',
			'paras'      => array(
				'The right sports floor gives consistent ball bounce, good grip and shock absorption to protect players. Choice depends on the sport, indoor or outdoor use and budget.',
				'We survey the base, recommend a system and handle installation and line marking for basketball, badminton, futsal, tennis and multi-sport courts.',
			),
			'checks'     => array( 'PVC, modular and rubber systems', 'Indoor and outdoor courts', 'Shock absorption and grip', 'Line marking for any sport', 'Schools, clubs and communities' ),
			'tiles'      => array(
				array( 'Multi-sport courts', 'One surface, several games.' ),
				array( 'School halls', 'Durable floors for daily PE.' ),
				array( 'Outdoor courts', 'UV-stable modular tiles.' ),
				array( 'Line marking', 'Accurate court markings.' ),
			),
			'faqs'       => array(
				array( 'Which floor is best for a multi-sport hall?', 'PVC sports flooring is a popular all-round choice; we advise based on the sports played.' ),
				array( 'Do you do outdoor courts?', 'Yes, with UV-stable modular tiles and other outdoor systems.' ),
				array( 'Do you mark lines?', 'Yes, for basketball, badminton, futsal, tennis and more.' ),
			),
			'related'    => array( 'gym-flooring', 'rubber-flooring', 'epoxy-flooring', 'exhibition-carpet' ),
		) ),

		'rubber-flooring' => dbh_s( 'flooring', 'Rubber Flooring', array(
			'media'      => array( 'rubber' ),
			'seo_title'  => 'Rubber Flooring Dubai | Play Areas, Gyms & Commercial Floors',
			'seo_desc'   => 'Rubber flooring in Dubai for playgrounds, nurseries, gyms and commercial spaces: tiles, rolls and poured surfaces that are safe, durable and slip resistant.',
			'card'       => 'Safe, slip-resistant rubber for play areas, gyms and walkways.',
			'h1'         => '<em>Rubber</em> flooring in Dubai',
			'lead'       => 'Soft, safe and slip resistant. Rubber flooring protects children in play areas, cushions gym training and stands up to heavy foot traffic outdoors.',
			'what_title' => 'Safety you can <em>stand on</em>',
			'paras'      => array(
				'Rubber surfaces absorb impact and stay grippy when wet, which makes them a natural choice for playgrounds, nurseries, pool surrounds and gyms.',
				'Choose interlocking tiles, rolls or seamless poured rubber in a range of colours and thicknesses.',
			),
			'checks'     => array( 'Tiles, rolls and poured rubber', 'Impact absorption for play areas', 'Slip resistant when wet', 'Indoor and outdoor use', 'Many colours and thicknesses' ),
			'tiles'      => array(
				array( 'Playgrounds', 'Soft-landing surfaces.' ),
				array( 'Nurseries', 'Safe, comfortable floors.' ),
				array( 'Pool areas', 'Grip when wet.' ),
				array( 'Gyms', 'Durable training floors.' ),
			),
			'faqs'       => array(
				array( 'Is rubber flooring safe for children?', 'Yes — it is widely used in play areas to cushion falls.' ),
				array( 'Can rubber flooring be used outside?', 'Yes, outdoor-grade tiles and poured rubber handle sun and weather.' ),
				array( 'Tiles or poured rubber?', 'Tiles are modular and easy to replace; poured rubber is seamless.' ),
			),
			'related'    => array( 'gym-flooring', 'sports-flooring', 'epoxy-flooring', 'kids-room-wallpaper' ),
		) ),

		/* ------------------------------------------------ UPHOLSTERY ------------------------------------------------ */

		'sofa-upholstery' => dbh_s( 'upholstery', 'Sofa Upholstery', array(
			'media'      => array( 'cv-sofa-upholstery', 'sofa-upholstery', 'leather-sofa-upholstery', 'reupholstery', 'sofa-repair' ),
			'seo_title'  => 'Sofa Upholstery Dubai | Reupholstery, Repair & New Fabric',
			'seo_desc'   => 'Sofa upholstery in Dubai: reupholster, repair and refresh sofas and majlis seating with new fabric, leather or foam. Collection and delivery. Free visit.',
			'card'       => 'Reupholster, repair and refresh sofas and majlis seating.',
			'h1'         => '<em>Sofa</em> upholstery in Dubai',
			'lead'       => 'Love the shape but not the fabric? We reupholster sofas, sectionals and majlis seating with new fabric or leather, fresh foam and repaired frames.',
			'what_title' => 'Like new, <em>without replacing</em>',
			'paras'      => array(
				'Reupholstery gives a quality sofa a second life. We strip the old cover, check the frame and springs, replace tired foam and tailor a new cover in your chosen fabric or leather.',
				'Choose from our fabric catalogues — performance fabrics for families and pets, velvets and linens for living rooms, leather and leatherette for a classic finish.',
			),
			'checks'     => array( 'Fabric, leather and leatherette', 'Foam and cushion replacement', 'Frame and spring repair', 'Majlis and sectional sofas', 'Collection and delivery' ),
			'tiles'      => array(
				array( 'Reupholstery', 'New covers in any fabric.' ),
				array( 'Foam refresh', 'Restore comfort and shape.' ),
				array( 'Repairs', 'Frames, springs and legs.' ),
				array( 'Majlis seating', 'Traditional floor and sofa seating.' ),
			),
			'faqs'       => array(
				array( 'Is reupholstering cheaper than a new sofa?', 'For good-quality frames it is often better value and gives a custom result.' ),
				array( 'Do you collect the sofa?', 'Yes, we collect, reupholster in our workshop and deliver it back.' ),
				array( 'Can you upholster on site?', 'Some jobs, like majlis seating, can be done on site — we confirm after a visit.' ),
			),
			'related'    => array( 'chair-upholstery', 'cushion-pillow-upholstery', 'restaurant-seating-upholstery', 'outdoor-furniture-upholstery' ),
		) ),

		'chair-upholstery' => dbh_s( 'upholstery', 'Chair Upholstery', array(
			'media'      => array( 'cv-chair-upholstery', 'chair' ),
			'seo_title'  => 'Chair Upholstery Dubai | Dining, Office & Accent Chairs',
			'seo_desc'   => 'Chair upholstery in Dubai: reupholster dining chairs, accent chairs, headboards and office chairs in fabric or leather. Collection and delivery. Free quotation.',
			'card'       => 'Dining, accent and office chairs reupholstered in fabric or leather.',
			'h1'         => '<em>Chair</em> upholstery in Dubai',
			'lead'       => 'Refresh a full dining set, rescue a favourite armchair or restyle office seating — re-covered in fabric or leather with new foam where needed.',
			'what_title' => 'Small pieces, <em>big difference</em>',
			'paras'      => array(
				'Chairs take daily wear. New fabric and padding transform dining sets and accent chairs and can tie a room together with your curtains and cushions.',
				'We also upholster headboards, ottomans, benches and office chairs for homes and businesses.',
			),
			'checks'     => array( 'Dining and accent chairs', 'Office and task chairs', 'Headboards, ottomans and benches', 'Fabric or leather', 'Foam replacement' ),
			'tiles'      => array(
				array( 'Dining sets', 'Matching seats in durable fabrics.' ),
				array( 'Armchairs', 'Restore a favourite piece.' ),
				array( 'Headboards', 'Upholstered statement beds.' ),
				array( 'Offices', 'Re-cover chairs in bulk.' ),
			),
			'faqs'       => array(
				array( 'Can you match my curtains?', 'Yes, we can use matching or coordinating fabrics from our catalogues.' ),
				array( 'Do you handle large quantities?', 'Yes, for offices, hotels and restaurants.' ),
				array( 'Which fabric is easiest to clean?', 'Performance and stain-resistant fabrics are ideal for dining chairs.' ),
			),
			'related'    => array( 'sofa-upholstery', 'restaurant-seating-upholstery', 'cushion-pillow-upholstery', 'car-seat-upholstery' ),
		) ),

		'restaurant-seating-upholstery' => dbh_s( 'upholstery', 'Restaurant Seating Upholstery', array(
			'media'      => array( 'cv-restaurant-seating', 'restaurant' ),
			'seo_title'  => 'Restaurant Seating Upholstery Dubai | Booths, Banquettes & Chairs',
			'seo_desc'   => 'Restaurant, café and hotel seating upholstery in Dubai: booths, banquettes, bar stools and chairs in commercial-grade fabric or leatherette. Minimal downtime.',
			'card'       => 'Booths, banquettes and chairs in commercial-grade materials.',
			'h1'         => '<em>Restaurant seating</em> upholstery in Dubai',
			'lead'       => 'Commercial upholstery for restaurants, cafés, hotels and lounges — booths, banquettes, bar stools and dining chairs, planned around your opening hours.',
			'what_title' => 'Built for <em>busy service</em>',
			'paras'      => array(
				'Hospitality seating needs durable, easy-to-clean materials. We use commercial-grade fabrics and leatherettes that withstand spills and daily cleaning.',
				'We work in phases or overnight to keep your venue open, and can build new custom banquettes to fit your layout.',
			),
			'checks'     => array( 'Booths and banquettes', 'Bar stools and dining chairs', 'Commercial-grade, wipe-clean materials', 'Night and phased work', 'Custom new seating' ),
			'tiles'      => array(
				array( 'Restaurants', 'Booths and banquettes refreshed.' ),
				array( 'Cafés', 'Durable chairs and stools.' ),
				array( 'Hotels', 'Lobby and lounge seating.' ),
				array( 'Custom builds', 'New banquettes to fit your space.' ),
			),
			'faqs'       => array(
				array( 'Can you work while we are closed?', 'Yes, we schedule work overnight or in phases.' ),
				array( 'Which materials are best for restaurants?', 'Commercial leatherettes and performance fabrics that are easy to clean.' ),
				array( 'Do you build new booths?', 'Yes, we can make custom seating to your layout.' ),
			),
			'related'    => array( 'chair-upholstery', 'sofa-upholstery', 'outdoor-furniture-upholstery', 'commercial-wallpaper-installation' ),
		) ),

		'car-seat-upholstery' => dbh_s( 'upholstery', 'Car Seat Upholstery', array(
			'media'      => array( 'car-seat', 'car-upholstery' ),
			'catalogue'  => false,
			'seo_title'  => 'Car Seat Upholstery Dubai | Leather & Fabric Car Interiors',
			'seo_desc'   => 'Car seat upholstery in Dubai: leather and fabric seat covers, repairs and full interior retrims for cars and SUVs. Free quotation on WhatsApp.',
			'card'       => 'Leather and fabric seat retrims and repairs for cars and SUVs.',
			'h1'         => '<em>Car seat</em> upholstery in Dubai',
			'lead'       => 'Restore worn, cracked or torn car seats, or upgrade to premium leather. We retrim seats, door cards and headliners for cars and SUVs.',
			'what_title' => 'A fresh interior <em>for your car</em>',
			'paras'      => array(
				'Strong UAE sun is hard on car interiors. We repair tears and cracked leather, replace seat foam and retrim seats in leather, leatherette or durable fabric.',
				'Send photos of your seats on WhatsApp with the make and model, and we will advise the options and price.',
			),
			'checks'     => array( 'Leather, leatherette and fabric', 'Tear and crack repair', 'Seat foam replacement', 'Door cards and headliners', 'Custom stitching and colours' ),
			'tiles'      => array(
				array( 'Full retrim', 'New covers for all seats.' ),
				array( 'Repairs', 'Fix tears, burns and cracks.' ),
				array( 'Headliners', 'Replace sagging roof lining.' ),
				array( 'Custom designs', 'Contrast stitching and colours.' ),
			),
			'faqs'       => array(
				array( 'How long does a car seat retrim take?', 'It depends on the car and the work; we confirm after seeing photos.' ),
				array( 'Can you repair just one seat?', 'Yes, we can repair or re-cover individual seats.' ),
				array( 'How do I get a quote?', 'Send photos and your car model on WhatsApp.' ),
			),
			'related'    => array( 'sofa-upholstery', 'chair-upholstery', 'cushion-pillow-upholstery', 'outdoor-furniture-upholstery' ),
		) ),

		'outdoor-furniture-upholstery' => dbh_s( 'upholstery', 'Outdoor Furniture Upholstery', array(
			'media'      => array( 'cv-outdoor-furniture', 'outdoor-furniture' ),
			'seo_title'  => 'Outdoor Furniture Upholstery Dubai | Weatherproof Cushions',
			'seo_desc'   => 'Outdoor furniture upholstery in Dubai: weather and UV-resistant cushions for terraces, pool areas, majlis and yachts. Sunbrella-style outdoor fabrics. Free visit.',
			'card'       => 'UV and weather-resistant cushions for terraces, pools and majlis.',
			'h1'         => '<em>Outdoor furniture</em> upholstery in Dubai',
			'lead'       => 'Sun-proof, splash-proof comfort for terraces, pool decks, rooftops and outdoor majlis. We make new cushions and re-cover outdoor seating in outdoor-rated fabrics.',
			'what_title' => 'Made for <em>UAE sun</em>',
			'paras'      => array(
				'Ordinary fabric fades and rots outdoors. Outdoor-rated fabrics resist UV, moisture and mildew, and quick-dry foam lets cushions recover after rain or pool splashes.',
				'Browse our Sunbrella outdoor fabric collection on the catalogues page, then we measure your furniture and make cushions to fit.',
			),
			'checks'     => array( 'UV and fade-resistant fabrics', 'Quick-dry outdoor foam', 'Terraces, pools, rooftops and yachts', 'Outdoor majlis seating', 'Custom shapes and piping' ),
			'tiles'      => array(
				array( 'Terraces', 'Lounge and dining cushions.' ),
				array( 'Pool decks', 'Sunbed cushions that dry fast.' ),
				array( 'Outdoor majlis', 'Floor seating and back cushions.' ),
				array( 'Hotels', 'Durable cushions for guests.' ),
			),
			'faqs'       => array(
				array( 'Will outdoor cushions fade?', 'Outdoor-rated fabrics are made to resist UV fading much better than indoor fabrics.' ),
				array( 'Can cushions stay outside in summer?', 'Yes, though storing them during sandstorms and unused months extends their life.' ),
				array( 'Do you make custom shapes?', 'Yes, we measure and pattern cushions for any furniture.' ),
			),
			'related'    => array( 'sofa-upholstery', 'cushion-pillow-upholstery', 'restaurant-seating-upholstery', 'rubber-flooring' ),
		) ),

		'cushion-pillow-upholstery' => dbh_s( 'upholstery', 'Cushion &amp; Pillow Upholstery', array(
			'media'      => array( 'cv-cushion-pillow', 'cushion', 'pillow', 'sofa-upholstery-fabric' ),
			'seo_title'  => 'Custom Cushions & Pillows Dubai | Cushion Covers & Fillings',
			'seo_desc'   => 'Custom cushions and pillows in Dubai: scatter cushions, floor cushions, window-seat and bench pads with new covers and fillings. Match your curtains.',
			'card'       => 'Custom cushions, covers and fillings — matched to your curtains.',
			'h1'         => 'Custom <em>cushions &amp; pillows</em> in Dubai',
			'lead'       => 'Scatter cushions, floor cushions, window-seat pads and bench cushions — made to size with new covers and fillings, and matched to your curtains or sofa.',
			'what_title' => 'The finishing <em>touch</em>',
			'paras'      => array(
				'Cushions pull a room together. We make covers from the same fabric as your curtains or upholstery, or choose contrasting textures for depth.',
				'Choose feather, fibre or foam fillings, zip or envelope closures, and finishing details like piping and fringing.',
			),
			'checks'     => array( 'Scatter and floor cushions', 'Window-seat and bench pads', 'Feather, fibre or foam fillings', 'Piping, fringes and zips', 'Matched to curtains and sofas' ),
			'tiles'      => array(
				array( 'Scatter cushions', 'Any size, any fabric.' ),
				array( 'Window seats', 'Custom pads for bay windows.' ),
				array( 'Majlis cushions', 'Floor and back cushions.' ),
				array( 'Refills', 'New fillings for flat cushions.' ),
			),
			'faqs'       => array(
				array( 'Can you match my curtain fabric?', 'Yes — order extra fabric with your curtains or bring a sample.' ),
				array( 'Can you refill old cushions?', 'Yes, we replace flat or lumpy fillings.' ),
				array( 'Do you make floor cushions?', 'Yes, including majlis floor seating.' ),
			),
			'related'    => array( 'sofa-upholstery', 'outdoor-furniture-upholstery', 'curtain-alteration-stitching', 'chair-upholstery' ),
		) ),

		/* ------------------------------------------------ WALLPAPER ------------------------------------------------ */

		'3d-wallpaper' => dbh_s( 'wallpaper', '3D Wallpaper', array(
			'media'      => array( '3d-wallpaper' ),
			'seo_title'  => '3D Wallpaper Dubai | Feature Walls Supplied & Installed',
			'seo_desc'   => '3D wallpaper in Dubai for feature walls in living rooms, bedrooms, offices and shops. Depth-effect designs supplied and professionally installed. Free site visit.',
			'card'       => 'Depth-effect designs for striking feature walls.',
			'h1'         => '<em>3D</em> wallpaper in Dubai',
			'lead'       => 'Create a feature wall with real visual depth. 3D wallpapers use shading and texture to make walls look carved, layered or geometric.',
			'what_title' => 'Feature walls <em>with depth</em>',
			'paras'      => array(
				'3D wallpaper is ideal behind a bed, TV or sofa, in reception areas and in retail spaces where the wall should make an impression.',
				'We prepare the wall, plan the pattern repeat so it lines up perfectly, and install with clean, invisible seams.',
			),
			'checks'     => array( 'Geometric, stone and abstract designs', 'Feature walls for any room', 'Pattern alignment planned', 'Wall preparation included', 'Homes, offices and shops' ),
			'tiles'      => array(
				array( 'TV walls', 'A backdrop with impact.' ),
				array( 'Bedrooms', 'Headboard feature walls.' ),
				array( 'Receptions', 'Make a first impression.' ),
				array( 'Retail', 'Eye-catching shop interiors.' ),
			),
			'faqs'       => array(
				array( 'Is 3D wallpaper actually raised?', 'Most 3D wallpapers create depth visually with print and texture; some have embossed surfaces.' ),
				array( 'Does the wall need preparation?', 'Smooth, clean walls give the best result; we prepare walls as needed.' ),
				array( 'Can 3D wallpaper be removed later?', 'Yes — see our wallpaper removal service.' ),
			),
			'related'    => array( 'textured-wallpaper', 'custom-wall-murals', 'office-wallpaper', 'wallpaper-removal' ),
		) ),

		'textured-wallpaper' => dbh_s( 'wallpaper', 'Textured Wallpaper', array(
			'media'      => array( 'textured-wallpaper' ),
			'seo_title'  => 'Textured Wallpaper Dubai | Linen, Grasscloth & Plaster Effects',
			'seo_desc'   => 'Textured wallpaper in Dubai: linen, grasscloth, silk, plaster and stone effects for homes, hotels and offices. Supplied and installed. Free site visit.',
			'card'       => 'Linen, grasscloth and plaster effects that add warmth.',
			'h1'         => '<em>Textured</em> wallpaper in Dubai',
			'lead'       => 'Subtle texture that makes walls feel warmer and more luxurious. Linen, grasscloth, silk, plaster and stone effects for calm, layered interiors.',
			'what_title' => 'Quiet luxury <em>on your walls</em>',
			'paras'      => array(
				'Textured wallcoverings hide small wall imperfections and add depth that paint cannot. They work in whole rooms or as a soft feature wall.',
				'Many textured wallpapers are washable and durable, making them popular in hotels, offices and family homes.',
			),
			'checks'     => array( 'Linen, grasscloth and silk looks', 'Plaster, concrete and stone effects', 'Hides minor wall imperfections', 'Washable commercial options', 'Professional installation' ),
			'tiles'      => array(
				array( 'Living rooms', 'Warm, layered walls.' ),
				array( 'Bedrooms', 'Soft texture behind the bed.' ),
				array( 'Hotels', 'Durable, elegant corridors and rooms.' ),
				array( 'Offices', 'Calm meeting rooms.' ),
			),
			'faqs'       => array(
				array( 'Is textured wallpaper washable?', 'Many vinyl-based textured wallpapers are wipeable; natural grasscloth needs gentle care.' ),
				array( 'Does it hide wall cracks?', 'It helps hide small imperfections; larger cracks are repaired first.' ),
				array( 'Can I wallpaper just one wall?', 'Yes, feature walls are very popular.' ),
			),
			'related'    => array( '3d-wallpaper', 'office-wallpaper', 'custom-wall-murals', 'commercial-wallpaper-installation' ),
		) ),

		'kids-room-wallpaper' => dbh_s( 'wallpaper', 'Kids Room Wallpaper', array(
			'media'      => array( 'kids-room-wallpaper', 'kids-wallpaper' ),
			'seo_title'  => 'Kids Room Wallpaper Dubai | Nursery & Children’s Wallpaper',
			'seo_desc'   => 'Kids room and nursery wallpaper in Dubai: playful patterns, murals and washable wallcoverings, supplied and installed. Free site visit.',
			'card'       => 'Playful, washable designs for nurseries and kids’ rooms.',
			'h1'         => '<em>Kids room</em> wallpaper in Dubai',
			'lead'       => 'Turn a bedroom or nursery into a world of its own — animals, stars, maps, clouds and custom murals in washable, family-friendly wallcoverings.',
			'what_title' => 'Rooms they’ll <em>love growing up in</em>',
			'paras'      => array(
				'Kids’ wallpaper adds personality and makes a room feel special. Washable finishes make small fingerprints and crayon marks easy to wipe away.',
				'Combine a patterned feature wall with plain walls, or design a custom mural with your child’s favourite theme.',
			),
			'checks'     => array( 'Nursery and kids’ room designs', 'Washable, wipe-clean finishes', 'Custom murals and names', 'Feature walls or full rooms', 'Matching blackout blinds available' ),
			'tiles'      => array(
				array( 'Nurseries', 'Soft, calming patterns.' ),
				array( 'Toddlers', 'Animals, clouds and stars.' ),
				array( 'Older kids', 'Maps, space and sport themes.' ),
				array( 'Playrooms', 'Bright, durable walls.' ),
			),
			'faqs'       => array(
				array( 'Is kids’ wallpaper washable?', 'We recommend washable finishes for kids’ rooms.' ),
				array( 'Can you print a custom design?', 'Yes — see custom wall murals.' ),
				array( 'Can it be changed when they grow?', 'Yes, wallpaper can be removed and replaced.' ),
			),
			'related'    => array( 'custom-wall-murals', '3d-wallpaper', 'blackout-blinds', 'rubber-flooring' ),
		) ),

		'office-wallpaper' => dbh_s( 'wallpaper', 'Office Wallpaper', array(
			'media'      => array( 'office-wallpaper' ),
			'seo_title'  => 'Office Wallpaper Dubai | Branded & Commercial Wallcoverings',
			'seo_desc'   => 'Office wallpaper in Dubai: branded walls, textured and durable commercial wallcoverings for receptions, meeting rooms and corridors. Supply and installation.',
			'card'       => 'Branded and durable wallcoverings for receptions and meeting rooms.',
			'h1'         => '<em>Office</em> wallpaper in Dubai',
			'lead'       => 'Bring your brand and culture to the walls. Durable, professional wallcoverings for receptions, meeting rooms, corridors and break-out areas.',
			'what_title' => 'Walls that <em>work for your brand</em>',
			'paras'      => array(
				'Office wallpaper can show your logo, values and colours, add texture to meeting rooms or hide wear in busy corridors. Commercial wallcoverings are durable and easy to clean.',
				'We install outside working hours when needed and coordinate with your fit-out team.',
			),
			'checks'     => array( 'Branded logo and graphic walls', 'Durable commercial grades', 'Textured meeting room walls', 'Evening and weekend installation', 'Coordinated with fit-outs' ),
			'tiles'      => array(
				array( 'Receptions', 'Logo and brand walls.' ),
				array( 'Meeting rooms', 'Texture and calm.' ),
				array( 'Corridors', 'Durable, scuff-resistant walls.' ),
				array( 'Break-out areas', 'Murals and colour.' ),
			),
			'faqs'       => array(
				array( 'Can you print our logo on the wall?', 'Yes — send your artwork and we prepare a proof.' ),
				array( 'Is office wallpaper durable?', 'Commercial-grade wallcoverings are designed for high traffic.' ),
				array( 'Can you work at night?', 'Yes, we schedule around your working hours.' ),
			),
			'related'    => array( 'commercial-wallpaper-installation', 'custom-wall-murals', 'office-carpet-installation', 'office-blinds' ),
		) ),

		'custom-wall-murals' => dbh_s( 'wallpaper', 'Custom Wall Murals', array(
			'media'      => array( 'mural' ),
			'seo_title'  => 'Custom Wall Murals Dubai | Printed Photo & Art Murals',
			'seo_desc'   => 'Custom wall murals in Dubai: print any photo, artwork or design to fit your wall exactly, for homes, cafés, offices and shops. Supplied and installed.',
			'card'       => 'Your photo or artwork printed to fit the wall exactly.',
			'h1'         => 'Custom <em>wall murals</em> in Dubai',
			'lead'       => 'Any image, any wall. We print photos, artwork, skylines, landscapes or brand graphics to the exact size of your wall and install them seamlessly.',
			'what_title' => 'Art that fills <em>the whole wall</em>',
			'paras'      => array(
				'A custom mural is sized to your wall, so the design fits around doors, windows and corners exactly as planned. We check image resolution and send a proof before printing.',
				'Murals are popular in kids’ rooms, living rooms, cafés, restaurants, gyms and offices.',
			),
			'checks'     => array( 'Print any photo or artwork', 'Sized exactly to your wall', 'Proof before printing', 'Matte and textured finishes', 'Homes and businesses' ),
			'tiles'      => array(
				array( 'Living rooms', 'Landscapes and skylines.' ),
				array( 'Kids’ rooms', 'Custom themes and names.' ),
				array( 'Cafés', 'Brand storytelling on the wall.' ),
				array( 'Gyms', 'Motivational graphics.' ),
			),
			'faqs'       => array(
				array( 'What image resolution do I need?', 'Higher is better; send the original file and we check it for your wall size.' ),
				array( 'Can you design the mural?', 'We can help adapt and size your artwork.' ),
				array( 'Will I see a proof?', 'Yes, you approve a proof before we print.' ),
			),
			'related'    => array( 'kids-room-wallpaper', 'office-wallpaper', '3d-wallpaper', 'commercial-wallpaper-installation' ),
		) ),

		'wallpaper-removal' => dbh_s( 'wallpaper', 'Wallpaper Removal', array(
			'media'      => array( 'wallpaper-removal' ),
			'catalogue'  => false,
			'seo_title'  => 'Wallpaper Removal Dubai | Clean Removal & Wall Preparation',
			'seo_desc'   => 'Wallpaper removal in Dubai: careful stripping, adhesive cleaning and wall preparation ready for paint or new wallpaper. Homes, offices and hand-overs.',
			'card'       => 'Careful stripping and wall prep, ready for paint or new paper.',
			'h1'         => '<em>Wallpaper removal</em> in Dubai',
			'lead'       => 'Old wallpaper out, smooth walls back. We strip wallpaper carefully, clean off adhesive and prepare walls for paint or a new wallcovering.',
			'what_title' => 'A clean start <em>for your walls</em>',
			'paras'      => array(
				'Rushed removal damages plaster and gypsum. We use the right method for each wallpaper type to protect the wall underneath, then clean and prepare the surface.',
				'Ideal before moving out, after buying a property, or before installing new wallpaper.',
			),
			'checks'     => array( 'Careful stripping by wallpaper type', 'Adhesive residue cleaned', 'Minor wall repairs', 'Ready for paint or new wallpaper', 'Move-out and hand-over jobs' ),
			'tiles'      => array(
				array( 'Move-outs', 'Return walls to original condition.' ),
				array( 'Renovations', 'Prepare for a new look.' ),
				array( 'Offices', 'Rebranding and refits.' ),
				array( 'Re-wallpapering', 'Removal and new installation together.' ),
			),
			'faqs'       => array(
				array( 'Will removal damage my walls?', 'We use careful methods to minimise damage and repair small marks.' ),
				array( 'Can you install new wallpaper straight after?', 'Yes, once the wall is prepared.' ),
				array( 'Do you remove wallpaper for landlords?', 'Yes, including move-out and hand-over work.' ),
			),
			'related'    => array( 'textured-wallpaper', '3d-wallpaper', 'commercial-wallpaper-installation', 'custom-wall-murals' ),
		) ),

		'commercial-wallpaper-installation' => dbh_s( 'wallpaper', 'Commercial Wallpaper Installation', array(
			'media'      => array( 'commercial-wallpaper' ),
			'seo_title'  => 'Commercial Wallpaper Installation Dubai | Hotels, Offices, Retail',
			'seo_desc'   => 'Commercial wallpaper installation in Dubai and the UAE for hotels, offices, retail and restaurants. Large-area projects, night work and fit-out coordination.',
			'card'       => 'Large-area wallcovering projects for hotels, offices and retail.',
			'h1'         => '<em>Commercial wallpaper</em> installation',
			'lead'       => 'Hotels, offices, retail units, restaurants and clinics — we install commercial wallcoverings at scale, on schedule and to fit-out standards.',
			'what_title' => 'Projects <em>at scale</em>',
			'paras'      => array(
				'Commercial projects need planning: quantities, pattern matching across long runs, access, and coordination with other trades. Our team handles all of it.',
				'We work nights and weekends where needed so your business keeps running.',
			),
			'checks'     => array( 'Hotels, offices, retail and F&amp;B', 'Large-area pattern matching', 'Fit-out coordination', 'Night and weekend work', 'Across the UAE' ),
			'tiles'      => array(
				array( 'Hotels', 'Rooms, corridors and lobbies.' ),
				array( 'Retail', 'Brand-led shop walls.' ),
				array( 'Restaurants', 'Feature walls and murals.' ),
				array( 'Clinics', 'Durable, cleanable walls.' ),
			),
			'faqs'       => array(
				array( 'Do you work with fit-out contractors?', 'Yes, we coordinate with contractors and project managers.' ),
				array( 'Can you supply the wallpaper too?', 'Yes, or we install materials you have specified.' ),
				array( 'Do you work outside Dubai?', 'Yes, across all emirates.' ),
			),
			'related'    => array( 'office-wallpaper', 'textured-wallpaper', 'office-carpet-installation', 'restaurant-seating-upholstery' ),
		) ),

		/* ------------------------------------------------ CARPETS ------------------------------------------------ */

		'wall-to-wall-carpet' => dbh_s( 'carpets', 'Wall to Wall Carpet', array(
			'media'      => array( 'wall-to-wall', 'carpet' ),
			'seo_title'  => 'Wall to Wall Carpet Dubai | Fitted Carpet Supply & Installation',
			'seo_desc'   => 'Wall to wall carpet in Dubai for bedrooms, majlis, hotels and offices: soft, durable fitted carpets with underlay and expert installation. Free site visit.',
			'card'       => 'Soft, quiet fitted carpets for bedrooms, majlis and hotels.',
			'h1'         => '<em>Wall to wall</em> carpet in Dubai',
			'lead'       => 'Soft, warm and quiet underfoot. Fitted carpet installed edge to edge for bedrooms, majlis, hotel rooms and offices.',
			'what_title' => 'Comfort from <em>wall to wall</em>',
			'paras'      => array(
				'Fitted carpet reduces noise, adds warmth to air-conditioned rooms and feels luxurious in bedrooms and majlis. A good underlay makes it more comfortable and longer-lasting.',
				'We measure the room, plan seams where they will not show and install with neat edges at doors and walls.',
			),
			'checks'     => array( 'Cut pile, loop and twist carpets', 'Underlay for comfort and sound', 'Hidden seams and neat edges', 'Bedrooms, majlis, hotels and offices', 'Old carpet removal available' ),
			'tiles'      => array(
				array( 'Bedrooms', 'Soft and quiet.' ),
				array( 'Majlis', 'Rich, formal carpets.' ),
				array( 'Hotels', 'Durable contract carpets.' ),
				array( 'Prayer rooms', 'Comfortable fitted carpet.' ),
			),
			'faqs'       => array(
				array( 'Is fitted carpet suitable for Dubai?', 'Yes, in air-conditioned bedrooms, majlis and offices it adds warmth and reduces noise.' ),
				array( 'Do you remove old carpet?', 'Yes, removal can be included.' ),
				array( 'How do I keep carpet clean?', 'Vacuum regularly and book periodic deep cleaning.' ),
			),
			'related'    => array( 'carpet-tiles', 'custom-carpet-runners', 'carpet-cleaning-maintenance', 'wooden-flooring' ),
		) ),

		'carpet-tiles' => dbh_s( 'carpets', 'Carpet Tiles', array(
			'media'      => array( 'carpet-tile' ),
			'seo_title'  => 'Carpet Tiles Dubai | Office & Commercial Carpet Tiles',
			'seo_desc'   => 'Carpet tiles in Dubai for offices, schools and commercial spaces: durable, easy-to-replace modular carpet in many patterns. Supply and installation.',
			'card'       => 'Modular carpet that is durable and easy to replace.',
			'h1'         => '<em>Carpet tiles</em> in Dubai',
			'lead'       => 'Modular, hard-wearing carpet for offices, schools, clinics and commercial spaces. Replace a single tile instead of the whole floor.',
			'what_title' => 'Flexible flooring <em>for busy spaces</em>',
			'paras'      => array(
				'Carpet tiles install quickly with minimal disruption, work well over raised access floors, and let you mix colours to zone areas or create patterns.',
				'Damaged or stained tiles can be swapped individually, which keeps maintenance costs down.',
			),
			'checks'     => array( 'Commercial-grade durability', 'Easy single-tile replacement', 'Patterns and zoning', 'Works on raised access floors', 'Fast installation' ),
			'tiles'      => array(
				array( 'Offices', 'Quiet, professional floors.' ),
				array( 'Schools', 'Durable classrooms and libraries.' ),
				array( 'Clinics', 'Comfortable waiting areas.' ),
				array( 'Retail', 'Defined zones with colour.' ),
			),
			'faqs'       => array(
				array( 'Carpet tiles or broadloom?', 'Tiles are easier to install and replace; broadloom gives a seamless look.' ),
				array( 'Can you install over raised floors?', 'Yes, carpet tiles are ideal for raised access floors.' ),
				array( 'Can we install over a weekend?', 'Yes, we can schedule around your office hours.' ),
			),
			'related'    => array( 'office-carpet-installation', 'wall-to-wall-carpet', 'exhibition-carpet', 'vinyl-flooring' ),
		) ),

		'exhibition-carpet' => dbh_s( 'carpets', 'Exhibition Carpet', array(
			'media'      => array( 'exhibition' ),
			'catalogue'  => false,
			'seo_title'  => 'Exhibition Carpet Dubai | Event & Stand Carpet Supply',
			'seo_desc'   => 'Exhibition and event carpet in Dubai: fast supply and laying for stands, halls, weddings and events in many colours. Short lead times. Call or WhatsApp.',
			'card'       => 'Fast-turnaround carpet for stands, halls and events.',
			'h1'         => '<em>Exhibition</em> carpet in Dubai',
			'lead'       => 'Quick, clean carpet for exhibition stands, conferences, weddings and events — in the colour you need, laid on time for build-up.',
			'what_title' => 'On time for <em>build-up</em>',
			'paras'      => array(
				'Event schedules are tight. We supply and lay exhibition carpet in a wide choice of colours, cut to your stand or hall layout.',
				'We serve exhibition venues, hotels and event spaces across Dubai and the UAE.',
			),
			'checks'     => array( 'Many colours available', 'Cut to stand or hall layout', 'Fast supply and laying', 'Protective film options', 'Removal after the event' ),
			'tiles'      => array(
				array( 'Exhibition stands', 'Branded colours for your booth.' ),
				array( 'Conferences', 'Halls and walkways.' ),
				array( 'Weddings', 'Aisles and stages.' ),
				array( 'Launch events', 'Clean, fresh floors.' ),
			),
			'faqs'       => array(
				array( 'How quickly can you supply?', 'Lead times depend on colour and quantity — contact us early with your event dates.' ),
				array( 'Do you remove the carpet afterwards?', 'Yes, removal can be arranged.' ),
				array( 'Do you work at Dubai exhibition venues?', 'Yes, we coordinate with venue build-up schedules.' ),
			),
			'related'    => array( 'carpet-tiles', 'custom-carpet-runners', 'wall-to-wall-carpet', 'sports-flooring' ),
		) ),

		'office-carpet-installation' => dbh_s( 'carpets', 'Office Carpet Installation', array(
			'media'      => array( 'office-carpet' ),
			'seo_title'  => 'Office Carpet Installation Dubai | Commercial Carpet Fitting',
			'seo_desc'   => 'Office carpet installation in Dubai: carpet tiles and broadloom for offices, meeting rooms and receptions. Night and weekend fitting. Free site survey.',
			'card'       => 'Carpet tiles and broadloom fitted around your working hours.',
			'h1'         => '<em>Office carpet</em> installation in Dubai',
			'lead'       => 'Quieter, more comfortable offices. We supply and install carpet tiles and broadloom for open-plan areas, meeting rooms and receptions — with minimal disruption.',
			'what_title' => 'A better floor <em>for your team</em>',
			'paras'      => array(
				'Carpet reduces noise in open-plan offices and makes long working days more comfortable. We help you choose durable commercial grades and colours that match your brand.',
				'Furniture moving, old flooring removal and night or weekend installation can all be arranged.',
			),
			'checks'     => array( 'Carpet tiles and broadloom', 'Brand-matched colours', 'Old flooring removal', 'Furniture moving coordination', 'Night and weekend installation' ),
			'tiles'      => array(
				array( 'Open-plan', 'Acoustic comfort for teams.' ),
				array( 'Meeting rooms', 'Quiet, premium carpets.' ),
				array( 'Receptions', 'First-impression floors.' ),
				array( 'Refurbishments', 'Phased replacement.' ),
			),
			'faqs'       => array(
				array( 'Can you install while we keep working?', 'Yes, in phases or outside office hours.' ),
				array( 'Do you remove old carpet?', 'Yes, removal and disposal can be included.' ),
				array( 'Which carpet is best for offices?', 'Commercial carpet tiles are the most practical for most offices.' ),
			),
			'related'    => array( 'carpet-tiles', 'office-blinds', 'office-wallpaper', 'carpet-cleaning-maintenance' ),
		) ),

		'carpet-cleaning-maintenance' => dbh_s( 'carpets', 'Carpet Cleaning &amp; Maintenance', array(
			'media'      => array( 'carpet-cleaning' ),
			'catalogue'  => false,
			'seo_title'  => 'Carpet Cleaning & Maintenance Dubai | Homes & Offices',
			'seo_desc'   => 'Carpet cleaning and maintenance in Dubai for homes, offices and hotels: deep cleaning, stain treatment and scheduled maintenance plans. Book on WhatsApp.',
			'card'       => 'Deep cleaning, stain treatment and maintenance plans.',
			'h1'         => '<em>Carpet cleaning</em> &amp; maintenance in Dubai',
			'lead'       => 'Fresh, clean carpets that last longer. Deep cleaning, stain treatment and regular maintenance for homes, offices, hotels and majlis.',
			'what_title' => 'Protect your <em>investment</em>',
			'paras'      => array(
				'Dust and sand build up quickly in UAE carpets. Regular deep cleaning lifts embedded dirt, refreshes colours and extends carpet life.',
				'We also repair small damage, re-stretch loose fitted carpet and offer scheduled maintenance for offices and hospitality.',
			),
			'checks'     => array( 'Deep cleaning for fitted carpets', 'Stain and spot treatment', 'Re-stretching and small repairs', 'Scheduled maintenance plans', 'Homes, offices and hotels' ),
			'tiles'      => array(
				array( 'Homes', 'Bedrooms and majlis carpets.' ),
				array( 'Offices', 'Scheduled maintenance.' ),
				array( 'Hotels', 'Rooms and corridors.' ),
				array( 'Repairs', 'Re-stretching and patching.' ),
			),
			'faqs'       => array(
				array( 'How often should carpets be deep cleaned?', 'It depends on traffic; busy offices and family homes benefit from regular cleaning.' ),
				array( 'Can you remove old stains?', 'Many stains can be lifted or reduced; we assess on site.' ),
				array( 'Do you offer maintenance contracts?', 'Yes, for offices and hospitality.' ),
			),
			'related'    => array( 'wall-to-wall-carpet', 'carpet-tiles', 'office-carpet-installation', 'sofa-upholstery' ),
		) ),

		'custom-carpet-runners' => dbh_s( 'carpets', 'Custom Carpet Runners', array(
			'media'      => array( 'runner' ),
			'seo_title'  => 'Custom Carpet Runners Dubai | Stair & Hallway Runners',
			'seo_desc'   => 'Custom carpet runners in Dubai for stairs, hallways and corridors: made to length with bound edges, fitted with rods or grippers. Free site visit.',
			'card'       => 'Stair and hallway runners made to length and fitted.',
			'h1'         => 'Custom <em>carpet runners</em> in Dubai',
			'lead'       => 'Stair and hallway runners made to your exact length and width, with bound edges and secure fitting — comfortable, quieter and safer underfoot.',
			'what_title' => 'Style and grip <em>for stairs and halls</em>',
			'paras'      => array(
				'A runner protects stairs and hallway floors, softens footsteps and adds a decorative line through the home.',
				'We bind the edges in a matching or contrast colour and fit runners with grippers or decorative stair rods.',
			),
			'checks'     => array( 'Stair and hallway runners', 'Made to exact length', 'Bound edges in many colours', 'Gripper or stair-rod fitting', 'Villas, hotels and offices' ),
			'tiles'      => array(
				array( 'Staircases', 'Safer, quieter stairs.' ),
				array( 'Hallways', 'A soft path through the home.' ),
				array( 'Hotel corridors', 'Durable contract runners.' ),
				array( 'Stair rods', 'Brass, black or chrome finishes.' ),
			),
			'faqs'       => array(
				array( 'Can runners go on curved stairs?', 'Yes, we measure and fit them to each step.' ),
				array( 'What fixing do you use?', 'Grippers for a clean look or decorative rods as a feature.' ),
				array( 'Can I choose the binding colour?', 'Yes, matching or contrasting.' ),
			),
			'related'    => array( 'wall-to-wall-carpet', 'wooden-flooring', 'carpet-cleaning-maintenance', 'exhibition-carpet' ),
		) ),
	);
}
