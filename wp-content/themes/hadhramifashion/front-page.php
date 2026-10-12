<?php
/**
 * Home page.
 *
 * @package HadhramiFashion
 */

get_header();

$hf_suits_link  = get_term_link( 'suits', 'product_cat' );
$hf_shirts_link = get_term_link( 'shirts', 'product_cat' );
$hf_suits_link  = is_wp_error( $hf_suits_link ) ? '#' : $hf_suits_link;
$hf_shirts_link = is_wp_error( $hf_shirts_link ) ? '#' : $hf_shirts_link;
$hf_cloth_link  = hf_page_url( 'page-templates/cloth.php' );
?>

<section class="hero">
	<div class="hero__media">
		<img src="<?php echo esc_url( hf_brand_image( 'hero', 2200 ) ); ?>" alt="" fetchpriority="high" referrerpolicy="no-referrer">
	</div>
	<div class="wrap hero__content">
		<span class="eyebrow"><?php esc_html_e( 'The House of Saquib Hadhrami', 'hadhramifashion' ); ?></span>
		<h1><?php echo wp_kses( hf_copy( 'hero_title' ), array( 'em' => array(), 'br' => array() ) ); ?></h1>
		<p class="lede"><?php echo esc_html( hf_copy( 'hero_text' ) ); ?></p>
		<div class="hero__actions">
			<a class="btn btn--light" href="<?php echo esc_url( $hf_suits_link ); ?>"><?php esc_html_e( 'Commission a suit', 'hadhramifashion' ); ?></a>
			<a class="btn btn--outline-light" href="<?php echo esc_url( add_query_arg( 'express', 1, $hf_shirts_link ) ); ?>"><?php esc_html_e( 'A shirt by this evening', 'hadhramifashion' ); ?></a>
		</div>
	</div>
	<div class="hero__seal" aria-hidden="true">
		<div><strong>24</strong><span><?php esc_html_e( 'Hours to', 'hadhramifashion' ); ?><br><?php esc_html_e( 'your measure', 'hadhramifashion' ); ?></span></div>
	</div>
</section>

<section class="promise" aria-label="<?php esc_attr_e( 'Our promise', 'hadhramifashion' ); ?>">
	<div class="wrap promise__grid">
		<div class="promise__item">
			<span class="promise__num">2</span>
			<div>
				<h3><?php esc_html_e( 'Hours for a shirt', 'hadhramifashion' ); ?></h3>
				<p><?php esc_html_e( 'Whites, blues and evening shirts, kept pressed in every size, hand-delivered across the city.', 'hadhramifashion' ); ?></p>
			</div>
		</div>
		<div class="promise__item">
			<span class="promise__num">24</span>
			<div>
				<h3><?php esc_html_e( 'Hours for a suit', 'hadhramifashion' ); ?></h3>
				<p><?php esc_html_e( 'Measured at home or in the atelier, cut by hand overnight, fitted to you tomorrow.', 'hadhramifashion' ); ?></p>
			</div>
		</div>
		<div class="promise__item">
			<span class="promise__num">∞</span>
			<div>
				<h3><?php esc_html_e( 'Alterations for life', 'hadhramifashion' ); ?></h3>
				<p><?php esc_html_e( 'Every piece carries generous inlays. Bring it back as you change, and we will follow.', 'hadhramifashion' ); ?></p>
			</div>
		</div>
	</div>
</section>

