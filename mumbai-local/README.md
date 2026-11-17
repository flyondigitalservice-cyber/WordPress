# Lokal: Mumbai local train tracker (PWA prototype)

A mobile-first web app for Mumbai suburban trains, in the spirit of M-Indicator.
It is plain HTML, CSS and JavaScript with no build step, and it installs to the
phone home screen and works offline.

## Features

- **Trains**: next trains between any two stations, with fast/slow, 12/15-car, AC,
  arrival time and countdown. Routes that need a change of train (for example
  Virar to Panvel) are planned automatically with at least 3 minutes to change.
- **Saved routes** and recent stations, kept on the device.
- **Train detail**: every stop with times and an estimated live position of the train.
- **Station**: departure board for any station across all lines.
- **Lines**: Western, Central (Kasara and Khopoli), Harbour (Panvel and Goregaon)
  and Trans-Harbour line maps, with fast stops marked.
- **More**: 12/24-hour clock, helpline numbers, install instructions.

## Run it

Serve the folder over HTTP (service workers need http/https):

```bash
cd mumbai-local && python3 -m http.server 8080
```

Then open http://localhost:8080. On the WordPress site it is served as static
files at `/mumbai-local/`.

## Files

| File | What it holds |
| --- | --- |
| `data.js` | Stations, distances, fast-stop lists, service patterns, helplines |
| `app.js` | Timetable generation, journey search, live position, UI |
| `styles.css` | Styles, light and dark theme |
| `sw.js` | Offline cache |
| `manifest.webmanifest`, `icon*.png`, `icon.svg` | Install metadata |

## Important: the timetable is generated

There is no free public API for live Mumbai local positions. This prototype
generates a realistic timetable from `PATTERNS` in `data.js`, and "live"
positions are estimated from that timetable. Before real use:

1. Replace generated trips with the official WR/CR timetable (load it as JSON
   into the same `TRIPS` shape: `{ num, line, fast, ac, cars, stops: [{ s, t }] }`).
2. For real-time running, add a backend (for example crowd-sourced GPS from
   riders, the approach M-Indicator-style apps use) and overlay delays on
   `tripState()`.
3. Add platform numbers per station and train if the data source has them.
