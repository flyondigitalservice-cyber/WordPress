<?php get_header(); ?>
<section class="sec-wrap">
    <div class="container" id="ras-checkout-container">
        <div class="sec-head" style="margin-bottom:32px;">
            <div class="eyebrow-pill">256-Bit Encrypted &bull; HSN 21069099 (5% GST Included)</div>
            <h1>Complete Your Daily Ritual Order</h1>
            <p>Orders submitted here are stored live inside the WordPress database (<code>Customer Orders</code> CPT in WP-Admin).</p>
        </div>

        <div class="two-col-grid">
            <form onsubmit="return window.rasSubmitOrder(event)" style="background:#FFF;border:1px solid #EAE1CF;border-radius:16px;padding:28px;box-shadow:0 10px 30px rgba(0,0,0,0.05);">
                <h3 style="font-size:18px;color:#0A2217;margin-bottom:16px;">1. Delivery &amp; WhatsApp Ritual Details</h3>
                <div style="display:grid;gap:14px;margin-bottom:22px;">
                    <div>
                        <label style="display:block;font-size:12.5px;font-weight:700;margin-bottom:4px;">Full Name *</label>
                        <input id="co_name" type="text" required placeholder="e.g. Aarav Mehta" style="width:100%;padding:11px 14px;border:1px solid #CBD5E1;border-radius:8px;font-family:inherit;">
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                        <div>
                            <label style="display:block;font-size:12.5px;font-weight:700;margin-bottom:4px;">WhatsApp Mobile *</label>
                            <input id="co_phone" type="tel" required placeholder="+91 98200 12345" style="width:100%;padding:11px 14px;border:1px solid #CBD5E1;border-radius:8px;font-family:inherit;">
                        </div>
                        <div>
                            <label style="display:block;font-size:12.5px;font-weight:700;margin-bottom:4px;">City &amp; PIN Code *</label>
                            <input id="co_city" type="text" required placeholder="Mumbai - 400050" style="width:100%;padding:11px 14px;border:1px solid #CBD5E1;border-radius:8px;font-family:inherit;">
                        </div>
                    </div>
                    <div>
                        <label style="display:block;font-size:12.5px;font-weight:700;margin-bottom:4px;">Street / Apartment Address *</label>
                        <input id="co_addr" type="text" required placeholder="Flat 702, Palm Grove, Bandra West" style="width:100%;padding:11px 14px;border:1px solid #CBD5E1;border-radius:8px;font-family:inherit;">
                    </div>
                </div>

                <h3 style="font-size:18px;color:#0A2217;margin-bottom:12px;">2. Payment Mode (India D2C Checkout)</h3>
                <div style="display:grid;gap:10px;margin-bottom:18px;">
                    <label style="display:flex;align-items:center;gap:10px;padding:12px 14px;border:1.5px solid #113826;background:#F2F7F4;border-radius:10px;cursor:pointer;">
                        <input type="radio" name="ras_pay" value="UPI Prepaid (GPay / PhonePe / Cred)" checked onchange="window.renderCheckoutSummary()">
                        <span><strong>UPI Instant Pay (GPay / PhonePe / Paytm)</strong> — <span style="color:#184D35;font-weight:800;">Extra ₹25 Instant Off on ₹199+</span></span>
                    </label>
                    <label style="display:flex;align-items:center;gap:10px;padding:12px 14px;border:1px solid #CBD5E1;border-radius:10px;cursor:pointer;">
                        <input type="radio" name="ras_pay" value="Credit / Debit Card" onchange="window.renderCheckoutSummary()">
                        <span><strong>Credit / Debit Card</strong> — Zero Convenience Fee</span>
                    </label>
                    <label style="display:flex;align-items:center;gap:10px;padding:12px 14px;border:1px solid #CBD5E1;border-radius:10px;cursor:pointer;">
                        <input type="radio" name="ras_pay" value="Cash on Delivery" onchange="window.renderCheckoutSummary()">
                        <span><strong>Cash on Delivery (WhatsApp OTP Confirmed)</strong> — +₹25 COD Handling</span>
                    </label>
                </div>

                <label style="display:flex;align-items:center;gap:8px;font-size:12.5px;color:#37473F;margin-bottom:20px;cursor:pointer;">
                    <input type="checkbox" id="co_wa" checked>
                    <span>Send me my 7-Day Gut Ritual Guide &amp; Day-25 1-tap refill reminder on WhatsApp</span>
                </label>

                <button type="submit" id="ras-place-order-btn" class="btn btn-primary" style="width:100%;font-size:15.5px;">
                    Place Ritual Order &rarr;
                </button>
            </form>

            <div style="background:#FFF;border:1px solid #EAE1CF;border-radius:16px;padding:28px;box-shadow:0 10px 30px rgba(0,0,0,0.05);">
                <h3 style="font-size:18px;color:#0A2217;margin-bottom:14px;">Order Summary &amp; GST 2.0 Breakdown</h3>
                <div id="ras-checkout-summary">Loading your ritual bag...</div>
            </div>
        </div>
    </div>
</section>
<?php get_footer(); ?>
