<?php
/**
 * FLOOR PLANS & LIVE INVENTORY (residential projects, villa layouts, office floors)
 *
 * Stored in post meta "_pzs_inventory":
 *   show_prices  bool
 *   plans[]      id, name, prefix, floors ("1-20", "G,1-5,7-12", "" = single level),
 *                image (attachment ID), slots[]
 *   slots[]      id, label ("01" / "Villa 7"), x, y, w, h (percent of the plan image,
 *                empty = no hotspot), config ("2 BHK"), carpet, facing, price
 *   units{}      "planId|slotId|floor" => status, price (override), buyer, phone,
 *                date (booking date), note
 *
 * A unit with no entry is Available. Buyer, phone and note are admin-only and
 * never leave wp-admin. The public view only gets code, type, size, facing,
 * price (if enabled) and status.
 *
 * Front end: an interactive section after the Overview on the property page,
 * and [propzed_inventory id="123"] anywhere else.
 *
 * @package PropzedProjects
 */

defined( 'ABSPATH' ) || exit;

/**
 * Unit statuses: key => [ label, public label ].
 *
 * @return array
 */
function pzs_unit_statuses() {
	return array(
		'available' => array( __( 'Available', 'propzed-projects' ), __( 'Available', 'propzed-projects' ) ),
		'hold'      => array( __( 'On hold', 'propzed-projects' ), __( 'On hold', 'propzed-projects' ) ),
		'booked'    => array( __( 'Booked', 'propzed-projects' ), __( 'Booked', 'propzed-projects' ) ),
		'sold'      => array( __( 'Sold (registered)', 'propzed-projects' ), __( 'Sold', 'propzed-projects' ) ),
	);
}

/**
 * Raw inventory.
 *
 * @param int $post_id Post ID.
 * @return array
 */
function pzs_get_inventory( $post_id ) {
	$inv = get_post_meta( $post_id, '_pzs_inventory', true );
	$inv = is_array( $inv ) ? $inv : array();
	return wp_parse_args(
		$inv,
		array(
			'show_prices' => false,
			'plans'       => array(),
			'units'       => array(),
		)
	);
}

/**
 * Expand a floors spec: "G,1-3,5" => [ 'G', '1', '2', '3', '5' ]; "" => [ '' ].
 *
 * @param string $spec Spec.
 * @return string[]
 */
function pzs_parse_floors( $spec ) {
	$spec = trim( (string) $spec );
	if ( '' === $spec ) {
		return array( '' );
	}
	$floors = array();
	foreach ( explode( ',', $spec ) as $token ) {
		$token = trim( $token );
		if ( preg_match( '/^(\d{1,3})\s*-\s*(\d{1,3})$/', $token, $m ) ) {
			$from = (int) $m[1];
			$to   = min( (int) $m[2], $from + 199 );
			for ( $f = $from; $f <= $to; $f++ ) {
				$floors[] = (string) $f;
			}
		} elseif ( preg_match( '/^[A-Za-z0-9]{1,6}$/', $token ) ) {
			$floors[] = $token;
		}
	}
	$floors = array_values( array_unique( $floors ) );
	return $floors ? array_slice( $floors, 0, 200 ) : array( '' );
}

/**
 * Unit code, e.g. "A-1203", "Villa 7", "B-G04".
 *
 * @param array  $plan  Plan.
 * @param array  $slot  Slot.
 * @param string $floor Floor.
 * @return string
 */
function pzs_unit_code( $plan, $slot, $floor ) {
	$label = (string) $slot['label'];
	if ( '' === $floor ) {
		return $plan['prefix'] . $label;
	}
	return $plan['prefix'] . $floor . ( ctype_digit( $label ) ? str_pad( $label, 2, '0', STR_PAD_LEFT ) : '-' . $label );
}

/**
 * Sanitize editor JSON into the stored structure.
 *
 * @param array $in Decoded JSON.
 * @return array
 */
