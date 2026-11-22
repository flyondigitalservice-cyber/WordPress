"""Generate Packaging Series reels (p1..p3) from _shared/series_spec.json.
Same design kit as the campaign: brand.css + kit.js, Blue Shutter, signature end card,
shared scene types (problem/steps/statement/process) plus hook/wall/spotlight/morph/video."""
import json, os, sys, html, shutil, subprocess

ROOT = os.path.dirname(os.path.abspath(__file__)) + "/.."
SPEC = json.load(open(ROOT + "/_shared/series_spec.json"))
VX = json.load(open(ROOT + "/_shared/vo_series.json"))
CHECK = '<svg viewBox="0 0 40 40"><path d="M8 21l8 8 16-17" fill="none" stroke="#fff" stroke-width="6" stroke-linecap="round" stroke-linejoin="round"/></svg>'
PROC = [("Discover", "Understand your business &amp; goals"), ("Plan", "Strategy, structure &amp; timeline"), ("Build", "Design, develop &amp; launch"), ("Grow", "Measure, improve &amp; scale")]
PEAK = {"whoosh.mp3": 0.16, "whoosh-short.mp3": 0.16, "pop.mp3": 0.12, "click-soft.mp3": 0.07, "chime.mp3": 0.42, "impact-bass-1.mp3": 0.07}

def esc(s): return html.escape(s, quote=False)
def heads(lines, blue_last=True, cls=""):
    o = []
    for i, l in enumerate(lines):
        c = "w blue" if (blue_last and len(lines) > 1 and i == len(lines) - 1) else "w"
        o.append(f'<span class="line"><span class="wm"><span class="{c}">{esc(l)}</span></span></span>')
    return "".join(o)

def r2(x): return round(x, 2)

