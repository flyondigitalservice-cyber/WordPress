// The seven shot types of the series. Inside a shot, frame 0 is the start of its push-in (TR_PRE frames before its line is spoken).
import React from 'react';
import { AbsoluteFill, Img, staticFile, useCurrentFrame } from 'remotion';
import { C, EASE, F, H, PAD, W, clamp01, mix, ramp } from './theme';
import { Checker, Lockup, Numeral, Tone, Words } from './parts';
import type { Cell, ReelData } from './timeline';

const T0 = 8; // content starts moving a few frames into the push
const two = (n: number) => String(n).padStart(2, '0');
const headline: React.CSSProperties = { fontFamily: F.head, fontWeight: 800, letterSpacing: '-0.025em', lineHeight: 1.02 };
const hardCard = (shadow = 14): React.CSSProperties => ({ border: `6px solid ${C.ink}`, boxShadow: `${shadow}px ${shadow}px 0 ${C.ink}` });
const Glow: React.FC<{ x: string; y: string; a?: number }> = ({ x, y, a = 0.2 }) => (
  <div style={{ position: 'absolute', inset: 0, background: `radial-gradient(ellipse 70% 46% at ${x} ${y}, rgba(255,199,44,${a}), rgba(255,199,44,0) 70%)` }} />
);
/** Eyebrow + title + sub, top-left: the same block opens every deliverable shot. */
const TitleBlock: React.FC<{ eyebrow: string; h: string; s?: string; tone: Tone; top?: number; size?: number }> = ({ eyebrow, h, s, tone, top = 300, size = 92 }) => {
  const f = useCurrentFrame();
  const k = ramp(f, T0 - 2, 18, EASE.out);
  const dark = tone === 'dark';
  return (
    <div style={{ position: 'absolute', left: PAD, right: PAD, top }}>
      <div style={{ display: 'flex', alignItems: 'center', gap: 18, fontFamily: F.body, fontWeight: 700, fontSize: 30, letterSpacing: '0.14em', color: dark ? C.yellow : C.ink, opacity: k * (dark ? 1 : 0.7), transform: `translateY(${((1 - k) * 18).toFixed(2)}px)` }}>
        <span style={{ width: 46, height: 6, borderRadius: 6, background: dark ? C.yellow : C.ink, display: 'inline-block' }} />{eyebrow}
      </div>
      <Words text={h} start={T0 + 2} gap={4} dur={22} dy={size * 0.38} blur={16} style={{ ...headline, fontSize: size, color: dark ? C.textOnDark : C.ink, marginTop: 26 }} />
      {s ? <Words text={s} start={T0 + 16} gap={2.4} dur={20} dy={24} blur={9} wordGap="0.26em" style={{ fontFamily: F.body, fontWeight: 500, fontSize: 42, lineHeight: 1.25, color: dark ? C.softOnDark : C.mute, marginTop: 24 }} /> : null}
    </div>
  );
};

