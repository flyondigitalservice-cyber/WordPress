<?php
$faqs = raikwar_faqs();
?>
<section class="section section--white" id="faq">
	<div class="wrap">
		<div class="section__head">
			<div>
				<div class="label">Questions</div>
				<h2>Frequently asked.</h2>
			</div>
			<p>Can’t find your answer? Message us on WhatsApp and we’ll reply within working hours.</p>
		</div>
		<div class="faq">
			<?php foreach ( $faqs as $i => $f ) : ?>
				<details<?php echo 0 === $i ? ' open' : ''; ?>>
					<summary><?php echo esc_html( $f[0] ); ?></summary>
					<p><?php echo esc_html( $f[1] ); ?></p>
				</details>
			<?php endforeach; ?>
		</div>
		<p style="margin-top:28px;font-size:13px;color:var(--ink-3)">*Same-day DSC issuance is subject to successful document and video verification by the Certifying Authority.</p>
	</div>
</section>
<script type="application/ld+json"><?php
echo wp_json_encode( array(
	'@context'   => 'https://schema.org',
	'@type'      => 'FAQPage',
	'mainEntity' => array_map( function ( $f ) {
		return array( '@type' => 'Question', 'name' => $f[0], 'acceptedAnswer' => array( '@type' => 'Answer', 'text' => $f[1] ) );
	}, $faqs ),
), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
?></script>
