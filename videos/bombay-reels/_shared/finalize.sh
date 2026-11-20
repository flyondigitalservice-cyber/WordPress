#!/bin/bash
# usage: finalize.sh vNN slug cover_time "Title"
set -e
ROOT=$(cd "$(dirname "$0")/.." && pwd); V=$1; SLUG=$2; CT=$3; TITLE=$4
D=$ROOT/$V; MP4=$D/renders/$V-$SLUG.mp4; OUT=$ROOT/deliverables/$V-$SLUG; mkdir -p $OUT
ffprobe -v error -show_entries stream=codec_name,width,height,r_frame_rate -show_entries format=duration,size -of compact $MP4
node /root/.claude/skills/hyperframes/scripts/plugin-cli.mjs audio $MP4 --json 2>/dev/null | python3 -c "import json,sys;it=json.load(sys.stdin)['items'][0];print('LUFS',it['loudness']['lufsIntegrated'],'TP',it['loudness']['truePeakDb'],'clip',it['loudness']['clipping']['count'])"
cp $MP4 $OUT/
ffmpeg -v error -y -ss $CT -i $MP4 -frames:v 1 -q:v 2 $OUT/$V-$SLUG-cover.jpg
python3 - "$V" "$TITLE" "$OUT/$V-$SLUG-transcript.md" "$D/index.html" <<'PY'
import json,sys,re
v,title,out,html=sys.argv[1:5]
lines=json.load(open(sys.argv[0] if False else __import__('os').path.dirname(out)+'/../../_shared/vo_lines.json'))[v]
h=open(html).read()
starts=[float(re.search(r'id="vo%d"[^>]*data-start="([\d.]+)"'%(i+1),h).group(1)) for i in range(len(lines))]
durs=[float(re.search(r'id="vo%d"[^>]*data-duration="([\d.]+)"'%(i+1),h).group(1)) for i in range(len(lines))]
f=open(out,'w');f.write(f"# Bombay Marketing — {v.upper()[1:]} · {title}\n\nVoiceover transcript (voice: Kokoro af_heart, on-device TTS)\n\n| In | Out | Line |\n|---|---|---|\n")
for s,d,l in zip(starts,durs,lines): f.write(f"| {s:05.2f} | {s+d:05.2f} | {l} |\n")
f.write("\nPlain text:\n\n"+" ".join(lines)+"\n")
PY
ls -la $OUT
