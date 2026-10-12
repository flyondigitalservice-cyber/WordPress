<?php
/**
 * Checkout: service-area check (PIN code + distance), the "pin your exact door" map,
 * and the delivery-promise chooser (2-hour express / tailored within 24 hours).
 *
 * @package HadhramiDelivery
 */

defined( 'ABSPATH' ) || exit;

class HFD_Checkout {

	public static function init() {
		add_action( 'woocommerce_after_checkout_validation', array( __CLASS__, 'validate' ), 10, 2 );
		add_action( 'woocommerce_checkout_create_order', array( __CLASS__, 'save' ), 10, 2 );
		add_action( 'woocommerce_after_checkout_billing_form', array( __CLASS__, 'pin_box' ) );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'assets' ) );
		add_action( 'woocommerce_review_order_before_shipping', array( __CLASS__, 'promise_row' ), 5 );
		add_action( 'woocommerce_review_order_before_order_total', array( __CLASS__, 'promise_row_fallback' ), 1 );
	}

	/** Promise options for the current cart and pin. */
	public static function current_options() {
		$cart = WC()->cart;
		return HFD_Promise::options( HFD_Fees::current_point(), $cart ? HFD_Promise::cart_all_express( $cart ) : false );
	}

	protected static $promise_printed = false;

	/** "When" chooser in the checkout order summary (re-rendered on every checkout refresh). */
	public static function promise_row() {
		if ( self::$promise_printed || ! WC()->cart || ! HFD_Fees::has_physical_items( WC()->cart ) ) {
			return;
		}
		self::$promise_printed = true;
		$opts   = self::current_options();
		$chosen = HFD_Promise::chosen_tier( $opts );
		echo '<tr class="hfd-promise"><th>When</th><td><ul class="hfd-tiers">';
		foreach ( $opts['options'] as $tier => $o ) {
			printf(
				'<li class="hfd-tier%5$s"><label><input type="radio" name="hfd_tier" value="%1$s" %2$s %3$s> <strong>%4$s</strong>%6$s</label></li>',
				esc_attr( $tier ),
				checked( $chosen, $tier, false ),
				disabled( ! $o['available'], true, false ),
				esc_html( $o['label'] ),
				$o['available'] ? '' : ' is-off',
				$o['available']
					? '<small>Arrives ' . esc_html( $o['due_text'] ) . ( $o['surcharge'] > 0 ? ' · +' . wp_kses_post( wc_price( $o['surcharge'] ) ) : '' ) . '</small>'
					: '<small>' . esc_html( $o['reason'] ) . '</small>'
			);
		}
		echo '</ul></td></tr>';
	}

	/** Shops without shipping methods never fire the shipping hook — print before the total instead. */
	public static function promise_row_fallback() {
		self::promise_row();
	}

	protected static function pin_enabled() {
		return HFD_Settings::yes( 'checkout_pin' ) && '' !== HFD_Settings::get( 'maps_key' );
	}

	public static function assets() {
		if ( ! function_exists( 'is_checkout' ) || ! is_checkout() || is_order_received_page() ) {
			return;
		}
		// Switching promise re-prices the order (express surcharge).
		wp_add_inline_script( 'wc-checkout', "jQuery(function($){ $(document.body).on('change','input[name=hfd_tier]',function(){ $('#hfd_tier_picked').val('1'); $(document.body).trigger('update_checkout'); }); });" );
		wp_add_inline_style( 'woocommerce-general', '.hfd-tiers{list-style:none;margin:0;padding:0}.hfd-tier{margin:0 0 .6em}.hfd-tier small{display:block;margin-left:1.6em;opacity:.75}.hfd-tier.is-off{opacity:.55}' );
		if ( ! self::pin_enabled() ) {
			return;
		}
		wp_enqueue_style( 'hfd-checkout', HFD_DELIVERY_URL . 'assets/css/checkout.css', array(), HFD_DELIVERY_VERSION );
		wp_enqueue_script( 'hfd-maps-loader', HFD_DELIVERY_URL . 'assets/js/maps-loader.js', array(), HFD_DELIVERY_VERSION, true );
		wp_enqueue_script( 'hfd-checkout', HFD_DELIVERY_URL . 'assets/js/checkout.js', array( 'hfd-maps-loader' ), HFD_DELIVERY_VERSION, true );
		$store = HFD_Settings::store_point();
		wp_localize_script( 'hfd-checkout', 'HFD_CHECKOUT', array(
			'key'   => HFD_Settings::get( 'maps_key' ),
			'store' => array( 'lat' => $store[0], 'lng' => $store[1] ),
		) );
	}

	public static function pin_box() {
		// Set to 1 once the customer clicks a delivery promise (see assets()).
		echo '<input type="hidden" name="hfd_tier_picked" id="hfd_tier_picked" value="">';
		if ( ! self::pin_enabled() ) {
			return;
		}
		?>
		<div class="hfd-pin" id="hfd-pin" hidden>
			<div class="hfd-pin__head">
				<strong>Pin your exact delivery spot</strong>
				<span>Optional — helps our rider reach your door faster.</span>
			</div>
			<div class="hfd-pin__search" id="hfd-pin-search"></div>
			<div class="hfd-pin__map" id="hfd-pin-map" aria-label="Delivery location map"></div>
			<div class="hfd-pin__bar">
				<button type="button" class="button hfd-pin__locate" id="hfd-pin-locate">Use my current location</button>
				<span class="hfd-pin__status" id="hfd-pin-status">Drag the pin to your building gate.</span>
			</div>
			<input type="hidden" name="hfd_lat" id="hfd_lat" value="">
			<input type="hidden" name="hfd_lng" id="hfd_lng" value="">
		</div>
		<?php
	}

	protected static function posted_point() {
		$lat = isset( $_POST['hfd_lat'] ) ? sanitize_text_field( wp_unslash( $_POST['hfd_lat'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
		$lng = isset( $_POST['hfd_lng'] ) ? sanitize_text_field( wp_unslash( $_POST['hfd_lng'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
		return HFD_Geo::valid_point( $lat, $lng ) ? array( (float) $lat, (float) $lng ) : null;
	}

	/** Promise the customer is placing the order with (validated against the pin and bag). */
	protected static function final_tier() {
		$pt   = self::posted_point();
		$opts = HFD_Promise::options( $pt, WC()->cart ? HFD_Promise::cart_all_express( WC()->cart ) : false );
		return array( HFD_Promise::chosen_tier( $opts ), $opts );
	}

	public static function validate( $data, $errors ) {
		$ship     = ! empty( $data['ship_to_different_address'] );
		$postcode = preg_replace( '/\s+/', '', (string) ( $ship ? ( $data['shipping_postcode'] ?? '' ) : ( $data['billing_postcode'] ?? '' ) ) );
		$prefixes = HFD_Settings::postcode_prefixes();

		if ( $prefixes && '' !== $postcode ) {
			$ok = false;
			foreach ( $prefixes as $p ) {
				if ( 0 === strpos( $postcode, $p ) ) {
					$ok = true;
					break;
				}
			}
			if ( ! $ok ) {
				$errors->add( 'hfd_area', sprintf(
					'Sorry, PIN %s is outside our delivery area. Please call the atelier and we will arrange your fitting.',
					esc_html( $postcode )
				) );
				return;
			}
		}

		$radius = (float) HFD_Settings::get( 'service_radius_km' );
		$pt     = self::posted_point();
		if ( $pt && $radius > 0 ) {
			$store = HFD_Settings::store_point();
			$km    = HFD_Geo::distance_km( $pt[0], $pt[1], $store[0], $store[1] );
			if ( $km > $radius ) {
				$errors->add( 'hfd_area', sprintf( 'The pinned location is %.1f km away — we currently deliver within %s km. Please move the pin or check your address.', $km, esc_html( HFD_Settings::get( 'service_radius_km' ) ) ) );
				return;
			}
		}

		if ( WC()->cart && HFD_Fees::has_physical_items( WC()->cart ) ) {
			list( $tier, $opts ) = self::final_tier();
			$want = isset( $_POST['hfd_tier'] ) ? sanitize_key( wp_unslash( $_POST['hfd_tier'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
			if ( ! $tier ) {
				$errors->add( 'hfd_tier', 'Please choose when you would like your order delivered.' );
			} elseif ( $want && $want !== $tier ) {
				$errors->add( 'hfd_tier', $opts['options'][ $want ]['reason'] ? $opts['options'][ $want ]['reason'] : 'That delivery time is not available — please choose another.' );
			}
		}
	}

	public static function save( $order, $data ) {
		$pt = self::posted_point();
		if ( $pt ) {
			$order->update_meta_data( '_hfd_lat', $pt[0] );
			$order->update_meta_data( '_hfd_lng', $pt[1] );
		}
		list( $tier ) = self::final_tier();
		if ( $tier ) {
			$order->update_meta_data( '_hfd_tier', $tier );
			$order->update_meta_data( '_hfd_due', HFD_Promise::due_for( $tier ) );
		}
	}
}
