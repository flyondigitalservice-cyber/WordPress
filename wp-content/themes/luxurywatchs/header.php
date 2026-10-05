<?php
/**
 * Site header.
 *
 * @package luxurywatchs
 */

$lw_has_wc   = class_exists( 'WooCommerce' );
$lw_cart_url = $lw_has_wc ? wc_get_cart_url() : lw_shop_url();
$lw_acct_url = $lw_has_wc ? wc_get_page_permalink( 'myaccount' ) : wp_login_url();
$lw_count    = ( $lw_has_wc && WC()->cart ) ? WC()->cart->get_cart_contents_count() : 0;
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="theme-color" content="#0b0b0c">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="screen-reader-text skip-link" href="#main"><?php esc_html_e( 'Skip to content', 'luxurywatchs' ); ?></a>

<div class="lw-announce" role="note">
	<div class="lw-announce__track">
		<?php
		$lw_ann = get_theme_mod( 'lw_announcement', 'Free Shipping Across India  •  Cash on Delivery Available  •  Live Video Before Dispatch' );
		for ( $i = 0; $i < 2; $i++ ) :
			?>
			<span><?php echo esc_html( $lw_ann ); ?></span>
			<span><?php printf( esc_html__( 'Use code %s for 10%% off your first order', 'luxurywatchs' ), '<b>' . esc_html( get_theme_mod( 'lw_offer_code', 'LUXE10' ) ) . '</b>' ); ?></span>
		<?php endfor; ?>
	</div>
</div>

<header class="lw-header" data-header>
	<div class="lw-container lw-header__inner">
		<button class="lw-iconbtn lw-header__burger" type="button" aria-label="<?php esc_attr_e( 'Open menu', 'luxurywatchs' ); ?>" aria-controls="lw-drawer" aria-expanded="false" data-drawer-open>
			<?php echo lw_icon( 'menu' ); // phpcs:ignore ?>
		</button>

		<a class="lw-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
			<?php if ( has_custom_logo() ) : ?>
				<?php echo wp_kses_post( wp_get_attachment_image( get_theme_mod( 'custom_logo' ), 'full', false, array( 'class' => 'lw-logo__img' ) ) ); ?>
			<?php else : ?>
				<span class="lw-logo__mark">LW</span>
				<span class="lw-logo__text">LuxuryWatchs<small><?php esc_html_e( 'Signature', 'luxurywatchs' ); ?></small></span>
			<?php endif; ?>
		</a>

		<nav class="lw-nav" aria-label="<?php esc_attr_e( 'Primary', 'luxurywatchs' ); ?>">
			<?php
			if ( has_nav_menu( 'primary' ) ) {
				wp_nav_menu(
					array(
						'theme_location' => 'primary',
						'container'      => false,
						'menu_class'     => 'lw-nav__list',
						'depth'          => 2,
					)
				);
			} else {
				echo '<ul class="lw-nav__list">';
				echo '<li><a href="' . esc_url( lw_shop_url() ) . '">' . esc_html__( 'Shop All', 'luxurywatchs' ) . '</a></li>';
				echo '<li class="menu-item-has-children"><a href="' . esc_url( lw_shop_url() ) . '">' . esc_html__( 'Styles', 'luxurywatchs' ) . '</a><ul class="sub-menu">';
				foreach ( lw_style_categories() as $c ) {
					echo '<li><a href="' . esc_url( lw_cat_url( $c['slug'] ) ) . '">' . esc_html( $c['name'] ) . '</a></li>';
				}
				echo '</ul></li>';
				echo '<li class="menu-item-has-children"><a href="#collections">' . esc_html__( 'Collections', 'luxurywatchs' ) . '</a><ul class="sub-menu">';
				foreach ( lw_collections() as $c ) {
					echo '<li><a href="' . esc_url( lw_cat_url( $c['slug'] ) ) . '">' . esc_html( $c['name'] ) . '</a></li>';
				}
				echo '</ul></li>';
				echo '<li><a href="' . esc_url( lw_cat_url( 'men' ) ) . '">' . esc_html__( 'Men', 'luxurywatchs' ) . '</a></li>';
				echo '<li><a href="' . esc_url( lw_cat_url( 'women' ) ) . '">' . esc_html__( 'Women', 'luxurywatchs' ) . '</a></li>';
				echo '<li><a class="lw-nav__hot" href="' . esc_url( lw_cat_url( 'sale' ) ) . '">' . esc_html__( 'Sale', 'luxurywatchs' ) . '</a></li>';
				echo '</ul>';
			}
			?>
		</nav>

		<div class="lw-header__actions">
			<button class="lw-iconbtn" type="button" aria-label="<?php esc_attr_e( 'Search', 'luxurywatchs' ); ?>" data-search-toggle><?php echo lw_icon( 'search' ); // phpcs:ignore ?></button>
			<a class="lw-iconbtn lw-hide-sm" href="<?php echo esc_url( $lw_acct_url ); ?>" aria-label="<?php esc_attr_e( 'My account', 'luxurywatchs' ); ?>"><?php echo lw_icon( 'user' ); // phpcs:ignore ?></a>
			<a class="lw-iconbtn lw-cart" href="<?php echo esc_url( $lw_cart_url ); ?>" aria-label="<?php esc_attr_e( 'Cart', 'luxurywatchs' ); ?>">
				<?php echo lw_icon( 'bag' ); // phpcs:ignore ?>
				<span class="lw-cart-count"><?php echo esc_html( $lw_count ); ?></span>
			</a>
		</div>
	</div>

	<div class="lw-search" data-search hidden>
		<form class="lw-container lw-search__form" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
			<label class="screen-reader-text" for="lw-s"><?php esc_html_e( 'Search watches', 'luxurywatchs' ); ?></label>
			<input id="lw-s" type="search" name="s" placeholder="<?php esc_attr_e( 'Search chronograph, diver, gold…', 'luxurywatchs' ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>">
			<?php if ( $lw_has_wc ) : ?><input type="hidden" name="post_type" value="product"><?php endif; ?>
			<button class="lw-btn lw-btn--gold" type="submit"><?php esc_html_e( 'Search', 'luxurywatchs' ); ?></button>
		</form>
	</div>
