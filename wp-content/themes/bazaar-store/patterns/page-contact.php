<?php
/**
 * Title: Contact page
 * Slug: bazaar-store/page-contact
 * Categories: bazaar-pages
 * Keywords: contact form
 * Viewport Width: 1000
 *
 * @package Bazaar
 */

?>
<!-- wp:paragraph {"textColor":"muted","fontSize":"large"} -->
<p class="has-muted-color has-text-color has-large-font-size"><?php esc_html_e( 'Questions about an order, a product or a bulk purchase? Send us a message and we will reply within one business day.', 'bazaar-store' ); ?></p>
<!-- /wp:paragraph -->
<!-- wp:columns {"align":"wide","style":{"spacing":{"blockGap":{"left":"var:preset|spacing|60"}}}} -->
<div class="wp-block-columns alignwide">
<!-- wp:column {"width":"60%"} -->
<div class="wp-block-column" style="flex-basis:60%">
<!-- wp:shortcode -->
[bzr_contact_form]
<!-- /wp:shortcode -->
</div>
<!-- /wp:column -->
<!-- wp:column {"width":"40%"} -->
<div class="wp-block-column" style="flex-basis:40%">
<!-- wp:group {"className":"bzr-card"} -->
<div class="wp-block-group bzr-card">
<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading"><?php esc_html_e( 'Get in touch', 'bazaar-store' ); ?></h3>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p><strong><?php esc_html_e( 'Email', 'bazaar-store' ); ?></strong><br><a href="mailto:<?php echo esc_attr( bzr_option( 'store_email', get_option( 'admin_email' ) ) ); ?>"><?php echo esc_html( bzr_option( 'store_email', get_option( 'admin_email' ) ) ); ?></a></p>
<!-- /wp:paragraph -->
<!-- wp:paragraph -->
<p><strong><?php esc_html_e( 'Phone', 'bazaar-store' ); ?></strong><br><?php echo esc_html( bzr_option( 'store_phone', '-' ) ); ?></p>
<!-- /wp:paragraph -->
<!-- wp:paragraph -->
<p><strong><?php esc_html_e( 'Address', 'bazaar-store' ); ?></strong><br><?php echo esc_html( bzr_option( 'store_address', '-' ) ); ?></p>
<!-- /wp:paragraph -->
<!-- wp:paragraph -->
<p><strong><?php esc_html_e( 'Hours', 'bazaar-store' ); ?></strong><br><?php echo esc_html( bzr_option( 'store_hours', '-' ) ); ?></p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:column -->
</div>
<!-- /wp:columns -->
