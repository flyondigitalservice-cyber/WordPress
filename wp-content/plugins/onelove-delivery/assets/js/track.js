/* Customer live tracking page. */
(function () {
  'use strict';

  var C = window.OLE_TRACK || {};
  var map = null, markers = {}, fitted = false, last = null;

  function $(id) { return document.getElementById(id); }
  function money(n) { return '₹' + Number(n || 0).toLocaleString('en-IN', { maximumFractionDigits: 2 }); }
  function time(ts) {
    return ts ? new Date(ts * 1000).toLocaleTimeString('en-IN', { hour: 'numeric', minute: '2-digit' }) : '';
  }
  function tel(p) { return 'tel:' + String(p || '').replace(/[^\d+]/g, ''); }

  var HEADLINE = {
    pending: 'Order confirmed — finding a rider',
    assigned: 'Your rider is getting ready',
    out_for_delivery: 'On the way to you',
    delivered: 'Delivered. Enjoy!',
    failed: 'Delivery delayed — we will call you',
    cancelled: 'This order was cancelled'
  };
  var REACHED = {
    pending: 1, assigned: 2, out_for_delivery: 3, delivered: 4, failed: 2, cancelled: 0
  };

  function initMap(data) {
    if (map || !C.mapsKey) return;
    window.oleLoadMaps(C.mapsKey).then(function (gm) {
      var center = data.dest || data.store;
      map = new gm.Map($('t-map'), {
        center: { lat: center.lat, lng: center.lng },
        zoom: 14,
        disableDefaultUI: true,
        zoomControl: true,
        gestureHandling: 'greedy',
        clickableIcons: false,
        styles: [
          { featureType: 'poi', stylers: [{ visibility: 'off' }] },
          { featureType: 'transit', stylers: [{ visibility: 'off' }] }
        ]
      });
      $('t-map-ph').hidden = true;
      draw(last || data);
    }).catch(function () { /* Map is optional — status still works. */ });
  }

  function place(key, pos, color, label, title) {
    if (!map || !pos || pos.lat == null) {
      if (markers[key]) { markers[key].setMap(null); delete markers[key]; }
      return;
    }
    var latLng = { lat: pos.lat, lng: pos.lng };
    if (!markers[key]) {
      markers[key] = new google.maps.Marker({ map: map, position: latLng, icon: window.oleMarkerIcon(color, label), title: title, zIndex: key === 'rider' ? 10 : 1 });
    } else {
      markers[key].setPosition(latLng);
    }
  }

  function draw(data) {
    if (!map) return;
    var active = data.status === 'assigned' || data.status === 'out_for_delivery';
    place('store', data.store, '#f2b705', 'S', data.store.name);
    place('home', data.dest, '#128a3f', 'H', 'Delivery location');
    place('rider', active && data.rider ? data.rider : null, '#1a73e8', 'R', 'Rider');

    if (!fitted || active) {
      var b = new google.maps.LatLngBounds();
      var n = 0;
      Object.keys(markers).forEach(function (k) {
        if (k === 'store' && data.status === 'out_for_delivery' && markers.rider && markers.home) return;
        b.extend(markers[k].getPosition()); n++;
      });
      if (n > 1) map.fitBounds(b, { top: 60, bottom: 40, left: 40, right: 40 });
      else if (n === 1) map.setCenter(b.getCenter());
      fitted = true;
    }
  }

  function render(data) {
    last = data;
    $('t-order').textContent = '#' + data.order;
    $('t-status').textContent = HEADLINE[data.status] || data.label;
    document.body.setAttribute('data-status', data.status);

    // Calm, fixed delivery window (no live countdown) — riders are never rushed.
    var p = data.promise || {};
    var windowText = p.from && p.to ? time(p.from) + '–' + time(p.to) : '';
    var late = p.to && Date.now() / 1000 > p.to;
    var eta = '';
    if (data.status === 'pending' || data.status === 'assigned') {
      eta = windowText ? 'Expected between ' + windowText : 'Delivered within ' + (p.text || '30–45 min');
    } else if (data.status === 'out_for_delivery') {
      eta = late || !windowText ? 'On the way — arriving shortly' : 'On the way · expected by ' + time(p.to);
    } else if (data.status === 'delivered') {
      eta = 'Delivered at ' + time(data.times.delivered);
    }
    $('t-eta').textContent = eta;

    $('t-otp').hidden = !data.otp;
    $('t-otp-code').textContent = data.otp || '';

    var cod = $('t-cod');
    if (data.is_cod && data.status !== 'delivered' && data.status !== 'cancelled') {
      cod.hidden = false;
      cod.innerHTML = 'Cash on delivery: please keep <b>' + money(data.amount) + '</b> ready';
    } else {
      cod.hidden = true;
    }

    var reached = REACHED[data.status] || 0;
    Array.prototype.forEach.call($('t-steps').children, function (li, i) {
      li.classList.toggle('done', i < reached);
      li.classList.toggle('now', i === reached - 1 && data.status !== 'delivered');
      var t = data.times[li.getAttribute('data-step')];
      li.querySelector('small').textContent = t ? time(t) : '';
    });

    var r = data.rider;
    $('t-rider').hidden = !r;
    if (r) {
      $('t-rider-name').textContent = r.name;
      $('t-rider-initial').textContent = (r.name || 'R').trim().charAt(0).toUpperCase();
      var call = $('t-rider-call');
      call.hidden = !r.phone;
      call.href = tel(r.phone);
    }
    $('t-store-call').href = tel(data.store.phone);

    var liveOn = data.status === 'assigned' || data.status === 'out_for_delivery' || data.status === 'pending';
    $('t-live').classList.toggle('off', !liveOn);

    initMap(data);
    draw(data);
    return liveOn;
  }

  function poll() {
    fetch(C.api, { credentials: 'omit', cache: 'no-store' })
      .then(function (r) { return r.json(); })
      .then(function (data) {
        if (!data || !data.status) return;
        var live = render(data);
        setTimeout(poll, live ? (document.hidden ? 60000 : 15000) : 120000);
      })
      .catch(function () { setTimeout(poll, 30000); });
  }

  poll();
})();
