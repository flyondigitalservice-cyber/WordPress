# Dubai Gym Flooring — WordPress theme

A custom, fully editable theme for **dubaigymflooring.com** (a Casa Vera Home company).
It builds a 45-page SEO site, sends every lead to WhatsApp **and** saves it in wp-admin, and turns uploaded PDFs into catalogue download buttons.

No page-builder plugin is needed: all page content uses core Gutenberg blocks.

---

## 1. Install (5 minutes)

1. Upload the `dubai-gym-flooring` folder to `wp-content/themes/` (or zip it and use **Appearance → Themes → Add New → Upload**).
2. **Appearance → Themes → Activate.** On first activation the theme automatically:
   - creates all 45 pages (below) with SEO titles, meta descriptions and FAQ schema,
   - creates the header menu and 4 footer menus,
   - sets **Home** as the front page and the Privacy Policy page,
   - switches permalinks to `/%postname%/` if they were "Plain",
   - moves WordPress's untouched "Sample Page" / "Hello world!" to the bin.
   Existing pages with the same slug are **never overwritten**.
3. **Appearance → DGF Setup → WhatsApp number** — enter the business WhatsApp number (e.g. `9715XXXXXXXX` or `05XXXXXXXX`). It defaults to +971 50 859 9803; change it here or in the Customizer at any time.
4. **Appearance → Customize → Dubai Gym Flooring** — phone, email, address, hours, map, socials, footer text, mother company link, home-page highlight numbers, lead email.
5. **Appearance → Customize → Site Identity** — upload the logo and site icon (favicon). Until then a built-in wordmark is shown.

## 2. Images from the Google Drive folder

