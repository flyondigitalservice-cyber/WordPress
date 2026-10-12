<?php
/**
 * Settings storage and the Deliveries → Settings screen.
 *
 * @package HadhramiDelivery
 */

defined( 'ABSPATH' ) || exit;

class HFD_Settings {

	const OPTION = 'hfd_delivery_settings';

	/** Fields that hold secrets: never echoed back into the form. */
	const SECRETS = array( 'wa_token' );

	public static function defaults() {
		return array(
			'maps_key'          => '',
			'store_name'        => 'Hadhrami Fashion Atelier',
			'store_address'     => 'Old City, Hyderabad, Telangana',
			'store_lat'         => '17.3616',
			'store_lng'         => '78.4747',
			'store_phone'       => '',
			'postcode_prefixes' => '',
			'service_radius_km' => '30',
			'express_radius_km' => '12',
			'express_minutes'   => '120',
			'express_fee'       => '149',
			'tailored_hours'    => '24',
			'open_hour'         => '9',
			'express_cutoff_hour' => '19',
			'checkout_pin'      => 'yes',
			'auto_assign'       => 'yes',
			'max_active'        => '3',
			'stale_minutes'     => '10',
			'otp_required'      => 'yes',
			'ontime_minutes'    => '120',
			'admin_skin'        => 'yes',
			'hq_home'           => 'yes',
			'fee_enabled'       => 'yes',
			'fee_free_above'    => '4999',
			'fee_base'          => '99',
			'fee_base_km'       => '5',
			'fee_per_km'        => '10',
			'fee_max'           => '249',
			'fee_fallback'      => '149',
			'pay_per_delivery'  => '25',
			'pay_per_km'        => '5',
			'pay_fallback_km'   => '4',
			'wa_enabled'        => 'no',
			'wa_token'          => '',
			'wa_phone_id'       => '',
			'wa_lang'           => 'en',
			'wa_tpl_rider'      => 'hfd_rider_new_order',
			'wa_tpl_out'        => 'hfd_out_for_delivery',
			'wa_tpl_delivered'  => 'hfd_order_delivered',
			'wa_tpl_admin'      => 'hfd_no_rider_alert',
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
		$numbers = array( 'store_lat', 'store_lng', 'service_radius_km', 'express_radius_km', 'express_minutes', 'express_fee', 'tailored_hours', 'open_hour', 'express_cutoff_hour', 'ontime_minutes', 'fee_free_above', 'fee_base', 'fee_base_km', 'fee_per_km', 'fee_max', 'fee_fallback', 'pay_per_delivery', 'pay_per_km', 'pay_fallback_km' );
		foreach ( $numbers as $num ) {
			$clean[ $num ] = is_numeric( $clean[ $num ] ) ? $clean[ $num ] : $current[ $num ];
			if ( ! in_array( $num, array( 'store_lat', 'store_lng' ), true ) ) {
				$clean[ $num ] = (string) max( 0, (float) $clean[ $num ] );
			}
		}
		$clean['open_hour']           = (string) min( 23, (int) $clean['open_hour'] );
		$clean['express_cutoff_hour'] = (string) min( 23, (int) $clean['express_cutoff_hour'] );
		$clean['max_active']    = (string) max( 1, absint( $clean['max_active'] ) );
		$clean['stale_minutes'] = (string) max( 2, absint( $clean['stale_minutes'] ) );
		update_option( self::OPTION, $clean, false );
	}

	public static function render_page() {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}
		if ( isset( $_POST['hfd_settings_nonce'] ) && wp_verify_nonce( sanitize_key( $_POST['hfd_settings_nonce'] ), 'hfd_save_settings' ) ) {
			self::save( isset( $_POST['hfd'] ) ? (array) $_POST['hfd'] : array() ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
			echo '<div class="notice notice-success is-dismissible"><p>Settings saved.</p></div>';
		}
		$s = self::all();

		$text = function ( $key, $label, $help = '', $type = 'text' ) use ( $s ) {
			$is_secret = in_array( $key, self::SECRETS, true );
			$value     = $is_secret ? '' : $s[ $key ];
			$ph        = ( $is_secret && $s[ $key ] ) ? 'Saved — leave blank to keep' : '';
			printf(
				'<tr><th scope="row"><label for="hfd-%1$s">%2$s</label></th><td><input type="%5$s" class="regular-text" id="hfd-%1$s" name="hfd[%1$s]" value="%3$s" placeholder="%6$s" autocomplete="off">%4$s</td></tr>',
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
				'<tr><th scope="row">%2$s</th><td><label><input type="checkbox" name="hfd[%1$s]" value="1" %3$s> Enabled</label>%4$s</td></tr>',
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
				<?php wp_nonce_field( 'hfd_save_settings', 'hfd_settings_nonce' ); ?>

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
					$text( 'postcode_prefixes', 'Allowed PIN codes', 'Comma-separated prefixes, e.g. <code>500</code> for Hyderabad. Leave blank to allow all.' );
					$text( 'service_radius_km', 'Delivery area — max distance from the atelier (km)', 'Tailored 24-hour delivery reaches this far. Only checked when the customer pins a location. 0 = no limit.' );
					?>
				</table>

				<h2>Hadhrami HQ (app-style admin)</h2>
				<table class="form-table" role="presentation">
					<?php
					$check( 'admin_skin', 'Hadhrami colours & app look', 'Colourful app-style admin, branded login screen and a bottom tab bar on phones. Untick to return to the standard WordPress look.' );
					$check( 'hq_home', 'Open HQ instead of the WordPress dashboard', 'The classic dashboard stays available under HQ → Classic dashboard.' );
					?>
				</table>

				<h2>Delivery promises</h2>
				<p>Customers choose at checkout. The due time is shown at checkout, in emails, in the rider app and on the tracking page.</p>
				<table class="form-table" role="presentation">
					<?php
					$text( 'express_minutes', 'Express — delivered within (minutes)', 'Default 120 = two hours. Only for bags made entirely of products ticked “2-hour delivery stock”.', 'number' );
					$text( 'express_radius_km', 'Express — max distance (km)', 'Road estimate from the atelier to the customer’s pin. Express needs a map pin.', 'number' );
					$text( 'express_fee', 'Express surcharge (₹)', 'Added on top of the distance fee. Always charged, even when delivery is otherwise free.', 'number' );
					$text( 'open_hour', 'Atelier opens (hour, 0–23)', '', 'number' );
					$text( 'express_cutoff_hour', 'Last express order (hour, 0–23)', 'Later orders are due two hours after the next opening.', 'number' );
					$text( 'tailored_hours', 'Made to measure — delivered within (hours)', 'Default 24. These orders wait “In the atelier” until you press <b>Ready — send rider</b> on the dispatch board.', 'number' );
					?>
				</table>

				<h2>Delivery fee (charged at checkout)</h2>
				<p>Example with the defaults: 3 km → ₹99 · 10 km → ₹149 · 20 km → ₹249 · free when the bag is ₹4,999 or more (the express surcharge still applies).</p>
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
					$text( 'ontime_minutes', 'On-time target when an order has no promise (minutes)', 'Reports count an order on time when it reaches the door by its promised time.', 'number' );
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
					$text( 'wa_admin_phone', 'Your WhatsApp number for alerts', 'With country code, e.g. 919876543210.' );
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
