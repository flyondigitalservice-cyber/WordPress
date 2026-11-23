<?php get_header(); ?>
<section class="sec-wrap has-doodles">
    <?php echo rasaaya_doodle( 'mortar', 'float-doodle fd-mortar' ); ?>
    <?php echo rasaaya_doodle( 'sprig', 'float-doodle fd-sprig-r' ); ?>
    <div class="container">
        <div class="sec-head">
            <span class="hand-kicker">the nerdy bit (we love it) <?php echo rasaaya_doodle( 'arrow-down' ); ?></span>
            <div class="eyebrow-pill">Botanical Standards &amp; 2026 Regulatory Rigour</div>
            <h1>Classical Ayurveda Meets NABL Analytical Science</h1>
            <p>Formulated to bridge classical Grantha herb synergy with modern food-safety engineering and transparent batch testing.</p>
        </div>

        <div class="review-grid notes-grid" style="margin-bottom:44px;">
            <div class="rev-card">
                <h3 style="color:#0A2217;margin-bottom:8px;">1. Zero Senna Guarantee</h3>
                <p style="font-size:13.5px;color:#37473F;">Unlike harsh over-the-counter laxative powders, RASAAYA never uses <em>Cassia angustifolia</em> (Senna leaf), protecting natural gut motility for daily long-term use.</p>
            </div>
            <div class="rev-card">
                <h3 style="color:#0A2217;margin-bottom:8px;">2. 100% Root-Only Ashwagandha</h3>
                <p style="font-size:13.5px;color:#37473F;">In strict compliance with Ministry of AYUSH &amp; FSSAI advisories, Calm &amp; Sleep uses exclusively pure <em>Withania somnifera</em> root—never cheap aerial leaf extracts.</p>
            </div>
            <div class="rev-card">
                <h3 style="color:#0A2217;margin-bottom:8px;">3. 4-Ply WVTR Moisture Barrier</h3>
                <p style="font-size:13.5px;color:#37473F;">Hygroscopic herbs like Isabgol, Amla and Kokum are sealed in 4-ply PET/Alu/PE laminate sachets keeping moisture under 4.2% across 18 months.</p>
            </div>
        </div>

        <div class="forest-bg" style="border-radius:20px;padding:36px;">
            <h2 style="margin-bottom:10px;">Live Batch Certificate of Analysis (CoA) Checker</h2>
            <p style="color:#CBD5E1;margin-bottom:16px;font-size:14px;">Enter a batch code printed on your RASAAYA mono-carton (e.g. <code>RAS-GR-2611</code>, <code>RAS-GL-2611</code>, <code>RAS-CS-2611</code>):</p>
            <div class="coa-input-row" style="max-width:540px;">
                <input type="text" id="ras-coa-input" value="RAS-GR-2611">
                <button type="button" class="btn btn-saffron" onclick="window.rasVerifyBatch()">Verify CoA</button>
            </div>
            <div id="ras-coa-result" class="coa-output">Click "Verify CoA" to fetch live NABL batch metrics.</div>
        </div>
    </div>
</section>
<?php get_footer(); ?>
