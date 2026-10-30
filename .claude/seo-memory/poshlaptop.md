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
- Phase 1: done in an earlier session (details not recorded in this repo; add them here).
- Phase 2: started 2026-10-30. Toolkit installed. Not done yet: Site Kit pull and page
  creation, both blocked on WPVibe quota that day.

## Pages created
| Date | URL | Primary keyword | Status |
|------|-----|-----------------|--------|
