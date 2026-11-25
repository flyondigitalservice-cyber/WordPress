<?php
/**
 * SOLD / LEASED OUT
 *
 * A listing is sold when it has the "Sold" (or "Leased Out") term in the theme's
 * "Listing Status" taxonomy, so the theme's own badges, filters and term pages
 * show it with no template changes. This file adds:
 *
 *   wp-admin   "Mark as Sold / Leased Out / Available" row actions, bulk actions,
 *              a "Sale" column, and a "Listing outcome" box on the edit screen
 *              (date, private closing price and note).
 *   Front end  Admin-bar button on the property page, SOLD ribbon on cards,
 *              a "sold" banner with similar listings, sold listings sorted last
 *              (or hidden, see settings), Schema.org availability = SoldOut.
 *
 * The term is always shown first, so cards (which show one status) say "Sold".
 *
 * @package PropzedProjects
 */

defined( 'ABSPATH' ) || exit;

/**
 * Sale states: slug => label.
 *
 * @return array
 */
function pzs_sale_states() {
	return array(
		'sold'       => __( 'Sold', 'propzed-projects' ),
		'leased-out' => __( 'Leased Out', 'propzed-projects' ),
	);
}

/**
 * Current state: 'sold', 'leased-out' or '' (available).
 *
 * @param int|null $post_id Post ID.
 * @return string
 */
function pzs_sale_state( $post_id = null ) {
	$post_id = $post_id ? (int) $post_id : get_the_ID();
	$slugs   = wp_get_object_terms( $post_id, 'property_status', array( 'fields' => 'slugs' ) );
	if ( is_wp_error( $slugs ) ) {
		return '';
	}
	foreach ( array_keys( pzs_sale_states() ) as $state ) {
		if ( in_array( $state, $slugs, true ) ) {
			return $state;
		}
	}
	return '';
}

/**
 * Set the state. Other statuses (For Sale, New Launch...) are kept so the listing
 * stays on its SEO landing pages as proof of past deals.
 *
 * @param int    $post_id Post ID.
 * @param string $state   'sold', 'leased-out' or '' for available again.
 * @param string $date    Y-m-d (defaults to today when marking sold).
 */
function pzs_set_sale_state( $post_id, $state, $date = '' ) {
	$post_id = (int) $post_id;
	$states  = array_keys( pzs_sale_states() );
	$state   = in_array( $state, $states, true ) ? $state : '';

	wp_remove_object_terms( $post_id, $states, 'property_status' );

	if ( $state ) {
		wp_set_object_terms( $post_id, $state, 'property_status', true );
		update_post_meta( $post_id, '_pzs_sold_rank', 1 );
		update_post_meta( $post_id, '_pzs_sold_date', $date ? $date : current_time( 'Y-m-d' ) );
		update_post_meta( $post_id, '_pzs_sold_by', get_current_user_id() );
	} else {
		delete_post_meta( $post_id, '_pzs_sold_rank' );
		delete_post_meta( $post_id, '_pzs_sold_date' );
	}
	clean_post_cache( $post_id );
	do_action( 'pzs_sale_state_changed', $post_id, $state );
}

/**
 * Put Sold / Leased Out first among the statuses, so the card badge shows it.
 *
 * @param WP_Term[]|false|WP_Error $terms    Terms.
 * @param int                      $post_id  Post ID.
 * @param string                   $taxonomy Taxonomy.
 * @return mixed
 */
function pzs_sold_terms_first( $terms, $post_id, $taxonomy ) {
	if ( 'property_status' !== $taxonomy || ! is_array( $terms ) || count( $terms ) < 2 ) {
		return $terms;
	}
	usort(
		$terms,
		static function ( $a, $b ) {
			$sa = array_key_exists( $a->slug, pzs_sale_states() ) ? 0 : 1;
			$sb = array_key_exists( $b->slug, pzs_sale_states() ) ? 0 : 1;
			return $sa - $sb;
		}
	);
	return $terms;
}
add_filter( 'get_the_terms', 'pzs_sold_terms_first', 10, 3 );

