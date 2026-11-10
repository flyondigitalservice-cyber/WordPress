// One reel: hook → five deliverables → sign-off. Shots push up into each other; voice, captions and sound follow SHOTS.
import React from 'react';
import { AbsoluteFill, Audio, Sequence, interpolate, staticFile, useCurrentFrame, useVideoConfig } from 'remotion';
import { REELS, TR, TR_PRE, timeline, type Cell, type ReelData, type Shot } from './timeline';
import { EASE, H, ramp } from './theme';
import { Caption, Checker, Header, type Tone } from './parts';
import { Chips, Hook, List, Marker, Note, Outro, PageShot, Stat } from './shots';
import { BGM, SFX } from './sound';

const toneOf = (kind: string): Tone => (kind === 'hook' || kind === 'stat' || kind === 'note' ? 'dark' : kind === 'outro' ? 'yellow' : 'light');

const ShotFrame: React.FC<{ shot: Shot; last: boolean; first: boolean; children: React.ReactNode }> = ({ shot, last, first, children }) => {
  const f = useCurrentFrame();
  const len = shot.to - shot.from;
  const pin = first ? 1 : ramp(f, 0, TR, EASE.smooth);
  const pout = last ? 0 : ramp(f, len, TR, EASE.smooth);
  return (
    <AbsoluteFill style={{ transform: `translateY(${((1 - pin) * H - pout * H * 0.26).toFixed(2)}px) scale(${(1 - 0.05 * pout).toFixed(4)})`, overflow: 'hidden', borderRadius: pin < 1 ? 56 * (1 - pin) + 0.01 : 0, boxShadow: pin < 1 ? '0 -30px 80px rgba(0,0,0,0.35)' : undefined }}>
      {children}
      {pout > 0 ? <AbsoluteFill style={{ background: `rgba(20,20,20,${(0.4 * pout).toFixed(3)})` }} /> : null}
    </AbsoluteFill>
  );
};

export const Reel: React.FC<{ reelId: string; bgm: boolean }> = ({ reelId, bgm }) => {
  const reel = REELS.find((r) => r.id === reelId) as ReelData;
  const { shots, total } = timeline(reel);
  const frame = useCurrentFrame();
  const { fps } = useVideoConfig();
  const kinds = ['hook', ...reel.cells.map((c) => c.kind), 'outro'];
  const caps = [reel.hook.cap, ...reel.cells.map((c) => c.cap), reel.cta.cap];
  const cur = shots.reduce((a, s) => (frame >= s.voiceAt - 4 ? s.i : a), 0);
  return (
    <AbsoluteFill style={{ background: '#141414' }}>
      {shots.map((s) => {
        const len = s.to - s.from; const kind = kinds[s.i]; const first = s.i === 0, last = s.i === shots.length - 1;
        const cellIdx = s.i - 1; const cell = reel.cells[cellIdx] as Cell | undefined;
        const body = kind === 'hook' ? <Hook reel={reel} len={len} />
          : kind === 'outro' ? <Outro reel={reel} len={len} />
          : kind === 'page' ? <PageShot reel={reel} cell={cell!} len={len} />
          : kind === 'chips' ? <Chips reel={reel} cell={cell!} idx={cellIdx} len={len} />
          : kind === 'list' ? <List reel={reel} cell={cell!} idx={cellIdx} len={len} />
          : kind === 'marker' ? <Marker reel={reel} cell={cell!} idx={cellIdx} len={len} />
          : kind === 'stat' ? <Stat reel={reel} cell={cell!} idx={cellIdx} len={len} />
          : <Note reel={reel} cell={cell!} idx={cellIdx} len={len} />;
        return (
          <Sequence key={s.i} from={s.from - TR_PRE} durationInFrames={len + TR + (last ? TR_PRE : 0)} layout="none">
            <ShotFrame shot={s} first={first} last={last}>
              {body}
              {kind !== 'outro' && kind !== 'hook' ? <Checker y={H - 60} speed={0.6} /> : null}
              {kind !== 'outro' ? <HeaderFor tone={toneOf(kind)} n={reel.n} cell={kind === 'hook' ? -1 : cellIdx} len={len} /> : null}
            </ShotFrame>
          </Sequence>
        );
      })}
      <Caption key={cur} text={caps[cur]} voiceAt={shots[cur].voiceAt} voiceLen={shots[cur].voiceLen} />
      {shots.map((s) => (
        <Sequence key={'v' + s.i} from={s.voiceAt} layout="none"><Audio src={staticFile(`voice/${reel.id}/l${s.i}.mp3`)} volume={1} /></Sequence>
      ))}
      {SFX(shots).map((x, i) => (
        <Sequence key={'s' + i} from={x.from} layout="none"><Audio src={staticFile(x.src)} volume={x.volume} /></Sequence>
      ))}
      {bgm && BGM ? <Audio src={staticFile(BGM.src)} loop volume={(f) => BGM!.volume * interpolate(f, [0, 20, total - 40, total - 4], [0, 1, 1, 0], { extrapolateLeft: 'clamp', extrapolateRight: 'clamp' })} /> : null}
    </AbsoluteFill>
  );
};

const HeaderFor: React.FC<{ tone: Tone; n: number; cell: number; len: number }> = ({ tone, n, cell, len }) => {
  const f = useCurrentFrame();
  return <Header tone={tone} n={n} cell={cell} cellP={(f - TR_PRE) / (len - 6)} />;
};
