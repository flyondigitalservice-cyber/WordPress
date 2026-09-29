<?php
/**
 * Shortcodes used inside page content. Each one is a normal "Shortcode" block
 * in the editor, so it can be moved, removed or re-added anywhere.
 *
 *   [dgf_whatsapp label="…" message="…" style="solid|outline"]
 *   [dgf_cta title="…" text="…"]
 *   [dgf_quote_form title="…"]
 *   [dgf_children parent="page-slug" style="cards|pills" limit="0"]
 *   [dgf_related limit="3"]
 *   [dgf_catalogues scope="all|page" title="…"]
 *   [dgf_gallery source="page|all" limit="12"]
 *   [dgf_stats]  [dgf_contact_info]  [dgf_map]  [dgf_parent_company]
 *
 * @package DGF
 */

defined( 'ABSPATH' ) || exit;

add_action( 'init', 'dgf_register_shortcodes' );
/**
 * Register shortcodes.
 */
function dgf_register_shortcodes() {
	$map = array(
		'dgf_whatsapp'       => 'dgf_sc_whatsapp',
		'dgf_cta'            => 'dgf_sc_cta',
		'dgf_quote_form'     => 'dgf_sc_quote_form',
		'dgf_children'       => 'dgf_sc_children',
		'dgf_related'        => 'dgf_sc_related',
		'dgf_catalogues'     => 'dgf_sc_catalogues',
		'dgf_gallery'        => 'dgf_sc_gallery',
		'dgf_stats'          => 'dgf_sc_stats',
		'dgf_contact_info'   => 'dgf_sc_contact_info',
		'dgf_map'            => 'dgf_sc_map',
		'dgf_parent_company' => 'dgf_sc_parent_company',
	);
	foreach ( $map as $tag => $callback ) {
		add_shortcode( $tag, $callback );
	}
}

/**
 * WhatsApp button.
 *
 * @param array $atts Attributes.
 * @return string
 */
function dgf_sc_whatsapp( $atts ) {
	$atts = shortcode_atts(
		array(
			'label'   => __( 'Chat on WhatsApp', 'dgf' ),
			'message' => '',
			'style'   => 'solid',
		),
		$atts,
		'dgf_whatsapp'
	);
	$message = $atts['message'] ? $atts['message'] : dgf_wa_message_for();
	$class   = 'outline' === $atts['style'] ? 'dgf-btn dgf-btn--ghost' : 'dgf-btn dgf-btn--wa';
	return sprintf(
		'<a class="%1$s" href="%2$s" target="_blank" rel="noopener" data-dgf-wa>%3$s<span>%4$s</span></a>',
		esc_attr( $class ),
		esc_url( dgf_wa_url( $message ) ),
		dgf_icon( 'whatsapp', 20 ),
		esc_html( $atts['label'] )
	);
}

/**
 * Full-width call-to-action band.
 *
 * @param array $atts Attributes.
 * @return string
 */
