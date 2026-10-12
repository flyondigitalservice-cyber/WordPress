<?php
/**
 * Search form.
 *
 * @package Dastaan
 */

defined( 'ABSPATH' ) || exit;
$dastaan_id = uniqid( 'search-' );
?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="screen-reader-text" for="<?php echo esc_attr( $dastaan_id ); ?>"><?php esc_html_e( 'Search for', 'dastaan' ); ?></label>
	<input id="<?php echo esc_attr( $dastaan_id ); ?>" type="search" class="search-field" placeholder="<?php esc_attr_e( 'Search…', 'dastaan' ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>" name="s">
	<?php if ( dastaan_has_wc() ) : ?><input type="hidden" name="post_type" value="product"><?php endif; ?>
	<button type="submit" class="search-submit" aria-label="<?php esc_attr_e( 'Search', 'dastaan' ); ?>"><?php dastaan_the_icon( 'search', array( 'size' => 18 ) ); ?></button>
</form>
