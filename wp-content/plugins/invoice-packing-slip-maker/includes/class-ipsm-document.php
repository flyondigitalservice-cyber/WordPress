<?php
/**
 * Document data model: turns a WC_Order into the data shown on an invoice,
 * packing slip or shipping label. Both the HTML templates and the PDF renderer
 * read from this one object so they always agree.
 *
 * @package Invoice_Packing_Slip_Maker
 */

defined( 'ABSPATH' ) || exit;

/**
 * One document for one order.
 */
class IPSM_Document {

	const TYPE_INVOICE = 'invoice';
	const TYPE_SLIP    = 'packing-slip';
	const TYPE_LABEL   = 'shipping-label';

	const META_NUMBER  = '_ipsm_invoice_number';
	const META_DATE    = '_ipsm_invoice_date';
	const META_SENT    = '_ipsm_invoice_sent';

	/**
	 * Order.
	 *
	 * @var WC_Order
	 */
	public $order;

	/**
	 * Document type.
	 *
	 * @var string
	 */
	public $type;

	/**
	 * Constructor.
	 *
	 * @param WC_Order $order Order.
	 * @param string   $type  Document type.
	 */
	public function __construct( WC_Order $order, $type = self::TYPE_INVOICE ) {
		$this->order = $order;
		$this->type  = in_array( $type, self::types(), true ) ? $type : self::TYPE_INVOICE;

		if ( self::TYPE_INVOICE === $this->type ) {
			$this->ensure_number();
		}
	}

	/**
	 * Valid document types.
	 *
	 * @return string[]
	 */
	public static function types() {
		return array( self::TYPE_INVOICE, self::TYPE_SLIP, self::TYPE_LABEL );
	}

	/**
	 * Human title for a type.
	 *
	 * @param string $type Type.
	 * @return string
	 */
	public static function type_label( $type ) {
		switch ( $type ) {
			case self::TYPE_SLIP:
				return __( 'Packing Slip', 'invoice-packing-slip-maker' );
			case self::TYPE_LABEL:
				return __( 'Shipping Label', 'invoice-packing-slip-maker' );
			default:
				return __( 'Invoice', 'invoice-packing-slip-maker' );
		}
	}

	/**
	 * Title of this document.
	 *
	 * @return string
	 */
	public function title() {
		return self::type_label( $this->type );
	}

	/**
	 * Assign an invoice number and date the first time an invoice is generated.
	 */
	public function ensure_number() {
		if ( $this->order->get_meta( self::META_NUMBER ) ) {
			return;
		}

		$number = self::next_number();
		$date   = ( 'created' === IPSM_Settings::get( 'invoice_date' ) || ! $this->order->get_date_created() )
			? time()
			: $this->order->get_date_created()->getTimestamp();

		$this->order->update_meta_data( self::META_NUMBER, self::format_number( $number, $date ) );
		$this->order->update_meta_data( self::META_DATE, $date );
		$this->order->save();

		/* translators: %s: invoice number */
		$this->order->add_order_note( sprintf( __( 'Invoice %s created.', 'invoice-packing-slip-maker' ), $this->invoice_number() ) );
	}

	/**
	 * Atomically take the next sequential number.
	 *
	 * Uses MySQL's LAST_INSERT_ID(expr) so two simultaneous requests can never
	 * receive the same number.
	 *
	 * @return int
	 */
	public static function next_number() {
		global $wpdb;

		if ( false === get_option( IPSM_Settings::NUMBER_OPT ) ) {
			add_option( IPSM_Settings::NUMBER_OPT, 1, '', false );
		}

		if ( IPSM_Settings::enabled( 'reset_yearly' ) ) {
			$year = (int) gmdate( 'Y' );
			if ( (int) get_option( 'ipsm_number_year' ) !== $year ) {
				update_option( 'ipsm_number_year', $year, false );
				update_option( IPSM_Settings::NUMBER_OPT, 1, false );
			}
		}

		$wpdb->query(
			$wpdb->prepare(
				"UPDATE {$wpdb->options} SET option_value = LAST_INSERT_ID( CAST(option_value AS UNSIGNED) + 1 ) WHERE option_name = %s",
				IPSM_Settings::NUMBER_OPT
			)
		);
		$next = (int) $wpdb->get_var( 'SELECT LAST_INSERT_ID()' );

		wp_cache_delete( IPSM_Settings::NUMBER_OPT, 'options' );
		wp_cache_delete( 'alloptions', 'options' );

		return max( 1, $next - 1 );
	}

