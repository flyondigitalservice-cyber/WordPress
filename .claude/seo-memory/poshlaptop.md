# Posh Laptop (https://poshlaptop.com): SEO memory

Persistent notes for SEO work on Posh Laptop. Read this before any Posh Laptop SEO task
and update it at the end of each session.

## Access
- Live site is managed through the **WPVibe** MCP connector (site name "Posh Laptop").
  The repo itself is WordPress core and does not contain the site's content.
- WPVibe has a rolling 24h call limit (500). Check `list_sites` before a big batch.
  Batch reads (`per_page=100`, `fields=`) to save calls.
- Cloud sessions can't reach poshlaptop.com directly unless it's added to the
  environment's allowed network domains.

## SEO toolkit
- claude-seo (AgriciDaniel/claude-seo, MIT, installed in `.claude/skills`, `.claude/agents`,
  runtime in `.claude/claude-seo`). Relevant skills for this project:
  - `seo-cluster`: build long-tail keyword clusters from a seed
  - `seo-content-brief`: brief per page (keywords, outline, internal links)
  - `seo-programmatic`: templated long-tail page sets without thin/duplicate content
  - `seo-schema`: Product / FAQPage / BreadcrumbList JSON-LD
  - `seo-ecommerce`, `seo-local`, `seo-page`, `seo-google` (GSC/GA4 via API keys)

## Phase 2 workflow (long-tail SEO pages)
1. **Pull Site Kit data** via WPVibe REST:
   - `GET /google-site-kit/v1/modules/search-console/data/searchanalytics`
     with `dimensions=query,page`, `startDate`/`endDate` (last 90 days), `limit=500`.
   - Optionally `.../modules/analytics-4/data/report` for landing-page sessions.
2. **Pick targets**: queries at position 8–40 with impressions but few clicks, plus
   queries with no matching page. Group them into clusters (`seo-cluster`).
3. **Check for cannibalisation**: list existing pages/posts/products
   (`/wp/v2/pages`, `/wp/v2/posts`, `/wc/v3/products`) so a new page doesn't
   target a query an existing URL already ranks for. Improve that URL instead.
