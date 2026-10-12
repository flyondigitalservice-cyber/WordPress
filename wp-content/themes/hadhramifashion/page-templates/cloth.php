<?php
/**
 * Template Name: Cloth Library
 *
 * @package HadhramiFashion
 */

get_header();
?>
<header class="page-hero page-hero--image">
	<img src="<?php echo esc_url( hf_brand_image( 'tile_cloth', 2000 ) ); ?>" alt="" referrerpolicy="no-referrer">
	<div class="wrap">
		<span class="eyebrow"><?php esc_html_e( 'The Cloth Library', 'hadhramifashion' ); ?></span>
		<h1><?php esc_html_e( 'Every suit begins with a cloth.', 'hadhramifashion' ); ?></h1>
		<p class="lede"><?php esc_html_e( 'Worsteds for the boardroom, flannels for cooler evenings, linens for the coast and barathea for the wedding night. Touch them in the atelier, or choose here and we will bring swatches to your fitting.', 'hadhramifashion' ); ?></p>
	</div>
</header>

<?php
$hf_occasions = get_terms( array( 'taxonomy' => 'hf_occasion', 'hide_empty' => true ) );
if ( ! is_wp_error( $hf_occasions ) ) :
	foreach ( $hf_occasions as $hf_i => $hf_o ) :
		$hf_q = new WP_Query(
			array(
				'post_type'      => 'hf_product',
				'posts_per_page' => -1,
				'orderby'        => 'menu_order',
				'order'          => 'ASC',
				'meta_query'     => array( array( 'key' => '_hf_drive_swatch', 'compare' => '!=', 'value' => '' ) ),
				'tax_query'      => array( array( 'taxonomy' => 'hf_occasion', 'field' => 'term_id', 'terms' => $hf_o->term_id ) ),
			)
		);
		if ( ! $hf_q->have_posts() ) {
			continue;
		}
		?>
		<section class="section section--tight <?php echo $hf_i % 2 ? 'section--ivory' : ''; ?>">
			<div class="wrap">
				<div class="section-head">
					<h2><?php echo esc_html( $hf_o->name ); ?></h2>
					<a class="link-arrow" href="<?php echo esc_url( get_term_link( $hf_o ) ); ?>"><?php esc_html_e( 'See the suits', 'hadhramifashion' ); ?> <?php echo hf_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
				</div>
				<div class="cloth-grid">
					<?php
					while ( $hf_q->have_posts() ) :
						$hf_q->the_post();
						?>
						<a class="cloth reveal" href="<?php the_permalink(); ?>">
							<div class="cloth__swatch"><?php hf_drive_img( get_post_meta( get_the_ID(), '_hf_drive_swatch', true ), get_post_meta( get_the_ID(), '_hf_fabric', true ), 300 ); ?></div>
							<h4><?php echo esc_html( get_post_meta( get_the_ID(), '_hf_fabric', true ) ); ?></h4>
							<small><?php the_title(); ?> · <?php echo esc_html( hf_price( get_post_meta( get_the_ID(), '_hf_price', true ) ) ); ?></small>
						</a>
					<?php endwhile; ?>
				</div>
			</div>
		</section>
		<?php
		wp_reset_postdata();
	endforeach;
endif;

get_footer();
