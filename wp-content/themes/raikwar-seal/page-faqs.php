<?php
/**
 * FAQ page (slug: faqs).
 */
get_header();
get_template_part( 'template-parts/page-hero', null, array(
	'title' => 'Frequently asked questions',
	'lead'  => 'Quick answers about Digital Signatures, registrations, documents and fees.',
) );
get_template_part( 'template-parts/faq' );
get_template_part( 'template-parts/cta' );
get_footer();
