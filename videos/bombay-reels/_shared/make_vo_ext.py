import json,subprocess,os
H=["node","/root/.claude/skills/hyperframes/scripts/plugin-cli.mjs"]
S=json.load(open("ext_spec.json"));out={}
for v,spec in S.items():
  out[v]=[]
  for i,sc in enumerate(spec["scenes"]):
    f=f"vo/{v}_x{i+1}.wav"
    r=subprocess.run(H+["tts",sc["vo"],"-v","af_heart","-s","1.0","-o",f,"--json"],capture_output=True,text=True)
    d=json.loads(r.stdout.strip().splitlines()[-1]);ent={"dur":round(d["durationSeconds"],2)}
    if sc["type"]=="process":
      tj=f"/tmp/claude-0/tr_{v}.json"
      r=subprocess.run(H+["transcribe",f,"--json","-o",tj],capture_output=True,text=True)
      try:
        w=json.load(open(tj)); w=w if isinstance(w,list) else w.get("words",[])
        ent["words"]=[[x.get("text","").strip(" .,").lower(),round(x.get("start",0),2)] for x in w]
      except Exception as e: ent["err"]=str(e)+r.stdout[-300:]
    out[v].append(ent)
  print(v,[e["dur"] for e in out[v]],flush=True)
json.dump(out,open("vo_ext.json","w"),indent=1)