/* -------------------------------------------------------------------------
 * wp-admin: actions
 * ---------------------------------------------------------------------- */

/**
 * Nonce'd URL that changes the state of one listing.
 *
 * @param int    $post_id Post ID.
 * @param string $state   State or 'available'.
 * @return string
 */
function pzs_mark_url( $post_id, $state ) {
	return wp_nonce_url(
		admin_url( 'admin-post.php?action=pzs_mark&post=' . (int) $post_id . '&state=' . rawurlencode( $state ) ),
		'pzs_mark_' . (int) $post_id
	);
}

add_action(
	'admin_post_pzs_mark',
	static function () {
		$post_id = isset( $_GET['post'] ) ? absint( $_GET['post'] ) : 0;
		$state   = isset( $_GET['state'] ) ? sanitize_key( wp_unslash( $_GET['state'] ) ) : '';
		check_admin_referer( 'pzs_mark_' . $post_id );
		if ( ! $post_id || 'property' !== get_post_type( $post_id ) || ! current_user_can( 'edit_post', $post_id ) ) {
			wp_die( esc_html__( 'You are not allowed to change this listing.', 'propzed-projects' ) );
		}
		pzs_set_sale_state( $post_id, 'available' === $state ? '' : $state );

		$back = wp_get_referer() ? wp_get_referer() : admin_url( 'edit.php?post_type=property' );
		if ( 0 === strpos( $back, admin_url() ) ) {
			$back = add_query_arg( 'pzs_marked', 'available' === $state ? 'available' : $state, remove_query_arg( 'pzs_marked', $back ) );
		}
		wp_safe_redirect( $back );
		exit;
	}
);

/**
 * Row actions on the Properties list.
 */
add_filter(
	'post_row_actions',
	static function ( $actions, $post ) {
		if ( 'property' !== $post->post_type || ! current_user_can( 'edit_post', $post->ID ) ) {
			return $actions;
		}
		$state = pzs_sale_state( $post->ID );
		if ( $state ) {
			$actions['pzs_available'] = '<a href="' . esc_url( pzs_mark_url( $post->ID, 'available' ) ) . '">' . esc_html__( 'Mark Available', 'propzed-projects' ) . '</a>';
		} else {
			$actions['pzs_sold']   = '<a href="' . esc_url( pzs_mark_url( $post->ID, 'sold' ) ) . '" style="color:#b32d2e;font-weight:600">' . esc_html__( 'Mark as Sold', 'propzed-projects' ) . '</a>';
			$actions['pzs_leased'] = '<a href="' . esc_url( pzs_mark_url( $post->ID, 'leased-out' ) ) . '">' . esc_html__( 'Leased Out', 'propzed-projects' ) . '</a>';
		}
		return $actions;
	},
	10,
	2
);

/**
 * "Sale" column with a one-click button.
 */
add_filter(
	'manage_property_posts_columns',
	static function ( $columns ) {
		$new = array();
		foreach ( $columns as $key => $label ) {
			$new[ $key ] = $label;
			if ( 'title' === $key ) {
				$new['pzs_sale'] = __( 'Sale', 'propzed-projects' );
			}
		}
		if ( ! isset( $new['pzs_sale'] ) ) {
			$new['pzs_sale'] = __( 'Sale', 'propzed-projects' );
		}
		return $new;
	},
	20
);

add_action(
	'manage_property_posts_custom_column',
	static function ( $column, $post_id ) {
		if ( 'pzs_sale' !== $column ) {
			return;
		}
		$state  = pzs_sale_state( $post_id );
		$states = pzs_sale_states();
		if ( $state ) {
			$date = get_post_meta( $post_id, '_pzs_sold_date', true );
			printf(
				'<span class="pzs-pill pzs-pill--%1$s">%2$s</span>%3$s<br><a class="pzs-undo" href="%4$s">%5$s</a>',
				esc_attr( $state ),
				esc_html( $states[ $state ] ),
				$date ? ' <small>' . esc_html( mysql2date( get_option( 'date_format' ), $date ) ) . '</small>' : '',
				esc_url( pzs_mark_url( $post_id, 'available' ) ),
				esc_html__( 'Undo', 'propzed-projects' )
			);
		} else {
			printf(
				'<a class="button button-small pzs-mark" href="%1$s">%2$s</a>',
				esc_url( pzs_mark_url( $post_id, 'sold' ) ),
				esc_html__( 'Mark Sold', 'propzed-projects' )
			);
		}
	},
	10,
	2
);

