// Brand Story Reels — shared Pop Art kit (vertical 1080×1920).
// The print engine (pop.js) + type (headline, label, subtitle, handle), product prints and walls.
// Page units: at zoom 1, one unit = one pixel; (0,0) is the centre of the first poster.
import { createPop, T, rectPts, ellipsePts, toPath, dens } from './pop.js';
import { clamp, lerp, seg, ss, eio, eo, ei, back, hash } from '/core/lib.js';
export { T, rectPts, ellipsePts, clamp, lerp, seg, ss, eio, eo, ei, back, hash };

export const W = 1080, H = 1920;
export const pop = createPop(document.getElementById('c'), { W, H });
export const Q = new URLSearchParams(location.search);

// colourways (plate densities). Primaries flat; pastels are light dot tints.
export const C = {
  ...T,
  green: { y: 1, b: .62 }, forest: { y: .8, b: .95 }, pink: { r: .38 }, hot: { r: .75 }, sky: { b: .38 }, lemon: { y: .55 },
  mint: { y: .45, b: .3 }, lilac: { r: .32, b: .38 }, peach: { r: .3, y: .5 }, cream: { y: .12 }, steel: { b: .22, r: .05 },
  dark: { r: .8, b: .9 }, fire: { r: .6, y: 1 }, ice: { b: .2 },
};
export const PASTEL = [C.pink, C.sky, C.lemon, C.mint, C.lilac, C.peach];

// ---------------------------------------------------------------- timeline + sound events
export let TL = null, LN = {};
export async function loadTimeline() {
  TL = await (await fetch('timeline.json')).json();
  TL.lines.forEach(l => LN[l.id] = l);
  await Promise.all(['40px Anton', '40px Archivo'].map(f => document.fonts.load(f)));
  return TL;
}
export const EV = [];
export const ev = (t, type, o = {}) => EV.push({ t: +(+t).toFixed(3), type, ...o });
export const h2 = (i, j, k = 0) => hash(i * 17.13 + j * 91.7 + k * 7.31);

// ---------------------------------------------------------------- shapes
export const rr = (x, y, w, h, r, n = 8) => {   // rounded rectangle polygon
  r = Math.min(r, w / 2, h / 2); const o = [];
  const c = [[x + w - r, y + r, -Math.PI / 2], [x + w - r, y + h - r, 0], [x + r, y + h - r, Math.PI / 2], [x + r, y + r, Math.PI]];
  for (const [cx, cy, a0] of c) for (let k = 0; k <= n; k++) { const a = a0 + k / n * Math.PI / 2; o.push([cx + Math.cos(a) * r, cy + Math.sin(a) * r]); }
  o.push(o[0]); return o;
};
export const circ = (x, y, r, n = 40) => ellipsePts(x, y, r, r, n);
export const poly = (pts, dx = 0, dy = 0, s = 1) => pts.map(([x, y]) => [dx + x * s, dy + y * s]);
const shape = (s, ink, w = 7) => { pop.fill(s, ink); pop.outline(s, w, { closed: true }); };
export { shape };

