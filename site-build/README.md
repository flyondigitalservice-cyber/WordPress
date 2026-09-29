# dubaicurtainexperts.ae — site build

Source for the 45 block-editor pages and the Drive image import.

- `data.py` – contacts, Drive image IDs (grouped by the owner's Drive folders), catalogue PDFs
- `products.py` – copy for 12 curtain and 10 blind pages
- `areas.py` – 8 Dubai + 5 UAE location pages
- `blocks.py` – serializer for core blocks (paragraph, heading, group, columns, image, gallery, buttons, list, details, shortcode)
- `build.py` – assembles every page; `python3 build.py [imagemap.json]` → `out/pages.json`, `out/html/*.html`, `out/import.json`

Validate before upload (uses WordPress's own block parser + validation):

    node validate.cjs out/html/*.html

Theme changes live in `wp-content/themes/dubai-curtain-experts/` (only the files added or rewritten
for this build are tracked here; the rest of the theme is on the server).