/* ───────────── 0 · HOOK ─ the seller's question, word by word (blur-slide) ───────────── */
export const Hook: React.FC<{ reel: ReelData; len: number }> = ({ reel, len }) => {
  const f = useCurrentFrame();
  const push = 1 + 0.03 * ramp(f, 0, len, EASE.smooth);
  const k = ramp(f, 6, 18, EASE.out);
  const n = reel.hook.lines.length;
  const size = n >= 3 ? 124 : 138;
  let wordsBefore = 0;
  return (
    <AbsoluteFill style={{ background: C.ink }}>
      <Glow x="88%" y="72%" a={0.22} /><Glow x="6%" y="8%" a={0.07} />
      <Numeral text={two(reel.n)} tone="dark" top={330} size={520} />
      <div style={{ position: 'absolute', inset: 0, transform: `scale(${push.toFixed(5)})`, transformOrigin: `${PAD}px 900px` }}>
        <div style={{ position: 'absolute', left: PAD, top: 640, display: 'flex', alignItems: 'center', gap: 18, fontFamily: F.body, fontWeight: 700, fontSize: 32, letterSpacing: '0.14em', color: C.yellow, opacity: k, transform: `translateY(${((1 - k) * 18).toFixed(2)}px)`, filter: k >= 0.999 ? undefined : `blur(${((1 - k) * 6).toFixed(2)}px)` }}>
          <span style={{ width: 52, height: 6, borderRadius: 6, background: C.yellow, display: 'inline-block' }} />{reel.eyebrow}
        </div>
        <div style={{ position: 'absolute', left: PAD - 4, right: PAD, top: 716 }}>
          {reel.hook.lines.map((ln, i) => {
            const start = 12 + wordsBefore * 4 + i * 6; // each new line takes a breath first
            wordsBefore += ln.split(' ').length;
            return <Words key={i} text={ln} start={start} gap={4} dur={i === n - 1 ? 28 : 22} dy={size * 0.38} blur={18} style={{ ...headline, fontSize: size, color: C.textOnDark }} />;
          })}
          {reel.hook.sub ? <Words text={reel.hook.sub} start={12 + wordsBefore * 4 + n * 6 + 10} gap={3} dur={22} dy={28} blur={10} wordGap="0.26em" style={{ fontFamily: F.head, fontWeight: 700, fontSize: 64, lineHeight: 1.15, letterSpacing: '-0.015em', color: C.softOnDark, marginTop: 44 }} /> : null}
        </div>
      </div>
      <Checker y={H - 60} />
    </AbsoluteFill>
  );
};

/* ───────────── 1 · PAGE ─ the real service page; the camera travels to the card being spoken about ───────────── */
export const PageShot: React.FC<{ reel: ReelData; cell: Cell; len: number }> = ({ reel, cell, len }) => {
  const f = useCurrentFrame();
  const PW = 884, CSS = 430, S = PW / CSS;           // phone card width on screen; the capture is 430 CSS px wide at 3x
  const top = 690, PH = H - top + 40;
  const pg = reel.page;
  const card = cell.card !== undefined && cell.card >= 0 ? pg.cards[cell.card] : null;
  const viewH = PH / S;
  const target = card ? card.y + card.h / 2 - viewH * 0.25 : pg.secs[1].y + 20;
  const go = Math.min(64, Math.round(len * 0.3));    // first third: the hero; then travel
  const sc = ramp(f, go, 54, EASE.smooth);
  const drift = card ? 0 : ramp(f, go + 54, len, EASE.linear) * 260; // no single card to land on → keep reading down the page
  const y = mix(0, target, sc) + drift;
  const inP = ramp(f, 2, 30, EASE.snappy);
  const ring = ramp(f, go + 50, 16, EASE.overshoot);
  const ringO = ramp(f, go + 48, 8, EASE.out);
  return (
    <AbsoluteFill style={{ background: C.cream }}>
      <TitleBlock eyebrow="BOMBAY MARKETING" h={cell.h as string} s={cell.s} tone="light" top={290} size={88} />
      <div style={{ position: 'absolute', left: (W - PW) / 2, top, width: PW, height: PH, perspective: 1800, perspectiveOrigin: '50% 30%' }}>
        <div style={{ position: 'absolute', inset: 0, borderRadius: 60, overflow: 'hidden', background: C.ink, ...hardCard(16), border: `8px solid ${C.ink}`, opacity: clamp01(inP * 1.6), transform: `translateY(${((1 - inP) * 300).toFixed(2)}px) rotateX(${((1 - inP) * 16).toFixed(2)}deg) scale(${mix(0.94, 1, inP).toFixed(4)})`, transformOrigin: '50% 0%' }}>
          <div style={{ position: 'absolute', left: 0, top: 0, width: CSS, height: pg.pageH, zoom: S, transform: `translateY(${(-y).toFixed(2)}px)` }}>
            <Img src={staticFile(`textures/${pg.key}.png`)} style={{ position: 'absolute', left: 0, top: 0, width: CSS, height: pg.pageH }} />
            {card ? <div style={{ position: 'absolute', left: card.x - 7, top: card.y - 7, width: card.w + 14, height: card.h + 14, borderRadius: 24, border: `5px solid ${C.yellow}`, boxShadow: `0 0 0 3px ${C.ink}, 0 0 0 200vmax rgba(20,20,20,${(0.28 * ringO).toFixed(3)})`, opacity: ringO, transform: `scale(${mix(1.08, 1, ring).toFixed(4)})` }} /> : null}
          </div>
        </div>
      </div>
    </AbsoluteFill>
  );
};

