"""Record every voice line with edge-tts (cached: delete public/voice/<id>/ to re-record) and write src/data/durations.json.
   python3 tools/voice.py"""
import asyncio, json, os, re, subprocess, sys, glob
ca = os.environ.get('SSL_CERT_FILE') or '/root/.ccr/ca-bundle.crt'
if os.path.exists(ca):
    import certifi
    certifi.where = lambda: ca      # behind a TLS-inspecting proxy edge-tts must trust the proxy's CA
import edge_tts
ROOT = os.path.join(os.path.dirname(os.path.abspath(__file__)), '..')

TARGET = -17.0   # LUFS: both voices are brought to the same level (the female voice records ~4 dB quieter)

def run(args):
    return subprocess.run(args, capture_output=True, text=True).stderr

def speech_end(p):
    """Seconds until the last word ends (the service pads every line with ~1 s of silence)."""
    total = float(subprocess.check_output(['ffprobe', '-v', 'error', '-show_entries', 'format=duration', '-of', 'csv=p=0', p]).strip())
    log = run(['ffmpeg', '-hide_banner', '-i', p, '-af', 'silencedetect=n=-45dB:d=0.25', '-f', 'null', '-'])
    starts = [float(x) for x in re.findall(r'silence_start: ([\d.]+)', log)]
    ends = [float(x) for x in re.findall(r'silence_end: ([\d.]+)', log)]
    trailing = starts and (len(starts) > len(ends) or ends[-1] >= total - 0.05)   # the last silence runs to the end of the file
    return starts[-1] if trailing and starts[-1] > 0.5 else total

def level(d, raw):
    """Static gain for the whole reel so its voice sits at TARGET LUFS; writes l<i>.mp3 from raw/l<i>.mp3."""
    lst = os.path.join(d, 'raw', 'list.txt'); open(lst, 'w').write(''.join(f"file '{os.path.abspath(r)}'\n" for r in raw))
    m = re.findall(r'I:\s+(-?[\d.]+) LUFS', run(['ffmpeg', '-hide_banner', '-f', 'concat', '-safe', '0', '-i', lst, '-af', 'ebur128', '-f', 'null', '-']))
    gain = TARGET - float(m[-1])
    for r in raw:
        subprocess.check_call(['ffmpeg', '-loglevel', 'error', '-y', '-i', r, '-af', f'volume={gain:.2f}dB', '-ar', '48000', '-b:a', '160k', os.path.join(d, os.path.basename(r))])
    return round(gain, 2)

async def say(line, path):
    for attempt in range(5):        # the service drops a line now and then
        try:
            await edge_tts.Communicate(line['text'], line['voice'], rate=line['rate']).save(path)
            if os.path.getsize(path) > 2000: return
        except Exception as e:
            print('retry', os.path.basename(path), type(e).__name__, file=sys.stderr)
        await asyncio.sleep(3 + attempt * 3)
    raise SystemExit('voice failed: ' + path)

async def main():
    out = {}
    for f in sorted(glob.glob(os.path.join(ROOT, 'public/voice/*/lines.json'))):
        d = os.path.dirname(f); rid = os.path.basename(d); os.makedirs(os.path.join(d, 'raw'), exist_ok=True); raw = []
        for line in json.load(open(f)):
            p = os.path.join(d, 'raw', line['id'] + '.mp3')
            if not os.path.exists(p): await say(line, p)
            raw.append(p)
        gain = level(d, raw)
        out[rid] = [round(speech_end(p), 3) for p in raw]
        print(rid, f'{gain:+.1f} dB', out[rid], flush=True)
    json.dump(out, open(os.path.join(ROOT, 'src/data/durations.json'), 'w'), indent=1)
asyncio.run(main())
