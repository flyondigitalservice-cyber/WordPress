"""POSH explainer reel - how an order becomes a laptop at the customer's door.
1080x1920, 24 fps, flat-vector drawing (same engine and palette as the lighthouse reel), every frame = f(t).
python3 explainer_reel.py still out.jpg <seconds>   |   python3 explainer_reel.py video out.mp4
"""
import os, sys, subprocess
import numpy as np
from PIL import Image, ImageDraw, ImageFont
sys.path.insert(0, os.path.expanduser('~/.claude/skills/claude-drawing/lib'))
from flatvector import FlatVector, tone, mix, NIGHT, PLUM, BERRY, EMBER, SAND, PINE, CREAM

W, H, FPS = 1080, 1920, 24
HERE = os.path.dirname(os.path.abspath(__file__))
FONT = os.path.join(HERE, 'fonts', 'Inter.ttf')
GREEN, SKIN, TEAL, DARK = '#46b381', '#f1c59c', '#3f9c95', '#14152b'
LIGHT = tone(SAND, 0.55)
CARD = tone(SAND, -0.08)                                    # cardboard

# ------------------------------------------------------------------ easing
cl = lambda x: max(0.0, min(1.0, x))
seg = lambda t, a, b: cl((t - a) / (b - a))
eio = lambda x: (lambda x: 4 * x ** 3 if x < .5 else 1 - (-2 * x + 2) ** 3 / 2)(cl(x))
eo = lambda x: 1 - (1 - cl(x)) ** 3
def back(x, s=1.9): x = cl(x) - 1; return 1 + x * x * ((s + 1) * x + s)
pop = lambda u, t0, d=0.5: back(seg(u, t0, t0 + d))
lerp = lambda a, b, t: a + (b - a) * t
def S(pts, c, k): return [(c[0] + (x - c[0]) * k, c[1] + (y - c[1]) * k) for x, y in pts]


class Art:
    """thin helpers over FlatVector: everything takes a scale k about a centre c so parts can pop in"""
    def __init__(self, fv): self.fv = fv; self.texts = []
    def rect(self, x0, y0, x1, y1, col, r=0, k=1, c=None, **kw):
        if k < 0.03: return
        c = c or ((x0 + x1) / 2, (y0 + y1) / 2)
        (a0, b0), (a1, b1) = S([(x0, y0), (x1, y1)], c, k)
        rr = [q * k for q in r] if isinstance(r, (list, tuple)) else r * k
        return self.fv.fill(self.fv.rect(a0, b0, a1, b1, round=rr), col, **kw)
    def poly(self, pts, col, r=0, k=1, c=None, **kw):
        if k < 0.03: return
        c = c or tuple(np.mean(pts, 0))
        return self.fv.fill(self.fv.poly(S(pts, c, k), round=r * k if not isinstance(r, (list, tuple)) else [q * k for q in r]), col, **kw)
    def circ(self, x, y, r, col, k=1, c=None, **kw):
        if k < 0.03: return
        (x, y), = S([(x, y)], c or (x, y), k)
        return self.fv.fill(self.fv.circle(x, y, r * k), col, **kw)
    def cap(self, p0, p1, r, col, k=1, c=None, **kw):
        if k < 0.03: return
        c = c or ((p0[0] + p1[0]) / 2, (p0[1] + p1[1]) / 2)
        p0, p1 = S([p0, p1], c, k)
        return self.fv.fill(self.fv.capsule(p0, p1, r * k), col, **kw)
    def ring(self, x, y, r, w, col, alpha=1.0):
        if r < w + 1 or alpha <= 0.01: return
        self.fv.fill(self.fv.circle(x, y, r) - self.fv.circle(x, y, r - w), col, alpha=alpha, texture=0)
    def check(self, x, y, s, col=CREAM, k=1):
        self.cap((x - .42 * s, y + .02 * s), (x - .12 * s, y + .32 * s), .11 * s, col, k=k, c=(x, y), texture=0)
        self.cap((x - .12 * s, y + .32 * s), (x + .44 * s, y - .30 * s), .11 * s, col, k=k, c=(x, y), texture=0)
    def heart(self, x, y, s, col=EMBER, k=1):
        if k < 0.03: return
        s *= k; fv = self.fv
        m = fv.circle(x - .26 * s, y - .16 * s, .29 * s) | fv.circle(x + .26 * s, y - .16 * s, .29 * s) | \
            fv.poly([(x - .53 * s, y - .04 * s), (x + .53 * s, y - .04 * s), (x, y + .56 * s)], round=2)
        fv.fill(m, col, texture=0)
    def text(self, s, x, y, size, weight=700, col=(246, 239, 228), alpha=1.0, anchor='mm', spacing=0):
        if alpha > 0.01: self.texts.append((s, x, y, size, weight, col, alpha, anchor, spacing))


