<?php
/**
 * Service register with category filters.
 */
$cats = array(
	'all'    => 'All services',
	'dsc'    => 'Digital Signature',
	'biz'    => 'Business & Govt.',
	'export' => 'Import / Export',
	'food'   => 'Food, Drug & Product',
	'ip'     => 'Brand & IP',
);
$heading_tag = is_front_page() ? 'h2' : 'h2';
?>
<section class="section section--white" id="services">
	<div class="wrap">
		<div class="section__head">
			<div>
				<div class="label">The Register · <?php echo count( raikwar_services() ); ?> services</div>
				<<?php echo $heading_tag; ?>>Every licence your business needs, under one roof.</<?php echo $heading_tag; ?>>
			</div>
			<p>Pick a service and we handle documents, filing and follow-ups with the department until your certificate is in your hands.</p>
		</div>

		<div class="filters" data-filters role="group" aria-label="Filter services">
			<?php foreach ( $cats as $k => $label ) : ?>
				<button type="button" data-filter="<?php echo esc_attr( $k ); ?>" aria-pressed="<?php echo 'all' === $k ? 'true' : 'false'; ?>"><?php echo esc_html( $label ); ?></button>
			<?php endforeach; ?>
		</div>

		<div class="register">
			<?php foreach ( raikwar_services() as $i => $s ) :
				$has_page = ! empty( $s[4] );
				$href     = $has_page ? $s[4] : raikwar_wa( 'Hello Raikwar DSC, I need help with ' . $s[0] . '.' );
				?>
				<a class="svc" data-cat="<?php echo esc_attr( $s[2] ); ?>" href="<?php echo esc_url( $href ); ?>"<?php echo $has_page ? '' : ' target="_blank" rel="noopener"'; ?>>
					<span class="svc__no"><span>No. <?php echo esc_html( str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span><span class="svc__code"><?php echo esc_html( $s[1] ); ?></span></span>
					<h3><?php echo esc_html( $s[0] ); ?></h3>
					<p><?php echo esc_html( $s[3] ); ?></p>
					<span class="svc__go"><?php echo $has_page ? 'Read more' : 'Enquire'; ?><?php echo raikwar_icon( 'arrow' ); ?></span>
				</a>
			<?php endforeach; ?>
			<a class="svc svc--cta" href="<?php echo esc_url( raikwar_wa( 'Hello Raikwar DSC, I need help with a registration that is not listed on your website.' ) ); ?>" target="_blank" rel="noopener">
				<span class="svc__no" style="color:rgba(255,255,255,.8)"><span>Not listed?</span></span>
				<h3>Don’t see your licence? We probably do it.</h3>
				<p>Tell us what you need on WhatsApp and get an answer within working hours.</p>
				<span class="svc__go">Ask us<?php echo raikwar_icon( 'arrow' ); ?></span>
			</a>
		</div>
	</div>
</section>
