<?php
/**
 * Installer: creates all site pages, menus and reading settings, and adds the
 * "Appearance → CFM Setup" screen.
 *
 * Safe to run repeatedly: existing pages are never overwritten unless you
 * explicitly rebuild a single page.
 *
 * @package CFM
 */

defined( 'ABSPATH' ) || exit;

add_action( 'after_switch_theme', 'cfm_on_activate', 10, 2 );
/**
 * First activation: carry over the previous theme's logo/menus-free settings,
 * then install pages and menus once.
 *
 * @param string   $old_name  Previous theme name.
 * @param WP_Theme $old_theme Previous theme.
 */
function cfm_on_activate( $old_name = '', $old_theme = null ) {
	// Theme mods are stored per theme: bring the logo over from the previous theme.
	if ( ! get_theme_mod( 'custom_logo' ) && $old_theme instanceof WP_Theme ) {
		$old_mods = get_option( 'theme_mods_' . $old_theme->get_stylesheet() );
		if ( is_array( $old_mods ) && ! empty( $old_mods['custom_logo'] ) && wp_attachment_is_image( (int) $old_mods['custom_logo'] ) ) {
			set_theme_mod( 'custom_logo', (int) $old_mods['custom_logo'] );
		}
	}
	if ( ! get_option( 'cfm_installed' ) ) {
		cfm_install();
	}
}

add_action( 'admin_init', 'cfm_maybe_upgrade' );
/**
 * After a theme update (new version uploaded), add any new pages and menu items
 * automatically on the next wp-admin visit. Existing content is kept.
 */
function cfm_maybe_upgrade() {
	$installed = get_option( 'cfm_installed' );
	if ( $installed && version_compare( (string) $installed, CFM_VERSION, '<' ) && current_user_can( 'edit_theme_options' ) ) {
		cfm_install();
	}
}

/**
 * Core pages that replace an existing page with the same slug when that page
 * was not made by this theme (e.g. a previous template's demo "About" page).
 * The old content stays available under the page's Revisions.
 *
 * @return string[]
 */
function cfm_adoptable_slugs() {
	return array( 'home', 'about', 'services', 'projects', 'faq', 'contact', 'privacy-policy' );
}

/**
 * Create/repair pages, menus and settings.
 *
 * @return array{created:int,existing:int}
 */
function cfm_install() {
	$result = cfm_install_pages();
	cfm_install_settings();
	cfm_install_menus();
	cfm_install_interiors_menu();
	update_option( 'cfm_installed', CFM_VERSION );
	return $result;
}

/**
 * Create missing pages (pass 1) and fill content for new ones (pass 2).
 *
 * @param string[] $rebuild Slugs whose content should be regenerated.
 * @return array{created:int,existing:int}
 */
