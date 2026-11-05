<?php
/**
 * Foundation events: details, the yearly cycle, RSVPs and attendee lists.
 *
 * @package Lawyers_Foundation
 */

defined( 'ABSPATH' ) || exit;

/* -------------------------------------------------------------------------
 * Data helpers
 * ---------------------------------------------------------------------- */

/**
 * Attendee user IDs for an event.
 *
 * @param int $event_id Event ID.
 * @return int[]
 */
function lf_get_attendees( $event_id ) {
	return array_values( array_unique( array_map( 'absint', (array) get_post_meta( $event_id, '_lf_attendees', true ) ) ) );
}

/**
 * Events by time window, ordered by start date.
 *
 * @param string $when  upcoming|past|all.
 * @param int    $limit Max events (-1 for all).
 * @param array  $extra Extra meta_query clauses.
 * @return WP_Post[]
 */
function lf_get_events( $when = 'upcoming', $limit = 6, $extra = array() ) {
	$now  = current_time( 'Y-m-d H:i' );
	$meta = array( array( 'key' => '_lf_start', 'compare' => 'EXISTS' ) );
	if ( 'upcoming' === $when ) {
		$meta[] = array( 'key' => '_lf_start', 'value' => $now, 'compare' => '>=' );
	} elseif ( 'past' === $when ) {
		$meta[] = array( 'key' => '_lf_start', 'value' => $now, 'compare' => '<' );
	}
	return get_posts(
		array(
			'post_type'      => 'lf_event',
			'post_status'    => 'publish',
			'posts_per_page' => $limit,
			'meta_key'       => '_lf_start', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'orderby'        => 'meta_value',
			'order'          => 'past' === $when ? 'DESC' : 'ASC',
			'meta_query'     => array_merge( $meta, $extra ), // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
		)
	);
}

/**
 * Upcoming events a user is registered for.
 *
 * @param int $user_id User ID.
 * @return WP_Post[]
 */
function lf_get_user_events( $user_id ) {
	return array_values(
		array_filter(
			lf_get_events( 'upcoming', -1 ),
			function ( $event ) use ( $user_id ) {
				return in_array( (int) $user_id, lf_get_attendees( $event->ID ), true );
			}
		)
	);
}

/**
 * Registration state for an event.
 *
 * @param int $event_id Event ID.
 * @return string open|closed|full|past
 */
function lf_event_status( $event_id ) {
	$start = get_post_meta( $event_id, '_lf_start', true );
	if ( $start && $start < current_time( 'Y-m-d H:i' ) ) {
		return 'past';
	}
	if ( ! get_post_meta( $event_id, '_lf_rsvp_open', true ) ) {
		return 'closed';
	}
	$cap = (int) get_post_meta( $event_id, '_lf_capacity', true );
	if ( $cap > 0 && count( lf_get_attendees( $event_id ) ) >= $cap ) {
		return 'full';
	}
	return 'open';
}

/* -------------------------------------------------------------------------
 * Admin: meta boxes, columns, CSV export
 * ---------------------------------------------------------------------- */

/**
 * Register event meta boxes.
 */
function lf_event_meta_boxes() {
	add_meta_box( 'lf_event_details', __( 'Event details', 'lawyers-foundation' ), 'lf_render_event_details_box', 'lf_event', 'normal', 'high' );
	add_meta_box( 'lf_event_attendees', __( 'Registered members', 'lawyers-foundation' ), 'lf_render_event_attendees_box', 'lf_event', 'side' );
}
add_action( 'add_meta_boxes', 'lf_event_meta_boxes' );

/**
 * Event details box.
 *
 * @param WP_Post $post Post.
 */
