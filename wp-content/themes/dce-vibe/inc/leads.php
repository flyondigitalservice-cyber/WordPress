<?php
/**
 * Lead capture: every form submission is saved (Leads menu), emailed to the team,
 * and then opened in WhatsApp with all details pre-filled — so no lead is lost.
 *
 * Shortcode: [dce_lead_form title="Book your free home visit" service="Wave curtains" button="Send on WhatsApp"]
 *
 * @package dce-vibe
 */

defined( 'ABSPATH' ) || exit;

add_action( 'init', 'dce_register_leads' );
function dce_register_leads() {
	register_post_type(
		'dce_lead',
		array(
			'labels'          => array(
				'name'          => __( 'Leads', 'dce-vibe' ),
				'singular_name' => __( 'Lead', 'dce-vibe' ),
				'all_items'     => __( 'All leads', 'dce-vibe' ),
				'edit_item'     => __( 'Lead details', 'dce-vibe' ),
			),
			'public'          => false,
			'show_ui'         => true,
			'show_in_menu'    => true,
			'menu_icon'       => 'dashicons-whatsapp',
			'menu_position'   => 25,
			'supports'        => array( 'title', 'editor' ),
			'capability_type' => 'post',
			'capabilities'    => array( 'create_posts' => 'do_not_allow' ),
			'map_meta_cap'    => true,
		)
	);
}

/**
 * Build the WhatsApp message for a lead.
 *
 * @param array $lead Lead fields.
 * @return string
 */
function dce_lead_message( $lead ) {
	$lines   = array( sprintf( 'Hello %s, I would like a free home visit / quote.', dce_opt( 'brand' ) ) );
	$labels  = array(
		'name'    => 'Name',
		'phone'   => 'Phone',
		'service' => 'Interested in',
		'area'    => 'Area',
		'message' => 'Details',
		'source'  => 'Page',
	);
	foreach ( $labels as $k => $label ) {
		if ( ! empty( $lead[ $k ] ) ) {
			$lines[] = $label . ': ' . $lead[ $k ];
		}
	}
	return implode( "\n", $lines );
}

/**
 * Sanitize lead input.
 *
 * @param array $src Raw input.
 * @return array
 */
function dce_lead_from_request( $src ) {
	$get = function ( $k, $cb = 'sanitize_text_field' ) use ( $src ) {
		return isset( $src[ $k ] ) ? call_user_func( $cb, wp_unslash( $src[ $k ] ) ) : '';
	};
	return array(
		'name'    => $get( 'dce_name' ),
		'phone'   => $get( 'dce_phone' ),
		'email'   => $get( 'dce_email', 'sanitize_email' ),
		'service' => $get( 'dce_service' ),
		'area'    => $get( 'dce_area' ),
		'message' => $get( 'dce_message', 'sanitize_textarea_field' ),
		'source'  => $get( 'dce_source', 'esc_url_raw' ),
		'trap'    => $get( 'dce_company' ),
	);
}

/**
 * Save the lead and notify the team.
 *
 * @param array $lead Lead.
 * @return int|WP_Error
 */
function dce_store_lead( $lead ) {
	if ( '' !== $lead['trap'] ) {
		return new WP_Error( 'spam', 'spam' );
	}
	if ( '' === $lead['name'] || strlen( preg_replace( '/\D+/', '', $lead['phone'] ) ) < 7 ) {
		return new WP_Error( 'invalid', __( 'Please add your name and a valid phone number.', 'dce-vibe' ) );
	}
	$body = dce_lead_message( $lead );
	if ( $lead['email'] ) {
		$body .= "\nEmail: " . $lead['email'];
	}
	$id = wp_insert_post(
		array(
			'post_type'    => 'dce_lead',
			'post_status'  => 'private',
			'post_title'   => sprintf( '%s — %s', $lead['name'], $lead['service'] ? $lead['service'] : __( 'General enquiry', 'dce-vibe' ) ),
			'post_content' => $body,
		),
		true
	);
	if ( is_wp_error( $id ) ) {
		return $id;
	}
	foreach ( array( 'name', 'phone', 'email', 'service', 'area', 'source' ) as $k ) {
		update_post_meta( $id, '_dce_' . $k, $lead[ $k ] );
	}
	$to = dce_opt( 'lead_email' );
	if ( $to && is_email( $to ) ) {
		$headers = array();
		if ( $lead['email'] ) {
			$headers[] = 'Reply-To: ' . $lead['name'] . ' <' . $lead['email'] . '>';
		}
		wp_mail( $to, sprintf( '[%s] New lead: %s', dce_opt( 'brand' ), $lead['name'] ), $body . "\n\nWhatsApp the customer: https://wa.me/" . preg_replace( '/\D+/', '', $lead['phone'] ), $headers );
	}
	do_action( 'dce_lead_saved', $id, $lead );
	return $id;
}

