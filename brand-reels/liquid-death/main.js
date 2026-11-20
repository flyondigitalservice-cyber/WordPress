// LIQUID DEATH — "Boring category, loud brand". Pop Art brand reel, 1080×1920. render(t) is pure.
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

// ---------------------------------------------------------------- props (generic stand-ins)
function drop(x, y, s = 1, ink = C.sky, o = {}) {
  pop.save(); pop.translate(x, y); pop.scale(s);
  const p = []; for (let k = 0; k <= 40; k++) { const a = -Math.PI / 2 + k / 40 * Math.PI * 2; const r = 1 - .55 * Math.pow(Math.max(0, Math.cos((a + Math.PI / 2) / 2)), 8); p.push([Math.cos(a) * 150 * r, 40 + Math.sin(a) * 170 * (a < Math.PI / 2 && a > -Math.PI / 2 ? 1 : 1)]); }
  const d = [[0, -220]]; for (let k = 0; k <= 30; k++) { const a = -.15 * Math.PI + k / 30 * 1.3 * Math.PI; d.push([Math.cos(a) * 150, 50 + Math.sin(a) * 150]); } d.push([0, -220]);
  shape(d, ink, 8); pop.knock(ellipsePts(-60, 20, 22, 44, 20));
  if (o.xeyes) for (const ex of [-50, 50]) { pop.line([[ex - 20, 40], [ex + 20, 80]], 9, { taper: 0 }); pop.line([[ex + 20, 40], [ex - 20, 80]], 9, { taper: 0 }); }
  pop.restore();
}
function tallCan(x, y, s = 1, ink = C.dark, o = {}) {
  pop.save(); pop.translate(x, y); pop.scale(s); pop.rotate(o.rot || 0);
  const body = [[-105, -270], [105, -270], [105, 270], [-105, 270], [-105, -270]];
  pop.fill(body, ink);
  pop.fill(rectPts(-105, -120, 210, 220), o.band || C.paper);
  drop(0, -20, .36, o.dropInk || C.sky, { xeyes: true });
  pop.knock(rectPts(-84, -250, 16, 120)); pop.knock(rectPts(-84, 120, 16, 130));
  pop.outline(body, 8, { closed: true });
  shape(ellipsePts(0, -280, 105, 26, 40), C.steel, 7); shape(rr(-28, -292, 56, 20, 9), C.paper, 4);
  shape(ellipsePts(0, 274, 98, 18, 40), C.steel, 6);
  pop.restore();
}
function eCan(x, y, s = 1) {   // an energy-drink can with a bolt
  pop.save(); pop.translate(x, y); pop.scale(s);
  shape(rectPts(-90, -200, 180, 400), C.green, 8);
  shape([[10, -150], [-50, 20], [0, 20], [-20, 150], [50, -30], [0, -30], [30, -150], [10, -150]], C.yellow, 6);
  shape(ellipsePts(0, -205, 90, 22, 30), C.steel, 6);
  pop.restore();
}
function waterBottle(x, y, s = 1) {
  pop.save(); pop.translate(x, y); pop.scale(s);
  shape([[-40, -260], [40, -260], [40, -200], [100, -130], [100, 250], [-100, 250], [-100, -130], [-40, -200], [-40, -260]], C.ice, 7);
  pop.fill(rectPts(-100, -20, 200, 110), C.sky); pop.outline(rectPts(-100, -20, 200, 110), 5, { closed: true });
  shape(rr(-46, -300, 92, 44, 10), C.blue, 5); pop.knock(rr(-80, -110, 16, 320, 8));
  pop.restore();
}
function stage(x, y, s = 1) {
  pop.save(); pop.translate(x, y); pop.scale(s);
  for (const k of [-1, 1]) shape([[k * 60, -520], [k * 300, 120], [k * 140, 120]], C.lemon, 5);
  shape(rectPts(-460, 120, 920, 120), C.red, 8);
  for (const k of [-1, 1]) { shape(rectPts(k * 340 - 70, -160, 140, 280), C.dark, 7); shape(circ(k * 340, -80, 40), C.steel, 5); shape(circ(k * 340, 40, 54), C.steel, 5); }
  pop.restore();
}

