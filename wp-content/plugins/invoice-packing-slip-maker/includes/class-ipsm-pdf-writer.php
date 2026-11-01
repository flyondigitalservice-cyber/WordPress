<?php
/**
 * Minimal, dependency-free PDF writer.
 *
 * Supports: multiple pages of any size, the built-in Helvetica / Helvetica-Bold
 * fonts (WinAnsi encoding), text, lines, rectangles, colours and JPEG images
 * (PNG/GIF/WebP are converted to JPEG through GD when available).
 * Coordinates are in points and measured from the TOP-LEFT corner.
 *
 * @package Invoice_Packing_Slip_Maker
 */

defined( 'ABSPATH' ) || exit;

/**
 * PDF writer.
 */
class IPSM_PDF_Writer {

	/**
	 * Page width in points.
	 *
	 * @var float
	 */
	public $w;

	/**
	 * Page height in points.
	 *
	 * @var float
	 */
	public $h;

	/**
	 * Content stream for each page.
	 *
	 * @var string[]
	 */
	private $pages = array();

	/**
	 * Images: key => [data, w, h, colorspace].
	 *
	 * @var array
	 */
	private $images = array();

	/**
	 * Current font: 'F1' regular, 'F2' bold.
	 *
	 * @var string
	 */
	private $font = 'F1';

	/**
	 * Current font size.
	 *
	 * @var float
	 */
	private $size = 10;

	/**
	 * Document title.
	 *
	 * @var string
	 */
	private $title = '';

	/**
	 * Helvetica glyph widths for chars 32..126 (1/1000 em).
	 *
	 * @var int[]
	 */
	private static $widths_regular = array( 278, 278, 355, 556, 556, 889, 667, 191, 333, 333, 389, 584, 278, 333, 278, 278, 556, 556, 556, 556, 556, 556, 556, 556, 556, 556, 278, 278, 584, 584, 584, 556, 1015, 667, 667, 722, 722, 667, 611, 778, 722, 278, 500, 667, 556, 833, 722, 778, 667, 778, 722, 667, 611, 722, 667, 944, 667, 667, 611, 278, 278, 278, 469, 556, 333, 556, 556, 500, 556, 556, 278, 556, 556, 222, 222, 500, 222, 833, 556, 556, 556, 556, 333, 500, 278, 556, 500, 722, 500, 500, 500, 334, 260, 334, 584 );

	/**
	 * Helvetica-Bold glyph widths for chars 32..126 (1/1000 em).
	 *
	 * @var int[]
	 */
	private static $widths_bold = array( 278, 333, 474, 556, 556, 889, 722, 238, 333, 333, 389, 584, 278, 333, 278, 278, 556, 556, 556, 556, 556, 556, 556, 556, 556, 556, 333, 333, 584, 584, 584, 611, 975, 722, 722, 722, 722, 667, 611, 778, 722, 278, 556, 722, 611, 833, 722, 778, 667, 778, 722, 667, 611, 722, 667, 944, 667, 667, 611, 333, 278, 333, 584, 556, 333, 556, 611, 556, 611, 556, 333, 611, 611, 278, 278, 556, 278, 889, 611, 611, 611, 611, 389, 556, 333, 611, 556, 778, 556, 556, 500, 389, 280, 389, 584 );

	/**
	 * Constructor.
	 *
	 * @param float  $width  Page width in points.
	 * @param float  $height Page height in points.
	 * @param string $title  Document title metadata.
	 */
	public function __construct( $width, $height, $title = '' ) {
		$this->w     = (float) $width;
		$this->h     = (float) $height;
		$this->title = (string) $title;
	}

	/**
	 * Start a new page.
	 */
	public function add_page() {
		$this->pages[] = '';
	}

	/**
	 * Number of pages so far.
	 *
	 * @return int
	 */
	public function page_count() {
		return count( $this->pages );
	}

	/**
	 * Append raw operators to the current page.
	 *
	 * @param string $s Operators.
	 */
	private function out( $s ) {
		if ( ! $this->pages ) {
			$this->add_page();
		}
		$this->pages[ count( $this->pages ) - 1 ] .= $s . "\n";
	}

	/**
	 * Select font.
	 *
	 * @param bool  $bold Bold.
	 * @param float $size Size in points.
	 */
	public function set_font( $bold, $size ) {
		$this->font = $bold ? 'F2' : 'F1';
		$this->size = (float) $size;
	}

	/**
	 * Current font size.
	 *
	 * @return float
	 */
	public function font_size() {
		return $this->size;
	}

