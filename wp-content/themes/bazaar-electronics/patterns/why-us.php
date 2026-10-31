<?php
/**
 * Title: Why shop with us
 * Slug: bazaar-electronics/why-us
 * Categories: bazaar-store
 * Keywords: why trust
 * Viewport Width: 1400
 *
 * @package Bazaar
 */

$bzr_invoice = bzr_option( 'invoice_label', __( 'GST invoice', 'bazaar-electronics' ) );
?>
<!-- wp:group {"align":"full","backgroundColor":"base","style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70"}}},"layout":{"type":"constrained","wideSize":"1280px"}} -->
<div class="wp-block-group alignfull has-base-background-color has-background" style="padding-top:var(--wp--preset--spacing--70);padding-bottom:var(--wp--preset--spacing--70)">
<!-- wp:heading {"textAlign":"center"} -->
<h2 class="wp-block-heading has-text-align-center"><?php esc_html_e( 'Why buy from us', 'bazaar-electronics' ); ?></h2>
<!-- /wp:heading -->
<!-- wp:group {"align":"wide","layout":{"type":"grid","minimumColumnWidth":"15rem"}} -->
<div class="wp-block-group alignwide">
<!-- wp:group {"className":"bzr-card"} -->
<div class="wp-block-group bzr-card">
<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading"><?php esc_html_e( 'Authorised & genuine', 'bazaar-electronics' ); ?></h3>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p><?php esc_html_e( 'Every product is sealed, original and covered by the brand warranty.', 'bazaar-electronics' ); ?></p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
<!-- wp:group {"className":"bzr-card"} -->
<div class="wp-block-group bzr-card">
<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading"><?php esc_html_e( 'Warranty support', 'bazaar-electronics' ); ?></h3>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p><?php esc_html_e( 'We help you with warranty claims and service centre details.', 'bazaar-electronics' ); ?></p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
<!-- wp:group {"className":"bzr-card"} -->
<div class="wp-block-group bzr-card">
<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading"><?php echo esc_html( $bzr_invoice ); ?></h3>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p><?php esc_html_e( 'A proper tax invoice with every order, ready for business use.', 'bazaar-electronics' ); ?></p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
<!-- wp:group {"className":"bzr-card"} -->
<div class="wp-block-group bzr-card">
<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading"><?php esc_html_e( 'Expert help', 'bazaar-electronics' ); ?></h3>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p><?php esc_html_e( 'Ask us which model suits you before you buy.', 'bazaar-electronics' ); ?></p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->