</header>

<div class="lw-drawer" id="lw-drawer" data-drawer hidden>
	<div class="lw-drawer__backdrop" data-drawer-close></div>
	<aside class="lw-drawer__panel" aria-label="<?php esc_attr_e( 'Menu', 'luxurywatchs' ); ?>">
		<div class="lw-drawer__head">
			<span class="lw-logo__text">LuxuryWatchs<small><?php esc_html_e( 'Signature', 'luxurywatchs' ); ?></small></span>
			<button class="lw-iconbtn" type="button" aria-label="<?php esc_attr_e( 'Close menu', 'luxurywatchs' ); ?>" data-drawer-close><?php echo lw_icon( 'close' ); // phpcs:ignore ?></button>
		</div>
		<p class="lw-drawer__label"><?php esc_html_e( 'Shop by style', 'luxurywatchs' ); ?></p>
		<ul class="lw-drawer__chips">
			<?php foreach ( lw_style_categories() as $c ) : ?>
				<li><a href="<?php echo esc_url( lw_cat_url( $c['slug'] ) ); ?>"><?php echo esc_html( $c['name'] ); ?></a></li>
			<?php endforeach; ?>
		</ul>
		<p class="lw-drawer__label"><?php esc_html_e( 'Collections', 'luxurywatchs' ); ?></p>
		<ul class="lw-drawer__list">
			<?php foreach ( lw_collections() as $c ) : ?>
				<li><a href="<?php echo esc_url( lw_cat_url( $c['slug'] ) ); ?>"><?php echo esc_html( $c['name'] ); ?> <small><?php echo esc_html( $c['line'] ); ?></small></a></li>
			<?php endforeach; ?>
		</ul>
		<ul class="lw-drawer__list">
			<li><a href="<?php echo esc_url( lw_shop_url() ); ?>"><?php esc_html_e( 'Shop All Watches', 'luxurywatchs' ); ?></a></li>
			<li><a href="<?php echo esc_url( lw_cat_url( 'sale' ) ); ?>"><?php esc_html_e( 'Sale', 'luxurywatchs' ); ?></a></li>
			<li><a href="<?php echo esc_url( $lw_acct_url ); ?>"><?php esc_html_e( 'My Account / Track Order', 'luxurywatchs' ); ?></a></li>
		</ul>
		<a class="lw-btn lw-btn--wa lw-btn--block" href="<?php echo esc_url( lw_whatsapp_url() ); ?>" target="_blank" rel="noopener"><?php echo lw_whatsapp_icon(); // phpcs:ignore ?><?php esc_html_e( 'Chat on WhatsApp', 'luxurywatchs' ); ?></a>
	</aside>
</div>

<main id="main" class="lw-main">