4. **Write each page** (`seo-content-brief`, then the content):
   - one primary long-tail keyword in the title, H1, slug, first 100 words, and meta description
   - original content: specs tables, buying advice, FAQs drawn from real GSC queries
   - internal links to the matching product/category pages and 2–3 sibling long-tail pages
   - FAQPage + BreadcrumbList schema (via the active SEO plugin's fields where possible)
5. **Publish as draft** first, check it with the user, then publish and add it to the sitemap/menu hub.
6. **Log** every created page below (URL, target keyword, date) so later sessions don't duplicate it.

## Phase log
- Phase 1 (seen on site): product-tag landing pages (e.g. /product-tag/used-laptop-uae/), country pages
  /sa/ and /qa/, and 4 draft persona pages: laptops-for-designers / -architects / -it-professionals,
  laptop-rental-dubai (IDs 10595-10598, still drafts).
- Phase 2 (2026-10-30): Site Kit GSC pull (Aug 1 - Oct 29) showed only 12 query/page rows, all
  tiny impressions. Rankings came from product-tag pages: "chromebook price in uae" (38.5),
  "laptop i7 12th gen 16gb 512gb" (9), "used laptop computers for sale" (12), "elitebook 430 g8" (8),
  "second hand laptop" (44.7), "laptop shop near me" (41, homepage).
  Created 6 long-tail DRAFT pages (Gutenberg blocks, FAQ + FAQPage JSON-LD, price tables from live
  stock, internal links to products/tags/each other). Rank Math title/description/focus keyword set via
  POST /rankmath/v1/updateMeta (one call per page). Sources in seo/poshlaptop/phase2/.

- Phase 3 (2026-10-31): blog launched. Competitor uaepc.com/en/blog (B2B wholesaler, Sharjah) used ONLY
  for topic ideas: its content is copyrighted (their image-license page), never copy text. uaepc.com is
  blocked from cloud sessions (network allowlist); topics found via WebSearch.
  Category "Laptop Buying Guides" (ID 495, /category/laptop-buying-guides/). Post permalinks: /%postname%/.
  6 original posts built in seo/poshlaptop/blog/ (build.py -> <slug>.json request bodies incl. category).
  All 6 uploaded as DRAFT posts in category 495 with Rank Math meta set (2026-10-31):
  10631 buy-laptop-in-sharjah, 10632 refurbished-laptop-warranty-uae, 10633 refurbished-laptop-grades-explained,
  10634 thinkpad-vs-elitebook-vs-latitude, 10635 windows-11-compatible-refurbished-laptops,
  10636 bulk-refurbished-laptops-for-business-uae. Awaiting owner review before publishing.
  Claims to confirm with owner before publish: Windows 11 install before delivery, RAM/SSD upgrades,
  bulk-order setup services. The theme has no blog index page yet (page_for_posts unknown); link posts via
  footer menu 494 or a blog page after publishing.
  NOTE 2026-10-31: WPVibe's own plugin routes (/wpvibe/v1, used by run_wp_cli) returned 404 — plugin may be
  deactivated/blocked; core /wp/v2 and /rankmath/v1 still worked. Ask the user to re-check WPVibe plugin.

## Site facts (confirmed from site content)
- Business: Posh Touch Computers, UAE showroom, prices in AED. WhatsApp +971 52 292 0920,
  marketing@poshlaptop.com. 6-month warranty, 15-day returns, services: sales, rental, repair, AMC.
- Theme: custom "Posh Touch" (page.php); older pages use Elementor; new SEO pages use plain blocks.
- SEO plugin: Rank Math. Analytics: Site Kit (Search Console connected). Cache: NitroPack.
- Rank Math meta: write with POST /rankmath/v1/updateMeta {objectType:"post",objectID,meta:{...}}.
- Showroom: Shop 01, Warehouse 02, Madinat Al Salam Warehouses, Industrial Area 5 (behind ENOC 1127),
  Sharjah (from theme footer, posh_contact()). Use Sharjah, not Dubai, for any location/LocalBusiness page.
- Footer: hard-coded in theme footer.php. Since 2026-10-30 it shows a "Buying guides" column that renders
  the menu in theme location `footer` = menu "Buying Guides" (ID 494). To link a new SEO page in the
  footer: `menu item add-post 494 <page_id> --title="..."`. No theme edit needed.

## Next ideas (phase 3)
- Phase 2 pages published 2026-10-30 and linked from the footer "Buying guides" column. Still to do: request indexing in Search Console.
- "laptop shop near me" (pos 41): build a Sharjah showroom/location page with the address above +
  LocalBusiness schema; confirm opening hours with the owner.
- "i7 12th gen 16gb 512gb" (pos 9 on tag page): enrich that tag page's description rather than a new URL.
- Re-pull GSC ~4 weeks after publishing to measure.

## Pages created
| Date | URL | Primary keyword | Status |
|------|-----|-----------------|--------|
| 2026-10-30 | /chromebook-price-in-uae/ (10613) | chromebook price in uae | published 2026-10-30 |
| 2026-10-30 | /second-hand-laptop-dubai/ (10614) | second hand laptop dubai | published 2026-10-30 |
| 2026-10-30 | /refurbished-workstation-laptop-dubai/ (10615) | refurbished workstation laptop dubai | published 2026-10-30 |
| 2026-10-30 | /used-macbook-dubai/ (10616) | used macbook dubai | published 2026-10-30 |
| 2026-10-30 | /hp-elitebook-vs-probook/ (10617) | hp elitebook vs probook | published 2026-10-30 |
| 2026-10-30 | /2-in-1-detachable-laptop-uae/ (10618) | 2 in 1 detachable laptop uae | published 2026-10-30 |
