<?php
/**
 * Comments.
 *
 * @package Dastaan
 */

defined( 'ABSPATH' ) || exit;

if ( post_password_required() ) {
	return;
}
?>
<section id="comments" class="comments-area">
	<?php if ( have_comments() ) : ?>
		<h2 class="comments-title">
			<?php
			$dastaan_n = get_comments_number();
			echo esc_html( sprintf( /* translators: %s: count */ _n( '%s comment', '%s comments', $dastaan_n, 'dastaan' ), number_format_i18n( $dastaan_n ) ) );
			?>
		</h2>
		<ol class="comment-list">
			<?php wp_list_comments( array( 'style' => 'ol', 'short_ping' => true, 'avatar_size' => 48 ) ); ?>
		</ol>
		<?php the_comments_navigation(); ?>
		<?php if ( ! comments_open() ) : ?>
			<p class="no-comments"><?php esc_html_e( 'Comments are closed.', 'dastaan' ); ?></p>
		<?php endif; ?>
	<?php endif; ?>
	<?php comment_form(); ?>
</section>
