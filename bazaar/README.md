# Bazaar store suite

Three WooCommerce block themes plus one shared plugin, built to be reused across many client stores and kept up to date.

| Package | Path | What it is |
| --- | --- | --- |
| **Bazaar Core** (plugin) | `wp-content/plugins/bazaar-core` | All store features: OTP login, contact form, compare, filters, swatches, size guide, WhatsApp, product-page extras, real reviews, Store Options, starter pages, updates. |
| **Bazaar Store** | `wp-content/themes/bazaar-store` | General stores: household, kitchen, glassware, crockery. |
| **Bazaar Electronics** | `wp-content/themes/bazaar-electronics` | Gadgets: spec filters, compare, deal countdown, EMI line, warranty. |
| **Bazaar Boutique** | `wp-content/themes/bazaar-boutique` | Fashion: swatches, size guide (with abaya lengths), size/colour filters, exchange policy. |

The themes hold **design only**: `theme.json`, templates, parts, patterns and CSS. Each theme turns features on with `add_theme_support( 'bazaar-<feature>' )` in `inc/theme-features.php`. All of the code lives once, in Bazaar Core. A fix made there reaches every theme and every client.

## Setting up a new client

1. Install and activate **WooCommerce**, then **Bazaar Core**, then the right Bazaar theme.
2. Copy `bazaar/child-theme-template/` to `wp-content/themes/<client>-store/`. In its `style.css`, replace `CLIENT_NAME`, `PARENT_SLUG` (e.g. `bazaar-boutique`) and `CHILD_SLUG`. Activate the child theme.
   **Never edit the Bazaar theme or plugin for one client.** Put brand colours in the child `theme.json`, template overrides in the child folder, and wording changes in the Site Editor. That way the client keeps getting updates.
3. Go to **Appearance > Store Options**:
   - Fill in the store details, delivery days, WhatsApp number, invoice label and so on.
   - **Security & rate limiting**: if the site is behind Cloudflare, a load balancer or a host proxy, choose the "Visitor IP header". If you skip this, all visitors share one IP and the OTP and contact limits block everyone at once. The page warns you when it detects a proxy.
   - Click **Send a test email to me**. Login codes need working email, so install an SMTP plugin (WP Mail SMTP, FluentSMTP, ...) if the test lands in spam or never arrives.
   - Click **Create missing pages** if the policy, contact and sign-in pages were not created automatically.
4. Decide on sign-ups. The OTP form creates new customer accounts only when **WooCommerce > Settings > Accounts & Privacy > "Allow customers to create an account on the My account page"** is on. Otherwise only existing customers can sign in.
5. Before going live, replace the placeholder text in the header announcement bar and the policy pages. The reviews section stays hidden until the store has real 4- and 5-star reviews.

## Updates for client sites

1. Bump `Version` in the plugin header (and `BAZAAR_CORE_VERSION`) and/or in the theme `style.css`.
2. Regenerate the translation templates: `npm install --no-save wp-pot && node bazaar/bin/make-pot.js`
3. Build: `bazaar/bin/build.sh https://your-server.com/bazaar`. This writes zips and `manifest.json` to `bazaar/dist/`.
4. Upload everything in `bazaar/dist/` to that HTTPS folder.
5. On each client site, add this line to `wp-config.php` (once):

   ```php
   define( 'BAZAAR_UPDATE_URL', 'https://your-server.com/bazaar/manifest.json' );
   ```

   Updates then appear in **Dashboard > Updates** like any other plugin or theme, and auto-updates work. Client child themes are never touched.

## Translations

- Text domains: `bazaar-core`, `bazaar-store`, `bazaar-electronics`, `bazaar-boutique`. Each package has `languages/<domain>.pot`.
- Translate with Poedit or Loco Translate. Save as `<domain>-<locale>.mo` (e.g. `bazaar-boutique-ar.mo`) in the package's `languages/` folder, or in `wp-content/languages/themes|plugins/`.
- All CSS uses logical properties, so Arabic and Urdu (RTL) need no extra stylesheet.

## Developer hooks

| Filter | Use |
| --- | --- |
| `bzr_rate_limits` | Change OTP/contact limits: `[ 'otp_email' => [3, 900], 'otp_ip' => [10, 900], 'contact_ip' => [5, 600] ]` |
| `bzr_client_ip` | Override visitor-IP detection |
| `bzr_otp_allow_signup` | Force sign-ups on or off regardless of the WooCommerce setting |
| `bzr_otp_deliver` | Send login codes by SMS or WhatsApp instead of email |
| `bzr_trust_badges` | Trust badges under add-to-cart |
| `bzr_default_size_guide` | Default size chart |
| `bzr_swatch_colors` | Colour-name to hex map for swatches |
| `bzr_starter_pages` | Pages created from patterns |
| `bzr_option_fields` | Add Store Options fields |
| `bzr_update_manifest_url` | Update manifest URL (instead of the constant) |
