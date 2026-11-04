<?php
/**
 * Contact page (slug: contact).
 */
get_header();
get_template_part( 'template-parts/page-hero', null, array(
	'title' => 'Contact us',
	'lead'  => 'Call, WhatsApp or send an enquiry. We reply quickly during working hours.',
) );
get_template_part( 'template-parts/contact' );
get_template_part( 'template-parts/faq' );
get_footer();
