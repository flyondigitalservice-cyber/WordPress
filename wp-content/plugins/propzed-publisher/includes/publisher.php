<?php
/**
 * Publisher accounts.
 *
 * A "Publisher" (owner, builder or broker) can ONLY:
 *   - create an account and log in at /publisher/
 *   - submit a property, edit or withdraw their own properties
 * Every submission and every edit is saved as PENDING. Nothing is shown on the
 * site until an administrator opens it under Properties and clicks Publish.
 * Publishers have no access to wp-admin, the media library, other people's
 * listings, featured / sponsored flags or SEO fields.
 *
 * Forms post to wp-admin/admin-post.php (same convention as the theme):
 *   pzp_register, pzp_login, pzp_profile, pzp_save_property, pzp_withdraw
 *
 * @package PropzedPublisher
 */

defined( 'ABSPATH' ) || exit;

const PZP_ROLE = 'pz_publisher';
const PZP_CAP  = 'pzp_submit_property';

/**
 * Create the Publisher role (once) and let administrators use the publisher area too.
 */
function pzp_register_role() {
	if ( ! get_role( PZP_ROLE ) ) {
		add_role(
			PZP_ROLE,
			__( 'Publisher (property submitter)', 'propzed-publisher' ),
			array(
				'read'  => true,
				PZP_CAP => true,
			)
		);
	}
	$admin = get_role( 'administrator' );
	if ( $admin && ! $admin->has_cap( PZP_CAP ) ) {
		$admin->add_cap( PZP_CAP );
	}
}
add_action( 'init', 'pzp_register_role', 5 );

/**
 * Is this user a publisher-only account (no editing rights in wp-admin)?
 *
 * @param WP_User|null $user User (defaults to the current user).
 * @return bool
 */
function pzp_is_publisher_only( $user = null ) {
	$user = $user ? $user : wp_get_current_user();
	return $user && $user->exists() && in_array( PZP_ROLE, (array) $user->roles, true ) && ! user_can( $user, 'edit_posts' );
}

/**
 * Property fields a publisher may fill (theme field keys, see inc/property-fields.php).
 * Featured, sponsored, SEO and seller fields are deliberately NOT in this list.
 *
 * @return string[]
 */
function pzp_publisher_fields() {
	return apply_filters(
		'pzp_publisher_fields',
		array( 'address', 'pincode', 'area', 'area_unit', 'price', 'price_unit', 'possession', 'developer', 'possession_date', 'approvals', 'features', 'video' )
	);
}

/**
 * Who a publisher is: options for the "You are" field.
 *
 * @return string[]
 */
function pzp_publisher_types() {
	return array(
		__( 'Owner', 'propzed-publisher' ),
		__( 'Developer / Builder', 'propzed-publisher' ),
		__( 'Broker / Channel Partner', 'propzed-publisher' ),
		__( 'Company / Institution', 'propzed-publisher' ),
	);
}

/**
 * Listing state as the publisher sees it.
 *
 * @param WP_Post $post Property.
 * @return array [ key, label ].
 */
function pzp_listing_state( $post ) {
	if ( 'publish' === $post->post_status ) {
		return array( 'live', __( 'Live', 'propzed-publisher' ) );
	}
	if ( 'pending' === $post->post_status ) {
		return array( 'review', __( 'Under review', 'propzed-publisher' ) );
	}
	if ( 'changes' === get_post_meta( $post->ID, '_pzp_state', true ) ) {
		return array( 'changes', __( 'Changes requested', 'propzed-publisher' ) );
	}
	return array( 'withdrawn', __( 'Withdrawn', 'propzed-publisher' ) );
}

/**
 * A publisher's own properties.
 *
 * @param int $user_id User ID.
 * @return WP_Post[]
 */
function pzp_get_user_properties( $user_id ) {
	return get_posts(
		array(
			'post_type'   => 'property',
			'post_status' => array( 'publish', 'pending', 'draft' ),
			'author'      => (int) $user_id,
			'numberposts' => 200,
			'orderby'     => 'modified',
			'order'       => 'DESC',
		)
	);
}

/**
 * Get one property if (and only if) it belongs to the user.
 *
 * @param int $post_id Property ID.
 * @param int $user_id User ID.
 * @return WP_Post|null
 */