1. Download the Drive folder ("GYM FLOORINGS") and export any `.HEIC` photos as `.jpg` (browsers can't show HEIC).
2. Upload the files (keep sub-folders if you like) to `wp-content/uploads/dgf-import/` via FTP or your host's File Manager.
3. **Appearance → DGF Setup → Import images now.**

Matching rules:
- A file **or folder** named after a page's slug is attached to that page: `rubber-gym-flooring.jpg`, `rubber-gym-flooring-2.jpg`, or a folder `rubber-gym-flooring/`.
- Common names also match: `EPDM`, `Turf`, `Sled`, `Interlocking`, `Rolls`, `Vinyl`, `PVC`, `CrossFit`, `Deadlift`, `Acoustic`, `Outdoor`, `Kids`/`Playground`, `Yoga`/`Pilates`, `Home gym`, `Commercial`, emirate and area names, etc.
- The first image on a page becomes its **hero/featured image**; the rest appear in that page's photo gallery and on **Projects**.
- A file named `logo…` becomes the site logo (if none is set).
- **PDFs** become catalogue buttons (View / Download / Ask price on WhatsApp) on **Catalogues** and on the matching product page.
- Anything that matches no page goes to **Projects** and is listed in the import report.
- Re-running the import skips files already imported.

You can always change a picture by hand: edit the page → **Featured image**. Upload more catalogues any time in **Media → Add New** — every PDF in the Media Library shows on the Catalogues page automatically.

WP-CLI equivalents: `wp dgf install`, `wp dgf import-images`, `wp dgf whatsapp 9715XXXXXXXX`, `wp dgf list`, `wp dgf rebuild <slug>`.

## 3. Editing

| What | Where |
|---|---|
| Page text, headings, lists, buttons, FAQs | Edit the page (block editor) |
| Hero heading / sub-heading / background | "Page hero" box (heading), **Excerpt** (sub-heading), **Featured image** (background) |
| SEO title, meta description, focus keyword, noindex | "SEO (Dubai Gym Flooring)" box under each page — steps aside automatically if Yoast / Rank Math / AIOSEO is installed |
| Header & footer menus | Appearance → Menus |
| Header/footer text, contact details, socials, mother company | Appearance → Customize → Dubai Gym Flooring |
| Leads | wp-admin → **Leads** (each has a one-click "Chat" WhatsApp button) |
| Restore a page's starter content | Appearance → DGF Setup → **Rebuild** (overwrites that page only) |

**WhatsApp links anywhere:** set any button or link URL to `#whatsapp` (also works in menus) — it opens WhatsApp with the page name prefilled, and follows the number in the Customizer.

**Dynamic sections** are shortcode blocks you can move or delete:
`[dgf_quote_form]`, `[dgf_cta]`, `[dgf_whatsapp]`, `[dgf_children parent="gym-flooring-products"]`, `[dgf_children parent="areas-we-serve" style="pills"]`, `[dgf_related]`, `[dgf_catalogues]`, `[dgf_gallery]`, `[dgf_stats]`, `[dgf_contact_info]`, `[dgf_map]`, `[dgf_parent_company]`.

## 4. Leads — nothing is lost

Every form submission is (1) saved as a private **Lead** in wp-admin, (2) emailed to the lead address (Customize → Leads, default: admin email), then (3) opened in WhatsApp with all details prefilled. It still works with JavaScript disabled. A honeypot and rate-limit block spam. WhatsApp clicks and leads are pushed to `dataLayer` (`whatsapp_click`, `generate_lead`) for GA4/GTM.

## 5. SEO built in

Unique title + meta description per page, Open Graph/Twitter tags, JSON-LD (`HomeAndConstructionBusiness` with Casa Vera Home as `parentOrganization`, `Service` with `areaServed`, `BreadcrumbList`, `FAQPage`), breadcrumbs, one H1 per page, internal linking between products and areas, and WordPress's `wp-sitemap.xml` (noindex pages excluded). Submit `https://dubaigymflooring.com/wp-sitemap.xml` in Google Search Console.

**Before launch, please check:** real phone/WhatsApp/email/address, the home-page highlight numbers (Customize → Home page highlights), and the starter Privacy Policy / Terms text with your legal adviser. Product specifications are typical industry ranges — align them with your actual catalogues.

## 6. The 45 pages

| # | Page | URL | Image file name |
|---|---|---|---|
| 1 | Home | `/home/` | `home.jpg` |
| 2 | About Us | `/about/` | `about.jpg` |
| 3 | Gym Flooring Products | `/gym-flooring-products/` | `gym-flooring-products.jpg` |
| 4 | Services | `/services/` | `services.jpg` |
| 5 | Areas We Serve | `/areas-we-serve/` | `areas-we-serve.jpg` |
| 6 | Catalogues | `/catalogues/` | `catalogues.jpg` |
| 7 | Projects | `/projects/` | `projects.jpg` |
| 8 | FAQs | `/faq/` | `faq.jpg` |
| 9 | Contact Us | `/contact/` | `contact.jpg` |
| 10 | Get a Free Quote | `/get-a-free-quote/` | `get-a-free-quote.jpg` |
| 11 | Privacy Policy | `/privacy-policy/` | `privacy-policy.jpg` |
| 12 | Terms & Conditions | `/terms-and-conditions/` | `terms-and-conditions.jpg` |
| 13 | Rubber Gym Flooring | `/gym-flooring-products/rubber-gym-flooring/` | `rubber-gym-flooring.jpg` |
| 14 | Interlocking Rubber Gym Tiles | `/gym-flooring-products/interlocking-rubber-gym-tiles/` | `interlocking-rubber-gym-tiles.jpg` |
| 15 | Rubber Flooring Rolls | `/gym-flooring-products/rubber-flooring-rolls/` | `rubber-flooring-rolls.jpg` |
| 16 | EPDM Rubber Flooring | `/gym-flooring-products/epdm-rubber-flooring/` | `epdm-rubber-flooring.jpg` |
| 17 | Gym Turf & Sled Track | `/gym-flooring-products/gym-turf-sled-track/` | `gym-turf-sled-track.jpg` |
| 18 | Outdoor Gym Flooring | `/gym-flooring-products/outdoor-gym-flooring/` | `outdoor-gym-flooring.jpg` |
| 19 | Vinyl Sports Flooring | `/gym-flooring-products/vinyl-sports-flooring/` | `vinyl-sports-flooring.jpg` |
| 20 | PVC Gym Flooring | `/gym-flooring-products/pvc-gym-flooring/` | `pvc-gym-flooring.jpg` |
| 21 | CrossFit Flooring | `/gym-flooring-products/crossfit-flooring/` | `crossfit-flooring.jpg` |
| 22 | Deadlift Platforms | `/gym-flooring-products/deadlift-platforms/` | `deadlift-platforms.jpg` |
| 23 | Acoustic & Anti-Vibration Gym Flooring | `/gym-flooring-products/acoustic-gym-flooring/` | `acoustic-gym-flooring.jpg` |
| 24 | Home Gym Flooring | `/gym-flooring-products/home-gym-flooring/` | `home-gym-flooring.jpg` |
| 25 | Commercial Gym Flooring | `/gym-flooring-products/commercial-gym-flooring/` | `commercial-gym-flooring.jpg` |
| 26 | Kids Play Area Flooring | `/gym-flooring-products/kids-play-area-flooring/` | `kids-play-area-flooring.jpg` |
| 27 | Yoga & Pilates Studio Flooring | `/gym-flooring-products/yoga-pilates-studio-flooring/` | `yoga-pilates-studio-flooring.jpg` |
| 28 | Gym Flooring Installation | `/services/gym-flooring-installation/` | `gym-flooring-installation.jpg` |
| 29 | Gym Floor Repair & Replacement | `/services/gym-floor-repair-replacement/` | `gym-floor-repair-replacement.jpg` |
| 30 | Free Site Survey & Samples | `/services/free-site-survey-samples/` | `free-site-survey-samples.jpg` |
| 31 | Gym Flooring in Dubai | `/areas-we-serve/gym-flooring-dubai/` | `gym-flooring-dubai.jpg` |
| 32 | Gym Flooring in Dubai Marina | `/areas-we-serve/gym-flooring-dubai-marina/` | `gym-flooring-dubai-marina.jpg` |
| 33 | Gym Flooring in Downtown Dubai | `/areas-we-serve/gym-flooring-downtown-dubai/` | `gym-flooring-downtown-dubai.jpg` |
| 34 | Gym Flooring in Business Bay | `/areas-we-serve/gym-flooring-business-bay/` | `gym-flooring-business-bay.jpg` |
| 35 | Gym Flooring in JLT | `/areas-we-serve/gym-flooring-jlt/` | `gym-flooring-jlt.jpg` |
| 36 | Gym Flooring in Palm Jumeirah | `/areas-we-serve/gym-flooring-palm-jumeirah/` | `gym-flooring-palm-jumeirah.jpg` |
| 37 | Gym Flooring in Al Quoz | `/areas-we-serve/gym-flooring-al-quoz/` | `gym-flooring-al-quoz.jpg` |
| 38 | Gym Flooring in JVC | `/areas-we-serve/gym-flooring-jvc/` | `gym-flooring-jvc.jpg` |
| 39 | Gym Flooring in Abu Dhabi | `/areas-we-serve/gym-flooring-abu-dhabi/` | `gym-flooring-abu-dhabi.jpg` |
| 40 | Gym Flooring in Sharjah | `/areas-we-serve/gym-flooring-sharjah/` | `gym-flooring-sharjah.jpg` |
| 41 | Gym Flooring in Ajman | `/areas-we-serve/gym-flooring-ajman/` | `gym-flooring-ajman.jpg` |
| 42 | Gym Flooring in Ras Al Khaimah | `/areas-we-serve/gym-flooring-ras-al-khaimah/` | `gym-flooring-ras-al-khaimah.jpg` |
| 43 | Gym Flooring in Fujairah | `/areas-we-serve/gym-flooring-fujairah/` | `gym-flooring-fujairah.jpg` |
| 44 | Gym Flooring in Umm Al Quwain | `/areas-we-serve/gym-flooring-umm-al-quwain/` | `gym-flooring-umm-al-quwain.jpg` |
| 45 | Gym Flooring in Al Ain | `/areas-we-serve/gym-flooring-al-ain/` | `gym-flooring-al-ain.jpg` |
