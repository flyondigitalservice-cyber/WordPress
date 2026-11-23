<?php
get_header();
$products = rasaaya_get_products_from_db();
$img      = function ( $file ) {
    return esc_url( rasaaya_theme_asset( 'assets/images/' . $file ) );
};

// Shop-by-need tiles: one katori each.
$needs = array(
    array( 'gut-reset', 'Gut Reset', 'Mornings that just… happen.', 'Triphala · Isabgol · Saunf', 'sku-gut-reset.jpg', '₹359' ),
    array( 'digest-plus', 'Digest+', 'For the 3 PM biryani regret.', 'Jeera · Pudina · Hing', 'sku-digest-plus.jpg', '₹225' ),
    array( 'glow-within', 'Glow Within', 'Skin starts in the stomach.', 'Amla · Manjistha · Haldi', 'sku-glow.jpg', '₹399' ),
    array( 'immunity-kadha', 'Immunity Kadha', 'Nani&rsquo;s kadha, no boiling.', 'Giloy · Tulsi · Mulethi', 'sku-immunity.jpg', '₹315' ),
    array( 'calm-and-sleep', 'Calm &amp; Sleep', 'For the 11 PM wired brain.', 'Ashwagandha root · Brahmi', 'sku-calm-sleep.jpg', '₹399' ),
    array( 'hydrate-kokum-plus', 'Hydrate Kokum+', 'Konkan summers, in a sachet.', 'Kokum · Nimbu · Sendha namak', 'sku-hydrate.jpg', '₹269' ),
);

// Gut Reset, declared gram by gram (6 g sachet).
$label = array(
    array( 'Triphala', 40, '2.4 g', 'Amla, haritaki &amp; bibhitaki. The three-fruit classic your Dadi swore by.' ),
    array( 'Unjha Isabgol husk', 30, '1.8 g', 'From Unjha, Gujarat. Soaks up water, keeps things moving gently.' ),
    array( 'Saunf', 10, '0.6 g', 'The after-meal mukhwas, for a calmer stomach.' ),
    array( 'Jeera', 10, '0.6 g', 'Roasted, not raw. Tastes like home.' ),
    array( 'Ajwain', 5, '0.3 g', 'A small pinch for gas. Any more would be too much.' ),
    array( 'Sendha namak', 5, '0.3 g', 'Rock salt, so it tastes like chaas, not medicine.' ),
);

$never = array(
    array( 'Senna leaf', 'the laxative hiding in most &ldquo;churnas&rdquo;' ),
    array( 'Added sugar', 'not even the &ldquo;natural&rdquo; kind' ),
    array( 'Maltodextrin', 'cheap filler that bulks up the pack' ),
    array( 'Artificial colour', 'your Gut Reset is supposed to be brown' ),
    array( 'Nature-identical flavour', 'real jeera tastes like jeera' ),
    array( 'Ashwagandha leaf', 'we use root only, as tradition says' ),
);
?>

