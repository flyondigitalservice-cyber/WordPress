<?php
/**
 * Lead capture: enquiry form → saved in WP admin (Enquiries) → email alert → opens WhatsApp chat.
 *
 * Works with AJAX (no page reload) and without JavaScript (admin-post fallback).
 * No nonce on the public form on purpose: pages are cached (LiteSpeed etc.) and stale nonces
 * would silently drop leads. Spam is handled with a honeypot, a timing check and IP rate limiting.
 *
 * @package Tasdir
 */

defined( 'ABSPATH' ) || exit;

/**
 * Lead fields: key => label.
 *
 * @return array
 */
function tasdir_lead_fields() {
	return array(
		'name'     => __( 'Name', 'tasdir' ),
		'phone'    => __( 'WhatsApp / Phone', 'tasdir' ),
		'email'    => __( 'Email', 'tasdir' ),
		'company'  => __( 'Company', 'tasdir' ),
		'country'  => __( 'Country', 'tasdir' ),
		'product'  => __( 'Product', 'tasdir' ),
		'quantity' => __( 'Quantity', 'tasdir' ),
		'message'  => __( 'Message', 'tasdir' ),
		'page'     => __( 'Submitted from', 'tasdir' ),
		'source'   => __( 'Campaign / source', 'tasdir' ),
	);
}

/**
 * Lead statuses.
 *
 * @return array
 */
function tasdir_lead_statuses() {
	return array(
		'new'       => __( 'New', 'tasdir' ),
		'contacted' => __( 'Contacted', 'tasdir' ),
		'quoted'    => __( 'Quotation sent', 'tasdir' ),
		'won'       => __( 'Order won', 'tasdir' ),
		'lost'      => __( 'Lost', 'tasdir' ),
	);
}

/**
 * Register the private "Enquiries" post type.
 */
function tasdir_register_leads() {
	$new = (int) get_transient( 'tasdir_new_leads' );
	register_post_type(
		'tasdir_lead',
		array(
			'labels'              => array(
				'name'          => __( 'Enquiries', 'tasdir' ),
				'singular_name' => __( 'Enquiry', 'tasdir' ),
				'edit_item'     => __( 'Enquiry details', 'tasdir' ),
				'menu_name'     => $new ? sprintf( '%s <span class="awaiting-mod">%d</span>', __( 'Enquiries', 'tasdir' ), $new ) : __( 'Enquiries', 'tasdir' ),
				'all_items'     => __( 'All enquiries', 'tasdir' ),
				'not_found'     => __( 'No enquiries yet.', 'tasdir' ),
			),
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_position'       => 4,
			'menu_icon'           => 'dashicons-whatsapp',
			'exclude_from_search' => true,
			'show_in_rest'        => false,
			'supports'            => array( 'title' ),
			'capability_type'     => 'post',
			'capabilities'        => array( 'create_posts' => 'do_not_allow' ),
			'map_meta_cap'        => true,
		)
	);
}
add_action( 'init', 'tasdir_register_leads' );

/**
 * Client IP (best effort; only used hashed for rate limiting).
 *
 * @return string
 */
function tasdir_client_ip() {
	$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
	foreach ( array( 'HTTP_CF_CONNECTING_IP', 'HTTP_X_REAL_IP' ) as $h ) {
		if ( ! empty( $_SERVER[ $h ] ) ) {
			$ip = sanitize_text_field( wp_unslash( $_SERVER[ $h ] ) );
			break;
		}
	}
	return $ip;
}

/**
 * Handle a lead submission (AJAX + no-JS).
 */
