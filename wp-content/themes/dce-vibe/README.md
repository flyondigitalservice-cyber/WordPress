# DCE Vibe — Dubai Curtain Experts theme

A clean, editorial WordPress theme for **Dubai Curtain Experts**, the curtains and blinds studio of [Casa Vera Home](https://casaverahome.ae/).
It ships with 52 SEO pages built from editable core blocks, 87 optimised photos from the curtain and blind Drive folders,
WhatsApp lead capture, and structured data for local SEO.

## Install

1. Back up the live site (your host's backup or a backup plugin).
2. Upload `wp-content/themes/dce-vibe` and activate it in **Appearance → Themes**.
3. Open **Appearance → DCE Site Setup** and click **Build / update the site**.
   - Pages are matched by their current URL (e.g. `/curtains/wave-curtains/`), so existing links keep working.
   - Updated pages keep a revision, so you can restore an older version from the editor.
   - Running it again is safe: images are not duplicated.
4. Check **Appearance → Customize → Site Identity** for the logo (the existing logo is used automatically).

## What gets built

| Group | Pages |
| --- | --- |
| Core | Home, Services, Fabric Catalogues, Projects, About Us, Contact Us, Blog, Privacy Policy |
| Curtains (12) | Wave, Pinch Pleat, Eyelet, American Style, Roman, Blackout, Sheer, Motorized, Beaded, Cinema & Home Theatre, Kids, Hospital |
| Blinds (10) | Blackout Roller, Sunscreen Roller, Zebra, Roman, Vertical, Wooden Venetian, Aluminium Venetian, Bamboo, Custom Printed, Logo Sunscreen |
| Dubai areas (12) | Marina & JBR, Downtown & Business Bay, Palm Jumeirah, Jumeirah & Umm Suqeim, Dubai Hills & Arabian Ranches, JVC/JLT & Dubai South, Deira & Bur Dubai, Mirdif & Festival City, **Al Barsha & Al Sufouh**, **Emirates Hills/Springs/Meadows**, **Silicon Oasis & International City**, **DAMAC Hills/Motor City** |
| UAE (7) | Abu Dhabi, Sharjah, Ajman, Ras Al Khaimah, Al Ain, **Fujairah**, **Umm Al Quwain** |

New pages are in **bold**. Hubs: Curtains, Blinds and Areas We Serve.

## Editing

- **Page content:** Pages → Edit. Every section is a standard block (group, columns, image, heading, list, buttons, details).
- **Header / footer / contact details:** Appearance → Customize → *DCE Business, Header & Footer*. This covers brand name, phone, WhatsApp number and default message, lead email, address, map link, hours, the top bar, the header button, footer text, social links and the Casa Vera Home strip.
- **Menus:** Appearance → Menus. The theme has five menu locations: header plus four footer columns.
- **Smart links:** use these as the URL of any button, link or menu item:
  `#whatsapp` (WhatsApp with a message naming the current page), `#call`, `#email`, `#map`, `#quote`, `#parent` (Casa Vera Home).
- **Shortcodes:** `[dce_lead_form service="Wave curtains" area="Dubai Marina / JBR"]`, `[dce_info key="phone|email|address|hours"]`.
- **Patterns:** in the block inserter, open Patterns → *Dubai Curtain Experts* to add new sections in the same style (quote form, CTA band, FAQ, steps, tiles, catalogue cards, parent company).
- **SEO:** each page and post has an *SEO* box with meta title, meta description, service name (for Service schema) and noindex. The FAQ blocks automatically become FAQPage schema. If Yoast, Rank Math, AIOSEO or SEOPress is active, the theme's SEO output switches off.

## Leads — nothing gets missed

Every quote form submission is:
1. saved under **Leads** in the admin (name, phone, service, area, source page),
2. emailed to the lead email set in the Customizer,
3. opened in WhatsApp with all the details pre-filled.

Every WhatsApp button sends a message that names the page it was clicked on.

## Catalogues

The fabric catalogue PDFs are 3–17 MB each, so they stay on Google Drive and open from the buttons on the **Fabric Catalogues** page.
Make sure the Drive files and folders are shared as **Anyone with the link → Viewer**.

## Images

Photos live in `assets/img/` (`manifest.json` lists the alt text) and are imported into the Media Library on setup.
Some categories only had small thumbnails on Drive: Blackout Roller, Sunscreen Roller and Hospital, plus extra photos for Kids, Zebra, Vertical and Logo blinds.
For these, the importer reuses the matching photos already in the site's Media Library (for example `blackout-roller-blinds-dubai-1.jpg`).