// ---------------------------------------------------------------- timings + sound
const T = {
  head: A('l01') + .2, big: A('l01') + 3.3, title: A('l02') - .05,
  track1: A('l03') - .85, ecan: A('l04') + .3, inside: A('l04') + 2.2,
  track2: A('l05') - .8, tall: A('l05') + 2.4, slogan: A('l06') + .25,
  track3: A('l07') - .7, ha: [A('l07') + .3, A('l07') + .7, A('l07') + 1.1], dark0: A('l07') + 1.6, dark1: B('l07') + .3,
  flip8: A('l08') - .02, views: A('l08') + .3,
  wall0: A('l09') - .2, wall1: B('l09') - .2,
  track10: A('l10') - .9, num: A('l10') + 1.2,
  flip11: A('l11') - .1, end: A('l12') - .2,
};
ev(.05, 'stamp', { v: 1.2 }); ev(T.head, 'print'); ev(T.big, 'stamp', { v: 1.3 }); ev(T.title, 'print', { v: 1.1 });
ev(T.track1, 'whoosh', { d: 1 }); ev(T.ecan, 'stamp'); ev(T.inside, 'stamp', { v: 1 });
ev(T.track2, 'whoosh', { d: 1 }); ev(T.tall, 'stamp', { v: 1.2 }); ev(T.slogan, 'print', { v: 1.2 });
ev(T.track3, 'whoosh', { d: 1 }); T.ha.forEach((t, k) => ev(t, 'stamp', { v: .8, pan: k - 1 })); ev(T.dark0, 'crackle', { d: T.dark1 - T.dark0 });
ev(T.flip8, 'flip', { v: 1.3 }); ev(T.track10, 'whoosh', { d: 1.1 }); ev(T.num, 'print', { v: 1.2 });
ev(T.flip11, 'flip', { v: 1.1 }); ev(T.end + .6, 'print');

