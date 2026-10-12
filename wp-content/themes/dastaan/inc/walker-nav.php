<?php
/**
 * Nav walker with dropdown + mega menu support.
 *
 * Usage: add CSS class "mega" to a top-level menu item (Appearance → Menus → Screen Options → CSS classes).
 * Its children become column headings, grandchildren become links. Add class "badge-new" to any item for a "New" tag.
 *
 * @package Dastaan
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Dastaan_Walker_Nav' ) ) {
	/**
	 * Walker.
	 */
	class Dastaan_Walker_Nav extends Walker_Nav_Menu {

		/**
		 * Whether we are inside a mega item.
		 *
		 * @var bool
		 */
		private $in_mega = false;

		/**
		 * Start sub-level.
		 *
		 * @param string   $output Output.
		 * @param int      $depth  Depth.
		 * @param stdClass $args   Args.
		 */
		public function start_lvl( &$output, $depth = 0, $args = null ) {
			$indent = str_repeat( "\t", $depth );
			if ( 0 === $depth && $this->in_mega ) {
				$output .= "\n$indent<div class=\"mega-panel\"><ul class=\"mega-columns\">\n";
			} elseif ( 0 === $depth ) {
				$output .= "\n$indent<ul class=\"sub-menu\">\n";
			} else {
				$output .= "\n$indent<ul class=\"sub-menu sub-menu--level-$depth\">\n";
			}
		}

		/**
		 * End sub-level.
		 *
		 * @param string   $output Output.
		 * @param int      $depth  Depth.
		 * @param stdClass $args   Args.
		 */
		public function end_lvl( &$output, $depth = 0, $args = null ) {
			$indent = str_repeat( "\t", $depth );
			if ( 0 === $depth && $this->in_mega ) {
				$output .= "$indent</ul></div>\n";
			} else {
				$output .= "$indent</ul>\n";
			}
		}

		/**
		 * Start element.
		 *
		 * @param string   $output Output.
		 * @param WP_Post  $item   Item.
		 * @param int      $depth  Depth.
		 * @param stdClass $args   Args.
		 * @param int      $id     ID.
		 */
		public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
			$classes = empty( $item->classes ) ? array() : (array) $item->classes;
			$classes = array_filter( array_map( 'sanitize_html_class', $classes ) );

			$has_children = in_array( 'menu-item-has-children', $classes, true );
			$is_mega      = 0 === $depth && in_array( 'mega', $classes, true ) && $has_children;
			if ( 0 === $depth ) {
				$this->in_mega = $is_mega;
			}

			$classes[] = 'menu-item-depth-' . $depth;
			if ( $is_mega ) {
				$classes[] = 'menu-item-mega';
			}
			if ( 1 === $depth && $this->in_mega ) {
				$classes[] = 'mega-column';
			}

			$class_names = implode( ' ', apply_filters( 'nav_menu_css_class', array_unique( $classes ), $item, $args, $depth ) );
			$output     .= '<li id="menu-item-' . esc_attr( $item->ID ) . '" class="' . esc_attr( $class_names ) . '">';

			$atts           = array();
			$atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
			$atts['target'] = ! empty( $item->target ) ? $item->target : '';
			$atts['rel']    = ! empty( $item->xfn ) ? $item->xfn : ( '_blank' === $atts['target'] ? 'noopener' : '' );
			$atts['href']   = ! empty( $item->url ) ? $item->url : '';
			$atts['class']  = 'menu-link';
			if ( $has_children && $depth < 2 ) {
				$atts['aria-haspopup'] = 'true';
				$atts['aria-expanded'] = 'false';
			}
			if ( in_array( 'current-menu-item', $classes, true ) ) {
				$atts['aria-current'] = 'page';
			}
			$atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args, $depth );

			$attributes = '';
			foreach ( $atts as $attr => $value ) {
				if ( '' === $value ) {
					continue;
				}
				$value       = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
				$attributes .= ' ' . $attr . '="' . $value . '"';
			}

			$title = apply_filters( 'the_title', $item->title, $item->ID );
			$title = apply_filters( 'nav_menu_item_title', $title, $item, $args, $depth );

			$badge = '';
			if ( in_array( 'badge-new', $classes, true ) ) {
				$badge = '<span class="menu-badge">' . esc_html__( 'New', 'dastaan' ) . '</span>';
			} elseif ( in_array( 'badge-sale', $classes, true ) ) {
				$badge = '<span class="menu-badge menu-badge--sale">' . esc_html__( 'Sale', 'dastaan' ) . '</span>';
			}

			$item_output  = isset( $args->before ) ? $args->before : '';
			$item_output .= '<a' . $attributes . '><span class="menu-text">' . esc_html( $title ) . '</span>' . $badge;
			if ( $has_children && 0 === $depth ) {
				$item_output .= dastaan_icon( 'chevron-down', array( 'size' => 14, 'class' => 'menu-caret' ) );
			}
			$item_output .= '</a>';

			// Mega column image: use the item description if it is an image URL.
			if ( 1 === $depth && $this->in_mega && ! empty( $item->description ) && preg_match( '/^https?:\/\/\S+\.(jpe?g|png|webp|gif)(\?\S*)?$/i', trim( $item->description ) ) ) {
				$item_output .= '<a class="mega-image" href="' . esc_url( $item->url ) . '" tabindex="-1" aria-hidden="true"><img src="' . esc_url( trim( $item->description ) ) . '" alt="" loading="lazy" decoding="async" /></a>';
			}

			if ( $has_children && $depth < 2 ) {
				$item_output .= '<button class="submenu-toggle" type="button" aria-expanded="false" aria-label="' . esc_attr( sprintf( /* translators: %s: menu item */ __( 'Open %s submenu', 'dastaan' ), wp_strip_all_tags( $title ) ) ) . '">' . dastaan_icon( 'chevron-down', array( 'size' => 16 ) ) . '</button>';
			}
			$item_output .= isset( $args->after ) ? $args->after : '';

			$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
		}

		/**
		 * End element.
		 *
		 * @param string   $output Output.
		 * @param WP_Post  $item   Item.
		 * @param int      $depth  Depth.
		 * @param stdClass $args   Args.
		 */
		public function end_el( &$output, $item, $depth = 0, $args = null ) {
			$output .= "</li>\n";
			if ( 0 === $depth ) {
				$this->in_mega = false;
			}
		}
	}
}

