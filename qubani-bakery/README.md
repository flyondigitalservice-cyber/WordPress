# Qubani Atelier: Royal Qubani landing page

A single-page, responsive landing page for a Deccan fusion bakery. Its signature product is a layer cake built on Qubani ka Meetha (stewed apricots), sponge and fresh cream.

- `QubaniLanding.jsx`: the whole page as one React component. It uses Tailwind CSS (arbitrary values only, so no Tailwind config is needed) and `lucide-react` icons.
- `images/`: the product photos the page uses.
- `dist/index.html`: a prebuilt, self-contained page (inlined JS and CSS) that loads `dist/images/`. Open it directly in a browser.

## Customise
Edit the constants at the top of `QubaniLanding.jsx`:
- `BRAND`, `WHATSAPP_NUMBER` (digits only, with country code), `PHONE_DISPLAY`
- `PRODUCTS` (prices, sizes), `REVIEWS`, `FAQS`, and the address in `Footer`

## Use in a React + Tailwind project
```bash
npm i lucide-react
```
Copy `QubaniLanding.jsx` and the `images/` folder into `src/`, then render `<QubaniLanding />`.
Make sure Tailwind's `content` glob includes the file.
