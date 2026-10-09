<?php
/**
 * Public screens: /rider/ (installable rider app) and /track/{token}/ (customer live tracking),
 * plus tracking links in WooCommerce emails and My Account.
 *
 * @package OneLoveDelivery
 */

defined( 'ABSPATH' ) || exit;

class OLE_Frontend {

	public static function init() {
		add_action( 'init', array( __CLASS__, 'add_rewrites' ) );
		add_filter( 'query_vars', array( __CLASS__, 'query_vars' ) );
		add_action( 'template_redirect', array( __CLASS__, 'route' ), 1 );

		// Riders live in the app, not in wp-admin.
		add_filter( 'login_redirect', array( __CLASS__, 'login_redirect' ), 20, 3 );
		add_action( 'admin_init', array( __CLASS__, 'keep_riders_out_of_admin' ) );
		add_filter( 'show_admin_bar', array( __CLASS__, 'hide_admin_bar' ) );

		// Tracking link for customers.
		add_action( 'woocommerce_email_after_order_table', array( __CLASS__, 'email_link' ), 5, 4 );
		add_action( 'woocommerce_order_details_before_order_table', array( __CLASS__, 'account_link' ) );
		add_action( 'woocommerce_thankyou', array( __CLASS__, 'account_link' ), 5 );
	}

	public static function add_rewrites() {
		add_rewrite_rule( '^rider/?$', 'index.php?ole_rider_app=app', 'top' );
		add_rewrite_rule( '^rider/manifest\.webmanifest$', 'index.php?ole_rider_app=manifest', 'top' );
		add_rewrite_rule( '^rider/sw\.js$', 'index.php?ole_rider_app=sw', 'top' );
		add_rewrite_rule( '^track/([A-Za-z0-9]{20,40})/?$', 'index.php?ole_track=$matches[1]', 'top' );
	}

	public static function query_vars( $vars ) {
		$vars[] = 'ole_rider_app';
		$vars[] = 'ole_track';
		return $vars;
	}

	protected static function no_cache_headers() {
		if ( ! defined( 'DONOTCACHEPAGE' ) ) {
			define( 'DONOTCACHEPAGE', true );
		}
		nocache_headers();
		header( 'X-LiteSpeed-Cache-Control: no-cache' );
		header( 'X-Robots-Tag: noindex, nofollow' );
	}

	public static function route() {
		$app   = get_query_var( 'ole_rider_app' );
		$token = get_query_var( 'ole_track' );

		if ( 'manifest' === $app ) {
			self::manifest();
		} elseif ( 'sw' === $app ) {
			self::service_worker();
		} elseif ( 'app' === $app ) {
			self::rider_app();
		} elseif ( $token ) {
			self::track_page( $token );
		}
	}

	/* Rider app ---------------------------------------------------------- */

	protected static function rider_app() {
		self::no_cache_headers();
		status_header( 200 );
		$logged_in = is_user_logged_in();
		$allowed   = $logged_in && current_user_can( 'ole_deliver' );
		$config    = array(
			'api'     => esc_url_raw( rest_url( OLE_REST::NS ) ),
			'nonce'   => $allowed ? wp_create_nonce( 'wp_rest' ) : '',
			'logout'  => wp_logout_url( home_url( '/rider/' ) ),
			'sw'      => home_url( '/rider/sw.js' ),
			'reasons' => array( 'Customer not reachable', 'Customer not at address', 'Wrong / incomplete address', 'Customer refused order', 'Cash not available (COD)', 'Vehicle / safety issue' ),
		);
		include OLE_DELIVERY_DIR . 'templates/rider-app.php';
		exit;
	}

	protected static function manifest() {
		self::no_cache_headers();
		header( 'Content-Type: application/manifest+json; charset=utf-8' );
		echo wp_json_encode( array(
			'name'             => OLE_Settings::get( 'store_name' ) . ' Rider',
			'short_name'       => 'OL Rider',
			'start_url'        => home_url( '/rider/' ),
			'scope'            => home_url( '/rider/' ),
			'display'          => 'standalone',
			'orientation'      => 'portrait',
			'background_color' => '#0b0b0b',
			'theme_color'      => '#0b0b0b',
			'icons'            => array(
				array(
					'src'     => OLE_DELIVERY_URL . 'assets/img/icon.svg',
					'sizes'   => 'any',
					'type'    => 'image/svg+xml',
					'purpose' => 'any maskable',
				),
			),
		), JSON_UNESCAPED_SLASHES );
		exit;
	}

