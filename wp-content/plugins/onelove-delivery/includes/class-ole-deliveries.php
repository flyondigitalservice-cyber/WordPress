<?php
/**
 * Delivery records, rider availability, auto-assignment and status flow.
 *
 * Flow: pending → assigned → out_for_delivery → delivered
 *       (failed / cancelled can happen from any active state; reject sends it back to pending)
 *
 * @package OneLoveDelivery
 */

defined( 'ABSPATH' ) || exit;

class OLE_Deliveries {

	const ACTIVE = array( 'assigned', 'out_for_delivery' );

	public static function labels() {
		return array(
			'pending'          => 'Waiting for rider',
			'assigned'         => 'Rider assigned',
			'out_for_delivery' => 'Out for delivery',
			'delivered'        => 'Delivered',
			'failed'           => 'Delivery failed',
			'cancelled'        => 'Cancelled',
		);
	}

	public static function init() {
		add_action( 'woocommerce_order_status_processing', array( __CLASS__, 'on_order_ready' ), 20 );
		foreach ( array( 'cancelled', 'refunded', 'failed' ) as $st ) {
			add_action( 'woocommerce_order_status_' . $st, array( __CLASS__, 'on_order_cancelled' ), 20 );
		}

		add_action( 'ole_delivery_assign_pending', array( __CLASS__, 'assign_pending_all' ) );
		add_action( 'init', array( __CLASS__, 'schedule_sweeper' ) );
	}

	/** Every 2 minutes retry orders that are still waiting for a rider. */
	public static function schedule_sweeper() {
		if ( ! function_exists( 'as_has_scheduled_action' ) || ! OLE_Settings::yes( 'auto_assign' ) ) {
			return;
		}
		if ( ! as_has_scheduled_action( 'ole_delivery_assign_pending' ) ) {
			as_schedule_recurring_action( time() + 60, 2 * MINUTE_IN_SECONDS, 'ole_delivery_assign_pending', array(), 'onelove-delivery' );
		}
	}

	/* ------------------------------------------------------------------ */
	/* Storage                                                             */
	/* ------------------------------------------------------------------ */

	public static function table() {
		global $wpdb;
		return $wpdb->prefix . 'ole_deliveries';
	}

	public static function now() {
		return current_time( 'mysql', true ); // UTC.
	}

	public static function get( $id ) {
		global $wpdb;
		return $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM ' . self::table() . ' WHERE id = %d', $id ) ); // phpcs:ignore WordPress.DB.PreparedSQL
	}

	public static function get_by_order( $order_id ) {
		global $wpdb;
		return $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM ' . self::table() . ' WHERE order_id = %d', $order_id ) ); // phpcs:ignore WordPress.DB.PreparedSQL
	}

	public static function get_by_token( $token ) {
		global $wpdb;
		if ( ! preg_match( '/^[A-Za-z0-9]{20,40}$/', (string) $token ) ) {
			return null;
		}
		return $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM ' . self::table() . ' WHERE token = %s', $token ) ); // phpcs:ignore WordPress.DB.PreparedSQL
	}

	public static function update( $id, array $data ) {
		global $wpdb;
		$data['updated_at'] = self::now();
		$wpdb->update( self::table(), $data, array( 'id' => $id ) );
		return self::get( $id );
	}

	/**
	 * @param array $args status (string|array), rider_id, since (UTC datetime), limit.
	 */
	public static function query( array $args = array() ) {
		global $wpdb;
		$where  = array( '1=1' );
		$params = array();
		if ( ! empty( $args['status'] ) ) {
			$statuses = (array) $args['status'];
			$where[]  = 'status IN (' . implode( ',', array_fill( 0, count( $statuses ), '%s' ) ) . ')';
			$params   = array_merge( $params, $statuses );
		}
		if ( isset( $args['rider_id'] ) ) {
			$where[]  = 'rider_id = %d';
			$params[] = (int) $args['rider_id'];
		}
		if ( ! empty( $args['since'] ) ) {
			$where[]  = 'updated_at >= %s';
			$params[] = $args['since'];
		}
		$limit = isset( $args['limit'] ) ? max( 1, (int) $args['limit'] ) : 200;
		$sql   = 'SELECT * FROM ' . self::table() . ' WHERE ' . implode( ' AND ', $where ) . ' ORDER BY id DESC LIMIT ' . $limit;
		return $params ? $wpdb->get_results( $wpdb->prepare( $sql, $params ) ) : $wpdb->get_results( $sql ); // phpcs:ignore WordPress.DB.PreparedSQL
	}

