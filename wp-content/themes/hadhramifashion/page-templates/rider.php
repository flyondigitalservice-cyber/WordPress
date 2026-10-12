<?php
/**
 * Template Name: Rider
 *
 * Opened by the delivery rider from the link in the order screen. Shares the
 * phone's GPS position with the customer's tracking page.
 *
 * @package HadhramiFashion
 */

get_header();
$hf_key   = isset( $_GET['order'] ) ? preg_replace( '/[^A-Za-z0-9]/', '', sanitize_text_field( wp_unslash( $_GET['order'] ) ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
$hf_token = isset( $_GET['token'] ) ? preg_replace( '/[^A-Za-z0-9]/', '', sanitize_text_field( wp_unslash( $_GET['token'] ) ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
?>
<div class="rider-app" data-rider="<?php echo esc_attr( $hf_key ); ?>" data-token="<?php echo esc_attr( $hf_token ); ?>">
	<span class="eyebrow eyebrow--plain"><?php esc_html_e( 'Rider', 'hadhramifashion' ); ?></span>
	<h2 data-rider-number><?php esc_html_e( 'Delivery', 'hadhramifashion' ); ?></h2>
	<?php if ( ! $hf_key || ! $hf_token ) : ?>
		<p class="lede"><?php esc_html_e( 'Open this page from the rider link in the order.', 'hadhramifashion' ); ?></p>
	<?php else : ?>
		<p class="lede" data-rider-address></p>
		<div class="map" data-map="rider"><div class="map-placeholder" data-map-placeholder><span><?php esc_html_e( 'Loading map…', 'hadhramifashion' ); ?></span></div></div>
		<p class="rider-status" data-rider-status><?php esc_html_e( 'Location sharing is off.', 'hadhramifashion' ); ?></p>
		<button class="btn" type="button" data-rider-start><?php echo hf_icon( 'rider' ); // phpcs:ignore WordPress.Security.EscapeOutput ?> <?php esc_html_e( 'Start delivery & share location', 'hadhramifashion' ); ?></button>
		<a class="btn btn--ghost" data-rider-nav target="_blank" rel="noopener" href="#"><?php echo hf_icon( 'pin' ); // phpcs:ignore WordPress.Security.EscapeOutput ?> <?php esc_html_e( 'Navigate in Google Maps', 'hadhramifashion' ); ?></a>
		<button class="btn btn--ghost" type="button" data-rider-done><?php echo hf_icon( 'check' ); // phpcs:ignore WordPress.Security.EscapeOutput ?> <?php esc_html_e( 'Mark as delivered', 'hadhramifashion' ); ?></button>
	<?php endif; ?>
</div>
<?php
get_footer();
