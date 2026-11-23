<?php
/**
 * Baystore Amazon Lean — Footer, Cart Drawer & Pincode Modal
 *
 * @package Baystore_Amazon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$shop_url     = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );
$checkout_url = function_exists( 'wc_get_checkout_url' ) ? wc_get_checkout_url() : home_url( '/checkout/' );
$coupon_code  = get_theme_mod( 'baystore_coupon_code', 'BAY10' );
$free_thresh  = (int) get_theme_mod( 'baystore_free_shipping_threshold', 499 );
?>

<footer class="bs-footer">
	<svg class="bs-footer-wave" viewBox="0 0 1440 48" preserveAspectRatio="none" aria-hidden="true"><path d="M0 48V22c120-20 240-20 360 0s240 20 360 0 240-20 360 0 240 20 360 0v26z" fill="#14307a"/></svg>
	<div class="bs-promise">
		<div><b>The Baystore Promise</b></div>
		<div>🚚 Free delivery above ₹<?php echo (int) $free_thresh; ?></div>
		<div>💵 Pay on delivery — cash or UPI</div>
		<div>🔄 7-day easy replacement</div>
		<div>🤝 Ask us anything before you buy</div>
	</div>
	
	<div class="bs-footer-main">
		<div class="bs-footer-cols">
			<div class="bs-footer-brand">
				<span class="bs-logo-word">baystore<em>.in</em></span>
				<p>A small online shop for the everyday things Indian homes actually use — kitchen fixes, pantry staples, self-care, room glow-ups and festive decor. Pay when it arrives, anywhere in India.</p>
				<p class="bs-footer-pay">Cash on delivery — pay by cash or UPI at your door</p>
			</div>
			<div>
				<h4>Shop</h4>
				<ul>
					<li><a href="<?php echo esc_url( $shop_url ); ?>">All products</a></li>
					<?php foreach ( baystore_get_departments() as $d ) : ?>
						<li><a href="<?php echo esc_url( get_term_link( $d ) ); ?>"><?php echo esc_html( baystore_short_cat_name( $d->name ) ); ?></a></li>
					<?php endforeach; ?>
				</ul>
			</div>
			<div>
				<h4>Help</h4>
				<ul>
					<li><a href="<?php echo esc_url( function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : '#' ); ?>">My account &amp; orders</a></li>
					<li>Free delivery above ₹<?php echo (int) $free_thresh; ?></li>
					<li>7-day easy replacement</li>
					<li>Cash on delivery across India</li>
				</ul>
			</div>
			<div>
				<h4>Offers</h4>
				<ul>
					<li>Extra 10% off with <?php echo esc_html( $coupon_code ); ?></li>
					<li><a href="<?php echo esc_url( add_query_arg( 'orderby', 'popularity', $shop_url ) ); ?>">Best sellers</a></li>
					<li><a href="<?php echo esc_url( add_query_arg( 'orderby', 'date', $shop_url ) ); ?>">New arrivals</a></li>
				</ul>
			</div>
		</div>
	</div>
	<div class="bs-footer-bottom">
		<button type="button" class="bs-totop" onclick="window.scrollTo({top:0,behavior:'smooth'})">↑ Back to top</button> &copy; <?php echo esc_html( date( 'Y' ) ); ?> Baystore.in · Made for Indian homes
	</div>
</footer>

<!-- Slide-Over AJAX Cart Drawer -->
<div id="bs-cart-backdrop" class="bs-drawer-backdrop">
	<div class="bs-drawer">
		<div class="bs-drawer-head">
			<div style="font-size:16px;font-weight:800;">
				My Cart (<span id="bs-drawer-cart-count">0</span>)
			</div>
			<button type="button" id="bs-close-cart" class="bs-icon-btn" aria-label="Close cart">✕</button>
		</div>

		<!-- Dynamic Free Delivery Threshold Progress Bar -->
		<div class="bs-free-ship-bar">
			<div id="bs-free-ship-text">Add ₹<?php echo (int) $free_thresh; ?> for FREE Express Delivery</div>
			<div class="bs-progress-track">
				<div id="bs-free-ship-fill" class="bs-progress-fill" style="width:0%;"></div>
			</div>
		</div>

		<div id="bs-drawer-items" class="bs-drawer-body"></div>

		<div class="bs-drawer-foot">
			<button type="button" class="bs-coupon-pill" data-bs-apply-coupon="<?php echo esc_attr( $coupon_code ); ?>">
				<span>Coupon <strong><?php echo esc_html( $coupon_code ); ?></strong> (10% Off)</span>
				<span class="bs-coupon-apply">Apply</span>
			</button>
			<div style="display:flex;justify-content:space-between;font-size:16px;font-weight:800;margin-bottom:10px;">
				<span>Order Total</span>
				<span id="bs-drawer-total">₹0</span>
			</div>
			<a href="<?php echo esc_url( $checkout_url ); ?>" class="bs-btn bs-btn-blue bs-btn-block bs-btn-lg">
				Place order →
			</a>
		</div>
	</div>
</div>

<!-- Pincode Location Modal -->
<div id="bs-pincode-backdrop" class="bs-modal-backdrop">
	<div class="bs-modal">
		<div style="background:#F0F2F2;padding:12px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid #d5d9d9;">
			<strong style="font-size:14px;">Choose your delivery location</strong>
			<button type="button" id="bs-close-pincode" style="border:none;background:transparent;font-size:18px;cursor:pointer;">✕</button>
		</div>
		<form id="bs-pincode-form" style="padding:16px;">
			<p style="margin:0 0 10px;font-size:12px;color:#565959;">Enter an Indian pincode to see exact delivery dates and COD availability.</p>
			<div style="display:flex;gap:8px;margin-bottom:12px;">
				<input type="text" id="bs-pincode-input" maxlength="6" placeholder="e.g. 400001" style="flex:1;padding:8px 10px;border:1px solid #ccc;border-radius:4px;font-size:14px;" />
				<button type="submit" class="bs-btn bs-btn-blue">Apply</button>
			</div>
			<div style="display:flex;flex-wrap:wrap;gap:6px;">
				<button type="button" data-bs-pin-preset="400001" style="border:1px solid #ddd;background:#f7f8f8;border-radius:999px;padding:4px 10px;font-size:11px;cursor:pointer;">Mumbai (400001)</button>
				<button type="button" data-bs-pin-preset="110001" style="border:1px solid #ddd;background:#f7f8f8;border-radius:999px;padding:4px 10px;font-size:11px;cursor:pointer;">Delhi (110001)</button>
				<button type="button" data-bs-pin-preset="560001" style="border:1px solid #ddd;background:#f7f8f8;border-radius:999px;padding:4px 10px;font-size:11px;cursor:pointer;">Bengaluru (560001)</button>
				<button type="button" data-bs-pin-preset="411001" style="border:1px solid #ddd;background:#f7f8f8;border-radius:999px;padding:4px 10px;font-size:11px;cursor:pointer;">Pune (411001)</button>
			</div>
		</form>
	</div>
</div>

<div id="bs-toast" class="bs-toast"></div>

<?php wp_footer(); ?>
</body>
</html>
