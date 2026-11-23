<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<!-- SLIDE-OVER CART DRAWER -->
<div id="ras-cart-backdrop" class="cart-backdrop" onclick="window.rasCloseCart()"></div>
<aside id="ras-cart-drawer" class="cart-drawer" aria-label="Shopping Bag">
    <div class="cart-hdr">
        <div>
            <strong style="font-size:16px;letter-spacing:0.04em;">YOUR DAILY RITUAL BAG</strong>
            <div style="font-size:11px;color:#E5A94C;">15% Off Auto-Replenish Available</div>
        </div>
        <button type="button" onclick="window.rasCloseCart()" style="background:none;border:none;color:#FFF;font-size:22px;cursor:pointer;">&times;</button>
    </div>

    <div class="cart-ship-bar">
        <div id="ras-ship-msg">Add <strong>₹499</strong> for FREE Express Shipping + Free Digest+ Sachet</div>
        <div class="ship-progress-track">
            <div id="ras-ship-fill" class="ship-progress-fill"></div>
        </div>
    </div>

    <div id="ras-cart-items" class="cart-items-area"></div>

    <div style="padding:10px 22px;background:#F2F7F4;border-top:1px solid #CFE0D6;display:flex;justify-content:space-between;align-items:center;gap:10px;">
        <div style="font-size:12px;color:#113826;">
            <strong>Summer Add-On:</strong> Hydrate Kokum+ (15 Sachets)
        </div>
        <button type="button" onclick="window.rasAddBySlug('hydrate-kokum-plus', false)" style="background:#113826;color:#FFF;border:none;border-radius:6px;padding:5px 10px;font-size:11.5px;font-weight:700;cursor:pointer;">+ ₹269</button>
    </div>

    <div class="cart-ftr">
        <div style="display:flex;justify-content:space-between;align-items:baseline;margin-bottom:4px;">
            <span style="font-size:14px;font-weight:700;color:#37473F;">Estimated Subtotal</span>
            <strong id="ras-cart-subtotal" style="font-size:21px;font-weight:800;color:#0A2217;">₹0</strong>
        </div>
        <div id="ras-cart-savings" style="font-size:11.5px;color:#B8761A;font-weight:700;margin-bottom:12px;">5% GST Included</div>
        <a href="/checkout/" class="btn btn-primary" style="width:100%;">Proceed to 5%-GST Checkout &rarr;</a>
        <div style="text-align:center;font-size:11px;color:#63736B;margin-top:8px;">UPI Extra ₹25 Off (Orders ≥₹199) &bull; NABL Tested &bull; Zero Senna</div>
    </div>
</aside>

<div id="ras-toast" class="ras-toast">Added to bag</div>

<footer class="ras-footer">
    <div class="footer-vine" aria-hidden="true"></div>
    <div class="container">
        <div class="footer-handline">made by hand, with haldi-stained fingers, in Mumbai <?php echo rasaaya_doodle( 'heart' ); ?></div>
        <div class="ras-footer-grid">
            <div>
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:14px;">
                    <div class="ras-crest">R</div>
                    <strong style="font-size:19px;letter-spacing:0.15em;color:#FFF;">RASAAYA</strong>
                </div>
                <p style="margin-bottom:14px;color:#CBD5E1;">Ancient Indian kitchen &amp; classical Rasayana wisdom sealed inside moisture-barrier 4-ply single-serve sachets. Swirl into water or warm milk in 15 seconds.</p>
                <div class="footer-credit">Brand &amp; storefront concept by Bombay Marketing Studio</div>
            </div>

            <div>
                <h4>Daily Rituals</h4>
                <ul>
                    <li><a href="/product/7-day-gut-trial-pack/">7-Day Gut Trial Pack (₹99)</a></li>
                    <li><a href="/product/30-day-gut-starter-kit/">30-Day Gut Starter Kit (₹549)</a></li>
                    <li><a href="/product/gut-reset/">Gut Reset — Morning Cleanse</a></li>
                    <li><a href="/product/digest-plus/">Digest+ — Post-Meal Comfort</a></li>
                    <li><a href="/product/glow-within/">Glow Within — Gut-Skin Axis</a></li>
                    <li><a href="/product/calm-and-sleep/">Calm &amp; Sleep — Root Ashwagandha</a></li>
                </ul>
            </div>

            <div>
                <h4>Science &amp; Trust</h4>
                <ul>
                    <li><a href="/ayurvedic-science/">Formulation &amp; Herb Standards</a></li>
                    <li><a href="/#coa-checker">NABL Batch CoA Lookup</a></li>
                    <li><a href="/7-day-trial/">WhatsApp 7-Day Gut Challenge</a></li>
                    <li><a href="/checkout/">UPI &amp; Auto-Replenish Checkout</a></li>
                </ul>
            </div>

            <div>
                <h4>Compliance &amp; Sourcing</h4>
                <p style="font-size:12.5px;margin-bottom:10px;"><strong>Category:</strong> FSSAI Nutraceutical / Ayurveda Aahara Concept<br>
                <strong>HSN &amp; Tax:</strong> HSN 21069099 &bull; 5% GST Included (Post-Sept 2025 Reform)<br>
                <strong>GMP Blending:</strong> Baddi &amp; Gujarat Certified Clusters<br>
                <strong>Support:</strong> care@rasaaya.in &bull; Mumbai, MH</p>
            </div>
        </div>

        <div class="fssai-disclaimer">
            <strong>Mandatory FSSAI &amp; Regulatory Statement:</strong> RASAAYA products are daily culinary-herbal &amp; nutraceutical wellness infusions formulated to support normal digestive comfort, hydration and relaxation. Not intended to diagnose, treat, cure, or prevent any disease. Contains 0% Senna leaf and 100% root-only <em>Withania somnifera</em> (never Ashwagandha leaf) in Calm &amp; Sleep. Consult a healthcare professional if pregnant, nursing, or on medication. &copy; 2026 RASAAYA Rituals &bull; Prepared by Bombay Marketing Studio, Mumbai.
        </div>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
