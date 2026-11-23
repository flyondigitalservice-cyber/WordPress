<?php
/**
 * Baystore — Header Template
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

<div class="bs-announce">
	<span>Free delivery on orders above ₹<?php echo (int) $free_thresh; ?></span>
	<span class="bs-announce-dot" aria-hidden="true">·</span>
	<span>Extra 10% off with code <strong><?php echo esc_html( $coupon_code ); ?></strong></span>
	<span class="bs-announce-dot bs-hide-sm" aria-hidden="true">·</span>
	<span class="bs-hide-sm">Cash on delivery across India</span>
</div>

<header class="bs-header">
	<div class="bs-header-main">
		<div class="bs-header-left">
			<button type="button" id="bs-open-mega" class="bs-icon-btn bs-show-sm" aria-label="Open menu">
				<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
			</button>
			<button type="button" data-bs-open-pincode class="bs-header-tagline bs-hide-sm">
				<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 21s-7-6.2-7-11a7 7 0 0 1 14 0c0 4.8-7 11-7 11z"/><circle cx="12" cy="10" r="2.5"/></svg>
				<span data-bs-loc-text>Delivering to Mumbai 400001</span>
			</button>
		</div>

		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="bs-logo" aria-label="Baystore home">
			<span class="bs-logo-word">BAYSTORE</span>
			<span class="bs-logo-sub">Everyday Essentials</span>
		</a>

		<div class="bs-header-right">
			<button type="button" class="bs-icon-btn" data-bs-toggle-search aria-label="Search">
				<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.5" y2="16.5"/></svg>
			</button>
			<a href="<?php echo esc_url( $account_url ); ?>" class="bs-icon-btn bs-hide-sm" aria-label="My account">
				<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><circle cx="12" cy="8" r="4"/><path d="M4 21c1.5-4 4.5-6 8-6s6.5 2 8 6"/></svg>
			</a>
			<button type="button" id="bs-open-cart" class="bs-icon-btn bs-cart-btn" aria-label="Open cart">
				<svg width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M6 7h12l-1 13H7L6 7z"/><path d="M9 7a3 3 0 0 1 6 0"/></svg>
				<span id="bs-header-cart-count" class="bs-cart-badge"><?php echo (int) $cart_count; ?></span>
				<span id="bs-header-cart-subtotal" class="bs-sr-only"><?php echo $cart_count > 0 ? esc_html( baystore_format_inr( $cart_sub ) ) : 'Empty'; ?></span>
			</button>
		</div>
	</div>

	<div id="bs-search-panel" class="bs-search-panel">
		<div class="bs-search-wrap">
			<form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" class="bs-search-form">
				<?php if ( class_exists( 'WooCommerce' ) ) : ?>
					<input type="hidden" name="post_type" value="product" />
				<?php endif; ?>
				<select id="bs-search-dept" name="product_cat" class="bs-search-dept" aria-label="Category">
					<option value="">All</option>
					<?php foreach ( $dept_cats as $cat ) : ?>
						<option value="<?php echo esc_attr( $cat->slug ); ?>"><?php echo esc_html( $cat->name ); ?></option>
					<?php endforeach; ?>
				</select>
				<input type="search" id="bs-search-input" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="Search chopping boards, chia seeds, projectors…" class="bs-search-input" autocomplete="off" />
				<button type="submit" class="bs-search-submit" aria-label="Search">
					<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.5" y2="16.5"/></svg>
				</button>
			</form>
			<div id="bs-search-results" class="bs-search-results"></div>
		</div>
	</div>

	<nav class="bs-nav" aria-label="Departments">
		<div class="bs-nav-inner">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a>
			<a href="<?php echo esc_url( $shop_url ); ?>">Shop All</a>
			<?php foreach ( $dept_cats as $cat ) : ?>
				<a href="<?php echo esc_url( get_term_link( $cat ) ); ?>"><?php echo esc_html( baystore_short_cat_name( $cat->name ) ); ?></a>
			<?php endforeach; ?>
			<a href="<?php echo esc_url( add_query_arg( 'orderby', 'popularity', $shop_url ) ); ?>" class="bs-nav-hot">Offers</a>
		</div>
	</nav>
</header>

<div id="bs-mega-backdrop" class="bs-mega-backdrop">
	<div class="bs-mega-drawer">
		<div class="bs-mega-head">
			<span class="bs-logo-word" style="font-size:18px;">BAYSTORE</span>
			<button type="button" id="bs-close-mega" class="bs-icon-btn" aria-label="Close menu">✕</button>
		</div>
		<div class="bs-mega-links">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a>
			<a href="<?php echo esc_url( $shop_url ); ?>">Shop All</a>
			<?php foreach ( $dept_cats as $cat ) : ?>
				<a href="<?php echo esc_url( get_term_link( $cat ) ); ?>"><?php echo esc_html( $cat->name ); ?></a>
			<?php endforeach; ?>
			<a href="<?php echo esc_url( add_query_arg( 'orderby', 'popularity', $shop_url ) ); ?>">Offers</a>
			<a href="<?php echo esc_url( $account_url ); ?>">My Account</a>
		</div>
	</div>
</div>
