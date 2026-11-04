(function () {
	// Mobile nav
	var t = document.querySelector('[data-nav-toggle]');
	var nav = document.getElementById('site-nav');
	if (t && nav) {
		t.addEventListener('click', function () {
			var open = nav.classList.toggle('is-open');
			t.setAttribute('aria-expanded', open ? 'true' : 'false');
			t.querySelector('[data-icon-open]').hidden = open;
			t.querySelector('[data-icon-close]').hidden = !open;
		});
		document.addEventListener('keydown', function (e) {
			if (e.key === 'Escape' && nav.classList.contains('is-open')) { t.click(); t.focus(); }
		});
	}

	// Service filters
	var bar = document.querySelector('[data-filters]');
	if (bar) {
		var cards = document.querySelectorAll('[data-cat]');
		bar.addEventListener('click', function (e) {
			var b = e.target.closest('button');
			if (!b) return;
			var f = b.getAttribute('data-filter');
			bar.querySelectorAll('button').forEach(function (x) { x.setAttribute('aria-pressed', x === b ? 'true' : 'false'); });
			cards.forEach(function (c) { c.hidden = !(f === 'all' || c.getAttribute('data-cat') === f); });
			var reg = document.querySelector('.register');
			if (reg) reg.classList.toggle('is-filtered', f !== 'all');
		});
	}
})();
