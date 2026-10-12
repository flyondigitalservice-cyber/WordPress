<?php
/**
 * Google Maps–based delivery: quotes, orders, live rider tracking.
 *
 * Delivery promises
 *  - express  : ready-to-wear shirts, within the express radius, in 2 hours.
 *  - tailored : anything made to measure, within the tailoring radius, in 24 hours.
 *  - standard : everywhere else, 3–5 days.
 *
 * Routes (namespace hf/v1)
 *  POST /quote                 → available delivery promises for a location + bag
 *  POST /orders                → place an order, returns its tracking key
 *  GET  /orders/{key}          → live status, rider position, ETA
 *  POST /orders/{key}/rider    → rider app pushes its GPS position (token protected)
 *
 * @package HadhramiFashion
 */

defined( 'ABSPATH' ) || exit;

/**
 * Order statuses in the order they happen.
 *
 * @return array
 */
function hf_order_statuses() {
	return array(
		'received'   => __( 'Order received', 'hadhramifashion' ),
		'atelier'    => __( 'In the atelier', 'hadhramifashion' ),
		'dispatched' => __( 'With your rider', 'hadhramifashion' ),
		'delivered'  => __( 'Delivered', 'hadhramifashion' ),
		'cancelled'  => __( 'Cancelled', 'hadhramifashion' ),
	);
}

/**
 * Delivery promise labels.
 *
 * @return array
 */
function hf_delivery_tiers() {
	return array(
		'express'  => array(
			'label' => __( 'Within 2 hours', 'hadhramifashion' ),
			'note'  => __( 'Pressed, folded in tissue and hand-delivered by our rider.', 'hadhramifashion' ),
		),
		'tailored' => array(
			'label' => __( 'Tailored within 24 hours', 'hadhramifashion' ),
			'note'  => __( 'Cut to your measure in our atelier and delivered tomorrow.', 'hadhramifashion' ),
		),
		'standard' => array(
			'label' => __( 'Standard, 3–5 days', 'hadhramifashion' ),
			'note'  => __( 'For addresses beyond our rider routes.', 'hadhramifashion' ),
		),
	);
}

add_action(
	'init',
	function () {
		register_post_type(
			'hf_order',
			array(
				'labels'          => array(
					'name'          => __( 'Orders', 'hadhramifashion' ),
					'singular_name' => __( 'Order', 'hadhramifashion' ),
					'edit_item'     => __( 'Order', 'hadhramifashion' ),
				),
				'public'          => false,
				'show_ui'         => true,
				'show_in_menu'    => 'hf-settings',
				'supports'        => array( 'title' ),
				'capability_type' => 'post',
				'capabilities'    => array( 'create_posts' => 'do_not_allow' ),
				'map_meta_cap'    => true,
			)
		);
	}
);

/**
 * Great-circle distance in km.
 *
 * @param float $lat1 Latitude A.
 * @param float $lng1 Longitude A.
 * @param float $lat2 Latitude B.
 * @param float $lng2 Longitude B.
 * @return float
 */
function hf_haversine_km( $lat1, $lng1, $lat2, $lng2 ) {
	$r    = 6371;
	$dlat = deg2rad( $lat2 - $lat1 );
	$dlng = deg2rad( $lng2 - $lng1 );
	$a    = sin( $dlat / 2 ) ** 2 + cos( deg2rad( $lat1 ) ) * cos( deg2rad( $lat2 ) ) * sin( $dlng / 2 ) ** 2;
	return $r * 2 * atan2( sqrt( $a ), sqrt( 1 - $a ) );
}

/**
 * Road distance to use for a quote.
 *
 * The browser measures the driving route with Google; we trust it only within
 * sane bounds of the straight-line distance so it cannot be spoofed to zero.
 *
 * @param float      $lat     Destination latitude.
 * @param float      $lng     Destination longitude.
 * @param float|null $road_km Driving distance reported by the browser.
 * @return float
 */
function hf_road_km( $lat, $lng, $road_km = null ) {
	$straight = hf_haversine_km( (float) hf_opt( 'store_lat' ), (float) hf_opt( 'store_lng' ), $lat, $lng );
	if ( null === $road_km || $road_km <= 0 ) {
		return round( $straight * 1.3, 1 );
	}
	return round( min( max( $road_km, $straight ), $straight * 2.5 + 1 ), 1 );
}

/**
 * Normalise bag lines against the catalogue (never trust client prices).
 *
 * @param array $lines Raw lines from the browser.
 * @return array
 */
