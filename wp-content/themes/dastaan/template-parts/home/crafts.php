<?php
/**
 * Craft showcase: editorial category tiles.
 *
 * @package Dastaan
 */

defined( 'ABSPATH' ) || exit;

$dastaan_force = ! empty( $args['force'] );
if ( ! $dastaan_force && ! dastaan_option( 'crafts_enable' ) ) {
	return;
}
$dastaan_terms = dastaan_get_showcase_categories( (int) dastaan_option( 'crafts_count' ) );
if ( ! $dastaan_terms ) {
	return;
}
?>
<section class="section crafts">
	<div class="container">
		<?php dastaan_section_heading( array( 'eyebrow' => dastaan_option( 'crafts_eyebrow' ), 'title' => dastaan_option( 'crafts_title' ), 'text' => dastaan_option( 'crafts_text' ) ) ); ?>
		<div class="crafts__grid">
			<?php foreach ( $dastaan_terms as $dastaan_i => $dastaan_t ) : ?>
				<a class="craft-tile<?php echo 0 === $dastaan_i ? ' craft-tile--feature' : ''; ?>" href="<?php echo esc_url( get_term_link( $dastaan_t ) ); ?>">
					<img src="<?php echo esc_url( dastaan_category_image( $dastaan_t, 'dastaan-tile' ) ); ?>" alt="<?php echo esc_attr( $dastaan_t->name ); ?>" loading="lazy" decoding="async">
					<span class="craft-tile__content">
						<span class="craft-tile__title"><?php echo esc_html( $dastaan_t->name ); ?></span>
						<?php if ( $dastaan_t->description ) : ?><span class="craft-tile__text"><?php echo esc_html( wp_trim_words( $dastaan_t->description, 14 ) ); ?></span><?php endif; ?>
						<span class="craft-tile__count"><?php echo esc_html( sprintf( /* translators: %d: count */ _n( '%d piece', '%d pieces', (int) $dastaan_t->count, 'dastaan' ), (int) $dastaan_t->count ) ); ?> <?php dastaan_the_icon( 'arrow-right', array( 'size' => 14 ) ); ?></span>
					</span>
				</a>
			<?php endforeach; ?>
		</div>
	</div>
</section>
