<?php
/**
 * Benefits around a hero product.
 *
 * @package FlavorKit
 */

defined( 'ABSPATH' ) || exit;

$fk_items = flavorkit_lines( flavorkit_mod( 'benefits_items' ), 3 );
if ( ! $fk_items ) {
	return;
}
$fk_half  = (int) ceil( count( $fk_items ) / 2 );
$fk_cols  = array( array_slice( $fk_items, 0, $fk_half ), array_slice( $fk_items, $fk_half ) );
$fk_image = flavorkit_mod( 'hero_image' );
?>
<section class="fk-section fk-benefits">
	<?php flavorkit_wave( 'top', 'var(--fk-accent)' ); ?>
	<div class="fk-container">
		<?php flavorkit_section_heading( flavorkit_mod( 'benefits_eyebrow' ), flavorkit_mod( 'benefits_title' ) ); ?>
		<div class="fk-benefits-grid">
			<?php foreach ( $fk_cols as $fk_c => $fk_col ) : ?>
				<ul class="fk-benefits-col fk-benefits-col-<?php echo esc_attr( $fk_c ); ?>">
					<?php foreach ( $fk_col as $fk_item ) : ?>
						<li class="fk-benefit fk-reveal">
							<span class="fk-benefit-icon"><?php echo flavorkit_icon( $fk_item[2] ? $fk_item[2] : 'check', 26 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
							<h3><?php echo esc_html( $fk_item[0] ); ?></h3>
							<p><?php echo esc_html( $fk_item[1] ); ?></p>
						</li>
					<?php endforeach; ?>
				</ul>
				<?php if ( 0 === $fk_c ) : ?>
					<div class="fk-benefits-center fk-reveal" aria-hidden="true">
						<div class="fk-benefits-ring"></div>
						<?php
						echo $fk_image ? flavorkit_image( $fk_image, 'large', 'fk-benefits-img' ) : flavorkit_pack_svg( 'can', 2, 'LEMON', 'GINGER SODA' ); // phpcs:ignore WordPress.Security.EscapeOutput
						?>
					</div>
				<?php endif; ?>
			<?php endforeach; ?>
		</div>
	</div>
	<?php flavorkit_wave( 'bottom', 'var(--fk-cream)' ); ?>
</section>
