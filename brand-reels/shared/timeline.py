"""One timeline per film: narration line starts, subtitle chunks, music sections.
Reads <film>/film.json, <film>/voice/lines.json, <film>/out/voice/dur.json.
Writes <film>/timeline.json (read by the page and mix.py) and <film>/subtitles_hinglish.srt.
Usage: python timeline.py <film folder>"""
import json, os, re, sys

F = os.path.abspath(sys.argv[1])
C = json.load(open(os.path.join(F, 'film.json')))
L = json.load(open(os.path.join(F, 'voice', 'lines.json')))
D = json.load(open(os.path.join(F, 'out', 'voice', 'dur.json')))
GAP = C.get('gap', 0.38)
HUSH = C.get('hush', {})          # {line_id: seconds of near-silence before it}
MAXC = C.get('chunk', 40)


def chunks(text):
    """Split a subtitle line into pieces of at most MAXC characters, preferring breaks after punctuation."""
    words = text.split()
    out, cur = [], []
    for w in words:
        cand = ' '.join(cur + [w])
        if cur and len(cand) > MAXC:
            out.append(' '.join(cur)); cur = [w]
        else:
            cur.append(w)
            if re.search(r'[.?!:]$', w) and len(' '.join(cur)) > MAXC * .45:
                out.append(' '.join(cur)); cur = []
    if cur: out.append(' '.join(cur))
    # merge a tiny tail into the previous piece if it fits
    if len(out) > 1 and len(out[-1]) < 9 and len(out[-2]) + len(out[-1]) + 1 <= MAXC + 6:
        out[-2] = out[-2] + ' ' + out.pop()
    return out


t = C.get('start', 0.45)
lines = []
for l in L:
    i = l['id']
    t += C.get('gaps', {}).get(i, 0) + HUSH.get(i, 0)
    d = D[i]
    lines.append({'id': i, 't0': round(t, 3), 't1': round(t + d, 3), 'sub': l['sub']})
    t += d + GAP
DUR = round(lines[-1]['t1'] + C.get('tail', 3.4), 3)

# subtitle cues: pieces timed by character share of the spoken line; each piece held until the next starts
cues = []
for k, ln in enumerate(lines):
    ps = chunks(ln['sub']); tot = sum(len(p) for p in ps)
    a = ln['t0']; span = ln['t1'] - ln['t0']
    nxt = lines[k + 1]['t0'] - 0.06 if k + 1 < len(lines) else DUR - 0.3
    for n, p in enumerate(ps):
        b = a + span * len(p) / tot
        end = b if n + 1 < len(ps) else min(nxt, max(b + 0.5, ln['t0'] + 1.8))
        cues.append({'t0': round(a, 3), 't1': round(end, 3), 'text': p, 'line': ln['id']})
        a = b
    ln['cues'] = len(ps)

# music sections: [[line_id | "start", mood], ...] → absolute times (a section starts 0.25 s before its line)
by = {l['id']: l for l in lines}
secs = []
for key, mood in C['music']:
    if key == 'start': s = 0.0
    elif key.startswith('end:'): s = by[key[4:]]['t1'] + 0.35
    else: s = max(0.0, by[key]['t0'] - 0.25)
    secs.append([round(s, 3), mood])
for i, h in HUSH.items():                      # the held note before a turn
    secs.append([round(by[i]['t0'] - h - 0.05, 3), 'hush'])
    secs.append([round(by[i]['t0'] - 0.05, 3), C.get('after_hush', {}).get(i, 'joy')])
secs.sort()

json.dump({'dur': DUR, 'lines': lines, 'cues': cues, 'music': secs, 'bpm': C.get('bpm', 104), 'key': C.get('key', 0)},
          open(os.path.join(F, 'timeline.json'), 'w'), ensure_ascii=False, indent=1)


def ts(x):
    h, r = divmod(x, 3600); m, s = divmod(r, 60)
    return f'{int(h):02d}:{int(m):02d}:{int(s):02d},{int(round((s - int(s)) * 1000)):03d}'


with open(os.path.join(F, 'subtitles_hinglish.srt'), 'w') as f:
    for n, c in enumerate(cues, 1):
        f.write(f"{n}\n{ts(c['t0'])} --> {ts(c['t1'])}\n{c['text']}\n\n")
print(f'{os.path.basename(F)}: {DUR:.1f} s, {len(lines)} lines, {len(cues)} subtitle cues')
short = [c for c in cues if c['t1'] - c['t0'] < 1.0]
for c in short: print('  short cue', round(c['t1'] - c['t0'], 2), c['text'])
