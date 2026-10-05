<?php
/**
 * WooCommerce wrapper template (shop, category, product pages).
 *
 * @package luxurywatchs
 */

get_header();
?>
<div class="lw-container lw-page lw-shop">
	<?php if ( ! is_product() ) : ?>
		<div class="lw-shop__chips">
			<a href="<?php echo esc_url( lw_shop_url() ); ?>"><?php esc_html_e( 'All', 'luxurywatchs' ); ?></a>
			<?php foreach ( lw_style_categories() as $c ) : ?>
				<a href="<?php echo esc_url( lw_cat_url( $c['slug'] ) ); ?>"><?php echo esc_html( $c['name'] ); ?></a>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
	<?php woocommerce_content(); ?>
</div>
<?php
get_footer();
