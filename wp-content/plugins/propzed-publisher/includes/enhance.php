<?php
/**
 * Front-end layer on top of the Propzed theme (no theme files are changed):
 *
 *   assets/css/pzp.css  Accessibility fixes (contrast, tap targets, focus), mobile
 *                       polish, plus styles for builder pages and the publisher area.
 *   assets/js/pzp.js    Form helpers, WhatsApp shortcut on cards, keyboard and
 *                       screen-reader fixes.
 *
 * Also: header menu links, a publisher call-to-action on the "List Your Property"
 * page, and the settings screen (Properties > Publisher Settings).
 *
 * Switch the whole layer off with the "enhance" setting if it ever needs to be ruled out.
 *
 * @package PropzedPublisher
 */

defined( 'ABSPATH' ) || exit;

/**
 * Enqueue the CSS and JS after the theme's own files.
 */
function pzp_assets() {
	$deps_css = wp_style_is( 'propzed-main', 'registered' ) ? array( 'propzed-main' ) : array();
	$deps_js  = wp_script_is( 'propzed-main', 'registered' ) ? array( 'propzed-main' ) : array();

	wp_enqueue_style( 'pzp', PZP_URL . 'assets/css/pzp.css', $deps_css, PZP_VERSION );
	wp_enqueue_script( 'pzp', PZP_URL . 'assets/js/pzp.js', $deps_js, PZP_VERSION, true );
	wp_add_inline_script(
		'pzp',
		'window.pzpData = ' . wp_json_encode(
			array(
				'enhance'      => (bool) pzp_setting( 'enhance' ),
				'cardWhatsapp' => (bool) pzp_setting( 'card_whatsapp' ),
				'whatsapp'     => preg_replace( '/\D/', '', (string) propzed_option( 'whatsapp' ) ),
				'i18n'         => array(
					'phone'      => __( 'Enter a valid 10-digit mobile number.', 'propzed-publisher' ),
					'sending'    => __( 'Sending…', 'propzed-publisher' ),
					'waCard'     => __( 'WhatsApp', 'propzed-publisher' ),
					/* translators: %s: property title */
					'waLabel'    => __( 'Enquire about %s on WhatsApp', 'propzed-publisher' ),
					/* translators: 1: property title 2: URL */
					'waText'     => __( 'Hi, I am interested in: %1$s %2$s', 'propzed-publisher' ),
					'contact'    => __( 'Contact', 'propzed-publisher' ),
					'quick'      => __( 'Quick contact', 'propzed-publisher' ),
					'tooBig'     => __( 'Some photos are over 5 MB and will be skipped.', 'propzed-publisher' ),
					'tooMany'    => __( 'You can add up to 10 photos per property.', 'propzed-publisher' ),
					/* translators: %d: number of photos */
					'photos'     => __( '%d photo(s) selected', 'propzed-publisher' ),
					'noBuilders' => __( 'No builder matches your search.', 'propzed-publisher' ),
				),
			)
		) . ';',
		'before'
	);
}
add_action( 'wp_enqueue_scripts', 'pzp_assets', 20 );

/**
 * The fixes in pzp.css only apply while this body class is present.
 */
add_filter(
	'body_class',
	static function ( $classes ) {
		if ( pzp_setting( 'enhance' ) ) {
			$classes[] = 'pzp-enhance';
		}
		return $classes;
	}
);

/**
 * Header menu: add "Builders" and the publisher link (unless already in the menu).
 *
 * @param string   $items Menu HTML.
 * @param stdClass $args  Menu arguments.
 * @return string
 */
