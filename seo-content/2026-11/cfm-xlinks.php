<?php
/**
 * One-time cross-linking for area pages (draft-theme only). Inserts a links
 * section before the "areas" pills section; skips pages already linked.
 *
 * @package CFM
 */

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'CFMX_SITE' ) ) {
	define( 'CFMX_SITE', untrailingslashit( home_url() ) );
}

function cfmx_xl_links( $eyebrow, $heading, $links ) {
	$lis = '';
	foreach ( $links as $l ) {
		$lis .= "<!-- wp:list-item -->\n<li><a href=\"" . CFMX_SITE . $l[1] . "\">{$l[0]}</a></li>\n<!-- /wp:list-item -->";
	}
	return "<!-- wp:group {\"className\":\"cfm-section cfm-related\"} -->\n<div class=\"wp-block-group cfm-section cfm-related\">"
		. "<!-- wp:paragraph {\"className\":\"cfm-eyebrow\"} -->\n<p class=\"cfm-eyebrow\">$eyebrow</p>\n<!-- /wp:paragraph -->\n\n"
		. "<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">$heading</h2>\n<!-- /wp:heading -->\n\n"
		. "<!-- wp:list {\"className\":\"cfm-checks\"} -->\n<ul class=\"wp-block-list cfm-checks\">$lis</ul>\n<!-- /wp:list -->\n\n"
		. "</div>\n<!-- /wp:group -->\n\n";
}

/**
 * All edits as [post_id, section_html].
 *
 * @return array
 */
function cfmx_xl_edits() {
	$em    = array( 'abu-dhabi' => array( 'Abu Dhabi', 41 ), 'sharjah' => array( 'Sharjah', 42 ), 'ajman' => array( 'Ajman', 43 ), 'ras-al-khaimah' => array( 'Ras Al Khaimah', 44 ), 'fujairah' => array( 'Fujairah', 45 ), 'umm-al-quwain' => array( 'Umm Al Quwain', 46 ), 'al-ain' => array( 'Al Ain', 47 ) );
	$svc   = array( 'curtains', 'blinds', 'carpets' );
	$edits = array();
	foreach ( $em as $k => $e ) {
		$n       = $e[0];
		$edits[] = array( $e[1], cfmx_xl_links( 'Casa Vera Home', "Curtains, blinds &amp; carpets in $n", array(
			array( "Curtains in $n", "/interiors-uae/curtains-$k/" ), array( "Blinds in $n", "/interiors-uae/blinds-$k/" ),
			array( "Carpets in $n", "/interiors-uae/carpets-$k/" ), array( 'Event &amp; exhibition flooring', '/events-exhibitions/' ),
			array( 'Best floor mats: buying guide', '/buying-guides/best-floor-mats-dubai/' ),
		) ) );
	}
	$id = 444;
	foreach ( $em as $k => $e ) {
		$n = $e[0];
		foreach ( $svc as $s ) {
			$o       = array_values( array_diff( $svc, array( $s ) ) );
			$edits[] = array( $id, cfmx_xl_links( "Also in $n", "More for your property in $n", array(
				array( "Floor mats in $n", "/areas-we-serve/floor-mats-$k/" ),
				array( ucfirst( $o[0] ) . " in $n", "/interiors-uae/{$o[0]}-$k/" ),
				array( ucfirst( $o[1] ) . " in $n", "/interiors-uae/{$o[1]}-$k/" ),
				array( 'Event &amp; exhibition flooring', '/events-exhibitions/' ),
			) ) );
			$id++;
		}
	}
	$dubai = array_merge( range( 33, 40 ), range( 431, 442 ) );
	foreach ( $dubai as $pid ) {
		$edits[] = array( $pid, cfmx_xl_links( 'Casa Vera Home · Dubai', 'Curtains, blinds, carpets &amp; events in Dubai', array(
			array( 'Curtains in Dubai', '/curtains/' ), array( 'Blinds in Dubai', '/blinds/' ), array( 'Carpets in Dubai', '/carpets/' ),
			array( 'Event &amp; exhibition flooring', '/events-exhibitions/' ), array( 'Best floor mats in Dubai: buying guide', '/buying-guides/best-floor-mats-dubai/' ),
			array( 'How to choose a floor mat supplier', '/buying-guides/floor-mat-supplier-dubai/' ),
		) ) );
	}
	return $edits;
}

if ( function_exists( 'add_action' ) ) {
	add_action( 'wp_loaded', 'cfmx_run_xlinks', 99 );
}
/**
 * Apply the edits once.
 */
function cfmx_run_xlinks() {
	$flag = 'cfm_xlinks_2026_11';
	if ( get_option( $flag ) || ! isset( $_GET['cfmx_xlinks'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		return;
	}
	update_option( $flag, 'running', false );
	kses_remove_filters();
	$anchor = '<!-- wp:group {"className":"cfm-section cfm-areas"} -->';
	$log    = array( 'edited' => array(), 'skipped' => array(), 'errors' => array() );
	foreach ( cfmx_xl_edits() as $e ) {
		list( $pid, $section ) = $e;
		$content = (string) get_post_field( 'post_content', $pid, 'raw' );
		if ( false !== strpos( $content, $section ) ) {
			$log['skipped'][] = $pid;
			continue;
		}
		if ( 1 !== substr_count( $content, $anchor ) ) {
			$log['errors'][] = $pid . ': anchor count ' . substr_count( $content, $anchor );
			continue;
		}
		$res = wp_update_post( wp_slash( array( 'ID' => $pid, 'post_content' => str_replace( $anchor, $section . $anchor, $content ) ) ), true );
		if ( is_wp_error( $res ) ) {
			$log['errors'][] = $pid . ': ' . $res->get_error_message();
		} else {
			$log['edited'][] = $pid;
		}
	}
	update_option( $flag, wp_json_encode( $log ), false );
}
