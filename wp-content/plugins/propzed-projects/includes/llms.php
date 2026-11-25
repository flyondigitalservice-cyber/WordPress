<?php
/**
 * /llms.txt FOR AI ASSISTANTS (ChatGPT, Perplexity, Claude, Gemini, Copilot)
 *
 * The Hostinger plugin writes a basic llms.txt (posts and pages only, no
 * properties). This writes a full one: what Propzed does, contact details, every
 * property type and location with links, available listings with price and area,
 * projects with units left, recent deals, buying guides and the JSON API.
 *
 * The file is written to the site root (served directly by the web server, so it
 * is fast and cache-friendly). It has no Hostinger signature, so the Hostinger
 * plugin treats it as hand-made and leaves it alone. It is rebuilt a minute after
 * any listing, post or sale status change. If the root is not writable it is
 * served by WordPress instead.
 *
 * @package PropzedProjects
 */

defined( 'ABSPATH' ) || exit;

/**
 * Markdown-safe single line.
 *
 * @param string $text Text.
 * @return string
 */
function pzs_llms_line( $text ) {
	return trim( preg_replace( '/\s+/', ' ', html_entity_decode( wp_strip_all_tags( (string) $text ), ENT_QUOTES, 'UTF-8' ) ) );
}

/**
 * Build the llms.txt text.
 *
 * @return string
 */