function hf_normalise_lines( $lines ) {
	$clean = array();
	if ( ! is_array( $lines ) ) {
		return $clean;
	}
	foreach ( array_slice( $lines, 0, 30 ) as $line ) {
		$id   = isset( $line['id'] ) ? absint( $line['id'] ) : 0;
		$post = $id ? get_post( $id ) : null;
		if ( ! $post || 'hf_product' !== $post->post_type || 'publish' !== $post->post_status ) {
			continue;
		}
		$kind = hf_product_kind( $id );
		$fit  = ( 'suits' === $kind || ( isset( $line['fit'] ) && 'measure' === $line['fit'] ) ) ? 'measure' : 'ready';

		$measure = array();
		if ( 'measure' === $fit && ! empty( $line['measurements'] ) && is_array( $line['measurements'] ) ) {
			foreach ( array_slice( $line['measurements'], 0, 20, true ) as $k => $v ) {
				$measure[ sanitize_key( $k ) ] = sanitize_text_field( (string) $v );
			}
		}

		$clean[] = array(
			'id'           => $id,
			'title'        => get_the_title( $id ),
			'kind'         => $kind,
			'qty'          => max( 1, min( 10, isset( $line['qty'] ) ? absint( $line['qty'] ) : 1 ) ),
			'size'         => isset( $line['size'] ) ? sanitize_text_field( $line['size'] ) : '',
			'fit'          => $fit,
			'measurements' => $measure,
			'price'        => (float) get_post_meta( $id, '_hf_price', true ),
			'express'      => '1' === get_post_meta( $id, '_hf_express', true ),
		);
	}
	return $clean;
}

/**
 * Next timestamp at which the atelier can start a 2-hour run.
 *
 * @return DateTimeImmutable
 */
function hf_express_start() {
	$now    = current_datetime();
	$hour   = (int) $now->format( 'G' );
	$open   = (int) hf_opt( 'open_hour' );
	$cutoff = (int) hf_opt( 'express_cutoff_hour' );

	if ( $hour >= $open && $hour < $cutoff ) {
		return $now;
	}
	$day = $hour >= $cutoff ? $now->modify( '+1 day' ) : $now;
	return $day->setTime( $open, 0 );
}

/**
 * Work out which delivery promises apply.
 *
 * @param float      $lat     Destination latitude.
 * @param float      $lng     Destination longitude.
 * @param array      $lines   Normalised bag lines.
 * @param float|null $road_km Browser-measured driving distance.
 * @return array
 */
function hf_delivery_quote( $lat, $lng, $lines, $road_km = null ) {
	$km        = hf_road_km( $lat, $lng, $road_km );
	$tiers     = hf_delivery_tiers();
	$options   = array();
	$all_ready = ! empty( $lines );
	foreach ( $lines as $line ) {
		if ( 'ready' !== $line['fit'] || ! $line['express'] ) {
			$all_ready = false;
		}
	}

	$express_km  = (float) hf_opt( 'express_radius_km' );
	$tailored_km = (float) hf_opt( 'tailored_radius_km' );

	if ( $km <= $express_km ) {
		$start = hf_express_start();
		$eta   = $start->modify( '+2 hours' );
		$fee   = (float) hf_opt( 'express_fee' ) + max( 0, $km - (float) hf_opt( 'free_km' ) ) * (float) hf_opt( 'per_km_fee' );

		$options['express'] = array(
			'available' => $all_ready,
			'reason'    => $all_ready ? '' : __( 'Available when every piece in your bag is a ready-to-wear express item.', 'hadhramifashion' ),
			'fee'       => round( $fee ),
			'eta'       => $eta->getTimestamp(),
			'etaText'   => hf_eta_text( $eta ),
		);
	}

	if ( $km <= $tailored_km ) {
		$eta                 = current_datetime()->modify( '+24 hours' );
		$options['tailored'] = array(
			'available' => true,
			'reason'    => '',
			'fee'       => round( (float) hf_opt( 'tailored_fee' ) ),
			'eta'       => $eta->getTimestamp(),
			'etaText'   => hf_eta_text( $eta ),
		);
	}

	$eta                 = current_datetime()->modify( '+5 days' );
	$options['standard'] = array(
		'available' => true,
		'reason'    => '',
		'fee'       => round( (float) hf_opt( 'standard_fee' ) ),
		'eta'       => $eta->getTimestamp(),
		'etaText'   => hf_eta_text( $eta ),
	);

	foreach ( $options as $key => &$option ) {
		$option['label'] = $tiers[ $key ]['label'];
		$option['note']  = $tiers[ $key ]['note'];
	}
	unset( $option );

	return array(
		'distanceKm' => $km,
		'inExpress'  => $km <= $express_km,
		'inTailored' => $km <= $tailored_km,
		'options'    => $options,
	);
}

