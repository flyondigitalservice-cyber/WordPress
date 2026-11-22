"""Funky showreel (fx/): bespoke beat-synced composition at 105 BPM over _shared/music/funk_bed.wav."""
import json, os, shutil, html, math
ROOT = os.path.dirname(os.path.abspath(__file__)) + "/.."
B = 60 / 105.0                        # one beat
def bt(n): return round(n * B, 3)     # beat index -> seconds
D = f"{ROOT}/fx"
esc = lambda s: html.escape(s, quote=False)
YEL = "#FFC72C"                        # the brand's own taxi yellow (from the logo / website)

COLLAGE = ["sp-gracie-bcaa", "pk-kinray", "bt-jovae-1oz-2", "hl-cctv", "fd-zenobar", "sp-beast", "pk-janbex-2", "bt-lashes",
           "fd-cherry", "sp-optimum17", "hl-coffee-grinder", "pk-nystia", "sn-power-stacks", "bt-amazon-complex", "fd-kipnuggets", "pk-conazol"]
SPOT = [("sp-optimum17-row", "Optimum 17"), ("sp-dragon", "Dragon's Breath"), ("sp-alpha-male", "Alpha Male"), ("sp-maxitto-d3", "Maxitto"),
        ("sp-shampoo-8oz", "Jovaé"), ("bt-lashes-2", "Scandinavian Lashes"), ("bt-botanicals", "Botanicals"), ("sp-biogen-collagen", "Biogen"),
        ("fd-matcha-pure", "Pure Matcha"), ("fd-tea-boxes", "Tea blends"), ("sp-clever-set", "Clever Goodness"), ("pk-whey", "Whey"),
        ("pk-led-string", "LED string lights"), ("hl-czm", "CZM 5000"), ("hl-paipur-notebooks", "Paipur"), ("hl-prix", "Prix")]
CATS = ["SUPPLEMENTS", "BEAUTY", "FOOD", "HOME & TECH"]
SLIDE_BG = ["#245BFF", "#FFFFFF", "#111111", YEL]
WORDS = ["BOXES.", "LABELS.", "BOTTLES.", "POUCHES.", "TUBS.", "TINS.", "BRANDS.", "WOW."]
WORD_BG = ["#245BFF", "#111111", YEL, "#FFFFFF", "#245BFF", "#111111", YEL, "#245BFF"]
WORD_FG = ["#FFFFFF", "#FFFFFF", "#111111", "#111111", "#FFFFFF", "#FFFFFF", "#111111", "#FFFFFF"]
ROWS = [["pk-baby-swaddle", "sp-rainier", "hl-magneto", "fd-matcha-daily", "sp-ultravita-men", "pk-bella-seafood", "sp-jovae-immune", "hl-shago"],
        ["sp-moringa", "pk-food-steamer", "bt-amazon-complex", "sn-citrulline-2", "pk-door-closer", "sp-niasun", "bt-bunny-tube", "hl-moth-trap"],
        ["pk-comfort-system", "sp-stemstim", "fd-nystia-2", "hl-vida-car", "sp-tough-tummies", "pk-athletic-tape", "sp-collagen", "hl-jcrobot"]]
ORBIT = [("shakti-card", "Cards"), ("shakti-pouch", "Pouches"), ("shakti-car", "Vehicle"), ("shakti-kmb", "Bottles"), ("shakti-gallon", "Gallon"), ("shakti-stationery", "Stationery")]

H, J, imgs = [], [], set()
def img(f): imgs.add(f + ".jpg"); return f"assets/work/{f}.jpg"
def sec(id_, s, e, bg, inner, z=1, extra=""):
    H.append(f'  <section id="{id_}" class="scene clip" data-start="{s}" data-duration="{round(e - s, 3)}" data-track-index="{z}" style="background:{bg}" data-layout-allow-overlap data-layout-allow-overflow {extra}>\n{inner}\n  </section>')

