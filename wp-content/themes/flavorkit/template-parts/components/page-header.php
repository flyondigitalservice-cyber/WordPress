<?php
/**
 * Coloured page header used on pages, archives and search.
 *
 * @package FlavorKit
 *
 * @var array $args { title: string, text?: string }
 */

defined( 'ABSPATH' ) || exit;
?>
<header class="fk-page-header">
	<div class="fk-container">
		<h1 class="fk-page-title"><?php echo wp_kses_post( $args['title'] ); ?></h1>
		<?php if ( ! empty( $args['text'] ) ) : ?>
			<div class="fk-page-intro"><?php echo wp_kses_post( $args['text'] ); ?></div>
		<?php endif; ?>
	</div>
	<?php flavorkit_wave( 'bottom', 'var(--fk-cream)' ); ?>
</header>
