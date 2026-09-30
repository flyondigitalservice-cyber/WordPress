<?php
/**
 * Front-page content: the launch journey, services, plans and FAQ.
 *
 * Kept in one place so copy can be edited without touching markup.
 *
 * @package Zobo
 */

/**
 * The four phases of the launch journey, each with its stages.
 *
 * @return array
 */
function zobod2c_journey() {
	return array(
		array(
			'phase'  => __( 'Shape it', 'zobo-d2c' ),
			'weeks'  => __( 'Week 1–3', 'zobo-d2c' ),
			'stages' => array(
				array(
					'icon'  => 'bulb',
					'title' => __( 'Idea & strategy', 'zobo-d2c' ),
					'text'  => __( 'Market sizing, competitor teardown, pricing and positioning, so you know who buys, why, and at what price before you spend on stock.', 'zobo-d2c' ),
				),
				array(
					'icon'  => 'pen',
					'title' => __( 'Name, logo & identity', 'zobo-d2c' ),
					'text'  => __( 'Brand name, logo, colours, type, tone of voice and packaging design built to stand out on a crowded shelf or scroll.', 'zobo-d2c' ),
				),
				array(
					'icon'  => 'shield',
					'title' => __( 'Trademark', 'zobo-d2c' ),
					'text'  => __( 'Search, class selection and filing so the name you fall in love with is actually yours to keep.', 'zobo-d2c' ),
				),
			),
		),
		array(
			'phase'  => __( 'Make it legal', 'zobo-d2c' ),
			'weeks'  => __( 'Week 2–6', 'zobo-d2c' ),
			'stages' => array(
				array(
					'icon'  => 'doc',
					'title' => __( 'Company & licences', 'zobo-d2c' ),
					'text'  => __( 'Company registration, GST, MSME/Udyam, IEC and category licences like FSSAI, CDSCO, BIS and AYUSH, filed in the right order.', 'zobo-d2c' ),
				),
				array(
					'icon'  => 'handshake',
					'title' => __( 'Vendor registration', 'zobo-d2c' ),
					'text'  => __( 'Seller onboarding and vendor codes with marketplaces, quick-commerce apps, modern trade and distributors.', 'zobo-d2c' ),
				),
			),
		),
		array(
			'phase'  => __( 'Make it real', 'zobo-d2c' ),
			'weeks'  => __( 'Week 4–10', 'zobo-d2c' ),
			'stages' => array(
				array(
					'icon'  => 'factory',
					'title' => __( 'Manufacturing & packaging', 'zobo-d2c' ),
					'text'  => __( 'Vetted private-label and contract manufacturers, sampling, MOQ negotiation, barcodes and print-ready packaging.', 'zobo-d2c' ),
				),
				array(
					'icon'  => 'flask',
					'title' => __( 'Testing & certification', 'zobo-d2c' ),
					'text'  => __( 'Microbial, heavy-metal, assay and shelf-life testing with NABL-accredited labs, plus ISO, GMP and HACCP readiness.', 'zobo-d2c' ),
				),
				array(
					'icon'  => 'doc',
					'title' => __( 'Claims & scientific proof', 'zobo-d2c' ),
					'text'  => __( 'Label and claims review, literature reviews and efficacy studies, so every promise on your pack is one you can defend.', 'zobo-d2c' ),
				),
			),
		),
		array(
			'phase'  => __( 'Make it sell', 'zobo-d2c' ),
			'weeks'  => __( 'Week 8 onwards', 'zobo-d2c' ),
			'stages' => array(
				array(
					'icon'  => 'cart',
					'title' => __( 'Marketplaces', 'zobo-d2c' ),
					'text'  => __( 'Amazon, Flipkart, Myntra, Nykaa, Blinkit, Zepto and more: listings, A+ content, catalog SEO and account management.', 'zobo-d2c' ),
				),
				array(
					'icon'  => 'monitor',
					'title' => __( 'D2C website', 'zobo-d2c' ),
					'text'  => __( 'Fast Shopify or WordPress/WooCommerce stores with payments, COD, shipping and WhatsApp integrations, built to convert.', 'zobo-d2c' ),
				),
				array(
					'icon'  => 'chart',
					'title' => __( 'Performance marketing', 'zobo-d2c' ),
					'text'  => __( 'Meta, Google and marketplace ads run against ROAS and CAC targets, with creatives tested every week.', 'zobo-d2c' ),
				),
				array(
					'icon'  => 'star',
					'title' => __( 'Influencer marketing', 'zobo-d2c' ),
					'text'  => __( 'Creator discovery, briefs, UGC and seeding campaigns from nano to celebrity, tracked to sales, not just likes.', 'zobo-d2c' ),
				),
				array(
					'icon'  => 'tv',
					'title' => __( 'OTT & TVC ads', 'zobo-d2c' ),
					'text'  => __( 'Scripting, production and media buying for connected TV, OTT platforms and television when you are ready to go big.', 'zobo-d2c' ),
				),
			),
		),
	);
}

