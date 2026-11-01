<?php
/**
 * Lays out invoices, packing slips and shipping labels as PDF.
 *
 * Uses Dompdf (rendering the same HTML templates used for printing) when the
 * library is already available on the site, otherwise the bundled
 * IPSM_PDF_Writer. Developers can force either with the `ipsm_use_dompdf` filter.
 *
 * @package Invoice_Packing_Slip_Maker
 */

defined( 'ABSPATH' ) || exit;

/**
 * PDF generation.
 */
class IPSM_PDF {

	const MARGIN = 40;

	/**
	 * Generate a PDF containing one or more documents of the same type.
	 *
	 * @param IPSM_Document[] $docs Documents.
	 * @return string Binary PDF.
	 */
	public static function generate( array $docs ) {
		$docs = array_values( $docs );
		if ( ! $docs ) {
			return '';
		}

		if ( apply_filters( 'ipsm_use_dompdf', class_exists( '\Dompdf\Dompdf' ), $docs ) ) {
			return self::generate_dompdf( $docs );
		}

		$type = $docs[0]->type;
		list( $w, $h ) = self::page_size( $type );
		$title = 1 === count( $docs ) ? $docs[0]->title() . ' ' . ( IPSM_Document::TYPE_INVOICE === $type ? $docs[0]->invoice_number() : $docs[0]->order->get_order_number() ) : $docs[0]->title();
		$pdf   = new IPSM_PDF_Writer( $w, $h, $title );

		foreach ( $docs as $doc ) {
			switch ( $doc->type ) {
				case IPSM_Document::TYPE_LABEL:
					self::draw_label( $pdf, $doc );
					break;
				case IPSM_Document::TYPE_SLIP:
					self::draw_document( $pdf, $doc, true );
					break;
				default:
					self::draw_document( $pdf, $doc, false );
			}
		}

		return $pdf->output();
	}

	/**
	 * Page size in points for a document type.
	 *
	 * @param string $type Type.
	 * @return float[] [w, h]
	 */
	public static function page_size( $type ) {
		if ( IPSM_Document::TYPE_LABEL === $type ) {
			switch ( IPSM_Settings::get( 'label_size' ) ) {
				case '4x6':
					return array( 288, 432 );
				case '100x150':
					return array( 283.46, 425.20 );
				case 'A6':
					return array( 297.64, 419.53 );
			}
			// "paper": same size as invoices and packing slips.
		}
		switch ( IPSM_Settings::get( 'paper_size' ) ) {
			case 'Letter':
				return array( 612, 792 );
			case 'A4':
				return array( 595.28, 841.89 );
			default:
				return array( 419.53, 595.28 ); // A5.
		}
	}

	/**
	 * Layout scale for invoices / packing slips: 1 on A4/Letter, smaller on A5
	 * so fonts, margins and columns shrink in proportion.
	 *
	 * @param float $page_w Page width in points.
	 * @return float
	 */
	public static function scale( $page_w ) {
		return (float) apply_filters( 'ipsm_pdf_scale', $page_w < 500 ? 0.78 : 1.0, $page_w );
	}

	/**
	 * Render through Dompdf.
	 *
	 * @param IPSM_Document[] $docs Documents.
	 * @return string
	 */
	private static function generate_dompdf( array $docs ) {
		$options = new \Dompdf\Options();
		$options->set( 'isRemoteEnabled', true );
		$options->set( 'defaultFont', 'DejaVu Sans' );
		$dompdf = new \Dompdf\Dompdf( $options );
		$dompdf->loadHtml( IPSM_Renderer::html( $docs, false ) );
		list( $w, $h ) = self::page_size( $docs[0]->type );
		$dompdf->setPaper( array( 0, 0, $w, $h ) );
		$dompdf->render();
		return $dompdf->output();
	}