function pzp_get_own_property( $post_id, $user_id ) {
	$post = get_post( (int) $post_id );
	if ( ! $post || 'property' !== $post->post_type || (int) $post->post_author !== (int) $user_id || ! in_array( $post->post_status, array( 'publish', 'pending', 'draft' ), true ) ) {
		return null;
	}
	return $post;
}

/**
 * Number of buyer enquiries received for a property.
 *
 * @param int $post_id Property ID.
 * @return int
 */
function pzp_enquiry_count( $post_id ) {
	$query = new WP_Query(
		array(
			'post_type'      => 'pz_enquiry',
			'post_status'    => 'publish',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'meta_key'       => '_pz_property_id', // phpcs:ignore WordPress.DB.SlowDBQuery
			'meta_value'     => (int) $post_id, // phpcs:ignore WordPress.DB.SlowDBQuery
		)
	);
	return (int) $query->found_posts;
}

/* -------------------------------------------------------------------------
 * Saving a submission (shared by the website form and the app API)
 * ---------------------------------------------------------------------- */

/**
 * Create or update a property for a publisher. Always ends as PENDING.
 *
 * @param int   $user_id Publisher.
 * @param array $data    Unslashed input: title, description, type, location, status (term IDs),
 *                       meta[ field => value ], remove[] (attachment IDs).
 * @param array $files   $_FILES-style array of photos (may be empty).
 * @param int   $post_id Existing property to update (0 = new).
 * @return array|WP_Error [ 'id' => int, 'skipped' => int ].
 */
