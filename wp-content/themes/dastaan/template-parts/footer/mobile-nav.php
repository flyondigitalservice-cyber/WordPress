<?php
/**
 * Mobile bottom navigation.
 *
 * @package Dastaan
 */

defined( 'ABSPATH' ) || exit;
if ( ! dastaan_has_wc() ) {
	return;
}
$dastaan_count = WC()->cart ? WC()->cart->get_cart_contents_count() : 0;
?>
<nav class="mobile-bottom-nav" aria-label="<?php esc_attr_e( 'Quick navigation', 'dastaan' ); ?>">
	<a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" <?php echo is_shop() ? 'aria-current="page"' : ''; ?>><?php dastaan_the_icon( 'grid', array( 'size' => 22 ) ); ?><span><?php esc_html_e( 'Shop', 'dastaan' ); ?></span></a>
	<?php if ( dastaan_option( 'header_show_search' ) ) : ?>
		<button type="button" class="js-open-search"><?php dastaan_the_icon( 'search', array( 'size' => 22 ) ); ?><span><?php esc_html_e( 'Search', 'dastaan' ); ?></span></button>
	<?php endif; ?>
	<a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" <?php echo is_account_page() ? 'aria-current="page"' : ''; ?>><?php dastaan_the_icon( 'user', array( 'size' => 22 ) ); ?><span><?php esc_html_e( 'Account', 'dastaan' ); ?></span></a>
	<?php if ( dastaan_option( 'header_show_wishlist' ) && dastaan_wishlist_url() ) : ?>
		<a href="<?php echo dastaan_wishlist_url(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>"><?php dastaan_the_icon( 'heart', array( 'size' => 22 ) ); ?><span><?php esc_html_e( 'Wishlist', 'dastaan' ); ?></span></a>
	<?php endif; ?>
	<button type="button" class="js-open-cart cart-btn"><?php dastaan_the_icon( 'bag', array( 'size' => 22 ) ); ?><span class="header-cart-count<?php echo $dastaan_count ? ' has-items' : ''; ?>"><?php echo esc_html( $dastaan_count ); ?></span><span><?php esc_html_e( 'Bag', 'dastaan' ); ?></span></button>
</nav>
