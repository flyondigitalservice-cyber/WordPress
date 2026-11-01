<?php
/**
 * Admin integration: order screen box, orders list column / buttons and bulk
 * actions. Works with both the legacy posts-based order screens and HPOS.
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
		add_action( 'wp_ajax_ipsm_email_invoice', array( __CLASS__, 'ajax_email' ) );
		add_action( 'admin_notices', array( __CLASS__, 'notices' ) );
		add_filter( 'woocommerce_admin_order_actions', array( __CLASS__, 'list_buttons' ), 20, 2 );

		foreach ( array( 'edit-shop_order', 'woocommerce_page_wc-orders' ) as $screen ) {
			add_filter( 'bulk_actions-' . $screen, array( __CLASS__, 'bulk_actions' ) );
			add_filter( 'handle_bulk_actions-' . $screen, array( __CLASS__, 'handle_bulk' ), 10, 3 );
		}

		// "Invoice" column on the orders list (legacy + HPOS).
		add_filter( 'manage_edit-shop_order_columns', array( __CLASS__, 'add_column' ), 20 );
		add_action( 'manage_shop_order_posts_custom_column', array( __CLASS__, 'render_column_legacy' ), 10, 2 );
		add_filter( 'manage_woocommerce_page_wc-orders_columns', array( __CLASS__, 'add_column' ), 20 );
		add_action( 'manage_woocommerce_page_wc-orders_custom_column', array( __CLASS__, 'render_column' ), 10, 2 );
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
	 * Data for admin.js.
	 *
	 * @return array
	 */
	public static function script_data() {
		return array(
			'ajaxUrl'    => admin_url( 'admin-ajax.php' ),
			'docUrl'     => add_query_arg(
				array(
					'action'   => IPSM_Renderer::ACTION,
					'_wpnonce' => wp_create_nonce( IPSM_Renderer::NONCE ),
				),
				admin_url( 'admin-ajax.php' )
			),
			'emailNonce' => wp_create_nonce( 'ipsm_email_ajax' ),
			'bulk'       => array(
				'ipsm_print_invoice' => array( IPSM_Document::TYPE_INVOICE, 'html' ),
				'ipsm_pdf_invoice'   => array( IPSM_Document::TYPE_INVOICE, 'pdf' ),
				'ipsm_csv_invoice'   => array( IPSM_Document::TYPE_INVOICE, 'csv' ),
				'ipsm_print_slip'    => array( IPSM_Document::TYPE_SLIP, 'html' ),
				'ipsm_pdf_slip'      => array( IPSM_Document::TYPE_SLIP, 'pdf' ),
				'ipsm_print_label'   => array( IPSM_Document::TYPE_LABEL, 'html' ),
				'ipsm_pdf_label'     => array( IPSM_Document::TYPE_LABEL, 'pdf' ),
			),
			'i18n'       => array(
				'sending'    => __( 'Sending…', 'invoice-packing-slip-maker' ),
				'sent'       => __( 'Sent!', 'invoice-packing-slip-maker' ),
				'failed'     => __( 'Could not send — see the order notes.', 'invoice-packing-slip-maker' ),
				'noneChosen' => __( 'Please tick at least one order first.', 'invoice-packing-slip-maker' ),
				'popup'      => __( 'Your browser blocked the new tab. Please allow pop-ups for this site and try again.', 'invoice-packing-slip-maker' ),
				'chooseLogo' => __( 'Choose your logo', 'invoice-packing-slip-maker' ),
				'useLogo'    => __( 'Use this logo', 'invoice-packing-slip-maker' ),
				'leave'      => __( 'You have unsaved changes.', 'invoice-packing-slip-maker' ),
			),
		);
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
		wp_localize_script( 'ipsm-admin', 'ipsmAdmin', self::script_data() );
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
	 * Status block HTML (also returned after an AJAX send).
	 *
	 * @param WC_Order $order Order.
	 * @return string
	 */
	public static function status_html( WC_Order $order ) {
		$number = $order->get_meta( IPSM_Document::META_NUMBER );
		$date   = (int) $order->get_meta( IPSM_Document::META_DATE );
		$sent   = (int) $order->get_meta( IPSM_Document::META_SENT );

		ob_start();
		if ( $number ) {
			echo '<div class="ipsm-mb-status is-issued">';
			echo '<span class="ipsm-mb-label">' . esc_html__( 'Invoice', 'invoice-packing-slip-maker' ) . '</span>';
			echo '<strong class="ipsm-mb-number">' . esc_html( $number ) . '</strong>';
			echo $date ? '<span class="ipsm-mb-date">' . esc_html( wp_date( wc_date_format(), $date ) ) . '</span>' : '';
			if ( $sent ) {
				/* translators: %s: date and time */
				echo '<span class="ipsm-badge is-success"><span class="dashicons dashicons-yes"></span>' . esc_html( sprintf( __( 'Emailed %s', 'invoice-packing-slip-maker' ), wp_date( 'j M, H:i', $sent ) ) ) . '</span>';
			} else {
				echo '<span class="ipsm-badge">' . esc_html__( 'Not emailed yet', 'invoice-packing-slip-maker' ) . '</span>';
			}
			echo '</div>';
		} else {
			echo '<div class="ipsm-mb-status is-new">';
			echo '<span class="ipsm-mb-label">' . esc_html__( 'Invoice', 'invoice-packing-slip-maker' ) . '</span>';
			echo '<strong class="ipsm-mb-number">' . esc_html__( 'Not created yet', 'invoice-packing-slip-maker' ) . '</strong>';
			echo '<span class="ipsm-mb-date">' . esc_html__( 'It gets its number the first time you print, download or email it.', 'invoice-packing-slip-maker' ) . '</span>';
			echo '</div>';
		}
		return ob_get_clean();
	}

	/**
	 * Meta box content.
	 *
	 * @param WP_Post|WC_Order $post_or_order Post or order.
	 */
	public static function render_meta_box( $post_or_order ) {
		$order = $post_or_order instanceof WC_Order ? $post_or_order : wc_get_order( $post_or_order->ID );
		if ( ! $order || ! $order->get_id() || 'auto-draft' === $order->get_status() ) {
			echo '<p class="ipsm-mb-empty">' . esc_html__( 'Create the order first — then you can print and email documents here.', 'invoice-packing-slip-maker' ) . '</p>';
			return;
		}

		$id        = $order->get_id();
		$sent      = (int) $order->get_meta( IPSM_Document::META_SENT );
		$email_url = wp_nonce_url( admin_url( 'admin-post.php?action=ipsm_email_invoice&order_id=' . $id ), 'ipsm_email_' . $id );
		$docs      = array(
			IPSM_Document::TYPE_INVOICE => array( __( 'Invoice', 'invoice-packing-slip-maker' ), 'dashicons-media-text' ),
			IPSM_Document::TYPE_SLIP    => array( __( 'Packing slip', 'invoice-packing-slip-maker' ), 'dashicons-clipboard' ),
			IPSM_Document::TYPE_LABEL   => array( __( 'Shipping label', 'invoice-packing-slip-maker' ), 'dashicons-tag' ),
		);
		?>
		<div class="ipsm-mb" data-order="<?php echo esc_attr( $id ); ?>">
			<div class="ipsm-mb-status-wrap"><?php echo self::status_html( $order ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in status_html(). ?></div>

			<ul class="ipsm-mb-docs">
				<?php foreach ( $docs as $type => $doc ) : ?>
					<li>
						<span class="ipsm-mb-icon dashicons <?php echo esc_attr( $doc[1] ); ?>" aria-hidden="true"></span>
						<span class="ipsm-mb-name"><?php echo esc_html( $doc[0] ); ?></span>
						<a class="ipsm-icon-btn" target="_blank" rel="noopener" href="<?php echo esc_url( IPSM_Renderer::url( $id, $type, 'html', array( 'autoprint' => 1 ) ) ); ?>" title="<?php echo esc_attr( sprintf( /* translators: %s: document name */ __( 'Print %s', 'invoice-packing-slip-maker' ), strtolower( $doc[0] ) ) ); ?>">
							<span class="dashicons dashicons-printer"></span><span class="ipsm-icon-btn-text"><?php esc_html_e( 'Print', 'invoice-packing-slip-maker' ); ?></span>
						</a>
						<a class="ipsm-icon-btn" href="<?php echo esc_url( IPSM_Renderer::url( $id, $type, 'pdf' ) ); ?>" title="<?php echo esc_attr( sprintf( /* translators: %s: document name */ __( 'Download %s as PDF', 'invoice-packing-slip-maker' ), strtolower( $doc[0] ) ) ); ?>">
							<span class="dashicons dashicons-download"></span><span class="ipsm-icon-btn-text">PDF</span>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>

			<?php if ( $order->get_billing_email() ) : ?>
				<a class="ipsm-btn ipsm-btn-primary ipsm-btn-block ipsm-email" href="<?php echo esc_url( $email_url ); ?>" data-order="<?php echo esc_attr( $id ); ?>"
					data-confirm="<?php echo esc_attr( sprintf( /* translators: %s: email address */ __( 'Email the invoice PDF to %s?', 'invoice-packing-slip-maker' ), $order->get_billing_email() ) ); ?>">
					<span class="dashicons dashicons-email-alt"></span>
					<span class="ipsm-email-text"><?php echo $sent ? esc_html__( 'Resend invoice', 'invoice-packing-slip-maker' ) : esc_html__( 'Email invoice to customer', 'invoice-packing-slip-maker' ); ?></span>
				</a>
				<p class="ipsm-mb-to"><?php /* translators: %s: email */ echo esc_html( sprintf( __( 'to %s', 'invoice-packing-slip-maker' ), $order->get_billing_email() ) ); ?></p>
			<?php else : ?>
				<p class="ipsm-mb-to ipsm-mb-warn"><span class="dashicons dashicons-info-outline"></span><?php esc_html_e( 'Add a billing email to this order to email the invoice.', 'invoice-packing-slip-maker' ); ?></p>
			<?php endif; ?>

			<a class="ipsm-mb-settings" href="<?php echo esc_url( admin_url( 'admin.php?page=' . IPSM_Settings::PAGE ) ); ?>"><span class="dashicons dashicons-admin-generic"></span><?php esc_html_e( 'Invoice settings', 'invoice-packing-slip-maker' ); ?></a>
		</div>
		<?php
	}

	/**
	 * Send the invoice via AJAX from the order box (no page reload, so unsaved
	 * edits on the order screen are not lost).
	 */
	public static function ajax_email() {
		check_ajax_referer( 'ipsm_email_ajax', 'nonce' );
		if ( ! IPSM_Renderer::is_shop_manager() ) {
			wp_send_json_error( array( 'message' => __( 'You are not allowed to do this.', 'invoice-packing-slip-maker' ) ), 403 );
		}
		$order = wc_get_order( isset( $_POST['order_id'] ) ? absint( $_POST['order_id'] ) : 0 );
		if ( ! $order ) {
			wp_send_json_error( array( 'message' => __( 'Order not found.', 'invoice-packing-slip-maker' ) ), 404 );
		}

		$sent  = IPSM_Mailer::send_invoice( $order );
		$order = wc_get_order( $order->get_id() ); // Fresh copy with the new meta.
		$data  = array( 'status' => self::status_html( $order ) );

		if ( $sent ) {
			wp_send_json_success( $data );
		}
		$data['message'] = IPSM_Settings::invoice_email_enabled()
			? __( 'The email could not be sent. Check the order notes and your site\'s email setup.', 'invoice-packing-slip-maker' )
			: __( 'The "Invoice (PDF)" email is switched off in WooCommerce → Settings → Emails.', 'invoice-packing-slip-maker' );
		wp_send_json_error( $data );
	}

	/**
	 * Send the invoice without JavaScript (fallback for the order box button).
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
	 * Add the "Invoice" column after the order status.
	 *
	 * @param array $columns Columns.
	 * @return array
	 */
	public static function add_column( $columns ) {
		$out = array();
		foreach ( $columns as $key => $label ) {
			$out[ $key ] = $label;
			if ( 'order_status' === $key ) {
				$out['ipsm_invoice'] = __( 'Invoice', 'invoice-packing-slip-maker' );
			}
		}
		if ( ! isset( $out['ipsm_invoice'] ) ) {
			$out['ipsm_invoice'] = __( 'Invoice', 'invoice-packing-slip-maker' );
		}
		return $out;
	}

	/**
	 * Column content (legacy screen passes a post ID).
	 *
	 * @param string $column  Column.
	 * @param int    $post_id Post ID.
	 */
	public static function render_column_legacy( $column, $post_id ) {
		if ( 'ipsm_invoice' === $column ) {
			self::render_column( $column, wc_get_order( $post_id ) );
		}
	}

	/**
	 * Column content.
	 *
	 * @param string   $column Column.
	 * @param WC_Order $order  Order.
	 */
	public static function render_column( $column, $order ) {
		if ( 'ipsm_invoice' !== $column || ! $order instanceof WC_Order ) {
			return;
		}
		$number = $order->get_meta( IPSM_Document::META_NUMBER );
		if ( ! $number ) {
			echo '<span class="ipsm-col-none">—</span>';
			return;
		}
		printf(
			'<a class="ipsm-col-number" href="%s" target="_blank" rel="noopener">%s</a>',
			esc_url( IPSM_Renderer::url( $order->get_id(), IPSM_Document::TYPE_INVOICE, 'html' ) ),
			esc_html( $number )
		);
		if ( $order->get_meta( IPSM_Document::META_SENT ) ) {
			echo ' <span class="ipsm-col-sent" title="' . esc_attr__( 'Emailed to customer', 'invoice-packing-slip-maker' ) . '"><span class="dashicons dashicons-email-alt"></span></span>';
		}
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
	 * Handle bulk actions (server-side fallback; with JavaScript the print and
	 * download actions open directly in a new tab instead).
	 *
	 * @param string $redirect Redirect URL.
	 * @param string $action   Action.
	 * @param int[]  $ids      Order IDs.
	 * @return string
	 */
	public static function handle_bulk( $redirect, $action, $ids ) {
		if ( 0 !== strpos( (string) $action, 'ipsm_' ) || ! IPSM_Renderer::is_shop_manager() ) {
			return $redirect;
		}
		$ids = array_filter( array_map( 'absint', (array) $ids ) );
		if ( ! $ids ) {
			return $redirect;
		}

		if ( 'ipsm_email_invoice' === $action ) {
			$sent   = 0;
			$failed = 0;
			foreach ( $ids as $id ) {
				$order = wc_get_order( $id );
				if ( $order && IPSM_Mailer::send_invoice( $order ) ) {
					++$sent;
				} else {
					++$failed;
				}
			}
			return add_query_arg(
				array(
					'ipsm_bulk_emailed' => $sent,
					'ipsm_bulk_failed'  => $failed,
				),
				$redirect
			);
		}

		$map = self::script_data()['bulk'];
		if ( ! isset( $map[ $action ] ) ) {
			return $redirect;
		}
		list( $type, $output ) = $map[ $action ];
		return IPSM_Renderer::url( $ids, $type, $output, 'html' === $output ? array( 'autoprint' => 1 ) : array() );
	}

	/**
	 * Quick buttons in the orders list "Actions" column.
	 *
	 * @param array    $actions Actions.
	 * @param WC_Order $order   Order.
	 * @return array
	 */
	public static function list_buttons( $actions, $order ) {
		if ( ! $order instanceof WC_Order ) {
			return $actions;
		}
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
			$count  = absint( $_GET['ipsm_bulk_emailed'] );
			$failed = isset( $_GET['ipsm_bulk_failed'] ) ? absint( $_GET['ipsm_bulk_failed'] ) : 0;
			/* translators: %d: number of invoices */
			$message = sprintf( _n( '%d invoice emailed.', '%d invoices emailed.', $count, 'invoice-packing-slip-maker' ), $count );
			if ( $failed ) {
				/* translators: %d: number of orders */
				$message .= ' ' . sprintf( _n( '%d could not be sent (no billing email, or the Invoice email is switched off).', '%d could not be sent (no billing email, or the Invoice email is switched off).', $failed, 'invoice-packing-slip-maker' ), $failed );
			}
			printf( '<div class="notice notice-%s is-dismissible"><p>%s</p></div>', $failed ? 'warning' : 'success', esc_html( $message ) );
		}
		// phpcs:enable
	}
}
