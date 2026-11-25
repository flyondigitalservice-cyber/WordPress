<?php
/**
 * Settings screen: Properties > Sales & Projects.
 *
 * @package PropzedProjects
 */

defined( 'ABSPATH' ) || exit;

/**
 * Defaults.
 *
 * @return array
 */
function pzs_setting_defaults() {
	return array(
		'sold_mode'        => 'end',  // end | normal | hide (listing pages).
		'sold_banner'      => 1,
		'auto_sold'        => 1,      // Mark a project Sold when every unit is booked.
		'scarcity'         => 1,      // "12 units left" chip on cards.
		'assistant'        => 1,
		'assistant_name'   => 'Propzed Assistant',
		'assistant_hello'  => 'Hi! I can help you find offices, warehouses, hospitals, villas and new residential projects. What are you looking for?',
		'llms'             => 1,
		'guides_category'  => 'guides',
	);
}

/**
 * Read one setting.
 *
 * @param string $key Key.
 * @return mixed
 */
function pzs_setting( $key ) {
	$saved    = get_option( 'pzs_settings', array() );
	$defaults = pzs_setting_defaults();
	$saved    = is_array( $saved ) ? $saved : array();
	return array_key_exists( $key, $saved ) ? $saved[ $key ] : ( isset( $defaults[ $key ] ) ? $defaults[ $key ] : '' );
}

add_action(
	'admin_menu',
	static function () {
		add_submenu_page( 'edit.php?post_type=property', __( 'Sales & Projects', 'propzed-projects' ), __( 'Sales & Projects', 'propzed-projects' ), 'manage_options', 'pzs-settings', 'pzs_render_settings' );
	}
);