<!-- HERO -->
<section class="b-hero">
    <div class="container b-hero-grid">
        <div class="b-hero-copy">
            <p class="b-mono-kicker">Daily Ayurvedic sachets &middot; Made in India</p>
            <h1 class="b-display">
                Ayurveda, with<br>
                <span class="hand-mark">nothing hidden<?php echo rasaaya_doodle( 'underline' ); ?></span><br>
                in the dabba.
            </h1>
            <span class="b-aside b-aside-hero"><?php echo rasaaya_doodle( 'arrow-loop' ); ?>not even a pinch of senna</span>
            <p class="b-lead">We print every herb and every gram on the <em>front</em> of the pack. Every batch is lab-tested, and the reports are public. We think that's how it should work.</p>
            <div class="b-cta-row">
                <button type="button" class="b-btn b-btn-haldi" onclick="window.rasAddBySlug('7-day-gut-trial-pack', false)">Try 7 days for ₹99 <span aria-hidden="true">&rarr;</span></button>
                <a href="#inside" class="b-btn b-btn-ghost">See what&rsquo;s inside</a>
            </div>
            <p class="b-mono-small">★ 4.9 from 2,400+ people &middot; free shipping on the trial &middot; no subscription trap</p>
        </div>

        <div class="b-hero-art">
            <div class="b-hero-frame">
                <span class="tape tape-l"></span><span class="tape tape-r"></span>
                <img src="<?php echo $img( 'hero-collection.jpg' ); ?>" alt="RASAAYA Gut Reset and Digest+ boxes with sachets and a glass of herbal water">
            </div>
            <span class="b-callout b-callout-1">Triphala 40%<?php echo rasaaya_doodle( 'arrow-down' ); ?></span>
            <span class="b-callout b-callout-2"><?php echo rasaaya_doodle( 'arrow-left' ); ?>4-ply foil.<br>monsoon-proof.</span>
            <span class="b-callout b-callout-3">swirls in 15 sec <?php echo rasaaya_doodle( 'sparkle' ); ?></span>
            <?php echo rasaaya_doodle( 'sprig', 'b-hero-sprig' ); ?>
        </div>
    </div>
</section>

<!-- SHOP BY NEED -->
<section class="b-sec" id="needs">
    <div class="container">
        <div class="b-head b-head-split">
            <h2 class="b-h2">What&rsquo;s your body<br>telling you?</h2>
            <p class="b-mono-note">Six rituals, one for each thing Indian bodies complain about most. Pick yours. <span class="b-hand-inline">(or take the quiz &darr;)</span></p>
        </div>
        <div class="b-need-grid">
            <?php foreach ( $needs as $n ) : ?>
                <a class="b-need" href="/product/<?php echo esc_attr( $n[0] ); ?>/" style="--pc: <?php echo esc_attr( rasaaya_product_color( $n[0] ) ); ?>;">
                    <div class="b-need-img"><img src="<?php echo $img( $n[4] ); ?>" alt="" loading="lazy"></div>
                    <div class="b-need-body">
                        <span class="b-need-line"><?php echo $n[2]; ?></span>
                        <h3><?php echo $n[1]; ?></h3>
                        <span class="b-need-herbs"><?php echo esc_html( $n[3] ); ?></span>
                        <span class="b-need-foot"><b><?php echo esc_html( $n[5] ); ?></b> <span aria-hidden="true">&rarr;</span></span>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- WHAT'S INSIDE: DECLARED UPFRONT -->
<section class="b-sec b-inside" id="inside">
    <div class="container">
        <div class="b-head">
            <span class="hand-kicker">we&rsquo;ll go first <?php echo rasaaya_doodle( 'arrow-down' ); ?></span>
            <h2 class="b-h2">Here&rsquo;s exactly what&rsquo;s in<br>one Gut Reset sachet.</h2>
            <p class="b-mono-note b-center">Most brands hide this on the back in 6-point type. We think you should see it first.</p>
        </div>

        <div class="b-label-card">
            <div class="b-label-pack">
                <img src="<?php echo $img( 'sku-gut-reset.jpg' ); ?>" alt="RASAAYA Gut Reset box and sachets" loading="lazy">
                <span class="sticky-note b-label-sticky"><span class="tape"></span>6 g sachet.<br>100% of it<br>listed here &rarr;</span>
            </div>
            <div class="b-label-table">
                <div class="b-label-hdr"><span>Ingredient</span><span>Share</span><span>Grams</span></div>
                <?php foreach ( $label as $row ) : ?>
                    <div class="b-label-row">
                        <div class="b-label-name"><strong><?php echo esc_html( $row[0] ); ?></strong><small><?php echo $row[3]; ?></small></div>
                        <div class="b-label-bar"><span style="width: <?php echo (int) $row[1] * 2.5; ?>%;"></span><em><?php echo (int) $row[1]; ?>%</em></div>
                        <div class="b-label-g"><?php echo esc_html( $row[2] ); ?></div>
                    </div>
                <?php endforeach; ?>
                <div class="b-label-total">
                    <span>Total</span><span>100%</span><span>6.0 g</span>
                </div>
                <p class="b-label-end">That&rsquo;s it. That&rsquo;s the whole sachet. <?php echo rasaaya_doodle( 'tick' ); ?></p>
            </div>
        </div>
    </div>
