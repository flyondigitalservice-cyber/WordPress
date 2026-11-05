<?php
/**
 * Home page.
 *
 * @package Lawyers_Foundation_Theme
 */

get_header();
$lft_img = get_theme_mod( 'lft_hero_image' );
?>
<section class="hero"<?php echo $lft_img ? ' style="--hero-img:url(' . esc_url( $lft_img ) . ')"' : ''; ?>>
	<div class="wrap">
		<p class="eyebrow"><?php echo esc_html( lft_mod( 'lft_hero_eyebrow' ) ); ?></p>
		<h1><?php echo esc_html( lft_mod( 'lft_hero_title' ) ); ?></h1>
		<p><?php echo esc_html( lft_mod( 'lft_hero_text' ) ); ?></p>
		<div class="hero-actions">
			<a class="btn" href="<?php echo esc_url( lft_url( 'join' ) ); ?>"><?php esc_html_e( 'Become a member', 'lawyers-foundation-theme' ); ?></a>
			<a class="btn btn--line" href="<?php echo esc_url( lft_url( 'work' ) ); ?>"><?php esc_html_e( 'See our work', 'lawyers-foundation-theme' ); ?></a>
		</div>
	</div>
</section>

<?php if ( lft_has_plugin() ) : ?>
	<div class="wrap impact-band"><?php echo do_shortcode( '[lf_impact]' ); ?></div>

	<section class="section">
		<div class="wrap">
			<div class="section-head">
				<div>
					<p class="eyebrow"><?php esc_html_e( 'What we do', 'lawyers-foundation-theme' ); ?></p>
					<h2><?php esc_html_e( 'Good work, done together', 'lawyers-foundation-theme' ); ?></h2>
					<p><?php esc_html_e( 'Members choose the initiatives that match their expertise and log every pro bono hour, so we can show the difference the profession makes.', 'lawyers-foundation-theme' ); ?></p>
				</div>
				<a class="btn btn--dark btn--sm" href="<?php echo esc_url( lft_url( 'work' ) ); ?>"><?php esc_html_e( 'All initiatives', 'lawyers-foundation-theme' ); ?></a>
			</div>
			<?php echo do_shortcode( '[lf_initiatives limit="4"]' ); ?>
		</div>
	</section>

	<section class="section section--white">
		<div class="wrap">
			<div class="section-head">
				<div>
					<p class="eyebrow"><?php esc_html_e( 'Our year', 'lawyers-foundation-theme' ); ?></p>
					<h2>
						<?php
						$lft_count = count( lf_event_cycle() );
						/* translators: %s: number of events, e.g. "Two" */
						printf( esc_html__( '%s flagship gatherings every year', 'lawyers-foundation-theme' ), esc_html( ucfirst( 2 === $lft_count ? __( 'two', 'lawyers-foundation-theme' ) : ( 3 === $lft_count ? __( 'three', 'lawyers-foundation-theme' ) : number_format_i18n( $lft_count ) ) ) ) );
						?>
					</h2>
					<p><?php esc_html_e( 'Members meet to learn, plan the next season of pro bono work and celebrate those who have gone furthest for justice.', 'lawyers-foundation-theme' ); ?></p>
				</div>
				<a class="btn btn--dark btn--sm" href="<?php echo esc_url( lft_url( 'events' ) ); ?>"><?php esc_html_e( 'All events', 'lawyers-foundation-theme' ); ?></a>
			</div>
			<?php echo do_shortcode( '[lf_event_cycle]' ); ?>
		</div>
	</section>

	<section class="section section--ink">
		<div class="wrap">
			<div class="section-head"><div><p class="eyebrow"><?php esc_html_e( 'Membership', 'lawyers-foundation-theme' ); ?></p><h2><?php esc_html_e( 'Why lawyers join', 'lawyers-foundation-theme' ); ?></h2></div></div>
			<div class="benefits">
				<div class="benefit">
					<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 21s-7-4.35-7-10a4 4 0 0 1 7-2.65A4 4 0 0 1 19 11c0 5.65-7 10-7 10z"/></svg>
					<h3><?php esc_html_e( 'Meaningful pro bono work', 'lawyers-foundation-theme' ); ?></h3>
					<p><?php esc_html_e( 'Matched to your practice area, with the foundation handling intake and logistics.', 'lawyers-foundation-theme' ); ?></p>
				</div>
				<div class="benefit">
					<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="9" cy="8" r="3.5"/><path d="M2.5 20a6.5 6.5 0 0 1 13 0"/><path d="M16 4.5a3.5 3.5 0 0 1 0 7M21.5 20a6.5 6.5 0 0 0-4-6"/></svg>
					<h3><?php esc_html_e( 'A network of peers', 'lawyers-foundation-theme' ); ?></h3>
					<p><?php esc_html_e( 'A members-only directory and flagship events that connect you with colleagues across practice areas.', 'lawyers-foundation-theme' ); ?></p>
				</div>
				<div class="benefit">
					<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 19.5V5a2 2 0 0 1 2-2h12v16H6a2 2 0 0 0-2 2"/><path d="M8 7h6"/></svg>
					<h3><?php esc_html_e( 'Learning & recognition', 'lawyers-foundation-theme' ); ?></h3>
					<p><?php esc_html_e( 'CPD sessions at our summits, mentoring, and awards for outstanding service at our annual gala.', 'lawyers-foundation-theme' ); ?></p>
				</div>
			</div>
		</div>
	</section>

	<section class="section">
		<div class="wrap">
			<div class="section-head"><div><p class="eyebrow"><?php esc_html_e( 'Calendar', 'lawyers-foundation-theme' ); ?></p><h2><?php esc_html_e( 'Coming up', 'lawyers-foundation-theme' ); ?></h2></div></div>
			<?php echo do_shortcode( '[lf_events show="upcoming" limit="3"]' ); ?>
		</div>
	</section>
<?php endif; ?>

<?php
while ( is_page() && have_posts() ) :
	the_post();
	if ( '' !== trim( get_the_content() ) ) :
		?>
		<section class="section section--white"><div class="wrap entry-content"><?php the_content(); ?></div></section>
		<?php
	endif;
endwhile;
?>

<section class="section section--white cta-band">
	<div class="wrap">
		<h2><?php esc_html_e( 'Put your practice to work for justice', 'lawyers-foundation-theme' ); ?></h2>
		<p><?php esc_html_e( 'Membership is open to lawyers in good standing. Applications are reviewed by our membership committee.', 'lawyers-foundation-theme' ); ?></p>
		<a class="btn btn--dark" href="<?php echo esc_url( lft_url( 'join' ) ); ?>"><?php esc_html_e( 'Apply for membership', 'lawyers-foundation-theme' ); ?></a>
	</div>
</section>
<?php
get_footer();
