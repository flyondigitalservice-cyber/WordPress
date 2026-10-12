<?php
/**
 * USP bar.
 *
 * @package Dastaan
 */

defined( 'ABSPATH' ) || exit;

$dastaan_force = ! empty( $args['force'] );
if ( ! $dastaan_force && ! dastaan_option( 'usp_enable' ) ) {
	return;
}
$dastaan_items = array();
for ( $i = 1; $i <= 4; $i++ ) {
	$t = dastaan_option( "usp_{$i}_title" );
	if ( $t ) {
		$dastaan_items[] = array( dastaan_option( "usp_{$i}_icon" ), $t, dastaan_option( "usp_{$i}_text" ) );
	}
}
if ( ! $dastaan_items ) {
	return;
}
?>
<section class="usp" aria-label="<?php esc_attr_e( 'Why shop with us', 'dastaan' ); ?>">
	<div class="container">
		<ul class="usp__list">
			<?php foreach ( $dastaan_items as $dastaan_u ) : ?>
				<li class="usp__item">
					<span class="usp__icon"><?php dastaan_the_icon( $dastaan_u[0], array( 'size' => 26 ) ); ?></span>
					<span class="usp__copy"><strong><?php echo esc_html( $dastaan_u[1] ); ?></strong><?php if ( $dastaan_u[2] ) : ?><small><?php echo esc_html( $dastaan_u[2] ); ?></small><?php endif; ?></span>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
