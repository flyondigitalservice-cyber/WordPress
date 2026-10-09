<?php
/**
 * REST API: rider app, public tracking and the admin dispatch board.
 * Namespace: /wp-json/ole-delivery/v1
 *
 * @package OneLoveDelivery
 */

defined( 'ABSPATH' ) || exit;

class OLE_REST {

	const NS = 'ole-delivery/v1';

	public static function init() {
		add_action( 'rest_api_init', array( __CLASS__, 'routes' ) );
	}

	public static function routes() {
		$rider = array( __CLASS__, 'can_ride' );
		$admin = array( __CLASS__, 'can_dispatch' );
		$id    = '(?P<id>\d+)';

		register_rest_route( self::NS, '/rider/me', array( 'methods' => 'GET', 'callback' => array( __CLASS__, 'rider_me' ), 'permission_callback' => $rider ) );
		register_rest_route( self::NS, '/rider/duty', array( 'methods' => 'POST', 'callback' => array( __CLASS__, 'rider_duty' ), 'permission_callback' => $rider ) );
		register_rest_route( self::NS, '/rider/location', array( 'methods' => 'POST', 'callback' => array( __CLASS__, 'rider_location' ), 'permission_callback' => $rider ) );
		register_rest_route( self::NS, "/rider/deliveries/{$id}/start", array( 'methods' => 'POST', 'callback' => array( __CLASS__, 'rider_start' ), 'permission_callback' => $rider ) );
		register_rest_route( self::NS, "/rider/deliveries/{$id}/deliver", array( 'methods' => 'POST', 'callback' => array( __CLASS__, 'rider_deliver' ), 'permission_callback' => $rider ) );
		register_rest_route( self::NS, "/rider/deliveries/{$id}/fail", array( 'methods' => 'POST', 'callback' => array( __CLASS__, 'rider_fail' ), 'permission_callback' => $rider ) );
		register_rest_route( self::NS, "/rider/deliveries/{$id}/reject", array( 'methods' => 'POST', 'callback' => array( __CLASS__, 'rider_reject' ), 'permission_callback' => $rider ) );

		register_rest_route( self::NS, '/track/(?P<token>[A-Za-z0-9]{20,40})', array( 'methods' => 'GET', 'callback' => array( __CLASS__, 'track' ), 'permission_callback' => '__return_true' ) );

		register_rest_route( self::NS, '/dispatch', array( 'methods' => 'GET', 'callback' => array( __CLASS__, 'dispatch' ), 'permission_callback' => $admin ) );
		register_rest_route( self::NS, "/dispatch/{$id}/assign", array( 'methods' => 'POST', 'callback' => array( __CLASS__, 'dispatch_assign' ), 'permission_callback' => $admin ) );
		register_rest_route( self::NS, "/dispatch/{$id}/status", array( 'methods' => 'POST', 'callback' => array( __CLASS__, 'dispatch_status' ), 'permission_callback' => $admin ) );
		register_rest_route( self::NS, '/dispatch/riders/(?P<rider>\d+)/settle', array( 'methods' => 'POST', 'callback' => array( __CLASS__, 'dispatch_settle' ), 'permission_callback' => $admin ) );
	}

	public static function can_ride() {
		return current_user_can( 'ole_deliver' );
	}

	public static function can_dispatch() {
		return current_user_can( 'manage_woocommerce' );
	}

	protected static function no_cache( $data ) {
		$res = rest_ensure_response( $data );
		if ( ! is_wp_error( $res ) ) {
			$res->header( 'Cache-Control', 'no-store, max-age=0' );
			$res->header( 'X-LiteSpeed-Cache-Control', 'no-cache' );
		}
		return $res;
	}

	/** The rider's own delivery, or an error. */
	protected static function own( WP_REST_Request $req ) {
		$d = OLE_Deliveries::get( (int) $req['id'] );
		if ( ! $d || (int) $d->rider_id !== get_current_user_id() ) {
			return new WP_Error( 'ole_not_found', 'Delivery not found.', array( 'status' => 404 ) );
		}
		return $d;
	}

	protected static function result( $d ) {
		if ( is_wp_error( $d ) ) {
			$d->add_data( array( 'status' => 400 ) );
			return $d;
		}
		if ( ! $d ) {
			return new WP_Error( 'ole_failed', 'Could not update this delivery. Refresh and try again.', array( 'status' => 409 ) );
		}
		return self::no_cache( array( 'ok' => true ) );
	}

	/* Rider ------------------------------------------------------------- */

