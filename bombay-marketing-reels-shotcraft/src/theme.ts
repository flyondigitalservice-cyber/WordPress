// Design tokens read from bombaymarketing.in (computed styles, see public/textures/layout.json) + the motion voice of the series.
import { Easing } from 'remotion';
export const W = 1080, H = 1920, FPS = 30;
export const C = {
  yellow: '#FFC72C', ink: '#141414', ink2: '#1F1F1F', cream: '#F6F1E7', cream2: '#EFE7D6', paper: '#FFFDF8',
  mute: '#5B5649', muteOnDark: '#BDB7A9', textOnDark: '#FCFAFA', softOnDark: '#E9E4D8', green: '#1FA855',
};
export const F = { head: '"Bricolage Grotesque", sans-serif', body: '"DM Sans", system-ui, sans-serif' };
export const PAD = 84;            // side margin
export const CAPTION_BOTTOM = 330; // keeps captions above the Instagram UI
const clamp01 = (t: number) => Math.min(1, Math.max(0, t));
const bz = (a: number, b: number, c: number, d: number) => Easing.bezier(a, b, c, d);
// One motion voice for the whole series: soft ease-outs, long tails, no bounce except where something lands.
export const EASE = {
  snappy: bz(0.16, 1, 0.3, 1), out: bz(0.22, 1, 0.36, 1), smooth: bz(0.65, 0, 0.35, 1), swift: bz(0.4, 0, 0.2, 1),
  overshoot: bz(0.34, 1.45, 0.64, 1), softBack: bz(0.3, 1.18, 0.5, 1), deal: bz(0.3, 0, 0.2, 1), settle: bz(0.3, 0, 0.25, 1.15),
  linear: (t: number) => clamp01(t),
};
export const ramp = (frame: number, start: number, dur: number, ease: (t: number) => number = EASE.snappy) => ease(clamp01((frame - start) / Math.max(1e-6, dur)));
export const mix = (a: number, b: number, t: number) => a + (b - a) * t;
export { clamp01 };
