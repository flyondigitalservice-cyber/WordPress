#!/bin/bash
cd "$(dirname "$0")/.."
H="node /root/.claude/skills/hyperframes/scripts/plugin-cli.mjs"
r(){ (cd $1 && $H render . -q high -o renders/raw.mp4 > /tmp/claude-0/render-$1.log 2>&1); echo "$1 rc=$?"; }
for v in p1 p2 p3 p4 p5 p6 p7 p8 p9 sr; do [ -s $v/renders/raw.mp4 ] || todo="$todo $v"; done
for v in $todo; do r $v & done; wait
python3 - <<'PY'
import json,subprocess,os,re
S=json.load(open('_shared/series_spec.json'))
for v,sp in S.items():
  slug=f"{v}-{sp['slug']}"; out=f"deliverables/{slug}"; os.makedirs(out,exist_ok=True); mp4=f"{out}/{slug}.mp4"
  subprocess.run(["ffmpeg","-v","error","-y","-i",f"{v}/renders/raw.mp4","-c:v","copy","-af","loudnorm=I=-15:TP=-1.5:LRA=11","-c:a","aac","-b:a","192k","-ar","48000",mp4],check=True)
  subprocess.run(["ffmpeg","-v","error","-y","-ss","2.3","-i",mp4,"-frames:v","1","-q:v","2",f"{out}/{slug}-cover.jpg"],check=True)
  h=open(f"{v}/index.html").read(); rows=[]
  for m in re.finditer(r'<audio id="vo(\d+)"[^>]*data-start="([\d.]+)"[^>]*data-duration="([\d.]+)"',h):
    i,s,d=int(m.group(1)),float(m.group(2)),float(m.group(3)); rows.append((s,d,sp['scenes'][i-1]['vo']))
  tot=re.search(r'id="root"[^>]*data-duration="([\d.]+)"',h).group(1)
  with open(f"{out}/{slug}-transcript.md","w") as f:
    f.write(f"# Bombay Marketing — {sp['tag'].split(' · ')[0]} · {sp['title']}\n\nRunning time: {tot} s · Voiceover: Kokoro af_heart (on-device TTS)\n\n| In | Out | Line |\n|---|---|---|\n")
    for s,d,l in sorted(rows): f.write(f"| {s:05.2f} | {s+d:05.2f} | {l} |\n")
  pr=subprocess.run(["ffprobe","-v","error","-show_entries","format=duration","-of","csv=p=0",mp4],capture_output=True,text=True).stdout.strip()
  a=json.loads(subprocess.run(["node","/root/.claude/skills/hyperframes/scripts/plugin-cli.mjs","audio",mp4,"--json"],capture_output=True,text=True).stdout)['items'][0]['loudness']
  print(v,pr,"LUFS",a['lufsIntegrated'],"TP",a['truePeakDb'],"clip",a['clipping']['count'],flush=True)
PY
echo ALL DONE
