<?php
/**
 * Instagram / UGC grid.
 *
 * @package FlavorKit
 */

defined( 'ABSPATH' ) || exit;

$fk_ids   = array_filter( array_map( 'absint', explode( ',', (string) flavorkit_mod( 'ugc_images' ) ) ) );
$fk_url   = flavorkit_mod( 'ugc_url' );
$fk_demo  = flavorkit_demo_products();
$fk_tiles = $fk_ids ? array_slice( $fk_ids, 0, 6 ) : range( 0, 5 );
?>
<section class="fk-section fk-ugc">
	<div class="fk-container">
		<?php flavorkit_section_heading( '', flavorkit_mod( 'ugc_title' ), flavorkit_mod( 'ugc_text' ) ); ?>
		<ul class="fk-ugc-grid">
			<?php foreach ( $fk_tiles as $fk_i => $fk_tile ) : ?>
				<li class="fk-reveal" style="--fk-delay:<?php echo esc_attr( $fk_i * 60 ); ?>ms">
					<a class="fk-ugc-tile fk-tone-<?php echo esc_attr( $fk_i % 4 ); ?>" href="<?php echo esc_url( $fk_url ? $fk_url : '#' ); ?>" target="_blank" rel="noopener">
						<?php
						if ( $fk_ids ) {
							echo wp_get_attachment_image( $fk_tile, 'flavorkit-card', false, array( 'loading' => 'lazy' ) );
						} else {
							$fk_p = $fk_demo[ $fk_i % count( $fk_demo ) ];
							echo flavorkit_pack_svg( $fk_p['shape'], $fk_p['palette'], $fk_p['label'], $fk_p['sub'] ); // phpcs:ignore WordPress.Security.EscapeOutput
						}
						?>
						<span class="fk-ugc-overlay"><?php echo flavorkit_icon( 'instagram', 32 ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span class="screen-reader-text"><?php esc_html_e( 'View on Instagram', 'flavorkit' ); ?></span></span>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
