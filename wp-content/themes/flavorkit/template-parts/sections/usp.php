<?php
/**
 * USP badges.
 *
 * @package FlavorKit
 */

defined( 'ABSPATH' ) || exit;

$fk_items = flavorkit_lines( flavorkit_mod( 'usp_items' ), 3 );
if ( ! $fk_items ) {
	return;
}
?>
<section class="fk-section fk-usp" aria-label="<?php esc_attr_e( 'Why shop with us', 'flavorkit' ); ?>">
	<div class="fk-container">
		<ul class="fk-usp-grid">
			<?php foreach ( $fk_items as $fk_i => $fk_item ) : ?>
				<li class="fk-usp-item fk-reveal" style="--fk-delay:<?php echo esc_attr( $fk_i * 80 ); ?>ms">
					<span class="fk-usp-icon fk-tone-<?php echo esc_attr( $fk_i % 4 ); ?>"><?php echo flavorkit_icon( $fk_item[2] ? $fk_item[2] : 'check', 26 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
					<span>
						<strong><?php echo esc_html( $fk_item[0] ); ?></strong>
						<?php if ( $fk_item[1] ) : ?>
							<small><?php echo esc_html( $fk_item[1] ); ?></small>
						<?php endif; ?>
					</span>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
