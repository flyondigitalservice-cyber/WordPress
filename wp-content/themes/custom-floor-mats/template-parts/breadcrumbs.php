<?php
/**
 * Breadcrumbs (schema is output separately in inc/seo.php).
 *
 * @package CFM
 */

defined( 'ABSPATH' ) || exit;

$cfm_crumb_id = get_queried_object_id();
?>
<nav class="cfm-crumbs" aria-label="<?php esc_attr_e( 'Breadcrumb', 'cfm' ); ?>">
	<ol>
		<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'cfm' ); ?></a></li>
		<?php foreach ( array_reverse( get_post_ancestors( $cfm_crumb_id ) ) as $cfm_ancestor ) : ?>
			<li><a href="<?php echo esc_url( get_permalink( $cfm_ancestor ) ); ?>"><?php echo esc_html( wp_strip_all_tags( get_the_title( $cfm_ancestor ) ) ); ?></a></li>
		<?php endforeach; ?>
		<li aria-current="page"><?php echo esc_html( wp_strip_all_tags( get_the_title( $cfm_crumb_id ) ) ); ?></li>
	</ol>
</nav>