// ---------------------------------------------------------------- posters
function poster1(t) {
  const [x, y] = P1;
  ground(x, y, C.yellow);
  drop(x, y + 170, 1.25 * (stampS(t, 0) || 1), C.sky);
  printType('SIRF PAANI', x, y - 470, { size: 190, ink: C.blue, dropInk: C.red }, seg(t, T.head, T.head + .6));
  if (t >= T.big) { pop.save(); pop.translate(x + 280, y - 190); pop.scale(stampS(t, T.big, .25)); pop.rotate(.1); burst(0, 0, 190, C.red, 18); pop.text('$1.4B', 0, 4, { size: 110, fill: C.yellow, outline: 4 }); pop.restore(); }
  if (t >= T.title) {
    const e = eo(seg(t, T.title, T.title + .45));
    pop.fill(rectPts(x - 800 + (1 - e) * -900, y - 820, 1600, 780), C.dark);
    printType('LIQUID', x, y - 560, { size: 220, ink: C.paper, dropInk: C.red, letter: 6 }, seg(t, T.title + .15, T.title + .75));
    printType('DEATH', x, y - 350, { size: 220, ink: C.paper, dropInk: C.blue, letter: 6 }, seg(t, T.title + .3, T.title + .9));
  }
}
function poster2(t) {
  const [x, y] = P2;
  ground(x, y, C.blue);
  printType('FESTIVAL', x, y - 430, { size: 200, ink: C.yellow, dropInk: C.red }, seg(t, T.track1 + .5, T.track1 + 1.1));
  stage(x, y + 120, .9);
  if (t >= T.ecan) eCan(x, y + 130, .8 * stampS(t, T.ecan));
  if (t >= T.inside) { pop.save(); pop.translate(x + 250, y - 120); pop.scale(stampS(t, T.inside)); pop.rotate(.12); burst(0, 0, 170, C.paper, 14); pop.text('ANDAR', 0, -38, { size: 70, fill: C.blue, outline: 3 }); pop.text('PAANI!', 0, 40, { size: 70, fill: C.blue, outline: 3 }); pop.restore(); }
}
function poster3(t) {
  const [x, y] = P3;
  ground(x, y, C.red);
  if (t < T.slogan) printType('IDEA', x, y - 430, { size: 230, ink: C.yellow, dropInk: C.blue }, seg(t, T.track2 + .5, T.track2 + 1.1));
  else { printType('MURDER YOUR', x, y - 520, { size: 150, ink: C.yellow, dropInk: C.dark }, seg(t, T.slogan, T.slogan + .5)); printType('THIRST', x, y - 330, { size: 190, ink: C.paper, dropInk: C.dark, letter: 8 }, seg(t, T.slogan + .3, T.slogan + .8)); }
  if (t < T.tall) { waterBottle(x - 200, y + 160, .8); pop.text('→', x + 20, y + 160, { size: 160, fill: C.yellow, outline: 5 }); }
  if (t >= T.tall) tallCan(x, y + 150, 1.05 * stampS(t, T.tall));
  if (t < T.tall && t > T.track2 + 1.4) tallCan(x + 230, y + 160, .6 * stampS(t, T.track2 + 1.4), C.dark);
}
function poster4(t) {
  const [x, y] = P4;
  ground(x, y, C.cream);
  printType('INVESTORS?', x, y - 430, { size: 180, ink: C.red, dropInk: C.blue }, seg(t, T.track3 + .5, T.track3 + 1.1));
  tallCan(x, y + 150, 1.0);
  const pos = [[-300, -120, -.2], [300, -40, .15], [-260, 330, .1]];
  T.ha.forEach((th, k) => { if (t < th) return; pop.save(); pop.translate(x + pos[k][0], y + pos[k][1]); pop.scale(stampS(th === th ? t : t, th) * .8); pop.rotate(pos[k][2]); burst(0, 0, 130, C.yellow, 12); pop.text('HA HA', 0, 4, { size: 64, fill: C.red, outline: 3 }); pop.restore(); });
  if (t > T.dark0) front(x - 800, y - 1300, x + 800, y + 1300, seg(t, T.dark0, T.dark1) * 1.3, C.dark, { max: .55 });
}
function poster5(t) {   // the ad before the product: one video, the views climb
  const [x, y] = P4;
  ground(x, y, C.pink);
  printType('PEHLE AD', x, y - 500, { size: 170, ink: C.yellow, dropInk: C.blue }, seg(t, T.flip8 + .3, T.flip8 + .9));
  phone(x, y + 190, .85, C.dark, { icon: () => { shape([[-50, -160], [70, -90], [-50, -20], [-50, -160]], C.red, 6); } });
  const v = Math.round(Math.pow(10, 3 + 3.3 * eio(seg(t, T.views, B('l08') - .5))) / 1000) * 1000;
  const txt = v.toLocaleString('en-IN') + ' VIEWS';
  pop.save(); pop.translate(x, y - 280); pop.rotate(-.03);
  pop.fill(rectPts(-330, -60, 660, 120), C.yellow); pop.outline(rectPts(-330, -60, 660, 120), 6, { closed: true });
  pop.text(txt, 0, 4, { size: 76, fill: C.red });
  pop.restore();
}
// stunts: a wall of cans in colourways
const TS = 480;
const ORDER = []; for (let j = -5; j <= 5; j++) for (let i = -4; i <= 4; i++) if (i || j) ORDER.push([i, j]);
ORDER.sort((a, b) => (Math.max(Math.abs(a[0]), Math.abs(a[1])) - Math.max(Math.abs(b[0]), Math.abs(b[1]))) || (Math.atan2(a[1], a[0]) - Math.atan2(b[1], b[0])));
const STAMP = new Map();
ORDER.forEach(([i, j], n) => { const ta = T.wall0 + .4 + (T.wall1 - T.wall0 - .4) * Math.pow((n + 1) / ORDER.length, .55); STAMP.set(i + ',' + j, ta); if (n < 24 || n % 4 == 0) ev(ta, 'stamp', { pan: clamp(i / 4, -1, 1) * .6, v: n < 8 ? .8 : .45 }); });
const stampT = (i, j) => (i || j) ? STAMP.get(i + ',' + j) ?? 1e9 : T.wall0;
const CW = [C.dark, C.red, C.blue, C.green, C.purple, C.orange];
function wall(t) {
  const [x, y] = P6;
  ground(x, y, C.yellow);
  for (let j = -5; j <= 5; j++) for (let i = -4; i <= 4; i++) {
    const s = stampS(t, stampT(i, j)); if (!s) continue;
    pop.save(); pop.translate(x + i * TS, y + 100 + j * TS); pop.scale(s);
    pop.fill(rectPts(-TS / 2, -TS / 2, TS, TS), [C.yellow, C.paper, C.sky][Math.floor(h2(i, j, 2) * 3)]); pop.outline(rectPts(-TS / 2, -TS / 2, TS, TS), 6, { closed: true });
    tallCan(0, 10, .7, CW[Math.floor(h2(i, j, 4) * CW.length)], { rot: (h2(i, j, 1) - .5) * .2 });
    pop.restore();
  }
}
function poster9(t) {   // the number
  const [x, y] = P8;
  ground(x, y, C.blue);
  printType('VALUE', x, y - 520, { size: 150, ink: C.paper, dropInk: C.red }, seg(t, T.track10 + .6, T.track10 + 1.2));
  printType('$1.4B', x, y - 260, { size: 310, ink: C.yellow, dropInk: C.red, letter: 2 }, seg(t, T.num, T.num + .7));
  for (let k = 0; k < 5; k++) tallCan(x + (k - 2) * 190, y + 270 + Math.abs(k - 2) * 30, .58, CW[k], { rot: (k - 2) * .06 });
}
function poster10(t) {   // lesson + end: plain water bottles, one loud can
  const TS7 = 500;
  for (let j = -4; j <= 4; j++) for (let i = -3; i <= 3; i++) {
    const odd = i === 0 && j === 0;
    const s = stampS(t, T.flip11 + .1 + (Math.abs(i) + Math.abs(j)) * .06); if (!s) continue;
    pop.save(); pop.translate(P8[0] + i * TS7, P8[1] + 3000 + j * TS7); pop.scale(s);
    pop.fill(rectPts(-TS7 / 2, -TS7 / 2, TS7, TS7), odd ? C.red : C.cream); pop.outline(rectPts(-TS7 / 2, -TS7 / 2, TS7, TS7), 6, { closed: true });
    if (odd) tallCan(0, 10, .72, C.dark); else waterBottle(0, 10, .7);
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
    else if (t < T.wall0) { const u = eio(seg(t, T.track3, T.track3 + 1.1)); const pz = ss(seg(t, A('l07'), T.dark1)) * (t < T.flip8 ? 1 : 0); cam = { x: lerp(P3[0], P4[0], u), y: 0, zoom: 1 - .22 * Math.sin(Math.PI * u) + .08 * pz }; }
    else if (t < T.track10) { const u = eio(seg(t, T.wall0 - .1, T.wall0 + 1.1)), v = eio(seg(t, T.wall0 + .5, T.wall1 + .6)); cam = { x: P4[0], y: lerp(0, P6[1], u), zoom: 1 - .3 * Math.sin(Math.PI * u) - .64 * v, rot: .03 * v }; }
    else if (t < T.flip11) { const u = eio(seg(t, T.track10, T.track10 + 1.3)); cam = { x: lerp(P6[0], P8[0], u), y: P8[1], zoom: lerp(.36, 1, u) + .05 * ss(seg(t, T.num, T.flip11)), rot: lerp(.03, 0, u) }; }
    else cam = { x: P8[0], y: P8[1] + 3000, zoom: lerp(.7, .56, ss(seg(t, T.flip11, DUR))) };
    let sx = 1; for (const tf of [T.flip8, T.flip11]) if (Math.abs(t - tf) < .18) sx = flipX(t, tf);
    pop.begin(cam, { desk: null });
    pop.save(); pop.translate(cam.x, cam.y); pop.scale(Math.max(sx, .02), 1); pop.translate(-cam.x, -cam.y);
    if (t < T.flip11) {
      if (t < T.track2 + 1.2) { poster1(t); poster2(t); }
      if (t >= T.track2 - .1 && t < T.track3 + 1.2) poster3(t);
      if (t >= T.track3 - .1 && t < T.flip8) poster4(t);
      if (t >= T.flip8 && t < T.wall0 + 1.3) poster5(t);
      if (t >= T.wall0 - .1 && t < T.track10 + 1.4) wall(t);
      if (t >= T.track10 - .1) poster9(t);
    } else poster10(t);
    pop.restore();
  }, (t) => {
    slip(t, A('l03') + .4, B('l03'), 70, 165, [{ s: 'MIKE CESSARIO', size: 64 }, { s: 'AD BANATE THE', size: 44, ink: C.red }], { shadowInk: C.yellow });
    slip(t, A('l04') + .3, B('l04'), 70, 165, [{ s: 'COOL DIKHNE', size: 60 }, { s: 'KE LIYE', size: 50, ink: C.red }], { shadowInk: C.yellow });
    slip(t, A('l05') + .3, T.slogan - 1.2, 70, 165, [{ s: 'BEER JAISA CAN', size: 60 }, { s: 'PUNK BAND BRAND', size: 46, ink: C.red }], { shadowInk: C.blue });
    slip(t, T.slogan - .9, B('l06') + .2, 70, 165, [{ s: 'LIQUID DEATH', size: 70, ink: C.red }], { shadowInk: C.yellow });
    slip(t, A('l08') + 1.4, B('l08'), 70, 165, [{ s: 'YE MILEGA KAHAN?', size: 60, ink: C.blue }], { shadowInk: C.red });
    slip(t, A('l09') + .3, B('l09') + .2, 70, 165, [{ s: 'AJEEB STUNTS', size: 66, ink: C.red }, { s: 'HATE COMMENTS = GAANE', size: 40 }], { shadowInk: C.blue });
    slip(t, A('l10') + 1.4, B('l10') + .2, 70, 165, [{ s: 'PAANI KI COMPANY', size: 58 }, { s: '(2024)', size: 44, ink: C.blue }], { shadowInk: C.red });
    if (t >= T.flip11 && t < T.end + .3) {
      const o = t > T.end ? -ei(seg(t, T.end, T.end + .3)) * 1200 : 0;
      printType('SABAK', W / 2 + o, 420, { size: 240, ink: C.yellow, dropInk: C.blue }, seg(t, T.flip11 + .3, T.flip11 + .9));
    }
    if (t >= T.end) {
      printType('FOLLOW', W / 2, 520, { size: 250, ink: C.yellow, dropInk: C.blue }, seg(t, T.end + .3, T.end + .9));
      if (HANDLE) label(W / 2, 680, [{ s: HANDLE, size: 62, font: 'Archivo' }], seg(t, T.end + .7, T.end + 1.2), { align: 'center', rot: -.02, shadowInk: C.blue });
      label(W / 2, 1640, [{ s: 'AGLI KAHANI: RHODE', size: 48 }], seg(t, T.end + 1.4, T.end + 1.9), { align: 'center', rot: .02 });
    } else handleSlip(t, HANDLE);
  });
}
install(render, DUR);