/**
 * Fallback when no menu is assigned: list product categories + pages.
 */
function dastaan_menu_fallback() {
	echo '<ul class="menu menu--fallback">';
	if ( dastaan_has_wc() ) {
		printf( '<li class="menu-item menu-item-depth-0"><a class="menu-link" href="%s"><span class="menu-text">%s</span></a></li>', esc_url( wc_get_page_permalink( 'shop' ) ), esc_html__( 'Shop All', 'dastaan' ) );
		$cats = get_terms(
			array(
				'taxonomy'   => 'product_cat',
				'hide_empty' => true,
				'number'     => 6,
				'exclude'    => array( get_option( 'default_product_cat' ) ),
				'orderby'    => 'count',
				'order'      => 'DESC',
			)
		);
		if ( ! is_wp_error( $cats ) ) {
			foreach ( $cats as $cat ) {
				printf( '<li class="menu-item menu-item-depth-0"><a class="menu-link" href="%s"><span class="menu-text">%s</span></a></li>', esc_url( get_term_link( $cat ) ), esc_html( $cat->name ) );
			}
		}
	}
	if ( ! dastaan_has_wc() ) {
		// Without WooCommerce, fall back to the top-level pages (kept short so the header stays on one line).
		wp_list_pages(
			array(
				'title_li' => '',
				'depth'    => 1,
				'number'   => 6,
				'exclude'  => (string) get_option( 'wp_page_for_privacy_policy' ),
			)
		);
	}
	echo '</ul>';
}
