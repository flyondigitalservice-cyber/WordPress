<?php
/**
 * Template tags: reusable output helpers.
 *
 * @package Dastaan
 */

defined( 'ABSPATH' ) || exit;

/**
 * Section heading block (eyebrow + title + optional text/link).
 *
 * @param array $args { eyebrow, title, text, link, link_text, align, tag }.
 */
function dastaan_section_heading( $args ) {
	$args = wp_parse_args(
		$args,
		array(
			'eyebrow'   => '',
			'title'     => '',
			'text'      => '',
			'link'      => '',
			'link_text' => '',
			'align'     => 'center',
			'tag'       => 'h2',
		)
	);
	if ( '' === $args['title'] && '' === $args['eyebrow'] ) {
		return;
	}
	$tag = in_array( $args['tag'], array( 'h1', 'h2', 'h3', 'p' ), true ) ? $args['tag'] : 'h2';
	?>
	<header class="section-heading section-heading--<?php echo esc_attr( $args['align'] ); ?>">
		<div class="section-heading__main">
			<?php if ( $args['eyebrow'] ) : ?>
				<p class="eyebrow"><?php echo esc_html( $args['eyebrow'] ); ?></p>
			<?php endif; ?>
			<?php if ( $args['title'] ) : ?>
				<<?php echo $tag; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- validated above ?> class="section-title"><?php echo esc_html( $args['title'] ); ?></<?php echo $tag; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
			<?php endif; ?>
			<?php if ( $args['text'] ) : ?>
				<p class="section-text"><?php echo esc_html( $args['text'] ); ?></p>
			<?php endif; ?>
		</div>
		<?php if ( $args['link'] && $args['link_text'] ) : ?>
			<a class="section-link" href="<?php echo dastaan_url( $args['link'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>"><?php echo esc_html( $args['link_text'] ); ?> <?php dastaan_the_icon( 'arrow-right', array( 'size' => 16 ) ); ?></a>
		<?php endif; ?>
	</header>
	<?php
}

/**
 * Social links list.
 *
 * @param string $class Extra class.
 */
