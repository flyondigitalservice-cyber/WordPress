// RHODE — "Kam products, ek yaadgaar cheez". Pop Art brand reel, 1080×1920. render(t) is pure.
import {
  pop, W, H, C, PASTEL, TL, LN, loadTimeline, ev, h2, rr, circ, rectPts, ellipsePts, shape, burst, arrow, front,
  printType, label, slip, handleSlip, frame, install, clamp, lerp, seg, ss, eio, eo, ei, back,
} from './kit/kit.js';
import { HANDLE } from './kit/handle.js';

await loadTimeline();
const A = id => LN[id].t0, B = id => LN[id].t1;
const DUR = TL.dur;
const P1 = [0, 0], P2 = [1400, 0], P3 = [2800, 0], P4 = [4200, 0], P8 = [5600, 0];
const ground = (x, y, ink, w = 1600, h = 2600) => pop.fill(rectPts(x - w / 2, y - h / 2, w, h), ink);
const stampS = (t, t0, d = .22) => t < t0 ? 0 : 1 + .14 * (1 - eo(seg(t, t0, t0 + d)));
const flipX = (t, tf, d = .36) => Math.abs(Math.cos(Math.PI * seg(t, tf - d / 2, tf + d / 2)));

// ---------------------------------------------------------------- props (generic stand-ins)
function tube(x, y, s = 1, ink = C.pink) {      // a serum squeeze tube, cap down
  pop.save(); pop.translate(x, y); pop.scale(s);
  shape([[-90, -260], [90, -260], [70, 150], [-70, 150], [-90, -260]], ink, 7);
  pop.fill(rectPts(-90, -268, 180, 26), C.paper); pop.outline(rectPts(-90, -268, 180, 26), 5, { closed: true });
  shape(rr(-74, 150, 148, 110, 14), C.paper, 6);
  pop.knock(rr(-60, -220, 16, 320, 8));
  pop.restore();
}
function jar(x, y, s = 1, ink = C.peach) {
  pop.save(); pop.translate(x, y); pop.scale(s);
  shape(rr(-140, -60, 280, 200, 30), ink, 7); shape(rr(-150, -130, 300, 80, 20), C.paper, 7); pop.knock(rr(-110, -30, 18, 130, 8));
  pop.restore();
}
function lip(x, y, s = 1, ink = C.hot) {
  pop.save(); pop.translate(x, y); pop.scale(s);
  shape(rr(-40, -150, 80, 200, 30), ink, 6); shape(rr(-44, 40, 88, 120, 14), C.paper, 6); pop.knock(rr(-26, -120, 12, 130, 6));
  pop.restore();
}
function donut(x, y, s = 1) {
  pop.save(); pop.translate(x, y); pop.scale(s);
  const outer = ellipsePts(0, 0, 300, 270, 60), hole = ellipsePts(0, -10, 90, 80, 40);
  shape(outer, C.peach, 9);
  const glaze = []; for (let k = 0; k <= 60; k++) { const a = k / 60 * Math.PI * 2; const r = 250 + 22 * Math.sin(a * 7); glaze.push([Math.cos(a) * r, -18 + Math.sin(a) * r * .88]); }
  shape(glaze, C.pink, 7);
  pop.fill(hole, C.blue); pop.outline(hole, 8, { closed: true });
  pop.knock(pop.ribbon(ellipsePts(0, -10, 200, 175, 20, Math.PI * 1.1, Math.PI * 1.55), 34, { taper: .4 }));
  pop.knock(ellipsePts(140, -120, 26, 16, 16));
  for (let k = 0; k < 14; k++) { const a = k * 2.3, r = 150 + h2(k, 1) * 80; pop.save(); pop.translate(Math.cos(a) * r, -18 + Math.sin(a) * r * .88); pop.rotate(a); pop.fill(rr(-18, -6, 36, 12, 6), [C.yellow, C.sky, C.paper][k % 3]); pop.restore(); }
  pop.restore();
}
function caseBack(x, y, s = 1, ink = C.pink, o = {}) {   // phone case seen from the back, a lip tint slotted in
  pop.save(); pop.translate(x, y); pop.scale(s); pop.rotate(o.rot || 0);
  shape(rr(-190, -380, 380, 760, 56), ink, 9);
  shape(rr(-150, -340, 150, 150, 36), C.dark, 7); shape(circ(-110, -300, 28), C.steel, 4); shape(circ(-40, -230, 28), C.steel, 4);
  const slot = rr(-60, 40, 120, 300, 30); pop.fill(slot, C.dark); pop.outline(slot, 7, { closed: true });
  const e = o.slide ?? 1;
  if (e > 0) { pop.save(); pop.clip(rr(-60, -600, 120, 940, 30)); lip(0, 190 - (1 - e) * 420, .78, o.lipInk || C.hot); pop.restore(); }
  pop.knock(rr(140, -300, 16, 520, 8));
  pop.restore();
}
function star(x, y, r, ink = C.yellow) {
  const p = []; for (let k = 0; k <= 10; k++) { const a = -Math.PI / 2 + k / 10 * Math.PI * 2, rr2 = k % 2 ? r * .45 : r; p.push([x + Math.cos(a) * rr2, y + Math.sin(a) * rr2]); }
  shape(p, ink, 6);
}

