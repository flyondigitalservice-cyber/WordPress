<?php
/**
 * Instagram block.
 *
 * @package Dastaan
 */

defined( 'ABSPATH' ) || exit;

$dastaan_force = ! empty( $args['force'] );
if ( ! $dastaan_force && ! dastaan_option( 'instagram_enable' ) ) {
	return;
}
$dastaan_handle    = ltrim( dastaan_option( 'instagram_handle' ), '@' );
$dastaan_profile   = dastaan_option( 'social_instagram' ) ? dastaan_option( 'social_instagram' ) : ( $dastaan_handle ? 'https://www.instagram.com/' . rawurlencode( $dastaan_handle ) . '/' : '' );
$dastaan_shortcode = dastaan_option( 'instagram_shortcode' );
$dastaan_tiles     = $dastaan_shortcode ? array() : dastaan_get_instagram_tiles();
if ( ! $dastaan_shortcode && ! $dastaan_tiles ) {
	return;
}
?>
<section class="section instagram">
	<div class="container">
		<header class="section-heading section-heading--center">
			<div class="section-heading__main">
				<p class="eyebrow"><?php dastaan_the_icon( 'instagram', array( 'size' => 16 ) ); ?> <?php echo $dastaan_handle ? '@' . esc_html( $dastaan_handle ) : ''; ?></p>
				<h2 class="section-title"><?php echo esc_html( dastaan_option( 'instagram_title' ) ); ?></h2>
			</div>
		</header>
		<?php if ( $dastaan_shortcode ) : ?>
			<div class="instagram__plugin"><?php echo do_shortcode( $dastaan_shortcode ); ?></div>
		<?php else : ?>
			<ul class="instagram__grid">
				<?php foreach ( array_slice( $dastaan_tiles, 0, 8 ) as $dastaan_t ) : ?>
					<li class="insta-tile">
						<a href="<?php echo $dastaan_t['link'] ? dastaan_url( $dastaan_t['link'] ) : esc_url( $dastaan_profile ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>" <?php echo ( ! $dastaan_t['link'] || false !== strpos( $dastaan_t['link'], 'instagram.com' ) ) ? 'target="_blank" rel="noopener"' : ''; ?>>
							<?php echo dastaan_img( $dastaan_t['image'], __( 'Instagram post', 'dastaan' ), array( 'size' => 'dastaan-square', 'sizes' => '(min-width: 1024px) 25vw, 50vw' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							<span class="insta-tile__hover"><?php dastaan_the_icon( 'instagram', array( 'size' => 22 ) ); ?></span>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
		<?php if ( $dastaan_profile ) : ?>
			<p class="instagram__cta"><a class="btn btn--outline" href="<?php echo esc_url( $dastaan_profile ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Follow on Instagram', 'dastaan' ); ?></a></p>
		<?php endif; ?>
	</div>
</section>
