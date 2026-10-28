<?php
/**
 * Not found.
 *
 * @package CFM
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<section class="cfm-hero cfm-hero--slim">
	<div class="cfm-hero__shade" aria-hidden="true"></div>
	<div class="cfm-wrap cfm-hero__inner">
		<p class="cfm-eyebrow cfm-hero__eyebrow">404</p>
		<h1 class="cfm-hero__title"><?php esc_html_e( 'This page dropped the bar.', 'cfm' ); ?></h1>
		<p class="cfm-hero__sub"><?php esc_html_e( 'The page you are looking for has moved or no longer exists. Try one of these instead — or ask us on WhatsApp.', 'cfm' ); ?></p>
		<div class="cfm-hero__actions">
			<a class="cfm-btn cfm-btn--wa cfm-btn--lg" href="<?php echo esc_url( cfm_wa_url() ); ?>" target="_blank" rel="noopener"><?php echo cfm_icon( 'whatsapp', 22 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><?php esc_html_e( 'Ask on WhatsApp', 'cfm' ); ?></span></a>
			<a class="cfm-btn cfm-btn--ghost cfm-btn--lg" href="<?php echo esc_url( home_url( '/' ) ); ?>"><span><?php esc_html_e( 'Back to home', 'cfm' ); ?></span></a>
		</div>
	</div>
</section>
<div class="cfm-content">
	<div class="cfm-section cfm-products">
		<p class="cfm-eyebrow"><?php esc_html_e( 'Popular', 'cfm' ); ?></p>
		<h2><?php esc_html_e( 'Our floor mats', 'cfm' ); ?></h2>
		<?php echo do_shortcode( '[cfm_children parent="floor-mats" limit="8"]' ); ?>
	</div>
</div>
<?php
get_footer();