function tasdir_handle_lead() {
	$is_ajax = wp_doing_ajax();
	// phpcs:disable WordPress.Security.NonceVerification.Missing -- public cached form, see file header.
	$post = wp_unslash( $_POST );

	$fail = static function ( $msg, $code = 400 ) use ( $is_ajax ) {
		if ( $is_ajax ) {
			wp_send_json_error( array( 'message' => $msg ), $code );
		}
		wp_die( esc_html( $msg ), esc_html__( 'Enquiry', 'tasdir' ), array( 'response' => (int) $code, 'back_link' => true ) );
	};

	// Honeypot.
	if ( ! empty( $post['website'] ) ) {
		$fail( __( 'Spam detected.', 'tasdir' ) );
	}
	// Submitted faster than a human could type (JS sets the timestamp on page load).
	if ( ! empty( $post['tasdir_t'] ) ) {
		$elapsed = time() - (int) ( (int) $post['tasdir_t'] / 1000 );
		if ( $elapsed >= 0 && $elapsed < 3 ) {
			$fail( __( 'Please take a moment and try again.', 'tasdir' ) );
		}
	}
	// Rate limit: 6 enquiries per 10 minutes per IP.
	$rl_key = 'tasdir_rl_' . md5( tasdir_client_ip() );
	$count  = (int) get_transient( $rl_key );
	if ( $count >= 6 ) {
		$fail( __( 'Too many enquiries. Please message us on WhatsApp directly.', 'tasdir' ), 429 );
	}
	set_transient( $rl_key, $count + 1, 10 * MINUTE_IN_SECONDS );

	$data = array();
	foreach ( array_keys( tasdir_lead_fields() ) as $key ) {
		$raw          = isset( $post[ $key ] ) && is_string( $post[ $key ] ) ? $post[ $key ] : '';
		$data[ $key ] = 'message' === $key ? sanitize_textarea_field( $raw ) : sanitize_text_field( $raw );
		$data[ $key ] = mb_substr( $data[ $key ], 0, 'message' === $key ? 2000 : 200 );
	}
	// phpcs:enable
	$data['email'] = sanitize_email( $data['email'] );
	$data['page']  = esc_url_raw( $data['page'] );

	$digits = preg_replace( '/\D+/', '', $data['phone'] );
	if ( '' === $data['name'] || strlen( $digits ) < 7 || strlen( $digits ) > 16 ) {
		$fail( __( 'Please fill in your name and a valid phone/WhatsApp number.', 'tasdir' ), 422 );
	}

	$title   = trim( implode( ' — ', array_filter( array( $data['name'], $data['country'], $data['product'] ) ) ) );
	$lead_id = wp_insert_post(
		array(
			'post_type'   => 'tasdir_lead',
			'post_status' => 'private',
			'post_title'  => $title,
		),
		true
	);
	if ( is_wp_error( $lead_id ) ) {
		$fail( __( 'Could not save your enquiry. Please message us on WhatsApp.', 'tasdir' ), 500 );
	}
	foreach ( $data as $key => $val ) {
		if ( '' !== $val ) {
			update_post_meta( $lead_id, '_lead_' . $key, $val );
		}
	}
	update_post_meta( $lead_id, '_lead_status', 'new' );
	set_transient( 'tasdir_new_leads', (int) get_transient( 'tasdir_new_leads' ) + 1, 0 );

	tasdir_lead_notify( $lead_id, $data );

	$wa = tasdir_wa_number() ? tasdir_wa_url( tasdir_lead_wa_text( $data ) ) : '';

	if ( $is_ajax ) {
		wp_send_json_success(
			array(
				'id' => $lead_id,
				'wa' => $wa,
			)
		);
	}
	// No-JS: go straight to WhatsApp, or back to the page with a thank-you flag.
	$back = $data['page'] ? $data['page'] : home_url( '/' );
	wp_redirect( $wa ? $wa : add_query_arg( 'enquiry', 'sent', $back ) . '#enquiry' ); // phpcs:ignore WordPress.Security.SafeRedirect.wp_redirect_wp_redirect -- wa.me is external by design.
	exit;
}
add_action( 'wp_ajax_tasdir_lead', 'tasdir_handle_lead' );
add_action( 'wp_ajax_nopriv_tasdir_lead', 'tasdir_handle_lead' );
add_action( 'admin_post_tasdir_lead', 'tasdir_handle_lead' );
add_action( 'admin_post_nopriv_tasdir_lead', 'tasdir_handle_lead' );

/**
 * WhatsApp message built from the form.
 *
 * @param array $d Lead data.
 * @return string
 */
function tasdir_lead_wa_text( $d ) {
	$lines   = array( __( 'Hello Tasdir, I would like a quotation.', 'tasdir' ), '' );
	$labels  = tasdir_lead_fields();
	foreach ( array( 'name', 'company', 'country', 'product', 'quantity', 'email', 'message' ) as $k ) {
		if ( ! empty( $d[ $k ] ) ) {
			$lines[] = '*' . $labels[ $k ] . ':* ' . $d[ $k ];
		}
	}
	return implode( "\n", $lines );
}

/**
 * Email alert for a new lead.
 *
 * @param int   $lead_id Lead ID.
 * @param array $d       Data.
 */
