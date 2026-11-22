# Bombay Marketing — Funky Showreel (60 s)

Original music: a quirky funk track at 105 BPM, composed in code by `_shared/music/compose_funk.py`. It uses synth slap bass, swung drums, wobbly detuned plucks and chord stabs, with a stop-time break at 32 s and an impact under the logo at 54.9 s. Every animation hit is placed on its beat grid (1 beat = 0.571 s).

| Time | Section | Motion |
|---|---|---|
| 0–4.6 | "WE MAKE / BOXES / DANCE." | words drop with squash and stretch, wiggle on each beat, star sticker |
| 4.6–13.7 | Collage | 16 products pop in, one per beat (back-out plus squash), camera bumps every bar, burst apart on the last beat |
| 13.7–32.0 | Spotlight | 16 hero products, a circle-mask reveal every 2 beats, drop and squash landing, wobble on the off-beat, colour flips (blue / white / black / yellow), category words |
| 32.0–36.6 | Word break | BOXES · LABELS · BOTTLES · POUCHES · TUBS · TINS · BRANDS · WOW, one per beat on the music's stop-time hits |
| 36.6–45.7 | Conveyor | three tilted product rows sliding opposite ways; "TEN YEARS OF BOXES." band bounces on the beats |
| 45.7–50.3 | Shakti orbit | the Shakti identity pieces orbit the Shakti logo |
| 50.3–54.9 | Breakdown | a morphing blob behind "EVERY PROJECT, OUR OWN DESIGN." |
| 54.9–60.0 | Outro | the logo lands on the impact with an elastic bounce; taxi-checker strip; "LET'S MAKE SOMETHING FUNKY." |

Colours: the campaign blue, black, white and grey, plus the brand's own taxi yellow #FFC72C from the logo and website, used here for energy.
Rebuild: `python3 _shared/music/compose_funk.py` (from `_shared/music/`), then `python3 _shared/gen_funk.py`, then render `fx/`.
To use a licensed track instead, replace `fx/assets/music/funk_bed.wav` with a 105 BPM track, or re-time to a new BPM by changing `B` in `gen_funk.py`.
