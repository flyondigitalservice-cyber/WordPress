# Zobo D2C theme

A custom WordPress theme for Zobo, the launch partner for D2C founders. It covers the whole journey: idea, logo, trademark, licences, manufacturing, lab testing and certification, vendor registration, marketplaces, website, performance marketing, influencer marketing and OTT/TVC ads.

The theme folder is `zobo-d2c`. Your current live theme uses the folder `zobo`, so uploading this one installs it alongside and never overwrites the live theme.

## Setup

1. Upload `zobo-d2c` to `wp-content/themes/` (or zip it and use *Appearance → Themes → Add New → Upload*). Use *Live Preview* to check it before activating **Zobo D2C**.
2. *Appearance → Customize → Zobo Settings*:
   - **Hero**: eyebrow, headline and intro text.
   - **Contact**: email (the enquiry form sends here), phone, WhatsApp number, address, working hours, and an optional booking link (Calendly etc.) used by every "Book a call" button. The WhatsApp number should be digits with the country code, e.g. `919876543210`. Setting it also turns on the floating WhatsApp button.
   - **Social links**: Behance, Instagram, LinkedIn, YouTube.
3. *Appearance → Customize → Site Identity*: upload your logo. Without one, the site name is shown as a wordmark.
4. *Appearance → Menus*: optionally assign **Primary** and **Footer** menus. With no primary menu, the header links to the front-page sections.

## Pages from the previous theme

The Services, Industries, Process, Pricing, Work and Contact pages were drawn entirely by the old theme's templates and have no content of their own. With this theme active, those URLs redirect (302) to the matching section of the homepage. If you add content to one of those pages in the editor, it is shown as a normal page instead.

## Case studies

The theme adds a **Case Studies** post type, listed at `/case-studies/`. Add a title, excerpt and featured image for each project and the six latest appear in the Work section on the homepage. With none published, that section shows the client brands from `zobo_clients()` in `inc/content.php`.

## Editing copy

Journey stages, services, industries, client brands, plans, audiences and FAQs live in `inc/content.php`. All strings are translatable (text domain `zobo-d2c`).

## Enquiry form

The contact form posts to `admin-post.php`, checks a nonce and a honeypot, and sends the enquiry with `wp_mail()`. It includes the founder's product category and stage. Many hosts need an SMTP plugin (for example WP Mail SMTP) for mail to arrive reliably.
