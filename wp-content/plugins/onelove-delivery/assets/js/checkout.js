/* Optional "pin your delivery spot" map on the WooCommerce checkout.
   Fails silently: if Maps can't load, checkout works exactly as before. */
(function () {
  'use strict';

  var C = window.OLE_CHECKOUT || {};
  var box = document.getElementById('ole-pin');
  if (!box || !C.key || !window.oleLoadMaps) return;

  var latInput = document.getElementById('ole_lat');
  var lngInput = document.getElementById('ole_lng');
  var status = document.getElementById('ole-pin-status');
  var map, marker;

  function setPoint(latLng, msg, zoom) {
    var lat = typeof latLng.lat === 'function' ? latLng.lat() : latLng.lat;
    var lng = typeof latLng.lng === 'function' ? latLng.lng() : latLng.lng;
    marker.setPosition({ lat: lat, lng: lng });
    map.panTo({ lat: lat, lng: lng });
    if (zoom) map.setZoom(zoom);
    latInput.value = lat.toFixed(7);
    lngInput.value = lng.toFixed(7);
    box.classList.add('is-set');
    status.textContent = msg || 'Pinned! Drag to fine-tune.';
  }

  window.oleLoadMaps(C.key).then(function (gm) {
    box.hidden = false;
    map = new gm.Map(document.getElementById('ole-pin-map'), {
      center: C.store,
      zoom: 12,
      disableDefaultUI: true,
      zoomControl: true,
      gestureHandling: 'cooperative',
      clickableIcons: false
    });
    marker = new gm.Marker({
      map: map,
      position: C.store,
      draggable: true,
      icon: window.oleMarkerIcon('#128a3f', 'H'),
      title: 'Your delivery spot'
    });
    marker.addListener('dragend', function () { setPoint(marker.getPosition(), 'Pinned! Thanks — the rider will come right here.'); });
    map.addListener('click', function (e) { setPoint(e.latLng, 'Pinned! Drag to fine-tune.'); });

    // Address search (Places API New). Optional — skipped if the library is unavailable.
    gm.importLibrary('places').then(function (places) {
      if (!places.PlaceAutocompleteElement) return;
      var ac = new places.PlaceAutocompleteElement({
        includedRegionCodes: ['in'],
        locationBias: { center: C.store, radius: 40000 }
      });
      ac.setAttribute('placeholder', 'Search your building or area');
      document.getElementById('ole-pin-search').appendChild(ac);

      function useplace(place) {
        if (!place) return;
        place.fetchFields({ fields: ['location', 'displayName'] }).then(function () {
          if (place.location) setPoint(place.location, 'Found it! Drag the pin to your exact gate.', 17);
        });
      }
      ac.addEventListener('gmp-select', function (ev) { if (ev.placePrediction) useplace(ev.placePrediction.toPlace()); });
      ac.addEventListener('gmp-placeselect', function (ev) { useplace(ev.place); });
    }).catch(function () {});

    document.getElementById('ole-pin-locate').addEventListener('click', function () {
      if (!navigator.geolocation) { status.textContent = 'Location is not available on this device.'; return; }
      status.textContent = 'Finding you…';
      navigator.geolocation.getCurrentPosition(function (pos) {
        setPoint({ lat: pos.coords.latitude, lng: pos.coords.longitude }, 'Pinned to your current location. Drag to fine-tune.', 17);
      }, function () {
        status.textContent = 'Could not get your location — search or drag the pin instead.';
      }, { enableHighAccuracy: true, timeout: 15000 });
    });
  }).catch(function () { /* Keep checkout untouched */ });
})();
