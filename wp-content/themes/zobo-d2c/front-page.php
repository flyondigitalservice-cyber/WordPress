<?php
/**
 * Front page: the Zobo launch journey for D2C founders.
 *
 * @package Zobo
 */

get_header();

$zobo_cta     = zobod2c_opt( 'calendar_url' ) ? zobod2c_opt( 'calendar_url' ) : '#contact';
$zobo_wa      = zobod2c_whatsapp_url();
$zobod2c_journey = zobod2c_journey();
$zobo_step    = 0;
$zobo_stages  = array_sum( array_map( 'count', wp_list_pluck( $zobod2c_journey, 'stages' ) ) );
$zobo_marquee = array(
	__( 'Idea', 'zobo-d2c' ),
	__( 'Logo', 'zobo-d2c' ),
	__( 'Trademark', 'zobo-d2c' ),
	__( 'Licences', 'zobo-d2c' ),
	__( 'Manufacturing', 'zobo-d2c' ),
	__( 'Lab testing', 'zobo-d2c' ),
	__( 'FSSAI · CDSCO · BIS', 'zobo-d2c' ),
	__( 'Vendor registration', 'zobo-d2c' ),
	__( 'Marketplaces', 'zobo-d2c' ),
	__( 'Website', 'zobo-d2c' ),
	__( 'Performance marketing', 'zobo-d2c' ),
	__( 'Influencers', 'zobo-d2c' ),
	__( 'OTT & TVC', 'zobo-d2c' ),
);
$zobo_status  = isset( $_GET['enquiry'] ) ? sanitize_key( wp_unslash( $_GET['enquiry'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
?>

<section class="hero">
	<div class="container hero-grid">
		<div class="hero-copy">
			<p class="eyebrow reveal"><span class="pulse" aria-hidden="true"></span><?php echo esc_html( zobod2c_opt( 'hero_eyebrow' ) ); ?></p>
			<h1 class="hero-title reveal"><?php echo esc_html( zobod2c_opt( 'hero_title' ) ); ?></h1>
			<p class="hero-text reveal"><?php echo esc_html( zobod2c_opt( 'hero_text' ) ); ?></p>
			<div class="hero-actions reveal">
				<a class="btn btn-primary btn-lg" href="<?php echo esc_url( $zobo_cta ); ?>">
					<?php esc_html_e( 'Start my brand', 'zobo-d2c' ); ?> <?php echo zobod2c_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				</a>
				<?php if ( $zobo_wa ) : ?>
					<a class="btn btn-ghost btn-lg" href="<?php echo esc_url( $zobo_wa ); ?>" target="_blank" rel="noopener">
						<?php echo zobod2c_icon( 'whatsapp' ); // phpcs:ignore WordPress.Security.EscapeOutput ?> <?php esc_html_e( 'WhatsApp us', 'zobo-d2c' ); ?>
					</a>
				<?php else : ?>
					<a class="btn btn-ghost btn-lg" href="#journey"><?php esc_html_e( 'See the journey', 'zobo-d2c' ); ?></a>
				<?php endif; ?>
			</div>
			<ul class="hero-proof reveal">
				<li><strong><?php echo esc_html( number_format_i18n( $zobo_stages ) ); ?></strong> <?php esc_html_e( 'launch stages', 'zobo-d2c' ); ?></li>
				<li><strong>1</strong> <?php esc_html_e( 'team & timeline', 'zobo-d2c' ); ?></li>
				<li><strong>100%</strong> <?php esc_html_e( 'yours: IP & accounts', 'zobo-d2c' ); ?></li>
			</ul>
		</div>

		<div class="hero-visual" aria-hidden="true">
			<div class="board">
				<div class="board-head">
					<span class="board-dots"><i></i><i></i><i></i></span>
					<span><?php esc_html_e( 'Launch board', 'zobo-d2c' ); ?></span>
				</div>
				<div class="board-brand">
					<div class="board-logo">B</div>
					<div>
						<div class="board-name"><?php esc_html_e( 'Your Brand', 'zobo-d2c' ); ?><sup>™</sup></div>
						<div class="board-sub"><?php esc_html_e( 'Clean snacks · D2C', 'zobo-d2c' ); ?></div>
					</div>
				</div>
				<ul class="board-steps">
					<li class="done"><span></span><?php esc_html_e( 'Logo & packaging', 'zobo-d2c' ); ?></li>
					<li class="done"><span></span><?php esc_html_e( 'Trademark filed', 'zobo-d2c' ); ?></li>
					<li class="done"><span></span><?php esc_html_e( 'GST · FSSAI · MSME', 'zobo-d2c' ); ?></li>
					<li class="done"><span></span><?php esc_html_e( 'Lab tested · shelf-life OK', 'zobo-d2c' ); ?></li>
					<li class="live"><span></span><?php esc_html_e( 'Live on marketplaces', 'zobo-d2c' ); ?></li>
					<li><span></span><?php esc_html_e( 'TVC shoot', 'zobo-d2c' ); ?></li>
				</ul>
			</div>
			<div class="float-card float-orders">
				<span class="float-label"><?php esc_html_e( 'Orders', 'zobo-d2c' ); ?></span>
				<svg viewBox="0 0 120 40" class="spark"><path d="M0 34 L15 30 L30 32 L45 22 L60 24 L75 14 L90 16 L105 6 L120 4" /></svg>
			</div>
			<div class="float-card float-tag">
				<?php echo zobod2c_icon( 'star' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<span><?php esc_html_e( 'Creator campaign live', 'zobo-d2c' ); ?></span>
			</div>
		</div>
	</div>

	<div class="marquee" aria-hidden="true">
		<div class="marquee-track">
			<?php for ( $zobo_i = 0; $zobo_i < 2; $zobo_i++ ) : ?>
				<?php foreach ( $zobo_marquee as $zobo_word ) : ?>
					<span><?php echo esc_html( $zobo_word ); ?></span><span class="marquee-sep">✦</span>
				<?php endforeach; ?>
			<?php endfor; ?>
		</div>
	</div>
</section>

<section class="section problem">
	<div class="container">
		<div class="section-head reveal">
			<p class="kicker"><?php esc_html_e( 'The founder problem', 'zobo-d2c' ); ?></p>
			<h2 class="section-title"><?php esc_html_e( 'A dozen vendors. A dozen invoices. Nobody owns the launch.', 'zobo-d2c' ); ?></h2>
		</div>
		<div class="compare">
			<div class="compare-card compare-old reveal">
				<h3><?php esc_html_e( 'The usual way', 'zobo-d2c' ); ?></h3>
				<ul>
					<li><?php esc_html_e( 'A designer who never saw your factory quote', 'zobo-d2c' ); ?></li>
					<li><?php esc_html_e( 'A CA who files licences in the wrong order', 'zobo-d2c' ); ?></li>
					<li><?php esc_html_e( 'Claims on the pack that no lab report backs up', 'zobo-d2c' ); ?></li>
					<li><?php esc_html_e( 'Packaging that fails marketplace guidelines', 'zobo-d2c' ); ?></li>
					<li><?php esc_html_e( 'An ad agency that inherits a brand it did not build', 'zobo-d2c' ); ?></li>
					<li><?php esc_html_e( 'Months lost chasing people on WhatsApp', 'zobo-d2c' ); ?></li>
				</ul>
			</div>
			<div class="compare-card compare-new reveal">
				<h3><?php esc_html_e( 'The Zobo way', 'zobo-d2c' ); ?></h3>
				<ul>
					<li><?php esc_html_e( 'One team from napkin sketch to first ad', 'zobo-d2c' ); ?></li>
					<li><?php esc_html_e( 'One timeline, with every dependency mapped', 'zobo-d2c' ); ?></li>
					<li><?php esc_html_e( 'Brand built for the channels it will sell on', 'zobo-d2c' ); ?></li>
					<li><?php esc_html_e( 'Every claim tested and backed by NABL labs', 'zobo-d2c' ); ?></li>
					<li><?php esc_html_e( 'Marketing that knows your margins', 'zobo-d2c' ); ?></li>
					<li><?php esc_html_e( 'One point of contact who answers', 'zobo-d2c' ); ?></li>
				</ul>
			</div>
		</div>
	</div>
</section>

<section class="section journey" id="journey">
	<div class="container">
		<div class="section-head reveal">
			<p class="kicker"><?php esc_html_e( 'The journey', 'zobo-d2c' ); ?></p>
			<h2 class="section-title"><?php esc_html_e( 'Idea to ground marketing, in four phases.', 'zobo-d2c' ); ?></h2>
			<p class="section-lead"><?php esc_html_e( 'Start at any stage. We pick up where you are and take you the rest of the way.', 'zobo-d2c' ); ?></p>
		</div>

		<div class="phases">
			<?php foreach ( $zobod2c_journey as $zobo_phase_index => $zobo_phase ) : ?>
				<div class="phase reveal">
					<div class="phase-head">
						<span class="phase-num"><?php echo esc_html( sprintf( '0%d', $zobo_phase_index + 1 ) ); ?></span>
						<h3 class="phase-title"><?php echo esc_html( $zobo_phase['phase'] ); ?></h3>
						<span class="phase-weeks"><?php echo esc_html( $zobo_phase['weeks'] ); ?></span>
					</div>
					<ol class="stages" start="<?php echo esc_attr( $zobo_step + 1 ); ?>">
						<?php foreach ( $zobo_phase['stages'] as $zobo_stage ) : ?>
							<?php $zobo_step++; ?>
							<li class="stage">
								<span class="stage-icon"><?php echo zobod2c_icon( $zobo_stage['icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
								<div>
									<h4 class="stage-title"><span class="stage-no"><?php echo esc_html( str_pad( (string) $zobo_step, 2, '0', STR_PAD_LEFT ) ); ?></span><?php echo esc_html( $zobo_stage['title'] ); ?></h4>
									<p><?php echo esc_html( $zobo_stage['text'] ); ?></p>
								</div>
							</li>
						<?php endforeach; ?>
					</ol>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<section class="section services" id="services">
	<div class="container">
		<div class="section-head reveal">
			<p class="kicker"><?php esc_html_e( 'Services', 'zobo-d2c' ); ?></p>
			<h2 class="section-title"><?php esc_html_e( 'Everything a D2C brand needs, under one roof.', 'zobo-d2c' ); ?></h2>
		</div>
		<div class="bento">
			<?php foreach ( zobod2c_services() as $zobo_service ) : ?>
				<article class="bento-card reveal <?php echo esc_attr( trim( 'tone-' . $zobo_service['tone'] . ' ' . ( $zobo_service['size'] ? 'is-' . $zobo_service['size'] : '' ) ) ); ?>">
					<span class="chip"><?php echo esc_html( $zobo_service['tag'] ); ?></span>
					<h3><?php echo esc_html( $zobo_service['title'] ); ?></h3>
					<ul class="pills">
						<?php foreach ( $zobo_service['items'] as $zobo_item ) : ?>
							<li><?php echo esc_html( $zobo_item ); ?></li>
						<?php endforeach; ?>
					</ul>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<section class="section industries" id="industries">
	<div class="container">
		<div class="section-head reveal">
			<p class="kicker"><?php esc_html_e( 'Industries', 'zobo-d2c' ); ?></p>
			<h2 class="section-title"><?php esc_html_e( 'Regulated categories, decoded.', 'zobo-d2c' ); ?></h2>
			<p class="section-lead"><?php esc_html_e( 'Every category has its own licences, lab tests and buyer psychology. We know the rules before you pay to learn them.', 'zobo-d2c' ); ?></p>
		</div>
		<div class="industry-grid">
			<?php foreach ( zobod2c_industries() as $zobo_ind ) : ?>
				<div class="industry-card reveal">
					<h3><?php echo esc_html( $zobo_ind['title'] ); ?></h3>
					<p><?php echo esc_html( $zobo_ind['text'] ); ?></p>
					<ul class="pills">
						<?php foreach ( $zobo_ind['tags'] as $zobo_tag ) : ?>
							<li><?php echo esc_html( $zobo_tag ); ?></li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<section class="section audience">
	<div class="container">
		<div class="section-head reveal">
			<p class="kicker"><?php esc_html_e( 'Who we build with', 'zobo-d2c' ); ?></p>
			<h2 class="section-title"><?php esc_html_e( 'For founders who want to build something that changes things.', 'zobo-d2c' ); ?></h2>
		</div>
		<div class="audience-grid">
			<?php foreach ( zobod2c_audiences() as $zobo_i => $zobo_aud ) : ?>
				<div class="audience-card reveal">
					<span class="audience-num"><?php echo esc_html( sprintf( '0%d', $zobo_i + 1 ) ); ?></span>
					<h3><?php echo esc_html( $zobo_aud['title'] ); ?></h3>
					<p><?php echo esc_html( $zobo_aud['text'] ); ?></p>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<section class="section plans" id="plans">
	<div class="container">
		<div class="section-head reveal">
			<p class="kicker"><?php esc_html_e( 'Ways to work together', 'zobo-d2c' ); ?></p>
			<h2 class="section-title"><?php esc_html_e( 'Pick your starting line.', 'zobo-d2c' ); ?></h2>
			<p class="section-lead"><?php esc_html_e( 'Every plan is scoped to your category. Tell us where you are and we will send a fixed proposal.', 'zobo-d2c' ); ?></p>
		</div>
		<div class="plan-grid">
			<?php foreach ( zobod2c_plans() as $zobo_plan ) : ?>
				<div class="plan reveal<?php echo $zobo_plan['featured'] ? ' is-featured' : ''; ?>">
					<?php if ( $zobo_plan['featured'] ) : ?>
						<span class="plan-badge"><?php esc_html_e( 'Most founders start here', 'zobo-d2c' ); ?></span>
					<?php endif; ?>
					<h3 class="plan-name"><?php echo esc_html( $zobo_plan['name'] ); ?></h3>
					<p class="plan-for"><?php echo esc_html( $zobo_plan['for'] ); ?></p>
					<ul class="plan-list">
						<?php foreach ( $zobo_plan['items'] as $zobo_item ) : ?>
							<li><?php echo esc_html( $zobo_item ); ?></li>
						<?php endforeach; ?>
					</ul>
					<a class="btn <?php echo $zobo_plan['featured'] ? 'btn-lime' : 'btn-outline'; ?>" href="<?php echo esc_url( $zobo_cta ); ?>"><?php esc_html_e( 'Get a proposal', 'zobo-d2c' ); ?></a>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<section class="section work" id="work">
	<div class="container">
		<div class="section-head section-head-row reveal">
			<div>
				<p class="kicker"><?php esc_html_e( 'Work', 'zobo-d2c' ); ?></p>
				<h2 class="section-title"><?php esc_html_e( 'Brands we’ve worked with.', 'zobo-d2c' ); ?></h2>
			</div>
			<?php if ( zobod2c_opt( 'behance' ) ) : ?>
				<a class="btn btn-outline" href="<?php echo esc_url( zobod2c_opt( 'behance' ) ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Full portfolio on Behance', 'zobo-d2c' ); ?> <?php echo zobod2c_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
			<?php endif; ?>
		</div>

		<?php
		$zobo_work = new WP_Query(
			array(
				'post_type'           => 'zobo_work',
				'posts_per_page'      => 6,
				'ignore_sticky_posts' => true,
				'no_found_rows'       => true,
			)
		);
		?>
		<?php if ( $zobo_work->have_posts() ) : ?>
			<div class="work-grid">
				<?php
				while ( $zobo_work->have_posts() ) :
					$zobo_work->the_post();
					?>
					<a class="work-card reveal" href="<?php the_permalink(); ?>">
						<div class="work-media">
							<?php if ( has_post_thumbnail() ) : ?>
								<?php the_post_thumbnail( 'zobo-work', array( 'loading' => 'lazy' ) ); ?>
							<?php else : ?>
								<span class="work-placeholder"><?php echo esc_html( mb_substr( get_the_title(), 0, 1 ) ); ?></span>
							<?php endif; ?>
						</div>
						<h3><?php the_title(); ?></h3>
						<?php if ( has_excerpt() ) : ?>
							<p><?php echo esc_html( get_the_excerpt() ); ?></p>
						<?php endif; ?>
					</a>
				<?php endwhile; ?>
			</div>
			<?php wp_reset_postdata(); ?>
		<?php else : ?>
			<div class="client-grid">
				<?php foreach ( zobod2c_clients() as $zobo_client ) : ?>
					<a class="client-card reveal" href="<?php echo esc_url( $zobo_client['url'] ); ?>" target="_blank" rel="noopener">
						<span class="chip"><?php echo esc_html( $zobo_client['cat'] ); ?></span>
						<h3><?php echo esc_html( $zobo_client['name'] ); ?></h3>
						<p><?php echo esc_html( $zobo_client['text'] ); ?></p>
						<span class="client-link"><?php esc_html_e( 'Visit brand', 'zobo-d2c' ); ?> <?php echo zobod2c_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
					</a>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section>

<section class="section faq" id="faq">
	<div class="container faq-grid">
		<div class="section-head reveal">
			<p class="kicker"><?php esc_html_e( 'FAQ', 'zobo-d2c' ); ?></p>
			<h2 class="section-title"><?php esc_html_e( 'Questions founders ask us.', 'zobo-d2c' ); ?></h2>
		</div>
		<div class="faq-list">
			<?php foreach ( zobod2c_faqs() as $zobo_faq ) : ?>
				<details class="faq-item reveal">
					<summary><?php echo esc_html( $zobo_faq['q'] ); ?><span class="faq-plus"><?php echo zobod2c_icon( 'plus' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span></summary>
					<p><?php echo esc_html( $zobo_faq['a'] ); ?></p>
				</details>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<section class="section contact" id="contact">
	<div class="container contact-grid">
		<div class="contact-copy reveal">
			<p class="kicker"><?php esc_html_e( 'Let’s build it', 'zobo-d2c' ); ?></p>
			<h2 class="contact-title"><?php esc_html_e( 'Got an idea? Let’s put it on shelves.', 'zobo-d2c' ); ?></h2>
			<p><?php esc_html_e( 'Tell us what you want to build. We reply within one working day with next steps, a rough timeline and the licences your category needs.', 'zobo-d2c' ); ?></p>
			<ul class="contact-list">
				<?php if ( zobod2c_opt( 'email' ) ) : ?>
					<li><?php echo zobod2c_icon( 'mail' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><a href="mailto:<?php echo esc_attr( antispambot( zobod2c_opt( 'email' ) ) ); ?>"><?php echo esc_html( antispambot( zobod2c_opt( 'email' ) ) ); ?></a></li>
				<?php endif; ?>
				<?php if ( zobod2c_opt( 'phone' ) ) : ?>
					<li><?php echo zobod2c_icon( 'phone' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><a href="tel:<?php echo esc_attr( preg_replace( '/[^\d+]/', '', zobod2c_opt( 'phone' ) ) ); ?>"><?php echo esc_html( zobod2c_opt( 'phone' ) ); ?></a></li>
				<?php endif; ?>
				<?php if ( $zobo_wa ) : ?>
					<li><?php echo zobod2c_icon( 'whatsapp' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><a href="<?php echo esc_url( $zobo_wa ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Chat on WhatsApp', 'zobo-d2c' ); ?></a></li>
				<?php endif; ?>
				<?php if ( zobod2c_opt( 'address' ) ) : ?>
					<li><?php echo zobod2c_icon( 'pin' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo esc_html( zobod2c_opt( 'address' ) ); ?></span></li>
				<?php endif; ?>
				<?php if ( zobod2c_opt( 'hours' ) ) : ?>
					<li><?php echo zobod2c_icon( 'clock' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo esc_html( zobod2c_opt( 'hours' ) ); ?></span></li>
				<?php endif; ?>
			</ul>
		</div>

		<form class="contact-form reveal" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<?php if ( 'sent' === $zobo_status ) : ?>
				<p class="form-note is-success" role="status"><?php esc_html_e( 'Thanks! Your enquiry is in. We will be in touch within one working day.', 'zobo-d2c' ); ?></p>
			<?php elseif ( 'invalid' === $zobo_status ) : ?>
				<p class="form-note is-error" role="alert"><?php esc_html_e( 'Please add your name and a valid email.', 'zobo-d2c' ); ?></p>
			<?php elseif ( 'error' === $zobo_status ) : ?>
				<p class="form-note is-error" role="alert"><?php esc_html_e( 'Something went wrong sending your message. Please email or WhatsApp us instead.', 'zobo-d2c' ); ?></p>
			<?php endif; ?>

			<input type="hidden" name="action" value="zobo_enquiry">
			<?php wp_nonce_field( 'zobo_enquiry', 'zobo_nonce' ); ?>
			<div class="hp" aria-hidden="true">
				<label for="zobo_company_site"><?php esc_html_e( 'Leave empty', 'zobo-d2c' ); ?></label>
				<input type="text" id="zobo_company_site" name="zobo_company_site" tabindex="-1" autocomplete="off">
			</div>

			<div class="field-row">
				<div class="field">
					<label for="zobo-name"><?php esc_html_e( 'Your name', 'zobo-d2c' ); ?></label>
					<input id="zobo-name" name="name" type="text" autocomplete="name" required>
				</div>
				<div class="field">
					<label for="zobo-phone"><?php esc_html_e( 'Phone / WhatsApp', 'zobo-d2c' ); ?></label>
					<input id="zobo-phone" name="phone" type="tel" autocomplete="tel">
				</div>
			</div>
			<div class="field-row">
				<div class="field">
					<label for="zobo-email"><?php esc_html_e( 'Email', 'zobo-d2c' ); ?></label>
					<input id="zobo-email" name="email" type="email" autocomplete="email" required>
				</div>
				<div class="field">
					<label for="zobo-category"><?php esc_html_e( 'Category', 'zobo-d2c' ); ?></label>
					<select id="zobo-category" name="category">
						<option value=""><?php esc_html_e( 'Select…', 'zobo-d2c' ); ?></option>
						<?php foreach ( zobod2c_form_categories() as $zobo_cat ) : ?>
							<option value="<?php echo esc_attr( $zobo_cat ); ?>"><?php echo esc_html( $zobo_cat ); ?></option>
						<?php endforeach; ?>
					</select>
				</div>
			</div>
			<fieldset class="field">
				<legend><?php esc_html_e( 'Where are you today?', 'zobo-d2c' ); ?></legend>
				<div class="radio-pills">
					<?php foreach ( zobod2c_form_stages() as $zobo_key => $zobo_label ) : ?>
						<label><input type="radio" name="stage" value="<?php echo esc_attr( $zobo_key ); ?>" <?php checked( 'idea', $zobo_key ); ?>><span><?php echo esc_html( $zobo_label ); ?></span></label>
					<?php endforeach; ?>
				</div>
			</fieldset>
			<div class="field">
				<label for="zobo-message"><?php esc_html_e( 'Tell us about your idea', 'zobo-d2c' ); ?></label>
				<textarea id="zobo-message" name="message" rows="4" placeholder="<?php esc_attr_e( 'Product, category, target customer, launch date…', 'zobo-d2c' ); ?>"></textarea>
			</div>
			<button class="btn btn-lime btn-lg btn-block" type="submit"><?php esc_html_e( 'Send my idea', 'zobo-d2c' ); ?> <?php echo zobod2c_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
		</form>
	</div>
</section>

<?php
get_footer();
