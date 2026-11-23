<?php
get_header();
$products = rasaaya_get_products_from_db();
?>

<!-- HERO SECTION -->
<section class="ras-hero">
    <div class="container ras-hero-grid">
        <div>
            <div class="hand-ribbon">स्वाद भी, सेहत भी &mdash; Dadi&rsquo;s nuskhe, minus the mess</div>
            <div class="eyebrow-pill">★ 4.9/5 Across 2,400+ Indian Gut Ritual Members &bull; Zero Senna</div>
            <h1>Ancient Kitchen Wisdom.<br><em>One Tear-Sachet <span class="hand-mark">a Day.<?php echo rasaaya_doodle( 'underline' ); ?></span></em></h1>
            <p class="ras-hero-lead">
                Ditch clumpy churna jars and harsh laxative pills. RASAAYA seals precision-dosed Unjha Isabgol, 3-Fruit Triphala, Roasted Jeera-Hing &amp; Root-Only Ashwagandha in moisture-proof 4-ply sachets that swirl into water in 15 seconds.
            </p>
            <div class="hero-cta-row">
                <button type="button" class="btn btn-saffron" onclick="window.rasAddBySlug('7-day-gut-trial-pack', false)">
                    Start ₹99 7-Day Gut Trial &rarr;
                </button>
                <button type="button" class="btn btn-primary" onclick="window.rasAddBySlug('30-day-gut-starter-kit', false)">
                    Get 30-Day Starter Kit &bull; ₹549
                </button>
                <a href="#gut-quiz" class="btn btn-outline">Take 20s Gut Quiz</a>
                <span class="hand-note hero-cta-note"><?php echo rasaaya_doodle( 'arrow-left' ); ?>start small. ₹99, no lock-in</span>
            </div>

            <div class="hero-trust-grid">
                <div class="trust-mini">
                    <strong>0% Senna &amp; 0g Sugar</strong>
                    Non-habit-forming daily fibre &amp; kitchen herbs
                </div>
                <div class="trust-mini">
                    <strong>4-Ply Moisture Lock</strong>
                    Zero monsoon clumping &bull; Travel-ready tear packs
                </div>
                <div class="trust-mini">
                    <strong>NABL CoA Verified</strong>
                    Heavy-metal &amp; microbial lab-tested every batch
                </div>
            </div>
        </div>

        <div class="hero-visual-wrap">
        <?php echo rasaaya_doodle( 'sprig', 'hero-sprig' ); ?>
        <?php echo rasaaya_doodle( 'sparkle', 'hero-sparkle' ); ?>
        <div class="sticky-note hero-sticky"><span class="tape"></span>tear. swirl. sip.<br><b>15 seconds</b> &#10003;</div>
        <div class="hero-visual-card">
            <img src="<?php echo esc_url( rasaaya_theme_asset( 'assets/images/hero-collection.jpg' ) ); ?>" alt="RASAAYA Ayurvedic Gut & Wellness Sachet Collection">
            <div class="hero-float-badge">
                <div class="hfb-left">
                    <strong>#1 Bestseller: 30-Day Gut Starter Kit</strong>
                    <span>Full Box Gut Reset (30) + Digest+ (30) &bull; Saves ₹99</span>
                </div>
                <button type="button" class="hfb-price" onclick="window.rasAddBySlug('30-day-gut-starter-kit', false)">Add ₹549 +</button>
            </div>
        </div>
        </div>
    </div>
</section>

<!-- BENEFIT RIBBON -->
<div class="benefit-ribbon">
    <div class="container benefit-ribbon-inner">
        <span><b><?php echo rasaaya_doodle( 'tick' ); ?></b> Unjha Psyllium + Classical Triphala</span>
        <span><b><?php echo rasaaya_doodle( 'tick' ); ?></b> 100% Root-Only Ashwagandha (0% Leaf)</span>
        <span><b><?php echo rasaaya_doodle( 'tick' ); ?></b> 15-Second Water/Milk Swirl</span>
        <span><b><?php echo rasaaya_doodle( 'tick' ); ?></b> 5% GST Nutraceutical Pricing</span>
        <span><b><?php echo rasaaya_doodle( 'tick' ); ?></b> Day-25 WhatsApp Refill Nudge</span>
    </div>
