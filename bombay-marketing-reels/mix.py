"""Score, foley and voice for one reel:  python mix.py <reel-id>   (reads out/<id>/events.json, voice/<id>/*.wav → out/<id>/mix.wav)"""
import sys, os, json, numpy as np, soundfile as sf, soxr
HERE = os.path.dirname(os.path.abspath(__file__)); LIB = os.environ.get('LIB', os.path.expanduser('~/lemo-opuscar'))
sys.path.insert(0, os.path.join(LIB, 'core', 'audio'))
import sfx, pluck as PL
from sfx import SR, add, noise, bp, lp, hp, t_, norm
rid = sys.argv[1]; J = json.load(open(f'{HERE}/out/{rid}/events.json')); DUR, EV = J['dur'], J['ev']
meta = json.load(open(f'{HERE}/out/meta.json'))[rid]; BPM, KEY, VAR = meta['bpm'], meta['key'], meta['n'] % 3
N = int((DUR + .2) * SR); rng = np.random.default_rng(meta['n'])
voice = np.zeros((N, 2), np.float32); fol = np.zeros((N, 2), np.float32); mus = np.zeros((N, 2), np.float32); amb = np.zeros((N, 2), np.float32)
ev = lambda k: [e for e in EV if e['type'] == k]

# --- voice ---
for e in ev('vo'):
    x, sr = sf.read(f"{HERE}/voice/{rid}/l{e['i']}.wav", dtype='float32'); x = soxr.resample(x, sr, SR)
    x = hp(x, 85); x = sfx.compress(x / (np.abs(x).max() + 1e-9) * .8, thr=.2, ratio=3.2); add(voice, x.astype(np.float32), e['t'], .95)
venv = np.abs(voice).max(1); k = int(.12 * SR); from scipy.ndimage import maximum_filter1d, uniform_filter1d
venv = uniform_filter1d(maximum_filter1d(venv, k), int(.25 * SR)); duck = 1 - .62 * np.clip(venv / .25, 0, 1)

# --- foley: pen on cold-press paper, brush, marker, moves, stamp ---
def pen(d, bright=1.0):
    d = max(d, .03); x = bp(noise(d), 1700 * bright, 7200); tt = t_(d); x *= (.55 + .45 * np.sin(2 * np.pi * (38 + 20 * rng.random()) * tt) ** 2); a = int(min(.012, d / 3) * SR)
    env = np.ones(len(x)); env[:a] = np.linspace(0, 1, a); env[-a:] = np.linspace(1, 0, a); return (x * env).astype(np.float32)
for e in ev('stroke'): add(fol, pen(e['d'], .8 + .5 * rng.random()), e['t'], .05 + .03 * rng.random(), (e.get('x', .5) - .5) * 1.2)
for e in ev('write'):
    d = e['d']; x = pen(d, 1.15); x *= (.25 + .75 * (np.sin(2 * np.pi * 8.5 * t_(d)[:len(x)]) > -.2)); add(fol, x.astype(np.float32), e['t'], .085)
for e in ev('wash'):
    d = 1.0; x = lp(hp(noise(d), 500), 3800) * np.sin(np.pi * t_(d) / d) ** 1.5; add(fol, x.astype(np.float32), e['t'], .05); add(fol, sfx.pop(.5), e['t'] + .02, .05)
for e in ev('hl'):
    d = .38; x = bp(noise(d), 900, 2600) * np.sin(np.pi * t_(d) / d); add(fol, x.astype(np.float32), e['t'], .10)
for e in ev('move'): add(fol, sfx.whoosh(.55), e['t'], .10)
for e in ev('pull'): add(fol, sfx.whoosh(.8), e['t'], .16)
for e in ev('stamp'): add(fol, sfx.thump(1, 85), e['t'], .7); add(fol, sfx.clack(.7), e['t'], .25); add(fol, sfx.ding(), e['t'] + .12, .16)

# --- ambience: paper room tone, then a far Mumbai street (J-cut before the first colour) ---
t = np.arange(N) / SR; tm = ev('music')[0]['t']; tp = ev('pull')[0]['t']; ts = ev('stamp')[0]['t']
room = lp(noise(N / SR), 500)[:N] * .012
street = bp(sfx.brown(N / SR)[:N], 90, 900) * (.6 + .4 * np.sin(2 * np.pi * .13 * t)) * .05 * np.clip((t - (tm - 1.6)) / 1.6, 0, 1)
dip = 1 - .75 * np.clip(1 - np.abs(t - (tp + .2)) / .5, 0, 1)
for c in (0, 1): amb[:, c] = (room + street) * dip
hd = 1.3; ht = t_(hd); horn = sum(np.sign(np.sin(2 * np.pi * f * ht)) for f in (311, 370)) * np.minimum(1, ht * 6) * np.minimum(1, (hd - ht) * 3); add(amb, lp(horn, 900).astype(np.float32), max(.2, tm - 1.3), .012, -.5)

