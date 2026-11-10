// content.js → out/meta.json (tempo, key per reel) and out/ids.txt
import { REELS } from '../content.js'; import fs from 'node:fs'; import path from 'node:path';
const out = path.join(path.dirname(new URL(import.meta.url).pathname), '..', 'out'); fs.mkdirSync(out, { recursive: true });
fs.writeFileSync(path.join(out, 'meta.json'), JSON.stringify(Object.fromEntries(REELS.map(r => [r.id, { n: r.n, bpm: r.bpm, key: r.key, name: r.name }])), null, 1));
fs.writeFileSync(path.join(out, 'ids.txt'), REELS.map(r => r.id).join('\n') + '\n');
