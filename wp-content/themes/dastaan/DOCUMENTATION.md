# Dastaan by Artisans — Theme Documentation

Version 1.0.0 · WordPress 6.4+ · WooCommerce 8.0+ (tested with WooCommerce 11.2) · PHP 8.0+

---

## 1. Install (10 minutes)

1. **WordPress + WooCommerce** — install WooCommerce, run its wizard: Country *India*, Currency *INR (₹)*, enable the payment methods you use (Razorpay/PayU/Cashfree plugin for UPI & cards, optionally COD).
2. **Upload the theme** — Appearance → Themes → Add New → Upload `dastaan-theme.zip` → Activate.
3. **Front page** — Settings → Reading → *A static page* → choose any page (e.g. "Home"). The theme's `front-page.php` renders all home sections; any content you write in that page is shown below the studio strip (handy for extra SEO copy or blocks).
4. **Permalinks** — Settings → Permalinks → *Post name* → Save (this also activates `/llms.txt`).
5. **Menus** — Appearance → Menus. Create and assign:
   * **Primary Menu (header)** — e.g. Shop All, Crafts (mega), Fabrics (mega), Festive Edit, Our Story, Contact & Studio
   * **Footer — Shop column**, **Footer — Help column**, **Footer — Legal links**
   * Optional: **Mobile Menu** (otherwise Primary is reused).
   * Mega menu: add CSS class `mega` to a top-level item (Screen Options → enable "CSS Classes"). Its child items become columns; grandchildren are links. Add `badge-new` or `badge-sale` to any item for a pill badge.
6. **Logo** — Customize → Site Identity → Logo (SVG or PNG, ~360×120). Without a logo a typographic wordmark "DASTAAN / by artisans" is used.
7. **Category images** — Products → Categories → edit each → Thumbnail. These power the category circles, craft showcase and category banners.
8. **Customize** — Appearance → Customize → **Dastaan Theme Options** (sections 1–20, see §3).
9. **Dastaan → Setup & Help** in the admin sidebar repeats this checklist with status indicators.

### Recommended plugins (optional, all detected automatically)
| Need | Plugin | Notes |
|---|---|---|
| UPI / cards / EMI | Razorpay for WooCommerce (or PayU, Cashfree) | Payment icons in the footer are static; edit under section 16. |
| SEO | Rank Math or Yoast | The theme's own JSON-LD/meta output switches off automatically to avoid duplicates. |
| Wishlist | TI WooCommerce Wishlist or YITH Wishlist | Heart buttons appear on cards/product page. Without a plugin the heart links to My Account. |
| Caching | LiteSpeed Cache / WP Rocket | Exclude cart, checkout, my-account (standard). |
| Images | ShortPixel / Imagify | Upload hero 1920×1000, banners 800×1000, products 1200×1600 (3:4). |
| Product video | Your existing plugin keeps working — see §5. |

---

## 2. Structure of the home page

Order of sections (each has an *Enable* toggle in its Customizer section; reorder with the `dastaan_home_sections` filter):

1. Announcement bar (site-wide)
2. Category circles — story-style, auto from product categories (like the reference site, they sit above the hero)
3. Hero slider — up to 4 slides, each with eyebrow, title, text, two buttons, optional coupon pill
4. USP / trust bar — 4 icon + text promises
5. New in — product carousel (newest / featured / on-sale / by category)
6. Offer banners — 3 tall tiles (image, kicker, title, button, link)
7. Bestsellers — second product carousel
8. Craft showcase — editorial tiles from categories (first tile is the large feature)
9. Reels / video block — up to 6 vertical reels (MP4 upload or Instagram/YouTube link, poster, linked product)
10. Testimonials — up to 4 with photo, rating, name, city
11. Behind the scenes — 3 images + story text
12. Instagram block — up to 8 manual tiles (image + link) *or* a feed plugin shortcode
13. Studio visit strip — address, hours, map link, WhatsApp button
14. Page content (whatever you write in the front page itself)
15. SEO content + FAQ — long-form editorial text and up to 8 Q&As (output as FAQPage schema)
16. Newsletter + footer (site-wide)

---

## 3. Customizer sections (Appearance → Customize → Dastaan Theme Options)

