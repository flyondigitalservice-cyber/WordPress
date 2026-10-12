<?php
/**
 * Site header.
 *
 * @package HadhramiFashion
 */

$established = hf_opt( 'established' );
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="theme-color" content="#14161a">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link rel="preconnect" href="https://lh3.googleusercontent.com">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'hadhramifashion' ); ?></a>

<div class="announce">
	<?php esc_html_e( 'White shirts at your door in 2 hours', 'hadhramifashion' ); ?>
	<span class="announce__sep">·</span>
	<?php esc_html_e( 'Suits tailored to measure in 24 hours', 'hadhramifashion' ); ?>
	<span class="announce__sep">·</span>
	<a href="<?php echo esc_url( hf_page_url( 'page-templates/express.php' ) ); ?>"><?php esc_html_e( 'Check your address', 'hadhramifashion' ); ?></a>
</div>

<header class="site-header" id="site-header">
	<div class="wrap site-header__inner">
		<button class="nav-toggle" aria-controls="primary-nav" aria-expanded="false" data-nav-open>
			<?php echo hf_icon( 'menu' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<span class="screen-reader-text"><?php esc_html_e( 'Menu', 'hadhramifashion' ); ?></span>
		</button>

		<nav class="primary-nav" id="primary-nav" aria-label="<?php esc_attr_e( 'Primary', 'hadhramifashion' ); ?>">
			<button class="nav-close" data-nav-close aria-label="<?php esc_attr_e( 'Close menu', 'hadhramifashion' ); ?>"><?php echo hf_icon( 'close' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'fallback_cb'    => 'hf_fallback_menu',
					'depth'          => 1,
				)
			);
			?>
		</nav>

		<a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
			<?php if ( has_custom_logo() ) : ?>
				<?php echo wp_get_attachment_image( get_theme_mod( 'custom_logo' ), 'full', false, array( 'alt' => get_bloginfo( 'name' ) ) ); ?>
			<?php else : ?>
				<span class="brand__name">Hadhrami</span>
				<span class="brand__sub">
					<?php
					echo $established
						/* translators: %s: year */
						? esc_html( sprintf( __( 'Tailors · Est. %s', 'hadhramifashion' ), $established ) )
						: esc_html__( 'Fashion · Tailors', 'hadhramifashion' );
					?>
				</span>
			<?php endif; ?>
		</a>

		<div class="header-tools">
			<a href="<?php echo esc_url( hf_page_url( 'page-templates/track.php' ) ); ?>">
				<?php echo hf_icon( 'pin' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<span class="label"><?php esc_html_e( 'Track', 'hadhramifashion' ); ?></span>
			</a>
			<button type="button" data-bag-open aria-controls="bag-drawer">
				<?php echo hf_icon( 'bag' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<span class="label"><?php esc_html_e( 'Bag', 'hadhramifashion' ); ?></span>
				<span class="bag-count" data-bag-count data-count="0">0</span>
			</button>
		</div>
	</div>
</header>

<main id="content" class="site-main">
