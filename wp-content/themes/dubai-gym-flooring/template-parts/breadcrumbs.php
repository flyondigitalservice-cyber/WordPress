<?php
/**
 * Breadcrumbs (schema is output separately in inc/seo.php).
 *
 * @package DGF
 */

defined( 'ABSPATH' ) || exit;

$dgf_crumb_id = get_queried_object_id();
?>
<nav class="dgf-crumbs" aria-label="<?php esc_attr_e( 'Breadcrumb', 'dgf' ); ?>">
	<ol>
		<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'dgf' ); ?></a></li>
		<?php foreach ( array_reverse( get_post_ancestors( $dgf_crumb_id ) ) as $dgf_ancestor ) : ?>
			<li><a href="<?php echo esc_url( get_permalink( $dgf_ancestor ) ); ?>"><?php echo esc_html( wp_strip_all_tags( get_the_title( $dgf_ancestor ) ) ); ?></a></li>
		<?php endforeach; ?>
		<li aria-current="page"><?php echo esc_html( wp_strip_all_tags( get_the_title( $dgf_crumb_id ) ) ); ?></li>
	</ol>
</nav>
