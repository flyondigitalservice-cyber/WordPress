<?php
/**
 * Lead capture. Every form submission is:
 *   1. saved as a private "Lead" in wp-admin (so nothing is lost),
 *   2. emailed to the lead address,
 *   3. handed to WhatsApp with all details prefilled.
 *
 * Works with and without JavaScript (admin-post.php fallback).
 *
 * @package CFM
 */

defined( 'ABSPATH' ) || exit;

add_action( 'init', 'cfm_register_leads' );
/**
 * Private Lead post type.
 */
function cfm_register_leads() {
	register_post_type(
		'cfm_lead',
		array(
			'labels'          => array(
				'name'          => __( 'Leads', 'cfm' ),
				'singular_name' => __( 'Lead', 'cfm' ),
				'menu_name'     => __( 'Leads', 'cfm' ),
				'all_items'     => __( 'All leads', 'cfm' ),
				'edit_item'     => __( 'Lead details', 'cfm' ),
			),
			'public'          => false,
			'show_ui'         => true,
			'show_in_menu'    => true,
			'menu_position'   => 3,
			'menu_icon'       => 'dashicons-whatsapp',
			'supports'        => array( 'title', 'editor' ),
			'capability_type' => 'post',
			'capabilities'    => array( 'create_posts' => 'do_not_allow' ),
			'map_meta_cap'    => true,
		)
	);
}

/**
 * Options shown in the "Location" dropdown.
 *
 * @return string[]
 */
function cfm_lead_locations() {
	return array( 'Dubai', 'Abu Dhabi', 'Sharjah', 'Ajman', 'Ras Al Khaimah', 'Fujairah', 'Umm Al Quwain', 'Al Ain', __( 'Other', 'cfm' ) );
}

/**
 * Options shown in the "Interested in" dropdown: live product page titles.
 *
 * @return string[]
 */
function cfm_lead_products() {
	$titles = array();
	$parent = cfm_get_page( 'floor-mats' );
	if ( $parent ) {
		$children = get_pages( array( 'parent' => $parent->ID, 'sort_column' => 'menu_order,post_title' ) );
		foreach ( $children as $child ) {
			$titles[] = wp_strip_all_tags( $child->post_title );
		}
	}
	$titles[] = __( 'Measuring / installation service', 'cfm' );
	$titles[] = __( 'Not sure — please advise', 'cfm' );
	return $titles;
}

/**
 * Validate and store a lead. Returns the WhatsApp URL to continue to.
 *
 * @param array $raw Raw input.
 * @return array{ok:bool,message?:string,whatsapp?:string,id?:int}
 */
function cfm_process_lead( $raw ) {
	$field = static function ( $key, $max = 200 ) use ( $raw ) {
		$value = isset( $raw[ $key ] ) ? sanitize_text_field( wp_unslash( (string) $raw[ $key ] ) ) : '';
		return mb_substr( $value, 0, $max );
	};

	// Honeypot: bots fill hidden fields.
	if ( '' !== $field( 'cfm_company_website' ) ) {
		return array( 'ok' => true, 'whatsapp' => cfm_wa_url() );
	}

	$data = array(
		'name'     => $field( 'name', 100 ),
		'phone'    => $field( 'phone', 40 ),
		'email'    => sanitize_email( isset( $raw['email'] ) ? wp_unslash( (string) $raw['email'] ) : '' ),
		'location' => $field( 'location', 60 ),
		'product'  => $field( 'product', 120 ),
		'size'     => $field( 'size', 40 ),
		'message'  => isset( $raw['message'] ) ? mb_substr( sanitize_textarea_field( wp_unslash( (string) $raw['message'] ) ), 0, 1500 ) : '',
		'page'     => esc_url_raw( isset( $raw['page'] ) ? wp_unslash( (string) $raw['page'] ) : '' ),
	);

	if ( '' === $data['name'] || strlen( preg_replace( '/\D+/', '', $data['phone'] ) ) < 7 ) {
		return array( 'ok' => false, 'message' => __( 'Please fill in your name and a valid phone number.', 'cfm' ) );
	}

	// Simple flood protection: max 8 submissions per IP per 10 minutes.
	$ip  = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : 'unknown';
	$key = 'cfm_lead_rate_' . md5( $ip );
	$hit = (int) get_transient( $key );
	if ( $hit >= 8 ) {
		return array( 'ok' => false, 'message' => __( 'Too many requests — please message us on WhatsApp directly.', 'cfm' ), 'whatsapp' => cfm_wa_url() );
	}
	set_transient( $key, $hit + 1, 10 * MINUTE_IN_SECONDS );

	$lines = array(
		__( 'Name', 'cfm' )          => $data['name'],
		__( 'Phone', 'cfm' )         => $data['phone'],
		__( 'Email', 'cfm' )         => $data['email'],
		__( 'Location', 'cfm' )      => $data['location'],
		__( 'Interested in', 'cfm' ) => $data['product'],
		__( 'Size / qty', 'cfm' )     => $data['size'],
		__( 'Message', 'cfm' )       => $data['message'],
		__( 'Page', 'cfm' )          => $data['page'],
	);
	$text = '';
	foreach ( $lines as $label => $value ) {
		if ( '' !== $value ) {
			$text .= $label . ': ' . $value . "\n";
		}
	}

	$lead_id = wp_insert_post(
		array(
			'post_type'    => 'cfm_lead',
			'post_status'  => 'private',
			'post_title'   => sprintf( '%s — %s', $data['name'], $data['phone'] ),
			'post_content' => $text,
		),
		true
	);
	if ( ! is_wp_error( $lead_id ) ) {
		foreach ( $data as $key => $value ) {
			update_post_meta( $lead_id, '_cfm_' . $key, $value );
		}
	}

	$to = cfm_opt( 'lead_email' ) ? cfm_opt( 'lead_email' ) : get_option( 'admin_email' );
	if ( $to ) {
		$headers = array();
		if ( $data['email'] ) {
			$headers[] = 'Reply-To: ' . $data['name'] . ' <' . $data['email'] . '>';
		}
		wp_mail(
			$to,
			sprintf( '[%s] New lead: %s (%s)', cfm_opt( 'brand_name' ), $data['name'], $data['location'] ? $data['location'] : 'UAE' ),
			$text . "\n" . admin_url( 'edit.php?post_type=cfm_lead' ),
			$headers
		);
	}

	$wa_text = sprintf( "Hello %s, I'd like a quote.\n", cfm_opt( 'brand_name' ) ) . $text;

	return array(
		'ok'       => true,
		'id'       => is_wp_error( $lead_id ) ? 0 : (int) $lead_id,
		'whatsapp' => cfm_wa_number() ? cfm_wa_url( $wa_text ) : '',
		'message'  => __( 'Thank you! Your enquiry was received — we will contact you shortly.', 'cfm' ),
	);
}

