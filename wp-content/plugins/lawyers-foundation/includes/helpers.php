<?php
/**
 * Shared helpers.
 *
 * @package Lawyers_Foundation
 */

defined( 'ABSPATH' ) || exit;

/**
 * Default plugin settings.
 *
 * @return array
 */
function lf_default_settings() {
	return array(
		'org_name'             => get_bloginfo( 'name' ),
		'event_cycle'          => "Spring Summit\nAutumn Gala",
		'directory_visibility' => 'members',
		'auto_approve'         => 0,
		'notify_email'         => get_option( 'admin_email' ),
		'dues'                 => 'Annual membership dues: contact the secretariat.',
		'code_of_conduct'      => 'I confirm I am a lawyer in good standing with my bar or law society, and I agree to uphold the foundation\'s mission and code of professional conduct.',
	);
}

/**
 * Read one setting.
 *
 * @param string $key Setting key.
 * @return mixed
 */
function lf_opt( $key ) {
	$settings = wp_parse_args( (array) get_option( 'lf_settings', array() ), lf_default_settings() );
	return isset( $settings[ $key ] ) ? $settings[ $key ] : null;
}

/**
 * Organisation display name.
 *
 * @return string
 */
function lf_org_name() {
	$name = trim( (string) lf_opt( 'org_name' ) );
	return '' !== $name ? $name : get_bloginfo( 'name' );
}

/**
 * The named events in the foundation's yearly cycle (2 or 3 per year).
 *
 * @return string[]
 */
function lf_event_cycle() {
	$lines = preg_split( '/\r\n|\r|\n/', (string) lf_opt( 'event_cycle' ) );
	$lines = array_values( array_filter( array_map( 'trim', $lines ) ) );
	return $lines ? $lines : array( 'Spring Summit', 'Autumn Gala' );
}

/**
 * Whether a user is an approved member (admins count as members).
 *
 * @param int|null $user_id User ID; current user when null.
 * @return bool
 */
function lf_is_member( $user_id = null ) {
	$user = $user_id ? get_userdata( $user_id ) : wp_get_current_user();
	if ( ! $user || ! $user->exists() ) {
		return false;
	}
	return in_array( 'lf_member', (array) $user->roles, true ) || user_can( $user, 'manage_options' );
}

/**
 * Whether a user's application is awaiting approval.
 *
 * @param int|null $user_id User ID; current user when null.
 * @return bool
 */
function lf_is_pending( $user_id = null ) {
	$user = $user_id ? get_userdata( $user_id ) : wp_get_current_user();
	return $user && $user->exists() && in_array( 'lf_pending', (array) $user->roles, true );
}

/**
 * Practice areas offered in forms and directory filters.
 *
 * @return string[]
 */
function lf_practice_areas() {
	return apply_filters(
		'lf_practice_areas',
		array(
			'Civil Litigation',
			'Constitutional & Human Rights',
			'Corporate & Commercial',
			'Criminal Defence',
			'Employment & Labour',
			'Environmental',
			'Family Law',
			'Immigration & Refugee',
			'Intellectual Property',
			'Property & Real Estate',
			'Tax',
			'Other',
		)
	);
}

/**
 * URL of one of the pages the plugin created.
 *
 * @param string $key join|directory|events|dashboard|work.
 * @return string
 */
function lf_page_url( $key ) {
	$pages = (array) get_option( 'lf_pages', array() );
	if ( ! empty( $pages[ $key ] ) && get_post_status( $pages[ $key ] ) ) {
		return get_permalink( $pages[ $key ] );
	}
	return home_url( '/' );
}

/**
 * Render a one-shot notice passed via the lf_msg query arg.
 *
 * @return string
 */
function lf_flash() {
	if ( empty( $_GET['lf_msg'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return '';
	}
	$messages = array(
		'applied'      => array( 'success', __( 'Thank you! Your membership application has been received. We will email you once it has been reviewed.', 'lawyers-foundation' ) ),
		'welcome'      => array( 'success', __( 'Welcome to the foundation! Your membership is active.', 'lawyers-foundation' ) ),
		'rsvp'         => array( 'success', __( 'You are registered for this event. See you there!', 'lawyers-foundation' ) ),
		'rsvp_cancel'  => array( 'info', __( 'Your registration has been cancelled.', 'lawyers-foundation' ) ),
		'rsvp_full'    => array( 'error', __( 'Sorry, this event is fully booked.', 'lawyers-foundation' ) ),
		'rsvp_closed'  => array( 'error', __( 'Registration for this event is closed.', 'lawyers-foundation' ) ),
		'profile'      => array( 'success', __( 'Your profile has been updated.', 'lawyers-foundation' ) ),
		'hours'        => array( 'success', __( 'Thank you! Your service hours were submitted for verification.', 'lawyers-foundation' ) ),
		'hours_error'  => array( 'error', __( 'Please enter a valid date, number of hours and description.', 'lawyers-foundation' ) ),
	);
	$key = sanitize_key( wp_unslash( $_GET['lf_msg'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( ! isset( $messages[ $key ] ) ) {
		return '';
	}
	return sprintf( '<div class="lf-notice lf-notice--%1$s" role="status">%2$s</div>', esc_attr( $messages[ $key ][0] ), esc_html( $messages[ $key ][1] ) );
}

/**
 * Redirect back to the referring page with a message.
 *
 * @param string      $msg Message key.
 * @param string|null $url Target; defaults to the referer.
 */
function lf_redirect_with( $msg, $url = null ) {
	$url = $url ? $url : wp_get_referer();
	$url = $url ? $url : home_url( '/' );
	wp_safe_redirect( add_query_arg( 'lf_msg', $msg, remove_query_arg( 'lf_msg', $url ) ) );
	exit;
}

/**
 * Format a stored "Y-m-d H:i" event date for display.
 *
 * @param string $value  Stored value.
 * @param string $format Optional date format.
 * @return string
 */
function lf_format_date( $value, $format = '' ) {
	if ( ! $value ) {
		return '';
	}
	$date = date_create_immutable_from_format( 'Y-m-d H:i', $value, wp_timezone() );
	if ( ! $date ) {
		return '';
	}
	$format = $format ? $format : get_option( 'date_format' ) . ' · ' . get_option( 'time_format' );
	return wp_date( $format, $date->getTimestamp() );
}
