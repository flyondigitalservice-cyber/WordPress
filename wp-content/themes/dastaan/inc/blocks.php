<?php
/**
 * Shortcodes + block patterns so every home section can be reused on any page.
 *
 * [dastaan_hero] [dastaan_circles] [dastaan_usp] [dastaan_products source="recent|featured|bestselling|sale|category" category="" count="8" title=""]
 * [dastaan_banners] [dastaan_crafts] [dastaan_reels] [dastaan_instagram] [dastaan_testimonials] [dastaan_bts] [dastaan_studio] [dastaan_faq]
 * [dastaan_whatsapp text=""] [dastaan_newsletter]
 *
 * @package Dastaan
 */

defined( 'ABSPATH' ) || exit;

/**
 * Render a template part into a string.
 *
 * @param string $slug Part.
 * @param array  $args Args.
 * @return string
 */
function dastaan_part( $slug, $args = array() ) {
	ob_start();
	get_template_part( 'template-parts/' . $slug, null, $args );

	return ob_get_clean();
}

$dastaan_part_shortcodes = array(
	'dastaan_hero'         => 'home/hero',
	'dastaan_circles'      => 'home/circles',
	'dastaan_usp'          => 'home/usp',
	'dastaan_banners'      => 'home/banners',
	'dastaan_crafts'       => 'home/crafts',
	'dastaan_reels'        => 'home/reels',
	'dastaan_instagram'    => 'home/instagram',
	'dastaan_testimonials' => 'home/testimonials',
	'dastaan_bts'          => 'home/bts',
	'dastaan_studio'       => 'home/studio',
	'dastaan_faq'          => 'home/seo-content',
	'dastaan_newsletter'   => 'footer/newsletter',
);
foreach ( $dastaan_part_shortcodes as $tag => $part ) {
	add_shortcode(
		$tag,
		function ( $atts ) use ( $part ) {
			$atts = shortcode_atts( array( 'force' => '1' ), $atts );
			return dastaan_part( $part, array( 'force' => (bool) $atts['force'] ) );
		}
	);
}
unset( $dastaan_part_shortcodes, $tag, $part );

add_shortcode(
	'dastaan_products',
	function ( $atts ) {
		$atts = shortcode_atts(
			array(
				'source'   => 'recent',
				'category' => '',
				'count'    => 8,
				'title'    => '',
				'eyebrow'  => '',
				'link'     => '',
				'link_text'=> '',
			),
			$atts
		);

		return dastaan_part(
			'home/products',
			array(
				'force'     => true,
				'source'    => sanitize_key( $atts['source'] ),
				'category'  => dastaan_sanitize_slug_list( $atts['category'] ),
				'count'     => absint( $atts['count'] ),
				'title'     => sanitize_text_field( $atts['title'] ),
				'eyebrow'   => sanitize_text_field( $atts['eyebrow'] ),
				'link'      => $atts['link'],
				'link_text' => sanitize_text_field( $atts['link_text'] ),
			)
		);
	}
);

add_shortcode(
	'dastaan_whatsapp',
	function ( $atts ) {
		$atts = shortcode_atts( array( 'text' => __( 'Chat on WhatsApp', 'dastaan' ), 'message' => '' ), $atts );
		$url  = dastaan_whatsapp_url( $atts['message'] );
		if ( ! $url ) {
			return '';
		}

		return '<a class="btn btn--whatsapp" href="' . esc_url( $url ) . '" target="_blank" rel="noopener">' . dastaan_icon( 'whatsapp', array( 'size' => 18 ) ) . '<span>' . esc_html( $atts['text'] ) . '</span></a>';
	}
);

/**
 * Block patterns (Appearance → Editor / page editor → Patterns → Dastaan).
 */
function dastaan_register_patterns() {
	if ( ! function_exists( 'register_block_pattern' ) ) {
		return;
	}
	$patterns = array(
		'reels'        => array( __( 'Reels block', 'dastaan' ), '[dastaan_reels]' ),
		'instagram'    => array( __( 'Instagram block', 'dastaan' ), '[dastaan_instagram]' ),
		'usp'          => array( __( 'USP trust bar', 'dastaan' ), '[dastaan_usp]' ),
		'banners'      => array( __( 'Offer banners', 'dastaan' ), '[dastaan_banners]' ),
		'testimonials' => array( __( 'Testimonials', 'dastaan' ), '[dastaan_testimonials]' ),
		'faq'          => array( __( 'SEO text + FAQ', 'dastaan' ), '[dastaan_faq]' ),
		'products'     => array( __( 'Product carousel', 'dastaan' ), '[dastaan_products source="recent" count="8" title="New in the studio"]' ),
		'whatsapp'     => array( __( 'WhatsApp button', 'dastaan' ), '[dastaan_whatsapp text="Chat with a stylist"]' ),
	);
	foreach ( $patterns as $slug => $p ) {
		register_block_pattern(
			'dastaan/' . $slug,
			array(
				'title'      => $p[0],
				'categories' => array( 'dastaan' ),
				'content'    => '<!-- wp:shortcode -->' . $p[1] . '<!-- /wp:shortcode -->',
			)
		);
	}
}
add_action( 'init', 'dastaan_register_patterns' );