function cfm_install_pages( $rebuild = array() ) {
	$data    = cfm_pages_data();
	$ids     = array();
	$fresh   = array();
	$created = 0;

	// Pass 1: make sure every page exists in the right place.
	foreach ( $data as $slug => $page ) {
		$parent_id = 0;
		if ( ! empty( $page['parent'] ) && isset( $ids[ $page['parent'] ] ) ) {
			$parent_id = $ids[ $page['parent'] ];
		}
		$existing = get_page_by_path( cfm_page_path( $slug ) );
		if ( ! $existing ) {
			$existing = cfm_get_page( $slug );
		}
		// The current static front page becomes our Home, so the site's front page keeps its ID.
		if ( 'home' === $slug && 'page' === get_option( 'show_on_front' ) && get_option( 'page_on_front' ) ) {
			$front = get_post( (int) get_option( 'page_on_front' ) );
			if ( $front && 'page' === $front->post_type && 'trash' !== $front->post_status ) {
				$existing = $front;
			}
		}
		$is_ours = $existing && '' !== (string) get_post_meta( $existing->ID, '_cfm_type', true );

		// Adopt: unpublished leftovers (e.g. WordPress's "Privacy Policy" draft) and core pages from a previous theme.
		if ( $existing && ! $is_ours && ( 'publish' !== $existing->post_status || in_array( $slug, cfm_adoptable_slugs(), true ) || ! empty( $page['adopt'] ) ) ) {
			wp_update_post(
				wp_slash(
					array(
						'ID'           => $existing->ID,
						'post_status'  => 'publish',
						'post_title'   => $page['title'],
						'page_template' => 'default',
						'post_parent'  => $parent_id,
						'menu_order'   => isset( $page['order'] ) ? (int) $page['order'] : 0,
						'post_excerpt' => $page['excerpt'],
					)
				)
			);
			$ids[ $slug ]   = (int) $existing->ID;
			$fresh[ $slug ] = true;
			++$created;
		} elseif ( $existing ) {
			$ids[ $slug ] = (int) $existing->ID;
		} else {
			$id = wp_insert_post(
				wp_slash( array(
					'post_type'    => 'page',
					'post_status'  => 'publish',
					'post_title'   => $page['title'],
					'post_name'    => $slug,
					'post_parent'  => $parent_id,
					'menu_order'   => isset( $page['order'] ) ? (int) $page['order'] : 0,
					'post_excerpt' => $page['excerpt'],
					'post_content' => '',
					'comment_status' => 'closed',
					'ping_status'  => 'closed',
				) ),
				true
			);
			if ( is_wp_error( $id ) ) {
				continue;
			}
			$ids[ $slug ]  = (int) $id;
			$fresh[ $slug ] = true;
			++$created;
		}

		// Meta that drives templates, schema and image matching (refreshed only for new pages).
		if ( isset( $fresh[ $slug ] ) || in_array( $slug, $rebuild, true ) ) {
			$id = $ids[ $slug ];
			update_post_meta( $id, '_cfm_type', $page['type'] );
			update_post_meta( $id, '_cfm_seo_title', $page['seo_title'] );
			update_post_meta( $id, '_cfm_seo_desc', $page['seo_desc'] );
			update_post_meta( $id, '_cfm_seo_keyword', $page['keyword'] );
			update_post_meta( $id, '_cfm_seo_noindex', empty( $page['noindex'] ) ? '' : '1' );
			update_post_meta( $id, '_cfm_aliases', isset( $page['aliases'] ) ? implode( ',', $page['aliases'] ) : '' );
			if ( isset( $page['place'] ) ) {
				update_post_meta( $id, '_cfm_place', $page['place'] );
				update_post_meta( $id, '_cfm_emirate', $page['emirate'] );
			}
			if ( isset( $page['hero'] ) ) {
				update_post_meta( $id, '_cfm_hero_title', $page['hero'] );
			}
		}
	}

	// Pass 2: content (needs every page to exist so internal links resolve).
	foreach ( $data as $slug => $page ) {
		if ( ! isset( $ids[ $slug ] ) ) {
			continue;
		}
		$is_rebuild = in_array( $slug, $rebuild, true );
		if ( ! isset( $fresh[ $slug ] ) && ! $is_rebuild ) {
			continue;
		}
		$update = array(
			'ID'           => $ids[ $slug ],
			'post_content' => cfm_build_content( $slug, $page ),
		);
		if ( $is_rebuild ) {
			$update['post_excerpt'] = $page['excerpt'];
		}
		wp_update_post( wp_slash( $update ) );
	}

	update_option( 'cfm_page_ids', $ids );
	return array(
		'created'  => $created,
		'existing' => count( $ids ) - $created,
	);
}

/**
 * Reading settings, privacy page, permalinks.
 */
