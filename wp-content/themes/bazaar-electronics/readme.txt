=== Bazaar Electronics ===
Contributors: flyondigitalservice
Requires at least: 6.5
Tested up to: 7.1
Requires PHP: 8.0
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A block theme for electronics and gadget stores: spec-based filters, side-by-side product comparison, deal countdowns, warranty and tax-invoice trust signals, passwordless OTP login, real customer reviews and ready-made policy pages. Requires the Bazaar Core plugin and WooCommerce.

== Requirements ==

* WooCommerce
* Bazaar Core plugin (all store features live there; the theme shows a notice if it is missing)

== Setup ==

1. Activate WooCommerce and Bazaar Core, then this theme.
2. For a client site, create a child theme from bazaar/child-theme-template and put all client changes there.
3. Fill in Appearance > Store Options. Policy, contact and sign-in pages are created automatically (or with "Create missing pages").
4. Edit the header announcement and footer wording in Appearance > Editor.

== Changelog ==

= 1.0.0 =
* Store features moved to the shared Bazaar Core plugin; the theme now only holds design.
* All text is translatable (languages/bazaar-electronics.pot), including template text and the footer copyright, which now shows the current year and site name.
* Customer reviews section shows real WooCommerce reviews and hides itself until there are some.
* "GST invoice" wording is now the configurable Invoice label in Store Options (default: GST invoice).
