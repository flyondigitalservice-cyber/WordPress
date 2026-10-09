/* One Love HQ — live refresh + chart tooltip. */
(function () {
  'use strict';
  var C = window.OLE_HQ || {};
  var live = document.getElementById('ole-hq-live');
  if (!live) return;

  function bindTips() {
    var card = live.querySelector('.hq-chart');
    var tip = card && card.querySelector('.hq-tip');
    if (!card || !tip) return;
    function show(col) {
      var cr = card.getBoundingClientRect();
      var br = col.getBoundingClientRect();
      tip.textContent = col.getAttribute('data-tip');
      tip.hidden = false;
      tip.style.left = (br.left - cr.left + br.width / 2) + 'px';
      tip.style.top = (br.top - cr.top + 18) + 'px';
    }
    card.querySelectorAll('.hq-bars__col').forEach(function (col) {
      col.addEventListener('mouseenter', function () { show(col); });
      col.addEventListener('focus', function () { show(col); });
      col.addEventListener('mouseleave', function () { tip.hidden = true; });
      col.addEventListener('blur', function () { tip.hidden = true; });
    });
  }

  function refresh() {
    if (document.hidden) return;
    var body = new FormData();
    body.append('action', 'ole_hq_live');
    body.append('nonce', C.nonce);
    live.classList.add('is-refreshing');
    fetch(C.ajax, { method: 'POST', body: body, credentials: 'same-origin' })
      .then(function (r) { return r.json(); })
      .then(function (j) { if (j && j.success) { live.innerHTML = j.data.html; bindTips(); } })
      .catch(function () {})
      .finally(function () { live.classList.remove('is-refreshing'); });
  }

  bindTips();
  setInterval(refresh, 60000);
  document.addEventListener('visibilitychange', function () { if (!document.hidden) refresh(); });
})();
