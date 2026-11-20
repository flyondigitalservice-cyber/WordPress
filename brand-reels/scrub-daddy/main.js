// SCRUB DADDY — "Boring product ko chehra do". Pop Art brand reel, 1080×1920. render(t) is pure.
import {
  pop, W, H, C, PASTEL, TL, LN, loadTimeline, ev, h2, rr, circ, rectPts, ellipsePts, shape, iceCubes, car, heart, burst, arrow, front,
  printType, label, slip, handleSlip, frame, install, clamp, lerp, seg, ss, eio, eo, ei, back,
} from './kit/kit.js';
import { HANDLE } from './kit/handle.js';

await loadTimeline();
const A = id => LN[id].t0, B = id => LN[id].t1;
const DUR = TL.dur;
const P1 = [0, 0], P2 = [1400, 0], P3 = [2800, 0], P4 = [4200, 0], P6 = [4200, 3600];
const ground = (x, y, ink, w = 1600, h = 2600) => pop.fill(rectPts(x - w / 2, y - h / 2, w, h), ink);
const stampS = (t, t0, d = .22) => t < t0 ? 0 : 1 + .14 * (1 - eo(seg(t, t0, t0 + d)));
const flipX = (t, tf, d = .36) => Math.abs(Math.cos(Math.PI * seg(t, tf - d / 2, tf + d / 2)));

// ---------------------------------------------------------------- the sponge (generic, no logo)
// o.face: 0..1 (eyes appear from .0, mouth from .5); o.hole: ink seen through the holes; o.squish: soft (warm) sag
function sponge(x, y, s = 1, ink = C.yellow, o = {}) {
  pop.save(); pop.translate(x, y); pop.scale(s * (1 + (o.squish || 0) * .12), s * (1 - (o.squish || 0) * .16));
  const body = rr(-200, -150, 400, 300, o.hard ? 40 : 120, 10);
  pop.fill(body, ink);
  // scrub texture: a dot band on one side
  pop.over(rr(-200, 70, 400, 80, 30), { r: .35, b: .25 });
  pop.outline(body, 9, { closed: true });
  pop.line([[-196, 70], [196, 70]], 6, { taper: 0 });
  pop.knock(rr(-150, -118, 90, 22, 11));
  const f = o.face || 0, hole = o.hole || C.paper;
  if (f > 0) {
    const e = eo(seg(f, 0, .45)) * 40;
    for (const ex of [-82, 82]) { const p = circ(ex, -40, e, 30); pop.fill(p, hole); pop.outline(p, 6, { closed: true }); }
    const m = eo(seg(f, .5, 1));
    if (m > 0) {
      const pts = []; for (let k = 0; k <= 24; k++) { const a = Math.PI * (.15 + .7 * k / 24); pts.push([Math.cos(a) * -130 * m, -20 + Math.sin(a) * 85]); }
      const mouth = pop.ribbon(pts, 34 * m + 4, { taper: .12, tmin: .5 });
      pop.fill(mouth, hole); pop.outline(mouth, 6, { closed: true });
    }
  }
  pop.restore();
}
function finger(x, y, s = 1, rot = 0) {
  pop.save(); pop.translate(x, y); pop.rotate(rot); pop.scale(s);
  shape(rr(-34, -10, 68, 300, 34), C.peach, 7); shape(rr(-20, 0, 40, 46, 16), C.pink, 4);
  pop.restore();
}
function spoon(x, y, s = 1, rot = 0) {
  pop.save(); pop.translate(x, y); pop.rotate(rot); pop.scale(s);
  shape(rr(-18, 60, 36, 300, 18), C.steel, 6); shape(ellipsePts(0, 0, 62, 92, 40), C.steel, 7); pop.knock(ellipsePts(-18, -24, 14, 34, 20));
  pop.restore();
}
function tv(x, y, s = 1, ink = C.red, screenInk = C.sky) {
  pop.save(); pop.translate(x, y); pop.scale(s);
  pop.line([[-40, -300], [-140, -440]], 9, { taper: 0 }); pop.line([[40, -300], [140, -440]], 9, { taper: 0 });
  shape(rr(-360, -300, 720, 540, 50), ink, 9);
  shape(rr(-310, -250, 520, 440, 40), screenInk, 7);
  shape(circ(285, -150, 30), C.yellow, 6); shape(circ(285, -50, 30), C.yellow, 6);
  shape(rectPts(-260, 240, 50, 80), C.dark, 6); shape(rectPts(210, 240, 50, 80), C.dark, 6);
  pop.restore();
}
function garage(x, y, s = 1) {
  pop.save(); pop.translate(x, y); pop.scale(s);
  shape([[-420, -120], [0, -420], [420, -120], [-420, -120]], C.red, 8);
  shape(rectPts(-380, -120, 760, 560), C.cream, 8);
  shape(rectPts(-300, -40, 600, 480), C.steel, 7);
  for (let k = 1; k < 6; k++) pop.line([[-300, -40 + k * 80], [300, -40 + k * 80]], 5, { taper: 0 });
  pop.restore();
}

