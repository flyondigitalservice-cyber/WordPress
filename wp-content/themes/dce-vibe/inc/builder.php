<?php
/**
 * Block markup builders. Everything produced here is standard core blocks,
 * so every section stays editable in the block editor.
 *
 * @package dce-vibe
 */

defined( 'ABSPATH' ) || exit;

/** Serialize block attributes the same way the editor does. */
function dce_attrs( $attrs ) {
	$attrs = array_filter( $attrs, fn( $v ) => null !== $v && '' !== $v && array() !== $v );
	if ( ! $attrs ) {
		return '';
	}
	return ' ' . serialize_block_attributes( $attrs );
}

function dce_cls( $base, $extra = '' ) {
	return trim( $base . ' ' . $extra );
}

/** Escape text for rich-text content while allowing simple inline HTML we write ourselves. */
function dce_t( $html ) {
	return wp_kses(
		$html,
		array(
			'em'     => array(),
			'strong' => array(),
			'br'     => array(),
			'a'      => array( 'href' => array() ),
		)
	);
}

function dce_b_p( $html, $class = '' ) {
	$a = dce_attrs( array( 'className' => $class ) );
	$c = $class ? ' class="' . esc_attr( $class ) . '"' : '';
	return "<!-- wp:paragraph{$a} -->\n<p{$c}>" . dce_t( $html ) . "</p>\n<!-- /wp:paragraph -->\n\n";
}

function dce_b_h( $html, $level = 2, $class = '' ) {
	$a = dce_attrs(
		array(
			'level'     => 2 === $level ? null : $level,
			'className' => $class,
		)
	);
	return "<!-- wp:heading{$a} -->\n<h{$level} class=\"" . esc_attr( dce_cls( 'wp-block-heading', $class ) ) . '">' . dce_t( $html ) . "</h{$level}>\n<!-- /wp:heading -->\n\n";
}

function dce_b_list( $items, $class = '' ) {
	$a   = dce_attrs( array( 'className' => $class ) );
	$out = "<!-- wp:list{$a} -->\n<ul class=\"" . esc_attr( dce_cls( 'wp-block-list', $class ) ) . '">';
	foreach ( $items as $item ) {
		$out .= "<!-- wp:list-item -->\n<li>" . dce_t( $item ) . "</li>\n<!-- /wp:list-item -->";
	}
	return $out . "</ul>\n<!-- /wp:list -->\n\n";
}

/**
 * Group block.
 *
 * @param string $inner Inner block markup.
 * @param array  $args  tag, align, class, anchor, layout ('constrained'|'default').
 */
function dce_b_group( $inner, $args = array() ) {
	$args  = wp_parse_args(
		$args,
		array(
			'tag'    => 'div',
			'align'  => '',
			'class'  => '',
			'anchor' => '',
			'layout' => 'default',
		)
	);
	$attrs = array(
		'tagName'   => 'div' === $args['tag'] ? null : $args['tag'],
		'anchor'    => $args['anchor'],
		'align'     => $args['align'],
		'className' => $args['class'],
		'layout'    => 'constrained' === $args['layout'] ? array( 'type' => 'constrained' ) : null,
	);
	$class = 'wp-block-group';
	if ( $args['align'] ) {
		$class .= ' align' . $args['align'];
	}
	$class = dce_cls( $class, $args['class'] );
	$id    = $args['anchor'] ? ' id="' . esc_attr( $args['anchor'] ) . '"' : '';
	$tag   = $args['tag'];
	return '<!-- wp:group' . dce_attrs( $attrs ) . " -->\n<{$tag}{$id} class=\"" . esc_attr( $class ) . "\">{$inner}</{$tag}>\n<!-- /wp:group -->\n\n";
}

/** Full-width section with constrained inner content. */
function dce_b_section( $inner, $class = 'dce-sec', $anchor = '' ) {
	return dce_b_group(
		$inner,
		array(
			'tag'    => 'section',
			'align'  => 'full',
			'class'  => $class,
			'anchor' => $anchor,
			'layout' => 'constrained',
		)
	);
}

/** Wide inner wrapper (keeps grids at wide width inside constrained sections). */
function dce_b_wide( $inner, $class = '' ) {
	return dce_b_group(
		$inner,
		array(
			'align' => 'wide',
			'class' => $class,
		)
	);
}

