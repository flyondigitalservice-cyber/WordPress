=== Propzed Publisher & Builders ===
Requires at least: 6.0
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later

Add-on for the Propzed theme: builder brand pages, publisher accounts with admin approval, app API, and a mobile / accessibility / conversion layer. No theme files are changed.

== What it adds ==

1. BUILDER PAGES
   * /builder/{name}/  one page per builder with logo, facts, every project, an enquiry form and WhatsApp / call buttons.
   * /builders/        directory of all builders, with search.
   * Homepage "Top Builders" strip, and a "Developer" card on each property linking to the builder's other projects.
   * A property joins a builder page through the existing "Developer / Builder" field. Pages are created automatically for published properties.
   * Edit logo, tagline, about text, website, year, head office and "Featured" under Properties > Builders.

2. PUBLISHER ACCOUNTS  (/publisher/)
   * Owners, builders and brokers create an account, then add, edit or withdraw their own properties.
   * Every submission and every edit is saved as Pending. It is public only after an administrator clicks Publish.
   * Publishers cannot open wp-admin, the media library or anyone else's listing, and cannot set Featured, Sponsored or SEO fields.
   * Admin: open the pending property, then Publish (publisher is emailed), or tick "Send back for changes" in the Publisher Review box with a note.

3. APP API  (/wp-json/propzed/v1/)
   * GET builders, GET builders/{slug}
   * POST publisher/register, POST publisher/login (returns a token), GET publisher/me,
     GET/POST publisher/properties, POST/DELETE publisher/properties/{id}
   * Details are at the top of includes/rest.php.

4. MOBILE, ACCESSIBILITY, CONVERSION
   * WCAG AA colour contrast, 44px tap targets, 16px form text (no iPhone zoom), visible focus, landmarks, reduced motion.
   * Forms remember the visitor's name and phone, show a clear phone error, and cannot be sent twice.
   * WhatsApp shortcut on every property card.

== Install ==

1. Copy the folder to wp-content/plugins/propzed-publisher/ (the site blocks uploads from wp-admin, so use the hosting File Manager, FTP or your Git deployment).
2. Plugins > activate "Propzed Publisher & Builders". Permalinks refresh by themselves.
3. Properties > Publisher Settings: review the switches.
4. Properties > Builders: add logos and descriptions.
5. Install an SMTP plugin if emails to publishers are not arriving.

== Safety ==

* Loads only when the Propzed theme is active; otherwise it does nothing and shows an admin notice.
* Deactivating it restores the site exactly as before. Publisher accounts, builders and submitted properties stay in the database.
* Properties > Publisher Settings can switch off the front-end layer, the menu links, the homepage strip and new sign-ups one by one.

== Changelog ==

= 1.0.0 =
* First release.
