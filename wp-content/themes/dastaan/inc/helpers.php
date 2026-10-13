<?php
/**
 * Core helpers: option access, defaults, icons, sanitizers.
 *
 * @package Dastaan
 */

defined( 'ABSPATH' ) || exit;

/**
 * Theme-wide defaults. Every Customizer setting has a sensible, brand-specific default
 * so the theme looks complete the moment it is activated.
 *
 * @return array<string,mixed>
 */
function dastaan_defaults() {
	static $defaults = null;
	if ( null !== $defaults ) {
		return $defaults;
	}

	$img = DASTAAN_URI . 'assets/images/';

	$defaults = array(
		// Brand & contact.
		'brand_tagline'        => __( 'Grace & elegance in every unstitched outfit', 'dastaan' ),
		'whatsapp_number'      => '917208200521',
		'whatsapp_message'     => __( 'Hello Dastaan, I would like some help choosing a piece.', 'dastaan' ),
		'phone_number'         => '+91 72082 00521',
		'contact_email'        => 'hello@dastaanbyartisans.in',
		'studio_address'       => __( 'Seawoods, Navi Mumbai, Maharashtra 400706, India', 'dastaan' ),
		'studio_hours'         => __( 'Mon – Sat, 11:00 AM – 8:00 PM (by appointment)', 'dastaan' ),
		'social_instagram'     => 'https://www.instagram.com/dastaanbyartisans/',
		'social_facebook'      => 'https://www.facebook.com/dastaanbyartisans/',
		'social_youtube'       => '',
		'social_pinterest'     => '',
		'show_whatsapp_float'  => 1,

		// Announcement bar.
		'announcement_enable'    => 1,
		'announcement_text'      => __( 'The Festive Edit is here — complimentary shipping across India on every order', 'dastaan' ),
		'announcement_link'      => '',
		'announcement_link_text' => __( 'Shop now', 'dastaan' ),
		'announcement_countdown' => '',
		'announcement_secondary' => __( 'Handcrafted by real artisans · Seawoods, Navi Mumbai', 'dastaan' ),

		// Header.
		'header_sticky'       => 1,
		'header_show_search'  => 1,
		'header_show_wishlist'=> 1,
		'wishlist_url'        => '/wishlist/',
		'header_promo_text'   => __( 'Need help? Chat with a stylist', 'dastaan' ),

		// Hero slider.
		'hero_autoplay'        => 1,
		'hero_interval'        => 6,
		'hero_1_image'         => $img . 'hero/hero-chikankari.jpg',
		'hero_1_eyebrow'       => __( 'The Chikankari Edit', 'dastaan' ),
		'hero_1_heading'       => __( 'Woven stories, stitched by hand', 'dastaan' ),
		'hero_1_text'          => __( 'Pure organza & mul chanderi unstitched sets with fine Lucknowi chikankari, pearl cut-dana and shadow work — sourced directly from the artisans who make them.', 'dastaan' ),
		'hero_1_button'        => __( 'Shop the Edit', 'dastaan' ),
		'hero_1_url'           => '/product-category/chikankari/',
		'hero_1_button2'       => __( 'Our Story', 'dastaan' ),
		'hero_1_url2'          => '/our-story/',
		'hero_1_theme'         => 'light',
		'hero_1_focus'         => 'right',
		'hero_1_coupon'        => '',
		'hero_1_mobile_image'  => '',
		'hero_2_image'         => $img . 'hero/hero-zardozi.jpg',
		'hero_2_eyebrow'       => __( 'Zardozi & Dabka', 'dastaan' ),
		'hero_2_heading'       => __( 'Festive gold, worked by karigars', 'dastaan' ),
		'hero_2_text'          => __( 'Statement borders, sequins and sitara-moti highlights on pure organza silk. Limited pieces — most are one of one.', 'dastaan' ),
		'hero_2_button'        => __( 'Explore Zardozi', 'dastaan' ),
		'hero_2_url'           => '/product-category/zardozi/',
		'hero_2_button2'       => '',
		'hero_2_url2'          => '',
		'hero_2_theme'         => 'dark',
		'hero_2_focus'         => 'center',
		'hero_2_coupon'        => '',
		'hero_2_mobile_image'  => '',
		'hero_3_image'         => $img . 'hero/hero-pastel-organza.jpg',
		'hero_3_eyebrow'       => __( 'New Arrivals', 'dastaan' ),
		'hero_3_heading'       => __( 'The Pastel Organza Collection', 'dastaan' ),
		'hero_3_text'          => __( 'Powder blue, onion pink, lavender and mango — airy organza and chanderi with gota patti and mukaish, ready to be tailored your way.', 'dastaan' ),
		'hero_3_button'        => __( 'Shop New Arrivals', 'dastaan' ),
		'hero_3_url'           => '/shop/?orderby=date',
		'hero_3_button2'       => '',
		'hero_3_url2'          => '',
		'hero_3_theme'         => 'light',
		'hero_3_focus'         => 'center',
		'hero_3_coupon'        => '',
		'hero_3_mobile_image'  => '',
		'hero_4_image'         => '',
		'hero_4_eyebrow'       => '',
		'hero_4_heading'       => '',
		'hero_4_text'          => '',
		'hero_4_button'        => '',
		'hero_4_url'           => '',
		'hero_4_button2'       => '',
		'hero_4_url2'          => '',
		'hero_4_theme'         => 'light',
		'hero_4_focus'         => 'center',
		'hero_4_coupon'        => '',
		'hero_4_mobile_image'  => '',

		// Category circles.
		'circles_enable'   => 1,
		'circles_title'    => __( 'Shop by craft', 'dastaan' ),
		'circles_source'   => 'woocommerce',
		'circles_count'    => 10,
		'circles_exclude'  => 'uncategorized',

		// USP bar.
		'usp_enable'   => 1,
		'usp_1_icon'   => 'truck',
		'usp_1_title'  => __( 'Free shipping in India', 'dastaan' ),
		'usp_1_text'   => __( 'Insured, tracked dispatch', 'dastaan' ),
		'usp_2_icon'   => 'hand',
		'usp_2_title'  => __( 'Real artisans, real craft', 'dastaan' ),
		'usp_2_text'   => __( 'Sourced directly from karigars', 'dastaan' ),
		'usp_3_icon'   => 'exchange',
		'usp_3_title'  => __( 'Easy 48-hour exchange', 'dastaan' ),
		'usp_3_text'   => __( 'After delivery, no questions asked', 'dastaan' ),
		'usp_4_icon'   => 'shield',
		'usp_4_title'  => __( 'Secure payments', 'dastaan' ),
		'usp_4_text'   => __( 'UPI · Cards · Net banking · EMI', 'dastaan' ),

		// Featured products.
		'featured_enable'    => 1,
		'featured_eyebrow'   => __( 'Curated for you', 'dastaan' ),
		'featured_title'     => __( 'New in the studio', 'dastaan' ),
		'featured_source'    => 'recent',
		'featured_category'  => '',
		'featured_count'     => 8,
		'featured_link_text' => __( 'View all pieces', 'dastaan' ),
		'bestseller_enable'  => 1,
		'bestseller_eyebrow' => __( 'Most loved', 'dastaan' ),
		'bestseller_title'   => __( 'Bestsellers', 'dastaan' ),
		'bestseller_source'  => 'bestselling',
		'bestseller_category'=> '',
		'bestseller_count'   => 8,

		// Offer banners.
		'banners_enable'  => 1,
		'banner_1_image'  => $img . 'categories/festive-edit.jpg',
		'banner_1_eyebrow'=> __( 'Limited time', 'dastaan' ),
		'banner_1_title'  => __( 'The Festive Edit', 'dastaan' ),
		'banner_1_text'   => __( 'Up to 15% off with code FESTIVE15', 'dastaan' ),
		'banner_1_button' => __( 'Shop festive', 'dastaan' ),
		'banner_1_url'    => '/product-category/festive-edit/',
		'banner_2_image'  => $img . 'categories/pure-organza.jpg',
		'banner_2_eyebrow'=> __( 'Just landed', 'dastaan' ),
		'banner_2_title'  => __( 'Pure Organza Silk', 'dastaan' ),
		'banner_2_text'   => __( 'Sheer, luminous, embroidered by hand', 'dastaan' ),
		'banner_2_button' => __( 'Discover', 'dastaan' ),
		'banner_2_url'    => '/product-category/pure-organza/',
		'banner_3_image'  => $img . 'categories/zardozi.jpg',
		'banner_3_eyebrow'=> __( 'Trousseau', 'dastaan' ),
		'banner_3_title'  => __( 'Bridal & Occasion', 'dastaan' ),
		'banner_3_text'   => __( 'Zardozi heirlooms for the big days', 'dastaan' ),
		'banner_3_button' => __( 'Shop bridal', 'dastaan' ),
		'banner_3_url'    => '/product-category/zardozi/',

		// Craft showcase (large tiles).
		'crafts_enable'  => 1,
		'crafts_eyebrow' => __( 'Heritage techniques', 'dastaan' ),
		'crafts_title'   => __( 'Every piece carries a craft', 'dastaan' ),
		'crafts_text'    => __( 'Zardozi, Chikankari, Jaali, Pitta, Mukaish and Gota Patti — practised for generations, brought to you without middlemen.', 'dastaan' ),
		'crafts_count'   => 5,

		// Reels.
		'reels_enable'  => 1,
		'reels_eyebrow' => __( 'Watch & shop', 'dastaan' ),
		'reels_title'   => __( 'Dastaan in motion', 'dastaan' ),
		'reels_text'    => __( 'Fabric fall, embroidery sheen and drape — see the pieces move before you choose.', 'dastaan' ),
		'reels_count'   => 6,

		// Instagram.
		'instagram_enable'    => 1,
		'instagram_title'     => __( 'From our Instagram', 'dastaan' ),
		'instagram_handle'    => 'dastaanbyartisans',
		'instagram_shortcode' => '',
		'instagram_count'     => 8,

		// Testimonials.
		'testimonials_enable'  => 1,
		'testimonials_eyebrow' => __( 'Rave reviews', 'dastaan' ),
		'testimonials_title'   => __( 'Loved by women who know their craft', 'dastaan' ),
		'testimonial_1_name'   => 'Minakshi Jain',
		'testimonial_1_text'   => __( 'The gold zari work on this emerald green piece is even more stunning in person. The embroidery along the hem is so dense and high-quality, and the diagonal sequins catch the light beautifully. I received so many compliments!', 'dastaan' ),
		'testimonial_1_product'=> __( 'The Emerald', 'dastaan' ),
		'testimonial_1_rating' => 5,
		'testimonial_1_image'  => '',
		'testimonial_2_name'   => 'Sonia Madan',
		'testimonial_2_text'   => __( 'Beautiful fabric and exquisite pitta work. The linen feels very high-quality and the organza dupatta is just gorgeous. It is even better in person. So glad I added this to my ethnic collection!', 'dastaan' ),
		'testimonial_2_product'=> __( 'Noor – Sky Blue Pure Organza Pitta Work', 'dastaan' ),
		'testimonial_2_rating' => 5,
		'testimonial_2_image'  => '',
		'testimonial_3_name'   => 'Ritika Shah',
		'testimonial_3_text'   => __( 'I had the kurta stitched by my tailor and the chikankari placement was perfect — neck panel, sleeves, everything aligned. The team even shared a stitching guide on WhatsApp.', 'dastaan' ),
		'testimonial_3_product'=> __( 'Pearl White Mul Chanderi Chikankari', 'dastaan' ),
		'testimonial_3_rating' => 5,
		'testimonial_3_image'  => '',
		'testimonial_4_name'   => '',
		'testimonial_4_text'   => '',
		'testimonial_4_product'=> '',
		'testimonial_4_rating' => 5,
		'testimonial_4_image'  => '',

		// Behind the scenes.
		'bts_enable'  => 1,
		'bts_eyebrow' => __( 'Dastaan by Artisans', 'dastaan' ),
		'bts_title'   => __( 'Behind the scenes', 'dastaan' ),
		'bts_text'    => __( 'A glimpse into the hands, stitches and traditions that bring every Dastaan creation to life. We work directly with artisan families in Lucknow, Bareilly and Jaipur — so every rupee you spend reaches the people who hold the needle.', 'dastaan' ),
		'bts_image_1' => $img . 'categories/chikankari.jpg',
		'bts_image_2' => $img . 'categories/zardozi.jpg',
		'bts_image_3' => $img . 'categories/gotta-patti.jpg',
		'bts_button'  => __( 'Meet the artisans', 'dastaan' ),
		'bts_url'     => '/our-story/',
		'bts_crafts'  => 'Zardozi, Chikankari, Jaali, Pitta, Mukaish, Gota Patti',

		// Studio strip.
		'studio_enable' => 1,
		'studio_title'  => __( 'Visit the studio in Seawoods', 'dastaan' ),
		'studio_text'   => __( 'Touch the fabrics, see the embroidery under daylight and get stitching advice from our stylists. Private appointments available for trousseau shopping.', 'dastaan' ),
		'studio_button' => __( 'Book an appointment', 'dastaan' ),
		'studio_url'    => '',
		'studio_image'  => $img . 'hero/hero-pastel-organza.jpg',

		// SEO content + FAQ.
		'seo_enable'   => 1,
		'seo_title'    => __( 'Premium unstitched dress material for women — handcrafted, artisan-sourced', 'dastaan' ),
		'seo_text'     => __( "Dastaan by Artisans is a Navi Mumbai based studio sourcing luxury unstitched dress material directly from master artisans across India. Every set — kurta fabric, dupatta and bottom — is crafted in pure organza silk, mul chanderi, viscose georgette, linen or tussar silk, and finished with genuine hand embroidery: Lucknowi chikankari, zardozi and dabka, gota patti, mukaish, pitta and jaali work.\n\nBecause our pieces are unstitched, you choose the silhouette: anarkali, straight kurta set, sharara or co-ord. Prices range from ₹5,000 to ₹25,000, reflecting the hours of handwork in each piece. Most designs are limited to one or two sets, so what you wear stays uniquely yours.", 'dastaan' ),
		'faq_1_q'      => __( 'What does an unstitched dress material set include?', 'dastaan' ),
		'faq_1_a'      => __( 'Most Dastaan sets are three pieces: kurta fabric (approx. 3.25 m), dupatta (approx. 2.75 m) and bottom fabric (approx. 2.5 m). Exact lengths and contents are listed on each product page.', 'dastaan' ),
		'faq_2_q'      => __( 'Do you offer stitching or tailoring?', 'dastaan' ),
		'faq_2_a'      => __( 'We sell unstitched fabric so you can tailor it to your silhouette. On request, our studio in Seawoods can recommend trusted tailors and share a stitching guide on WhatsApp.', 'dastaan' ),
		'faq_3_q'      => __( 'How should I care for hand-embroidered fabric?', 'dastaan' ),
		'faq_3_a'      => __( 'Dry clean only for zardozi, dabka, mukaish and sequin work. Fine chikankari on mul or chanderi may be gently hand-washed in cold water. Always store folded in muslin, away from direct sunlight.', 'dastaan' ),
		'faq_4_q'      => __( 'What is your exchange policy?', 'dastaan' ),
		'faq_4_a'      => __( 'Exchanges are accepted within 48 hours of delivery for unused, uncut fabric with tags intact. Because every piece is handcrafted, slight variations in embroidery are natural and not considered defects.', 'dastaan' ),

		// Footer.
		'footer_about'         => __( 'An artisan-source studio in Seawoods, Navi Mumbai. We bring handcrafted unstitched dress material from master karigars to women who value real craft.', 'dastaan' ),
		'footer_shop_title'    => __( 'Shop', 'dastaan' ),
		'footer_help_title'    => __( 'Help', 'dastaan' ),
		'footer_studio_title'  => __( 'Studio', 'dastaan' ),
		'newsletter_enable'    => 1,
		'newsletter_title'     => __( 'Join the Dastaan circle', 'dastaan' ),
		'newsletter_text'      => __( 'New arrivals, artisan stories and private previews — once a fortnight, never spam.', 'dastaan' ),
		'newsletter_action'    => '',
		'newsletter_success'   => __( 'Shukriya! You are on the list.', 'dastaan' ),
		'footer_payment_icons' => 1,
		'footer_copyright'     => __( '© {year} Dastaan by Artisans. All rights reserved. Handcrafted in India.', 'dastaan' ),
		'footer_legal_text'    => '',

		// Shop.
		'shop_columns'         => 4,
		'shop_per_page'        => 12,
		'shop_show_sidebar'    => 1,
		'shop_hover_image'     => 1,
		'shop_quick_add'       => 1,
		'shop_new_badge_days'  => 30,
		'shop_sale_badge'      => 1,

		// Product page.
		'product_video_position'  => 'second',
		'product_video_autoplay'  => 1,
		'product_show_whatsapp'   => 1,
		'product_whatsapp_text'   => __( 'Ask a stylist about this piece', 'dastaan' ),
		'product_show_buynow'     => 1,
		'product_delivery_text'   => __( 'Dispatched within 2–4 working days · Free insured shipping across India · Delivered in 3–7 days', 'dastaan' ),
		'product_trust_1'         => __( '100% genuine hand embroidery', 'dastaan' ),
		'product_trust_2'         => __( 'Secure UPI, card & EMI payments', 'dastaan' ),
		'product_trust_3'         => __( '48-hour exchange after delivery', 'dastaan' ),
		'product_care_text'       => __( "Crafted with premium fabrics and delicate handcrafted, traditional artisanal detailing. Each piece carries the individuality of the artisan's hand, so minor variations in embroidery, texture, weave and colour are natural — making every creation unique.\n\n→ Dry clean only to preserve delicate embroidery and embellishments.\n→ Gentle hand wash recommended for fine chikankari on cotton or mul.\n→ Store folded in muslin, away from direct sunlight and moisture.", 'dastaan' ),
		'product_shipping_text'   => __( "Free insured shipping across India. Orders are dispatched within 2–4 working days and typically delivered within 3–7 days. International shipping is available on request via WhatsApp.\n\nExchanges are accepted within 48 hours of delivery for uncut, unused fabric with tags intact.", 'dastaan' ),
		'product_size_guide_page' => 0,
		'product_sticky_atc'      => 1,
		'product_related_count'   => 4,

		// Tracking & privacy.
		'fb_pixel_id'          => '',
		'fb_domain_verify'     => '',
		'ga4_id'               => '',
		'clarity_id'           => '',
		'google_verify'        => '',
		'consent_enable'       => 1,
		'consent_text'         => __( 'We use cookies to improve your experience and measure our campaigns. By continuing you agree to our Privacy Policy.', 'dastaan' ),
		'consent_accept'       => __( 'Accept', 'dastaan' ),
		'consent_decline'      => __( 'Decline', 'dastaan' ),
		'ai_crawlers'          => 'allow',
		'llms_enable'          => 1,
		'llms_extra'           => '',
		'xmlrpc_disable'       => 1,
		'remove_jquery_migrate'=> 1,

		// Design.
		'color_primary'   => '#6E1E2B',
		'color_accent'    => '#B8933E',
		'color_bg'        => '#FAF7F2',
		'color_text'      => '#1F1A17',
		'google_fonts'    => 1,
		'container_width' => 1360,
		'button_radius'   => 2,
	);

	/**
	 * Filter the theme defaults.
	 *
	 * @param array $defaults Defaults.
	 */
	$defaults = apply_filters( 'dastaan_defaults', $defaults );

	return $defaults;
}

