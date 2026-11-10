<?php
/**
 * App API additions (same namespace as the theme: /wp-json/propzed/v1/).
 *
 * PUBLIC
 *   GET  builders                    All builders with a live project
 *   GET  builders/{slug}             One builder + its projects (?page=1&per_page=12)
 *
 * PUBLISHER (mobile app)
 *   POST publisher/register          name, email, phone, password, company, type
 *   POST publisher/login             username (email), password
 *                                    -> { username, token }. Send both on later requests as
 *                                       HTTP Basic auth:  Authorization: Basic base64(username:token)
 *   GET    publisher/me              Account details
 *   GET    publisher/properties      The publisher's own listings with their review state
 *   POST   publisher/properties      Create (multipart/form-data, photos in images[])
 *   POST   publisher/properties/{id} Update (always goes back to review)
 *   DELETE publisher/properties/{id} Withdraw
 *
 * Property fields for create / update: title, description, type, location, status
 * (term IDs from GET /terms), and any of pzp_publisher_fields() such as address,
 * pincode, area, area_unit, price, price_unit, developer, possession_date,
 * approvals, features, video. remove = comma-separated photo IDs.
 *
 * The token is a standard WordPress Application Password, so it can be revoked
 * per device under Users > Profile.
 *
 * @package PropzedPublisher
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register routes.
 */
function pzp_rest_init() {
	$ns     = 'propzed/v1';
	$public = '__return_true';
	$logged = static function () {
		return current_user_can( PZP_CAP ) ? true : new WP_Error( 'pzp_auth', __( 'Please log in with a publisher account.', 'propzed-publisher' ), array( 'status' => 401 ) );
	};

	register_rest_route( $ns, '/builders', array( 'methods' => WP_REST_Server::READABLE, 'callback' => 'pzp_rest_builders', 'permission_callback' => $public ) );
	register_rest_route(
		$ns,
		'/builders/(?P<slug>[a-z0-9-]+)',
		array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => 'pzp_rest_builder',
			'permission_callback' => $public,
			'args'                => array(
				'page'     => array( 'type' => 'integer', 'default' => 1, 'minimum' => 1 ),
				'per_page' => array( 'type' => 'integer', 'default' => 12, 'minimum' => 1, 'maximum' => 50 ),
			),
		)
	);

	register_rest_route( $ns, '/publisher/register', array( 'methods' => WP_REST_Server::CREATABLE, 'callback' => 'pzp_rest_register', 'permission_callback' => $public ) );
	register_rest_route( $ns, '/publisher/login', array( 'methods' => WP_REST_Server::CREATABLE, 'callback' => 'pzp_rest_login', 'permission_callback' => $public ) );
	register_rest_route( $ns, '/publisher/me', array( 'methods' => WP_REST_Server::READABLE, 'callback' => 'pzp_rest_me', 'permission_callback' => $logged ) );
	register_rest_route(
		$ns,
		'/publisher/properties',
		array(
			array( 'methods' => WP_REST_Server::READABLE, 'callback' => 'pzp_rest_my_properties', 'permission_callback' => $logged ),
			array( 'methods' => WP_REST_Server::CREATABLE, 'callback' => 'pzp_rest_save_property', 'permission_callback' => $logged ),
		)
	);
	register_rest_route(
		$ns,
		'/publisher/properties/(?P<id>\d+)',
		array(
			array( 'methods' => WP_REST_Server::CREATABLE, 'callback' => 'pzp_rest_save_property', 'permission_callback' => $logged ),
			array( 'methods' => WP_REST_Server::DELETABLE, 'callback' => 'pzp_rest_withdraw', 'permission_callback' => $logged ),
		)
	);
}
add_action( 'rest_api_init', 'pzp_rest_init' );

/**
 * Publisher responses are personal: never cache them.
 */
add_filter(
	'rest_post_dispatch',
	static function ( $response, $server, $request ) {
		if ( 0 === strpos( $request->get_route(), '/propzed/v1/publisher' ) && $response instanceof WP_HTTP_Response ) {
			$response->header( 'Cache-Control', 'no-store, private' );
			do_action( 'litespeed_control_set_nocache', 'propzed publisher api' );
		}
		return $response;
	},
	10,
	3
);

/**
 * GET /builders
 *
 * @return WP_REST_Response
 */
