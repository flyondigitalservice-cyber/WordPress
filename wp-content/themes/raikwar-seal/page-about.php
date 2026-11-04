<?php
/**
 * About page (slug: about) – curated from the existing About text.
 */
get_header();
get_template_part( 'template-parts/page-hero', null, array(
	'title' => 'About Raikwar DSC',
	'lead'  => 'A trusted Digital Signature and government licence consultant in Mumbai, making registrations easy, fast and stress-free.',
) );
?>
<section class="section">
	<div class="wrap why">
		<div>
			<div class="label">Who we are</div>
			<h2>Reliable, transparent compliance help for Mumbai businesses.</h2>
		</div>
		<div class="entry">
			<p>Raikwar DSC is a professional Digital Signature Certificate (DSC) and government registration service provider based in Mumbai. We help individuals, startups, businesses, contractors and exporters complete their legal registrations and compliance processes smoothly and efficiently.</p>
			<p>With years of practical experience with documentation and government portals, we simplify complex procedures and make sure every submission is accurate and on time.</p>
			<p><strong>Our goal is simple: make government registrations easy, fast and stress-free for our clients.</strong></p>
		</div>
	</div>
</section>

<section class="section section--deep">
	<div class="wrap">
		<div class="section__head">
			<div>
				<div class="label">The problem we solve</div>
				<h2>Government paperwork shouldn’t slow your business down.</h2>
			</div>
			<p>Many business owners struggle with the same things. That’s where we step in, with structured support, document verification and step-by-step help.</p>
		</div>
		<ul class="chips">
			<li>Complicated government portals</li>
			<li>Documentation errors</li>
			<li>Application rejections</li>
			<li>Delays in approvals</li>
			<li>Lack of proper guidance</li>
		</ul>
	</div>
</section>

<section class="section section--white">
	<div class="wrap">
		<div class="section__head">
			<div>
				<div class="label">What we do</div>
				<h2>End-to-end assistance, from documents to approval.</h2>
			</div>
		</div>
		<div class="steps" style="grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:40px">
			<div><h3 style="font-size:26px">Digital Signatures</h3><ul class="entry" style="font-size:16px"><li>Class 3 DSC for individuals</li><li>Company / organisation DSC</li><li>Tender DSC (sign + encrypt)</li><li>DGFT &amp; ICEGATE DSC</li><li>USB token &amp; DSC renewal</li></ul></div>
			<div><h3 style="font-size:26px">Registrations</h3><ul class="entry" style="font-size:16px"><li>GST registration</li><li>MSME / Udyam</li><li>Shop Act (Gumasta)</li><li>Trade licence &amp; BMC</li><li>Company, LLP &amp; Startup India</li></ul></div>
			<div><h3 style="font-size:26px">Licences &amp; Export</h3><ul class="entry" style="font-size:16px"><li>Import Export Code (IEC)</li><li>FSSAI &amp; FDA</li><li>Drug licence</li><li>APEDA &amp; Spice Board</li><li>Trademark &amp; copyright</li></ul></div>
		</div>
	</div>
</section>

<?php
get_template_part( 'template-parts/why' );
get_template_part( 'template-parts/cta' );
get_footer();
