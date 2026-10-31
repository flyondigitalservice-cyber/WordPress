<?php
/**
 * Title: Call to action
 * Slug: bazaar-boutique/cta
 * Categories: bazaar-store
 * Keywords: cta, contact, whatsapp, help
 * Viewport Width: 1400
 *
 * @package Bazaar
 */

?>
<!-- wp:group {"align":"full","backgroundColor":"contrast","textColor":"base","style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull has-base-color has-contrast-background-color has-text-color has-background" style="padding-top:var(--wp--preset--spacing--70);padding-bottom:var(--wp--preset--spacing--70)">
	<!-- wp:heading {"textAlign":"center","textColor":"base"} -->
	<h2 class="wp-block-heading has-text-align-center has-base-color has-text-color"><?php esc_html_e( 'Need help choosing?', 'bazaar-boutique' ); ?></h2>
	<!-- /wp:heading -->
	<!-- wp:paragraph {"align":"center","textColor":"base"} -->
	<p class="has-text-align-center has-base-color has-text-color"><?php esc_html_e( 'Message us on WhatsApp for sizing, styling and order help.', 'bazaar-boutique' ); ?></p>
	<!-- /wp:paragraph -->
	<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
	<div class="wp-block-buttons">
		<!-- wp:button {"backgroundColor":"base","textColor":"contrast"} -->
		<div class="wp-block-button"><a class="wp-block-button__link has-contrast-color has-base-background-color has-text-color has-background wp-element-button" href="<?php echo esc_url( function_exists( 'bzr_whatsapp_url' ) && bzr_whatsapp_url() ? bzr_whatsapp_url() : home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Chat with us', 'bazaar-boutique' ); ?></a></div>
		<!-- /wp:button -->
	</div>
	<!-- /wp:buttons -->
</div>
<!-- /wp:group -->
