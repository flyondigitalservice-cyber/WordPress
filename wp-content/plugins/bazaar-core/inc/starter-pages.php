<?php
/**
 * Creates starter pages (About, Contact, policies...) from the active theme's patterns.
 *
 * A theme declares pages with the `bzr_starter_pages` filter:
 *   [ 'about-us' => [ 'title' => 'About us', 'pattern' => 'bazaar-store/page-about' ], ... ]
 * Existing pages with the same slug are never touched, so running it twice is safe.
 *
 * Runs when the theme is switched, when this plugin is activated, and from the
 * "Create missing pages" button in Store Options.
 *
 * @package BazaarCore
 */

defined( 'ABSPATH' ) || exit;

/**
 * Create missing pages from patterns.
 *
 * @return int Number of pages created.
 */
function bzr_create_starter_pages() {
	$pages = apply_filters( 'bzr_starter_pages', array() );
	if ( empty( $pages ) || ! class_exists( 'WP_Block_Patterns_Registry' ) ) {
		return 0;
	}

	$registry = WP_Block_Patterns_Registry::get_instance();
	$created  = 0;

	foreach ( $pages as $slug => $page ) {
		if ( get_page_by_path( $slug, OBJECT, 'page' ) ) {
			continue;
		}
		$pattern = $registry->get_registered( $page['pattern'] );
		if ( ! $pattern ) {
			continue;
		}

		// File-based patterns may load their content lazily.
		$content = isset( $pattern['content'] ) ? $pattern['content'] : '';
		if ( '' === $content && ! empty( $pattern['filePath'] ) && file_exists( $pattern['filePath'] ) ) {
			ob_start();
			include $pattern['filePath'];
			$content = (string) ob_get_clean();
		}
		if ( '' === $content ) {
			continue;
		}

		$id = wp_insert_post(
			array(
				'post_type'    => 'page',
				'post_status'  => 'publish',
				'post_title'   => $page['title'],
				'post_name'    => $slug,
				'post_content' => $content,
			)
		);
		if ( $id && ! is_wp_error( $id ) ) {
			++$created;
		}
	}

	return $created;
}
add_action( 'after_switch_theme', 'bzr_create_starter_pages' );

/**
 * Deferred run after plugin activation (patterns are registered on init).
 */
function bzr_maybe_create_starter_pages() {
	if ( ! get_option( 'bzr_create_starter_pages' ) || ! current_user_can( 'manage_options' ) ) {
		return;
	}
	delete_option( 'bzr_create_starter_pages' );
	bzr_create_starter_pages();
}
add_action( 'admin_init', 'bzr_maybe_create_starter_pages' );

/**
 * Tool button in Store Options.
 */
function bzr_handle_starter_pages() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'Sorry, you are not allowed to do that.', 'bazaar-core' ), 403 );
	}
	check_admin_referer( 'bzr_starter_pages' );

	$count = bzr_create_starter_pages();
	wp_safe_redirect(
		add_query_arg(
			array(
				'bzr_notice' => 'pages',
				'bzr_count'  => $count,
			),
			admin_url( 'themes.php?page=bzr-options' )
		)
	);
	exit;
}
add_action( 'admin_post_bzr_starter_pages', 'bzr_handle_starter_pages' );