function lf_render_event_details_box( $post ) {
	wp_nonce_field( 'lf_event_save', 'lf_event_nonce' );
	$get   = function ( $key ) use ( $post ) {
		return get_post_meta( $post->ID, $key, true );
	};
	$is_new = 'auto-draft' === $post->post_status;
	$to_input = function ( $value ) {
		return $value ? str_replace( ' ', 'T', $value ) : '';
	};
	?>
	<table class="form-table" role="presentation">
		<tr><th><label for="lf_cycle"><?php esc_html_e( 'Foundation cycle slot', 'lawyers-foundation' ); ?></label></th>
			<td><select id="lf_cycle" name="lf_cycle">
				<option value=""><?php esc_html_e( '— Additional event (not a flagship) —', 'lawyers-foundation' ); ?></option>
				<?php foreach ( lf_event_cycle() as $slot ) : ?>
					<option value="<?php echo esc_attr( $slot ); ?>" <?php selected( $get( '_lf_cycle' ), $slot ); ?>><?php echo esc_html( $slot ); ?></option>
				<?php endforeach; ?>
			</select>
			<p class="description"><?php esc_html_e( 'Which of the yearly flagship events this is. Edit the cycle under Foundation → Settings.', 'lawyers-foundation' ); ?></p></td></tr>
		<tr><th><label for="lf_start"><?php esc_html_e( 'Starts', 'lawyers-foundation' ); ?> *</label></th><td><input type="datetime-local" id="lf_start" name="lf_start" value="<?php echo esc_attr( $to_input( $get( '_lf_start' ) ) ); ?>" required></td></tr>
		<tr><th><label for="lf_end"><?php esc_html_e( 'Ends', 'lawyers-foundation' ); ?></label></th><td><input type="datetime-local" id="lf_end" name="lf_end" value="<?php echo esc_attr( $to_input( $get( '_lf_end' ) ) ); ?>"></td></tr>
		<tr><th><label for="lf_venue"><?php esc_html_e( 'Venue', 'lawyers-foundation' ); ?></label></th><td><input type="text" class="regular-text" id="lf_venue" name="lf_venue" value="<?php echo esc_attr( $get( '_lf_venue' ) ); ?>"></td></tr>
		<tr><th><label for="lf_address"><?php esc_html_e( 'Address / online link', 'lawyers-foundation' ); ?></label></th><td><input type="text" class="large-text" id="lf_address" name="lf_address" value="<?php echo esc_attr( $get( '_lf_address' ) ); ?>"></td></tr>
		<tr><th><label for="lf_capacity"><?php esc_html_e( 'Capacity', 'lawyers-foundation' ); ?></label></th><td><input type="number" min="0" id="lf_capacity" name="lf_capacity" value="<?php echo esc_attr( $get( '_lf_capacity' ) ); ?>"> <span class="description"><?php esc_html_e( '0 or empty = unlimited', 'lawyers-foundation' ); ?></span></td></tr>
		<tr><th><?php esc_html_e( 'Registration', 'lawyers-foundation' ); ?></th><td>
			<label><input type="checkbox" name="lf_rsvp_open" value="1" <?php checked( $is_new || $get( '_lf_rsvp_open' ) ); ?>> <?php esc_html_e( 'Open for member registration', 'lawyers-foundation' ); ?></label><br>
			<label><input type="checkbox" name="lf_cpd" value="1" <?php checked( (bool) $get( '_lf_cpd' ) ); ?>> <?php esc_html_e( 'Counts towards CPD / CLE credit', 'lawyers-foundation' ); ?></label>
		</td></tr>
	</table>
	<?php
}

/**
 * Attendees box with export link.
 *
 * @param WP_Post $post Post.
 */
function lf_render_event_attendees_box( $post ) {
	$ids = lf_get_attendees( $post->ID );
	$cap = (int) get_post_meta( $post->ID, '_lf_capacity', true );
	echo '<p><strong>' . (int) count( $ids ) . '</strong>' . ( $cap ? ' / ' . (int) $cap : '' ) . ' ' . esc_html__( 'registered', 'lawyers-foundation' ) . '</p>';
	if ( $ids ) {
		echo '<ol style="max-height:240px;overflow:auto">';
		foreach ( $ids as $id ) {
			$u = get_userdata( $id );
			if ( $u ) {
				echo '<li><a href="' . esc_url( get_edit_user_link( $id ) ) . '">' . esc_html( $u->display_name ) . '</a></li>';
			}
		}
		echo '</ol>';
		$url = wp_nonce_url( admin_url( 'admin-post.php?action=lf_export_attendees&event_id=' . $post->ID ), 'lf_export_' . $post->ID );
		echo '<p><a class="button" href="' . esc_url( $url ) . '">' . esc_html__( 'Download attendee list (CSV)', 'lawyers-foundation' ) . '</a></p>';
	}
}