	protected static function service_worker() {
		self::no_cache_headers();
		header( 'Content-Type: application/javascript; charset=utf-8' );
		header( 'Service-Worker-Allowed: ' . wp_parse_url( home_url( '/rider/' ), PHP_URL_PATH ) );
		readfile( OLE_DELIVERY_DIR . 'assets/js/sw.js' ); // phpcs:ignore WordPress.WP.AlternativeFunctions
		exit;
	}

	/* Customer tracking -------------------------------------------------- */

	protected static function track_page( $token ) {
		$d = OLE_Deliveries::get_by_token( $token );
		self::no_cache_headers();
		if ( ! $d ) {
			status_header( 404 );
		} else {
			status_header( 200 );
		}
		$config = array(
			'api'     => esc_url_raw( rest_url( OLE_REST::NS . '/track/' . $token ) ),
			'mapsKey' => OLE_Settings::get( 'maps_key' ),
			'shop'    => home_url( '/' ),
		);
		include OLE_DELIVERY_DIR . 'templates/track.php';
		exit;
	}

	/* Rider login behaviour --------------------------------------------- */

	protected static function is_only_rider( $user ) {
		return $user instanceof WP_User && $user->has_cap( 'ole_deliver' ) && ! $user->has_cap( 'edit_posts' ) && ! $user->has_cap( 'manage_woocommerce' );
	}

	public static function login_redirect( $redirect, $requested, $user ) {
		return self::is_only_rider( $user ) ? home_url( '/rider/' ) : $redirect;
	}

	public static function keep_riders_out_of_admin() {
		if ( wp_doing_ajax() || ! self::is_only_rider( wp_get_current_user() ) ) {
			return;
		}
		$page = isset( $GLOBALS['pagenow'] ) ? $GLOBALS['pagenow'] : '';
		if ( in_array( $page, array( 'admin-post.php', 'async-upload.php' ), true ) ) {
			return;
		}
		wp_safe_redirect( home_url( '/rider/' ) );
		exit;
	}

	public static function hide_admin_bar( $show ) {
		return self::is_only_rider( wp_get_current_user() ) ? false : $show;
	}

	/* Tracking links ------------------------------------------------------ */

	public static function email_link( $order, $sent_to_admin, $plain_text, $email = null ) {
		if ( $sent_to_admin || ! $order instanceof WC_Order ) {
			return;
		}
		$d = OLE_Deliveries::get_by_order( $order->get_id() );
		if ( ! $d && $order->has_status( 'processing' ) ) {
			$d = OLE_Deliveries::create_for_order( $order );
		}
		if ( ! $d || in_array( $d->status, array( 'delivered', 'cancelled' ), true ) ) {
			return;
		}
		$url     = OLE_Deliveries::track_url( $d );
		$promise = 'Expected within ' . OLE_Settings::promise_text() . ' of your order.';
		if ( $plain_text ) {
			echo "\n" . esc_html( $promise ) . "\nTrack your delivery live: " . esc_url_raw( $url ) . "\n";
			return;
		}
		printf(
			'<p style="margin:16px 0 8px;font-weight:bold;">%s</p><p style="margin:0 0 24px;"><a href="%s" style="display:inline-block;background:#128a3f;color:#ffffff;font-weight:bold;padding:12px 22px;border-radius:999px;text-decoration:none;">Track your delivery live &rarr;</a></p>',
			esc_html( $promise ),
			esc_url( $url )
		);
	}

	public static function account_link( $order ) {
		$order = wc_get_order( $order );
		if ( ! $order ) {
			return;
		}
		$d = OLE_Deliveries::get_by_order( $order->get_id() );
		if ( ! $d || 'cancelled' === $d->status ) {
			return;
		}
		static $printed = array();
		if ( isset( $printed[ $d->id ] ) ) {
			return;
		}
		$printed[ $d->id ] = true;
		printf(
			'<p class="ole-track-cta">%s<a class="button ole-btn ole-btn--green" href="%s">%s</a></p>',
			'delivered' === $d->status ? '' : '<strong>Expected within ' . esc_html( OLE_Settings::promise_text() ) . '.</strong><br>',
			esc_url( OLE_Deliveries::track_url( $d ) ),
			'delivered' === $d->status ? 'View delivery details' : 'Track your delivery live &rarr;'
		);
	}
}
