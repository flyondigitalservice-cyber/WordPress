<?php
/**
 * Hero slider.
 *
 * @package Dastaan
 */

defined( 'ABSPATH' ) || exit;

$dastaan_slides = array();
for ( $i = 1; $i <= 4; $i++ ) {
	$img = dastaan_option( "hero_{$i}_image" );
	if ( ! $img ) {
		continue;
	}
	$dastaan_slides[] = array(
		'image'   => $img,
		'eyebrow' => dastaan_option( "hero_{$i}_eyebrow" ),
		'heading' => dastaan_option( "hero_{$i}_heading" ),
		'text'    => dastaan_option( "hero_{$i}_text" ),
		'button'  => dastaan_option( "hero_{$i}_button" ),
		'url'     => dastaan_option( "hero_{$i}_url" ),
		'button2' => dastaan_option( "hero_{$i}_button2" ),
		'url2'    => dastaan_option( "hero_{$i}_url2" ),
		'theme'   => dastaan_option( "hero_{$i}_theme" ),
		'focus'   => in_array( dastaan_option( "hero_{$i}_focus" ), array( 'left', 'right' ), true ) ? dastaan_option( "hero_{$i}_focus" ) : 'center',
		'coupon'  => dastaan_option( "hero_{$i}_coupon" ),
		'mobile'  => dastaan_option( "hero_{$i}_mobile_image" ),
	);
}
if ( ! $dastaan_slides ) {
	return;
}
$dastaan_multi = count( $dastaan_slides ) > 1;
?>
<section class="hero<?php echo $dastaan_multi ? ' hero--multi' : ''; ?>" aria-roledescription="carousel" aria-label="<?php esc_attr_e( 'Featured collections', 'dastaan' ); ?>" data-autoplay="<?php echo dastaan_option( 'hero_autoplay' ) ? '1' : '0'; ?>">
	<div class="hero__track">
		<?php foreach ( $dastaan_slides as $dastaan_i => $dastaan_s ) : ?>
			<div class="hero__slide hero__slide--<?php echo esc_attr( $dastaan_s['theme'] ); ?> hero__slide--focus-<?php echo esc_attr( $dastaan_s['focus'] ); ?><?php echo 0 === $dastaan_i ? ' is-active' : ''; ?>" role="group" aria-roledescription="slide" aria-label="<?php echo esc_attr( sprintf( /* translators: 1: index 2: total */ __( '%1$d of %2$d', 'dastaan' ), $dastaan_i + 1, count( $dastaan_slides ) ) ); ?>" <?php echo 0 === $dastaan_i ? '' : 'aria-hidden="true"'; ?>>
				<div class="hero__media">
					<?php if ( $dastaan_s['mobile'] ) : ?><picture><source media="(max-width: 767px)" srcset="<?php echo esc_url( $dastaan_s['mobile'] ); ?>"><?php endif; ?>
					<?php
					echo dastaan_img( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						$dastaan_s['image'],
						$dastaan_s['heading'] ? $dastaan_s['heading'] : get_bloginfo( 'name' ),
						array(
							'size'          => 'dastaan-hero',
							'loading'       => 0 === $dastaan_i ? 'eager' : 'lazy',
							'fetchpriority' => 0 === $dastaan_i ? 'high' : 'auto',
							'sizes'         => '100vw',
						)
					);
					?>
					<?php if ( $dastaan_s['mobile'] ) : ?></picture><?php endif; ?>
				</div>
				<div class="container hero__content">
					<div class="hero__box">
						<?php if ( $dastaan_s['eyebrow'] ) : ?><p class="hero__eyebrow"><?php echo esc_html( $dastaan_s['eyebrow'] ); ?></p><?php endif; ?>
						<?php if ( $dastaan_s['heading'] ) : ?>
							<?php $dastaan_tag = 0 === $dastaan_i && ! is_paged() ? 'h1' : 'h2'; ?>
							<<?php echo $dastaan_tag; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> class="hero__title"><?php echo esc_html( $dastaan_s['heading'] ); ?></<?php echo $dastaan_tag; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
						<?php endif; ?>
						<?php if ( $dastaan_s['text'] ) : ?><p class="hero__text"><?php echo esc_html( $dastaan_s['text'] ); ?></p><?php endif; ?>
						<?php if ( $dastaan_s['coupon'] ) : ?>
							<button class="hero__coupon js-copy-code" type="button" data-code="<?php echo esc_attr( $dastaan_s['coupon'] ); ?>" aria-label="<?php echo esc_attr( sprintf( /* translators: %s: coupon code */ __( 'Copy coupon code %s', 'dastaan' ), $dastaan_s['coupon'] ) ); ?>">
								<?php dastaan_the_icon( 'gift', array( 'size' => 14 ) ); ?>
								<span><?php esc_html_e( 'Use code', 'dastaan' ); ?> <strong><?php echo esc_html( $dastaan_s['coupon'] ); ?></strong></span>
							</button>
						<?php endif; ?>
						<?php if ( ( $dastaan_s['button'] && $dastaan_s['url'] ) || ( $dastaan_s['button2'] && $dastaan_s['url2'] ) ) : ?>
							<div class="hero__actions">
								<?php if ( $dastaan_s['button'] && $dastaan_s['url'] ) : ?>
									<a class="btn btn--primary btn--lg" href="<?php echo dastaan_url( $dastaan_s['url'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>"><?php echo esc_html( $dastaan_s['button'] ); ?></a>
								<?php endif; ?>
								<?php if ( $dastaan_s['button2'] && $dastaan_s['url2'] ) : ?>
									<a class="btn btn--text btn--lg" href="<?php echo dastaan_url( $dastaan_s['url2'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>"><?php echo esc_html( $dastaan_s['button2'] ); ?> <?php dastaan_the_icon( 'arrow-right', array( 'size' => 16 ) ); ?></a>
								<?php endif; ?>
							</div>
						<?php endif; ?>
					</div>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
	<?php if ( $dastaan_multi ) : ?>
		<div class="hero__controls container">
			<button class="hero__arrow hero__arrow--prev" type="button" aria-label="<?php esc_attr_e( 'Previous slide', 'dastaan' ); ?>"><?php dastaan_the_icon( 'chevron-left', array( 'size' => 20 ) ); ?></button>
			<div class="hero__dots" role="tablist">
				<?php foreach ( $dastaan_slides as $dastaan_i => $dastaan_s ) : ?>
					<button class="hero__dot<?php echo 0 === $dastaan_i ? ' is-active' : ''; ?>" type="button" role="tab" aria-selected="<?php echo 0 === $dastaan_i ? 'true' : 'false'; ?>" aria-label="<?php echo esc_attr( sprintf( /* translators: %d: slide number */ __( 'Go to slide %d', 'dastaan' ), $dastaan_i + 1 ) ); ?>"><span></span></button>
				<?php endforeach; ?>
			</div>
			<button class="hero__arrow hero__arrow--next" type="button" aria-label="<?php esc_attr_e( 'Next slide', 'dastaan' ); ?>"><?php dastaan_the_icon( 'chevron-right', array( 'size' => 20 ) ); ?></button>
		</div>
	<?php endif; ?>
</section>
