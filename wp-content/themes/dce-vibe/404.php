<?php
/**
 * 404.
 *
 * @package dce-vibe
 */

get_header();
?>
<main id="primary" class="dce-main">
	<div class="dce-container dce-404">
		<p class="dce-kicker"><?php esc_html_e( 'Page not found', 'dce-vibe' ); ?></p>
		<h1><?php esc_html_e( 'This window is still waiting for its curtains', 'dce-vibe' ); ?></h1>
		<p><?php esc_html_e( 'The page you were looking for has moved. Try the menu, or message us and we will help.', 'dce-vibe' ); ?></p>
		<p>
			<a class="dce-btn dce-btn-dark" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Back to home', 'dce-vibe' ); ?></a>
			<a class="dce-btn dce-btn-wa" href="<?php echo esc_url( dce_wa_url() ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'WhatsApp us', 'dce-vibe' ); ?></a>
		</p>
	</div>
</main>
<?php
get_footer();
