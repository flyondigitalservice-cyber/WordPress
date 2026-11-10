// QA stills: node tools/qa.mjs 6 [fraction,fraction…]  → out/qa/ReelNN/sNN_f.png + a contact sheet
import fs from 'node:fs'; import { execFileSync } from 'node:child_process';
const n = Number(process.argv[2]); const fr = (process.argv[3] || '0.35,0.9').split(',').map(Number);
const reels = JSON.parse(fs.readFileSync('src/data/reels.json')); const durs = JSON.parse(fs.readFileSync('src/data/durations.json'));
const r = reels.find((x) => x.n === n); const d = durs[r.id]; const LEAD = 10, GAP = 13, TAIL = 54;
let cur = LEAD; const shots = d.map((s, i) => { const len = Math.ceil(s * 30); const o = { i, from: i ? cur : 0, to: cur + len + GAP + (i === d.length - 1 ? TAIL : 0) }; cur += len + GAP; return o; });
const comp = 'Reel' + String(n).padStart(2, '0'); const dir = `out/qa/${comp}`; fs.mkdirSync(dir, { recursive: true });
const only = process.argv[4] ? process.argv[4].split(',').map(Number) : null; const files = [];
for (const s of shots) { if (only && !only.includes(s.i)) continue; for (const f of fr) {
  const frame = Math.round(f <= 1 ? s.from + (s.to - s.from) * f : s.from + f); const out = `${dir}/s${s.i}_${frame}.png`;
  execFileSync('npx', ['remotion', 'still', 'src/index.ts', comp, out, `--frame=${frame}`, '--scale=0.5', '--log=error'], { stdio: 'inherit' }); files.push(out); } }
execFileSync('python3', ['-I', '-c', `
import sys
from PIL import Image
fs=sys.argv[2:]; ims=[Image.open(f).convert('RGB') for f in fs]; w,h=ims[0].size; cols=min(len(ims),7); rows=-(-len(ims)//cols)
sh=Image.new('RGB',(cols*w//2*1, rows*h//2)); 
for i,im in enumerate(ims): sh.paste(im.resize((w//2,h//2)),((i%cols)*w//2,(i//cols)*h//2))
sh.save(sys.argv[1], quality=88)`, `${dir}/sheet.jpg`, ...files]);
console.log(shots.map((s) => `${s.i}:${s.from}-${s.to}`).join(' '), dir + '/sheet.jpg');
