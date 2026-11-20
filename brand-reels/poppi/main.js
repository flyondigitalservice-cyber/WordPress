// POPPI — "Naam badlo, kismat badlo". Pop Art brand reel, 1080×1920. render(t) is pure.
import {
  pop, W, H, C, PASTEL, TL, LN, loadTimeline, ev, h2, rr, circ, rectPts, ellipsePts, shape, phone, heart, burst, arrow, front,
  printType, label, slip, handleSlip, frame, install, clamp, lerp, seg, ss, eio, eo, ei, back,
} from './kit/kit.js';
import { HANDLE } from './kit/handle.js';

await loadTimeline();
const A = id => LN[id].t0, B = id => LN[id].t1;
const DUR = TL.dur;
const P1 = [0, 0], P2 = [1400, 0], P3 = [2800, 0], P4 = [4200, 0], P6 = [4200, 3600], P8 = [5600, 3600];
const ground = (x, y, ink, w = 1600, h = 2600) => pop.fill(rectPts(x - w / 2, y - h / 2, w, h), ink);
const stampS = (t, t0, d = .22) => t < t0 ? 0 : 1 + .14 * (1 - eo(seg(t, t0, t0 + d)));
const flipX = (t, tf, d = .36) => Math.abs(Math.cos(Math.PI * seg(t, tf - d / 2, tf + d / 2)));
const AMBER = { r: .55, y: .9, b: .25 };

// ---------------------------------------------------------------- props (generic, no real packaging)
function can(x, y, s = 1, ink = C.pink, o = {}) {
  pop.save(); pop.translate(x, y); pop.scale(s); pop.rotate(o.rot || 0);
  const body = [[-120, -190], [120, -190], [120, 200], [-120, 200], [-120, -190]];
  pop.fill(body, ink);
  pop.fill(rectPts(-120, -40, 240, 120), o.band || C.paper);
  shape(circ(0, 20, 48), o.fruit || C.red, 6);
  pop.fill(ellipsePts(0, -42, 14, 22, 16), C.green);
  pop.over(rectPts(55, -190, 65, 390), { b: .3, r: .15 });
  pop.knock(rectPts(-95, -170, 20, 330));
  pop.outline(body, 8, { closed: true });
  shape(ellipsePts(0, -200, 120, 30, 40), C.steel, 7);
  shape(rr(-30, -214, 60, 22, 10), C.paper, 4);
  shape(ellipsePts(0, 205, 112, 22, 40), C.steel, 6);
  pop.restore();
}
function bottle(x, y, s = 1, ink = AMBER, o = {}) {
  pop.save(); pop.translate(x, y); pop.scale(s); pop.rotate(o.rot || 0);
  const b = [[-50, -300], [50, -300], [50, -180], [130, -100], [130, 300], [-130, 300], [-130, -100], [-50, -180], [-50, -300]];
  shape(b, ink, 8);
  pop.knock(rr(-104, -80, 22, 340, 10));
  shape(rr(-60, -350, 120, 60, 12), C.red, 6);
  if (o.label !== false) { shape(rectPts(-110, 20, 220, 170), C.cream, 6); if (o.text) pop.text(o.text, 0, 105, { size: o.textSize || 54, font: 'Anton', lh: 1, ...(o.textInk ? { fill: o.textInk } : {}) }); }
  pop.restore();
}
function glass(x, y, s = 1, fill = 1) {
  pop.save(); pop.translate(x, y); pop.scale(s);
  const g = [[-150, -230], [150, -230], [120, 240], [-120, 240], [-150, -230]];
  pop.fill(g, C.paper);
  const h = 440 * fill, top = 240 - h;
  if (fill > 0) {
    const liq = [[-150 + 30 * (1 - (top + 230) / 470), top], [150 - 30 * (1 - (top + 230) / 470), top], [120, 240], [-120, 240]];
    pop.fill(liq, C.pink); pop.over(liq, { y: .45 });
    for (let k = 0; k < 9; k++) { const bx = -90 + h2(k, 1) * 180, by = 220 - ((h2(k, 2) * 400 + fill * 300) % Math.max(20, h - 30)); if (by > top + 10) shape(circ(bx, by, 10 + h2(k, 3) * 8), C.paper, 3); }
  }
  pop.outline(g, 8, { closed: true });
  pop.knock([[-128, -200], [-108, -200], [-90, 200], [-104, 200]]);
  pop.restore();
}
function apple(x, y, s = 1) {
  pop.save(); pop.translate(x, y); pop.scale(s);
  shape(ellipsePts(0, 0, 110, 100, 40), C.red, 7); pop.knock(ellipsePts(-45, -35, 18, 26, 16));
  shape([[0, -95], [12, -150], [24, -150], [10, -95]], C.dark, 4); shape(ellipsePts(55, -130, 44, 20, 24, 0), C.green, 5);
  pop.restore();
}
function tv(x, y, s = 1, ink = C.blue, screenInk = C.sky) {
  pop.save(); pop.translate(x, y); pop.scale(s);
  pop.line([[-40, -300], [-140, -440]], 9, { taper: 0 }); pop.line([[40, -300], [140, -440]], 9, { taper: 0 });
  shape(rr(-360, -300, 720, 540, 50), ink, 9); shape(rr(-310, -250, 520, 440, 40), screenInk, 7);
  shape(circ(285, -150, 30), C.yellow, 6); shape(circ(285, -50, 30), C.yellow, 6);
  pop.restore();
}