def box(a, x, y, w, h, k=1, label=True, lift=9):
    """a sealed cardboard parcel standing on (x, y) = bottom centre"""
    c = (x, y)
    a.rect(x - w / 2, y - h, x + w / 2, y, CARD, r=8, k=k, c=c, lift=lift)
    a.rect(x + w * .22, y - h, x + w / 2, y, tone(SAND, -0.28), r=[0, 8, 8, 0], k=k, c=c, texture=0)
    a.rect(x - w * .09, y - h, x + w * .09, y - h * .58, EMBER, k=k, c=c, texture=0)
    if label:
        a.rect(x - w * .38, y - h * .44, x + w * .08, y - h * .12, CREAM, r=5, k=k, c=c, texture=0)
        if k > 0.9: a.text('POSH', x - w * .15, y - h * .28, h * .15, 800, (27, 31, 77))


def laptop(a, x, y, s, k=1, screen=None, lift=12):
    """open laptop, front view; (x, y) = centre of the base's bottom edge, s = scale (1 = 580 px wide lid)"""
    c = (x, y)
    a.rect(x - 290 * s, y - 446 * s, x + 290 * s, y - 44 * s, tone(CREAM, -0.1), r=22 * s, k=k, c=c, lift=lift)
    a.rect(x - 266 * s, y - 422 * s, x + 266 * s, y - 70 * s, screen if screen is not None else mix(NIGHT, PLUM, 0.22), r=8 * s, k=k, c=c, texture=0)
    a.poly([(x - 350 * s, y - 46 * s), (x + 350 * s, y - 46 * s), (x + 390 * s, y), (x - 390 * s, y)], CREAM, r=6 * s, k=k, c=c, lift=8)
    a.cap((x - 60 * s, y - 32 * s), (x + 60 * s, y - 32 * s), 5 * s, tone(CREAM, -0.25), k=k, c=c, texture=0)


# ================================================================== scenes: static(a) once, then dynamic(a, u)
def s1_static(a):
    fv = a.fv; fv.backdrop(NIGHT)
    fv.glow(540, 1060, 600, 600, PLUM, rings=4, alpha=0.2)
    fv.stars(46, box=(0, 420, W, 860), size=(1.0, 2.6))
    fv.fill(fv.rect(-20, 1300, W + 20, H + 20), tone(PLUM, -0.38), lift=12)
    a.rect(848, 1210, 928, 1300, CREAM, r=[6, 6, 16, 16], lift=6)                     # mug
    a.ring(934, 1250, 26, 9, CREAM)
    a.poly([(112, 1300), (212, 1300), (228, 1214), (96, 1214)], EMBER, r=6, lift=6)   # plant
    for rot, dx, h in ((-28, -34, 150), (0, 0, 190), (27, 36, 150)):
        fv.fill(fv.ellipse(162 + dx, 1214 - h / 2 + 10, 26, h / 2, rot=-rot), tone(PINE, 0.12 if dx else 0), lift=3)

