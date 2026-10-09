<?php
/**
 * Distance-based delivery fee at checkout.
 *
 * Fee = base (covers the first N km) + per-km beyond that, capped, rounded to whole rupees.
 * Free above a basket value. Distance is the road estimate from the store to the customer's
 * map pin; without a pin a flat fallback fee applies.
 *
 * @package OneLoveDelivery
 */

defined( 'ABSPATH' ) || exit;

class OLE_Fees {

	const SESSION_KEY = 'ole_point';

	public static function init() {
		add_action( 'woocommerce_checkout_update_order_review', array( __CLASS__, 'remember_point' ) );
		add_action( 'woocommerce_cart_calculate_fees', array( __CLASS__, 'add_fee' ) );
		add_action( 'woocommerce_review_order_before_order_total', array( __CLASS__, 'free_nudge' ) );
		add_action( 'woocommerce_checkout_create_order', array( __CLASS__, 'save_to_order' ), 20 );
	}

	public static function enabled() {
		return OLE_Settings::yes( 'fee_enabled' );
	}

	/** Road distance estimate in km (straight line × 1.35), one decimal. */
	public static function road_km( $lat, $lng ) {
		$store = OLE_Settings::store_point();
		return round( OLE_Geo::distance_km( $store[0], $store[1], $lat, $lng ) * 1.35, 1 );
	}

	/**
	 * @return array{amount: float, km: float|null, free: bool, label: string}
	 */
	public static function quote( $subtotal, $point = null ) {
		$free_above = (float) OLE_Settings::get( 'fee_free_above' );
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
			$amount = (float) OLE_Settings::get( 'fee_fallback' );
			$label  = 'Delivery (pin your location for an exact fee)';
		} else {
			$base_km = (float) OLE_Settings::get( 'fee_base_km' );
			$amount  = (float) OLE_Settings::get( 'fee_base' ) + max( 0, $km - $base_km ) * (float) OLE_Settings::get( 'fee_per_km' );
			$max     = (float) OLE_Settings::get( 'fee_max' );
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
	protected static function current_point() {
		// phpcs:disable WordPress.Security.NonceVerification
		if ( isset( $_POST['ole_lat'], $_POST['ole_lng'] ) && OLE_Geo::valid_point( wp_unslash( $_POST['ole_lat'] ), wp_unslash( $_POST['ole_lng'] ) ) ) {
			return array( (float) wp_unslash( $_POST['ole_lat'] ), (float) wp_unslash( $_POST['ole_lng'] ) );
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
		if ( isset( $data['ole_lat'], $data['ole_lng'] ) && OLE_Geo::valid_point( $data['ole_lat'], $data['ole_lng'] ) ) {
			WC()->session->set( self::SESSION_KEY, array( (float) $data['ole_lat'], (float) $data['ole_lng'] ) );
		} else {
			WC()->session->set( self::SESSION_KEY, null );
		}
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
		$free_above = (float) OLE_Settings::get( 'fee_free_above' );
		$gap        = $free_above - self::cart_value( WC()->cart );
		if ( $free_above <= 0 || $gap <= 0 ) {
			return;
		}
		printf(
			'<tr class="ole-free-nudge"><td colspan="2" style="background:#fff3d1;border:2px dashed #f2b705;border-radius:10px;font-size:13px;font-weight:700;text-align:center;padding:10px;">Add %s more for <span style="color:#128a3f;">FREE delivery</span> &rarr;</td></tr>',
			wp_kses_post( wc_price( $gap ) )
		);
	}

	public static function save_to_order( $order ) {
		if ( ! self::enabled() ) {
			return;
		}
		foreach ( $order->get_fees() as $fee ) {
			if ( 0 === strpos( $fee->get_name(), 'Delivery' ) ) {
				$order->update_meta_data( '_ole_delivery_fee', (float) $fee->get_total() );
				return;
			}
		}
	}

	/** Delivery fee charged on an order (0 when free / none). */
	public static function order_fee( $order ) {
		$order = wc_get_order( $order );
		return $order ? (float) $order->get_meta( '_ole_delivery_fee' ) : 0.0;
	}
}
