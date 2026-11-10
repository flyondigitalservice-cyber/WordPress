// Capture bombaymarketing.in pages (mobile viewport) + design tokens. Re-runnable: node capture/capture.mjs [slug...]
import { createRequire } from 'node:module';
const { chromium } = createRequire(import.meta.url)(process.env.PLAYWRIGHT_PATH || 'playwright');
import fs from 'node:fs';
const BASE = 'https://bombaymarketing.in';
const PAGES = { home: '/', myntra: '/myntra-seller-onboarding/', quick: '/blinkit-seller-onboarding/', seo: '/ecommerce-seo-services/',
  social: '/social-media-marketing-for-ecommerce/', ads: '/performance-marketing-for-ecommerce/', packaging: '/packaging-design-branding-mumbai/', plugins: '/plugins/' };
const want = process.argv.slice(2); const out = 'public/textures'; fs.mkdirSync(out, { recursive: true });
const b = await chromium.launch();
const ctx = await b.newContext({ viewport: { width: 430, height: 860 }, deviceScaleFactor: 3, isMobile: true, hasTouch: true,
  userAgent: 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Mobile/15E148 Safari/604.1' });
const layout = fs.existsSync(`${out}/layout.json`) ? JSON.parse(fs.readFileSync(`${out}/layout.json`)) : {};
for (const [k, p] of Object.entries(PAGES)) {
  if (want.length && !want.includes(k)) continue;
  const pg = await ctx.newPage();
  try {
    await pg.goto(BASE + p, { waitUntil: 'networkidle', timeout: 60000 });
    await pg.evaluate(() => document.fonts.ready);
    // fixed chrome (bottom bar, WhatsApp bubble, back-to-top) would be stamped over the content of a full-page shot
    await pg.addStyleTag({ content: '.bm-mbar,.bm-fab,#uicore-back-to-top{display:none!important}' });
    // scroll through once so lazy images load, then return to top
    await pg.evaluate(async () => { for (let y = 0; y < document.body.scrollHeight; y += 600) { window.scrollTo(0, y); await new Promise(r => setTimeout(r, 120)); } window.scrollTo(0, 0); });
    await pg.waitForTimeout(900);
    const info = await pg.evaluate(() => {
      const cs = (el) => { if (!el) return null; const s = getComputedStyle(el); return { font: s.fontFamily, size: s.fontSize, weight: s.fontWeight, lh: s.lineHeight, ls: s.letterSpacing, color: s.color, bg: s.backgroundColor, radius: s.borderRadius, text: (el.innerText || '').slice(0, 80) }; };
      const heads = [...document.querySelectorAll('h1,h2,h3')].map(h => { const r = h.getBoundingClientRect(); return { tag: h.tagName, text: h.innerText.trim().slice(0, 90), y: Math.round(r.top + scrollY), h: Math.round(r.height) }; });
      const box = (el) => { const r = el.getBoundingClientRect(); return { x: Math.round(r.left), y: Math.round(r.top + scrollY), w: Math.round(r.width), h: Math.round(r.height) }; };
      const cards = [...document.querySelectorAll('.bm-card')].map(c => ({ title: (c.querySelector('b,h3')?.innerText || '').trim(), ...box(c) }));
      const secs = [...document.querySelectorAll('.bm-page > section')].map(s => ({ cls: s.className, ...box(s) }));
      const btn = document.querySelector('a.wp-block-button__link, .wp-element-button, a[class*=btn], button');
      const bgs = {}; for (const el of document.querySelectorAll('section,div,header,footer,a,span')) { const c = getComputedStyle(el).backgroundColor; if (c !== 'rgba(0, 0, 0, 0)') bgs[c] = (bgs[c] || 0) + 1; }
      const cols = {}; for (const el of document.querySelectorAll('h1,h2,h3,p,a,span,li')) { const c = getComputedStyle(el).color; cols[c] = (cols[c] || 0) + 1; }
      const top = (o) => Object.entries(o).sort((a, b) => b[1] - a[1]).slice(0, 8);
      return { title: document.title, pageH: document.documentElement.scrollHeight, body: cs(document.body), h1: cs(document.querySelector('h1')), h2: cs(document.querySelector('h2')), p: cs(document.querySelector('p')), btn: cs(btn), heads, cards, secs, bgs: top(bgs), cols: top(cols),
        logo: [...document.querySelectorAll('header img, .custom-logo, header svg')].map(i => i.src || i.outerHTML.slice(0, 200)).slice(0, 3) };
    });
    await pg.screenshot({ path: `${out}/${k}.png`, fullPage: true });
    layout[k] = info; console.log(k, info.pageH, info.title);
  } catch (e) { console.log('FAIL', k, e.message.slice(0, 200)); }
  await pg.close();
}
fs.writeFileSync(`${out}/layout.json`, JSON.stringify(layout, null, 1));
await b.close();
