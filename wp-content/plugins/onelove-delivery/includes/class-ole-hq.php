<?php
/**
 * "One Love HQ": an app-style wp-admin.
 *  - HQ home screen (live sales, orders, deliveries, riders, top products, stock) as the dashboard
 *  - One Love colour skin for the whole admin + login screen
 *  - Mobile bottom tab bar, installable as a home-screen app (manifest)
 *
 * @package OneLoveDelivery
 */

defined( 'ABSPATH' ) || exit;

class OLE_HQ {

	const SLUG = 'ole-hq';

	public static function init() {
		// Login screen skin applies even when not logged in.
		add_action( 'login_enqueue_scripts', array( __CLASS__, 'login_assets' ) );
		add_filter( 'login_headerurl', array( __CLASS__, 'login_url' ) );
		add_filter( 'login_headertext', array( __CLASS__, 'login_text' ) );

		if ( ! is_admin() ) {
			return;
		}
		add_action( 'admin_menu', array( __CLASS__, 'menu' ), 5 );
		add_action( 'load-index.php', array( __CLASS__, 'redirect_dashboard' ) );
		add_filter( 'admin_body_class', array( __CLASS__, 'body_class' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'assets' ) );
		add_action( 'admin_head', array( __CLASS__, 'app_head' ) );
		add_action( 'admin_footer', array( __CLASS__, 'bottom_nav' ) );
		add_action( 'admin_bar_menu', array( __CLASS__, 'admin_bar' ), 999 );
		add_action( 'wp_ajax_ole_hq_live', array( __CLASS__, 'ajax_live' ) );
	}

	public static function skin_on() {
		return OLE_Settings::yes( 'admin_skin' );
	}

	protected static function can_view() {
		return current_user_can( 'manage_woocommerce' );
	}

	/* ------------------------------------------------------------------ */
	/* Menu, redirect, chrome                                              */
	/* ------------------------------------------------------------------ */

	public static function menu() {
		add_menu_page( 'One Love HQ', 'HQ', 'manage_woocommerce', self::SLUG, array( __CLASS__, 'page' ), 'dashicons-heart', 1 );
		add_submenu_page( self::SLUG, 'One Love HQ', 'Home', 'manage_woocommerce', self::SLUG, array( __CLASS__, 'page' ) );
		add_submenu_page( self::SLUG, 'Classic dashboard', 'Classic dashboard', 'manage_woocommerce', 'index.php?classic=1' );
	}

	/** Shop managers land on HQ instead of the WordPress dashboard. */
	public static function redirect_dashboard() {
		if ( ! OLE_Settings::yes( 'hq_home' ) || ! self::can_view() || isset( $_GET['classic'] ) || wp_doing_ajax() ) { // phpcs:ignore WordPress.Security.NonceVerification
			return;
		}
		wp_safe_redirect( admin_url( 'admin.php?page=' . self::SLUG ) );
		exit;
	}

	public static function body_class( $classes ) {
		if ( self::skin_on() ) {
			$classes .= ' ole-skin';
		}
		return $classes;
	}

	public static function assets( $hook ) {
		if ( self::skin_on() ) {
			wp_enqueue_style( 'ole-fonts', 'https://fonts.googleapis.com/css2?family=Archivo:wght@400;600;700;800;900&display=swap', array(), null );
			wp_enqueue_style( 'ole-admin-skin', OLE_DELIVERY_URL . 'assets/css/admin-skin.css', array(), OLE_DELIVERY_VERSION );
		}
		if ( 'toplevel_page_' . self::SLUG === $hook ) {
			wp_enqueue_style( 'ole-hq', OLE_DELIVERY_URL . 'assets/css/hq.css', array(), OLE_DELIVERY_VERSION );
			wp_enqueue_script( 'ole-hq', OLE_DELIVERY_URL . 'assets/js/hq.js', array(), OLE_DELIVERY_VERSION, true );
			wp_localize_script( 'ole-hq', 'OLE_HQ', array(
				'ajax'  => admin_url( 'admin-ajax.php' ),
				'nonce' => wp_create_nonce( 'ole_hq_live' ),
			) );
		}
	}

