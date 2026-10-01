<?php
/**
 * Block patterns so editors can add new sections in the same style
 * (Block inserter → Patterns → Dubai Curtain Experts).
 *
 * @package dce-vibe
 */

defined( 'ABSPATH' ) || exit;

add_action( 'init', 'dce_register_patterns' );
function dce_register_patterns() {
	if ( ! function_exists( 'register_block_pattern' ) ) {
		return;
	}
	register_block_pattern_category( 'dce', array( 'label' => __( 'Dubai Curtain Experts', 'dce-vibe' ) ) );

	$patterns = array(
		'quote'   => array( __( 'Quote form + contact details (WhatsApp lead form)', 'dce-vibe' ), dce_sec_quote() ),
		'cta'     => array( __( 'Call-to-action band', 'dce-vibe' ), dce_sec_cta( 'Ready for <em>new curtains</em>?', 'Message us on WhatsApp with a photo of your window and we will book your free home visit.' ) ),
		'steps'   => array( __( 'How it works — 4 steps', 'dce-vibe' ), dce_sec_steps() ),
		'faq'     => array( __( 'FAQ (adds FAQ schema automatically)', 'dce-vibe' ), dce_sec_faq( array( array( 'Your question?', 'Your answer.' ), array( 'Another question?', 'Another answer.' ) ) ) ),
		'tiles'   => array( __( 'Feature tiles', 'dce-vibe' ), dce_sec_tiles( 'Kicker', 'Section <em>title</em>', 'Short introduction.', array( array( 'Feature one', 'Describe it.' ), array( 'Feature two', 'Describe it.' ), array( 'Feature three', 'Describe it.' ) ) ) ),
		'trust'   => array( __( 'Trust strip', 'dce-vibe' ), dce_sec_trust() ),
		'parent'  => array( __( 'Casa Vera Home — parent company', 'dce-vibe' ), dce_sec_parent() ),
		'catalog' => array(
			__( 'Catalogue PDF cards', 'dce-vibe' ),
			dce_b_section(
				dce_sec_head( 'Catalogues', 'Fabric <em>catalogues</em>', 'Paste a PDF or Google Drive link into each button.' )
				. dce_b_wide(
					dce_b_group( dce_b_h( 'Catalogue name', 3 ) . dce_b_p( 'Short description · PDF' ) . dce_b_buttons( array( array( 'View catalogue', '#', '', true ) ) ), array( 'class' => 'dce-cat-card' ) )
					. dce_b_group( dce_b_h( 'Catalogue name', 3 ) . dce_b_p( 'Short description · PDF' ) . dce_b_buttons( array( array( 'View catalogue', '#', '', true ) ) ), array( 'class' => 'dce-cat-card' ) ),
					'dce-catalogue'
				)
			),
		),
	);
	foreach ( $patterns as $slug => $p ) {
		register_block_pattern(
			'dce/' . $slug,
			array(
				'title'      => $p[0],
				'categories' => array( 'dce' ),
				'content'    => $p[1],
			)
		);
	}
}
