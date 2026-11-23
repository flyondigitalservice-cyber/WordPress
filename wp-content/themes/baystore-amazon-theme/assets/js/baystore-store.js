/* Baystore — header search toggle and homepage tabs. */
(function () {
  'use strict';

  document.addEventListener('click', function (e) {
    var toggle = e.target.closest('[data-bs-toggle-search]');
    if (toggle) {
      var panel = document.getElementById('bs-search-panel');
      if (panel) {
        var open = panel.classList.toggle('is-open');
        if (open) {
          var input = document.getElementById('bs-search-input');
          if (input) input.focus();
        }
      }
      return;
    }

    var tab = e.target.closest('[data-bs-tab]');
    if (tab) {
      var key = tab.getAttribute('data-bs-tab');
      var section = tab.closest('section') || document;
      section.querySelectorAll('[data-bs-tab]').forEach(function (t) {
        t.classList.toggle('is-active', t === tab);
      });
      section.querySelectorAll('[data-bs-panel]').forEach(function (p) {
        p.classList.toggle('is-active', p.getAttribute('data-bs-panel') === key);
      });
    }
  });

  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
      var panel = document.getElementById('bs-search-panel');
      if (panel) panel.classList.remove('is-open');
    }
  });
})();
