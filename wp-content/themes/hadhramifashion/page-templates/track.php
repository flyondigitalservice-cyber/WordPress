<?php
/**
 * Template Name: Track Order
 *
 * @package HadhramiFashion
 */

get_header();
$hf_key = isset( $_GET['order'] ) ? preg_replace( '/[^A-Za-z0-9]/', '', sanitize_text_field( wp_unslash( $_GET['order'] ) ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
?>
<header class="page-hero" style="margin-bottom:clamp(28px,4vw,48px)">
	<div class="wrap">
		<span class="eyebrow"><?php esc_html_e( 'Live delivery', 'hadhramifashion' ); ?></span>
		<h1><?php esc_html_e( 'Follow your order', 'hadhramifashion' ); ?></h1>
	</div>
</header>

<div class="wrap">
	<?php if ( ! $hf_key ) : ?>
		<form class="wrap--narrow" method="get" style="padding-bottom:6rem">
			<p class="lede"><?php esc_html_e( 'Open the tracking link from your confirmation, or paste your order code here.', 'hadhramifashion' ); ?></p>
			<div class="address-search">
				<input class="input" name="order" placeholder="<?php esc_attr_e( 'Order code', 'hadhramifashion' ); ?>" required>
				<button class="btn" type="submit"><?php esc_html_e( 'Track', 'hadhramifashion' ); ?></button>
			</div>
		</form>
	<?php else : ?>
		<div class="track" data-track="<?php echo esc_attr( $hf_key ); ?>">
			<div>
				<span class="eyebrow eyebrow--plain" data-track-number></span>
				<div class="track__eta" data-track-eta><?php esc_html_e( 'Finding your order…', 'hadhramifashion' ); ?></div>
				<p class="muted" data-track-tier></p>
				<ol class="timeline" data-track-timeline></ol>
				<div data-track-rider></div>
				<div data-track-items style="margin-top:1.6rem"></div>
			</div>
			<div class="map" data-map="track">
				<div class="map-placeholder" data-map-placeholder><span><?php esc_html_e( 'Loading map…', 'hadhramifashion' ); ?></span></div>
			</div>
		</div>
	<?php endif; ?>
</div>
<?php
get_footer();