// ---------------------------------------------------------------- timings + sound
const T = {
  sp1: 0.0, head: A('d01') + .2, big: A('d01') + 2.4, title: A('d02') - .05,
  track1: A('d03') - .85, pad: A('d03') + .4, foam: A('d03') + 3.6,
  track2: A('d04') - .8, hot: A('d04') + 2.4,
  track3: A('d05') - .8, dust0: A('d05') + 1.5, dust1: B('d05') + .2,
  flip5: A('d06') - .02, eyes: A('d06') + 1.1, mouth: A('d06') + 2.2,
  push7: A('d07') - .3, fing: A('d07') + .2, spn: A('d07') + 2.6,
  track8: A('d08') - .9, deal: A('d08') + 2.6,
  wall0: A('d09') + .3, wall1: B('d09') - .2,
  wave: A('d10') + .3, flip11: A('d11') - .1, end: A('d12') - .2,
};
ev(.05, 'stamp', { v: 1.2 }); ev(T.head, 'print'); ev(T.big, 'stamp', { v: 1.2 }); ev(T.title, 'print', { v: 1.1 });
ev(T.track1, 'whoosh', { d: 1 }); ev(T.pad, 'stamp'); ev(T.foam, 'stamp', { v: 1 });
ev(T.track2, 'whoosh', { d: 1 }); ev(T.track2 + .9, 'stamp'); ev(T.hot, 'stamp');
ev(T.track3, 'whoosh', { d: 1 }); ev(T.dust0, 'crackle', { d: T.dust1 - T.dust0 });
ev(T.flip5, 'flip', { v: 1.3 }); ev(T.eyes, 'pop', { v: 1.2 }); ev(T.eyes + .12, 'pop', { v: 1.2 }); ev(T.mouth, 'stamp', { v: 1 });
ev(T.push7, 'whoosh', { d: 1 }); ev(T.fing, 'pop'); ev(T.spn, 'pop');
ev(T.track8, 'whoosh', { d: 1.1 }); ev(T.track8 + 1, 'print'); ev(T.deal, 'stamp', { v: 1.3 });
ev(T.flip11, 'flip', { v: 1.1 }); ev(T.end + .6, 'print');

