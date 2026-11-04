<!doctype html>
<html <?php language_attributes(); ?>>
<head>
<?php get_template_part( 'template-parts/head' ); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link screen-reader-text" href="#main"><?php esc_html_e( 'Skip to content', 'raikwar-seal' ); ?></a>

<div class="topbar">
	<div class="wrap">
		<div class="topbar__links">
			<a href="<?php echo esc_attr( raikwar_tel( raikwar_info( 'phone1' ) ) ); ?>"><span><?php echo raikwar_icon( 'phone' ); ?><?php echo esc_html( raikwar_phone_label( raikwar_info( 'phone1' ) ) ); ?></span></a>
			<a href="mailto:<?php echo esc_attr( antispambot( raikwar_info( 'email' ) ) ); ?>"><span><?php echo raikwar_icon( 'mail' ); ?><?php echo esc_html( antispambot( raikwar_info( 'email' ) ) ); ?></span></a>
		</div>
		<div class="topbar__hours"><?php echo esc_html( raikwar_info( 'hours' ) ); ?></div>
	</div>
</div>

<header class="site-header">
	<div class="wrap">
		<a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php echo esc_attr( raikwar_info( 'brand' ) ); ?> – home">
			<?php
			$logo_id = get_theme_mod( 'custom_logo' );
			if ( $logo_id ) {
				echo wp_get_attachment_image( $logo_id, 'medium', false, array( 'alt' => raikwar_info( 'brand' ) . ' logo', 'loading' => 'eager' ) );
			} else {
				echo '<span class="brand__seal" aria-hidden="true">R</span>';
			}
			?>
			<span class="brand__text">
				<span class="brand__name"><?php echo esc_html( raikwar_info( 'brand' ) ); ?></span>
				<span class="brand__sub">Liaisoning &amp; Business Registration</span>
			</span>
		</a>

		<button class="nav-toggle" type="button" aria-expanded="false" aria-controls="site-nav" data-nav-toggle>
			<span class="screen-reader-text"><?php esc_html_e( 'Menu', 'raikwar-seal' ); ?></span>
			<span data-icon-open><?php echo raikwar_icon( 'menu' ); ?></span>
			<span data-icon-close hidden><?php echo raikwar_icon( 'close' ); ?></span>
		</button>

		<nav class="nav" id="site-nav" aria-label="<?php esc_attr_e( 'Primary', 'raikwar-seal' ); ?>">
			<?php
			wp_nav_menu( array(
				'theme_location' => 'primary',
				'container'      => false,
				'fallback_cb'    => 'raikwar_fallback_menu',
				'depth'          => 2,
			) );
			?>
			<a class="btn btn--primary nav__cta" href="<?php echo esc_url( raikwar_wa() ); ?>" target="_blank" rel="noopener"><?php echo raikwar_icon( 'wa' ); ?>WhatsApp us</a>
		</nav>
	</div>
</header>

<main id="main" tabindex="-1">
