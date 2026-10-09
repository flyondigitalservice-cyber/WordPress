<?php
/**
 * Settings storage and the Deliveries → Settings screen.
 *
 * @package OneLoveDelivery
 */

defined( 'ABSPATH' ) || exit;

class OLE_Settings {

	const OPTION = 'ole_delivery_settings';

	/** Fields that hold secrets: never echoed back into the form. */
	const SECRETS = array( 'wa_token' );

	public static function defaults() {
		return array(
			'maps_key'          => '',
			'store_name'        => 'One Love Energy',
			'store_address'     => 'Andheri, Mumbai, Maharashtra 400058',
			'store_lat'         => '19.1197',
			'store_lng'         => '72.8464',
			'store_phone'       => '+91 90823 36907',
			'postcode_prefixes' => '400,401',
			'service_radius_km' => '35',
			'checkout_pin'      => 'yes',
			'auto_assign'       => 'yes',
			'max_active'        => '3',
			'stale_minutes'     => '10',
			'otp_required'      => 'yes',
			'ontime_minutes'    => '45',
			'admin_skin'        => 'yes',
			'hq_home'           => 'yes',
			'promise_min'       => '30',
			'promise_max'       => '45',
			'fee_enabled'       => 'yes',
			'fee_free_above'    => '499',
			'fee_base'          => '19',
			'fee_base_km'       => '3',
			'fee_per_km'        => '6',
			'fee_max'           => '69',
			'fee_fallback'      => '29',
			'pay_per_delivery'  => '25',
			'pay_per_km'        => '5',
			'pay_fallback_km'   => '4',
			'wa_enabled'        => 'no',
			'wa_token'          => '',
			'wa_phone_id'       => '',
			'wa_lang'           => 'en',
			'wa_tpl_rider'      => 'ole_rider_new_order',
			'wa_tpl_out'        => 'ole_out_for_delivery',
			'wa_tpl_delivered'  => 'ole_order_delivered',
			'wa_tpl_admin'      => 'ole_no_rider_alert',
			'wa_admin_phone'    => '',
		);
	}

	public static function all() {
		$saved = get_option( self::OPTION, array() );
		return wp_parse_args( is_array( $saved ) ? $saved : array(), self::defaults() );
	}

	public static function get( $key ) {
		$all = self::all();
		return isset( $all[ $key ] ) ? $all[ $key ] : '';
	}

	public static function yes( $key ) {
		return 'yes' === self::get( $key );
	}

	public static function store_point() {
		return array( (float) self::get( 'store_lat' ), (float) self::get( 'store_lng' ) );
	}

	/** Customer-facing delivery window, e.g. "30–45 min". */
	public static function promise_text() {
		$min = (int) self::get( 'promise_min' );
		$max = max( $min, (int) self::get( 'promise_max' ) );
		return $min === $max ? $max . ' min' : $min . '–' . $max . ' min';
	}

	public static function postcode_prefixes() {
		return array_filter( array_map( 'trim', explode( ',', (string) self::get( 'postcode_prefixes' ) ) ) );
	}

	public static function save( $input ) {
		$current = self::all();
		$clean   = array();
		foreach ( self::defaults() as $key => $default ) {
			if ( in_array( $key, array( 'checkout_pin', 'auto_assign', 'otp_required', 'wa_enabled', 'fee_enabled', 'admin_skin', 'hq_home' ), true ) ) {
				$clean[ $key ] = ! empty( $input[ $key ] ) ? 'yes' : 'no';
				continue;
			}
			$value = isset( $input[ $key ] ) ? trim( wp_unslash( $input[ $key ] ) ) : '';
			if ( in_array( $key, self::SECRETS, true ) && '' === $value ) {
				$value = $current[ $key ]; // Blank secret field = keep the stored one.
			}
			$clean[ $key ] = sanitize_text_field( $value );
		}
		$numbers = array( 'store_lat', 'store_lng', 'service_radius_km', 'ontime_minutes', 'promise_min', 'promise_max', 'fee_free_above', 'fee_base', 'fee_base_km', 'fee_per_km', 'fee_max', 'fee_fallback', 'pay_per_delivery', 'pay_per_km', 'pay_fallback_km' );
		foreach ( $numbers as $num ) {
			$clean[ $num ] = is_numeric( $clean[ $num ] ) ? $clean[ $num ] : $current[ $num ];
			if ( ! in_array( $num, array( 'store_lat', 'store_lng' ), true ) ) {
				$clean[ $num ] = (string) max( 0, (float) $clean[ $num ] );
			}
		}
		$clean['max_active']    = (string) max( 1, absint( $clean['max_active'] ) );
		$clean['stale_minutes'] = (string) max( 2, absint( $clean['stale_minutes'] ) );
		update_option( self::OPTION, $clean, false );
	}

