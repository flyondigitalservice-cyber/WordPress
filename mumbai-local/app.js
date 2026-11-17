/* Lokal: Mumbai local train app. Depends on data.js. */
(function () {
  'use strict';

  /* ------------------------------------------------------------------ *
   * Storage (per-device conveniences only)
   * ------------------------------------------------------------------ */
  const store = {
    get(key, fallback) {
      try {
        const v = localStorage.getItem('lokal:' + key);
        return v == null ? fallback : JSON.parse(v);
      } catch (e) { return fallback; }
    },
    set(key, value) {
      try { localStorage.setItem('lokal:' + key, JSON.stringify(value)); } catch (e) { /* ignore */ }
    },
  };

  /* ------------------------------------------------------------------ *
   * Network model
   * ------------------------------------------------------------------ */
  const STATIONS = new Map(); // name -> { name, lines:Set, corridors:Set }
  for (const [cid, c] of Object.entries(CORRIDORS)) {
    c.id = cid;
    c.idx = new Map(c.stations.map((s, i) => [s[0], i]));
    for (const [name] of c.stations) {
      if (!STATIONS.has(name)) STATIONS.set(name, { name, lines: new Set(), corridors: new Set() });
      const st = STATIONS.get(name);
      st.lines.add(c.line);
      st.corridors.add(cid);
    }
  }
  const STATION_NAMES = [...STATIONS.keys()].sort((a, b) => a.localeCompare(b));
  const LINE_ORDER = ['WR', 'CR', 'HR', 'TH'];

  const DAY_START = 4 * 60;     // first departures 04:00
  const LAST_DEP = 24 * 60 + 45; // last departures 00:45
  const TRANSFER_MIN = 3;

  function isPeak(t) {
    const h = Math.floor(t / 60) % 24;
    return (h >= 7 && h < 11) || (h >= 17 && h < 21);
  }
  function headway(hw, t) {
    const h = Math.floor(t / 60) % 24;
    if (isPeak(t)) return hw[1];
    if (h >= 23 || h < 5) return Math.round(hw[0] * 1.6);
    return hw[0];
  }
  function shortCode(name) {
    const a = ALIASES[name];
    if (a && /^[A-Z]{2,5}$/.test(a.split(' ')[0])) return a.split(' ')[0];
    return name.replace(/[^A-Za-z ]/g, '').split(' ').map(w => w[0]).join('').toUpperCase().slice(0, 3);
  }

  // Generate the day's trips from service patterns.
  const TRIPS = [];
  (function generateTrips() {
    const numBase = { WR: 90000, CR: 95000, HR: 98000, TH: 97000 };
    const count = { WR: 0, CR: 0, HR: 0, TH: 0 };
    PATTERNS.forEach((p, pi) => {
      const c = CORRIDORS[p.c];
      const i0 = c.idx.get(p.from), i1 = c.idx.get(p.to);
      let names = c.stations.slice(i0, i1 + 1).map(s => s[0]);
      if (p.fast) {
        const after = c.idx.get(c.fastAllAfter);
        names = names.filter((n, k) => k === 0 || k === names.length - 1 ||
          c.fast.includes(n) || c.idx.get(n) >= after);
      }
      for (const dir of ['down', 'up']) {
        const seq = dir === 'down' ? names : names.slice().reverse();
        const rel = [0];
        for (let k = 1; k < seq.length; k++) {
          const d = Math.abs(c.stations[c.idx.get(seq[k])][1] - c.stations[c.idx.get(seq[k - 1])][1]);
          rel.push(rel[k - 1] + d * (d > 5 ? 1.05 : 1.25) + 1.2);
        }
        let t = DAY_START + ((pi * 7 + (dir === 'up' ? 4 : 0)) % p.hw[1]);
        let n = 0;
        while (t <= LAST_DEP) {
          const line = c.line;
          count[line]++;
          const trip = {
            id: TRIPS.length,
            num: String(numBase[line] + count[line] * 2 + (dir === 'down' ? 1 : 0)),
            line,
            corridor: c.id,
            dir,
            fast: !!p.fast,
            ac: !!(p.ac && n % p.ac === p.ac - 1),
            cars: p.cars15 && n % p.cars15 === 1 ? 15 : 12,
            origin: seq[0],
            dest: seq[seq.length - 1],
            stops: seq.map((s, k) => ({ s, t: Math.round(t + rel[k]) })),
          };
          trip.pos = new Map(trip.stops.map((x, k) => [x.s, k]));
          TRIPS.push(trip);
          t += headway(p.hw, t);
          n++;
        }
      }
    });
  })();

  // Departures per station, sorted by time.
  const BY_STATION = new Map();
  for (const trip of TRIPS) {
    trip.stops.forEach((st, k) => {
      if (!BY_STATION.has(st.s)) BY_STATION.set(st.s, []);
      BY_STATION.get(st.s).push({ trip, k, t: st.t });
    });
  }
  for (const list of BY_STATION.values()) list.sort((a, b) => a.t - b.t);

  // Elementary connections (for journeys that need a change of train).
  const CONNS = [];
  for (const trip of TRIPS) {
    for (let k = 0; k < trip.stops.length - 1; k++) {
      CONNS.push({ trip, k, from: trip.stops[k].s, to: trip.stops[k + 1].s, dep: trip.stops[k].t, arr: trip.stops[k + 1].t });
    }
  }
  CONNS.sort((a, b) => a.dep - b.dep || a.arr - b.arr);

  function lowerBound(list, t, key) {
    let lo = 0, hi = list.length;
    while (lo < hi) { const m = (lo + hi) >> 1; if (list[m][key] < t) lo = m + 1; else hi = m; }
    return lo;
  }

  /* ------------------------------------------------------------------ *
   * Queries
   * ------------------------------------------------------------------ */
  function serviceNow() {
    const d = new Date();
    let m = d.getHours() * 60 + d.getMinutes() + d.getSeconds() / 60;
    if (m < DAY_START) m += 1440;
    return m;
  }

  function directTrips(from, to, t, limit) {
    const list = BY_STATION.get(from) || [];
    const out = [];
    for (let i = lowerBound(list, t, 't'); i < list.length && out.length < limit; i++) {
      const { trip, k } = list[i];
      const j = trip.pos.get(to);
      if (j != null && j > k) out.push({ trip, i: k, j, dep: trip.stops[k].t, arr: trip.stops[j].t });
    }
    return out;
  }

  const directCache = new Map();
  function hasDirect(from, to) {
    const key = from + '|' + to;
    if (!directCache.has(key)) directCache.set(key, directTrips(from, to, 0, 1).length > 0);
    return directCache.get(key);
  }

  // Connection Scan: earliest arrival from `from` to `to` departing at or after t.
  function earliestJourney(from, to, t) {
    const ready = new Map([[from, t]]);
    const arrival = new Map([[from, t]]);
    const via = new Map();
    const boarded = new Map(); // trip id -> boarding connection
    let best = Infinity;
    for (let i = lowerBound(CONNS, t, 'dep'); i < CONNS.length; i++) {
      const c = CONNS[i];
      if (c.dep >= best) break;
      const tid = c.trip.id;
      let board = boarded.get(tid);
      if (!board) {
        const r = ready.get(c.from);
        if (r == null || r > c.dep) continue;
        board = c;
        boarded.set(tid, c);
      }
      const a = arrival.get(c.to);
      if (a == null || c.arr < a) {
        arrival.set(c.to, c.arr);
        ready.set(c.to, c.arr + (c.to === to ? 0 : TRANSFER_MIN));
        via.set(c.to, { trip: c.trip, board, arr: c.arr, k: c.k + 1 });
        if (c.to === to) best = c.arr;
      }
    }
    if (best === Infinity) return null;
    const legs = [];
    let s = to;
    let guard = 0;
    while (s !== from && guard++ < 10) {
      const v = via.get(s);
      if (!v) return null;
      legs.unshift({ trip: v.trip, i: v.board.k, j: v.k, dep: v.board.dep, arr: v.arr });
      s = v.board.from;
    }
    return { legs, dep: legs[0].dep, arr: legs[legs.length - 1].arr };
  }

  function journeys(from, to, t) {
    if (from === to) return { kind: 'same', items: [] };
    if (hasDirect(from, to)) {
      return { kind: 'direct', items: directTrips(from, to, t, 40).map(d => ({ legs: [d], dep: d.dep, arr: d.arr })) };
    }
    const items = [];
    let at = t;
    for (let run = 0; run < 18 && items.length < 8; run++) {
      const j = earliestJourney(from, to, at);
      if (!j) break;
      const last = items[items.length - 1];
      if (last && last.arr === j.arr) items[items.length - 1] = j; // same arrival, later start: keep the later one
      else items.push(j);
      at = j.dep + 1;
    }
    return { kind: 'change', items };
  }

  function departures(station, t, limit) {
    const list = BY_STATION.get(station) || [];
    const out = [];
    for (let i = lowerBound(list, t, 't'); i < list.length && out.length < limit; i++) {
      const { trip, k } = list[i];
      if (k < trip.stops.length - 1) out.push({ trip, k, dep: trip.stops[k].t });
    }
    return out;
  }

  // Estimated position of a train from its timetable.
  function tripState(trip, now) {
    const st = trip.stops;
    if (now < st[0].t) return { phase: 'before', k: 0, p: 0 };
    if (now >= st[st.length - 1].t) return { phase: 'done', k: st.length - 1, p: 0 };
    let k = 0;
    while (k < st.length - 1 && st[k + 1].t <= now) k++;
    const span = st[k + 1].t - st[k].t;
    const p = Math.min(1, Math.max(0, (now - st[k].t) / span));
    return { phase: now - st[k].t < 0.6 ? 'at' : 'running', k, p };
  }

  /* ------------------------------------------------------------------ *
   * Formatting
   * ------------------------------------------------------------------ */
  let use24 = store.get('use24', false);
  function fmt(t, withAp) {
    const m = Math.round(t) % 1440;
    let h = Math.floor(m / 60);
    const mm = String(m % 60).padStart(2, '0');
    if (use24) return String(h).padStart(2, '0') + ':' + mm;
    const ap = h < 12 ? 'am' : 'pm';
    h = h % 12 || 12;
    return withAp === false ? h + ':' + mm : h + ':' + mm + (withAp === 'html' ? '<span class="ap">' + ap + '</span>' : ' ' + ap);
  }
  function inMin(t, now) {
    const d = Math.round(t - now);
    if (d <= 0) return 'now';
    if (d < 60) return d + ' min';
    return Math.floor(d / 60) + ' h ' + (d % 60) + ' m';
  }
  const esc = s => String(s).replace(/[&<>"']/g, ch => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[ch]));
  const pill = line => `<span class="pill ${line}">${LINES[line].short}</span>`;
  const lineVar = line => `var(--${line.toLowerCase()})`;
  function trainName(trip) {
    return `${trip.dest} ${trip.fast ? 'Fast' : 'Slow'}`;
  }
  function indicator(trip, t) {
    return `<div class="ind" aria-hidden="true">
      <span class="ind-time">${fmt(t, 'html')}</span>
      <span class="ind-type">${trip.fast ? 'F' : 'S'}</span>
      <span class="ind-sub"><b>${esc(shortCode(trip.dest))}</b><span>${trip.cars} car</span></span>
    </div>`;
  }

  const ICONS = {
    train: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="3" width="14" height="14" rx="3"/><path d="M5 11h14M9 21l1.5-4M15 21l-1.5-4"/><circle cx="9" cy="14" r=".6" fill="currentColor"/><circle cx="15" cy="14" r=".6" fill="currentColor"/></svg>',
    station: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18M5 21V9l7-5 7 5v12"/><path d="M9 21v-6h6v6"/></svg>',
    lines: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="6" cy="5" r="2"/><circle cx="6" cy="19" r="2"/><circle cx="18" cy="12" r="2"/><path d="M6 7v10M8 6l8 5"/></svg>',
    more: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 7h16M4 12h16M4 17h16"/></svg>',
    swap: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M7 4v16M7 20l-3-3M7 20l3-3M17 20V4M17 4l-3 3M17 4l3 3"/></svg>',
    back: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 5l-7 7 7 7"/></svg>',
    star: '<svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M12 3l2.7 5.6 6.1.9-4.4 4.3 1 6.1L12 17l-5.4 2.9 1-6.1L3.2 9.5l6.1-.9z"/></svg>',
    tram: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M12 5v14M6 13l6 6 6-6"/></svg>',
  };

  /* ------------------------------------------------------------------ *
   * State
   * ------------------------------------------------------------------ */
  const state = {
    tab: 'trains',
    from: store.get('from', 'Andheri'),
    to: store.get('to', 'Churchgate'),
    when: null,            // null = now, else service minutes
    station: store.get('station', 'Dadar'),
    detail: null,          // { tripId, from, to } when viewing a train
    saved: store.get('saved', [['Borivali', 'Dadar'], ['Thane', 'CSMT'], ['Vashi', 'Kurla']]),
    recentStations: store.get('recentStations', []),
    openCorridor: null,
  };
  if (!STATIONS.has(state.from)) state.from = 'Andheri';
  if (!STATIONS.has(state.to)) state.to = 'Churchgate';
  if (!STATIONS.has(state.station)) state.station = 'Dadar';

  const view = document.getElementById('view');
  const scrollMemo = {};

  function setTab(tab) {
    scrollMemo[state.tab + (state.detail ? ':d' : '')] = view.scrollTop;
    state.tab = tab;
    state.detail = null;
    render();
    view.scrollTop = scrollMemo[tab] || 0;
  }

  function openTrain(tripId, from, to) {
    scrollMemo[state.tab] = view.scrollTop;
    state.detail = { tripId, from, to };
    render();
    view.scrollTop = 0;
    // Scroll the live position into view.
    const here = view.querySelector('.stop.here, .stop.next');
    if (here) here.scrollIntoView({ block: 'center' });
  }
  function closeTrain() {
    state.detail = null;
    render();
    view.scrollTop = scrollMemo[state.tab] || 0;
  }

  function rememberStation(name) {
    state.recentStations = [name, ...state.recentStations.filter(n => n !== name)].slice(0, 6);
    store.set('recentStations', state.recentStations);
  }

  /* ------------------------------------------------------------------ *
   * Views
   * ------------------------------------------------------------------ */
  function render() {
    document.querySelectorAll('.tab').forEach(b => {
      if (b.dataset.tab === state.tab) b.setAttribute('aria-current', 'page');
      else b.removeAttribute('aria-current');
    });
    if (state.detail) return renderTrain();
    ({ trains: renderTrains, station: renderStation, lines: renderLines, more: renderMore })[state.tab]();
  }

  function isSaved() {
    return state.saved.some(([a, b]) => a === state.from && b === state.to);
  }

  function renderTrains() {
    const now = serviceNow();
    const t = state.when ?? now;
    const res = journeys(state.from, state.to, t - (state.when == null ? 0.5 : 0));
    let body = '';
    if (res.kind === 'same') {
      body = `<div class="note">Pick two different stations to see trains.</div>`;
    } else if (!res.items.length) {
      body = `<div class="note"><strong>No more trains after ${fmt(t)}.</strong><br>Services start again around 4 am. Change the time to see tomorrow's first trains.</div>`;
    } else {
      body = `<div class="list">${res.items.map(j => journeyRow(j, now)).join('')}</div>`;
    }
    const kindNote = res.kind === 'change' && res.items.length
      ? `<p class="disclaimer">No direct train runs between these stations. These options include a change of train, with at least ${TRANSFER_MIN} minutes to switch platforms.</p>` : '';

    view.innerHTML = `
      <section class="route" aria-label="Journey">
        <div class="route-fields">
          <button class="field" id="pick-from" type="button">
            <span class="field-label">From</span>
            <span class="field-value">${esc(state.from)}</span>
          </button>
          <button class="field" id="pick-to" type="button">
            <span class="field-label">To</span>
            <span class="field-value">${esc(state.to)}</span>
          </button>
        </div>
        <button class="swap" id="swap" type="button" aria-label="Swap stations">${ICONS.swap}</button>
      </section>
      <div class="toolbar">
        <button class="chip" id="when-now" type="button" aria-pressed="${state.when == null}">Now</button>
        <label class="chip" for="when-time">Leave at
          <input type="time" id="when-time" value="${state.when == null ? '' : fmt24(state.when)}">
        </label>
        <button class="chip star" id="save-route" type="button" aria-pressed="${isSaved()}">${ICONS.star}${isSaved() ? 'Saved' : 'Save route'}</button>
      </div>
      ${state.saved.length ? `<div class="saved-row" aria-label="Saved routes">${state.saved.map(([a, b], i) =>
        `<button class="chip" type="button" data-saved="${i}">${esc(a)} → ${esc(b)}</button>`).join('')}</div>` : ''}
      <h2 class="section">${res.kind === 'change' ? 'Best journeys' : 'Next trains'} · ${state.when == null ? 'from now' : 'from ' + fmt(t)}</h2>
      ${kindNote}
      ${body}
      <p class="disclaimer">Times come from a sample timetable built into this prototype, and live positions are estimated from it. Check station displays before you travel.</p>
    `;

    view.querySelector('#pick-from').onclick = () => openPicker('From', name => { state.from = name; store.set('from', name); render(); });
    view.querySelector('#pick-to').onclick = () => openPicker('To', name => { state.to = name; store.set('to', name); render(); });
    view.querySelector('#swap').onclick = () => {
      [state.from, state.to] = [state.to, state.from];
      store.set('from', state.from); store.set('to', state.to);
      render();
    };
    view.querySelector('#when-now').onclick = () => { state.when = null; render(); };
    view.querySelector('#when-time').onchange = e => {
      const v = e.target.value;
      if (!v) { state.when = null; render(); return; }
      const [h, m] = v.split(':').map(Number);
      let mins = h * 60 + m;
      if (mins < DAY_START) mins += 1440;
      state.when = mins;
      render();
    };
    view.querySelector('#save-route').onclick = () => {
      if (isSaved()) state.saved = state.saved.filter(([a, b]) => !(a === state.from && b === state.to));
      else state.saved = [[state.from, state.to], ...state.saved].slice(0, 8);
      store.set('saved', state.saved);
      render();
    };
    view.querySelectorAll('[data-saved]').forEach(b => b.onclick = () => {
      const [a, c] = state.saved[+b.dataset.saved];
      state.from = a; state.to = c;
      store.set('from', a); store.set('to', c);
      render();
    });
    bindTrainRows();
  }

  function fmt24(t) {
    const m = Math.round(t) % 1440;
    return String(Math.floor(m / 60)).padStart(2, '0') + ':' + String(m % 60).padStart(2, '0');
  }

  function journeyRow(j, now) {
    const first = j.legs[0];
    const trip = first.trip;
    const dur = j.arr - j.dep;
    const soon = j.dep - now < 10 && j.dep >= now - 1;
    const when = `<span class="row-when${soon ? ' soon' : ''}">${inMin(j.dep, now)}<small>${dur} min ride</small></span>`;
    if (j.legs.length === 1) {
      return `<button class="row" type="button" data-trip="${trip.id}" data-from="${esc(state.from)}" data-to="${esc(state.to)}">
        ${indicator(trip, j.dep)}
        <span class="row-main">
          <span class="row-title">${esc(trainName(trip))}</span>
          <span class="row-meta">${pill(trip.line)} ${trip.ac ? '<span class="pill ac">AC</span>' : ''} Arrives ${fmt(j.arr)} · ${first.j - first.i} stops</span>
        </span>
        ${when}
      </button>`;
    }
    const legs = j.legs.map((l, n) => {
      const a = l.trip.stops[l.i].s, b = l.trip.stops[l.j].s;
      const wait = n > 0 ? l.dep - j.legs[n - 1].arr : 0;
      return `${n > 0 ? `<span class="leg-change">Change at ${esc(a)} · ${wait} min wait</span>` : ''}
        <span class="row-meta">${pill(l.trip.line)} ${fmt(l.dep)} ${esc(a)} → ${esc(b)} ${fmt(l.arr)} · ${esc(trainName(l.trip))}</span>`;
    }).join('');
    return `<button class="row journey" type="button" data-trip="${trip.id}" data-from="${esc(state.from)}" data-to="${esc(trip.stops[first.j].s)}">
      ${indicator(trip, j.dep)}
      <span class="row-main">
        <span class="row-title">Arrive ${fmt(j.arr)} · ${j.legs.length - 1} change${j.legs.length > 2 ? 's' : ''}</span>
        <span class="legs">${legs}</span>
      </span>
      ${when}
    </button>`;
  }

  function bindTrainRows() {
    view.querySelectorAll('[data-trip]').forEach(b => b.onclick = () =>
      openTrain(+b.dataset.trip, b.dataset.from || null, b.dataset.to || null));
  }

  function renderStation() {
    const now = serviceNow();
    const deps = departures(state.station, now - 0.5, 40);
    const st = STATIONS.get(state.station);
    const lines = LINE_ORDER.filter(l => st.lines.has(l));
    view.innerHTML = `
      <section class="route" aria-label="Station">
        <div class="route-fields">
          <button class="field" id="pick-station" type="button">
            <span class="field-label">Departures from</span>
            <span class="field-value">${esc(state.station)}</span>
          </button>
        </div>
        <span style="margin-right:14px; display:flex; gap:4px">${lines.map(pill).join('')}</span>
      </section>
      <h2 class="section">Next departures</h2>
      ${deps.length ? `<div class="list">${deps.map(d => {
        const soon = d.dep - now < 10;
        return `<button class="row" type="button" data-trip="${d.trip.id}" data-from="${esc(state.station)}">
          ${indicator(d.trip, d.dep)}
          <span class="row-main">
            <span class="row-title">${esc(trainName(d.trip))}</span>
            <span class="row-meta">${pill(d.trip.line)} ${d.trip.ac ? '<span class="pill ac">AC</span>' : ''} Towards ${esc(d.trip.dest)} · reaches ${fmt(d.trip.stops[d.trip.stops.length - 1].t)}</span>
          </span>
          <span class="row-when${soon ? ' soon' : ''}">${inMin(d.dep, now)}</span>
        </button>`;
      }).join('')}</div>` : `<div class="note"><strong>No more departures tonight.</strong><br>The first trains leave around 4 am.</div>`}
    `;
    view.querySelector('#pick-station').onclick = () => openPicker('Station', name => {
      state.station = name; store.set('station', name); render();
    });
    bindTrainRows();
  }

  function renderTrain() {
    const trip = TRIPS[state.detail.tripId];
    const now = serviceNow();
    const s = tripState(trip, now);
    const lc = lineVar(trip.line);
    const fromK = state.detail.from ? trip.pos.get(state.detail.from) : null;
    const toK = state.detail.to ? trip.pos.get(state.detail.to) : null;
    const stops = trip.stops;

    let headline, sub;
    if (s.phase === 'before') {
      headline = `Starts from ${trip.origin} in ${inMin(stops[0].t, now)}`;
      sub = `Scheduled departure ${fmt(stops[0].t)}`;
    } else if (s.phase === 'done') {
      headline = `Reached ${trip.dest}`;
      sub = `Arrived ${fmt(stops[stops.length - 1].t)}`;
    } else if (s.phase === 'at') {
      headline = `At ${stops[s.k].s}`;
      sub = `Next stop ${stops[s.k + 1].s} at ${fmt(stops[s.k + 1].t)}`;
    } else {
      headline = `Between ${stops[s.k].s} and ${stops[s.k + 1].s}`;
      sub = `Reaching ${stops[s.k + 1].s} in about ${inMin(stops[s.k + 1].t, now)}`;
    }
    if (fromK != null && s.phase !== 'done' && (s.phase === 'before' ? fromK > 0 : s.k + 1 < fromK)) {
      sub += ` · at ${stops[fromK].s} in ${inMin(stops[fromK].t, now)}`;
    }
    const liveOn = s.phase === 'running' || s.phase === 'at';

    const rows = stops.map((x, k) => {
      let cls = 'stop';
      let p = 0;
      if (s.phase === 'done' || k < s.k || (k === s.k && s.phase !== 'before')) cls += ' past';
      if (k === s.k && s.phase !== 'before' && s.phase !== 'done') { cls += ' here'; p = s.p; }
      if (k < s.k || s.phase === 'done') p = 1;
      if (s.phase === 'before' && k === 0) cls += ' next';
      let tag = '';
      if (k === fromK) tag = 'Board';
      if (k === toK) tag = 'Get off';
      if (tag) cls += ' mine';
      const marker = (k === s.k && liveOn) ? `<span class="train-marker" style="--p:${s.p.toFixed(3)}" aria-label="Train is here">${ICONS.tram}</span>` : '';
      return `<li class="${cls}" ${tag ? `data-tag="${tag}"` : ''}>
        <span class="stop-time">${fmt(x.t)}</span>
        <span class="stop-track"><span class="fill" style="--p:${p.toFixed(3)}"></span><span class="dot"></span>${marker}</span>
        <span class="stop-name" ${tag ? `data-tag="${tag}"` : ''}>${esc(x.s)}</span>
      </li>`;
    }).join('');

    view.innerHTML = `
      <button class="back" id="back" type="button">${ICONS.back}Back</button>
      <section class="hero" aria-label="Train">
        <div class="hero-top">
          <span class="hero-time">${fmt(fromK != null ? stops[fromK].t : stops[0].t)}</span>
          <span class="hero-dest">${esc(trip.dest)}</span>
        </div>
        <div class="hero-tags">
          <span>${trip.fast ? 'Fast' : 'Slow'}</span>
          <span>${trip.cars} car</span>
          ${trip.ac ? '<span>AC</span>' : ''}
          <span class="dim">${LINES[trip.line].name}</span>
          <span class="dim">No. ${trip.num}</span>
        </div>
      </section>
      <div class="status" role="status">
        <span class="pulse${liveOn ? '' : ' off'}" aria-hidden="true"></span>
        <span class="status-text"><strong>${esc(headline)}</strong><span>${esc(sub)}. Estimated from the timetable.</span></span>
      </div>
      <h2 class="section">${stops.length} stops · ${trip.origin} to ${trip.dest}</h2>
      <ol class="stops" style="--lc:${lc}">${rows}</ol>
    `;
    view.querySelector('#back').onclick = closeTrain;
  }

  function renderLines() {
    view.innerHTML = `
      <h2 class="section">Lines</h2>
      <div class="legend"><span><b>Bold</b> stations are fast-train stops</span><span>Tap a station for its departures</span></div>
      ${Object.values(CORRIDORS).map(c => `
        <details class="corridor" data-c="${c.id}" ${state.openCorridor === c.id ? 'open' : ''}>
          <summary>${pill(c.line)}<span class="corridor-name">${esc(c.label)}</span><span class="corridor-count">${c.stations.length} stations</span></summary>
          <ul class="linemap" style="--lc:${lineVar(c.line)}">
            ${c.stations.map(([n, km]) => {
              const fastStop = c.fast && (c.fast.includes(n) || c.idx.get(n) >= c.idx.get(c.fastAllAfter));
              return `<li><span class="tick"></span><button class="stn" type="button" data-station="${esc(n)}">
                <span class="${fastStop ? 'fast-stop' : ''}">${esc(n)}</span><span class="km">${km.toFixed(1)} km</span></button></li>`;
            }).join('')}
          </ul>
        </details>`).join('')}
    `;
    view.querySelectorAll('details').forEach(d => d.ontoggle = () => {
      if (d.open) state.openCorridor = d.dataset.c;
      else if (state.openCorridor === d.dataset.c) state.openCorridor = null;
    });
    view.querySelectorAll('[data-station]').forEach(b => b.onclick = () => {
      state.station = b.dataset.station;
      store.set('station', state.station);
      rememberStation(state.station);
      setTab('station');
    });
  }

  function renderMore() {
    view.innerHTML = `
      <h2 class="section">Settings</h2>
      <div class="panel">
        <div class="panel-row"><span>Clock format</span>
          <span class="toolbar">
            <button class="chip" type="button" id="fmt12" aria-pressed="${!use24}">12-hour</button>
            <button class="chip" type="button" id="fmt24" aria-pressed="${use24}">24-hour</button>
          </span>
        </div>
        <div class="panel-row"><span>Saved routes and recent stations</span><button class="btn" type="button" id="clear">Clear</button></div>
      </div>
      <h2 class="section">Helplines</h2>
      <div class="panel">
        ${HELPLINES.map(h => `<div class="panel-row"><span>${esc(h.name)}</span><span class="num">${esc(h.number)}</span></div>`).join('')}
      </div>
      <h2 class="section">About this app</h2>
      <div class="prose">
        <p>Lokal covers ${STATIONS.size} stations on the Western, Central, Harbour and Trans-Harbour lines, with ${TRIPS.length.toLocaleString('en-IN')} trains a day in its sample timetable.</p>
        <p>The timetable in this prototype is generated from typical service patterns: how often each train type runs, where fast trains stop, and approximate distances. Real train numbers, platforms and times will differ. Live positions are worked out from the timetable, so a delayed train will not show as delayed.</p>
        <p>To install, open the browser menu and choose <strong>Add to Home screen</strong>. The app then works offline.</p>
      </div>
    `;
    view.querySelector('#fmt12').onclick = () => { use24 = false; store.set('use24', false); render(); tick(); };
    view.querySelector('#fmt24').onclick = () => { use24 = true; store.set('use24', true); render(); tick(); };
    const clear = view.querySelector('#clear');
    clear.onclick = () => {
      if (clear.dataset.confirm) {
        state.saved = []; state.recentStations = [];
        store.set('saved', []); store.set('recentStations', []);
        clear.textContent = 'Cleared';
        delete clear.dataset.confirm;
      } else {
        clear.dataset.confirm = '1';
        clear.textContent = 'Tap again to clear';
      }
    };
  }

  /* ------------------------------------------------------------------ *
   * Station picker
   * ------------------------------------------------------------------ */
  const sheet = document.getElementById('sheet');
  const sheetInput = document.getElementById('sheet-q');
  const sheetList = document.getElementById('sheet-list');
  let pickCb = null;

  function openPicker(label, cb) {
    pickCb = cb;
    sheetInput.value = '';
    sheetInput.placeholder = `${label}: search stations or codes like BVI, CSMT`;
    fillPicker('');
    sheet.hidden = false;
    setTimeout(() => sheetInput.focus(), 30);
  }
  function closePicker() { sheet.hidden = true; pickCb = null; }

  function stationButton(name) {
    const st = STATIONS.get(name);
    return `<li><button type="button" data-pick="${esc(name)}"><span>${esc(name)}</span>
      <span class="pills">${LINE_ORDER.filter(l => st.lines.has(l)).map(pill).join('')}</span></button></li>`;
  }
  function fillPicker(q) {
    q = q.trim().toLowerCase();
    let html = '';
    if (!q) {
      if (state.recentStations.length) {
        html += `<li class="sheet-sub">Recent</li>` + state.recentStations.filter(n => STATIONS.has(n)).map(stationButton).join('');
        html += `<li class="sheet-sub">All stations</li>`;
      }
      html += STATION_NAMES.map(stationButton).join('');
    } else {
      const scored = [];
      for (const n of STATION_NAMES) {
        const name = n.toLowerCase();
        const alias = (ALIASES[n] || '').toLowerCase();
        let score = -1;
        if (name.startsWith(q)) score = 0;
        else if (alias.split(' ').includes(q)) score = 1;
        else if (name.split(/[\s']+/).some(w => w.startsWith(q))) score = 2;
        else if (name.includes(q) || alias.includes(q)) score = 3;
        if (score >= 0) scored.push([score, n]);
      }
      scored.sort((a, b) => a[0] - b[0] || a[1].localeCompare(b[1]));
      html = scored.length ? scored.map(([, n]) => stationButton(n)).join('')
        : `<li class="sheet-sub">No station matches “${esc(q)}”</li>`;
    }
    sheetList.innerHTML = html;
  }
  sheetInput.addEventListener('input', () => fillPicker(sheetInput.value));
  sheetInput.addEventListener('keydown', e => {
    if (e.key === 'Enter') { const b = sheetList.querySelector('[data-pick]'); if (b) b.click(); }
    if (e.key === 'Escape') closePicker();
  });
  sheetList.addEventListener('click', e => {
    const b = e.target.closest('[data-pick]');
    if (!b) return;
    const name = b.dataset.pick;
    rememberStation(name);
    const cb = pickCb;
    closePicker();
    if (cb) cb(name);
  });
  document.getElementById('sheet-close').onclick = closePicker;
  sheet.addEventListener('click', e => { if (e.target === sheet) closePicker(); });

  /* ------------------------------------------------------------------ *
   * Boot
   * ------------------------------------------------------------------ */
  document.querySelectorAll('.tab').forEach(b => b.onclick = () => setTab(b.dataset.tab));
  document.querySelectorAll('.tab [data-icon]').forEach(s => { s.outerHTML = ICONS[s.dataset.icon]; });

  const clock = document.getElementById('clock');
  function tick() {
    const d = new Date();
    clock.textContent = fmt(d.getHours() * 60 + d.getMinutes());
  }
  tick();
  render();

  // Refresh countdowns and live positions every 20 seconds (not while picking).
  setInterval(() => {
    tick();
    if (!sheet.hidden) return;
    if (document.activeElement && document.activeElement.id === 'when-time') return;
    const top = view.scrollTop;
    render();
    view.scrollTop = top;
  }, 20000);

  if ('serviceWorker' in navigator && location.protocol.startsWith('http')) {
    navigator.serviceWorker.register('sw.js').catch(() => { /* offline support unavailable */ });
  }

  // Exposed for debugging in the console.
  window.lokal = { TRIPS, STATIONS, journeys, departures, tripState };
})();
