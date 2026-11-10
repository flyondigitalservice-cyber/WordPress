<?php
/**
 * Admin side of publisher submissions.
 *
 * REVIEW A SUBMISSION
 *   Properties shows a red count for pending listings (theme feature).
 *   Open the listing, check the details, then either:
 *     - click Publish            -> goes live, the publisher gets a "your listing is live" email
 *     - tick "Send back for changes" in the Publisher Review box, add a note and Update
 *                                -> hidden from the site, the publisher sees the note and can resubmit
 *
 * Also in this file: publishers are kept out of wp-admin, and the Properties
 * list shows who submitted each listing.
 *
 * @package PropzedPublisher
 */

defined( 'ABSPATH' ) || exit;

/**
 * Keep publisher-only accounts out of wp-admin (form posts and AJAX still work).
 * Runs on "init" so they are redirected before WordPress prints its own
 * "you are not allowed" screen.
 */
add_action(
	'init',
	static function () {
		global $pagenow;
		if ( ! is_admin() || wp_doing_ajax() || 'admin-post.php' === $pagenow || ! pzp_is_publisher_only() ) {
			return;
		}
		wp_safe_redirect( pzp_publisher_url() );
		exit;
	},
	20
);

add_filter(
	'show_admin_bar',
	static function ( $show ) {
		return pzp_is_publisher_only() ? false : $show;
	}
);

/**
 * After logging in through wp-login.php, publishers land in the publisher area.
 */
add_filter(
	'login_redirect',
	static function ( $redirect, $requested, $user ) {
		return ( $user instanceof WP_User && pzp_is_publisher_only( $user ) ) ? pzp_publisher_url() : $redirect;
	},
	10,
	3
);

/**
 * Phone and company on the user profile screen (Users > Edit).
 */
add_filter(
	'user_contactmethods',
	static function ( $methods ) {
		$methods['pzp_phone']   = __( 'Mobile number (publisher)', 'propzed-publisher' );
		$methods['pzp_company'] = __( 'Company (publisher)', 'propzed-publisher' );
		return $methods;
	}
);

/* -------------------------------------------------------------------------
 * Review box on the property edit screen
 * ---------------------------------------------------------------------- */

add_action(
	'add_meta_boxes_property',
	static function ( $post ) {
		if ( get_post_meta( $post->ID, '_pzp_submitted', true ) ) {
			add_meta_box( 'pzp_review', __( 'Publisher Review', 'propzed-publisher' ), 'pzp_render_review_box', 'property', 'side', 'high' );
		}
	}
);

/**
 * Render the review box.
 *
 * @param WP_Post $post Property.
 */
function pzp_render_review_box( $post ) {
	$author = get_userdata( $post->post_author );
	$state  = pzp_listing_state( $post );
	wp_nonce_field( 'pzp_review', 'pzp_review_nonce' );

	if ( $author ) {
		$phone = get_user_meta( $author->ID, 'pzp_phone', true );
		echo '<p><strong>' . esc_html( $author->display_name ) . '</strong><br>';
		if ( $phone ) {
			echo '<a href="tel:' . esc_attr( preg_replace( '/[^0-9+]/', '', $phone ) ) . '">' . esc_html( $phone ) . '</a><br>';
		}
		echo '<a href="mailto:' . esc_attr( $author->user_email ) . '">' . esc_html( $author->user_email ) . '</a></p>';
	}
	/* translators: %s: listing state */
	echo '<p>' . esc_html( sprintf( __( 'Publisher sees: %s', 'propzed-publisher' ), $state[1] ) ) . '</p>';
	echo '<p><label for="pzp_review_note"><strong>' . esc_html__( 'Note to the publisher', 'propzed-publisher' ) . '</strong></label>';
	echo '<textarea id="pzp_review_note" name="pzp_review_note" rows="4" style="width:100%">' . esc_textarea( get_post_meta( $post->ID, '_pzp_review_note', true ) ) . '</textarea></p>';
	echo '<p><label><input type="checkbox" name="pzp_send_back" value="1"> ' . esc_html__( 'Send back for changes (hides the listing and emails this note)', 'propzed-publisher' ) . '</label></p>';
	echo '<p class="description">' . esc_html__( 'To approve, click Publish. The publisher is emailed automatically.', 'propzed-publisher' ) . '</p>';
}

/**
 * Save the review note and, when asked, send the listing back.
 *
 * @param int $post_id Property ID.
 */
