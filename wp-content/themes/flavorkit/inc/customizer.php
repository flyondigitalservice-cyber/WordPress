<?php
/**
 * Customizer: every brand setting a client needs, in one panel.
 *
 * @package FlavorKit
 */

defined( 'ABSPATH' ) || exit;

/**
 * Field definitions grouped by section.
 *
 * Types: text, textarea, url, color, select, number, image, checkbox.
 *
 * @return array<string, array<string, mixed>>
 */
function flavorkit_customizer_fields() {
	$lines_help = __( 'One item per line. Separate parts with a vertical bar |', 'flavorkit' );
	$hl_help    = __( 'Wrap a word in *asterisks* to highlight it.', 'flavorkit' );
	$icons      = 'leaf, heart, truck, shield, sparkle, flame, drop, gift, check, star';

	$preset_choices = array();
	foreach ( flavorkit_presets() as $key => $preset ) {
		$preset_choices[ $key ] = $preset['label'];
	}
	$font_choices = array_combine( array_keys( flavorkit_fonts() ), array_keys( flavorkit_fonts() ) );

	return array(
		'brand'      => array(
			'title'  => __( 'Brand colours & fonts', 'flavorkit' ),
			'fields' => array(
				'preset'          => array( 'select', __( 'Colour preset', 'flavorkit' ), $preset_choices, __( 'Pick a ready-made palette, or choose "Custom" to use the colour pickers.', 'flavorkit' ) ),
				'color_primary'   => array( 'color', __( 'Primary colour (Custom)', 'flavorkit' ) ),
				'color_secondary' => array( 'color', __( 'Secondary colour (Custom)', 'flavorkit' ) ),
				'color_accent'    => array( 'color', __( 'Accent colour (Custom)', 'flavorkit' ) ),
				'color_dark'      => array( 'color', __( 'Text / dark colour (Custom)', 'flavorkit' ) ),
				'color_cream'     => array( 'color', __( 'Background colour (Custom)', 'flavorkit' ) ),
				'font_heading'    => array( 'select', __( 'Heading font', 'flavorkit' ), $font_choices ),
				'font_body'       => array( 'select', __( 'Body font', 'flavorkit' ), $font_choices ),
				'radius'          => array( 'number', __( 'Corner roundness (px)', 'flavorkit' ) ),
				'button_style'    => array(
					'select',
					__( 'Button style', 'flavorkit' ),
					array(
						'sticker' => __( 'Sticker (outline + hard shadow)', 'flavorkit' ),
						'pill'    => __( 'Soft pill', 'flavorkit' ),
						'square'  => __( 'Clean square', 'flavorkit' ),
					),
				),
			),
		),
		'layout'     => array(
			'title'  => __( 'Homepage sections', 'flavorkit' ),
			'fields' => array(
				'sections_order' => array(
					'text',
					__( 'Sections to show (in order)', 'flavorkit' ),
					null,
					sprintf(
						/* translators: %s list of keys */
						__( 'Comma separated. Remove a key to hide it, move keys to reorder. Available: %s', 'flavorkit' ),
						implode( ', ', array_keys( flavorkit_sections() ) )
					),
				),
			),
		),
		'header'     => array(
			'title'  => __( 'Header & announcement bar', 'flavorkit' ),
			'fields' => array(
				'announcement'    => array( 'textarea', __( 'Announcement messages', 'flavorkit' ), null, __( 'Separate messages with |. Leave empty to hide the bar.', 'flavorkit' ) ),
				'header_cta_text' => array( 'text', __( 'Header button text (optional)', 'flavorkit' ) ),
				'header_cta_url'  => array( 'url', __( 'Header button link', 'flavorkit' ) ),
			),
		),
		'hero'       => array(
			'title'  => __( 'Hero banner', 'flavorkit' ),
			'fields' => array(
				'hero_eyebrow'   => array( 'text', __( 'Small label', 'flavorkit' ) ),
				'hero_title'     => array( 'textarea', __( 'Headline', 'flavorkit' ), null, $hl_help ),
				'hero_text'      => array( 'textarea', __( 'Sub-text', 'flavorkit' ) ),
				'hero_btn1_text' => array( 'text', __( 'Main button text', 'flavorkit' ) ),
				'hero_btn1_url'  => array( 'text', __( 'Main button link (empty = shop)', 'flavorkit' ) ),
				'hero_btn2_text' => array( 'text', __( 'Second button text', 'flavorkit' ) ),
				'hero_btn2_url'  => array( 'text', __( 'Second button link', 'flavorkit' ) ),
				'hero_image'     => array( 'image', __( 'Hero product image (transparent PNG works best)', 'flavorkit' ), null, __( 'Leave empty to show illustrated packs in brand colours.', 'flavorkit' ) ),
				'hero_badges'    => array( 'text', __( 'Trust badges (separate with |)', 'flavorkit' ) ),
				'hero_rating'    => array( 'text', __( 'Rating line', 'flavorkit' ) ),
			),
		),
		'usp'        => array(
			'title'  => __( 'Marquee & USP badges', 'flavorkit' ),
			'fields' => array(
				'marquee_items' => array( 'textarea', __( 'Marquee words (separate with |)', 'flavorkit' ) ),
				'usp_items'     => array( 'textarea', __( 'USP badges', 'flavorkit' ), null, $lines_help . ' — ' . __( 'Title | Text | Icon. Icons:', 'flavorkit' ) . ' ' . $icons ),
			),
		),
		'categories' => array(
			'title'  => __( 'Shop by category', 'flavorkit' ),
			'fields' => array(
				'cat_title' => array( 'text', __( 'Title', 'flavorkit' ), null, $hl_help ),
				'cat_items' => array( 'textarea', __( 'Demo categories', 'flavorkit' ), null, __( 'Used only until WooCommerce product categories exist. Name | shape (can, pouch, jar, bottle, box)', 'flavorkit' ) ),
			),
		),
		'products'   => array(
			'title'  => __( 'Bestseller products', 'flavorkit' ),
			'fields' => array(
				'products_eyebrow'  => array( 'text', __( 'Small label', 'flavorkit' ) ),
				'products_title'    => array( 'text', __( 'Title', 'flavorkit' ), null, $hl_help ),
				'products_source'   => array(
					'select',
					__( 'Which products', 'flavorkit' ),
					array(
						'best_selling' => __( 'Best selling', 'flavorkit' ),
						'featured'     => __( 'Featured (starred)', 'flavorkit' ),
						'recent'       => __( 'Newest', 'flavorkit' ),
						'on_sale'      => __( 'On sale', 'flavorkit' ),
						'top_rated'    => __( 'Top rated', 'flavorkit' ),
					),
				),
				'products_count'    => array( 'number', __( 'Number of products', 'flavorkit' ) ),
				'products_btn_text' => array( 'text', __( 'Button text', 'flavorkit' ) ),
			),
		),
		'story'      => array(
			'title'  => __( 'Brand story', 'flavorkit' ),
			'fields' => array(
				'story_eyebrow'  => array( 'text', __( 'Small label', 'flavorkit' ) ),
				'story_title'    => array( 'textarea', __( 'Title', 'flavorkit' ), null, $hl_help ),
				'story_text'     => array( 'textarea', __( 'Text', 'flavorkit' ) ),
				'story_btn_text' => array( 'text', __( 'Button text', 'flavorkit' ) ),
				'story_btn_url'  => array( 'text', __( 'Button link', 'flavorkit' ) ),
				'story_image'    => array( 'image', __( 'Image', 'flavorkit' ) ),
				'story_stats'    => array( 'textarea', __( 'Stats', 'flavorkit' ), null, $lines_help . ' — ' . __( 'Number | Label', 'flavorkit' ) ),
			),
		),
		'benefits'   => array(
			'title'  => __( 'Why choose us', 'flavorkit' ),
			'fields' => array(
				'benefits_eyebrow' => array( 'text', __( 'Small label', 'flavorkit' ) ),
				'benefits_title'   => array( 'text', __( 'Title', 'flavorkit' ), null, $hl_help ),
				'benefits_items'   => array( 'textarea', __( 'Benefits', 'flavorkit' ), null, $lines_help . ' — ' . __( 'Title | Text | Icon', 'flavorkit' ) ),
			),
		),
		'reviews'    => array(
			'title'  => __( 'Customer reviews', 'flavorkit' ),
			'fields' => array(
				'reviews_eyebrow' => array( 'text', __( 'Small label', 'flavorkit' ) ),
				'reviews_title'   => array( 'text', __( 'Title', 'flavorkit' ), null, $hl_help ),
				'reviews_items'   => array( 'textarea', __( 'Reviews', 'flavorkit' ), null, $lines_help . ' — ' . __( 'Name | City | Review | Stars (1-5)', 'flavorkit' ) ),
			),
		),
		'ugc'        => array(
			'title'  => __( 'Instagram / UGC grid', 'flavorkit' ),
			'fields' => array(
				'ugc_title'  => array( 'text', __( 'Title', 'flavorkit' ), null, $hl_help ),
				'ugc_text'   => array( 'text', __( 'Text', 'flavorkit' ) ),
				'ugc_url'    => array( 'url', __( 'Instagram profile link', 'flavorkit' ) ),
				'ugc_images' => array( 'text', __( 'Image IDs (comma separated)', 'flavorkit' ), null, __( 'Media Library attachment IDs, e.g. 12,15,18,21,24,27. Leave empty for illustrated tiles.', 'flavorkit' ) ),
			),
		),
		'faq'        => array(
			'title'  => __( 'FAQ', 'flavorkit' ),
			'fields' => array(
				'faq_title' => array( 'text', __( 'Title', 'flavorkit' ), null, $hl_help ),
				'faq_items' => array( 'textarea', __( 'Questions', 'flavorkit' ), null, $lines_help . ' — ' . __( 'Question | Answer', 'flavorkit' ) ),
			),
		),
		'newsletter' => array(
			'title'  => __( 'Newsletter / offer', 'flavorkit' ),
			'fields' => array(
				'newsletter_title'     => array( 'text', __( 'Title', 'flavorkit' ), null, $hl_help ),
				'newsletter_text'      => array( 'textarea', __( 'Text', 'flavorkit' ) ),
				'newsletter_shortcode' => array( 'text', __( 'Form shortcode (optional)', 'flavorkit' ), null, __( 'Paste a Mailchimp / Klaviyo / Contact Form 7 shortcode. Empty = built-in form (subscribers appear under Tools → Subscribers).', 'flavorkit' ) ),
			),
		),
		'footer'     => array(
			'title'  => __( 'Footer, social & WhatsApp', 'flavorkit' ),
			'fields' => array(
				'footer_about'     => array( 'textarea', __( 'About text', 'flavorkit' ) ),
				'footer_copyright' => array( 'text', __( 'Copyright text (empty = automatic)', 'flavorkit' ) ),
				'social_instagram' => array( 'url', 'Instagram' ),
				'social_facebook'  => array( 'url', 'Facebook' ),
				'social_youtube'   => array( 'url', 'YouTube' ),
				'social_x'         => array( 'url', 'X / Twitter' ),
				'social_linkedin'  => array( 'url', 'LinkedIn' ),
				'whatsapp_number'  => array( 'text', __( 'WhatsApp number with country code', 'flavorkit' ), null, __( 'e.g. 919876543210. Adds a floating chat button.', 'flavorkit' ) ),
				'whatsapp_message' => array( 'text', __( 'WhatsApp pre-filled message', 'flavorkit' ) ),
			),
		),
	);
}