STAR = '<svg viewBox="0 0 100 100"><path d="M50 0 L61 35 L98 35 L68 57 L79 92 L50 70 L21 92 L32 57 L2 35 L39 35 Z" fill="{c}"/></svg>'
SQUIG = '<svg viewBox="0 0 220 60"><path d="M5 30 Q 32 0 60 30 T 115 30 T 170 30 T 215 30" fill="none" stroke="{c}" stroke-width="12" stroke-linecap="round"/></svg>'
CHECK = "".join(f'<i style="left:{i*36}px;top:{(i%2)*36}px"></i>' for i in range(30)) + "".join(f'<i style="left:{i*36}px;top:{((i+1)%2)*36}px;opacity:0"></i>' for i in range(0))

# ---------------- A · hook (bars 0–1) ----------------
A0, A1 = 0.0, bt(8)
sec("a", A0, A1, "#111111", f'''    <div class="hk" id="a-w1" style="top:470px">WE MAKE</div>
    <div class="hk blue" id="a-w2" style="top:640px">BOXES</div>
    <div class="hk" id="a-w3" style="top:810px">DANCE<span style="color:{YEL}">.</span></div>
    <div class="stk" id="a-star" style="left:760px;top:330px;width:170px;height:170px">{STAR.format(c=YEL)}</div>
    <div class="stk" id="a-sq" style="left:120px;top:1010px;width:330px;height:90px">{SQUIG.format(c="#245BFF")}</div>''')
J += [f'tl.fromTo("#a-w1", {{ scaleY: 0.86, scaleX: 1.08, y: 10 }}, {{ scaleY: 1, scaleX: 1, y: 0, duration: 0.5, ease: "elastic.out(1, 0.45)" }}, 0);']
for i, w in enumerate(["#a-w2", "#a-w3"]):
    t = bt(i + 1)
    J += [f'tl.fromTo("{w}", {{ y: -420, scaleY: 1.5, scaleX: 0.7, opacity: 0 }}, {{ y: 0, scaleY: 1, scaleX: 1, opacity: 1, duration: 0.42, ease: "power3.in" }}, {round(t - 0.42, 3)});',
          f'tl.to("{w}", {{ keyframes: [{{ scaleY: 0.72, scaleX: 1.22, duration: 0.07 }}, {{ scaleY: 1.08, scaleX: 0.95, duration: 0.12 }}, {{ scaleY: 1, scaleX: 1, duration: 0.25, ease: "elastic.out(1, 0.4)" }}] }}, {t});']
J += [f'tl.fromTo("#a-star", {{ scale: 0, rotate: -180 }}, {{ scale: 1, rotate: 0, duration: 0.6, ease: "back.out(2.2)" }}, {bt(3)});',
      f'tl.to("#a-star", {{ rotate: 200, duration: {bt(5)}, ease: "none" }}, {bt(3) + 0.6});',
      f'tl.fromTo("#a-sq", {{ scaleX: 0, transformOrigin: "0 50%" }}, {{ scaleX: 1, duration: 0.5, ease: "expo.out" }}, {bt(4)});']
for k in range(4, 8):
    J.append(f'tl.fromTo(["#a-w1", "#a-w2", "#a-w3"], {{ rotate: {(-1) ** k * 3} }}, {{ rotate: 0, duration: {round(B - 0.02, 3)}, ease: "elastic.out(1, 0.3)", stagger: 0.04 }}, {bt(k)});')
J.append(f'tl.to(["#a-w1", "#a-w2", "#a-w3", "#a-star", "#a-sq"], {{ y: -900, rotate: 12, duration: 0.45, ease: "back.in(1.6)", stagger: 0.04 }}, {round(A1 - 0.55, 3)});')

# ---------------- B · collage (bars 2–5) ----------------
B0, B1 = A1, bt(24)
pos = []
for i in range(16):
    c, r = i % 4, i // 4
    x = [210, 440, 670, 880][c] + (37 * math.sin(i * 2.3)); y = [520, 830, 1140, 1450][r] + 45 * math.cos(i * 1.7)
    pos.append((round(x), round(y), round(10 * math.sin(i * 3.1), 1), 250 + int(40 * (0.5 + 0.5 * math.sin(i * 5.3)))))
