<?php
/**
 * Shop by category.
 *
 * @package FlavorKit
 */

defined( 'ABSPATH' ) || exit;

$fk_cards = array();

if ( taxonomy_exists( 'product_cat' ) ) {
	$fk_terms = get_terms(
		array(
			'taxonomy'   => 'product_cat',
			'hide_empty' => true,
			'parent'     => 0,
			'number'     => 6,
			'exclude'    => array( (int) get_option( 'default_product_cat' ) ),
		)
	);
	if ( ! is_wp_error( $fk_terms ) ) {
		$fk_shapes = array( 'pouch', 'can', 'jar', 'box', 'bottle' );
		foreach ( $fk_terms as $fk_i => $fk_term ) {
			$fk_thumb   = (int) get_term_meta( $fk_term->term_id, 'thumbnail_id', true );
			$fk_cards[] = array(
				'name'  => $fk_term->name,
				'url'   => get_term_link( $fk_term ),
				'image' => $fk_thumb ? wp_get_attachment_image( $fk_thumb, 'flavorkit-card', false, array( 'loading' => 'lazy' ) ) : '',
				'shape' => $fk_shapes[ $fk_i % count( $fk_shapes ) ],
				'count' => $fk_term->count,
			);
		}
	}
}

if ( ! $fk_cards ) {
	foreach ( flavorkit_lines( flavorkit_mod( 'cat_items' ), 2 ) as $fk_item ) {
		$fk_cards[] = array(
			'name'  => $fk_item[0],
			'url'   => flavorkit_shop_url(),
			'image' => '',
			'shape' => $fk_item[1] ? $fk_item[1] : 'pouch',
			'count' => 0,
		);
	}
}

if ( ! $fk_cards ) {
	return;
}
?>
<section class="fk-section fk-categories" id="categories">
	<div class="fk-container">
		<?php flavorkit_section_heading( '', flavorkit_mod( 'cat_title' ) ); ?>
		<ul class="fk-cat-grid">
			<?php foreach ( $fk_cards as $fk_i => $fk_card ) : ?>
				<li class="fk-reveal" style="--fk-delay:<?php echo esc_attr( $fk_i * 70 ); ?>ms">
					<a class="fk-cat-card" href="<?php echo esc_url( $fk_card['url'] ); ?>">
						<span class="fk-cat-media fk-tone-<?php echo esc_attr( $fk_i % 4 ); ?>">
							<?php
							echo $fk_card['image'] ? $fk_card['image'] : flavorkit_pack_svg( $fk_card['shape'], $fk_i, strtoupper( $fk_card['name'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput
							?>
						</span>
						<span class="fk-cat-name"><?php echo esc_html( $fk_card['name'] ); ?> <?php echo flavorkit_icon( 'arrow', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
