=== Bazaar Core ===
Contributors: flyondigitalservice
Tags: woocommerce, otp login, whatsapp, product compare, swatches
Requires at least: 6.5
Tested up to: 7.1
Requires PHP: 8.0
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Shared store features for the Bazaar themes (Store, Electronics, Boutique).

== Description ==

Bazaar Core holds all the functionality used by the Bazaar block themes, so one fix reaches every theme and every client site. Features switch on only when the active theme asks for them (`add_theme_support( 'bazaar-otp' )` etc.):

* Passwordless email OTP login (`[bzr_otp_login]`), proxy-aware rate limiting, follows WooCommerce's registration setting
* Contact form (`[bzr_contact_form]`) with nonce, honeypot, timing check and rate limit
* Product compare (`[bzr_compare]`) using the WooCommerce Store API
* Attribute/price filters (`[bzr_filters]`) using WooCommerce's native filter URLs
* Real customer reviews (`[bzr_reviews]`) - no placeholder testimonials
* Variation swatches, size guide, WhatsApp ordering, sticky add-to-cart, delivery estimate, trust badges, deal countdown, EMI line
* Appearance > Store Options, with test email and "create missing pages" tools
* Self-hosted updates for the plugin and the Bazaar themes (define `BAZAAR_UPDATE_URL`)

== Installation ==

1. Install WooCommerce.
2. Upload and activate Bazaar Core, then activate a Bazaar theme.
3. Configure Appearance > Store Options. If the site is behind Cloudflare or another proxy, set "Visitor IP header".
4. Use "Send a test email to me"; install an SMTP plugin if it does not arrive.

== Changelog ==

= 1.0.0 =
* Shared code moved out of the three themes into this plugin.
* Rate limits now work behind Cloudflare/proxies (configurable IP header + trusted proxy list).
* OTP sign-up respects WooCommerce's "allow registration" setting.
* Store Options shows only fields for the active theme's features; adds test-email and starter-page tools; warns when no SMTP plugin is active.
* Real reviews shortcode replaces placeholder testimonials.
* Generic, filterable default size guide.
* Self-hosted update checker.
* Translation template (languages/bazaar-core.pot).