// ---------------------------------------------------------------- timings + sound
const T = {
  head: A('p01') + .2, big: A('p01') + 3.6, title: A('p02') - .05,
  track1: A('p03') - .85, yuck: A('p03') + 5.2,
  track2: A('p04') - .8, mix0: A('p04') + .6, mix1: B('p04'),
  track3: A('p05') - .8, cross: A('p05') + 3.4, dull0: A('p05') + 3.0, dull1: B('p05') + .2,
  flip6: A('p06') - .02, deal: A('p06') + 1.6, badlo: A('p06') + 2.95,
  flip7: A('p07') - .1, cans: A('p07') + 1.6,
  track8: A('p08') - .8, ph0: A('p08') + .3, wall1: B('p08') - .2,
  track9: A('p09') - .9, num: A('p09') + 1.4, sold: A('p09') + 4.6,
  flip10: A('p10') - .1, end: A('p11') - .2,
};
ev(.05, 'stamp', { v: 1.2 }); ev(T.head, 'print'); ev(T.big, 'stamp', { v: 1.3 }); ev(T.title, 'print', { v: 1.1 });
ev(T.track1, 'whoosh', { d: 1 }); ev(T.track1 + 1, 'stamp'); ev(T.yuck, 'stamp', { v: 1 });
ev(T.track2, 'whoosh', { d: 1 }); [0, .7, 1.4].forEach(d => ev(T.mix0 + d, 'stamp', { v: .7 }));
ev(T.track3, 'whoosh', { d: 1 }); ev(T.cross, 'stamp', { v: 1.3 }); ev(T.dull0, 'crackle', { d: T.dull1 - T.dull0 });
ev(T.flip6, 'flip', { v: 1.3 }); ev(T.deal, 'stamp', { v: 1.2 }); ev(T.badlo, 'print');
ev(T.flip7, 'flip', { v: 1.3, note: 0 }); [0, 1, 2, 3, 4].forEach(k => ev(T.cans + k * .22, 'flip', { v: .6, note: 4 + k * 4 }));
ev(T.track8, 'whoosh', { d: 1 });
ev(T.track9, 'whoosh', { d: 1.1 }); ev(T.num, 'print', { v: 1.2 }); ev(T.sold, 'stamp', { v: 1.3 });
ev(T.flip10, 'flip', { v: 1.1 }); ev(T.end + .6, 'print');

