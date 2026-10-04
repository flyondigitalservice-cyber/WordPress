<?php
/**
 * 404 page.
 *
 * @package FlavorKit
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="primary" class="fk-main fk-section fk-404">
	<div class="fk-container fk-center">
		<div class="fk-404-art" aria-hidden="true"><?php echo flavorkit_pack_svg( 'pouch', 0, '404', 'ALL GONE' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
		<h1 class="fk-section-title"><?php echo flavorkit_highlight( __( 'Oops, someone *ate* this page.', 'flavorkit' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h1>
		<p><?php esc_html_e( 'The page you are looking for doesn\'t exist. Grab a snack from the shop instead.', 'flavorkit' ); ?></p>
		<p><a class="fk-btn fk-btn-primary fk-btn-lg" href="<?php echo esc_url( flavorkit_shop_url() ); ?>"><?php esc_html_e( 'Back to shopping', 'flavorkit' ); ?></a></p>
	</div>
</main>
<?php
get_footer();
