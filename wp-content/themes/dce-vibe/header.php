<?php
/**
 * Header. Logo: Appearance → Customize → Site Identity. Menu: Appearance → Menus (Header — main menu).
 * Text, phone, WhatsApp and button: Appearance → Customize → DCE Business, Header & Footer.
 *
 * @package dce-vibe
 */

defined( 'ABSPATH' ) || exit;

$dce_cta_url = dce_opt( 'header_cta_url' );
if ( '#quote' === $dce_cta_url && is_singular() && false !== strpos( (string) get_post_field( 'post_content', get_queried_object_id() ), 'dce_lead_form' ) ) {
	$dce_cta_url = '#quote';
} else {
	$dce_cta_url = dce_resolve_url( $dce_cta_url );
}
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="theme-color" content="#faf7f2">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="screen-reader-text" href="#primary"><?php esc_html_e( 'Skip to content', 'dce-vibe' ); ?></a>

<?php if ( dce_opt( 'topbar_show' ) ) : ?>
<div class="dce-topbar">
	<div class="dce-container dce-topbar-inner">
		<p class="dce-topbar-msg"><?php echo dce_icon( 'calendar', 14 ); // phpcs:ignore ?> <?php echo esc_html( dce_opt( 'topbar_text' ) ); ?></p>
		<div class="dce-topbar-links">
			<a href="<?php echo esc_url( dce_tel_url() ); ?>"><?php echo dce_icon( 'phone', 13 ); // phpcs:ignore ?> <?php echo esc_html( dce_opt( 'phone' ) ); ?></a>
			<a class="dce-hide-sm" href="mailto:<?php echo esc_attr( antispambot( dce_opt( 'email' ) ) ); ?>"><?php echo esc_html( antispambot( dce_opt( 'email' ) ) ); ?></a>
			<?php if ( dce_opt( 'parent_url' ) ) : ?>
				<a class="dce-hide-sm" href="<?php echo esc_url( dce_opt( 'parent_url' ) ); ?>" target="_blank" rel="noopener"><span><?php esc_html_e( 'Part of', 'dce-vibe' ); ?></span> <?php echo esc_html( dce_opt( 'parent_name' ) ); ?> ↗</a>
			<?php endif; ?>
		</div>
	</div>
</div>
<?php endif; ?>

<header class="dce-header" data-dce-header>
	<div class="dce-container dce-header-inner">
		<a class="dce-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
			<span class="dce-brand-logo"><?php echo dce_logo_html(); // phpcs:ignore ?></span>
			<?php if ( ! has_custom_logo() || dce_opt( 'logo_with_text' ) ) : ?>
			<span class="dce-brand-text">
				<strong><?php echo esc_html( dce_opt( 'brand' ) ); ?></strong>
				<?php if ( dce_opt( 'brand_sub' ) ) : ?><em><?php echo esc_html( dce_opt( 'brand_sub' ) ); ?></em><?php endif; ?>
			</span>
			<?php endif; ?>
		</a>

		<nav class="dce-nav" aria-label="<?php esc_attr_e( 'Primary', 'dce-vibe' ); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'dce-nav-list',
					'fallback_cb'    => 'dce_menu_fallback',
					'depth'          => 2,
				)
			);
			?>
		</nav>

		<div class="dce-header-actions">
			<a class="dce-header-wa" href="<?php echo esc_url( dce_wa_url( dce_page_wa_message() ) ); ?>" target="_blank" rel="noopener" aria-label="<?php esc_attr_e( 'Chat on WhatsApp', 'dce-vibe' ); ?>">
				<?php echo dce_icon( 'whatsapp', 18 ); // phpcs:ignore ?><span><?php esc_html_e( 'WhatsApp', 'dce-vibe' ); ?></span>
			</a>
			<?php if ( dce_opt( 'header_cta_text' ) ) : ?>
			<a class="dce-btn dce-btn-dark dce-btn-sm dce-header-cta" href="<?php echo esc_url( $dce_cta_url ); ?>"><?php echo esc_html( dce_opt( 'header_cta_text' ) ); ?> <?php echo dce_icon( 'arrow', 14 ); // phpcs:ignore ?></a>
			<?php endif; ?>
			<button class="dce-menu-toggle" type="button" aria-label="<?php esc_attr_e( 'Open menu', 'dce-vibe' ); ?>" aria-expanded="false" aria-controls="dce-drawer" data-dce-open><?php echo dce_icon( 'menu', 20 ); // phpcs:ignore ?></button>
		</div>
	</div>
</header>

<div class="dce-drawer" id="dce-drawer" aria-hidden="true">
	<div class="dce-drawer-backdrop" data-dce-close></div>
	<div class="dce-drawer-panel" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Menu', 'dce-vibe' ); ?>">
		<div class="dce-drawer-head">
			<a class="dce-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>"><span class="dce-brand-logo"><?php echo dce_logo_html(); // phpcs:ignore ?></span></a>
			<button class="dce-menu-toggle" style="display:inline-flex" type="button" aria-label="<?php esc_attr_e( 'Close menu', 'dce-vibe' ); ?>" data-dce-close><?php echo dce_icon( 'close', 20 ); // phpcs:ignore ?></button>
		</div>
		<nav class="dce-drawer-nav" aria-label="<?php esc_attr_e( 'Mobile', 'dce-vibe' ); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'dce-drawer-list',
					'fallback_cb'    => 'dce_menu_fallback',
					'depth'          => 2,
				)
			);
			?>
		</nav>
		<div class="dce-drawer-actions">
			<a class="dce-btn dce-btn-wa" href="<?php echo esc_url( dce_wa_url( dce_page_wa_message() ) ); ?>" target="_blank" rel="noopener"><?php echo dce_icon( 'whatsapp', 18 ); // phpcs:ignore ?> <?php esc_html_e( 'Chat on WhatsApp', 'dce-vibe' ); ?></a>
			<a class="dce-btn dce-btn-ghost" href="<?php echo esc_url( dce_tel_url() ); ?>"><?php echo dce_icon( 'phone', 16 ); // phpcs:ignore ?> <?php echo esc_html( dce_opt( 'phone' ) ); ?></a>
		</div>
	</div>
</div>
