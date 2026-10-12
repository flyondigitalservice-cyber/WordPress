/*!
 * Dastaan by Artisans — tracking.js
 * Meta Pixel + GA4 + Microsoft Clarity with optional consent gating. Config in window.dastaanTracking.
 */
(function () {
	'use strict';

	var T = window.dastaanTracking || {};
	var doc = document;
	var loaded = { pixel: false, ga4: false, clarity: false };
	var queue = [];

	function getCookie(name) { var m = doc.cookie.match('(?:^|; )' + name + '=([^;]*)'); return m ? decodeURIComponent(m[1]) : null; }
	function consentGranted() { return !T.consent || getCookie('dastaan_consent') === 'granted'; }

	/* ---- loaders ---------------------------------------------------- */
	function loadPixel() {
		if (loaded.pixel || !T.pixel) { return; }
		loaded.pixel = true;
		/* Official Meta Pixel base code (unmodified semantics) */
		!function (f, b, e, v, n, t, s) { if (f.fbq) return; n = f.fbq = function () { n.callMethod ? n.callMethod.apply(n, arguments) : n.queue.push(arguments); }; if (!f._fbq) f._fbq = n; n.push = n; n.loaded = !0; n.version = '2.0'; n.queue = []; t = b.createElement(e); t.async = !0; t.src = v; s = b.getElementsByTagName(e)[0]; s.parentNode.insertBefore(t, s); }(window, doc, 'script', 'https://connect.facebook.net/en_US/fbevents.js');
		window.fbq('init', String(T.pixel));
		window.fbq('track', 'PageView');
	}
	function loadGA4() {
		if (loaded.ga4 || !T.ga4) { return; }
		loaded.ga4 = true;
		window.dataLayer = window.dataLayer || [];
		window.gtag = window.gtag || function () { window.dataLayer.push(arguments); };
		window.gtag('js', new Date());
		window.gtag('config', String(T.ga4), { anonymize_ip: true });
		var s = doc.createElement('script'); s.async = true; s.src = 'https://www.googletagmanager.com/gtag/js?id=' + encodeURIComponent(T.ga4);
		doc.head.appendChild(s);
	}
	function loadClarity() {
		if (loaded.clarity || !T.clarity) { return; }
		loaded.clarity = true;
		/* Official Microsoft Clarity snippet */
		(function (c, l, a, r, i, t, y) { c[a] = c[a] || function () { (c[a].q = c[a].q || []).push(arguments); }; t = l.createElement(r); t.async = 1; t.src = 'https://www.clarity.ms/tag/' + i; y = l.getElementsByTagName(r)[0]; y.parentNode.insertBefore(t, y); })(window, doc, 'clarity', 'script', String(T.clarity));
	}
	function loadAll() {
		loadPixel(); loadGA4(); loadClarity();
		while (queue.length) { var ev = queue.shift(); send(ev[0], ev[1]); }
	}

	/* ---- event mapping --------------------------------------------- */
	function ga4Items(items) {
		return (items || []).map(function (i) { return { item_id: i.id, item_name: i.name, price: i.price, quantity: i.quantity || 1, item_category: i.category || '' }; });
	}
	function send(name, data) {
		if (!consentGranted()) { queue.push([name, data]); return; }
		data = data || {};
		var cur = T.currency || 'INR';
		var ids = (data.items || []).map(function (i) { return i.id; });
		var contents = (data.items || []).map(function (i) { return { id: i.id, quantity: i.quantity || 1, item_price: i.price }; });

		if (window.fbq) {
			switch (name) {
				case 'viewContent': window.fbq('track', 'ViewContent', { content_ids: ids, content_type: 'product', content_name: data.items && data.items[0] ? data.items[0].name : '', value: data.value, currency: cur, contents: contents }); break;
				case 'viewCategory': window.fbq('trackCustom', 'ViewCategory', { content_category: data.name }); break;
				case 'search': window.fbq('track', 'Search', { search_string: data.query }); break;
				case 'addToCart': window.fbq('track', 'AddToCart', { content_ids: ids, content_type: 'product', content_name: data.items && data.items[0] ? data.items[0].name : '', value: data.value, currency: cur, contents: contents }); break;
				case 'initiateCheckout': window.fbq('track', 'InitiateCheckout', { content_ids: ids, num_items: data.num_items, value: data.value, currency: cur, contents: contents }); break;
				case 'purchase': window.fbq('track', 'Purchase', { content_ids: ids, content_type: 'product', value: data.value, currency: cur, contents: contents, order_id: data.transaction_id }); break;
				case 'lead': window.fbq('track', 'Lead', { content_name: data.source || 'newsletter' }); break;
				case 'contact': window.fbq('track', 'Contact', { content_name: data.source || 'whatsapp' }); break;
			}
		}
		if (window.gtag) {
			switch (name) {
				case 'viewContent': window.gtag('event', 'view_item', { currency: cur, value: data.value, items: ga4Items(data.items) }); break;
				case 'viewCategory': window.gtag('event', 'view_item_list', { item_list_name: data.name }); break;
				case 'search': window.gtag('event', 'search', { search_term: data.query }); break;
				case 'addToCart': window.gtag('event', 'add_to_cart', { currency: cur, value: data.value, items: ga4Items(data.items) }); break;
				case 'initiateCheckout': window.gtag('event', 'begin_checkout', { currency: cur, value: data.value, items: ga4Items(data.items) }); break;
				case 'purchase': window.gtag('event', 'purchase', { transaction_id: data.transaction_id, currency: cur, value: data.value, shipping: data.shipping, tax: data.tax, coupon: data.coupon, items: ga4Items(data.items) }); break;
				case 'lead': window.gtag('event', 'generate_lead', { source: data.source || 'newsletter' }); break;
				case 'contact': window.gtag('event', 'contact', { method: data.source || 'whatsapp' }); break;
			}
		}
		if (window.clarity && (name === 'purchase' || name === 'addToCart' || name === 'initiateCheckout')) {
			try { window.clarity('event', name); if (name === 'purchase') { window.clarity('set', 'order_value', String(data.value)); } } catch (e) {}
		}
	}
	window.dastaanTrack = send;

	/* ---- boot ------------------------------------------------------- */
	function boot() {
		if (consentGranted()) { loadAll(); }
		Object.keys(T.events || {}).forEach(function (k) { send(k, T.events[k]); });
	}
	doc.addEventListener('dastaan:consent', function (e) {
		if (e.detail && e.detail.state === 'granted') { loadAll(); }
		else { queue = []; }
	});
	if (doc.readyState === 'loading') { doc.addEventListener('DOMContentLoaded', boot); } else { boot(); }

	/* ---- runtime events --------------------------------------------- */
	// Theme AJAX add to cart (single product).
	doc.addEventListener('dastaan:added_to_cart', function (e) {
		var it = e.detail || {};
		if (!it.name) { return; }
		send('addToCart', { items: [{ id: it.sku, name: it.name, price: parseFloat(it.price) || 0, quantity: parseInt(it.qty, 10) || 1 }], value: (parseFloat(it.price) || 0) * (parseInt(it.qty, 10) || 1) });
	});
	// WooCommerce loop AJAX add to cart.
	if (window.jQuery) {
		window.jQuery(doc.body).on('added_to_cart', function (e, fragments, hash, button) {
			var b = button && button[0] ? button[0] : null;
			if (!b || !b.getAttribute('data-track-name')) { return; }
			var price = parseFloat(b.getAttribute('data-track-price')) || 0;
			var qty = parseInt(b.getAttribute('data-quantity'), 10) || 1;
			send('addToCart', { items: [{ id: b.getAttribute('data-track-sku'), name: b.getAttribute('data-track-name'), price: price, quantity: qty }], value: price * qty });
		});
	}
	// Newsletter + WhatsApp.
	doc.addEventListener('dastaan:lead', function (e) { send('lead', e.detail || {}); });
	doc.addEventListener('click', function (e) {
		var a = e.target.closest('a[href*="wa.me"], a[href*="whatsapp.com"], a[href^="tel:"]');
		if (a) { send('contact', { source: a.href.indexOf('tel:') === 0 ? 'phone' : 'whatsapp' }); }
	});
})();
