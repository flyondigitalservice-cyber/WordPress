<?php
/**
 * Title: About us page
 * Slug: bazaar-electronics/page-about
 * Categories: bazaar-pages
 * Keywords: about us story
 * Viewport Width: 1000
 *
 * @package Bazaar
 */

?>
<!-- wp:paragraph {"textColor":"muted","fontSize":"large"} -->
<p class="has-muted-color has-text-color has-large-font-size"><?php esc_html_e( 'We are a small team that believes everyday products should be well made, fairly priced and easy to buy.', 'bazaar-electronics' ); ?></p>
<!-- /wp:paragraph -->
<!-- wp:heading -->
<h2 class="wp-block-heading"><?php esc_html_e( 'Our story', 'bazaar-electronics' ); ?></h2>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p><?php esc_html_e( 'We started with a simple idea: stock the things people actually use at home, and make sure every order arrives as expected. Tell your own story here: who you are, where you are based and what makes your store different.', 'bazaar-electronics' ); ?></p>
<!-- /wp:paragraph -->
<!-- wp:heading -->
<h2 class="wp-block-heading"><?php esc_html_e( 'What we stand for', 'bazaar-electronics' ); ?></h2>
<!-- /wp:heading -->
<!-- wp:group {"align":"wide","layout":{"type":"grid","minimumColumnWidth":"14rem"}} -->
<div class="wp-block-group alignwide">
<!-- wp:group {"className":"bzr-card"} -->
<div class="wp-block-group bzr-card">
<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading"><?php esc_html_e( 'Quality first', 'bazaar-electronics' ); ?></h3>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p><?php esc_html_e( 'We check products before we list them and drop anything that does not meet our standard.', 'bazaar-electronics' ); ?></p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
<!-- wp:group {"className":"bzr-card"} -->
<div class="wp-block-group bzr-card">
<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading"><?php esc_html_e( 'Fair prices', 'bazaar-electronics' ); ?></h3>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p><?php esc_html_e( 'No inflated list prices. What you see is what you pay.', 'bazaar-electronics' ); ?></p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
<!-- wp:group {"className":"bzr-card"} -->
<div class="wp-block-group bzr-card">
<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading"><?php esc_html_e( 'Service that cares', 'bazaar-electronics' ); ?></h3>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p><?php esc_html_e( 'Questions, problems or returns are handled by real people.', 'bazaar-electronics' ); ?></p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->
<!-- wp:buttons -->
<div class="wp-block-buttons">
<!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' ) ); ?>"><?php esc_html_e( 'Browse the shop', 'bazaar-electronics' ); ?></a></div>
<!-- /wp:button -->
<!-- wp:button {"className":"is-style-outline"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( home_url( '/contact-us/' ) ); ?>"><?php esc_html_e( 'Contact us', 'bazaar-electronics' ); ?></a></div>
<!-- /wp:button -->
</div>
<!-- /wp:buttons -->