function dastaan_social_links( $class = '' ) {
	$links = array(
		'instagram' => array( dastaan_option( 'social_instagram' ), 'Instagram' ),
		'facebook'  => array( dastaan_option( 'social_facebook' ), 'Facebook' ),
		'youtube'   => array( dastaan_option( 'social_youtube' ), 'YouTube' ),
		'pinterest' => array( dastaan_option( 'social_pinterest' ), 'Pinterest' ),
		'whatsapp'  => array( dastaan_whatsapp_url(), 'WhatsApp' ),
	);
	$links = array_filter(
		$links,
		function ( $l ) {
			return ! empty( $l[0] );
		}
	);
	if ( ! $links ) {
		return;
	}
	echo '<ul class="social-links ' . esc_attr( $class ) . '">';
	foreach ( $links as $icon => $l ) {
		printf(
			'<li><a href="%1$s" target="_blank" rel="noopener nofollow" aria-label="%2$s">%3$s</a></li>',
			esc_url( $l[0] ),
			esc_attr( $l[1] ),
			dastaan_icon( $icon, array( 'size' => 18 ) ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		);
	}
	echo '</ul>';
}

/**
 * Payment method badges (inline SVG/text, no external images).
 */
function dastaan_payment_icons() {
	$methods = array(
		array( 'UPI', 'upi' ),
		array( 'Visa', 'card' ),
		array( 'Mastercard', 'card' ),
		array( 'RuPay', 'card' ),
		array( 'Net Banking', 'bank' ),
		array( 'EMI', 'rupee' ),
	);
	echo '<ul class="payment-icons" aria-label="' . esc_attr__( 'Accepted payment methods', 'dastaan' ) . '">';
	foreach ( $methods as $m ) {
		echo '<li class="payment-icon">' . dastaan_icon( $m[1], array( 'size' => 14 ) ) . '<span>' . esc_html( $m[0] ) . '</span></li>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
	echo '</ul>';
}

/**
 * Star rating markup.
 *
 * @param float $rating 0–5.
 * @param int   $size   Icon size.
 */
function dastaan_stars( $rating, $size = 14 ) {
	$rating = max( 0, min( 5, (float) $rating ) );
	echo '<span class="stars" role="img" aria-label="' . esc_attr( sprintf( /* translators: %s: rating */ __( 'Rated %s out of 5', 'dastaan' ), $rating ) ) . '">';
	for ( $i = 1; $i <= 5; $i++ ) {
		$class = $i <= round( $rating ) ? 'star is-on' : 'star';
		echo dastaan_icon( 'star', array( 'size' => $size, 'class' => $class ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
	echo '</span>';
}

/**
 * Initials avatar.
 *
 * @param string $name Name.
 * @param string $img  Optional image URL.
 */
function dastaan_avatar( $name, $img = '' ) {
	if ( $img ) {
		echo '<span class="avatar-img">' . dastaan_img( $img, $name, array( 'size' => 'thumbnail' ) ) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		return;
	}
	$parts    = preg_split( '/\s+/', trim( $name ) );
	$initials = '';
	foreach ( array_slice( $parts, 0, 2 ) as $p ) {
		$initials .= mb_substr( $p, 0, 1 );
	}
	echo '<span class="avatar-initials" aria-hidden="true">' . esc_html( mb_strtoupper( $initials ) ) . '</span>';
}

/**
 * Breadcrumbs (schema is emitted separately in seo.php).
 */
function dastaan_breadcrumbs() {
	if ( is_front_page() ) {
		return;
	}
	$crumbs   = array();
	$crumbs[] = array( __( 'Home', 'dastaan' ), home_url( '/' ) );

	if ( dastaan_has_wc() && ( is_woocommerce() || is_cart() || is_checkout() || is_account_page() ) ) {
		$shop_id = wc_get_page_id( 'shop' );
		if ( ! is_shop() && $shop_id > 0 ) {
			$crumbs[] = array( get_the_title( $shop_id ), get_permalink( $shop_id ) );
		}
		if ( is_product_category() || is_product_tag() ) {
			$term = get_queried_object();
			if ( $term && ! empty( $term->parent ) ) {
				$ancestors = array_reverse( get_ancestors( $term->term_id, $term->taxonomy ) );
				foreach ( $ancestors as $a ) {
					$at = get_term( $a, $term->taxonomy );
					if ( $at && ! is_wp_error( $at ) ) {
						$crumbs[] = array( $at->name, get_term_link( $at ) );
					}
				}
			}
			$crumbs[] = array( single_term_title( '', false ), '' );
		} elseif ( is_product() ) {
			$terms = wc_get_product_terms( get_the_ID(), 'product_cat', array( 'orderby' => 'parent', 'order' => 'DESC' ) );
			if ( $terms ) {
				$main = $terms[0];
				$crumbs[] = array( $main->name, get_term_link( $main ) );
			}
			$crumbs[] = array( get_the_title(), '' );
		} elseif ( is_shop() ) {
			$crumbs[] = array( $shop_id > 0 ? get_the_title( $shop_id ) : __( 'Shop', 'dastaan' ), '' );
		} else {
			$crumbs[] = array( get_the_title(), '' );
		}
	} elseif ( is_singular() ) {
		if ( is_singular( 'post' ) ) {
			$cat = get_the_category();
			if ( $cat ) {
				$crumbs[] = array( $cat[0]->name, get_category_link( $cat[0] ) );
			}
		}
		$crumbs[] = array( get_the_title(), '' );
	} elseif ( is_archive() ) {
		$crumbs[] = array( wp_strip_all_tags( get_the_archive_title() ), '' );
	} elseif ( is_search() ) {
		$crumbs[] = array( sprintf( /* translators: %s: search query */ __( 'Search: %s', 'dastaan' ), get_search_query() ), '' );
	} elseif ( is_404() ) {
		$crumbs[] = array( __( 'Page not found', 'dastaan' ), '' );
	}

	$crumbs = apply_filters( 'dastaan_breadcrumbs', $crumbs );
	if ( count( $crumbs ) < 2 ) {
		return;
	}
	echo '<nav class="breadcrumbs" aria-label="' . esc_attr__( 'Breadcrumb', 'dastaan' ) . '"><ol>';
	$last = count( $crumbs ) - 1;
	foreach ( $crumbs as $i => $c ) {
		if ( $i === $last || empty( $c[1] ) ) {
			echo '<li aria-current="page">' . esc_html( $c[0] ) . '</li>';
		} else {
			echo '<li><a href="' . esc_url( $c[1] ) . '">' . esc_html( $c[0] ) . '</a></li>';
		}
	}
	echo '</ol></nav>';
}

/**
 * Get the crumbs array (for schema).
 *
 * @return array
 */
function dastaan_get_breadcrumb_items() {
	ob_start();
	dastaan_breadcrumbs();
	$html = ob_get_clean();
	if ( ! $html ) {
		return array();
	}
	$items = array();
	if ( preg_match_all( '/<li(?: aria-current="page")?>(?:<a href="([^"]+)">)?([^<]+)/', $html, $m, PREG_SET_ORDER ) ) {
		foreach ( $m as $row ) {
			$items[] = array( 'name' => html_entity_decode( $row[2] ), 'url' => $row[1] );
		}
	}

	return $items;
}

/**
 * Product categories for circles / crafts.
 *
 * @param int $limit Limit.
 * @return WP_Term[]
 */
function dastaan_get_showcase_categories( $limit = 10 ) {
	if ( ! dastaan_has_wc() ) {
		return array();
	}
	$exclude_slugs = array_filter( explode( ',', (string) dastaan_option( 'circles_exclude' ) ) );
	$exclude_ids   = array( (int) get_option( 'default_product_cat' ) );
	foreach ( $exclude_slugs as $slug ) {
		$t = get_term_by( 'slug', trim( $slug ), 'product_cat' );
		if ( $t ) {
			$exclude_ids[] = $t->term_id;
		}
	}
	$terms = get_terms(
		array(
			'taxonomy'   => 'product_cat',
			'hide_empty' => true,
			'exclude'    => $exclude_ids,
			'number'     => (int) $limit,
			'orderby'    => 'menu_order',
			'order'      => 'ASC',
			'meta_key'   => 'order', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
		)
	);
	if ( is_wp_error( $terms ) || empty( $terms ) ) {
		$terms = get_terms(
			array(
				'taxonomy'   => 'product_cat',
				'hide_empty' => true,
				'exclude'    => $exclude_ids,
				'number'     => (int) $limit,
				'orderby'    => 'count',
				'order'      => 'DESC',
			)
		);
	}

	return is_wp_error( $terms ) ? array() : $terms;
}

/**
 * Category image URL (thumbnail meta) with theme fallback by slug.
 *
 * @param WP_Term $term Term.
 * @param string  $size Size.
 * @return string
 */
function dastaan_category_image( $term, $size = 'dastaan-tile' ) {
	$thumb_id = (int) get_term_meta( $term->term_id, 'thumbnail_id', true );
	if ( $thumb_id ) {
		$src = wp_get_attachment_image_url( $thumb_id, $size );
		if ( $src ) {
			return $src;
		}
	}
	$map = array(
		'chikankari'   => 'chikankari',
		'zardozi'      => 'zardozi',
		'gota-patti'   => 'gotta-patti',
		'gotta-patti'  => 'gotta-patti',
		'pure-organza' => 'pure-organza',
		'organza'      => 'pure-organza',
		'mul-chanderi' => 'mul-chanderi',
		'chanderi'     => 'mul-chanderi',
		'mukaish'      => 'mukaish-pitta',
		'pitta'        => 'mukaish-pitta',
		'festive-edit' => 'festive-edit',
		'festive'      => 'festive-edit',
	);
	foreach ( $map as $needle => $file ) {
		if ( false !== strpos( $term->slug, $needle ) ) {
			return DASTAAN_URI . 'assets/images/categories/' . $file . '.jpg';
		}
	}

	return dastaan_has_wc() ? wc_placeholder_img_src( $size ) : '';
}

/**
 * Query products for home carousels.
 *
 * @param string $source   recent|featured|bestselling|sale|category.
 * @param int    $count    Count.
 * @param string $category Slug.
 * @return WP_Query|null
 */
function dastaan_products_query( $source, $count, $category = '' ) {
	if ( ! dastaan_has_wc() ) {
		return null;
	}
	$args = array(
		'post_type'           => 'product',
		'post_status'         => 'publish',
		'posts_per_page'      => max( 1, (int) $count ),
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
		'tax_query'           => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
			array(
				'taxonomy' => 'product_visibility',
				'field'    => 'name',
				'terms'    => array( 'exclude-from-catalog' ),
				'operator' => 'NOT IN',
			),
		),
	);
	switch ( $source ) {
		case 'featured':
			$args['tax_query'][] = array(
				'taxonomy' => 'product_visibility',
				'field'    => 'name',
				'terms'    => 'featured',
			);
			break;
		case 'bestselling':
			$args['meta_key'] = 'total_sales'; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			$args['orderby']  = array( 'meta_value_num' => 'DESC', 'date' => 'DESC' );
			break;
		case 'sale':
			$ids = wc_get_product_ids_on_sale();
			if ( empty( $ids ) ) {
				return null;
			}
			$args['post__in'] = $ids;
			break;
		case 'category':
			if ( $category ) {
				$args['tax_query'][] = array(
					'taxonomy' => 'product_cat',
					'field'    => 'slug',
					'terms'    => array_map( 'trim', explode( ',', $category ) ),
				);
			}
			break;
		default:
			$args['orderby'] = 'date';
			$args['order']   = 'DESC';
	}

	return new WP_Query( apply_filters( 'dastaan_products_query_args', $args, $source ) );
}

/**
 * Convert a YouTube / Vimeo / Instagram URL into an embeddable iframe URL. Returns '' if unsupported.
 *
 * @param string $url URL.
 * @return string
 */
function dastaan_video_embed_url( $url ) {
	$url = trim( (string) $url );
	if ( '' === $url ) {
		return '';
	}
	if ( preg_match( '~(?:youtube\.com/(?:watch\?v=|shorts/|embed/)|youtu\.be/)([A-Za-z0-9_-]{6,20})~', $url, $m ) ) {
		return 'https://www.youtube-nocookie.com/embed/' . $m[1] . '?autoplay=1&rel=0&playsinline=1';
	}
	if ( preg_match( '~vimeo\.com/(?:video/)?(\d+)~', $url, $m ) ) {
		return 'https://player.vimeo.com/video/' . $m[1] . '?autoplay=1&dnt=1';
	}
	if ( preg_match( '~instagram\.com/(?:reel|reels|p)/([A-Za-z0-9_-]+)~', $url, $m ) ) {
		return 'https://www.instagram.com/reel/' . $m[1] . '/embed/';
	}

	return '';
}

/**
 * Is a URL a direct video file?
 *
 * @param string $url URL.
 * @return bool
 */
function dastaan_is_video_file( $url ) {
	return (bool) preg_match( '/\.(mp4|webm|mov|m4v)(\?.*)?$/i', (string) $url );
}

/**
 * Reels data from the Customizer.
 *
 * @return array
 */
function dastaan_get_reels() {
	$reels = array();
	for ( $i = 1; $i <= 6; $i++ ) {
		$video_id = (int) dastaan_option( "reel_{$i}_video" );
		$url      = dastaan_option( "reel_{$i}_url" );
		$src      = $video_id ? wp_get_attachment_url( $video_id ) : '';
		if ( ! $src && ! $url ) {
			continue;
		}
		$poster = dastaan_option( "reel_{$i}_poster" );
		if ( ! $poster && $video_id ) {
			$poster = get_the_post_thumbnail_url( $video_id, 'dastaan-tile' );
		}
		$reels[] = array(
			'src'     => $src,
			'embed'   => $src ? '' : dastaan_video_embed_url( $url ),
			'url'     => $url,
			'poster'  => $poster,
			'caption' => dastaan_option( "reel_{$i}_caption" ),
			'link'    => dastaan_option( "reel_{$i}_link" ),
		);
	}

	return $reels;
}

/**
 * Instagram tiles from the Customizer, falling back to recent product images.
 *
 * @return array
 */
function dastaan_get_instagram_tiles() {
	$tiles = array();
	for ( $i = 1; $i <= 8; $i++ ) {
		$img = dastaan_option( "instagram_{$i}_image" );
		if ( $img ) {
			$tiles[] = array( 'image' => $img, 'link' => dastaan_option( "instagram_{$i}_link" ) );
		}
	}
	if ( empty( $tiles ) && dastaan_has_wc() ) {
		$q = dastaan_products_query( 'recent', 8 );
		if ( $q && $q->have_posts() ) {
			foreach ( $q->posts as $p ) {
				$img = get_the_post_thumbnail_url( $p->ID, 'dastaan-square' );
				if ( $img ) {
					$tiles[] = array( 'image' => $img, 'link' => get_permalink( $p->ID ) );
				}
			}
		}
	}

	return apply_filters( 'dastaan_instagram_tiles', $tiles );
}

/**
 * Posted-on meta for blog posts.
 */
function dastaan_posted_on() {
	printf(
		'<span class="posted-on"><time datetime="%1$s">%2$s</time></span> <span class="byline">%3$s</span>',
		esc_attr( get_the_date( DATE_W3C ) ),
		esc_html( get_the_date() ),
		esc_html( sprintf( /* translators: %s: author */ __( 'by %s', 'dastaan' ), get_the_author() ) )
	);
}
