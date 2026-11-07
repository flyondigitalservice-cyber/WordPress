<?php
/**
 * Single product page.
 *
 * @package Tasdir
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	$tasdir_id    = get_the_ID();
	$tasdir_specs = tasdir_product_specs( $tasdir_id );
	get_template_part( 'template-parts/page-hero', null, array( 'title' => get_the_title() ) );
	?>
	<section class="section">
		<div class="wrap product-layout">
			<div class="product-media">
				<?php echo tasdir_product_image( $tasdir_id, '(min-width: 980px) 620px, 100vw', true ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
			<div class="product-summary">
				<span class="eyebrow"><?php esc_html_e( 'Export grade', 'tasdir' ); ?></span>
				<h2><?php the_title(); ?></h2>
				<?php if ( has_excerpt() ) : ?>
					<p style="font-size:1.1rem;color:var(--muted)"><?php echo esc_html( get_the_excerpt() ); ?></p>
				<?php endif; ?>
				<?php if ( $tasdir_specs ) : ?>
					<ul class="specs">
						<?php foreach ( $tasdir_specs as $tasdir_label => $tasdir_val ) : ?>
							<li><span><?php echo esc_html( $tasdir_label ); ?></span><span><?php echo esc_html( $tasdir_val ); ?></span></li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
				<div class="btns">
					<a class="btn btn--wa" href="<?php echo esc_url( tasdir_wa_url( tasdir_product_wa_message( get_the_title() ) ) ); ?>" target="_blank" rel="noopener"><?php tasdir_the_icon( 'whatsapp' ); ?><?php esc_html_e( 'Get price on WhatsApp', 'tasdir' ); ?></a>
					<a class="btn btn--outline" href="#enquiry" data-scroll><?php esc_html_e( 'Request quotation', 'tasdir' ); ?></a>
				</div>
			</div>
		</div>
	</section>

	<?php if ( get_the_content() ) : ?>
	<section class="section section--cream">
		<div class="wrap">
			<div class="content"><?php the_content(); ?></div>
		</div>
	</section>
	<?php endif; ?>

	<section id="enquiry" class="section">
		<div class="wrap enquiry">
			<div class="enquiry__info">
				<span class="eyebrow"><?php esc_html_e( 'Get a quote', 'tasdir' ); ?></span>
				<?php /* translators: %s: product name. */ ?>
				<h2><?php echo esc_html( sprintf( __( 'Import %s from India', 'tasdir' ), get_the_title() ) ); ?></h2>
				<p><?php esc_html_e( 'Send your quantity and destination port. We reply with price, packing and the next shipment date.', 'tasdir' ); ?></p>
				<?php get_template_part( 'template-parts/contact-list' ); ?>
			</div>
			<?php get_template_part( 'template-parts/enquiry-form', null, array( 'product' => get_the_title() ) ); ?>
		</div>
	</section>
	<?php
endwhile;

get_footer();
