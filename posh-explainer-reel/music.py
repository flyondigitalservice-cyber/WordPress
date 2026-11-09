"""Original score for the POSH reels, synthesised with numpy: marimba arpeggio, bell melody, round bass, soft kick.
python music.py out.wav <dur> <bpm> <root midi> <seed> <cut,cut,...>"""
import sys, numpy as np, soundfile as sf
sys.path.insert(0, '/root/lemo-opuscar/core/audio')
import sfx
SR = sfx.SR
out, dur, bpm, root, seed = sys.argv[1], float(sys.argv[2]), float(sys.argv[3]), int(sys.argv[4]), int(sys.argv[5])
cuts = [float(x) for x in sys.argv[6].split(',')] if len(sys.argv) > 6 and sys.argv[6] else []
rng = np.random.default_rng(seed); beat = 60 / bpm; bar = 4 * beat
buf = np.zeros((int((dur + 0.5) * SR), 2))
hz = lambda m: 440 * 2 ** ((m - 69) / 12)
def tt(d): return np.arange(int(d * SR)) / SR
def marimba(m, v=1.0):
    t = tt(0.9); f = hz(m)
    return v * (np.sin(2 * np.pi * f * t) + .35 * np.sin(2 * np.pi * 4 * f * t) * np.exp(-t / .05) + .12 * np.sin(2 * np.pi * 10 * f * t) * np.exp(-t / .02)) * np.exp(-t / .2) * np.minimum(1, t / .002)
def bell(m, v=1.0):
    t = tt(1.8); f = hz(m)
    return v * (np.sin(2 * np.pi * f * t) + .3 * np.sin(2 * np.pi * 2.76 * f * t) * np.exp(-t / .25) + .15 * np.sin(2 * np.pi * 5.4 * f * t) * np.exp(-t / .1)) * np.exp(-t / .55) * np.minimum(1, t / .003)
def bass(m, v=1.0):
    t = tt(0.7); f = hz(m)
    return v * (np.sin(2 * np.pi * f * t) + .3 * np.sin(4 * np.pi * f * t) + .14 * np.sin(6 * np.pi * f * t)) * np.exp(-t / .28) * np.minimum(1, t / .006)
def kick(v=1.0):
    t = tt(0.3); ph = 2 * np.pi * (45 * t + 90 * .03 * (1 - np.exp(-t / .03)))
    return v * np.sin(ph) * np.exp(-t / .11)
def hat(v=1.0): return v * sfx.hp(sfx.noise(.05), 7000) * np.exp(-tt(.05) / .012)
def pad(notes, d, v=1.0):
    t = tt(d); e = np.minimum(1, t / (d * .35)) * np.minimum(1, (d - t) / (d * .3))
    return v * e * sum(np.sin(2 * np.pi * hz(n) * t + i) for i, n in enumerate(notes)) / len(notes)
PROG = [[0, 4, 7], [7, 11, 14], [9, 12, 16], [5, 9, 12]]
ARP = [[0, 1, 2, 1, 0, 2, 1, 2], [0, 2, 1, 2, 0, 1, 2, 1]][seed % 2]
PENT = [0, 2, 4, 7, 9, 12, 14, 16]
nbars = int(np.ceil(dur / bar)); end = dur - 1.9; mel = 4
for b in range(nbars):
    t0 = b * bar; ch = PROG[b % 4]
    if t0 >= end: break
    sfx.add(buf, pad([root + n for n in ch], bar, .05), t0, 1.0, 0)
    for e in range(8):
        t = t0 + e * beat / 2
        if t >= end: break
        n = ch[ARP[e]] + (12 if e in (3, 6) and b % 2 else 0)
        sfx.add(buf, marimba(root + n, .5 + .22 * (e % 2 == 0)), t, .34, -.35 + .7 * (e % 2))
        sfx.add(buf, hat(.5 + .5 * (e % 2)), t, .06 if b else .03, .3)
        if b >= 1 and e in (0, 4): sfx.add(buf, kick(), t, .5, 0); sfx.add(buf, bass(root - 24 + ch[0] % 12, 1), t, .34, 0)
        if b >= 1 and e in (2, 6): sfx.add(buf, sfx.click(.8, 1), t, .07, -.2)
        if b >= 1 and e == 3: sfx.add(buf, bass(root - 24 + ch[0] % 12, .6), t, .22, 0)
    if b >= 2:
        for q in range(4):
            if rng.random() < (.85 if q in (0, 2) else .45):
                mel = int(np.clip(mel + rng.integers(-2, 3), 1, 7)); t = t0 + q * beat
                if t < end: sfx.add(buf, bell(root + 12 + PENT[mel], 1), t, .14, .25 - .5 * rng.random())
for i, n in enumerate([0, 7, 12, 16, 19, 24]):                      # the last chord, rolled upward
    sfx.add(buf, marimba(root + n, 1), end + i * .09, .32, -.4 + i * .16); sfx.add(buf, bell(root + 12 + n, 1), end + i * .09, .09, .4 - i * .16)
sfx.add(buf, bass(root - 24, 1), end, .4, 0); sfx.add(buf, kick(), end, .5, 0)
for c in cuts: sfx.add(buf, sfx.whoosh(.55, 1), c - .05, .16, 0)
if cuts: sfx.add(buf, sfx.ding(1), cuts[-1] + 1.0, .1, .2)
buf = buf[:int(dur * SR)]; n = int(1.1 * SR); buf[-n:] *= np.linspace(1, 0, n)[:, None] ** 1.5
buf = np.stack([sfx.limit(buf[:, 0] / np.abs(buf).max() * .9), sfx.limit(buf[:, 1] / np.abs(buf).max() * .9)], 1)
sf.write(out, buf.astype(np.float32), SR); print('music', out, dur)
