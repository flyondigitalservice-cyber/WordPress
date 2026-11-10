// Shared furniture: brand lockup, kaali-peeli strip, word reveal, header, captions.
import React from 'react';
import { useCurrentFrame } from 'remotion';
import { C, CAPTION_BOTTOM, EASE, F, PAD, W, clamp01, ramp } from './theme';

export type Tone = 'dark' | 'light' | 'yellow';
export const ink = (tone: Tone) => (tone === 'dark' ? C.textOnDark : C.ink);

/** The site's header lockup: "Bombay" on a tilted taxi-yellow tag + "Marketing". On yellow the tag turns black. */
export const Lockup: React.FC<{ size: number; tone: Tone; marketing?: React.ReactNode }> = ({ size, tone, marketing }) => (
  <div style={{ display: 'flex', alignItems: 'center', gap: size * 0.2, fontFamily: F.head, fontWeight: 800, fontSize: size, letterSpacing: '-0.03em', lineHeight: 1, whiteSpace: 'nowrap' }}>
    <span style={{ background: tone === 'yellow' ? C.ink : C.yellow, color: tone === 'yellow' ? C.yellow : C.ink, padding: `${size * 0.16}px ${size * 0.26}px ${size * 0.2}px`, borderRadius: size * 0.2, transform: 'rotate(-2deg)', display: 'inline-block' }}>Bombay</span>
    <span style={{ color: ink(tone) }}>{marketing ?? 'Marketing'}</span>
  </div>
);

/** Kaali-peeli taxi strip (two rows of squares), drifting slowly. */
export const Checker: React.FC<{ y: number; size?: number; speed?: number; a?: string; b?: string }> = ({ y, size = 30, speed = 0.6, a = C.yellow, b = C.ink }) => {
  const f = useCurrentFrame();
  const g = `linear-gradient(45deg, ${b} 25%, transparent 25%, transparent 75%, ${b} 75%)`;
  return <div style={{ position: 'absolute', left: 0, top: y, width: W, height: size * 2, backgroundColor: a, backgroundImage: `${g}, ${g}`, backgroundSize: `${size * 2}px ${size * 2}px`, backgroundPosition: `${(-f * speed).toFixed(2)}px 0, ${(size - f * speed).toFixed(2)}px ${size}px` }} />;
};

/** blur-slide: every word rises out of soft focus; y, blur and opacity share ONE progress value (the card's core rule). */
export const Words: React.FC<{ text: string; start: number; gap?: number; dur?: number; dy: number; blur: number; accent?: string; style?: React.CSSProperties; wordGap?: string }> = ({ text, start, gap = 4, dur = 22, dy, blur, accent = C.yellow, style, wordGap = '0.24em' }) => {
  const frame = useCurrentFrame();
  let on = false;
  const words = text.split(' ').filter(Boolean).map((raw) => {
    let t = raw; let acc = on;
    if (t.startsWith('*')) { t = t.slice(1); acc = true; on = true; }
    if (t.endsWith('*')) { t = t.slice(0, -1); on = false; }
    return { t, acc };
  });
  return (
    <div style={{ display: 'flex', flexWrap: 'wrap', alignItems: 'baseline', columnGap: wordGap, ...style }}>
      {words.map((w, i) => {
        const p = ramp(frame, start + i * gap, dur, EASE.out);
        return <span key={i} style={{ display: 'inline-block', opacity: p, transform: `translateY(${((1 - p) * dy).toFixed(2)}px)`, filter: p >= 0.999 ? undefined : `blur(${((1 - p) * blur).toFixed(2)}px)`, color: w.acc ? accent : undefined }}>{w.t}</span>;
      })}
    </div>
  );
};

