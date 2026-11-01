=== Invoice & Packing Slip Maker for WooCommerce ===
Contributors: flyondigitalservice
Tags: woocommerce, invoice, pdf invoice, packing slip, shipping label
Requires at least: 6.2
Tested up to: 6.7
Requires PHP: 7.4
Stable tag: 1.2.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Create, email, print and export invoices for WooCommerce orders, and print packing slips and shipping labels for your parcels.

== Description ==

**Invoices**

* Sequential invoice numbers with prefix/suffix, zero padding, `{Y}`/`{m}` placeholders and optional yearly reset. Numbers are taken atomically, so two orders can never get the same number.
* Email the invoice to the customer as a PDF attachment — from the order screen, as a bulk action, or automatically when an order reaches a status you choose.
* Optionally attach the invoice PDF to WooCommerce's own emails (Processing, Completed, Customer invoice…).
* Print the invoice from the browser, or download it as a PDF.
* Export invoices to CSV (for accounting / spreadsheets).
* Customers can download their invoice from My Account and from the order confirmation page.

**Packing slips & shipping labels**

* Packing slip with a large "Ship to" block, tick boxes for each item, quantities, SKUs, variation details, weights and the customer's note — put it inside the box.
* Shipping label with sender, recipient, phone, order number and a scannable Code 128 barcode of the order number — stick it on the parcel. Same size as the invoice by default, or 4×6 in / 100×150 mm / A6 for label printers.

**Everything else**

* Your logo, shop address, tax/VAT/GST number, accent colour, notes and footer.
* A5 by default (compact layout with small fonts), or A4 / US Letter. Invoices, packing slips and shipping labels share the same size unless you choose a dedicated label size.
* Bulk print / download for many orders at once.
* Compatible with WooCommerce High-Performance Order Storage (HPOS).
* Safe for your store: creates no database tables (uses one settings option and order meta), loads nothing on your storefront except the customer's invoice button, and never prints database errors on your site.
* No external services and no extra libraries required: PDFs are produced by a built-in generator. If the Dompdf library is already present on your site it is used automatically for full Unicode font support.

== Installation ==

1. Upload the `invoice-packing-slip-maker` folder to `/wp-content/plugins/` and activate it (WooCommerce must be active).
2. Go to **WooCommerce → Invoices & Slips** and enter your shop details and logo.
3. Optional: in **WooCommerce → Settings → Emails → Invoice (PDF)** edit the subject, heading and text of the invoice email.

== Usage ==

* **Single order:** open an order — the *Invoice & Packing Slip* box on the right has Print and PDF buttons for the invoice, packing slip and shipping label, and an *Email invoice to customer* button.
* **Many orders:** on the Orders list tick the orders, then choose from *Bulk actions*: Print invoices, Download invoices (PDF), Email invoices to customers, Export invoices (CSV), Print packing slips, Download packing slips (PDF), Print shipping labels, Download shipping labels (PDF).
* **Quick buttons:** the Actions column on the Orders list has an *Invoice PDF* and a *Print packing slip* button.

== Customising ==

Copy any file from the plugin's `templates/` folder into `yourtheme/invoice-packing-slip-maker/` to change the printed layout (invoice.php, packing-slip.php, shipping-label.php, document.php).

Filters: `ipsm_invoice_number`, `ipsm_barcode_value`, `ipsm_pdf_scale`, `ipsm_filename`, `ipsm_document_item`, `ipsm_document_totals`, `ipsm_attach_invoice`, `ipsm_attachable_emails`, `ipsm_customer_can_view_invoice`, `ipsm_csv_headers`, `ipsm_csv_row`, `ipsm_use_dompdf`, `ipsm_pdf_text_replacements`, `ipsm_template_path`.
Actions: `ipsm_before_items`, `ipsm_after_totals`.

== Frequently Asked Questions ==

= The ₹ (or another) currency symbol shows as "Rs." in the PDF =

The built-in PDF generator uses the standard PDF fonts, which cover Western European characters only. Symbols outside that set are replaced with a readable code (₹ → "Rs.", ₽ → "RUB" …; change via the `ipsm_pdf_text_replacements` filter). The printed (browser) version always shows the real symbol. For full Unicode in PDFs install Dompdf (e.g. `composer require dompdf/dompdf`) and it will be used automatically.

= What happens to invoice numbers if I uninstall? =

Settings are removed, but the invoice number and date saved on each order are kept, since issued invoices are accounting records.

== Changelog ==

= 1.2.0 =
* Redesigned settings page: tabs, toggle switches, visual paper/label size pickers, colour swatches, live "next invoice number" example, getting-started checklist, unsaved-changes warning and a live preview of the invoice, packing slip and label (previews never use up an invoice number).
* Redesigned order box: clear invoice status, one-click Print/PDF for each document, and "Email invoice" now sends without reloading the page (unsaved order edits are kept).
* New "Invoice" column on the orders list showing the invoice number and whether it was emailed.
* Bulk print / download now open in a new tab so you stay on the orders list.
* Fix: invoice numbering no longer uses MySQL-only SQL, so it can't cause a database error on hosts using SQLite or other database drop-ins; database errors are never printed on the page and are logged instead.
* Fix: on the very first settings save, switched-off options could be saved as switched on.
* Fix: an error while creating an invoice can no longer interrupt checkout, order updates or WooCommerce emails; the email still goes out and the problem is logged (WooCommerce → Status → Logs).
* Fix: PDF and CSV downloads can no longer be corrupted by stray output from other plugins or the theme.
* Fix: possible PHP warnings while cleaning up temporary files.

= 1.1.0 =
* Code 128 barcode of the order number on shipping labels.
* A5 paper size (new default) with a compact, small-font layout; shipping labels match the invoice size by default.
* Totals block no longer splits across pages.

= 1.0.0 =
* First release.
