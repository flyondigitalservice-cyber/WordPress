/* Delivery Dispatch board (wp-admin). */
(function () {
  'use strict';

  var C = window.OLE_DISPATCH || {};
  var data = { deliveries: [], riders: [], store: null };
  var filter = 'open';
  var map = null, markers = {}, fitted = false, focusId = null;

  function $(id) { return document.getElementById(id); }
  function esc(s) {
    return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) {
      return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
    });
  }
  function money(n) { return '₹' + Number(n || 0).toLocaleString('en-IN', { maximumFractionDigits: 2 }); }
  function ago(ts) {
    if (!ts) return 'never';
    var s = Math.max(0, Math.round(Date.now() / 1000 - ts));
    if (s < 60) return s + 's ago';
    if (s < 3600) return Math.round(s / 60) + ' min ago';
    if (s < 86400) return Math.round(s / 3600) + ' h ago';
    return Math.round(s / 86400) + ' d ago';
  }

  function api(path, body) {
    return fetch(C.api + path, {
      method: body ? 'POST' : 'GET',
      credentials: 'same-origin',
      headers: { 'X-WP-Nonce': C.nonce, 'Content-Type': 'application/json' },
      body: body ? JSON.stringify(body) : undefined
    }).then(function (res) {
      return res.json().catch(function () { return {}; }).then(function (j) {
        if (!res.ok) throw new Error((j && j.message) || 'Request failed');
        return j;
      });
    });
  }

  var GROUPS = {
    open: ['pending', 'assigned', 'out_for_delivery', 'failed'],
    pending: ['pending', 'failed'],
    moving: ['assigned', 'out_for_delivery'],
    done: ['delivered', 'cancelled']
  };

  /* ---------------- Render ---------------- */

  function render() {
    var d = data.deliveries;
    var count = function (st) { return d.filter(function (x) { return st.indexOf(x.status) > -1; }).length; };
    var online = data.riders.filter(function (r) { return r.online; });
    var cash = data.riders.reduce(function (a, r) { return a + Number(r.cash || 0); }, 0);

    $('ole-d-sub').textContent = (data.auto ? 'Auto-assign ON' : 'Auto-assign OFF') + ' · refreshed ' + new Date().toLocaleTimeString('en-IN', { hour: 'numeric', minute: '2-digit', second: '2-digit' });

    $('ole-d-kpis').innerHTML = [
      ['Waiting', count(['pending', 'failed']), count(['failed']) ? 'is-alert' : ''],
      ['Rider assigned', count(['assigned']), ''],
      ['On the way', count(['out_for_delivery']), ''],
      ['Delivered (24h)', count(['delivered']), 'is-good'],
      ['Riders on duty', online.length + ' / ' + data.riders.length, online.length ? '' : 'is-alert'],
      ['Cash with riders', money(cash), '']
    ].map(function (k) {
      return '<div class="ole-kpi ' + k[2] + '"><b>' + esc(k[1]) + '</b><span>' + esc(k[0]) + '</span></div>';
    }).join('');

    var list = d.filter(function (x) { return GROUPS[filter].indexOf(x.status) > -1; });
    // Don't redraw the list while the admin is picking a rider in a dropdown.
    var picking = document.activeElement && document.activeElement.matches && document.activeElement.matches('#ole-d-list select');
    if (!picking) $('ole-d-list').innerHTML = list.length ? list.map(row).join('') :
      '<div class="ole-d-empty">Nothing here right now.</div>';

    $('ole-d-riders').innerHTML = data.riders.length ? data.riders.map(riderRow).join('') :
      '<div class="ole-d-empty">No riders yet. <a href="' + esc(C.addRider) + '">Add a user</a> with the role <b>Delivery Rider</b>.</div>';

    drawMap();
  }

  function riderOptions(selected) {
    return '<option value="0">Auto (best rider)</option>' + data.riders.map(function (r) {
      return '<option value="' + r.id + '"' + (r.id === selected ? ' selected' : '') + '>' +
        esc(r.name) + (r.online ? ' • on duty' : ' • off') + ' (' + r.active + ' active)</option>';
    }).join('');
  }

  function row(x) {
    var canAssign = ['pending', 'failed', 'assigned'].indexOf(x.status) > -1;
    var canDeliver = ['assigned', 'out_for_delivery'].indexOf(x.status) > -1;
    return '<article class="ole-row ole-row--' + esc(x.status) + (focusId === x.id ? ' is-focus' : '') + '" data-id="' + x.id + '">' +
      '<div class="ole-row__top">' +
        '<a class="ole-row__order" href="' + esc(x.edit_url) + '">#' + esc(x.order) + '</a>' +
        '<span class="ole-pill ole-pill--' + esc(x.status) + '">' + esc(x.label) + '</span>' +
        '<span class="ole-row__age">' + esc(ago(x.created)) + '</span>' +
      '</div>' +
      '<div class="ole-row__who"><b>' + esc(x.customer) + '</b> · <a href="tel:' + esc(x.phone) + '">' + esc(x.phone) + '</a></div>' +
      '<div class="ole-row__addr">' + esc(x.address) + (x.lat == null ? ' <em>(no map pin)</em>' : '') + '</div>' +
      '<div class="ole-row__meta">' + esc((x.items || []).join(', ')) + ' · <b>' + money(x.total) + '</b> ' +
        (x.is_cod ? '<span class="ole-cod">COD</span>' : '<span class="ole-paid">Paid</span>') +
        (x.rider ? ' · Rider: <b>' + esc(x.rider) + '</b>' : '') + '</div>' +
      (x.fail ? '<div class="ole-row__fail">Problem: ' + esc(x.fail) + '</div>' : '') +
      '<div class="ole-row__actions">' +
        (canAssign ? '<select data-rider>' + riderOptions(x.rider_id) + '</select><button type="button" class="button button-primary" data-do="assign">' + (x.rider_id ? 'Re-assign' : 'Assign') + '</button>' : '') +
        (canDeliver ? '<button type="button" class="button" data-do="delivered">Mark delivered</button>' : '') +
        (x.status !== 'delivered' && x.status !== 'cancelled' ? '<button type="button" class="button-link ole-cancel" data-do="cancelled">Cancel</button>' : '') +
        '<a class="button-link" href="' + esc(x.track_url) + '" target="_blank">Tracking page ↗</a>' +
      '</div>' +
    '</article>';
  }

  function riderRow(r) {
    return '<div class="ole-rider' + (r.online ? ' is-on' : '') + '">' +
      '<span class="ole-rider__dot"></span>' +
      '<div class="ole-rider__info"><b>' + esc(r.name) + '</b>' +
        '<small>' + (r.online ? 'On duty' : 'Off duty') + ' · GPS ' + esc(ago(r.last_seen)) + ' · ' + r.active + ' active</small>' +
        (r.phone ? '<small><a href="tel:' + esc(r.phone) + '">' + esc(r.phone) + '</a></small>' : '<small class="ole-warn">No phone on profile</small>') +
      '</div>' +
      '<div class="ole-rider__cash">' + (Number(r.cash) > 0 ?
        '<b>' + money(r.cash) + '</b><button type="button" class="button button-small" data-settle="' + r.id + '">Cash received</button>' :
        '<small>No cash due</small>') + '</div>' +
    '</div>';
  }

  /* ---------------- Map ---------------- */

  function initMap() {
    if (map || !C.mapsKey || !data.store) return;
    window.oleLoadMaps(C.mapsKey).then(function (gm) {
      map = new gm.Map($('ole-d-map'), {
        center: { lat: data.store.lat, lng: data.store.lng },
        zoom: 12,
        mapTypeControl: false,
        streetViewControl: false,
        clickableIcons: false
      });
      drawMap();
    }).catch(function (e) {
      $('ole-d-map').innerHTML = '<div class="ole-d-empty">Map unavailable: ' + esc(e.message) + '. Check the key in Settings.</div>';
    });
  }

  function upsert(key, lat, lng, color, label, title, onClick) {
    if (lat == null || lng == null) return;
    var pos = { lat: lat, lng: lng };
    if (markers[key]) {
      markers[key].setPosition(pos);
      markers[key].setTitle(title);
    } else {
      markers[key] = new google.maps.Marker({ map: map, position: pos, icon: window.oleMarkerIcon(color, label), title: title });
      if (onClick) markers[key].addListener('click', onClick);
    }
    markers[key].__seen = true;
  }

  function drawMap() {
    if (!map) { initMap(); return; }
    Object.keys(markers).forEach(function (k) { markers[k].__seen = false; });

    upsert('store', data.store.lat, data.store.lng, '#f2b705', 'S', data.store.name);
    data.riders.forEach(function (r) {
      if (r.lat == null) return;
      upsert('r' + r.id, r.lat, r.lng, r.online ? '#1a73e8' : '#9ca3af', (r.name || 'R').charAt(0).toUpperCase(), r.name + (r.online ? ' (on duty)' : ' (off duty)'));
    });
    data.deliveries.forEach(function (x) {
      if (x.lat == null || GROUPS.open.indexOf(x.status) === -1) return;
      var color = x.status === 'out_for_delivery' ? '#128a3f' : (x.status === 'failed' || x.status === 'pending') ? '#d92d20' : '#0b0b0b';
      upsert('d' + x.id, x.lat, x.lng, color, '#', '#' + x.order + ' · ' + x.customer + ' · ' + x.label, function () { focus(x.id); });
    });

    Object.keys(markers).forEach(function (k) {
      if (!markers[k].__seen) { markers[k].setMap(null); delete markers[k]; }
    });

    if (!fitted && Object.keys(markers).length > 1) {
      var b = new google.maps.LatLngBounds();
      Object.keys(markers).forEach(function (k) { b.extend(markers[k].getPosition()); });
      map.fitBounds(b, 60);
      fitted = true;
    }
  }

  function focus(id) {
    focusId = id;
    var x = data.deliveries.filter(function (d) { return d.id === id; })[0];
    if (x && GROUPS[filter].indexOf(x.status) === -1) {
      filter = 'open';
      setTab();
    }
    render();
    var el = document.querySelector('.ole-row[data-id="' + id + '"]');
    if (el) el.scrollIntoView({ behavior: 'smooth', block: 'center' });
  }

  function setTab() {
    Array.prototype.forEach.call(document.querySelectorAll('.ole-d-tabs button'), function (b) {
      b.classList.toggle('is-active', b.getAttribute('data-filter') === filter);
    });
  }

  /* ---------------- Events ---------------- */

  document.addEventListener('click', function (e) {
    var tab = e.target.closest('.ole-d-tabs button');
    if (tab) {
      filter = tab.getAttribute('data-filter');
      setTab();
      render();
      return;
    }

    var settle = e.target.closest('[data-settle]');
    if (settle) {
      if (!window.confirm('Confirm you received this cash from the rider?')) return;
      settle.disabled = true;
      api('/dispatch/riders/' + settle.getAttribute('data-settle') + '/settle', {})
        .then(function (r) { window.alert('Recorded ' + money(r.amount) + ' as received.'); load(); })
        .catch(function (err) { window.alert(err.message); settle.disabled = false; });
      return;
    }

    var act = e.target.closest('[data-do]');
    if (!act) {
      var rowEl = e.target.closest('.ole-row');
      if (rowEl && map && !e.target.closest('a,select,button')) {
        var id = Number(rowEl.getAttribute('data-id'));
        var m = markers['d' + id];
        if (m) { map.panTo(m.getPosition()); map.setZoom(15); }
        focusId = id;
        render();
      }
      return;
    }
    var row = act.closest('.ole-row');
    var deliveryId = row.getAttribute('data-id');
    var what = act.getAttribute('data-do');
    var req;

    if (what === 'assign') {
      var sel = row.querySelector('[data-rider]');
      req = api('/dispatch/' + deliveryId + '/assign', { rider_id: Number(sel.value) });
    } else {
      var ask = what === 'delivered' ? 'Mark this order as delivered without the rider OTP?' : 'Cancel this delivery? (The WooCommerce order is not changed.)';
      if (!window.confirm(ask)) return;
      req = api('/dispatch/' + deliveryId + '/status', { status: what });
    }
    act.disabled = true;
    req.then(load).catch(function (err) { window.alert(err.message); }).finally(function () { act.disabled = false; });
  });

  /* ---------------- Load ---------------- */

  function load() {
    return api('/dispatch').then(function (j) {
      data = j;
      render();
    }).catch(function (err) {
      $('ole-d-sub').textContent = 'Could not refresh: ' + err.message;
    });
  }

  load();
  setInterval(function () { if (!document.hidden) load(); }, 15000);
})();
