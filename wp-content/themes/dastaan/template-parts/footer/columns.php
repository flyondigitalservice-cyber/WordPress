<?php
/**
 * Footer columns.
 *
 * @package Dastaan
 */

defined( 'ABSPATH' ) || exit;
$dastaan_address = dastaan_option( 'studio_address' );
$dastaan_hours   = dastaan_option( 'studio_hours' );
$dastaan_phone   = dastaan_option( 'phone_number' );
$dastaan_email   = dastaan_option( 'contact_email' );
$dastaan_wa      = dastaan_whatsapp_url();
?>
<div class="container footer-grid<?php echo is_active_sidebar( 'footer-extra' ) ? ' has-extra' : ''; ?>">
	<div class="footer-col footer-col--brand">
		<div class="footer-logo"><?php get_template_part( 'template-parts/header/logo' ); ?></div>
		<p class="footer-tagline"><?php echo esc_html( dastaan_option( 'brand_tagline' ) ); ?></p>
		<p class="footer-about"><?php echo esc_html( dastaan_option( 'footer_about' ) ); ?></p>
		<?php dastaan_social_links( 'social-links--footer' ); ?>
	</div>

	<nav class="footer-col" aria-label="<?php echo esc_attr( dastaan_option( 'footer_shop_title' ) ); ?>">
		<h3 class="footer-title"><?php echo esc_html( dastaan_option( 'footer_shop_title' ) ); ?></h3>
		<?php
		if ( has_nav_menu( 'footer_shop' ) ) {
			wp_nav_menu( array( 'theme_location' => 'footer_shop', 'container' => false, 'menu_class' => 'footer-menu', 'depth' => 1 ) );
		} elseif ( dastaan_has_wc() ) {
			echo '<ul class="footer-menu">';
			printf( '<li><a href="%s">%s</a></li>', esc_url( wc_get_page_permalink( 'shop' ) ), esc_html__( 'All pieces', 'dastaan' ) );
			foreach ( dastaan_get_showcase_categories( 7 ) as $dastaan_t ) {
				printf( '<li><a href="%s">%s</a></li>', esc_url( get_term_link( $dastaan_t ) ), esc_html( $dastaan_t->name ) );
			}
			echo '</ul>';
		}
		?>
	</nav>

	<nav class="footer-col" aria-label="<?php echo esc_attr( dastaan_option( 'footer_help_title' ) ); ?>">
		<h3 class="footer-title"><?php echo esc_html( dastaan_option( 'footer_help_title' ) ); ?></h3>
		<?php
		if ( has_nav_menu( 'footer_help' ) ) {
			wp_nav_menu( array( 'theme_location' => 'footer_help', 'container' => false, 'menu_class' => 'footer-menu', 'depth' => 1 ) );
		} else {
			echo '<ul class="footer-menu">';
			// Fallback links are shown only when a published page with that slug exists (no 404s).
			$dastaan_help = array(
				'shipping-policy' => __( 'Shipping & delivery', 'dastaan' ),
				'refund_returns'  => __( 'Exchange policy', 'dastaan' ),
				'stitching-guide' => __( 'Stitching guide', 'dastaan' ),
				'faqs'            => __( 'FAQs', 'dastaan' ),
				'contact'         => __( 'Contact us', 'dastaan' ),
			);
			foreach ( $dastaan_help as $dastaan_slug => $dastaan_label ) {
				$dastaan_page = get_page_by_path( $dastaan_slug );
				if ( $dastaan_page && 'publish' === $dastaan_page->post_status ) {
					printf( '<li><a href="%s">%s</a></li>', esc_url( get_permalink( $dastaan_page ) ), esc_html( $dastaan_label ) );
				}
			}
			if ( dastaan_has_wc() ) {
				printf( '<li><a href="%s">%s</a></li>', esc_url( wc_get_page_permalink( 'myaccount' ) ), esc_html__( 'Track your order', 'dastaan' ) );
			}
			if ( dastaan_whatsapp_url() ) {
				printf( '<li><a href="%s" target="_blank" rel="noopener">%s</a></li>', esc_url( dastaan_whatsapp_url() ), esc_html__( 'Chat on WhatsApp', 'dastaan' ) );
			}
			echo '</ul>';
		}
		?>
	</nav>

	<div class="footer-col footer-col--studio">
		<h3 class="footer-title"><?php echo esc_html( dastaan_option( 'footer_studio_title' ) ); ?></h3>
		<?php if ( has_nav_menu( 'footer_studio' ) ) : ?>
			<?php wp_nav_menu( array( 'theme_location' => 'footer_studio', 'container' => false, 'menu_class' => 'footer-menu', 'depth' => 1 ) ); ?>
		<?php endif; ?>
		<address class="footer-contact">
			<?php if ( $dastaan_address ) : ?><p><?php dastaan_the_icon( 'map-pin', array( 'size' => 16 ) ); ?><span><?php echo nl2br( esc_html( $dastaan_address ) ); ?></span></p><?php endif; ?>
			<?php if ( $dastaan_hours ) : ?><p><?php dastaan_the_icon( 'clock', array( 'size' => 16 ) ); ?><span><?php echo esc_html( $dastaan_hours ); ?></span></p><?php endif; ?>
			<?php if ( $dastaan_phone ) : ?><p><?php dastaan_the_icon( 'phone', array( 'size' => 16 ) ); ?><a href="tel:<?php echo esc_attr( preg_replace( '/[^\d+]/', '', $dastaan_phone ) ); ?>"><?php echo esc_html( $dastaan_phone ); ?></a></p><?php endif; ?>
			<?php if ( $dastaan_email ) : ?><p><?php dastaan_the_icon( 'mail', array( 'size' => 16 ) ); ?><a href="mailto:<?php echo esc_attr( antispambot( $dastaan_email ) ); ?>"><?php echo esc_html( antispambot( $dastaan_email ) ); ?></a></p><?php endif; ?>
		</address>
		<?php if ( $dastaan_wa ) : ?>
			<a class="btn btn--whatsapp btn--small" href="<?php echo esc_url( $dastaan_wa ); ?>" target="_blank" rel="noopener"><?php dastaan_the_icon( 'whatsapp', array( 'size' => 16 ) ); ?><span><?php esc_html_e( 'WhatsApp the studio', 'dastaan' ); ?></span></a>
		<?php endif; ?>
	</div>

	<?php if ( is_active_sidebar( 'footer-extra' ) ) : ?>
		<div class="footer-col footer-col--extra"><?php dynamic_sidebar( 'footer-extra' ); ?></div>
	<?php endif; ?>
</div>