	/**
	 * Convert a hex colour into PDF rgb components.
	 *
	 * @param string $hex #rrggbb.
	 * @return string "r g b"
	 */
	private static function rgb( $hex ) {
		$hex = ltrim( (string) $hex, '#' );
		if ( 3 === strlen( $hex ) ) {
			$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
		}
		if ( ! preg_match( '/^[0-9a-f]{6}$/i', $hex ) ) {
			$hex = '000000';
		}
		return sprintf( '%.3F %.3F %.3F', hexdec( substr( $hex, 0, 2 ) ) / 255, hexdec( substr( $hex, 2, 2 ) ) / 255, hexdec( substr( $hex, 4, 2 ) ) / 255 );
	}

	/**
	 * Set fill (and text) colour.
	 *
	 * @param string $hex Colour.
	 */
	public function set_fill( $hex ) {
		$this->out( self::rgb( $hex ) . ' rg' );
	}

	/**
	 * Set stroke colour.
	 *
	 * @param string $hex Colour.
	 */
	public function set_stroke( $hex ) {
		$this->out( self::rgb( $hex ) . ' RG' );
	}

	/**
	 * Convert UTF-8 text into a WinAnsi byte string.
	 *
	 * @param string $text UTF-8 text.
	 * @return string
	 */
	public static function encode( $text ) {
		$text = (string) $text;
		$map  = apply_filters(
			'ipsm_pdf_text_replacements',
			array(
				'₹' => 'Rs. ',
				'₽' => 'RUB ',
				'₺' => 'TL ',
				'₩' => 'KRW ',
				'₱' => 'PHP ',
				'₫' => ' VND',
				'₴' => 'UAH ',
				'₦' => 'NGN ',
				'₪' => 'ILS ',
				'৳' => 'BDT ',
				'฿' => 'THB ',
				'₨' => 'Rs. ',
				"\xc2\xa0" => ' ',
			)
		);
		$text = strtr( $text, $map );

		if ( function_exists( 'iconv' ) ) {
			$converted = @iconv( 'UTF-8', 'Windows-1252//TRANSLIT', $text ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
			if ( false !== $converted ) {
				return $converted;
			}
		}
		if ( function_exists( 'mb_convert_encoding' ) ) {
			return mb_convert_encoding( $text, 'Windows-1252', 'UTF-8' );
		}
		return preg_replace( '/[^\x20-\x7e]/', '?', $text );
	}

	/**
	 * Width of a UTF-8 string in points using the current or given font.
	 *
	 * @param string     $text UTF-8 text.
	 * @param float|null $size Size override.
	 * @param bool|null  $bold Bold override.
	 * @return float
	 */
	public function text_width( $text, $size = null, $bold = null ) {
		$bold   = null === $bold ? ( 'F2' === $this->font ) : $bold;
		$size   = null === $size ? $this->size : $size;
		$widths = $bold ? self::$widths_bold : self::$widths_regular;
		$bytes  = self::encode( $text );
		$sum    = 0;
		$len    = strlen( $bytes );
		for ( $i = 0; $i < $len; $i++ ) {
			$code = ord( $bytes[ $i ] );
			$sum += ( $code >= 32 && $code <= 126 ) ? $widths[ $code - 32 ] : 556;
		}
		return $sum * $size / 1000;
	}

	/**
	 * Draw text with its baseline at ($x, $y).
	 *
	 * @param float  $x     X.
	 * @param float  $y     Baseline Y from top.
	 * @param string $text  UTF-8 text.
	 * @param string $align left|right|center — relative to $x.
	 */
	public function text( $x, $y, $text, $align = 'left' ) {
		$text = (string) $text;
		if ( '' === $text ) {
			return;
		}
		if ( 'right' === $align ) {
			$x -= $this->text_width( $text );
		} elseif ( 'center' === $align ) {
			$x -= $this->text_width( $text ) / 2;
		}
		$escaped = str_replace( array( '\\', '(', ')', "\r", "\n" ), array( '\\\\', '\\(', '\\)', '', ' ' ), self::encode( $text ) );
		$this->out( sprintf( 'BT /%s %.2F Tf %.2F %.2F Td (%s) Tj ET', $this->font, $this->size, $x, $this->h - $y, $escaped ) );
	}

	/**
	 * Word-wrap text into lines that fit $width.
	 *
	 * @param string $text  UTF-8 text (may contain newlines).
	 * @param float  $width Max width.
	 * @return string[]
	 */
	public function wrap( $text, $width ) {
		$lines = array();
		foreach ( preg_split( '/\r\n|\r|\n/', (string) $text ) as $paragraph ) {
			$words = preg_split( '/\s+/u', trim( $paragraph ) );
			$line  = '';
			foreach ( $words as $word ) {
				if ( '' === $word ) {
					continue;
				}
				$try = '' === $line ? $word : $line . ' ' . $word;
				if ( $this->text_width( $try ) <= $width ) {
					$line = $try;
					continue;
				}
				if ( '' !== $line ) {
					$lines[] = $line;
				}
				// Hard-break words that are longer than the line on their own.
				while ( $this->text_width( $word ) > $width && function_exists( 'mb_substr' ) && mb_strlen( $word ) > 1 ) {
					$cut = mb_strlen( $word );
					while ( $cut > 1 && $this->text_width( mb_substr( $word, 0, $cut ) ) > $width ) {
						--$cut;
					}
					$lines[] = mb_substr( $word, 0, $cut );
					$word    = mb_substr( $word, $cut );
				}
				$line = $word;
			}
			$lines[] = $line;
		}
		return $lines;
	}

	/**
	 * Draw a rectangle.
	 *
	 * @param float  $x     X.
	 * @param float  $y     Top Y.
	 * @param float  $w     Width.
	 * @param float  $h     Height.
	 * @param string $style F fill, S stroke, B both.
	 */
	public function rect( $x, $y, $w, $h, $style = 'F' ) {
		$op = array(
			'F' => 'f',
			'S' => 'S',
			'B' => 'B',
		);
		$this->out( sprintf( '%.2F %.2F %.2F %.2F re %s', $x, $this->h - $y - $h, $w, $h, isset( $op[ $style ] ) ? $op[ $style ] : 'f' ) );
	}

	/**
	 * Draw a line.
	 *
	 * @param float $x1    X1.
	 * @param float $y1    Y1.
	 * @param float $x2    X2.
	 * @param float $y2    Y2.
	 * @param float $width Line width.
	 */
	public function line( $x1, $y1, $x2, $y2, $width = 0.5 ) {
		$this->out( sprintf( '%.2F w %.2F %.2F m %.2F %.2F l S', $width, $x1, $this->h - $y1, $x2, $this->h - $y2 ) );
	}

	/**
	 * Draw a dashed line.
	 *
	 * @param float $x1    X1.
	 * @param float $y1    Y1.
	 * @param float $x2    X2.
	 * @param float $y2    Y2.
	 * @param float $width Line width.
	 */
	public function dashed_line( $x1, $y1, $x2, $y2, $width = 0.75 ) {
		$this->out( '[4 3] 0 d' );
		$this->line( $x1, $y1, $x2, $y2, $width );
		$this->out( '[] 0 d' );
	}

	/**
	 * Load an image file as JPEG data.
	 *
	 * @param string $path File path.
	 * @return array|null [data, w, h, colorspace]
	 */
	private function load_image( $path ) {
		if ( ! $path || ! is_readable( $path ) ) {
			return null;
		}
		$info = @getimagesize( $path ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
		if ( ! $info ) {
			return null;
		}

		// Plain RGB/greyscale JPEG can be embedded as-is.
		if ( IMAGETYPE_JPEG === $info[2] && ( ! isset( $info['channels'] ) || 4 !== $info['channels'] ) ) {
			$data = file_get_contents( $path ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
			$cs   = ( isset( $info['channels'] ) && 1 === $info['channels'] ) ? 'DeviceGray' : 'DeviceRGB';
			return array( $data, $info[0], $info[1], $cs );
		}

		if ( ! function_exists( 'imagecreatefromstring' ) ) {
			return null;
		}
		$src = @imagecreatefromstring( file_get_contents( $path ) ); // phpcs:ignore
		if ( ! $src ) {
			return null;
		}
		$w   = imagesx( $src );
		$h   = imagesy( $src );
		$dst = imagecreatetruecolor( $w, $h );
		imagefill( $dst, 0, 0, imagecolorallocate( $dst, 255, 255, 255 ) );
		imagecopy( $dst, $src, 0, 0, 0, 0, $w, $h );
		ob_start();
		imagejpeg( $dst, null, 90 );
		$data = ob_get_clean();
		imagedestroy( $src );
		imagedestroy( $dst );
		return array( $data, $w, $h, 'DeviceRGB' );
	}

	/**
	 * Draw an image fitted inside a box, keeping aspect ratio.
	 *
	 * @param string $path  File path.
	 * @param float  $x     X.
	 * @param float  $y     Top Y.
	 * @param float  $max_w Max width.
	 * @param float  $max_h Max height.
	 * @return float[]|null Drawn [w, h] or null on failure.
	 */
	public function image( $path, $x, $y, $max_w, $max_h ) {
		$key = md5( (string) $path );
		if ( ! isset( $this->images[ $key ] ) ) {
			$img = $this->load_image( $path );
			if ( ! $img ) {
				return null;
			}
			$this->images[ $key ] = $img;
		}
		list( , $iw, $ih ) = $this->images[ $key ];
		$scale             = min( $max_w / $iw, $max_h / $ih );
		$w                 = $iw * $scale;
		$h                 = $ih * $scale;
		$name              = 'I' . ( array_search( $key, array_keys( $this->images ), true ) + 1 );
		$this->out( sprintf( 'q %.2F 0 0 %.2F %.2F %.2F cm /%s Do Q', $w, $h, $x, $this->h - $y - $h, $name ) );
		return array( $w, $h );
	}

	/**
	 * Build the PDF file.
	 *
	 * @return string Binary PDF.
	 */
	public function output() {
		if ( ! $this->pages ) {
			$this->add_page();
		}

		$objects  = array();
		$compress = function_exists( 'gzcompress' );

		// 1 catalog, 2 pages tree, 3-4 fonts, 5 info, then images, then pages.
		$objects[3] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>';
		$objects[4] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold /Encoding /WinAnsiEncoding >>';
		$objects[5] = '<< /Producer (' . self::pdf_string( 'Invoice & Packing Slip Maker' ) . ') /Title (' . self::pdf_string( $this->title ) . ') /CreationDate (D:' . gmdate( 'YmdHis' ) . 'Z) >>';

		$next      = 6;
		$xobjects  = array();
		$img_index = 1;
		foreach ( $this->images as $img ) {
			list( $data, $iw, $ih, $cs ) = $img;
			$objects[ $next ]            = array(
				'dict'   => sprintf( '<< /Type /XObject /Subtype /Image /Width %d /Height %d /ColorSpace /%s /BitsPerComponent 8 /Filter /DCTDecode /Length %d >>', $iw, $ih, $cs, strlen( $data ) ),
				'stream' => $data,
			);
			$xobjects[]                  = '/I' . $img_index . ' ' . $next . ' 0 R';
			++$img_index;
			++$next;
		}

		$resources = '<< /Font << /F1 3 0 R /F2 4 0 R >>' . ( $xobjects ? ' /XObject << ' . implode( ' ', $xobjects ) . ' >>' : '' ) . ' >>';
		$kids      = array();
		foreach ( $this->pages as $content ) {
			$page_id    = $next;
			$content_id = $next + 1;
			$next      += 2;
			$kids[]     = $page_id . ' 0 R';

			$objects[ $page_id ] = sprintf( '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 %.2F %.2F] /Resources %s /Contents %d 0 R >>', $this->w, $this->h, $resources, $content_id );

			$data                   = $compress ? gzcompress( $content ) : $content;
			$objects[ $content_id ] = array(
				'dict'   => '<< /Length ' . strlen( $data ) . ( $compress ? ' /Filter /FlateDecode' : '' ) . ' >>',
				'stream' => $data,
			);
		}

		$objects[1] = '<< /Type /Catalog /Pages 2 0 R >>';
		$objects[2] = '<< /Type /Pages /Kids [' . implode( ' ', $kids ) . '] /Count ' . count( $kids ) . ' >>';
		ksort( $objects );

		$pdf     = "%PDF-1.4\n%\xe2\xe3\xcf\xd3\n";
		$offsets = array();
		foreach ( $objects as $id => $obj ) {
			$offsets[ $id ] = strlen( $pdf );
			$pdf           .= $id . " 0 obj\n";
			if ( is_array( $obj ) ) {
				$pdf .= $obj['dict'] . "\nstream\n" . $obj['stream'] . "\nendstream\n";
			} else {
				$pdf .= $obj . "\n";
			}
			$pdf .= "endobj\n";
		}

		$xref  = strlen( $pdf );
		$count = max( array_keys( $objects ) ) + 1;
		$pdf  .= "xref\n0 " . $count . "\n0000000000 65535 f \n";
		for ( $i = 1; $i < $count; $i++ ) {
			$pdf .= isset( $offsets[ $i ] ) ? sprintf( "%010d 00000 n \n", $offsets[ $i ] ) : "0000000000 65535 f \n";
		}
		$pdf .= "trailer\n<< /Size " . $count . " /Root 1 0 R /Info 5 0 R >>\nstartxref\n" . $xref . "\n%%EOF\n";

		return $pdf;
	}

	/**
	 * Escape a string for a PDF literal.
	 *
	 * @param string $s Text.
	 * @return string
	 */
	private static function pdf_string( $s ) {
		return str_replace( array( '\\', '(', ')', "\r", "\n" ), array( '\\\\', '\\(', '\\)', '', ' ' ), self::encode( $s ) );
	}
}
