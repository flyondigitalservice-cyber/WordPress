<?php
/**
 * Search form.
 *
 * @package FlavorKit
 */

defined( 'ABSPATH' ) || exit;

$fk_id = wp_unique_id( 'fk-search-' );
?>
<form role="search" method="get" class="search-form fk-search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="screen-reader-text" for="<?php echo esc_attr( $fk_id ); ?>"><?php esc_html_e( 'Search for:', 'flavorkit' ); ?></label>
	<input type="search" id="<?php echo esc_attr( $fk_id ); ?>" class="search-field" placeholder="<?php esc_attr_e( 'Search…', 'flavorkit' ); ?>" value="<?php echo get_search_query(); ?>" name="s" />
	<button type="submit" class="fk-btn fk-btn-dark"><?php echo flavorkit_icon( 'search', 20 ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span class="screen-reader-text"><?php esc_html_e( 'Search', 'flavorkit' ); ?></span></button>
</form>
