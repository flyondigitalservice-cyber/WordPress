<?php
/**
 * Logo / wordmark.
 *
 * @package Dastaan
 */

defined( 'ABSPATH' ) || exit;

if ( has_custom_logo() ) {
	the_custom_logo();
	return;
}
$dastaan_name = get_bloginfo( 'name' );
$dastaan_main = $dastaan_name;
$dastaan_sub  = '';
if ( preg_match( '/^(.*?)\s+(by\s+.*)$/i', $dastaan_name, $m ) ) {
	$dastaan_main = $m[1];
	$dastaan_sub  = $m[2];
}
?>
<a class="site-title" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
	<span class="wordmark"><?php echo esc_html( $dastaan_main ); ?></span>
	<?php if ( $dastaan_sub ) : ?><span class="wordmark-sub"><?php echo esc_html( $dastaan_sub ); ?></span><?php endif; ?>
</a>