/**
 * Human ETA, e.g. “today by 4:30 pm”.
 *
 * @param DateTimeImmutable $eta ETA.
 * @return string
 */
function hf_eta_text( $eta ) {
	$today    = current_datetime()->format( 'Y-m-d' );
	$tomorrow = current_datetime()->modify( '+1 day' )->format( 'Y-m-d' );
	$day      = $eta->format( 'Y-m-d' );
	$time     = wp_date( get_option( 'time_format' ), $eta->getTimestamp() );

	if ( $day === $today ) {
		/* translators: %s: time */
		return sprintf( __( 'today by %s', 'hadhramifashion' ), $time );
	}
	if ( $day === $tomorrow ) {
		/* translators: %s: time */
		return sprintf( __( 'tomorrow by %s', 'hadhramifashion' ), $time );
	}
	return wp_date( 'l, j F', $eta->getTimestamp() );
}

/*
 * REST API.
 */
add_action(
	'rest_api_init',
	function () {
		register_rest_route(
			'hf/v1',
			'/quote',
			array(
				'methods'             => 'POST',
				'permission_callback' => '__return_true',
				'callback'            => 'hf_rest_quote',
			)
		);
		register_rest_route(
			'hf/v1',
			'/orders',
			array(
				'methods'             => 'POST',
				'permission_callback' => '__return_true',
				'callback'            => 'hf_rest_create_order',
			)
		);
		register_rest_route(
			'hf/v1',
			'/orders/(?P<key>[A-Za-z0-9]{12,40})',
			array(
				'methods'             => 'GET',
				'permission_callback' => '__return_true',
				'callback'            => 'hf_rest_get_order',
			)
		);
		register_rest_route(
			'hf/v1',
			'/orders/(?P<key>[A-Za-z0-9]{12,40})/rider',
			array(
				'methods'             => 'POST',
				'permission_callback' => '__return_true',
				'callback'            => 'hf_rest_rider_ping',
			)
		);
	}
);

/**
 * Validate a coordinate pair from a request.
 *
 * @param WP_REST_Request $request Request.
 * @return array|WP_Error array( lat, lng ).
 */
function hf_request_coords( $request ) {
	$lat = $request->get_param( 'lat' );
	$lng = $request->get_param( 'lng' );
	if ( ! is_numeric( $lat ) || ! is_numeric( $lng ) || abs( $lat ) > 90 || abs( $lng ) > 180 ) {
		return new WP_Error( 'hf_bad_location', __( 'Please choose your address on the map.', 'hadhramifashion' ), array( 'status' => 400 ) );
	}
	return array( (float) $lat, (float) $lng );
}

/**
 * POST /quote.
 *
 * @param WP_REST_Request $request Request.
 * @return WP_REST_Response|WP_Error
 */
function hf_rest_quote( $request ) {
	$coords = hf_request_coords( $request );
	if ( is_wp_error( $coords ) ) {
		return $coords;
	}
	$road  = $request->get_param( 'roadKm' );
	$lines = hf_normalise_lines( $request->get_param( 'items' ) );
	return rest_ensure_response( hf_delivery_quote( $coords[0], $coords[1], $lines, is_numeric( $road ) ? (float) $road : null ) );
}

/**
 * POST /orders.
 *
 * @param WP_REST_Request $request Request.
 * @return WP_REST_Response|WP_Error
 */