	public static function render_page() {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}
		if ( isset( $_POST['ole_settings_nonce'] ) && wp_verify_nonce( sanitize_key( $_POST['ole_settings_nonce'] ), 'ole_save_settings' ) ) {
			self::save( isset( $_POST['ole'] ) ? (array) $_POST['ole'] : array() ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
			echo '<div class="notice notice-success is-dismissible"><p>Settings saved.</p></div>';
		}
		$s = self::all();

		$text = function ( $key, $label, $help = '', $type = 'text' ) use ( $s ) {
			$is_secret = in_array( $key, self::SECRETS, true );
			$value     = $is_secret ? '' : $s[ $key ];
			$ph        = ( $is_secret && $s[ $key ] ) ? 'Saved — leave blank to keep' : '';
			printf(
				'<tr><th scope="row"><label for="ole-%1$s">%2$s</label></th><td><input type="%5$s" class="regular-text" id="ole-%1$s" name="ole[%1$s]" value="%3$s" placeholder="%6$s" autocomplete="off">%4$s</td></tr>',
				esc_attr( $key ),
				esc_html( $label ),
				esc_attr( $value ),
				$help ? '<p class="description">' . wp_kses_post( $help ) . '</p>' : '',
				esc_attr( $type ),
				esc_attr( $ph )
			);
		};
		$check = function ( $key, $label, $help = '' ) use ( $s ) {
			printf(
				'<tr><th scope="row">%2$s</th><td><label><input type="checkbox" name="ole[%1$s]" value="1" %3$s> Enabled</label>%4$s</td></tr>',
				esc_attr( $key ),
				esc_html( $label ),
				checked( 'yes', $s[ $key ], false ),
				$help ? '<p class="description">' . wp_kses_post( $help ) . '</p>' : ''
			);
		};
		?>
		<div class="wrap">
			<h1>Delivery Settings</h1>
			<form method="post">
				<?php wp_nonce_field( 'ole_save_settings', 'ole_settings_nonce' ); ?>

				<h2>Google Maps</h2>
				<table class="form-table" role="presentation">
					<?php
					$text( 'maps_key', 'Maps API key', 'Enable <b>Maps JavaScript API</b> and <b>Places API (New)</b>. Restrict the key to <b>Websites</b> → <code>' . esc_html( home_url( '/*' ) ) . '</code>. Used for the dispatch map, live tracking map and checkout location pin. Navigation opens the rider\'s Google Maps app and needs no key.' );
					$check( 'checkout_pin', 'Location pin at checkout', 'Customers can pin their exact door on a map (optional for them). Riders then navigate to the exact point instead of the typed address.' );
					?>
				</table>

				<h2>Store (pickup point)</h2>
				<table class="form-table" role="presentation">
					<?php
					$text( 'store_name', 'Store name' );
					$text( 'store_address', 'Pickup address' );
					$text( 'store_lat', 'Latitude', 'Open Google Maps, right-click your store and click the numbers to copy them. The first number is latitude.' );
					$text( 'store_lng', 'Longitude', 'The second number is longitude.' );
					$text( 'store_phone', 'Store phone (shown to riders & customers)' );
					?>
				</table>

				<h2>Delivery area</h2>
				<table class="form-table" role="presentation">
					<?php
					$text( 'postcode_prefixes', 'Allowed PIN codes', 'Comma-separated prefixes. <code>400,401</code> covers Mumbai, Thane, Navi Mumbai and Mira-Bhayandar. Leave blank to allow all.' );
					$text( 'service_radius_km', 'Max distance from store (km)', 'Only checked when the customer pins a location. 0 = no limit.' );
					?>
				</table>

				<h2>One Love HQ (app-style admin)</h2>
				<table class="form-table" role="presentation">
					<?php
					$check( 'admin_skin', 'One Love colours & app look', 'Colourful app-style admin, branded login screen and a bottom tab bar on phones. Untick to return to the standard WordPress look.' );
					$check( 'hq_home', 'Open HQ instead of the WordPress dashboard', 'The classic dashboard stays available under HQ → Classic dashboard.' );
					?>
				</table>

				<h2>Delivery time promise</h2>
				<p>Shown to customers at checkout, in order emails and on the tracking page. No live countdown — riders are never rushed.</p>
				<table class="form-table" role="presentation">
					<?php
					$text( 'promise_min', 'Delivered within — from (min)', '', 'number' );
					$text( 'promise_max', 'Delivered within — to (min)', 'Default 30–45 minutes from the order.', 'number' );
					?>
				</table>

				<h2>Delivery fee (charged at checkout)</h2>
				<p>Example with the defaults: 2 km → ₹19 · 5 km → ₹31 · 10 km → ₹61 · any distance → free when the basket is ₹499 or more.</p>
				<table class="form-table" role="presentation">
					<?php
					$check( 'fee_enabled', 'Charge a delivery fee', 'Distance is the road estimate from your store to the customer\'s map pin.' );
					$text( 'fee_free_above', 'Free delivery above (₹)', 'Basket value after discounts. 0 = never free.', 'number' );
					$text( 'fee_base', 'Base fee (₹)', '', 'number' );
					$text( 'fee_base_km', 'Base fee covers first (km)', '', 'number' );
					$text( 'fee_per_km', 'Then per extra km (₹)', '', 'number' );
					$text( 'fee_max', 'Maximum fee (₹)', '0 = no cap.', 'number' );
					$text( 'fee_fallback', 'Fee when no pin (₹)', 'Used when the customer does not pin a location on the map.', 'number' );
					?>
				</table>

				<h2>Rider pay</h2>
				<table class="form-table" role="presentation">
					<?php
					$text( 'pay_per_delivery', 'Per delivered order (₹)', '', 'number' );
					$text( 'pay_per_km', 'Per km (₹)', 'Road km from store to customer. Failed deliveries are not paid.', 'number' );
					$text( 'pay_fallback_km', 'Assumed km when no pin', '', 'number' );
					$text( 'ontime_minutes', 'On-time target (minutes, order → door)', 'Used in Deliveries → Reports.', 'number' );
					?>
				</table>

				<h2>Riders &amp; assignment</h2>
				<table class="form-table" role="presentation">
					<?php
					$check( 'auto_assign', 'Auto-assign', 'New paid / COD orders go to the on-duty rider nearest the store with the fewest active deliveries.' );
					$text( 'max_active', 'Max active orders per rider', '', 'number' );
					$text( 'stale_minutes', 'Rider offline after (minutes without GPS)', '', 'number' );
					$check( 'otp_required', 'Delivery OTP', 'Rider must enter the 4-digit code the customer received before marking delivered.' );
					?>
				</table>

				<h2>WhatsApp (Meta WhatsApp Cloud API)</h2>
				<p>Create the message templates listed in the plugin's <code>readme.txt</code> in WhatsApp Manager and wait for approval before enabling.</p>
				<table class="form-table" role="presentation">
					<?php
					$check( 'wa_enabled', 'Send WhatsApp messages' );
					$text( 'wa_token', 'Permanent access token', '', 'password' );
					$text( 'wa_phone_id', 'Phone number ID' );
					$text( 'wa_lang', 'Template language code', 'Usually <code>en</code> or <code>en_US</code> — must match your templates.' );
					$text( 'wa_tpl_rider', 'Template: new order → rider' );
					$text( 'wa_tpl_out', 'Template: out for delivery → customer' );
					$text( 'wa_tpl_delivered', 'Template: delivered → customer' );
					$text( 'wa_tpl_admin', 'Template: no rider available → you' );
					$text( 'wa_admin_phone', 'Your WhatsApp number for alerts', 'With country code, e.g. 919082336907.' );
					?>
				</table>

				<?php submit_button( 'Save settings' ); ?>
			</form>

			<h2>Rider app</h2>
			<p>Riders open <a href="<?php echo esc_url( home_url( '/rider/' ) ); ?>" target="_blank"><?php echo esc_html( home_url( '/rider/' ) ); ?></a> on their phone, log in, and tap <b>Add to Home screen</b>. Create riders under <a href="<?php echo esc_url( admin_url( 'user-new.php' ) ); ?>">Users → Add New</a> with the role <b>Delivery Rider</b> and add their WhatsApp number on their profile.</p>
		</div>
		<?php
	}
}
