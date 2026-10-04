<?php
/**
 * Main template: blog index and fallback.
 *
 * @package FlavorKit
 */

defined( 'ABSPATH' ) || exit;

get_header();

if ( is_home() && ! is_front_page() ) {
	$fk_title = single_post_title( '', false );
} elseif ( is_search() ) {
	/* translators: %s search query */
	$fk_title = sprintf( __( 'Results for “%s”', 'flavorkit' ), esc_html( get_search_query() ) );
} elseif ( is_archive() ) {
	$fk_title = get_the_archive_title();
} else {
	$fk_title = __( 'Latest stories', 'flavorkit' );
}

get_template_part(
	'template-parts/components/page-header',
	null,
	array(
		'title' => $fk_title,
		'text'  => is_archive() ? get_the_archive_description() : '',
	)
);
?>
<main id="primary" class="fk-main fk-section">
	<div class="fk-container">
		<?php if ( have_posts() ) : ?>
			<div class="fk-post-grid">
				<?php
				while ( have_posts() ) {
					the_post();
					get_template_part( 'template-parts/components/post-card' );
				}
				?>
			</div>
			<?php
			the_posts_pagination(
				array(
					'prev_text' => flavorkit_icon( 'arrow-left', 18 ) . '<span class="screen-reader-text">' . __( 'Previous', 'flavorkit' ) . '</span>',
					'next_text' => flavorkit_icon( 'arrow', 18 ) . '<span class="screen-reader-text">' . __( 'Next', 'flavorkit' ) . '</span>',
				)
			);
			?>
		<?php else : ?>
			<div class="fk-empty">
				<p><?php esc_html_e( 'Nothing here yet. Try a search instead?', 'flavorkit' ); ?></p>
				<?php get_search_form(); ?>
			</div>
		<?php endif; ?>
	</div>
</main>
<?php
get_footer();