// tumbler with handle: origin at its centre, ~560 tall at s=1
export function tumbler(x, y, s = 1, ink = C.blue, o = {}) {
  pop.save(); pop.translate(x, y); pop.scale(s);
  // handle (behind): a thick C on the right
  const hd = []; for (let k = 0; k <= 24; k++) { const a = -Math.PI / 2 + k / 24 * Math.PI; hd.push([130 + Math.cos(a) * 120, -40 + Math.sin(a) * 165]); }
  const outer = pop.ribbon(hd, 62, { taper: 0, tmin: 1 });
  shape(outer, o.handle || ink, 7);
  // body: wide top, tapering to a narrow base (fits a cup holder)
  const body = [[-150, -205], [150, -205], [128, 150], [96, 200], [92, 262], [-92, 262], [-96, 200], [-128, 150], [-150, -205]];
  shape(body, ink, 8);
  pop.fill([[-150, -205], [150, -205], [146, -170], [-146, -170]], C.paper);           // printed band under the lid
  pop.over([[60, -170], [140, -170], [118, 150], [70, 150]], { b: .35, r: .2 });        // shade (dots) on the far side
  pop.knock([[-118, -150], [-92, -150], [-78, 130], [-100, 130]]);                      // highlight
  pop.outline(body, 8, { closed: true });
  if (o.ice) iceCubes(0, -60, .9);
  // lid + straw
  shape(rr(-162, -262, 324, 64, 22), o.lid || C.steel, 7);
  shape([[18, -262], [44, -262], [64, -440], [38, -440]], o.straw || C.paper, 6);
  pop.restore();
}
export function iceCubes(x, y, s = 1) {
  pop.save(); pop.translate(x, y); pop.scale(s);
  [[-60, -10, -.2], [40, -30, .25], [-5, 60, .1]].forEach(([a, b, r]) => { pop.save(); pop.translate(a, b); pop.rotate(r); shape(rr(-42, -42, 84, 84, 16), C.ice, 6); pop.knock(rr(-28, -30, 22, 22, 6)); pop.restore(); });
  pop.restore();
}
// old vacuum flask (1913 world)
export function flask(x, y, s = 1, ink = C.green) {
  pop.save(); pop.translate(x, y); pop.scale(s);
  shape(rr(-120, -300, 240, 620, 46), ink, 8);
  pop.over(rr(40, -280, 70, 580, 30), { b: .5, r: .2 });
  pop.knock(rr(-92, -260, 26, 500, 12));
  shape(rr(-100, -380, 200, 92, 18), ink, 7);            // cup-cap
  shape(rr(-70, -420, 140, 46, 12), C.steel, 6);
  shape(rr(-128, 120, 256, 46, 10), C.steel, 6);           // band
  pop.restore();
}
// side-view car; origin centre of the car body
export const CAR_BODY = [[-420, 60], [-410, -40], [-300, -70], [-200, -170], [120, -170], [240, -70], [400, -50], [430, 40], [420, 90], [-420, 90], [-420, 60]];
export function car(x, y, s = 1, ink = C.red, o = {}) {
  pop.save(); pop.translate(x, y); pop.scale(s);
  shape(CAR_BODY, ink, 9);
  shape([[-180, -150], [-30, -150], [-30, -72], [-260, -72]], C.sky, 6);
  shape([[0, -150], [110, -150], [200, -72], [0, -72]], C.sky, 6);
  pop.fill(rectPts(-405, 10, 820, 18), C.paper);
  for (const wx of [-250, 250]) { shape(circ(wx, 92, 86), C.dark, 8); shape(circ(wx, 92, 36), C.steel, 6); }
  if (o.bow) {   // a gift bow on the roof
    shape(rectPts(-30, -172, 60, 172), C.yellow, 6);
    shape(ellipsePts(-85, -215, 90, 52, 40), C.yellow, 7); shape(ellipsePts(85, -215, 90, 52, 40), C.yellow, 7);
    shape(circ(0, -205, 38), C.red, 7);
  }
  pop.restore();
}
// phone card: a social post with a heart
export function phone(x, y, s = 1, ink = C.pink, o = {}) {
  pop.save(); pop.translate(x, y); pop.scale(s); pop.rotate(o.rot || 0);
  shape(rr(-170, -300, 340, 600, 40), C.paper, 8);
  pop.fill(rr(-150, -250, 300, 330, 14), ink); pop.outline(rr(-150, -250, 300, 330, 14), 5, { closed: true });
  if (o.icon) o.icon();
  heart(-90, 150, .9, C.red); pop.fill(rr(-30, 128, 170, 18, 9), C.steel); pop.fill(rr(-30, 162, 120, 18, 9), C.steel);
  pop.fill(rr(-150, 210, 300, 22, 11), C.steel);
  pop.restore();
}
export function heart(x, y, s = 1, ink = C.red) {
  const p = []; for (let k = 0; k <= 48; k++) { const a = k / 48 * Math.PI * 2; p.push([x + s * 16 * Math.pow(Math.sin(a), 3) * 2.4, y - s * (13 * Math.cos(a) - 5 * Math.cos(2 * a) - 2 * Math.cos(3 * a) - Math.cos(4 * a)) * 2.4]); }
  shape(p, ink, 5);
}
// starburst (price / sale burst)
export function burst(x, y, r, ink = C.yellow, n = 16, rot = 0) {
  const p = []; for (let k = 0; k <= n * 2; k++) { const a = rot + k / (n * 2) * Math.PI * 2, rr2 = k % 2 ? r * .74 : r; p.push([x + Math.cos(a) * rr2, y + Math.sin(a) * rr2]); }
  shape(p, ink, 8);
}
// arrow pointing right
export function arrow(x, y, len, ink = C.red, s = 1) {
  const h = 60 * s, w = 34 * s;
  const p = [[x, y - w / 2], [x + len - h, y - w / 2], [x + len - h, y - h * .8], [x + len, y], [x + len - h, y + h * .8], [x + len - h, y + w / 2], [x, y + w / 2], [x, y - w / 2]];
  shape(p, ink, 6);
}
// a front of growing dots crossing a region (dots as data): u = front position 0..1 across [x0,x1]
export function front(x0, y0, x1, y1, u, ink = C.dark, o = {}) {
  const n = o.n || 36, wdt = (x1 - x0) / n, soft = o.soft ?? .28;
  for (let k = 0; k < n; k++) {
    const c = (k + .5) / n; const d = clamp((u - c) / soft + .5, 0, 1); if (d <= .02) continue;
    const dd = ss(d) * (o.max ?? 1);
    const ink2 = {}; for (const ch of ['r', 'y', 'b']) if (ink[ch]) ink2[ch] = ink[ch] * dd;
    pop.over(rectPts(x0 + k * wdt - .5, y0, wdt + 1, y1 - y0), ink2);
  }
}

