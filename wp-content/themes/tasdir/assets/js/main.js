/* Tasdir Export — tiny vanilla JS (no jQuery). */
(function () {
	'use strict';

	var cfg = window.TASDIR || {};
	var track = function (name, params) {
		if (typeof window.tasdirTrack === 'function') {
			window.tasdirTrack(name, params || {});
		}
	};

	/* ---------- Mobile navigation ---------- */
	var nav = document.getElementById('primary-nav');
	var backdrop = document.querySelector('.nav-backdrop');
	var openBtn = document.querySelector('.nav-toggle:not(.nav-close)');

	function setNav(open) {
		if (!nav) { return; }
		nav.classList.toggle('is-open', open);
		document.body.classList.toggle('nav-locked', open);
		if (backdrop) {
			backdrop.hidden = false;
			backdrop.classList.toggle('is-open', open);
		}
		if (openBtn) { openBtn.setAttribute('aria-expanded', open ? 'true' : 'false'); }
		if (open) {
			var first = nav.querySelector('a');
			if (first) { first.focus(); }
		} else if (openBtn) {
			openBtn.focus({ preventScroll: true });
		}
	}

	document.querySelectorAll('.nav-toggle').forEach(function (btn) {
		btn.addEventListener('click', function () {
			setNav(!nav.classList.contains('is-open'));
		});
	});
	if (backdrop) { backdrop.addEventListener('click', function () { setNav(false); }); }
	document.addEventListener('keydown', function (e) {
		if (e.key === 'Escape' && nav && nav.classList.contains('is-open')) { setNav(false); }
	});

	/* ---------- In-page anchors (scroll if the target exists on this page) ---------- */
	document.addEventListener('click', function (e) {
		var a = e.target.closest('a[href*="#"]');
		if (!a) { return; }
		var url;
		try { url = new URL(a.href, location.href); } catch (err) { return; }
		if (!url.hash || url.hash.length < 2) { return; }
		var target = document.getElementById(url.hash.slice(1));
		if (!target) { return; }
		e.preventDefault();
		if (nav && nav.classList.contains('is-open')) { setNav(false); }
		target.scrollIntoView({ behavior: 'smooth', block: 'start' });
		if (url.hash === '#enquiry') {
			var field = target.querySelector('input[name="name"]');
			if (field) { setTimeout(function () { field.focus({ preventScroll: true }); }, 500); }
		}
		history.replaceState(null, '', url.hash);
	});

	/* ---------- Conversion tracking on contact links ---------- */
	document.addEventListener('click', function (e) {
		var a = e.target.closest('a[href]');
		if (!a) { return; }
		var href = a.getAttribute('href') || '';
		var where = a.className || 'link';
		if (/wa\.me|api\.whatsapp\.com|whatsapp:\/\//.test(href)) {
			track('whatsapp_click', { link_location: where, content_name: document.title });
		} else if (href.indexOf('tel:') === 0) {
			track('call_click', { link_location: where });
		} else if (href.indexOf('mailto:') === 0) {
			track('email_click', { link_location: where });
		}
	}, true);

	/* ---------- First-touch campaign source (UTM / click IDs / referrer) ---------- */
	var source = '';
	try {
		var params = new URLSearchParams(location.search);
		var parts = [];
		['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content'].forEach(function (k) {
			if (params.get(k)) { parts.push(k.replace('utm_', '') + '=' + params.get(k)); }
		});
		if (params.get('gclid')) { parts.push('gclid'); }
		if (params.get('fbclid')) { parts.push('fbclid'); }
		if (!parts.length && document.referrer && document.referrer.indexOf(location.host) === -1) {
			parts.push('ref=' + new URL(document.referrer).host);
		}
		var stored = sessionStorage.getItem('tasdir_src');
		if (parts.length && !stored) {
			stored = parts.join(' | ');
			sessionStorage.setItem('tasdir_src', stored);
		}
		source = stored || 'direct';
	} catch (err) {
		source = '';
	}

	/* ---------- Enquiry form → save lead → open WhatsApp ---------- */
	var labels = { name: 'Name', company: 'Company', country: 'Country', product: 'Product', quantity: 'Quantity', email: 'Email', message: 'Message' };

	function clientWaUrl(form) {
		if (!cfg.waNumber) { return ''; }
		var lines = ['Hello Tasdir, I would like a quotation.', ''];
		Object.keys(labels).forEach(function (k) {
			var el = form.elements[k];
			if (el && el.value.trim()) { lines.push('*' + labels[k] + ':* ' + el.value.trim()); }
		});
		return 'https://wa.me/' + cfg.waNumber + '?text=' + encodeURIComponent(lines.join('\n'));
	}

	document.querySelectorAll('.lead-form').forEach(function (form) {
		var status = form.querySelector('.form-status');
		var btn = form.querySelector('[type="submit"]');
		form.elements.page.value = location.href.split('#')[0];
		form.elements.source.value = source;
		form.elements.tasdir_t.value = String(Date.now());

		function show(msg, ok) {
			if (!status) { return; }
			status.textContent = msg;
			status.className = 'form-status ' + (ok ? 'is-ok' : 'is-err');
		}

		form.addEventListener('submit', function (e) {
			e.preventDefault();
			var name = form.elements.name.value.trim();
			var digits = form.elements.phone.value.replace(/\D+/g, '');
			if (!name || digits.length < 7 || digits.length > 16) {
				show(cfg.i18n.invalid, false);
				(name ? form.elements.phone : form.elements.name).focus();
				return;
			}

			btn.setAttribute('aria-busy', 'true');
			show(cfg.i18n.sending, true);

			var data = new FormData(form);
			data.set('action', 'tasdir_lead');
			var fallbackWa = clientWaUrl(form);
			var controller = 'AbortController' in window ? new AbortController() : null;
			var timer = setTimeout(function () { if (controller) { controller.abort(); } }, 6000);

			fetch(cfg.ajax, { method: 'POST', body: data, credentials: 'same-origin', signal: controller ? controller.signal : undefined })
				.then(function (r) { return r.json().catch(function () { return { success: false }; }); })
				.then(function (res) {
					clearTimeout(timer);
					if (res && res.success) {
						track('generate_lead', {
							content_name: form.elements.product ? form.elements.product.value : '',
							method: 'enquiry_form'
						});
						var wa = (res.data && res.data.wa) || fallbackWa;
						if (wa) {
							show(cfg.i18n.ok, true);
							setTimeout(function () { window.location.href = wa; }, 400);
						} else {
							show(cfg.i18n.okNoWa, true);
							form.reset();
						}
					} else if (fallbackWa && !(res && res.data && res.data.message)) {
						window.location.href = fallbackWa;
					} else {
						show((res && res.data && res.data.message) || cfg.i18n.err, false);
					}
				})
				.catch(function () {
					clearTimeout(timer);
					// Network problem: never lose the lead — hand it to WhatsApp directly.
					if (fallbackWa) {
						track('generate_lead', { method: 'whatsapp_fallback' });
						window.location.href = fallbackWa;
					} else {
						show(cfg.i18n.err, false);
					}
				})
				.then(function () { btn.removeAttribute('aria-busy'); });
		});
	});
}());