function pzp_rest_builders() {
	return rest_ensure_response( array( 'items' => pzp_get_builders() ) );
}

/**
 * GET /builders/{slug}
 *
 * @param WP_REST_Request $request Request.
 * @return WP_REST_Response|WP_Error
 */
function pzp_rest_builder( $request ) {
	$term = get_term_by( 'slug', $request['slug'], PZP_BUILDER_TAX );
	if ( ! $term ) {
		return new WP_Error( 'pzp_not_found', __( 'Builder not found.', 'propzed-publisher' ), array( 'status' => 404 ) );
	}
	$query = new WP_Query(
		array(
			'post_type'      => 'property',
			'post_status'    => 'publish',
			'posts_per_page' => (int) $request['per_page'],
			'paged'          => (int) $request['page'],
			'tax_query'      => array( // phpcs:ignore WordPress.DB.SlowDBQuery
				array(
					'taxonomy' => PZP_BUILDER_TAX,
					'terms'    => $term->term_id,
				),
			),
		)
	);
	$data               = pzp_builder_data( $term );
	$data['properties'] = function_exists( 'propzed_rest_format' ) ? array_map( 'propzed_rest_format', $query->posts ) : array();
	$data['total']      = (int) $query->found_posts;
	$data['pages']      = (int) $query->max_num_pages;
	$data['page']       = (int) $request['page'];
	return rest_ensure_response( $data );
}

/**
 * Account details for the app.
 *
 * @param WP_User $user User.
 * @return array
 */
function pzp_rest_user( $user ) {
	return array(
		'id'      => (int) $user->ID,
		'name'    => $user->display_name,
		'email'   => $user->user_email,
		'phone'   => (string) get_user_meta( $user->ID, 'pzp_phone', true ),
		'company' => (string) get_user_meta( $user->ID, 'pzp_company', true ),
		'type'    => (string) get_user_meta( $user->ID, 'pzp_type', true ),
	);
}

/**
 * Create an app token (WordPress Application Password) for a user.
 *
 * @param WP_User $user User.
 * @return array|WP_Error [ username, token, user ].
 */
function pzp_rest_issue_token( $user ) {
	if ( ! class_exists( 'WP_Application_Passwords' ) || ! wp_is_application_passwords_available_for_user( $user ) ) {
		return new WP_Error( 'pzp_tokens_off', __( 'App login is not available on this site. Please use the website.', 'propzed-publisher' ), array( 'status' => 501 ) );
	}
	$created = WP_Application_Passwords::create_new_application_password(
		$user->ID,
		array( 'name' => 'Propzed app ' . gmdate( 'Y-m-d H:i' ) )
	);
	if ( is_wp_error( $created ) ) {
		return $created;
	}
	return array(
		'username' => $user->user_login,
		'token'    => $created[0],
		'user'     => pzp_rest_user( $user ),
	);
}

/**
 * POST /publisher/register
 *
 * @param WP_REST_Request $request Request.
 * @return WP_REST_Response|WP_Error
 */
function pzp_rest_register( $request ) {
	if ( pzp_rate_limited( 'register', 5, HOUR_IN_SECONDS ) ) {
		return new WP_Error( 'pzp_rate', __( 'Too many attempts. Please try again later.', 'propzed-publisher' ), array( 'status' => 429 ) );
	}
	$user_id = pzp_create_publisher(
		array(
			'name'     => $request->get_param( 'name' ),
			'email'    => $request->get_param( 'email' ),
			'phone'    => $request->get_param( 'phone' ),
			'password' => $request->get_param( 'password' ),
			'company'  => $request->get_param( 'company' ),
			'type'     => $request->get_param( 'type' ),
		)
	);
	if ( is_wp_error( $user_id ) ) {
		return $user_id;
	}
	$token = pzp_rest_issue_token( get_userdata( $user_id ) );
	return is_wp_error( $token ) ? $token : rest_ensure_response( $token );
}

/**
 * POST /publisher/login
 *
 * @param WP_REST_Request $request Request.
 * @return WP_REST_Response|WP_Error
 */