</div>

<!-- SHOP ALL RITUALS -->
<section id="shop-rituals" class="sec-wrap has-doodles">
    <?php echo rasaaya_doodle( 'haldi', 'float-doodle fd-haldi' ); ?>
    <?php echo rasaaya_doodle( 'mortar', 'float-doodle fd-mortar' ); ?>
    <div class="container">
        <div class="sec-head">
            <div class="eyebrow-pill">Complete 6-SKU Functional Line + Trial Kits</div>
            <span class="hand-kicker">pick the one that sounds like you <?php echo rasaaya_doodle( 'arrow-down' ); ?></span>
            <h2>Daily Ayurvedic Sachet <span class="hand-circle">Rituals<?php echo rasaaya_doodle( 'circle' ); ?></span></h2>
            <p>Formulated around real Indian meals, late dinners, monsoon weather shifts, and high-screen workdays. Subscribe for 15% off automatic monthly replenishment.</p>
        </div>

        <div class="filter-tabs">
            <button type="button" class="filter-tab active" data-filter="all">All Rituals (8)</button>
            <button type="button" class="filter-tab" data-filter="bundles">Trial &amp; Starter Bundles</button>
            <button type="button" class="filter-tab" data-filter="gut">Gut &amp; Post-Meal</button>
            <button type="button" class="filter-tab" data-filter="skin">Skin &amp; Immunity</button>
            <button type="button" class="filter-tab" data-filter="sleep">Sleep &amp; Hydration</button>
        </div>

        <div class="product-grid">
            <?php foreach ( $products as $prod ) : ?>
                <?php get_template_part( 'template-parts/product', 'card', array( 'product' => $prod ) ); ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ₹99 7-DAY TRIAL CHALLENGE -->
<section class="sec-wrap" style="padding-top:0;">
    <div class="container">
        <div class="trial-banner-card">
            <div>
                <div class="eyebrow-pill" style="background:rgba(229,169,76,0.18);border-color:#E5A94C;color:#FDE68A;">First-Order Discovery Hook &bull; Prepaid Free Delivery</div>
                <h2 style="font-size:32px;font-weight:800;margin-bottom:10px;">Feel Lighter by Day 7 — <span class="hand-chalk">or don’t upgrade.</span></h2>
                <p style="color:#E2E8F0;font-size:15px;">Test both core gut formulations before committing to a full 30-day carton: <strong>7 Sachets of Gut Reset</strong> + <strong>7 Sachets of Digest+</strong> + complimentary entry to our moderated WhatsApp 7-Day Gut Challenge.</p>
                <div class="trial-steps">
                    <div class="t-step"><strong>Days 1–2</strong>Post-lunch Digest+ calms gas &amp; spicy-meal heaviness.</div>
                    <div class="t-step"><strong>Days 3–5</strong>Isabgol + Triphala hydrate &amp; soften overnight transit.</div>
                    <div class="t-step"><strong>Day 7 Unlock</strong>Get ₹99 credited back toward your 30-Day Starter Kit!</div>
                </div>
                <div style="display:flex;gap:12px;flex-wrap:wrap;">
                    <button type="button" class="btn btn-saffron" onclick="window.rasAddBySlug('7-day-gut-trial-pack', false)">Claim ₹99 Trial Pack Now</button>
                    <a href="/7-day-trial/" class="btn btn-outline" style="color:#FFF;border-color:rgba(255,255,255,0.5);">How the Challenge Works</a>
                </div>
            </div>
            <div class="trial-visual">
                <img src="<?php echo esc_url( rasaaya_theme_asset( 'assets/images/sku-trial-pack.jpg' ) ); ?>" alt="7-Day Gut Trial Pack ₹99" style="border-radius:16px;border:2px solid rgba(229,169,76,0.45);box-shadow:0 16px 36px rgba(0,0,0,0.3);">
                <div class="sticky-note trial-sticky"><span class="tape"></span>Not for you?<br>No hard feelings.<br>It was only ₹99 :)</div>
                <?php echo rasaaya_doodle( 'chai', 'trial-chai' ); ?>
            </div>
        </div>
    </div>
