<?php
/**
 * Title: Call to action
 * Slug: bazaar-electronics/cta
 * Categories: bazaar-store
 * Keywords: cta
 * Viewport Width: 1400
 *
 * @package Bazaar
 */

?>
<!-- wp:group {"align":"full","backgroundColor":"contrast","textColor":"base","style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull has-base-color has-contrast-background-color has-text-color has-background" style="padding-top:var(--wp--preset--spacing--70);padding-bottom:var(--wp--preset--spacing--70)">
<!-- wp:heading {"textAlign":"center","textColor":"base"} -->
<h2 class="wp-block-heading has-text-align-center has-base-color has-text-color"><?php esc_html_e( 'Need help choosing?', 'bazaar-electronics' ); ?></h2>
<!-- /wp:heading -->
<!-- wp:paragraph {"align":"center","textColor":"base"} -->
<p class="has-text-align-center has-base-color has-text-color"><?php esc_html_e( 'Chat with us for recommendations, bulk orders and business pricing.', 'bazaar-electronics' ); ?></p>
<!-- /wp:paragraph -->
<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-buttons">
<!-- wp:button {"backgroundColor":"base","textColor":"contrast"} -->
<div class="wp-block-button"><a class="wp-block-button__link has-base-background-color has-background has-contrast-color has-text-color wp-element-button" href="<?php echo esc_url( home_url( '/contact-us/' ) ); ?>"><?php esc_html_e( 'Contact us', 'bazaar-electronics' ); ?></a></div>
<!-- /wp:button -->
</div>
<!-- /wp:buttons -->
</div>
<!-- /wp:group -->
