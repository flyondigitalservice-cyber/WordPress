<?php
/**
 * SEO editorial text + FAQ accordion.
 *
 * @package Dastaan
 */

defined( 'ABSPATH' ) || exit;

$dastaan_force = ! empty( $args['force'] );
if ( ! $dastaan_force && ! dastaan_option( 'seo_enable' ) ) {
	return;
}
$dastaan_faqs = array();
for ( $i = 1; $i <= 4; $i++ ) {
	$q = dastaan_option( "faq_{$i}_q" );
	$a = dastaan_option( "faq_{$i}_a" );
	if ( $q && $a ) {
		$dastaan_faqs[] = array( $q, $a );
	}
}
$dastaan_text = dastaan_option( 'seo_text' );
if ( ! $dastaan_text && ! $dastaan_faqs ) {
	return;
}
?>
<section class="section seo-block">
	<div class="container seo-block__inner">
		<?php if ( $dastaan_text ) : ?>
			<div class="seo-block__text">
				<?php if ( dastaan_option( 'seo_title' ) ) : ?><h2><?php echo esc_html( dastaan_option( 'seo_title' ) ); ?></h2><?php endif; ?>
				<?php echo wp_kses_post( wpautop( $dastaan_text ) ); ?>
			</div>
		<?php endif; ?>
		<?php if ( $dastaan_faqs ) : ?>
			<div class="seo-block__faq">
				<h2><?php esc_html_e( 'Frequently asked questions', 'dastaan' ); ?></h2>
				<div class="accordion">
					<?php foreach ( $dastaan_faqs as $dastaan_i => $dastaan_f ) : ?>
						<details class="accordion__item" <?php echo 0 === $dastaan_i ? 'open' : ''; ?>>
							<summary class="accordion__summary"><h3 class="accordion__title"><?php echo esc_html( $dastaan_f[0] ); ?></h3><?php dastaan_the_icon( 'plus', array( 'size' => 18, 'class' => 'accordion__icon' ) ); ?></summary>
							<div class="accordion__body"><p><?php echo esc_html( $dastaan_f[1] ); ?></p></div>
						</details>
					<?php endforeach; ?>
				</div>
			</div>
		<?php endif; ?>
	</div>
</section>
