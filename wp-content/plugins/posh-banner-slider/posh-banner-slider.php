<?php
/**
 * Plugin Name: Posh Banner Slider
 * Description: Auto-sliding promotional hero banners for a premium laptop store. Ships with bold 3D-text placeholder banners; swap in your own generated images from Appearance → Banner Slider. Use the shortcode [posh_banner_slider] or the PHP call posh_banner_slider().
 * Version:     1.0.0
 * Author:      Flyon Digital
 * License:     GPL-2.0-or-later
 * Text Domain: posh-banner-slider
 */

defined( 'ABSPATH' ) || exit;

define( 'POSH_BS_VERSION', '1.0.0' );
define( 'POSH_BS_URL', plugin_dir_url( __FILE__ ) );
define( 'POSH_BS_OPTION', 'posh_banner_slider' );

/**
 * Default placeholder slides. Each one renders as a CSS "text effect" banner
 * until an image is set for it.
 */
function posh_bs_default_slides() {
	return array(
		array(
			'theme'    => 'fire',
			'badge'    => 'Limited Time',
			'title'    => 'Special Offer',
			'subtitle' => 'Up to 30% off premium ultrabooks',
			'button'   => 'Shop the Deal',
			'link'     => '/shop/',
			'image'    => '',
		),
		array(
			'theme'    => 'gold',
			'badge'    => 'New Arrivals',
			'title'    => 'Luxury Laptops',
			'subtitle' => 'Crafted in aluminium. Built for the bold.',
			'button'   => 'Explore Collection',
			'link'     => '/shop/',
			'image'    => '',
		),
		array(
			'theme'    => 'neon',
			'badge'    => 'Gaming Week',
			'title'    => 'Power Up',
			'subtitle' => 'RTX gaming laptops with free accessories',
			'button'   => 'Gear Up Now',
			'link'     => '/shop/',
			'image'    => '',
		),
		array(
			'theme'    => 'royal',
			'badge'    => 'Members Only',
			'title'    => 'Mega Sale',
			'subtitle' => 'Flat discounts + no-cost EMI on top brands',
			'button'   => 'Grab Yours',
			'link'     => '/shop/',
			'image'    => '',
		),
		array(
			'theme'    => 'mint',
			'badge'    => 'Trade-In',
			'title'    => 'Upgrade Day',
			'subtitle' => 'Exchange your old laptop for extra value',
			'button'   => 'Check Value',
			'link'     => '/shop/',
			'image'    => '',
		),
	);
}

function posh_bs_themes() {
	return array(
		'fire'  => 'Fire Red / Gold',
		'gold'  => 'Black / Gold Luxury',
		'neon'  => 'Neon Gaming',
		'royal' => 'Royal Purple',
		'mint'  => 'Mint / Teal',
	);
}

function posh_bs_get_settings() {
	$saved = get_option( POSH_BS_OPTION, array() );
	$defaults = array(
		'interval' => 5,
		'height'   => 'auto',
		'slides'   => posh_bs_default_slides(),
	);
	$settings = wp_parse_args( is_array( $saved ) ? $saved : array(), $defaults );
	if ( empty( $settings['slides'] ) || ! is_array( $settings['slides'] ) ) {
		$settings['slides'] = posh_bs_default_slides();
	}
	return $settings;
}

/* -------------------------------------------------------------------------
 * Front end
 * ---------------------------------------------------------------------- */

add_action( 'wp_enqueue_scripts', function () {
	wp_register_style( 'posh-banner-slider', POSH_BS_URL . 'assets/slider.css', array(), POSH_BS_VERSION );
	wp_register_script( 'posh-banner-slider', POSH_BS_URL . 'assets/slider.js', array(), POSH_BS_VERSION, true );
	wp_register_style( 'posh-banner-slider-font', 'https://fonts.googleapis.com/css2?family=Anton&family=Inter:wght@500;700&display=swap', array(), null );
} );

