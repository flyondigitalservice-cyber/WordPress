<?php
/**
 * BLOG / GUIDE SEO AND CONTENT TOOLS
 *
 *   JSON-LD on posts:  BlogPosting (or Article), BreadcrumbList and FAQPage.
 *                      FAQs come from <details class="pzs-faq"><summary>Q</summary>A</details>.
 *   Table of contents: posts with 3+ H2 headings get anchor IDs and a "Contents" box
 *                      (Google shows these as "Jump to" links).
 *   Shortcodes:
 *     [propzed_listings type="villa" location="karjat" status="" count="3"]
 *                      Live property cards in the guide (sold listings skipped).
 *     [propzed_cta title="..." text="..."]  Advisor call-to-action box.
 *     [propzed_emi price="7500000"]          Home-loan EMI + stamp duty calculator.
 *
 * Yoast / Rank Math / AIOSEO / SEOPress: Article and breadcrumb JSON-LD are left to
 * them; FAQ JSON-LD, the TOC and the shortcodes still work.
 *
 * @package PropzedProjects
 */

defined( 'ABSPATH' ) || exit;

/**
 * FAQs found in a post's content.
 *
 * @param string $content HTML.
 * @return array[] q, a.
 */
function pzs_extract_faqs( $content ) {
	$faqs = array();
	if ( false === strpos( $content, 'pzs-faq' ) ) {
		return $faqs;
	}
	if ( preg_match_all( '#<details[^>]*class="[^"]*pzs-faq[^"]*"[^>]*>\s*<summary[^>]*>(.*?)</summary>(.*?)</details>#is', $content, $m, PREG_SET_ORDER ) ) {
		foreach ( $m as $row ) {
			$q = trim( wp_strip_all_tags( $row[1] ) );
			$a = trim( wp_kses( $row[2], array( 'a' => array( 'href' => true ), 'strong' => array(), 'em' => array(), 'br' => array(), 'ul' => array(), 'ol' => array(), 'li' => array(), 'p' => array() ) ) );
			if ( $q && $a ) {
				$faqs[] = array( 'q' => $q, 'a' => $a );
			}
		}
	}
	return $faqs;
}

/**
 * JSON-LD for blog posts.
 */
