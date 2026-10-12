/*!
 * Dastaan by Artisans — checkout.js (cart + checkout enhancements; relies on WooCommerce's jQuery checkout script)
 */
(function ($) {
	'use strict';
	if (!$) { return; }

	var doc = document;

	/* Order summary collapse on mobile */
	var toggle = doc.querySelector('.js-summary-toggle');
	var review = doc.getElementById('order_review');
	function syncSummary() {
		if (!toggle || !review) { return; }
		var mobile = window.innerWidth < 1024;
		var expanded = toggle.getAttribute('aria-expanded') === 'true';
		review.classList.toggle('is-collapsed', mobile && !expanded);
	}
	if (toggle) {
		toggle.addEventListener('click', function () {
			toggle.setAttribute('aria-expanded', toggle.getAttribute('aria-expanded') === 'true' ? 'false' : 'true');
			syncSummary();
		});
		window.addEventListener('resize', syncSummary);
		syncSummary();
	}

	/* Keep the mobile total in sync after WooCommerce refreshes the review */
	$(doc.body).on('updated_checkout', function () {
		var total = doc.querySelector('.d-checkout__summary-total');
		var src = doc.querySelector('#order_review .order-total td');
		if (total && src) { total.innerHTML = src.innerHTML; }
		syncSummary();
		bindCoupon();
	});

	/* Coupon inside the order summary */
	var lastCouponMsg = null; // survives WooCommerce re-rendering the review block
	function bindCoupon() {
		var wrap = doc.querySelector('.checkout-coupon');
		if (!wrap || wrap.getAttribute('data-bound')) { return; }
		wrap.setAttribute('data-bound', '1');
		var t = wrap.querySelector('.js-coupon-toggle');
		var fields = wrap.querySelector('.checkout-coupon__fields');
		var input = wrap.querySelector('#d_coupon_code');
		var apply = wrap.querySelector('.js-apply-coupon');
		var msg = doc.createElement('p');
		msg.className = 'checkout-coupon__msg';
		wrap.appendChild(msg);
		if (lastCouponMsg) {
			msg.textContent = lastCouponMsg.text;
			msg.className = 'checkout-coupon__msg ' + lastCouponMsg.cls;
			if (lastCouponMsg.cls === 'is-error') { t.setAttribute('aria-expanded', 'true'); fields.removeAttribute('hidden'); }
			lastCouponMsg = null;
		}

		t.addEventListener('click', function () {
			var open = t.getAttribute('aria-expanded') === 'true';
			t.setAttribute('aria-expanded', open ? 'false' : 'true');
			if (open) { fields.setAttribute('hidden', ''); } else { fields.removeAttribute('hidden'); input.focus(); }
		});
		function submitCoupon() {
			var code = (input.value || '').trim();
			if (!code || !window.wc_checkout_params) { return; }
			apply.classList.add('is-loading');
			$.ajax({
				type: 'POST',
				url: window.wc_checkout_params.wc_ajax_url.toString().replace('%%endpoint%%', 'apply_coupon'),
				data: { security: window.wc_checkout_params.apply_coupon_nonce, coupon_code: code },
				dataType: 'html',
				success: function (html) {
					var tmp = doc.createElement('div');
					tmp.innerHTML = html;
					var isError = !!tmp.querySelector('.woocommerce-error, .is-error');
					msg.textContent = tmp.textContent.trim();
					msg.className = 'checkout-coupon__msg ' + (isError ? 'is-error' : 'is-success');
					lastCouponMsg = { text: msg.textContent, cls: isError ? 'is-error' : 'is-success' };
					if (!isError) { input.value = ''; }
					$(doc.body).trigger('update_checkout', { update_shipping_method: false });
				},
				error: function () { msg.textContent = 'Could not apply the code. Please try again.'; msg.className = 'checkout-coupon__msg is-error'; },
				complete: function () { apply.classList.remove('is-loading'); }
			});
		}
		apply.addEventListener('click', submitCoupon);
		input.addEventListener('keydown', function (e) { if (e.key === 'Enter') { e.preventDefault(); submitCoupon(); } });
	}
	bindCoupon();

	/* Remove coupon links inside our custom summary */
	$(doc).on('click', '.checkout-totals .woocommerce-remove-coupon', function (e) {
		e.preventDefault();
		var code = $(this).data('coupon');
		if (!code || !window.wc_checkout_params) { return; }
		$.ajax({
			type: 'POST',
			url: window.wc_checkout_params.wc_ajax_url.toString().replace('%%endpoint%%', 'remove_coupon'),
			data: { security: window.wc_checkout_params.remove_coupon_nonce, coupon: code },
			complete: function () { $(doc.body).trigger('update_checkout', { update_shipping_method: false }); }
		});
	});

	/* Phone: digits only, India friendly */
	var phone = doc.getElementById('billing_phone');
	if (phone) {
		phone.setAttribute('inputmode', 'tel');
		phone.addEventListener('input', function () { phone.value = phone.value.replace(/[^\d+\s-]/g, ''); });
	}

	/* Pincode: digits only, max 6 for India */
	$(doc).on('input', '#billing_postcode, #shipping_postcode', function () {
		var country = $(this).closest('.woocommerce-billing-fields, .woocommerce-shipping-fields').find('select[id$="_country"]').val();
		if (country === 'IN') { this.value = this.value.replace(/\D/g, '').slice(0, 6); }
	});

	/* Scroll to first error when checkout fails */
	$(doc.body).on('checkout_error', function () {
		var err = doc.querySelector('.woocommerce-NoticeGroup-checkout, .woocommerce-error');
		if (err) { err.scrollIntoView({ behavior: 'smooth', block: 'center' }); }
	});

	/* Place order: lock button to prevent double submit (WC re-enables on error) */
	$(doc).on('click', '#place_order', function () {
		var b = this;
		setTimeout(function () { b.disabled = true; setTimeout(function () { b.disabled = false; }, 8000); }, 10);
	});

	/* Cart page: auto-update on quantity change */
	var cartTimer;
	$(doc).on('change', '.woocommerce-cart-form input.qty', function () {
		clearTimeout(cartTimer);
		cartTimer = setTimeout(function () { var b = doc.querySelector('button[name="update_cart"]'); if (b) { b.disabled = false; b.click(); } }, 500);
	});
})(window.jQuery);