/* ───────────── 2 · CHIPS ─ pills dealt in, one after another (deck-deal: accelerating cues, arc, soft overshoot) ───────────── */
export const Chips: React.FC<{ reel: ReelData; cell: Cell; idx: number; len: number }> = ({ reel, cell, idx, len }) => {
  const f = useCurrentFrame();
  const items = cell.items ?? [];
  const big = items.every((t) => t.length <= 14);
  const fs = big ? (items.length >= 4 ? 68 : 76) : 54;
  const many = items.length >= 4;                                  // four pills must still clear the caption
  const rowH = many ? (big ? 150 : 136) : big ? 178 : 150;
  const top0 = many ? 800 : 880;
  return (
    <AbsoluteFill style={{ background: C.cream2 }}>
      <Numeral text={two(idx + 1)} tone="light" />
      <TitleBlock eyebrow={reel.eyebrow} h={cell.h as string} s={cell.s} tone="light" />
      {items.map((t, k) => {
        const cue = T0 + 26 + 9 * k - 0.9 * k * (k - 1);          // intervals shrink: each pill leaves sooner than the last
        const fly = ramp(f, cue, 12, EASE.deal);
        const set = ramp(f, cue + 12, 6, EASE.settle);
        const arc = Math.sin(Math.PI * clamp01((f - cue) / 12));
        const rot = ((k * 7) % 5) - 2;                             // deterministic resting tilt, ±2°
        const x = mix(760, 0, fly), y = mix(900, 0, fly) - arc * 90;
        const shadow = ramp(f, cue + 15, 6, EASE.out);            // the shadow settles a beat after the pill
        const yellow = k % 2 === 0;
        return (
          <div key={k} style={{ position: 'absolute', left: PAD + (k % 2) * 46, top: top0 + k * rowH, opacity: clamp01((f - cue) / 3), transform: `translate(${x.toFixed(2)}px, ${y.toFixed(2)}px) rotate(${mix(20, rot, fly).toFixed(2)}deg) scale(${(mix(0.9, 1.05, fly) - 0.05 * set).toFixed(4)})`, transformOrigin: '20% 50%' }}>
            <div style={{ display: 'inline-flex', alignItems: 'center', gap: 24, padding: many ? '18px 44px 24px' : big ? '28px 52px 34px' : '24px 44px 30px', borderRadius: 999, background: yellow ? C.yellow : C.paper, border: `6px solid ${C.ink}`, boxShadow: `${(10 * shadow).toFixed(1)}px ${(10 * shadow).toFixed(1)}px 0 ${C.ink}`, fontFamily: F.head, fontWeight: 800, fontSize: fs, letterSpacing: '-0.02em', color: C.ink, whiteSpace: 'nowrap' }}>
              <span style={{ width: fs * 0.3, height: fs * 0.3, borderRadius: fs, background: C.ink, display: 'inline-block' }} />{t}
            </div>
          </div>
        );
      })}
    </AbsoluteFill>
  );
};

