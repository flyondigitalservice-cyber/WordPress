<?php
/**
 * Minimal HTML to Markdown converter built on DOMDocument.
 *
 * @package Bombay_Agent_Ready
 */

defined( 'ABSPATH' ) || exit;

class BMAR_Markdown {

	/**
	 * Elements that never contribute content.
	 *
	 * @var string[]
	 */
	private static $skip = array( 'script', 'style', 'noscript', 'template', 'svg', 'iframe', 'form', 'button', 'input', 'select', 'textarea', 'nav', 'header', 'footer', 'aside', 'head', 'link', 'meta' );

	/**
	 * Convert a full HTML document (or fragment) into Markdown.
	 *
	 * @param string $html HTML.
	 * @return string
	 */
	public static function from_html( $html ) {
		if ( ! class_exists( 'DOMDocument' ) ) {
			return trim( wp_strip_all_tags( $html ) );
		}

		$dom = new DOMDocument();
		libxml_use_internal_errors( true );
		$dom->loadHTML( '<?xml encoding="utf-8"?>' . $html, LIBXML_NOWARNING | LIBXML_NOERROR );
		libxml_clear_errors();

		// Prefer the main content region when the theme marks one up.
		$xpath = new DOMXPath( $dom );
		$root  = null;
		foreach ( array( '//*[@data-elementor-type="wp-page"]', '//*[@data-elementor-type="wp-post"]', '//main','//*[@role="main"]', '//article', '//body' ) as $query ) {
			$nodes = $xpath->query( $query );
			if ( $nodes && $nodes->length ) {
				$root = $nodes->item( 0 );
				break;
			}
		}
		if ( ! $root ) {
			$root = $dom->documentElement;
		}
		if ( ! $root ) {
			return '';
		}

		$md = self::children( $root );

		// Tidy whitespace.
		$md = preg_replace( "/[ \t]+\n/", "\n", $md );
		$md = preg_replace( "/\n{3,}/", "\n\n", $md );

		return trim( $md ) . "\n";
	}

	/**
	 * Convert the children of a node.
	 *
	 * @param DOMNode $node Node.
	 * @return string
	 */
	private static function children( $node ) {
		$out = '';
		foreach ( $node->childNodes as $child ) {
			$out .= self::node( $child );
		}
		return $out;
	}

	/**
	 * Inline text of a node with collapsed whitespace.
	 *
	 * @param DOMNode $node Node.
	 * @return string
	 */
	private static function inline( $node ) {
		return trim( preg_replace( '/\s+/u', ' ', self::children( $node ) ) );
	}

	/**
	 * Convert a single node.
	 *
	 * @param DOMNode $node Node.
	 * @return string
	 */
	private static function node( $node ) {
		if ( XML_TEXT_NODE === $node->nodeType ) {
			return preg_replace( '/\s+/u', ' ', $node->nodeValue );
		}
		if ( XML_ELEMENT_NODE !== $node->nodeType ) {
			return '';
		}

		$tag = strtolower( $node->nodeName );
		if ( in_array( $tag, self::$skip, true ) ) {
			return '';
		}

		/** @var DOMElement $node */
		$style = strtolower( (string) $node->getAttribute( 'style' ) );
		if ( $node->hasAttribute( 'hidden' ) || 'true' === $node->getAttribute( 'aria-hidden' ) || false !== strpos( str_replace( ' ', '', $style ), 'display:none' ) ) {
			return '';
		}

		switch ( $tag ) {
			case 'h1':
			case 'h2':
			case 'h3':
			case 'h4':
			case 'h5':
			case 'h6':
				$text = self::inline( $node );
				return '' === $text ? '' : "\n\n" . str_repeat( '#', (int) $tag[1] ) . ' ' . $text . "\n\n";

			case 'p':
				$text = self::inline( $node );
				return '' === $text ? '' : "\n\n" . $text . "\n\n";

			case 'br':
				return "  \n";

			case 'hr':
				return "\n\n---\n\n";

			case 'strong':
			case 'b':
				$text = self::inline( $node );
				return '' === $text ? '' : ' **' . $text . '** ';

			case 'em':
			case 'i':
				$text = self::inline( $node );
				return '' === $text ? '' : ' *' . $text . '* ';

			case 'code':
				return '`' . $node->textContent . '`';

			case 'pre':
				return "\n\n```\n" . rtrim( $node->textContent ) . "\n```\n\n";

			case 'blockquote':
				$text = trim( self::children( $node ) );
				return "\n\n> " . str_replace( "\n", "\n> ", $text ) . "\n\n";

			case 'a':
				$text = self::inline( $node );
				$href = trim( (string) $node->getAttribute( 'href' ) );
				if ( '' === $text ) {
					return '';
				}
				if ( '' === $href || '#' === $href[0] || 0 === strpos( $href, 'javascript:' ) ) {
					return ' ' . $text . ' ';
				}
				return ' [' . $text . '](' . self::absolute( $href ) . ') ';

			case 'img':
				$alt = trim( (string) $node->getAttribute( 'alt' ) );
				$src = (string) $node->getAttribute( 'src' );
				return ( '' === $alt || '' === $src || 0 === strpos( $src, 'data:' ) ) ? '' : ' ![' . $alt . '](' . self::absolute( $src ) . ') ';

			case 'ul':
			case 'ol':
				$out = "\n\n";
				$i   = 1;
				foreach ( $node->childNodes as $li ) {
					if ( XML_ELEMENT_NODE !== $li->nodeType || 'li' !== strtolower( $li->nodeName ) ) {
						continue;
					}
					$text = trim( preg_replace( '/\n{2,}/', "\n", self::children( $li ) ) );
					$text = preg_replace( '/[ \t]+/', ' ', $text );
					if ( '' === $text ) {
						continue;
					}
					$out .= ( 'ol' === $tag ? $i++ . '. ' : '- ' ) . str_replace( "\n", "\n  ", $text ) . "\n";
				}
				return $out . "\n";

			case 'table':
				return self::table( $node );

			default:
				return self::children( $node );
		}
	}

	/**
	 * Make a root-relative URL absolute so links work outside the site.
	 *
	 * @param string $url URL.
	 * @return string
	 */
	private static function absolute( $url ) {
		if ( '/' === substr( $url, 0, 1 ) && '//' !== substr( $url, 0, 2 ) ) {
			return untrailingslashit( home_url() ) . $url;
		}
		return $url;
	}

	/**
	 * Convert a table into a Markdown table.
	 *
	 * @param DOMElement $table Table.
	 * @return string
	 */
	private static function table( $table ) {
		$rows = array();
		foreach ( $table->getElementsByTagName( 'tr' ) as $tr ) {
			$cells = array();
			foreach ( $tr->childNodes as $cell ) {
				if ( XML_ELEMENT_NODE === $cell->nodeType && in_array( strtolower( $cell->nodeName ), array( 'td', 'th' ), true ) ) {
					$cells[] = str_replace( '|', '\|', self::inline( $cell ) );
				}
			}
			if ( $cells ) {
				$rows[] = $cells;
			}
		}
		if ( ! $rows ) {
			return '';
		}
		$cols = max( array_map( 'count', $rows ) );
		$out  = "\n\n";
		foreach ( $rows as $n => $cells ) {
			$cells = array_pad( $cells, $cols, '' );
			$out  .= '| ' . implode( ' | ', $cells ) . " |\n";
			if ( 0 === $n ) {
				$out .= '|' . str_repeat( ' --- |', $cols ) . "\n";
			}
		}
		return $out . "\n";
	}
}