	/**
	 * Draw an invoice or packing slip (may span several pages).
	 *
	 * @param IPSM_PDF_Writer $pdf     Writer.
	 * @param IPSM_Document   $doc     Document.
	 * @param bool            $is_slip Packing slip layout.
	 */
	private static function draw_document( IPSM_PDF_Writer $pdf, IPSM_Document $doc, $is_slip ) {
		$s      = self::scale( $pdf->w );
		$m      = self::MARGIN * $s;
		$right  = $pdf->w - $m;
		$width  = $pdf->w - 2 * $m;
		$accent = (string) IPSM_Settings::get( 'accent_color' );
		$grey   = '#6b7280';
		$text   = '#111827';
		$shop   = $doc->shop();
		$order  = $doc->order;

		$pdf->add_page();
		$y = $m;

		// Header: logo / shop name on the left, document title on the right.
		$logo_h = 0;
		if ( $shop['logo_path'] ) {
			$drawn  = $pdf->image( $shop['logo_path'], $m, $y, 170 * $s, 60 * $s );
			$logo_h = $drawn ? $drawn[1] : 0;
		}
		if ( ! $logo_h ) {
			$pdf->set_fill( $text );
			$pdf->set_font( true, 18 * $s );
			$pdf->text( $m, $y + 18 * $s, $shop['name'] );
			$logo_h = 24 * $s;
		}
		$pdf->set_fill( $accent );
		$pdf->set_font( true, 22 * $s );
		$pdf->text( $right, $y + 20 * $s, strtoupper( $doc->title() ), 'right' );

		$y += max( $logo_h, 28 * $s ) + 16 * $s;

		// Shop details (left) and document meta (right).
		$lh     = 12 * $s;
		$left_y = $y;
		$pdf->set_fill( $text );
		$pdf->set_font( true, 10 * $s );
		if ( $shop['logo_path'] ) {
			$pdf->text( $m, $left_y, $shop['name'] );
			$left_y += 13 * $s;
		}
		$pdf->set_font( false, 9 * $s );
		$pdf->set_fill( $grey );
		$shop_lines = $shop['address'];
		if ( $shop['phone'] ) {
			$shop_lines[] = $shop['phone'];
		}
		if ( $shop['email'] ) {
			$shop_lines[] = $shop['email'];
		}
		if ( $shop['tax_id'] ) {
			/* translators: %s: tax number */
			$shop_lines[] = sprintf( __( 'Tax ID: %s', 'invoice-packing-slip-maker' ), $shop['tax_id'] );
		}
		foreach ( $shop_lines as $line ) {
			foreach ( $pdf->wrap( $line, $width / 2 - 10 ) as $wrapped ) {
				$pdf->text( $m, $left_y, $wrapped );
				$left_y += $lh;
			}
		}

		$meta = array();
		if ( ! $is_slip ) {
			$meta[ __( 'Invoice number', 'invoice-packing-slip-maker' ) ] = $doc->invoice_number();
			$meta[ __( 'Invoice date', 'invoice-packing-slip-maker' ) ]   = $doc->invoice_date();
		}
		$meta[ __( 'Order number', 'invoice-packing-slip-maker' ) ] = $order->get_order_number();
		$meta[ __( 'Order date', 'invoice-packing-slip-maker' ) ]   = $doc->order_date();
		if ( $is_slip ) {
			$meta[ __( 'Shipping method', 'invoice-packing-slip-maker' ) ] = $order->get_shipping_method();
		} else {
			$meta[ __( 'Payment method', 'invoice-packing-slip-maker' ) ] = $order->get_payment_method_title();
		}

		$right_y = $y;
		foreach ( array_filter( $meta, 'strlen' ) as $label => $value ) {
			$pdf->set_font( false, 9 * $s );
			$pdf->set_fill( $grey );
			$pdf->text( $right - 130 * $s, $right_y, $label . ':', 'right' );
			$pdf->set_font( true, 9 * $s );
			$pdf->set_fill( $text );
			$pdf->text( $right, $right_y, $value, 'right' );
			$right_y += 13 * $s;
		}

		$y = max( $left_y, $right_y ) + 14 * $s;

		// Addresses.
		$col_w   = ( $width - 20 * $s ) / 2;
		$columns = array();
		if ( $is_slip ) {
			$columns[ __( 'Ship to', 'invoice-packing-slip-maker' ) ] = array_merge( $doc->shipping_lines(), array_filter( array( $doc->shipping_phone() ) ) );
			$columns[ __( 'Bill to', 'invoice-packing-slip-maker' ) ] = $doc->billing_lines();
		} else {
			$bill = $doc->billing_lines();
			if ( IPSM_Settings::enabled( 'show_email_phone' ) ) {
				$bill = array_merge( $bill, array_filter( array( $order->get_billing_email(), $order->get_billing_phone() ) ) );
			}
			$columns[ __( 'Bill to', 'invoice-packing-slip-maker' ) ] = $bill;
			if ( IPSM_Settings::enabled( 'show_shipping_addr' ) && $doc->has_shipping_address() ) {
				$columns[ __( 'Ship to', 'invoice-packing-slip-maker' ) ] = $doc->shipping_lines();
			}
		}

		$x         = $m;
		$max_y     = $y;
		$first_col = true;
		foreach ( $columns as $heading => $lines ) {
			$cy = $y;
			$pdf->set_fill( $accent );
			$pdf->set_font( true, 8 * $s );
			$pdf->text( $x, $cy, strtoupper( $heading ) );
			$cy += 14 * $s;
			$pdf->set_fill( $text );
			foreach ( $lines as $i => $line ) {
				// The ship-to block on a packing slip is the important one: make it larger.
				$big = $is_slip && $first_col;
				$pdf->set_font( $big && 0 === $i, ( $big ? 11 : 9.5 ) * $s );
				foreach ( $pdf->wrap( $line, $col_w ) as $wrapped ) {
					$pdf->text( $x, $cy, $wrapped );
					$cy += ( $big ? 14 : 12 ) * $s;
				}
			}
			$max_y     = max( $max_y, $cy );
			$x        += $col_w + 20 * $s;
			$first_col = false;
		}
		$y = $max_y + 16 * $s;

		// Items table.
		$show_prices = ! $is_slip || IPSM_Settings::enabled( 'slip_show_prices' );
		$check_w     = $is_slip ? 22 * $s : 0;
		$qty_w       = 50 * $s;
		$price_w     = $show_prices ? 80 * $s : 0;
		$total_w     = $show_prices ? 85 * $s : 0;
		$weight_w    = ( $is_slip && ! $show_prices ) ? 80 * $s : 0;
		$name_w      = $width - $check_w - $qty_w - $price_w - $total_w - $weight_w - 10 * $s;
		$pad         = 6 * $s;
		$head_h      = 20 * $s;

		$draw_header = static function ( $y ) use ( $pdf, $s, $m, $width, $right, $accent, $check_w, $name_w, $qty_w, $price_w, $weight_w, $show_prices, $pad, $head_h ) {
			$pdf->set_fill( $accent );
			$pdf->rect( $m, $y, $width, $head_h );
			$pdf->set_fill( '#ffffff' );
			$pdf->set_font( true, 8.5 * $s );
			$base = $y + 13.5 * $s;
			$pdf->text( $m + $pad + $check_w, $base, strtoupper( __( 'Product', 'invoice-packing-slip-maker' ) ) );
			$qx = $m + $check_w + $name_w + $qty_w;
			$pdf->text( $qx, $base, strtoupper( __( 'Qty', 'invoice-packing-slip-maker' ) ), 'right' );
			if ( $show_prices ) {
				$pdf->text( $qx + $price_w, $base, strtoupper( __( 'Price', 'invoice-packing-slip-maker' ) ), 'right' );
				$pdf->text( $right - $pad, $base, strtoupper( __( 'Total', 'invoice-packing-slip-maker' ) ), 'right' );
			} elseif ( $weight_w ) {
				$pdf->text( $right - $pad, $base, strtoupper( __( 'Weight', 'invoice-packing-slip-maker' ) ), 'right' );
			}
			return $y + $head_h;
		};

		$y      = $draw_header( $y );
		$bottom = $pdf->h - $m - 30 * $s;
		$row    = 0;

		foreach ( $doc->items() as $item ) {
			$pdf->set_font( true, 9.5 * $s );
			$name_lines = $pdf->wrap( $item['name'], $name_w - 8 * $s );
			$pdf->set_font( false, 8 * $s );
			$sub_lines = array();
			if ( $item['sku'] && IPSM_Settings::enabled( 'show_sku' ) ) {
				/* translators: %s: SKU */
				$sub_lines[] = sprintf( __( 'SKU: %s', 'invoice-packing-slip-maker' ), $item['sku'] );
			}
			foreach ( $item['meta'] as $meta_line ) {
				$sub_lines = array_merge( $sub_lines, $pdf->wrap( $meta_line, $name_w - 8 * $s ) );
			}
			$row_h = ( 10 + count( $name_lines ) * 12 + count( $sub_lines ) * 10 ) * $s;

			if ( $y + $row_h > $bottom ) {
				$pdf->add_page();
				$y = $draw_header( $m );
			}

			if ( $row % 2 ) {
				$pdf->set_fill( '#f3f4f6' );
				$pdf->rect( $m, $y, $width, $row_h );
			}
			++$row;

			$ty = $y + 15 * $s;
			if ( $is_slip ) {
				$pdf->set_stroke( '#9ca3af' );
				$pdf->rect( $m + 7 * $s, $y + 6 * $s, 10 * $s, 10 * $s, 'S' );
			}
			$pdf->set_fill( $text );
			$pdf->set_font( true, 9.5 * $s );
			$line_y = $ty;
			foreach ( $name_lines as $l ) {
				$pdf->text( $m + $pad + $check_w, $line_y, $l );
				$line_y += 12 * $s;
			}
			$pdf->set_font( false, 8 * $s );
			$pdf->set_fill( $grey );
			foreach ( $sub_lines as $l ) {
				$pdf->text( $m + $pad + $check_w, $line_y - 2 * $s, $l );
				$line_y += 10 * $s;
			}

			$pdf->set_fill( $text );
			$pdf->set_font( $is_slip, ( $is_slip ? 11 : 9.5 ) * $s );
			$qx = $m + $check_w + $name_w + $qty_w;
			$pdf->text( $qx, $ty, wc_stock_amount( $item['qty'] ), 'right' );
			$pdf->set_font( false, 9.5 * $s );
			if ( $show_prices ) {
				$pdf->text( $qx + $price_w, $ty, $doc->price_text( $item['unit_price'] ), 'right' );
				$pdf->text( $right - $pad, $ty, $doc->price_text( $item['total'] ), 'right' );
			} elseif ( $weight_w && $item['weight'] ) {
				$pdf->text( $right - $pad, $ty, wp_strip_all_tags( wc_format_weight( $item['weight'] ) ), 'right' );
			}

			$y += $row_h;
		}

		$pdf->set_stroke( '#d1d5db' );
		$pdf->line( $m, $y, $right, $y );
		$y += 16 * $s;

		// Totals (invoice) or package summary (slip).
		$summary = array();
		if ( $is_slip ) {
			$summary[ __( 'Total items', 'invoice-packing-slip-maker' ) ] = wc_stock_amount( $doc->total_qty() );
			if ( $doc->total_weight() ) {
				$summary[ __( 'Total weight', 'invoice-packing-slip-maker' ) ] = wp_strip_all_tags( $doc->total_weight() );
			}
		} else {
			foreach ( $doc->totals() as $key => $t ) {
				$summary[ $t['label'] ] = array( $t['text'], $t['note'] );
			}
		}

		$label_x = $right - 175 * $s;
		$last    = count( $summary ) - 1;
		$i       = 0;

		// Keep the totals block together: move it all to a new page if it won't fit.
		if ( $y + ( count( $summary ) * 18 + 30 ) * $s > $bottom + 30 * $s ) {
			$pdf->add_page();
			$y = $m + 12 * $s;
		}
		foreach ( $summary as $label => $value ) {
			list( $value, $note ) = is_array( $value ) ? $value : array( $value, '' );
			if ( $y > $bottom ) {
				$pdf->add_page();
				$y = $m;
			}
			$is_total = ! $is_slip && $i === $last;
			if ( $is_total ) {
				$y += 4 * $s;
				$pdf->set_fill( $accent );
				$pdf->rect( $label_x - 90 * $s, $y - 13 * $s, $right - $label_x + 90 * $s, 21 * $s );
				$pdf->set_fill( '#ffffff' );
			} else {
				$pdf->set_fill( $text );
			}
			$pdf->set_font( $is_total, ( $is_total ? 10.5 : 9.5 ) * $s );
			$pdf->text( $label_x, $y + 1, $label, 'right' );
			$pdf->set_font( true, ( $is_total ? 11 : 9.5 ) * $s );
			$pdf->text( $right - $pad, $y + 1, $value, 'right' );
			$y += ( $is_total ? 20 : 13 ) * $s;
			if ( $note ) {
				$pdf->set_fill( $grey );
				$pdf->set_font( false, 8 * $s );
				foreach ( $pdf->wrap( $note, 250 * $s ) as $nl ) {
					$pdf->text( $right - $pad, $y - 2 * $s, $nl, 'right' );
					$y += 10 * $s;
				}
			}
			$y += 3 * $s;
			++$i;
		}

		// Notes.
		$texts = $doc->texts();
		$notes = array();
		if ( $order->get_customer_note() ) {
			$notes[ __( 'Customer note', 'invoice-packing-slip-maker' ) ] = $order->get_customer_note();
		}
		if ( ! $is_slip && $texts['notes'] ) {
			$notes[ __( 'Notes', 'invoice-packing-slip-maker' ) ] = $texts['notes'];
		}
		$y += 10 * $s;
		foreach ( $notes as $heading => $body ) {
			$pdf->set_font( false, 9 * $s );
			$lines = $pdf->wrap( $body, $width );
			if ( $y + ( 16 + count( $lines ) * 12 ) * $s > $bottom ) {
				$pdf->add_page();
				$y = $m;
			}
			$pdf->set_fill( $accent );
			$pdf->set_font( true, 8 * $s );
			$pdf->text( $m, $y, strtoupper( $heading ) );
			$y += 13 * $s;
			$pdf->set_fill( $text );
			$pdf->set_font( false, 9 * $s );
			foreach ( $lines as $l ) {
				$pdf->text( $m, $y, $l );
				$y += 12 * $s;
			}
			$y += 8 * $s;
		}

		// Footer.
		if ( $texts['footer'] ) {
			$pdf->set_fill( $grey );
			$pdf->set_font( false, 8.5 * $s );
			$fy = $pdf->h - $m + 6 * $s;
			$fl = array_reverse( $pdf->wrap( $texts['footer'], $width ) );
			foreach ( $fl as $l ) {
				$pdf->text( $pdf->w / 2, $fy, $l, 'center' );
				$fy -= 11 * $s;
			}
		}
	}

