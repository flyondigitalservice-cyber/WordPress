// content.js → voice/<id>/lines.json (one file per reel) for the TTS tool
import { REELS } from '../content.js';
import fs from 'node:fs'; import path from 'node:path';
const here = path.dirname(new URL(import.meta.url).pathname), V = { f: 'hi-IN-SwaraNeural', m: 'hi-IN-MadhurNeural' };
for (const r of REELS) {
  const lines = [r.hook, ...r.cells, r.cta].map((c, i) => ({ id: 'l' + i, text: c.vo, voice: V[r.voice], rate: r.voice === 'm' ? '+16%' : '+13%' }));
  const d = path.join(here, '..', 'voice', r.id); fs.mkdirSync(d, { recursive: true }); fs.writeFileSync(path.join(d, 'lines.json'), JSON.stringify(lines, null, 1));
}
console.log(REELS.map(r => r.id).join('\n'));