	/** Lets the owner "Add to Home screen" the admin and open it like an app. */
	public static function app_head() {
		if ( ! self::skin_on() ) {
			return;
		}
		printf( '<link rel="manifest" href="%s">', esc_url( home_url( '/ole-hq.webmanifest' ) ) );
		echo '<meta name="theme-color" content="#0b0b0b">';
		echo '<meta name="apple-mobile-web-app-capable" content="yes"><meta name="mobile-web-app-capable" content="yes">';
		echo '<meta name="apple-mobile-web-app-title" content="One Love HQ">';
		printf( '<link rel="apple-touch-icon" href="%s">', esc_url( OLE_DELIVERY_URL . 'assets/img/icon.svg' ) );
	}

	public static function manifest() {
		nocache_headers();
		header( 'Content-Type: application/manifest+json; charset=utf-8' );
		echo wp_json_encode( array(
			'name'             => 'One Love HQ',
			'short_name'       => 'OL HQ',
			'start_url'        => admin_url( 'admin.php?page=' . self::SLUG ),
			'scope'            => admin_url(),
			'display'          => 'standalone',
			'background_color' => '#f6f1e4',
			'theme_color'      => '#0b0b0b',
			'icons'            => array(
				array(
					'src'   => OLE_DELIVERY_URL . 'assets/img/icon.svg',
					'sizes' => 'any',
					'type'  => 'image/svg+xml',
				),
			),
		), JSON_UNESCAPED_SLASHES );
		exit;
	}

	public static function admin_bar( $bar ) {
		if ( ! self::skin_on() || ! self::can_view() ) {
			return;
		}
		$bar->remove_node( 'wp-logo' );
		$bar->add_node( array(
			'id'    => 'ole-hq',
			'title' => '<span class="ole-ab-logo" aria-hidden="true"></span><span class="ole-ab-text">One Love HQ</span>',
			'href'  => admin_url( 'admin.php?page=' . self::SLUG ),
			'meta'  => array( 'class' => 'ole-ab-hq' ),
		) );
	}

	public static function orders_url( $status = '' ) {
		$hpos = class_exists( '\Automattic\WooCommerce\Utilities\OrderUtil' ) && \Automattic\WooCommerce\Utilities\OrderUtil::custom_orders_table_usage_is_enabled();
		$url  = $hpos ? admin_url( 'admin.php?page=wc-orders' ) : admin_url( 'edit.php?post_type=shop_order' );
		if ( $status ) {
			$url = add_query_arg( $hpos ? 'status' : 'post_status', 'wc-' . $status, $url );
		}
		return $url;
	}

	/** App-style tab bar on phones (hidden on desktop by CSS). */
	public static function bottom_nav() {
		if ( ! self::skin_on() || ! self::can_view() ) {
			return;
		}
		$screen  = get_current_screen();
		$id      = $screen ? $screen->id : '';
		$pending = function_exists( 'wc_orders_count' ) ? (int) wc_orders_count( 'processing' ) : 0;
		$tabs    = array(
			array( 'Home', admin_url( 'admin.php?page=' . self::SLUG ), 'toplevel_page_' . self::SLUG, 'M3 11.5 12 4l9 7.5V20a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1z' ),
			array( 'Orders', self::orders_url(), 'orders', 'M6 3h12l1 4H5zM5 8h14l-1 12a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1zm4 4v2a3 3 0 0 0 6 0v-2' ),
			array( 'Dispatch', admin_url( 'admin.php?page=ole-dispatch' ), 'toplevel_page_ole-dispatch', 'M12 2a7 7 0 0 0-7 7c0 5 7 13 7 13s7-8 7-13a7 7 0 0 0-7-7zm0 9.5A2.5 2.5 0 1 1 12 6.5a2.5 2.5 0 0 1 0 5z' ),
			array( 'Products', admin_url( 'edit.php?post_type=product' ), 'edit-product', 'M8 2h8v3l2 3v13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V8l2-3zm0 9h8v6H8z' ),
		);
		echo '<nav class="ole-tabbar" aria-label="App navigation">';
		foreach ( $tabs as $t ) {
			$active = ( $id === $t[2] ) || ( 'orders' === $t[2] && false !== strpos( $id, 'shop_order' ) ) || ( 'orders' === $t[2] && 'woocommerce_page_wc-orders' === $id );
			printf(
				'<a href="%1$s" class="%2$s"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="%3$s"/></svg><span>%4$s</span>%5$s</a>',
				esc_url( $t[1] ),
				$active ? 'is-active' : '',
				esc_attr( $t[3] ),
				esc_html( $t[0] ),
				( 'orders' === $t[2] && $pending ) ? '<b class="ole-tabbar__badge">' . esc_html( $pending ) . '</b>' : ''
			);
		}
		echo '<button type="button" class="ole-tabbar__more" onclick="var b=document.querySelector(\'#wp-admin-bar-menu-toggle a\');if(b){b.click();}"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h16"/></svg><span>Menu</span></button>';
		echo '</nav>';
	}

