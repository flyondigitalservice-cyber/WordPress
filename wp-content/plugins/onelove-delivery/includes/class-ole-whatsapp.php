<?php
/**
 * WhatsApp notifications through the Meta WhatsApp Cloud API (approved templates).
 * Messages are queued with Action Scheduler so riders never wait on Meta's servers.
 *
 * @package OneLoveDelivery
 */

defined( 'ABSPATH' ) || exit;

class OLE_WhatsApp {

	const API = 'https://graph.facebook.com/v21.0/';

	public static function init() {
		add_action( 'ole_delivery_assigned', array( __CLASS__, 'on_assigned' ), 10, 2 );
		add_action( 'ole_delivery_out', array( __CLASS__, 'on_out' ) );
		add_action( 'ole_delivery_delivered', array( __CLASS__, 'on_delivered' ) );
		add_action( 'ole_delivery_no_rider', array( __CLASS__, 'on_no_rider' ) );
		add_action( 'ole_delivery_send_whatsapp', array( __CLASS__, 'send_now' ), 10, 3 );
	}

	public static function enabled() {
		return OLE_Settings::yes( 'wa_enabled' ) && OLE_Settings::get( 'wa_token' ) && OLE_Settings::get( 'wa_phone_id' );
	}

	/** Queue a template message. $params fill {{1}}, {{2}}, … in the template body. */
	public static function queue( $phone, $template, array $params ) {
		$to = OLE_Geo::wa_phone( $phone );
		if ( ! self::enabled() || ! $to || ! $template ) {
			return;
		}
		$params = array_map( function ( $p ) {
			// WhatsApp rejects empty params and newlines/tabs inside params.
			$p = trim( preg_replace( '/\s+/', ' ', (string) $p ) );
			return '' === $p ? '-' : mb_substr( $p, 0, 900 );
		}, $params );

		if ( function_exists( 'as_enqueue_async_action' ) ) {
			as_enqueue_async_action( 'ole_delivery_send_whatsapp', array( $to, $template, array_values( $params ) ), 'onelove-delivery' );
		} else {
			self::send_now( $to, $template, $params );
		}
	}

	public static function send_now( $to, $template, $params ) {
		$body = array(
			'messaging_product' => 'whatsapp',
			'to'                => $to,
			'type'              => 'template',
			'template'          => array(
				'name'       => $template,
				'language'   => array( 'code' => OLE_Settings::get( 'wa_lang' ) ),
				'components' => array(
					array(
						'type'       => 'body',
						'parameters' => array_map( function ( $p ) {
							return array( 'type' => 'text', 'text' => $p );
						}, (array) $params ),
					),
				),
			),
		);
		$res = wp_remote_post( self::API . rawurlencode( OLE_Settings::get( 'wa_phone_id' ) ) . '/messages', array(
			'timeout' => 15,
			'headers' => array(
				'Authorization' => 'Bearer ' . OLE_Settings::get( 'wa_token' ),
				'Content-Type'  => 'application/json',
			),
			'body'    => wp_json_encode( $body ),
		) );

		$code = is_wp_error( $res ) ? 0 : wp_remote_retrieve_response_code( $res );
		if ( $code < 200 || $code >= 300 ) {
			$detail = is_wp_error( $res ) ? $res->get_error_message() : wp_remote_retrieve_body( $res );
			if ( function_exists( 'wc_get_logger' ) ) {
				wc_get_logger()->error( sprintf( 'WhatsApp "%s" to %s failed (%d): %s', $template, $to, $code, $detail ), array( 'source' => 'onelove-delivery' ) );
			}
		}
	}

	/* Events ------------------------------------------------------------ */

	public static function on_assigned( $d, $rider_id ) {
		$order = wc_get_order( $d->order_id );
		if ( ! $order ) {
			return;
		}
		$info = OLE_Deliveries::order_info( $order );
		self::queue( get_user_meta( $rider_id, 'ole_phone', true ), OLE_Settings::get( 'wa_tpl_rider' ), array(
			$info['number'],
			$info['address'],
			$d->is_cod ? 'COD ₹' . number_format( (float) $d->cod_amount, 2 ) : 'Prepaid',
			home_url( '/rider/' ),
		) );
	}

	public static function on_out( $d ) {
		$order = wc_get_order( $d->order_id );
		if ( ! $order ) {
			return;
		}
		$info  = OLE_Deliveries::order_info( $order );
		$rider = get_userdata( $d->rider_id );
		self::queue( $info['phone'], OLE_Settings::get( 'wa_tpl_out' ), array(
			$info['first'] ? $info['first'] : 'there',
			$info['number'],
			$rider ? $rider->display_name : 'our rider',
			OLE_Deliveries::track_url( $d ),
			OLE_Settings::yes( 'otp_required' ) ? $d->otp : '-',
		) );
	}

	public static function on_delivered( $d ) {
		$order = wc_get_order( $d->order_id );
		if ( ! $order ) {
			return;
		}
		$info = OLE_Deliveries::order_info( $order );
		self::queue( $info['phone'], OLE_Settings::get( 'wa_tpl_delivered' ), array(
			$info['first'] ? $info['first'] : 'there',
			$info['number'],
		) );
	}

	public static function on_no_rider( $d ) {
		$order = wc_get_order( $d->order_id );
		if ( ! $order ) {
			return;
		}
		self::queue( OLE_Settings::get( 'wa_admin_phone' ), OLE_Settings::get( 'wa_tpl_admin' ), array(
			$order->get_order_number(),
			admin_url( 'admin.php?page=ole-dispatch' ),
		) );
	}
}
