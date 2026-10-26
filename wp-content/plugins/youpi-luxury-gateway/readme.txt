=== YouPI Luxury Payment Gateway ===
Contributors: flyondigitalservice
Tags: woocommerce, upi, payment gateway, india, luxury
Requires at least: 6.2
Tested up to: 6.7
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later

Direct-to-bank UPI payments for luxury watch boutiques on WooCommerce, powered by a self-hosted YouPI PG gateway.

== Description ==

Connects WooCommerce to a self-hosted [YouPI PG](https://github.com/youpi-pg/YouPI-PG) instance. Clients pay with any UPI app (Google Pay, PhonePe, Paytm, CRED, BHIM) on YouPI's hosted checkout; funds settle straight into the boutique's bank account.

Built for high-value timepieces:

* **Reconciliation hold** – orders at or above a configurable value go On hold when payment is observed, and are released automatically only when YouPI reports the payment RECONCILED (or when staff confirm the credit and set the order to Processing).
* **Exact amount check** – every reported payment must match the order total to the paisa, otherwise the order is held for manual reconciliation.
* **UPI ceiling** – UPI is hidden above a set order value (default ₹1,00,000, the usual bank UPI limit).
* **Discreet billing** – product names and brands are not sent to the gateway by default.
* **Concierge** – phone/WhatsApp and email shown at checkout, on the confirmation page and in error messages.
* **Signed webhooks** – HMAC-SHA256 (V2 timestamp-bound or V1) verification, freshness window, delivery-id deduplication.
* **Missed-webhook safety net** – status is re-checked when the client returns, and every 10 minutes by WP-Cron for recent orders.
* Classic and block checkout, HPOS compatible.

== Installation ==

1. Run a YouPI PG instance and note the Payment API URL, Merchant ID, Merchant API key and webhook secret from the Merchant Admin panel.
2. Upload `youpi-luxury-gateway` to `wp-content/plugins/` and activate it (WooCommerce must be active, store currency INR).
3. WooCommerce → Settings → Payments → **YouPI UPI — Luxury**: enter the credentials and enable the method.
4. Copy the webhook URL shown in the settings (`https://your-site/wp-json/youpi-luxury/v1/webhook`) into the merchant's **Webhook URL** in YouPI Merchant Admin.
5. Place a low-value test order end to end before going live.

== Order flow ==

1. Client chooses UPI → plugin creates a YouPI payment session (`POST /api/v1/payments`) and redirects to its `checkoutUrl`.
2. Client verifies by WhatsApp OTP and pays by QR / UPI app.
3. YouPI sends a signed webhook; the plugin marks the order Processing, or On hold above the reconciliation threshold.
4. FAILED / EXPIRED / CANCELLED attempts mark the order Failed; the client can retry from "Pay for order".

== Important ==

YouPI PG describes itself as an experimental, self-hosted research project that is not bank- or NPCI-approved. Bank alert parsing can be delayed or wrong, and a webhook does not prove settlement. For luxury goods, always confirm the credit in your bank statement before releasing a timepiece, and take legal/compliance advice before accepting real payments. Refunds are not supported through the gateway; issue them by bank transfer and record them manually in WooCommerce.

== Changelog ==

= 1.0.0 =
* Initial release.
