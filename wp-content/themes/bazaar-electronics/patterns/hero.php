<?php
/**
 * Title: Electronics hero
 * Slug: bazaar-electronics/hero
 * Categories: bazaar-store
 * Keywords: hero banner
 * Viewport Width: 1400
 *
 * @package Bazaar
 */

$bzr_invoice = bzr_option( 'invoice_label', __( 'GST invoice', 'bazaar-electronics' ) );
?>
<!-- wp:group {"align":"full","backgroundColor":"contrast","textColor":"base","style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70"}}},"layout":{"type":"constrained","wideSize":"1280px"}} -->
<div class="wp-block-group alignfull has-base-color has-contrast-background-color has-text-color has-background" style="padding-top:var(--wp--preset--spacing--70);padding-bottom:var(--wp--preset--spacing--70)">
<!-- wp:columns {"align":"wide","verticalAlignment":"center","style":{"spacing":{"blockGap":{"left":"var:preset|spacing|60"}}}} -->
<div class="wp-block-columns alignwide are-vertically-aligned-center">
<!-- wp:column {"verticalAlignment":"center","width":"55%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:55%">
<!-- wp:paragraph {"className":"bzr-eyebrow","textColor":"secondary"} -->
<p class="bzr-eyebrow has-secondary-color has-text-color"><?php esc_html_e( 'New arrivals every week', 'bazaar-electronics' ); ?></p>
<!-- /wp:paragraph -->
<!-- wp:heading {"level":1,"textColor":"base","fontSize":"huge"} -->
<h1 class="wp-block-heading has-base-color has-text-color has-huge-font-size"><?php esc_html_e( 'Genuine tech. Honest prices.', 'bazaar-electronics' ); ?></h1>
<!-- /wp:heading -->
<!-- wp:paragraph {"textColor":"base","fontSize":"large"} -->
<p class="has-base-color has-text-color has-large-font-size"><?php /* translators: %s: invoice label, e.g. "GST invoice" */ echo esc_html( sprintf( __( 'Phones, laptops, audio and accessories from trusted brands, with warranty and a proper %s on every order.', 'bazaar-electronics' ), $bzr_invoice ) ); ?></p>
<!-- /wp:paragraph -->
<!-- wp:buttons -->
<div class="wp-block-buttons">
<!-- wp:button {"backgroundColor":"base","textColor":"contrast"} -->
<div class="wp-block-button"><a class="wp-block-button__link has-base-background-color has-background has-contrast-color has-text-color wp-element-button" href="<?php echo esc_url( function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' ) ); ?>"><?php esc_html_e( 'Shop all products', 'bazaar-electronics' ); ?></a></div>
<!-- /wp:button -->
<!-- wp:button {"className":"is-style-outline"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( home_url( '/compare/' ) ); ?>"><?php esc_html_e( 'Compare models', 'bazaar-electronics' ); ?></a></div>
<!-- /wp:button -->
</div>
<!-- /wp:buttons -->
</div>
<!-- /wp:column -->
<!-- wp:column {"verticalAlignment":"center","width":"45%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:45%">
<!-- wp:cover {"overlayColor":"primary","minHeight":420,"contentPosition":"center center"} -->
<div class="wp-block-cover" style="min-height:420px"><span aria-hidden="true" class="wp-block-cover__background has-primary-background-color has-background-dim-100 has-background-dim"></span><div class="wp-block-cover__inner-container">
<!-- wp:paragraph {"align":"center","textColor":"base","fontSize":"small"} -->
<p class="has-text-align-center has-base-color has-text-color has-small-font-size"><?php esc_html_e( 'Replace with your hero product photo', 'bazaar-electronics' ); ?></p>
<!-- /wp:paragraph -->
</div></div>
<!-- /wp:cover -->
</div>
<!-- /wp:column -->
</div>
<!-- /wp:columns -->
</div>
<!-- /wp:group -->
