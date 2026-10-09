/* Loads the Google Maps JS API once and resolves when it is ready. */
window.oleLoadMaps = window.oleLoadMaps || (function () {
  var promise = null;
  return function (key) {
    if (window.google && window.google.maps && window.google.maps.importLibrary) {
      return Promise.resolve(window.google.maps);
    }
    if (!key) return Promise.reject(new Error('No Google Maps key'));
    if (promise) return promise;
    promise = new Promise(function (resolve, reject) {
      var cb = '__oleMapsReady' + Date.now();
      window[cb] = function () { resolve(window.google.maps); delete window[cb]; };
      window.gm_authFailure = function () { reject(new Error('Google Maps key rejected')); };
      var s = document.createElement('script');
      s.src = 'https://maps.googleapis.com/maps/api/js?key=' + encodeURIComponent(key) +
        '&v=weekly&loading=async&libraries=places,geometry&region=IN&language=en&callback=' + cb;
      s.async = true;
      s.onerror = function () { reject(new Error('Google Maps failed to load')); };
      document.head.appendChild(s);
    });
    return promise;
  };
})();

/* Simple round SVG markers that look the same everywhere (no Map ID needed). */
window.oleMarkerIcon = function (color, label) {
  var svg = '<svg xmlns="http://www.w3.org/2000/svg" width="44" height="54" viewBox="0 0 44 54">' +
    '<path d="M22 52s18-17.6 18-30A18 18 0 0 0 4 22c0 12.4 18 30 18 30z" fill="' + color + '" stroke="#0b0b0b" stroke-width="3"/>' +
    '<circle cx="22" cy="22" r="11" fill="#fff" stroke="#0b0b0b" stroke-width="2"/>' +
    '<text x="22" y="27" font-family="Arial,sans-serif" font-size="13" font-weight="900" text-anchor="middle" fill="#0b0b0b">' + label + '</text></svg>';
  return {
    url: 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(svg),
    scaledSize: new window.google.maps.Size(44, 54),
    anchor: new window.google.maps.Point(22, 52)
  };
};
