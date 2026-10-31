# Apricot Dessert: Deccan dessert house website

A responsive multi-page website for a Deccan dessert bakery, built as one React component with hash-based pages:

- `#home`: hero, the three categories, a signature spotlight, story, reviews, bulk orders teaser, FAQ
- `#qubani`: classic Qubani ka Meetha and premium Apricot Delight, a comparison table, and the interactive layer breakdown
- `#brownies`: walnut fudge and cream cheese swirl brownies
- `#halwa`: badam halwa, kaju halwa and winter-special gajar ka halwa
- `#bulk-orders`: party, marriage and hotel orders, both locations, FAQ

Every product has a size picker and quantity picker that pre-fill a WhatsApp order message.

- `QubaniLanding.jsx`: the whole page as one React component. It uses Tailwind CSS (arbitrary values only, so no Tailwind config is needed) and `lucide-react` icons.
- `images/`: the product photos the page uses.
- `dist/index.html`: a prebuilt, self-contained page (inlined JS and CSS) that loads `dist/images/`. Open it directly in a browser.

## Customise
Edit the constants at the top of `QubaniLanding.jsx`:
- `BRAND`, `WHATSAPP_NUMBER` (digits only, with country code), `PHONE_DISPLAY`, `LOCATIONS`
- `CATEGORIES` (products, sizes, prices, photos), `REVIEWS`, `FAQS`, `EVENT_TYPES` (party, marriage and hotel orders)

## Use in a React + Tailwind project
```bash
npm i lucide-react
```
Copy `QubaniLanding.jsx` and the `images/` folder into `src/`, then render `<QubaniLanding />`.
Make sure Tailwind's `content` glob includes the file.
