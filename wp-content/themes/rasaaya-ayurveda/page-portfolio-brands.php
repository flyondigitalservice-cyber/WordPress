<?php
get_header();
$portfolio = array(
    array(
        'cat'   => 'Category 1A • Nutraceuticals (LIVE WORDPRESS SITE)',
        'name'  => 'RASAAYA',
        'tag'   => 'Classical Ayurveda, Modern Daily Sachet Rituals.',
        'offer' => '₹99 7-Day Gut Trial Pack & ₹549 30-Day Gut Starter Kit • 5% GST (HSN 21069099)',
        'img'   => 'hero-collection.jpg',
        'live'  => true,
    ),
    array(
        'cat'   => 'Category 1B • Herbal Personal Care',
        'name'  => 'SNANA BOTANICALS',
        'tag'   => 'Ditch Harsh Alkaline Soap. Pure pH 5.5 Herbal Shower Rituals.',
        'offer' => '₹649 His & Hers Shower Duo (Mint-Neem + Ubtan Glow) + ₹499 500ml Refill • 18% GST',
        'img'   => 'brand-1b-snana.jpg',
        'live'  => false,
    ),
    array(
        'cat'   => 'Category 2 • Home & Living',
        'name'  => 'AANGAN & CO.',
        'tag'   => 'Hand-Stamped in Sanganer. Hand-Poured for Modern Indian Homes.',
        'offer' => '₹1,799 Cotton Block-Print Bedroom Bundle & ₹1,499 Soy Candle + Brass Urli Gift Box • 5% GST',
        'img'   => 'brand-2-aangan.jpg',
        'live'  => false,
    ),
    array(
        'cat'   => 'Category 3 • Fashion & Screen Eyewear',
        'name'  => 'KAIRO OPTICS',
        'tag'   => 'Polarised for Indian Highways. Engineered for 9-to-9 Screens.',
        'offer' => '₹1,799 Day & Screen Combo (UV400 Polarised + Zero-Power Anti-Glare) • 18% GST',
        'img'   => 'brand-3-kairo.jpg',
        'live'  => false,
    ),
    array(
        'cat'   => 'Category 4 • Pet Grooming & Walk Gear',
        'name'  => 'PAWVEDA',
        'tag'   => 'Canine pH 7.0 Herbal Coat Care & Deep-Chest Indie Walk Gear.',
        'offer' => '₹799 Complete Bath Day Kit & ₹999 First Walk Harness Starter Kit • 18% GST',
        'img'   => 'brand-4-pawveda.jpg',
        'live'  => false,
    ),
    array(
        'cat'   => 'Category 5 • Demi-Fine Sterling Silver',
        'name'  => 'CHANDNI 925',
        'tag'   => 'Pure 92.5% Sterling Silver. BIS-HUID Verified. Made for Every Day.',
        'offer' => '₹1,999 48h Express Initial Necklace & ₹3,999 Everyday Trio Gift Box • 3% GST',
        'img'   => 'brand-5-chandni.jpg',
        'live'  => false,
    ),
    array(
        'cat'   => 'Category 6 • Active & Home-Pilates Add-On',
        'name'  => 'RASAAYA MOVE',
        'tag'   => 'Mindful Home Movement. Engineered to Pair with Your Daily Ritual.',
        'offer' => '₹549 Workout + Hydrate Bundle & ₹1,999 Complete Home Workout Kit • 18% GST',
        'img'   => 'brand-6-move.jpg',
        'live'  => false,
    ),
);
?>
<section class="sec-wrap">
    <div class="container">
        <div class="sec-head">
            <div class="eyebrow-pill">Bombay Marketing Studio &bull; 2026 D2C Venture Concepts</div>
            <h1>All 7 Sample Brand Identities &amp; Studio Mockups</h1>
            <p>Per launch roadmap, Category 1A (<strong>RASAAYA</strong>) is deployed as this full WordPress storefront for review before rolling out storefronts for Categories 1B–6.</p>
        </div>

        <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:26px;">
            <?php foreach ( $portfolio as $b ) : ?>
                <div style="background:#FFF;border:1.5px solid <?php echo $b['live'] ? '#C88A2B' : '#EAE1CF'; ?>;border-radius:16px;overflow:hidden;box-shadow:0 12px 32px rgba(0,0,0,0.06);">
                    <img src="<?php echo esc_url( rasaaya_theme_asset( 'assets/images/' . $b['img'] ) ); ?>" alt="<?php echo esc_attr( $b['name'] ); ?>" style="width:100%;height:270px;object-fit:cover;">
                    <div style="padding:22px;">
                        <div style="font-size:11px;font-weight:800;color:#B8761A;text-transform:uppercase;letter-spacing:0.06em;margin-bottom:4px;"><?php echo esc_html( $b['cat'] ); ?></div>
                        <h3 style="font-size:22px;font-weight:800;color:#0A2217;margin-bottom:4px;"><?php echo esc_html( $b['name'] ); ?></h3>
                        <p style="font-size:14px;font-weight:600;color:#216647;margin-bottom:8px;">“<?php echo esc_html( $b['tag'] ); ?>”</p>
                        <p style="font-size:13px;color:#37473F;margin-bottom:14px;"><?php echo esc_html( $b['offer'] ); ?></p>
                        <?php if ( $b['live'] ) : ?>
                            <a href="/" class="btn btn-primary" style="padding:9px 18px;font-size:13px;">Active WordPress Storefront &rarr;</a>
                        <?php else : ?>
                            <span style="display:inline-block;padding:7px 14px;border-radius:999px;background:#F5F0E5;font-size:12px;font-weight:700;color:#63736B;">Brand Ready &bull; Queued After 1A Review</span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php get_footer(); ?>
