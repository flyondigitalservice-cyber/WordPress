/* One Love Rider app — vanilla JS, no build step. */
(function () {
  'use strict';

  var C = window.OLE_RIDER || {};
  var state = { online: false, active: [], done: [], known: {}, first: true };
  var watchId = null;
  var lastSent = { t: 0, lat: 0, lng: 0 };
  var wakeLock = null;
  var pollTimer = null;
  var sheetAction = null;

  function $(id) { return document.getElementById(id); }
  function esc(s) {
    return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) {
      return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
    });
  }
  function money(n) { return '₹' + Number(n || 0).toLocaleString('en-IN', { maximumFractionDigits: 2 }); }

  var ICON = {
    nav: '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2 4.5 20.3l.7.7L12 18l6.8 3 .7-.7z"/></svg>',
    call: '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M6.6 10.8a15.1 15.1 0 0 0 6.6 6.6l2.2-2.2a1 1 0 0 1 1-.25 11.4 11.4 0 0 0 3.6.57 1 1 0 0 1 1 1V20a1 1 0 0 1-1 1A17 17 0 0 1 3 4a1 1 0 0 1 1-1h3.5a1 1 0 0 1 1 1c0 1.25.2 2.45.57 3.57a1 1 0 0 1-.25 1z"/></svg>',
    wa: '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 0 0-8.6 15.1L2 22l5-1.3A10 10 0 1 0 12 2zm5.3 14.2c-.2.6-1.3 1.2-1.8 1.2-.5.1-1 .2-3.3-.7-2.8-1.1-4.6-4-4.7-4.2-.1-.2-1.1-1.5-1.1-2.8s.7-2 1-2.3c.2-.3.5-.3.7-.3h.5c.2 0 .4 0 .6.5l.8 2c.1.2.1.3 0 .5l-.3.5-.4.4c-.1.1-.3.3-.1.6.2.3.8 1.3 1.7 2.1 1.2 1 2.1 1.3 2.4 1.5.3.1.5.1.6-.1l.9-1c.2-.3.4-.2.6-.1l1.9.9c.3.1.5.2.5.3.1.2.1.7-.1 1.3z"/></svg>',
    store: '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M4 4h16l1 5a3 3 0 0 1-2 2.8V20H5v-8.2A3 3 0 0 1 3 9zm5 16v-5h6v5"/></svg>'
  };

  /* ---------------- API ---------------- */

  function api(path, opts) {
    opts = opts || {};
    var init = { method: opts.method || 'GET', credentials: 'same-origin', headers: { 'X-WP-Nonce': C.nonce } };
    if (opts.form) {
      init.body = opts.form;
    } else if (opts.body) {
      init.headers['Content-Type'] = 'application/json';
      init.body = JSON.stringify(opts.body);
    }
    return fetch(C.api + path, init).then(function (res) {
      return res.json().catch(function () { return {}; }).then(function (data) {
        if (res.status === 403 && data && data.code === 'rest_cookie_invalid_nonce') {
          location.reload();
        }
        if (!res.ok) {
          var err = new Error((data && data.message) || 'Network error, try again.');
          throw err;
        }
        return data;
      });
    });
  }

  function toast(msg, isErr) {
    var t = $('r-toast');
    t.textContent = msg;
    t.className = 'r-toast show' + (isErr ? ' err' : '');
    clearTimeout(toast._t);
    toast._t = setTimeout(function () { t.className = 'r-toast'; }, 3200);
  }

  /* ---------------- Alerts for new orders ---------------- */

  function beep() {
    try {
      var ctx = new (window.AudioContext || window.webkitAudioContext)();
      [0, 0.25, 0.5].forEach(function (when) {
        var o = ctx.createOscillator();
        var g = ctx.createGain();
        o.frequency.value = 880;
        o.connect(g); g.connect(ctx.destination);
        g.gain.setValueAtTime(0.25, ctx.currentTime + when);
        g.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + when + 0.2);
        o.start(ctx.currentTime + when);
        o.stop(ctx.currentTime + when + 0.22);
      });
    } catch (e) {}
    if (navigator.vibrate) navigator.vibrate([300, 150, 300, 150, 300]);
  }

  /* ---------------- Rendering ---------------- */

  function render(data) {
    state.online = !!data.online;
    state.active = data.active || [];
    state.done = data.done || [];

    $('r-name').textContent = data.name || 'rider';
    $('r-active-count').textContent = state.active.length;
    $('r-done-count').textContent = state.done.filter(function (d) { return d.status === 'delivered'; }).length;
    $('r-cash').textContent = money(data.cash);

    var duty = $('r-duty');
    duty.setAttribute('aria-pressed', state.online ? 'true' : 'false');
    duty.querySelector('.r-duty__txt').textContent = state.online ? 'On duty' : 'Off duty';

    var banner = $('r-banner');
    if (!state.online) {
      banner.hidden = false;
      banner.textContent = 'You are off duty. Tap "Off duty" to start receiving orders.';
    } else {
      banner.hidden = true;
    }

    var fresh = false;
    var html = state.active.map(function (d) {
      var isNew = !state.first && !state.known[d.id];
      if (isNew) fresh = true;
      state.known[d.id] = true;
      return card(d, isNew);
    }).join('');

    $('r-active').innerHTML = html || (
      '<div class="r-empty"><b>' + (state.online ? 'Waiting for orders…' : 'No active deliveries') + '</b>' +
      (state.online ? 'Keep this app open — new orders ring and vibrate.' : 'Go on duty to get orders.') + '</div>'
    );

    $('r-done').innerHTML = state.done.length ? state.done.map(function (d) {
      return '<div class="r-done-item"><span>#' + esc(d.order) + ' · ' + esc(d.customer) + '</span><span class="r-pill r-pill--' + esc(d.status) + '">' +
        esc(d.status === 'delivered' && d.is_cod ? money(d.cod_collected) : d.label) + '</span></div>';
    }).join('') : '<div class="r-done-item">Nothing yet today.</div>';

    if (fresh) {
      beep();
      toast('New delivery assigned!');
    }
    state.first = false;
  }

  function card(d, isNew) {
    var assigned = d.status === 'assigned';
    var pay = d.is_cod
      ? '<div class="r-cod r-cod--cash"><span>COLLECT CASH</span><b>' + money(d.cod_amount) + '</b></div>'
      : '<div class="r-cod r-cod--paid"><span>PREPAID</span><b>Do not collect</b></div>';

    var contact = '<div class="r-row">' +
      '<a class="r-btn" href="tel:' + esc(d.phone) + '">' + ICON.call + 'Call</a>' +
      (d.wa ? '<a class="r-btn r-btn--wa" target="_blank" rel="noopener" href="https://wa.me/' + esc(d.wa) + '?text=' +
        encodeURIComponent('Hi, I am your One Love delivery rider for order #' + d.order + '. I am on the way!') + '">' + ICON.wa + 'Chat</a>' : '<span></span>') +
      '<a class="r-btn" target="_blank" rel="noopener" href="' + esc(d.store_nav) + '">' + ICON.store + 'Store</a>' +
      '</div>';

    var primary = assigned
      ? '<button type="button" class="r-btn r-btn--gold r-btn--big r-btn--block" data-act="start" data-id="' + d.id + '">Picked up · Start trip</button>'
      : '<button type="button" class="r-btn r-btn--green r-btn--big r-btn--block" data-act="deliver" data-id="' + d.id + '">Mark delivered</button>';

    return '<article class="r-card' + (isNew ? ' is-new' : '') + '">' +
      '<div class="r-card__head"><span class="r-card__order">#' + esc(d.order) + '</span><span class="r-pill r-pill--' + esc(d.status) + '">' + esc(d.label) + '</span></div>' +
      '<div class="r-card__body">' +
        '<div class="r-cust">' + esc(d.customer) + '</div>' +
        '<div class="r-addr">' + esc(d.address) + (d.pinned ? '<small>Exact door location pinned by customer</small>' : '') + '</div>' +
        (d.items && d.items.length ? '<div class="r-items">' + d.items.map(esc).join(' · ') + '</div>' : '') +
        (d.note ? '<div class="r-note">' + esc(d.note) + '</div>' : '') +
        pay +
        '<a class="r-btn r-btn--nav r-btn--block" target="_blank" rel="noopener" href="' + esc(d.nav_url) + '">' + ICON.nav + 'Navigate with Google Maps</a>' +
        contact +
        primary +
      '</div>' +
      '<div class="r-card__more">' +
        (assigned ? '<button type="button" class="r-link" data-act="reject" data-id="' + d.id + '">Can\'t take this order</button>' : '<span></span>') +
        '<button type="button" class="r-link" data-act="fail" data-id="' + d.id + '">Report a problem</button>' +
      '</div>' +
    '</article>';
  }

  function find(id) {
    id = Number(id);
    return state.active.filter(function (d) { return d.id === id; })[0];
  }

  /* ---------------- Load / poll ---------------- */

  function load() {
    return api('/rider/me').then(render).catch(function (e) { toast(e.message, true); });
  }

  function schedulePoll() {
    clearInterval(pollTimer);
    pollTimer = setInterval(function () {
      if (!document.hidden) load();
    }, state.online ? 15000 : 60000);
  }

  /* ---------------- GPS ---------------- */

  function gpsStatus(text, cls) {
    var el = $('r-gps');
    el.textContent = text;
    el.className = 'r-sub' + (cls ? ' ' + cls : '');
  }

  function sendLocation(pos, force) {
    var c = pos.coords;
    var now = Date.now();
    var moved = Math.abs(c.latitude - lastSent.lat) + Math.abs(c.longitude - lastSent.lng) > 0.0003; // ~30 m
    if (!force && now - lastSent.t < 20000 && !moved) return;
    if (!force && now - lastSent.t < 8000) return;
    lastSent = { t: now, lat: c.latitude, lng: c.longitude };
    api('/rider/location', { method: 'POST', body: { lat: c.latitude, lng: c.longitude, accuracy: c.accuracy } })
      .then(function () { gpsStatus('GPS live · ±' + Math.round(c.accuracy) + ' m', 'is-ok'); })
      .catch(function () { gpsStatus('GPS: upload failed, retrying', 'is-bad'); });
  }

  function startGps() {
    if (!navigator.geolocation) {
      gpsStatus('This phone has no GPS access', 'is-bad');
      return;
    }
    if (watchId !== null) return;
    gpsStatus('Finding your location…');
    watchId = navigator.geolocation.watchPosition(
      function (pos) { sendLocation(pos, false); },
      function (err) {
        gpsStatus(err.code === 1 ? 'Location blocked — allow it in browser settings' : 'GPS signal weak…', 'is-bad');
      },
      { enableHighAccuracy: true, maximumAge: 10000, timeout: 30000 }
    );
    // Heartbeat even when standing still, so dispatch knows you are online.
    clearInterval(startGps._hb);
    startGps._hb = setInterval(function () {
      if (lastSent.t && Date.now() - lastSent.t > 60000) {
        navigator.geolocation.getCurrentPosition(function (p) { sendLocation(p, true); }, function () {}, { maximumAge: 30000, timeout: 20000 });
      }
    }, 30000);
    keepAwake();
  }

  function stopGps() {
    if (watchId !== null) navigator.geolocation.clearWatch(watchId);
    watchId = null;
    clearInterval(startGps._hb);
    gpsStatus('GPS off');
    if (wakeLock) { wakeLock.release().catch(function () {}); wakeLock = null; }
  }

  function keepAwake() {
    if (!('wakeLock' in navigator) || wakeLock) return;
    navigator.wakeLock.request('screen').then(function (l) {
      wakeLock = l;
      l.addEventListener('release', function () { wakeLock = null; });
    }).catch(function () {});
  }

  /* ---------------- Actions ---------------- */

  function setDuty(online) {
    var btn = $('r-duty');
    btn.disabled = true;
    api('/rider/duty', { method: 'POST', body: { online: online } })
      .then(function (data) {
        render(data);
        if (online) { startGps(); toast('You are on duty. Orders will ring here.'); } else { stopGps(); toast('You are off duty.'); }
        schedulePoll();
      })
      .catch(function (e) { toast(e.message, true); })
      .finally(function () { btn.disabled = false; });
  }

  function openSheet(title, bodyHtml, submitText, onSubmit, danger) {
    $('r-sheet-title').textContent = title;
    $('r-sheet-body').innerHTML = bodyHtml;
    var submit = $('r-sheet-submit');
    submit.textContent = submitText;
    submit.className = 'r-btn ' + (danger ? 'r-btn--danger' : 'r-btn--green');
    sheetAction = onSubmit;
    $('r-sheet').hidden = false;
    var first = $('r-sheet-body').querySelector('input');
    if (first) setTimeout(function () { first.focus(); }, 250);
  }

  function closeSheet() {
    $('r-sheet').hidden = true;
    sheetAction = null;
  }

  function deliverSheet(d) {
    var html = '';
    if (d.otp_needed) {
      html += '<div class="r-field"><label for="r-otp">Delivery OTP</label>' +
        '<input id="r-otp" class="r-otp" type="text" inputmode="numeric" pattern="[0-9]*" maxlength="4" autocomplete="one-time-code" required>' +
        '<small>Ask the customer for the 4-digit code they got on WhatsApp / tracking page.</small></div>';
    }
    if (d.is_cod) {
      html += '<div class="r-field"><label for="r-cash-in">Cash collected (₹)</label>' +
        '<input id="r-cash-in" type="number" inputmode="decimal" step="0.01" min="0" value="' + Number(d.cod_amount).toFixed(2) + '" required>' +
        '<small>Order total: <b>' + money(d.cod_amount) + '</b></small></div>';
    }
    html += '<div class="r-field"><label for="r-proof">Photo proof (optional)</label>' +
      '<input id="r-proof" type="file" accept="image/*" capture="environment"></div>';

    openSheet('Deliver #' + d.order, html, 'Confirm delivered', function () {
      var form = new FormData();
      var otp = $('r-otp');
      var cash = $('r-cash-in');
      var proof = $('r-proof');
      if (otp) form.append('otp', otp.value.trim());
      if (cash) form.append('cash', cash.value);
      if (proof && proof.files[0]) form.append('proof', proof.files[0]);
      return api('/rider/deliveries/' + d.id + '/deliver', { method: 'POST', form: form }).then(function () {
        toast('Delivered! Great job 🎉');
      });
    });
  }

  function failSheet(d) {
    var html = '<div class="r-reasons">' + (C.reasons || []).map(function (r, i) {
      return '<label><input type="radio" name="r-reason" value="' + esc(r) + '"' + (i === 0 ? ' checked' : '') + '>' + esc(r) + '</label>';
    }).join('') + '</div>' +
      '<p class="r-muted" style="font-size:14px">The store will see this and re-assign or call the customer. Bring the order back to the store.</p>';
    openSheet('Problem with #' + d.order, html, 'Report problem', function () {
      var picked = document.querySelector('input[name="r-reason"]:checked');
      return api('/rider/deliveries/' + d.id + '/fail', { method: 'POST', body: { reason: picked ? picked.value : 'Other' } }).then(function () {
        toast('Reported to the store.');
      });
    }, true);
  }

  document.addEventListener('click', function (e) {
    if (e.target.closest('#r-duty')) {
      setDuty(!state.online);
      return;
    }
    if (e.target.closest('[data-close-sheet]') || e.target === $('r-sheet')) {
      closeSheet();
      return;
    }
    var btn = e.target.closest('[data-act]');
    if (!btn) return;
    var d = find(btn.getAttribute('data-id'));
    if (!d) return;
    var act = btn.getAttribute('data-act');

    if (act === 'start') {
      btn.disabled = true;
      api('/rider/deliveries/' + d.id + '/start', { method: 'POST' })
        .then(function () { toast('Trip started — customer notified. Tap Navigate.'); return load(); })
        .catch(function (err) { toast(err.message, true); btn.disabled = false; });
    } else if (act === 'deliver') {
      deliverSheet(d);
    } else if (act === 'fail') {
      failSheet(d);
    } else if (act === 'reject') {
      if (!window.confirm('Decline order #' + d.order + '? It will go to another rider.')) return;
      api('/rider/deliveries/' + d.id + '/reject', { method: 'POST' })
        .then(function () { toast('Order passed to another rider.'); return load(); })
        .catch(function (err) { toast(err.message, true); });
    }
  });

  $('r-sheet-form').addEventListener('submit', function (e) {
    e.preventDefault();
    if (!sheetAction) return;
    var submit = $('r-sheet-submit');
    submit.disabled = true;
    sheetAction()
      .then(function () { closeSheet(); return load(); })
      .catch(function (err) { toast(err.message, true); })
      .finally(function () { submit.disabled = false; });
  });

  document.addEventListener('visibilitychange', function () {
    if (!document.hidden) {
      load();
      if (state.online) keepAwake();
    }
  });

  /* ---------------- Boot ---------------- */

  if ('serviceWorker' in navigator && C.sw) {
    navigator.serviceWorker.register(C.sw).catch(function () {});
  }

  load().then(function () {
    if (state.online) startGps();
    schedulePoll();
  });
})();