</section>

<!-- A NOTE FROM OUR KITCHEN -->
<section class="sec-wrap kitchen-sec">
    <div class="container kitchen-grid">
        <div class="kitchen-art">
            <?php echo rasaaya_doodle( 'dabba' ); ?>
            <p class="hand-caption">Ma&rsquo;s masala dabba &mdash; where every RASAAYA blend started.</p>
        </div>
        <div class="kitchen-letter">
            <span class="tape tape-l"></span><span class="tape tape-r"></span>
            <div class="letter-kicker">a little note from our kitchen</div>
            <p>Every Indian home has a masala dabba, and someone who knows exactly which pinch fixes what. Jeera after a heavy lunch. Haldi in warm milk when you&rsquo;re run down. Triphala before bed &mdash; because Dadi said so.</p>
            <p>We didn&rsquo;t invent any of this. We just measured it properly, got every batch lab-tested, and sealed it into a sachet that fits in your laptop bag.</p>
            <p>No senna. No sugar. No shortcuts. And if something feels off on day three, write to us &mdash; a real person reads every message.</p>
            <div class="letter-sign">with love (&amp; slightly yellow fingers),<br><span>the RASAAYA kitchen, Mumbai <?php echo rasaaya_doodle( 'heart' ); ?></span></div>
        </div>
    </div>
</section>

<!-- INTERACTIVE GUT QUIZ -->
<section id="gut-quiz" class="sec-wrap alt-bg">
    <div class="container">
        <div class="sec-head">
            <div class="eyebrow-pill">20-Second Vaidya Formulation Matcher</div>
            <span class="hand-kicker">be honest &mdash; nobody&rsquo;s watching <?php echo rasaaya_doodle( 'arrow-down' ); ?></span>
            <h2>Not Sure Where to Begin?</h2>
            <p>Select the #1 daily signal your body gives you between 8 AM and 11 PM for an instant sachet protocol.</p>
        </div>

        <div class="quiz-shell">
            <strong style="font-size:14px;color:#113826;text-transform:uppercase;letter-spacing:0.05em;">Step 1 of 1 — Choose Your Primary Wellness Goal:</strong>
            <div class="quiz-options">
                <button type="button" class="quiz-opt" onclick="window.rasSelectQuiz(this, 'bloating')">
                    <strong>1. Post-Lunch Bloating &amp; Sluggish Mornings</strong>
                    <span>Heavy feeling after office thalis, late dinners, irregular mornings</span>
                </button>
                <button type="button" class="quiz-opt" onclick="window.rasSelectQuiz(this, 'regularity')">
                    <strong>2. Gentle Daily Gut Cleanse (Zero Laxatives)</strong>
                    <span>Want predictable, effortless morning clarity without Senna cramps</span>
                </button>
                <button type="button" class="quiz-opt" onclick="window.rasSelectQuiz(this, 'skin')">
                    <strong>3. Dull Skin, Breakouts &amp; Gut-Skin Balance</strong>
                    <span>Looking for internal Amla + Manjistha + Lakadong Haldi radiance</span>
                </button>
                <button type="button" class="quiz-opt" onclick="window.rasSelectQuiz(this, 'sleep')">
                    <strong>4. Wired 11 PM Brain &amp; Light Sleep</strong>
                    <span>Need melatonin-free evening cortisol unwind with warm bedtime milk</span>
                </button>
            </div>
            <div id="ras-quiz-result" class="quiz-result-box"></div>
        </div>
    </div>
