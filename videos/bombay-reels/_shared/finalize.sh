#!/bin/bash
# usage: finalize.sh vNN slug cover_time "Title"
set -e
ROOT=$(cd "$(dirname "$0")/.." && pwd); V=$1; SLUG=$2; CT=$3; TITLE=$4
D=$ROOT/$V; MP4=$D/renders/$V-$SLUG.mp4; OUT=$ROOT/deliverables/$V-$SLUG; mkdir -p $OUT
ffprobe -v error -show_entries stream=codec_name,width,height,r_frame_rate -show_entries format=duration,size -of compact $MP4
node /root/.claude/skills/hyperframes/scripts/plugin-cli.mjs audio $MP4 --json 2>/dev/null | python3 -c "import json,sys;it=json.load(sys.stdin)['items'][0];print('LUFS',it['loudness']['lufsIntegrated'],'TP',it['loudness']['truePeakDb'],'clip',it['loudness']['clipping']['count'])"
cp $MP4 $OUT/
ffmpeg -v error -y -ss $CT -i $MP4 -frames:v 1 -q:v 2 $OUT/$V-$SLUG-cover.jpg
python3 - "$V" "$TITLE" "$OUT/$V-$SLUG-transcript.md" "$D/index.html" "$ROOT" <<'PY'
import json,sys,re
v,title,out,html,root=sys.argv[1:6]
base=json.load(open(root+'/_shared/vo_lines.json'))[v]
ext=[s["vo"] for s in json.load(open(root+'/_shared/ext_spec.json')).get(v,{}).get("scenes",[])]
h=open(html).read(); rows=[]
for m in re.finditer(r'<audio id="(vo|vx)(\d+)"[^>]*data-start="([\d.]+)"[^>]*data-duration="([\d.]+)"',h):
  kind,i,s,d=m.group(1),int(m.group(2)),float(m.group(3)),float(m.group(4))
  rows.append((s,d,(base if kind=="vo" else ext)[i-1]))
rows.sort(); tot=re.search(r'id="root"[^>]*data-duration="([\d.]+)"',h).group(1)
f=open(out,'w');f.write(f"# Bombay Marketing — {v[1:]} · {title}\n\nRunning time: {tot} s · Voiceover: Kokoro af_heart (on-device TTS)\n\n| In | Out | Line |\n|---|---|---|\n")
for s,d,l in rows: f.write(f"| {s:05.2f} | {s+d:05.2f} | {l} |\n")
f.write("\nPlain text:\n\n"+" ".join(r[2] for r in rows)+"\n")
PY
ls -la $OUT