function dce_b_columns( $columns, $class = '' ) {
	$a   = dce_attrs(
		array(
			'align'     => 'wide',
			'className' => $class,
		)
	);
	$out = "<!-- wp:columns{$a} -->\n<div class=\"" . esc_attr( dce_cls( 'wp-block-columns alignwide', $class ) ) . '">';
	foreach ( $columns as $col ) {
		$out .= "<!-- wp:column -->\n<div class=\"wp-block-column\">{$col}</div>\n<!-- /wp:column -->";
	}
	return $out . "</div>\n<!-- /wp:columns -->\n\n";
}

/**
 * Image block.
 *
 * @param array  $img  id, url, alt.
 * @param string $link Optional link URL.
 * @param string $caption Optional caption.
 */
function dce_b_image( $img, $link = '', $caption = '', $class = '' ) {
	if ( empty( $img['url'] ) ) {
		return '';
	}
	$attrs = array(
		'id'              => ! empty( $img['id'] ) ? (int) $img['id'] : null,
		'sizeSlug'        => 'large',
		'linkDestination' => $link ? 'custom' : 'none',
		'className'       => $class,
	);
	$tag   = '<img src="' . esc_url( $img['url'] ) . '" alt="' . esc_attr( $img['alt'] ) . '"' . ( ! empty( $img['id'] ) ? ' class="wp-image-' . (int) $img['id'] . '"' : '' ) . '/>';
	if ( $link ) {
		$tag = '<a href="' . esc_url( $link ) . '">' . $tag . '</a>';
	}
	if ( $caption ) {
		$tag .= '<figcaption class="wp-element-caption">' . dce_t( $caption ) . '</figcaption>';
	}
	return '<!-- wp:image' . dce_attrs( $attrs ) . " -->\n<figure class=\"" . esc_attr( dce_cls( 'wp-block-image size-large', $class ) ) . "\">{$tag}</figure>\n<!-- /wp:image -->\n\n";
}

/**
 * Buttons.
 *
 * @param array $buttons Each: [text, url, class].
 */
function dce_b_buttons( $buttons, $class = '' ) {
	$a   = dce_attrs( array( 'className' => $class ) );
	$out = "<!-- wp:buttons{$a} -->\n<div class=\"" . esc_attr( dce_cls( 'wp-block-buttons', $class ) ) . '">';
	foreach ( $buttons as $b ) {
		$bc    = isset( $b[2] ) ? $b[2] : '';
		$blank = ! empty( $b[3] );
		$attrs = array(
			'className'  => $bc,
			'linkTarget' => $blank ? '_blank' : null,
			'rel'        => $blank ? 'noreferrer noopener' : null,
		);
		$extra = $blank ? ' target="_blank" rel="noreferrer noopener"' : '';
		$out  .= '<!-- wp:button' . dce_attrs( $attrs ) . " -->\n<div class=\"" . esc_attr( dce_cls( 'wp-block-button', $bc ) ) . '"><a class="wp-block-button__link wp-element-button" href="' . esc_url( $b[1] ) . '"' . $extra . '>' . dce_t( $b[0] ) . "</a></div>\n<!-- /wp:button -->";
	}
	return $out . "</div>\n<!-- /wp:buttons -->\n\n";
}

function dce_b_details( $q, $a ) {
	return "<!-- wp:details -->\n<details class=\"wp-block-details\"><summary>" . dce_t( $q ) . "</summary><!-- wp:paragraph -->\n<p>" . dce_t( $a ) . "</p>\n<!-- /wp:paragraph --></details>\n<!-- /wp:details -->\n\n";
}

function dce_b_shortcode( $code ) {
	return "<!-- wp:shortcode -->\n{$code}\n<!-- /wp:shortcode -->\n\n";
}

function dce_b_html( $html ) {
	return "<!-- wp:html -->\n{$html}\n<!-- /wp:html -->\n\n";
}

function dce_b_latest_posts( $count = 9 ) {
	return '<!-- wp:latest-posts ' . serialize_block_attributes(
		array(
			'postsToShow'          => $count,
			'displayPostContent'   => true,
			'excerptLength'        => 24,
			'displayFeaturedImage' => true,
			'featuredImageSizeSlug' => 'medium_large',
			'postLayout'           => 'grid',
			'columns'              => 3,
			'align'                => 'wide',
		)
	) . " /-->\n\n";
}