cards = "".join(f'<div class="cc" id="b-c{i}" style="left:{x - s//2}px;top:{y - s//2}px;width:{s}px;height:{s}px"><img src="{img(f)}" alt="" /></div>' for i, ((x, y, r, s), f) in enumerate(zip(pos, COLLAGE)))
sec("b", B0, B1, "#F3F4F6", f'''    <div class="cam" id="b-cam">
      <div class="outline" id="b-bgword" data-layout-ignore>OUR<br/>WORK</div>
      {cards}
      <div class="stk" id="b-s1" style="left:820px;top:300px;width:140px;height:140px">{STAR.format(c="#245BFF")}</div>
      <div class="dot" id="b-s2" style="left:90px;top:1560px;width:120px;height:120px;background:{YEL}"></div>
      <div class="stk" id="b-s3" style="left:600px;top:1640px;width:300px;height:80px">{SQUIG.format(c="#111")}</div>
    </div>''', z=2)
J += [f'tl.fromTo("#b-bgword", {{ scale: 0.6, opacity: 0 }}, {{ scale: 1, opacity: 1, duration: 0.7, ease: "elastic.out(1, 0.5)" }}, {B0});']
for i, (x, y, r, s) in enumerate(pos):
    t = round(B0 + i * B, 3)
    J += [f'tl.fromTo("#b-c{i}", {{ scale: 0, rotate: {r - 40}, y: 60 }}, {{ scale: 1, rotate: {r}, y: 0, duration: 0.5, ease: "back.out(2.6)" }}, {t});',
          f'tl.to("#b-c{i}", {{ keyframes: [{{ scaleY: 0.86, scaleX: 1.1, duration: 0.08 }}, {{ scaleY: 1, scaleX: 1, duration: 0.3, ease: "elastic.out(1, 0.35)" }}] }}, {round(t + 0.22, 3)});']
for k in range(4):
    J.append(f'tl.fromTo("#b-cam", {{ scale: 1.035 }}, {{ scale: 1, duration: {round(B * 1.5, 3)}, ease: "elastic.out(1, 0.4)" }}, {round(B0 + k * 4 * B, 3)});')
J += [f'tl.fromTo("#b-s1", {{ scale: 0 }}, {{ scale: 1, rotate: 180, duration: 1.2, ease: "back.out(2)" }}, {round(B0 + 2 * B, 3)});',
      f'tl.fromTo("#b-s2", {{ scale: 0 }}, {{ scale: 1, duration: 0.5, ease: "back.out(3)" }}, {round(B0 + 6 * B, 3)});',
      f'tl.fromTo("#b-s3", {{ scaleX: 0, transformOrigin: "0 50%" }}, {{ scaleX: 1, duration: 0.5, ease: "expo.out" }}, {round(B0 + 10 * B, 3)});']
for i, (x, y, r, s) in enumerate(pos):
    dx, dy = (x - 540) * 1.6, (y - 960) * 1.4
    J.append(f'tl.to("#b-c{i}", {{ x: {round(dx)}, y: {round(dy)}, rotate: {r * 4}, scale: 0.6, opacity: 0, duration: 0.42, ease: "power3.in" }}, {round(B1 - 0.45, 3)});')

# ---------------- C · spotlight (bars 6–13) ----------------
C0, C1 = B1, bt(56)
slides = []
for i, (f, n) in enumerate(SPOT):
    bg = SLIDE_BG[i % 4]; dark = bg in ("#245BFF", "#111111")
    slides.append(f'''      <div class="sl" id="c-s{i}" style="background:{bg}">
        <div class="cat" style="color:{'#fff' if dark else '#111'}">{esc(CATS[i // 4])}</div>
        <div class="pc" id="c-p{i}"><img src="{img(f)}" alt="{esc(n)}" /><span class="chip xchip" style="bottom:24px">{esc(n)}</span></div>
        <div class="stk" id="c-k{i}" style="left:{[790, 110, 800, 130][i % 4]}px;top:{[1330, 1350, 470, 470][i % 4]}px;width:150px;height:150px">{STAR.format(c=YEL if bg != YEL else "#111") if i % 2 == 0 else SQUIG.format(c="#fff" if dark else "#245BFF")}</div>
      </div>''')
