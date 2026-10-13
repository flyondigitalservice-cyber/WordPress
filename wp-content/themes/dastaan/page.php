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
	if ( dastaan_is_elementor_page( get_the_ID() ) ) {
		// Elementor controls this page's layout and headings: give it the full width.
		ob_start();
		the_content();
		$dastaan_builder_html = ob_get_clean();
		echo '<article id="post-' . esc_attr( get_the_ID() ) . '" class="' . esc_attr( implode( ' ', get_post_class( 'page-article page-article--builder' ) ) ) . '">';
		if ( false === stripos( $dastaan_builder_html, '<h1' ) ) {
			// Every page needs one main heading (SEO + screen readers); the visible design stays Elementor's.
			echo '<h1 class="screen-reader-text">' . esc_html( get_the_title() ) . '</h1>';
		}
		echo $dastaan_builder_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- the_content() output.
		echo '</article>';
		continue;
	}
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