	/* ------------------------------------------------------------------ */
	/* Login screen                                                        */
	/* ------------------------------------------------------------------ */

	public static function login_assets() {
		if ( ! self::skin_on() ) {
			return;
		}
		wp_enqueue_style( 'ole-fonts', 'https://fonts.googleapis.com/css2?family=Archivo:wght@400;600;800;900&display=swap', array(), null );
		wp_enqueue_style( 'ole-login', OLE_DELIVERY_URL . 'assets/css/admin-skin.css', array(), OLE_DELIVERY_VERSION );
		wp_add_inline_style( 'ole-login', 'body.login h1 a{background-image:url(' . esc_url( OLE_DELIVERY_URL . 'assets/img/icon.svg' ) . ')!important;}' );
	}

	public static function login_url() {
		return home_url( '/' );
	}

	public static function login_text() {
		return 'One Love Energy';
	}

	/* ------------------------------------------------------------------ */
	/* Data                                                                */
	/* ------------------------------------------------------------------ */

	const PAID = array( 'processing', 'completed', 'on-hold' );

	public static function data() {
		$tz    = wp_timezone();
		$now   = new DateTimeImmutable( 'now', $tz );
		$today = $now->setTime( 0, 0 );
		$start = $today->modify( '-6 days' );

		$orders = wc_get_orders( array(
			'limit'        => 2000,
			'status'       => self::PAID,
			'date_created' => '>=' . $today->modify( '-7 days' )->getTimestamp(),
			'return'       => 'objects',
		) );

		$days = array();
		for ( $i = 0; $i < 7; $i++ ) {
			$d                        = $start->modify( "+{$i} days" );
			$days[ $d->format( 'Y-m-d' ) ] = array( 'label' => $d->format( 'D' ), 'date' => $d->format( 'j M' ), 'sales' => 0.0, 'orders' => 0 );
		}
		$yday_key  = $today->modify( '-1 day' )->format( 'Y-m-d' );
		$today_key = $today->format( 'Y-m-d' );
		$yday      = array( 'sales' => 0.0, 'orders' => 0 );
		$products  = array();

		foreach ( $orders as $o ) {
			if ( ! $o->get_date_created() ) {
				continue;
			}
			$key = $o->get_date_created()->setTimezone( $tz )->format( 'Y-m-d' );
			if ( isset( $days[ $key ] ) ) {
				$days[ $key ]['sales'] += (float) $o->get_total();
				$days[ $key ]['orders']++;
				foreach ( $o->get_items() as $item ) {
					$pid = $item->get_product_id();
					if ( ! isset( $products[ $pid ] ) ) {
						$products[ $pid ] = array( 'name' => $item->get_name(), 'qty' => 0, 'revenue' => 0.0 );
					}
					$products[ $pid ]['qty']     += $item->get_quantity();
					$products[ $pid ]['revenue'] += (float) $item->get_total();
				}
			}
			if ( $key === $yday_key ) {
				$yday['sales'] += (float) $o->get_total();
				$yday['orders']++;
			}
		}
		uasort( $products, function ( $a, $b ) {
			return $b['qty'] <=> $a['qty'];
		} );

		$t        = $days[ $today_key ];
		$week     = array_sum( wp_list_pluck( $days, 'sales' ) );
		$week_n   = array_sum( wp_list_pluck( $days, 'orders' ) );
		$riders   = OLE_Deliveries::riders();
		$on_duty  = 0;
		$cash     = 0.0;
		foreach ( $riders as $r ) {
			$on_duty += OLE_Deliveries::rider_online( $r->ID ) ? 1 : 0;
			$cash    += OLE_Deliveries::rider_cash( $r->ID );
		}
		$waiting = count( OLE_Deliveries::query( array( 'status' => array( 'pending', 'failed' ), 'limit' => 200 ) ) );
		$moving  = count( OLE_Deliveries::query( array( 'status' => OLE_Deliveries::ACTIVE, 'limit' => 200 ) ) );

		// Stock watch.
		$low_at = max( 1, (int) get_option( 'woocommerce_notify_low_stock_amount', 2 ) );
		$stock  = array();
		foreach ( wc_get_products( array( 'limit' => 50, 'status' => 'publish' ) ) as $p ) {
			if ( ! $p->is_in_stock() ) {
				$stock[] = array( 'product' => $p, 'label' => 'Out of stock', 'level' => 'out' );
			} elseif ( $p->managing_stock() && null !== $p->get_stock_quantity() && $p->get_stock_quantity() <= max( $low_at, 12 ) ) {
				$stock[] = array( 'product' => $p, 'label' => $p->get_stock_quantity() . ' left', 'level' => 'low' );
			}
		}

		return array(
			'now'       => $now,
			'days'      => $days,
			'today'     => $t,
			'yday'      => $yday,
			'week'      => $week,
			'week_n'    => $week_n,
			'aov'       => $week_n ? $week / $week_n : 0,
			'processing'=> function_exists( 'wc_orders_count' ) ? (int) wc_orders_count( 'processing' ) : 0,
			'waiting'   => $waiting,
			'moving'    => $moving,
			'on_duty'   => $on_duty,
			'riders'    => count( $riders ),
			'cash'      => $cash,
			'top'       => array_slice( $products, 0, 4, true ),
			'stock'     => $stock,
			'recent'    => wc_get_orders( array( 'limit' => 8, 'orderby' => 'date', 'order' => 'DESC', 'type' => 'shop_order', 'status' => array_keys( wc_get_order_statuses() ) ) ),
		);
	}

