<?php
/**
 * Publisher area: /publisher/
 *
 *   Logged out            Login + create account
 *   ?view=new             Add a property
 *   ?view=edit&id=123     Edit one of your own properties
 *   ?view=profile         Your contact details
 *   (default)             Dashboard: your listings and their review status
 *
 * To customise, copy this file to your (child) theme as
 * propzed-publisher/publisher.php.
 *
 * @package PropzedPublisher
 */

defined( 'ABSPATH' ) || exit;

get_header();

$pzp_user   = wp_get_current_user();
$pzp_logged = is_user_logged_in();
$pzp_can    = $pzp_logged && current_user_can( PZP_CAP );
$pzp_view   = isset( $_GET['view'] ) ? sanitize_key( wp_unslash( $_GET['view'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
$pzp_tab    = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
$pzp_post   = null;

if ( $pzp_can && 'edit' === $pzp_view ) {
	$pzp_post = pzp_get_own_property( isset( $_GET['id'] ) ? absint( $_GET['id'] ) : 0, $pzp_user->ID ); // phpcs:ignore WordPress.Security.NonceVerification
	if ( ! $pzp_post ) {
		$pzp_view = '';
	}
}
if ( ! in_array( $pzp_view, array( 'new', 'edit', 'profile' ), true ) ) {
	$pzp_view = '';
}

$pzp_titles = array(
	''        => $pzp_can ? __( 'My Listings', 'propzed-publisher' ) : __( 'Publisher Account', 'propzed-publisher' ),
	'new'     => __( 'Add a Property', 'propzed-publisher' ),
	'edit'    => __( 'Edit Property', 'propzed-publisher' ),
	'profile' => __( 'My Details', 'propzed-publisher' ),
);
$pzp_action = admin_url( 'admin-post.php' );
?>

<section class="pz-page-head">
	<div class="pz-container">
		<nav class="pz-breadcrumb" aria-label="<?php esc_attr_e( 'Breadcrumb', 'propzed-publisher' ); ?>">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'propzed-publisher' ); ?></a> /
			<?php if ( $pzp_view ) : ?>
				<a href="<?php echo esc_url( pzp_publisher_url() ); ?>"><?php esc_html_e( 'My Listings', 'propzed-publisher' ); ?></a> /
			<?php endif; ?>
			<span aria-current="page"><?php echo esc_html( $pzp_titles[ $pzp_can ? $pzp_view : '' ] ); ?></span>
		</nav>
		<h1><?php echo esc_html( $pzp_titles[ $pzp_can ? $pzp_view : '' ] ); ?></h1>
		<div class="pz-page-head__desc">
			<p>
				<?php
				echo esc_html(
					$pzp_can
						? __( 'Every listing is checked by the Propzed team before it goes live.', 'propzed-publisher' )
						: __( 'For owners, builders and brokers: list your commercial properties and manage them in one place. Listings go live after verification by our team.', 'propzed-publisher' )
				);
				?>
			</p>
		</div>
	</div>
</section>

<div class="pz-container pzp-pub">
	<?php pzp_notice(); ?>

	<?php if ( ! $pzp_logged ) : ?>

		<div class="pzp-auth">
			<form class="pz-form pzp-auth__form" action="<?php echo esc_url( $pzp_action ); ?>" method="post" id="pzp-login" aria-labelledby="pzp-login-title">
				<h2 class="pz-form__title" id="pzp-login-title"><?php esc_html_e( 'Log in', 'propzed-publisher' ); ?></h2>
				<input type="hidden" name="action" value="pzp_login">
				<label><span><?php esc_html_e( 'Email', 'propzed-publisher' ); ?> *</span><input type="email" name="pzp_login" required autocomplete="username" inputmode="email"></label>
				<label><span><?php esc_html_e( 'Password', 'propzed-publisher' ); ?> *</span><input type="password" name="pzp_password" required autocomplete="current-password" class="pzp-input"></label>
				<button class="pz-btn pz-btn--navy pz-btn--block" type="submit"><?php esc_html_e( 'Log in', 'propzed-publisher' ); ?></button>
				<p class="pz-form__note"><a href="<?php echo esc_url( wp_lostpassword_url( pzp_publisher_url() ) ); ?>"><?php esc_html_e( 'Forgot your password?', 'propzed-publisher' ); ?></a></p>
			</form>

			<?php if ( pzp_setting( 'signup' ) ) : ?>
				<form class="pz-form pzp-auth__form" action="<?php echo esc_url( $pzp_action ); ?>" method="post" id="pzp-register" aria-labelledby="pzp-register-title"<?php echo 'register' === $pzp_tab ? ' data-pzp-focus' : ''; ?>>
					<h2 class="pz-form__title" id="pzp-register-title"><?php esc_html_e( 'Create a free publisher account', 'propzed-publisher' ); ?></h2>
					<input type="hidden" name="action" value="pzp_register">
					<?php wp_nonce_field( 'pzp_register', 'pzp_nonce' ); ?>
					<div class="pz-hp" aria-hidden="true"><input type="text" name="pzp_website" tabindex="-1" autocomplete="off"></div>

					<div class="pz-form__grid">
						<label><span><?php esc_html_e( 'Full name', 'propzed-publisher' ); ?> *</span><input type="text" name="pzp_name" required autocomplete="name"></label>
						<label><span><?php esc_html_e( 'Mobile number', 'propzed-publisher' ); ?> *</span><input type="tel" name="pzp_phone" required pattern="[0-9+\s\-]{10,16}" inputmode="tel" autocomplete="tel" placeholder="98XXXXXXXX"></label>
						<label><span><?php esc_html_e( 'Email', 'propzed-publisher' ); ?> *</span><input type="email" name="pzp_email" required autocomplete="email" inputmode="email"></label>
						<label><span><?php esc_html_e( 'Password (8 or more characters)', 'propzed-publisher' ); ?> *</span><input type="password" name="pzp_password" required minlength="8" autocomplete="new-password" class="pzp-input"></label>
						<label><span><?php esc_html_e( 'You are', 'propzed-publisher' ); ?></span>
							<select name="pzp_type" class="pz-select">
								<?php foreach ( pzp_publisher_types() as $pzp_type ) : ?>
									<option><?php echo esc_html( $pzp_type ); ?></option>
								<?php endforeach; ?>
							</select>
						</label>
						<label><span><?php esc_html_e( 'Company (optional)', 'propzed-publisher' ); ?></span><input type="text" name="pzp_company" autocomplete="organization"></label>
					</div>
					<label class="pzp-check"><input type="checkbox" name="pzp_terms" value="1" required> <span><?php esc_html_e( 'I confirm I am authorised to list these properties and agree to be contacted by the Propzed team for verification.', 'propzed-publisher' ); ?> *</span></label>
					<button class="pz-btn pz-btn--gold pz-btn--block" type="submit"><?php esc_html_e( 'Create account', 'propzed-publisher' ); ?></button>
					<p class="pz-form__note"><?php esc_html_e( 'No listing fee. Your listings go live after verification.', 'propzed-publisher' ); ?></p>
				</form>
			<?php else : ?>
				<div class="pz-form pzp-auth__form">
					<h2 class="pz-form__title"><?php esc_html_e( 'New publisher accounts', 'propzed-publisher' ); ?></h2>
					<p><?php esc_html_e( 'Sign-ups are handled by our office right now. Call us to get an account, or send your property through the form.', 'propzed-publisher' ); ?></p>
					<a class="pz-btn pz-btn--gold pz-btn--block" href="<?php echo esc_url( propzed_template_url( 'templates/template-sell.php' ) ); ?>"><?php esc_html_e( 'List a property without an account', 'propzed-publisher' ); ?></a>
				</div>
			<?php endif; ?>
		</div>

	<?php elseif ( ! $pzp_can ) : ?>

		<div class="pz-empty">
			<h2><?php esc_html_e( 'This account is not a publisher account', 'propzed-publisher' ); ?></h2>
			<p><?php esc_html_e( 'Please call our office to enable property submissions for this login.', 'propzed-publisher' ); ?></p>
			<a class="pz-btn pz-btn--gold" href="<?php echo esc_url( propzed_phone_link() ); ?>"><?php echo esc_html( propzed_option( 'phone' ) ); ?></a>
		</div>

	<?php else : ?>

		<nav class="pzp-tabs" aria-label="<?php esc_attr_e( 'Publisher menu', 'propzed-publisher' ); ?>">
			<a href="<?php echo esc_url( pzp_publisher_url() ); ?>"<?php echo '' === $pzp_view ? ' class="is-active" aria-current="page"' : ''; ?>><?php esc_html_e( 'My Listings', 'propzed-publisher' ); ?></a>
			<a href="<?php echo esc_url( pzp_publisher_url( array( 'view' => 'new' ) ) ); ?>"<?php echo 'new' === $pzp_view ? ' class="is-active" aria-current="page"' : ''; ?>><?php esc_html_e( 'Add Property', 'propzed-publisher' ); ?></a>
			<a href="<?php echo esc_url( pzp_publisher_url( array( 'view' => 'profile' ) ) ); ?>"<?php echo 'profile' === $pzp_view ? ' class="is-active" aria-current="page"' : ''; ?>><?php esc_html_e( 'My Details', 'propzed-publisher' ); ?></a>
			<a href="<?php echo esc_url( wp_logout_url( pzp_publisher_url() ) ); ?>"><?php esc_html_e( 'Log out', 'propzed-publisher' ); ?></a>
		</nav>

		<?php if ( 'profile' === $pzp_view ) : ?>

			<h2 class="pz-sr"><?php esc_html_e( 'Your contact details', 'propzed-publisher' ); ?></h2>
			<form class="pz-form pz-form--wide pzp-narrow" action="<?php echo esc_url( $pzp_action ); ?>" method="post">
				<input type="hidden" name="action" value="pzp_profile">
				<?php wp_nonce_field( 'pzp_profile', 'pzp_nonce' ); ?>
				<div class="pz-form__grid">
					<label><span><?php esc_html_e( 'Full name', 'propzed-publisher' ); ?> *</span><input type="text" name="pzp_name" required autocomplete="name" value="<?php echo esc_attr( $pzp_user->display_name ); ?>"></label>
					<label><span><?php esc_html_e( 'Mobile number', 'propzed-publisher' ); ?> *</span><input type="tel" name="pzp_phone" required pattern="[0-9+\s\-]{10,16}" inputmode="tel" autocomplete="tel" value="<?php echo esc_attr( get_user_meta( $pzp_user->ID, 'pzp_phone', true ) ); ?>"></label>
					<label><span><?php esc_html_e( 'Company', 'propzed-publisher' ); ?></span><input type="text" name="pzp_company" autocomplete="organization" value="<?php echo esc_attr( get_user_meta( $pzp_user->ID, 'pzp_company', true ) ); ?>"></label>
					<label><span><?php esc_html_e( 'Email (login)', 'propzed-publisher' ); ?></span><input type="email" value="<?php echo esc_attr( $pzp_user->user_email ); ?>" readonly aria-describedby="pzp-email-help"></label>
				</div>
				<p class="pz-form__note" id="pzp-email-help"><?php esc_html_e( 'To change your email or password, please call our office or use "Forgot your password?" on the login screen.', 'propzed-publisher' ); ?></p>
				<button class="pz-btn pz-btn--gold" type="submit"><?php esc_html_e( 'Save details', 'propzed-publisher' ); ?></button>
			</form>

		<?php elseif ( 'new' === $pzp_view || 'edit' === $pzp_view ) : ?>

			<?php
			$pzp_id     = $pzp_post ? $pzp_post->ID : 0;
			$pzp_fields = propzed_all_fields();
			$pzp_meta   = static function ( $key ) use ( $pzp_id ) {
				return $pzp_id ? (string) get_post_meta( $pzp_id, '_pz_' . $key, true ) : '';
			};
			$pzp_term   = static function ( $taxonomy ) use ( $pzp_id ) {
				$terms = $pzp_id ? get_the_terms( $pzp_id, $taxonomy ) : array();
				return ( $terms && ! is_wp_error( $terms ) ) ? (int) $terms[0]->term_id : '';
			};
			$pzp_photos = $pzp_id ? propzed_gallery_ids( $pzp_id ) : array();
			$pzp_note   = $pzp_id && 'changes' === get_post_meta( $pzp_id, '_pzp_state', true ) ? get_post_meta( $pzp_id, '_pzp_review_note', true ) : '';
			?>

			<?php if ( $pzp_note ) : ?>
				<div class="pz-notice pz-notice--error" role="status"><strong><?php esc_html_e( 'Changes requested by our team:', 'propzed-publisher' ); ?></strong> <?php echo esc_html( $pzp_note ); ?></div>
			<?php elseif ( $pzp_post && 'publish' === $pzp_post->post_status ) : ?>
				<div class="pz-notice pz-notice--success" role="status"><?php esc_html_e( 'This listing is live. If you save changes, it is hidden until our team has reviewed the update.', 'propzed-publisher' ); ?></div>
			<?php endif; ?>

			<h2 class="pz-sr"><?php esc_html_e( 'Property details', 'propzed-publisher' ); ?></h2>
			<form class="pz-form pz-form--wide" action="<?php echo esc_url( $pzp_action ); ?>" method="post" enctype="multipart/form-data" data-pzp-property-form>
				<input type="hidden" name="action" value="pzp_save_property">
				<input type="hidden" name="pzp_id" value="<?php echo esc_attr( $pzp_id ); ?>">
				<?php wp_nonce_field( 'pzp_save_property', 'pzp_nonce' ); ?>

				<fieldset>
					<legend><?php esc_html_e( '1. Basics', 'propzed-publisher' ); ?></legend>
					<div class="pz-form__grid">
						<label class="pz-form__full"><span><?php esc_html_e( 'Property title', 'propzed-publisher' ); ?> *</span><input type="text" name="pzp_title" required maxlength="150" value="<?php echo esc_attr( $pzp_post ? $pzp_post->post_title : '' ); ?>" placeholder="<?php esc_attr_e( 'e.g. 12,000 sq.ft office floor near Vashi station', 'propzed-publisher' ); ?>"></label>
						<label><span><?php esc_html_e( 'Property type', 'propzed-publisher' ); ?> *</span><?php echo str_replace( '<select ', '<select required ', propzed_term_select( 'property_type', 'pzp_type', $pzp_term( 'property_type' ), __( 'Select type', 'propzed-publisher' ), 'term_id' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></label>
						<label><span><?php esc_html_e( 'Sale / Lease', 'propzed-publisher' ); ?></span><?php echo propzed_term_select( 'property_status', 'pzp_status', $pzp_term( 'property_status' ), __( 'Select', 'propzed-publisher' ), 'term_id' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></label>
						<label><span><?php esc_html_e( 'City / Locality', 'propzed-publisher' ); ?> *</span><?php echo str_replace( '<select ', '<select required ', propzed_term_select( 'property_location', 'pzp_location', $pzp_term( 'property_location' ), __( 'Select location', 'propzed-publisher' ), 'term_id' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></label>
						<label><span><?php esc_html_e( 'Address / Landmark', 'propzed-publisher' ); ?></span><input type="text" name="pzp_meta[address]" autocomplete="street-address" value="<?php echo esc_attr( $pzp_meta( 'address' ) ); ?>"></label>
						<label><span><?php esc_html_e( 'PIN code', 'propzed-publisher' ); ?></span><input type="text" name="pzp_meta[pincode]" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" autocomplete="postal-code" value="<?php echo esc_attr( $pzp_meta( 'pincode' ) ); ?>"></label>
					</div>
				</fieldset>

				<fieldset>
					<legend><?php esc_html_e( '2. Size and price', 'propzed-publisher' ); ?></legend>
					<div class="pz-form__grid">
						<label><span><?php esc_html_e( 'Area', 'propzed-publisher' ); ?></span>
							<span class="pz-input-group">
								<input type="number" name="pzp_meta[area]" min="0" step="any" inputmode="decimal" value="<?php echo esc_attr( $pzp_meta( 'area' ) ); ?>">
								<select name="pzp_meta[area_unit]" class="pz-select" aria-label="<?php esc_attr_e( 'Area unit', 'propzed-publisher' ); ?>">
									<?php foreach ( propzed_field_options( 'area_unit' ) as $pzp_value => $pzp_label ) : ?>
										<option value="<?php echo esc_attr( $pzp_value ); ?>" <?php selected( $pzp_meta( 'area_unit' ), $pzp_value ); ?>><?php echo esc_html( $pzp_label ); ?></option>
									<?php endforeach; ?>
								</select>
							</span>
						</label>
						<label><span><?php esc_html_e( 'Expected price (₹, full amount)', 'propzed-publisher' ); ?></span>
							<span class="pz-input-group">
								<input type="number" name="pzp_meta[price]" min="0" step="any" inputmode="numeric" placeholder="25000000" value="<?php echo esc_attr( $pzp_meta( 'price' ) ); ?>" aria-describedby="pzp-price-help">
								<select name="pzp_meta[price_unit]" class="pz-select" aria-label="<?php esc_attr_e( 'Price is', 'propzed-publisher' ); ?>">
									<?php foreach ( propzed_field_options( 'price_unit' ) as $pzp_value => $pzp_label ) : ?>
										<option value="<?php echo esc_attr( $pzp_value ); ?>" <?php selected( $pzp_meta( 'price_unit' ), $pzp_value ); ?>><?php echo esc_html( $pzp_label ); ?></option>
									<?php endforeach; ?>
								</select>
							</span>
							<small id="pzp-price-help"><?php esc_html_e( 'Example: 25000000 = ₹ 2.5 Cr. Leave empty for "Price on Request".', 'propzed-publisher' ); ?></small>
						</label>
						<?php if ( isset( $pzp_fields['possession']['options'] ) ) : ?>
							<label><span><?php esc_html_e( 'Property status', 'propzed-publisher' ); ?></span>
								<select name="pzp_meta[possession]" class="pz-select">
									<?php foreach ( $pzp_fields['possession']['options'] as $pzp_value => $pzp_label ) : ?>
										<option value="<?php echo esc_attr( $pzp_value ); ?>" <?php selected( $pzp_meta( 'possession' ), $pzp_value ); ?>><?php echo esc_html( $pzp_label ); ?></option>
									<?php endforeach; ?>
								</select>
							</label>
						<?php endif; ?>
						<label><span><?php esc_html_e( 'Possession / launch date', 'propzed-publisher' ); ?></span><input type="text" name="pzp_meta[possession_date]" placeholder="Dec 2028" value="<?php echo esc_attr( $pzp_meta( 'possession_date' ) ); ?>"></label>
					</div>
				</fieldset>

				<fieldset>
					<legend><?php esc_html_e( '3. Details', 'propzed-publisher' ); ?></legend>
					<div class="pz-form__grid">
						<label><span><?php esc_html_e( 'Developer / Builder', 'propzed-publisher' ); ?></span><input type="text" name="pzp_meta[developer]" value="<?php echo esc_attr( $pzp_meta( 'developer' ) ? $pzp_meta( 'developer' ) : ( $pzp_id ? '' : get_user_meta( $pzp_user->ID, 'pzp_company', true ) ) ); ?>" aria-describedby="pzp-dev-help"><small id="pzp-dev-help"><?php esc_html_e( 'The listing is added to this builder\'s page.', 'propzed-publisher' ); ?></small></label>
						<label><span><?php esc_html_e( 'RERA / Approvals', 'propzed-publisher' ); ?></span><input type="text" name="pzp_meta[approvals]" placeholder="CIDCO / MIDC / RERA no." value="<?php echo esc_attr( $pzp_meta( 'approvals' ) ); ?>"></label>
						<label class="pz-form__full"><span><?php esc_html_e( 'Description', 'propzed-publisher' ); ?></span><textarea name="pzp_description" rows="5" placeholder="<?php esc_attr_e( 'Approvals, title status, road access, nearby highway / station / airport, current tenants…', 'propzed-publisher' ); ?>"><?php echo esc_textarea( $pzp_post ? $pzp_post->post_content : '' ); ?></textarea></label>
						<label class="pz-form__full"><span><?php esc_html_e( 'Key highlights (one per line)', 'propzed-publisher' ); ?></span><textarea name="pzp_meta[features]" rows="4" placeholder="<?php esc_attr_e( "24x7 power backup\n60 ft road frontage", 'propzed-publisher' ); ?>"><?php echo esc_textarea( $pzp_meta( 'features' ) ); ?></textarea></label>
						<label class="pz-form__full"><span><?php esc_html_e( 'YouTube video link (optional)', 'propzed-publisher' ); ?></span><input type="url" name="pzp_meta[video]" inputmode="url" placeholder="https://" value="<?php echo esc_attr( $pzp_meta( 'video' ) ); ?>"></label>
					</div>
				</fieldset>

				<fieldset>
					<legend><?php esc_html_e( '4. Photos', 'propzed-publisher' ); ?></legend>
					<?php if ( $pzp_photos ) : ?>
						<ul class="pzp-photos">
							<?php foreach ( $pzp_photos as $pzp_n => $pzp_photo ) : ?>
								<li>
									<?php echo wp_get_attachment_image( $pzp_photo, 'thumbnail', false, array( 'alt' => sprintf( /* translators: %d: photo number */ __( 'Photo %d', 'propzed-publisher' ), $pzp_n + 1 ) ) ); ?>
									<label class="pzp-check"><input type="checkbox" name="pzp_remove[]" value="<?php echo esc_attr( $pzp_photo ); ?>"> <span><?php esc_html_e( 'Remove', 'propzed-publisher' ); ?></span></label>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
					<label class="pz-form__full"><span><?php esc_html_e( 'Add photos (up to 10 in total, JPG / PNG / WebP, max 5 MB each)', 'propzed-publisher' ); ?></span><input type="file" name="pzp_images[]" accept="image/jpeg,image/png,image/webp" multiple data-pzp-photos aria-describedby="pzp-photo-status"></label>
					<p class="pz-form__note pzp-left" id="pzp-photo-status" role="status" data-pzp-photo-status><?php esc_html_e( 'The first photo is used as the cover image.', 'propzed-publisher' ); ?></p>
				</fieldset>

				<div class="pzp-form-actions">
					<button class="pz-btn pz-btn--gold" type="submit"><?php echo esc_html( $pzp_post ? __( 'Save & submit for review', 'propzed-publisher' ) : __( 'Submit property for review', 'propzed-publisher' ) ); ?></button>
					<a class="pz-btn pz-btn--outline" href="<?php echo esc_url( pzp_publisher_url() ); ?>"><?php esc_html_e( 'Cancel', 'propzed-publisher' ); ?></a>
				</div>
				<p class="pz-form__note pzp-left"><?php esc_html_e( 'Your contact details are taken from "My Details" and are shared only with the Propzed team.', 'propzed-publisher' ); ?></p>
			</form>

		<?php else : ?>

			<?php
			$pzp_listings = pzp_get_user_properties( $pzp_user->ID );
			$pzp_counts   = array( 'live' => 0, 'review' => 0, 'changes' => 0, 'withdrawn' => 0 );
			foreach ( $pzp_listings as $pzp_listing ) {
				$pzp_state = pzp_listing_state( $pzp_listing );
				++$pzp_counts[ $pzp_state[0] ];
			}
			?>

			<div class="pzp-dash-head">
				<p class="pzp-hello">
					<?php
					/* translators: %s: publisher name */
					echo esc_html( sprintf( __( 'Hello, %s', 'propzed-publisher' ), $pzp_user->display_name ) );
					?>
				</p>
				<a class="pz-btn pz-btn--gold" href="<?php echo esc_url( pzp_publisher_url( array( 'view' => 'new' ) ) ); ?>"><?php echo propzed_icon( 'plus' ); // phpcs:ignore WordPress.Security.EscapeOutput ?> <?php esc_html_e( 'Add a property', 'propzed-publisher' ); ?></a>
			</div>

			<dl class="pzp-stats pzp-stats--light">
				<div><dt><?php esc_html_e( 'Live', 'propzed-publisher' ); ?></dt><dd><?php echo esc_html( $pzp_counts['live'] ); ?></dd></div>
				<div><dt><?php esc_html_e( 'Under review', 'propzed-publisher' ); ?></dt><dd><?php echo esc_html( $pzp_counts['review'] ); ?></dd></div>
				<div><dt><?php esc_html_e( 'Changes requested', 'propzed-publisher' ); ?></dt><dd><?php echo esc_html( $pzp_counts['changes'] ); ?></dd></div>
				<div><dt><?php esc_html_e( 'Withdrawn', 'propzed-publisher' ); ?></dt><dd><?php echo esc_html( $pzp_counts['withdrawn'] ); ?></dd></div>
			</dl>

			<?php if ( $pzp_listings ) : ?>
				<ul class="pzp-listings">
					<?php foreach ( $pzp_listings as $pzp_listing ) : ?>
						<?php
						$pzp_state     = pzp_listing_state( $pzp_listing );
						$pzp_enquiries = 'live' === $pzp_state[0] ? pzp_enquiry_count( $pzp_listing->ID ) : 0;
						$pzp_note      = 'changes' === $pzp_state[0] ? get_post_meta( $pzp_listing->ID, '_pzp_review_note', true ) : '';
						$pzp_edit_url  = pzp_publisher_url(
							array(
								'view' => 'edit',
								'id'   => $pzp_listing->ID,
							)
						);
						?>
						<li class="pzp-listing">
							<div class="pzp-listing__media">
								<?php if ( has_post_thumbnail( $pzp_listing ) ) : ?>
									<?php echo get_the_post_thumbnail( $pzp_listing, 'thumbnail', array( 'alt' => '', 'loading' => 'lazy' ) ); ?>
								<?php else : ?>
									<?php echo propzed_icon( 'building' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
								<?php endif; ?>
							</div>
							<div class="pzp-listing__body">
								<span class="pzp-pill pzp-pill--<?php echo esc_attr( $pzp_state[0] ); ?>"><?php echo esc_html( $pzp_state[1] ); ?></span>
								<h2 class="pzp-listing__title"><?php echo esc_html( get_the_title( $pzp_listing ) ); ?></h2>
								<p class="pzp-listing__meta">
									<?php echo esc_html( implode( ' · ', array_filter( array( propzed_location_label( $pzp_listing->ID ), propzed_area( $pzp_listing->ID ), propzed_price( $pzp_listing->ID ) ) ) ) ); ?>
								</p>
								<?php if ( $pzp_note ) : ?>
									<p class="pzp-listing__note"><strong><?php esc_html_e( 'Our team says:', 'propzed-publisher' ); ?></strong> <?php echo esc_html( $pzp_note ); ?></p>
								<?php endif; ?>
								<?php if ( 'live' === $pzp_state[0] ) : ?>
									<p class="pzp-listing__meta">
										<?php
										/* translators: %d: number of enquiries */
										echo esc_html( sprintf( _n( '%d buyer enquiry received', '%d buyer enquiries received', $pzp_enquiries, 'propzed-publisher' ), $pzp_enquiries ) );
										?>
									</p>
								<?php endif; ?>
							</div>
							<div class="pzp-listing__actions">
								<?php if ( 'live' === $pzp_state[0] ) : ?>
									<a class="pz-btn pz-btn--outline" href="<?php echo esc_url( get_permalink( $pzp_listing ) ); ?>"><?php esc_html_e( 'View', 'propzed-publisher' ); ?><span class="pz-sr"> <?php echo esc_html( get_the_title( $pzp_listing ) ); ?></span></a>
								<?php endif; ?>
								<a class="pz-btn pz-btn--navy" href="<?php echo esc_url( $pzp_edit_url ); ?>"><?php echo esc_html( in_array( $pzp_state[0], array( 'changes', 'withdrawn' ), true ) ? __( 'Edit & resubmit', 'propzed-publisher' ) : __( 'Edit', 'propzed-publisher' ) ); ?><span class="pz-sr"> <?php echo esc_html( get_the_title( $pzp_listing ) ); ?></span></a>
								<?php if ( 'withdrawn' !== $pzp_state[0] ) : ?>
									<form action="<?php echo esc_url( $pzp_action ); ?>" method="post" data-pzp-confirm="<?php esc_attr_e( 'Withdraw this listing? It will be hidden from the site.', 'propzed-publisher' ); ?>">
										<input type="hidden" name="action" value="pzp_withdraw">
										<input type="hidden" name="pzp_id" value="<?php echo esc_attr( $pzp_listing->ID ); ?>">
										<input type="hidden" name="pzp_nonce" value="<?php echo esc_attr( wp_create_nonce( 'pzp_withdraw' ) ); ?>">
										<button class="pzp-linkbtn" type="submit"><?php esc_html_e( 'Withdraw', 'propzed-publisher' ); ?><span class="pz-sr"> <?php echo esc_html( get_the_title( $pzp_listing ) ); ?></span></button>
									</form>
								<?php endif; ?>
							</div>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php else : ?>
				<div class="pz-empty">
					<h2><?php esc_html_e( 'You have not added a property yet', 'propzed-publisher' ); ?></h2>
					<p><?php esc_html_e( 'It takes about three minutes. Keep the area, expected price and a few photos handy.', 'propzed-publisher' ); ?></p>
					<a class="pz-btn pz-btn--gold" href="<?php echo esc_url( pzp_publisher_url( array( 'view' => 'new' ) ) ); ?>"><?php esc_html_e( 'Add your first property', 'propzed-publisher' ); ?></a>
				</div>
			<?php endif; ?>

		<?php endif; ?>
	<?php endif; ?>
</div>

<?php
get_footer();