| # | Section | What you edit |
|---|---|---|
| 1 | Brand, Contact & Social | WhatsApp number (digits with country code, e.g. `917208200521`), pre-filled WhatsApp message, phone, email, studio address, hours, Google Maps link, Instagram/Facebook/Pinterest/YouTube URLs, price range (used in schema). |
| 2 | Announcement Bar | Left/right messages, link, optional countdown end date (`YYYY-MM-DD HH:MM`), colours. |
| 3 | Header | Sticky header, show search / wishlist / account icons, "Need help?" promo pill text + WhatsApp. |
| 4 | Hero Slider | 4 slides × (image, mobile image, eyebrow, title, text, button 1/2 text + URL, coupon code, align, overlay strength), autoplay interval. |
| 5 | Category Circles | Enable, title, which categories (comma-separated slugs or auto), count. |
| 6 | USP / Trust Bar | 4 × icon + title + text. |
| 7 | Product Sections | "New in" and "Bestsellers": title, subtitle, source (newest / featured / bestselling / on sale / category), count, "View all" link. |
| 8 | Offer Banners | 3 × image, kicker, title, button text, URL. |
| 9 | Craft Showcase | Title, intro, categories, count (default 5: 1 feature + 4). |
| 10 | Reels / Video Block | 6 × (MP4 upload or URL, poster image, caption, product link), autoplay-muted toggle. |
| 11 | Instagram Block | Handle, follow URL, 8 × (image + URL) **or** shortcode from any feed plugin (e.g. Smash Balloon). Falls back to product images if empty. |
| 12 | Testimonials | 4 × (quote, name, city, rating, photo). |
| 13 | Behind the Scenes | Title, text, 3 images, button. |
| 14 | Studio Visit Strip | Title, text, image, button, map URL. |
| 15 | SEO Content & FAQ | Heading, rich text (HTML allowed for admins), 8 × Q&A. |
| 16 | Footer & Newsletter | About text, newsletter title/text/action URL, payment icons toggle, copyright (`{year}` placeholder), bottom tagline. |
| 17 | Shop / Catalogue | Products per page, default grid density, hover image, badges (New ≤ N days, Sale %, One of one), quick add, off-canvas filters, show "Only X left". |
| 18 | Product Page | Video position (first/last in gallery), autoplay, WhatsApp "Ask a stylist" button, delivery promise text, size-guide page, care/shipping/exchange accordion content, related count, sticky mobile bar. |
| 19 | Tracking, Privacy & AI | Meta Pixel ID, GA4 ID, Microsoft Clarity ID, Google/Bing verification, cookie consent banner (text, buttons), AI-crawler policy (allow/block), llms.txt enable + extra text. |
| 20 | Colours & Typography | Primary (wine), accent (gold), background, ink, heading/body font, container width, button radius. |

All options have sensible Dastaan defaults; the Customizer shows live previews.

---

## 4. Tracking, consent and Microsoft Clarity

* **Meta Pixel** — enter the ID. The theme fires `PageView`, `ViewContent` (product), `AddToCart` (AJAX add-to-bag included), `InitiateCheckout`, `Purchase` (order-received, once per order, with value/currency/content_ids) and `Lead` (newsletter). If the official *Facebook for WooCommerce* plugin is active, the theme does **not** inject a second pixel.
* **GA4** — `gtag.js` with the same e-commerce events (`view_item`, `add_to_cart`, `begin_checkout`, `purchase`).
* **Microsoft Clarity** — enter the project ID. The snippet loads after consent, Clarity's cookie list is declared in the consent text, drawers/menus use real `aria-hidden` states and the layout has no CLS traps, so recordings/heatmaps are clean. Nothing in the theme masks or blocks Clarity.
* **Consent** — when the consent banner is enabled, all trackers wait for *Accept*; the choice is stored in cookie `dastaan_consent` for 180 days. JS event `dastaan:consent` is dispatched for other scripts. Disable the banner (section 19) if you prefer trackers to load immediately.
* **Custom events** — `window.dastaanTrack(name, data)` forwards to Pixel (trackCustom), GA4 and Clarity (custom tags).

---

## 5. Product video (compatible with your existing plugin)

The product gallery merges videos from three sources, de-duplicated, in this order (filterable via `dastaan_product_videos`):

1. **Theme meta box** (*Product Video* in the product editor sidebar): upload MP4/WebM **or** paste a YouTube / Vimeo / Instagram URL; optional poster. Stored as `_dastaan_product_video` (attachment ID), `_dastaan_product_video_url`, `_dastaan_product_video_poster`.
2. **WooCommerce 11 native gallery videos** (`_wc_video_gallery`), when that feature is enabled in WooCommerce → Settings → Advanced → Features.
3. **Third-party video plugins** — meta keys `_video_url`, `_product_video_url`, `_woo_product_video`, `product_video_url`, `_wc_product_video`, `_ywcfav_video_url`, `wcpv_video_url` are read automatically. Add your plugin's key with:
   ```php
   add_filter( 'dastaan_product_video_meta_keys', fn( $k ) => array_merge( $k, [ 'my_plugin_video_key' ] ) );
   ```
   If your plugin renders its own player via `woocommerce_single_product_summary` or a shortcode, it still works — the theme does not remove third-party hooks. To avoid a duplicate player, either leave the theme's video empty or set *Video position → Off* in section 18.

