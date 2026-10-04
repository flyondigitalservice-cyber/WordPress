<?php
/**
 * Single page.
 *
 * @package FlavorKit
 */

defined( 'ABSPATH' ) || exit;

get_header();

$fk_is_wc_page = function_exists( 'is_cart' ) && ( is_cart() || is_checkout() || is_account_page() );

while ( have_posts() ) :
	the_post();
	get_template_part( 'template-parts/components/page-header', null, array( 'title' => get_the_title() ) );
	?>
	<main id="primary" class="fk-main fk-section">
		<div class="fk-container<?php echo $fk_is_wc_page ? '' : ' fk-container-narrow'; ?>">
			<article id="post-<?php the_ID(); ?>" <?php post_class( 'fk-entry' ); ?>>
				<?php the_content(); ?>
				<?php wp_link_pages(); ?>
			</article>
			<?php
			if ( comments_open() || get_comments_number() ) {
				comments_template();
			}
			?>
		</div>
	</main>
	<?php
endwhile;

get_footer();
