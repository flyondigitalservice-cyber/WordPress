<?php
/**
 * Site header.
 *
 * @package Zobo
 */

$zobo_cta = zobod2c_opt( 'calendar_url' ) ? zobod2c_opt( 'calendar_url' ) : ( is_front_page() ? '#contact' : home_url( '/#contact' ) );
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="theme-color" content="#111111">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link" href="#main"><?php esc_html_e( 'Skip to content', 'zobo-d2c' ); ?></a>

<header class="site-header" data-header>
	<div class="container header-inner">
		<div class="brand">
			<?php if ( has_custom_logo() ) : ?>
				<?php the_custom_logo(); ?>
			<?php else : ?>
				<a class="wordmark" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
					<?php echo esc_html( zobod2c_brand_name() ); ?><span class="wordmark-dot">.</span>
				</a>
			<?php endif; ?>
		</div>

		<nav class="primary-nav" id="primary-nav" aria-label="<?php esc_attr_e( 'Primary', 'zobo-d2c' ); ?>" data-nav>
			<?php
			$zobo_menu = ! has_nav_menu( 'primary' ) ? '' : wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'depth'          => 1,
					'fallback_cb'    => false,
					'echo'           => false,
				)
			);
			if ( $zobo_menu ) {
				echo $zobo_menu; // phpcs:ignore WordPress.Security.EscapeOutput
			} else {
				zobod2c_fallback_menu();
			}
			?>
			<a class="btn btn-primary nav-cta-mobile" href="<?php echo esc_url( $zobo_cta ); ?>"><?php esc_html_e( 'Book a free call', 'zobo-d2c' ); ?></a>
		</nav>

		<div class="header-actions">
			<a class="btn btn-primary btn-sm header-cta" href="<?php echo esc_url( $zobo_cta ); ?>">
				<?php esc_html_e( 'Book a free call', 'zobo-d2c' ); ?> <?php echo zobod2c_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</a>
			<button class="nav-toggle" type="button" aria-controls="primary-nav" aria-expanded="false" data-nav-toggle>
				<span class="screen-reader-text"><?php esc_html_e( 'Menu', 'zobo-d2c' ); ?></span>
				<span class="nav-toggle-bar"></span>
				<span class="nav-toggle-bar"></span>
			</button>
		</div>
	</div>
</header>

<main id="main" class="site-main">
