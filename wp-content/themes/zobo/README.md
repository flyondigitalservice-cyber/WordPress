# Zobo D2C theme

A custom WordPress theme for Zobo, the launch partner for D2C founders, from idea to logo, trademark, licences, manufacturing, vendor registration, marketplaces, website, performance marketing, influencer marketing and OTT/TVC ads.

## Setup

1. Copy `wp-content/themes/zobo` to your site and activate **Zobo D2C** under *Appearance → Themes*.
2. *Settings → Reading*: set "Your homepage displays" to **A static page** and pick any page as the homepage. The theme's `front-page.php` renders the full landing page.
3. *Appearance → Customize → Zobo Settings*:
   - **Hero**: eyebrow, headline and intro text.
   - **Contact**: email (enquiry form sends here), phone, WhatsApp number (digits with country code, e.g. `919876543210`; this turns on the floating WhatsApp button), address, and an optional booking link (Calendly etc.) used by every "Book a call" button.
   - **Social links**: Behance, Instagram, LinkedIn, YouTube.
4. *Appearance → Customize → Site Identity*: upload your logo. Without one, the site name is shown as a wordmark.
5. *Appearance → Menus*: optionally assign **Primary** and **Footer** menus. With no primary menu, the header links to the front-page sections.

## Case studies

The theme adds a **Case Studies** post type (at `/work/`). Add a title, excerpt and featured image for each project and the six latest appear in the Work section on the homepage. With none published, that section links to Behance instead.

## Editing copy

Journey stages, services, plans, audiences and FAQs live in `inc/content.php`. All strings are translatable.

## Enquiry form

The contact form posts to `admin-post.php`, checks a nonce and a honeypot, and sends the enquiry with `wp_mail()`. Many hosts need an SMTP plugin (for example WP Mail SMTP) for mail to arrive reliably.
