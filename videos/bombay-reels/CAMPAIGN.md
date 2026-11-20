# Bombay Marketing — Nine-Reel Campaign

**Campaign line:** *Your Digital Growth Partner.*
**Format:** 1080 × 1920 · 9:16 · 30 fps · H.264 MP4 + AAC · 18–22 s per reel (per-video timelines in the brief; the 45–60 s figure in the master brief is superseded by each video's own timing).
**Engine:** HyperFrames (HTML + GSAP, rendered frame-by-frame, deterministic). Each reel is an editable project in `vNN/`.

---

## 1. Shared design system

### 1.1 Colour (brief palette — nothing else is introduced)

| Token | Hex | Role |
| --- | --- | --- |
| `--white` | `#FFFFFF` | Light grounds, type on dark |
| `--black` | `#111111` | Dark grounds, type on light |
| `--blue` | `#245BFF` | The one accent: the Blue Line, CTAs, active states, shutter |
| `--grey` | `#F3F4F6` | UI surfaces, card grounds |
| `--hair` | `#E3E5EA` | Hairlines (tint of grey) |
| `--mute` | `#6B7280` | Secondary copy (tint of black) |

The **official logo is used exactly as supplied** (from bombaymarketing.in: `Bombay-Marketing-Logo-White.png` on dark, `-Dark.png` on light, `Icon` for the series tag). Its taxi-yellow plate is never recoloured, cropped or stretched; it is the only place yellow appears in the campaign, which makes every logo moment pop.

### 1.2 Typography

- **Display:** Inter Display Black (900), uppercase, tracking −0.045em, leading 0.9. Hook lines 128–150 px; end-card headline 96 px.
- **Headline:** Inter Display Bold (700), 72–84 px, tracking −0.03em.
- **Body / VO captions:** Inter 500, 40–44 px.
- **Labels:** Inter 600, 28 px, uppercase, tracking +0.16em.
- Max ~5 words per line for claims. Nothing read is under 28 px. All copy sits in the Reels safe zone (x 120–888, y 288–1248).

### 1.3 Grid & space

Swiss 6-column grid inside the 768 px safe width; 8 px spacing unit; cards radius 36 px (outer) / 20 px (inner); soft shadow `0 40px 80px rgba(17,17,17,.18)`.

### 1.4 Recurring motifs (in every reel)

1. **The Blue Line** — a 10 px electric-blue rule that draws on with `expo.out`. It is the underline of every hook, becomes the growth/visibility line in data moments, and is the leading edge of the shutter.
2. **The Frame** — one rounded white UI card in 3D perspective (rotateX 18° → 0°) that holds every interface: browser, phone, search, map, profile, feed, ad, funnel.
3. **The Blue Shutter** — the campaign transition. A full-frame blue panel rises from the bottom (0.45 s, `expo.inOut`), holds 2 frames, exits through the top revealing the next scene. Used once mid-reel and once into the end card.
4. **Series tag** — the official icon (56 px) + `0N / 09` + reel title, top-left at y≈300, Mute grey. Appears after the hook, gives the nine reels a numbered "collection" feel.
5. **The Signature end card** (final ~3.5 s, identical structure in all nine) — black ground; headline lands word-by-word; Blue Line draws; white logo rises with a 6 % camera push; `bombaymarketing.in` fades in; sonic logo.

### 1.5 Motion language

- Eases: entrances `expo.out` (0.6–0.9 s), exits `power3.in` (0.35 s), camera `sine.inOut`. No bounce, no elastic, no spin.
- Kinetic type: word-by-word from y+60 %, mask reveal; letters push on Z for "depth" moments (perspective 1200 px).
- Camera: slow continuous push (scale 1 → 1.06) per scene so no frame is ever static; parallax by giving foreground cards 1.4× the background's travel.
- Pacing: a new beat every 2–4 s; first legible line on screen by frame 6.

### 1.6 Sound

- **Voiceover:** Kokoro `af_heart` (on-device), one clip per line, timed so on-screen type lands on the spoken word. Replace with a recorded human VO by swapping the files in `vNN/assets/vo/` (same names).
- **SFX** (bundled, Pixabay licence): `whoosh` on shutter/scene moves, `key-press`/`typing` on typed text, `click-soft` on cursor clicks, `pop` on chips/pins.
- **Sonic logo** (same in all nine): `impact-bass-1` under the logo landing + `chime` 0.12 s later, −6 dB.
- **Music bed:** not included — no licensed music source is connected in this environment. Each reel reserves an audio lane (`#music`) for the brand's track; see the README for dropping it in.

---

## 2. Storyboard — all nine reels

Each row: time · picture · on-screen type · motion/camera · sound.

### 01 — WE ARE BOMBAY MARKETING (20 s)
| t | Picture | Type | Motion / camera | Sound |
| --- | --- | --- | --- | --- |
| 0–3 | Black. Giant white type | THE WORLD / HAS GONE / DIGITAL. | Lines fly forward from Z −900 with blur → sharp; Blue Line under DIGITAL | VO1 at 0.2 |
| 3–6 | Type shatters into letter tiles that re-form as a browser Frame, phone and campaign card | — | Tiles scatter (deterministic offsets), cards assemble in 3D, camera push | whoosh |
| 6–11 | Four service vignettes in one Frame: Web build (layout blocks), SEO (search bar + rising line), Ads (ad card + cursor), Branding (logo grid) | WEBSITES · SEO · ADVERTISING · BRANDING | Each 1.25 s, Frame content swaps with vertical slide | VO2, VO3; ticks |
| 11–16 | All four cards orbit inward and converge into one point → blue square | FROM IDEAS TO GROWTH | Cards scale to centre, blue square pulses | Blue Shutter at 15.6 |
| 16–20 | Signature end card | YOUR DIGITAL GROWTH PARTNER. | Logo reveal, push | VO4 "We are…", VO5, sonic logo |

### 02 — YOUR WEBSITE IS YOUR DIGITAL STOREFRONT (20 s)
| 0–3 | White. Browser Frame opens in 3D | YOUR FIRST / IMPRESSION / STARTS HERE. | Frame rotates up from rotateX 60° | VO1 |
| 3–7 | Cluttered, misaligned site (clashing boxes, tiny text) → snaps into a clean layout | BEFORE → AFTER label | Clutter elements shake, then fly to grid positions | glitch → whoosh |
| 7–12 | Same site on desktop, tablet, phone side by side (real client work: Propzed) | RESPONSIVE BY DESIGN | Devices slide in staggered, parallax | VO2, VO3 |
| 12–16 | UI components: enquiry form fills, product card, cart count +1, CTA pressed | — | Cursor clicks; chips pop | clicks, pop |
| 16–20 | End card | DON'T JUST HAVE A WEBSITE. / MAKE IT WORK. | Signature | VO4, sonic logo |

### 03 — BE FOUND. BE CHOSEN. (20 s)
| 0–3 | Search bar Frame on white | WHERE IS YOUR / BUSINESS? | Bar draws, caret blinks | VO1 |
| 3–7 | Query types "digital agency in mumbai"; generic result rows cascade | — | Rows stagger; one blank slot pulses ("Your business?") | key ticks, VO2 |
| 7–12 | Four SEO pillars as stacked cards | KEYWORD RESEARCH · TECHNICAL SEO · ON-PAGE OPTIMISATION · CONTENT ARCHITECTURE | Cards deal in, each ticks a check | VO3 |
| 12–16 | Abstract visibility dashboard: Blue Line climbs, bars grow — **no numbers, no ranks, no axis values** | VISIBILITY ↑ | Line draws, area fills | VO4 |
| 16–20 | End card | BE FOUND. / BE CHOSEN. | Signature | VO5, sonic logo |

### 04 — OWN YOUR LOCAL PRESENCE (18 s)
| 0–3 | Abstract Mumbai map (coastline silhouette + street grid, own drawing) | YOUR NEXT CUSTOMER / COULD BE NEARBY. | Camera drifts over map | VO1 |
| 3–7 | Pins drop across the map; one blue pin grows | — | Pins drop with stagger | pops |
| 7–12 | Generic business-profile card (no Google marks): name, category, photo strip (real work), star outline (no rating number), hours, directions | — | Card slides up from pin | VO2, VO3 |
| 12–15 | CALL · WEBSITE · DIRECTIONS buttons press in sequence | — | Cursor taps | clicks, VO4 |
| 15–18 | End card | BE VISIBLE WHERE / IT MATTERS. | Signature | sonic logo |

### 05 — ATTENTION IS THE NEW CURRENCY (20 s)
| 0–3 | Phone feed scrolling fast (grey placeholder posts) | ANOTHER POST. / OR A BRAND PEOPLE / REMEMBER? | Motion-blurred scroll, hard stop | whoosh, VO1 |
| 3–7 | Random posts → consistent 3×3 branded grid | — | Tiles flip to brand grid | VO2 |
| 7–12 | Reel cover, carousel, campaign graphic (real packaging work) | REELS · CAROUSELS · CAMPAIGNS | Cards fan | VO3 |
| 12–16 | Repeated elements (Blue Line, type, frame) lock into a unified system | ONE IDENTITY | Grid snap | — |
| 16–20 | End card | MAKE EVERY / IMPRESSION COUNT. | Signature | VO4, sonic logo |

### 06 — TURN CLICKS INTO CUSTOMERS (20 s)
| 0–3 | Cursor clicks an ad card | CLICKS ARE JUST / THE BEGINNING. | Click ripple | click, VO1 |
| 3–7 | Ad creative on phone (real work) | — | Phone rises | VO2 |
| 7–12 | Journey: AD → LANDING PAGE → ENQUIRY → FOLLOW-UP | 4 nodes on the Blue Line | Line travels node to node | ticks, VO3 |
| 12–16 | Conversion funnel (4 tiers, no numbers) | — | Tiers stack, dot falls through | VO3 tail, VO4 |
| 16–20 | End card | TURN ATTENTION INTO / OPPORTUNITY. | Signature | sonic logo |

### 07 — MAKE YOUR BRAND UNFORGETTABLE (20 s)
| 0–3 | Blank white canvas | WHAT MAKES A BRAND / MEMORABLE? | Type sets, cursor | VO1 |
| 3–7 | Type specimen, geometric shapes, swatches assemble | Aa · ● ■ · swatches | Shapes snap to grid | VO2 |
| 7–12 | Real packaging work (Evermore, MYSA, Darjeeling, Naked Life) + stationery mock | — | Parallax gallery | VO3 |
| 12–16 | Applications align into one system board | — | Board zooms out | — |
| 16–20 | End card | MAKE YOUR BRAND / UNFORGETTABLE. | Signature | VO4, sonic logo |

### 08 — IDEAS INTO IMPACT (22 s)
| 0–3 | Type | EVERY BUSINESS / HAS A STORY. | Kinetic | VO1 |
| 3–8 | Real client websites (Biovixa, Dastaan by Artisans, Glowleaf, Posh Laptop) | Project names | Card swap stack | VO2 |
| 8–13 | Industry chapters — E-COMMERCE · MANUFACTURING · CONSUMER BRANDS · PROFESSIONAL SERVICES — each with real project | — | Chapter wipe on Blue Line | VO3 |
| 13–18 | Portfolio wall of approved work with names | — | Wall pans, camera pull-back | VO4 |
| 18–22 | End card | IDEAS INTO / IMPACT. | Signature | VO5, sonic logo |

### 09 — LET'S BUILD SOMETHING BIGGER (20 s)
| 0–3 | Dark | YOUR BUSINESS HAS / BIGGER PLANS. | Type pushes forward | VO1 |
| 3–7 | Web, SEO, branding, ads modules appear | — | Modules pop around centre | VO2 |
| 7–12 | Modules connect by Blue Lines into an ecosystem | — | Network draws, orbit | VO3 |
| 12–16 | Cinematic logo reveal | — | Push-in, logo | VO4, sonic logo |
| 16–20 | CTA | LET'S GROW YOUR BUSINESS. / GET IN TOUCH / bombaymarketing.in / DM US TO GET STARTED · @bombaymarketing.in | Buttons pulse | VO5, sonic logo tail |

---

## 3. Integrity rules applied
- No testimonials, statistics, rankings, ratings or results are shown anywhere. Dashboards are unlabelled shapes.
- Client work shown is the agency's own published portfolio from bombaymarketing.in; project names are taken from the published file names.
- No Google, Meta or Instagram logos or trade dress; interfaces are generic and drawn in the campaign palette.