function tasdir_lead_notify( $lead_id, $d ) {
	$to = tasdir_opt( 'lead_email' );
	if ( ! is_email( $to ) ) {
		$to = get_option( 'admin_email' );
	}
	$body = '';
	foreach ( tasdir_lead_fields() as $k => $label ) {
		if ( ! empty( $d[ $k ] ) ) {
			$body .= $label . ': ' . $d[ $k ] . "\n";
		}
	}
	$digits = preg_replace( '/\D+/', '', $d['phone'] );
	$body  .= "\n" . __( 'Reply on WhatsApp:', 'tasdir' ) . ' https://wa.me/' . $digits . "\n";
	$body  .= __( 'View in dashboard:', 'tasdir' ) . ' ' . admin_url( 'post.php?post=' . (int) $lead_id . '&action=edit' ) . "\n";

	$headers = array();
	if ( is_email( $d['email'] ) ) {
		$headers[] = 'Reply-To: ' . $d['name'] . ' <' . $d['email'] . '>';
	}
	/* translators: 1: name, 2: product. */
	$subject = sprintf( __( 'New export enquiry: %1$s (%2$s)', 'tasdir' ), $d['name'], $d['product'] ? $d['product'] : __( 'general', 'tasdir' ) );
	wp_mail( $to, $subject, $body, $headers );
}

/* ---------- Admin: list columns, detail box, status, CSV export ---------- */

/**
 * Columns.
 *
 * @return array
 */
function tasdir_lead_columns() {
	return array(
		'cb'       => '<input type="checkbox">',
		'title'    => __( 'Enquiry', 'tasdir' ),
		'phone'    => __( 'WhatsApp / Phone', 'tasdir' ),
		'product'  => __( 'Product', 'tasdir' ),
		'quantity' => __( 'Quantity', 'tasdir' ),
		'status'   => __( 'Status', 'tasdir' ),
		'date'     => __( 'Date', 'tasdir' ),
	);
}
add_filter( 'manage_tasdir_lead_posts_columns', 'tasdir_lead_columns' );

/**
 * Column content.
 *
 * @param string $col     Column.
 * @param int    $post_id Post ID.
 */
function tasdir_lead_column_content( $col, $post_id ) {
	if ( 'status' === $col ) {
		$s = get_post_meta( $post_id, '_lead_status', true );
		$l = tasdir_lead_statuses();
		echo esc_html( isset( $l[ $s ] ) ? $l[ $s ] : $l['new'] );
		return;
	}
	$v = get_post_meta( $post_id, '_lead_' . $col, true );
	if ( 'phone' === $col && $v ) {
		printf( '<a href="https://wa.me/%1$s" target="_blank" rel="noopener">%2$s</a>', esc_attr( preg_replace( '/\D+/', '', $v ) ), esc_html( $v ) );
		return;
	}
	echo esc_html( $v );
}
add_action( 'manage_tasdir_lead_posts_custom_column', 'tasdir_lead_column_content', 10, 2 );

/**
 * Show private leads in the list (they are stored private).
 *
 * @param WP_Query $q Query.
 */
function tasdir_lead_admin_query( $q ) {
	if ( is_admin() && $q->is_main_query() && 'tasdir_lead' === $q->get( 'post_type' ) ) {
		delete_transient( 'tasdir_new_leads' );
	}
}
add_action( 'pre_get_posts', 'tasdir_lead_admin_query' );

/**
 * Detail + status meta boxes.
 */
function tasdir_lead_metaboxes() {
	add_meta_box( 'tasdir_lead_details', __( 'Enquiry details', 'tasdir' ), 'tasdir_lead_details_html', 'tasdir_lead', 'normal', 'high' );
	add_meta_box( 'tasdir_lead_status', __( 'Follow-up', 'tasdir' ), 'tasdir_lead_status_html', 'tasdir_lead', 'side', 'high' );
}
add_action( 'add_meta_boxes', 'tasdir_lead_metaboxes' );

/**
 * Details box.
 *
 * @param WP_Post $post Post.
 */
function tasdir_lead_details_html( $post ) {
	echo '<table class="widefat striped"><tbody>';
	foreach ( tasdir_lead_fields() as $k => $label ) {
		$v = get_post_meta( $post->ID, '_lead_' . $k, true );
		if ( '' === $v ) {
			continue;
		}
		printf( '<tr><th style="width:180px">%1$s</th><td>%2$s</td></tr>', esc_html( $label ), nl2br( esc_html( $v ) ) );
	}
	echo '</tbody></table>';
}

/**
 * Status box.
 *
 * @param WP_Post $post Post.
 */
