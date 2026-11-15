=== Boutique Desk ===
Requires Plugins: woocommerce
Stable tag: 1.3.0

Store Desk for WooCommerce: a step-by-step order desk, a limited staff role, category sellers, and invoices and packing slips (GST or VAT).

== What it does ==
* Store Desk (1.2): every open order moves through To confirm, To pack, Hand to courier and In transit, and is completed only when it is delivered. Each step is one click, with Undo.
* Confirm by phone or WhatsApp: tap-to-call and a ready-written WhatsApp message per order, "No answer" attempts counted, cancel before packing.
* Courier and tracking number saved on the order, emailed to the customer, and shown on their order page.
* Cash on delivery: total cash to collect and how much is with couriers; "Came back" cancels an undelivered COD order and returns the stock.
* Warnings: late orders, and the same customer ordering twice.
* Serial number recorded at packing and printed on the invoice and slip.
* A "Step" column in the WooCommerce orders list.
* Product list (Products -> All Products): specs (condition, CPU, RAM, storage, screen) read from the title, deal %, units sold, and a Check column that flags missing photo, short description or price. One-click "Mark sold out" / "Back in stock", views for On sale, Sold out and Needs attention, a condition filter, and bulk actions (sold out, back in stock, remove sale price, hide from shop). Empty SKU, Tags and Brands columns are hidden. Can be switched off in Store Desk settings.
* Automatic WhatsApp (1.3): order received (customer replies YES), order shipped (courier and tracking) and order delivered, sent through Meta's WhatsApp Cloud API with approved templates, in English or Arabic by country. Queued in the background with retries; a webhook records replies on the order and can confirm it when the customer says YES. Failures are written on the order and shown on the desk. Set up under WooCommerce -> Store Desk settings.
* Category Sellers (1.1): outside sellers list products only in the categories you assign, see only their own products, images and order lines, and get a Seller Desk with revenue, earnings after commission, a 30-day chart, best sellers and sold-out items.
* Store Editor role: add and edit products, process orders, print documents. No deleting, no settings, no plugins, themes, users, coupons or analytics.
* Invoice: sequential numbers, tax number (GSTIN, TRN...), optional HSN per product, amount in words in the order currency (Indian numbering for INR).
* Packing slip: product photos, packing checkboxes, big "Cash on delivery: collect X" box, return address, your own checklist line.
* Bulk print from the Orders list; customers can download invoices from My account.
* Print or save as PDF from the browser; no PDF library needed.

== Setup ==
1. Upload and activate.
2. WooCommerce → Store Desk settings: add store address, tax number, logo, invoice prefix, couriers and WhatsApp messages.
3. Users → Add New → role "Store Editor" for staff.
4. For outside sellers: Users → Add New → role "Category Seller", then on their profile tick their categories and set the commission %.

== Changelog ==
= 1.3.0 =
* Automatic WhatsApp messages through the Meta WhatsApp Cloud API, with reply webhook, retries and a test button.
* Messages show amounts as AED 999.00 and use the short product name.

= 1.2.0 =
* Order steps on the Store Desk (confirm, pack, hand to courier, in transit, delivered / came back) replace the single "Mark shipped" button.
* WhatsApp and call links, courier and tracking, cash-to-collect totals, late and duplicate-order warnings, serial numbers.
* Works outside India: VAT / TRN wording, amount in words for AED, SAR and other currencies, HSN column only when used.
* Product list upgrades (see above).
* Fixed: packing slips showed "Prepaid" for cash on delivery orders that were still processing.
* Fixed: internal order data (such as _reduced_stock) printed on documents.
