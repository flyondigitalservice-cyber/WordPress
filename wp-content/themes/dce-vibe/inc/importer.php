<?php
/**
 * Site setup / importer: Appearance → DCE Site Setup.
 * Imports bundled images into the Media Library, creates or updates every page
 * (keeping existing URLs), builds menus and sets the homepage. Safe to run again.
 *
 * WP-CLI: wp dce import [--no-overwrite] [--skip-menus] [--skip-reading]
 *
 * @package dce-vibe
 */

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'DCE_REMOTE_IMG' ) ) {
	define( 'DCE_REMOTE_IMG', 'https://raw.githubusercontent.com/flyondigitalservice-cyber/WordPress/claude/cool-bell-fgw7bv/wp-content/themes/dce-vibe/assets/img/' );
}

add_action( 'admin_menu', 'dce_importer_menu' );
function dce_importer_menu() {
	add_theme_page( __( 'DCE Site Setup', 'dce-vibe' ), __( 'DCE Site Setup', 'dce-vibe' ), 'manage_options', 'dce-setup', 'dce_importer_page' );
}

add_action( 'admin_notices', 'dce_importer_notice' );
function dce_importer_notice() {
	if ( ! current_user_can( 'manage_options' ) || get_option( 'dce_imported' ) || ( isset( $_GET['page'] ) && 'dce-setup' === $_GET['page'] ) ) { // phpcs:ignore
		return;
	}
	echo '<div class="notice notice-info"><p><strong>DCE Vibe:</strong> ' . esc_html__( 'Build all pages, menus and images in one click.', 'dce-vibe' ) . ' <a class="button button-primary" href="' . esc_url( admin_url( 'themes.php?page=dce-setup' ) ) . '">' . esc_html__( 'Open DCE Site Setup', 'dce-vibe' ) . '</a></p></div>';
}

function dce_importer_page() {
	$report = get_transient( 'dce_import_report' );
	delete_transient( 'dce_import_report' );
	$defs = dce_page_defs();
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'DCE Site Setup', 'dce-vibe' ); ?></h1>
		<p><?php printf( esc_html__( 'This builds %d pages from editable blocks, imports the curtain and blind photos into the Media Library, creates the header and footer menus and sets the homepage. Existing pages are matched by their URL, so links stay the same.', 'dce-vibe' ), count( $defs ) ); ?></p>
		<p><strong><?php esc_html_e( 'Before you run it on a live site, take a backup (your host or a backup plugin). Updated pages keep a revision you can restore from the editor.', 'dce-vibe' ); ?></strong></p>
		<?php if ( $report ) : ?>
			<div class="notice notice-success"><p><?php echo wp_kses_post( $report ); ?></p></div>
		<?php endif; ?>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="dce_run_import">
			<?php wp_nonce_field( 'dce_run_import' ); ?>
			<p><strong><?php esc_html_e( 'Website', 'dce-vibe' ); ?>:</strong>
				<label><input type="radio" name="profile" value="dce" <?php checked( dce_profile(), 'dce' ); ?>> Dubai Curtain Experts</label> &nbsp;
				<label><input type="radio" name="profile" value="dbh" <?php checked( dce_profile(), 'dbh' ); ?>> Dubai Blinds Hub</label></p>
			<p><label><input type="checkbox" name="overwrite" value="1" checked> <?php esc_html_e( 'Replace the content of existing pages with the new design', 'dce-vibe' ); ?></label></p>
			<p><label><input type="checkbox" name="menus" value="1" checked> <?php esc_html_e( 'Create / refresh header and footer menus', 'dce-vibe' ); ?></label></p>
			<p><label><input type="checkbox" name="reading" value="1" checked> <?php esc_html_e( 'Set the Home page as front page and Blog as posts page', 'dce-vibe' ); ?></label></p>
			<?php submit_button( __( 'Build / update the site', 'dce-vibe' ) ); ?>
		</form>
		<h2><?php esc_html_e( 'How to edit', 'dce-vibe' ); ?></h2>
		<ul style="list-style:disc;padding-left:20px">
			<li><?php esc_html_e( 'Pages: Pages → edit any page. Every section is a normal block — change text, swap images, reorder or duplicate sections.', 'dce-vibe' ); ?></li>
			<li><?php esc_html_e( 'Header, footer, phone, WhatsApp, email, address, hours, Casa Vera Home link: Appearance → Customize → DCE Business, Header & Footer.', 'dce-vibe' ); ?></li>
			<li><?php esc_html_e( 'Logo: Appearance → Customize → Site Identity. Menus: Appearance → Menus.', 'dce-vibe' ); ?></li>
			<li><?php esc_html_e( 'Smart links for buttons and menu items: #whatsapp, #call, #email, #map, #quote, #parent.', 'dce-vibe' ); ?></li>
			<li><?php esc_html_e( 'Leads: every form request is saved under Leads and emailed to you, then opened in WhatsApp.', 'dce-vibe' ); ?></li>
			<li><?php esc_html_e( 'SEO: each page has an "SEO" box under the editor for meta title and description.', 'dce-vibe' ); ?></li>
		</ul>
	</div>
	<?php
}