function cfm_install_settings() {
	// Remove WordPress's untouched demo content ("Sample Page", "Hello world!").
	$sample = get_page_by_path( 'sample-page' );
	if ( $sample && false !== strpos( $sample->post_content, 'This is an example page' ) ) {
		wp_trash_post( $sample->ID );
	}
	$hello = get_posts( array( 'name' => 'hello-world', 'post_type' => 'post', 'posts_per_page' => 1 ) );
	if ( $hello && false !== strpos( $hello[0]->post_content, 'Welcome to WordPress' ) ) {
		wp_trash_post( $hello[0]->ID );
	}

	$home = cfm_get_page( 'home' );
	if ( $home ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $home->ID );
	}
	$privacy = cfm_get_page( 'privacy-policy' );
	if ( $privacy ) {
		update_option( 'wp_page_for_privacy_policy', $privacy->ID );
	}
	if ( '' === (string) get_option( 'permalink_structure' ) ) {
		update_option( 'permalink_structure', '/%postname%/' );
		flush_rewrite_rules( false );
	}
	if ( get_option( 'blogname' ) === 'My WordPress Website' || '' === (string) get_option( 'blogname' ) || 'WordPress' === get_option( 'blogname' ) ) {
		update_option( 'blogname', cfm_opt( 'brand_name' ) );
	}
	if ( '' === (string) get_option( 'blogdescription' ) || 'Just another WordPress site' === get_option( 'blogdescription' ) ) {
		update_option( 'blogdescription', cfm_opt( 'brand_tagline' ) );
	}
}

/**
 * Add one page item to a menu.
 *
 * @param int    $menu_id Menu.
 * @param string $slug    Page slug.
 * @param int    $parent  Parent item.
 * @param string $label   Optional label.
 * @return int Item ID.
 */
function cfm_menu_add_page( $menu_id, $slug, $parent = 0, $label = '' ) {
	$page = cfm_get_page( $slug );
	if ( ! $page ) {
		return 0;
	}
	$data = cfm_pages_data();
	if ( '' === $label ) {
		$label = isset( $data[ $slug ]['menu'] ) ? $data[ $slug ]['menu'] : $page->post_title;
	}
	return (int) wp_update_nav_menu_item(
		$menu_id,
		0,
		array(
			'menu-item-title'     => $label,
			'menu-item-object'    => 'page',
			'menu-item-object-id' => $page->ID,
			'menu-item-type'      => 'post_type',
			'menu-item-status'    => 'publish',
			'menu-item-parent-id' => $parent,
		)
	);
}

/**
 * Create a menu if it does not exist yet, filled by a callback.
 *
 * @param string   $name     Menu name.
 * @param string   $location Theme location.
 * @param callable $fill     Callback receiving the menu ID.
 */
function cfm_make_menu( $name, $location, $fill ) {
	$locations = get_theme_mod( 'nav_menu_locations', array() );
	$menu      = wp_get_nav_menu_object( $name );
	if ( ! $menu ) {
		$menu_id = wp_create_nav_menu( $name );
		if ( is_wp_error( $menu_id ) ) {
			return;
		}
		$fill( $menu_id );
	} else {
		$menu_id = $menu->term_id;
	}
	if ( empty( $locations[ $location ] ) ) {
		$locations[ $location ] = $menu_id;
		set_theme_mod( 'nav_menu_locations', $locations );
	}
}

/**
 * Header and footer menus.
 */
