=== Propzed Projects, Sales & Assistant ===
Requires at least: 6.2
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPL-2.0-or-later

Add-on for the Propzed theme: Sold / Leased Out listings, villas and residential projects with an interactive floor plan and live unit availability, guide SEO, a property assistant chat and an AI-ready llms.txt. No theme files are changed.

== What it adds ==

1. SOLD / LEASED OUT
   * Properties list: "Mark Sold" button in a new Sale column, row actions (Mark as Sold, Leased Out, Mark Available) and bulk actions.
   * Edit screen: "Listing outcome" box with one-click buttons, deal date, and a private closing price and note (never shown on the website).
   * Front end: when you are logged in, the admin bar on a property page shows "Mark as Sold".
   * Visitors see a SOLD stamp on cards and a "This property has been sold" banner with links to similar listings and the requirement form (keeps Google traffic converting).
   * Listing pages show sold listings after available ones (or hide them, see settings). The page stays online for SEO; Google sees availability "SoldOut" and "(Sold)" in the title.
   * Uses new "Sold" and "Leased Out" terms in Listing Status, so the theme's own badges and /status/sold/ page work.

2. VILLAS, RESIDENTIAL PROJECTS, FLOOR PLANS
   * New property types: Villas & Bungalows, Residential Projects, Flats & Apartments, Villa Plots (NA). SEO pages such as /villa-in-karjat/ work automatically.
   * New "Residential / Villa details" fields (configurations, carpet range, plot size, bedrooms, towers, MahaRERA no., amenities...) shown in the Specifications table.
   * New locations: Karjat, Khopoli, Neral, Pen, Lonavala, Dronagiri, Kamothe, Badlapur, Mira Road, Kandivali, Malad, Chembur, Ghatkopar.
   * "Floor Plans & Unit Inventory" box on every property:
       - Add a plan per tower (or one master layout for villas / plots), set floors ("1-20", "G,1-5"), upload the plan image.
       - Drag on the image to draw each flat / villa; set type (2 BHK), carpet, facing, base price.
       - Building chart of every unit: pick "Click a unit to: mark Booked" and click units to punch them in one go, or click one unit to set status, price, booking date and private buyer name / phone / note.
   * Property page: "Floor Plans & Live Availability" with % booked, last booking date, filter by type, plan with coloured units, building chart, and a unit card with Enquire (fills the sidebar form) and WhatsApp buttons. Buyer details are never sent to the browser.
   * Projects are marked Sold automatically when every unit is booked (setting).
   * "12 of 48 units left" chip on property cards.
   * Shortcode [propzed_inventory id="123"], REST: /wp-json/propzed-projects/v1/inventory/{id}, Schema.org ApartmentComplex with unit counts.

3. GUIDES / BLOG SEO
   * BlogPosting, BreadcrumbList and FAQPage JSON-LD on posts. FAQs are written as:
     <details class="pzs-faq"><summary>Question?</summary><p>Answer.</p></details>
   * Automatic "In this guide" table of contents on posts with 3+ H2 headings.
   * [propzed_listings type="villa" location="karjat" count="3" title="..."]  live cards inside a post (sold listings skipped).
   * [propzed_emi price="7500000"]  EMI, cash-needed and stamp duty calculator.
   * [propzed_cta topic="Karjat villas"]  advisor box with requirement and WhatsApp buttons.
   * Six starter guides (content/guides/): Properties > Sales & Projects > "Create starter guides as drafts".

4. PROPERTY ASSISTANT (chat)
   * Guided chat: buy / lease / invest / sell, type, location, budget, then live matching listings and lead capture.
   * Answers stamp duty, GST, RERA, home loan and NA-land questions, understands "2 bhk in ulwe under 80 lakh".
   * On a property page it offers site visit, price sheet and unit availability.
   * Leads go to Enquiries through the theme's /propzed/v1/enquiry API: same inbox, email alert and spam limit as the forms. A "generate_lead" event is sent to Google Analytics / Tag Manager if present.
   * Runs in the browser. No AI subscription or API key.

5. AI & SEARCH VISIBILITY
   * /llms.txt with contact details, types, locations, available listings with price and area, projects with units left, guides, recent deals and the JSON API. Rebuilt a minute after any change. Replaces the basic Hostinger file (the Hostinger plugin leaves a custom file alone).
   * robots.txt: keeps sort / keyword / budget filter URLs out of crawls and points to llms.txt.

== Install ==

1. Upload the "propzed-projects" folder to wp-content/plugins/ (the site blocks uploads from wp-admin, so use the Hostinger File Manager, FTP or Git deployment).
2. Plugins > activate "Propzed Projects, Sales & Assistant".
3. Properties > Sales & Projects: review the settings, click "Save and rebuild llms.txt now" and "Create starter guides as drafts".
4. Purge LiteSpeed Cache.

== Safety ==

* Loads only with the Propzed theme; otherwise it shows a notice and does nothing.
* Deactivating restores the site as before. Sold / Leased Out terms, inventories and guides stay in the database.
* Buyer names, phones, notes and closing prices are stored as private post meta and are never output on the website, in the REST API or in llms.txt.

== Changelog ==

= 1.0.0 =
* First release.