function tasdir_lead_status_html( $post ) {
	wp_nonce_field( 'tasdir_lead_status', 'tasdir_lead_nonce' );
	$current = get_post_meta( $post->ID, '_lead_status', true );
	echo '<p><select name="tasdir_lead_status" style="width:100%">';
	foreach ( tasdir_lead_statuses() as $k => $label ) {
		printf( '<option value="%1$s"%2$s>%3$s</option>', esc_attr( $k ), selected( $current, $k, false ), esc_html( $label ) );
	}
	echo '</select></p>';
	echo '<p><label for="tasdir_lead_note">' . esc_html__( 'Internal note', 'tasdir' ) . '</label><textarea id="tasdir_lead_note" name="tasdir_lead_note" rows="4" style="width:100%">' . esc_textarea( get_post_meta( $post->ID, '_lead_note', true ) ) . '</textarea></p>';
	$phone = preg_replace( '/\D+/', '', (string) get_post_meta( $post->ID, '_lead_phone', true ) );
	if ( $phone ) {
		printf( '<p><a class="button button-primary" style="width:100%%;text-align:center" target="_blank" rel="noopener" href="https://wa.me/%s">%s</a></p>', esc_attr( $phone ), esc_html__( 'Reply on WhatsApp', 'tasdir' ) );
	}
}

/**
 * Save status + note.
 *
 * @param int $post_id Post ID.
 */
function tasdir_save_lead( $post_id ) {
	if ( ! isset( $_POST['tasdir_lead_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['tasdir_lead_nonce'] ) ), 'tasdir_lead_status' ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	$status = isset( $_POST['tasdir_lead_status'] ) ? sanitize_key( $_POST['tasdir_lead_status'] ) : 'new';
	if ( isset( tasdir_lead_statuses()[ $status ] ) ) {
		update_post_meta( $post_id, '_lead_status', $status );
	}
	update_post_meta( $post_id, '_lead_note', isset( $_POST['tasdir_lead_note'] ) ? sanitize_textarea_field( wp_unslash( $_POST['tasdir_lead_note'] ) ) : '' );
}
add_action( 'save_post_tasdir_lead', 'tasdir_save_lead' );

/**
 * Export button above the list.
 *
 * @param string $which top|bottom.
 */
function tasdir_lead_export_button( $which ) {
	global $typenow;
	if ( 'tasdir_lead' !== $typenow || 'top' !== $which ) {
		return;
	}
	$url = wp_nonce_url( admin_url( 'admin-post.php?action=tasdir_export_leads' ), 'tasdir_export_leads' );
	printf( '<div class="alignleft actions"><a class="button" href="%s">%s</a></div>', esc_url( $url ), esc_html__( 'Export CSV', 'tasdir' ) );
}
add_action( 'manage_posts_extra_tablenav', 'tasdir_lead_export_button' );

/**
 * Stream all leads as CSV.
 */
function tasdir_export_leads() {
	if ( ! current_user_can( 'edit_posts' ) ) {
		wp_die( esc_html__( 'Not allowed.', 'tasdir' ) );
	}
	check_admin_referer( 'tasdir_export_leads' );

	nocache_headers();
	header( 'Content-Type: text/csv; charset=utf-8' );
	header( 'Content-Disposition: attachment; filename=tasdir-enquiries-' . gmdate( 'Y-m-d' ) . '.csv' );

	$out    = fopen( 'php://output', 'w' );
	$fields = tasdir_lead_fields();
	fwrite( $out, "\xEF\xBB\xBF" ); // UTF-8 BOM for Excel.
	fputcsv( $out, array_merge( array( __( 'Date', 'tasdir' ) ), array_values( $fields ), array( __( 'Status', 'tasdir' ), __( 'Note', 'tasdir' ) ) ) );

	$ids = get_posts(
		array(
			'post_type'      => 'tasdir_lead',
			'post_status'    => 'any',
			'posts_per_page' => -1,
			'fields'         => 'ids',
		)
	);
	foreach ( $ids as $id ) {
		$row = array( get_the_date( 'Y-m-d H:i', $id ) );
		foreach ( array_keys( $fields ) as $k ) {
			$row[] = tasdir_csv_safe( get_post_meta( $id, '_lead_' . $k, true ) );
		}
		$row[] = get_post_meta( $id, '_lead_status', true );
		$row[] = tasdir_csv_safe( get_post_meta( $id, '_lead_note', true ) );
		fputcsv( $out, $row );
	}
	fclose( $out ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose
	exit;
}
add_action( 'admin_post_tasdir_export_leads', 'tasdir_export_leads' );

/**
 * Neutralise spreadsheet formula injection.
 *
 * @param string $v Value.
 * @return string
 */
function tasdir_csv_safe( $v ) {
	$v = (string) $v;
	return ( '' !== $v && in_array( $v[0], array( '=', '+', '-', '@', "\t", "\r" ), true ) ) ? "'" . $v : $v;
}