	/* ------------------------------------------------------------------ */
	/* Rendering                                                           */
	/* ------------------------------------------------------------------ */

	protected static function money( $n ) {
		return '₹' . number_format_i18n( (float) $n, 0 );
	}

	protected static function delta( $now, $before ) {
		if ( $before <= 0 ) {
			return $now > 0 ? array( 'New today', 'up' ) : array( 'No sales yet', 'flat' );
		}
		$pct = round( ( $now - $before ) / $before * 100 );
		return array( ( $pct >= 0 ? '▲ ' : '▼ ' ) . abs( $pct ) . '% vs yesterday', $pct >= 0 ? 'up' : 'down' );
	}

	protected static function greeting( DateTimeImmutable $now ) {
		$h = (int) $now->format( 'G' );
		if ( $h < 5 ) {
			return 'Burning the midnight oil';
		}
		if ( $h < 12 ) {
			return 'Good morning';
		}
		if ( $h < 17 ) {
			return 'Good afternoon';
		}
		return 'Good evening';
	}

	public static function page() {
		if ( ! self::can_view() ) {
			return;
		}
		$user = wp_get_current_user();
		$now  = new DateTimeImmutable( 'now', wp_timezone() );
		$soon = 'yes' === get_option( 'woocommerce_coming_soon' );
		?>
		<div class="wrap ole-hq">
			<h1 class="screen-reader-text">One Love HQ</h1>
			<header class="hq-hero">
				<div class="hq-hero__text">
					<p class="hq-hero__eyebrow"><?php echo esc_html( $now->format( 'l, j F' ) ); ?></p>
					<h2 class="hq-hero__title"><?php echo esc_html( self::greeting( $now ) ); ?>, <?php echo esc_html( $user->first_name ? $user->first_name : $user->display_name ); ?></h2>
					<p class="hq-hero__sub">Here's how One Love is doing right now.</p>
				</div>
				<div class="hq-hero__side">
					<span class="hq-status <?php echo $soon ? 'is-off' : 'is-on'; ?>"><i></i><?php echo $soon ? 'Store in Coming-soon mode' : 'Store is live'; ?></span>
					<a class="hq-hero__link" href="<?php echo esc_url( home_url( '/' ) ); ?>" target="_blank">View store ↗</a>
				</div>
				<div class="hq-hero__stripe" aria-hidden="true"><span></span><span></span><span></span></div>
			</header>


			<div id="ole-hq-live" aria-live="polite">
				<?php self::render_live(); ?>
			</div>


			<p class="hq-foot">Updates every minute · <a href="<?php echo esc_url( admin_url( 'index.php?classic=1' ) ); ?>">Classic WordPress dashboard</a></p>
		</div>
		<?php
	}