function pzp_save_submission( $user_id, $data, $files = array(), $post_id = 0 ) {
	$user = get_userdata( $user_id );
	if ( ! $user || ! user_can( $user, PZP_CAP ) ) {
		return new WP_Error( 'pzp_forbidden', __( 'This account cannot submit properties.', 'propzed-publisher' ), array( 'status' => 403 ) );
	}

	$existing = null;
	if ( $post_id ) {
		$existing = pzp_get_own_property( $post_id, $user_id );
		if ( ! $existing ) {
			return new WP_Error( 'pzp_not_found', __( 'Property not found.', 'propzed-publisher' ), array( 'status' => 404 ) );
		}
	} else {
		$waiting = get_posts(
			array(
				'post_type'   => 'property',
				'post_status' => 'pending',
				'author'      => $user_id,
				'numberposts' => (int) pzp_setting( 'max_pending' ) + 1,
				'fields'      => 'ids',
			)
		);
		if ( count( $waiting ) >= (int) pzp_setting( 'max_pending' ) ) {
			return new WP_Error( 'pzp_limit', __( 'You have many listings waiting for review. Please wait for our team to approve them before adding more.', 'propzed-publisher' ), array( 'status' => 429 ) );
		}
	}

	$scalar = static function ( $key ) use ( $data ) {
		return isset( $data[ $key ] ) && is_scalar( $data[ $key ] ) ? (string) $data[ $key ] : '';
	};

	$title    = wp_html_excerpt( sanitize_text_field( $scalar( 'title' ) ), 150 );
	$type     = absint( $scalar( 'type' ) );
	$location = absint( $scalar( 'location' ) );
	$status   = absint( $scalar( 'status' ) );

	if ( '' === $title || ! $type || ! $location || ! term_exists( $type, 'property_type' ) || ! term_exists( $location, 'property_location' ) ) {
		return new WP_Error( 'pzp_invalid', __( 'Please enter a title and choose the property type and location.', 'propzed-publisher' ), array( 'status' => 400 ) );
	}

	$postarr = array(
		'post_type'    => 'property',
		'post_status'  => 'pending',
		'post_author'  => $user_id,
		'post_title'   => $title,
		'post_content' => sanitize_textarea_field( $scalar( 'description' ) ),
	);
	if ( $existing ) {
		$postarr['ID'] = $existing->ID;
	}

	$saved_id = wp_insert_post( $postarr, true );
	if ( is_wp_error( $saved_id ) || ! $saved_id ) {
		return new WP_Error( 'pzp_save_failed', __( 'Could not save the property. Please try again.', 'propzed-publisher' ), array( 'status' => 500 ) );
	}

	// Taxonomies: only existing terms, one per taxonomy.
	wp_set_object_terms( $saved_id, array( $type ), 'property_type' );
	wp_set_object_terms( $saved_id, array( $location ), 'property_location' );
	wp_set_object_terms( $saved_id, ( $status && term_exists( $status, 'property_status' ) ) ? array( $status ) : array(), 'property_status' );

	// Property fields, sanitised by the theme's own rules.
	$fields = propzed_all_fields();
	$meta   = isset( $data['meta'] ) && is_array( $data['meta'] ) ? $data['meta'] : array();
	foreach ( pzp_publisher_fields() as $key ) {
		if ( ! isset( $fields[ $key ] ) ) {
			continue;
		}
		$value = propzed_sanitize_field( isset( $meta[ $key ] ) ? $meta[ $key ] : '', $fields[ $key ] );
		if ( '' === $value ) {
			delete_post_meta( $saved_id, '_pz_' . $key );
		} else {
			update_post_meta( $saved_id, '_pz_' . $key, $value );
		}
	}

	// Contact details come from the account, never from the form.
	update_post_meta( $saved_id, '_pz_seller_name', $user->display_name );
	update_post_meta( $saved_id, '_pz_seller_email', $user->user_email );
	update_post_meta( $saved_id, '_pz_seller_phone', (string) get_user_meta( $user_id, 'pzp_phone', true ) );
	update_post_meta( $saved_id, '_pz_seller_role', (string) get_user_meta( $user_id, 'pzp_type', true ) );
	update_post_meta( $saved_id, '_pzp_submitted', '1' );
	delete_post_meta( $saved_id, '_pzp_state' );

	$remove  = isset( $data['remove'] ) && is_array( $data['remove'] ) ? array_map( 'absint', $data['remove'] ) : array();
	$skipped = pzp_handle_photos( $saved_id, $user_id, $files, $remove );

	pzp_sync_builder( $saved_id );

	// Tell the office.
	wp_mail(
		propzed_notify_email(),
		sprintf( '[Propzed] %s: %s', $existing ? 'Listing updated, needs review' : 'New listing from a publisher', $title ),
		sprintf(
			"Publisher: %s (%s, %s)\nProperty: %s\n\nReview and publish: %s",
			$user->display_name,
			get_user_meta( $user_id, 'pzp_phone', true ),
			$user->user_email,
			$title,
			admin_url( 'post.php?action=edit&post=' . $saved_id )
		)
	);

	return array(
		'id'      => (int) $saved_id,
		'skipped' => (int) $skipped,
	);
}

/**
 * Add / remove photos. Up to 10 per property, JPG / PNG / WebP, 5 MB each.
 * The first photo is the Featured Image, the rest go to the gallery.
 *
 * @param int   $post_id Property ID.
 * @param int   $user_id Publisher.
 * @param array $files   $_FILES-style array (name[], tmp_name[], error[], size[]).
 * @param int[] $remove  Attachment IDs to remove.
 * @return int Number of files skipped (too large, wrong type or over the limit).
 */
