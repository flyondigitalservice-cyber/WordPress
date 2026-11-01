<?php
/**
 * Admin integration: order screen meta box, order list buttons and bulk actions.
 * Works with both the legacy posts-based order screens and HPOS.
 *
 * @package Invoice_Packing_Slip_Maker
 */

defined( 'ABSPATH' ) || exit;

/**
 * Admin.
 */
class IPSM_Admin {

	/**
	 * Hooks.
	 */
	public static function init() {
		add_action( 'add_meta_boxes', array( __CLASS__, 'add_meta_box' ), 30 );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'assets' ) );
		add_action( 'admin_post_ipsm_email_invoice', array( __CLASS__, 'handle_email' ) );
		add_action( 'admin_notices', array( __CLASS__, 'notices' ) );
		add_filter( 'woocommerce_admin_order_actions', array( __CLASS__, 'list_buttons' ), 20, 2 );

		foreach ( array( 'edit-shop_order', 'woocommerce_page_wc-orders' ) as $screen ) {
			add_filter( 'bulk_actions-' . $screen, array( __CLASS__, 'bulk_actions' ) );
			add_filter( 'handle_bulk_actions-' . $screen, array( __CLASS__, 'handle_bulk' ), 10, 3 );
		}
	}

	/**
	 * Order edit screen IDs.
	 *
	 * @return string[]
	 */
	private static function order_screens() {
		$screens = array( 'shop_order', 'woocommerce_page_wc-orders' );
		if ( function_exists( 'wc_get_page_screen_id' ) ) {
			$screens[] = wc_get_page_screen_id( 'shop-order' );
		}
		return array_unique( $screens );
	}

	/**
	 * Enqueue CSS/JS on order screens.
	 */
	public static function assets() {
		$screen = get_current_screen();
		if ( ! $screen || ! in_array( $screen->id, array_merge( self::order_screens(), array( 'edit-shop_order' ) ), true ) ) {
			return;
		}
		wp_enqueue_style( 'ipsm-admin', IPSM_URL . 'assets/css/admin.css', array(), IPSM_VERSION );
		wp_enqueue_script( 'ipsm-admin', IPSM_URL . 'assets/js/admin.js', array( 'jquery' ), IPSM_VERSION, true );
	}

	/**
	 * Register the meta box.
	 */
	public static function add_meta_box() {
		foreach ( self::order_screens() as $screen ) {
			add_meta_box( 'ipsm-documents', __( 'Invoice & Packing Slip', 'invoice-packing-slip-maker' ), array( __CLASS__, 'render_meta_box' ), $screen, 'side', 'high' );
		}
	}

	/**
	 * Meta box content.
	 *
	 * @param WP_Post|WC_Order $post_or_order Post or order.
	 */
	public static function render_meta_box( $post_or_order ) {
		$order = $post_or_order instanceof WC_Order ? $post_or_order : wc_get_order( $post_or_order->ID );
		if ( ! $order || ! $order->get_id() ) {
			echo '<p>' . esc_html__( 'Save the order first.', 'invoice-packing-slip-maker' ) . '</p>';
			return;
		}

		$id     = $order->get_id();
		$number = $order->get_meta( IPSM_Document::META_NUMBER );
		$date   = (int) $order->get_meta( IPSM_Document::META_DATE );
		$sent   = (int) $order->get_meta( IPSM_Document::META_SENT );

		echo '<div class="ipsm-box">';
		if ( $number ) {
			echo '<p class="ipsm-status"><strong>' . esc_html( $number ) . '</strong>';
			echo $date ? '<br /><span>' . esc_html( wp_date( wc_date_format(), $date ) ) . '</span>' : '';
			if ( $sent ) {
				/* translators: %s: date */
				echo '<br /><span class="ipsm-sent">' . esc_html( sprintf( __( 'Emailed %s', 'invoice-packing-slip-maker' ), wp_date( wc_date_format() . ' ' . wc_time_format(), $sent ) ) ) . '</span>';
			}
			echo '</p>';
		} else {
			echo '<p class="ipsm-status description">' . esc_html__( 'No invoice yet — one is numbered the first time you open, download or email it.', 'invoice-packing-slip-maker' ) . '</p>';
		}

		$email_url = wp_nonce_url( admin_url( 'admin-post.php?action=ipsm_email_invoice&order_id=' . $id ), 'ipsm_email_' . $id );
		$groups    = array(
			IPSM_Document::TYPE_INVOICE => __( 'Invoice', 'invoice-packing-slip-maker' ),
			IPSM_Document::TYPE_SLIP    => __( 'Packing slip', 'invoice-packing-slip-maker' ),
			IPSM_Document::TYPE_LABEL   => __( 'Shipping label', 'invoice-packing-slip-maker' ),
		);

		foreach ( $groups as $type => $label ) {
			echo '<div class="ipsm-row"><span class="ipsm-row-label">' . esc_html( $label ) . '</span><span class="ipsm-row-buttons">';
			printf(
				'<a class="button" target="_blank" href="%s" title="%s"><span class="dashicons dashicons-printer"></span> %s</a>',
				esc_url( IPSM_Renderer::url( $id, $type, 'html', array( 'autoprint' => 1 ) ) ),
				esc_attr__( 'Open print view', 'invoice-packing-slip-maker' ),
				esc_html__( 'Print', 'invoice-packing-slip-maker' )
			);
			printf(
				'<a class="button" href="%s" title="%s"><span class="dashicons dashicons-pdf"></span> PDF</a>',
				esc_url( IPSM_Renderer::url( $id, $type, 'pdf' ) ),
				esc_attr__( 'Download PDF', 'invoice-packing-slip-maker' )
			);
			echo '</span></div>';
		}

		echo '<p class="ipsm-actions">';
		printf(
			'<a class="button button-primary ipsm-email" href="%s" data-confirm="%s"><span class="dashicons dashicons-email-alt"></span> %s</a>',
			esc_url( $email_url ),
			/* translators: %s: email address */
			esc_attr( sprintf( __( 'Email the invoice PDF to %s?', 'invoice-packing-slip-maker' ), $order->get_billing_email() ) ),
			$sent ? esc_html__( 'Resend invoice to customer', 'invoice-packing-slip-maker' ) : esc_html__( 'Email invoice to customer', 'invoice-packing-slip-maker' )
		);
		echo '</p>';
		printf(
			'<p class="ipsm-small"><a href="%s" target="_blank">%s</a></p>',
			esc_url( IPSM_Renderer::url( $id, IPSM_Document::TYPE_INVOICE, 'html' ) ),
			esc_html__( 'Preview invoice', 'invoice-packing-slip-maker' )
		);
		echo '</div>';
	}

	/**
	 * Send the invoice from the meta box.
	 */
	public static function handle_email() {
		$order_id = isset( $_GET['order_id'] ) ? absint( $_GET['order_id'] ) : 0;
		check_admin_referer( 'ipsm_email_' . $order_id );
		if ( ! IPSM_Renderer::is_shop_manager() ) {
			wp_die( esc_html__( 'You are not allowed to do this.', 'invoice-packing-slip-maker' ), '', array( 'response' => 403 ) );
		}
		$order = wc_get_order( $order_id );
		$sent  = $order ? IPSM_Mailer::send_invoice( $order ) : false;

		$back = wp_get_referer();
		if ( ! $back && $order ) {
			$back = $order->get_edit_order_url();
		}
		wp_safe_redirect( add_query_arg( $sent ? 'ipsm_emailed' : 'ipsm_email_failed', 1, $back ? $back : admin_url() ) );
		exit;
	}

	/**
	 * Bulk actions on the orders list.
	 *
	 * @param array $actions Actions.
	 * @return array
	 */
	public static function bulk_actions( $actions ) {
		$actions['ipsm_print_invoice'] = __( 'Print invoices', 'invoice-packing-slip-maker' );
		$actions['ipsm_pdf_invoice']   = __( 'Download invoices (PDF)', 'invoice-packing-slip-maker' );
		$actions['ipsm_email_invoice'] = __( 'Email invoices to customers', 'invoice-packing-slip-maker' );
		$actions['ipsm_csv_invoice']   = __( 'Export invoices (CSV)', 'invoice-packing-slip-maker' );
		$actions['ipsm_print_slip']    = __( 'Print packing slips', 'invoice-packing-slip-maker' );
		$actions['ipsm_pdf_slip']      = __( 'Download packing slips (PDF)', 'invoice-packing-slip-maker' );
		$actions['ipsm_print_label']   = __( 'Print shipping labels', 'invoice-packing-slip-maker' );
		$actions['ipsm_pdf_label']     = __( 'Download shipping labels (PDF)', 'invoice-packing-slip-maker' );
		return $actions;
	}

	/**
	 * Handle bulk actions.
	 *
	 * @param string $redirect Redirect URL.
	 * @param string $action   Action.
	 * @param int[]  $ids      Order IDs.
	 * @return string
	 */
	public static function handle_bulk( $redirect, $action, $ids ) {
		if ( 0 !== strpos( $action, 'ipsm_' ) || ! IPSM_Renderer::is_shop_manager() ) {
			return $redirect;
		}
		$ids = array_map( 'absint', (array) $ids );
		if ( ! $ids ) {
			return $redirect;
		}

		if ( 'ipsm_email_invoice' === $action ) {
			$count = 0;
			foreach ( $ids as $id ) {
				$order = wc_get_order( $id );
				if ( $order && IPSM_Mailer::send_invoice( $order ) ) {
					++$count;
				}
			}
			return add_query_arg( 'ipsm_bulk_emailed', $count, $redirect );
		}

		$map = array(
			'ipsm_print_invoice' => array( IPSM_Document::TYPE_INVOICE, 'html' ),
			'ipsm_pdf_invoice'   => array( IPSM_Document::TYPE_INVOICE, 'pdf' ),
			'ipsm_csv_invoice'   => array( IPSM_Document::TYPE_INVOICE, 'csv' ),
			'ipsm_print_slip'    => array( IPSM_Document::TYPE_SLIP, 'html' ),
			'ipsm_pdf_slip'      => array( IPSM_Document::TYPE_SLIP, 'pdf' ),
			'ipsm_print_label'   => array( IPSM_Document::TYPE_LABEL, 'html' ),
			'ipsm_pdf_label'     => array( IPSM_Document::TYPE_LABEL, 'pdf' ),
		);
		if ( ! isset( $map[ $action ] ) ) {
			return $redirect;
		}
		list( $type, $output ) = $map[ $action ];
		$extra                 = 'html' === $output ? array( 'autoprint' => 1 ) : array();
		return IPSM_Renderer::url( $ids, $type, $output, $extra );
	}

	/**
	 * Quick buttons in the orders list "Actions" column.
	 *
	 * @param array    $actions Actions.
	 * @param WC_Order $order   Order.
	 * @return array
	 */
	public static function list_buttons( $actions, $order ) {
		$actions['ipsm-invoice'] = array(
			'url'    => IPSM_Renderer::url( $order->get_id(), IPSM_Document::TYPE_INVOICE, 'pdf' ),
			'name'   => __( 'Invoice PDF', 'invoice-packing-slip-maker' ),
			'action' => 'ipsm-invoice',
		);
		$actions['ipsm-slip']    = array(
			'url'    => IPSM_Renderer::url( $order->get_id(), IPSM_Document::TYPE_SLIP, 'html', array( 'autoprint' => 1 ) ),
			'name'   => __( 'Print packing slip', 'invoice-packing-slip-maker' ),
			'action' => 'ipsm-slip',
		);
		return $actions;
	}

	/**
	 * Admin notices after actions.
	 */
	public static function notices() {
		// phpcs:disable WordPress.Security.NonceVerification.Recommended -- display only.
		if ( isset( $_GET['ipsm_emailed'] ) ) {
			echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Invoice emailed to the customer.', 'invoice-packing-slip-maker' ) . '</p></div>';
		}
		if ( isset( $_GET['ipsm_email_failed'] ) ) {
			echo '<div class="notice notice-error is-dismissible"><p>' . esc_html__( 'The invoice could not be emailed. Check the customer has a billing email and that the "Invoice (PDF)" email is enabled in WooCommerce → Settings → Emails.', 'invoice-packing-slip-maker' ) . '</p></div>';
		}
		if ( isset( $_GET['ipsm_bulk_emailed'] ) ) {
			$count = absint( $_GET['ipsm_bulk_emailed'] );
			/* translators: %d: number of invoices */
			echo '<div class="notice notice-success is-dismissible"><p>' . esc_html( sprintf( _n( '%d invoice emailed.', '%d invoices emailed.', $count, 'invoice-packing-slip-maker' ), $count ) ) . '</p></div>';
		}
		// phpcs:enable
	}
}