def s1(a, u):
    fv = a.fv
    k = pop(u, 0.15)
    laptop(a, 540, 1296, 1.0, k)
    kc = pop(u, 0.55, 0.45); c = (540, 1050)
    a.rect(330, 925, 520, 1055, PLUM, r=12, k=kc, c=c, texture=0)                      # product picture
    a.rect(356, 946, 494, 1016, tone(SAND, 0.2), r=5, k=kc, c=c, texture=0)
    a.cap((346, 1032), (504, 1032), 6, CREAM, k=kc, c=c, texture=0)
    for i, (x1, r, al) in enumerate(((760, 11, .95), (700, 8, .5), (730, 8, .5))):
        kk = pop(u, 0.7 + i * 0.1, 0.4)
        a.cap((560, 946 + i * 40), (560 + (x1 - 560) * kk, 946 + i * 40), r, CREAM, alpha=al * cl(kk * 2), texture=0)
    click = 2.15
    done = seg(u, click + 0.08, click + 0.3)
    press = 1 - 0.07 * np.sin(np.pi * seg(u, click - 0.06, click + 0.2))
    kb = pop(u, 1.0, 0.45) * press
    a.rect(330, 1112, 750, 1188, mix(EMBER, GREEN, done), r=38, k=kb, c=(540, 1150), texture=0)
    if kb > 0.9:
        if done < 0.5: a.text('Buy now', 540, 1149, 40, 700, alpha=1 - done * 2)
        else:
            a.text('Ordered', 520, 1149, 40, 700, alpha=done * 2 - 1); a.check(640, 1146, 34, k=pop(u, click + 0.2, 0.35))
    rp = seg(u, click, click + 0.6)
    a.ring(600, 1160, 26 + 120 * eo(rp), 8, CREAM, alpha=(1 - rp) * 0.8 if u > click else 0)
    for i, (x, y, r) in enumerate(((190, 900, 20), (890, 860, 24), (150, 1090, 14), (930, 1080, 16), (300, 760, 15), (780, 740, 18), (540, 700, 22))):
        kk = pop(u, click + 0.25 + i * 0.06, 0.4) * (0.82 + 0.18 * np.sin(u * 7 + i * 2))
        if kk > 0.05: fv.sparkle(x, y, r * kk, colour=SAND if i % 2 else CREAM)
    # the cursor glides in, clicks, drifts away
    m = eio(seg(u, 1.15, 2.05)); out = eio(seg(u, 2.9, 4.2))
    px, py = lerp(900, 596, m) + 150 * out, lerp(1620, 1150, m) + 90 * out + 40 * np.sin(np.pi * m)
    ks = 1 - 0.18 * np.sin(np.pi * seg(u, click - 0.08, click + 0.16))
    arrow = [(0, 0), (0, 76), (19, 58), (35, 92), (52, 84), (36, 52), (62, 52)]
    P = [(px + x * ks, py + y * ks) for x, y in arrow]
    fv.fill(fv.poly(S(P, (px + 20, py + 44), 1.22), round=5), DARK, texture=0)
    fv.fill(fv.poly(P, round=3), CREAM, lift=5)


def s2_static(a):
    fv = a.fv; fv.backdrop(mix(NIGHT, PLUM, 0.3))
    fv.glow(540, 1150, 620, 620, BERRY, rings=4, alpha=0.1)
    fv.fill(fv.rect(-20, 1420, W + 20, H + 20), tone(NIGHT, -0.25), lift=12)
    a.rect(120, 1330, 250, 1420, tone(EMBER, -0.1), r=10, lift=6)                      # tape roll
    a.circ(185, 1375, 22, tone(NIGHT, -0.25), texture=0)
    a.rect(860, 1300, 990, 1420, CARD, r=8, lift=6); a.rect(915, 1300, 935, 1350, EMBER, texture=0)   # a spare box