add_action( 'admin_post_dce_run_import', 'dce_handle_import' );
function dce_handle_import() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'Not allowed.', 'dce-vibe' ) );
	}
	check_admin_referer( 'dce_run_import' );
	if ( isset( $_POST['profile'] ) && in_array( $_POST['profile'], array( 'dce', 'dbh' ), true ) ) {
		update_option( 'dce_profile', sanitize_key( $_POST['profile'] ) );
	}
	$result = dce_run_import(
		array(
			'overwrite' => ! empty( $_POST['overwrite'] ),
			'menus'     => ! empty( $_POST['menus'] ),
			'reading'   => ! empty( $_POST['reading'] ),
		)
	);
	set_transient( 'dce_import_report', $result['report'], 120 );
	wp_safe_redirect( admin_url( 'themes.php?page=dce-setup' ) );
	exit;
}

/**
 * Run the full import.
 *
 * @param array $opts overwrite, menus, reading.
 * @return array
 */
function dce_run_import( $opts = array() ) {
	$opts = wp_parse_args(
		$opts,
		array(
			'overwrite' => true,
			'menus'     => true,
			'reading'   => true,
		)
	);
	if ( function_exists( 'set_time_limit' ) ) {
		@set_time_limit( 0 ); // phpcs:ignore
	}
	wp_raise_memory_limit( 'admin' );
	kses_remove_filters();

	$media                = dce_import_media();
	$GLOBALS['dce_media'] = $media['map'];

	$ids     = array();
	$errors  = array();
	$created = 0;
	$updated = 0;
	$skipped = 0;
	foreach ( dce_page_defs() as $slug => $def ) {
		$parent_id = 0;
		$path      = $slug;
		if ( ! empty( $def['parent'] ) ) {
			$parent_id = isset( $ids[ $def['parent'] ] ) ? $ids[ $def['parent'] ] : 0;
			$path      = $def['parent'] . '/' . $slug;
		}
		$existing = get_page_by_path( $path, OBJECT, 'page' );
		if ( ! $existing && 'home' === $slug && 'page' === get_option( 'show_on_front' ) && get_option( 'page_on_front' ) ) {
			$existing = get_post( (int) get_option( 'page_on_front' ) );
		}
		$content = call_user_func( $def['build'] );
		$title   = html_entity_decode( $def['title'], ENT_QUOTES, 'UTF-8' );
		$postarr = array(
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_title'   => $title,
			'post_name'    => $slug,
			'post_parent'  => $parent_id,
			'post_content' => $content,
			'post_excerpt' => isset( $def['seo'][1] ) ? $def['seo'][1] : '',
			'menu_order'   => count( $ids ),
		);
		if ( $existing ) {
			if ( ! $opts['overwrite'] ) {
				$ids[ $slug ] = $existing->ID;
				++$skipped;
				continue;
			}
			$postarr['ID'] = $existing->ID;
			// Old theme/plugin templates (e.g. Service Pages Builder) would make the update fail.
			update_post_meta( $existing->ID, '_wp_page_template', 'default' );
			$id            = wp_update_post( wp_slash( $postarr ), true );
			++$updated;
		} else {
			$id = wp_insert_post( wp_slash( $postarr ), true );
			++$created;
		}
		if ( is_wp_error( $id ) ) {
			$errors[] = $slug . ': ' . $id->get_error_message();
			continue;
		}
		$ids[ $slug ] = $id;
		update_post_meta( $id, '_wp_page_template', 'default' );
		if ( ! empty( $def['seo'] ) ) {
			update_post_meta( $id, '_dce_seo_title', $def['seo'][0] );
			update_post_meta( $id, '_dce_seo_desc', $def['seo'][1] );
		}
		update_post_meta( $id, '_dce_service_name', isset( $def['svc'] ) ? $def['svc'] : '' );
		if ( ! empty( $def['thumb'] ) ) {
			$img = is_array( $def['thumb'] ) ? dce_img( $def['thumb'][0], $def['thumb'][1] ) : dce_img( $def['thumb'], 1 );
			if ( $img && ! empty( $img['id'] ) ) {
				set_post_thumbnail( $id, $img['id'] );
			}
		}
	}

	if ( $opts['menus'] ) {
		if ( 'dbh' === dce_profile() ) {
			dbh_build_menus( $ids );
		} else {
			dce_build_menus( $ids );
		}
	}
	if ( $opts['reading'] && isset( $ids['home'] ) ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $ids['home'] );
		if ( isset( $ids['blog'] ) ) {
			update_option( 'page_for_posts', $ids['blog'] );
		}
	}
	dce_clear_default_widgets();
	update_option( 'dce_imported', time() );
	kses_init_filters();
	flush_rewrite_rules();

	$report = sprintf(
		'Done. Pages created: %1$d, updated: %2$d, unchanged: %3$d. Images imported: %4$d, reused: %5$d, matched from your existing media library: %6$d.',
		$created,
		$updated,
		$skipped,
		$media['imported'],
		$media['reused'],
		$media['matched']
	);
	if ( $errors ) {
		$report .= ' Errors: ' . esc_html( implode( '; ', $errors ) ) . '.';
	}
	if ( $media['missing'] ) {
		$report .= ' Image groups with no photo yet (add one in the page editor): ' . esc_html( implode( ', ', $media['missing'] ) ) . '.';
	}
	return array(
		'ids'    => $ids,
		'report' => $report,
	);
}

