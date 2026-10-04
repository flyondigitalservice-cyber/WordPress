<?php
/**
 * Site header.
 *
 * @package FlavorKit
 */

defined( 'ABSPATH' ) || exit;

$fk_has_wc        = class_exists( 'WooCommerce' );
$fk_announcements = flavorkit_pipe_list( flavorkit_mod( 'announcement' ) );
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e( 'Skip to content', 'flavorkit' ); ?></a>

<?php if ( $fk_announcements ) : ?>
	<div class="fk-announce" role="region" aria-label="<?php esc_attr_e( 'Announcements', 'flavorkit' ); ?>">
		<div class="fk-announce-track">
			<?php for ( $fk_loop = 0; $fk_loop < 2; $fk_loop++ ) : ?>
				<div class="fk-announce-group"<?php echo $fk_loop ? ' aria-hidden="true"' : ''; ?>>
					<?php foreach ( $fk_announcements as $fk_message ) : ?>
						<span><?php echo esc_html( $fk_message ); ?></span>
						<span class="fk-announce-dot" aria-hidden="true">✦</span>
					<?php endforeach; ?>
				</div>
			<?php endfor; ?>
		</div>
	</div>
<?php endif; ?>

<header id="masthead" class="fk-header">
	<div class="fk-container fk-header-inner">
		<button class="fk-icon-btn fk-menu-toggle" type="button" aria-controls="fk-mobile-nav" aria-expanded="false" aria-label="<?php esc_attr_e( 'Open menu', 'flavorkit' ); ?>">
			<?php echo flavorkit_icon( 'menu' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		</button>

		<div class="fk-brand">
			<?php if ( has_custom_logo() ) : ?>
				<?php the_custom_logo(); ?>
			<?php else : ?>
				<a class="fk-wordmark" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a>
			<?php endif; ?>
		</div>

		<nav class="fk-nav" aria-label="<?php esc_attr_e( 'Primary', 'flavorkit' ); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'menu_class'     => 'fk-menu',
					'container'      => false,
					'depth'          => 2,
					'fallback_cb'    => 'flavorkit_menu_fallback',
				)
			);
			?>
		</nav>

		<div class="fk-header-actions">
			<?php if ( flavorkit_mod( 'header_cta_text' ) ) : ?>
				<a class="fk-btn fk-btn-sm fk-header-cta" href="<?php echo esc_url( flavorkit_link( flavorkit_mod( 'header_cta_url' ) ) ); ?>"><?php echo esc_html( flavorkit_mod( 'header_cta_text' ) ); ?></a>
			<?php endif; ?>

			<button class="fk-icon-btn fk-search-toggle" type="button" aria-controls="fk-search" aria-expanded="false" aria-label="<?php esc_attr_e( 'Search', 'flavorkit' ); ?>">
				<?php echo flavorkit_icon( 'search' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</button>

			<?php if ( $fk_has_wc ) : ?>
				<a class="fk-icon-btn fk-account-link" href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" aria-label="<?php esc_attr_e( 'My account', 'flavorkit' ); ?>">
					<?php echo flavorkit_icon( 'user' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				</a>
				<a class="fk-icon-btn fk-cart-toggle" href="<?php echo esc_url( wc_get_cart_url() ); ?>" aria-controls="fk-cart-drawer" aria-label="<?php esc_attr_e( 'Cart', 'flavorkit' ); ?>">
					<?php echo flavorkit_icon( 'cart' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<?php echo flavorkit_cart_count_html(); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				</a>
			<?php endif; ?>
		</div>
	</div>

	<div id="fk-search" class="fk-search-panel" hidden>
		<div class="fk-container">
			<?php
			if ( $fk_has_wc ) {
				get_product_search_form();
			} else {
				get_search_form();
			}
			?>
		</div>
	</div>
</header>

<div id="fk-mobile-nav" class="fk-drawer fk-drawer-left" aria-hidden="true" aria-label="<?php esc_attr_e( 'Menu', 'flavorkit' ); ?>" role="dialog" aria-modal="true">
	<div class="fk-drawer-head">
		<span class="fk-drawer-title"><?php esc_html_e( 'Menu', 'flavorkit' ); ?></span>
		<button class="fk-icon-btn fk-drawer-close" type="button" aria-label="<?php esc_attr_e( 'Close menu', 'flavorkit' ); ?>"><?php echo flavorkit_icon( 'close' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
	</div>
	<nav class="fk-mobile-menu" aria-label="<?php esc_attr_e( 'Mobile', 'flavorkit' ); ?>">
		<?php
		wp_nav_menu(
			array(
				'theme_location' => 'primary',
				'menu_class'     => 'fk-menu',
				'container'      => false,
				'depth'          => 2,
				'fallback_cb'    => 'flavorkit_menu_fallback',
			)
		);
		?>
	</nav>
	<?php flavorkit_social_links(); ?>
</div>
