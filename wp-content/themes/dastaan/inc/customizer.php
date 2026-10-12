<?php
/**
 * Customizer: every editable option, registered from a single config array.
 *
 * @package Dastaan
 */

defined( 'ABSPATH' ) || exit;

/**
 * Sections inside the "Dastaan" panel.
 *
 * @return array
 */
function dastaan_customizer_sections() {
	return array(
		'brand'        => array( __( '1. Brand, Contact & Social', 'dastaan' ), __( 'WhatsApp, phone, studio address and social profiles used across header, footer and product pages.', 'dastaan' ) ),
		'announcement' => array( __( '2. Announcement Bar', 'dastaan' ), __( 'Thin bar above the header. Supports an optional countdown.', 'dastaan' ) ),
		'header'       => array( __( '3. Header', 'dastaan' ), __( 'Sticky behaviour, search, wishlist icon. Upload your logo under Site Identity.', 'dastaan' ) ),
		'hero'         => array( __( '4. Hero Slider', 'dastaan' ), __( 'Up to 4 full-width slides. Leave an image empty to hide a slide. Recommended 1920×1000px.', 'dastaan' ) ),
		'circles'      => array( __( '5. Category Circles', 'dastaan' ), __( 'Story-style circles pulled from your WooCommerce product categories (set thumbnails under Products → Categories).', 'dastaan' ) ),
		'usp'          => array( __( '6. USP / Trust Bar', 'dastaan' ), __( 'Four promises shown under the hero.', 'dastaan' ) ),
		'featured'     => array( __( '7. Product Sections', 'dastaan' ), __( 'Two product carousels: New in / Bestsellers.', 'dastaan' ) ),
		'banners'      => array( __( '8. Offer Banners', 'dastaan' ), __( 'Three tall promotional tiles. Recommended 800×1000px.', 'dastaan' ) ),
		'crafts'       => array( __( '9. Craft Showcase', 'dastaan' ), __( 'Large editorial tiles of your crafts (uses product categories).', 'dastaan' ) ),
		'reels'        => array( __( '10. Reels / Video Block', 'dastaan' ), __( 'Upload MP4 reels or paste Instagram/YouTube links. Each reel can link to a product.', 'dastaan' ) ),
		'instagram'    => array( __( '11. Instagram Block', 'dastaan' ), __( 'Manual grid (no API needed) or paste a feed plugin shortcode.', 'dastaan' ) ),
		'testimonials' => array( __( '12. Testimonials', 'dastaan' ), __( 'Customer reviews with optional photo.', 'dastaan' ) ),
		'bts'          => array( __( '13. Behind the Scenes', 'dastaan' ), __( 'Artisan story block with three images.', 'dastaan' ) ),
		'studio'       => array( __( '14. Studio Visit Strip', 'dastaan' ), __( 'Invite customers to the Seawoods studio.', 'dastaan' ) ),
		'seo'          => array( __( '15. SEO Content & FAQ', 'dastaan' ), __( 'Editorial text + FAQ at the bottom of the home page (output as FAQ schema for Google & AI assistants).', 'dastaan' ) ),
		'footer'       => array( __( '16. Footer & Newsletter', 'dastaan' ), __( 'About text, newsletter, payment icons, copyright. Footer menus are under Appearance → Menus.', 'dastaan' ) ),
		'shop'         => array( __( '17. Shop / Catalogue', 'dastaan' ), __( 'Grid density, badges, quick add.', 'dastaan' ) ),
		'product'      => array( __( '18. Product Page', 'dastaan' ), __( 'Video position, WhatsApp enquiry, delivery promise, care & shipping copy.', 'dastaan' ) ),
		'tracking'     => array( __( '19. Tracking, Privacy & AI', 'dastaan' ), __( 'Meta Pixel, GA4, Microsoft Clarity, verification tags, cookie consent, AI crawler policy.', 'dastaan' ) ),
		'design'       => array( __( '20. Colours & Typography', 'dastaan' ), __( 'Brand colours, fonts, container width, button radius.', 'dastaan' ) ),
	);
}

/**
 * Field config. Format: key => [ label, type, section, (args) ].
 *
 * @return array
 */