def s2(a, u):
    fv = a.fv
    kb = pop(u, 0.1); c = (540, 1420)
    X0, X1, TOP, BOT = 300, 780, 1100, 1420
    close = eio(seg(u, 2.35, 2.95))
    a.poly([(X0, TOP), (X1, TOP), (X1 - 44, lerp(950, TOP - 6, close)), (X0 + 44, lerp(950, TOP - 6, close))], tone(SAND, -0.26), r=6, k=kb, c=c, texture=0)   # rear flap
    a.rect(X0, TOP, X1, BOT, tone(SAND, -0.5), r=8, k=kb, c=c, texture=0)             # inside
    # the laptop: shown, checked, lowered in
    kl = pop(u, 0.4, 0.5); down = eio(seg(u, 1.45, 2.3))
    ly = lerp(770, 1190, down) + (1 - down) * 9 * np.sin(u * 3.2)
    if kl > 0.03:
        a.rect(350, ly, 730, ly + 50, tone(CREAM, -0.04), r=16, k=kl, c=(540, ly + 25), lift=10 if down < 0.6 else 0)
        a.cap((500, ly + 25), (580, ly + 25), 4, tone(CREAM, -0.25), k=kl, c=(540, ly + 25), texture=0)
    sweep = seg(u, 0.75, 1.3)                                                           # the inspection: a light bar passes over it
    if 0 < sweep < 1: a.rect(lerp(350, 700, sweep), ly - 16, lerp(350, 700, sweep) + 30, ly + 66, LIGHT, r=10, alpha=0.55 * np.sin(np.pi * sweep), texture=0)
    kc = pop(u, 1.05, 0.35) * (1 - seg(u, 1.6, 1.85))
    a.circ(742, ly - 14, 46, GREEN, k=kc, lift=5); a.check(742, ly - 14, 46, k=kc if kc > 0.5 else 0)
    # front panel hides what went in
    a.rect(X0, TOP, X1, BOT, CARD, r=8, k=kb, c=c, lift=11)
    a.rect(660, TOP, X1, BOT, tone(SAND, -0.28), r=[0, 8, 8, 0], k=kb, c=c, texture=0)
    # side flaps swing shut
    for hx, sgn in ((X0, 1), (X1, -1)):
        th = np.radians(lerp(118, 0, close)); L = 238
        ex, ey = hx + sgn * L * np.cos(th), TOP - L * np.sin(th)
        nx, ny = -np.sin(th) * 15 * sgn, -np.cos(th) * 15
        if kb > 0.9: fv.fill(fv.poly([(hx, TOP), (ex, ey), (ex + nx, ey + ny), (hx + nx, TOP + ny)], round=3), tone(SAND, 0.12 if sgn > 0 else 0.0), lift=4)
    tp = eo(seg(u, 2.95, 3.35))                                                         # tape
    if tp > 0: a.rect(500, TOP - 16, 580, TOP - 16 + 170 * tp, EMBER, r=4, texture=0.5)
    kl2 = pop(u, 3.3, 0.4)                                                              # label
    a.rect(346, 1250, 566, 1356, CREAM, r=8, k=kl2, c=(456, 1303), lift=4)
    if kl2 > 0.9:
        a.text('POSH', 456, 1290, 50, 800, (27, 31, 77))
        for i in range(9): a.rect(372 + i * 19, 1326, 372 + i * 19 + (6 if i % 3 else 11), 1344, DARK, texture=0)
    a.heart(690, 1310, 74, k=pop(u, 3.55, 0.4))


def hill(fv, base, amp, wl, shift, col, lift=8):
    xs = np.linspace(-80, W + 80, 15)
    return fv.land([(x, base + amp * np.sin((x + shift) / wl) + amp * .4 * np.sin((x + shift) / (wl * .37) + 1)) for x in xs], col, lift=lift)

def s3_static(a):
    fv = a.fv; fv.backdrop(NIGHT)
    fv.fill(fv.rect(0, 900, W, 1400), mix(NIGHT, PLUM, 0.35), alpha=0.4, texture=0)
    fv.moon(850, 640, 74, rays=16)
    fv.stars(90, box=(0, 420, W, 1080), avoid=[(850, 640, 180)])

