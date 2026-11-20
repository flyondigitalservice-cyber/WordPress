// STANLEY — "Asli customer koi aur tha". Pop Art brand reel, 1080×1920. render(t) is pure.
import {
  pop, W, H, C, PASTEL, TL, LN, loadTimeline, ev, h2, rr, circ, rectPts, shape, tumbler, iceCubes, flask, car, CAR_BODY, phone, heart, burst, arrow, front,
  screen, printType, label, slip, handleSlip, frame, install, clamp, lerp, seg, ss, eio, eo, ei, back,
} from './kit/kit.js';
import { HANDLE } from './kit/handle.js';

await loadTimeline();
const A = id => LN[id].t0, B = id => LN[id].t1;
const DUR = TL.dur;

// posters (page positions)
const P1 = [0, 0], P2 = [1400, 0], P3 = [2800, 0], P5 = [2800, 3600], P7 = [4200, 3600];
const ground = (x, y, ink, w = 1500, h = 2500) => pop.fill(rectPts(x - w / 2, y - h / 2, w, h), ink);
const stampS = (t, t0, d = .22) => t < t0 ? 0 : 1 + .14 * (1 - eo(seg(t, t0, t0 + d)));   // 0 = not yet printed
const flipX = (t, tf, d = .36) => Math.abs(Math.cos(Math.PI * seg(t, tf - d / 2, tf + d / 2)));

// ---------------------------------------------------------------- timings
const T = {
  num1: A('s01') + .25, arrow: A('s01') + 2.2, num2: A('s01') + 2.6, saal: A('s01') + 5.2, title: A('s02') - .05,
  track1: A('s03') - .85, flaskT: A('s03') + .2,
  track2: A('s04') - .8, flop: A('s04') + 2.6, dark0: A('s04') + 2.0, dark1: B('s04') + .2,
  flip4: A('s05') - .02, ph: [A('s05') + .9, A('s05') + 1.9, A('s05') + 2.9],
  wall0: A('s06') + .15, wall1: A('s06') + 4.4, sold: B('s06') - .7,
  push7: A('s07'), wave: A('s08') + .8,
  track5: A('s09') - .95, fire0: A('s09') + .6, fire1: A('s09') + 3.2, disc: A('s09') + 3.6,
  viral: A('s10') + .1, flip6: A('s10') + 2.15,
  track7: A('s11') - .8, bars: A('s11') + .2, tenx: A('s11') + 1.9,
  flip8: A('s12') - .1, end: A('s13') - .2,
};
// sound events
ev(.05, 'stamp', { v: 1.2 }); ev(T.num1, 'print'); ev(T.arrow, 'stamp', { v: .6 }); ev(T.num2, 'print'); ev(T.saal, 'stamp');
ev(T.title, 'print', { v: 1.1 }); ev(T.track1, 'whoosh', { d: 1 }); ev(T.flaskT, 'stamp'); ev(T.track2, 'whoosh', { d: 1 });
ev(T.flop, 'stamp', { v: .9 }); ev(T.dark0, 'crackle', { d: T.dark1 - T.dark0 }); ev(T.flip4, 'flip', { v: 1.3 });
T.ph.forEach((t, k) => ev(t, 'stamp', { v: .7, pan: (k - 1) * .5 }));
ev(T.sold, 'stamp', { v: 1.3 }); ev(T.push7, 'whoosh', { d: 1.2 });
ev(T.track5, 'whoosh', { d: 1.1 }); ev(T.fire0, 'fire', { d: 5.5 }); ev(T.disc, 'stamp', { v: 1.1 });
ev(T.flip6, 'flip', { v: 1.2 }); ev(T.flip6 + .25, 'stamp', { v: .9 }); ev(T.track7, 'whoosh', { d: 1 }); ev(T.tenx, 'stamp', { v: 1.3 });
ev(T.flip8, 'flip', { v: 1.1 }); ev(T.end + .6, 'print', { v: 1 });

