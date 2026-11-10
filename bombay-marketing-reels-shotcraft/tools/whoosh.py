"""Synthesise the one sound effect of the series: a soft air whoosh for the shot-to-shot push (original, no third-party audio).
   python3 tools/whoosh.py  →  public/audio/whoosh.wav   (deterministic: fixed seed)"""
import numpy as np, wave, os
sr, dur = 48000, 0.62
rng = np.random.default_rng(7)
n = int(sr * dur); t = np.arange(n) / sr
x = rng.standard_normal(n)
# time-varying band: cutoff rises then falls (air moving past), one-pole low-pass + one-pole high-pass
cut = 500 + 3200 * np.sin(np.pi * t / dur) ** 2
y = np.zeros(n); lp = 0.0
for i in range(n):
    a = 1 - np.exp(-2 * np.pi * cut[i] / sr); lp += a * (x[i] - lp); y[i] = lp
hp = np.zeros(n); prev_x = prev_y = 0.0; r = np.exp(-2 * np.pi * 180 / sr)
for i in range(n):
    prev_y = r * (prev_y + y[i] - prev_x); prev_x = y[i]; hp[i] = prev_y
env = np.sin(np.pi * np.clip(t / dur, 0, 1)) ** 2.4           # slow swell, slow release: no click, no attack
out = hp * env; out = out / np.max(np.abs(out)) * 10 ** (-12 / 20)
p = os.path.join(os.path.dirname(os.path.abspath(__file__)), '..', 'public/audio/whoosh.wav')
with wave.open(p, 'wb') as w:
    w.setnchannels(1); w.setsampwidth(2); w.setframerate(sr); w.writeframes((out * 32767).astype('<i2').tobytes())
print('whoosh.wav', dur, 's, peak -12 dBFS, energy peak at', round(float(t[np.argmax(np.abs(out))]), 3), 's')