def s3(a, u):
    fv = a.fv
    hill(fv, 1150, 46, 190, 70 * u, tone(PLUM, -0.34), lift=5)
    hill(fv, 1262, 30, 130, 170 * u + 300, PLUM, lift=8)
    for i in range(6):
        x = (i * 260 + 90 - 170 * u) % 1500 - 200
        y = 1262 + 30 * np.sin((x + 170 * u + 300) / 130) + 12 * np.sin((x + 170 * u + 300) / 48.1 + 1)
        fv.pine(x, y + 16, 120 + 40 * (i % 3), tone(PINE, -0.1), tiers=2 + i % 2, lift=3)
    fv.fill(fv.rect(-20, 1372, W + 20, H + 20), tone(NIGHT, -0.3), lift=10)
    for i in range(7):
        x = (i * 230 - 560 * u) % 1610 - 200
        a.cap((x, 1484), (x + 110, 1484), 6, CREAM, alpha=0.65, texture=0)
    dx = lerp(-940, 0, eo(seg(u, 0.1, 1.2))) + 14 * np.sin(u * 1.3); by = 4 * np.sin(u * 10)
    X = lambda x: x + dx
    Y = lambda y: y + by
    for i in range(5):                                                                  # speed lines
        ln = 70 + 60 * (0.5 + 0.5 * np.sin(u * 5 + i * 1.9)); yy = 1120 + i * 46
        a.cap((X(214 - ln - 26 * (i % 2)), yy), (X(214 - 26 * (i % 2)), yy), 5, CREAM, alpha=0.5, texture=0)
    fv.fill(fv.poly([(X(800), Y(1262)), (X(1500), Y(1150)), (X(1500), Y(1420)), (X(800), Y(1296))]), LIGHT, alpha=0.2, texture=0)   # headlight
    a.rect(X(236), Y(1080), X(652), Y(1330), CREAM, r=20, lift=12)                     # cargo
    a.rect(X(236), Y(1248), X(652), Y(1288), EMBER, texture=0.5)
    a.poly([(X(652), Y(1146)), (X(742), Y(1146)), (X(812), Y(1236)), (X(812), Y(1330)), (X(652), Y(1330))], EMBER, r=14, lift=10)   # cab
    a.poly([(X(672), Y(1168)), (X(732), Y(1168)), (X(782), Y(1234)), (X(672), Y(1234))], mix(NIGHT, CREAM, 0.3), r=7, texture=0)
    a.circ(X(800), Y(1282), 13, LIGHT, texture=0)
    a.rect(X(276), Y(1118), X(356), Y(1198), CARD, r=6, texture=0); a.rect(X(308), Y(1118), X(324), Y(1150), EMBER, texture=0)
    a.text('POSH', X(505), Y(1160), 78, 800, (27, 31, 77))
    for wx in (346, 706):                                                               # wheels turn
        a.circ(X(wx), 1338, 50, DARK, lift=5); a.circ(X(wx), 1338, 21, CREAM, texture=0)
        ang = u * 9 + dx / 50
        a.cap((X(wx) - 15 * np.cos(ang), 1338 - 15 * np.sin(ang)), (X(wx) + 15 * np.cos(ang), 1338 + 15 * np.sin(ang)), 4, DARK, texture=0)
    # tracking line
    kp = pop(u, 0.9, 0.4); p = eio(seg(u, 1.2, 4.3)); ty = 1588
    if kp > 0.03:
        a.cap((210, ty), (210 + 620 * kp, ty), 7, CREAM, alpha=0.28, texture=0)
        a.cap((210, ty), (210 + 620 * p * kp, ty), 7, EMBER, texture=0)
        a.circ(210 + 620 * p, ty, 17, CREAM, k=kp, lift=4)
        a.poly([(858, ty + 20), (858, ty - 12), (884, ty - 36), (910, ty - 12), (910, ty + 20)], CREAM if p < 0.98 else GREEN, r=4, k=kp, texture=0)


