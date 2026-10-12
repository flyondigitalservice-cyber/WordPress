<?php
/**
 * House settings (Appearance → Hadhrami House): contact details and the patron.
 *
 * Delivery settings — Google Maps key, atelier location, 2-hour / 24-hour promises,
 * fees, riders — live in the Hadhrami Delivery plugin (Deliveries → Settings).
 *
 * @package HadhramiFashion
 */

defined( 'ABSPATH' ) || exit;

/**
 * Theme defaults.
 *
 * @return array
 */
function hf_default_settings() {
	return array(
		'phone'       => '+91 00000 00000',
		'whatsapp'    => '',
		'email'       => get_option( 'admin_email' ),
		'founder'     => 'Saquib Hadhrami',
		'established' => '',
		'close_hour'  => '21',
	);
}

/**
 * Field definitions: key => array( label, type, help ).
 *
 * @return array
 */
function hf_settings_fields() {
	return array(
		'phone'       => array( __( 'Phone', 'hadhramifashion' ), 'text', '' ),
		'whatsapp'    => array( __( 'WhatsApp number', 'hadhramifashion' ), 'text', __( 'International format without + or spaces, e.g. 919876543210. Shows the “Speak to a tailor” button.', 'hadhramifashion' ) ),
		'email'       => array( __( 'Email', 'hadhramifashion' ), 'email', '' ),
		'founder'     => array( __( 'Patron name', 'hadhramifashion' ), 'text', '' ),
		'established' => array( __( 'Established (year)', 'hadhramifashion' ), 'text', __( 'Optional. Shown as “Est. YEAR” when filled in.', 'hadhramifashion' ) ),
		'close_hour'  => array( __( 'Atelier closes (hour, 0–23)', 'hadhramifashion' ), 'number', __( 'Shown in the footer. Opening hour comes from Deliveries → Settings.', 'hadhramifashion' ) ),
	);
}

add_action(
	'admin_menu',
	function () {
		add_theme_page( __( 'Hadhrami House', 'hadhramifashion' ), __( 'Hadhrami House', 'hadhramifashion' ), 'manage_options', 'hf-settings', 'hf_render_settings_page' );
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
	foreach ( hf_settings_fields() as $key => $field ) {
		if ( ! isset( $input[ $key ] ) ) {
			continue;
		}
		$value = wp_unslash( $input[ $key ] );
		switch ( $field[1] ) {
			case 'number':
				$clean[ $key ] = '' === trim( $value ) ? '' : (string) absint( $value );
				break;
			case 'email':
				$clean[ $key ] = sanitize_email( $value );
				break;
			default:
				$clean[ $key ] = sanitize_text_field( $value );
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
		<h1><?php esc_html_e( 'Hadhrami House', 'hadhramifashion' ); ?></h1>
		<p>
			<?php esc_html_e( 'Contact details and the patron shown across the site.', 'hadhramifashion' ); ?>
			<?php if ( class_exists( 'HFD_Settings' ) ) : ?>
				<?php
				printf(
					/* translators: %s: link */
					esc_html__( 'Delivery, Google Maps and the atelier location are in %s.', 'hadhramifashion' ),
					'<a href="' . esc_url( admin_url( 'admin.php?page=hfd-settings' ) ) . '">' . esc_html__( 'Deliveries → Settings', 'hadhramifashion' ) . '</a>'
				);
				?>
			<?php endif; ?>
		</p>
		<form method="post" action="options.php">
			<?php settings_fields( 'hf_settings_group' ); ?>
			<table class="form-table" role="presentation">
				<?php foreach ( hf_settings_fields() as $key => $field ) : ?>
					<tr>
						<th scope="row"><label for="hf_<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $field[0] ); ?></label></th>
						<td>
							<input type="<?php echo esc_attr( $field[1] ); ?>" class="regular-text" id="hf_<?php echo esc_attr( $key ); ?>" name="hf_settings[<?php echo esc_attr( $key ); ?>]" value="<?php echo esc_attr( isset( $saved[ $key ] ) ? $saved[ $key ] : '' ); ?>" placeholder="<?php echo esc_attr( hf_default_settings()[ $key ] ); ?>">
							<?php if ( $field[2] ) : ?>
								<p class="description"><?php echo esc_html( $field[2] ); ?></p>
							<?php endif; ?>
						</td>
					</tr>
				<?php endforeach; ?>
			</table>
			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}
