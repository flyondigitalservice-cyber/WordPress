<?php
/**
 * Baystore — Header Template (blue bar, wide search, category strip)
 *
 * @package Baystore_Amazon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$shop_url    = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );
$account_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : wp_login_url();
$cart_count  = ( function_exists( 'WC' ) && WC()->cart ) ? WC()->cart->get_cart_contents_count() : 0;
$cart_sub    = ( function_exists( 'WC' ) && WC()->cart ) ? WC()->cart->get_subtotal() : 0;
$coupon_code = get_theme_mod( 'baystore_coupon_code', 'BAY10' );
$free_thresh = (int) get_theme_mod( 'baystore_free_shipping_threshold', 499 );
$dept_cats   = baystore_get_departments();
$user        = wp_get_current_user();
$hello       = $user->exists() ? 'Hello, ' . $user->display_name : 'Hello, sign in';
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<?php wp_head(); ?>
</head>
<body <?php body_class( 'bs-store' ); ?>>
<?php wp_body_open(); ?>

<header class="bs-header">
	<div class="bs-topbar">
		<div class="bs-topbar-wave" aria-hidden="true"></div>
		<div class="bs-topbar-inner">
			<button type="button" id="bs-open-mega" class="bs-hdr-icon bs-show-sm" aria-label="Open menu">
				<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
			</button>

			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="bs-logo" aria-label="Baystore home">
				<svg class="bs-logo-mark" width="34" height="34" viewBox="0 0 40 40" aria-hidden="true"><rect x="2" y="9" width="36" height="29" rx="9" fill="#FFC21A"/><path d="M13 13V10a7 7 0 0 1 14 0v3" fill="none" stroke="#FFC21A" stroke-width="3.2" stroke-linecap="round"/><path d="M7 27c4.5-4 8.5-4 13 0s8.5 4 13 0" fill="none" stroke="#14307a" stroke-width="3.2" stroke-linecap="round"/></svg>
				<span class="bs-logo-word">baystore<em>.in</em></span>
			</a>

			<div class="bs-search-wrap">
				<form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" class="bs-search-form">
					<?php if ( class_exists( 'WooCommerce' ) ) : ?>
						<input type="hidden" name="post_type" value="product" />
					<?php endif; ?>
					<select id="bs-search-dept" name="product_cat" class="bs-search-dept" aria-label="Category">
						<option value="">All</option>
						<?php foreach ( $dept_cats as $cat ) : ?>
							<option value="<?php echo esc_attr( $cat->slug ); ?>"><?php echo esc_html( baystore_short_cat_name( $cat->name ) ); ?></option>
						<?php endforeach; ?>
					</select>
					<input type="search" id="bs-search-input" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="Search for products, brands and more" class="bs-search-input" autocomplete="off" />
					<button type="submit" class="bs-search-submit" aria-label="Search">
						<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.5" y2="16.5"/></svg>
					</button>
				</form>
				<div id="bs-search-results" class="bs-search-results"></div>
			</div>

			<button type="button" data-bs-open-pincode class="bs-hdr-block bs-hide-sm">
				<small>Deliver to</small>
				<strong><span data-bs-loc-text>Mumbai 400001</span></strong>
			</button>

			<a href="<?php echo esc_url( $account_url ); ?>" class="bs-hdr-block bs-hide-sm">
				<small><?php echo esc_html( $hello ); ?></small>
				<strong>Account &amp; Orders</strong>
			</a>

			<button type="button" id="bs-open-cart" class="bs-hdr-cart" aria-label="Open cart">
				<span class="bs-hdr-cart-icon">
					<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="20" r="1.4"/><circle cx="18" cy="20" r="1.4"/><path d="M2 3h3l2.6 12.2a1.6 1.6 0 0 0 1.6 1.3h8.6a1.6 1.6 0 0 0 1.6-1.2L21 7H6"/></svg>
					<span id="bs-header-cart-count" class="bs-cart-badge"><?php echo (int) $cart_count; ?></span>
				</span>
				<span class="bs-hide-sm">Cart</span>
				<span id="bs-header-cart-subtotal" class="bs-sr-only"><?php echo $cart_count > 0 ? esc_html( baystore_format_inr( $cart_sub ) ) : 'Empty'; ?></span>
			</button>
		</div>
	</div>

	<nav class="bs-catbar" aria-label="Departments">
		<div class="bs-catbar-inner">
			<a href="<?php echo esc_url( $shop_url ); ?>" class="bs-catbar-all">
				<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
				All
			</a>
			<a href="<?php echo esc_url( add_query_arg( 'orderby', 'popularity', $shop_url ) ); ?>" class="bs-catbar-deal">Today's Deals</a>
			<?php foreach ( $dept_cats as $cat ) : ?>
				<a href="<?php echo esc_url( get_term_link( $cat ) ); ?>"><?php echo esc_html( html_entity_decode( $cat->name ) ); ?></a>
			<?php endforeach; ?>
			<span class="bs-catbar-note bs-hide-sm">Free delivery above ₹<?php echo (int) $free_thresh; ?> · Extra 10% off: <b><?php echo esc_html( $coupon_code ); ?></b></span>
		</div>
	</nav>
</header>

<div id="bs-mega-backdrop" class="bs-mega-backdrop">
	<div class="bs-mega-drawer">
		<div class="bs-mega-head">
			<span><?php echo esc_html( $hello ); ?></span>
			<button type="button" id="bs-close-mega" class="bs-hdr-icon" aria-label="Close menu">✕</button>
		</div>
		<div class="bs-mega-links">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a>
			<a href="<?php echo esc_url( add_query_arg( 'orderby', 'popularity', $shop_url ) ); ?>">Today's Deals</a>
			<a href="<?php echo esc_url( $shop_url ); ?>">All Products</a>
			<div class="bs-mega-label">Shop by category</div>
			<?php foreach ( $dept_cats as $cat ) : ?>
				<a href="<?php echo esc_url( get_term_link( $cat ) ); ?>"><?php echo esc_html( html_entity_decode( $cat->name ) ); ?></a>
			<?php endforeach; ?>
			<div class="bs-mega-label">Your account</div>
			<a href="<?php echo esc_url( $account_url ); ?>">Account &amp; Orders</a>
			<button type="button" data-bs-open-pincode class="bs-mega-pin">📍 <span data-bs-loc-text>Mumbai 400001</span></button>
		</div>
	</div>
</div>
