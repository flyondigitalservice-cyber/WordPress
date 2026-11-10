// content.src.mjs (what is said) + visual.mjs (what is shown) + captured page layout → src/data/reels.json and the voice line lists
import { REELS } from '../content.src.mjs';
import { VISUAL } from './visual.mjs';
import fs from 'node:fs'; import path from 'node:path';
const root = path.join(path.dirname(new URL(import.meta.url).pathname), '..');
const layout = JSON.parse(fs.readFileSync(path.join(root, 'public/textures/layout.json')));
const V = { f: 'hi-IN-SwaraNeural', m: 'hi-IN-MadhurNeural' };
const out = [];
for (const r of REELS) {
  const v = VISUAL[r.id]; if (!v) continue;
  const said = [r.hook, ...r.cells, r.cta];
  const L = layout[v.page];
  out.push({ id: r.id, n: r.n, name: r.name, eyebrow: v.eyebrow, voice: r.voice,
    page: { key: v.page, pageH: L.pageH, cards: L.cards.map(({ title, x, y, w, h }) => ({ title, x, y, w, h })), secs: L.secs },
    hook: { ...v.hook, cap: r.hook.sub }, cells: v.cells.map((c, i) => ({ ...c, cap: r.cells[i].sub })), cta: { cap: r.cta.sub } });
  const d = path.join(root, 'public/voice', r.id); fs.mkdirSync(d, { recursive: true });
  fs.writeFileSync(path.join(d, 'lines.json'), JSON.stringify(said.map((c, i) => ({ id: 'l' + i, text: c.vo, voice: V[r.voice], rate: '-6%' })), null, 1));
}
fs.writeFileSync(path.join(root, 'src/data/reels.json'), JSON.stringify(out, null, 1));
console.log(out.map(r => r.id).join('\n'));
