# Custom Floor Mats — WordPress theme

A custom, fully editable theme for **customfloormats.ae** (a Casa Vera Home company), built on the same engine as dubaigymflooring.com.
It builds a complete SEO site (42 core pages + the Casa Vera Home interiors section), sends every lead to WhatsApp **and** saves it in wp-admin, and turns PDFs into catalogue cards that open in an **in-page PDF viewer** (View PDF / Download / Ask price).

No page-builder plugin is needed: all page content uses core Gutenberg blocks.

## 1. Install

1. Upload the `custom-floor-mats` folder to `wp-content/themes/` (or zip it and use **Appearance → Themes → Add New → Upload**), then **Activate**. On first activation the theme automatically:
   - creates every page below (plus the Interiors pages) with SEO titles, meta descriptions and FAQ schema,
   - creates the header menu and footer menus, sets **Home** as the front page and the Privacy Policy page,
   - switches permalinks to `/%postname%/` if they were "Plain", and bins WordPress's untouched demo content.
   Existing pages with the same slug are **never overwritten**.
2. **Appearance → CFM Setup → WhatsApp number** — defaults to +971 50 859 9803.
3. **Appearance → Customize → Custom Floor Mats** — phone, email, address, hours, map, socials, footer text, home-page highlight numbers, lead email.
4. **Appearance → Customize → Site Identity** — logo and site icon. Until then a built-in wordmark/favicon is shown.

## 2. Images & catalogues from Google Drive

**Appearance → CFM Setup → Import from Google Drive** (or `wp cfm drive-import`, or `POST /wp-json/cfm/v1/drive-import` as an administrator) downloads the Casa Vera Home photos and catalogue PDFs listed in `inc/drive-import.php` straight from Drive, attaches them to their pages and sets featured images. The files must stay shared "anyone with the link". Add a line to the manifest to import more.

You can also bulk-import from `wp-content/uploads/cfm-import/` (**Import images now**): a file or folder named after a page slug is attached to that page; PDFs become catalogue cards.

Every PDF in the Media Library appears on **Catalogues** automatically; a PDF attached to a product page also appears on that page. Set a PDF's featured image (or let WordPress render the first page) to show a cover.

## 3. Editing

| What | Where |
|---|---|
| Page text, headings, lists, buttons, FAQs | Edit the page (block editor) |
| Hero heading / sub-heading / background | "Page hero" box, **Excerpt**, **Featured image** |
| SEO title, description, keyword, noindex | "SEO (Custom Floor Mats)" box — steps aside if Yoast / Rank Math / AIOSEO is installed |
| Menus | Appearance → Menus |
| Contact details, socials, mother company | Appearance → Customize → Custom Floor Mats |
| Leads | wp-admin → **Leads** |
| Restore a page's starter content | Appearance → CFM Setup → **Rebuild** |

Set any button or menu URL to `#whatsapp` to open WhatsApp with the page name prefilled.

Shortcodes: `[cfm_quote_form]`, `[cfm_cta]`, `[cfm_whatsapp]`, `[cfm_children parent="floor-mats"]`, `[cfm_children parent="areas-we-serve" style="pills"]`, `[cfm_related]`, `[cfm_catalogues]`, `[cfm_gallery]`, `[cfm_stats]`, `[cfm_contact_info]`, `[cfm_map]`, `[cfm_parent_company]`.

WP-CLI: `wp cfm install`, `wp cfm drive-import`, `wp cfm import-images`, `wp cfm whatsapp 9715XXXXXXXX`, `wp cfm list`, `wp cfm rebuild <slug>`.

## 4. Before launch

Check the real phone/WhatsApp/email/address, the home-page highlight numbers, lead times quoted in the copy (logo mats "7–14 working days"), and the starter Privacy Policy / Terms with your legal adviser. Replace stock photos with real project photos as they come in.

## 5. Core pages

