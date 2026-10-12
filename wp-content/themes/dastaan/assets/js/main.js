/*!
 * Dastaan by Artisans — main.js (vanilla, no jQuery dependency)
 */
(function () {
	'use strict';

	var D = window.dastaanData || {};
	var doc = document;
	var body = doc.body;
	var $ = function (sel, ctx) { return (ctx || doc).querySelector(sel); };
	var $$ = function (sel, ctx) { return Array.prototype.slice.call((ctx || doc).querySelectorAll(sel)); };
	var on = function (el, ev, fn, opts) { if (el) { el.addEventListener(ev, fn, opts || false); } };
	var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	doc.documentElement.classList.remove('no-js');
	doc.documentElement.classList.add('js');

	/* -------------------------------------------------------------------
	 * Toast
	 * ---------------------------------------------------------------- */
	var toastTimer;
	function toast(message, action) {
		var el = $('.toast');
		if (!el) { return; }
		el.innerHTML = '';
		el.appendChild(doc.createTextNode(message));
		if (action && action.href && action.label) {
			var a = doc.createElement('a');
			a.href = action.href;
			a.textContent = ' ' + action.label;
			a.style.cssText = 'color:#D4B46A;margin-left:10px;text-decoration:underline';
			el.appendChild(a);
		}
		el.classList.add('is-visible');
		clearTimeout(toastTimer);
		toastTimer = setTimeout(function () { el.classList.remove('is-visible'); }, 3800);
	}
	window.dastaanToast = toast;

	/* -------------------------------------------------------------------
	 * Focus trap + drawers
	 * ---------------------------------------------------------------- */
	var FOCUSABLE = 'a[href],button:not([disabled]),input:not([disabled]),select:not([disabled]),textarea:not([disabled]),[tabindex]:not([tabindex="-1"])';
	var openDrawer = null;
	var lastFocus = null;

	function trapFocus(e, container) {
		if (e.key !== 'Tab') { return; }
		var items = $$(FOCUSABLE, container).filter(function (el) { return el.offsetParent !== null; });
		if (!items.length) { return; }
		var first = items[0], last = items[items.length - 1];
		if (e.shiftKey && doc.activeElement === first) { e.preventDefault(); last.focus(); }
		else if (!e.shiftKey && doc.activeElement === last) { e.preventDefault(); first.focus(); }
	}

	function setExpanded(id, state) {
		$$('[aria-controls="' + id + '"]').forEach(function (btn) { btn.setAttribute('aria-expanded', state ? 'true' : 'false'); });
	}

	function open(el) {
		if (!el) { return; }
		if (openDrawer && openDrawer !== el) { close(openDrawer); }
		lastFocus = doc.activeElement;
		el.removeAttribute('hidden');
		el.setAttribute('aria-hidden', 'false');
		setExpanded(el.id, true);
		body.classList.add('has-drawer-open');
		openDrawer = el;
		var focusTarget = $('input:not([type="hidden"]), button.js-close-drawer, button.js-close-search, .drawer__title', el);
		setTimeout(function () { if (focusTarget) { focusTarget.focus({ preventScroll: true }); } }, 60);
		el.dispatchEvent(new CustomEvent('dastaan:open', { bubbles: true }));
	}
	function close(el) {
		if (!el) { return; }
		el.setAttribute('aria-hidden', 'true');
		setExpanded(el.id, false);
		if (el.classList.contains('reel-modal') || el.classList.contains('lightbox')) { el.setAttribute('hidden', ''); }
		if (openDrawer === el) { openDrawer = null; }
		if (!openDrawer) { body.classList.remove('has-drawer-open'); }
		if (lastFocus && lastFocus.focus) { lastFocus.focus({ preventScroll: true }); }
		el.dispatchEvent(new CustomEvent('dastaan:close', { bubbles: true }));
	}
	window.dastaanOpen = open;
	window.dastaanClose = close;

	on(doc, 'click', function (e) {
		var t = e.target.closest('.hamburger, .js-open-search, .js-open-cart, .js-open-filters, .js-close-drawer, .js-close-search, .drawer__backdrop');
		if (!t) { return; }
		if (t.classList.contains('hamburger')) { e.preventDefault(); open($('#mobile-menu')); }
		else if (t.classList.contains('js-open-search')) { e.preventDefault(); open($('#search-overlay')); }
		else if (t.classList.contains('js-open-cart')) { e.preventDefault(); open($('#mini-cart-drawer')); }
		else if (t.classList.contains('js-open-filters')) { e.preventDefault(); open($('#shop-filters')); }
		else { e.preventDefault(); close(t.closest('.drawer, .search-overlay, .reel-modal, .lightbox') || openDrawer); }
	});
	on(doc, 'keydown', function (e) {
		if (e.key === 'Escape' && openDrawer) { close(openDrawer); }
		else if (openDrawer) { trapFocus(e, openDrawer); }
	});

	// Mobile submenu toggles.
	on(doc, 'click', function (e) {
		var btn = e.target.closest('.mobile-nav .submenu-toggle');
		if (!btn) { return; }
		e.preventDefault();
		var li = btn.closest('li');
		var isOpen = li.classList.toggle('is-open');
		btn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
	});

	// Desktop nav: keyboard access to submenus.
	$$('.primary-nav .menu-item-has-children > a').forEach(function (a) {
		on(a, 'keydown', function (e) {
			if (e.key === 'ArrowDown') { e.preventDefault(); var first = $('.sub-menu a, .mega-panel a', a.parentNode); if (first) { first.focus(); } }
		});
	});

	/* -------------------------------------------------------------------
	 * Sticky header behaviour
	 * ---------------------------------------------------------------- */
	var header = $('#masthead');
	if (header && header.getAttribute('data-sticky') === '1') {
		var lastY = window.pageYOffset;
		var ticking = false;
		var update = function () {
			var y = window.pageYOffset;
			header.classList.toggle('is-stuck', y > 10);
			if (window.innerWidth < 1200) {
				header.classList.toggle('is-hidden', y > 160 && y > lastY + 4 && !body.classList.contains('has-drawer-open'));
			} else {
				header.classList.remove('is-hidden');
			}
			lastY = y;
			ticking = false;
		};
		on(window, 'scroll', function () { if (!ticking) { requestAnimationFrame(update); ticking = true; } }, { passive: true });
		update();
	}

	// Set header height var for scroll-padding.
	var setHeaderVar = function () {
		if (header) { doc.documentElement.style.setProperty('--d-header-h', header.offsetHeight + 'px'); }
	};
	setHeaderVar();
	on(window, 'resize', setHeaderVar);

	/* -------------------------------------------------------------------
	 * Back to top + reveal on scroll
	 * ---------------------------------------------------------------- */
	var btt = $('.back-to-top');
	if (btt) {
		on(window, 'scroll', function () { btt.classList.toggle('is-visible', window.pageYOffset > 700); }, { passive: true });
		on(btt, 'click', function (e) { e.preventDefault(); window.scrollTo({ top: 0, behavior: reduceMotion ? 'auto' : 'smooth' }); });
	}
	if ('IntersectionObserver' in window) {
		var io = new IntersectionObserver(function (entries) {
			entries.forEach(function (en) { if (en.isIntersecting) { en.target.classList.add('is-revealed'); io.unobserve(en.target); } });
		}, { rootMargin: '0px 0px -8% 0px' });
		$$('[data-reveal]').forEach(function (el) { io.observe(el); });
	}

	/* -------------------------------------------------------------------
	 * Countdown
	 * ---------------------------------------------------------------- */
	$$('.countdown[data-end]').forEach(function (cd) {
		var end = parseInt(cd.getAttribute('data-end'), 10) * 1000;
		if (!end) { return; }
		var units = { d: $('[data-u="d"]', cd), h: $('[data-u="h"]', cd), m: $('[data-u="m"]', cd), s: $('[data-u="s"]', cd) };
		var pad = function (n) { return (n < 10 ? '0' : '') + n; };
		var tick = function () {
			var diff = Math.max(0, Math.floor((end - Date.now()) / 1000));
			if (units.d) { units.d.textContent = pad(Math.floor(diff / 86400)); }
			if (units.h) { units.h.textContent = pad(Math.floor((diff % 86400) / 3600)); }
			if (units.m) { units.m.textContent = pad(Math.floor((diff % 3600) / 60)); }
			if (units.s) { units.s.textContent = pad(diff % 60); }
			if (diff <= 0) { clearInterval(iv); var bar = cd.closest('.announcement-bar'); if (bar && bar.getAttribute('data-hide-on-end') === '1') { bar.style.display = 'none'; } }
		};
		var iv = setInterval(tick, 1000);
		tick();
	});

	/* -------------------------------------------------------------------
	 * Generic carousels ([data-carousel] with .carousel__track)
	 * ---------------------------------------------------------------- */
	$$('[data-carousel]').forEach(function (c) {
		var track = $('.carousel__track', c);
		if (!track) { return; }
		var prev = $('.carousel__arrow--prev', c), next = $('.carousel__arrow--next', c);
		var step = function () { var first = track.children[0]; return first ? first.getBoundingClientRect().width + 24 : track.clientWidth * 0.8; };
		var sync = function () {
			if (prev) { prev.disabled = track.scrollLeft <= 4; }
			if (next) { next.disabled = track.scrollLeft + track.clientWidth >= track.scrollWidth - 4; }
		};
		on(prev, 'click', function () { track.scrollBy({ left: -step(), behavior: 'smooth' }); });
		on(next, 'click', function () { track.scrollBy({ left: step(), behavior: 'smooth' }); });
		on(track, 'scroll', sync, { passive: true });
		on(window, 'resize', sync);
		sync();
	});

	/* -------------------------------------------------------------------
	 * Hero slider
	 * ---------------------------------------------------------------- */
	$$('.hero--multi').forEach(function (hero) {
		var slides = $$('.hero__slide', hero);
		var dots = $$('.hero__dot', hero);
		var i = Math.max(0, slides.findIndex(function (s) { return s.classList.contains('is-active'); }));
		var timer = null;
		var autoplay = hero.getAttribute('data-autoplay') === '1' && !reduceMotion;
		var interval = D.heroInterval || 6000;
		var go = function (n) {
			i = (n + slides.length) % slides.length;
			slides.forEach(function (s, k) { s.classList.toggle('is-active', k === i); s.setAttribute('aria-hidden', k === i ? 'false' : 'true'); });
			dots.forEach(function (d, k) { d.classList.toggle('is-active', k === i); d.setAttribute('aria-selected', k === i ? 'true' : 'false'); });
		};
		var start = function () { if (autoplay && slides.length > 1) { stop(); timer = setInterval(function () { go(i + 1); }, interval); } };
		var stop = function () { if (timer) { clearInterval(timer); timer = null; } };
		on($('.hero__arrow--prev', hero), 'click', function () { go(i - 1); start(); });
		on($('.hero__arrow--next', hero), 'click', function () { go(i + 1); start(); });
		dots.forEach(function (d, k) { on(d, 'click', function () { go(k); start(); }); });
		on(hero, 'mouseenter', stop);
		on(hero, 'mouseleave', start);
		on(hero, 'focusin', stop);
		on(hero, 'focusout', start);
		// Swipe.
		var x0 = null;
		on(hero, 'touchstart', function (e) { x0 = e.touches[0].clientX; }, { passive: true });
		on(hero, 'touchend', function (e) {
			if (x0 === null) { return; }
			var dx = e.changedTouches[0].clientX - x0;
			if (Math.abs(dx) > 40) { go(dx < 0 ? i + 1 : i - 1); start(); }
			x0 = null;
		}, { passive: true });
		on(doc, 'visibilitychange', function () { doc.hidden ? stop() : start(); });
		go(i);
		start();
	});

	/* -------------------------------------------------------------------
	 * Reels modal
	 * ---------------------------------------------------------------- */
	var reelModal = $('.reel-modal');
	if (reelModal) {
		var reelBody = $('.reel-modal__body', reelModal);
		var reelCaption = $('.reel-modal__caption', reelModal);
		var reelLink = $('.reel-modal__link', reelModal);
		on(doc, 'click', function (e) {
			var btn = e.target.closest('.js-reel');
			if (!btn) { return; }
			e.preventDefault();
			var src = btn.getAttribute('data-src'), embed = btn.getAttribute('data-embed');
			reelBody.innerHTML = '';
			if (src) {
				var v = doc.createElement('video');
				v.src = src; v.controls = true; v.autoplay = true; v.playsInline = true; v.loop = true;
				reelBody.appendChild(v);
			} else if (embed) {
				var f = doc.createElement('iframe');
				f.src = embed + (embed.indexOf('?') > -1 ? '&' : '?') + 'autoplay=1';
				f.allow = 'autoplay; fullscreen; picture-in-picture';
				f.setAttribute('allowfullscreen', '');
				f.setAttribute('loading', 'lazy');
				f.setAttribute('referrerpolicy', 'strict-origin-when-cross-origin');
				reelBody.appendChild(f);
			}
			if (reelCaption) { reelCaption.textContent = btn.getAttribute('data-caption') || ''; }
			if (reelLink) {
				var link = btn.getAttribute('data-link');
				reelLink.style.display = link ? '' : 'none';
				if (link) { reelLink.href = link; }
			}
			open(reelModal);
		});
		on(reelModal, 'dastaan:close', function () { reelBody.innerHTML = ''; });
		on(reelModal, 'click', function (e) { if (e.target === reelModal) { close(reelModal); } });
	}

	/* -------------------------------------------------------------------
	 * Newsletter (AJAX)
	 * ---------------------------------------------------------------- */
	$$('.js-newsletter').forEach(function (form) {
		on(form, 'submit', function (e) {
			e.preventDefault();
			var msg = $('.newsletter__msg', form);
			var btn = $('button[type="submit"]', form);
			var email = $('input[type="email"]', form);
			if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)) {
				if (msg) { msg.textContent = email && email.getAttribute('data-invalid') || 'Please enter a valid email address.'; msg.className = 'newsletter__msg is-error'; }
				return;
			}
			var fd = new FormData(form);
			fd.set('action', 'dastaan_subscribe');
			if (btn) { btn.classList.add('is-loading'); btn.disabled = true; }
			fetch(D.ajaxUrl, { method: 'POST', credentials: 'same-origin', body: fd })
				.then(function (r) { return r.json(); })
				.then(function (res) {
					var ok = res && res.success;
					if (msg) { msg.textContent = (res && res.data && res.data.message) || ''; msg.className = 'newsletter__msg ' + (ok ? 'is-success' : 'is-error'); }
					if (ok) { form.reset(); doc.dispatchEvent(new CustomEvent('dastaan:lead', { detail: { source: 'newsletter' } })); }
				})
				.catch(function () { if (msg) { msg.textContent = 'Something went wrong. Please try again.'; msg.className = 'newsletter__msg is-error'; } })
				.finally(function () { if (btn) { btn.classList.remove('is-loading'); btn.disabled = false; } });
		});
	});

	/* -------------------------------------------------------------------
	 * Consent
	 * ---------------------------------------------------------------- */
	var consentBox = $('.consent');
	var getCookie = function (name) { var m = doc.cookie.match('(?:^|; )' + name + '=([^;]*)'); return m ? decodeURIComponent(m[1]) : null; };
	if (consentBox) {
		if (!getCookie('dastaan_consent')) { consentBox.removeAttribute('hidden'); }
		$$('.js-consent', consentBox).forEach(function (b) {
			on(b, 'click', function () {
				var state = b.getAttribute('data-consent') === 'granted' ? 'granted' : 'denied';
				doc.cookie = 'dastaan_consent=' + state + '; path=/; max-age=' + (60 * 60 * 24 * 180) + '; SameSite=Lax' + (location.protocol === 'https:' ? '; Secure' : '');
				consentBox.setAttribute('hidden', '');
				doc.dispatchEvent(new CustomEvent('dastaan:consent', { detail: { state: state } }));
			});
		});
	}

	/* -------------------------------------------------------------------
	 * Copy coupon code pills (hero, banners)
	 * ---------------------------------------------------------------- */
	on(doc, 'click', function (e) {
		var btn = e.target.closest('.js-copy-code');
		if (!btn) { return; }
		e.preventDefault();
		var code = btn.getAttribute('data-code') || '';
		if (!code) { return; }
		var done = function () { toast(((D.i18n && D.i18n.codeCopied) || 'Code copied: %s').replace('%s', code)); };
		if (navigator.clipboard && navigator.clipboard.writeText) { navigator.clipboard.writeText(code).then(done, done); } else { done(); }
	});

	/* -------------------------------------------------------------------
	 * Share
	 * ---------------------------------------------------------------- */
	on(doc, 'click', function (e) {
		var btn = e.target.closest('.js-share');
		if (!btn) { return; }
		e.preventDefault();
		var data = { title: btn.getAttribute('data-title') || doc.title, url: btn.getAttribute('data-url') || location.href };
		if (navigator.share) { navigator.share(data).catch(function () {}); }
		else if (navigator.clipboard) { navigator.clipboard.writeText(data.url).then(function () { toast((D.i18n && D.i18n.copied) || 'Link copied'); }); }
	});

	/* -------------------------------------------------------------------
	 * Quantity +/- (works everywhere: product, cart)
	 * ---------------------------------------------------------------- */
	on(doc, 'click', function (e) {
		var btn = e.target.closest('.qty-btn');
		if (!btn) { return; }
		e.preventDefault();
		var input = $('input.qty', btn.parentNode);
		if (!input) { return; }
		var step = parseFloat(input.getAttribute('step')) || 1;
		var min = parseFloat(input.getAttribute('min')); if (isNaN(min)) { min = 0; }
		var max = parseFloat(input.getAttribute('max'));
		var val = parseFloat(input.value) || 0;
		val = btn.classList.contains('qty-btn--plus') ? val + step : val - step;
		if (val < min) { val = min; }
		if (!isNaN(max) && max > 0 && val > max) { val = max; }
		input.value = val;
		input.dispatchEvent(new Event('change', { bubbles: true }));
		input.dispatchEvent(new Event('input', { bubbles: true }));
		// Enable cart "update" button.
		var upd = $('button[name="update_cart"]');
		if (upd) { upd.disabled = false; }
	});

	/* -------------------------------------------------------------------
	 * Shop: grid density switch
	 * ---------------------------------------------------------------- */
	$$('.grid-switch__btn').forEach(function (b) {
		on(b, 'click', function () {
			$$('.grid-switch__btn').forEach(function (x) { x.classList.remove('is-active'); });
			b.classList.add('is-active');
			var grid = $('.shop-body ul.products');
			if (grid) { grid.classList.toggle('is-dense', b.getAttribute('data-cols') === 'dense'); }
			try { localStorage.setItem('dastaan_grid', b.getAttribute('data-cols')); } catch (err) {}
		});
	});
	try {
		var savedGrid = localStorage.getItem('dastaan_grid');
		if (savedGrid === 'dense') { var gb = $('.grid-switch__btn[data-cols="dense"]'); if (gb) { gb.click(); } }
	} catch (err) {}

	/* -------------------------------------------------------------------
	 * WooCommerce: open drawer + toast after AJAX add to cart (loop buttons),
	 * keep cart count fresh after fragment refresh.
	 * ---------------------------------------------------------------- */
	if (window.jQuery) {
		window.jQuery(doc.body).on('added_to_cart', function (e, fragments, hash, button) {
			if (fragments) { applyFragments(fragments); }
			open($('#mini-cart-drawer'));
			toast((D.i18n && D.i18n.addedToCart) || 'Added to your bag');
		});
		window.jQuery(doc.body).on('wc_fragments_refreshed wc_fragments_loaded removed_from_cart', function () {
			var c = $('.header-cart-count');
			$$('.header-cart-count').forEach(function (el) { if (c && el !== c) { el.textContent = c.textContent; el.classList.toggle('has-items', c.classList.contains('has-items')); } });
		});
	}
	function applyFragments(fragments) {
		Object.keys(fragments).forEach(function (sel) {
			$$(sel).forEach(function (el) {
				var tmp = doc.createElement('div');
				tmp.innerHTML = fragments[sel];
				if (tmp.firstElementChild) { el.replaceWith(tmp.firstElementChild.cloneNode(true)); }
			});
		});
	}
	window.dastaanApplyFragments = applyFragments;

	/* -------------------------------------------------------------------
	 * Search overlay: focus input on open
	 * ---------------------------------------------------------------- */
	var so = $('#search-overlay');
	on(so, 'dastaan:open', function () { var inp = $('input[type="search"]', so); if (inp) { setTimeout(function () { inp.focus(); }, 80); } });

	/* -------------------------------------------------------------------
	 * External links: safety
	 * ---------------------------------------------------------------- */
	$$('a[target="_blank"]').forEach(function (a) { if (!/noopener/.test(a.rel)) { a.rel = (a.rel ? a.rel + ' ' : '') + 'noopener'; } });
})();