function cfm_install_menus() {
	$data     = cfm_pages_data();
	$children = static function ( $parent ) use ( $data ) {
		return array_keys(
			array_filter(
				$data,
				static function ( $p ) use ( $parent ) {
					return isset( $p['parent'] ) && $parent === $p['parent'];
				}
			)
		);
	};

	cfm_make_menu(
		'Primary Menu',
		'primary',
		static function ( $menu_id ) use ( $children, $data ) {
			$products = cfm_menu_add_page( $menu_id, 'floor-mats' );
			foreach ( $children( 'floor-mats' ) as $slug ) {
				cfm_menu_add_page( $menu_id, $slug, $products );
			}
			$services = cfm_menu_add_page( $menu_id, 'services' );
			foreach ( $children( 'services' ) as $slug ) {
				cfm_menu_add_page( $menu_id, $slug, $services );
			}
			$areas = cfm_menu_add_page( $menu_id, 'areas-we-serve' );
			foreach ( $children( 'areas-we-serve' ) as $slug ) {
				cfm_menu_add_page( $menu_id, $slug, $areas, $data[ $slug ]['place'] );
			}
			cfm_menu_add_page( $menu_id, 'catalogues' );
			cfm_menu_add_page( $menu_id, 'projects' );
			cfm_menu_add_page( $menu_id, 'about', 0, 'About' );
			cfm_menu_add_page( $menu_id, 'contact' );
		}
	);

	cfm_make_menu(
		'Footer — Products',
		'footer_products',
		static function ( $menu_id ) use ( $children ) {
			foreach ( $children( 'floor-mats' ) as $slug ) {
				cfm_menu_add_page( $menu_id, $slug );
			}
		}
	);

	cfm_make_menu(
		'Footer — Areas',
		'footer_areas',
		static function ( $menu_id ) use ( $children, $data ) {
			foreach ( $children( 'areas-we-serve' ) as $slug ) {
				cfm_menu_add_page( $menu_id, $slug, 0, $data[ $slug ]['place'] );
			}
		}
	);

	cfm_make_menu(
		'Footer — Company',
		'footer_company',
		static function ( $menu_id ) use ( $children ) {
			foreach ( array( 'about', 'services' ) as $slug ) {
				cfm_menu_add_page( $menu_id, $slug );
			}
			foreach ( $children( 'services' ) as $slug ) {
				cfm_menu_add_page( $menu_id, $slug );
			}
			foreach ( array( 'catalogues', 'projects', 'faq', 'get-a-free-quote', 'contact' ) as $slug ) {
				cfm_menu_add_page( $menu_id, $slug );
			}
			wp_update_nav_menu_item(
				$menu_id,
				0,
				array(
					'menu-item-title'  => cfm_opt( 'parent_name' ),
					'menu-item-url'    => cfm_opt( 'parent_url' ),
					'menu-item-type'   => 'custom',
					'menu-item-status' => 'publish',
					'menu-item-target' => '_blank',
				)
			);
		}
	);

	cfm_make_menu(
		'Footer — Legal',
		'legal',
		static function ( $menu_id ) {
			cfm_menu_add_page( $menu_id, 'privacy-policy' );
			cfm_menu_add_page( $menu_id, 'terms-and-conditions' );
		}
	);
}

/* -------------------------------------------------------------------------
 * Admin screen: Appearance → CFM Setup
 * ----------------------------------------------------------------------- */

add_action( 'admin_menu', 'cfm_admin_menu' );
/**
 * Register the setup screen.
 */
function cfm_admin_menu() {
	add_theme_page( __( 'CFM Setup', 'cfm' ), __( 'CFM Setup', 'cfm' ), 'edit_theme_options', 'cfm-setup', 'cfm_setup_screen' );
}

add_action( 'admin_post_cfm_setup', 'cfm_setup_actions' );
/**
 * Handle setup-screen buttons.
 */
function cfm_setup_actions() {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		wp_die( esc_html__( 'Not allowed.', 'cfm' ) );
	}
	check_admin_referer( 'cfm_setup' );
	$task    = isset( $_POST['task'] ) ? sanitize_key( $_POST['task'] ) : '';
	$message = '';

	if ( 'install' === $task ) {
		$r       = cfm_install();
		$message = sprintf( __( 'Done: %1$d pages created, %2$d already existed. Menus and front page are set.', 'cfm' ), $r['created'], $r['existing'] );
	} elseif ( 'rebuild' === $task && ! empty( $_POST['slug'] ) ) {
		$slug = sanitize_title( wp_unslash( $_POST['slug'] ) );
		cfm_install_pages( array( $slug ) );
		$message = sprintf( __( 'Rebuilt "%s" from the starter content.', 'cfm' ), $slug );
	} elseif ( 'import' === $task ) {
		$r       = cfm_import_images();
		$message = sprintf( __( 'Images: %1$d imported, %2$d featured images set, %3$d skipped (no matching page). %4$s', 'cfm' ), $r['imported'], $r['featured'], $r['skipped'], implode( ' ', array_slice( $r['notes'], 0, 10 ) ) );
	} elseif ( 'drive' === $task ) {
		$r       = cfm_drive_import( 12 );
		$message = sprintf( __( 'Google Drive: %1$d files imported, %2$d featured images set, %3$d failed, %4$d still to import (click again). %5$s', 'cfm' ), $r['imported'], $r['featured'], $r['failed'], $r['remaining'], implode( ' ', array_slice( $r['notes'], 0, 5 ) ) );
	} elseif ( 'whatsapp' === $task ) {
		$number = isset( $_POST['whatsapp'] ) ? sanitize_text_field( wp_unslash( $_POST['whatsapp'] ) ) : '';
		set_theme_mod( 'cfm_whatsapp_number', $number );
		$message = $number ? __( 'WhatsApp number saved. Every quote button now opens WhatsApp.', 'cfm' ) : __( 'WhatsApp number cleared.', 'cfm' );
	}

	set_transient( 'cfm_setup_notice', $message, 60 );
	wp_safe_redirect( admin_url( 'themes.php?page=cfm-setup' ) );
	exit;
}