</section>

<!-- NEVER LIST -->
<section class="b-sec b-never">
    <div class="container b-never-grid">
        <div>
            <p class="b-mono-kicker b-on-dark">The not-so-secret recipe</p>
            <h2 class="b-h2 b-on-dark">Things that will<br>never go in.</h2>
            <p class="b-never-sub">We wrote this list before we wrote a single formula. It&rsquo;s printed on the inside flap of every box, so it&rsquo;s hard for us to forget.</p>
        </div>
        <ul class="b-never-list">
            <?php foreach ( $never as $nv ) : ?>
                <li>
                    <span class="b-never-word"><?php echo esc_html( $nv[0] ); ?><?php echo rasaaya_doodle( 'strike' ); ?></span>
                    <span class="b-never-why"><?php echo $nv[1]; ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</section>

<!-- ALL RITUALS -->
<section id="shop-rituals" class="b-sec has-doodles">
    <?php echo rasaaya_doodle( 'haldi', 'float-doodle fd-haldi' ); ?>
    <div class="container">
        <div class="b-head b-head-split">
            <h2 class="b-h2">All rituals.<br><span class="b-h2-soft">No fine print.</span></h2>
            <p class="b-mono-note">Prices include 5% GST. Subscribe on any 30-day box for 15% off, and skip or cancel from WhatsApp in one tap.</p>
        </div>

        <div class="filter-tabs">
            <button type="button" class="filter-tab active" data-filter="all">All (8)</button>
            <button type="button" class="filter-tab" data-filter="bundles">Trial &amp; bundles</button>
            <button type="button" class="filter-tab" data-filter="gut">Gut</button>
            <button type="button" class="filter-tab" data-filter="skin">Skin &amp; immunity</button>
            <button type="button" class="filter-tab" data-filter="sleep">Sleep &amp; hydration</button>
        </div>

        <div class="product-grid">
            <?php foreach ( $products as $prod ) : ?>
                <?php get_template_part( 'template-parts/product', 'card', array( 'product' => $prod ) ); ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ₹99 TRIAL -->
<section class="b-sec b-trial-sec">
    <div class="container">
        <div class="b-trial">
            <div class="b-trial-copy">
                <p class="b-mono-kicker b-on-dark">The ₹99 deal</p>
                <h2 class="b-h2 b-on-dark">Seven days.<br>Fourteen sachets.<br><span class="hand-chalk">One honest verdict.</span></h2>
                <ol class="b-trial-steps">
                    <li><b>Days 1–2</b> Digest+ after lunch. The heaviness eases off.</li>
                    <li><b>Days 3–5</b> Gut Reset after dinner. Mornings get easier.</li>
                    <li><b>Day 7</b> Liked it? Your ₹99 comes off the 30-day kit.</li>
                </ol>
                <div class="b-cta-row">
                    <button type="button" class="b-btn b-btn-haldi" onclick="window.rasAddBySlug('7-day-gut-trial-pack', false)">Get the ₹99 trial <span aria-hidden="true">&rarr;</span></button>
                    <a href="/7-day-trial/" class="b-btn b-btn-ghost-light">How it works</a>
                </div>
            </div>
            <div class="trial-visual">
                <img src="<?php echo $img( 'sku-trial-pack.jpg' ); ?>" alt="RASAAYA 7-Day Gut Trial Pack" loading="lazy">
                <div class="sticky-note trial-sticky"><span class="tape"></span>Not for you?<br>No hard feelings.<br>It was only ₹99 :)</div>
                <?php echo rasaaya_doodle( 'chai', 'trial-chai' ); ?>
            </div>
        </div>
    </div>
