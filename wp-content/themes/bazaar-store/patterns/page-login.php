<?php
/**
 * Title: OTP login page
 * Slug: bazaar-store/page-login
 * Categories: bazaar-pages
 * Keywords: login otp sign in
 * Viewport Width: 800
 *
 * @package Bazaar
 */

?>
<!-- wp:shortcode -->
[bzr_otp_login]
<!-- /wp:shortcode -->
<!-- wp:paragraph {"fontSize":"small"} -->
<p class="has-small-font-size"><?php esc_html_e( 'Prefer a password?', 'bazaar-store' ); ?> <a href="<?php echo esc_url( function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : wp_login_url() ); ?>"><?php esc_html_e( 'Sign in with your password', 'bazaar-store' ); ?></a></p>
<!-- /wp:paragraph -->