/**
 * Setup screen.
 */
function cfm_setup_screen() {
	$notice = get_transient( 'cfm_setup_notice' );
	delete_transient( 'cfm_setup_notice' );
	$data   = cfm_pages_data();
	$upload = wp_upload_dir();
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Custom Floor Mats — Setup', 'cfm' ); ?></h1>
		<?php if ( $notice ) : ?>
			<div class="notice notice-success is-dismissible"><p><?php echo esc_html( $notice ); ?></p></div>
		<?php endif; ?>

		<div class="card" style="max-width:none">
			<h2><?php esc_html_e( '1. WhatsApp number (all leads go here)', 'cfm' ); ?></h2>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<?php wp_nonce_field( 'cfm_setup' ); ?>
				<input type="hidden" name="action" value="cfm_setup"><input type="hidden" name="task" value="whatsapp">
				<input type="text" name="whatsapp" value="<?php echo esc_attr( cfm_opt( 'whatsapp_number' ) ); ?>" placeholder="9715XXXXXXXX" class="regular-text">
				<?php submit_button( __( 'Save number', 'cfm' ), 'primary', 'submit', false ); ?>
				<p class="description"><?php esc_html_e( 'International format without + or spaces. More contact fields: Appearance → Customize → Custom Floor Mats.', 'cfm' ); ?></p>
			</form>
		</div>

		<div class="card" style="max-width:none">
			<h2><?php esc_html_e( '2. Pages, menus & front page', 'cfm' ); ?></h2>
			<p><?php esc_html_e( 'Creates any of the site’s pages that are missing, the header/footer menus and sets the Home page. Existing pages and your edits are never overwritten.', 'cfm' ); ?></p>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<?php wp_nonce_field( 'cfm_setup' ); ?>
				<input type="hidden" name="action" value="cfm_setup"><input type="hidden" name="task" value="install">
				<?php submit_button( __( 'Install / repair pages & menus', 'cfm' ), 'primary', 'submit', false ); ?>
			</form>
		</div>

		<div class="card" style="max-width:none">
			<h2><?php esc_html_e( '3. Import page images', 'cfm' ); ?></h2>
			<p>
				<?php
				printf(
					/* translators: %s: folder path */
					esc_html__( 'Upload your photos (e.g. the Casa Vera Google Drive mats folder) by FTP / File Manager to %s. Name each file or sub-folder after the page it belongs to — e.g. "custom-logo-mats.jpg", "custom-logo-mats-2.jpg" or a folder "anti-fatigue-mats/". Common names such as "logo", "entrance", "coir", "car", "EPDM" or "anti-slip" are matched automatically. The first image becomes the page’s hero/featured image; the rest appear in that page’s gallery.', 'cfm' ),
					'<code>' . esc_html( trailingslashit( $upload['basedir'] ) . 'cfm-import/' ) . '</code>'
				);
				?>
			</p>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<?php wp_nonce_field( 'cfm_setup' ); ?>
				<input type="hidden" name="action" value="cfm_setup"><input type="hidden" name="task" value="import">
				<?php submit_button( __( 'Import images now', 'cfm' ), 'secondary', 'submit', false ); ?>
			</form>
		</div>

		<div class="card" style="max-width:none">
			<h2><?php esc_html_e( '4. Import from Google Drive', 'cfm' ); ?></h2>
			<p><?php esc_html_e( 'Downloads the Casa Vera Home photos and catalogue PDFs listed in inc/drive-import.php straight from Google Drive (the files must stay shared “anyone with the link”) and attaches them to their pages. Imports 12 files per click; already-imported files are skipped.', 'cfm' ); ?></p>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<?php wp_nonce_field( 'cfm_setup' ); ?>
				<input type="hidden" name="action" value="cfm_setup"><input type="hidden" name="task" value="drive">
				<?php submit_button( __( 'Import from Google Drive', 'cfm' ), 'secondary', 'submit', false ); ?>
			</form>
		</div>

		<h2><?php esc_html_e( 'All pages', 'cfm' ); ?></h2>
		<table class="widefat striped">
			<thead><tr><th><?php esc_html_e( 'Page', 'cfm' ); ?></th><th><?php esc_html_e( 'Type', 'cfm' ); ?></th><th><?php esc_html_e( 'Hero image', 'cfm' ); ?></th><th><?php esc_html_e( 'Image file name to use', 'cfm' ); ?></th><th><?php esc_html_e( 'Actions', 'cfm' ); ?></th></tr></thead>
			<tbody>
			<?php
			$n = 0;
			foreach ( $data as $slug => $page ) :
				++$n;
				$post = cfm_get_page( $slug );
				?>
				<tr>
					<td><?php echo (int) $n; ?>. <strong><?php echo esc_html( $page['title'] ); ?></strong><br><code>/<?php echo esc_html( cfm_page_path( $slug ) ); ?>/</code></td>
					<td><?php echo esc_html( $page['type'] ); ?></td>
					<td><?php echo $post && has_post_thumbnail( $post ) ? get_the_post_thumbnail( $post, array( 80, 60 ) ) : '—'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></td>
					<td><code><?php echo esc_html( $slug ); ?>.jpg</code></td>
					<td>
						<?php if ( $post ) : ?>
							<a class="button button-small" href="<?php echo esc_url( get_edit_post_link( $post ) ); ?>"><?php esc_html_e( 'Edit', 'cfm' ); ?></a>
							<a class="button button-small" href="<?php echo esc_url( get_permalink( $post ) ); ?>" target="_blank"><?php esc_html_e( 'View', 'cfm' ); ?></a>
							<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="display:inline" onsubmit="return confirm('<?php echo esc_js( __( 'Replace this page’s content with the starter content? Your edits on this page will be lost.', 'cfm' ) ); ?>');">
								<?php wp_nonce_field( 'cfm_setup' ); ?>
								<input type="hidden" name="action" value="cfm_setup"><input type="hidden" name="task" value="rebuild"><input type="hidden" name="slug" value="<?php echo esc_attr( $slug ); ?>">
								<button class="button button-small button-link-delete" type="submit"><?php esc_html_e( 'Rebuild', 'cfm' ); ?></button>
							</form>
						<?php else : ?>
							<em><?php esc_html_e( 'Missing — run Install', 'cfm' ); ?></em>
						<?php endif; ?>
					</td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
	</div>
	<?php
}

/**
 * Add the "Interiors" dropdown (6 categories) to the header menu if it is not there yet.
 */
function cfm_install_interiors_menu() {
	$locations = get_theme_mod( 'nav_menu_locations', array() );
	if ( empty( $locations['primary'] ) ) {
		return;
	}
	$menu_id = (int) $locations['primary'];
	$hub     = cfm_get_page( 'interiors' );
	if ( ! $hub ) {
		return;
	}
	foreach ( (array) wp_get_nav_menu_items( $menu_id ) as $item ) {
		if ( 'page' === $item->object && (int) $item->object_id === (int) $hub->ID ) {
			return; // Already added.
		}
	}
	$parent = (int) wp_update_nav_menu_item(
		$menu_id,
		0,
		array(
			'menu-item-title'     => 'Interiors',
			'menu-item-object'    => 'page',
			'menu-item-object-id' => $hub->ID,
			'menu-item-type'      => 'post_type',
			'menu-item-status'    => 'publish',
		)
	);
	foreach ( cfm_interior_category_slugs() as $slug ) {
		cfm_menu_add_page( $menu_id, $slug, $parent );
	}
}
