<?php
/**
 * Customer reviews slider.
 *
 * @package FlavorKit
 */

defined( 'ABSPATH' ) || exit;

$fk_items = flavorkit_lines( flavorkit_mod( 'reviews_items' ), 4 );
if ( ! $fk_items ) {
	return;
}
?>
<section class="fk-section fk-reviews" id="reviews">
	<div class="fk-container">
		<div class="fk-slider-head">
			<?php flavorkit_section_heading( flavorkit_mod( 'reviews_eyebrow' ), flavorkit_mod( 'reviews_title' ), '', 'left' ); ?>
			<div class="fk-slider-nav">
				<button class="fk-icon-btn fk-slider-prev" type="button" aria-controls="fk-reviews-track" aria-label="<?php esc_attr_e( 'Previous reviews', 'flavorkit' ); ?>"><?php echo flavorkit_icon( 'arrow-left' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
				<button class="fk-icon-btn fk-slider-next" type="button" aria-controls="fk-reviews-track" aria-label="<?php esc_attr_e( 'Next reviews', 'flavorkit' ); ?>"><?php echo flavorkit_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
			</div>
		</div>

		<ul class="fk-slider-track" id="fk-reviews-track" tabindex="0" aria-label="<?php esc_attr_e( 'Customer reviews', 'flavorkit' ); ?>">
			<?php foreach ( $fk_items as $fk_i => $fk_item ) : ?>
				<?php $fk_rating = $fk_item[3] ? (float) $fk_item[3] : 5; ?>
				<li class="fk-review fk-tone-<?php echo esc_attr( $fk_i % 4 ); ?>">
					<?php echo flavorkit_stars( $fk_rating ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<blockquote><p>“<?php echo esc_html( $fk_item[2] ); ?>”</p></blockquote>
					<p class="fk-review-author">
						<span class="fk-avatar" aria-hidden="true"><?php echo esc_html( mb_substr( $fk_item[0], 0, 1 ) ); ?></span>
						<span><strong><?php echo esc_html( $fk_item[0] ); ?></strong><?php if ( $fk_item[1] ) : ?><small><?php echo esc_html( $fk_item[1] ); ?> · <?php esc_html_e( 'Verified buyer', 'flavorkit' ); ?></small><?php endif; ?></span>
					</p>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
