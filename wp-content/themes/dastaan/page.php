<?php
/**
 * Page template.
 *
 * @package Dastaan
 */

defined( 'ABSPATH' ) || exit;
get_header();

while ( have_posts() ) :
	the_post();
	$dastaan_is_wc_page = dastaan_has_wc() && ( is_cart() || is_checkout() || is_account_page() );
	?>
	<article id="post-<?php the_ID(); ?>" <?php post_class( 'page-article' ); ?>>
		<div class="container page-wrap<?php echo $dastaan_is_wc_page ? ' page-wrap--wc' : ' page-wrap--narrow'; ?>">
			<?php dastaan_breadcrumbs(); ?>
			<header class="page-header">
				<h1 class="page-title">
					<?php
					if ( function_exists( 'is_wc_endpoint_url' ) && is_wc_endpoint_url( 'order-received' ) ) {
						esc_html_e( 'Order confirmed', 'dastaan' );
					} elseif ( function_exists( 'is_wc_endpoint_url' ) && is_wc_endpoint_url( 'order-pay' ) ) {
						esc_html_e( 'Complete your payment', 'dastaan' );
					} else {
						the_title();
					}
					?>
				</h1>
			</header>
			<?php if ( has_post_thumbnail() && ! $dastaan_is_wc_page ) : ?>
				<figure class="page-thumbnail"><?php the_post_thumbnail( 'large' ); ?></figure>
			<?php endif; ?>
			<div class="entry-content">
				<?php
				the_content();
				wp_link_pages( array( 'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'dastaan' ), 'after' => '</div>' ) );
				?>
			</div>
			<?php
			if ( comments_open() || get_comments_number() ) {
				comments_template();
			}
			?>
		</div>
	</article>
	<?php
endwhile;

get_footer();
