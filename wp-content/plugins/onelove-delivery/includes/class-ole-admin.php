<?php
/**
 * wp-admin: Deliveries menu (Dispatch board + Settings), rider phone on user profiles,
 * delivery box on the order edit screen.
 *
 * @package OneLoveDelivery
 */

defined( 'ABSPATH' ) || exit;

class OLE_Admin {

	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'menu' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'assets' ) );

		add_action( 'show_user_profile', array( __CLASS__, 'profile_fields' ) );
		add_action( 'edit_user_profile', array( __CLASS__, 'profile_fields' ) );
		add_action( 'user_new_form', array( __CLASS__, 'profile_fields' ) );
		add_action( 'personal_options_update', array( __CLASS__, 'save_profile' ) );
		add_action( 'edit_user_profile_update', array( __CLASS__, 'save_profile' ) );
		add_action( 'user_register', array( __CLASS__, 'save_profile' ) );

		add_action( 'add_meta_boxes', array( __CLASS__, 'order_box' ) );
		add_filter( 'plugin_action_links_' . plugin_basename( OLE_DELIVERY_FILE ), array( __CLASS__, 'action_links' ) );
	}

	public static function menu() {
		add_menu_page( 'Deliveries', 'Deliveries', 'manage_woocommerce', 'ole-dispatch', array( __CLASS__, 'dispatch_page' ), 'dashicons-location-alt', 56 );
		add_submenu_page( 'ole-dispatch', 'Dispatch', 'Dispatch', 'manage_woocommerce', 'ole-dispatch', array( __CLASS__, 'dispatch_page' ) );
		add_submenu_page( 'ole-dispatch', 'Riders', 'Riders', 'manage_woocommerce', 'users.php?role=ole_rider' );
		add_submenu_page( 'ole-dispatch', 'Delivery Settings', 'Settings', 'manage_woocommerce', 'ole-settings', array( 'OLE_Settings', 'render_page' ) );
	}

	public static function action_links( $links ) {
		array_unshift( $links, '<a href="' . esc_url( admin_url( 'admin.php?page=ole-settings' ) ) . '">Settings</a>' );
		return $links;
	}

	public static function assets( $hook ) {
		if ( 'toplevel_page_ole-dispatch' !== $hook ) {
			return;
		}
		wp_enqueue_style( 'ole-dispatch', OLE_DELIVERY_URL . 'assets/css/dispatch.css', array(), OLE_DELIVERY_VERSION );
		wp_enqueue_script( 'ole-maps-loader', OLE_DELIVERY_URL . 'assets/js/maps-loader.js', array(), OLE_DELIVERY_VERSION, true );
		wp_enqueue_script( 'ole-dispatch', OLE_DELIVERY_URL . 'assets/js/dispatch.js', array( 'ole-maps-loader' ), OLE_DELIVERY_VERSION, true );
		wp_localize_script( 'ole-dispatch', 'OLE_DISPATCH', array(
			'api'      => esc_url_raw( rest_url( OLE_REST::NS ) ),
			'nonce'    => wp_create_nonce( 'wp_rest' ),
			'mapsKey'  => OLE_Settings::get( 'maps_key' ),
			'settings' => admin_url( 'admin.php?page=ole-settings' ),
			'addRider' => admin_url( 'user-new.php' ),
		) );
	}

	public static function dispatch_page() {
		?>
		<div class="wrap ole-dispatch">
			<div class="ole-d-head">
				<div>
					<h1>Delivery Dispatch</h1>
					<p class="ole-d-sub" id="ole-d-sub">Loading…</p>
				</div>
				<div class="ole-d-actions">
					<a class="button" href="<?php echo esc_url( home_url( '/rider/' ) ); ?>" target="_blank">Open rider app</a>
					<a class="button" href="<?php echo esc_url( admin_url( 'user-new.php' ) ); ?>">Add rider</a>
					<a class="button" href="<?php echo esc_url( admin_url( 'admin.php?page=ole-settings' ) ); ?>">Settings</a>
				</div>
			</div>

			<?php if ( ! OLE_Settings::get( 'maps_key' ) ) : ?>
				<div class="notice notice-warning inline"><p>Add your Google Maps API key in <a href="<?php echo esc_url( admin_url( 'admin.php?page=ole-settings' ) ); ?>">Settings</a> to see riders and orders on the map. Everything else already works.</p></div>
			<?php endif; ?>

			<div class="ole-d-kpis" id="ole-d-kpis"></div>

			<div class="ole-d-grid">
				<section class="ole-d-card ole-d-orders">
					<div class="ole-d-tabs" role="tablist">
						<button type="button" class="is-active" data-filter="open">Open</button>
						<button type="button" data-filter="pending">Waiting</button>
						<button type="button" data-filter="moving">On the road</button>
						<button type="button" data-filter="done">Done (24h)</button>
					</div>
					<div id="ole-d-list" class="ole-d-list"></div>
				</section>

				<section class="ole-d-card ole-d-mapwrap">
					<div id="ole-d-map" class="ole-d-map"></div>
				</section>

				<section class="ole-d-card ole-d-riders">
					<h2>Riders</h2>
					<div id="ole-d-riders"></div>
				</section>
			</div>
		</div>
		<?php
	}

	/* Rider phone on the user profile ----------------------------------- */

	public static function profile_fields( $user ) {
		if ( ! current_user_can( 'manage_woocommerce' ) && ! ( $user instanceof WP_User && get_current_user_id() === $user->ID ) ) {
			return;
		}
		$phone = $user instanceof WP_User ? get_user_meta( $user->ID, 'ole_phone', true ) : '';
		?>
		<h2>Delivery rider</h2>
		<table class="form-table" role="presentation">
			<tr>
				<th><label for="ole_phone">Rider mobile / WhatsApp</label></th>
				<td>
					<input type="tel" name="ole_phone" id="ole_phone" value="<?php echo esc_attr( $phone ); ?>" class="regular-text" placeholder="9876543210">
					<p class="description">Used for new-order WhatsApp alerts and shown to customers so they can call the rider. Only needed for users with the <b>Delivery Rider</b> role.</p>
				</td>
			</tr>
		</table>
		<?php
	}

	public static function save_profile( $user_id ) {
		if ( ! current_user_can( 'edit_user', $user_id ) || ! isset( $_POST['ole_phone'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification -- core verifies the profile nonce.
			return;
		}
		update_user_meta( $user_id, 'ole_phone', sanitize_text_field( wp_unslash( $_POST['ole_phone'] ) ) ); // phpcs:ignore WordPress.Security.NonceVerification
	}

	/* Order edit screen -------------------------------------------------- */

	public static function order_box() {
		$screens = array( 'shop_order' );
		if ( function_exists( 'wc_get_page_screen_id' ) ) {
			$screens[] = wc_get_page_screen_id( 'shop-order' );
		}
		foreach ( array_unique( $screens ) as $screen ) {
			add_meta_box( 'ole-delivery', 'Delivery', array( __CLASS__, 'order_box_html' ), $screen, 'side', 'high' );
		}
	}

	public static function order_box_html( $post_or_order ) {
		$order = $post_or_order instanceof WP_Post ? wc_get_order( $post_or_order->ID ) : $post_or_order;
		if ( ! $order ) {
			return;
		}
		$d = OLE_Deliveries::get_by_order( $order->get_id() );
		if ( ! $d ) {
			echo '<p>No delivery yet. It is created automatically when the order is <b>Processing</b> (paid or COD).</p>';
			return;
		}
		$rider = $d->rider_id ? get_userdata( $d->rider_id ) : null;
		printf( '<p><b>Status:</b> %s</p>', esc_html( OLE_Deliveries::labels()[ $d->status ] ?? $d->status ) );
		printf( '<p><b>Rider:</b> %s</p>', esc_html( $rider ? $rider->display_name : '—' ) );
		if ( $d->is_cod ) {
			printf( '<p><b>COD:</b> ₹%s %s</p>', esc_html( number_format( (float) $d->cod_amount, 2 ) ), null !== $d->cod_collected ? '(collected ₹' . esc_html( number_format( (float) $d->cod_collected, 2 ) ) . ')' : '' );
		}
		printf( '<p><b>Delivery OTP:</b> <code>%s</code></p>', esc_html( $d->otp ) );
		if ( $d->fail_reason ) {
			printf( '<p><b>Problem:</b> %s</p>', esc_html( $d->fail_reason ) );
		}
		printf(
			'<p><a class="button" href="%s" target="_blank">Tracking page</a> <a class="button" href="%s">Dispatch</a></p>',
			esc_url( OLE_Deliveries::track_url( $d ) ),
			esc_url( admin_url( 'admin.php?page=ole-dispatch' ) )
		);
	}
}
