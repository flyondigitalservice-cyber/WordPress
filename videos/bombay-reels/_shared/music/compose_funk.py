"""Original quirky-funk bed for the Bombay Marketing funky showreel.
105 BPM, 4/4, 26 bars + tail (= 60.0 s). Deterministic (seeded). Pure numpy/scipy synthesis.
Writes funk_bed.wav (48 kHz stereo) and beats.json (bar/beat times used by the animation)."""
import json, numpy as np
from scipy.signal import butter, lfilter
from scipy.io import wavfile

SR = 48000; BPM = 105.0; BEAT = 60.0 / BPM; S16 = BEAT / 4; BARS = 26; TAIL = 60.0 - BARS * 4 * BEAT
N = int(SR * 60.0); L = np.zeros(N); R = np.zeros(N)
rng = np.random.default_rng(7)
def note(n): return 440.0 * 2 ** ((n - 69) / 12)
def bp(x, lo, hi, o=2): b, a = butter(o, [lo / (SR / 2), hi / (SR / 2)], "band"); return lfilter(b, a, x)
def lp(x, f, o=2): b, a = butter(o, f / (SR / 2), "low"); return lfilter(b, a, x)
def hp(x, f, o=2): b, a = butter(o, f / (SR / 2), "high"); return lfilter(b, a, x)
def put(sig, t, vol=1.0, pan=0.0):
    i = int(t * SR); j = min(N, i + len(sig))
    if i >= N or j <= i: return
    s = sig[: j - i] * vol
    L[i:j] += s * (1 - max(0, pan)); R[i:j] += s * (1 + min(0, pan))
def swing(step): return S16 * step + (0.028 if step % 2 else 0.0)

# ---- instruments
def kick():
    t = np.arange(int(0.38 * SR)) / SR; f = 45 + 110 * np.exp(-t * 28)
    ph = 2 * np.pi * np.cumsum(f) / SR; return np.sin(ph) * np.exp(-t * 7.5) + 0.25 * np.sin(ph * 2) * np.exp(-t * 30)
def snare():
    t = np.arange(int(0.25 * SR)) / SR; n = bp(rng.standard_normal(len(t)), 900, 7000)
    return 0.7 * n * np.exp(-t * 17) + 0.5 * np.sin(2 * np.pi * 185 * t) * np.exp(-t * 26)
def hat(open_=False):
    d = 0.32 if open_ else 0.06; t = np.arange(int(d * SR)) / SR
    return hp(rng.standard_normal(len(t)), 7000) * np.exp(-t * (11 if open_ else 70))
def saw(f, d):
    t = np.arange(int(d * SR)) / SR; return 2 * ((t * f) % 1.0) - 1, t
def bass(n, d=0.2):
    s, t = saw(note(n), d); bright = lp(s, 2400); dark = lp(s, 380); env = np.exp(-t * 9)
    x = (bright * env + dark * (1 - env)) * np.minimum(1, t / 0.003) * np.exp(-t * 3.5)
    click = np.sin(2 * np.pi * note(n + 24) * t) * np.exp(-t * 60) * 0.55
    return np.tanh(1.6 * (x + click)) * 0.8
def pluck(n, d=0.42, bend=3.0):
    t = np.arange(int(d * SR)) / SR
    semis = bend * np.exp(-t * 22) + 0.35 * np.sin(2 * np.pi * 6.2 * t) * np.minimum(1, t / 0.15)   # wonky bend + vibrato
    f = note(n) * 2 ** (semis / 12); ph = np.cumsum(f) / SR
    sq = np.sign(np.sin(2 * np.pi * ph)) + np.sign(np.sin(2 * np.pi * ph * 1.006)) * 0.7
    return lp(sq, 3800) * np.exp(-t * 6.5) * 0.36
def stab(ns, d=0.16):
    t = np.arange(int(d * SR)) / SR; x = sum(saw(note(n), d)[0] for n in ns)
    return lp(x, 2600) * np.exp(-t * 16) * 0.26
def boing(t0, up=True):
    t = np.arange(int(0.45 * SR)) / SR; f = (220 if up else 660) * 2 ** ((2.2 if up else -2.2) * (1 - np.exp(-t * 9)))
    put(np.sin(2 * np.pi * np.cumsum(f) / SR) * np.exp(-t * 6) * 0.35, t0, 1.0, 0.25 if up else -0.25)
def riser(t0, d):
    t = np.arange(int(d * SR)) / SR; n = rng.standard_normal(len(t))
    x = np.concatenate([bp(n[i:i + 2400], 300 + 6000 * (i / len(t)) ** 2, 800 + 9000 * (i / len(t)) ** 2) for i in range(0, len(t), 2400)])[: len(t)]
    put(x * (t / d) ** 2 * 0.5, t0)
def impact(t0):
    t = np.arange(int(1.6 * SR)) / SR; f = 38 + 60 * np.exp(-t * 6)
    put(np.sin(2 * np.pi * np.cumsum(f) / SR) * np.exp(-t * 2.6) * 1.1 + lp(rng.standard_normal(len(t)), 900) * np.exp(-t * 5) * 0.3, t0)
