# Tasdir Export — WordPress theme

A custom theme for **Tasdir Import Export Private Limited**, a fresh fruit exporter from India. It is built mobile-first and for speed: no jQuery, no page builder, no web fonts, one CSS file and one small deferred JS file. Images are WebP with responsive `srcset`.

## Go-live checklist (about 10 minutes)

1. **Activate** under *Appearance → Themes → Tasdir Export*. On first activation the theme:
   - creates the products *Fresh Cavendish Banana* and *Fresh Table Grapes*, plus the pages *About Us*, *Export Process*, *Contact* and *Privacy Policy*
   - switches permalinks to `/%postname%/` if they were set to "Plain"
   - never overwrites content that already exists
2. **Settings → Reading:** set "Your homepage displays" to *Your latest posts* or to any static page. The landing page (`front-page.php`) is used either way.
3. **Appearance → Customize → Tasdir Settings:**
   - **WhatsApp & Contact:** WhatsApp number (required), phone, email, address, social links, and the email address that receives enquiry alerts
   - **Analytics & Pixels:** GA4 Measurement ID, Meta Pixel ID, optional GTM container, and the Google, Bing and Facebook verification codes
   - **SEO:** home page title and description, and the default share image
   - **Home Hero & Sections:** headline, hero image, export markets
4. *(Optional)* **Appearance → Customize → Site Identity:** upload the Tasdir logo. Until you do, a text wordmark is shown.
5. *(Optional)* **Appearance → Menus:** assign menus to *Primary* and *Footer*. Until you do, an automatic menu is shown.
6. Submit **Google Search Console** and **Bing** with the sitemap at `https://YOURDOMAIN/wp-sitemap.xml`.

## Lead capture

- An enquiry form appears on the home page, on every product page and on the Contact page. Each submission is:
  1. saved under **Dashboard → Enquiries**, with name, WhatsApp, company, country, product, quantity, message, page and campaign source (UTM, gclid, fbclid or referrer)
  2. emailed to the alert address
  3. opened in **WhatsApp** with all the details pre-filled, ready to send to your number
- In the enquiry list you can set a status (New, Contacted, Quotation sent, Won, Lost), add internal notes, reply on WhatsApp with one click and **export everything as CSV**.
- Other WhatsApp entry points: the floating button, the sticky mobile bar (Call, Quote, WhatsApp), the "Get price" button on each product (with a pre-filled product message), and the header and footer.
- Spam protection works with page caching: a honeypot field, a timing check and a limit of 6 enquiries per IP every 10 minutes.

## Tracking events

| Action | GA4 event | Meta Pixel | dataLayer (GTM) |
|---|---|---|---|
| Any page | page_view | PageView | — |
| Any WhatsApp click | `whatsapp_click` | `Contact` | `tasdir_whatsapp_click` |
| Enquiry form submitted | `generate_lead` | `Lead` | `tasdir_generate_lead` |
| Product page viewed | `view_item` | `ViewContent` | `tasdir_view_item` |
| Phone or email click | `call_click` / `email_click` | — | `tasdir_call_click` / `tasdir_email_click` |

In GA4, mark `generate_lead` and `whatsapp_click` as **key events**. Logged-in editors are not tracked unless you enable that in the Customizer.

## SEO

- Per-page SEO title, meta description and noindex option, in the "SEO" box on posts, pages and products
- Open Graph and Twitter cards, canonical URLs, and robots rules (search results and 404 pages are noindexed)
- JSON-LD for Organization, WebSite, FAQPage (home), Product (product pages) and BreadcrumbList
- Core XML sitemap, with noindexed pages and user archives removed
- If Yoast, Rank Math, SEOPress, AIOSEO or The SEO Framework is active, the theme's SEO output switches off automatically

## Speed notes

- The hero image is preloaded with `fetchpriority="high"`. Every other image is lazy-loaded WebP (768 and 1376 widths).
- Emoji scripts, oEmbed, RSD, the generator tag and shortlinks are removed. Block CSS is skipped on the landing and product archive pages.
- Recommended stack: a LiteSpeed Cache or WP Rocket page cache plus a CDN. The enquiry form is cache-safe.

## Content to review

The starter text includes typical export specs: carton sizes, minimum order, seasons, shelf life, varieties, FAQ answers and the list of export markets. Edit each one to match your actual offer:

- **Products:** *Export Products* → edit
- **FAQ:** the `tasdir_faqs` filter in `inc/seo.php`
- **Export markets:** in the Customizer