function pzp_rest_login( $request ) {
	if ( pzp_rate_limited( 'login', 10, 10 * MINUTE_IN_SECONDS ) ) {
		return new WP_Error( 'pzp_rate', __( 'Too many attempts. Please try again in a few minutes.', 'propzed-publisher' ), array( 'status' => 429 ) );
	}
	$user = wp_authenticate( sanitize_text_field( (string) $request->get_param( 'username' ) ), (string) $request->get_param( 'password' ) );
	// Tokens are only issued to publisher-only accounts, never to staff logins.
	if ( is_wp_error( $user ) || ! pzp_is_publisher_only( $user ) ) {
		return new WP_Error( 'pzp_login_failed', __( 'The email or password is not correct.', 'propzed-publisher' ), array( 'status' => 401 ) );
	}
	$token = pzp_rest_issue_token( $user );
	return is_wp_error( $token ) ? $token : rest_ensure_response( $token );
}

/**
 * GET /publisher/me
 *
 * @return WP_REST_Response
 */
function pzp_rest_me() {
	return rest_ensure_response( pzp_rest_user( wp_get_current_user() ) );
}

/**
 * One of the publisher's own listings for the app.
 *
 * @param WP_Post $post Property.
 * @return array
 */
function pzp_rest_own_property( $post ) {
	$state = pzp_listing_state( $post );
	$data  = function_exists( 'propzed_rest_format' ) ? propzed_rest_format( $post ) : array( 'id' => $post->ID, 'title' => $post->post_title );
	return array_merge(
		$data,
		array(
			'state'       => $state[0],
			'state_label' => $state[1],
			'review_note' => 'changes' === $state[0] ? (string) get_post_meta( $post->ID, '_pzp_review_note', true ) : '',
			'enquiries'   => pzp_enquiry_count( $post->ID ),
			'link'        => 'publish' === $post->post_status ? get_permalink( $post ) : '',
		)
	);
}

/**
 * GET /publisher/properties
 *
 * @return WP_REST_Response
 */
function pzp_rest_my_properties() {
	return rest_ensure_response( array( 'items' => array_map( 'pzp_rest_own_property', pzp_get_user_properties( get_current_user_id() ) ) ) );
}

/**
 * POST /publisher/properties and /publisher/properties/{id}
 *
 * @param WP_REST_Request $request Request.
 * @return WP_REST_Response|WP_Error
 */
function pzp_rest_save_property( $request ) {
	$meta = array();
	foreach ( pzp_publisher_fields() as $key ) {
		if ( null !== $request->get_param( $key ) ) {
			$meta[ $key ] = $request->get_param( $key );
		}
	}
	$files  = $request->get_file_params();
	$remove = $request->get_param( 'remove' );

	$result = pzp_save_submission(
		get_current_user_id(),
		array(
			'title'       => $request->get_param( 'title' ),
			'description' => $request->get_param( 'description' ),
			'type'        => $request->get_param( 'type' ),
			'location'    => $request->get_param( 'location' ),
			'status'      => $request->get_param( 'status' ),
			'meta'        => $meta,
			'remove'      => is_array( $remove ) ? $remove : array_filter( explode( ',', (string) $remove ) ),
		),
		isset( $files['images'] ) && is_array( $files['images'] ) ? $files['images'] : array(),
		isset( $request['id'] ) ? (int) $request['id'] : 0
	);
	if ( is_wp_error( $result ) ) {
		return $result;
	}
	$data                   = pzp_rest_own_property( get_post( $result['id'] ) );
	$data['photos_skipped'] = $result['skipped'];
	$data['message']        = __( 'Submitted. Your listing will go live after our team verifies it.', 'propzed-publisher' );
	return rest_ensure_response( $data );
}

/**
 * DELETE /publisher/properties/{id}
 *
 * @param WP_REST_Request $request Request.
 * @return WP_REST_Response|WP_Error
 */
function pzp_rest_withdraw( $request ) {
	$post = pzp_get_own_property( (int) $request['id'], get_current_user_id() );
	if ( ! $post ) {
		return new WP_Error( 'pzp_not_found', __( 'Property not found.', 'propzed-publisher' ), array( 'status' => 404 ) );
	}
	wp_update_post(
		array(
			'ID'          => $post->ID,
			'post_status' => 'draft',
		)
	);
	update_post_meta( $post->ID, '_pzp_state', 'withdrawn' );
	return rest_ensure_response( array( 'success' => true, 'id' => $post->ID, 'state' => 'withdrawn' ) );
}