/**
 * Register the panel, sections, settings and controls.
 *
 * @param WP_Customize_Manager $wp_customize Manager.
 */
function flavorkit_customize_register( $wp_customize ) {
	$defaults = flavorkit_defaults();

	$wp_customize->add_panel(
		'flavorkit',
		array(
			'title'       => __( 'FlavorKit Theme', 'flavorkit' ),
			'description' => __( 'Re-brand the whole store: colours, fonts, homepage sections and copy.', 'flavorkit' ),
			'priority'    => 30,
		)
	);

	foreach ( flavorkit_customizer_fields() as $section_id => $section ) {
		$wp_customize->add_section(
			'flavorkit_' . $section_id,
			array(
				'title' => $section['title'],
				'panel' => 'flavorkit',
			)
		);

		foreach ( $section['fields'] as $key => $field ) {
			list( $type, $label ) = $field;
			$choices              = isset( $field[2] ) ? $field[2] : null;
			$description          = isset( $field[3] ) ? $field[3] : '';
			$setting_id           = 'flavorkit_' . $key;

			$sanitize = 'sanitize_text_field';
			switch ( $type ) {
				case 'textarea':
					$sanitize = 'sanitize_textarea_field';
					break;
				case 'url':
					$sanitize = 'esc_url_raw';
					break;
				case 'color':
					$sanitize = 'sanitize_hex_color';
					break;
				case 'number':
					$sanitize = 'absint';
					break;
				case 'image':
					$sanitize = 'absint';
					break;
				case 'select':
					$sanitize = 'flavorkit_sanitize_choice';
					break;
			}

			$wp_customize->add_setting(
				$setting_id,
				array(
					'default'           => isset( $defaults[ $key ] ) ? $defaults[ $key ] : '',
					'sanitize_callback' => $sanitize,
					'transport'         => 'refresh',
				)
			);

			$args = array(
				'label'       => $label,
				'description' => $description,
				'section'     => 'flavorkit_' . $section_id,
				'settings'    => $setting_id,
			);

			if ( 'color' === $type ) {
				$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, $setting_id, $args ) );
			} elseif ( 'image' === $type ) {
				$args['mime_type'] = 'image';
				$wp_customize->add_control( new WP_Customize_Media_Control( $wp_customize, $setting_id, $args ) );
			} else {
				$args['type'] = $type;
				if ( $choices ) {
					$args['choices'] = $choices;
				}
				if ( 'number' === $type ) {
					$args['input_attrs'] = array( 'min' => 0, 'max' => 60 );
				}
				$wp_customize->add_control( $setting_id, $args );
			}
		}
	}
}
add_action( 'customize_register', 'flavorkit_customize_register' );

/**
 * Sanitize select controls against their registered choices.
 *
 * @param string               $value   Value.
 * @param WP_Customize_Setting $setting Setting.
 * @return string
 */
function flavorkit_sanitize_choice( $value, $setting ) {
	$control = $setting->manager->get_control( $setting->id );
	$choices = $control ? $control->choices : array();
	return array_key_exists( $value, $choices ) ? $value : $setting->default;
}
