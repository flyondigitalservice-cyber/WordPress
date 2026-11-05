<?php
/**
 * Membership: applications, approval, profiles, directory and dashboard.
 *
 * @package Lawyers_Foundation
 */

defined( 'ABSPATH' ) || exit;

/**
 * Profile meta keys and labels.
 *
 * @return array
 */
function lf_profile_fields() {
	return array(
		'lf_bar_number'    => __( 'Bar / licence number', 'lawyers-foundation' ),
		'lf_jurisdiction'  => __( 'Jurisdiction (bar / law society)', 'lawyers-foundation' ),
		'lf_year_admitted' => __( 'Year admitted to practice', 'lawyers-foundation' ),
		'lf_firm'          => __( 'Firm / organisation', 'lawyers-foundation' ),
		'lf_city'          => __( 'City', 'lawyers-foundation' ),
		'lf_phone'         => __( 'Phone', 'lawyers-foundation' ),
		'lf_website'       => __( 'Website', 'lawyers-foundation' ),
	);
}

/**
 * Sanitize and save profile fields from a request array.
 *
 * @param int   $user_id User ID.
 * @param array $data    Unslashed request data.
 * @param bool  $is_admin Whether an admin is editing (may change bar number).
 */
function lf_save_profile_fields( $user_id, $data, $is_admin = false ) {
	foreach ( array_keys( lf_profile_fields() ) as $key ) {
		if ( ! isset( $data[ $key ] ) ) {
			continue;
		}
		// Bar details are verified at approval; only admins change them afterwards.
		if ( ! $is_admin && in_array( $key, array( 'lf_bar_number', 'lf_jurisdiction' ), true ) && lf_is_member( $user_id ) && ! user_can( $user_id, 'manage_options' ) ) {
			continue;
		}
		$value = 'lf_website' === $key ? esc_url_raw( $data[ $key ] ) : sanitize_text_field( $data[ $key ] );
		if ( 'lf_year_admitted' === $key ) {
			$value = preg_match( '/^(19|20)\d{2}$/', $value ) ? $value : '';
		}
		update_user_meta( $user_id, $key, $value );
	}

	if ( isset( $data['lf_bio'] ) ) {
		update_user_meta( $user_id, 'lf_bio', sanitize_textarea_field( $data['lf_bio'] ) );
	}

	$areas = isset( $data['lf_practice_areas'] ) ? array_map( 'sanitize_text_field', (array) $data['lf_practice_areas'] ) : array();
	update_user_meta( $user_id, 'lf_practice_areas', array_values( array_intersect( $areas, lf_practice_areas() ) ) );

	update_user_meta( $user_id, 'lf_show_in_directory', empty( $data['lf_show_in_directory'] ) ? '0' : '1' );
}

/**
 * Practice-area checkboxes.
 *
 * @param array $selected Selected areas.
 * @return string
 */
function lf_practice_area_checkboxes( $selected ) {
	$html = '<div class="lf-checks">';
	foreach ( lf_practice_areas() as $area ) {
		$html .= sprintf(
			'<label><input type="checkbox" name="lf_practice_areas[]" value="%1$s" %2$s> %3$s</label>',
			esc_attr( $area ),
			checked( in_array( $area, (array) $selected, true ), true, false ),
			esc_html( $area )
		);
	}
	return $html . '</div>';
}

/* -------------------------------------------------------------------------
 * Application form: [lf_register]
 * ---------------------------------------------------------------------- */

/**
 * Errors from the last application attempt (same request).
 *
 * @var WP_Error|null
 */
$GLOBALS['lf_register_errors'] = null;

/**
 * Process a membership application.
 */
