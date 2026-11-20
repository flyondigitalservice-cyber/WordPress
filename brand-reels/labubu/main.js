// LABUBU — "Log khilauna nahi, ehsaas khareedte hain". Pop Art brand reel, 1080×1920. render(t) is pure.
// The toy is a generic monster stand-in (horns, round eyes, toothy grin), not the real character design.
import {
  pop, W, H, C, PASTEL, TL, LN, loadTimeline, ev, h2, rr, circ, rectPts, ellipsePts, shape, phone, heart, burst, arrow, front,
  printType, label, slip, handleSlip, frame, install, clamp, lerp, seg, ss, eio, eo, ei, back,
} from './kit/kit.js';
import { HANDLE } from './kit/handle.js';

await loadTimeline();
const A = id => LN[id].t0, B = id => LN[id].t1;
const DUR = TL.dur;
const P1 = [0, 0], P2 = [1400, 0], P3 = [2800, 0], P4 = [4200, 0], P6 = [5600, 0];
const ground = (x, y, ink, w = 1600, h = 2600) => pop.fill(rectPts(x - w / 2, y - h / 2, w, h), ink);
const stampS = (t, t0, d = .22) => t < t0 ? 0 : 1 + .14 * (1 - eo(seg(t, t0, t0 + d)));
const flipX = (t, tf, d = .36) => Math.abs(Math.cos(Math.PI * seg(t, tf - d / 2, tf + d / 2)));

// ---------------------------------------------------------------- props
function monster(x, y, s = 1, ink = C.peach, o = {}) {
  pop.save(); pop.translate(x, y); pop.scale(s); pop.rotate(o.rot || 0);
  for (const k of [-1, 1]) shape([[k * 70, -150], [k * 120, -290], [k * 150, -140]], C.paper, 6);   // little horns
  shape(rectPts(-120, 120, 80, 110).map(([a, b]) => [a, b]), ink, 6); shape(rectPts(40, 120, 80, 110), ink, 6);   // feet
  shape(ellipsePts(0, 0, 190, 180, 50), ink, 8);                                                             // round fuzzy body
  pop.over(ellipsePts(60, 30, 120, 140, 30), { b: .25, r: .1 });
  for (const ex of [-70, 70]) { shape(circ(ex, -40, 46), C.paper, 6); shape(circ(ex + 8, -34, 20), C.dark, 3); }
  const m = [[-110, 50], [110, 50], [80, 110], [-80, 110], [-110, 50]]; pop.fill(m, C.dark); pop.outline(m, 6, { closed: true });
  for (let k = 0; k < 4; k++) shape([[-84 + k * 46, 52], [-48 + k * 46, 52], [-66 + k * 46, 86]], C.paper, 3);
  if (o.blush) for (const k of [-1, 1]) pop.over(ellipsePts(k * 130, 20, 30, 18, 16), { r: .55 });
  pop.restore();
}
function box(x, y, s = 1, ink = C.pink, o = {}) {
  pop.save(); pop.translate(x, y); pop.scale(s);
  shape([[-160, -120], [0, -200], [160, -120], [0, -40], [-160, -120]], o.top || C.paper, 6);
  shape([[-160, -120], [0, -40], [0, 220], [-160, 140], [-160, -120]], ink, 7);
  shape([[160, -120], [0, -40], [0, 220], [160, 140], [160, -120]], ink, 7); pop.over([[160, -120], [0, -40], [0, 220], [160, 140]], { b: .3, r: .2 });
  pop.text('?', -80, 70, { size: 150, fill: C.yellow, outline: 4 });
  pop.restore();
}
function person(x, y, s = 1, ink = C.blue) {
  pop.save(); pop.translate(x, y); pop.scale(s);
  shape(rr(-70, -40, 140, 260, 60), ink, 6); shape(circ(0, -110, 60), C.peach, 6);
  pop.restore();
}
function book(x, y, s = 1) {
  pop.save(); pop.translate(x, y); pop.scale(s);
  shape([[-420, -260], [0, -220], [420, -260], [420, 260], [0, 300], [-420, 260], [-420, -260]], C.red, 8);
  shape([[-390, -230], [-10, -195], [-10, 270], [-390, 235], [-390, -230]], C.paper, 6);
  shape([[390, -230], [10, -195], [10, 270], [390, 235], [390, -230]], C.paper, 6);
  for (let k = 0; k < 6; k++) pop.fill(rr(60, -150 + k * 60, 260 - (k % 2) * 60, 16, 8), C.steel);
  pop.restore();
}
function bag(x, y, s = 1, ink = C.dark) {
  pop.save(); pop.translate(x, y); pop.scale(s);
  pop.line(ellipsePts(0, -230, 170, 150, 30, Math.PI, Math.PI * 2), 26, { taper: 0 });
  shape([[-300, -230], [300, -230], [340, 240], [-340, 240], [-300, -230]], ink, 9);
  shape(rr(-60, -170, 120, 70, 14), C.yellow, 6);
  pop.restore();
}