function posh_banner_slider( $atts = array() ) {
	$settings = posh_bs_get_settings();
	$atts     = shortcode_atts(
		array(
			'interval' => $settings['interval'],
		),
		$atts,
		'posh_banner_slider'
	);

	wp_enqueue_style( 'posh-banner-slider-font' );
	wp_enqueue_style( 'posh-banner-slider' );
	wp_enqueue_script( 'posh-banner-slider' );

	$slides   = array_values( array_filter( $settings['slides'], function ( $s ) {
		return ! empty( $s['title'] ) || ! empty( $s['image'] );
	} ) );
	$interval = max( 2, (int) $atts['interval'] ) * 1000;
	$themes   = posh_bs_themes();

	ob_start();
	?>
	<section class="posh-bs" data-interval="<?php echo esc_attr( $interval ); ?>" aria-roledescription="carousel" aria-label="<?php esc_attr_e( 'Featured offers', 'posh-banner-slider' ); ?>">
		<div class="posh-bs__track">
			<?php foreach ( $slides as $i => $s ) :
				$theme   = isset( $themes[ $s['theme'] ?? '' ] ) ? $s['theme'] : 'fire';
				$has_img = ! empty( $s['image'] );
				?>
				<div class="posh-bs__slide posh-bs--<?php echo esc_attr( $theme ); ?><?php echo $has_img ? ' has-image' : ''; ?><?php echo 0 === $i ? ' is-active' : ''; ?>"
					role="group" aria-roledescription="slide"
					aria-label="<?php echo esc_attr( sprintf( '%d / %d', $i + 1, count( $slides ) ) ); ?>"
					<?php echo 0 === $i ? '' : 'aria-hidden="true"'; ?>>
					<?php if ( $has_img ) : ?>
						<img class="posh-bs__img" src="<?php echo esc_url( $s['image'] ); ?>" alt="<?php echo esc_attr( $s['title'] ); ?>" <?php echo 0 === $i ? 'fetchpriority="high"' : 'loading="lazy"'; ?>>
					<?php else : ?>
						<div class="posh-bs__bg" aria-hidden="true"><span class="posh-bs__rays"></span><span class="posh-bs__dots"></span><span class="posh-bs__glow"></span></div>
						<div class="posh-bs__content">
							<?php if ( ! empty( $s['badge'] ) ) : ?>
								<span class="posh-bs__badge"><?php echo esc_html( $s['badge'] ); ?></span>
							<?php endif; ?>
							<h2 class="posh-bs__title" data-text="<?php echo esc_attr( $s['title'] ); ?>"><?php echo esc_html( $s['title'] ); ?></h2>
							<?php if ( ! empty( $s['subtitle'] ) ) : ?>
								<p class="posh-bs__sub"><?php echo esc_html( $s['subtitle'] ); ?></p>
							<?php endif; ?>
							<?php if ( ! empty( $s['button'] ) && ! empty( $s['link'] ) ) : ?>
								<a class="posh-bs__btn" href="<?php echo esc_url( $s['link'] ); ?>"><?php echo esc_html( $s['button'] ); ?></a>
							<?php endif; ?>
						</div>
						<span class="posh-bs__sparkle s1" aria-hidden="true"></span>
						<span class="posh-bs__sparkle s2" aria-hidden="true"></span>
						<span class="posh-bs__sparkle s3" aria-hidden="true"></span>
					<?php endif; ?>
					<?php if ( $has_img && ! empty( $s['link'] ) ) : ?>
						<a class="posh-bs__cover-link" href="<?php echo esc_url( $s['link'] ); ?>" aria-label="<?php echo esc_attr( $s['button'] ?: $s['title'] ); ?>"></a>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
		<?php if ( count( $slides ) > 1 ) : ?>
			<button class="posh-bs__nav posh-bs__prev" type="button" aria-label="<?php esc_attr_e( 'Previous slide', 'posh-banner-slider' ); ?>">&#10094;</button>
			<button class="posh-bs__nav posh-bs__next" type="button" aria-label="<?php esc_attr_e( 'Next slide', 'posh-banner-slider' ); ?>">&#10095;</button>
			<div class="posh-bs__dots-nav" role="tablist">
				<?php foreach ( $slides as $i => $s ) : ?>
					<button type="button" class="posh-bs__dot<?php echo 0 === $i ? ' is-active' : ''; ?>" aria-label="<?php echo esc_attr( sprintf( __( 'Go to slide %d', 'posh-banner-slider' ), $i + 1 ) ); ?>"><span></span></button>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</section>
	<?php
	return ob_get_clean();
}
add_shortcode( 'posh_banner_slider', 'posh_banner_slider' );

/* -------------------------------------------------------------------------
 * Admin: Appearance → Banner Slider
 * ---------------------------------------------------------------------- */

add_action( 'admin_menu', function () {
	add_theme_page(
		__( 'Banner Slider', 'posh-banner-slider' ),
		__( 'Banner Slider', 'posh-banner-slider' ),
		'manage_options',
		'posh-banner-slider',
		'posh_bs_render_admin'
	);
} );

