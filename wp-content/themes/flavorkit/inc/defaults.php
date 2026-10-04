<?php
/**
 * Default content, colour presets and font choices.
 *
 * Every piece of front-page copy can be overridden from
 * Appearance → Customize → FlavorKit. These defaults make the theme look
 * finished the moment it is activated, before any client content exists.
 *
 * @package FlavorKit
 */

defined( 'ABSPATH' ) || exit;

/**
 * Colour presets inspired by modern D2C food & beverage brands.
 *
 * @return array<string, array<string, string>>
 */
function flavorkit_presets() {
	return array(
		'soda'   => array(
			'label'     => __( 'Soda Pop (pink & sunshine)', 'flavorkit' ),
			'primary'   => '#ff4f8b',
			'secondary' => '#ffd23f',
			'accent'    => '#6c4cf1',
			'dark'      => '#1d1238',
			'cream'     => '#fff5e9',
		),
		'wicked' => array(
			'label'     => __( 'Wicked Orange (bold & quirky)', 'flavorkit' ),
			'primary'   => '#ff5a1f',
			'secondary' => '#ffe14d',
			'accent'    => '#00b39b',
			'dark'      => '#141414',
			'cream'     => '#fff3dc',
		),
		'spice'  => array(
			'label'     => __( 'Spice Route (red & turmeric)', 'flavorkit' ),
			'primary'   => '#c8241c',
			'secondary' => '#f7a823',
			'accent'    => '#1f6f43',
			'dark'      => '#2a1a12',
			'cream'     => '#fff6ea',
		),
		'fresh'  => array(
			'label'     => __( 'Fresh Fit (green & citrus)', 'flavorkit' ),
			'primary'   => '#1fa64a',
			'secondary' => '#ffc93c',
			'accent'    => '#ff6b35',
			'dark'      => '#0f2a1d',
			'cream'     => '#f3faec',
		),
		'mom'    => array(
			'label'     => __( 'Gentle Mom (coral & mint)', 'flavorkit' ),
			'primary'   => '#ff7a59',
			'secondary' => '#9fe0d3',
			'accent'    => '#5b5fef',
			'dark'      => '#2b2d42',
			'cream'     => '#fff8f1',
		),
		'berry'  => array(
			'label'     => __( 'Berry Night (purple & lime)', 'flavorkit' ),
			'primary'   => '#7b2ff7',
			'secondary' => '#c6f432',
			'accent'    => '#ff3d7f',
			'dark'      => '#160b2e',
			'cream'     => '#f6f1ff',
		),
		'custom' => array(
			'label' => __( 'Custom (use the colour pickers below)', 'flavorkit' ),
		),
	);
}

/**
 * Google Font choices. Key = family name, value = weights to request.
 *
 * @return array<string, string>
 */
function flavorkit_fonts() {
	return array(
		'Bricolage Grotesque' => '400;600;700;800',
		'DM Sans'             => '400;500;700',
		'Fredoka'             => '400;500;600;700',
		'Baloo 2'             => '400;600;700;800',
		'Poppins'             => '400;500;600;700;800',
		'Outfit'              => '400;500;600;700;800',
		'Syne'                => '500;600;700;800',
		'Space Grotesk'       => '400;500;700',
		'Fraunces'            => '400;600;700;900',
		'Playfair Display'    => '400;600;700;800',
		'Nunito'              => '400;600;700;800',
		'Inter'               => '400;500;600;700',
	);
}

/**
 * Front-page sections that can be enabled and reordered.
 *
 * @return array<string, string>
 */
function flavorkit_sections() {
	return array(
		'hero'       => __( 'Hero banner', 'flavorkit' ),
		'marquee'    => __( 'Scrolling marquee', 'flavorkit' ),
		'usp'        => __( 'USP badges', 'flavorkit' ),
		'categories' => __( 'Shop by category', 'flavorkit' ),
		'products'   => __( 'Bestseller products', 'flavorkit' ),
		'story'      => __( 'Brand story', 'flavorkit' ),
		'benefits'   => __( 'Why choose us', 'flavorkit' ),
		'reviews'    => __( 'Customer reviews', 'flavorkit' ),
		'ugc'        => __( 'Instagram / UGC grid', 'flavorkit' ),
		'faq'        => __( 'FAQ', 'flavorkit' ),
		'newsletter' => __( 'Newsletter / offer', 'flavorkit' ),
	);
}

/**
 * Default value for every theme mod.
 *
 * @return array<string, mixed>
 */
