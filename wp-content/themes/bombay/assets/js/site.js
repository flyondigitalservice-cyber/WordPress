/* Bombay Marketing theme: mobile menu, smart sticky header, back-to-top. No dependencies. */
(function () {
	'use strict';

	var header = document.getElementById('bm-header');
	var mobile = document.getElementById('bm-mobile');
	var burger = header && header.querySelector('.bm-burger');
	var closeBtn = mobile && mobile.querySelector('.bm-close');
	var topBtn = document.getElementById('bm-top');

	/* Mobile menu */
	function openMenu() {
		mobile.hidden = false;
		document.body.classList.add('bm-menu-open');
		burger.setAttribute('aria-expanded', 'true');
		closeBtn.focus();
	}
	function closeMenu() {
		mobile.hidden = true;
		document.body.classList.remove('bm-menu-open');
		burger.setAttribute('aria-expanded', 'false');
		burger.focus();
	}
	if (burger && mobile && closeBtn) {
		burger.addEventListener('click', openMenu);
		closeBtn.addEventListener('click', closeMenu);
		document.addEventListener('keydown', function (e) {
			if (e.key === 'Escape' && !mobile.hidden) { closeMenu(); }
		});
		mobile.querySelectorAll('.menu-item-has-children').forEach(function (li, i) {
			var sub = li.querySelector('.sub-menu');
			if (!sub) { return; }
			sub.id = sub.id || 'bm-sub-' + i;
			var btn = document.createElement('button');
			btn.type = 'button';
			btn.className = 'bm-sub-toggle';
			btn.setAttribute('aria-expanded', 'false');
			btn.setAttribute('aria-controls', sub.id);
			btn.setAttribute('aria-label', 'Show ' + li.querySelector('a').textContent.trim() + ' pages');
			btn.innerHTML = '<svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true"><path d="M9 6l6 6-6 6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
			btn.addEventListener('click', function () {
				var open = li.classList.toggle('is-open');
				btn.setAttribute('aria-expanded', open ? 'true' : 'false');
			});
			li.insertBefore(btn, sub);
		});
	}

	/* Smart sticky header: show a fixed header when scrolling up past the hero */
	var lastY = window.pageYOffset;
	var ticking = false;
	function onScroll() {
		var y = window.pageYOffset;
		if (header) {
			if (y > 300) {
				header.classList.add('is-fixed');
				header.classList.toggle('is-shown', y < lastY);
			} else if (y <= 0 || !header.classList.contains('is-shown')) {
				header.classList.remove('is-fixed', 'is-shown');
			}
		}
		if (topBtn) { topBtn.classList.toggle('is-visible', y > 600); }
		lastY = y;
		ticking = false;
	}
	window.addEventListener('scroll', function () {
		if (!ticking) { ticking = true; window.requestAnimationFrame(onScroll); }
	}, { passive: true });

	if (topBtn) {
		topBtn.addEventListener('click', function () {
			window.scrollTo({ top: 0, behavior: 'smooth' });
		});
	}
})();
