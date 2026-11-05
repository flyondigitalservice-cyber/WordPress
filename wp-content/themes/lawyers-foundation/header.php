<?php
/**
 * Header.
 *
 * @package Lawyers_Foundation_Theme
 */

?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link screen-reader-text" href="#main"><?php esc_html_e( 'Skip to content', 'lawyers-foundation-theme' ); ?></a>
<header class="site-header">
	<div class="wrap">
		<a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
			<?php
			if ( has_custom_logo() ) {
				echo wp_get_attachment_image( get_theme_mod( 'custom_logo' ), 'full', false, array( 'class' => 'custom-logo', 'alt' => '' ) );
			} else {
				echo lft_mark(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
			?>
			<span><?php echo esc_html( lft_name() ); ?></span>
		</a>
		<button class="nav-toggle" aria-expanded="false" aria-controls="primary-nav"><?php esc_html_e( 'Menu', 'lawyers-foundation-theme' ); ?></button>
		<nav class="primary-nav" id="primary-nav" aria-label="<?php esc_attr_e( 'Primary', 'lawyers-foundation-theme' ); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'depth'          => 1,
					'fallback_cb'    => 'lft_fallback_menu',
				)
			);
			?>
		</nav>
		<?php if ( lft_has_plugin() ) : ?>
			<div class="header-cta">
				<?php if ( is_user_logged_in() ) : ?>
					<a class="btn btn--line btn--sm" href="<?php echo esc_url( lft_url( 'dashboard' ) ); ?>"><?php esc_html_e( 'My dashboard', 'lawyers-foundation-theme' ); ?></a>
				<?php else : ?>
					<a class="btn btn--line btn--sm" href="<?php echo esc_url( wp_login_url( lft_url( 'dashboard' ) ) ); ?>"><?php esc_html_e( 'Sign in', 'lawyers-foundation-theme' ); ?></a>
					<a class="btn btn--sm" href="<?php echo esc_url( lft_url( 'join' ) ); ?>"><?php esc_html_e( 'Join', 'lawyers-foundation-theme' ); ?></a>
				<?php endif; ?>
			</div>
		<?php endif; ?>
	</div>
</header>
<main id="main">
