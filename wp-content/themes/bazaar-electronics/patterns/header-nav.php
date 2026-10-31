<?php
/**
 * Title: Header navigation
 * Slug: bazaar-electronics/header-nav
 * Categories: bazaar-store
 * Keywords: 
 * Viewport Width: 1400
 * Inserter: no
 *
 * @package Bazaar
 */

?>
<!-- wp:navigation {"overlayMenu":"mobile","layout":{"type":"flex","justifyContent":"left"}} -->
<!-- wp:navigation-submenu {"label":"Categories","url":"#","kind":"custom","isTopLevelItem":true} -->
<!-- wp:navigation-link {"label":"Smartphones","url":"<?php echo esc_url( home_url( '/product-category/smartphones/' ) ); ?>","kind":"custom","isTopLevelLink":true} /-->
<!-- wp:navigation-link {"label":"Laptops","url":"<?php echo esc_url( home_url( '/product-category/laptops/' ) ); ?>","kind":"custom","isTopLevelLink":true} /-->
<!-- wp:navigation-link {"label":"Audio","url":"<?php echo esc_url( home_url( '/product-category/audio/' ) ); ?>","kind":"custom","isTopLevelLink":true} /-->
<!-- wp:navigation-link {"label":"Wearables","url":"<?php echo esc_url( home_url( '/product-category/wearables/' ) ); ?>","kind":"custom","isTopLevelLink":true} /-->
<!-- wp:navigation-link {"label":"Cameras","url":"<?php echo esc_url( home_url( '/product-category/cameras/' ) ); ?>","kind":"custom","isTopLevelLink":true} /-->
<!-- wp:navigation-link {"label":"Accessories","url":"<?php echo esc_url( home_url( '/product-category/accessories/' ) ); ?>","kind":"custom","isTopLevelLink":true} /-->
<!-- /wp:navigation-submenu -->
<!-- wp:navigation-link {"label":"All products","url":"<?php echo esc_url( function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' ) ); ?>","kind":"custom","isTopLevelLink":true} /-->
<!-- wp:navigation-link {"label":"Compare","url":"<?php echo esc_url( home_url( '/compare/' ) ); ?>","kind":"custom","isTopLevelLink":true} /-->
<!-- wp:navigation-link {"label":"Warranty","url":"<?php echo esc_url( home_url( '/warranty-service/' ) ); ?>","kind":"custom","isTopLevelLink":true} /-->
<!-- wp:navigation-link {"label":"About","url":"<?php echo esc_url( home_url( '/about-us/' ) ); ?>","kind":"custom","isTopLevelLink":true} /-->
<!-- wp:navigation-link {"label":"Contact","url":"<?php echo esc_url( home_url( '/contact-us/' ) ); ?>","kind":"custom","isTopLevelLink":true} /-->
<!-- /wp:navigation -->