def s4_static(a):
    fv = a.fv; fv.backdrop(mix(PLUM, NIGHT, 0.42))
    for y in range(480, 1420, 78): a.cap((-20, y), (W + 20, y), 2, NIGHT, alpha=0.22, texture=0)   # siding
    fv.fill(fv.rect(-20, 1420, W + 20, H + 20), tone(NIGHT, -0.3), lift=12)
    a.rect(280, 1380, 800, 1424, tone(CREAM, -0.32), r=6, lift=6)                      # step
    fv.glow(850, 930, 210, 210, LIGHT, rings=4, alpha=0.1)                             # porch lamp
    a.poly([(820, 900), (880, 900), (866, 850), (834, 850)], EMBER, r=4, lift=4); a.circ(850, 912, 15, LIGHT, texture=0)
    a.rect(70, 880, 280, 1130, CREAM, r=10, lift=6); a.rect(88, 898, 262, 1112, mix(SAND, CREAM, 0.3), r=4, texture=0)   # window
    a.rect(170, 898, 180, 1112, CREAM, texture=0); a.rect(88, 1000, 262, 1010, CREAM, texture=0)
    a.rect(368, 786, 712, 1380, CREAM, r=[18, 18, 0, 0], lift=9)                       # door frame
    a.rect(394, 812, 686, 1380, mix(SAND, CREAM, 0.35), r=[8, 8, 0, 0], texture=0)     # the lit hallway
    a.rect(410, 1384, 670, 1404, tone(EMBER, -0.3), r=4, texture=0)                    # mat

def s4(a, u):
    fv = a.fv
    o = eio(seg(u, 2.2, 2.9))
    # the customer, waiting behind the door
    kp = pop(u, 2.3, 0.5); cy = 1380; joy = seg(u, 2.9, 3.3)
    if kp > 0.03:
        c = (540, cy)
        for sgn in (-1, 1):                                                             # arms go up
            ang = np.radians(lerp(-62, 52, eo(joy)) + 7 * np.sin(u * 9 + sgn) * joy)
            sx, sy = 540 + sgn * 62, 1120
            a.cap((sx, sy), (sx + sgn * 120 * np.cos(ang), sy - 120 * np.sin(ang)), 22, TEAL, k=kp, c=c, texture=0)
            a.circ(sx + sgn * 128 * np.cos(ang), sy - 128 * np.sin(ang), 23, SKIN, k=kp, c=c, texture=0)
        a.rect(462, 1080, 618, 1380, TEAL, r=[46, 46, 0, 0], k=kp, c=c, texture=0)
        hy = 1010 - 10 * np.sin(np.pi * joy)
        a.circ(540, hy, 56, SKIN, k=kp, c=c, texture=0)
        if kp > 0.9:
            fv.fill(fv.half(fv.circle(540, hy, 60), (480, hy - 16), (600, hy - 16)), tone(EMBER, -0.55), texture=0)   # hair
            a.circ(520, hy + 4, 5.5, DARK, texture=0); a.circ(560, hy + 4, 5.5, DARK, texture=0)
            fv.fill(fv.half(fv.circle(540, hy + 18, 17), (557, hy + 20), (523, hy + 20)), DARK, texture=0)            # smile
    # the door swings open on its left hinge
    xr = lerp(686, 448, o); sk = 34 * o
    fv.fill(fv.poly([(394, 812), (xr, 812 + sk), (xr, 1380), (394, 1380)], round=[8, 6, 0, 0]), mix(EMBER, tone(EMBER, -0.3), o), lift=6 if o > 0.05 else 0)
    f = (xr - 394) / 292
    for y0, y1 in ((862, 1060), (1100, 1330)):
        fv.fill(fv.poly([(394 + 36 * f, y0 + sk * .15), (394 + 256 * f, y0 + sk * .8), (394 + 256 * f, y1), (394 + 36 * f, y1)], round=5 * f), tone(EMBER, -0.16 - 0.2 * o), texture=0)
    a.circ(394 + 262 * f, 1090, 11, SAND, texture=0)
    # bell
    for i in range(3):
        rp = seg(u, 1.45 + i * 0.16, 2.05 + i * 0.16)
        if 0 < rp < 1: a.ring(748, 1060, 14 + 60 * rp, 6, CREAM, alpha=(1 - rp) * 0.9)
    a.circ(748, 1060, 12, SAND, lift=3)
    # the parcel slides up to the step, then is lifted with joy
    bx = lerp(-260, 540, eo(seg(u, 0.25, 1.25))); hop = 26 * abs(np.sin(np.pi * seg(u, 0.25, 1.25) * 2)) * (1 - seg(u, 0.25, 1.25))
    up = 54 * eo(seg(u, 3.0, 3.45)) + 8 * np.sin(u * 8) * seg(u, 3.4, 3.6)
    box(a, bx, 1380 - hop - up, 250, 170)
    for i, (x, y, s) in enumerate(((330, 760, 60), (760, 720, 76), (540, 640, 54), (236, 640, 40), (850, 600, 44))):
        kk = pop(u, 3.2 + i * 0.1, 0.4)
        if i % 2 == 0: a.heart(x, y - 30 * seg(u, 3.2, 4.8), s, k=kk)
        elif kk > 0.05: fv.sparkle(x, y, s * 0.4 * kk * (0.8 + 0.2 * np.sin(u * 8 + i)), colour=SAND)


