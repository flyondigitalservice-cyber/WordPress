<?php
get_header();
$products = rasaaya_get_products_from_db();
?>
<section class="sec-wrap has-doodles">
    <?php echo rasaaya_doodle( 'sprig', 'float-doodle fd-sprig-l' ); ?>
    <?php echo rasaaya_doodle( 'haldi', 'float-doodle fd-haldi' ); ?>
    <div class="container">
        <div class="sec-head">
            <span class="hand-kicker">everything we make, in one place <?php echo rasaaya_doodle( 'arrow-down' ); ?></span>
            <div class="eyebrow-pill">RASAAYA Nutraceutical Catalog &bull; 5% GST Included</div>
            <h1>Shop All Ayurvedic Sachet Rituals</h1>
            <p>Precision single-serve 4-ply foil sachets blended in GMP-certified Indian facilities. Free Shipping on all orders ≥ ₹499 or the ₹99 Prepaid Trial Pack.</p>
        </div>

        <div class="filter-tabs">
            <button type="button" class="filter-tab active" data-filter="all">All Products (8)</button>
            <button type="button" class="filter-tab" data-filter="bundles">Bundles &amp; ₹99 Trial</button>
            <button type="button" class="filter-tab" data-filter="gut">Gut &amp; Digestion</button>
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
<?php get_footer(); ?>
