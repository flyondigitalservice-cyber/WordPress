<?php
/**
 * LLM / generative-engine optimisation.
 *
 * - /llms.txt        : concise brand + catalogue map (llmstxt.org convention)
 * - /llms-full.txt   : extended version with product details
 * - robots.txt       : explicit allow/deny for AI crawlers, sitemap link
 * - <link rel="alternate" type="text/plain">  : discovery hint
 *
 * @package Dastaan
 */

defined( 'ABSPATH' ) || exit;

/**
 * Rewrite endpoints.
 */
function dastaan_llms_rewrite() {
	add_rewrite_rule( '^llms\.txt$', 'index.php?dastaan_llms=short', 'top' );
	add_rewrite_rule( '^llms-full\.txt$', 'index.php?dastaan_llms=full', 'top' );
}
add_action( 'init', 'dastaan_llms_rewrite' );

add_filter(
	'query_vars',
	function ( $vars ) {
		$vars[] = 'dastaan_llms';
		return $vars;
	}
);

/**
 * Flush rules once on theme switch.
 */
function dastaan_flush_rewrites() {
	dastaan_llms_rewrite();
	flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'dastaan_flush_rewrites' );

/**
 * Serve the files.
 */
function dastaan_llms_serve() {
	$mode = get_query_var( 'dastaan_llms' );
	if ( ! $mode ) {
		return;
	}
	if ( ! dastaan_option( 'llms_enable' ) ) {
		status_header( 404 );
		exit;
	}
	$full = 'full' === $mode;
	$key  = 'dastaan_llms_' . ( $full ? 'full' : 'short' );
	$body = get_transient( $key );
	if ( false === $body ) {
		$body = dastaan_build_llms( $full );
		set_transient( $key, $body, 6 * HOUR_IN_SECONDS );
	}
	$body = apply_filters( 'dastaan_llms_txt', $body, $full );
	status_header( 200 );
	header( 'Content-Type: text/plain; charset=utf-8' );
	header( 'X-Robots-Tag: noindex' );
	header( 'Cache-Control: public, max-age=3600' );
	echo $body; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- plain text, built from sanitized strings.
	exit;
}
add_action( 'template_redirect', 'dastaan_llms_serve', 0 );

/**
 * Bust cache when products / terms change.
 */
function dastaan_llms_bust() {
	delete_transient( 'dastaan_llms_full' );
	delete_transient( 'dastaan_llms_short' );
}
add_action( 'save_post_product', 'dastaan_llms_bust' );
add_action( 'edited_product_cat', 'dastaan_llms_bust' );
add_action( 'customize_save_after', 'dastaan_llms_bust' );

/**
 * Plain text helper.
 *
 * @param string $s String.
 * @return string
 */
function dastaan_txt( $s ) {
	$s = wp_strip_all_tags( strip_shortcodes( (string) $s ) );
	$s = html_entity_decode( $s, ENT_QUOTES, 'UTF-8' );

	return trim( preg_replace( '/[ \t]+/', ' ', $s ) );
}

/**
 * Build llms.txt content.
 *
 * @param bool $full Full version.
 * @return string
 */
