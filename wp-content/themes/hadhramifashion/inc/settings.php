<?php
/**
 * Atelier & delivery settings (Hadhrami → Atelier Settings).
 *
 * @package HadhramiFashion
 */

defined( 'ABSPATH' ) || exit;

/**
 * Default settings. The atelier location defaults to Hyderabad's old city —
 * change it to the real shop address before going live.
 *
 * @return array
 */
function hf_default_settings() {
	return array(
		'maps_api_key'        => '',
		'store_name'          => 'Hadhrami Fashion Atelier',
		'store_address'       => 'Old City, Hyderabad',
		'store_lat'           => '17.3616',
		'store_lng'           => '78.4747',
		'express_radius_km'   => '12',
		'tailored_radius_km'  => '30',
		'express_fee'         => '199',
		'tailored_fee'        => '0',
		'standard_fee'        => '0',
		'per_km_fee'          => '15',
		'free_km'             => '5',
		'open_hour'           => '9',
		'close_hour'          => '21',
		'express_cutoff_hour' => '19',
		'currency_symbol'     => '₹',
		'phone'               => '+91 00000 00000',
		'whatsapp'            => '',
		'email'               => get_option( 'admin_email' ),
		'established'         => '',
		'founder'             => 'Saquib Hadhrami',
	);
}

/**
 * Field definitions grouped by section: key => array( label, type, help ).
 *
 * @return array
 */
function hf_settings_fields() {
	return array(
		'Google Maps'      => array(
			'maps_api_key' => array( 'Google Maps API key', 'text', 'Enable Maps JavaScript API, Places API (New), Geocoding API and Routes API for this key, and restrict it to your domain.' ),
		),
		'Atelier'          => array(
			'store_name'    => array( 'Atelier name', 'text', '' ),
			'store_address' => array( 'Atelier address', 'text', 'Shown on the map and in the footer.' ),
			'store_lat'     => array( 'Atelier latitude', 'text', 'Right-click your shop in Google Maps to copy its coordinates.' ),
			'store_lng'     => array( 'Atelier longitude', 'text', '' ),
			'phone'         => array( 'Phone', 'text', '' ),
			'whatsapp'      => array( 'WhatsApp number', 'text', 'International format without + or spaces, e.g. 919876543210.' ),
			'email'         => array( 'Email', 'email', '' ),
			'founder'       => array( 'Patron name', 'text', '' ),
			'established'   => array( 'Established (year)', 'text', 'Optional. Shown as “Est. YEAR” when filled in.' ),
		),
		'Delivery promise' => array(
			'express_radius_km'   => array( '2-hour express radius (km)', 'number', 'Ready-to-wear shirts reach addresses within this road distance in 2 hours.' ),
			'tailored_radius_km'  => array( '24-hour tailoring radius (km)', 'number', 'Made-to-measure suits and shirts delivered within 24 hours inside this radius.' ),
			'express_fee'         => array( '2-hour express fee', 'number', '' ),
			'tailored_fee'        => array( '24-hour tailoring delivery fee', 'number', '' ),
			'standard_fee'        => array( 'Standard delivery fee', 'number', '' ),
			'free_km'             => array( 'Free kilometres', 'number', 'Distance included before the per-km charge applies.' ),
			'per_km_fee'          => array( 'Per-km charge beyond free kilometres', 'number', 'Applied to express orders only.' ),
			'open_hour'           => array( 'Atelier opens (hour, 0–23)', 'number', '' ),
			'close_hour'          => array( 'Atelier closes (hour, 0–23)', 'number', '' ),
			'express_cutoff_hour' => array( 'Last express order (hour, 0–23)', 'number', 'Orders after this roll to the next morning’s first slot.' ),
			'currency_symbol'     => array( 'Currency symbol', 'text', '' ),
		),
	);
}

add_action(
	'admin_menu',
	function () {
		add_menu_page(
			__( 'Hadhrami', 'hadhramifashion' ),
			__( 'Hadhrami', 'hadhramifashion' ),
			'manage_options',
			'hf-settings',
			'hf_render_settings_page',
			'dashicons-store',
			3
		);
		add_submenu_page( 'hf-settings', __( 'Atelier Settings', 'hadhramifashion' ), __( 'Atelier Settings', 'hadhramifashion' ), 'manage_options', 'hf-settings', 'hf_render_settings_page' );
	}
);

add_action(
	'admin_init',
	function () {
		register_setting(
			'hf_settings_group',
			'hf_settings',
			array(
				'type'              => 'array',
				'sanitize_callback' => 'hf_sanitize_settings',
			)
		);
	}
);

/**
 * Sanitize the settings array.
 *
 * @param array $input Raw input.
 * @return array
 */
function hf_sanitize_settings( $input ) {
	$clean = array();
	foreach ( hf_settings_fields() as $fields ) {
		foreach ( $fields as $key => $field ) {
			if ( ! isset( $input[ $key ] ) ) {
				continue;
			}
			$value = wp_unslash( $input[ $key ] );
			switch ( $field[1] ) {
				case 'number':
					$clean[ $key ] = '' === trim( $value ) ? '' : (string) floatval( $value );
					break;
				case 'email':
					$clean[ $key ] = sanitize_email( $value );
					break;
				default:
					$clean[ $key ] = sanitize_text_field( $value );
			}
		}
	}
	return $clean;
}

/**
 * Render the settings screen.
 */
function hf_render_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$saved = get_option( 'hf_settings', array() );
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Hadhrami Fashion — Atelier Settings', 'hadhramifashion' ); ?></h1>
		<p><?php esc_html_e( 'These settings drive the Google Maps delivery checker, checkout and live order tracking.', 'hadhramifashion' ); ?></p>
		<form method="post" action="options.php">
			<?php settings_fields( 'hf_settings_group' ); ?>
			<?php foreach ( hf_settings_fields() as $section => $fields ) : ?>
				<h2 class="title"><?php echo esc_html( $section ); ?></h2>
				<table class="form-table" role="presentation">
					<?php foreach ( $fields as $key => $field ) : ?>
						<tr>
							<th scope="row"><label for="hf_<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $field[0] ); ?></label></th>
							<td>
								<input
									type="<?php echo 'number' === $field[1] ? 'number' : esc_attr( $field[1] ); ?>"
									<?php echo 'number' === $field[1] ? 'step="any"' : ''; ?>
									class="regular-text"
									id="hf_<?php echo esc_attr( $key ); ?>"
									name="hf_settings[<?php echo esc_attr( $key ); ?>]"
									value="<?php echo esc_attr( isset( $saved[ $key ] ) ? $saved[ $key ] : '' ); ?>"
									placeholder="<?php echo esc_attr( hf_default_settings()[ $key ] ); ?>"
								>
								<?php if ( $field[2] ) : ?>
									<p class="description"><?php echo esc_html( $field[2] ); ?></p>
								<?php endif; ?>
							</td>
						</tr>
					<?php endforeach; ?>
				</table>
			<?php endforeach; ?>
			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}