</section>

<!-- WHY SACHETS + LIVE NABL COA CHECKER -->
<section id="coa-checker" class="sec-wrap forest-bg">
    <div class="container">
        <div class="two-col-grid">
            <div>
                <div class="eyebrow-pill" style="background:rgba(255,255,255,0.1);color:#FDE68A;border-color:rgba(255,255,255,0.2);">Format Innovation</div>
                <h2 style="font-size:30px;font-weight:800;margin-bottom:14px;">Why Single-Serve Foil Sachets Beat Plastic Churna Jars</h2>
                <p style="color:#CBD5E1;margin-bottom:20px;font-size:14.5px;">In Mumbai &amp; Bengaluru’s 80% monsoon humidity, open herbal jars absorb moisture within 10 days—turning hard, losing volatile oils, and making spoon dosing guesswork.</p>
                <table class="compare-table" style="color:#132019;">
                    <thead>
                        <tr>
                            <th>Feature</th>
                            <th>RASAAYA Sachet</th>
                            <th>Old Churna Jar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>Dose Accuracy</strong></td>
                            <td class="good">Exact 4g / 5g / 6g Fill</td>
                            <td>Messy heaped teaspoons</td>
                        </tr>
                        <tr>
                            <td><strong>Monsoon Freshness</strong></td>
                            <td class="good">4-Ply PET/Alu Barrier</td>
                            <td>Clumps &amp; oxidises fast</td>
                        </tr>
                        <tr>
                            <td><strong>Laxative Safety</strong></td>
                            <td class="good">0% Senna (Daily Safe)</td>
                            <td>Often spiked with Senna</td>
                        </tr>
                        <tr>
                            <td><strong>Office / Flight Carry</strong></td>
                            <td class="good">Fits wallet or laptop sleeve</td>
                            <td>Bulky leaking plastic tub</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="coa-card">
                <div style="font-size:11.5px;font-weight:800;color:#E5A94C;letter-spacing:0.08em;text-transform:uppercase;margin-bottom:6px;">Radical transparency &bull; check any batch yourself</div>
                <h3 style="font-size:24px;font-weight:800;margin-bottom:8px;">Verify Your Batch NABL Lab Certificate (CoA)</h3>
                <p style="font-size:13.5px;color:#CBD5E1;">Every RASAAYA production lot is tested at an ISO/IEC 17025 NABL-accredited laboratory for Lead, Arsenic, Mercury, Moisture &amp; zero Senna adulteration. Enter any batch code below:</p>
                <div class="coa-input-row">
                    <input type="text" id="ras-coa-input" value="RAS-GR-2611" placeholder="e.g. RAS-GR-2611 or RAS-GL-2611">
                    <button type="button" class="btn btn-saffron" onclick="window.rasVerifyBatch()">Check Lab CoA</button>
                </div>
                <div style="font-size:11.5px;color:#94A3B8;margin-bottom:8px;">Try codes: <code>RAS-GR-2611</code> (Gut Reset), <code>RAS-DG-2611</code> (Digest+), <code>RAS-CS-2611</code> (Calm &amp; Sleep)</div>
                <div id="ras-coa-result" class="coa-output">
                    <div style="color:#4ADE80;font-weight:800;margin-bottom:4px;">✓ PASSED — BATCH RAS-GR-2611 (Baddi GMP Cluster)</div>
                    <div style="font-size:12.5px;color:#E2E8F0;">Moisture: 3.8% (&lt;5% limit) &bull; Heavy Metals: Compliant &bull; Sennosides: 0.0% Detected. Click "Check Lab CoA" to query live.</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- SOCIAL PROOF & FAQ -->