/**
 * Service groupings for the bento grid.
 *
 * @return array
 */
function zobod2c_services() {
	return array(
		array(
			'size'  => 'wide',
			'tone'  => 'ink',
			'tag'   => __( 'Brand', 'zobo-d2c' ),
			'title' => __( 'Branding & packaging that earns the first look', 'zobo-d2c' ),
			'items' => array( __( 'Naming', 'zobo-d2c' ), __( 'Logo & identity', 'zobo-d2c' ), __( 'Packaging', 'zobo-d2c' ), __( 'Brand book', 'zobo-d2c' ), __( 'Product photography', 'zobo-d2c' ) ),
		),
		array(
			'size'  => 'tall',
			'tone'  => 'accent',
			'tag'   => __( 'Legal', 'zobo-d2c' ),
			'title' => __( 'Paperwork, sorted', 'zobo-d2c' ),
			'items' => array( __( 'Trademark', 'zobo-d2c' ), __( 'Company registration', 'zobo-d2c' ), __( 'GST', 'zobo-d2c' ), __( 'FSSAI', 'zobo-d2c' ), __( 'CDSCO', 'zobo-d2c' ), __( 'BIS', 'zobo-d2c' ), __( 'AYUSH', 'zobo-d2c' ), __( 'MSME / Udyam', 'zobo-d2c' ), __( 'IEC', 'zobo-d2c' ), __( 'Vendor codes', 'zobo-d2c' ) ),
		),
		array(
			'size'  => '',
			'tone'  => 'lime',
			'tag'   => __( 'Product', 'zobo-d2c' ),
			'title' => __( 'Made, tested and proven', 'zobo-d2c' ),
			'items' => array( __( 'Private label', 'zobo-d2c' ), __( 'MOQ & costing', 'zobo-d2c' ), __( 'NABL lab testing', 'zobo-d2c' ), __( 'Shelf-life studies', 'zobo-d2c' ), __( 'Claims dossier', 'zobo-d2c' ) ),
		),
		array(
			'size'  => '',
			'tone'  => 'paper',
			'tag'   => __( 'Commerce', 'zobo-d2c' ),
			'title' => __( 'Marketplaces & D2C store', 'zobo-d2c' ),
			'items' => array( __( 'Amazon', 'zobo-d2c' ), __( 'Flipkart', 'zobo-d2c' ), __( 'Quick commerce', 'zobo-d2c' ), __( 'Shopify', 'zobo-d2c' ), __( 'WooCommerce', 'zobo-d2c' ) ),
		),
		array(
			'size'  => 'wide',
			'tone'  => 'paper',
			'tag'   => __( 'Growth', 'zobo-d2c' ),
			'title' => __( 'Performance, influencer and TV: every channel that moves sales', 'zobo-d2c' ),
			'items' => array( __( 'Meta ads', 'zobo-d2c' ), __( 'Google ads', 'zobo-d2c' ), __( 'Marketplace ads', 'zobo-d2c' ), __( 'Influencers & UGC', 'zobo-d2c' ), __( 'OTT', 'zobo-d2c' ), __( 'TVC production', 'zobo-d2c' ) ),
		),
	);
}

/**
 * Engagement plans. Prices are intentionally left as "on request".
 *
 * @return array
 */