sec("c", C0, C1, "#111", "\n".join(slides), z=1)
for i in range(16):
    t = round(C0 + i * 2 * B, 3)
    J += [f'tl.fromTo("#c-s{i}", {{ clipPath: "circle(0% at {[50, 20, 80, 50][i % 4]}% {[50, 30, 70, 80][i % 4]}%)" }}, {{ clipPath: "circle(150% at {[50, 20, 80, 50][i % 4]}% {[50, 30, 70, 80][i % 4]}%)", duration: 0.5, ease: "expo.inOut" }}, {round(t - 0.12, 3)});',
          f'tl.fromTo("#c-p{i}", {{ y: -700, scaleY: 1.35, scaleX: 0.8, rotate: {(-1) ** i * 8} }}, {{ y: 0, scaleY: 1, scaleX: 1, rotate: 0, duration: 0.36, ease: "power3.in" }}, {round(t - 0.05, 3)});',
          f'tl.to("#c-p{i}", {{ keyframes: [{{ scaleY: 0.84, scaleX: 1.14, duration: 0.07 }}, {{ scaleY: 1.04, scaleX: 0.97, duration: 0.12 }}, {{ scaleY: 1, scaleX: 1, duration: 0.3, ease: "elastic.out(1, 0.4)" }}] }}, {round(t + 0.31, 3)});',
          f'tl.fromTo("#c-p{i}", {{ rotate: 0 }}, {{ keyframes: [{{ rotate: {(-1) ** i * 4}, duration: 0.12 }}, {{ rotate: 0, duration: 0.45, ease: "elastic.out(1, 0.3)" }}] }}, {round(t + B, 3)});',
          f'tl.fromTo("#c-k{i}", {{ scale: 0, rotate: -90 }}, {{ scale: 1, rotate: 30, duration: 0.6, ease: "back.out(2.4)" }}, {round(t + 0.2, 3)});']
    if i % 4 == 0:
        J.append(f'tl.fromTo("#c-s{i} .cat", {{ y: 80, scaleY: 1.5, opacity: 0 }}, {{ y: 0, scaleY: 1, opacity: 1, duration: 0.6, ease: "elastic.out(1, 0.45)" }}, {round(t + 0.1, 3)});')
    else:
        J.append(f'tl.set("#c-s{i} .cat", {{ opacity: 1 }}, {t});')
        J.append(f'tl.fromTo("#c-s{i} .cat", {{ scale: 1.08 }}, {{ scale: 1, duration: 0.4, ease: "elastic.out(1, 0.4)" }}, {t});')

# ---------------- D · word break (bars 14–15) ----------------
D0, D1 = C1, bt(64)
wd = "".join(f'<div class="wb" id="d-b{i}" style="background:{WORD_BG[i]}"><div class="ww" id="d-w{i}" style="color:{WORD_FG[i]};font-size:{min(210, int(760 / (len(w) * 0.62)))}px">{esc(w)}</div></div>' for i, w in enumerate(WORDS))
sec("d", D0, D1, "#111", wd, z=2)
for i in range(8):
    t = round(D0 + i * B, 3)
    J += [f'tl.set("#d-b{i}", {{ opacity: 1 }}, {t});',
          f'tl.fromTo("#d-w{i}", {{ scale: 1.9, rotate: {(-1) ** i * 10} }}, {{ scale: 1, rotate: {(-1) ** i * -4}, duration: 0.38, ease: "elastic.out(1, 0.5)" }}, {t});']

# ---------------- E · conveyor (bars 16–19) ----------------
E0, E1 = D1, bt(80)
rows = ""
for r, row in enumerate(ROWS):
    tiles = "".join(f'<div class="tt"><img src="{img(f)}" alt="" /></div>' for f in row + row)
    rows += f'<div class="row" id="e-r{r}" style="top:{380 + r * 330}px">{tiles}</div>'
