(function () {
  'use strict';

  var cfg = window.baystoreLeanConfig || {
    ajaxUrl: '/wp-admin/admin-ajax.php',
    nonce: '',
    freeThreshold: 499,
    currency: '₹',
    checkoutUrl: '/checkout/',
  };

  function formatINR(n) {
    return cfg.currency + Math.round(Number(n || 0)).toLocaleString('en-IN');
  }

  function showToast(msg) {
    var el = document.getElementById('bs-toast');
    if (!el) return;
    el.textContent = msg;
    el.classList.add('is-show');
    clearTimeout(el._timer);
    el._timer = setTimeout(function () {
      el.classList.remove('is-show');
    }, 2400);
  }

  // ------------------------------------------------------------------
  // 1. Pincode Estimator & Modal
  // ------------------------------------------------------------------
  function calcDelivery(pin) {
    var clean = String(pin || '400001').replace(/\D/g, '');
    if (clean.length !== 6) clean = '400001';
    var metros = {
      '400': ['Mumbai', 1],
      '401': ['Thane MMR', 1],
      '411': ['Pune', 1],
      '110': ['New Delhi', 1],
      '122': ['Gurugram', 1],
      '201': ['Noida', 1],
      '560': ['Bengaluru', 1],
      '500': ['Hyderabad', 2],
      '600': ['Chennai', 2],
      '700': ['Kolkata', 2],
      '380': ['Ahmedabad', 2],
    };
    var info = metros[clean.slice(0, 3)] || ['India', 3];
    var d = new Date();
    d.setDate(d.getDate() + info[1]);
    var label =
      info[1] === 1
        ? 'Tomorrow, ' + d.toLocaleDateString('en-IN', { day: 'numeric', month: 'short' })
        : d.toLocaleDateString('en-IN', { weekday: 'short', day: 'numeric', month: 'short' });
    return { pincode: clean, city: info[0], label: label };
  }

  function applyPincodeUI(pin) {
    var info = calcDelivery(pin);
    try {
      localStorage.setItem('bs_pincode', info.pincode);
    } catch (e) {}
    document.querySelectorAll('[data-bs-loc-text]').forEach(function (el) {
      el.textContent = info.city + ' ' + info.pincode;
    });
    document.querySelectorAll('[data-bs-eta-text]').forEach(function (el) {
      el.textContent = info.label;
    });
  }

  // ------------------------------------------------------------------
  // 2. Hero Carousel & Lightning Countdown
  // ------------------------------------------------------------------
  function initHero() {
    var slides = document.querySelectorAll('.bs-hero-slide');
    if (!slides.length) return;
    var idx = 0;

    function showSlide(n) {
      slides.forEach(function (s, i) {
        s.classList.toggle('is-active', i === n);
      });
    }

    var prev = document.getElementById('bs-hero-prev');
    var next = document.getElementById('bs-hero-next');
    if (prev) {
      prev.addEventListener('click', function () {
        idx = (idx - 1 + slides.length) % slides.length;
        showSlide(idx);
      });
    }
    if (next) {
      next.addEventListener('click', function () {
        idx = (idx + 1) % slides.length;
        showSlide(idx);
      });
    }
    setInterval(function () {
      idx = (idx + 1) % slides.length;
      showSlide(idx);
    }, 6000);
  }

  function initCountdown() {
    var el = document.getElementById('bs-countdown-timer');
    if (!el) return;
    var secs = 4 * 3600 + 18 * 60 + 42;
    setInterval(function () {
      secs = secs > 0 ? secs - 1 : 4 * 3600;
      var h = String(Math.floor(secs / 3600)).padStart(2, '0');
      var m = String(Math.floor((secs % 3600) / 60)).padStart(2, '0');
      var s = String(secs % 60).padStart(2, '0');
      el.textContent = 'Ends in ' + h + ':' + m + ':' + s;
    }, 1000);
  }

  // ------------------------------------------------------------------
  // 3. AJAX Live Search Autocomplete
  // ------------------------------------------------------------------
  function initLiveSearch() {
    var input = document.getElementById('bs-search-input');
    var deptSel = document.getElementById('bs-search-dept');
    var box = document.getElementById('bs-search-results');
    if (!input || !box) return;

    var timer = null;
    input.addEventListener('input', function () {
      clearTimeout(timer);
      var q = input.value.trim();
      if (q.length < 2) {
        box.classList.remove('is-open');
        box.innerHTML = '';
        return;
      }
      timer = setTimeout(function () {
        var fd = new FormData();
        fd.append('action', 'baystore_live_search');
        fd.append('nonce', cfg.nonce);
        fd.append('query', q);
        fd.append('dept', deptSel ? deptSel.value : '');

        fetch(cfg.ajaxUrl, { method: 'POST', body: fd })
          .then(function (r) {
            return r.json();
          })
          .then(function (res) {
            if (!res.success || !res.data || !res.data.length) {
              box.classList.remove('is-open');
              return;
            }
            box.innerHTML = res.data
              .map(function (it) {
                return (
                  '<a href="' +
                  it.url +
                  '" class="bs-search-res-item">' +
                  '<img src="' +
                  it.image +
                  '" alt="" />' +
                  '<div style="flex:1;min-width:0;">' +
                  '<div style="font-size:13px;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">' +
                  it.title +
                  '</div>' +
                  '<div style="font-size:11px;color:#1F49B6;">' +
                  it.category +
                  '</div></div>' +
                  '<div style="font-size:13px;font-weight:800;">' +
                  formatINR(it.price) +
                  '</div></a>'
                );
              })
              .join('');
            box.classList.add('is-open');
          })
          .catch(function () {});
      }, 220);
    });

    document.addEventListener('click', function (e) {
      if (!box.contains(e.target) && e.target !== input) {
        box.classList.remove('is-open');
      }
    });
  }

  // ------------------------------------------------------------------
  // 4. Slide-Over Cart Drawer & AJAX Cart
  // ------------------------------------------------------------------
  function openCartDrawer() {
    var bd = document.getElementById('bs-cart-backdrop');
    if (bd) bd.classList.add('is-open');
    refreshCartDrawer();
  }

  function closeCartDrawer() {
    var bd = document.getElementById('bs-cart-backdrop');
    if (bd) bd.classList.remove('is-open');
  }

  function renderCartPayload(data) {
    if (!data) return;
    var countEl = document.getElementById('bs-header-cart-count');
    var subEl = document.getElementById('bs-header-cart-subtotal');
    var drawerTitle = document.getElementById('bs-drawer-cart-count');
    var body = document.getElementById('bs-drawer-items');
    var shipText = document.getElementById('bs-free-ship-text');
    var shipFill = document.getElementById('bs-free-ship-fill');
    var totalEl = document.getElementById('bs-drawer-total');

    if (countEl) countEl.textContent = data.count || 0;
    if (drawerTitle) drawerTitle.textContent = data.count || 0;
    if (subEl) subEl.textContent = data.count > 0 ? formatINR(data.subtotal) : 'Empty';
    if (totalEl) totalEl.textContent = formatINR(data.total || data.subtotal || 0);

    var threshold = Number(cfg.freeThreshold || 499);
    var sub = Number(data.subtotal || 0);
    var rem = Math.max(0, threshold - sub);
    var pct = Math.min(100, Math.round((sub / threshold) * 100));

    if (shipText) {
      shipText.innerHTML =
        rem > 0
          ? 'Add <strong style="color:#212121">' +
            formatINR(rem) +
            '</strong> more for <strong>FREE Express Delivery</strong>'
          : '<strong style="color:#1F49B6">✓ Your order qualifies for FREE delivery</strong>';
    }
    if (shipFill) {
      shipFill.style.width = pct + '%';
      shipFill.classList.toggle('is-free', rem === 0);
    }

    if (body) {
      if (!data.items || !data.items.length) {
        body.innerHTML =
          '<div style="text-align:center;padding:42px 12px;color:#565959;">' +
          '<p style="font-weight:700;color:#0F1111;">Your cart is empty</p>' +
          '<p style="font-size:12px;">Add a few favourites to unlock Free Delivery ≥ ' +
          formatINR(threshold) +
          '</p></div>';
      } else {
        body.innerHTML = data.items
          .map(function (it) {
            return (
              '<div style="display:flex;gap:10px;border:1px solid #e3e6e6;border-radius:6px;padding:10px;margin-bottom:10px;">' +
              '<img src="' +
              it.image +
              '" style="width:68px;height:68px;object-fit:contain;background:#f7f8f8;border-radius:4px;" />' +
              '<div style="flex:1;min-width:0;">' +
              '<div style="font-size:12px;font-weight:700;margin-bottom:4px;">' +
              it.name +
              '</div>' +
              '<div style="font-size:13px;font-weight:800;color:#212121;margin-bottom:6px;">' +
              formatINR(it.price) +
              '</div>' +
              '<div style="display:flex;align-items:center;justify-content:space-between;">' +
              '<div style="display:inline-flex;align-items:center;border:1px solid #d5d9d9;border-radius:999px;background:#f0f2f2;">' +
              '<button type="button" data-bs-qty-key="' +
              it.key +
              '" data-bs-qty-val="' +
              (it.qty - 1) +
              '" style="border:none;background:transparent;padding:3px 9px;cursor:pointer;font-weight:800;">-</button>' +
              '<span style="font-size:12px;font-weight:700;padding:0 6px;">' +
              it.qty +
              '</span>' +
              '<button type="button" data-bs-qty-key="' +
              it.key +
              '" data-bs-qty-val="' +
              (it.qty + 1) +
              '" style="border:none;background:transparent;padding:3px 9px;cursor:pointer;font-weight:800;">+</button>' +
              '</div>' +
              '<button type="button" data-bs-qty-key="' +
              it.key +
              '" data-bs-qty-val="0" style="border:none;background:transparent;color:#1F49B6;font-size:11px;cursor:pointer;">Delete</button>' +
              '</div></div></div>'
            );
          })
          .join('');
      }
    }
  }

  function refreshCartDrawer() {
    var fd = new FormData();
    fd.append('action', 'baystore_get_cart');
    fd.append('nonce', cfg.nonce);
    fetch(cfg.ajaxUrl, { method: 'POST', body: fd })
      .then(function (r) {
        return r.json();
      })
      .then(function (res) {
        if (res.success) renderCartPayload(res.data);
      })
      .catch(function () {});
  }

  document.addEventListener('click', function (e) {
    // Open / Close Cart Drawer
    if (e.target.closest('#bs-open-cart')) {
      e.preventDefault();
      openCartDrawer();
      return;
    }
    if (e.target.closest('#bs-close-cart') || e.target.id === 'bs-cart-backdrop') {
      closeCartDrawer();
      return;
    }

    // Mega Drawer
    if (e.target.closest('#bs-open-mega')) {
      var mb = document.getElementById('bs-mega-backdrop');
      if (mb) mb.classList.add('is-open');
      return;
    }
    if (e.target.closest('#bs-close-mega') || e.target.id === 'bs-mega-backdrop') {
      var mb2 = document.getElementById('bs-mega-backdrop');
      if (mb2) mb2.classList.remove('is-open');
      return;
    }

    // Pincode Modal
    if (e.target.closest('[data-bs-open-pincode]')) {
      var pm = document.getElementById('bs-pincode-backdrop');
      if (pm) pm.classList.add('is-open');
      return;
    }
    if (e.target.closest('#bs-close-pincode') || e.target.id === 'bs-pincode-backdrop') {
      var pm2 = document.getElementById('bs-pincode-backdrop');
      if (pm2) pm2.classList.remove('is-open');
      return;
    }

    // Quick Pincode Chip
    var pinChip = e.target.closest('[data-bs-pin-preset]');
    if (pinChip) {
      applyPincodeUI(pinChip.getAttribute('data-bs-pin-preset'));
      var pm3 = document.getElementById('bs-pincode-backdrop');
      if (pm3) pm3.classList.remove('is-open');
      showToast('Delivery location updated!');
      return;
    }

    // AJAX Add to Cart
    var addBtn = e.target.closest('[data-bs-add-product]');
    if (addBtn) {
      e.preventDefault();
      var pid = addBtn.getAttribute('data-bs-add-product');
      var qty = addBtn.getAttribute('data-bs-qty') || 1;
      var fd = new FormData();
      fd.append('action', 'baystore_add_to_cart');
      fd.append('nonce', cfg.nonce);
      fd.append('product_id', pid);
      fd.append('quantity', qty);

      addBtn.disabled = true;
      fetch(cfg.ajaxUrl, { method: 'POST', body: fd })
        .then(function (r) {
          return r.json();
        })
        .then(function (res) {
          addBtn.disabled = false;
          if (res.success) {
            renderCartPayload(res.data);
            var cartBd = document.getElementById('bs-cart-backdrop');
            if (cartBd) cartBd.classList.add('is-open');
            showToast('✓ Added to cart');
          }
        })
        .catch(function () {
          addBtn.disabled = false;
        });
      return;
    }

    // Cart Drawer Qty Update
    var qtyBtn = e.target.closest('[data-bs-qty-key]');
    if (qtyBtn) {
      var key = qtyBtn.getAttribute('data-bs-qty-key');
      var newQty = qtyBtn.getAttribute('data-bs-qty-val');
      var fd2 = new FormData();
      fd2.append('action', 'baystore_update_cart_qty');
      fd2.append('nonce', cfg.nonce);
      fd2.append('cart_key', key);
      fd2.append('quantity', newQty);
      fetch(cfg.ajaxUrl, { method: 'POST', body: fd2 })
        .then(function (r) {
          return r.json();
        })
        .then(function (res) {
          if (res.success) renderCartPayload(res.data);
        });
      return;
    }

    // Apply BAY10 Coupon
    var coupBtn = e.target.closest('[data-bs-apply-coupon]');
    if (coupBtn) {
      var code = coupBtn.getAttribute('data-bs-apply-coupon') || 'BAY10';
      var fd3 = new FormData();
      fd3.append('action', 'baystore_apply_coupon');
      fd3.append('nonce', cfg.nonce);
      fd3.append('coupon', code);
      fetch(cfg.ajaxUrl, { method: 'POST', body: fd3 })
        .then(function (r) {
          return r.json();
        })
        .then(function (res) {
          if (res.success) {
            renderCartPayload(res.data);
            coupBtn.classList.add('is-applied');
            showToast('Coupon ' + code + ' applied!');
          } else {
            showToast((res.data && res.data.message) || 'Coupon activated at checkout: ' + code);
          }
        });
    }
  });

  // Pincode Form Submit
  document.addEventListener('submit', function (e) {
    if (e.target && e.target.id === 'bs-pincode-form') {
      e.preventDefault();
      var inp = document.getElementById('bs-pincode-input');
      var val = inp ? inp.value.replace(/\D/g, '') : '';
      if (val.length !== 6) {
        showToast('Enter a valid 6-digit Indian pincode');
        return;
      }
      applyPincodeUI(val);
      var pm = document.getElementById('bs-pincode-backdrop');
      if (pm) pm.classList.remove('is-open');
      showToast('Delivering to pincode ' + val);
    }
  });

  document.addEventListener('DOMContentLoaded', function () {
    try {
      var savedPin = localStorage.getItem('bs_pincode');
      if (savedPin) applyPincodeUI(savedPin);
    } catch (e) {}
    initHero();
    initCountdown();
    initLiveSearch();
    if (document.getElementById('bs-open-cart')) {
      refreshCartDrawer();
    }
  });
})();
