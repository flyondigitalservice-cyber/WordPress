import React from 'react';
import { Composition } from 'remotion';
import '@fontsource/bricolage-grotesque/700.css';
import '@fontsource/bricolage-grotesque/800.css';
import '@fontsource/dm-sans/500.css';
import '@fontsource/dm-sans/600.css';
import '@fontsource/dm-sans/700.css';
import { Reel } from './Reel';
import { REELS, timeline } from './timeline';
import { FPS, H, W } from './theme';
export const Root: React.FC = () => (
  <>
    {REELS.map((r) => (
      <Composition key={r.id} id={'Reel' + String(r.n).padStart(2, '0')} component={Reel} width={W} height={H} fps={FPS}
        durationInFrames={timeline(r).total} defaultProps={{ reelId: r.id, bgm: true }} />
    ))}
  </>
);