// ---------------------------------------------------------------- posters
function poster1(t) {
  const [x, y] = P1;
  ground(x, y, C.blue);
  sponge(x, y + 230, 1.15 * (stampS(t, 0) || 1), C.yellow, { hard: true });
  printType('EK SPONGE', x, y - 520, { size: 180, ink: C.yellow, dropInk: C.red }, seg(t, T.head, T.head + .6));
  if (t >= T.big) { pop.save(); pop.translate(x, y - 230); pop.scale(stampS(t, T.big, .25)); pop.rotate(-.08); burst(0, 0, 250, C.red, 18); pop.text('SAIKDON', 0, -62, { size: 84, fill: C.yellow, outline: 4 }); pop.text('MILLION $', 0, 40, { size: 84, fill: C.yellow, outline: 4 }); pop.restore(); }
  if (t >= T.title) {
    const e = eo(seg(t, T.title, T.title + .45));
    pop.fill(rectPts(x - 800 + (1 - e) * -900, y - 820, 1600, 780), C.yellow);
    printType('SCRUB', x, y - 590, { size: 210, ink: C.red, dropInk: C.blue, letter: 4 }, seg(t, T.title + .15, T.title + .75));
    printType('DADDY', x, y - 380, { size: 210, ink: C.blue, dropInk: C.red, letter: 4 }, seg(t, T.title + .3, T.title + .9));
  }
}
function poster2(t) {
  const [x, y] = P2;
  ground(x, y, C.yellow);
  printType('CAR POLISH', x, y - 430, { size: 180, ink: C.red, dropInk: C.blue }, seg(t, T.track1 + .5, T.track1 + 1.1));
  car(x, y - 100, .95, C.blue);
  if (t >= T.pad) { pop.save(); pop.translate(x + 60, y - 150); pop.scale(stampS(t, T.pad)); shape(circ(0, 0, 90), C.red, 7); shape(circ(0, 0, 40), C.paper, 5); pop.restore(); }
  if (t >= T.foam) { pop.save(); pop.translate(x, y + 330); pop.scale(stampS(t, T.foam, .26)); sponge(0, 0, .8, C.lemon, { hard: true }); pop.restore(); }
}
function poster3(t) {
  const [x, y] = P3;
  pop.fill(rectPts(x - 800, y - 1300, 800, 2600), C.sky); pop.fill(rectPts(x, y - 1300, 800, 2600), C.red);
  pop.line([[x, y - 1300], [x, y + 1300]], 8, { taper: 0 });
  printType('THANDA', x - 270, y - 500, { size: 140, ink: C.paper, dropInk: C.blue }, seg(t, T.track2 + .5, T.track2 + 1.1));
  printType('GARAM', x + 270, y - 500, { size: 140, ink: C.yellow, dropInk: C.blue }, seg(t, T.hot - .2, T.hot + .4));
  if (t >= T.track2 + .9) { sponge(x - 270, y + 60, .7 * stampS(t, T.track2 + .9), C.blue, { hard: true }); iceCubes(x - 270, y - 220, .7); }
  if (t >= T.hot) { const sq = .6 + .4 * Math.sin((t - T.hot) * 5) * Math.exp(-(t - T.hot) * 1.2); sponge(x + 270, y + 80, .7 * stampS(t, T.hot), C.pink, { squish: sq }); }
  printType('SAKHT', x - 270, y + 300, { size: 110, ink: C.blue, dropInk: C.paper }, seg(t, T.track2 + 1.3, T.track2 + 1.9));
  printType('NARAM', x + 270, y + 300, { size: 110, ink: C.yellow, dropInk: C.blue }, seg(t, T.hot + .4, T.hot + 1));
}
function poster4(t) {
  const [x, y] = P4;
  ground(x, y, C.sky);
  garage(x, y + 120, .95);
  sponge(x, y + 300, .5, C.lemon, { hard: true });
  printType('GARAGE', x, y - 430, { size: 180, ink: C.yellow, dropInk: C.red }, seg(t, T.track3 + .5, T.track3 + 1.1));
  if (t > T.dust0) front(x - 800, y - 1300, x + 800, y + 1300, seg(t, T.dust0, T.dust1) * 1.3, C.dark, { max: .6 });
}
function poster5(t) {   // the face
  const [x, y] = P4;
  ground(x, y, C.red);
  sponge(x, y + 130, 1.45, C.yellow, { face: t < T.eyes ? 0 : t < T.mouth ? seg(t, T.eyes, T.eyes + .3) * .45 : .5 + .5 * seg(t, T.mouth, T.mouth + .5), hole: C.red });
  printType('SMILE!', x, y - 420, { size: 230, ink: C.yellow, dropInk: C.blue }, seg(t, T.mouth + .3, T.mouth + .9));
  // the demo: fingers in the eyes, a spoon through the mouth
  if (t >= T.fing) { const e = eo(seg(t, T.fing, T.fing + .5)); finger(x - 119, y + 72 + (1 - e) * 500, 1, 0); finger(x + 119, y + 72 + (1 - e) * 500, 1, 0); }
  if (t >= T.spn) { const e = eio(seg(t, T.spn, T.spn + 1.4)); spoon(x - 500 + e * 500, y + 170, .9, Math.PI / 2); }
}
function poster6(t) {   // 2012: the TV deal → copies
  const [x, y] = P6;
  ground(x, y, C.yellow);
  printType('2012', x, y - 430, { size: 230, ink: C.red, dropInk: C.blue }, seg(t, T.track8 + .9, T.track8 + 1.5));
  tv(x, y + 120, .9, C.blue, C.sky);
  sponge(x - 70, y + 140, .55, C.yellow, { face: 1, hole: C.sky });
  if (t >= T.deal) { pop.save(); pop.translate(x + 250, y - 190); pop.scale(stampS(t, T.deal, .25)); pop.rotate(.12); burst(0, 0, 170, C.red, 16); pop.text('DEAL!', 0, 4, { size: 96, fill: C.yellow, outline: 4 }); pop.restore(); }
}
// the wall of smiling sponges (from the TV outward)
const TS = 520;
const ORDER = []; for (let j = -5; j <= 5; j++) for (let i = -4; i <= 4; i++) if (i || j) ORDER.push([i, j]);
ORDER.sort((a, b) => (Math.max(Math.abs(a[0]), Math.abs(a[1])) - Math.max(Math.abs(b[0]), Math.abs(b[1]))) || (Math.atan2(a[1], a[0]) - Math.atan2(b[1], b[0])));
const STAMP = new Map();
ORDER.forEach(([i, j], n) => { const ta = T.wall0 + .3 + (T.wall1 - T.wall0) * Math.pow((n + 1) / ORDER.length, .55); STAMP.set(i + ',' + j, ta); if (n < 24 || n % 4 == 0) ev(ta, 'stamp', { pan: clamp(i / 4, -1, 1) * .6, v: n < 8 ? .8 : .45 }); });
const stampT = (i, j) => (i || j) ? STAMP.get(i + ',' + j) ?? 1e9 : T.wall0;
const waveT = (i, j) => (i || j) ? T.wave + .5 + Math.hypot(i, j * 1.1) * .15 + h2(i, j, 6) * .1 : T.wave;
{ const fl = []; for (const [i, j] of ORDER) fl.push(waveT(i, j)); fl.sort((a, b) => a - b); ev(T.wave, 'flip', { v: 1.2, note: 0 }); let last = -1, n = 1; for (const t of fl) if (t - last > .07) { ev(t, 'flip', { v: .5, note: n++ }); last = t; } }
const SP = [C.yellow, C.pink, C.sky, C.mint, C.lilac, C.peach];
function wall(t) {
  const [x, y] = P6;
  for (let j = -5; j <= 5; j++) for (let i = -4; i <= 4; i++) {
    const s = stampS(t, stampT(i, j)); if (!s || (!i && !j)) continue;
    const fu = seg(t, waveT(i, j) - .17, waveT(i, j) + .17), nu = fu >= .5, sx = Math.abs(Math.cos(Math.PI * fu));
    const grd = nu ? [C.blue, C.red, C.yellow][Math.floor(h2(i, j, 2) * 3)] : C.blue;
    pop.save(); pop.translate(x + i * TS, y + 120 + j * TS); pop.scale(s * sx, s);
    pop.fill(rectPts(-TS / 2, -TS / 2, TS, TS), grd); pop.outline(rectPts(-TS / 2, -TS / 2, TS, TS), 6, { closed: true });
    sponge(0, 0, .85, nu ? SP[Math.floor(h2(i, j, 4) * SP.length)] : C.yellow, { face: 1, hole: grd });
    pop.restore();
  }
}
function poster7(t) {   // lesson + end: plain sponges, one with a face
  const TS7 = 500;
  for (let j = -4; j <= 4; j++) for (let i = -3; i <= 3; i++) {
    const odd = i === 0 && j === 0;
    const s = stampS(t, T.flip11 + .1 + (Math.abs(i) + Math.abs(j)) * .06); if (!s) continue;
    pop.save(); pop.translate(P6[0] + i * TS7, P6[1] + 3000 + j * TS7); pop.scale(s);
    pop.fill(rectPts(-TS7 / 2, -TS7 / 2, TS7, TS7), odd ? C.red : C.sky); pop.outline(rectPts(-TS7 / 2, -TS7 / 2, TS7, TS7), 6, { closed: true });
    sponge(0, 0, .8, odd ? C.yellow : C.cream, { face: odd ? 1 : 0, hole: C.red, hard: !odd });
    pop.restore();
  }
}

