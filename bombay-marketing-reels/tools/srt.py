"""events.json → .srt (the 'cue' events carry the caption text)"""
import sys, json
ev = [e for e in json.load(open(sys.argv[1]))['ev'] if e['type'] == 'cue']
f = lambda t: '%02d:%02d:%02d,%03d' % (t // 3600, t % 3600 // 60, t % 60, round(t % 1 * 1000) % 1000)
open(sys.argv[2], 'w').write('\n'.join(f"{i + 1}\n{f(e['t'])} --> {f(e['t1'])}\n{e['text']}\n" for i, e in enumerate(ev)))