/**
 * Get a theme option with its default.
 *
 * @param string $key     Option key.
 * @param mixed  $default Explicit default (optional).
 * @return mixed
 */
function dastaan_option( $key, $default = null ) {
	$defaults = dastaan_defaults();
	if ( null === $default ) {
		$default = isset( $defaults[ $key ] ) ? $defaults[ $key ] : '';
	}
	$value = get_theme_mod( $key, $default );

	return apply_filters( 'dastaan_option_' . $key, $value );
}

/**
 * Resolve a possibly relative URL saved in options into an absolute, safe URL.
 *
 * @param string $url URL or path.
 * @return string
 */
function dastaan_url( $url ) {
	$url = trim( (string) $url );
	if ( '' === $url ) {
		return '';
	}
	if ( 0 === strpos( $url, '/' ) && 0 !== strpos( $url, '//' ) ) {
		return esc_url( home_url( $url ) );
	}

	return esc_url( $url );
}

/**
 * First published page matching one of the given slugs (lets fallbacks follow the site's real pages).
 *
 * @param string[] $slugs Candidate slugs, most preferred first.
 * @return WP_Post|null
 */
function dastaan_find_page( $slugs ) {
	foreach ( (array) $slugs as $slug ) {
		$page = get_page_by_path( $slug );
		if ( $page && 'publish' === $page->post_status ) {
			return $page;
		}
	}

	return null;
}