def build(v):
    spec = SPEC[v]; vx = VX[v]; D = f"{ROOT}/{v}"
    scenes = spec["scenes"]
    # ---- timing
    T = []; t = 0.0
    for i, sc in enumerate(scenes):
        vd = vx[i]["dur"]; ty = sc["type"]
        lead = 0.2 if ty == "hook" else (0.35 if ty != "end" else 1.2)
        tail = {"hook": 1.6, "problem": 1.7, "steps": 1.4, "statement": 1.8, "process": 1.3, "wall": 1.9, "spotlight": 1.8, "morph": 1.8, "video": 1.5, "end": 0, "rapid": 0.6}[ty]
        dur = max(4.4, lead + vd + tail)
        if ty == "video": dur = max(dur, sc["len"] + 1.4)
        if ty == "rapid": dur = max(dur, len(sc["images"]) * sc.get("shot", 0.72) + 0.5)
        if ty == "end": dur = max(5.0, lead + vd + 2.2)
        dur = r2(dur); T.append((r2(t), dur, r2(t + lead))); t = r2(t + dur)
    TOTAL = t
    Hs, J, A = [], [], []
    n_sfx = [0]
    def sfx(name, at, vol, track=6):
        n_sfx[0] += 1
        A.append(f'  <audio id="fx{n_sfx[0]}" class="clip" src="assets/sfx/{name}" data-start="{r2(max(0, at - PEAK.get(name, 0.1)))}" data-track-index="{track}" data-volume="{vol}"></audio>')
    images = set()
    end_start = T[-1][0]
    for i, sc in enumerate(scenes):
        st, du, vo_at = T[i]; end = r2(st + du); k = f"s{i+1}"; ty = sc["type"]; vd = vx[i]["dur"]
        A.append(f'  <audio id="vo{i+1}" class="clip" src="assets/vo/{v}_{i+1}.wav" data-start="{vo_at}" data-track-index="5" data-volume="1"></audio>')
        nl = len(sc.get("head", [])); items_top = 440 + nl * 68 + 56
        nxt = scenes[i + 1]["type"] if i + 1 < len(scenes) else None
        fade_out = nxt not in (None, "statement", "end") and i != 0
        if i > 1 and ty not in ("statement", "end"): sfx(["whoosh-short.mp3", "whoosh.mp3"][i % 2], st, 0.4, 7)

        if ty == "hook":
            dark = sc.get("bg") == "dark"
            images.add(sc["image"])
            Hs.append(f'''  <section id="{k}" class="scene {'dark' if dark else 'light'} clip" data-start="{st}" data-duration="{du}" data-track-index="1" data-layout-allow-overlap>
    <div class="cam" id="{k}-cam">
      <div class="hook display" id="{k}-hook" style="top:330px;font-size:104px">{''.join(f'<span class="line" id="{k}-l1">{esc(l)}</span>' if j == 0 else f'<span class="line"><span class="wm"><span class="{"w blue" if j == nl-1 else "w"}">{esc(l)}</span></span></span>' for j, l in enumerate(sc["head"]))}</div>
      <div class="bline" id="{k}-rule" style="position:absolute;left:120px;top:{330 + nl*96 + 40}px;width:360px"></div>
      <div class="xcard" id="{k}-img" style="left:180px;top:{330 + nl*96 + 110}px;width:720px;height:450px"><img src="assets/work/{sc['image']}" alt="" style="object-fit:{sc.get('fit','cover')}" /></div>
    </div>
  </section>''')
            J += [f'K.push(tl, "#{k}-cam", {st}, {end}, 1.0, 1.05);',
                  f'tl.fromTo("#{k}-l1", {{ opacity: 0.85, y: 14 }}, {{ opacity: 1, y: 0, duration: 0.6, ease: "expo.out" }}, {st});',
                  f'K.words(tl, "#{k}-hook", {st + 0.25}, {{ stagger: 0.14 }});',
                  f'K.line(tl, "#{k}-rule", {st + 0.9}, 0.9);',
                  f'tl.fromTo("#{k}-img", {{ y: 260, opacity: 0, rotateX: 25 }}, {{ y: 0, opacity: 1, rotateX: 0, duration: 1.0, ease: "expo.out" }}, {st + 1.0});']
        elif ty == "problem":
            its = "".join(f'<div class="pain" id="{k}-p{j+1}" style="top:{items_top + j*140}px"><span class="x"></span><span class="pt">{esc(x)}</span><i class="strike" data-layout-allow-occlusion style="width:{min(660, 20*len(x))}px"></i></div>' for j, x in enumerate(sc["items"]))
            Hs.append(f'''  <section id="{k}" class="scene soft clip" data-start="{st}" data-duration="{du}" data-track-index="2" data-layout-allow-overlap>
    <div class="cam" id="{k}-cam"><div class="label mute xlab" id="{k}-lab">{esc(sc["label"])}</div><div class="display xhead" id="{k}-head">{heads(sc["head"])}</div>{its}</div>
  </section>''')
            J += [f'K.push(tl, "#{k}-cam", {st}, {end}, 1.0, 1.04);',
                  f'tl.fromTo("#{k}-lab", {{ opacity: 0, y: 20 }}, {{ opacity: 1, y: 0, duration: 0.5, ease: "expo.out" }}, {st + 0.05});',
                  f'K.words(tl, "#{k}-head", {st + 0.12}, {{ stagger: 0.1 }});']
            n = len(sc["items"])
            for j in range(n):
                J.append(f'tl.fromTo("#{k}-p{j+1}", {{ opacity: 0, x: 120 }}, {{ opacity: 1, x: 0, duration: 0.6, ease: "expo.out" }}, {r2(vo_at + vd*(j+0.4)/n - 0.3)});')
            sk = r2(end - 1.2)
            J += [f'tl.fromTo("#{k} .strike", {{ scaleX: 0, transformOrigin: "0 50%" }}, {{ scaleX: 1, duration: 0.45, ease: "expo.inOut", stagger: 0.12 }}, {sk});',
                  f'tl.to("#{k} .pain", {{ opacity: 0.55, duration: 0.4, stagger: 0.12 }}, {r2(sk + 0.2)});']
            sfx("whoosh-short.mp3", sk + 0.2, 0.25, 8)
        elif ty == "steps":
            its = "".join(f'<div class="step" id="{k}-p{j+1}" style="top:{items_top + j*132}px"><span class="n">{j+1:02d}</span><span class="pt">{esc(x)}</span><span class="xck"><span class="f"></span>{CHECK}</span></div>' for j, x in enumerate(sc["items"]))
            Hs.append(f'''  <section id="{k}" class="scene light clip" data-start="{st}" data-duration="{du}" data-track-index="2" data-layout-allow-overlap>
    <div class="cam" id="{k}-cam"><div class="label mute xlab" id="{k}-lab">{esc(sc["label"])}</div><div class="display xhead" id="{k}-head">{heads(sc["head"])}</div>{its}</div>
  </section>''')
            J += [f'K.push(tl, "#{k}-cam", {st}, {end}, 1.0, 1.04);',
                  f'tl.fromTo("#{k}-lab", {{ opacity: 0, y: 20 }}, {{ opacity: 1, y: 0, duration: 0.5, ease: "expo.out" }}, {st + 0.05});',
                  f'K.words(tl, "#{k}-head", {st + 0.12}, {{ stagger: 0.1 }});']
            n = len(sc["items"])
            for j in range(n):
                at = r2(vo_at + vd * j / n)
                J += [f'tl.fromTo("#{k}-p{j+1}", {{ opacity: 0, y: 50 }}, {{ opacity: 1, y: 0, duration: 0.6, ease: "expo.out" }}, {at});',
                      f'tl.fromTo("#{k}-p{j+1} .f", {{ scale: 0 }}, {{ scale: 1, duration: 0.35, ease: "back.out(1.8)" }}, {r2(at + 0.45)});',
                      f'tl.fromTo("#{k}-p{j+1} svg", {{ opacity: 0 }}, {{ opacity: 1, duration: 0.15 }}, {r2(at + 0.55)});']
                sfx("click-soft.mp3" if j % 2 == 0 else "pop.mp3", at + 0.45, 0.7 if j % 2 == 0 else 0.3, 8)
        elif ty == "wall":
            ims = sc["images"]; cols = 3; tw = 256; gap = 16
            tiles = "".join(f'<div class="wt2" style="left:{(j%cols)*(tw+gap)}px;top:{(j//cols)*(tw+gap)}px"><img src="assets/work/{f}.jpg" alt="" /></div>' for j, f in enumerate(ims))
            for f in ims: images.add(f + ".jpg")
            rows = (len(ims) + cols - 1) // cols; wall_h = rows * (tw + gap)
            win_top = items_top - 20; win_h = 1250 - win_top
            Hs.append(f'''  <section id="{k}" class="scene dark clip" data-start="{st}" data-duration="{du}" data-track-index="2" data-layout-allow-overlap>
    <div class="cam" id="{k}-cam">
      <div class="label xlab" id="{k}-lab" style="color:#9aa0ab">{esc(sc["label"])}</div>
      <div class="display xhead" id="{k}-head" style="color:#fff">{heads(sc["head"])}</div>
      <div class="wwin" data-layout-allow-overflow style="top:{win_top}px;height:{win_h}px"><div class="wgrid" id="{k}-grid">{tiles}</div></div>
    </div>
  </section>''')
            travel = max(0, wall_h - win_h + 40)
            J += [f'K.push(tl, "#{k}-cam", {st}, {end}, 1.0, 1.03);',
                  f'tl.fromTo("#{k}-lab", {{ opacity: 0, y: 20 }}, {{ opacity: 1, y: 0, duration: 0.5, ease: "expo.out" }}, {st + 0.05});',
                  f'K.words(tl, "#{k}-head", {st + 0.12}, {{ stagger: 0.1 }});',
                  f'tl.fromTo("#{k} .wt2", {{ opacity: 0, scale: 0.85 }}, {{ opacity: 1, scale: 1, duration: 0.5, ease: "expo.out", stagger: 0.035 }}, {st + 0.3});',
                  f'tl.fromTo("#{k}-grid", {{ y: 40 }}, {{ y: -{travel}, duration: {r2(du - 0.5)}, ease: "sine.inOut" }}, {st + 0.3});']
        elif ty == "spotlight":
            ims = sc["images"]; n = len(ims)
            for f, _ in ims: images.add(f)
            cards = "".join(f'<div class="spot" data-layout-allow-occlusion id="{k}-i{j+1}" style="top:{items_top}px"><img src="assets/work/{f}" alt="{esc(nm)}" /><span class="chip xchip" style="bottom:22px">{esc(nm)}</span></div>' for j, (f, nm) in enumerate(ims))
            Hs.append(f'''  <section id="{k}" class="scene soft clip" data-start="{st}" data-duration="{du}" data-track-index="2" data-layout-allow-overlap>
    <div class="cam" id="{k}-cam">
      <div class="label mute xlab" id="{k}-lab">{esc(sc["label"])}</div>
      <div class="display xhead" id="{k}-head">{heads(sc["head"])}</div>
      {cards}
      <div class="sdots" style="top:{items_top + 660}px">{''.join(f'<i id="{k}-d{j+1}"></i>' for j in range(n))}</div>
    </div>
  </section>''')
            J += [f'K.push(tl, "#{k}-cam", {st}, {end}, 1.0, 1.03);',
                  f'tl.fromTo("#{k}-lab", {{ opacity: 0, y: 20 }}, {{ opacity: 1, y: 0, duration: 0.5, ease: "expo.out" }}, {st + 0.05});',
                  f'K.words(tl, "#{k}-head", {st + 0.12}, {{ stagger: 0.1 }});']
            seg = (du - 0.4) / n
            for j in range(n):
                a = r2(st + 0.35 + j * seg)
                if j == 0:
                    J.append(f'tl.fromTo("#{k}-i1", {{ opacity: 0, y: 160, rotateX: 18 }}, {{ opacity: 1, y: 0, rotateX: 0, duration: 0.9, ease: "expo.out" }}, {a});')
                else:
                    J.append(f'tl.fromTo("#{k}-i{j+1}", {{ opacity: 0, x: 300, rotateY: -25 }}, {{ opacity: 1, x: 0, rotateY: 0, duration: 0.7, ease: "expo.out" }}, {a});')
                    J.append(f'tl.to("#{k}-i{j}", {{ opacity: 0, x: -300, rotateY: 25, duration: 0.6, ease: "expo.inOut" }}, {a});')
                    sfx("whoosh-short.mp3" if j % 2 else "whoosh.mp3", a + 0.2, 0.25, 8)
                J.append(f'tl.fromTo("#{k}-i{j+1} img", {{ scale: 1.08 }}, {{ scale: 1.0, duration: {r2(seg + 0.6)}, ease: "sine.out" }}, {a});')
                J.append(f'tl.to("#{k}-d{j+1}", {{ backgroundColor: "#245BFF", scale: 1.3, duration: 0.25 }}, {a});')
                if j > 0: J.append(f'tl.to("#{k}-d{j}", {{ backgroundColor: "#d1d5db", scale: 1, duration: 0.25 }}, {a});')
            J.append(f'tl.set("#{k} .spot", {{ opacity: 0 }}, {st});')
        elif ty == "rapid":
            ims = sc["images"]; n = len(ims); sd = (du - 0.5) / n
            for f, _ in ims: images.add(f)
            shots = "".join(f'<div class="rs" data-layout-allow-occlusion id="{k}-r{j+1}" style="top:{items_top}px"><img src="assets/work/{f}" alt="{esc(nm)}" /><span class="chip xchip" style="bottom:22px">{esc(nm)}</span></div>' for j, (f, nm) in enumerate(ims))
            Hs.append(f'''  <section id="{k}" class="scene {sc.get("bg","soft")} clip" data-start="{st}" data-duration="{du}" data-track-index="2" data-layout-allow-overlap>
    <div class="cam" id="{k}-cam">
      <div class="label xlab" id="{k}-lab" style="color:var(--blue)">{esc(sc["label"])}</div>
      <div class="display xhead" id="{k}-head" style="{'color:#fff' if sc.get('bg')=='dark' else ''}">{heads(sc["head"])}</div>
      {shots}
      <div class="rbar" style="top:{items_top + 700}px"><i id="{k}-bar"></i></div>
    </div>
  </section>''')
            J += [f'K.push(tl, "#{k}-cam", {st}, {end}, 1.0, 1.03);',
                  f'tl.fromTo("#{k}-lab", {{ opacity: 0, y: 20 }}, {{ opacity: 1, y: 0, duration: 0.4, ease: "expo.out" }}, {st});',
                  f'K.words(tl, "#{k}-head", {st + 0.05}, {{ stagger: 0.08, dur: 0.6 }});',
                  f'tl.set("#{k} .rs", {{ opacity: 0 }}, {st});',
                  f'tl.fromTo("#{k}-bar", {{ scaleX: 0, transformOrigin: "0 50%" }}, {{ scaleX: 1, duration: {r2(du - 0.4)}, ease: "none" }}, {st + 0.2});']
            moves = [("x: 90", "x: 0"), ("y: 90", "y: 0"), ("scale: 1.14", "scale: 1"), ("x: -90", "x: 0"), ("rotate: -3, scale: 1.06", "rotate: 0, scale: 1")]
            for j in range(n):
                a = r2(st + 0.25 + j * sd); fr, to = moves[j % len(moves)]
                J.append(f'tl.fromTo("#{k}-r{j+1}", {{ opacity: 1, {fr} }}, {{ opacity: 1, {to}, duration: {r2(min(0.5, sd))}, ease: "expo.out" }}, {a});')
                J.append(f'tl.fromTo("#{k}-r{j+1} img", {{ scale: 1.0 }}, {{ scale: 1.06, duration: {r2(sd + 0.1)}, ease: "none" }}, {a});')
                if j < n - 1: J.append(f'tl.set("#{k}-r{j+1}", {{ opacity: 0 }}, {r2(a + sd)});')
        elif ty == "morph":
            (ff, fn), (bf, bn) = sc["front"], sc["back"]; images.add(ff); images.add(bf)
            Hs.append(f'''  <section id="{k}" class="scene light clip" data-start="{st}" data-duration="{du}" data-track-index="2" data-layout-allow-overlap>
    <div class="cam" id="{k}-cam">
      <div class="label xlab" id="{k}-lab" style="color:var(--blue)">{esc(sc["step"])}</div>
      <div class="display xhead" id="{k}-head">{heads(sc["head"])}</div>
      <div class="flip" style="top:{items_top + 10}px"><div class="fin2" id="{k}-fin">
        <div class="face2"><img src="assets/work/{ff}" alt="{esc(fn)}" /><span class="chip xchip" style="bottom:22px">{esc(fn)}</span></div>
        <div class="face2 back2"><img src="assets/work/{bf}" alt="{esc(bn)}" /><span class="chip xchip" style="bottom:22px;background:var(--blue)">{esc(bn)}</span></div>
      </div></div>
    </div>
  </section>''')
            flip = r2(vo_at + vd * 0.55)
            J += [f'K.push(tl, "#{k}-cam", {st}, {end}, 1.0, 1.04);',
                  f'tl.fromTo("#{k}-lab", {{ opacity: 0, y: 20 }}, {{ opacity: 1, y: 0, duration: 0.5, ease: "expo.out" }}, {st + 0.05});',
                  f'K.words(tl, "#{k}-head", {st + 0.12}, {{ stagger: 0.1 }});',
                  f'tl.fromTo("#{k}-fin", {{ opacity: 0, y: 160, rotateY: 0 }}, {{ opacity: 1, y: 0, duration: 0.9, ease: "expo.out" }}, {st + 0.35});',
                  f'tl.to("#{k}-fin", {{ rotateY: 180, duration: 1.0, ease: "expo.inOut" }}, {flip});']
            sfx("whoosh.mp3", flip + 0.5, 0.35, 8)
        elif ty == "video":
            vid_top = items_top + 40
            Hs.append(f'''  <section id="{k}" class="scene light clip" data-start="{st}" data-duration="{du}" data-track-index="2" data-layout-allow-overlap>
    <div class="cam" id="{k}-cam">
      <div class="label xlab" id="{k}-lab" style="color:var(--blue)">{esc(sc["step"])}</div>
      <div class="display xhead" id="{k}-head">{heads(sc["head"])}</div>
      <div class="vframe" id="{k}-vf" style="top:{vid_top - 14}px"></div>
    </div>
  </section>''')
            A.append(f'  <video id="{k}-video" class="clip" src="assets/work/{sc["src"]}" muted playsinline data-start="{r2(st + 0.5)}" data-duration="{r2(min(sc["len"], du - 0.6))}" data-track-index="9" style="position:absolute;left:204px;top:{vid_top}px;width:672px;height:504px;border-radius:20px;object-fit:cover;z-index:6"></video>')
            images.add(sc["src"])
            J += [f'K.push(tl, "#{k}-cam", {st}, {end}, 1.0, 1.03);',
                  f'tl.fromTo("#{k}-lab", {{ opacity: 0, y: 20 }}, {{ opacity: 1, y: 0, duration: 0.5, ease: "expo.out" }}, {st + 0.05});',
                  f'K.words(tl, "#{k}-head", {st + 0.12}, {{ stagger: 0.1 }});',
                  f'tl.fromTo("#{k}-vf", {{ opacity: 0, scale: 0.94 }}, {{ opacity: 1, scale: 1, duration: 0.6, ease: "expo.out" }}, {st + 0.2});']
        elif ty == "statement":
            n = nl; top = int(900 - n * 88 / 2); s0 = r2(st - 0.45)
            Hs.append(f'''  <section id="{k}" class="scene clip" data-start="{s0}" data-duration="{r2(du + 0.45)}" data-track-index="3">
    <div class="xbg" id="{k}-bg"></div>
    <div class="cam" id="{k}-cam"><div class="display xstate" id="{k}-head" style="top:{top}px">{heads(sc["head"], False)}</div><div class="xwrule" id="{k}-rule" style="top:{top + n*88 + 50}px"></div></div>
  </section>''')
            J += [f'tl.fromTo("#{k}-bg", {{ yPercent: 100 }}, {{ yPercent: 0, duration: 0.45, ease: "expo.in" }}, {s0});',
                  f'K.push(tl, "#{k}-cam", {st}, {end}, 1.0, 1.06);',
                  f'K.words(tl, "#{k}-head", {st + 0.1}, {{ stagger: 0.14 }});',
                  f'K.line(tl, "#{k}-rule", {st + 0.6}, 0.9);',
                  f'tl.to("#tag", {{ opacity: 0, duration: 0.2 }}, {s0});', f'tl.to("#tag", {{ opacity: 1, duration: 0.3 }}, {end});']
            sfx("whoosh.mp3", st, 0.45, 7)
        elif ty == "process":
            nodes = "".join(f'<div class="pnode" id="{k}-n{j+1}" style="top:{470 + j*180}px"><span class="pd"></span><div><div class="ptt">{a}</div><div class="pds">{b}</div></div></div>' for j, (a, b) in enumerate(PROC))
            Hs.append(f'''  <section id="{k}" class="scene dark clip" data-start="{st}" data-duration="{du}" data-track-index="2">
    <div class="cam" id="{k}-cam"><div class="label xlab" id="{k}-lab" style="color:#9aa0ab">How we work</div><i class="pline"></i><i class="pfill" id="{k}-fill"></i>{nodes}</div>
  </section>''')
            words = {w: tt for w, tt in vx[i].get("words", [])}
            tms = [max(r2(st + 0.75), r2(vo_at + words.get(w, vd * (j + 1) / 5))) for j, w in enumerate(["discover", "plan", "build", "grow"])]
            tms = [max(tms[j], tms[j-1] + 0.3) if j else tms[j] for j in range(4)]
            J += [f'K.push(tl, "#{k}-cam", {st}, {end}, 1.0, 1.04);',
                  f'tl.fromTo("#{k}-lab", {{ opacity: 0, y: 20 }}, {{ opacity: 1, y: 0, duration: 0.5, ease: "expo.out" }}, {st + 0.05});',
                  f'tl.fromTo("#{k} .pnode", {{ opacity: 0, x: 60 }}, {{ opacity: 0.3, x: 0, duration: 0.4, ease: "expo.out", stagger: 0.05 }}, {st + 0.05});',
                  f'tl.fromTo("#{k}-fill", {{ scaleY: 0, transformOrigin: "50% 0" }}, {{ scaleY: 0, duration: 0.01 }}, {st});']
            for j, tt in enumerate(tms):
                J += [f'tl.to("#{k}-n{j+1}", {{ opacity: 1, duration: 0.25 }}, {tt});',
                      f'tl.to("#{k}-n{j+1} .pd", {{ backgroundColor: "#245BFF", borderColor: "#245BFF", duration: 0.2 }}, {tt});',
                      f'tl.fromTo("#{k}-n{j+1} .pd", {{ scale: 1 }}, {{ scale: 1.2, duration: 0.15, yoyo: true, repeat: 1, ease: "power2.out" }}, {tt});']
                if j: J.append(f'tl.to("#{k}-fill", {{ scaleY: {round(j/3, 3)}, duration: {r2(min(0.5, tt - tms[j-1]))}, ease: "power2.inOut" }}, {tms[j-1]});')
                sfx("pop.mp3", tt, 0.3, 8)
        elif ty == "end":
            n = nl; top = 560 if n == 2 else 440
            Hs.append(f'''  <section id="end" class="scene end clip" data-start="{st}" data-duration="{du}" data-track-index="1">
    <div class="cam" id="end-cam">
      <div class="tag end-tag" id="end-tag"><img src="assets/brand/icon.png" alt="" /><span class="label mute">{esc(spec["tag"].split(" · ")[0])}</span></div>
      <div class="display end-head" id="end-head" style="top:{top}px">{heads(sc["head"])}</div>
      <div class="bline end-rule" id="end-rule"></div>
      <img class="end-logo" id="end-logo" src="assets/brand/logo-white.png" alt="Bombay Marketing" />
      <div class="end-url" id="end-url">bombaymarketing.in</div>
    </div>
  </section>''')
            J.append(f'K.endcard(tl, {st}, 0.8);')
            sfx("chime.mp3", st + 0.84 + 0.25, 0.8, 7); sfx("impact-bass-1.mp3", st + 0.84 + 0.3, 0.5, 8)
        if fade_out:
            J.append(f'tl.to("#{k}-cam", {{ opacity: 0, y: -40, duration: 0.3, ease: "power3.in" }}, {r2(end - 0.3)});')
    # shutters: hook → scene 2, process → end
    sh1 = T[1][0]; sh2 = end_start
    Hs.append(f'  <div id="sh1" class="shutter clip" data-layout-allow-occlusion data-start="{r2(sh1 - 0.5)}" data-duration="1.1" data-track-index="4"></div>')
    Hs.append(f'  <div id="sh2" class="shutter clip" data-layout-allow-occlusion data-start="{r2(sh2 - 0.5)}" data-duration="1.1" data-track-index="4"></div>')
    J += [f'K.shutter(tl, "#sh1", {sh1});', f'K.shutter(tl, "#sh2", {sh2});']
    sfx("whoosh-short.mp3", sh1, 0.5, 9); sfx("whoosh-short.mp3", sh2, 0.5, 9)
    tag_s = T[1][0]
    Hs.append(f'  <div id="tag" class="tag clip" data-start="{tag_s}" data-duration="{r2(end_start - tag_s)}" data-track-index="3"><img src="assets/brand/icon.png" alt="" /><span class="label mute">{esc(spec["tag"])}</span></div>')
    page = f'''<!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=1080, height=1920" />
<title>Bombay Marketing — {esc(spec["tag"].split(" · ")[0])} {esc(spec["title"])}</title>
<script src="https://cdn.jsdelivr.net/npm/gsap@3.15.0/dist/gsap.min.js"></script>
<link rel="stylesheet" href="assets/brand.css" />
<script src="assets/kit.js"></script>
<style>
  #bg {{ position: absolute; inset: 0; background: var(--black); }}
  .wwin {{ position: absolute; left: 120px; width: 840px; overflow: hidden; border-radius: 24px; }}
  .wgrid {{ position: absolute; left: 6px; top: 0; width: 828px; }}
  .wt2 {{ position: absolute; width: 256px; height: 256px; border-radius: 20px; overflow: hidden; background: #fff; }}
  .wt2 img {{ width: 100%; height: 100%; object-fit: cover; display: block; }}
  .spot {{ position: absolute; left: 120px; width: 840px; height: 620px; border-radius: 30px; overflow: hidden; background: #fff; box-shadow: var(--shadow); }}
  .spot img {{ width: 100%; height: 100%; object-fit: contain; display: block; }}
  .sdots {{ position: absolute; left: 120px; display: flex; gap: 14px; }}
  .sdots i {{ display: block; width: 14px; height: 14px; border-radius: 50%; background: #d1d5db; }}
  .flip {{ position: absolute; left: 120px; width: 840px; height: 620px; perspective: 1800px; }}
  .fin2 {{ position: absolute; inset: 0; transform-style: preserve-3d; }}
  .face2 {{ position: absolute; inset: 0; border-radius: 30px; overflow: hidden; background: #fff; box-shadow: var(--shadow); backface-visibility: hidden; -webkit-backface-visibility: hidden; }}
  .face2.back2 {{ transform: rotateY(180deg); }}
  .face2 img {{ width: 100%; height: 100%; object-fit: contain; display: block; }}
  .rs {{ position: absolute; left: 120px; width: 840px; height: 680px; border-radius: 30px; overflow: hidden; background: #fff; box-shadow: var(--shadow); }}
  .rs img {{ width: 100%; height: 100%; object-fit: contain; display: block; }}
  .rbar {{ position: absolute; left: 120px; width: 840px; height: 6px; border-radius: 3px; background: rgba(127,127,127,.25); overflow: hidden; }}
  .rbar i {{ display: block; height: 100%; background: var(--blue); }}
  .vframe {{ position: absolute; left: 190px; width: 700px; height: 532px; border-radius: 30px; background: var(--black); box-shadow: var(--shadow); }}
</style>
</head>
<body>
<div id="root" data-composition-id="main" data-start="0" data-duration="{TOTAL}" data-width="1080" data-height="1920">
  <div id="bg" class="clip" data-start="0" data-duration="{TOTAL}" data-track-index="0"></div>
{chr(10).join(Hs)}
{chr(10).join(A)}
</div>

<script>
  window.__timelines = window.__timelines || {{}};
  const tl = gsap.timeline({{ paused: true }});
  const K = window.BM;
  {chr(10).join("  " + j for j in J).lstrip()}
  window.__timelines["main"] = tl;
  tl.seek(0);
</script>
</body>
</html>
'''
    # ---- project scaffold
    os.makedirs(f"{D}/assets/vo", exist_ok=True); os.makedirs(f"{D}/assets/work", exist_ok=True); os.makedirs(f"{D}/renders", exist_ok=True)
    for f in ("hyperframes.json", "package.json"): shutil.copy(f"{ROOT}/_shared/template/{f}", D)
    json.dump({"id": f"bombay-{v}", "name": f"Bombay Marketing {v.upper()} — {spec['title']}"}, open(f"{D}/meta.json", "w"))
    for sub in ("brand", "fonts", "sfx"):
        shutil.copytree(f"{ROOT}/_shared/kit/{sub}", f"{D}/assets/{sub}", dirs_exist_ok=True)
    for f in ("brand.css", "kit.js"): shutil.copy(f"{ROOT}/_shared/kit/{f}", f"{D}/assets/")
    for i in range(len(scenes)): shutil.copy(f"{ROOT}/_shared/vo/{v}_{i+1}.wav", f"{D}/assets/vo/")
    for f in images: shutil.copy(f"{ROOT}/_brand/drive/{f}", f"{D}/assets/work/")
    open(f"{D}/index.html", "w").write(page)
    subprocess.run(["python3", f"{ROOT}/_shared/audiodur.py", D])
    print(v, "total", TOTAL, "scenes", [(s[0], s[1]) for s in T])

for v in (sys.argv[1:] or SPEC.keys()): build(v)
