import json, re, sys, html, os, shutil
ROOT = os.path.dirname(os.path.abspath(__file__)) + "/.."
S = json.load(open(ROOT + "/_shared/ext_spec.json"))
VX = json.load(open(ROOT + "/_shared/vo_ext.json"))
CHECK = '<svg viewBox="0 0 40 40"><path d="M8 21l8 8 16-17" fill="none" stroke="#fff" stroke-width="6" stroke-linecap="round" stroke-linejoin="round"/></svg>'
PROC = [("Discover", "Understand your business &amp; goals"), ("Plan", "Strategy, structure &amp; timeline"), ("Build", "Design, develop &amp; launch"), ("Grow", "Measure, improve &amp; scale")]
SFX = {"whoosh.mp3": 0.16, "whoosh-short.mp3": 0.16, "pop.mp3": 0.12, "click-soft.mp3": 0.07}

def esc(s): return html.escape(s, quote=False)
def head_html(lines, color_last=True):
    out = []
    for i, l in enumerate(lines):
        cls = "w blue" if (color_last and i == len(lines) - 1 and len(lines) > 1) else "w"
        out.append(f'<span class="line"><span class="wm"><span class="{cls}">{esc(l)}</span></span></span>')
    return "".join(out)

def build(v):
    spec = S[v]; vx = VX[v]; d = f"{ROOT}/{v}"; p = f"{d}/index.html"
    src = open(p).read()
    if "/* EXTENDED */" in src: print(v, "already extended"); return
    Ssplit = spec["split"]
    # ---- timing
    t = Ssplit; sc_t = []
    for i, sc in enumerate(spec["scenes"]):
        vd = vx[i]["dur"]; delay = sc.get("vo_delay", 0)
        tail = {"problem": 1.6, "steps": 1.3, "showcase": 1.5, "statement": 1.7, "process": 1.2}[sc["type"]]
        dur = round(max(4.2, 0.4 + delay + vd + tail), 2)
        sc_t.append((t, dur, t + 0.4 + delay)); t = round(t + dur, 2)
    D = round(t - Ssplit, 2)
    # ---- shift HTML clip times
    def shift(m):
        tag = m.group(0)
        idm = re.search(r'\sid="([^"]+)"', tag); i_ = idm.group(1) if idm else ""
        st = float(re.search(r'data-start="([\d.]+)"', tag).group(1))
        dm = re.search(r'data-duration="([\d.]+)"', tag); du = float(dm.group(1)) if dm else None
        if i_ == "root" or i_ == "bg" or (i_ == "tag" and du is not None and st + du >= Ssplit - 0.05):
            return tag.replace(f'data-duration="{dm.group(1)}"', f'data-duration="{round(du + D, 2)}"')
        if st >= Ssplit - 1e-6:
            return re.sub(r'data-start="[\d.]+"', f'data-start="{round(st + D, 2)}"', tag, count=1)
        return tag
    src = re.sub(r'<(?:section|div|audio)\b[^>]*\sdata-start="[\d.]+"[^>]*>', shift, src)
    # ---- new scene HTML + JS + audio
    H, J, A = [], [], []
    sfx_rot = ["whoosh-short.mp3", "whoosh.mp3"]; sfx_n = 0
    def sfx(name, at, vol, track):
        nonlocal sfx_n; sfx_n += 1
        A.append(f'  <audio id="xs{sfx_n}" class="clip" src="assets/sfx/{name}" data-start="{round(at - SFX.get(name, 0.1), 2)}" data-track-index="{track}" data-volume="{vol}"></audio>')
    for i, sc in enumerate(spec["scenes"]):
        st, du, vo_at = sc_t[i]; k = f"x{i+1}"; ty = sc["type"]; end = round(st + du, 2)
        A.append(f'  <audio id="vx{i+1}" class="clip" src="assets/vo/{v}_x{i+1}.wav" data-start="{round(vo_at, 2)}" data-track-index="5" data-volume="1"></audio>')
        vd = vx[i]["dur"]
        if i > 0 and ty != "statement": sfx(sfx_rot[i % 2], st, 0.4, 6)
        nlines = len(sc.get("head", []))
        items_top = 440 + nlines * 68 + 56
        last = (i == len(spec["scenes"]) - 1)
        if ty == "problem":
            items = "".join(f'<div class="pain" id="{k}-p{j+1}" style="top:{items_top + j*140}px"><span class="x"></span><span class="pt">{esc(it)}</span><i class="strike" data-layout-allow-occlusion id="{k}-s{j+1}" style="width:{min(660, 20*len(it))}px"></i></div>' for j, it in enumerate(sc["items"]))
            H.append(f'''  <section id="{k}" class="scene soft clip" data-start="{st}" data-duration="{du}" data-track-index="2" data-layout-allow-overlap>
    <div class="cam" id="{k}-cam">
      <div class="label mute xlab" id="{k}-lab">{esc(sc["label"])}</div>
      <div class="display xhead" id="{k}-head">{head_html(sc["head"])}</div>
      {items}
    </div>
  </section>''')
            J.append(f'K.push(tl, "#{k}-cam", {st}, {end}, 1.0, 1.04);')
            J.append(f'tl.fromTo("#{k}-lab", {{ opacity: 0, y: 20 }}, {{ opacity: 1, y: 0, duration: 0.5, ease: "expo.out" }}, {st + 0.05});')
            J.append(f'K.words(tl, "#{k}-head", {st + 0.12}, {{ stagger: 0.1 }});')
            n = len(sc["items"])
            for j in range(n):
                at = round(vo_at + vd * (j + 0.4) / n - 0.3, 2)
                J.append(f'tl.fromTo("#{k}-p{j+1}", {{ opacity: 0, x: 120 }}, {{ opacity: 1, x: 0, duration: 0.6, ease: "expo.out" }}, {at});')
            sk = round(end - 1.15, 2)
            J.append(f'tl.fromTo("#{k} .strike", {{ scaleX: 0, transformOrigin: "0 50%" }}, {{ scaleX: 1, duration: 0.45, ease: "expo.inOut", stagger: 0.12 }}, {sk});')
            J.append(f'tl.to("#{k} .pain", {{ opacity: 0.55, duration: 0.4, stagger: 0.12 }}, {sk + 0.2});')
            sfx("whoosh-short.mp3", sk + 0.2, 0.25, 7)
        elif ty == "steps":
            items = "".join(f'<div class="step" id="{k}-p{j+1}" style="top:{items_top + j*132}px"><span class="n">{j+1:02d}</span><span class="pt">{esc(it)}</span><span class="xck"><span class="f"></span>{CHECK}</span></div>' for j, it in enumerate(sc["items"]))
            H.append(f'''  <section id="{k}" class="scene light clip" data-start="{st}" data-duration="{du}" data-track-index="2" data-layout-allow-overlap>
    <div class="cam" id="{k}-cam">
      <div class="label mute xlab" id="{k}-lab">{esc(sc["label"])}</div>
      <div class="display xhead" id="{k}-head">{head_html(sc["head"])}</div>
      {items}
    </div>
  </section>''')
            J.append(f'K.push(tl, "#{k}-cam", {st}, {end}, 1.0, 1.04);')
            J.append(f'tl.fromTo("#{k}-lab", {{ opacity: 0, y: 20 }}, {{ opacity: 1, y: 0, duration: 0.5, ease: "expo.out" }}, {st + 0.05});')
            J.append(f'K.words(tl, "#{k}-head", {st + 0.12}, {{ stagger: 0.1 }});')
            n = len(sc["items"])
            for j in range(n):
                at = round(vo_at + vd * j / n, 2)
                J.append(f'tl.fromTo("#{k}-p{j+1}", {{ opacity: 0, y: 50 }}, {{ opacity: 1, y: 0, duration: 0.6, ease: "expo.out" }}, {at});')
                J.append(f'tl.fromTo("#{k}-p{j+1} .f", {{ scale: 0 }}, {{ scale: 1, duration: 0.35, ease: "back.out(1.8)" }}, {round(at + 0.45, 2)});')
                J.append(f'tl.fromTo("#{k}-p{j+1} svg", {{ opacity: 0 }}, {{ opacity: 1, duration: 0.15 }}, {round(at + 0.55, 2)});')
                sfx("click-soft.mp3" if j % 2 == 0 else "pop.mp3", at + 0.45, 0.7 if j % 2 == 0 else 0.3, 7 + (j % 2))
        elif ty == "showcase":
            (f1, n1), (f2, n2) = sc["cards"]
            H.append(f'''  <section id="{k}" class="scene dark clip" data-start="{st}" data-duration="{du}" data-track-index="2" data-layout-allow-overlap>
    <div class="cam" id="{k}-cam">
      <div class="label xlab" id="{k}-lab" style="color:#9aa0ab">{esc(sc["label"])}</div>
      <div class="display xhead" id="{k}-head" style="color:#fff">{head_html(sc["head"])}</div>
      <div class="xcard" id="{k}-c1" style="left:120px;top:{items_top}px"><img src="assets/work/{f1}" alt="{esc(n1)}" /><span class="chip xchip" style="top:20px">{esc(n1)}</span></div>
      <div class="xcard" id="{k}-c2" style="left:340px;top:{items_top + 212}px"><img src="assets/work/{f2}" alt="{esc(n2)}" /><span class="chip xchip" style="bottom:20px">{esc(n2)}</span></div>
    </div>
  </section>''')
            J.append(f'K.push(tl, "#{k}-cam", {st}, {end}, 1.0, 1.05);')
            J.append(f'tl.fromTo("#{k}-lab", {{ opacity: 0, y: 20 }}, {{ opacity: 1, y: 0, duration: 0.5, ease: "expo.out" }}, {st + 0.05});')
            J.append(f'K.words(tl, "#{k}-head", {st + 0.12}, {{ stagger: 0.1 }});')
            J.append(f'tl.fromTo("#{k}-c1", {{ x: -220, opacity: 0, rotate: -4 }}, {{ x: 0, opacity: 1, rotate: 0, duration: 0.8, ease: "expo.out" }}, {st + 0.4});')
            J.append(f'tl.fromTo("#{k}-c2", {{ x: 220, opacity: 0, rotate: 4 }}, {{ x: 0, opacity: 1, rotate: 0, duration: 0.8, ease: "expo.out" }}, {round(st + 0.4 + vd * 0.45, 2)});')
            J.append(f'tl.to("#{k}-c1", {{ y: -30, duration: {round(du - 1.2, 2)}, ease: "sine.inOut" }}, {st + 1.2});')
            J.append(f'tl.to("#{k}-c2", {{ y: -70, duration: {round(du - 1.2 - vd*0.45, 2)}, ease: "sine.inOut" }}, {round(st + 1.2 + vd * 0.45, 2)});')
            sfx("whoosh.mp3", st + 0.4 + vd * 0.45, 0.25, 7)
        elif ty == "statement":
            n = len(sc["head"]); top = int(900 - n * 88 / 2)
            s0 = round(st - 0.45, 2)
            H.append(f'''  <section id="{k}" class="scene clip" data-start="{s0}" data-duration="{round(du + 0.45, 2)}" data-track-index="3">
    <div class="xbg" id="{k}-bg"></div>
    <div class="cam" id="{k}-cam">
      <div class="display xstate" id="{k}-head" style="top:{top}px">{head_html(sc["head"], False)}</div>
      <div class="xwrule" id="{k}-rule" style="top:{top + n*88 + 50}px"></div>
    </div>
  </section>''')
            J.append(f'tl.fromTo("#{k}-bg", {{ yPercent: 100 }}, {{ yPercent: 0, duration: 0.45, ease: "expo.in" }}, {s0});')
            J.append(f'K.push(tl, "#{k}-cam", {st}, {end}, 1.0, 1.06);')
            J.append(f'K.words(tl, "#{k}-head", {st + 0.1}, {{ stagger: 0.14 }});')
            J.append(f'K.line(tl, "#{k}-rule", {st + 0.6}, 0.9);')
            J.append(f'tl.to("#tag", {{ opacity: 0, duration: 0.2 }}, {s0});')
            J.append(f'tl.to("#tag", {{ opacity: 1, duration: 0.3 }}, {end});')
            sfx("whoosh.mp3", st, 0.45, 6)
        elif ty == "process":
            nodes = "".join(f'<div class="pnode" id="{k}-n{j+1}" style="top:{470 + j*180}px"><span class="pd"></span><div><div class="ptt">{a}</div><div class="pds">{b}</div></div></div>' for j, (a, b) in enumerate(PROC))
            H.append(f'''  <section id="{k}" class="scene dark clip" data-start="{st}" data-duration="{du}" data-track-index="2">
    <div class="cam" id="{k}-cam">
      <div class="label xlab" id="{k}-lab" style="color:#9aa0ab">How we work</div>
      <i class="pline"></i><i class="pfill" id="{k}-fill"></i>
      {nodes}
    </div>
  </section>''')
            words = {w: t_ for w, t_ in vx[i].get("words", [])}
            keys = ["discover", "plan", "build", "grow"]
            times = [max(round(st + 0.75, 2), round(vo_at + words.get(w, vd * (j + 1) / 5), 2)) for j, w in enumerate(keys)]
            times = [max(times[j], times[j-1] + 0.3) if j else times[j] for j in range(4)]
            J.append(f'K.push(tl, "#{k}-cam", {st}, {end}, 1.0, 1.04);')
            J.append(f'tl.fromTo("#{k}-lab", {{ opacity: 0, y: 20 }}, {{ opacity: 1, y: 0, duration: 0.5, ease: "expo.out" }}, {st + 0.05});')
            J.append(f'tl.fromTo("#{k} .pnode", {{ opacity: 0, x: 60 }}, {{ opacity: 0.3, x: 0, duration: 0.4, ease: "expo.out", stagger: 0.05 }}, {st + 0.05});')
            J.append(f'tl.fromTo("#{k}-fill", {{ scaleY: 0, transformOrigin: "50% 0" }}, {{ scaleY: 0, duration: 0.01 }}, {st});')
            for j, tt in enumerate(times):
                J.append(f'tl.to("#{k}-n{j+1}", {{ opacity: 1, duration: 0.25 }}, {tt});')
                J.append(f'tl.to("#{k}-n{j+1} .pd", {{ backgroundColor: "#245BFF", borderColor: "#245BFF", duration: 0.2 }}, {tt});')
                J.append(f'tl.fromTo("#{k}-n{j+1} .pd", {{ scale: 1 }}, {{ scale: 1.2, duration: 0.15, yoyo: true, repeat: 1, ease: "power2.out" }}, {tt});')
                if j > 0: J.append(f'tl.to("#{k}-fill", {{ scaleY: {round(j/3, 3)}, duration: {round(min(0.5, tt - times[j-1]), 2)}, ease: "power2.inOut" }}, {times[j-1]});')
                sfx("pop.mp3", tt, 0.3, 8)
        if not last and ty not in ("statement",) and spec["scenes"][i + 1]["type"] != "statement":
            J.append(f'tl.to("#{k}-cam", {{ opacity: 0, y: -40, duration: 0.3, ease: "power3.in" }}, {round(end - 0.3, 2)});')
    # final shutter into the shifted tail
    cut = round(Ssplit + D, 2)
    H.append(f'  <div id="shx" class="shutter clip" data-layout-allow-occlusion data-start="{round(cut - 0.5, 2)}" data-duration="1.1" data-track-index="4"></div>')
    J.append(f'K.shutter(tl, "#shx", {cut});')
    sfx("whoosh-short.mp3", cut, 0.5, 6)
    # ---- splice HTML
    first_scene = re.search(r'\n\s*<!-- [^\n]*-->\n\s*<section', src)
    ins_at = src.index('<div id="bg"'); ins_at = src.index("\n", ins_at) + 1
    src = src[:ins_at] + "\n  <!-- EXTENDED SCENES (generated by _shared/extend.py) -->\n" + "\n".join(H) + "\n" + src[ins_at:]
    rclose = src.rindex("</div>\n\n<script>")
    src = src[:rclose] + "  <!-- extended voiceover + sfx -->\n" + "\n".join(A) + "\n" + src[rclose:]
    # ---- splice JS
    mk = spec["marker"]; mi = src.index(mk); ri = src.index('window.__timelines["main"] = tl;')
    tail = src[mi:ri]
    tail = re.sub(r'\btl\.', 'te.', tail); tail = re.sub(r'\(tl,', '(te,', tail)
    newjs = "  /* EXTENDED */\n  /* ---- extended scenes (absolute times) ---- */\n  " + "\n  ".join(J) + "\n\n  /* ---- original scenes after the split, shifted by D on a nested timeline ---- */\n  const D = " + str(D) + ";\n  const te = gsap.timeline();\n  "
    src = src[:mi] + newjs + tail + "tl.add(te, D);\n  " + src[ri:]
    # ---- root duration
    open(p, "w").write(src)
    for i in range(len(spec["scenes"])):
        shutil.copy(f"{ROOT}/_shared/vo/{v}_x{i+1}.wav", f"{d}/assets/vo/")
    for sc in spec["scenes"]:
        for f, _ in sc.get("cards", []):
            shutil.copy(f"{ROOT}/_brand/raw/{f}", f"{d}/assets/work/")
    shutil.copy(f"{ROOT}/_shared/kit/brand.css", f"{d}/assets/brand.css")
    for f in ("pop.mp3", "click-soft.mp3", "whoosh.mp3", "whoosh-short.mp3"):
        shutil.copy(f"{ROOT}/_shared/kit/sfx/{f}", f"{d}/assets/sfx/")
    print(v, "D =", D, "total =", re.search(r'id="root"[^>]*data-duration="([\d.]+)"', src).group(1))

for v in (sys.argv[1:] or S.keys()): build(v)
