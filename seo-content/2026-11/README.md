# SEO pages — November 2026 (customfloormats.ae)

Source for the 50 SEO pages added to customfloormats.ae:

- 12 new Dubai area pages for floor mats (children of *Areas We Serve*)
- *Curtains, Blinds & Carpets Across the UAE* hub + 21 pages (curtains / blinds / carpets × Abu Dhabi, Al Ain, Sharjah, Ajman, Ras Al Khaimah, Fujairah, Umm Al Quwain)
- *Events & Exhibitions* hub + 5 pages
- *Buying Guides & Top Choices* hub + 5 "best of" guides + 4 "how to choose a provider" guides

Files:

- `data_*.py` — the page content (edit text here)
- `blocks.py`, `build.py` — build block markup locally (`python3 build.py` → `pages.json`)
- `seo-pages-data.json` — the same data exported as JSON (what the site importer reads)
- `cfm-seo-builder.php` — PHP port of the builder (output verified identical to `build.py`)
- `cfm-seo-import.php` — one-time importer: creates missing pages only, never overwrites

Regenerate the JSON after editing the Python data:

```
python3 -c "import json,data_areas as a,data_interiors as i,data_events_guides as e; json.dump(dict(DUBAI_AREAS=a.DUBAI_AREAS,EMIRATES=i.EMIRATES,SERVICES=i.SERVICES,EVENTS_HUB=e.EVENTS_HUB,EVENTS=e.EVENTS,GUIDES_HUB=e.GUIDES_HUB,TOP_GUIDES=e.TOP_GUIDES,PROVIDER_GUIDES=e.PROVIDER_GUIDES),open('seo-pages-data.json','w'),ensure_ascii=False,separators=(',',':'))"
```

## Cross-links (added after the import)

- `xlinks.py` / `cfm-xlinks.php` — insert a links section before the "Other areas" pills on 48 area pages:
  emirate floor-mat pages → curtains/blinds/carpets in the same emirate; emirate interiors pages → floor mats
  and the other two services there; Dubai area pages → Dubai curtains, blinds, carpets, events and guides.
  Idempotent: pages that already contain the section are skipped.
