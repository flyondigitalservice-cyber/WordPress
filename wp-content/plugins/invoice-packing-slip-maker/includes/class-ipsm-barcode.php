<?php
/**
 * Code 128 barcode encoder (subsets B and C), output as module widths,
 * inline SVG (print view) or rectangles on a PDF page.
 *
 * @package Invoice_Packing_Slip_Maker
 */

defined( 'ABSPATH' ) || exit;

/**
 * Code 128.
 */
class IPSM_Barcode {

	const START_B = 104;
	const START_C = 105;
	const STOP    = 106;

	/**
	 * Quiet zone on each side, in modules.
	 */
	const QUIET = 10;

	/**
	 * Bar/space widths for symbol values 0–106 (bar first).
	 *
	 * @var string[]
	 */
	public static $patterns = array(
		'212222', '222122', '222221', '121223', '121322', '131222', '122213', '122312', '132212', '221213',
		'221312', '231212', '112232', '122132', '122231', '113222', '123122', '123221', '223211', '221132',
		'221231', '213212', '223112', '312131', '311222', '321122', '321221', '312212', '322112', '322211',
		'212123', '212321', '232121', '111323', '131123', '131321', '112313', '132113', '132311', '211313',
		'231113', '231311', '112133', '112331', '132131', '113123', '113321', '133121', '313121', '211331',
		'231131', '213113', '213311', '213131', '311123', '311321', '331121', '312113', '312311', '332111',
		'314111', '221411', '431111', '111224', '111422', '121124', '121421', '141122', '141221', '112214',
		'112412', '122114', '122411', '142112', '142211', '241211', '221114', '413111', '241112', '134111',
		'111242', '121142', '121241', '114212', '124112', '124211', '411212', '421112', '421211', '212141',
		'214121', '412121', '111143', '111341', '131141', '114113', '114311', '411113', '411311', '113141',
		'114131', '311141', '411131', '211412', '211214', '211232', '2331112',
	);

	/**
	 * Keep only characters Code 128 subset B can carry (printable ASCII).
	 *
	 * @param string $text Text.
	 * @return string
	 */
	public static function clean( $text ) {
		return preg_replace( '/[^\x20-\x7e]/', '', (string) $text );
	}

	/**
	 * Encode text into symbol values (start, data, checksum, stop).
	 *
	 * Purely numeric text of even length uses subset C (two digits per symbol,
	 * much narrower); everything else uses subset B.
	 *
	 * @param string $text Text.
	 * @return int[]
	 */
	public static function symbols( $text ) {
		$text = self::clean( $text );
		if ( '' === $text ) {
			return array();
		}

		if ( preg_match( '/^\d+$/', $text ) && 0 === strlen( $text ) % 2 ) {
			$codes = array( self::START_C );
			foreach ( str_split( $text, 2 ) as $pair ) {
				$codes[] = (int) $pair;
			}
		} else {
			$codes = array( self::START_B );
			$len   = strlen( $text );
			for ( $i = 0; $i < $len; $i++ ) {
				$codes[] = ord( $text[ $i ] ) - 32;
			}
		}

		$sum = $codes[0];
		$cnt = count( $codes );
		for ( $i = 1; $i < $cnt; $i++ ) {
			$sum += $codes[ $i ] * $i;
		}
		$codes[] = $sum % 103;
		$codes[] = self::STOP;
		return $codes;
	}

	/**
	 * Module widths, alternating bar, space, bar… (starts with a bar).
	 *
	 * @param string $text Text.
	 * @return int[]
	 */
	public static function widths( $text ) {
		$widths = array();
		foreach ( self::symbols( $text ) as $code ) {
			foreach ( str_split( self::$patterns[ $code ] ) as $w ) {
				$widths[] = (int) $w;
			}
		}
		return $widths;
	}

	/**
	 * Total width in modules, including quiet zones.
	 *
	 * @param string $text Text.
	 * @return int
	 */
	public static function total_modules( $text ) {
		$widths = self::widths( $text );
		return $widths ? array_sum( $widths ) + 2 * self::QUIET : 0;
	}

	/**
	 * Inline SVG for HTML output. Scales to its container's width.
	 *
	 * @param string $text   Text.
	 * @param int    $height Height in CSS px.
	 * @return string
	 */
	public static function svg( $text, $height = 50 ) {
		$widths = self::widths( $text );
		if ( ! $widths ) {
			return '';
		}
		$total = array_sum( $widths ) + 2 * self::QUIET;
		$x     = self::QUIET;
		$rects = '';
		foreach ( $widths as $i => $w ) {
			if ( 0 === $i % 2 ) {
				$rects .= '<rect x="' . $x . '" y="0" width="' . $w . '" height="100" />';
			}
			$x += $w;
		}
		return '<svg class="ipsm-barcode-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 ' . $total . ' 100" preserveAspectRatio="none" shape-rendering="crispEdges" style="width:100%;max-width:' . ( $total * 2 ) . 'px;height:' . (int) $height . 'px" role="img" aria-label="' . esc_attr( self::clean( $text ) ) . '"><rect width="' . $total . '" height="100" fill="#fff" /><g fill="#000">' . $rects . '</g></svg>';
	}

	/**
	 * Draw the barcode on a PDF page, centred in a box.
	 *
	 * @param IPSM_PDF_Writer $pdf    Writer.
	 * @param string          $text   Text.
	 * @param float           $x      Box left.
	 * @param float           $y      Box top.
	 * @param float           $max_w  Box width.
	 * @param float           $height Bar height.
	 * @return bool
	 */
	public static function draw( IPSM_PDF_Writer $pdf, $text, $x, $y, $max_w, $height ) {
		$widths = self::widths( $text );
		if ( ! $widths ) {
			return false;
		}
		$total  = array_sum( $widths ) + 2 * self::QUIET;
		$module = min( 2.0, $max_w / $total ); // Cap so short numbers don't become huge.
		$cx     = $x + ( $max_w - $total * $module ) / 2 + self::QUIET * $module;

		$pdf->set_fill( '#000000' );
		foreach ( $widths as $i => $w ) {
			if ( 0 === $i % 2 ) {
				$pdf->rect( $cx, $y, $w * $module, $height );
			}
			$cx += $w * $module;
		}
		return true;
	}
}
