<?php
/**
 * Area (local SEO) page content for Dubai communities and the wider UAE.
 *
 * @package dce-vibe
 */

defined( 'ABSPATH' ) || exit;

function dce_areas() {
	return array(
		'curtains-blinds-dubai-marina'                => array(
			'name'      => 'Dubai Marina &amp; JBR',
			'lead_area' => 'Dubai Marina / JBR',
			'region'    => 'dubai',
			'img'       => array( 'wave-curtains', 2 ),
			'seo_title' => 'Curtains & Blinds Dubai Marina & JBR | Free Home Visit',
			'seo_desc'  => 'Made-to-measure curtains and blinds in Dubai Marina, JBR and Bluewaters: wave, blackout and motorized options for high-rise glass. Free measurement visit.',
			'lead'      => 'Floor-to-ceiling glass, sea views and strong afternoon sun — we design curtains and blinds for Marina and JBR towers that control glare and heat without hiding the view.',
			'paras'     => array(
				'Most Marina and JBR apartments have full-height windows and sliding balcony doors facing the water or the Palm. Ceiling-mounted wave curtains with a sheer and blackout layer are the most popular solution, often motorized for wide spans.',
				'For west-facing living rooms we often suggest sunscreen roller blinds to cut glare on screens while keeping the view, with blackout curtains in bedrooms for restful sleep.',
			),
			'places'    => array( 'Dubai Marina', 'JBR (Jumeirah Beach Residence)', 'Bluewaters Island', 'Dubai Harbour', 'Emaar Beachfront', 'Marina Promenade', 'Al Sufouh' ),
			'popular'   => array( 'wave-curtains', 'motorized-curtains', 'sunscreen-roller-blinds', 'blackout-curtains', 'sheer-curtains', 'zebra-blinds' ),
			'faqs'      => array(
				array( 'Do you install in Dubai Marina towers?', 'Yes. We visit, measure and install in Marina, JBR, Bluewaters and Dubai Harbour towers, working with building access rules and timings.' ),
				array( 'What works best for full-height sea-view windows?', 'Ceiling-mounted wave curtains with a sheer for daytime and blackout for night, or sunscreen roller blinds where you want a clear view with less glare.' ),
				array( 'Can curtains be motorized in a rented apartment?', 'Yes — wire-free motor options avoid new wiring. We discuss what is allowed by your landlord or building.' ),
			),
			'nearby'    => array( 'curtains-blinds-jvc-jlt', 'curtains-blinds-palm-jumeirah', 'curtains-blinds-al-barsha' ),
		),

		'curtains-blinds-downtown-dubai-business-bay' => array(
			'name'      => 'Downtown Dubai &amp; Business Bay',
			'lead_area' => 'Downtown Dubai / Business Bay',
			'region'    => 'dubai',
			'img'       => array( 'blackout-curtains', 1 ),
			'seo_title' => 'Curtains & Blinds Downtown Dubai & Business Bay',
			'seo_desc'  => 'Curtains and blinds for Downtown Dubai and Business Bay apartments and offices: motorized wave curtains, blackout and sunscreen blinds. Free home visit.',
			'lead'      => 'Skyline views, modern apartments and busy offices. We fit sleek curtains and blinds across Downtown, Business Bay and DIFC — for homes, short-stay units and workplaces.',
			'paras'     => array(
				'Downtown and Business Bay apartments call for clean, modern solutions: slim ceiling tracks, wave curtains, zebra blinds and motorized systems that suit smart homes.',
				'For offices along Business Bay and DIFC we install sunscreen and vertical blinds that reduce glare on screens, plus logo-printed blinds for reception areas and shopfronts.',
			),
			'places'    => array( 'Downtown Dubai', 'Business Bay', 'DIFC', 'City Walk', 'Dubai Creek Harbour', 'Za’abeel', 'Al Wasl' ),
			'popular'   => array( 'wave-curtains', 'motorized-curtains', 'zebra-blinds', 'sunscreen-roller-blinds', 'blackout-roller-blinds', 'logo-sunscreen-blinds' ),
			'faqs'      => array(
				array( 'Do you work with offices in Business Bay and DIFC?', 'Yes. We supply sunscreen, vertical, blackout and branded blinds for offices and can schedule installation around working hours.' ),
				array( 'Can you fit blinds for holiday-home apartments?', 'Yes. Durable blackout rollers and easy-care curtains are popular for short-stay units in Downtown and Business Bay.' ),
				array( 'How soon can you visit?', 'Message us on WhatsApp with your location and preferred time — we confirm the earliest available visit.' ),
			),
			'nearby'    => array( 'curtains-blinds-jumeirah-umm-suqeim', 'curtains-blinds-deira-bur-dubai', 'curtains-blinds-dubai-hills-arabian-ranches' ),
		),

		'curtains-blinds-palm-jumeirah'               => array(
			'name'      => 'Palm Jumeirah',
			'lead_area' => 'Palm Jumeirah',
			'region'    => 'dubai',
			'img'       => array( 'american-style-curtains', 1 ),
			'seo_title' => 'Curtains & Blinds Palm Jumeirah | Villas & Apartments',
			'seo_desc'  => 'Luxury curtains and blinds for Palm Jumeirah villas and apartments: motorized, blackout, sheer and American style drapes. Free home visit & measurement.',
			'lead'      => 'Beachfront villas, signature residences and sea-facing apartments deserve curtains that match the view. We create luxurious, sun-ready window dressings across the Palm.',
			'paras'     => array(
				'Palm villas often have double-height living rooms and large glazed walls facing the sea. Motorized wave curtains, layered sheers and full American style drapes give comfort and drama.',
				'Strong sea light and salt air call for quality sheers, blackout bedrooms and easy-to-clean blinds in kitchens and bathrooms.',
			),
			'places'    => array( 'Palm Jumeirah fronds', 'Shoreline Apartments', 'Golden Mile', 'The Crescent', 'Palm West Beach', 'Club Vista Mare' ),
			'popular'   => array( 'motorized-curtains', 'american-style-curtains', 'sheer-curtains', 'wave-curtains', 'blackout-curtains', 'roman-blinds' ),
			'faqs'      => array(
				array( 'Do you handle double-height villa windows?', 'Yes. We plan tracks, motors and safe installation for tall windows, often with motorized curtains.' ),
				array( 'Which fabrics suit sea-facing rooms?', 'Quality sheers to soften light, lined or blackout curtains to protect furniture, and easy-care fabrics near the beach.' ),
				array( 'Can you dress a whole villa?', 'Yes. We measure every room and give a single quotation for curtains and blinds throughout the villa.' ),
			),
			'nearby'    => array( 'curtains-blinds-dubai-marina', 'curtains-blinds-jumeirah-umm-suqeim', 'curtains-blinds-al-barsha' ),
		),

		'curtains-blinds-jumeirah-umm-suqeim'         => array(
			'name'      => 'Jumeirah &amp; Umm Suqeim',
			'lead_area' => 'Jumeirah / Umm Suqeim',
			'region'    => 'dubai',
			'img'       => array( 'pinch-pleat-curtains', 1 ),
			'seo_title' => 'Curtains & Blinds Jumeirah & Umm Suqeim | Villa Experts',
			'seo_desc'  => 'Made-to-measure curtains and blinds for Jumeirah, Umm Suqeim and Al Safa villas: pinch pleat, Roman, blackout and wooden blinds. Free home visit.',
			'lead'      => 'Classic beach-side villas, family homes and majlis. We bring tailored pinch pleats, Roman curtains and wooden blinds to Jumeirah, Umm Suqeim and Al Safa.',
			'paras'     => array(
				'Jumeirah villas mix large living rooms, majlis and many bedrooms. We measure every window in one visit and help you build a consistent look from room to room.',
				'Popular choices include lined pinch pleat curtains with sheers, Roman blinds in kitchens and studies, and wooden blinds in home offices.',
			),
			'places'    => array( 'Jumeirah 1, 2 &amp; 3', 'Umm Suqeim 1, 2 &amp; 3', 'Al Safa', 'Al Manara', 'Al Wasl', 'La Mer' ),
			'popular'   => array( 'pinch-pleat-curtains', 'american-style-curtains', 'roman-curtains', 'wooden-blinds', 'blackout-curtains', 'sheer-curtains' ),
			'faqs'      => array(
				array( 'Do you make majlis curtains?', 'Yes — formal pinch pleat or American style curtains with sheers and valances are popular for majlis rooms.' ),
				array( 'Can you measure the whole villa in one visit?', 'Yes. We measure all windows and bring samples so you can choose room by room.' ),
				array( 'Do you remove old curtains and tracks?', 'Tell us during the visit and we will plan removal and replacement as part of the installation.' ),
			),
			'nearby'    => array( 'curtains-blinds-palm-jumeirah', 'curtains-blinds-al-barsha', 'curtains-blinds-downtown-dubai-business-bay' ),
		),

		'curtains-blinds-dubai-hills-arabian-ranches' => array(
			'name'      => 'Dubai Hills &amp; Arabian Ranches',
			'lead_area' => 'Dubai Hills / Arabian Ranches',
			'region'    => 'dubai',
			'img'       => array( 'roman-curtains', 2 ),
			'seo_title' => 'Curtains & Blinds Dubai Hills & Arabian Ranches',
			'seo_desc'  => 'Curtains and blinds for Dubai Hills Estate, Arabian Ranches and Mudon villas and townhouses. Wave, Roman, blackout and zebra blinds. Free home visit.',
			'lead'      => 'New villas, townhouses and family homes with big garden-facing windows. We fit complete homes in Dubai Hills Estate, Arabian Ranches, Mudon and nearby communities.',
			'paras'     => array(
				'Many homes here are handed over without window treatments, so families need curtains and blinds for every room at once. We measure the whole home and give one clear quotation.',
				'Garden-facing living rooms suit wave curtains with sheers; bedrooms need blackout; kitchens and kids’ rooms often get Roman or zebra blinds.',
			),
			'places'    => array( 'Dubai Hills Estate', 'Arabian Ranches 1, 2 &amp; 3', 'Mudon', 'Al Barsha South', 'Villanova', 'The Valley', 'Town Square' ),
			'popular'   => array( 'wave-curtains', 'blackout-curtains', 'roman-blinds', 'zebra-blinds', 'kids-curtains', 'motorized-curtains' ),
			'faqs'      => array(
				array( 'Can you fit out a new handover villa?', 'Yes. We measure every window, help you choose fabrics and blinds per room, and install everything in a planned schedule.' ),
				array( 'Is motorization possible in new villas?', 'Yes — new homes are the best time to plan power points for wired curtain motors.' ),
				array( 'Do you cover Town Square and Mudon?', 'Yes, along with Arabian Ranches, Dubai Hills, Villanova and surrounding communities.' ),
			),
			'nearby'    => array( 'curtains-blinds-damac-hills-motor-city', 'curtains-blinds-al-barsha', 'curtains-blinds-emirates-hills-springs' ),
		),

		'curtains-blinds-jvc-jlt'                     => array(
			'name'      => 'JVC, JLT &amp; Dubai South',
			'lead_area' => 'JVC / JLT / Dubai South',
			'region'    => 'dubai',
			'img'       => array( 'zebra-blinds', 1 ),
			'seo_title' => 'Curtains & Blinds JVC, JLT & Dubai South | Free Visit',
			'seo_desc'  => 'Affordable made-to-measure curtains and blinds in JVC, JLT, JVT, Dubai South and Al Furjan. Blackout, zebra and roller blinds. Free home visit.',
			'lead'      => 'Smart, practical curtains and blinds for apartments and townhouses in JVC, JLT, JVT, Al Furjan and Dubai South — made to measure and installed by our team.',
			'paras'     => array(
				'Apartments in JVC and JLT benefit from space-saving solutions: zebra blinds and roller blinds for bedrooms and living rooms, and wave curtains on balcony doors.',
				'For investors and landlords we recommend durable, easy-care options that suit tenants and short-stay guests.',
			),
			'places'    => array( 'Jumeirah Village Circle (JVC)', 'Jumeirah Lake Towers (JLT)', 'Jumeirah Village Triangle (JVT)', 'Al Furjan', 'Discovery Gardens', 'Dubai South', 'Dubai Production City' ),
			'popular'   => array( 'zebra-blinds', 'blackout-roller-blinds', 'wave-curtains', 'blackout-curtains', 'sunscreen-roller-blinds', 'vertical-blinds' ),
			'faqs'      => array(
				array( 'Do you work with landlords and property managers?', 'Yes. We can measure and fit multiple units and suggest durable, easy-maintenance options.' ),
				array( 'What is the most popular choice for JVC apartments?', 'Zebra blinds in living rooms and blackout roller blinds or curtains in bedrooms.' ),
				array( 'Do you cover Dubai South?', 'Yes, including Dubai South residential districts and nearby communities.' ),
			),
			'nearby'    => array( 'curtains-blinds-dubai-marina', 'curtains-blinds-al-barsha', 'curtains-blinds-damac-hills-motor-city' ),
		),

		'curtains-blinds-deira-bur-dubai'             => array(
			'name'      => 'Deira &amp; Bur Dubai',
			'lead_area' => 'Deira / Bur Dubai',
			'region'    => 'dubai',
			'img'       => array( 'curtain-project-dubai', 1 ),
			'seo_title' => 'Curtain Shop Deira & Bur Dubai | Curtains & Blinds Showroom',
			'seo_desc'  => 'Visit our curtain and blinds showroom at Empire Plaza, Naif Road, Deira. Made-to-measure curtains and blinds for Deira, Bur Dubai and Al Karama. Free visit.',
			'lead'      => 'Our showroom is right here in Deira — Empire Plaza Shopping Center, Shop 49, Naif Road. Visit to see fabrics and blinds, or book a free home visit anywhere in old Dubai.',
			'paras'     => array(
				'Deira and Bur Dubai are home to busy apartments, family villas, shops, hotels and offices. Being local means we can visit quickly and bring a wide range of samples.',
				'From pinch pleat curtains for traditional homes to vertical and sunscreen blinds for shops and offices, we cover every type of window in Deira, Bur Dubai, Al Karama and Oud Metha.',
			),
			'places'    => array( 'Deira', 'Naif', 'Al Rigga', 'Al Muraqqabat', 'Bur Dubai', 'Al Karama', 'Oud Metha', 'Al Qusais', 'Al Nahda' ),
			'popular'   => array( 'pinch-pleat-curtains', 'blackout-curtains', 'vertical-blinds', 'sunscreen-roller-blinds', 'sheer-curtains', 'logo-sunscreen-blinds' ),
			'faqs'      => array(
				array( 'Where is your showroom?', 'Empire Plaza Shopping Center, Shop 49, Naif Road, Deira, Dubai. Open Monday to Saturday, 8:00 AM to 5:30 PM.' ),
				array( 'Can I see fabric samples at the showroom?', 'Yes. You can browse fabrics and blind samples in person, or we bring them to your home.' ),
				array( 'Do you supply shops and hotels in Deira?', 'Yes — we work with retail, hospitality and offices as well as homes.' ),
			),
			'nearby'    => array( 'curtains-blinds-mirdif-festival-city', 'curtains-blinds-downtown-dubai-business-bay', 'curtains-blinds-sharjah' ),
		),

		'curtains-blinds-mirdif-festival-city'        => array(
			'name'      => 'Mirdif &amp; Dubai Festival City',
			'lead_area' => 'Mirdif / Festival City',
			'region'    => 'dubai',
			'img'       => array( 'eyelet-curtains', 1 ),
			'seo_title' => 'Curtains & Blinds Mirdif & Dubai Festival City',
			'seo_desc'  => 'Made-to-measure curtains and blinds for Mirdif, Dubai Festival City, Al Warqa and Rashidiya villas and apartments. Free home visit & installation.',
			'lead'      => 'Family villas and spacious apartments in Mirdif, Festival City, Al Warqa and Rashidiya — dressed with tailored curtains and practical blinds for every room.',
			'paras'     => array(
				'Mirdif villas often have generous living rooms, majlis and many bedrooms. We help families choose a coordinated look and plan blackout where children sleep.',
				'Festival City apartments and villas benefit from wave curtains on large glass, and sunscreen blinds to keep creek-side rooms cool.',
			),
			'places'    => array( 'Mirdif', 'Dubai Festival City', 'Al Warqa', 'Rashidiya', 'Nad Al Hamar', 'Al Mizhar', 'Muhaisnah' ),
			'popular'   => array( 'eyelet-curtains', 'pinch-pleat-curtains', 'blackout-curtains', 'kids-curtains', 'roman-blinds', 'wave-curtains' ),
			'faqs'      => array(
				array( 'Do you serve Al Warqa and Rashidiya?', 'Yes, as well as Mirdif, Festival City, Nad Al Hamar and nearby areas.' ),
				array( 'Can you match curtains across several rooms?', 'Yes — we help you create a consistent palette and choose fabrics room by room.' ),
				array( 'Do you install curtain rods and tracks?', 'Yes, all hardware is supplied and installed by our team.' ),
			),
			'nearby'    => array( 'curtains-blinds-deira-bur-dubai', 'curtains-blinds-silicon-oasis', 'curtains-blinds-sharjah' ),
		),

		'curtains-blinds-al-barsha'                   => array(
			'new'       => true,
			'title'     => 'Curtains &amp; Blinds in Al Barsha &amp; Al Sufouh',
			'name'      => 'Al Barsha &amp; Al Sufouh',
			'lead_area' => 'Al Barsha / Al Sufouh',
			'region'    => 'dubai',
			'img'       => array( 'sheer-curtains', 2 ),
			'seo_title' => 'Curtains & Blinds Al Barsha & Al Sufouh | Free Home Visit',
			'seo_desc'  => 'Made-to-measure curtains and blinds in Al Barsha, Barsha Heights (Tecom), Al Sufouh and Knowledge Park. Villas, apartments and offices. Free visit.',
			'lead'      => 'Villas in Al Barsha, apartments in Barsha Heights and offices around Al Sufouh — we measure, make and install curtains and blinds across this central Dubai district.',
			'paras'     => array(
				'Al Barsha’s family villas suit layered sheers and lined curtains, while Barsha Heights apartments benefit from zebra and blackout roller blinds that save space.',
				'For offices in Tecom and Knowledge Park we supply sunscreen, vertical and aluminium venetian blinds that cut glare on screens.',
			),
			'places'    => array( 'Al Barsha 1, 2 &amp; 3', 'Barsha Heights (Tecom)', 'Al Sufouh', 'Knowledge Park', 'Internet City', 'Media City', 'Al Quoz' ),
			'popular'   => array( 'sheer-curtains', 'wave-curtains', 'zebra-blinds', 'blackout-roller-blinds', 'sunscreen-roller-blinds', 'aluminium-venetian-blinds' ),
			'faqs'      => array(
				array( 'Do you install in Barsha Heights towers?', 'Yes, we work in residential and office towers across Barsha Heights and Al Sufouh.' ),
				array( 'Which blinds suit Media City offices?', 'Sunscreen roller, vertical and aluminium venetian blinds are durable and reduce glare on screens.' ),
				array( 'Can you visit on a Saturday?', 'We work Monday to Saturday. Message us with your preferred time.' ),
			),
			'nearby'    => array( 'curtains-blinds-dubai-marina', 'curtains-blinds-jumeirah-umm-suqeim', 'curtains-blinds-emirates-hills-springs' ),
		),

		'curtains-blinds-emirates-hills-springs'      => array(
			'new'       => true,
			'title'     => 'Curtains &amp; Blinds in Emirates Hills, The Springs &amp; Meadows',
			'name'      => 'Emirates Hills, Springs &amp; Meadows',
			'lead_area' => 'Emirates Hills / The Springs',
			'region'    => 'dubai',
			'img'       => array( 'american-style-curtains', 2 ),
			'seo_title' => 'Curtains & Blinds Emirates Hills, The Springs & Meadows',
			'seo_desc'  => 'Luxury and family curtains and blinds for Emirates Hills, The Meadows, The Springs, The Lakes and Jumeirah Islands villas. Free home visit & measurement.',
			'lead'      => 'From grand Emirates Hills mansions to family villas in The Springs and Meadows — tailored curtains, motorized systems and practical blinds for every room.',
			'paras'     => array(
				'Larger villas here often feature formal reception rooms, double-height halls and lake or golf-course views. American style drapery, motorized wave curtains and layered sheers are popular.',
				'In The Springs and Meadows townhouses and villas, families choose blackout bedrooms, Roman blinds in kitchens and durable blinds for kids’ rooms.',
			),
			'places'    => array( 'Emirates Hills', 'The Meadows', 'The Springs', 'The Lakes', 'Jumeirah Islands', 'Jumeirah Park', 'The Greens &amp; The Views' ),
			'popular'   => array( 'american-style-curtains', 'motorized-curtains', 'pinch-pleat-curtains', 'blackout-curtains', 'roman-blinds', 'wooden-blinds' ),
			'faqs'      => array(
				array( 'Do you handle large villa projects?', 'Yes. We measure every room, schedule production in phases if needed and install with a dedicated team.' ),
				array( 'Can you integrate motors with a smart home?', 'Yes — we confirm motor and system compatibility during the visit.' ),
				array( 'Do you serve The Greens and The Views?', 'Yes, as well as the villa communities nearby.' ),
			),
			'nearby'    => array( 'curtains-blinds-al-barsha', 'curtains-blinds-dubai-marina', 'curtains-blinds-dubai-hills-arabian-ranches' ),
		),

		'curtains-blinds-silicon-oasis'               => array(
			'new'       => true,
			'title'     => 'Curtains &amp; Blinds in Dubai Silicon Oasis &amp; International City',
			'name'      => 'Silicon Oasis &amp; International City',
			'lead_area' => 'Dubai Silicon Oasis',
			'region'    => 'dubai',
			'img'       => array( 'zebra-blinds', 1 ),
			'seo_title' => 'Curtains & Blinds Dubai Silicon Oasis & International City',
			'seo_desc'  => 'Made-to-measure curtains and blinds in Dubai Silicon Oasis, Academic City, International City and Dubailand. Apartments, villas and offices. Free visit.',
			'lead'      => 'Apartments, townhouses and offices in Dubai Silicon Oasis, International City, Academic City and Dubailand — measured and fitted by our own team.',
			'paras'     => array(
				'DSO apartments and townhouses suit practical solutions such as zebra blinds, blackout roller blinds and eyelet curtains that are easy to live with.',
				'For offices and campuses in DSO and Academic City we supply glare-reducing sunscreen and vertical blinds.',
			),
			'places'    => array( 'Dubai Silicon Oasis', 'International City', 'Academic City', 'Dubailand', 'Living Legends', 'Liwan', 'Warsan' ),
			'popular'   => array( 'zebra-blinds', 'blackout-roller-blinds', 'eyelet-curtains', 'vertical-blinds', 'sunscreen-roller-blinds', 'blackout-curtains' ),
			'faqs'      => array(
				array( 'Do you serve International City?', 'Yes, along with Silicon Oasis, Academic City, Liwan and Dubailand.' ),
				array( 'Do you supply schools and offices in Academic City?', 'Yes — vertical, sunscreen and blackout blinds for classrooms and offices.' ),
				array( 'Is the home visit really free?', 'Yes. Measurement and samples at your home are free, with a clear quotation afterwards.' ),
			),
			'nearby'    => array( 'curtains-blinds-mirdif-festival-city', 'curtains-blinds-damac-hills-motor-city', 'curtains-blinds-deira-bur-dubai' ),
		),

		'curtains-blinds-damac-hills-motor-city'      => array(
			'new'       => true,
			'title'     => 'Curtains &amp; Blinds in DAMAC Hills, Motor City &amp; Sports City',
			'name'      => 'DAMAC Hills, Motor City &amp; Sports City',
			'lead_area' => 'DAMAC Hills / Motor City',
			'region'    => 'dubai',
			'img'       => array( 'wave-curtains', 3 ),
			'seo_title' => 'Curtains & Blinds DAMAC Hills, Motor City & Sports City',
			'seo_desc'  => 'Curtains and blinds for DAMAC Hills 1 & 2, Motor City, Sports City and Remraam homes. Wave, blackout, zebra and roller blinds. Free home visit.',
			'lead'      => 'Golf-course villas, townhouses and apartments in DAMAC Hills, Motor City, Sports City and Remraam — made-to-measure curtains and blinds, installed by our team.',
			'paras'     => array(
				'Villas and townhouses overlooking golf courses and parks benefit from wave curtains with sheers that keep the green views while softening the sun.',
				'Apartments in Motor City and Sports City suit zebra and blackout roller blinds that are compact and easy to operate.',
			),
			'places'    => array( 'DAMAC Hills', 'DAMAC Hills 2 (Akoya)', 'Motor City', 'Dubai Sports City', 'Remraam', 'Majan', 'Arjan' ),
			'popular'   => array( 'wave-curtains', 'sheer-curtains', 'zebra-blinds', 'blackout-roller-blinds', 'blackout-curtains', 'bamboo-blinds' ),
			'faqs'      => array(
				array( 'Do you travel to DAMAC Hills 2?', 'Yes, we cover DAMAC Hills 1 and 2 and surrounding communities.' ),
				array( 'Do you offer outdoor blinds for terraces?', 'Yes — bamboo blinds are a popular choice for terraces and pergolas.' ),
				array( 'Can you fit the whole townhouse?', 'Yes. We measure every window and quote room by room in one document.' ),
			),
			'nearby'    => array( 'curtains-blinds-dubai-hills-arabian-ranches', 'curtains-blinds-jvc-jlt', 'curtains-blinds-silicon-oasis' ),
		),

		'curtains-blinds-abu-dhabi'                   => array(
			'name'      => 'Abu Dhabi',
			'lead_area' => 'Abu Dhabi',
			'region'    => 'uae',
			'img'       => array( 'motorized-curtains', 1 ),
			'seo_title' => 'Curtains & Blinds Abu Dhabi | Made to Measure & Installed',
			'seo_desc'  => 'Made-to-measure curtains and blinds in Abu Dhabi: Saadiyat, Yas, Al Reem, Khalifa City and more. Villas, apartments and offices. Free site visit.',
			'lead'      => 'We bring our Dubai workshop quality to Abu Dhabi — curtains, blinds and motorized systems for villas, apartments, offices and hospitality projects across the capital.',
			'paras'     => array(
				'From Saadiyat and Yas Island villas to Al Reem Island apartments, Abu Dhabi homes have large windows and strong sun. Layered sheers, blackout bedrooms and motorized curtains are in high demand.',
				'Send us your floor plan or window photos on WhatsApp to plan the visit — we coordinate measurement and installation trips to Abu Dhabi.',
			),
			'places'    => array( 'Saadiyat Island', 'Yas Island', 'Al Reem Island', 'Al Raha Beach', 'Khalifa City', 'Mohammed Bin Zayed City', 'Al Reef', 'Corniche' ),
			'popular'   => array( 'motorized-curtains', 'wave-curtains', 'blackout-curtains', 'sheer-curtains', 'sunscreen-roller-blinds', 'hospital-curtains' ),
			'faqs'      => array(
				array( 'Do you install curtains in Abu Dhabi?', 'Yes. We plan measurement and installation visits to Abu Dhabi for homes, offices and commercial projects.' ),
				array( 'Is there an extra charge for Abu Dhabi?', 'Any travel considerations are included clearly in your quotation — ask us on WhatsApp for details.' ),
				array( 'Can you handle commercial projects in Abu Dhabi?', 'Yes, including offices, clinics and hospitality projects.' ),
			),
			'nearby'    => array( 'curtains-blinds-al-ain', 'curtains-blinds-dubai-marina', 'curtains-blinds-damac-hills-motor-city' ),
		),

		'curtains-blinds-sharjah'                     => array(
			'name'      => 'Sharjah',
			'lead_area' => 'Sharjah',
			'region'    => 'uae',
			'img'       => array( 'pinch-pleat-curtains', 2 ),
			'seo_title' => 'Curtains & Blinds Sharjah | Made-to-Measure, Free Visit',
			'seo_desc'  => 'Made-to-measure curtains and blinds in Sharjah: Al Majaz, Al Khan, Muwaileh, Aljada and Al Zahia. Villas, apartments and offices. Free home visit.',
			'lead'      => 'Just across from our Deira showroom, we serve Sharjah homes and businesses with tailored curtains, blackout solutions and durable blinds.',
			'paras'     => array(
				'Sharjah apartments in Al Majaz, Al Khan and Al Nahda often face strong sun — sheers with blackout curtains and zebra blinds are popular choices.',
				'In new communities like Aljada, Al Zahia and Tilal City, we fit complete villas and townhouses with a coordinated look.',
			),
			'places'    => array( 'Al Majaz', 'Al Khan', 'Al Nahda (Sharjah)', 'Al Taawun', 'Muwaileh', 'Aljada', 'Al Zahia', 'University City' ),
			'popular'   => array( 'pinch-pleat-curtains', 'blackout-curtains', 'sheer-curtains', 'zebra-blinds', 'vertical-blinds', 'eyelet-curtains' ),
			'faqs'      => array(
				array( 'Do you offer free home visits in Sharjah?', 'Yes — measurement and samples at your home in Sharjah are free.' ),
				array( 'Can I visit your showroom from Sharjah?', 'Yes. Our showroom on Naif Road, Deira is a short drive from Sharjah.' ),
				array( 'Do you fit offices in Sharjah?', 'Yes, we supply vertical, sunscreen and branded blinds for offices and shops.' ),
			),
			'nearby'    => array( 'curtains-blinds-ajman', 'curtains-blinds-deira-bur-dubai', 'curtains-blinds-umm-al-quwain' ),
		),

		'curtains-blinds-ajman'                       => array(
			'name'      => 'Ajman',
			'lead_area' => 'Ajman',
			'region'    => 'uae',
			'img'       => array( 'eyelet-curtains', 2 ),
			'seo_title' => 'Curtains & Blinds Ajman | Made-to-Measure Curtains Shop',
			'seo_desc'  => 'Curtains and blinds in Ajman made to measure: Al Nuaimiya, Al Rashidiya, Al Jurf, Al Zorah and Al Yasmeen. Free home visit and installation.',
			'lead'      => 'Made-to-measure curtains and blinds for Ajman apartments, villas and businesses — from Corniche towers to Al Zorah and Al Yasmeen villas.',
			'paras'     => array(
				'Ajman apartments often have generous windows facing the sea or city. Sheers, eyelet curtains and blackout rollers are practical, good-value choices.',
				'For villas in Al Yasmeen, Al Rawda and Al Zorah we create coordinated curtains and blinds for every room.',
			),
			'places'    => array( 'Ajman Corniche', 'Al Nuaimiya', 'Al Rashidiya (Ajman)', 'Al Jurf', 'Al Zorah', 'Al Yasmeen', 'Al Rawda', 'Al Mowaihat' ),
			'popular'   => array( 'eyelet-curtains', 'sheer-curtains', 'blackout-roller-blinds', 'zebra-blinds', 'pinch-pleat-curtains', 'vertical-blinds' ),
			'faqs'      => array(
				array( 'Do you serve all of Ajman?', 'Yes, from the Corniche to Al Zorah, Al Yasmeen and Al Jurf.' ),
				array( 'How do I book a visit in Ajman?', 'Message us on WhatsApp with your location and preferred time.' ),
				array( 'Do you supply businesses in Ajman?', 'Yes — vertical, sunscreen and logo-printed blinds for shops and offices.' ),
			),
			'nearby'    => array( 'curtains-blinds-sharjah', 'curtains-blinds-umm-al-quwain', 'curtains-blinds-ras-al-khaimah' ),
		),

		'curtains-blinds-umm-al-quwain'               => array(
			'new'       => true,
			'title'     => 'Curtains &amp; Blinds in Umm Al Quwain',
			'name'      => 'Umm Al Quwain',
			'lead_area' => 'Umm Al Quwain',
			'region'    => 'uae',
			'img'       => array( 'bamboo-blinds', 1 ),
			'seo_title' => 'Curtains & Blinds Umm Al Quwain | Made to Measure',
			'seo_desc'  => 'Made-to-measure curtains and blinds in Umm Al Quwain for villas, apartments and businesses. Free home visit, measurement and professional installation.',
			'lead'      => 'Coastal villas, family homes and new communities in Umm Al Quwain — tailored curtains and blinds measured and installed by our team.',
			'paras'     => array(
				'Homes near the lagoon and coast enjoy bright light and sea air. Quality sheers, lined curtains and easy-care blinds keep rooms comfortable.',
				'For terraces and outdoor seating, bamboo blinds add shade and a natural look.',
			),
			'places'    => array( 'Umm Al Quwain city', 'Al Salamah', 'Al Raas', 'Al Ramlah', 'Umm Al Quwain Marina', 'Al Humrah' ),
			'popular'   => array( 'sheer-curtains', 'blackout-curtains', 'bamboo-blinds', 'eyelet-curtains', 'zebra-blinds', 'roman-blinds' ),
			'faqs'      => array(
				array( 'Do you install in Umm Al Quwain?', 'Yes. We plan visits to Umm Al Quwain for measurement and installation.' ),
				array( 'Can you shade an outdoor terrace?', 'Yes — bamboo blinds are ideal for terraces and pergolas.' ),
				array( 'How do I start?', 'Send window photos and your location on WhatsApp and we will arrange a visit.' ),
			),
			'nearby'    => array( 'curtains-blinds-ajman', 'curtains-blinds-ras-al-khaimah', 'curtains-blinds-sharjah' ),
		),

		'curtains-blinds-ras-al-khaimah'              => array(
			'name'      => 'Ras Al Khaimah',
			'lead_area' => 'Ras Al Khaimah',
			'region'    => 'uae',
			'img'       => array( 'wooden-blinds', 2 ),
			'seo_title' => 'Curtains & Blinds Ras Al Khaimah (RAK) | Free Visit',
			'seo_desc'  => 'Made-to-measure curtains and blinds in Ras Al Khaimah: Al Hamra, Mina Al Arab, Al Marjan Island and RAK city. Homes, hotels and offices. Free visit.',
			'lead'      => 'Beach villas, resort apartments and hotels in Ras Al Khaimah — we design, make and install curtains and blinds for homes and hospitality across RAK.',
			'paras'     => array(
				'Al Hamra, Mina Al Arab and Al Marjan Island homes enjoy sea views and bright light. Sheers with blackout layers and wooden or bamboo blinds suit the relaxed coastal style.',
				'We also supply hotels, serviced apartments and offices in RAK with durable blackout and sunscreen solutions.',
			),
			'places'    => array( 'Al Hamra Village', 'Mina Al Arab', 'Al Marjan Island', 'RAK City', 'Al Nakheel', 'Khuzam', 'Julphar' ),
			'popular'   => array( 'sheer-curtains', 'blackout-curtains', 'wooden-blinds', 'bamboo-blinds', 'wave-curtains', 'blackout-roller-blinds' ),
			'faqs'      => array(
				array( 'Do you work with hotels in Ras Al Khaimah?', 'Yes — blackout curtains, sheers and blinds for rooms and public areas.' ),
				array( 'How are visits to RAK arranged?', 'We schedule measurement and installation trips; message us to plan a date.' ),
				array( 'Do you cover Al Marjan Island?', 'Yes, along with Al Hamra, Mina Al Arab and RAK city.' ),
			),
			'nearby'    => array( 'curtains-blinds-umm-al-quwain', 'curtains-blinds-fujairah', 'curtains-blinds-ajman' ),
		),

		'curtains-blinds-fujairah'                    => array(
			'new'       => true,
			'title'     => 'Curtains &amp; Blinds in Fujairah',
			'name'      => 'Fujairah',
			'lead_area' => 'Fujairah',
			'region'    => 'uae',
			'img'       => array( 'roman-blinds', 2 ),
			'seo_title' => 'Curtains & Blinds Fujairah | Made to Measure & Installed',
			'seo_desc'  => 'Made-to-measure curtains and blinds in Fujairah, Dibba and the east coast for villas, apartments and resorts. Free home visit and installation.',
			'lead'      => 'East-coast homes, beach resorts and businesses in Fujairah and Dibba — made-to-measure curtains and blinds with professional installation.',
			'paras'     => array(
				'Fujairah’s mountain and sea views deserve window treatments that frame them: light sheers, blackout bedrooms and natural Roman or bamboo blinds.',
				'We plan visits to the east coast for homes, villas, resorts and offices — send window photos on WhatsApp to get started.',
			),
			'places'    => array( 'Fujairah city', 'Dibba Al Fujairah', 'Al Aqah', 'Kalba', 'Khor Fakkan', 'Masafi' ),
			'popular'   => array( 'sheer-curtains', 'blackout-curtains', 'roman-blinds', 'bamboo-blinds', 'wave-curtains', 'sunscreen-roller-blinds' ),
			'faqs'      => array(
				array( 'Do you install curtains in Fujairah?', 'Yes. We arrange measurement and installation visits to Fujairah and the east coast.' ),
				array( 'Do you supply resorts?', 'Yes — blackout curtains, sheers and blinds for rooms and public areas.' ),
				array( 'Can we choose fabrics remotely?', 'We can share catalogues online and bring samples to the visit.' ),
			),
			'nearby'    => array( 'curtains-blinds-ras-al-khaimah', 'curtains-blinds-sharjah', 'curtains-blinds-al-ain' ),
		),

		'curtains-blinds-al-ain'                      => array(
			'name'      => 'Al Ain',
			'lead_area' => 'Al Ain',
			'region'    => 'uae',
			'img'       => array( 'roman-curtains', 4 ),
			'seo_title' => 'Curtains & Blinds Al Ain | Made-to-Measure, Free Visit',
			'seo_desc'  => 'Made-to-measure curtains and blinds in Al Ain for villas, majlis, apartments and offices. Blackout, sheer, Roman and vertical blinds. Free visit.',
			'lead'      => 'Spacious villas, majlis and family homes in the Garden City. We design and install curtains and blinds for Al Ain homes and businesses.',
			'paras'     => array(
				'Al Ain villas often have large majlis and family rooms. Formal pinch pleat or American style curtains with sheers are favourite choices, with blackout in bedrooms.',
				'For offices, clinics and schools we supply vertical, sunscreen and hospital curtain systems.',
			),
			'places'    => array( 'Al Jimi', 'Al Muwaiji', 'Al Towayya', 'Hili', 'Zakher', 'Al Khabisi', 'Falaj Hazza' ),
			'popular'   => array( 'american-style-curtains', 'pinch-pleat-curtains', 'blackout-curtains', 'sheer-curtains', 'vertical-blinds', 'hospital-curtains' ),
			'faqs'      => array(
				array( 'Do you serve Al Ain?', 'Yes. We plan measurement and installation visits to Al Ain.' ),
				array( 'Can you make majlis curtains?', 'Yes — formal curtains with sheers, valances and tiebacks.' ),
				array( 'Do you supply clinics in Al Ain?', 'Yes, including hospital cubicle curtains and track systems.' ),
			),
			'nearby'    => array( 'curtains-blinds-abu-dhabi', 'curtains-blinds-fujairah', 'curtains-blinds-dubai-hills-arabian-ranches' ),
		),
	);
}
