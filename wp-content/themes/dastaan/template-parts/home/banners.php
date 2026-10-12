<?php
/**
 * Offer banners (3 tiles).
 *
 * @package Dastaan
 */

defined( 'ABSPATH' ) || exit;

$dastaan_force = ! empty( $args['force'] );
if ( ! $dastaan_force && ! dastaan_option( 'banners_enable' ) ) {
	return;
}
$dastaan_banners = array();
for ( $i = 1; $i <= 3; $i++ ) {
	$img = dastaan_option( "banner_{$i}_image" );
	if ( $img ) {
		$dastaan_banners[] = array(
			'image'   => $img,
			'eyebrow' => dastaan_option( "banner_{$i}_eyebrow" ),
			'title'   => dastaan_option( "banner_{$i}_title" ),
			'text'    => dastaan_option( "banner_{$i}_text" ),
			'button'  => dastaan_option( "banner_{$i}_button" ),
			'url'     => dastaan_option( "banner_{$i}_url" ),
		);
	}
}
if ( ! $dastaan_banners ) {
	return;
}
?>
<section class="section banners" aria-label="<?php esc_attr_e( 'Offers', 'dastaan' ); ?>">
	<div class="container">
		<div class="banners__grid banners__grid--<?php echo count( $dastaan_banners ); ?>">
			<?php foreach ( $dastaan_banners as $dastaan_b ) : ?>
				<a class="banner" href="<?php echo $dastaan_b['url'] ? dastaan_url( $dastaan_b['url'] ) : '#'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>">
					<span class="banner__media"><?php echo dastaan_img( $dastaan_b['image'], $dastaan_b['title'], array( 'size' => 'dastaan-tile', 'sizes' => '(min-width: 1024px) 33vw, 90vw' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
					<span class="banner__content">
						<?php if ( $dastaan_b['eyebrow'] ) : ?><span class="banner__eyebrow"><?php echo esc_html( $dastaan_b['eyebrow'] ); ?></span><?php endif; ?>
						<?php if ( $dastaan_b['title'] ) : ?><span class="banner__title"><?php echo esc_html( $dastaan_b['title'] ); ?></span><?php endif; ?>
						<?php if ( $dastaan_b['text'] ) : ?><span class="banner__text"><?php echo esc_html( $dastaan_b['text'] ); ?></span><?php endif; ?>
						<?php if ( $dastaan_b['button'] ) : ?><span class="banner__btn"><?php echo esc_html( $dastaan_b['button'] ); ?> <?php dastaan_the_icon( 'arrow-right', array( 'size' => 14 ) ); ?></span><?php endif; ?>
					</span>
				</a>
			<?php endforeach; ?>
		</div>
	</div>
</section>