	/**
	 * Apply prefix/suffix/padding.
	 *
	 * @param int $number    Sequential number.
	 * @param int $timestamp Invoice date.
	 * @return string
	 */
	public static function format_number( $number, $timestamp ) {
		$replace = array(
			'{Y}' => wp_date( 'Y', $timestamp ),
			'{y}' => wp_date( 'y', $timestamp ),
			'{m}' => wp_date( 'm', $timestamp ),
		);
		$prefix  = strtr( (string) IPSM_Settings::get( 'number_prefix' ), $replace );
		$suffix  = strtr( (string) IPSM_Settings::get( 'number_suffix' ), $replace );
		$padded  = str_pad( (string) $number, (int) IPSM_Settings::get( 'number_padding' ), '0', STR_PAD_LEFT );

		return apply_filters( 'ipsm_invoice_number', $prefix . $padded . $suffix, $number, $timestamp );
	}

	/**
	 * Formatted invoice number.
	 *
	 * @return string
	 */
	public function invoice_number() {
		return (string) $this->order->get_meta( self::META_NUMBER );
	}

	/**
	 * Invoice date as a string.
	 *
	 * @return string
	 */
	public function invoice_date() {
		$ts = (int) $this->order->get_meta( self::META_DATE );
		return $ts ? wp_date( wc_date_format(), $ts ) : '';
	}

	/**
	 * Order date as a string.
	 *
	 * @return string
	 */
	public function order_date() {
		$date = $this->order->get_date_created();
		return $date ? wc_format_datetime( $date ) : '';
	}

	/**
	 * File name used for PDF downloads and attachments.
	 *
	 * @return string
	 */
	public function filename() {
		if ( self::TYPE_INVOICE === $this->type ) {
			$base = 'invoice-' . $this->invoice_number();
		} else {
			$base = $this->type . '-' . $this->order->get_order_number();
		}
		return sanitize_file_name( apply_filters( 'ipsm_filename', $base, $this ) ) . '.pdf';
	}

	/**
	 * Shop details from settings.
	 *
	 * @return array
	 */
	public function shop() {
		$logo_id = (int) IPSM_Settings::get( 'logo_id' );
		return array(
			'name'      => (string) IPSM_Settings::get( 'shop_name' ),
			'address'   => array_filter( array_map( 'trim', explode( "\n", (string) IPSM_Settings::get( 'shop_address' ) ) ) ),
			'phone'     => (string) IPSM_Settings::get( 'shop_phone' ),
			'email'     => (string) IPSM_Settings::get( 'shop_email' ),
			'tax_id'    => (string) IPSM_Settings::get( 'tax_id' ),
			'logo_url'  => $logo_id ? wp_get_attachment_image_url( $logo_id, 'medium' ) : '',
			'logo_path' => $logo_id ? self::logo_path( $logo_id ) : '',
		);
	}

	/**
	 * Local path to the logo file (medium size if available).
	 *
	 * @param int $logo_id Attachment ID.
	 * @return string
	 */
	private static function logo_path( $logo_id ) {
		$file = get_attached_file( $logo_id );
		if ( ! $file ) {
			return '';
		}
		$meta = wp_get_attachment_metadata( $logo_id );
		if ( ! empty( $meta['sizes']['medium']['file'] ) ) {
			$candidate = path_join( dirname( $file ), $meta['sizes']['medium']['file'] );
			if ( file_exists( $candidate ) ) {
				return $candidate;
			}
		}
		return file_exists( $file ) ? $file : '';
	}

	/**
	 * Billing address as an array of lines.
	 *
	 * @return string[]
	 */
	public function billing_lines() {
		return self::address_lines( $this->order->get_formatted_billing_address() );
	}

	/**
	 * Shipping address lines (falls back to billing when there is no shipping address).
	 *
	 * @return string[]
	 */
	public function shipping_lines() {
		$lines = self::address_lines( $this->order->get_formatted_shipping_address() );
		return $lines ? $lines : $this->billing_lines();
	}

	/**
	 * Whether the order has its own shipping address.
	 *
	 * @return bool
	 */
	public function has_shipping_address() {
		return (bool) $this->order->get_formatted_shipping_address();
	}

	/**
	 * Convert WC formatted address (with <br/>) into lines of plain text.
	 *
	 * @param string $formatted Formatted address.
	 * @return string[]
	 */
	private static function address_lines( $formatted ) {
		if ( ! $formatted ) {
			return array();
		}
		$lines = preg_split( '#<br\s*/?>#i', $formatted );
		$lines = array_map(
			static function ( $line ) {
				return trim( html_entity_decode( wp_strip_all_tags( $line ), ENT_QUOTES, 'UTF-8' ) );
			},
			$lines
		);
		return array_values( array_filter( $lines, 'strlen' ) );
	}