/**
 * Import bundled images and match extra photos already in the Media Library.
 *
 * @return array map, imported, reused, matched, missing.
 */
function dce_import_media( $max_new = 0 ) {
	require_once ABSPATH . 'wp-admin/includes/image.php';
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';

	$manifest = json_decode( (string) file_get_contents( DCE_DIR . '/assets/img/manifest.json' ), true ); // phpcs:ignore
	$manifest = is_array( $manifest ) ? $manifest : array();
	$map      = array();
	$stats    = array(
		'imported' => 0,
		'reused'   => 0,
		'matched'  => 0,
		'pending'  => 0,
		'missing'  => array(),
	);

	foreach ( $manifest as $key => $files ) {
		foreach ( $files as $f ) {
			$src      = $key . '/' . $f['file'];
			$existing = get_posts(
				array(
					'post_type'      => 'attachment',
					'post_status'    => 'inherit',
					'meta_key'       => '_dce_src', // phpcs:ignore
					'meta_value'     => $src, // phpcs:ignore
					'fields'         => 'ids',
					'posts_per_page' => 1,
				)
			);
			if ( $existing ) {
				$aid = $existing[0];
				++$stats['reused'];
			} elseif ( $max_new && $stats['imported'] >= $max_new ) {
				++$stats['pending'];
				continue;
			} else {
				$aid = dce_sideload_local( DCE_DIR . '/assets/img/' . $f['file'], $f['alt'] );
				if ( ! $aid ) {
					continue;
				}
				update_post_meta( $aid, '_dce_src', $src );
				++$stats['imported'];
			}
			update_post_meta( $aid, '_wp_attachment_image_alt', $f['alt'] );
			$map[ $key ][] = dce_media_item( $aid, $f['alt'] );
		}
	}

	// Photos already uploaded to this site (e.g. blackout-roller-blinds-dubai-1.jpg) fill gaps.
	$extra = array(
		'blackout-roller-blinds'  => array( 'blackout-roller-blinds-dubai', 'Blackout roller blinds in a Dubai home' ),
		'sunscreen-roller-blinds' => array( 'sunscreen-roller-blinds-dubai', 'Sunscreen roller blinds in Dubai' ),
		'hospital-curtains'       => array( 'hospital-curtains-dubai', 'Hospital cubicle curtains on ceiling tracks' ),
		'kids-curtains'           => array( 'kids-curtains-dubai', 'Kids room curtains in Dubai' ),
		'zebra-blinds'            => array( 'zebra-blinds-dubai', 'Zebra blinds in Dubai' ),
		'vertical-blinds'         => array( 'vertical-blinds-dubai', 'Vertical blinds in Dubai' ),
		'logo-sunscreen-blinds'   => array( 'logo-printed-sunscreen-blinds-dubai', 'Logo printed sunscreen blinds' ),
		'cinema-curtains'         => array( 'cinema-curtains-dubai', 'Cinema curtains in Dubai' ),
	);
	if ( 'dbh' === dce_profile() ) {
		$extra = array();
	}
	foreach ( $extra as $key => $info ) {
		$have = isset( $map[ $key ] ) ? count( $map[ $key ] ) : 0;
		if ( $have >= 4 ) {
			continue;
		}
		foreach ( dce_find_media_by_prefix( $info[0] ) as $aid ) {
			$alt = get_post_meta( $aid, '_wp_attachment_image_alt', true );
			if ( ! $alt ) {
				$alt = $info[1];
				update_post_meta( $aid, '_wp_attachment_image_alt', $alt );
			}
			$map[ $key ][] = dce_media_item( $aid, $alt );
			++$stats['matched'];
			if ( ++$have >= 6 ) {
				break;
			}
		}
	}

	// Per-page image lists (Dubai Blinds Hub): bundled photos first, then matching Media Library photos.
	foreach ( dce_products() as $slug => $p ) {
		if ( empty( $p['imgs'] ) && empty( $p['media'] ) ) {
			continue;
		}
		$list = array();
		foreach ( (array) ( isset( $p['imgs'] ) ? $p['imgs'] : array() ) as $spec ) {
			if ( ! empty( $map[ $spec[0] ] ) ) {
				$all    = array_values( $map[ $spec[0] ] );
				$list[] = $all[ ( $spec[1] - 1 ) % count( $all ) ];
			}
		}
		foreach ( (array) ( isset( $p['media'] ) ? $p['media'] : array() ) as $prefix ) {
			foreach ( dce_find_media_by_prefix( $prefix ) as $aid ) {
				if ( count( $list ) >= 6 ) {
					break 2;
				}
				$alt = get_post_meta( $aid, '_wp_attachment_image_alt', true );
				if ( ! $alt ) {
					$alt = wp_strip_all_tags( html_entity_decode( $p['name'] ) ) . ' in Dubai';
					update_post_meta( $aid, '_wp_attachment_image_alt', $alt );
				}
				$list[] = dce_media_item( $aid, $alt );
				++$stats['matched'];
			}
		}
		if ( $list ) {
			$map[ 'p:' . $slug ] = $list;
		}
	}

	foreach ( dce_products() as $slug => $p ) {
		if ( empty( $map[ 'p:' . $slug ] ) && ( ! $p['img'] || empty( $map[ $p['img'] ] ) ) ) {
			$stats['missing'][] = html_entity_decode( wp_strip_all_tags( $p['name'] ), ENT_QUOTES, 'UTF-8' );
		}
	}
	$stats['map'] = $map;
	return $stats;
}

