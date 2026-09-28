<?php
/**
 * Installer: creates the 45 pages, menus and reading settings, and adds the
 * "Appearance → DGF Setup" screen.
 *
 * Safe to run repeatedly: existing pages are never overwritten unless you
 * explicitly rebuild a single page.
 *
 * @package DGF
 */

defined( 'ABSPATH' ) || exit;

add_action( 'after_switch_theme', 'dgf_on_activate' );
/**
 * First activation: install pages and menus once.
 */
function dgf_on_activate() {
	if ( ! get_option( 'dgf_installed' ) ) {
		dgf_install();
	}
}

/**
 * Create/repair pages, menus and settings.
 *
 * @return array{created:int,existing:int}
 */
function dgf_install() {
	$result = dgf_install_pages();
	dgf_install_settings();
	dgf_install_menus();
	update_option( 'dgf_installed', DGF_VERSION );
	return $result;
}

/**
 * Create missing pages (pass 1) and fill content for new ones (pass 2).
 *
 * @param string[] $rebuild Slugs whose content should be regenerated.
 * @return array{created:int,existing:int}
 */
function dgf_install_pages( $rebuild = array() ) {
	$data    = dgf_pages_data();
	$ids     = array();
	$fresh   = array();
	$created = 0;

	// Pass 1: make sure every page exists in the right place.
	foreach ( $data as $slug => $page ) {
		$parent_id = 0;
		if ( ! empty( $page['parent'] ) && isset( $ids[ $page['parent'] ] ) ) {
			$parent_id = $ids[ $page['parent'] ];
		}
		$existing = get_page_by_path( dgf_page_path( $slug ) );
		if ( ! $existing ) {
			$existing = dgf_get_page( $slug );
		}

		// WordPress ships an unpublished "Privacy Policy" draft: adopt it instead of skipping ours.
		if ( $existing && 'publish' !== $existing->post_status && '' === (string) get_post_meta( $existing->ID, '_dgf_type', true ) ) {
			wp_update_post(
				wp_slash(
					array(
						'ID'           => $existing->ID,
						'post_status'  => 'publish',
						'post_title'   => $page['title'],
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
			update_post_meta( $id, '_dgf_type', $page['type'] );
			update_post_meta( $id, '_dgf_seo_title', $page['seo_title'] );
			update_post_meta( $id, '_dgf_seo_desc', $page['seo_desc'] );
			update_post_meta( $id, '_dgf_seo_keyword', $page['keyword'] );
			update_post_meta( $id, '_dgf_seo_noindex', empty( $page['noindex'] ) ? '' : '1' );
			update_post_meta( $id, '_dgf_aliases', isset( $page['aliases'] ) ? implode( ',', $page['aliases'] ) : '' );
			if ( isset( $page['place'] ) ) {
				update_post_meta( $id, '_dgf_place', $page['place'] );
				update_post_meta( $id, '_dgf_emirate', $page['emirate'] );
			}
			if ( isset( $page['hero'] ) ) {
				update_post_meta( $id, '_dgf_hero_title', $page['hero'] );
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
			'post_content' => dgf_build_content( $slug, $page ),
		);
		if ( $is_rebuild ) {
			$update['post_excerpt'] = $page['excerpt'];
		}
		wp_update_post( wp_slash( $update ) );
	}

	update_option( 'dgf_page_ids', $ids );
	return array(
		'created'  => $created,
		'existing' => count( $ids ) - $created,
	);
}

/**
 * Reading settings, privacy page, permalinks.
 */
function dgf_install_settings() {
	// Remove WordPress's untouched demo content ("Sample Page", "Hello world!").
	$sample = get_page_by_path( 'sample-page' );
	if ( $sample && false !== strpos( $sample->post_content, 'This is an example page' ) ) {
		wp_trash_post( $sample->ID );
	}
	$hello = get_posts( array( 'name' => 'hello-world', 'post_type' => 'post', 'posts_per_page' => 1 ) );
	if ( $hello && false !== strpos( $hello[0]->post_content, 'Welcome to WordPress' ) ) {
		wp_trash_post( $hello[0]->ID );
	}

	$home = dgf_get_page( 'home' );
	if ( $home ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $home->ID );
	}
	$privacy = dgf_get_page( 'privacy-policy' );
	if ( $privacy ) {
		update_option( 'wp_page_for_privacy_policy', $privacy->ID );
	}
	if ( '' === (string) get_option( 'permalink_structure' ) ) {
		update_option( 'permalink_structure', '/%postname%/' );
		flush_rewrite_rules( false );
	}
	if ( get_option( 'blogname' ) === 'My WordPress Website' || '' === (string) get_option( 'blogname' ) || 'WordPress' === get_option( 'blogname' ) ) {
		update_option( 'blogname', dgf_opt( 'brand_name' ) );
	}
	if ( '' === (string) get_option( 'blogdescription' ) || 'Just another WordPress site' === get_option( 'blogdescription' ) ) {
		update_option( 'blogdescription', dgf_opt( 'brand_tagline' ) );
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
function dgf_menu_add_page( $menu_id, $slug, $parent = 0, $label = '' ) {
	$page = dgf_get_page( $slug );
	if ( ! $page ) {
		return 0;
	}
	$data = dgf_pages_data();
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
function dgf_make_menu( $name, $location, $fill ) {
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
function dgf_install_menus() {
	$data     = dgf_pages_data();
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

	dgf_make_menu(
		'Primary Menu',
		'primary',
		static function ( $menu_id ) use ( $children, $data ) {
			$products = dgf_menu_add_page( $menu_id, 'gym-flooring-products' );
			foreach ( $children( 'gym-flooring-products' ) as $slug ) {
				dgf_menu_add_page( $menu_id, $slug, $products );
			}
			$services = dgf_menu_add_page( $menu_id, 'services' );
			foreach ( $children( 'services' ) as $slug ) {
				dgf_menu_add_page( $menu_id, $slug, $services );
			}
			$areas = dgf_menu_add_page( $menu_id, 'areas-we-serve' );
			foreach ( $children( 'areas-we-serve' ) as $slug ) {
				dgf_menu_add_page( $menu_id, $slug, $areas, $data[ $slug ]['place'] );
			}
			dgf_menu_add_page( $menu_id, 'catalogues' );
			dgf_menu_add_page( $menu_id, 'projects' );
			dgf_menu_add_page( $menu_id, 'about-us', 0, 'About' );
			dgf_menu_add_page( $menu_id, 'contact-us' );
		}
	);

	dgf_make_menu(
		'Footer — Products',
		'footer_products',
		static function ( $menu_id ) use ( $children ) {
			foreach ( $children( 'gym-flooring-products' ) as $slug ) {
				dgf_menu_add_page( $menu_id, $slug );
			}
		}
	);

	dgf_make_menu(
		'Footer — Areas',
		'footer_areas',
		static function ( $menu_id ) use ( $children, $data ) {
			foreach ( $children( 'areas-we-serve' ) as $slug ) {
				dgf_menu_add_page( $menu_id, $slug, 0, $data[ $slug ]['place'] );
			}
		}
	);

	dgf_make_menu(
		'Footer — Company',
		'footer_company',
		static function ( $menu_id ) use ( $children ) {
			foreach ( array( 'about-us', 'services' ) as $slug ) {
				dgf_menu_add_page( $menu_id, $slug );
			}
			foreach ( $children( 'services' ) as $slug ) {
				dgf_menu_add_page( $menu_id, $slug );
			}
			foreach ( array( 'catalogues', 'projects', 'faqs', 'get-a-free-quote', 'contact-us' ) as $slug ) {
				dgf_menu_add_page( $menu_id, $slug );
			}
			wp_update_nav_menu_item(
				$menu_id,
				0,
				array(
					'menu-item-title'  => dgf_opt( 'parent_name' ),
					'menu-item-url'    => dgf_opt( 'parent_url' ),
					'menu-item-type'   => 'custom',
					'menu-item-status' => 'publish',
					'menu-item-target' => '_blank',
				)
			);
		}
	);

	dgf_make_menu(
		'Footer — Legal',
		'legal',
		static function ( $menu_id ) {
			dgf_menu_add_page( $menu_id, 'privacy-policy' );
			dgf_menu_add_page( $menu_id, 'terms-and-conditions' );
		}
	);
}

/* -------------------------------------------------------------------------
 * Admin screen: Appearance → DGF Setup
 * ----------------------------------------------------------------------- */

add_action( 'admin_menu', 'dgf_admin_menu' );
/**
 * Register the setup screen.
 */
function dgf_admin_menu() {
	add_theme_page( __( 'DGF Setup', 'dgf' ), __( 'DGF Setup', 'dgf' ), 'edit_theme_options', 'dgf-setup', 'dgf_setup_screen' );
}

add_action( 'admin_post_dgf_setup', 'dgf_setup_actions' );
/**
 * Handle setup-screen buttons.
 */
function dgf_setup_actions() {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		wp_die( esc_html__( 'Not allowed.', 'dgf' ) );
	}
	check_admin_referer( 'dgf_setup' );
	$task    = isset( $_POST['task'] ) ? sanitize_key( $_POST['task'] ) : '';
	$message = '';

	if ( 'install' === $task ) {
		$r       = dgf_install();
		$message = sprintf( __( 'Done: %1$d pages created, %2$d already existed. Menus and front page are set.', 'dgf' ), $r['created'], $r['existing'] );
	} elseif ( 'rebuild' === $task && ! empty( $_POST['slug'] ) ) {
		$slug = sanitize_title( wp_unslash( $_POST['slug'] ) );
		dgf_install_pages( array( $slug ) );
		$message = sprintf( __( 'Rebuilt "%s" from the starter content.', 'dgf' ), $slug );
	} elseif ( 'import' === $task ) {
		$r       = dgf_import_images();
		$message = sprintf( __( 'Images: %1$d imported, %2$d featured images set, %3$d skipped (no matching page). %4$s', 'dgf' ), $r['imported'], $r['featured'], $r['skipped'], implode( ' ', array_slice( $r['notes'], 0, 10 ) ) );
	} elseif ( 'whatsapp' === $task ) {
		$number = isset( $_POST['whatsapp'] ) ? sanitize_text_field( wp_unslash( $_POST['whatsapp'] ) ) : '';
		set_theme_mod( 'dgf_whatsapp_number', $number );
		$message = $number ? __( 'WhatsApp number saved. Every quote button now opens WhatsApp.', 'dgf' ) : __( 'WhatsApp number cleared.', 'dgf' );
	}

	set_transient( 'dgf_setup_notice', $message, 60 );
	wp_safe_redirect( admin_url( 'themes.php?page=dgf-setup' ) );
	exit;
}

/**
 * Setup screen.
 */
function dgf_setup_screen() {
	$notice = get_transient( 'dgf_setup_notice' );
	delete_transient( 'dgf_setup_notice' );
	$data   = dgf_pages_data();
	$upload = wp_upload_dir();
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Dubai Gym Flooring — Setup', 'dgf' ); ?></h1>
		<?php if ( $notice ) : ?>
			<div class="notice notice-success is-dismissible"><p><?php echo esc_html( $notice ); ?></p></div>
		<?php endif; ?>

		<div class="card" style="max-width:none">
			<h2><?php esc_html_e( '1. WhatsApp number (all leads go here)', 'dgf' ); ?></h2>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<?php wp_nonce_field( 'dgf_setup' ); ?>
				<input type="hidden" name="action" value="dgf_setup"><input type="hidden" name="task" value="whatsapp">
				<input type="text" name="whatsapp" value="<?php echo esc_attr( dgf_opt( 'whatsapp_number' ) ); ?>" placeholder="9715XXXXXXXX" class="regular-text">
				<?php submit_button( __( 'Save number', 'dgf' ), 'primary', 'submit', false ); ?>
				<p class="description"><?php esc_html_e( 'International format without + or spaces. More contact fields: Appearance → Customize → Dubai Gym Flooring.', 'dgf' ); ?></p>
			</form>
		</div>

		<div class="card" style="max-width:none">
			<h2><?php esc_html_e( '2. Pages, menus & front page', 'dgf' ); ?></h2>
			<p><?php esc_html_e( 'Creates any of the 45 pages that are missing, the header/footer menus and sets the Home page. Existing pages and your edits are never overwritten.', 'dgf' ); ?></p>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<?php wp_nonce_field( 'dgf_setup' ); ?>
				<input type="hidden" name="action" value="dgf_setup"><input type="hidden" name="task" value="install">
				<?php submit_button( __( 'Install / repair pages & menus', 'dgf' ), 'primary', 'submit', false ); ?>
			</form>
		</div>

		<div class="card" style="max-width:none">
			<h2><?php esc_html_e( '3. Import page images', 'dgf' ); ?></h2>
			<p>
				<?php
				printf(
					/* translators: %s: folder path */
					esc_html__( 'Upload your photos (e.g. the Google Drive "GYM FLOORINGS" folder) by FTP / File Manager to %s. Name each file or sub-folder after the page it belongs to — e.g. "rubber-gym-flooring.jpg", "rubber-gym-flooring-2.jpg" or a folder "rubber-gym-flooring/". Common names such as "EPDM", "Turf", "Interlocking" or "Deadlift" are matched automatically. The first image becomes the page’s hero/featured image; the rest appear in that page’s gallery.', 'dgf' ),
					'<code>' . esc_html( trailingslashit( $upload['basedir'] ) . 'dgf-import/' ) . '</code>'
				);
				?>
			</p>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<?php wp_nonce_field( 'dgf_setup' ); ?>
				<input type="hidden" name="action" value="dgf_setup"><input type="hidden" name="task" value="import">
				<?php submit_button( __( 'Import images now', 'dgf' ), 'secondary', 'submit', false ); ?>
			</form>
		</div>

		<h2><?php esc_html_e( 'All pages', 'dgf' ); ?></h2>
		<table class="widefat striped">
			<thead><tr><th><?php esc_html_e( 'Page', 'dgf' ); ?></th><th><?php esc_html_e( 'Type', 'dgf' ); ?></th><th><?php esc_html_e( 'Hero image', 'dgf' ); ?></th><th><?php esc_html_e( 'Image file name to use', 'dgf' ); ?></th><th><?php esc_html_e( 'Actions', 'dgf' ); ?></th></tr></thead>
			<tbody>
			<?php
			$n = 0;
			foreach ( $data as $slug => $page ) :
				++$n;
				$post = dgf_get_page( $slug );
				?>
				<tr>
					<td><?php echo (int) $n; ?>. <strong><?php echo esc_html( $page['title'] ); ?></strong><br><code>/<?php echo esc_html( dgf_page_path( $slug ) ); ?>/</code></td>
					<td><?php echo esc_html( $page['type'] ); ?></td>
					<td><?php echo $post && has_post_thumbnail( $post ) ? get_the_post_thumbnail( $post, array( 80, 60 ) ) : '—'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></td>
					<td><code><?php echo esc_html( $slug ); ?>.jpg</code></td>
					<td>
						<?php if ( $post ) : ?>
							<a class="button button-small" href="<?php echo esc_url( get_edit_post_link( $post ) ); ?>"><?php esc_html_e( 'Edit', 'dgf' ); ?></a>
							<a class="button button-small" href="<?php echo esc_url( get_permalink( $post ) ); ?>" target="_blank"><?php esc_html_e( 'View', 'dgf' ); ?></a>
							<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="display:inline" onsubmit="return confirm('<?php echo esc_js( __( 'Replace this page’s content with the starter content? Your edits on this page will be lost.', 'dgf' ) ); ?>');">
								<?php wp_nonce_field( 'dgf_setup' ); ?>
								<input type="hidden" name="action" value="dgf_setup"><input type="hidden" name="task" value="rebuild"><input type="hidden" name="slug" value="<?php echo esc_attr( $slug ); ?>">
								<button class="button button-small button-link-delete" type="submit"><?php esc_html_e( 'Rebuild', 'dgf' ); ?></button>
							</form>
						<?php else : ?>
							<em><?php esc_html_e( 'Missing — run Install', 'dgf' ); ?></em>
						<?php endif; ?>
					</td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
	</div>
	<?php
}
