<?php
/**
 * Admin menu, overview screen and settings.
 *
 * @package Lawyers_Foundation
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register the Foundation admin menu.
 */
function lf_admin_menu() {
	$pending = count( get_users( array( 'role' => 'lf_pending', 'fields' => 'ID' ) ) );
	$bubble  = $pending ? ' <span class="awaiting-mod">' . (int) $pending . '</span>' : '';

	add_menu_page( __( 'Foundation', 'lawyers-foundation' ), __( 'Foundation', 'lawyers-foundation' ) . $bubble, 'manage_options', 'lf-foundation', 'lf_render_overview', 'dashicons-bank', 26 );
	add_submenu_page( 'lf-foundation', __( 'Overview', 'lawyers-foundation' ), __( 'Overview', 'lawyers-foundation' ), 'manage_options', 'lf-foundation', 'lf_render_overview' );
	add_submenu_page( 'lf-foundation', __( 'Settings', 'lawyers-foundation' ), __( 'Settings', 'lawyers-foundation' ), 'manage_options', 'lf-settings', 'lf_render_settings' );
}
add_action( 'admin_menu', 'lf_admin_menu', 9 );

/**
 * Overview screen.
 */
function lf_render_overview() {
	$members  = count( get_users( array( 'role' => 'lf_member', 'fields' => 'ID' ) ) );
	$pending  = get_users( array( 'role' => 'lf_pending' ) );
	$hours    = lf_total_service_hours();
	$upcoming = lf_get_events( 'upcoming', 5 );
	$logs     = wp_count_posts( 'lf_service' );
	?>
	<div class="wrap">
		<h1><?php echo esc_html( lf_org_name() ); ?> — <?php esc_html_e( 'Overview', 'lawyers-foundation' ); ?></h1>
		<div style="display:flex;gap:16px;flex-wrap:wrap;margin:16px 0;">
			<?php
			$cards = array(
				__( 'Active members', 'lawyers-foundation' )      => $members,
				__( 'Applications pending', 'lawyers-foundation' ) => count( $pending ),
				__( 'Verified pro bono hours', 'lawyers-foundation' ) => number_format_i18n( $hours, 1 ),
				__( 'Hour logs to review', 'lawyers-foundation' ) => isset( $logs->pending ) ? (int) $logs->pending : 0,
			);
			foreach ( $cards as $label => $value ) {
				printf( '<div class="card" style="min-width:180px;margin:0"><p style="font-size:28px;margin:8px 0">%s</p><p>%s</p></div>', esc_html( $value ), esc_html( $label ) );
			}
			?>
		</div>

		<h2><?php esc_html_e( 'Applications awaiting approval', 'lawyers-foundation' ); ?></h2>
		<?php if ( ! $pending ) : ?>
			<p><?php esc_html_e( 'No pending applications.', 'lawyers-foundation' ); ?></p>
		<?php else : ?>
			<table class="widefat striped">
				<thead><tr><th><?php esc_html_e( 'Name', 'lawyers-foundation' ); ?></th><th><?php esc_html_e( 'Bar / licence no.', 'lawyers-foundation' ); ?></th><th><?php esc_html_e( 'Jurisdiction', 'lawyers-foundation' ); ?></th><th><?php esc_html_e( 'Applied', 'lawyers-foundation' ); ?></th><th></th></tr></thead>
				<tbody>
				<?php foreach ( $pending as $user ) : ?>
					<tr>
						<td><a href="<?php echo esc_url( get_edit_user_link( $user->ID ) ); ?>"><?php echo esc_html( $user->display_name ); ?></a><br><small><?php echo esc_html( $user->user_email ); ?></small></td>
						<td><?php echo esc_html( get_user_meta( $user->ID, 'lf_bar_number', true ) ); ?></td>
						<td><?php echo esc_html( get_user_meta( $user->ID, 'lf_jurisdiction', true ) ); ?></td>
						<td><?php echo esc_html( mysql2date( get_option( 'date_format' ), $user->user_registered ) ); ?></td>
						<td><a class="button button-primary" href="<?php echo esc_url( lf_approve_url( $user->ID ) ); ?>"><?php esc_html_e( 'Approve', 'lawyers-foundation' ); ?></a></td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
		<?php endif; ?>

		<h2><?php esc_html_e( 'Upcoming events', 'lawyers-foundation' ); ?></h2>
		<?php if ( ! $upcoming ) : ?>
			<p><?php esc_html_e( 'No upcoming events.', 'lawyers-foundation' ); ?> <a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=lf_event' ) ); ?>"><?php esc_html_e( 'Schedule one', 'lawyers-foundation' ); ?></a></p>
		<?php else : ?>
			<ul>
			<?php foreach ( $upcoming as $event ) : ?>
				<li><a href="<?php echo esc_url( get_edit_post_link( $event->ID ) ); ?>"><?php echo esc_html( get_the_title( $event ) ); ?></a> — <?php echo esc_html( lf_format_date( get_post_meta( $event->ID, '_lf_start', true ) ) ); ?> (<?php echo (int) count( lf_get_attendees( $event->ID ) ); ?> <?php esc_html_e( 'registered', 'lawyers-foundation' ); ?>)</li>
			<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * Register the settings option.
 */
function lf_register_settings() {
	register_setting(
		'lf_settings',
		'lf_settings',
		array(
			'type'              => 'array',
			'sanitize_callback' => 'lf_sanitize_settings',
			'default'           => lf_default_settings(),
		)
	);
}
add_action( 'admin_init', 'lf_register_settings' );

/**
 * Sanitize settings.
 *
 * @param array $input Raw input.
 * @return array
 */
function lf_sanitize_settings( $input ) {
	$input = (array) $input;
	$cycle = preg_split( '/\r\n|\r|\n/', isset( $input['event_cycle'] ) ? (string) $input['event_cycle'] : '' );
	$cycle = array_slice( array_values( array_filter( array_map( 'sanitize_text_field', $cycle ) ) ), 0, 4 );

	return array(
		'org_name'             => sanitize_text_field( isset( $input['org_name'] ) ? $input['org_name'] : '' ),
		'event_cycle'          => implode( "\n", $cycle ),
		'directory_visibility' => ( isset( $input['directory_visibility'] ) && 'public' === $input['directory_visibility'] ) ? 'public' : 'members',
		'auto_approve'         => empty( $input['auto_approve'] ) ? 0 : 1,
		'notify_email'         => sanitize_email( isset( $input['notify_email'] ) ? $input['notify_email'] : '' ),
		'dues'                 => sanitize_textarea_field( isset( $input['dues'] ) ? $input['dues'] : '' ),
		'code_of_conduct'      => sanitize_textarea_field( isset( $input['code_of_conduct'] ) ? $input['code_of_conduct'] : '' ),
	);
}

/**
 * Settings screen.
 */
function lf_render_settings() {
	$s = wp_parse_args( (array) get_option( 'lf_settings', array() ), lf_default_settings() );
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Foundation Settings', 'lawyers-foundation' ); ?></h1>
		<form method="post" action="options.php">
			<?php settings_fields( 'lf_settings' ); ?>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><label for="lf_org_name"><?php esc_html_e( 'Foundation name', 'lawyers-foundation' ); ?></label></th>
					<td><input type="text" class="regular-text" id="lf_org_name" name="lf_settings[org_name]" value="<?php echo esc_attr( $s['org_name'] ); ?>"></td>
				</tr>
				<tr>
					<th scope="row"><label for="lf_event_cycle"><?php esc_html_e( 'Yearly event cycle', 'lawyers-foundation' ); ?></label></th>
					<td>
						<textarea id="lf_event_cycle" name="lf_settings[event_cycle]" rows="4" class="regular-text"><?php echo esc_textarea( $s['event_cycle'] ); ?></textarea>
						<p class="description"><?php esc_html_e( 'One flagship event per line. Two lines = two events a year (biannual); add a third line for three a year. Each event you schedule is assigned to one of these slots.', 'lawyers-foundation' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Member directory', 'lawyers-foundation' ); ?></th>
					<td>
						<label><input type="radio" name="lf_settings[directory_visibility]" value="members" <?php checked( 'members', $s['directory_visibility'] ); ?>> <?php esc_html_e( 'Visible to signed-in members only', 'lawyers-foundation' ); ?></label><br>
						<label><input type="radio" name="lf_settings[directory_visibility]" value="public" <?php checked( 'public', $s['directory_visibility'] ); ?>> <?php esc_html_e( 'Public (helps people find a lawyer)', 'lawyers-foundation' ); ?></label>
						<p class="description"><?php esc_html_e( 'Members can always hide themselves from the directory, and contact details are only shown to other members.', 'lawyers-foundation' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Approval', 'lawyers-foundation' ); ?></th>
					<td><label><input type="checkbox" name="lf_settings[auto_approve]" value="1" <?php checked( 1, (int) $s['auto_approve'] ); ?>> <?php esc_html_e( 'Approve new applications automatically (not recommended — bar numbers should be verified first)', 'lawyers-foundation' ); ?></label></td>
				</tr>
				<tr>
					<th scope="row"><label for="lf_notify_email"><?php esc_html_e( 'Notification email', 'lawyers-foundation' ); ?></label></th>
					<td><input type="email" class="regular-text" id="lf_notify_email" name="lf_settings[notify_email]" value="<?php echo esc_attr( $s['notify_email'] ); ?>"><p class="description"><?php esc_html_e( 'Receives new applications and service-hour submissions.', 'lawyers-foundation' ); ?></p></td>
				</tr>
				<tr>
					<th scope="row"><label for="lf_dues"><?php esc_html_e( 'Membership dues text', 'lawyers-foundation' ); ?></label></th>
					<td><textarea id="lf_dues" name="lf_settings[dues]" rows="2" class="large-text"><?php echo esc_textarea( $s['dues'] ); ?></textarea></td>
				</tr>
				<tr>
					<th scope="row"><label for="lf_coc"><?php esc_html_e( 'Applicant declaration', 'lawyers-foundation' ); ?></label></th>
					<td><textarea id="lf_coc" name="lf_settings[code_of_conduct]" rows="3" class="large-text"><?php echo esc_textarea( $s['code_of_conduct'] ); ?></textarea></td>
				</tr>
			</table>
			<?php submit_button(); ?>
		</form>
		<h2><?php esc_html_e( 'Shortcodes', 'lawyers-foundation' ); ?></h2>
		<p><code>[lf_register]</code> <code>[lf_dashboard]</code> <code>[lf_directory]</code> <code>[lf_event_cycle]</code> <code>[lf_events show="upcoming|past" limit="6"]</code> <code>[lf_initiatives limit="6"]</code> <code>[lf_impact]</code></p>
	</div>
	<?php
}