	/**
	 * Draw a shipping label to stick on the parcel, with a Code 128 barcode of
	 * the order number.
	 *
	 * @param IPSM_PDF_Writer $pdf Writer.
	 * @param IPSM_Document   $doc Document.
	 */
	private static function draw_label( IPSM_PDF_Writer $pdf, IPSM_Document $doc ) {
		// Base layout is designed for a 4x6 in label; grow a little on larger
		// pages but keep text compact.
		$s     = min( 1.15, max( 0.9, $pdf->w / 288 ) );
		$m     = 14 * $s;
		$w     = $pdf->w - 2 * $m;
		$shop  = $doc->shop();
		$order = $doc->order;
		$black = '#000000';
		$x0    = $m - 4;
		$x1    = $pdf->w - $m + 4;

		$pdf->add_page();
		$pdf->set_stroke( $black );
		$pdf->rect( $x0, $m - 4, $x1 - $x0, $pdf->h - 2 * $m + 8, 'S' );

		// From.
		$y = $m + 8 * $s;
		$pdf->set_fill( $black );
		$pdf->set_font( true, 7 * $s );
		$pdf->text( $m + 4, $y, strtoupper( __( 'From', 'invoice-packing-slip-maker' ) ) );
		$y += 10 * $s;
		$pdf->set_font( true, 8.5 * $s );
		$pdf->text( $m + 4, $y, $shop['name'] );
		$y += 10 * $s;
		$pdf->set_font( false, 8 * $s );
		$from = $shop['address'];
		if ( $shop['phone'] ) {
			$from[] = $shop['phone'];
		}
		foreach ( $from as $line ) {
			foreach ( $pdf->wrap( $line, $w - 8 ) as $l ) {
				$pdf->text( $m + 4, $y, $l );
				$y += 9.5 * $s;
			}
		}
		$y += 4 * $s;
		$pdf->line( $x0, $y, $x1, $y, 1.5 );
		$y += 18 * $s;

		// Ship to.
		$pdf->set_font( true, 9 * $s );
		$pdf->text( $m + 4, $y, strtoupper( __( 'Ship to', 'invoice-packing-slip-maker' ) ) );
		$y += 20 * $s;
		foreach ( $doc->shipping_lines() as $i => $line ) {
			$pdf->set_font( true, ( 0 === $i ? 15 : 12.5 ) * $s );
			foreach ( $pdf->wrap( $line, $w - 8 ) as $l ) {
				$pdf->text( $m + 4, $y, $l );
				$y += ( 0 === $i ? 19 : 16 ) * $s;
			}
		}
		$phone = $doc->shipping_phone();
		if ( $phone ) {
			$pdf->set_font( false, 11 * $s );
			/* translators: %s: phone number */
			$pdf->text( $m + 4, $y + 2, sprintf( __( 'Phone: %s', 'invoice-packing-slip-maker' ), $phone ) );
		}

		// Bottom block: order number + package info.
		$by = $pdf->h - $m - 70 * $s;

		// Barcode of the order number, just above the bottom block.
		$code = IPSM_Barcode::clean( apply_filters( 'ipsm_barcode_value', $order->get_order_number(), $doc ) );
		if ( '' !== $code ) {
			$bar_h = 46 * $s;
			$top   = $by - $bar_h - 24 * $s;
			$pdf->line( $x0, $top - 8 * $s, $x1, $top - 8 * $s, 1.5 );
			IPSM_Barcode::draw( $pdf, $code, $m, $top, $w, $bar_h );
			$pdf->set_fill( $black );
			$pdf->set_font( false, 8 * $s );
			$pdf->text( $pdf->w / 2, $top + $bar_h + 11 * $s, $code, 'center' );
		}

		$pdf->set_fill( $black );
		$pdf->line( $x0, $by, $x1, $by, 1.5 );
		$pdf->set_font( true, 7.5 * $s );
		$pdf->text( $m + 4, $by + 14 * $s, strtoupper( __( 'Order', 'invoice-packing-slip-maker' ) ) );
		$pdf->set_font( true, 22 * $s );
		$pdf->text( $m + 4, $by + 38 * $s, '#' . $order->get_order_number() );

		$pdf->set_font( false, 8 * $s );
		$info = array();
		if ( $order->get_shipping_method() ) {
			$info[] = $order->get_shipping_method();
		}
		/* translators: %s: number of items */
		$info[] = sprintf( __( 'Items: %s', 'invoice-packing-slip-maker' ), wc_stock_amount( $doc->total_qty() ) );
		if ( $doc->total_weight() ) {
			/* translators: %s: weight */
			$info[] = sprintf( __( 'Weight: %s', 'invoice-packing-slip-maker' ), wp_strip_all_tags( $doc->total_weight() ) );
		}
		$iy = $by + 14 * $s;
		foreach ( $info as $line ) {
			$pdf->text( $pdf->w - $m - 4, $iy, $line, 'right' );
			$iy += 10 * $s;
		}
		$pdf->text( $pdf->w - $m - 4, $by + 58 * $s, $doc->order_date(), 'right' );
	}
}
