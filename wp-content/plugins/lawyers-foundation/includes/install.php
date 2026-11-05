<?php
/**
 * Activation: roles, pages and starter content.
 *
 * @package Lawyers_Foundation
 */

defined( 'ABSPATH' ) || exit;

/**
 * Plugin activation.
 */
function lf_activate() {
	add_role( 'lf_member', __( 'Foundation Member', 'lawyers-foundation' ), array( 'read' => true ) );
	add_role( 'lf_pending', __( 'Pending Member', 'lawyers-foundation' ), array( 'read' => true ) );

	lf_register_post_types();
	lf_create_pages();
	lf_seed_content();

	if ( false === get_option( 'lf_settings' ) ) {
		add_option( 'lf_settings', lf_default_settings() );
	}
	flush_rewrite_rules();
}

/**
 * Plugin deactivation (roles and content are kept).
 */
function lf_deactivate() {
	flush_rewrite_rules();
}

/**
 * Create the plugin's pages once.
 */
function lf_create_pages() {
	$pages   = (array) get_option( 'lf_pages', array() );
	$defined = array(
		'join'      => array( __( 'Become a Member', 'lawyers-foundation' ), "<!-- wp:paragraph -->\n<p>Membership is open to qualified lawyers in good standing who want to put their skills to work for people who could not otherwise afford legal help. Every application is reviewed by our membership committee.</p>\n<!-- /wp:paragraph -->\n\n<!-- wp:shortcode -->\n[lf_register]\n<!-- /wp:shortcode -->" ),
		'dashboard' => array( __( 'Member Dashboard', 'lawyers-foundation' ), "<!-- wp:shortcode -->\n[lf_dashboard]\n<!-- /wp:shortcode -->" ),
		'directory' => array( __( 'Member Directory', 'lawyers-foundation' ), "<!-- wp:shortcode -->\n[lf_directory]\n<!-- /wp:shortcode -->" ),
		'events'    => array( __( 'Events', 'lawyers-foundation' ), "<!-- wp:paragraph -->\n<p>The foundation brings its members together for flagship events through the year: a chance to learn, to plan our pro bono work and to celebrate what we have achieved together.</p>\n<!-- /wp:paragraph -->\n\n<!-- wp:heading -->\n<h2>This year's flagship events</h2>\n<!-- /wp:heading -->\n\n<!-- wp:shortcode -->\n[lf_event_cycle]\n<!-- /wp:shortcode -->\n\n<!-- wp:heading -->\n<h2>Upcoming</h2>\n<!-- /wp:heading -->\n\n<!-- wp:shortcode -->\n[lf_events show=\"upcoming\" limit=\"12\"]\n<!-- /wp:shortcode -->\n\n<!-- wp:heading -->\n<h2>Past events</h2>\n<!-- /wp:heading -->\n\n<!-- wp:shortcode -->\n[lf_events show=\"past\" limit=\"6\"]\n<!-- /wp:shortcode -->" ),
		'work'      => array( __( 'Our Work', 'lawyers-foundation' ), "<!-- wp:paragraph -->\n<p>Our members give their time and expertise through the foundation's initiatives — from free legal-aid clinics to rights-awareness programmes in schools and communities.</p>\n<!-- /wp:paragraph -->\n\n<!-- wp:shortcode -->\n[lf_impact]\n<!-- /wp:shortcode -->\n\n<!-- wp:shortcode -->\n[lf_initiatives limit=\"24\"]\n<!-- /wp:shortcode -->" ),
	);
	foreach ( $defined as $key => $page ) {
		if ( ! empty( $pages[ $key ] ) && get_post_status( $pages[ $key ] ) ) {
			continue;
		}
		$pages[ $key ] = wp_insert_post(
			array(
				'post_type'    => 'page',
				'post_status'  => 'publish',
				'post_title'   => $page[0],
				'post_content' => $page[1],
			)
		);
	}
	update_option( 'lf_pages', $pages );
}

/**
 * Starter initiatives and the two flagship events for next year, created once.
 */
function lf_seed_content() {
	if ( get_option( 'lf_seeded' ) ) {
		return;
	}
	update_option( 'lf_seeded', 1 );

	$initiatives = array(
		array( 'Free Legal Aid Clinics', 'Members staff walk-in clinics where anyone can get free advice on family, tenancy, employment and consumer problems, with follow-up representation for those who need it.' ),
		array( 'Know Your Rights', 'Volunteer lawyers run plain-language workshops in schools, colleges and community centres on constitutional rights, women\'s rights and how to approach the courts.' ),
		array( 'Undertrial Prisoner Support', 'Members review the cases of undertrial prisoners who cannot afford counsel and help secure bail and speedy hearings.' ),
		array( 'Young Lawyers Mentorship', 'Senior members mentor law students and lawyers in their first years of practice, with a focus on those from under-represented backgrounds.' ),
	);
	foreach ( $initiatives as $i => $item ) {
		wp_insert_post(
			array(
				'post_type'    => 'lf_initiative',
				'post_status'  => 'publish',
				'post_title'   => $item[0],
				'post_excerpt' => $item[1],
				'post_content' => "<!-- wp:paragraph -->\n<p>" . esc_html( $item[1] ) . "</p>\n<!-- /wp:paragraph -->",
				'menu_order'   => $i,
			)
		);
	}

	$year   = (int) current_time( 'Y' ) + 1;
	$events = array(
		array( 'Spring Summit', $year . '-04-18 10:00', $year . '-04-18 17:00', 'Pro Bono Spring Summit ' . $year, 'A day of CPD sessions, initiative planning and our annual members\' assembly.' ),
		array( 'Autumn Gala', $year . '-10-24 18:30', $year . '-10-24 22:00', 'Annual Justice Gala ' . $year, 'Our yearly fundraising gala and awards evening honouring members for outstanding pro bono service.' ),
	);
	foreach ( $events as $e ) {
		$id = wp_insert_post(
			array(
				'post_type'    => 'lf_event',
				'post_status'  => 'publish',
				'post_title'   => $e[3],
				'post_excerpt' => $e[4],
				'post_content' => "<!-- wp:paragraph -->\n<p>" . esc_html( $e[4] ) . " Venue and programme details will be shared with registered members.</p>\n<!-- /wp:paragraph -->",
			)
		);
		if ( $id && ! is_wp_error( $id ) ) {
			update_post_meta( $id, '_lf_cycle', $e[0] );
			update_post_meta( $id, '_lf_start', $e[1] );
			update_post_meta( $id, '_lf_end', $e[2] );
			update_post_meta( $id, '_lf_venue', 'To be announced' );
			update_post_meta( $id, '_lf_capacity', 200 );
			update_post_meta( $id, '_lf_rsvp_open', 1 );
			update_post_meta( $id, '_lf_cpd', 'Spring Summit' === $e[0] ? 1 : 0 );
		}
	}
}
