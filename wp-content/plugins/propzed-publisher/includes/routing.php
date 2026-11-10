<?php
/**
 * Shared plumbing: settings, virtual pages (/builders/ and /publisher/),
 * template loading, rate limiting and small helpers.
 *
 * @package PropzedPublisher
 */

defined( 'ABSPATH' ) || exit;

/**
 * Plugin settings with defaults (Properties > Publisher Settings).
 *
 * @param string $key Setting key.
 * @return mixed
 */
function pzp_setting( $key ) {
	$defaults = array(
		'signup'         => 1, // Allow new publisher sign-ups.
		'menu_builders'  => 1, // Add "Builders" to the header menu.
		'menu_publisher' => 1, // Add "Publisher Login / My Listings" to the header menu.
		'home_builders'  => 1, // Show the "Top Builders" strip on the homepage.
		'enhance'        => 1, // Load the mobile / accessibility / conversion layer.
		'card_whatsapp'  => 1, // WhatsApp shortcut on property cards.
		'max_pending'    => 20, // Listings one publisher may have waiting for review.
	);
	$saved = get_option( 'pzp_settings', array() );
	$all   = array_merge( $defaults, is_array( $saved ) ? $saved : array() );
	return isset( $all[ $key ] ) ? $all[ $key ] : null;
}

/**
 * Register the two virtual pages and refresh permalinks after an update.
 */
function pzp_register_routes() {
	add_rewrite_rule( '^builders/?$', 'index.php?pzp_view=builders', 'top' );
	add_rewrite_rule( '^publisher/?$', 'index.php?pzp_view=publisher', 'top' );
}
add_action( 'init', 'pzp_register_routes', 12 );

add_filter(
	'query_vars',
	static function ( $vars ) {
		$vars[] = 'pzp_view';
		return $vars;
	}
);

/**
 * Soft-flush permalinks once per plugin version (no .htaccess write).
 */
add_action(
	'init',
	static function () {
		if ( get_option( 'pzp_rewrite_version' ) !== PZP_VERSION ) {
			flush_rewrite_rules( false );
			update_option( 'pzp_rewrite_version', PZP_VERSION );
		}
	},
	999
);

/**
 * Which virtual page is being shown: 'builders', 'publisher' or ''.
 *
 * @return string
 */
function pzp_current_view() {
	$view = get_query_var( 'pzp_view' );
	return in_array( $view, array( 'builders', 'publisher' ), true ) ? $view : '';
}

/**
 * URL of the builders directory.
 *
 * @return string
 */
function pzp_builders_url() {
	return get_option( 'permalink_structure' ) ? home_url( user_trailingslashit( 'builders' ) ) : add_query_arg( 'pzp_view', 'builders', home_url( '/' ) );
}

/**
 * URL of the publisher area.
 *
 * @param array $args Query arguments.
 * @return string
 */
function pzp_publisher_url( $args = array() ) {
	$base = get_option( 'permalink_structure' ) ? home_url( user_trailingslashit( 'publisher' ) ) : add_query_arg( 'pzp_view', 'publisher', home_url( '/' ) );
	return $args ? add_query_arg( $args, $base ) : $base;
}

/**
 * Virtual pages are not the blog: fix the query flags and keep the query cheap.
 *
 * @param WP_Query $query Query.
 */
function pzp_virtual_query( $query ) {
	if ( is_admin() || ! $query->is_main_query() || ! $query->get( 'pzp_view' ) ) {
		return;
	}
	$query->is_home    = false;
	$query->is_archive = false;
	$query->set( 'posts_per_page', 1 );
	$query->set( 'no_found_rows', true );
	$query->set( 'ignore_sticky_posts', true );
}
add_action( 'pre_get_posts', 'pzp_virtual_query' );

/**
 * Virtual pages always answer 200, even on a site with no blog posts.
 */
add_filter(
	'pre_handle_404',
	static function ( $handled, $query ) {
		if ( $query->is_main_query() && $query->get( 'pzp_view' ) ) {
			status_header( 200 );
			return true;
		}
		return $handled;
	},
	10,
	2
);

/**
 * Load this plugin's templates for builder pages and the virtual pages.
 * A child theme can override any of them by adding
 * propzed-publisher/{builder,builders,publisher}.php.
 *
 * @param string $template Template chosen by WordPress.
 * @return string
 */
function pzp_template_include( $template ) {
	$name = '';
	if ( pzp_current_view() ) {
		$name = pzp_current_view();
	} elseif ( is_tax( 'property_builder' ) ) {
		$name = 'builder';
	}
	if ( ! $name ) {
		return $template;
	}
	$override = locate_template( 'propzed-publisher/' . $name . '.php' );
	if ( $override ) {
		return $override;
	}
	$file = PZP_DIR . 'templates/' . $name . '.php';
	return is_readable( $file ) ? $file : $template;
}
add_filter( 'template_include', 'pzp_template_include', 50 );

add_filter(
	'body_class',
	static function ( $classes ) {
		if ( pzp_current_view() ) {
			$classes[] = 'pzp-view-' . pzp_current_view();
		}
		return $classes;
	}
);

/**
 * The publisher area is personal: never cache it (browser, LiteSpeed, others).
 */
add_action(
	'template_redirect',
	static function () {
		if ( 'publisher' !== pzp_current_view() ) {
			return;
		}
		if ( ! defined( 'DONOTCACHEPAGE' ) ) {
			define( 'DONOTCACHEPAGE', true );
		}
		do_action( 'litespeed_control_set_nocache', 'propzed publisher area' );
		nocache_headers();
	},
	1
);

/**
 * Simple per-visitor rate limit (transient based).
 *
 * @param string $bucket Name of the limited action.
 * @param int    $max    Attempts allowed inside the window.
 * @param int    $window Window in seconds.
 * @return bool True when the visitor is over the limit.
 */
function pzp_rate_limited( $bucket, $max, $window ) {
	$ip    = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
	$key   = 'pzp_rl_' . md5( $bucket . '|' . $ip );
	$count = (int) get_transient( $key );
	if ( $count >= $max ) {
		return true;
	}
	set_transient( $key, $count + 1, $window );
	return false;
}

/**
 * One or two initials for a name, used when a builder has no logo.
 *
 * @param string $name Name.
 * @return string
 */
function pzp_initials( $name ) {
	$words = preg_split( '/\s+/', trim( wp_strip_all_tags( $name ) ) );
	$out   = '';
	foreach ( array_slice( array_filter( (array) $words ), 0, 2 ) as $word ) {
		$out .= function_exists( 'mb_substr' ) ? mb_substr( $word, 0, 1 ) : substr( $word, 0, 1 );
	}
	return function_exists( 'mb_strtoupper' ) ? mb_strtoupper( $out ) : strtoupper( $out );
}

/**
 * Purge cached copies of a URL when a page cache plugin is present.
 *
 * @param string $url URL.
 */
function pzp_purge_url( $url ) {
	do_action( 'litespeed_purge_url', $url );
}
