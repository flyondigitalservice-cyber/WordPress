/* Bombay Marketing — shared motion kit. Every helper only adds tweens to a
   paused timeline at absolute times, so every frame is a pure function of time. */
(function () {
  const K = {};

  /* Word-mask reveal: animates every .w inside `sel` up from below its mask. */
  K.words = function (tl, sel, t, o) {
    o = o || {};
    tl.fromTo(sel + " .w", { yPercent: 115 }, {
      yPercent: 0, duration: o.dur || 0.8, ease: "expo.out", stagger: o.stagger || 0.07,
    }, t);
    return tl;
  };
  /* Words exit upward. */
  K.wordsOut = function (tl, sel, t, o) {
    o = o || {};
    tl.to(sel + " .w", { yPercent: -115, duration: o.dur || 0.4, ease: "power3.in", stagger: o.stagger || 0.03 }, t);
    return tl;
  };
  /* The Blue Line draws on from the left. */
  K.line = function (tl, sel, t, dur) {
    tl.fromTo(sel, { scaleX: 0 }, { scaleX: 1, duration: dur || 0.9, ease: "expo.out" }, t);
    return tl;
  };
  /* Slow continuous camera push on a .cam wrapper (never a static frame). */
  K.push = function (tl, sel, t0, t1, from, to) {
    tl.fromTo(sel, { scale: from || 1 }, { scale: to || 1.06, duration: t1 - t0, ease: "sine.inOut" }, t0);
    return tl;
  };
  /* The Blue Shutter: rises from below, covers, exits through the top.
     `t` is the moment the frame is fully covered (the cut point). */
  K.shutter = function (tl, sel, t) {
    tl.fromTo(sel, { yPercent: 100 }, { yPercent: 0, duration: 0.42, ease: "expo.in" }, t - 0.42);
    tl.to(sel, { yPercent: -100, duration: 0.5, ease: "expo.out" }, t + 0.07);
    return tl;
  };
  /* A Frame (UI card) arriving in 3D. */
  K.frameIn = function (tl, sel, t, o) {
    o = o || {};
    tl.fromTo(sel, { rotateX: o.rx != null ? o.rx : 28, y: o.y != null ? o.y : 220, opacity: 0, scale: o.s || 0.94 },
      { rotateX: 0, y: 0, opacity: 1, scale: 1, duration: o.dur || 1.0, ease: "expo.out" }, t);
    return tl;
  };
  /* Fade/slide in a simple element. */
  K.rise = function (tl, sel, t, o) {
    o = o || {};
    tl.fromTo(sel, { y: o.y != null ? o.y : 40, opacity: 0 },
      { y: 0, opacity: 1, duration: o.dur || 0.7, ease: "expo.out", stagger: o.stagger || 0 }, t);
    return tl;
  };
  K.pop = function (tl, sel, t, o) {
    o = o || {};
    tl.fromTo(sel, { scale: 0.6, opacity: 0 }, { scale: 1, opacity: 1, duration: o.dur || 0.5,
      ease: "back.out(1.6)", stagger: o.stagger || 0 }, t);
    return tl;
  };
  /* Series tag (icon + number + title). */
  K.tag = function (tl, sel, t) {
    tl.fromTo(sel, { opacity: 0, x: -20 }, { opacity: 1, x: 0, duration: 0.6, ease: "expo.out" }, t);
    return tl;
  };
  /* Signature end card. `p` = id prefix ("end"). Expects:
     #end-cam, #end-head (with .w words), #end-rule, #end-logo, #end-url, #end-tag */
  K.endcard = function (tl, t) {
    K.push(tl, "#end-cam", t, t + 4.5, 1.0, 1.06);
    K.words(tl, "#end-head", t + 0.15, { stagger: 0.09 });
    K.line(tl, "#end-rule", t + 0.75, 1.0);
    tl.fromTo("#end-logo", { y: 60, opacity: 0 }, { y: 0, opacity: 1, duration: 1.0, ease: "expo.out" }, t + 1.05);
    tl.fromTo("#end-url", { opacity: 0, y: 16 }, { opacity: 1, y: 0, duration: 0.7, ease: "expo.out" }, t + 1.6);
    tl.fromTo("#end-tag", { opacity: 0 }, { opacity: 1, duration: 0.6, ease: "sine.out" }, t + 1.8);
    return tl;
  };
  /* Typewriter: reveals characters of a pre-split element (.ch spans). */
  K.type = function (tl, sel, t, perChar) {
    tl.fromTo(sel + " .ch", { opacity: 0 }, { opacity: 1, duration: 0.01, stagger: perChar || 0.055 }, t);
    return tl;
  };
  /* Split an element's text into .ch spans (static, before the timeline is built). */
  K.split = function (sel) {
    document.querySelectorAll(sel).forEach(function (el) {
      const txt = el.textContent; el.textContent = "";
      for (const c of txt) { const s = document.createElement("span"); s.className = "ch"; s.textContent = c; el.appendChild(s); }
    });
  };
  window.BM = K;
})();