/**
 * Is a post built with Elementor (its content is Elementor's, not the block editor's)?
 *
 * @param int $post_id Post ID.
 * @return bool
 */
function dastaan_is_elementor_page( $post_id ) {
	return $post_id && did_action( 'elementor/loaded' ) && 'builder' === get_post_meta( $post_id, '_elementor_edit_mode', true );
}

/**
 * Wishlist page URL: the active wishlist plugin's page first, then the Customizer value
 * (a relative path is only used when that page exists, so the icon never links to a 404).
 *
 * @return string Escaped URL or ''.
 */
function dastaan_wishlist_url() {
	$plugin_pages = array( 'wcboost_wishlist_page_id', 'yith_wcwl_wishlist_page_id', 'woosw_page_id' );
	foreach ( $plugin_pages as $option ) {
		$page_id = (int) get_option( $option );
		if ( $page_id && 'publish' === get_post_status( $page_id ) ) {
			return esc_url( get_permalink( $page_id ) );
		}
	}
	$url = trim( (string) dastaan_option( 'wishlist_url' ) );
	if ( '' === $url ) {
		return '';
	}
	if ( 0 === strpos( $url, '/' ) && 0 !== strpos( $url, '//' ) && ! dastaan_find_page( array( trim( $url, '/' ) ) ) ) {
		return '';
	}

	return dastaan_url( $url );
}

