<?php
/**
 * Site facts shared by llms.txt, schema, Markdown, WebMCP and MCP.
 *
 * Everything is filterable through `bmar_config` so values can be changed
 * without editing the plugin.
 *
 * @package Bombay_Agent_Ready
 */

defined( 'ABSPATH' ) || exit;

class BMAR_Config {

	/**
	 * Cached config.
	 *
	 * @var array|null
	 */
	private static $config = null;

	/**
	 * Return the site config.
	 *
	 * @return array
	 */
	public static function get() {
		if ( null !== self::$config ) {
			return self::$config;
		}

		$home = home_url( '/' );

		$config = array(
			'name'           => 'Bombay Marketing',
			'summary'        => 'Bombay Marketing is an ecommerce growth agency in Mumbai and Navi Mumbai. We build, launch and scale ecommerce brands: Shopify and WooCommerce stores, Amazon, Flipkart, Myntra, Blinkit, Zepto and Instamart seller onboarding, SEO, performance marketing and packaging design. We also sell a bundle of five WordPress and WooCommerce plugins.',

			// Content-Signal directive (https://contentsignals.org/).
			'content_signal' => 'search=yes, ai-input=yes, ai-train=no',

			'contact'        => array(
				'telephone'     => '+91-8767697389',
				'address'       => 'Shamji Damji Udyog Bhavan, Veera Desai Road, Near Hotel Courtyard, Andheri West, Mumbai, Maharashtra 400053, India',
				'opening_hours' => 'Monday to Saturday, 10:30 to 19:00 IST',
				'contact_url'   => home_url( '/contact/' ),
			),

			// Products with public prices. Used for Offer schema, llms.txt, Markdown and tools.
			'offers'         => array(
				array(
					'name'        => 'WordPress Plugin Bundle by Bombay Marketing',
					'description' => 'Five WordPress and WooCommerce plugins in one bundle, including Geo Page Generator, Currency Switcher and Packing Slip & Invoice. Free for Bombay Marketing clients.',
					'url'         => home_url( '/plugins/' ),
					'prices'      => array(
						array(
							'price'    => '5000',
							'currency' => 'INR',
						),
						array(
							'price'    => '50',
							'currency' => 'USD',
						),
					),
				),
			),

			// Services are quoted per project; listed so agents know where to ask.
			'services'       => array(
				array( 'Ecommerce website development', home_url( '/ecommerce-website-development-mumbai/' ) ),
				array( 'Shopify store setup', home_url( '/shopify-store-setup/' ) ),
				array( 'Amazon seller onboarding', home_url( '/amazon-seller-onboarding/' ) ),
				array( 'Blinkit seller onboarding', home_url( '/blinkit-seller-onboarding/' ) ),
				array( 'SEO company in Mumbai', home_url( '/seo-company-in-mumbai/' ) ),
				array( 'Performance marketing for ecommerce', home_url( '/performance-marketing-for-ecommerce/' ) ),
				array( 'Packaging design and branding', home_url( '/packaging-design-branding-mumbai/' ) ),
			),

			'key_pages'      => array(
				array( 'Home', $home, 'Overview of the agency and its services' ),
				array( 'Services', home_url( '/services/' ), 'All ecommerce services' ),
				array( 'Plugins and pricing', home_url( '/plugins/' ), 'WordPress plugin bundle: 5 plugins for INR 5,000 (USD 50)' ),
				array( 'Our work', home_url( '/our-work/' ), 'Case studies and portfolio' ),
				array( 'About us', home_url( '/about-us/' ), 'Team and company background' ),
				array( 'Guides', home_url( '/guides/' ), 'Ecommerce guides' ),
				array( 'Blog', home_url( '/blog/' ), 'Articles and news' ),
				array( 'Contact', home_url( '/contact/' ), 'Request a quote or ask a question' ),
			),
		);

		/**
		 * Filter the agent-readiness config.
		 *
		 * @param array $config Config.
		 */
		self::$config = apply_filters( 'bmar_config', $config );

		return self::$config;
	}

	/**
	 * Human readable price list, e.g. "INR 5,000 / USD 50".
	 *
	 * @param array $offer Offer.
	 * @return string
	 */
	public static function price_label( $offer ) {
		$parts = array();
		foreach ( $offer['prices'] as $p ) {
			$parts[] = $p['currency'] . ' ' . number_format( (float) $p['price'] );
		}
		return implode( ' / ', $parts );
	}
}
