<?php
/**
 * Title: Shop by category
 * Slug: bazaar-boutique/category-grid
 * Categories: bazaar-store
 * Keywords: categories, collections, grid
 * Viewport Width: 1400
 *
 * @package Bazaar
 */

?>
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70"}}},"layout":{"type":"constrained","wideSize":"1280px"}} -->
<div class="wp-block-group alignfull" style="padding-top:var(--wp--preset--spacing--70);padding-bottom:var(--wp--preset--spacing--70)">
	<!-- wp:heading {"textAlign":"center","align":"wide"} -->
	<h2 class="wp-block-heading alignwide has-text-align-center"><?php esc_html_e( 'Shop by category', 'bazaar-boutique' ); ?></h2>
	<!-- /wp:heading -->

	<!-- wp:group {"align":"wide","layout":{"type":"grid","minimumColumnWidth":"14rem"}} -->
	<div class="wp-block-group alignwide">
		<!-- wp:cover {"overlayColor":"accent","minHeight":360,"contentPosition":"bottom left","className":"bzr-tile"} -->
		<div class="wp-block-cover has-custom-content-position is-position-bottom-left bzr-tile" style="min-height:360px"><span aria-hidden="true" class="wp-block-cover__background has-accent-background-color has-background-dim-100 has-background-dim"></span><div class="wp-block-cover__inner-container">
			<!-- wp:heading {"level":3} -->
			<h3 class="wp-block-heading"><a href="<?php echo esc_url( home_url( '/product-category/abayas/' ) ); ?>"><?php esc_html_e( "Abayas", 'bazaar-boutique' ); ?></a></h3>
			<!-- /wp:heading -->
		</div></div>
		<!-- /wp:cover -->

		<!-- wp:cover {"overlayColor":"accent","minHeight":360,"contentPosition":"bottom left","className":"bzr-tile"} -->
		<div class="wp-block-cover has-custom-content-position is-position-bottom-left bzr-tile" style="min-height:360px"><span aria-hidden="true" class="wp-block-cover__background has-accent-background-color has-background-dim-100 has-background-dim"></span><div class="wp-block-cover__inner-container">
			<!-- wp:heading {"level":3} -->
			<h3 class="wp-block-heading"><a href="<?php echo esc_url( home_url( '/product-category/ethnic-wear/' ) ); ?>"><?php esc_html_e( "Ethnic wear", 'bazaar-boutique' ); ?></a></h3>
			<!-- /wp:heading -->
		</div></div>
		<!-- /wp:cover -->

		<!-- wp:cover {"overlayColor":"accent","minHeight":360,"contentPosition":"bottom left","className":"bzr-tile"} -->
		<div class="wp-block-cover has-custom-content-position is-position-bottom-left bzr-tile" style="min-height:360px"><span aria-hidden="true" class="wp-block-cover__background has-accent-background-color has-background-dim-100 has-background-dim"></span><div class="wp-block-cover__inner-container">
			<!-- wp:heading {"level":3} -->
			<h3 class="wp-block-heading"><a href="<?php echo esc_url( home_url( '/product-category/western-wear/' ) ); ?>"><?php esc_html_e( "Western wear", 'bazaar-boutique' ); ?></a></h3>
			<!-- /wp:heading -->
		</div></div>
		<!-- /wp:cover -->

		<!-- wp:cover {"overlayColor":"accent","minHeight":360,"contentPosition":"bottom left","className":"bzr-tile"} -->
		<div class="wp-block-cover has-custom-content-position is-position-bottom-left bzr-tile" style="min-height:360px"><span aria-hidden="true" class="wp-block-cover__background has-accent-background-color has-background-dim-100 has-background-dim"></span><div class="wp-block-cover__inner-container">
			<!-- wp:heading {"level":3} -->
			<h3 class="wp-block-heading"><a href="<?php echo esc_url( home_url( '/product-category/kids-wear/' ) ); ?>"><?php esc_html_e( "Kids wear", 'bazaar-boutique' ); ?></a></h3>
			<!-- /wp:heading -->
		</div></div>
		<!-- /wp:cover -->

		<!-- wp:cover {"overlayColor":"accent","minHeight":360,"contentPosition":"bottom left","className":"bzr-tile"} -->
		<div class="wp-block-cover has-custom-content-position is-position-bottom-left bzr-tile" style="min-height:360px"><span aria-hidden="true" class="wp-block-cover__background has-accent-background-color has-background-dim-100 has-background-dim"></span><div class="wp-block-cover__inner-container">
			<!-- wp:heading {"level":3} -->
			<h3 class="wp-block-heading"><a href="<?php echo esc_url( home_url( '/product-category/mens-fashion/' ) ); ?>"><?php esc_html_e( "Men's fashion", 'bazaar-boutique' ); ?></a></h3>
			<!-- /wp:heading -->
		</div></div>
		<!-- /wp:cover -->

	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->
