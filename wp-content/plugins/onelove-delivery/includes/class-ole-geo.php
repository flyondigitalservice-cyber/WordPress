<?php
/**
 * Small geo / phone helpers (no external API calls).
 *
 * @package OneLoveDelivery
 */

defined( 'ABSPATH' ) || exit;

class OLE_Geo {

	/** Average city riding speed used for rough ETAs (km/h). */
	const CITY_SPEED_KMH = 20;

	/** Straight-line distance in km between two lat/lng points. */
	public static function distance_km( $lat1, $lng1, $lat2, $lng2 ) {
		$r    = 6371;
		$dlat = deg2rad( $lat2 - $lat1 );
		$dlng = deg2rad( $lng2 - $lng1 );
		$a    = sin( $dlat / 2 ) ** 2 + cos( deg2rad( $lat1 ) ) * cos( deg2rad( $lat2 ) ) * sin( $dlng / 2 ) ** 2;
		return $r * 2 * atan2( sqrt( $a ), sqrt( 1 - $a ) );
	}

	/** Rough ETA in minutes: road distance ≈ 1.35 × straight line, plus 2 min to park & hand over. */
	public static function eta_minutes( $km ) {
		return (int) max( 2, round( ( $km * 1.35 ) / self::CITY_SPEED_KMH * 60 + 2 ) );
	}

	public static function valid_point( $lat, $lng ) {
		return is_numeric( $lat ) && is_numeric( $lng )
			&& abs( (float) $lat ) <= 90 && abs( (float) $lng ) <= 180
			&& ! ( 0.0 === (float) $lat && 0.0 === (float) $lng );
	}

	/** Google Maps turn-by-turn link that opens the Maps app on the rider's phone. */
	public static function nav_url( $lat, $lng, $address = '' ) {
		$dest = self::valid_point( $lat, $lng ) ? $lat . ',' . $lng : $address;
		return 'https://www.google.com/maps/dir/?api=1&travelmode=two-wheeler&destination=' . rawurlencode( (string) $dest );
	}

	/** Normalise an Indian phone number to WhatsApp format (91XXXXXXXXXX). */
	public static function wa_phone( $phone ) {
		$digits = preg_replace( '/\D+/', '', (string) $phone );
		if ( 11 === strlen( $digits ) && '0' === $digits[0] ) {
			$digits = substr( $digits, 1 );
		}
		if ( 10 === strlen( $digits ) ) {
			$digits = '91' . $digits;
		}
		return strlen( $digits ) >= 11 ? $digits : '';
	}
}
