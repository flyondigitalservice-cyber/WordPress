<?php
/**
 * Hero banner.
 *
 * @package FlavorKit
 */

defined( 'ABSPATH' ) || exit;

$fk_image  = flavorkit_mod( 'hero_image' );
$fk_badges = flavorkit_pipe_list( flavorkit_mod( 'hero_badges' ) );
$fk_rating = flavorkit_mod( 'hero_rating' );
?>
<section class="fk-hero" aria-labelledby="fk-hero-title">
	<div class="fk-hero-blob fk-hero-blob-1" aria-hidden="true"></div>
	<div class="fk-hero-blob fk-hero-blob-2" aria-hidden="true"></div>

	<div class="fk-container fk-hero-grid">
		<div class="fk-hero-copy">
			<?php if ( flavorkit_mod( 'hero_eyebrow' ) ) : ?>
				<p class="fk-pill"><?php echo flavorkit_icon( 'sparkle', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput ?> <?php echo esc_html( flavorkit_mod( 'hero_eyebrow' ) ); ?></p>
			<?php endif; ?>

			<h1 id="fk-hero-title" class="fk-hero-title"><?php echo flavorkit_highlight( flavorkit_mod( 'hero_title' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h1>

			<?php if ( flavorkit_mod( 'hero_text' ) ) : ?>
				<p class="fk-hero-text"><?php echo esc_html( flavorkit_mod( 'hero_text' ) ); ?></p>
			<?php endif; ?>

			<div class="fk-btn-row">
				<?php if ( flavorkit_mod( 'hero_btn1_text' ) ) : ?>
					<a class="fk-btn fk-btn-dark fk-btn-lg" href="<?php echo esc_url( flavorkit_link( flavorkit_mod( 'hero_btn1_url' ) ) ); ?>">
						<?php echo esc_html( flavorkit_mod( 'hero_btn1_text' ) ); ?> <?php echo flavorkit_icon( 'arrow', 20 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					</a>
				<?php endif; ?>
				<?php if ( flavorkit_mod( 'hero_btn2_text' ) ) : ?>
					<a class="fk-btn fk-btn-light fk-btn-lg" href="<?php echo esc_url( flavorkit_link( flavorkit_mod( 'hero_btn2_url' ) ) ); ?>"><?php echo esc_html( flavorkit_mod( 'hero_btn2_text' ) ); ?></a>
				<?php endif; ?>
			</div>

			<?php if ( $fk_rating ) : ?>
				<p class="fk-hero-rating"><?php echo flavorkit_stars( 5 ); // phpcs:ignore WordPress.Security.EscapeOutput ?> <span><?php echo esc_html( $fk_rating ); ?></span></p>
			<?php endif; ?>

			<?php if ( $fk_badges ) : ?>
				<ul class="fk-hero-badges">
					<?php foreach ( $fk_badges as $fk_badge ) : ?>
						<li><?php echo flavorkit_icon( 'check', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput ?> <?php echo esc_html( $fk_badge ); ?></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>

		<div class="fk-hero-visual" aria-hidden="<?php echo $fk_image ? 'false' : 'true'; ?>">
			<div class="fk-hero-disc"></div>
			<?php if ( $fk_image ) : ?>
				<?php echo flavorkit_image( $fk_image, 'large', 'fk-hero-img', get_bloginfo( 'name' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<?php else : ?>
				<div class="fk-hero-packs">
					<div class="fk-float fk-float-1"><?php echo flavorkit_pack_svg( 'pouch', 1, 'PERI PERI', 'MAKHANA' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
					<div class="fk-float fk-float-2"><?php echo flavorkit_pack_svg( 'can', 0, 'MANGO', 'CHILLI FIZZ' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
					<div class="fk-float fk-float-3"><?php echo flavorkit_pack_svg( 'jar', 2, 'MASALA', 'CLASSIC' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
				</div>
			<?php endif; ?>
			<div class="fk-sticker fk-sticker-spin" aria-hidden="true">
				<svg viewBox="0 0 120 120"><defs><path id="fk-circle-text" d="M60,60 m-44,0 a44,44 0 1,1 88,0 a44,44 0 1,1 -88,0"/></defs><text><textPath href="#fk-circle-text"><?php echo esc_html( strtoupper( __( '100% natural • no nasties • ', 'flavorkit' ) ) ); ?></textPath></text></svg>
				<span><?php echo flavorkit_icon( 'leaf', 30 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
			</div>
			<div class="fk-sticker fk-sticker-new" aria-hidden="true"><?php esc_html_e( 'NEW!', 'flavorkit' ); ?></div>
		</div>
	</div>
	<?php flavorkit_wave( 'bottom', 'var(--fk-cream)' ); ?>
</section>