function pzs_sanitize_inventory( $in ) {
	$num = static function ( $v, $min = 0, $max = 100 ) {
		if ( '' === $v || null === $v || ! is_numeric( $v ) ) {
			return '';
		}
		return round( max( $min, min( $max, (float) $v ) ), 3 );
	};
	$id = static function ( $v ) {
		return substr( preg_replace( '/[^a-z0-9_]/', '', strtolower( (string) $v ) ), 0, 20 );
	};

	$out = array(
		'show_prices' => ! empty( $in['show_prices'] ),
		'plans'       => array(),
		'units'       => array(),
	);

	$valid_keys = array();
	foreach ( array_slice( isset( $in['plans'] ) && is_array( $in['plans'] ) ? $in['plans'] : array(), 0, 30 ) as $plan ) {
		if ( ! is_array( $plan ) || ! $id( isset( $plan['id'] ) ? $plan['id'] : '' ) ) {
			continue;
		}
		$p = array(
			'id'     => $id( $plan['id'] ),
			'name'   => sanitize_text_field( isset( $plan['name'] ) ? $plan['name'] : '' ),
			'prefix' => substr( sanitize_text_field( isset( $plan['prefix'] ) ? $plan['prefix'] : '' ), 0, 12 ),
			'floors' => sanitize_text_field( isset( $plan['floors'] ) ? $plan['floors'] : '' ),
			'image'  => isset( $plan['image'] ) ? absint( $plan['image'] ) : 0,
			'slots'  => array(),
		);
		foreach ( array_slice( isset( $plan['slots'] ) && is_array( $plan['slots'] ) ? $plan['slots'] : array(), 0, 300 ) as $slot ) {
			if ( ! is_array( $slot ) || ! $id( isset( $slot['id'] ) ? $slot['id'] : '' ) ) {
				continue;
			}
			$p['slots'][] = array(
				'id'     => $id( $slot['id'] ),
				'label'  => substr( sanitize_text_field( isset( $slot['label'] ) ? $slot['label'] : '' ), 0, 20 ),
				'x'      => $num( isset( $slot['x'] ) ? $slot['x'] : '' ),
				'y'      => $num( isset( $slot['y'] ) ? $slot['y'] : '' ),
				'w'      => $num( isset( $slot['w'] ) ? $slot['w'] : '' ),
				'h'      => $num( isset( $slot['h'] ) ? $slot['h'] : '' ),
				'config' => substr( sanitize_text_field( isset( $slot['config'] ) ? $slot['config'] : '' ), 0, 40 ),
				'carpet' => substr( sanitize_text_field( isset( $slot['carpet'] ) ? $slot['carpet'] : '' ), 0, 40 ),
				'facing' => substr( sanitize_text_field( isset( $slot['facing'] ) ? $slot['facing'] : '' ), 0, 40 ),
				'price'  => isset( $slot['price'] ) && is_numeric( $slot['price'] ) ? (float) $slot['price'] : '',
			);
		}
		foreach ( pzs_parse_floors( $p['floors'] ) as $floor ) {
			foreach ( $p['slots'] as $s ) {
				$valid_keys[ $p['id'] . '|' . $s['id'] . '|' . $floor ] = true;
			}
		}
		$out['plans'][] = $p;
	}

	$statuses = pzs_unit_statuses();
	if ( isset( $in['units'] ) && is_array( $in['units'] ) ) {
		foreach ( $in['units'] as $key => $unit ) {
			if ( ! isset( $valid_keys[ $key ] ) || ! is_array( $unit ) ) {
				continue;
			}
			$u = array(
				'status' => isset( $unit['status'] ) && isset( $statuses[ $unit['status'] ] ) ? $unit['status'] : 'available',
				'price'  => isset( $unit['price'] ) && is_numeric( $unit['price'] ) ? (float) $unit['price'] : '',
				'buyer'  => substr( sanitize_text_field( isset( $unit['buyer'] ) ? $unit['buyer'] : '' ), 0, 80 ),
				'phone'  => substr( sanitize_text_field( isset( $unit['phone'] ) ? $unit['phone'] : '' ), 0, 20 ),
				'date'   => isset( $unit['date'] ) && preg_match( '/^\d{4}-\d{2}-\d{2}$/', (string) $unit['date'] ) ? $unit['date'] : '',
				'note'   => substr( sanitize_textarea_field( isset( $unit['note'] ) ? $unit['note'] : '' ), 0, 500 ),
			);
			// Store only units that differ from a plain available unit.
			if ( 'available' !== $u['status'] || '' !== $u['price'] || $u['buyer'] || $u['phone'] || $u['note'] ) {
				$out['units'][ $key ] = $u;
			}
		}
	}
	return $out;
}