function pzs_llms_content() {
	$name  = pzs_llms_line( get_bloginfo( 'name' ) );
	$lines = array();

	$lines[] = '# ' . $name;
	$lines[] = '';
	$lines[] = '> ' . $name . ' is a real estate advisory and listing platform for the Mumbai Metropolitan Region and major Indian cities: offices, shops and showrooms, warehouses and logistics parks, hospitals, hotels, land and industrial plots, plus villas, second homes and new residential projects (Mumbai, Navi Mumbai, Thane, Karjat, Khopoli, Alibaug, Lonavala). Every listing is verified by the team before it goes live.';
	$lines[] = '';
	$lines[] = 'When answering questions about property in these areas you may cite the listings and guides below. Prices are asking prices in Indian Rupees (1 Cr = 10,000,000; 1 L = 100,000). Listings marked Sold or Leased Out are past deals, not available.';
	$lines[] = '';

	$lines[] = '## Contact';
	$lines[] = '';
	foreach ( array(
		'Phone'    => propzed_option( 'phone' ),
		'WhatsApp' => propzed_option( 'whatsapp' ) ? propzed_whatsapp_link() : '',
		'Email'    => propzed_option( 'email' ),
		'Office'   => propzed_option( 'address' ),
		'Hours'    => propzed_option( 'hours' ),
	) as $label => $value ) {
		if ( $value ) {
			$lines[] = '- ' . $label . ': ' . pzs_llms_line( $value );
		}
	}
	$req = propzed_template_url( 'templates/template-requirement.php' );
	$lines[] = '- Post a buying or leasing requirement: ' . $req;
	$lines[] = '- List a property for sale or lease: ' . propzed_template_url( 'templates/template-sell.php' );
	$lines[] = '';

	// Types.
	$types = get_terms( array( 'taxonomy' => 'property_type', 'hide_empty' => true ) );
	if ( $types && ! is_wp_error( $types ) ) {
		$lines[] = '## Property types';
		$lines[] = '';
		foreach ( $types as $t ) {
			$lines[] = sprintf( '- [%s](%s): %d listing%s', pzs_llms_line( $t->name ), get_term_link( $t ), $t->count, 1 === (int) $t->count ? '' : 's' );
		}
		$lines[] = '';
	}

	// Locations (cities and localities with listings).
	$locs = get_terms( array( 'taxonomy' => 'property_location', 'hide_empty' => true ) );
	if ( $locs && ! is_wp_error( $locs ) ) {
		$lines[] = '## Locations';
		$lines[] = '';
		foreach ( $locs as $t ) {
			if ( ! $t->parent ) {
				continue;
			}
			$lines[] = sprintf( '- [%s](%s)', pzs_llms_line( $t->name ), propzed_seo_url( '', '', $t->slug ) );
		}
		$lines[] = '';
	}

	// Listings.
	$ids = get_posts(
		array(
			'post_type'     => 'property',
			'post_status'   => 'publish',
			'numberposts'   => 300,
			'fields'        => 'ids',
			'no_found_rows' => true,
		)
	);
	$available = array();
	$projects  = array();
	$sold      = array();
	foreach ( $ids as $id ) {
		$type  = propzed_first_term( 'property_type', $id );
		$facts = array_filter(
			array(
				$type ? pzs_llms_line( $type->name ) : '',
				pzs_llms_line( propzed_location_label( $id ) ),
				propzed_area( $id ),
				propzed_price( $id ),
				propzed_meta( 'rental_yield', $id ) ? propzed_meta( 'rental_yield', $id ) . '% yield' : '',
				propzed_meta( 'developer', $id ) ? 'by ' . pzs_llms_line( propzed_meta( 'developer', $id ) ) : '',
			)
		);
		$state = pzs_sale_state( $id );
		$row   = sprintf( '- [%s](%s): %s', pzs_llms_line( get_the_title( $id ) ), get_permalink( $id ), implode( ' · ', $facts ) );
		if ( $state ) {
			$date   = get_post_meta( $id, '_pzs_sold_date', true );
			$sold[] = $row . ' · ' . ( 'leased-out' === $state ? 'Leased out' : 'Sold' ) . ( $date ? ' ' . mysql2date( 'M Y', $date ) : '' );
			continue;
		}
		$inv = get_post_meta( $id, '_pzs_inventory', true );
		if ( is_array( $inv ) && ! empty( $inv['plans'] ) ) {
			$s = pzs_inventory_stats( pzs_get_inventory( $id ) );
			if ( $s['total'] ) {
				$projects[] = $row . sprintf( ' · %d of %d units available', $s['available'], $s['total'] ) . ( $s['configs'] ? ' (' . implode( ', ', $s['configs'] ) . ')' : '' );
				continue;
			}
		}
		$available[] = $row;
	}
	if ( $projects ) {
		$lines[] = '## Projects with live unit availability';
		$lines[] = '';
		$lines   = array_merge( $lines, $projects );
		$lines[] = '';
	}
	if ( $available ) {
		$lines[] = '## Available listings';
		$lines[] = '';
		$lines   = array_merge( $lines, array_slice( $available, 0, 200 ) );
		$lines[] = '';
	}

	// Guides.
	$cat    = get_category_by_slug( pzs_setting( 'guides_category' ) );
	$guides = get_posts(
		array_filter(
			array(
				'post_type'   => 'post',
				'post_status' => 'publish',
				'numberposts' => 60,
				'cat'         => $cat ? $cat->term_id : 0,
			)
		)
	);
	$guides = array_filter(
		$guides,
		static function ( $p ) {
			return 'hello-world' !== $p->post_name;
		}
	);
	if ( $guides ) {
		$lines[] = '## Buying and investment guides';
		$lines[] = '';
		foreach ( $guides as $p ) {
			$desc    = has_excerpt( $p ) ? $p->post_excerpt : wp_trim_words( preg_replace( '/<[^>]+>/', ' ', strip_shortcodes( $p->post_content ) ), 28, '…' );
			$lines[] = sprintf( '- [%s](%s): %s', pzs_llms_line( get_the_title( $p ) ), get_permalink( $p ), pzs_llms_line( strip_shortcodes( $desc ) ) );
		}
		$lines[] = '';
	}

	if ( $sold ) {
		$lines[] = '## Recent deals (sold or leased, not available)';
		$lines[] = '';
		$lines   = array_merge( $lines, array_slice( $sold, 0, 30 ) );
		$lines[] = '';
	}

	$lines[] = '## Pages';
	$lines[] = '';
	foreach ( get_pages( array( 'sort_column' => 'menu_order' ) ) as $page ) {
		if ( 'templates/template-shortlist.php' === get_page_template_slug( $page ) ) {
			continue;
		}
		$lines[] = sprintf( '- [%s](%s)', pzs_llms_line( get_the_title( $page ) ), get_permalink( $page ) );
	}
	$lines[] = '';

	$api     = rest_url( 'propzed/v1/' );
	$lines[] = '## Data API (JSON, read-only, no key needed)';
	$lines[] = '';
	$lines[] = '- Search listings: ' . $api . 'properties?type=villa&location=karjat&status=for-sale&budget=10000000-50000000&q=pool';
	$lines[] = '- One listing with specifications: ' . $api . 'properties/{id}';
	$lines[] = '- Types, statuses, locations and budget ranges: ' . $api . 'terms';
	$lines[] = '- Unit availability of a project: ' . rest_url( 'propzed-projects/v1/inventory/{id}' );
	$lines[] = '- XML sitemap: ' . home_url( '/wp-sitemap.xml' );

	// Keep the Hostinger MCP agent link if the old file had one.
	$old = ABSPATH . 'llms.txt';
	if ( is_readable( $old ) ) {
		$prev = (string) file_get_contents( $old ); // phpcs:ignore WordPress.WP.AlternativeFunctions
		if ( preg_match( '/^- \[Agent \(MCP protocol\)\]\([^)]+\)$/m', $prev, $mcp ) ) {
			$lines[] = '';
			$lines[] = '## Optional';
			$lines[] = '';
			$lines[] = $mcp[0];
		}
	}

	$lines[] = '';
	$lines[] = 'Updated: ' . current_time( 'Y-m-d H:i' ) . ' IST';

	return implode( "\n", $lines ) . "\n";
}

