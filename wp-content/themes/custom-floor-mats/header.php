<?php
/**
 * Site header. Logo: Appearance → Customize → Site Identity.
 * Menu: Appearance → Menus ("Primary (header)"). Texts: Customize → Custom Floor Mats → Header.
 *
 * @package CFM
 */

defined( 'ABSPATH' ) || exit;
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="theme-color" content="#17130e">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="cfm-skip" href="#main"><?php esc_html_e( 'Skip to content', 'cfm' ); ?></a>

<?php if ( cfm_opt( 'show_topbar' ) ) : ?>
<div class="cfm-topbar">
	<div class="cfm-wrap cfm-topbar__inner">
		<p class="cfm-topbar__text"><?php echo esc_html( cfm_opt( 'topbar_text' ) ); ?></p>
		<ul class="cfm-topbar__links">
			<?php if ( cfm_opt( 'phone_link' ) ) : ?>
				<li><a href="<?php echo esc_url( 'tel:' . preg_replace( '/[^\d+]/', '', cfm_opt( 'phone_link' ) ) ); ?>"><?php echo cfm_icon( 'phone', 14 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php echo esc_html( cfm_opt( 'phone_display' ) ? cfm_opt( 'phone_display' ) : cfm_opt( 'phone_link' ) ); ?></a></li>
			<?php endif; ?>
			<?php if ( cfm_opt( 'email' ) ) : ?>
				<li class="cfm-hide-sm"><a href="<?php echo esc_url( 'mailto:' . cfm_opt( 'email' ) ); ?>"><?php echo cfm_icon( 'mail', 14 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php echo esc_html( cfm_opt( 'email' ) ); ?></a></li>
			<?php endif; ?>
			<?php if ( cfm_opt( 'parent_url' ) ) : ?>
				<li><a href="<?php echo esc_url( cfm_opt( 'parent_url' ) ); ?>" target="_blank" rel="noopener"><?php
					/* translators: %s: mother company name */
					echo esc_html( sprintf( __( 'A %s company', 'cfm' ), cfm_opt( 'parent_name' ) ) );
				?><?php echo cfm_icon( 'external', 12 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a></li>
			<?php endif; ?>
		</ul>
	</div>
</div>
<?php endif; ?>

<header class="cfm-header" data-cfm-header>
	<div class="cfm-wrap cfm-header__inner">
		<a class="cfm-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" aria-label="<?php echo esc_attr( cfm_opt( 'brand_name' ) ); ?>">
			<?php echo cfm_brand_mark( 'light' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</a>

		<nav class="cfm-nav" id="cfm-nav" aria-label="<?php esc_attr_e( 'Main menu', 'cfm' ); ?>" data-cfm-nav>
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'cfm-menu',
					'depth'          => 2,
					'fallback_cb'    => 'cfm_menu_fallback',
				)
			);
			?>
			<a class="cfm-btn cfm-btn--wa cfm-nav__cta" href="<?php echo esc_url( cfm_wa_url( cfm_wa_message_for() ) ); ?>" target="_blank" rel="noopener" data-cfm-wa><?php echo cfm_icon( 'whatsapp', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><?php echo esc_html( cfm_opt( 'header_cta_label' ) ); ?></span></a>
		</nav>

		<a class="cfm-btn cfm-btn--wa cfm-header__cta" href="<?php echo esc_url( cfm_wa_url( cfm_wa_message_for() ) ); ?>" target="_blank" rel="noopener" data-cfm-wa><?php echo cfm_icon( 'whatsapp', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><?php echo esc_html( cfm_opt( 'header_cta_label' ) ); ?></span></a>

		<button class="cfm-burger" type="button" aria-controls="cfm-nav" aria-expanded="false" data-cfm-burger>
			<span class="screen-reader-text"><?php esc_html_e( 'Menu', 'cfm' ); ?></span>
			<span class="cfm-burger__bars" aria-hidden="true"><span></span><span></span><span></span></span>
		</button>
	</div>
</header>

<main id="main" class="cfm-main">
