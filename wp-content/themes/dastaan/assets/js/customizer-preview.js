/*!
 * Dastaan — Customizer live preview
 */
(function (api) {
	'use strict';
	if (!api) { return; }
	var text = function (setting, selector) {
		api(setting, function (v) { v.bind(function (val) { document.querySelectorAll(selector).forEach(function (el) { el.textContent = val; }); }); });
	};
	text('blogname', '.site-title .wordmark');
	text('dastaan_announcement_text', '.announcement-bar__text > span:first-child');
	text('dastaan_announcement_secondary', '.announcement-bar__secondary');
	text('dastaan_footer_about', '.footer-about');
	api('dastaan_footer_copyright', function (v) { v.bind(function (val) { var el = document.querySelector('.copyright'); if (el) { el.textContent = val.replace('{year}', new Date().getFullYear()); } }); });
	var colors = { dastaan_color_primary: '--d-primary', dastaan_color_accent: '--d-accent', dastaan_color_bg: '--d-bg', dastaan_color_text: '--d-ink' };
	Object.keys(colors).forEach(function (k) {
		api(k, function (v) { v.bind(function (val) { document.documentElement.style.setProperty(colors[k], val); }); });
	});
})(window.wp && window.wp.customize);
