<?php
/**
 * Search form.
 *
 * @package DGF
 */

defined( 'ABSPATH' ) || exit;
?>
<form role="search" method="get" class="dgf-search" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label><span class="screen-reader-text"><?php esc_html_e( 'Search for:', 'dgf' ); ?></span>
		<input type="search" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php esc_attr_e( 'Search flooring…', 'dgf' ); ?>">
	</label>
	<button class="dgf-btn dgf-btn--dark" type="submit"><?php esc_html_e( 'Search', 'dgf' ); ?></button>
</form>
