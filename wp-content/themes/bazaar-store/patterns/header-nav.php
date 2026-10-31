<?php
/**
 * Title: Header navigation
 * Slug: bazaar-store/header-nav
 * Categories: bazaar-store
 * Keywords: 
 * Viewport Width: 1400
 * Inserter: no
 *
 * @package Bazaar
 */

?>
<!-- wp:navigation {"overlayMenu":"mobile","layout":{"type":"flex","justifyContent":"center"}} -->
<!-- wp:navigation-link {"label":"Home","url":"<?php echo esc_url( home_url( '/' ) ); ?>","kind":"custom","isTopLevelLink":true} /-->
<!-- wp:navigation-submenu {"label":"Categories","url":"#","kind":"custom","isTopLevelItem":true} -->
<!-- wp:navigation-link {"label":"Kitchen & dining","url":"<?php echo esc_url( home_url( '/product-category/kitchen-dining/' ) ); ?>","kind":"custom","isTopLevelLink":true} /-->
<!-- wp:navigation-link {"label":"Plastic ware","url":"<?php echo esc_url( home_url( '/product-category/plastic-ware/' ) ); ?>","kind":"custom","isTopLevelLink":true} /-->
<!-- wp:navigation-link {"label":"Glassware","url":"<?php echo esc_url( home_url( '/product-category/glassware/' ) ); ?>","kind":"custom","isTopLevelLink":true} /-->
<!-- wp:navigation-link {"label":"Crockery","url":"<?php echo esc_url( home_url( '/product-category/crockery/' ) ); ?>","kind":"custom","isTopLevelLink":true} /-->
<!-- wp:navigation-link {"label":"Home essentials","url":"<?php echo esc_url( home_url( '/product-category/home-essentials/' ) ); ?>","kind":"custom","isTopLevelLink":true} /-->
<!-- /wp:navigation-submenu -->
<!-- wp:navigation-link {"label":"Shop","url":"<?php echo esc_url( function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' ) ); ?>","kind":"custom","isTopLevelLink":true} /-->
<!-- wp:navigation-link {"label":"About","url":"<?php echo esc_url( home_url( '/about-us/' ) ); ?>","kind":"custom","isTopLevelLink":true} /-->
<!-- wp:navigation-link {"label":"Contact","url":"<?php echo esc_url( home_url( '/contact-us/' ) ); ?>","kind":"custom","isTopLevelLink":true} /-->
<!-- /wp:navigation -->
