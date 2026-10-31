<?php
/**
 * Title: Customer reviews
 * Slug: bazaar-boutique/testimonials
 * Categories: bazaar-store
 * Keywords: testimonials, reviews, quotes
 * Viewport Width: 1400
 * Description: Shows your latest real 4- and 5-star product reviews from WooCommerce. The section hides itself until the store has reviews.
 *
 * @package Bazaar
 */

?>
<!-- wp:group {"align":"full","className":"bzr-reviews-section","backgroundColor":"accent","style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70"}}},"layout":{"type":"constrained","wideSize":"1280px"}} -->
<div class="wp-block-group alignfull bzr-reviews-section has-accent-background-color has-background" style="padding-top:var(--wp--preset--spacing--70);padding-bottom:var(--wp--preset--spacing--70)">
	<!-- wp:heading {"textAlign":"center","align":"wide"} -->
	<h2 class="wp-block-heading alignwide has-text-align-center"><?php esc_html_e( 'Loved by our customers', 'bazaar-boutique' ); ?></h2>
	<!-- /wp:heading -->

	<!-- wp:shortcode -->
	[bzr_reviews count="3" min_rating="4"]
	<!-- /wp:shortcode -->
</div>
<!-- /wp:group -->
