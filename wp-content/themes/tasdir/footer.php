<?php
/**
 * Footer.
 *
 * @package Tasdir
 */

defined( 'ABSPATH' ) || exit;

$tasdir_products = get_posts(
	array(
		'post_type'      => 'tasdir_product',
		'posts_per_page' => 6,
		'orderby'        => 'menu_order',
		'order'          => 'ASC',
		'no_found_rows'  => true,
	)
);
$tasdir_socials  = array_filter(
	array(
		'facebook'  => tasdir_opt( 'facebook_url' ),
		'instagram' => tasdir_opt( 'instagram_url' ),
		'linkedin'  => tasdir_opt( 'linkedin_url' ),
		'youtube'   => tasdir_opt( 'youtube_url' ),
	)
);
?>
</main>

<footer class="site-footer">
	<div class="wrap">
		<div class="footer-grid">
			<div class="footer-about">
				<?php tasdir_logo( false ); ?>
				<p><?php echo esc_html( tasdir_opt( 'footer_text' ) ); ?></p>
				<?php if ( $tasdir_socials ) : ?>
					<div class="socials">
						<?php foreach ( $tasdir_socials as $tasdir_net => $tasdir_url ) : ?>
							<a href="<?php echo esc_url( $tasdir_url ); ?>" target="_blank" rel="noopener" aria-label="<?php echo esc_attr( ucfirst( $tasdir_net ) ); ?>"><?php tasdir_the_icon( $tasdir_net ); ?></a>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>

			<div>
				<h2><?php esc_html_e( 'Products', 'tasdir' ); ?></h2>
				<ul>
					<?php foreach ( $tasdir_products as $tasdir_p ) : ?>
						<li><a href="<?php echo esc_url( get_permalink( $tasdir_p ) ); ?>"><?php echo esc_html( get_the_title( $tasdir_p ) ); ?></a></li>
					<?php endforeach; ?>
					<li><a href="<?php echo esc_url( (string) get_post_type_archive_link( 'tasdir_product' ) ); ?>"><?php esc_html_e( 'All products', 'tasdir' ); ?></a></li>
				</ul>
			</div>

			<div>
				<h2><?php esc_html_e( 'Company', 'tasdir' ); ?></h2>
				<?php
				if ( has_nav_menu( 'footer' ) ) {
					wp_nav_menu(
						array(
							'theme_location' => 'footer',
							'container'      => false,
							'depth'          => 1,
						)
					);
				} else {
					tasdir_menu_fallback();
				}
				?>
			</div>

			<div>
				<h2><?php esc_html_e( 'Contact', 'tasdir' ); ?></h2>
				<ul>
					<?php if ( tasdir_wa_number() ) : ?>
						<li><a href="<?php echo esc_url( tasdir_wa_url() ); ?>" target="_blank" rel="noopener">WhatsApp: +<?php echo esc_html( tasdir_wa_number() ); ?></a></li>
					<?php endif; ?>
					<?php if ( tasdir_opt( 'phone' ) ) : ?>
						<li><a href="tel:<?php echo esc_attr( preg_replace( '/[^\d+]/', '', tasdir_opt( 'phone' ) ) ); ?>"><?php echo esc_html( tasdir_opt( 'phone' ) ); ?></a></li>
					<?php endif; ?>
					<?php if ( tasdir_opt( 'email' ) ) : ?>
						<li><a href="mailto:<?php echo esc_attr( antispambot( tasdir_opt( 'email' ) ) ); ?>"><?php echo esc_html( antispambot( tasdir_opt( 'email' ) ) ); ?></a></li>
					<?php endif; ?>
					<?php if ( tasdir_opt( 'address' ) ) : ?>
						<li><?php echo nl2br( esc_html( tasdir_opt( 'address' ) ) ); ?></li>
					<?php endif; ?>
					<?php if ( tasdir_opt( 'hours' ) ) : ?>
						<li><?php echo esc_html( tasdir_opt( 'hours' ) ); ?></li>
					<?php endif; ?>
				</ul>
				<?php dynamic_sidebar( 'footer-extra' ); ?>
			</div>
		</div>

		<div class="footer-bottom">
			<span>&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php echo esc_html( tasdir_opt( 'company_name' ) ); ?>. <?php esc_html_e( 'All rights reserved.', 'tasdir' ); ?></span>
			<?php
			if ( function_exists( 'get_the_privacy_policy_link' ) ) {
				echo get_the_privacy_policy_link(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
			?>
		</div>
	</div>
</footer>

<?php if ( tasdir_opt( 'show_wa_float' ) && tasdir_wa_number() ) : ?>
	<a class="wa-float<?php echo tasdir_opt( 'show_mobile_bar' ) ? ' wa-float--desktop' : ''; ?>" href="<?php echo esc_url( tasdir_wa_url() ); ?>" target="_blank" rel="noopener" aria-label="<?php esc_attr_e( 'Chat with us on WhatsApp', 'tasdir' ); ?>"><?php tasdir_the_icon( 'whatsapp' ); ?></a>
<?php endif; ?>

<?php if ( tasdir_opt( 'show_mobile_bar' ) ) : ?>
	<nav class="mobile-bar" aria-label="<?php esc_attr_e( 'Quick actions', 'tasdir' ); ?>">
		<?php
		$tasdir_call = tasdir_opt( 'phone' ) ? preg_replace( '/[^\d+]/', '', tasdir_opt( 'phone' ) ) : ( tasdir_wa_number() ? '+' . tasdir_wa_number() : '' );
		if ( $tasdir_call ) :
			?>
			<a href="tel:<?php echo esc_attr( $tasdir_call ); ?>"><?php tasdir_the_icon( 'phone' ); ?><?php esc_html_e( 'Call', 'tasdir' ); ?></a>
		<?php else : ?>
			<a href="<?php echo esc_url( (string) get_post_type_archive_link( 'tasdir_product' ) ); ?>"><?php tasdir_the_icon( 'box' ); ?><?php esc_html_e( 'Products', 'tasdir' ); ?></a>
		<?php endif; ?>
		<a href="<?php echo esc_url( home_url( '/#enquiry' ) ); ?>" data-scroll><?php tasdir_the_icon( 'doc' ); ?><?php esc_html_e( 'Quote', 'tasdir' ); ?></a>
		<a class="is-wa" href="<?php echo esc_url( tasdir_wa_url() ); ?>" target="_blank" rel="noopener"><?php tasdir_the_icon( 'whatsapp' ); ?><?php esc_html_e( 'WhatsApp', 'tasdir' ); ?></a>
	</nav>
<?php endif; ?>

<?php wp_footer(); ?>
</body>
</html>