add_action(
	'admin_post_pzs_settings',
	static function () {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You are not allowed to do this.', 'propzed-projects' ) );
		}
		check_admin_referer( 'pzs_settings' );
		$in  = isset( $_POST['pzs'] ) && is_array( $_POST['pzs'] ) ? wp_unslash( $_POST['pzs'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$out = array();
		foreach ( array( 'sold_banner', 'auto_sold', 'scarcity', 'assistant', 'llms' ) as $key ) {
			$out[ $key ] = empty( $in[ $key ] ) ? 0 : 1;
		}
		$out['sold_mode']       = isset( $in['sold_mode'] ) && in_array( $in['sold_mode'], array( 'end', 'normal', 'hide' ), true ) ? $in['sold_mode'] : 'end';
		$out['assistant_name']  = isset( $in['assistant_name'] ) ? sanitize_text_field( $in['assistant_name'] ) : '';
		$out['assistant_hello'] = isset( $in['assistant_hello'] ) ? sanitize_textarea_field( $in['assistant_hello'] ) : '';
		$out['guides_category'] = isset( $in['guides_category'] ) ? sanitize_title( $in['guides_category'] ) : 'guides';
		update_option( 'pzs_settings', $out );

		if ( isset( $_POST['pzs_llms_now'] ) && function_exists( 'pzs_write_llms' ) ) {
			pzs_write_llms();
		}
		$extra = '';
		if ( isset( $_POST['pzs_import_guides'] ) ) {
			$extra = '&guides=' . pzs_import_guides();
		}
		wp_safe_redirect( admin_url( 'edit.php?post_type=property&page=pzs-settings&saved=1' . $extra ) );
		exit;
	}
);

/**
 * Parse one guide file: an HTML comment with "key: value" lines, then the body.
 *
 * @param string $file Path.
 * @return array|null
 */
function pzs_parse_guide( $file ) {
	$raw = (string) file_get_contents( $file ); // phpcs:ignore WordPress.WP.AlternativeFunctions
	if ( ! preg_match( '/^<!--(.*?)-->\s*(.*)$/s', $raw, $m ) ) {
		return null;
	}
	$meta = array();
	foreach ( preg_split( '/\R/', trim( $m[1] ) ) as $line ) {
		if ( false !== strpos( $line, ':' ) ) {
			list( $k, $v )          = array_map( 'trim', explode( ':', $line, 2 ) );
			$meta[ strtolower( $k ) ] = $v;
		}
	}
	if ( empty( $meta['title'] ) || empty( $meta['slug'] ) ) {
		return null;
	}
	$meta['content'] = trim( $m[2] );
	return $meta;
}

/**
 * Create the bundled guides (content/guides/*.html) as drafts. Skips slugs that exist.
 *
 * @return int Drafts created.
 */
function pzs_import_guides() {
	$created = 0;
	foreach ( glob( PZS_DIR . 'content/guides/*.html' ) as $file ) {
		$g = pzs_parse_guide( $file );
		if ( ! $g ) {
			continue;
		}
		$exists = get_posts(
			array(
				'name'        => $g['slug'],
				'post_type'   => 'post',
				'post_status' => 'any',
				'numberposts' => 1,
				'fields'      => 'ids',
			)
		);
		if ( $exists ) {
			continue;
		}
		$cat_slug = ! empty( $g['category'] ) ? $g['category'] : pzs_setting( 'guides_category' );
		$cat      = get_category_by_slug( $cat_slug );
		$cat_id   = $cat ? (int) $cat->term_id : 0;
		if ( ! $cat_id ) {
			$new    = wp_insert_term( __( 'Buying Guides', 'propzed-projects' ), 'category', array( 'slug' => $cat_slug ) );
			$cat_id = is_wp_error( $new ) ? 0 : (int) $new['term_id'];
		}
		$id = wp_insert_post(
			array(
				'post_type'     => 'post',
				'post_status'   => 'draft',
				'post_title'    => $g['title'],
				'post_name'     => $g['slug'],
				'post_excerpt'  => isset( $g['excerpt'] ) ? $g['excerpt'] : '',
				'post_content'  => $g['content'],
				'post_category' => $cat_id ? array( $cat_id ) : array(),
				'tags_input'    => isset( $g['tags'] ) ? array_map( 'trim', explode( ',', $g['tags'] ) ) : array(),
			)
		);
		if ( $id && ! is_wp_error( $id ) ) {
			++$created;
		}
	}
	return $created;
}

/**
 * Render settings.
 */
function pzs_render_settings() {
	$checkbox = static function ( $key, $label, $help = '' ) {
		printf(
			'<tr><th scope="row">%1$s</th><td><label><input type="checkbox" name="pzs[%2$s]" value="1" %3$s> %4$s</label>%5$s</td></tr>',
			esc_html( $label ),
			esc_attr( $key ),
			checked( (bool) pzs_setting( $key ), true, false ),
			esc_html__( 'On', 'propzed-projects' ),
			$help ? '<p class="description">' . esc_html( $help ) . '</p>' : ''
		);
	};
	$sold_count = 0;
	$sold_term  = get_term_by( 'slug', 'sold', 'property_status' );
	$sold_count = $sold_term ? (int) $sold_term->count : 0;
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Sales, Projects & Assistant', 'propzed-projects' ); ?></h1>
		<?php if ( isset( $_GET['saved'] ) ) : // phpcs:ignore WordPress.Security.NonceVerification ?>
			<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Settings saved.', 'propzed-projects' ); ?></p></div>
		<?php endif; ?>
		<?php if ( isset( $_GET['guides'] ) ) : // phpcs:ignore WordPress.Security.NonceVerification ?>
			<div class="notice notice-info is-dismissible"><p>
				<?php
				/* translators: %d: number of drafts */
				echo esc_html( sprintf( __( '%d guide draft(s) created. Existing guides were left unchanged.', 'propzed-projects' ), absint( $_GET['guides'] ) ) ); // phpcs:ignore WordPress.Security.NonceVerification
				?>
				<a href="<?php echo esc_url( admin_url( 'edit.php?post_status=draft&post_type=post' ) ); ?>"><?php esc_html_e( 'Review drafts', 'propzed-projects' ); ?></a>
			</p></div>
		<?php endif; ?>
		<p>
			<a class="button" href="<?php echo esc_url( admin_url( 'edit.php?post_type=property&property_status=sold' ) ); ?>">
				<?php echo esc_html( sprintf( /* translators: %d: count */ __( 'Sold listings: %d', 'propzed-projects' ), $sold_count ) ); ?>
			</a>
			<a class="button" href="<?php echo esc_url( home_url( '/llms.txt' ) ); ?>" target="_blank" rel="noopener">/llms.txt</a>
		</p>

		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="pzs_settings">
			<?php wp_nonce_field( 'pzs_settings' ); ?>
			<h2><?php esc_html_e( 'Sold & leased listings', 'propzed-projects' ); ?></h2>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><?php esc_html_e( 'On listing and search pages', 'propzed-projects' ); ?></th>
					<td>
						<?php
						$modes = array(
							'end'    => __( 'Show sold listings after the available ones, with a SOLD ribbon (recommended: shows your track record)', 'propzed-projects' ),
							'normal' => __( 'Keep their normal position, with a SOLD ribbon', 'propzed-projects' ),
							'hide'   => __( 'Hide them from listing pages (the property page itself stays online)', 'propzed-projects' ),
						);
						foreach ( $modes as $value => $label ) {
							printf( '<label style="display:block;margin-bottom:6px"><input type="radio" name="pzs[sold_mode]" value="%1$s" %2$s> %3$s</label>', esc_attr( $value ), checked( pzs_setting( 'sold_mode' ), $value, false ), esc_html( $label ) );
						}
						?>
					</td>
				</tr>
				<?php
				$checkbox( 'sold_banner', __( '"This property is sold" banner', 'propzed-projects' ), __( 'Shown on the property page with links to similar listings and the requirement form, so visitors from Google still convert.', 'propzed-projects' ) );
				$checkbox( 'auto_sold', __( 'Auto-mark projects sold out', 'propzed-projects' ), __( 'When every unit in a project inventory is booked or sold, the property is marked Sold automatically.', 'propzed-projects' ) );
				$checkbox( 'scarcity', __( '"Units left" chip on property cards', 'propzed-projects' ), __( 'For projects with an inventory, e.g. "8 of 120 units left".', 'propzed-projects' ) );
				?>
			</table>

			<h2><?php esc_html_e( 'Property assistant (chat widget)', 'propzed-projects' ); ?></h2>
			<table class="form-table" role="presentation">
				<?php $checkbox( 'assistant', __( 'Show the assistant', 'propzed-projects' ), __( 'Guided chat that searches live listings and sends leads to Enquiries (same inbox and email alert as the forms). No AI subscription needed.', 'propzed-projects' ) ); ?>
				<tr>
					<th scope="row"><label for="pzs-name"><?php esc_html_e( 'Assistant name', 'propzed-projects' ); ?></label></th>
					<td><input type="text" id="pzs-name" class="regular-text" name="pzs[assistant_name]" value="<?php echo esc_attr( pzs_setting( 'assistant_name' ) ); ?>"></td>
				</tr>
				<tr>
					<th scope="row"><label for="pzs-hello"><?php esc_html_e( 'Greeting', 'propzed-projects' ); ?></label></th>
					<td><textarea id="pzs-hello" class="large-text" rows="3" name="pzs[assistant_hello]"><?php echo esc_textarea( pzs_setting( 'assistant_hello' ) ); ?></textarea></td>
				</tr>
			</table>

			<h2><?php esc_html_e( 'Search & AI visibility', 'propzed-projects' ); ?></h2>
			<table class="form-table" role="presentation">
				<?php $checkbox( 'llms', __( 'Write /llms.txt', 'propzed-projects' ), __( 'A plain-text summary of live listings, locations and guides for ChatGPT, Perplexity, Claude and Gemini. Replaces the basic file from the Hostinger plugin and refreshes itself when listings or posts change.', 'propzed-projects' ) ); ?>
				<tr>
					<th scope="row"><label for="pzs-cat"><?php esc_html_e( 'Guides category slug', 'propzed-projects' ); ?></label></th>
					<td><input type="text" id="pzs-cat" name="pzs[guides_category]" value="<?php echo esc_attr( pzs_setting( 'guides_category' ) ); ?>">
					<p class="description"><?php esc_html_e( 'Posts in this category are listed as buying guides in llms.txt and the assistant.', 'propzed-projects' ); ?></p></td>
				</tr>
			</table>
			<p>
				<?php submit_button( __( 'Save settings', 'propzed-projects' ), 'primary', 'submit', false ); ?>
				<button class="button" type="submit" name="pzs_llms_now" value="1"><?php esc_html_e( 'Save and rebuild llms.txt now', 'propzed-projects' ); ?></button>
			</p>

			<h2><?php esc_html_e( 'Starter guides', 'propzed-projects' ); ?></h2>
			<p class="description"><?php esc_html_e( 'Six buying and investment guides (budget flats in Mumbai and Navi Mumbai, villas in Karjat, Alibaug and Khopoli, total buying cost in Maharashtra) with FAQs, live listings, an EMI calculator and advisor boxes. They are created as drafts for you to review; guides that already exist are skipped.', 'propzed-projects' ); ?></p>
			<p><button class="button" type="submit" name="pzs_import_guides" value="1"><?php esc_html_e( 'Create starter guides as drafts', 'propzed-projects' ); ?></button></p>
		</form>
	</div>
	<?php
}