	public static function create_for_order( $order ) {
		global $wpdb;
		$order = wc_get_order( $order );
		if ( ! $order ) {
			return null;
		}
		$existing = self::get_by_order( $order->get_id() );
		if ( $existing ) {
			return $existing;
		}

		$lat = $order->get_meta( '_ole_lat' );
		$lng = $order->get_meta( '_ole_lng' );
		$ok  = OLE_Geo::valid_point( $lat, $lng );
		$now = self::now();

		$wpdb->insert( self::table(), array(
			'order_id'   => $order->get_id(),
			'status'     => 'pending',
			'otp'        => (string) wp_rand( 1000, 9999 ),
			'token'      => wp_generate_password( 32, false, false ),
			'dest_lat'   => $ok ? (float) $lat : null,
			'dest_lng'   => $ok ? (float) $lng : null,
			'is_cod'     => 'cod' === $order->get_payment_method() ? 1 : 0,
			'cod_amount' => 'cod' === $order->get_payment_method() ? (float) $order->get_total() : 0,
			'created_at' => $now,
			'updated_at' => $now,
		) );

		$delivery = self::get_by_order( $order->get_id() );
		if ( $delivery ) {
			$order->add_order_note( 'Delivery created. Tracking link: ' . self::track_url( $delivery ) );
		}
		return $delivery;
	}

	public static function track_url( $d ) {
		return home_url( '/track/' . $d->token . '/' );
	}

	/* ------------------------------------------------------------------ */
	/* WooCommerce hooks                                                   */
	/* ------------------------------------------------------------------ */

	public static function on_order_ready( $order_id ) {
		$d = self::create_for_order( $order_id );
		if ( $d && 'pending' === $d->status && OLE_Settings::yes( 'auto_assign' ) ) {
			self::auto_assign( $d );
		}
	}

	public static function on_order_cancelled( $order_id ) {
		$d = self::get_by_order( $order_id );
		if ( $d && ! in_array( $d->status, array( 'delivered', 'cancelled' ), true ) ) {
			self::update( $d->id, array( 'status' => 'cancelled' ) );
		}
	}

	/* ------------------------------------------------------------------ */
	/* Riders                                                              */
	/* ------------------------------------------------------------------ */

	public static function is_rider( $user_id ) {
		return user_can( $user_id, 'ole_deliver' );
	}

	public static function rider_online( $user_id ) {
		if ( '1' !== (string) get_user_meta( $user_id, 'ole_online', true ) ) {
			return false;
		}
		$seen = (int) get_user_meta( $user_id, 'ole_last_seen', true );
		return $seen > time() - (int) OLE_Settings::get( 'stale_minutes' ) * MINUTE_IN_SECONDS;
	}

	public static function rider_load( $user_id ) {
		global $wpdb;
		return (int) $wpdb->get_var( $wpdb->prepare( 'SELECT COUNT(*) FROM ' . self::table() . " WHERE rider_id = %d AND status IN ('assigned','out_for_delivery')", $user_id ) ); // phpcs:ignore WordPress.DB.PreparedSQL
	}

	/** Cash collected by a rider and not yet handed over to the store. */
	public static function rider_cash( $user_id ) {
		global $wpdb;
		return (float) $wpdb->get_var( $wpdb->prepare( 'SELECT COALESCE(SUM(cod_collected),0) FROM ' . self::table() . " WHERE rider_id = %d AND status = 'delivered' AND is_cod = 1 AND cod_settled = 0", $user_id ) ); // phpcs:ignore WordPress.DB.PreparedSQL
	}

	public static function riders() {
		return get_users( array(
			'role__in' => array( 'ole_rider' ),
			'orderby'  => 'display_name',
			'number'   => 200,
		) );
	}

	public static function rider_point( $user_id ) {
		$lat = get_user_meta( $user_id, 'ole_lat', true );
		$lng = get_user_meta( $user_id, 'ole_lng', true );
		return OLE_Geo::valid_point( $lat, $lng ) ? array( (float) $lat, (float) $lng ) : null;
	}

	public static function set_location( $user_id, $lat, $lng ) {
		update_user_meta( $user_id, 'ole_lat', (float) $lat );
		update_user_meta( $user_id, 'ole_lng', (float) $lng );
		update_user_meta( $user_id, 'ole_last_seen', time() );
	}

