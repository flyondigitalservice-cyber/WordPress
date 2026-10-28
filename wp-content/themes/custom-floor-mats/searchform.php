<?php
/**
 * Search form.
 *
 * @package CFM
 */

defined( 'ABSPATH' ) || exit;
?>
<form role="search" method="get" class="cfm-search" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label><span class="screen-reader-text"><?php esc_html_e( 'Search for:', 'cfm' ); ?></span>
		<input type="search" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php esc_attr_e( 'Search flooring…', 'cfm' ); ?>">
	</label>
	<button class="cfm-btn cfm-btn--dark" type="submit"><?php esc_html_e( 'Search', 'cfm' ); ?></button>
</form>
