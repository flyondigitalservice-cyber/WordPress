<?php
/**
 * Store Options page (Appearance > Store Options), built on the Settings API.
 * Block themes have no Customizer UI, so store-level values live here.
 *
 * Only fields for features the active theme uses are shown.
 *
 * @package BazaarCore
 */

defined( 'ABSPATH' ) || exit;

const BZR_OPTION_KEY = 'bzr_options';

/**
 * Field definitions.
 *
 * Each field: label, type (text|email|tel|number|textarea|html|select), description,
 * section, feature (theme feature that must be on, '' = always), choices (select only).
 *
 * @return array<string,array>
 */
function bzr_option_fields() {
	$proxy_choices = array();
	foreach ( bzr_proxy_headers() as $key => $header ) {
		$proxy_choices[ $key ] = $header[1];
	}

	$fields = array(
		'store_email'      => array(
			'label'   => __( 'Store email', 'bazaar-core' ),
			'type'    => 'email',
			'desc'    => __( 'Shown on the contact page and used to receive contact-form messages. Defaults to the site admin email.', 'bazaar-core' ),
			'section' => 'store',
		),
		'store_phone'      => array(
			'label'   => __( 'Store phone', 'bazaar-core' ),
			'type'    => 'tel',
			'desc'    => __( 'Shown on the contact page and footer.', 'bazaar-core' ),
			'section' => 'store',
		),
		'store_address'    => array(
			'label'   => __( 'Store address', 'bazaar-core' ),
			'type'    => 'textarea',
			'desc'    => __( 'Shown on the contact page.', 'bazaar-core' ),
			'section' => 'store',
		),
		'store_hours'      => array(
			'label'   => __( 'Business hours', 'bazaar-core' ),
			'type'    => 'text',
			'desc'    => __( 'e.g. Mon-Sat, 10am-7pm', 'bazaar-core' ),
			'section' => 'store',
		),
		'invoice_label'    => array(
			'label'   => __( 'Invoice label', 'bazaar-core' ),
			'type'    => 'text',
			'desc'    => __( 'How tax invoices are called in your market, e.g. "GST invoice" or "VAT invoice". Used in trust badges.', 'bazaar-core' ),
			'section' => 'store',
			'feature' => 'pdp-extras',
		),
		'delivery_min'     => array(
			'label'   => __( 'Delivery: minimum days', 'bazaar-core' ),
			'type'    => 'number',
			'desc'    => __( 'Used for the estimated delivery date on product pages.', 'bazaar-core' ),
			'section' => 'product',
			'feature' => 'pdp-extras',
		),
		'delivery_max'     => array(
			'label'   => __( 'Delivery: maximum days', 'bazaar-core' ),
			'type'    => 'number',
			'section' => 'product',
			'feature' => 'pdp-extras',
		),
		'warranty_text'    => array(
			'label'   => __( 'Warranty badge text', 'bazaar-core' ),
			'type'    => 'text',
			'desc'    => __( 'e.g. 1-year brand warranty', 'bazaar-core' ),
			'section' => 'product',
			'feature' => 'warranty',
		),
		'emi_months'       => array(
			'label'   => __( 'EMI: number of instalments', 'bazaar-core' ),
			'type'    => 'number',
			'desc'    => __( 'Shows "Pay in N instalments of X" on products above the minimum below. Leave empty to hide. Only enable if you really offer instalments.', 'bazaar-core' ),
			'section' => 'product',
			'feature' => 'emi',
		),
		'emi_min_price'    => array(
			'label'   => __( 'EMI: minimum product price', 'bazaar-core' ),
			'type'    => 'number',
			'section' => 'product',
			'feature' => 'emi',
		),
		'size_guide_html'  => array(
			'label'   => __( 'Size guide', 'bazaar-core' ),
			'type'    => 'html',
			'desc'    => __( 'HTML shown in the size guide pop-up (a table works best). Leave empty for the default chart.', 'bazaar-core' ),
			'section' => 'product',
			'feature' => 'size-guide',
		),
		'whatsapp_number'  => array(
			'label'   => __( 'WhatsApp number', 'bazaar-core' ),
			'type'    => 'tel',
			'desc'    => __( 'Country code + number, digits only (e.g. 919876543210). Leave empty to hide WhatsApp buttons.', 'bazaar-core' ),
			'section' => 'whatsapp',
			'feature' => 'whatsapp',
		),
		'whatsapp_message' => array(
			'label'   => __( 'WhatsApp message', 'bazaar-core' ),
			'type'    => 'text',
			'section' => 'whatsapp',
			'feature' => 'whatsapp',
		),
		'proxy_header'     => array(
			'label'   => __( 'Visitor IP header', 'bazaar-core' ),
			'type'    => 'select',
			'choices' => $proxy_choices,
			'desc'    => __( 'Login codes and the contact form are rate-limited per visitor IP. If the site sits behind Cloudflare, a load balancer or a reverse proxy, choose its header here, otherwise all visitors look like one IP and get blocked together. Leave on "None" if unsure and not behind a proxy: a wrong choice lets bots fake their IP.', 'bazaar-core' ),
			'section' => 'security',
		),
		'trusted_proxies'  => array(
			'label'   => __( 'Trusted proxy IPs', 'bazaar-core' ),
			'type'    => 'textarea',
			'desc'    => __( 'Optional but recommended: IPs or ranges (CIDR) of your proxy, one per line, e.g. Cloudflare\'s published ranges. The header above is then only believed for requests coming from these addresses.', 'bazaar-core' ),
			'section' => 'security',
		),
	);

	/**
	 * Filter Store Options fields. Themes can add their own.
	 *
	 * @param array $fields Field definitions.
	 */
	return apply_filters( 'bzr_option_fields', $fields );
}