function flavorkit_defaults() {
	return array(
		// Brand & style.
		'preset'              => 'soda',
		'color_primary'       => '#ff4f8b',
		'color_secondary'     => '#ffd23f',
		'color_accent'        => '#6c4cf1',
		'color_dark'          => '#1d1238',
		'color_cream'         => '#fff5e9',
		'font_heading'        => 'Bricolage Grotesque',
		'font_body'           => 'DM Sans',
		'radius'              => 22,
		'button_style'        => 'sticker',
		'sections_order'      => 'hero,marquee,usp,categories,products,story,benefits,reviews,ugc,faq,newsletter',

		// Header.
		'announcement'        => 'Free shipping on orders above ₹499 | Use code HELLO10 for 10% off your first order | 100% natural, zero nasties',
		'header_cta_text'     => '',
		'header_cta_url'      => '',

		// Hero.
		'hero_eyebrow'        => 'New flavour just dropped',
		'hero_title'          => 'Snacks that taste *wicked* good.',
		'hero_text'           => 'Real ingredients, bold flavours and zero guilt. Crafted in small batches for people who refuse to choose between tasty and healthy.',
		'hero_btn1_text'      => 'Shop bestsellers',
		'hero_btn1_url'       => '',
		'hero_btn2_text'      => 'Our story',
		'hero_btn2_url'       => '#story',
		'hero_image'          => '',
		'hero_badges'         => 'No added sugar | 100% natural | Made in India',
		'hero_rating'         => '4.8/5 from 12,000+ reviews',

		// Marquee.
		'marquee_items'       => 'Real ingredients | Zero preservatives | Gut friendly | Small batch | Plant powered | Ships in 24 hrs',

		// USP.
		'usp_items'           => "Clean label | Nothing you can't pronounce | leaf\nHigh protein | Fuel that keeps you going | flame\nFast delivery | Pan-India in 2–5 days | truck\nLoved by 50K+ | Rated 4.8 by real customers | heart",

		// Categories.
		'cat_title'           => 'Shop by *craving*',
		'cat_items'           => "Snacks | pouch\nBeverages | can\nSpices | jar\nBreakfast | box\nCombos | bottle",

		// Products.
		'products_eyebrow'    => 'Fan favourites',
		'products_title'      => 'Our *bestsellers*',
		'products_source'     => 'best_selling',
		'products_count'      => 8,
		'products_btn_text'   => 'View all products',

		// Story.
		'story_eyebrow'       => 'Our story',
		'story_title'         => 'Started in a tiny kitchen. Now in *50,000+* homes.',
		'story_text'          => "We were tired of choosing between snacks that tasted great and snacks that were good for us. So we rolled up our sleeves and made our own — with real ingredients, honest labels and flavours that slap.\n\nEvery batch is made fresh, tested in-house and shipped straight to your door.",
		'story_btn_text'      => 'Read more',
		'story_btn_url'       => '',
		'story_image'         => '',
		'story_stats'         => "50K+ | happy customers\n4.8★ | average rating\n0g | added sugar",

		// Benefits.
		'benefits_eyebrow'    => 'Why you\'ll love it',
		'benefits_title'      => 'Good for you. *Great* to eat.',
		'benefits_items'      => "Real fruit & spices | Sourced directly from farmers across India. | leaf\nNo palm oil | Roasted, never fried in cheap oils. | drop\nProtein packed | Up to 12g protein per serve. | flame\nKid approved | Gentle flavours the whole family loves. | heart",

		// Reviews.
		'reviews_eyebrow'     => 'Reviews',
		'reviews_title'       => 'Loved by *50,000+* snackers',
		'reviews_items'       => "Aanya S. | Mumbai | Honestly the best healthy snack I've tried. The peri peri one is addictive! | 5\nRohan M. | Bengaluru | Finally a brand that doesn't taste like cardboard. My whole office is hooked. | 5\nPriya K. | Delhi | Love the clean ingredients and the packaging is so cute. Reordering every month. | 5\nKaran T. | Pune | Fast delivery, fresh product and super tasty. Highly recommend the combo packs. | 4\nMeera J. | Hyderabad | My kids ask for these every day and I don't feel guilty saying yes. | 5",

		// UGC.
		'ugc_title'           => 'Snack with us *@yourbrand*',
		'ugc_text'            => 'Tag us on Instagram for a chance to be featured.',
		'ugc_url'             => 'https://instagram.com/',
		'ugc_images'          => '',

		// FAQ.
		'faq_title'           => 'Got *questions?*',
		'faq_items'           => "Are your products 100% natural? | Yes. We use only real ingredients with no artificial colours, flavours or preservatives.\nHow long does delivery take? | Orders ship within 24 hours and arrive in 2–5 working days across India.\nDo you offer cash on delivery? | Yes, COD is available on most pin codes at checkout.\nWhat is your return policy? | If anything arrives damaged, message us within 48 hours and we'll replace it — no questions asked.\nAre your products safe for kids? | Absolutely. Our recipes are gentle, clean and loved by little ones.",

		// Newsletter.
		'newsletter_title'    => 'Get *10% off* your first order',
		'newsletter_text'     => 'Join the club for early drops, secret recipes and members-only offers.',
		'newsletter_shortcode' => '',

		// Footer & social.
		'footer_about'        => 'Bold flavours, clean ingredients and snacks you can feel good about. Made with love in India.',
		'footer_copyright'    => '',
		'social_instagram'    => 'https://instagram.com/',
		'social_facebook'     => 'https://facebook.com/',
		'social_youtube'      => '',
		'social_x'            => '',
		'social_linkedin'     => '',
		'whatsapp_number'     => '',
		'whatsapp_message'    => 'Hi! I have a question about your products.',
	);
}

/**
 * Get a theme mod with the FlavorKit default as fallback.
 *
 * @param string $key Setting key without prefix.
 * @return mixed
 */
function flavorkit_mod( $key ) {
	$defaults = flavorkit_defaults();
	$default  = isset( $defaults[ $key ] ) ? $defaults[ $key ] : '';
	return get_theme_mod( 'flavorkit_' . $key, $default );
}

/**
 * Resolve the active colour palette (preset or custom pickers).
 *
 * @return array<string, string>
 */
function flavorkit_colors() {
	$presets = flavorkit_presets();
	$preset  = flavorkit_mod( 'preset' );

	if ( 'custom' !== $preset && isset( $presets[ $preset ] ) ) {
		$p = $presets[ $preset ];
		return array(
			'primary'   => $p['primary'],
			'secondary' => $p['secondary'],
			'accent'    => $p['accent'],
			'dark'      => $p['dark'],
			'cream'     => $p['cream'],
		);
	}

	return array(
		'primary'   => flavorkit_mod( 'color_primary' ),
		'secondary' => flavorkit_mod( 'color_secondary' ),
		'accent'    => flavorkit_mod( 'color_accent' ),
		'dark'      => flavorkit_mod( 'color_dark' ),
		'cream'     => flavorkit_mod( 'color_cream' ),
	);
}