	public static function rider_me() {
		$uid    = get_current_user_id();
		$user   = wp_get_current_user();
		$active = array();
		foreach ( array_reverse( OLE_Deliveries::query( array( 'rider_id' => $uid, 'status' => OLE_Deliveries::ACTIVE ) ) ) as $d ) {
			$row = OLE_Deliveries::for_rider( $d );
			if ( $row ) {
				$active[] = $row;
			}
		}
		$today_start = get_gmt_from_date( wp_date( 'Y-m-d 00:00:00' ) );
		$done        = array();
		foreach ( OLE_Deliveries::query( array( 'rider_id' => $uid, 'status' => array( 'delivered', 'failed' ), 'since' => $today_start, 'limit' => 50 ) ) as $d ) {
			$row = OLE_Deliveries::for_rider( $d );
			if ( $row ) {
				$done[] = $row;
			}
		}
		$today = OLE_Deliveries::rider_stats( $uid, $today_start );
		return self::no_cache( array(
			'name'    => $user->display_name,
			'today'   => $today,
			'online'  => '1' === (string) get_user_meta( $uid, 'ole_online', true ),
			'cash'    => OLE_Deliveries::rider_cash( $uid ),
			'active'  => $active,
			'done'    => $done,
			'store'   => array(
				'name'    => OLE_Settings::get( 'store_name' ),
				'address' => OLE_Settings::get( 'store_address' ),
				'phone'   => OLE_Settings::get( 'store_phone' ),
			),
			'ping_ms' => 20000,
		) );
	}

	public static function rider_duty( WP_REST_Request $req ) {
		OLE_Deliveries::set_duty( get_current_user_id(), rest_sanitize_boolean( $req['online'] ) );
		return self::rider_me();
	}

	public static function rider_location( WP_REST_Request $req ) {
		$lat = $req['lat'];
		$lng = $req['lng'];
		if ( ! OLE_Geo::valid_point( $lat, $lng ) ) {
			return new WP_Error( 'ole_bad_point', 'Invalid location.', array( 'status' => 400 ) );
		}
		OLE_Deliveries::set_location( get_current_user_id(), $lat, $lng );
		return self::no_cache( array( 'ok' => true ) );
	}

	public static function rider_start( WP_REST_Request $req ) {
		$d = self::own( $req );
		return is_wp_error( $d ) ? $d : self::result( OLE_Deliveries::start( $d ) );
	}

	public static function rider_deliver( WP_REST_Request $req ) {
		$d = self::own( $req );
		if ( is_wp_error( $d ) ) {
			return $d;
		}
		$proof_id = 0;
		$files    = $req->get_file_params();
		if ( ! empty( $files['proof']['tmp_name'] ) ) {
			$proof_id = self::save_proof( $files['proof'], $d );
			if ( is_wp_error( $proof_id ) ) {
				$proof_id->add_data( array( 'status' => 400 ) );
				return $proof_id;
			}
		}
		return self::result( OLE_Deliveries::deliver( $d, array(
			'otp'      => $req['otp'],
			'cash'     => $req['cash'],
			'proof_id' => $proof_id,
		) ) );
	}

	public static function rider_fail( WP_REST_Request $req ) {
		$d = self::own( $req );
		if ( is_wp_error( $d ) ) {
			return $d;
		}
		$reason = trim( (string) $req['reason'] );
		if ( '' === $reason ) {
			return new WP_Error( 'ole_reason', 'Please choose a reason.', array( 'status' => 400 ) );
		}
		return self::result( OLE_Deliveries::fail( $d, $reason ) );
	}

	public static function rider_reject( WP_REST_Request $req ) {
		$d = self::own( $req );
		return is_wp_error( $d ) ? $d : self::result( OLE_Deliveries::reject( $d, get_current_user_id() ) );
	}

	protected static function save_proof( $file, $d ) {
		$type = wp_check_filetype_and_ext( $file['tmp_name'], $file['name'] );
		if ( empty( $type['type'] ) || 0 !== strpos( $type['type'], 'image/' ) ) {
			return new WP_Error( 'ole_proof', 'The proof must be a photo.' );
		}
		if ( $file['size'] > 8 * MB_IN_BYTES ) {
			return new WP_Error( 'ole_proof', 'Photo is too large (max 8 MB).' );
		}
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';
		$id = media_handle_sideload( $file, 0, 'Delivery proof — order #' . $d->order_id );
		return $id;
	}

	/* Public tracking ---------------------------------------------------- */