| # | Page | URL | Image file name |
|---|---|---|---|
| 1 | Home | `/home/` | `home.jpg` |
| 2 | About Us | `/about/` | `about.jpg` |
| 3 | Floor Mats | `/floor-mats/` | `floor-mats.jpg` |
| 4 | Services | `/services/` | `services.jpg` |
| 5 | Areas We Serve | `/areas-we-serve/` | `areas-we-serve.jpg` |
| 6 | Catalogues | `/catalogues/` | `catalogues.jpg` |
| 7 | Projects | `/projects/` | `projects.jpg` |
| 8 | FAQs | `/faq/` | `faq.jpg` |
| 9 | Contact Us | `/contact/` | `contact.jpg` |
| 10 | Get a Free Quote | `/get-a-free-quote/` | `get-a-free-quote.jpg` |
| 11 | Privacy Policy | `/privacy-policy/` | `privacy-policy.jpg` |
| 12 | Terms & Conditions | `/terms-and-conditions/` | `terms-and-conditions.jpg` |
| 13 | Custom Logo Mats | `/floor-mats/custom-logo-mats/` | `custom-logo-mats.jpg` |
| 14 | Entrance Mats | `/floor-mats/entrance-mats/` | `entrance-mats.jpg` |
| 15 | Outdoor Scraper Mats | `/floor-mats/outdoor-scraper-mats/` | `outdoor-scraper-mats.jpg` |
| 16 | Coir Doormats | `/floor-mats/coir-doormats/` | `coir-doormats.jpg` |
| 17 | Recessed Entrance Matting | `/floor-mats/recessed-entrance-matting/` | `recessed-entrance-matting.jpg` |
| 18 | Anti-Fatigue Mats | `/floor-mats/anti-fatigue-mats/` | `anti-fatigue-mats.jpg` |
| 19 | Rubber Gym Mats | `/floor-mats/rubber-gym-mats/` | `rubber-gym-mats.jpg` |
| 20 | Anti-Slip & Wet Area Mats | `/floor-mats/anti-slip-wet-area-mats/` | `anti-slip-wet-area-mats.jpg` |
| 21 | PVC Coil Mats | `/floor-mats/pvc-coil-mats/` | `pvc-coil-mats.jpg` |
| 22 | Lift & Day Mats | `/floor-mats/lift-day-mats/` | `lift-day-mats.jpg` |
| 23 | Car Floor Mats | `/floor-mats/car-floor-mats/` | `car-floor-mats.jpg` |
| 24 | Kids Play Mats | `/floor-mats/kids-play-mats/` | `kids-play-mats.jpg` |
| 25 | Free Logo Mockup & Samples | `/services/free-logo-mockup-samples/` | `free-logo-mockup-samples.jpg` |
| 26 | Measuring & Installation | `/services/measuring-installation/` | `measuring-installation.jpg` |
| 27 | Corporate & Bulk Orders | `/services/corporate-bulk-orders/` | `corporate-bulk-orders.jpg` |
| 28 | Floor Mats in Dubai | `/areas-we-serve/floor-mats-dubai/` | `floor-mats-dubai.jpg` |
| 29 | Floor Mats in Dubai Marina | `/areas-we-serve/floor-mats-dubai-marina/` | `floor-mats-dubai-marina.jpg` |
| 30 | Floor Mats in Downtown Dubai | `/areas-we-serve/floor-mats-downtown-dubai/` | `floor-mats-downtown-dubai.jpg` |
| 31 | Floor Mats in Business Bay | `/areas-we-serve/floor-mats-business-bay/` | `floor-mats-business-bay.jpg` |
| 32 | Floor Mats in JLT | `/areas-we-serve/floor-mats-jlt/` | `floor-mats-jlt.jpg` |
| 33 | Floor Mats in Palm Jumeirah | `/areas-we-serve/floor-mats-palm-jumeirah/` | `floor-mats-palm-jumeirah.jpg` |
| 34 | Floor Mats in Al Quoz | `/areas-we-serve/floor-mats-al-quoz/` | `floor-mats-al-quoz.jpg` |
| 35 | Floor Mats in JVC | `/areas-we-serve/floor-mats-jvc/` | `floor-mats-jvc.jpg` |
| 36 | Floor Mats in Abu Dhabi | `/areas-we-serve/floor-mats-abu-dhabi/` | `floor-mats-abu-dhabi.jpg` |
| 37 | Floor Mats in Sharjah | `/areas-we-serve/floor-mats-sharjah/` | `floor-mats-sharjah.jpg` |
| 38 | Floor Mats in Ajman | `/areas-we-serve/floor-mats-ajman/` | `floor-mats-ajman.jpg` |
| 39 | Floor Mats in Ras Al Khaimah | `/areas-we-serve/floor-mats-ras-al-khaimah/` | `floor-mats-ras-al-khaimah.jpg` |
| 40 | Floor Mats in Fujairah | `/areas-we-serve/floor-mats-fujairah/` | `floor-mats-fujairah.jpg` |
| 41 | Floor Mats in Umm Al Quwain | `/areas-we-serve/floor-mats-umm-al-quwain/` | `floor-mats-umm-al-quwain.jpg` |
| 42 | Floor Mats in Al Ain | `/areas-we-serve/floor-mats-al-ain/` | `floor-mats-al-ain.jpg` |
