/* LuxuryWatchs Signature — front-end interactions (no dependencies). */
(function () {
	'use strict';

	var $ = function (sel, ctx) { return (ctx || document).querySelector(sel); };
	var $$ = function (sel, ctx) { return Array.prototype.slice.call((ctx || document).querySelectorAll(sel)); };
	var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	// Header shadow on scroll.
	var header = $('[data-header]');
	if (header) {
		var onScroll = function () { header.classList.toggle('is-scrolled', window.scrollY > 10); };
		window.addEventListener('scroll', onScroll, { passive: true });
		onScroll();
	}

	// Search toggle.
	var searchBtn = $('[data-search-toggle]');
	var search = $('[data-search]');
	if (searchBtn && search) {
		searchBtn.addEventListener('click', function () {
			search.hidden = !search.hidden;
			if (!search.hidden) { $('input[type="search"]', search).focus(); }
		});
	}

	// Mobile drawer.
	var drawer = $('[data-drawer]');
	var openBtn = $('[data-drawer-open]');
	if (drawer && openBtn) {
		var setDrawer = function (open) {
			drawer.hidden = !open;
			openBtn.setAttribute('aria-expanded', String(open));
			document.body.classList.toggle('lw-lock', open);
			if (open) { $('[data-drawer-close]', drawer.querySelector('aside')).focus(); } else { openBtn.focus(); }
		};
		openBtn.addEventListener('click', function () { setDrawer(true); });
		$$('[data-drawer-close]', drawer).forEach(function (el) {
			el.addEventListener('click', function () { setDrawer(false); });
		});
		document.addEventListener('keydown', function (e) {
			if (e.key === 'Escape' && !drawer.hidden) { setDrawer(false); }
		});
	}

	// Hero slider.
	var slider = $('[data-slider]');
	if (slider) {
		var slides = $$('[data-slide]', slider);
		var dotsWrap = $('[data-dots]', slider);
		var current = 0;
		var timer;
		var dots = slides.map(function (_, i) {
			var b = document.createElement('button');
			b.type = 'button';
			b.setAttribute('aria-label', 'Go to slide ' + (i + 1));
			b.addEventListener('click', function () { go(i); restart(); });
			dotsWrap.appendChild(b);
			return b;
		});
		var go = function (i) {
			current = (i + slides.length) % slides.length;
			slides.forEach(function (s, j) {
				s.classList.toggle('is-active', j === current);
				s.setAttribute('aria-hidden', String(j !== current));
			});
			dots.forEach(function (d, j) { d.setAttribute('aria-current', String(j === current)); });
		};
		var restart = function () {
			clearInterval(timer);
			if (!reduceMotion && slides.length > 1) { timer = setInterval(function () { go(current + 1); }, 6000); }
		};
		$('[data-prev]', slider).addEventListener('click', function () { go(current - 1); restart(); });
		$('[data-next]', slider).addEventListener('click', function () { go(current + 1); restart(); });
		slider.addEventListener('mouseenter', function () { clearInterval(timer); });
		slider.addEventListener('mouseleave', restart);

		// Swipe support.
		var startX = null;
		slider.addEventListener('touchstart', function (e) { startX = e.touches[0].clientX; }, { passive: true });
		slider.addEventListener('touchend', function (e) {
			if (startX === null) { return; }
			var dx = e.changedTouches[0].clientX - startX;
			if (Math.abs(dx) > 50) { go(current + (dx < 0 ? 1 : -1)); restart(); }
			startX = null;
		});

		go(0);
		restart();
	}

	// Horizontal rails.
	$$('[data-rail-prev],[data-rail-next]').forEach(function (btn) {
		btn.addEventListener('click', function () {
			var name = btn.getAttribute('data-rail-prev') || btn.getAttribute('data-rail-next');
			var rail = $('[data-rail="' + name + '"]');
			if (!rail) { return; }
			var dir = btn.hasAttribute('data-rail-next') ? 1 : -1;
			rail.scrollBy({ left: dir * rail.clientWidth * 0.8, behavior: reduceMotion ? 'auto' : 'smooth' });
		});
	});

	// Countdown: rolls to the next Sunday midnight IST so it never shows zero.
	var cd = $('[data-countdown]');
	if (cd) {
		var target = (function () {
			var now = new Date();
			var istNow = new Date(now.getTime() + (now.getTimezoneOffset() + 330) * 60000);
			var daysToSunday = (7 - istNow.getDay()) % 7 || 7;
			var t = new Date(istNow);
			t.setDate(istNow.getDate() + daysToSunday);
			t.setHours(0, 0, 0, 0);
			return now.getTime() + (t.getTime() - istNow.getTime());
		})();
		var pad = function (n) { return (n < 10 ? '0' : '') + n; };
		var tick = function () {
			var diff = Math.max(0, target - Date.now());
			var s = Math.floor(diff / 1000);
			$('[data-d]', cd).textContent = pad(Math.floor(s / 86400));
			$('[data-h]', cd).textContent = pad(Math.floor((s % 86400) / 3600));
			$('[data-m]', cd).textContent = pad(Math.floor((s % 3600) / 60));
			$('[data-s]', cd).textContent = pad(s % 60);
		};
		tick();
		setInterval(tick, 1000);
	}

	// Newsletter: hand off to WhatsApp with the email prefilled.
	var news = $('[data-news]');
	if (news) {
		news.addEventListener('submit', function (e) {
			e.preventDefault();
			var email = $('input[type="email"]', news).value;
			var url = news.getAttribute('action').split('?')[0];
			var text = 'Hi! Please add me to the LuxuryWatchs VIP list. My email: ' + email;
			window.open(url + '?text=' + encodeURIComponent(text), '_blank', 'noopener');
		});
	}

	// WhatsApp contact form: build the message from the fields.
	$$('[data-waform]').forEach(function (form) {
		form.addEventListener('submit', function (e) {
			e.preventDefault();
			var lines = ['Hi LuxuryWatchs!'];
			$$('[data-field]', form).forEach(function (f) {
				if (f.value.trim()) { lines.push(f.getAttribute('data-field') + ': ' + f.value.trim()); }
			});
			window.open(form.getAttribute('action') + '?text=' + encodeURIComponent(lines.join('\n')), '_blank', 'noopener');
		});
	});

	// Sticky add-to-cart once the main button scrolls out of view.
	var sticky = $('[data-sticky-atc]');
	var mainAtc = $('.single_add_to_cart_button');
	if (sticky && mainAtc && 'IntersectionObserver' in window) {
		new IntersectionObserver(function (entries) {
			sticky.classList.toggle('is-visible', !entries[0].isIntersecting && entries[0].boundingClientRect.top < 0);
		}).observe(mainAtc);
	}

	// Reveal sections on scroll.
	if (!reduceMotion && 'IntersectionObserver' in window) {
		var io = new IntersectionObserver(function (entries) {
			entries.forEach(function (en) {
				if (en.isIntersecting) { en.target.classList.add('is-in'); io.unobserve(en.target); }
			});
		}, { rootMargin: '0px 0px -10% 0px' });
		$$('.lw-section .lw-container, .lw-promo__inner').forEach(function (el) {
			el.classList.add('lw-reveal');
			io.observe(el);
		});
	}
})();