// ---------------------------------------------------------------- timings + sound
const T = {
  q0: .0, head: A('b01') + .3, title: A('b02') - .05,
  track1: A('b03') - .85, mon: A('b03') + .4,
  track2: A('b04') - .8, boxes: A('b04') + 1.2, open0: A('b05') + 1.4,
  flip6: A('b06') - .02, like: A('b06') + 2.0,
  track7: A('b07') - .8, tag: A('b07') + 3.8,
  track8: A('b08') - .8, chart: A('b08') + .2,
  flip9: A('b09') - .1, end: A('b10') - .2,
};
ev(.05, 'stamp', { v: 1.2 }); ev(T.head, 'print'); ev(T.title, 'print', { v: 1.1 });
ev(T.track1, 'whoosh', { d: 1 }); ev(T.mon, 'stamp', { v: 1 });
ev(T.track2, 'whoosh', { d: 1 }); for (let k = 0; k < 6; k++) ev(T.boxes + k * .18, 'stamp', { v: .6, pan: (k % 3 - 1) * .5 });
const OPEN = [0, 1, 2, 3, 4, 5].map(k => T.open0 + k * .55 * Math.pow(.86, k)); OPEN.forEach((t, k) => ev(t, 'flip', { v: .9, note: k * 4 }));
ev(T.flip6, 'flip', { v: 1.3 }); ev(T.like, 'stamp', { v: 1.1 });
ev(T.track7, 'whoosh', { d: 1 }); ev(T.tag, 'stamp', { v: 1.2 });
ev(T.track8, 'whoosh', { d: 1 }); ev(T.chart + 1.3, 'stamp', { v: 1.2 });
ev(T.flip9, 'flip', { v: 1.1 }); ev(T.end + .6, 'print');

