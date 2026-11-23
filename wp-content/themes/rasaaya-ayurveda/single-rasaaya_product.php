<?php
get_header();
global $post;
$all = rasaaya_get_products_from_db();
$current = null;
foreach ( $all as $item ) {
    if ( $item['slug'] === $post->post_name ) {
        $current = $item;
        break;
    }
}
if ( ! $current ) {
    $current = $all[0];
}
$sub_price = round( $current['price'] * 0.85 );
?>

<section class="sec-wrap">
    <div class="container">
        <div style="font-size:12.5px;color:#63736B;margin-bottom:18px;">
            <a href="/">Home</a> &nbsp;/&nbsp; <a href="/shop/">Shop Sachets</a> &nbsp;/&nbsp; <strong style="color:#113826;"><?php echo esc_html( $current['title'] ); ?></strong>
        </div>

        <div class="two-col-grid" style="align-items:center;">
            <div style="border-radius:18px;overflow:hidden;border:2px solid #EAE1CF;background:#FFF;box-shadow:0 16px 40px rgba(0,0,0,0.08);">
                <img src="<?php echo esc_url( $current['img'] ); ?>" alt="<?php echo esc_attr( $current['title'] ); ?>" style="width:100%;height:460px;object-fit:cover;">
            </div>

            <div>
                <span class="eyebrow-pill"><?php echo esc_html( $current['badge'] ); ?> &bull; <?php echo esc_html( $current['pack'] ); ?></span>
                <h1 style="font-size:38px;font-weight:800;color:#0A2217;line-height:1.12;margin-bottom:8px;"><?php echo esc_html( $current['title'] ); ?></h1>
                <div style="font-size:15px;font-weight:700;color:#216647;margin-bottom:10px;"><?php echo esc_html( $current['job'] ); ?> &nbsp;&bull;&nbsp; ★ <?php echo esc_html( $current['rating'] ); ?> (<?php echo esc_html( $current['reviews'] ); ?> Verified Reviews)</div>
                <p style="font-size:15.5px;color:#37473F;margin-bottom:20px;"><?php echo esc_html( $current['desc'] ); ?></p>

                <div style="background:#FFF;border:1.5px solid #CFE0D6;border-radius:14px;padding:18px;margin-bottom:20px;">
                    <div style="display:flex;justify-content:space-between;align-items:baseline;margin-bottom:12px;">
                        <div>
                            <span style="font-size:28px;font-weight:800;color:#0A2217;">₹<?php echo esc_html( $current['price'] ); ?></span>
                            <span style="font-size:15px;color:#94A3B8;text-decoration:line-through;margin-left:8px;">MRP ₹<?php echo esc_html( $current['mrp'] ); ?></span>
                        </div>
                        <span style="font-size:12px;font-weight:700;color:#184D35;">5% GST Included (HSN 21069099)</span>
                    </div>

                    <div style="display:flex;gap:12px;flex-wrap:wrap;">
                        <button type="button" class="btn btn-primary" onclick="window.rasAddBySlug('<?php echo esc_js( $current['slug'] ); ?>', false)">
                            Add One-Time Box &bull; ₹<?php echo esc_html( $current['price'] ); ?>
                        </button>
                        <?php if ( $current['sub_eligible'] ) : ?>
                            <button type="button" class="btn btn-saffron" onclick="window.rasAddBySlug('<?php echo esc_js( $current['slug'] ); ?>', true)">
                                Subscribe &amp; Save 15% &bull; ₹<?php echo esc_html( $sub_price ); ?>/mo
                            </button>
                        <?php endif; ?>
                    </div>
                </div>

                <div style="background:#F2F7F4;border-radius:12px;padding:16px;font-size:13.5px;display:grid;gap:10px;">
                    <div><strong style="color:#0A2217;">Active Botanical Breakdown:</strong><br><?php echo esc_html( $current['herbs'] ); ?></div>
                    <div><strong style="color:#0A2217;">15-Second Daily Ritual:</strong><br><?php echo esc_html( $current['ritual'] ); ?></div>
                    <div><strong style="color:#0A2217;">NABL CoA Reference Batch:</strong> <code><?php echo esc_html( $current['coa'] ); ?></code> (0% Senna &bull; Heavy Metals Tested)</div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="sec-wrap alt-bg">
    <div class="container">
        <div class="sec-head">
            <h2>Pair With Another Daily Ritual</h2>
            <p>Orders above ₹499 unlock FREE Express Delivery + 1 Complimentary Digest+ Sample Sachet.</p>
        </div>
        <div class="product-grid">
            <?php foreach ( array_slice( $all, 0, 4 ) as $rel ) : ?>
                <?php get_template_part( 'template-parts/product', 'card', array( 'product' => $rel ) ); ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php get_footer(); ?>