sec("e", E0, E1, YEL, f'''    <div class="tilt" id="e-tilt">{rows}</div>
    <div class="band" id="e-band"><div class="bt" id="e-t1">TEN YEARS</div><div class="bt blue" id="e-t2">OF BOXES.</div></div>''', z=1)
span = 8 * 290
for r in range(3):
    a, b2 = (0, -span) if r % 2 == 0 else (-span, 0)
    J.append(f'tl.fromTo("#e-r{r}", {{ x: {a} }}, {{ x: {b2}, duration: {round(E1 - E0, 3)}, ease: "none" }}, {E0});')
J += [f'tl.fromTo("#e-tilt", {{ rotate: -14, scale: 1.15 }}, {{ rotate: -9, scale: 1.15, duration: {round(E1 - E0, 3)}, ease: "sine.inOut" }}, {E0});',
      f'tl.fromTo("#e-band", {{ scaleX: 0 }}, {{ scaleX: 1, duration: 0.5, ease: "expo.out" }}, {round(E0 + 2 * B, 3)});',
      f'tl.fromTo("#e-t1", {{ y: 120, opacity: 0 }}, {{ y: 0, opacity: 1, duration: 0.5, ease: "elastic.out(1, 0.45)" }}, {round(E0 + 3 * B, 3)});',
      f'tl.fromTo("#e-t2", {{ y: 120, opacity: 0 }}, {{ y: 0, opacity: 1, duration: 0.5, ease: "elastic.out(1, 0.45)" }}, {round(E0 + 4 * B, 3)});']
for k in range(5, 16, 2):
    J.append(f'tl.fromTo("#e-band", {{ scaleY: 1.12, scaleX: 0.97 }}, {{ scaleY: 1, scaleX: 1, duration: {round(B * 1.2, 3)}, ease: "elastic.out(1, 0.35)" }}, {round(E0 + k * B, 3)});')
J.append(f'tl.to(["#e-band", "#e-tilt"], {{ scale: 0.2, opacity: 0, rotate: 40, duration: 0.45, ease: "back.in(1.5)" }}, {round(E1 - 0.5, 3)});')

# ---------------- F · Shakti orbit (bars 20–21) ----------------
F0, F1 = E1, bt(88)
orb = "".join(f'<div class="ob" id="f-o{i}" style="transform-origin:0 0"><div class="obi" id="f-i{i}"><img src="{img(f)}" alt="" /><span>{esc(n)}</span></div></div>' for i, (f, n) in enumerate(ORBIT))
sec("f", F0, F1, "#FFFFFF", f'''    <div class="label" id="f-lab" style="position:absolute;left:120px;top:330px;color:#245BFF">Brand identity · Shakti Agrobiotech</div>
    <div class="hk2" id="f-h" style="top:380px">ONE BRAND.<br/><span class="blue">EVERYWHERE.</span></div>
    <div id="f-ring"><div class="ologo" id="f-logo"><img src="{img('shakti-logo')}" alt="Shakti Agrobiotech" /></div>{orb}</div>''', z=2)
J += [f'tl.fromTo("#f-h", {{ y: 80, opacity: 0, scaleY: 1.4 }}, {{ y: 0, opacity: 1, scaleY: 1, duration: 0.6, ease: "elastic.out(1, 0.45)" }}, {F0});',
      f'tl.fromTo("#f-lab", {{ opacity: 0 }}, {{ opacity: 1, duration: 0.4 }}, {F0});',
      f'tl.fromTo("#f-logo", {{ scale: 0, rotate: -30 }}, {{ scale: 1, rotate: 0, duration: 0.7, ease: "elastic.out(1, 0.45)" }}, {round(F0 + B, 3)});']
for i in range(6):
    a0 = i * 60
    J += [f'tl.fromTo("#f-o{i}", {{ rotate: {a0} }}, {{ rotate: {a0 + 100}, duration: {round(F1 - F0, 3)}, ease: "sine.inOut" }}, {F0});',
          f'tl.fromTo("#f-i{i}", {{ rotate: {-a0} }}, {{ rotate: {-a0 - 100}, duration: {round(F1 - F0, 3)}, ease: "sine.inOut" }}, {F0});',
          f'tl.fromTo("#f-i{i}", {{ scale: 0 }}, {{ scale: 1, duration: 0.5, ease: "back.out(2.6)" }}, {round(F0 + (2 + i) * B * 0.8, 3)});']