	public static function track( WP_REST_Request $req ) {
		$d = OLE_Deliveries::get_by_token( $req['token'] );
		if ( ! $d ) {
			return new WP_Error( 'ole_not_found', 'Tracking link not found.', array( 'status' => 404 ) );
		}
		$order = wc_get_order( $d->order_id );
		if ( ! $order ) {
			return new WP_Error( 'ole_not_found', 'Order not found.', array( 'status' => 404 ) );
		}
		$store = OLE_Settings::store_point();
		$rider = null;
		$eta   = null;
		if ( $d->rider_id && in_array( $d->status, OLE_Deliveries::ACTIVE, true ) ) {
			$u  = get_userdata( $d->rider_id );
			$pt = OLE_Deliveries::rider_point( $d->rider_id );
			$rider = array(
				'name'  => $u ? $u->display_name : 'Rider',
				'phone' => (string) get_user_meta( $d->rider_id, 'ole_phone', true ),
				'lat'   => $pt ? $pt[0] : null,
				'lng'   => $pt ? $pt[1] : null,
				'seen'  => (int) get_user_meta( $d->rider_id, 'ole_last_seen', true ),
			);
			$dest = OLE_Geo::valid_point( $d->dest_lat, $d->dest_lng ) ? array( (float) $d->dest_lat, (float) $d->dest_lng ) : null;
			if ( 'out_for_delivery' === $d->status && $pt && $dest ) {
				$eta = OLE_Geo::eta_minutes( OLE_Geo::distance_km( $pt[0], $pt[1], $dest[0], $dest[1] ) );
			}
		}
		$stamp = function ( $v ) {
			return $v ? strtotime( $v . ' UTC' ) : 0;
		};
		return self::no_cache( array(
			'order'   => $order->get_order_number(),
			'status'  => $d->status,
			'label'   => OLE_Deliveries::labels()[ $d->status ] ?? $d->status,
			'otp'     => ( OLE_Settings::yes( 'otp_required' ) && in_array( $d->status, OLE_Deliveries::ACTIVE, true ) ) ? $d->otp : '',
			'is_cod'  => (bool) $d->is_cod,
			'amount'  => (float) $d->cod_amount,
			'eta'     => $eta,
			'rider'   => $rider,
			'dest'    => OLE_Geo::valid_point( $d->dest_lat, $d->dest_lng ) ? array( 'lat' => (float) $d->dest_lat, 'lng' => (float) $d->dest_lng ) : null,
			'store'   => array( 'lat' => $store[0], 'lng' => $store[1], 'name' => OLE_Settings::get( 'store_name' ), 'phone' => OLE_Settings::get( 'store_phone' ) ),
			'times'   => array(
				'placed'    => $stamp( $d->created_at ),
				'assigned'  => $stamp( $d->assigned_at ),
				'picked'    => $stamp( $d->picked_at ),
				'delivered' => $stamp( $d->delivered_at ),
			),
		) );
	}

	/* Dispatch board ----------------------------------------------------- */

	public static function dispatch() {
		$since = gmdate( 'Y-m-d H:i:s', time() - DAY_IN_SECONDS );
		$rows  = array();
		$seen  = array();
		$lists = array(
			OLE_Deliveries::query( array( 'status' => array( 'pending', 'assigned', 'out_for_delivery', 'failed' ), 'limit' => 200 ) ),
			OLE_Deliveries::query( array( 'status' => array( 'delivered', 'cancelled' ), 'since' => $since, 'limit' => 100 ) ),
		);
		foreach ( $lists as $list ) {
			foreach ( $list as $d ) {
				if ( isset( $seen[ $d->id ] ) ) {
					continue;
				}
				$seen[ $d->id ] = true;
				$row            = OLE_Deliveries::for_admin( $d );
				if ( $row ) {
					$rows[] = $row;
				}
			}
		}
		$store = OLE_Settings::store_point();
		return self::no_cache( array(
			'deliveries' => $rows,
			'riders'     => array_map( array( 'OLE_Deliveries', 'rider_summary' ), OLE_Deliveries::riders() ),
			'store'      => array( 'lat' => $store[0], 'lng' => $store[1], 'name' => OLE_Settings::get( 'store_name' ) ),
			'auto'       => OLE_Settings::yes( 'auto_assign' ),
		) );
	}

	public static function dispatch_assign( WP_REST_Request $req ) {
		$d = OLE_Deliveries::get( (int) $req['id'] );
		if ( ! $d ) {
			return new WP_Error( 'ole_not_found', 'Delivery not found.', array( 'status' => 404 ) );
		}
		$rider = (int) $req['rider_id'];
		if ( in_array( $d->status, array( 'failed', 'assigned' ), true ) && ! $rider ) {
			$d = OLE_Deliveries::reset( $d );
		}
		if ( $rider ) {
			$res = OLE_Deliveries::assign( $d, $rider, 'admin' );
		} else {
			$res = OLE_Deliveries::auto_assign( $d );
			if ( ! $res ) {
				return new WP_Error( 'ole_no_rider', 'No on-duty rider is free right now. The order stays in the queue and is retried every 2 minutes.', array( 'status' => 409 ) );
			}
		}
		return self::result( $res );
	}

	public static function dispatch_status( WP_REST_Request $req ) {
		$d = OLE_Deliveries::get( (int) $req['id'] );
		if ( ! $d ) {
			return new WP_Error( 'ole_not_found', 'Delivery not found.', array( 'status' => 404 ) );
		}
		switch ( $req['status'] ) {
			case 'delivered':
				return self::result( OLE_Deliveries::deliver( $d, array( 'admin' => true ) ) );
			case 'pending':
				return self::result( OLE_Deliveries::reset( $d ) );
			case 'cancelled':
				return self::result( OLE_Deliveries::update( $d->id, array( 'status' => 'cancelled' ) ) );
		}
		return new WP_Error( 'ole_status', 'Unknown status.', array( 'status' => 400 ) );
	}

	public static function dispatch_settle( WP_REST_Request $req ) {
		$amount = OLE_Deliveries::settle_cash( (int) $req['rider'] );
		return self::no_cache( array( 'ok' => true, 'amount' => $amount ) );
	}
}
