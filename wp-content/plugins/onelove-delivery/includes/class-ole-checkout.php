<?php
/**
 * Checkout: Mumbai service-area check (PIN code + optional distance) and an optional
 * "pin your exact location" map so riders navigate to the right door.
 *
 * @package OneLoveDelivery
 */

defined( 'ABSPATH' ) || exit;

class OLE_Checkout {

	public static function init() {
		add_action( 'woocommerce_after_checkout_validation', array( __CLASS__, 'validate' ), 10, 2 );
		add_action( 'woocommerce_checkout_create_order', array( __CLASS__, 'save' ), 10, 2 );
		add_action( 'woocommerce_after_checkout_billing_form', array( __CLASS__, 'pin_box' ) );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'assets' ) );
		add_action( 'woocommerce_review_order_before_order_total', array( __CLASS__, 'promise_row' ), 5 );
	}

	/** "Delivery time: 30–45 min" line in the checkout order summary. */
	public static function promise_row() {
		if ( ! WC()->cart || ! OLE_Fees::has_physical_items( WC()->cart ) ) {
			return;
		}
		printf(
			'<tr class="ole-promise"><th>Delivery time</th><td><strong>%s</strong><br><small>Our riders deliver safely — no rush.</small></td></tr>',
			esc_html( OLE_Settings::promise_text() )
		);
	}

	protected static function pin_enabled() {
		return OLE_Settings::yes( 'checkout_pin' ) && '' !== OLE_Settings::get( 'maps_key' );
	}

	public static function assets() {
		if ( ! self::pin_enabled() || ! function_exists( 'is_checkout' ) || ! is_checkout() || is_order_received_page() ) {
			return;
		}
		wp_enqueue_style( 'ole-checkout', OLE_DELIVERY_URL . 'assets/css/checkout.css', array(), OLE_DELIVERY_VERSION );
		wp_enqueue_script( 'ole-maps-loader', OLE_DELIVERY_URL . 'assets/js/maps-loader.js', array(), OLE_DELIVERY_VERSION, true );
		wp_enqueue_script( 'ole-checkout', OLE_DELIVERY_URL . 'assets/js/checkout.js', array( 'ole-maps-loader' ), OLE_DELIVERY_VERSION, true );
		$store = OLE_Settings::store_point();
		wp_localize_script( 'ole-checkout', 'OLE_CHECKOUT', array(
			'key'   => OLE_Settings::get( 'maps_key' ),
			'store' => array( 'lat' => $store[0], 'lng' => $store[1] ),
		) );
	}

	public static function pin_box() {
		if ( ! self::pin_enabled() ) {
			return;
		}
		?>
		<div class="ole-pin" id="ole-pin" hidden>
			<div class="ole-pin__head">
				<strong>Pin your exact delivery spot</strong>
				<span>Optional — helps our rider reach your door faster.</span>
			</div>
			<div class="ole-pin__search" id="ole-pin-search"></div>
			<div class="ole-pin__map" id="ole-pin-map" aria-label="Delivery location map"></div>
			<div class="ole-pin__bar">
				<button type="button" class="button ole-pin__locate" id="ole-pin-locate">Use my current location</button>
				<span class="ole-pin__status" id="ole-pin-status">Drag the pin to your building gate.</span>
			</div>
			<input type="hidden" name="ole_lat" id="ole_lat" value="">
			<input type="hidden" name="ole_lng" id="ole_lng" value="">
		</div>
		<?php
	}

	protected static function posted_point() {
		$lat = isset( $_POST['ole_lat'] ) ? sanitize_text_field( wp_unslash( $_POST['ole_lat'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
		$lng = isset( $_POST['ole_lng'] ) ? sanitize_text_field( wp_unslash( $_POST['ole_lng'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
		return OLE_Geo::valid_point( $lat, $lng ) ? array( (float) $lat, (float) $lng ) : null;
	}

	public static function validate( $data, $errors ) {
		$ship     = ! empty( $data['ship_to_different_address'] );
		$postcode = preg_replace( '/\s+/', '', (string) ( $ship ? ( $data['shipping_postcode'] ?? '' ) : ( $data['billing_postcode'] ?? '' ) ) );
		$prefixes = OLE_Settings::postcode_prefixes();

		if ( $prefixes && '' !== $postcode ) {
			$ok = false;
			foreach ( $prefixes as $p ) {
				if ( 0 === strpos( $postcode, $p ) ) {
					$ok = true;
					break;
				}
			}
			if ( ! $ok ) {
				$errors->add( 'ole_area', sprintf(
					'Sorry, we deliver only in Mumbai right now (PIN %s is outside our area). You can still order One Love on Amazon or Flipkart for pan-India delivery.',
					esc_html( $postcode )
				) );
				return;
			}
		}

		$radius = (float) OLE_Settings::get( 'service_radius_km' );
		$pt     = self::posted_point();
		if ( $pt && $radius > 0 ) {
			$store = OLE_Settings::store_point();
			$km    = OLE_Geo::distance_km( $pt[0], $pt[1], $store[0], $store[1] );
			if ( $km > $radius ) {
				$errors->add( 'ole_area', sprintf( 'The pinned location is %.1f km away — we currently deliver within %s km. Please move the pin or check your address.', $km, esc_html( OLE_Settings::get( 'service_radius_km' ) ) ) );
			}
		}
	}

	public static function save( $order, $data ) {
		$pt = self::posted_point();
		if ( $pt ) {
			$order->update_meta_data( '_ole_lat', $pt[0] );
			$order->update_meta_data( '_ole_lng', $pt[1] );
		}
	}
}
