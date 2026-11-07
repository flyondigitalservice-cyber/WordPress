<?php
/**
 * Product card (inside The Loop).
 *
 * @package Tasdir
 */

defined( 'ABSPATH' ) || exit;

$tasdir_id    = get_the_ID();
$tasdir_specs = array_slice( tasdir_product_specs( $tasdir_id ), 0, 4, true );
?>
<article class="card">
	<a class="card__media" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
		<?php echo tasdir_product_image( $tasdir_id, '(min-width: 980px) 560px, (min-width: 640px) 50vw, 100vw' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<span class="card__tag"><?php esc_html_e( 'Export grade', 'tasdir' ); ?></span>
	</a>
	<div class="card__body">
		<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
		<?php if ( has_excerpt() ) : ?>
			<p><?php echo esc_html( get_the_excerpt() ); ?></p>
		<?php endif; ?>
		<?php if ( $tasdir_specs ) : ?>
			<ul class="specs">
				<?php foreach ( $tasdir_specs as $tasdir_label => $tasdir_val ) : ?>
					<li><span><?php echo esc_html( $tasdir_label ); ?></span><span><?php echo esc_html( $tasdir_val ); ?></span></li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
		<div class="card__actions">
			<a class="btn btn--wa" href="<?php echo esc_url( tasdir_wa_url( tasdir_product_wa_message( get_the_title() ) ) ); ?>" target="_blank" rel="noopener"><?php tasdir_the_icon( 'whatsapp' ); ?><?php esc_html_e( 'Get price', 'tasdir' ); ?></a>
			<a class="btn btn--outline" href="<?php the_permalink(); ?>"><?php esc_html_e( 'Details', 'tasdir' ); ?></a>
		</div>
	</div>
</article>