<section class="section">
	<div class="wrap">
		<div class="section-head reveal">
			<div>
				<span class="eyebrow"><?php esc_html_e( 'The Collections', 'hadhramifashion' ); ?></span>
				<h2><?php esc_html_e( 'A legacy of choice, kept in one atelier.', 'hadhramifashion' ); ?></h2>
			</div>
			<p class="lede"><?php esc_html_e( 'Hundreds of cloths, a cutter who knows your shoulders, and shirts that are already waiting in your size.', 'hadhramifashion' ); ?></p>
		</div>
		<div class="collections">
			<a class="tile reveal" href="<?php echo esc_url( $hf_suits_link ); ?>">
				<img src="<?php echo esc_url( hf_brand_image( 'tile_suits', 1200 ) ); ?>" alt="" loading="lazy" referrerpolicy="no-referrer">
				<div class="tile__label"><span><?php esc_html_e( 'Made to measure · 24 hours', 'hadhramifashion' ); ?></span><h3><?php esc_html_e( 'Suits', 'hadhramifashion' ); ?></h3></div>
			</a>
			<a class="tile reveal" href="<?php echo esc_url( $hf_shirts_link ); ?>">
				<img src="<?php echo esc_url( hf_brand_image( 'tile_shirts', 1000 ) ); ?>" alt="" loading="lazy" referrerpolicy="no-referrer">
				<div class="tile__label"><span><?php esc_html_e( 'Ready in 2 hours', 'hadhramifashion' ); ?></span><h3><?php esc_html_e( 'Shirts', 'hadhramifashion' ); ?></h3></div>
			</a>
			<a class="tile reveal" href="<?php echo esc_url( $hf_cloth_link ); ?>">
				<img src="<?php echo esc_url( hf_brand_image( 'tile_cloth', 1000 ) ); ?>" alt="" loading="lazy" referrerpolicy="no-referrer">
				<div class="tile__label"><span><?php esc_html_e( 'Wools, flannels, linens', 'hadhramifashion' ); ?></span><h3><?php esc_html_e( 'The Cloth Library', 'hadhramifashion' ); ?></h3></div>
			</a>
		</div>
	</div>
</section>

<section class="section section--ivory">
	<div class="wrap split">
		<div class="split__media reveal">
			<img src="<?php echo esc_url( hf_brand_image( 'legacy', 1400 ) ); ?>" alt="<?php esc_attr_e( 'Inside the Hadhrami atelier', 'hadhramifashion' ); ?>" loading="lazy" referrerpolicy="no-referrer">
		</div>
		<div class="reveal">
			<span class="eyebrow"><?php esc_html_e( 'A family house', 'hadhramifashion' ); ?></span>
			<h2><?php esc_html_e( 'From the coast of Hadhramaut to your doorstep.', 'hadhramifashion' ); ?></h2>
			<p class="lede"><?php esc_html_e( 'Hadhrami families carried their trades across the Indian Ocean — merchants, scholars, and tailors who dressed whole neighbourhoods for Eid, for weddings, for the first day of work. This house is one of those threads, still unbroken.', 'hadhramifashion' ); ?></p>
			<blockquote class="patron">
				<?php echo esc_html( hf_copy( 'legacy_quote' ) ); ?>
				<cite><?php echo esc_html( hf_opt( 'founder' ) ); ?></cite>
			</blockquote>
			<div class="stats">
				<div><strong>2h</strong><span><?php esc_html_e( 'Shirt to your door', 'hadhramifashion' ); ?></span></div>
				<div><strong>24h</strong><span><?php esc_html_e( 'Suit to your measure', 'hadhramifashion' ); ?></span></div>
				<div><strong>1</strong><span><?php esc_html_e( 'Pattern, yours', 'hadhramifashion' ); ?></span></div>
			</div>
			<p style="margin-top:2rem"><a class="link-arrow" href="<?php echo esc_url( hf_page_url( 'page-templates/heritage.php' ) ); ?>"><?php esc_html_e( 'Read our story', 'hadhramifashion' ); ?> <?php echo hf_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a></p>
		</div>
	</div>
</section>