/**
 * Save event meta.
 *
 * @param int $post_id Post ID.
 */
function lf_save_event( $post_id ) {
	if ( ! isset( $_POST['lf_event_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['lf_event_nonce'] ), 'lf_event_save' ) ) {
		return;
	}
	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	$data = wp_unslash( $_POST );

	foreach ( array( 'lf_start' => '_lf_start', 'lf_end' => '_lf_end' ) as $field => $key ) {
		$raw  = isset( $data[ $field ] ) ? sanitize_text_field( $data[ $field ] ) : '';
		$date = date_create_from_format( 'Y-m-d\TH:i', $raw );
		if ( $date ) {
			update_post_meta( $post_id, $key, $date->format( 'Y-m-d H:i' ) );
		} else {
			delete_post_meta( $post_id, $key );
		}
	}

	$cycle = isset( $data['lf_cycle'] ) ? sanitize_text_field( $data['lf_cycle'] ) : '';
	update_post_meta( $post_id, '_lf_cycle', in_array( $cycle, lf_event_cycle(), true ) ? $cycle : '' );
	update_post_meta( $post_id, '_lf_venue', sanitize_text_field( isset( $data['lf_venue'] ) ? $data['lf_venue'] : '' ) );
	update_post_meta( $post_id, '_lf_address', sanitize_text_field( isset( $data['lf_address'] ) ? $data['lf_address'] : '' ) );
	update_post_meta( $post_id, '_lf_capacity', absint( isset( $data['lf_capacity'] ) ? $data['lf_capacity'] : 0 ) );
	update_post_meta( $post_id, '_lf_rsvp_open', empty( $data['lf_rsvp_open'] ) ? 0 : 1 );
	update_post_meta( $post_id, '_lf_cpd', empty( $data['lf_cpd'] ) ? 0 : 1 );
}
add_action( 'save_post_lf_event', 'lf_save_event' );

/**
 * Event list columns.
 *
 * @param array $columns Columns.
 * @return array
 */
function lf_event_columns( $columns ) {
	$date = isset( $columns['date'] ) ? $columns['date'] : '';
	unset( $columns['date'] );
	$columns['lf_when']      = __( 'Event date', 'lawyers-foundation' );
	$columns['lf_cycle']     = __( 'Cycle slot', 'lawyers-foundation' );
	$columns['lf_attendees'] = __( 'Registered', 'lawyers-foundation' );
	$columns['date']         = $date;
	return $columns;
}
add_filter( 'manage_lf_event_posts_columns', 'lf_event_columns' );

/**
 * Event list column values.
 *
 * @param string $column  Column.
 * @param int    $post_id Post ID.
 */
function lf_event_column_value( $column, $post_id ) {
	if ( 'lf_when' === $column ) {
		echo esc_html( lf_format_date( get_post_meta( $post_id, '_lf_start', true ) ) );
	} elseif ( 'lf_cycle' === $column ) {
		echo esc_html( get_post_meta( $post_id, '_lf_cycle', true ) );
	} elseif ( 'lf_attendees' === $column ) {
		$cap = (int) get_post_meta( $post_id, '_lf_capacity', true );
		echo (int) count( lf_get_attendees( $post_id ) ) . ( $cap ? ' / ' . (int) $cap : '' );
	}
}
add_action( 'manage_lf_event_posts_custom_column', 'lf_event_column_value', 10, 2 );

/**
 * Download attendees as CSV.
 */
function lf_export_attendees() {
	$event_id = isset( $_GET['event_id'] ) ? absint( $_GET['event_id'] ) : 0;
	if ( ! current_user_can( 'edit_post', $event_id ) || ! check_admin_referer( 'lf_export_' . $event_id ) ) {
		wp_die( esc_html__( 'You are not allowed to do that.', 'lawyers-foundation' ) );
	}
	nocache_headers();
	header( 'Content-Type: text/csv; charset=utf-8' );
	header( 'Content-Disposition: attachment; filename="attendees-' . sanitize_title( get_the_title( $event_id ) ) . '.csv"' );
	$out = fopen( 'php://output', 'w' );
	fputcsv( $out, array( 'Name', 'Email', 'Bar / licence no.', 'Jurisdiction', 'Firm', 'Phone' ) );
	foreach ( lf_get_attendees( $event_id ) as $id ) {
		$u = get_userdata( $id );
		if ( ! $u ) {
			continue;
		}
		$row = array( $u->display_name, $u->user_email, get_user_meta( $id, 'lf_bar_number', true ), get_user_meta( $id, 'lf_jurisdiction', true ), get_user_meta( $id, 'lf_firm', true ), get_user_meta( $id, 'lf_phone', true ) );
		// Neutralise spreadsheet formula injection.
		$row = array_map(
			function ( $cell ) {
				return preg_match( '/^[=+\-@]/', (string) $cell ) ? "'" . $cell : $cell;
			},
			$row
		);
		fputcsv( $out, $row );
	}
	fclose( $out ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose
	exit;
}
add_action( 'admin_post_lf_export_attendees', 'lf_export_attendees' );

/**
 * Order the public event archive by event date.
 *
 * @param WP_Query $query Query.
 */
function lf_event_archive_order( $query ) {
	if ( ! is_admin() && $query->is_main_query() && $query->is_post_type_archive( 'lf_event' ) ) {
		$query->set( 'meta_key', '_lf_start' );
		$query->set( 'orderby', 'meta_value' );
		$query->set( 'order', 'DESC' );
	}
}
add_action( 'pre_get_posts', 'lf_event_archive_order' );

/* -------------------------------------------------------------------------
 * RSVP handling
 * ---------------------------------------------------------------------- */

/**
 * Register / cancel a member's RSVP.
 */
function lf_handle_rsvp() {
	if ( empty( $_POST['lf_action'] ) || ! in_array( $_POST['lf_action'], array( 'rsvp', 'rsvp_cancel' ), true ) ) {
		return;
	}
	$event_id = isset( $_POST['event_id'] ) ? absint( $_POST['event_id'] ) : 0;
	if ( ! isset( $_POST['lf_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['lf_nonce'] ), 'lf_rsvp_' . $event_id ) ) {
		wp_die( esc_html__( 'Your session expired. Please go back and try again.', 'lawyers-foundation' ) );
	}
	if ( ! lf_is_member() || 'lf_event' !== get_post_type( $event_id ) || 'publish' !== get_post_status( $event_id ) ) {
		wp_die( esc_html__( 'Only approved members can register for foundation events.', 'lawyers-foundation' ) );
	}

	$user_id   = get_current_user_id();
	$attendees = lf_get_attendees( $event_id );
	$url       = get_permalink( $event_id );

	if ( 'rsvp_cancel' === $_POST['lf_action'] ) {
		update_post_meta( $event_id, '_lf_attendees', array_values( array_diff( $attendees, array( $user_id ) ) ) );
		lf_redirect_with( 'rsvp_cancel', $url );
	}

	if ( in_array( $user_id, $attendees, true ) ) {
		lf_redirect_with( 'rsvp', $url );
	}
	$status = lf_event_status( $event_id );
	if ( 'full' === $status ) {
		lf_redirect_with( 'rsvp_full', $url );
	}
	if ( 'open' !== $status ) {
		lf_redirect_with( 'rsvp_closed', $url );
	}

	$attendees[] = $user_id;
	update_post_meta( $event_id, '_lf_attendees', $attendees );

	$user = wp_get_current_user();
	wp_mail(
		$user->user_email,
		/* translators: %s: event title */
		sprintf( __( 'You are registered: %s', 'lawyers-foundation' ), get_the_title( $event_id ) ),
		sprintf(
			"%s\n%s\n%s\n\n%s",
			get_the_title( $event_id ),
			lf_format_date( get_post_meta( $event_id, '_lf_start', true ) ),
			trim( get_post_meta( $event_id, '_lf_venue', true ) . ' ' . get_post_meta( $event_id, '_lf_address', true ) ),
			$url
		)
	);
	lf_redirect_with( 'rsvp', $url );
}
add_action( 'template_redirect', 'lf_handle_rsvp' );

/* -------------------------------------------------------------------------
 * Front end
 * ---------------------------------------------------------------------- */

/**
 * RSVP box for an event.
 *
 * @param int $event_id Event ID.
 * @return string
 */
function lf_rsvp_box( $event_id ) {
	$status    = lf_event_status( $event_id );
	$attendees = lf_get_attendees( $event_id );
	$cap       = (int) get_post_meta( $event_id, '_lf_capacity', true );

	$html = '<div class="lf-rsvp">';
	if ( $cap && 'past' !== $status ) {
		/* translators: %d: seats left */
		$html .= '<p class="lf-muted">' . esc_html( sprintf( _n( '%d seat left', '%d seats left', max( 0, $cap - count( $attendees ) ), 'lawyers-foundation' ), max( 0, $cap - count( $attendees ) ) ) ) . '</p>';
	}

	if ( 'past' === $status ) {
		return $html . '<p>' . esc_html__( 'This event has taken place. Thank you to everyone who joined us.', 'lawyers-foundation' ) . '</p></div>';
	}
	if ( ! is_user_logged_in() ) {
		return $html . '<p>' . esc_html__( 'Registration is for foundation members.', 'lawyers-foundation' ) . '</p><p><a class="lf-btn" href="' . esc_url( wp_login_url( get_permalink( $event_id ) ) ) . '">' . esc_html__( 'Sign in to register', 'lawyers-foundation' ) . '</a> <a class="lf-btn lf-btn--ghost" href="' . esc_url( lf_page_url( 'join' ) ) . '">' . esc_html__( 'Become a member', 'lawyers-foundation' ) . '</a></p></div>';
	}
	if ( ! lf_is_member() ) {
		return $html . '<p>' . esc_html__( 'Registration opens to you once your membership is approved.', 'lawyers-foundation' ) . '</p></div>';
	}

	$going = in_array( get_current_user_id(), $attendees, true );
	$form  = '<form method="post">' . wp_nonce_field( 'lf_rsvp_' . $event_id, 'lf_nonce', true, false ) . '<input type="hidden" name="event_id" value="' . (int) $event_id . '">';

	if ( $going ) {
		$html .= '<p class="lf-going">✓ ' . esc_html__( 'You are registered for this event.', 'lawyers-foundation' ) . '</p>';
		$html .= $form . '<input type="hidden" name="lf_action" value="rsvp_cancel"><button class="lf-btn lf-btn--ghost" type="submit">' . esc_html__( 'Cancel my registration', 'lawyers-foundation' ) . '</button></form>';
	} elseif ( 'open' === $status ) {
		$html .= $form . '<input type="hidden" name="lf_action" value="rsvp"><button class="lf-btn" type="submit">' . esc_html__( 'Register to attend', 'lawyers-foundation' ) . '</button></form>';
	} elseif ( 'full' === $status ) {
		$html .= '<p>' . esc_html__( 'This event is fully booked.', 'lawyers-foundation' ) . '</p>';
	} else {
		$html .= '<p>' . esc_html__( 'Registration is not open yet.', 'lawyers-foundation' ) . '</p>';
	}
	return $html . '</div>';
}

/**
 * Event facts list.
 *
 * @param int $event_id Event ID.
 * @return string
 */
function lf_event_facts( $event_id ) {
	$rows = array(
		__( 'When', 'lawyers-foundation' )  => lf_format_date( get_post_meta( $event_id, '_lf_start', true ) ) . ( get_post_meta( $event_id, '_lf_end', true ) ? ' – ' . lf_format_date( get_post_meta( $event_id, '_lf_end', true ) ) : '' ),
		__( 'Venue', 'lawyers-foundation' ) => get_post_meta( $event_id, '_lf_venue', true ),
		__( 'Where', 'lawyers-foundation' ) => get_post_meta( $event_id, '_lf_address', true ),
	);
	$html = '<dl class="lf-facts">';
	foreach ( $rows as $label => $value ) {
		if ( $value ) {
			$html .= '<div><dt>' . esc_html( $label ) . '</dt><dd>' . ( wp_http_validate_url( $value ) ? '<a href="' . esc_url( $value ) . '" rel="noopener" target="_blank">' . esc_html__( 'Join online', 'lawyers-foundation' ) . '</a>' : esc_html( $value ) ) . '</dd></div>';
		}
	}
	if ( get_post_meta( $event_id, '_lf_cpd', true ) ) {
		$html .= '<div><dt>' . esc_html__( 'Credit', 'lawyers-foundation' ) . '</dt><dd>' . esc_html__( 'Counts towards CPD / CLE', 'lawyers-foundation' ) . '</dd></div>';
	}
	return $html . '</dl>';
}

/**
 * Add details and the RSVP box to single event pages.
 *
 * @param string $content Content.
 * @return string
 */
function lf_event_content( $content ) {
	if ( ! is_singular( 'lf_event' ) || ! in_the_loop() || ! is_main_query() ) {
		return $content;
	}
	$id    = get_the_ID();
	$cycle = get_post_meta( $id, '_lf_cycle', true );
	$top   = lf_flash() . '<div class="lf-event-box lf-card">' . ( $cycle ? '<p class="lf-eyebrow">' . esc_html( $cycle ) . '</p>' : '' ) . lf_event_facts( $id ) . lf_rsvp_box( $id ) . '</div>';
	return $top . $content;
}
add_filter( 'the_content', 'lf_event_content' );

/**
 * Event card markup.
 *
 * @param WP_Post $event Event.
 * @return string
 */
function lf_event_card( $event ) {
	$start = get_post_meta( $event->ID, '_lf_start', true );
	$date  = $start ? date_create_immutable_from_format( 'Y-m-d H:i', $start, wp_timezone() ) : false;
	$cycle = get_post_meta( $event->ID, '_lf_cycle', true );
	ob_start();
	?>
	<article class="lf-event-card">
		<?php if ( $date ) : ?>
			<div class="lf-date"><span><?php echo esc_html( wp_date( 'M', $date->getTimestamp() ) ); ?></span><strong><?php echo esc_html( wp_date( 'j', $date->getTimestamp() ) ); ?></strong><span><?php echo esc_html( wp_date( 'Y', $date->getTimestamp() ) ); ?></span></div>
		<?php endif; ?>
		<div>
			<?php if ( $cycle ) : ?><p class="lf-eyebrow"><?php echo esc_html( $cycle ); ?></p><?php endif; ?>
			<h3><a href="<?php echo esc_url( get_permalink( $event ) ); ?>"><?php echo esc_html( get_the_title( $event ) ); ?></a></h3>
			<p class="lf-muted"><?php echo esc_html( implode( ' · ', array_filter( array( $date ? wp_date( get_option( 'time_format' ), $date->getTimestamp() ) : '', get_post_meta( $event->ID, '_lf_venue', true ) ) ) ) ); ?></p>
			<p><?php echo esc_html( wp_trim_words( get_the_excerpt( $event ), 24 ) ); ?></p>
		</div>
	</article>
	<?php
	return ob_get_clean();
}

/**
 * [lf_events show="upcoming|past" limit="6"] shortcode.
 *
 * @param array $atts Attributes.
 * @return string
 */
function lf_shortcode_events( $atts ) {
	$atts   = shortcode_atts( array( 'show' => 'upcoming', 'limit' => 6 ), $atts, 'lf_events' );
	$when   = in_array( $atts['show'], array( 'upcoming', 'past', 'all' ), true ) ? $atts['show'] : 'upcoming';
	$events = lf_get_events( $when, (int) $atts['limit'] );
	if ( ! $events ) {
		return '<p class="lf-muted">' . ( 'past' === $when ? esc_html__( 'No past events yet.', 'lawyers-foundation' ) : esc_html__( 'New events will be announced soon.', 'lawyers-foundation' ) ) . '</p>';
	}
	return '<div class="lf-events">' . implode( '', array_map( 'lf_event_card', $events ) ) . '</div>';
}
add_shortcode( 'lf_events', 'lf_shortcode_events' );

/**
 * [lf_event_cycle year="2027"]: one card per flagship slot in the yearly cycle.
 *
 * @param array $atts Attributes.
 * @return string
 */
function lf_shortcode_event_cycle( $atts ) {
	$atts  = shortcode_atts( array( 'year' => '' ), $atts, 'lf_event_cycle' );
	$year  = preg_match( '/^\d{4}$/', (string) $atts['year'] ) ? (int) $atts['year'] : 0;
	$slots = lf_event_cycle();

	if ( ! $year ) {
		// Show the current year until its last flagship has passed, then next year.
		$year = (int) current_time( 'Y' );
		$left = lf_get_events( 'upcoming', 1, array( array( 'key' => '_lf_start', 'value' => $year . '-12-31 23:59', 'compare' => '<=' ), array( 'key' => '_lf_cycle', 'value' => '', 'compare' => '!=' ) ) );
		$any  = lf_get_events( 'all', 1, array( array( 'key' => '_lf_start', 'value' => array( $year . '-01-01 00:00', $year . '-12-31 23:59' ), 'compare' => 'BETWEEN' ), array( 'key' => '_lf_cycle', 'value' => '', 'compare' => '!=' ) ) );
		if ( $any && ! $left ) {
			++$year;
		}
	}

	$html = '<div class="lf-cycle lf-cycle--' . count( $slots ) . '">';
	foreach ( $slots as $i => $slot ) {
		$found = lf_get_events(
			'all',
			1,
			array(
				array( 'key' => '_lf_cycle', 'value' => $slot ),
				array( 'key' => '_lf_start', 'value' => array( $year . '-01-01 00:00', $year . '-12-31 23:59' ), 'compare' => 'BETWEEN' ),
			)
		);
		$html .= '<article class="lf-cycle-slot"><p class="lf-eyebrow">' . esc_html( sprintf( /* translators: 1: number, 2: total, 3: year */ __( 'Event %1$d of %2$d · %3$d', 'lawyers-foundation' ), $i + 1, count( $slots ), $year ) ) . '</p><h3>' . esc_html( $slot ) . '</h3>';
		if ( $found ) {
			$e     = $found[0];
			$html .= '<p><a href="' . esc_url( get_permalink( $e ) ) . '">' . esc_html( get_the_title( $e ) ) . '</a></p><p class="lf-muted">' . esc_html( lf_format_date( get_post_meta( $e->ID, '_lf_start', true ), get_option( 'date_format' ) ) ) . '</p>';
			$st    = lf_event_status( $e->ID );
			$label = array(
				'open'   => __( 'Registration open', 'lawyers-foundation' ),
				'full'   => __( 'Fully booked', 'lawyers-foundation' ),
				'closed' => __( 'Registration opens soon', 'lawyers-foundation' ),
				'past'   => __( 'Held', 'lawyers-foundation' ),
			);
			$html .= '<span class="lf-pill lf-pill--' . esc_attr( $st ) . '">' . esc_html( $label[ $st ] ) . '</span>';
		} else {
			$html .= '<p class="lf-muted">' . esc_html__( 'Date to be announced', 'lawyers-foundation' ) . '</p>';
		}
		$html .= '</article>';
	}
	return $html . '</div>';
}
add_shortcode( 'lf_event_cycle', 'lf_shortcode_event_cycle' );
