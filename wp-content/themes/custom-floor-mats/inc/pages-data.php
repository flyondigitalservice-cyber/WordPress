<?php
/**
 * Site map + starter content for every page.
 *
 * This data is used ONCE by the installer to create the pages. After that,
 * every word lives in the page itself and is edited in the block editor —
 * editing this file does not change existing pages (use "Rebuild page" in
 * Appearance → CFM Setup to regenerate a single page from here).
 *
 * @package CFM
 */

defined( 'ABSPATH' ) || exit;

/**
 * All pages, in menu order. Keys are slugs; "parent" is the parent slug.
 *
 * @return array<string,array>
 */
function cfm_pages_data() {
	$pages = array();

	/* ---------------------------------------------------------------------
	 * Core pages (12)
	 * ------------------------------------------------------------------- */
	$pages['home'] = array(
		'title'     => 'Home',
		'type'      => 'home',
		'hero'      => 'Custom floor mats that make the first impression for you',
		'excerpt'   => 'Logo mats, entrance systems, anti-fatigue, rubber and car mats — designed, made to size and delivered for villas, offices, hotels, malls, restaurants and gyms across Dubai and all seven emirates.',
		'seo_title' => 'Custom Floor Mats Dubai | Logo, Entrance & Anti-Fatigue Mats',
		'seo_desc'  => 'Custom floor mats in Dubai & UAE: printed logo mats, entrance & scraper mats, anti-fatigue, rubber gym and car mats. Free logo mockup. Quote on WhatsApp.',
		'keyword'   => 'custom floor mats dubai',
		'aliases'   => array( 'home', 'hero', 'banner', 'cover', 'main' ),
	);
	$pages['about'] = array(
		'title'     => 'About Us',
		'type'      => 'about',
		'excerpt'   => 'A specialist matting team from Casa Vera Home — focused on mats that welcome guests, carry your brand and keep floors clean and safe.',
		'seo_title' => 'About Custom Floor Mats UAE | A Casa Vera Home Company',
		'seo_desc'  => 'Custom Floor Mats is the matting division of Casa Vera Home. Meet the team that designs, makes and installs logo, entrance and safety mats across the UAE.',
		'keyword'   => 'floor mats company dubai',
		'aliases'   => array( 'about', 'team', 'showroom', 'office', 'warehouse', 'factory' ),
	);
	$pages['floor-mats'] = array(
		'title'     => 'Floor Mats',
		'menu'      => 'Mats',
		'type'      => 'hub',
		'hub_of'    => 'products',
		'excerpt'   => 'Every kind of floor mat in one place — compare logo, entrance, scraper, coir, anti-fatigue, rubber, wet-area and car mats, then get a quote on WhatsApp.',
		'seo_title' => 'Floor Mats in Dubai | Logo, Entrance, Rubber & Car Mats',
		'seo_desc'  => 'Browse floor mats made to size in Dubai & UAE: custom logo mats, entrance & scraper mats, coir doormats, anti-fatigue, rubber gym, wet-area and car mats.',
		'keyword'   => 'floor mats dubai',
		'aliases'   => array( 'products', 'range', 'collection', 'all products', 'mats', 'mat' ),
	);
	$pages['services'] = array(
		'title'     => 'Services',
		'type'      => 'hub',
		'hub_of'    => 'services',
		'excerpt'   => 'From a free logo mockup and samples to measuring, recessed-well installation and bulk corporate orders — one team handles your mats end to end.',
		'seo_title' => 'Floor Mat Services Dubai | Design, Measuring, Installation',
		'seo_desc'  => 'Floor mat services across Dubai & UAE: free logo mockups and samples, site measuring, recessed entrance installation and corporate bulk orders.',
		'keyword'   => 'custom mat services dubai',
		'aliases'   => array( 'services', 'service' ),
	);
	$pages['areas-we-serve'] = array(
		'title'     => 'Areas We Serve',
		'menu'      => 'Areas',
		'type'      => 'hub',
		'hub_of'    => 'locations',
		'excerpt'   => 'Delivery and installation across Dubai’s communities and every emirate — Abu Dhabi, Sharjah, Ajman, Ras Al Khaimah, Fujairah, Umm Al Quwain and Al Ain.',
		'seo_title' => 'Custom Floor Mats Across Dubai & the UAE | Areas We Serve',
		'seo_desc'  => 'We deliver and install custom floor mats across Dubai (Marina, Downtown, Business Bay, JLT, Palm, Al Quoz, JVC) and all UAE emirates. Check your area.',
		'keyword'   => 'floor mats uae',
		'aliases'   => array( 'areas', 'locations', 'map', 'uae' ),
	);
	$pages['catalogues'] = array(
		'title'     => 'Catalogues',
		'type'      => 'catalogues',
		'excerpt'   => 'View or download our latest floor mat catalogues and spec sheets as PDF — or ask on WhatsApp and we will send the right one for your project.',
		'seo_title' => 'Floor Mat Catalogue PDF | View & Download | Custom Floor Mats',
		'seo_desc'  => 'View and download floor mat catalogues (PDF): logo mats, entrance matting, rubber and gym mats, anti-fatigue and more. Ask for prices on WhatsApp.',
		'keyword'   => 'floor mats catalogue',
		'aliases'   => array( 'catalogue', 'catalog', 'brochure', 'pdf' ),
	);
	$pages['projects'] = array(
		'title'     => 'Projects',
		'type'      => 'projects',
		'excerpt'   => 'Recent mats we have made and installed — branded entrances, hotel lobbies, restaurant kitchens, gyms, offices and villas across the UAE.',
		'seo_title' => 'Floor Mat Projects in Dubai & UAE | Our Work',
		'seo_desc'  => 'See recent custom floor mat projects: logo mats, recessed entrance systems, hotel lift mats, kitchen and gym mats installed across Dubai and the UAE.',
		'keyword'   => 'logo mat projects dubai',
		'aliases'   => array( 'projects', 'project', 'portfolio', 'gallery', 'installation photos', 'work' ),
	);
	$pages['faq'] = array(
		'title'     => 'FAQs',
		'type'      => 'faqs',
		'excerpt'   => 'Straight answers on sizes, logo printing, lead times, cleaning and which mat suits your entrance.',
		'seo_title' => 'Floor Mat FAQs | Sizes, Logo Printing, Delivery | Dubai',
		'seo_desc'  => 'Answers to common floor mat questions in Dubai: logo mat printing, sizes, lead times, indoor vs outdoor mats, cleaning and delivery across the UAE.',
		'keyword'   => 'floor mats faq',
		'aliases'   => array( 'faq', 'faqs', 'questions' ),
	);
	$pages['contact'] = array(
		'title'     => 'Contact Us',
		'menu'      => 'Contact',
		'type'      => 'contact',
		'excerpt'   => 'The fastest way to reach us is WhatsApp. Send your logo, size and a photo of the entrance — we reply with a mockup and a clear quote.',
		'seo_title' => 'Contact Custom Floor Mats UAE | WhatsApp, Call, Visit',
		'seo_desc'  => 'Contact Custom Floor Mats on WhatsApp for a fast quote, free logo mockup and samples anywhere in Dubai and the UAE.',
		'keyword'   => 'contact floor mats dubai',
		'aliases'   => array( 'contact', 'contact us' ),
	);
	$pages['get-a-free-quote'] = array(
		'title'     => 'Get a Free Quote',
		'type'      => 'quote',
		'excerpt'   => 'Tell us about your mat in under a minute. Your details go straight to our team on WhatsApp — no waiting for an email reply.',
		'seo_title' => 'Free Floor Mat Quote Dubai | Logo Mockup on WhatsApp',
		'seo_desc'  => 'Request a free custom floor mat quote in Dubai & the UAE. Share your size and logo — we reply on WhatsApp with a mockup, options and pricing.',
		'keyword'   => 'floor mat quote dubai',
		'aliases'   => array( 'quote', 'free quote' ),
	);
	$pages['privacy-policy'] = array(
		'title'     => 'Privacy Policy',
		'type'      => 'legal',
		'legal'     => 'privacy',
		'excerpt'   => 'How we collect, use and protect the details you share with us.',
		'seo_title' => 'Privacy Policy | Custom Floor Mats UAE',
		'seo_desc'  => 'How Custom Floor Mats collects, uses and protects personal information submitted through this website and WhatsApp.',
		'keyword'   => '',
		'noindex'   => true,
	);
	$pages['terms-and-conditions'] = array(
		'title'     => 'Terms & Conditions',
		'type'      => 'legal',
		'legal'     => 'terms',
		'excerpt'   => 'The terms that apply to quotes, orders, production, delivery and installation.',
		'seo_title' => 'Terms & Conditions | Custom Floor Mats UAE',
		'seo_desc'  => 'Terms and conditions for quotes, orders, production, delivery and installation by Custom Floor Mats.',
		'keyword'   => '',
		'noindex'   => true,
	);

	/* ---------------------------------------------------------------------
	 * Products (12) — children of floor-mats
	 * ------------------------------------------------------------------- */
	$products = array(
		'custom-logo-mats'           => array(
			'title'     => 'Custom Logo Mats',
			'excerpt'   => 'Your logo, colours and message on a hard-wearing entrance mat — the first thing every guest, client and customer steps on.',
			'seo_title' => 'Custom Logo Mats Dubai | Printed & Inlaid Branded Mats',
			'seo_desc'  => 'Custom logo mats in Dubai & UAE: high-definition printed and inlaid branded entrance mats in any size. Free digital mockup. Get a quote on WhatsApp.',
			'keyword'   => 'logo mats dubai',
			'intro'     => array(
				'A logo mat turns a plain doorway into a branded welcome. We reproduce your logo, colours and tagline on a dense nylon pile with a non-slip rubber backing, so it looks sharp on day one and keeps trapping dust and moisture for years.',
				'Choose high-definition printing for photo-quality detail and gradients, or inlaid (cut-and-fit) construction for bold logos that will never fade. Every mat is made to your exact size and shape — rectangles, rounds, half-moons and custom outlines.',
			),
			'benefits'  => array(
				array( 'Brand at the door', 'Your logo greets every visitor — perfect for showrooms, offices, hotels, restaurants and retail.' ),
				array( 'Free digital mockup', 'See your logo on the mat, in your colours and size, before anything is produced.' ),
				array( 'Built to work', 'Absorbent nylon pile traps dust and water, protecting the marble or wood floor behind it.' ),
				array( 'Safe & flat', 'Rubber backing and bevelled edges keep the mat in place and trip-free.' ),
			),
			'specs'     => array(
				'Constructions: high-definition printed nylon, inlaid / cut-and-fit, and logo scraper mats',
				'Any size from about 60 × 90 cm up to wall-to-wall entrance runs; rounds and custom shapes on request',
				'Backing: nitrile rubber with bevelled edges; recessed versions for matwells',
				'Colours: printed in full colour (Pantone-matched where possible)',
				'Indoor, covered-outdoor and full-outdoor options',
			),
			'uses'      => array( 'Office and showroom entrances', 'Hotel, restaurant and café doors', 'Retail stores and malls', 'Villas and private residences', 'Events, launches and exhibitions' ),
			'faqs'      => array(
				array( 'What do you need from me to make a logo mat?', 'Your logo (vector AI, EPS, PDF or SVG is best — a clear PNG or JPG also works), the size you want and a photo of the entrance. We send back a free digital mockup on WhatsApp.' ),
				array( 'Will the logo fade or wash out?', 'Our printed logo mats use dye-injection printing that goes deep into the pile, so colours stay sharp with normal vacuuming and washing. For the longest life outdoors, inlaid or rubber-moulded logos are the best choice.' ),
				array( 'How long does a custom logo mat take?', 'After you approve the mockup, standard logo mats are usually ready in around 7–14 working days. Tell us your deadline on WhatsApp and we will confirm the fastest option.' ),
			),
			'aliases'   => array( 'logo', 'logo mat', 'logo mats', 'branded', 'printed mat', 'custom mat', 'customized', 'customised', 'personalised' ),
		),
		'entrance-mats'              => array(
			'title'     => 'Entrance Mats',
			'excerpt'   => 'Commercial entrance matting that stops sand, dust and water at the door — keeping lobbies cleaner, safer and cheaper to maintain.',
			'seo_title' => 'Entrance Mats Dubai | Commercial Dust-Control Matting',
			'seo_desc'  => 'Entrance mats in Dubai & UAE: commercial dust-control and absorbent matting for offices, malls, hotels and villas. Made to size. Quote on WhatsApp.',
			'keyword'   => 'entrance mats dubai',
			'intro'     => array(
				'In the UAE most of the dirt inside a building walks in from outside — fine sand, dust and, in rainy spells or near pools, water. A properly sized entrance mat captures most of it in the first few steps, before it scratches marble, dulls carpets or becomes a slip hazard.',
				'We plan entrance matting as a system: a scraper zone outside, a wiper-scraper zone in the vestibule and an absorbent wiper mat inside. Every piece is cut to your doorway, with options from economical PVC-backed mats to premium recessed systems.',
			),
			'benefits'  => array(
				array( 'Cleaner buildings', 'Traps sand and dust at the door, so floors and carpets stay cleaner for longer.' ),
				array( 'Fewer slips', 'Absorbent pile and rubber backing keep wet shoes from reaching polished floors.' ),
				array( 'Lower cleaning cost', 'Less dirt inside means less mopping, polishing and floor wear.' ),
				array( 'Made to size', 'Cut to your doorway, vestibule or revolving door — no gaps, no curling edges.' ),
			),
			'specs'     => array(
				'Surfaces: nylon and polypropylene wiper pile, ribbed, and wiper-scraper combinations',
				'Backings: nitrile rubber, vinyl and recessed (matwell) versions',
				'Typical thickness: 7 mm to 12 mm for surface mats; deeper for recessed systems',
				'Colours: charcoal, grey, brown, blue, red and custom borders or logos',
				'Made to measure, including L-shapes and curved revolving-door mats',
			),
			'uses'      => array( 'Office tower and residential lobbies', 'Malls and retail entrances', 'Hotels and serviced apartments', 'Hospitals, clinics and schools', 'Villas and back doors' ),
			'faqs'      => array(
				array( 'How big should an entrance mat be?', 'As a rule of thumb, people need four to six steps on matting to remove most dirt. For busy commercial doors we recommend around 3–6 metres of matting in the walking direction, split between outdoor scraper and indoor wiper zones.' ),
				array( 'Can entrance mats be washed?', 'Yes. Most of our wiper mats can be vacuumed daily and pressure-washed or machine-washed when needed. We advise the right cleaning routine for each mat.' ),
				array( 'Will the mat slide on marble?', 'No — our mats use high-grip rubber backing designed for smooth stone and tile floors, and bevelled edges that stay flat.' ),
			),
			'aliases'   => array( 'entrance', 'entry', 'door mat', 'doormat', 'lobby', 'dust control', 'wiper' ),
		),
		'outdoor-scraper-mats'       => array(
			'title'     => 'Outdoor Scraper Mats',
			'excerpt'   => 'Tough rubber, ribbed and coil scraper mats that knock sand and grit off shoes before they reach the door — built for UAE sun and heat.',
			'seo_title' => 'Outdoor Scraper Mats Dubai | Rubber & Heavy-Duty Mats',
			'seo_desc'  => 'Outdoor scraper mats in Dubai & UAE: rubber ring, ribbed and heavy-duty scraper mats for entrances, villas and car parks. Made to size. WhatsApp us.',
			'keyword'   => 'outdoor mats dubai',
			'intro'     => array(
				'Scraper mats are the first line of defence. Their raised, aggressive surface pulls sand, stones and grit out of shoe treads, and their open or drained design lets water and debris fall through instead of sitting on top.',
				'For UAE entrances we specify UV-stable rubber and heavy-duty PVC that cope with direct sun, high temperatures and pressure-washing — and pair them with an indoor wiper mat for a complete system.',
			),
			'benefits'  => array(
				array( 'Built for the sun', 'UV-stable rubber and PVC that do not crack or curl in summer heat.' ),
				array( 'Knocks off sand', 'Raised scraping surface removes the coarse grit that scratches floors.' ),
				array( 'Drains & dries', 'Open designs let water and debris fall through; easy to hose down.' ),
				array( 'Heavy-duty', 'Handles trolleys, foot traffic and the occasional vehicle at loading doors.' ),
			),
			'specs'     => array(
				'Types: rubber ring / honeycomb, ribbed rubber, PVC coil and rubber-backed scraper',
				'Typical thickness: 10 mm to 23 mm',
				'Interlocking modules for large or irregular areas',
				'Optional moulded logo or lettering in rubber',
				'Colours: black as standard; grey, green and red in selected ranges',
			),
			'uses'      => array( 'Building and villa entrances', 'Car-park and lift-lobby doors', 'Pool decks and outdoor showers', 'Loading bays and back-of-house doors', 'Construction site offices' ),
			'faqs'      => array(
				array( 'Can scraper mats stay in direct sun?', 'Yes. We choose UV-stable rubber and PVC grades designed for outdoor use, which do not crack or curl in UAE summers when cared for normally.' ),
				array( 'How do I clean an outdoor scraper mat?', 'Lift and shake, then hose or pressure-wash. Open ring and coil mats can also be swept or vacuumed in place.' ),
				array( 'Can I put my logo on an outdoor mat?', 'Yes — moulded rubber logo mats and logo coil mats are made for outdoor entrances and keep the design for years.' ),
			),
			'aliases'   => array( 'outdoor', 'scraper', 'rubber ring', 'honeycomb', 'ribbed', 'heavy duty' ),
		),
		'coir-doormats'              => array(
			'title'     => 'Coir Doormats',
			'excerpt'   => 'Natural coconut-fibre doormats — plain, printed or personalised with your name, family crest or villa number.',
			'seo_title' => 'Coir Doormats Dubai | Personalised Coconut-Fibre Mats',
			'seo_desc'  => 'Coir doormats in Dubai & UAE: natural coconut-fibre mats, plain or personalised with names, numbers and logos. Made to size. Quote on WhatsApp.',
			'keyword'   => 'coir doormat dubai',
			'intro'     => array(
				'Coir is made from natural coconut fibre — stiff, bristly and excellent at brushing dirt off shoes. It is the classic welcome mat for villas, townhouses and boutique shops.',
				'We supply coir in standard and custom sizes, cut to fit door recesses, and personalise it with names, villa numbers, greetings in English or Arabic, and logos.',
			),
			'benefits'  => array(
				array( 'Natural fibre', 'Eco-friendly coconut fibre that brushes dirt off effectively.' ),
				array( 'Personalised', 'Family names, villa numbers, greetings and logos printed on.' ),
				array( 'Fits recesses', 'Cut to size for matwells and stone surrounds at villa doors.' ),
				array( 'Warm welcome', 'A timeless, textured look that suits classic and modern homes.' ),
			),
			'specs'     => array(
				'Pile height: typically 17 mm to 24 mm',
				'Backing: PVC-backed for grip, or natural for recessed wells',
				'Printing: single or multi-colour designs, English and Arabic text',
				'Sizes: standard doormat sizes or cut to any size and shape',
			),
			'uses'      => array( 'Villa and townhouse front doors', 'Apartment doors', 'Boutique shops and cafés', 'Majlis and guest entrances', 'Gifts and housewarmings' ),
			'faqs'      => array(
				array( 'Can coir mats go outside?', 'Coir works best at covered entrances. In fully exposed spots, a rubber or coil mat lasts longer; coir can still be used if it is allowed to dry out between uses.' ),
				array( 'Can you print Arabic names?', 'Yes. We print names, greetings and villa numbers in Arabic, English or both, and send a mockup first.' ),
				array( 'Do coir mats shed?', 'New coir sheds a few loose fibres in the first weeks. Shaking it out regularly solves this.' ),
			),
			'aliases'   => array( 'coir', 'coconut', 'welcome mat', 'personalised doormat', 'villa mat' ),
		),
		'recessed-entrance-matting'  => array(
			'title'     => 'Recessed Entrance Matting',
			'excerpt'   => 'Architectural aluminium and rubber-link matting set flush into the floor — the premium, permanent entrance for towers, hotels and malls.',
			'seo_title' => 'Recessed Entrance Matting Dubai | Aluminium Matwell Systems',
			'seo_desc'  => 'Recessed entrance matting in Dubai & UAE: aluminium and rubber-link matwell systems for towers, hotels and malls. Measured & installed. WhatsApp us.',
			'keyword'   => 'recessed entrance matting dubai',
			'intro'     => array(
				'Recessed matting sits flush inside a matwell in the floor, so there is no edge to trip on, nothing to slide, and the entrance looks like part of the architecture. Aluminium profiles carry inserts of carpet, rubber or brush that scrape and wipe in one surface.',
				'We measure existing matwells precisely or work with your fit-out team on new ones, then manufacture the system to size — including frames, levelling and replacement inserts.',
			),
			'benefits'  => array(
				array( 'Flush & safe', 'No raised edges — wheelchairs, trolleys and luggage roll straight over.' ),
				array( 'Architectural look', 'Clean aluminium lines that suit marble lobbies and glass façades.' ),
				array( 'Long life', 'Replaceable inserts keep the system looking new for many years.' ),
				array( 'Engineered for traffic', 'Rated for heavy pedestrian use at the busiest entrances.' ),
			),
			'specs'     => array(
				'Profiles: aluminium or rigid PVC, typically 12 mm to 22 mm deep',
				'Inserts: carpet, rubber, brush, scraper or combinations',
				'Frames: aluminium, brass-look or stainless-steel angle',
				'Rolls up for cleaning the matwell; inserts replaceable',
				'Made to measure from a site survey',
			),
			'uses'      => array( 'Office and residential tower lobbies', 'Hotel main entrances', 'Malls and airports', 'Hospitals and universities', 'Luxury villa entrances' ),
			'faqs'      => array(
				array( 'My building already has a matwell — can you replace the mat?', 'Yes. We measure the existing well (width, length and depth) and make a new system to fit, or refurbish the frame and replace worn inserts.' ),
				array( 'How deep does the matwell need to be?', 'Most systems need roughly 12–22 mm depth. Share drawings or a photo with a tape measure and we will recommend the right profile.' ),
				array( 'How is recessed matting cleaned?', 'Vacuum daily; periodically roll the mat up and clean the well beneath. Inserts can be replaced individually when worn.' ),
			),
			'aliases'   => array( 'recessed', 'matwell', 'mat well', 'aluminium', 'aluminum', 'architectural' ),
		),
		'anti-fatigue-mats'          => array(
			'title'     => 'Anti-Fatigue Mats',
			'excerpt'   => 'Cushioned standing mats that reduce leg, back and foot strain for kitchen teams, cashiers, workshops and standing desks.',
			'seo_title' => 'Anti-Fatigue Mats Dubai | Kitchen, Workstation & Industrial',
			'seo_desc'  => 'Anti-fatigue mats in Dubai & UAE for kitchens, cashiers, workshops and standing desks. Cushioned, anti-slip, made to size. Get a quote on WhatsApp.',
			'keyword'   => 'anti fatigue mats dubai',
			'intro'     => array(
				'Standing on hard floors all day causes tired legs, sore backs and lower productivity. Anti-fatigue mats add a cushioned, slightly springy surface that encourages tiny muscle movements and improves circulation.',
				'We supply anti-fatigue matting for every workplace — oil-resistant rubber for commercial kitchens, ESD-safe mats for electronics, heavy-duty modular tiles for factories and slim designer mats for home kitchens and standing desks.',
			),
			'benefits'  => array(
				array( 'Less strain', 'Reduces fatigue in feet, legs and lower back during long shifts.' ),
				array( 'Anti-slip', 'Grippy surfaces and drainage holes for wet and oily areas.' ),
				array( 'Workplace-ready', 'Oil-, grease- and chemical-resistant compounds available.' ),
				array( 'Any length', 'Modular and roll options cover a single station or a whole production line.' ),
			),
			'specs'     => array(
				'Types: solid rubber, drainage rubber, PVC foam, modular interlocking tiles',
				'Typical thickness: 9 mm to 25 mm',
				'Bevelled safety edges in black or yellow',
				'Oil-resistant (nitrile) and ESD / anti-static versions',
			),
			'uses'      => array( 'Commercial and hotel kitchens', 'Cashier counters and receptions', 'Workshops, labs and factories', 'Warehouse packing stations', 'Home kitchens and standing desks' ),
			'faqs'      => array(
				array( 'Which anti-fatigue mat is best for a restaurant kitchen?', 'A nitrile rubber mat with drainage holes — it is grease-resistant, lets liquids drain through and keeps its grip when wet.' ),
				array( 'Can modular anti-fatigue tiles be extended later?', 'Yes. Interlocking tiles clip together, so you can add length or reconfigure the layout at any time.' ),
				array( 'Are anti-fatigue mats easy to clean?', 'Yes — hose, mop or pressure-wash rubber mats; wipe foam mats with a damp cloth and mild detergent.' ),
			),
			'aliases'   => array( 'anti fatigue', 'anti-fatigue', 'standing', 'kitchen mat', 'workstation', 'industrial' ),
		),
		'rubber-gym-mats'            => array(
			'title'     => 'Rubber Gym Mats',
			'excerpt'   => 'Dense rubber gym mats and tiles that protect floors from dropped weights, cut noise and give a firm, non-slip surface for training.',
			'seo_title' => 'Rubber Gym Mats Dubai | Tiles, Rolls & EPDM Gym Mats',
			'seo_desc'  => 'Rubber gym mats in Dubai & UAE: tiles, rolls and EPDM gym mats for home and commercial gyms. Shock-absorbing, quiet, made to size. Quote on WhatsApp.',
			'keyword'   => 'rubber gym mats dubai',
			'intro'     => array(
				'Rubber gym mats absorb the impact of dropped dumbbells and plates, protect the slab and the floor finish beneath, and give a stable, non-slip surface for lifting, cardio and stretching.',
				'We supply plain black and colour-fleck EPDM rubber mats as tiles, rolls and custom-cut pieces — from slim mats under a treadmill to thick drop-zone tiles. For complete gym projects we work with our sister company, Dubai Gym Flooring.',
			),
			'benefits'  => array(
				array( 'Impact protection', 'Cushions dropped weights to protect equipment, flooring and the slab.' ),
				array( 'Quieter training', 'Dense rubber dampens noise and vibration — ideal for apartments.' ),
				array( 'Firm grip', 'A textured, slip-resistant surface for shoes and bare feet.' ),
				array( 'Easy upkeep', 'Vacuum and damp-mop with a neutral cleaner.' ),
			),
			'specs'     => array(
				'Formats: square tiles, interlocking tiles, rolls and custom-cut mats',
				'Common thicknesses: 8 mm to 50 mm',
				'Finishes: black, colour-fleck EPDM and full-colour tops',
				'Equipment mats for treadmills, bikes, rowers and racks',
			),
			'uses'      => array( 'Home and garage gyms', 'Commercial gyms and studios', 'Hotel and building gyms', 'Physio and rehab rooms', 'Under fitness equipment' ),
			'faqs'      => array(
				array( 'What thickness gym mat do I need?', 'Cardio and stretching: 8–15 mm. Dumbbells and general strength: 15–30 mm. Heavy barbell drops: 30–50 mm or a lifting platform.' ),
				array( 'Can I put rubber gym mats on marble or parquet?', 'Yes. Rubber mats protect the original floor. For delicate finishes we can add a protective layer underneath.' ),
				array( 'Do new rubber mats smell?', 'Some rubber has a mild smell for the first days. Airing the room and damp-mopping speeds this up; low-odour options are available.' ),
			),
			'aliases'   => array( 'gym', 'gym mat', 'gym mats', 'rubber', 'rubber tile', 'epdm', 'fitness', 'gym flooring' ),
		),
		'anti-slip-wet-area-mats'    => array(
			'title'     => 'Anti-Slip & Wet Area Mats',
			'excerpt'   => 'Drainage, grid and PVC matting for pools, showers, changing rooms and kitchens — keeps wet floors safe and hygienic.',
			'seo_title' => 'Anti-Slip Mats Dubai | Pool, Shower & Wet Area Matting',
			'seo_desc'  => 'Anti-slip mats in Dubai & UAE for pools, showers, changing rooms and kitchens. Drainage, PVC grid and rubber matting. Made to size. WhatsApp us.',
			'keyword'   => 'anti slip mats dubai',
			'intro'     => array(
				'Wet floors are one of the most common causes of slips in homes, hotels, gyms and restaurants. Wet-area matting raises feet above standing water, drains it away and gives bare feet and shoes a secure grip.',
				'We supply soft, comfortable PVC grid matting for barefoot areas such as pool surrounds, showers and spas, and heavy-duty drainage rubber for kitchens, wash bays and food production.',
			),
			'benefits'  => array(
				array( 'Safer floors', 'Raised, textured surfaces reduce slips where floors are always wet.' ),
				array( 'Hygienic', 'Open designs drain and dry quickly; many contain anti-microbial additives.' ),
				array( 'Barefoot comfort', 'Soft PVC grids that feel good underfoot around pools and spas.' ),
				array( 'Cut to fit', 'Rolls and tiles cut around drains, benches and corners.' ),
			),
			'specs'     => array(
				'Types: PVC hollow grid / Z-grid, drainage rubber, interlocking PVC tiles',
				'Typical thickness: 5 mm to 22 mm',
				'Colours: blue, grey, white, green, terracotta and black',
				'Rolls (often 0.6–1.2 m wide) or interlocking tiles',
			),
			'uses'      => array( 'Pool decks and surrounds', 'Showers, saunas and spas', 'Gym and club changing rooms', 'Commercial kitchens and wash areas', 'Villa bathrooms and laundries' ),
			'faqs'      => array(
				array( 'Which mat is best around a swimming pool?', 'A soft PVC grid mat — it drains instantly, is comfortable for bare feet and resists chlorine and sun.' ),
				array( 'Can wet-area mats be cleaned easily?', 'Yes. Lift, hose and disinfect regularly; they dry quickly in the open air.' ),
				array( 'Do you supply small bathroom sizes?', 'Yes — from single shower mats to full changing-room roll lengths.' ),
			),
			'aliases'   => array( 'anti slip', 'anti-slip', 'wet', 'pool', 'shower', 'bathroom', 'grid', 'drainage', 'spa' ),
		),
		'pvc-coil-mats'              => array(
			'title'     => 'PVC Coil Mats',
			'excerpt'   => 'Spaghetti-loop vinyl mats that trap dirt and water inside the coil — colourful, washable and great for logos.',
			'seo_title' => 'PVC Coil Mats Dubai | Vinyl Loop Mats with Logo',
			'seo_desc'  => 'PVC coil (vinyl loop) mats in Dubai & UAE for entrances, lifts and cars. Many colours, inlaid logos, cut to size. Get a quote on WhatsApp.',
			'keyword'   => 'pvc coil mats dubai',
			'intro'     => array(
				'Coil mats are made from thousands of vinyl loops that scrape shoes and hold dirt and water deep inside the mat, out of sight. They are easy to shake out and hose down, which makes them popular for busy entrances, lifts and car parks.',
				'They come in a wide range of colours and can carry inlaid logos and lettering cut into the coil — a durable, budget-friendly way to brand an entrance indoors or out.',
			),
			'benefits'  => array(
				array( 'Hides dirt', 'Dirt and sand drop inside the loops, so the surface stays looking clean.' ),
				array( 'Indoor & outdoor', 'Weather-resistant vinyl that handles sun and water.' ),
				array( 'Logo-friendly', 'Inlaid logos and lettering in contrasting colours.' ),
				array( 'Easy to wash', 'Shake out, hose down and it is ready again.' ),
			),
			'specs'     => array(
				'Thickness: typically 10 mm to 20 mm',
				'Backing: open (drainage) or solid backed',
				'Colours: black, grey, red, blue, green, beige and more',
				'Rolls up to around 1.2 m wide; cut to any length',
			),
			'uses'      => array( 'Building and shop entrances', 'Lift and elevator floors', 'Car boots and floors', 'Outdoor steps and terraces', 'Pool and garden entrances' ),
			'faqs'      => array(
				array( 'Are coil mats good for outdoor use?', 'Yes. Vinyl coil handles sun and rain well and is easy to hose clean, making it a good outdoor or semi-outdoor mat.' ),
				array( 'Can coil mats have a logo?', 'Yes. Logos and text are cut into the coil in a contrasting colour — durable and fade-resistant.' ),
				array( 'Which backing should I choose?', 'Open backing for outdoor and wet areas so water drains; solid backing indoors to keep dirt inside the mat.' ),
			),
			'aliases'   => array( 'coil', 'pvc', 'vinyl loop', 'spaghetti', 'loop mat' ),
		),
		'lift-day-mats'              => array(
			'title'     => 'Lift & Day Mats',
			'excerpt'   => 'Printed day-of-the-week lift mats and branded elevator mats that keep hotel and tower lifts clean, fresh and on-brand.',
			'seo_title' => 'Lift Mats & Day Mats Dubai | Hotel Elevator Mats',
			'seo_desc'  => 'Lift mats and day-of-the-week mats in Dubai & UAE for hotels, towers and malls. Printed with your logo, cut to your lift size. Quote on WhatsApp.',
			'keyword'   => 'lift mats dubai',
			'intro'     => array(
				'Day mats are a hospitality classic: a set of seven printed mats — Monday to Sunday — swapped in the lift each morning, so guests see a fresh, freshly cleaned floor every day. Add your logo and greeting and the lift becomes part of your brand.',
				'We make lift mats to the exact internal size of your elevator car, with slim backing so doors close cleanly, and supply full seven-day sets in English, Arabic or both.',
			),
			'benefits'  => array(
				array( 'Always fresh', 'A clean mat every day — the old one goes for washing.' ),
				array( 'Branded welcome', 'Logo, day and greeting printed in your colours.' ),
				array( 'Exact fit', 'Cut to your lift car so there are no gaps or curled edges.' ),
				array( 'Protects the lift', 'Shields lift floors from wear, sand and spills.' ),
			),
			'specs'     => array(
				'Sets of 7 (Monday–Sunday) or single branded lift mats',
				'High-definition printed nylon on slim rubber backing',
				'Made to the internal dimensions of each lift',
				'English, Arabic or bilingual designs',
			),
			'uses'      => array( 'Hotels and resorts', 'Residential and office towers', 'Serviced apartments', 'Malls and hospitals', 'Corporate headquarters' ),
			'faqs'      => array(
				array( 'How do I measure my lift for a mat?', 'Measure the internal width and depth of the lift car floor, wall to wall, and send a photo. We allow for door clearance and send a mockup.' ),
				array( 'Do you make day mats in Arabic?', 'Yes — Arabic, English or bilingual day names and greetings, with your logo.' ),
				array( 'Can day mats be machine-washed?', 'Yes. Our printed nylon mats are designed for regular washing to keep a fresh mat in rotation.' ),
			),
			'aliases'   => array( 'lift', 'elevator', 'day mat', 'days of the week', 'monday', 'hotel' ),
		),
		'car-floor-mats'             => array(
			'title'     => 'Car Floor Mats',
			'excerpt'   => 'Custom-fit car mats for every make and model — premium 3D/7D leather-look, rubber and carpet mats, with optional logo or name.',
			'seo_title' => 'Car Floor Mats Dubai | Custom-Fit 3D, 7D & Rubber Car Mats',
			'seo_desc'  => 'Custom car floor mats in Dubai & UAE: made-to-fit 3D/7D leather-look, rubber and carpet mats for every car, SUV and fleet. Quote on WhatsApp.',
			'keyword'   => 'car floor mats dubai',
			'intro'     => array(
				'Sand gets everywhere in a UAE car. Custom-fit floor mats follow the exact shape of your footwells, with raised edges that keep sand, water and spills off the original carpet — and they look far better than universal mats.',
				'Choose premium 3D/7D leather-look mats with stitched edges, heavy-duty rubber liners for work vehicles and SUVs, or classic carpet mats with heel pads. We also supply fleets, rental companies and dealerships, with branding if you need it.',
			),
			'benefits'  => array(
				array( 'Perfect fit', 'Made for your make, model and year — no sliding or gaps.' ),
				array( 'Sand-proof', 'Raised edges and textured surfaces hold sand, water and spills.' ),
				array( 'Premium look', 'Leather-look, stitched and colour-matched options to suit your interior.' ),
				array( 'Fleet & branded', 'Bulk orders for fleets and dealerships, with logos available.' ),
			),
			'specs'     => array(
				'Types: 3D / 5D / 7D leather-look, rubber liners, carpet and coil',
				'Cover: driver, passenger, rear and boot / trunk',
				'Colours: black, beige, brown, red, blue and two-tone stitching',
				'Fixings matched to the factory clips where fitted',
			),
			'uses'      => array( 'Private cars and SUVs', 'Luxury and sports cars', 'Company and rental fleets', 'Taxis, limousines and chauffeur cars', 'Dealership handovers' ),
			'faqs'      => array(
				array( 'Will the mats fit my car exactly?', 'Yes. Send your make, model and year on WhatsApp and we confirm the pattern for your car before production.' ),
				array( 'What is the difference between 3D and 7D mats?', 'Both are moulded to the footwell. 7D mats add extra layers — usually a cushioned core and a removable top carpet — for more comfort and a more luxurious finish.' ),
				array( 'Do you supply fleets?', 'Yes. We supply fleets, rental companies and dealerships in bulk, with optional logos.' ),
			),
			'aliases'   => array( 'car', 'car mat', 'car mats', 'auto', 'vehicle', '7d', '3d', 'suv', 'trunk', 'boot' ),
		),
		'kids-play-mats'             => array(
			'title'     => 'Kids Play Mats',
			'excerpt'   => 'Soft, colourful foam and EVA play mats for nurseries, playrooms and play areas — cushioned, washable and safe for little ones.',
			'seo_title' => 'Kids Play Mats Dubai | EVA Foam & Nursery Floor Mats',
			'seo_desc'  => 'Kids play mats in Dubai & UAE: EVA foam interlocking tiles and soft floor mats for nurseries, playrooms and play areas. Quote on WhatsApp.',
			'keyword'   => 'kids play mats dubai',
			'intro'     => array(
				'Play mats give children a soft, warm surface to crawl, tumble and play on, and protect them from hard tiled floors common in UAE homes. Interlocking EVA tiles are lightweight, quick to lay and easy to wipe clean.',
				'We supply play mats for homes, nurseries, schools and soft-play areas — in bright colours, calm neutrals and wood-look finishes, with matching borders for a neat edge.',
			),
			'benefits'  => array(
				array( 'Soft landings', 'Cushioned foam reduces bumps from falls and tumbles.' ),
				array( 'Easy to clean', 'Wipe-clean surfaces that handle spills and crafts.' ),
				array( 'Quick to lay', 'Interlocking tiles fit together in minutes, no glue.' ),
				array( 'Warm & quiet', 'Insulates from cold tiles and reduces noise.' ),
			),
			'specs'     => array(
				'Formats: interlocking EVA tiles, foam rolls and folding play mats',
				'Typical thickness: 10 mm to 40 mm',
				'Colours: bright multi-colour, pastel, neutral and wood-look',
				'Edge borders and corner pieces for a finished look',
			),
			'uses'      => array( 'Nurseries and playrooms', 'Schools and kindergartens', 'Soft-play and indoor play areas', 'Martial arts and gymnastics rooms', 'Clinics and waiting areas' ),
			'faqs'      => array(
				array( 'Are your play mats safe for babies?', 'We supply play mats from suppliers who state compliance with recognised toy-safety and material standards. Ask us for the documentation for a specific product.' ),
				array( 'Can play mats be used outdoors?', 'EVA foam is best indoors or in shaded areas. For outdoor play areas we recommend rubber or EPDM safety surfacing.' ),
				array( 'How thick should a play mat be?', 'For crawling and play, 10–20 mm is comfortable. For tumbling and climbing areas, choose 30–40 mm.' ),
			),
			'aliases'   => array( 'kids', 'play', 'baby', 'nursery', 'eva', 'foam', 'puzzle', 'children' ),
		),
	);
	$order = 1;
	foreach ( $products as $slug => $product ) {
		$pages[ $slug ] = array_merge(
			array(
				'parent' => 'floor-mats',
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
		'free-logo-mockup-samples'   => array(
			'title'     => 'Free Logo Mockup & Samples',
			'excerpt'   => 'See your logo on the mat before you pay. Send your artwork and we reply with a free digital mockup, colour options and samples.',
			'seo_title' => 'Free Logo Mat Mockup & Samples | Dubai & UAE',
			'seo_desc'  => 'Get a free digital mockup of your logo mat in Dubai & the UAE. Send your logo on WhatsApp — we reply with a design, colour options and samples.',
			'keyword'   => 'logo mat design dubai',
			'intro'     => array(
				'Ordering a branded mat should not be guesswork. Send us your logo and the size you need, and our design team prepares a digital mockup that shows exactly how the finished mat will look — colours, layout, border and proportions.',
				'Want to feel the quality first? We can bring or send pile and backing samples so you can compare textures and colours in your own space.',
			),
			'steps'     => array(
				array( 'Send your logo', 'Share your artwork, size and a photo of the entrance on WhatsApp.' ),
				array( 'Get the mockup', 'We send a free digital proof with colour and layout options.' ),
				array( 'Fine-tune', 'Adjust colours, text or size until it is exactly right.' ),
				array( 'Approve & produce', 'Approve the design and we move straight to production.' ),
			),
			'faqs'      => array(
				array( 'Is the mockup really free?', 'Yes. Digital mockups are free with no obligation to order.' ),
				array( 'My logo is only a low-resolution image — is that OK?', 'Send what you have. Our designers can often redraw simple logos; for complex artwork we will ask for a higher-quality file.' ),
				array( 'Can I get samples delivered?', 'Yes. Tell us which mats you are considering and your location, and we will arrange samples.' ),
			),
			'aliases'   => array( 'mockup', 'design', 'artwork', 'proof', 'samples', 'sample' ),
		),
		'measuring-installation'     => array(
			'title'     => 'Measuring & Installation',
			'excerpt'   => 'We visit, measure doorways, lifts and matwells precisely, and install recessed and made-to-measure matting with a perfect fit.',
			'seo_title' => 'Floor Mat Measuring & Installation Dubai | Matwell Fitting',
			'seo_desc'  => 'Floor mat measuring and installation in Dubai & UAE: site measurement, matwell fitting, recessed entrance systems and wall-to-wall entrance matting.',
			'keyword'   => 'entrance mat installation dubai',
			'intro'     => array(
				'A made-to-measure mat is only as good as the measurement behind it. We visit your site to measure doorways, vestibules, revolving doors, lifts and existing matwells, and check depth, levels and door clearance.',
				'For recessed and wall-to-wall systems our fitters install the mat, frame and any levelling needed — and coordinate with building management on access and working hours.',
			),
			'steps'     => array(
				array( 'Book a visit', 'Send your location and photos on WhatsApp.' ),
				array( 'Measure', 'We measure every entrance, lift or matwell precisely.' ),
				array( 'Produce', 'Your mats are made to the exact size and shape.' ),
				array( 'Install', 'We deliver, fit, level and hand over — clean and finished.' ),
			),
			'faqs'      => array(
				array( 'Do I need a site visit for a simple doormat?', 'No — standard mats can be ordered from your measurements and photos. Site visits are best for recessed systems, large entrances and multi-site projects.' ),
				array( 'Do you handle building NOCs?', 'We provide the documents building management usually requests and work to their rules on access and working hours.' ),
				array( 'Can you remove old mats?', 'Yes. We remove and dispose of old matting as part of replacement jobs.' ),
			),
			'aliases'   => array( 'installation', 'install', 'fitting', 'measure', 'measurement', 'site visit' ),
		),
		'corporate-bulk-orders'      => array(
			'title'     => 'Corporate & Bulk Orders',
			'excerpt'   => 'Branded mats for every branch, hotel, lift and vehicle — one design, consistent quality, delivered on schedule across the UAE.',
			'seo_title' => 'Corporate & Bulk Floor Mat Orders | UAE Supplier',
			'seo_desc'  => 'Bulk custom floor mats for UAE businesses: logo mats for branches, hotel day mats, fleet car mats and replacement programmes. Quote on WhatsApp.',
			'keyword'   => 'bulk logo mats uae',
			'intro'     => array(
				'Retail chains, hotel groups, property managers, banks and fleets need the same branded mat in many places. We keep your approved artwork and specification on file, produce in batches and deliver to each site.',
				'We can also plan a replacement schedule so worn mats are swapped before they look tired — keeping every entrance on-brand all year.',
			),
			'steps'     => array(
				array( 'Brief', 'Share your sites, sizes and brand guidelines.' ),
				array( 'Standardise', 'We create approved designs and specs for every mat type.' ),
				array( 'Produce & deliver', 'Batch production with delivery to each location.' ),
				array( 'Replace', 'Optional scheduled replacements to stay fresh.' ),
			),
			'faqs'      => array(
				array( 'Is there a minimum order for corporate pricing?', 'Volume pricing applies as quantities grow. Send your list of sites and sizes and we will quote tiered pricing.' ),
				array( 'Can you deliver to all our branches?', 'Yes — across Dubai and all seven emirates, to one address or to each branch.' ),
				array( 'Do you keep our artwork for re-orders?', 'Yes. Approved designs are kept on file so re-orders are quick and consistent.' ),
			),
			'aliases'   => array( 'corporate', 'bulk', 'wholesale', 'fleet', 'chain', 'branches' ),
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
		'floor-mats-dubai'               => array(
			'place'       => 'Dubai',
			'emirate'     => 'Dubai',
			'title'       => 'Floor Mats in Dubai',
			'excerpt'     => 'Custom logo, entrance, lift and safety mats delivered and installed across every Dubai community — from Marina towers to Al Quoz warehouses.',
			'seo_title'   => 'Custom Floor Mats Dubai | Logo & Entrance Mats, All Areas',
			'seo_desc'    => 'Custom floor mats across Dubai: logo mats, entrance systems, lift mats and anti-fatigue mats for offices, hotels, villas and malls. Quote on WhatsApp.',
			'keyword'     => 'custom mats dubai',
			'intro'       => array(
				'Dubai’s towers, hotels, malls and villas all share one challenge: fine sand blowing in from outside. The right mat at every entrance keeps marble polished, carpets clean and floors safe — and a branded one makes a confident first impression.',
				'We design, make and deliver mats across the whole city, measure and install recessed systems, and work around building-management rules in towers and communities.',
			),
			'communities' => array( 'Dubai Marina & JBR', 'Downtown Dubai', 'Business Bay', 'Jumeirah Lakes Towers (JLT)', 'Palm Jumeirah', 'Al Quoz', 'Jumeirah Village Circle (JVC)', 'Dubai Hills Estate', 'Arabian Ranches', 'Al Barsha', 'Jumeirah', 'Mirdif', 'Dubai Silicon Oasis', 'Deira & Bur Dubai' ),
			'projects'    => array( 'Office and showroom logo mats', 'Tower lobby entrance systems', 'Hotel day mats and lift mats', 'Restaurant kitchen anti-fatigue mats', 'Villa coir and logo doormats', 'Fleet and private car mats' ),
			'note'        => 'Inside Dubai we can usually measure on site and bring samples. Many towers ask for a contractor NOC and service-lift booking for installations — we prepare the documents building management typically requests.',
			'faqs'        => array(
				array( 'Do you deliver to all areas of Dubai?', 'Yes — from the Marina and Palm to Deira, Mirdif, Dubai Silicon Oasis and Dubai South.' ),
				array( 'Can I collect my mat instead?', 'Yes. Let us know on WhatsApp and we will arrange collection when your mat is ready.' ),
			),
		),
		'floor-mats-dubai-marina'        => array(
			'place'       => 'Dubai Marina',
			'emirate'     => 'Dubai',
			'title'       => 'Floor Mats in Dubai Marina',
			'excerpt'     => 'Lobby, lift and apartment mats for Dubai Marina and JBR towers, waterfront restaurants and boutique stores.',
			'seo_title'   => 'Floor Mats Dubai Marina | Tower Lobby, Lift & Logo Mats',
			'seo_desc'    => 'Custom floor mats in Dubai Marina & JBR: tower lobby matting, lift day mats, restaurant and apartment mats. Made to size. Quote on WhatsApp.',
			'keyword'     => 'floor mats dubai marina',
			'intro'       => array(
				'Dubai Marina is a city of towers, promenades and beach traffic — which means sand and wet feet heading straight for polished lobbies and lifts. Well-planned entrance and lift matting keeps common areas clean and safe.',
				'We supply Marina towers, JBR restaurants and boutiques with logo mats, recessed lobby systems, day mats and anti-slip mats for pool decks.',
			),
			'communities' => array( 'Marina Walk towers', 'Marina Promenade', 'Dubai Marina Mall area', 'JBR (Jumeirah Beach Residence)', 'Emaar 6 Towers', 'Marina Gate' ),
			'projects'    => array( 'Tower lobby entrance matting', 'Lift and day mats', 'Restaurant and café logo mats', 'Pool-deck anti-slip matting' ),
			'note'        => 'Most Marina towers require a contractor NOC and a service-lift booking for installation. We plan delivery around these rules.',
			'faqs'        => array(
				array( 'Which mats suit a beachside building in JBR?', 'An outdoor scraper mat to catch sand, followed by an absorbent indoor wiper mat — and anti-slip grid matting by the pool.' ),
				array( 'Do you supply lift mats for Marina towers?', 'Yes — single branded lift mats and full seven-day sets.' ),
			),
		),
		'floor-mats-downtown-dubai'      => array(
			'place'       => 'Downtown Dubai',
			'emirate'     => 'Dubai',
			'title'       => 'Floor Mats in Downtown Dubai',
			'excerpt'     => 'Premium logo and recessed entrance mats for Downtown Dubai hotels, residences, boutiques and offices.',
			'seo_title'   => 'Floor Mats Downtown Dubai | Hotel, Residence & Logo Mats',
			'seo_desc'    => 'Custom floor mats in Downtown Dubai for luxury hotels, residences and boutiques: logo mats, recessed entrance systems and lift mats. WhatsApp us.',
			'keyword'     => 'logo mats downtown dubai',
			'intro'       => array(
				'Downtown Dubai sets the bar for luxury — and the entrance mat is part of that first impression. Recessed aluminium systems, crisp logo mats and elegant day mats help a lobby feel designed, not just cleaned.',
				'We work quietly in occupied buildings, plan out-of-hours installation where needed and match colours to luxury interiors.',
			),
			'communities' => array( 'Burj Khalifa district', 'Opera District', 'Boulevard residences', 'Old Town', 'Burj Views', 'Downtown hotels' ),
			'projects'    => array( 'Hotel entrance and lift mats', 'Residential lobby matting', 'Boutique logo mats', 'Office reception mats' ),
			'note'        => 'Delivery and working hours in Downtown are often restricted — we schedule around building rules and can arrange out-of-hours work where permitted.',
			'faqs'        => array(
				array( 'Can the mat match our interior design?', 'Yes. We match pile colours to your palette, add borders and logos, and can propose recessed systems that sit flush with stone floors.' ),
				array( 'Do you work with hotel operators?', 'Yes — entrance systems, day mats and back-of-house anti-fatigue and kitchen matting.' ),
			),
		),
		'floor-mats-business-bay'        => array(
			'place'       => 'Business Bay',
			'emirate'     => 'Dubai',
			'title'       => 'Floor Mats in Business Bay',
			'excerpt'     => 'Branded entrance and reception mats for Business Bay offices, showrooms, hotels and canal-side towers.',
			'seo_title'   => 'Floor Mats Business Bay | Office & Showroom Logo Mats',
			'seo_desc'    => 'Custom floor mats in Business Bay: office reception logo mats, tower entrance matting, showroom and hotel mats. Free mockup. Quote on WhatsApp.',
			'keyword'     => 'office mats business bay',
			'intro'       => array(
				'Business Bay is full of company headquarters, showrooms and hotels — places where a branded entrance mat reinforces your identity before a client reaches the reception desk.',
				'We make office logo mats, tower entrance systems and chair mats for workspaces, and deliver to towers along the canal and Al Khail Road.',
			),
			'communities' => array( 'Bay Avenue', 'Executive Towers', 'Canal-side towers', 'Al Khail Road offices', 'Business Bay hotels', 'Marasi Drive' ),
			'projects'    => array( 'Office reception logo mats', 'Tower entrance matting', 'Showroom branded mats', 'Hotel and serviced apartment mats' ),
			'note'        => 'Office towers usually need deliveries booked through the service entrance; we coordinate directly with facility management.',
			'faqs'        => array(
				array( 'Can you make a mat for our office reception?', 'Yes — send your logo and the area size and we will send a free mockup.' ),
				array( 'Do you supply mats for multiple floors of one company?', 'Yes. We can make matching mats for every floor and entrance.' ),
			),
		),
		'floor-mats-jlt'                 => array(
			'place'       => 'Jumeirah Lakes Towers (JLT)',
			'emirate'     => 'Dubai',
			'title'       => 'Floor Mats in JLT',
			'excerpt'     => 'Entrance, office and café mats for JLT’s cluster towers, offices, restaurants and gyms.',
			'seo_title'   => 'Floor Mats JLT | Office, Café & Tower Entrance Mats',
			'seo_desc'    => 'Custom floor mats in JLT (Jumeirah Lakes Towers): tower entrance matting, office logo mats, café and gym mats. Made to size. Quote on WhatsApp.',
			'keyword'     => 'floor mats jlt',
			'intro'       => array(
				'JLT’s cluster towers mix offices, apartments, cafés and gyms around the lakes — busy podium entrances where good matting makes a big difference to cleanliness.',
				'We supply tower and podium entrance matting, logo mats for businesses and anti-fatigue and wet-area mats for the many restaurants and fitness studios.',
			),
			'communities' => array( 'Clusters A–Z', 'JLT podium retail', 'Almas Tower area', 'JLT Park side', 'DMCC offices' ),
			'projects'    => array( 'Tower and podium entrance mats', 'Office logo mats', 'Café and restaurant mats', 'Gym rubber mats' ),
			'note'        => 'Most JLT towers are DMCC-managed with specific contractor rules; we prepare the usual documents and book access in advance.',
			'faqs'        => array(
				array( 'Do you deliver to DMCC offices?', 'Yes — we deliver across all JLT clusters and DMCC areas.' ),
				array( 'Can you supply rubber mats for a JLT gym?', 'Yes — rubber gym mats and tiles, plus entrance mats for the studio door.' ),
			),
		),
		'floor-mats-palm-jumeirah'       => array(
			'place'       => 'Palm Jumeirah',
			'emirate'     => 'Dubai',
			'title'       => 'Floor Mats in Palm Jumeirah',
			'excerpt'     => 'Villa, resort and pool-side mats for Palm Jumeirah — personalised doormats, beach-entrance scrapers and anti-slip pool matting.',
			'seo_title'   => 'Floor Mats Palm Jumeirah | Villa, Resort & Pool Mats',
			'seo_desc'    => 'Custom floor mats on Palm Jumeirah: personalised villa doormats, resort logo mats, sand-scraper and pool-side anti-slip mats. Quote on WhatsApp.',
			'keyword'     => 'floor mats palm jumeirah',
			'intro'       => array(
				'On the Palm, the beach is never far away — and nor is the sand. Villas and resorts need mats that scrape sand at the door, stay safe around pools and still look the part.',
				'We supply personalised villa doormats, resort entrance and lift mats, outdoor scrapers and soft PVC pool-side matting.',
			),
			'communities' => array( 'Frond villas', 'Shoreline Apartments', 'Golden Mile', 'The Crescent resorts', 'Palm West Beach', 'Palm Tower' ),
			'projects'    => array( 'Personalised villa doormats', 'Resort logo and day mats', 'Beach-entrance scraper mats', 'Pool-side anti-slip matting' ),
			'note'        => 'Resort deliveries are scheduled with each property’s receiving team; for villas we deliver directly to your door.',
			'faqs'        => array(
				array( 'What mat stops beach sand coming into a villa?', 'A rubber or coil scraper mat outside, then a coir or absorbent indoor mat inside the door.' ),
				array( 'Can you print our villa name on the mat?', 'Yes — in English, Arabic or both, with a free mockup first.' ),
			),
		),
		'floor-mats-al-quoz'             => array(
			'place'       => 'Al Quoz',
			'emirate'     => 'Dubai',
			'title'       => 'Floor Mats in Al Quoz',
			'excerpt'     => 'Heavy-duty anti-fatigue, rubber and entrance mats for Al Quoz warehouses, workshops, galleries and gyms.',
			'seo_title'   => 'Floor Mats Al Quoz | Industrial, Warehouse & Gym Mats',
			'seo_desc'    => 'Industrial floor mats in Al Quoz: anti-fatigue mats, heavy-duty rubber, gym mats and entrance matting for warehouses and workshops. WhatsApp us.',
			'keyword'     => 'industrial mats dubai',
			'intro'       => array(
				'Al Quoz is Dubai’s workshop district — warehouses, fabrication units, studios, galleries and gyms. Floors here need mats that handle trolleys, oil, long shifts and heavy equipment.',
				'We supply modular anti-fatigue matting, heavy-duty rubber, gym mats and logo entrance mats for showrooms and creative spaces.',
			),
			'communities' => array( 'Al Quoz Industrial 1–4', 'Alserkal Avenue', 'Al Quoz Creative Zone', 'Al Khail Gate', 'Al Quoz 3 showrooms' ),
			'projects'    => array( 'Workshop anti-fatigue lines', 'Warehouse entrance matting', 'Gym and CrossFit rubber mats', 'Gallery and showroom logo mats' ),
			'note'        => 'Industrial sites are often easy to access — we can usually deliver and fit quickly once sizes are confirmed.',
			'faqs'        => array(
				array( 'Do you have oil-resistant mats for workshops?', 'Yes — nitrile rubber anti-fatigue mats resist oil, grease and many chemicals.' ),
				array( 'Can you cover a long production line?', 'Yes — modular interlocking tiles can run any length and be reconfigured later.' ),
			),
		),
		'floor-mats-jvc'                 => array(
			'place'       => 'Jumeirah Village Circle (JVC)',
			'emirate'     => 'Dubai',
			'title'       => 'Floor Mats in JVC',
			'excerpt'     => 'Doormats, kids play mats, kitchen and car mats for JVC apartments and townhouses — plus entrance mats for community buildings.',
			'seo_title'   => 'Floor Mats JVC | Home, Kids, Kitchen & Car Mats',
			'seo_desc'    => 'Floor mats in Jumeirah Village Circle (JVC): personalised doormats, kids play mats, kitchen anti-fatigue and car mats. Delivered. Quote on WhatsApp.',
			'keyword'     => 'floor mats jvc',
			'intro'       => array(
				'JVC is one of Dubai’s busiest family communities, with apartments, townhouses and plenty of young children. Homes here need practical mats: doormats that stop sand, soft play mats and comfortable kitchen mats.',
				'We also supply entrance and lift mats for JVC’s residential buildings and car mats for family SUVs.',
			),
			'communities' => array( 'JVC districts 10–18', 'Circle Mall area', 'JVC townhouses', 'JVT (Jumeirah Village Triangle)' ),
			'projects'    => array( 'Personalised apartment doormats', 'Kids play mats', 'Kitchen anti-fatigue mats', 'Building entrance and lift mats' ),
			'note'        => 'Home orders in JVC are usually delivered to your door within the quoted lead time.',
			'faqs'        => array(
				array( 'Do you deliver single doormats?', 'Yes — single personalised doormats are delivered across JVC and JVT.' ),
				array( 'Which play mat is best for an apartment?', 'Interlocking EVA tiles, 15–20 mm thick — soft, warm and quieter for neighbours below.' ),
			),
		),
		'floor-mats-abu-dhabi'           => array(
			'place'       => 'Abu Dhabi',
			'emirate'     => 'Abu Dhabi',
			'title'       => 'Floor Mats in Abu Dhabi',
			'excerpt'     => 'Logo, entrance, lift and safety mats for Abu Dhabi offices, government buildings, hotels, malls and villas.',
			'seo_title'   => 'Custom Floor Mats Abu Dhabi | Logo & Entrance Mats',
			'seo_desc'    => 'Custom floor mats in Abu Dhabi: logo mats, entrance matting, lift mats, anti-fatigue and car mats for offices, hotels and villas. Quote on WhatsApp.',
			'keyword'     => 'floor mats abu dhabi',
			'intro'       => array(
				'From government and corporate headquarters to island resorts and family villas, Abu Dhabi buildings put a lot of care into their entrances. A well-made mat protects the floor and carries the brand.',
				'We supply logo mats, entrance systems, lift day mats and safety matting across Abu Dhabi city, the islands and Khalifa City.',
			),
			'communities' => array( 'Abu Dhabi City', 'Al Reem Island', 'Saadiyat Island', 'Yas Island', 'Al Raha Beach', 'Khalifa City', 'Mussafah' ),
			'projects'    => array( 'Corporate and government logo mats', 'Hotel and resort day mats', 'Mall entrance matting', 'Mussafah workshop anti-fatigue mats' ),
			'note'        => 'For Abu Dhabi projects we start with photos and measurements on WhatsApp, then combine site visits and installations into planned trips.',
			'faqs'        => array(
				array( 'Do you deliver to Abu Dhabi?', 'Yes — across Abu Dhabi city, the islands, Khalifa City and Mussafah.' ),
				array( 'Can you install recessed matting in Abu Dhabi?', 'Yes. We measure the matwell, produce the system and install it on a scheduled visit.' ),
			),
		),
		'floor-mats-sharjah'             => array(
			'place'       => 'Sharjah',
			'emirate'     => 'Sharjah',
			'title'       => 'Floor Mats in Sharjah',
			'excerpt'     => 'Entrance, logo, prayer-room and safety mats for Sharjah homes, schools, mosques, offices and industrial areas.',
			'seo_title'   => 'Floor Mats Sharjah | Logo, Entrance & Industrial Mats',
			'seo_desc'    => 'Floor mats in Sharjah: logo and entrance mats, school and office matting, anti-fatigue and industrial mats. Delivered across Sharjah. WhatsApp us.',
			'keyword'     => 'floor mats sharjah',
			'intro'       => array(
				'Sharjah combines busy family neighbourhoods, schools and universities with large industrial areas — so matting needs range from personalised doormats to heavy-duty factory mats.',
				'We deliver across Sharjah and combine visits with Ajman and Dubai for quick scheduling.',
			),
			'communities' => array( 'Al Majaz', 'Al Nahda', 'Al Khan', 'Muwaileh', 'University City', 'Aljada', 'Sharjah Industrial Areas' ),
			'projects'    => array( 'School and university entrance mats', 'Office logo mats', 'Industrial anti-fatigue matting', 'Villa and apartment doormats' ),
			'note'        => 'Sharjah deliveries are usually combined with Dubai and Ajman routes for fast turnaround.',
			'faqs'        => array(
				array( 'Do you supply mats to Sharjah industrial areas?', 'Yes — anti-fatigue, rubber and entrance matting for factories and warehouses.' ),
				array( 'Can schools get branded entrance mats?', 'Yes — school crests and names on hard-wearing entrance mats.' ),
			),
		),
		'floor-mats-ajman'               => array(
			'place'       => 'Ajman',
			'emirate'     => 'Ajman',
			'title'       => 'Floor Mats in Ajman',
			'excerpt'     => 'Doormats, entrance mats and business logo mats delivered across Ajman.',
			'seo_title'   => 'Floor Mats Ajman | Doormats, Entrance & Logo Mats',
			'seo_desc'    => 'Floor mats in Ajman: personalised doormats, entrance mats, logo mats for shops and offices, car mats and more. Delivered. Quote on WhatsApp.',
			'keyword'     => 'floor mats ajman',
			'intro'       => array(
				'Ajman’s growing towers, shops, clinics and villas all need practical entrance matting — and businesses want their logo at the door.',
				'We deliver across Ajman, usually alongside Sharjah routes, with free mockups for every logo mat.',
			),
			'communities' => array( 'Ajman Downtown', 'Al Nuaimiya', 'Al Rashidiya', 'Al Jurf', 'Ajman Corniche', 'Al Zorah' ),
			'projects'    => array( 'Shop and clinic logo mats', 'Tower entrance mats', 'Villa doormats', 'Car mats' ),
			'note'        => 'Ajman orders are typically combined with nearby Sharjah deliveries.',
			'faqs'        => array(
				array( 'Do you deliver to Ajman?', 'Yes, across Ajman city and Al Zorah.' ),
				array( 'Can I order without a site visit?', 'Yes. Send measurements and photos on WhatsApp and we will quote and deliver.' ),
			),
		),
		'floor-mats-ras-al-khaimah'      => array(
			'place'       => 'Ras Al Khaimah',
			'emirate'     => 'Ras Al Khaimah',
			'title'       => 'Floor Mats in Ras Al Khaimah',
			'excerpt'     => 'Resort, hotel and villa mats for Ras Al Khaimah — logo mats, day mats, pool-side and outdoor scraper mats.',
			'seo_title'   => 'Floor Mats Ras Al Khaimah | Resort, Hotel & Villa Mats',
			'seo_desc'    => 'Floor mats in Ras Al Khaimah: resort logo and day mats, pool-side anti-slip mats, outdoor scrapers and villa doormats. Get a quote on WhatsApp.',
			'keyword'     => 'floor mats ras al khaimah',
			'intro'       => array(
				'Ras Al Khaimah’s resorts on Al Marjan Island and along the coast welcome guests straight from beaches and pools — so entrance, lift and pool-side matting work hard.',
				'We supply resort logo and day mats, outdoor scrapers, anti-slip pool matting and villa doormats across RAK.',
			),
			'communities' => array( 'Al Marjan Island', 'Al Hamra Village', 'Mina Al Arab', 'RAK City', 'Al Nakheel' ),
			'projects'    => array( 'Resort entrance and logo mats', 'Hotel lift day mats', 'Pool-side anti-slip matting', 'Villa doormats' ),
			'note'        => 'RAK projects are planned around a combined delivery and installation visit — share photos and sizes on WhatsApp to start.',
			'faqs'        => array(
				array( 'Do you supply resorts in RAK?', 'Yes — entrance systems, day mats, pool-side matting and back-of-house mats.' ),
				array( 'Which mats suit beachfront entrances?', 'Rubber or coil scrapers outside, then absorbent logo mats inside.' ),
			),
		),
		'floor-mats-fujairah'            => array(
			'place'       => 'Fujairah',
			'emirate'     => 'Fujairah',
			'title'       => 'Floor Mats in Fujairah',
			'excerpt'     => 'Mats for Fujairah’s east-coast hotels, homes, ports and community facilities — built for humidity and heavy use.',
			'seo_title'   => 'Floor Mats Fujairah | East Coast Hotel & Home Mats',
			'seo_desc'    => 'Floor mats in Fujairah and the east coast: hotel logo mats, outdoor scrapers, anti-slip and industrial mats. Get a quote on WhatsApp.',
			'keyword'     => 'floor mats fujairah',
			'intro'       => array(
				'Fujairah and the east coast — including Dibba and the resort strip — have hotels, villas, port facilities and community buildings that need mats able to handle humidity, sand and heavy footfall.',
				'We specify quick-drying, hard-wearing mats and plan deliveries across the mountains efficiently.',
			),
			'communities' => array( 'Fujairah City', 'Dibba Al Fujairah', 'Al Aqah', 'Kalba (east coast)', 'Mirbah' ),
			'projects'    => array( 'Resort and hotel mats', 'Villa doormats', 'Port and industrial matting', 'Community facility entrances' ),
			'note'        => 'East-coast orders are planned around combined deliveries — share sizes and photos on WhatsApp to start.',
			'faqs'        => array(
				array( 'Do you deliver to Fujairah and Dibba?', 'Yes, across Fujairah city and the east coast.' ),
				array( 'Which mats cope best with humidity?', 'Rubber, PVC coil and grid mats dry quickly and resist mould in humid conditions.' ),
			),
		),
		'floor-mats-umm-al-quwain'       => array(
			'place'       => 'Umm Al Quwain',
			'emirate'     => 'Umm Al Quwain',
			'title'       => 'Floor Mats in Umm Al Quwain',
			'excerpt'     => 'Doormats, entrance and logo mats for Umm Al Quwain homes, businesses and new waterfront developments.',
			'seo_title'   => 'Floor Mats Umm Al Quwain | Home & Business Mats',
			'seo_desc'    => 'Floor mats in Umm Al Quwain: personalised doormats, entrance mats and logo mats for homes, shops and offices. Delivered. Quote on WhatsApp.',
			'keyword'     => 'floor mats umm al quwain',
			'intro'       => array(
				'Umm Al Quwain is growing, with new waterfront and residential developments alongside established neighbourhoods and businesses.',
				'We deliver practical, durable mats across UAQ and combine trips with Ajman and Sharjah for quick scheduling.',
			),
			'communities' => array( 'UAQ City', 'Al Salamah', 'Al Raas', 'Umm Al Quwain Marina', 'Al Humrah' ),
			'projects'    => array( 'Villa and apartment doormats', 'Shop and office logo mats', 'Community building entrances', 'Car mats' ),
			'note'        => 'UAQ deliveries are usually combined with nearby Ajman and Sharjah routes.',
			'faqs'        => array(
				array( 'Do you deliver to Umm Al Quwain?', 'Yes, across UAQ city and surrounding communities.' ),
				array( 'Can I order a single personalised mat?', 'Yes — single mats are welcome, with a free mockup first.' ),
			),
		),
		'floor-mats-al-ain'              => array(
			'place'       => 'Al Ain',
			'emirate'     => 'Abu Dhabi',
			'title'       => 'Floor Mats in Al Ain',
			'excerpt'     => 'Entrance, logo, school and villa mats for Al Ain — plus outdoor scrapers built for inland heat and dust.',
			'seo_title'   => 'Floor Mats Al Ain | Villa, School & Business Mats',
			'seo_desc'    => 'Floor mats in Al Ain for villas, schools, universities and businesses: entrance mats, logo mats and heat-ready outdoor scrapers. Quote on WhatsApp.',
			'keyword'     => 'floor mats al ain',
			'intro'       => array(
				'Al Ain — the Garden City — has large family villas, major education campuses and plenty of dust in the dry inland climate. Entrance mats here need to scrape hard and stand up to intense summer heat.',
				'We supply UV-stable outdoor scrapers, absorbent indoor mats and logo mats for schools, universities and businesses across Al Ain.',
			),
			'communities' => array( 'Al Jimi', 'Al Muwaiji', 'Zakher', 'Al Towayya', 'Al Hili', 'University areas' ),
			'projects'    => array( 'Villa doormats and majlis mats', 'School and university entrance mats', 'Business logo mats', 'Outdoor scraper mats' ),
			'note'        => 'Al Ain orders start with a WhatsApp review of photos and sizes, followed by a scheduled delivery or visit.',
			'faqs'        => array(
				array( 'Do you deliver to Al Ain?', 'Yes, across Al Ain for homes, schools, universities and businesses.' ),
				array( 'Which outdoor mat handles Al Ain’s summer heat?', 'UV-stable rubber or heavy-duty PVC scraper mats — they do not crack or curl in high temperatures.' ),
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
				'aliases' => array( strtolower( $location['place'] ), str_replace( 'floor-mats-', '', $slug ) ),
			),
			$location
		);
	}

	// Real product details from the supplier catalogues.
	foreach ( cfm_catalogue_data() as $slug => $extra ) {
		if ( ! isset( $pages[ $slug ] ) ) {
			continue;
		}
		if ( isset( $extra['faqs'] ) ) {
			$extra['faqs'] = array_merge( $pages[ $slug ]['faqs'], $extra['faqs'] );
		}
		$pages[ $slug ] = array_merge( $pages[ $slug ], $extra );
	}

	return array_merge( $pages, cfm_interiors_data() );
}

/**
 * Full page path (parent/child) for a slug.
 *
 * @param string $slug Slug.
 * @return string
 */
function cfm_page_path( $slug ) {
	$pages = cfm_pages_data();
	if ( ! isset( $pages[ $slug ] ) ) {
		return $slug;
	}
	$parent = isset( $pages[ $slug ]['parent'] ) ? $pages[ $slug ]['parent'] : '';
	return $parent ? $parent . '/' . $slug : $slug;
}