function pzs_post_schema() {
	if ( ! is_singular( 'post' ) ) {
		return;
	}
	$post   = get_queried_object();
	$plugin = function_exists( 'propzed_seo_plugin_active' ) && propzed_seo_plugin_active();
	$graph  = array();

	if ( ! $plugin ) {
		$image = get_the_post_thumbnail_url( $post, 'large' );
		$graph[] = array_filter(
			array(
				'@type'            => 'BlogPosting',
				'@id'              => get_permalink( $post ) . '#article',
				'headline'         => wp_strip_all_tags( get_the_title( $post ) ),
				'description'      => wp_strip_all_tags( has_excerpt( $post ) ? get_the_excerpt( $post ) : wp_trim_words( wp_strip_all_tags( $post->post_content ), 30, '…' ) ),
				'image'            => $image ? array( $image ) : null,
				'datePublished'    => get_the_date( 'c', $post ),
				'dateModified'     => get_the_modified_date( 'c', $post ),
				'author'           => array(
					'@type' => 'Organization',
					'name'  => get_bloginfo( 'name' ) . ' Research Desk',
					'url'   => home_url( '/' ),
				),
				'publisher'        => array( '@id' => home_url( '/#organization' ), '@type' => 'Organization', 'name' => get_bloginfo( 'name' ), 'url' => home_url( '/' ) ),
				'mainEntityOfPage' => get_permalink( $post ),
				'inLanguage'       => 'en-IN',
				'about'            => array_values(
					array_map(
						static function ( $t ) {
							return $t->name;
						},
						array_merge( (array) get_the_category( $post->ID ), (array) ( get_the_tags( $post->ID ) ? get_the_tags( $post->ID ) : array() ) )
					)
				),
			)
		);

		$crumbs = array( __( 'Home', 'propzed-projects' ) => home_url( '/' ) );
		$blog   = (int) get_option( 'page_for_posts' );
		if ( $blog ) {
			$crumbs[ get_the_title( $blog ) ] = get_permalink( $blog );
		}
		$cats = get_the_category( $post->ID );
		if ( $cats ) {
			$crumbs[ $cats[0]->name ] = get_category_link( $cats[0] );
		}
		$crumbs[ wp_strip_all_tags( get_the_title( $post ) ) ] = get_permalink( $post );
		if ( function_exists( 'propzed_schema_breadcrumbs' ) ) {
			$graph[] = propzed_schema_breadcrumbs( $crumbs );
		}
	}

	$faqs = pzs_extract_faqs( $post->post_content );
	if ( $faqs ) {
		$graph[] = array(
			'@type'      => 'FAQPage',
			'mainEntity' => array_map(
				static function ( $f ) {
					return array(
						'@type'          => 'Question',
						'name'           => $f['q'],
						'acceptedAnswer' => array( '@type' => 'Answer', 'text' => $f['a'] ),
					);
				},
				$faqs
			),
		);
	}

	if ( $graph ) {
		echo '<script type="application/ld+json">' . wp_json_encode( array( '@context' => 'https://schema.org', '@graph' => $graph ), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . "</script>\n";
	}
}
add_action( 'wp_head', 'pzs_post_schema', 22 );

/**
 * Table of contents for long posts.
 *
 * @param string $content Content.
 * @return string
 */
function pzs_toc( $content ) {
	if ( ! is_singular( 'post' ) || ! in_the_loop() || ! is_main_query() || false !== strpos( $content, 'pzs-toc' ) ) {
		return $content;
	}
	if ( preg_match_all( '#<h2([^>]*)>(.*?)</h2>#is', $content, $m, PREG_SET_ORDER ) < 3 ) {
		return $content;
	}
	$items = array();
	$used  = array();
	foreach ( $m as $h ) {
		$text = trim( wp_strip_all_tags( $h[2] ) );
		if ( preg_match( '/\bid="([^"]+)"/', $h[1], $idm ) ) {
			$id = $idm[1];
		} else {
			$id = sanitize_title( $text );
			$id = $id ? $id : 'section';
			$base = $id;
			$n    = 2;
			while ( isset( $used[ $id ] ) ) {
				$id = $base . '-' . $n++;
			}
			$new = '<h2' . $h[1] . ' id="' . esc_attr( $id ) . '">' . $h[2] . '</h2>';
			$at  = strpos( $content, $h[0] );
			if ( false !== $at ) {
				$content = substr_replace( $content, $new, $at, strlen( $h[0] ) );
			}
		}
		$used[ $id ] = true;
		$items[]     = '<li><a href="#' . esc_attr( $id ) . '">' . esc_html( $text ) . '</a></li>';
	}
	$toc = '<nav class="pzs-toc" aria-label="' . esc_attr__( 'Contents', 'propzed-projects' ) . '"><details open><summary>' . esc_html__( 'In this guide', 'propzed-projects' ) . '</summary><ol>' . implode( '', $items ) . '</ol></details></nav>';

	// After the first paragraph (the intro), otherwise at the top.
	$pos = stripos( $content, '</p>' );
	return false === $pos ? $toc . $content : substr( $content, 0, $pos + 4 ) . $toc . substr( $content, $pos + 4 );
}
add_filter( 'the_content', 'pzs_toc', 12 );

/**
 * [propzed_listings]: live cards inside guides.
 */
add_shortcode(
	'propzed_listings',
	static function ( $atts ) {
		$atts = shortcode_atts(
			array(
				'type'     => '',
				'location' => '',
				'status'   => '',
				'budget'   => '',
				'count'    => 3,
				'title'    => '',
			),
			$atts,
			'propzed_listings'
		);
		if ( ! function_exists( 'propzed_property_query_args' ) ) {
			return '';
		}
		$args = array_merge(
			propzed_property_query_args(
				array(
					'type'     => $atts['type'],
					'location' => $atts['location'],
					'status'   => $atts['status'],
					'budget'   => $atts['budget'],
				)
			),
			array(
				'post_type'           => 'property',
				'post_status'         => 'publish',
				'posts_per_page'      => max( 1, min( 12, (int) $atts['count'] ) ),
				'ignore_sticky_posts' => true,
				'no_found_rows'       => true,
			)
		);
		// Skip sold / leased listings.
		$args['tax_query']   = isset( $args['tax_query'] ) ? $args['tax_query'] : array( 'relation' => 'AND' ); // phpcs:ignore WordPress.DB.SlowDBQuery
		$args['tax_query'][] = array(
			'taxonomy' => 'property_status',
			'field'    => 'slug',
			'terms'    => array( 'sold', 'leased-out' ),
			'operator' => 'NOT IN',
		);
		$q = new WP_Query( $args );
		$more = function_exists( 'propzed_seo_url' ) && $atts['location'] ? propzed_seo_url( $atts['type'], $atts['status'], $atts['location'] ) : propzed_listing_url();

		ob_start();
		echo '<div class="pzs-listings">';
		if ( $atts['title'] ) {
			echo '<h3 class="pzs-listings__title">' . esc_html( $atts['title'] ) . '</h3>';
		}
		if ( $q->have_posts() ) {
			echo '<div class="pz-grid">';
			while ( $q->have_posts() ) {
				$q->the_post();
				get_template_part( 'template-parts/property-card' );
			}
			echo '</div>';
			wp_reset_postdata();
		} else {
			echo '<p class="pzs-listings__empty">' . esc_html__( 'New listings for this search are added every week. Share your requirement and we will send matching options, including off-market ones.', 'propzed-projects' ) . '</p>';
		}
		echo '<p class="pzs-listings__more"><a class="pz-btn pz-btn--outline" href="' . esc_url( $more ) . '">' . esc_html__( 'See all matching properties', 'propzed-projects' ) . '</a></p>';
		echo '</div>';
		return ob_get_clean();
	}
);

/**
 * [propzed_cta]: advisor box.
 */
add_shortcode(
	'propzed_cta',
	static function ( $atts ) {
		$atts = shortcode_atts(
			array(
				'title' => __( 'Talk to a Propzed advisor', 'propzed-projects' ),
				'text'  => __( 'Free shortlist of verified options in your budget, site visits and help with documents, loans and registration.', 'propzed-projects' ),
				'topic' => '',
			),
			$atts,
			'propzed_cta'
		);
		$wa  = function_exists( 'propzed_whatsapp_link' ) ? propzed_whatsapp_link( trim( 'Hi Propzed, I read your guide' . ( $atts['topic'] ? ' on ' . $atts['topic'] : '' ) . ' and need help.' ) ) : '';
		$req = function_exists( 'propzed_template_url' ) ? propzed_template_url( 'templates/template-requirement.php' ) : home_url( '/post-requirement/' );
		$out  = '<aside class="pzs-cta">';
		$out .= '<div><strong>' . esc_html( $atts['title'] ) . '</strong><p>' . esc_html( $atts['text'] ) . '</p></div>';
		$out .= '<div class="pzs-cta__btns">';
		$out .= '<a class="pz-btn pz-btn--gold" href="' . esc_url( $req ) . '">' . esc_html__( 'Share my requirement', 'propzed-projects' ) . '</a>';
		if ( $wa ) {
			$out .= '<a class="pz-btn pz-btn--wa" href="' . esc_url( $wa ) . '" target="_blank" rel="noopener">' . esc_html__( 'WhatsApp us', 'propzed-projects' ) . '</a>';
		}
		$out .= '</div></aside>';
		return $out;
	}
);

/**
 * [propzed_emi]: EMI, down payment and stamp duty estimate (assets/js/pzs.js).
 */
add_shortcode(
	'propzed_emi',
	static function ( $atts ) {
		$atts = shortcode_atts(
			array(
				'price' => 7500000,
				'rate'  => 8.5,
				'years' => 20,
				'down'  => 20,
			),
			$atts,
			'propzed_emi'
		);
		ob_start();
		?>
		<div class="pzs-emi" data-pzs-emi>
			<h3 class="pzs-emi__title"><?php esc_html_e( 'Budget calculator: EMI and total cost', 'propzed-projects' ); ?></h3>
			<div class="pzs-emi__grid">
				<label><span><?php esc_html_e( 'Property price (₹)', 'propzed-projects' ); ?></span><input type="number" inputmode="numeric" min="500000" step="100000" name="price" value="<?php echo esc_attr( (float) $atts['price'] ); ?>"></label>
				<label><span><?php esc_html_e( 'Down payment (%)', 'propzed-projects' ); ?></span><input type="number" min="10" max="90" step="1" name="down" value="<?php echo esc_attr( (float) $atts['down'] ); ?>"></label>
				<label><span><?php esc_html_e( 'Loan interest (% a year)', 'propzed-projects' ); ?></span><input type="number" min="5" max="15" step="0.05" name="rate" value="<?php echo esc_attr( (float) $atts['rate'] ); ?>"></label>
				<label><span><?php esc_html_e( 'Tenure (years)', 'propzed-projects' ); ?></span><input type="number" min="1" max="30" step="1" name="years" value="<?php echo esc_attr( (int) $atts['years'] ); ?>"></label>
				<label class="pzs-emi__wide"><span><?php esc_html_e( 'Where is the property?', 'propzed-projects' ); ?></span>
					<select name="duty">
						<option value="6"><?php esc_html_e( 'Mumbai (BMC limits): ~6% stamp duty', 'propzed-projects' ); ?></option>
						<option value="7"><?php esc_html_e( 'Navi Mumbai, Thane, Pune & other city limits: ~7%', 'propzed-projects' ); ?></option>
						<option value="5"><?php esc_html_e( 'Woman buyer in Mumbai (1% concession): ~5%', 'propzed-projects' ); ?></option>
						<option value="6"><?php esc_html_e( 'Woman buyer outside Mumbai: ~6%', 'propzed-projects' ); ?></option>
					</select>
				</label>
			</div>
			<dl class="pzs-emi__out" aria-live="polite">
				<div><dt><?php esc_html_e( 'Monthly EMI', 'propzed-projects' ); ?></dt><dd data-out="emi">–</dd></div>
				<div><dt><?php esc_html_e( 'Loan amount', 'propzed-projects' ); ?></dt><dd data-out="loan">–</dd></div>
				<div><dt><?php esc_html_e( 'Cash needed upfront', 'propzed-projects' ); ?></dt><dd data-out="cash">–</dd></div>
				<div><dt><?php esc_html_e( 'Stamp duty + registration', 'propzed-projects' ); ?></dt><dd data-out="duty">–</dd></div>
				<div><dt><?php esc_html_e( 'Total interest over the loan', 'propzed-projects' ); ?></dt><dd data-out="interest">–</dd></div>
				<div><dt><?php esc_html_e( 'Suggested monthly income', 'propzed-projects' ); ?></dt><dd data-out="income">–</dd></div>
			</dl>
			<p class="pzs-emi__note"><?php esc_html_e( 'Estimate only. Registration fee is 1% capped at ₹30,000. Stamp duty is charged on the higher of the agreement value and the ready-reckoner rate; confirm the current rate with your lawyer or bank. "Suggested income" keeps the EMI near 40% of take-home pay.', 'propzed-projects' ); ?></p>
		</div>
		<?php
		return ob_get_clean();
	}
);