add_action( 'rest_api_init', 'cfm_register_lead_route' );
/**
 * Public REST endpoint used by the form script.
 */
function cfm_register_lead_route() {
	register_rest_route(
		'cfm/v1',
		'/lead',
		array(
			'methods'             => 'POST',
			'permission_callback' => '__return_true',
			'callback'            => static function ( WP_REST_Request $request ) {
				$result = cfm_process_lead( $request->get_params() );
				return new WP_REST_Response( $result, $result['ok'] ? 200 : 422 );
			},
		)
	);
}

add_action( 'admin_post_nopriv_cfm_lead', 'cfm_lead_fallback' );
add_action( 'admin_post_cfm_lead', 'cfm_lead_fallback' );
/**
 * No-JS fallback: save and redirect straight to WhatsApp.
 */
function cfm_lead_fallback() {
	$result = cfm_process_lead( $_POST ); // phpcs:ignore WordPress.Security.NonceVerification.Missing -- public form, honeypot + rate-limit protected.
	if ( ! empty( $result['whatsapp'] ) ) {
		// wp_redirect()/esc_url() strip encoded line breaks (%0A). cfm_wa_url() builds this URL from
		// digits + rawurlencode(), so it has no raw CR/LF; send it directly to keep the message readable.
		$url = $result['whatsapp'];
		if ( 0 === strpos( $url, 'https://wa.me/' ) && ! preg_match( '/[\r\n\s]/', $url ) && ! headers_sent() ) {
			nocache_headers();
			header( 'Location: ' . $url, true, 303 );
			exit;
		}
		wp_redirect( $result['whatsapp'] ); // phpcs:ignore WordPress.Security.SafeRedirect.wp_redirect_wp_redirect -- wa.me is external by design.
		exit;
	}
	$back = wp_get_referer() ? wp_get_referer() : home_url( '/' );
	wp_safe_redirect( add_query_arg( 'cfm_lead', $result['ok'] ? 'thanks' : 'error', $back ) . '#quote' );
	exit;
}

/**
 * Render the quote form.
 *
 * @param array $args { title, product }.
 * @return string
 */