def s5_static(a):
    fv = a.fv; fv.backdrop(NIGHT)
    fv.stars(110, box=(0, 0, W, H), size=(1.0, 2.8))

def s5(a, u):
    fv = a.fv
    kg = eo(seg(u, 0.3, 1.2)); r = (430 + 22 * np.sin(u * 2.2)) * kg
    if kg > 0.05: fv.glow(540, 800, r, r, LIGHT, rings=5, alpha=0.085)
    k = pop(u, 0.15, 0.6); on = eo(seg(u, 0.6, 1.1))
    laptop(a, 540, 1040, 1.02, k, screen=mix(mix(NIGHT, PLUM, 0.22), mix(SAND, CREAM, 0.45), on))
    a.heart(540, 800, 150, k=pop(u, 0.9, 0.45) * (1 + 0.05 * np.sin(u * 6)))
    for i, (x, y, rr) in enumerate(((150, 520, 22), (930, 470, 26), (110, 900, 16), (975, 880, 18), (250, 330, 16), (800, 290, 20), (540, 380, 14))):
        kk = pop(u, 1.0 + i * 0.09, 0.4) * (0.75 + 0.25 * np.sin(u * 5 + i * 1.7))
        if kk > 0.05: fv.sparkle(x, y, rr * kk, colour=SAND if i % 2 else CREAM)
    for i, ch in enumerate('POSH'):                                                    # the name lands letter by letter
        kk = pop(u, 1.3 + i * 0.12, 0.45)
        a.text(ch, 540 + (i - 1.5) * 168, 1300 + (1 - kk) * 60, 200, 800, alpha=cl(kk * 1.5))
    lw = 150 * eo(seg(u, 1.9, 2.5))
    if lw > 2: a.cap((540 - lw, 1436), (540 + lw, 1436), 4, EMBER, texture=0)


SCENES = [
    dict(dur=4.7, st=s1_static, dy=s1, step='STEP 1', title='You choose\nyour laptop', cap='Order online in a few clicks.'),
    dict(dur=4.9, st=s2_static, dy=s2, step='STEP 2', title='We check\nand pack it', cap='Inspected, wrapped and boxed with care.'),
    dict(dur=4.7, st=s3_static, dy=s3, step='STEP 3', title='We send it\non its way', cap='Tracked from our door to yours.'),
    dict(dur=5.0, st=s4_static, dy=s4, step='STEP 4', title='It arrives\nat your door', cap='Sealed, safe and ready to open.'),
    dict(dur=4.7, st=s5_static, dy=s5, step=None, title=None, cap='From our hands to your door.'),
]
STARTS = np.cumsum([0] + [s['dur'] for s in SCENES]); DUR = float(STARTS[-1]); TR = 0.55

_fv = None; _bg = {}; _fonts = {}
def font(size, weight):
    key = (int(size), weight)
    if key not in _fonts:
        f = ImageFont.truetype(FONT, int(size))
        try: f.set_variation_by_axes([min(32, max(14, size)), weight])
        except Exception: pass
        _fonts[key] = f
    return _fonts[key]

