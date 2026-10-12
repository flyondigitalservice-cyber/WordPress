# Hadhrami Fashion — WordPress theme

A heritage tailoring house site for the House of Saquib Hadhrami: suits made to
measure in **24 hours**, ready-to-wear shirts delivered in **2 hours**, with a
Google Maps–based delivery system and live rider tracking.

## Go-live checklist

1. **Appearance → Themes → activate “Hadhrami Fashion”.** This creates the
   Suits/Shirts collections, 84 products (from the brand Google Drive folder)
   and the pages: Bag, Track, Rider, Heritage, Cloth Library, Two-Hour Delivery.
2. **Hadhrami → Atelier Settings**
   - Paste the **Google Maps API key**. Enable *Maps JavaScript API*,
     *Places API (New)*, *Geocoding API* and *Routes API*, and restrict the key
     to your domain.
   - Set the atelier **latitude/longitude** (defaults to Hyderabad old city),
     the 2-hour and 24-hour radii, fees, opening hours, phone and WhatsApp.
3. **Hadhrami → Setup & Images → Import images** copies every Drive image into
   the Media Library (they load straight from Drive until then; the Drive files
   must stay shared “anyone with the link”).
4. Edit product names, prices and copy under **Products**. Swap home-page
   imagery and headline under **Appearance → Customize**.

## How delivery works

| Promise | Who qualifies | Where |
|---|---|---|
| Within 2 hours | Bag contains only ready-to-wear shirts marked “In stock for 2-hour express” | Inside the express radius, before the cut-off hour |
| Tailored in 24 hours | Anything made to measure (all suits) | Inside the tailoring radius |
| Standard 3–5 days | Everything | Anywhere |

- Checkout: the customer searches their address or drops a pin; the browser
  measures the driving route with Google, the server re-checks it against the
  straight-line distance, re-prices the bag and offers only valid promises.
- Orders land in **Hadhrami → Orders**. Each order has a **rider link** — send
  it to the rider’s phone; opening it shares live GPS with the customer’s
  tracking page until the rider taps *Mark as delivered*.
- Payment is pay-on-delivery/fitting; no gateway is wired in yet.
