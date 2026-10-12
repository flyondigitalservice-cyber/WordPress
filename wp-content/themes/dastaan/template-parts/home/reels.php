<?php
/**
 * Reels / video block.
 *
 * @package Dastaan
 */

defined( 'ABSPATH' ) || exit;

$dastaan_force = ! empty( $args['force'] );
if ( ! $dastaan_force && ! dastaan_option( 'reels_enable' ) ) {
	return;
}
$dastaan_reels = dastaan_get_reels();

// Fall back to product videos so the block is never empty.
if ( ! $dastaan_reels && dastaan_has_wc() && function_exists( 'dastaan_get_product_videos' ) ) {
	$dastaan_q = new WP_Query(
		array(
			'post_type'      => 'product',
			'post_status'    => 'publish',
			'posts_per_page' => 6,
			'no_found_rows'  => true,
			'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
				'relation' => 'OR',
				array( 'key' => '_dastaan_product_video', 'compare' => 'EXISTS' ),
				array( 'key' => '_dastaan_product_video_url', 'compare' => 'EXISTS' ),
				array( 'key' => '_wc_video_gallery', 'compare' => 'EXISTS' ),
			),
		)
	);
	foreach ( $dastaan_q->posts as $dastaan_p ) {
		$dastaan_product = wc_get_product( $dastaan_p->ID );
		if ( ! $dastaan_product ) {
			continue;
		}
		$dastaan_v = dastaan_get_product_videos( $dastaan_product );
		if ( $dastaan_v ) {
			$dastaan_reels[] = array(
				'src'     => $dastaan_v[0]['src'],
				'embed'   => $dastaan_v[0]['embed'],
				'url'     => isset( $dastaan_v[0]['url'] ) ? $dastaan_v[0]['url'] : '',
				'poster'  => $dastaan_v[0]['poster'] ? $dastaan_v[0]['poster'] : get_the_post_thumbnail_url( $dastaan_p->ID, 'dastaan-card' ),
				'caption' => $dastaan_product->get_name(),
				'link'    => get_permalink( $dastaan_p->ID ),
			);
		}
	}
}
if ( ! $dastaan_reels ) {
	return;
}
?>
<section class="section reels">
	<div class="container">
		<?php dastaan_section_heading( array( 'eyebrow' => dastaan_option( 'reels_eyebrow' ), 'title' => dastaan_option( 'reels_title' ), 'text' => dastaan_option( 'reels_text' ) ) ); ?>
		<div class="carousel" data-carousel>
			<button class="carousel__arrow carousel__arrow--prev" type="button" aria-label="<?php esc_attr_e( 'Scroll left', 'dastaan' ); ?>"><?php dastaan_the_icon( 'chevron-left', array( 'size' => 20 ) ); ?></button>
			<ul class="reels__track carousel__track">
				<?php foreach ( $dastaan_reels as $dastaan_r ) : ?>
					<li class="reel">
						<button class="reel__media js-reel" type="button"
							data-src="<?php echo esc_url( $dastaan_r['src'] ); ?>"
							data-embed="<?php echo esc_url( $dastaan_r['embed'] ); ?>"
							data-caption="<?php echo esc_attr( $dastaan_r['caption'] ); ?>"
							data-link="<?php echo $dastaan_r['link'] ? dastaan_url( $dastaan_r['link'] ) : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>"
							aria-label="<?php echo esc_attr( sprintf( /* translators: %s: caption */ __( 'Play reel: %s', 'dastaan' ), $dastaan_r['caption'] ) ); ?>">
							<?php if ( $dastaan_r['poster'] ) : ?>
								<img src="<?php echo esc_url( $dastaan_r['poster'] ); ?>" alt="" loading="lazy" decoding="async">
							<?php elseif ( $dastaan_r['src'] ) : ?>
								<video src="<?php echo esc_url( $dastaan_r['src'] ); ?>#t=0.5" muted playsinline preload="metadata"></video>
							<?php else : ?>
								<span class="reel__placeholder"><?php dastaan_the_icon( 'video', array( 'size' => 32 ) ); ?></span>
							<?php endif; ?>
							<span class="reel__play"><?php dastaan_the_icon( 'play', array( 'size' => 22 ) ); ?></span>
						</button>
						<?php if ( $dastaan_r['caption'] ) : ?>
							<p class="reel__caption">
								<?php if ( $dastaan_r['link'] ) : ?>
									<a href="<?php echo dastaan_url( $dastaan_r['link'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>"><?php echo esc_html( $dastaan_r['caption'] ); ?></a>
								<?php else : ?>
									<?php echo esc_html( $dastaan_r['caption'] ); ?>
								<?php endif; ?>
							</p>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
			<button class="carousel__arrow carousel__arrow--next" type="button" aria-label="<?php esc_attr_e( 'Scroll right', 'dastaan' ); ?>"><?php dastaan_the_icon( 'chevron-right', array( 'size' => 20 ) ); ?></button>
		</div>
	</div>
</section>
<div class="reel-modal" hidden role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Video', 'dastaan' ); ?>">
	<button class="reel-modal__close icon-btn" type="button" aria-label="<?php esc_attr_e( 'Close video', 'dastaan' ); ?>"><?php dastaan_the_icon( 'close', array( 'size' => 28 ) ); ?></button>
	<div class="reel-modal__body"></div>
	<div class="reel-modal__foot"><p class="reel-modal__caption"></p><a class="btn btn--light btn--small reel-modal__link" href="#"><?php esc_html_e( 'Shop this piece', 'dastaan' ); ?></a></div>
</div>