/* ───────────── 3 · LIST ─ the site's yellow checklist box; rows find their place while the box drifts (list-reveal) ───────────── */
export const List: React.FC<{ reel: ReelData; cell: Cell; idx: number; len: number }> = ({ reel, cell, idx, len }) => {
  const f = useCurrentFrame();
  const items = cell.items ?? [];
  const drift = 30 - 60 * clamp01(f / len);                          // container: slow, constant, unrelated to the rows
  const boxP = ramp(f, T0 + 6, 26, EASE.snappy);
  return (
    <AbsoluteFill style={{ background: C.cream }}>
      <Numeral text={two(idx + 1)} tone="light" />
      <TitleBlock eyebrow={reel.eyebrow} h={cell.h as string} tone="light" />
      <div style={{ position: 'absolute', left: PAD, width: W - 2 * PAD - 14, top: 760, transform: `translateY(${(drift + (1 - boxP) * 120).toFixed(2)}px)`, opacity: clamp01(boxP * 1.8) }}>
        <div style={{ background: C.yellow, borderRadius: 44, padding: '26px 50px', ...hardCard(14) }}>
          {items.map((t, i) => {
            const s0 = T0 + 22 + i * 10; const lin = clamp01((f - s0) / 24); const p = EASE.softBack(lin);
            const tick = ramp(f, s0 + 8, 12, EASE.overshoot);
            return (
              <div key={i} style={{ display: 'flex', alignItems: 'center', gap: 34, padding: '34px 0', borderTop: i ? '4px dashed rgba(20,20,20,0.28)' : undefined, opacity: Math.min(1, lin * 2.2), transform: `translateY(${((1 - p) * 44).toFixed(2)}px) scale(${mix(0.82, 1, p).toFixed(4)})`, transformOrigin: '0% 50%', filter: lin >= 0.999 ? undefined : `blur(${((1 - lin) * 8).toFixed(2)}px)` }}>
                <div style={{ flex: 'none', width: 82, height: 82, borderRadius: 82, background: C.ink, display: 'flex', alignItems: 'center', justifyContent: 'center', transform: `scale(${tick.toFixed(4)})` }}>
                  <svg width="44" height="44" viewBox="0 0 24 24"><path d="M5 12.5l4.5 4.5L19 7.5" fill="none" stroke={C.yellow} strokeWidth="3.4" strokeLinecap="round" strokeLinejoin="round" /></svg>
                </div>
                <div style={{ fontFamily: F.head, fontWeight: 700, fontSize: items.some((x) => x.length > 20) ? 56 : 64, letterSpacing: '-0.015em', lineHeight: 1.1, color: C.ink }}>{t}</div>
              </div>
            );
          })}
        </div>
      </div>
    </AbsoluteFill>
  );
};

