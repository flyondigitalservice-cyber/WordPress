<?php
/**
 * Site footer.
 *
 * @package luxurywatchs
 */

$lw_has_wc = class_exists( 'WooCommerce' );
?>
</main>

<footer class="lw-footer">
	<div class="lw-container lw-footer__news">
		<div>
			<h2 class="lw-footer__title"><?php esc_html_e( 'Join the Inner Circle', 'luxurywatchs' ); ?></h2>
			<p><?php esc_html_e( 'Early access to drops, private sale prices and styling tips — straight to your inbox.', 'luxurywatchs' ); ?></p>
		</div>
		<form class="lw-news-form" action="<?php echo esc_url( lw_whatsapp_url( 'Hi! Please add me to the LuxuryWatchs VIP list.' ) ); ?>" method="get" target="_blank" data-news>
			<label class="screen-reader-text" for="lw-news-email"><?php esc_html_e( 'Email', 'luxurywatchs' ); ?></label>
			<input id="lw-news-email" type="email" placeholder="<?php esc_attr_e( 'Your email address', 'luxurywatchs' ); ?>" required>
			<button class="lw-btn lw-btn--gold" type="submit"><?php esc_html_e( 'Subscribe', 'luxurywatchs' ); ?></button>
		</form>
	</div>

	<div class="lw-container lw-footer__grid">
		<div class="lw-footer__brand">
			<span class="lw-logo__text">LuxuryWatchs<small><?php esc_html_e( 'Signature', 'luxurywatchs' ); ?></small></span>
			<p><?php esc_html_e( 'Designed for the Indian wrist. Premium automatic and quartz timepieces at honest prices, delivered free across India with Cash on Delivery.', 'luxurywatchs' ); ?></p>
			<div class="lw-social">
				<a href="<?php echo esc_url( get_theme_mod( 'lw_instagram', 'https://instagram.com/' ) ); ?>" target="_blank" rel="noopener">Instagram</a>
				<a href="<?php echo esc_url( get_theme_mod( 'lw_youtube', 'https://youtube.com/' ) ); ?>" target="_blank" rel="noopener">YouTube</a>
				<a href="<?php echo esc_url( get_theme_mod( 'lw_facebook', 'https://facebook.com/' ) ); ?>" target="_blank" rel="noopener">Facebook</a>
			</div>
		</div>
		<div>
			<h3><?php esc_html_e( 'Shop', 'luxurywatchs' ); ?></h3>
			<ul>
				<?php foreach ( array_slice( lw_style_categories(), 0, 6 ) as $c ) : ?>
					<li><a href="<?php echo esc_url( lw_cat_url( $c['slug'] ) ); ?>"><?php echo esc_html( $c['name'] ); ?></a></li>
				<?php endforeach; ?>
			</ul>
		</div>
		<div>
			<h3><?php esc_html_e( 'Help', 'luxurywatchs' ); ?></h3>
			<?php
			if ( has_nav_menu( 'footer' ) ) {
				wp_nav_menu(
					array(
						'theme_location' => 'footer',
						'container'      => false,
						'depth'          => 1,
					)
				);
			} else {
				?>
				<ul>
					<li><a href="<?php echo esc_url( home_url( '/shipping-policy/' ) ); ?>"><?php esc_html_e( 'Shipping Policy', 'luxurywatchs' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/refund-policy/' ) ); ?>"><?php esc_html_e( 'Returns & Refunds', 'luxurywatchs' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/warranty/' ) ); ?>"><?php esc_html_e( 'Warranty', 'luxurywatchs' ); ?></a></li>
					<li><a href="<?php echo esc_url( $lw_has_wc ? wc_get_page_permalink( 'myaccount' ) : wp_login_url() ); ?>"><?php esc_html_e( 'Track Order', 'luxurywatchs' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Contact Us', 'luxurywatchs' ); ?></a></li>
					<li><a href="<?php echo esc_url( get_privacy_policy_url() ? get_privacy_policy_url() : home_url( '/privacy-policy/' ) ); ?>"><?php esc_html_e( 'Privacy Policy', 'luxurywatchs' ); ?></a></li>
				</ul>
			<?php } ?>
		</div>
		<div>
			<h3><?php esc_html_e( 'Talk to us', 'luxurywatchs' ); ?></h3>
			<ul class="lw-footer__contact">
				<li><a href="<?php echo esc_url( lw_whatsapp_url() ); ?>" target="_blank" rel="noopener"><?php echo lw_whatsapp_icon(); // phpcs:ignore ?> <?php echo esc_html( get_theme_mod( 'lw_phone', '+91 99999 99999' ) ); ?></a></li>
				<li><a href="mailto:<?php echo esc_attr( get_theme_mod( 'lw_email', 'care@luxurywatchs.co.in' ) ); ?>"><?php echo esc_html( get_theme_mod( 'lw_email', 'care@luxurywatchs.co.in' ) ); ?></a></li>
				<li><?php esc_html_e( 'Mon–Sat, 10am–8pm IST', 'luxurywatchs' ); ?></li>
			</ul>
			<div class="lw-pay">
				<span>UPI</span><span>GPay</span><span>PhonePe</span><span>Visa</span><span>Mastercard</span><span>RuPay</span><span>COD</span>
			</div>
		</div>
	</div>

	<div class="lw-container lw-footer__bottom">
		<p>&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>. <?php esc_html_e( 'All rights reserved.', 'luxurywatchs' ); ?></p>
		<p class="lw-footer__note"><?php esc_html_e( 'LuxuryWatchs Signature is an independent watch brand. All designs and collection names are our own and are not affiliated with any other watchmaker.', 'luxurywatchs' ); ?></p>
	</div>
</footer>

<a class="lw-wa-float" href="<?php echo esc_url( lw_whatsapp_url() ); ?>" target="_blank" rel="noopener" aria-label="<?php esc_attr_e( 'Chat on WhatsApp', 'luxurywatchs' ); ?>">
	<?php echo lw_whatsapp_icon(); // phpcs:ignore ?>
	<span class="lw-wa-float__tip"><?php esc_html_e( 'Need help choosing?', 'luxurywatchs' ); ?></span>
</a>

<nav class="lw-bottombar" aria-label="<?php esc_attr_e( 'Mobile', 'luxurywatchs' ); ?>">
	<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php echo lw_icon( 'home' ); // phpcs:ignore ?><span><?php esc_html_e( 'Home', 'luxurywatchs' ); ?></span></a>
	<a href="<?php echo esc_url( lw_shop_url() ); ?>"><?php echo lw_icon( 'grid' ); // phpcs:ignore ?><span><?php esc_html_e( 'Shop', 'luxurywatchs' ); ?></span></a>
	<a href="<?php echo esc_url( lw_whatsapp_url() ); ?>" target="_blank" rel="noopener" class="lw-bottombar__wa"><?php echo lw_whatsapp_icon(); // phpcs:ignore ?><span><?php esc_html_e( 'WhatsApp', 'luxurywatchs' ); ?></span></a>
	<a href="<?php echo esc_url( $lw_has_wc ? wc_get_page_permalink( 'myaccount' ) : wp_login_url() ); ?>"><?php echo lw_icon( 'user' ); // phpcs:ignore ?><span><?php esc_html_e( 'Account', 'luxurywatchs' ); ?></span></a>
	<a href="<?php echo esc_url( $lw_has_wc ? wc_get_cart_url() : lw_shop_url() ); ?>"><?php echo lw_icon( 'bag' ); // phpcs:ignore ?><span><?php esc_html_e( 'Cart', 'luxurywatchs' ); ?></span></a>
</nav>

<?php wp_footer(); ?>
</body>
</html>
