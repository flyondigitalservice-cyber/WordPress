<?php
/**
 * Collection listing (all products, a collection, or an occasion).
 *
 * @package HadhramiFashion
 */

get_header();

$hf_term     = is_tax() ? get_queried_object() : null;
$hf_is_suits = $hf_term && 'suits' === $hf_term->slug;
$hf_is_shirt = $hf_term && 'shirts' === $hf_term->slug;
$hf_base     = $hf_term ? get_term_link( $hf_term ) : get_post_type_archive_link( 'hf_product' );
$hf_colour   = isset( $_GET['colour'] ) ? sanitize_text_field( wp_unslash( $_GET['colour'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
$hf_express  = isset( $_GET['express'] ); // phpcs:ignore WordPress.Security.NonceVerification

if ( $hf_is_suits ) {
	$hf_title = __( 'Suits, cut to your measure', 'hadhramifashion' );
	$hf_lede  = __( 'Choose a cloth, share your measurements or ask for a home visit, and wear it tomorrow. Every suit is drafted from your own paper pattern and finished by hand.', 'hadhramifashion' );
	$hf_image = 'tile_suits';
} elseif ( $hf_is_shirt ) {
	$hf_title = __( 'Shirts, ready when you are', 'hadhramifashion' );
	$hf_lede  = __( 'Kept pressed in every collar size for two-hour delivery — or made to your measure within a day.', 'hadhramifashion' );
	$hf_image = 'tile_shirts';
} elseif ( $hf_term ) {
	$hf_title = $hf_term->name;
	$hf_lede  = $hf_term->description;
	$hf_image = 'wedding' === $hf_term->slug ? 'tile_wedding' : 'tile_occasion';
} else {
	$hf_title = __( 'The Collection', 'hadhramifashion' );
	$hf_lede  = __( 'Suits and shirts from the House of Hadhrami.', 'hadhramifashion' );
	$hf_image = 'tile_essential';
}
?>

<header class="page-hero page-hero--image">
	<img src="<?php echo esc_url( hf_brand_image( $hf_image, 2000 ) ); ?>" alt="" referrerpolicy="no-referrer">
	<div class="wrap">
		<span class="eyebrow"><?php esc_html_e( 'House of Hadhrami', 'hadhramifashion' ); ?></span>
		<h1><?php echo esc_html( $hf_title ); ?></h1>
		<?php if ( $hf_lede ) : ?>
			<p class="lede"><?php echo esc_html( $hf_lede ); ?></p>
		<?php endif; ?>
	</div>
</header>

<div class="wrap section--tight" style="padding-top:clamp(36px,5vw,64px)">
	<div class="filters">
		<a class="chip <?php echo ! $hf_colour && ! $hf_express ? 'is-active' : ''; ?>" href="<?php echo esc_url( $hf_base ); ?>"><?php esc_html_e( 'All', 'hadhramifashion' ); ?></a>
		<?php if ( $hf_is_shirt ) : ?>
			<a class="chip <?php echo $hf_express ? 'is-active' : ''; ?>" href="<?php echo esc_url( add_query_arg( 'express', 1, $hf_base ) ); ?>"><?php echo hf_icon( 'clock' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php esc_html_e( 'In 2 hours', 'hadhramifashion' ); ?></a>
			<?php foreach ( array( 'White', 'Blue', 'Navy', 'Pink', 'Stripe', 'Check', 'Evening' ) as $hf_c ) : ?>
				<a class="chip <?php echo $hf_colour === $hf_c ? 'is-active' : ''; ?>" href="<?php echo esc_url( add_query_arg( 'colour', $hf_c, $hf_base ) ); ?>"><?php echo esc_html( $hf_c ); ?></a>
			<?php endforeach; ?>
		<?php else : ?>
			<?php
			$hf_occasions = get_terms( array( 'taxonomy' => 'hf_occasion', 'hide_empty' => true ) );
			if ( ! is_wp_error( $hf_occasions ) ) :
				foreach ( $hf_occasions as $hf_o ) :
					?>
					<a class="chip <?php echo $hf_term && $hf_term->term_id === $hf_o->term_id ? 'is-active' : ''; ?>" href="<?php echo esc_url( get_term_link( $hf_o ) ); ?>"><?php echo esc_html( $hf_o->name ); ?></a>
					<?php
				endforeach;
			endif;
			?>
		<?php endif; ?>
	</div>

	<?php if ( have_posts() ) : ?>
		<div class="products">
			<?php
			while ( have_posts() ) {
				the_post();
				get_template_part( 'template-parts/product-card' );
			}
			?>
		</div>
		<div class="pagination"><?php echo paginate_links(); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
	<?php else : ?>
		<p class="lede"><?php esc_html_e( 'Nothing here yet — our tailors are cutting more.', 'hadhramifashion' ); ?></p>
	<?php endif; ?>
</div>

<?php
get_footer();