<section class="sec-wrap">
    <div class="container">
        <div class="sec-head">
            <div class="eyebrow-pill">Real Habits Across India</div>
            <span class="hand-kicker">in their own words <?php echo rasaaya_doodle( 'arrow-down' ); ?></span>
            <h2>Why 32% of Trial Buyers Auto-Replenish Every Month</h2>
        </div>

        <div class="review-grid notes-grid" style="margin-bottom:58px;">
            <div class="rev-card">
                <div class="rev-stars">★★★★★</div>
                <strong style="display:block;margin-bottom:6px;font-size:15px;">“Zero cramping—unlike every churna I tried before”</strong>
                <p style="font-size:13.5px;color:#37473F;margin-bottom:12px;">Started with the ₹99 7-Day Trial Pack because I hated carrying a Triphala jar on work trips. By Day 4 my mornings felt genuinely light with zero urgency.</p>
                <div class="rev-sign">Rohan Deshmukh, 34 &bull; Lower Parel, Mumbai (Subscribed: Starter Kit)</div>
            </div>
            <div class="rev-card">
                <div class="rev-stars">★★★★★</div>
                <strong style="display:block;margin-bottom:6px;font-size:15px;">“Digest+ lives permanently in my laptop bag now”</strong>
                <p style="font-size:13.5px;color:#37473F;margin-bottom:12px;">Tastes like a crisp, authentic jeera-pudina-kala namak chaas spice mix without fizz or baking soda overload. Kills 3 PM post-biryani slump in 15 minutes.</p>
                <div class="rev-sign">Meghna Nair, 29 &bull; Indiranagar, Bengaluru (Verified Buyer)</div>
            </div>
            <div class="rev-card">
                <div class="rev-stars">★★★★★</div>
                <strong style="display:block;margin-bottom:6px;font-size:15px;">“Calm &amp; Sleep in warm almond milk is magic”</strong>
                <p style="font-size:13.5px;color:#37473F;margin-bottom:12px;">Love that RASAAYA specifies 100% root-only Ashwagandha and publishes the batch CoA right on the site. Subtle chamomile warmth and zero morning brain fog.</p>
                <div class="rev-sign">Dr. Kavita Shah, 41 &bull; Satellite, Ahmedabad (Subscribed: Calm &amp; Sleep)</div>
            </div>
        </div>

        <div class="sec-head faq-head" style="margin-bottom:24px;">
            <?php echo rasaaya_doodle( 'chai', 'faq-chai' ); ?>
            <span class="hand-kicker">still wondering? grab a chai.</span>
            <h2>Frequently Asked Questions</h2>
        </div>
        <div class="faq-list">
            <div class="faq-item open">
                <button type="button" class="faq-q"><span>Does Gut Reset contain Senna leaf or cause laxative dependency?</span><span>+</span></button>
                <div class="faq-a">Never. Many commercial Ayurvedic constipation powders hide <em>Swarnapatri</em> (Senna leaf), which irritates colon nerves and creates dependency. RASAAYA Gut Reset relies strictly on classical 3-fruit Triphala (40%), high-swell Unjha Isabgol husk (30%), Saunf, Jeera, Ajwain and Sendha Namak—safe for gentle, long-term daily use.</div>
            </div>
            <div class="faq-item">
                <button type="button" class="faq-q"><span>How does the 15%-Off Monthly Auto-Replenish &amp; Day-25 WhatsApp reminder work?</span><span>+</span></button>
                <div class="faq-a">Toggle “Auto-Replenish (15% Off)” on any 30-sachet box or Starter Kit in your cart. On Day 25 of your ritual, we message you on WhatsApp so you can confirm, skip, swap flavours, or pause in one tap—zero lock-in.</div>
            </div>
            <div class="faq-item">
                <button type="button" class="faq-q"><span>How are RASAAYA sachets taxed under India’s 2026 GST 2.0 regime?</span><span>+</span></button>
                <div class="faq-a">Following the September 2025 GST 2.0 reform (Notification 9/2025-CT Rate), health supplements and nutraceutical preparations under HSN 21069099 attract just 5% GST (reduced from 18%). All prices displayed on RASAAYA already include 5% GST.</div>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>