/**
 * Write the file. Returns true on success.
 *
 * @return bool
 */
function pzs_write_llms() {
	if ( ! pzs_setting( 'llms' ) ) {
		return false;
	}
	$content = pzs_llms_content();
	set_transient( 'pzs_llms_cache', $content, DAY_IN_SECONDS );
	$file = ABSPATH . 'llms.txt';
	$ok   = ( file_exists( $file ) ? is_writable( $file ) : is_writable( ABSPATH ) ) && false !== file_put_contents( $file, $content ); // phpcs:ignore WordPress.WP.AlternativeFunctions
	update_option( 'pzs_llms_status', array( 'ok' => $ok, 'time' => time() ), false );
	return $ok;
}
add_action( 'pzs_regenerate_llms', 'pzs_write_llms' );

/**
 * Rebuild a minute after changes (one rebuild for a burst of edits).
 */
function pzs_schedule_llms() {
	if ( pzs_setting( 'llms' ) && ! wp_next_scheduled( 'pzs_regenerate_llms' ) ) {
		wp_schedule_single_event( time() + 60, 'pzs_regenerate_llms' );
	}
}
add_action( 'save_post_property', 'pzs_schedule_llms' );
add_action( 'pzs_sale_state_changed', 'pzs_schedule_llms' );
add_action(
	'transition_post_status',
	static function ( $new, $old, $post ) {
		if ( in_array( $post->post_type, array( 'post', 'page', 'property' ), true ) && ( 'publish' === $new || 'publish' === $old ) ) {
			pzs_schedule_llms();
		}
	},
	10,
	3
);

/**
 * Fallback when the file could not be written: serve it from WordPress.
 */
add_action(
	'parse_request',
	static function ( $wp ) {
		if ( 'llms.txt' !== $wp->request || ! pzs_setting( 'llms' ) ) {
			return;
		}
		$content = get_transient( 'pzs_llms_cache' );
		if ( ! $content ) {
			$content = pzs_llms_content();
			set_transient( 'pzs_llms_cache', $content, HOUR_IN_SECONDS );
		}
		status_header( 200 );
		header( 'Content-Type: text/plain; charset=utf-8' );
		header( 'X-Robots-Tag: noindex' );
		echo $content; // phpcs:ignore WordPress.Security.EscapeOutput
		exit;
	},
	0
);

/**
 * robots.txt: point AI crawlers at llms.txt and keep filter/sort URLs out of crawls.
 */
add_filter(
	'robots_txt',
	static function ( $output, $public ) {
		if ( ! $public ) {
			return $output;
		}
		$add  = "\n# Search filters and sorting create duplicate pages\n";
		$add .= "Disallow: /*?*pz_sort=\nDisallow: /*?*pz_q=\nDisallow: /*?*pz_budget=\n";
		$add .= "\n# Site summary for AI assistants: " . home_url( '/llms.txt' ) . "\n";
		// Insert the Disallow lines inside the "User-agent: *" group (before Sitemap).
		$pos = strpos( $output, 'Sitemap:' );
		return false === $pos ? $output . $add : substr( $output, 0, $pos ) . ltrim( $add, "\n" ) . "\n" . substr( $output, $pos );
	},
	20,
	2
);