# --- music: plucked ostinato, hand drums, a breathy flute answer, drone ---
beat = 60 / BPM; ROOT = 50 + KEY                              # D3 + key
SCALES = [[0, 2, 4, 7, 9], [0, 2, 4, 5, 7, 9, 10], [0, 2, 3, 5, 7, 9, 10]][VAR]   # pentatonic · Khamaj-like · Kafi-like
deg = lambda d: ROOT + 12 * (d // len(SCALES)) + SCALES[d % len(SCALES)]
OST = [[0, 2, 4, 2, 3, 4, 2, 1], [0, 4, 2, 4, 3, 2, 1, 2], [0, 1, 2, 4, 2, 4, 3, 1]][VAR]
cache = {}
def note(pr, m, d, v, **kw):
    key = (pr, round(m, 2), round(d, 2), round(v, 1), tuple(sorted(kw)));
    if key not in cache: cache[key] = PL.pluck(pr, m, d, v, **kw)
    return cache[key]
def dayan(v, hi=True):
    d = .28; tt = t_(d); f = PL._hz(ROOT + (24 if hi else 12)); return ((np.sin(2 * np.pi * f * tt) + .35 * np.sin(2 * np.pi * f * 2.02 * tt)) * np.exp(-tt / (.07 if hi else .05)) + bp(noise(d), 1500, 5000) * np.exp(-tt / .008) * .5).astype(np.float32) * v
def bayan(v):
    d = .5; tt = t_(d); f = 62 + 34 * np.exp(-tt / .09); ph = 2 * np.pi * np.cumsum(f) / SR; return ((np.sin(ph) + .3 * np.sin(2 * ph)) * np.exp(-tt / .16)).astype(np.float32) * v
def flute(m, d, v):
    tt = t_(d); f = PL._hz(m) * (1 + .006 * np.sin(2 * np.pi * 5.2 * tt) * np.minimum(1, tt * 3)) * (1 - .03 * np.exp(-tt / .05)); ph = 2 * np.pi * np.cumsum(f) / SR
    x = np.sin(ph) + .28 * np.sin(2 * ph) + .08 * np.sin(3 * ph) + bp(noise(d), PL._hz(m) * .8, PL._hz(m) * 3)[:len(tt)] * .5; a = np.minimum(1, tt / .07) * np.minimum(1, (d - tt) / .12); return (x * a).astype(np.float32) * v
TAAL = [('b+d', 1), ('b', .5), ('d', .7), ('t', .45), ('d', .75), ('t', .4), ('b+d', .85), ('d', .6)]   # an 8-count hand-drum cycle
nb = int((DUR - tm) / (beat / 2)); quiet0, quiet1 = tp - .1, ev('vo')[-1]['t'] + .2
for i in range(nb):
    tt0 = tm + i * beat / 2; drums = not (quiet0 <= tt0 < quiet1) and tt0 < ts - .05; bar = i // 8
    if tt0 > ts: break
    add(mus, note('nylon_guitar', deg(OST[i % 8] + (2 if bar % 4 == 3 else 0)) + 12, beat * .9, .55 + .15 * (i % 4 == 0)), tt0, .5 if drums else .32, -.25)
    if i % 8 == 0: add(mus, note('upright_bass_pizz', ROOT - 12 + (SCALES[2] if bar % 4 == 2 else 0), beat * 1.8, .7), tt0, .55)
    if drums and i >= 8:
        k, v = TAAL[i % 8]; sw = .012 * (i % 2)
        if 'b' in k: add(mus, bayan(v), tt0 + sw, .34, .1)
        if 'd' in k: add(mus, dayan(v), tt0 + sw, .16, .3)
        if k == 't': add(mus, dayan(v, False), tt0 + sw, .10, .35)
# flute answers in the gaps after each spoken line
cues = sorted(e for e in EV if e['type'] == 'cue')  if False else [e for e in EV if e['type'] == 'cue']
PHR = [[4, 5, 7], [7, 5, 4, 2], [2, 4, 5], [5, 4, 2, 0], [4, 7, 9]]
for j, c in enumerate(cues[1:-1]):
    t0 = np.ceil((c['t1'] - .25 - tm) / (beat / 2)) * (beat / 2) + tm; ph = PHR[(j + VAR) % 5]
    for k2, d in enumerate(ph): add(mus, flute(deg(d) + 24, beat * (.5 if k2 < len(ph) - 1 else 1.1), .5), t0 + k2 * beat / 2, .055, .35)
dr = sum(np.sin(2 * np.pi * PL._hz(ROOT + o) * t * (1 + .0007 * i)) * a for i, (o, a) in enumerate([(-12, .5), (0, .5), (7, .3), (12, .2), (19, .08)])) * (.8 + .2 * np.sin(2 * np.pi * .21 * t))
dr *= np.clip((t - tm) / 1.5, 0, 1) * np.clip((DUR - t) / 1.2, 0, 1) * .02
for c in (0, 1): mus[:, c] += dr.astype(np.float32)
fin = PL.strum('nylon_guitar', [ROOT, ROOT + 7, ROOT + 12, ROOT + 16 if VAR != 2 else ROOT + 15, ROOT + 19], 2.2, .8, spread=.03); add(mus, fin, ts + .02, .6)
mus *= (duck * np.clip((DUR - t) / 1.0, 0, 1))[:, None]

mix = voice * 1.0 + fol * (.75 + .25 * duck[:, None]) + mus * .62 + amb
mix = sfx.limit(mix[:, 0], .92), sfx.limit(mix[:, 1], .92); mix = np.stack(mix, 1)
# low end in check
os.makedirs(f'{HERE}/out/{rid}', exist_ok=True); sf.write(f'{HERE}/out/{rid}/mix.wav', mix[:int(DUR * SR)], SR, subtype='PCM_16')
print(rid, 'mix', round(DUR, 2), 's  peak', round(float(np.abs(mix).max()), 2))
