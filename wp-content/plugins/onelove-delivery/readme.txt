=== One Love Delivery ===
Requires at least: 6.2
Requires PHP: 7.4
Stable tag: 1.0.0

Last-mile delivery for WooCommerce with your own riders: auto-assignment, rider mobile app
with Google Maps navigation, live customer tracking, delivery OTP, COD cash tracking and
WhatsApp updates.

== What you get ==

* Deliveries → Dispatch (wp-admin): live map of riders and open orders, assign / re-assign,
  mark delivered, cash each rider is holding, "Cash received" button.
* Rider app at /rider/ — installable on the phone (Add to Home screen). On/Off duty,
  new-order ring + vibration, Navigate (opens Google Maps turn-by-turn, two-wheeler mode),
  Call / WhatsApp customer, OTP check, COD amount, photo proof, report a problem.
* Customer tracking page at /track/<code>/ — live rider on the map, ETA, status timeline,
  delivery OTP, call rider. Link is added to order emails, the thank-you page and My Account.
* Checkout: Mumbai-only PIN code check (400xxx / 401xxx by default) and an optional
  "pin your exact delivery spot" map.
* Auto-assign: every Processing order (paid online or COD) goes to the on-duty rider closest
  to the store with the fewest active orders. If nobody is free it retries every 2 minutes
  and (optionally) WhatsApps you.

== Setup (10 minutes) ==

1. Plugins → Add New → Upload Plugin → choose onelove-delivery.zip → Install → Activate.
2. Settings → Permalinks → click "Save Changes" once (refreshes the /rider/ and /track/ links).
3. Deliveries → Settings:
   * Paste your Google Maps API key.
   * Check the store latitude / longitude (right-click your store in Google Maps to copy them).
4. Add riders: Users → Add New → Role "Delivery Rider". Fill "Rider mobile / WhatsApp".
   Give each rider their username + password.
5. Riders open https://YOUR-SITE/rider/ on their phone, log in, allow location, tap
   "Add to Home screen", then tap "Off duty" to go On duty.
6. Enable Cash on Delivery: WooCommerce → Settings → Payments → Cash on delivery → Enable.
7. LiteSpeed Cache: the plugin already sends no-cache headers for /rider/ and /track/.
   If you use extra rules, also exclude those two paths.

== Google Maps API key ==

Google Cloud Console → APIs & Services:
* Enable: Maps JavaScript API, Places API (New).
* Credentials → Create API key → Application restrictions: Websites →
  add https://oneloveenergy.com/* → API restrictions: the two APIs above.
* A billing account is required by Google; the monthly free usage covers a small fleet.
Navigation itself opens the rider's Google Maps app and does not use your key.

== WhatsApp templates (Meta WhatsApp Cloud API) ==

Create these in WhatsApp Manager → Message templates (category: Utility, language: English).
Names must match Deliveries → Settings. Variables are filled in this exact order.

1. ole_rider_new_order  (to rider)
   New delivery! Order #{{1}} to {{2}}. Payment: {{3}}. Open your rider app: {{4}}

2. ole_out_for_delivery  (to customer)
   Hi {{1}}, your One Love order #{{2}} is on the way with {{3}}. Track it live: {{4}}
   Share this delivery OTP with the rider: {{5}}

3. ole_order_delivered  (to customer)
   Hi {{1}}, your One Love order #{{2}} has been delivered. Enjoy, and thanks for choosing One Love!

4. ole_no_rider_alert  (to you)
   No rider is free for order #{{1}}. Assign one here: {{2}}

Then in Deliveries → Settings paste the permanent access token and the Phone number ID
(WhatsApp Manager → API setup) and tick "Send WhatsApp messages".
Failed sends are logged in WooCommerce → Status → Logs (source: onelove-delivery).

== Status flow ==

Waiting for rider → Rider assigned → Out for delivery → Delivered
(Delivery failed / Cancelled possible at any active step. Delivered completes the WooCommerce order.)

== Limits ==

* The rider app sends GPS while it is open on screen (it keeps the screen awake while on duty).
  Phones pause web apps in the background, so riders should keep it open during a shift.
* Turn-by-turn voice navigation happens in the Google Maps app (Google does not allow it
  inside web pages).
