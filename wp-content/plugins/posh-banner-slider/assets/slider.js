(function () {
	function init(root) {
		var slides = root.querySelectorAll('.posh-bs__slide');
		var dots = root.querySelectorAll('.posh-bs__dot');
		if (slides.length < 2) return;
		var interval = parseInt(root.getAttribute('data-interval'), 10) || 5000;
		var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
		var current = 0, timer = null, paused = false;
		root.style.setProperty('--dur', interval + 'ms');

		function go(n) {
			slides[current].classList.remove('is-active');
			slides[current].setAttribute('aria-hidden', 'true');
			if (dots[current]) dots[current].classList.remove('is-active');
			current = (n + slides.length) % slides.length;
			slides[current].classList.add('is-active');
			slides[current].removeAttribute('aria-hidden');
			if (dots[current]) {
				// restart progress animation
				var bar = dots[current].firstElementChild;
				bar.style.animation = 'none'; void bar.offsetWidth; bar.style.animation = '';
				dots[current].classList.add('is-active');
			}
			schedule();
		}
		function schedule() {
			clearTimeout(timer);
			if (!paused && !reduce) timer = setTimeout(function () { go(current + 1); }, interval);
		}
		function pause(p) { paused = p; root.classList.toggle('is-paused', p); if (p) clearTimeout(timer); else schedule(); }

		root.querySelector('.posh-bs__prev').addEventListener('click', function () { go(current - 1); });
		root.querySelector('.posh-bs__next').addEventListener('click', function () { go(current + 1); });
		dots.forEach(function (d, i) { d.addEventListener('click', function () { go(i); }); });
		root.addEventListener('mouseenter', function () { pause(true); });
		root.addEventListener('mouseleave', function () { pause(false); });
		root.addEventListener('focusin', function () { pause(true); });
		root.addEventListener('focusout', function () { pause(false); });
		document.addEventListener('visibilitychange', function () { pause(document.hidden); });

		var x0 = null;
		root.addEventListener('touchstart', function (e) { x0 = e.touches[0].clientX; }, { passive: true });
		root.addEventListener('touchend', function (e) {
			if (x0 === null) return;
			var dx = e.changedTouches[0].clientX - x0;
			if (Math.abs(dx) > 40) go(current + (dx < 0 ? 1 : -1));
			x0 = null;
		});
		root.addEventListener('keydown', function (e) {
			if (e.key === 'ArrowLeft') go(current - 1);
			if (e.key === 'ArrowRight') go(current + 1);
		});
		schedule();
	}
	function boot() { document.querySelectorAll('.posh-bs').forEach(init); }
	if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', boot); else boot();
})();