	/** App-icon grid of quick actions. */
	public static function render_apps() {
		?>
			<section class="hq-section hq-section--apps">
				<h3 class="hq-h">Quick actions</h3>
				<div class="hq-apps">
					<?php
					$apps = array(
						array( 'Orders', self::orders_url(), '#d92d20', 'M6 3h12l1 4H5zM5 8h14l-1 12a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1zm4 4v2a3 3 0 0 0 6 0v-2' ),
						array( 'Dispatch', admin_url( 'admin.php?page=ole-dispatch' ), '#1a73e8', 'M12 2a7 7 0 0 0-7 7c0 5 7 13 7 13s7-8 7-13a7 7 0 0 0-7-7zm0 9.5A2.5 2.5 0 1 1 12 6.5a2.5 2.5 0 0 1 0 5z' ),
						array( 'Add product', admin_url( 'post-new.php?post_type=product' ), '#128a3f', 'M12 5v14M5 12h14' ),
						array( 'Products', admin_url( 'edit.php?post_type=product' ), '#f2b705', 'M8 2h8v3l2 3v13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V8l2-3zm0 9h8v6H8z' ),
						array( 'Riders', admin_url( 'users.php?role=ole_rider' ), '#7c3aed', 'M5 17a3 3 0 1 0 0 .01M19 17a3 3 0 1 0 0 .01M8 17h6l3-6h-4l-2-4H8' ),
						array( 'Reports', admin_url( 'admin.php?page=ole-reports' ), '#0e7490', 'M4 20V10M10 20V4M16 20v-7M22 20H2' ),
						array( 'Coupons', admin_url( 'edit.php?post_type=shop_coupon' ), '#db2777', 'M3 7h18v4a2 2 0 0 0 0 4v4H3v-4a2 2 0 0 0 0-4zM10 7v12' ),
						array( 'Customers', admin_url( 'admin.php?page=wc-admin&path=/customers' ), '#ea580c', 'M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8zM2 21a7 7 0 0 1 14 0M17 11a3 3 0 1 0 0-6M22 21a6 6 0 0 0-4-5.6' ),
						array( 'Delivery settings', admin_url( 'admin.php?page=ole-settings' ), '#52525b', 'M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6zM19.4 15a1.7 1.7 0 0 0 .3 1.8l.1.1a2 2 0 1 1-2.8 2.8l-.1-.1a1.7 1.7 0 0 0-1.8-.3 1.7 1.7 0 0 0-1 1.5V21a2 2 0 1 1-4 0v-.1a1.7 1.7 0 0 0-1.1-1.5 1.7 1.7 0 0 0-1.8.3l-.1.1a2 2 0 1 1-2.8-2.8l.1-.1a1.7 1.7 0 0 0 .3-1.8 1.7 1.7 0 0 0-1.5-1H3a2 2 0 1 1 0-4h.1a1.7 1.7 0 0 0 1.5-1.1 1.7 1.7 0 0 0-.3-1.8l-.1-.1a2 2 0 1 1 2.8-2.8l.1.1a1.7 1.7 0 0 0 1.8.3H9a1.7 1.7 0 0 0 1-1.5V3a2 2 0 1 1 4 0v.1a1.7 1.7 0 0 0 1 1.5 1.7 1.7 0 0 0 1.8-.3l.1-.1a2 2 0 1 1 2.8 2.8l-.1.1a1.7 1.7 0 0 0-.3 1.8V9a1.7 1.7 0 0 0 1.5 1H21a2 2 0 1 1 0 4h-.1a1.7 1.7 0 0 0-1.5 1z' ),
						array( 'Rider app', home_url( '/rider/' ), '#0b0b0b', 'M7 2h10a1 1 0 0 1 1 1v18a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V3a1 1 0 0 1 1-1zm4 17h2' ),
					);
					foreach ( $apps as $a ) {
						printf(
							'<a class="hq-app" href="%1$s"%5$s><span class="hq-app__icon" style="background:%2$s"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="%3$s"/></svg></span><span class="hq-app__label">%4$s</span></a>',
							esc_url( $a[1] ),
							esc_attr( $a[2] ),
							esc_attr( $a[3] ),
							esc_html( $a[0] ),
							'Rider app' === $a[0] ? ' target="_blank"' : ''
						);
					}
					?>
				</div>
			</section>
		<?php
	}

	public static function ajax_live() {
		check_ajax_referer( 'ole_hq_live', 'nonce' );
		if ( ! self::can_view() ) {
			wp_send_json_error( null, 403 );
		}
		ob_start();
		self::render_live();
		wp_send_json_success( array( 'html' => ob_get_clean() ) );
	}

