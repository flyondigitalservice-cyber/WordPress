<?php
/**
 * Distance-based delivery fee at checkout.
 *
 * Fee = base (covers the first N km) + per-km beyond that, capped, rounded to whole rupees.
 * Free above a basket value. Distance is the road estimate from the store to the customer's
 * map pin; without a pin a flat fallback fee applies.
 *
 * @package HadhramiDelivery
 */

defined( 'ABSPATH' ) || exit;

class HFD_Fees {

	const SESSION_KEY = 'hfd_point';

	public static function init() {
		add_action( 'woocommerce_checkout_update_order_review', array( __CLASS__, 'remember_point' ) );
		add_action( 'woocommerce_cart_calculate_fees', array( __CLASS__, 'add_fee' ) );
		add_action( 'woocommerce_review_order_before_order_total', array( __CLASS__, 'free_nudge' ) );
		add_action( 'woocommerce_checkout_create_order', array( __CLASS__, 'save_to_order' ), 20 );
	}

	public static function enabled() {
		return HFD_Settings::yes( 'fee_enabled' );
	}

	/** Road distance estimate in km (straight line × 1.35), one decimal. */
	public static function road_km( $lat, $lng ) {
		$store = HFD_Settings::store_point();
		return round( HFD_Geo::distance_km( $store[0], $store[1], $lat, $lng ) * 1.35, 1 );
	}

	/**
	 * @return array{amount: float, km: float|null, free: bool, label: string}
	 */
	public static function quote( $subtotal, $point = null ) {
		$free_above = (float) HFD_Settings::get( 'fee_free_above' );
		$km         = $point ? self::road_km( $point[0], $point[1] ) : null;

		if ( $free_above > 0 && $subtotal >= $free_above ) {
			return array(
				'amount' => 0.0,
				'km'     => $km,
				'free'   => true,
				'label'  => 'Delivery' . ( null !== $km ? ' · ' . number_format( $km, 1 ) . ' km' : '' ) . ' · FREE',
			);
		}

		if ( null === $km ) {
			$amount = (float) HFD_Settings::get( 'fee_fallback' );
			$label  = 'Delivery (pin your location for an exact fee)';
		} else {
			$base_km = (float) HFD_Settings::get( 'fee_base_km' );
			$amount  = (float) HFD_Settings::get( 'fee_base' ) + max( 0, $km - $base_km ) * (float) HFD_Settings::get( 'fee_per_km' );
			$max     = (float) HFD_Settings::get( 'fee_max' );
			if ( $max > 0 ) {
				$amount = min( $amount, $max );
			}
			$label = 'Delivery · ' . number_format( $km, 1 ) . ' km';
		}

		return array(
			'amount' => (float) round( max( 0, $amount ) ),
			'km'     => $km,
			'free'   => false,
			'label'  => $label,
		);
	}

	/** Pin from the final checkout POST, else from the last checkout refresh. */
	public static function current_point() {
		// phpcs:disable WordPress.Security.NonceVerification
		if ( isset( $_POST['hfd_lat'], $_POST['hfd_lng'] ) && HFD_Geo::valid_point( wp_unslash( $_POST['hfd_lat'] ), wp_unslash( $_POST['hfd_lng'] ) ) ) {
			return array( (float) wp_unslash( $_POST['hfd_lat'] ), (float) wp_unslash( $_POST['hfd_lng'] ) );
		}
		// phpcs:enable
		$saved = WC()->session ? WC()->session->get( self::SESSION_KEY ) : null;
		return is_array( $saved ) && 2 === count( $saved ) ? $saved : null;
	}

	/** The checkout sends the whole form on every refresh — keep the pin in the session. */
	public static function remember_point( $post_data ) {
		parse_str( (string) $post_data, $data );
		if ( ! WC()->session ) {
			return;
		}
		if ( isset( $data['hfd_lat'], $data['hfd_lng'] ) && HFD_Geo::valid_point( $data['hfd_lat'], $data['hfd_lng'] ) ) {
			WC()->session->set( self::SESSION_KEY, array( (float) $data['hfd_lat'], (float) $data['hfd_lng'] ) );
		} else {
			WC()->session->set( self::SESSION_KEY, null );
		}
		// Remember only a promise the customer actually picked; otherwise default to the fastest.
		$picked = ! empty( $data['hfd_tier_picked'] ) && isset( $data['hfd_tier'] ) ? sanitize_key( $data['hfd_tier'] ) : '';
		WC()->session->set( HFD_Promise::SESSION_KEY, $picked );
	}

	protected static function cart_value( $cart ) {
		return (float) $cart->get_subtotal() + (float) $cart->get_subtotal_tax() - (float) $cart->get_discount_total() - (float) $cart->get_discount_tax();
	}

	public static function add_fee( $cart ) {
		if ( ! self::enabled() || ! function_exists( 'is_checkout' ) || ! is_checkout() || ! self::has_physical_items( $cart ) ) {
			return;
		}
		$q = self::quote( self::cart_value( $cart ), self::current_point() );
		$cart->add_fee( $q['label'], $q['amount'], false );

		$opts = HFD_Promise::options( self::current_point(), HFD_Promise::cart_all_express( $cart ) );
		if ( 'express' === HFD_Promise::chosen_tier( $opts ) && (float) HFD_Settings::get( 'express_fee' ) > 0 ) {
			$cart->add_fee( 'Express ' . HFD_Promise::label( 'express' ), (float) HFD_Settings::get( 'express_fee' ), false );
		}
	}

	/**
	 * Cart has something a rider must carry. (Not $cart->needs_shipping(): that is false
	 * whenever the store has no WooCommerce shipping methods, which is how this store runs.)
	 */
	public static function has_physical_items( $cart ) {
		foreach ( $cart->get_cart() as $item ) {
			if ( isset( $item['data'] ) && $item['data'] instanceof WC_Product && ! $item['data']->is_virtual() ) {
				return true;
			}
		}
		return false;
	}

	public static function free_nudge() {
		if ( ! self::enabled() || ! WC()->cart || ! self::has_physical_items( WC()->cart ) ) {
			return;
		}
		$free_above = (float) HFD_Settings::get( 'fee_free_above' );
		$gap        = $free_above - self::cart_value( WC()->cart );
		if ( $free_above <= 0 || $gap <= 0 ) {
			return;
		}
		printf(
			'<tr class="hfd-free-nudge"><td colspan="2" style="background:#f6eedc;border:1px dashed #c8a86f;font-size:13px;text-align:center;padding:10px;">Add %s more for <strong style="color:#7a5a2e;">complimentary delivery</strong></td></tr>',
			wp_kses_post( wc_price( $gap ) )
		);
	}

	public static function save_to_order( $order ) {
		if ( ! self::enabled() ) {
			return;
		}
		$total = 0.0;
		foreach ( $order->get_fees() as $fee ) {
			if ( 0 === strpos( $fee->get_name(), 'Delivery' ) || 0 === strpos( $fee->get_name(), 'Express' ) ) {
				$total += (float) $fee->get_total();
			}
		}
		$order->update_meta_data( '_hfd_delivery_fee', $total );
	}

	/** Delivery fee charged on an order (0 when free / none). */
	public static function order_fee( $order ) {
		$order = wc_get_order( $order );
		return $order ? (float) $order->get_meta( '_hfd_delivery_fee' ) : 0.0;
	}
}
