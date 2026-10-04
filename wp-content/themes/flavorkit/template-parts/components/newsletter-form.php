<?php
/**
 * Newsletter form: configured shortcode or built-in capture.
 *
 * @package FlavorKit
 *
 * @var array $args { compact?: bool }
 */

defined( 'ABSPATH' ) || exit;

$fk_compact   = ! empty( $args['compact'] );
$fk_shortcode = trim( (string) flavorkit_mod( 'newsletter_shortcode' ) );

if ( $fk_shortcode ) {
	echo '<div class="fk-news-shortcode">' . do_shortcode( $fk_shortcode ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput
	return;
}

// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- display only.
$fk_status = isset( $_GET['fk_subscribed'] ) ? sanitize_key( wp_unslash( $_GET['fk_subscribed'] ) ) : '';
$fk_id     = wp_unique_id( 'fk-email-' );
?>
<form class="fk-news-form<?php echo $fk_compact ? ' is-compact' : ''; ?>" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
	<input type="hidden" name="action" value="flavorkit_subscribe">
	<?php wp_nonce_field( 'flavorkit_subscribe', 'flavorkit_nonce', false ); ?>
	<label class="screen-reader-text" for="<?php echo esc_attr( $fk_id ); ?>"><?php esc_html_e( 'Email address', 'flavorkit' ); ?></label>
	<input id="<?php echo esc_attr( $fk_id ); ?>" type="email" name="fk_email" required placeholder="<?php esc_attr_e( 'Your email address', 'flavorkit' ); ?>" autocomplete="email">
	<input class="fk-hp" type="text" name="fk_website" tabindex="-1" autocomplete="off" aria-hidden="true">
	<button class="fk-btn fk-btn-dark" type="submit"><?php echo $fk_compact ? flavorkit_icon( 'arrow', 20 ) . '<span class="screen-reader-text">' . esc_html__( 'Subscribe', 'flavorkit' ) . '</span>' : esc_html__( 'Unlock my code', 'flavorkit' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
</form>
<?php if ( '1' === $fk_status && ! $fk_compact ) : ?>
	<p class="fk-news-msg is-success" role="status"><?php esc_html_e( 'You\'re in! Watch your inbox for offers and new drops.', 'flavorkit' ); ?></p>
<?php elseif ( 'error' === $fk_status && ! $fk_compact ) : ?>
	<p class="fk-news-msg is-error" role="alert"><?php esc_html_e( 'Please enter a valid email address and try again.', 'flavorkit' ); ?></p>
<?php endif; ?>