function dastaan_customizer_fields() {
	$f = array();
	$icons = array(
		'truck'    => __( 'Truck (shipping)', 'dastaan' ),
		'hand'     => __( 'Hand (handcrafted)', 'dastaan' ),
		'exchange' => __( 'Exchange arrows', 'dastaan' ),
		'shield'   => __( 'Shield (secure)', 'dastaan' ),
		'support'  => __( 'Headset (support)', 'dastaan' ),
		'needle'   => __( 'Needle', 'dastaan' ),
		'scissors' => __( 'Scissors', 'dastaan' ),
		'leaf'     => __( 'Leaf (natural fabric)', 'dastaan' ),
		'gift'     => __( 'Gift', 'dastaan' ),
		'sparkle'  => __( 'Sparkle', 'dastaan' ),
		'lock'     => __( 'Lock', 'dastaan' ),
		'clock'    => __( 'Clock', 'dastaan' ),
		'rupee'    => __( 'Rupee', 'dastaan' ),
	);

	// 1. Brand.
	$f['brand_tagline']     = array( __( 'Brand line (shown under logo in footer)', 'dastaan' ), 'text', 'brand' );
	$f['whatsapp_number']   = array( __( 'WhatsApp number (digits with country code, e.g. 917208200521)', 'dastaan' ), 'digits', 'brand' );
	$f['whatsapp_message']  = array( __( 'Default WhatsApp message', 'dastaan' ), 'text', 'brand' );
	$f['phone_number']      = array( __( 'Display phone', 'dastaan' ), 'text', 'brand' );
	$f['contact_email']     = array( __( 'Contact email', 'dastaan' ), 'email', 'brand' );
	$f['studio_address']    = array( __( 'Studio address', 'dastaan' ), 'textarea', 'brand' );
	$f['studio_hours']      = array( __( 'Studio hours', 'dastaan' ), 'text', 'brand' );
	$f['social_instagram']  = array( __( 'Instagram URL', 'dastaan' ), 'url', 'brand' );
	$f['social_facebook']   = array( __( 'Facebook URL', 'dastaan' ), 'url', 'brand' );
	$f['social_youtube']    = array( __( 'YouTube URL', 'dastaan' ), 'url', 'brand' );
	$f['social_pinterest']  = array( __( 'Pinterest URL', 'dastaan' ), 'url', 'brand' );
	$f['show_whatsapp_float'] = array( __( 'Show floating WhatsApp button', 'dastaan' ), 'checkbox', 'brand' );

	// 2. Announcement.
	$f['announcement_enable']    = array( __( 'Enable announcement bar', 'dastaan' ), 'checkbox', 'announcement' );
	$f['announcement_text']      = array( __( 'Message', 'dastaan' ), 'text', 'announcement', array( 'transport' => 'postMessage' ) );
	$f['announcement_link']      = array( __( 'Link URL (optional)', 'dastaan' ), 'url', 'announcement' );
	$f['announcement_link_text'] = array( __( 'Link text', 'dastaan' ), 'text', 'announcement' );
	$f['announcement_countdown'] = array( __( 'Countdown end (YYYY-MM-DD HH:MM, site timezone). Empty = no countdown.', 'dastaan' ), 'datetime', 'announcement' );
	$f['announcement_secondary'] = array( __( 'Right-side note (desktop only)', 'dastaan' ), 'text', 'announcement', array( 'transport' => 'postMessage' ) );

	// 3. Header.
	$f['header_sticky']        = array( __( 'Sticky header on scroll', 'dastaan' ), 'checkbox', 'header' );
	$f['header_show_search']   = array( __( 'Show search', 'dastaan' ), 'checkbox', 'header' );
	$f['header_show_wishlist'] = array( __( 'Show wishlist icon', 'dastaan' ), 'checkbox', 'header' );
	$f['wishlist_url']         = array( __( 'Wishlist page URL (from your wishlist plugin)', 'dastaan' ), 'url', 'header' );
	$f['header_promo_text']    = array( __( 'Top-right helper text (links to WhatsApp)', 'dastaan' ), 'text', 'header' );

	// 4. Hero.
	$f['hero_autoplay'] = array( __( 'Autoplay slides', 'dastaan' ), 'checkbox', 'hero' );
	$f['hero_interval'] = array( __( 'Seconds per slide', 'dastaan' ), 'number', 'hero', array( 'input_attrs' => array( 'min' => 3, 'max' => 20 ) ) );
	for ( $i = 1; $i <= 4; $i++ ) {
		/* translators: %d: slide number */
		$f[ "hero_{$i}_heading_h" ] = array( sprintf( __( 'Slide %d', 'dastaan' ), $i ), 'heading', 'hero' );
		$f[ "hero_{$i}_image" ]   = array( __( 'Image (1920×1000)', 'dastaan' ), 'image', 'hero' );
		$f[ "hero_{$i}_mobile_image" ] = array( __( 'Mobile image (optional, 900×1200 portrait)', 'dastaan' ), 'image', 'hero' );
		$f[ "hero_{$i}_focus" ]   = array( __( 'Image focal point (what stays visible when cropped on phones)', 'dastaan' ), 'select', 'hero', array( 'choices' => array( 'center' => __( 'Centre', 'dastaan' ), 'left' => __( 'Left', 'dastaan' ), 'right' => __( 'Right', 'dastaan' ) ) ) );
		$f[ "hero_{$i}_coupon" ]  = array( __( 'Coupon pill (e.g. FESTIVE10 — shown as "Use code")', 'dastaan' ), 'text', 'hero' );
		$f[ "hero_{$i}_eyebrow" ] = array( __( 'Eyebrow (small line)', 'dastaan' ), 'text', 'hero' );
		$f[ "hero_{$i}_heading" ] = array( __( 'Heading', 'dastaan' ), 'text', 'hero' );
		$f[ "hero_{$i}_text" ]    = array( __( 'Supporting text', 'dastaan' ), 'textarea', 'hero' );
		$f[ "hero_{$i}_button" ]  = array( __( 'Primary button text', 'dastaan' ), 'text', 'hero' );
		$f[ "hero_{$i}_url" ]     = array( __( 'Primary button URL', 'dastaan' ), 'url', 'hero' );
		$f[ "hero_{$i}_button2" ] = array( __( 'Secondary button text', 'dastaan' ), 'text', 'hero' );
		$f[ "hero_{$i}_url2" ]    = array( __( 'Secondary button URL', 'dastaan' ), 'url', 'hero' );
		$f[ "hero_{$i}_theme" ]   = array( __( 'Text colour', 'dastaan' ), 'select', 'hero', array( 'choices' => array( 'light' => __( 'Dark text (light image)', 'dastaan' ), 'dark' => __( 'White text (dark image)', 'dastaan' ) ) ) );
	}

	// 5. Circles.
	$f['circles_enable']  = array( __( 'Enable category circles', 'dastaan' ), 'checkbox', 'circles' );
	$f['circles_title']   = array( __( 'Small heading', 'dastaan' ), 'text', 'circles' );
	$f['circles_count']   = array( __( 'Maximum circles', 'dastaan' ), 'number', 'circles', array( 'input_attrs' => array( 'min' => 3, 'max' => 16 ) ) );
	$f['circles_exclude'] = array( __( 'Exclude category slugs (comma separated)', 'dastaan' ), 'slugs', 'circles' );

	// 6. USP.
	$f['usp_enable'] = array( __( 'Enable USP bar', 'dastaan' ), 'checkbox', 'usp' );
	for ( $i = 1; $i <= 4; $i++ ) {
		/* translators: %d: item number */
		$f[ "usp_{$i}_h" ]     = array( sprintf( __( 'Item %d', 'dastaan' ), $i ), 'heading', 'usp' );
		$f[ "usp_{$i}_icon" ]  = array( __( 'Icon', 'dastaan' ), 'select', 'usp', array( 'choices' => $icons ) );
		$f[ "usp_{$i}_title" ] = array( __( 'Title', 'dastaan' ), 'text', 'usp' );
		$f[ "usp_{$i}_text" ]  = array( __( 'Sub text', 'dastaan' ), 'text', 'usp' );
	}

	// 7. Product sections.
	$sources = array(
		'recent'      => __( 'Newest products', 'dastaan' ),
		'featured'    => __( 'Featured products (starred)', 'dastaan' ),
		'bestselling' => __( 'Best selling', 'dastaan' ),
		'sale'        => __( 'On sale', 'dastaan' ),
		'category'    => __( 'Specific category (slug below)', 'dastaan' ),
	);
	$f['featured_enable']    = array( __( 'Enable first carousel', 'dastaan' ), 'checkbox', 'featured' );
	$f['featured_eyebrow']   = array( __( 'Eyebrow', 'dastaan' ), 'text', 'featured' );
	$f['featured_title']     = array( __( 'Title', 'dastaan' ), 'text', 'featured' );
	$f['featured_source']    = array( __( 'Source', 'dastaan' ), 'select', 'featured', array( 'choices' => $sources ) );
	$f['featured_category']  = array( __( 'Category slug (if source = category)', 'dastaan' ), 'slugs', 'featured' );
	$f['featured_count']     = array( __( 'Number of products', 'dastaan' ), 'number', 'featured', array( 'input_attrs' => array( 'min' => 4, 'max' => 16 ) ) );
	$f['featured_link_text'] = array( __( '"View all" text', 'dastaan' ), 'text', 'featured' );
	$f['bestseller_h']       = array( __( 'Second carousel', 'dastaan' ), 'heading', 'featured' );
	$f['bestseller_enable']  = array( __( 'Enable second carousel', 'dastaan' ), 'checkbox', 'featured' );
	$f['bestseller_eyebrow'] = array( __( 'Eyebrow', 'dastaan' ), 'text', 'featured' );
	$f['bestseller_title']   = array( __( 'Title', 'dastaan' ), 'text', 'featured' );
	$f['bestseller_source']  = array( __( 'Source', 'dastaan' ), 'select', 'featured', array( 'choices' => $sources ) );
	$f['bestseller_category']= array( __( 'Category slug (if source = category)', 'dastaan' ), 'slugs', 'featured' );
	$f['bestseller_count']   = array( __( 'Number of products', 'dastaan' ), 'number', 'featured', array( 'input_attrs' => array( 'min' => 4, 'max' => 16 ) ) );

	// 8. Banners.
	$f['banners_enable'] = array( __( 'Enable offer banners', 'dastaan' ), 'checkbox', 'banners' );
	for ( $i = 1; $i <= 3; $i++ ) {
		/* translators: %d: banner number */
		$f[ "banner_{$i}_h" ]       = array( sprintf( __( 'Banner %d', 'dastaan' ), $i ), 'heading', 'banners' );
		$f[ "banner_{$i}_image" ]   = array( __( 'Image', 'dastaan' ), 'image', 'banners' );
		$f[ "banner_{$i}_eyebrow" ] = array( __( 'Eyebrow', 'dastaan' ), 'text', 'banners' );
		$f[ "banner_{$i}_title" ]   = array( __( 'Title', 'dastaan' ), 'text', 'banners' );
		$f[ "banner_{$i}_text" ]    = array( __( 'Text', 'dastaan' ), 'text', 'banners' );
		$f[ "banner_{$i}_button" ]  = array( __( 'Button text', 'dastaan' ), 'text', 'banners' );
		$f[ "banner_{$i}_url" ]     = array( __( 'URL', 'dastaan' ), 'url', 'banners' );
	}

	// 9. Crafts.
	$f['crafts_enable']  = array( __( 'Enable craft showcase', 'dastaan' ), 'checkbox', 'crafts' );
	$f['crafts_eyebrow'] = array( __( 'Eyebrow', 'dastaan' ), 'text', 'crafts' );
	$f['crafts_title']   = array( __( 'Title', 'dastaan' ), 'text', 'crafts' );
	$f['crafts_text']    = array( __( 'Intro text', 'dastaan' ), 'textarea', 'crafts' );
	$f['crafts_count']   = array( __( 'Number of tiles', 'dastaan' ), 'number', 'crafts', array( 'input_attrs' => array( 'min' => 2, 'max' => 8 ) ) );

	// 10. Reels.
	$f['reels_enable']  = array( __( 'Enable reels block', 'dastaan' ), 'checkbox', 'reels' );
	$f['reels_eyebrow'] = array( __( 'Eyebrow', 'dastaan' ), 'text', 'reels' );
	$f['reels_title']   = array( __( 'Title', 'dastaan' ), 'text', 'reels' );
	$f['reels_text']    = array( __( 'Intro text', 'dastaan' ), 'textarea', 'reels' );
	for ( $i = 1; $i <= 6; $i++ ) {
		/* translators: %d: reel number */
		$f[ "reel_{$i}_h" ]       = array( sprintf( __( 'Reel %d', 'dastaan' ), $i ), 'heading', 'reels' );
		$f[ "reel_{$i}_video" ]   = array( __( 'Upload MP4 (vertical 9:16 recommended)', 'dastaan' ), 'video', 'reels' );
		$f[ "reel_{$i}_url" ]     = array( __( 'OR Instagram reel / YouTube Shorts URL', 'dastaan' ), 'url', 'reels' );
		$f[ "reel_{$i}_poster" ]  = array( __( 'Poster image (shown before play)', 'dastaan' ), 'image', 'reels' );
		$f[ "reel_{$i}_caption" ] = array( __( 'Caption', 'dastaan' ), 'text', 'reels' );
		$f[ "reel_{$i}_link" ]    = array( __( '"Shop this" link', 'dastaan' ), 'url', 'reels' );
	}

	// 11. Instagram.
	$f['instagram_enable']    = array( __( 'Enable Instagram block', 'dastaan' ), 'checkbox', 'instagram' );
	$f['instagram_title']     = array( __( 'Title', 'dastaan' ), 'text', 'instagram' );
	$f['instagram_handle']    = array( __( 'Handle (without @)', 'dastaan' ), 'text', 'instagram' );
	$f['instagram_shortcode'] = array( __( 'Feed plugin shortcode (optional, e.g. [instagram-feed]). Overrides the manual grid.', 'dastaan' ), 'shortcode', 'instagram' );
	for ( $i = 1; $i <= 8; $i++ ) {
		/* translators: %d: tile number */
		$f[ "instagram_{$i}_image" ] = array( sprintf( __( 'Tile %d image', 'dastaan' ), $i ), 'image', 'instagram' );
		$f[ "instagram_{$i}_link" ]  = array( __( 'Tile link (post URL or product)', 'dastaan' ), 'url', 'instagram' );
	}

	// 12. Testimonials.
	$f['testimonials_enable']  = array( __( 'Enable testimonials', 'dastaan' ), 'checkbox', 'testimonials' );
	$f['testimonials_eyebrow'] = array( __( 'Eyebrow', 'dastaan' ), 'text', 'testimonials' );
	$f['testimonials_title']   = array( __( 'Title', 'dastaan' ), 'text', 'testimonials' );
	for ( $i = 1; $i <= 4; $i++ ) {
		/* translators: %d: review number */
		$f[ "testimonial_{$i}_h" ]       = array( sprintf( __( 'Review %d', 'dastaan' ), $i ), 'heading', 'testimonials' );
		$f[ "testimonial_{$i}_name" ]    = array( __( 'Customer name', 'dastaan' ), 'text', 'testimonials' );
		$f[ "testimonial_{$i}_text" ]    = array( __( 'Review', 'dastaan' ), 'textarea', 'testimonials' );
		$f[ "testimonial_{$i}_product" ] = array( __( 'Purchased item', 'dastaan' ), 'text', 'testimonials' );
		$f[ "testimonial_{$i}_rating" ]  = array( __( 'Stars (1–5)', 'dastaan' ), 'number', 'testimonials', array( 'input_attrs' => array( 'min' => 1, 'max' => 5 ) ) );
		$f[ "testimonial_{$i}_image" ]   = array( __( 'Photo (optional)', 'dastaan' ), 'image', 'testimonials' );
	}

	// 13. BTS.
	$f['bts_enable']  = array( __( 'Enable behind the scenes', 'dastaan' ), 'checkbox', 'bts' );
	$f['bts_eyebrow'] = array( __( 'Eyebrow', 'dastaan' ), 'text', 'bts' );
	$f['bts_title']   = array( __( 'Title', 'dastaan' ), 'text', 'bts' );
	$f['bts_text']    = array( __( 'Text', 'dastaan' ), 'textarea', 'bts' );
	$f['bts_image_1'] = array( __( 'Image 1 (tall)', 'dastaan' ), 'image', 'bts' );
	$f['bts_image_2'] = array( __( 'Image 2', 'dastaan' ), 'image', 'bts' );
	$f['bts_image_3'] = array( __( 'Image 3', 'dastaan' ), 'image', 'bts' );
	$f['bts_button']  = array( __( 'Button text', 'dastaan' ), 'text', 'bts' );
	$f['bts_url']     = array( __( 'Button URL', 'dastaan' ), 'url', 'bts' );
	$f['bts_crafts']  = array( __( 'Craft ticker words (comma separated)', 'dastaan' ), 'text', 'bts' );

	// 14. Studio.
	$f['studio_enable'] = array( __( 'Enable studio strip', 'dastaan' ), 'checkbox', 'studio' );
	$f['studio_title']  = array( __( 'Title', 'dastaan' ), 'text', 'studio' );
	$f['studio_text']   = array( __( 'Text', 'dastaan' ), 'textarea', 'studio' );
	$f['studio_button'] = array( __( 'Button text', 'dastaan' ), 'text', 'studio' );
	$f['studio_url']    = array( __( 'Button URL (empty = WhatsApp)', 'dastaan' ), 'url', 'studio' );
	$f['studio_image']  = array( __( 'Image', 'dastaan' ), 'image', 'studio' );

	// 15. SEO.
	$f['seo_enable'] = array( __( 'Enable SEO content + FAQ', 'dastaan' ), 'checkbox', 'seo' );
	$f['seo_title']  = array( __( 'Heading (H2)', 'dastaan' ), 'text', 'seo' );
	$f['seo_text']   = array( __( 'Editorial text (paragraphs separated by blank lines)', 'dastaan' ), 'richtext', 'seo' );
	for ( $i = 1; $i <= 4; $i++ ) {
		/* translators: %d: FAQ number */
		$f[ "faq_{$i}_q" ] = array( sprintf( __( 'FAQ %d — question', 'dastaan' ), $i ), 'text', 'seo' );
		$f[ "faq_{$i}_a" ] = array( __( 'Answer', 'dastaan' ), 'textarea', 'seo' );
	}

	// 16. Footer.
	$f['footer_about']        = array( __( 'About text', 'dastaan' ), 'textarea', 'footer', array( 'transport' => 'postMessage' ) );
	$f['footer_shop_title']   = array( __( 'Shop column title', 'dastaan' ), 'text', 'footer' );
	$f['footer_help_title']   = array( __( 'Help column title', 'dastaan' ), 'text', 'footer' );
	$f['footer_studio_title'] = array( __( 'Studio column title', 'dastaan' ), 'text', 'footer' );
	$f['newsletter_h']        = array( __( 'Newsletter', 'dastaan' ), 'heading', 'footer' );
	$f['newsletter_enable']   = array( __( 'Show newsletter strip', 'dastaan' ), 'checkbox', 'footer' );
	$f['newsletter_title']    = array( __( 'Title', 'dastaan' ), 'text', 'footer' );
	$f['newsletter_text']     = array( __( 'Text', 'dastaan' ), 'text', 'footer' );
	$f['newsletter_action']   = array( __( 'External form action URL (Mailchimp/Klaviyo). Empty = store subscribers in WordPress (Dastaan → Subscribers).', 'dastaan' ), 'url', 'footer' );
	$f['newsletter_success']  = array( __( 'Success message', 'dastaan' ), 'text', 'footer' );
	$f['footer_bottom_h']     = array( __( 'Bottom bar', 'dastaan' ), 'heading', 'footer' );
	$f['footer_payment_icons']= array( __( 'Show payment icons (UPI, cards, net banking, EMI)', 'dastaan' ), 'checkbox', 'footer' );
	$f['footer_copyright']    = array( __( 'Copyright ({year} = current year)', 'dastaan' ), 'text', 'footer', array( 'transport' => 'postMessage' ) );
	$f['footer_legal_text']   = array( __( 'Legal line (GSTIN, registered name)', 'dastaan' ), 'text', 'footer' );

	// 17. Shop.
	$f['shop_columns']        = array( __( 'Desktop columns', 'dastaan' ), 'select', 'shop', array( 'choices' => array( '3' => '3', '4' => '4', '5' => '5' ) ) );
	$f['shop_per_page']       = array( __( 'Products per page', 'dastaan' ), 'number', 'shop', array( 'input_attrs' => array( 'min' => 4, 'max' => 48 ) ) );
	$f['shop_show_sidebar']   = array( __( 'Show filter drawer button', 'dastaan' ), 'checkbox', 'shop' );
	$f['shop_hover_image']    = array( __( 'Swap to second gallery image on hover', 'dastaan' ), 'checkbox', 'shop' );
	$f['shop_quick_add']      = array( __( 'Quick add-to-bag on cards', 'dastaan' ), 'checkbox', 'shop' );
	$f['shop_new_badge_days'] = array( __( '"New" badge for products younger than (days, 0 = off)', 'dastaan' ), 'number', 'shop', array( 'input_attrs' => array( 'min' => 0, 'max' => 365 ) ) );
	$f['shop_sale_badge']     = array( __( 'Show % off badge', 'dastaan' ), 'checkbox', 'shop' );

	// 18. Product.
	$f['product_video_position'] = array( __( 'Product video position in gallery', 'dastaan' ), 'select', 'product', array( 'choices' => array( 'first' => __( 'First slide', 'dastaan' ), 'second' => __( 'Second slide (after cover image)', 'dastaan' ), 'last' => __( 'Last slide', 'dastaan' ) ) ) );
	$f['product_video_autoplay'] = array( __( 'Autoplay muted video when its slide is active', 'dastaan' ), 'checkbox', 'product' );
	$f['product_show_whatsapp']  = array( __( 'Show WhatsApp enquiry button', 'dastaan' ), 'checkbox', 'product' );
	$f['product_whatsapp_text']  = array( __( 'WhatsApp button text', 'dastaan' ), 'text', 'product' );
	$f['product_show_buynow']    = array( __( 'Show "Buy now" (direct to checkout)', 'dastaan' ), 'checkbox', 'product' );
	$f['product_delivery_text']  = array( __( 'Delivery promise line', 'dastaan' ), 'text', 'product' );
	$f['product_trust_1']        = array( __( 'Trust point 1', 'dastaan' ), 'text', 'product' );
	$f['product_trust_2']        = array( __( 'Trust point 2', 'dastaan' ), 'text', 'product' );
	$f['product_trust_3']        = array( __( 'Trust point 3', 'dastaan' ), 'text', 'product' );
	$f['product_care_text']      = array( __( '"Fabric & care" accordion text', 'dastaan' ), 'textarea', 'product' );
	$f['product_shipping_text']  = array( __( '"Shipping & exchange" accordion text', 'dastaan' ), 'textarea', 'product' );
	$f['product_size_guide_page']= array( __( 'Measurement / stitching guide page', 'dastaan' ), 'page', 'product' );
	$f['product_sticky_atc']     = array( __( 'Sticky add-to-bag bar on mobile', 'dastaan' ), 'checkbox', 'product' );
	$f['product_related_count']  = array( __( 'Related products', 'dastaan' ), 'number', 'product', array( 'input_attrs' => array( 'min' => 0, 'max' => 8 ) ) );

	// 19. Tracking.
	$f['fb_pixel_id']       = array( __( 'Meta (Facebook) Pixel ID', 'dastaan' ), 'tracking', 'tracking', array( 'description' => __( 'Fires PageView, ViewContent, AddToCart, InitiateCheckout, Purchase with value & currency.', 'dastaan' ) ) );
	$f['fb_domain_verify']  = array( __( 'Meta domain verification code', 'dastaan' ), 'tracking', 'tracking' );
	$f['ga4_id']            = array( __( 'Google Analytics 4 Measurement ID (G-XXXX)', 'dastaan' ), 'tracking', 'tracking' );
	$f['clarity_id']        = array( __( 'Microsoft Clarity project ID', 'dastaan' ), 'tracking', 'tracking' );
	$f['google_verify']     = array( __( 'Google Search Console verification code', 'dastaan' ), 'tracking', 'tracking' );
	$f['consent_h']         = array( __( 'Cookie consent', 'dastaan' ), 'heading', 'tracking' );
	$f['consent_enable']    = array( __( 'Ask for consent before loading trackers (recommended)', 'dastaan' ), 'checkbox', 'tracking' );
	$f['consent_text']      = array( __( 'Consent text', 'dastaan' ), 'textarea', 'tracking' );
	$f['consent_accept']    = array( __( 'Accept label', 'dastaan' ), 'text', 'tracking' );
	$f['consent_decline']   = array( __( 'Decline label', 'dastaan' ), 'text', 'tracking' );
	$f['ai_h']              = array( __( 'AI / LLM optimisation', 'dastaan' ), 'heading', 'tracking' );
	$f['ai_crawlers']       = array( __( 'AI crawlers (GPTBot, ClaudeBot, PerplexityBot, Google-Extended)', 'dastaan' ), 'select', 'tracking', array( 'choices' => array( 'allow' => __( 'Allow (be discoverable in AI answers)', 'dastaan' ), 'block' => __( 'Block', 'dastaan' ) ) ) );
	$f['llms_enable']       = array( __( 'Publish /llms.txt and /llms-full.txt (brand + catalogue summary for AI assistants)', 'dastaan' ), 'checkbox', 'tracking' );
	$f['llms_extra']        = array( __( 'Extra notes for llms.txt (policies, USPs)', 'dastaan' ), 'textarea', 'tracking' );
	$f['security_h']        = array( __( 'Hardening', 'dastaan' ), 'heading', 'tracking' );
	$f['xmlrpc_disable']    = array( __( 'Disable XML-RPC (recommended unless you use the WordPress app/Jetpack)', 'dastaan' ), 'checkbox', 'tracking' );
	$f['remove_jquery_migrate'] = array( __( 'Remove jQuery Migrate on the front end (faster)', 'dastaan' ), 'checkbox', 'tracking' );

	// 20. Design.
	$f['color_primary']   = array( __( 'Primary (wine)', 'dastaan' ), 'color', 'design', array( 'transport' => 'postMessage' ) );
	$f['color_accent']    = array( __( 'Accent (gold)', 'dastaan' ), 'color', 'design', array( 'transport' => 'postMessage' ) );
	$f['color_bg']        = array( __( 'Background (ivory)', 'dastaan' ), 'color', 'design', array( 'transport' => 'postMessage' ) );
	$f['color_text']      = array( __( 'Text (ink)', 'dastaan' ), 'color', 'design', array( 'transport' => 'postMessage' ) );
	$f['google_fonts']    = array( __( 'Load Google Fonts (Cormorant Garamond + Jost). Off = system fonts, zero third-party requests.', 'dastaan' ), 'checkbox', 'design' );
	$f['container_width'] = array( __( 'Container width (px)', 'dastaan' ), 'number', 'design', array( 'input_attrs' => array( 'min' => 1100, 'max' => 1800, 'step' => 20 ) ) );
	$f['button_radius']   = array( __( 'Button corner radius (px)', 'dastaan' ), 'number', 'design', array( 'input_attrs' => array( 'min' => 0, 'max' => 40 ) ) );

	return apply_filters( 'dastaan_customizer_fields', $f );
}

