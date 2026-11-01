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
	 * Settings page hook suffix.
	 *
	 * @var string
	 */
	private static $hook = '';

	/**
	 * Hook registration.
	 */
	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'menu' ), 60 );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'assets' ) );
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
		self::$hook = (string) add_submenu_page(
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
			$out[ $k ] = ( isset( $input[ $k ] ) && in_array( $input[ $k ], array( 'yes', '1', 'on', 1, true ), true ) ) ? 'yes' : 'no';
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
	 * Enqueue settings page assets in the <head>, only on our page.
	 *
	 * @param string $hook Current admin page hook.
	 */
	public static function assets( $hook ) {
		if ( self::$hook !== $hook ) {
			return;
		}
		wp_enqueue_media();
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_style( 'ipsm-admin', IPSM_URL . 'assets/css/admin.css', array(), IPSM_VERSION );
		wp_enqueue_script( 'ipsm-admin', IPSM_URL . 'assets/js/admin.js', array( 'jquery', 'wp-color-picker' ), IPSM_VERSION, true );
		wp_localize_script( 'ipsm-admin', 'ipsmAdmin', IPSM_Admin::script_data() );
	}

	/**
	 * Most recent order, used for the live preview.
	 *
	 * @return int
	 */
	private static function preview_order_id() {
		$ids = wc_get_orders(
			array(
				'limit'   => 1,
				'orderby' => 'date',
				'order'   => 'DESC',
				'type'    => 'shop_order',
				'return'  => 'ids',
			)
		);
		return $ids ? (int) $ids[0] : 0;
	}

	/**
	 * Whether WooCommerce's "Invoice (PDF)" email is switched on.
	 *
	 * @return bool
	 */
	public static function invoice_email_enabled() {
		$emails = WC()->mailer()->get_emails();
		return ! empty( $emails['IPSM_Email_Invoice'] ) && $emails['IPSM_Email_Invoice']->is_enabled();
	}

	/**
	 * Render the settings page.
	 */
	public static function render_page() {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}

		$logo_id    = (int) self::get( 'logo_id' );
		$logo_url   = $logo_id ? wp_get_attachment_image_url( $logo_id, 'medium' ) : '';
		$preview_id = self::preview_order_id();
		$email_on   = self::invoice_email_enabled();
		$email_url  = admin_url( 'admin.php?page=wc-settings&tab=email&section=ipsm_email_invoice' );
		$orders_url = function_exists( 'wc_get_container' ) && class_exists( '\Automattic\WooCommerce\Utilities\OrderUtil' ) && \Automattic\WooCommerce\Utilities\OrderUtil::custom_orders_table_usage_is_enabled()
			? admin_url( 'admin.php?page=wc-orders' )
			: admin_url( 'edit.php?post_type=shop_order' );

		$steps = array(
			array( '' !== trim( (string) self::get( 'shop_address' ) ), __( 'Add your shop address', 'invoice-packing-slip-maker' ), 'shop' ),
			array( (bool) $logo_id, __( 'Upload your logo', 'invoice-packing-slip-maker' ), 'shop' ),
			array( $email_on, __( 'Invoice email is switched on', 'invoice-packing-slip-maker' ), 'emails' ),
		);
		$done = count( array_filter( wp_list_pluck( $steps, 0 ) ) );
		?>
		<div class="wrap ipsm-wrap">
			<h1 class="screen-reader-text"><?php esc_html_e( 'Invoices & Packing Slips', 'invoice-packing-slip-maker' ); ?></h1>

			<header class="ipsm-hero">
				<div class="ipsm-hero-icon"><span class="dashicons dashicons-media-spreadsheet"></span></div>
				<div class="ipsm-hero-text">
					<h2><?php esc_html_e( 'Invoices & Packing Slips', 'invoice-packing-slip-maker' ); ?></h2>
					<p><?php esc_html_e( 'Set up how your invoices, packing slips and shipping labels look. Print, download and email them from any order.', 'invoice-packing-slip-maker' ); ?></p>
				</div>
				<a class="ipsm-btn ipsm-btn-ghost" href="<?php echo esc_url( $orders_url ); ?>"><span class="dashicons dashicons-list-view"></span><?php esc_html_e( 'Go to orders', 'invoice-packing-slip-maker' ); ?></a>
			</header>

			<?php settings_errors(); ?>

			<?php if ( $done < count( $steps ) ) : ?>
				<section class="ipsm-card ipsm-onboarding">
					<div class="ipsm-onboarding-head">
						<strong><?php esc_html_e( 'Getting started', 'invoice-packing-slip-maker' ); ?></strong>
						<span class="ipsm-progress"><span style="width:<?php echo esc_attr( round( $done / count( $steps ) * 100 ) ); ?>%"></span></span>
						<?php /* translators: 1: steps done, 2: total steps */ ?>
						<span class="ipsm-muted"><?php echo esc_html( sprintf( __( '%1$d of %2$d done', 'invoice-packing-slip-maker' ), $done, count( $steps ) ) ); ?></span>
					</div>
					<ul class="ipsm-steps">
						<?php foreach ( $steps as $step ) : ?>
							<li class="<?php echo $step[0] ? 'is-done' : ''; ?>">
								<span class="dashicons <?php echo $step[0] ? 'dashicons-yes-alt' : 'dashicons-marker'; ?>"></span>
								<?php if ( $step[0] ) : ?>
									<?php echo esc_html( $step[1] ); ?>
								<?php elseif ( 'emails' === $step[2] ) : ?>
									<a href="<?php echo esc_url( $email_url ); ?>"><?php echo esc_html( $step[1] ); ?></a>
								<?php else : ?>
									<a href="#<?php echo esc_attr( $step[2] ); ?>" data-ipsm-goto="<?php echo esc_attr( $step[2] ); ?>"><?php echo esc_html( $step[1] ); ?></a>
								<?php endif; ?>
							</li>
						<?php endforeach; ?>
					</ul>
				</section>
			<?php endif; ?>

			<form method="post" action="options.php" class="ipsm-form" id="ipsm-settings-form">
				<?php settings_fields( 'ipsm_settings_group' ); ?>

				<div class="ipsm-layout">
					<div class="ipsm-main">
						<nav class="ipsm-tabs" role="tablist">
							<button type="button" role="tab" class="ipsm-tab is-active" data-tab="shop"><span class="dashicons dashicons-store"></span><?php esc_html_e( 'Shop details', 'invoice-packing-slip-maker' ); ?></button>
							<button type="button" role="tab" class="ipsm-tab" data-tab="invoices"><span class="dashicons dashicons-media-text"></span><?php esc_html_e( 'Invoices', 'invoice-packing-slip-maker' ); ?></button>
							<button type="button" role="tab" class="ipsm-tab" data-tab="emails"><span class="dashicons dashicons-email-alt"></span><?php esc_html_e( 'Emails', 'invoice-packing-slip-maker' ); ?></button>
							<button type="button" role="tab" class="ipsm-tab" data-tab="layout"><span class="dashicons dashicons-layout"></span><?php esc_html_e( 'Layout', 'invoice-packing-slip-maker' ); ?></button>
						</nav>

						<!-- Shop details -->
						<section class="ipsm-panel is-active" data-panel="shop" role="tabpanel">
							<div class="ipsm-card">
								<h3><?php esc_html_e( 'Your logo', 'invoice-packing-slip-maker' ); ?></h3>
								<div class="ipsm-logo-field">
									<input type="hidden" id="ipsm-logo-id" name="<?php echo esc_attr( self::OPTION ); ?>[logo_id]" value="<?php echo esc_attr( $logo_id ); ?>" />
									<button type="button" class="ipsm-logo-drop<?php echo $logo_url ? ' has-logo' : ''; ?>" id="ipsm-logo-select">
										<span class="ipsm-logo-img" id="ipsm-logo-preview"><?php echo $logo_url ? '<img src="' . esc_url( $logo_url ) . '" alt="" />' : ''; ?></span>
										<span class="ipsm-logo-empty"><span class="dashicons dashicons-format-image"></span><?php esc_html_e( 'Click to choose a logo', 'invoice-packing-slip-maker' ); ?><small><?php esc_html_e( 'PNG or JPG, ideally wider than tall', 'invoice-packing-slip-maker' ); ?></small></span>
									</button>
									<button type="button" class="button-link ipsm-link-danger" id="ipsm-logo-remove"<?php echo $logo_id ? '' : ' hidden'; ?>><?php esc_html_e( 'Remove logo', 'invoice-packing-slip-maker' ); ?></button>
								</div>
							</div>

							<div class="ipsm-card">
								<h3><?php esc_html_e( 'Business information', 'invoice-packing-slip-maker' ); ?></h3>
								<p class="ipsm-card-desc"><?php esc_html_e( 'Printed at the top of every invoice and packing slip, and as the return address on shipping labels.', 'invoice-packing-slip-maker' ); ?></p>
								<div class="ipsm-grid">
									<?php
									self::field( 'shop_name', __( 'Shop name', 'invoice-packing-slip-maker' ) );
									self::field( 'tax_id', __( 'Tax / VAT / GST number', 'invoice-packing-slip-maker' ), '', 'text', __( 'Optional', 'invoice-packing-slip-maker' ) );
									self::field( 'shop_address', __( 'Address', 'invoice-packing-slip-maker' ), __( 'One line per row.', 'invoice-packing-slip-maker' ), 'textarea', "12 Main Street\nCity 560001\nCountry", true );
									self::field( 'shop_phone', __( 'Phone', 'invoice-packing-slip-maker' ), '', 'tel' );
									self::field( 'shop_email', __( 'Email', 'invoice-packing-slip-maker' ), '', 'email' );
									?>
								</div>
							</div>
						</section>

						<!-- Invoices -->
						<section class="ipsm-panel" data-panel="invoices" role="tabpanel" hidden>
							<div class="ipsm-card">
								<h3><?php esc_html_e( 'Invoice numbers', 'invoice-packing-slip-maker' ); ?></h3>
								<div class="ipsm-number-example">
									<span><?php esc_html_e( 'Next invoice will be', 'invoice-packing-slip-maker' ); ?></span>
									<strong id="ipsm-number-example"><?php echo esc_html( IPSM_Document::format_number( IPSM_Document::peek_number(), time() ) ); ?></strong>
								</div>
								<div class="ipsm-grid ipsm-grid-4">
									<?php
									self::field( 'number_prefix', __( 'Prefix', 'invoice-packing-slip-maker' ), '', 'text', 'INV-' );
									self::field( 'number_suffix', __( 'Suffix', 'invoice-packing-slip-maker' ) );
									self::field( 'number_padding', __( 'Digits', 'invoice-packing-slip-maker' ), '', 'number' );
									?>
									<div class="ipsm-field">
										<label for="ipsm-next"><?php esc_html_e( 'Next number', 'invoice-packing-slip-maker' ); ?></label>
										<input type="number" min="1" id="ipsm-next" name="<?php echo esc_attr( self::NUMBER_OPT ); ?>" value="<?php echo esc_attr( IPSM_Document::peek_number() ); ?>" />
									</div>
								</div>
								<p class="ipsm-help"><?php echo wp_kses( __( 'Use <code>{Y}</code> for the year and <code>{m}</code> for the month, e.g. <code>INV-{Y}-</code>.', 'invoice-packing-slip-maker' ), array( 'code' => array() ) ); ?></p>
								<?php self::toggle( 'reset_yearly', __( 'Start again at 1 every new year', 'invoice-packing-slip-maker' ) ); ?>
							</div>

							<div class="ipsm-card">
								<h3><?php esc_html_e( 'Invoice date', 'invoice-packing-slip-maker' ); ?></h3>
								<?php
								self::choices(
									'invoice_date',
									array(
										'order'   => array( __( 'Order date', 'invoice-packing-slip-maker' ), __( 'The day the customer placed the order', 'invoice-packing-slip-maker' ) ),
										'created' => array( __( 'Invoice creation date', 'invoice-packing-slip-maker' ), __( 'The day the invoice is first opened or sent', 'invoice-packing-slip-maker' ) ),
									)
								);
								?>
							</div>

							<div class="ipsm-card">
								<h3><?php esc_html_e( 'Notes & footer', 'invoice-packing-slip-maker' ); ?></h3>
								<div class="ipsm-grid">
									<?php
									self::field( 'notes', __( 'Terms / notes', 'invoice-packing-slip-maker' ), __( 'Shown on invoices, e.g. payment terms or return policy.', 'invoice-packing-slip-maker' ), 'textarea', '', true );
									self::field( 'footer', __( 'Footer', 'invoice-packing-slip-maker' ), __( 'Shown at the bottom of invoices and packing slips.', 'invoice-packing-slip-maker' ), 'textarea', '', true );
									?>
								</div>
							</div>
						</section>

						<!-- Emails -->
						<section class="ipsm-panel" data-panel="emails" role="tabpanel" hidden>
							<div class="ipsm-card">
								<div class="ipsm-status-line <?php echo $email_on ? 'is-ok' : 'is-warn'; ?>">
									<span class="dashicons <?php echo $email_on ? 'dashicons-yes-alt' : 'dashicons-warning'; ?>"></span>
									<span>
										<?php
										echo $email_on
											? esc_html__( 'The "Invoice (PDF)" email is on.', 'invoice-packing-slip-maker' )
											: esc_html__( 'The "Invoice (PDF)" email is switched off, so invoices cannot be emailed.', 'invoice-packing-slip-maker' );
										?>
									</span>
									<a class="ipsm-btn ipsm-btn-small" href="<?php echo esc_url( $email_url ); ?>"><?php echo $email_on ? esc_html__( 'Edit email text', 'invoice-packing-slip-maker' ) : esc_html__( 'Turn it on', 'invoice-packing-slip-maker' ); ?></a>
								</div>
							</div>

							<div class="ipsm-card">
								<h3><?php esc_html_e( 'Send invoices automatically', 'invoice-packing-slip-maker' ); ?></h3>
								<p class="ipsm-card-desc"><?php esc_html_e( 'Email the invoice once, as soon as an order reaches this status. You can always send or resend it by hand from the order.', 'invoice-packing-slip-maker' ); ?></p>
								<?php
								self::select(
									'auto_send_status',
									array( '' => __( 'Don\'t send automatically', 'invoice-packing-slip-maker' ) ) + wc_get_order_statuses()
								);
								?>
							</div>

							<div class="ipsm-card">
								<h3><?php esc_html_e( 'Attach the invoice PDF to these WooCommerce emails', 'invoice-packing-slip-maker' ); ?></h3>
								<div class="ipsm-toggle-list">
									<?php
									$selected = (array) self::get( 'attach_to' );
									foreach ( self::attachable_emails() as $id => $label ) {
										self::toggle_raw( self::OPTION . '[attach_to][]', $id, in_array( $id, $selected, true ), $label );
									}
									?>
								</div>
							</div>

							<div class="ipsm-card">
								<h3><?php esc_html_e( 'Customers', 'invoice-packing-slip-maker' ); ?></h3>
								<?php self::toggle( 'customer_download', __( 'Let customers download their invoice', 'invoice-packing-slip-maker' ), __( 'Adds an "Invoice" button in My Account → Orders and on the order confirmation page, once the order is paid.', 'invoice-packing-slip-maker' ) ); ?>
							</div>
						</section>

						<!-- Layout -->
						<section class="ipsm-panel" data-panel="layout" role="tabpanel" hidden>
							<div class="ipsm-card">
								<h3><?php esc_html_e( 'Paper size', 'invoice-packing-slip-maker' ); ?></h3>
								<p class="ipsm-card-desc"><?php esc_html_e( 'For invoices and packing slips.', 'invoice-packing-slip-maker' ); ?></p>
								<?php
								self::choices(
									'paper_size',
									array(
										'A5'     => array( 'A5', __( '148 × 210 mm · compact', 'invoice-packing-slip-maker' ), 'a5' ),
										'A4'     => array( 'A4', '210 × 297 mm', 'a4' ),
										'Letter' => array( 'US Letter', '8.5 × 11 in', 'letter' ),
									),
									true
								);
								?>
							</div>

							<div class="ipsm-card">
								<h3><?php esc_html_e( 'Shipping label size', 'invoice-packing-slip-maker' ); ?></h3>
								<?php
								self::choices(
									'label_size',
									array(
										'paper'   => array( __( 'Same as invoice', 'invoice-packing-slip-maker' ), __( 'Print on normal paper', 'invoice-packing-slip-maker' ), 'same' ),
										'4x6'     => array( '4 × 6 in', __( 'Thermal label printers', 'invoice-packing-slip-maker' ), 'label' ),
										'100x150' => array( '100 × 150 mm', __( 'Thermal label printers', 'invoice-packing-slip-maker' ), 'label' ),
										'A6'      => array( 'A6', '105 × 148 mm', 'label' ),
									),
									true
								);
								?>
							</div>

							<div class="ipsm-card">
								<h3><?php esc_html_e( 'Colour', 'invoice-packing-slip-maker' ); ?></h3>
								<p class="ipsm-card-desc"><?php esc_html_e( 'Used for titles, table headers and the total.', 'invoice-packing-slip-maker' ); ?></p>
								<div class="ipsm-swatches">
									<?php foreach ( array( '#1f2937', '#0f766e', '#1d4ed8', '#7c3aed', '#be123c', '#b45309', '#15803d' ) as $swatch ) : ?>
										<button type="button" class="ipsm-swatch" data-color="<?php echo esc_attr( $swatch ); ?>" style="background:<?php echo esc_attr( $swatch ); ?>" aria-label="<?php echo esc_attr( $swatch ); ?>"></button>
									<?php endforeach; ?>
									<input type="text" id="ipsm-accent" class="ipsm-color" name="<?php echo esc_attr( self::OPTION ); ?>[accent_color]" value="<?php echo esc_attr( self::get( 'accent_color' ) ); ?>" />
								</div>
							</div>

							<div class="ipsm-card">
								<h3><?php esc_html_e( 'What to show', 'invoice-packing-slip-maker' ); ?></h3>
								<div class="ipsm-toggle-list">
									<?php
									self::toggle( 'show_sku', __( 'Product SKU', 'invoice-packing-slip-maker' ) );
									self::toggle( 'show_shipping_addr', __( 'Shipping address on invoices', 'invoice-packing-slip-maker' ) );
									self::toggle( 'show_email_phone', __( 'Customer email and phone on invoices', 'invoice-packing-slip-maker' ) );
									self::toggle( 'slip_show_prices', __( 'Prices on packing slips', 'invoice-packing-slip-maker' ), __( 'Usually off — handy when the parcel is a gift.', 'invoice-packing-slip-maker' ) );
									?>
								</div>
							</div>
						</section>

						<div class="ipsm-savebar">
							<span class="ipsm-unsaved" hidden><span class="dashicons dashicons-warning"></span><?php esc_html_e( 'You have unsaved changes', 'invoice-packing-slip-maker' ); ?></span>
							<button type="submit" class="ipsm-btn ipsm-btn-primary"><?php esc_html_e( 'Save changes', 'invoice-packing-slip-maker' ); ?></button>
						</div>
					</div>

					<aside class="ipsm-side">
						<div class="ipsm-card ipsm-preview">
							<div class="ipsm-preview-head">
								<h3><?php esc_html_e( 'Preview', 'invoice-packing-slip-maker' ); ?></h3>
								<?php if ( $preview_id ) : ?>
									<div class="ipsm-segmented" role="group">
										<?php
										foreach ( IPSM_Document::types() as $i => $type ) {
											printf(
												'<button type="button" class="%s" data-src="%s" data-full="%s">%s</button>',
												0 === $i ? 'is-active' : '',
												esc_url( IPSM_Renderer::url( $preview_id, $type, 'html', array( 'preview' => 1, 'embed' => 1 ) ) ),
												esc_url( IPSM_Renderer::url( $preview_id, $type, 'html', array( 'preview' => 1 ) ) ),
												esc_html( array( __( 'Invoice', 'invoice-packing-slip-maker' ), __( 'Slip', 'invoice-packing-slip-maker' ), __( 'Label', 'invoice-packing-slip-maker' ) )[ $i ] )
											);
										}
										?>
									</div>
								<?php endif; ?>
							</div>
							<?php if ( $preview_id ) : ?>
								<div class="ipsm-preview-frame">
									<iframe id="ipsm-preview-iframe" title="<?php esc_attr_e( 'Document preview', 'invoice-packing-slip-maker' ); ?>" src="<?php echo esc_url( IPSM_Renderer::url( $preview_id, IPSM_Document::TYPE_INVOICE, 'html', array( 'preview' => 1, 'embed' => 1 ) ) ); ?>" loading="lazy"></iframe>
								</div>
								<p class="ipsm-help">
									<?php esc_html_e( 'Uses your latest order. Previews never use up an invoice number. Save to update.', 'invoice-packing-slip-maker' ); ?>
									<a href="<?php echo esc_url( IPSM_Renderer::url( $preview_id, IPSM_Document::TYPE_INVOICE, 'html', array( 'preview' => 1 ) ) ); ?>" target="_blank" id="ipsm-preview-full"><?php esc_html_e( 'Open full size ↗', 'invoice-packing-slip-maker' ); ?></a>
								</p>
							<?php else : ?>
								<div class="ipsm-empty">
									<span class="dashicons dashicons-cart"></span>
									<p><?php esc_html_e( 'A preview appears here once your shop has its first order.', 'invoice-packing-slip-maker' ); ?></p>
								</div>
							<?php endif; ?>
						</div>

						<div class="ipsm-card ipsm-tips">
							<h3><?php esc_html_e( 'Where to find everything', 'invoice-packing-slip-maker' ); ?></h3>
							<ul>
								<li><span class="dashicons dashicons-media-default"></span><?php esc_html_e( 'Open an order: the Invoice & Packing Slip box has print, PDF and email buttons.', 'invoice-packing-slip-maker' ); ?></li>
								<li><span class="dashicons dashicons-yes"></span><?php esc_html_e( 'Tick several orders in the list and use Bulk actions to print or email many at once.', 'invoice-packing-slip-maker' ); ?></li>
								<li><span class="dashicons dashicons-media-spreadsheet"></span><?php esc_html_e( 'Bulk actions → Export invoices (CSV) for your accountant.', 'invoice-packing-slip-maker' ); ?></li>
							</ul>
						</div>
					</aside>
				</div>
			</form>
		</div>
		<?php
	}

	/**
	 * Text / textarea / number field.
	 *
	 * @param string $key         Key.
	 * @param string $label       Label.
	 * @param string $help        Help text.
	 * @param string $type        text|email|tel|number|textarea.
	 * @param string $placeholder Placeholder.
	 * @param bool   $wide        Span both grid columns.
	 */
	private static function field( $key, $label, $help = '', $type = 'text', $placeholder = '', $wide = false ) {
		$id   = 'ipsm-' . $key;
		$name = self::OPTION . '[' . $key . ']';
		?>
		<div class="ipsm-field<?php echo $wide ? ' is-wide' : ''; ?>">
			<label for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $label ); ?></label>
			<?php if ( 'textarea' === $type ) : ?>
				<textarea id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>" rows="3" placeholder="<?php echo esc_attr( $placeholder ); ?>"><?php echo esc_textarea( self::get( $key ) ); ?></textarea>
			<?php else : ?>
				<input type="<?php echo esc_attr( $type ); ?>" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( self::get( $key ) ); ?>" placeholder="<?php echo esc_attr( $placeholder ); ?>"<?php echo 'number' === $type ? ' min="1" max="10"' : ''; ?> />
			<?php endif; ?>
			<?php if ( $help ) : ?>
				<p class="ipsm-help"><?php echo esc_html( $help ); ?></p>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Toggle switch bound to a yes/no setting.
	 *
	 * @param string $key   Key.
	 * @param string $label Label.
	 * @param string $help  Help text.
	 */
	private static function toggle( $key, $label, $help = '' ) {
		self::toggle_raw( self::OPTION . '[' . $key . ']', 'yes', self::enabled( $key ), $label, $help );
	}

	/**
	 * Toggle switch markup.
	 *
	 * @param string $name    Input name.
	 * @param string $value   Input value.
	 * @param bool   $checked Checked.
	 * @param string $label   Label.
	 * @param string $help    Help text.
	 */
	private static function toggle_raw( $name, $value, $checked, $label, $help = '' ) {
		?>
		<label class="ipsm-toggle">
			<input type="checkbox" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $value ); ?>" <?php checked( $checked ); ?> />
			<span class="ipsm-switch" aria-hidden="true"></span>
			<span class="ipsm-toggle-text"><?php echo esc_html( $label ); ?>
				<?php if ( $help ) : ?>
					<small><?php echo esc_html( $help ); ?></small>
				<?php endif; ?>
			</span>
		</label>
		<?php
	}

	/**
	 * Visual radio cards.
	 *
	 * @param string $key     Key.
	 * @param array  $options value => [title, subtitle, shape].
	 * @param bool   $shapes  Show a paper-shape icon.
	 */
	private static function choices( $key, $options, $shapes = false ) {
		$current = (string) self::get( $key );
		echo '<div class="ipsm-choices">';
		foreach ( $options as $value => $opt ) {
			?>
			<label class="ipsm-choice">
				<input type="radio" name="<?php echo esc_attr( self::OPTION . '[' . $key . ']' ); ?>" value="<?php echo esc_attr( $value ); ?>" <?php checked( $current, (string) $value ); ?> />
				<span class="ipsm-choice-body">
					<?php if ( $shapes && ! empty( $opt[2] ) ) : ?>
						<span class="ipsm-paper ipsm-paper-<?php echo esc_attr( $opt[2] ); ?>" aria-hidden="true"></span>
					<?php endif; ?>
					<strong><?php echo esc_html( $opt[0] ); ?></strong>
					<small><?php echo esc_html( $opt[1] ); ?></small>
				</span>
			</label>
			<?php
		}
		echo '</div>';
	}

	/**
	 * Select box.
	 *
	 * @param string $key     Key.
	 * @param array  $options Options.
	 */
	private static function select( $key, $options ) {
		$current = (string) self::get( $key );
		?>
		<div class="ipsm-field">
			<select id="ipsm-<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( self::OPTION . '[' . $key . ']' ); ?>">
				<?php foreach ( $options as $value => $text ) : ?>
					<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $current, (string) $value ); ?>><?php echo esc_html( $text ); ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<?php
	}
}