	public static function set_duty( $user_id, $online ) {
		update_user_meta( $user_id, 'ole_online', $online ? '1' : '0' );
		update_user_meta( $user_id, 'ole_last_seen', time() );
		if ( $online && OLE_Settings::yes( 'auto_assign' ) ) {
			self::assign_pending_all();
		}
	}

	/**
	 * Pick the best on-duty rider: below the active-order cap, closest to the store,
	 * lightly penalised for every order already in hand.
	 */
	public static function pick_rider( $exclude = array() ) {
		$store = OLE_Settings::store_point();
		$max   = (int) OLE_Settings::get( 'max_active' );
		$best  = 0;
		$score = PHP_FLOAT_MAX;

		foreach ( self::riders() as $rider ) {
			if ( in_array( (int) $rider->ID, $exclude, true ) || ! self::rider_online( $rider->ID ) ) {
				continue;
			}
			$load = self::rider_load( $rider->ID );
			if ( $load >= $max ) {
				continue;
			}
			$pt   = self::rider_point( $rider->ID );
			$km   = $pt ? OLE_Geo::distance_km( $pt[0], $pt[1], $store[0], $store[1] ) : 50;
			$cost = $km + $load * 3;
			if ( $cost < $score ) {
				$score = $cost;
				$best  = (int) $rider->ID;
			}
		}
		return $best;
	}

	/* ------------------------------------------------------------------ */
	/* Status flow                                                         */
	/* ------------------------------------------------------------------ */

	public static function auto_assign( $d ) {
		$exclude  = array_map( 'intval', array_filter( explode( ',', (string) $d->rejected_by ) ) );
		$rider_id = self::pick_rider( $exclude );
		if ( $rider_id ) {
			return self::assign( $d, $rider_id, 'auto' );
		}
		self::alert_no_rider( $d );
		return false;
	}

	public static function assign_pending_all() {
		if ( ! OLE_Settings::yes( 'auto_assign' ) ) {
			return;
		}
		foreach ( array_reverse( self::query( array( 'status' => 'pending', 'limit' => 50 ) ) ) as $d ) {
			if ( ! self::auto_assign( $d ) ) {
				break; // Nobody free — no point trying the rest.
			}
		}
	}

	public static function assign( $d, $rider_id, $by = 'auto' ) {
		global $wpdb;
		$rider = get_userdata( $rider_id );
		if ( ! $rider || ! self::is_rider( $rider_id ) || ! in_array( $d->status, array( 'pending', 'assigned', 'failed' ), true ) ) {
			return false;
		}

		// Atomic: only one request can move a delivery out of its current state.
		$done = $wpdb->query( $wpdb->prepare(
			'UPDATE ' . self::table() . " SET rider_id = %d, status = 'assigned', fail_reason = '', assigned_at = %s, updated_at = %s WHERE id = %d AND status = %s AND rider_id = %d", // phpcs:ignore WordPress.DB.PreparedSQL
			$rider_id, self::now(), self::now(), $d->id, $d->status, $d->rider_id
		) );
		if ( ! $done ) {
			return false;
		}

		$d = self::get( $d->id );
		self::note( $d, sprintf( 'Delivery assigned to %s (%s).', $rider->display_name, 'auto' === $by ? 'auto' : 'by admin' ) );
		do_action( 'ole_delivery_assigned', $d, $rider_id );
		return $d;
	}

	public static function start( $d ) {
		if ( 'assigned' !== $d->status ) {
			return new WP_Error( 'ole_state', 'This order is not waiting for pickup.' );
		}
		$d = self::update( $d->id, array( 'status' => 'out_for_delivery', 'picked_at' => self::now() ) );
		self::note( $d, 'Rider picked up the order and is on the way.' );
		do_action( 'ole_delivery_out', $d );
		return $d;
	}

