/*!
 * Dastaan by Artisans — product.js
 * Gallery (images + video), lightbox, AJAX add-to-bag, sticky bar, variation image sync.
 */
(function () {
	'use strict';

	var D = window.dastaanData || {};
	var doc = document;
	var $ = function (sel, ctx) { return (ctx || doc).querySelector(sel); };
	var $$ = function (sel, ctx) { return Array.prototype.slice.call((ctx || doc).querySelectorAll(sel)); };
	var on = function (el, ev, fn, opts) { if (el) { el.addEventListener(ev, fn, opts || false); } };

	/* -------------------------------------------------------------------
	 * Gallery
	 * ---------------------------------------------------------------- */
	var gallery = $('.d-gallery');
	var lightbox = $('.lightbox');
	var slides = gallery ? $$('.d-gallery__slide', gallery) : [];
	var thumbs = gallery ? $$('.d-gallery__thumb', gallery) : [];
	var current = 0;
	var autoplay = gallery && gallery.getAttribute('data-autoplay') === '1';

	function pauseAllVideos(except) {
		$$('video', gallery).forEach(function (v) {
			if (v !== except) { v.pause(); var s = v.closest('.d-gallery__slide'); if (s) { s.classList.remove('is-playing'); } }
		});
		$$('.d-gallery__slide--video iframe', gallery).forEach(function (f) { if (!except || f.closest('.d-gallery__slide') !== except.closest('.d-gallery__slide')) { f.remove(); var poster = f.parentNode && $('.js-embed-load', f.parentNode); if (poster) { poster.style.display = ''; } } });
	}

	function show(n, fromUser) {
		if (!slides.length) { return; }
		current = (n + slides.length) % slides.length;
		slides.forEach(function (s, k) { s.classList.toggle('is-active', k === current); });
		thumbs.forEach(function (t, k) {
			t.classList.toggle('is-active', k === current);
			t.setAttribute('aria-selected', k === current ? 'true' : 'false');
			if (k === current && t.scrollIntoView && fromUser) { t.scrollIntoView({ block: 'nearest', inline: 'nearest', behavior: 'smooth' }); }
		});
		var counter = $('.d-gallery__current', gallery);
		if (counter) { counter.textContent = current + 1; }
		var active = slides[current];
		var vid = $('video', active);
		pauseAllVideos(vid);
		if (vid && autoplay) { vid.muted = true; var p = vid.play(); if (p && p.catch) { p.catch(function () {}); } active.classList.add('is-playing'); }
	}

	if (gallery) {
		thumbs.forEach(function (t) { on(t, 'click', function () { show(parseInt(t.getAttribute('data-index'), 10), true); }); });
		on($('.d-gallery__arrow--prev', gallery), 'click', function () { show(current - 1, true); });
		on($('.d-gallery__arrow--next', gallery), 'click', function () { show(current + 1, true); });
		on(gallery, 'keydown', function (e) { if (e.key === 'ArrowLeft') { show(current - 1, true); } if (e.key === 'ArrowRight') { show(current + 1, true); } });

		// Swipe on stage.
		var stage = $('.d-gallery__stage', gallery), x0 = null, y0 = null;
		on(stage, 'touchstart', function (e) { x0 = e.touches[0].clientX; y0 = e.touches[0].clientY; }, { passive: true });
		on(stage, 'touchend', function (e) {
			if (x0 === null) { return; }
			var dx = e.changedTouches[0].clientX - x0, dy = e.changedTouches[0].clientY - y0;
			if (Math.abs(dx) > 40 && Math.abs(dx) > Math.abs(dy)) { show(dx < 0 ? current + 1 : current - 1, true); }
			x0 = y0 = null;
		}, { passive: true });

		// Video controls.
		on(gallery, 'click', function (e) {
			var toggle = e.target.closest('.js-video-toggle');
			var mute = e.target.closest('.js-video-mute');
			var embed = e.target.closest('.js-embed-load');
			if (toggle) {
				var s = toggle.closest('.d-gallery__slide'), v = $('video', s);
				if (!v) { return; }
				if (v.paused) { v.play().catch(function () {}); s.classList.add('is-playing'); } else { v.pause(); s.classList.remove('is-playing'); }
			} else if (mute) {
				var v2 = $('video', mute.closest('.d-gallery__slide'));
				if (!v2) { return; }
				v2.muted = !v2.muted;
				mute.setAttribute('aria-pressed', v2.muted ? 'true' : 'false');
				mute.setAttribute('aria-label', v2.muted ? 'Unmute' : 'Mute');
			} else if (embed) {
				var slide = embed.closest('.d-gallery__slide');
				var url = slide.getAttribute('data-embed');
				if (!url) { return; }
				var f = doc.createElement('iframe');
				f.src = url + (url.indexOf('?') > -1 ? '&' : '?') + 'autoplay=1';
				f.allow = 'autoplay; fullscreen; picture-in-picture';
				f.setAttribute('allowfullscreen', '');
				f.setAttribute('referrerpolicy', 'strict-origin-when-cross-origin');
				f.setAttribute('title', 'Product video');
				embed.style.display = 'none';
				slide.appendChild(f);
			}
		});

		// Pause when gallery scrolls out of view.
		if ('IntersectionObserver' in window) {
			var io = new IntersectionObserver(function (entries) {
				entries.forEach(function (en) { if (!en.isIntersecting) { pauseAllVideos(null); } else if (autoplay) { show(current); } });
			}, { threshold: 0.2 });
			io.observe(gallery);
		}

		// Variation image swap: WooCommerce sets the first .woocommerce-product-gallery__image img src via wc_variations_image_update.
		// We mirror it into the first thumb and the lightbox dataset.
		if (window.jQuery) {
			window.jQuery('.variations_form').on('found_variation', function (e, variation) {
				var first = slides.find(function (s) { return s.getAttribute('data-type') === 'image'; });
				if (!first || !variation || !variation.image || !variation.image.src) { return; }
				var img = $('img', first), link = $('a', first);
				if (img) {
					img.src = variation.image.src;
					if (variation.image.srcset) { img.srcset = variation.image.srcset; } else { img.removeAttribute('srcset'); }
					img.alt = variation.image.alt || img.alt;
					img.setAttribute('data-large_image', variation.image.full_src || variation.image.src);
				}
				if (link) { link.href = variation.image.full_src || variation.image.src; link.setAttribute('data-full', link.href); }
				var tIdx = slides.indexOf(first), t = thumbs[tIdx] && $('img', thumbs[tIdx]);
				if (t) { t.src = variation.image.gallery_thumbnail_src || variation.image.thumb_src || variation.image.src; }
				show(tIdx);
			}).on('reset_image', function () {
				// Restore originals from data attributes set at load.
				slides.forEach(function (s, k) {
					var img = $('img', s);
					if (img && img.getAttribute('data-o-src')) { img.src = img.getAttribute('data-o-src'); img.srcset = img.getAttribute('data-o-srcset') || ''; var l = $('a', s); if (l) { l.href = img.getAttribute('data-large_image'); } }
					var t = thumbs[k] && $('img', thumbs[k]);
					if (t && t.getAttribute('data-o-src')) { t.src = t.getAttribute('data-o-src'); }
				});
			});
			slides.forEach(function (s, k) {
				var img = $('img', s);
				if (img) { img.setAttribute('data-o-src', img.src); img.setAttribute('data-o-srcset', img.srcset || ''); }
				var t = thumbs[k] && $('img', thumbs[k]);
				if (t) { t.setAttribute('data-o-src', t.src); }
			});
		}

		show(0);
	}

	/* -------------------------------------------------------------------
	 * Lightbox
	 * ---------------------------------------------------------------- */
	if (gallery && lightbox) {
		var lbBody = $('.lightbox__body', lightbox);
		var lbCounter = $('.lightbox__counter', lightbox);
		var lbIndex = 0;

		function renderLightbox(n) {
			lbIndex = (n + slides.length) % slides.length;
			var s = slides[lbIndex];
			lbBody.innerHTML = '';
			if (s.getAttribute('data-type') === 'video') {
				var src = s.getAttribute('data-src'), embed = s.getAttribute('data-embed');
				if (src) {
					var v = doc.createElement('video'); v.src = src; v.controls = true; v.autoplay = true; v.playsInline = true; v.loop = true;
					if (s.getAttribute('data-poster')) { v.poster = s.getAttribute('data-poster'); }
					lbBody.appendChild(v);
				} else if (embed) {
					var f = doc.createElement('iframe'); f.src = embed + (embed.indexOf('?') > -1 ? '&' : '?') + 'autoplay=1'; f.allow = 'autoplay; fullscreen'; f.setAttribute('allowfullscreen', ''); f.setAttribute('title', 'Product video');
					lbBody.appendChild(f);
				}
			} else {
				var img = doc.createElement('img');
				var srcImg = $('img', s);
				img.src = (srcImg && srcImg.getAttribute('data-large_image')) || ($('a', s) && $('a', s).href);
				img.alt = srcImg ? srcImg.alt : '';
				on(img, 'click', function () { img.classList.toggle('is-zoomed'); });
				lbBody.appendChild(img);
			}
			if (lbCounter) { lbCounter.textContent = (lbIndex + 1) + ' / ' + slides.length; }
		}
		function openLightbox(n) { pauseAllVideos(null); renderLightbox(n); window.dastaanOpen(lightbox); }

		on(gallery, 'click', function (e) {
			var link = e.target.closest('.d-gallery__zoom-link');
			var expand = e.target.closest('.d-gallery__expand');
			if (link) { e.preventDefault(); openLightbox(slides.indexOf(link.closest('.d-gallery__slide'))); }
			else if (expand) { e.preventDefault(); openLightbox(current); }
		});
		on($('.lightbox__arrow--prev', lightbox), 'click', function () { renderLightbox(lbIndex - 1); });
		on($('.lightbox__arrow--next', lightbox), 'click', function () { renderLightbox(lbIndex + 1); });
		on($('.lightbox__close', lightbox), 'click', function () { window.dastaanClose(lightbox); });
		on(lightbox, 'click', function (e) { if (e.target === lightbox || e.target === lbBody) { window.dastaanClose(lightbox); } });
		on(doc, 'keydown', function (e) {
			if (lightbox.hasAttribute('hidden')) { return; }
			if (e.key === 'ArrowLeft') { renderLightbox(lbIndex - 1); }
			if (e.key === 'ArrowRight') { renderLightbox(lbIndex + 1); }
		});
		on(lightbox, 'dastaan:close', function () { lbBody.innerHTML = ''; });
	}

	/* -------------------------------------------------------------------
	 * AJAX add to bag (simple + variable), Buy now passes through normally.
	 * ---------------------------------------------------------------- */
	var form = $('form.cart');
	if (form && D.ajaxUrl && !form.classList.contains('grouped_form')) {
		var submitter = null;
		$$('button[type="submit"]', form).forEach(function (b) { on(b, 'click', function () { submitter = b; }); });
		on(form, 'submit', function (e) {
			var btn = submitter || $('.single_add_to_cart_button', form);
			if (!btn || btn.name === 'dastaan_buy_now' || btn.classList.contains('disabled')) { return; }
			var pid = $('[name="add-to-cart"]', form) || $('[name="product_id"]', form);
			if (!pid) { return; }
			e.preventDefault();
			var fd = new FormData(form);
			fd.set('action', 'dastaan_add_to_cart');
			fd.set('nonce', D.nonce);
			fd.set('product_id', pid.value);
			if (!fd.get('quantity')) { fd.set('quantity', 1); }
			btn.classList.add('is-loading');
			btn.disabled = true;
			fetch(D.ajaxUrl, { method: 'POST', credentials: 'same-origin', body: fd })
				.then(function (r) { return r.json(); })
				.then(function (res) {
					if (res && res.success) {
						if (res.data.fragments && window.dastaanApplyFragments) { window.dastaanApplyFragments(res.data.fragments); }
						btn.classList.add('is-added');
						setTimeout(function () { btn.classList.remove('is-added'); }, 2000);
						window.dastaanOpen($('#mini-cart-drawer'));
						if (window.dastaanToast) { window.dastaanToast((D.i18n && D.i18n.addedToCart) || 'Added to your bag', { href: D.cartUrl, label: (D.i18n && D.i18n.viewBag) || 'View bag' }); }
						doc.dispatchEvent(new CustomEvent('dastaan:added_to_cart', { detail: res.data.item || {} }));
						if (window.jQuery) { window.jQuery(doc.body).trigger('wc_fragment_refresh'); }
					} else {
						var m = (res && res.data && res.data.message) || 'Could not add to bag.';
						if (window.dastaanToast) { window.dastaanToast(m); } else { window.alert(m); }
					}
				})
				.catch(function () { form.submit(); /* native submit() does not re-trigger this listener */ })
				.finally(function () { btn.classList.remove('is-loading'); btn.disabled = false; submitter = null; });
		});
	}

	/* -------------------------------------------------------------------
	 * Sticky add-to-bag bar
	 * ---------------------------------------------------------------- */
	var sticky = $('.sticky-atc');
	if (sticky && form) {
		var atcBtn = $('.single_add_to_cart_button', form);
		if ('IntersectionObserver' in window && atcBtn) {
			var sio = new IntersectionObserver(function (entries) {
				var visible = entries[0].isIntersecting;
				var pastForm = entries[0].boundingClientRect.top < 0;
				sticky.classList.toggle('is-visible', !visible && pastForm);
				sticky.setAttribute('aria-hidden', (!visible && pastForm) ? 'false' : 'true');
				doc.body.classList.toggle('has-sticky-atc', !visible && pastForm);
			}, { threshold: 0 });
			sio.observe(atcBtn);
		}
		on($('.js-sticky-atc', sticky), 'click', function () {
			if (form.classList.contains('variations_form') && !$('input[name="variation_id"]', form).value) {
				form.scrollIntoView({ behavior: 'smooth', block: 'center' });
				return;
			}
			if (atcBtn) { atcBtn.click(); }
		});
	}

	// Buy now mirrors the add-to-bag state for variable products (no variation chosen → disabled).
	if (window.jQuery && form && form.classList.contains('variations_form')) {
		var buyNow = $('.btn--buynow', form);
		if (buyNow) {
			buyNow.disabled = true;
			window.jQuery(form).on('show_variation', function (e, variation, purchasable) { buyNow.disabled = !purchasable; })
				.on('hide_variation reset_data', function () { buyNow.disabled = true; });
		}
	}

	// Sync sticky price with variation.
	if (window.jQuery && sticky) {
		window.jQuery('.variations_form').on('found_variation', function (e, variation) {
			var p = $('.sticky-atc__price', sticky);
			if (p && variation.price_html) { p.innerHTML = variation.price_html; }
		});
	}
})();
