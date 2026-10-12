<?php
/**
 * Mini cart drawer.
 *
 * @package Dastaan
 */

defined( 'ABSPATH' ) || exit;
?>
<div id="mini-cart-drawer" class="drawer drawer--right mini-cart-drawer" aria-hidden="true" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Shopping bag', 'dastaan' ); ?>">
	<div class="drawer__panel">
		<div class="drawer__head">
			<h2 class="drawer__title"><?php esc_html_e( 'Your bag', 'dastaan' ); ?></h2>
			<button class="icon-btn js-close-drawer" type="button" aria-label="<?php esc_attr_e( 'Close bag', 'dastaan' ); ?>"><?php dastaan_the_icon( 'close', array( 'size' => 24 ) ); ?></button>
		</div>
		<div class="mini-cart-drawer__content">
			<?php woocommerce_mini_cart(); ?>
		</div>
	</div>
	<button class="drawer__backdrop js-close-drawer" type="button" tabindex="-1" aria-label="<?php esc_attr_e( 'Close', 'dastaan' ); ?>"></button>
</div>