Videos play inline in the gallery (muted autoplay optional, tap to unmute), in the lightbox, and are output as `VideoObject` schema.

---

## 6. SEO and generative-AI (LLM) optimisation

* Semantic HTML5, one `<h1>` per page, breadcrumb trail with BreadcrumbList schema, canonical and Open Graph/Twitter tags (only when no SEO plugin is active).
* JSON-LD: `Organization` + `Store` (name, logo, address in Seawoods, Navi Mumbai, opening hours, price range, sameAs socials), `Product` (brand, SKU, offers with INR, availability, aggregateRating, material/colour/craft as `additionalProperty`, videos), `FAQPage` (home FAQ), `WebSite` with SearchAction, `VideoObject` for product videos.
* **`/llms.txt`** and **`/llms-full.txt`** — brand summary, policies, crafts/fabrics glossary and the live catalogue (titles, prices, URLs) in the llmstxt.org format. Section 19 lets you add extra text and disable it.
* **robots.txt** — AI crawlers (GPTBot, ChatGPT-User, OAI-SearchBot, ClaudeBot, PerplexityBot, Google-Extended, Applebot-Extended, CCBot, Bytespider …) are explicitly *allowed* by default (for AI shopping visibility) or *blocked* with one switch. Cart, checkout, account and search are disallowed and `noindex`.
* Product descriptions: the "Specs" grid (fabric, craft, colour, occasion, set contents, lengths, care) is rendered from product attributes — fill WooCommerce attributes *Fabric*, *Craft*, *Colour*, *Occasion*, *Set includes*, *Care* and they appear as structured facts for both shoppers and crawlers.
* Images get descriptive `alt` fallbacks (product title + attribute), `loading="lazy"`, explicit dimensions.

---

## 7. Checkout and cart

* Shortcode cart/checkout are used (classic WooCommerce templates). If your WooCommerce created *block* Cart/Checkout pages, switch them: edit the Cart page → replace the block with `[woocommerce_cart]`, Checkout page → `[woocommerce_checkout]`.
* Field order: first name, last name, **phone (required, Indian mobile validation)**, email, country, address, city, state, PIN. Company is removed. Order notes are relabelled "Delivery instructions or gift note".
* Phone validation accepts `9876543210`, `+91 98765 43210`, `09876543210`; non-Indian addresses are not validated. Disable with `add_filter( 'dastaan_validate_indian_phone', '__return_false' );` or make phone optional with `add_filter( 'dastaan_phone_required', '__return_false' );`.
* Coupon field lives inside the order summary ("Have a gift or coupon code?"). Coupons in WooCommerce → Marketing → Coupons.
* Trust tiles, payment-badge footer and the WhatsApp help link are editable (sections 1 and 18). Order-received page shows "What happens next" and a *Message us about this order* WhatsApp button pre-filled with the order number.

---

## 8. Newsletter

The footer form posts via AJAX to the theme (honeypot + nonce protected) and stores subscribers in **Dastaan → Subscribers** (CSV export). To use Mailchimp/Klaviyo/Sender etc. instead, paste the provider's form *action URL* in section 16 — the form then posts straight to the provider in a new tab (field name `EMAIL`, Mailchimp-compatible).

---

## 9. Hooks for developers

