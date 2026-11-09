"""Three more POSH reels on the same drawing engine: order tracking, how it is packed, unboxing.
python3 reels2.py still <reel> out.jpg <seconds>   |   python3 reels2.py video <reel>     (reel = track | pack | unbox)
"""
import os, sys, subprocess
import numpy as np
import explainer_reel as E
from explainer_reel import *

FOAM = '#ddd8ec'
INKBLUE = (27, 31, 77)

def bg_static(a, glow=BERRY, table=1420):
    fv = a.fv; fv.backdrop(mix(NIGHT, PLUM, 0.3))
    fv.glow(540, 1150, 620, 620, glow, rings=4, alpha=0.1)
    fv.stars(40, box=(0, 420, W, 900), size=(1.0, 2.4))
    fv.fill(fv.rect(-20, table, W + 20, H + 20), tone(NIGHT, -0.25), lift=12)

def sparkles(a, u, t0, pts):
    for i, (x, y, r) in enumerate(pts):
        kk = pop(u, t0 + i * 0.07, 0.4) * (0.8 + 0.2 * np.sin(u * 6 + i * 1.9))
        if kk > 0.05: a.fv.sparkle(x, y, r * kk, colour=SAND if i % 2 else CREAM)

# ================================================================== track: a phone timeline that fills in
def t1_static(a):
    fv = a.fv; fv.backdrop(NIGHT); fv.glow(540, 1090, 660, 660, PLUM, rings=4, alpha=0.18)
    fv.stars(60, box=(0, 420, W, 1640), size=(1.0, 2.6))

def t1(a, u):
    fv = a.fv; k = pop(u, 0.15, 0.6); c = (540, 1090); dy = 6 * np.sin(u * 1.6)
    a.rect(290, 610 + dy, 790, 1570 + dy, CREAM, r=64, k=k, c=c, lift=14)
    a.rect(312, 632 + dy, 768, 1548 + dy, mix(NIGHT, PLUM, 0.28), r=46, k=k, c=c, texture=0)
    a.cap((490, 672 + dy), (590, 672 + dy), 10, CREAM, k=k, c=c, alpha=0.45, texture=0)
    if k < 0.9: return
    a.text('Your order', 540, 752 + dy, 34, 600, alpha=0.7 * seg(u, 0.5, 0.9))
    names = ['Order placed', 'Packed', 'On the way', 'Delivered']; ys = [870 + dy + i * 170 for i in range(4)]; x = 392
    T0 = [1.1 + i * 1.9 for i in range(4)]
    a.cap((x, ys[0]), (x, ys[3]), 5, CREAM, alpha=0.2, texture=0)
    for i in range(3):
        g = eio(seg(u, T0[i] + 0.35, T0[i + 1]))
        if g > 0.01: a.cap((x, ys[i]), (x, ys[i] + 170 * g), 5, EMBER, texture=0)
    for i in range(4):
        y = ys[i]; act = seg(u, T0[i], T0[i] + 0.3); kk = pop(u, 0.55 + i * 0.1, 0.4)
        rp = seg(u, T0[i], T0[i] + 0.7)
        if 0 < rp < 1: a.ring(x, y, 28 + 56 * rp, 6, GREEN, alpha=1 - rp)
        a.circ(x, y, 28, mix(mix(NIGHT, CREAM, 0.3), GREEN, act), k=kk * (1 + 0.15 * np.sin(np.pi * act)), texture=0)
        if act > 0.5: a.check(x, y, 27, k=pop(u, T0[i] + 0.1, 0.3))
        a.text(names[i], 444, y - 2, 34, 700, alpha=(0.35 + 0.65 * act) * cl(kk), anchor='lm')
        ki = pop(u, T0[i] + 0.05, 0.4); ix = 716; ic = (ix, y)
        if i == 0:
            a.rect(ix - 24, y - 32, ix + 24, y + 32, CREAM, r=5, k=ki, c=ic, texture=0)
            for j in range(3): a.cap((ix - 12, y - 14 + j * 14), (ix + (12 if j < 2 else 2), y - 14 + j * 14), 3, DARK, k=ki, c=ic, texture=0)
        elif i == 1: E.box(a, ix, y + 30, 64, 58, k=ki, label=False, lift=3)
        elif i == 2:
            a.rect(ix - 34, y - 24, ix + 10, y + 16, CREAM, r=5, k=ki, c=ic, texture=0)
            a.rect(ix + 10, y - 8, ix + 34, y + 16, EMBER, r=4, k=ki, c=ic, texture=0)
            a.circ(ix - 18, y + 18, 9, DARK, k=ki, c=ic, texture=0); a.circ(ix + 20, y + 18, 9, DARK, k=ki, c=ic, texture=0)
        else:
            a.poly([(ix - 28, y + 28), (ix - 28, y - 4), (ix, y - 32), (ix + 28, y - 4), (ix + 28, y + 28)], CREAM, r=4, k=ki, c=ic, texture=0)
            a.rect(ix - 8, y + 2, ix + 8, y + 28, EMBER, r=2, k=ki, c=ic, texture=0)
    sparkles(a, u, T0[3] + 0.3, [(220, 700, 22), (880, 760, 26), (180, 1300, 16), (900, 1380, 20), (860, 560, 16), (230, 520, 18)])

