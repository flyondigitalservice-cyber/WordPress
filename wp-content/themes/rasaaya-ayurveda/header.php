<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
$ras_catalog = rasaaya_get_products_from_db();
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
    <script>
        window.RASAAYA_CATALOG = <?php echo wp_json_encode( $ras_catalog ); ?>;
    </script>
</head>
<body <?php body_class(); ?>>
<?php rasaaya_doodle_defs(); ?>

<?php
$ras_ticker = array(
    'Every herb, every gram — printed on the front',
    '₹99 for 7 days, free shipping',
    'Zero senna. Zero sugar. Checked every batch',
    'Dispatched in 24–48 hours',
    '5% GST already included',
    'Lab reports are public. Ask for any batch',
);
?>
<div class="ras-ticker" aria-label="Announcements">
    <div class="ras-ticker-track">
        <?php for ( $i = 0; $i < 2; $i++ ) : ?>
            <?php foreach ( $ras_ticker as $line ) : ?>
                <span><?php echo esc_html( $line ); ?></span><i aria-hidden="true">✺</i>
            <?php endforeach; ?>
        <?php endfor; ?>
    </div>
</div>

<header class="ras-header">
    <div class="container ras-nav-inner">
        <a href="/" class="ras-logo" aria-label="RASAAYA home">
            <div class="ras-crest">R</div>
            <div class="ras-wordmark">
                <strong>RASAAYA</strong>
                <small>nothing hidden in the dabba</small>
            </div>
        </a>

        <ul class="ras-menu">
            <li><a href="/shop/">Shop</a></li>
            <li><a href="/7-day-trial/" class="pill-trial">₹99 Trial</a></li>
            <li><a href="/#inside">What&rsquo;s Inside</a></li>
            <li><a href="/#gut-quiz">Gut Quiz</a></li>
            <li><a href="/ayurvedic-science/">Lab Reports</a></li>
            <li><a href="/#kitchen">Our Kitchen</a></li>
        </ul>

        <div class="ras-nav-actions">
            <button type="button" class="cart-trigger-btn" onclick="window.rasOpenCart()">
                <span>Bag</span>
                <span class="cart-count-pill js-cart-count">0</span>
            </button>
        </div>
    </div>
</header>