function dgf_sc_cta( $atts ) {
	$atts = shortcode_atts(
		array(
			'title' => __( 'Ready to upgrade your gym floor?', 'dgf' ),
			'text'  => __( 'Send us your room size and a photo on WhatsApp — we reply with the right system, samples and a clear quote.', 'dgf' ),
		),
		$atts,
		'dgf_cta'
	);
	$phone = dgf_opt( 'phone_link' );
	ob_start();
	?>
	<section class="dgf-cta">
		<div class="dgf-cta__inner">
			<div class="dgf-cta__copy">
				<p class="dgf-eyebrow"><?php esc_html_e( 'Free quote · Free samples', 'dgf' ); ?></p>
				<h2 class="dgf-cta__title"><?php echo esc_html( $atts['title'] ); ?></h2>
				<p><?php echo esc_html( $atts['text'] ); ?></p>
			</div>
			<div class="dgf-cta__actions">
				<?php echo dgf_sc_whatsapp( array( 'label' => __( 'Get a quote on WhatsApp', 'dgf' ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php if ( $phone ) : ?>
					<a class="dgf-btn dgf-btn--ghost" href="<?php echo esc_url( 'tel:' . preg_replace( '/[^\d+]/', '', $phone ) ); ?>"><?php echo dgf_icon( 'phone', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><?php echo esc_html( dgf_opt( 'phone_display' ) ? dgf_opt( 'phone_display' ) : $phone ); ?></span></a>
				<?php else : ?>
					<a class="dgf-btn dgf-btn--ghost" href="<?php echo esc_url( dgf_page_url( 'catalogues' ) ); ?>"><?php echo dgf_icon( 'file', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><?php esc_html_e( 'View catalogues', 'dgf' ); ?></span></a>
				<?php endif; ?>
			</div>
		</div>
	</section>
	<?php
	return ob_get_clean();
}

/**
 * Quote form.
 *
 * @param array $atts Attributes.
 * @return string
 */
function dgf_sc_quote_form( $atts ) {
	$atts = shortcode_atts(
		array(
			'title'   => __( 'Get a free quote on WhatsApp', 'dgf' ),
			'product' => '',
		),
		$atts,
		'dgf_quote_form'
	);
	return dgf_lead_form( $atts );
}

/**
 * Card for one page.
 *
 * @param WP_Post $page Page.
 * @return string
 */
function dgf_page_card( $page ) {
	$thumb = get_the_post_thumbnail( $page, 'dgf-card', array( 'loading' => 'lazy', 'alt' => wp_strip_all_tags( $page->post_title ) ) );
	if ( ! $thumb ) {
		$thumb = '<span class="dgf-card__placeholder" aria-hidden="true"><span>' . esc_html( mb_substr( wp_strip_all_tags( $page->post_title ), 0, 1 ) ) . '</span></span>';
	}
	$excerpt = has_excerpt( $page ) ? get_the_excerpt( $page ) : '';
	return sprintf(
		'<a class="dgf-card" href="%1$s"><span class="dgf-card__media">%2$s</span><span class="dgf-card__body"><span class="dgf-card__title">%3$s</span>%4$s<span class="dgf-card__more">%5$s %6$s</span></span></a>',
		esc_url( get_permalink( $page ) ),
		$thumb,
		esc_html( wp_strip_all_tags( get_post_meta( $page->ID, '_dgf_card_title', true ) ? get_post_meta( $page->ID, '_dgf_card_title', true ) : $page->post_title ) ),
		$excerpt ? '<span class="dgf-card__text">' . esc_html( wp_trim_words( $excerpt, 18, '…' ) ) . '</span>' : '',
		esc_html__( 'Explore', 'dgf' ),
		dgf_icon( 'arrow', 16 )
	);
}

/**
 * Grid of child pages.
 *
 * @param array $atts Attributes.
 * @return string
 */
function dgf_sc_children( $atts ) {
	$atts   = shortcode_atts(
		array(
			'parent'  => '',
			'style'   => 'cards',
			'limit'   => 0,
			'emirate' => '',
			'slugs'   => '',
		),
		$atts,
		'dgf_children'
	);
	// slugs="a,b,c" lists those specific pages in that order (e.g. category cards on the home page).
	if ( $atts['slugs'] ) {
		$list = array();
		foreach ( array_filter( array_map( 'trim', explode( ',', $atts['slugs'] ) ) ) as $slug ) {
			$found = dgf_get_page( $slug );
			if ( $found && 'publish' === $found->post_status ) {
				$list[] = $found;
			}
		}
		if ( ! $list ) {
			return '';
		}
		$out = '<div class="dgf-cards">';
		foreach ( $list as $item ) {
			$out .= dgf_page_card( $item );
		}
		return $out . '</div>';
	}
	$parent = $atts['parent'] ? dgf_get_page( $atts['parent'] ) : get_post( get_queried_object_id() );
	if ( ! $parent ) {
		return '';
	}
	$pages = get_pages(
		array(
			'parent'      => $parent->ID,
			'sort_column' => 'menu_order,post_title',
			'post_status' => 'publish',
		)
	);
	// emirate="dubai" → Dubai pages only; emirate="other" → everything outside Dubai.
	if ( $atts['emirate'] ) {
		$want  = strtolower( $atts['emirate'] );
		$pages = array_values(
			array_filter(
				$pages,
				static function ( $page ) use ( $want ) {
					$is_dubai = 'dubai' === strtolower( (string) get_post_meta( $page->ID, '_dgf_emirate', true ) );
					return 'other' === $want ? ! $is_dubai : $is_dubai;
				}
			)
		);
	}
	if ( (int) $atts['limit'] > 0 ) {
		$pages = array_slice( $pages, 0, (int) $atts['limit'] );
	}
	if ( ! $pages ) {
		return '';
	}
	if ( 'pills' === $atts['style'] ) {
		$out = '<ul class="dgf-pills">';
		foreach ( $pages as $page ) {
			$label = get_post_meta( $page->ID, '_dgf_place', true );
			$out  .= sprintf( '<li><a href="%s">%s%s</a></li>', esc_url( get_permalink( $page ) ), dgf_icon( 'pin', 14 ), esc_html( $label ? $label : wp_strip_all_tags( $page->post_title ) ) );
		}
		return $out . '</ul>';
	}
	$out = '<div class="dgf-cards">';
	foreach ( $pages as $page ) {
		$out .= dgf_page_card( $page );
	}
	return $out . '</div>';
}

/**
 * Sibling pages of the current page.
 *
 * @param array $atts Attributes.
 * @return string
 */
function dgf_sc_related( $atts ) {
	$atts = shortcode_atts( array( 'limit' => 3 ), $atts, 'dgf_related' );
	$id   = get_queried_object_id();
	$post = get_post( $id );
	if ( ! $post || ! $post->post_parent ) {
		return '';
	}
	$siblings = get_pages(
		array(
			'parent'      => $post->post_parent,
			'exclude'     => array( $id ),
			'sort_column' => 'menu_order,post_title',
			'post_status' => 'publish',
		)
	);
	if ( ! $siblings ) {
		return '';
	}
	// Rotate so each page links to its next neighbours (spreads internal links evenly).
	$all   = get_pages( array( 'parent' => $post->post_parent, 'sort_column' => 'menu_order,post_title', 'post_status' => 'publish' ) );
	$index = 0;
	foreach ( $all as $i => $p ) {
		if ( (int) $p->ID === (int) $id ) {
			$index = $i;
			break;
		}
	}
	$ordered = array_merge( array_slice( $siblings, $index ), array_slice( $siblings, 0, $index ) );
	$ordered = array_slice( $ordered, 0, max( 1, (int) $atts['limit'] ) );

	$out = '<div class="dgf-cards dgf-cards--compact">';
	foreach ( $ordered as $page ) {
		$out .= dgf_page_card( $page );
	}
	return $out . '</div>';
}

/**
 * Catalogue library: every PDF in the Media Library becomes a clickable button.
 * On a product page (scope="page") only PDFs attached to that page are shown.
 *
 * @param array $atts Attributes.
 * @return string
 */
function dgf_sc_catalogues( $atts ) {
	$atts = shortcode_atts(
		array(
			'scope' => 'all',
			'title' => '',
		),
		$atts,
		'dgf_catalogues'
	);
	$args = array(
		'post_type'      => 'attachment',
		'post_mime_type' => 'application/pdf',
		'post_status'    => 'inherit',
		'posts_per_page' => 100,
		'orderby'        => array( 'menu_order' => 'ASC', 'title' => 'ASC' ),
	);
	if ( 'page' === $atts['scope'] ) {
		$args['post_parent'] = get_queried_object_id();
	}
	$pdfs = get_posts( $args );

	if ( ! $pdfs ) {
		if ( 'page' === $atts['scope'] ) {
			return '';
		}
		$msg = current_user_can( 'upload_files' )
			? sprintf(
				/* translators: %s: media upload URL */
				__( 'No catalogues yet. Upload PDF files in <a href="%s">Media → Add New</a> and they appear here automatically as download buttons.', 'dgf' ),
				esc_url( admin_url( 'media-new.php' ) )
			)
			: __( 'Our latest catalogues are available on request — tap below and we will send them to you on WhatsApp.', 'dgf' );
		return '<div class="dgf-catalogues dgf-catalogues--empty"><p>' . wp_kses( $msg, array( 'a' => array( 'href' => array() ) ) ) . '</p>' . dgf_sc_whatsapp( array( 'label' => __( 'Request catalogue on WhatsApp', 'dgf' ), 'message' => sprintf( 'Hello %s, please send me your gym flooring catalogue.', dgf_opt( 'brand_name' ) ) ) ) . '</div>';
	}

	$out = '<div class="dgf-catalogues">';
	if ( $atts['title'] ) {
		$out .= '<h3 class="dgf-catalogues__title">' . esc_html( $atts['title'] ) . '</h3>';
	}
	$out .= '<ul class="dgf-catalogues__list">';
	foreach ( $pdfs as $pdf ) {
		$url   = wp_get_attachment_url( $pdf->ID );
		$file  = get_attached_file( $pdf->ID );
		$size  = ( $file && file_exists( $file ) ) ? size_format( filesize( $file ), 1 ) : '';
		$title = $pdf->post_title ? $pdf->post_title : basename( (string) $url );
		$desc  = $pdf->post_excerpt ? $pdf->post_excerpt : $pdf->post_content;
		$out  .= '<li class="dgf-catalogue">';
		$out  .= '<span class="dgf-catalogue__icon">' . dgf_icon( 'file', 28 ) . '<em>PDF</em></span>';
		$out  .= '<span class="dgf-catalogue__meta"><strong>' . esc_html( $title ) . '</strong>';
		if ( $desc ) {
			$out .= '<span>' . esc_html( wp_trim_words( wp_strip_all_tags( $desc ), 20, '…' ) ) . '</span>';
		}
		if ( $size ) {
			$out .= '<small>' . esc_html( $size ) . '</small>';
		}
		$out .= '</span><span class="dgf-catalogue__actions">';
		$out .= sprintf( '<a class="dgf-btn dgf-btn--dark dgf-btn--sm" href="%1$s" target="_blank" rel="noopener">%2$s<span>%3$s</span></a>', esc_url( $url ), dgf_icon( 'external', 16 ), esc_html__( 'View PDF', 'dgf' ) );
		$out .= sprintf( '<a class="dgf-btn dgf-btn--ghost dgf-btn--sm" href="%1$s" download>%2$s<span>%3$s</span></a>', esc_url( $url ), dgf_icon( 'download', 16 ), esc_html__( 'Download', 'dgf' ) );
		$out .= sprintf(
			'<a class="dgf-btn dgf-btn--wa dgf-btn--sm" href="%1$s" target="_blank" rel="noopener">%2$s<span>%3$s</span></a>',
			esc_url( dgf_wa_url( sprintf( 'Hello %s, I saw the catalogue "%s" and would like prices.', dgf_opt( 'brand_name' ), $title ) ) ),
			dgf_icon( 'whatsapp', 16 ),
			esc_html__( 'Ask price', 'dgf' )
		);
		$out .= '</span></li>';
	}
	return $out . '</ul></div>';
}

/**
 * Image gallery: images attached to this page (or site-wide for "all").
 *
 * @param array $atts Attributes.
 * @return string
 */
function dgf_sc_gallery( $atts ) {
	$atts = shortcode_atts(
		array(
			'source' => 'page',
			'limit'  => 12,
			'title'  => '',
		),
		$atts,
		'dgf_gallery'
	);
	$id   = get_queried_object_id();
	$args = array(
		'post_type'      => 'attachment',
		'post_mime_type' => 'image',
		'post_status'    => 'inherit',
		'posts_per_page' => max( 1, (int) $atts['limit'] ),
		'orderby'        => array( 'menu_order' => 'ASC', 'date' => 'DESC' ),
	);
	if ( 'all' === $atts['source'] ) {
		// Every image attached to a page (imported or uploaded from a page's editor).
		$page_ids = get_posts(
			array(
				'post_type'      => 'page',
				'post_status'    => 'publish',
				'fields'         => 'ids',
				'posts_per_page' => 500,
			)
		);
		if ( ! $page_ids ) {
			return '';
		}
		$args['post_parent__in'] = $page_ids;
	} else {
		$args['post_parent'] = $id;
		$thumb               = get_post_thumbnail_id( $id );
		if ( $thumb ) {
			$args['post__not_in'] = array( $thumb );
		}
	}
	$images = get_posts( $args );
	if ( ! $images ) {
		return '';
	}
	$out = '';
	if ( $atts['title'] ) {
		$out .= '<h2 class="dgf-gallery__title">' . esc_html( $atts['title'] ) . '</h2>';
	}
	$out .= '<div class="dgf-gallery" data-dgf-gallery>';
	foreach ( $images as $image ) {
		$full = wp_get_attachment_image_url( $image->ID, 'full' );
		$alt  = get_post_meta( $image->ID, '_wp_attachment_image_alt', true );
		$out .= sprintf(
			'<a class="dgf-gallery__item" href="%1$s" data-dgf-lightbox>%2$s</a>',
			esc_url( $full ),
			wp_get_attachment_image( $image->ID, 'dgf-card', false, array( 'loading' => 'lazy', 'alt' => $alt ? $alt : wp_strip_all_tags( get_the_title( $image->post_parent ) ) ) )
		);
	}
	return '<div class="dgf-gallery-wrap">' . $out . '</div></div>';
}

/**
 * Highlight stats from the Customizer.
 *
 * @return string
 */
function dgf_sc_stats() {
	$out = '<div class="dgf-stats">';
	for ( $i = 1; $i <= 4; $i++ ) {
		$value = dgf_opt( 'stat' . $i . '_value' );
		$label = dgf_opt( 'stat' . $i . '_label' );
		if ( '' === $value && '' === $label ) {
			continue;
		}
		$out .= sprintf( '<div class="dgf-stat"><strong>%s</strong><span>%s</span></div>', esc_html( $value ), esc_html( $label ) );
	}
	return $out . '</div>';
}

/**
 * Contact card.
 *
 * @return string
 */
function dgf_sc_contact_info() {
	$rows = array();
	if ( dgf_wa_number() ) {
		$rows[] = sprintf( '<li><a href="%1$s" target="_blank" rel="noopener">%2$s<span><small>%3$s</small>+%4$s</span></a></li>', esc_url( dgf_wa_url() ), dgf_icon( 'whatsapp', 22 ), esc_html__( 'WhatsApp', 'dgf' ), esc_html( dgf_wa_number() ) );
	}
	if ( dgf_opt( 'phone_link' ) ) {
		$rows[] = sprintf( '<li><a href="%1$s">%2$s<span><small>%3$s</small>%4$s</span></a></li>', esc_url( 'tel:' . preg_replace( '/[^\d+]/', '', dgf_opt( 'phone_link' ) ) ), dgf_icon( 'phone', 22 ), esc_html__( 'Call', 'dgf' ), esc_html( dgf_opt( 'phone_display' ) ? dgf_opt( 'phone_display' ) : dgf_opt( 'phone_link' ) ) );
	}
	if ( dgf_opt( 'email' ) ) {
		$rows[] = sprintf( '<li><a href="%1$s">%2$s<span><small>%3$s</small>%4$s</span></a></li>', esc_url( 'mailto:' . dgf_opt( 'email' ) ), dgf_icon( 'mail', 22 ), esc_html__( 'Email', 'dgf' ), esc_html( dgf_opt( 'email' ) ) );
	}
	if ( dgf_opt( 'address' ) ) {
		$rows[] = sprintf( '<li><span class="dgf-contact__row">%1$s<span><small>%2$s</small>%3$s</span></span></li>', dgf_icon( 'pin', 22 ), esc_html__( 'Showroom / office', 'dgf' ), nl2br( esc_html( dgf_opt( 'address' ) ) ) );
	}
	if ( dgf_opt( 'hours' ) ) {
		$rows[] = sprintf( '<li><span class="dgf-contact__row">%1$s<span><small>%2$s</small>%3$s</span></span></li>', dgf_icon( 'clock', 22 ), esc_html__( 'Hours', 'dgf' ), esc_html( dgf_opt( 'hours' ) ) );
	}
	return '<ul class="dgf-contact">' . implode( '', $rows ) . '</ul>';
}

/**
 * Google map embed from the Customizer URL.
 *
 * @return string
 */
function dgf_sc_map() {
	$src = dgf_opt( 'map_embed_url' );
	if ( ! $src || false === strpos( $src, 'google.com/maps' ) ) {
		return '';
	}
	return sprintf( '<div class="dgf-map"><iframe src="%s" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="%s" allowfullscreen></iframe></div>', esc_url( $src ), esc_attr__( 'Map', 'dgf' ) );
}

/**
 * Mother-company band.
 *
 * @return string
 */
function dgf_sc_parent_company() {
	if ( ! dgf_opt( 'parent_url' ) ) {
		return '';
	}
	return sprintf(
		'<section class="dgf-parent"><div class="dgf-parent__inner"><p class="dgf-eyebrow">%1$s</p><p class="dgf-parent__text">%2$s</p><a class="dgf-btn dgf-btn--ghost" href="%3$s" target="_blank" rel="noopener">%4$s<span>%5$s</span></a></div></section>',
		esc_html__( 'Part of the family', 'dgf' ),
		esc_html( dgf_opt( 'parent_blurb' ) ),
		esc_url( dgf_opt( 'parent_url' ) ),
		dgf_icon( 'external', 18 ),
		/* translators: %s: mother company */
		esc_html( sprintf( __( 'Visit %s', 'dgf' ), dgf_opt( 'parent_name' ) ) )
	);
}
