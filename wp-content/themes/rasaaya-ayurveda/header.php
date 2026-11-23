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

<div class="ras-topbar">
    🇮🇳 <span class="hl">7-DAY GUT RESET + DIGEST+ TRIAL PACK @ ₹99</span> (Prepaid Free Shipping) &nbsp;&bull;&nbsp; Free Express Delivery ≥ ₹499 &nbsp;&bull;&nbsp; <span class="hl">GST 2.0 Applied (5% GST Included)</span>
    <a href="/7-day-trial/">Claim ₹99 Trial →</a>
</div>

<header class="ras-header">
    <div class="container ras-nav-inner">
        <a href="/" class="ras-logo">
            <div class="ras-crest">R</div>
            <div class="ras-wordmark">
                <strong>RASAAYA</strong>
                <small>Ayurvedic Nutraceuticals</small>
            </div>
        </a>

        <ul class="ras-menu">
            <li><a href="/">Home</a></li>
            <li><a href="/shop/">Shop Sachets</a></li>
            <li><a href="/7-day-trial/" class="pill-trial">₹99 7-Day Trial</a></li>
            <li><a href="/#gut-quiz">20s Gut Quiz</a></li>
            <li><a href="/ayurvedic-science/">Herbs &amp; Lab CoA</a></li>
            <li><a href="/portfolio-brands/">Studio Portfolio (7)</a></li>
        </ul>

        <div class="ras-nav-actions">
            <button type="button" class="cart-trigger-btn" onclick="window.rasOpenCart()">
                <span>Ritual Bag</span>
                <span class="cart-count-pill js-cart-count">0</span>
            </button>
        </div>
    </div>
</header>