/* ------------------------------------------------------------------------
 * Media lookup (filled by the importer).
 * --------------------------------------------------------------------- */

/**
 * Get an imported image.
 *
 * @param string $key Image group key, e.g. "wave-curtains".
 * @param int    $n   1-based index.
 * @return array|null
 */
function dce_img( $key, $n = 1 ) {
	$media = isset( $GLOBALS['dce_media'] ) ? $GLOBALS['dce_media'] : array();
	if ( empty( $media[ $key ] ) ) {
		return null;
	}
	$list = array_values( $media[ $key ] );
	return $list[ ( $n - 1 ) % count( $list ) ];
}

function dce_imgs( $key ) {
	$media = isset( $GLOBALS['dce_media'] ) ? $GLOBALS['dce_media'] : array();
	return empty( $media[ $key ] ) ? array() : array_values( $media[ $key ] );
}

/* ------------------------------------------------------------------------
 * Sections.
 * --------------------------------------------------------------------- */

/**
 * Hero.
 *
 * @param array $h kicker, title, lead, img, chips, buttons.
 */
function dce_sec_hero( $h ) {
	$buttons = isset( $h['buttons'] ) ? $h['buttons'] : array(
		array( 'WhatsApp for a free visit', '#whatsapp', 'dce-wa' ),
		array( 'Get a free quote', '#quote', 'is-style-outline' ),
	);
	$text    = dce_b_p( $h['kicker'], 'dce-kicker' )
		. dce_b_h( $h['title'], 1 )
		. dce_b_p( $h['lead'], 'dce-lead' )
		. dce_b_buttons( $buttons )
		. ( ! empty( $h['chips'] ) ? dce_b_list( $h['chips'], 'dce-chips' ) : '' );
	if ( empty( $h['img'] ) ) {
		return dce_b_section( dce_b_group( $text, array( 'class' => 'dce-hero-text' ) ), 'dce-hero dce-hero-solo' );
	}
	return dce_b_section( dce_b_columns( array( $text, dce_b_image( $h['img'] ) ) ), 'dce-hero' . ( ! empty( $h['wide'] ) ? ' dce-hero-wide' : '' ) );
}

function dce_sec_trust( $items = null ) {
	if ( null === $items && 'dbh' === dce_profile() ) {
		$items = array(
			'Free site visit &amp; measurement',
			'Blinds, curtains, flooring, upholstery &amp; wallpaper',
			'Installed by our own team',
			'Dubai &amp; all UAE emirates',
			'Part of <a href="#parent">Casa Vera Home</a>',
		);
	}
	if ( null === $items ) {
		$items = array(
			'Free home visit &amp; measurement',
			'Made to measure in our workshop',
			'Installed by our own team',
			'Dubai &amp; all UAE emirates',
			'Part of <a href="#parent">Casa Vera Home</a>',
		);
	}
	return dce_b_group( dce_b_list( $items ), array( 'align' => 'full', 'class' => 'dce-trust', 'layout' => 'constrained' ) );
}

/** Section heading block (kicker + h2 + lead). */
function dce_sec_head( $kicker, $title, $lead = '' ) {
	return dce_b_group( dce_b_p( $kicker, 'dce-kicker' ) . dce_b_h( $title ) . ( $lead ? dce_b_p( $lead, 'dce-lead' ) : '' ), array( 'class' => 'dce-sec-head' ) );
}

/**
 * Split image/text section.
 *
 * @param array $s kicker, title, paras[], checks[], img, buttons, reverse, class.
 */
function dce_sec_split( $s ) {
	$text = dce_b_p( $s['kicker'], 'dce-kicker' ) . dce_b_h( $s['title'] );
	foreach ( $s['paras'] as $p ) {
		$text .= dce_b_p( $p );
	}
	if ( ! empty( $s['checks'] ) ) {
		$text .= dce_b_list( $s['checks'], 'dce-checks' );
	}
	if ( ! empty( $s['buttons'] ) ) {
		$text .= dce_b_buttons( $s['buttons'] );
	}
	if ( empty( $s['img'] ) ) {
		return dce_b_section( $text, dce_cls( 'dce-sec dce-split dce-split-solo', isset( $s['class'] ) ? $s['class'] : '' ) );
	}
	$img  = dce_b_image( $s['img'] );
	$cols = ! empty( $s['reverse'] ) ? array( $text, $img ) : array( $img, $text );
	return dce_b_section( dce_b_columns( $cols ), dce_cls( 'dce-sec dce-split', isset( $s['class'] ) ? $s['class'] : '' ) );
}

