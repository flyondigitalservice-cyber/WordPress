<?php
/**
 * Title: About us page
 * Slug: bazaar-boutique/page-about
 * Categories: bazaar-pages
 * Keywords: about us story
 * Viewport Width: 1000
 *
 * @package Bazaar
 */

?>
<!-- wp:paragraph {"textColor":"muted","fontSize":"large"} -->
<p class="has-muted-color has-text-color has-large-font-size"><?php esc_html_e( 'We are a small boutique that believes beautiful clothing should be well made, fairly priced and easy to buy.', 'bazaar-boutique' ); ?></p>
<!-- /wp:paragraph -->
<!-- wp:heading -->
<h2 class="wp-block-heading"><?php esc_html_e( 'Our story', 'bazaar-boutique' ); ?></h2>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p><?php esc_html_e( 'We started with a simple idea: offer pieces we would love to wear ourselves, and make sure every order fits and arrives as expected. Tell your own story here: who you are, where you are based and what makes your collection different.', 'bazaar-boutique' ); ?></p>
<!-- /wp:paragraph -->
<!-- wp:heading -->
<h2 class="wp-block-heading"><?php esc_html_e( 'What we stand for', 'bazaar-boutique' ); ?></h2>
<!-- /wp:heading -->
<!-- wp:group {"align":"wide","layout":{"type":"grid","minimumColumnWidth":"14rem"}} -->
<div class="wp-block-group alignwide">
<!-- wp:group {"className":"bzr-card"} -->
<div class="wp-block-group bzr-card">
<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading"><?php esc_html_e( 'Quality first', 'bazaar-boutique' ); ?></h3>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p><?php esc_html_e( 'We check fabric, stitching and finish on every piece before it is listed.', 'bazaar-boutique' ); ?></p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
<!-- wp:group {"className":"bzr-card"} -->
<div class="wp-block-group bzr-card">
<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading"><?php esc_html_e( 'Fair prices', 'bazaar-boutique' ); ?></h3>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p><?php esc_html_e( 'No inflated list prices. What you see is what you pay.', 'bazaar-boutique' ); ?></p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
<!-- wp:group {"className":"bzr-card"} -->
<div class="wp-block-group bzr-card">
<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading"><?php esc_html_e( 'Service that cares', 'bazaar-boutique' ); ?></h3>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p><?php esc_html_e( 'Questions, problems or returns are handled by real people.', 'bazaar-boutique' ); ?></p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->
<!-- wp:buttons -->
<div class="wp-block-buttons">
<!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' ) ); ?>"><?php esc_html_e( 'Browse the shop', 'bazaar-boutique' ); ?></a></div>
<!-- /wp:button -->
<!-- wp:button {"className":"is-style-outline"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( home_url( '/contact-us/' ) ); ?>"><?php esc_html_e( 'Contact us', 'bazaar-boutique' ); ?></a></div>
<!-- /wp:button -->
</div>
<!-- /wp:buttons -->
