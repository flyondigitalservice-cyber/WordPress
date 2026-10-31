<?php
/**
 * Title: Footer content
 * Slug: bazaar-store/footer-content
 * Categories: bazaar-store
 * Keywords: 
 * Viewport Width: 1400
 * Inserter: no
 *
 * @package Bazaar
 */

?>
<?php $bzr_p = bzr_option( 'store_phone' ); $bzr_e = bzr_option( 'store_email', get_option( 'admin_email' ) ); ?><!-- wp:columns {"align":"wide"} -->
<div class="wp-block-columns alignwide">
<!-- wp:column {"width":"34%"} -->
<div class="wp-block-column" style="flex-basis:34%">
<!-- wp:site-title {"level":2,"isLink":false} /-->
<!-- wp:site-tagline /-->
<!-- wp:paragraph {"fontSize":"small"} -->
<p class="has-small-font-size"><?php echo esc_html( $bzr_p ); ?><?php echo $bzr_p ? '<br>' : ''; ?><a href="mailto:<?php echo esc_attr( $bzr_e ); ?>"><?php echo esc_html( $bzr_e ); ?></a></p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->
<!-- wp:column -->
<div class="wp-block-column">
<!-- wp:heading {"level":3,"fontSize":"medium"} -->
<h3 class="wp-block-heading has-medium-font-size"><?php esc_html_e( 'Shop', 'bazaar-store' ); ?></h3>
<!-- /wp:heading -->
<!-- wp:list {"className":"bzr-footer-links"} -->
<ul class="wp-block-list bzr-footer-links">
<!-- wp:list-item -->
<li><a href="<?php echo esc_url( function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' ) ); ?>"><?php esc_html_e( 'All products', 'bazaar-store' ); ?></a></li>
<!-- /wp:list-item -->
<!-- wp:list-item -->
<li><a href="<?php echo esc_url( home_url( '/product-category/kitchen-dining/' ) ); ?>"><?php esc_html_e( 'Kitchen & dining', 'bazaar-store' ); ?></a></li>
<!-- /wp:list-item -->
<!-- wp:list-item -->
<li><a href="<?php echo esc_url( home_url( '/product-category/plastic-ware/' ) ); ?>"><?php esc_html_e( 'Plastic ware', 'bazaar-store' ); ?></a></li>
<!-- /wp:list-item -->
<!-- wp:list-item -->
<li><a href="<?php echo esc_url( home_url( '/product-category/glassware/' ) ); ?>"><?php esc_html_e( 'Glassware', 'bazaar-store' ); ?></a></li>
<!-- /wp:list-item -->
<!-- wp:list-item -->
<li><a href="<?php echo esc_url( home_url( '/product-category/crockery/' ) ); ?>"><?php esc_html_e( 'Crockery', 'bazaar-store' ); ?></a></li>
<!-- /wp:list-item -->
</ul>
<!-- /wp:list -->
</div>
<!-- /wp:column -->
<!-- wp:column -->
<div class="wp-block-column">
<!-- wp:heading {"level":3,"fontSize":"medium"} -->
<h3 class="wp-block-heading has-medium-font-size"><?php esc_html_e( 'Customer service', 'bazaar-store' ); ?></h3>
<!-- /wp:heading -->
<!-- wp:list {"className":"bzr-footer-links"} -->
<ul class="wp-block-list bzr-footer-links">
<!-- wp:list-item -->
<li><a href="<?php echo esc_url( home_url( '/shipping-policy/' ) ); ?>"><?php esc_html_e( 'Shipping policy', 'bazaar-store' ); ?></a></li>
<!-- /wp:list-item -->
<!-- wp:list-item -->
<li><a href="<?php echo esc_url( home_url( '/delivery-information/' ) ); ?>"><?php esc_html_e( 'Delivery information', 'bazaar-store' ); ?></a></li>
<!-- /wp:list-item -->
<!-- wp:list-item -->
<li><a href="<?php echo esc_url( home_url( '/return-refund-policy/' ) ); ?>"><?php esc_html_e( 'Return & refund policy', 'bazaar-store' ); ?></a></li>
<!-- /wp:list-item -->
<!-- wp:list-item -->
<li><a href="<?php echo esc_url( home_url( '/payment-methods/' ) ); ?>"><?php esc_html_e( 'Payment methods', 'bazaar-store' ); ?></a></li>
<!-- /wp:list-item -->
<!-- wp:list-item -->
<li><a href="<?php echo esc_url( home_url( '/contact-us/' ) ); ?>"><?php esc_html_e( 'Contact us', 'bazaar-store' ); ?></a></li>
<!-- /wp:list-item -->
</ul>
<!-- /wp:list -->
</div>
<!-- /wp:column -->
<!-- wp:column -->
<div class="wp-block-column">
<!-- wp:heading {"level":3,"fontSize":"medium"} -->
<h3 class="wp-block-heading has-medium-font-size"><?php esc_html_e( 'Company', 'bazaar-store' ); ?></h3>
<!-- /wp:heading -->
<!-- wp:list {"className":"bzr-footer-links"} -->
<ul class="wp-block-list bzr-footer-links">
<!-- wp:list-item -->
<li><a href="<?php echo esc_url( home_url( '/about-us/' ) ); ?>"><?php esc_html_e( 'About us', 'bazaar-store' ); ?></a></li>
<!-- /wp:list-item -->
<!-- wp:list-item -->
<li><a href="<?php echo esc_url( home_url( '/terms-conditions/' ) ); ?>"><?php esc_html_e( 'Terms & conditions', 'bazaar-store' ); ?></a></li>
<!-- /wp:list-item -->
<!-- wp:list-item -->
<li><a href="<?php echo esc_url( home_url( '/privacy-policy/' ) ); ?>"><?php esc_html_e( 'Privacy policy', 'bazaar-store' ); ?></a></li>
<!-- /wp:list-item -->
<!-- wp:list-item -->
<li><a href="<?php echo esc_url( home_url( '/login/' ) ); ?>"><?php esc_html_e( 'Sign in with OTP', 'bazaar-store' ); ?></a></li>
<!-- /wp:list-item -->
</ul>
<!-- /wp:list -->
</div>
<!-- /wp:column -->
</div>
<!-- /wp:columns -->