	/** Everything that refreshes every minute. */
	public static function render_live() {
		$d       = self::data();
		$delta   = self::delta( $d['today']['sales'], $d['yday']['sales'] );
		$max     = max( 1, max( wp_list_pluck( $d['days'], 'sales' ) ) );
		$keys    = array_keys( $d['days'] );
		$today_k = end( $keys );
		?>
		<section class="hq-kpis" aria-label="Today at a glance">
			<a class="hq-kpi hq-kpi--hero" href="<?php echo esc_url( self::orders_url() ); ?>">
				<span class="hq-kpi__label">Today's sales</span>
				<b class="hq-kpi__value"><?php echo esc_html( self::money( $d['today']['sales'] ) ); ?></b>
				<span class="hq-kpi__delta is-<?php echo esc_attr( $delta[1] ); ?>"><?php echo esc_html( $delta[0] ); ?></span>
			</a>
			<a class="hq-kpi" href="<?php echo esc_url( self::orders_url() ); ?>">
				<span class="hq-kpi__label">Orders today</span>
				<b class="hq-kpi__value"><?php echo esc_html( $d['today']['orders'] ); ?></b>
				<span class="hq-kpi__sub"><?php echo esc_html( $d['yday']['orders'] ); ?> yesterday</span>
			</a>
			<a class="hq-kpi <?php echo $d['processing'] ? 'is-alert' : ''; ?>" href="<?php echo esc_url( self::orders_url( 'processing' ) ); ?>">
				<span class="hq-kpi__label">To deliver</span>
				<b class="hq-kpi__value"><?php echo esc_html( $d['processing'] ); ?></b>
				<span class="hq-kpi__sub"><?php echo esc_html( $d['moving'] ); ?> on the road · <?php echo esc_html( $d['waiting'] ); ?> waiting</span>
			</a>
			<a class="hq-kpi <?php echo ( $d['riders'] && ! $d['on_duty'] ) ? 'is-alert' : ''; ?>" href="<?php echo esc_url( admin_url( 'admin.php?page=ole-dispatch' ) ); ?>">
				<span class="hq-kpi__label">Riders on duty</span>
				<b class="hq-kpi__value"><?php echo esc_html( $d['on_duty'] ); ?><small>/<?php echo esc_html( $d['riders'] ); ?></small></b>
				<span class="hq-kpi__sub"><?php echo esc_html( self::money( $d['cash'] ) ); ?> cash with riders</span>
			</a>
			<a class="hq-kpi" href="<?php echo esc_url( admin_url( 'admin.php?page=ole-reports&range=7d' ) ); ?>">
				<span class="hq-kpi__label">Avg order · 7 days</span>
				<b class="hq-kpi__value"><?php echo esc_html( self::money( $d['aov'] ) ); ?></b>
				<span class="hq-kpi__sub"><?php echo esc_html( $d['week_n'] ); ?> orders · <?php echo esc_html( self::money( $d['week'] ) ); ?></span>
			</a>
		</section>

		<?php self::render_apps(); ?>

		<div class="hq-grid">
			<section class="hq-card hq-chart">
				<div class="hq-card__head">
					<div>
						<h3 class="hq-h">Sales · last 7 days</h3>
						<p class="hq-muted"><?php echo esc_html( self::money( $d['week'] ) ); ?> from <?php echo esc_html( $d['week_n'] ); ?> orders</p>
					</div>
				</div>
				<?php
				// Single series column chart: ≤24px columns, 4px rounded tops, hairline grid, selective labels.
				$w      = 700;
				$h      = 220;
				$pad_l  = 44;
				$pad_b  = 28;
				$pad_t  = 22;
				$plot_h = $h - $pad_b - $pad_t;
				$slot   = ( $w - $pad_l ) / 7;
				$step   = self::nice_step( $max );
				$top    = max( $step, ceil( $max / $step ) * $step );
				$best   = array_search( max( wp_list_pluck( $d['days'], 'sales' ) ), wp_list_pluck( $d['days'], 'sales' ), true );
				?>
				<svg class="hq-bars" viewBox="0 0 <?php echo (int) $w; ?> <?php echo (int) $h; ?>" role="img" aria-label="Daily sales for the last 7 days">
					<?php for ( $v = 0; $v <= $top; $v += $step ) : $y = $pad_t + $plot_h - ( $v / $top ) * $plot_h; ?>
						<line x1="<?php echo (int) $pad_l; ?>" x2="<?php echo (int) $w; ?>" y1="<?php echo esc_attr( round( $y, 1 ) ); ?>" y2="<?php echo esc_attr( round( $y, 1 ) ); ?>" class="hq-bars__grid"/>
						<text x="<?php echo (int) ( $pad_l - 8 ); ?>" y="<?php echo esc_attr( round( $y + 4, 1 ) ); ?>" class="hq-bars__tick" text-anchor="end"><?php echo esc_html( self::compact( $v ) ); ?></text>
					<?php endfor; ?>
					<?php
					$i = 0;
					foreach ( $d['days'] as $key => $day ) :
						$bh  = $day['sales'] > 0 ? max( 3, ( $day['sales'] / $top ) * $plot_h ) : 0;
						$bw  = 24;
						$x   = $pad_l + $slot * $i + ( $slot - $bw ) / 2;
						$y   = $pad_t + $plot_h - $bh;
						$r   = min( 4, $bh );
						$lbl = ( $key === $today_k || $key === $best ) && $day['sales'] > 0;
						?>
						<g class="hq-bars__col<?php echo $key === $today_k ? ' is-today' : ''; ?>" tabindex="0" data-tip="<?php echo esc_attr( $day['date'] . ' · ' . self::money( $day['sales'] ) . ' · ' . $day['orders'] . ' order' . ( 1 === $day['orders'] ? '' : 's' ) ); ?>">
							<rect class="hq-bars__hit" x="<?php echo esc_attr( round( $pad_l + $slot * $i, 1 ) ); ?>" y="<?php echo (int) $pad_t; ?>" width="<?php echo esc_attr( round( $slot, 1 ) ); ?>" height="<?php echo (int) $plot_h; ?>"/>
							<?php if ( $bh > 0 ) : ?>
								<path class="hq-bars__bar" d="<?php echo esc_attr( sprintf( 'M%1$.1f %2$.1fv%3$.1fa%4$.1f %4$.1f 0 0 1 %4$.1f -%4$.1fh%5$.1fa%4$.1f %4$.1f 0 0 1 %4$.1f %4$.1fv%6$.1fz', $x, $y + $bh, -( $bh - $r ), $r, $bw - 2 * $r, $bh - $r ) ); ?>"/>
							<?php endif; ?>
							<?php if ( $lbl ) : ?>
								<text x="<?php echo esc_attr( round( $x + $bw / 2, 1 ) ); ?>" y="<?php echo esc_attr( round( $y - 6, 1 ) ); ?>" text-anchor="middle" class="hq-bars__value"><?php echo esc_html( self::money( $day['sales'] ) ); ?></text>
							<?php endif; ?>
							<text x="<?php echo esc_attr( round( $x + $bw / 2, 1 ) ); ?>" y="<?php echo (int) ( $h - 8 ); ?>" text-anchor="middle" class="hq-bars__day"><?php echo esc_html( $key === $today_k ? 'Today' : $day['label'] ); ?></text>
						</g>
						<?php
						$i++;
					endforeach;
					?>
				</svg>
				<div class="hq-tip" role="status" hidden></div>
				<details class="hq-table">
					<summary>View as table</summary>
					<table>
						<thead><tr><th>Day</th><th>Orders</th><th>Sales</th></tr></thead>
						<tbody>
							<?php foreach ( $d['days'] as $day ) : ?>
								<tr><td><?php echo esc_html( $day['label'] . ' ' . $day['date'] ); ?></td><td><?php echo esc_html( $day['orders'] ); ?></td><td><?php echo esc_html( self::money( $day['sales'] ) ); ?></td></tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</details>
			</section>

			<section class="hq-card hq-feed">
				<div class="hq-card__head">
					<h3 class="hq-h">Latest orders</h3>
					<a class="hq-more" href="<?php echo esc_url( self::orders_url() ); ?>">See all</a>
				</div>
				<?php if ( ! $d['recent'] ) : ?>
					<p class="hq-empty">No orders yet — your first one will pop up here.</p>
				<?php endif; ?>
				<ul class="hq-orders">
					<?php
					foreach ( $d['recent'] as $o ) :
						$first = null;
						$count = 0;
						foreach ( $o->get_items() as $item ) {
							$count += $item->get_quantity();
							if ( ! $first ) {
								$first = $item->get_product();
							}
						}
						$status = $o->get_status();
						$del    = OLE_Deliveries::get_by_order( $o->get_id() );
						$ago    = $o->get_date_created() ? human_time_diff( $o->get_date_created()->getTimestamp() ) . ' ago' : '';
						$name   = trim( $o->get_formatted_billing_full_name() );
						?>
						<li>
							<a class="hq-order" href="<?php echo esc_url( $o->get_edit_order_url() ); ?>">
								<span class="hq-order__img"><?php echo $first ? wp_kses_post( $first->get_image( 'thumbnail' ) ) : ''; ?></span>
								<span class="hq-order__main">
									<b>#<?php echo esc_html( $o->get_order_number() ); ?> · <?php echo esc_html( $name ? $name : 'Guest' ); ?></b>
									<small><?php echo esc_html( implode( ' · ', array_filter( array( $count . ' item' . ( 1 === $count ? '' : 's' ), $o->get_payment_method_title(), $ago ) ) ) ); ?></small>
								</span>
								<span class="hq-order__side">
									<b><?php echo esc_html( self::money( $o->get_total() ) ); ?></b>
									<span class="hq-chip hq-chip--<?php echo esc_attr( $del && 'processing' === $status ? $del->status : $status ); ?>"><?php echo esc_html( $del && 'processing' === $status ? ( OLE_Deliveries::labels()[ $del->status ] ?? $del->status ) : wc_get_order_status_name( $status ) ); ?></span>
								</span>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			</section>

			<section class="hq-card hq-top">
				<div class="hq-card__head"><h3 class="hq-h">Bestsellers · 7 days</h3></div>
				<?php if ( ! $d['top'] ) : ?>
					<p class="hq-empty">Sales will rank your products here.</p>
				<?php endif; ?>
				<ol class="hq-rank">
					<?php
					$rank = 1;
					$maxq = $d['top'] ? max( wp_list_pluck( $d['top'], 'qty' ) ) : 1;
					foreach ( $d['top'] as $pid => $p ) :
						$prod = wc_get_product( $pid );
						?>
						<li>
							<span class="hq-rank__n"><?php echo (int) $rank++; ?></span>
							<span class="hq-rank__img"><?php echo $prod ? wp_kses_post( $prod->get_image( 'thumbnail' ) ) : ''; ?></span>
							<span class="hq-rank__main">
								<b><?php echo esc_html( $p['name'] ); ?></b>
								<span class="hq-rank__bar"><i style="width:<?php echo (int) round( 100 * $p['qty'] / max( 1, $maxq ) ); ?>%"></i></span>
							</span>
							<span class="hq-rank__qty"><?php echo esc_html( $p['qty'] ); ?> sold</span>
						</li>
					<?php endforeach; ?>
				</ol>
			</section>

			<section class="hq-card hq-stock">
				<div class="hq-card__head"><h3 class="hq-h">Stock watch</h3></div>
				<?php if ( ! $d['stock'] ) : ?>
					<p class="hq-ok"><span aria-hidden="true">✓</span> All products in stock.</p>
				<?php else : ?>
					<ul class="hq-stocklist">
						<?php foreach ( $d['stock'] as $s ) : ?>
							<li>
								<a href="<?php echo esc_url( get_edit_post_link( $s['product']->get_id() ) ); ?>">
									<span><?php echo esc_html( $s['product']->get_name() ); ?></span>
									<span class="hq-chip hq-chip--<?php echo esc_attr( 'out' === $s['level'] ? 'failed' : 'assigned' ); ?>"><?php echo esc_html( $s['label'] ); ?></span>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</section>
		</div>
		<?php
	}

	protected static function nice_step( $max ) {
		$raw  = $max / 4;
		$mag  = pow( 10, floor( log10( max( 1, $raw ) ) ) );
		$norm = $raw / $mag;
		$nice = $norm <= 1 ? 1 : ( $norm <= 2 ? 2 : ( $norm <= 5 ? 5 : 10 ) );
		return max( 100, $nice * $mag );
	}

	protected static function compact( $v ) {
		if ( $v >= 100000 ) {
			return '₹' . round( $v / 100000, 1 ) . 'L';
		}
		if ( $v >= 1000 ) {
			return '₹' . round( $v / 1000, 1 ) . 'K';
		}
		return '₹' . (int) $v;
	}
}