# ---------------- G · breakdown (bars 22–23) ----------------
G0, G1 = F1, bt(96)
sec("g", G0, G1, "#245BFF", f'''    <div class="blob" id="g-blob"></div>
    <div class="hk3" id="g-t" style="top:640px"><span id="g-1">EVERY</span><br/><span id="g-2">PROJECT,</span><br/><span id="g-3">OUR OWN</span><br/><span id="g-4" style="color:{YEL}">DESIGN.</span></div>''', z=1)
J += [f'tl.fromTo("#g-blob", {{ scale: 0.2, rotate: 0, borderRadius: "42% 58% 63% 37% / 41% 44% 56% 59%" }}, {{ scale: 1.25, rotate: 120, borderRadius: "63% 37% 38% 62% / 55% 61% 39% 45%", duration: {round(G1 - G0, 3)}, ease: "sine.inOut" }}, {G0});']
for i in range(4):
    J.append(f'tl.fromTo("#g-{i + 1}", {{ y: 60, opacity: 0, scaleY: 1.3 }}, {{ y: 0, opacity: 1, scaleY: 1, duration: 0.7, ease: "elastic.out(1, 0.5)" }}, {round(G0 + 0.2 + i * B * 1.6, 3)});')

# ---------------- H · outro (bar 24–end) ----------------
H0, H1 = G1, 60.0
sec("h", H0, H1, "#111111", f'''    <div class="check" id="h-chk">{''.join(f'<i style="left:{i*40}px;top:{(i%2)*40}px"></i>' for i in range(28))}</div>
    <div class="hk2 white" id="h-t" style="top:400px"><span id="h-1">LET'S MAKE</span><br/><span id="h-2">SOMETHING</span><br/><span id="h-3" class="blue">FUNKY.</span></div>
    <img id="h-logo" src="assets/brand/logo-white.png" alt="Bombay Marketing" style="position:absolute;left:150px;top:840px;width:780px" />
    <div class="end-url" id="h-url" style="position:absolute;left:150px;top:1040px;font-family:var(--display);font-weight:500;font-size:42px;color:#fff">bombaymarketing.in</div>
    <div class="stk" id="h-star" style="left:810px;top:300px;width:150px;height:150px">{STAR.format(c=YEL)}</div>''', z=2)
J += [f'tl.fromTo("#h-logo", {{ scale: 0, rotate: -12, y: 80 }}, {{ scale: 1, rotate: 0, y: 0, duration: 1.0, ease: "elastic.out(1, 0.42)" }}, {H0});',
      f'tl.fromTo("#h-chk", {{ x: -1200 }}, {{ x: 0, duration: 0.6, ease: "expo.out" }}, {H0});']
for i in range(3):
    J.append(f'tl.fromTo("#h-{i + 1}", {{ y: -300, scaleY: 1.5, scaleX: 0.75, opacity: 0 }}, {{ y: 0, scaleY: 1, scaleX: 1, opacity: 1, duration: 0.5, ease: "elastic.out(1, 0.45)" }}, {round(H0 + (i + 1) * B, 3)});')
J += [f'tl.fromTo("#h-url", {{ opacity: 0, y: 20 }}, {{ opacity: 1, y: 0, duration: 0.5, ease: "expo.out" }}, {round(H0 + 4 * B, 3)});',
      f'tl.fromTo("#h-star", {{ scale: 0, rotate: -90 }}, {{ scale: 1, rotate: 90, duration: 0.8, ease: "back.out(2.4)" }}, {round(H0 + 2 * B, 3)});',
      f'tl.to("#h-star", {{ rotate: 360, duration: 3, ease: "none" }}, {round(H0 + 2 * B + 0.8, 3)});',
      f'tl.fromTo(["#h-t", "#h-logo", "#h-url"], {{ scale: 1.06 }}, {{ scale: 1, duration: 0.5, ease: "elastic.out(1, 0.35)", stagger: 0.03 }}, {bt(104)});']