function pzp_save_review( $post_id ) {
	if ( ! isset( $_POST['pzp_review_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['pzp_review_nonce'] ), 'pzp_review' ) ) {
		return;
	}
	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || wp_is_post_revision( $post_id ) || ! current_user_can( 'publish_posts' ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$note = isset( $_POST['pzp_review_note'] ) ? sanitize_textarea_field( wp_unslash( $_POST['pzp_review_note'] ) ) : '';
	update_post_meta( $post_id, '_pzp_review_note', $note );

	if ( empty( $_POST['pzp_send_back'] ) ) {
		return;
	}

	remove_action( 'save_post_property', 'pzp_save_review', 30 );
	wp_update_post(
		array(
			'ID'          => $post_id,
			'post_status' => 'draft',
		)
	);
	add_action( 'save_post_property', 'pzp_save_review', 30 );
	update_post_meta( $post_id, '_pzp_state', 'changes' );

	$author = get_userdata( get_post_field( 'post_author', $post_id ) );
	if ( $author ) {
		wp_mail(
			$author->user_email,
			/* translators: %s: site name */
			sprintf( __( '[%s] Changes needed on your listing', 'propzed-publisher' ), get_bloginfo( 'name' ) ),
			sprintf(
				/* translators: 1: name 2: property title 3: note 4: URL */
				__( "Hello %1\$s,\n\nOur team reviewed \"%2\$s\" and needs a few changes before it can go live:\n\n%3\$s\n\nUpdate and resubmit it here: %4\$s", 'propzed-publisher' ),
				$author->display_name,
				get_the_title( $post_id ),
				$note ? $note : __( '(Please call our office for details.)', 'propzed-publisher' ),
				pzp_publisher_url(
					array(
						'view' => 'edit',
						'id'   => $post_id,
					)
				)
			)
		);
	}
}
add_action( 'save_post_property', 'pzp_save_review', 30 );

/**
 * Email the publisher when the admin publishes their listing.
 *
 * @param string  $new_status New status.
 * @param string  $old_status Old status.
 * @param WP_Post $post       Post.
 */
function pzp_notify_published( $new_status, $old_status, $post ) {
	if ( 'publish' !== $new_status || 'publish' === $old_status || 'property' !== $post->post_type || ! get_post_meta( $post->ID, '_pzp_submitted', true ) ) {
		return;
	}
	delete_post_meta( $post->ID, '_pzp_state' );
	pzp_purge_url( pzp_builders_url() );

	$author = get_userdata( $post->post_author );
	if ( ! $author || ! pzp_is_publisher_only( $author ) ) {
		return;
	}
	wp_mail(
		$author->user_email,
		/* translators: %s: site name */
		sprintf( __( '[%s] Your listing is live', 'propzed-publisher' ), get_bloginfo( 'name' ) ),
		sprintf(
			/* translators: 1: name 2: property title 3: property URL 4: publisher area URL */
			__( "Hello %1\$s,\n\nGood news: \"%2\$s\" has been verified and is now live.\n\nView it: %3\$s\nManage your listings: %4\$s", 'propzed-publisher' ),
			$author->display_name,
			$post->post_title,
			get_permalink( $post ),
			pzp_publisher_url()
		)
	);
}
add_action( 'transition_post_status', 'pzp_notify_published', 10, 3 );

/* -------------------------------------------------------------------------
 * Properties list: who submitted the listing
 * ---------------------------------------------------------------------- */

add_filter(
	'manage_property_posts_columns',
	static function ( $columns ) {
		$columns['pzp_publisher'] = __( 'Submitted by', 'propzed-publisher' );
		return $columns;
	},
	20
);

add_action(
	'manage_property_posts_custom_column',
	static function ( $column, $post_id ) {
		if ( 'pzp_publisher' !== $column ) {
			return;
		}
		if ( ! get_post_meta( $post_id, '_pzp_submitted', true ) ) {
			echo '–';
			return;
		}
		$author = get_userdata( get_post_field( 'post_author', $post_id ) );
		echo esc_html( $author ? $author->display_name : __( 'Publisher', 'propzed-publisher' ) );
		$phone = $author ? get_user_meta( $author->ID, 'pzp_phone', true ) : '';
		if ( $phone ) {
			echo '<br><small>' . esc_html( $phone ) . '</small>';
		}
	},
	10,
	2
);
