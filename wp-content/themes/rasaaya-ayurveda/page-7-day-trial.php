<?php get_header(); ?>
<section class="sec-wrap">
    <div class="container">
        <div class="trial-banner-card" style="margin-bottom:44px;">
            <div>
                <div class="eyebrow-pill" style="background:rgba(229,169,76,0.2);color:#FDE68A;border-color:#E5A94C;">India’s #1 Gut Trial Funnel &bull; ₹99 All-Inclusive</div>
                <h1 style="font-size:38px;font-weight:800;line-height:1.12;margin-bottom:12px;">The 7-Day RASAAYA Gut Reset Challenge</h1>
                <p style="font-size:15.5px;color:#E2E8F0;margin-bottom:20px;">Why gamble ₹900 on an untested jar? Try <strong>7 Sachets of Gut Reset</strong> (overnight Triphala + Isabgol) + <strong>7 Sachets of Digest+</strong> (post-lunch Jeera-Pudina-Hing) for just ₹99 with FREE prepaid shipping—plus get a ₹99 voucher toward your full 30-Day Starter Kit on Day 7.</p>
                <button type="button" class="btn btn-saffron" onclick="window.rasAddBySlug('7-day-gut-trial-pack', false)">Add ₹99 7-Day Trial Pack to Bag &rarr;</button>
            </div>
            <div class="trial-visual">
                <img src="<?php echo esc_url( rasaaya_theme_asset( 'assets/images/sku-trial-pack.jpg' ) ); ?>" alt="7-Day Trial Pack" style="border-radius:16px;border:2px solid #E5A94C;">
                <div class="sticky-note trial-sticky"><span class="tape"></span>7 days.<br>14 sachets.<br>one honest verdict.</div>
            </div>
        </div>

        <span class="hand-kicker hand-kicker-left">how your week goes <?php echo rasaaya_doodle( 'arrow-down' ); ?></span>
        <div class="review-grid notes-grid">
            <div class="rev-card">
                <strong style="color:#B8761A;font-size:12px;text-transform:uppercase;">Day 1 – Day 2</strong>
                <h3 style="margin:6px 0 8px;color:#0A2217;">Post-Meal Bloat Relief</h3>
                <p style="font-size:13.5px;color:#37473F;">Swirl 1 Digest+ sachet after lunch. Roasted Jeera, Ajwain, Pudina &amp; Hing calm gas and heaviness within 20 minutes.</p>
            </div>
            <div class="rev-card">
                <strong style="color:#B8761A;font-size:12px;text-transform:uppercase;">Day 3 – Day 5</strong>
                <h3 style="margin:6px 0 8px;color:#0A2217;">Gentle Overnight Hydration</h3>
                <p style="font-size:13.5px;color:#37473F;">Take 1 Gut Reset sachet in warm water post-dinner. Unjha Isabgol &amp; Triphala support smooth, cramp-free morning transit.</p>
            </div>
            <div class="rev-card">
                <strong style="color:#B8761A;font-size:12px;text-transform:uppercase;">Day 6 – Day 7</strong>
                <h3 style="margin:6px 0 8px;color:#0A2217;">₹99 Cashback Upgrade</h3>
                <p style="font-size:13.5px;color:#37473F;">Receive your automated WhatsApp Day-7 check-in with ₹99 off the ₹549 30-Day Gut Starter Kit.</p>
            </div>
        </div>
    </div>
</section>
<?php get_footer(); ?>