function hf_rest_create_order( $request ) {
	// Light abuse protection: 8 orders per IP per hour.
	$ip    = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
	$bucket = 'hf_rl_' . md5( $ip );
	$count  = (int) get_transient( $bucket );
	if ( $count >= 8 ) {
		return new WP_Error( 'hf_rate_limited', __( 'Too many orders from this connection. Please call the atelier.', 'hadhramifashion' ), array( 'status' => 429 ) );
	}

	$coords = hf_request_coords( $request );
	if ( is_wp_error( $coords ) ) {
		return $coords;
	}

	$lines = hf_normalise_lines( $request->get_param( 'items' ) );
	if ( ! $lines ) {
		return new WP_Error( 'hf_empty', __( 'Your bag is empty.', 'hadhramifashion' ), array( 'status' => 400 ) );
	}

	$customer = (array) $request->get_param( 'customer' );
	$name     = isset( $customer['name'] ) ? sanitize_text_field( $customer['name'] ) : '';
	$phone    = isset( $customer['phone'] ) ? sanitize_text_field( $customer['phone'] ) : '';
	$email    = isset( $customer['email'] ) ? sanitize_email( $customer['email'] ) : '';
	$address  = sanitize_textarea_field( (string) $request->get_param( 'address' ) );
	$details  = sanitize_text_field( (string) $request->get_param( 'addressDetails' ) );
	$notes    = sanitize_textarea_field( (string) $request->get_param( 'notes' ) );
	$measure  = 'visit' === $request->get_param( 'measureMode' ) ? 'visit' : 'provided';

	if ( ! $name || strlen( preg_replace( '/\D/', '', $phone ) ) < 7 ) {
		return new WP_Error( 'hf_contact', __( 'Please share your name and a phone number our rider can call.', 'hadhramifashion' ), array( 'status' => 400 ) );
	}

	$road  = $request->get_param( 'roadKm' );
	$quote = hf_delivery_quote( $coords[0], $coords[1], $lines, is_numeric( $road ) ? (float) $road : null );
	$tier  = sanitize_key( (string) $request->get_param( 'tier' ) );
	if ( empty( $quote['options'][ $tier ]['available'] ) ) {
		return new WP_Error( 'hf_tier', __( 'That delivery promise is not available for this address. Please choose another.', 'hadhramifashion' ), array( 'status' => 400 ) );
	}
	$option = $quote['options'][ $tier ];

	$subtotal = 0;
	foreach ( $lines as $line ) {
		$subtotal += $line['price'] * $line['qty'];
	}
	$total = $subtotal + $option['fee'];

	$key   = wp_generate_password( 20, false, false );
	$token = wp_generate_password( 24, false, false );

	$order_id = wp_insert_post(
		array(
			'post_type'   => 'hf_order',
			'post_status' => 'publish',
			/* translators: 1: customer name, 2: delivery promise */
			'post_title'  => sprintf( __( '%1$s — %2$s', 'hadhramifashion' ), $name, $option['label'] ),
		),
		true
	);
	if ( is_wp_error( $order_id ) ) {
		return $order_id;
	}

	$meta = array(
		'_hf_key'          => $key,
		'_hf_rider_token'  => $token,
		'_hf_customer'     => array( 'name' => $name, 'phone' => $phone, 'email' => $email ),
		'_hf_address'      => $address,
		'_hf_address_more' => $details,
		'_hf_lat'          => $coords[0],
		'_hf_lng'          => $coords[1],
		'_hf_distance_km'  => $quote['distanceKm'],
		'_hf_items'        => $lines,
		'_hf_tier'         => $tier,
		'_hf_fee'          => $option['fee'],
		'_hf_subtotal'     => $subtotal,
		'_hf_total'        => $total,
		'_hf_eta'          => $option['eta'],
		'_hf_status'       => 'received',
		'_hf_status_log'   => array( array( 'received', time() ) ),
		'_hf_notes'        => $notes,
		'_hf_measure_mode' => $measure,
		'_hf_payment'      => __( 'Pay on delivery', 'hadhramifashion' ),
	);
	foreach ( $meta as $k => $v ) {
		update_post_meta( $order_id, $k, $v );
	}

	set_transient( $bucket, $count + 1, HOUR_IN_SECONDS );

	$track = add_query_arg( 'order', $key, hf_page_url( 'page-templates/track.php' ) );
	hf_send_order_emails( $order_id, $track );

	return rest_ensure_response(
		array(
			'key'      => $key,
			'number'   => hf_order_number( $order_id ),
			'trackUrl' => $track,
			'etaText'  => $option['etaText'],
		)
	);
}

/**
 * Display number for an order.
 *
 * @param int $order_id Order post ID.
 * @return string
 */
function hf_order_number( $order_id ) {
	return 'HF-' . str_pad( (string) $order_id, 5, '0', STR_PAD_LEFT );
}

/**
 * Find an order by its public key.
 *
 * @param string $key Tracking key.
 * @return int
 */
function hf_order_by_key( $key ) {
	$ids = get_posts(
		array(
			'post_type'      => 'hf_order',
			'post_status'    => 'any',
			'meta_key'       => '_hf_key',
			'meta_value'     => sanitize_text_field( $key ),
			'posts_per_page' => 1,
			'fields'         => 'ids',
		)
	);
	return $ids ? (int) $ids[0] : 0;
}