/**
 * Counts for one inventory.
 *
 * @param array $inv Inventory.
 * @return array total, available, hold, booked (booked + sold), last (Y-m-d), configs[]
 */
function pzs_inventory_stats( $inv ) {
	$stats = array(
		'total'     => 0,
		'available' => 0,
		'hold'      => 0,
		'booked'    => 0,
		'last'      => '',
		'configs'   => array(),
	);
	foreach ( $inv['plans'] as $plan ) {
		foreach ( pzs_parse_floors( $plan['floors'] ) as $floor ) {
			foreach ( $plan['slots'] as $slot ) {
				++$stats['total'];
				if ( $slot['config'] ) {
					$stats['configs'][ $slot['config'] ] = true;
				}
				$key    = $plan['id'] . '|' . $slot['id'] . '|' . $floor;
				$status = isset( $inv['units'][ $key ]['status'] ) ? $inv['units'][ $key ]['status'] : 'available';
				if ( 'available' === $status ) {
					++$stats['available'];
				} elseif ( 'hold' === $status ) {
					++$stats['hold'];
				} else {
					++$stats['booked'];
					$date = $inv['units'][ $key ]['date'];
					if ( $date && $date > $stats['last'] ) {
						$stats['last'] = $date;
					}
				}
			}
		}
	}
	$stats['configs'] = array_keys( $stats['configs'] );
	natsort( $stats['configs'] );
	$stats['configs'] = array_values( $stats['configs'] );
	return $stats;
}

/**
 * Public, privacy-safe data for the front end.
 *
 * @param int $post_id Post ID.
 * @return array|null Null when there is no inventory.
 */
function pzs_inventory_public( $post_id ) {
	$inv = pzs_get_inventory( $post_id );
	if ( ! $inv['plans'] ) {
		return null;
	}
	$plans = array();
	foreach ( $inv['plans'] as $plan ) {
		if ( ! $plan['slots'] ) {
			continue;
		}
		$img = $plan['image'] ? wp_get_attachment_image_src( $plan['image'], 'full' ) : false;
		$p   = array(
			'id'     => $plan['id'],
			'name'   => $plan['name'],
			'image'  => $img ? array( 'url' => $img[0], 'w' => (int) $img[1], 'h' => (int) $img[2], 'alt' => $plan['name'] ) : null,
			'floors' => pzs_parse_floors( $plan['floors'] ),
			'slots'  => array(),
			'units'  => array(),
		);
		foreach ( $plan['slots'] as $slot ) {
			$p['slots'][] = array(
				'id'     => $slot['id'],
				'label'  => $slot['label'],
				'box'    => ( '' !== $slot['x'] && '' !== $slot['w'] ) ? array( $slot['x'], $slot['y'], $slot['w'], $slot['h'] ) : null,
				'config' => $slot['config'],
				'carpet' => $slot['carpet'],
				'facing' => $slot['facing'],
			);
			foreach ( $p['floors'] as $floor ) {
				$key    = $plan['id'] . '|' . $slot['id'] . '|' . $floor;
				$unit   = isset( $inv['units'][ $key ] ) ? $inv['units'][ $key ] : array();
				$status = isset( $unit['status'] ) ? $unit['status'] : 'available';
				$price  = isset( $unit['price'] ) && '' !== $unit['price'] ? $unit['price'] : $slot['price'];
				$p['units'][ $slot['id'] . '|' . $floor ] = array(
					'code'   => pzs_unit_code( $plan, $slot, $floor ),
					'status' => 'sold' === $status ? 'booked' : $status,
					'price'  => ( $inv['show_prices'] && '' !== $price && $price > 0 && 'available' === $status ) ? propzed_format_inr( $price ) : '',
				);
			}
		}
		$plans[] = $p;
	}
	if ( ! $plans ) {
		return null;
	}
	return array(
		'id'    => (int) $post_id,
		'title' => html_entity_decode( get_the_title( $post_id ), ENT_QUOTES, 'UTF-8' ),
		'url'   => get_permalink( $post_id ),
		'plans' => $plans,
		'stats' => pzs_inventory_stats( $inv ),
	);
}

