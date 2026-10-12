=== Hadhrami Delivery ===
Requires at least: 6.2
Requires PHP: 7.4
Stable tag: 1.3.0

Own-rider delivery for Hadhrami Fashion on WooCommerce. Built on the One Love Delivery
system (1.2) — same rider app, dispatch board, live tracking, OTP, COD cash and WhatsApp —
with Hadhrami's two promises: shirts in 2 hours, suits tailored in 24 hours.

== What you get ==

* Two delivery promises, chosen by the customer at checkout:
  - "Within 2 hours" — only when every piece in the bag is ticked "2-hour delivery stock"
    (Product → General), the customer pins a spot inside the express radius, and the
    atelier is open. Orders after the cut-off are due 2 hours after the next opening.
    An express surcharge is added on top of the distance fee.
  - "Tailored within 24 hours" — everything else inside the delivery area.
  The due time ("today by 4:30 pm") is shown at checkout, in emails, in the rider app,
  on the dispatch board and on the tracking page.
* "In the atelier" stage: tailored orders wait on the dispatch board until you press
  "Ready — send rider". Express orders go straight to the riders.
* Deliveries → Dispatch: live map of riders and open orders sorted by due time, late
  orders flagged, assign / re-assign, mark delivered, cash each rider holds.
* Rider app at /rider/ — installable on the phone. On/Off duty, new-order ring, Navigate
  (Google Maps), Call / WhatsApp customer, OTP check, COD amount, photo proof, report a
  problem, earnings today. Each card shows "2-HOUR · Due today by 4:30 pm".
* Customer tracking at /track/<code>/ — live rider on the map, due time, steps (including
  "Tailored in our atelier"), delivery OTP, call rider. Linked from emails and My Account.
* Checkout map pin, distance-based fee, free delivery above a bag value.
* Auto-assign to the nearest free on-duty rider, soonest promise first; retries every
  2 minutes and (optionally) WhatsApps you when nobody is free.
* Rider pay & reports: on-time % measured against each order's own promise.
* Hadhrami HQ: app-style admin home in the house colours (can be switched off).
* Public endpoint GET /wp-json/hadhrami-delivery/v1/quote?lat=..&lng=.. — used by the
  Hadhrami theme's "Is your door within the two-hour circle?" map.

== Setup ==

1. Install and activate WooCommerce, then this plugin.
2. Settings → Permalinks → Save Changes once (refreshes /rider/ and /track/).
3. Deliveries → Settings: Google Maps key, atelier latitude/longitude, delivery radii,
   promise hours, fees.
4. Products → edit each ready-to-wear shirt → tick "2-hour delivery stock".
5. Users → Add New → role "Delivery Rider", fill "Rider mobile / WhatsApp".
6. Riders open https://YOUR-SITE/rider/, log in, allow location, Add to Home screen,
   and go On duty.
7. WooCommerce → Settings → Payments → enable Cash on delivery.
8. The checkout page must be the classic [woocommerce_checkout] shortcode (the
   Hadhrami theme sets this up when it is activated).

== Google Maps API key ==

Enable Maps JavaScript API and Places API (New); restrict the key to your website.
Navigation opens the rider's Google Maps app and does not use your key.

== WhatsApp templates (Meta WhatsApp Cloud API) ==

Create in WhatsApp Manager → Message templates (Utility, English). Names must match
Deliveries → Settings. Variables are filled in this order.

1. hfd_rider_new_order (to rider)
   New delivery! Order #{{1}} to {{2}}. Payment: {{3}}. Open your rider app: {{4}}
2. hfd_out_for_delivery (to customer)
   Hello {{1}}, your Hadhrami Fashion order #{{2}} is on its way with {{3}}. Follow it live: {{4}}
   Please share this delivery code with the rider: {{5}}
3. hfd_order_delivered (to customer)
   Hello {{1}}, your Hadhrami Fashion order #{{2}} has been delivered. Wear it well.
4. hfd_no_rider_alert (to you)
   No rider is free for order #{{1}}. Assign one here: {{2}}

== Status flow ==

In the atelier (tailored only) → Waiting for rider → Rider assigned → Out for delivery →
Delivered. Delivery failed / Cancelled possible at any active step. Delivered completes
the WooCommerce order.

== Limits ==

* The rider app sends GPS while it is open on screen. Riders should keep it open on shift.
* Delivery is only offered inside the delivery area; addresses beyond it are refused at
  checkout with a message to call the atelier.
