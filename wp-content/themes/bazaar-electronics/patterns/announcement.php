<?php
/**
 * Title: Announcement bar text
 * Slug: bazaar-electronics/announcement
 * Categories: bazaar-store
 * Inserter: no
 *
 * @package Bazaar
 */

$bzr_invoice = bzr_option( 'invoice_label', __( 'GST invoice', 'bazaar-electronics' ) );
?>
<!-- wp:paragraph {"align":"center","fontSize":"small"} -->
<p class="has-text-align-center has-small-font-size"><?php
	/* translators: %s: invoice label from Store Options, e.g. "GST invoice" */
	echo esc_html( sprintf( __( '100%% genuine products · Brand warranty · %s on every order', 'bazaar-electronics' ), $bzr_invoice ) );
?></p>
<!-- /wp:paragraph -->
