<?php
/**
 * Delivery promises: the Hadhrami "2 hours for a shirt, 24 hours for a suit".
 *
 *  express  — every item in the bag is ready-to-wear stock marked for 2-hour delivery,
 *             the customer pinned a spot inside the express radius, and the atelier is
 *             open (orders after the cut-off are due first thing next morning).
 *  tailored — anything else inside the service area: cut to measure and delivered
 *             within the tailoring window (24 h by default).
 *
 * The chosen promise is stored on the order (_hfd_tier, _hfd_due) and copied onto the
 * delivery row, so dispatch, the rider app, tracking and reports all share one due time.
 *
 * @package HadhramiDelivery
 */

defined( 'ABSPATH' ) || exit;

class HFD_Promise {

	const SESSION_KEY = 'hfd_tier';

	public static function labels() {
		return array(
			'express'  => sprintf( 'Within %s', self::duration_text( (int) HFD_Settings::get( 'express_minutes' ) ) ),
			'tailored' => sprintf( 'Tailored within %d hours', (int) HFD_Settings::get( 'tailored_hours' ) ),
		);
	}

	public static function label( $tier ) {
		$labels = self::labels();
		return isset( $labels[ $tier ] ) ? $labels[ $tier ] : '';
	}

	protected static function duration_text( $minutes ) {
		if ( $minutes >= 60 && 0 === $minutes % 60 ) {
			$h = $minutes / 60;
			return $h . ' hour' . ( 1 === $h ? '' : 's' );
		}
		return $minutes . ' min';
	}

	public static function init() {
		// Product flag: "ready-to-wear stock for 2-hour delivery".
		add_action( 'woocommerce_product_options_general_product_data', array( __CLASS__, 'product_field' ) );
		add_action( 'woocommerce_admin_process_product_object', array( __CLASS__, 'save_product_field' ) );
	}

	/* ------------------------------------------------------------------ */
	/* Product flag                                                        */
	/* ------------------------------------------------------------------ */

	public static function product_field() {
		woocommerce_wp_checkbox( array(
			'id'          => '_hfd_express',
			'label'       => '2-hour delivery stock',
			'description' => 'Ready-to-wear and kept in stock at the atelier — eligible for the 2-hour promise.',
		) );
	}

	public static function save_product_field( $product ) {
		$product->update_meta_data( '_hfd_express', isset( $_POST['_hfd_express'] ) ? 'yes' : 'no' ); // phpcs:ignore WordPress.Security.NonceVerification -- WooCommerce verifies the product nonce.
	}

	/** Item is ready-to-wear express stock (and not ordered made-to-measure). */
	public static function item_is_express( $product, $cart_item = array() ) {
		if ( ! $product instanceof WC_Product ) {
			return false;
		}
		$id = $product->get_parent_id() ? $product->get_parent_id() : $product->get_id();
		if ( ! empty( $cart_item['hfd_made_to_measure'] ) ) {
			return false;
		}
		return 'yes' === get_post_meta( $id, '_hfd_express', true );
	}

	public static function cart_all_express( $cart ) {
		if ( ! $cart || $cart->is_empty() ) {
			return false;
		}
		foreach ( $cart->get_cart() as $item ) {
			if ( isset( $item['data'] ) && $item['data'] instanceof WC_Product && ! $item['data']->is_virtual() && ! self::item_is_express( $item['data'], $item ) ) {
				return false;
			}
		}
		return true;
	}

	/* ------------------------------------------------------------------ */
	/* Timing                                                              */
	/* ------------------------------------------------------------------ */

	/** When the atelier can start an express run: now if open, else the next opening. */
	public static function express_start( $now = null ) {
		$now    = $now ? $now : current_datetime();
		$hour   = (int) $now->format( 'G' );
		$open   = (int) HFD_Settings::get( 'open_hour' );
		$cutoff = (int) HFD_Settings::get( 'express_cutoff_hour' );
		if ( $hour >= $open && $hour < $cutoff ) {
			return $now;
		}
		$day = $hour >= $cutoff ? $now->modify( '+1 day' ) : $now;
		return $day->setTime( $open, 0 );
	}

