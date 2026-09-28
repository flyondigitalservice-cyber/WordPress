<?php
/**
 * Page hero. Edit per page: heading = "Page hero" box (or the title),
 * sub-heading = Excerpt, background = Featured image.
 *
 * @package DGF
 */

defined( 'ABSPATH' ) || exit;

$dgf_id      = get_queried_object_id();
$dgf_is_home = is_front_page();
$dgf_heading = get_post_meta( $dgf_id, '_dgf_hero_title', true );
$dgf_heading = $dgf_heading ? $dgf_heading : get_the_title( $dgf_id );
$dgf_sub     = has_excerpt( $dgf_id ) ? get_the_excerpt( $dgf_id ) : '';
$dgf_type    = get_post_meta( $dgf_id, '_dgf_type', true );
$dgf_labels  = array(
	'product'  => __( 'Gym flooring system', 'dgf' ),
	'service'  => __( 'Service', 'dgf' ),
	'location' => __( 'Areas we serve', 'dgf' ),
);
$dgf_eyebrow = isset( $dgf_labels[ $dgf_type ] ) ? $dgf_labels[ $dgf_type ] : dgf_opt( 'brand_name' );
if ( 'location' === $dgf_type && get_post_meta( $dgf_id, '_dgf_emirate', true ) ) {
	$dgf_eyebrow = sprintf( '%s · %s', __( 'Gym flooring', 'dgf' ), get_post_meta( $dgf_id, '_dgf_emirate', true ) );
}
$dgf_second = in_array( $dgf_type, array( 'product', 'location', 'service', 'home' ), true )
	? array( __( 'Download catalogue', 'dgf' ), dgf_page_url( 'catalogues' ) )
	: array( __( 'Explore products', 'dgf' ), dgf_page_url( 'gym-flooring-products' ) );

$dgf_thumb_id = (int) get_post_thumbnail_id( $dgf_id );
$dgf_meta     = $dgf_thumb_id ? wp_get_attachment_metadata( $dgf_thumb_id ) : array();
$dgf_width    = is_array( $dgf_meta ) && ! empty( $dgf_meta['width'] ) ? (int) $dgf_meta['width'] : 0;
// Photos narrower than 1600px would look soft stretched full-width, so they are shown framed beside the text instead.
$dgf_split    = $dgf_thumb_id && $dgf_width < 1600;
$dgf_classes  = 'dgf-hero';
$dgf_classes .= $dgf_is_home ? ' dgf-hero--home' : '';
$dgf_classes .= $dgf_thumb_id ? ( $dgf_split ? ' dgf-hero--split' : ' has-image' ) : '';
?>
<section class="<?php echo esc_attr( $dgf_classes ); ?>">
	<?php if ( $dgf_thumb_id && ! $dgf_split ) : ?>
		<?php
		echo wp_get_attachment_image(
			$dgf_thumb_id,
			'dgf-hero',
			false,
			array(
				'class'         => 'dgf-hero__img',
				'loading'       => 'eager',
				'fetchpriority' => 'high',
				'decoding'      => 'async',
				'alt'           => wp_strip_all_tags( $dgf_heading ),
				'sizes'         => '100vw',
			)
		);
		?>
	<?php endif; ?>
	<div class="dgf-hero__shade" aria-hidden="true"></div>
	<div class="dgf-wrap dgf-hero__inner">
		<div class="dgf-hero__text">
			<?php if ( ! $dgf_is_home ) : ?>
				<?php get_template_part( 'template-parts/breadcrumbs' ); ?>
			<?php endif; ?>
			<p class="dgf-eyebrow dgf-hero__eyebrow"><?php echo esc_html( $dgf_eyebrow ); ?></p>
			<h1 class="dgf-hero__title"><?php echo esc_html( wp_strip_all_tags( $dgf_heading ) ); ?></h1>
			<?php if ( $dgf_sub ) : ?>
				<p class="dgf-hero__sub"><?php echo esc_html( $dgf_sub ); ?></p>
			<?php endif; ?>
			<?php if ( ! in_array( $dgf_type, array( 'legal', 'quote', 'contact' ), true ) ) : ?>
				<div class="dgf-hero__actions">
					<a class="dgf-btn dgf-btn--wa dgf-btn--lg" href="<?php echo esc_url( dgf_wa_url( dgf_wa_message_for( $dgf_id ) ) ); ?>" target="_blank" rel="noopener" data-dgf-wa><?php echo dgf_icon( 'whatsapp', 22 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><?php esc_html_e( 'Get a quote on WhatsApp', 'dgf' ); ?></span></a>
					<a class="dgf-btn dgf-btn--ghost dgf-btn--lg" href="<?php echo esc_url( $dgf_second[1] ); ?>"><span><?php echo esc_html( $dgf_second[0] ); ?></span><?php echo dgf_icon( 'arrow', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
				</div>
			<?php endif; ?>
			<?php if ( $dgf_is_home ) : ?>
				<ul class="dgf-hero__proof">
					<li><?php echo dgf_icon( 'check', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php esc_html_e( 'Free survey & samples', 'dgf' ); ?></li>
					<li><?php echo dgf_icon( 'check', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php esc_html_e( 'Our own install crews', 'dgf' ); ?></li>
					<li><?php echo dgf_icon( 'check', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php esc_html_e( 'All 7 emirates', 'dgf' ); ?></li>
				</ul>
			<?php endif; ?>
		</div>
		<?php if ( $dgf_split ) : ?>
			<figure class="dgf-hero__media">
				<?php
				echo wp_get_attachment_image(
					$dgf_thumb_id,
					'large',
					false,
					array(
						'loading'       => 'eager',
						'fetchpriority' => 'high',
						'decoding'      => 'async',
						'alt'           => wp_strip_all_tags( $dgf_heading ),
						'sizes'         => '(max-width: 900px) 100vw, 560px',
					)
				);
				?>
			</figure>
		<?php endif; ?>
	</div>
</section>