// ---------------------------------------------------------------- timings + sound
const T = {
  p3: [.0, .35, .7], head: A('r01') + .3, big: A('r01') + 3.4, title: A('r02') - .05,
  track1: A('r03') - .85, clear: A('r03') + 3.2, three: A('r03') + 4.0,
  track2: A('r04') - .8, stars: A('r04') + .3, dark0: A('r04') + 2.2, dark1: B('r04') + .3,
  flip5: A('r05') - .02, glaze: A('r05') + 2.2,
  track6: A('r06') - .8, slide: A('r06') + 2.1, hit: A('r06') + 3.3,
  wall0: A('r07') + .1, wall1: B('r07') + .1, sold: A('r08') + .3,
  track9: A('r09') - .9, num: A('r09') + 1.6,
  flip10: A('r10') - .1, end: A('r11') - .2,
};
T.p3.forEach(t => ev(t + .05, 'stamp', { v: 1 })); ev(T.head, 'print'); ev(T.big, 'stamp', { v: 1.3 }); ev(T.title, 'print', { v: 1.1 });
ev(T.track1, 'whoosh', { d: 1 }); ev(T.clear, 'flip', { v: .9 }); ev(T.three, 'stamp', { v: 1.1 });
ev(T.track2, 'whoosh', { d: 1 }); [0, 1, 2, 3, 4].forEach(k => ev(T.stars + k * .25, 'stamp', { v: .6, pan: (k - 2) * .3 })); ev(T.dark0, 'crackle', { d: T.dark1 - T.dark0 });
ev(T.flip5, 'flip', { v: 1.3 }); ev(T.glaze, 'print', { v: 1 });
ev(T.track6, 'whoosh', { d: 1 }); ev(T.slide, 'stamp', { v: .8 }); ev(T.hit, 'stamp', { v: 1.3 });
ev(T.track9, 'whoosh', { d: 1.1 }); ev(T.num, 'print', { v: 1.2 }); ev(T.flip10, 'flip', { v: 1.1 }); ev(T.end + .6, 'print');