// ---------------------------------------------------------------- posters
function poster1(t) {
  const [x, y] = P1;
  ground(x, y, C.sky);
  for (let k = 0; k < 6; k++) { const ts = .15 + k * .12; if (t < ts) continue; person(x + 380 - k * 150, y + 50 - k * 25, (.62 - k * .05) * stampS(t, ts), [C.blue, C.red, C.purple, C.green, C.orange, C.blue][k]); }
  monster(x - 210, y + 120, .9 * (stampS(t, 0) || 1), C.peach, { blush: true });
  printType('GHANTON KI', x, y - 560, { size: 160, ink: C.yellow, dropInk: C.red }, seg(t, T.head, T.head + .6));
  printType('LINE', x, y - 390, { size: 200, ink: C.paper, dropInk: C.blue }, seg(t, T.head + .4, T.head + 1));
  if (t >= T.title) {
    const e = eo(seg(t, T.title, T.title + .45));
    pop.fill(rectPts(x - 800 + (1 - e) * -900, y - 820, 1600, 680), C.yellow);
    printType('LABUBU', x, y - 470, { size: 260, ink: C.red, dropInk: C.blue, letter: 8 }, seg(t, T.title + .15, T.title + .75));
  }
}
function poster2(t) {   // the artist's book
  const [x, y] = P2;
  ground(x, y, C.yellow);
  printType('EK KITAAB', x, y - 430, { size: 190, ink: C.red, dropInk: C.blue }, seg(t, T.track1 + .5, T.track1 + 1.1));
  book(x, y + 160, .95);
  if (t >= T.mon) monster(x - 200, y + 170, .78 * stampS(t, T.mon), C.peach);
}
const MON = [C.peach, C.pink, C.sky, C.mint, C.lilac, C.lemon];
function poster3(t) {   // blind boxes
  const [x, y] = P3;
  ground(x, y, C.red);
  printType('BLIND BOX', x, y - 430, { size: 190, ink: C.yellow, dropInk: C.blue }, seg(t, T.track2 + .5, T.track2 + 1.1));
  for (let k = 0; k < 6; k++) {
    const ts = T.boxes + k * .18; if (t < ts) continue;
    const bx = x - 300 + (k % 3) * 300, by = y - 60 + Math.floor(k / 3) * 350;
    const fu = seg(t, OPEN[k] - .15, OPEN[k] + .15), sx = Math.abs(Math.cos(Math.PI * fu));
    pop.save(); pop.translate(bx, by); pop.scale(Math.max(.02, sx) * stampS(t, ts) * .72, stampS(t, ts) * .72);
    if (fu < .5) box(0, 0, 1, PASTEL[(k * 2) % 6]); else { shape(rr(-170, -200, 340, 400, 30), C.paper, 6); monster(0, 20, .72, MON[k]); }
    pop.restore();
  }
}
function poster4(t) {   // a famous post
  const [x, y] = P3;
  ground(x, y, C.pink);
  printType('1 PHOTO', x, y - 430, { size: 200, ink: C.yellow, dropInk: C.blue }, seg(t, T.flip6 + .3, T.flip6 + .9));
  phone(x, y + 180, .92, C.sky, { icon: () => monster(0, -80, .65, C.peach, { blush: true }) });
  if (t >= T.like) { pop.save(); pop.translate(x + 290, y - 130); pop.scale(stampS(t, T.like, .25)); burst(0, 0, 160, C.red, 16); heart(0, 6, 1.6, C.paper); pop.restore(); }
}
function poster5(t) {   // the luxury bag and the resale tag
  const [x, y] = P4;
  ground(x, y, C.yellow);
  printType('BAG CHARM', x, y - 430, { size: 190, ink: C.red, dropInk: C.blue }, seg(t, T.track7 + .5, T.track7 + 1.1));
  bag(x, y + 170, .8, C.dark);
  const sw = Math.sin((t - T.track7) * 2.6) * .18;
  pop.save(); pop.translate(x + 200, y + 10); pop.rotate(sw); pop.line([[0, 0], [0, 130]], 8, { taper: 0 }); monster(0, 230, .42, C.pink); pop.restore();
  if (t >= T.tag) {
    pop.save(); pop.translate(x - 250, y - 120); pop.scale(stampS(t, T.tag, .25)); pop.rotate(-.18);
    shape([[-150, -70], [120, -70], [180, 0], [120, 70], [-150, 70], [-150, -70]], C.paper, 7); shape(circ(130, 0, 14), C.yellow, 4);
    pop.text('RESALE', -20, -26, { size: 46, font: 'Archivo' }); pop.text('KAI GUNA', -20, 32, { size: 50, fill: C.red });
    pop.restore();
  }
}
function poster6(t) {   // the company soars
  const [x, y] = P6;
  ground(x, y, C.paper);
  pop.fill(rectPts(x - 800, y + 330, 1600, 1000), C.blue);
  printType('POP MART', x, y - 470, { size: 190, ink: C.red, dropInk: C.blue }, seg(t, T.track8 + .5, T.track8 + 1.1));
  const hs = [90, 150, 260, 420, 640];
  hs.forEach((h, k) => { const g = eo(seg(t, T.chart + k * .18, T.chart + .5 + k * .18)); shape(rectPts(x - 400 + k * 170, y + 330 - h * g, 120, h * g + 1), [C.sky, C.sky, C.yellow, C.yellow, C.red][k], 6); });
  if (t >= T.chart + 1.3) { pop.save(); pop.translate(x - 80, y - 60); pop.scale(stampS(t, T.chart + 1.3)); pop.rotate(-.55); arrow(-260, 0, 520, C.red, 1.6); pop.restore(); }
}
function poster7(t) {   // lesson + end: closed boxes, one opened
  const TS7 = 500;
  for (let j = -4; j <= 4; j++) for (let i = -3; i <= 3; i++) {
    const odd = i === 0 && j === 0;
    const s = stampS(t, T.flip9 + .1 + (Math.abs(i) + Math.abs(j)) * .06); if (!s) continue;
    pop.save(); pop.translate(P6[0] + i * TS7, P6[1] + 3000 + j * TS7); pop.scale(s);
    pop.fill(rectPts(-TS7 / 2, -TS7 / 2, TS7, TS7), odd ? C.yellow : C.sky); pop.outline(rectPts(-TS7 / 2, -TS7 / 2, TS7, TS7), 6, { closed: true });
    if (odd) monster(0, 20, .75, C.pink, { blush: true }); else box(0, 0, .82, PASTEL[Math.floor(h2(i, j, 4) * 6)]);
    pop.restore();
  }
}

