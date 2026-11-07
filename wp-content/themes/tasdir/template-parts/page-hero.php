<?php
/**
 * Inner page title band with breadcrumbs.
 *
 * @package Tasdir
 */

defined( 'ABSPATH' ) || exit;

$tasdir_title    = isset( $args['title'] ) ? $args['title'] : '';
$tasdir_subtitle = isset( $args['subtitle'] ) ? $args['subtitle'] : '';
?>
<header class="page-hero">
	<div class="wrap">
		<?php tasdir_breadcrumbs(); ?>
		<h1><?php echo esc_html( $tasdir_title ); ?></h1>
		<?php if ( $tasdir_subtitle ) : ?>
			<p><?php echo esc_html( $tasdir_subtitle ); ?></p>
		<?php endif; ?>
	</div>
</header>
