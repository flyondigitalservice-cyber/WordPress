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
function zobo_form_stages() {
	return array(
		'idea'    => __( 'Just an idea', 'zobo' ),
		'brand'   => __( 'Have a brand, not selling yet', 'zobo' ),
		'selling' => __( 'Selling, want to grow', 'zobo' ),
	);
}

/**
 * Handle the enquiry form and email it to the Customizer address.
 */
function zobo_handle_enquiry() {
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
	$message = isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '';

	if ( '' === $name || ! is_email( $email ) ) {
		wp_safe_redirect( add_query_arg( 'enquiry', 'invalid', $redirect ) . '#contact' );
		exit;
	}

	$stages = zobo_form_stages();
	$stage  = isset( $stages[ $stage ] ) ? $stages[ $stage ] : '';
	$to     = zobo_opt( 'email' );
	if ( ! is_email( $to ) ) {
		$to = get_option( 'admin_email' );
	}

	$body = sprintf(
		"Name: %s\nEmail: %s\nPhone: %s\nStage: %s\n\n%s",
		$name,
		$email,
		$phone,
		$stage,
		$message
	);

	$sent = wp_mail(
		$to,
		/* translators: %s: sender name */
		sprintf( __( 'New launch enquiry from %s', 'zobo' ), $name ),
		$body,
		array( 'Reply-To: ' . $name . ' <' . $email . '>' )
	);

	wp_safe_redirect( add_query_arg( 'enquiry', $sent ? 'sent' : 'error', $redirect ) . '#contact' );
	exit;
}
add_action( 'admin_post_nopriv_zobo_enquiry', 'zobo_handle_enquiry' );
add_action( 'admin_post_zobo_enquiry', 'zobo_handle_enquiry' );