function pzp_handle_photos( $post_id, $user_id, $files, $remove = array() ) {
	$current = propzed_gallery_ids( $post_id );

	// Remove: only photos that belong to this property.
	foreach ( array_intersect( $current, $remove ) as $attachment_id ) {
		$attachment = get_post( $attachment_id );
		if ( $attachment && (int) $attachment->post_parent === (int) $post_id && (int) $attachment->post_author === (int) $user_id ) {
			wp_delete_attachment( $attachment_id, true );
		}
	}
	$current = array_values( array_diff( $current, $remove ) );

	$skipped = 0;
	if ( ! empty( $files['name'] ) && is_array( $files['name'] ) ) {
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';

		$allowed  = array( 'image/jpeg', 'image/png', 'image/webp' );
		$max_size = (int) apply_filters( 'pzp_max_photo_bytes', 5 * MB_IN_BYTES );
		$max      = (int) apply_filters( 'pzp_max_photos', 10 );

		foreach ( array_keys( $files['name'] ) as $i ) {
			if ( '' === (string) $files['name'][ $i ] ) {
				continue;
			}
			$tmp = isset( $files['tmp_name'][ $i ] ) ? $files['tmp_name'][ $i ] : '';
			if ( count( $current ) >= $max || UPLOAD_ERR_OK !== (int) $files['error'][ $i ] || (int) $files['size'][ $i ] > $max_size || ! is_uploaded_file( $tmp ) ) {
				++$skipped;
				continue;
			}
			$check = wp_check_filetype_and_ext( $tmp, $files['name'][ $i ] );
			if ( empty( $check['type'] ) || ! in_array( $check['type'], $allowed, true ) ) {
				++$skipped;
				continue;
			}
			$attachment_id = media_handle_sideload(
				array(
					'name'     => sanitize_file_name( $files['name'][ $i ] ),
					'tmp_name' => $tmp,
				),
				$post_id,
				null,
				array( 'post_author' => $user_id )
			);
			if ( is_wp_error( $attachment_id ) ) {
				++$skipped;
				continue;
			}
			$current[] = (int) $attachment_id;
		}
	}

	if ( $current ) {
		set_post_thumbnail( $post_id, $current[0] );
		$gallery = array_slice( $current, 1 );
		if ( $gallery ) {
			update_post_meta( $post_id, '_pz_gallery', implode( ',', $gallery ) );
		} else {
			delete_post_meta( $post_id, '_pz_gallery' );
		}
	} else {
		delete_post_thumbnail( $post_id );
		delete_post_meta( $post_id, '_pz_gallery' );
	}
	return $skipped;
}

/**
 * Create a publisher account. Shared by the website form and the app API.
 *
 * @param array $data Unslashed: name, email, phone, password, company, type.
 * @return int|WP_Error User ID.
 */
function pzp_create_publisher( $data ) {
	if ( ! pzp_setting( 'signup' ) ) {
		return new WP_Error( 'pzp_closed', __( 'New publisher accounts are not open right now. Please call our office.', 'propzed-publisher' ), array( 'status' => 403 ) );
	}
	$get = static function ( $key ) use ( $data ) {
		return isset( $data[ $key ] ) && is_scalar( $data[ $key ] ) ? (string) $data[ $key ] : '';
	};

	$name     = sanitize_text_field( $get( 'name' ) );
	$email    = sanitize_email( $get( 'email' ) );
	$phone    = sanitize_text_field( $get( 'phone' ) );
	$password = $get( 'password' );
	$type     = sanitize_text_field( $get( 'type' ) );

	if ( '' === $name || ! is_email( $email ) || ! propzed_valid_phone( $phone ) ) {
		return new WP_Error( 'pzp_invalid', __( 'Please enter your name, a valid email and a 10-digit mobile number.', 'propzed-publisher' ), array( 'status' => 400 ) );
	}
	if ( strlen( $password ) < 8 ) {
		return new WP_Error( 'pzp_weak', __( 'Please choose a password with at least 8 characters.', 'propzed-publisher' ), array( 'status' => 400 ) );
	}
	if ( email_exists( $email ) ) {
		return new WP_Error( 'pzp_exists', __( 'An account with this email already exists. Please log in instead.', 'propzed-publisher' ), array( 'status' => 409 ) );
	}

	$user_id = wp_insert_user(
		array(
			'user_login'   => $email,
			'user_email'   => $email,
			'user_pass'    => $password,
			'display_name' => $name,
			'first_name'   => $name,
			'role'         => PZP_ROLE,
		)
	);
	if ( is_wp_error( $user_id ) ) {
		return new WP_Error( 'pzp_save_failed', __( 'Could not create the account. Please try again.', 'propzed-publisher' ), array( 'status' => 500 ) );
	}

	update_user_meta( $user_id, 'pzp_phone', $phone );
	update_user_meta( $user_id, 'pzp_company', sanitize_text_field( $get( 'company' ) ) );
	update_user_meta( $user_id, 'pzp_type', in_array( $type, pzp_publisher_types(), true ) ? $type : pzp_publisher_types()[0] );
	update_user_meta( $user_id, 'show_admin_bar_front', 'false' );

	wp_mail(
		propzed_notify_email(),
		'[Propzed] New publisher account: ' . $name,
		sprintf( "Name: %s\nPhone: %s\nEmail: %s\nCompany: %s\n\n%s", $name, $phone, $email, sanitize_text_field( $get( 'company' ) ), admin_url( 'user-edit.php?user_id=' . $user_id ) )
	);

	return (int) $user_id;
}

