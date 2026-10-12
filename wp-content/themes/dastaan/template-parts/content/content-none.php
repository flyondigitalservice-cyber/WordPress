<?php
/**
 * Nothing found.
 *
 * @package Dastaan
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="empty-state">
	<?php dastaan_the_icon( 'search', array( 'size' => 40 ) ); ?>
	<h2><?php esc_html_e( 'Nothing found', 'dastaan' ); ?></h2>
	<p><?php esc_html_e( 'Try a different word — fabric (organza, chanderi), craft (chikankari, zardozi) or colour.', 'dastaan' ); ?></p>
	<?php get_search_form(); ?>
</div>
