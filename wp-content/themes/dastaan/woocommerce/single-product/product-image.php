<?php
/**
 * Product media gallery: images + videos, thumbnails, zoom, lightbox. No jQuery plugins.
 *
 * Markup keeps WooCommerce's gallery class names so variation image swapping continues to work.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package Dastaan
 * @version 11.1.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( ! function_exists( 'dastaan_get_product_media' ) ) {
	return;
}

$dastaan_media    = dastaan_get_product_media( $product );
$dastaan_autoplay = dastaan_option( 'product_video_autoplay' ) ? '1' : '0';
$dastaan_count    = count( $dastaan_media );
?>
<div class="woocommerce-product-gallery d-gallery<?php echo $dastaan_count > 1 ? ' d-gallery--multi' : ''; ?>" data-autoplay="<?php echo esc_attr( $dastaan_autoplay ); ?>">
	<?php if ( $dastaan_count > 1 ) : ?>
	<div class="d-gallery__thumbs" role="tablist" aria-label="<?php esc_attr_e( 'Product media', 'dastaan' ); ?>">
		<?php foreach ( $dastaan_media as $dastaan_i => $dastaan_m ) : ?>
			<button class="d-gallery__thumb<?php echo 0 === $dastaan_i ? ' is-active' : ''; ?>" type="button" role="tab" aria-selected="<?php echo 0 === $dastaan_i ? 'true' : 'false'; ?>" data-index="<?php echo (int) $dastaan_i; ?>" aria-label="<?php echo esc_attr( sprintf( /* translators: %d: index */ __( 'Show media %d', 'dastaan' ), $dastaan_i + 1 ) ); ?>">
				<?php if ( $dastaan_m['thumb'] ) : ?><img src="<?php echo esc_url( $dastaan_m['thumb'] ); ?>" alt="" loading="lazy" decoding="async"><?php endif; ?>
				<?php if ( 'video' === $dastaan_m['type'] ) : ?><span class="d-gallery__thumb-play"><?php dastaan_the_icon( 'play', array( 'size' => 12 ) ); ?></span><?php endif; ?>
			</button>
		<?php endforeach; ?>
	</div>
	<?php endif; ?>

	<div class="woocommerce-product-gallery__wrapper d-gallery__stage">
		<?php echo dastaan_product_badges( $product ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<div class="d-gallery__track">
			<?php foreach ( $dastaan_media as $dastaan_i => $dastaan_m ) : ?>
				<?php if ( 'video' === $dastaan_m['type'] ) : ?>
					<div class="woocommerce-product-gallery__image d-gallery__slide d-gallery__slide--video<?php echo 0 === $dastaan_i ? ' is-active' : ''; ?>" data-index="<?php echo (int) $dastaan_i; ?>" data-type="video" data-src="<?php echo esc_url( $dastaan_m['src'] ); ?>" data-embed="<?php echo esc_url( $dastaan_m['embed'] ); ?>" data-poster="<?php echo esc_url( $dastaan_m['poster'] ); ?>">
						<?php if ( $dastaan_m['src'] ) : ?>
							<video class="d-gallery__video" src="<?php echo esc_url( $dastaan_m['src'] ); ?>" <?php echo $dastaan_m['poster'] ? 'poster="' . esc_url( $dastaan_m['poster'] ) . '"' : ''; ?> muted loop playsinline preload="metadata" aria-label="<?php echo esc_attr( $dastaan_m['alt'] ); ?>"></video>
							<button type="button" class="d-gallery__play js-video-toggle" aria-label="<?php esc_attr_e( 'Play video', 'dastaan' ); ?>"><?php dastaan_the_icon( 'play', array( 'size' => 28 ) ); ?></button>
							<button type="button" class="d-gallery__mute js-video-mute" aria-label="<?php esc_attr_e( 'Unmute', 'dastaan' ); ?>" aria-pressed="true"><span class="d-gallery__mute-txt"><?php esc_html_e( 'Sound', 'dastaan' ); ?></span></button>
						<?php else : ?>
							<button type="button" class="d-gallery__embed-poster js-embed-load" aria-label="<?php esc_attr_e( 'Play video', 'dastaan' ); ?>">
								<?php if ( $dastaan_m['poster'] ) : ?><img src="<?php echo esc_url( $dastaan_m['poster'] ); ?>" alt="<?php echo esc_attr( $dastaan_m['alt'] ); ?>" loading="lazy" decoding="async"><?php endif; ?>
								<span class="d-gallery__play"><?php dastaan_the_icon( 'play', array( 'size' => 28 ) ); ?></span>
							</button>
						<?php endif; ?>
					</div>
				<?php else : ?>
					<div class="woocommerce-product-gallery__image d-gallery__slide<?php echo 0 === $dastaan_i ? ' is-active' : ''; ?>" data-index="<?php echo (int) $dastaan_i; ?>" data-type="image" data-thumb="<?php echo esc_url( $dastaan_m['thumb'] ); ?>">
						<a href="<?php echo esc_url( $dastaan_m['full'] ); ?>" class="d-gallery__zoom-link" data-full="<?php echo esc_url( $dastaan_m['full'] ); ?>" aria-label="<?php esc_attr_e( 'Open image in lightbox', 'dastaan' ); ?>">
							<img
								src="<?php echo esc_url( $dastaan_m['src'] ); ?>"
								<?php if ( $dastaan_m['srcset'] ) : ?>srcset="<?php echo esc_attr( $dastaan_m['srcset'] ); ?>" sizes="<?php echo esc_attr( $dastaan_m['sizes'] ); ?>"<?php endif; ?>
								width="<?php echo (int) $dastaan_m['w']; ?>" height="<?php echo (int) $dastaan_m['h']; ?>"
								alt="<?php echo esc_attr( $dastaan_m['alt'] ); ?>"
								class="wp-post-image d-gallery__img"
								data-large_image="<?php echo esc_url( $dastaan_m['full'] ); ?>"
								data-large_image_width="<?php echo (int) $dastaan_m['full_w']; ?>"
								data-large_image_height="<?php echo (int) $dastaan_m['full_h']; ?>"
								<?php echo 0 === $dastaan_i ? 'loading="eager" fetchpriority="high"' : 'loading="lazy"'; ?> decoding="async">
						</a>
					</div>
				<?php endif; ?>
			<?php endforeach; ?>
		</div>
		<?php if ( $dastaan_count > 1 ) : ?>
			<button class="d-gallery__arrow d-gallery__arrow--prev" type="button" aria-label="<?php esc_attr_e( 'Previous media', 'dastaan' ); ?>"><?php dastaan_the_icon( 'chevron-left', array( 'size' => 22 ) ); ?></button>
			<button class="d-gallery__arrow d-gallery__arrow--next" type="button" aria-label="<?php esc_attr_e( 'Next media', 'dastaan' ); ?>"><?php dastaan_the_icon( 'chevron-right', array( 'size' => 22 ) ); ?></button>
			<span class="d-gallery__counter" aria-live="polite"><span class="d-gallery__current">1</span>/<?php echo (int) $dastaan_count; ?></span>
		<?php endif; ?>
		<button class="d-gallery__expand" type="button" aria-label="<?php esc_attr_e( 'View fullscreen', 'dastaan' ); ?>"><?php dastaan_the_icon( 'zoom', array( 'size' => 20 ) ); ?></button>
	</div>
</div>

<div class="lightbox" hidden role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Media viewer', 'dastaan' ); ?>">
	<button class="lightbox__close icon-btn" type="button" aria-label="<?php esc_attr_e( 'Close', 'dastaan' ); ?>"><?php dastaan_the_icon( 'close', array( 'size' => 28 ) ); ?></button>
	<button class="lightbox__arrow lightbox__arrow--prev" type="button" aria-label="<?php esc_attr_e( 'Previous', 'dastaan' ); ?>"><?php dastaan_the_icon( 'chevron-left', array( 'size' => 28 ) ); ?></button>
	<div class="lightbox__body"></div>
	<button class="lightbox__arrow lightbox__arrow--next" type="button" aria-label="<?php esc_attr_e( 'Next', 'dastaan' ); ?>"><?php dastaan_the_icon( 'chevron-right', array( 'size' => 28 ) ); ?></button>
	<p class="lightbox__counter"></p>
</div>