function pzp_menu_items( $items, $args ) {
	$location = empty( $args->theme_location ) ? '' : $args->theme_location;
	$label    = is_user_logged_in() && current_user_can( PZP_CAP ) ? __( 'My Listings', 'propzed-publisher' ) : __( 'Publisher Login', 'propzed-publisher' );

	// Footer menu: plain links.
	if ( 'footer' === $location ) {
		if ( pzp_setting( 'menu_builders' ) && false === strpos( $items, '/builders' ) && pzp_get_builders( 1 ) ) {
			$items .= '<li class="menu-item"><a href="' . esc_url( pzp_builders_url() ) . '">' . esc_html__( 'Builders', 'propzed-publisher' ) . '</a></li>';
		}
		if ( pzp_setting( 'menu_publisher' ) && false === strpos( $items, '/publisher' ) ) {
			$items .= '<li class="menu-item"><a href="' . esc_url( pzp_publisher_url() ) . '">' . esc_html( $label ) . '</a></li>';
		}
		return $items;
	}
	if ( 'primary' !== $location ) {
		return $items;
	}
	if ( pzp_setting( 'menu_builders' ) && false === strpos( $items, '/builders' ) && pzp_get_builders( 1 ) ) {
		$current = ( 'builders' === pzp_current_view() || is_tax( PZP_BUILDER_TAX ) );
		$items  .= sprintf(
			'<li class="menu-item pzp-menu-builders%1$s"><a href="%2$s"%3$s>%4$s</a></li>',
			$current ? ' current-menu-item' : '',
			esc_url( pzp_builders_url() ),
			'builders' === pzp_current_view() ? ' aria-current="page"' : '',
			esc_html__( 'Builders', 'propzed-publisher' )
		);
	}
	if ( pzp_setting( 'menu_publisher' ) && false === strpos( $items, '/publisher' ) ) {
		$items .= sprintf(
			'<li class="menu-item pzp-menu-publisher%1$s"><a href="%2$s"%3$s>%4$s</a></li>',
			'publisher' === pzp_current_view() ? ' current-menu-item' : '',
			esc_url( pzp_publisher_url() ),
			'publisher' === pzp_current_view() ? ' aria-current="page"' : '',
			esc_html( $label )
		);
	}
	return $items;
}
add_filter( 'wp_nav_menu_items', 'pzp_menu_items', 10, 2 );

/**
 * "List Your Property" page: invite regular sellers to open a publisher account.
 *
 * @param string $content Page content.
 * @return string
 */
function pzp_sell_page_cta( $content ) {
	if ( ! is_page_template( 'templates/template-sell.php' ) || ! in_the_loop() || ! is_main_query() ) {
		return $content;
	}
	$logged = is_user_logged_in() && current_user_can( PZP_CAP );
	$cta    = '<div class="pzp-cta-card">';
	$cta   .= '<div><strong>' . esc_html__( 'Builder, broker or owner with several properties?', 'propzed-publisher' ) . '</strong>';
	$cta   .= '<p>' . esc_html__( 'Open a free publisher account to add listings with more details, edit them any time and track their review status.', 'propzed-publisher' ) . '</p></div>';
	$cta   .= '<a class="pz-btn pz-btn--navy" href="' . esc_url( pzp_publisher_url( $logged ? array( 'view' => 'new' ) : array( 'tab' => 'register' ) ) ) . '">';
	$cta   .= esc_html( $logged ? __( 'Add a property', 'propzed-publisher' ) : __( 'Create publisher account', 'propzed-publisher' ) ) . '</a></div>';
	return $content . $cta;
}
add_filter( 'the_content', 'pzp_sell_page_cta', 20 );

/* -------------------------------------------------------------------------
 * Settings screen: Properties > Publisher Settings
 * ---------------------------------------------------------------------- */

/**
 * Checkbox settings: key => [ label, help ].
 *
 * @return array
 */
function pzp_setting_toggles() {
	return array(
		'signup'         => array( __( 'Allow new publisher sign-ups', 'propzed-publisher' ), __( 'Turn off to stop new accounts. Existing publishers can still log in.', 'propzed-publisher' ) ),
		'menu_builders'  => array( __( 'Add "Builders" to the header and footer menus', 'propzed-publisher' ), '' ),
		'menu_publisher' => array( __( 'Add "Publisher Login / My Listings" to the menus', 'propzed-publisher' ), __( 'Shown in the mobile menu and the footer menu. The desktop header keeps only "Builders" so it never wraps.', 'propzed-publisher' ) ),
		'home_builders'  => array( __( 'Show the "Top Builders" strip on the homepage', 'propzed-publisher' ), __( 'Appears once at least 3 builders have a live project.', 'propzed-publisher' ) ),
		'card_whatsapp'  => array( __( 'WhatsApp shortcut on property cards', 'propzed-publisher' ), '' ),
		'enhance'        => array( __( 'Mobile, accessibility and conversion layer', 'propzed-publisher' ), __( 'Contrast and tap-target fixes, form helpers. Turn off to compare with the plain theme.', 'propzed-publisher' ) ),
	);
}

add_action(
	'admin_menu',
	static function () {
		add_submenu_page( 'edit.php?post_type=property', __( 'Publisher Settings', 'propzed-publisher' ), __( 'Publisher Settings', 'propzed-publisher' ), 'manage_options', 'pzp-settings', 'pzp_render_settings' );
	}
);

/**
 * Save settings / run the builder sync.
 */
