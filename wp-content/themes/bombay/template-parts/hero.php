<?php
/**
 * Small dark hero used by theme-rendered pages (archives, search, 404, plain pages).
 *
 * Expects $args: eyebrow, title, lead (all optional except title).
 *
 * @package Bombay
 */

defined( 'ABSPATH' ) || exit;

$args = wp_parse_args( $args ?? array(), array( 'eyebrow' => '', 'title' => '', 'lead' => '' ) );
?>
<section class="bm-hero bm-hero-sm">
	<div class="bm-wrap">
		<?php if ( $args['eyebrow'] ) : ?>
			<p class="bm-eyebrow"><?php echo wp_kses_post( $args['eyebrow'] ); ?></p>
		<?php endif; ?>
		<h1><?php echo wp_kses_post( $args['title'] ); ?></h1>
		<?php if ( $args['lead'] ) : ?>
			<p class="bm-lead"><?php echo wp_kses_post( $args['lead'] ); ?></p>
		<?php endif; ?>
	</div>
</section>