# ================================================================== pack: sleeve, foam, seal
def p_static(a): bg_static(a)

def p1(a, u):
    k = pop(u, 0.15); y0 = 1150 + 7 * np.sin(u * 2.0)
    a.rect(220, y0, 860, y0 + 90, tone(CREAM, -0.04), r=28, k=k, c=(540, y0 + 45), lift=12)
    a.cap((490, y0 + 45), (590, y0 + 45), 5, tone(CREAM, -0.25), k=k, c=(540, y0 + 45), texture=0)
    g = eio(seg(u, 1.2, 2.6))
    if g > 0.01:
        xl = lerp(900, 190, g)
        a.rect(xl, y0 - 20, 900, y0 + 110, TEAL, r=32, lift=9)
        if 900 - xl > 120: a.cap((xl + 40, y0 + 88), (870, y0 + 88), 3, CREAM, alpha=0.5, texture=0)
    kt = pop(u, 2.8, 0.4)
    a.rect(820, y0 + 20, 930, y0 + 70, tone(EMBER, 0.05), r=10, k=kt, c=(875, y0 + 45), lift=4)      # the tab that closes it
    sparkles(a, u, 3.0, [(200, 980, 22), (880, 940, 26), (540, 900, 18), (330, 1360, 14), (760, 1370, 16)])

def p2(a, u):
    kb = pop(u, 0.1); c = (540, 1420)
    a.rect(270, 1030, 810, 1420, CARD, r=10, k=kb, c=c, lift=11)
    a.rect(302, 1062, 778, 1390, tone(SAND, -0.5), r=6, k=kb, c=c, texture=0)                          # cutaway: we see inside
    if kb < 0.9: return
    a.rect(400, 1318, 680, 1384, FOAM, r=8, k=pop(u, 0.5, 0.4), c=(540, 1384), texture=0.5)
    d = eio(seg(u, 1.0, 1.9)); ly = lerp(720, 1244, d) + (1 - d) * 7 * np.sin(u * 3)
    a.rect(400, ly, 680, ly + 70, TEAL, r=22, k=pop(u, 0.6, 0.4), c=(540, ly + 35), lift=8 if d < 1 else 0)
    for sgn, t0 in ((-1, 2.1), (1, 2.3)):
        s = eo(seg(u, t0, t0 + 0.6)); x = lerp(540 + sgn * 700, 351 if sgn < 0 else 729, s)
        a.rect(x - 39, 1072, x + 39, 1384, FOAM, r=8, texture=0.5, lift=4 if s < 1 else 0)
    s = eo(seg(u, 2.95, 3.5))
    if u > 2.9: a.rect(400, lerp(800, 1072, s), 680, lerp(800, 1072, s) + 164, FOAM, r=8, alpha=cl(seg(u, 2.9, 3.05)), texture=0.5, lift=4 if s < 1 else 0)
    kc = pop(u, 3.7, 0.4)
    a.circ(800, 1040, 58, GREEN, k=kc, lift=6); a.check(800, 1040, 58, k=kc if kc > 0.5 else 0)
    sparkles(a, u, 3.9, [(200, 960, 20), (900, 900, 16), (170, 1250, 14)])