/**
 * Build a WhatsApp click-to-chat URL.
 *
 * @param string $message Optional message.
 * @return string
 */
function dastaan_whatsapp_url( $message = '' ) {
	$number = preg_replace( '/\D+/', '', (string) dastaan_option( 'whatsapp_number' ) );
	if ( ! $number ) {
		return '';
	}
	$message = '' === $message ? dastaan_option( 'whatsapp_message' ) : $message;

	return esc_url( 'https://wa.me/' . $number . ( $message ? '?text=' . rawurlencode( $message ) : '' ) );
}

/**
 * Inline SVG icon library. All icons are decorative by default (aria-hidden).
 *
 * @param string $name  Icon name.
 * @param array  $args  { size (int), class (string), label (string) }.
 * @return string
 */
function dastaan_icon( $name, $args = array() ) {
	$args  = wp_parse_args( $args, array( 'size' => 20, 'class' => '', 'label' => '' ) );
	$icons = array(
		'search'    => '<circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/>',
		'user'      => '<circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/>',
		'heart'     => '<path d="M12 20.5s-7.5-4.6-9.3-9.4C1.4 7.6 3.6 4.5 6.8 4.5c2 0 3.5 1 4.3 2.4h1.8c.8-1.4 2.3-2.4 4.3-2.4 3.2 0 5.4 3.1 4.1 6.6-1.8 4.8-9.3 9.4-9.3 9.4Z"/>',
		'bag'       => '<path d="M6 8h12l1 13H5L6 8Z"/><path d="M9 8V6a3 3 0 0 1 6 0v2"/>',
		'menu'      => '<path d="M4 7h16M4 12h16M4 17h16"/>',
		'close'     => '<path d="m6 6 12 12M18 6 6 18"/>',
		'chevron-down' => '<path d="m6 9 6 6 6-6"/>',
		'chevron-left' => '<path d="m15 6-6 6 6 6"/>',
		'chevron-right'=> '<path d="m9 6 6 6-6 6"/>',
		'arrow-right'  => '<path d="M4 12h16m-6-6 6 6-6 6"/>',
		'arrow-up'     => '<path d="M12 20V4m-6 6 6-6 6 6"/>',
		'play'      => '<path d="M8 5v14l11-7L8 5Z" fill="currentColor" stroke="none"/>',
		'pause'     => '<path d="M7 5h4v14H7zM13 5h4v14h-4z" fill="currentColor" stroke="none"/>',
		'whatsapp'  => '<path d="M12 3a9 9 0 0 0-7.7 13.6L3 21l4.5-1.2A9 9 0 1 0 12 3Z"/><path d="M9.2 8.6c.2-.4.4-.4.7-.4h.5c.2 0 .4 0 .5.4l.7 1.7c.1.2 0 .4-.1.5l-.5.6c-.1.1-.2.3 0 .5a6.7 6.7 0 0 0 3.2 2.8c.2.1.4 0 .5-.1l.7-.8c.2-.2.3-.2.6-.1l1.6.8c.3.1.4.2.4.4a2 2 0 0 1-.4 1.2c-.4.5-1.2 1-2 .9a8 8 0 0 1-6.5-5.4c-.4-1 0-2.2.1-3Z" fill="currentColor" stroke="none"/>',
		'instagram' => '<rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor"/>',
		'facebook'  => '<path d="M14 8h3V4h-3a4 4 0 0 0-4 4v3H7v4h3v6h4v-6h3l1-4h-4V8Z"/>',
		'youtube'   => '<path d="M3 8.5A3.5 3.5 0 0 1 6.5 5h11A3.5 3.5 0 0 1 21 8.5v7a3.5 3.5 0 0 1-3.5 3.5h-11A3.5 3.5 0 0 1 3 15.5v-7Z"/><path d="m10 9 5 3-5 3V9Z" fill="currentColor" stroke="none"/>',
		'pinterest' => '<circle cx="12" cy="12" r="9"/><path d="M11 21c.4-2 1.1-5.3 1.4-6.4M9.5 9.5c0-2 1.6-3.5 3.5-3.5s3 1.3 3 3c0 2.7-1.5 5-3.5 5-1 0-1.7-.7-1.5-1.6"/>',
		'phone'     => '<path d="M5 4h4l2 5-2.5 1.5a11 11 0 0 0 5 5L15 13l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 6a2 2 0 0 1 2-2Z"/>',
		'mail'      => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/>',
		'map-pin'   => '<path d="M12 21s-7-6.2-7-11a7 7 0 0 1 14 0c0 4.8-7 11-7 11Z"/><circle cx="12" cy="10" r="2.5"/>',
		'clock'     => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
		'truck'     => '<path d="M3 7h11v9H3zM14 10h4l3 3v3h-7z"/><circle cx="7" cy="18" r="1.8"/><circle cx="17" cy="18" r="1.8"/>',
		'shield'    => '<path d="M12 3 4 6v6c0 5 3.5 8 8 9 4.5-1 8-4 8-9V6l-8-3Z"/><path d="m9 12 2 2 4-4"/>',
		'exchange'  => '<path d="M4 9h13l-3-3M20 15H7l3 3"/>',
		'hand'      => '<path d="M8 13V5.5a1.5 1.5 0 0 1 3 0V12M11 11V4.5a1.5 1.5 0 0 1 3 0V12M14 11V6.5a1.5 1.5 0 0 1 3 0V13"/><path d="M17 13v2a6 6 0 0 1-6 6h-1a6 6 0 0 1-6-6v-3.5a1.5 1.5 0 0 1 3 0V15"/>',
		'support'   => '<path d="M4 13a8 8 0 0 1 16 0"/><rect x="3" y="13" width="4" height="6" rx="1"/><rect x="17" y="13" width="4" height="6" rx="1"/><path d="M19 19a3 3 0 0 1-3 3h-3"/>',
		'star'      => '<path d="m12 3 2.7 5.6 6.1.9-4.4 4.3 1 6.1L12 17l-5.4 2.9 1-6.1-4.4-4.3 6.1-.9L12 3Z"/>',
		'check'     => '<path d="m5 12 5 5 9-10"/>',
		'minus'     => '<path d="M5 12h14"/>',
		'plus'      => '<path d="M12 5v14M5 12h14"/>',
		'filter'    => '<path d="M4 6h16M7 12h10M10 18h4"/>',
		'share'     => '<circle cx="18" cy="5" r="2.5"/><circle cx="6" cy="12" r="2.5"/><circle cx="18" cy="19" r="2.5"/><path d="m8.2 10.8 7.6-4.6M8.2 13.2l7.6 4.6"/>',
		'lock'      => '<rect x="5" y="11" width="14" height="10" rx="2"/><path d="M8 11V8a4 4 0 0 1 8 0v3"/>',
		'sparkle'   => '<path d="M12 3v4M12 17v4M3 12h4M17 12h4M6 6l2.5 2.5M15.5 15.5 18 18M6 18l2.5-2.5M15.5 8.5 18 6"/>',
		'scissors'  => '<circle cx="6" cy="6" r="3"/><circle cx="6" cy="18" r="3"/><path d="M8.5 7.5 20 19M8.5 16.5 20 5"/>',
		'needle'    => '<path d="M4 20 18 6M15 3l6 6-3 3-6-6 3-3Z"/><path d="M4 20c2-.5 3-1 4-2"/>',
		'leaf'      => '<path d="M5 19c0-8 4-13 14-14 0 10-5 14-13 14H5Z"/><path d="M5 19c3-4 6-7 10-9"/>',
		'gift'      => '<rect x="3" y="8" width="18" height="4"/><path d="M5 12v8h14v-8M12 8v12M12 8c-2 0-4-1-4-3s3-2 4 3c1-5 4-5 4-3s-2 3-4 3Z"/>',
		'zoom'      => '<circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5M11 8v6M8 11h6"/>',
		'grid'      => '<rect x="4" y="4" width="6" height="6"/><rect x="14" y="4" width="6" height="6"/><rect x="4" y="14" width="6" height="6"/><rect x="14" y="14" width="6" height="6"/>',
		'video'     => '<rect x="3" y="6" width="13" height="12" rx="2"/><path d="m16 10 5-3v10l-5-3"/>',
		'ruler'     => '<path d="m3 17 14-14 4 4L7 21l-4-4Z"/><path d="m7 13 2 2M10 10l2 2M13 7l2 2"/>',
		'upi'       => '<path d="M4 17 9 7l2 4M14 7l5 10M12 7v10"/>',
		'card'      => '<rect x="3" y="6" width="18" height="12" rx="2"/><path d="M3 10h18M7 15h4"/>',
		'bank'      => '<path d="M3 10 12 4l9 6H3ZM5 10v8M10 10v8M14 10v8M19 10v8M3 20h18"/>',
		'rupee'     => '<path d="M7 5h10M7 9h10M7 5c5 0 7 1.5 7 4s-2 4-7 4l7 8"/>',
		'cart-empty'=> '<path d="M3 4h2l2.5 11h11L21 7H7"/><circle cx="9" cy="19" r="1.5"/><circle cx="17" cy="19" r="1.5"/>',
		'eye'       => '<path d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/>',
	);

	if ( ! isset( $icons[ $name ] ) ) {
		return '';
	}

	$size  = absint( $args['size'] );
	$class = 'icon d-icon d-icon--' . sanitize_html_class( $name ) . ( $args['class'] ? ' ' . esc_attr( $args['class'] ) : '' );
	$aria  = $args['label'] ? ' role="img" aria-label="' . esc_attr( $args['label'] ) . '"' : ' aria-hidden="true" focusable="false"';

	return sprintf(
		'<svg class="%1$s" width="%2$d" height="%2$d" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"%3$s>%4$s</svg>',
		esc_attr( $class ),
		$size,
		$aria,
		$icons[ $name ] // Static, theme-authored markup.
	);
}