/* AJAX (used by the form script). */
add_action( 'wp_ajax_dce_lead', 'dce_ajax_lead' );
add_action( 'wp_ajax_nopriv_dce_lead', 'dce_ajax_lead' );
function dce_ajax_lead() {
	check_ajax_referer( 'dce_lead', 'nonce' );
	$lead = dce_lead_from_request( $_POST ); // phpcs:ignore WordPress.Security.NonceVerification
	$res  = dce_store_lead( $lead );
	if ( is_wp_error( $res ) && 'spam' !== $res->get_error_code() ) {
		wp_send_json_error( array( 'message' => $res->get_error_message() ), 400 );
	}
	wp_send_json_success( array( 'wa' => dce_wa_url( dce_lead_message( $lead ) ) ) );
}

/* No-JavaScript fallback: save, then redirect to WhatsApp. */
add_action( 'admin_post_dce_lead_submit', 'dce_post_lead' );
add_action( 'admin_post_nopriv_dce_lead_submit', 'dce_post_lead' );
function dce_post_lead() {
	if ( ! isset( $_POST['dce_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['dce_nonce'] ) ), 'dce_lead' ) ) {
		wp_die( esc_html__( 'Your session expired. Please go back and try again.', 'dce-vibe' ) );
	}
	$lead = dce_lead_from_request( $_POST );
	$res  = dce_store_lead( $lead );
	if ( is_wp_error( $res ) && 'spam' !== $res->get_error_code() ) {
		wp_die( esc_html( $res->get_error_message() ) );
	}
	wp_redirect( dce_wa_url( dce_lead_message( $lead ) ) ); // phpcs:ignore WordPress.Security.SafeRedirect -- WhatsApp is the intended destination.
	exit;
}

add_shortcode( 'dce_lead_form', 'dce_lead_form_shortcode' );
function dce_lead_form_shortcode( $atts ) {
	$atts = shortcode_atts(
		array(
			'title'   => __( 'Book your free home visit', 'dce-vibe' ),
			'text'    => __( 'Send your details and WhatsApp opens with everything filled in. We reply within working hours.', 'dce-vibe' ),
			'service' => '',
			'area'    => '',
			'button'  => __( 'Send on WhatsApp', 'dce-vibe' ),
		),
		$atts,
		'dce_lead_form'
	);
	$source = is_singular() ? get_permalink() : home_url( add_query_arg( array() ) );
	ob_start();
	?>
	<form class="dce-form dce-lead-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" novalidate>
		<?php if ( $atts['title'] ) : ?><p class="dce-form-title"><?php echo esc_html( $atts['title'] ); ?></p><?php endif; ?>
		<?php if ( $atts['text'] ) : ?><p class="dce-form-sub"><?php echo esc_html( $atts['text'] ); ?></p><?php endif; ?>
		<input type="hidden" name="action" value="dce_lead_submit">
		<input type="hidden" name="dce_nonce" value="<?php echo esc_attr( wp_create_nonce( 'dce_lead' ) ); ?>">
		<input type="hidden" name="dce_source" value="<?php echo esc_url( $source ); ?>">
		<input class="dce-honeypot" type="text" name="dce_company" tabindex="-1" autocomplete="off" aria-hidden="true">
		<div class="dce-form-grid">
			<input class="dce-field" type="text" name="dce_name" required aria-label="<?php esc_attr_e( 'Full name', 'dce-vibe' ); ?>" placeholder="<?php esc_attr_e( 'Full name *', 'dce-vibe' ); ?>" autocomplete="name">
			<input class="dce-field" type="tel" name="dce_phone" required aria-label="<?php esc_attr_e( 'Phone or WhatsApp number', 'dce-vibe' ); ?>" placeholder="<?php esc_attr_e( 'Phone / WhatsApp *', 'dce-vibe' ); ?>" autocomplete="tel" inputmode="tel">
			<select class="dce-field" name="dce_service" aria-label="<?php esc_attr_e( 'What do you need?', 'dce-vibe' ); ?>">
				<option value=""><?php esc_html_e( 'What do you need?', 'dce-vibe' ); ?></option>
				<?php
				$services = dce_lead_services();
				foreach ( $services as $s ) {
					if ( 0 === strcasecmp( $s, $atts['service'] ) ) {
						$atts['service'] = $s;
					}
				}
				if ( $atts['service'] && ! in_array( $atts['service'], $services, true ) ) {
					array_unshift( $services, $atts['service'] );
				}
				foreach ( $services as $s ) {
					printf( '<option value="%1$s"%2$s>%1$s</option>', esc_attr( $s ), selected( $s, $atts['service'], false ) );
				}
				?>
			</select>
			<select class="dce-field" name="dce_area" aria-label="<?php esc_attr_e( 'Your area', 'dce-vibe' ); ?>">
				<option value=""><?php esc_html_e( 'Your area', 'dce-vibe' ); ?></option>
				<?php
				$areas = dce_lead_areas();
				foreach ( $areas as $a ) {
					if ( 0 === strcasecmp( $a, $atts['area'] ) ) {
						$atts['area'] = $a;
					}
				}
				if ( $atts['area'] && ! in_array( $atts['area'], $areas, true ) ) {
					array_unshift( $areas, $atts['area'] );
				}
				foreach ( $areas as $a ) {
					printf( '<option value="%1$s"%2$s>%1$s</option>', esc_attr( $a ), selected( $a, $atts['area'], false ) );
				}
				?>
			</select>
			<textarea class="dce-field dce-field-wide" name="dce_message" rows="3" aria-label="<?php esc_attr_e( 'Project details', 'dce-vibe' ); ?>" placeholder="<?php esc_attr_e( 'Rooms, number of windows, approximate sizes… (optional)', 'dce-vibe' ); ?>"></textarea>
		</div>
		<button class="dce-btn dce-btn-wa dce-btn-block" type="submit"><?php echo dce_icon( 'whatsapp', 20 ); // phpcs:ignore ?><span><?php echo esc_html( $atts['button'] ); ?></span></button>
		<p class="dce-form-status" role="status" aria-live="polite"></p>
		<p class="dce-form-note">
			<?php
			printf(
				/* translators: 1: phone link */
				esc_html__( 'Prefer to talk? Call %s', 'dce-vibe' ),
				'<a href="' . esc_url( dce_tel_url() ) . '">' . esc_html( dce_opt( 'phone' ) ) . '</a>'
			);
			?>
		</p>
	</form>
	<?php
	return ob_get_clean();
}

/* Admin list columns for leads. */
add_filter( 'manage_dce_lead_posts_columns', 'dce_lead_columns' );
function dce_lead_columns( $cols ) {
	return array(
		'cb'          => $cols['cb'],
		'title'       => __( 'Lead', 'dce-vibe' ),
		'dce_phone'   => __( 'Phone', 'dce-vibe' ),
		'dce_area'    => __( 'Area', 'dce-vibe' ),
		'dce_source'  => __( 'From page', 'dce-vibe' ),
		'date'        => $cols['date'],
	);
}
add_action( 'manage_dce_lead_posts_custom_column', 'dce_lead_column', 10, 2 );
function dce_lead_column( $col, $id ) {
	$val = get_post_meta( $id, '_' . $col, true );
	if ( 'dce_phone' === $col && $val ) {
		printf( '<a href="https://wa.me/%1$s" target="_blank" rel="noopener">%2$s</a>', esc_attr( preg_replace( '/\D+/', '', $val ) ), esc_html( $val ) );
	} elseif ( 'dce_source' === $col && $val ) {
		printf( '<a href="%1$s" target="_blank" rel="noopener">%2$s</a>', esc_url( $val ), esc_html( wp_parse_url( $val, PHP_URL_PATH ) ) );
	} else {
		echo esc_html( $val );
	}
}
