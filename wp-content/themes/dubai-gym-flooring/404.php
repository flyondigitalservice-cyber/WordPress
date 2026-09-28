<?php
/**
 * Not found.
 *
 * @package DGF
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<section class="dgf-hero dgf-hero--slim">
	<div class="dgf-hero__shade" aria-hidden="true"></div>
	<div class="dgf-wrap dgf-hero__inner">
		<p class="dgf-eyebrow dgf-hero__eyebrow">404</p>
		<h1 class="dgf-hero__title"><?php esc_html_e( 'This page dropped the bar.', 'dgf' ); ?></h1>
		<p class="dgf-hero__sub"><?php esc_html_e( 'The page you are looking for has moved or no longer exists. Try one of these instead — or ask us on WhatsApp.', 'dgf' ); ?></p>
		<div class="dgf-hero__actions">
			<a class="dgf-btn dgf-btn--wa dgf-btn--lg" href="<?php echo esc_url( dgf_wa_url() ); ?>" target="_blank" rel="noopener"><?php echo dgf_icon( 'whatsapp', 22 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><?php esc_html_e( 'Ask on WhatsApp', 'dgf' ); ?></span></a>
			<a class="dgf-btn dgf-btn--ghost dgf-btn--lg" href="<?php echo esc_url( home_url( '/' ) ); ?>"><span><?php esc_html_e( 'Back to home', 'dgf' ); ?></span></a>
		</div>
	</div>
</section>
<div class="dgf-content">
	<div class="dgf-section dgf-products">
		<p class="dgf-eyebrow"><?php esc_html_e( 'Popular', 'dgf' ); ?></p>
		<h2><?php esc_html_e( 'Gym flooring systems', 'dgf' ); ?></h2>
		<?php echo do_shortcode( '[dgf_children parent="gym-flooring-products" limit="8"]' ); ?>
	</div>
</div>
<?php
get_footer();
