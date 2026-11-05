<?php
/**
 * Pro bono / service hours, initiatives and impact numbers.
 *
 * @package Lawyers_Foundation
 */

defined( 'ABSPATH' ) || exit;

/**
 * Total verified (published) hours, optionally for one member.
 *
 * @param int $user_id User ID or 0 for everyone.
 * @return float
 */
function lf_sum_hours( $user_id = 0 ) {
	global $wpdb;
	$sql = "SELECT COALESCE(SUM(CAST(pm.meta_value AS DECIMAL(10,2))),0) FROM {$wpdb->posts} p INNER JOIN {$wpdb->postmeta} pm ON pm.post_id = p.ID AND pm.meta_key = '_lf_hours' WHERE p.post_type = 'lf_service' AND p.post_status = 'publish'";
	if ( $user_id ) {
		$sql .= $wpdb->prepare( ' AND p.post_author = %d', $user_id );
	}
	return (float) $wpdb->get_var( $sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared,WordPress.DB.DirectDatabaseQuery
}

/**
 * Total verified hours across the foundation.
 *
 * @return float
 */
function lf_total_service_hours() {
	return lf_sum_hours();
}

/**
 * Verified hours for one member.
 *
 * @param int $user_id User ID.
 * @return float
 */
function lf_user_service_hours( $user_id ) {
	return lf_sum_hours( (int) $user_id );
}

/**
 * Handle a service-hours submission from the dashboard.
 */
function lf_handle_service_submit() {
	if ( empty( $_POST['lf_action'] ) || 'log_hours' !== $_POST['lf_action'] ) {
		return;
	}
	if ( ! isset( $_POST['lf_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['lf_nonce'] ), 'lf_log_hours' ) ) {
		wp_die( esc_html__( 'Your session expired. Please go back and try again.', 'lawyers-foundation' ) );
	}
	if ( ! lf_is_member() ) {
		wp_die( esc_html__( 'Only approved members can log service hours.', 'lawyers-foundation' ) );
	}

	$data       = wp_unslash( $_POST );
	$hours      = isset( $data['lf_hours'] ) ? round( (float) $data['lf_hours'], 2 ) : 0;
	$date       = isset( $data['lf_date'] ) ? sanitize_text_field( $data['lf_date'] ) : '';
	$desc       = isset( $data['lf_desc'] ) ? sanitize_textarea_field( $data['lf_desc'] ) : '';
	$initiative = isset( $data['lf_initiative'] ) ? absint( $data['lf_initiative'] ) : 0;
	$valid_date = date_create_from_format( 'Y-m-d', $date );

	if ( $hours <= 0 || $hours > 200 || ! $valid_date || $valid_date->format( 'Y-m-d' ) > current_time( 'Y-m-d' ) || '' === $desc ) {
		lf_redirect_with( 'hours_error' );
	}
	if ( $initiative && 'lf_initiative' !== get_post_type( $initiative ) ) {
		$initiative = 0;
	}

	$user    = wp_get_current_user();
	$post_id = wp_insert_post(
		array(
			'post_type'    => 'lf_service',
			'post_status'  => 'pending',
			'post_author'  => $user->ID,
			/* translators: 1: member, 2: hours, 3: date */
			'post_title'   => sprintf( __( '%1$s — %2$s h on %3$s', 'lawyers-foundation' ), $user->display_name, $hours, $date ),
			'post_content' => $desc,
		)
	);
	if ( $post_id && ! is_wp_error( $post_id ) ) {
		update_post_meta( $post_id, '_lf_hours', $hours );
		update_post_meta( $post_id, '_lf_date', $date );
		update_post_meta( $post_id, '_lf_initiative', $initiative );
		$notify = lf_opt( 'notify_email' );
		if ( $notify ) {
			wp_mail( $notify, __( 'Service hours submitted for review', 'lawyers-foundation' ), get_the_title( $post_id ) . "\n\n" . $desc . "\n\n" . admin_url( 'edit.php?post_type=lf_service&post_status=pending' ) );
		}
	}
	lf_redirect_with( 'hours' );
}
add_action( 'template_redirect', 'lf_handle_service_submit' );

/**
 * Service-hours form.
 *
 * @return string
 */
function lf_service_form() {
	$initiatives = get_posts( array( 'post_type' => 'lf_initiative', 'post_status' => 'publish', 'posts_per_page' => -1, 'orderby' => 'title', 'order' => 'ASC' ) );
	ob_start();
	?>
	<form class="lf-form" method="post">
		<?php wp_nonce_field( 'lf_log_hours', 'lf_nonce' ); ?>
		<input type="hidden" name="lf_action" value="log_hours">
		<div class="lf-grid-2">
			<p><label for="lf-date"><?php esc_html_e( 'Date', 'lawyers-foundation' ); ?></label><input id="lf-date" type="date" name="lf_date" required max="<?php echo esc_attr( current_time( 'Y-m-d' ) ); ?>" value="<?php echo esc_attr( current_time( 'Y-m-d' ) ); ?>"></p>
			<p><label for="lf-hours"><?php esc_html_e( 'Hours', 'lawyers-foundation' ); ?></label><input id="lf-hours" type="number" name="lf_hours" min="0.25" max="200" step="0.25" required></p>
		</div>
		<?php if ( $initiatives ) : ?>
			<p><label for="lf-init"><?php esc_html_e( 'Initiative', 'lawyers-foundation' ); ?></label>
			<select id="lf-init" name="lf_initiative"><option value="0"><?php esc_html_e( 'General pro bono work', 'lawyers-foundation' ); ?></option>
			<?php foreach ( $initiatives as $i ) : ?>
				<option value="<?php echo (int) $i->ID; ?>"><?php echo esc_html( get_the_title( $i ) ); ?></option>
			<?php endforeach; ?>
			</select></p>
		<?php endif; ?>
		<p><label for="lf-desc"><?php esc_html_e( 'What did you do? (no confidential client details)', 'lawyers-foundation' ); ?></label><textarea id="lf-desc" name="lf_desc" rows="3" required></textarea></p>
		<p><button type="submit" class="lf-btn"><?php esc_html_e( 'Submit hours', 'lawyers-foundation' ); ?></button></p>
	</form>
	<?php
	return ob_get_clean();
}

/**
 * A member's recent service logs.
 *
 * @param int $user_id User ID.
 * @return string
 */
function lf_service_history( $user_id ) {
	$logs = get_posts( array( 'post_type' => 'lf_service', 'post_status' => array( 'publish', 'pending', 'draft' ), 'author' => $user_id, 'posts_per_page' => 10 ) );
	if ( ! $logs ) {
		return '';
	}
	$html = '<h4>' . esc_html__( 'Recent submissions', 'lawyers-foundation' ) . '</h4><ul class="lf-list">';
	foreach ( $logs as $log ) {
		$status = 'publish' === $log->post_status ? __( 'Verified', 'lawyers-foundation' ) : __( 'Awaiting verification', 'lawyers-foundation' );
		$html  .= '<li>' . esc_html( get_post_meta( $log->ID, '_lf_date', true ) ) . ' · <strong>' . esc_html( get_post_meta( $log->ID, '_lf_hours', true ) ) . ' h</strong> <span class="lf-pill lf-pill--' . esc_attr( $log->post_status ) . '">' . esc_html( $status ) . '</span></li>';
	}
	return $html . '</ul>';
}

/**
 * Admin columns for service logs.
 *
 * @param array $columns Columns.
 * @return array
 */
function lf_service_columns( $columns ) {
	return array(
		'cb'            => $columns['cb'],
		'title'         => __( 'Log', 'lawyers-foundation' ),
		'lf_hours'      => __( 'Hours', 'lawyers-foundation' ),
		'lf_initiative' => __( 'Initiative', 'lawyers-foundation' ),
		'lf_status'     => __( 'Status', 'lawyers-foundation' ),
	);
}
add_filter( 'manage_lf_service_posts_columns', 'lf_service_columns' );

/**
 * Admin column values for service logs.
 *
 * @param string $column  Column.
 * @param int    $post_id Post ID.
 */
function lf_service_column_value( $column, $post_id ) {
	if ( 'lf_hours' === $column ) {
		echo esc_html( get_post_meta( $post_id, '_lf_hours', true ) );
	} elseif ( 'lf_initiative' === $column ) {
		$i = (int) get_post_meta( $post_id, '_lf_initiative', true );
		echo $i ? esc_html( get_the_title( $i ) ) : '—';
	} elseif ( 'lf_status' === $column ) {
		if ( 'publish' === get_post_status( $post_id ) ) {
			esc_html_e( 'Verified', 'lawyers-foundation' );
		} else {
			$url = wp_nonce_url( admin_url( 'admin-post.php?action=lf_verify_hours&post_id=' . $post_id ), 'lf_verify_' . $post_id );
			echo '<a class="button button-small button-primary" href="' . esc_url( $url ) . '">' . esc_html__( 'Verify', 'lawyers-foundation' ) . '</a>';
		}
	}
}
add_action( 'manage_lf_service_posts_custom_column', 'lf_service_column_value', 10, 2 );

/**
 * One-click verify a service log.
 */
function lf_verify_hours() {
	$post_id = isset( $_GET['post_id'] ) ? absint( $_GET['post_id'] ) : 0;
	if ( ! current_user_can( 'edit_post', $post_id ) || ! check_admin_referer( 'lf_verify_' . $post_id ) || 'lf_service' !== get_post_type( $post_id ) ) {
		wp_die( esc_html__( 'You are not allowed to do that.', 'lawyers-foundation' ) );
	}
	wp_update_post( array( 'ID' => $post_id, 'post_status' => 'publish' ) );
	wp_safe_redirect( wp_get_referer() ? wp_get_referer() : admin_url( 'edit.php?post_type=lf_service' ) );
	exit;
}
add_action( 'admin_post_lf_verify_hours', 'lf_verify_hours' );

/**
 * Hours meta box on the service-log edit screen.
 */
function lf_service_meta_box() {
	add_meta_box(
		'lf_service_details',
		__( 'Service details', 'lawyers-foundation' ),
		function ( $post ) {
			wp_nonce_field( 'lf_service_save', 'lf_service_nonce' );
			printf(
				'<p><label>%1$s <input type="number" step="0.25" min="0" name="lf_hours" value="%2$s"></label> &nbsp; <label>%3$s <input type="date" name="lf_date" value="%4$s"></label></p><p class="description">%5$s</p>',
				esc_html__( 'Hours', 'lawyers-foundation' ),
				esc_attr( get_post_meta( $post->ID, '_lf_hours', true ) ),
				esc_html__( 'Date', 'lawyers-foundation' ),
				esc_attr( get_post_meta( $post->ID, '_lf_date', true ) ),
				esc_html__( 'Publish this log to count it as verified.', 'lawyers-foundation' )
			);
		},
		'lf_service',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'lf_service_meta_box' );

/**
 * Save service-log meta from the admin.
 *
 * @param int $post_id Post ID.
 */
function lf_save_service( $post_id ) {
	if ( ! isset( $_POST['lf_service_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['lf_service_nonce'] ), 'lf_service_save' ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	if ( isset( $_POST['lf_hours'] ) ) {
		update_post_meta( $post_id, '_lf_hours', max( 0, round( (float) $_POST['lf_hours'], 2 ) ) );
	}
	if ( isset( $_POST['lf_date'] ) ) {
		update_post_meta( $post_id, '_lf_date', sanitize_text_field( wp_unslash( $_POST['lf_date'] ) ) );
	}
}
add_action( 'save_post_lf_service', 'lf_save_service' );

/**
 * [lf_initiatives limit="6"] shortcode.
 *
 * @param array $atts Attributes.
 * @return string
 */
function lf_shortcode_initiatives( $atts ) {
	$atts  = shortcode_atts( array( 'limit' => 6 ), $atts, 'lf_initiatives' );
	$items = get_posts( array( 'post_type' => 'lf_initiative', 'post_status' => 'publish', 'posts_per_page' => (int) $atts['limit'], 'orderby' => 'menu_order date', 'order' => 'ASC' ) );
	if ( ! $items ) {
		return '<p class="lf-muted">' . esc_html__( 'Our initiatives will be listed here soon.', 'lawyers-foundation' ) . '</p>';
	}
	$html = '<div class="lf-initiatives">';
	foreach ( $items as $item ) {
		$html .= '<article class="lf-initiative">';
		if ( has_post_thumbnail( $item ) ) {
			$html .= '<a href="' . esc_url( get_permalink( $item ) ) . '">' . get_the_post_thumbnail( $item, 'medium_large' ) . '</a>';
		}
		$html .= '<div class="lf-initiative-body"><h3><a href="' . esc_url( get_permalink( $item ) ) . '">' . esc_html( get_the_title( $item ) ) . '</a></h3><p>' . esc_html( wp_trim_words( get_the_excerpt( $item ), 28 ) ) . '</p></div></article>';
	}
	return $html . '</div>';
}
add_shortcode( 'lf_initiatives', 'lf_shortcode_initiatives' );

/**
 * [lf_impact] headline numbers.
 *
 * @return string
 */
function lf_shortcode_impact() {
	$members = count( get_users( array( 'role' => 'lf_member', 'fields' => 'ID' ) ) );
	$events  = count( lf_get_events( 'past', -1 ) );
	$inits   = (int) wp_count_posts( 'lf_initiative' )->publish;
	$stats   = array(
		array( number_format_i18n( $members ), __( 'Member lawyers', 'lawyers-foundation' ) ),
		array( number_format_i18n( lf_total_service_hours() ), __( 'Pro bono hours given', 'lawyers-foundation' ) ),
		array( number_format_i18n( $inits ), __( 'Active initiatives', 'lawyers-foundation' ) ),
		array( number_format_i18n( $events ), __( 'Events held', 'lawyers-foundation' ) ),
	);
	$html = '<div class="lf-impact">';
	foreach ( $stats as $s ) {
		$html .= '<div><strong>' . esc_html( $s[0] ) . '</strong><span>' . esc_html( $s[1] ) . '</span></div>';
	}
	return $html . '</div>';
}
add_shortcode( 'lf_impact', 'lf_shortcode_impact' );
