<?php
/**
 * Contact details list.
 *
 * @package Tasdir
 */

defined( 'ABSPATH' ) || exit;
?>
<ul class="contact-list">
	<?php if ( tasdir_wa_number() ) : ?>
		<li><?php tasdir_the_icon( 'whatsapp' ); ?><div><?php esc_html_e( 'WhatsApp', 'tasdir' ); ?><br><a href="<?php echo esc_url( tasdir_wa_url() ); ?>" target="_blank" rel="noopener">+<?php echo esc_html( tasdir_wa_number() ); ?></a></div></li>
	<?php endif; ?>
	<?php if ( tasdir_opt( 'phone' ) ) : ?>
		<li><?php tasdir_the_icon( 'phone' ); ?><div><?php esc_html_e( 'Phone', 'tasdir' ); ?><br><a href="tel:<?php echo esc_attr( preg_replace( '/[^\d+]/', '', tasdir_opt( 'phone' ) ) ); ?>"><?php echo esc_html( tasdir_opt( 'phone' ) ); ?></a></div></li>
	<?php endif; ?>
	<?php if ( tasdir_opt( 'email' ) ) : ?>
		<li><?php tasdir_the_icon( 'mail' ); ?><div><?php esc_html_e( 'Email', 'tasdir' ); ?><br><a href="mailto:<?php echo esc_attr( antispambot( tasdir_opt( 'email' ) ) ); ?>"><?php echo esc_html( antispambot( tasdir_opt( 'email' ) ) ); ?></a></div></li>
	<?php endif; ?>
	<?php if ( tasdir_opt( 'address' ) ) : ?>
		<li><?php tasdir_the_icon( 'pin' ); ?><div><?php esc_html_e( 'Office', 'tasdir' ); ?><br><?php echo nl2br( esc_html( tasdir_opt( 'address' ) ) ); ?></div></li>
	<?php endif; ?>
	<?php if ( tasdir_opt( 'hours' ) ) : ?>
		<li><?php tasdir_the_icon( 'clock' ); ?><div><?php esc_html_e( 'Hours', 'tasdir' ); ?><br><?php echo esc_html( tasdir_opt( 'hours' ) ); ?></div></li>
	<?php endif; ?>
</ul>