// ---------------------------------------------------------------- camera + shots
function render(t) {
  frame(t, (t) => {
    let cam;
    if (t < T.track1) cam = { x: 0, y: 0, zoom: lerp(1, 1.04, ss(seg(t, 0, T.track1))) };
    else if (t < T.track2) { const u = eio(seg(t, T.track1, T.track1 + 1.1)); cam = { x: lerp(P1[0], P2[0], u), y: 0, zoom: lerp(1.04, 1, u) - .22 * Math.sin(Math.PI * u) }; }
    else if (t < T.track7) { const u = eio(seg(t, T.track2, T.track2 + 1.1)); cam = { x: lerp(P2[0], P3[0], u), y: 0, zoom: 1 - .22 * Math.sin(Math.PI * u) + .05 * ss(seg(t, T.like - 1, T.like + 1)) }; }
    else if (t < T.track8) { const u = eio(seg(t, T.track7, T.track7 + 1.1)); cam = { x: lerp(P3[0], P4[0], u), y: 0, zoom: 1 - .22 * Math.sin(Math.PI * u) }; }
    else if (t < T.flip9) { const u = eio(seg(t, T.track8, T.track8 + 1.1)); cam = { x: lerp(P4[0], P6[0], u), y: 0, zoom: 1 - .22 * Math.sin(Math.PI * u) + .04 * ss(seg(t, T.chart, T.flip9)) }; }
    else cam = { x: P6[0], y: P6[1] + 3000, zoom: lerp(.7, .56, ss(seg(t, T.flip9, DUR))) };
    let sx = 1; for (const tf of [T.flip6, T.flip9]) if (Math.abs(t - tf) < .18) sx = flipX(t, tf);
    pop.begin(cam, { desk: null });
    pop.save(); pop.translate(cam.x, cam.y); pop.scale(Math.max(sx, .02), 1); pop.translate(-cam.x, -cam.y);
    if (t < T.flip9) {
      if (t < T.track2 + 1.2) { poster1(t); poster2(t); }
      if (t >= T.track2 - .1 && t < T.flip6) poster3(t);
      if (t >= T.flip6 && t < T.track7 + 1.2) poster4(t);
      if (t >= T.track7 - .1 && t < T.track8 + 1.2) poster5(t);
      if (t >= T.track8 - .1) poster6(t);
    } else poster7(t);
    pop.restore();
  }, (t) => {
    slip(t, A('b03') + .3, B('b03'), 70, 165, [{ s: 'KASING LUNG', size: 66 }, { s: 'ARTIST · KITAAB KE LIYE', size: 40, ink: C.red }], { shadowInk: C.blue });
    slip(t, A('b04') + .4, B('b04') + .1, 70, 165, [{ s: 'POP MART', size: 70, ink: C.red }, { s: 'CHINA KI COMPANY', size: 44 }], { shadowInk: C.yellow });
    slip(t, A('b05') + .3, B('b05') + .1, 70, 165, [{ s: 'KAUN SA MILEGA?', size: 60 }, { s: 'BAAR BAAR KHAREEDO', size: 44, ink: C.red }], { shadowInk: C.blue });
    slip(t, A('b06') + .3, B('b06') + .1, 70, 165, [{ s: 'BLACKPINK KI LISA', size: 58, ink: C.blue }], { shadowInk: C.red });
    slip(t, A('b07') + .3, T.tag - .1, 70, 165, [{ s: 'MEHENGE BAGS PAR', size: 58 }], { shadowInk: C.blue });
    slip(t, T.tag + .2, B('b07') + .2, 70, 165, [{ s: 'LINES, JHAGDE', size: 62, ink: C.red }], { shadowInk: C.yellow });
    slip(t, A('b08') + .2, B('b08') + .2, 70, 165, [{ s: 'KAMAI + SHARE', size: 62 }, { s: 'AASMAAN PAR', size: 52, ink: C.red }], { shadowInk: C.blue });
    if (t >= T.flip9 && t < T.end + .3) {
      const o = t > T.end ? -ei(seg(t, T.end, T.end + .3)) * 1200 : 0;
      printType('SABAK', W / 2 + o, 420, { size: 240, ink: C.yellow, dropInk: C.blue }, seg(t, T.flip9 + .3, T.flip9 + .9));
    }
    if (t >= T.end) {
      printType('FOLLOW', W / 2, 520, { size: 250, ink: C.yellow, dropInk: C.blue }, seg(t, T.end + .3, T.end + .9));
      if (HANDLE) label(W / 2, 680, [{ s: HANDLE, size: 62, font: 'Archivo' }], seg(t, T.end + .7, T.end + 1.2), { align: 'center', rot: -.02, shadowInk: C.blue });
      label(W / 2, 1640, [{ s: 'AGLI KAHANI JALD', size: 48 }], seg(t, T.end + 1.4, T.end + 1.9), { align: 'center', rot: .02 });
    } else handleSlip(t, HANDLE);
  });
}
install(render, DUR);