function dce_media_item( $aid, $alt ) {
	$src = wp_get_attachment_image_src( $aid, 'large' );
	return array(
		'id'  => (int) $aid,
		'url' => $src ? $src[0] : wp_get_attachment_url( $aid ),
		'alt' => $alt,
	);
}

/**
 * Attachment IDs whose file name starts with a prefix (newest uploads first, excluding our own imports).
 *
 * @param string $prefix File name prefix.
 * @return int[]
 */
function dce_find_media_by_prefix( $prefix ) {
	global $wpdb;
	$like = '%/' . $wpdb->esc_like( $prefix ) . '%';
	$ids  = $wpdb->get_col( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$wpdb->prepare(
			"SELECT p.ID FROM {$wpdb->posts} p
			INNER JOIN {$wpdb->postmeta} m ON m.post_id = p.ID AND m.meta_key = '_wp_attached_file'
			LEFT JOIN {$wpdb->postmeta} own ON own.post_id = p.ID AND own.meta_key = '_dce_src'
			WHERE p.post_type = 'attachment' AND p.post_mime_type LIKE 'image/%%' AND own.meta_id IS NULL AND CONCAT('/', m.meta_value) LIKE %s
			ORDER BY m.meta_value ASC LIMIT 8",
			$like
		)
	);
	return array_map( 'intval', $ids );
}

