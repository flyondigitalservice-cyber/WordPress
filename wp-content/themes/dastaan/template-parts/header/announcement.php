<?php
/**
 * Announcement bar with optional countdown.
 *
 * @package Dastaan
 */

defined( 'ABSPATH' ) || exit;

if ( ! dastaan_option( 'announcement_enable' ) ) {
	return;
}
$dastaan_text      = dastaan_option( 'announcement_text' );
$dastaan_link      = dastaan_option( 'announcement_link' );
$dastaan_link_text = dastaan_option( 'announcement_link_text' );
$dastaan_countdown = dastaan_option( 'announcement_countdown' );
$dastaan_secondary = dastaan_option( 'announcement_secondary' );
$dastaan_end_ts    = 0;
if ( $dastaan_countdown ) {
	$dastaan_dt     = date_create_immutable( $dastaan_countdown, wp_timezone() );
	$dastaan_end_ts = $dastaan_dt ? $dastaan_dt->getTimestamp() : 0;
	if ( $dastaan_end_ts && $dastaan_end_ts < time() ) {
		$dastaan_end_ts = 0; // Expired → hide countdown.
	}
}
if ( ! $dastaan_text && ! $dastaan_end_ts ) {
	return;
}
?>
<div class="announcement-bar" role="region" aria-label="<?php esc_attr_e( 'Announcement', 'dastaan' ); ?>">
	<div class="container announcement-bar__inner">
		<p class="announcement-bar__text">
			<?php echo wp_kses( $dastaan_text, dastaan_kses_inline() ); ?>
			<?php if ( $dastaan_link && $dastaan_link_text ) : ?>
				<a class="announcement-bar__link" href="<?php echo dastaan_url( $dastaan_link ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>"><?php echo esc_html( $dastaan_link_text ); ?> <?php dastaan_the_icon( 'arrow-right', array( 'size' => 14 ) ); ?></a>
			<?php endif; ?>
		</p>
		<?php if ( $dastaan_end_ts ) : ?>
			<div class="countdown" data-end="<?php echo esc_attr( $dastaan_end_ts ); ?>" aria-live="off">
				<span class="countdown__unit"><b data-u="d">00</b><small><?php esc_html_e( 'days', 'dastaan' ); ?></small></span>
				<span class="countdown__unit"><b data-u="h">00</b><small><?php esc_html_e( 'hrs', 'dastaan' ); ?></small></span>
				<span class="countdown__unit"><b data-u="m">00</b><small><?php esc_html_e( 'min', 'dastaan' ); ?></small></span>
				<span class="countdown__unit"><b data-u="s">00</b><small><?php esc_html_e( 'sec', 'dastaan' ); ?></small></span>
			</div>
		<?php endif; ?>
		<?php if ( $dastaan_secondary ) : ?>
			<p class="announcement-bar__secondary"><?php echo wp_kses( $dastaan_secondary, dastaan_kses_inline() ); ?></p>
		<?php endif; ?>
	</div>
</div>
