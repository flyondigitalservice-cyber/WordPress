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
function zobo_journey() {
	return array(
		array(
			'phase'  => __( 'Shape it', 'zobo' ),
			'weeks'  => __( 'Week 1–3', 'zobo' ),
			'stages' => array(
				array(
					'icon'  => 'bulb',
					'title' => __( 'Idea & strategy', 'zobo' ),
					'text'  => __( 'Market sizing, competitor teardown, pricing and positioning, so you know who buys, why, and at what price before you spend on stock.', 'zobo' ),
				),
				array(
					'icon'  => 'pen',
					'title' => __( 'Name, logo & identity', 'zobo' ),
					'text'  => __( 'Brand name, logo, colours, type, tone of voice and packaging design built to stand out on a crowded shelf or scroll.', 'zobo' ),
				),
				array(
					'icon'  => 'shield',
					'title' => __( 'Trademark', 'zobo' ),
					'text'  => __( 'Search, class selection and filing so the name you fall in love with is actually yours to keep.', 'zobo' ),
				),
			),
		),
		array(
			'phase'  => __( 'Make it legal', 'zobo' ),
			'weeks'  => __( 'Week 2–6', 'zobo' ),
			'stages' => array(
				array(
					'icon'  => 'doc',
					'title' => __( 'Company & licences', 'zobo' ),
					'text'  => __( 'Company registration, GST, MSME/Udyam, FSSAI, IEC and category-specific approvals, filed in the right order.', 'zobo' ),
				),
				array(
					'icon'  => 'handshake',
					'title' => __( 'Vendor registration', 'zobo' ),
					'text'  => __( 'Seller onboarding and vendor codes with marketplaces, quick-commerce apps, modern trade and distributors.', 'zobo' ),
				),
			),
		),
		array(
			'phase'  => __( 'Make it real', 'zobo' ),
			'weeks'  => __( 'Week 4–10', 'zobo' ),
			'stages' => array(
				array(
					'icon'  => 'factory',
					'title' => __( 'Manufacturing & packaging', 'zobo' ),
					'text'  => __( 'Vetted private-label and contract manufacturers, sampling, MOQ negotiation, barcodes and print-ready packaging.', 'zobo' ),
				),
			),
		),
		array(
			'phase'  => __( 'Make it sell', 'zobo' ),
			'weeks'  => __( 'Week 8 onwards', 'zobo' ),
			'stages' => array(
				array(
					'icon'  => 'cart',
					'title' => __( 'Marketplaces', 'zobo' ),
					'text'  => __( 'Amazon, Flipkart, Myntra, Nykaa, Blinkit, Zepto and more: listings, A+ content, catalog SEO and account management.', 'zobo' ),
				),
				array(
					'icon'  => 'monitor',
					'title' => __( 'D2C website', 'zobo' ),
					'text'  => __( 'Fast Shopify or WordPress/WooCommerce stores with payments, COD, shipping and WhatsApp integrations, built to convert.', 'zobo' ),
				),
				array(
					'icon'  => 'chart',
					'title' => __( 'Performance marketing', 'zobo' ),
					'text'  => __( 'Meta, Google and marketplace ads run against ROAS and CAC targets, with creatives tested every week.', 'zobo' ),
				),
				array(
					'icon'  => 'star',
					'title' => __( 'Influencer marketing', 'zobo' ),
					'text'  => __( 'Creator discovery, briefs, UGC and seeding campaigns from nano to celebrity, tracked to sales, not just likes.', 'zobo' ),
				),
				array(
					'icon'  => 'tv',
					'title' => __( 'OTT & TVC ads', 'zobo' ),
					'text'  => __( 'Scripting, production and media buying for connected TV, OTT platforms and television when you are ready to go big.', 'zobo' ),
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
function zobo_services() {
	return array(
		array(
			'size'  => 'wide',
			'tone'  => 'ink',
			'tag'   => __( 'Brand', 'zobo' ),
			'title' => __( 'Branding & packaging that earns the first look', 'zobo' ),
			'items' => array( __( 'Naming', 'zobo' ), __( 'Logo & identity', 'zobo' ), __( 'Packaging', 'zobo' ), __( 'Brand book', 'zobo' ), __( 'Product photography', 'zobo' ) ),
		),
		array(
			'size'  => 'tall',
			'tone'  => 'accent',
			'tag'   => __( 'Legal', 'zobo' ),
			'title' => __( 'Paperwork, sorted', 'zobo' ),
			'items' => array( __( 'Trademark', 'zobo' ), __( 'Company registration', 'zobo' ), __( 'GST', 'zobo' ), __( 'FSSAI', 'zobo' ), __( 'MSME / Udyam', 'zobo' ), __( 'IEC', 'zobo' ), __( 'Vendor codes', 'zobo' ) ),
		),
		array(
			'size'  => '',
			'tone'  => 'lime',
			'tag'   => __( 'Supply', 'zobo' ),
			'title' => __( 'Manufacturing partners', 'zobo' ),
			'items' => array( __( 'Private label', 'zobo' ), __( 'Sampling', 'zobo' ), __( 'MOQ & costing', 'zobo' ) ),
		),
		array(
			'size'  => '',
			'tone'  => 'paper',
			'tag'   => __( 'Commerce', 'zobo' ),
			'title' => __( 'Marketplaces & D2C store', 'zobo' ),
			'items' => array( __( 'Amazon', 'zobo' ), __( 'Flipkart', 'zobo' ), __( 'Quick commerce', 'zobo' ), __( 'Shopify', 'zobo' ), __( 'WooCommerce', 'zobo' ) ),
		),
		array(
			'size'  => 'wide',
			'tone'  => 'paper',
			'tag'   => __( 'Growth', 'zobo' ),
			'title' => __( 'Performance, influencer and TV: every channel that moves sales', 'zobo' ),
			'items' => array( __( 'Meta ads', 'zobo' ), __( 'Google ads', 'zobo' ), __( 'Marketplace ads', 'zobo' ), __( 'Influencers & UGC', 'zobo' ), __( 'OTT', 'zobo' ), __( 'TVC production', 'zobo' ) ),
		),
	);
}

/**
 * Engagement plans. Prices are intentionally left as "on request".
 *
 * @return array
 */
function zobo_plans() {
	return array(
		array(
			'name'     => __( 'Spark', 'zobo' ),
			'for'      => __( 'You have an idea and need a brand.', 'zobo' ),
			'featured' => false,
			'items'    => array(
				__( 'Idea validation & positioning', 'zobo' ),
				__( 'Brand name & logo', 'zobo' ),
				__( 'Trademark filing', 'zobo' ),
				__( 'Company & GST registration', 'zobo' ),
				__( 'Packaging design', 'zobo' ),
			),
		),
		array(
			'name'     => __( 'Launch', 'zobo' ),
			'for'      => __( 'You want to go from idea to first orders.', 'zobo' ),
			'featured' => true,
			'items'    => array(
				__( 'Everything in Spark', 'zobo' ),
				__( 'All category licences', 'zobo' ),
				__( 'Manufacturer sourcing & sampling', 'zobo' ),
				__( 'Marketplace onboarding & listings', 'zobo' ),
				__( 'D2C website', 'zobo' ),
				__( 'Launch campaign: ads + creators', 'zobo' ),
			),
		),
		array(
			'name'     => __( 'Scale', 'zobo' ),
			'for'      => __( 'You are selling and want to grow faster.', 'zobo' ),
			'featured' => false,
			'items'    => array(
				__( 'Performance marketing retainer', 'zobo' ),
				__( 'Influencer & UGC programme', 'zobo' ),
				__( 'Marketplace account management', 'zobo' ),
				__( 'Quick-commerce & modern trade', 'zobo' ),
				__( 'OTT & TVC campaigns', 'zobo' ),
			),
		),
	);
}

/**
 * Who Zobo is for.
 *
 * @return array
 */
function zobo_audiences() {
	return array(
		array(
			'title' => __( 'First-time founders', 'zobo' ),
			'text'  => __( 'You have the idea and the drive, but not a map. We hand you one and walk it with you.', 'zobo' ),
		),
		array(
			'title' => __( 'D2C brands ready to scale', 'zobo' ),
			'text'  => __( 'You have product-market fit. Now you need more channels, better ads and a brand people remember.', 'zobo' ),
		),
		array(
			'title' => __( 'Makers & family businesses', 'zobo' ),
			'text'  => __( 'You already make something great. We package it, register it and put it online for the whole country.', 'zobo' ),
		),
	);
}

/**
 * Frequently asked questions.
 *
 * @return array
 */
function zobo_faqs() {
	return array(
		array(
			'q' => __( 'I only have an idea. Is that too early?', 'zobo' ),
			'a' => __( 'No, that is exactly where we like to start. The earlier we are involved, the fewer expensive mistakes you make on naming, packaging and stock.', 'zobo' ),
		),
		array(
			'q' => __( 'Can I pick just one service?', 'zobo' ),
			'a' => __( 'Yes. Many founders start with branding or a trademark and add the rest later. Every service is available on its own.', 'zobo' ),
		),
		array(
			'q' => __( 'How long does it take to launch?', 'zobo' ),
			'a' => __( 'It depends on the category and licences involved. A typical brand goes from idea to first orders in roughly 8–12 weeks. We share a week-by-week plan on our first call.', 'zobo' ),
		),
		array(
			'q' => __( 'Do you help find manufacturers?', 'zobo' ),
			'a' => __( 'Yes. We connect you with private-label and contract manufacturers, manage sampling and help negotiate MOQs and costing.', 'zobo' ),
		),
		array(
			'q' => __( 'Who owns the brand, trademark and accounts?', 'zobo' ),
			'a' => __( 'You do. Trademarks, domains, seller accounts and ad accounts are registered in your name from day one.', 'zobo' ),
		),
		array(
			'q' => __( 'What does it cost?', 'zobo' ),
			'a' => __( 'It depends on your category and how much of the journey you want us to run. Book a free call and we will send a fixed-scope proposal.', 'zobo' ),
		),
	);
}