</section>

<!-- A NOTE FROM OUR KITCHEN -->
<section class="sec-wrap kitchen-sec" id="kitchen">
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

<!-- GUT QUIZ -->
<section id="gut-quiz" class="b-sec b-quiz-sec">
    <div class="container">
        <div class="b-head">
            <span class="hand-kicker">be honest &mdash; nobody&rsquo;s watching <?php echo rasaaya_doodle( 'arrow-down' ); ?></span>
            <h2 class="b-h2">Not sure where to start?</h2>
            <p class="b-mono-note b-center">One question. Pick what bothers you most between 8 AM and 11 PM.</p>
        </div>

        <div class="quiz-shell">
            <div class="quiz-options">
                <button type="button" class="quiz-opt" onclick="window.rasSelectQuiz(this, 'bloating')">
                    <strong>Heavy after lunch, slow in the morning</strong>
                    <span>Office thalis, late dinners, no fixed routine</span>
                </button>
                <button type="button" class="quiz-opt" onclick="window.rasSelectQuiz(this, 'regularity')">
                    <strong>I want regular mornings, without laxatives</strong>
                    <span>Something gentle that doesn&rsquo;t cramp or create a habit</span>
                </button>
                <button type="button" class="quiz-opt" onclick="window.rasSelectQuiz(this, 'skin')">
                    <strong>Dull skin and breakouts that keep coming back</strong>
                    <span>Looking for help from the inside, not another cream</span>
                </button>
                <button type="button" class="quiz-opt" onclick="window.rasSelectQuiz(this, 'sleep')">
                    <strong>Wide awake at 11 PM, tired at 7 AM</strong>
                    <span>Want to wind down without melatonin grogginess</span>
                </button>
            </div>
            <div id="ras-quiz-result" class="quiz-result-box"></div>
        </div>
    </div>
</section>

<!-- LAB REPORT -->
<section id="coa-checker" class="b-sec b-lab-sec">
    <div class="container b-lab-grid">
        <div>
            <p class="b-mono-kicker">Proof, not promises</p>
            <h2 class="b-h2">Every batch gets a lab report.<br><span class="b-h2-soft">You can read it.</span></h2>
            <p class="b-lead">Each production lot is tested by an NABL-accredited lab for heavy metals, microbes, moisture &mdash; and for senna, in case anyone in the supply chain gets clever. The batch code is printed on your box. Type it in here.</p>
            <div class="coa-input-row">
                <input type="text" id="ras-coa-input" value="RAS-GR-2611" aria-label="Batch code" placeholder="e.g. RAS-GR-2611">
                <button type="button" class="b-btn b-btn-ink" onclick="window.rasVerifyBatch()">Check batch</button>
            </div>
            <p class="b-mono-small">Try RAS-GR-2611 (Gut Reset), RAS-DG-2611 (Digest+) or RAS-CS-2611 (Calm &amp; Sleep)</p>
        </div>
        <div class="b-receipt">
            <div class="b-receipt-head">
                <strong>CERTIFICATE OF ANALYSIS</strong>
                <span>NABL &middot; ISO/IEC 17025 lab</span>
            </div>
            <div id="ras-coa-result" class="coa-output">
                <div class="b-receipt-pass">✓ PASSED &mdash; RAS-GR-2611</div>
                <div>Moisture ........ 3.8% (limit 5.0%)</div>
                <div>Lead ............ &lt;0.4 ppm</div>
                <div>Arsenic ......... &lt;0.2 ppm</div>
                <div>Senna ........... 0.0% detected</div>
                <div>E. coli ......... absent</div>
            </div>
            <div class="b-receipt-foot">Baddi GMP cluster, HP &middot; Nov 2026</div>
        </div>
    </div>