/**
 * Copy a theme file into uploads and create an attachment.
 *
 * @param string $path Absolute file path.
 * @param string $alt  Alt text / title.
 * @return int Attachment ID or 0.
 */
function dce_sideload_local( $path, $alt ) {
	if ( file_exists( $path ) ) {
		$bytes = file_get_contents( $path ); // phpcs:ignore
	} else {
		// Photos not shipped with the theme files are fetched from the theme's public repository.
		$res = wp_remote_get( trailingslashit( DCE_REMOTE_IMG ) . rawurlencode( basename( $path ) ), array( 'timeout' => 60 ) );
		if ( is_wp_error( $res ) || 200 !== wp_remote_retrieve_response_code( $res ) ) {
			return 0;
		}
		$bytes = wp_remote_retrieve_body( $res );
	}
	$upload = wp_upload_bits( basename( $path ), null, $bytes );
	if ( ! empty( $upload['error'] ) ) {
		return 0;
	}
	$type = wp_check_filetype( $upload['file'] );
	$aid  = wp_insert_attachment(
		array(
			'post_mime_type' => $type['type'],
			'post_title'     => $alt,
			'post_content'   => '',
			'post_status'    => 'inherit',
		),
		$upload['file']
	);
	if ( is_wp_error( $aid ) || ! $aid ) {
		return 0;
	}
	wp_update_attachment_metadata( $aid, wp_generate_attachment_metadata( $aid, $upload['file'] ) );
	return (int) $aid;
}

/**
 * Create or refresh a menu and assign it to a location.
 *
 * @param string $name     Menu name.
 * @param string $location Theme location.
 * @param array  $items    Items: [title, page-slug|url, children[]].
 * @param array  $ids      Page IDs by slug.
 */
function dce_make_menu( $name, $location, $items, $ids ) {
	$menu = wp_get_nav_menu_object( $name );
	if ( $menu ) {
		foreach ( (array) wp_get_nav_menu_items( $menu->term_id, array( 'post_status' => 'any' ) ) as $item ) {
			wp_delete_post( $item->ID, true );
		}
		$menu_id = $menu->term_id;
	} else {
		$menu_id = wp_create_nav_menu( $name );
	}
	if ( is_wp_error( $menu_id ) ) {
		return;
	}
	$add = function ( $item, $parent = 0 ) use ( $menu_id, $ids, &$add ) {
		list( $title, $target ) = $item;
		$args = array(
			'menu-item-title'     => $title,
			'menu-item-status'    => 'publish',
			'menu-item-parent-id' => $parent,
		);
		if ( isset( $ids[ $target ] ) ) {
			$args['menu-item-object-id'] = $ids[ $target ];
			$args['menu-item-object']    = 'page';
			$args['menu-item-type']      = 'post_type';
		} else {
			$args['menu-item-url']  = $target;
			$args['menu-item-type'] = 'custom';
			if ( 0 === strpos( $target, 'http' ) && false === strpos( $target, home_url() ) ) {
				$args['menu-item-target'] = '_blank';
			}
		}
		$item_id = wp_update_nav_menu_item( $menu_id, 0, $args );
		if ( ! empty( $item[2] ) && ! is_wp_error( $item_id ) ) {
			foreach ( $item[2] as $child ) {
				$add( $child, $item_id );
			}
		}
	};
	foreach ( $items as $item ) {
		$add( $item );
	}
	$locations              = get_theme_mod( 'nav_menu_locations', array() );
	$locations[ $location ] = $menu_id;
	set_theme_mod( 'nav_menu_locations', $locations );
}