add_action( 'admin_enqueue_scripts', function ( $hook ) {
	if ( 'appearance_page_posh-banner-slider' !== $hook ) {
		return;
	}
	wp_enqueue_media();
	wp_enqueue_script( 'posh-banner-slider-admin', POSH_BS_URL . 'assets/admin.js', array( 'jquery' ), POSH_BS_VERSION, true );
} );

add_action( 'admin_init', function () {
	register_setting( 'posh_bs_group', POSH_BS_OPTION, array( 'sanitize_callback' => 'posh_bs_sanitize' ) );
} );

function posh_bs_sanitize( $input ) {
	$themes = posh_bs_themes();
	$out    = array(
		'interval' => isset( $input['interval'] ) ? min( 30, max( 2, (int) $input['interval'] ) ) : 5,
		'slides'   => array(),
	);
	if ( ! empty( $input['slides'] ) && is_array( $input['slides'] ) ) {
		foreach ( $input['slides'] as $s ) {
			$out['slides'][] = array(
				'theme'    => isset( $themes[ $s['theme'] ?? '' ] ) ? $s['theme'] : 'fire',
				'badge'    => sanitize_text_field( $s['badge'] ?? '' ),
				'title'    => sanitize_text_field( $s['title'] ?? '' ),
				'subtitle' => sanitize_text_field( $s['subtitle'] ?? '' ),
				'button'   => sanitize_text_field( $s['button'] ?? '' ),
				'link'     => esc_url_raw( $s['link'] ?? '' ),
				'image'    => esc_url_raw( $s['image'] ?? '' ),
			);
		}
	}
	return $out;
}

function posh_bs_render_admin() {
	$settings = posh_bs_get_settings();
	$themes   = posh_bs_themes();
	$name     = POSH_BS_OPTION;
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Banner Slider', 'posh-banner-slider' ); ?></h1>
		<p><?php echo wp_kses_post( __( 'Place the slider anywhere with the shortcode <code>[posh_banner_slider]</code>. Leave <em>Banner image</em> empty to show the built-in 3D text placeholder; pick an image (recommended 1920×700) to replace it.', 'posh-banner-slider' ) ); ?></p>
		<form method="post" action="options.php">
			<?php settings_fields( 'posh_bs_group' ); ?>
			<table class="form-table">
				<tr>
					<th scope="row"><label for="posh-bs-interval"><?php esc_html_e( 'Auto-slide every (seconds)', 'posh-banner-slider' ); ?></label></th>
					<td><input id="posh-bs-interval" type="number" min="2" max="30" name="<?php echo esc_attr( $name ); ?>[interval]" value="<?php echo esc_attr( $settings['interval'] ); ?>"></td>
				</tr>
			</table>
			<?php foreach ( $settings['slides'] as $i => $s ) :
				$base = $name . '[slides][' . $i . ']';
				?>
				<h2><?php echo esc_html( sprintf( __( 'Slide %d', 'posh-banner-slider' ), $i + 1 ) ); ?></h2>
				<table class="form-table posh-bs-slide">
					<tr><th><?php esc_html_e( 'Banner image', 'posh-banner-slider' ); ?></th><td>
						<input type="url" class="regular-text posh-bs-image" name="<?php echo esc_attr( $base ); ?>[image]" value="<?php echo esc_attr( $s['image'] ); ?>">
						<button type="button" class="button posh-bs-pick"><?php esc_html_e( 'Choose image', 'posh-banner-slider' ); ?></button>
						<button type="button" class="button-link posh-bs-clear"><?php esc_html_e( 'Use placeholder', 'posh-banner-slider' ); ?></button>
					</td></tr>
					<tr><th><?php esc_html_e( 'Placeholder style', 'posh-banner-slider' ); ?></th><td>
						<select name="<?php echo esc_attr( $base ); ?>[theme]">
							<?php foreach ( $themes as $key => $label ) : ?>
								<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $s['theme'], $key ); ?>><?php echo esc_html( $label ); ?></option>
							<?php endforeach; ?>
						</select>
					</td></tr>
					<?php foreach ( array( 'badge' => 'Badge', 'title' => 'Headline', 'subtitle' => 'Subtitle', 'button' => 'Button text', 'link' => 'Link URL' ) as $key => $label ) : ?>
						<tr><th><?php echo esc_html( $label ); ?></th><td>
							<input type="text" class="regular-text" name="<?php echo esc_attr( $base . '[' . $key . ']' ); ?>" value="<?php echo esc_attr( $s[ $key ] ); ?>">
						</td></tr>
					<?php endforeach; ?>
				</table>
			<?php endforeach; ?>
			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}
