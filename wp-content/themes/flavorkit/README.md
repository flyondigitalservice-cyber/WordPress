# FlavorKit: a WooCommerce theme for D2C food and beverage brands

A bold, playful theme for snack, beverage, spice, baby-food and wellness
stores. The design follows the style of modern Indian and global D2C brands:
chunky type, sticker-style buttons, marquee strips, wavy dividers, bestseller
grids, reviews, a UGC grid, an FAQ and a newsletter offer.

You can rebrand it for each client from the Customizer, with no code changes.

## Quick start for a new client

1. Copy `wp-content/themes/flavorkit` into the client site and activate it.
2. Install **WooCommerce** and add products. Until products exist, the
   homepage shows illustrated demo products in the brand colours.
3. Go to **Appearance → Customize → FlavorKit Theme** and set the following:
   * **Brand colours & fonts**: pick one of the presets (Soda Pop,
     Wicked Orange, Spice Route, Fresh Fit, Gentle Mom, Berry Night) or choose
     *Custom* and use the 5 colour pickers. Also set the heading and body
     fonts, the corner roundness and the button style.
   * **Homepage sections**: a comma-separated list. Delete a key to hide that
     section and move keys to reorder them.
     `hero,marquee,usp,categories,products,story,benefits,reviews,ugc,faq,newsletter`
   * **Hero, Story, Reviews, FAQ…**: all the copy. Wrap a word in
     `*asterisks*` to highlight it.
   * **Footer, social & WhatsApp**: social links and a floating WhatsApp
     button.
4. Under **Site Identity**, upload the logo.
5. Under **Appearance → Menus**, assign *Primary*, *Footer — Quick links*
   and *Footer — Help*. Until you assign them, the theme shows sensible
   default links.

### List fields

Several fields hold one item per line, with the parts separated by `|`:

| Field | Format |
| --- | --- |
| USP badges / Benefits | `Title \| Text \| icon` |
| Reviews | `Name \| City \| Review \| 5` |
| FAQ | `Question \| Answer` |
| Story stats | `50K+ \| happy customers` |
| Demo categories | `Snacks \| pouch` |

Available icons: `leaf heart truck shield sparkle flame drop gift check star`.
Available pack shapes: `can pouch jar bottle box`.

## Features

* Colour presets plus custom colours, all driven by CSS variables. Text
  contrast on coloured backgrounds is calculated automatically.
* 12 Google Font choices. Only the fonts you select are loaded.
* Sticky header, scrolling announcement bar, mobile drawer menu and search
  panel.
* **AJAX cart drawer** with a free-shipping progress bar. The bar reads the
  minimum amount from your WooCommerce *Free shipping* method.
* Custom product cards: % off badge, star rating, an image swap on hover
  (uses the first gallery image) and AJAX add to cart.
* A sticky single-product summary, a quantity stepper, trust badges under the
  add-to-cart button, and restyled tabs, cart, checkout and My Account pages.
* Built-in newsletter capture with a honeypot and nonce. Subscribers are
  listed under **Tools → Subscribers** with CSV export. You can also paste a
  Mailchimp, Klaviyo or CF7 shortcode instead.
* FAQ section that outputs `FAQPage` schema for SEO.
* Accessibility: skip link, focus styles, focus trap in the drawers, Esc to
  close, `aria-*` states and support for `prefers-reduced-motion`.
* Vanilla JS (no jQuery dependency), a deferred script, no build step.

## File map

```
flavorkit/
├── functions.php            Loads modules from /inc
├── inc/
│   ├── defaults.php         Presets, fonts, sections, default copy
│   ├── customizer.php       All Customizer controls
│   ├── enqueue.php          Assets + dynamic CSS variables
│   ├── illustrations.php    Brand-coloured SVG packs + demo catalogue
│   ├── template-tags.php    Helpers (icons, stars, waves, highlight)
│   ├── newsletter.php       Subscriber capture + Tools page
│   ├── setup.php            Theme supports, menus, sidebars
│   └── woocommerce.php      Woo hooks, cart fragments, product queries
├── template-parts/sections  One file per homepage section
├── template-parts/components
├── woocommerce/content-product.php
└── assets/css, assets/js
```