add_action(
	'admin_post_pzp_settings',
	static function () {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You are not allowed to do this.', 'propzed-publisher' ) );
		}
		check_admin_referer( 'pzp_settings' );

		$done = 'saved';
		if ( isset( $_POST['pzp_sync'] ) ) {
			pzp_sync_all_builders();
			$done = 'synced';
		} else {
			$settings = array();
			foreach ( array_keys( pzp_setting_toggles() ) as $key ) {
				$settings[ $key ] = empty( $_POST['pzp'][ $key ] ) ? 0 : 1;
			}
			$settings['max_pending'] = isset( $_POST['pzp']['max_pending'] ) ? max( 1, min( 200, absint( $_POST['pzp']['max_pending'] ) ) ) : 20;
			update_option( 'pzp_settings', $settings );
		}
		wp_safe_redirect( admin_url( 'edit.php?post_type=property&page=pzp-settings&pzp_done=' . $done ) );
		exit;
	}
);

/**
 * Render the settings screen.
 */
function pzp_render_settings() {
	$done       = isset( $_GET['pzp_done'] ) ? sanitize_key( wp_unslash( $_GET['pzp_done'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
	$publishers = count_users();
	$count      = isset( $publishers['avail_roles'][ PZP_ROLE ] ) ? (int) $publishers['avail_roles'][ PZP_ROLE ] : 0;
	$pending    = (int) wp_count_posts( 'property' )->pending;
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Publisher & Builder Settings', 'propzed-publisher' ); ?></h1>
		<?php if ( 'saved' === $done ) : ?>
			<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Settings saved.', 'propzed-publisher' ); ?></p></div>
		<?php elseif ( 'synced' === $done ) : ?>
			<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Builders re-linked from the "Developer / Builder" field of every property.', 'propzed-publisher' ); ?></p></div>
		<?php endif; ?>

		<p>
			<a class="button" href="<?php echo esc_url( pzp_publisher_url() ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Publisher area', 'propzed-publisher' ); ?></a>
			<a class="button" href="<?php echo esc_url( pzp_builders_url() ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Builders directory', 'propzed-publisher' ); ?></a>
			<a class="button" href="<?php echo esc_url( admin_url( 'edit.php?post_status=pending&post_type=property' ) ); ?>">
				<?php
				/* translators: %d: number of listings */
				echo esc_html( sprintf( __( 'Listings waiting for review: %d', 'propzed-publisher' ), $pending ) );
				?>
			</a>
			<a class="button" href="<?php echo esc_url( admin_url( 'users.php?role=' . PZP_ROLE ) ); ?>">
				<?php
				/* translators: %d: number of accounts */
				echo esc_html( sprintf( __( 'Publisher accounts: %d', 'propzed-publisher' ), $count ) );
				?>
			</a>
		</p>

		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="pzp_settings">
			<?php wp_nonce_field( 'pzp_settings' ); ?>
			<table class="form-table" role="presentation">
				<?php foreach ( pzp_setting_toggles() as $key => $toggle ) : ?>
					<tr>
						<th scope="row"><?php echo esc_html( $toggle[0] ); ?></th>
						<td>
							<label><input type="checkbox" name="pzp[<?php echo esc_attr( $key ); ?>]" value="1" <?php checked( (bool) pzp_setting( $key ) ); ?>> <?php esc_html_e( 'On', 'propzed-publisher' ); ?></label>
							<?php if ( $toggle[1] ) : ?>
								<p class="description"><?php echo esc_html( $toggle[1] ); ?></p>
							<?php endif; ?>
						</td>
					</tr>
				<?php endforeach; ?>
				<tr>
					<th scope="row"><label for="pzp-max-pending"><?php esc_html_e( 'Listings a publisher may have in review', 'propzed-publisher' ); ?></label></th>
					<td><input type="number" id="pzp-max-pending" name="pzp[max_pending]" min="1" max="200" value="<?php echo esc_attr( (int) pzp_setting( 'max_pending' ) ); ?>" class="small-text"></td>
				</tr>
			</table>
			<?php submit_button(); ?>
		</form>

		<h2><?php esc_html_e( 'Builders', 'propzed-publisher' ); ?></h2>
		<p><?php esc_html_e( 'Builder pages are created from the "Developer / Builder" field of published properties. Run this after bulk-editing properties.', 'propzed-publisher' ); ?></p>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="pzp_settings">
			<?php wp_nonce_field( 'pzp_settings' ); ?>
			<button class="button" type="submit" name="pzp_sync" value="1"><?php esc_html_e( 'Re-link builders now', 'propzed-publisher' ); ?></button>
			<a class="button" href="<?php echo esc_url( admin_url( 'edit-tags.php?taxonomy=' . PZP_BUILDER_TAX . '&post_type=property' ) ); ?>"><?php esc_html_e( 'Edit builder logos and details', 'propzed-publisher' ); ?></a>
		</form>
	</div>
	<?php
}
