<?php
/**
 * Deliveries → Reports: speed, rider performance, payouts, cash and delivery-fee margin,
 * with CSV export for payroll.
 *
 * @package OneLoveDelivery
 */

defined( 'ABSPATH' ) || exit;

class OLE_Reports {

	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'menu' ), 20 );
		add_action( 'admin_post_ole_report_csv', array( __CLASS__, 'csv' ) );
	}

	public static function menu() {
		add_submenu_page( 'ole-dispatch', 'Delivery Reports', 'Reports', 'manage_woocommerce', 'ole-reports', array( __CLASS__, 'page' ) );
	}

	public static function ranges() {
		return array(
			'today'      => 'Today',
			'yesterday'  => 'Yesterday',
			'7d'         => 'Last 7 days',
			'this_month' => 'This month',
			'last_month' => 'Last month',
			'custom'     => 'Custom',
		);
	}

	/**
	 * Resolve the requested range to local dates and UTC bounds.
	 *
	 * @return array{key:string, from:string, to:string, from_utc:string, to_utc:string, label:string}
	 */
	public static function range_from_request() {
		// phpcs:disable WordPress.Security.NonceVerification -- read-only report filters.
		$key  = isset( $_GET['range'] ) ? sanitize_key( $_GET['range'] ) : '7d';
		$key  = isset( self::ranges()[ $key ] ) ? $key : '7d';
		$tz   = wp_timezone();
		$now  = new DateTimeImmutable( 'now', $tz );
		$from = $now->setTime( 0, 0 );
		$to   = $now->setTime( 0, 0 )->modify( '+1 day' );

		switch ( $key ) {
			case 'yesterday':
				$from = $from->modify( '-1 day' );
				$to   = $to->modify( '-1 day' );
				break;
			case '7d':
				$from = $from->modify( '-6 days' );
				break;
			case 'this_month':
				$from = $from->modify( 'first day of this month' );
				break;
			case 'last_month':
				$from = $from->modify( 'first day of last month' );
				$to   = $now->setTime( 0, 0 )->modify( 'first day of this month' );
				break;
			case 'custom':
				$f = isset( $_GET['from'] ) ? sanitize_text_field( wp_unslash( $_GET['from'] ) ) : '';
				$t = isset( $_GET['to'] ) ? sanitize_text_field( wp_unslash( $_GET['to'] ) ) : '';
				if ( preg_match( '/^\d{4}-\d{2}-\d{2}$/', $f ) && preg_match( '/^\d{4}-\d{2}-\d{2}$/', $t ) ) {
					$from = new DateTimeImmutable( $f . ' 00:00:00', $tz );
					$to   = ( new DateTimeImmutable( $t . ' 00:00:00', $tz ) )->modify( '+1 day' );
					if ( $to <= $from ) {
						$to = $from->modify( '+1 day' );
					}
				}
				break;
		}
		// phpcs:enable
		$utc = new DateTimeZone( 'UTC' );
		return array(
			'key'      => $key,
			'from'     => $from->format( 'Y-m-d' ),
			'to'       => $to->modify( '-1 day' )->format( 'Y-m-d' ),
			'from_utc' => $from->setTimezone( $utc )->format( 'Y-m-d H:i:s' ),
			'to_utc'   => $to->setTimezone( $utc )->format( 'Y-m-d H:i:s' ),
			'label'    => $from->format( 'j M Y' ) . ( $to->modify( '-1 day' )->format( 'Y-m-d' ) !== $from->format( 'Y-m-d' ) ? ' – ' . $to->modify( '-1 day' )->format( 'j M Y' ) : '' ),
		);
	}

	/** Delivered + failed deliveries that finished inside the range. */
	public static function rows( $range ) {
		global $wpdb;
		$table = OLE_Deliveries::table();
		return $wpdb->get_results( $wpdb->prepare(
			"SELECT * FROM {$table} WHERE ( status = 'delivered' AND delivered_at >= %s AND delivered_at < %s ) OR ( status = 'failed' AND updated_at >= %s AND updated_at < %s ) ORDER BY id ASC", // phpcs:ignore WordPress.DB.PreparedSQL
			$range['from_utc'], $range['to_utc'], $range['from_utc'], $range['to_utc']
		) );
	}

	protected static function minutes( $from, $to ) {
		if ( ! $from || ! $to ) {
			return null;
		}
		return max( 0, ( strtotime( $to . ' UTC' ) - strtotime( $from . ' UTC' ) ) / 60 );
	}

	/** Aggregate rows into totals + per-rider stats. */
	public static function summarise( array $rows ) {
		$target = (float) OLE_Settings::get( 'ontime_minutes' );
		$blank  = array(
			'delivered' => 0, 'failed' => 0, 'km' => 0.0, 'pay' => 0.0, 'cod' => 0.0, 'cod_pending' => 0.0,
			'fees' => 0.0, 'door' => array(), 'ride' => array(), 'ontime' => 0,
		);
		$total  = $blank;
		$riders = array();

		foreach ( $rows as $d ) {
			$rid = (int) $d->rider_id;
			if ( ! isset( $riders[ $rid ] ) ) {
				$riders[ $rid ] = $blank;
			}
			foreach ( array( 'total', 'rider' ) as $bucket ) {
				$t = &$total;
				if ( 'rider' === $bucket ) {
					$t = &$riders[ $rid ];
				}
				if ( 'failed' === $d->status ) {
					$t['failed']++;
					unset( $t );
					continue;
				}
				$t['delivered']++;
				$t['km']  += null !== $d->distance_km ? (float) $d->distance_km : (float) OLE_Settings::get( 'pay_fallback_km' );
				$t['pay'] += null !== $d->rider_pay ? (float) $d->rider_pay : OLE_Deliveries::pay_for( $d );
				if ( $d->is_cod ) {
					$t['cod'] += (float) $d->cod_collected;
					if ( ! $d->cod_settled ) {
						$t['cod_pending'] += (float) $d->cod_collected;
					}
				}
				$t['fees'] += OLE_Fees::order_fee( $d->order_id );
				$door = self::minutes( $d->created_at, $d->delivered_at );
				$ride = self::minutes( $d->picked_at, $d->delivered_at );
				if ( null !== $door ) {
					$t['door'][] = $door;
					if ( $target > 0 && $door <= $target ) {
						$t['ontime']++;
					}
				}
				if ( null !== $ride ) {
					$t['ride'][] = $ride;
				}
				unset( $t );
			}
		}

		$finish = function ( $s ) {
			$s['avg_door']   = $s['door'] ? round( array_sum( $s['door'] ) / count( $s['door'] ) ) : null;
			$s['avg_ride']   = $s['ride'] ? round( array_sum( $s['ride'] ) / count( $s['ride'] ) ) : null;
			$s['ontime_pct'] = $s['door'] ? round( 100 * $s['ontime'] / count( $s['door'] ) ) : null;
			$attempts        = $s['delivered'] + $s['failed'];
			$s['success']    = $attempts ? round( 100 * $s['delivered'] / $attempts ) : null;
			unset( $s['door'], $s['ride'] );
			return $s;
		};

		$out = array();
		foreach ( $riders as $rid => $s ) {
			$u          = $rid ? get_userdata( $rid ) : null;
			$s          = $finish( $s );
			$s['id']    = $rid;
			$s['name']  = $u ? $u->display_name : ( $rid ? 'Deleted rider #' . $rid : 'Unassigned' );
			$out[]      = $s;
		}
		usort( $out, function ( $a, $b ) {
			return $b['delivered'] <=> $a['delivered'];
		} );

		return array( 'total' => $finish( $total ), 'riders' => $out );
	}

	protected static function rupees( $n ) {
		return '₹' . number_format( (float) $n, 0 );
	}

	public static function page() {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}
		$range = self::range_from_request();
		$data  = self::summarise( self::rows( $range ) );
		$t     = $data['total'];
		$csv   = wp_nonce_url( add_query_arg( array(
			'action' => 'ole_report_csv',
			'range'  => $range['key'],
			'from'   => $range['from'],
			'to'     => $range['to'],
		), admin_url( 'admin-post.php' ) ), 'ole_report_csv' );
		$fmt_min = function ( $m ) {
			return null === $m ? '—' : $m . ' min';
		};
		$fmt_pct = function ( $p ) {
			return null === $p ? '—' : $p . '%';
		};
		?>
		<div class="wrap ole-reports">
			<style>
				.ole-reports .ole-r-bar{display:flex;flex-wrap:wrap;gap:8px;align-items:center;margin:14px 0 18px}
				.ole-reports .ole-r-bar a.button.is-on{background:#0b0b0b;color:#fff;border-color:#0b0b0b}
				.ole-reports .ole-r-kpis{display:grid;grid-template-columns:repeat(auto-fit,minmax(170px,1fr));gap:12px;margin-bottom:20px}
				.ole-reports .ole-r-kpi{background:#fff;border:1px solid #e5e1d6;border-left:5px solid #0b0b0b;border-radius:10px;padding:12px 14px}
				.ole-reports .ole-r-kpi b{display:block;font-size:24px;line-height:1.1}
				.ole-reports .ole-r-kpi span{font-size:12px;color:#5c5a54;text-transform:uppercase;letter-spacing:.04em}
				.ole-reports .ole-r-kpi small{display:block;color:#5c5a54;margin-top:2px}
				.ole-reports .k-green{border-left-color:#128a3f}.ole-reports .k-gold{border-left-color:#f2b705}.ole-reports .k-red{border-left-color:#d92d20}.ole-reports .k-blue{border-left-color:#1a73e8}
				.ole-reports table.widefat td,.ole-reports table.widefat th{vertical-align:middle}
				.ole-reports td.num,.ole-reports th.num{text-align:right}
				.ole-reports tfoot th{font-weight:800}
				.ole-reports .ole-bar{display:inline-block;height:8px;border-radius:8px;background:#128a3f;vertical-align:middle;margin-right:6px}
				.ole-reports .ole-bad{color:#d92d20;font-weight:700}
			</style>

			<h1 class="wp-heading-inline">Delivery Reports</h1>
			<a class="page-title-action" href="<?php echo esc_url( $csv ); ?>">Download CSV (payroll)</a>
			<p><?php echo esc_html( $range['label'] ); ?> · on-time target: <?php echo esc_html( OLE_Settings::get( 'ontime_minutes' ) ); ?> min from order to door</p>

			<form class="ole-r-bar" method="get">
				<input type="hidden" name="page" value="ole-reports">
				<?php foreach ( self::ranges() as $key => $label ) : ?>
					<?php if ( 'custom' === $key ) { continue; } ?>
					<a class="button<?php echo $range['key'] === $key ? ' is-on' : ''; ?>" href="<?php echo esc_url( add_query_arg( array( 'page' => 'ole-reports', 'range' => $key ), admin_url( 'admin.php' ) ) ); ?>"><?php echo esc_html( $label ); ?></a>
				<?php endforeach; ?>
				<span style="margin-left:8px">Custom:</span>
				<input type="hidden" name="range" value="custom">
				<input type="date" name="from" value="<?php echo esc_attr( $range['from'] ); ?>">
				<input type="date" name="to" value="<?php echo esc_attr( $range['to'] ); ?>">
				<button class="button">Show</button>
			</form>

			<div class="ole-r-kpis">
				<div class="ole-r-kpi k-green"><b><?php echo esc_html( $t['delivered'] ); ?></b><span>Delivered</span><small><?php echo esc_html( $t['failed'] ); ?> failed · <?php echo esc_html( $fmt_pct( $t['success'] ) ); ?> success</small></div>
				<div class="ole-r-kpi k-blue"><b><?php echo esc_html( $fmt_min( $t['avg_door'] ) ); ?></b><span>Avg order → door</span><small>Ride only: <?php echo esc_html( $fmt_min( $t['avg_ride'] ) ); ?></small></div>
				<div class="ole-r-kpi k-gold"><b><?php echo esc_html( $fmt_pct( $t['ontime_pct'] ) ); ?></b><span>On time</span><small>within <?php echo esc_html( OLE_Settings::get( 'ontime_minutes' ) ); ?> min</small></div>
				<div class="ole-r-kpi"><b><?php echo esc_html( number_format( $t['km'], 1 ) ); ?> km</b><span>Distance ridden</span><small>road estimate</small></div>
				<div class="ole-r-kpi k-red"><b><?php echo esc_html( self::rupees( $t['pay'] ) ); ?></b><span>Rider payout</span><small><?php echo esc_html( $t['delivered'] ? self::rupees( $t['pay'] / $t['delivered'] ) . ' per order' : '—' ); ?></small></div>
				<div class="ole-r-kpi k-green"><b><?php echo esc_html( self::rupees( $t['fees'] ) ); ?></b><span>Delivery fees charged</span><small>Net delivery cost: <?php echo esc_html( self::rupees( $t['pay'] - $t['fees'] ) ); ?></small></div>
				<div class="ole-r-kpi k-gold"><b><?php echo esc_html( self::rupees( $t['cod'] ) ); ?></b><span>COD collected</span><small class="<?php echo $t['cod_pending'] > 0 ? 'ole-bad' : ''; ?>"><?php echo esc_html( self::rupees( $t['cod_pending'] ) ); ?> still with riders</small></div>
			</div>

			<h2>Riders</h2>
			<table class="widefat striped">
				<thead>
					<tr>
						<th>Rider</th>
						<th class="num">Delivered</th>
						<th class="num">Failed</th>
						<th class="num">Success</th>
						<th class="num">Avg order → door</th>
						<th class="num">Avg ride</th>
						<th class="num">On time</th>
						<th class="num">Km</th>
						<th class="num">COD collected</th>
						<th class="num">Cash pending</th>
						<th class="num">Payout</th>
					</tr>
				</thead>
				<tbody>
					<?php if ( ! $data['riders'] ) : ?>
						<tr><td colspan="11">No finished deliveries in this period.</td></tr>
					<?php endif; ?>
					<?php
					$max = 1;
					foreach ( $data['riders'] as $r ) {
						$max = max( $max, $r['delivered'] );
					}
					foreach ( $data['riders'] as $r ) :
						?>
						<tr>
							<td><b><?php echo esc_html( $r['name'] ); ?></b></td>
							<td class="num"><span class="ole-bar" style="width:<?php echo (int) round( 60 * $r['delivered'] / $max ); ?>px"></span><?php echo esc_html( $r['delivered'] ); ?></td>
							<td class="num<?php echo $r['failed'] ? ' ole-bad' : ''; ?>"><?php echo esc_html( $r['failed'] ); ?></td>
							<td class="num"><?php echo esc_html( $fmt_pct( $r['success'] ) ); ?></td>
							<td class="num"><?php echo esc_html( $fmt_min( $r['avg_door'] ) ); ?></td>
							<td class="num"><?php echo esc_html( $fmt_min( $r['avg_ride'] ) ); ?></td>
							<td class="num"><?php echo esc_html( $fmt_pct( $r['ontime_pct'] ) ); ?></td>
							<td class="num"><?php echo esc_html( number_format( $r['km'], 1 ) ); ?></td>
							<td class="num"><?php echo esc_html( self::rupees( $r['cod'] ) ); ?></td>
							<td class="num<?php echo $r['cod_pending'] > 0 ? ' ole-bad' : ''; ?>"><?php echo esc_html( self::rupees( $r['cod_pending'] ) ); ?></td>
							<td class="num"><b><?php echo esc_html( self::rupees( $r['pay'] ) ); ?></b></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
				<?php if ( $data['riders'] ) : ?>
					<tfoot>
						<tr>
							<th>Total</th>
							<th class="num"><?php echo esc_html( $t['delivered'] ); ?></th>
							<th class="num"><?php echo esc_html( $t['failed'] ); ?></th>
							<th class="num"><?php echo esc_html( $fmt_pct( $t['success'] ) ); ?></th>
							<th class="num"><?php echo esc_html( $fmt_min( $t['avg_door'] ) ); ?></th>
							<th class="num"><?php echo esc_html( $fmt_min( $t['avg_ride'] ) ); ?></th>
							<th class="num"><?php echo esc_html( $fmt_pct( $t['ontime_pct'] ) ); ?></th>
							<th class="num"><?php echo esc_html( number_format( $t['km'], 1 ) ); ?></th>
							<th class="num"><?php echo esc_html( self::rupees( $t['cod'] ) ); ?></th>
							<th class="num"><?php echo esc_html( self::rupees( $t['cod_pending'] ) ); ?></th>
							<th class="num"><?php echo esc_html( self::rupees( $t['pay'] ) ); ?></th>
						</tr>
					</tfoot>
				<?php endif; ?>
			</table>
			<p class="description">Payout = ₹<?php echo esc_html( OLE_Settings::get( 'pay_per_delivery' ) ); ?> per delivered order + ₹<?php echo esc_html( OLE_Settings::get( 'pay_per_km' ) ); ?> per km. Change it in <a href="<?php echo esc_url( admin_url( 'admin.php?page=ole-settings' ) ); ?>">Settings</a>. Mark cash as received on the Dispatch board.</p>
		</div>
		<?php
	}

	/** One row per finished delivery — ready for payroll in Excel / Google Sheets. */
	public static function csv() {
		if ( ! current_user_can( 'manage_woocommerce' ) || ! check_admin_referer( 'ole_report_csv' ) ) {
			wp_die( 'Not allowed.' );
		}
		$range = self::range_from_request();
		$rows  = self::rows( $range );
		$tz    = wp_timezone();
		$local = function ( $utc ) use ( $tz ) {
			return $utc ? ( new DateTimeImmutable( $utc, new DateTimeZone( 'UTC' ) ) )->setTimezone( $tz )->format( 'Y-m-d H:i' ) : '';
		};

		nocache_headers();
		header( 'Content-Type: text/csv; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename="deliveries-' . $range['from'] . '-to-' . $range['to'] . '.csv"' );
		$out = fopen( 'php://output', 'w' );
		fwrite( $out, "\xEF\xBB\xBF" ); // Excel-friendly UTF-8.
		fputcsv( $out, array( 'Order', 'Status', 'Rider', 'Customer', 'Ordered', 'Picked up', 'Delivered', 'Order to door (min)', 'Km', 'Payment', 'COD collected', 'Cash settled', 'Delivery fee', 'Rider payout', 'Problem' ) );
		foreach ( $rows as $d ) {
			$order = wc_get_order( $d->order_id );
			$rider = $d->rider_id ? get_userdata( $d->rider_id ) : null;
			$door  = self::minutes( $d->created_at, $d->delivered_at );
			fputcsv( $out, array(
				$order ? $order->get_order_number() : $d->order_id,
				$d->status,
				$rider ? $rider->display_name : '',
				$order ? trim( $order->get_formatted_billing_full_name() ) : '',
				$local( $d->created_at ),
				$local( $d->picked_at ),
				$local( $d->delivered_at ),
				null === $door ? '' : round( $door ),
				null === $d->distance_km ? '' : (float) $d->distance_km,
				$d->is_cod ? 'COD' : 'Prepaid',
				$d->is_cod ? (float) $d->cod_collected : '',
				$d->is_cod ? ( $d->cod_settled ? 'yes' : 'no' ) : '',
				OLE_Fees::order_fee( $d->order_id ),
				'delivered' === $d->status ? ( null !== $d->rider_pay ? (float) $d->rider_pay : OLE_Deliveries::pay_for( $d ) ) : 0,
				$d->fail_reason,
			) );
		}
		fclose( $out ); // phpcs:ignore WordPress.WP.AlternativeFunctions
		exit;
	}
}
