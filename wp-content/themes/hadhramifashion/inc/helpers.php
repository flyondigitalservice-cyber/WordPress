<?php
/**
 * Small template helpers shared across the theme.
 *
 * @package HadhramiFashion
 */

defined( 'ABSPATH' ) || exit;

/**
 * Read a theme option with a default.
 *
 * @param string $key     Option key inside the `hf_settings` array.
 * @param mixed  $default Fallback value.
 * @return mixed
 */
function hf_opt( $key, $default = null ) {
	$settings = get_option( 'hf_settings', array() );
	$defaults = hf_default_settings();

	if ( isset( $settings[ $key ] ) && '' !== $settings[ $key ] ) {
		return $settings[ $key ];
	}
	if ( null !== $default ) {
		return $default;
	}
	return isset( $defaults[ $key ] ) ? $defaults[ $key ] : '';
}

/**
 * Bundled catalogue data (brand imagery, suits, shirts).
 *
 * @return array
 */
function hf_catalog_data() {
	static $data = null;
	if ( null === $data ) {
		$data = require HF_DIR . '/inc/catalog-data.php';
	}
	return $data;
}

/**
 * Public URL for a Google Drive image that is shared "anyone with the link".
 *
 * @param string $file_id Drive file ID.
 * @param int    $width   Requested width in pixels.
 * @return string
 */
function hf_drive_url( $file_id, $width = 1200 ) {
	if ( ! $file_id ) {
		return '';
	}
	return sprintf( 'https://lh3.googleusercontent.com/d/%s=w%d', rawurlencode( $file_id ), (int) $width );
}

/**
 * Fallback Drive thumbnail endpoint, used if the primary URL fails to load.
 *
 * @param string $file_id Drive file ID.
 * @param int    $width   Requested width.
 * @return string
 */
function hf_drive_fallback_url( $file_id, $width = 1200 ) {
	return sprintf( 'https://drive.google.com/thumbnail?id=%s&sz=w%d', rawurlencode( $file_id ), (int) $width );
}

/**
 * Resolve a brand image slot to a URL.
 *
 * A slot can be overridden in Appearance → Customize with an uploaded image;
 * otherwise an imported attachment is used, otherwise the Drive original.
 *
 * @param string $slot  Key from the `brand` array in catalog-data.php.
 * @param int    $width Requested width.
 * @return string
 */
function hf_brand_image( $slot, $width = 1800 ) {
	$custom = get_theme_mod( 'hf_img_' . $slot );
	if ( $custom ) {
		return esc_url_raw( $custom );
	}

	$data    = hf_catalog_data();
	$file_id = isset( $data['brand'][ $slot ] ) ? $data['brand'][ $slot ] : '';
	if ( ! $file_id ) {
		return '';
	}

	$attachment = hf_attachment_for_drive_id( $file_id );
	if ( $attachment ) {
		$src = wp_get_attachment_image_url( $attachment, 'full' );
		if ( $src ) {
			return $src;
		}
	}
	return hf_drive_url( $file_id, $width );
}

/**
 * Find a Media Library attachment previously imported from a Drive file.
 *
 * @param string $file_id Drive file ID.
 * @return int Attachment ID or 0.
 */
function hf_attachment_for_drive_id( $file_id ) {
	$map = get_option( 'hf_drive_imports', array() );
	return isset( $map[ $file_id ] ) ? (int) $map[ $file_id ] : 0;
}

/**
 * Print an <img> for a Drive file with a graceful fallback chain.
 *
 * @param string $file_id Drive file ID.
 * @param string $alt     Alt text.
 * @param int    $width   Requested width.
 * @param string $class   CSS class.
 * @param bool   $lazy    Lazy-load the image.
 */
function hf_drive_img( $file_id, $alt = '', $width = 900, $class = '', $lazy = true ) {
	$attachment = hf_attachment_for_drive_id( $file_id );
	if ( $attachment ) {
		echo wp_get_attachment_image(
			$attachment,
			$width > 1000 ? 'full' : 'large',
			false,
			array(
				'class'   => $class,
				'alt'     => $alt,
				'loading' => $lazy ? 'lazy' : 'eager',
			)
		);
		return;
	}

	printf(
		'<img src="%1$s" alt="%2$s" class="%3$s" loading="%4$s" decoding="async" referrerpolicy="no-referrer" data-fallback="%5$s" onerror="if(this.dataset.fallback&&this.src!==this.dataset.fallback){this.src=this.dataset.fallback;}">',
		esc_url( hf_drive_url( $file_id, $width ) ),
		esc_attr( $alt ),
		esc_attr( $class ),
		$lazy ? 'lazy' : 'eager',
		esc_url( hf_drive_fallback_url( $file_id, $width ) )
	);
}

/**
 * Format a price with the configured currency.
 *
 * @param float $amount Amount.
 * @return string
 */
function hf_price( $amount ) {
	$symbol = hf_opt( 'currency_symbol' );
	return $symbol . number_format_i18n( (float) $amount, 0 );
}

/**
 * URL of the first page using a given page template.
 *
 * @param string $template Template file, e.g. `page-templates/checkout.php`.
 * @return string
 */
function hf_page_url( $template ) {
	$pages = get_posts(
		array(
			'post_type'      => 'page',
			'post_status'    => 'publish',
			'meta_key'       => '_wp_page_template',
			'meta_value'     => $template,
			'posts_per_page' => 1,
			'fields'         => 'ids',
		)
	);
	return $pages ? get_permalink( $pages[0] ) : home_url( '/' );
}

/**
 * Inline SVG icon set (stroke icons, inherit currentColor).
 *
 * @param string $name Icon name.
 * @return string
 */
function hf_icon( $name ) {
	$paths = array(
		'bag'    => '<path d="M5 8h14l-1 12H6L5 8Z"/><path d="M9 8V6a3 3 0 0 1 6 0v2"/>',
		'pin'    => '<path d="M12 21s-7-6.2-7-11a7 7 0 0 1 14 0c0 4.8-7 11-7 11Z"/><circle cx="12" cy="10" r="2.5"/>',
		'clock'  => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
		'needle' => '<path d="M4 20 18 6"/><path d="M16 4a2.8 2.8 0 1 1 4 4"/><path d="M7 14c-3 0-4 2-4 4"/>',
		'scale'  => '<path d="M3 7h18"/><path d="M6 7v4M10 7v2M14 7v4M18 7v2"/><rect x="2" y="5" width="20" height="8" rx="1"/>',
		'menu'   => '<path d="M4 7h16M4 12h16M4 17h16"/>',
		'close'  => '<path d="M6 6l12 12M18 6 6 18"/>',
		'arrow'  => '<path d="M5 12h14M13 6l6 6-6 6"/>',
		'rider'  => '<circle cx="6" cy="17" r="3"/><circle cx="18" cy="17" r="3"/><path d="M6 17h6l3-7h3"/><path d="M12 10h3"/>',
		'check'  => '<path d="m5 12 5 5 9-10"/>',
		'phone'  => '<path d="M5 4h4l2 5-2.5 1.5a11 11 0 0 0 5 5L15 13l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 6a2 2 0 0 1 2-2"/>',
		'locate' => '<circle cx="12" cy="12" r="3"/><path d="M12 2v3M12 19v3M2 12h3M19 12h3"/><circle cx="12" cy="12" r="7"/>',
	);
	if ( ! isset( $paths[ $name ] ) ) {
		return '';
	}
	return '<svg class="hf-icon" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $paths[ $name ] . '</svg>';
}
