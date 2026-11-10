<?php if (!defined('ABSPATH')) exit; ?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width,initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip" href="#main">Skip to content</a>
<div class="topbar no-print">
  <div class="wrap">
    <p><a href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/', '', lf_opt('phone'))); ?>"><?php echo lf_icon('phone'); ?><?php echo esc_html(lf_opt('phone')); ?></a>
       <a href="mailto:<?php echo esc_attr(lf_opt('email')); ?>"><?php echo lf_icon('mail'); ?><?php echo esc_html(lf_opt('email')); ?></a></p>
    <p class="topbar-links">
      <?php if (is_user_logged_in() && lf_member_status()) : ?>
        <a href="<?php echo esc_url(lf_url('dashboard')); ?>">My dashboard</a><a href="<?php echo esc_url(lf_logout_url()); ?>">Sign out</a>
      <?php else : ?>
        <a href="<?php echo esc_url(lf_url('login')); ?>">Member sign in</a>
      <?php endif; ?>
    </p>
  </div>
</div>
<header class="site-header no-print">
  <div class="wrap">
    <a class="brand" href="<?php echo esc_url(home_url('/')); ?>" aria-label="<?php echo esc_attr(lf_org()); ?> home">
      <?php echo lf_logo_mark(); ?>
      <span><b><?php echo esc_html(strtoupper(lf_org())); ?></b><small><?php echo esc_html(lf_opt('org_region')); ?></small></span>
    </a>
    <button class="menu-btn" type="button" aria-controls="site-nav" aria-expanded="false">Menu</button>
    <nav class="nav" id="site-nav" aria-label="Main">
      <?php wp_nav_menu(['theme_location' => 'primary', 'container' => false, 'menu_class' => 'menu', 'depth' => 2, 'fallback_cb' => 'lf_fallback_menu']); ?>
      <?php if (is_user_logged_in() && lf_member_status()) : ?>
        <a class="btn btn-gold nav-cta" href="<?php echo esc_url(lf_url('dashboard')); ?>">Dashboard</a>
      <?php else : ?>
        <a class="btn btn-gold nav-cta" href="<?php echo esc_url(lf_url('join')); ?>">Join us</a>
      <?php endif; ?>
    </nav>
  </div>
</header>
<main id="main">
