<?php
/**
 * Native newsletter capture — no plugin required.
 *
 * Subscribers are stored as a private post type (Dastaan → Subscribers) with CSV export.
 * If an external action URL is set in the Customizer, the form posts there instead.
 *
 * @package Dastaan
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register the private subscriber post type.
 */
function dastaan_register_subscriber_cpt() {
	register_post_type(
		'dastaan_subscriber',
		array(
			'labels'              => array(
				'name'          => __( 'Subscribers', 'dastaan' ),
				'singular_name' => __( 'Subscriber', 'dastaan' ),
				'menu_name'     => __( 'Subscribers', 'dastaan' ),
			),
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => 'dastaan-welcome',
			'show_in_rest'        => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
			'capability_type'     => 'post',
			'capabilities'        => array( 'create_posts' => 'do_not_allow' ),
			'map_meta_cap'        => true,
			'supports'            => array( 'title' ),
			'rewrite'             => false,
			'query_var'           => false,
		)
	);
}
add_action( 'init', 'dastaan_register_subscriber_cpt' );

/**
 * AJAX + no-JS handler.
 */
function dastaan_handle_subscribe() {
	$is_ajax = wp_doing_ajax();

	$respond = function ( $ok, $message ) use ( $is_ajax ) {
		if ( $is_ajax ) {
			$ok ? wp_send_json_success( array( 'message' => $message ) ) : wp_send_json_error( array( 'message' => $message ), 400 );
		}
		$url = wp_get_referer() ? wp_get_referer() : home_url( '/' );
		wp_safe_redirect( add_query_arg( 'newsletter', $ok ? 'ok' : 'error', $url ) );
		exit;
	};

	if ( ! isset( $_POST['dastaan_nl_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['dastaan_nl_nonce'] ) ), 'dastaan_newsletter' ) ) {
		$respond( false, __( 'Security check failed. Please refresh and try again.', 'dastaan' ) );
	}

	// Honeypot: real users never fill this.
	if ( ! empty( $_POST['website_url'] ) ) {
		$respond( true, dastaan_option( 'newsletter_success' ) );
	}

	$email = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	if ( ! $email || ! is_email( $email ) ) {
		$respond( false, __( 'Please enter a valid email address.', 'dastaan' ) );
	}

	// Rate limit: 5 attempts / 10 minutes per IP (hashed, never stored raw).
	$ip_hash = md5( ( isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '' ) . wp_salt() );
	$key     = 'dastaan_nl_' . $ip_hash;
	$count   = (int) get_transient( $key );
	if ( $count >= 5 ) {
		$respond( false, __( 'Too many attempts. Please try again later.', 'dastaan' ) );
	}
	set_transient( $key, $count + 1, 10 * MINUTE_IN_SECONDS );

	// Duplicate check.
	$existing = get_posts(
		array(
			'post_type'              => 'dastaan_subscriber',
			'title'                  => $email,
			'post_status'            => 'any',
			'posts_per_page'         => 1,
			'fields'                 => 'ids',
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		)
	);
	if ( ! empty( $existing ) ) {
		$respond( true, __( 'You are already on the list — shukriya!', 'dastaan' ) );
	}

	$id = wp_insert_post(
		array(
			'post_type'   => 'dastaan_subscriber',
			'post_title'  => $email,
			'post_status' => 'publish',
			'meta_input'  => array(
				'_source'     => isset( $_POST['source'] ) ? sanitize_text_field( wp_unslash( $_POST['source'] ) ) : 'footer',
				'_subscribed' => current_time( 'mysql' ),
				'_consent'    => 1,
			),
		),
		true
	);

	if ( is_wp_error( $id ) ) {
		$respond( false, __( 'Something went wrong. Please try again.', 'dastaan' ) );
	}

	/**
	 * Fires after a new subscriber is saved (connect to Mailchimp, WhatsApp, etc.).
	 *
	 * @param string $email Email.
	 * @param int    $id    Post ID.
	 */
	do_action( 'dastaan_new_subscriber', $email, $id );

	$respond( true, dastaan_option( 'newsletter_success' ) );
}
add_action( 'wp_ajax_dastaan_subscribe', 'dastaan_handle_subscribe' );
add_action( 'wp_ajax_nopriv_dastaan_subscribe', 'dastaan_handle_subscribe' );
add_action( 'admin_post_dastaan_subscribe', 'dastaan_handle_subscribe' );
add_action( 'admin_post_nopriv_dastaan_subscribe', 'dastaan_handle_subscribe' );

/**
 * Admin columns.
 *
 * @param array $cols Columns.
 * @return array
 */
function dastaan_subscriber_columns( $cols ) {
	return array(
		'cb'         => $cols['cb'],
		'title'      => __( 'Email', 'dastaan' ),
		'source'     => __( 'Source', 'dastaan' ),
		'subscribed' => __( 'Subscribed', 'dastaan' ),
	);
}
add_filter( 'manage_dastaan_subscriber_posts_columns', 'dastaan_subscriber_columns' );

add_action(
	'manage_dastaan_subscriber_posts_custom_column',
	function ( $col, $id ) {
		if ( 'source' === $col ) {
			echo esc_html( get_post_meta( $id, '_source', true ) );
		} elseif ( 'subscribed' === $col ) {
			echo esc_html( get_post_meta( $id, '_subscribed', true ) );
		}
	},
	10,
	2
);

/**
 * CSV export (Dastaan → Subscribers → Export button).
 */
function dastaan_export_subscribers() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'Not allowed.', 'dastaan' ) );
	}
	check_admin_referer( 'dastaan_export_subscribers' );

	$posts = get_posts(
		array(
			'post_type'      => 'dastaan_subscriber',
			'posts_per_page' => -1,
			'post_status'    => 'publish',
			'orderby'        => 'date',
			'order'          => 'DESC',
		)
	);
	nocache_headers();
	header( 'Content-Type: text/csv; charset=utf-8' );
	header( 'Content-Disposition: attachment; filename=dastaan-subscribers-' . gmdate( 'Y-m-d' ) . '.csv' );
	$out = fopen( 'php://output', 'w' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fopen
	fputcsv( $out, array( 'email', 'source', 'subscribed_at' ) );
	foreach ( $posts as $p ) {
		fputcsv( $out, array( $p->post_title, get_post_meta( $p->ID, '_source', true ), get_post_meta( $p->ID, '_subscribed', true ) ) );
	}
	fclose( $out ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose
	exit;
}
add_action( 'admin_post_dastaan_export_subscribers', 'dastaan_export_subscribers' );

add_action(
	'admin_notices',
	function () {
		$screen = get_current_screen();
		if ( ! $screen || 'edit-dastaan_subscriber' !== $screen->id || ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$url = wp_nonce_url( admin_url( 'admin-post.php?action=dastaan_export_subscribers' ), 'dastaan_export_subscribers' );
		echo '<div class="notice notice-info" style="display:flex;align-items:center;gap:12px"><p>' . esc_html__( 'Newsletter subscribers captured by the theme footer form.', 'dastaan' ) . '</p><a class="button button-primary" href="' . esc_url( $url ) . '">' . esc_html__( 'Export CSV', 'dastaan' ) . '</a></div>';
	}
);