def p3(a, u):
    k = pop(u, 0.15, 0.6); hop = 16 * np.sin(np.pi * seg(u, 3.3, 3.7))
    y = 1420 - hop
    E.box(a, 540, y, 520, 340, k=k, label=False, lift=12)
    if k < 0.9: return
    tp = eo(seg(u, 1.0, 1.7))
    if tp > 0.01: a.rect(280, y - 226, 280 + 520 * tp, y - 182, EMBER, texture=0.5)
    kl = pop(u, 1.9, 0.4)
    a.rect(318, y - 152, 548, y - 38, CREAM, r=8, k=kl, c=(433, y - 95), lift=4)
    if kl > 0.9:
        a.text('POSH', 433, y - 110, 54, 800, INKBLUE)
        for i in range(9): a.rect(350 + i * 19, y - 70, 350 + i * 19 + (6 if i % 3 else 11), y - 52, DARK, texture=0)
    kf = pop(u, 2.4, 0.4)
    a.rect(590, y - 128, 772, y - 66, EMBER, r=10, k=kf, c=(681, y - 97), lift=4)
    if kf > 0.9: a.text('FRAGILE', 681, y - 97, 30, 800)
    kc = pop(u, 2.95, 0.4)
    a.circ(806, y - 346, 60, GREEN, k=kc, lift=6); a.check(806, y - 346, 60, k=kc if kc > 0.5 else 0)
    sparkles(a, u, 3.2, [(190, 1000, 22), (900, 1230, 18), (250, 860, 16), (700, 900, 20), (150, 1300, 14)])

# ================================================================== unbox
def u1(a, u):
    fv = a.fv; X0, X1, TOP, BOT = 300, 780, 1100, 1420
    kb = pop(u, 0.15, 0.6); c = (540, 1420); opn = eio(seg(u, 1.9, 2.6))
    g = eo(seg(u, 2.3, 3.3))
    if g > 0.05: fv.glow(540, TOP - 60, 460 * g, 460 * g, LIGHT, rings=4, alpha=0.1)
    a.poly([(X0, TOP), (X1, TOP), (X1 - 44, lerp(TOP - 6, 950, opn)), (X0 + 44, lerp(TOP - 6, 950, opn))], tone(SAND, -0.26), r=6, k=kb, c=c, texture=0)
    a.rect(X0, TOP, X1, BOT, tone(SAND, -0.5), r=8, k=kb, c=c, texture=0)
    r = eio(seg(u, 2.7, 3.9)); ly = lerp(1190, 790, r) + r * 7 * np.sin(u * 3)
    if u > 2.6:
        a.rect(340, ly, 740, ly + 54, tone(CREAM, -0.04), r=17, lift=10 if r > 0.4 else 0)
        a.cap((500, ly + 27), (580, ly + 27), 4, tone(CREAM, -0.25), texture=0)
    a.rect(X0, TOP, X1, BOT, CARD, r=8, k=kb, c=c, lift=11)
    a.rect(660, TOP, X1, BOT, tone(SAND, -0.28), r=[0, 8, 8, 0], k=kb, c=c, texture=0)
    if kb > 0.9:
        for hx, sgn in ((X0, 1), (X1, -1)):
            th = np.radians(lerp(0, 118, opn)); L = 238
            ex, ey = hx + sgn * L * np.cos(th), TOP - L * np.sin(th); nx, ny = -np.sin(th) * 15 * sgn, -np.cos(th) * 15
            fv.fill(fv.poly([(hx, TOP), (ex, ey), (ex + nx, ey + ny), (hx + nx, TOP + ny)], round=3), tone(SAND, 0.12 if sgn > 0 else 0.0), lift=4)
        tp = 1 - eo(seg(u, 1.1, 1.75))
        if tp > 0.02: a.rect(500, TOP - 16, 580, TOP - 16 + 170 * tp, EMBER, r=4, texture=0.5)
        a.rect(346, 1250, 566, 1356, CREAM, r=8, lift=4); a.text('POSH', 456, 1303, 54, 800, INKBLUE)
    sparkles(a, u, 3.7, [(230, 760, 24), (860, 720, 28), (540, 660, 20), (160, 980, 16), (930, 960, 18), (360, 620, 14)])

