<?php
/**
 * Front page.
 */
get_header();

$areas = array( 'South Mumbai', 'Andheri', 'Borivali', 'Malad', 'Kandivali', 'Thane', 'Navi Mumbai', 'Vashi', 'Nerul', 'Kharghar', 'Dombivali', 'Mira Road', 'Virar', 'Pan-India online' );
?>

<section class="hero grain">
	<div class="wrap">
		<div>
			<div class="label rise">Mumbai · Govt. Registration Desk</div>
			<h1 class="rise rise-2">Licenses &amp; Digital Signatures, <em>done right</em>, without the office runs.</h1>
			<p class="hero__lead rise rise-3">Same-day Class 3 DSC, GST, FSSAI, Trademark, IEC, Shop Act and 25+ government registrations. Fast, error-free and 100% online for businesses across Mumbai and India.</p>
			<div class="hero__actions rise rise-4">
				<a class="btn btn--primary" href="<?php echo esc_url( raikwar_wa() ); ?>" target="_blank" rel="noopener"><?php echo raikwar_icon( 'wa' ); ?>Get a free quote on WhatsApp</a>
				<a class="btn btn--ghost" href="<?php echo esc_attr( raikwar_tel( raikwar_info( 'phone1' ) ) ); ?>"><?php echo raikwar_icon( 'phone' ); ?>Call <?php echo esc_html( raikwar_phone_label( raikwar_info( 'phone1' ) ) ); ?></a>
			</div>
			<ul class="hero__checks rise rise-5">
				<li><?php echo raikwar_icon( 'check' ); ?>Same-day DSC*</li>
				<li><?php echo raikwar_icon( 'check' ); ?>Transparent fees</li>
				<li><?php echo raikwar_icon( 'check' ); ?>Dedicated executive</li>
			</ul>
		</div>

		<div class="cert rise rise-3" aria-label="Sample application status">
			<div class="cert__head"><span>Application Register</span><span>Form No. RD-01</span></div>
			<div class="cert__title">Your file, handled.</div>
			<div style="color:var(--ink-3);font-size:15px">We track every step until approval.</div>
			<ul class="cert__rows">
				<li><b>Class 3 DSC</b><span class="ok">ISSUED · SAME DAY</span></li>
				<li><b>GST Registration</b><span class="ok">GSTIN APPROVED</span></li>
				<li><b>FSSAI License</b><span class="ok">FILED</span></li>
				<li><b>Trademark</b><span class="ok">TM APPLIED</span></li>
				<li><b>Import Export Code</b><span class="ok">IEC ISSUED</span></li>
			</ul>
			<div class="stamp" aria-hidden="true"><div><b><?php echo esc_html( raikwar_info( 'years' ) ); ?></b>Years<br>Trusted</div></div>
		</div>
	</div>
</section>

<div class="ticker" aria-label="Areas we serve">
	<div class="ticker__track">
		<?php for ( $i = 0; $i < 2; $i++ ) : foreach ( $areas as $a ) : ?>
			<span<?php echo $i ? ' aria-hidden="true"' : ''; ?>><?php echo esc_html( $a ); ?></span>
		<?php endforeach; endfor; ?>
	</div>
</div>

<?php
get_template_part( 'template-parts/services' );
get_template_part( 'template-parts/steps' );
get_template_part( 'template-parts/why' );
?>

<section class="section section--deep">
	<div class="wrap">
		<div class="section__head">
			<div>
				<div class="label">Who we serve</div>
				<h2>Built for people who sign, file &amp; bid.</h2>
			</div>
			<p>From first-time founders to CA firms filing for hundreds of clients, we work with businesses of every size.</p>
		</div>
		<ul class="chips">
			<?php foreach ( array( 'E-commerce sellers', 'Startups', 'Private limited companies', 'Proprietorship firms', 'LLPs & partnerships', 'Exporters & importers', 'Contractors & tender bidders', 'Chartered accountants', 'Tax consultants', 'Restaurants & food businesses', 'Pharmacies', 'Manufacturers' ) as $c ) : ?>
				<li><?php echo esc_html( $c ); ?></li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>

<?php
get_template_part( 'template-parts/faq' );
get_template_part( 'template-parts/cta' );
get_template_part( 'template-parts/contact' );
get_footer();
