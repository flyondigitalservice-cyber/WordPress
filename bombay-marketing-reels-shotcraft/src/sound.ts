// Sound, pinned to SHOTS (never to bare frame numbers). The voice leads; everything else stays well under it.
import type { Shot } from './timeline';
import { TR_PRE } from './timeline';

// Mixkit "G Eazy NBA type" (Arulo), Mixkit Stock Music Free License. Measured -16.4 LUFS; 0.12 puts it ~18 dB under the voice (-17 LUFS).
export const BGM: { src: string; volume: number } | null = { src: 'audio/bgm.mp3', volume: 0.12 };

// One effect only: a soft air whoosh under each push (tools/whoosh.py, energy peak 0.30 s = 9 frames in).
// The push runs from (shot.from - TR_PRE) for 18 frames and is fastest at its midpoint, 9 frames in → the whoosh starts with the push.
const WHOOSH = { src: 'audio/whoosh.wav', volume: 0.5 };
export const SFX = (shots: Shot[]): { from: number; src: string; volume: number }[] =>
  shots.slice(1).map((s) => ({ from: s.from - TR_PRE, ...WHOOSH }));