<section class="section">
	<div class="wrap">
		<div class="section-head reveal">
			<div>
				<span class="eyebrow"><?php esc_html_e( 'Suits', 'hadhramifashion' ); ?></span>
				<h2><?php esc_html_e( 'Cut tonight. Worn tomorrow.', 'hadhramifashion' ); ?></h2>
			</div>
			<a class="link-arrow" href="<?php echo esc_url( $hf_suits_link ); ?>"><?php esc_html_e( 'All suits', 'hadhramifashion' ); ?> <?php echo hf_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
		</div>
		<div class="products">
			<?php
			$hf_q = new WP_Query(
				array(
					'post_type'      => 'product',
					'posts_per_page' => 8,
					'orderby'        => 'menu_order',
					'order'          => 'ASC',
					'tax_query'      => array( array( 'taxonomy' => 'product_cat', 'field' => 'slug', 'terms' => 'suits' ) ),
				)
			);
			while ( $hf_q->have_posts() ) {
				$hf_q->the_post();
				get_template_part( 'template-parts/product-card' );
			}
			wp_reset_postdata();
			?>
		</div>
	</div>
</section>

<section class="section section--ink">
	<div class="wrap">
		<div class="section-head reveal">
			<div>
				<span class="eyebrow"><?php esc_html_e( 'The craft', 'hadhramifashion' ); ?></span>
				<h2><?php esc_html_e( 'Twenty-four hours, five pairs of hands.', 'hadhramifashion' ); ?></h2>
			</div>
			<p class="lede" style="color:rgba(229,220,203,.7)"><?php esc_html_e( 'Speed without shortcuts: the atelier works in shifts, so your suit is never waiting on a queue — only on the next careful step.', 'hadhramifashion' ); ?></p>
		</div>
		<div class="craft">
			<div class="craft__step reveal"><small><?php esc_html_e( 'Hour 0', 'hadhramifashion' ); ?></small><h3><?php esc_html_e( 'Measure', 'hadhramifashion' ); ?></h3><p><?php esc_html_e( 'Twenty-two measurements taken at home, office or the atelier — or sent from your favourite jacket.', 'hadhramifashion' ); ?></p></div>
			<div class="craft__step reveal"><small><?php esc_html_e( 'Hour 2', 'hadhramifashion' ); ?></small><h3><?php esc_html_e( 'Draft', 'hadhramifashion' ); ?></h3><p><?php esc_html_e( 'A paper pattern drafted for your posture, shoulder slope and the way you stand.', 'hadhramifashion' ); ?></p></div>
			<div class="craft__step reveal"><small><?php esc_html_e( 'Hour 4', 'hadhramifashion' ); ?></small><h3><?php esc_html_e( 'Cut', 'hadhramifashion' ); ?></h3><p><?php esc_html_e( 'Cloth chalked and cut by hand, stripes and checks matched across every seam.', 'hadhramifashion' ); ?></p></div>
			<div class="craft__step reveal"><small><?php esc_html_e( 'Hour 6–18', 'hadhramifashion' ); ?></small><h3><?php esc_html_e( 'Make', 'hadhramifashion' ); ?></h3><p><?php esc_html_e( 'Canvas shaped, collar set by hand, buttonholes finished, every seam pressed open.', 'hadhramifashion' ); ?></p></div>
			<div class="craft__step reveal"><small><?php esc_html_e( 'Hour 24', 'hadhramifashion' ); ?></small><h3><?php esc_html_e( 'Deliver', 'hadhramifashion' ); ?></h3><p><?php esc_html_e( 'Our rider brings it to you on a hanger — with a tailor on call for the final touch.', 'hadhramifashion' ); ?></p></div>
		</div>
	</div>
</section>

