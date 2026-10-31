<?php
/**
 * Spec/attribute filters for shop pages: [bzr_filters]. Opt in with add_theme_support( 'bazaar-filters' ).
 *
 * The form posts to the same page; a template_redirect handler converts the selection
 * into WooCommerce's native layered-nav URL params (filter_{attribute}=a,b &query_type_{attribute}=or,
 * min_price, max_price), which WooCommerce applies to the main shop query. No JS required and
 * URLs are shareable/cacheable.
 *
 * @package BazaarCore
 */

defined( 'ABSPATH' ) || exit;

/**
 * Convert submitted filter form into clean WooCommerce URL params, then redirect.
 */
function bzr_filters_redirect() {
	if ( ! bzr_feature( 'filters' ) || ! isset( $_GET['bzr_apply'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification -- read-only filter form, no state change.
		return;
	}

	$args = array();

	// phpcs:disable WordPress.Security.NonceVerification
	$selected = isset( $_GET['bzr_f'] ) && is_array( $_GET['bzr_f'] ) ? wp_unslash( $_GET['bzr_f'] ) : array();
	foreach ( $selected as $name => $slugs ) {
		$name = sanitize_key( $name );
		if ( '' === $name || ! is_array( $slugs ) ) {
			continue;
		}
		$slugs = array_filter( array_map( 'sanitize_title', $slugs ) );
		if ( $slugs ) {
			$args[ 'filter_' . $name ]     = implode( ',', $slugs );
			$args[ 'query_type_' . $name ] = 'or';
		}
	}

	foreach ( array( 'min_price', 'max_price' ) as $key ) {
		if ( isset( $_GET[ $key ] ) && '' !== $_GET[ $key ] ) {
			$args[ $key ] = absint( $_GET[ $key ] );
		}
	}
	if ( isset( $_GET['orderby'] ) ) {
		$args['orderby'] = sanitize_key( wp_unslash( $_GET['orderby'] ) );
	}
	$path = isset( $_SERVER['REQUEST_URI'] ) ? (string) wp_parse_url( wp_unslash( $_SERVER['REQUEST_URI'] ), PHP_URL_PATH ) : '/';
	$host = isset( $_SERVER['HTTP_HOST'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) ) : '';
	// phpcs:enable

	$path = preg_replace( '#/page/\d+/?$#', '/', $path );
	$base = set_url_scheme( '//' . $host . $path );

	// wp_safe_redirect() refuses any host that is not this site's, so a spoofed Host header cannot redirect off-site.
	wp_safe_redirect( add_query_arg( $args, $base ) );
	exit;
}
add_action( 'template_redirect', 'bzr_filters_redirect' );

/**
 * Currently chosen slugs for an attribute (from WooCommerce's own URL params).
 *
 * @param string $name Attribute name without "pa_".
 * @return string[]
 */
function bzr_filters_chosen( $name ) {
	$key = 'filter_' . $name;
	if ( empty( $_GET[ $key ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		return array();
	}
	return array_filter( array_map( 'sanitize_title', explode( ',', sanitize_text_field( wp_unslash( $_GET[ $key ] ) ) ) ) ); // phpcs:ignore WordPress.Security.NonceVerification
}

/**
 * Render the filter form.
 *
 * @return string
 */
function bzr_filters_shortcode() {
	if ( ! bzr_feature( 'filters' ) || ! function_exists( 'wc_get_attribute_taxonomies' ) ) {
		return '';
	}

	$action = ( function_exists( 'is_product_taxonomy' ) && is_product_taxonomy() && get_queried_object() )
		? get_term_link( get_queried_object() )
		: wc_get_page_permalink( 'shop' );
	if ( is_wp_error( $action ) ) {
		$action = wc_get_page_permalink( 'shop' );
	}

	$min = isset( $_GET['min_price'] ) ? absint( $_GET['min_price'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
	$max = isset( $_GET['max_price'] ) ? absint( $_GET['max_price'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
	$ord = isset( $_GET['orderby'] ) ? sanitize_key( wp_unslash( $_GET['orderby'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification

	$active = ( '' !== $min || '' !== $max ) ? 1 : 0;
	$groups = '';

	foreach ( wc_get_attribute_taxonomies() as $attr ) {
		$taxonomy = wc_attribute_taxonomy_name( $attr->attribute_name );
		$terms    = get_terms( array( 'taxonomy' => $taxonomy, 'hide_empty' => true, 'number' => 20 ) );
		if ( is_wp_error( $terms ) || empty( $terms ) ) {
			continue;
		}
		$chosen  = bzr_filters_chosen( $attr->attribute_name );
		$active += count( $chosen );
		$items   = '';
		foreach ( $terms as $term ) {
			$items .= sprintf(
				'<li><label><input type="checkbox" name="bzr_f[%1$s][]" value="%2$s"%3$s> <span>%4$s</span></label></li>',
				esc_attr( $attr->attribute_name ),
				esc_attr( $term->slug ),
				checked( in_array( $term->slug, $chosen, true ), true, false ),
				esc_html( $term->name )
			);
		}
		$groups .= sprintf(
			'<details class="bzr-filters__group"%1$s><summary>%2$s</summary><ul>%3$s</ul></details>',
			$chosen ? ' open' : '',
			esc_html( wc_attribute_label( $taxonomy ) ),
			$items
		);
	}

	ob_start();
	?>
	<form class="bzr-filters" method="get" action="<?php echo esc_url( $action ); ?>">
		<input type="hidden" name="bzr_apply" value="1" />
		<?php if ( $ord ) : ?>
			<input type="hidden" name="orderby" value="<?php echo esc_attr( $ord ); ?>" />
		<?php endif; ?>
		<h2 class="bzr-filters__title"><?php esc_html_e( 'Filters', 'bazaar-core' ); ?></h2>
		<details class="bzr-filters__group" open>
			<summary><?php esc_html_e( 'Price', 'bazaar-core' ); ?></summary>
			<div class="bzr-filters__price">
				<label><span class="screen-reader-text"><?php esc_html_e( 'Minimum price', 'bazaar-core' ); ?></span>
					<input type="number" name="min_price" min="0" step="1" inputmode="numeric" placeholder="<?php esc_attr_e( 'Min', 'bazaar-core' ); ?>" value="<?php echo esc_attr( (string) $min ); ?>" /></label>
				<span aria-hidden="true">&ndash;</span>
				<label><span class="screen-reader-text"><?php esc_html_e( 'Maximum price', 'bazaar-core' ); ?></span>
					<input type="number" name="max_price" min="0" step="1" inputmode="numeric" placeholder="<?php esc_attr_e( 'Max', 'bazaar-core' ); ?>" value="<?php echo esc_attr( (string) $max ); ?>" /></label>
			</div>
		</details>
		<?php echo $groups; // phpcs:ignore WordPress.Security.EscapeOutput -- every dynamic value escaped above. ?>
		<div class="bzr-filters__actions">
			<button type="submit" class="wp-element-button"><?php esc_html_e( 'Apply filters', 'bazaar-core' ); ?></button>
			<?php if ( $active ) : ?>
				<a href="<?php echo esc_url( $action ); ?>"><?php esc_html_e( 'Clear all', 'bazaar-core' ); ?></a>
			<?php endif; ?>
		</div>
	</form>
	<?php
	return (string) ob_get_clean();
}
add_shortcode( 'bzr_filters', 'bzr_filters_shortcode' );
