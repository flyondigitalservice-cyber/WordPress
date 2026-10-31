<?php
/**
 * Title: Benefits strip
 * Slug: bazaar-electronics/usp-strip
 * Categories: bazaar-store
 * Keywords: usp trust warranty
 * Viewport Width: 1400
 *
 * @package Bazaar
 */

$bzr_invoice = bzr_option( 'invoice_label', __( 'GST invoice', 'bazaar-electronics' ) );
?>
<!-- wp:group {"align":"full","backgroundColor":"surface","style":{"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40"}}},"layout":{"type":"constrained","wideSize":"1280px"}} -->
<div class="wp-block-group alignfull has-surface-background-color has-background" style="padding-top:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40)">
<!-- wp:group {"align":"wide","layout":{"type":"grid","minimumColumnWidth":"12rem"}} -->
<div class="wp-block-group alignwide">
<!-- wp:paragraph {"align":"center","className":"bzr-usp","fontSize":"small"} -->
<p class="has-text-align-center bzr-usp has-small-font-size"><strong><?php esc_html_e( '100% genuine', 'bazaar-electronics' ); ?></strong><br><?php esc_html_e( 'Sourced from authorised channels', 'bazaar-electronics' ); ?></p>
<!-- /wp:paragraph -->
<!-- wp:paragraph {"align":"center","className":"bzr-usp","fontSize":"small"} -->
<p class="has-text-align-center bzr-usp has-small-font-size"><strong><?php esc_html_e( 'Brand warranty', 'bazaar-electronics' ); ?></strong><br><?php esc_html_e( 'Valid on every product', 'bazaar-electronics' ); ?></p>
<!-- /wp:paragraph -->
<!-- wp:paragraph {"align":"center","className":"bzr-usp","fontSize":"small"} -->
<p class="has-text-align-center bzr-usp has-small-font-size"><strong><?php echo esc_html( $bzr_invoice ); ?></strong><br><?php esc_html_e( 'Included with every order', 'bazaar-electronics' ); ?></p>
<!-- /wp:paragraph -->
<!-- wp:paragraph {"align":"center","className":"bzr-usp","fontSize":"small"} -->
<p class="has-text-align-center bzr-usp has-small-font-size"><strong><?php esc_html_e( 'Secure payments', 'bazaar-electronics' ); ?></strong><br><?php esc_html_e( 'Cards, UPI, net banking', 'bazaar-electronics' ); ?></p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->