/* -------------------------------------------------------------------------
 * Form handlers (admin-post.php)
 * ---------------------------------------------------------------------- */

/**
 * Redirect back to the publisher area with a message code.
 *
 * @param string $msg  Message code (see pzp_notice()).
 * @param array  $args Extra query arguments.
 */
function pzp_redirect( $msg, $args = array() ) {
	wp_safe_redirect( pzp_publisher_url( array_merge( $args, array( 'pzp_msg' => $msg ) ) ) );
	exit;
}

/**
 * Stop a logged-in-only action for visitors and non-publishers.
 *
 * @param string $nonce_action Nonce action to verify.
 */
function pzp_require_publisher( $nonce_action ) {
	if ( ! is_user_logged_in() ) {
		pzp_redirect( 'login_required' );
	}
	if ( ! isset( $_POST['pzp_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['pzp_nonce'] ), $nonce_action ) || ! current_user_can( PZP_CAP ) ) {
		pzp_redirect( 'error' );
	}
}

add_action( 'admin_post_nopriv_pzp_register', 'pzp_handle_register' );
add_action( 'admin_post_pzp_register', 'pzp_handle_register' );
add_action( 'admin_post_nopriv_pzp_login', 'pzp_handle_login' );
add_action( 'admin_post_pzp_login', 'pzp_handle_login' );
add_action( 'admin_post_pzp_profile', 'pzp_handle_profile' );
add_action( 'admin_post_pzp_save_property', 'pzp_handle_save_property' );
add_action( 'admin_post_pzp_withdraw', 'pzp_handle_withdraw' );
foreach ( array( 'pzp_profile', 'pzp_save_property', 'pzp_withdraw' ) as $pzp_action ) {
	add_action(
		'admin_post_nopriv_' . $pzp_action,
		static function () {
			pzp_redirect( 'login_required' );
		}
	);
}

/**
 * Sign-up form.
 */
function pzp_handle_register() {
	if ( is_user_logged_in() ) {
		pzp_redirect( 'welcome' );
	}
	if ( ! empty( $_POST['pzp_website'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification -- honeypot.
		pzp_redirect( 'check_email' );
	}
	if ( ! isset( $_POST['pzp_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['pzp_nonce'] ), 'pzp_register' ) ) {
		pzp_redirect( 'expired', array( 'tab' => 'register' ) );
	}
	if ( pzp_rate_limited( 'register', 5, HOUR_IN_SECONDS ) ) {
		pzp_redirect( 'rate', array( 'tab' => 'register' ) );
	}
	if ( empty( $_POST['pzp_terms'] ) ) {
		pzp_redirect( 'pzp_invalid', array( 'tab' => 'register' ) );
	}

	$input   = wp_unslash( $_POST ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput -- sanitised in pzp_create_publisher().
	$user_id = pzp_create_publisher(
		array(
			'name'     => isset( $input['pzp_name'] ) ? $input['pzp_name'] : '',
			'email'    => isset( $input['pzp_email'] ) ? $input['pzp_email'] : '',
			'phone'    => isset( $input['pzp_phone'] ) ? $input['pzp_phone'] : '',
			'password' => isset( $input['pzp_password'] ) ? $input['pzp_password'] : '',
			'company'  => isset( $input['pzp_company'] ) ? $input['pzp_company'] : '',
			'type'     => isset( $input['pzp_type'] ) ? $input['pzp_type'] : '',
		)
	);
	if ( is_wp_error( $user_id ) ) {
		pzp_redirect( $user_id->get_error_code(), array( 'tab' => 'register' ) );
	}

	wp_set_current_user( $user_id );
	wp_set_auth_cookie( $user_id, true );
	pzp_redirect( 'welcome' );
}

/**
 * Login form (email + password).
 */
function pzp_handle_login() {
	if ( is_user_logged_in() ) {
		wp_safe_redirect( pzp_publisher_url() );
		exit;
	}
	if ( pzp_rate_limited( 'login', 10, 10 * MINUTE_IN_SECONDS ) ) {
		pzp_redirect( 'rate' );
	}
	$login    = isset( $_POST['pzp_login'] ) ? sanitize_text_field( wp_unslash( $_POST['pzp_login'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification -- login forms carry no nonce (same as wp-login.php).
	$password = isset( $_POST['pzp_password'] ) ? (string) wp_unslash( $_POST['pzp_password'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification, WordPress.Security.ValidatedSanitizedInput -- passwords are not altered.

	$user = wp_signon(
		array(
			'user_login'    => $login,
			'user_password' => $password,
			'remember'      => true,
		),
		is_ssl()
	);
	if ( is_wp_error( $user ) ) {
		pzp_redirect( 'login_failed' );
	}
	wp_safe_redirect( pzp_publisher_url() );
	exit;
}

/**
 * Profile form: name, phone, company.
 */
function pzp_handle_profile() {
	pzp_require_publisher( 'pzp_profile' );
	$user_id = get_current_user_id();
	$name    = isset( $_POST['pzp_name'] ) ? sanitize_text_field( wp_unslash( $_POST['pzp_name'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification -- verified in pzp_require_publisher().
	$phone   = isset( $_POST['pzp_phone'] ) ? sanitize_text_field( wp_unslash( $_POST['pzp_phone'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
	$company = isset( $_POST['pzp_company'] ) ? sanitize_text_field( wp_unslash( $_POST['pzp_company'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification

	if ( '' === $name || ! propzed_valid_phone( $phone ) ) {
		pzp_redirect( 'pzp_invalid', array( 'view' => 'profile' ) );
	}
	wp_update_user(
		array(
			'ID'           => $user_id,
			'display_name' => $name,
			'first_name'   => $name,
		)
	);
	update_user_meta( $user_id, 'pzp_phone', $phone );
	update_user_meta( $user_id, 'pzp_company', $company );
	pzp_redirect( 'profile' );
}

/**
 * Add / edit property form.
 */
function pzp_handle_save_property() {
	pzp_require_publisher( 'pzp_save_property' );

	$input   = wp_unslash( $_POST ); // phpcs:ignore WordPress.Security.NonceVerification, WordPress.Security.ValidatedSanitizedInput -- verified above, sanitised in pzp_save_submission().
	$post_id = isset( $input['pzp_id'] ) ? absint( $input['pzp_id'] ) : 0;
	$files   = isset( $_FILES['pzp_images'] ) && is_array( $_FILES['pzp_images'] ) ? $_FILES['pzp_images'] : array(); // phpcs:ignore WordPress.Security.NonceVerification, WordPress.Security.ValidatedSanitizedInput -- checked in pzp_handle_photos().

	$result = pzp_save_submission(
		get_current_user_id(),
		array(
			'title'       => isset( $input['pzp_title'] ) ? $input['pzp_title'] : '',
			'description' => isset( $input['pzp_description'] ) ? $input['pzp_description'] : '',
			'type'        => isset( $input['pzp_type'] ) ? $input['pzp_type'] : '',
			'location'    => isset( $input['pzp_location'] ) ? $input['pzp_location'] : '',
			'status'      => isset( $input['pzp_status'] ) ? $input['pzp_status'] : '',
			'meta'        => isset( $input['pzp_meta'] ) ? $input['pzp_meta'] : array(),
			'remove'      => isset( $input['pzp_remove'] ) ? $input['pzp_remove'] : array(),
		),
		$files,
		$post_id
	);

	if ( is_wp_error( $result ) ) {
		pzp_redirect( $result->get_error_code(), $post_id ? array( 'view' => 'edit', 'id' => $post_id ) : array( 'view' => 'new' ) );
	}
	pzp_redirect( $post_id ? 'updated' : 'saved', $result['skipped'] ? array( 'pzp_skipped' => $result['skipped'] ) : array() );
}

/**
 * Withdraw a listing (hidden from the site, kept in the account).
 */
function pzp_handle_withdraw() {
	pzp_require_publisher( 'pzp_withdraw' );
	$post = pzp_get_own_property( isset( $_POST['pzp_id'] ) ? absint( $_POST['pzp_id'] ) : 0, get_current_user_id() ); // phpcs:ignore WordPress.Security.NonceVerification -- verified above.
	if ( ! $post ) {
		pzp_redirect( 'error' );
	}
	wp_update_post(
		array(
			'ID'          => $post->ID,
			'post_status' => 'draft',
		)
	);
	update_post_meta( $post->ID, '_pzp_state', 'withdrawn' );
	pzp_redirect( 'withdrawn' );
}

/**
 * Message shown at the top of the publisher area after an action.
 */
function pzp_notice() {
	if ( empty( $_GET['pzp_msg'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		return;
	}
	$code     = sanitize_key( wp_unslash( $_GET['pzp_msg'] ) ); // phpcs:ignore WordPress.Security.NonceVerification
	$skipped  = isset( $_GET['pzp_skipped'] ) ? absint( $_GET['pzp_skipped'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification
	$messages = array(
		'welcome'         => array( 'success', __( 'Welcome! Your publisher account is ready. Add your first property below.', 'propzed-publisher' ) ),
		'saved'           => array( 'success', __( 'Thank you! Your property has been submitted. It will go live after our team verifies it.', 'propzed-publisher' ) ),
		'updated'         => array( 'success', __( 'Your changes have been submitted and will go live after our team reviews them.', 'propzed-publisher' ) ),
		'withdrawn'       => array( 'success', __( 'The listing has been withdrawn and is no longer shown on the site.', 'propzed-publisher' ) ),
		'profile'         => array( 'success', __( 'Your details have been updated.', 'propzed-publisher' ) ),
		'login_failed'    => array( 'error', __( 'The email or password is not correct. Please try again or reset your password.', 'propzed-publisher' ) ),
		'login_required'  => array( 'error', __( 'Please log in to continue.', 'propzed-publisher' ) ),
		'expired'         => array( 'error', __( 'The form expired. Please try again.', 'propzed-publisher' ) ),
		'rate'            => array( 'error', __( 'Too many attempts. Please wait a few minutes and try again.', 'propzed-publisher' ) ),
		'pzp_invalid'     => array( 'error', __( 'Please fill in all required fields with a valid email and 10-digit mobile number.', 'propzed-publisher' ) ),
		'pzp_weak'        => array( 'error', __( 'Please choose a password with at least 8 characters.', 'propzed-publisher' ) ),
		'pzp_exists'      => array( 'error', __( 'An account with this email already exists. Please log in instead.', 'propzed-publisher' ) ),
		'pzp_closed'      => array( 'error', __( 'New publisher accounts are not open right now. Please call our office.', 'propzed-publisher' ) ),
		'pzp_limit'       => array( 'error', __( 'You have many listings waiting for review. Please wait for our team to approve them before adding more.', 'propzed-publisher' ) ),
		'pzp_not_found'   => array( 'error', __( 'That property could not be found in your account.', 'propzed-publisher' ) ),
		'pzp_forbidden'   => array( 'error', __( 'This account cannot submit properties.', 'propzed-publisher' ) ),
		'pzp_save_failed' => array( 'error', __( 'Something went wrong while saving. Please try again.', 'propzed-publisher' ) ),
		'error'           => array( 'error', __( 'Something went wrong. Please try again.', 'propzed-publisher' ) ),
	);
	if ( ! isset( $messages[ $code ] ) ) {
		return;
	}
	$text = $messages[ $code ][1];
	if ( $skipped ) {
		/* translators: %d: number of photos */
		$text .= ' ' . sprintf( _n( '%d photo was skipped (over 5 MB, wrong format or above the 10 photo limit).', '%d photos were skipped (over 5 MB, wrong format or above the 10 photo limit).', $skipped, 'propzed-publisher' ), $skipped );
	}
	printf(
		'<div class="pz-notice pz-notice--%1$s pzp-notice" role="%2$s" tabindex="-1">%3$s</div>',
		esc_attr( $messages[ $code ][0] ),
		'error' === $messages[ $code ][0] ? 'alert' : 'status',
		esc_html( $text )
	);
}
