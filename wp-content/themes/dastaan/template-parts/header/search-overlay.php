<?php
/**
 * Search overlay.
 *
 * @package Dastaan
 */

defined( 'ABSPATH' ) || exit;
if ( ! dastaan_option( 'header_show_search' ) ) {
	return;
}
$dastaan_terms = dastaan_has_wc() ? dastaan_get_showcase_categories( 6 ) : array();
?>
<div id="search-overlay" class="search-overlay" aria-hidden="true" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Search', 'dastaan' ); ?>">
	<div class="container search-overlay__inner">
		<form role="search" method="get" class="search-form search-form--overlay" action="<?php echo esc_url( home_url( '/' ) ); ?>">
			<label class="screen-reader-text" for="overlay-search"><?php esc_html_e( 'Search for', 'dastaan' ); ?></label>
			<?php dastaan_the_icon( 'search', array( 'size' => 22 ) ); ?>
			<input id="overlay-search" class="search-field" type="search" name="s" placeholder="<?php esc_attr_e( 'Search chikankari, organza, zardozi…', 'dastaan' ); ?>" autocomplete="off" value="<?php echo esc_attr( get_search_query() ); ?>">
			<?php if ( dastaan_has_wc() ) : ?><input type="hidden" name="post_type" value="product"><?php endif; ?>
			<button type="submit" class="btn btn--primary search-submit"><?php esc_html_e( 'Search', 'dastaan' ); ?></button>
		</form>
		<?php if ( $dastaan_terms ) : ?>
			<p class="search-overlay__label"><?php esc_html_e( 'Popular', 'dastaan' ); ?></p>
			<ul class="search-chips">
				<?php foreach ( $dastaan_terms as $dastaan_t ) : ?>
					<li><a href="<?php echo esc_url( get_term_link( $dastaan_t ) ); ?>"><?php echo esc_html( $dastaan_t->name ); ?></a></li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
		<button class="icon-btn search-overlay__close js-close-search" type="button" aria-label="<?php esc_attr_e( 'Close search', 'dastaan' ); ?>"><?php dastaan_the_icon( 'close', array( 'size' => 26 ) ); ?></button>
	</div>
</div>
