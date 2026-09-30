<?php
/**
 * Front-page enquiry form handler.
 *
 * @package Zobo
 */

/**
 * Stages a founder can pick on the form.
 *
 * @return array
 */
function zobod2c_form_stages() {
	return array(
		'idea'    => __( 'Just an idea', 'zobo-d2c' ),
		'brand'   => __( 'Have a brand, not selling yet', 'zobo-d2c' ),
		'selling' => __( 'Selling, want to grow', 'zobo-d2c' ),
	);
}

/**
 * Product categories a founder can pick on the form.
 *
 * @return array
 */
function zobod2c_form_categories() {
	$cats = wp_list_pluck( zobod2c_industries(), 'title' );
	$cats[] = __( 'Other', 'zobo-d2c' );
	return $cats;
}

/**
 * Handle the enquiry form and email it to the Customizer address.
 */
function zobod2c_handle_enquiry() {
	$redirect = wp_get_referer() ? wp_get_referer() : home_url( '/' );
	$redirect = remove_query_arg( 'enquiry', $redirect );

	if ( ! isset( $_POST['zobo_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['zobo_nonce'] ) ), 'zobo_enquiry' ) ) {
		wp_safe_redirect( add_query_arg( 'enquiry', 'error', $redirect ) . '#contact' );
		exit;
	}

	// Honeypot: real visitors never fill this in.
	if ( ! empty( $_POST['zobo_company_site'] ) ) {
		wp_safe_redirect( add_query_arg( 'enquiry', 'sent', $redirect ) . '#contact' );
		exit;
	}

	$name    = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
	$email   = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$phone   = isset( $_POST['phone'] ) ? sanitize_text_field( wp_unslash( $_POST['phone'] ) ) : '';
	$stage   = isset( $_POST['stage'] ) ? sanitize_key( wp_unslash( $_POST['stage'] ) ) : '';
	$category = isset( $_POST['category'] ) ? sanitize_text_field( wp_unslash( $_POST['category'] ) ) : '';
	$message = isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '';

	if ( '' === $name || ! is_email( $email ) ) {
		wp_safe_redirect( add_query_arg( 'enquiry', 'invalid', $redirect ) . '#contact' );
		exit;
	}

	$stages = zobod2c_form_stages();
	$stage  = isset( $stages[ $stage ] ) ? $stages[ $stage ] : '';
	if ( ! in_array( $category, zobod2c_form_categories(), true ) ) {
		$category = '';
	}
	$to     = zobod2c_opt( 'email' );
	if ( ! is_email( $to ) ) {
		$to = get_option( 'admin_email' );
	}

	$body = sprintf(
		"Name: %s\nEmail: %s\nPhone: %s\nCategory: %s\nStage: %s\n\n%s",
		$name,
		$email,
		$phone,
		$category,
		$stage,
		$message
	);

	$sent = wp_mail(
		$to,
		/* translators: %s: sender name */
		sprintf( __( 'New launch enquiry from %s', 'zobo-d2c' ), $name ),
		$body,
		array( 'Reply-To: ' . $name . ' <' . $email . '>' )
	);

	wp_safe_redirect( add_query_arg( 'enquiry', $sent ? 'sent' : 'error', $redirect ) . '#contact' );
	exit;
}
add_action( 'admin_post_nopriv_zobo_enquiry', 'zobod2c_handle_enquiry' );
add_action( 'admin_post_zobo_enquiry', 'zobod2c_handle_enquiry' );