* Filters: `dastaan_defaults`, `dastaan_home_sections`, `dastaan_keep_wc_styles`, `dastaan_defer_pixel_to_plugin` (return true to keep WooCommerce's own CSS), `dastaan_product_videos`, `dastaan_product_media`, `dastaan_product_video_meta_keys`, `dastaan_phone_required`, `dastaan_validate_indian_phone`, `dastaan_badges`, `dastaan_schema_product`, `dastaan_llms_txt`, `dastaan_instagram_tiles`.
* Actions: `dastaan_before_header`, `dastaan_after_header`, `dastaan_home_sections_before/after`, `dastaan_product_summary_after_specs`, `dastaan_footer_before`.
* JS events on `document`: `dastaan:added_to_cart`, `dastaan:open` / `dastaan:close` (drawers), `dastaan:lead`, `dastaan:consent`. Helpers: `window.dastaanToast(msg)`, `dastaanOpen(id)`, `dastaanClose(id)`, `dastaanTrack(name, data)`.
* Widget areas: *Shop Filters (off-canvas)*, *Footer — Extra column*, *Product page — after summary*.
* Child-theme friendly: all template parts are overridable; functions are `function_exists`-guarded where it matters.

---

## 10. Security notes

* Recommended: add `define( 'DISALLOW_FILE_EDIT', true );` to wp-config.php (site configuration belongs there, not in a theme). WP version, RSD/WLW links, REST user enumeration (for non-logged-in), XML-RPC (toggle) and login error details removed.
* All front-end AJAX (`dastaan_add_to_cart`, `dastaan_subscribe`) is nonce-protected and sanitised; all output is escaped; admin screens check `manage_options`/`edit_theme_options`.
* No external calls apart from the fonts (Google Fonts with preconnect — switch *Load Google Fonts* off in section 20 for zero third-party requests, or enqueue self-hosted @font-face files from a child theme), the trackers you configure, and the video embeds you paste.
* The theme never stores customer data beyond WooCommerce's own orders and the optional newsletter list.

---

## 11. Performance

* Front end: ~38 KB CSS (gzipped ~9 KB), ~14 KB JS, no jQuery outside cart/checkout, no sliders libraries (native scroll-snap carousels), WooCommerce's three stylesheets removed, block-library CSS loaded only where blocks are used, emoji script removed, preconnect/preload for fonts and hero.
* Images: use 3:4 product photos ≥ 1200 px; the theme registers `dastaan-card` (600×800), `dastaan-card-2x` (900×1200), `dastaan-circle` (300×300), `dastaan-tile` (800×1000), `dastaan-hero` (1920×1000), `dastaan-hero-mobile` (900×1200), `dastaan-square` (700×700). Regenerate thumbnails after switching (plugin *Regenerate Thumbnails*).

---

## 12. Known notes

* **After every theme update, purge your page cache** (LiteSpeed Cache → Toolbox → Purge All, or your host's cache button). Otherwise visitors and Google may keep seeing the previous version's HTML.

* Local/demo installs on **SQLite** (the "SQLite Database Integration" plugin) cannot run WooCommerce's stock-reservation query; set WooCommerce → Settings → Products → Inventory → *Hold stock (minutes)* to `0` on such installs. MySQL/MariaDB production servers are unaffected.
* The admin "Dastaan" welcome page and the Customizer need `edit_theme_options` capability (Administrators).
* Translations: `languages/dastaan.pot` is included; use Loco Translate for Hindi/Marathi.

---

## 13. Switching an existing store to Dastaan (e.g. from Ecomus)

Dastaan works with the plugins a typical Ecomus store uses:

* **WC Featured Video Gallery** — product videos (`_wcfv_video_id`, optional cover image) appear in the Dastaan gallery automatically; the plugin's own Ecomus-specific scripts are not loaded.
* **WCBoost Wishlist** — the heart button appears on product cards and next to "Ask a stylist"; the header/mobile wishlist links use the plugin's wishlist page.
* **Elementor pages** (About, Contact, policies…) render full width with their own design. An Elementor-built *front page* is not shown below the Dastaan home sections (filter: `dastaan_home_show_page_content`).
* **Footer links** find existing pages by slug (shipping, returns-exchanges / refund_returns, faq / faq-v2, contact / contact-v1, order-tracking, terms-conditions, privacy-policy). Assign menus under Appearance → Menus for full control.

Checklist:
1. Take a full backup (Hostinger: hPanel → Backups).
2. Upload and activate Dastaan, then purge LiteSpeed Cache.
3. Appearance → Customize → Dastaan Theme Options: logo, hero slides, banners, WhatsApp number, studio details.
4. Appearance → Menus: assign Primary, Footer and Legal menus.
5. Keep only one floating WhatsApp button (Dastaan's, or the Simple Chat Button plugin).
6. Elementor pages that used Ecomus Addons widgets: open each page once and check it still looks right.
7. Place a test order with Razorpay (test mode) before announcing the new look.
8. Do not run "Import demo products" on the live store.

## 14. Demo products (one click)

**wp-admin → Dastaan → Demo Products → Import demo products** (or `wp dastaan demo import` with WP-CLI).

It creates the seven craft categories (with images) and 11 demo pieces: product galleries with a product video, sale prices, "one of one" stock, a colour-variable set, reviews and bestseller counts. Only the images and the short video bundled in `assets/` are used, and no existing products, pages or settings are changed.

**Remove demo products** (or `wp dastaan demo remove`) deletes exactly what the importer created: the products, their media and any categories it added that are now empty.

© Dastaan by Artisans · Seawoods, Navi Mumbai
