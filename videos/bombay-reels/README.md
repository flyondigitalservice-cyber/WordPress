# Bombay Marketing — Instagram Reels Campaign (9 reels)

Design system and storyboard: [`CAMPAIGN.md`](CAMPAIGN.md).

## Deliverables (`deliverables/`)

| # | Reel | Length | Files |
|---|---|---|---|
| 01 | We Are Bombay Marketing | 20 s | `v01-we-are-bombay-marketing/` |
| 02 | Your Website Is Your Digital Storefront | 20 s | `v02-your-website-is-your-digital-storefront/` |
| 03 | Be Found. Be Chosen. | 20 s | `v03-be-found-be-chosen/` |
| 04 | Own Your Local Presence | 18 s | `v04-own-your-local-presence/` |
| 05 | Attention Is the New Currency | 20 s | `v05-attention-is-the-new-currency/` |
| 06 | Turn Clicks Into Customers | 20 s | `v06-turn-clicks-into-customers/` |
| 07 | Make Your Brand Unforgettable | 20 s | `v07-make-your-brand-unforgettable/` |
| 08 | Ideas Into Impact | 22 s | `v08-ideas-into-impact/` |
| 09 | Let's Build Something Bigger | 20 s | `v09-lets-build-something-bigger/` |

Each folder holds the **MP4** (1080×1920, 30 fps, H.264 + AAC, about −17 LUFS, no clipping), a **cover JPG** (the hook frame) and a **voiceover transcript** with in/out timecodes. `campaign-covers.jpg` shows all nine covers side by side.

## Editable source (`v01/` … `v09/`)

Each reel is a HyperFrames project: one `index.html` (HTML + GSAP timeline), with all its assets under `assets/`. Copy, colours, timings and media are plain text.

```bash
cd videos/bombay-reels/v01
npx hyperframes preview        # open in Studio
npx hyperframes render . -q high -o renders/out.mp4
```

The shared design kit lives in `_shared/kit/` (`brand.css` = tokens, type, end card; `kit.js` = motion helpers such as the Blue Shutter, word reveals and the signature end card). `_shared/scaffold.sh` copies it into a new reel.

## Assets and integrity

- Logo, icon and every portfolio/packaging image are the agency's own published files from bombaymarketing.in (`_brand/raw/`). The logo is never recoloured, cropped or distorted.
- No testimonials, statistics, ratings, rankings or results appear anywhere. The visibility chart in 03 and the funnel in 06 have no numbers or axes.
- Interfaces such as search, the business profile, the social feed and the ad are drawn generically, with no Google, Meta or Instagram marks.
- Reel 08 industries: E-commerce (Denim State, Hadrami Fashion) · Manufacturing & Trade (Custom Floor Mats, Dolly Exports) · Consumer Brands (One Love Energy, Naked Life) · Professional Services (Experia Consultancy, Propzed).

## Audio

- **Voiceover:** on-device Kokoro TTS (`af_heart`). To use a human recording, replace `vNN/assets/vo/vNN_K.wav` (same names; one file per line in `_shared/vo_lines.json`), adjust each `<audio id="voK">` `data-start` and `data-duration` if the timing changed, and re-render.
- **SFX:** bundled Pixabay-licensed effects (see `assets/sfx/CREDITS.md`). The sonic logo is `impact-bass-1` plus `chime` on every logo landing.
- **Music:** not included, because no licensed music source was connected. To add one, put the track in `vNN/assets/` and add `<audio id="music" class="clip" src="assets/music.mp3" data-start="0" data-duration="20" data-volume="0.14" data-fade-out="1" data-track-index="9"></audio>` before re-rendering.
