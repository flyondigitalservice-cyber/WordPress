<?php
/**
 * Site header. Logo: Appearance → Customize → Site Identity.
 * Menu: Appearance → Menus ("Primary (header)"). Texts: Customize → Dubai Gym Flooring → Header.
 *
 * @package DGF
 */

defined( 'ABSPATH' ) || exit;
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="theme-color" content="#111311">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="dgf-skip" href="#main"><?php esc_html_e( 'Skip to content', 'dgf' ); ?></a>

<?php if ( dgf_opt( 'show_topbar' ) ) : ?>
<div class="dgf-topbar">
	<div class="dgf-wrap dgf-topbar__inner">
		<p class="dgf-topbar__text"><?php echo esc_html( dgf_opt( 'topbar_text' ) ); ?></p>
		<ul class="dgf-topbar__links">
			<?php if ( dgf_opt( 'phone_link' ) ) : ?>
				<li><a href="<?php echo esc_url( 'tel:' . preg_replace( '/[^\d+]/', '', dgf_opt( 'phone_link' ) ) ); ?>"><?php echo dgf_icon( 'phone', 14 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php echo esc_html( dgf_opt( 'phone_display' ) ? dgf_opt( 'phone_display' ) : dgf_opt( 'phone_link' ) ); ?></a></li>
			<?php endif; ?>
			<?php if ( dgf_opt( 'email' ) ) : ?>
				<li class="dgf-hide-sm"><a href="<?php echo esc_url( 'mailto:' . dgf_opt( 'email' ) ); ?>"><?php echo dgf_icon( 'mail', 14 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php echo esc_html( dgf_opt( 'email' ) ); ?></a></li>
			<?php endif; ?>
			<?php if ( dgf_opt( 'parent_url' ) ) : ?>
				<li><a href="<?php echo esc_url( dgf_opt( 'parent_url' ) ); ?>" target="_blank" rel="noopener"><?php
					/* translators: %s: mother company name */
					echo esc_html( sprintf( __( 'A %s company', 'dgf' ), dgf_opt( 'parent_name' ) ) );
				?><?php echo dgf_icon( 'external', 12 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a></li>
			<?php endif; ?>
		</ul>
	</div>
</div>
<?php endif; ?>

<header class="dgf-header" data-dgf-header>
	<div class="dgf-wrap dgf-header__inner">
		<a class="dgf-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" aria-label="<?php echo esc_attr( dgf_opt( 'brand_name' ) ); ?>">
			<?php echo dgf_brand_mark( 'light' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</a>

		<nav class="dgf-nav" id="dgf-nav" aria-label="<?php esc_attr_e( 'Main menu', 'dgf' ); ?>" data-dgf-nav>
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'dgf-menu',
					'depth'          => 2,
					'fallback_cb'    => 'dgf_menu_fallback',
				)
			);
			?>
			<a class="dgf-btn dgf-btn--wa dgf-nav__cta" href="<?php echo esc_url( dgf_wa_url( dgf_wa_message_for() ) ); ?>" target="_blank" rel="noopener" data-dgf-wa><?php echo dgf_icon( 'whatsapp', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><?php echo esc_html( dgf_opt( 'header_cta_label' ) ); ?></span></a>
		</nav>

		<a class="dgf-btn dgf-btn--wa dgf-header__cta" href="<?php echo esc_url( dgf_wa_url( dgf_wa_message_for() ) ); ?>" target="_blank" rel="noopener" data-dgf-wa><?php echo dgf_icon( 'whatsapp', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><?php echo esc_html( dgf_opt( 'header_cta_label' ) ); ?></span></a>

		<button class="dgf-burger" type="button" aria-controls="dgf-nav" aria-expanded="false" data-dgf-burger>
			<span class="screen-reader-text"><?php esc_html_e( 'Menu', 'dgf' ); ?></span>
			<span class="dgf-burger__bars" aria-hidden="true"><span></span><span></span><span></span></span>
		</button>
	</div>
</header>

<main id="main" class="dgf-main">