/**
 * Public tracking payload.
 *
 * @param int $order_id Order ID.
 * @return array
 */
function hf_order_public( $order_id ) {
	$status   = get_post_meta( $order_id, '_hf_status', true );
	$tier     = get_post_meta( $order_id, '_hf_tier', true );
	$tiers    = hf_delivery_tiers();
	$statuses = hf_order_statuses();
	$log      = (array) get_post_meta( $order_id, '_hf_status_log', true );
	$rider    = get_post_meta( $order_id, '_hf_rider', true );
	$items    = (array) get_post_meta( $order_id, '_hf_items', true );
	$eta      = (int) get_post_meta( $order_id, '_hf_eta', true );

	// A step counts as done once the order has reached it or anything after it.
	$steps    = array( 'received', 'atelier', 'dispatched', 'delivered' );
	$reached  = array_search( $status, $steps, true );
	$timeline = array();
	foreach ( $steps as $index => $step ) {
		$at = null;
		foreach ( $log as $entry ) {
			if ( is_array( $entry ) && $entry[0] === $step ) {
				$at = (int) $entry[1];
			}
		}
		$timeline[] = array(
			'key'   => $step,
			'label' => $statuses[ $step ],
			'done'  => null !== $at || ( false !== $reached && $index <= $reached ),
			'time'  => $at ? wp_date( get_option( 'time_format' ), $at ) : '',
		);
	}

	$rider_out = null;
	if ( is_array( $rider ) && in_array( $status, array( 'dispatched', 'atelier' ), true ) ) {
		$rider_out = array(
			'lat'     => (float) $rider['lat'],
			'lng'     => (float) $rider['lng'],
			'heading' => isset( $rider['heading'] ) ? (float) $rider['heading'] : null,
			'ago'     => human_time_diff( (int) $rider['ts'] ),
		);
	}

	return array(
		'number'      => hf_order_number( $order_id ),
		'status'      => $status,
		'statusLabel' => isset( $statuses[ $status ] ) ? $statuses[ $status ] : $status,
		'tier'        => $tier,
		'tierLabel'   => isset( $tiers[ $tier ] ) ? $tiers[ $tier ]['label'] : '',
		'etaText'     => $eta ? hf_eta_text( ( new DateTimeImmutable( '@' . $eta ) )->setTimezone( wp_timezone() ) ) : '',
		'eta'         => $eta,
		'timeline'    => $timeline,
		'destination' => array(
			'lat'     => (float) get_post_meta( $order_id, '_hf_lat', true ),
			'lng'     => (float) get_post_meta( $order_id, '_hf_lng', true ),
			'address' => get_post_meta( $order_id, '_hf_address', true ),
		),
		'rider'       => $rider_out,
		'riderName'   => get_post_meta( $order_id, '_hf_rider_name', true ),
		'riderPhone'  => in_array( $status, array( 'dispatched' ), true ) ? get_post_meta( $order_id, '_hf_rider_phone', true ) : '',
		'items'       => array_map(
			function ( $line ) {
				return array(
					'title' => $line['title'],
					'qty'   => $line['qty'],
					'fit'   => $line['fit'],
				);
			},
			$items
		),
		'total'       => hf_price( get_post_meta( $order_id, '_hf_total', true ) ),
	);
}

/**
 * GET /orders/{key}.
 *
 * @param WP_REST_Request $request Request.
 * @return WP_REST_Response|WP_Error
 */
function hf_rest_get_order( $request ) {
	$order_id = hf_order_by_key( $request['key'] );
	if ( ! $order_id ) {
		return new WP_Error( 'hf_not_found', __( 'We could not find that order.', 'hadhramifashion' ), array( 'status' => 404 ) );
	}
	return rest_ensure_response( hf_order_public( $order_id ) );
}

/**
 * POST /orders/{key}/rider — rider GPS ping, and status changes from the rider.
 *
 * @param WP_REST_Request $request Request.
 * @return WP_REST_Response|WP_Error
 */