def pad(ns, t0, d):
    t = np.arange(int(d * SR)) / SR; x = sum(saw(note(n), d)[0] + saw(note(n) * 1.004, d)[0] for n in ns)
    put(lp(x, 900) * np.minimum(1, t / 0.6) * np.minimum(1, (d - t) / 0.5) * 0.05, t0)

E2, G2, A2, B2, D3, E3 = 40, 43, 45, 47, 50, 52
BASS_A = {0: E2, 3: E3, 4: G2, 6: A2, 7: B2, 10: D3, 11: E3, 12: E2, 14: G2}
BASS_B = {0: E2, 2: E2, 3: G2, 5: A2, 8: B2, 10: A2, 11: G2, 13: D3, 14: E2}
BASS_C = {0: A2, 3: E3 - 7 + 12, 4: A2, 6: B2, 7: D3, 10: E3, 12: A2, 14: G2}   # over A7
CH_EM = [64, 67, 71, 74]; CH_A = [61, 64, 67, 69]
LEAD = [{0: 71, 3: 74, 6: 76, 8: 79, 11: 76, 14: 74}, {0: 76, 2: 74, 4: 71, 7: 69, 10: 67, 12: 71}]

beats = []
for bar in range(BARS):
    t0 = bar * 4 * BEAT; sec = ("intro" if bar < 2 else "groove" if bar < 6 else "lead" if bar < 14 else
                                "break" if bar < 16 else "lead2" if bar < 22 else "down" if bar < 24 else "outro")
    chord = CH_EM if bar % 2 == 0 else CH_A
    bl = BASS_C if bar % 2 else (BASS_A if bar % 4 == 0 else BASS_B)
    for b in range(4): beats.append(round(t0 + b * BEAT, 4))
    if sec == "break":
        for b in range(4):
            put(kick(), t0 + b * BEAT, 0.9); put(stab([n + 12 for n in chord], 0.22), t0 + b * BEAT, 1.4, 0.15 * (-1) ** b)
            if b % 2: put(snare(), t0 + b * BEAT, 0.7)
        boing(t0 + 3.5 * BEAT, up=bar == 15); continue
    if sec == "down":
        pad(chord, t0, 4 * BEAT)
        for s, n in bl.items():
            if s % 4 == 0: put(bass(n, 0.3), t0 + swing(s), 0.8)
        for s in range(0, 16, 2): put(hat(), t0 + swing(s), 0.18, 0.4)
        if bar == 23: riser(t0, 4 * BEAT)
        continue
    # drums
    for s in (0, 7, 10): put(kick(), t0 + swing(s), 0.95 if sec != "intro" else 0.7)
    if sec != "intro":
        for s in (4, 12): put(snare(), t0 + swing(s), 0.75)
        for s in (9, 15): put(snare(), t0 + swing(s), 0.18)
        if sec in ("groove", "lead", "lead2", "outro"):
            for s in (2, 6, 10): put(stab(chord), t0 + swing(s), 1.0, -0.2)
    for s in range(16):
        if s == 14 and sec in ("lead", "lead2"): put(hat(True), t0 + swing(s), 0.22, 0.35)
        elif s % 2 == 0 or sec in ("lead2",): put(hat(), t0 + swing(s), 0.28 if s % 4 == 0 else 0.18, 0.35)
    for s, n in bl.items(): put(bass(n), t0 + swing(s), 1.0)
    if sec in ("lead", "lead2", "outro"):
        ph = LEAD[bar % 2]
        for s, n in ph.items(): put(pluck(n + (12 if sec == "lead2" and bar % 4 == 3 else 0)), t0 + swing(s), 1.0, 0.3 * (-1) ** s)
    if sec == "intro" and bar == 1: boing(t0 + 3 * BEAT); riser(t0 + 2 * BEAT, 2 * BEAT)
impact(24 * 4 * BEAT)            # logo hit (bar 24)
put(stab([n + 12 for n in CH_EM], 0.5), BARS * 4 * BEAT, 1.6); put(kick(), BARS * 4 * BEAT, 1.0); boing(BARS * 4 * BEAT + 0.05)

mix = np.stack([L, R], 1); mix = np.tanh(mix * 0.9); mix /= np.max(np.abs(mix)) / 0.89
fade = int(0.35 * SR); mix[-fade:] *= np.linspace(1, 0, fade)[:, None]
wavfile.write("funk_bed.wav", SR, (mix * 32767).astype(np.int16))
json.dump({"bpm": BPM, "beat": BEAT, "bars": BARS, "beats": beats, "logo_hit": round(24 * 4 * BEAT, 4), "end_hit": round(BARS * 4 * BEAT, 4)}, open("beats.json", "w"), indent=0)
print("ok", round(BARS * 4 * BEAT, 3), "s of bars + tail", round(TAIL, 3))
