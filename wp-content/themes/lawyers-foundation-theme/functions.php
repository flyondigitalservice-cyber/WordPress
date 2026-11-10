<?php
/**
 * Lawyers Foundation — membership portal theme for the lawyers of Mumbai and Navi Mumbai.
 */
if (!defined('ABSPATH')) exit;

define('LF_VERSION', '2.0.0');

add_action('after_setup_theme', function () {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', ['search-form', 'gallery', 'caption', 'style', 'script']);
    register_nav_menus(['primary' => 'Primary menu', 'footer' => 'Footer menu']);
});

add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('lf-fonts', 'https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700&family=Spectral:ital,wght@0,400;0,500;0,600;1,400&display=swap', [], null);
    wp_enqueue_style('lf', get_stylesheet_uri(), [], LF_VERSION);
    wp_enqueue_script('lf', get_template_directory_uri() . '/assets/theme.js', [], LF_VERSION, true);
});

require get_template_directory() . '/inc/core.php';
require get_template_directory() . '/inc/admin.php';
require get_template_directory() . '/inc/forms.php';
require get_template_directory() . '/inc/views.php';
require get_template_directory() . '/inc/seed.php';
