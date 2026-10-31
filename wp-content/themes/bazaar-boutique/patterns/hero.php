<?php
/**
 * Title: Boutique hero
 * Slug: bazaar-boutique/hero
 * Categories: bazaar-store
 * Keywords: hero, banner, header
 * Viewport Width: 1400
 *
 * @package Bazaar
 */

?>
<!-- wp:group {"align":"full","gradient":"primary-to-contrast","textColor":"base","style":{"spacing":{"padding":{"top":"var:preset|spacing|80","bottom":"var:preset|spacing|80"}}},"layout":{"type":"constrained","wideSize":"1280px"}} -->
<div class="wp-block-group alignfull has-base-color has-primary-to-contrast-gradient-background has-text-color has-background" style="padding-top:var(--wp--preset--spacing--80);padding-bottom:var(--wp--preset--spacing--80)">
	<!-- wp:paragraph {"className":"bzr-eyebrow","textColor":"base"} -->
	<p class="bzr-eyebrow has-base-color has-text-color"><?php esc_html_e( 'New season collection', 'bazaar-boutique' ); ?></p>
	<!-- /wp:paragraph -->

	<!-- wp:heading {"level":1,"fontSize":"huge","textColor":"base"} -->
	<h1 class="wp-block-heading has-base-color has-text-color has-huge-font-size"><?php esc_html_e( 'Dressed for every occasion', 'bazaar-boutique' ); ?></h1>
	<!-- /wp:heading -->

	<!-- wp:paragraph {"fontSize":"large","textColor":"base"} -->
	<p class="has-base-color has-text-color has-large-font-size"><?php esc_html_e( 'Abayas, ethnic wear, western styles, kids and menswear, all in one place.', 'bazaar-boutique' ); ?></p>
	<!-- /wp:paragraph -->

	<!-- wp:buttons -->
	<div class="wp-block-buttons">
		<!-- wp:button {"backgroundColor":"base","textColor":"contrast"} -->
		<div class="wp-block-button"><a class="wp-block-button__link has-contrast-color has-base-background-color has-text-color has-background wp-element-button" href="<?php echo esc_url( function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' ) ); ?>"><?php esc_html_e( 'Shop now', 'bazaar-boutique' ); ?></a></div>
		<!-- /wp:button -->
	</div>
	<!-- /wp:buttons -->
</div>
<!-- /wp:group -->
