<?php
/**
 * Title: Benefits strip
 * Slug: bazaar-boutique/usp-strip
 * Categories: bazaar-store
 * Keywords: usp, shipping, returns, trust
 * Viewport Width: 1400
 *
 * @package Bazaar
 */

?>
<!-- wp:group {"align":"full","backgroundColor":"accent","style":{"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40"}}},"layout":{"type":"constrained","wideSize":"1280px"}} -->
<div class="wp-block-group alignfull has-accent-background-color has-background" style="padding-top:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40)">
	<!-- wp:group {"align":"wide","layout":{"type":"grid","minimumColumnWidth":"12rem"}} -->
	<div class="wp-block-group alignwide">
		<!-- wp:paragraph {"align":"center","className":"bzr-usp","fontSize":"small"} -->
		<p class="has-text-align-center bzr-usp has-small-font-size"><strong><?php esc_html_e( 'Free shipping', 'bazaar-boutique' ); ?></strong><br><?php esc_html_e( 'On orders over your set amount', 'bazaar-boutique' ); ?></p>
		<!-- /wp:paragraph -->
		<!-- wp:paragraph {"align":"center","className":"bzr-usp","fontSize":"small"} -->
		<p class="has-text-align-center bzr-usp has-small-font-size"><strong><?php esc_html_e( 'Cash on delivery', 'bazaar-boutique' ); ?></strong><br><?php esc_html_e( 'Pay when it arrives', 'bazaar-boutique' ); ?></p>
		<!-- /wp:paragraph -->
		<!-- wp:paragraph {"align":"center","className":"bzr-usp","fontSize":"small"} -->
		<p class="has-text-align-center bzr-usp has-small-font-size"><strong><?php esc_html_e( 'Easy returns', 'bazaar-boutique' ); ?></strong><br><?php esc_html_e( '7-day exchange policy', 'bazaar-boutique' ); ?></p>
		<!-- /wp:paragraph -->
		<!-- wp:paragraph {"align":"center","className":"bzr-usp","fontSize":"small"} -->
		<p class="has-text-align-center bzr-usp has-small-font-size"><strong><?php esc_html_e( 'Secure payments', 'bazaar-boutique' ); ?></strong><br><?php esc_html_e( 'Cards, UPI and wallets', 'bazaar-boutique' ); ?></p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->