// ---------------------------------------------------------------- posters
function poster1(t) {
  const [x, y] = P1;
  ground(x, y, C.red);
  bottle(x - 200, y + 110, .82 * (stampS(t, 0) || 1), AMBER, { text: 'SIRKA', textSize: 64 });
  if (t > .9) can(x + 210, y + 140, .85 * stampS(t, .9), C.sky, { fruit: C.red });
  if (t > .9) pop.text('+', x + 5, y + 120, { size: 160, fill: C.yellow, outline: 5 });
  printType('SIRKE WALA', x, y - 600, { size: 160, ink: C.yellow, dropInk: C.blue }, seg(t, T.head, T.head + .6));
  printType('SODA?', x, y - 430, { size: 190, ink: C.paper, dropInk: C.blue }, seg(t, T.head + .5, T.head + 1.1));
  if (t >= T.big) { pop.save(); pop.translate(x + 260, y - 230); pop.scale(stampS(t, T.big, .25)); pop.rotate(.1); burst(0, 0, 200, C.yellow, 18); pop.text('$1.95B', 0, 4, { size: 104, fill: C.red, outline: 4 }); pop.restore(); }
  if (t >= T.title) {
    const e = eo(seg(t, T.title, T.title + .45));
    pop.fill(rectPts(x - 800 + (1 - e) * -900, y - 820, 1600, 640), C.yellow);
    printType('POPPI', x, y - 500, { size: 280, ink: C.red, dropInk: C.blue, letter: 8 }, seg(t, T.title + .15, T.title + .75));
  }
}
function poster2(t) {
  const [x, y] = P2;
  ground(x, y, C.yellow);
  printType('TEXAS', x, y - 430, { size: 210, ink: C.red, dropInk: C.blue }, seg(t, T.track1 + .5, T.track1 + 1.1));
  if (t > T.track1 + 1) bottle(x, y + 200, 1.1 * stampS(t, T.track1 + 1), AMBER, { text: 'ACV', textSize: 90 });
  if (t >= T.yuck) { pop.save(); pop.translate(x + 270, y - 160); pop.scale(stampS(t, T.yuck)); pop.rotate(.14); burst(0, 0, 170, C.green, 14); pop.text('YUCK!', 0, 4, { size: 92, fill: C.paper, outline: 4 }); pop.restore(); }
}
function poster3(t) {
  const [x, y] = P3;
  ground(x, y, C.sky);
  printType('RASOI MEIN', x, y - 440, { size: 180, ink: C.yellow, dropInk: C.red }, seg(t, T.track2 + .5, T.track2 + 1.1));
  const items = [[-300, -150, 'b'], [0, -200, 'a'], [300, -150, 's']];
  items.forEach(([dx, dy, k], n) => {
    const ts = T.mix0 + n * .7; if (t < ts) return;
    const s = stampS(t, ts) * .55;
    const into = eio(seg(t, T.mix1 - 1.4 + n * .15, T.mix1 - .5 + n * .15));
    const px = x + dx * (1 - into), py = y + dy + into * 200, sc = s * (1 - .6 * into);
    if (into >= 1) return;
    if (k === 'b') bottle(px, py, sc, AMBER, { label: false });
    else if (k === 'a') apple(px, py, sc * 1.4);
    else can(px, py, sc, C.paper, { band: C.sky, fruit: C.sky });
  });
  glass(x, y + 330, .9, eo(seg(t, T.mix1 - 1.0, T.mix1 + .4)));
}
function poster4(t) {
  const [x, y] = P4;
  ground(x, y, C.cream);
  printType('NAAM?', x, y - 430, { size: 210, ink: C.red, dropInk: C.blue }, seg(t, T.track3 + .5, T.track3 + 1.1));
  bottle(x, y + 200, 1.15, { r: .5, y: .55, b: .45 }, { text: 'MOTHER\nBEVERAGE', textSize: 46 });
  if (t > T.dull0) front(x - 800, y - 1300, x + 800, y + 1300, seg(t, T.dull0, T.dull1) * 1.3, { r: .35, b: .45 }, { max: .7 });
  if (t >= T.cross) {
    pop.save(); pop.translate(x, y + 300); pop.scale(stampS(t, T.cross, .25));
    shape(pop.ribbon([[-230, -150], [230, 150]], 70, { taper: .05, tmin: .6 }), C.red, 6); shape(pop.ribbon([[230, -150], [-230, 150]], 70, { taper: .05, tmin: .6 }), C.red, 6);
    pop.restore();
  }
}
function poster5(t) {   // 2018: the TV deal
  const [x, y] = P4;
  ground(x, y, C.yellow);
  printType('2018', x, y - 430, { size: 230, ink: C.red, dropInk: C.blue }, seg(t, T.flip6 + .3, T.flip6 + .9));
  tv(x, y + 150, .9, C.blue, C.sky);
  bottle(x - 70, y + 170, .5, AMBER, { label: false });
  if (t >= T.deal) { pop.save(); pop.translate(x + 250, y - 170); pop.scale(stampS(t, T.deal, .25)); pop.rotate(.12); burst(0, 0, 170, C.red, 16); pop.text('DEAL!', 0, 4, { size: 96, fill: C.yellow, outline: 4 }); pop.restore(); }
}
const CANS = [C.pink, C.sky, C.lemon, C.mint, C.lilac];
const FRUIT = [C.red, C.blue, C.yellow, C.green, C.hot];
function poster7(t) {   // new name, bright cans
  const [x, y] = P4;
  ground(x, y, C.blue);
  printType('POPPI', x, y - 420, { size: 270, ink: C.yellow, dropInk: C.red, letter: 8 }, seg(t, T.flip7 + .25, T.flip7 + .85));
  for (let k = 0; k < 5; k++) {
    const ts = T.cans + k * .22; if (t < ts) continue;
    const sx = Math.abs(Math.cos(Math.PI * seg(t, ts - .15, ts + .15))) * (t > ts + .15 ? 1 : 1);
    const cx = x + (k - 2) * 205, cy = y + 180 + (k % 2) * 60;
    pop.save(); pop.translate(cx, cy); pop.scale(Math.max(.02, sx), 1); can(0, 0, .78, CANS[k], { fruit: FRUIT[k] }); pop.restore();
  }
}
// TikTok: phones with cans multiply into a wall
const TS = 480;
const ORDER = []; for (let j = -5; j <= 5; j++) for (let i = -4; i <= 4; i++) if (i || j) ORDER.push([i, j]);
ORDER.sort((a, b) => (Math.max(Math.abs(a[0]), Math.abs(a[1])) - Math.max(Math.abs(b[0]), Math.abs(b[1]))) || (Math.atan2(a[1], a[0]) - Math.atan2(b[1], b[0])));
const STAMP = new Map();
ORDER.forEach(([i, j], n) => { const ta = T.ph0 + 1.2 + (T.wall1 - T.ph0 - 1.2) * Math.pow((n + 1) / ORDER.length, .55); STAMP.set(i + ',' + j, ta); if (n < 24 || n % 4 == 0) ev(ta, 'stamp', { pan: clamp(i / 4, -1, 1) * .6, v: n < 8 ? .8 : .45 }); });
const stampT = (i, j) => (i || j) ? STAMP.get(i + ',' + j) ?? 1e9 : T.ph0;
function poster8(t) {
  const [x, y] = P6;
  ground(x, y, C.pink);
  for (let j = -5; j <= 5; j++) for (let i = -4; i <= 4; i++) {
    const s = stampS(t, stampT(i, j)); if (!s) continue;
    const k = Math.floor(h2(i, j, 5) * 5);
    pop.save(); pop.translate(x + i * TS, y + 100 + j * TS); pop.scale(s);
    pop.fill(rectPts(-TS / 2, -TS / 2, TS, TS), [C.pink, C.yellow, C.sky][Math.floor(h2(i, j, 2) * 3)]); pop.outline(rectPts(-TS / 2, -TS / 2, TS, TS), 6, { closed: true });
    phone(0, 0, .68, PASTEL[k], { rot: (h2(i, j, 1) - .5) * .12, icon: () => can(0, -85, .55, CANS[k], { fruit: FRUIT[k] }) });
    pop.restore();
  }
}
function poster9(t) {   // 2025: the number
  const [x, y] = P8;
  ground(x, y, C.yellow);
  printType('2025', x, y - 500, { size: 160, ink: C.blue, dropInk: C.red }, seg(t, T.track9 + .6, T.track9 + 1.2));
  printType('$1.95B', x, y - 260, { size: 300, ink: C.red, dropInk: C.blue, letter: 2 }, seg(t, T.num, T.num + .7));
  for (let k = 0; k < 5; k++) can(x + (k - 2) * 190, y + 190 + Math.abs(k - 2) * 30, .58, CANS[k], { fruit: FRUIT[k], rot: (k - 2) * .06 });
  if (t >= T.sold) { pop.save(); pop.translate(x + 300, y - 60); pop.scale(stampS(t, T.sold, .25)); pop.rotate(-.12); burst(0, 0, 175, C.blue, 16); pop.text('BIK', 0, -40, { size: 90, fill: C.yellow, outline: 4 }); pop.text('GAYA!', 0, 50, { size: 90, fill: C.yellow, outline: 4 }); pop.restore(); }
}
function poster10(t) {   // lesson + end: plain bottles, one bright can
  const TS7 = 500;
  for (let j = -4; j <= 4; j++) for (let i = -3; i <= 3; i++) {
    const odd = i === 0 && j === 0;
    const s = stampS(t, T.flip10 + .1 + (Math.abs(i) + Math.abs(j)) * .06); if (!s) continue;
    pop.save(); pop.translate(P8[0] + i * TS7, P8[1] + 3000 + j * TS7); pop.scale(s);
    pop.fill(rectPts(-TS7 / 2, -TS7 / 2, TS7, TS7), odd ? C.yellow : C.cream); pop.outline(rectPts(-TS7 / 2, -TS7 / 2, TS7, TS7), 6, { closed: true });
    if (odd) can(0, 10, .95, C.pink, { fruit: C.red }); else bottle(0, 10, .62, { r: .5, y: .55, b: .45 }, { label: true });
    pop.restore();
  }
}