/**
 * Bulk actions.
 */
add_filter(
	'bulk_actions-edit-property',
	static function ( $actions ) {
		$actions['pzs_bulk_sold']      = __( 'Mark as Sold', 'propzed-projects' );
		$actions['pzs_bulk_leased']    = __( 'Mark as Leased Out', 'propzed-projects' );
		$actions['pzs_bulk_available'] = __( 'Mark as Available', 'propzed-projects' );
		return $actions;
	}
);

add_filter(
	'handle_bulk_actions-edit-property',
	static function ( $redirect, $action, $ids ) {
		$map = array(
			'pzs_bulk_sold'      => 'sold',
			'pzs_bulk_leased'    => 'leased-out',
			'pzs_bulk_available' => '',
		);
		if ( ! array_key_exists( $action, $map ) ) {
			return $redirect;
		}
		$done = 0;
		foreach ( $ids as $id ) {
			if ( current_user_can( 'edit_post', $id ) ) {
				pzs_set_sale_state( $id, $map[ $action ] );
				++$done;
			}
		}
		return add_query_arg(
			array(
				'pzs_marked' => $map[ $action ] ? $map[ $action ] : 'available',
				'pzs_count'  => $done,
			),
			$redirect
		);
	},
	10,
	3
);

add_action(
	'admin_notices',
	static function () {
		if ( empty( $_GET['pzs_marked'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			return;
		}
		$state = sanitize_key( wp_unslash( $_GET['pzs_marked'] ) ); // phpcs:ignore WordPress.Security.NonceVerification
		$count = isset( $_GET['pzs_count'] ) ? absint( $_GET['pzs_count'] ) : 1; // phpcs:ignore WordPress.Security.NonceVerification
		$label = 'available' === $state ? __( 'Available', 'propzed-projects' ) : ( 'leased-out' === $state ? __( 'Leased Out', 'propzed-projects' ) : __( 'Sold', 'propzed-projects' ) );
		/* translators: 1: number of listings 2: state */
		$msg = sprintf( _n( '%1$d listing marked as %2$s.', '%1$d listings marked as %2$s.', $count, 'propzed-projects' ), $count, $label );
		echo '<div class="notice notice-success is-dismissible"><p>' . esc_html( $msg ) . '</p></div>';
	}
);

/**
 * "Listing outcome" box on the edit screen.
 */
add_action(
	'add_meta_boxes_property',
	static function () {
		add_meta_box( 'pzs_outcome', __( 'Listing outcome (Sold / Leased)', 'propzed-projects' ), 'pzs_render_outcome_box', 'property', 'side', 'high' );
	}
);

/**
 * Render the outcome box.
 *
 * @param WP_Post $post Post.
 */
function pzs_render_outcome_box( $post ) {
	$state = pzs_sale_state( $post->ID );
	$date  = get_post_meta( $post->ID, '_pzs_sold_date', true );
	wp_nonce_field( 'pzs_outcome', 'pzs_outcome_nonce' );
	?>
	<div class="pzs-outcome">
		<p class="pzs-outcome__now">
			<?php if ( $state ) : ?>
				<span class="pzs-pill pzs-pill--<?php echo esc_attr( $state ); ?>"><?php echo esc_html( pzs_sale_states()[ $state ] ); ?></span>
				<?php echo $date ? esc_html( mysql2date( get_option( 'date_format' ), $date ) ) : ''; ?>
			<?php else : ?>
				<span class="pzs-pill pzs-pill--available"><?php esc_html_e( 'Available', 'propzed-projects' ); ?></span>
			<?php endif; ?>
		</p>
		<?php if ( 'publish' === $post->post_status ) : ?>
			<p class="pzs-outcome__buttons">
				<?php if ( $state ) : ?>
					<a class="button" href="<?php echo esc_url( pzs_mark_url( $post->ID, 'available' ) ); ?>"><?php esc_html_e( 'Mark Available again', 'propzed-projects' ); ?></a>
				<?php else : ?>
					<a class="button button-primary pzs-btn-sold" href="<?php echo esc_url( pzs_mark_url( $post->ID, 'sold' ) ); ?>"><?php esc_html_e( 'Mark as Sold', 'propzed-projects' ); ?></a>
					<a class="button" href="<?php echo esc_url( pzs_mark_url( $post->ID, 'leased-out' ) ); ?>"><?php esc_html_e( 'Leased Out', 'propzed-projects' ); ?></a>
				<?php endif; ?>
			</p>
			<p class="description"><?php esc_html_e( 'One click, takes effect immediately. Save any other edits first.', 'propzed-projects' ); ?></p>
		<?php endif; ?>
		<hr>
		<p><label for="pzs-sold-date"><?php esc_html_e( 'Deal date', 'propzed-projects' ); ?></label><br>
			<input type="date" id="pzs-sold-date" name="pzs_sold_date" value="<?php echo esc_attr( $date ); ?>" style="width:100%"></p>
		<p><label for="pzs-sold-price"><?php esc_html_e( 'Closing price ₹ (private)', 'propzed-projects' ); ?></label><br>
			<input type="number" id="pzs-sold-price" name="pzs_sold_price" min="0" step="1" value="<?php echo esc_attr( get_post_meta( $post->ID, '_pzs_sold_price', true ) ); ?>" style="width:100%"></p>
		<p><label for="pzs-sold-note"><?php esc_html_e( 'Buyer / deal note (private)', 'propzed-projects' ); ?></label><br>
			<textarea id="pzs-sold-note" name="pzs_sold_note" rows="2" style="width:100%"><?php echo esc_textarea( get_post_meta( $post->ID, '_pzs_sold_note', true ) ); ?></textarea></p>
		<p class="description"><?php esc_html_e( 'Price and note are never shown on the website.', 'propzed-projects' ); ?></p>
	</div>
	<?php
}

add_action(
	'save_post_property',
	static function ( $post_id ) {
		if ( ! isset( $_POST['pzs_outcome_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['pzs_outcome_nonce'] ), 'pzs_outcome' ) ) {
			return;
		}
		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		$date = isset( $_POST['pzs_sold_date'] ) ? sanitize_text_field( wp_unslash( $_POST['pzs_sold_date'] ) ) : '';
		if ( $date && preg_match( '/^\d{4}-\d{2}-\d{2}$/', $date ) && pzs_sale_state( $post_id ) ) {
			update_post_meta( $post_id, '_pzs_sold_date', $date );
		}
		$price = isset( $_POST['pzs_sold_price'] ) ? preg_replace( '/[^\d]/', '', wp_unslash( $_POST['pzs_sold_price'] ) ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		'' === $price ? delete_post_meta( $post_id, '_pzs_sold_price' ) : update_post_meta( $post_id, '_pzs_sold_price', $price );
		$note = isset( $_POST['pzs_sold_note'] ) ? sanitize_textarea_field( wp_unslash( $_POST['pzs_sold_note'] ) ) : '';
		'' === $note ? delete_post_meta( $post_id, '_pzs_sold_note' ) : update_post_meta( $post_id, '_pzs_sold_note', $note );
	}
);

/**
 * Keep _pzs_sold_rank in step when someone ticks the Sold term by hand
 * in the Listing Status box or Quick Edit.
 */
add_action(
	'set_object_terms',
	static function ( $object_id, $terms, $tt_ids, $taxonomy ) {
		if ( 'property_status' !== $taxonomy || 'property' !== get_post_type( $object_id ) ) {
			return;
		}
		if ( pzs_sale_state( $object_id ) ) {
			if ( ! get_post_meta( $object_id, '_pzs_sold_rank', true ) ) {
				update_post_meta( $object_id, '_pzs_sold_rank', 1 );
				update_post_meta( $object_id, '_pzs_sold_date', current_time( 'Y-m-d' ) );
			}
		} else {
			delete_post_meta( $object_id, '_pzs_sold_rank' );
		}
	},
	10,
	4
);

/**
 * Admin bar button on the property page (front end) and the edit screen.
 *
 * @param WP_Admin_Bar $bar Admin bar.
 */
function pzs_admin_bar( $bar ) {
	$post_id = 0;
	if ( is_singular( 'property' ) ) {
		$post_id = get_queried_object_id();
	} elseif ( is_admin() && function_exists( 'get_current_screen' ) && get_current_screen() && 'property' === get_current_screen()->post_type && isset( $_GET['post'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		$post_id = absint( $_GET['post'] ); // phpcs:ignore WordPress.Security.NonceVerification
	}
	if ( ! $post_id || ! current_user_can( 'edit_post', $post_id ) || 'publish' !== get_post_status( $post_id ) ) {
		return;
	}
	$state = pzs_sale_state( $post_id );
	$bar->add_node(
		array(
			'id'    => 'pzs-sale',
			'title' => $state ? '✓ ' . pzs_sale_states()[ $state ] . ' — ' . __( 'mark available', 'propzed-projects' ) : '● ' . __( 'Mark as Sold', 'propzed-projects' ),
			'href'  => pzs_mark_url( $post_id, $state ? 'available' : 'sold' ),
			'meta'  => array( 'class' => $state ? 'pzs-ab-sold' : 'pzs-ab-available' ),
		)
	);
	if ( ! $state ) {
		$bar->add_node(
			array(
				'parent' => 'pzs-sale',
				'id'     => 'pzs-sale-leased',
				'title'  => __( 'Mark as Leased Out', 'propzed-projects' ),
				'href'   => pzs_mark_url( $post_id, 'leased-out' ),
			)
		);
	}
}
add_action( 'admin_bar_menu', 'pzs_admin_bar', 90 );

/* -------------------------------------------------------------------------
 * Front end
 * ---------------------------------------------------------------------- */

/**
 * Listing pages: sold last, or hidden. Uses a LEFT JOIN so it works with every
 * theme sort (price, area, newest) and with the budget filter.
 *
 * @param array    $clauses SQL clauses.
 * @param WP_Query $query   Query.
 * @return array
 */
function pzs_sold_order_clauses( $clauses, $query ) {
	if ( is_admin() || ! $query->is_main_query() || $query->is_singular() ) {
		return $clauses;
	}
	if ( ! $query->is_post_type_archive( 'property' ) && ! $query->is_tax( array( 'property_type', 'property_location', 'property_status' ) ) ) {
		return $clauses;
	}
	// A visitor who opens /status/sold/ wants to see sold listings.
	if ( $query->is_tax( 'property_status', array_keys( pzs_sale_states() ) ) ) {
		return $clauses;
	}
	$mode = pzs_setting( 'sold_mode' );
	if ( 'normal' === $mode ) {
		return $clauses;
	}
	global $wpdb;
	$clauses['join'] .= " LEFT JOIN {$wpdb->postmeta} AS pzs_sold ON ( pzs_sold.post_id = {$wpdb->posts}.ID AND pzs_sold.meta_key = '_pzs_sold_rank' )";
	if ( 'hide' === $mode ) {
		$clauses['where'] .= ' AND pzs_sold.meta_id IS NULL';
	} else {
		$order              = trim( $clauses['orderby'] );
		$clauses['orderby'] = '( pzs_sold.meta_id IS NOT NULL ) ASC' . ( $order ? ', ' . $order : '' );
	}
	if ( empty( $clauses['groupby'] ) ) {
		$clauses['groupby'] = "{$wpdb->posts}.ID";
	}
	return $clauses;
}
add_filter( 'posts_clauses', 'pzs_sold_order_clauses', 20, 2 );

/**
 * "(Sold)" in the browser / Google title of a sold property.
 *
 * @param array $parts Title parts.
 * @return array
 */
function pzs_sold_title( $parts ) {
	if ( is_singular( 'property' ) && isset( $parts['title'] ) ) {
		$state = pzs_sale_state( get_queried_object_id() );
		if ( $state ) {
			$parts['title'] .= ' (' . pzs_sale_states()[ $state ] . ')';
		}
	}
	return $parts;
}
add_filter( 'document_title_parts', 'pzs_sold_title', 30 );

add_filter(
	'body_class',
	static function ( $classes ) {
		if ( is_singular( 'property' ) && pzs_sale_state( get_queried_object_id() ) ) {
			$classes[] = 'pzs-is-sold';
		}
		return $classes;
	}
);

/**
 * Banner on a sold property: keeps visitors from Google moving.
 *
 * @param string $content Content.
 * @return string
 */
function pzs_sold_banner( $content ) {
	if ( ! is_singular( 'property' ) || ! in_the_loop() || ! is_main_query() || ! pzs_setting( 'sold_banner' ) ) {
		return $content;
	}
	$state = pzs_sale_state();
	if ( ! $state ) {
		return $content;
	}
	$type     = propzed_first_term( 'property_type' );
	$similar  = $type ? get_term_link( $type ) : propzed_listing_url();
	$req      = function_exists( 'propzed_template_url' ) ? propzed_template_url( 'templates/template-requirement.php' ) : home_url( '/post-requirement/' );
	$date     = get_post_meta( get_the_ID(), '_pzs_sold_date', true );
	$headline = 'leased-out' === $state ? __( 'This property has been leased out', 'propzed-projects' ) : __( 'This property has been sold', 'propzed-projects' );
	$when     = $date ? ' ' . sprintf( /* translators: %s: month and year */ __( 'in %s', 'propzed-projects' ), mysql2date( 'M Y', $date ) ) : '';

	$html  = '<div class="pzs-sold-banner" role="note">';
	$html .= '<strong>' . esc_html( $headline . $when ) . ' through Propzed.</strong>';
	$html .= '<p>' . esc_html__( 'Looking for something similar? We often have off-market options in the same area. Tell us your requirement and an advisor will call you.', 'propzed-projects' ) . '</p>';
	$html .= '<p class="pzs-sold-banner__cta"><a class="pz-btn pz-btn--gold" href="' . esc_url( $req ) . '">' . esc_html__( 'Post your requirement', 'propzed-projects' ) . '</a> ';
	$html .= '<a class="pz-btn pz-btn--outline" href="' . esc_url( is_wp_error( $similar ) ? propzed_listing_url() : $similar ) . '">' . esc_html__( 'See similar listings', 'propzed-projects' ) . '</a></p>';
	$html .= '</div>';
	return $html . $content;
}
add_filter( 'the_content', 'pzs_sold_banner', 20 ); // After wpautop, so the banner markup is left alone.

/**
 * Swap the theme's JSON-LD availability to SoldOut for sold listings.
 * The theme prints JSON-LD in propzed_seo_schema() with no filter, so its output
 * is buffered and the one value replaced.
 */
add_action(
	'wp',
	static function () {
		if ( ! is_singular( 'property' ) || ! pzs_sale_state( get_queried_object_id() ) || ! function_exists( 'propzed_seo_schema' ) ) {
			return;
		}
		if ( remove_action( 'wp_head', 'propzed_seo_schema', 20 ) ) {
			add_action(
				'wp_head',
				static function () {
					ob_start();
					propzed_seo_schema();
					echo str_replace( '"https://schema.org/InStock"', '"https://schema.org/SoldOut"', (string) ob_get_clean() ); // phpcs:ignore WordPress.Security.EscapeOutput
				},
				20
			);
		}
	}
);

/**
 * Sale state in the WP REST API (/wp-json/wp/v2/property).
 */
add_action(
	'rest_api_init',
	static function () {
		register_rest_field(
			'property',
			'pzs_sale',
			array(
				'get_callback' => static function ( $data ) {
					return array(
						'state' => pzs_sale_state( $data['id'] ),
						'date'  => (string) get_post_meta( $data['id'], '_pzs_sold_date', true ),
					);
				},
				'schema'       => null,
			)
		);
	}
);