/** Top bar: lockup, reel number, and five ticks that fill as the five deliverables are covered. */
export const Header: React.FC<{ tone: Tone; n: number; cell: number; cellP: number }> = ({ tone, n, cell, cellP }) => {
  const line = tone === 'dark' ? 'rgba(252,250,250,0.22)' : 'rgba(20,20,20,0.18)';
  const fill = tone === 'yellow' ? C.ink : tone === 'dark' ? C.yellow : C.ink;
  return (
    <div style={{ position: 'absolute', left: PAD, right: PAD, top: 108 }}>
      <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
        <Lockup size={44} tone={tone} />
        <div style={{ fontFamily: F.body, fontWeight: 700, fontSize: 28, letterSpacing: '0.14em', color: ink(tone), opacity: 0.72 }}>{String(n).padStart(2, '0')} / 12</div>
      </div>
      <div style={{ display: 'flex', gap: 12, marginTop: 34 }}>
        {[0, 1, 2, 3, 4].map((i) => (
          <div key={i} style={{ flex: 1, height: 8, borderRadius: 8, background: line, overflow: 'hidden' }}>
            <div style={{ width: `${(i < cell ? 1 : i === cell ? clamp01(cellP) : 0) * 100}%`, height: '100%', background: fill, borderRadius: 8 }} />
          </div>
        ))}
      </div>
    </div>
  );
};

/** Big outlined numeral in the background, like the 01 / 02 / 03 on the site's Build · Launch · Grow cards. */
export const Numeral: React.FC<{ text: string; tone: Tone; top?: number; size?: number }> = ({ text, tone, top = 250, size = 420 }) => {
  const f = useCurrentFrame();
  const p = ramp(f, 0, 40, EASE.out);
  return <div style={{ position: 'absolute', right: PAD - 10, top, fontFamily: F.head, fontWeight: 800, fontSize: size, lineHeight: 1, letterSpacing: '-0.05em', color: 'transparent', WebkitTextStroke: `4px ${tone === 'dark' ? 'rgba(252,250,250,0.13)' : 'rgba(20,20,20,0.11)'}`, opacity: p, transform: `translateX(${(18 - f * 0.12).toFixed(2)}px)` }}>{text}</div>;
};

// Captions: the roman-Hinglish line, in readable chunks, timed across the spoken line by length.
const chunk = (s: string, max = 46) => {
  const parts = s.split(/(?<=—|…|\.|,|\?|!)\s+/).filter(Boolean);
  const out: string[] = [];
  for (const p of parts) {
    if (out.length && (out[out.length - 1] + ' ' + p).length <= max) out[out.length - 1] += ' ' + p;
    else if (p.length <= max * 1.5) out.push(p);
    else { // a long clause with no punctuation: split near the middle on a space
      const mid = p.lastIndexOf(' ', Math.ceil(p.length / 2) + 4); out.push(p.slice(0, mid), p.slice(mid + 1));
    }
  }
  return out.map((c) => c.replace(/\s*—$/, ''));
};
export const Caption: React.FC<{ text: string; voiceAt: number; voiceLen: number }> = ({ text, voiceAt, voiceLen }) => {
  const frame = useCurrentFrame();
  const chunks = chunk(text); const total = chunks.reduce((a, c) => a + c.length, 0);
  const span = voiceLen;
  let acc = 0; const starts = chunks.map((c) => { const s = voiceAt + (acc / total) * span; acc += c.length; return s; });
  let k = 0; for (let i = 0; i < chunks.length; i++) if (frame >= starts[i] - 2) k = i;
  if (frame < voiceAt - 4) return null;
  const p = ramp(frame, starts[k] - 2, 8, EASE.out);
  return (
    <div style={{ position: 'absolute', left: 0, right: 0, bottom: CAPTION_BOTTOM, display: 'flex', justifyContent: 'center' }}>
      <div style={{ maxWidth: W - 2 * 70, background: C.paper, color: C.ink, border: `5px solid ${C.ink}`, borderRadius: 30, boxShadow: `8px 8px 0 ${C.ink}`, padding: '20px 34px 24px', fontFamily: F.body, fontWeight: 600, fontSize: 44, lineHeight: 1.22, textAlign: 'center', opacity: p, transform: `translateY(${((1 - p) * 14).toFixed(2)}px)` }}>{chunks[k]}</div>
    </div>
  );
};