function dce_build_menus( $ids ) {
	$curtains = array();
	$blinds   = array();
	foreach ( dce_products() as $slug => $p ) {
		$label = str_replace( ' &amp; Home Theatre', '', wp_strip_all_tags( $p['name'] ) );
		$label = html_entity_decode( $label, ENT_QUOTES, 'UTF-8' );
		if ( 'curtains' === $p['parent'] ) {
			$curtains[] = array( $label, $slug );
		} else {
			$blinds[] = array( $label, $slug );
		}
	}
	$areas = array();
	foreach ( dce_areas() as $slug => $a ) {
		$areas[] = array( html_entity_decode( $a['name'], ENT_QUOTES, 'UTF-8' ), $slug );
	}
	$parent = dce_opt( 'parent_url' );

	dce_make_menu(
		'Main Menu',
		'primary',
		array(
			array( 'Curtains', 'curtains', $curtains ),
			array( 'Blinds', 'blinds', $blinds ),
			array( 'Services', 'services' ),
			array( 'Catalogues', 'catalogue' ),
			array( 'Projects', 'projects' ),
			array( 'Areas', 'areas-we-serve', $areas ),
			array( 'About', 'about-us', array( array( 'About us', 'about-us' ), array( 'Blog & guides', 'blog' ), array( 'Casa Vera Home', $parent ) ) ),
			array( 'Contact', 'contact-us' ),
		),
		$ids
	);
	dce_make_menu( 'Curtains', 'footer_curtains', $curtains, $ids );
	dce_make_menu( 'Blinds', 'footer_blinds', $blinds, $ids );
	dce_make_menu(
		'Company',
		'footer_company',
		array(
			array( 'About us', 'about-us' ),
			array( 'Services', 'services' ),
			array( 'Fabric catalogues', 'catalogue' ),
			array( 'Projects', 'projects' ),
			array( 'Areas we serve', 'areas-we-serve' ),
			array( 'Blog & guides', 'blog' ),
			array( 'Contact', 'contact-us' ),
			array( 'Casa Vera Home', $parent ),
		),
		$ids
	);
	dce_make_menu( 'Footer links', 'footer_legal', array( array( 'Privacy policy', 'privacy-policy' ), array( 'Contact', 'contact-us' ) ), $ids );
}

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	WP_CLI::add_command(
		'dce import',
		function ( $args, $assoc ) {
			$r = dce_run_import(
				array(
					'overwrite' => empty( $assoc['no-overwrite'] ),
					'menus'     => empty( $assoc['skip-menus'] ),
					'reading'   => empty( $assoc['skip-reading'] ),
				)
			);
			WP_CLI::success( wp_strip_all_tags( $r['report'] ) );
		}
	);
}

/** Remove WordPress default widgets (Archives, Categories…) that get auto-assigned to the footer area. */
function dce_clear_default_widgets() {
	$sidebars = wp_get_sidebars_widgets();
	if ( ! empty( $sidebars['footer-extra'] ) ) {
		$sidebars['wp_inactive_widgets'] = array_merge( isset( $sidebars['wp_inactive_widgets'] ) ? $sidebars['wp_inactive_widgets'] : array(), $sidebars['footer-extra'] );
		$sidebars['footer-extra']        = array();
		wp_set_sidebars_widgets( $sidebars );
	}
}

/*
 * REST endpoints for remote setup (administrators only):
 *   POST /wp-json/dce/v1/media   {"profile":"dbh","batch":15}  imports photos in batches; repeat until pending = 0
 *   POST /wp-json/dce/v1/import  {"profile":"dbh"}             builds pages, menus and the homepage
 */
add_action( 'rest_api_init', 'dce_register_import_routes' );
function dce_register_import_routes() {
	$perm = fn() => current_user_can( 'manage_options' );
	register_rest_route(
		'dce/v1',
		'/media',
		array(
			'methods'             => 'POST',
			'permission_callback' => $perm,
			'callback'            => function ( WP_REST_Request $r ) {
				dce_rest_profile( $r );
				@set_time_limit( 0 ); // phpcs:ignore
				$m = dce_import_media( max( 1, (int) ( $r['batch'] ? $r['batch'] : 15 ) ) );
				unset( $m['map'] );
				return $m;
			},
		)
	);
	register_rest_route(
		'dce/v1',
		'/import',
		array(
			'methods'             => 'POST',
			'permission_callback' => $perm,
			'callback'            => function ( WP_REST_Request $r ) {
				dce_rest_profile( $r );
				$res = dce_run_import(
					array(
						'overwrite' => false !== $r['overwrite'],
						'menus'     => false !== $r['menus'],
						'reading'   => false !== $r['reading'],
					)
				);
				return array(
					'report' => wp_strip_all_tags( $res['report'] ),
					'pages'  => count( $res['ids'] ),
				);
			},
		)
	);
}

function dce_rest_profile( $r ) {
	if ( in_array( $r['profile'], array( 'dce', 'dbh' ), true ) ) {
		update_option( 'dce_profile', $r['profile'] );
	}
}
