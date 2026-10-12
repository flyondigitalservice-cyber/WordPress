<?php
/**
 * Newsletter strip.
 *
 * @package Dastaan
 */

defined( 'ABSPATH' ) || exit;

$dastaan_force = ! empty( $args['force'] );
if ( ! $dastaan_force && ! dastaan_option( 'newsletter_enable' ) ) {
	return;
}
$dastaan_action = dastaan_option( 'newsletter_action' );
$dastaan_status = isset( $_GET['newsletter'] ) ? sanitize_key( wp_unslash( $_GET['newsletter'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
?>
<section class="newsletter" aria-labelledby="newsletter-title">
	<div class="container newsletter__inner">
		<div class="newsletter__copy">
			<p class="eyebrow eyebrow--light"><?php esc_html_e( 'Newsletter', 'dastaan' ); ?></p>
			<h2 id="newsletter-title" class="newsletter__title"><?php echo esc_html( dastaan_option( 'newsletter_title' ) ); ?></h2>
			<p class="newsletter__text"><?php echo esc_html( dastaan_option( 'newsletter_text' ) ); ?></p>
		</div>
		<?php if ( $dastaan_action ) : ?>
			<form class="newsletter__form" action="<?php echo esc_url( $dastaan_action ); ?>" method="post" target="_blank" rel="noopener">
				<label class="screen-reader-text" for="nl-email"><?php esc_html_e( 'Email address', 'dastaan' ); ?></label>
				<input id="nl-email" type="email" name="EMAIL" required placeholder="<?php esc_attr_e( 'Your email address', 'dastaan' ); ?>" autocomplete="email">
				<button class="btn btn--light" type="submit"><?php esc_html_e( 'Subscribe', 'dastaan' ); ?></button>
			</form>
		<?php else : ?>
			<form class="newsletter__form js-newsletter" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post" novalidate>
				<input type="hidden" name="action" value="dastaan_subscribe">
				<input type="hidden" name="source" value="footer">
				<?php wp_nonce_field( 'dastaan_newsletter', 'dastaan_nl_nonce' ); ?>
				<p class="nl-hp" aria-hidden="true"><label>Website<input type="text" name="website_url" tabindex="-1" autocomplete="off"></label></p>
				<label class="screen-reader-text" for="nl-email"><?php esc_html_e( 'Email address', 'dastaan' ); ?></label>
				<input id="nl-email" type="email" name="email" required placeholder="<?php esc_attr_e( 'Your email address', 'dastaan' ); ?>" autocomplete="email" inputmode="email">
				<button class="btn btn--light" type="submit"><span><?php esc_html_e( 'Subscribe', 'dastaan' ); ?></span></button>
				<p class="newsletter__msg" role="status" aria-live="polite">
					<?php
					if ( 'ok' === $dastaan_status ) {
						echo esc_html( dastaan_option( 'newsletter_success' ) );
					} elseif ( 'error' === $dastaan_status ) {
						esc_html_e( 'Please check the email address and try again.', 'dastaan' );
					}
					?>
				</p>
			</form>
		<?php endif; ?>
	</div>
</section>
