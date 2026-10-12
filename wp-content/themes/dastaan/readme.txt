=== Dastaan by Artisans ===
Contributors: dastaanbyartisans
Requires at least: 6.4
Tested up to: 7.1
Requires PHP: 8.0
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Tags: e-commerce, one-column, two-columns, custom-logo, custom-menu, featured-images, threaded-comments, translation-ready, block-styles, wide-blocks, accessibility-ready

A premium, mobile-first WooCommerce theme for Dastaan by Artisans — luxury unstitched dress material for women, handcrafted by real artisans. Studio in Seawoods, Navi Mumbai.

== Description ==

Dastaan is a purpose-built WooCommerce theme (not a page-builder template). Everything a luxury Indian ethnic-wear store needs is included and editable from Appearance → Customize → Dastaan Theme Options:

* Announcement bar with optional countdown, sticky header with mega menu, WhatsApp "chat with a stylist" button
* Hero slider (up to 4 slides), category "story" circles, USP/trust bar, two product carousels
* Offer banners, craft showcase, Reels/video block, Instagram block, testimonials, behind-the-scenes, studio-visit strip, SEO copy + FAQ (FAQ schema)
* Shop grid with badges, quick-add, wishlist (plugin-agnostic hooks), off-canvas filters, grid-density switch
* Product page with image + video gallery (theme video meta box, WooCommerce 11 native gallery videos and third-party video plugins all supported), sticky summary, specs, accordion, WhatsApp enquiry, sticky mobile add-to-bag
* Distraction-free checkout: steps, Indian phone validation, coupon inside the order summary, trust tiles, order-received "what happens next"
* Mini-cart drawer, AJAX add-to-bag, mobile bottom navigation
* Tracking with consent gating: Meta (Facebook) Pixel, GA4, Microsoft Clarity, site verification tags
* SEO: clean semantic markup, Organization / Store / Product / Breadcrumb / FAQ JSON-LD (auto-disabled when an SEO plugin is active), noindex on cart/checkout/account/search
* Generative-AI search optimisation: /llms.txt and /llms-full.txt, AI-crawler policy in robots.txt, structured product facts
* Security hardening: no file editor, version/leak removal, nonce-protected AJAX, escaped output, capability checks
* Performance: no jQuery on the front end except on cart/checkout (WooCommerce requires it), WooCommerce CSS replaced by one lean stylesheet, lazy images, preconnects
* Translation-ready (languages/dastaan.pot), RTL-safe layout, keyboard-accessible drawers and menus

== Installation ==

1. Upload the `dastaan` folder to `/wp-content/themes/` (or upload the zip under Appearance → Themes → Add New → Upload).
2. Install and activate WooCommerce (8.0+). Run the WooCommerce setup wizard (currency INR, country India).
3. Activate Dastaan. The theme creates nothing automatically — follow Dastaan → Setup & Help in the admin menu, or DOCUMENTATION.md in the theme folder.
4. Set a static front page (Settings → Reading) — any page; the theme's front-page.php renders the home sections.
5. Assign menus (Appearance → Menus): Primary, Footer — Shop, Footer — Help, Footer — Legal.
6. Go to Appearance → Customize → Dastaan Theme Options and work through sections 1–20.

== Frequently Asked Questions ==

= Does it work without WooCommerce? =
Yes — blog, pages and the home page render; shop-specific sections hide themselves until WooCommerce is active.

= How do I add a product video? =
Edit the product → "Product Video" box in the sidebar: upload an MP4 or paste a YouTube/Vimeo/Instagram URL, optional poster. WooCommerce 11 native gallery videos and existing video plugins are picked up automatically (see DOCUMENTATION.md → Product video).

= Where do I put my Facebook Pixel / Clarity / GA4 IDs? =
Customize → Dastaan Theme Options → 19. Tracking, Privacy & AI.

== Changelog ==

= 1.1.0 =
* Fixed: fatal error on the classic checkout page (WC_Cart::get_item_product_name() is not available in all WooCommerce versions).
* Fixed: product page summary column no longer slides over the details accordion and related products when scrolling.
* Fixed: product gallery thumbnails sit below the main image on mobile; Buy now is disabled until a variation is chosen.
* Fixed: header navigation overlapping the logo and help pill; desktop menu now starts at 1200px and wraps safely.
* Fixed: icons were never matched by the stylesheet (missing "icon" class), which misaligned icons across the header, footer, studio, trust and USP blocks.
* Fixed: footer contact rows, bottom bar alignment, extra widget column, and mobile column layout.
* Fixed: search overlay form, mini-cart drawer padding/quantity, WooCommerce notices, payment methods and list indentation inside cart/checkout/account.
* Fixed: testimonials, banners, craft tiles, ticker and category circles alignment; light hero slides are readable on mobile.
* Improved: responsive layout verified at 360, 390, 768, 1024, 1280 and 1440px with no horizontal scrolling.

= 1.0.0 =
* Initial release.

== Credits ==

* Fonts: Cormorant Garamond and Jost (SIL Open Font License) loaded from Google Fonts with preconnect; can be self-hosted (see documentation).
* Icons: original inline SVG set, GPL-compatible.
* Bundled hero/category images: generated artwork licensed for use with this theme; replace with your own photography.