	/**
	 * @param array $args otp, cash, proof_id, admin (bool, skips OTP).
	 */
	public static function deliver( $d, array $args = array() ) {
		if ( ! in_array( $d->status, self::ACTIVE, true ) ) {
			return new WP_Error( 'ole_state', 'This order is not out for delivery.' );
		}
		$admin = ! empty( $args['admin'] );
		if ( ! $admin && OLE_Settings::yes( 'otp_required' ) && trim( (string) ( $args['otp'] ?? '' ) ) !== $d->otp ) {
			return new WP_Error( 'ole_otp', 'Wrong OTP. Ask the customer for the 4-digit delivery code.' );
		}

		$data = array(
			'status'       => 'delivered',
			'delivered_at' => self::now(),
		);
		if ( $d->is_cod ) {
			$cash = isset( $args['cash'] ) && is_numeric( $args['cash'] ) ? (float) $args['cash'] : (float) $d->cod_amount;
			if ( ! $admin && $cash + 0.01 < (float) $d->cod_amount ) {
				return new WP_Error( 'ole_cash', sprintf( 'Collect the full amount: ₹%s.', number_format( (float) $d->cod_amount, 2 ) ) );
			}
			$data['cod_collected'] = $cash;
		}
		if ( ! empty( $args['proof_id'] ) ) {
			$data['proof_id'] = (int) $args['proof_id'];
		}
		$d = self::update( $d->id, $data );

		$order = wc_get_order( $d->order_id );
		if ( $order ) {
			$msg = 'Delivered' . ( $admin ? ' (marked by admin)' : '' ) . '.';
			if ( $d->is_cod ) {
				$msg .= sprintf( ' Cash collected: ₹%s.', number_format( (float) $d->cod_collected, 2 ) );
			}
			if ( $d->proof_id ) {
				$msg .= ' Proof photo: ' . wp_get_attachment_url( $d->proof_id );
			}
			$order->add_order_note( $msg );
			if ( ! $order->has_status( 'completed' ) ) {
				$order->update_status( 'completed' );
			}
		}
		do_action( 'ole_delivery_delivered', $d );
		return $d;
	}

	public static function fail( $d, $reason ) {
		if ( ! in_array( $d->status, self::ACTIVE, true ) ) {
			return new WP_Error( 'ole_state', 'This order is not active.' );
		}
		$reason = sanitize_text_field( $reason );
		$d      = self::update( $d->id, array( 'status' => 'failed', 'fail_reason' => $reason ) );
		self::note( $d, 'Delivery failed: ' . $reason . '. Re-assign it from Deliveries → Dispatch.' );
		do_action( 'ole_delivery_failed', $d );
		return $d;
	}

	/** Rider declines an assignment: back to the pool, never offered to them again. */
	public static function reject( $d, $rider_id ) {
		if ( 'assigned' !== $d->status || (int) $d->rider_id !== (int) $rider_id ) {
			return new WP_Error( 'ole_state', 'Only newly assigned orders can be declined.' );
		}
		$rejected   = array_filter( explode( ',', (string) $d->rejected_by ) );
		$rejected[] = (int) $rider_id;
		$d          = self::update( $d->id, array(
			'status'      => 'pending',
			'rider_id'    => 0,
			'rejected_by' => implode( ',', array_unique( $rejected ) ),
		) );
		$rider = get_userdata( $rider_id );
		self::note( $d, sprintf( '%s declined the delivery.', $rider ? $rider->display_name : 'Rider' ) );
		if ( OLE_Settings::yes( 'auto_assign' ) ) {
			self::auto_assign( $d );
		}
		return self::get( $d->id );
	}

	/** Admin: send back to the queue (clears declines) so it can be re-assigned. */
	public static function reset( $d ) {
		$d = self::update( $d->id, array(
			'status'      => 'pending',
			'rider_id'    => 0,
			'rejected_by' => '',
			'fail_reason' => '',
		) );
		self::note( $d, 'Delivery moved back to the queue by admin.' );
		return $d;
	}

	public static function settle_cash( $rider_id ) {
		global $wpdb;
		$amount = self::rider_cash( $rider_id );
		$wpdb->query( $wpdb->prepare( 'UPDATE ' . self::table() . " SET cod_settled = 1 WHERE rider_id = %d AND status = 'delivered' AND is_cod = 1 AND cod_settled = 0", $rider_id ) ); // phpcs:ignore WordPress.DB.PreparedSQL
		return $amount;
	}

	protected static function note( $d, $text ) {
		$order = wc_get_order( $d->order_id );
		if ( $order ) {
			$order->add_order_note( $text );
		}
	}

	protected static function alert_no_rider( $d ) {
		$key = 'ole_no_rider_' . $d->id;
		if ( get_transient( $key ) ) {
			return;
		}
		set_transient( $key, 1, 30 * MINUTE_IN_SECONDS );
		do_action( 'ole_delivery_no_rider', $d );
	}

	/* ------------------------------------------------------------------ */
	/* Presenters                                                          */
	/* ------------------------------------------------------------------ */

