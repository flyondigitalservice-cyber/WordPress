"""Regenerate selected lines: python retts.py lines.json voice_dir id [id...] (merges dur.json)"""
import json, sys, subprocess, os, tempfile
L = json.load(open(sys.argv[1])); D = sys.argv[2]; ids = set(sys.argv[3:])
sel = [l for l in L if l['id'] in ids]
tmp = tempfile.mkdtemp(); jf = os.path.join(tmp, 'l.json'); json.dump(sel, open(jf, 'w'), ensure_ascii=False)
LIB = '/root/lemo-opuscar'
subprocess.run([LIB + '/.venv/bin/python', LIB + '/core/tts/tts.py', jf, tmp], check=True, capture_output=True)
d = json.load(open(os.path.join(D, 'dur.json'))); d.update(json.load(open(os.path.join(tmp, 'dur.json'))))
for i in ids: os.replace(os.path.join(tmp, i + '.wav'), os.path.join(D, i + '.wav'))
json.dump(d, open(os.path.join(D, 'dur.json'), 'w'), indent=1); print('ok', sorted(ids))
