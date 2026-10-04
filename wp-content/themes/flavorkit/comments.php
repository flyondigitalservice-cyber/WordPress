<?php
/**
 * Comments.
 *
 * @package FlavorKit
 */

defined( 'ABSPATH' ) || exit;

if ( post_password_required() ) {
	return;
}
?>
<section id="comments" class="fk-comments">
	<?php if ( have_comments() ) : ?>
		<h2 class="fk-comments-title">
			<?php
			/* translators: %s: comment count */
			echo esc_html( sprintf( _n( '%s comment', '%s comments', get_comments_number(), 'flavorkit' ), number_format_i18n( get_comments_number() ) ) );
			?>
		</h2>
		<ol class="comment-list">
			<?php
			wp_list_comments(
				array(
					'style'       => 'ol',
					'short_ping'  => true,
					'avatar_size' => 48,
				)
			);
			?>
		</ol>
		<?php the_comments_navigation(); ?>
	<?php endif; ?>
	<?php comment_form(); ?>
</section>