/* ───────────── 4 · MARKER ─ one phrase gets the highlighter, after the headline has landed (marker-underline-title) ───────────── */
export const Marker: React.FC<{ reel: ReelData; cell: Cell; idx: number; len: number }> = ({ reel, cell, idx, len }) => {
  const f = useCurrentFrame();
  const lines = cell.h as string[];
  const longest = Math.max(...lines.map((l) => l.length));
  const size = Math.min(190, Math.floor(1650 / longest));              // the longest line always fits the column
  const k = ramp(f, T0 - 2, 18, EASE.out);
  const push = 1 + 0.025 * ramp(f, 0, len, EASE.smooth);
  const landed = T0 + 4 + lines.join(' ').split(' ').length * 4 + 22;
  const stroke = ramp(f, landed + 6, 10, EASE.out);                  // one quick stroke, left to right
  return (
    <AbsoluteFill style={{ background: C.paper }}>
      <Numeral text={two(idx + 1)} tone="light" />
      <svg width="0" height="0" style={{ position: 'absolute' }}><filter id="dry"><feTurbulence type="fractalNoise" baseFrequency="0.035 0.4" numOctaves="2" seed="7" result="n" /><feDisplacementMap in="SourceGraphic" in2="n" scale="9" /></filter></svg>
      <div style={{ position: 'absolute', inset: 0, transform: `scale(${push.toFixed(5)})`, transformOrigin: `${PAD}px 800px` }}>
        <div style={{ position: 'absolute', left: PAD, top: 560, display: 'flex', alignItems: 'center', gap: 18, fontFamily: F.body, fontWeight: 700, fontSize: 30, letterSpacing: '0.14em', color: C.ink, opacity: 0.7 * k }}>
          <span style={{ width: 46, height: 6, borderRadius: 6, background: C.ink, display: 'inline-block' }} />{reel.eyebrow}
        </div>
        <div style={{ position: 'absolute', left: PAD - 4, right: PAD - 30, top: 640 }}>
          {lines.map((ln, i) => {
            const before = lines.slice(0, i).join(' ').split(' ').filter(Boolean).length;
            return (
              <div key={i} style={{ position: 'relative', display: 'inline-block', marginBottom: size * 0.06 }}>
                {i === cell.mark ? <div style={{ position: 'absolute', left: -size * 0.06, right: -size * 0.08, bottom: size * 0.02, height: size * 0.42, background: C.yellow, borderRadius: size * 0.08, transform: `rotate(-1.1deg) scaleX(${stroke.toFixed(4)})`, transformOrigin: '0% 50%', filter: 'url(#dry)' }} /> : null}
                <Words text={ln} start={T0 + 4 + before * 4 + i * 6} gap={4} dur={22} dy={size * 0.38} blur={18} style={{ ...headline, fontSize: size, color: C.ink, position: 'relative', whiteSpace: 'nowrap', flexWrap: 'nowrap' }} />
                <br />
              </div>
            );
          })}
          {cell.s ? <Words text={cell.s} start={landed + 4} gap={2.4} dur={20} dy={26} blur={9} wordGap="0.26em" style={{ fontFamily: F.body, fontWeight: 500, fontSize: 48, lineHeight: 1.25, color: C.mute, marginTop: 50, maxWidth: 860 }} /> : null}
        </div>
      </div>
    </AbsoluteFill>
  );
};

/* ───────────── 5a · STAT ─ honest numbers, large ───────────── */
export const Stat: React.FC<{ reel: ReelData; cell: Cell; idx: number; len: number }> = ({ reel, cell, idx, len }) => {
  const f = useCurrentFrame();
  const stats = cell.stats ?? [];
  const one = stats.length === 1;
  return (
    <AbsoluteFill style={{ background: C.ink }}>
      <Glow x="90%" y="80%" a={0.2} />
      <TitleBlock eyebrow={reel.eyebrow} h={cell.h as string} tone="dark" size={84} />
      {stats.map((s, i) => {
        const s0 = T0 + 30 + i * 26; const p = ramp(f, s0, 26, EASE.out); const lineP = ramp(f, s0 + 10, 24, EASE.snappy); const lab = ramp(f, s0 + 12, 20, EASE.out);
        let big = s.big;
        const m = s.big.match(/^(\D*)([\d,]+)$/);
        if (m) { const target = Number(m[2].replace(/,/g, '')); big = m[1] + Math.round(target * ramp(f, s0, 34, EASE.out)).toLocaleString('en-IN'); }
        return (
          <div key={i} style={{ position: 'absolute', left: PAD, right: PAD, top: (one ? 820 : 720) + i * 370 }}>
            <div style={{ display: 'flex', alignItems: 'baseline', gap: 26, opacity: p, transform: `translateY(${((1 - p) * 70).toFixed(2)}px)`, filter: p >= 0.999 ? undefined : `blur(${((1 - p) * 18).toFixed(2)}px)` }}>
              <span style={{ ...headline, fontSize: one ? 196 : 210, color: C.yellow, fontVariantNumeric: 'tabular-nums' }}>{big}</span>
              <span style={{ ...headline, fontSize: one ? 56 : 68, color: C.textOnDark }}>{s.unit}</span>
            </div>
            <div style={{ height: 5, borderRadius: 5, background: 'rgba(252,250,250,0.3)', marginTop: 22, transform: `scaleX(${lineP.toFixed(4)})`, transformOrigin: '0 50%' }} />
            <div style={{ fontFamily: F.body, fontWeight: 500, fontSize: 46, color: C.softOnDark, marginTop: 22, opacity: lab, transform: `translateY(${((1 - lab) * 20).toFixed(2)}px)` }}>{s.label}</div>
          </div>
        );
      })}
    </AbsoluteFill>
  );
};