function cfm_lead_form( $args = array() ) {
	$args = wp_parse_args(
		$args,
		array(
			'title'   => __( 'Get a free quote on WhatsApp', 'cfm' ),
			'product' => '',
		)
	);
	// Preselect the product on product/service pages; elsewhere let the visitor choose.
	$qid      = get_queried_object_id();
	$current  = ( $qid && in_array( get_post_meta( $qid, '_cfm_type', true ), array( 'product', 'service', 'interior' ), true ) ) ? wp_strip_all_tags( get_the_title( $qid ) ) : __( 'Not sure — please advise', 'cfm' );
	$selected = $args['product'] ? $args['product'] : $current;
	$place    = get_queried_object_id() ? get_post_meta( get_queried_object_id(), '_cfm_emirate', true ) : '';
	$status   = isset( $_GET['cfm_lead'] ) ? sanitize_key( $_GET['cfm_lead'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

	ob_start();
	?>
	<form class="cfm-form" id="quote" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" data-cfm-lead novalidate>
		<?php if ( $args['title'] ) : ?>
			<h3 class="cfm-form__title"><?php echo esc_html( $args['title'] ); ?></h3>
		<?php endif; ?>
		<p class="cfm-form__note"><?php esc_html_e( 'Send your details and we reply on WhatsApp with prices, a free logo mockup and samples.', 'cfm' ); ?></p>
		<input type="hidden" name="action" value="cfm_lead">
		<input type="hidden" name="page" value="<?php echo esc_url( get_queried_object_id() ? get_permalink( get_queried_object_id() ) : home_url( '/' ) ); ?>">
		<div class="cfm-form__hp" aria-hidden="true"><label>Website <input type="text" name="cfm_company_website" tabindex="-1" autocomplete="off"></label></div>
		<div class="cfm-form__grid">
			<label class="cfm-field"><span><?php esc_html_e( 'Your name *', 'cfm' ); ?></span><input type="text" name="name" required autocomplete="name"></label>
			<label class="cfm-field"><span><?php esc_html_e( 'Phone / WhatsApp *', 'cfm' ); ?></span><input type="tel" name="phone" required autocomplete="tel" placeholder="+971"></label>
			<label class="cfm-field"><span><?php esc_html_e( 'Email', 'cfm' ); ?></span><input type="email" name="email" autocomplete="email"></label>
			<label class="cfm-field"><span><?php esc_html_e( 'Location', 'cfm' ); ?></span>
				<select name="location">
					<?php foreach ( cfm_lead_locations() as $loc ) : ?>
						<option <?php selected( $place ? $place : 'Dubai', $loc ); ?>><?php echo esc_html( $loc ); ?></option>
					<?php endforeach; ?>
				</select>
			</label>
			<label class="cfm-field"><span><?php esc_html_e( 'Interested in', 'cfm' ); ?></span>
				<select name="product">
					<?php
					$products = cfm_lead_products();
					if ( $selected && ! in_array( $selected, $products, true ) ) {
						array_unshift( $products, $selected );
					}
					foreach ( $products as $product ) :
						?>
						<option <?php selected( $selected, $product ); ?>><?php echo esc_html( $product ); ?></option>
					<?php endforeach; ?>
				</select>
			</label>
			<label class="cfm-field"><span><?php esc_html_e( 'Size / quantity', 'cfm' ); ?></span><input type="text" name="size" placeholder="e.g. 90 × 150 cm, 2 pcs"></label>
			<label class="cfm-field cfm-field--full"><span><?php esc_html_e( 'Tell us about your mat', 'cfm' ); ?></span><textarea name="message" rows="3" placeholder="<?php esc_attr_e( 'Logo mat for office entrance, lift day mats, kitchen anti-fatigue, car mats…', 'cfm' ); ?>"></textarea></label>
		</div>
		<button class="cfm-btn cfm-btn--wa cfm-btn--block" type="submit"><?php echo cfm_icon( 'whatsapp', 20 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> <span><?php esc_html_e( 'Send on WhatsApp', 'cfm' ); ?></span></button>
		<p class="cfm-form__status" role="status" aria-live="polite">
			<?php
			if ( 'thanks' === $status ) {
				esc_html_e( 'Thank you! Your enquiry was received — we will contact you shortly.', 'cfm' );
			} elseif ( 'error' === $status ) {
				esc_html_e( 'Please fill in your name and a valid phone number.', 'cfm' );
			}
			?>
		</p>
	</form>
	<?php
	return ob_get_clean();
}

// Admin list columns for leads.
add_filter(
	'manage_cfm_lead_posts_columns',
	static function ( $columns ) {
		return array(
			'cb'           => $columns['cb'],
			'title'        => __( 'Lead', 'cfm' ),
			'cfm_location' => __( 'Location', 'cfm' ),
			'cfm_product'  => __( 'Interested in', 'cfm' ),
			'cfm_wa'       => __( 'WhatsApp', 'cfm' ),
			'date'         => __( 'Received', 'cfm' ),
		);
	}
);
add_action(
	'manage_cfm_lead_posts_custom_column',
	static function ( $column, $post_id ) {
		if ( 'cfm_location' === $column ) {
			echo esc_html( get_post_meta( $post_id, '_cfm_location', true ) );
		} elseif ( 'cfm_product' === $column ) {
			echo esc_html( get_post_meta( $post_id, '_cfm_product', true ) );
		} elseif ( 'cfm_wa' === $column ) {
			$phone = preg_replace( '/\D+/', '', (string) get_post_meta( $post_id, '_cfm_phone', true ) );
			if ( $phone ) {
				if ( 10 === strlen( $phone ) && 0 === strpos( $phone, '05' ) ) {
					$phone = '971' . substr( $phone, 1 );
				}
				printf( '<a class="button button-small" href="%s" target="_blank" rel="noopener">%s</a>', esc_url( 'https://wa.me/' . $phone ), esc_html__( 'Chat', 'cfm' ) );
			}
		}
	},
	10,
	2
);