def u2(a, u):
    fv = a.fv; x, y = 540, 1296
    k = pop(u, 0.15); op = eio(seg(u, 0.8, 2.0)); on = eo(seg(u, 2.2, 2.75)); c = (x, y)
    if k < 0.03: return
    if on > 0.05: fv.glow(540, 1040, 520 * on, 520 * on, LIGHT, rings=4, alpha=0.07)
    lh = lerp(20, 402, op)
    a.rect(250, y - 44 - lh, 830, y - 44, tone(CREAM, -0.1), r=min(22, lh / 2), k=k, c=c, lift=12)
    if lh > 90: a.rect(274, y - 44 - lh + 24, 806, y - 70, mix(mix(NIGHT, PLUM, 0.22), mix(SAND, CREAM, 0.45), on), r=8, texture=0)
    a.poly([(x - 350, y - 46), (x + 350, y - 46), (x + 390, y), (x - 390, y)], CREAM, r=6, k=k, c=c, lift=8)
    a.cap((x - 60, y - 32), (x + 60, y - 32), 5, tone(CREAM, -0.25), k=k, c=c, texture=0)
    if on > 0.4:
        a.text('hello', 540, y - 262, 120, 600, INKBLUE, alpha=cl((on - 0.4) * 2))
        a.heart(540, y - 150, 54, k=pop(u, 2.9, 0.4))
    sparkles(a, u, 2.8, [(190, 900, 20), (890, 860, 24), (150, 1090, 14), (930, 1080, 16), (300, 760, 15), (780, 740, 18), (540, 700, 22)])

END = lambda cap: dict(dur=4.6, st=E.s5_static, dy=E.s5, step=None, title=None, cap=cap)
REELS = {
    'track': dict(file='reel-2-track-your-order', music=(108, 62, 2), scenes=[
        dict(dur=9.6, st=t1_static, dy=t1, step='TRACKING', title='Follow it\nevery step', cap='Updates from order to doorstep.'),
        END('Always know where your laptop is.')]),
    'pack': dict(file='reel-3-how-we-pack', music=(96, 65, 3), scenes=[
        dict(dur=4.5, st=p_static, dy=p1, step='STEP 1', title='Wrapped in\na soft sleeve', cap='A snug layer against scratches.'),
        dict(dur=4.9, st=p_static, dy=p2, step='STEP 2', title='Cushioned\non every side', cap='Foam holds it firmly in place.'),
        dict(dur=4.7, st=p_static, dy=p3, step='STEP 3', title='Sealed and\nlabelled', cap='Ready for a safe journey.'),
        END('Packed like it is our own.')]),
    'unbox': dict(file='reel-4-unboxing', music=(112, 67, 4), scenes=[
        dict(dur=5.2, st=p_static, dy=u1, step='DELIVERY DAY', title='The best part:\nunboxing', cap='Peel, open, lift.'),
        dict(dur=4.9, st=E.s1_static, dy=u2, step='READY', title='Open it up\nand say hello', cap='Your new laptop is ready.'),
        END('Enjoy your new POSH.')]),
}

def use(name):
    r = REELS[name]; E.SCENES = r['scenes']; E.STARTS = np.cumsum([0] + [s['dur'] for s in E.SCENES]); E.DUR = float(E.STARTS[-1]); E._bg.clear(); return r

if __name__ == '__main__':
    mode, name = sys.argv[1], sys.argv[2]; r = use(name)
    if mode == 'still':
        E.frame(float(sys.argv[4])).save(sys.argv[3], quality=92, subsampling=0); sys.exit()
    from multiprocessing import Pool
    fd = os.path.join(E.HERE, 'frames'); subprocess.run(['rm', '-rf', fd]); os.makedirs(fd)
    N = int(round(E.DUR * FPS))
    with Pool(4) as p: list(p.imap_unordered(E.work, range(N), chunksize=6))
    sp = os.environ.get('SCRATCH', '/tmp'); silent, wav = f'{sp}/{name}.mp4', f'{sp}/{name}.wav'
    subprocess.run(['ffmpeg', '-y', '-loglevel', 'error', '-r', str(FPS), '-i', fd + '/%05d.jpg', '-c:v', 'libx264', '-pix_fmt', 'yuv420p', '-crf', '18', silent], check=True)
    bpm, root, seed = r['music']; cuts = ','.join(f'{t:.2f}' for t in E.STARTS[1:-1])
    subprocess.run(['/root/lemo-opuscar/.venv/bin/python', os.path.join(E.HERE, 'music.py'), wav, str(E.DUR), str(bpm), str(root), str(seed), cuts], check=True)
    out = os.path.join(E.HERE, r['file'] + '.mp4')
    subprocess.run(['ffmpeg', '-y', '-loglevel', 'error', '-i', silent, '-i', wav, '-c:v', 'copy', '-af', 'loudnorm=I=-14:TP=-1.5:LRA=11', '-c:a', 'aac', '-b:a', '192k',
                    '-shortest', '-movflags', '+faststart', out], check=True)
    subprocess.run(['rm', '-rf', fd]); print('done', out, E.DUR)
