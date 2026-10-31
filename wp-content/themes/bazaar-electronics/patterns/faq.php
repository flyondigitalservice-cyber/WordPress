<?php
/**
 * Title: Frequently asked questions
 * Slug: bazaar-electronics/faq
 * Categories: bazaar-store
 * Keywords: faq
 * Viewport Width: 1400
 *
 * @package Bazaar
 */

$bzr_invoice = bzr_option( 'invoice_label', __( 'GST invoice', 'bazaar-electronics' ) );
?>
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70"}}},"layout":{"type":"constrained","wideSize":"1280px"}} -->
<div class="wp-block-group alignfull" style="padding-top:var(--wp--preset--spacing--70);padding-bottom:var(--wp--preset--spacing--70)">
<!-- wp:heading {"textAlign":"center"} -->
<h2 class="wp-block-heading has-text-align-center"><?php esc_html_e( 'Frequently asked questions', 'bazaar-electronics' ); ?></h2>
<!-- /wp:heading -->
<!-- wp:group {"className":"bzr-faq","layout":{"type":"constrained"}} -->
<div class="wp-block-group bzr-faq">
<!-- wp:details -->
<details class="wp-block-details"><summary><?php esc_html_e( 'Are the products genuine?', 'bazaar-electronics' ); ?></summary>
<!-- wp:paragraph {"placeholder":""} -->
<p><?php esc_html_e( 'Yes. We source from authorised channels and every product comes with the manufacturer\'s warranty.', 'bazaar-electronics' ); ?></p>
<!-- /wp:paragraph --></details>
<!-- /wp:details -->
<!-- wp:details -->
<details class="wp-block-details"><summary><?php /* translators: %s: invoice label, e.g. "GST invoice" */ echo esc_html( sprintf( __( 'Will I get a %s?', 'bazaar-electronics' ), $bzr_invoice ) ); ?></summary>
<!-- wp:paragraph {"placeholder":""} -->
<p><?php esc_html_e( 'Yes, a tax invoice is emailed with every order and is available in your account.', 'bazaar-electronics' ); ?></p>
<!-- /wp:paragraph --></details>
<!-- /wp:details -->
<!-- wp:details -->
<details class="wp-block-details"><summary><?php esc_html_e( 'How do I claim warranty?', 'bazaar-electronics' ); ?></summary>
<!-- wp:paragraph {"placeholder":""} -->
<p><?php esc_html_e( 'Keep your invoice and contact us or the brand\'s service centre. Our Warranty & Service page explains the steps.', 'bazaar-electronics' ); ?></p>
<!-- /wp:paragraph --></details>
<!-- /wp:details -->
<!-- wp:details -->
<details class="wp-block-details"><summary><?php esc_html_e( 'What if the product is dead on arrival?', 'bazaar-electronics' ); ?></summary>
<!-- wp:paragraph {"placeholder":""} -->
<p><?php esc_html_e( 'Contact us within 48 hours with an unboxing video and we will arrange a replacement. See the Return policy.', 'bazaar-electronics' ); ?></p>
<!-- /wp:paragraph --></details>
<!-- /wp:details -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->