function hf_rest_rider_ping( $request ) {
	$order_id = hf_order_by_key( $request['key'] );
	$token    = (string) $request->get_param( 'token' );
	if ( ! $order_id || ! hash_equals( (string) get_post_meta( $order_id, '_hf_rider_token', true ), $token ) ) {
		return new WP_Error( 'hf_forbidden', __( 'This rider link is not valid.', 'hadhramifashion' ), array( 'status' => 403 ) );
	}

	$action = sanitize_key( (string) $request->get_param( 'action' ) );
	if ( 'delivered' === $action ) {
		hf_set_order_status( $order_id, 'delivered' );
		return rest_ensure_response( array( 'ok' => true, 'status' => 'delivered' ) );
	}

	$coords = hf_request_coords( $request );
	if ( is_wp_error( $coords ) ) {
		return $coords;
	}
	$heading = $request->get_param( 'heading' );
	update_post_meta(
		$order_id,
		'_hf_rider',
		array(
			'lat'     => $coords[0],
			'lng'     => $coords[1],
			'heading' => is_numeric( $heading ) ? (float) $heading : null,
			'ts'      => time(),
		)
	);

	$status = get_post_meta( $order_id, '_hf_status', true );
	if ( in_array( $status, array( 'received', 'atelier' ), true ) ) {
		hf_set_order_status( $order_id, 'dispatched' );
		$status = 'dispatched';
	}

	return rest_ensure_response( array( 'ok' => true, 'status' => $status ) );
}

/**
 * Move an order to a new status and log the moment.
 *
 * @param int    $order_id Order ID.
 * @param string $status   New status.
 */
function hf_set_order_status( $order_id, $status ) {
	if ( ! array_key_exists( $status, hf_order_statuses() ) ) {
		return;
	}
	$current = get_post_meta( $order_id, '_hf_status', true );
	if ( $current === $status ) {
		return;
	}
	update_post_meta( $order_id, '_hf_status', $status );
	$log   = (array) get_post_meta( $order_id, '_hf_status_log', true );
	$log[] = array( $status, time() );
	update_post_meta( $order_id, '_hf_status_log', $log );
}

/**
 * Notify the atelier and the customer.
 *
 * @param int    $order_id Order ID.
 * @param string $track    Tracking URL.
 */
function hf_send_order_emails( $order_id, $track ) {
	$customer = (array) get_post_meta( $order_id, '_hf_customer', true );
	$number   = hf_order_number( $order_id );
	$items    = (array) get_post_meta( $order_id, '_hf_items', true );
	$lines    = array();
	foreach ( $items as $line ) {
		$lines[] = sprintf( '%d × %s%s', $line['qty'], $line['title'], $line['size'] ? ' (' . $line['size'] . ')' : '' );
	}
	$body = implode( "\n", $lines ) . "\n\n" . sprintf(
		/* translators: 1: total, 2: address */
		__( "Total: %1\$s\nDeliver to: %2\$s", 'hadhramifashion' ),
		hf_price( get_post_meta( $order_id, '_hf_total', true ) ),
		get_post_meta( $order_id, '_hf_address', true )
	);

	$admin = hf_opt( 'email' );
	if ( $admin ) {
		wp_mail(
			$admin,
			/* translators: %s: order number */
			sprintf( __( 'New order %s', 'hadhramifashion' ), $number ),
			$body . "\n\n" . admin_url( 'post.php?post=' . $order_id . '&action=edit' )
		);
	}
	if ( ! empty( $customer['email'] ) ) {
		wp_mail(
			$customer['email'],
			/* translators: %s: order number */
			sprintf( __( 'Your Hadhrami order %s', 'hadhramifashion' ), $number ),
			__( 'Thank you. The atelier has your order.', 'hadhramifashion' ) . "\n\n" . $body . "\n\n" . __( 'Follow your delivery live:', 'hadhramifashion' ) . ' ' . $track
		);
	}
}

/*
 * Admin: order screen.
 */
add_filter(
	'manage_hf_order_posts_columns',
	function ( $columns ) {
		return array(
			'cb'        => $columns['cb'],
			'title'     => __( 'Order', 'hadhramifashion' ),
			'hf_status' => __( 'Status', 'hadhramifashion' ),
			'hf_tier'   => __( 'Promise', 'hadhramifashion' ),
			'hf_eta'    => __( 'Due', 'hadhramifashion' ),
			'hf_total'  => __( 'Total', 'hadhramifashion' ),
			'date'      => $columns['date'],
		);
	}
);