/**
 * Tiles section.
 *
 * @param array $tiles [title, text].
 */
function dce_sec_tiles( $kicker, $title, $lead, $tiles, $class = 'dce-sec dce-sec-alt' ) {
	$inner = '';
	foreach ( $tiles as $t ) {
		$inner .= dce_b_group( dce_b_h( $t[0], 3 ) . dce_b_p( $t[1] ), array( 'class' => 'dce-tile' ) );
	}
	return dce_b_section( dce_sec_head( $kicker, $title, $lead ) . dce_b_wide( $inner, 'dce-tiles' ), $class );
}

function dce_sec_steps( $title = 'From first message <em>to finished window</em>', $class = 'dce-sec dce-sec-dark' ) {
	$steps = array(
		array( 'Message or call', 'Send a few photos of your windows on <a href="#whatsapp">WhatsApp</a> or call us. We reply within working hours.' ),
		array( 'Free home visit', 'We visit your home or office, measure every window and bring fabric and blind samples to compare in your own light.' ),
		array( 'Choose &amp; confirm', 'Pick fabrics, headings, linings and controls. You receive a clear, itemised quotation before anything is made.' ),
		array( 'Made &amp; installed', 'Everything is made to measure and installed by our own team — tracks, rods, brackets and motors included.' ),
	);
	if ( 'dbh' === dce_profile() ) {
		$steps = array(
			array( 'Message or call', 'Send photos of your space on <a href="#whatsapp">WhatsApp</a> or call us. We reply within working hours.' ),
			array( 'Free site visit', 'We visit, measure the windows, floors, walls or furniture and bring samples to compare on site.' ),
			array( 'Choose &amp; confirm', 'Pick materials, colours and finishes. You receive a clear, itemised quotation before work starts.' ),
			array( 'Supply &amp; install', 'Our own team prepares, fits and installs everything, then leaves the space clean.' ),
		);
	}
	$inner = '';
	foreach ( $steps as $s ) {
		$inner .= dce_b_group( dce_b_h( $s[0], 3 ) . dce_b_p( $s[1] ), array( 'class' => 'dce-step' ) );
	}
	if ( 'dbh' === dce_profile() ) {
		$title = str_replace( 'to finished window', 'to finished space', $title );
	}
	return dce_b_section( dce_sec_head( 'How it works', $title ) . dce_b_wide( $inner, 'dce-steps' ), $class );
}

/**
 * Card grid.
 *
 * @param array $cards Each: title, text, url, img.
 */
function dce_sec_cards( $kicker, $title, $lead, $cards, $class = 'dce-sec', $after = '' ) {
	$inner = '';
	foreach ( $cards as $c ) {
		$card   = ( ! empty( $c['img'] ) ? dce_b_image( $c['img'], $c['url'] ) : '' )
			. dce_b_h( '<a href="' . esc_url( $c['url'] ) . '">' . $c['title'] . '</a>', 3 )
			. dce_b_p( $c['text'] )
			. dce_b_p( '<a href="' . esc_url( $c['url'] ) . '">' . ( isset( $c['more'] ) ? $c['more'] : 'Explore' ) . ' →</a>', 'dce-more' );
		$inner .= dce_b_group( $card, array( 'class' => 'dce-card' ) );
	}
	return dce_b_section( ( $title ? dce_sec_head( $kicker, $title, $lead ) : '' ) . dce_b_wide( $inner, 'dce-cards' ) . $after, $class );
}

function dce_sec_gallery( $kicker, $title, $lead, $imgs, $class = 'dce-sec' ) {
	if ( ! $imgs ) {
		return '';
	}
	$inner = '';
	foreach ( $imgs as $img ) {
		$inner .= dce_b_image( $img );
	}
	return dce_b_section( dce_sec_head( $kicker, $title, $lead ) . dce_b_wide( $inner, 'dce-gallery' ), $class );
}

function dce_sec_faq( $faqs, $title = 'Questions <em>we are often asked</em>', $class = 'dce-sec' ) {
	$inner = '';
	foreach ( $faqs as $f ) {
		$inner .= dce_b_details( $f[0], $f[1] );
	}
	return dce_b_section( dce_sec_head( 'FAQ', $title ) . dce_b_group( $inner, array( 'class' => 'dce-faq' ) ), $class );
}

