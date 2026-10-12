<?php
/**
 * Studio visit strip.
 *
 * @package Dastaan
 */

defined( 'ABSPATH' ) || exit;

$dastaan_force = ! empty( $args['force'] );
if ( ! $dastaan_force && ! dastaan_option( 'studio_enable' ) ) {
	return;
}
$dastaan_url = dastaan_option( 'studio_url' ) ? dastaan_url( dastaan_option( 'studio_url' ) ) : dastaan_whatsapp_url( __( 'Hi Dastaan, I would like to book a studio appointment.', 'dastaan' ) );
?>
<section class="section studio">
	<div class="container studio__inner">
		<?php if ( dastaan_option( 'studio_image' ) ) : ?>
			<figure class="studio__media"><?php echo dastaan_img( dastaan_option( 'studio_image' ), dastaan_option( 'studio_title' ), array( 'size' => 'large' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></figure>
		<?php endif; ?>
		<div class="studio__copy">
			<p class="eyebrow"><?php dastaan_the_icon( 'map-pin', array( 'size' => 14 ) ); ?> <?php echo esc_html( dastaan_option( 'studio_address' ) ); ?></p>
			<h2 class="section-title"><?php echo esc_html( dastaan_option( 'studio_title' ) ); ?></h2>
			<p class="section-text"><?php echo esc_html( dastaan_option( 'studio_text' ) ); ?></p>
			<p class="studio__hours"><?php dastaan_the_icon( 'clock', array( 'size' => 14 ) ); ?> <?php echo esc_html( dastaan_option( 'studio_hours' ) ); ?></p>
			<?php if ( $dastaan_url && dastaan_option( 'studio_button' ) ) : ?>
				<a class="btn btn--primary" href="<?php echo $dastaan_url; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by helpers ?>" <?php echo false !== strpos( $dastaan_url, 'wa.me' ) ? 'target="_blank" rel="noopener"' : ''; ?>><?php echo esc_html( dastaan_option( 'studio_button' ) ); ?></a>
			<?php endif; ?>
		</div>
	</div>
</section>
