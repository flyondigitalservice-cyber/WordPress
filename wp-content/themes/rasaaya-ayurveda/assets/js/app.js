(function () {
    const CART_KEY = 'rasaaya_cart_v2';

    function getCart() {
        try {
            return JSON.parse(localStorage.getItem(CART_KEY) || '[]');
        } catch (e) {
            return [];
        }
    }

    function saveCart(cart) {
        localStorage.setItem(CART_KEY, JSON.stringify(cart));
        renderCartUI();
        if (typeof window.renderCheckoutSummary === 'function') {
            window.renderCheckoutSummary();
        }
    }

    window.rasToast = function (msg) {
        let el = document.getElementById('ras-toast');
        if (!el) return;
        el.textContent = msg;
        el.classList.add('show');
        clearTimeout(window._rasToastTimer);
        window._rasToastTimer = setTimeout(() => el.classList.remove('show'), 2700);
    };

    window.rasOpenCart = function () {
        document.getElementById('ras-cart-backdrop')?.classList.add('open');
        document.getElementById('ras-cart-drawer')?.classList.add('open');
    };

    window.rasCloseCart = function () {
        document.getElementById('ras-cart-backdrop')?.classList.remove('open');
        document.getElementById('ras-cart-drawer')?.classList.remove('open');
    };

    window.rasAddToCart = function (prod, isSub) {
        let cart = getCart();
        const subFlag = Boolean(isSub && prod.sub_eligible);
        const key = prod.slug + (subFlag ? '__sub' : '__one');
        const existing = cart.find(i => i.key === key);
        if (existing) {
            existing.qty += 1;
        } else {
            cart.push({
                key: key,
                slug: prod.slug,
                title: prod.title,
                price: Number(prod.price),
                mrp: Number(prod.mrp),
                img: prod.img,
                pack: prod.pack,
                sub_eligible: Boolean(prod.sub_eligible),
                sub: subFlag,
                qty: 1
            });
        }
        saveCart(cart);
        window.rasToast(subFlag
            ? `Added ${prod.title} (Subscribe 15% Off) to ritual bag`
            : `Added ${prod.title} to your ritual bag`);
        window.rasOpenCart();
    };

    window.rasUpdateQty = function (key, delta) {
        let cart = getCart();
        const item = cart.find(i => i.key === key);
        if (!item) return;
        item.qty += delta;
        if (item.qty <= 0) {
            cart = cart.filter(i => i.key !== key);
        }
        saveCart(cart);
    };

    window.rasToggleSub = function (key) {
        let cart = getCart();
        const item = cart.find(i => i.key === key);
        if (!item || !item.sub_eligible) return;
        item.sub = !item.sub;
        item.key = item.slug + (item.sub ? '__sub' : '__one');
        saveCart(cart);
    };

    function calcTotals(cart) {
        let subtotal = 0;
        let mrpTotal = 0;
        let count = 0;
        cart.forEach(i => {
            const unit = i.sub ? Math.round(i.price * 0.85) : i.price;
            subtotal += unit * i.qty;
            mrpTotal += i.mrp * i.qty;
            count += i.qty;
        });
        const onlyTrial = cart.length === 1 && cart[0].slug === '7-day-gut-trial-pack';
        const shipping = (subtotal === 0 || subtotal >= 499 || onlyTrial) ? 0 : 49;
        return { subtotal, mrpTotal, count, shipping, grand: subtotal + shipping };
    }

    function renderCartUI() {
        const cart = getCart();
        const t = calcTotals(cart);

        document.querySelectorAll('.js-cart-count').forEach(el => {
            el.textContent = t.count;
        });

        const shipMsg = document.getElementById('ras-ship-msg');
        const shipFill = document.getElementById('ras-ship-fill');
        if (shipMsg && shipFill) {
            if (t.subtotal === 0) {
                shipMsg.innerHTML = 'Add <strong>₹499</strong> for FREE Express Shipping + Free Digest+ Sachet';
                shipFill.style.width = '0%';
            } else if (t.subtotal >= 499) {
                shipMsg.innerHTML = 'Unlocked: <strong>FREE Shipping + Complimentary Digest+ Sample Sachet!</strong>';
                shipFill.style.width = '100%';
            } else {
                const rem = 499 - t.subtotal;
                shipMsg.innerHTML = `Add <strong>₹${rem}</strong> more to unlock <strong>FREE Shipping + Free Sachet</strong>`;
                shipFill.style.width = Math.min(100, Math.round((t.subtotal / 499) * 100)) + '%';
            }
        }

        const listEl = document.getElementById('ras-cart-items');
        const subEl = document.getElementById('ras-cart-subtotal');
        const savEl = document.getElementById('ras-cart-savings');
        if (!listEl) return;

        if (cart.length === 0) {
            listEl.innerHTML = `
                <div style="text-align:center;padding:42px 12px;">
                    <div style="font-size:32px;margin-bottom:8px;">🌿</div>
                    <strong style="display:block;font-size:16px;color:#113826;margin-bottom:6px;">Your Ritual Bag is Empty</strong>
                    <p style="font-size:13px;color:#63736B;margin-bottom:18px;">Start with our ₹99 7-Day Gut Trial Pack or the ₹549 30-Day Gut Starter Kit.</p>
                    <button type="button" class="btn btn-saffron" onclick="window.rasQuickTrial()">Add ₹99 7-Day Trial Pack</button>
                </div>`;
        } else {
            listEl.innerHTML = cart.map(item => {
                const unit = item.sub ? Math.round(item.price * 0.85) : item.price;
                const line = unit * item.qty;
                const subToggle = item.sub_eligible ? `
                    <label style="display:flex;align-items:center;gap:6px;margin-top:6px;font-size:11.5px;color:#184D35;font-weight:700;cursor:pointer;">
                        <input type="checkbox" ${item.sub ? 'checked' : ''} onchange="window.rasToggleSub('${item.key}')">
                        <span>Auto-Replenish (15% Off • ₹${Math.round(item.price * 0.85)}/box + Day-25 WhatsApp)</span>
                    </label>` : `<div style="font-size:11px;color:#B8761A;font-weight:700;margin-top:4px;">Prepaid Free-Ship Trial Offer</div>`;
                return `
                <div class="cart-line-item">
                    <img src="${item.img}" alt="${item.title}">
                    <div style="flex:1;">
                        <div style="display:flex;justify-content:space-between;gap:8px;">
                            <strong style="font-size:14px;color:#132019;">${item.title}</strong>
                            <strong style="font-size:14.5px;color:#113826;">₹${line}</strong>
                        </div>
                        <div style="font-size:11.5px;color:#63736B;">${item.pack}</div>
                        ${subToggle}
                        <div style="display:flex;align-items:center;gap:10px;margin-top:8px;">
                            <button type="button" onclick="window.rasUpdateQty('${item.key}', -1)" style="width:25px;height:25px;border-radius:6px;border:1px solid #CBD5E1;background:#FFF;cursor:pointer;font-weight:800;">−</button>
                            <span style="font-size:13px;font-weight:800;">${item.qty}</span>
                            <button type="button" onclick="window.rasUpdateQty('${item.key}', 1)" style="width:25px;height:25px;border-radius:6px;border:1px solid #CBD5E1;background:#FFF;cursor:pointer;font-weight:800;">+</button>
                        </div>
                    </div>
                </div>`;
            }).join('');
        }

        if (subEl) subEl.textContent = '₹' + t.subtotal;
        if (savEl) {
            const saved = Math.max(0, t.mrpTotal - t.subtotal);
            savEl.textContent = saved > 0 ? `You save ₹${saved} (incl. 5% GST)` : '5% GST Included';
        }
    }

    window.rasQuickTrial = function () {
        if (window.RASAAYA_CATALOG && window.RASAAYA_CATALOG.length) {
            const tr = window.RASAAYA_CATALOG.find(p => p.slug === '7-day-gut-trial-pack') || window.RASAAYA_CATALOG[0];
            window.rasAddToCart(tr, false);
        }
    };

    window.rasAddBySlug = function (slug, isSub) {
        if (!window.RASAAYA_CATALOG) return;
        const p = window.RASAAYA_CATALOG.find(x => x.slug === slug);
        if (p) window.rasAddToCart(p, isSub);
    };

    // Category filter tabs
    document.addEventListener('click', function (e) {
        const tab = e.target.closest('.filter-tab');
        if (tab) {
            const cat = tab.getAttribute('data-filter');
            document.querySelectorAll('.filter-tab').forEach(b => b.classList.remove('active'));
            tab.classList.add('active');
            document.querySelectorAll('.prod-card[data-cat]').forEach(card => {
                if (cat === 'all' || card.getAttribute('data-cat') === cat) {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        const faqBtn = e.target.closest('.faq-q');
        if (faqBtn) {
            faqBtn.parentElement.classList.toggle('open');
        }
    });

    // Interactive Gut & Wellness Quiz
    let quizGoal = 'bloating';
    window.rasSelectQuiz = function (btn, goal) {
        quizGoal = goal;
        document.querySelectorAll('.quiz-opt').forEach(b => b.classList.remove('selected'));
        btn.classList.add('selected');

        const map = {
            bloating: {
                headline: 'Your Prescribed Ritual: 30-Day Gut Starter Kit (Gut Reset + Digest+)',
                why: 'Digest+ (Jeera, Pudina, Hing) settles post-lunch gas within 20 minutes, while Gut Reset (Triphala 40% + Isabgol 30%) restores effortless morning regularity without habit-forming Senna.',
                slug: '30-day-gut-starter-kit',
                cta: 'Add 30-Day Starter Kit — ₹549 (Saves ₹99)'
            },
            regularity: {
                headline: 'Your Prescribed Ritual: Gut Reset (Daily 6g Fibre-Triphala Cleanse)',
                why: 'Unjha Isabgol husk + classical 3-fruit Triphala gently hydrate and sweep the digestive tract overnight. Zero cramping, zero dependency.',
                slug: 'gut-reset',
                cta: 'Add Gut Reset 30-Pack — ₹359'
            },
            skin: {
                headline: 'Your Prescribed Ritual: Glow Within (Gut-Skin Axis Botanical Blend)',
                why: 'Dull skin and recurring breakouts often start in sluggish digestion. Wild Amla, Manjistha root, Lakadong Haldi + Piperine support clear radiance from within.',
                slug: 'glow-within',
                cta: 'Add Glow Within 30-Pack — ₹399'
            },
            sleep: {
                headline: 'Your Prescribed Ritual: Calm & Sleep (100% Root-Only Ashwagandha)',
                why: 'Crafted strictly with Ashwagandha root (0% banned leaf), Brahmi and Chamomile—swirl into warm bedtime milk to unwind evening cortisol without melatonin grogginess.',
                slug: 'calm-and-sleep',
                cta: 'Add Calm & Sleep 30-Pack — ₹399'
            }
        };

        const res = map[quizGoal] || map.bloating;
        const box = document.getElementById('ras-quiz-result');
        if (box) {
            box.style.display = 'block';
            box.innerHTML = `
                <div style="font-size:11.5px;font-weight:800;color:#B8761A;text-transform:uppercase;letter-spacing:0.06em;margin-bottom:4px;">VAIDYA FORMULATION MATCH</div>
                <h3 style="font-size:19px;color:#113826;margin-bottom:8px;">${res.headline}</h3>
                <p style="font-size:13.5px;color:#37473F;margin-bottom:16px;">${res.why}</p>
                <div style="display:flex;flex-wrap:wrap;gap:10px;">
                    <button type="button" class="btn btn-primary" onclick="window.rasAddBySlug('${res.slug}', false)">${res.cta}</button>
                    <button type="button" class="btn btn-outline" onclick="window.rasAddBySlug('7-day-gut-trial-pack', false)">Or Try 7-Day Pack — ₹99</button>
                </div>`;
        }
    };

    // Live WP REST NABL Batch CoA Checker
    window.rasVerifyBatch = async function () {
        const inp = document.getElementById('ras-coa-input');
        const out = document.getElementById('ras-coa-result');
        if (!out) return;
        const batch = inp ? inp.value.trim() : 'RAS-GR-2611';
        out.innerHTML = '<div class="coa-line">Checking the lab register&hellip;</div>';
        try {
            const r = await fetch('/wp-json/rasaaya/v1/coa?batch=' + encodeURIComponent(batch));
            const d = await r.json();
            const esc = (s) => String(s).replace(/[&<>"]/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' }[c]));
            out.innerHTML = `
                <div class="coa-pass">✓ ${esc(d.status)}</div>
                <div class="coa-line"><b>Batch</b> ${esc(d.batch)}</div>
                <div class="coa-line"><b>Made</b> ${esc(d.mfg_date)}</div>
                <div class="coa-line"><b>Moisture</b> ${esc(d.moisture)}</div>
                <div class="coa-line"><b>Senna</b> ${esc(d.senna_check)}</div>
                <div class="coa-line"><b>Heavy metals</b> ${esc(d.heavy_metal)}</div>
                <div class="coa-line"><b>Microbes</b> ${esc(d.microbial)}</div>`;
        } catch (err) {
            out.innerHTML = '<div class="coa-line">Couldn&rsquo;t reach the lab register just now. Please try again.</div>';
        }
    };

    // Checkout page integration
    window.renderCheckoutSummary = function () {
        const wrap = document.getElementById('ras-checkout-summary');
        if (!wrap) return;
        const cart = getCart();
        const t = calcTotals(cart);
        const payMode = document.querySelector('input[name="ras_pay"]:checked')?.value || 'UPI Prepaid';
        const upiDisc = (payMode.indexOf('UPI') === 0 && t.subtotal >= 199) ? 25 : 0;
        const codFee = (payMode === 'Cash on Delivery' && t.subtotal > 0) ? 25 : 0;
        const finalTotal = Math.max(0, t.subtotal + t.shipping - upiDisc + codFee);
        const gstPart = (finalTotal - (finalTotal / 1.05)).toFixed(2);

        window._rasCurrentCheckoutTotal = finalTotal;

        if (cart.length === 0) {
            wrap.innerHTML = `<p style="font-size:14px;color:#63736B;">Your bag is empty. <a href="/shop/" style="color:#113826;font-weight:800;text-decoration:underline;">Browse Sachets →</a></p>`;
            return;
        }

        wrap.innerHTML = `
            <div>
                ${cart.map(i => {
                    const u = i.sub ? Math.round(i.price * 0.85) : i.price;
                    return `<div style="display:flex;justify-content:space-between;font-size:13.5px;padding:8px 0;border-bottom:1px solid #EAE1CF;">
                        <span><strong>${i.title}</strong> × ${i.qty} ${i.sub ? '<em style="color:#B8761A;font-style:normal;font-size:11.5px;">(Sub 15% Off)</em>' : ''}</span>
                        <strong>₹${u * i.qty}</strong>
                    </div>`;
                }).join('')}
                <div style="padding-top:12px;font-size:13.5px;display:grid;gap:6px;">
                    <div style="display:flex;justify-content:space-between;"><span>Subtotal</span><span>₹${t.subtotal}</span></div>
                    <div style="display:flex;justify-content:space-between;"><span>3PL Express Shipping</span><span>${t.shipping === 0 ? '<strong style="color:#184D35;">FREE</strong>' : '₹' + t.shipping}</span></div>
                    ${upiDisc > 0 ? `<div style="display:flex;justify-content:space-between;color:#184D35;font-weight:700;"><span>UPI Instant Prepaid Discount</span><span>−₹${upiDisc}</span></div>` : ''}
                    ${codFee > 0 ? `<div style="display:flex;justify-content:space-between;color:#B8761A;"><span>COD Handling Fee</span><span>+₹${codFee}</span></div>` : ''}
                    <div style="display:flex;justify-content:space-between;font-size:18px;font-weight:800;color:#0A2217;padding-top:10px;border-top:2px solid #113826;">
                        <span>Total Payable</span><span>₹${finalTotal}</span>
                    </div>
                    <div style="font-size:11.5px;color:#63736B;">Includes ₹${gstPart} GST (5% Nutraceutical Slab — HSN 21069099) + 1 Free Digest+ Sample Sachet</div>
                </div>
            </div>`;
    };

    window.rasSubmitOrder = async function (e) {
        e.preventDefault();
        const cart = getCart();
        if (!cart.length) {
            window.rasToast('Please add at least one ritual pack first.');
            return false;
        }
        const btn = document.getElementById('ras-place-order-btn');
        if (btn) { btn.disabled = true; btn.textContent = 'Confirming Order in WordPress...'; }

        const payload = {
            name: document.getElementById('co_name')?.value || 'Aarav Mehta',
            phone: document.getElementById('co_phone')?.value || '+91 98200 00000',
            city: document.getElementById('co_city')?.value || 'Mumbai - 400050',
            address: document.getElementById('co_addr')?.value || 'Bandra West',
            payment: document.querySelector('input[name="ras_pay"]:checked')?.value || 'UPI Prepaid',
            whatsapp_remind: document.getElementById('co_wa')?.checked || false,
            total: window._rasCurrentCheckoutTotal || 549,
            items: cart.map(i => ({
                title: i.title,
                qty: i.qty,
                sub: i.sub,
                lineTotal: (i.sub ? Math.round(i.price * 0.85) : i.price) * i.qty
            }))
        };

        try {
            const r = await fetch('/wp-json/rasaaya/v1/order', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            const data = await r.json();
            localStorage.removeItem(CART_KEY);
            renderCartUI();
            const formArea = document.getElementById('ras-checkout-container');
            if (formArea) {
                formArea.innerHTML = `
                <div style="background:#FFF;border:2px solid #184D35;border-radius:18px;padding:38px;text-align:center;max-width:660px;margin:0 auto;box-shadow:0 16px 40px rgba(0,0,0,0.08);">
                    <div style="width:58px;height:58px;border-radius:50%;background:#113826;color:#E5A94C;font-size:26px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;font-weight:800;">✓</div>
                    <div style="font-size:12px;font-weight:800;color:#B8761A;letter-spacing:0.08em;text-transform:uppercase;">WORDPRESS ORDER SAVED • #${data.order_number}</div>
                    <h2 style="font-size:28px;color:#0A2217;margin:8px 0 10px;">${data.message}</h2>
                    <p style="font-size:14.5px;color:#37473F;margin-bottom:20px;">Total Paid: <strong>₹${data.total}</strong> via ${data.payment} (includes ₹${data.gst_5pct} at 5% GST under HSN 21069099). Your WhatsApp 7-Day Gut Challenge & Day-25 reminder link has been queued.</p>
                    <div style="display:flex;justify-content:center;gap:12px;flex-wrap:wrap;">
                        <a href="/" class="btn btn-primary">Return to Storefront</a>
                        <a href="/wp-admin/edit.php?post_type=rasaaya_order" class="btn btn-outline">View Order in WP-Admin</a>
                    </div>
                </div>`;
            }
        } catch (err) {
            if (btn) { btn.disabled = false; btn.textContent = 'Complete Ritual Order'; }
            window.rasToast('Could not submit order — please retry.');
        }
        return false;
    };

    document.addEventListener('DOMContentLoaded', function () {
        renderCartUI();
        window.renderCheckoutSummary();
    });
})();