/**
 * Links as pills.
 *
 * @param array $links [label, url].
 */
function dce_sec_links( $kicker, $title, $lead, $links, $class = 'dce-sec dce-sec-alt' ) {
	$items = array();
	foreach ( $links as $l ) {
		$items[] = '<a href="' . esc_url( $l[1] ) . '">' . $l[0] . '</a>';
	}
	return dce_b_section( dce_sec_head( $kicker, $title, $lead ) . dce_b_wide( dce_b_list( $items, 'dce-area-links' ) ), $class );
}

/**
 * Quote / lead section with WhatsApp form.
 *
 * @param string $service Preselected service.
 * @param string $area    Preselected area.
 */
function dce_sec_quote( $service = '', $area = '', $title = 'Book your <em>free home visit</em>' ) {
	$left  = dce_b_p( 'Get in touch', 'dce-kicker' )
		. dce_b_h( $title )
		. dce_b_p( 'Fill in the form and WhatsApp opens with your details ready to send. Your request is also saved and emailed to our team, so nothing gets missed.', 'dce-lead' )
		. dce_b_list(
			array(
				'<strong>WhatsApp</strong> <a href="#whatsapp">Chat with us now</a>',
				'<strong>Phone</strong> <a href="#call">[dce_info key="phone"]</a>',
				'<strong>Email</strong> <a href="#email">[dce_info key="email"]</a>',
				'<strong>Showroom</strong> <a href="#map">[dce_info key="address"]</a>',
				'<strong>Hours</strong> [dce_info key="hours"]',
			),
			'dce-contact-list'
		);
	$sc    = '[dce_lead_form' . ( $service ? ' service="' . esc_attr( $service ) . '"' : '' ) . ( $area ? ' area="' . esc_attr( $area ) . '"' : '' ) . ']';
	$right = dce_b_shortcode( $sc );
	return dce_b_section( dce_b_columns( array( $left, $right ) ), 'dce-sec dce-sec-alt dce-quote', 'quote' );
}

function dce_sec_cta( $title, $lead, $buttons = null ) {
	if ( null === $buttons ) {
		$buttons = array(
			array( 'WhatsApp us', '#whatsapp', 'dce-wa' ),
			array( 'Call ' . esc_html( dce_opt( 'phone' ) ), '#call', 'is-style-outline' ),
		);
	}
	$band = dce_b_group( dce_b_p( 'Free home visit', 'dce-kicker' ) . dce_b_h( $title ) . dce_b_p( $lead, 'dce-lead' ) . dce_b_buttons( $buttons ), array( 'align' => 'wide', 'class' => 'dce-cta' ) );
	return dce_b_section( $band, 'dce-sec' );
}

function dce_sec_parent() {
	$text = dce_b_p( 'Our parent company', 'dce-kicker' )
		. dce_b_h( 'Part of <em>Casa Vera Home</em>' )
		. dce_b_p( ( 'dbh' === dce_profile() ? 'Dubai Blinds Hub is part of' : 'Dubai Curtain Experts is the dedicated curtains and blinds studio of' ) . ' <strong>Casa Vera Home</strong> (Mukhtar Curtain LLC). For furniture, décor and complete interior fit-outs, explore our parent company — one team for your whole home.' );
	$btn  = dce_b_buttons( array( array( 'Visit Casa Vera Home', '#parent', 'is-style-outline' ) ) );
	return dce_b_section( dce_b_group( dce_b_columns( array( $text, $btn ) ), array( 'align' => 'wide', 'class' => 'dce-parent' ) ), 'dce-sec' );
}

/** Video embed (e.g. a Google Drive preview). */
function dce_sec_video( $kicker, $title, $lead, $embed_url, $class = 'dce-sec' ) {
	$iframe = '<div class="dce-video"><iframe src="' . esc_url( $embed_url ) . '" title="' . esc_attr( wp_strip_all_tags( $title ) ) . '" loading="lazy" allow="autoplay; encrypted-media" allowfullscreen></iframe></div>';
	return dce_b_section( dce_sec_head( $kicker, $title, $lead ) . dce_b_group( dce_b_html( $iframe ), array( 'align' => 'wide' ) ), $class );
}