/* ───────────── 5b · NOTE ─ the honest note that closes every service page ───────────── */
export const Note: React.FC<{ reel: ReelData; cell: Cell; idx: number; len: number }> = ({ reel, cell, len }) => {
  const f = useCurrentFrame();
  const push = 1 + 0.025 * ramp(f, 0, len, EASE.smooth);
  const badge = ramp(f, T0 + 2, 20, EASE.overshoot);
  const draw = ramp(f, T0 + 14, 14, EASE.out);
  const h = cell.h as string;
  const words = h.split(' ').length;
  const rule = ramp(f, T0 + 12 + words * 4 + 18, 26, EASE.snappy);
  return (
    <AbsoluteFill style={{ background: C.ink }}>
      <Glow x="12%" y="78%" a={0.2} />
      <div style={{ position: 'absolute', inset: 0, transform: `scale(${push.toFixed(5)})`, transformOrigin: `${PAD}px 900px` }}>
        <div style={{ position: 'absolute', left: PAD, top: 470, width: 170, height: 170, borderRadius: 170, background: C.yellow, display: 'flex', alignItems: 'center', justifyContent: 'center', transform: `scale(${badge.toFixed(4)})`, boxShadow: `0 0 0 ${(18 * (1 - ramp(f, T0 + 16, 30, EASE.out))).toFixed(1)}px rgba(255,199,44,0.25)` }}>
          <svg width="96" height="96" viewBox="0 0 24 24"><path d="M5 12.5l4.5 4.5L19 7.5" fill="none" stroke={C.ink} strokeWidth="3.2" strokeLinecap="round" strokeLinejoin="round" pathLength={1} strokeDasharray={1} strokeDashoffset={1 - draw} /></svg>
        </div>
        <div style={{ position: 'absolute', left: PAD, right: PAD, top: 700 }}>
          <div style={{ fontFamily: F.body, fontWeight: 700, fontSize: 30, letterSpacing: '0.14em', color: C.yellow, opacity: ramp(f, T0 + 4, 18, EASE.out) }}>AN HONEST NOTE</div>
          <Words text={h} start={T0 + 12} gap={4} dur={24} dy={44} blur={18} style={{ ...headline, fontSize: h.length > 26 ? 96 : 112, color: C.textOnDark, marginTop: 26 }} />
          <div style={{ height: 6, borderRadius: 6, width: 300, background: C.yellow, marginTop: 44, transform: `scaleX(${rule.toFixed(4)})`, transformOrigin: '0 50%' }} />
          {cell.s ? <Words text={cell.s} start={T0 + 12 + words * 4 + 22} gap={2.4} dur={20} dy={26} blur={9} wordGap="0.26em" style={{ fontFamily: F.body, fontWeight: 500, fontSize: 48, lineHeight: 1.3, color: C.softOnDark, marginTop: 40 }} /> : null}
        </div>
      </div>
    </AbsoluteFill>
  );
};

