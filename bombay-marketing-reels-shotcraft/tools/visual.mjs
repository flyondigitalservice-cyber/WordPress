// What each reel SHOWS. What it SAYS (voice + caption) comes from content.src.mjs, the script of the original 12-reel campaign.
// hook.lines: words wrapped in *stars* are set in taxi yellow.   cells: exactly five, in spoken order.
//   page   – the live service page; `card` = index of the real .bm-card the camera lands on
//   chips  – items dealt in as pills        list – ticked checklist        marker – one phrase underlined (`mark`)
//   stat   – big numbers                    note – the honest-note card
export const VISUAL = {
  '06-myntra-seller-onboarding': { eyebrow: 'MYNTRA', page: 'myntra',
    hook: { lines: ['Myntra sabko', 'entry *nahi deta.*'], sub: 'Application strong honi chahiye.' },
    cells: [
      { kind: 'page', card: 0, h: 'Eligibility review', s: 'Brand · category · positioning' },
      { kind: 'chips', h: 'Documents, ready', s: 'Organised for a clean application', items: ['GST', 'Trademark', 'Brand details'] },
      { kind: 'list', h: 'Catalog & imagery', items: ['Product data', 'Sizing', 'Photography guidance'] },
      { kind: 'marker', h: ['Margin-safe', 'pricing'], mark: 0, s: 'Discount planning for a commission platform' },
      { kind: 'note', h: 'Honest feedback first', s: 'Acceptance is Myntra’s call. We tell you clearly what to fix first.' },
    ] },
  '07-quick-commerce': { eyebrow: 'QUICK COMMERCE', page: 'quick',
    hook: { lines: ['10 minute mein', 'delivery…'], sub: 'Aapka brand *us bag mein* hai?' },
    cells: [
      { kind: 'page', card: 0, h: 'Brand pitch & application', s: 'Blinkit · Zepto · Instamart' },
      { kind: 'chips', h: 'Catalog & SKU setup', s: 'Product data done right', items: ['Images', 'MRP', 'Units', 'Barcodes'] },
      { kind: 'marker', h: ['Pricing &', 'margin planning'], mark: 1, s: 'For the brand and for the platform' },
      { kind: 'list', h: 'Stock & replenishment', items: ['Stock planning', 'Replenishment planning', 'Product stays available'] },
      { kind: 'note', h: 'Fit check before you buy stock', s: 'Approval is the platform’s decision, so we assess the fit first.' },
    ] },
  '08-ecommerce-seo': { eyebrow: 'ECOMMERCE SEO', page: 'seo',
    hook: { lines: ['Google pe aapka', 'product *doosre', '*page pe?*'], sub: 'Wahan toh koi jaata hi nahi.' },
    cells: [
      { kind: 'page', card: 0, h: 'Buyer-intent keywords', s: 'What people search when they want to buy' },
      { kind: 'chips', h: 'Technical SEO', s: 'The foundations, fixed', items: ['Site speed', 'Indexing', 'Mobile experience'] },
      { kind: 'list', h: 'Product & category pages', items: ['Titles', 'Descriptions', 'Internal links', 'Schema'] },
      { kind: 'marker', h: ['Guides · Blogs', 'Local SEO'], mark: 1, s: 'Google Business Profile for Mumbai' },
      { kind: 'stat', h: 'No instant-ranking promises', stats: [{ big: '2–3', unit: 'months', label: 'early improvements' }, { big: '6–12', unit: 'months', label: 'stronger growth' }] },
    ] },
  '09-social-media-marketing': { eyebrow: 'SOCIAL MEDIA', page: 'social',
    hook: { lines: ['Likes bahut…', 'par orders', '*zero?*'], sub: '' },
    cells: [
      { kind: 'page', card: 0, h: 'Monthly content calendar', s: 'Products · offers · launches · festivals' },
      { kind: 'chips', h: 'Content we make', s: 'Planning, design, copy and video', items: ['Reels', 'Short videos', 'Product shoots', 'Designed posts'] },
      { kind: 'list', h: 'Page management', items: ['Instagram', 'Facebook', 'Community replies'] },
      { kind: 'marker', h: ['Influencers', 'Instagram Shopping'], mark: 1, s: 'Connected straight to your store' },
      { kind: 'note', h: 'Your approval first', s: 'Nothing goes live without it. Measured on clicks and orders, not just likes.' },
    ] },
  '10-meta-google-ads': { eyebrow: 'PERFORMANCE MARKETING', page: 'ads',
    hook: { lines: ['Ads pe paisa', 'ja raha hai…'], sub: 'par *sale nahi* aa rahi?' },
    cells: [
      { kind: 'page', card: 0, h: 'Meta campaigns', s: 'Tested creatives · audiences · retargeting' },
      { kind: 'chips', h: 'Google campaigns', s: 'Catch people already looking to buy', items: ['Search', 'Shopping', 'Performance Max'] },
      { kind: 'marker', h: ['Amazon & Flipkart', 'ads too'], mark: 0, s: 'One team across store and marketplaces' },
      { kind: 'list', h: 'Every rupee tracked to orders', items: ['GA4', 'Pixels', 'Server-side tracking'] },
      { kind: 'note', h: 'Weekly optimisation, monthly review', s: 'If something isn’t working, we say so, and fix it.' },
    ] },
  '11-packaging-design-branding': { eyebrow: 'PACKAGING & BRANDING', page: 'packaging',
    hook: { lines: ['Product achha hai…', 'par shelf pe', '*dikhta hi nahi?*'], sub: '' },
    cells: [
      { kind: 'page', card: 0, h: 'Product packaging', s: 'Boxes · pouches · jars · bottles · cans · gift packs' },
      { kind: 'chips', h: 'Labels + brand identity', s: 'One look across the range', items: ['Logo', 'Colours', 'Typography'] },
      { kind: 'marker', h: ['3D', 'mockups'], mark: 0, s: 'Marketplace-ready visuals for Amazon · Blinkit · Zepto' },
      { kind: 'list', h: 'Print-ready artwork', items: ['Dielines', 'Bleed', 'Colour setup'] },
      { kind: 'note', h: 'Space for FSSAI · MRP · Net qty', s: 'Planned in from the start. You confirm the legal content.' },
    ] },
  '12-how-we-work': { eyebrow: 'HOW WE WORK', page: 'home',
    hook: { lines: ['Shuru kaise', 'karein?'], sub: 'Bas *chaar step.*' },
    cells: [
      { kind: 'page', card: -1, h: 'Step 1 · Free audit', s: 'Store, listings, SEO and ads → a clear plan' },
      { kind: 'chips', h: 'Step 2 · Set up', s: 'Everything in place', items: ['Store or marketplace accounts', 'Catalog', 'Content', 'Tracking'] },
      { kind: 'marker', h: ['Step 3', 'Launch'], mark: 1, s: 'Optimised listings, pages and first campaigns' },
      { kind: 'list', h: 'Step 4 · Grow', items: ['Track sales', 'Fix weak points', 'Scale SEO, social and ads'] },
      { kind: 'stat', h: 'Bonus · 5 WordPress plugins', stats: [{ big: '₹5,000', unit: 'bundle', label: 'free for our clients' }] },
    ] },
};
