<?php
/**
 * Scrolling marquee band.
 *
 * @package FlavorKit
 */

defined( 'ABSPATH' ) || exit;

$fk_items = flavorkit_pipe_list( flavorkit_mod( 'marquee_items' ) );
if ( ! $fk_items ) {
	return;
}
?>
<section class="fk-marquee-wrap" aria-label="<?php esc_attr_e( 'Highlights', 'flavorkit' ); ?>">
	<div class="fk-marquee">
	<div class="fk-marquee-track">
		<?php for ( $fk_loop = 0; $fk_loop < 2; $fk_loop++ ) : ?>
			<ul class="fk-marquee-group"<?php echo $fk_loop ? ' aria-hidden="true"' : ''; ?>>
				<?php foreach ( $fk_items as $fk_item ) : ?>
					<li><?php echo esc_html( $fk_item ); ?></li>
					<li class="fk-marquee-sep" aria-hidden="true"><?php echo flavorkit_icon( 'sparkle', 28 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></li>
				<?php endforeach; ?>
			</ul>
		<?php endfor; ?>
	</div>
	</div>
</section>