function zobod2c_plans() {
	return array(
		array(
			'name'     => __( 'Spark', 'zobo-d2c' ),
			'for'      => __( 'You have an idea and need a brand.', 'zobo-d2c' ),
			'featured' => false,
			'items'    => array(
				__( 'Idea validation & positioning', 'zobo-d2c' ),
				__( 'Brand name & logo', 'zobo-d2c' ),
				__( 'Trademark filing', 'zobo-d2c' ),
				__( 'Company & GST registration', 'zobo-d2c' ),
				__( 'Packaging design', 'zobo-d2c' ),
			),
		),
		array(
			'name'     => __( 'Launch', 'zobo-d2c' ),
			'for'      => __( 'You want to go from idea to first orders.', 'zobo-d2c' ),
			'featured' => true,
			'items'    => array(
				__( 'Everything in Spark', 'zobo-d2c' ),
				__( 'All category licences', 'zobo-d2c' ),
				__( 'Manufacturer sourcing & sampling', 'zobo-d2c' ),
				__( 'Marketplace onboarding & listings', 'zobo-d2c' ),
				__( 'D2C website', 'zobo-d2c' ),
				__( 'Launch campaign: ads + creators', 'zobo-d2c' ),
			),
		),
		array(
			'name'     => __( 'Scale', 'zobo-d2c' ),
			'for'      => __( 'You are selling and want to grow faster.', 'zobo-d2c' ),
			'featured' => false,
			'items'    => array(
				__( 'Performance marketing retainer', 'zobo-d2c' ),
				__( 'Influencer & UGC programme', 'zobo-d2c' ),
				__( 'Marketplace account management', 'zobo-d2c' ),
				__( 'Quick-commerce & modern trade', 'zobo-d2c' ),
				__( 'OTT & TVC campaigns', 'zobo-d2c' ),
			),
		),
	);
}

/**
 * Categories Zobo works in.
 *
 * @return array
 */
function zobod2c_industries() {
	return array(
		array(
			'title' => __( 'Food & Beverage', 'zobo-d2c' ),
			'text'  => __( 'Clean-label snacks, millets, functional drinks and fermented foods, from formulation to FSSAI licence to shelf-ready packaging.', 'zobo-d2c' ),
			'tags'  => array( 'FSSAI', __( 'Shelf-life', 'zobo-d2c' ) ),
		),
		array(
			'title' => __( 'Nutraceuticals & Supplements', 'zobo-d2c' ),
			'text'  => __( 'Protein, gummies, Ayurvedic blends and sports nutrition with assay-verified potency, GMP manufacturing partners and compliant claims.', 'zobo-d2c' ),
			'tags'  => array( 'GMP', __( 'Assay', 'zobo-d2c' ) ),
		),
		array(
			'title' => __( 'Skincare & Cosmetics', 'zobo-d2c' ),
			'text'  => __( 'Derma-grade actives, SPF and clean beauty with stability and challenge testing, CDSCO cosmetic registration and tested claims.', 'zobo-d2c' ),
			'tags'  => array( 'CDSCO', __( 'Stability', 'zobo-d2c' ) ),
		),
		array(
			'title' => __( 'Medical Devices', 'zobo-d2c' ),
			'text'  => __( 'Class A–D devices, IVDs and wellness hardware: CDSCO registration, ISO 13485 readiness and clinical evaluation reports.', 'zobo-d2c' ),
			'tags'  => array( 'CDSCO', 'ISO 13485' ),
		),
		array(
			'title' => __( 'Ayurveda & Wellness', 'zobo-d2c' ),
			'text'  => __( 'Classical and proprietary Ayurvedic products: AYUSH licensing, heavy-metal safety and modern efficacy evidence.', 'zobo-d2c' ),
			'tags'  => array( 'AYUSH', __( 'Safety', 'zobo-d2c' ) ),
		),
		array(
			'title' => __( 'Pet Nutrition', 'zobo-d2c' ),
			'text'  => __( 'Treats, supplements and functional pet food with palatability trials, nutritional adequacy statements and retail-ready compliance.', 'zobo-d2c' ),
			'tags'  => array( __( 'Palatability', 'zobo-d2c' ), __( 'Retail', 'zobo-d2c' ) ),
		),
	);
}

/**
 * Brands shown in the Work section when no case studies are published.
 *
 * @return array
 */
