<?php
/**
 * Raikwar Seal <head> partial.
 *
 * The <style type="text/tailwindcss"> block is the Tailwind v4 token
 * source the browser CDN reads in WPVibe draft preview. Do not remove it.
 */
?>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">

<?php if ( get_option( 'wpvibe_draft_theme' ) === get_stylesheet() ) : ?>
<style type="text/tailwindcss"><?php
	$theme_css_path = get_stylesheet_directory() . '/theme.css';
	if ( file_exists( $theme_css_path ) ) {
		readfile( $theme_css_path );
	}
?></style>
<?php endif; ?>

<?php wp_head(); ?>
