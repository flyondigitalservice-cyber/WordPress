<?php
/**
 * Header: top bar, primary navigation and mobile overlay.
 *
 * Logo: Appearance → Customize → Site Identity. Menu: Appearance → Menus
 * ("Primary Menu" location). Phone / WhatsApp / email / hours:
 * Appearance → Customize → Dubai Curtain Experts.
 *
 * @package Dubai_Curtain_Experts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div id="page" class="dce-site">
	<a class="screen-reader-text" href="#primary"><?php esc_html_e( 'Skip to content', 'dubai-curtain-experts' ); ?></a>

	<!-- ======================================================= top bar -->
	<div class="dce-topbar">
		<div class="dce-container dce-topbar-inner">
			<span class="dce-topbar-item dce-topbar-phone">
				<?php echo dce_icon( 'phone', array( 'size' => 12 ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in helper. ?>
				<a href="tel:<?php echo esc_attr( dce_tel() ); ?>"><?php echo esc_html( dce_mod( 'phone' ) ); ?></a>
			</span>
			<span class="dce-topbar-item dce-topbar-mail">
				<?php echo dce_icon( 'mail', array( 'size' => 12 ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<a href="mailto:<?php echo esc_attr( dce_mod( 'email' ) ); ?>"><?php echo esc_html( dce_mod( 'email' ) ); ?></a>
			</span>
			<span class="dce-topbar-item dce-topbar-hours"><?php echo esc_html( dce_mod( 'hours_weekday' ) ); ?></span>
			<span class="dce-topbar-item dce-topbar-sub dce-topbar-parent">
				<a href="<?php echo esc_url( dce_parent_url() ); ?>" target="_blank" rel="noopener"><?php echo esc_html( dce_mod( 'sub_brand' ) ); ?></a>
			</span>
		</div>
	</div>

	<!-- ======================================================= masthead -->
	<header class="dce-header" data-dce-header>
		<div class="dce-container dce-header-inner">

			<a class="dce-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
				<?php if ( has_custom_logo() ) : ?>
					<span class="dce-brand-logo"><?php echo wp_get_attachment_image( get_theme_mod( 'custom_logo' ), 'full', false, array( 'alt' => dce_mod( 'brand_name' ) ) ); ?></span>
				<?php else : ?>
					<span class="dce-brand-mark"><span><?php echo esc_html( mb_substr( dce_mod( 'brand_name' ), 0, 1 ) ); ?></span></span>
				<?php endif; ?>
				<span class="dce-brand-text">
					<strong><?php echo esc_html( dce_mod( 'brand_name' ) ); ?></strong>
					<em><?php echo esc_html( dce_mod( 'sub_brand' ) ); ?></em>
				</span>
			</a>

			<nav class="dce-nav" aria-label="<?php esc_attr_e( 'Primary', 'dubai-curtain-experts' ); ?>">
				<?php
				if ( has_nav_menu( 'primary' ) ) {
					wp_nav_menu(
						array(
							'theme_location' => 'primary',
							'container'      => false,
							'menu_class'     => 'dce-nav-list',
							'depth'          => 2,
							'fallback_cb'    => false,
						)
					);
				} else {
					dce_fallback_menu();
				}
				?>
			</nav>

			<div class="dce-header-actions">
				<a class="dce-header-wa" href="<?php echo esc_url( dce_wa_url( dce_wa_context_text() ) ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e( 'WhatsApp us', 'dubai-curtain-experts' ); ?>">
					<?php echo dce_icon( 'whatsapp', array( 'size' => 16 ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<span><?php esc_html_e( 'WhatsApp', 'dubai-curtain-experts' ); ?></span>
				</a>
				<a class="dce-btn dce-btn-dark dce-btn-sm dce-header-cta" href="<?php echo esc_url( dce_mod( 'hero_primary_url' ) ); ?>">
					<?php echo esc_html( dce_mod( 'hero_primary_label' ) ); ?>
					<?php echo dce_icon( 'arrow-up-right', array( 'size' => 14 ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</a>
				<button class="dce-menu-toggle" type="button" aria-label="<?php esc_attr_e( 'Open menu', 'dubai-curtain-experts' ); ?>" aria-expanded="false" data-dce-menu-open>
					<?php echo dce_icon( 'menu' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</button>
			</div>
		</div>
	</header>

	<!-- ======================================================= mobile menu -->
	<div class="dce-mobile" data-dce-mobile aria-hidden="true">
		<div class="dce-mobile-head">
			<span class="dce-mobile-brand"><?php echo esc_html( dce_mod( 'brand_name' ) ); ?></span>
			<button class="dce-menu-toggle dce-mobile-close" type="button" aria-label="<?php esc_attr_e( 'Close menu', 'dubai-curtain-experts' ); ?>" data-dce-menu-close>
				<?php echo dce_icon( 'close' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</button>
		</div>
		<nav class="dce-mobile-nav" aria-label="<?php esc_attr_e( 'Mobile', 'dubai-curtain-experts' ); ?>">
			<?php
			if ( has_nav_menu( 'primary' ) ) {
				wp_nav_menu(
					array(
						'theme_location' => 'primary',
						'container'      => false,
						'menu_class'     => 'dce-mobile-list',
						'depth'          => 2,
					)
				);
			} else {
				dce_fallback_menu();
			}
			?>
		</nav>
		<div class="dce-mobile-foot">
			<a class="dce-btn dce-btn-wa dce-btn-block" href="<?php echo esc_url( dce_wa_url( dce_wa_context_text() ) ); ?>" target="_blank" rel="noopener noreferrer">
				<?php echo dce_icon( 'whatsapp', array( 'size' => 18 ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<span><?php esc_html_e( 'WhatsApp for a free visit', 'dubai-curtain-experts' ); ?></span>
			</a>
			<p class="dce-mobile-meta">
				<a href="tel:<?php echo esc_attr( dce_tel() ); ?>"><?php echo esc_html( dce_mod( 'phone' ) ); ?></a> · <a href="mailto:<?php echo esc_attr( dce_mod( 'email' ) ); ?>"><?php echo esc_html( dce_mod( 'email' ) ); ?></a>
			</p>
		</div>
	</div>
