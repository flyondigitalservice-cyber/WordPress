<?php
/**
 * Services page (slug: services).
 */
get_header();
get_template_part( 'template-parts/page-hero', null, array(
	'title' => 'Services',
	'lead'  => 'Digital Signatures, business registrations, export codes, food & drug licences and brand protection, handled end-to-end by one team.',
) );
get_template_part( 'template-parts/services' );
get_template_part( 'template-parts/steps' );
get_template_part( 'template-parts/faq' );
get_template_part( 'template-parts/cta' );
get_footer();
