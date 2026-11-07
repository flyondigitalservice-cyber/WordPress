<?php
/**
 * Products archive (/products/).
 *
 * @package Tasdir
 */

defined( 'ABSPATH' ) || exit;

get_header();

get_template_part(
	'template-parts/page-hero',
	null,
	array(
		'title'    => __( 'Export Products', 'tasdir' ),
		'subtitle' => __( 'Premium fresh fruit from India, graded and packed for international buyers.', 'tasdir' ),
	)
);
?>
<section class="section section--cream">
	<div class="wrap">
		<?php if ( have_posts() ) : ?>
			<div class="grid grid--2">
				<?php
				while ( have_posts() ) {
					the_post();
					get_template_part( 'template-parts/product-card' );
				}
				?>
			</div>
		<?php else : ?>
			<p><?php esc_html_e( 'Products coming soon.', 'tasdir' ); ?></p>
		<?php endif; ?>
	</div>
</section>
<?php
get_template_part( 'template-parts/cta-band' );
get_footer();