/**
 * Sections, in display order.
 *
 * @return array<string,string>
 */
function bzr_option_sections() {
	return array(
		'store'    => __( 'Store details', 'bazaar-core' ),
		'product'  => __( 'Product pages', 'bazaar-core' ),
		'whatsapp' => __( 'WhatsApp', 'bazaar-core' ),
		'security' => __( 'Security & rate limiting', 'bazaar-core' ),
	);
}

/**
 * Is a field relevant to the active theme?
 *
 * @param array $field Field definition.
 * @return bool
 */
function bzr_option_field_active( $field ) {
	return empty( $field['feature'] ) || bzr_feature( $field['feature'] );
}

/**
 * Read one option.
 *
 * @param string $key     Option key.
 * @param mixed  $default Fallback.
 * @return mixed
 */
function bzr_option( $key, $default = '' ) {
	$options = get_option( BZR_OPTION_KEY, array() );
	return is_array( $options ) && isset( $options[ $key ] ) && '' !== $options[ $key ] ? $options[ $key ] : $default;
}

/**
 * Sanitize all options on save. Values of fields hidden for the current theme are kept,
 * so switching themes does not wipe settings.
 *
 * @param array $input Raw input.
 * @return array
 */
function bzr_sanitize_options( $input ) {
	$input = is_array( $input ) ? $input : array();
	$old   = get_option( BZR_OPTION_KEY, array() );
	$out   = is_array( $old ) ? $old : array();

	foreach ( bzr_option_fields() as $key => $field ) {
		if ( ! array_key_exists( $key, $input ) ) {
			continue;
		}
		$raw = (string) $input[ $key ];
		switch ( $field['type'] ) {
			case 'email':
				$out[ $key ] = sanitize_email( $raw );
				break;
			case 'number':
				$out[ $key ] = '' === $raw ? '' : (string) absint( $raw );
				break;
			case 'textarea':
				$out[ $key ] = sanitize_textarea_field( $raw );
				break;
			case 'html':
				$out[ $key ] = wp_kses_post( $raw );
				break;
			case 'select':
				$out[ $key ] = isset( $field['choices'][ $raw ] ) ? $raw : '';
				break;
			case 'tel':
				$out[ $key ] = 'whatsapp_number' === $key ? preg_replace( '/\D+/', '', $raw ) : sanitize_text_field( $raw );
				break;
			default:
				$out[ $key ] = sanitize_text_field( $raw );
		}
	}

	return $out;
}

/**
 * Register the setting. options.php passes already-unslashed values to the callback.
 */