/* -------------------------------------------------------------------------
 * wp-admin editor
 * ---------------------------------------------------------------------- */

add_action(
	'add_meta_boxes_property',
	static function () {
		add_meta_box( 'pzs_inventory', __( 'Floor Plans & Unit Inventory (projects, villas, office floors)', 'propzed-projects' ), 'pzs_render_inventory_box', 'property', 'normal', 'high' );
	}
);

/**
 * Meta box: the JS editor reads and writes the hidden JSON field.
 *
 * @param WP_Post $post Post.
 */
function pzs_render_inventory_box( $post ) {
	$inv = pzs_get_inventory( $post->ID );
	foreach ( $inv['plans'] as $i => $plan ) {
		$img                                = $plan['image'] ? wp_get_attachment_image_src( $plan['image'], 'large' ) : false;
		$inv['plans'][ $i ]['image_url']    = $img ? $img[0] : '';
	}
	wp_nonce_field( 'pzs_inventory', 'pzs_inventory_nonce' );
	?>
	<p class="description">
		<?php esc_html_e( 'Upload a floor plan or master layout, drag boxes over each flat / villa, set the floors, then click units below to mark them Booked or Sold. Visitors see live availability on the property page. Buyer details stay private.', 'propzed-projects' ); ?>
	</p>
	<textarea name="pzs_inventory_json" id="pzs-inventory-json" hidden><?php echo esc_textarea( wp_json_encode( (object) $inv ) ); ?></textarea>
	<div id="pzs-inv-app" class="pzs-inv-app"><p><?php esc_html_e( 'Loading editor…', 'propzed-projects' ); ?></p></div>
	<?php
}

add_action(
	'admin_enqueue_scripts',
	static function ( $hook ) {
		$screen = get_current_screen();
		if ( ! $screen || 'property' !== $screen->post_type ) {
			return;
		}
		wp_enqueue_style( 'pzs-admin', PZS_URL . 'assets/css/pzs-admin.css', array(), PZS_VERSION );
		if ( in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
			wp_enqueue_media();
			wp_enqueue_script( 'pzs-admin', PZS_URL . 'assets/js/pzs-admin.js', array(), PZS_VERSION, true );
			wp_localize_script(
				'pzs-admin',
				'pzsAdmin',
				array(
					'statuses' => array_map(
						static function ( $s ) {
							return $s[0];
						},
						pzs_unit_statuses()
					),
				)
			);
		}
	}
);

