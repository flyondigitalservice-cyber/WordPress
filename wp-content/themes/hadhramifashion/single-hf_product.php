<?php
/**
 * Single product: gallery, fit options, measurements, delivery promise.
 *
 * @package HadhramiFashion
 */

get_header();

while ( have_posts() ) :
	the_post();
	$hf_id      = get_the_ID();
	$hf_kind    = hf_product_kind( $hf_id );
	$hf_is_suit = 'suits' === $hf_kind;
	$hf_swatch  = get_post_meta( $hf_id, '_hf_drive_swatch', true );
	$hf_express = '1' === get_post_meta( $hf_id, '_hf_express', true );
	$hf_fabric  = get_post_meta( $hf_id, '_hf_fabric', true );
	$hf_colour  = get_post_meta( $hf_id, '_hf_colour', true );
	$hf_coll    = get_term_by( 'slug', $hf_kind, 'hf_collection' );

	$hf_suit_measures  = array(
		'chest'    => __( 'Chest', 'hadhramifashion' ),
		'waist'    => __( 'Waist', 'hadhramifashion' ),
		'hips'     => __( 'Seat', 'hadhramifashion' ),
		'shoulder' => __( 'Shoulder', 'hadhramifashion' ),
		'sleeve'   => __( 'Sleeve', 'hadhramifashion' ),
		'jacket'   => __( 'Jacket length', 'hadhramifashion' ),
		'inseam'   => __( 'Trouser inseam', 'hadhramifashion' ),
		'height'   => __( 'Height', 'hadhramifashion' ),
		'weight'   => __( 'Weight (kg)', 'hadhramifashion' ),
	);
	$hf_shirt_measures = array(
		'neck'     => __( 'Neck', 'hadhramifashion' ),
		'chest'    => __( 'Chest', 'hadhramifashion' ),
		'waist'    => __( 'Waist', 'hadhramifashion' ),
		'shoulder' => __( 'Shoulder', 'hadhramifashion' ),
		'sleeve'   => __( 'Sleeve', 'hadhramifashion' ),
		'length'   => __( 'Shirt length', 'hadhramifashion' ),
	);
	$hf_measures = $hf_is_suit ? $hf_suit_measures : $hf_shirt_measures;
	?>
	<div class="wrap">
		<div class="product" data-product='<?php echo esc_attr( wp_json_encode( hf_product_payload( $hf_id ) ) ); ?>'>
			<div class="product__gallery">
				<?php hf_product_image( $hf_id, 1200, '', false ); ?>
				<?php if ( $hf_swatch ) : ?>
					<div class="product__swatch-big"><?php hf_drive_img( $hf_swatch, $hf_fabric, 900 ); ?></div>
				<?php endif; ?>
			</div>

			<div class="product__info">
				<nav class="breadcrumbs" aria-label="<?php esc_attr_e( 'Breadcrumb', 'hadhramifashion' ); ?>">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'hadhramifashion' ); ?></a> /
					<?php if ( $hf_coll ) : ?>
						<a href="<?php echo esc_url( get_term_link( $hf_coll ) ); ?>"><?php echo esc_html( $hf_coll->name ); ?></a> /
					<?php endif; ?>
					<span><?php the_title(); ?></span>
				</nav>

				<h1><?php the_title(); ?></h1>
				<p class="product__sub"><?php echo esc_html( get_post_meta( $hf_id, '_hf_subtitle', true ) ); ?></p>
				<div class="product__price">
					<?php echo esc_html( hf_price( get_post_meta( $hf_id, '_hf_price', true ) ) ); ?>
					<small><?php echo $hf_is_suit ? esc_html__( 'Made to your measure', 'hadhramifashion' ) : esc_html__( 'Ready to wear or made to measure', 'hadhramifashion' ); ?></small>
				</div>

				<?php if ( has_excerpt() ) : ?>
					<p class="lede"><?php echo esc_html( get_the_excerpt() ); ?></p>
				<?php endif; ?>

				<form data-add-form>
					<?php if ( ! $hf_is_suit ) : ?>
						<div class="opt">
							<div class="opt__title"><span><?php esc_html_e( 'Fit', 'hadhramifashion' ); ?></span></div>
							<div class="choices">
								<div class="choice">
									<input type="radio" name="fit" id="fit-ready" value="ready" checked>
									<label for="fit-ready">
										<strong><?php echo hf_icon( 'clock' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php esc_html_e( 'Ready to wear', 'hadhramifashion' ); ?></strong>
										<span><?php echo $hf_express ? esc_html__( 'In stock — at your door within 2 hours.', 'hadhramifashion' ) : esc_html__( 'Delivered within 24 hours.', 'hadhramifashion' ); ?></span>
									</label>
								</div>
								<div class="choice">
									<input type="radio" name="fit" id="fit-measure" value="measure">
									<label for="fit-measure">
										<strong><?php echo hf_icon( 'needle' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php esc_html_e( 'Made to measure', 'hadhramifashion' ); ?></strong>
										<span><?php esc_html_e( 'Cut to your pattern — delivered within 24 hours.', 'hadhramifashion' ); ?></span>
									</label>
								</div>
							</div>
						</div>

						<div class="opt" data-show-fit="ready">
							<div class="opt__title"><span><?php esc_html_e( 'Collar size', 'hadhramifashion' ); ?></span><span class="muted"><?php esc_html_e( 'Slim · Tailored · Classic cuts in each', 'hadhramifashion' ); ?></span></div>
							<div class="sizes">
								<?php foreach ( array( '14.5', '15', '15.5', '16', '16.5', '17', '17.5', '18' ) as $hf_i => $hf_size ) : ?>
									<div class="choice">
										<input type="radio" name="size" id="size-<?php echo esc_attr( $hf_i ); ?>" value="<?php echo esc_attr( $hf_size ); ?>" <?php checked( 3, $hf_i ); ?>>
										<label for="size-<?php echo esc_attr( $hf_i ); ?>"><?php echo esc_html( $hf_size ); ?>″</label>
									</div>
								<?php endforeach; ?>
							</div>
							<div class="sizes" style="margin-top:.7rem">
								<?php foreach ( array( 'Slim', 'Tailored', 'Classic' ) as $hf_i => $hf_cut ) : ?>
									<div class="choice">
										<input type="radio" name="cut" id="cut-<?php echo esc_attr( $hf_i ); ?>" value="<?php echo esc_attr( $hf_cut ); ?>" <?php checked( 1, $hf_i ); ?>>
										<label for="cut-<?php echo esc_attr( $hf_i ); ?>"><?php echo esc_html( $hf_cut ); ?></label>
									</div>
								<?php endforeach; ?>
							</div>
						</div>
					<?php endif; ?>

					<div class="opt" <?php echo $hf_is_suit ? '' : 'data-show-fit="measure" hidden'; ?>>
						<div class="opt__title"><span><?php esc_html_e( 'Your measurements', 'hadhramifashion' ); ?></span><span class="muted"><?php esc_html_e( 'in inches · optional', 'hadhramifashion' ); ?></span></div>
						<p class="muted" style="font-size:.88rem"><?php esc_html_e( 'Leave blank and our tailor will visit to measure you — within two hours of your order, inside the delivery area.', 'hadhramifashion' ); ?></p>
						<div class="measure-grid">
							<?php foreach ( $hf_measures as $hf_key => $hf_label ) : ?>
								<div class="field">
									<label for="m-<?php echo esc_attr( $hf_key ); ?>"><?php echo esc_html( $hf_label ); ?></label>
									<input type="text" inputmode="decimal" id="m-<?php echo esc_attr( $hf_key ); ?>" name="m_<?php echo esc_attr( $hf_key ); ?>">
								</div>
							<?php endforeach; ?>
						</div>
					</div>

					<div class="promise-note" data-promise>
						<?php echo hf_icon( $hf_is_suit ? 'needle' : 'clock' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						<div data-promise-text
							data-ready="<?php echo esc_attr( $hf_express ? __( 'Order now and it can be with you within 2 hours — we will confirm the exact time for your address at checkout.', 'hadhramifashion' ) : __( 'Pressed and delivered within 24 hours inside our delivery area.', 'hadhramifashion' ) ); ?>"
							data-measure="<?php esc_attr_e( 'Cut to your measure and delivered within 24 hours inside our delivery area. A tailor is on call for the final touch.', 'hadhramifashion' ); ?>">
							<?php
							if ( $hf_is_suit ) {
								esc_html_e( 'Cut to your measure and delivered within 24 hours inside our delivery area. A tailor is on call for the final touch.', 'hadhramifashion' );
							} elseif ( $hf_express ) {
								esc_html_e( 'Order now and it can be with you within 2 hours — we will confirm the exact time for your address at checkout.', 'hadhramifashion' );
							} else {
								esc_html_e( 'Pressed and delivered within 24 hours inside our delivery area.', 'hadhramifashion' );
							}
							?>
						</div>
					</div>

					<button class="btn btn--block" type="submit"><?php echo hf_icon( 'bag' ); // phpcs:ignore WordPress.Security.EscapeOutput ?> <?php esc_html_e( 'Add to bag', 'hadhramifashion' ); ?></button>
				</form>

				<div class="product__desc">
					<?php the_content(); ?>
					<ul class="details-list">
						<?php if ( $hf_fabric ) : ?>
							<li><span><?php esc_html_e( 'Cloth', 'hadhramifashion' ); ?></span><span><?php echo esc_html( $hf_fabric ); ?></span></li>
						<?php endif; ?>
						<?php if ( $hf_colour ) : ?>
							<li><span><?php esc_html_e( 'Colour', 'hadhramifashion' ); ?></span><span><?php echo esc_html( $hf_colour ); ?></span></li>
						<?php endif; ?>
						<li><span><?php esc_html_e( 'Alterations', 'hadhramifashion' ); ?></span><span><?php esc_html_e( 'Complimentary, for life', 'hadhramifashion' ); ?></span></li>
						<li><span><?php esc_html_e( 'Payment', 'hadhramifashion' ); ?></span><span><?php esc_html_e( 'On delivery or fitting', 'hadhramifashion' ); ?></span></li>
					</ul>
				</div>
			</div>
		</div>
	</div>

	<section class="section section--ivory section--tight">
		<div class="wrap">
			<div class="section-head">
				<h2><?php esc_html_e( 'Wear it with', 'hadhramifashion' ); ?></h2>
			</div>
			<div class="products">
				<?php
				$hf_related = new WP_Query(
					array(
						'post_type'      => 'hf_product',
						'posts_per_page' => 4,
						'orderby'        => 'rand',
						'post__not_in'   => array( $hf_id ),
						'tax_query'      => array( array( 'taxonomy' => 'hf_collection', 'field' => 'slug', 'terms' => $hf_is_suit ? 'shirts' : 'suits' ) ),
					)
				);
				while ( $hf_related->have_posts() ) {
					$hf_related->the_post();
					get_template_part( 'template-parts/product-card' );
				}
				wp_reset_postdata();
				?>
			</div>
		</div>
	</section>
	<?php
endwhile;

get_footer();