</section>

<!-- REVIEWS -->
<section class="b-sec">
    <div class="container">
        <div class="b-head b-head-split">
            <h2 class="b-h2">Said by real people.<br><span class="b-h2-soft">Not by us.</span></h2>
            <p class="b-mono-note">32% of trial buyers come back every month. These are a few of them, in their own words.</p>
        </div>
        <div class="b-quote-grid">
            <figure class="b-quote">
                <blockquote>&ldquo;Zero cramping. Unlike every churna I tried before.&rdquo;</blockquote>
                <p>I hated carrying a Triphala jar on work trips. By day 4 my mornings were genuinely easy.</p>
                <figcaption><span>Rohan D.</span> Lower Parel, Mumbai &middot; on the Starter Kit</figcaption>
            </figure>
            <figure class="b-quote">
                <blockquote>&ldquo;Digest+ lives in my laptop bag now.&rdquo;</blockquote>
                <p>Tastes like proper jeera-pudina chaas masala, no fizz, no soda. Fixes the post-biryani slump.</p>
                <figcaption><span>Meghna N.</span> Indiranagar, Bengaluru</figcaption>
            </figure>
            <figure class="b-quote">
                <blockquote>&ldquo;They actually publish the lab report. Who does that?&rdquo;</blockquote>
                <p>Root-only ashwagandha, in warm almond milk. I sleep through and wake up without the fog.</p>
                <figcaption><span>Dr. Kavita S.</span> Satellite, Ahmedabad &middot; on Calm &amp; Sleep</figcaption>
            </figure>
        </div>
    </div>
</section>

<!-- FAQ -->
<section class="b-sec b-faq-sec">
    <div class="container">
        <div class="b-head faq-head">
            <?php echo rasaaya_doodle( 'chai', 'faq-chai' ); ?>
            <span class="hand-kicker">still wondering? grab a chai.</span>
            <h2 class="b-h2">Questions we get a lot</h2>
        </div>
        <div class="faq-list">
            <div class="faq-item open">
                <button type="button" class="faq-q"><span>Is there senna in Gut Reset? Will I get dependent on it?</span><span>+</span></button>
                <div class="faq-a">No, and no. A lot of Ayurvedic &ldquo;constipation powders&rdquo; quietly include <em>swarnapatri</em> (senna leaf), which works by irritating the gut and can make you dependent on it. Gut Reset is only Triphala, Unjha Isabgol, saunf, jeera, ajwain and sendha namak &mdash; you can see the exact grams above. Every batch is tested to confirm 0% senna.</div>
            </div>
            <div class="faq-item">
                <button type="button" class="faq-q"><span>How does the subscription work? Can I cancel?</span><span>+</span></button>
                <div class="faq-a">Switch on &ldquo;Subscribe &mdash; 15% off&rdquo; for any 30-sachet box. On day 25 we send you a WhatsApp message, and you reply to confirm, skip, swap or pause. There&rsquo;s no lock-in and no cancellation form.</div>
            </div>
            <div class="faq-item">
                <button type="button" class="faq-q"><span>Why only 5% GST?</span><span>+</span></button>
                <div class="faq-a">Since the September 2025 GST reform, health supplements under HSN 21069099 are taxed at 5% instead of 18%. Every price on this site already includes it &mdash; what you see is what you pay.</div>
            </div>
            <div class="faq-item">
                <button type="button" class="faq-q"><span>Why sachets and not a jar?</span><span>+</span></button>
                <div class="faq-a">In Mumbai or Chennai humidity, an open jar of herbal powder clumps within a couple of weeks and starts losing its essential oils. And nobody measures a &ldquo;heaped teaspoon&rdquo; the same way twice. A sealed 4-ply foil sachet gives you the exact dose, fresh, every time &mdash; and it fits in a wallet.</div>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>
