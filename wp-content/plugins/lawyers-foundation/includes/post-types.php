<?php
/**
 * Custom post types: events, initiatives, service-hour logs.
 *
 * @package Lawyers_Foundation
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register post types.
 */
function lf_register_post_types() {
	register_post_type(
		'lf_event',
		array(
			'labels'       => array(
				'name'          => __( 'Events', 'lawyers-foundation' ),
				'singular_name' => __( 'Event', 'lawyers-foundation' ),
				'add_new_item'  => __( 'Add New Event', 'lawyers-foundation' ),
				'edit_item'     => __( 'Edit Event', 'lawyers-foundation' ),
				'all_items'     => __( 'Events', 'lawyers-foundation' ),
			),
			'public'       => true,
			'has_archive'  => 'foundation-events',
			'rewrite'      => array( 'slug' => 'event' ),
			'menu_icon'    => 'dashicons-calendar-alt',
			'show_in_menu' => 'lf-foundation',
			'show_in_rest' => true,
			'supports'     => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
		)
	);

	register_post_type(
		'lf_initiative',
		array(
			'labels'       => array(
				'name'          => __( 'Initiatives', 'lawyers-foundation' ),
				'singular_name' => __( 'Initiative', 'lawyers-foundation' ),
				'add_new_item'  => __( 'Add New Initiative', 'lawyers-foundation' ),
				'edit_item'     => __( 'Edit Initiative', 'lawyers-foundation' ),
				'all_items'     => __( 'Initiatives', 'lawyers-foundation' ),
			),
			'public'       => true,
			'has_archive'  => 'initiatives',
			'rewrite'      => array( 'slug' => 'initiative' ),
			'show_in_menu' => 'lf-foundation',
			'show_in_rest' => true,
			'supports'     => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
		)
	);

	register_post_type(
		'lf_service',
		array(
			'labels'       => array(
				'name'          => __( 'Service Hours', 'lawyers-foundation' ),
				'singular_name' => __( 'Service Log', 'lawyers-foundation' ),
				'edit_item'     => __( 'Review Service Log', 'lawyers-foundation' ),
				'all_items'     => __( 'Service Hours', 'lawyers-foundation' ),
			),
			'public'       => false,
			'show_ui'      => true,
			'show_in_menu' => 'lf-foundation',
			'supports'     => array( 'title', 'editor', 'author' ),
			'capabilities' => array( 'create_posts' => 'do_not_allow' ),
			'map_meta_cap' => true,
		)
	);
}
add_action( 'init', 'lf_register_post_types' );