	public static function order_info( $order ) {
		$has_ship = $order->has_shipping_address();
		$address  = $has_ship ? $order->get_formatted_shipping_address() : $order->get_formatted_billing_address();
		$address  = trim( wp_strip_all_tags( preg_replace( '#<br\s*/?>#i', ', ', (string) $address ) ) );
		$phone    = $order->get_shipping_phone() ? $order->get_shipping_phone() : $order->get_billing_phone();
		$name     = trim( $has_ship ? $order->get_formatted_shipping_full_name() : $order->get_formatted_billing_full_name() );
		$name     = '' !== $name ? $name : 'Customer';

		$items = array();
		foreach ( $order->get_items() as $item ) {
			$items[] = $item->get_quantity() . ' × ' . $item->get_name();
		}
		return array(
			'number'   => $order->get_order_number(),
			'name'     => $name,
			'first'    => $order->get_billing_first_name(),
			'phone'    => $phone,
			'address'  => $address,
			'items'    => $items,
			'total'    => (float) $order->get_total(),
			'payment'  => $order->get_payment_method_title(),
			'note'     => $order->get_customer_note(),
			'created'  => $order->get_date_created() ? $order->get_date_created()->getTimestamp() : 0,
		);
	}

	public static function for_rider( $d ) {
		$order = wc_get_order( $d->order_id );
		if ( ! $order ) {
			return null;
		}
		$info  = self::order_info( $order );
		$store = OLE_Settings::store_point();
		return array(
			'id'          => (int) $d->id,
			'order'       => $info['number'],
			'status'      => $d->status,
			'label'       => self::labels()[ $d->status ] ?? $d->status,
			'customer'    => $info['name'],
			'phone'       => $info['phone'],
			'wa'          => OLE_Geo::wa_phone( $info['phone'] ),
			'address'     => $info['address'],
			'items'       => $info['items'],
			'note'        => $info['note'],
			'total'       => $info['total'],
			'is_cod'      => (bool) $d->is_cod,
			'cod_amount'  => (float) $d->cod_amount,
			'cod_collected' => null === $d->cod_collected ? null : (float) $d->cod_collected,
			'otp_needed'  => OLE_Settings::yes( 'otp_required' ),
			'nav_url'     => OLE_Geo::nav_url( $d->dest_lat, $d->dest_lng, $info['address'] ),
			'store_nav'   => OLE_Geo::nav_url( $store[0], $store[1], OLE_Settings::get( 'store_address' ) ),
			'pinned'      => OLE_Geo::valid_point( $d->dest_lat, $d->dest_lng ),
			'track_url'   => self::track_url( $d ),
			'assigned_at' => $d->assigned_at ? strtotime( $d->assigned_at . ' UTC' ) : 0,
			'delivered_at'=> $d->delivered_at ? strtotime( $d->delivered_at . ' UTC' ) : 0,
		);
	}

	public static function for_admin( $d ) {
		$order = wc_get_order( $d->order_id );
		if ( ! $order ) {
			return null;
		}
		$info  = self::order_info( $order );
		$rider = $d->rider_id ? get_userdata( $d->rider_id ) : null;
		return array(
			'id'         => (int) $d->id,
			'order_id'   => (int) $d->order_id,
			'order'      => $info['number'],
			'edit_url'   => $order->get_edit_order_url(),
			'status'     => $d->status,
			'label'      => self::labels()[ $d->status ] ?? $d->status,
			'customer'   => $info['name'],
			'phone'      => $info['phone'],
			'address'    => $info['address'],
			'items'      => $info['items'],
			'total'      => $info['total'],
			'is_cod'     => (bool) $d->is_cod,
			'rider_id'   => (int) $d->rider_id,
			'rider'      => $rider ? $rider->display_name : '',
			'lat'        => null === $d->dest_lat ? null : (float) $d->dest_lat,
			'lng'        => null === $d->dest_lng ? null : (float) $d->dest_lng,
			'fail'       => $d->fail_reason,
			'track_url'  => self::track_url( $d ),
			'created'    => strtotime( $d->created_at . ' UTC' ),
			'updated'    => strtotime( $d->updated_at . ' UTC' ),
		);
	}

	public static function rider_summary( $user ) {
		$pt = self::rider_point( $user->ID );
		return array(
			'id'        => (int) $user->ID,
			'name'      => $user->display_name,
			'phone'     => (string) get_user_meta( $user->ID, 'ole_phone', true ),
			'online'    => self::rider_online( $user->ID ),
			'last_seen' => (int) get_user_meta( $user->ID, 'ole_last_seen', true ),
			'lat'       => $pt ? $pt[0] : null,
			'lng'       => $pt ? $pt[1] : null,
			'active'    => self::rider_load( $user->ID ),
			'cash'      => self::rider_cash( $user->ID ),
		);
	}
}