<section class="section">
	<div class="wrap">
		<div class="section-head reveal">
			<div>
				<span class="eyebrow"><?php esc_html_e( 'Shirts', 'hadhramifashion' ); ?></span>
				<h2><?php esc_html_e( 'The white shirt, when you need it.', 'hadhramifashion' ); ?></h2>
			</div>
			<a class="link-arrow" href="<?php echo esc_url( $hf_shirts_link ); ?>"><?php esc_html_e( 'All shirts', 'hadhramifashion' ); ?> <?php echo hf_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
		</div>
		<div class="products">
			<?php
			$hf_q = new WP_Query(
				array(
					'post_type'      => 'product',
					'posts_per_page' => 8,
					'orderby'        => 'menu_order',
					'order'          => 'ASC',
					'meta_query'     => array( array( 'key' => '_hfd_express', 'value' => 'yes' ) ),
					'tax_query'      => array( array( 'taxonomy' => 'product_cat', 'field' => 'slug', 'terms' => 'shirts' ) ),
				)
			);
			while ( $hf_q->have_posts() ) {
				$hf_q->the_post();
				get_template_part( 'template-parts/product-card' );
			}
			wp_reset_postdata();
			?>
		</div>
	</div>
</section>

<section class="section section--ink">
	<div class="wrap">
		<div class="section-head reveal">
			<div>
				<span class="eyebrow"><?php esc_html_e( 'The Cloth Library', 'hadhramifashion' ); ?></span>
				<h2><?php esc_html_e( 'Choose the cloth. We will do the rest.', 'hadhramifashion' ); ?></h2>
			</div>
			<a class="link-arrow" href="<?php echo esc_url( $hf_cloth_link ); ?>" style="color:var(--paper)"><?php esc_html_e( 'Browse every cloth', 'hadhramifashion' ); ?> <?php echo hf_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
		</div>
		<div class="cloth-grid">
			<?php
			$hf_q = new WP_Query(
				array(
					'post_type'      => 'product',
					'posts_per_page' => 12,
					'orderby'        => 'menu_order',
					'order'          => 'ASC',
					'meta_query'     => array( array( 'key' => '_hf_drive_swatch', 'compare' => '!=', 'value' => '' ) ),
				)
			);
			while ( $hf_q->have_posts() ) :
				$hf_q->the_post();
				?>
				<a class="cloth reveal" href="<?php the_permalink(); ?>">
					<div class="cloth__swatch"><?php hf_drive_img( get_post_meta( get_the_ID(), '_hf_drive_swatch', true ), get_post_meta( get_the_ID(), '_hf_fabric', true ), 300 ); ?></div>
					<h4><?php echo esc_html( get_post_meta( get_the_ID(), '_hf_fabric', true ) ); ?></h4>
					<small><?php the_title(); ?></small>
				</a>
				<?php
			endwhile;
			wp_reset_postdata();
			?>
		</div>
	</div>
</section>

<section class="section section--ivory" id="delivery">
	<div class="wrap">
		<?php get_template_part( 'template-parts/zone-checker' ); ?>
	</div>
</section>

<section class="section cta-band">
	<div class="wrap reveal">
		<span class="eyebrow eyebrow--plain"><?php esc_html_e( 'For the groom, and his brothers', 'hadhramifashion' ); ?></span>
		<h2><?php esc_html_e( 'Wedding in a week? We have dressed grooms with less.', 'hadhramifashion' ); ?></h2>
		<p class="lede"><?php esc_html_e( 'Dinner suits, three-pieces and matching shirts for the whole party — measured together, delivered together.', 'hadhramifashion' ); ?></p>
		<div class="cta-band__actions">
			<a class="btn btn--light" href="<?php echo esc_url( get_term_link( 'wedding', 'product_tag' ) instanceof WP_Error ? $hf_suits_link : get_term_link( 'wedding', 'product_tag' ) ); ?>"><?php esc_html_e( 'The wedding edit', 'hadhramifashion' ); ?></a>
			<?php $hf_wa = preg_replace( '/\D/', '', (string) hf_opt( 'whatsapp' ) ); ?>
			<a class="btn btn--outline-light" href="<?php echo esc_url( $hf_wa ? 'https://wa.me/' . $hf_wa : 'tel:' . preg_replace( '/[^\d+]/', '', hf_opt( 'phone' ) ) ); ?>"><?php esc_html_e( 'Book a home fitting', 'hadhramifashion' ); ?></a>
		</div>
	</div>
</section>

<?php
get_footer();
