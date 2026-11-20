import json,sys,subprocess
H=["node","/root/.claude/skills/hyperframes/scripts/plugin-cli.mjs","check","--json","--safe-zone","reels"]
r=subprocess.run(H,capture_output=True,text=True);raw=r.stdout
d=json.loads(raw[raw.index('{'):])
seen=[]
def walk(x):
  if isinstance(x,dict):
    if 'severity' in x and ('code' in x or 'rule' in x):
      seen.append(x);return
    for v in x.values(): walk(v)
  elif isinstance(x,list):
    for v in x: walk(v)
walk(d)
for x in seen:
  if x.get('severity')=='info': continue
  print('-',x.get('severity'),x.get('code') or x.get('rule'),'|',(x.get('message') or '')[:260].replace('\n',' '),'|',x.get('selector',''),x.get('time',''))
print('TOTAL',len(seen),'ok' if d.get('ok') else d.get('ok'))