add_action(
	'save_post_property',
	static function ( $post_id ) {
		if ( ! isset( $_POST['pzs_inventory_nonce'], $_POST['pzs_inventory_json'] ) || ! wp_verify_nonce( sanitize_key( $_POST['pzs_inventory_nonce'] ), 'pzs_inventory' ) ) {
			return;
		}
		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || wp_is_post_revision( $post_id ) || ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		$data = json_decode( wp_unslash( $_POST['pzs_inventory_json'] ), true ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitized below.
		if ( ! is_array( $data ) ) {
			return;
		}
		$inv = pzs_sanitize_inventory( $data );
		if ( $inv['plans'] ) {
			update_post_meta( $post_id, '_pzs_inventory', $inv );
		} else {
			delete_post_meta( $post_id, '_pzs_inventory' );
		}
		pzs_maybe_auto_sold( $post_id, $inv );
	},
	20
);

/**
 * Sold out when every unit is booked; back to available if units free up
 * (only when the plugin set Sold automatically).
 *
 * @param int   $post_id Post ID.
 * @param array $inv     Inventory.
 */
function pzs_maybe_auto_sold( $post_id, $inv ) {
	if ( ! pzs_setting( 'auto_sold' ) || ! function_exists( 'pzs_set_sale_state' ) ) {
		return;
	}
	$stats = pzs_inventory_stats( $inv );
	$state = pzs_sale_state( $post_id );
	if ( $stats['total'] > 0 && 0 === $stats['available'] + $stats['hold'] && ! $state ) {
		pzs_set_sale_state( $post_id, 'sold', $stats['last'] );
		update_post_meta( $post_id, '_pzs_auto_sold', 1 );
	} elseif ( 'sold' === $state && get_post_meta( $post_id, '_pzs_auto_sold', true ) && $stats['available'] + $stats['hold'] > 0 ) {
		pzs_set_sale_state( $post_id, '' );
		delete_post_meta( $post_id, '_pzs_auto_sold' );
	}
}

/* -------------------------------------------------------------------------
 * Front end
 * ---------------------------------------------------------------------- */

/**
 * Inventory section HTML (data + no-JS summary; assets/js/pzs-inventory.js builds the UI).
 *
 * @param int $post_id Post ID.
 * @return string
 */
function pzs_render_inventory( $post_id ) {
	$data = pzs_inventory_public( $post_id );
	if ( ! $data ) {
		return '';
	}
	pzs_enqueue_front();
	$s       = $data['stats'];
	$sold    = $s['total'] ? round( 100 * $s['booked'] / $s['total'] ) : 0;
	$heading = count( $data['plans'] ) > 1 || count( $data['plans'][0]['floors'] ) > 1 ? __( 'Floor Plans & Live Availability', 'propzed-projects' ) : __( 'Layout & Live Availability', 'propzed-projects' );

	$html  = '<section class="pzs-inv pz-block" id="availability" aria-labelledby="pzs-inv-title-' . (int) $post_id . '">';
	$html .= '<h2 class="pz-block__title" id="pzs-inv-title-' . (int) $post_id . '">' . esc_html( $heading ) . '</h2>';
	$html .= '<div class="pzs-inv__stats">';
	$html .= '<div class="pzs-inv__meter" role="img" aria-label="' . esc_attr( sprintf( /* translators: %d: percent */ __( '%d%% booked', 'propzed-projects' ), $sold ) ) . '"><span style="width:' . (int) $sold . '%"></span></div>';
	$html .= '<p><strong>' . esc_html( sprintf( /* translators: 1: available 2: total */ __( '%1$d of %2$d units available', 'propzed-projects' ), $s['available'], $s['total'] ) ) . '</strong>';
	if ( $sold >= 10 ) {
		$html .= ' · ' . esc_html( sprintf( /* translators: %d: percent */ __( '%d%% booked', 'propzed-projects' ), $sold ) );
	}
	if ( $s['last'] ) {
		$days  = max( 0, (int) floor( ( strtotime( current_time( 'Y-m-d' ) ) - strtotime( $s['last'] ) ) / DAY_IN_SECONDS ) );
		$when  = 0 === $days ? __( 'today', 'propzed-projects' ) : ( $days < 60 ? sprintf( /* translators: %d: days */ _n( '%d day ago', '%d days ago', $days, 'propzed-projects' ), $days ) : '' );
		$html .= $when ? ' · ' . esc_html( sprintf( /* translators: %s: "3 days ago" */ __( 'last booking %s', 'propzed-projects' ), $when ) ) : '';
	}
	$html .= '</p></div>';
	$html .= '<div class="pzs-inv__app" data-pzs-inv><noscript><p>' . esc_html__( 'Turn on JavaScript to explore the floor plan, or call us for the availability sheet.', 'propzed-projects' ) . '</p></noscript></div>';
	$html .= '<script type="application/json" class="pzs-inv__data">' . wp_json_encode( $data, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE ) . '</script>';
	$html .= '</section>';
	return $html;
}

/**
 * Append the section to the property description (JS moves it below the Overview box).
 *
 * @param string $content Content.
 * @return string
 */
function pzs_inventory_content( $content ) {
	if ( ! is_singular( 'property' ) || ! in_the_loop() || ! is_main_query() ) {
		return $content;
	}
	if ( has_shortcode( $content, 'propzed_inventory' ) ) {
		return $content;
	}
	return $content . pzs_render_inventory( get_the_ID() );
}
add_filter( 'the_content', 'pzs_inventory_content', 15 );

add_shortcode(
	'propzed_inventory',
	static function ( $atts ) {
		$atts = shortcode_atts( array( 'id' => 0 ), $atts, 'propzed_inventory' );
		$id   = $atts['id'] ? absint( $atts['id'] ) : get_the_ID();
		if ( ! $id || 'property' !== get_post_type( $id ) || 'publish' !== get_post_status( $id ) ) {
			return '';
		}
		return pzs_render_inventory( $id );
	}
);

/**
 * Site-wide: styles (sold ribbons, chips, assistant) and the small card script.
 * The inventory script loads in the head queue when the property has one.
 */
add_action(
	'wp_enqueue_scripts',
	static function () {
		wp_enqueue_style( 'pzs', PZS_URL . 'assets/css/pzs.css', wp_style_is( 'propzed-main', 'registered' ) ? array( 'propzed-main' ) : array(), PZS_VERSION );
		wp_enqueue_script( 'pzs', PZS_URL . 'assets/js/pzs.js', array(), PZS_VERSION, true );
		if ( is_singular( 'property' ) && get_post_meta( get_queried_object_id(), '_pzs_inventory', true ) ) {
			pzs_enqueue_front();
		}
	},
	25
);

/**
 * Inventory viewer assets.
 */
function pzs_enqueue_front() {
	wp_enqueue_style( 'pzs', PZS_URL . 'assets/css/pzs.css', array(), PZS_VERSION );
	wp_enqueue_script( 'pzs-inventory', PZS_URL . 'assets/js/pzs-inventory.js', array(), PZS_VERSION, true );
	static $localized = false;
	if ( ! $localized ) {
		$localized = true;
		wp_localize_script(
			'pzs-inventory',
			'pzsInv',
			array(
				'whatsapp' => preg_replace( '/\D/', '', (string) propzed_option( 'whatsapp' ) ),
				'i18n'     => array(
					'available' => __( 'Available', 'propzed-projects' ),
					'hold'      => __( 'On hold', 'propzed-projects' ),
					'booked'    => __( 'Booked / Sold', 'propzed-projects' ),
					'floor'     => __( 'Floor', 'propzed-projects' ),
					'allTypes'  => __( 'All', 'propzed-projects' ),
					'onlyAvail' => __( 'Available only', 'propzed-projects' ),
					'pick'      => __( 'Tap a unit on the plan or in the building chart.', 'propzed-projects' ),
					'carpet'    => __( 'Carpet', 'propzed-projects' ),
					'facing'    => __( 'Facing', 'propzed-projects' ),
					'price'     => __( 'Price', 'propzed-projects' ),
					'onRequest' => __( 'On request', 'propzed-projects' ),
					'enquire'   => __( 'Enquire about this unit', 'propzed-projects' ),
					'whatsapp'  => __( 'WhatsApp', 'propzed-projects' ),
					'taken'     => __( 'This unit is booked. Similar units may still be available, ask us.', 'propzed-projects' ),
					'held'      => __( 'Temporarily on hold. Ask us to join the waiting list.', 'propzed-projects' ),
					/* translators: 1: unit code 2: project */
					'msg'       => __( 'I am interested in unit %1$s at %2$s. Please share the price and payment plan.', 'propzed-projects' ),
					'building'  => __( 'Building chart: floors and units', 'propzed-projects' ),
				),
			)
		);
	}
}

/**
 * Scarcity chips on property cards: collect IDs while cards render.
 */
add_action(
	'the_post',
	static function ( $post ) {
		if ( is_admin() || 'property' !== $post->post_type || ! pzs_setting( 'scarcity' ) ) {
			return;
		}
		$GLOBALS['pzs_seen_props'][ $post->ID ] = true;
	}
);

add_action(
	'wp_footer',
	static function () {
		if ( empty( $GLOBALS['pzs_seen_props'] ) ) {
			return;
		}
		$map = array();
		foreach ( array_keys( $GLOBALS['pzs_seen_props'] ) as $id ) {
			$inv = get_post_meta( $id, '_pzs_inventory', true );
			if ( ! is_array( $inv ) || empty( $inv['plans'] ) || pzs_sale_state( $id ) ) {
				continue;
			}
			$s = pzs_inventory_stats( pzs_get_inventory( $id ) );
			if ( $s['total'] ) {
				$map[ $id ] = array( $s['available'], $s['total'] );
			}
		}
		if ( ! $map ) {
			return;
		}
		/* translators: 1: units left 2: total units */
		$label = __( '%1$d of %2$d units left', 'propzed-projects' );
		echo '<script>window.pzsLeft=' . wp_json_encode( $map ) . ';window.pzsLeftLabel=' . wp_json_encode( $label ) . ';</script>' . "\n";
	},
	5
);

/**
 * Public REST endpoint for the app: GET /wp-json/propzed-projects/v1/inventory/{id}
 */
add_action(
	'rest_api_init',
	static function () {
		register_rest_route(
			'propzed-projects/v1',
			'/inventory/(?P<id>\d+)',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'permission_callback' => '__return_true',
				'callback'            => static function ( $request ) {
					$id = (int) $request['id'];
					if ( 'property' !== get_post_type( $id ) || 'publish' !== get_post_status( $id ) ) {
						return new WP_Error( 'pzs_not_found', __( 'Not found.', 'propzed-projects' ), array( 'status' => 404 ) );
					}
					$data = pzs_inventory_public( $id );
					return $data ? rest_ensure_response( $data ) : new WP_Error( 'pzs_no_inventory', __( 'This property has no inventory.', 'propzed-projects' ), array( 'status' => 404 ) );
				},
			)
		);
	}
);

