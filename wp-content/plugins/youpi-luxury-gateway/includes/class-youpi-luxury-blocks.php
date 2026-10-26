<?php
/**
 * WooCommerce block checkout integration.
 *
 * @package YouPI_Luxury_Gateway
 */

defined( 'ABSPATH' ) || exit;

use Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType;

/**
 * Exposes the gateway to the Cart & Checkout blocks.
 */
final class Youpi_Luxury_Blocks extends AbstractPaymentMethodType {

	/**
	 * Payment method name; must match the gateway id.
	 *
	 * @var string
	 */
	protected $name = YOUPI_LUXURY_GATEWAY_ID;

	/**
	 * Load settings.
	 */
	public function initialize() {
		$this->settings = get_option( 'woocommerce_' . YOUPI_LUXURY_GATEWAY_ID . '_settings', array() );
	}

	/**
	 * Whether the method is active. Cart-level checks (currency, ceiling) run in the gateway's is_available().
	 *
	 * @return bool
	 */
	public function is_active() {
		$gateway = WC_Gateway_YouPI_Luxury::instance();
		return $gateway && $gateway->is_available();
	}

	/**
	 * Script handles for the checkout block.
	 *
	 * @return string[]
	 */
	public function get_payment_method_script_handles() {
		wp_register_script(
			'youpi-luxury-blocks',
			YOUPI_LUXURY_URL . 'assets/js/youpi-luxury-blocks.js',
			array( 'wc-blocks-registry', 'wc-settings', 'wp-element', 'wp-html-entities' ),
			YOUPI_LUXURY_VERSION,
			true
		);
		wp_enqueue_style( 'youpi-luxury', YOUPI_LUXURY_URL . 'assets/css/youpi-luxury.css', array(), YOUPI_LUXURY_VERSION );
		return array( 'youpi-luxury-blocks' );
	}

	/**
	 * Data passed to the script.
	 *
	 * @return array
	 */
	public function get_payment_method_data() {
		$gateway = WC_Gateway_YouPI_Luxury::instance();
		return array(
			'title'       => $this->get_setting( 'title' ),
			'description' => $gateway ? $gateway->get_description() : $this->get_setting( 'description' ),
			'supports'    => array( 'products' ),
		);
	}
}
