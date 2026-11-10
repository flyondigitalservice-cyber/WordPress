// The picture is locked to the voice: every shot lasts exactly as long as its spoken line.
import reelsJson from './data/reels.json';
import dursJson from './data/durations.json';
import { FPS } from './theme';
export type Cell = { kind: 'page' | 'chips' | 'list' | 'marker' | 'stat' | 'note'; h: string | string[]; s?: string; items?: string[]; mark?: number; card?: number; cap: string;
  stats?: { big: string; unit: string; label: string }[] };
export type ReelData = { id: string; n: number; name: string; eyebrow: string; voice: string;
  page: { key: string; pageH: number; cards: { title: string; x: number; y: number; w: number; h: number }[]; secs: { cls: string; x: number; y: number; w: number; h: number }[] };
  hook: { lines: string[]; sub: string; cap: string }; cells: Cell[]; cta: { cap: string } };
export const REELS = reelsJson as unknown as ReelData[];
const DURS = dursJson as Record<string, number[]>;
export const LEAD = 10;      // frames before the first word
export const GAP = 13;       // breath between lines, after the last word (durations.json holds the end of speech, not of the file)
export const TAIL = 54;      // hold on the sign-off after the last word
export const TR = 18;        // shot-to-shot push, frames
export const TR_PRE = 10;    // the push starts this many frames before the next line is spoken
export type Shot = { i: number; from: number; to: number; voiceAt: number; voiceLen: number };
export const timeline = (r: ReelData) => {
  const d = DURS[r.id]; const shots: Shot[] = []; let cur = LEAD;
  d.forEach((sec, i) => { const len = Math.ceil(sec * FPS); const from = i === 0 ? 0 : cur; const to = cur + len + GAP + (i === d.length - 1 ? TAIL : 0);
    shots.push({ i, from, to, voiceAt: cur, voiceLen: len }); cur += len + GAP; });
  return { shots, total: shots[shots.length - 1].to };
};
