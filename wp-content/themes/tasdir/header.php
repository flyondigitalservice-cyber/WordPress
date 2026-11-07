<?php
/**
 * Header.
 *
 * @package Tasdir
 */

defined( 'ABSPATH' ) || exit;
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<meta name="theme-color" content="#14234a">
<meta name="format-detection" content="telephone=no">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link screen-reader-text" href="#main"><?php esc_html_e( 'Skip to content', 'tasdir' ); ?></a>

<?php if ( tasdir_opt( 'show_topbar' ) ) : ?>
<div class="topbar">
	<div class="wrap">
		<span class="topbar__hide-sm"><?php esc_html_e( 'Exporters of premium fresh fruit from India', 'tasdir' ); ?></span>
		<?php if ( tasdir_wa_number() ) : ?>
			<a href="<?php echo esc_url( tasdir_wa_url() ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'WhatsApp:', 'tasdir' ); ?> +<?php echo esc_html( tasdir_wa_number() ); ?></a>
		<?php endif; ?>
		<?php if ( tasdir_opt( 'email' ) ) : ?>
			<a class="topbar__hide-sm" href="mailto:<?php echo esc_attr( antispambot( tasdir_opt( 'email' ) ) ); ?>"><?php echo esc_html( antispambot( tasdir_opt( 'email' ) ) ); ?></a>
		<?php endif; ?>
	</div>
</div>
<?php endif; ?>

<header class="site-header">
	<div class="wrap">
		<?php tasdir_logo(); ?>

		<button class="nav-toggle" type="button" aria-controls="primary-nav" aria-expanded="false">
			<?php tasdir_the_icon( 'menu' ); ?><span class="screen-reader-text"><?php esc_html_e( 'Open menu', 'tasdir' ); ?></span>
		</button>

		<nav id="primary-nav" class="primary-nav" aria-label="<?php esc_attr_e( 'Primary', 'tasdir' ); ?>">
			<button class="nav-toggle nav-close" type="button" aria-controls="primary-nav">
				<?php tasdir_the_icon( 'close' ); ?><span class="screen-reader-text"><?php esc_html_e( 'Close menu', 'tasdir' ); ?></span>
			</button>
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'fallback_cb'    => 'tasdir_menu_fallback',
					'depth'          => 2,
				)
			);
			?>
			<div class="primary-nav__cta">
				<a class="btn btn--wa btn--block" href="<?php echo esc_url( tasdir_wa_url() ); ?>" target="_blank" rel="noopener"><?php tasdir_the_icon( 'whatsapp' ); ?><?php esc_html_e( 'Chat on WhatsApp', 'tasdir' ); ?></a>
			</div>
		</nav>

		<a class="btn btn--gold header-cta" href="<?php echo esc_url( home_url( '/#enquiry' ) ); ?>" data-scroll><?php esc_html_e( 'Get a Quote', 'tasdir' ); ?></a>
	</div>
</header>
<div class="nav-backdrop" hidden></div>

<main id="main">
