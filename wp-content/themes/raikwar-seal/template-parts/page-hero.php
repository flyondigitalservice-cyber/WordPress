<?php
/**
 * Inner page hero. Args: title, lead.
 */
$title = isset( $args['title'] ) ? $args['title'] : get_the_title();
$lead  = isset( $args['lead'] ) ? $args['lead'] : '';
?>
<header class="page-hero grain">
	<div class="wrap">
		<div class="crumbs"><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a> / <?php echo esc_html( wp_strip_all_tags( $title ) ); ?></div>
		<h1><?php echo esc_html( $title ); ?></h1>
		<?php if ( $lead ) : ?><p><?php echo esc_html( $lead ); ?></p><?php endif; ?>
		<div style="display:flex;gap:12px;flex-wrap:wrap;margin-top:28px">
			<a class="btn btn--primary" href="<?php echo esc_url( raikwar_wa() ); ?>" target="_blank" rel="noopener"><?php echo raikwar_icon( 'wa' ); ?>WhatsApp us</a>
			<a class="btn btn--ghost" href="<?php echo esc_attr( raikwar_tel( raikwar_info( 'phone1' ) ) ); ?>"><?php echo raikwar_icon( 'phone' ); ?><?php echo esc_html( raikwar_phone_label( raikwar_info( 'phone1' ) ) ); ?></a>
		</div>
	</div>
</header>