/**
 * Register everything.
 *
 * @param WP_Customize_Manager $wp_customize Manager.
 */
function dastaan_customize_register( $wp_customize ) {
	$defaults = dastaan_defaults();

	/**
	 * Heading-only control.
	 */
	if ( ! class_exists( 'Dastaan_Heading_Control' ) ) {
		class Dastaan_Heading_Control extends WP_Customize_Control { // phpcs:ignore
			public $type = 'dastaan_heading';
			public function render_content() {
				echo '<h3 style="margin:18px 0 4px;padding-top:12px;border-top:1px solid #dcdcde;font-size:13px;letter-spacing:.04em;text-transform:uppercase;color:#1d2327">' . esc_html( $this->label ) . '</h3>';
				if ( $this->description ) {
					echo '<p class="description">' . esc_html( $this->description ) . '</p>';
				}
			}
		}
	}

	$wp_customize->add_panel(
		'dastaan_panel',
		array(
			'title'       => __( 'Dastaan Theme Options', 'dastaan' ),
			'description' => __( 'Everything on the storefront is editable here. Changes preview live on the right.', 'dastaan' ),
			'priority'    => 1,
		)
	);

	foreach ( dastaan_customizer_sections() as $id => $labels ) {
		$wp_customize->add_section(
			'dastaan_' . $id,
			array(
				'title'       => $labels[0],
				'description' => $labels[1],
				'panel'       => 'dastaan_panel',
			)
		);
	}

	$i = 0;
	foreach ( dastaan_customizer_fields() as $key => $field ) {
		$i++;
		$label   = $field[0];
		$type    = $field[1];
		$section = 'dastaan_' . $field[2];
		$args    = isset( $field[3] ) ? $field[3] : array();

		if ( 'heading' === $type ) {
			$wp_customize->add_setting( 'dastaan_heading_' . $key, array( 'sanitize_callback' => 'sanitize_text_field' ) );
			$wp_customize->add_control( new Dastaan_Heading_Control( $wp_customize, 'dastaan_heading_' . $key, array( 'label' => $label, 'section' => $section, 'priority' => $i, 'description' => isset( $args['description'] ) ? $args['description'] : '' ) ) );
			continue;
		}

		$sanitize = 'sanitize_text_field';
		switch ( $type ) {
			case 'checkbox':
				$sanitize = 'dastaan_sanitize_checkbox';
				break;
			case 'number':
				$sanitize = 'dastaan_sanitize_int';
				break;
			case 'select':
				$sanitize = 'dastaan_sanitize_select';
				break;
			case 'color':
				$sanitize = 'dastaan_sanitize_hex';
				break;
			case 'url':
				$sanitize = 'dastaan_sanitize_url';
				break;
			case 'email':
				$sanitize = 'sanitize_email';
				break;
			case 'image':
				$sanitize = 'esc_url_raw';
				break;
			case 'video':
			case 'page':
				$sanitize = 'absint';
				break;
			case 'textarea':
				$sanitize = 'dastaan_sanitize_textarea';
				break;
			case 'richtext':
				$sanitize = 'dastaan_sanitize_richtext';
				break;
			case 'tracking':
				$sanitize = 'dastaan_sanitize_tracking_id';
				break;
			case 'digits':
				$sanitize = 'dastaan_sanitize_digits';
				break;
			case 'datetime':
				$sanitize = 'dastaan_sanitize_datetime';
				break;
			case 'slugs':
				$sanitize = 'dastaan_sanitize_slug_list';
				break;
			case 'shortcode':
				$sanitize = 'dastaan_sanitize_shortcode';
				break;
		}

		$wp_customize->add_setting(
			$key,
			array(
				'default'           => isset( $defaults[ $key ] ) ? $defaults[ $key ] : '',
				'sanitize_callback' => $sanitize,
				'transport'         => isset( $args['transport'] ) ? $args['transport'] : 'refresh',
				'type'              => 'theme_mod',
			)
		);

		$control_args = array(
			'label'       => $label,
			'section'     => $section,
			'priority'    => $i,
			'description' => isset( $args['description'] ) ? $args['description'] : '',
		);
		if ( isset( $args['input_attrs'] ) ) {
			$control_args['input_attrs'] = $args['input_attrs'];
		}

		switch ( $type ) {
			case 'image':
				$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, $key, $control_args ) );
				break;
			case 'video':
				$control_args['mime_type'] = 'video';
				$wp_customize->add_control( new WP_Customize_Media_Control( $wp_customize, $key, $control_args ) );
				break;
			case 'color':
				$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, $key, $control_args ) );
				break;
			case 'select':
				$control_args['type']    = 'select';
				$control_args['choices'] = $args['choices'];
				$wp_customize->add_control( $key, $control_args );
				break;
			case 'page':
				$control_args['type'] = 'dropdown-pages';
				$control_args['allow_addition'] = true;
				$wp_customize->add_control( $key, $control_args );
				break;
			case 'checkbox':
				$control_args['type'] = 'checkbox';
				$wp_customize->add_control( $key, $control_args );
				break;
			case 'number':
				$control_args['type'] = 'number';
				$wp_customize->add_control( $key, $control_args );
				break;
			case 'textarea':
			case 'richtext':
				$control_args['type'] = 'textarea';
				$wp_customize->add_control( $key, $control_args );
				break;
			case 'url':
				$control_args['type'] = 'text';
				$wp_customize->add_control( $key, $control_args );
				break;
			case 'email':
				$control_args['type'] = 'email';
				$wp_customize->add_control( $key, $control_args );
				break;
			case 'datetime':
				$control_args['type'] = 'text';
				$control_args['input_attrs'] = array( 'placeholder' => '2026-11-01 23:59' );
				$wp_customize->add_control( $key, $control_args );
				break;
			default:
				$control_args['type'] = 'text';
				$wp_customize->add_control( $key, $control_args );
		}
	}

	// Live preview for postMessage settings.
	$wp_customize->get_setting( 'blogname' )->transport        = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport = 'postMessage';

	if ( isset( $wp_customize->selective_refresh ) ) {
		$wp_customize->selective_refresh->add_partial(
			'blogname',
			array(
				'selector'        => '.site-title a',
				'render_callback' => function () {
					bloginfo( 'name' );
				},
			)
		);
	}
}
add_action( 'customize_register', 'dastaan_customize_register' );

/**
 * Preview JS.
 */
function dastaan_customize_preview_js() {
	wp_enqueue_script( 'dastaan-customizer-preview', DASTAAN_URI . 'assets/js/customizer-preview.js', array( 'customize-preview' ), DASTAAN_VERSION, true );
}
add_action( 'customize_preview_init', 'dastaan_customize_preview_js' );
