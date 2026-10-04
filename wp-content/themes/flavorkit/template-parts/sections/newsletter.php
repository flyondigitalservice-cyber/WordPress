<?php
/**
 * Newsletter / offer banner.
 *
 * @package FlavorKit
 */

defined( 'ABSPATH' ) || exit;
?>
<section class="fk-section fk-newsletter" id="newsletter">
	<div class="fk-container">
		<div class="fk-news-card fk-reveal">
			<div class="fk-news-pack fk-news-pack-1" aria-hidden="true"><?php echo flavorkit_pack_svg( 'can', 1, 'BERRY', 'FIZZ' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
			<div class="fk-news-pack fk-news-pack-2" aria-hidden="true"><?php echo flavorkit_pack_svg( 'pouch', 3, 'COMBO', 'PACK' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
			<div class="fk-news-copy">
				<span class="fk-pill"><?php echo flavorkit_icon( 'gift', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput ?> <?php esc_html_e( 'Members only', 'flavorkit' ); ?></span>
				<h2 class="fk-section-title"><?php echo flavorkit_highlight( flavorkit_mod( 'newsletter_title' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h2>
				<?php if ( flavorkit_mod( 'newsletter_text' ) ) : ?>
					<p><?php echo esc_html( flavorkit_mod( 'newsletter_text' ) ); ?></p>
				<?php endif; ?>
				<?php get_template_part( 'template-parts/components/newsletter-form' ); ?>
			</div>
		</div>
	</div>
</section>