// ---------------------------------------------------------------- posters
function three(x, y, t, ts, s = 1) {
  if (t >= ts[0]) tube(x - 260, y + 40, .85 * s * (stampS(t, ts[0]) || 1), C.pink);
  if (t >= ts[1]) jar(x, y + 140, .9 * s * stampS(t, ts[1]), C.peach);
  if (t >= ts[2]) lip(x + 260, y + 90, 1.1 * s * stampS(t, ts[2]), C.hot);
}
function poster1(t) {
  const [x, y] = P1;
  ground(x, y, C.yellow);
  three(x, y + 40, t, T.p3);
  printType('3 PRODUCTS', x, y - 470, { size: 180, ink: C.red, dropInk: C.blue }, seg(t, T.head, T.head + .6));
  if (t >= T.big) { pop.save(); pop.translate(x + 250, y - 200); pop.scale(stampS(t, T.big, .25)); pop.rotate(.1); burst(0, 0, 200, C.blue, 18); pop.text('$1B', 0, -40, { size: 110, fill: C.yellow, outline: 4 }); pop.text('DEAL', 0, 60, { size: 80, fill: C.paper, outline: 3 }); pop.restore(); }
  if (t >= T.title) {
    const e = eo(seg(t, T.title, T.title + .45));
    pop.fill(rectPts(x - 800 + (1 - e) * -900, y - 820, 1600, 680), C.pink);
    printType('RHODE', x, y - 470, { size: 290, ink: C.paper, dropInk: C.red, letter: 10 }, seg(t, T.title + .15, T.title + .75));
  }
}
function poster2(t) {   // fifty products → three
  const [x, y] = P2;
  ground(x, y, C.sky);
  printType(t < T.three ? '50 PRODUCTS?' : 'SIRF 3', x, y - 430, { size: t < T.three ? 160 : 220, ink: C.yellow, dropInk: C.red }, t < T.three ? seg(t, T.track1 + .5, T.track1 + 1.1) : seg(t, T.three, T.three + .5));
  if (t < T.three) {
    for (let j = 0; j < 7; j++) for (let i = 0; i < 7; i++) {
      const tc = T.clear + (i + j) * .035, sx = t < tc ? 1 : Math.abs(Math.cos(Math.PI * seg(t, tc, tc + .3))) * (t < tc + .15 ? 1 : 0);
      if (sx <= .01) continue;
      pop.save(); pop.translate(x - 390 + i * 130, y - 220 + j * 120); pop.scale(sx, 1);
      [() => tube(0, 0, .2, PASTEL[(i + j) % 6]), () => jar(0, 0, .22, PASTEL[(i * 2 + j) % 6]), () => lip(0, 0, .3, PASTEL[(i + 2 * j) % 6])][(i * 3 + j) % 3]();
      pop.restore();
    }
  } else three(x, y + 40, t, [T.three, T.three + .2, T.three + .4]);
}
function poster3(t) {   // celebrity brands flop
  const [x, y] = P3;
  ground(x, y, C.cream);
  printType('FLOP!', x, y - 430, { size: 230, ink: C.red, dropInk: C.blue }, seg(t, T.track2 + .5, T.track2 + 1.1));
  for (let k = 0; k < 5; k++) {
    const ts = T.stars + k * .25; if (t < ts) continue;
    const cx = x - 340 + (k % 3) * 340 + (k > 2 ? 170 : 0), cy = y + (k > 2 ? 380 : 40);
    pop.save(); pop.translate(cx, cy); pop.scale(stampS(t, ts)); star(0, -40, 110, C.yellow); pop.rotate(Math.PI / 2); arrow(30, 0, 150, C.red, .8); pop.restore();
  }
  if (t > T.dark0) front(x - 800, y - 1300, x + 800, y + 1300, seg(t, T.dark0, T.dark1) * 1.3, C.dark, { max: .55 });
}
function poster4(t) {   // glazed donut skin
  const [x, y] = P3;
  ground(x, y, C.blue);
  printType('GLAZED', x, y - 480, { size: 160, ink: C.yellow, dropInk: C.red, letter: 4 }, seg(t, T.flip5 + .3, T.flip5 + .9));
  printType('DONUT SKIN', x, y - 320, { size: 140, ink: C.pink, dropInk: C.red }, seg(t, T.glaze - .6, T.glaze));
  donut(x, y + 230, .85 * (1 + .03 * Math.sin((t - T.flip5) * 2)));
}
function poster5(t) {   // the phone case
  const [x, y] = P4;
  ground(x, y, C.yellow);
  printType('PHONE CASE', x, y - 470, { size: 170, ink: C.red, dropInk: C.blue }, seg(t, T.track6 + .5, T.track6 + 1.1));
  caseBack(x, y + 150, .72, C.pink, { slide: eo(seg(t, T.slide - .5, T.slide)) });
  if (t >= T.hit) { pop.save(); pop.translate(x + 290, y - 150); pop.scale(stampS(t, T.hit, .25)); pop.rotate(.14); burst(0, 0, 160, C.red, 16); pop.text('ASLI', 0, -36, { size: 76, fill: C.yellow, outline: 3 }); pop.text('HIT!', 0, 46, { size: 76, fill: C.yellow, outline: 3 }); pop.restore(); }
}
// copies: a wall of cases; then sold-out stamps
const TS = 500;
const ORDER = []; for (let j = -5; j <= 5; j++) for (let i = -4; i <= 4; i++) if (i || j) ORDER.push([i, j]);
ORDER.sort((a, b) => (Math.max(Math.abs(a[0]), Math.abs(a[1])) - Math.max(Math.abs(b[0]), Math.abs(b[1]))) || (Math.atan2(a[1], a[0]) - Math.atan2(b[1], b[0])));
const STAMP = new Map();
ORDER.forEach(([i, j], n) => { const ta = T.wall0 + .3 + (T.wall1 - T.wall0 - .3) * Math.pow((n + 1) / ORDER.length, .55); STAMP.set(i + ',' + j, ta); if (n < 24 || n % 4 == 0) ev(ta, 'stamp', { pan: clamp(i / 4, -1, 1) * .6, v: n < 8 ? .8 : .45 }); });
const stampT = (i, j) => (i || j) ? STAMP.get(i + ',' + j) ?? 1e9 : -1;
const SOLD = []; for (let n = 0; n < 9; n++) { const i = Math.round((h2(n, 9) - .5) * 6), j = Math.round((h2(n, 8) - .5) * 6); SOLD.push([i, j, T.sold + n * .33]); ev(T.sold + n * .33, 'stamp', { v: .9, pan: i / 4 }); }
function wall(t) {
  const [x, y] = P4;
  for (let j = -5; j <= 5; j++) for (let i = -4; i <= 4; i++) {
    if (!i && !j) continue;
    const s = stampS(t, stampT(i, j)); if (!s) continue;
    pop.save(); pop.translate(x + i * TS, y + 150 + j * TS); pop.scale(s);
    pop.fill(rectPts(-TS / 2, -TS / 2, TS, TS), [C.yellow, C.sky, C.cream][Math.floor(h2(i, j, 2) * 3)]); pop.outline(rectPts(-TS / 2, -TS / 2, TS, TS), 6, { closed: true });
    caseBack(0, 0, .55, PASTEL[Math.floor(h2(i, j, 4) * 6)], { rot: (h2(i, j, 1) - .5) * .2, lipInk: [C.hot, C.red, C.peach][Math.floor(h2(i, j, 3) * 3)] });
    pop.restore();
  }
  for (const [i, j, ts] of SOLD) { if (t < ts) continue; pop.save(); pop.translate(x + i * TS, y + 150 + j * TS); pop.scale(stampS(t, ts) * 1.1); pop.rotate(-.15 + h2(i, j, 7) * .3); burst(0, 0, 170, C.red, 14); pop.text('SOLD', 0, -34, { size: 74, fill: C.yellow, outline: 3 }); pop.text('OUT', 0, 44, { size: 74, fill: C.yellow, outline: 3 }); pop.restore(); }
}
function poster9(t) {   // the number
  const [x, y] = P8;
  ground(x, y, C.pink);
  printType('e.l.f. DEAL', x, y - 520, { size: 150, ink: C.paper, dropInk: C.blue }, seg(t, T.track9 + .6, T.track9 + 1.2));
  printType('$1B', x, y - 250, { size: 340, ink: C.yellow, dropInk: C.red, letter: 4 }, seg(t, T.num, T.num + .7));
  three(x, y + 140, t, [-1, -1, -1], .8);
}
function poster10(t) {   // lesson + end: crowded shelves, one case
  const TS7 = 500;
  for (let j = -4; j <= 4; j++) for (let i = -3; i <= 3; i++) {
    const odd = i === 0 && j === 0;
    const s = stampS(t, T.flip10 + .1 + (Math.abs(i) + Math.abs(j)) * .06); if (!s) continue;
    pop.save(); pop.translate(P8[0] + i * TS7, P8[1] + 3000 + j * TS7); pop.scale(s);
    pop.fill(rectPts(-TS7 / 2, -TS7 / 2, TS7, TS7), odd ? C.yellow : C.cream); pop.outline(rectPts(-TS7 / 2, -TS7 / 2, TS7, TS7), 6, { closed: true });
    if (odd) caseBack(0, 10, .55, C.pink);
    else for (let q = 0; q < 6; q++) { const qx = -150 + (q % 3) * 150, qy = -80 + Math.floor(q / 3) * 170; [() => tube(qx, qy, .25, C.steel), () => jar(qx, qy, .3, C.steel), () => lip(qx, qy, .4, C.steel)][(q + i + j + 9) % 3](); }
    pop.restore();
  }
}

