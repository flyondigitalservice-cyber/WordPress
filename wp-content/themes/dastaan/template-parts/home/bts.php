<?php
/**
 * Behind the scenes.
 *
 * @package Dastaan
 */

defined( 'ABSPATH' ) || exit;

$dastaan_force = ! empty( $args['force'] );
if ( ! $dastaan_force && ! dastaan_option( 'bts_enable' ) ) {
	return;
}
$dastaan_imgs = array_filter( array( dastaan_option( 'bts_image_1' ), dastaan_option( 'bts_image_2' ), dastaan_option( 'bts_image_3' ) ) );
if ( ! $dastaan_imgs ) {
	return;
}
$dastaan_words = array_filter( array_map( 'trim', explode( ',', (string) dastaan_option( 'bts_crafts' ) ) ) );
?>
<section class="section bts">
	<div class="container bts__inner">
		<div class="bts__copy">
			<p class="eyebrow"><?php echo esc_html( dastaan_option( 'bts_eyebrow' ) ); ?></p>
			<h2 class="section-title"><?php echo esc_html( dastaan_option( 'bts_title' ) ); ?></h2>
			<p class="section-text"><?php echo esc_html( dastaan_option( 'bts_text' ) ); ?></p>
			<?php if ( dastaan_option( 'bts_button' ) && dastaan_option( 'bts_url' ) ) : ?>
				<a class="btn btn--primary" href="<?php echo dastaan_url( dastaan_option( 'bts_url' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>"><?php echo esc_html( dastaan_option( 'bts_button' ) ); ?></a>
			<?php endif; ?>
		</div>
		<div class="bts__gallery bts__gallery--<?php echo count( $dastaan_imgs ); ?>">
			<?php foreach ( array_values( $dastaan_imgs ) as $dastaan_i => $dastaan_img ) : ?>
				<figure class="bts__img bts__img--<?php echo (int) $dastaan_i + 1; ?>"><?php echo dastaan_img( $dastaan_img, __( 'Artisan at work', 'dastaan' ), array( 'size' => 'dastaan-tile' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></figure>
			<?php endforeach; ?>
		</div>
	</div>
	<?php if ( $dastaan_words ) : ?>
		<div class="ticker" aria-hidden="true">
			<div class="ticker__track">
				<?php for ( $r = 0; $r < 3; $r++ ) : ?>
					<?php foreach ( $dastaan_words as $dastaan_w ) : ?><span><?php echo esc_html( $dastaan_w ); ?></span><i>✦</i><?php endforeach; ?>
				<?php endfor; ?>
			</div>
		</div>
	<?php endif; ?>
</section>