function bzr_register_settings() {
	register_setting(
		'bzr_options_group',
		BZR_OPTION_KEY,
		array(
			'type'              => 'array',
			'sanitize_callback' => 'bzr_sanitize_options',
			'default'           => array(),
		)
	);
}
add_action( 'admin_init', 'bzr_register_settings' );

/**
 * Add the menu entry.
 */
function bzr_add_options_page() {
	add_theme_page(
		__( 'Store Options', 'bazaar-core' ),
		__( 'Store Options', 'bazaar-core' ),
		'manage_options',
		'bzr-options',
		'bzr_render_options_page'
	);
}
add_action( 'admin_menu', 'bzr_add_options_page' );

/**
 * One form field.
 *
 * @param string $key   Option key.
 * @param array  $field Definition.
 */
function bzr_render_field( $key, $field ) {
	$id    = 'bzr-' . $key;
	$name  = BZR_OPTION_KEY . '[' . $key . ']';
	$value = (string) bzr_option( $key );

	switch ( $field['type'] ) {
		case 'textarea':
		case 'html':
			printf(
				'<textarea id="%1$s" class="large-text%2$s" rows="%3$d" name="%4$s">%5$s</textarea>',
				esc_attr( $id ),
				'html' === $field['type'] ? ' code' : '',
				'html' === $field['type'] ? 8 : 3,
				esc_attr( $name ),
				esc_textarea( $value )
			);
			break;
		case 'select':
			printf( '<select id="%1$s" name="%2$s">', esc_attr( $id ), esc_attr( $name ) );
			foreach ( $field['choices'] as $choice => $label ) {
				printf( '<option value="%1$s"%2$s>%3$s</option>', esc_attr( $choice ), selected( $value, $choice, false ), esc_html( $label ) );
			}
			echo '</select>';
			break;
		default:
			printf(
				'<input type="%1$s" id="%2$s" class="regular-text" name="%3$s" value="%4$s"%5$s />',
				esc_attr( $field['type'] ),
				esc_attr( $id ),
				esc_attr( $name ),
				esc_attr( $value ),
				'number' === $field['type'] ? ' min="0" step="1"' : ''
			);
	}

	if ( ! empty( $field['desc'] ) ) {
		echo '<p class="description">' . esc_html( $field['desc'] ) . '</p>';
	}
}

/**
 * Render the options page.
 */
function bzr_render_options_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$fields = array_filter( bzr_option_fields(), 'bzr_option_field_active' );
	?>
	<div class="wrap">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
		<?php bzr_options_page_notices(); ?>
		<form method="post" action="options.php">
			<?php settings_fields( 'bzr_options_group' ); ?>
			<?php foreach ( bzr_option_sections() as $section => $title ) : ?>
				<?php
				$in_section = array_filter(
					$fields,
					static function ( $field ) use ( $section ) {
						return ( isset( $field['section'] ) ? $field['section'] : 'store' ) === $section;
					}
				);
				if ( ! $in_section ) {
					continue;
				}
				?>
				<h2 class="title"><?php echo esc_html( $title ); ?></h2>
				<table class="form-table" role="presentation">
					<?php foreach ( $in_section as $key => $field ) : ?>
						<tr>
							<th scope="row"><label for="<?php echo esc_attr( 'bzr-' . $key ); ?>"><?php echo esc_html( $field['label'] ); ?></label></th>
							<td><?php bzr_render_field( $key, $field ); ?></td>
						</tr>
					<?php endforeach; ?>
				</table>
			<?php endforeach; ?>
			<?php submit_button(); ?>
		</form>

		<hr />
		<h2 class="title"><?php esc_html_e( 'Tools', 'bazaar-core' ); ?></h2>
		<table class="form-table" role="presentation">
			<tr>
				<th scope="row"><?php esc_html_e( 'Email delivery', 'bazaar-core' ); ?></th>
				<td>
					<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
						<input type="hidden" name="action" value="bzr_test_email" />
						<?php wp_nonce_field( 'bzr_test_email' ); ?>
						<?php submit_button( __( 'Send a test email to me', 'bazaar-core' ), 'secondary', 'submit', false ); ?>
					</form>
					<p class="description"><?php esc_html_e( 'Login codes and contact messages are sent by email. Check that the test arrives in the inbox, not spam.', 'bazaar-core' ); ?></p>
				</td>
			</tr>
			<?php if ( apply_filters( 'bzr_starter_pages', array() ) ) : ?>
			<tr>
				<th scope="row"><?php esc_html_e( 'Starter pages', 'bazaar-core' ); ?></th>
				<td>
					<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
						<input type="hidden" name="action" value="bzr_starter_pages" />
						<?php wp_nonce_field( 'bzr_starter_pages' ); ?>
						<?php submit_button( __( 'Create missing pages', 'bazaar-core' ), 'secondary', 'submit', false ); ?>
					</form>
					<p class="description"><?php esc_html_e( 'Creates About, Contact, policy and sign-in pages from the theme. Pages that already exist are never changed.', 'bazaar-core' ); ?></p>
				</td>
			</tr>
			<?php endif; ?>
		</table>
	</div>
	<?php
}

