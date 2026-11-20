"""Sound for a brand reel: Hindi narration + soft print-shop foley (stamps, flips, whooshes; no pen/scratch sounds)
+ an original 60s lounge score (vibraphone, marimba, jazz bass, conga, shaker, claps, muted trumpet) shaped by
timeline.json's music sections. Run: <LIB>/.venv/bin/python mix.py <film folder>   → <film>/out/mix.wav"""
import json, os, sys
import numpy as np, soundfile as sf, soxr
LIB = os.environ.get('LIB', '/root/lemo-opuscar'); sys.path.insert(0, LIB)
from core.audio import sampler as S
from core.audio.sfx import SR, bp, lp, hp, add, compress, limit, whoosh, thump

F = os.path.abspath(sys.argv[1])
TL = json.load(open(os.path.join(F, 'timeline.json')))
EV = json.load(open(os.path.join(F, 'events.json')))['ev']
DUR = float(TL['dur']); N = int(DUR * SR)
rng = np.random.default_rng(7); tt = np.arange(N) / SR
rms = lambda x: float(np.sqrt(np.mean(np.square(x)) + 1e-12))
db = lambda x: 20 * np.log10(x + 1e-12)
def smooth(x, sec): k = max(1, int(sec * SR)); return np.convolve(x, np.ones(k) / k, 'same')
def nz(d): return rng.standard_normal(max(1, int(d * SR)))
def env(n, a, r): e = np.ones(n); a = min(n, int(a * SR)); r = min(n - a, int(r * SR)); e[:a] = np.linspace(0, 1, a) if a else 1; e[n - r:] = np.linspace(1, 0, r) if r else 1; return e

# ---------------- voice ----------------
vo = np.zeros(N); vmask = np.zeros(N)
for L in TL['lines']:
    x, sr = sf.read(os.path.join(F, 'out', 'voice', L['id'] + '.wav'), dtype='float32')
    if x.ndim > 1: x = x.mean(1)
    if sr != SR: x = soxr.resample(x, sr, SR)
    i = int(L['t0'] * SR); n = min(len(x), N - i); vo[i:i + n] += x[:n]; vmask[i:i + n] = 1
vo = hp(vo, 80)
vo = compress(vo / (np.abs(vo).max() + 1e-9) * 0.9, thr=0.2, ratio=3.0, att=0.003, rel=0.12)
duck = smooth(np.maximum(vmask, np.roll(vmask, int(0.15 * SR))), 0.2)

# ---------------- foley (soft) ----------------
fo = np.zeros((N, 2))
def stamp(v=1.0):
    th = thump(v=1.0, f=90)[:int(.25 * SR)] * .55
    sl = lp(hp(nz(.05), 700), 5000) * np.exp(-np.arange(int(.05 * SR)) / SR / .010) * .22   # a soft paper slap
    out = np.zeros(max(len(th), len(sl))); out[:len(th)] += th; out[:len(sl)] += sl
    return out * v
def flick(v=1.0):
    d = .06; return bp(nz(d), 1500, 5500) * np.exp(-np.arange(int(d * SR)) / SR / .012) * .35 * v
def bed(d, lo, hi, g):
    n = int(d * SR); x = bp(nz(d), lo, hi) * (0.6 + 0.4 * np.abs(lp(nz(d), 6)) * 3)
    return x * env(n, .5, .8) * g
for e in EV:
    t, ty, v, pan = e['t'], e['type'], e.get('v', 1.0), e.get('pan', 0.0)
    if ty == 'stamp': add(fo, stamp(.6 * v), t, 1.0, pan)
    elif ty == 'print': add(fo, stamp(.5 * v), t + .25, 1.0, -0.15)
    elif ty == 'whoosh':
        d = round(min(max(e.get('d', .8), .3), 2.5), 2); add(fo, lp(whoosh(d, 1.0), 2400) * .14, t, 1.0, 0.0)
    elif ty == 'flip': add(fo, flick(v), t, 1.0, pan)
    elif ty == 'crackle': add(fo, lp(bed(e.get('d', 3), 300, 2500, .05), 3000), t, 1.0, -.1)
    elif ty == 'fire':
        d = e.get('d', 4); add(fo, lp(nz(d), 500) * env(int(d * SR), .8, 1.5) * .10, t, 1.0, -.2); add(fo, bed(d, 600, 3000, .03), t + .02, 1.0, .25)
    elif ty == 'pop': add(fo, stamp(.35 * v), t, 1.0, pan)
fo *= 0.55 * (1 - 0.4 * duck)[:, None]

# ---------------- score ----------------
BPM = TL.get('bpm', 104); B = 60 / BPM; K = TL.get('key', 0)
CH = {'I': [48, 55, 60, 64, 67], 'IV': [41, 53, 57, 60, 65], 'V': [43, 55, 59, 62, 67], 'vi': [45, 57, 60, 64, 69], 'ii': [50, 57, 62, 65, 69]}
PROG = {'intro': ['I'], 'groove': ['I', 'vi', 'IV', 'V'], 'bright': ['IV', 'V', 'I', 'I'], 'joy': ['I', 'IV', 'V', 'I'],
        'dark': ['vi', 'vi', 'ii', 'vi'], 'fire': ['vi', 'ii'], 'soft': ['IV', 'I'], 'hush': ['vi'], 'end': ['I']}
