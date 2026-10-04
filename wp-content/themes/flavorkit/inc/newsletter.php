<?php
/**
 * Built-in newsletter capture used when no form shortcode is configured.
 *
 * Emails are stored in the `flavorkit_subscribers` option and listed under
 * Tools → Subscribers, with a CSV export for importing into any email tool.
 *
 * @package FlavorKit
 */

defined( 'ABSPATH' ) || exit;

/**
 * Handle form submissions (logged-in and guests).
 */
function flavorkit_handle_subscribe() {
	$redirect = wp_get_referer() ? wp_get_referer() : home_url( '/' );
	$redirect = remove_query_arg( 'fk_subscribed', $redirect );

	if ( ! isset( $_POST['flavorkit_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['flavorkit_nonce'] ) ), 'flavorkit_subscribe' ) ) {
		wp_safe_redirect( add_query_arg( 'fk_subscribed', 'error', $redirect ) . '#newsletter' );
		exit;
	}

	// Honeypot: bots fill every field.
	if ( ! empty( $_POST['fk_website'] ) ) {
		wp_safe_redirect( add_query_arg( 'fk_subscribed', '1', $redirect ) . '#newsletter' );
		exit;
	}

	$email = isset( $_POST['fk_email'] ) ? sanitize_email( wp_unslash( $_POST['fk_email'] ) ) : '';
	if ( ! is_email( $email ) ) {
		wp_safe_redirect( add_query_arg( 'fk_subscribed', 'error', $redirect ) . '#newsletter' );
		exit;
	}

	$list = get_option( 'flavorkit_subscribers', array() );
	if ( ! is_array( $list ) ) {
		$list = array();
	}
	if ( ! isset( $list[ $email ] ) ) {
		$list[ $email ] = current_time( 'mysql' );
		update_option( 'flavorkit_subscribers', $list, false );
	}

	wp_safe_redirect( add_query_arg( 'fk_subscribed', '1', $redirect ) . '#newsletter' );
	exit;
}
add_action( 'admin_post_flavorkit_subscribe', 'flavorkit_handle_subscribe' );
add_action( 'admin_post_nopriv_flavorkit_subscribe', 'flavorkit_handle_subscribe' );

/**
 * Tools → Subscribers admin page.
 */
function flavorkit_subscribers_menu() {
	add_management_page(
		__( 'Newsletter subscribers', 'flavorkit' ),
		__( 'Subscribers', 'flavorkit' ),
		'manage_options',
		'flavorkit-subscribers',
		'flavorkit_subscribers_page'
	);
}
add_action( 'admin_menu', 'flavorkit_subscribers_menu' );

/**
 * CSV export.
 */
function flavorkit_subscribers_export() {
	if ( ! isset( $_GET['page'], $_GET['fk_export'] ) || 'flavorkit-subscribers' !== $_GET['page'] ) {
		return;
	}
	if ( ! current_user_can( 'manage_options' ) || ! check_admin_referer( 'flavorkit_export' ) ) {
		return;
	}
	$list = (array) get_option( 'flavorkit_subscribers', array() );
	nocache_headers();
	header( 'Content-Type: text/csv; charset=utf-8' );
	header( 'Content-Disposition: attachment; filename=subscribers-' . gmdate( 'Y-m-d' ) . '.csv' );
	$out = fopen( 'php://output', 'w' );
	fputcsv( $out, array( 'email', 'subscribed_at' ) );
	foreach ( $list as $email => $date ) {
		fputcsv( $out, array( $email, $date ) );
	}
	fclose( $out ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose
	exit;
}
add_action( 'admin_init', 'flavorkit_subscribers_export' );

/**
 * Render the subscribers page.
 */
function flavorkit_subscribers_page() {
	$list = (array) get_option( 'flavorkit_subscribers', array() );
	arsort( $list );
	$export = wp_nonce_url( admin_url( 'tools.php?page=flavorkit-subscribers&fk_export=1' ), 'flavorkit_export' );
	?>
	<div class="wrap">
		<h1 class="wp-heading-inline"><?php esc_html_e( 'Newsletter subscribers', 'flavorkit' ); ?></h1>
		<a href="<?php echo esc_url( $export ); ?>" class="page-title-action"><?php esc_html_e( 'Export CSV', 'flavorkit' ); ?></a>
		<p><?php echo esc_html( sprintf( /* translators: %d count */ _n( '%d subscriber', '%d subscribers', count( $list ), 'flavorkit' ), count( $list ) ) ); ?></p>
		<table class="widefat striped">
			<thead><tr><th><?php esc_html_e( 'Email', 'flavorkit' ); ?></th><th><?php esc_html_e( 'Subscribed', 'flavorkit' ); ?></th></tr></thead>
			<tbody>
			<?php if ( ! $list ) : ?>
				<tr><td colspan="2"><?php esc_html_e( 'No subscribers yet.', 'flavorkit' ); ?></td></tr>
			<?php endif; ?>
			<?php foreach ( $list as $email => $date ) : ?>
				<tr><td><?php echo esc_html( $email ); ?></td><td><?php echo esc_html( $date ); ?></td></tr>
			<?php endforeach; ?>
			</tbody>
		</table>
	</div>
	<?php
}
