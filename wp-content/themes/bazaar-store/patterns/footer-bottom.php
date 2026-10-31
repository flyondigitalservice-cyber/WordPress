<?php
/**
 * Title: Footer copyright line
 * Slug: bazaar-store/footer-bottom
 * Inserter: no
 *
 * Translatable text used by a template. Edit the wording here or in the Site Editor.
 *
 * @package Bazaar
 */

?>
<!-- wp:paragraph {"align":"center","fontSize":"small"} -->
<p class="has-text-align-center has-small-font-size"><?php
/* translators: 1: year, 2: site name */
echo esc_html( sprintf( __( '© %1$s %2$s.', 'bazaar-store' ), wp_date( 'Y' ), get_bloginfo( 'name' ) ) );
?> <?php esc_html_e( 'We accept cards, UPI, net banking and cash on delivery.', 'bazaar-store' ); ?></p>
<!-- /wp:paragraph -->