secs = TL['music'] + [[DUR, 'stop']]
ev = []
for (a, mood), (b, _) in zip(secs[:-1], secs[1:]):
    if mood == 'hush':
        ev.append(dict(t=a, inst='contrabass', pitch=33 + K, dur=b - a + .3, vel=.35, pan=0, gain=.6, attack=.1)); continue
    if mood == 'end':
        for i, p in enumerate([48, 55, 60, 64, 67, 72, 76]): ev.append((a + i * .05, 'vibraphone', p + K, 3.5, .55, -.3 + .1 * i, .7))
        ev += [(a, 'jazz_bass', 36 + K, 3.0, .7, 0, .9), (a + .1, 'glockenspiel', 84 + K, 2.5, .45, .3, .45),
               (a, 'trumpet_mute', 67 + K, 2.0, .4, -.2, .35), (a, 'trumpet_mute', 72 + K, 2.0, .4, .2, .35)]
        continue
    t = a; k = 0; prog = PROG.get(mood, ['I'])
    while t < b - .02:
        beat = k % 4; ch = [p + K for p in CH[prog[(k // 4) % len(prog)]]]
        if mood in ('groove', 'bright', 'joy', 'intro', 'soft'):
            soft = mood == 'soft'
            if mood != 'intro' and beat in (0, 2): ev.append((t, 'jazz_bass', ch[0] - 12 + (7 if beat == 2 else 0), B * .9, .6, 0, .85))
            if not soft: ev.append((t, 'shaker', None, B * .5, .22 + .08 * (beat % 2), .35, .3))
            if beat in (1, 3) and not soft: ev.append((t + B * .5, 'conga', None, .3, .32, -.35, .42))
            if beat in (1, 3) and mood in ('joy', 'bright'): ev.append((t, 'claps', None, .3, .42, .1, .36))
            if beat in (0, 2): ev.append((t + (B * .5 if mood != 'intro' else 0), 'vibraphone', ch[2 + (k // 2) % 3], B * 1.2, .3, .25, .42))
            if beat == 0 and mood in ('groove', 'joy', 'intro') and (k // 4) % 2 == 0:
                for i, p in enumerate([ch[4] + 12, ch[3] + 12, ch[4] + 12, ch[2] + 12]): ev.append((t + i * B * .5, 'marimba', p, .35, .28, .2, .36))
            if mood == 'bright' and beat == 0 and (k // 4) % 2 == 1:
                for i, p in enumerate([ch[3] + 12, ch[2] + 12, ch[4] + 12]): ev.append(dict(t=t + i * B, inst='trumpet_mute', pitch=p, dur=B * .85, vel=.42, pan=-.2, gain=.36))
            if mood == 'joy' and k == 0:
                for p in [60, 64, 67, 72]: ev.append((t, 'trumpet_stac', p + K, .4, .65, 0, .42))
        else:   # dark / fire: a pedal and a bowed shimmer, the groove gone
            if beat == 0: ev.append((t, 'jazz_bass', ch[0] - 12, B * 3.5, .5, 0, .8))
            if beat == 0 and k % 8 == 0:
                ev.append(dict(t=t, inst='vibraphone_bowed', pitch=ch[2], dur=B * 7.5, vel=.33, pan=-.2, gain=.38, attack=.5))
                ev.append(dict(t=t + .05, inst='vibraphone_bowed', pitch=ch[3], dur=B * 7.5, vel=.28, pan=.25, gain=.33, attack=.5))
            if beat == 2: ev.append((t, 'conga', 'tumba', .4, .24, -.3, .34))
            if mood == 'fire' and beat in (1, 3): ev.append((t, 'marimba', ch[1] + 12, .4, .2, .3, .3))
        t += B; k += 1
# one marimba note per flip, climbing a scale
scale = [60, 62, 64, 67, 69, 72, 74, 76, 79, 81, 84]
for e in EV:
    if e['type'] == 'flip' and 'note' in e:
        n = e['note']; ev.append((e['t'], 'marimba', scale[min(len(scale) - 1, n // 4)] + K, .3, .5 if n == 0 else .3, -.4 + .8 * ((n * 37) % 11) / 10, .5 if n == 0 else .32))
S.seed(11)
music = S.render(ev, dur=DUR + 4, master=False)[:N]
music = S.room(music, size=0.35, mix=0.16)
music = music - 0.4 * np.stack([bp(music[:, 0], 300, 4000), bp(music[:, 1], 300, 4000)], 1)
music *= (1 - 0.45 * duck)[:, None]
music *= np.clip((DUR - tt) / 1.4, 0, 1)[:, None] ** 1.5

# ---------------- balance + master prep ----------------
spk = vmask > 0
vg = (rms(music[spk].mean(1)) * 10 ** (11 / 20)) / rms(vo[spk])
mix = fo + music + np.stack([vo * vg, vo * vg], 1)
print(f'voice vs music: {db(rms(vo[spk]) * vg) - db(rms(music[spk].mean(1))):.1f} dB; voice vs foley: {db(rms(vo[spk]) * vg) - db(rms(fo[spk].mean(1))):.1f} dB')
mix = np.stack([compress(mix[:, c], thr=0.3, ratio=2.5) for c in (0, 1)], 1)
ceil = rms(mix.mean(1)) * 10 ** (11 / 20)
mix = np.stack([limit(mix[:, c] / ceil * .9, .9) * ceil / .9 for c in (0, 1)], 1)
mix *= np.clip((DUR - tt) / 0.3, 0, 1)[:, None]
os.makedirs(os.path.join(F, 'out'), exist_ok=True)
sf.write(os.path.join(F, 'out', 'mix.wav'), mix.astype(np.float32), SR)
used = ['jazz_bass', 'contrabass', 'vibraphone', 'vibraphone_bowed', 'marimba', 'glockenspiel', 'trumpet_mute', 'trumpet_stac', 'shaker', 'conga', 'claps']
cr = S.credits(used); open(os.path.join(F, 'out', 'instruments.txt'), 'w').write('\n'.join(cr) if isinstance(cr, list) else str(cr))
print('mix ok', round(mix.shape[0] / SR, 2), 's')
