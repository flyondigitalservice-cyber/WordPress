<?php
/**
 * Uninstall: remove plugin settings.
 *
 * Invoice numbers and dates stored on orders are deliberately kept, because
 * issued invoices are accounting records and must not change if the plugin is
 * reinstalled.
 *
 * @package Invoice_Packing_Slip_Maker
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

delete_option( 'ipsm_settings' );
delete_option( 'ipsm_next_invoice_number' );
delete_option( 'ipsm_number_year' );
wp_clear_scheduled_hook( 'ipsm_cleanup_temp' );