// ---------------------------------------------------------------- type
export const screen = (fn) => { pop.save(); [pop.P, pop.L, pop.S].forEach(c => c.setTransform(1, 0, 0, 1, 0, 0)); fn(); pop.restore(); };
// headline printed in: colour lands from an offset, the black key snaps on
export function printType(str, x, y, o, p) {
  if (p <= 0) return;
  const e = eo(seg(p, 0, .7));
  const dx = (o.fromX ?? 70) * (1 - e), dy = (o.fromY ?? -36) * (1 - e);
  const base = { size: o.size, font: o.font || 'Anton', align: o.align || 'center', letter: o.letter, rot: o.rot || 0 };
  if (o.drop !== false) pop.text(str, x + (o.drop?.[0] ?? 10) - dx * .6, y + (o.drop?.[1] ?? 10) - dy * .6, { ...base, fill: o.dropInk || C.red });
  pop.text(str, x + dx, y + dy, { ...base, fill: o.ink || C.yellow });
  if (p > .45 && o.outline !== 0) pop.text(str, x, y, { ...base, outline: o.outline ?? Math.max(3, o.size * .045), spot: 'rgba(0,0,0,0)', read: o.read === false ? undefined : 'type:' + str });
}
// paper label slip (lines: [{s, size, font, ink}])
export function label(x, y, lines, p, o = {}) {
  if (p <= 0) return;
  const padX = o.padX ?? 30, padY = o.padY ?? 20, gap = o.gap ?? 8;
  const ws = lines.map(l => pop.measure(l.s, { size: l.size, font: l.font || 'Anton' }));
  const w = Math.max(...ws) + padX * 2, h = lines.reduce((a, l) => a + l.size * (l.lh ?? 1.12), 0) + gap * (lines.length - 1) + padY * 2;
  const e = eo(seg(p, 0, .55)), k = seg(p, .06, .7);
  const ax = o.align === 'center' ? x - w / 2 : o.align === 'right' ? x - w : x;
  pop.save(); pop.translate(ax + w / 2, y + h / 2); pop.rotate(o.rot ?? -.025); pop.translate(-(ax + w / 2), -(y + h / 2));
  const off = o.shadow ?? 11;
  pop.fill(rectPts(ax + off + (1 - e) * -60, y + off + (1 - e) * 26, w, h), o.shadowInk || C.red);
  pop.fill(rectPts(ax + (1 - e) * 44, y - (1 - e) * 22, w, h), o.bg || C.paper);
  if (p > .1) pop.outline(rectPts(ax, y, w, h), o.lw ?? 5, { closed: true });
  let yy = y + padY;
  lines.forEach((l, n) => {
    const lh = l.size * (l.lh ?? 1.12);
    const lx = o.align === 'center' ? ax + w / 2 : ax + padX;
    if (k > 0) {
      const ke = eo(seg(k, n * .1, n * .1 + .45));
      pop.text(l.s, lx + (1 - ke) * 22, yy + lh / 2 + 1, { size: l.size, font: l.font || 'Anton', align: o.align === 'center' ? 'center' : 'left', read: 'label:' + l.s, ...(l.ink ? { fill: l.ink } : {}) });
    }
    yy += lh + gap;
  });
  pop.restore();
}
// a label shown in [t0, t1]: prints in, then slides out left
export function slip(t, t0, t1, x, y, lines, o = {}) {
  if (t < t0 || t > t1 + .4) return;
  const dx = t > t1 ? -ei(seg(t, t1, t1 + .38)) * 1200 : 0;
  label(x + dx, y, lines, seg(t, t0, t0 + .5), o);
}