// ---------------------------------------------------------------- P1 hook
function poster1(t) {
  const [x, y] = P1;
  ground(x, y, C.yellow);
  // the first print is already down at t = 0: the tumbler, stamped
  const s = stampS(t, 0) || 1;
  tumbler(x, y + 175, .86 * s, C.blue, { lid: C.steel });
  printType('$73M', x, y - 610, { size: 200, ink: C.red, dropInk: C.blue }, seg(t, T.num1, T.num1 + .6));
  if (t >= T.arrow) { pop.save(); pop.translate(x, y - 480); pop.rotate(Math.PI / 2); pop.scale(stampS(t, T.arrow)); arrow(-70, 0, 140, C.red, 1.25); pop.restore(); }
  printType('$750M', x, y - 330, { size: 230, ink: C.blue, dropInk: C.red }, seg(t, T.num2, T.num2 + .6));
  if (t >= T.saal) { pop.save(); pop.translate(x + 300, y - 40); pop.scale(stampS(t, T.saal)); pop.rotate(-.12); burst(0, 0, 150, C.red, 14); pop.text('4 SAAL', 0, 4, { size: 74, fill: C.yellow, outline: 4 }); pop.restore(); }
  if (t >= T.title) {
    const e = eo(seg(t, T.title, T.title + .45));
    pop.fill(rectPts(x - 760 + (1 - e) * -900, y - 820, 1520, 700), C.red);
    printType('STANLEY', x, y - 470, { size: 210, ink: C.yellow, dropInk: C.blue, letter: 6 }, seg(t, T.title + .15, T.title + .75));
  }
}
// ---------------------------------------------------------------- P2 1913
function poster2(t) {
  const [x, y] = P2;
  ground(x, y, C.red);
  printType('1913', x, y - 480, { size: 240, ink: C.yellow, dropInk: C.blue }, seg(t, T.track1 + .5, T.track1 + 1.1));
  const s = stampS(t, T.flaskT) || (t > T.track1 ? 1 : 0);
  if (s) flask(x, y + 160, .85 * s, C.green);
  // the world it was made for: a lunchbox and a tent
  if (t > T.flaskT + .6) { pop.save(); pop.translate(x - 350, y + 330); pop.scale(stampS(t, T.flaskT + .6)); shape(rr(-120, -70, 240, 140, 20), C.steel, 7); shape(rr(-50, -100, 100, 36, 10), C.steel, 6); pop.restore(); }
  if (t > T.flaskT + 1.1) { pop.save(); pop.translate(x + 350, y + 330); pop.scale(stampS(t, T.flaskT + 1.1)); shape([[-150, 90], [0, -120], [150, 90], [-150, 90]], C.yellow, 7); shape([[-30, 90], [0, 10], [30, 90], [-30, 90]], C.dark, 5); pop.restore(); }
}
// ---------------------------------------------------------------- P3 the tumbler nobody bought
function poster3(t) {
  const [x, y] = P3;
  ground(x, y, C.sky);
  printType('QUENCHER', x, y - 480, { size: 180, ink: C.yellow, dropInk: C.red, letter: 4 }, seg(t, T.track2 + .55, T.track2 + 1.15));
  tumbler(x, y + 180, 1.0, C.green, { lid: C.steel });
  if (t >= T.flop) { pop.save(); pop.translate(x - 290, y - 200); pop.scale(stampS(t, T.flop)); pop.rotate(-.18); burst(0, 0, 150, C.paper, 12); pop.text('BIKTA', 0, -26, { size: 62, fill: C.red, outline: 3 }); pop.text('NAHI!', 0, 40, { size: 62, fill: C.red, outline: 3 }); pop.restore(); }
  // the print darkens: a front of red + blue dots crosses it
  if (t > T.dark0) front(x - 750, y - 1250, x + 750, y + 1250, seg(t, T.dark0, T.dark1) * 1.3, C.dark, { max: .85 });
}
// ---------------------------------------------------------------- P4 the blog → copies → the wall
const TS = 560;
const ORDER = []; for (let j = -5; j <= 5; j++) for (let i = -4; i <= 4; i++) if (i || j) ORDER.push([i, j]);
ORDER.sort((a, b) => (Math.max(Math.abs(a[0]), Math.abs(a[1])) - Math.max(Math.abs(b[0]), Math.abs(b[1]))) || (Math.atan2(a[1], a[0]) - Math.atan2(b[1], b[0])));
const STAMP = new Map();
ORDER.forEach(([i, j], n) => { const ta = T.wall0 + .35 + (T.wall1 - T.wall0) * Math.pow((n + 1) / ORDER.length, .55); STAMP.set(i + ',' + j, ta); if (n < 24 || n % 4 == 0) ev(ta, 'stamp', { pan: clamp(i / 4, -1, 1) * .6, v: n < 8 ? .8 : .45 }); });
const stampT = (i, j) => (i || j) ? STAMP.get(i + ',' + j) ?? 1e9 : T.wall0;
const waveT = (i, j) => (i || j) ? T.wave + .55 + Math.hypot(i, j * 1.1) * .16 + h2(i, j, 6) * .1 : T.wave;
{ const fl = []; for (const [i, j] of ORDER) fl.push(waveT(i, j)); fl.sort((a, b) => a - b); ev(T.wave, 'flip', { v: 1.2, note: 0 }); let last = -1, n = 1; for (const t of fl) if (t - last > .07) { ev(t, 'flip', { v: .5, note: n++ }); last = t; } }
function tile(i, j, t) {
  const [x, y] = P3;
  const fu = seg(t, waveT(i, j) - .17, waveT(i, j) + .17), nu = fu >= .5;
  const sx = Math.abs(Math.cos(Math.PI * fu));
  const k = Math.floor(h2(i, j, 2) * PASTEL.length);
  const grd = nu ? PASTEL[k] : C.pink, cup = nu ? PASTEL[(k + 2 + Math.floor(h2(i, j, 3) * 3)) % PASTEL.length] : C.green;
  pop.save(); pop.translate(x + i * TS, y + j * TS); pop.scale(sx * (stampS(t, stampT(i, j)) || 0), stampS(t, stampT(i, j)) || 0);
  pop.fill(rectPts(-TS / 2, -TS / 2, TS, TS), grd); pop.outline(rectPts(-TS / 2, -TS / 2, TS, TS), 6, { closed: true });
  tumbler(0, 50, .6, cup, { lid: nu ? C.paper : C.steel });
  pop.restore();
}
function poster4(t) {
  const [x, y] = P3;
  ground(x, y, C.pink, 1600, 2600);
  printType('THE BUY GUIDE', x, y - 480, { size: 130, ink: C.yellow, dropInk: C.blue }, seg(t, T.flip4 + .3, T.flip4 + .9));
  const rot = [-.1, 0, .1], dx = [-270, 0, 270], dy = [40, -20, 40];
  T.ph.forEach((tp, k) => { if (t < tp) return; phone(x + dx[k], y + 170 + dy[k], .7 * stampS(t, tp), PASTEL[(k * 2 + 1) % 6], { rot: rot[k], icon: () => tumbler(0, -80, .42, C.green) }); });
  if (t >= T.wall0) { for (let j = -5; j <= 5; j++) for (let i = -4; i <= 4; i++) if (t >= stampT(i, j)) tile(i, j, t); }
  if (t >= T.sold) { pop.save(); pop.translate(x + 60, y + 60); pop.scale(stampS(t, T.sold, .26) * 1.25); pop.rotate(-.15); burst(0, 0, 210, C.red, 16); pop.text('SOLD', 0, -44, { size: 108, fill: C.yellow, outline: 4 }); pop.text('OUT', 0, 62, { size: 108, fill: C.yellow, outline: 4 }); pop.restore(); }
}
// ---------------------------------------------------------------- P5 the burnt car
function poster5(t) {
  const [x, y] = P5;
  ground(x, y, C.blue, 1700, 2700);
  const fu = seg(t, T.fire0, T.fire1);
  car(x, y + 260, 1.12, C.red);
  // fire: flames rise and the print burns from left to right (orange dots, then dark)
  if (t > T.fire0) {
    pop.save(); pop.translate(x, y + 260); pop.scale(1.12); pop.clip(CAR_BODY);
    front(-440, -200, 440, 120, fu * 1.35, C.fire, { max: 1, n: 30 });
    front(-440, -200, 440, 120, seg(t, T.fire0 + 1.2, T.fire1 + 1.2) * 1.2, C.dark, { max: .5, n: 30 });
    pop.restore();
    for (let k = 0; k < 7; k++) {
      const fx = x - 420 + k * 140, g = clamp((fu * 1.35 - (k + .5) / 7) / .25 + .5), fl = Math.sin(t * 9 + k * 1.7) * .08;
      if (g <= .02) continue;
      const hgt = (160 + h2(k, 1) * 140) * ss(g) * (1 + fl);
      shape([[fx - 70, y + 80], [fx - 30, y + 80 - hgt * .6], [fx, y + 80 - hgt], [fx + 30, y + 80 - hgt * .55], [fx + 70, y + 80], [fx - 70, y + 80]], C.yellow, 6);
      shape([[fx - 36, y + 80], [fx, y + 80 - hgt * .55], [fx + 36, y + 80], [fx - 36, y + 80]], C.red, 4);
    }
  }
  // the call-out: a paper disc — the cup inside, ice still there
  if (t >= T.disc) {
    const s = stampS(t, T.disc, .26);
    pop.line([[x - 120, y + 170], [x + 60, y - 120]], 9, { taper: 0 });
    pop.save(); pop.translate(x + 170, y - 330); pop.scale(s);
    shape(circ(0, 0, 260, 64), C.paper, 10);
    tumbler(-40, 40, .62, C.pink, { ice: true, lid: C.paper });
    iceCubes(140, 150, .45);
    pop.restore();
  }
}
function poster6(t) {
  const [x, y] = P5;
  ground(x, y, C.yellow, 1700, 2700);
  car(x, y + 260, 1.12, C.blue, { bow: true });
  if (t > T.flip6 + .25) { pop.save(); pop.translate(x, y - 300); pop.scale(stampS(t, T.flip6 + .25)); pop.rotate(-.06); burst(0, 0, 250, C.red, 18); pop.text('NAYI', 0, -50, { size: 120, fill: C.yellow, outline: 4 }); pop.text('CAR!', 0, 70, { size: 120, fill: C.yellow, outline: 4 }); pop.restore(); }
}
// viral: hearts stamp around the burnt car
function hearts(t) {
  const [x, y] = P5;
  for (let k = 0; k < 9; k++) {
    const tk = T.viral + k * .18; if (t < tk) continue;
    const a = k * 2.4, r = 470 + h2(k, 7) * 160;
    pop.save(); pop.translate(x + Math.cos(a) * r * .8, y - 60 + Math.sin(a) * r * .9); pop.scale(stampS(t, tk) * (1.4 + h2(k, 3) * .8)); heart(0, 0, 1, k % 2 ? C.red : C.hot); pop.restore();
  }
}
// ---------------------------------------------------------------- P7 numbers
function poster7(t) {
  const [x, y] = P7;
  ground(x, y, C.paper, 1700, 2700);
  pop.fill(rectPts(x - 800, y + 330, 1600, 900), C.red);
  const g = eo(seg(t, T.bars, T.bars + 1.4));
  const h1 = 70 * eo(seg(t, T.bars, T.bars + .5)), h2b = 720 * g;
  shape(rectPts(x - 360, y + 330 - h1, 240, h1 + 1), C.blue, 7);
  shape(rectPts(x + 80, y + 330 - h2b, 300, h2b + 1), C.yellow, 8);
  pop.text('2019', x - 240, y + 395, { size: 62, fill: C.paper, outline: 3 });
  pop.text('2023', x + 230, y + 395, { size: 62, fill: C.paper, outline: 3 });
  printType('$73M', x - 240, y + 330 - h1 - 80, { size: 100, ink: C.blue, dropInk: C.red }, seg(t, T.bars + .2, T.bars + .7));
  printType('$750M', x + 230, y + 330 - h2b - 90, { size: 130, ink: C.yellow, dropInk: C.blue }, seg(t, T.bars + 1.2, T.bars + 1.7));
  if (t >= T.tenx) { pop.save(); pop.translate(x - 250, y - 180); pop.scale(stampS(t, T.tenx, .25)); pop.rotate(-.14); burst(0, 0, 220, C.red, 16); pop.text('10X', 0, 6, { size: 150, fill: C.yellow, outline: 5 }); pop.restore(); }
}
// ---------------------------------------------------------------- P8 lesson + end: the wall, one cup in another colour
function poster8(t) {
  const t0 = T.flip8;
  const TS8 = 520;
  for (let j = -4; j <= 4; j++) for (let i = -3; i <= 3; i++) {
    const odd = i === 1 && j === 1;
    const st = t0 + .1 + (Math.abs(i) + Math.abs(j)) * .06;
    const s = stampS(t, st); if (!s) continue;
    pop.save(); pop.translate(P7[0] + i * TS8, P7[1] + 2800 + j * TS8); pop.scale(s);
    pop.fill(rectPts(-TS8 / 2, -TS8 / 2, TS8, TS8), odd ? C.yellow : C.red); pop.outline(rectPts(-TS8 / 2, -TS8 / 2, TS8, TS8), 6, { closed: true });
    tumbler(0, 50, .56, odd ? C.pink : C.green, { lid: odd ? C.paper : C.steel });
    pop.restore();
  }
}