function lf_handle_registration() {
	if ( empty( $_POST['lf_action'] ) || 'register' !== $_POST['lf_action'] ) {
		return;
	}
	if ( ! isset( $_POST['lf_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['lf_nonce'] ), 'lf_register' ) ) {
		wp_die( esc_html__( 'Your session expired. Please go back and try again.', 'lawyers-foundation' ) );
	}

	$data   = wp_unslash( $_POST );
	$errors = new WP_Error();

	// Honeypot: real people leave this hidden field empty.
	if ( ! empty( $data['lf_company_url'] ) ) {
		lf_redirect_with( 'applied' );
	}

	$first = sanitize_text_field( isset( $data['first_name'] ) ? $data['first_name'] : '' );
	$last  = sanitize_text_field( isset( $data['last_name'] ) ? $data['last_name'] : '' );
	$email = sanitize_email( isset( $data['email'] ) ? $data['email'] : '' );
	$pass  = isset( $data['password'] ) ? (string) $data['password'] : '';

	if ( '' === $first || '' === $last ) {
		$errors->add( 'name', __( 'Please enter your full name.', 'lawyers-foundation' ) );
	}
	if ( ! is_email( $email ) ) {
		$errors->add( 'email', __( 'Please enter a valid email address.', 'lawyers-foundation' ) );
	} elseif ( email_exists( $email ) ) {
		$errors->add( 'email', __( 'An account with this email already exists. Please sign in instead.', 'lawyers-foundation' ) );
	}
	if ( strlen( $pass ) < 8 ) {
		$errors->add( 'password', __( 'Your password must be at least 8 characters.', 'lawyers-foundation' ) );
	}
	if ( empty( $data['lf_bar_number'] ) || empty( $data['lf_jurisdiction'] ) ) {
		$errors->add( 'bar', __( 'Your bar / licence number and jurisdiction are required so we can verify your standing.', 'lawyers-foundation' ) );
	}
	if ( empty( $data['lf_declaration'] ) ) {
		$errors->add( 'declaration', __( 'Please accept the declaration.', 'lawyers-foundation' ) );
	}

	if ( $errors->has_errors() ) {
		$GLOBALS['lf_register_errors'] = $errors;
		return;
	}

	$base  = sanitize_user( strtolower( $first . '.' . $last ), true );
	$base  = $base ? $base : 'member';
	$login = $base;
	$i     = 1;
	while ( username_exists( $login ) ) {
		$login = $base . ( ++$i );
	}

	$approved = (bool) lf_opt( 'auto_approve' );
	$user_id  = wp_insert_user(
		array(
			'user_login'   => $login,
			'user_email'   => $email,
			'user_pass'    => $pass,
			'first_name'   => $first,
			'last_name'    => $last,
			'display_name' => $first . ' ' . $last,
			'role'         => $approved ? 'lf_member' : 'lf_pending',
		)
	);

	if ( is_wp_error( $user_id ) ) {
		$GLOBALS['lf_register_errors'] = $user_id;
		return;
	}

	$data['lf_show_in_directory'] = 1;
	lf_save_profile_fields( $user_id, $data, true );
	if ( $approved ) {
		update_user_meta( $user_id, 'lf_member_since', current_time( 'Y-m-d' ) );
	}

	$notify = lf_opt( 'notify_email' );
	if ( $notify ) {
		wp_mail(
			$notify,
			/* translators: %s: applicant name */
			sprintf( __( 'New membership application: %s', 'lawyers-foundation' ), $first . ' ' . $last ),
			sprintf(
				"%s %s\n%s: %s\n%s: %s\n\n%s\n%s",
				$first,
				$last,
				__( 'Bar / licence no.', 'lawyers-foundation' ),
				sanitize_text_field( $data['lf_bar_number'] ),
				__( 'Jurisdiction', 'lawyers-foundation' ),
				sanitize_text_field( $data['lf_jurisdiction'] ),
				__( 'Review and approve:', 'lawyers-foundation' ),
				admin_url( 'admin.php?page=lf-foundation' )
			)
		);
	}

	wp_set_current_user( $user_id );
	wp_set_auth_cookie( $user_id );
	lf_redirect_with( $approved ? 'welcome' : 'applied', lf_page_url( 'dashboard' ) );
}
add_action( 'template_redirect', 'lf_handle_registration' );

/**
 * [lf_register] shortcode.
 *
 * @return string
 */
function lf_shortcode_register() {
	if ( is_user_logged_in() ) {
		return lf_flash() . '<div class="lf-card"><p>' . esc_html__( 'You already have an account.', 'lawyers-foundation' ) . '</p><p><a class="lf-btn" href="' . esc_url( lf_page_url( 'dashboard' ) ) . '">' . esc_html__( 'Go to your member dashboard', 'lawyers-foundation' ) . '</a></p></div>';
	}

	$errors = $GLOBALS['lf_register_errors'];
	// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Re-populating the form after a failed, nonce-checked submission.
	$old = $errors ? wp_unslash( $_POST ) : array();
	$v   = function ( $key ) use ( $old ) {
		return isset( $old[ $key ] ) && is_string( $old[ $key ] ) ? esc_attr( $old[ $key ] ) : '';
	};

	ob_start();
	if ( $errors ) {
		echo '<div class="lf-notice lf-notice--error" role="alert"><ul>';
		foreach ( $errors->get_error_messages() as $message ) {
			echo '<li>' . esc_html( $message ) . '</li>';
		}
		echo '</ul></div>';
	}
	?>
	<form class="lf-form lf-card" method="post" novalidate>
		<?php wp_nonce_field( 'lf_register', 'lf_nonce' ); ?>
		<input type="hidden" name="lf_action" value="register">
		<p class="lf-hp" aria-hidden="true"><label>Leave empty <input type="text" name="lf_company_url" tabindex="-1" autocomplete="off"></label></p>

		<fieldset>
			<legend><?php esc_html_e( 'Your account', 'lawyers-foundation' ); ?></legend>
			<div class="lf-grid-2">
				<p><label for="lf-first"><?php esc_html_e( 'First name', 'lawyers-foundation' ); ?> *</label><input id="lf-first" type="text" name="first_name" required autocomplete="given-name" value="<?php echo $v( 'first_name' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>"></p>
				<p><label for="lf-last"><?php esc_html_e( 'Last name', 'lawyers-foundation' ); ?> *</label><input id="lf-last" type="text" name="last_name" required autocomplete="family-name" value="<?php echo $v( 'last_name' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>"></p>
				<p><label for="lf-email"><?php esc_html_e( 'Email', 'lawyers-foundation' ); ?> *</label><input id="lf-email" type="email" name="email" required autocomplete="email" value="<?php echo $v( 'email' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>"></p>
				<p><label for="lf-pass"><?php esc_html_e( 'Password (min. 8 characters)', 'lawyers-foundation' ); ?> *</label><input id="lf-pass" type="password" name="password" required minlength="8" autocomplete="new-password"></p>
			</div>
		</fieldset>

		<fieldset>
			<legend><?php esc_html_e( 'Professional details', 'lawyers-foundation' ); ?></legend>
			<div class="lf-grid-2">
				<p><label for="lf-bar"><?php esc_html_e( 'Bar / licence number', 'lawyers-foundation' ); ?> *</label><input id="lf-bar" type="text" name="lf_bar_number" required value="<?php echo $v( 'lf_bar_number' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>"></p>
				<p><label for="lf-jur"><?php esc_html_e( 'Jurisdiction (bar / law society)', 'lawyers-foundation' ); ?> *</label><input id="lf-jur" type="text" name="lf_jurisdiction" required placeholder="<?php esc_attr_e( 'e.g. Bar Council of Maharashtra & Goa', 'lawyers-foundation' ); ?>" value="<?php echo $v( 'lf_jurisdiction' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>"></p>
				<p><label for="lf-year"><?php esc_html_e( 'Year admitted to practice', 'lawyers-foundation' ); ?></label><input id="lf-year" type="number" name="lf_year_admitted" min="1950" max="<?php echo esc_attr( gmdate( 'Y' ) ); ?>" value="<?php echo $v( 'lf_year_admitted' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>"></p>
				<p><label for="lf-firm"><?php esc_html_e( 'Firm / organisation', 'lawyers-foundation' ); ?></label><input id="lf-firm" type="text" name="lf_firm" autocomplete="organization" value="<?php echo $v( 'lf_firm' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>"></p>
				<p><label for="lf-city"><?php esc_html_e( 'City', 'lawyers-foundation' ); ?></label><input id="lf-city" type="text" name="lf_city" autocomplete="address-level2" value="<?php echo $v( 'lf_city' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>"></p>
				<p><label for="lf-phone"><?php esc_html_e( 'Phone', 'lawyers-foundation' ); ?></label><input id="lf-phone" type="tel" name="lf_phone" autocomplete="tel" value="<?php echo $v( 'lf_phone' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>"></p>
			</div>
			<p class="lf-label"><?php esc_html_e( 'Practice areas', 'lawyers-foundation' ); ?></p>
			<?php echo lf_practice_area_checkboxes( isset( $old['lf_practice_areas'] ) ? (array) $old['lf_practice_areas'] : array() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<p><label for="lf-bio"><?php esc_html_e( 'Why do you want to join? (short bio)', 'lawyers-foundation' ); ?></label><textarea id="lf-bio" name="lf_bio" rows="4"><?php echo isset( $old['lf_bio'] ) ? esc_textarea( $old['lf_bio'] ) : ''; ?></textarea></p>
		</fieldset>

		<p class="lf-dues"><?php echo esc_html( lf_opt( 'dues' ) ); ?></p>
		<p><label class="lf-inline"><input type="checkbox" name="lf_declaration" value="1" required> <?php echo esc_html( lf_opt( 'code_of_conduct' ) ); ?></label></p>
		<p><button type="submit" class="lf-btn"><?php esc_html_e( 'Submit application', 'lawyers-foundation' ); ?></button></p>
		<p class="lf-muted"><?php esc_html_e( 'Already a member?', 'lawyers-foundation' ); ?> <a href="<?php echo esc_url( wp_login_url( lf_page_url( 'dashboard' ) ) ); ?>"><?php esc_html_e( 'Sign in', 'lawyers-foundation' ); ?></a></p>
	</form>
	<?php
	return ob_get_clean();
}
add_shortcode( 'lf_register', 'lf_shortcode_register' );

/* -------------------------------------------------------------------------
 * Approval (admin)
 * ---------------------------------------------------------------------- */

/**
 * Nonced approval URL.
 *
 * @param int $user_id User ID.
 * @return string
 */
function lf_approve_url( $user_id ) {
	return wp_nonce_url( admin_url( 'admin-post.php?action=lf_approve&user_id=' . (int) $user_id ), 'lf_approve_' . (int) $user_id );
}

/**
 * Approve a member and email them.
 *
 * @param int $user_id User ID.
 */
function lf_approve_member( $user_id ) {
	$user = get_userdata( $user_id );
	if ( ! $user || ! in_array( 'lf_pending', (array) $user->roles, true ) ) {
		return;
	}
	$user->set_role( 'lf_member' );
	update_user_meta( $user_id, 'lf_member_since', current_time( 'Y-m-d' ) );

	wp_mail(
		$user->user_email,
		/* translators: %s: foundation name */
		sprintf( __( 'Welcome to %s', 'lawyers-foundation' ), lf_org_name() ),
		sprintf(
			/* translators: 1: member name, 2: foundation name, 3: dashboard URL */
			__( "Dear %1\$s,\n\nYour membership of %2\$s has been approved. Sign in to your member dashboard to register for our events and log your pro bono work:\n%3\$s\n\nThank you for lending your skills to the foundation.", 'lawyers-foundation' ),
			$user->display_name,
			lf_org_name(),
			lf_page_url( 'dashboard' )
		)
	);
}

/**
 * Handle the approve link.
 */
function lf_admin_post_approve() {
	$user_id = isset( $_GET['user_id'] ) ? absint( $_GET['user_id'] ) : 0;
	if ( ! current_user_can( 'promote_users' ) || ! check_admin_referer( 'lf_approve_' . $user_id ) ) {
		wp_die( esc_html__( 'You are not allowed to do that.', 'lawyers-foundation' ) );
	}
	lf_approve_member( $user_id );
	wp_safe_redirect( wp_get_referer() ? wp_get_referer() : admin_url( 'admin.php?page=lf-foundation' ) );
	exit;
}
add_action( 'admin_post_lf_approve', 'lf_admin_post_approve' );

/**
 * "Approve" row action on the Users screen.
 *
 * @param array   $actions Row actions.
 * @param WP_User $user    User.
 * @return array
 */
function lf_user_row_actions( $actions, $user ) {
	if ( current_user_can( 'promote_users' ) && in_array( 'lf_pending', (array) $user->roles, true ) ) {
		$actions['lf_approve'] = '<a href="' . esc_url( lf_approve_url( $user->ID ) ) . '">' . esc_html__( 'Approve membership', 'lawyers-foundation' ) . '</a>';
	}
	return $actions;
}
add_filter( 'user_row_actions', 'lf_user_row_actions', 10, 2 );

/**
 * Bulk approve on the Users screen.
 *
 * @param array $actions Bulk actions.
 * @return array
 */
function lf_users_bulk_actions( $actions ) {
	$actions['lf_approve'] = __( 'Approve membership', 'lawyers-foundation' );
	return $actions;
}
add_filter( 'bulk_actions-users', 'lf_users_bulk_actions' );

/**
 * Handle bulk approval.
 *
 * @param string $redirect Redirect URL.
 * @param string $action   Action.
 * @param int[]  $ids      User IDs.
 * @return string
 */
function lf_users_handle_bulk( $redirect, $action, $ids ) {
	if ( 'lf_approve' === $action && current_user_can( 'promote_users' ) ) {
		array_map( 'lf_approve_member', array_map( 'absint', $ids ) );
	}
	return $redirect;
}
add_filter( 'handle_bulk_actions-users', 'lf_users_handle_bulk', 10, 3 );

/**
 * Bar number column on the Users screen.
 *
 * @param array $columns Columns.
 * @return array
 */
function lf_users_columns( $columns ) {
	$columns['lf_bar'] = __( 'Bar / licence no.', 'lawyers-foundation' );
	return $columns;
}
add_filter( 'manage_users_columns', 'lf_users_columns' );

/**
 * Render the bar number column.
 *
 * @param string $output  Output.
 * @param string $column  Column.
 * @param int    $user_id User ID.
 * @return string
 */
function lf_users_column_value( $output, $column, $user_id ) {
	if ( 'lf_bar' === $column ) {
		$bar = get_user_meta( $user_id, 'lf_bar_number', true );
		$jur = get_user_meta( $user_id, 'lf_jurisdiction', true );
		return esc_html( $bar ) . ( $jur ? '<br><small>' . esc_html( $jur ) . '</small>' : '' );
	}
	return $output;
}
add_filter( 'manage_users_custom_column', 'lf_users_column_value', 10, 3 );

/**
 * Keep pending applicants and members out of wp-admin (except profile.php is not needed: they use the dashboard page).
 */
function lf_block_admin_for_members() {
	if ( wp_doing_ajax() || ! is_user_logged_in() ) {
		return;
	}
	$user = wp_get_current_user();
	if ( array_intersect( array( 'lf_member', 'lf_pending' ), (array) $user->roles ) && ! current_user_can( 'edit_posts' ) ) {
		wp_safe_redirect( lf_page_url( 'dashboard' ) );
		exit;
	}
}
add_action( 'admin_init', 'lf_block_admin_for_members' );

/**
 * Hide the admin bar for members.
 *
 * @param bool $show Show.
 * @return bool
 */
function lf_hide_admin_bar( $show ) {
	return current_user_can( 'edit_posts' ) ? $show : false;
}
add_filter( 'show_admin_bar', 'lf_hide_admin_bar' );

/**
 * Send members to the dashboard after login.
 *
 * @param string           $redirect  Redirect.
 * @param string           $requested Requested redirect.
 * @param WP_User|WP_Error $user      User.
 * @return string
 */
function lf_login_redirect( $redirect, $requested, $user ) {
	if ( $user instanceof WP_User && array_intersect( array( 'lf_member', 'lf_pending' ), (array) $user->roles ) && ! $user->has_cap( 'edit_posts' ) ) {
		return ( $requested && false === strpos( $requested, 'wp-admin' ) ) ? $requested : lf_page_url( 'dashboard' );
	}
	return $redirect;
}
add_filter( 'login_redirect', 'lf_login_redirect', 10, 3 );

/* -------------------------------------------------------------------------
 * Profile fields on the admin user screen
 * ---------------------------------------------------------------------- */

/**
 * Show foundation fields on the user edit screen.
 *
 * @param WP_User $user User.
 */
function lf_admin_profile_fields( $user ) {
	?>
	<h2><?php esc_html_e( 'Foundation membership', 'lawyers-foundation' ); ?></h2>
	<table class="form-table" role="presentation">
		<tr><th><?php esc_html_e( 'Status', 'lawyers-foundation' ); ?></th><td>
			<?php
			if ( lf_is_pending( $user->ID ) ) {
				echo esc_html__( 'Pending approval', 'lawyers-foundation' ) . ' — <a href="' . esc_url( lf_approve_url( $user->ID ) ) . '">' . esc_html__( 'Approve now', 'lawyers-foundation' ) . '</a>';
			} elseif ( in_array( 'lf_member', (array) $user->roles, true ) ) {
				/* translators: %s: date */
				printf( esc_html__( 'Member since %s', 'lawyers-foundation' ), esc_html( get_user_meta( $user->ID, 'lf_member_since', true ) ) );
			} else {
				esc_html_e( 'Not a foundation member', 'lawyers-foundation' );
			}
			?>
		</td></tr>
		<?php foreach ( lf_profile_fields() as $key => $label ) : ?>
			<tr><th><label for="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></label></th>
			<td><input type="text" class="regular-text" id="<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( get_user_meta( $user->ID, $key, true ) ); ?>"></td></tr>
		<?php endforeach; ?>
		<tr><th><?php esc_html_e( 'Practice areas', 'lawyers-foundation' ); ?></th><td><?php echo lf_practice_area_checkboxes( (array) get_user_meta( $user->ID, 'lf_practice_areas', true ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></td></tr>
		<tr><th><label for="lf_bio"><?php esc_html_e( 'Professional bio', 'lawyers-foundation' ); ?></label></th><td><textarea id="lf_bio" name="lf_bio" rows="4" class="large-text"><?php echo esc_textarea( get_user_meta( $user->ID, 'lf_bio', true ) ); ?></textarea></td></tr>
		<tr><th><?php esc_html_e( 'Directory', 'lawyers-foundation' ); ?></th><td><label><input type="checkbox" name="lf_show_in_directory" value="1" <?php checked( '0' !== get_user_meta( $user->ID, 'lf_show_in_directory', true ) ); ?>> <?php esc_html_e( 'List in the member directory', 'lawyers-foundation' ); ?></label></td></tr>
	</table>
	<?php
}
add_action( 'show_user_profile', 'lf_admin_profile_fields' );
add_action( 'edit_user_profile', 'lf_admin_profile_fields' );

/**
 * Save foundation fields from the user edit screen (core already checked the nonce).
 *
 * @param int $user_id User ID.
 */
function lf_admin_save_profile( $user_id ) {
	if ( ! current_user_can( 'edit_user', $user_id ) ) {
		return;
	}
	// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Verified by core (update-user_{id}).
	lf_save_profile_fields( $user_id, wp_unslash( $_POST ), current_user_can( 'promote_users' ) );
}
add_action( 'personal_options_update', 'lf_admin_save_profile' );
add_action( 'edit_user_profile_update', 'lf_admin_save_profile' );

/* -------------------------------------------------------------------------
 * Member directory: [lf_directory]
 * ---------------------------------------------------------------------- */

/**
 * [lf_directory] shortcode.
 *
 * @return string
 */
function lf_shortcode_directory() {
	$viewer_is_member = lf_is_member();
	if ( 'public' !== lf_opt( 'directory_visibility' ) && ! $viewer_is_member ) {
		return '<div class="lf-card lf-locked"><h3>' . esc_html__( 'Members only', 'lawyers-foundation' ) . '</h3><p>' . esc_html__( 'The member directory is available to signed-in members of the foundation.', 'lawyers-foundation' ) . '</p><p><a class="lf-btn" href="' . esc_url( wp_login_url( get_permalink() ) ) . '">' . esc_html__( 'Sign in', 'lawyers-foundation' ) . '</a> <a class="lf-btn lf-btn--ghost" href="' . esc_url( lf_page_url( 'join' ) ) . '">' . esc_html__( 'Become a member', 'lawyers-foundation' ) . '</a></p></div>';
	}

	// phpcs:disable WordPress.Security.NonceVerification.Recommended -- Read-only search filters.
	$search = isset( $_GET['lf_q'] ) ? sanitize_text_field( wp_unslash( $_GET['lf_q'] ) ) : '';
	$area   = isset( $_GET['lf_area'] ) ? sanitize_text_field( wp_unslash( $_GET['lf_area'] ) ) : '';
	$city   = isset( $_GET['lf_city'] ) ? sanitize_text_field( wp_unslash( $_GET['lf_city'] ) ) : '';
	$paged  = isset( $_GET['lf_page'] ) ? max( 1, absint( $_GET['lf_page'] ) ) : 1;
	// phpcs:enable

	$meta = array(
		'relation' => 'AND',
		array(
			'relation' => 'OR',
			array( 'key' => 'lf_show_in_directory', 'value' => '1' ),
			array( 'key' => 'lf_show_in_directory', 'compare' => 'NOT EXISTS' ),
		),
	);
	if ( $area && in_array( $area, lf_practice_areas(), true ) ) {
		$meta[] = array( 'key' => 'lf_practice_areas', 'value' => '"' . $area . '"', 'compare' => 'LIKE' );
	}
	if ( $city ) {
		$meta[] = array( 'key' => 'lf_city', 'value' => $city, 'compare' => 'LIKE' );
	}

	$per_page = 24;
	$args     = array(
		'role'        => 'lf_member',
		'meta_query'  => $meta, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
		'orderby'     => 'display_name',
		'order'       => 'ASC',
		'number'      => $per_page,
		'paged'       => $paged,
		'count_total' => true,
	);
	if ( $search ) {
		$args['search']         = '*' . $search . '*';
		$args['search_columns'] = array( 'display_name', 'user_nicename' );
	}
	$query = new WP_User_Query( $args );
	$users = $query->get_results();

	ob_start();
	?>
	<form class="lf-filter" method="get" action="<?php echo esc_url( get_permalink() ); ?>">
		<input type="search" name="lf_q" value="<?php echo esc_attr( $search ); ?>" placeholder="<?php esc_attr_e( 'Search by name', 'lawyers-foundation' ); ?>" aria-label="<?php esc_attr_e( 'Search by name', 'lawyers-foundation' ); ?>">
		<select name="lf_area" aria-label="<?php esc_attr_e( 'Practice area', 'lawyers-foundation' ); ?>">
			<option value=""><?php esc_html_e( 'All practice areas', 'lawyers-foundation' ); ?></option>
			<?php foreach ( lf_practice_areas() as $option ) : ?>
				<option value="<?php echo esc_attr( $option ); ?>" <?php selected( $area, $option ); ?>><?php echo esc_html( $option ); ?></option>
			<?php endforeach; ?>
		</select>
		<input type="text" name="lf_city" value="<?php echo esc_attr( $city ); ?>" placeholder="<?php esc_attr_e( 'City', 'lawyers-foundation' ); ?>" aria-label="<?php esc_attr_e( 'City', 'lawyers-foundation' ); ?>">
		<button type="submit" class="lf-btn"><?php esc_html_e( 'Filter', 'lawyers-foundation' ); ?></button>
	</form>
	<p class="lf-muted">
		<?php
		/* translators: %s: number of members */
		printf( esc_html( _n( '%s member found', '%s members found', $query->get_total(), 'lawyers-foundation' ) ), esc_html( number_format_i18n( $query->get_total() ) ) );
		?>
	</p>
	<div class="lf-members">
		<?php foreach ( $users as $user ) : ?>
			<?php
			$areas = (array) get_user_meta( $user->ID, 'lf_practice_areas', true );
			$firm  = get_user_meta( $user->ID, 'lf_firm', true );
			$cityv = get_user_meta( $user->ID, 'lf_city', true );
			$year  = get_user_meta( $user->ID, 'lf_year_admitted', true );
			$hours = lf_user_service_hours( $user->ID );
			?>
			<article class="lf-member">
				<?php echo get_avatar( $user->ID, 72, '', '', array( 'class' => 'lf-avatar' ) ); ?>
				<div>
					<h3><?php echo esc_html( $user->display_name ); ?></h3>
					<p class="lf-muted"><?php echo esc_html( implode( ' · ', array_filter( array( $firm, $cityv ) ) ) ); ?></p>
					<?php if ( $year ) : ?>
						<p class="lf-small">
							<?php
							/* translators: %s: year */
							printf( esc_html__( 'Admitted %s', 'lawyers-foundation' ), esc_html( $year ) );
							?>
						</p>
					<?php endif; ?>
					<?php if ( array_filter( $areas ) ) : ?>
						<p class="lf-tags"><?php foreach ( array_filter( $areas ) as $tag ) { echo '<span>' . esc_html( $tag ) . '</span>'; } ?></p>
					<?php endif; ?>
					<?php if ( $hours > 0 ) : ?>
						<p class="lf-small lf-hours">
							<?php
							/* translators: %s: hours */
							printf( esc_html__( '%s pro bono hours with the foundation', 'lawyers-foundation' ), esc_html( number_format_i18n( $hours, 1 ) ) );
							?>
						</p>
					<?php endif; ?>
					<?php if ( $viewer_is_member ) : ?>
						<p class="lf-small"><a href="mailto:<?php echo esc_attr( antispambot( $user->user_email ) ); ?>"><?php esc_html_e( 'Email', 'lawyers-foundation' ); ?></a>
						<?php $phone = get_user_meta( $user->ID, 'lf_phone', true ); ?>
						<?php if ( $phone ) : ?> · <a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a><?php endif; ?>
						<?php $site = get_user_meta( $user->ID, 'lf_website', true ); ?>
						<?php if ( $site ) : ?> · <a href="<?php echo esc_url( $site ); ?>" rel="noopener nofollow" target="_blank"><?php esc_html_e( 'Website', 'lawyers-foundation' ); ?></a><?php endif; ?>
						</p>
					<?php endif; ?>
				</div>
			</article>
		<?php endforeach; ?>
	</div>
	<?php
	$pages = (int) ceil( $query->get_total() / $per_page );
	if ( $pages > 1 ) {
		echo '<nav class="lf-pager">';
		for ( $p = 1; $p <= $pages; $p++ ) {
			$url = add_query_arg( array_filter( array( 'lf_q' => $search, 'lf_area' => $area, 'lf_city' => $city, 'lf_page' => $p ) ), get_permalink() );
			printf( '<a href="%1$s"%2$s>%3$d</a>', esc_url( $url ), $p === $paged ? ' aria-current="page"' : '', (int) $p );
		}
		echo '</nav>';
	}
	return ob_get_clean();
}
add_shortcode( 'lf_directory', 'lf_shortcode_directory' );

/* -------------------------------------------------------------------------
 * Member dashboard: [lf_dashboard]
 * ---------------------------------------------------------------------- */

/**
 * Save the member's profile from the dashboard.
 */
function lf_handle_profile_update() {
	if ( empty( $_POST['lf_action'] ) || 'profile' !== $_POST['lf_action'] || ! is_user_logged_in() ) {
		return;
	}
	if ( ! isset( $_POST['lf_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['lf_nonce'] ), 'lf_profile' ) ) {
		wp_die( esc_html__( 'Your session expired. Please go back and try again.', 'lawyers-foundation' ) );
	}
	lf_save_profile_fields( get_current_user_id(), wp_unslash( $_POST ) );
	lf_redirect_with( 'profile' );
}
add_action( 'template_redirect', 'lf_handle_profile_update' );

/**
 * [lf_dashboard] shortcode.
 *
 * @return string
 */
function lf_shortcode_dashboard() {
	if ( ! is_user_logged_in() ) {
		ob_start();
		echo '<div class="lf-card lf-login"><h3>' . esc_html__( 'Member sign in', 'lawyers-foundation' ) . '</h3>';
		wp_login_form( array( 'redirect' => get_permalink() ) );
		echo '<p class="lf-muted"><a href="' . esc_url( wp_lostpassword_url( get_permalink() ) ) . '">' . esc_html__( 'Forgot your password?', 'lawyers-foundation' ) . '</a> · <a href="' . esc_url( lf_page_url( 'join' ) ) . '">' . esc_html__( 'Apply for membership', 'lawyers-foundation' ) . '</a></p></div>';
		return ob_get_clean();
	}

	$user = wp_get_current_user();
	ob_start();
	echo lf_flash(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

	if ( lf_is_pending() ) {
		echo '<div class="lf-card"><h3>' . esc_html__( 'Your application is under review', 'lawyers-foundation' ) . '</h3><p>' . esc_html__( 'Our membership committee verifies every applicant\'s bar standing. You will receive an email as soon as your membership is approved.', 'lawyers-foundation' ) . '</p></div>';
		return ob_get_clean();
	}
	if ( ! lf_is_member() ) {
		echo '<div class="lf-card"><p>' . esc_html__( 'Your account is not a foundation membership.', 'lawyers-foundation' ) . ' <a href="' . esc_url( lf_page_url( 'join' ) ) . '">' . esc_html__( 'Apply here', 'lawyers-foundation' ) . '</a></p></div>';
		return ob_get_clean();
	}

	$since    = get_user_meta( $user->ID, 'lf_member_since', true );
	$my_hours = lf_user_service_hours( $user->ID );
	$my_evts  = lf_get_user_events( $user->ID );
	?>
	<div class="lf-dash-head lf-card">
		<?php echo get_avatar( $user->ID, 80, '', '', array( 'class' => 'lf-avatar' ) ); ?>
		<div>
			<h2><?php
				/* translators: %s: first name */
				printf( esc_html__( 'Welcome, %s', 'lawyers-foundation' ), esc_html( $user->first_name ? $user->first_name : $user->display_name ) );
			?></h2>
			<p class="lf-muted"><?php echo $since ? esc_html( sprintf( /* translators: %s: date */ __( 'Member since %s', 'lawyers-foundation' ), mysql2date( get_option( 'date_format' ), $since ) ) ) : ''; ?></p>
		</div>
		<div class="lf-dash-stats">
			<div><strong><?php echo esc_html( number_format_i18n( $my_hours, 1 ) ); ?></strong><span><?php esc_html_e( 'verified pro bono hours', 'lawyers-foundation' ); ?></span></div>
			<div><strong><?php echo (int) count( $my_evts ); ?></strong><span><?php esc_html_e( 'upcoming events', 'lawyers-foundation' ); ?></span></div>
		</div>
		<a class="lf-btn lf-btn--ghost" href="<?php echo esc_url( wp_logout_url( home_url( '/' ) ) ); ?>"><?php esc_html_e( 'Sign out', 'lawyers-foundation' ); ?></a>
	</div>

	<div class="lf-dash-grid">
		<section class="lf-card">
			<h3><?php esc_html_e( 'My events', 'lawyers-foundation' ); ?></h3>
			<?php if ( $my_evts ) : ?>
				<ul class="lf-list">
				<?php foreach ( $my_evts as $event ) : ?>
					<li><a href="<?php echo esc_url( get_permalink( $event ) ); ?>"><?php echo esc_html( get_the_title( $event ) ); ?></a><br><span class="lf-muted"><?php echo esc_html( lf_format_date( get_post_meta( $event->ID, '_lf_start', true ) ) ); ?></span></li>
				<?php endforeach; ?>
				</ul>
			<?php else : ?>
				<p class="lf-muted"><?php esc_html_e( 'You have not registered for an upcoming event yet.', 'lawyers-foundation' ); ?></p>
			<?php endif; ?>
			<p><a class="lf-btn lf-btn--ghost" href="<?php echo esc_url( lf_page_url( 'events' ) ); ?>"><?php esc_html_e( 'Browse foundation events', 'lawyers-foundation' ); ?></a></p>
		</section>

		<section class="lf-card">
			<h3><?php esc_html_e( 'Log pro bono / service hours', 'lawyers-foundation' ); ?></h3>
			<?php echo lf_service_form(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php echo lf_service_history( $user->ID ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</section>
	</div>

	<section class="lf-card">
		<h3><?php esc_html_e( 'My profile', 'lawyers-foundation' ); ?></h3>
		<form class="lf-form" method="post">
			<?php wp_nonce_field( 'lf_profile', 'lf_nonce' ); ?>
			<input type="hidden" name="lf_action" value="profile">
			<div class="lf-grid-2">
				<?php foreach ( lf_profile_fields() as $key => $label ) : ?>
					<?php $locked = in_array( $key, array( 'lf_bar_number', 'lf_jurisdiction' ), true ) && ! current_user_can( 'manage_options' ); ?>
					<p><label for="d-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></label>
					<input id="d-<?php echo esc_attr( $key ); ?>" type="text" name="<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( get_user_meta( $user->ID, $key, true ) ); ?>" <?php disabled( $locked ); ?>>
					<?php if ( $locked ) : ?><small class="lf-muted"><?php esc_html_e( 'Verified — contact the secretariat to change.', 'lawyers-foundation' ); ?></small><?php endif; ?></p>
				<?php endforeach; ?>
			</div>
			<p class="lf-label"><?php esc_html_e( 'Practice areas', 'lawyers-foundation' ); ?></p>
			<?php echo lf_practice_area_checkboxes( (array) get_user_meta( $user->ID, 'lf_practice_areas', true ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<p><label for="d-bio"><?php esc_html_e( 'Professional bio', 'lawyers-foundation' ); ?></label><textarea id="d-bio" name="lf_bio" rows="4"><?php echo esc_textarea( get_user_meta( $user->ID, 'lf_bio', true ) ); ?></textarea></p>
			<p><label class="lf-inline"><input type="checkbox" name="lf_show_in_directory" value="1" <?php checked( '0' !== get_user_meta( $user->ID, 'lf_show_in_directory', true ) ); ?>> <?php esc_html_e( 'Show me in the member directory', 'lawyers-foundation' ); ?></label></p>
			<p><button type="submit" class="lf-btn"><?php esc_html_e( 'Save profile', 'lawyers-foundation' ); ?></button></p>
		</form>
	</section>
	<?php
	return ob_get_clean();
}
add_shortcode( 'lf_dashboard', 'lf_shortcode_dashboard' );
