<?php
/**
 * Category circles (story style).
 *
 * @package Dastaan
 */

defined( 'ABSPATH' ) || exit;

$dastaan_force = ! empty( $args['force'] );
if ( ! $dastaan_force && ! dastaan_option( 'circles_enable' ) ) {
	return;
}
$dastaan_terms = dastaan_get_showcase_categories( (int) dastaan_option( 'circles_count' ) );
if ( ! $dastaan_terms ) {
	return;
}
?>
<section class="circles" aria-label="<?php echo esc_attr( dastaan_option( 'circles_title' ) ); ?>">
	<div class="container">
		<ul class="circles__track">
			<?php if ( dastaan_has_wc() ) : ?>
				<li class="circle circle--all">
					<a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>">
						<span class="circle__ring"><span class="circle__img circle__img--all"><?php dastaan_the_icon( 'sparkle', array( 'size' => 26 ) ); ?></span></span>
						<span class="circle__label"><?php esc_html_e( 'All pieces', 'dastaan' ); ?></span>
					</a>
				</li>
			<?php endif; ?>
			<?php foreach ( $dastaan_terms as $dastaan_t ) : ?>
				<li class="circle">
					<a href="<?php echo esc_url( get_term_link( $dastaan_t ) ); ?>">
						<span class="circle__ring"><span class="circle__img"><img src="<?php echo esc_url( dastaan_category_image( $dastaan_t, 'dastaan-circle' ) ); ?>" alt="" loading="lazy" decoding="async" width="120" height="120"></span></span>
						<span class="circle__label"><?php echo esc_html( $dastaan_t->name ); ?></span>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