	/**
	 * Shipping phone (falls back to billing phone).
	 *
	 * @return string
	 */
	public function shipping_phone() {
		$phone = method_exists( $this->order, 'get_shipping_phone' ) ? $this->order->get_shipping_phone() : '';
		return $phone ? $phone : $this->order->get_billing_phone();
	}

	/**
	 * Line items with everything templates need.
	 *
	 * @return array[]
	 */
	public function items() {
		$items = array();
		foreach ( $this->order->get_items( 'line_item' ) as $item_id => $item ) {
			/** @var WC_Order_Item_Product $item */
			$product = $item->get_product();
			$qty     = (float) $item->get_quantity();
			$refund  = (float) $this->order->get_qty_refunded_for_item( $item_id );

			$meta_lines = array();
			foreach ( $item->get_formatted_meta_data( '_', true ) as $meta ) {
				$meta_lines[] = wp_strip_all_tags( $meta->display_key ) . ': ' . trim( html_entity_decode( wp_strip_all_tags( $meta->display_value ), ENT_QUOTES, 'UTF-8' ) );
			}

			$weight = ( $product && $product->get_weight() ) ? (float) $product->get_weight() * $qty : 0;

			$items[] = apply_filters(
				'ipsm_document_item',
				array(
					'id'         => $item_id,
					'name'       => $item->get_name(),
					'sku'        => $product ? $product->get_sku() : '',
					'meta'       => $meta_lines,
					'qty'        => $qty + $refund, // Refunded quantities are stored as negatives.
					'qty_raw'    => $qty,
					'unit_price' => $qty ? $this->order->get_item_subtotal( $item, false, true ) : 0,
					'total'      => $this->order->get_line_subtotal( $item, false, true ),
					'weight'     => $weight,
				),
				$item,
				$this
			);
		}
		return $items;
	}

	/**
	 * Totals rows (subtotal, discount, shipping, tax, total…) as label + plain value.
	 *
	 * @return array[] each: label, value (HTML), text (plain)
	 */
	public function totals() {
		$rows = array();
		foreach ( $this->order->get_order_item_totals( get_option( 'woocommerce_tax_display_cart' ) ) as $key => $row ) {
			if ( 'payment_method' === $key ) {
				continue;
			}
			// WooCommerce appends secondary info such as "(includes ₹X GST)" or
			// "via Flat rate" in <small> tags; keep it apart so PDFs can print it smaller.
			$note = '';
			if ( preg_match_all( '#<small[^>]*>(.*?)</small>#is', $row['value'], $m ) ) {
				$note = self::plain( implode( ' ', $m[1] ) );
			}
			$rows[ $key ] = array(
				'label' => rtrim( wp_strip_all_tags( $row['label'] ), ':' ),
				'value' => $row['value'],
				'text'  => self::plain( preg_replace( '#<small[^>]*>.*?</small>#is', '', $row['value'] ) ),
				'note'  => $note,
			);
		}
		return apply_filters( 'ipsm_document_totals', $rows, $this );
	}

	/**
	 * Total weight of the order.
	 *
	 * @return string
	 */
	public function total_weight() {
		$sum = 0;
		foreach ( $this->items() as $item ) {
			$sum += $item['weight'];
		}
		return $sum ? wc_format_weight( $sum ) : '';
	}

	/**
	 * Total number of units to pack.
	 *
	 * @return float
	 */
	public function total_qty() {
		$sum = 0;
		foreach ( $this->items() as $item ) {
			$sum += $item['qty'];
		}
		return $sum;
	}

	/**
	 * Price formatted as HTML.
	 *
	 * @param float $amount Amount.
	 * @return string
	 */
	public function price( $amount ) {
		return wc_price( $amount, array( 'currency' => $this->order->get_currency() ) );
	}

	/**
	 * Price as plain text.
	 *
	 * @param float $amount Amount.
	 * @return string
	 */
	public function price_text( $amount ) {
		return self::plain( $this->price( $amount ) );
	}

	/**
	 * Strip HTML and decode entities.
	 *
	 * @param string $html HTML.
	 * @return string
	 */
	public static function plain( $html ) {
		$html = preg_replace( '#<br\s*/?>#i', ' ', (string) $html );
		$text = html_entity_decode( wp_strip_all_tags( $html ), ENT_QUOTES, 'UTF-8' );
		return trim( preg_replace( '/\s+/u', ' ', str_replace( "\xc2\xa0", ' ', $text ) ) );
	}

	/**
	 * Settings-defined notes and footer.
	 *
	 * @return array
	 */
	public function texts() {
		return array(
			'notes'  => (string) IPSM_Settings::get( 'notes' ),
			'footer' => (string) IPSM_Settings::get( 'footer' ),
		);
	}
}