# ---------------- global badge (rotating circular text) ----------------
badge = '''  <div id="badge" class="clip" data-layout-ignore data-start="4.0" data-duration="50.29" data-track-index="5" style="position:absolute;left:820px;top:1560px;width:200px;height:200px;z-index:50">
    <svg id="badge-svg" viewBox="0 0 200 200" width="200" height="200"><defs><path id="bp" d="M100 100 m -78 0 a 78 78 0 1 1 156 0 a 78 78 0 1 1 -156 0"/></defs>
      <circle cx="100" cy="100" r="96" fill="#111"/><text font-family="BM Text, Inter" font-weight="600" font-size="19" letter-spacing="4" fill="#fff"><textPath href="#bp">BOMBAY MARKETING · SHOWREEL · TEN YEARS · </textPath></text>
      <circle cx="100" cy="100" r="34" fill="#FFC72C"/></svg></div>'''
J += ['tl.fromTo("#badge-svg", { rotate: 0, transformOrigin: "50% 50%" }, { rotate: 540, duration: 50.29, ease: "none" }, 4.0);']

CSS = f'''
  #bg {{ position: absolute; inset: 0; background: #111; }}
  .hk {{ position: absolute; left: 120px; font-family: var(--display); font-weight: 900; font-size: 150px; letter-spacing: -0.05em; line-height: 1; color: #fff; text-transform: uppercase; transform-origin: 50% 100%; white-space: nowrap; }}
  .hk.blue, .blue {{ color: var(--blue); }}
  .hk2 {{ position: absolute; left: 120px; right: 120px; font-family: var(--display); font-weight: 900; font-size: 104px; letter-spacing: -0.045em; line-height: 0.95; color: #111; text-transform: uppercase; }}
  .hk2.white {{ color: #fff; }}
  .hk2 span, .hk3 span {{ display: inline-block; transform-origin: 50% 100%; }}
  .hk3 {{ position: absolute; left: 120px; right: 120px; font-family: var(--display); font-weight: 900; font-size: 118px; letter-spacing: -0.045em; line-height: 0.95; color: #fff; z-index: 2; }}
  .stk {{ position: absolute; }} .stk svg {{ width: 100%; height: 100%; display: block; }}
  .dot {{ position: absolute; border-radius: 50%; }}
  .cc {{ position: absolute; border-radius: 34px; overflow: hidden; background: #fff; box-shadow: 0 30px 60px rgba(17,17,17,.18); border: 6px solid #fff; }}
  .cc img {{ width: 100%; height: 100%; object-fit: cover; display: block; }}
  .outline {{ position: absolute; left: 0; right: 0; top: 620px; text-align: center; font-family: var(--display); font-weight: 900; font-size: 300px; line-height: 0.85; letter-spacing: -0.05em; color: transparent; -webkit-text-stroke: 4px rgba(36,91,255,.35); }}
  .sl {{ position: absolute; inset: 0; }}
  .cat {{ position: absolute; left: 120px; right: 120px; top: 330px; font-family: var(--display); font-weight: 900; font-size: 112px; letter-spacing: -0.05em; line-height: 1; opacity: 0; transform-origin: 0 100%; }}
  .pc {{ position: absolute; left: 160px; top: 560px; width: 760px; height: 700px; border-radius: 44px; overflow: hidden; background: #fff; box-shadow: 0 40px 90px rgba(0,0,0,.35); transform-origin: 50% 100%; }}
  .pc img {{ width: 100%; height: 100%; object-fit: contain; display: block; }}
  .wb {{ position: absolute; inset: 0; opacity: 0; display: flex; align-items: center; justify-content: center; }}
  .ww {{ font-family: var(--display); font-weight: 900; letter-spacing: -0.05em; line-height: 1; }}
  .tilt {{ position: absolute; left: -300px; top: 0; width: 1680px; height: 1920px; }}
  .row {{ position: absolute; left: 0; display: flex; gap: 30px; }}
  .tt {{ width: 260px; height: 260px; border-radius: 30px; overflow: hidden; background: #fff; flex: none; box-shadow: 0 20px 40px rgba(17,17,17,.2); }}
  .tt img {{ width: 100%; height: 100%; object-fit: cover; display: block; }}
  .band {{ position: absolute; left: 90px; right: 90px; top: 760px; padding: 40px 40px 46px; background: #111; border-radius: 40px; z-index: 3; }}
  .bt {{ font-family: var(--display); font-weight: 900; font-size: 120px; letter-spacing: -0.05em; line-height: 0.95; color: #fff; }}
  #f-ring {{ position: absolute; left: 540px; top: 1040px; width: 0; height: 0; }}
  .ologo {{ position: absolute; left: -200px; top: -125px; width: 400px; height: 250px; border-radius: 34px; overflow: hidden; background: #fff; box-shadow: 0 20px 50px rgba(17,17,17,.15); }}
  .ologo img {{ width: 100%; height: 100%; object-fit: contain; }}
  .ob {{ position: absolute; left: 0; top: 0; width: 0; height: 0; }}
  .obi {{ position: absolute; left: 280px; top: -95px; width: 190px; height: 190px; border-radius: 50%; overflow: hidden; background: #fff; box-shadow: 0 16px 40px rgba(17,17,17,.2); border: 6px solid #245BFF; }}
  .obi img {{ width: 100%; height: 100%; object-fit: cover; }}
  .obi span {{ position: absolute; left: 0; right: 0; bottom: 18px; text-align: center; font-family: var(--text); font-weight: 600; font-size: 28px; color: #fff; text-shadow: 0 2px 8px rgba(0,0,0,.6); }}
  .blob {{ position: absolute; left: 140px; top: 560px; width: 800px; height: 800px; background: #111; }}
  .check {{ position: absolute; left: 0; top: 1300px; width: 1120px; height: 80px; }}
  .check i {{ position: absolute; width: 40px; height: 40px; background: #FFC72C; }}
'''
os.makedirs(f"{D}/assets/work", exist_ok=True); os.makedirs(f"{D}/assets/music", exist_ok=True); os.makedirs(f"{D}/renders", exist_ok=True)
for f in ("hyperframes.json", "package.json"): shutil.copy(f"{ROOT}/_shared/template/{f}", D)
json.dump({"id": "bombay-fx", "name": "Bombay Marketing — Funky Showreel"}, open(f"{D}/meta.json", "w"))
for sub in ("brand", "fonts"): shutil.copytree(f"{ROOT}/_shared/kit/{sub}", f"{D}/assets/{sub}", dirs_exist_ok=True)
for f in ("brand.css", "kit.js"): shutil.copy(f"{ROOT}/_shared/kit/{f}", f"{D}/assets/")
for f in imgs: shutil.copy(f"{ROOT}/_brand/drive/{f}", f"{D}/assets/work/")
shutil.copy(f"{ROOT}/_shared/music/funk_bed.wav", f"{D}/assets/music/funk_bed.wav")
page = f'''<!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=1080, height=1920" />
<title>Bombay Marketing — Funky Showreel</title>
<script src="https://cdn.jsdelivr.net/npm/gsap@3.15.0/dist/gsap.min.js"></script>
<link rel="stylesheet" href="assets/brand.css" />
<style>{CSS}</style>
</head>
<body>
<div id="root" data-composition-id="main" data-start="0" data-duration="60" data-width="1080" data-height="1920">
  <div id="bg" class="clip" data-start="0" data-duration="60" data-track-index="0"></div>
{chr(10).join(H)}
{badge}
  <audio id="music" class="clip" src="assets/music/funk_bed.wav" data-start="0" data-duration="60" data-track-index="6" data-volume="1"></audio>
</div>
<script>
  window.__timelines = window.__timelines || {{}};
  const tl = gsap.timeline({{ paused: true }});
  {chr(10).join("  " + j for j in J).lstrip()}
  window.__timelines["main"] = tl;
  tl.seek(0);
</script>
</body>
</html>
'''
open(f"{D}/index.html", "w").write(page)
print("fx built:", len(imgs), "images,", len(J), "tweens")
