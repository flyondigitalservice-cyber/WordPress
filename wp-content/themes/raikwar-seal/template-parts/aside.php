<aside class="aside-card">
	<div class="label label--light">Need this done?</div>
	<h3>Get a free quote in minutes.</h3>
	<p>Tell us what you need. We share the document checklist and the exact fee.</p>
	<a class="btn btn--primary" href="<?php echo esc_url( raikwar_wa( 'Hello Raikwar DSC, I need help with ' . wp_strip_all_tags( get_the_title() ) . '.' ) ); ?>" target="_blank" rel="noopener"><?php echo raikwar_icon( 'wa' ); ?>WhatsApp us</a>
	<a class="btn btn--ghost" style="color:var(--paper)" href="<?php echo esc_attr( raikwar_tel( raikwar_info( 'phone1' ) ) ); ?>"><?php echo raikwar_icon( 'phone' ); ?><?php echo esc_html( raikwar_phone_label( raikwar_info( 'phone1' ) ) ); ?></a>
	<ul>
		<li><?php echo raikwar_icon( 'check' ); ?>100% online processing</li>
		<li><?php echo raikwar_icon( 'check' ); ?>Transparent, upfront fees</li>
		<li><?php echo raikwar_icon( 'check' ); ?>Dedicated support executive</li>
	</ul>
</aside>