/**
 * Schema.org: ApartmentComplex with unit counts for residential projects.
 */
add_action(
	'wp_head',
	static function () {
		if ( ! is_singular( 'property' ) ) {
			return;
		}
		$id    = get_queried_object_id();
		$slugs = wp_get_object_terms( $id, 'property_type', array( 'fields' => 'slugs' ) );
		if ( is_wp_error( $slugs ) || ! array_intersect( $slugs, array( 'residential-project', 'apartment' ) ) ) {
			return;
		}
		$inv = pzs_get_inventory( $id );
		$s   = pzs_inventory_stats( $inv );
		$ld  = array_filter(
			array(
				'@context'                             => 'https://schema.org',
				'@type'                                => 'ApartmentComplex',
				'name'                                 => html_entity_decode( get_the_title( $id ), ENT_QUOTES, 'UTF-8' ),
				'url'                                  => get_permalink( $id ),
				'image'                                => get_the_post_thumbnail_url( $id, 'large' ),
				'address'                              => array(
					'@type'           => 'PostalAddress',
					'streetAddress'   => propzed_meta( 'address', $id ),
					'addressLocality' => propzed_location_label( $id ),
					'addressRegion'   => 'Maharashtra',
					'addressCountry'  => 'IN',
				),
				'numberOfAccommodationUnits'           => $s['total'] ? $s['total'] : null,
				'numberOfAvailableAccommodationUnits'  => $s['total'] ? $s['available'] : null,
				'tourBookingPage'                      => get_permalink( $id ) . '#pz-form',
			)
		);
		echo '<script type="application/ld+json">' . wp_json_encode( $ld, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . "</script>\n";
	},
	21
);
