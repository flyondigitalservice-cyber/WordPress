<?php
/**
 * Site map + starter content for all 45 pages.
 *
 * This data is used ONCE by the installer to create the pages. After that,
 * every word lives in the page itself and is edited in the block editor —
 * editing this file does not change existing pages (use "Rebuild page" in
 * Appearance → DGF Setup to regenerate a single page from here).
 *
 * @package DGF
 */

defined( 'ABSPATH' ) || exit;

/**
 * All pages, in menu order. Keys are slugs; "parent" is the parent slug.
 *
 * @return array<string,array>
 */
function dgf_pages_data() {
	$pages = array();

	/* ---------------------------------------------------------------------
	 * Core pages (12)
	 * ------------------------------------------------------------------- */
	$pages['home'] = array(
		'title'     => 'Home',
		'type'      => 'home',
		'hero'      => 'Gym flooring that takes the weight — built for Dubai',
		'excerpt'   => 'Rubber tiles, rolls, EPDM, sled-track turf and vinyl sports floors — supplied and installed for home gyms, commercial fitness centres, CrossFit boxes, hotels and schools across Dubai and all seven emirates.',
		'seo_title' => 'Gym Flooring Dubai | Rubber, Turf & EPDM Supply + Installation',
		'seo_desc'  => 'Gym flooring in Dubai & across the UAE: rubber tiles, rolls, EPDM, sled turf and vinyl. Free site survey & samples. Get a quote on WhatsApp in minutes.',
		'keyword'   => 'gym flooring dubai',
		'aliases'   => array( 'home', 'hero', 'banner', 'cover', 'main' ),
	);
	$pages['about'] = array(
		'title'     => 'About Us',
		'type'      => 'about',
		'excerpt'   => 'A specialist gym flooring team from Casa Vera Home — focused on one thing: floors that protect equipment, athletes and buildings.',
		'seo_title' => 'About Dubai Gym Flooring | A Casa Vera Home Company',
		'seo_desc'  => 'Dubai Gym Flooring is the fitness flooring division of Casa Vera Home. Meet the team that specifies, supplies and installs gym floors across the UAE.',
		'keyword'   => 'gym flooring company dubai',
		'aliases'   => array( 'about', 'team', 'showroom', 'office', 'warehouse' ),
	);
	$pages['gym-flooring-products'] = array(
		'title'     => 'Gym Flooring Products',
		'menu'      => 'Products',
		'type'      => 'hub',
		'hub_of'    => 'products',
		'excerpt'   => 'Every major gym flooring system in one place — compare rubber tiles, rolls, EPDM, turf, vinyl and specialist floors, then get a quote on WhatsApp.',
		'seo_title' => 'Gym Flooring Products in Dubai | Tiles, Rolls, Turf, Vinyl',
		'seo_desc'  => 'Browse gym flooring products available in Dubai & UAE: rubber tiles, rubber rolls, EPDM, sled turf, vinyl, PVC, deadlift platforms and acoustic underlay.',
		'keyword'   => 'gym flooring products dubai',
		'aliases'   => array( 'products', 'range', 'collection', 'all products' ),
	);
	$pages['services'] = array(
		'title'     => 'Services',
		'type'      => 'hub',
		'hub_of'    => 'services',
		'excerpt'   => 'From free site survey and samples to professional installation, repair and replacement — one team handles your gym floor end to end.',
		'seo_title' => 'Gym Flooring Services Dubai | Survey, Installation, Repair',
		'seo_desc'  => 'Gym flooring services across Dubai & the UAE: free site survey and samples, professional installation, repair and full floor replacement.',
		'keyword'   => 'gym flooring installation dubai',
		'aliases'   => array( 'services', 'service' ),
	);
	$pages['areas-we-serve'] = array(
		'title'     => 'Areas We Serve',
		'menu'      => 'Areas',
		'type'      => 'hub',
		'hub_of'    => 'locations',
		'excerpt'   => 'Supply and installation across Dubai’s communities and every emirate — Abu Dhabi, Sharjah, Ajman, Ras Al Khaimah, Fujairah, Umm Al Quwain and Al Ain.',
		'seo_title' => 'Gym Flooring Across Dubai & the UAE | Areas We Serve',
		'seo_desc'  => 'We supply and install gym flooring across Dubai (Marina, Downtown, Business Bay, JLT, Palm, Al Quoz, JVC) and all UAE emirates. Check your area.',
		'keyword'   => 'gym flooring uae',
		'aliases'   => array( 'areas', 'locations', 'map', 'uae' ),
	);
	$pages['catalogues'] = array(
		'title'     => 'Catalogues',
		'type'      => 'catalogues',
		'excerpt'   => 'Download our latest gym flooring catalogues and spec sheets as PDF — or ask on WhatsApp and we will send the right one for your project.',
		'seo_title' => 'Gym Flooring Catalogue PDF Download | Dubai Gym Flooring',
		'seo_desc'  => 'Download gym flooring catalogues and technical data sheets (PDF): rubber tiles, rolls, EPDM, turf and vinyl. Ask for prices on WhatsApp.',
		'keyword'   => 'gym flooring catalogue',
		'aliases'   => array( 'catalogue', 'catalog', 'brochure', 'pdf' ),
	);
	$pages['projects'] = array(
		'title'     => 'Projects',
		'type'      => 'projects',
		'excerpt'   => 'Recent gym flooring installations — home gyms, commercial fitness floors, CrossFit boxes, hotel gyms and outdoor training zones across the UAE.',
		'seo_title' => 'Gym Flooring Projects in Dubai & UAE | Our Work',
		'seo_desc'  => 'See recent gym flooring projects: home gyms, commercial gyms, CrossFit boxes and hotel fitness floors installed across Dubai and the UAE.',
		'keyword'   => 'gym flooring projects dubai',
		'aliases'   => array( 'projects', 'project', 'portfolio', 'gallery', 'installation photos', 'work' ),
	);
	$pages['faq'] = array(
		'title'     => 'FAQs',
		'type'      => 'faqs',
		'excerpt'   => 'Straight answers on thickness, prices, installation time, cleaning and which floor suits your training.',
		'seo_title' => 'Gym Flooring FAQs | Thickness, Price, Installation | Dubai',
		'seo_desc'  => 'Answers to common gym flooring questions in Dubai: what thickness you need, rubber vs EPDM, installation time, cleaning, noise and delivery across the UAE.',
		'keyword'   => 'gym flooring faq',
		'aliases'   => array( 'faq', 'faqs', 'questions' ),
	);
	$pages['contact'] = array(
		'title'     => 'Contact Us',
		'menu'      => 'Contact',
		'type'      => 'contact',
		'excerpt'   => 'The fastest way to reach us is WhatsApp. Send your room size and a photo — we reply with options, samples and a clear quote.',
		'seo_title' => 'Contact Dubai Gym Flooring | WhatsApp, Call, Visit',
		'seo_desc'  => 'Contact Dubai Gym Flooring on WhatsApp for a fast quote, free samples and a site survey anywhere in Dubai and the UAE.',
		'keyword'   => 'contact gym flooring dubai',
		'aliases'   => array( 'contact', 'contact us' ),
	);
	$pages['get-a-free-quote'] = array(
		'title'     => 'Get a Free Quote',
		'type'      => 'quote',
		'excerpt'   => 'Tell us about your space in under a minute. Your details go straight to our team on WhatsApp — no waiting for an email reply.',
		'seo_title' => 'Free Gym Flooring Quote Dubai | Reply on WhatsApp',
		'seo_desc'  => 'Request a free gym flooring quote in Dubai & the UAE. Share your area size and use — we reply on WhatsApp with options, samples and pricing.',
		'keyword'   => 'gym flooring quote dubai',
		'aliases'   => array( 'quote', 'free quote' ),
	);
	$pages['privacy-policy'] = array(
		'title'     => 'Privacy Policy',
		'type'      => 'legal',
		'legal'     => 'privacy',
		'excerpt'   => 'How we collect, use and protect the details you share with us.',
		'seo_title' => 'Privacy Policy | Dubai Gym Flooring',
		'seo_desc'  => 'How Dubai Gym Flooring collects, uses and protects personal information submitted through this website and WhatsApp.',
		'keyword'   => '',
		'noindex'   => true,
	);
	$pages['terms-and-conditions'] = array(
		'title'     => 'Terms & Conditions',
		'type'      => 'legal',
		'legal'     => 'terms',
		'excerpt'   => 'The terms that apply to quotes, orders, delivery and installation.',
		'seo_title' => 'Terms & Conditions | Dubai Gym Flooring',
		'seo_desc'  => 'Terms and conditions for quotes, orders, delivery and installation by Dubai Gym Flooring.',
		'keyword'   => '',
		'noindex'   => true,
	);

	/* ---------------------------------------------------------------------
	 * Products (15) — children of gym-flooring-products
	 * ------------------------------------------------------------------- */
	$products = array(
		'rubber-gym-flooring'          => array(
			'title'     => 'Rubber Gym Flooring',
			'excerpt'   => 'Dense, shock-absorbing rubber flooring that protects your equipment, your joints and the slab underneath — the default choice for serious gyms.',
			'seo_title' => 'Rubber Gym Flooring Dubai | Supply & Installation',
			'seo_desc'  => 'Rubber gym flooring in Dubai: shock-absorbing tiles and rolls for home and commercial gyms. Free samples & site survey. Quote on WhatsApp.',
			'keyword'   => 'rubber gym flooring dubai',
			'intro'     => array(
				'Rubber is the workhorse of gym flooring. It absorbs impact from dropped dumbbells and plates, reduces noise and vibration travelling into the building, and gives athletes a stable, slip-resistant surface whether they are lifting, running intervals or stretching.',
				'We supply rubber flooring as interlocking or square-edge tiles, rolls and speckled EPDM finishes, in the thickness that matches what you actually do in the room — from light cardio and functional training up to heavy Olympic lifting zones.',
			),
			'benefits'  => array(
				array( 'Impact protection', 'Cushions dropped weights so equipment, flooring and the concrete slab below are protected.' ),
				array( 'Noise & vibration control', 'Dense rubber dampens the thud of drops — important in apartments, offices and hotels.' ),
				array( 'Grip & comfort', 'A textured, slip-resistant surface that stays stable under shoes and bare feet.' ),
				array( 'Easy to maintain', 'Sweep or vacuum, then damp-mop with a neutral cleaner. No waxing or sealing needed.' ),
			),
			'specs'     => array(
				'Formats: tiles (typically 50 × 50 cm or 100 × 100 cm), rolls and custom-cut pieces',
				'Common thicknesses: 8 mm to 50 mm depending on the training zone',
				'Finishes: plain black, colour-fleck / speckled EPDM, and full-colour tops',
				'Installation: loose-lay, interlocking or fully bonded with adhesive',
				'Exact specifications per product are listed in our catalogues',
			),
			'uses'      => array( 'Home gyms and garage gyms', 'Commercial fitness centres', 'Free-weight and strength zones', 'Hotel and residential tower gyms', 'Physio and rehab studios' ),
			'faqs'      => array(
				array( 'What thickness of rubber flooring do I need?', 'For cardio and bodyweight areas 8–15 mm is usually enough. Free-weight areas typically use 15–30 mm, and heavy Olympic lifting or drop zones 30–50 mm. We confirm the right thickness after we know your equipment and the floor you are installing on.' ),
				array( 'Does new rubber flooring smell?', 'Some rubber has a mild smell for the first days after unpacking. Airing the room and damp-mopping speeds this up. Ask us for low-odour options if the gym is in a bedroom, office or enclosed space.' ),
				array( 'Can rubber flooring be installed over tiles or marble?', 'Yes. Rubber tiles and rolls can be laid over existing hard floors, which protects the original floor underneath. We check the level and condition of the surface during the site survey.' ),
			),
			'aliases'   => array( 'rubber', 'rubber flooring', 'rubber floor', 'black rubber' ),
		),
		'interlocking-rubber-gym-tiles' => array(
			'title'     => 'Interlocking Rubber Gym Tiles',
			'excerpt'   => 'Puzzle-edge rubber tiles that lock together without glue — fast to install, easy to lift and ideal for home gyms and rented spaces.',
			'seo_title' => 'Interlocking Rubber Gym Tiles Dubai | No-Glue Install',
			'seo_desc'  => 'Interlocking rubber gym tiles in Dubai: no-glue puzzle-edge tiles for home and commercial gyms. Easy install, replaceable tiles. Quote on WhatsApp.',
			'keyword'   => 'interlocking gym tiles dubai',
			'intro'     => array(
				'Interlocking tiles connect with a puzzle-style edge, so the floor stays tight and flat without adhesive. That makes them a favourite for apartments, villas and rented units where you may want to lift the floor later.',
				'Because each tile is separate, a worn or damaged tile can be swapped in minutes. Edge ramps and corner pieces give a clean, trip-free finish at doorways and open sides.',
			),
			'benefits'  => array(
				array( 'No glue needed', 'Tiles lock together, so installation is quick and the original floor is left untouched.' ),
				array( 'Replace single tiles', 'Damage in one spot? Swap one tile instead of the whole floor.' ),
				array( 'Portable', 'Lift and re-lay the floor if you move home or reconfigure the room.' ),
				array( 'Clean edges', 'Matching ramps and borders finish exposed edges neatly and safely.' ),
			),
			'specs'     => array(
				'Edge types: puzzle interlock, with optional ramp and corner pieces',
				'Typical thickness: 10 mm to 30 mm',
				'Colours: black and colour-fleck options',
				'Installation: loose-lay, interlocking — no adhesive required',
			),
			'uses'      => array( 'Apartment and villa home gyms', 'Rented units and temporary set-ups', 'Studios that change layouts', 'Garage gyms' ),
			'faqs'      => array(
				array( 'Do interlocking tiles move during workouts?', 'Properly fitted interlocking tiles stay in place under normal training. For heavy sled pushes or rigs we can bond the perimeter or recommend a heavier tile.' ),
				array( 'Can I install interlocking tiles myself?', 'Yes, many clients do. We can also install them for you so the layout, cuts around columns and edge ramps are finished professionally.' ),
				array( 'Are interlocking tiles good for heavy lifting?', 'For heavy drops we recommend thicker tiles or a dedicated deadlift platform in the lifting area, with interlocking tiles around it.' ),
			),
			'aliases'   => array( 'interlocking', 'interlock', 'puzzle', 'jigsaw', 'tiles', 'rubber tiles' ),
		),
		'rubber-flooring-rolls'        => array(
			'title'     => 'Rubber Flooring Rolls',
			'excerpt'   => 'Wide rolls of rubber that cover large gym floors with very few seams — a clean, continuous look for commercial and big home gyms.',
			'seo_title' => 'Rubber Flooring Rolls Dubai | Seamless Gym Floors',
			'seo_desc'  => 'Rubber flooring rolls for gyms in Dubai & UAE: fewer seams, fast coverage for large areas. Cut to size and installed. Get a quote on WhatsApp.',
			'keyword'   => 'rubber flooring rolls dubai',
			'intro'     => array(
				'Rolls are the fastest way to cover a large room with rubber. Fewer joints mean a cleaner, more professional look and fewer edges for dirt to collect in — which is why many commercial gyms choose rolls for cardio and functional areas.',
				'Rolls are cut on site to the shape of your room and can be loose-laid, taped or fully bonded. They combine well with thicker tiles or platforms in heavy lifting zones.',
			),
			'benefits'  => array(
				array( 'Fewer seams', 'A near-continuous surface that looks premium and is easy to clean.' ),
				array( 'Fast coverage', 'Ideal for large open floors, studios and cardio lines.' ),
				array( 'Cost-effective', 'Often the most economical way to cover a big area with rubber.' ),
				array( 'Custom fit', 'Cut precisely around columns, walls and fixed equipment.' ),
			),
			'specs'     => array(
				'Typical thickness: 4 mm to 15 mm',
				'Roll widths commonly around 1.2 m to 1.25 m; lengths cut to your room',
				'Finishes: black and speckled EPDM',
				'Installation: loose-lay, double-sided tape or full adhesive',
			),
			'uses'      => array( 'Commercial gym floors', 'Cardio and functional zones', 'Group-fitness studios', 'Large home gyms and basements' ),
			'faqs'      => array(
				array( 'Are rubber rolls glued down?', 'In most commercial gyms rolls are bonded with adhesive for a permanent, tight finish. In home gyms they can be loose-laid or taped at the seams.' ),
				array( 'Can rolls take heavy weights?', 'Thinner rolls suit cardio and functional training. For heavy free-weight areas we combine rolls with thicker tiles or lifting platforms.' ),
				array( 'How are rolls joined?', 'Rolls are butted tightly together and bonded or taped so the seam stays flat and closed.' ),
			),
			'aliases'   => array( 'roll', 'rolls', 'rubber roll', 'rubber rolls' ),
		),
		'epdm-rubber-flooring'         => array(
			'title'     => 'EPDM Rubber Flooring',
			'excerpt'   => 'Colour-fleck and full-colour EPDM rubber for gyms that want performance plus a designed look — indoor and UV-stable outdoor options.',
			'seo_title' => 'EPDM Rubber Flooring Dubai | Colour Gym & Outdoor Floors',
			'seo_desc'  => 'EPDM rubber flooring in Dubai: colour-fleck gym tiles and rolls, UV-stable outdoor EPDM for play and fitness areas. Supply & installation.',
			'keyword'   => 'epdm flooring dubai',
			'intro'     => array(
				'EPDM is a durable synthetic rubber that holds colour well, including under strong sun. Mixed as colour flecks into black rubber, it lifts the look of a gym floor and helps hide dust and chalk marks.',
				'For outdoor areas — rooftops, pool decks, playgrounds and outdoor training zones — EPDM surfaces are chosen for UV stability and comfort underfoot in the UAE climate.',
			),
			'benefits'  => array(
				array( 'Holds its colour', 'EPDM granules are UV-stable, so colours stay brighter for longer.' ),
				array( 'Designed look', 'Choose fleck percentages and colours to match your brand or interior.' ),
				array( 'Indoor & outdoor', 'Tiles and rolls indoors; poured-in-place or tile systems outdoors.' ),
				array( 'Hides marks', 'Speckled finishes disguise dust, chalk and scuffs between cleans.' ),
			),
			'specs'     => array(
				'Colour-fleck percentages commonly from about 10% up to full-colour tops',
				'Formats: tiles, rolls and poured-in-place surfaces',
				'Colours: grey, blue, red, green, beige and custom mixes (subject to availability)',
				'Outdoor systems built up to suit play and fitness use',
			),
			'uses'      => array( 'Branded commercial gyms', 'Hotel and residential gyms', 'Outdoor fitness areas', 'Kids’ play areas and rooftops' ),
			'faqs'      => array(
				array( 'What is the difference between EPDM and SBR rubber?', 'SBR is the black recycled rubber used in most gym floors. EPDM is a virgin synthetic rubber used for colour granules and outdoor surfaces because it is more UV and weather resistant.' ),
				array( 'Is EPDM suitable for outdoor use in the UAE?', 'Yes. EPDM is commonly used for outdoor fitness and play surfaces because it resists UV and heat better than standard black rubber.' ),
				array( 'Can I get EPDM in my brand colours?', 'We offer a wide range of standard colours and mixes. Share your brand colours on WhatsApp and we will suggest the closest options and samples.' ),
			),
			'aliases'   => array( 'epdm', 'speckled', 'fleck', 'colour fleck', 'color', 'colour' ),
		),
		'gym-turf-sled-track'          => array(
			'title'     => 'Gym Turf & Sled Track',
			'excerpt'   => 'Hard-wearing sports turf for sled pushes, prowler runs, agility and functional training — with printed lines, numbers and logos.',
			'seo_title' => 'Gym Turf & Sled Track Dubai | Functional Training Turf',
			'seo_desc'  => 'Gym turf and sled track in Dubai: dense, low-pile turf for sled pushes, prowler and agility work. Lines, numbers & logos available. Quote on WhatsApp.',
			'keyword'   => 'gym turf dubai',
			'intro'     => array(
				'Gym turf is a dense, low-pile artificial surface built to take sled pushes, prowler runs, lunges and agility drills. It gives a fast, consistent glide for sleds while staying grippy for athletes.',
				'Turf lanes can be finished with printed or inlaid lines, numbers, distance markers and your logo, turning the functional area into a signature feature of the gym.',
			),
			'benefits'  => array(
				array( 'Built for sleds', 'Dense, low-pile construction that stays even under heavy sled and prowler work.' ),
				array( 'Grip for athletes', 'Stable footing for sprints, lunges, bear crawls and agility drills.' ),
				array( 'Brandable', 'Add lane lines, yard or metre markers, numbers and logos.' ),
				array( 'Pairs with rubber', 'Install turf lanes alongside rubber zones for a complete functional floor.' ),
			),
			'specs'     => array(
				'Low, dense pile designed for sled traffic',
				'Common widths around 2 m, cut to lane length',
				'Options: with or without shock pad underneath',
				'Colours: green, black, grey and others; printed or inlaid markings',
			),
			'uses'      => array( 'Sled and prowler lanes', 'Functional and HIIT zones', 'Athletic performance centres', 'Home gyms with a turf strip' ),
			'faqs'      => array(
				array( 'Is gym turf the same as garden artificial grass?', 'No. Gym turf has a much shorter, denser pile designed for sleds and training, while landscape grass is longer and softer and would flatten and snag under sleds.' ),
				array( 'Can you add our logo to the turf?', 'Yes. Logos, lines and numbers can be printed or inlaid, subject to the turf chosen. Send your artwork on WhatsApp for a mock-up.' ),
				array( 'Does turf need a shock pad?', 'For sled lanes a pad is optional. In areas with jumping or drops, a shock pad under the turf adds comfort and protection.' ),
			),
			'aliases'   => array( 'turf', 'sled', 'sled track', 'grass', 'prowler', 'artificial grass' ),
		),
		'outdoor-gym-flooring'         => array(
			'title'     => 'Outdoor Gym Flooring',
			'excerpt'   => 'Weather-ready flooring for rooftops, terraces, pool decks and outdoor training zones — built for UAE sun, heat and humidity.',
			'seo_title' => 'Outdoor Gym Flooring Dubai | Rooftop & Terrace Fitness',
			'seo_desc'  => 'Outdoor gym flooring in Dubai & UAE: UV-stable EPDM, outdoor rubber tiles and sports turf for rooftops, terraces and outdoor fitness areas.',
			'keyword'   => 'outdoor gym flooring dubai',
			'intro'     => array(
				'Outdoor training areas face strong sun, high surface temperatures, humidity and occasional rain. The floor has to stay stable, drain well and keep its colour — while still protecting athletes and equipment.',
				'We specify UV-stable EPDM, outdoor-grade rubber tiles and sports turf for rooftops, terraces, villa gardens, hotel pool decks and community fitness zones.',
			),
			'benefits'  => array(
				array( 'UV & heat resistant', 'Materials selected for strong UAE sun and high surface temperatures.' ),
				array( 'Drains water', 'Permeable systems help water drain away after rain or washing down.' ),
				array( 'Safe underfoot', 'Cushioned surfaces for training, play and fall protection.' ),
				array( 'Low maintenance', 'Rinse, sweep or blow off sand and dust — no sealing required.' ),
			),
			'specs'     => array(
				'Systems: UV-stable EPDM (tiles or poured), outdoor rubber tiles, sports turf',
				'Suitable bases: concrete, screed and suitable roof build-ups (checked on survey)',
				'Colours: wide EPDM range; darker colours absorb more heat',
				'Drainage and falls reviewed during the site survey',
			),
			'uses'      => array( 'Rooftop and terrace gyms', 'Villa garden training areas', 'Hotel pool-side fitness zones', 'Community and school outdoor fitness' ),
			'faqs'      => array(
				array( 'Does outdoor rubber get hot in summer?', 'All outdoor surfaces heat up in direct UAE sun. Lighter EPDM colours and shade structures reduce surface temperature; we advise on both during the survey.' ),
				array( 'Can outdoor flooring go on a roof?', 'Often yes, but the roof’s waterproofing and drainage must be protected. We assess the build-up first and recommend a loose-lay or suitable system.' ),
				array( 'How do I clean outdoor gym flooring?', 'Sweep or use a leaf blower to remove sand, then rinse with water and a mild cleaner when needed.' ),
			),
			'aliases'   => array( 'outdoor', 'rooftop', 'terrace', 'garden', 'pool', 'outside' ),
		),
		'vinyl-sports-flooring'        => array(
			'title'     => 'Vinyl Sports Flooring',
			'excerpt'   => 'Cushioned vinyl sports floors for studios, multi-purpose halls, schools and group-fitness rooms — smooth, hygienic and easy to clean.',
			'seo_title' => 'Vinyl Sports Flooring Dubai | Studios, Schools & Halls',
			'seo_desc'  => 'Vinyl sports flooring in Dubai & UAE for studios, schools and multi-purpose halls. Cushioned, hygienic, easy to clean. Supply & installation.',
			'keyword'   => 'vinyl sports flooring dubai',
			'intro'     => array(
				'Vinyl sports flooring combines a hard-wearing, smooth top layer with a cushioned backing. It is the go-to choice where people move barefoot or in trainers: group-exercise studios, dance and aerobics rooms, school halls and multi-sport courts.',
				'The sealed surface is hygienic and quick to mop, and wood-look or solid-colour designs let the floor match the interior.',
			),
			'benefits'  => array(
				array( 'Comfort & shock absorption', 'Cushioned backing reduces strain for aerobics, dance and court sports.' ),
				array( 'Hygienic', 'A sealed, seam-welded surface that is easy to clean and disinfect.' ),
				array( 'Multi-purpose', 'Suits classes, court markings and school activities.' ),
				array( 'Design choice', 'Wood-look, solid colours and line markings available.' ),
			),
			'specs'     => array(
				'Formats: sheet vinyl on rolls, heat-welded seams',
				'Cushioned backings in various thicknesses for sports use',
				'Designs: wood-look, solid colours; line marking on request',
				'Installation: fully adhered on a prepared, level subfloor',
			),
			'uses'      => array( 'Group-fitness and aerobics studios', 'Dance and yoga rooms', 'School sports halls', 'Multi-purpose community halls' ),
			'faqs'      => array(
				array( 'Is vinyl suitable for weightlifting?', 'Vinyl is best for classes and court sports. For free weights, we recommend rubber zones or platforms in the lifting area.' ),
				array( 'Does vinyl need a perfectly flat floor?', 'Yes. Sheet vinyl is adhered to the subfloor, so levelling is often part of the job. We check this during the survey.' ),
				array( 'Can you add court lines?', 'Yes, court and zone lines can be marked after installation to suit your activities.' ),
			),
			'aliases'   => array( 'vinyl', 'sports vinyl', 'lvt', 'studio floor' ),
		),
		'pvc-gym-flooring'             => array(
			'title'     => 'PVC Gym Flooring',
			'excerpt'   => 'Interlocking PVC tiles for garages, multi-use rooms and light training areas — tough, water-resistant and quick to install.',
			'seo_title' => 'PVC Gym Flooring Dubai | Interlocking PVC Tiles',
			'seo_desc'  => 'PVC gym flooring in Dubai: interlocking PVC tiles for garage gyms, multi-use rooms and light training. Water-resistant, fast install. Get a quote.',
			'keyword'   => 'pvc gym flooring dubai',
			'intro'     => array(
				'Interlocking PVC tiles are rigid, water-resistant and easy to click together over most hard floors. They suit garages, multi-use rooms and areas that see light training, cardio machines and stretching.',
				'PVC is also a smart choice where you need a surface that resists oil, water and cleaning chemicals — such as garage gyms that double as parking or workshop space.',
			),
			'benefits'  => array(
				array( 'Water & chemical resistant', 'Handles spills, washing down and garage use.' ),
				array( 'Fast click install', 'Tiles interlock without adhesive over most hard floors.' ),
				array( 'Clean finish', 'Smooth or textured tops with matching edge ramps.' ),
				array( 'Great for garages', 'Doubles as a durable floor for parking and storage.' ),
			),
			'specs'     => array(
				'Interlocking tiles, typically 5 mm to 7 mm thick',
				'Textures: coin-top, diamond-plate and smooth',
				'Colours: black, grey and others; edge ramps available',
				'Installation: loose-lay interlocking, no glue',
			),
			'uses'      => array( 'Garage gyms', 'Multi-use rooms', 'Cardio and stretching areas', 'Workshops and storage areas' ),
			'faqs'      => array(
				array( 'Is PVC as shock-absorbing as rubber?', 'No. PVC is harder than rubber. It is ideal for cardio machines and light training; for dropped weights we add rubber mats or platforms.' ),
				array( 'Can cars drive on PVC gym tiles?', 'Many garage-grade PVC tiles are rated for car traffic. Tell us if the garage will also be used for parking and we will recommend the right grade.' ),
				array( 'Are PVC tiles slippery when wet?', 'Textured tops such as coin or diamond patterns improve grip. We recommend textured tiles for areas that may get wet.' ),
			),
			'aliases'   => array( 'pvc', 'garage', 'plastic tiles', 'garage flooring' ),
		),
		'crossfit-flooring'            => array(
			'title'     => 'CrossFit Flooring',
			'excerpt'   => 'Heavy-duty flooring for CrossFit boxes and functional gyms — built for barbell drops, box jumps, sleds and non-stop WOD traffic.',
			'seo_title' => 'CrossFit Flooring Dubai | Heavy-Duty Box Floors',
			'seo_desc'  => 'CrossFit flooring in Dubai & UAE: heavy-duty rubber tiles, lifting zones and sled turf for boxes and functional gyms. Quote on WhatsApp.',
			'keyword'   => 'crossfit flooring dubai',
			'intro'     => array(
				'A CrossFit box asks more of its floor than almost any other gym: repeated barbell drops, kettlebells, box jumps, rowers, sleds and heavy foot traffic all day long.',
				'We zone the floor — thick rubber for lifting, durable tiles for general WOD space and turf lanes for sleds — so every square metre is specified for what happens on it.',
			),
			'benefits'  => array(
				array( 'Built for drops', 'Thick, dense rubber that handles repeated barbell and kettlebell drops.' ),
				array( 'Zoned design', 'Lifting, WOD and sled zones specified separately for performance and budget.' ),
				array( 'Rig-friendly', 'Flooring detailed around rigs, anchors and storage.' ),
				array( 'Easy upkeep', 'Chalk and dust sweep off; tiles can be replaced individually.' ),
			),
			'specs'     => array(
				'Lifting zones: typically 30–50 mm rubber tiles or platforms',
				'General WOD areas: typically 15–30 mm tiles or rolls',
				'Sled lanes: dense gym turf',
				'Installation: loose-lay, interlocking or bonded depending on zone',
			),
			'uses'      => array( 'CrossFit affiliate boxes', 'Functional training gyms', 'Athletic performance centres', 'Military and security training rooms' ),
			'faqs'      => array(
				array( 'What floor is best for a CrossFit box?', 'Most boxes use thick rubber tiles in lifting areas, durable tiles or rolls in general WOD space and turf for sled lanes. We design the mix around your class layout.' ),
				array( 'Will the floor stop noise reaching neighbours?', 'Thick rubber reduces impact noise significantly. In sensitive buildings we add acoustic underlay or platforms in the drop zones.' ),
				array( 'Can you install around our rig?', 'Yes. We cut and detail the floor around rigs, anchors and wall fixings so everything sits flat and secure.' ),
			),
			'aliases'   => array( 'crossfit', 'box', 'functional', 'wod', 'hiit' ),
		),
		'deadlift-platforms'           => array(
			'title'     => 'Deadlift Platforms',
			'excerpt'   => 'Dedicated lifting platforms and drop pads that absorb heavy deadlifts and Olympic drops — protecting the floor and cutting noise.',
			'seo_title' => 'Deadlift Platforms Dubai | Lifting Platforms & Drop Pads',
			'seo_desc'  => 'Deadlift and weightlifting platforms in Dubai & UAE. Heavy rubber platforms and drop pads that protect floors and reduce noise. Get a quote.',
			'keyword'   => 'deadlift platform dubai',
			'intro'     => array(
				'A lifting platform concentrates protection where it matters most: the spot where a loaded barbell lands. Thick rubber absorbs the impact, reduces bounce and keeps noise and vibration out of the structure.',
				'Platforms can be built with a wooden or rubber centre strip for a stable stance, or supplied as heavy rubber drop pads for gyms that move equipment around.',
			),
			'benefits'  => array(
				array( 'Maximum protection', 'Thick rubber layers take the impact of heavy drops.' ),
				array( 'Stable stance', 'Optional centre strip gives a firm, flat surface for the lifter.' ),
				array( 'Noise reduction', 'Cuts impact noise and vibration — key in apartments and hotels.' ),
				array( 'Defined lifting area', 'Keeps heavy lifting in one controlled zone.' ),
			),
			'specs'     => array(
				'Common sizes around 2 × 2 m or 2.4 × 2.4 m (custom sizes available)',
				'Rubber thickness typically 30–50 mm, or layered systems',
				'Options: rubber-only, rubber with centre strip, portable drop pads',
				'Branding: logo or colour inserts on request',
			),
			'uses'      => array( 'Home gyms with barbells', 'Commercial strength areas', 'Olympic weightlifting clubs', 'Hotel and residential gyms' ),
			'faqs'      => array(
				array( 'Do I need a platform if I already have rubber tiles?', 'For heavy deadlifts and Olympic lifts a platform adds a thicker, dedicated drop zone that protects tiles and reduces noise further.' ),
				array( 'What size platform do I need?', 'A 2 × 2 m platform suits most lifters. Olympic weightlifting often uses larger platforms. We size it to your bar and space.' ),
				array( 'Can a platform be moved?', 'Modular platforms and drop pads can be lifted and relocated; bonded platforms are fixed in place.' ),
			),
			'aliases'   => array( 'deadlift', 'platform', 'lifting platform', 'drop pad', 'weightlifting' ),
		),
		'acoustic-gym-flooring'        => array(
			'title'     => 'Acoustic & Anti-Vibration Gym Flooring',
			'menu'      => 'Acoustic Gym Flooring',
			'excerpt'   => 'Acoustic underlays and anti-vibration systems that stop gym noise travelling to the floors below — for towers, hotels and offices.',
			'seo_title' => 'Acoustic Gym Flooring Dubai | Anti-Vibration Underlay',
			'seo_desc'  => 'Acoustic and anti-vibration gym flooring in Dubai: underlays and layered systems that reduce impact noise in towers, hotels and offices. Quote on WhatsApp.',
			'keyword'   => 'acoustic gym flooring dubai',
			'intro'     => array(
				'In high-rise buildings, the biggest complaint about gyms is noise and vibration travelling to the floors below. Dropped weights and treadmills send impact through the slab, where it is heard as thuds and felt as vibration.',
				'Acoustic gym flooring adds a resilient underlay or layered system beneath the finished surface, decoupling the gym floor from the structure so far less energy reaches neighbours.',
			),
			'benefits'  => array(
				array( 'Reduces impact noise', 'Resilient layers absorb the energy of drops and footfalls.' ),
				array( 'Cuts vibration', 'Helps stop machine and treadmill vibration spreading through the slab.' ),
				array( 'Building-friendly', 'Supports approval from building management and neighbours.' ),
				array( 'Works with any finish', 'Combine with rubber, vinyl or turf on top.' ),
			),
			'specs'     => array(
				'Systems: acoustic underlays, layered rubber and floating platforms',
				'Build-up height depends on the performance required',
				'Suitable for residential, hotel and commercial gyms',
				'Performance depends on slab and building — assessed on survey',
			),
			'uses'      => array( 'Gyms in residential towers', 'Hotel gyms above guest rooms', 'Office and corporate gyms', 'Home gyms on upper floors' ),
			'faqs'      => array(
				array( 'Will acoustic flooring make my gym completely silent?', 'No floor removes all noise, but a well-designed acoustic system reduces impact noise and vibration considerably. We recommend the build-up based on your building and use.' ),
				array( 'How thick is an acoustic gym floor?', 'It depends on the system, from a few millimetres of underlay up to layered platforms in heavy drop zones. We check door clearances and thresholds during the survey.' ),
				array( 'Is acoustic flooring needed for a home gym?', 'If you live in an apartment or train on an upper floor with heavy weights, an acoustic layer is strongly recommended.' ),
			),
			'aliases'   => array( 'acoustic', 'underlay', 'anti vibration', 'anti-vibration', 'noise', 'soundproof', 'sound' ),
		),
		'home-gym-flooring'            => array(
			'title'     => 'Home Gym Flooring',
			'excerpt'   => 'Floors for villa, apartment and garage gyms — protect your marble or parquet, keep the neighbours happy and make the room feel like a real gym.',
			'seo_title' => 'Home Gym Flooring Dubai | Villas, Apartments & Garages',
			'seo_desc'  => 'Home gym flooring in Dubai: rubber tiles, platforms, turf and acoustic underlay for villas, apartments and garages. Free samples. Quote on WhatsApp.',
			'keyword'   => 'home gym flooring dubai',
			'intro'     => array(
				'A home gym floor has two jobs: protect the house and make training feel good. Rubber tiles and platforms stop weights cracking marble or denting parquet, and a cushioned surface makes every session more comfortable.',
				'We help you choose the right mix for your room — villa gym, spare bedroom, garage, basement or apartment — and install it cleanly with finished edges and thresholds.',
			),
			'benefits'  => array(
				array( 'Protects your floor', 'Keeps marble, porcelain and parquet safe from dropped weights.' ),
				array( 'Quieter training', 'Reduces thuds and vibration — ideal for apartments and upper floors.' ),
				array( 'Looks finished', 'Neat edges, colour options and lifting platforms that look built-in.' ),
				array( 'Removable options', 'Interlocking and loose-lay systems that come with you when you move.' ),
			),
			'specs'     => array(
				'Popular: 15–20 mm rubber tiles for general home training',
				'Barbell users: add a deadlift platform or 30 mm+ drop zone',
				'Apartments: acoustic underlay recommended for heavy weights',
				'Garages: rubber tiles or PVC tiles depending on use',
			),
			'uses'      => array( 'Villa gyms', 'Apartment spare rooms', 'Garage gyms', 'Basements and rooftops' ),
			'faqs'      => array(
				array( 'What is the best flooring for a home gym in Dubai?', 'For most home gyms, 15–20 mm rubber tiles are the best all-rounder. If you lift heavy with a barbell, add a platform; in apartments add an acoustic layer.' ),
				array( 'Will rubber tiles damage my marble floor?', 'No. Quality rubber tiles protect marble and porcelain. We recommend keeping the floor clean and dry underneath and lifting tiles occasionally if the room is humid.' ),
				array( 'How long does a home gym installation take?', 'Most home gym floors are installed within a day once materials are on site. Larger or bonded floors may take longer.' ),
			),
			'aliases'   => array( 'home', 'home gym', 'villa', 'apartment', 'residential', 'garage gym' ),
		),
		'commercial-gym-flooring'      => array(
			'title'     => 'Commercial Gym Flooring',
			'excerpt'   => 'Complete floor packages for fitness clubs, boutique studios, hotels, corporate and residential tower gyms — zoned, durable and on brand.',
			'seo_title' => 'Commercial Gym Flooring Dubai | Clubs, Hotels & Offices',
			'seo_desc'  => 'Commercial gym flooring in Dubai & UAE: zoned rubber, turf, vinyl and acoustic systems for fitness clubs, hotels and corporate gyms. Get a quote.',
			'keyword'   => 'commercial gym flooring dubai',
			'intro'     => array(
				'Commercial gyms need floors that look good on opening day and still perform after years of heavy use. We plan the floor by zone — strength, cardio, functional, studio and changing areas — and specify the right system for each.',
				'We work with owners, fit-out contractors and facility managers on programme, building approvals, logistics and handover so the floor is ready when you open.',
			),
			'benefits'  => array(
				array( 'Zoned specification', 'The right system for strength, cardio, functional and studio areas.' ),
				array( 'Brand integration', 'Colours, inlays and turf logos that match your identity.' ),
				array( 'Fit-out coordination', 'We work to your contractor’s programme and building rules.' ),
				array( 'Long-term value', 'Durable systems with replaceable tiles in high-wear zones.' ),
			),
			'specs'     => array(
				'Strength: thick rubber tiles, platforms and acoustic build-ups',
				'Cardio & functional: rolls or tiles, turf lanes',
				'Studios: vinyl sports flooring',
				'Documentation: product data sheets available for approvals',
			),
			'uses'      => array( 'Fitness clubs and franchises', 'Boutique studios', 'Hotel gyms', 'Corporate and residential tower gyms' ),
			'faqs'      => array(
				array( 'Do you work with fit-out contractors?', 'Yes. We regularly work alongside fit-out and MEP contractors and can supply technical data sheets and samples for approvals.' ),
				array( 'Can you install outside business hours?', 'For operating venues we can plan phased or out-of-hours installation to reduce disruption, subject to building rules.' ),
				array( 'Do you offer maintenance or replacement later?', 'Yes. We can replace worn tiles, re-bond seams and refresh high-wear zones as part of an ongoing relationship.' ),
			),
			'aliases'   => array( 'commercial', 'club', 'fitness centre', 'fitness center', 'hotel gym', 'corporate' ),
		),
		'kids-play-area-flooring'      => array(
			'title'     => 'Kids Play Area Flooring',
			'excerpt'   => 'Soft, colourful safety surfaces for playrooms, nurseries, schools and outdoor playgrounds — designed to cushion falls.',
			'seo_title' => 'Kids Play Area Flooring Dubai | Soft Safety Surfaces',
			'seo_desc'  => 'Kids play area flooring in Dubai & UAE: soft rubber and EPDM safety surfaces for playrooms, nurseries, schools and playgrounds. Quote on WhatsApp.',
			'keyword'   => 'play area flooring dubai',
			'intro'     => array(
				'Children run, climb, tumble and fall — the right floor cushions those moments. Soft rubber tiles and EPDM surfaces reduce the impact of falls and create bright, inviting play spaces.',
				'For outdoor playgrounds the surfacing thickness is chosen to suit the height of the play equipment. Indoors, softer tiles and mats create safe playrooms and nursery spaces.',
			),
			'benefits'  => array(
				array( 'Cushions falls', 'Impact-absorbing surfaces chosen to suit play equipment height.' ),
				array( 'Bright colours', 'EPDM colours and patterns that make spaces fun.' ),
				array( 'Hygienic', 'Easy to clean and suitable for daily use in nurseries and schools.' ),
				array( 'Indoor & outdoor', 'Soft tiles indoors, UV-stable EPDM outdoors.' ),
			),
			'specs'     => array(
				'Outdoor: EPDM wet-pour or safety tiles',
				'Indoor: soft rubber or foam tiles',
				'Thickness selected according to equipment fall height',
				'Colours and simple graphics available',
			),
			'uses'      => array( 'Nurseries and schools', 'Villa playrooms and gardens', 'Community playgrounds', 'Hotel and mall kids’ zones' ),
			'faqs'      => array(
				array( 'How thick should playground flooring be?', 'It depends on the fall height of the play equipment. Higher equipment needs thicker surfacing; we confirm this from the equipment specification.' ),
				array( 'Is EPDM safe for children?', 'EPDM is widely used for playgrounds because it is durable, cushioned and colour-stable outdoors.' ),
				array( 'Can you add games or patterns?', 'Yes, simple games, shapes and colour zones can be built into EPDM surfaces.' ),
			),
			'aliases'   => array( 'kids', 'play', 'playground', 'nursery', 'children', 'soft play', 'wet pour' ),
		),
		'yoga-pilates-studio-flooring' => array(
			'title'     => 'Yoga & Pilates Studio Flooring',
			'menu'      => 'Yoga & Pilates Flooring',
			'excerpt'   => 'Calm, comfortable floors for yoga, Pilates and stretching studios — warm underfoot, quiet and easy to keep spotless.',
			'seo_title' => 'Yoga & Pilates Studio Flooring Dubai | Studio Floors',
			'seo_desc'  => 'Yoga and Pilates studio flooring in Dubai: cushioned vinyl, rubber and cork-look options that are quiet, comfortable and easy to clean.',
			'keyword'   => 'yoga studio flooring dubai',
			'intro'     => array(
				'Yoga, Pilates and stretching studios need a floor that feels good barefoot, stays quiet and looks calm. Cushioned vinyl and fine-grain rubber give comfort and grip without the heavy-gym look.',
				'For reformer Pilates studios, the floor must also support equipment without marking, while staying easy to clean between classes.',
			),
			'benefits'  => array(
				array( 'Comfortable barefoot', 'Warm, cushioned surfaces for mat work and stretching.' ),
				array( 'Quiet', 'Reduces footfall and equipment noise in studios.' ),
				array( 'Calm aesthetic', 'Wood-look and soft-colour options that suit wellness interiors.' ),
				array( 'Hygienic', 'Sealed surfaces that are quick to wipe down between classes.' ),
			),
			'specs'     => array(
				'Options: cushioned sports vinyl, fine-grain rubber',
				'Wood-look and neutral colour designs',
				'Suitable for mat, reformer and barre studios',
				'Fully adhered on a prepared, level subfloor',
			),
			'uses'      => array( 'Yoga studios', 'Reformer Pilates studios', 'Stretching and recovery rooms', 'Wellness spaces in hotels and villas' ),
			'faqs'      => array(
				array( 'What is the best floor for a Pilates reformer studio?', 'A cushioned vinyl or fine-grain rubber that supports equipment without marking works well. We check the load and footprint of your reformers.' ),
				array( 'Is rubber too hard for yoga?', 'Fine-grain rubber is comfortable, and most students use mats on top. Cushioned vinyl gives a softer, warmer feel.' ),
				array( 'Can the studio floor look like wood?', 'Yes, wood-look sports vinyl gives a warm studio look with better cushioning and hygiene than real timber.' ),
			),
			'aliases'   => array( 'yoga', 'pilates', 'studio', 'stretching', 'wellness', 'reformer' ),
		),
	);
	$order = 1;
	foreach ( $products as $slug => $product ) {
		$pages[ $slug ] = array_merge(
			array(
				'parent' => 'gym-flooring-products',
				'type'   => 'product',
				'order'  => $order++,
			),
			$product
		);
	}

	/* ---------------------------------------------------------------------
	 * Services (3) — children of services
	 * ------------------------------------------------------------------- */
	$services = array(
		'gym-flooring-installation'    => array(
			'title'     => 'Gym Flooring Installation',
			'excerpt'   => 'Professional installation by a specialist gym flooring crew — measured, prepared, laid and finished with clean edges and thresholds.',
			'seo_title' => 'Gym Flooring Installation Dubai | Professional Fitters',
			'seo_desc'  => 'Professional gym flooring installation in Dubai & UAE: survey, subfloor preparation, laying, bonding and finishing. Get a quote on WhatsApp.',
			'keyword'   => 'gym flooring installation dubai',
			'intro'     => array(
				'A great product can still fail if it is installed badly. Our crews set out the layout, prepare the subfloor, cut accurately around columns and equipment, and finish with ramps and thresholds that look intentional.',
				'We coordinate with building management for access, service lifts and working hours — and leave the site clean and ready for your equipment.',
			),
			'steps'     => array(
				array( 'Survey & measure', 'We check the subfloor, levels, access and measure every zone.' ),
				array( 'Specify & quote', 'You receive a clear quote with the recommended system for each area.' ),
				array( 'Prepare', 'Cleaning, levelling or repairs to the subfloor where needed.' ),
				array( 'Install & finish', 'Laying, bonding, cutting and edge finishing — then a final walkthrough.' ),
			),
			'faqs'      => array(
				array( 'Do you install flooring bought elsewhere?', 'Contact us with the product details. Where the material is suitable and in good condition, we can often install it.' ),
				array( 'Do you handle building permits and NOCs?', 'We provide the documents building management usually asks for (method statement, product data sheets, insurance where required) and work to their rules.' ),
				array( 'How much notice do you need?', 'Share your target date on WhatsApp. Stocked materials can be scheduled quickly; special colours or large projects need more lead time.' ),
			),
			'aliases'   => array( 'installation', 'install', 'fitting', 'fitters', 'installer' ),
		),
		'gym-floor-repair-replacement' => array(
			'title'     => 'Gym Floor Repair & Replacement',
			'excerpt'   => 'Worn, lifting, cracked or smelly gym floor? We repair seams and tiles, or strip and replace the floor with minimal downtime.',
			'seo_title' => 'Gym Floor Repair & Replacement Dubai | Fast Turnaround',
			'seo_desc'  => 'Gym floor repair and replacement in Dubai & UAE: re-bond lifting seams, replace damaged tiles or strip and renew the whole floor. Quote on WhatsApp.',
			'keyword'   => 'gym floor repair dubai',
			'intro'     => array(
				'Gym floors wear out in predictable places: under racks, at the dumbbell rack, along sled lanes and at entrances. Lifting seams and damaged tiles are trip hazards and make a gym look tired.',
				'We assess the floor, repair what can be saved and replace what cannot — often zone by zone so the gym can keep operating.',
			),
			'steps'     => array(
				array( 'Assess', 'Photos on WhatsApp or a site visit to see what is failing and why.' ),
				array( 'Repair', 'Re-bond seams, replace individual tiles, patch or re-line turf.' ),
				array( 'Replace', 'Strip old flooring, prepare the slab and install the new system.' ),
				array( 'Prevent', 'Upgrade high-wear zones so the problem does not come back.' ),
			),
			'faqs'      => array(
				array( 'Can you match my existing tiles?', 'We try to match thickness, colour and edge profile. If an exact match is not available, we may suggest replacing a full zone for a consistent look.' ),
				array( 'Can the gym stay open during repairs?', 'Often yes. We can work zone by zone or out of hours so members can keep training.' ),
				array( 'What do you do with the old flooring?', 'We remove and dispose of old flooring as part of replacement jobs, in line with site rules.' ),
			),
			'aliases'   => array( 'repair', 'replacement', 'replace', 'refurbish', 'renovation', 'before after' ),
		),
		'free-site-survey-samples'     => array(
			'title'     => 'Free Site Survey & Samples',
			'excerpt'   => 'See and feel the flooring before you decide. We bring samples, measure your space and recommend the right system — free.',
			'seo_title' => 'Free Gym Flooring Site Survey & Samples | Dubai & UAE',
			'seo_desc'  => 'Book a free gym flooring site survey in Dubai & the UAE. We measure, check your subfloor and bring samples of rubber, turf, EPDM and vinyl.',
			'keyword'   => 'gym flooring samples dubai',
			'intro'     => array(
				'Choosing a gym floor from a photo is hard. Our site survey puts real samples in your hands, in your room, under your lighting — so you can compare thickness, texture and colour properly.',
				'At the same visit we measure, check the subfloor and access, and talk through your training so the quote reflects exactly what you need.',
			),
			'steps'     => array(
				array( 'Message us', 'Send your location and a few photos on WhatsApp.' ),
				array( 'Book a slot', 'We agree a convenient time for the visit.' ),
				array( 'Survey', 'We measure, check the subfloor and show samples on site.' ),
				array( 'Quote', 'You receive a clear quote and recommendation on WhatsApp.' ),
			),
			'faqs'      => array(
				array( 'Is the site survey really free?', 'Yes, the survey and samples are free for gym flooring projects in our service areas. For remote locations we may first review photos and measurements on WhatsApp.' ),
				array( 'Can I just get samples delivered?', 'Yes. Tell us which products you are considering and your location, and we will arrange samples.' ),
				array( 'What should I prepare for the survey?', 'Clear access to the room, an idea of the equipment you will use and, for towers, any building rules for contractors.' ),
			),
			'aliases'   => array( 'survey', 'samples', 'sample', 'site visit', 'measurement' ),
		),
	);
	$order = 1;
	foreach ( $services as $slug => $service ) {
		$pages[ $slug ] = array_merge(
			array(
				'parent' => 'services',
				'type'   => 'service',
				'order'  => $order++,
			),
			$service
		);
	}

	/* ---------------------------------------------------------------------
	 * Locations (15) — children of areas-we-serve: 8 Dubai + 7 UAE
	 * ------------------------------------------------------------------- */
	$locations = array(
		'gym-flooring-dubai'           => array(
			'place'       => 'Dubai',
			'emirate'     => 'Dubai',
			'title'       => 'Gym Flooring in Dubai',
			'excerpt'     => 'Rubber, EPDM, turf and vinyl gym floors supplied and installed across every Dubai community — from Marina towers to Al Quoz warehouses.',
			'seo_title'   => 'Gym Flooring Across Dubai | Every Community Covered',
			'seo_desc'    => 'Gym flooring installers across Dubai: Marina, Downtown, Business Bay, JLT, Palm, Al Quoz, JVC, Mirdif & more. Free survey. Quote on WhatsApp.',
			'keyword'     => 'gym flooring installers dubai',
			'intro'       => array(
				'Dubai has one of the world’s most active fitness scenes — from building gyms in residential towers to boutique studios, hotel fitness floors, CrossFit boxes in Al Quoz and private villa gyms. Each needs a floor that suits the building as much as the training.',
				'We supply and install gym flooring across the whole city, handle building-management requirements in towers and communities, and match every zone to the right system.',
			),
			'communities' => array( 'Dubai Marina', 'Downtown Dubai', 'Business Bay', 'Jumeirah Lakes Towers (JLT)', 'Palm Jumeirah', 'Al Quoz', 'Jumeirah Village Circle (JVC)', 'Dubai Hills Estate', 'Arabian Ranches', 'Al Barsha', 'Jumeirah', 'Mirdif', 'Dubai Silicon Oasis', 'Deira & Bur Dubai' ),
			'projects'    => array( 'Residential tower and community gyms', 'Villa and apartment home gyms', 'Hotel fitness centres', 'CrossFit boxes and functional gyms', 'Corporate and office gyms', 'School sports rooms and outdoor areas' ),
			'note'        => 'Inside Dubai we can usually visit, measure and bring samples to your site. Many towers ask for a contractor NOC and service-lift booking — we prepare the documents building management typically requests.',
			'faqs'        => array(
				array( 'Do you cover all areas of Dubai?', 'Yes. We work across Dubai, from the Marina and Palm to Deira, Mirdif, Dubai Silicon Oasis and Dubai South.' ),
				array( 'Can you work with my building’s management rules?', 'Yes. We work to building rules on access, working hours and service-lift bookings, and provide the documents they usually request.' ),
			),
		),
		'gym-flooring-dubai-marina'    => array(
			'place'       => 'Dubai Marina',
			'emirate'     => 'Dubai',
			'title'       => 'Gym Flooring in Dubai Marina',
			'excerpt'     => 'Quiet, protective gym floors for Dubai Marina apartments, tower gyms and waterfront studios — with acoustic options for high-rise living.',
			'seo_title'   => 'Gym Flooring Dubai Marina | Apartments & Tower Gyms',
			'seo_desc'    => 'Gym flooring in Dubai Marina: rubber tiles, deadlift platforms and acoustic underlay for apartments, tower gyms and studios. Quote on WhatsApp.',
			'keyword'     => 'gym flooring dubai marina',
			'intro'       => array(
				'Dubai Marina is high-rise living, which makes noise the number-one issue for home and building gyms. A dropped dumbbell on a hard floor can be heard several floors down.',
				'For Marina apartments and tower gyms we typically combine rubber tiles with acoustic underlay or lifting platforms, so residents can train hard without complaints from neighbours.',
			),
			'communities' => array( 'Marina Walk towers', 'Marina Promenade', 'Dubai Marina Mall area', 'JBR (Jumeirah Beach Residence)', 'Emaar 6 Towers', 'Marina Gate' ),
			'projects'    => array( 'Apartment home gyms', 'Tower residents’ gyms', 'Boutique studios', 'Penthouse and terrace gyms' ),
			'note'        => 'Most Marina towers require a contractor NOC and a service-lift booking. We plan delivery and installation around these rules.',
			'faqs'        => array(
				array( 'How can I reduce gym noise in my Marina apartment?', 'Use thick rubber tiles with an acoustic underlay, and a deadlift platform if you lift heavy. This greatly reduces impact noise reaching the flat below.' ),
				array( 'Do you install in JBR as well?', 'Yes, we cover JBR and the wider Marina area.' ),
			),
		),
		'gym-flooring-downtown-dubai'  => array(
			'place'       => 'Downtown Dubai',
			'emirate'     => 'Dubai',
			'title'       => 'Gym Flooring in Downtown Dubai',
			'excerpt'     => 'Premium gym floors for Downtown Dubai residences, hotel fitness centres and boutique studios — finished to match luxury interiors.',
			'seo_title'   => 'Gym Flooring Downtown Dubai | Premium Home & Hotel Gyms',
			'seo_desc'    => 'Gym flooring in Downtown Dubai for luxury apartments, hotel gyms and boutique studios. EPDM colours, platforms & acoustic systems. Quote on WhatsApp.',
			'keyword'     => 'gym flooring downtown dubai',
			'intro'       => array(
				'Downtown Dubai’s residences, hotels and studios expect a floor that performs like a pro gym and looks like part of a luxury interior. Colour-fleck EPDM, clean edge details and custom-sized platforms help the gym feel designed, not added.',
				'In occupied buildings we plan quiet, tidy installation windows and protect lifts, corridors and finishes along the delivery route.',
			),
			'communities' => array( 'Burj Khalifa district', 'Opera District', 'Boulevard residences', 'Old Town', 'Burj Views', 'Downtown hotels' ),
			'projects'    => array( 'Luxury apartment gyms', 'Hotel fitness centres', 'Boutique and personal-training studios', 'Residents’ gyms' ),
			'note'        => 'Delivery and working hours in Downtown are often restricted — we schedule around building rules and can arrange out-of-hours work where permitted.',
			'faqs'        => array(
				array( 'Can the gym floor match our interior design?', 'Yes. EPDM fleck colours, full-colour tops and wood-look vinyl for studios let the floor complement the interior.' ),
				array( 'Can you work outside business hours in Downtown?', 'Where the building allows it, we can schedule evening or phased installation to reduce disruption.' ),
			),
		),
		'gym-flooring-business-bay'    => array(
			'place'       => 'Business Bay',
			'emirate'     => 'Dubai',
			'title'       => 'Gym Flooring in Business Bay',
			'excerpt'     => 'Gym floors for Business Bay office wellness rooms, residential tower gyms and canal-side studios — delivered to fit-out programmes.',
			'seo_title'   => 'Gym Flooring Business Bay | Office, Tower & Studio Gyms',
			'seo_desc'    => 'Gym flooring in Business Bay: corporate wellness rooms, residential tower gyms and studios. Rubber, vinyl & acoustic systems. Quote on WhatsApp.',
			'keyword'     => 'gym flooring business bay',
			'intro'       => array(
				'Business Bay mixes office towers, hotels and residential buildings along the Dubai Canal. Corporate wellness rooms, residents’ gyms and boutique studios all need floors that are durable, quiet and quick to install.',
				'We work with fit-out contractors and facility managers here, providing data sheets for approvals and installing to the project programme.',
			),
			'communities' => array( 'Dubai Canal front', 'Bay Square', 'Executive Towers', 'Marasi Drive', 'Business Bay hotels', 'Al Abraj Street' ),
			'projects'    => array( 'Corporate wellness gyms', 'Residential tower gyms', 'Hotel fitness rooms', 'Studios and PT spaces' ),
			'note'        => 'For office and fit-out projects we can supply product data sheets and samples for consultant approval before ordering.',
			'faqs'        => array(
				array( 'Do you work on office fit-out projects?', 'Yes. We regularly coordinate with fit-out contractors and provide the technical documents needed for approvals.' ),
				array( 'What floor suits a small office gym?', 'Rubber tiles for weights and cardio, with a vinyl or turf area if you run classes or functional training.' ),
			),
		),
		'gym-flooring-jlt'             => array(
			'place'       => 'Jumeirah Lakes Towers (JLT)',
			'emirate'     => 'Dubai',
			'title'       => 'Gym Flooring in JLT',
			'excerpt'     => 'Durable floors for JLT’s boutique gyms, functional studios and cluster-tower home gyms — built for heavy use and neighbour-friendly noise levels.',
			'seo_title'   => 'Gym Flooring JLT Dubai | Studios & Home Gyms',
			'seo_desc'    => 'Gym flooring in JLT (Jumeirah Lakes Towers): functional studio floors, sled turf and quiet home-gym flooring for cluster towers. Quote on WhatsApp.',
			'keyword'     => 'gym flooring jlt',
			'intro'       => array(
				'JLT’s clusters host a lively mix of boutique gyms, functional training studios and apartments. Many studios sit in mixed-use towers, so floors must handle intense classes while keeping noise and vibration under control.',
				'We combine heavy-duty rubber, turf lanes and acoustic layers to give JLT studios a floor that performs and keeps building management on side.',
			),
			'communities' => array( 'JLT clusters A–Z', 'Lake-side podium units', 'Cluster residential towers', 'Almas Tower area', 'JLT metro stations area' ),
			'projects'    => array( 'Functional and HIIT studios', 'Boutique gyms in podium units', 'Apartment home gyms', 'Office wellness rooms' ),
			'note'        => 'JLT towers often have specific delivery bays and time windows. We plan logistics with the cluster’s management.',
			'faqs'        => array(
				array( 'What flooring suits a JLT functional studio?', 'Heavy-duty rubber for the lifting and WOD area, turf for sled work and an acoustic layer if the studio is above other units.' ),
				array( 'Can you install in a podium unit that is still under fit-out?', 'Yes, we coordinate with your fit-out contractor so the floor goes in at the right stage.' ),
			),
		),
		'gym-flooring-palm-jumeirah'   => array(
			'place'       => 'Palm Jumeirah',
			'emirate'     => 'Dubai',
			'title'       => 'Gym Flooring in Palm Jumeirah',
			'excerpt'     => 'Villa gyms, hotel fitness floors and outdoor training decks on Palm Jumeirah — indoor rubber and UV-stable outdoor systems.',
			'seo_title'   => 'Gym Flooring Palm Jumeirah | Villa, Hotel & Outdoor Gyms',
			'seo_desc'    => 'Gym flooring on Palm Jumeirah for villas, hotels and outdoor training decks. Rubber, EPDM and turf built for sun and sea air. Quote on WhatsApp.',
			'keyword'     => 'gym flooring palm jumeirah',
			'intro'       => array(
				'Palm Jumeirah combines private villas, beach resorts and apartment buildings — and many residents want to train outdoors as well as in. That means floors for air-conditioned home gyms and for sunny terraces, gardens and pool decks.',
				'Indoors we fit rubber tiles and platforms that protect marble; outdoors we use UV-stable EPDM and turf chosen for heat, sun and humid sea air.',
			),
			'communities' => array( 'Palm Fronds villas', 'Shoreline Apartments', 'Golden Mile', 'Palm crescent resorts', 'Palm Tower & West Beach' ),
			'projects'    => array( 'Villa home gyms', 'Outdoor garden and terrace training areas', 'Resort and hotel gyms', 'Apartment building gyms' ),
			'note'        => 'Villa communities on the Palm usually require access approval for contractors — share your community details and we will prepare accordingly.',
			'faqs'        => array(
				array( 'Can I have an outdoor gym in my Palm villa garden?', 'Yes. UV-stable EPDM or outdoor rubber tiles on a suitable base make a durable outdoor training area.' ),
				array( 'Will outdoor flooring cope with sea air and humidity?', 'We select outdoor-grade materials for UAE coastal conditions and advise on drainage and shade.' ),
			),
		),
		'gym-flooring-al-quoz'         => array(
			'place'       => 'Al Quoz',
			'emirate'     => 'Dubai',
			'title'       => 'Gym Flooring in Al Quoz',
			'excerpt'     => 'Large-format, heavy-duty floors for Al Quoz warehouse gyms, CrossFit boxes and performance centres.',
			'seo_title'   => 'Gym Flooring Al Quoz | Warehouse & CrossFit Gym Floors',
			'seo_desc'    => 'Gym flooring in Al Quoz, Dubai: heavy-duty rubber, platforms and sled turf for warehouse gyms, CrossFit boxes and performance centres.',
			'keyword'     => 'gym flooring al quoz',
			'intro'       => array(
				'Al Quoz’s warehouses have become home to many of Dubai’s CrossFit boxes, strength gyms and performance centres. High ceilings and big open floors are perfect for training — but concrete slabs need serious protection.',
				'We install large-area rubber rolls, thick lifting tiles, platforms and long sled lanes, often in big single phases so the gym can open quickly.',
			),
			'communities' => array( 'Al Quoz Industrial Areas 1–4', 'Alserkal Avenue area', 'Al Quoz creative district', 'Sheikh Zayed Road service roads' ),
			'projects'    => array( 'CrossFit boxes', 'Strength and conditioning gyms', 'Sports performance centres', 'Martial-arts and boxing gyms' ),
			'note'        => 'Warehouse slabs can be uneven or dusty — we check levels and prepare the slab before installation so the floor sits flat.',
			'faqs'        => array(
				array( 'What floor do you recommend for a warehouse gym?', 'Thick rubber in lifting zones, rolls or tiles in general areas and turf for sleds. We zone the floor around your class layout.' ),
				array( 'Can you cover very large areas?', 'Yes. We plan large projects in phases and use rolls where they make sense to speed up installation and reduce seams.' ),
			),
		),
		'gym-flooring-jvc'             => array(
			'place'       => 'Jumeirah Village Circle (JVC)',
			'emirate'     => 'Dubai',
			'title'       => 'Gym Flooring in JVC',
			'excerpt'     => 'Home gym floors for JVC townhouses, villas and apartments — plus community and building gyms.',
			'seo_title'   => 'Gym Flooring JVC Dubai | Townhouse & Apartment Gyms',
			'seo_desc'    => 'Gym flooring in JVC (Jumeirah Village Circle): rubber tiles, platforms and PVC tiles for townhouses, villas, apartments and building gyms.',
			'keyword'     => 'gym flooring jvc',
			'intro'       => array(
				'JVC is full of families and young professionals turning spare rooms, garages and townhouse ground floors into home gyms. The goal: protect the floor, keep noise down and make the room feel like a real training space.',
				'We also fit floors for JVC building gyms and small studios, where durable rubber and neat finishing matter most.',
			),
			'communities' => array( 'JVC districts 10–18', 'JVC townhouse clusters', 'Circle Mall area', 'Apartment buildings across JVC' ),
			'projects'    => array( 'Townhouse and villa home gyms', 'Garage gyms', 'Apartment gyms', 'Building and community gyms' ),
			'note'        => 'Many JVC buildings require a contractor NOC — we can provide the documents your building manager asks for.',
			'faqs'        => array(
				array( 'What is the best garage gym floor for a JVC townhouse?', 'Rubber tiles if the space is dedicated to training, or heavy-duty PVC tiles if you also park or store items there.' ),
				array( 'Can you fit a small apartment gym?', 'Yes — even a few square metres of rubber tiles and a platform make a big difference in comfort and noise.' ),
			),
		),
		'gym-flooring-abu-dhabi'       => array(
			'place'       => 'Abu Dhabi',
			'emirate'     => 'Abu Dhabi',
			'title'       => 'Gym Flooring in Abu Dhabi',
			'excerpt'     => 'Gym flooring supply and installation across Abu Dhabi — from Saadiyat and Yas villas to Reem Island towers and commercial fitness centres.',
			'seo_title'   => 'Gym Flooring Abu Dhabi | Supply & Installation',
			'seo_desc'    => 'Gym flooring in Abu Dhabi: rubber tiles, rolls, EPDM, sled turf and vinyl for home, commercial, hotel and school gyms. Quote on WhatsApp.',
			'keyword'     => 'gym flooring abu dhabi',
			'intro'       => array(
				'Abu Dhabi’s fitness market ranges from villa gyms on Saadiyat and Yas Island to high-rise gyms on Al Reem Island, hotel fitness centres and school sports facilities.',
				'We deliver and install across the capital, planning site visits and installation days to make the trip from Dubai efficient for your project.',
			),
			'communities' => array( 'Al Reem Island', 'Saadiyat Island', 'Yas Island', 'Khalifa City', 'Al Raha Beach', 'Mohammed Bin Zayed City', 'Corniche & downtown Abu Dhabi' ),
			'projects'    => array( 'Villa home gyms', 'Residential tower gyms', 'Hotel and resort gyms', 'Commercial fitness centres', 'School and university sports rooms' ),
			'note'        => 'For Abu Dhabi projects we often start with photos and measurements on WhatsApp, then plan a combined survey and sample visit.',
			'faqs'        => array(
				array( 'Do you deliver gym flooring to Abu Dhabi?', 'Yes. We supply and install across Abu Dhabi city and the islands, including Reem, Saadiyat and Yas.' ),
				array( 'Can you survey my Abu Dhabi site?', 'Yes. Send photos and your location first; we then schedule a survey visit with samples where needed.' ),
			),
		),
		'gym-flooring-sharjah'         => array(
			'place'       => 'Sharjah',
			'emirate'     => 'Sharjah',
			'title'       => 'Gym Flooring in Sharjah',
			'excerpt'     => 'Gym floors for Sharjah homes, schools, community gyms and commercial fitness centres — supplied and installed.',
			'seo_title'   => 'Gym Flooring Sharjah | Home, School & Commercial Gyms',
			'seo_desc'    => 'Gym flooring in Sharjah: rubber tiles, rolls, PVC and vinyl sports floors for homes, schools and commercial gyms. Quote on WhatsApp.',
			'keyword'     => 'gym flooring sharjah',
			'intro'       => array(
				'Sharjah’s large family communities, schools and universities create strong demand for home gyms, school sports rooms and neighbourhood fitness centres.',
				'Being close to Dubai, Sharjah projects are easy for us to survey and install — from a single villa gym to a full school sports hall.',
			),
			'communities' => array( 'Al Nahda', 'Al Majaz', 'Al Khan', 'Muwaileh', 'Aljada', 'University City', 'Al Zahia' ),
			'projects'    => array( 'Villa and apartment home gyms', 'School sports halls and fitness rooms', 'Community and ladies’ gyms', 'Commercial fitness centres' ),
			'note'        => 'Sharjah is a short drive from Dubai, so survey visits with samples are straightforward to arrange.',
			'faqs'        => array(
				array( 'Do you install school sports flooring in Sharjah?', 'Yes. We supply vinyl sports flooring for halls and rubber flooring for fitness rooms in schools and universities.' ),
				array( 'Can you visit my home in Sharjah with samples?', 'Yes, we arrange survey visits with samples across Sharjah.' ),
			),
		),
		'gym-flooring-ajman'           => array(
			'place'       => 'Ajman',
			'emirate'     => 'Ajman',
			'title'       => 'Gym Flooring in Ajman',
			'excerpt'     => 'Value-focused, durable gym flooring for Ajman homes, gyms and residential towers.',
			'seo_title'   => 'Gym Flooring Ajman | Rubber Tiles & Gym Floors',
			'seo_desc'    => 'Gym flooring in Ajman for home gyms, commercial gyms and residential towers. Durable rubber tiles, rolls and PVC. Get a quote on WhatsApp.',
			'keyword'     => 'gym flooring ajman',
			'intro'       => array(
				'Ajman’s growing residential towers and villa communities have seen a steady rise in home gyms and neighbourhood fitness centres. Owners want durable floors that deliver real value.',
				'We help Ajman clients pick the right thickness and format so the budget goes into the zones that need it most.',
			),
			'communities' => array( 'Al Nuaimiya', 'Al Rashidiya', 'Al Jurf', 'Ajman Corniche', 'Al Zorah', 'Emirates City' ),
			'projects'    => array( 'Home gyms', 'Neighbourhood gyms', 'Residential tower gyms', 'Martial-arts and boxing gyms' ),
			'note'        => 'Ajman sits right next to Sharjah — we combine visits in the northern emirates to keep scheduling quick.',
			'faqs'        => array(
				array( 'Do you supply gym flooring in Ajman?', 'Yes, we supply and install across Ajman, including Al Nuaimiya, Al Jurf and Al Zorah.' ),
				array( 'How can I keep my gym flooring budget down?', 'Use thicker rubber only where weights are dropped and more economical rolls or tiles elsewhere — we will show you the options.' ),
			),
		),
		'gym-flooring-ras-al-khaimah'  => array(
			'place'       => 'Ras Al Khaimah',
			'emirate'     => 'Ras Al Khaimah',
			'title'       => 'Gym Flooring in Ras Al Khaimah',
			'excerpt'     => 'Gym floors for Ras Al Khaimah resorts, villas and community gyms — indoor rubber and outdoor-ready systems.',
			'seo_title'   => 'Gym Flooring Ras Al Khaimah | Resort, Villa & Gym Floors',
			'seo_desc'    => 'Gym flooring in Ras Al Khaimah for resorts, villas and commercial gyms. Rubber, EPDM and turf, indoor and outdoor. Quote on WhatsApp.',
			'keyword'     => 'gym flooring ras al khaimah',
			'intro'       => array(
				'Ras Al Khaimah’s resort coastline, Al Hamra and Marjan Island developments and growing villa communities all include gyms — often with outdoor training areas as well.',
				'We supply and install indoor rubber and vinyl floors and outdoor EPDM and turf systems across RAK.',
			),
			'communities' => array( 'Al Hamra Village', 'Marjan Island', 'Mina Al Arab', 'Al Nakheel', 'Julphar', 'RAK City' ),
			'projects'    => array( 'Resort and hotel gyms', 'Villa home gyms', 'Outdoor fitness areas', 'Community gyms' ),
			'note'        => 'For RAK projects we typically review photos and measurements first, then plan a single efficient visit for survey and installation.',
			'faqs'        => array(
				array( 'Do you install hotel gym flooring in Ras Al Khaimah?', 'Yes. We work with resorts and hotels on gym floors, including acoustic build-ups where gyms sit near guest rooms.' ),
				array( 'Can you do outdoor fitness flooring in RAK?', 'Yes — UV-stable EPDM and sports turf for outdoor training areas.' ),
			),
		),
		'gym-flooring-fujairah'        => array(
			'place'       => 'Fujairah',
			'emirate'     => 'Fujairah',
			'title'       => 'Gym Flooring in Fujairah',
			'excerpt'     => 'Gym flooring for Fujairah’s east-coast hotels, homes and community facilities — built for humidity and heavy use.',
			'seo_title'   => 'Gym Flooring Fujairah | East Coast Gym Floors',
			'seo_desc'    => 'Gym flooring in Fujairah and the east coast: rubber tiles, EPDM and turf for hotels, homes and community gyms. Get a quote on WhatsApp.',
			'keyword'     => 'gym flooring fujairah',
			'intro'       => array(
				'Fujairah and the east coast — including Dibba and the resort strip — have hotels, villas and community facilities that need gym floors able to handle coastal humidity.',
				'We specify materials that suit the environment and plan deliveries across the Hajar mountains efficiently.',
			),
			'communities' => array( 'Fujairah City', 'Dibba Al Fujairah', 'Al Aqah', 'Kalba (east coast)', 'Mirbah' ),
			'projects'    => array( 'Resort and hotel gyms', 'Villa home gyms', 'Community and government facilities', 'Outdoor fitness areas' ),
			'note'        => 'East-coast projects are planned around a combined visit — share photos and measurements on WhatsApp to start.',
			'faqs'        => array(
				array( 'Do you deliver to Fujairah and Dibba?', 'Yes, we supply and install across Fujairah city and the east coast.' ),
				array( 'Which floors cope best with humidity?', 'Rubber, EPDM and vinyl all perform well when installed correctly; we advise on ventilation and subfloor moisture during the survey.' ),
			),
		),
		'gym-flooring-umm-al-quwain'   => array(
			'place'       => 'Umm Al Quwain',
			'emirate'     => 'Umm Al Quwain',
			'title'       => 'Gym Flooring in Umm Al Quwain',
			'excerpt'     => 'Gym floors for Umm Al Quwain homes, community gyms and new waterfront developments.',
			'seo_title'   => 'Gym Flooring Umm Al Quwain | Home & Community Gyms',
			'seo_desc'    => 'Gym flooring in Umm Al Quwain for home gyms, community gyms and new developments. Rubber tiles, rolls and PVC. Get a quote on WhatsApp.',
			'keyword'     => 'gym flooring umm al quwain',
			'intro'       => array(
				'Umm Al Quwain is growing, with new waterfront and residential developments alongside established neighbourhoods. Home gyms and community fitness rooms are a natural part of that growth.',
				'We supply and install practical, durable gym flooring across UAQ and combine visits with Ajman and Sharjah for quick scheduling.',
			),
			'communities' => array( 'UAQ City', 'Al Salamah', 'Al Raas', 'Umm Al Quwain Marina', 'Al Humrah' ),
			'projects'    => array( 'Home gyms', 'Community gyms', 'School fitness rooms', 'New-build residential gyms' ),
			'note'        => 'UAQ visits are usually combined with nearby Ajman and Sharjah projects.',
			'faqs'        => array(
				array( 'Do you install gym flooring in Umm Al Quwain?', 'Yes, across UAQ city and surrounding communities.' ),
				array( 'Can I order flooring without installation?', 'Yes. We can supply materials with guidance on laying interlocking tiles yourself.' ),
			),
		),
		'gym-flooring-al-ain'          => array(
			'place'       => 'Al Ain',
			'emirate'     => 'Abu Dhabi',
			'title'       => 'Gym Flooring in Al Ain',
			'excerpt'     => 'Gym flooring for Al Ain homes, universities, schools and fitness centres — indoor systems and heat-ready outdoor surfaces.',
			'seo_title'   => 'Gym Flooring Al Ain | Home, University & Commercial Gyms',
			'seo_desc'    => 'Gym flooring in Al Ain for homes, universities, schools and commercial gyms. Rubber, vinyl, EPDM & turf, indoor and outdoor. Quote on WhatsApp.',
			'keyword'     => 'gym flooring al ain',
			'intro'       => array(
				'Al Ain — the Garden City — has a strong community sports culture, large family homes and major education campuses. Gyms here range from villa home gyms to university and school sports facilities.',
				'Inland Al Ain gets very hot in summer, so outdoor surfaces are specified for heat and UV, while indoor floors focus on durability and comfort.',
			),
			'communities' => array( 'Al Jimi', 'Al Muwaiji', 'Zakher', 'Al Towayya', 'Al Hili', 'University areas' ),
			'projects'    => array( 'Villa home gyms', 'University and school sports rooms', 'Commercial fitness centres', 'Outdoor fitness areas' ),
			'note'        => 'Al Ain projects are planned with an initial WhatsApp review of photos and measurements, followed by a scheduled visit.',
			'faqs'        => array(
				array( 'Do you supply gym flooring to Al Ain?', 'Yes, we supply and install across Al Ain for homes, schools, universities and gyms.' ),
				array( 'What outdoor floor handles Al Ain’s summer heat?', 'UV-stable EPDM in lighter colours, ideally with shade, performs best. We advise on build-up and drainage.' ),
			),
		),
	);
	$order = 1;
	foreach ( $locations as $slug => $location ) {
		$pages[ $slug ] = array_merge(
			array(
				'parent'  => 'areas-we-serve',
				'type'    => 'location',
				'order'   => $order++,
				'aliases' => array( strtolower( $location['place'] ), str_replace( 'gym-flooring-', '', $slug ) ),
			),
			$location
		);
	}

	return array_merge( $pages, dgf_interiors_data() );
}

/**
 * Full page path (parent/child) for a slug.
 *
 * @param string $slug Slug.
 * @return string
 */
function dgf_page_path( $slug ) {
	$pages = dgf_pages_data();
	if ( ! isset( $pages[ $slug ] ) ) {
		return $slug;
	}
	$parent = isset( $pages[ $slug ]['parent'] ) ? $pages[ $slug ]['parent'] : '';
	return $parent ? $parent . '/' . $slug : $slug;
}
