<?php
/**
 * Product card for grids. Expects to run inside the loop.
 *
 * @package HadhramiFashion
 */

$hf_id      = get_the_ID();
$hf_kind    = hf_product_kind( $hf_id );
$hf_swatch  = get_post_meta( $hf_id, '_hf_drive_swatch', true );
$hf_express = '1' === get_post_meta( $hf_id, '_hf_express', true );
?>
<article class="card card--<?php echo 'suits' === $hf_kind ? 'suit' : 'shirt'; ?> reveal">
	<a class="card__media" href="<?php the_permalink(); ?>" aria-label="<?php the_title_attribute(); ?>">
		<?php hf_product_image( $hf_id, 700 ); ?>
		<?php if ( 'suits' === $hf_kind ) : ?>
			<span class="card__badge"><?php echo hf_icon( 'needle' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php esc_html_e( 'Tailored in 24h', 'hadhramifashion' ); ?></span>
		<?php elseif ( $hf_express ) : ?>
			<span class="card__badge"><?php echo hf_icon( 'clock' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php esc_html_e( 'At your door in 2h', 'hadhramifashion' ); ?></span>
		<?php endif; ?>
		<?php if ( $hf_swatch ) : ?>
			<span class="card__swatch"><?php hf_drive_img( $hf_swatch, '', 160 ); ?></span>
		<?php endif; ?>
	</a>
	<div class="card__body">
		<a class="card__title" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
		<span class="card__sub"><?php echo esc_html( get_post_meta( $hf_id, '_hf_subtitle', true ) ); ?></span>
		<span class="card__price"><?php echo esc_html( hf_price( get_post_meta( $hf_id, '_hf_price', true ) ) ); ?></span>
	</div>
</article>