def draw_texts(img, items):
    if not items: return img
    ov = Image.new('RGBA', img.size, (0, 0, 0, 0)); d = ImageDraw.Draw(ov)
    for s, x, y, size, weight, col, alpha, anchor, spacing in items:
        if s == '__pill__':
            x0, y0, x1, y1 = x; d.rounded_rectangle((x0, y0, x1, y1), radius=(y1 - y0) / 2, fill=(*col, int(255 * alpha)))
        elif s == '__dot__':
            d.ellipse((x - size, y - size, x + size, y + size), fill=(*col, int(255 * alpha)))
        else:
            d.multiline_text((x, y), s, font=font(size, weight), fill=(*col, int(255 * alpha)), anchor=anchor, align='center', spacing=spacing)
    return Image.alpha_composite(img.convert('RGBA'), ov).convert('RGB')

def chrome(i, u):
    """step pill, title, caption, progress dots - all slide up and fade in"""
    sc = SCENES[i]; items = []; cream = (246, 239, 228); ember = (236, 122, 60)
    def ent(t0): k = eo(seg(u, t0, t0 + 0.5)); return k, (1 - k) * 34
    if sc['step']:
        k, dy = ent(0.1); pw = max(110, 13 * len(sc['step']) + 34); items.append(('__pill__', (540 - pw, 236 + dy, 540 + pw, 296 + dy), 0, 0, 0, ember, k, '', 0))
        items.append((sc['step'], 540, 266 + dy, 30, 800, (27, 31, 77), k, 'mm', 0))
        k, dy = ent(0.22); items.append((sc['title'], 540, 432 + dy, 92, 800, cream, k, 'mm', 6))
    k, dy = ent(0.5 if sc['step'] else 2.1)
    items.append((sc['cap'], 540, 1690 + dy, 44, 500, cream, k * 0.92, 'mm', 0))
    for j in range(len(SCENES)):
        items.append(('__dot__', 540 + (j - (len(SCENES) - 1) / 2) * 44, 1796, 9 if j == i else 6, 0, ember if j == i else cream, 1.0 if j == i else 0.35, '', 0))
    return items

def scene_frame(i, u):
    global _fv
    if _fv is None: _fv = FlatVector(W, H, seed=11)
    fv = _fv; a = Art(fv)
    if i not in _bg:
        fv.rng = np.random.default_rng(100 + i); fv.img[:] = 0; SCENES[i]['st'](a); _bg[i] = fv.img.copy()
    fv.img[:] = _bg[i]; fv.rng = np.random.default_rng(7); a.texts = []
    SCENES[i]['dy'](a, u)
    return draw_texts(fv.render(), a.texts + chrome(i, u))

def frame(t):
    i = min(len(SCENES) - 1, int(np.searchsorted(STARTS, t, side='right') - 1)); u = t - STARTS[i]
    img = scene_frame(i, u)
    if i > 0 and u < TR:                                                                # the next scene pushes the last one out
        prev = scene_frame(i - 1, SCENES[i - 1]['dur'] + u); k = eio(u / TR); off = int(round(W * k))
        out = Image.new('RGB', (W, H)); out.paste(prev, (-off, 0)); out.paste(img, (W - off, 0)); img = out
    if t < 0.4: img = Image.blend(Image.new('RGB', (W, H), NIGHT), img, t / 0.4)
    return img

def work(n):
    frame(n / FPS).save(os.path.join(HERE, 'frames', f'{n:05d}.jpg'), quality=95, subsampling=0); return n

if __name__ == '__main__':
    mode, out = sys.argv[1], sys.argv[2]
    if mode == 'still':
        frame(float(sys.argv[3])).save(out, quality=92, subsampling=0); sys.exit()
    from multiprocessing import Pool
    os.makedirs(os.path.join(HERE, 'frames'), exist_ok=True)
    N = int(round(DUR * FPS))
    with Pool(4) as p:
        for k, n in enumerate(p.imap_unordered(work, range(N), chunksize=6)):
            if k % 80 == 0: print('frame', k, '/', N, flush=True)
    subprocess.run(['ffmpeg', '-y', '-loglevel', 'error', '-r', str(FPS), '-i', os.path.join(HERE, 'frames', '%05d.jpg'), '-c:v', 'libx264',
                    '-pix_fmt', 'yuv420p', '-crf', '18', '-movflags', '+faststart', out], check=True)
    print('done', out, DUR)
