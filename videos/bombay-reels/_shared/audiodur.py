import re,subprocess,sys,os
d=sys.argv[1];p=os.path.join(d,'index.html');s=open(p).read()
def dur(f): return float(subprocess.check_output(['ffprobe','-v','error','-show_entries','format=duration','-of','csv=p=0',os.path.join(d,f)]).strip())
def fix(m):
  t=m.group(0)
  if 'data-duration' in t: return t
  src=re.search(r'src="([^"]+)"',t).group(1)
  return t.replace(' data-track-index',f' data-duration="{dur(src):.2f}" data-track-index')
open(p,'w').write(re.sub(r'<audio [^>]+>',fix,s))
