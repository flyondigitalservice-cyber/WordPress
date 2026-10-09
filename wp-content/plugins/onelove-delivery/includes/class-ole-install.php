<?php
/**
 * Activation: database table, rider role, rewrite rules.
 *
 * @package OneLoveDelivery
 */

defined( 'ABSPATH' ) || exit;

class OLE_Install {

	const DB_VERSION = '3';

	public static function activate() {
		self::create_table();
		self::add_role();
		OLE_Frontend::add_rewrites();
		flush_rewrite_rules();
		update_option( 'ole_delivery_db_version', self::DB_VERSION );
	}

	public static function deactivate() {
		if ( function_exists( 'as_unschedule_all_actions' ) ) {
			as_unschedule_all_actions( 'ole_delivery_assign_pending' );
		}
		flush_rewrite_rules();
	}

	public static function maybe_upgrade() {
		if ( get_option( 'ole_delivery_db_version' ) !== self::DB_VERSION ) {
			self::create_table();
			self::add_role();
			update_option( 'ole_delivery_flush_rewrites', 1 );
			update_option( 'ole_delivery_db_version', self::DB_VERSION );
		}
	}

	public static function create_table() {
		global $wpdb;
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$table   = $wpdb->prefix . 'ole_deliveries';
		$charset = $wpdb->get_charset_collate();

		dbDelta( "CREATE TABLE {$table} (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			order_id bigint(20) unsigned NOT NULL,
			rider_id bigint(20) unsigned NOT NULL DEFAULT 0,
			status varchar(20) NOT NULL DEFAULT 'pending',
			otp varchar(6) NOT NULL DEFAULT '',
			token varchar(40) NOT NULL DEFAULT '',
			dest_lat decimal(10,7) NULL,
			dest_lng decimal(10,7) NULL,
			is_cod tinyint(1) NOT NULL DEFAULT 0,
			cod_amount decimal(12,2) NOT NULL DEFAULT 0,
			cod_collected decimal(12,2) NULL,
			cod_settled tinyint(1) NOT NULL DEFAULT 0,
			rejected_by varchar(255) NOT NULL DEFAULT '',
			fail_reason varchar(255) NOT NULL DEFAULT '',
			proof_id bigint(20) unsigned NOT NULL DEFAULT 0,
			distance_km decimal(6,2) NULL,
			rider_pay decimal(10,2) NULL,
			created_at datetime NOT NULL,
			assigned_at datetime NULL,
			picked_at datetime NULL,
			delivered_at datetime NULL,
			updated_at datetime NOT NULL,
			PRIMARY KEY  (id),
			UNIQUE KEY order_id (order_id),
			KEY rider_status (rider_id,status),
			KEY status (status),
			KEY delivered_at (delivered_at),
			KEY token (token)
		) {$charset};" );
	}

	public static function add_role() {
		add_role( 'ole_rider', 'Delivery Rider', array(
			'read'        => true,
			'ole_deliver' => true,
		) );
		$admin = get_role( 'administrator' );
		if ( $admin && ! $admin->has_cap( 'ole_deliver' ) ) {
			// Lets the owner open the rider app to test it.
			$admin->add_cap( 'ole_deliver' );
		}
	}
}
