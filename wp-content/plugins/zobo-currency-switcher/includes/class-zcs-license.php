<?php
/**
 * Domain lock: the plugin only runs on sites Zobo Digital has licensed it for.
 *
 * @package ZoboCurrencySwitcher
 */

defined( 'ABSPATH' ) || exit;

class ZCS_License {

	const SALT = 'zcs|';

	/**
	 * sha256( SALT . domain ) of every licensed domain (without "www.").
	 * Hash a new domain with: php -r "echo hash( 'sha256', 'zcs|example.com' );"
	 */
	const DOMAINS = array(
		'6f2a6b68605a4fcfb2e842915b591763c10681c191058727f0b2d6bb910d1799', // Production.
		'57ab10a8c3a07f07fb8ce6c288c8c77de18a894181c5a612b5042e90a2511436', // Staging.
	);

	/** @var bool|null */
	private static $licensed = null;

	/**
	 * Site host, lower-case, without "www.".
	 *
	 * @return string
	 */
	public static function host() {
		$host = strtolower( (string) wp_parse_url( home_url( '/' ), PHP_URL_HOST ) );
		return 0 === strpos( $host, 'www.' ) ? substr( $host, 4 ) : $host;
	}

	/**
	 * Is this site allowed to run the plugin?
	 *
	 * @return bool
	 */
	public static function is_licensed() {
		if ( null === self::$licensed ) {
			$hash           = hash( 'sha256', self::SALT . self::host() );
			self::$licensed = in_array( $hash, self::DOMAINS, true );
		}
		return self::$licensed;
	}

	/**
	 * Message shown when the domain is not licensed.
	 *
	 * @return string
	 */
	public static function message() {
		return sprintf(
			/* translators: %s: site domain */
			__( 'Zobo Currency Switcher is not licensed for %s. This plugin is proprietary software of Zobo Digital and may only be used on licensed sites. Contact Zobo Digital (https://zobodigital.com/) for a licence.', 'zobo-currency-switcher' ),
			self::host()
		);
	}

	/**
	 * Admin notice on unlicensed sites.
	 */
	public static function notice() {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}
		echo '<div class="notice notice-error"><p>' . esc_html( self::message() ) . '</p></div>';
	}
}