function zobod2c_clients() {
	return array(
		array(
			'name' => 'One Love',
			'url'  => 'https://oneloveenergy.com/',
			'cat'  => __( 'Energy beverage', 'zobo-d2c' ),
			'text' => __( 'Functional energy cans: claim-verified formulation, FSSAI beverage compliance and a quick-commerce-first launch.', 'zobo-d2c' ),
		),
		array(
			'name' => 'Biovixa',
			'url'  => 'https://biovixa.in/',
			'cat'  => __( 'Organic plant care', 'zobo-d2c' ),
			'text' => __( 'Organic fertilisers and neem crop solutions: lab testing, compliant labelling and a multi-marketplace rollout.', 'zobo-d2c' ),
		),
		array(
			'name' => 'Evermore',
			'url'  => 'https://evermoreskincare.com/',
			'cat'  => __( 'Skincare', 'zobo-d2c' ),
			'text' => __( 'Nopal-cactus skincare for reactive skin: stability and challenge testing, derm-testing coordination and a claims framework.', 'zobo-d2c' ),
		),
	);
}

/**
 * Who Zobo is for.
 *
 * @return array
 */
function zobod2c_audiences() {
	return array(
		array(
			'title' => __( 'First-time founders', 'zobo-d2c' ),
			'text'  => __( 'You have the idea and the drive, but not a map. We hand you one and walk it with you.', 'zobo-d2c' ),
		),
		array(
			'title' => __( 'D2C brands ready to scale', 'zobo-d2c' ),
			'text'  => __( 'You have product-market fit. Now you need more channels, better ads and a brand people remember.', 'zobo-d2c' ),
		),
		array(
			'title' => __( 'Makers & family businesses', 'zobo-d2c' ),
			'text'  => __( 'You already make something great. We package it, register it and put it online for the whole country.', 'zobo-d2c' ),
		),
	);
}

/**
 * Frequently asked questions.
 *
 * @return array
 */
function zobod2c_faqs() {
	return array(
		array(
			'q' => __( 'I only have an idea. Is that too early?', 'zobo-d2c' ),
			'a' => __( 'No, that is exactly where we like to start. The earlier we are involved, the fewer expensive mistakes you make on naming, packaging and stock.', 'zobo-d2c' ),
		),
		array(
			'q' => __( 'Can I pick just one service?', 'zobo-d2c' ),
			'a' => __( 'Yes. Many founders start with branding or a trademark and add the rest later. Every service is available on its own.', 'zobo-d2c' ),
		),
		array(
			'q' => __( 'How long does it take to launch?', 'zobo-d2c' ),
			'a' => __( 'It depends on the category and licences involved. A typical brand goes from idea to first orders in roughly 8–12 weeks. We share a week-by-week plan on our first call.', 'zobo-d2c' ),
		),
		array(
			'q' => __( 'Do you help find manufacturers?', 'zobo-d2c' ),
			'a' => __( 'Yes. We connect you with private-label and contract manufacturers, manage sampling and help negotiate MOQs and costing.', 'zobo-d2c' ),
		),
		array(
			'q' => __( 'Which tests and licences does my product need?', 'zobo-d2c' ),
			'a' => __( 'It depends on your category, claims and sales channels. We map every claim on your label to a specific NABL lab test and every channel to the licence it needs, so you never pay for tests you don’t need or ship a claim you can’t prove.', 'zobo-d2c' ),
		),
		array(
			'q' => __( 'Do you own labs?', 'zobo-d2c' ),
			'a' => __( 'No, and that is deliberate. We coordinate a network of NABL-accredited (ISO 17025) laboratories. Independent labs are what make your reports credible with regulators and retailers.', 'zobo-d2c' ),
		),
		array(
			'q' => __( 'Who owns the brand, trademark and accounts?', 'zobo-d2c' ),
			'a' => __( 'You do. Trademarks, domains, seller accounts and ad accounts are registered in your name from day one.', 'zobo-d2c' ),
		),
		array(
			'q' => __( 'What does it cost?', 'zobo-d2c' ),
			'a' => __( 'It depends on your category and how much of the journey you want us to run. Book a free call and we will send a fixed-scope proposal.', 'zobo-d2c' ),
		),
	);
}