// ---------------------------------------------------------------- camera + shots
function render(t) {
  frame(t, (t) => {
    let cam;
    if (t < T.track1) cam = { x: 0, y: 0, zoom: lerp(1, 1.04, ss(seg(t, 0, T.track1))) };
    else if (t < T.track2) { const u = eio(seg(t, T.track1, T.track1 + 1.1)); cam = { x: lerp(P1[0], P2[0], u), y: 0, zoom: lerp(1.04, 1, u) - .22 * Math.sin(Math.PI * u) }; }
    else if (t < T.track6) { const u = eio(seg(t, T.track2, T.track2 + 1.1)); const pz = ss(seg(t, A('r04'), T.dark1)) * (t < T.flip5 ? 1 : 0); cam = { x: lerp(P2[0], P3[0], u), y: 0, zoom: 1 - .22 * Math.sin(Math.PI * u) + .08 * pz }; }
    else if (t < T.track9) { const u = eio(seg(t, T.track6, T.track6 + 1.1)), v = eio(seg(t, T.wall0, T.wall1 + .6)); cam = { x: lerp(P3[0], P4[0], u), y: 0, zoom: 1 - .22 * Math.sin(Math.PI * u) - .62 * v + .1 * ss(seg(t, T.sold, B('r08'))), rot: .03 * v }; }
    else if (t < T.flip10) { const u = eio(seg(t, T.track9, T.track9 + 1.3)); cam = { x: lerp(P4[0], P8[0], u), y: 0, zoom: lerp(.48, 1, u) - .12 * Math.sin(Math.PI * u) + .05 * ss(seg(t, T.num, T.flip10)), rot: lerp(.03, 0, u) }; }
    else cam = { x: P8[0], y: P8[1] + 3000, zoom: lerp(.7, .56, ss(seg(t, T.flip10, DUR))) };
    let sx = 1; for (const tf of [T.flip5, T.flip10]) if (Math.abs(t - tf) < .18) sx = flipX(t, tf);
    pop.begin(cam, { desk: null });
    pop.save(); pop.translate(cam.x, cam.y); pop.scale(Math.max(sx, .02), 1); pop.translate(-cam.x, -cam.y);
    if (t < T.flip10) {
      if (t < T.track2 + 1.2) { poster1(t); poster2(t); }
      if (t >= T.track2 - .1 && t < T.flip5) poster3(t);
      if (t >= T.flip5 && t < T.track6 + 1.2) poster4(t);
      if (t >= T.track6 - .1 && t < T.track9 + 1.4) { poster5(t); if (t >= T.wall0) wall(t); }
      if (t >= T.track9 - .1) poster9(t);
    } else poster10(t);
    pop.restore();
  }, (t) => {
    slip(t, A('r03') + .4, T.three - .2, 70, 165, [{ s: 'HAILEY BIEBER', size: 64 }, { s: 'SKINCARE LAUNCH', size: 44, ink: C.red }], { shadowInk: C.yellow });
    slip(t, A('r04') + .3, B('r04'), 70, 165, [{ s: 'CELEBRITY BRANDS', size: 58 }, { s: 'EK KE BAAD EK FLOP', size: 42, ink: C.red }], { shadowInk: C.blue });
    slip(t, A('r05') + .3, B('r05') + .2, 70, 165, [{ s: 'CREAM NAHI,', size: 58 }, { s: 'EK LOOK BECHA', size: 54, ink: C.red }], { shadowInk: C.yellow });
    slip(t, A('r07') + .2, B('r07') + .2, 70, 165, [{ s: 'HAR PHOTO =', size: 58 }, { s: 'FREE AD', size: 70, ink: C.red }], { shadowInk: C.blue });
    slip(t, A('r08') + .2, B('r08') + .2, 70, 165, [{ s: 'KAM STOCK', size: 62 }, { s: 'CHHOTE DROPS', size: 50, ink: C.blue }], { shadowInk: C.red });
    slip(t, A('r09') + 2.2, B('r09') + .2, 70, 165, [{ s: '3 SAAL MEIN', size: 62 }, { s: 'UP TO $1B (2025)', size: 44, ink: C.red }], { shadowInk: C.yellow });
    if (t >= T.flip10 && t < T.end + .3) {
      const o = t > T.end ? -ei(seg(t, T.end, T.end + .3)) * 1200 : 0;
      printType('SABAK', W / 2 + o, 420, { size: 240, ink: C.yellow, dropInk: C.blue }, seg(t, T.flip10 + .3, T.flip10 + .9));
    }
    if (t >= T.end) {
      printType('FOLLOW', W / 2, 520, { size: 250, ink: C.yellow, dropInk: C.blue }, seg(t, T.end + .3, T.end + .9));
      if (HANDLE) label(W / 2, 680, [{ s: HANDLE, size: 62, font: 'Archivo' }], seg(t, T.end + .7, T.end + 1.2), { align: 'center', rot: -.02, shadowInk: C.blue });
      label(W / 2, 1640, [{ s: 'AGLI KAHANI: LABUBU', size: 48 }], seg(t, T.end + 1.4, T.end + 1.9), { align: 'center', rot: .02 });
    } else handleSlip(t, HANDLE);
  });
}
install(render, DUR);
