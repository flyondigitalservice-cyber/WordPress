<?php
/**
 * Title: Warranty & service page
 * Slug: bazaar-electronics/page-warranty
 * Categories: bazaar-pages
 * Keywords: warranty service repair
 * Viewport Width: 1000
 *
 * @package Bazaar
 */

$bzr_invoice = bzr_option( 'invoice_label', __( 'GST invoice', 'bazaar-electronics' ) );
?>
<!-- wp:paragraph {"textColor":"muted","fontSize":"large"} -->
<p class="has-muted-color has-text-color has-large-font-size"><?php esc_html_e( 'Every product we sell is covered by the manufacturer\'s warranty. Here is how to use it.', 'bazaar-electronics' ); ?></p>
<!-- /wp:paragraph -->
<!-- wp:heading -->
<h2 class="wp-block-heading"><?php esc_html_e( 'What is covered', 'bazaar-electronics' ); ?></h2>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p><?php esc_html_e( 'Manufacturing defects and hardware faults under normal use, for the period stated by the brand (usually 6 to 24 months from the invoice date).', 'bazaar-electronics' ); ?></p>
<!-- /wp:paragraph -->
<!-- wp:heading -->
<h2 class="wp-block-heading"><?php esc_html_e( 'What is not covered', 'bazaar-electronics' ); ?></h2>
<!-- /wp:heading -->
<!-- wp:list -->
<ul class="wp-block-list">
<!-- wp:list-item -->
<li><?php esc_html_e( 'Physical, liquid or impact damage', 'bazaar-electronics' ); ?></li>
<!-- /wp:list-item -->
<!-- wp:list-item -->
<li><?php esc_html_e( 'Damage from unauthorised repair or modification', 'bazaar-electronics' ); ?></li>
<!-- /wp:list-item -->
<!-- wp:list-item -->
<li><?php esc_html_e( 'Wear and tear on batteries, cables and accessories unless the brand states otherwise', 'bazaar-electronics' ); ?></li>
<!-- /wp:list-item -->
<!-- wp:list-item -->
<li><?php esc_html_e( 'Products without a valid invoice or with tampered serial numbers', 'bazaar-electronics' ); ?></li>
<!-- /wp:list-item -->
</ul>
<!-- /wp:list -->
<!-- wp:heading -->
<h2 class="wp-block-heading"><?php esc_html_e( 'How to claim', 'bazaar-electronics' ); ?></h2>
<!-- /wp:heading -->
<!-- wp:list -->
<ul class="wp-block-list">
<!-- wp:list-item -->
<li><?php esc_html_e( 'Keep your invoice and the product box if possible.', 'bazaar-electronics' ); ?></li>
<!-- /wp:list-item -->
<!-- wp:list-item -->
<li><?php esc_html_e( 'Contact us with your order number, the product and a short description of the fault.', 'bazaar-electronics' ); ?></li>
<!-- /wp:list-item -->
<!-- wp:list-item -->
<li><?php esc_html_e( 'We guide you to the nearest authorised service centre or arrange pickup where available.', 'bazaar-electronics' ); ?></li>
<!-- /wp:list-item -->
<!-- wp:list-item -->
<li><?php esc_html_e( 'Repairs and replacements follow the brand\'s service timelines.', 'bazaar-electronics' ); ?></li>
<!-- /wp:list-item -->
</ul>
<!-- /wp:list -->
<!-- wp:heading -->
<h2 class="wp-block-heading"><?php esc_html_e( 'Need the invoice?', 'bazaar-electronics' ); ?></h2>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p><?php /* translators: %s: invoice label, e.g. "GST invoice" */ echo esc_html( sprintf( __( 'Your %s is emailed with every order and is available under My Account > Orders.', 'bazaar-electronics' ), $bzr_invoice ) ); ?></p>
<!-- /wp:paragraph -->
