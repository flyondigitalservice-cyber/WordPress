<?php
/**
 * Settings page and option access.
 *
 * @package Invoice_Packing_Slip_Maker
 */

defined( 'ABSPATH' ) || exit;

/**
 * Stores everything in one option, `ipsm_settings`, plus `ipsm_next_invoice_number`
 * which is kept separate so it can be incremented atomically.
 */
class IPSM_Settings {

	const OPTION      = 'ipsm_settings';
	const NUMBER_OPT  = 'ipsm_next_invoice_number';
	const PAGE        = 'ipsm-settings';

	/**
	 * Hook registration.
	 */
	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'menu' ), 60 );
		add_action( 'admin_init', array( __CLASS__, 'register' ) );
	}

	/**
	 * Default values.
	 *
	 * @return array
	 */
	public static function defaults() {
		return array(
			'shop_name'          => get_bloginfo( 'name' ),
			'shop_address'       => '',
			'shop_phone'         => '',
			'shop_email'         => get_option( 'admin_email' ),
			'tax_id'             => '',
			'logo_id'            => 0,
			'accent_color'       => '#1f2937',
			'paper_size'         => 'A5',
			'number_prefix'      => 'INV-',
			'number_suffix'      => '',
			'number_padding'     => 5,
			'reset_yearly'       => 'no',
			'invoice_date'       => 'order',
			'attach_to'          => array( 'customer_completed_order', 'customer_invoice' ),
			'auto_send_status'   => '',
			'show_sku'           => 'yes',
			'show_shipping_addr' => 'yes',
			'show_email_phone'   => 'yes',
			'notes'              => '',
			'footer'             => __( 'Thank you for your business!', 'invoice-packing-slip-maker' ),
			'customer_download'  => 'yes',
			'slip_show_prices'   => 'no',
			'label_size'         => 'paper',
		);
	}

	/**
	 * Read one setting.
	 *
	 * @param string $key Setting key.
	 * @return mixed
	 */
	public static function get( $key ) {
		$stored   = get_option( self::OPTION, array() );
		$defaults = self::defaults();
		$stored   = is_array( $stored ) ? $stored : array();
		if ( array_key_exists( $key, $stored ) ) {
			return $stored[ $key ];
		}
		return isset( $defaults[ $key ] ) ? $defaults[ $key ] : null;
	}

	/**
	 * Boolean helper for yes/no settings.
	 *
	 * @param string $key Setting key.
	 * @return bool
	 */
	public static function enabled( $key ) {
		return 'yes' === self::get( $key );
	}

	/**
	 * Add the submenu under WooCommerce.
	 */
	public static function menu() {
		add_submenu_page(
			'woocommerce',
			__( 'Invoices & Packing Slips', 'invoice-packing-slip-maker' ),
			__( 'Invoices & Slips', 'invoice-packing-slip-maker' ),
			'manage_woocommerce',
			self::PAGE,
			array( __CLASS__, 'render_page' )
		);
	}

	/**
	 * Register the option with sanitization.
	 */
	public static function register() {
		register_setting(
			'ipsm_settings_group',
			self::OPTION,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( __CLASS__, 'sanitize' ),
			)
		);
		register_setting(
			'ipsm_settings_group',
			self::NUMBER_OPT,
			array(
				'type'              => 'integer',
				'sanitize_callback' => static function ( $v ) {
					return max( 1, absint( $v ) );
				},
			)
		);
	}

	/**
	 * Sanitize submitted settings.
	 *
	 * @param mixed $input Raw input.
	 * @return array
	 */
	public static function sanitize( $input ) {
		$input = is_array( $input ) ? $input : array();
		$out   = array();

		foreach ( array( 'shop_name', 'shop_phone', 'tax_id', 'number_prefix', 'number_suffix' ) as $k ) {
			$out[ $k ] = isset( $input[ $k ] ) ? sanitize_text_field( wp_unslash( $input[ $k ] ) ) : '';
		}
		foreach ( array( 'shop_address', 'notes', 'footer' ) as $k ) {
			$out[ $k ] = isset( $input[ $k ] ) ? sanitize_textarea_field( wp_unslash( $input[ $k ] ) ) : '';
		}
		$out['shop_email']     = isset( $input['shop_email'] ) ? sanitize_email( wp_unslash( $input['shop_email'] ) ) : '';
		$out['logo_id']        = isset( $input['logo_id'] ) ? absint( $input['logo_id'] ) : 0;
		$color                 = isset( $input['accent_color'] ) ? sanitize_hex_color( wp_unslash( $input['accent_color'] ) ) : '';
		$out['accent_color']   = $color ? $color : '#1f2937';
		$out['paper_size']     = ( isset( $input['paper_size'] ) && in_array( $input['paper_size'], array( 'A5', 'A4', 'Letter' ), true ) ) ? $input['paper_size'] : 'A5';
		$out['label_size']     = ( isset( $input['label_size'] ) && in_array( $input['label_size'], array( 'paper', '4x6', '100x150', 'A6' ), true ) ) ? $input['label_size'] : 'paper';
		$out['number_padding'] = isset( $input['number_padding'] ) ? min( 10, absint( $input['number_padding'] ) ) : 5;
		$out['invoice_date']   = ( isset( $input['invoice_date'] ) && 'created' === $input['invoice_date'] ) ? 'created' : 'order';

		foreach ( array( 'reset_yearly', 'show_sku', 'show_shipping_addr', 'show_email_phone', 'customer_download', 'slip_show_prices' ) as $k ) {
			$out[ $k ] = ! empty( $input[ $k ] ) ? 'yes' : 'no';
		}

		$valid_emails     = array_keys( self::attachable_emails() );
		$out['attach_to'] = array();
		if ( ! empty( $input['attach_to'] ) && is_array( $input['attach_to'] ) ) {
			$out['attach_to'] = array_values( array_intersect( array_map( 'sanitize_key', $input['attach_to'] ), $valid_emails ) );
		}

		$statuses                = array_keys( wc_get_order_statuses() );
		$status                  = isset( $input['auto_send_status'] ) ? sanitize_text_field( wp_unslash( $input['auto_send_status'] ) ) : '';
		$out['auto_send_status'] = in_array( $status, $statuses, true ) ? $status : '';

		return $out;
	}

	/**
	 * Customer-facing WooCommerce emails the invoice PDF can be attached to.
	 *
	 * @return array id => title
	 */
	public static function attachable_emails() {
		$list = array(
			'new_order'                 => __( 'New order (admin)', 'invoice-packing-slip-maker' ),
			'customer_on_hold_order'    => __( 'Order on-hold', 'invoice-packing-slip-maker' ),
			'customer_processing_order' => __( 'Processing order', 'invoice-packing-slip-maker' ),
			'customer_completed_order'  => __( 'Completed order', 'invoice-packing-slip-maker' ),
			'customer_invoice'          => __( 'Customer invoice / Order details', 'invoice-packing-slip-maker' ),
			'customer_refunded_order'   => __( 'Refunded order', 'invoice-packing-slip-maker' ),
		);
		return apply_filters( 'ipsm_attachable_emails', $list );
	}

	/**
	 * Render the settings page.
	 */
	public static function render_page() {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}
		wp_enqueue_media();
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'ipsm-admin', IPSM_URL . 'assets/js/admin.js', array( 'jquery', 'wp-color-picker' ), IPSM_VERSION, true );
		wp_enqueue_style( 'ipsm-admin', IPSM_URL . 'assets/css/admin.css', array(), IPSM_VERSION );

		$name     = self::OPTION;
		$logo_id  = (int) self::get( 'logo_id' );
		$logo_url = $logo_id ? wp_get_attachment_image_url( $logo_id, 'medium' ) : '';
		?>
		<div class="wrap ipsm-settings">
			<h1><?php esc_html_e( 'Invoices & Packing Slips', 'invoice-packing-slip-maker' ); ?></h1>
			<form method="post" action="options.php">
				<?php settings_fields( 'ipsm_settings_group' ); ?>

				<h2><?php esc_html_e( 'Shop details', 'invoice-packing-slip-maker' ); ?></h2>
				<table class="form-table" role="presentation">
					<tr>
						<th scope="row"><?php esc_html_e( 'Logo', 'invoice-packing-slip-maker' ); ?></th>
						<td>
							<input type="hidden" id="ipsm-logo-id" name="<?php echo esc_attr( $name ); ?>[logo_id]" value="<?php echo esc_attr( $logo_id ); ?>" />
							<div id="ipsm-logo-preview"><?php echo $logo_url ? '<img src="' . esc_url( $logo_url ) . '" alt="" />' : ''; ?></div>
							<button type="button" class="button" id="ipsm-logo-select"><?php esc_html_e( 'Choose logo', 'invoice-packing-slip-maker' ); ?></button>
							<button type="button" class="button-link-delete" id="ipsm-logo-remove"<?php echo $logo_id ? '' : ' style="display:none"'; ?>><?php esc_html_e( 'Remove', 'invoice-packing-slip-maker' ); ?></button>
							<p class="description"><?php esc_html_e( 'PNG or JPEG. Shown on invoices, packing slips and in PDFs.', 'invoice-packing-slip-maker' ); ?></p>
						</td>
					</tr>
					<?php
					self::text_row( 'shop_name', __( 'Shop name', 'invoice-packing-slip-maker' ) );
					self::textarea_row( 'shop_address', __( 'Shop address', 'invoice-packing-slip-maker' ), __( 'One line per row. Also used as the return address on shipping labels.', 'invoice-packing-slip-maker' ) );
					self::text_row( 'shop_phone', __( 'Phone', 'invoice-packing-slip-maker' ) );
					self::text_row( 'shop_email', __( 'Email', 'invoice-packing-slip-maker' ) );
					self::text_row( 'tax_id', __( 'Tax / VAT / GST number', 'invoice-packing-slip-maker' ) );
					?>
					<tr>
						<th scope="row"><label for="ipsm-accent"><?php esc_html_e( 'Accent colour', 'invoice-packing-slip-maker' ); ?></label></th>
						<td><input type="text" id="ipsm-accent" class="ipsm-color" name="<?php echo esc_attr( $name ); ?>[accent_color]" value="<?php echo esc_attr( self::get( 'accent_color' ) ); ?>" /></td>
					</tr>
					<?php
					self::select_row(
						'paper_size',
						__( 'Paper size', 'invoice-packing-slip-maker' ),
						array(
							'A5'     => __( 'A5 (148 × 210 mm) — compact, small fonts', 'invoice-packing-slip-maker' ),
							'A4'     => 'A4',
							'Letter' => 'US Letter',
						),
						__( 'Used for invoices and packing slips (and shipping labels set to "Same as invoice").', 'invoice-packing-slip-maker' )
					);
					?>
				</table>

				<h2><?php esc_html_e( 'Invoice numbering', 'invoice-packing-slip-maker' ); ?></h2>
				<table class="form-table" role="presentation">
					<?php
					self::text_row( 'number_prefix', __( 'Prefix', 'invoice-packing-slip-maker' ), __( 'Placeholders: {Y} year, {m} month.', 'invoice-packing-slip-maker' ) );
					self::text_row( 'number_suffix', __( 'Suffix', 'invoice-packing-slip-maker' ) );
					self::text_row( 'number_padding', __( 'Digits (zero padding)', 'invoice-packing-slip-maker' ), '', 'number' );
					?>
					<tr>
						<th scope="row"><label for="ipsm-next"><?php esc_html_e( 'Next invoice number', 'invoice-packing-slip-maker' ); ?></label></th>
						<td><input type="number" min="1" id="ipsm-next" name="<?php echo esc_attr( self::NUMBER_OPT ); ?>" value="<?php echo esc_attr( (int) get_option( self::NUMBER_OPT, 1 ) ); ?>" /></td>
					</tr>
					<?php
					self::checkbox_row( 'reset_yearly', __( 'Restart numbering at 1 every new year', 'invoice-packing-slip-maker' ) );
					self::select_row(
						'invoice_date',
						__( 'Invoice date', 'invoice-packing-slip-maker' ),
						array(
							'order'   => __( 'Order date', 'invoice-packing-slip-maker' ),
							'created' => __( 'Date the invoice is first created', 'invoice-packing-slip-maker' ),
						)
					);
					?>
				</table>

				<h2><?php esc_html_e( 'Emailing invoices', 'invoice-packing-slip-maker' ); ?></h2>
				<table class="form-table" role="presentation">
					<tr>
						<th scope="row"><?php esc_html_e( 'Attach PDF invoice to', 'invoice-packing-slip-maker' ); ?></th>
						<td>
							<?php
							$selected = (array) self::get( 'attach_to' );
							foreach ( self::attachable_emails() as $id => $label ) {
								printf(
									'<label style="display:block;margin-bottom:4px"><input type="checkbox" name="%1$s[attach_to][]" value="%2$s" %3$s /> %4$s</label>',
									esc_attr( $name ),
									esc_attr( $id ),
									checked( in_array( $id, $selected, true ), true, false ),
									esc_html( $label )
								);
							}
							?>
							<p class="description"><?php esc_html_e( 'WooCommerce emails that should carry the invoice PDF as an attachment.', 'invoice-packing-slip-maker' ); ?></p>
						</td>
					</tr>
					<?php
					$statuses = array( '' => __( '— Never (send manually) —', 'invoice-packing-slip-maker' ) ) + wc_get_order_statuses();
					self::select_row( 'auto_send_status', __( 'Automatically email invoice when order becomes', 'invoice-packing-slip-maker' ), $statuses, __( 'Sends the dedicated "Invoice (PDF)" email once per order.', 'invoice-packing-slip-maker' ) );
					self::checkbox_row( 'customer_download', __( 'Let customers download their invoice from My Account', 'invoice-packing-slip-maker' ) );
					?>
				</table>

				<h2><?php esc_html_e( 'Document content', 'invoice-packing-slip-maker' ); ?></h2>
				<table class="form-table" role="presentation">
					<?php
					self::checkbox_row( 'show_sku', __( 'Show product SKU', 'invoice-packing-slip-maker' ) );
					self::checkbox_row( 'show_shipping_addr', __( 'Show shipping address on invoice', 'invoice-packing-slip-maker' ) );
					self::checkbox_row( 'show_email_phone', __( 'Show customer email and phone', 'invoice-packing-slip-maker' ) );
					self::checkbox_row( 'slip_show_prices', __( 'Show prices on packing slips', 'invoice-packing-slip-maker' ) );
					self::select_row(
						'label_size',
						__( 'Shipping label size', 'invoice-packing-slip-maker' ),
						array(
							'paper'   => __( 'Same as invoice paper size', 'invoice-packing-slip-maker' ),
							'4x6'     => __( '4 × 6 in (thermal label)', 'invoice-packing-slip-maker' ),
							'100x150' => __( '100 × 150 mm', 'invoice-packing-slip-maker' ),
							'A6'      => 'A6',
						)
					);
					self::textarea_row( 'notes', __( 'Invoice terms / notes', 'invoice-packing-slip-maker' ), __( 'E.g. payment terms or return policy.', 'invoice-packing-slip-maker' ) );
					self::textarea_row( 'footer', __( 'Footer text', 'invoice-packing-slip-maker' ) );
					?>
				</table>

				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}

	/**
	 * Text input row.
	 *
	 * @param string $key   Key.
	 * @param string $label Label.
	 * @param string $desc  Description.
	 * @param string $type  Input type.
	 */
	private static function text_row( $key, $label, $desc = '', $type = 'text' ) {
		$id = 'ipsm-' . $key;
		?>
		<tr>
			<th scope="row"><label for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $label ); ?></label></th>
			<td>
				<input type="<?php echo esc_attr( $type ); ?>" class="regular-text" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( self::OPTION . '[' . $key . ']' ); ?>" value="<?php echo esc_attr( self::get( $key ) ); ?>" />
				<?php if ( $desc ) : ?>
					<p class="description"><?php echo esc_html( $desc ); ?></p>
				<?php endif; ?>
			</td>
		</tr>
		<?php
	}

	/**
	 * Textarea row.
	 *
	 * @param string $key   Key.
	 * @param string $label Label.
	 * @param string $desc  Description.
	 */
	private static function textarea_row( $key, $label, $desc = '' ) {
		$id = 'ipsm-' . $key;
		?>
		<tr>
			<th scope="row"><label for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $label ); ?></label></th>
			<td>
				<textarea class="large-text" rows="4" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( self::OPTION . '[' . $key . ']' ); ?>"><?php echo esc_textarea( self::get( $key ) ); ?></textarea>
				<?php if ( $desc ) : ?>
					<p class="description"><?php echo esc_html( $desc ); ?></p>
				<?php endif; ?>
			</td>
		</tr>
		<?php
	}

	/**
	 * Checkbox row.
	 *
	 * @param string $key   Key.
	 * @param string $label Label.
	 */
	private static function checkbox_row( $key, $label ) {
		?>
		<tr>
			<th scope="row"></th>
			<td>
				<label><input type="checkbox" name="<?php echo esc_attr( self::OPTION . '[' . $key . ']' ); ?>" value="yes" <?php checked( self::enabled( $key ) ); ?> /> <?php echo esc_html( $label ); ?></label>
			</td>
		</tr>
		<?php
	}

	/**
	 * Select row.
	 *
	 * @param string $key     Key.
	 * @param string $label   Label.
	 * @param array  $options Options.
	 * @param string $desc    Description.
	 */
	private static function select_row( $key, $label, $options, $desc = '' ) {
		$id      = 'ipsm-' . $key;
		$current = (string) self::get( $key );
		?>
		<tr>
			<th scope="row"><label for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $label ); ?></label></th>
			<td>
				<select id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( self::OPTION . '[' . $key . ']' ); ?>">
					<?php foreach ( $options as $value => $text ) : ?>
						<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $current, (string) $value ); ?>><?php echo esc_html( $text ); ?></option>
					<?php endforeach; ?>
				</select>
				<?php if ( $desc ) : ?>
					<p class="description"><?php echo esc_html( $desc ); ?></p>
				<?php endif; ?>
			</td>
		</tr>
		<?php
	}
}