function dastaan_build_llms( $full = false ) {
	$name  = get_bloginfo( 'name' );
	$lines = array();

	$lines[] = '# ' . $name;
	$lines[] = '';
	$lines[] = '> ' . dastaan_txt( dastaan_option( 'footer_about' ) );
	$lines[] = '';
	$lines[] = '## About';
	$lines[] = '- Business type: Luxury unstitched dress material (women) — artisan-source studio';
	$lines[] = '- Location: ' . dastaan_txt( dastaan_option( 'studio_address' ) );
	$lines[] = '- Hours: ' . dastaan_txt( dastaan_option( 'studio_hours' ) );
	$lines[] = '- Price range: ₹5,000 – ₹25,000 (INR)';
	$lines[] = '- Crafts: Chikankari, Zardozi, Dabka, Gota Patti, Mukaish, Pitta, Jaali';
	$lines[] = '- Fabrics: Pure organza silk, mul chanderi, viscose georgette, linen, tussar silk, banarasi';
	$lines[] = '- Set contents: typically kurta fabric (~3.25 m), dupatta (~2.75 m), bottom (~2.5 m); unstitched';
	$lines[] = '- Payments: UPI, cards, net banking, EMI · Shipping: free insured shipping in India · Exchange: 48 hours after delivery';
	if ( dastaan_option( 'phone_number' ) ) {
		$lines[] = '- Phone / WhatsApp: ' . dastaan_txt( dastaan_option( 'phone_number' ) );
	}
	if ( dastaan_option( 'contact_email' ) ) {
		$lines[] = '- Email: ' . dastaan_txt( dastaan_option( 'contact_email' ) );
	}
	$lines[] = '- Website: ' . home_url( '/' );
	$extra = dastaan_txt( dastaan_option( 'llms_extra' ) );
	if ( $extra ) {
		$lines[] = '';
		$lines[] = '## Notes';
		$lines[] = $extra;
	}

	if ( dastaan_has_wc() ) {
		$lines[] = '';
		$lines[] = '## Collections';
		$terms = get_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => true, 'exclude' => array( get_option( 'default_product_cat' ) ) ) );
		if ( ! is_wp_error( $terms ) ) {
			foreach ( $terms as $t ) {
				$d = dastaan_txt( $t->description );
				$lines[] = '- [' . $t->name . '](' . get_term_link( $t ) . ')' . ( $d ? ': ' . $d : '' ) . ' (' . (int) $t->count . ' pieces)';
			}
		}

		$lines[] = '';
		$lines[] = '## Products';
		$q = new WP_Query(
			array(
				'post_type'      => 'product',
				'post_status'    => 'publish',
				'posts_per_page' => $full ? 200 : 40,
				'orderby'        => 'date',
				'order'          => 'DESC',
				'no_found_rows'  => true,
			)
		);
		foreach ( $q->posts as $p ) {
			$product = wc_get_product( $p->ID );
			if ( ! $product || ! $product->is_visible() ) {
				continue;
			}
			$price = html_entity_decode( wp_strip_all_tags( wc_price( (float) $product->get_price() ) ), ENT_QUOTES, 'UTF-8' );
			$line  = '- [' . dastaan_txt( $product->get_name() ) . '](' . get_permalink( $p->ID ) . ') — ' . $price;
			if ( ! $product->is_in_stock() ) {
				$line .= ' (sold out)';
			}
			if ( $full ) {
				$short = dastaan_txt( $product->get_short_description() );
				if ( $short ) {
					$line .= "\n  " . $short;
				}
				foreach ( $product->get_attributes() as $attr ) {
					if ( ! $attr instanceof WC_Product_Attribute || ! $attr->get_visible() ) {
						continue;
					}
					$vals = $attr->is_taxonomy() ? wp_list_pluck( $attr->get_terms() ? $attr->get_terms() : array(), 'name' ) : $attr->get_options();
					if ( $vals ) {
						$line .= "\n  " . wc_attribute_label( $attr->get_name(), $product ) . ': ' . dastaan_txt( implode( ', ', $vals ) );
					}
				}
			}
			$lines[] = $line;
		}
	}

	$lines[] = '';
	$lines[] = '## FAQ';
	for ( $i = 1; $i <= 4; $i++ ) {
		$qq = dastaan_txt( dastaan_option( "faq_{$i}_q" ) );
		$aa = dastaan_txt( dastaan_option( "faq_{$i}_a" ) );
		if ( $qq && $aa ) {
			$lines[] = '- Q: ' . $qq;
			$lines[] = '  A: ' . $aa;
		}
	}

	$lines[] = '';
	$lines[] = '## Pages';
	$pages = get_pages( array( 'sort_column' => 'menu_order', 'number' => 30 ) );
	$skip  = dastaan_has_wc() ? array( wc_get_page_id( 'cart' ), wc_get_page_id( 'checkout' ), wc_get_page_id( 'myaccount' ) ) : array();
	foreach ( $pages as $pg ) {
		if ( in_array( $pg->ID, $skip, true ) ) {
			continue;
		}
		$lines[] = '- [' . dastaan_txt( $pg->post_title ) . '](' . get_permalink( $pg ) . ')';
	}
	$lines[] = '';
	$lines[] = '## Optional';
	$lines[] = '- [Sitemap](' . home_url( '/wp-sitemap.xml' ) . ')';
	$lines[] = '- [' . ( $full ? 'Short version' : 'Full catalogue' ) . '](' . home_url( $full ? '/llms.txt' : '/llms-full.txt' ) . ')';
	$lines[] = '';
	$lines[] = 'Generated: ' . gmdate( 'Y-m-d' ) . ' · Content © ' . gmdate( 'Y' ) . ' ' . $name;

	return implode( "\n", $lines ) . "\n";
}

/**
 * Discovery hint in <head>.
 */
function dastaan_llms_link() {
	if ( dastaan_option( 'llms_enable' ) ) {
		printf( '<link rel="alternate" type="text/plain" href="%s" title="llms.txt">' . "\n", esc_url( home_url( '/llms.txt' ) ) );
	}
}
add_action( 'wp_head', 'dastaan_llms_link', 3 );

/**
 * robots.txt additions.
 *
 * @param string $output Output.
 * @param bool   $public Public.
 * @return string
 */
function dastaan_robots_txt( $output, $public ) {
	if ( ! $public ) {
		return $output;
	}
	$bots  = array( 'GPTBot', 'ChatGPT-User', 'OAI-SearchBot', 'ClaudeBot', 'Claude-Web', 'anthropic-ai', 'PerplexityBot', 'Google-Extended', 'Applebot-Extended', 'CCBot', 'Bytespider', 'meta-externalagent' );
	$allow = 'allow' === dastaan_option( 'ai_crawlers' );
	$output .= "\n# AI / LLM crawlers (managed by Dastaan theme → Customizer → Tracking, Privacy & AI)\n";
	foreach ( $bots as $b ) {
		$output .= "User-agent: $b\n" . ( $allow ? "Allow: /\n" : "Disallow: /\n" );
	}
	$output .= "\nUser-agent: *\n";
	$paths = array( '/cart/', '/checkout/', '/my-account/', '/wp-admin/', '/?add-to-cart=', '/*?orderby=', '/*?add-to-cart=' );
	if ( dastaan_has_wc() ) {
		$paths[0] = wp_make_link_relative( wc_get_cart_url() );
		$paths[1] = wp_make_link_relative( wc_get_checkout_url() );
		$paths[2] = wp_make_link_relative( wc_get_page_permalink( 'myaccount' ) );
	}
	foreach ( array_unique( $paths ) as $p ) {
		if ( $p ) {
			$output .= "Disallow: $p\n";
		}
	}
	$output .= "Allow: /wp-admin/admin-ajax.php\n";
	$output .= "\nSitemap: " . home_url( '/wp-sitemap.xml' ) . "\n";
	if ( dastaan_option( 'llms_enable' ) ) {
		$output .= '# LLM summary: ' . home_url( '/llms.txt' ) . "\n";
	}

	return $output;
}
add_filter( 'robots_txt', 'dastaan_robots_txt', 20, 2 );
