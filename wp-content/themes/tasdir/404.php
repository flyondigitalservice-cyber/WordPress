<?php
/**
 * 404.
 *
 * @package Tasdir
 */

defined( 'ABSPATH' ) || exit;

get_header();
get_template_part( 'template-parts/page-hero', null, array( 'title' => __( 'Page not found', 'tasdir' ) ) );
?>
<section class="section">
	<div class="wrap content" style="text-align:center">
		<p><?php esc_html_e( 'The page you are looking for has moved. Browse our products or message us directly.', 'tasdir' ); ?></p>
		<p>
			<a class="btn btn--navy" href="<?php echo esc_url( (string) get_post_type_archive_link( 'tasdir_product' ) ); ?>"><?php esc_html_e( 'View products', 'tasdir' ); ?></a>
			<a class="btn btn--wa" href="<?php echo esc_url( tasdir_wa_url() ); ?>" target="_blank" rel="noopener"><?php tasdir_the_icon( 'whatsapp' ); ?><?php esc_html_e( 'WhatsApp us', 'tasdir' ); ?></a>
		</p>
	</div>
</section>
<?php
get_footer();
