<?php
/**
 * Site header.
 *
 * @package Bombay
 */

defined( 'ABSPATH' ) || exit;
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="bm-skip" href="#content">Skip to content</a>
<header class="bm-header" id="bm-header">
	<div class="bm-header-in">
		<?php bombay_logo(); ?>
		<nav class="bm-nav" aria-label="Main">
			<?php bombay_menu( 'bm-menu', 'bm-menu' ); ?>
		</nav>
		<button class="bm-burger" type="button" aria-expanded="false" aria-controls="bm-mobile" aria-label="Open menu">
			<span></span><span></span><span></span>
		</button>
	</div>
</header>
<div class="bm-mobile" id="bm-mobile" hidden>
	<div class="bm-mobile-head">
		<?php bombay_logo(); ?>
		<button class="bm-close" type="button" aria-label="Close menu">
			<svg viewBox="0 0 24 24" width="24" height="24" aria-hidden="true"><path d="M5 5l14 14M19 5L5 19" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
		</button>
	</div>
	<nav class="bm-mobile-nav" aria-label="Mobile">
		<?php bombay_menu( 'bm-mmenu', 'bm-mmenu' ); ?>
	</nav>
</div>
<div id="content" class="bm-site-content">