// ---------------------------------------------------------------- camera + shots
function render(t) {
  frame(t, (t) => {
    let cam;
    if (t < T.track1) cam = { x: 0, y: 0, zoom: lerp(1, 1.05, ss(seg(t, 0, T.track1))) };
    else if (t < T.track2) { const u = eio(seg(t, T.track1, T.track1 + 1.1)); cam = { x: lerp(P1[0], P2[0], u), y: 0, zoom: lerp(1.05, 1, u) - .22 * Math.sin(Math.PI * u) - .03 * seg(t, T.track1 + 1.1, T.track2) }; }
    else if (t < T.flip4 + .18) { const u = eio(seg(t, T.track2, T.track2 + 1.1)); const pz = ss(seg(t, A('s04'), T.dark1)); cam = { x: lerp(P2[0], P3[0], u), y: lerp(0, 120, pz), zoom: lerp(.97, 1, u) - .22 * Math.sin(Math.PI * u) + .16 * pz }; }
    else if (t < T.push7) { const u = eio(seg(t, T.wall0 + .1, T.wall1 + .4)); cam = { x: P3[0], y: P3[1], zoom: lerp(1, .3, u), rot: -.04 * u }; }
    else if (t < T.track5) { const u = eio(seg(t, T.push7, T.push7 + 2.2)), v = ss(seg(t, T.wave, B('s08'))); cam = { x: P3[0], y: P3[1], zoom: lerp(.3, .55, u) - .17 * v, rot: lerp(-.04, 0, u) + .03 * v }; }
    else if (t < T.track7) { const u = eio(seg(t, T.track5, T.track5 + 1.3)); const pz = ss(seg(t, T.disc - .4, T.disc + 1.5)); cam = { x: P5[0], y: lerp(P3[1], P5[1], u) - 90 * pz, zoom: lerp(.38, .92, u) - .2 * Math.sin(Math.PI * u) + .05 * pz }; }
    else if (t < T.flip8 + .18) { const u = eio(seg(t, T.track7, T.track7 + 1.1)); cam = { x: lerp(P5[0], P7[0], u), y: P7[1], zoom: .92 - .2 * Math.sin(Math.PI * u) + .06 * ss(seg(t, T.bars, T.flip8)) }; }
    else cam = { x: P7[0], y: P7[1] + 2800, zoom: lerp(.68, .55, ss(seg(t, T.flip8, DUR))) };

    // a card flip turns the whole frame around its centre
    const flips = [[T.flip4], [T.flip6], [T.flip8]];
    let sx = 1; for (const [tf] of flips) if (Math.abs(t - tf) < .18) sx = flipX(t, tf);
    pop.begin(cam, { desk: null });
    pop.save(); pop.translate(cam.x, cam.y); pop.scale(Math.max(sx, .02), 1); pop.translate(-cam.x, -cam.y);
    if (t < T.flip8) {
      if (t < T.track2 + 1.2) { poster1(t); poster2(t); }
      if (t >= T.track2 - .1 && t < T.flip4) poster3(t);
      if (t >= T.flip4 && t < T.track5 + 1.4) poster4(t);
      if (t >= T.track5 - .1 && t < T.flip6) { poster5(t); hearts(t); }
      if (t >= T.flip6) poster6(t);
      if (t >= T.track7 - .1) poster7(t);
    } else poster8(t);
    pop.restore();
  }, (t) => {
    // labels (screen space, top-left) and the handle
    slip(t, A('s03') + .4, B('s03'), 70, 165, [{ s: 'KAMGARON KE LIYE', size: 58 }, { s: 'HARE FLASK', size: 44, ink: C.green }], { shadowInk: C.blue });
    slip(t, A('s04') + .6, B('s04') - .2, 70, 165, [{ s: 'BAND HONE WALA THA', size: 56 }], { shadowInk: C.red });
    slip(t, A('s06') + .3, B('s06') - .9, 70, 165, [{ s: '5,000 CUPS', size: 76, ink: C.red }, { s: 'KHUD KHAREEDE', size: 44 }], { shadowInk: C.blue });
    slip(t, A('s07') + .2, B('s07'), 70, 165, [{ s: 'ASLI CUSTOMER?', size: 70 }, { s: 'AURATEIN, ONLINE', size: 44, ink: C.red }], { shadowInk: C.yellow });
    slip(t, A('s08') + .3, B('s08'), 70, 165, [{ s: 'PASTEL RANG', size: 68, ink: C.red }, { s: 'LIMITED DROPS', size: 50 }], { shadowInk: C.blue });
    slip(t, A('s09') + .2, B('s09') - .3, 70, 165, [{ s: 'NOV 2023', size: 70 }, { s: 'CAR JAL GAYI', size: 46, ink: C.red }], { shadowInk: C.yellow });
    slip(t, T.disc + .5, B('s09') + .2, 80, 980, [{ s: 'BARF ABHI BHI!', size: 56, ink: C.blue }], { shadowInk: C.red, rot: .03 });
    slip(t, A('s10') - .3, T.flip6 + .05, 70, 165, [{ s: 'VIDEO VIRAL', size: 70, ink: C.red }], { shadowInk: C.blue });
    slip(t, A('s11') + .2, B('s11') + .3, 70, 165, [{ s: '4 SAAL = 10X', size: 70 }], { shadowInk: C.red });
    if (t >= T.flip8 && t < T.end + .3) {
      const o = t > T.end ? -ei(seg(t, T.end, T.end + .3)) * 1200 : 0;
      printType('SABAK', W / 2 + o, 420, { size: 240, ink: C.yellow, dropInk: C.blue }, seg(t, T.flip8 + .3, T.flip8 + .9));
    }
    if (t >= T.end) {
      printType('FOLLOW', W / 2, 520, { size: 250, ink: C.yellow, dropInk: C.blue }, seg(t, T.end + .3, T.end + .9));
      if (HANDLE) label(W / 2, 680, [{ s: HANDLE, size: 66, font: 'Archivo' }], seg(t, T.end + .7, T.end + 1.2), { align: 'center', rot: -.02, shadowInk: C.blue });
      label(W / 2, 1640, [{ s: 'AGLI KAHANI: SCRUB DADDY', size: 48 }], seg(t, T.end + 1.4, T.end + 1.9), { align: 'center', rot: .02 });
    } else handleSlip(t, HANDLE);
  });
}
install(render, DUR);
