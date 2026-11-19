<?php
/**
 * Page not found.
 *
 * @package Bombay
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main class="bm-main bm-page">
	<?php
	get_template_part(
		'template-parts/hero',
		null,
		array(
			'eyebrow' => 'Error 404',
			'title'   => 'This page has moved or <em>no longer exists.</em>',
			'lead'    => 'The link may be old or mistyped. Try one of these instead, or message us and we will point you to the right place.',
		)
	);
	?>
	<section class="bm-sec">
		<div class="bm-wrap">
			<div class="bm-btns">
				<a class="bm-btn" href="<?php echo esc_url( home_url( '/' ) ); ?>">Go to homepage</a>
				<a class="bm-btn ghost" href="<?php echo esc_url( home_url( '/services/' ) ); ?>">Our services</a>
				<a class="bm-btn wa" href="https://wa.me/918767697389?text=Hi%20Bombay%20Marketing">Chat on WhatsApp</a>
			</div>
			<div class="bm-empty"><?php get_search_form(); ?></div>
		</div>
	</section>
</main>
<?php
get_footer();