/* ───────────── 6 · SIGN-OFF ─ the tag lands with a soft brake, the wordmark slides in, then the three ways to reach us ───────────── */
export const Outro: React.FC<{ reel: ReelData; len: number }> = () => {
  const f = useCurrentFrame();
  const t = f - T0;
  const inOutCubic = (x: number) => (x < 0.5 ? 4 * x * x * x : 1 - Math.pow(-2 * x + 2, 3) / 2);
  const shrink = inOutCubic(clamp01((t - 2) / 30));
  const brake = t > 30 && t < 44 ? Math.sin((Math.PI * (t - 30)) / 14) * 0.06 : 0;   // arrives, swells 6 %, settles
  const scale = mix(4.6, 1, shrink) + brake;
  const SIZE = 104, WORD = 'Marketing';
  const letters = WORD.split('').map((ch, i) => { const p = ramp(t, 48 + i * 2.2, 13, EASE.out); return <span key={i} style={{ display: 'inline-block', opacity: p, transform: `translateX(${((1 - p) * 26).toFixed(2)}px)` }}>{ch}</span>; });
  const shift = inOutCubic(clamp01((t - 40) / 16));
  const tag = ramp(t, 72, 18, EASE.out);
  const row = (i: number) => ramp(t, 92 + i * 9, 26, EASE.softBack);
  const rowStyle = (i: number): React.CSSProperties => { const p = row(i); const lin = clamp01((t - 92 - i * 9) / 26); return { opacity: Math.min(1, lin * 2.2), transform: `translateY(${((1 - p) * 60).toFixed(2)}px) scale(${mix(0.9, 1, p).toFixed(4)})`, filter: lin >= 0.999 ? undefined : `blur(${((1 - lin) * 8).toFixed(2)}px)` }; };
  return (
    <AbsoluteFill style={{ background: C.yellow }}>
      <div style={{ position: 'absolute', inset: 0, background: 'radial-gradient(ellipse 80% 50% at 50% 30%, rgba(255,255,255,0.28), rgba(255,255,255,0) 70%)' }} />
      <div style={{ position: 'absolute', left: 0, right: 0, top: 520, display: 'flex', justifyContent: 'center' }}>
        <div style={{ transform: `translateX(${mix(250, 0, shift).toFixed(2)}px) scale(${scale.toFixed(4)})`, transformOrigin: '22% 50%', opacity: clamp01((t - 1) / 6) }}>
          <Lockup size={SIZE} tone="yellow" marketing={<>{letters}</>} />
        </div>
      </div>
      <div style={{ position: 'absolute', left: 0, right: 0, top: 690, textAlign: 'center', fontFamily: F.body, fontWeight: 700, fontSize: 34, letterSpacing: '0.2em', color: C.ink, opacity: tag * 0.8 }}>BUILD · LAUNCH · GROW</div>
      <div style={{ position: 'absolute', left: PAD, right: PAD, top: 850, display: 'flex', flexDirection: 'column', alignItems: 'center', gap: 34 }}>
        <div style={{ ...rowStyle(0), background: C.ink, color: C.textOnDark, borderRadius: 30, padding: '34px 70px 40px', fontFamily: F.head, fontWeight: 800, fontSize: 76, letterSpacing: '-0.02em', boxShadow: '12px 12px 0 rgba(20,20,20,0.25)' }}>Free audit <span style={{ color: C.yellow }}>→</span></div>
        <div style={{ ...rowStyle(1), ...headline, fontSize: 92, color: C.ink, marginTop: 10 }}>bombaymarketing.in</div>
        <div style={{ ...rowStyle(2), display: 'flex', alignItems: 'center', gap: 24, background: C.paper, borderRadius: 999, padding: '24px 46px 28px', ...hardCard(10), fontFamily: F.body, fontWeight: 700, fontSize: 50, color: C.ink }}>
          <span style={{ width: 30, height: 30, borderRadius: 30, background: C.green, display: 'inline-block' }} />WhatsApp +91 87676 97389
        </div>
        <div style={{ ...rowStyle(3), fontFamily: F.body, fontWeight: 600, fontSize: 38, color: C.ink, opacity: Math.min(0.75, rowStyle(3).opacity as number) }}>Andheri West, Mumbai · Mon–Sat</div>
      </div>
      <Checker y={H - 60} a={C.ink} b={C.yellow} />
    </AbsoluteFill>
  );
};
