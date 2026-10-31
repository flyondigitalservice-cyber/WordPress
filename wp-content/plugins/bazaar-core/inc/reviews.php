<?php
/**
 * [bzr_reviews] - real customer reviews from WooCommerce, instead of placeholder testimonials.
 *
 * Shows the latest approved product reviews with a minimum star rating. When the store has
 * no qualifying reviews yet, visitors see nothing (the theme hides the empty section) and
 * editors see a short hint.
 *
 * Usage: [bzr_reviews count="3" min_rating="4" words="40"]
 *
 * @package BazaarCore
 */

defined( 'ABSPATH' ) || exit;

/**
 * Short public name: "Priya S." instead of the full name the customer typed.
 *
 * @param string $name Full author name.
 * @return string
 */
function bzr_reviews_short_name( $name ) {
	$parts = preg_split( '/\s+/u', trim( $name ), -1, PREG_SPLIT_NO_EMPTY );
	if ( ! $parts ) {
		return __( 'Verified customer', 'bazaar-core' );
	}
	$first = $parts[0];
	$last  = count( $parts ) > 1 ? mb_substr( end( $parts ), 0, 1 ) . '.' : '';
	return trim( $first . ' ' . $last );
}

/**
 * Render reviews.
 *
 * @param array $atts Shortcode attributes.
 * @return string
 */
function bzr_reviews_shortcode( $atts ) {
	$atts = shortcode_atts(
		array(
			'count'      => 3,
			'min_rating' => 4,
			'words'      => 40,
		),
		$atts,
		'bzr_reviews'
	);

	$count = max( 1, min( 12, absint( $atts['count'] ) ) );
	$min   = max( 1, min( 5, absint( $atts['min_rating'] ) ) );

	$reviews = get_comments(
		array(
			'post_type'  => 'product',
			'type'       => 'review',
			'status'     => 'approve',
			'number'     => $count,
			'meta_query' => array( // phpcs:ignore WordPress.DB.SlowDBQuery -- small, capped query.
				array(
					'key'     => 'rating',
					'value'   => $min,
					'compare' => '>=',
					'type'    => 'NUMERIC',
				),
			),
		)
	);

	if ( ! $reviews ) {
		if ( current_user_can( 'edit_theme_options' ) ) {
			return '<p class="bzr-reviews__empty">' . esc_html__( 'Customer reviews appear here automatically once your store has approved product reviews. Only editors see this note.', 'bazaar-core' ) . '</p>';
		}
		return '';
	}

	$out = '<div class="bzr-reviews">';
	foreach ( $reviews as $review ) {
		$rating  = (int) get_comment_meta( $review->comment_ID, 'rating', true );
		$product = get_post( $review->comment_post_ID );
		$text    = wp_trim_words( wp_strip_all_tags( $review->comment_content ), absint( $atts['words'] ) );
		$label   = sprintf(
			/* translators: %d: star rating out of 5 */
			__( 'Rated %d out of 5', 'bazaar-core' ),
			$rating
		);

		$out .= '<figure class="bzr-review">';
		$out .= '<p class="bzr-review__stars" role="img" aria-label="' . esc_attr( $label ) . '">' . str_repeat( '&#9733;', $rating ) . str_repeat( '&#9734;', 5 - $rating ) . '</p>';
		$out .= '<blockquote class="bzr-review__text"><p>' . esc_html( $text ) . '</p></blockquote>';
		$out .= '<figcaption class="bzr-review__by">' . esc_html( bzr_reviews_short_name( $review->comment_author ) );
		if ( wc_review_is_from_verified_owner( $review->comment_ID ) ) {
			$out .= ' <span class="bzr-review__verified">' . esc_html__( 'Verified buyer', 'bazaar-core' ) . '</span>';
		}
		if ( $product ) {
			$out .= '<br><a href="' . esc_url( get_permalink( $product ) ) . '">' . esc_html( get_the_title( $product ) ) . '</a>';
		}
		$out .= '</figcaption></figure>';
	}
	return $out . '</div>';
}
add_shortcode( 'bzr_reviews', 'bzr_reviews_shortcode' );