// ---------------------------------------------------------------- camera + shots
function render(t) {
  frame(t, (t) => {
    let cam;
    if (t < T.track1) cam = { x: 0, y: 0, zoom: lerp(1, 1.04, ss(seg(t, 0, T.track1))) };
    else if (t < T.track2) { const u = eio(seg(t, T.track1, T.track1 + 1.1)); cam = { x: lerp(P1[0], P2[0], u), y: 0, zoom: lerp(1.04, 1, u) - .22 * Math.sin(Math.PI * u) }; }
    else if (t < T.track3) { const u = eio(seg(t, T.track2, T.track2 + 1.1)); cam = { x: lerp(P2[0], P3[0], u), y: 0, zoom: 1 - .22 * Math.sin(Math.PI * u) }; }
    else if (t < T.track8) { const u = eio(seg(t, T.track3, T.track3 + 1.1)); const pz = ss(seg(t, A('p05'), T.dull1)) * (t < T.flip6 ? 1 : 0); cam = { x: lerp(P3[0], P4[0], u), y: 0, zoom: 1 - .22 * Math.sin(Math.PI * u) + .08 * pz }; }
    else if (t < T.track9) { const u = eio(seg(t, T.track8, T.track8 + 1.2)), v = eio(seg(t, T.ph0 + 1, T.wall1 + .5)); cam = { x: P4[0], y: lerp(0, P6[1], u), zoom: 1 - .3 * Math.sin(Math.PI * u) - .66 * v, rot: .03 * v }; }
    else if (t < T.flip10) { const u = eio(seg(t, T.track9, T.track9 + 1.3)); cam = { x: lerp(P6[0], P8[0], u), y: P8[1], zoom: lerp(.34, 1, u) + .05 * ss(seg(t, T.num, T.flip10)), rot: lerp(.03, 0, u) }; }
    else cam = { x: P8[0], y: P8[1] + 3000, zoom: lerp(.7, .56, ss(seg(t, T.flip10, DUR))) };
    let sx = 1; for (const tf of [T.flip6, T.flip7, T.flip10]) if (Math.abs(t - tf) < .18) sx = flipX(t, tf);
    pop.begin(cam, { desk: null });
    pop.save(); pop.translate(cam.x, cam.y); pop.scale(Math.max(sx, .02), 1); pop.translate(-cam.x, -cam.y);
    if (t < T.flip10) {
      if (t < T.track2 + 1.2) { poster1(t); poster2(t); }
      if (t >= T.track2 - .1 && t < T.track3 + 1.2) poster3(t);
      if (t >= T.track3 - .1 && t < T.flip6) poster4(t);
      if (t >= T.flip6 && t < T.flip7) poster5(t);
      if (t >= T.flip7 && t < T.track8 + 1.4) poster7(t);
      if (t >= T.track8 - .1 && t < T.track9 + 1.4) poster8(t);
      if (t >= T.track9 - .1) poster9(t);
    } else poster10(t);
    pop.restore();
  }, (t) => {
    slip(t, A('p03') + .4, B('p03'), 70, 165, [{ s: 'ALLISON ELLSWORTH', size: 56 }, { s: 'APPLE CIDER VINEGAR', size: 42, ink: C.red }], { shadowInk: C.blue });
    slip(t, T.mix0, B('p04'), 70, 165, [{ s: 'SIRKA + FRUIT + SODA', size: 54, ink: C.blue }], { shadowInk: C.red });
    slip(t, A('p05') + .3, B('p05'), 70, 165, [{ s: '"MOTHER BEVERAGE"', size: 58 }, { s: 'DRINK ACHHA, NAAM BURA', size: 40, ink: C.red }], { shadowInk: C.blue });
    slip(t, A('p06') + .6, T.badlo - .1, 70, 165, [{ s: 'SHARK TANK', size: 70, ink: C.red }, { s: 'TV PAR DEAL', size: 46 }], { shadowInk: C.blue });
    slip(t, T.badlo, B('p06') + .1, 70, 165, [{ s: 'PEHLI SALAH:', size: 52 }, { s: 'NAAM BADLO!', size: 70, ink: C.red }], { shadowInk: C.yellow });
    slip(t, A('p07') + 1.2, B('p07'), 70, 165, [{ s: 'CHAMKEELE CANS', size: 62, ink: C.blue }], { shadowInk: C.red });
    slip(t, A('p08') + .3, B('p08'), 70, 165, [{ s: 'TIKTOK: ROZ VIDEO', size: 58 }, { s: 'GUT HEALTH = TREND', size: 46, ink: C.red }], { shadowInk: C.blue });
    slip(t, A('p09') + 1.8, B('p09'), 70, 165, [{ s: 'PEPSICO NE KHAREEDA', size: 54, ink: C.blue }], { shadowInk: C.red });
    if (t >= T.flip10 && t < T.end + .3) {
      const o = t > T.end ? -ei(seg(t, T.end, T.end + .3)) * 1200 : 0;
      printType('SABAK', W / 2 + o, 420, { size: 240, ink: C.yellow, dropInk: C.blue }, seg(t, T.flip10 + .3, T.flip10 + .9));
    }
    if (t >= T.end) {
      printType('FOLLOW', W / 2, 520, { size: 250, ink: C.yellow, dropInk: C.blue }, seg(t, T.end + .3, T.end + .9));
      if (HANDLE) label(W / 2, 680, [{ s: HANDLE, size: 66, font: 'Archivo' }], seg(t, T.end + .7, T.end + 1.2), { align: 'center', rot: -.02, shadowInk: C.blue });
      label(W / 2, 1640, [{ s: 'AGLI KAHANI: LIQUID DEATH', size: 48 }], seg(t, T.end + 1.4, T.end + 1.9), { align: 'center', rot: .02 });
    } else handleSlip(t, HANDLE);
  });
}
install(render, DUR);