/**
 * Echo an icon.
 *
 * @param string $name Icon.
 * @param array  $args Args.
 */
function dastaan_the_icon( $name, $args = array() ) {
	echo dastaan_icon( $name, $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Static SVG built with escaped attributes.
}

/**
 * Allowed inline HTML for short rich-text options (announcements, footer text).
 *
 * @return array
 */
function dastaan_kses_inline() {
	return array(
		'a'      => array( 'href' => array(), 'title' => array(), 'target' => array(), 'rel' => array(), 'class' => array() ),
		'strong' => array(), 'em' => array(), 'b' => array(), 'i' => array(), 'span' => array( 'class' => array() ), 'br' => array(),
	);
}

/* -------------------------------------------------------------------------
 * Sanitizers (used by Customizer and meta boxes)
 * ---------------------------------------------------------------------- */

/**
 * Checkbox.
 *
 * @param mixed $value Value.
 * @return int
 */
function dastaan_sanitize_checkbox( $value ) {
	return ( true === $value || 1 === $value || '1' === $value || 'on' === $value || 'true' === $value ) ? 1 : 0;
}

/**
 * Select with choices validation.
 *
 * @param string               $value   Value.
 * @param WP_Customize_Setting $setting Setting.
 * @return string
 */
function dastaan_sanitize_select( $value, $setting ) {
	$value   = sanitize_key( $value );
	$control = $setting->manager->get_control( $setting->id );
	$choices = $control && ! empty( $control->choices ) ? $control->choices : array();

	return array_key_exists( $value, $choices ) ? $value : $setting->default;
}

/**
 * Integer within range.
 *
 * @param mixed $value Value.
 * @return int
 */
function dastaan_sanitize_int( $value ) {
	return (int) $value;
}

/**
 * Hex colour.
 *
 * @param string $value Value.
 * @return string
 */
function dastaan_sanitize_hex( $value ) {
	$value = sanitize_hex_color( $value );

	return $value ? $value : '';
}

/**
 * URL or relative path.
 *
 * @param string $value Value.
 * @return string
 */
function dastaan_sanitize_url( $value ) {
	$value = trim( (string) $value );
	if ( '' === $value ) {
		return '';
	}
	if ( 0 === strpos( $value, '/' ) && 0 !== strpos( $value, '//' ) ) {
		return esc_url_raw( home_url( $value ) );
	}

	return esc_url_raw( $value );
}

/**
 * Multiline text with limited inline HTML.
 *
 * @param string $value Value.
 * @return string
 */
function dastaan_sanitize_richtext( $value ) {
	return wp_kses_post( (string) $value );
}

/**
 * Plain multiline text.
 *
 * @param string $value Value.
 * @return string
 */
function dastaan_sanitize_textarea( $value ) {
	return sanitize_textarea_field( (string) $value );
}

/**
 * Tracking IDs: strict allow-list of characters.
 *
 * @param string $value Value.
 * @return string
 */
function dastaan_sanitize_tracking_id( $value ) {
	$value = trim( (string) $value );

	return preg_match( '/^[A-Za-z0-9_\-]{0,64}$/', $value ) ? $value : '';
}

/**
 * Digits only (phone numbers for wa.me).
 *
 * @param string $value Value.
 * @return string
 */
function dastaan_sanitize_digits( $value ) {
	return preg_replace( '/\D+/', '', (string) $value );
}

/**
 * Date-time string (YYYY-MM-DD HH:MM).
 *
 * @param string $value Value.
 * @return string
 */
function dastaan_sanitize_datetime( $value ) {
	$value = trim( (string) $value );
	if ( '' === $value ) {
		return '';
	}
	$ts = strtotime( $value );

	return $ts ? gmdate( 'Y-m-d H:i', $ts ) : '';
}

/**
 * Comma-separated slug list.
 *
 * @param string $value Value.
 * @return string
 */
function dastaan_sanitize_slug_list( $value ) {
	$parts = array_filter( array_map( 'sanitize_title', explode( ',', (string) $value ) ) );

	return implode( ',', $parts );
}

/**
 * Shortcode-only string (e.g. Instagram plugin shortcode).
 *
 * @param string $value Value.
 * @return string
 */
function dastaan_sanitize_shortcode( $value ) {
	$value = trim( wp_strip_all_tags( (string) $value ) );
	if ( '' === $value ) {
		return '';
	}

	return preg_match( '/^\[[a-zA-Z0-9_\-]+(\s+[^\]]*)?\]$/', $value ) ? $value : '';
}

/**
 * Is WooCommerce active?
 *
 * @return bool
 */
function dastaan_has_wc() {
	return class_exists( 'WooCommerce' );
}

/**
 * Is an SEO plugin handling meta output?
 *
 * @return bool
 */
function dastaan_has_seo_plugin() {
	return defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || defined( 'AIOSEO_VERSION' ) || defined( 'SEOPRESS_VERSION' ) || class_exists( 'The_SEO_Framework\\Load' );
}

/**
 * Replace {year} and {site} placeholders.
 *
 * @param string $text Text.
 * @return string
 */
function dastaan_placeholders( $text ) {
	return str_replace( array( '{year}', '{site}' ), array( gmdate( 'Y' ), get_bloginfo( 'name' ) ), (string) $text );
}

/**
 * Image markup for a theme option that may hold a URL (default) or attachment URL.
 *
 * @param string $url   Image URL.
 * @param string $alt   Alt text.
 * @param array  $attrs Extra attributes.
 * @return string
 */
function dastaan_img( $url, $alt = '', $attrs = array() ) {
	$url = trim( (string) $url );
	if ( '' === $url ) {
		return '';
	}
	$id = attachment_url_to_postid( $url );
	if ( $id ) {
		$attrs = wp_parse_args( $attrs, array( 'alt' => $alt ) );
		$size  = isset( $attrs['size'] ) ? $attrs['size'] : 'large';
		unset( $attrs['size'] );

		return wp_get_attachment_image( $id, $size, false, $attrs );
	}
	unset( $attrs['size'] );
	$attrs = wp_parse_args( $attrs, array( 'loading' => 'lazy', 'decoding' => 'async' ) );
	$html  = '<img src="' . esc_url( $url ) . '" alt="' . esc_attr( $alt ) . '"';
	foreach ( $attrs as $k => $v ) {
		if ( '' === $v || null === $v ) {
			continue;
		}
		$html .= ' ' . sanitize_key( $k ) . '="' . esc_attr( $v ) . '"';
	}

	return $html . ' />';
}