// subtitles: paper slip at bottom centre, Roman Hinglish, up to 2 lines
const wrap = (s, max = 26) => { const w = s.split(' '), out = ['']; for (const x of w) { const c = out[out.length - 1]; if (c && (c + ' ' + x).length > max && out.length < 2) out.push(x); else out[out.length - 1] = c ? c + ' ' + x : x; } return out; };
export function subtitle(t) {
  const c = TL.cues.find(c => t >= c.t0 && t < c.t1); if (!c) return;
  const p = seg(t, c.t0, c.t0 + .12);
  const size = 46, lines = wrap(c.text);
  const w = Math.max(...lines.map(l => pop.measure(l, { size, font: 'Archivo' }))) + 60, h = lines.length * size * 1.22 + 34;
  const x = W / 2 - w / 2, y = 1430 - h / 2;
  pop.fill(rectPts(x + 9, y + 9, w, h), C.blue);
  pop.fill(rectPts(x, y, w, h), C.paper);
  pop.outline(rectPts(x, y, w, h), 4, { closed: true });
  lines.forEach((l, k) => pop.text(l, W / 2, y + 17 + size * 1.22 * (k + .5) + (1 - p) * 6, { size, font: 'Archivo' }));
}
export function handleSlip(t, handle) {
  if (!handle) return;
  label(W / 2, 1580, [{ s: handle, size: 34, font: 'Archivo' }], 1, { align: 'center', rot: 0, padX: 22, padY: 10, shadow: 7, shadowInk: C.yellow, lw: 4 });
}

// end card: wall of the product, one tile in another colourway; FOLLOW + handle
export function endCard(t, t0, handle, tile, o = {}) {
  const p = seg(t, t0, t0 + .6);
  const z = lerp(.62, .5, ss(seg(t, t0, t0 + 3)));
  pop.begin({ x: 0, y: 0, zoom: z }, { desk: null });
  const TS = 520;
  for (let j = -3; j <= 3; j++) for (let i = -2; i <= 2; i++) {
    const odd = i === (o.oddI ?? 1) && j === (o.oddJ ?? -1);
    const st = t0 + .05 + (Math.abs(i) + Math.abs(j)) * .07;
    if (t < st) continue;
    const s = 1 + .12 * (1 - eo(seg(t, st, st + .22)));
    pop.save(); pop.translate(i * TS, j * TS); pop.scale(s);
    pop.fill(rectPts(-TS / 2, -TS / 2, TS, TS), odd ? (o.oddGround || C.yellow) : (o.ground || C.red));
    tile(0, 0, odd);
    pop.restore();
  }
  screen(() => {
    printType('FOLLOW', W / 2, 760, { size: 230, ink: C.yellow, dropInk: C.blue }, seg(t, t0 + .5, t0 + 1.1));
    if (handle) label(W / 2, 900, [{ s: handle, size: 64, font: 'Archivo' }], seg(t, t0 + .8, t0 + 1.3), { align: 'center', rot: -.02, shadowInk: C.blue });
    label(W / 2, 1060, [{ s: o.next || 'AGLI KAHANI JALD', size: 50 }], seg(t, t0 + 1.1, t0 + 1.6), { align: 'center', rot: .02, shadowInk: C.red });
  });
}

// ---------------------------------------------------------------- the frame
// draw(t) draws the page (calls pop.begin itself); overlays are printed in the same pass
export function frame(t, draw, overlay, fx = {}) {
  draw(t);
  screen(() => { overlay && overlay(t); if (!Q.has('nosubs')) subtitle(t); });
  pop.present({ cell: 5.2, ...fx });
}
export function install(render, dur) {
  window.EV = EV.sort((a, b) => a.t - b.t);
  window.DUR = dur;
  window.render = render;
  window.TEXTS = () => pop.reads.slice();
  window.READY = true;
}