add_action(
	'manage_hf_order_posts_custom_column',
	function ( $column, $order_id ) {
		switch ( $column ) {
			case 'hf_status':
				$statuses = hf_order_statuses();
				$status   = get_post_meta( $order_id, '_hf_status', true );
				echo esc_html( isset( $statuses[ $status ] ) ? $statuses[ $status ] : $status );
				break;
			case 'hf_tier':
				$tiers = hf_delivery_tiers();
				$tier  = get_post_meta( $order_id, '_hf_tier', true );
				echo esc_html( isset( $tiers[ $tier ] ) ? $tiers[ $tier ]['label'] : '' );
				break;
			case 'hf_eta':
				$eta = (int) get_post_meta( $order_id, '_hf_eta', true );
				echo $eta ? esc_html( wp_date( 'j M, ' . get_option( 'time_format' ), $eta ) ) : '';
				break;
			case 'hf_total':
				echo esc_html( hf_price( get_post_meta( $order_id, '_hf_total', true ) ) );
				break;
		}
	},
	10,
	2
);

add_action(
	'add_meta_boxes_hf_order',
	function () {
		add_meta_box( 'hf_order_main', __( 'Order details', 'hadhramifashion' ), 'hf_render_order_box', 'hf_order', 'normal', 'high' );
		add_meta_box( 'hf_order_dispatch', __( 'Dispatch', 'hadhramifashion' ), 'hf_render_dispatch_box', 'hf_order', 'side', 'high' );
	}
);

/**
 * Order details meta box.
 *
 * @param WP_Post $post Order.
 */
function hf_render_order_box( $post ) {
	$customer = (array) get_post_meta( $post->ID, '_hf_customer', true );
	$items    = (array) get_post_meta( $post->ID, '_hf_items', true );
	$lat      = get_post_meta( $post->ID, '_hf_lat', true );
	$lng      = get_post_meta( $post->ID, '_hf_lng', true );
	$tiers    = hf_delivery_tiers();
	$tier     = get_post_meta( $post->ID, '_hf_tier', true );
	?>
	<h3 style="margin-top:0"><?php echo esc_html( hf_order_number( $post->ID ) ); ?> · <?php echo esc_html( isset( $tiers[ $tier ] ) ? $tiers[ $tier ]['label'] : '' ); ?></h3>
	<p>
		<strong><?php echo esc_html( isset( $customer['name'] ) ? $customer['name'] : '' ); ?></strong><br>
		<a href="tel:<?php echo esc_attr( isset( $customer['phone'] ) ? $customer['phone'] : '' ); ?>"><?php echo esc_html( isset( $customer['phone'] ) ? $customer['phone'] : '' ); ?></a>
		<?php echo ! empty( $customer['email'] ) ? ' · ' . esc_html( $customer['email'] ) : ''; ?>
	</p>
	<p>
		<?php echo esc_html( get_post_meta( $post->ID, '_hf_address', true ) ); ?><br>
		<?php echo esc_html( get_post_meta( $post->ID, '_hf_address_more', true ) ); ?><br>
		<a target="_blank" rel="noopener" href="<?php echo esc_url( 'https://www.google.com/maps/dir/?api=1&destination=' . rawurlencode( $lat . ',' . $lng ) ); ?>"><?php esc_html_e( 'Open route in Google Maps', 'hadhramifashion' ); ?></a>
		· <?php echo esc_html( get_post_meta( $post->ID, '_hf_distance_km', true ) ); ?> km
	</p>
	<table class="widefat striped">
		<thead><tr><th><?php esc_html_e( 'Piece', 'hadhramifashion' ); ?></th><th><?php esc_html_e( 'Fit', 'hadhramifashion' ); ?></th><th><?php esc_html_e( 'Qty', 'hadhramifashion' ); ?></th><th><?php esc_html_e( 'Price', 'hadhramifashion' ); ?></th></tr></thead>
		<tbody>
		<?php foreach ( $items as $line ) : ?>
			<tr>
				<td>
					<?php echo esc_html( $line['title'] ); ?>
					<?php if ( ! empty( $line['measurements'] ) ) : ?>
						<br><small>
						<?php
						$parts = array();
						foreach ( $line['measurements'] as $k => $v ) {
							$parts[] = $k . ': ' . $v;
						}
						echo esc_html( implode( ' · ', $parts ) );
						?>
						</small>
					<?php endif; ?>
				</td>
				<td><?php echo esc_html( 'measure' === $line['fit'] ? __( 'Made to measure', 'hadhramifashion' ) : __( 'Ready to wear', 'hadhramifashion' ) . ( $line['size'] ? ' · ' . $line['size'] : '' ) ); ?></td>
				<td><?php echo esc_html( $line['qty'] ); ?></td>
				<td><?php echo esc_html( hf_price( $line['price'] * $line['qty'] ) ); ?></td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
	<p>
		<?php esc_html_e( 'Delivery', 'hadhramifashion' ); ?>: <?php echo esc_html( hf_price( get_post_meta( $post->ID, '_hf_fee', true ) ) ); ?> ·
		<strong><?php esc_html_e( 'Total', 'hadhramifashion' ); ?>: <?php echo esc_html( hf_price( get_post_meta( $post->ID, '_hf_total', true ) ) ); ?></strong> ·
		<?php echo esc_html( get_post_meta( $post->ID, '_hf_payment', true ) ); ?>
	</p>
	<?php if ( 'visit' === get_post_meta( $post->ID, '_hf_measure_mode', true ) ) : ?>
		<p><strong><?php esc_html_e( 'Customer asked for a home measurement visit.', 'hadhramifashion' ); ?></strong></p>
	<?php endif; ?>
	<?php $notes = get_post_meta( $post->ID, '_hf_notes', true ); ?>
	<?php if ( $notes ) : ?>
		<p><em><?php echo esc_html( $notes ); ?></em></p>
	<?php endif; ?>
	<?php
}

