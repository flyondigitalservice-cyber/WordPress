/* One Love Rider service worker: makes the app installable and shows a friendly offline screen.
   API calls are never cached — order data must always be live. */
var CACHE = 'ole-rider-v1';

self.addEventListener('install', function (e) {
  self.skipWaiting();
});

self.addEventListener('activate', function (e) {
  e.waitUntil(
    caches.keys().then(function (keys) {
      return Promise.all(keys.filter(function (k) { return k !== CACHE; }).map(function (k) { return caches.delete(k); }));
    }).then(function () { return self.clients.claim(); })
  );
});

self.addEventListener('fetch', function (e) {
  var req = e.request;
  if (req.method !== 'GET' || req.mode !== 'navigate') return;
  e.respondWith(
    fetch(req).then(function (res) {
      var copy = res.clone();
      caches.open(CACHE).then(function (c) { c.put(req, copy); });
      return res;
    }).catch(function () {
      return caches.match(req).then(function (hit) {
        return hit || new Response(
          '<!doctype html><meta name="viewport" content="width=device-width,initial-scale=1"><body style="font-family:system-ui;background:#0b0b0b;color:#f6f1e4;display:grid;place-items:center;min-height:100vh;text-align:center;padding:24px"><div><h1>No internet</h1><p>Check your mobile data and reopen the app.</p></div>',
          { headers: { 'Content-Type': 'text/html; charset=utf-8' } }
        );
      });
    })
  );
});