/**
 * Notices on the options page: tool results, email and proxy hints.
 */
function bzr_options_page_notices() {
	// phpcs:disable WordPress.Security.NonceVerification -- display-only flags set by our own redirects.
	$flag = isset( $_GET['bzr_notice'] ) ? sanitize_key( wp_unslash( $_GET['bzr_notice'] ) ) : '';
	$num  = isset( $_GET['bzr_count'] ) ? absint( $_GET['bzr_count'] ) : 0;
	// phpcs:enable

	$messages = array(
		'mail-ok'   => array( 'success', __( 'Test email sent. If it is not in your inbox within a few minutes, check spam and set up an SMTP plugin.', 'bazaar-core' ) ),
		'mail-fail' => array( 'error', __( 'WordPress could not send the test email. Install and configure an SMTP plugin (e.g. WP Mail SMTP or FluentSMTP) before using login codes.', 'bazaar-core' ) ),
		/* translators: %d: number of pages created */
		'pages'     => array( 'success', sprintf( _n( '%d page created.', '%d pages created.', $num, 'bazaar-core' ), $num ) ),
	);
	if ( isset( $messages[ $flag ] ) ) {
		printf( '<div class="notice notice-%1$s"><p>%2$s</p></div>', esc_attr( $messages[ $flag ][0] ), esc_html( $messages[ $flag ][1] ) );
	}

	if ( ( bzr_feature( 'otp' ) || bzr_feature( 'contact-form' ) ) && ! bzr_has_smtp_plugin() ) {
		printf(
			'<div class="notice notice-warning"><p>%s</p></div>',
			esc_html__( 'No SMTP plugin detected. Without one, login codes and contact messages often land in spam or are never delivered, and customers cannot sign in. Install an SMTP plugin and use "Send a test email" below.', 'bazaar-core' )
		);
	}

	if ( '' === bzr_option( 'proxy_header', '' ) ) {
		$seen = array();
		foreach ( bzr_proxy_headers() as $key => $header ) {
			if ( '' !== $header[0] && ! empty( $_SERVER[ $header[0] ] ) ) {
				$seen[] = $header[1];
			}
		}
		if ( $seen ) {
			printf(
				'<div class="notice notice-warning"><p>%s</p></div>',
				esc_html(
					sprintf(
						/* translators: %s: header names */
						__( 'This site appears to be behind a proxy or CDN (detected: %s). Set "Visitor IP header" below, otherwise rate limits treat all visitors as one.', 'bazaar-core' ),
						implode( ', ', $seen )
					)
				)
			);
		}
	}
}

/**
 * Tool: send a test email to the current admin.
 */
function bzr_handle_test_email() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'Sorry, you are not allowed to do that.', 'bazaar-core' ), 403 );
	}
	check_admin_referer( 'bzr_test_email' );

	$user = wp_get_current_user();
	$sent = wp_mail(
		$user->user_email,
		/* translators: %s: site name */
		sprintf( __( '[%s] Test email', 'bazaar-core' ), wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ) ),
		__( 'Email delivery works. Login codes and contact-form messages will be sent the same way.', 'bazaar-core' )
	);

	wp_safe_redirect( add_query_arg( 'bzr_notice', $sent ? 'mail-ok' : 'mail-fail', admin_url( 'themes.php?page=bzr-options' ) ) );
	exit;
}
add_action( 'admin_post_bzr_test_email', 'bzr_handle_test_email' );
