"""Speech check for Hindi lines: transcribe each WAV (faster-whisper small, hi) and compare with the script.
Usage: python hi_check.py lines.json voice_dir   → prints similarity per line, writes words.json; exit 1 if any < 0.80"""
import json, sys, os, difflib, re
from faster_whisper import WhisperModel
L = json.load(open(sys.argv[1])); D = sys.argv[2]
m = WhisperModel(os.environ.get('WHISPER_MODEL', 'small'), compute_type='int8')
norm = lambda s: re.sub(r'[\s.,?!:;।\-—]', '', s).replace('़', '')
bad = 0; words = {}
for l in L:
    seg, _ = m.transcribe(os.path.join(D, l['id'] + '.wav'), language='hi', word_timestamps=True)
    seg = list(seg); txt = ' '.join(s.text.strip() for s in seg)
    words[l['id']] = [[w.start, w.end, w.word] for s in seg for w in s.words]
    r = difflib.SequenceMatcher(None, norm(l.get('asr', l['text'])), norm(txt)).ratio()
    flag = 'OK ' if r >= .8 else 'LOW'; bad += r < .8
    print(f"{flag} {l['id']} {r:.2f} | {txt}")
json.dump(words, open(os.path.join(D, 'words.json'), 'w'), ensure_ascii=False)
sys.exit(1 if bad else 0)
