import json,subprocess,sys
H=["node","/root/.claude/skills/hyperframes/scripts/plugin-cli.mjs","tts"]
L=json.load(open("vo_lines.json"));out={}
for v,lines in L.items():
  out[v]=[]
  for i,t in enumerate(lines):
    f=f"vo/{v}_{i+1}.wav"
    r=subprocess.run(H+[t,"-v","af_heart","-s","1.0","-o",f,"--json"],capture_output=True,text=True)
    d=json.loads(r.stdout.strip().splitlines()[-1]);out[v].append(round(d["durationSeconds"],2))
  print(v,out[v],round(sum(out[v]),2),flush=True)
json.dump(out,open("vo_durations.json","w"),indent=1)
