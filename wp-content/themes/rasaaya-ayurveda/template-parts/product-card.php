<?php
if ( ! defined( 'ABSPATH' ) || empty( $args['product'] ) ) {
    return;
}
$p = $args['product'];
$save_amt = max( 0, $p['mrp'] - $p['price'] );
$sub_price = round( $p['price'] * 0.85 );
?>
<article class="prod-card" data-cat="<?php echo esc_attr( $p['cat'] ); ?>">
    <a href="<?php echo esc_url( $p['url'] ); ?>" class="prod-img-box">
        <img src="<?php echo esc_url( $p['img'] ); ?>" alt="<?php echo esc_attr( $p['title'] ); ?>" loading="lazy">
        <span class="prod-badge"><?php echo esc_html( $p['badge'] ); ?></span>
        <?php if ( $save_amt > 0 ) : ?>
            <span class="prod-save-pill">SAVE ₹<?php echo esc_html( $save_amt ); ?></span>
        <?php endif; ?>
    </a>
    <div class="prod-body">
        <div class="prod-meta-top">
            <span><?php echo esc_html( $p['pack'] ); ?></span>
            <span class="prod-rating">★ <?php echo esc_html( $p['rating'] ); ?> (<?php echo esc_html( $p['reviews'] ); ?>)</span>
        </div>
        <a href="<?php echo esc_url( $p['url'] ); ?>">
            <h3 class="prod-title"><?php echo esc_html( $p['title'] ); ?></h3>
        </a>
        <div class="prod-job"><?php echo esc_html( $p['job'] ); ?></div>
        <p class="prod-herbs"><strong>Key Herbs:</strong> <?php echo esc_html( $p['herbs'] ); ?></p>

        <div class="prod-price-row">
            <div>
                <span class="price-now">₹<?php echo esc_html( $p['price'] ); ?></span>
                <?php if ( $p['mrp'] > $p['price'] ) : ?>
                    <span class="price-mrp">₹<?php echo esc_html( $p['mrp'] ); ?></span>
                <?php endif; ?>
            </div>
            <?php if ( $p['sub_eligible'] ) : ?>
                <span class="sub-hint">Sub: ₹<?php echo esc_html( $sub_price ); ?>/mo</span>
            <?php else : ?>
                <span class="sub-hint">Free Shipping</span>
            <?php endif; ?>
        </div>

        <div class="prod-btn-group">
            <button type="button" class="add-cart-btn" onclick="window.rasAddBySlug('<?php echo esc_js( $p['slug'] ); ?>', false)">
                Add to Bag &bull; ₹<?php echo esc_html( $p['price'] ); ?>
            </button>
            <?php if ( $p['sub_eligible'] ) : ?>
                <button type="button" class="sub-quick-btn" onclick="window.rasAddBySlug('<?php echo esc_js( $p['slug'] ); ?>', true)" title="Subscribe & Save 15%">
                    −15% Sub
                </button>
            <?php else : ?>
                <a href="<?php echo esc_url( $p['url'] ); ?>" class="sub-quick-btn" style="display:inline-flex;align-items:center;">Details</a>
            <?php endif; ?>
        </div>
    </div>
</article>
