<?php
/**
 * Testimonials.
 *
 * @package Dastaan
 */

defined( 'ABSPATH' ) || exit;

$dastaan_force = ! empty( $args['force'] );
if ( ! $dastaan_force && ! dastaan_option( 'testimonials_enable' ) ) {
	return;
}
$dastaan_items = array();
for ( $i = 1; $i <= 4; $i++ ) {
	$n = dastaan_option( "testimonial_{$i}_name" );
	$t = dastaan_option( "testimonial_{$i}_text" );
	if ( $n && $t ) {
		$dastaan_items[] = array( 'name' => $n, 'text' => $t, 'product' => dastaan_option( "testimonial_{$i}_product" ), 'rating' => (int) dastaan_option( "testimonial_{$i}_rating" ), 'image' => dastaan_option( "testimonial_{$i}_image" ) );
	}
}
if ( ! $dastaan_items ) {
	return;
}
?>
<section class="section testimonials">
	<div class="container">
		<?php dastaan_section_heading( array( 'eyebrow' => dastaan_option( 'testimonials_eyebrow' ), 'title' => dastaan_option( 'testimonials_title' ) ) ); ?>
		<div class="carousel" data-carousel>
			<ul class="testimonials__track carousel__track">
				<?php foreach ( $dastaan_items as $dastaan_t ) : ?>
					<li class="testimonial">
						<blockquote class="testimonial__card">
							<?php dastaan_stars( $dastaan_t['rating'] ); ?>
							<p class="testimonial__text">“<?php echo esc_html( $dastaan_t['text'] ); ?>”</p>
							<footer class="testimonial__meta">
								<?php dastaan_avatar( $dastaan_t['name'], $dastaan_t['image'] ); ?>
								<span><cite class="testimonial__name"><?php echo esc_html( $dastaan_t['name'] ); ?></cite>
								<?php if ( $dastaan_t['product'] ) : ?><small><?php echo esc_html( sprintf( /* translators: %s: product */ __( 'Purchased: %s', 'dastaan' ), $dastaan_t['product'] ) ); ?></small><?php endif; ?></span>
								<span class="verified"><?php dastaan_the_icon( 'check', array( 'size' => 12 ) ); ?><?php esc_html_e( 'Verified buyer', 'dastaan' ); ?></span>
							</footer>
						</blockquote>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>
</section>
