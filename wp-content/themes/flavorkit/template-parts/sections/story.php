<?php
/**
 * Brand story split section.
 *
 * @package FlavorKit
 */

defined( 'ABSPATH' ) || exit;

$fk_image = flavorkit_mod( 'story_image' );
$fk_stats = flavorkit_lines( flavorkit_mod( 'story_stats' ), 2 );
$fk_paras = array_filter( array_map( 'trim', preg_split( '/\n\s*\n/', (string) flavorkit_mod( 'story_text' ) ) ) );
?>
<section class="fk-section fk-story" id="story">
	<div class="fk-container fk-story-grid">
		<div class="fk-story-media fk-reveal">
			<?php if ( $fk_image ) : ?>
				<?php echo flavorkit_image( $fk_image, 'flavorkit-wide', 'fk-story-img', get_bloginfo( 'name' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<?php else : ?>
				<div class="fk-story-illus" aria-hidden="true">
					<div class="fk-story-pack fk-story-pack-1"><?php echo flavorkit_pack_svg( 'box', 0, 'CHOCO', 'GRANOLA' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
					<div class="fk-story-pack fk-story-pack-2"><?php echo flavorkit_pack_svg( 'bottle', 1, 'BERRY', 'KOMBUCHA' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
				</div>
			<?php endif; ?>
			<span class="fk-sticker fk-sticker-made" aria-hidden="true"><?php esc_html_e( 'Made with love', 'flavorkit' ); ?> ♥</span>
		</div>

		<div class="fk-story-copy fk-reveal">
			<?php if ( flavorkit_mod( 'story_eyebrow' ) ) : ?>
				<p class="fk-eyebrow"><?php echo esc_html( flavorkit_mod( 'story_eyebrow' ) ); ?></p>
			<?php endif; ?>
			<h2 class="fk-section-title"><?php echo flavorkit_highlight( flavorkit_mod( 'story_title' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h2>
			<?php foreach ( $fk_paras as $fk_para ) : ?>
				<p><?php echo esc_html( $fk_para ); ?></p>
			<?php endforeach; ?>

			<?php if ( $fk_stats ) : ?>
				<dl class="fk-stats">
					<?php foreach ( $fk_stats as $fk_stat ) : ?>
						<div><dt><?php echo esc_html( $fk_stat[1] ); ?></dt><dd><?php echo esc_html( $fk_stat[0] ); ?></dd></div>
					<?php endforeach; ?>
				</dl>
			<?php endif; ?>

			<?php if ( flavorkit_mod( 'story_btn_text' ) ) : ?>
				<a class="fk-btn fk-btn-dark" href="<?php echo esc_url( flavorkit_link( flavorkit_mod( 'story_btn_url' ) ) ); ?>"><?php echo esc_html( flavorkit_mod( 'story_btn_text' ) ); ?> <?php echo flavorkit_icon( 'arrow', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
			<?php endif; ?>
		</div>
	</div>
</section>