	/** Unix timestamp the order is due at the customer's door. */
	public static function due_for( $tier, $from = null ) {
		$from = $from ? $from : current_datetime();
		if ( 'express' === $tier ) {
			return self::express_start( $from )->getTimestamp() + (int) HFD_Settings::get( 'express_minutes' ) * MINUTE_IN_SECONDS;
		}
		return $from->getTimestamp() + (int) HFD_Settings::get( 'tailored_hours' ) * HOUR_IN_SECONDS;
	}

	/** "today by 4:30 pm" / "tomorrow by 11:00 am" / "Friday 17 Oct by 6:00 pm". */
	public static function due_text( $ts ) {
		if ( ! $ts ) {
			return '';
		}
		$time  = wp_date( 'g:i a', $ts );
		$day   = wp_date( 'Y-m-d', $ts );
		$today = wp_date( 'Y-m-d' );
		if ( $day === $today ) {
			return 'today by ' . $time;
		}
		if ( wp_date( 'Y-m-d', time() + DAY_IN_SECONDS ) === $day ) {
			return 'tomorrow by ' . $time;
		}
		return wp_date( 'l j M', $ts ) . ' by ' . $time;
	}

	/* ------------------------------------------------------------------ */
	/* Quote                                                               */
	/* ------------------------------------------------------------------ */

	/**
	 * Which promises apply for a pinned point (or no pin) and a bag.
	 *
	 * @param array|null $point     array( lat, lng ) or null.
	 * @param bool       $all_ready Bag is entirely express stock.
	 * @return array{km: float|null, in_area: bool, in_express: bool, options: array}
	 */
	public static function options( $point, $all_ready ) {
		$km         = $point ? HFD_Fees::road_km( $point[0], $point[1] ) : null;
		$area_km    = (float) HFD_Settings::get( 'service_radius_km' );
		$express_km = (float) HFD_Settings::get( 'express_radius_km' );
		$in_area    = null === $km || $area_km <= 0 || $km <= $area_km;
		$in_express = null !== $km && $km <= $express_km;

		$express_reason = '';
		if ( ! $all_ready ) {
			$express_reason = 'Only when every piece is ready-to-wear 2-hour stock.';
		} elseif ( null === $km ) {
			$express_reason = 'Pin your location on the map to check 2-hour delivery.';
		} elseif ( ! $in_express ) {
			$express_reason = sprintf( 'Your pin is %.1f km away — 2-hour delivery reaches %s km.', $km, HFD_Settings::get( 'express_radius_km' ) );
		}

		$express_due  = self::due_for( 'express' );
		$tailored_due = self::due_for( 'tailored' );
		return array(
			'km'         => $km,
			'in_area'    => $in_area,
			'in_express' => $in_express,
			'options'    => array(
				'express'  => array(
					'label'     => self::label( 'express' ),
					'available' => $in_area && '' === $express_reason,
					'reason'    => $express_reason,
					'surcharge' => (float) HFD_Settings::get( 'express_fee' ),
					'due'       => $express_due,
					'due_text'  => self::due_text( $express_due ),
				),
				'tailored' => array(
					'label'     => self::label( 'tailored' ),
					'available' => $in_area,
					'reason'    => $in_area ? '' : 'Outside our delivery area.',
					'surcharge' => 0.0,
					'due'       => $tailored_due,
					'due_text'  => self::due_text( $tailored_due ),
				),
			),
		);
	}

	/** Tier for the current checkout: the customer's choice if valid, else the fastest valid one. */
	public static function chosen_tier( $options ) {
		$want = WC()->session ? WC()->session->get( self::SESSION_KEY ) : '';
		// phpcs:ignore WordPress.Security.NonceVerification -- final checkout POST, verified by WooCommerce.
		if ( isset( $_POST['hfd_tier'] ) ) {
			$want = sanitize_key( wp_unslash( $_POST['hfd_tier'] ) ); // phpcs:ignore WordPress.Security.NonceVerification
		}
		if ( $want && ! empty( $options['options'][ $want ]['available'] ) ) {
			return $want;
		}
		foreach ( array( 'express', 'tailored' ) as $tier ) {
			if ( ! empty( $options['options'][ $tier ]['available'] ) ) {
				return $tier;
			}
		}
		return '';
	}
}
