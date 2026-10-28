<?php
/**
 * Page hero. Edit per page: heading = "Page hero" box (or the title),
 * sub-heading = Excerpt, background = Featured image.
 *
 * @package CFM
 */

defined( 'ABSPATH' ) || exit;

$cfm_id      = get_queried_object_id();
$cfm_is_home = is_front_page();
$cfm_heading = get_post_meta( $cfm_id, '_cfm_hero_title', true );
$cfm_heading = $cfm_heading ? $cfm_heading : get_the_title( $cfm_id );
$cfm_sub     = has_excerpt( $cfm_id ) ? get_the_excerpt( $cfm_id ) : '';
$cfm_type    = get_post_meta( $cfm_id, '_cfm_type', true );
$cfm_labels  = array(
	'product'  => __( 'Custom floor mats', 'cfm' ),
	'service'  => __( 'Service', 'cfm' ),
	'location' => __( 'Areas we serve', 'cfm' ),
);
$cfm_eyebrow = isset( $cfm_labels[ $cfm_type ] ) ? $cfm_labels[ $cfm_type ] : cfm_opt( 'brand_name' );
if ( 'interior' === $cfm_type && wp_get_post_parent_id( $cfm_id ) ) {
	$cfm_eyebrow = sprintf( '%s · %s', wp_strip_all_tags( get_the_title( wp_get_post_parent_id( $cfm_id ) ) ), cfm_opt( 'parent_name' ) );
}
if ( 'location' === $cfm_type && get_post_meta( $cfm_id, '_cfm_emirate', true ) ) {
	$cfm_eyebrow = sprintf( '%s · %s', __( 'Floor mats', 'cfm' ), get_post_meta( $cfm_id, '_cfm_emirate', true ) );
}
$cfm_second = in_array( $cfm_type, array( 'product', 'location', 'service', 'home' ), true )
	? array( __( 'Download catalogue', 'cfm' ), cfm_page_url( 'catalogues' ) )
	: array( __( 'Explore mats', 'cfm' ), cfm_page_url( 'floor-mats' ) );
if ( 'interior' === $cfm_type && wp_get_post_parent_id( $cfm_id ) ) {
	/* translators: %s: category name, e.g. Curtains */
	$cfm_second = array( sprintf( __( 'All %s', 'cfm' ), wp_strip_all_tags( get_the_title( wp_get_post_parent_id( $cfm_id ) ) ) ), get_permalink( wp_get_post_parent_id( $cfm_id ) ) );
}

$cfm_thumb_id = (int) get_post_thumbnail_id( $cfm_id );
$cfm_meta     = $cfm_thumb_id ? wp_get_attachment_metadata( $cfm_thumb_id ) : array();
$cfm_width    = is_array( $cfm_meta ) && ! empty( $cfm_meta['width'] ) ? (int) $cfm_meta['width'] : 0;
// Photos narrower than 1600px would look soft stretched full-width, so they are shown framed beside the text instead.
$cfm_split    = $cfm_thumb_id && $cfm_width < 1600;
$cfm_classes  = 'cfm-hero';
$cfm_classes .= $cfm_is_home ? ' cfm-hero--home' : '';
$cfm_classes .= $cfm_thumb_id ? ( $cfm_split ? ' cfm-hero--split' : ' has-image' ) : '';
?>
<section class="<?php echo esc_attr( $cfm_classes ); ?>">
	<?php if ( $cfm_thumb_id && ! $cfm_split ) : ?>
		<?php
		echo wp_get_attachment_image(
			$cfm_thumb_id,
			'cfm-hero',
			false,
			array(
				'class'         => 'cfm-hero__img',
				'loading'       => 'eager',
				'fetchpriority' => 'high',
				'decoding'      => 'async',
				'alt'           => wp_strip_all_tags( $cfm_heading ),
				'sizes'         => '100vw',
			)
		);
		?>
	<?php endif; ?>
	<div class="cfm-hero__shade" aria-hidden="true"></div>
	<div class="cfm-wrap cfm-hero__inner">
		<div class="cfm-hero__text">
			<?php if ( ! $cfm_is_home ) : ?>
				<?php get_template_part( 'template-parts/breadcrumbs' ); ?>
			<?php endif; ?>
			<p class="cfm-eyebrow cfm-hero__eyebrow"><?php echo esc_html( $cfm_eyebrow ); ?></p>
			<h1 class="cfm-hero__title"><?php echo esc_html( wp_strip_all_tags( $cfm_heading ) ); ?></h1>
			<?php if ( $cfm_sub ) : ?>
				<p class="cfm-hero__sub"><?php echo esc_html( $cfm_sub ); ?></p>
			<?php endif; ?>
			<?php if ( ! in_array( $cfm_type, array( 'legal', 'quote', 'contact' ), true ) ) : ?>
				<div class="cfm-hero__actions">
					<a class="cfm-btn cfm-btn--wa cfm-btn--lg" href="<?php echo esc_url( cfm_wa_url( cfm_wa_message_for( $cfm_id ) ) ); ?>" target="_blank" rel="noopener" data-cfm-wa><?php echo cfm_icon( 'whatsapp', 22 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><?php esc_html_e( 'Get a quote on WhatsApp', 'cfm' ); ?></span></a>
					<a class="cfm-btn cfm-btn--ghost cfm-btn--lg" href="<?php echo esc_url( $cfm_second[1] ); ?>"><span><?php echo esc_html( $cfm_second[0] ); ?></span><?php echo cfm_icon( 'arrow', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
				</div>
			<?php endif; ?>
			<?php if ( $cfm_is_home ) : ?>
				<ul class="cfm-hero__proof">
					<li><?php echo cfm_icon( 'check', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php esc_html_e( 'Free logo mockup', 'cfm' ); ?></li>
					<li><?php echo cfm_icon( 'check', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php esc_html_e( 'Made to any size', 'cfm' ); ?></li>
					<li><?php echo cfm_icon( 'check', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php esc_html_e( 'All 7 emirates', 'cfm' ); ?></li>
				</ul>
			<?php endif; ?>
		</div>
		<?php if ( $cfm_split ) : ?>
			<figure class="cfm-hero__media">
				<?php
				echo wp_get_attachment_image(
					$cfm_thumb_id,
					'large',
					false,
					array(
						'loading'       => 'eager',
						'fetchpriority' => 'high',
						'decoding'      => 'async',
						'alt'           => wp_strip_all_tags( $cfm_heading ),
						'sizes'         => '(max-width: 900px) 100vw, 560px',
					)
				);
				?>
			</figure>
		<?php endif; ?>
	</div>
</section>
