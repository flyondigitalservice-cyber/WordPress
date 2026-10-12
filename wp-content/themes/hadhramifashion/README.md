# Hadhrami Fashion — WordPress theme

A heritage tailoring house site for the House of Saquib Hadhrami: suits made to
measure in **24 hours**, ready-to-wear shirts delivered in **2 hours**.

The shop runs on **WooCommerce**. Delivery runs on the **Hadhrami Delivery** plugin
(`wp-content/plugins/hadhrami-delivery`) — the One Love Delivery system adapted
for Hadhrami: rider app, dispatch board, Google Maps checkout pin, live tracking,
OTP, cash on delivery, WhatsApp and reports, plus the 2-hour / 24-hour promises.

## Go-live checklist

1. Install and activate **WooCommerce**, then **Hadhrami Delivery**, then this theme.
   Activation creates the Suits/Shirts categories, occasion tags, 84 products from the
   brand Google Drive folder, the Heritage / Cloth Library / Delivery pages, and switches
   the cart and checkout to the classic WooCommerce pages the delivery plugin needs.
   (Already active? Appearance → Hadhrami Setup → **Run setup**.)
2. **Deliveries → Settings** — Google Maps key, atelier latitude/longitude, delivery
   radii, 2-hour and 24-hour promise hours, fees, WhatsApp.
3. **Users → Add New** — role *Delivery Rider* for each rider; they use `/rider/`.
4. **Appearance → Hadhrami House** — phone, WhatsApp, patron, established year.
5. **Appearance → Hadhrami Setup → Import images** copies the Drive images into the
   Media Library (until then they load from Drive; keep the folder shared).
6. If WooCommerce shows a "Coming soon" page, launch the store under
   WooCommerce → Settings → Site visibility.

## How a delivery flows

| Step | Ready-to-wear shirt (2 hours) | Made to measure (24 hours) |
|---|---|---|
| Checkout | Pin the door on the map; "Within 2 hours" offered when the whole bag is 2-hour stock and the pin is inside the express radius | "Tailored within 24 hours"; measurements or a tailor visit are saved on the order |
| After order | Goes straight to the rider queue and is auto-assigned | Waits **In the atelier** on Deliveries → Dispatch |
| Atelier | — | Press **Ready — send rider** when the piece is finished |
| Rider | `/rider/` app: navigate, call/WhatsApp, OTP, cash, photo proof | same |
| Customer | `/track/<code>/` live map, due time, OTP (linked in emails and My Account) | same, with a "Tailored in our atelier" step |

Tick **2-hour delivery stock** on any product (Product data → General) to make it
eligible for the 2-hour promise.