/**
 * Dispatch meta box: status, rider details and rider link.
 *
 * @param WP_Post $post Order.
 */
function hf_render_dispatch_box( $post ) {
	wp_nonce_field( 'hf_order_save', 'hf_order_nonce' );
	$status = get_post_meta( $post->ID, '_hf_status', true );
	$key    = get_post_meta( $post->ID, '_hf_key', true );
	$token  = get_post_meta( $post->ID, '_hf_rider_token', true );
	$rider  = add_query_arg(
		array(
			'order' => $key,
			'token' => $token,
		),
		hf_page_url( 'page-templates/rider.php' )
	);
	$track = add_query_arg( 'order', $key, hf_page_url( 'page-templates/track.php' ) );
	?>
	<p>
		<label for="hf_status"><strong><?php esc_html_e( 'Status', 'hadhramifashion' ); ?></strong></label><br>
		<select id="hf_status" name="hf_status" style="width:100%">
			<?php foreach ( hf_order_statuses() as $k => $label ) : ?>
				<option value="<?php echo esc_attr( $k ); ?>" <?php selected( $status, $k ); ?>><?php echo esc_html( $label ); ?></option>
			<?php endforeach; ?>
		</select>
	</p>
	<p>
		<label for="hf_rider_name"><?php esc_html_e( 'Rider name', 'hadhramifashion' ); ?></label>
		<input type="text" id="hf_rider_name" name="hf_rider_name" style="width:100%" value="<?php echo esc_attr( get_post_meta( $post->ID, '_hf_rider_name', true ) ); ?>">
	</p>
	<p>
		<label for="hf_rider_phone"><?php esc_html_e( 'Rider phone', 'hadhramifashion' ); ?></label>
		<input type="text" id="hf_rider_phone" name="hf_rider_phone" style="width:100%" value="<?php echo esc_attr( get_post_meta( $post->ID, '_hf_rider_phone', true ) ); ?>">
	</p>
	<p>
		<strong><?php esc_html_e( 'Rider link', 'hadhramifashion' ); ?></strong><br>
		<small><?php esc_html_e( 'Send this to the rider’s phone. Opening it shares their live location with the customer until delivery.', 'hadhramifashion' ); ?></small>
		<input type="text" readonly onclick="this.select()" style="width:100%" value="<?php echo esc_url( $rider ); ?>">
	</p>
	<p>
		<a href="<?php echo esc_url( $track ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Customer tracking page →', 'hadhramifashion' ); ?></a>
	</p>
	<?php
}

add_action(
	'save_post_hf_order',
	function ( $order_id ) {
		if ( ! isset( $_POST['hf_order_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['hf_order_nonce'] ), 'hf_order_save' ) ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $order_id ) ) {
			return;
		}
		if ( isset( $_POST['hf_status'] ) ) {
			hf_set_order_status( $order_id, sanitize_key( $_POST['hf_status'] ) );
		}
		foreach ( array( 'hf_rider_name', 'hf_rider_phone' ) as $field ) {
			if ( isset( $_POST[ $field ] ) ) {
				update_post_meta( $order_id, '_' . $field, sanitize_text_field( wp_unslash( $_POST[ $field ] ) ) );
			}
		}
	}
);