// ---------------------------------------------------------------- camera + shots
function render(t) {
  frame(t, (t) => {
    let cam;
    if (t < T.track1) cam = { x: 0, y: 0, zoom: lerp(1, 1.05, ss(seg(t, 0, T.track1))) };
    else if (t < T.track2) { const u = eio(seg(t, T.track1, T.track1 + 1.1)); cam = { x: lerp(P1[0], P2[0], u), y: 0, zoom: lerp(1.05, 1, u) - .22 * Math.sin(Math.PI * u) }; }
    else if (t < T.track3) { const u = eio(seg(t, T.track2, T.track2 + 1.1)); cam = { x: lerp(P2[0], P3[0], u), y: 0, zoom: 1 - .22 * Math.sin(Math.PI * u) }; }
    else if (t < T.push7) { const u = eio(seg(t, T.track3, T.track3 + 1.1)); const pz = ss(seg(t, A('d05'), T.dust1)); cam = { x: lerp(P3[0], P4[0], u), y: 0, zoom: 1 - .22 * Math.sin(Math.PI * u) + .1 * pz * (t < T.flip5 ? 1 : 0) }; }
    else if (t < T.track8) { const u = eio(seg(t, T.push7, T.push7 + 1.2)); cam = { x: P4[0], y: lerp(0, 70, u), zoom: lerp(1, 1.08, u) }; }
    else if (t < T.flip11) { const u = eio(seg(t, T.track8, T.track8 + 1.3)), v = eio(seg(t, T.wall0, T.wall1 + .4)), w2 = ss(seg(t, T.wave, B('d10'))); cam = { x: P4[0], y: lerp(70, P6[1], u), zoom: lerp(1.08, 1, u) - .3 * Math.sin(Math.PI * u) - .68 * v + .1 * w2, rot: -.03 * v + .04 * w2 }; }
    else cam = { x: P6[0], y: P6[1] + 3000, zoom: lerp(.7, .56, ss(seg(t, T.flip11, DUR))) };
    let sx = 1; for (const tf of [T.flip5, T.flip11]) if (Math.abs(t - tf) < .18) sx = flipX(t, tf);
    pop.begin(cam, { desk: null });
    pop.save(); pop.translate(cam.x, cam.y); pop.scale(Math.max(sx, .02), 1); pop.translate(-cam.x, -cam.y);
    if (t < T.flip11) {
      if (t < T.track2 + 1.2) { poster1(t); poster2(t); }
      if (t >= T.track2 - .1 && t < T.track3 + 1.2) poster3(t);
      if (t >= T.track3 - .1 && t < T.flip5) poster4(t);
      if (t >= T.flip5 && t < T.track8 + 1.4) poster5(t);
      if (t >= T.track8 - .1) { poster6(t); if (t >= T.wall0) wall(t); }
    } else poster7(t);
    pop.restore();
  }, (t) => {
    slip(t, A('d03') + .3, B('d03'), 70, 165, [{ s: 'AARON KRAUSE', size: 64 }, { s: 'CAR POLISH PADS', size: 44, ink: C.blue }], { shadowInk: C.red });
    slip(t, A('d05') + .3, B('d05'), 70, 165, [{ s: 'KISI KO KHAAS', size: 58 }, { s: 'NAHI LAGA', size: 58, ink: C.red }], { shadowInk: C.blue });
    slip(t, T.fing - .1, T.spn + .25, 70, 165, [{ s: 'AANKHEIN = GRIP', size: 62, ink: C.blue }], { shadowInk: C.yellow });
    slip(t, T.spn + .7, B('d07') + .3, 70, 165, [{ s: 'MUNH = CHAMMACH', size: 62, ink: C.red }], { shadowInk: C.blue });
    slip(t, A('d08') + .6, B('d08'), 70, 165, [{ s: 'SHARK TANK', size: 70, ink: C.red }, { s: 'TV PAR DEAL', size: 46 }], { shadowInk: C.blue });
    slip(t, A('d09') + .3, B('d09'), 70, 165, [{ s: 'MINUTON MEIN', size: 62 }, { s: 'HAZAARON BIKE', size: 52, ink: C.red }], { shadowInk: C.yellow });
    slip(t, A('d10') + .3, B('d10'), 70, 165, [{ s: 'SABSE BADA HIT', size: 66, ink: C.blue }], { shadowInk: C.red });
    if (t >= T.flip11 && t < T.end + .3) {
      const o = t > T.end ? -ei(seg(t, T.end, T.end + .3)) * 1200 : 0;
      printType('SABAK', W / 2 + o, 420, { size: 240, ink: C.yellow, dropInk: C.blue }, seg(t, T.flip11 + .3, T.flip11 + .9));
    }
    if (t >= T.end) {
      printType('FOLLOW', W / 2, 520, { size: 250, ink: C.yellow, dropInk: C.blue }, seg(t, T.end + .3, T.end + .9));
      if (HANDLE) label(W / 2, 680, [{ s: HANDLE, size: 66, font: 'Archivo' }], seg(t, T.end + .7, T.end + 1.2), { align: 'center', rot: -.02, shadowInk: C.blue });
      label(W / 2, 1640, [{ s: 'AGLI KAHANI: POPPI', size: 48 }], seg(t, T.end + 1.4, T.end + 1.9), { align: 'center', rot: .02 });
    } else handleSlip(t, HANDLE);
  });
}
install(render, DUR);
