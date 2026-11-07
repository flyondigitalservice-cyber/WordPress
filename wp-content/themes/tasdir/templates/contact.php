<?php
/**
 * Template Name: Contact (enquiry form)
 *
 * @package Tasdir
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	get_template_part(
		'template-parts/page-hero',
		null,
		array(
			'title'    => get_the_title(),
			'subtitle' => __( 'Talk to our export desk — fastest on WhatsApp.', 'tasdir' ),
		)
	);
	?>
	<section id="enquiry" class="section">
		<div class="wrap enquiry">
			<div class="enquiry__info">
				<h2><?php esc_html_e( 'Get in touch', 'tasdir' ); ?></h2>
				<?php
				if ( get_the_content() ) {
					echo '<div class="content" style="margin:0">';
					the_content();
					echo '</div>';
				} else {
					echo '<p>' . esc_html__( 'Send us your requirement and we will reply with a quotation, packing options and the next available shipment.', 'tasdir' ) . '</p>';
				}
				get_template_part( 'template-parts/contact-list' );
				?>
			</div>
			<?php get_template_part( 'template-parts/enquiry-form' ); ?>
		</div>
	</section>
	<?php
endwhile;

get_footer();
